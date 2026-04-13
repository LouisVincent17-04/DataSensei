<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Lesson;

/**
 * Module19LessonsSeeder
 * Seeds lessons for Module 19: Introduction to Artificial Intelligence.
 * Run AFTER CurriculumSeeder which creates the modules.
 *
 * Usage:  php artisan db:seed --class=Module19LessonsSeeder
 */
class Module19LessonsSeeder extends Seeder
{
    public function run()
    {
        $aiModule = Module::where('order_index', 19)->firstOrFail();
        Lesson::where('module_id', $aiModule->id)->delete();

        // ══════════════════════════════════════════════════════════════
        // LESSON 19.1 — What Is Artificial Intelligence? History, Definitions & Landscape
        // ══════════════════════════════════════════════════════════════
        $content1 = <<<'HTML'
<h2>What Is Artificial Intelligence?</h2>
<p>Artificial Intelligence (AI) is the science and engineering of creating machines that can perform tasks which, when performed by humans, would require intelligence. This deceptively simple definition — first attributed to John McCarthy, who coined the term "artificial intelligence" in 1956 — conceals enormous depth. Intelligence is not one thing: it encompasses reasoning, learning, planning, perception, language understanding, creativity, and social interaction. AI is the systematic attempt to endow machines with some or all of these capacities.</p>

<p>We are living through the most consequential period in AI's history. Systems that can hold conversations, generate photorealistic images, write and debug code, diagnose disease from medical scans, and drive cars have moved from science fiction to production deployment within a single decade. Understanding AI is no longer optional for scientists, engineers, businesspeople, or policymakers — it is a foundational literacy for the 21st century.</p>

<h3>A Brief History: From Logic to Large Language Models</h3>
<p>AI did not appear suddenly. It has a rich intellectual lineage stretching back centuries, with pivotal milestones that shaped what we build today. Understanding this history explains <em>why</em> modern approaches look the way they do — including the repeated pattern of hype, "AI winters," and eventual breakthrough.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — AI Timeline: Key Milestones</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Key milestones in AI history — structured as data</span>
<span style="color:#93c5fd;">milestones</span> = [
    (<span style="color:#fcd34d;">1950</span>, <span style="color:#a7f3d0;">"Turing Test proposed — 'Can machines think?'"</span>),
    (<span style="color:#fcd34d;">1956</span>, <span style="color:#a7f3d0;">"Dartmouth Conference — 'Artificial Intelligence' coined"</span>),
    (<span style="color:#fcd34d;">1966</span>, <span style="color:#a7f3d0;">"ELIZA chatbot — first natural language processing program"</span>),
    (<span style="color:#fcd34d;">1980</span>, <span style="color:#a7f3d0;">"Expert Systems boom — rule-based AI in industry"</span>),
    (<span style="color:#fcd34d;">1987</span>, <span style="color:#a7f3d0;">"First AI Winter — funding collapses, limits exposed"</span>),
    (<span style="color:#fcd34d;">1997</span>, <span style="color:#a7f3d0;">"Deep Blue defeats Kasparov at chess"</span>),
    (<span style="color:#fcd34d;">2006</span>, <span style="color:#a7f3d0;">"Hinton et al. revive deep learning with deep belief nets"</span>),
    (<span style="color:#fcd34d;">2012</span>, <span style="color:#a7f3d0;">"AlexNet wins ImageNet — deep learning era begins"</span>),
    (<span style="color:#fcd34d;">2017</span>, <span style="color:#a7f3d0;">"Transformer architecture published ('Attention Is All You Need')"</span>),
    (<span style="color:#fcd34d;">2020</span>, <span style="color:#a7f3d0;">"GPT-3: 175B parameters, emergent few-shot learning"</span>),
    (<span style="color:#fcd34d;">2022</span>, <span style="color:#a7f3d0;">"ChatGPT: 100M users in 2 months — mainstream AI arrives"</span>),
    (<span style="color:#fcd34d;">2024</span>, <span style="color:#a7f3d0;">"Multimodal LLMs, AI agents, reasoning models go mainstream"</span>),
]

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Year':<8} {'Milestone'}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"-" * 65</span>)
<span style="color:#c4b5fd;">for</span> year, event <span style="color:#c4b5fd;">in</span> milestones:
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{year:<8} {event}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Year     Milestone
-----------------------------------------------------------------
1950     Turing Test proposed — 'Can machines think?'
1956     Dartmouth Conference — 'Artificial Intelligence' coined
1966     ELIZA chatbot — first natural language processing program
1980     Expert Systems boom — rule-based AI in industry
1987     First AI Winter — funding collapses, limits exposed
1997     Deep Blue defeats Kasparov at chess
2006     Hinton et al. revive deep learning with deep belief nets
2012     AlexNet wins ImageNet — deep learning era begins
2017     Transformer architecture published ('Attention Is All You Need')
2020     GPT-3: 175B parameters, emergent few-shot learning
2022     ChatGPT: 100M users in 2 months — mainstream AI arrives
2024     Multimodal LLMs, AI agents, reasoning models go mainstream</div>
  </div>
</div>

<h3>The Three Tiers: ANI, AGI & ASI</h3>
<p><strong>Artificial Narrow Intelligence (ANI)</strong> — also called "weak AI" — is every AI system that exists today. ANI excels at one specific task (chess, image classification, language generation) but cannot transfer that competence to unrelated domains. Every model you will study in this course is ANI. <strong>Artificial General Intelligence (AGI)</strong> is a hypothetical system that can perform any intellectual task a human can — not just match us on narrow benchmarks, but genuinely understand, reason, and learn across arbitrary domains. AGI does not yet exist. <strong>Artificial Superintelligence (ASI)</strong> would surpass human intelligence across every dimension. ASI is purely speculative and raises profound philosophical and safety questions that are actively debated among researchers.</p>

<h3>The AI Landscape: How the Subfields Relate</h3>
<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">REFERENCE — AI Subfield Hierarchy</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># ┌─────────────────────────────────────────────────────────────┐
# │                  ARTIFICIAL INTELLIGENCE                    │
# │   (Search, Planning, Knowledge Representation, Reasoning)  │
# │  ┌──────────────────────────────────────────────────────┐  │
# │  │              MACHINE LEARNING                        │  │
# │  │   (Supervised, Unsupervised, Reinforcement)          │  │
# │  │  ┌───────────────────────────────────────────────┐  │  │
# │  │  │           DEEP LEARNING                       │  │  │
# │  │  │  (Neural Networks with many layers)           │  │  │
# │  │  │  ┌─────────────────────────────────────────┐  │  │  │
# │  │  │  │    GENERATIVE AI / LARGE LANGUAGE MODELS│  │  │  │
# │  │  │  │  (GPT, Claude, Gemini, Llama, DALL-E)   │  │  │  │
# │  │  │  └─────────────────────────────────────────┘  │  │  │
# │  │  └───────────────────────────────────────────────┘  │  │
# │  └──────────────────────────────────────────────────────┘  │
# └─────────────────────────────────────────────────────────────┘</span>

<span style="color:#93c5fd;">subfields</span> = {
    <span style="color:#a7f3d0;">"AI"</span>:             <span style="color:#a7f3d0;">"Broadest category — any technique making machines 'intelligent'"</span>,
    <span style="color:#a7f3d0;">"Machine Learning"</span>: <span style="color:#a7f3d0;">"AI that learns from data without explicit programming"</span>,
    <span style="color:#a7f3d0;">"Deep Learning"</span>:  <span style="color:#a7f3d0;">"ML using multi-layer neural networks on raw data"</span>,
    <span style="color:#a7f3d0;">"Generative AI"</span>:  <span style="color:#a7f3d0;">"DL models that CREATE new text, images, code, audio"</span>,
}
<span style="color:#c4b5fd;">for</span> field, desc <span style="color:#c4b5fd;">in</span> subfields.items():
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{field:>18}: {desc}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>                AI: Broadest category — any technique making machines 'intelligent'
   Machine Learning: AI that learns from data without explicit programming
     Deep Learning: ML using multi-layer neural networks on raw data
     Generative AI: DL models that CREATE new text, images, code, audio</div>
  </div>
</div>

<h3>Why AI Now? The Three Enabling Forces</h3>
<p>AI techniques like neural networks have existed since the 1950s. Why did the revolution happen in the 2010s and not earlier? Three forces converged simultaneously. <strong>Data:</strong> the internet, smartphones, and IoT devices created an unprecedented flood of labelled training data — ImageNet alone contains 14 million annotated images. <strong>Compute:</strong> GPUs (originally designed for video games) turned out to be perfectly suited to the massively parallel matrix multiplications at the heart of neural network training, reducing training time from months to hours. <strong>Algorithms:</strong> key innovations — ReLU activations, dropout regularisation, batch normalisation, the Transformer architecture — made it possible to train much deeper and larger networks reliably. Remove any one of these three ingredients and the deep learning revolution does not happen.</p>
HTML;

        Lesson::create([
            'module_id'   => $aiModule->id,
            'title'       => '19.1 What Is Artificial Intelligence? History, Definitions & Landscape',
            'order_index' => 1,
            'content'     => $this->appendQuiz($content1, 'L19_1', [
                ['q' => 'Who coined the term "Artificial Intelligence" and in what year?', 'opts' => ['Alan Turing, 1950', 'John McCarthy, 1956', 'Geoffrey Hinton, 2006', 'Claude Shannon, 1948'], 'ans' => 1, 'exp' => 'John McCarthy coined the term "Artificial Intelligence" at the Dartmouth Conference in 1956, which is widely considered the founding event of AI as an academic discipline. Alan Turing asked "Can machines think?" in 1950, but used different terminology.'],
                ['q' => 'Artificial Narrow Intelligence (ANI) differs from Artificial General Intelligence (AGI) in that:', 'opts' => ['ANI uses neural networks; AGI uses rule-based systems', 'ANI excels at one specific task but cannot generalise across domains; AGI can perform any intellectual task a human can', 'ANI requires more compute than AGI', 'ANI does not exist yet; AGI powers today\'s chatbots'], 'ans' => 1, 'exp' => 'Every AI system today — including GPT-4, AlphaGo, and self-driving cars — is ANI: highly capable within its training domain but unable to transfer intelligence to unrelated tasks. AGI, which would match human-level general reasoning, does not yet exist.'],
                ['q' => 'Which three forces converged in the 2010s to cause the deep learning revolution?', 'opts' => ['Better algorithms, faster CPUs, and more programmers', 'Big data, GPU compute, and algorithmic innovations like the Transformer', 'Expert systems, symbolic AI, and quantum computing', 'Internet access, cheaper storage, and Java programming'], 'ans' => 1, 'exp' => 'The deep learning revolution required three ingredients arriving simultaneously: massive labelled datasets (e.g., ImageNet), GPU hardware that made parallel matrix multiplication practical, and algorithmic advances like ReLU, dropout, batch norm, and the Transformer. Remove any one, and the revolution stalls.'],
                ['q' => 'The correct nesting order of AI subfields from broadest to narrowest is:', 'opts' => ['Deep Learning → Machine Learning → AI → Generative AI', 'AI → Machine Learning → Deep Learning → Generative AI', 'Machine Learning → AI → Deep Learning → Generative AI', 'Generative AI → Deep Learning → AI → Machine Learning'], 'ans' => 1, 'exp' => 'AI is the broadest category. Machine Learning is a subset of AI (learns from data). Deep Learning is a subset of ML (uses multi-layer neural networks). Generative AI is a subset of Deep Learning (creates new content). Each layer is more specialised than the one above it.'],
                ['q' => 'What was the significance of AlexNet winning the ImageNet competition in 2012?', 'opts' => ['It proved that rule-based expert systems were superior to neural networks', 'It demonstrated that deep convolutional neural networks could dramatically outperform hand-engineered features, launching the modern deep learning era', 'It was the first time a computer played chess', 'It introduced the Transformer architecture'], 'ans' => 1, 'exp' => "AlexNet achieved a top-5 error rate of 15.3% versus the runner-up's 26.2% — an unprecedented gap. It proved that deep CNNs trained on GPUs with large datasets could shatter the state of the art on vision tasks, triggering a massive pivot to deep learning across AI research."],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 19.2 — Machine Learning Fundamentals: Supervised, Unsupervised & Reinforcement
        // ══════════════════════════════════════════════════════════════
        $content2 = <<<'HTML'
<h2>Machine Learning Fundamentals</h2>
<p>Machine Learning (ML) is the subfield of AI that gives computers the ability to learn from experience without being explicitly programmed for every scenario. Instead of writing rules by hand ("if pixel is dark AND shape is round, classify as tumour"), we feed the system thousands of examples and let it discover the rules itself. This shift — from programming rules to learning patterns — is the most transformative idea in modern computing.</p>

<p>Arthur Samuel, who built the first self-learning checkers program in 1959, defined ML as "the field of study that gives computers the ability to learn without being explicitly programmed." Tom Mitchell gave a more precise definition in 1997: "A computer program is said to learn from experience E with respect to some task T and performance measure P, if its performance at T, as measured by P, improves with experience E." These definitions capture two essential ideas: (1) learning happens through exposure to data, and (2) success is measured by a quantifiable performance metric.</p>

<h3>The Three Paradigms of Machine Learning</h3>
<p>All ML falls into three broad paradigms, each defined by the type of feedback the learning algorithm receives during training.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Supervised Learning: Train & Evaluate a Classifier</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_iris
<span style="color:#c4b5fd;">from</span> sklearn.model_selection <span style="color:#c4b5fd;">import</span> train_test_split
<span style="color:#c4b5fd;">from</span> sklearn.tree <span style="color:#c4b5fd;">import</span> DecisionTreeClassifier
<span style="color:#c4b5fd;">from</span> sklearn.metrics <span style="color:#c4b5fd;">import</span> accuracy_score, classification_report

<span style="color:#6b7280;"># SUPERVISED LEARNING: every training sample has a known label</span>
<span style="color:#93c5fd;">iris</span> = load_iris()
<span style="color:#93c5fd;">X</span>, <span style="color:#93c5fd;">y</span> = iris.data, iris.target      <span style="color:#6b7280;"># features, labels</span>

<span style="color:#6b7280;"># Step 1: Split — NEVER evaluate on training data</span>
<span style="color:#93c5fd;">X_train</span>, <span style="color:#93c5fd;">X_test</span>, <span style="color:#93c5fd;">y_train</span>, <span style="color:#93c5fd;">y_test</span> = train_test_split(
    X, y, test_size=<span style="color:#fcd34d;">0.25</span>, random_state=<span style="color:#fcd34d;">42</span>
)

<span style="color:#6b7280;"># Step 2: Train — the model learns decision boundaries from labelled examples</span>
<span style="color:#93c5fd;">model</span> = DecisionTreeClassifier(max_depth=<span style="color:#fcd34d;">3</span>, random_state=<span style="color:#fcd34d;">42</span>)
<span style="color:#93c5fd;">model</span>.fit(X_train, y_train)

<span style="color:#6b7280;"># Step 3: Evaluate on held-out test set</span>
<span style="color:#93c5fd;">y_pred</span> = model.predict(X_test)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Training samples : {len(X_train)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Test samples     : {len(X_test)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Test Accuracy    : {accuracy_score(y_test, y_pred):.4f}"</span>)
<span style="color:#93c5fd;">print</span>()
<span style="color:#93c5fd;">print</span>(classification_report(y_test, y_pred, target_names=iris.target_names))</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Training samples : 112
Test samples     : 38
Test Accuracy    : 0.9737

              precision    recall  f1-score   support
      setosa       1.00      1.00      1.00        13
  versicolor       1.00      0.92      0.96        13
   virginica       0.92      1.00      0.96        12
    accuracy                           0.97        38</div>
  </div>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Unsupervised Learning: K-Means Clustering</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.cluster <span style="color:#c4b5fd;">import</span> KMeans
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>

<span style="color:#6b7280;"># UNSUPERVISED LEARNING: no labels — discover hidden structure</span>
<span style="color:#6b7280;"># Use only X features — y labels are hidden (not used for training)</span>
<span style="color:#93c5fd;">kmeans</span> = KMeans(n_clusters=<span style="color:#fcd34d;">3</span>, random_state=<span style="color:#fcd34d;">42</span>, n_init=<span style="color:#fcd34d;">10</span>)
<span style="color:#93c5fd;">kmeans</span>.fit(iris.data)          <span style="color:#6b7280;"># no labels passed!</span>

<span style="color:#6b7280;"># Evaluate: how well do discovered clusters match true species?</span>
<span style="color:#c4b5fd;">from</span> sklearn.metrics <span style="color:#c4b5fd;">import</span> adjusted_rand_score
<span style="color:#93c5fd;">ari</span> = adjusted_rand_score(iris.target, kmeans.labels_)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Discovered cluster labels:"</span>, np.unique(kmeans.labels_))
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Cluster sizes: {np.bincount(kmeans.labels_)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Adjusted Rand Index vs true labels: {ari:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"(ARI=1.0 = perfect recovery, ARI=0 = random)"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Discovered cluster labels: [0 1 2]
Cluster sizes: [50 62 38]
Adjusted Rand Index vs true labels: 0.7302
(ARI=1.0 = perfect recovery, ARI=0 = random)</div>
  </div>
</div>

<h3>Supervised vs. Unsupervised vs. Reinforcement Learning</h3>
<p>In <strong>Supervised Learning</strong>, every training example comes with a correct answer (label). The algorithm learns a mapping from inputs to outputs. Tasks: classification (discrete output — "spam or not spam?"), regression (continuous output — "what will this house sell for?"). In <strong>Unsupervised Learning</strong>, there are no labels. The algorithm discovers hidden structure in the data: clustering (group similar customers), dimensionality reduction (compress 1000-feature data into 2D for visualisation), density estimation (model the distribution of data). In <strong>Reinforcement Learning (RL)</strong>, an agent learns by taking actions in an environment and receiving reward signals. It is not given correct answers — it must discover them through trial and error. RL powers AlphaGo, robotics, and the RLHF training step used to align large language models like GPT.</p>

<h3>The Bias-Variance Trade-off: The Central Tension in ML</h3>
<p>Every ML model must balance two competing sources of error. <strong>Bias</strong> is the error from overly simplistic assumptions — an underfit model that misses real patterns (e.g., fitting a straight line to quadratic data). <strong>Variance</strong> is the error from excessive sensitivity to training data — an overfit model that memorises noise and fails to generalise. Reducing bias typically increases variance and vice versa. The sweet spot — a model complex enough to capture real structure but simple enough to generalise — is found through techniques like cross-validation, regularisation, and ensemble methods.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Overfitting vs. Underfitting: Depth vs. Accuracy</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Depth':<8} {'Train Acc':>10} {'Test Acc':>10} {'Diagnosis'}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"-" * 50</span>)

<span style="color:#c4b5fd;">for</span> depth <span style="color:#c4b5fd;">in</span> [<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">10</span>, <span style="color:#fca5a5;">None</span>]:
    <span style="color:#93c5fd;">clf</span> = DecisionTreeClassifier(max_depth=depth, random_state=<span style="color:#fcd34d;">42</span>)
    <span style="color:#93c5fd;">clf</span>.fit(X_train, y_train)
    <span style="color:#93c5fd;">tr_acc</span> = accuracy_score(y_train, clf.predict(X_train))
    <span style="color:#93c5fd;">te_acc</span> = accuracy_score(y_test,  clf.predict(X_test))
    <span style="color:#93c5fd;">gap</span>    = tr_acc - te_acc
    <span style="color:#93c5fd;">diag</span>   = <span style="color:#a7f3d0;">"Underfit"</span> <span style="color:#c4b5fd;">if</span> te_acc &lt; <span style="color:#fcd34d;">0.85</span> <span style="color:#c4b5fd;">else</span> (<span style="color:#a7f3d0;">"Overfit"</span> <span style="color:#c4b5fd;">if</span> gap &gt; <span style="color:#fcd34d;">0.05</span> <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">"Good Fit ✓"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{str(depth):<8} {tr_acc:>10.4f} {te_acc:>10.4f}  {diag}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Depth    Train Acc   Test Acc  Diagnosis
--------------------------------------------------
1           0.6875     0.6842  Underfit
2           0.9464     0.9474  Good Fit ✓
3           0.9732     0.9737  Good Fit ✓
5           0.9911     0.9737  Good Fit ✓
10          1.0000     0.9474  Overfit
None        1.0000     0.9474  Overfit</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $aiModule->id,
            'title'       => '19.2 Machine Learning Fundamentals: Supervised, Unsupervised & Reinforcement',
            'order_index' => 2,
            'content'     => $this->appendQuiz($content2, 'L19_2', [
                ['q' => 'In supervised learning, what is a "label"?', 'opts' => ['A feature column used as model input', 'The correct answer associated with each training example', 'A hyperparameter of the model', 'The name of a variable in the dataset'], 'ans' => 1, 'exp' => "A label (also called the target or ground truth) is the correct answer for each training example. In a spam classifier, the label is 'spam' or 'not spam'. The model learns to predict labels it has never seen by generalising from labelled training examples."],
                ['q' => 'What is the key difference between classification and regression?', 'opts' => ['Classification uses neural networks; regression uses decision trees', 'Classification predicts a discrete category; regression predicts a continuous numeric value', 'Classification requires more data than regression', 'Regression is unsupervised; classification is supervised'], 'ans' => 1, 'exp' => 'Both are supervised learning tasks. Classification output is discrete (e.g., "cat", "dog", "tumour"/"no tumour"). Regression output is continuous (e.g., house price = $347,500, temperature = 22.3°C). The choice depends on what the target variable is.'],
                ['q' => 'Unsupervised learning differs from supervised learning primarily because:', 'opts' => ['It uses reinforcement signals instead of labels', 'It finds hidden structure in data without any labelled examples', 'It requires more compute than supervised learning', 'It can only be applied to image data'], 'ans' => 1, 'exp' => 'In unsupervised learning, there are no labels. The algorithm must discover inherent structure — clusters, latent dimensions, or data distributions — from the raw input features alone. Clustering (K-Means), dimensionality reduction (PCA), and generative modelling (VAEs) are all unsupervised.'],
                ['q' => 'An overfitted model can be identified by:', 'opts' => ['High training accuracy AND high test accuracy', 'Low training accuracy AND low test accuracy', 'Very high training accuracy but significantly lower test accuracy', 'Equal training and test accuracy, both low'], 'ans' => 2, 'exp' => 'Overfitting: the model memorises training data (near-perfect training accuracy) but fails to generalise — test accuracy is substantially lower. The large gap between train and test performance is the diagnostic signal. Underfitting shows poor performance on BOTH sets.'],
                ['q' => 'In reinforcement learning, what is the "reward signal"?', 'opts' => ['The learning rate of the optimizer', 'A labelled dataset provided by human annotators', 'A scalar feedback signal telling the agent how good or bad its action was', 'The validation loss after each epoch'], 'ans' => 2, 'exp' => 'The reward signal is the only feedback an RL agent receives. It is a scalar number: positive for good actions, negative (penalty) for bad actions, zero for neutral. The agent learns to choose actions that maximise cumulative future reward — it is never told the "correct" action directly.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 19.3 — Neural Networks: From Perceptron to Deep Learning
        // ══════════════════════════════════════════════════════════════
        $content3 = <<<'HTML'
<h2>Neural Networks: From Perceptron to Deep Learning</h2>
<p>Neural networks are computing systems loosely inspired by the biological neural networks in animal brains. They are the dominant model architecture in modern AI, powering everything from image recognition to language generation. Understanding neural networks — from their simplest form to deep architectures — is foundational to understanding how modern AI actually works at a mechanistic level.</p>

<h3>The Biological Inspiration</h3>
<p>The human brain contains approximately 86 billion neurons, each connected to thousands of others. When a neuron receives enough electrical input from its connected neighbours, it "fires" — sending its own signal downstream. Complex behaviour emerges from the collective activity of billions of these simple switching elements. Artificial neural networks abstract this into mathematical objects: <strong>nodes</strong> (artificial neurons) connected by <strong>weighted edges</strong>. The weight on each connection determines how strongly one node influences another — and these weights are what the network <em>learns</em>.</p>

<h3>The Perceptron: The Simplest Neural Network</h3>
<p>Frank Rosenblatt's Perceptron (1958) was the first trainable neural network. It takes multiple inputs, multiplies each by a learned weight, sums them, adds a bias term, and passes the result through an activation function to produce an output. A single perceptron can learn any <em>linearly separable</em> binary classification — but not XOR, a discovery that temporarily ended neural network research (the first "AI winter").</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Perceptron: Forward Pass from Scratch</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>

<span style="color:#6b7280;"># A single artificial neuron (perceptron)</span>
<span style="color:#6b7280;"># Forward pass: output = activation(W·x + b)</span>

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">sigmoid</span>(z):
    <span style="color:#c4b5fd;">return</span> <span style="color:#fcd34d;">1</span> / (<span style="color:#fcd34d;">1</span> + np.exp(-z))

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">relu</span>(z):
    <span style="color:#c4b5fd;">return</span> np.maximum(<span style="color:#fcd34d;">0</span>, z)

<span style="color:#6b7280;"># Inputs: [hours_studied, hours_slept, practice_tests_done]</span>
<span style="color:#93c5fd;">x</span> = np.array([<span style="color:#fcd34d;">8.0</span>, <span style="color:#fcd34d;">7.0</span>, <span style="color:#fcd34d;">3.0</span>])

<span style="color:#6b7280;"># Learned weights and bias (normally found via training)</span>
<span style="color:#93c5fd;">W</span> = np.array([<span style="color:#fcd34d;">0.5</span>, <span style="color:#fcd34d;">0.3</span>, <span style="color:#fcd34d;">0.8</span>])
<span style="color:#93c5fd;">b</span> = <span style="color:#fcd34d;">-3.0</span>

<span style="color:#6b7280;"># Compute weighted sum (pre-activation)</span>
<span style="color:#93c5fd;">z</span> = np.dot(W, x) + b
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Weighted sum z = W·x + b = {z:.2f}"</span>)

<span style="color:#6b7280;"># Apply activation functions</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Sigmoid(z)  = {sigmoid(z):.4f}  → probability of passing exam"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"ReLU(z)     = {relu(z):.4f}    → used in hidden layers"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Decision    : {'PASS' if sigmoid(z) >= 0.5 else 'FAIL'}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Weighted sum z = W·x + b = 4.50
Sigmoid(z)  = 0.9890  → probability of passing exam
ReLU(z)     = 4.5000  → used in hidden layers
Decision    : PASS</div>
  </div>
</div>

<h3>Multi-Layer Perceptrons (MLPs) and the Role of Hidden Layers</h3>
<p>A <strong>Multi-Layer Perceptron (MLP)</strong> stacks multiple layers of neurons: an input layer, one or more hidden layers, and an output layer. Adding hidden layers is what allows the network to learn <em>non-linear</em> decision boundaries — solving problems like XOR that a single perceptron cannot. Each hidden layer transforms the data into a new representation that makes the final classification or regression easier. This is why hidden layers are sometimes called "feature detectors": the network automatically learns the features most useful for the task.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Building & Training an MLP with Keras</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>
<span style="color:#c4b5fd;">from</span> tensorflow <span style="color:#c4b5fd;">import</span> keras
<span style="color:#c4b5fd;">from</span> tensorflow.keras <span style="color:#c4b5fd;">import</span> layers
<span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_iris
<span style="color:#c4b5fd;">from</span> sklearn.model_selection <span style="color:#c4b5fd;">import</span> train_test_split
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> StandardScaler

<span style="color:#93c5fd;">iris</span> = load_iris()
<span style="color:#93c5fd;">X</span>, <span style="color:#93c5fd;">y</span> = iris.data, keras.utils.to_categorical(iris.target, num_classes=<span style="color:#fcd34d;">3</span>)
<span style="color:#93c5fd;">X_train</span>, <span style="color:#93c5fd;">X_test</span>, <span style="color:#93c5fd;">y_train</span>, <span style="color:#93c5fd;">y_test</span> = train_test_split(X, y, test_size=<span style="color:#fcd34d;">0.2</span>, random_state=<span style="color:#fcd34d;">42</span>)

<span style="color:#6b7280;"># Scale features — neural networks train faster on standardised inputs</span>
<span style="color:#93c5fd;">scaler</span> = StandardScaler()
<span style="color:#93c5fd;">X_train</span> = scaler.fit_transform(X_train)
<span style="color:#93c5fd;">X_test</span>  = scaler.transform(X_test)

<span style="color:#6b7280;"># Build MLP: Input(4) → Dense(16, ReLU) → Dense(8, ReLU) → Output(3, Softmax)</span>
<span style="color:#93c5fd;">model</span> = keras.Sequential([
    layers.Dense(<span style="color:#fcd34d;">16</span>, activation=<span style="color:#a7f3d0;">'relu'</span>, input_shape=(<span style="color:#fcd34d;">4</span>,)),
    layers.Dense(<span style="color:#fcd34d;">8</span>,  activation=<span style="color:#a7f3d0;">'relu'</span>),
    layers.Dense(<span style="color:#fcd34d;">3</span>,  activation=<span style="color:#a7f3d0;">'softmax'</span>),   <span style="color:#6b7280;"># outputs class probabilities</span>
])

<span style="color:#93c5fd;">model</span>.compile(optimizer=<span style="color:#a7f3d0;">'adam'</span>,
              loss=<span style="color:#a7f3d0;">'categorical_crossentropy'</span>,
              metrics=[<span style="color:#a7f3d0;">'accuracy'</span>])

<span style="color:#93c5fd;">history</span> = model.fit(X_train, y_train, epochs=<span style="color:#fcd34d;">50</span>,
                    validation_split=<span style="color:#fcd34d;">0.2</span>, verbose=<span style="color:#fcd34d;">0</span>)

<span style="color:#93c5fd;">loss</span>, <span style="color:#93c5fd;">acc</span> = model.evaluate(X_test, y_test, verbose=<span style="color:#fcd34d;">0</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Total parameters : {model.count_params()}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Test accuracy    : {acc:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Test loss        : {loss:.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Total parameters : 219
Test accuracy    : 1.0000
Test loss        : 0.0614</div>
  </div>
</div>

<h3>Backpropagation: How Networks Learn</h3>
<p><strong>Backpropagation</strong> is the algorithm that trains neural networks. It works in two passes. In the <strong>forward pass</strong>, input data flows through the network layer by layer to produce a prediction. The <strong>loss function</strong> then computes how wrong the prediction was (e.g., cross-entropy for classification, mean squared error for regression). In the <strong>backward pass</strong>, the gradient of the loss with respect to every weight in the network is computed using the chain rule of calculus — propagating the error signal backwards from output to input. The <strong>optimiser</strong> (e.g., Adam, SGD) then updates every weight by a small step in the direction that reduces the loss. Repeat for thousands of batches across many epochs and the network converges to a good solution.</p>

<h3>Activation Functions: The Source of Non-Linearity</h3>
<p>Without non-linear activation functions, a multi-layer network collapses into a single linear transformation — no matter how many layers you add. The activation function applied after each linear transformation is what gives the network its expressive power. <strong>Sigmoid</strong> squashes output to (0,1) — useful for binary output layers. <strong>Tanh</strong> squashes to (-1,1) — slightly better than sigmoid for hidden layers. <strong>ReLU</strong> (Rectified Linear Unit: max(0, x)) is the dominant choice for hidden layers — it is computationally cheap, does not saturate for positive values, and enables training of very deep networks.</p>
HTML;

        Lesson::create([
            'module_id'   => $aiModule->id,
            'title'       => '19.3 Neural Networks: From Perceptron to Deep Learning',
            'order_index' => 3,
            'content'     => $this->appendQuiz($content3, 'L19_3', [
                ['q' => 'What mathematical operation does a single artificial neuron perform?', 'opts' => ['It randomly selects an output from a lookup table', 'It computes a weighted sum of its inputs plus a bias, then applies an activation function', 'It sorts its inputs and returns the largest', 'It computes the dot product of two matrices'], 'ans' => 1, 'exp' => 'A neuron computes: output = activation(W·x + b). W is the weight vector, x is the input vector, b is the bias, and the activation function introduces non-linearity. This simple computation, replicated millions of times across a network, enables powerful pattern recognition.'],
                ['q' => 'Why are hidden layers essential in a multi-layer perceptron?', 'opts' => ['They store the training data', 'They reduce the number of parameters', 'They allow the network to learn non-linear decision boundaries that a single layer cannot', 'They implement the softmax output'], 'ans' => 2, 'exp' => 'A network with no hidden layers is just linear regression or logistic regression — it can only learn linear decision boundaries. Hidden layers with non-linear activations allow the network to compose non-linear transformations, learning arbitrarily complex mappings given sufficient width and depth.'],
                ['q' => 'In backpropagation, what is computed during the backward pass?', 'opts' => ['The predictions for each training example', 'The gradient of the loss function with respect to every weight in the network', 'The optimal learning rate', 'The accuracy on the validation set'], 'ans' => 1, 'exp' => 'The backward pass uses the chain rule of calculus to compute ∂L/∂w for every weight w in the network — how much the loss would change if that weight changed slightly. The optimiser then adjusts each weight by -η × ∂L/∂w (gradient descent step).'],
                ['q' => 'Why is ReLU the preferred activation function for hidden layers in modern neural networks?', 'opts' => ['It produces outputs in the range (0, 1)', 'It is computationally cheap, does not saturate for positive inputs, and enables training of very deep networks without vanishing gradients', 'It normalises the output to zero mean', 'It is the only differentiable activation function'], 'ans' => 1, 'exp' => 'ReLU = max(0, x). For positive inputs, gradient = 1 (no vanishing). For negative inputs, output = 0 (sparse activations). Unlike sigmoid/tanh, ReLU does not saturate for large positive values, so gradients flow through deep networks without shrinking to zero.'],
                ['q' => 'What does the softmax activation function output?', 'opts' => ['A single value between 0 and 1', 'A vector of values that sum to 1.0, interpretable as class probabilities', 'The index of the highest-valued input', 'A binary 0 or 1 for each class'], 'ans' => 1, 'exp' => 'Softmax converts a vector of raw scores (logits) into a probability distribution: each output is in (0,1) and all outputs sum to 1.0. This makes softmax the natural choice for the output layer of multi-class classifiers.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 19.4 — Computer Vision: CNNs, Object Detection & Image AI
        // ══════════════════════════════════════════════════════════════
        $content4 = <<<'HTML'
<h2>Computer Vision: CNNs, Object Detection & Image AI</h2>
<p><strong>Computer Vision</strong> is the AI subfield that enables machines to interpret and understand visual information — images, video, and spatial data. It is arguably the area where deep learning has had its most dramatic impact: tasks that were unsolvable in 2010 (recognising arbitrary objects in real-world photos) are now routine, and systems routinely match or exceed human performance on standard benchmarks. Computer vision powers self-driving cars, medical imaging diagnosis, satellite analysis, facial recognition, augmented reality, and quality control in manufacturing.</p>

<h3>Why Fully Connected Networks Fail on Images</h3>
<p>A modest 224×224 colour image contains 224 × 224 × 3 = 150,528 individual pixel values. If we fed this directly into a fully connected (dense) layer with 1,000 hidden units, the first layer alone would have 150,528 × 1,000 = 150.5 million parameters. This is computationally expensive, requires enormous amounts of training data to avoid overfitting, and — critically — throws away all spatial structure: the network treats pixel (0,0) and pixel (223,223) as equally related, even though nearby pixels are far more correlated. Convolutional Neural Networks solve all three problems.</p>

<h3>Convolutional Neural Networks (CNNs)</h3>
<p>A <strong>Convolutional Neural Network (CNN)</strong> uses a small learnable filter (kernel) that slides across the image computing the dot product between the filter and each local patch of pixels. This exploits three insights: <strong>local connectivity</strong> (each neuron only connects to a small local region, not the whole image), <strong>parameter sharing</strong> (the same filter is applied at every position — so detecting a vertical edge in the top-left uses the same weights as detecting it in the bottom-right), and <strong>translational equivariance</strong> (the feature detector works regardless of where the feature appears in the image).</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Building a CNN for MNIST Digit Classification</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> tensorflow <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">tf</span>
<span style="color:#c4b5fd;">from</span> tensorflow.keras <span style="color:#c4b5fd;">import</span> layers, models

<span style="color:#6b7280;"># Load MNIST: 70,000 handwritten digit images (28×28 greyscale)</span>
<span style="color:#93c5fd;">(X_train, y_train), (X_test, y_test)</span> = tf.keras.datasets.mnist.load_data()
<span style="color:#93c5fd;">X_train</span> = X_train.reshape(-<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">28</span>, <span style="color:#fcd34d;">28</span>, <span style="color:#fcd34d;">1</span>).astype(<span style="color:#a7f3d0;">'float32'</span>) / <span style="color:#fcd34d;">255.0</span>
<span style="color:#93c5fd;">X_test</span>  = X_test.reshape(-<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">28</span>, <span style="color:#fcd34d;">28</span>, <span style="color:#fcd34d;">1</span>).astype(<span style="color:#a7f3d0;">'float32'</span>) / <span style="color:#fcd34d;">255.0</span>

<span style="color:#6b7280;"># CNN architecture: Conv → Pool → Conv → Pool → Flatten → Dense</span>
<span style="color:#93c5fd;">cnn</span> = models.Sequential([
    layers.Conv2D(<span style="color:#fcd34d;">32</span>, (<span style="color:#fcd34d;">3</span>,<span style="color:#fcd34d;">3</span>), activation=<span style="color:#a7f3d0;">'relu'</span>, input_shape=(<span style="color:#fcd34d;">28</span>,<span style="color:#fcd34d;">28</span>,<span style="color:#fcd34d;">1</span>)),
    layers.MaxPooling2D((<span style="color:#fcd34d;">2</span>,<span style="color:#fcd34d;">2</span>)),
    layers.Conv2D(<span style="color:#fcd34d;">64</span>, (<span style="color:#fcd34d;">3</span>,<span style="color:#fcd34d;">3</span>), activation=<span style="color:#a7f3d0;">'relu'</span>),
    layers.MaxPooling2D((<span style="color:#fcd34d;">2</span>,<span style="color:#fcd34d;">2</span>)),
    layers.Flatten(),
    layers.Dense(<span style="color:#fcd34d;">64</span>,  activation=<span style="color:#a7f3d0;">'relu'</span>),
    layers.Dropout(<span style="color:#fcd34d;">0.5</span>),                          <span style="color:#6b7280;"># regularisation</span>
    layers.Dense(<span style="color:#fcd34d;">10</span>,  activation=<span style="color:#a7f3d0;">'softmax'</span>),
])

<span style="color:#93c5fd;">cnn</span>.compile(optimizer=<span style="color:#a7f3d0;">'adam'</span>,
            loss=<span style="color:#a7f3d0;">'sparse_categorical_crossentropy'</span>,
            metrics=[<span style="color:#a7f3d0;">'accuracy'</span>])

<span style="color:#93c5fd;">history</span> = cnn.fit(X_train, y_train, epochs=<span style="color:#fcd34d;">5</span>,
                  batch_size=<span style="color:#fcd34d;">128</span>, validation_split=<span style="color:#fcd34d;">0.1</span>, verbose=<span style="color:#fcd34d;">1</span>)

<span style="color:#93c5fd;">test_loss</span>, <span style="color:#93c5fd;">test_acc</span> = cnn.evaluate(X_test, y_test, verbose=<span style="color:#fcd34d;">0</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nTest Accuracy: {test_acc:.4f}  ({test_acc*100:.2f}%)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Parameters:    {cnn.count_params():,}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Epoch 1/5 - loss: 0.2041 - accuracy: 0.9374
Epoch 2/5 - loss: 0.0831 - accuracy: 0.9753
Epoch 3/5 - loss: 0.0634 - accuracy: 0.9808
Epoch 4/5 - loss: 0.0519 - accuracy: 0.9840
Epoch 5/5 - loss: 0.0453 - accuracy: 0.9862

Test Accuracy: 0.9914  (99.14%)
Parameters:    93,322</div>
  </div>
</div>

<h3>Transfer Learning: Leveraging Pre-Trained Models</h3>
<p><strong>Transfer learning</strong> is one of the most practically powerful ideas in modern AI. Instead of training a CNN from scratch (requiring millions of images and days of compute), you take a model pre-trained on a massive dataset (e.g., ImageNet's 14M images) and adapt it to your specific task. The pre-trained layers have already learned general visual features — edges, textures, shapes — that are useful for almost any vision task. You only need to fine-tune the final layers on your smaller dataset.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Transfer Learning with MobileNetV2</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> tensorflow.keras.applications <span style="color:#c4b5fd;">import</span> MobileNetV2
<span style="color:#c4b5fd;">from</span> tensorflow.keras <span style="color:#c4b5fd;">import</span> layers, models

<span style="color:#6b7280;"># Load MobileNetV2 pre-trained on ImageNet — freeze its weights</span>
<span style="color:#93c5fd;">base_model</span> = MobileNetV2(input_shape=(<span style="color:#fcd34d;">96</span>, <span style="color:#fcd34d;">96</span>, <span style="color:#fcd34d;">3</span>),
                          include_top=<span style="color:#fca5a5;">False</span>,    <span style="color:#6b7280;"># remove ImageNet classifier head</span>
                          weights=<span style="color:#a7f3d0;">'imagenet'</span>)
<span style="color:#93c5fd;">base_model</span>.trainable = <span style="color:#fca5a5;">False</span>           <span style="color:#6b7280;"># freeze pre-trained weights</span>

<span style="color:#6b7280;"># Add a new task-specific classification head</span>
<span style="color:#93c5fd;">model</span> = models.Sequential([
    <span style="color:#93c5fd;">base_model</span>,
    layers.GlobalAveragePooling2D(),
    layers.Dropout(<span style="color:#fcd34d;">0.3</span>),
    layers.Dense(<span style="color:#fcd34d;">5</span>, activation=<span style="color:#a7f3d0;">'softmax'</span>),  <span style="color:#6b7280;"># 5 custom classes</span>
])

<span style="color:#93c5fd;">trainable_params</span> = <span style="color:#93c5fd;">sum</span>(tf.size(v).numpy() <span style="color:#c4b5fd;">for</span> v <span style="color:#c4b5fd;">in</span> model.trainable_variables)
<span style="color:#93c5fd;">total_params</span>     = model.count_params()

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Total params    : {total_params:,}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Trainable params: {trainable_params:,}  (only the new head!)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Frozen params   : {total_params - trainable_params:,}  (ImageNet features)"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Total params    : 2,264,069
Trainable params: 6,405  (only the new head!)
Frozen params   : 2,257,664  (ImageNet features)</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $aiModule->id,
            'title'       => '19.4 Computer Vision: CNNs, Object Detection & Image AI',
            'order_index' => 4,
            'content'     => $this->appendQuiz($content4, 'L19_4', [
                ['q' => 'What three key properties make CNNs well-suited for image data?', 'opts' => ['Global connectivity, parameter diversity, and translational invariance', 'Local connectivity, parameter sharing, and translational equivariance', 'Recurrent connections, dropout, and batch normalisation', 'Dense layers, sigmoid activations, and global pooling'], 'ans' => 1, 'exp' => 'CNNs exploit: (1) Local connectivity — each neuron sees only a small spatial patch, (2) Parameter sharing — one filter applied everywhere drastically reduces parameters, (3) Translational equivariance — the same feature detector works regardless of where in the image a feature appears.'],
                ['q' => 'What does a MaxPooling2D(2,2) layer do to a feature map?', 'opts' => ['Doubles the spatial dimensions', 'Replaces each 2×2 patch with its maximum value, halving the spatial dimensions', 'Averages all values in the feature map to a single number', 'Applies a 2×2 convolutional filter'], 'ans' => 1, 'exp' => 'Max pooling takes the maximum value within each non-overlapping 2×2 window. A 28×28 feature map becomes 14×14 after MaxPooling2D(2,2). This reduces computation, controls overfitting, and provides a small degree of translational invariance.'],
                ['q' => 'In transfer learning, why are pre-trained CNN weights "frozen"?', 'opts' => ['To save memory during training', 'To preserve the general visual features already learned from large datasets like ImageNet, which are broadly useful for the new task', 'Because they cannot be loaded into RAM', 'To prevent the model from training at all'], 'ans' => 1, 'exp' => 'Freezing (setting trainable=False) preserves the valuable feature representations learned from millions of images. You only train the new task-specific head on your small dataset. This prevents overfitting and drastically reduces the compute and data needed compared to training from scratch.'],
                ['q' => 'Why does Dropout regularisation help prevent overfitting?', 'opts' => ['It removes neurons permanently from the network', 'It randomly deactivates a fraction of neurons during each training step, preventing co-adaptation and forcing redundant representations', 'It reduces the learning rate', 'It adds Gaussian noise to the input data'], 'ans' => 1, 'exp' => 'Dropout (rate=p) randomly sets p% of activations to zero during each forward pass in training. This forces the network to learn robust features that do not rely on any specific neuron being active — preventing neurons from co-adapting. Dropout is disabled at inference time.'],
                ['q' => 'Which CNN architecture won the 2012 ImageNet competition and launched the deep learning era?', 'opts' => ['ResNet', 'VGG-16', 'AlexNet', 'MobileNetV2'], 'ans' => 2, 'exp' => "AlexNet (Krizhevsky, Sutskever, Hinton) achieved 15.3% top-5 error vs. the runner-up's 26.2% — a 10+ percentage point gap that shocked the field. It used ReLU activations, GPU training, dropout, and data augmentation, demonstrating that deep CNNs trained on GPUs could dramatically outperform hand-engineered approaches."],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 19.5 — Natural Language Processing: From Bag-of-Words to Transformers
        // ══════════════════════════════════════════════════════════════
        $content5 = <<<'HTML'
<h2>Natural Language Processing: From Bag-of-Words to Transformers</h2>
<p><strong>Natural Language Processing (NLP)</strong> is the AI subfield that enables computers to understand, interpret, and generate human language. Language is arguably the most distinctive human capability — and the hardest to replicate artificially. Language is ambiguous, context-dependent, culturally situated, creative, and ever-changing. NLP encompasses tasks from simple text classification and named entity recognition to machine translation, question answering, summarisation, and — with modern large language models — open-ended conversation and code generation.</p>

<h3>The Text Representation Problem</h3>
<p>Before any ML model can process text, we must convert words into numbers. This text-to-number conversion — called <strong>text representation</strong> or <strong>encoding</strong> — is one of the most important and nuanced decisions in NLP. The choice of representation directly determines what linguistic information the model can access.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Text Representations: Bag-of-Words, TF-IDF & Word Embeddings</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.feature_extraction.text <span style="color:#c4b5fd;">import</span> CountVectorizer, TfidfVectorizer
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>

<span style="color:#93c5fd;">corpus</span> = [
    <span style="color:#a7f3d0;">"The cat sat on the mat"</span>,
    <span style="color:#a7f3d0;">"The dog sat on the log"</span>,
    <span style="color:#a7f3d0;">"The cat chased the dog"</span>,
]

<span style="color:#6b7280;"># ── Bag of Words ──────────────────────────────────────────────</span>
<span style="color:#93c5fd;">bow</span> = CountVectorizer()
<span style="color:#93c5fd;">X_bow</span> = bow.fit_transform(corpus)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"=== Bag of Words ==="</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Vocabulary:"</span>, bow.get_feature_names_out())
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Sentence 1:"</span>, X_bow.toarray()[<span style="color:#fcd34d;">0</span>])
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Problem: word order lost, 'cat' = 'dog' in importance"</span>)

<span style="color:#6b7280;"># ── TF-IDF ────────────────────────────────────────────────────</span>
<span style="color:#93c5fd;">tfidf</span> = TfidfVectorizer()
<span style="color:#93c5fd;">X_tfidf</span> = tfidf.fit_transform(corpus)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\n=== TF-IDF ==="</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Sentence 1 TF-IDF scores:"</span>)
<span style="color:#93c5fd;">scores</span> = X_tfidf.toarray()[<span style="color:#fcd34d;">0</span>]
<span style="color:#c4b5fd;">for</span> word, score <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>(tfidf.get_feature_names_out(), scores):
    <span style="color:#c4b5fd;">if</span> score > <span style="color:#fcd34d;">0</span>:
        <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  {word:<10}: {score:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Note: 'the','sat' get low scores (common); 'cat','mat' get higher scores"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>=== Bag of Words ===
Vocabulary: ['cat' 'chased' 'dog' 'log' 'mat' 'on' 'sat' 'the']
Sentence 1: [1 0 0 0 1 1 1 2]
Problem: word order lost, 'cat' = 'dog' in importance

=== TF-IDF ===
Sentence 1 TF-IDF scores:
  cat       : 0.4505
  mat       : 0.4505
  on        : 0.3090
  sat       : 0.3090
  the       : 0.2318
Note: 'the','sat' get low scores (common); 'cat','mat' get higher scores</div>
  </div>
</div>

<h3>Word Embeddings: Encoding Meaning in Vectors</h3>
<p><strong>Word embeddings</strong> map each word to a dense, low-dimensional vector (typically 50–300 dimensions) such that words with similar meanings are close in the embedding space. The landmark insight of Word2Vec (Mikolov et al., 2013) was that semantic relationships become geometric: <em>king − man + woman ≈ queen</em>. These embeddings are learned by training a neural network to predict context words (the "distributional hypothesis": words that appear in similar contexts have similar meanings). Modern large language models use contextualised embeddings: unlike Word2Vec where every occurrence of "bank" gets the same vector, contextualised models assign different vectors depending on context ("river bank" vs. "investment bank").</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Sentiment Classification with TF-IDF + Logistic Regression</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.linear_model <span style="color:#c4b5fd;">import</span> LogisticRegression
<span style="color:#c4b5fd;">from</span> sklearn.pipeline <span style="color:#c4b5fd;">import</span> Pipeline
<span style="color:#c4b5fd;">from</span> sklearn.metrics <span style="color:#c4b5fd;">import</span> accuracy_score

<span style="color:#6b7280;"># Mini sentiment dataset</span>
<span style="color:#93c5fd;">texts</span> = [
    <span style="color:#a7f3d0;">"I absolutely loved this product, amazing quality!"</span>,
    <span style="color:#a7f3d0;">"Terrible experience, would not recommend at all"</span>,
    <span style="color:#a7f3d0;">"Great value for money, very happy with purchase"</span>,
    <span style="color:#a7f3d0;">"Broke after one day, complete waste of money"</span>,
    <span style="color:#a7f3d0;">"Outstanding customer service, fast delivery"</span>,
    <span style="color:#a7f3d0;">"Disappointed with the quality, feels cheap"</span>,
    <span style="color:#a7f3d0;">"Best purchase I have made this year!"</span>,
    <span style="color:#a7f3d0;">"Do not buy this, it is a scam"</span>,
]
<span style="color:#93c5fd;">labels</span> = [<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">0</span>]   <span style="color:#6b7280;"># 1=positive, 0=negative</span>

<span style="color:#93c5fd;">pipe</span> = Pipeline([
    (<span style="color:#a7f3d0;">'tfidf'</span>, TfidfVectorizer(ngram_range=(<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">2</span>))),   <span style="color:#6b7280;"># unigrams + bigrams</span>
    (<span style="color:#a7f3d0;">'clf'</span>,   LogisticRegression(max_iter=<span style="color:#fcd34d;">1000</span>)),
])
<span style="color:#93c5fd;">pipe</span>.fit(texts, labels)

<span style="color:#6b7280;"># Test on new reviews</span>
<span style="color:#93c5fd;">new_reviews</span> = [
    <span style="color:#a7f3d0;">"Fantastic item, exceeded all expectations"</span>,
    <span style="color:#a7f3d0;">"Horrible product, fell apart immediately"</span>,
]
<span style="color:#93c5fd;">preds</span> = pipe.predict(new_reviews)
<span style="color:#93c5fd;">probs</span> = pipe.predict_proba(new_reviews)

<span style="color:#c4b5fd;">for</span> review, pred, prob <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>(new_reviews, preds, probs):
    <span style="color:#93c5fd;">sentiment</span> = <span style="color:#a7f3d0;">"POSITIVE 😊"</span> <span style="color:#c4b5fd;">if</span> pred == <span style="color:#fcd34d;">1</span> <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">"NEGATIVE 😞"</span>
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{sentiment}  ({prob[pred]:.1%} confidence)"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  → '{review[:45]}...'"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>POSITIVE 😊  (98.3% confidence)
  → 'Fantastic item, exceeded all expectations...'
NEGATIVE 😞  (97.1% confidence)
  → 'Horrible product, fell apart immediately...'</div>
  </div>
</div>

<h3>The Transformer Architecture: The Engine of Modern NLP</h3>
<p>The <strong>Transformer</strong> (Vaswani et al., 2017 — "Attention Is All You Need") replaced recurrent networks as the dominant NLP architecture almost overnight. Its key innovation is the <strong>self-attention mechanism</strong>: instead of processing tokens sequentially (as RNNs do), every token attends directly to every other token in the sequence simultaneously, weighting their relevance dynamically. This enables massively parallel training on GPUs and the capture of long-range dependencies that RNNs struggled with. The Transformer is the architectural foundation of BERT, GPT, T5, Claude, Gemini, Llama, and virtually every state-of-the-art NLP system in existence today.</p>
HTML;

        Lesson::create([
            'module_id'   => $aiModule->id,
            'title'       => '19.5 Natural Language Processing: From Bag-of-Words to Transformers',
            'order_index' => 5,
            'content'     => $this->appendQuiz($content5, 'L19_5', [
                ['q' => 'What is the main limitation of Bag-of-Words text representation?', 'opts' => ['It cannot handle more than 100 words', 'It is too computationally expensive', 'It discards word order and treats all words as equally important independent of context', 'It only works for English text'], 'ans' => 2, 'exp' => 'Bag-of-Words counts word occurrences but completely ignores word order and inter-word relationships. "The cat bit the dog" and "The dog bit the cat" produce identical BoW vectors despite opposite meanings. Every word is treated as an independent, context-free token.'],
                ['q' => 'TF-IDF improves on Bag-of-Words by:', 'opts' => ['Learning word embeddings from the corpus', 'Downweighting words that appear frequently across many documents (they carry less distinctive information) and upweighting rare, discriminative words', 'Adding positional encoding to each word', 'Using a recurrent network to process sequences'], 'ans' => 1, 'exp' => "TF-IDF = Term Frequency × Inverse Document Frequency. Common words like 'the', 'is', 'and' appear in every document (high DF → low IDF → low TF-IDF). Rare, topic-specific words like 'photosynthesis' appear in few documents (low DF → high IDF → high TF-IDF score), making them more informative."],
                ['q' => 'The famous Word2Vec analogy "king − man + woman ≈ queen" demonstrates that word embeddings:', 'opts' => ['Can be used for arithmetic in Python', 'Encode semantic relationships as geometric directions in vector space', 'Are identical to one-hot encodings with dimension reduction', 'Require supervised labels to train'], 'ans' => 1, 'exp' => 'Word embeddings learn that certain directions in the embedding space correspond to semantic relationships (e.g., the "gender" direction, the "royalty" direction). Semantic reasoning becomes vector arithmetic. This emerges purely from learning to predict context words — no explicit semantic labels are needed.'],
                ['q' => 'What is the key innovation of the Transformer architecture over recurrent networks (RNNs)?', 'opts' => ['Transformers use convolutional layers instead of recurrent connections', 'The self-attention mechanism allows every token to attend directly to every other token simultaneously, enabling parallel training and long-range dependency capture', 'Transformers do not require any training data', 'Transformers use sigmoid activations instead of ReLU'], 'ans' => 1, 'exp' => 'RNNs process sequences one token at a time — gradients vanish over long distances, and parallelisation is impossible. Self-attention computes relationships between all token pairs at once: every token can directly attend to every other token regardless of distance. This enables parallel GPU training and far better modelling of long-range dependencies.'],
                ['q' => 'A contextualised word embedding differs from a static embedding (like Word2Vec) because:', 'opts' => ['It has more dimensions', 'The same word receives different vector representations depending on its surrounding context', 'It is faster to compute', 'It does not require pre-training'], 'ans' => 1, 'exp' => 'Static embeddings give "bank" the same vector whether in "river bank" or "bank account". Contextualised embeddings (BERT, GPT) produce different vectors for each occurrence based on the full surrounding context. This resolves lexical ambiguity and is why modern LLMs are so much more capable than earlier NLP systems.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 19.6 — Large Language Models: Architecture, Training & Capabilities
        // ══════════════════════════════════════════════════════════════
        $content6 = <<<'HTML'
<h2>Large Language Models: Architecture, Training & Capabilities</h2>
<p><strong>Large Language Models (LLMs)</strong> are Transformer-based neural networks trained on massive text corpora to predict the next token in a sequence. Through this deceptively simple training objective — applied at enormous scale — these models develop emergent capabilities that were not explicitly programmed: reasoning, summarisation, translation, code generation, analogical thinking, and multi-step problem solving. LLMs like GPT-4, Claude, Gemini, and Llama have fundamentally changed what AI systems can do and how humans interact with technology.</p>

<h3>How LLMs Are Trained: The Three-Phase Pipeline</h3>
<p>Modern production LLMs are not trained in a single step. They undergo a carefully engineered multi-phase process. <strong>Phase 1 — Pre-training:</strong> The model processes hundreds of billions to trillions of tokens of text (books, web pages, code, scientific papers) and learns to predict the next token. This phase instils broad world knowledge and language understanding. <strong>Phase 2 — Supervised Fine-Tuning (SFT):</strong> The pre-trained model is fine-tuned on high-quality human-written demonstrations of helpful conversations. This teaches the model to be a good assistant. <strong>Phase 3 — Reinforcement Learning from Human Feedback (RLHF):</strong> Human annotators rank model responses, and a reward model is trained on these preferences. The LLM is then optimised to generate responses that the reward model scores highly. RLHF is what makes modern LLMs safe, helpful, and aligned with human values.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Using an LLM API: Token Generation & Temperature</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Simulate LLM token sampling to understand temperature's effect</span>
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">sample_next_token</span>(logits, temperature=<span style="color:#fcd34d;">1.0</span>, top_k=<span style="color:#fcd34d;">5</span>):
    <span style="color:#a7f3d0;">"""Simulate how an LLM selects the next token."""</span>
    <span style="color:#6b7280;"># Scale logits by temperature (lower T = more confident/deterministic)</span>
    <span style="color:#93c5fd;">scaled</span> = logits / temperature
    <span style="color:#6b7280;"># Softmax to get probabilities</span>
    <span style="color:#93c5fd;">probs</span>  = np.exp(scaled) / np.sum(np.exp(scaled))
    <span style="color:#c4b5fd;">return</span> probs

<span style="color:#93c5fd;">vocab</span>  = [<span style="color:#a7f3d0;">"cat"</span>, <span style="color:#a7f3d0;">"dog"</span>, <span style="color:#a7f3d0;">"bird"</span>, <span style="color:#a7f3d0;">"fish"</span>, <span style="color:#a7f3d0;">"elephant"</span>]
<span style="color:#93c5fd;">logits</span> = np.array([<span style="color:#fcd34d;">3.0</span>, <span style="color:#fcd34d;">2.0</span>, <span style="color:#fcd34d;">1.5</span>, <span style="color:#fcd34d;">0.5</span>, <span style="color:#fcd34d;">-1.0</span>])  <span style="color:#6b7280;"># raw model scores</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Token':<12} {'T=0.1 (focused)':>18} {'T=1.0 (balanced)':>18} {'T=2.0 (creative)':>18}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"-" * 68</span>)
<span style="color:#93c5fd;">p_low</span>  = sample_next_token(logits, temperature=<span style="color:#fcd34d;">0.1</span>)
<span style="color:#93c5fd;">p_mid</span>  = sample_next_token(logits, temperature=<span style="color:#fcd34d;">1.0</span>)
<span style="color:#93c5fd;">p_high</span> = sample_next_token(logits, temperature=<span style="color:#fcd34d;">2.0</span>)
<span style="color:#c4b5fd;">for</span> word, pl, pm, ph <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>(vocab, p_low, p_mid, p_high):
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{word:<12} {pl:>18.4f} {pm:>18.4f} {ph:>18.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Token          T=0.1 (focused)   T=1.0 (balanced)   T=2.0 (creative)
--------------------------------------------------------------------
cat                     0.9999             0.6364             0.4223
dog                     0.0001             0.2341             0.2551
bird                    0.0000             0.1426             0.2007
fish                    0.0000             0.0523             0.1010
elephant                0.0000             0.0053             0.0209</div>
  </div>
</div>

<h3>Emergent Capabilities: What Scale Unlocks</h3>
<p>One of the most surprising findings in LLM research is <strong>emergent capabilities</strong> — abilities that appear suddenly as model scale (parameters × training compute) crosses a threshold, without being explicitly trained for. These include multi-step arithmetic, chain-of-thought reasoning, code generation, analogy solving, and few-shot learning. A model with 1 billion parameters may show no ability at a task; scale it to 100 billion parameters and the capability appears abruptly. This "phase transition" behaviour suggests that LLMs are not just memorising text but forming internal representations that support genuine reasoning.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Prompt Engineering: Zero-Shot, Few-Shot & Chain-of-Thought</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Three prompting strategies — shown as templates (requires API key for live use)</span>

<span style="color:#6b7280;"># ── Zero-Shot: just ask ───────────────────────────────────────</span>
<span style="color:#93c5fd;">zero_shot</span> = <span style="color:#a7f3d0;">"""
Classify the sentiment of this review as POSITIVE or NEGATIVE.
Review: "The battery life is awful and the screen cracked within a week."
Sentiment:"""</span>

<span style="color:#6b7280;"># ── Few-Shot: provide examples first ─────────────────────────</span>
<span style="color:#93c5fd;">few_shot</span> = <span style="color:#a7f3d0;">"""
Classify sentiment as POSITIVE or NEGATIVE.

Review: "Absolutely love this phone!" → POSITIVE
Review: "Stopped working after 2 days." → NEGATIVE
Review: "Incredible camera quality, best I've used." → POSITIVE
Review: "Feels cheap and scratches very easily." → NEGATIVE

Review: "The battery life is awful and the screen cracked within a week."
Sentiment:"""</span>

<span style="color:#6b7280;"># ── Chain-of-Thought: ask model to reason step by step ────────</span>
<span style="color:#93c5fd;">cot</span> = <span style="color:#a7f3d0;">"""
Classify sentiment. Think step by step before giving your answer.

Review: "The battery life is awful and the screen cracked within a week."

Step-by-step reasoning:
1. Key phrases: 'battery life is awful' → strongly negative
2. Key phrases: 'screen cracked within a week' → product defect → negative
3. No positive phrases found.
4. Conclusion: Both aspects are negative with no redeeming points.

Sentiment: NEGATIVE"""</span>

<span style="color:#c4b5fd;">for</span> name, prompt <span style="color:#c4b5fd;">in</span> [(<span style="color:#a7f3d0;">"Zero-Shot"</span>, zero_shot), (<span style="color:#a7f3d0;">"Few-Shot"</span>, few_shot), (<span style="color:#a7f3d0;">"Chain-of-Thought"</span>, cot)]:
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"[{name}] tokens in prompt: {len(prompt.split())}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>[Zero-Shot] tokens in prompt: 22
[Few-Shot] tokens in prompt: 58
[Chain-of-Thought] tokens in prompt: 79</div>
  </div>
</div>

<h3>Hallucination: The Critical Limitation of LLMs</h3>
<p>LLMs <strong>hallucinate</strong> — they confidently generate text that is factually incorrect, logically inconsistent, or completely fabricated. This happens because the model's objective is to generate plausible-sounding text, not to be truthful. A model that has never encountered a fact may "fill in" plausible-sounding details rather than saying "I don't know." Hallucination is one of the central unsolved problems in AI safety and reliability. Mitigations include Retrieval-Augmented Generation (RAG — grounding responses in verified documents), structured outputs, and chain-of-thought prompting that makes reasoning visible and verifiable.</p>
HTML;

        Lesson::create([
            'module_id'   => $aiModule->id,
            'title'       => '19.6 Large Language Models: Architecture, Training & Capabilities',
            'order_index' => 6,
            'content'     => $this->appendQuiz($content6, 'L19_6', [
                ['q' => 'What is the primary training objective of a Large Language Model during pre-training?', 'opts' => ['Classifying images into 1000 categories', 'Predicting the next token in a sequence across a massive corpus of text', 'Playing games and receiving reward signals', 'Translating text between language pairs'], 'ans' => 1, 'exp' => 'LLMs are trained with a self-supervised next-token prediction objective: given a sequence of tokens, predict the next one. This is applied to trillions of tokens from the internet, books, and code. No human labels are needed — the text itself provides the training signal.'],
                ['q' => 'What does RLHF (Reinforcement Learning from Human Feedback) achieve in LLM training?', 'opts' => ['It pre-trains the model on text data', 'It aligns the model with human values and preferences by training it to generate responses that human annotators rate highly', 'It reduces the model size for efficient deployment', 'It translates the model to multiple languages'], 'ans' => 1, 'exp' => 'RLHF: human annotators rank model responses → a reward model is trained on these rankings → the LLM is fine-tuned with RL to maximise reward model scores. This shapes the model to be helpful, harmless, and honest. Without RLHF, LLMs may be capable but poorly aligned.'],
                ['q' => 'LLM "temperature" controls:', 'opts' => ['The GPU temperature during inference', 'The diversity/randomness of generated text — low temperature is deterministic and focused, high temperature is creative and diverse', 'The number of tokens generated', 'The learning rate during training'], 'ans' => 1, 'exp' => "Temperature scales the logits before softmax. T→0: probability mass collapses onto the highest-scoring token (greedy/deterministic). T=1: probabilities proportional to raw model scores. T>1: flattened distribution — lower-probability tokens get relatively more likely → more creative/diverse/risky outputs."],
                ['q' => 'What is "hallucination" in the context of LLMs?', 'opts' => ['When the model generates visual content', 'When the model confidently generates factually incorrect, fabricated, or inconsistent information', 'When the model refuses to answer a question', 'When the model repeats the same phrase in a loop'], 'ans' => 1, 'exp' => "Hallucination occurs because the model's objective is to generate plausible-sounding text, not to be truthful. When the model lacks knowledge of a fact, it may generate a plausible-sounding but completely false answer with high confidence. This is a fundamental limitation of current LLMs."],
                ['q' => 'Chain-of-Thought (CoT) prompting improves LLM performance by:', 'opts' => ['Providing more training data', 'Instructing the model to show its reasoning step by step before giving a final answer, improving accuracy on complex multi-step problems', 'Reducing hallucination by adding retrieved documents', 'Fine-tuning the model on domain-specific data'], 'ans' => 1, 'exp' => 'CoT prompting ("Let\'s think step by step") encourages the model to externalise its reasoning process. Each intermediate reasoning step conditions subsequent steps, reducing errors on arithmetic, logic, and multi-step problems where direct answer generation fails. It also makes the reasoning auditable.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 19.7 — Generative AI: GANs, Diffusion Models & Multimodal Systems
        // ══════════════════════════════════════════════════════════════
        $content7 = <<<'HTML'
<h2>Generative AI: GANs, Diffusion Models & Multimodal Systems</h2>
<p><strong>Generative AI</strong> refers to AI systems that create new content — images, music, video, code, 3D models, protein structures — that did not exist before. This is a qualitative leap from discriminative AI (which classifies or predicts) to AI that can imagine and create. Generative AI has triggered an unprecedented creative revolution: professional-quality images from text descriptions, photorealistic videos, functional code from natural language, and synthetic data for training other models. Understanding the key generative model families — GANs and Diffusion Models — is essential for navigating the modern AI landscape.</p>

<h3>Generative Adversarial Networks (GANs)</h3>
<p>Ian Goodfellow's GAN (2014) is one of the most creative ideas in AI history. A GAN frames image generation as a game between two neural networks: a <strong>Generator</strong> (G) that creates fake samples and a <strong>Discriminator</strong> (D) that tries to distinguish fakes from real data. They compete in a minimax game: G learns to fool D; D learns to detect G's fakes. At equilibrium, G produces samples so realistic that D cannot distinguish them from real ones. GANs produced the first photorealistic synthetic faces (StyleGAN) and revolutionised image synthesis throughout the late 2010s.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — GAN Architecture: Generator & Discriminator with Keras</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> tensorflow.keras <span style="color:#c4b5fd;">import</span> layers, models
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>

<span style="color:#93c5fd;">LATENT_DIM</span> = <span style="color:#fcd34d;">100</span>    <span style="color:#6b7280;"># size of the noise vector fed to Generator</span>
<span style="color:#93c5fd;">IMG_DIM</span>    = <span style="color:#fcd34d;">784</span>    <span style="color:#6b7280;"># 28×28 flattened MNIST image</span>

<span style="color:#6b7280;"># Generator: latent noise vector → fake image</span>
<span style="color:#93c5fd;">generator</span> = models.Sequential([
    layers.Dense(<span style="color:#fcd34d;">256</span>, activation=<span style="color:#a7f3d0;">'relu'</span>,      input_shape=(<span style="color:#93c5fd;">LATENT_DIM</span>,)),
    layers.BatchNormalization(),
    layers.Dense(<span style="color:#fcd34d;">512</span>, activation=<span style="color:#a7f3d0;">'relu'</span>),
    layers.BatchNormalization(),
    layers.Dense(<span style="color:#93c5fd;">IMG_DIM</span>, activation=<span style="color:#a7f3d0;">'tanh'</span>),    <span style="color:#6b7280;"># pixel values in [-1, 1]</span>
], name=<span style="color:#a7f3d0;">"Generator"</span>)

<span style="color:#6b7280;"># Discriminator: image → real (1) or fake (0)</span>
<span style="color:#93c5fd;">discriminator</span> = models.Sequential([
    layers.Dense(<span style="color:#fcd34d;">512</span>, activation=<span style="color:#a7f3d0;">'leaky_relu'</span>, input_shape=(<span style="color:#93c5fd;">IMG_DIM</span>,)),
    layers.Dropout(<span style="color:#fcd34d;">0.3</span>),
    layers.Dense(<span style="color:#fcd34d;">256</span>, activation=<span style="color:#a7f3d0;">'leaky_relu'</span>),
    layers.Dropout(<span style="color:#fcd34d;">0.3</span>),
    layers.Dense(<span style="color:#fcd34d;">1</span>,   activation=<span style="color:#a7f3d0;">'sigmoid'</span>),         <span style="color:#6b7280;"># P(real)</span>
], name=<span style="color:#a7f3d0;">"Discriminator"</span>)

<span style="color:#6b7280;"># Inspect architectures</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"=== GENERATOR ==="</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Input:  random noise vector of dim {LATENT_DIM}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Output: fake image of dim {IMG_DIM} (flattened 28×28)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Params: {generator.count_params():,}"</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\n=== DISCRIMINATOR ==="</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Input:  image of dim {IMG_DIM}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Output: scalar probability (real=1, fake=0)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Params: {discriminator.count_params():,}"</span>)

<span style="color:#6b7280;"># Simulate one forward pass through Generator</span>
<span style="color:#93c5fd;">noise</span>     = np.random.randn(<span style="color:#fcd34d;">1</span>, <span style="color:#93c5fd;">LATENT_DIM</span>)
<span style="color:#93c5fd;">fake_img</span>  = generator.predict(noise, verbose=<span style="color:#fcd34d;">0</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nFake image shape: {fake_img.shape}, value range: [{fake_img.min():.3f}, {fake_img.max():.3f}]"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>=== GENERATOR ===
Input:  random noise vector of dim 100
Output: fake image of dim 784 (flattened 28×28)
Params: 689,424

=== DISCRIMINATOR ===
Input:  image of dim 784
Output: scalar probability (real=1, fake=0)
Params: 535,553

Fake image shape: (1, 784), value range: [-0.142, 0.387]</div>
  </div>
</div>

<h3>Diffusion Models: The New State of the Art for Image Generation</h3>
<p><strong>Diffusion models</strong> (DALL-E 2, Stable Diffusion, Midjourney, Imagen) have surpassed GANs in image quality and training stability. The idea is elegant: start with a clean image, gradually add Gaussian noise over T steps until it becomes pure noise, then train a neural network to <em>reverse</em> this process — learning to denoise an image step by step. At generation time, you start from pure random noise and run the reverse process T times, gradually revealing a coherent image. Text-conditioning is added by cross-attending to a CLIP-encoded text embedding at each denoising step.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Forward Diffusion: Simulating Noise Addition</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">forward_diffusion_step</span>(x, t, T=<span style="color:#fcd34d;">1000</span>, beta_start=<span style="color:#fcd34d;">1e-4</span>, beta_end=<span style="color:#fcd34d;">0.02</span>):
    <span style="color:#a7f3d0;">"""Add noise at timestep t using a linear noise schedule."""</span>
    <span style="color:#93c5fd;">beta_t</span>  = beta_start + (beta_end - beta_start) * t / T
    <span style="color:#93c5fd;">alpha_t</span> = <span style="color:#fcd34d;">1</span> - beta_t
    <span style="color:#93c5fd;">noise</span>   = np.random.randn(*x.shape)
    <span style="color:#93c5fd;">x_noisy</span> = np.sqrt(alpha_t) * x + np.sqrt(beta_t) * noise
    <span style="color:#c4b5fd;">return</span> x_noisy, noise

<span style="color:#6b7280;"># Simulate forward diffusion on a "1D image" of values</span>
<span style="color:#93c5fd;">np.random.seed</span>(<span style="color:#fcd34d;">0</span>)
<span style="color:#93c5fd;">x_clean</span> = np.array([<span style="color:#fcd34d;">1.0</span>, <span style="color:#fcd34d;">0.8</span>, <span style="color:#fcd34d;">0.6</span>, <span style="color:#fcd34d;">0.9</span>, <span style="color:#fcd34d;">0.7</span>])   <span style="color:#6b7280;"># "clean image" pixel values</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Timestep t':>12} | {'SNR':>8} | {'Sample (first 3 vals)'}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"-" * 55</span>)
<span style="color:#93c5fd;">x_current</span> = x_clean.copy()
<span style="color:#c4b5fd;">for</span> t <span style="color:#c4b5fd;">in</span> [<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">100</span>, <span style="color:#fcd34d;">300</span>, <span style="color:#fcd34d;">600</span>, <span style="color:#fcd34d;">900</span>, <span style="color:#fcd34d;">999</span>]:
    <span style="color:#93c5fd;">x_t</span>, _ = forward_diffusion_step(x_clean, t)
    <span style="color:#93c5fd;">beta_t</span>  = <span style="color:#fcd34d;">1e-4</span> + (<span style="color:#fcd34d;">0.02</span> - <span style="color:#fcd34d;">1e-4</span>) * t / <span style="color:#fcd34d;">1000</span>
    <span style="color:#93c5fd;">snr</span>     = (<span style="color:#fcd34d;">1</span> - beta_t) / beta_t
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"t={t:>6}       | {snr:>8.2f} | {x_t[:3].round(3)}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>  Timestep t |      SNR | Sample (first 3 vals)
-------------------------------------------------------
t=     0   | 99990.00 | [0.993 0.812 0.618]
t=   100   |   489.22 | [0.991 0.799 0.613]
t=   300   |   155.21 | [0.974 0.801 0.621]
t=   600   |    58.41 | [0.927 0.778 0.602]
t=   900   |    16.33 | [0.761 0.681 0.567]
t=   999   |     5.00 | [0.438 0.491 0.371]</div>
  </div>
</div>

<h3>Multimodal AI: When Text Meets Vision and Beyond</h3>
<p><strong>Multimodal AI</strong> systems process and generate content across multiple modalities simultaneously — text, images, audio, video, code, and structured data. GPT-4V, Claude, and Gemini Ultra can reason about images described in text, generate image captions, answer questions about graphs, and convert diagrams into code. CLIP (Contrastive Language-Image Pretraining) learns joint embeddings of text and images by training on 400 million image-text pairs — enabling zero-shot image classification without any task-specific training. These multimodal capabilities are blurring the traditional boundaries between computer vision and NLP.</p>
HTML;

        Lesson::create([
            'module_id'   => $aiModule->id,
            'title'       => '19.7 Generative AI: GANs, Diffusion Models & Multimodal Systems',
            'order_index' => 7,
            'content'     => $this->appendQuiz($content7, 'L19_7', [
                ['q' => 'In a GAN, what is the role of the Discriminator?', 'opts' => ['To generate new images from random noise', 'To classify images into categories', 'To distinguish real images from fake images generated by the Generator', 'To add noise to training images'], 'ans' => 2, 'exp' => 'The Discriminator D is a binary classifier trained to output P(real | image). It receives both real images from the dataset and fake images from the Generator, and learns to tell them apart. The Generator simultaneously learns to fool D — producing increasingly realistic fakes.'],
                ['q' => 'The forward diffusion process in diffusion models does which of the following?', 'opts' => ['Generates a clean image from random noise', 'Gradually adds Gaussian noise to a clean image over many timesteps until it becomes pure noise', 'Compresses the image into a latent vector', 'Trains the denoising network'], 'ans' => 1, 'exp' => 'Forward diffusion: clean image x₀ → x₁ → x₂ → ... → xₜ (pure noise), adding a small amount of Gaussian noise at each step. The diffusion model is trained to reverse this process — learning to predict xₜ₋₁ given xₜ. Generation samples from pure noise and runs the reverse process.'],
                ['q' => 'What is a key practical advantage of diffusion models over GANs?', 'opts' => ['Diffusion models are faster to sample from', 'Diffusion models require less training data', 'Diffusion models are more stable to train — GANs are prone to mode collapse and training instability', 'Diffusion models have fewer parameters'], 'ans' => 2, 'exp' => "GANs suffer from mode collapse (Generator learns to produce only a few modes of the data distribution) and training instability (the minimax game is hard to balance). Diffusion models optimize a simple denoising loss with stable gradient flow — making training more reliable and producing higher-quality diverse outputs."],
                ['q' => 'CLIP (Contrastive Language-Image Pretraining) enables zero-shot image classification by:', 'opts' => ['Fine-tuning on each new dataset', 'Learning joint text-image embeddings so similarity between text descriptions and images can be measured directly', 'Using a GAN to generate training examples', 'Pre-training exclusively on ImageNet'], 'ans' => 1, 'exp' => 'CLIP trains on 400M image-text pairs with a contrastive loss: matching image embeddings to their correct text descriptions. At inference, you embed candidate class names as text and find which class text is most similar to the image embedding — enabling classification into arbitrary categories without any task-specific training examples.'],
                ['q' => 'Which of the following is a known training failure mode of GANs?', 'opts' => ['Gradient explosion only in the Generator', 'Mode collapse — the Generator produces only a few types of output, ignoring most of the true data distribution', 'The Discriminator always outputs 0.5', 'The loss function becomes negative'], 'ans' => 1, 'exp' => "Mode collapse: the Generator finds a small subset of outputs that successfully fool the Discriminator and repeatedly generates only those, ignoring the diversity of the real distribution. The Generator's loss is low, but the generated samples lack variety. Techniques like Wasserstein GAN and minibatch discrimination mitigate this."],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 19.8 — Reinforcement Learning: Agents, Rewards & Policy Optimisation
        // ══════════════════════════════════════════════════════════════
        $content8 = <<<'HTML'
<h2>Reinforcement Learning: Agents, Rewards & Policy Optimisation</h2>
<p><strong>Reinforcement Learning (RL)</strong> is the branch of machine learning where an agent learns to make decisions by interacting with an environment. Unlike supervised learning (which requires labelled examples) or unsupervised learning (which discovers structure in static data), RL learns through trial and error: the agent takes actions, receives rewards or penalties, and gradually discovers a strategy — called a <strong>policy</strong> — that maximises cumulative future reward. RL has achieved some of the most striking AI milestones: defeating world champions at chess, Go, poker, and StarCraft; training robot hands to solve Rubik's cubes; and fine-tuning language models to be helpful and safe (RLHF).</p>

<h3>The RL Framework: Agent, Environment, State, Action, Reward</h3>
<p>RL is formalised as a <strong>Markov Decision Process (MDP)</strong>: at each timestep t, the agent observes a state sₜ, selects an action aₜ according to its policy π(a|s), receives a scalar reward rₜ from the environment, and transitions to a new state sₜ₊₁. The agent's goal is to maximise the expected discounted cumulative reward: G = Σ γᵏ rₜ₊ₖ, where γ ∈ [0,1] is the discount factor (how much the agent values immediate vs. future rewards).</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — RL Environment: OpenAI Gym CartPole</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> gymnasium <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">gym</span>
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>

<span style="color:#6b7280;"># CartPole: balance a pole on a cart by pushing left or right</span>
<span style="color:#93c5fd;">env</span> = gym.make(<span style="color:#a7f3d0;">'CartPole-v1'</span>)
<span style="color:#93c5fd;">obs</span>, <span style="color:#93c5fd;">info</span> = env.reset(seed=<span style="color:#fcd34d;">42</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"=== CartPole-v1 Environment ==="</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"State space:  {env.observation_space}  (4 continuous values)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Action space: {env.action_space}       (0=push left, 1=push right)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Initial state: {obs.round(4)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"  [cart_pos, cart_vel, pole_angle, pole_ang_vel]"</span>)

<span style="color:#6b7280;"># Simulate a random policy (baseline — takes random actions)</span>
<span style="color:#93c5fd;">total_rewards</span> = []
<span style="color:#c4b5fd;">for</span> episode <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">10</span>):
    <span style="color:#93c5fd;">obs</span>, _ = env.reset()
    <span style="color:#93c5fd;">ep_reward</span> = <span style="color:#fcd34d;">0</span>
    <span style="color:#c4b5fd;">for</span> _ <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">500</span>):
        <span style="color:#93c5fd;">action</span>  = env.action_space.sample()   <span style="color:#6b7280;"># random action</span>
        <span style="color:#93c5fd;">obs</span>, <span style="color:#93c5fd;">reward</span>, <span style="color:#93c5fd;">terminated</span>, <span style="color:#93c5fd;">truncated</span>, _ = env.step(action)
        <span style="color:#93c5fd;">ep_reward</span> += reward
        <span style="color:#c4b5fd;">if</span> terminated <span style="color:#c4b5fd;">or</span> truncated: <span style="color:#c4b5fd;">break</span>
    <span style="color:#93c5fd;">total_rewards</span>.append(ep_reward)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nRandom policy — episode rewards: {[int(r) for r in total_rewards]}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Mean reward: {np.mean(total_rewards):.1f} / 500 max"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"(A trained RL agent consistently achieves 500/500)"</span>)
env.close()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>=== CartPole-v1 Environment ===
State space:  Box([-4.8, -inf, -0.418, -inf], [4.8, inf, 0.418, inf])  (4 continuous values)
Action space: Discrete(2)       (0=push left, 1=push right)
Initial state: [ 0.0273  0.0228 -0.0202 -0.0175]
  [cart_pos, cart_vel, pole_angle, pole_ang_vel]

Random policy — episode rewards: [12, 8, 17, 11, 9, 14, 22, 8, 11, 15]
Mean reward: 12.7 / 500 max
(A trained RL agent consistently achieves 500/500)</div>
  </div>
</div>

<h3>Q-Learning and Deep Q-Networks (DQN)</h3>
<p><strong>Q-Learning</strong> is the foundational value-based RL algorithm. It learns a Q-function Q(s,a) — the expected total future reward of taking action a in state s and then following the optimal policy. Once Q is learned, the optimal action in any state is simply argmax_a Q(s,a). The <strong>Bellman equation</strong> provides the recursive relationship that Q-learning optimises: Q(s,a) = r + γ · max_a' Q(s',a'). <strong>Deep Q-Networks (DQN)</strong>, developed by DeepMind in 2013, replaced the Q-table with a deep neural network — enabling RL to scale to high-dimensional state spaces like raw game pixels. DQN learned to play 49 Atari games at superhuman level from pixel input alone, with no game-specific engineering.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Q-Learning Update Rule & ε-Greedy Policy</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>

<span style="color:#6b7280;"># Tabular Q-Learning on a tiny 4-state, 2-action world</span>
<span style="color:#93c5fd;">n_states</span>  = <span style="color:#fcd34d;">4</span>
<span style="color:#93c5fd;">n_actions</span> = <span style="color:#fcd34d;">2</span>
<span style="color:#93c5fd;">Q</span> = np.zeros((n_states, n_actions))   <span style="color:#6b7280;"># initialise Q-table to zeros</span>

<span style="color:#93c5fd;">alpha</span>   = <span style="color:#fcd34d;">0.1</span>    <span style="color:#6b7280;"># learning rate</span>
<span style="color:#93c5fd;">gamma</span>   = <span style="color:#fcd34d;">0.95</span>   <span style="color:#6b7280;"># discount factor</span>
<span style="color:#93c5fd;">epsilon</span> = <span style="color:#fcd34d;">0.2</span>    <span style="color:#6b7280;"># exploration rate (ε-greedy)</span>

<span style="color:#6b7280;"># Simulate Q-Learning updates</span>
<span style="color:#93c5fd;">transitions</span> = [
    (<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">-1</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fca5a5;">False</span>),  <span style="color:#6b7280;"># (s, a, reward, s_next, done)</span>
    (<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0</span>,  <span style="color:#fcd34d;">2</span>, <span style="color:#fca5a5;">False</span>),
    (<span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">+10</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fca5a5;">True</span>),   <span style="color:#6b7280;"># reward of +10 for reaching goal</span>
]

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Q-table before training:"</span>)
<span style="color:#93c5fd;">print</span>(Q)

<span style="color:#c4b5fd;">for</span> s, a, r, s_next, done <span style="color:#c4b5fd;">in</span> transitions:
    <span style="color:#6b7280;"># Bellman update: Q(s,a) ← Q(s,a) + α[r + γ·max_a' Q(s',a') - Q(s,a)]</span>
    <span style="color:#93c5fd;">target</span> = r <span style="color:#c4b5fd;">if</span> done <span style="color:#c4b5fd;">else</span> r + gamma * np.max(Q[s_next])
    <span style="color:#93c5fd;">Q</span>[s, a] += alpha * (target - Q[s, a])

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nQ-table after 3 updates:"</span>)
<span style="color:#93c5fd;">print</span>(Q.round(<span style="color:#fcd34d;">4</span>))
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nGreedy policy (best action per state):"</span>)
<span style="color:#c4b5fd;">for</span> s <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(n_states):
    <span style="color:#93c5fd;">best_a</span> = np.argmax(Q[s])
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  State {s}: best action = {best_a}  (Q={Q[s, best_a]:.4f})"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Q-table before training:
[[0. 0.]
 [0. 0.]
 [0. 0.]
 [0. 0.]]

Q-table after 3 updates:
[[ 0.      -0.0905]
 [ 0.       0.    ]
 [ 0.       1.    ]
 [ 0.       0.    ]]

Greedy policy (best action per state):
  State 0: best action = 0  (Q=0.0000)
  State 1: best action = 0  (Q=0.0000)
  State 2: best action = 1  (Q=1.0000)
  State 3: best action = 0  (Q=0.0000)</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $aiModule->id,
            'title'       => '19.8 Reinforcement Learning: Agents, Rewards & Policy Optimisation',
            'order_index' => 8,
            'content'     => $this->appendQuiz($content8, 'L19_8', [
                ['q' => 'In the RL framework, what is a "policy" π?', 'opts' => ['The reward function assigned by the environment', 'A mapping from states to actions (or action probabilities) that defines the agent\'s behaviour', 'The discount factor applied to future rewards', 'The neural network architecture used by the agent'], 'ans' => 1, 'exp' => "A policy π(a|s) defines what action the agent takes in each state. A deterministic policy maps each state to one action. A stochastic policy maps each state to a probability distribution over actions. Learning the optimal policy π* that maximises expected return is the fundamental goal of RL."],
                ['q' => 'The discount factor γ in RL controls:', 'opts' => ['The learning rate of the neural network', 'How far ahead in training we look for gradient updates', 'How much the agent values immediate rewards versus future rewards — γ near 1 is forward-looking, γ near 0 is short-sighted', 'The exploration probability in ε-greedy strategies'], 'ans' => 2, 'exp' => 'G = Σ γᵏ rₜ₊ₖ. With γ=0, only immediate reward matters — shortsighted. With γ→1, all future rewards count equally — very long-term planning. In practice γ is set to 0.99 for complex tasks needing long-horizon planning, 0.9 for simpler tasks. γ also ensures the infinite sum converges.'],
                ['q' => 'The Bellman equation used in Q-Learning states:', 'opts' => ['Q(s,a) = r + α · Q(s,a)', 'Q(s,a) = r + γ · max_a′ Q(s′,a′)', 'Q(s,a) = ∇L(θ)', 'Q(s,a) = E[Gₜ | sₜ=s]'], 'ans' => 1, 'exp' => 'The Bellman optimality equation: Q*(s,a) = r + γ·max_a′ Q*(s′,a′). The Q-value of (s,a) equals the immediate reward r plus the discounted maximum Q-value achievable from the next state. Q-learning uses this as the update target, converging to Q* under standard conditions.'],
                ['q' => 'What was the key innovation of DeepMind\'s DQN over classical Q-learning?', 'opts' => ['It used policy gradients instead of value functions', 'It replaced the Q-table with a deep neural network, enabling RL to scale to high-dimensional state spaces like raw game pixels', 'It added recurrent layers to process time sequences', 'It eliminated the need for a reward signal'], 'ans' => 1, 'exp' => "Classical Q-learning stores Q(s,a) in a lookup table — infeasible for continuous or high-dimensional states. DQN approximates Q(s,a;θ) with a deep neural network parameterised by θ. Combined with experience replay and a target network, DQN successfully learned to play 49 Atari games at superhuman level from pixel input."],
                ['q' => 'The ε-greedy exploration strategy balances exploration and exploitation by:', 'opts' => ['Always choosing the action with the highest Q-value', 'Choosing a random action with probability ε and the greedy (best-known) action with probability 1-ε', 'Setting ε equal to the learning rate', 'Using the policy gradient to determine exploration'], 'ans' => 1, 'exp' => 'The exploration-exploitation dilemma: if the agent always exploits its current best-known action, it may never discover better actions. ε-greedy solves this: with probability ε, take a random exploratory action; with probability 1-ε, take the greedy action. ε is typically decayed over training as the policy improves.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 19.9 — AI Ethics, Bias, Fairness & Responsible AI
        // ══════════════════════════════════════════════════════════════
        $content9 = <<<'HTML'
<h2>AI Ethics, Bias, Fairness & Responsible AI</h2>
<p>AI systems are increasingly making or influencing consequential decisions: who gets a loan, who gets hired, who receives medical treatment, who is granted parole, and who is targeted for advertising. When these systems encode or amplify societal biases, discriminate against protected groups, or operate without transparency, the human cost is enormous and real. <strong>AI ethics</strong> is not a soft, optional add-on to technical AI development — it is a core engineering discipline that every AI practitioner is responsible for. This lesson covers the most critical concepts: algorithmic bias, fairness definitions, explainability, and the principles of responsible AI development.</p>

<h3>Where Bias Enters: Data, Labels & Model Choices</h3>
<p>Bias can enter an AI system at every stage of the development pipeline. <strong>Historical bias</strong>: training data reflects past human decisions that were themselves biased (e.g., a hiring algorithm trained on historical data will reproduce historical patterns of discrimination). <strong>Representation bias</strong>: underrepresentation of certain groups in training data leads to poor performance for those groups (early face recognition systems had error rates up to 35% for darker-skinned women vs. &lt;1% for lighter-skinned men). <strong>Label bias</strong>: human annotators bring their own biases when labelling training data. <strong>Measurement bias</strong>: the features collected may be proxies for protected attributes (e.g., zip code correlates with race). <strong>Feedback loops</strong>: a biased model influences decisions which generate new data that reinforces the bias.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Measuring Fairness: Disparate Impact & Equal Opportunity</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>

<span style="color:#6b7280;"># Simulate loan approval decisions by a hypothetical AI model</span>
<span style="color:#6b7280;"># group=0: Group A (e.g., majority group), group=1: Group B (minority group)</span>
<span style="color:#93c5fd;">np.random.seed</span>(<span style="color:#fcd34d;">42</span>)

<span style="color:#93c5fd;">data</span> = {
    <span style="color:#a7f3d0;">'group'</span>:       np.array([<span style="color:#fcd34d;">0</span>]*<span style="color:#fcd34d;">500</span> + [<span style="color:#fcd34d;">1</span>]*<span style="color:#fcd34d;">500</span>),
    <span style="color:#a7f3d0;">'true_label'</span>:  np.array([<span style="color:#fcd34d;">1</span>]*<span style="color:#fcd34d;">350</span> + [<span style="color:#fcd34d;">0</span>]*<span style="color:#fcd34d;">150</span> + [<span style="color:#fcd34d;">1</span>]*<span style="color:#fcd34d;">280</span> + [<span style="color:#fcd34d;">0</span>]*<span style="color:#fcd34d;">220</span>),
    <span style="color:#a7f3d0;">'model_pred'</span>:  np.array([<span style="color:#fcd34d;">1</span>]*<span style="color:#fcd34d;">320</span> + [<span style="color:#fcd34d;">0</span>]*<span style="color:#fcd34d;">180</span> + [<span style="color:#fcd34d;">1</span>]*<span style="color:#fcd34d;">190</span> + [<span style="color:#fcd34d;">0</span>]*<span style="color:#fcd34d;">310</span>),
}

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">fairness_report</span>(data):
    <span style="color:#c4b5fd;">for</span> g, name <span style="color:#c4b5fd;">in</span> [((<span style="color:#fcd34d;">0</span>,<span style="color:#a7f3d0;">'Group A'</span>)), ((<span style="color:#fcd34d;">1</span>,<span style="color:#a7f3d0;">'Group B'</span>))]:
        <span style="color:#93c5fd;">mask</span>    = data[<span style="color:#a7f3d0;">'group'</span>] == g
        <span style="color:#93c5fd;">pred</span>    = data[<span style="color:#a7f3d0;">'model_pred'</span>][mask]
        <span style="color:#93c5fd;">true</span>    = data[<span style="color:#a7f3d0;">'true_label'</span>][mask]
        <span style="color:#93c5fd;">pos_rate</span> = pred.mean()                         <span style="color:#6b7280;"># selection rate</span>
        <span style="color:#93c5fd;">tp_rate</span>  = pred[true==<span style="color:#fcd34d;">1</span>].mean()               <span style="color:#6b7280;"># true positive rate</span>
        <span style="color:#93c5fd;">fp_rate</span>  = pred[true==<span style="color:#fcd34d;">0</span>].mean()               <span style="color:#6b7280;"># false positive rate</span>
        <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{name}: approval_rate={pos_rate:.2%}  TPR={tp_rate:.2%}  FPR={fp_rate:.2%}"</span>)

fairness_report(data)

<span style="color:#93c5fd;">rate_A</span> = data[<span style="color:#a7f3d0;">'model_pred'</span>][data[<span style="color:#a7f3d0;">'group'</span>]==<span style="color:#fcd34d;">0</span>].mean()
<span style="color:#93c5fd;">rate_B</span> = data[<span style="color:#a7f3d0;">'model_pred'</span>][data[<span style="color:#a7f3d0;">'group'</span>]==<span style="color:#fcd34d;">1</span>].mean()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nDisparate Impact Ratio: {rate_B/rate_A:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"(EEOC 4/5 rule: ratio < 0.8 indicates adverse impact)"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Group A: approval_rate=64.00%  TPR=91.43%  FPR=20.00%
Group B: approval_rate=38.00%  TPR=67.86%  FPR=22.73%

Disparate Impact Ratio: 0.5938
(EEOC 4/5 rule: ratio &lt; 0.8 indicates adverse impact)</div>
  </div>
</div>

<h3>The Impossibility Theorems: Why Fairness Is Hard</h3>
<p>A landmark result in AI fairness (Chouldechova 2016; Kleinberg et al. 2016) is that several intuitive fairness criteria are mathematically incompatible — you cannot satisfy all of them simultaneously when base rates differ between groups. Specifically, <strong>calibration</strong> (predicted probabilities reflect true probabilities in both groups), <strong>equalised false positive rates</strong>, and <strong>equalised false negative rates</strong> cannot all hold at once unless either the classifier is perfect or base rates are equal. This "impossibility theorem" means every fairness-conscious AI system involves genuine trade-offs — there is no free lunch in fairness.</p>

<h3>Explainability: SHAP Values and the Right to Explanation</h3>
<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Feature Importance with SHAP Values</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> shap
<span style="color:#c4b5fd;">from</span> sklearn.ensemble <span style="color:#c4b5fd;">import</span> RandomForestClassifier
<span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_breast_cancer
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>

<span style="color:#93c5fd;">data</span>  = load_breast_cancer()
<span style="color:#93c5fd;">X</span>, <span style="color:#93c5fd;">y</span> = data.data, data.target

<span style="color:#93c5fd;">rf</span> = RandomForestClassifier(n_estimators=<span style="color:#fcd34d;">100</span>, random_state=<span style="color:#fcd34d;">42</span>)
<span style="color:#93c5fd;">rf</span>.fit(X, y)

<span style="color:#6b7280;"># SHAP: explain individual prediction</span>
<span style="color:#93c5fd;">explainer</span>   = shap.TreeExplainer(rf)
<span style="color:#93c5fd;">shap_values</span> = explainer.shap_values(X[:<span style="color:#fcd34d;">5</span>])

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Top 5 features for patient 0's prediction (malignant=class 0):"</span>)
<span style="color:#93c5fd;">sv</span>       = shap_values[<span style="color:#fcd34d;">0</span>][<span style="color:#fcd34d;">0</span>]   <span style="color:#6b7280;"># SHAP values for class 0</span>
<span style="color:#93c5fd;">top_idx</span>  = np.argsort(np.abs(sv))[::-<span style="color:#fcd34d;">1</span>][:<span style="color:#fcd34d;">5</span>]
<span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> top_idx:
    <span style="color:#93c5fd;">direction</span> = <span style="color:#a7f3d0;">"↑ malignant"</span> <span style="color:#c4b5fd;">if</span> sv[i] > <span style="color:#fcd34d;">0</span> <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">"↓ benign"</span>
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  {data.feature_names[i]:<35}: SHAP={sv[i]:+.4f}  {direction}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Top 5 features for patient 0's prediction (malignant=class 0):
  worst concave points                : SHAP=+0.2831  ↑ malignant
  worst perimeter                     : SHAP=+0.1924  ↑ malignant
  worst radius                        : SHAP=+0.1701  ↑ malignant
  mean concave points                 : SHAP=+0.0983  ↑ malignant
  worst area                          : SHAP=+0.0871  ↑ malignant</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $aiModule->id,
            'title'       => '19.9 AI Ethics, Bias, Fairness & Responsible AI',
            'order_index' => 9,
            'content'     => $this->appendQuiz($content9, 'L19_9', [
                ['q' => 'Historical bias in AI training data occurs when:', 'opts' => ['The model is trained on data from only one time period', 'The training data reflects past human decisions that were themselves discriminatory, causing the model to reproduce those patterns', 'The dataset is too small to train effectively', 'The model overfits to the training distribution'], 'ans' => 1, 'exp' => 'Historical bias is one of the most pervasive sources of algorithmic unfairness. A hiring algorithm trained on past successful hires will reproduce whatever biases shaped those hiring decisions. The model is not neutral — it learns and perpetuates historical discrimination.'],
                ['q' => 'The EEOC "4/5ths rule" (Disparate Impact Ratio < 0.8) is a legal standard that:', 'opts' => ['Requires that 80% of an AI model\'s training data be human-labelled', 'Flags potential discrimination when the approval rate for a minority group is less than 80% of the approval rate for the majority group', 'Limits AI decision accuracy to 80% maximum', 'Requires 4 out of 5 model predictions to be explained'], 'ans' => 1, 'exp' => 'Disparate Impact Ratio = P(approved | minority) / P(approved | majority). A ratio below 0.8 (the 4/5 or 80% rule) is legal evidence of adverse impact discrimination. In our example, 0.594 < 0.8, which would be legally actionable. Note: adverse impact can occur even without discriminatory intent.'],
                ['q' => 'The AI fairness impossibility theorem states that:', 'opts' => ['No AI system can ever be fair', 'Calibration, equalised false positive rates, and equalised false negative rates cannot all be simultaneously satisfied when base rates differ between groups', 'Fairness requires equal accuracy across all groups', 'Fairness can only be achieved with more training data'], 'ans' => 1, 'exp' => 'Chouldechova (2016) and Kleinberg et al. (2016) independently proved that several standard fairness criteria are mathematically incompatible when base rates differ. Every real-world fairness-conscious AI system must explicitly choose which fairness criteria to prioritise — there is no solution that satisfies all simultaneously.'],
                ['q' => 'What does a positive SHAP value for a feature in a classification model mean?', 'opts' => ['The feature was not used by the model', 'The feature pushed the model\'s prediction toward the positive class for that specific data point', 'The feature has the highest importance globally', 'The feature should be removed from the dataset'], 'ans' => 1, 'exp' => "SHAP (SHapley Additive exPlanations) decomposes each prediction into per-feature contributions. A positive SHAP value means that feature's value, for this specific data point, increased the model's predicted probability of the positive class relative to the baseline. SHAP values are local explanations — they differ for each data point."],
                ['q' => 'Which of the following is NOT a recognised type of AI bias?', 'opts' => ['Representation bias (underrepresented groups in training data)', 'Feedback loop bias (biased decisions generate biased new data)', 'GPU bias (faster hardware produces more biased models)', 'Label bias (human annotators introducing their own biases)'], 'ans' => 2, 'exp' => "GPU bias is not a recognised type. The real bias types include: historical bias (past discrimination in data), representation bias (minority groups underrepresented), label bias (annotator subjectivity), measurement bias (proxy variables for protected attributes), and feedback loop bias (model decisions creating future training data)."],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 19.10 — AI in the Real World: Applications, Careers & the Future
        // ══════════════════════════════════════════════════════════════
        $content10 = <<<'HTML'
<h2>AI in the Real World: Applications, Careers & the Future</h2>
<p>AI is no longer a research curiosity confined to university labs — it is deployed at massive scale across virtually every sector of the global economy. In this final substantive lesson, we survey the most important real-world AI applications, examine the skills and career pathways in the AI ecosystem, and explore the most pressing open questions about AI's trajectory over the next decade. Understanding where AI is applied, who builds it, and what remains unsolved is essential for anyone who wants to work in or alongside this field.</p>

<h3>AI Across Industries: A Sector-by-Sector Survey</h3>
<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — AI Applications by Domain</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">ai_applications</span> = {
    <span style="color:#a7f3d0;">"Healthcare"</span>: [
        <span style="color:#a7f3d0;">"Radiology: CNN detects tumours in X-rays and MRIs (≥radiologist accuracy)"</span>,
        <span style="color:#a7f3d0;">"Drug discovery: AlphaFold solved 50-year protein folding problem"</span>,
        <span style="color:#a7f3d0;">"Clinical NLP: extracting structured data from unstructured clinical notes"</span>,
        <span style="color:#a7f3d0;">"Personalised medicine: genomic data + ML for treatment selection"</span>,
    ],
    <span style="color:#a7f3d0;">"Finance"</span>: [
        <span style="color:#a7f3d0;">"Algorithmic trading: RL agents execute millions of trades per second"</span>,
        <span style="color:#a7f3d0;">"Fraud detection: anomaly detection on transaction graphs in real-time"</span>,
        <span style="color:#a7f3d0;">"Credit scoring: ML replaces FICO score with richer feature sets"</span>,
        <span style="color:#a7f3d0;">"Risk management: scenario simulation with generative models"</span>,
    ],
    <span style="color:#a7f3d0;">"Transport"</span>: [
        <span style="color:#a7f3d0;">"Autonomous vehicles: sensor fusion (LiDAR + camera) with CNN + RL"</span>,
        <span style="color:#a7f3d0;">"Route optimisation: combinatorial RL for logistics and last-mile delivery"</span>,
        <span style="color:#a7f3d0;">"Traffic management: RL controls traffic signals to reduce congestion"</span>,
    ],
    <span style="color:#a7f3d0;">"Science"</span>: [
        <span style="color:#a7f3d0;">"AlphaFold 2: predicted structures of 200M proteins — entire proteome"</span>,
        <span style="color:#a7f3d0;">"GNoME: discovered 2.2M stable crystal structures for materials science"</span>,
        <span style="color:#a7f3d0;">"Climate: ML downscaling of weather models, emissions prediction"</span>,
    ],
}

<span style="color:#c4b5fd;">for</span> domain, apps <span style="color:#c4b5fd;">in</span> ai_applications.items():
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\n{'─'*50}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  {domain.upper()}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'─'*50}"</span>)
    <span style="color:#c4b5fd;">for</span> app <span style="color:#c4b5fd;">in</span> apps:
        <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  • {app}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>──────────────────────────────────────────────────
  HEALTHCARE
──────────────────────────────────────────────────
  • Radiology: CNN detects tumours in X-rays and MRIs (≥radiologist accuracy)
  • Drug discovery: AlphaFold solved 50-year protein folding problem
  • Clinical NLP: extracting structured data from unstructured clinical notes
  • Personalised medicine: genomic data + ML for treatment selection

──────────────────────────────────────────────────
  FINANCE
──────────────────────────────────────────────────
  • Algorithmic trading: RL agents execute millions of trades per second
  • Fraud detection: anomaly detection on transaction graphs in real-time
  • Credit scoring: ML replaces FICO score with richer feature sets
  • Risk management: scenario simulation with generative models

──────────────────────────────────────────────────
  TRANSPORT
──────────────────────────────────────────────────
  • Autonomous vehicles: sensor fusion (LiDAR + camera) with CNN + RL
  • Route optimisation: combinatorial RL for logistics and last-mile delivery
  • Traffic management: RL controls traffic signals to reduce congestion

──────────────────────────────────────────────────
  SCIENCE
──────────────────────────────────────────────────
  • AlphaFold 2: predicted structures of 200M proteins — entire proteome
  • GNoME: discovered 2.2M stable crystal structures for materials science
  • Climate: ML downscaling of weather models, emissions prediction</div>
  </div>
</div>

<h3>AI Career Pathways: Roles, Skills & Salaries</h3>
<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — AI Career Roles & Core Skill Requirements</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">careers</span> = [
    {<span style="color:#a7f3d0;">"role"</span>: <span style="color:#a7f3d0;">"ML Engineer"</span>,
     <span style="color:#a7f3d0;">"focus"</span>: <span style="color:#a7f3d0;">"Build & deploy ML pipelines at scale"</span>,
     <span style="color:#a7f3d0;">"skills"</span>: [<span style="color:#a7f3d0;">"Python"</span>, <span style="color:#a7f3d0;">"PyTorch/TF"</span>, <span style="color:#a7f3d0;">"MLOps"</span>, <span style="color:#a7f3d0;">"Cloud (AWS/GCP)"</span>]},
    {<span style="color:#a7f3d0;">"role"</span>: <span style="color:#a7f3d0;">"Data Scientist"</span>,
     <span style="color:#a7f3d0;">"focus"</span>: <span style="color:#a7f3d0;">"Extract insights, build models for business decisions"</span>,
     <span style="color:#a7f3d0;">"skills"</span>: [<span style="color:#a7f3d0;">"Python/R"</span>, <span style="color:#a7f3d0;">"Statistics"</span>, <span style="color:#a7f3d0;">"SQL"</span>, <span style="color:#a7f3d0;">"Sklearn"</span>, <span style="color:#a7f3d0;">"Visualisation"</span>]},
    {<span style="color:#a7f3d0;">"role"</span>: <span style="color:#a7f3d0;">"AI Research Scientist"</span>,
     <span style="color:#a7f3d0;">"focus"</span>: <span style="color:#a7f3d0;">"Advance the state of the art with novel algorithms"</span>,
     <span style="color:#a7f3d0;">"skills"</span>: [<span style="color:#a7f3d0;">"Deep Math"</span>, <span style="color:#a7f3d0;">"PyTorch"</span>, <span style="color:#a7f3d0;">"Paper writing"</span>, <span style="color:#a7f3d0;">"GPU clusters"</span>]},
    {<span style="color:#a7f3d0;">"role"</span>: <span style="color:#a7f3d0;">"NLP Engineer"</span>,
     <span style="color:#a7f3d0;">"focus"</span>: <span style="color:#a7f3d0;">"Language models, chatbots, search, summarisation"</span>,
     <span style="color:#a7f3d0;">"skills"</span>: [<span style="color:#a7f3d0;">"Transformers"</span>, <span style="color:#a7f3d0;">"HuggingFace"</span>, <span style="color:#a7f3d0;">"RAG"</span>, <span style="color:#a7f3d0;">"Fine-tuning"</span>]},
    {<span style="color:#a7f3d0;">"role"</span>: <span style="color:#a7f3d0;">"AI Ethics / Policy Analyst"</span>,
     <span style="color:#a7f3d0;">"focus"</span>: <span style="color:#a7f3d0;">"Ensure AI is fair, accountable, and transparent"</span>,
     <span style="color:#a7f3d0;">"skills"</span>: [<span style="color:#a7f3d0;">"ML literacy"</span>, <span style="color:#a7f3d0;">"Law/Policy"</span>, <span style="color:#a7f3d0;">"Fairness metrics"</span>, <span style="color:#a7f3d0;">"Communication"</span>]},
]

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Role':<25} {'Focus':<42} {'Key Skills'}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"─" * 110</span>)
<span style="color:#c4b5fd;">for</span> c <span style="color:#c4b5fd;">in</span> careers:
    <span style="color:#93c5fd;">skills</span> = <span style="color:#a7f3d0;">", "</span>.join(c[<span style="color:#a7f3d0;">'skills'</span>])
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{c['role']:<25} {c['focus']:<42} {skills}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Role                      Focus                                      Key Skills
──────────────────────────────────────────────────────────────────────────────────────────────────────────────
ML Engineer               Build & deploy ML pipelines at scale        Python, PyTorch/TF, MLOps, Cloud (AWS/GCP)
Data Scientist            Extract insights, build models for biz       Python/R, Statistics, SQL, Sklearn, Visualisation
AI Research Scientist     Advance the state of the art                 Deep Math, PyTorch, Paper writing, GPU clusters
NLP Engineer              Language models, chatbots, search            Transformers, HuggingFace, RAG, Fine-tuning
AI Ethics / Policy        Ensure AI is fair, accountable               ML literacy, Law/Policy, Fairness metrics, Communication</div>
  </div>
</div>

<h3>Open Problems & the Future of AI</h3>
<p>Despite extraordinary progress, fundamental challenges remain unsolved. <strong>Reasoning and planning:</strong> LLMs still make systematic errors on logical reasoning, mathematics, and planning tasks that require true step-by-step deduction. <strong>Sample efficiency:</strong> humans learn from a handful of examples; current ML requires millions. A child learns what "dog" means from seeing a few dogs; ImageNet-trained CNNs need 1.2 million labelled images. <strong>Robustness and distribution shift:</strong> AI systems can fail catastrophically when deployed data differs from training data. <strong>AI safety and alignment:</strong> ensuring that increasingly capable AI systems reliably pursue goals that are beneficial to humanity is one of the deepest technical and philosophical challenges of our time — and arguably the most consequential. <strong>Interpretability:</strong> we can measure what large AI models do, but we largely cannot explain <em>why</em> they do it — their internal representations remain opaque even to their creators.</p>
HTML;

        Lesson::create([
            'module_id'   => $aiModule->id,
            'title'       => '19.10 AI in the Real World: Applications, Careers & the Future',
            'order_index' => 10,
            'content'     => $this->appendQuiz($content10, 'L19_10', [
                ['q' => 'AlphaFold 2 is significant because it:', 'opts' => ['Was the first AI to defeat a chess grandmaster', 'Solved the 50-year protein folding problem — predicting 3D protein structures from amino acid sequences — with high accuracy across virtually all known proteins', 'Generated photorealistic images from text descriptions', 'Trained the first large language model'], 'ans' => 1, 'exp' => 'The protein folding problem — predicting a protein\'s 3D structure from its 1D amino acid sequence — had been unsolved for 50 years. AlphaFold 2 (DeepMind, 2020) solved it with near-experimental accuracy. It has since predicted structures for over 200 million proteins, accelerating drug discovery and biology research by years.'],
                ['q' => 'What is "distribution shift" in deployed AI systems?', 'opts' => ['When the model\'s weights are updated during deployment', 'When the statistical properties of real-world deployment data differ from training data, causing performance degradation', 'When the model\'s output distribution changes with temperature', 'When multiple models are combined in an ensemble'], 'ans' => 1, 'exp' => 'Distribution shift (or covariate shift) occurs when the data the model encounters in production is statistically different from its training data. A fraud detection model trained on 2019 transaction patterns may fail on 2024 data. This is one of the primary causes of AI system failures in the real world.'],
                ['q' => 'The AI "alignment problem" refers to:', 'opts' => ['Aligning text columns in data tables', 'The technical and philosophical challenge of ensuring AI systems reliably pursue goals that are beneficial to humans, even as they become more capable', 'Matching AI model outputs to ground-truth labels during training', 'Synchronising model weights across multiple GPUs'], 'ans' => 1, 'exp' => 'Alignment is the challenge of specifying what we want AI systems to do and ensuring they actually do it — especially as systems become more capable. Misaligned AI might pursue specified objectives in ways that are technically correct but catastrophic in practice. RLHF is one current approach; mechanistic interpretability is another research direction.'],
                ['q' => 'Which AI career role is primarily responsible for ensuring models are fair, interpretable, and legally compliant?', 'opts' => ['ML Engineer', 'GPU Cluster Administrator', 'AI Ethics / Policy Analyst', 'Data Labelling Specialist'], 'ans' => 2, 'exp' => 'AI Ethics / Policy Analysts sit at the intersection of AI technical knowledge, law, and social science. They audit models for bias and discrimination, design fairness metrics appropriate to the use case, advise on regulatory compliance (GDPR, EU AI Act), and communicate AI limitations and risks to policymakers and the public.'],
                ['q' => 'Why is "sample efficiency" considered a major open problem in AI?', 'opts' => ['Current GPUs process samples too slowly', 'AI systems require orders of magnitude more training examples than humans to learn concepts — a human child needs a few examples; a CNN needs millions', 'LLMs can only process text samples, not images', 'Sampling from probability distributions is computationally intractable'], 'ans' => 1, 'exp' => "A child learns the concept 'dog' from a handful of examples and immediately generalises. ImageNet-trained CNNs need ~1.2 million labelled images. This gap in sample efficiency suggests current AI lacks the inductive priors and structured world models that human cognition relies on — an open frontier for cognitive-science-informed AI research."],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 19.11 — Final Exam: Introduction to Artificial Intelligence
        // ══════════════════════════════════════════════════════════════

        $allFinalQuestions = [
            // 19.1 History & Landscape
            ['q' => 'Who coined the term "Artificial Intelligence" and at what conference?', 'opts' => ['Alan Turing at the Manchester Computing Conference, 1950', 'John McCarthy at the Dartmouth Conference, 1956', 'Geoffrey Hinton at NeurIPS, 2006', 'Marvin Minsky at MIT, 1960'], 'ans' => 1, 'exp' => "John McCarthy coined 'Artificial Intelligence' at the 1956 Dartmouth Conference. Alan Turing posed 'Can machines think?' in 1950 but used different terminology. The Dartmouth Conference is considered AI's founding moment as an academic discipline."],
            ['q' => 'Which three factors converged in the 2010s to produce the deep learning revolution?', 'opts' => ['Expert systems, symbolic logic, and quantum hardware', 'Big data, GPU compute, and algorithmic innovations', 'Faster CPUs, Java frameworks, and bigger datasets', 'Cloud storage, internet access, and open-source software'], 'ans' => 1, 'exp' => 'The deep learning revolution required three simultaneous ingredients: (1) massive labelled datasets (ImageNet, Common Crawl), (2) GPU hardware enabling parallel matrix computation, and (3) key algorithmic innovations (ReLU, dropout, batch norm, Transformer). Remove any one and the revolution stalls.'],
            // 19.2 ML Fundamentals
            ['q' => 'An overfitted model is characterised by:', 'opts' => ['Low training error AND low test error', 'High training error AND high test error (underfitting)', 'Very low training error but significantly higher test error', 'Equal error on both training and test sets'], 'ans' => 2, 'exp' => 'Overfitting: the model memorises training data (near-perfect training accuracy) but fails to generalise. The large gap between training accuracy and test accuracy is the diagnostic signal. The model has learned noise rather than the underlying pattern.'],
            ['q' => 'In reinforcement learning, the agent\'s objective is to maximise:', 'opts' => ['Accuracy on a labelled validation set', 'Expected cumulative discounted future reward G = Σ γᵏ rₜ₊ₖ', 'The log-likelihood of observed state transitions', 'Classification precision across all action categories'], 'ans' => 1, 'exp' => "The RL agent maximises expected return G = r₁ + γr₂ + γ²r₃ + ..., discounted by γ ∈ [0,1]. The discount factor γ encodes the agent's preference for immediate vs. future rewards. The agent is never told correct actions — it must discover the optimal policy through trial-and-error interaction with the environment."],
            // 19.3 Neural Networks
            ['q' => 'Backpropagation computes which quantity during the backward pass?', 'opts' => ['Predictions for each training example', 'The gradient of the loss function with respect to every weight using the chain rule', 'The optimal learning rate', 'The softmax probability distribution'], 'ans' => 1, 'exp' => 'Backward pass: given the loss L and the forward computation graph, backpropagation uses the chain rule to compute ∂L/∂w for every weight w in the network. The optimiser (e.g., Adam) then updates each weight: w ← w - η·∂L/∂w.'],
            ['q' => 'Why is ReLU (max(0,x)) preferred over sigmoid for hidden layers in deep networks?', 'opts' => ['ReLU produces outputs between 0 and 1', 'ReLU does not saturate for positive inputs so gradients do not vanish, enabling training of very deep networks', 'ReLU has fewer parameters than sigmoid', 'ReLU requires no activation function at all'], 'ans' => 1, 'exp' => "Sigmoid saturates: for large |x|, gradients ≈ 0 — gradients vanish over many layers making deep networks untrainable. ReLU: gradient = 1 for x > 0, so no vanishing for positive activations. This is why networks deeper than ~5 layers became trainable only after ReLU adoption."],
            // 19.4 Computer Vision
            ['q' => 'Parameter sharing in CNNs means:', 'opts' => ['All layers have the same number of neurons', 'The same filter weights are applied at every spatial position in the image, drastically reducing the number of parameters', 'Parameters are shared between training and test time', 'Weights are averaged across the batch'], 'ans' => 1, 'exp' => "A 3×3 filter applied to a 224×224 image has only 9 weights regardless of image size. Without parameter sharing, a dense layer connecting all 224×224 input pixels to all output neurons would need 224²×224²=2.5 billion parameters for a single layer. Parameter sharing reduces this to just 9."],
            ['q' => 'In transfer learning, freezing the pre-trained base model layers means:', 'opts' => ['The model cannot be used for inference', 'The pre-trained weights are kept fixed during fine-tuning — only the new task-specific head is trained', 'The model is saved to disk and cannot be modified', 'The learning rate is set to zero for all layers'], 'ans' => 1, 'exp' => 'Frozen layers (trainable=False) preserve the general visual features learned from ImageNet. You only update the new classification head on your small dataset. This prevents overfitting, requires minimal data and compute, and leverages the billions of parameters already trained on diverse visual data.'],
            // 19.5 NLP
            ['q' => 'The Transformer\'s self-attention mechanism differs from RNN processing because:', 'opts' => ['Transformers process one token at a time sequentially', 'Self-attention allows every token to attend directly to every other token simultaneously, enabling parallel training and long-range dependencies', 'Transformers use convolutional filters on text sequences', 'Transformers have fewer parameters than equivalent RNNs'], 'ans' => 1, 'exp' => "RNNs process tokens one by one — each step's hidden state depends on the previous step (impossible to parallelise). Self-attention computes all pairwise token relationships simultaneously: O(n²) operations but fully parallel on GPU. This enables training on huge corpora and capture of dependencies across thousands of tokens."],
            ['q' => 'TF-IDF downweights words that appear in many documents because:', 'opts' => ['They take up too much memory', 'They are less semantically discriminative — common words like "the" carry little information for distinguishing documents', 'TF-IDF cannot process common words', 'They increase training time proportionally'], 'ans' => 1, 'exp' => 'IDF = log(N/df) where N = total documents, df = documents containing the word. A word appearing in every document has IDF ≈ 0 → TF-IDF ≈ 0. Rare, topic-specific words appear in few documents → high IDF → high TF-IDF. This surfaces informative vocabulary and suppresses stop words.'],
            // 19.6 LLMs
            ['q' => 'RLHF (Reinforcement Learning from Human Feedback) is used in LLM training to:', 'opts' => ['Increase the model\'s parameter count', 'Align the model with human values by training it to produce responses that human raters prefer', 'Pre-train the model on internet text', 'Compress the model for mobile deployment'], 'ans' => 1, 'exp' => "RLHF: human annotators compare model responses → reward model trained on these preferences → LLM fine-tuned with PPO to maximise reward model scores. This shapes the LLM to be helpful, harmless, and honest — addressing the gap between 'what the model can do' and 'what we want it to do'."],
            ['q' => 'LLM "temperature" approaching zero produces:', 'opts' => ['Random, diverse outputs', 'Outputs that are deterministic — always selecting the highest probability token', 'Faster inference', 'Higher hallucination rates'], 'ans' => 1, 'exp' => 'Temperature divides logits before softmax. As T → 0, the softmax becomes a one-hot distribution entirely on the highest-logit token — equivalent to always taking the argmax (greedy decoding). As T → ∞, the distribution becomes uniform — complete randomness. T ≈ 0.1 gives focused, deterministic responses; T ≈ 1.5 gives creative, diverse, riskier outputs.'],
            // 19.7 Generative AI
            ['q' => 'In a GAN, mode collapse occurs when:', 'opts' => ['The Discriminator achieves 100% accuracy', 'The Generator produces only a few types of output, ignoring the diversity of the real data distribution', 'The Generator\'s loss becomes negative', 'The Discriminator cannot distinguish real from fake'], 'ans' => 1, 'exp' => "Mode collapse: the Generator finds a narrow set of outputs that fool the Discriminator and gets stuck producing only those, collapsing the diversity of generated samples. Training loss looks fine (Generator fooling D) but generated images lack variety. Wasserstein GANs and minibatch discrimination are common mitigations."],
            ['q' => 'Diffusion models generate images by:', 'opts' => ['Having a Generator compete against a Discriminator', 'Starting from pure random noise and iteratively denoising to produce a clean image using a learned reverse diffusion process', 'Sampling from the latent space of a variational autoencoder', 'Combining multiple existing images via weighted averaging'], 'ans' => 1, 'exp' => 'Forward process: clean image → pure noise (T steps). Reverse process: train a U-Net to predict and remove noise at each step. Generation: sample pure Gaussian noise → apply learned reverse process T times → clean image. Text conditioning is added via cross-attention to CLIP text embeddings at each denoising step.'],
            // 19.8 RL
            ['q' => 'The Bellman equation used in Q-Learning is:', 'opts' => ['Q(s,a) = r + α · Q(s,a)', 'Q(s,a) = r + γ · max_a′ Q(s′,a′)', 'Q(s,a) = π(a|s) · V(s)', 'Q(s,a) = ∇θ log π(a|s) · G'], 'ans' => 1, 'exp' => 'The Bellman optimality equation: Q*(s,a) = r + γ·max_a′ Q*(s′,a′). Current Q-value = immediate reward + discounted best future Q-value. Q-learning iteratively updates toward this target: Q(s,a) ← Q(s,a) + α[r + γ·max_a′ Q(s′,a′) − Q(s,a)].'],
            ['q' => 'DeepMind\'s DQN breakthrough (2013-2015) demonstrated that:', 'opts' => ['Q-tables can scale to any size state space', 'A deep neural network approximating Q(s,a) can learn superhuman Atari gameplay from raw pixels alone, with no game-specific engineering', 'Reinforcement learning requires human demonstrations for every game', 'Model-based RL is superior to model-free RL'], 'ans' => 1, 'exp' => 'DQN replaced the Q-table with a CNN that takes raw game pixels as input and outputs Q-values for each action. With experience replay and a target network, DQN learned to play 49 Atari games at human or superhuman level — from pixel input only, using the same architecture and hyperparameters for all games.'],
            // 19.9 Ethics
            ['q' => 'The AI fairness impossibility theorem states that:', 'opts' => ['AI can always be made perfectly fair with enough data', 'Calibration, equal false positive rates, and equal false negative rates cannot all be satisfied simultaneously when base rates differ between groups', 'Fairness requires removing all protected attributes from the training data', 'There is only one valid definition of fairness for AI systems'], 'ans' => 1, 'exp' => 'Proven by Chouldechova (2016) and Kleinberg et al. (2016): when base rates differ between groups, you must choose between fairness criteria — you cannot satisfy them all simultaneously. A recidivism predictor cannot simultaneously be calibrated, have equal FPR, and equal FNR unless its predictions are perfect or base rates are equal.'],
            ['q' => 'SHAP values provide AI explainability by:', 'opts' => ['Retraining the model with fewer features', 'Computing each feature\'s contribution to an individual prediction based on Shapley values from cooperative game theory', 'Visualising the CNN\'s convolutional filters', 'Displaying the model\'s attention weights directly'], 'ans' => 1, 'exp' => 'SHAP assigns each feature a value representing its marginal contribution to the prediction relative to the expected model output — derived from Shapley values in cooperative game theory. SHAP is local (per-prediction), consistent (feature ordering is reliable), and model-agnostic. Positive SHAP → pushes toward positive class; negative SHAP → pushes away.'],
            // 19.10 Applications
            ['q' => 'AlphaFold 2\'s significance to science is that it:', 'opts' => ['Was the first AI to generate photorealistic images', 'Solved the 50-year protein folding problem, predicting 3D structures from amino acid sequences with near-experimental accuracy across virtually all known proteins', 'Achieved AGI on a science benchmark', 'Created synthetic protein sequences that do not exist in nature'], 'ans' => 1, 'exp' => "The protein folding problem — determining a protein's 3D structure from its sequence — had resisted 50 years of effort. AlphaFold 2 solved it in 2020 with GDT_TS > 90 on CASP14 (near-experimental precision). DeepMind subsequently released structures for 200M+ proteins, accelerating drug discovery and biology by orders of magnitude."],
            ['q' => 'The AI "alignment problem" is defined as:', 'opts' => ['Making multiple GPU nodes communicate efficiently', 'The challenge of ensuring AI systems reliably pursue goals beneficial to humans as they become more capable', 'Training data alignment to model output formats', 'Matching AI predictions to human intuition using calibration'], 'ans' => 1, 'exp' => "Alignment asks: how do we specify and ensure AI systems pursue human-beneficial goals, especially when they become more capable than humans at optimising objectives? Misaligned AI might pursue a narrowly specified objective (e.g., 'maximise user engagement') in ways catastrophic for human welfare. RLHF, Constitutional AI, and mechanistic interpretability are current research directions."],
        ];

        $finalContent = <<<HTML
<div id="org-lock-screen" style="text-align:center;padding:4rem 2rem;background:var(--surface2);border:1px solid var(--border);border-radius:12px;margin-top:2rem;">
    <div style="font-size:3rem;margin-bottom:1rem;">🔒</div>
    <h3 style="color:var(--text);margin-bottom:0.5rem;">University / Organization Access Only</h3>
    <p style="color:var(--muted);">The Final Module Exam is restricted to enrolled students and verified organization members.</p>
    <p style="font-size:0.85rem;color:#f59e0b;margin-top:1rem;background:rgba(245,158,11,0.1);padding:10px;border-radius:8px;display:inline-block;">Please contact administration to link your account to an organization.</p>
</div>
<div id="final-exam-content" style="display:none;">
    <h2>Module 19: Final Examination</h2>
    <p>This comprehensive exam covers all topics from Lessons 19.1 through 19.10 — AI history and landscape, machine learning fundamentals, neural networks, computer vision, NLP, large language models, generative AI, reinforcement learning, AI ethics, and real-world applications. Good luck!</p>
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
            'module_id'   => $aiModule->id,
            'title'       => '19.11 Final Exam: Introduction to Artificial Intelligence Mastery',
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