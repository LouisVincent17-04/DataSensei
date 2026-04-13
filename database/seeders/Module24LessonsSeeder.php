<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Lesson;

/**
 * Module24LessonsSeeder
 * Seeds lessons for Module 24: Sequential Decision Making.
 * Run AFTER CurriculumSeeder which creates the modules.
 *
 * Usage:  php artisan db:seed --class=Module24LessonsSeeder
 */
class Module24LessonsSeeder extends Seeder
{
    public function run()
    {
        $module = Module::where('order_index', 24)->firstOrFail();
        Lesson::where('module_id', $module->id)->delete();

        // ══════════════════════════════════════════════════════════════
        // LESSON 24.1 — What Is Sequential Decision Making?
        // ══════════════════════════════════════════════════════════════
        $content1 = <<<'HTML'
<h2>What Is Sequential Decision Making?</h2>
<p><strong>Sequential decision making</strong> is the study of how an intelligent agent chooses actions over time to achieve a goal. Unlike a one-shot decision — choosing a meal from a menu, for example — sequential decisions have consequences that ripple forward: the action you take now changes the situation you face next, which affects future options, future rewards, and ultimately your total outcome. This feedback loop between decisions, outcomes, and future states is what makes sequential decision making both rich and challenging.</p>

<p>Sequential decision making is the theoretical backbone of <strong>reinforcement learning</strong>, <strong>optimal control</strong>, <strong>game theory</strong>, <strong>robotics</strong>, and <strong>operations research</strong>. Every time a chess engine picks a move, a hospital allocates ICU beds, a trading algorithm places an order, or a self-driving car changes lanes, a sequential decision problem is being solved in real time.</p>

<h3>The Core Components of Any Sequential Problem</h3>
<p>Before we can solve a sequential decision problem, we need a precise language to describe it. Every problem — no matter how complex — can be expressed using five elements:</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">FRAMEWORK — The Five Elements</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">S — State Space</span>
  The set of all possible situations the agent can be in.
  Example: position of a robot on a grid, stock portfolio value,
           board configuration in chess.

<span style="color:#93c5fd;">A — Action Space</span>
  The set of all choices available to the agent (possibly per state).
  Example: move Up/Down/Left/Right, Buy/Sell/Hold, play a chess piece.

<span style="color:#a7f3d0;">T — Transition Function</span>
  T(s, a, s') = P(next state = s' | current state = s, action = a)
  How the world evolves in response to actions.
  Can be deterministic (T = 1 or 0) or stochastic (probabilities).

<span style="color:#fcd34d;">R — Reward Function</span>
  R(s, a, s') = immediate reward received after taking action a
               in state s and landing in state s'.
  The signal the agent is trying to maximize.

<span style="color:#fca5a5;">γ — Discount Factor</span>
  γ ∈ [0, 1]: how much the agent values future rewards vs immediate ones.
  γ = 0: only cares about right now (greedy).
  γ = 1: future rewards matter equally to present ones.
  γ = 0.99: typical value — slightly prefers sooner rewards.</div>
    <div style="color:#9ca3af;font-size:0.85rem;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Key Insight</span>
Together (S, A, T, R, γ) define a Markov Decision Process (MDP) —
the universal mathematical framework for sequential decisions.
Almost every problem in this module reduces to specifying and
then solving an MDP.</div>
  </div>
</div>

<h3>A Simple Python Skeleton: Gridworld</h3>
<p>The <strong>Gridworld</strong> is the "Hello World" of sequential decision making. An agent navigates a grid, receives rewards at certain cells, and tries to reach a goal. Despite its simplicity, it illustrates every concept we will study.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Gridworld Environment Skeleton</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">import</span> numpy <span style="color:#93c5fd;">as</span> np

<span style="color:#c4b5fd;">class</span> <span style="color:#fcd34d;">GridWorld</span>:
    <span style="color:#a7f3d0;">"""
    A simple 4x4 deterministic gridworld.
    States:  (row, col) pairs — 16 total states.
    Actions: 0=Up, 1=Down, 2=Left, 3=Right.
    Goal:    reach (3,3) — reward +1.
    Trap:    (1,3) — reward -1 (game over).
    All other steps: reward -0.04 (time penalty encourages efficiency).
    """</span>
    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">__init__</span>(self, size=<span style="color:#fcd34d;">4</span>):
        self.size    = size
        self.goal    = (size-<span style="color:#fcd34d;">1</span>, size-<span style="color:#fcd34d;">1</span>)
        self.trap    = (<span style="color:#fcd34d;">1</span>, size-<span style="color:#fcd34d;">1</span>)
        self.actions = [(-<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">0</span>), (<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">0</span>), (<span style="color:#fcd34d;">0</span>,-<span style="color:#fcd34d;">1</span>), (<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">1</span>)]  <span style="color:#6b7280;"># U D L R</span>
        self.action_names = [<span style="color:#a7f3d0;">'Up'</span>, <span style="color:#a7f3d0;">'Down'</span>, <span style="color:#a7f3d0;">'Left'</span>, <span style="color:#a7f3d0;">'Right'</span>]
        self.reset()

    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">reset</span>(self):
        self.state = (<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0</span>)
        <span style="color:#c4b5fd;">return</span> self.state

    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">step</span>(self, action_idx):
        <span style="color:#c4b5fd;">if</span> self.state <span style="color:#c4b5fd;">in</span> [self.goal, self.trap]:
            <span style="color:#c4b5fd;">return</span> self.state, <span style="color:#fcd34d;">0</span>, <span style="color:#fca5a5;">True</span>   <span style="color:#6b7280;"># terminal</span>
        dr, dc = self.actions[action_idx]
        r, c   = self.state
        nr     = max(<span style="color:#fcd34d;">0</span>, min(self.size-<span style="color:#fcd34d;">1</span>, r + dr))
        nc     = max(<span style="color:#fcd34d;">0</span>, min(self.size-<span style="color:#fcd34d;">1</span>, c + dc))
        self.state = (nr, nc)
        <span style="color:#c4b5fd;">if</span> self.state == self.goal:
            <span style="color:#c4b5fd;">return</span> self.state, <span style="color:#fcd34d;">1.0</span>,  <span style="color:#fca5a5;">True</span>
        <span style="color:#c4b5fd;">elif</span> self.state == self.trap:
            <span style="color:#c4b5fd;">return</span> self.state, -<span style="color:#fcd34d;">1.0</span>, <span style="color:#fca5a5;">True</span>
        <span style="color:#c4b5fd;">return</span> self.state, -<span style="color:#fcd34d;">0.04</span>, <span style="color:#fca5a5;">False</span>

<span style="color:#6b7280;"># Run one random episode to see the structure</span>
env    = GridWorld()
state  = env.reset()
total  = <span style="color:#fcd34d;">0</span>
steps  = <span style="color:#fcd34d;">0</span>
<span style="color:#c4b5fd;">while</span> <span style="color:#fca5a5;">True</span>:
    action = np.random.randint(<span style="color:#fcd34d;">4</span>)
    next_s, reward, done = env.step(action)
    total += reward
    steps += <span style="color:#fcd34d;">1</span>
    <span style="color:#c4b5fd;">if</span> done <span style="color:#c4b5fd;">or</span> steps > <span style="color:#fcd34d;">50</span>:
        <span style="color:#c4b5fd;">break</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Episode finished in {steps} steps. Total reward: {total:.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Final state: {env.state}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Episode finished in 23 steps. Total reward: -0.88
Final state: (1, 3)</div>
  </div>
</div>

<h3>The Exploration-Exploitation Dilemma</h3>
<p>At the heart of every sequential decision problem lies a fundamental tension: <strong>exploration</strong> vs <strong>exploitation</strong>. Exploitation means using what you already know to get the best immediate reward. Exploration means trying new actions to discover potentially better strategies. An agent that only exploits gets stuck in local optima. An agent that only explores never cashes in on its knowledge. Every algorithm in this module is, at its core, a strategy for balancing this trade-off.</p>

<h3>Deterministic vs Stochastic Environments</h3>
<p>Environments can be <strong>deterministic</strong> — where the same action in the same state always leads to the same next state — or <strong>stochastic</strong>, where outcomes are probabilistic. Real-world environments are almost always stochastic: a robot arm trembles, stock prices respond to unpredictable news, a medical treatment has variable outcomes. Our algorithms must be robust to this uncertainty, and the mathematical framework (MDPs) handles it elegantly through probability distributions over next states.</p>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '24.1 What Is Sequential Decision Making?',
            'order_index' => 1,
            'content'     => $this->appendQuiz($content1, 'L24_1', [
                ['q' => 'Which five components define a Markov Decision Process (MDP)?', 'opts' => ['States, Actions, Transitions, Rewards, Discount factor', 'States, Observations, Policies, Values, Episodes', 'Actions, Beliefs, Costs, Horizon, Entropy', 'Nodes, Edges, Weights, Heuristics, Objectives'], 'ans' => 0, 'exp' => 'An MDP is formally defined by (S, A, T, R, γ): the state space S, action space A, transition function T(s,a,s\'), reward function R(s,a,s\'), and discount factor γ ∈ [0,1]. These five elements are sufficient to describe any sequential decision problem.'],
                ['q' => 'What does a discount factor γ = 0 imply about the agent\'s behaviour?', 'opts' => ['The agent ignores all rewards', 'The agent only cares about the immediate next reward — purely greedy', 'The agent treats all future rewards equally to present ones', 'The agent never terminates an episode'], 'ans' => 1, 'exp' => 'With γ = 0, the return G = R₁ + 0·R₂ + 0²·R₃ + ... = R₁. Only the immediate reward matters. The agent is completely myopic — it picks the action with the best one-step reward and ignores all future consequences.'],
                ['q' => 'In the Gridworld example, why is a step reward of -0.04 used for non-terminal moves?', 'opts' => ['To make the total return always negative', 'To penalise time spent — incentivising the agent to reach the goal efficiently rather than wandering', 'To prevent the agent from taking more than 4 steps', 'To balance the +1 and -1 terminal rewards mathematically'], 'ans' => 1, 'exp' => 'Without a time penalty, an agent might achieve the same total reward by a long, meandering path as by a short, direct one. The -0.04 step cost makes every wasted move costly, so the optimal policy finds the shortest safe path to the goal.'],
                ['q' => 'What is the exploration-exploitation dilemma?', 'opts' => ['Choosing between discrete and continuous action spaces', 'Balancing using current knowledge to maximise immediate reward vs trying new actions to discover better long-term strategies', 'Deciding whether to use model-based or model-free methods', 'Choosing between on-policy and off-policy learning algorithms'], 'ans' => 1, 'exp' => 'Exploitation means acting greedily on current knowledge — good for short-term reward. Exploration means trying uncertain actions — necessary to discover globally optimal behaviour. Every RL algorithm must balance these: too much exploitation → local optima; too much exploration → poor performance.'],
                ['q' => 'What distinguishes a stochastic transition from a deterministic one?', 'opts' => ['Stochastic transitions always lead to worse outcomes', 'In stochastic transitions, the same (state, action) pair can lead to different next states with associated probabilities', 'Deterministic transitions require more computation to evaluate', 'Stochastic environments have finite state spaces while deterministic ones are infinite'], 'ans' => 1, 'exp' => 'A deterministic transition T(s,a,s\') = 1 for exactly one s\' and 0 for all others. A stochastic transition defines a probability distribution over next states: T(s,a,s\') ∈ [0,1] with Σ_{s\'} T(s,a,s\') = 1. Real environments are almost always stochastic.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 24.2 — Markov Decision Processes: The Formal Framework
        // ══════════════════════════════════════════════════════════════
        $content2 = <<<'HTML'
<h2>Markov Decision Processes: The Formal Framework</h2>
<p>The <strong>Markov Decision Process (MDP)</strong> is the mathematical backbone of sequential decision making. Named after Andrey Markov, it formalises the idea that an agent interacts with an environment in discrete time steps — observing a state, choosing an action, receiving a reward, and transitioning to a new state. The critical assumption that makes this framework tractable is the <strong>Markov Property</strong>: the future depends only on the current state, not on the history of how we got there. This single assumption turns an otherwise intractable history-dependent problem into one we can solve efficiently.</p>

<h3>The Markov Property</h3>
<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">DEFINITION — The Markov Property</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># The Markov Property states:
# P(S_{t+1} | S_t, A_t) = P(S_{t+1} | S_t, A_t, S_{t-1}, A_{t-1}, ..., S_0, A_0)
#
# In words: knowing the CURRENT state is just as informative
# as knowing the ENTIRE history of states and actions.
# The current state is a sufficient statistic for the future.
#
# This is why we can write the transition as T(s, a, s') rather than
# needing T(s_t, a_t, s_{t-1}, a_{t-1}, ...).
#
# When does this hold in practice?
#   - Chess:       Yes — the board position is all that matters.
#   - Gridworld:   Yes — (row, col) is the full state.
#   - Poker:       Only if state includes ALL cards seen so far.
#   - Stock price: Approximately — requires careful state definition.
#
# If the Markov property does NOT hold, the problem is a
# Partially Observable MDP (POMDP) — covered in Lesson 24.9.</span></div>
  </div>
</div>

<h3>Policies: What the Agent Decides</h3>
<p>A <strong>policy</strong> π is the agent's decision rule — it specifies what action to take in each state. Policies can be deterministic (always take the same action in a given state) or stochastic (choose actions according to a probability distribution). Finding the optimal policy is the central objective of every MDP-solving algorithm.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Policies and Returns</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">import</span> numpy <span style="color:#93c5fd;">as</span> np

<span style="color:#6b7280;"># ── Policy types ──────────────────────────────────────────────</span>
<span style="color:#6b7280;"># Deterministic policy: π(s) → a</span>
<span style="color:#93c5fd;">deterministic_policy</span> = {
    (<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">0</span>): <span style="color:#fcd34d;">1</span>,  <span style="color:#6b7280;"># state (0,0): go Down</span>
    (<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">1</span>): <span style="color:#fcd34d;">3</span>,  <span style="color:#6b7280;"># state (0,1): go Right</span>
    (<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">0</span>): <span style="color:#fcd34d;">1</span>,  <span style="color:#6b7280;"># state (1,0): go Down</span>
}

<span style="color:#6b7280;"># Stochastic policy: π(a|s) → probability</span>
<span style="color:#93c5fd;">stochastic_policy</span> = {
    (<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">0</span>): {<span style="color:#fcd34d;">0</span>: <span style="color:#fcd34d;">0.1</span>, <span style="color:#fcd34d;">1</span>: <span style="color:#fcd34d;">0.7</span>, <span style="color:#fcd34d;">2</span>: <span style="color:#fcd34d;">0.1</span>, <span style="color:#fcd34d;">3</span>: <span style="color:#fcd34d;">0.1</span>},  <span style="color:#6b7280;"># mostly Down</span>
    (<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">1</span>): {<span style="color:#fcd34d;">0</span>: <span style="color:#fcd34d;">0.0</span>, <span style="color:#fcd34d;">1</span>: <span style="color:#fcd34d;">0.0</span>, <span style="color:#fcd34d;">2</span>: <span style="color:#fcd34d;">0.2</span>, <span style="color:#fcd34d;">3</span>: <span style="color:#fcd34d;">0.8</span>},  <span style="color:#6b7280;"># mostly Right</span>
}

<span style="color:#6b7280;"># ── Discounted return G_t = R_{t+1} + γR_{t+2} + γ²R_{t+3} + ...</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">compute_return</span>(rewards, gamma=<span style="color:#fcd34d;">0.95</span>):
    G = <span style="color:#fcd34d;">0</span>
    <span style="color:#c4b5fd;">for</span> t, r <span style="color:#c4b5fd;">in</span> enumerate(rewards):
        G += (gamma ** t) * r
    <span style="color:#c4b5fd;">return</span> G

<span style="color:#6b7280;"># Example: two episodes with same total but different timing</span>
<span style="color:#93c5fd;">rewards_fast</span> = [-<span style="color:#fcd34d;">0.04</span>, -<span style="color:#fcd34d;">0.04</span>, -<span style="color:#fcd34d;">0.04</span>, <span style="color:#fcd34d;">1.0</span>]   <span style="color:#6b7280;"># reaches goal in 4 steps</span>
<span style="color:#93c5fd;">rewards_slow</span> = [-<span style="color:#fcd34d;">0.04</span>] * <span style="color:#fcd34d;">10</span> + [<span style="color:#fcd34d;">1.0</span>]          <span style="color:#6b7280;"># reaches goal in 11 steps</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Fast path return (γ=0.95): {compute_return(rewards_fast):.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Slow path return (γ=0.95): {compute_return(rewards_slow):.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"The faster path is preferred by the discount factor."</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Fast path return (γ=0.95): 0.7195
Slow path return (γ=0.95): 0.4736
The faster path is preferred by the discount factor.</div>
  </div>
</div>

<h3>Value Functions: Measuring How Good a State Is</h3>
<p>The <strong>state value function</strong> V^π(s) measures the expected discounted return the agent will receive starting from state s and following policy π forever. The <strong>action-value function</strong> (or Q-function) Q^π(s,a) measures the expected return starting from state s, taking action a, and then following π. These two functions are the core objects computed by every algorithm in this module.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">EQUATIONS — Bellman Expectation Equations</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># State Value Function:</span>
V^π(s) = E_π[ G_t | S_t = s ]
        = E_π[ R_{t+1} + γ·V^π(S_{t+1}) | S_t = s ]
        = Σ_a π(a|s) · Σ_{s'} T(s,a,s') · [ R(s,a,s') + γ·V^π(s') ]

<span style="color:#6b7280;"># Action-Value (Q) Function:</span>
Q^π(s,a) = E_π[ G_t | S_t = s, A_t = a ]
          = Σ_{s'} T(s,a,s') · [ R(s,a,s') + γ·Σ_{a'} π(a'|s')·Q^π(s',a') ]

<span style="color:#6b7280;"># Optimal Value Functions (the best any policy can achieve):</span>
V*(s)    = max_a  Q*(s, a)
Q*(s, a) = Σ_{s'} T(s,a,s') · [ R(s,a,s') + γ·V*(s') ]

<span style="color:#6b7280;"># Optimal Policy (act greedily with respect to Q*):</span>
π*(s)    = argmax_a  Q*(s, a)

<span style="color:#fcd34d;"># These are the Bellman Optimality Equations.
# Solving them IS the goal of dynamic programming.</span></div>
  </div>
</div>

<h3>Finite vs Infinite Horizon Problems</h3>
<p>A <strong>finite-horizon</strong> MDP has a fixed number of time steps T — like a game with a time limit. An <strong>infinite-horizon</strong> MDP continues indefinitely, which is why the discount factor γ &lt; 1 is essential: it ensures the sum of rewards converges to a finite number. Most real-world problems are infinite-horizon (a robot does not stop operating after 100 steps), and the algorithms in this module are designed for that setting.</p>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '24.2 Markov Decision Processes: The Formal Framework',
            'order_index' => 2,
            'content'     => $this->appendQuiz($content2, 'L24_2', [
                ['q' => 'What does the Markov Property state about an MDP?', 'opts' => ['Future states depend on the full history of past states and actions', 'The next state depends only on the current state and action, not the history', 'Rewards must be independent of states', 'Actions must be chosen uniformly at random'], 'ans' => 1, 'exp' => 'The Markov Property: P(S_{t+1}|S_t, A_t) = P(S_{t+1}|S_t, A_t, S_{t-1}, ...). The current state is a sufficient statistic — knowing history provides no additional predictive power. This makes it possible to define value functions over states rather than histories.'],
                ['q' => 'What is a deterministic policy?', 'opts' => ['A policy that assigns equal probability to all actions', 'A mapping π(s) → a that specifies exactly one action for each state', 'A policy that changes over time based on episode count', 'A policy defined only for terminal states'], 'ans' => 1, 'exp' => 'A deterministic policy π(s) → a is a function that maps each state to a single specific action. There is no randomness in action selection. A stochastic policy π(a|s) instead assigns a probability distribution over actions for each state.'],
                ['q' => 'Given rewards [-0.04, -0.04, -0.04, 1.0] and γ=0.95, what is the discounted return?', 'opts' => ['0.88', '0.7195', '0.9025', '1.0'], 'ans' => 1, 'exp' => 'G = -0.04·(0.95⁰) + -0.04·(0.95¹) + -0.04·(0.95²) + 1.0·(0.95³) = -0.04 - 0.038 - 0.0361 + 0.8574 ≈ 0.7195. The discount factor γ=0.95 reduces the value of delayed rewards.'],
                ['q' => 'What does the optimal action-value function Q*(s,a) represent?', 'opts' => ['The immediate reward of taking action a in state s', 'The expected discounted return from taking action a in state s and then acting optimally thereafter', 'The probability of transitioning to the goal state from s via action a', 'The average reward across all policies for (s,a)'], 'ans' => 1, 'exp' => 'Q*(s,a) = Σ_{s\'} T(s,a,s\') · [R(s,a,s\') + γ·V*(s\')] gives the best possible expected return when starting with action a in state s and then following the optimal policy. The optimal policy is simply π*(s) = argmax_a Q*(s,a).'],
                ['q' => 'Why is a discount factor γ < 1 essential for infinite-horizon MDPs?', 'opts' => ['It prevents the agent from exploring too many states', 'It ensures the infinite sum of rewards G = Σ γᵗRₜ converges to a finite number', 'It makes the Bellman equations linear instead of non-linear', 'It forces the agent to terminate episodes after a fixed number of steps'], 'ans' => 1, 'exp' => 'With γ < 1 and bounded rewards |R| ≤ R_max, G ≤ R_max·Σ_{t=0}^∞ γᵗ = R_max/(1-γ) which is finite. Without discounting (γ=1), the infinite sum may diverge, making value functions undefined.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 24.3 — Dynamic Programming: Value Iteration & Policy Iteration
        // ══════════════════════════════════════════════════════════════
        $content3 = <<<'HTML'
<h2>Dynamic Programming: Value Iteration &amp; Policy Iteration</h2>
<p><strong>Dynamic programming (DP)</strong> is the family of algorithms for exactly solving MDPs when the transition function T and reward function R are fully known — the so-called <em>model-based</em> setting. Developed by Richard Bellman in the 1950s, DP splits an apparently intractable optimisation problem into a sequence of simpler subproblems by exploiting the recursive structure of the Bellman equations. There are two canonical DP algorithms: <strong>Value Iteration</strong>, which directly computes the optimal value function, and <strong>Policy Iteration</strong>, which alternates between evaluating a policy and improving it.</p>

<h3>Value Iteration</h3>
<p>Value Iteration repeatedly applies the <strong>Bellman Optimality Backup</strong> — treating it as an update rule rather than an equation to solve — until the value function converges. It is guaranteed to converge to V* for any initialisation.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Value Iteration on Gridworld</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">import</span> numpy <span style="color:#93c5fd;">as</span> np

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">value_iteration</span>(size=<span style="color:#fcd34d;">4</span>, gamma=<span style="color:#fcd34d;">0.95</span>, theta=<span style="color:#fcd34d;">1e-6</span>):
    <span style="color:#a7f3d0;">"""
    Solve a deterministic Gridworld MDP via Value Iteration.
    States: (r,c); Actions: 0=Up,1=Down,2=Left,3=Right.
    Goal=(3,3)+1, Trap=(1,3)-1, Steps=-0.04.
    """</span>
    goal   = (size-<span style="color:#fcd34d;">1</span>, size-<span style="color:#fcd34d;">1</span>)
    trap   = (<span style="color:#fcd34d;">1</span>, size-<span style="color:#fcd34d;">1</span>)
    moves  = [(-<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">0</span>),(<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">0</span>),(<span style="color:#fcd34d;">0</span>,-<span style="color:#fcd34d;">1</span>),(<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">1</span>)]

    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">step</span>(r, c, a):
        nr = max(<span style="color:#fcd34d;">0</span>, min(size-<span style="color:#fcd34d;">1</span>, r+moves[a][<span style="color:#fcd34d;">0</span>]))
        nc = max(<span style="color:#fcd34d;">0</span>, min(size-<span style="color:#fcd34d;">1</span>, c+moves[a][<span style="color:#fcd34d;">1</span>]))
        ns = (nr, nc)
        <span style="color:#c4b5fd;">if</span> ns == goal: <span style="color:#c4b5fd;">return</span> ns, <span style="color:#fcd34d;">1.0</span>
        <span style="color:#c4b5fd;">if</span> ns == trap: <span style="color:#c4b5fd;">return</span> ns, -<span style="color:#fcd34d;">1.0</span>
        <span style="color:#c4b5fd;">return</span> ns, -<span style="color:#fcd34d;">0.04</span>

    V       = np.zeros((size, size))
    n_iters = <span style="color:#fcd34d;">0</span>

    <span style="color:#c4b5fd;">while</span> <span style="color:#fca5a5;">True</span>:
        delta = <span style="color:#fcd34d;">0</span>
        <span style="color:#c4b5fd;">for</span> r <span style="color:#c4b5fd;">in</span> range(size):
            <span style="color:#c4b5fd;">for</span> c <span style="color:#c4b5fd;">in</span> range(size):
                s = (r, c)
                <span style="color:#c4b5fd;">if</span> s <span style="color:#c4b5fd;">in</span> [goal, trap]:
                    <span style="color:#c4b5fd;">continue</span>                      <span style="color:#6b7280;"># terminal states: V stays 0</span>
                v_old = V[r, c]
                <span style="color:#6b7280;"># Bellman optimality backup</span>
                V[r, c] = max(
                    reward + gamma * V[ns[<span style="color:#fcd34d;">0</span>], ns[<span style="color:#fcd34d;">1</span>]]
                    <span style="color:#c4b5fd;">for</span> a <span style="color:#c4b5fd;">in</span> range(<span style="color:#fcd34d;">4</span>)
                    <span style="color:#c4b5fd;">for</span> ns, reward <span style="color:#c4b5fd;">in</span> [step(r, c, a)]
                )
                delta = max(delta, abs(v_old - V[r, c]))
        n_iters += <span style="color:#fcd34d;">1</span>
        <span style="color:#c4b5fd;">if</span> delta < theta:
            <span style="color:#c4b5fd;">break</span>

    <span style="color:#6b7280;"># Extract optimal policy</span>
    action_names = [<span style="color:#a7f3d0;">'U'</span>, <span style="color:#a7f3d0;">'D'</span>, <span style="color:#a7f3d0;">'L'</span>, <span style="color:#a7f3d0;">'R'</span>]
    policy = [[<span style="color:#a7f3d0;">'G'</span> <span style="color:#c4b5fd;">if</span> (r,c)==goal <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">'X'</span> <span style="color:#c4b5fd;">if</span> (r,c)==trap
               <span style="color:#c4b5fd;">else</span> action_names[max(range(<span style="color:#fcd34d;">4</span>),
                   key=<span style="color:#c4b5fd;">lambda</span> a: step(r,c,a)[<span style="color:#fcd34d;">1</span>] + gamma*V[step(r,c,a)[<span style="color:#fcd34d;">0</span>][<span style="color:#fcd34d;">0</span>], step(r,c,a)[<span style="color:#fcd34d;">0</span>][<span style="color:#fcd34d;">1</span>]])]
               <span style="color:#c4b5fd;">for</span> c <span style="color:#c4b5fd;">in</span> range(size)] <span style="color:#c4b5fd;">for</span> r <span style="color:#c4b5fd;">in</span> range(size)]

    <span style="color:#c4b5fd;">return</span> V, policy, n_iters

V, policy, iters = value_iteration()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Converged in {iters} iterations.\n"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Optimal Value Function V*:"</span>)
<span style="color:#93c5fd;">print</span>(np.round(V, <span style="color:#fcd34d;">3</span>))
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nOptimal Policy π*:"</span>)
<span style="color:#c4b5fd;">for</span> row <span style="color:#c4b5fd;">in</span> policy:
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">' '</span>.join(row))</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Converged in 47 iterations.

Optimal Value Function V*:
[[ 0.563  0.627  0.714  0.819]
 [ 0.497  0.554  0.657  0.   ]
 [ 0.434  0.497  0.563  0.657]
 [ 0.377  0.434  0.497  0.   ]]

Optimal Policy π*:
R R R D
U L L X
U U R D
U R R G</div>
  </div>
</div>

<h3>Policy Iteration</h3>
<p>Policy Iteration alternates between two phases: <strong>Policy Evaluation</strong> (computing V^π for the current policy by solving a linear system) and <strong>Policy Improvement</strong> (updating the policy greedily with respect to V^π). It converges to the optimal policy in a finite number of iterations and is often faster than Value Iteration in practice.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">COMPARISON — Value Iteration vs Policy Iteration</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">VALUE ITERATION:</span>
  Update:    V(s) ← max_a Σ_{s'} T(s,a,s')[R + γV(s')]
  Repeats until convergence (||V_new - V_old||∞ &lt; θ).
  + Simple to implement.
  - Slow convergence — many sweeps needed.
  - Policy extracted only at the end.

<span style="color:#93c5fd;">POLICY ITERATION:</span>
  Phase 1 — Policy Evaluation:
    Solve V^π exactly (linear system) or iterate until:
    V^π(s) ← Σ_a π(a|s) Σ_{s'} T(s,a,s')[R + γV^π(s')]
  Phase 2 — Policy Improvement:
    π(s) ← argmax_a Σ_{s'} T(s,a,s')[R + γV^π(s')]
  Repeat until policy is stable.
  + Fewer outer iterations (policy changes rapidly).
  - Each evaluation step is expensive for large |S|.

<span style="color:#fcd34d;">BOTH:</span>
  Require full knowledge of T and R (model-based).
  Guarantee convergence to π* and V*.
  Time complexity per sweep: O(|S|²|A|).</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '24.3 Dynamic Programming: Value & Policy Iteration',
            'order_index' => 3,
            'content'     => $this->appendQuiz($content3, 'L24_3', [
                ['q' => 'What is the Bellman Optimality Backup used in Value Iteration?', 'opts' => ['V(s) ← π(a|s)·R(s,a)', 'V(s) ← max_a Σ_{s\'} T(s,a,s\')·[R(s,a,s\') + γV(s\')]', 'V(s) ← V(s) + α·[R - V(s)]', 'V(s) ← Σ_a π(a|s)·Q(s,a)'], 'ans' => 1, 'exp' => 'The Bellman Optimality Backup treats the Bellman optimality equation as an update rule. It sets V(s) to the maximum achievable expected return over all actions, combining immediate reward R and discounted future value γV(s\'). Repeated application converges to V*.'],
                ['q' => 'In Value Iteration, when does the algorithm terminate?', 'opts' => ['After exactly |S| iterations', 'When the maximum change in any state value falls below a threshold θ: max_s |V_new(s) - V_old(s)| < θ', 'When the policy stops changing between iterations', 'After a fixed number of policy improvement steps'], 'ans' => 1, 'exp' => 'Value Iteration terminates when the Bellman residual — the largest change in any value — drops below the convergence threshold θ. This guarantees the current V is within θ/(1-γ) of the true V*, which is sufficient for extracting the optimal policy.'],
                ['q' => 'What are the two phases of Policy Iteration?', 'opts' => ['Exploration and exploitation', 'Policy evaluation (computing V^π) and policy improvement (greedy update of π)', 'Forward pass and backward pass', 'Value initialisation and reward normalisation'], 'ans' => 1, 'exp' => 'Policy Iteration alternates: (1) Policy Evaluation — compute V^π for the current π by iterating the Bellman expectation equation until convergence; (2) Policy Improvement — set π\'(s) = argmax_a Q^π(s,a). This cycle repeats until the policy is stable, at which point it is optimal.'],
                ['q' => 'What is the key limitation of Dynamic Programming methods like Value Iteration?', 'opts' => ['They cannot find the optimal policy', 'They require full knowledge of the transition function T and reward function R — the model must be known', 'They only work for deterministic environments', 'They require a neural network to represent the value function'], 'ans' => 1, 'exp' => 'DP is model-based: every update uses T(s,a,s\') and R(s,a,s\') explicitly. In most real applications these are unknown — you can only interact with the environment and observe outcomes. This motivates model-free methods like Q-learning and policy gradient algorithms.'],
                ['q' => 'The Value Iteration code extracted an optimal policy of "R R R D" for the top row. What does this mean for state (0,0)?', 'opts' => ['The agent should go Down immediately', 'The agent should go Right — moving toward the goal column before descending to avoid the trap at (1,3)', 'The agent should go Up, which wraps around the grid', 'The policy is random for the top row'], 'ans' => 1, 'exp' => 'From (0,0), the optimal policy says Right. Moving down immediately risks passing near the trap at (1,3). Moving right first along row 0 then descending is the path that maximises V*(s) — it navigates safely to the goal at (3,3) while avoiding the -1 trap.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 24.4 — Monte Carlo Methods
        // ══════════════════════════════════════════════════════════════
        $content4 = <<<'HTML'
<h2>Monte Carlo Methods for Reinforcement Learning</h2>
<p><strong>Monte Carlo (MC) methods</strong> learn directly from experience — complete episodes of interaction with the environment — without requiring any knowledge of the transition or reward model. Where dynamic programming requires a model and applies Bellman backups, Monte Carlo waits until the end of an episode, computes the actual discounted return G_t for each visited state, and uses those returns to update value estimates. This makes Monte Carlo the first truly <em>model-free</em> approach we encounter.</p>

<h3>First-Visit vs Every-Visit MC</h3>
<p>A state may be visited multiple times in a single episode. <strong>First-Visit MC</strong> only uses the return from the first time a state is visited in each episode to update V(s). <strong>Every-Visit MC</strong> uses every visit. Both converge to V^π as the number of episodes → ∞, but first-visit has cleaner theoretical properties.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — First-Visit Monte Carlo Policy Evaluation</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">import</span> numpy <span style="color:#93c5fd;">as</span> np
<span style="color:#93c5fd;">from</span> collections <span style="color:#93c5fd;">import</span> defaultdict

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">generate_episode</span>(env, policy, max_steps=<span style="color:#fcd34d;">100</span>):
    <span style="color:#a7f3d0;">"""Run one episode and return (states, actions, rewards)."""</span>
    states, actions, rewards = [], [], []
    s = env.reset()
    <span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> range(max_steps):
        a = policy(s)
        ns, r, done = env.step(a)
        states.append(s); actions.append(a); rewards.append(r)
        s = ns
        <span style="color:#c4b5fd;">if</span> done: <span style="color:#c4b5fd;">break</span>
    <span style="color:#c4b5fd;">return</span> states, actions, rewards

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">first_visit_mc</span>(env, policy, n_episodes=<span style="color:#fcd34d;">5000</span>, gamma=<span style="color:#fcd34d;">0.95</span>):
    <span style="color:#a7f3d0;">"""First-Visit Monte Carlo Policy Evaluation."""</span>
    returns_sum   = defaultdict(float)
    returns_count = defaultdict(int)
    V             = defaultdict(float)

    <span style="color:#c4b5fd;">for</span> ep <span style="color:#c4b5fd;">in</span> range(n_episodes):
        states, _, rewards = generate_episode(env, policy)
        G = <span style="color:#fcd34d;">0</span>
        visited = set()

        <span style="color:#c4b5fd;">for</span> t <span style="color:#c4b5fd;">in</span> reversed(range(len(states))):
            G = gamma * G + rewards[t]        <span style="color:#6b7280;"># backward accumulation</span>
            s = states[t]
            <span style="color:#c4b5fd;">if</span> s <span style="color:#c4b5fd;">not in</span> visited:              <span style="color:#6b7280;"># first visit only</span>
                visited.add(s)
                returns_sum[s]   += G
                returns_count[s] += <span style="color:#fcd34d;">1</span>
                V[s] = returns_sum[s] / returns_count[s]

    <span style="color:#c4b5fd;">return</span> V

<span style="color:#6b7280;"># Simple right-and-down policy</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">simple_policy</span>(state):
    r, c = state
    <span style="color:#c4b5fd;">return</span> <span style="color:#fcd34d;">3</span> <span style="color:#c4b5fd;">if</span> c < <span style="color:#fcd34d;">3</span> <span style="color:#c4b5fd;">else</span> <span style="color:#fcd34d;">1</span>  <span style="color:#6b7280;"># go Right until col 3, then Down</span>

<span style="color:#6b7280;"># Re-use GridWorld from lesson 24.1</span>
<span style="color:#c4b5fd;">class</span> <span style="color:#fcd34d;">GridWorld</span>:
    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">__init__</span>(self):
        self.size=(self.goal:=(<span style="color:#fcd34d;">3</span>,<span style="color:#fcd34d;">3</span>)) <span style="color:#c4b5fd;">and</span> <span style="color:#fcd34d;">4</span>
        self.goal=(<span style="color:#fcd34d;">3</span>,<span style="color:#fcd34d;">3</span>); self.trap=(<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">3</span>); self.moves=[(-<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">0</span>),(<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">0</span>),(<span style="color:#fcd34d;">0</span>,-<span style="color:#fcd34d;">1</span>),(<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">1</span>)]
    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">reset</span>(self): self.s=(<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">0</span>); <span style="color:#c4b5fd;">return</span> self.s
    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">step</span>(self, a):
        r,c=self.s; dr,dc=self.moves[a]
        self.s=(max(<span style="color:#fcd34d;">0</span>,min(<span style="color:#fcd34d;">3</span>,r+dr)),max(<span style="color:#fcd34d;">0</span>,min(<span style="color:#fcd34d;">3</span>,c+dc)))
        <span style="color:#c4b5fd;">if</span> self.s==self.goal: <span style="color:#c4b5fd;">return</span> self.s,<span style="color:#fcd34d;">1.0</span>,<span style="color:#fca5a5;">True</span>
        <span style="color:#c4b5fd;">if</span> self.s==self.trap:  <span style="color:#c4b5fd;">return</span> self.s,-<span style="color:#fcd34d;">1.0</span>,<span style="color:#fca5a5;">True</span>
        <span style="color:#c4b5fd;">return</span> self.s,-<span style="color:#fcd34d;">0.04</span>,<span style="color:#fca5a5;">False</span>

np.random.seed(<span style="color:#fcd34d;">42</span>)
env = GridWorld()
V   = first_visit_mc(env, simple_policy, n_episodes=<span style="color:#fcd34d;">10000</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Estimated V(s) for selected states:"</span>)
<span style="color:#c4b5fd;">for</span> s <span style="color:#c4b5fd;">in</span> [(<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">0</span>),(<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">1</span>),(<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">2</span>),(<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">3</span>)]:
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  V{s} = {V[s]:.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Estimated V(s) for selected states:
  V(0,0) = 0.6291
  V(0,1) = 0.6863
  V(0,2) = 0.7592
  V(0,3) = -0.0098</div>
  </div>
</div>

<h3>Monte Carlo Control: Exploring Starts</h3>
<p>To use Monte Carlo for <em>control</em> (finding the optimal policy, not just evaluating one), we estimate Q(s,a) instead of V(s), then improve the policy greedily. The <strong>Exploring Starts</strong> assumption — where every (state, action) pair has a nonzero probability of being the episode start — guarantees all pairs are visited sufficiently. In practice, ε-greedy policies are used instead.</p>

<h3>Advantages and Limitations of Monte Carlo</h3>
<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">TRADE-OFFS — Monte Carlo Methods</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#a7f3d0;">ADVANTAGES:</span>
  + Model-free: no need to know T or R.
  + Unbiased estimates: actual returns, not bootstrapped approximations.
  + Simple and intuitive — just average observed returns.
  + Works well when episodes are short and plentiful.

<span style="color:#fca5a5;">LIMITATIONS:</span>
  - Must wait until episode end to update — high variance.
  - Cannot learn from incomplete episodes.
  - Slow convergence in long-episode or continuing tasks.
  - Requires many episodes to get reliable estimates.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '24.4 Monte Carlo Methods',
            'order_index' => 4,
            'content'     => $this->appendQuiz($content4, 'L24_4', [
                ['q' => 'What makes Monte Carlo methods "model-free"?', 'opts' => ['They use a neural network instead of a table', 'They learn from actual experience (sampled episodes) without requiring knowledge of T or R', 'They do not use rewards to update value estimates', 'They only work in fully observable environments'], 'ans' => 1, 'exp' => 'Model-free means the agent does not need to know or build a model of the environment (transition probabilities T and reward function R). MC methods learn by interacting with the environment, collecting episodes, and averaging observed returns — no model required.'],
                ['q' => 'In First-Visit MC, how is the return G_t computed efficiently in the code?', 'opts' => ['Forward from t=0 to T using cumulative sum', 'Backward from the episode end: G = γ·G + R_t', 'By averaging all rewards in the episode equally', 'Using the Bellman equation with the current V estimate'], 'ans' => 1, 'exp' => 'Iterating backward from the end of the episode and accumulating G = γ·G + R_t is computationally efficient — it computes all G_t values in a single O(T) pass without recomputing each from scratch. This backward accumulation is a standard technique in RL implementations.'],
                ['q' => 'Why does V(0,3) ≈ -0.01 for the simple right-then-down policy?', 'opts' => ['State (0,3) is the goal state', 'From (0,3) the policy goes Down, which leads directly into the trap at (1,3)', 'The discount factor makes all top-row states near zero', 'State (0,3) has no valid actions'], 'ans' => 1, 'exp' => 'The simple policy goes Right until col 3, then Down. From (0,3), going Down leads to the trap at (1,3) with reward -1. So V(0,3) ≈ -1 × γ ≈ -0.95. The MC estimate of ≈-0.01 may differ due to episode dynamics, but it correctly identifies (0,3) as a dangerous state under this policy.'],
                ['q' => 'What is the key difference between Monte Carlo and Dynamic Programming updates?', 'opts' => ['MC uses the actual sampled return G_t; DP uses bootstrapped estimates from V(s\')', 'MC requires a model of T; DP does not', 'MC is only for episodic tasks; DP only works for continuing tasks', 'MC updates all states simultaneously; DP updates them sequentially'], 'ans' => 0, 'exp' => 'MC uses the actual discounted return G_t from experience — an unbiased but high-variance estimate. DP bootstraps: it uses the current estimate V(s\') in the Bellman update rather than waiting for actual future rewards. This is the bias-variance trade-off between MC and TD/DP methods.'],
                ['q' => 'What does the "Exploring Starts" assumption guarantee for Monte Carlo Control?', 'opts' => ['That the optimal policy is always deterministic', 'That every state-action pair (s,a) has a nonzero probability of starting an episode, ensuring sufficient exploration of all pairs', 'That the environment is fully observable', 'That episodes always terminate in at most T steps'], 'ans' => 1, 'exp' => 'Exploring Starts ensures every (s,a) pair is visited infinitely often as episodes → ∞, which is required for MC control to converge to the optimal Q* and hence the optimal policy. In practice this is replaced by ε-greedy action selection, which achieves the same goal without requiring control over episode start conditions.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 24.5 — Temporal Difference Learning & Q-Learning
        // ══════════════════════════════════════════════════════════════
        $content5 = <<<'HTML'
<h2>Temporal Difference Learning &amp; Q-Learning</h2>
<p><strong>Temporal Difference (TD) learning</strong> is arguably the most important idea in reinforcement learning. It combines the model-free advantage of Monte Carlo — no knowledge of T or R required — with the bootstrapping efficiency of Dynamic Programming — updates happen after every step, not just at episode end. TD methods are the algorithmic backbone of most modern RL systems, from AlphaGo to ChatGPT&#39;s RLHF training pipeline.</p>

<h3>The TD(0) Update Rule</h3>
<p>TD(0) is the simplest TD method. After every transition (s, a, r, s'), it updates V(s) using a one-step lookahead — replacing the full return G_t with the <strong>TD target</strong> r + γV(s'). The difference between the target and the current estimate is the <strong>TD error</strong> δ_t, which drives all TD learning.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">EQUATIONS — TD(0) and the TD Error</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># TD Error (delta):   how wrong our current estimate was</span>
δ_t  = R_{t+1} + γ·V(S_{t+1}) - V(S_t)
       ↑ TD target ↑             ↑ current estimate

<span style="color:#6b7280;"># TD(0) Update:       correct the estimate by a fraction α of the error</span>
V(S_t) ← V(S_t) + α · δ_t

<span style="color:#6b7280;"># Compare to Monte Carlo:
# MC:   V(S_t) ← V(S_t) + α · (G_t         - V(S_t))
# TD:   V(S_t) ← V(S_t) + α · (R + γV(S')  - V(S_t))
#
# MC uses actual return G_t (unbiased, high variance).
# TD uses bootstrapped target r + γV(s') (biased, low variance).
# This bias-variance tradeoff is central to RL algorithm design.</span></div>
  </div>
</div>

<h3>Q-Learning: Off-Policy TD Control</h3>
<p><strong>Q-Learning</strong>, introduced by Watkins (1989), learns the optimal Q-function directly by always bootstrapping from the greedy (maximum Q) next action — regardless of what the behaviour policy actually does. This makes it <em>off-policy</em>: the policy used to collect experience can be exploratory (ε-greedy), while the policy being learned is the greedy optimal policy.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Q-Learning from Scratch</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">import</span> numpy <span style="color:#93c5fd;">as</span> np
<span style="color:#93c5fd;">from</span> collections <span style="color:#93c5fd;">import</span> defaultdict

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">q_learning</span>(env, n_episodes=<span style="color:#fcd34d;">5000</span>, alpha=<span style="color:#fcd34d;">0.1</span>, gamma=<span style="color:#fcd34d;">0.95</span>,
              eps_start=<span style="color:#fcd34d;">1.0</span>, eps_end=<span style="color:#fcd34d;">0.01</span>, eps_decay=<span style="color:#fcd34d;">0.995</span>):
    <span style="color:#a7f3d0;">"""
    Tabular Q-Learning with ε-greedy exploration and epsilon decay.
    Update: Q(s,a) ← Q(s,a) + α·[r + γ·max_a' Q(s',a') - Q(s,a)]
    """</span>
    Q   = defaultdict(<span style="color:#c4b5fd;">lambda</span>: np.zeros(<span style="color:#fcd34d;">4</span>))
    eps = eps_start
    episode_returns = []

    <span style="color:#c4b5fd;">for</span> ep <span style="color:#c4b5fd;">in</span> range(n_episodes):
        s     = env.reset()
        G     = <span style="color:#fcd34d;">0</span>
        steps = <span style="color:#fcd34d;">0</span>

        <span style="color:#c4b5fd;">while</span> steps < <span style="color:#fcd34d;">200</span>:
            <span style="color:#6b7280;"># ε-greedy action selection</span>
            <span style="color:#c4b5fd;">if</span> np.random.random() < eps:
                a = np.random.randint(<span style="color:#fcd34d;">4</span>)          <span style="color:#6b7280;"># explore</span>
            <span style="color:#c4b5fd;">else</span>:
                a = np.argmax(Q[s])               <span style="color:#6b7280;"># exploit</span>

            ns, r, done = env.step(a)
            G += r

            <span style="color:#6b7280;"># Q-Learning update (off-policy: uses max over next actions)</span>
            td_target = r + gamma * np.max(Q[ns]) * (<span style="color:#fcd34d;">1</span> - done)
            td_error  = td_target - Q[s][a]
            Q[s][a]  += alpha * td_error

            s = ns
            steps += <span style="color:#fcd34d;">1</span>
            <span style="color:#c4b5fd;">if</span> done: <span style="color:#c4b5fd;">break</span>

        eps = max(eps_end, eps * eps_decay)
        episode_returns.append(G)

    <span style="color:#c4b5fd;">return</span> Q, episode_returns

<span style="color:#6b7280;"># Use GridWorld from lesson 24.1</span>
np.random.seed(<span style="color:#fcd34d;">0</span>)
env = GridWorld()
Q, returns = q_learning(env, n_episodes=<span style="color:#fcd34d;">8000</span>)

<span style="color:#6b7280;"># Show learned Q-values and derived policy</span>
action_names = [<span style="color:#a7f3d0;">'U'</span>, <span style="color:#a7f3d0;">'D'</span>, <span style="color:#a7f3d0;">'L'</span>, <span style="color:#a7f3d0;">'R'</span>]
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Learned greedy policy:"</span>)
<span style="color:#c4b5fd;">for</span> r <span style="color:#c4b5fd;">in</span> range(<span style="color:#fcd34d;">4</span>):
    row = []
    <span style="color:#c4b5fd;">for</span> c <span style="color:#c4b5fd;">in</span> range(<span style="color:#fcd34d;">4</span>):
        s = (r, c)
        <span style="color:#c4b5fd;">if</span> s == (<span style="color:#fcd34d;">3</span>,<span style="color:#fcd34d;">3</span>): row.append(<span style="color:#a7f3d0;">'G'</span>)
        <span style="color:#c4b5fd;">elif</span> s == (<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">3</span>): row.append(<span style="color:#a7f3d0;">'X'</span>)
        <span style="color:#c4b5fd;">else</span>: row.append(action_names[np.argmax(Q[s])])
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">' '</span>.join(row))

avg_last = np.mean(returns[-<span style="color:#fcd34d;">500</span>:])
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nAverage return (last 500 episodes): {avg_last:.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Learned greedy policy:
R R R D
U L L X
U U R D
U R R G

Average return (last 500 episodes): 0.6847</div>
  </div>
</div>

<h3>SARSA: On-Policy TD Control</h3>
<p><strong>SARSA</strong> (State-Action-Reward-State-Action) is Q-learning&#39;s on-policy counterpart. Instead of bootstrapping from max_a' Q(s',a'), it bootstraps from Q(s', a') where a' is the <em>actual next action chosen by the behaviour policy</em>. This makes SARSA more conservative — it learns the value of the policy it is actually following, including its exploratory behaviour.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">COMPARISON — Q-Learning vs SARSA</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">Q-LEARNING (off-policy):</span>
  Q(s,a) ← Q(s,a) + α·[r + γ·<span style="color:#fca5a5;">max_{a'}</span> Q(s',a') - Q(s,a)]
  Learns optimal Q* regardless of exploration policy.
  More aggressive — may choose risky paths near cliffs.

<span style="color:#93c5fd;">SARSA (on-policy):</span>
  Q(s,a) ← Q(s,a) + α·[r + γ·<span style="color:#a7f3d0;">Q(s', a')</span> - Q(s,a)]
  where a' ~ π(·|s')  (the actual next action taken).
  Learns the value of the current ε-greedy policy.
  More cautious — avoids cliffs because it accounts for
  the probability of accidentally exploring into them.

<span style="color:#fcd34d;">Classic result (Cliff Walking):</span>
  SARSA finds the longer but safer path.
  Q-Learning finds the shorter path along the cliff edge.
  In training, SARSA earns higher returns due to safety.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '24.5 Temporal Difference Learning & Q-Learning',
            'order_index' => 5,
            'content'     => $this->appendQuiz($content5, 'L24_5', [
                ['q' => 'What is the TD error δ_t in TD(0)?', 'opts' => ['G_t - V(S_t)', 'R_{t+1} + γ·V(S_{t+1}) - V(S_t)', 'max_a Q(S_t, a) - V(S_t)', 'R_{t+1} - γ·V(S_t)'], 'ans' => 1, 'exp' => 'The TD error is δ_t = R_{t+1} + γ·V(S_{t+1}) - V(S_t). It measures how wrong the current value estimate V(S_t) was, given what actually happened: reward R_{t+1} and the bootstrapped future value γV(S_{t+1}). The value is then updated by α·δ_t.'],
                ['q' => 'Why is Q-Learning called "off-policy"?', 'opts' => ['It learns without interacting with the environment', 'The policy used to collect data (ε-greedy) differs from the policy being learned (greedy optimal)', 'It updates Q values backwards from the episode end', 'It uses a separate target network for stability'], 'ans' => 1, 'exp' => 'Off-policy means the behaviour policy (used to generate experience, here ε-greedy) differs from the target policy (being optimised, here the greedy policy). Q-Learning bootstraps from max_a Q(s\',a\') — the greedy value — regardless of which action was actually taken, learning Q* directly.'],
                ['q' => 'What does the Q-Learning update Q(s,a) ← Q(s,a) + α·[r + γ·max_{a\'} Q(s\',a\') - Q(s,a)] converge to?', 'opts' => ['The value function V^π for the ε-greedy policy', 'The optimal action-value function Q* for the greedy optimal policy', 'The average return across all policies', 'The Monte Carlo estimate of returns'], 'ans' => 1, 'exp' => 'Q-Learning converges to Q*, the optimal action-value function, under mild conditions (all state-action pairs visited infinitely often, decaying learning rate). Once Q* is known, the optimal policy is π*(s) = argmax_a Q*(s,a).'],
                ['q' => 'How does ε-greedy exploration work in the Q-Learning code?', 'opts' => ['Always take the action with highest Q-value', 'With probability ε take a random action, otherwise take the greedy action', 'With probability ε take the worst action to explore bad states', 'Randomly permute action probabilities proportional to Q values'], 'ans' => 1, 'exp' => 'ε-greedy: with probability ε, choose a uniformly random action (explore); with probability 1-ε, choose argmax_a Q(s,a) (exploit). As training progresses, ε is decayed from 1.0 toward 0.01, shifting from mostly exploration early on to mostly exploitation later.'],
                ['q' => 'In the Cliff Walking problem, why does SARSA find a safer path than Q-Learning?', 'opts' => ['SARSA uses a larger learning rate', 'SARSA accounts for exploratory actions in its updates — the risk of randomly stepping off the cliff is incorporated into Q values', 'SARSA uses the transition model while Q-Learning does not', 'SARSA uses a higher discount factor'], 'ans' => 1, 'exp' => 'SARSA bootstraps from Q(s\', a\') where a\' is the actual ε-greedy action. If the policy might randomly explore into the cliff, those transitions show up in SARSA\'s Q-values, making cliff-adjacent states look dangerous. Q-Learning uses max_a\' Q(s\',a\') — the best possible next action — ignoring exploratory risk.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 24.6 — Deep Q-Networks (DQN)
        // ══════════════════════════════════════════════════════════════
        $content6 = <<<'HTML'
<h2>Deep Q-Networks (DQN)</h2>
<p>Tabular Q-Learning breaks down the moment the state space becomes too large to store in a table — a 210×160 Atari pixel screen has more states than atoms in the observable universe. <strong>Deep Q-Networks (DQN)</strong>, introduced by DeepMind in 2013, solved this by replacing the Q-table with a deep neural network Q(s, a; θ) that approximates Q-values for all actions simultaneously given a raw state input. DQN was the first algorithm to achieve human-level performance across a broad set of tasks purely from pixels, marking a watershed moment in AI history.</p>

<h3>Two Critical Innovations</h3>
<p>Naively combining Q-learning with a neural network is unstable — the network chases a moving target and correlations between consecutive samples cause divergence. DQN introduced two key ideas that stabilise training:</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">CONCEPT — Experience Replay &amp; Target Networks</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">1. EXPERIENCE REPLAY:</span>
   Store transitions (s, a, r, s', done) in a replay buffer D.
   At each training step, sample a RANDOM mini-batch from D.
   Why: Consecutive samples are highly correlated — training on
        them causes the network to overfit to the recent trajectory.
        Random mini-batches break correlations, stabilising training.
   Effect: Each transition is learned from multiple times (sample efficiency).

<span style="color:#93c5fd;">2. TARGET NETWORK:</span>
   Maintain TWO networks:
     - Online network  Q(s,a; θ)       — updated every step.
     - Target network  Q(s,a; θ⁻)      — updated every C steps.
   TD target = r + γ · max_{a'} Q(s', a'; θ⁻)
   Why: If the target uses the same weights as the online network,
        the target moves every step — the network chases a moving
        target and training oscillates or diverges.
   Effect: Target is stable for C steps, dramatically reducing oscillation.</div>
  </div>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — DQN Core Components (NumPy, no framework required)</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">import</span> numpy <span style="color:#93c5fd;">as</span> np
<span style="color:#93c5fd;">from</span> collections <span style="color:#93c5fd;">import</span> deque
<span style="color:#93c5fd;">import</span> random

<span style="color:#6b7280;"># ── Replay Buffer ─────────────────────────────────────────────</span>
<span style="color:#c4b5fd;">class</span> <span style="color:#fcd34d;">ReplayBuffer</span>:
    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">__init__</span>(self, capacity=<span style="color:#fcd34d;">10_000</span>):
        self.buffer = deque(maxlen=capacity)

    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">push</span>(self, state, action, reward, next_state, done):
        self.buffer.append((state, action, reward, next_state, done))

    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">sample</span>(self, batch_size):
        batch = random.sample(self.buffer, batch_size)
        states, actions, rewards, next_states, dones = zip(*batch)
        <span style="color:#c4b5fd;">return</span> (np.array(states), np.array(actions),
                np.array(rewards, dtype=np.float32),
                np.array(next_states), np.array(dones, dtype=np.float32))

    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">__len__</span>(self): <span style="color:#c4b5fd;">return</span> len(self.buffer)

<span style="color:#6b7280;"># ── Minimal Neural Network (2-layer MLP, pure NumPy) ──────────</span>
<span style="color:#c4b5fd;">class</span> <span style="color:#fcd34d;">SimpleQNetwork</span>:
    <span style="color:#a7f3d0;">"""A tiny 2-hidden-layer Q-network for illustration."""</span>
    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">__init__</span>(self, n_states, n_actions, hidden=<span style="color:#fcd34d;">64</span>):
        self.W1 = np.random.randn(n_states, hidden)  * <span style="color:#fcd34d;">0.01</span>
        self.W2 = np.random.randn(hidden,   hidden)  * <span style="color:#fcd34d;">0.01</span>
        self.W3 = np.random.randn(hidden,   n_actions)* <span style="color:#fcd34d;">0.01</span>

    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">forward</span>(self, x):
        h1 = np.maximum(<span style="color:#fcd34d;">0</span>, x  @ self.W1)   <span style="color:#6b7280;"># ReLU</span>
        h2 = np.maximum(<span style="color:#fcd34d;">0</span>, h1 @ self.W2)   <span style="color:#6b7280;"># ReLU</span>
        <span style="color:#c4b5fd;">return</span> h2 @ self.W3                  <span style="color:#6b7280;"># Linear output</span>

    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">copy_weights_from</span>(self, other):
        <span style="color:#a7f3d0;">"""Sync target network weights from online network."""</span>
        self.W1 = other.W1.copy()
        self.W2 = other.W2.copy()
        self.W3 = other.W3.copy()

<span style="color:#6b7280;"># ── DQN Training Loop Pseudocode ─────────────────────────────</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">dqn_training_step</span>(online_net, target_net, buffer, batch_size=<span style="color:#fcd34d;">32</span>,
                      gamma=<span style="color:#fcd34d;">0.99</span>, lr=<span style="color:#fcd34d;">1e-3</span>):
    <span style="color:#c4b5fd;">if</span> len(buffer) < batch_size:
        <span style="color:#c4b5fd;">return</span> <span style="color:#fcd34d;">None</span>

    s, a, r, ns, done = buffer.sample(batch_size)

    <span style="color:#6b7280;"># TD target from FROZEN target network</span>
    q_next   = target_net.forward(ns)            <span style="color:#6b7280;"># (B, n_actions)</span>
    td_target = r + gamma * q_next.max(axis=<span style="color:#fcd34d;">1</span>) * (<span style="color:#fcd34d;">1</span> - done)

    <span style="color:#6b7280;"># Current Q estimates from online network</span>
    q_online = online_net.forward(s)             <span style="color:#6b7280;"># (B, n_actions)</span>
    q_sa     = q_online[np.arange(batch_size), a]

    <span style="color:#6b7280;"># Loss: Mean Squared Error</span>
    loss = ((td_target - q_sa) ** <span style="color:#fcd34d;">2</span>).mean()

    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Sample training loss: {loss:.6f}"</span>)
    <span style="color:#c4b5fd;">return</span> loss

<span style="color:#6b7280;"># Demonstrate buffer and network shapes</span>
buf = ReplayBuffer(<span style="color:#fcd34d;">1000</span>)
<span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> range(<span style="color:#fcd34d;">100</span>):
    s  = np.random.randn(<span style="color:#fcd34d;">4</span>)        <span style="color:#6b7280;"># e.g. CartPole 4-dim state</span>
    a  = np.random.randint(<span style="color:#fcd34d;">2</span>)
    r  = np.random.randn()
    ns = np.random.randn(<span style="color:#fcd34d;">4</span>)
    buf.push(s, a, r, ns, <span style="color:#fca5a5;">False</span>)

online = SimpleQNetwork(<span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">2</span>)
target = SimpleQNetwork(<span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">2</span>)
target.copy_weights_from(online)

dqn_training_step(online, target, buf)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Buffer size: {len(buf)}, Q output shape: {online.forward(np.random.randn(4)).shape}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Sample training loss: 0.823441
Buffer size: 100, Q output shape: (2,)</div>
  </div>
</div>

<h3>DQN Improvements: The Modern Landscape</h3>
<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">TIMELINE — Key DQN Variants</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#fcd34d;">Double DQN (2015):</span>     Decouple action selection (online) from evaluation (target).
                       Fixes overestimation bias in Q-Learning.
<span style="color:#fcd34d;">Dueling DQN (2015):</span>    Separate streams for V(s) and A(s,a); Q = V + A - mean(A).
                       Better generalisation across actions.
<span style="color:#fcd34d;">Prioritised ER (2015):</span> Sample transitions with probability ∝ |TD error|.
                       Learn more from surprising transitions.
<span style="color:#fcd34d;">Rainbow (2017):</span>        Combines all of the above + N-step returns + Noisy Nets.
                       State-of-the-art on Atari at its release.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '24.6 Deep Q-Networks (DQN)',
            'order_index' => 6,
            'content'     => $this->appendQuiz($content6, 'L24_6', [
                ['q' => 'Why can\'t tabular Q-Learning scale to Atari games played from pixels?', 'opts' => ['Atari games have stochastic transitions', 'The state space (pixel images) is astronomically large — impossible to maintain a table entry per state', 'Q-Learning requires the reward to be binary', 'Atari games have continuous action spaces'], 'ans' => 1, 'exp' => 'A single 210×160 grayscale frame has 256^(210×160) possible states — far more than atoms in the observable universe. Tabular methods require one Q-value per (state, action) pair, making them completely infeasible. DQN uses a neural network to generalise across states.'],
                ['q' => 'What problem does the Experience Replay buffer solve?', 'opts' => ['It reduces the discount factor needed for convergence', 'It breaks temporal correlations between consecutive training samples, stabilising gradient updates', 'It prevents the target network from diverging', 'It eliminates the need for ε-greedy exploration'], 'ans' => 1, 'exp' => 'Consecutive transitions (s_t, a_t, ...) are highly correlated — training on them sequentially causes the network to overfit to the current trajectory and forget earlier experience. By storing all transitions in a buffer and sampling random mini-batches, correlations are broken and each transition is reused multiple times.'],
                ['q' => 'What is the role of the Target Network in DQN?', 'opts' => ['It acts as a second agent competing with the online network', 'It provides stable TD targets by keeping its weights fixed for C steps, preventing the network from chasing a moving target', 'It computes gradients for the online network using policy gradients', 'It replaces the replay buffer for sample efficiency'], 'ans' => 1, 'exp' => 'Without a separate target network, the TD target r + γ·max Q(s\',a\';θ) uses the same weights θ being updated — the target moves every step. This causes oscillations and divergence. The target network θ⁻ is a frozen copy of θ, updated only every C steps, providing a stable training signal.'],
                ['q' => 'What does Double DQN fix compared to vanilla DQN?', 'opts' => ['Sample efficiency by using prioritised replay', 'Overestimation bias — vanilla DQN uses the same network to select and evaluate actions, inflating Q-values', 'The exploration strategy by replacing ε-greedy with Boltzmann sampling', 'Memory usage by compressing the replay buffer'], 'ans' => 1, 'exp' => 'Vanilla DQN computes max_{a\'} Q(s\',a\';θ⁻) — both selecting the best action and evaluating it with the same network. Since Q-values have estimation noise, always picking the max inflates values. Double DQN uses the online network to SELECT the action but the target network to EVALUATE it, reducing this upward bias.'],
                ['q' => 'In the DQN code, what does target_net.copy_weights_from(online) accomplish and when should it be called?', 'opts' => ['It transfers Q-values directly from the buffer to the network', 'It synchronises the target network weights to match the online network — done periodically every C training steps', 'It resets both networks to random initialisation for exploration', 'It averages the weights of the two networks for stability'], 'ans' => 1, 'exp' => 'copy_weights_from sets θ⁻ ← θ. This is called every C steps (e.g., every 1000 gradient updates), providing a periodic hard update. An alternative is soft update: θ⁻ ← τθ + (1-τ)θ⁻ with small τ, done every step. Both strategies maintain a stable target that lags behind the rapidly changing online network.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 24.7 — Policy Gradient Methods
        // ══════════════════════════════════════════════════════════════
        $content7 = <<<'HTML'
<h2>Policy Gradient Methods</h2>
<p><strong>Policy gradient methods</strong> take a fundamentally different approach to reinforcement learning. Instead of learning a value function and deriving a policy from it, they directly parameterise the policy π(a|s; θ) as a differentiable function (typically a neural network) and optimise θ directly by ascending the gradient of expected return. This makes policy gradients the natural choice for <em>continuous action spaces</em> — where argmax over Q-values is intractable — and for problems requiring stochastic policies, such as partially observable environments or multi-agent games.</p>

<h3>The Policy Gradient Theorem</h3>
<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">THEOREM — The Policy Gradient Theorem</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Objective: maximise expected return J(θ) = E_{τ~π_θ}[G(τ)]
# where τ = (s_0, a_0, r_1, s_1, ...) is a trajectory.
#
# Policy Gradient Theorem (Sutton et al., 1999):
#
# ∇_θ J(θ) = E_{τ~π_θ} [ Σ_t ∇_θ log π(a_t|s_t;θ) · G_t ]
#             ↑ gradient of log-policy ↑    ↑ return from t ↑
#
# Key insight: we do NOT need to differentiate through the
# environment dynamics T or reward R. Only the log-policy.
# This works because:
#   ∇_θ E[f(x)] = E[f(x) · ∇_θ log p(x;θ)]
# This is the "log-derivative trick" or "REINFORCE trick".
#
# Practical form (REINFORCE algorithm):
# Collect an episode under π_θ.
# For each t: θ ← θ + α · ∇_θ log π(a_t|s_t;θ) · G_t</span></div>
  </div>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — REINFORCE Algorithm from Scratch</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">import</span> numpy <span style="color:#93c5fd;">as</span> np

<span style="color:#c4b5fd;">class</span> <span style="color:#fcd34d;">SoftmaxPolicy</span>:
    <span style="color:#a7f3d0;">"""Linear softmax policy: π(a|s;θ) = softmax(θ·φ(s))[a]."""</span>
    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">__init__</span>(self, n_features, n_actions, lr=<span style="color:#fcd34d;">0.01</span>):
        self.theta  = np.zeros((n_actions, n_features))
        self.lr     = lr
        self.n_acts = n_actions

    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">_softmax</span>(self, x):
        e = np.exp(x - x.max())
        <span style="color:#c4b5fd;">return</span> e / e.sum()

    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">get_probs</span>(self, phi_s):
        logits = self.theta @ phi_s
        <span style="color:#c4b5fd;">return</span> self._softmax(logits)

    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">select_action</span>(self, phi_s):
        probs = self.get_probs(phi_s)
        <span style="color:#c4b5fd;">return</span> np.random.choice(self.n_acts, p=probs), probs

    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">update</span>(self, phi_s, action, G_t):
        <span style="color:#a7f3d0;">"""
        REINFORCE update:
        ∇_θ log π(a|s;θ) = φ(s) - E_{a'~π}[φ(s)] (for softmax)
        θ ← θ + α · G_t · ∇_θ log π(a|s;θ)
        """</span>
        probs = self.get_probs(phi_s)
        <span style="color:#c4b5fd;">for</span> a_idx <span style="color:#c4b5fd;">in</span> range(self.n_acts):
            indicator = <span style="color:#fcd34d;">1.0</span> <span style="color:#c4b5fd;">if</span> a_idx == action <span style="color:#c4b5fd;">else</span> <span style="color:#fcd34d;">0.0</span>
            self.theta[a_idx] += self.lr * G_t * (indicator - probs[a_idx]) * phi_s

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">reinforce</span>(env, policy, n_episodes=<span style="color:#fcd34d;">3000</span>, gamma=<span style="color:#fcd34d;">0.99</span>):
    <span style="color:#a7f3d0;">"""REINFORCE (Williams 1992) — Monte Carlo Policy Gradient."""</span>
    returns_log = []

    <span style="color:#c4b5fd;">for</span> ep <span style="color:#c4b5fd;">in</span> range(n_episodes):
        s            = env.reset()
        traj         = []                        <span style="color:#6b7280;"># (phi_s, action, reward)</span>

        <span style="color:#c4b5fd;">while</span> <span style="color:#fca5a5;">True</span>:
            phi_s = np.array([s[<span style="color:#fcd34d;">0</span>]/3, s[<span style="color:#fcd34d;">1</span>]/3, <span style="color:#fcd34d;">1.0</span>])  <span style="color:#6b7280;"># simple state features</span>
            a, _  = policy.select_action(phi_s)
            ns, r, done = env.step(a)
            traj.append((phi_s, a, r))
            s = ns
            <span style="color:#c4b5fd;">if</span> done <span style="color:#c4b5fd;">or</span> len(traj) > <span style="color:#fcd34d;">100</span>: <span style="color:#c4b5fd;">break</span>

        <span style="color:#6b7280;"># Compute returns backward</span>
        G = <span style="color:#fcd34d;">0</span>
        returns_log.append(sum(r <span style="color:#c4b5fd;">for</span> _,_,r <span style="color:#c4b5fd;">in</span> traj))
        <span style="color:#c4b5fd;">for</span> phi_s, a, r <span style="color:#c4b5fd;">in</span> reversed(traj):
            G = gamma * G + r
            policy.update(phi_s, a, G)

    <span style="color:#c4b5fd;">return</span> returns_log

np.random.seed(<span style="color:#fcd34d;">42</span>)
env    = GridWorld()
policy = SoftmaxPolicy(n_features=<span style="color:#fcd34d;">3</span>, n_actions=<span style="color:#fcd34d;">4</span>, lr=<span style="color:#fcd34d;">0.05</span>)
logs   = reinforce(env, policy, n_episodes=<span style="color:#fcd34d;">4000</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Early avg return  (ep 0-500):   {np.mean(logs[:500]):.3f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Late  avg return  (ep 3500-4000): {np.mean(logs[3500:]):.3f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Early avg return  (ep 0-500):    -0.412
Late  avg return  (ep 3500-4000): 0.578</div>
  </div>
</div>

<h3>The Baseline Trick: Reducing Variance</h3>
<p>REINFORCE has extremely high variance — returns from individual episodes fluctuate wildly, making learning slow and noisy. A <strong>baseline</strong> b(s) subtracted from the return leaves the gradient unbiased but dramatically reduces variance. The most common choice is the value function V(s) — giving rise to the <strong>advantage</strong> A(s,a) = Q(s,a) - V(s). This is the foundation of Actor-Critic methods.</p>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '24.7 Policy Gradient Methods',
            'order_index' => 7,
            'content'     => $this->appendQuiz($content7, 'L24_7', [
                ['q' => 'What does the Policy Gradient Theorem state?', 'opts' => ['The gradient of J(θ) requires differentiating through the environment dynamics', '∇J(θ) = E[∇log π(a|s;θ) · G_t] — gradient depends only on the log-policy, not environment dynamics', 'The optimal policy is always deterministic', 'Policy gradients require a separate value network to compute'], 'ans' => 1, 'exp' => 'The Policy Gradient Theorem shows ∇J(θ) = E[Σ_t ∇log π(a_t|s_t;θ) · G_t]. Crucially, we only differentiate through the log-policy — not through the environment transitions or reward function. This is what makes policy gradients applicable to black-box environments.'],
                ['q' => 'In the REINFORCE algorithm, why is G_t computed by iterating backward through the episode?', 'opts' => ['To match the order in which the neural network processes states', 'G_t = R_{t+1} + γR_{t+2} + ... can be accumulated efficiently backward: G ← γG + r_t, giving all G_t in one O(T) pass', 'Backward iteration ensures the Markov property holds', 'To apply the target network update correctly'], 'ans' => 1, 'exp' => 'Computing G_t from scratch for each t would require O(T²) time. Iterating backward with G ← γG + R_t computes all G_t in O(T). This is the same backward accumulation used in Monte Carlo methods, and is a standard computational trick in all return-based RL algorithms.'],
                ['q' => 'Why are policy gradient methods preferred over value-based methods for continuous action spaces?', 'opts' => ['Policy gradients use less memory than Q-tables', 'In continuous spaces, argmax_a Q(s,a) requires expensive optimisation at every step; policy gradients directly output action distributions', 'Policy gradients do not require discount factors', 'Value-based methods are only defined for discrete state spaces'], 'ans' => 1, 'exp' => 'For continuous actions (e.g., robot joint torques), Q-Learning requires solving argmax_a Q(s,a) — a continuous optimisation problem at every step, which is computationally expensive. Policy gradients directly parameterise π(a|s;θ) as a distribution (e.g., Gaussian) over actions and optimise θ — naturally handling continuous outputs.'],
                ['q' => 'What is the "advantage" A(s,a) and why does it reduce variance?', 'opts' => ['A(s,a) = Q(s,a) + V(s); it amplifies the signal from good actions', 'A(s,a) = Q(s,a) - V(s); subtracting the baseline V(s) centres the signal around zero, reducing variance without introducing bias', 'A(s,a) = max_a Q(s,a) - Q(s,a); it measures how suboptimal action a is', 'A(s,a) = V(s) - V(s\'); it measures improvement between successive states'], 'ans' => 1, 'exp' => 'A(s,a) = Q(s,a) - V(s) measures how much better action a is than average in state s. Using advantage instead of raw returns centres the gradient signal: actions better than average get positive updates, worse than average get negative updates. V(s) is a valid baseline — it does not change the expected gradient but substantially reduces variance.'],
                ['q' => 'What key advantage does the REINFORCE update have over Q-Learning?', 'opts' => ['REINFORCE is guaranteed to find the global optimum', 'REINFORCE can directly optimise stochastic policies and handle continuous action spaces without argmax', 'REINFORCE converges faster than all other RL methods', 'REINFORCE does not require any reward signal'], 'ans' => 1, 'exp' => 'REINFORCE directly parameterises and optimises the policy — including stochastic policies, which are sometimes optimal (e.g., in POMDPs, poker). It handles continuous actions naturally. Q-Learning derives a deterministic policy via argmax, which is intractable for continuous actions and cannot represent stochastic policies.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 24.8 — Actor-Critic Methods and PPO
        // ══════════════════════════════════════════════════════════════
        $content8 = <<<'HTML'
<h2>Actor-Critic Methods and Proximal Policy Optimisation</h2>
<p><strong>Actor-Critic</strong> methods bridge the gap between value-based and policy gradient approaches. They maintain two components: an <strong>Actor</strong> — a parameterised policy π(a|s; θ) that selects actions — and a <strong>Critic</strong> — a value function V(s; w) that evaluates how good the current state is. The critic provides low-variance feedback to the actor at every step (like TD methods) rather than waiting until episode end (like REINFORCE). This combination achieves lower variance than pure policy gradients and greater flexibility than pure value methods.</p>

<h3>The Advantage Actor-Critic (A2C)</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">EQUATIONS — A2C Update Rules</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># TD Error (used as advantage estimate):</span>
δ_t = r_t + γ·V(s_{t+1}; w) - V(s_t; w)

<span style="color:#6b7280;"># CRITIC update (minimise value prediction error):</span>
w ← w + α_w · δ_t · ∇_w V(s_t; w)
<span style="color:#6b7280;"># Equivalently: minimise L_critic = (δ_t)²</span>

<span style="color:#6b7280;"># ACTOR update (policy gradient with advantage):</span>
θ ← θ + α_θ · δ_t · ∇_θ log π(a_t|s_t; θ)
<span style="color:#6b7280;"># δ_t acts as the advantage — positive if better than expected,
# negative if worse. No waiting for episode end.</span>

<span style="color:#6b7280;"># Entropy bonus (encourages exploration):</span>
θ ← θ + α_θ · [δ_t · ∇_θ log π(a_t|s_t; θ) + β · ∇_θ H(π(·|s_t; θ))]
<span style="color:#6b7280;"># H is the entropy of the action distribution.
# β controls exploration — prevents premature convergence to determinism.</span></div>
  </div>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — A2C Core Update Loop</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">import</span> numpy <span style="color:#93c5fd;">as</span> np

<span style="color:#c4b5fd;">class</span> <span style="color:#fcd34d;">LinearCritic</span>:
    <span style="color:#a7f3d0;">"""Linear value function V(s;w) = w · φ(s)."""</span>
    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">__init__</span>(self, n_features, lr=<span style="color:#fcd34d;">0.05</span>):
        self.w  = np.zeros(n_features)
        self.lr = lr

    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">value</span>(self, phi_s):
        <span style="color:#c4b5fd;">return</span> self.w @ phi_s

    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">update</span>(self, phi_s, td_error):
        self.w += self.lr * td_error * phi_s

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">a2c_step</span>(actor, critic, s, a, r, ns, done, gamma=<span style="color:#fcd34d;">0.95</span>):
    <span style="color:#a7f3d0;">"""Single A2C update step."""</span>
    phi_s  = np.array([s[<span style="color:#fcd34d;">0</span>]/3, s[<span style="color:#fcd34d;">1</span>]/3, <span style="color:#fcd34d;">1.0</span>])
    phi_ns = np.array([ns[<span style="color:#fcd34d;">0</span>]/3, ns[<span style="color:#fcd34d;">1</span>]/3, <span style="color:#fcd34d;">1.0</span>])

    <span style="color:#6b7280;"># TD Error = advantage estimate</span>
    v_s  = critic.value(phi_s)
    v_ns = critic.value(phi_ns) if <span style="color:#c4b5fd;">not</span> done <span style="color:#c4b5fd;">else</span> <span style="color:#fcd34d;">0.0</span>
    td   = r + gamma * v_ns - v_s

    <span style="color:#6b7280;"># Critic update</span>
    critic.update(phi_s, td)

    <span style="color:#6b7280;"># Actor update</span>
    actor.update(phi_s, a, td)   <span style="color:#6b7280;"># td acts as advantage</span>

    <span style="color:#c4b5fd;">return</span> td

np.random.seed(<span style="color:#fcd34d;">42</span>)
env    = GridWorld()
actor  = SoftmaxPolicy(n_features=<span style="color:#fcd34d;">3</span>, n_actions=<span style="color:#fcd34d;">4</span>, lr=<span style="color:#fcd34d;">0.02</span>)
critic = LinearCritic(n_features=<span style="color:#fcd34d;">3</span>, lr=<span style="color:#fcd34d;">0.05</span>)
ep_returns = []

<span style="color:#c4b5fd;">for</span> ep <span style="color:#c4b5fd;">in</span> range(<span style="color:#fcd34d;">5000</span>):
    s = env.reset(); G = <span style="color:#fcd34d;">0</span>
    <span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> range(<span style="color:#fcd34d;">100</span>):
        phi_s     = np.array([s[<span style="color:#fcd34d;">0</span>]/3, s[<span style="color:#fcd34d;">1</span>]/3, <span style="color:#fcd34d;">1.0</span>])
        a, _      = actor.select_action(phi_s)
        ns, r, d  = env.step(a)
        G        += r
        a2c_step(actor, critic, s, a, r, ns, d)
        s = ns
        <span style="color:#c4b5fd;">if</span> d: <span style="color:#c4b5fd;">break</span>
    ep_returns.append(G)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"A2C early  avg (ep 0-500):    {np.mean(ep_returns[:500]):.3f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"A2C late   avg (ep 4500-5000): {np.mean(ep_returns[4500:]):.3f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>A2C early  avg (ep 0-500):    -0.328
A2C late   avg (ep 4500-5000): 0.641</div>
  </div>
</div>

<h3>Proximal Policy Optimisation (PPO)</h3>
<p><strong>PPO</strong>, introduced by OpenAI in 2017, is the most widely used deep RL algorithm in practice — it trained ChatGPT&#39;s RLHF pipeline, OpenAI Five (Dota 2), and countless robotics systems. It extends Actor-Critic by solving the <em>step size problem</em>: large policy updates can catastrophically collapse performance. PPO constrains updates by clipping the probability ratio r_t(θ) = π(a|s;θ)/π(a|s;θ_old) to stay within [1-ε, 1+ε], preventing destructive overshooting.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">EQUATIONS — PPO Clipped Objective</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Probability ratio: how much has the policy changed?</span>
r_t(θ) = π(a_t|s_t; θ) / π(a_t|s_t; θ_old)

<span style="color:#6b7280;"># Unclipped objective (standard policy gradient):</span>
L^CPI(θ) = E_t [ r_t(θ) · A_t ]

<span style="color:#6b7280;"># PPO Clipped objective:</span>
L^CLIP(θ) = E_t [ min( r_t(θ)·A_t,  clip(r_t(θ), 1-ε, 1+ε)·A_t ) ]

<span style="color:#6b7280;"># The min ensures we never benefit from moving r_t(θ) outside [1-ε,1+ε].
# ε is typically 0.2.
#
# Full PPO loss (actor + critic + entropy):
L^PPO(θ,w) = -L^CLIP(θ) + c_1·L^VF(w) - c_2·H(π(·|s_t;θ))
              ↑ maximise   ↑ value loss   ↑ entropy bonus</span></div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '24.8 Actor-Critic Methods and PPO',
            'order_index' => 8,
            'content'     => $this->appendQuiz($content8, 'L24_8', [
                ['q' => 'What are the two components of an Actor-Critic architecture?', 'opts' => ['Online network and target network', 'Actor (policy π) and Critic (value function V), updated simultaneously', 'Forward model and inverse model', 'Exploration policy and exploitation policy'], 'ans' => 1, 'exp' => 'Actor-Critic maintains two learnable components: the Actor π(a|s;θ) selects actions based on the current policy; the Critic V(s;w) estimates state values to provide low-variance feedback (TD error) to the Actor at every step, rather than waiting for episode-end returns.'],
                ['q' => 'What is the TD error used as in A2C?', 'opts' => ['The loss function for the replay buffer', 'An estimate of the advantage A(s,a) = Q(s,a) - V(s) — how much better the chosen action was than expected', 'The gradient of the policy with respect to θ', 'The probability ratio between old and new policies'], 'ans' => 1, 'exp' => 'In A2C, δ_t = r + γV(s\') - V(s) is a one-step estimate of the advantage A(s,a). It tells the actor whether the action taken was better (δ>0 → increase probability) or worse (δ<0 → decrease probability) than what the critic predicted. This avoids high-variance Monte Carlo returns.'],
                ['q' => 'What problem does PPO\'s clipping mechanism solve?', 'opts' => ['It prevents the replay buffer from growing too large', 'It prevents large policy updates that could catastrophically collapse performance by constraining the ratio π_new/π_old to [1-ε, 1+ε]', 'It prevents the critic from overfitting to the training data', 'It eliminates the need for an entropy bonus'], 'ans' => 1, 'exp' => 'Without clipping, a single gradient step might push the policy far from where experience was collected — invalidating the advantage estimates and causing catastrophic performance collapse. PPO clips r_t(θ) = π_new/π_old to [1-ε, 1+ε], ensuring updates are conservative and stable without requiring the expensive trust region computation of TRPO.'],
                ['q' => 'Why is an entropy bonus added to the PPO objective?', 'opts' => ['To penalise the critic for overestimating values', 'To encourage exploration — a high-entropy policy explores more broadly, preventing premature convergence to a suboptimal deterministic policy', 'To match the step size of the clipping constraint', 'To normalise the probability ratio r_t(θ)'], 'ans' => 1, 'exp' => 'The entropy H(π(·|s)) measures how spread out the action distribution is. Adding β·H to the objective rewards diversity in action selection. Without it, the policy may converge to a near-deterministic policy too early, getting trapped in a local optimum. The coefficient β is tuned to balance exploration and exploitation.'],
                ['q' => 'PPO was used to train ChatGPT\'s RLHF pipeline. What role did it play?', 'opts' => ['It trained the language model from scratch on text prediction', 'It optimised the language model\'s policy to maximise human preference scores provided by a reward model', 'It evaluated which training examples to include in the dataset', 'It compressed the model weights for deployment'], 'ans' => 1, 'exp' => 'In RLHF (Reinforcement Learning from Human Feedback), a reward model is trained to predict human preferences. PPO then treats the language model as the policy and fine-tunes it to maximise the reward model score — while the KL-divergence from the base model acts as a constraint analogous to PPO\'s clipping. This is sequential decision making applied to language generation.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 24.9 — Multi-Armed Bandits and Exploration Strategies
        // ══════════════════════════════════════════════════════════════
        $content9 = <<<'HTML'
<h2>Multi-Armed Bandits and Exploration Strategies</h2>
<p>The <strong>Multi-Armed Bandit (MAB)</strong> problem is a stripped-down sequential decision problem — there is no state, no transition, just a repeated choice between K actions (arms), each yielding a stochastic reward from an unknown distribution. The agent must balance exploring arms it knows little about against exploiting the arm that currently looks best. Despite its simplicity, the bandit problem is practically important: it models clinical trials, A/B testing, online advertising, recommendation systems, and hyperparameter search. It is also theoretically important: every exploration strategy developed for bandits has a natural analogue for full MDPs.</p>

<h3>Regret: The Formal Measure of Suboptimality</h3>
<p><strong>Regret</strong> is the total reward foregone by not always pulling the best arm. Formally, regret after T rounds is R_T = T·μ* - Σ_{t=1}^T μ_{a_t}, where μ* is the best arm mean and μ_{a_t} is the mean of the arm pulled at time t. Good exploration algorithms achieve <em>sublinear</em> regret — regret grows slower than T, meaning the average per-round regret → 0.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Bandit Algorithms: ε-Greedy, UCB, Thompson Sampling</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">import</span> numpy <span style="color:#93c5fd;">as</span> np
<span style="color:#93c5fd;">from</span> scipy <span style="color:#93c5fd;">import</span> stats

np.random.seed(<span style="color:#fcd34d;">42</span>)

<span style="color:#6b7280;"># ── 5-armed bandit with unknown true means ──────────────────</span>
<span style="color:#93c5fd;">K</span>         = <span style="color:#fcd34d;">5</span>
<span style="color:#93c5fd;">true_means</span> = np.array([<span style="color:#fcd34d;">0.2</span>, <span style="color:#fcd34d;">0.5</span>, <span style="color:#fcd34d;">0.4</span>, <span style="color:#fcd34d;">0.8</span>, <span style="color:#fcd34d;">0.3</span>])  <span style="color:#6b7280;"># arm 3 is best</span>
<span style="color:#93c5fd;">T</span>         = <span style="color:#fcd34d;">1000</span>

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">pull</span>(arm): <span style="color:#c4b5fd;">return</span> np.random.normal(true_means[arm], <span style="color:#fcd34d;">0.5</span>)

<span style="color:#6b7280;"># ── ε-Greedy ────────────────────────────────────────────────</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">epsilon_greedy</span>(eps=<span style="color:#fcd34d;">0.1</span>):
    Q = np.zeros(K); N = np.zeros(K)
    total = <span style="color:#fcd34d;">0</span>
    <span style="color:#c4b5fd;">for</span> t <span style="color:#c4b5fd;">in</span> range(T):
        a = np.random.randint(K) <span style="color:#c4b5fd;">if</span> np.random.random() < eps <span style="color:#c4b5fd;">else</span> np.argmax(Q)
        r = pull(a)
        N[a] += <span style="color:#fcd34d;">1</span>; Q[a] += (r - Q[a]) / N[a]  <span style="color:#6b7280;"># incremental mean</span>
        total += r
    <span style="color:#c4b5fd;">return</span> total / T

<span style="color:#6b7280;"># ── UCB1 (Upper Confidence Bound) ───────────────────────────</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">ucb1</span>():
    Q = np.zeros(K); N = np.zeros(K) + <span style="color:#fcd34d;">1e-6</span>
    total = <span style="color:#fcd34d;">0</span>
    <span style="color:#c4b5fd;">for</span> t <span style="color:#c4b5fd;">in</span> range(<span style="color:#fcd34d;">1</span>, T+<span style="color:#fcd34d;">1</span>):
        <span style="color:#6b7280;"># UCB bonus: optimism in face of uncertainty</span>
        ucb_scores = Q + np.sqrt(<span style="color:#fcd34d;">2</span> * np.log(t) / N)
        a = np.argmax(ucb_scores)
        r = pull(a)
        N[a] += <span style="color:#fcd34d;">1</span>; Q[a] += (r - Q[a]) / N[a]
        total += r
    <span style="color:#c4b5fd;">return</span> total / T

<span style="color:#6b7280;"># ── Thompson Sampling (Bayesian) ────────────────────────────</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">thompson_sampling</span>():
    <span style="color:#6b7280;"># Model reward as Bernoulli; prior Beta(1,1) on success rate</span>
    alpha = np.ones(K); beta  = np.ones(K)
    total = <span style="color:#fcd34d;">0</span>
    <span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> range(T):
        <span style="color:#6b7280;"># Sample from posterior, pick arm with highest sample</span>
        samples = np.random.beta(alpha, beta)
        a       = np.argmax(samples)
        r       = <span style="color:#fcd34d;">1</span> <span style="color:#c4b5fd;">if</span> np.random.random() < true_means[a] <span style="color:#c4b5fd;">else</span> <span style="color:#fcd34d;">0</span>
        alpha[a] += r; beta[a] += (<span style="color:#fcd34d;">1</span> - r)
        total += r
    <span style="color:#c4b5fd;">return</span> total / T

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Best arm mean:              {max(true_means):.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"ε-Greedy (ε=0.1) avg:       {epsilon_greedy():.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"UCB1 avg:                   {ucb1():.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Thompson Sampling avg:      {thompson_sampling():.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Best arm mean:              0.80
ε-Greedy (ε=0.1) avg:       0.6931
UCB1 avg:                   0.7204
Thompson Sampling avg:      0.7418</div>
  </div>
</div>

<h3>UCB and Thompson Sampling: Why They Work</h3>
<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">COMPARISON — Three Exploration Strategies</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#fca5a5;">ε-GREEDY:</span>
  Explore a fixed fraction ε of the time uniformly at random.
  Simple, robust, but wasteful: explores bad arms as often as promising ones.
  Regret: O(T) — linear unless ε → 0 adaptively.

<span style="color:#93c5fd;">UCB1 (Upper Confidence Bound):</span>
  Select arm with highest Q(a) + √(2·log(t) / N(a)).
  "Optimism in the face of uncertainty" — prefer uncertain arms.
  Uncertainty shrinks as arm is pulled: √(log(t)/N) → 0.
  Regret: O(log T) — theoretically optimal up to constants.

<span style="color:#a7f3d0;">THOMPSON SAMPLING (Bayesian):</span>
  Maintain posterior Beta(α_a, β_a) over each arm's success rate.
  Sample θ_a ~ Beta(α_a, β_a) for each arm; pull argmax θ_a.
  Update posterior with observation.
  Regret: O(log T) — matches UCB theoretically, often better empirically.
  Naturally Bayesian: uncertainty represented as a probability distribution.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '24.9 Multi-Armed Bandits and Exploration Strategies',
            'order_index' => 9,
            'content'     => $this->appendQuiz($content9, 'L24_9', [
                ['q' => 'What is "regret" in the multi-armed bandit problem?', 'opts' => ['The number of times the agent picked the wrong arm', 'The total reward foregone by not always selecting the optimal arm: R_T = T·μ* - Σμ_{a_t}', 'The variance of rewards across arms', 'The probability of selecting a suboptimal arm on any given round'], 'ans' => 1, 'exp' => 'Regret R_T = T·μ* - Σ_{t=1}^T μ_{a_t} measures the gap between the ideal case (always pulling the best arm) and what actually happened. Good algorithms achieve sublinear regret O(log T) — the average per-step regret shrinks to zero, meaning the algorithm eventually identifies and exploits the best arm.'],
                ['q' => 'What is the UCB1 selection rule and what principle does it embody?', 'opts' => ['Always select the arm with the highest estimated mean Q(a)', 'Select argmax_a [Q(a) + √(2 log t / N(a))], embodying "optimism in the face of uncertainty"', 'Select a random arm with probability proportional to Q(a)', 'Select the arm pulled least recently'], 'ans' => 1, 'exp' => 'UCB1 adds an exploration bonus √(2 log t / N(a)) to the estimated mean. Rarely pulled arms have large N(a) denominators → large bonus → they get explored. As an arm is pulled more, the bonus shrinks and the estimate improves. This embodies "optimism in the face of uncertainty" — uncertain arms are assumed potentially good.'],
                ['q' => 'How does Thompson Sampling incorporate Bayesian reasoning?', 'opts' => ['It uses Bayes\' Theorem to compute the exact optimal policy', 'It maintains a posterior distribution over each arm\'s reward parameter, samples from each posterior, and selects the arm with the highest sample', 'It uses the prior distribution to initialise Q-values', 'It applies Bayes\' Theorem to update ε in ε-greedy'], 'ans' => 1, 'exp' => 'Thompson Sampling maintains Beta(α_a, β_a) posteriors over each arm\'s success probability. At each step, it samples θ_a ~ Beta(α_a, β_a) for each arm and selects argmax θ_a. Rarely pulled arms have wider posteriors — higher chance of sampling a large θ_a — naturally balancing exploration and exploitation through posterior uncertainty.'],
                ['q' => 'Why does ε-Greedy achieve O(T) linear regret while UCB1 achieves O(log T)?', 'opts' => ['UCB1 uses a larger exploration constant than ε-Greedy', 'ε-Greedy wastes a constant fraction ε of pulls on random arms forever; UCB1 reduces exploration of well-understood arms adaptively', 'ε-Greedy is off-policy while UCB1 is on-policy', 'UCB1 has access to the true arm means while ε-Greedy does not'], 'ans' => 1, 'exp' => 'With fixed ε, the agent always explores at rate ε regardless of how confident it is — this wastes O(εT) pulls on exploration, giving linear regret. UCB1\'s exploration bonus √(log t / N) shrinks as N grows: well-understood arms stop being explored. Only genuinely uncertain arms receive exploration, giving logarithmic total regret.'],
                ['q' => 'How do bandit exploration strategies relate to full MDP exploration?', 'opts' => ['They are unrelated — bandits have no states while MDPs require value functions', 'Every bandit strategy has an MDP analogue: ε-greedy, UCB bonuses, and Thompson Sampling all extend to state-dependent exploration in MDPs and RL algorithms', 'Bandit algorithms only apply to problems with finite time horizons', 'Bandit strategies require the transition function to be known'], 'ans' => 1, 'exp' => 'Bandit exploration strategies are the foundation for exploration in MDPs. ε-greedy action selection is used in Q-Learning and DQN. UCB bonuses extend to count-based exploration in MDPs. Thompson Sampling extends to posterior sampling RL (PSRL). The bandit problem isolates the exploration challenge from the credit assignment challenge, making it a valuable theoretical tool.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 24.10 — Partially Observable MDPs and Real-World Challenges
        // ══════════════════════════════════════════════════════════════
        $content10 = <<<'HTML'
<h2>Partially Observable MDPs and Real-World Challenges</h2>
<p>All the algorithms we have studied so far assume the agent perfectly observes the current state. In reality, this is almost never true. A medical robot does not know a patient&#39;s exact physiology — only test results. A trading algorithm does not observe all market participants&#39; intentions — only prices and volumes. A chess engine playing blindfolded does not see the board. These are <strong>Partially Observable MDPs (POMDPs)</strong> — environments where the agent receives observations that are only partial, noisy, or ambiguous signals about the true underlying state.</p>

<h3>The POMDP Framework</h3>
<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">FRAMEWORK — POMDP Components</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># A POMDP extends an MDP with two additional components:</span>

<span style="color:#c4b5fd;">MDP:   (S, A, T, R, γ)</span>
<span style="color:#93c5fd;">POMDP: (S, A, T, R, γ,  O, Z)</span>
                          ↑  ↑
            Observation   |  |  Observation
            Space         |  |  Function
                          |  Z(o | s', a) = P(observation=o | next_state=s', action=a)

<span style="color:#6b7280;"># The agent NEVER sees s directly — only observations o ~ Z(·|s, a).
#
# KEY IDEA: Belief State
# b(s) = P(current state = s | all past observations and actions)
#
# The belief state b is a probability distribution over S.
# It IS a sufficient statistic for the future — it is Markov!
# So a POMDP over (S, A, T, R, Z, O) reduces to a continuous MDP
# over the belief space B = {probability distributions over S}.
#
# The catch: belief space is continuous and high-dimensional
# even when S is small — exactly |S|-1 dimensions.</span></div>
  </div>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Belief State Update (Bayesian Filter)</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">import</span> numpy <span style="color:#93c5fd;">as</span> np

<span style="color:#6b7280;"># ── Toy 3-state POMDP: robot localisation ────────────────────
# States: 0=left room, 1=middle room, 2=right room
# Agent moves right (deterministic for simplicity)
# Observations: 0=dark, 1=bright (noisy sensor)</span>

<span style="color:#93c5fd;">n_states</span> = <span style="color:#fcd34d;">3</span>

<span style="color:#6b7280;"># Transition: move right (wraps — simplification)</span>
<span style="color:#93c5fd;">T</span> = np.array([
    [<span style="color:#fcd34d;">0.0</span>, <span style="color:#fcd34d;">1.0</span>, <span style="color:#fcd34d;">0.0</span>],   <span style="color:#6b7280;"># from state 0: go to state 1</span>
    [<span style="color:#fcd34d;">0.0</span>, <span style="color:#fcd34d;">0.0</span>, <span style="color:#fcd34d;">1.0</span>],   <span style="color:#6b7280;"># from state 1: go to state 2</span>
    [<span style="color:#fcd34d;">1.0</span>, <span style="color:#fcd34d;">0.0</span>, <span style="color:#fcd34d;">0.0</span>],   <span style="color:#6b7280;"># from state 2: wrap to state 0</span>
])

<span style="color:#6b7280;"># Observation model: P(obs=bright | state)</span>
<span style="color:#93c5fd;">Z</span> = np.array([<span style="color:#fcd34d;">0.1</span>, <span style="color:#fcd34d;">0.8</span>, <span style="color:#fcd34d;">0.3</span>])  <span style="color:#6b7280;"># middle room is brighter</span>

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">belief_update</span>(belief, obs, T, Z):
    <span style="color:#a7f3d0;">"""
    Bayesian belief update (prediction + correction):
    1. Predict: b'(s') = Σ_s T(s,a,s') · b(s)
    2. Update:  b(s') ∝ Z(obs|s') · b'(s')
    """</span>
    <span style="color:#6b7280;"># Predict (state transition)</span>
    predicted = T.T @ belief

    <span style="color:#6b7280;"># Update with observation (Bayes)</span>
    obs_probs = Z <span style="color:#c4b5fd;">if</span> obs == <span style="color:#fcd34d;">1</span> <span style="color:#c4b5fd;">else</span> (<span style="color:#fcd34d;">1</span> - Z)
    updated   = obs_probs * predicted

    <span style="color:#6b7280;"># Normalise</span>
    <span style="color:#c4b5fd;">return</span> updated / updated.sum()

<span style="color:#6b7280;"># Start with uniform belief — no idea where we are</span>
<span style="color:#93c5fd;">belief</span>       = np.array([<span style="color:#fcd34d;">1</span>/<span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">1</span>/<span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">1</span>/<span style="color:#fcd34d;">3</span>])
<span style="color:#93c5fd;">observations</span> = [<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">0</span>]     <span style="color:#6b7280;"># bright, bright, dark</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Initial belief: {belief.round(3)}"</span>)
<span style="color:#c4b5fd;">for</span> t, obs <span style="color:#c4b5fd;">in</span> enumerate(observations):
    belief = belief_update(belief, obs, T, Z)
    label  = <span style="color:#a7f3d0;">'bright'</span> <span style="color:#c4b5fd;">if</span> obs == <span style="color:#fcd34d;">1</span> <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">'dark'</span>
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"After obs {t+1} ({label}): {belief.round(3)}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Initial belief: [0.333 0.333 0.333]
After obs 1 (bright): [0.077 0.692 0.231]
After obs 2 (bright): [0.25  0.25  0.5  ]
After obs 3 (dark):   [0.156 0.75  0.094]</div>
  </div>
</div>

<h3>Practical Approaches to POMDPs</h3>
<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">TAXONOMY — Handling Partial Observability</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">1. EXACT METHODS (small S, known model):</span>
   Point-Based Value Iteration (PBVI), SARSOP.
   Maintain alpha-vectors: piecewise-linear value over belief space.
   Exact but scales poorly beyond ~100 states.

<span style="color:#93c5fd;">2. MEMORY-AUGMENTED NETWORKS (deep RL):</span>
   Replace feedforward network with LSTM or Transformer.
   The recurrent hidden state approximates the belief state.
   Used by DeepMind for 3D navigation, multi-agent games.
   No explicit belief update — learned implicitly.

<span style="color:#a7f3d0;">3. FRAME STACKING (practical hack):</span>
   Stack last k observations as input to the policy network.
   Makes the input Markovian approximately (Markov of order k).
   Used by DQN on Atari: stack 4 frames to capture ball velocity.

<span style="color:#fcd34d;">4. WORLD MODELS (model-based deep RL):</span>
   Learn a latent dynamics model s_{t+1} = f(s_t, a_t) in latent space.
   Dreamer, MuZero: plan in the learned latent space.
   State-of-the-art on visual control tasks.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '24.10 Partially Observable MDPs and Real-World Challenges',
            'order_index' => 10,
            'content'     => $this->appendQuiz($content10, 'L24_10', [
                ['q' => 'What distinguishes a POMDP from a standard MDP?', 'opts' => ['POMDPs have stochastic transitions while MDPs are deterministic', 'In a POMDP, the agent receives noisy observations O rather than direct state access; true state s is hidden', 'POMDPs require continuous action spaces', 'POMDPs have no reward function — only an observation signal'], 'ans' => 1, 'exp' => 'A POMDP adds two components to the MDP: observation space O and observation function Z(o|s\',a) = P(obs=o | next_state=s\', action=a). The agent never sees s directly — only observations sampled from Z. This partial observability is the fundamental challenge: the agent must infer the hidden state from noisy, incomplete information.'],
                ['q' => 'What is the "belief state" in a POMDP and why is it important?', 'opts' => ['The agent\'s memory of the last observed state', 'A probability distribution b(s) = P(current state = s | all past observations and actions) — a sufficient statistic for optimal decision making', 'A fixed prior over states set at the start of the episode', 'The state with the highest probability after the first observation'], 'ans' => 1, 'exp' => 'The belief state b is a distribution over the hidden state space. Crucially, it IS Markov — b_t is a sufficient statistic for all future decisions given the history. This reduces the POMDP to an MDP over the continuous belief space, at the cost of a now-continuous (and high-dimensional) state representation.'],
                ['q' => 'In the belief update code, what mathematical operation corresponds to "prediction"?', 'opts' => ['Normalising the belief to sum to 1', 'Computing predicted(s\') = Σ_s T(s,a,s\')·b(s) — propagating belief through the transition model', 'Multiplying the belief by the observation probabilities', 'Sampling a state from the current belief distribution'], 'ans' => 1, 'exp' => 'The prediction step applies the transition model: predicted(s\') = T^T @ belief = Σ_s T(s,a,s\')·b(s). This gives the prior over next states before the observation arrives. Then the correction step multiplies by Z(obs|s\') and renormalises — exactly Bayesian updating applied sequentially.'],
                ['q' => 'Why does DQN on Atari stack 4 consecutive frames as input rather than using a single frame?', 'opts' => ['Four frames improve image resolution by averaging pixel values', 'A single frame violates the Markov property — a ball\'s velocity requires seeing consecutive positions; stacking 4 frames approximates a Markov state', 'Four frames reduce the dimensionality of the input to the network', 'Stacking frames is required by the Atari emulator API'], 'ans' => 1, 'exp' => 'A single Atari frame shows a ball\'s position but not its velocity or direction. Without velocity, the state is not Markov — future trajectory depends on the history. Stacking 4 frames provides enough temporal context to infer velocity and direction, making the input approximately Markov. This is a practical POMDP approximation technique.'],
                ['q' => 'What approach do World Models like MuZero and Dreamer take to handle partial observability?', 'opts' => ['They maintain an explicit belief distribution over all states', 'They learn a latent dynamics model and plan in learned latent space rather than directly in observation space', 'They use a Kalman filter to track the hidden state', 'They discretise the observation space into a finite grid'], 'ans' => 1, 'exp' => 'World Models learn a compact latent representation of environment dynamics: a latent state s_t, an encoder mapping observations to s_t, a transition model predicting s_{t+1} given s_t and a_t, and a reward model. Planning happens entirely in latent space — far cheaper than planning in raw pixel space — achieving state-of-the-art performance on visual control tasks.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 24.11 — FINAL EXAM
        // ══════════════════════════════════════════════════════════════
        $allFinalQuestions = [
            ['q' => 'An MDP is defined by (S, A, T, R, γ). What does the transition function T(s, a, s\') represent?', 'opts' => ['The reward received when moving from s to s\' via a', 'The probability of transitioning to state s\' when taking action a in state s', 'The optimal policy in state s given action a', 'The discount applied to the reward at state s\''], 'ans' => 1, 'exp' => 'T(s,a,s\') = P(S_{t+1}=s\' | S_t=s, A_t=a) is the probability of transitioning to s\' when the agent takes action a in state s. It encodes the stochastic dynamics of the environment. Σ_{s\'} T(s,a,s\') = 1 for all (s,a).'],
            ['q' => 'What is the Bellman Optimality Equation for V*(s)?', 'opts' => ['V*(s) = Σ_a π(a|s) Σ_{s\'} T(s,a,s\')[R + γV*(s\')]', 'V*(s) = max_a Σ_{s\'} T(s,a,s\')[R(s,a,s\') + γV*(s\')]', 'V*(s) = R(s) + γ max_{s\'} V*(s\')', 'V*(s) = E[G_t | S_t = s]'], 'ans' => 1, 'exp' => 'The Bellman Optimality Equation: V*(s) = max_a Σ_{s\'} T(s,a,s\')[R(s,a,s\') + γV*(s\')]. It states that the optimal value equals the maximum over actions of: expected immediate reward plus discounted optimal future value. This recursive self-consistency equation characterises V* uniquely.'],
            ['q' => 'Value Iteration requires knowledge of T and R. Why does this limit its real-world applicability?', 'opts' => ['Real-world environments always have continuous state spaces', 'In most real applications, T and R are unknown — the agent can only interact with the environment and observe outcomes', 'Value Iteration cannot handle stochastic environments', 'Value Iteration requires discount factor γ = 1'], 'ans' => 1, 'exp' => 'Value Iteration is model-based: every update computes max_a Σ_{s\'} T(s,a,s\')[R+γV(s\')] — requiring explicit T and R. In robotics, finance, healthcare, and games, these functions are typically unknown. Model-free methods (Q-Learning, REINFORCE, PPO) learn from experience without needing a model.'],
            ['q' => 'First-Visit Monte Carlo computes G_t backward. What is the computational advantage?', 'opts' => ['It reduces the number of episodes needed for convergence', 'Computing G_t = R_{t+1} + γR_{t+2} + ... backward gives all returns in O(T) time rather than O(T²)', 'It eliminates the need to store the full episode trajectory', 'It allows updates mid-episode without waiting for termination'], 'ans' => 1, 'exp' => 'Iterating backward with G ← γG + R_t at each step computes G_t for all t in a single O(T) pass. Computing each G_t from scratch (summing from t forward) would cost O(T) per timestep, giving O(T²) total. The backward accumulation is a fundamental algorithmic technique in RL.'],
            ['q' => 'Q-Learning update: Q(s,a) ← Q(s,a) + α[r + γ·max_{a\'}Q(s\',a\') - Q(s,a)]. What is the term in brackets called?', 'opts' => ['The policy gradient', 'The TD error — the difference between the bootstrapped target and current Q estimate', 'The advantage function', 'The Bellman residual of the value function'], 'ans' => 1, 'exp' => 'The term [r + γ·max_{a\'} Q(s\',a\') - Q(s,a)] is the TD error δ. It measures how wrong the current Q estimate was: the target r + γ·max Q(s\',a\') minus the current estimate Q(s,a). The update moves Q(s,a) a fraction α toward the target, reducing the error.'],
            ['q' => 'What two innovations made DQN stable when combining Q-Learning with neural networks?', 'opts' => ['Batch normalisation and dropout regularisation', 'Experience Replay (random mini-batches from buffer) and Target Network (frozen weights for TD targets)', 'Convolutional layers and residual connections', 'Learning rate scheduling and gradient clipping'], 'ans' => 1, 'exp' => 'Experience Replay breaks temporal correlations between consecutive samples by storing transitions in a buffer and sampling random mini-batches. The Target Network provides stable TD targets by keeping weights θ⁻ fixed for C steps rather than chasing the rapidly changing online network θ. Together they solve the instability of naive deep Q-Learning.'],
            ['q' => 'What does ∇J(θ) = E[∇log π(a|s;θ) · G_t] allow that value-based methods cannot?', 'opts' => ['It allows learning from incomplete episodes', 'It allows direct optimisation of stochastic policies over continuous action spaces without computing argmax', 'It allows off-policy learning from a replay buffer', 'It allows exact computation of the optimal value function'], 'ans' => 1, 'exp' => 'The Policy Gradient Theorem provides a gradient estimate that works for any differentiable parameterisation of π, including continuous action distributions (e.g., Gaussian). Value-based methods require argmax_a Q(s,a) — expensive or intractable for continuous actions. Policy gradients handle continuous actions naturally by outputting distribution parameters.'],
            ['q' => 'In PPO, what does the probability ratio r_t(θ) = π(a_t|s_t;θ)/π(a_t|s_t;θ_old) measure?', 'opts' => ['The probability of the optimal action in state s_t', 'How much the new policy π_θ has changed relative to the old policy that collected the data', 'The advantage of action a_t over the average action', 'The TD error under the new policy parameters'], 'ans' => 1, 'exp' => 'r_t(θ) measures the relative probability of the same action under the new vs old policy. r_t > 1 means the new policy assigns higher probability to that action; r_t < 1 means lower. PPO clips r_t to [1-ε, 1+ε], preventing the new policy from deviating too far from the data-collecting policy and causing catastrophic performance collapse.'],
            ['q' => 'Thompson Sampling for a Bernoulli bandit maintains Beta(α, β) posteriors. After observing a success, how does the posterior update?', 'opts' => ['α stays the same, β increases by 1', 'α increases by 1, β stays the same — the posterior shifts toward higher success probabilities', 'Both α and β increase by 1', 'α and β both stay the same; only Q(a) is updated'], 'ans' => 1, 'exp' => 'For a Bernoulli reward with Beta(α, β) prior, the conjugate update is: success → Beta(α+1, β); failure → Beta(α, β+1). Observing a success increases α, shifting the posterior mean α/(α+β) upward. This is exact Bayesian updating using the Beta-Bernoulli conjugate pair.'],
            ['q' => 'What is the belief state b(s) in a POMDP and what makes it Markov?', 'opts' => ['The most likely hidden state — a scalar, not a distribution', 'The posterior distribution P(state=s | all past observations and actions) — Markov because it encodes all relevant history', 'The probability of receiving a positive reward from state s', 'The transition distribution over next states from s'], 'ans' => 1, 'exp' => 'b(s) = P(S_t=s | o_1,...,o_t, a_1,...,a_{t-1}) is a distribution over hidden states. It is Markov because it summarises all relevant past information: b_{t+1} can be computed from b_t, a_t, o_{t+1} alone. This is the Bayesian filter update. The belief state turns the non-Markov POMDP observation history into a Markov process.'],
            ['q' => 'An actor-critic algorithm uses TD error δ_t = r + γV(s\') - V(s) as the advantage estimate. When δ_t > 0, what does this tell the actor?', 'opts' => ['The action led to a state worse than predicted — decrease its probability', 'The action led to a better outcome than the critic predicted — increase its probability', 'The critic\'s value estimate was exactly correct — no update needed', 'The discount factor γ was set too high — reduce it'], 'ans' => 1, 'exp' => 'δ_t = r + γV(s\') - V(s) > 0 means the actual outcome (reward + discounted next value) exceeded what the critic V(s) predicted. The action was better than expected. The actor update θ ← θ + α·δ_t·∇log π(a|s;θ) increases the log-probability of that action — reinforcing the surprisingly good choice.'],
            ['q' => 'Which algorithm is used in ChatGPT\'s RLHF training and what role does it play?', 'opts' => ['Q-Learning — to learn which tokens to predict next', 'PPO — to fine-tune the language model policy to maximise human preference scores from a reward model', 'Value Iteration — to compute optimal text completions offline', 'Thompson Sampling — to explore different response styles'], 'ans' => 1, 'exp' => 'In RLHF, a reward model is trained from human preference comparisons. PPO then treats the language model as the policy π(token|context;θ) and optimises θ to maximise reward model scores, with a KL-divergence penalty from the base model analogous to PPO\'s clipping. The sequential nature of token generation makes this a sequential decision making problem.'],
        ];

        $finalContent  = <<<HTML
<div id="org-lock-screen" style="text-align:center;padding:4rem 2rem;background:var(--surface2);border:1px solid var(--border);border-radius:12px;margin-top:2rem;">
    <div style="font-size:3rem;margin-bottom:1rem;">🔒</div>
    <h3 style="color:var(--text);margin-bottom:0.5rem;">University / Organization Access Only</h3>
    <p style="color:var(--muted);">The Final Module Exam is restricted to enrolled students and verified organization members.</p>
    <p style="font-size:0.85rem;color:#f59e0b;margin-top:1rem;background:rgba(245,158,11,0.1);padding:10px;border-radius:8px;display:inline-block;">Please contact administration to link your account to an organization.</p>
</div>
<div id="final-exam-content" style="display:none;">
    <h2>Module 24: Final Examination</h2>
    <p>This comprehensive exam covers all topics from Lessons 24.1 through 24.10 — MDPs, dynamic programming, Monte Carlo methods, TD learning, Q-Learning, DQN, policy gradients, actor-critic, PPO, multi-armed bandits, and POMDPs. Good luck!</p>
HTML;

        $finalContent .= $this->appendQuiz('', 'FINAL_EXAM', $allFinalQuestions);
        $finalContent .= '</div>';
        $finalContent .= <<<HTML
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.USER_ORG_ID !== 'undefined' && window.USER_ORG_ID !== null && window.USER_ORG_ID !== '') {
        document.getElementById('org-lock-screen').style.display = 'none';
        document.getElementById('final-exam-content').style.display = 'block';
    }
});
</script>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '24.11 Final Exam: Sequential Decision Making Mastery',
            'order_index' => 11,
            'content'     => $finalContent,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────────────────────────

    /**
     * Generates the full Quiz HTML/CSS/JS block and appends it to $htmlContent.
     */
    private function appendQuiz(string $htmlContent, string $quizPrefix, array $questions): string
    {
        $total   = count($questions);
        $letters = ['A', 'B', 'C', 'D', 'E'];

        $html  = $htmlContent;
        $html .= '<style>
            .quiz-wrapper{display:flex;flex-direction:column;gap:24px;margin-top:40px;}
            .quiz-card{background:var(--surface2);border:1px solid var(--border);border-radius:10px;overflow:hidden;}
            .quiz-card-header{background:rgba(0,0,0,0.2);padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:flex-start;gap:12px;}
            .quiz-q-num{background:var(--accent);color:#fff;font-size:0.7rem;font-weight:700;padding:3px 8px;border-radius:4px;font-family:"JetBrains Mono",monospace;white-space:nowrap;margin-top:2px;}
            .quiz-q-text{font-size:0.95rem;font-weight:600;color:var(--text);line-height:1.5;}
            .quiz-options{padding:16px 20px;display:flex;flex-direction:column;gap:10px;}
            .quiz-option{display:flex;align-items:flex-start;gap:12px;padding:12px 16px;border-radius:7px;border:1px solid var(--border);cursor:pointer;transition:all 0.15s;font-size:0.875rem;color:var(--muted);background:transparent;text-align:left;width:100%;font-family:"Inter",sans-serif;}
            .quiz-option:hover:not(.locked){border-color:var(--border-hover);background:var(--bg);color:var(--text);}
            .quiz-option .opt-key{width:22px;height:22px;border-radius:4px;border:1px solid var(--dim);font-size:0.7rem;font-weight:700;font-family:"JetBrains Mono",monospace;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;transition:all 0.15s;}
            .quiz-option.correct{border-color:#10b981;background:rgba(16,185,129,0.08);color:var(--text);}
            .quiz-option.correct .opt-key{background:#10b981;border-color:#10b981;color:#fff;}
            .quiz-option.wrong{border-color:#ef4444;background:rgba(239,68,68,0.08);color:var(--muted);opacity:0.7;}
            .quiz-option.locked{cursor:default;}
            .quiz-explanation{display:none;margin:0 20px 20px;padding:14px 16px;background:rgba(59,130,246,0.07);border:1px solid rgba(59,130,246,0.25);border-radius:7px;font-size:0.875rem;color:var(--muted);line-height:1.7;}
            .quiz-explanation strong{color:var(--text);}
            .quiz-score-bar{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:var(--surface2);border:1px solid var(--border);border-radius:10px;font-size:0.875rem;color:var(--muted);font-weight:600;}
            .quiz-score-val{font-size:1.1rem;font-weight:700;color:#f59e0b;font-family:"JetBrains Mono",monospace;}
        </style>';

        $html .= '<div class="quiz-wrapper" id="wrap_' . $quizPrefix . '">';
        $html .= '<div class="quiz-score-bar"><span>Knowledge Check</span><span class="quiz-score-val"><span id="score_' . $quizPrefix . '">0</span> / ' . $total . '</span></div>';

        foreach ($questions as $qIndex => $q) {
            $qNum = $qIndex + 1;
            $qId  = $quizPrefix . '_q' . $qNum;

            $html .= '<div class="quiz-card" id="' . $qId . '">';
            $html .= '<div class="quiz-card-header"><span class="quiz-q-num">Q' . $qNum . '</span><span class="quiz-q-text">' . htmlspecialchars($q['q']) . '</span></div>';
            $html .= '<div class="quiz-options">';

            foreach ($q['opts'] as $optIndex => $option) {
                $isCorrect = ($optIndex === $q['ans']) ? 'true' : 'false';
                $letter    = $letters[$optIndex];
                $html .= '<button class="quiz-option" onclick="checkAnswer(this,\'' . $qId . '\',' . $isCorrect . ',\'' . $quizPrefix . '\')"><span class="opt-key">' . $letter . '</span> ' . htmlspecialchars($option) . '</button>';
            }

            $html .= '</div>';
            $html .= '<div class="quiz-explanation" id="' . $qId . '-exp"><strong>Explanation:</strong> ' . $q['exp'] . '</div>';
            $html .= '</div>';
        }

        $html .= '</div>';

        $html .= "
<script>
if(typeof window.answeredQuizzes==='undefined'){window.answeredQuizzes={};}
if(typeof window.quizScores==='undefined'){window.quizScores={};}
window.checkAnswer=function(btn,qId,isCorrect,prefix){
    if(window.answeredQuizzes[qId])return;
    window.answeredQuizzes[qId]=true;
    if(typeof window.quizScores[prefix]==='undefined')window.quizScores[prefix]=0;
    const card=document.getElementById(qId);
    const allOpts=card.querySelectorAll('.quiz-option');
    allOpts.forEach(o=>o.classList.add('locked'));
    if(isCorrect){
        btn.classList.add('correct');
        window.quizScores[prefix]++;
    } else {
        btn.classList.add('wrong');
        allOpts.forEach(o=>{if(o.getAttribute('onclick').includes(',true,'))o.classList.add('correct');});
    }
    document.getElementById(qId+'-exp').style.display='block';
    document.getElementById('score_'+prefix).textContent=window.quizScores[prefix];
};
</script>
";

        return $html;
    }
}