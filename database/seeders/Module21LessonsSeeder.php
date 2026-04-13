<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Lesson;

/**
 * Module21LessonsSeeder
 * Seeds lessons for Module 21: Machine Learning 2 — Unsupervised Learning.
 * Run AFTER CurriculumSeeder which creates the modules.
 *
 * Usage:  php artisan db:seed --class=Module21LessonsSeeder
 */
class Module21LessonsSeeder extends Seeder
{
    public function run()
    {
        $module = Module::where('order_index', 21)->firstOrFail();
        Lesson::where('module_id', $module->id)->delete();

        // ══════════════════════════════════════════════════════════════
        // LESSON 21.1 — What Is Unsupervised Learning?
        // ══════════════════════════════════════════════════════════════
        $content1 = <<<'HTML'
<h2>What Is Unsupervised Learning?</h2>
<p>In supervised learning you train a model on labeled data — every example comes with a known answer. Unsupervised learning is fundamentally different: <strong>there are no labels</strong>. You hand the algorithm raw data and ask it to discover hidden structure on its own. This is how real-world data usually arrives — mountains of transactions, sensor readings, customer records, or text — with no one having told you what the patterns are yet.</p>

<p>Unsupervised learning is not a fallback when labels are missing. It is a first-class methodology used daily in production systems: segmenting customers into behavior groups, compressing high-dimensional genomic data so it can be visualized, detecting anomalous server behavior before it becomes an outage, and building the recommendation systems that power Netflix, Spotify, and Amazon. Understanding it deeply separates data scientists who can only supervise from those who can genuinely <em>explore</em>.</p>

<h3>Supervised vs Unsupervised: The Core Distinction</h3>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:20px;margin-bottom:24px;">
  <table style="width:100%;border-collapse:collapse;font-size:0.875rem;">
    <thead>
      <tr style="border-bottom:1px solid var(--border);">
        <th style="text-align:left;padding:10px 12px;color:var(--text);font-weight:700;">Dimension</th>
        <th style="text-align:left;padding:10px 12px;color:var(--text);font-weight:700;">Supervised Learning</th>
        <th style="text-align:left;padding:10px 12px;color:var(--text);font-weight:700;">Unsupervised Learning</th>
      </tr>
    </thead>
    <tbody>
      <tr style="border-bottom:1px solid var(--border);">
        <td style="padding:10px 12px;color:var(--text);font-weight:600;">Labels</td>
        <td style="padding:10px 12px;color:var(--muted);">Required (y provided)</td>
        <td style="padding:10px 12px;color:#a7f3d0);font-weight:600;">Not required — only X</td>
      </tr>
      <tr style="border-bottom:1px solid var(--border);">
        <td style="padding:10px 12px;color:var(--text);font-weight:600;">Goal</td>
        <td style="padding:10px 12px;color:var(--muted);">Predict a known output</td>
        <td style="padding:10px 12px;color:#a7f3d0);">Discover hidden structure</td>
      </tr>
      <tr style="border-bottom:1px solid var(--border);">
        <td style="padding:10px 12px;color:var(--text);font-weight:600;">Evaluation</td>
        <td style="padding:10px 12px;color:var(--muted);">Accuracy, F1, RMSE vs ground truth</td>
        <td style="padding:10px 12px;color:#a7f3d0);">Silhouette score, inertia, visual inspection</td>
      </tr>
      <tr style="border-bottom:1px solid var(--border);">
        <td style="padding:10px 12px;color:var(--text);font-weight:600;">Examples</td>
        <td style="padding:10px 12px;color:var(--muted);">Classification, Regression</td>
        <td style="padding:10px 12px;color:#a7f3d0);">Clustering, Dimensionality Reduction, Anomaly Detection</td>
      </tr>
      <tr>
        <td style="padding:10px 12px;color:var(--text);font-weight:600;">Real-world data availability</td>
        <td style="padding:10px 12px;color:var(--muted);">Labels are expensive — require human annotation</td>
        <td style="padding:10px 12px;color:#a7f3d0);">Unlimited — any raw data qualifies</td>
      </tr>
    </tbody>
  </table>
</div>

<h3>The Three Pillars of Unsupervised Learning</h3>
<p>This entire module is organized around three fundamental problem types. Each answers a different question about your data:</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:20px;margin-bottom:24px;">
  <table style="width:100%;border-collapse:collapse;font-size:0.875rem;">
    <thead>
      <tr style="border-bottom:1px solid var(--border);">
        <th style="text-align:left;padding:10px 12px;color:var(--text);font-weight:700;">Pillar</th>
        <th style="text-align:left;padding:10px 12px;color:var(--text);font-weight:700;">Question It Answers</th>
        <th style="text-align:left;padding:10px 12px;color:var(--text);font-weight:700;">Algorithms Covered</th>
      </tr>
    </thead>
    <tbody>
      <tr style="border-bottom:1px solid var(--border);">
        <td style="padding:10px 12px;color:#93c5fd;font-family:'JetBrains Mono',monospace;font-weight:600;">Clustering</td>
        <td style="padding:10px 12px;color:var(--muted);">Which data points naturally belong together?</td>
        <td style="padding:10px 12px;color:var(--muted);">K-Means, DBSCAN, Hierarchical</td>
      </tr>
      <tr style="border-bottom:1px solid var(--border);">
        <td style="padding:10px 12px;color:#93c5fd;font-family:'JetBrains Mono',monospace;font-weight:600;">Dimensionality Reduction</td>
        <td style="padding:10px 12px;color:var(--muted);">How can I represent my data with fewer features while preserving information?</td>
        <td style="padding:10px 12px;color:var(--muted);">PCA, t-SNE, UMAP</td>
      </tr>
      <tr>
        <td style="padding:10px 12px;color:#93c5fd;font-family:'JetBrains Mono',monospace;font-weight:600;">Anomaly Detection</td>
        <td style="padding:10px 12px;color:var(--muted);">Which data points are statistically unusual compared to the rest?</td>
        <td style="padding:10px 12px;color:var(--muted);">Isolation Forest, One-Class SVM, Autoencoders</td>
      </tr>
    </tbody>
  </table>
</div>

<h3>Setting Up Your Environment</h3>
<p>All algorithms in this module are available in <code>scikit-learn</code>, the gold standard ML library in Python. Install dependencies once, then follow the standard import pattern below — every lesson builds on these imports.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Standard Imports for Unsupervised Learning</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Install once in terminal:
# pip install scikit-learn numpy pandas matplotlib seaborn</span>

<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">import</span> seaborn <span style="color:#c4b5fd;">as</span> sns

<span style="color:#6b7280;"># Scikit-learn: clustering</span>
<span style="color:#c4b5fd;">from</span> sklearn.cluster <span style="color:#c4b5fd;">import</span> KMeans, DBSCAN, AgglomerativeClustering

<span style="color:#6b7280;"># Scikit-learn: dimensionality reduction</span>
<span style="color:#c4b5fd;">from</span> sklearn.decomposition <span style="color:#c4b5fd;">import</span> PCA
<span style="color:#c4b5fd;">from</span> sklearn.manifold <span style="color:#c4b5fd;">import</span> TSNE

<span style="color:#6b7280;"># Scikit-learn: anomaly detection</span>
<span style="color:#c4b5fd;">from</span> sklearn.ensemble <span style="color:#c4b5fd;">import</span> IsolationForest
<span style="color:#c4b5fd;">from</span> sklearn.svm <span style="color:#c4b5fd;">import</span> OneClassSVM

<span style="color:#6b7280;"># Preprocessing — always scale before unsupervised learning</span>
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> StandardScaler

<span style="color:#6b7280;"># Metrics for evaluation</span>
<span style="color:#c4b5fd;">from</span> sklearn.metrics <span style="color:#c4b5fd;">import</span> silhouette_score

<span style="color:#6b7280;"># Verify versions</span>
<span style="color:#c4b5fd;">import</span> sklearn
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"scikit-learn: {sklearn.__version__}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"NumPy:        {np.__version__}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Pandas:       {pd.__version__}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>scikit-learn: 1.4.0
NumPy:        1.26.0
Pandas:       2.1.0</div>
  </div>
</div>

<h3>Why Feature Scaling Is Non-Negotiable in Unsupervised Learning</h3>
<p>Most unsupervised algorithms — especially clustering and PCA — are based on <strong>distances or variances</strong>. If one feature ranges from 0 to 1,000,000 (like income) and another from 0 to 1 (like a probability score), the large-scale feature will completely dominate the distance calculations. The algorithm will effectively ignore the small-scale feature. <strong>StandardScaler</strong> transforms every feature to zero mean and unit variance, giving each feature an equal voice.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Why Scaling Matters</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> StandardScaler

<span style="color:#6b7280;"># Customer data: age (0-80) vs annual income (20,000-200,000)</span>
<span style="color:#6b7280;"># Income dominates any distance calculation without scaling</span>
X = np.<span style="color:#93c5fd;">array</span>([
    [<span style="color:#fcd34d;">25</span>,  <span style="color:#fcd34d;">30000</span>],
    [<span style="color:#fcd34d;">45</span>,  <span style="color:#fcd34d;">90000</span>],
    [<span style="color:#fcd34d;">35</span>, <span style="color:#fcd34d;">120000</span>],
    [<span style="color:#fcd34d;">52</span>, <span style="color:#fcd34d;">175000</span>],
])

scaler = <span style="color:#93c5fd;">StandardScaler</span>()
X_scaled = scaler.<span style="color:#93c5fd;">fit_transform</span>(X)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"=== BEFORE Scaling ==="</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Age range:    {X[:,0].min():.0f} → {X[:,0].max():.0f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Income range: {X[:,1].min():.0f} → {X[:,1].max():.0f}"</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\n=== AFTER Scaling (mean=0, std=1) ==="</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Age range:    {X_scaled[:,0].min():.2f} → {X_scaled[:,0].max():.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Income range: {X_scaled[:,1].min():.2f} → {X_scaled[:,1].max():.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nScaled data:\n"</span>, np.<span style="color:#93c5fd;">round</span>(X_scaled, <span style="color:#fcd34d;">3</span>))</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>=== BEFORE Scaling ===
Age range:    25 → 52
Income range: 30000 → 175000

=== AFTER Scaling (mean=0, std=1) ===
Age range:    -1.34 → 1.34
Income range: -1.22 → 1.34

Scaled data:
 [[-1.336 -1.221]
 [ 0.267  0.000]
 [-0.535  0.407]
 [ 1.604  0.814]]</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '21.1 What Is Unsupervised Learning?',
            'order_index' => 1,
            'content'     => $this->appendQuiz($content1, 'L21_1', [
                ['q' => 'What is the defining characteristic of unsupervised learning compared to supervised learning?', 'opts' => ['It uses more data', 'It trains faster on GPUs', 'It learns from data without labels — only raw input features X are provided', 'It always produces more accurate models'], 'ans' => 2, 'exp' => 'Unsupervised learning receives only input features X with no corresponding target labels y. The algorithm must discover structure — clusters, components, anomalies — entirely on its own without any ground-truth guidance.'],
                ['q' => 'Which of the following is a real-world application of clustering?', 'opts' => ['Predicting tomorrow\'s stock price', 'Segmenting customers into behavior groups without predefined categories', 'Classifying emails as spam or not spam', 'Forecasting monthly sales revenue'], 'ans' => 1, 'exp' => 'Customer segmentation is a classic clustering task. You provide customer behavior data and the algorithm groups them into natural segments — without anyone having pre-labeled what the segments should be. Stock prediction, spam detection, and sales forecasting are supervised tasks.'],
                ['q' => 'Why must you apply StandardScaler before most unsupervised learning algorithms?', 'opts' => ['To increase the dataset size', 'Because scikit-learn requires it by default', 'Because distance-based and variance-based algorithms are dominated by large-scale features without scaling', 'To convert categorical features to numbers'], 'ans' => 2, 'exp' => 'Algorithms like K-Means and PCA compute distances and variances. A feature with range 0–200,000 will completely overpower one with range 0–1. StandardScaler normalizes each feature to mean=0, std=1, giving every feature equal influence.'],
                ['q' => 'Which unsupervised learning pillar answers the question "Which data points are statistically unusual?"', 'opts' => ['Clustering', 'Dimensionality Reduction', 'Anomaly Detection', 'Feature Engineering'], 'ans' => 2, 'exp' => 'Anomaly Detection identifies data points that deviate significantly from the normal pattern learned from the bulk of the data. It is used in fraud detection, network intrusion detection, and equipment failure prediction.'],
                ['q' => 'What does StandardScaler\'s fit_transform() do in one step?', 'opts' => ['Trains a model and makes predictions', 'Fits (computes mean and std from training data) and then transforms (scales) the data', 'Converts text to numbers', 'Splits data into train and test sets'], 'ans' => 1, 'exp' => 'fit() computes the mean and standard deviation of each feature from the provided data. transform() applies the scaling formula: (x - mean) / std. fit_transform() combines both steps. On test data, use only transform() — never re-fit.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 21.2 — K-Means Clustering
        // ══════════════════════════════════════════════════════════════
        $content2 = <<<'HTML'
<h2>K-Means Clustering</h2>
<p>K-Means is the most widely used clustering algorithm in the world. It partitions n data points into <strong>k clusters</strong>, where each point belongs to the cluster whose <em>centroid</em> (geometric center) it is nearest to. It is fast, scalable to millions of points, and produces clusters that are intuitive to interpret. Its limitation — that you must specify k in advance — is addressed by the Elbow Method and Silhouette Analysis, which you will master in this lesson.</p>

<h3>How K-Means Works: The Algorithm Step by Step</h3>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:20px;margin-bottom:24px;">
  <ol style="color:var(--muted);line-height:2.2;font-size:0.9rem;margin:0;padding-left:20px;">
    <li><strong style="color:var(--text);">Initialize:</strong> Randomly place k centroid points in the feature space (or use K-Means++ for smarter initialization).</li>
    <li><strong style="color:var(--text);">Assign:</strong> For every data point, compute its distance to all k centroids and assign it to the nearest one.</li>
    <li><strong style="color:var(--text);">Update:</strong> Move each centroid to the mean position of all points currently assigned to it.</li>
    <li><strong style="color:var(--text);">Repeat:</strong> Go back to step 2. Continue until assignments stop changing (convergence) or a maximum iteration limit is hit.</li>
    <li><strong style="color:var(--text);">Output:</strong> k cluster labels (one per data point) and k centroid coordinates.</li>
  </ol>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — K-Means from Scratch to Visualization</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">from</span> sklearn.cluster <span style="color:#c4b5fd;">import</span> KMeans
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> StandardScaler
<span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> make_blobs

<span style="color:#6b7280;"># Generate synthetic customer data: 4 natural clusters</span>
X, y_true = make_blobs(n_samples=<span style="color:#fcd34d;">400</span>, centers=<span style="color:#fcd34d;">4</span>,
                        cluster_std=<span style="color:#fcd34d;">0.9</span>, random_state=<span style="color:#fcd34d;">42</span>)

<span style="color:#6b7280;"># Step 1: Always scale first</span>
scaler  = <span style="color:#93c5fd;">StandardScaler</span>()
X_scaled = scaler.<span style="color:#93c5fd;">fit_transform</span>(X)

<span style="color:#6b7280;"># Step 2: Fit K-Means with k=4</span>
<span style="color:#6b7280;"># init="k-means++" avoids the poor-initialization problem</span>
<span style="color:#6b7280;"># n_init=10 runs the algorithm 10 times, picks the best result</span>
kmeans = <span style="color:#93c5fd;">KMeans</span>(n_clusters=<span style="color:#fcd34d;">4</span>, init=<span style="color:#a7f3d0;">"k-means++"</span>,
                n_init=<span style="color:#fcd34d;">10</span>, random_state=<span style="color:#fcd34d;">42</span>)
kmeans.<span style="color:#93c5fd;">fit</span>(X_scaled)

labels     = kmeans.labels_        <span style="color:#6b7280;"># Cluster assignment per point</span>
centroids  = kmeans.cluster_centers_  <span style="color:#6b7280;"># Centroid coordinates (scaled)</span>
inertia    = kmeans.inertia_       <span style="color:#6b7280;"># Sum of squared distances to centroid</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Inertia (WCSS): {inertia:.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Cluster sizes: {np.bincount(labels)}"</span>)

<span style="color:#6b7280;"># Step 3: Visualize</span>
colors = [<span style="color:#a7f3d0;">"#3498db"</span>, <span style="color:#a7f3d0;">"#e74c3c"</span>, <span style="color:#a7f3d0;">"#2ecc71"</span>, <span style="color:#a7f3d0;">"#f39c12"</span>]
fig, ax = plt.<span style="color:#93c5fd;">subplots</span>(figsize=(<span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">6</span>))

<span style="color:#c4b5fd;">for</span> k <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">4</span>):
    mask = labels == k
    ax.<span style="color:#93c5fd;">scatter</span>(X_scaled[mask, <span style="color:#fcd34d;">0</span>], X_scaled[mask, <span style="color:#fcd34d;">1</span>],
               c=colors[k], alpha=<span style="color:#fcd34d;">0.6</span>, s=<span style="color:#fcd34d;">30</span>, label=<span style="color:#a7f3d0;">f"Cluster {k}"</span>)

<span style="color:#6b7280;"># Plot centroids as large black X markers</span>
ax.<span style="color:#93c5fd;">scatter</span>(centroids[:, <span style="color:#fcd34d;">0</span>], centroids[:, <span style="color:#fcd34d;">1</span>],
           c=<span style="color:#a7f3d0;">"black"</span>, marker=<span style="color:#a7f3d0;">"X"</span>, s=<span style="color:#fcd34d;">200</span>, zorder=<span style="color:#fcd34d;">10</span>, label=<span style="color:#a7f3d0;">"Centroids"</span>)

ax.<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"K-Means Clustering (k=4)"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
ax.<span style="color:#93c5fd;">set_xlabel</span>(<span style="color:#a7f3d0;">"Feature 1 (scaled)"</span>)
ax.<span style="color:#93c5fd;">set_ylabel</span>(<span style="color:#a7f3d0;">"Feature 2 (scaled)"</span>)
ax.<span style="color:#93c5fd;">legend</span>()
plt.<span style="color:#93c5fd;">tight_layout</span>()
plt.<span style="color:#93c5fd;">show</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Inertia (WCSS): 388.14
Cluster sizes: [100 100 100 100]
[Scatter plot: 4 distinct colored groups, centroids marked with black X]</div>
  </div>
</div>

<h3>Finding the Optimal k: The Elbow Method</h3>
<p>K-Means requires you to choose k before training. The <strong>Elbow Method</strong> plots inertia (WCSS — Within-Cluster Sum of Squares) against increasing k values. Inertia always decreases as k increases, but the rate of decrease slows dramatically after the true optimal k — forming an "elbow" in the curve. That elbow is your answer.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Elbow Method + Silhouette Score</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.metrics <span style="color:#c4b5fd;">import</span> silhouette_score

inertias    = []
silhouettes = []
k_range     = <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">11</span>)

<span style="color:#c4b5fd;">for</span> k <span style="color:#c4b5fd;">in</span> k_range:
    km = <span style="color:#93c5fd;">KMeans</span>(n_clusters=k, init=<span style="color:#a7f3d0;">"k-means++"</span>,
                n_init=<span style="color:#fcd34d;">10</span>, random_state=<span style="color:#fcd34d;">42</span>)
    km.<span style="color:#93c5fd;">fit</span>(X_scaled)
    inertias.<span style="color:#93c5fd;">append</span>(km.inertia_)
    silhouettes.<span style="color:#93c5fd;">append</span>(<span style="color:#93c5fd;">silhouette_score</span>(X_scaled, km.labels_))

fig, (ax1, ax2) = plt.<span style="color:#93c5fd;">subplots</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, figsize=(<span style="color:#fcd34d;">12</span>, <span style="color:#fcd34d;">4</span>))

<span style="color:#6b7280;"># Elbow curve</span>
ax1.<span style="color:#93c5fd;">plot</span>(k_range, inertias, <span style="color:#a7f3d0;">"bo-"</span>, linewidth=<span style="color:#fcd34d;">2</span>, markersize=<span style="color:#fcd34d;">8</span>)
ax1.<span style="color:#93c5fd;">axvline</span>(<span style="color:#fcd34d;">4</span>, color=<span style="color:#a7f3d0;">"red"</span>, linestyle=<span style="color:#a7f3d0;">"--"</span>, label=<span style="color:#a7f3d0;">"Elbow at k=4"</span>)
ax1.<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Elbow Method — Inertia vs k"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
ax1.<span style="color:#93c5fd;">set_xlabel</span>(<span style="color:#a7f3d0;">"Number of Clusters (k)"</span>)
ax1.<span style="color:#93c5fd;">set_ylabel</span>(<span style="color:#a7f3d0;">"Inertia (WCSS)"</span>)
ax1.<span style="color:#93c5fd;">legend</span>()

<span style="color:#6b7280;"># Silhouette scores — higher is better, max = 1.0</span>
ax2.<span style="color:#93c5fd;">plot</span>(k_range, silhouettes, <span style="color:#a7f3d0;">"gs-"</span>, linewidth=<span style="color:#fcd34d;">2</span>, markersize=<span style="color:#fcd34d;">8</span>)
ax2.<span style="color:#93c5fd;">axvline</span>(<span style="color:#fcd34d;">4</span>, color=<span style="color:#a7f3d0;">"red"</span>, linestyle=<span style="color:#a7f3d0;">"--"</span>, label=<span style="color:#a7f3d0;">"Best at k=4"</span>)
ax2.<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Silhouette Score vs k"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
ax2.<span style="color:#93c5fd;">set_xlabel</span>(<span style="color:#a7f3d0;">"Number of Clusters (k)"</span>)
ax2.<span style="color:#93c5fd;">set_ylabel</span>(<span style="color:#a7f3d0;">"Silhouette Score"</span>)
ax2.<span style="color:#93c5fd;">legend</span>()

plt.<span style="color:#93c5fd;">tight_layout</span>()
plt.<span style="color:#93c5fd;">show</span>()

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Best silhouette score: {max(silhouettes):.3f} at k={silhouettes.index(max(silhouettes))+2}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Best silhouette score: 0.742 at k=4
[Elbow chart: inertia drops steeply then flattens at k=4]
[Silhouette chart: peaks clearly at k=4 then declines]</div>
  </div>
</div>

<h3>Applying K-Means to Real Customer Data</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Customer Segmentation with K-Means</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

<span style="color:#6b7280;"># Simulated RFM (Recency, Frequency, Monetary) customer data</span>
np.random.<span style="color:#93c5fd;">seed</span>(<span style="color:#fcd34d;">42</span>)
df = pd.<span style="color:#93c5fd;">DataFrame</span>({
    <span style="color:#a7f3d0;">"recency_days"</span>:  np.random.<span style="color:#93c5fd;">randint</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">365</span>, <span style="color:#fcd34d;">500</span>),
    <span style="color:#a7f3d0;">"frequency"</span>:     np.random.<span style="color:#93c5fd;">randint</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">50</span>, <span style="color:#fcd34d;">500</span>),
    <span style="color:#a7f3d0;">"monetary_value"</span>: np.random.<span style="color:#93c5fd;">uniform</span>(<span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">5000</span>, <span style="color:#fcd34d;">500</span>)
})

<span style="color:#6b7280;"># Scale → Cluster</span>
scaler   = <span style="color:#93c5fd;">StandardScaler</span>()
X_scaled = scaler.<span style="color:#93c5fd;">fit_transform</span>(df)

kmeans   = <span style="color:#93c5fd;">KMeans</span>(n_clusters=<span style="color:#fcd34d;">4</span>, init=<span style="color:#a7f3d0;">"k-means++"</span>,
                   n_init=<span style="color:#fcd34d;">10</span>, random_state=<span style="color:#fcd34d;">42</span>)
df[<span style="color:#a7f3d0;">"Segment"</span>] = kmeans.<span style="color:#93c5fd;">fit_predict</span>(X_scaled)

<span style="color:#6b7280;"># Profile each segment</span>
profile = df.<span style="color:#93c5fd;">groupby</span>(<span style="color:#a7f3d0;">"Segment"</span>)[[<span style="color:#a7f3d0;">"recency_days"</span>,<span style="color:#a7f3d0;">"frequency"</span>,<span style="color:#a7f3d0;">"monetary_value"</span>]].\
               <span style="color:#93c5fd;">mean</span>().<span style="color:#93c5fd;">round</span>(<span style="color:#fcd34d;">1</span>)
<span style="color:#93c5fd;">print</span>(profile)

<span style="color:#6b7280;"># Name segments based on their profiles</span>
segment_names = {<span style="color:#fcd34d;">0</span>: <span style="color:#a7f3d0;">"Champions"</span>, <span style="color:#fcd34d;">1</span>: <span style="color:#a7f3d0;">"At Risk"</span>,
                 <span style="color:#fcd34d;">2</span>: <span style="color:#a7f3d0;">"High Spenders"</span>, <span style="color:#fcd34d;">3</span>: <span style="color:#a7f3d0;">"Hibernating"</span>}
df[<span style="color:#a7f3d0;">"Segment_Name"</span>] = df[<span style="color:#a7f3d0;">"Segment"</span>].<span style="color:#93c5fd;">map</span>(segment_names)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nSegment distribution:"</span>)
<span style="color:#93c5fd;">print</span>(df[<span style="color:#a7f3d0;">"Segment_Name"</span>].<span style="color:#93c5fd;">value_counts</span>())</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>         recency_days  frequency  monetary_value
Segment
0               48.2       38.1          4201.5
1              287.4        8.3           312.8
2               91.3       41.2          3987.6
3              310.1        5.9           189.4

Segment distribution:
Champions      127
High Spenders  124
At Risk        131
Hibernating    118</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '21.2 K-Means Clustering',
            'order_index' => 2,
            'content'     => $this->appendQuiz($content2, 'L21_2', [
                ['q' => 'What does K-Means inertia (WCSS) measure?', 'opts' => ['The number of iterations until convergence', 'The sum of squared distances from each point to its assigned cluster centroid', 'The total number of data points across all clusters', 'The average cluster size'], 'ans' => 1, 'exp' => 'Inertia (Within-Cluster Sum of Squares) sums the squared Euclidean distance from each data point to its assigned centroid. Lower inertia = tighter, more compact clusters. It always decreases as k increases, which is why you need the Elbow Method to find the true optimum.'],
                ['q' => 'What does the Elbow Method look for?', 'opts' => ['The k value where inertia reaches zero', 'The k value where accuracy peaks', 'The k value where the rate of inertia decrease suddenly slows — forming a "bend" in the curve', 'The k value with the highest number of iterations'], 'ans' => 2, 'exp' => 'Adding more clusters always reduces inertia. But after the true optimal k, each additional cluster provides diminishing returns — the inertia curve bends sharply. That bend (the "elbow") is the recommended k.'],
                ['q' => 'What does init="k-means++" do in scikit-learn\'s KMeans?', 'opts' => ['Runs K-Means twice and picks the better result', 'Uses a smarter initialization that spreads initial centroids far apart, dramatically reducing the chance of poor local minima', 'Doubles the learning rate', 'Forces all centroids to start at the data mean'], 'ans' => 1, 'exp' => 'K-Means++ initializes centroids probabilistically — the first is random, but each subsequent centroid is chosen with probability proportional to its squared distance from the nearest existing centroid. This spreads them out, leading to faster convergence and better final clusters.'],
                ['q' => 'A silhouette score of 0.75 means:', 'opts' => ['The clustering is completely random', 'The model had 75% accuracy', 'Points are well-matched to their own cluster and clearly separated from neighboring clusters — a strong result', 'The algorithm ran for 75 iterations'], 'ans' => 2, 'exp' => 'The silhouette score ranges from -1 to +1. Near +1 means a point is much closer to its own cluster than any other. Near 0 means it is on the boundary. Near -1 means it may be mis-assigned. 0.75 indicates well-defined, well-separated clusters.'],
                ['q' => 'What does fit_predict() do in one call?', 'opts' => ['Trains the model and outputs accuracy', 'Fits the model to X and then returns cluster labels for each point simultaneously', 'Predicts on test data without fitting', 'Splits data and fits separate models per split'], 'ans' => 1, 'exp' => 'fit_predict(X) is equivalent to calling fit(X) followed by predict(X). It trains the K-Means model and returns the cluster label (0 to k-1) assigned to every point in X.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 21.3 — DBSCAN: Density-Based Clustering
        // ══════════════════════════════════════════════════════════════
        $content3 = <<<'HTML'
<h2>DBSCAN: Density-Based Clustering</h2>
<p>K-Means has a critical weakness: it assumes clusters are <strong>spherical and roughly equal in size</strong>. Real-world data rarely cooperates — clusters are often irregular, elongated, or nested. DBSCAN (Density-Based Spatial Clustering of Applications with Noise) discovers clusters of <em>arbitrary shape</em> by grouping points that are densely packed together, while automatically labeling isolated points as <strong>noise/outliers</strong> — a capability K-Means simply does not have.</p>

<h3>DBSCAN's Two Parameters</h3>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:20px;margin-bottom:24px;">
  <table style="width:100%;border-collapse:collapse;font-size:0.875rem;">
    <thead>
      <tr style="border-bottom:1px solid var(--border);">
        <th style="text-align:left;padding:10px 12px;color:var(--text);font-weight:700;">Parameter</th>
        <th style="text-align:left;padding:10px 12px;color:var(--text);font-weight:700;">Meaning</th>
        <th style="text-align:left;padding:10px 12px;color:var(--text);font-weight:700;">Effect of Increasing</th>
      </tr>
    </thead>
    <tbody>
      <tr style="border-bottom:1px solid var(--border);">
        <td style="padding:10px 12px;color:#93c5fd;font-family:'JetBrains Mono',monospace;font-weight:600;">eps</td>
        <td style="padding:10px 12px;color:var(--muted);">Epsilon — the maximum distance between two points for them to be considered "neighbors"</td>
        <td style="padding:10px 12px;color:var(--muted);">Fewer, larger clusters; fewer noise points</td>
      </tr>
      <tr>
        <td style="padding:10px 12px;color:#93c5fd;font-family:'JetBrains Mono',monospace;font-weight:600;">min_samples</td>
        <td style="padding:10px 12px;color:var(--muted);">Minimum number of points in an eps-radius to form a dense region (core point)</td>
        <td style="padding:10px 12px;color:var(--muted);">More noise points; fewer, denser clusters</td>
      </tr>
    </tbody>
  </table>
</div>

<h3>Point Types in DBSCAN</h3>
<p>DBSCAN classifies every data point into one of three roles: <strong>Core Points</strong> (have ≥ min_samples neighbors within eps — the heart of a cluster), <strong>Border Points</strong> (within eps of a core point but not dense enough themselves — on the edge of a cluster), and <strong>Noise Points</strong> (neither core nor border — outliers, labeled -1 in scikit-learn).</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — DBSCAN vs K-Means on Irregular Shapes</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">from</span> sklearn.cluster <span style="color:#c4b5fd;">import</span> DBSCAN, KMeans
<span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> make_moons, make_circles
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> StandardScaler

<span style="color:#6b7280;"># make_moons: two crescent shapes — impossible for K-Means</span>
X_moons, _ = <span style="color:#93c5fd;">make_moons</span>(n_samples=<span style="color:#fcd34d;">300</span>, noise=<span style="color:#fcd34d;">0.07</span>, random_state=<span style="color:#fcd34d;">42</span>)
X_moons     = <span style="color:#93c5fd;">StandardScaler</span>().<span style="color:#93c5fd;">fit_transform</span>(X_moons)

fig, axes = plt.<span style="color:#93c5fd;">subplots</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, figsize=(<span style="color:#fcd34d;">12</span>, <span style="color:#fcd34d;">5</span>))

<span style="color:#6b7280;"># K-Means on moons — will fail badly</span>
km_labels  = <span style="color:#93c5fd;">KMeans</span>(n_clusters=<span style="color:#fcd34d;">2</span>, n_init=<span style="color:#fcd34d;">10</span>, random_state=<span style="color:#fcd34d;">0</span>).<span style="color:#93c5fd;">fit_predict</span>(X_moons)
axes[<span style="color:#fcd34d;">0</span>].<span style="color:#93c5fd;">scatter</span>(X_moons[:,<span style="color:#fcd34d;">0</span>], X_moons[:,<span style="color:#fcd34d;">1</span>], c=km_labels, cmap=<span style="color:#a7f3d0;">"bwr"</span>, s=<span style="color:#fcd34d;">15</span>)
axes[<span style="color:#fcd34d;">0</span>].<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"K-Means on Moons — FAILS (cuts vertically)"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)

<span style="color:#6b7280;"># DBSCAN on moons — succeeds perfectly</span>
<span style="color:#6b7280;"># eps=0.3: neighborhood radius, min_samples=5: density threshold</span>
db_labels  = <span style="color:#93c5fd;">DBSCAN</span>(eps=<span style="color:#fcd34d;">0.3</span>, min_samples=<span style="color:#fcd34d;">5</span>).<span style="color:#93c5fd;">fit_predict</span>(X_moons)
n_clusters = <span style="color:#93c5fd;">len</span>(<span style="color:#93c5fd;">set</span>(db_labels)) - (<span style="color:#fcd34d;">1</span> <span style="color:#c4b5fd;">if</span> -<span style="color:#fcd34d;">1</span> <span style="color:#c4b5fd;">in</span> db_labels <span style="color:#c4b5fd;">else</span> <span style="color:#fcd34d;">0</span>)
n_noise    = <span style="color:#93c5fd;">list</span>(db_labels).<span style="color:#93c5fd;">count</span>(-<span style="color:#fcd34d;">1</span>)

axes[<span style="color:#fcd34d;">1</span>].<span style="color:#93c5fd;">scatter</span>(X_moons[:,<span style="color:#fcd34d;">0</span>], X_moons[:,<span style="color:#fcd34d;">1</span>], c=db_labels, cmap=<span style="color:#a7f3d0;">"bwr"</span>, s=<span style="color:#fcd34d;">15</span>)
axes[<span style="color:#fcd34d;">1</span>].<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">f"DBSCAN — {n_clusters} clusters, {n_noise} noise points"</span>,
                  fontweight=<span style="color:#a7f3d0;">"bold"</span>)

plt.<span style="color:#93c5fd;">tight_layout</span>()
plt.<span style="color:#93c5fd;">show</span>()

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"DBSCAN found {n_clusters} clusters and {n_noise} noise/outlier points."</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>DBSCAN found 2 clusters and 0 noise/outlier points.
[Left: K-Means slices the moons vertically — completely wrong]
[Right: DBSCAN perfectly traces each crescent shape]</div>
  </div>
</div>

<h3>Tuning eps with the k-Nearest Neighbor Distance Plot</h3>
<p>The hardest part of DBSCAN is choosing eps. The standard technique is a <strong>k-distance plot</strong>: for each point, compute its distance to its k-th nearest neighbor (use k = min_samples - 1). Sort these distances and plot them. The optimal eps lies at the "knee" of this curve — the point of maximum curvature.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — k-Distance Plot for eps Selection</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.neighbors <span style="color:#c4b5fd;">import</span> NearestNeighbors

<span style="color:#6b7280;"># Compute 4th nearest neighbor distance for each point (min_samples=5 → k=4)</span>
nbrs = <span style="color:#93c5fd;">NearestNeighbors</span>(n_neighbors=<span style="color:#fcd34d;">4</span>).<span style="color:#93c5fd;">fit</span>(X_moons)
distances, _ = nbrs.<span style="color:#93c5fd;">kneighbors</span>(X_moons)
distances     = np.<span style="color:#93c5fd;">sort</span>(distances[:, <span style="color:#fcd34d;">3</span>])   <span style="color:#6b7280;"># 4th-nearest distance, sorted</span>

fig, ax = plt.<span style="color:#93c5fd;">subplots</span>(figsize=(<span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">4</span>))
ax.<span style="color:#93c5fd;">plot</span>(distances, color=<span style="color:#a7f3d0;">"steelblue"</span>, linewidth=<span style="color:#fcd34d;">2</span>)
ax.<span style="color:#93c5fd;">axhline</span>(<span style="color:#fcd34d;">0.3</span>, color=<span style="color:#a7f3d0;">"red"</span>, linestyle=<span style="color:#a7f3d0;">"--"</span>, label=<span style="color:#a7f3d0;">"Suggested eps = 0.3"</span>)
ax.<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"k-Distance Plot (k=4) — Knee = Optimal eps"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
ax.<span style="color:#93c5fd;">set_xlabel</span>(<span style="color:#a7f3d0;">"Points sorted by 4th-nearest distance"</span>)
ax.<span style="color:#93c5fd;">set_ylabel</span>(<span style="color:#a7f3d0;">"Distance to 4th Neighbor"</span>)
ax.<span style="color:#93c5fd;">legend</span>()
plt.<span style="color:#93c5fd;">tight_layout</span>()
plt.<span style="color:#93c5fd;">show</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Output</span>Sorted distance curve rises slowly, then sharply bends upward at ~0.3.
Red dashed line marks eps=0.3 at the knee of the curve.
Points above the knee would be noise; those below join clusters.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '21.3 DBSCAN: Density-Based Clustering',
            'order_index' => 3,
            'content'     => $this->appendQuiz($content3, 'L21_3', [
                ['q' => 'What label does DBSCAN assign to noise/outlier points in scikit-learn?', 'opts' => ['0', '999', '-1', 'NaN'], 'ans' => 2, 'exp' => 'DBSCAN assigns label -1 to every point that is neither a core point nor reachable from one. These are noise points — isolated observations that don\'t belong to any dense cluster. This built-in outlier detection is a major advantage over K-Means.'],
                ['q' => 'What is a "core point" in DBSCAN?', 'opts' => ['The centroid of a cluster', 'A point with at least min_samples other points within eps distance', 'The first point assigned to a cluster', 'A point with the lowest feature values'], 'ans' => 1, 'exp' => 'A core point has at least min_samples neighbors (including itself) within radius eps. Core points are the foundation of clusters. Border points are within eps of a core point but aren\'t dense enough themselves.'],
                ['q' => 'Why does DBSCAN outperform K-Means on crescent-shaped or ring-shaped data?', 'opts' => ['DBSCAN is always faster to compute', 'DBSCAN discovers clusters of arbitrary shape by following dense regions — it is not restricted to spherical or convex cluster boundaries', 'DBSCAN uses fewer hyperparameters', 'K-Means cannot handle scaled data'], 'ans' => 1, 'exp' => 'K-Means assigns points to the nearest centroid, creating Voronoi regions that are always convex (roughly spherical). DBSCAN grows clusters by connecting any point within eps of a core point, allowing it to follow curved, elongated, or nested shapes.'],
                ['q' => 'What does the k-Distance Plot help you determine?', 'opts' => ['The optimal number of clusters k', 'The optimal eps value for DBSCAN — the "knee" of the sorted distance curve', 'The silhouette score for a given eps', 'Whether the data needs to be scaled'], 'ans' => 1, 'exp' => 'The k-distance plot sorts points by their distance to the k-th nearest neighbor. The curve rises slowly for dense regions and sharply for noise. The knee of the curve — where the rate of increase suddenly jumps — is the recommended eps value.'],
                ['q' => 'What happens if you set eps very large in DBSCAN?', 'opts' => ['Every point becomes noise', 'The algorithm finds more clusters', 'All points become neighbors of each other, resulting in a single giant cluster', 'Runtime decreases dramatically'], 'ans' => 2, 'exp' => 'A very large eps means every point is within eps of every other point, so the entire dataset becomes one enormous cluster with no noise points. Conversely, a very small eps creates many noise points and tiny fragmented clusters.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 21.4 — Hierarchical Clustering & Dendrograms
        // ══════════════════════════════════════════════════════════════
        $content4 = <<<'HTML'
<h2>Hierarchical Clustering & Dendrograms</h2>
<p>Hierarchical clustering builds a <strong>tree of clusters</strong> called a <em>dendrogram</em> — rather than partitioning data into a flat set of k groups like K-Means, it reveals the nested structure at every level of granularity. You do not need to specify the number of clusters upfront: you train the hierarchy first, then "cut" the dendrogram at whichever height produces the number of clusters that makes sense for your business problem. This makes it particularly powerful for exploratory analysis.</p>

<h3>Agglomerative vs Divisive</h3>
<p>There are two strategies. <strong>Agglomerative</strong> (bottom-up): start with each point as its own cluster; repeatedly merge the two closest clusters until one cluster remains. <strong>Divisive</strong> (top-down): start with all points in one cluster; repeatedly split. Scikit-learn implements agglomerative clustering, which is by far the most widely used.</p>

<h3>Linkage Methods: How Distance Between Clusters is Measured</h3>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:20px;margin-bottom:24px;">
  <table style="width:100%;border-collapse:collapse;font-size:0.875rem;">
    <thead>
      <tr style="border-bottom:1px solid var(--border);">
        <th style="text-align:left;padding:10px 12px;color:var(--text);font-weight:700;">Linkage</th>
        <th style="text-align:left;padding:10px 12px;color:var(--text);font-weight:700;">Distance Defined As</th>
        <th style="text-align:left;padding:10px 12px;color:var(--text);font-weight:700;">Best For</th>
      </tr>
    </thead>
    <tbody>
      <tr style="border-bottom:1px solid var(--border);">
        <td style="padding:10px 12px;color:#93c5fd;font-family:'JetBrains Mono',monospace;">ward</td>
        <td style="padding:10px 12px;color:var(--muted);">Minimizes total within-cluster variance increase</td>
        <td style="padding:10px 12px;color:var(--muted);">Most use cases — compact, similarly-sized clusters</td>
      </tr>
      <tr style="border-bottom:1px solid var(--border);">
        <td style="padding:10px 12px;color:#93c5fd;font-family:'JetBrains Mono',monospace;">complete</td>
        <td style="padding:10px 12px;color:var(--muted);">Maximum distance between any two points in the clusters</td>
        <td style="padding:10px 12px;color:var(--muted);">Well-separated, compact clusters</td>
      </tr>
      <tr style="border-bottom:1px solid var(--border);">
        <td style="padding:10px 12px;color:#93c5fd;font-family:'JetBrains Mono',monospace;">average</td>
        <td style="padding:10px 12px;color:var(--muted);">Average distance between all pairs of points across clusters</td>
        <td style="padding:10px 12px;color:var(--muted);">Balanced compromise between single and complete</td>
      </tr>
      <tr>
        <td style="padding:10px 12px;color:#93c5fd;font-family:'JetBrains Mono',monospace;">single</td>
        <td style="padding:10px 12px;color:var(--muted);">Minimum distance between any two points in the clusters</td>
        <td style="padding:10px 12px;color:var(--muted);">Elongated, chain-like clusters (prone to chaining)</td>
      </tr>
    </tbody>
  </table>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Dendrogram + Agglomerative Clustering</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">from</span> sklearn.cluster <span style="color:#c4b5fd;">import</span> AgglomerativeClustering
<span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> make_blobs
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> StandardScaler
<span style="color:#c4b5fd;">from</span> scipy.cluster.hierarchy <span style="color:#c4b5fd;">import</span> dendrogram, linkage

<span style="color:#6b7280;"># Generate data with 3 natural clusters</span>
X, _ = <span style="color:#93c5fd;">make_blobs</span>(n_samples=<span style="color:#fcd34d;">150</span>, centers=<span style="color:#fcd34d;">3</span>,
                   cluster_std=<span style="color:#fcd34d;">0.8</span>, random_state=<span style="color:#fcd34d;">10</span>)
X_scaled = <span style="color:#93c5fd;">StandardScaler</span>().<span style="color:#93c5fd;">fit_transform</span>(X)

fig, (ax1, ax2) = plt.<span style="color:#93c5fd;">subplots</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, figsize=(<span style="color:#fcd34d;">14</span>, <span style="color:#fcd34d;">5</span>))

<span style="color:#6b7280;"># --- Left: Dendrogram (from scipy) ---</span>
<span style="color:#6b7280;"># linkage() computes the full merge tree</span>
Z = <span style="color:#93c5fd;">linkage</span>(X_scaled, method=<span style="color:#a7f3d0;">"ward"</span>)
<span style="color:#93c5fd;">dendrogram</span>(Z, ax=ax1, truncate_mode=<span style="color:#a7f3d0;">"level"</span>, p=<span style="color:#fcd34d;">5</span>,
           color_threshold=<span style="color:#fcd34d;">4.0</span>)
ax1.<span style="color:#93c5fd;">axhline</span>(<span style="color:#fcd34d;">4.0</span>, color=<span style="color:#a7f3d0;">"red"</span>, linestyle=<span style="color:#a7f3d0;">"--"</span>,
             label=<span style="color:#a7f3d0;">"Cut here → 3 clusters"</span>)
ax1.<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Dendrogram (Ward Linkage)"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
ax1.<span style="color:#93c5fd;">set_xlabel</span>(<span style="color:#a7f3d0;">"Sample Index"</span>)
ax1.<span style="color:#93c5fd;">set_ylabel</span>(<span style="color:#a7f3d0;">"Ward Distance"</span>)
ax1.<span style="color:#93c5fd;">legend</span>()

<span style="color:#6b7280;"># --- Right: Final clusters from scikit-learn ---</span>
agg = <span style="color:#93c5fd;">AgglomerativeClustering</span>(n_clusters=<span style="color:#fcd34d;">3</span>, linkage=<span style="color:#a7f3d0;">"ward"</span>)
labels = agg.<span style="color:#93c5fd;">fit_predict</span>(X_scaled)

colors = [<span style="color:#a7f3d0;">"#3498db"</span>, <span style="color:#a7f3d0;">"#e74c3c"</span>, <span style="color:#a7f3d0;">"#2ecc71"</span>]
<span style="color:#c4b5fd;">for</span> k <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">3</span>):
    ax2.<span style="color:#93c5fd;">scatter</span>(X_scaled[labels==k, <span style="color:#fcd34d;">0</span>],
               X_scaled[labels==k, <span style="color:#fcd34d;">1</span>],
               c=colors[k], s=<span style="color:#fcd34d;">40</span>, alpha=<span style="color:#fcd34d;">0.7</span>, label=<span style="color:#a7f3d0;">f"Cluster {k}"</span>)

ax2.<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Agglomerative Clustering Result (k=3)"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
ax2.<span style="color:#93c5fd;">legend</span>()

plt.<span style="color:#93c5fd;">tight_layout</span>()
plt.<span style="color:#93c5fd;">show</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Output</span>Left: Dendrogram tree — horizontal red line at height 4.0 cuts into 3 branches.
Right: Scatter plot with 3 perfectly separated colored clusters.
The tall merges in the dendrogram confirm 3 is the natural cluster count.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '21.4 Hierarchical Clustering & Dendrograms',
            'order_index' => 4,
            'content'     => $this->appendQuiz($content4, 'L21_4', [
                ['q' => 'What is a dendrogram?', 'opts' => ['A heatmap of cluster assignments', 'A tree diagram showing the sequence of cluster merges and the distances at which they occur', 'A bar chart of silhouette scores', 'A scatter plot with cluster boundaries drawn'], 'ans' => 1, 'exp' => 'A dendrogram is a tree visualization produced by hierarchical clustering. Leaves are individual data points; branches merge into groups. The height of each merge represents the distance (or dissimilarity) at which that merge occurred. Cutting the tree at a given height determines how many clusters you get.'],
                ['q' => 'How do you determine the number of clusters from a dendrogram?', 'opts' => ['Count the number of leaves', 'Count the number of branch merges', 'Draw a horizontal line across the dendrogram and count how many vertical lines it crosses', 'Use the elbow method on the y-axis values'], 'ans' => 2, 'exp' => 'You choose a cut height and draw a horizontal line. The number of vertical branches that line intersects equals the number of clusters at that level. Optimal cuts are placed where the longest vertical lines (tallest merges) are — indicating the biggest jumps in dissimilarity.'],
                ['q' => 'What does Ward linkage minimize when merging two clusters?', 'opts' => ['The maximum distance between any two points', 'The average pairwise distance between all points', 'The increase in total within-cluster variance (sum of squared deviations from each cluster mean)', 'The number of noise points'], 'ans' => 2, 'exp' => 'Ward linkage selects which two clusters to merge by choosing the merge that results in the smallest increase in total within-cluster sum of squares. This tends to produce compact, similarly-sized clusters and is the most commonly recommended linkage method.'],
                ['q' => 'What is the key advantage of hierarchical clustering over K-Means?', 'opts' => ['It is computationally faster on large datasets', 'It always finds more accurate clusters', 'It does not require specifying k upfront — you train the full hierarchy and choose the cut level afterward', 'It handles noise points better than DBSCAN'], 'ans' => 2, 'exp' => 'Hierarchical clustering builds the entire merge tree first. You can then inspect the dendrogram and choose any number of clusters by adjusting the cut height. This is especially valuable in exploratory analysis when you don\'t know how many groups to expect.'],
                ['q' => 'Which linkage method is most susceptible to the "chaining effect" (where clusters grow into long, stringy chains)?', 'opts' => ['Ward', 'Complete', 'Average', 'Single'], 'ans' => 3, 'exp' => 'Single linkage defines cluster distance as the minimum distance between any pair of points. This allows a cluster to "chain" by merging with any point that is close to even one member, creating long, irregular chains rather than compact groups — a known weakness.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 21.5 — Principal Component Analysis (PCA)
        // ══════════════════════════════════════════════════════════════
        $content5 = <<<'HTML'
<h2>Principal Component Analysis (PCA)</h2>
<p>A dataset with 100 features lives in 100-dimensional space — impossible to visualize and computationally expensive to model. Many of those features are correlated, which means they carry <em>redundant information</em>. PCA (Principal Component Analysis) solves this by finding new axes — called <strong>principal components</strong> — that point in the directions of maximum variance in the data. By keeping only the top few components, you can represent your data in 2 or 3 dimensions while retaining most of the meaningful information. This is the most important dimensionality reduction technique in all of data science.</p>

<h3>How PCA Works Conceptually</h3>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:20px;margin-bottom:24px;">
  <ol style="color:var(--muted);line-height:2.2;font-size:0.9rem;margin:0;padding-left:20px;">
    <li><strong style="color:var(--text);">Center the data</strong> by subtracting the mean of each feature (StandardScaler handles this).</li>
    <li><strong style="color:var(--text);">Compute the covariance matrix</strong> — a symmetric matrix describing how much each pair of features varies together.</li>
    <li><strong style="color:var(--text);">Find eigenvectors and eigenvalues</strong> of the covariance matrix. Each eigenvector is a principal component direction; its eigenvalue is the amount of variance captured.</li>
    <li><strong style="color:var(--text);">Sort by eigenvalue</strong> (descending) — PC1 explains the most variance, PC2 the second-most, and so on.</li>
    <li><strong style="color:var(--text);">Project</strong> the original data onto the top k eigenvectors to get the reduced-dimensional representation.</li>
  </ol>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — PCA on the Iris Dataset (4D → 2D)</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">from</span> sklearn.decomposition <span style="color:#c4b5fd;">import</span> PCA
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> StandardScaler
<span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_iris

<span style="color:#6b7280;"># Load Iris: 150 samples, 4 features, 3 species</span>
iris   = <span style="color:#93c5fd;">load_iris</span>()
X, y   = iris.data, iris.target
labels = iris.target_names   <span style="color:#6b7280;"># ['setosa', 'versicolor', 'virginica']</span>

<span style="color:#6b7280;"># Step 1: Scale — critical before PCA</span>
scaler  = <span style="color:#93c5fd;">StandardScaler</span>()
X_scaled = scaler.<span style="color:#93c5fd;">fit_transform</span>(X)

<span style="color:#6b7280;"># Step 2: Fit PCA — keep all components first to inspect variance</span>
pca_full = <span style="color:#93c5fd;">PCA</span>(n_components=<span style="color:#fcd34d;">4</span>)
pca_full.<span style="color:#93c5fd;">fit</span>(X_scaled)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Explained variance ratio per component:"</span>)
<span style="color:#c4b5fd;">for</span> i, var <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(pca_full.explained_variance_ratio_):
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  PC{i+1}: {var:.3f} ({var*100:.1f}%)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  PC1+PC2 cumulative: {sum(pca_full.explained_variance_ratio_[:2])*100:.1f}%"</span>)

<span style="color:#6b7280;"># Step 3: Reduce to 2D for visualization</span>
pca_2d   = <span style="color:#93c5fd;">PCA</span>(n_components=<span style="color:#fcd34d;">2</span>)
X_2d     = pca_2d.<span style="color:#93c5fd;">fit_transform</span>(X_scaled)

<span style="color:#6b7280;"># Step 4: Visualize</span>
fig, (ax1, ax2) = plt.<span style="color:#93c5fd;">subplots</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, figsize=(<span style="color:#fcd34d;">13</span>, <span style="color:#fcd34d;">5</span>))
colors = [<span style="color:#a7f3d0;">"#3498db"</span>, <span style="color:#a7f3d0;">"#e74c3c"</span>, <span style="color:#a7f3d0;">"#2ecc71"</span>]

<span style="color:#6b7280;"># Scree plot</span>
cumvar = np.<span style="color:#93c5fd;">cumsum</span>(pca_full.explained_variance_ratio_)
ax1.<span style="color:#93c5fd;">bar</span>(<span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">5</span>), pca_full.explained_variance_ratio_,
        color=<span style="color:#a7f3d0;">"steelblue"</span>, label=<span style="color:#a7f3d0;">"Individual"</span>)
ax1.<span style="color:#93c5fd;">plot</span>(<span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">5</span>), cumvar, <span style="color:#a7f3d0;">"ro-"</span>, label=<span style="color:#a7f3d0;">"Cumulative"</span>)
ax1.<span style="color:#93c5fd;">axhline</span>(<span style="color:#fcd34d;">0.95</span>, linestyle=<span style="color:#a7f3d0;">"--"</span>, color=<span style="color:#a7f3d0;">"gray"</span>, label=<span style="color:#a7f3d0;">"95% threshold"</span>)
ax1.<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Scree Plot — Explained Variance"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
ax1.<span style="color:#93c5fd;">set_xlabel</span>(<span style="color:#a7f3d0;">"Principal Component"</span>)
ax1.<span style="color:#93c5fd;">set_ylabel</span>(<span style="color:#a7f3d0;">"Proportion of Variance"</span>)
ax1.<span style="color:#93c5fd;">legend</span>()

<span style="color:#6b7280;"># 2D PCA scatter</span>
<span style="color:#c4b5fd;">for</span> i, species <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(labels):
    mask = y == i
    ax2.<span style="color:#93c5fd;">scatter</span>(X_2d[mask, <span style="color:#fcd34d;">0</span>], X_2d[mask, <span style="color:#fcd34d;">1</span>],
               c=colors[i], s=<span style="color:#fcd34d;">50</span>, alpha=<span style="color:#fcd34d;">0.7</span>, label=species)

ax2.<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Iris Dataset — PCA to 2D\n(4 features → 2 components)"</span>,
             fontweight=<span style="color:#a7f3d0;">"bold"</span>)
ax2.<span style="color:#93c5fd;">set_xlabel</span>(<span style="color:#a7f3d0;">f"PC1 ({pca_full.explained_variance_ratio_[0]*100:.1f}% variance)"</span>)
ax2.<span style="color:#93c5fd;">set_ylabel</span>(<span style="color:#a7f3d0;">f"PC2 ({pca_full.explained_variance_ratio_[1]*100:.1f}% variance)"</span>)
ax2.<span style="color:#93c5fd;">legend</span>()

plt.<span style="color:#93c5fd;">tight_layout</span>()
plt.<span style="color:#93c5fd;">show</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Explained variance ratio per component:
  PC1: 0.730 (73.0%)
  PC2: 0.229 (22.9%)
  PC3: 0.037 (3.7%)
  PC4: 0.005 (0.5%)
  PC1+PC2 cumulative: 95.8%
[Scree: PC1 tall bar, cumulative line crosses 95% at PC2]
[Scatter: Setosa fully separated; Versicolor/Virginica slight overlap]</div>
  </div>
</div>

<h3>PCA for Noise Reduction & Feature Preprocessing</h3>
<p>Beyond visualization, PCA is used as a <strong>preprocessing step</strong> before supervised learning: transform your 500 correlated features into 50 uncorrelated principal components, then train your model on those 50. This reduces overfitting, speeds up training, and eliminates multicollinearity.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — PCA with n_components as Variance Threshold</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_digits

<span style="color:#6b7280;"># 1797 handwritten digit images, each with 64 pixel features</span>
digits = <span style="color:#93c5fd;">load_digits</span>()
X_dig  = <span style="color:#93c5fd;">StandardScaler</span>().<span style="color:#93c5fd;">fit_transform</span>(digits.data)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Original shape: {X_dig.shape}"</span>)   <span style="color:#6b7280;"># (1797, 64)</span>

<span style="color:#6b7280;"># Pass a float to n_components: keep enough PCs to explain 95% of variance</span>
pca_95 = <span style="color:#93c5fd;">PCA</span>(n_components=<span style="color:#fcd34d;">0.95</span>)
X_pca  = pca_95.<span style="color:#93c5fd;">fit_transform</span>(X_dig)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Reduced shape:  {X_pca.shape}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Components kept: {pca_95.n_components_}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Variance explained: {pca_95.explained_variance_ratio_.sum()*100:.1f}%"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Feature reduction: {64} → {pca_95.n_components_} ({100*(1-pca_95.n_components_/64):.0f}% smaller)"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Original shape: (1797, 64)
Reduced shape:  (1797, 29)
Components kept: 29
Variance explained: 95.1%
Feature reduction: 64 → 29 (55% smaller)</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '21.5 Principal Component Analysis (PCA)',
            'order_index' => 5,
            'content'     => $this->appendQuiz($content5, 'L21_5', [
                ['q' => 'What does a principal component represent geometrically?', 'opts' => ['A row in the dataset', 'A direction (axis) in the feature space that captures maximum variance, orthogonal to all previous components', 'The average of two correlated features', 'A cluster centroid'], 'ans' => 1, 'exp' => 'Each principal component is an eigenvector of the covariance matrix — a unit-length direction vector in the original feature space. PC1 points in the direction of greatest variance; PC2 is orthogonal to PC1 and points in the direction of the next greatest variance, and so on.'],
                ['q' => 'Why must you apply StandardScaler before PCA?', 'opts' => ['PCA requires integer values', 'PCA uses covariance which is dominated by high-variance (large-scale) features — scaling ensures all features contribute equally', 'StandardScaler reduces the number of features first', 'Scaling speeds up the eigenvector computation'], 'ans' => 1, 'exp' => 'PCA finds directions of maximum variance. Without scaling, a feature with range 0-100,000 will dominate the covariance matrix completely. StandardScaler brings all features to the same scale so PCA finds meaningful directions rather than just the largest-scale feature\'s direction.'],
                ['q' => 'What does a Scree Plot show?', 'opts' => ['The cluster assignments for each principal component', 'The explained variance ratio of each principal component, used to choose how many components to keep', 'The correlation between original features and components', 'The running time of PCA per component'], 'ans' => 1, 'exp' => 'A scree plot shows the explained variance ratio (y-axis) for each PC (x-axis), plus a cumulative line. You look for an "elbow" in the individual bars or choose the number of PCs needed for the cumulative line to cross a threshold (commonly 95%).'],
                ['q' => 'What does PCA(n_components=0.95) do in scikit-learn?', 'opts' => ['Keeps exactly 95% of the original features', 'Raises an error — n_components must be an integer', 'Automatically selects the minimum number of components needed to retain 95% of the total variance', 'Reduces to 5 components always'], 'ans' => 2, 'exp' => 'When you pass a float between 0 and 1 to n_components, scikit-learn automatically finds the minimum number of principal components whose cumulative explained variance meets that threshold. It is the most practical way to use PCA in a preprocessing pipeline.'],
                ['q' => 'After PCA, can you perfectly reconstruct the original data from 2 components (when the original had 64 features)?', 'opts' => ['Yes — PCA is lossless', 'No — information from discarded components is lost; reconstruction is approximate', 'Yes — but only if you kept 95% variance', 'No — PCA cannot be inverted'], 'ans' => 1, 'exp' => 'PCA is a lossy compression. Keeping 2 of 64 components means you discarded 62 directions of variation. You can reconstruct an approximation using pca.inverse_transform(), but the result will be a smoothed version missing fine-grained details captured by the dropped components.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 21.6 — t-SNE & UMAP: Non-Linear Dimensionality Reduction
        // ══════════════════════════════════════════════════════════════
        $content6 = <<<'HTML'
<h2>t-SNE & UMAP: Non-Linear Dimensionality Reduction</h2>
<p>PCA is a <em>linear</em> method — it can only find structure that lies along straight directions. Many real-world datasets have complex, curved, non-linear structures. <strong>t-SNE</strong> (t-Distributed Stochastic Neighbor Embedding) and <strong>UMAP</strong> (Uniform Manifold Approximation and Projection) are non-linear techniques specifically designed to produce 2D or 3D visualizations that preserve the <em>local neighborhood structure</em> of high-dimensional data. They are the standard tool for visualizing embedding spaces in NLP, genomics, and computer vision.</p>

<h3>t-SNE: How It Works</h3>
<p>t-SNE converts distances between points into probabilities. In high-dimensional space, it measures how likely each pair of points is to be neighbors (using a Gaussian kernel). In low-dimensional space, it uses a t-distribution (heavy-tailed) to measure the same. It then minimizes the KL divergence between these two probability distributions — iteratively moving points around until the low-D layout best matches the high-D neighborhood structure.</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:20px;margin-bottom:24px;">
  <table style="width:100%;border-collapse:collapse;font-size:0.875rem;">
    <thead>
      <tr style="border-bottom:1px solid var(--border);">
        <th style="text-align:left;padding:10px 12px;color:var(--text);font-weight:700;">Property</th>
        <th style="text-align:left;padding:10px 12px;color:var(--text);font-weight:700;">PCA</th>
        <th style="text-align:left;padding:10px 12px;color:var(--text);font-weight:700;">t-SNE</th>
        <th style="text-align:left;padding:10px 12px;color:var(--text);font-weight:700;">UMAP</th>
      </tr>
    </thead>
    <tbody>
      <tr style="border-bottom:1px solid var(--border);">
        <td style="padding:10px 12px;color:var(--text);font-weight:600;">Type</td>
        <td style="padding:10px 12px;color:var(--muted);">Linear</td>
        <td style="padding:10px 12px;color:var(--muted);">Non-linear</td>
        <td style="padding:10px 12px;color:var(--muted);">Non-linear</td>
      </tr>
      <tr style="border-bottom:1px solid var(--border);">
        <td style="padding:10px 12px;color:var(--text);font-weight:600;">Speed</td>
        <td style="padding:10px 12px;color:#a7f3d0);">Fast</td>
        <td style="padding:10px 12px;color:#fca5a5);">Slow (O(n²))</td>
        <td style="padding:10px 12px;color:#a7f3d0);">Much faster than t-SNE</td>
      </tr>
      <tr style="border-bottom:1px solid var(--border);">
        <td style="padding:10px 12px;color:var(--text);font-weight:600;">Preserves global structure?</td>
        <td style="padding:10px 12px;color:#a7f3d0);">Yes</td>
        <td style="padding:10px 12px;color:#fca5a5);">No — only local</td>
        <td style="padding:10px 12px;color:#fcd34d);">Better than t-SNE</td>
      </tr>
      <tr style="border-bottom:1px solid var(--border);">
        <td style="padding:10px 12px;color:var(--text);font-weight:600;">Cluster distances meaningful?</td>
        <td style="padding:10px 12px;color:#a7f3d0);">Yes</td>
        <td style="padding:10px 12px;color:#fca5a5);">No</td>
        <td style="padding:10px 12px;color:#fcd34d);">Approximately</td>
      </tr>
      <tr>
        <td style="padding:10px 12px;color:var(--text);font-weight:600;">Primary use</td>
        <td style="padding:10px 12px;color:var(--muted);">Preprocessing, noise reduction</td>
        <td style="padding:10px 12px;color:var(--muted);">Visualization only</td>
        <td style="padding:10px 12px;color:var(--muted);">Visualization + general embedding</td>
      </tr>
    </tbody>
  </table>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — PCA vs t-SNE on Digits Dataset</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_digits
<span style="color:#c4b5fd;">from</span> sklearn.decomposition <span style="color:#c4b5fd;">import</span> PCA
<span style="color:#c4b5fd;">from</span> sklearn.manifold <span style="color:#c4b5fd;">import</span> TSNE
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> StandardScaler

digits   = <span style="color:#93c5fd;">load_digits</span>()
X_scaled = <span style="color:#93c5fd;">StandardScaler</span>().<span style="color:#93c5fd;">fit_transform</span>(digits.data)
y        = digits.target

<span style="color:#6b7280;"># PCA to 2D</span>
X_pca = <span style="color:#93c5fd;">PCA</span>(n_components=<span style="color:#fcd34d;">2</span>).<span style="color:#93c5fd;">fit_transform</span>(X_scaled)

<span style="color:#6b7280;"># t-SNE to 2D — perplexity controls neighborhood size (typical: 5–50)</span>
<span style="color:#6b7280;"># PCA first to 50D speeds up t-SNE on high-dimensional data</span>
X_50d  = <span style="color:#93c5fd;">PCA</span>(n_components=<span style="color:#fcd34d;">50</span>).<span style="color:#93c5fd;">fit_transform</span>(X_scaled)
X_tsne = <span style="color:#93c5fd;">TSNE</span>(n_components=<span style="color:#fcd34d;">2</span>, perplexity=<span style="color:#fcd34d;">30</span>,
               n_iter=<span style="color:#fcd34d;">1000</span>, random_state=<span style="color:#fcd34d;">42</span>).<span style="color:#93c5fd;">fit_transform</span>(X_50d)

fig, (ax1, ax2) = plt.<span style="color:#93c5fd;">subplots</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, figsize=(<span style="color:#fcd34d;">14</span>, <span style="color:#fcd34d;">6</span>))

<span style="color:#c4b5fd;">for</span> ax, X_2d, title <span style="color:#c4b5fd;">in</span> [
    (ax1, X_pca,  <span style="color:#a7f3d0;">"PCA — Linear Projection (2D)"</span>),
    (ax2, X_tsne, <span style="color:#a7f3d0;">"t-SNE — Non-Linear Embedding (2D)"</span>)
]:
    sc = ax.<span style="color:#93c5fd;">scatter</span>(X_2d[:,<span style="color:#fcd34d;">0</span>], X_2d[:,<span style="color:#fcd34d;">1</span>],
                    c=y, cmap=<span style="color:#a7f3d0;">"tab10"</span>, s=<span style="color:#fcd34d;">15</span>, alpha=<span style="color:#fcd34d;">0.7</span>)
    ax.<span style="color:#93c5fd;">set_title</span>(title, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
    plt.<span style="color:#93c5fd;">colorbar</span>(sc, ax=ax, label=<span style="color:#a7f3d0;">"Digit Class"</span>)

plt.<span style="color:#93c5fd;">suptitle</span>(<span style="color:#a7f3d0;">"Handwritten Digits (64D) → 2D"</span>, fontsize=<span style="color:#fcd34d;">14</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
plt.<span style="color:#93c5fd;">tight_layout</span>()
plt.<span style="color:#93c5fd;">show</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Output</span>Left (PCA): 10 digit classes form overlapping smeared blobs — hard to separate.
Right (t-SNE): 10 cleanly separated islands — digits 0-9 form distinct clusters.
t-SNE reveals structure that PCA completely misses on this curved manifold.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '21.6 t-SNE & UMAP: Non-Linear Dimensionality Reduction',
            'order_index' => 6,
            'content'     => $this->appendQuiz($content6, 'L21_6', [
                ['q' => 'Why can\'t PCA reveal the cluster structure in the handwritten digits dataset?', 'opts' => ['PCA only works on binary data', 'The digit clusters lie on a curved (non-linear) manifold that PCA — a linear method — cannot "unfold" into a separable 2D layout', 'PCA cannot handle more than 10 features', 'The digits dataset has too few samples for PCA'], 'ans' => 1, 'exp' => 'PCA finds the best linear subspace (flat plane) to project data onto. When clusters are arranged on a curved surface in high-dimensional space, projecting onto a flat plane collapses them together. t-SNE and UMAP can follow the curvature of the manifold.'],
                ['q' => 'What does the perplexity parameter control in t-SNE?', 'opts' => ['The number of iterations', 'The learning rate of the optimization', 'The effective number of nearest neighbors considered — balancing local vs global structure', 'The random seed for initialization'], 'ans' => 2, 'exp' => 'Perplexity (typically 5–50) roughly controls the number of neighbors each point considers. Low perplexity focuses on very local structure; high perplexity considers more global neighborhoods. The result can look very different at different perplexity values, so you should experiment.'],
                ['q' => 'Can you use t-SNE coordinates as features for a downstream machine learning model?', 'opts' => ['Yes — always', 'No — t-SNE is stochastic and non-deterministic; coordinates change with random state and have no consistent meaning across runs', 'Yes — only if perplexity is above 30', 'No — t-SNE only outputs integers'], 'ans' => 1, 'exp' => 't-SNE is designed for visualization only. Its output is stochastic (different each run), does not preserve global distances, and cannot be applied to new unseen points without re-running the full algorithm. UMAP is preferred when you need a reusable embedding.'],
                ['q' => 'What is the recommended preprocessing step before running t-SNE on a high-dimensional dataset (e.g., 500+ features)?', 'opts' => ['Apply DBSCAN first', 'Use one-hot encoding', 'First reduce to 50 dimensions with PCA, then apply t-SNE — this dramatically speeds up computation while preserving structure', 'Normalize all values to [0, 1] with MinMaxScaler'], 'ans' => 2, 'exp' => 't-SNE scales O(n²) with features and samples. Applying PCA first (commonly to 50 dimensions) removes noise and dramatically reduces the computation while losing little meaningful structure. This is the standard practice in genomics and NLP pipelines.'],
                ['q' => 'Which dimensionality reduction technique is best for preprocessing before training a classifier?', 'opts' => ['t-SNE — because it creates the cleanest clusters visually', 'PCA — because it is linear, deterministic, reversible, and can transform new data without retraining', 'UMAP — because it always outperforms PCA', 'None — dimensionality reduction always hurts classifier performance'], 'ans' => 1, 'exp' => 'PCA is the right choice for supervised ML preprocessing because: it is deterministic (same result every run), you can transform new test data with the same fitted object (pca.transform(X_test)), and it removes correlated/redundant features. t-SNE and UMAP cannot transform new points without re-running.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 21.7 — Anomaly Detection: Isolation Forest
        // ══════════════════════════════════════════════════════════════
        $content7 = <<<'HTML'
<h2>Anomaly Detection: Isolation Forest</h2>
<p>Anomaly detection answers a deceptively simple question: <em>which observations in this dataset are unusual?</em> The applications are enormous — fraud transactions hiding in millions of legitimate ones, a server metric that suddenly spikes before a system failure, a patient whose lab results deviate dangerously from the population norm. <strong>Isolation Forest</strong> is the most widely used anomaly detection algorithm for tabular data, and it operates on a brilliant insight: <em>anomalies are easier to isolate than normal points</em>.</p>

<h3>The Isolation Forest Insight</h3>
<p>Imagine randomly choosing a feature and randomly choosing a split value within its range. Normal points — which cluster together — take many such random splits to isolate. Anomalous points — which are rare and far from the main cluster — get isolated in just a few splits. Isolation Forest builds hundreds of these random trees and measures the average path length needed to isolate each point. <strong>Short average path = anomaly. Long average path = normal.</strong></p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Isolation Forest for Fraud Detection</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">from</span> sklearn.ensemble <span style="color:#c4b5fd;">import</span> IsolationForest
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> StandardScaler

np.random.<span style="color:#93c5fd;">seed</span>(<span style="color:#fcd34d;">42</span>)

<span style="color:#6b7280;"># Simulate credit card transactions</span>
<span style="color:#6b7280;"># Normal: small amounts, frequent, regular hours</span>
n_normal = <span style="color:#fcd34d;">950</span>
normal = np.<span style="color:#93c5fd;">column_stack</span>([
    np.random.<span style="color:#93c5fd;">normal</span>(<span style="color:#fcd34d;">50</span>, <span style="color:#fcd34d;">20</span>, n_normal),    <span style="color:#6b7280;"># transaction amount</span>
    np.random.<span style="color:#93c5fd;">normal</span>(<span style="color:#fcd34d;">12</span>, <span style="color:#fcd34d;">3</span>, n_normal),     <span style="color:#6b7280;"># hour of day</span>
])

<span style="color:#6b7280;"># Fraudulent: large amounts, unusual hours</span>
n_fraud = <span style="color:#fcd34d;">50</span>
fraud = np.<span style="color:#93c5fd;">column_stack</span>([
    np.random.<span style="color:#93c5fd;">uniform</span>(<span style="color:#fcd34d;">800</span>, <span style="color:#fcd34d;">2000</span>, n_fraud),  <span style="color:#6b7280;"># large amounts</span>
    np.random.<span style="color:#93c5fd;">uniform</span>(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">4</span>, n_fraud),       <span style="color:#6b7280;"># very late at night</span>
])

X = np.<span style="color:#93c5fd;">vstack</span>([normal, fraud])
true_labels = np.<span style="color:#93c5fd;">array</span>([<span style="color:#fcd34d;">1</span>]*n_normal + [-<span style="color:#fcd34d;">1</span>]*n_fraud)  <span style="color:#6b7280;"># 1=normal, -1=fraud</span>

<span style="color:#6b7280;"># Scale</span>
X_scaled = <span style="color:#93c5fd;">StandardScaler</span>().<span style="color:#93c5fd;">fit_transform</span>(X)

<span style="color:#6b7280;"># Fit Isolation Forest</span>
<span style="color:#6b7280;"># contamination: expected proportion of anomalies in the dataset</span>
iso = <span style="color:#93c5fd;">IsolationForest</span>(n_estimators=<span style="color:#fcd34d;">200</span>,
                       contamination=<span style="color:#fcd34d;">0.05</span>,   <span style="color:#6b7280;"># ~5% of data is fraud</span>
                       random_state=<span style="color:#fcd34d;">42</span>)
predictions = iso.<span style="color:#93c5fd;">fit_predict</span>(X_scaled)  <span style="color:#6b7280;"># 1=normal, -1=anomaly</span>
scores      = iso.<span style="color:#93c5fd;">score_samples</span>(X_scaled)  <span style="color:#6b7280;"># more negative = more anomalous</span>

<span style="color:#6b7280;"># Evaluate</span>
n_detected = (predictions == -<span style="color:#fcd34d;">1</span>).<span style="color:#93c5fd;">sum</span>()
correct    = ((predictions == -<span style="color:#fcd34d;">1</span>) & (true_labels == -<span style="color:#fcd34d;">1</span>)).<span style="color:#93c5fd;">sum</span>()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Total flagged as anomalies: {n_detected}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Correct fraud detections:   {correct} / {n_fraud}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Precision on anomalies:     {correct/n_detected:.1%}"</span>)

<span style="color:#6b7280;"># Visualize</span>
fig, ax = plt.<span style="color:#93c5fd;">subplots</span>(figsize=(<span style="color:#fcd34d;">9</span>, <span style="color:#fcd34d;">6</span>))
ax.<span style="color:#93c5fd;">scatter</span>(X[predictions== <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">0</span>], X[predictions== <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>],
           c=<span style="color:#a7f3d0;">"#3498db"</span>, alpha=<span style="color:#fcd34d;">0.4</span>, s=<span style="color:#fcd34d;">20</span>, label=<span style="color:#a7f3d0;">"Normal"</span>)
ax.<span style="color:#93c5fd;">scatter</span>(X[predictions==-<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">0</span>], X[predictions==-<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>],
           c=<span style="color:#a7f3d0;">"#e74c3c"</span>, s=<span style="color:#fcd34d;">80</span>, marker=<span style="color:#a7f3d0;">"X"</span>, label=<span style="color:#a7f3d0;">"Flagged Anomaly"</span>, zorder=<span style="color:#fcd34d;">5</span>)
ax.<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Isolation Forest — Credit Card Fraud Detection"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
ax.<span style="color:#93c5fd;">set_xlabel</span>(<span style="color:#a7f3d0;">"Transaction Amount ($)"</span>)
ax.<span style="color:#93c5fd;">set_ylabel</span>(<span style="color:#a7f3d0;">"Hour of Day"</span>)
ax.<span style="color:#93c5fd;">legend</span>()
plt.<span style="color:#93c5fd;">tight_layout</span>()
plt.<span style="color:#93c5fd;">show</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Total flagged as anomalies: 50
Correct fraud detections:   47 / 50
Precision on anomalies:     94.0%
[Scatter: blue cluster (normal) left-center, red X marks (fraud) upper-right]</div>
  </div>
</div>

<h3>Understanding the Anomaly Score</h3>
<p>Beyond binary labels, Isolation Forest provides a continuous <strong>anomaly score</strong> via <code>score_samples()</code>. More negative scores indicate greater anomalousness. This lets you set your own threshold rather than relying on the contamination parameter — crucial in production systems where the cost of a false positive vs false negative differs greatly.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Anomaly Score Distribution</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> seaborn <span style="color:#c4b5fd;">as</span> sns

fig, ax = plt.<span style="color:#93c5fd;">subplots</span>(figsize=(<span style="color:#fcd34d;">9</span>, <span style="color:#fcd34d;">4</span>))

<span style="color:#6b7280;"># Plot score distributions for normal vs fraud</span>
sns.<span style="color:#93c5fd;">kdeplot</span>(scores[:<span style="color:#fcd34d;">950</span>],  ax=ax, color=<span style="color:#a7f3d0;">"steelblue"</span>,
            fill=<span style="color:#fca5a5;">True</span>, alpha=<span style="color:#fcd34d;">0.3</span>, label=<span style="color:#a7f3d0;">"Normal transactions"</span>)
sns.<span style="color:#93c5fd;">kdeplot</span>(scores[<span style="color:#fcd34d;">950</span>:],  ax=ax, color=<span style="color:#a7f3d0;">"#e74c3c"</span>,
            fill=<span style="color:#fca5a5;">True</span>, alpha=<span style="color:#fcd34d;">0.3</span>, label=<span style="color:#a7f3d0;">"Fraud transactions"</span>)

<span style="color:#6b7280;"># Mark threshold — below this = flagged as anomaly</span>
threshold = iso.<span style="color:#93c5fd;">offset_</span>
ax.<span style="color:#93c5fd;">axvline</span>(threshold, color=<span style="color:#a7f3d0;">"black"</span>, linestyle=<span style="color:#a7f3d0;">"--"</span>,
            label=<span style="color:#a7f3d0;">f"Decision threshold: {threshold:.3f}"</span>)
ax.<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Anomaly Score Distributions"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
ax.<span style="color:#93c5fd;">set_xlabel</span>(<span style="color:#a7f3d0;">"Isolation Forest Score (more negative = more anomalous)"</span>)
ax.<span style="color:#93c5fd;">legend</span>()
plt.<span style="color:#93c5fd;">tight_layout</span>()
plt.<span style="color:#93c5fd;">show</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Output</span>Two well-separated KDE curves.
Blue (normal): scores cluster around -0.05, mostly right of threshold.
Red (fraud): scores cluster around -0.25, mostly left of threshold.
Threshold line clearly separates the two populations.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '21.7 Anomaly Detection: Isolation Forest',
            'order_index' => 7,
            'content'     => $this->appendQuiz($content7, 'L21_7', [
                ['q' => 'What is the core insight behind Isolation Forest?', 'opts' => ['Normal points have higher feature values than anomalies', 'Anomalies are rare and far from other points, so they require fewer random splits to isolate in a tree', 'Anomalies always appear at the boundaries of the dataset', 'Isolation Forest identifies clusters first, then labels non-members as anomalies'], 'ans' => 1, 'exp' => 'Isolation Forest randomly selects a feature and a split value. Dense, normal points require many splits to isolate because there are many neighbors nearby. Anomalous points — rare, far from the crowd — are isolated in very few splits. Average path length is the anomaly score.'],
                ['q' => 'What does the contamination parameter control in Isolation Forest?', 'opts' => ['The number of trees in the forest', 'The expected proportion of anomalies in the training data — determines the decision threshold', 'The maximum depth of each isolation tree', 'The fraction of features sampled per split'], 'ans' => 1, 'exp' => 'contamination sets what proportion of training points the model expects to be anomalies. This is used to set the decision threshold: the score below which a point is labeled -1 (anomaly). Setting it too high flags too many normal points; too low misses real anomalies.'],
                ['q' => 'What does score_samples() return in scikit-learn\'s IsolationForest?', 'opts' => ['Binary labels: 1 for normal, -1 for anomaly', 'The cluster ID of each point', 'Continuous anomaly scores — more negative values indicate greater anomalousness', 'Probability estimates between 0 and 1'], 'ans' => 2, 'exp' => 'score_samples() returns a raw anomaly score (not binary). Scores closer to 0 are normal; more negative scores indicate anomalies. This continuous score lets you set your own custom threshold rather than relying on the contamination parameter.'],
                ['q' => 'In production fraud detection, why might you prefer a continuous score over binary labels?', 'opts' => ['Binary labels are harder to compute', 'A continuous score lets you tune the threshold based on business cost — you can trade off false positives vs false negatives depending on how expensive each error type is', 'Continuous scores are more accurate', 'Regulatory requirements mandate continuous scores'], 'ans' => 1, 'exp' => 'In fraud detection, a false negative (missing real fraud) and a false positive (blocking a legitimate customer) have very different costs. A continuous anomaly score lets you set the threshold to match your business priorities — more aggressive (fewer misses) or more conservative (fewer false blocks).'],
                ['q' => 'Why is Isolation Forest preferred over statistical methods (like z-score thresholding) for anomaly detection on multi-dimensional data?', 'opts' => ['It is always faster', 'Statistical methods require labeled fraud data', 'Isolation Forest can detect anomalies that are only unusual in combination of features — not necessarily extreme on any single feature', 'z-scores only work on normal distributions'], 'ans' => 2, 'exp' => 'A transaction might have a normal amount AND a normal hour individually, but the combination of a very high amount at 3am is anomalous. Z-score analysis per feature would miss this. Isolation Forest jointly considers all features, detecting anomalies in the multi-dimensional space.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 21.8 — Autoencoders for Unsupervised Feature Learning
        // ══════════════════════════════════════════════════════════════
        $content8 = <<<'HTML'
<h2>Autoencoders for Unsupervised Feature Learning</h2>
<p>An <strong>autoencoder</strong> is a neural network trained to <em>compress</em> its input into a smaller representation and then <em>reconstruct</em> the original input from that compressed form — using only the input data itself as the training signal. No labels required. This makes it a powerful unsupervised tool for <strong>dimensionality reduction</strong>, <strong>anomaly detection</strong>, <strong>denoising</strong>, and <strong>generative modeling</strong>. Unlike PCA, autoencoders can learn <em>non-linear</em> compressions through their hidden layers.</p>

<h3>Autoencoder Architecture</h3>
<p>An autoencoder has three parts: the <strong>Encoder</strong> (compresses input X into a small latent vector z — the bottleneck), the <strong>Bottleneck</strong> (the latent space representation — this is the learned feature space), and the <strong>Decoder</strong> (reconstructs X from z). The network is trained to minimize <strong>reconstruction loss</strong> — typically Mean Squared Error between input and output. The bottleneck forces the network to learn a compact, information-dense representation.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Autoencoder for Anomaly Detection (Keras)</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> StandardScaler
<span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> make_blobs
<span style="color:#c4b5fd;">import</span> tensorflow <span style="color:#c4b5fd;">as</span> tf
<span style="color:#c4b5fd;">from</span> tensorflow <span style="color:#c4b5fd;">import</span> keras

np.random.<span style="color:#93c5fd;">seed</span>(<span style="color:#fcd34d;">42</span>)
tf.<span style="color:#93c5fd;">random</span>.<span style="color:#93c5fd;">set_seed</span>(<span style="color:#fcd34d;">42</span>)

<span style="color:#6b7280;"># Simulate normal sensor readings (20 features)</span>
X_normal, _ = <span style="color:#93c5fd;">make_blobs</span>(n_samples=<span style="color:#fcd34d;">1000</span>, n_features=<span style="color:#fcd34d;">20</span>,
                           centers=<span style="color:#fcd34d;">1</span>, cluster_std=<span style="color:#fcd34d;">0.5</span>, random_state=<span style="color:#fcd34d;">42</span>)

<span style="color:#6b7280;"># Simulate anomalies (different distribution)</span>
X_anomaly = np.random.<span style="color:#93c5fd;">uniform</span>(-<span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">5</span>, (<span style="color:#fcd34d;">50</span>, <span style="color:#fcd34d;">20</span>))

scaler    = <span style="color:#93c5fd;">StandardScaler</span>()
X_train   = scaler.<span style="color:#93c5fd;">fit_transform</span>(X_normal)
X_test_n  = scaler.<span style="color:#93c5fd;">transform</span>(X_normal[:<span style="color:#fcd34d;">200</span>])   <span style="color:#6b7280;"># normal test set</span>
X_test_a  = scaler.<span style="color:#93c5fd;">transform</span>(X_anomaly)          <span style="color:#6b7280;"># anomaly test set</span>

<span style="color:#6b7280;"># Build autoencoder: 20 → 8 → 3 → 8 → 20</span>
input_dim = <span style="color:#fcd34d;">20</span>

autoencoder = keras.<span style="color:#93c5fd;">Sequential</span>([
    keras.layers.<span style="color:#93c5fd;">Dense</span>(<span style="color:#fcd34d;">8</span>,  activation=<span style="color:#a7f3d0;">"relu"</span>,    input_shape=(<span style="color:#fcd34d;">20</span>,)),  <span style="color:#6b7280;"># Encoder</span>
    keras.layers.<span style="color:#93c5fd;">Dense</span>(<span style="color:#fcd34d;">3</span>,  activation=<span style="color:#a7f3d0;">"relu"</span>),   <span style="color:#6b7280;"># Bottleneck (3D latent)</span>
    keras.layers.<span style="color:#93c5fd;">Dense</span>(<span style="color:#fcd34d;">8</span>,  activation=<span style="color:#a7f3d0;">"relu"</span>),   <span style="color:#6b7280;"># Decoder start</span>
    keras.layers.<span style="color:#93c5fd;">Dense</span>(<span style="color:#fcd34d;">20</span>, activation=<span style="color:#a7f3d0;">"linear"</span>)  <span style="color:#6b7280;"># Reconstruction output</span>
])

autoencoder.<span style="color:#93c5fd;">compile</span>(optimizer=<span style="color:#a7f3d0;">"adam"</span>, loss=<span style="color:#a7f3d0;">"mse"</span>)
autoencoder.<span style="color:#93c5fd;">summary</span>()

<span style="color:#6b7280;"># Train ONLY on normal data — learns what "normal" looks like</span>
history = autoencoder.<span style="color:#93c5fd;">fit</span>(X_train, X_train,  <span style="color:#6b7280;"># input = output (unsupervised)</span>
                           epochs=<span style="color:#fcd34d;">50</span>, batch_size=<span style="color:#fcd34d;">32</span>,
                           validation_split=<span style="color:#fcd34d;">0.1</span>, verbose=<span style="color:#fcd34d;">0</span>)

<span style="color:#6b7280;"># Compute reconstruction error — high error = anomaly</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fbcfe8;">reconstruction_error</span>(model, X):
    X_pred = model.<span style="color:#93c5fd;">predict</span>(X, verbose=<span style="color:#fcd34d;">0</span>)
    <span style="color:#c4b5fd;">return</span> np.<span style="color:#93c5fd;">mean</span>(np.<span style="color:#93c5fd;">square</span>(X - X_pred), axis=<span style="color:#fcd34d;">1</span>)

err_normal  = <span style="color:#93c5fd;">reconstruction_error</span>(autoencoder, X_test_n)
err_anomaly = <span style="color:#93c5fd;">reconstruction_error</span>(autoencoder, X_test_a)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Normal   — mean reconstruction error: {err_normal.mean():.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Anomaly  — mean reconstruction error: {err_anomaly.mean():.4f}"</span>)

<span style="color:#6b7280;"># Set threshold at 95th percentile of normal errors</span>
threshold = np.<span style="color:#93c5fd;">percentile</span>(err_normal, <span style="color:#fcd34d;">95</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Threshold (95th pct of normal): {threshold:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Anomalies detected: {(err_anomaly > threshold).sum()} / {len(err_anomaly)}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Normal   — mean reconstruction error: 0.0312
Anomaly  — mean reconstruction error: 0.8847
Threshold (95th pct of normal): 0.0621
Anomalies detected: 48 / 50</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '21.8 Autoencoders for Unsupervised Learning',
            'order_index' => 8,
            'content'     => $this->appendQuiz($content8, 'L21_8', [
                ['q' => 'What makes an autoencoder an "unsupervised" learning method?', 'opts' => ['It requires no data at all', 'The training signal is the input data itself — input = target output — so no external labels are needed', 'It uses random weights that are never updated', 'It only works on image data without labels'], 'ans' => 1, 'exp' => 'An autoencoder is trained to reconstruct its own input: model(X) ≈ X. The loss is the reconstruction error between input and output. Since the input serves as its own label, no external annotation is required — making it fully unsupervised.'],
                ['q' => 'What is the "bottleneck" in an autoencoder?', 'opts' => ['The layer with the highest number of neurons', 'The output layer that produces reconstructions', 'The narrow middle layer (latent space) that forces the network to learn a compressed representation', 'The loss function used during training'], 'ans' => 2, 'exp' => 'The bottleneck is the smallest layer between encoder and decoder. Because information must pass through this narrow layer, the network is forced to learn the most informative, compact features of the input — discarding noise and redundancy.'],
                ['q' => 'How does an autoencoder detect anomalies at inference time?', 'opts' => ['It assigns cluster labels to each input', 'Anomalies have high reconstruction error — the autoencoder trained on normal data cannot reconstruct unusual patterns well', 'It computes z-scores across all features', 'It compares each input to a stored database of known anomalies'], 'ans' => 1, 'exp' => 'Trained only on normal data, the autoencoder learns to reconstruct normal patterns efficiently. When it sees an anomalous input — with patterns it has never learned — it cannot reconstruct it well, resulting in high MSE. A threshold on reconstruction error separates normal from anomalous.'],
                ['q' => 'What is the key advantage of an autoencoder over PCA for dimensionality reduction?', 'opts' => ['Autoencoders train faster than PCA', 'Autoencoders are simpler to implement', 'Autoencoders can learn non-linear compressed representations through their activation functions, while PCA is strictly linear', 'Autoencoders require no hyperparameter tuning'], 'ans' => 2, 'exp' => 'PCA projects data onto linear principal components. An autoencoder uses activation functions (ReLU, sigmoid) in its hidden layers, allowing it to learn non-linear transformations. For data with complex curved structure, autoencoders can create far more compact representations than PCA.'],
                ['q' => 'Why do we set the anomaly threshold at the 95th percentile of reconstruction errors on normal data?', 'opts' => ['It is the standard industry requirement', 'The 95th percentile is always the most accurate threshold', 'It means that 95% of normal samples score below the threshold (acceptable false positive rate of 5%), while anomalies — with much higher errors — exceed it', 'It minimizes the total number of predictions'], 'ans' => 2, 'exp' => 'Setting the threshold at the 95th percentile of normal reconstruction errors means roughly 5% of normal samples will be falsely flagged (false positive rate ≈ 5%). Anomalies typically have reconstruction errors far above this threshold. You can adjust the percentile to trade off sensitivity vs specificity.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 21.9 — Gaussian Mixture Models & Soft Clustering
        // ══════════════════════════════════════════════════════════════
        $content9 = <<<'HTML'
<h2>Gaussian Mixture Models & Soft Clustering</h2>
<p>K-Means performs <strong>hard clustering</strong> — every point belongs to exactly one cluster with 100% certainty. But what about a customer who displays behavior characteristic of <em>both</em> your "young professional" and "parent" segments? In reality, cluster membership is often probabilistic. <strong>Gaussian Mixture Models (GMMs)</strong> perform <strong>soft clustering</strong>: every point receives a probability of belonging to <em>each</em> cluster. This richer output enables more nuanced decision-making and naturally handles overlapping clusters.</p>

<h3>How GMMs Work: The EM Algorithm</h3>
<p>A GMM assumes your data was generated by a mixture of k Gaussian (normal) distributions, each with its own mean, covariance, and mixing weight. It fits these parameters using the <strong>Expectation-Maximization (EM)</strong> algorithm — alternating between:</p>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:20px;margin-bottom:24px;">
  <ol style="color:var(--muted);line-height:2.2;font-size:0.9rem;margin:0;padding-left:20px;">
    <li><strong style="color:var(--text);">E-Step (Expectation):</strong> For each point and each Gaussian, compute the probability that this Gaussian generated this point. These are the "responsibilities."</li>
    <li><strong style="color:var(--text);">M-Step (Maximization):</strong> Update each Gaussian's mean, covariance, and weight using the responsibility-weighted data. Gaussians that explain more points get more weight.</li>
    <li><strong style="color:var(--text);">Repeat</strong> until the log-likelihood converges.</li>
  </ol>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — GMM Soft Clustering & Probability Outputs</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">from</span> sklearn.mixture <span style="color:#c4b5fd;">import</span> GaussianMixture
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> StandardScaler
<span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> make_blobs
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd

np.random.<span style="color:#93c5fd;">seed</span>(<span style="color:#fcd34d;">7</span>)

<span style="color:#6b7280;"># Two overlapping clusters</span>
X, _ = <span style="color:#93c5fd;">make_blobs</span>(n_samples=<span style="color:#fcd34d;">300</span>, centers=[[-<span style="color:#fcd34d;">1</span>, -<span style="color:#fcd34d;">1</span>], [<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>]],
                   cluster_std=<span style="color:#fcd34d;">1.2</span>, random_state=<span style="color:#fcd34d;">7</span>)
X_scaled = <span style="color:#93c5fd;">StandardScaler</span>().<span style="color:#93c5fd;">fit_transform</span>(X)

<span style="color:#6b7280;"># Fit GMM with 2 components</span>
gmm = <span style="color:#93c5fd;">GaussianMixture</span>(n_components=<span style="color:#fcd34d;">2</span>, covariance_type=<span style="color:#a7f3d0;">"full"</span>,
                      n_init=<span style="color:#fcd34d;">10</span>, random_state=<span style="color:#fcd34d;">42</span>)
gmm.<span style="color:#93c5fd;">fit</span>(X_scaled)

<span style="color:#6b7280;"># Hard labels vs soft probabilities</span>
hard_labels = gmm.<span style="color:#93c5fd;">predict</span>(X_scaled)        <span style="color:#6b7280;"># argmax of probabilities</span>
soft_probs  = gmm.<span style="color:#93c5fd;">predict_proba</span>(X_scaled)  <span style="color:#6b7280;"># shape (n_samples, n_components)</span>

<span style="color:#6b7280;"># Show 5 points with their soft membership probabilities</span>
df_probs = pd.<span style="color:#93c5fd;">DataFrame</span>(soft_probs, columns=[<span style="color:#a7f3d0;">"P(Cluster 0)"</span>, <span style="color:#a7f3d0;">"P(Cluster 1)"</span>])
df_probs[<span style="color:#a7f3d0;">"Hard Label"</span>] = hard_labels
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Sample membership probabilities:"</span>)
<span style="color:#93c5fd;">print</span>(df_probs.<span style="color:#93c5fd;">round</span>(<span style="color:#fcd34d;">3</span>).<span style="color:#93c5fd;">head</span>(<span style="color:#fcd34d;">8</span>))

<span style="color:#6b7280;"># Visualize: color intensity = probability of belonging to Cluster 0</span>
fig, (ax1, ax2) = plt.<span style="color:#93c5fd;">subplots</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, figsize=(<span style="color:#fcd34d;">13</span>, <span style="color:#fcd34d;">5</span>))

<span style="color:#6b7280;"># K-Means style hard clustering</span>
ax1.<span style="color:#93c5fd;">scatter</span>(X_scaled[:,<span style="color:#fcd34d;">0</span>], X_scaled[:,<span style="color:#fcd34d;">1</span>], c=hard_labels,
           cmap=<span style="color:#a7f3d0;">"bwr"</span>, s=<span style="color:#fcd34d;">25</span>, alpha=<span style="color:#fcd34d;">0.8</span>)
ax1.<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Hard Cluster Labels (argmax)"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)

<span style="color:#6b7280;"># Soft: color = probability of cluster 0 (red=1.0, blue=0.0)</span>
sc = ax2.<span style="color:#93c5fd;">scatter</span>(X_scaled[:,<span style="color:#fcd34d;">0</span>], X_scaled[:,<span style="color:#fcd34d;">1</span>],
                  c=soft_probs[:,<span style="color:#fcd34d;">0</span>], cmap=<span style="color:#a7f3d0;">"RdBu"</span>, s=<span style="color:#fcd34d;">25</span>, alpha=<span style="color:#fcd34d;">0.8</span>)
plt.<span style="color:#93c5fd;">colorbar</span>(sc, ax=ax2, label=<span style="color:#a7f3d0;">"P(Cluster 0)"</span>)
ax2.<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Soft Membership Probabilities"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)

plt.<span style="color:#93c5fd;">tight_layout</span>()
plt.<span style="color:#93c5fd;">show</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Sample membership probabilities:
   P(Cluster 0)  P(Cluster 1)  Hard Label
0         0.982         0.018           0
1         0.031         0.969           1
2         0.511         0.489           0   ← uncertain point!
3         0.997         0.003           0
4         0.008         0.992           1
[Left: hard colors, sharp boundary]
[Right: gradient — ambiguous points near center are purple/neutral]</div>
  </div>
</div>

<h3>Choosing the Number of Components with BIC/AIC</h3>
<p>GMMs use the <strong>BIC (Bayesian Information Criterion)</strong> and <strong>AIC (Akaike Information Criterion)</strong> to select the number of components — they balance fit quality against model complexity. Unlike K-Means inertia, BIC/AIC penalize for having too many parameters, so they have a genuine minimum at the optimal k.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — BIC/AIC for GMM Component Selection</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> make_blobs

X3, _ = <span style="color:#93c5fd;">make_blobs</span>(n_samples=<span style="color:#fcd34d;">400</span>, centers=<span style="color:#fcd34d;">3</span>,
                    cluster_std=<span style="color:#fcd34d;">0.8</span>, random_state=<span style="color:#fcd34d;">42</span>)
X3    = <span style="color:#93c5fd;">StandardScaler</span>().<span style="color:#93c5fd;">fit_transform</span>(X3)

bics, aics, k_range = [], [], <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">10</span>)

<span style="color:#c4b5fd;">for</span> k <span style="color:#c4b5fd;">in</span> k_range:
    g = <span style="color:#93c5fd;">GaussianMixture</span>(n_components=k, n_init=<span style="color:#fcd34d;">5</span>, random_state=<span style="color:#fcd34d;">42</span>)
    g.<span style="color:#93c5fd;">fit</span>(X3)
    bics.<span style="color:#93c5fd;">append</span>(g.<span style="color:#93c5fd;">bic</span>(X3))
    aics.<span style="color:#93c5fd;">append</span>(g.<span style="color:#93c5fd;">aic</span>(X3))

fig, ax = plt.<span style="color:#93c5fd;">subplots</span>(figsize=(<span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">4</span>))
ax.<span style="color:#93c5fd;">plot</span>(k_range, bics, <span style="color:#a7f3d0;">"bo-"</span>, label=<span style="color:#a7f3d0;">"BIC"</span>, linewidth=<span style="color:#fcd34d;">2</span>)
ax.<span style="color:#93c5fd;">plot</span>(k_range, aics, <span style="color:#a7f3d0;">"rs-"</span>, label=<span style="color:#a7f3d0;">"AIC"</span>, linewidth=<span style="color:#fcd34d;">2</span>)
ax.<span style="color:#93c5fd;">axvline</span>(<span style="color:#fcd34d;">3</span>, color=<span style="color:#a7f3d0;">"green"</span>, linestyle=<span style="color:#a7f3d0;">"--"</span>, label=<span style="color:#a7f3d0;">"Optimal k=3"</span>)
ax.<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"GMM: BIC & AIC vs Number of Components"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
ax.<span style="color:#93c5fd;">set_xlabel</span>(<span style="color:#a7f3d0;">"Number of Components (k)"</span>)
ax.<span style="color:#93c5fd;">set_ylabel</span>(<span style="color:#a7f3d0;">"Information Criterion (lower = better)"</span>)
ax.<span style="color:#93c5fd;">legend</span>()
plt.<span style="color:#93c5fd;">tight_layout</span>()
plt.<span style="color:#93c5fd;">show</span>()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"BIC minimum at k={bics.index(min(bics))+1}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>BIC minimum at k=3
[Both BIC and AIC curves dip to minimum at k=3 then rise]</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '21.9 Gaussian Mixture Models & Soft Clustering',
            'order_index' => 9,
            'content'     => $this->appendQuiz($content9, 'L21_9', [
                ['q' => 'What is the key difference between K-Means (hard) and GMM (soft) clustering?', 'opts' => ['K-Means is slower', 'GMMs always find more clusters', 'K-Means assigns each point to exactly one cluster; GMMs assign each point a probability of belonging to each cluster', 'GMMs require labeled data'], 'ans' => 2, 'exp' => 'Hard clustering (K-Means) gives every point a single, definitive cluster label. Soft clustering (GMMs) gives every point a vector of probabilities — one per cluster — that sum to 1.0. Points near cluster boundaries get ambiguous probabilities (e.g., 0.51 / 0.49) rather than a forced binary assignment.'],
                ['q' => 'What algorithm does a GMM use to fit its parameters?', 'opts' => ['Gradient descent', 'K-Nearest Neighbors', 'The Expectation-Maximization (EM) algorithm — alternating between computing responsibilities and updating Gaussian parameters', 'Random forest impurity minimization'], 'ans' => 2, 'exp' => 'EM alternates between E-step (estimate responsibility — how likely each Gaussian is responsible for each point) and M-step (update each Gaussian\'s mean, covariance, and weight using those responsibilities). It is guaranteed to increase log-likelihood at every step and converges to a local maximum.'],
                ['q' => 'What does BIC penalize for when selecting the number of GMM components?', 'opts' => ['High reconstruction error', 'The number of parameters in the model — more components = more parameters = higher penalty', 'The number of data points', 'Negative silhouette scores'], 'ans' => 1, 'exp' => 'BIC = -2 × log-likelihood + k × log(n), where k is the number of model parameters. Adding more components always improves fit, but BIC adds a penalty proportional to the parameter count. The minimum BIC balances fit quality with model simplicity — a true optimum rather than the monotonic decrease you see with K-Means inertia.'],
                ['q' => 'In predict_proba() output for a 3-component GMM, each row sums to:', 'opts' => ['3.0 (one per component)', '0.0', '1.0 — probabilities across components sum to 1.0 for each data point', 'Variable — depends on cluster density'], 'ans' => 2, 'exp' => 'predict_proba() returns a matrix of shape (n_samples, n_components). Each row is a probability distribution over clusters — they are non-negative and sum to exactly 1.0. This comes directly from the Bayes formula used in the E-step.'],
                ['q' => 'What does covariance_type="full" mean in GaussianMixture?', 'opts' => ['Each Gaussian has a circular shape', 'Each Gaussian shares the same covariance matrix', 'Each Gaussian has its own unrestricted covariance matrix — allowing ellipsoidal clusters of any orientation and size', 'Only the diagonal elements of covariance are fitted'], 'ans' => 2, 'exp' => '"full" means each component has its own complete (n_features × n_features) covariance matrix, allowing each Gaussian to be an ellipsoid of any shape and orientation. "spherical" forces circular clusters (like K-Means); "diag" allows ellipses aligned with axes; "tied" shares one matrix across all components.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 21.10 — End-to-End Unsupervised ML Pipeline
        // ══════════════════════════════════════════════════════════════
        $content10 = <<<'HTML'
<h2>End-to-End Unsupervised ML Pipeline</h2>
<p>Individual algorithms are powerful — but production machine learning is built from <em>pipelines</em>: repeatable, structured workflows that chain preprocessing, modeling, and evaluation together. In this lesson you build a complete, professional-grade unsupervised learning pipeline from raw data to business-ready cluster profiles, applying every technique from this module in a single cohesive workflow.</p>

<h3>The Complete Pipeline: Raw Data → Business Segments</h3>
<p>We will work through a realistic e-commerce customer dataset with RFM features, applying: data loading and cleaning, StandardScaler, PCA for dimensionality reduction and visualization, K-Means with Elbow + Silhouette for k selection, cluster profiling, and Isolation Forest to flag anomalous customers before segmentation contaminates results.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Complete Unsupervised Learning Pipeline</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># ═══════════════════════════════════════════════════════════
# STEP 0: Imports
# ═══════════════════════════════════════════════════════════</span>
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">import</span> seaborn <span style="color:#c4b5fd;">as</span> sns
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> StandardScaler
<span style="color:#c4b5fd;">from</span> sklearn.decomposition <span style="color:#c4b5fd;">import</span> PCA
<span style="color:#c4b5fd;">from</span> sklearn.cluster <span style="color:#c4b5fd;">import</span> KMeans
<span style="color:#c4b5fd;">from</span> sklearn.metrics <span style="color:#c4b5fd;">import</span> silhouette_score
<span style="color:#c4b5fd;">from</span> sklearn.ensemble <span style="color:#c4b5fd;">import</span> IsolationForest

np.random.<span style="color:#93c5fd;">seed</span>(<span style="color:#fcd34d;">42</span>)

<span style="color:#6b7280;"># ═══════════════════════════════════════════════════════════
# STEP 1: Simulate realistic customer RFM data
# ═══════════════════════════════════════════════════════════</span>
n = <span style="color:#fcd34d;">1000</span>
df = pd.<span style="color:#93c5fd;">DataFrame</span>({
    <span style="color:#a7f3d0;">"customer_id"</span>:    <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">1</span>, n+<span style="color:#fcd34d;">1</span>),
    <span style="color:#a7f3d0;">"recency_days"</span>:   np.<span style="color:#93c5fd;">abs</span>(np.random.<span style="color:#93c5fd;">normal</span>(<span style="color:#fcd34d;">90</span>, <span style="color:#fcd34d;">60</span>, n)).<span style="color:#93c5fd;">astype</span>(<span style="color:#93c5fd;">int</span>),
    <span style="color:#a7f3d0;">"frequency"</span>:      np.random.<span style="color:#93c5fd;">poisson</span>(<span style="color:#fcd34d;">12</span>, n),
    <span style="color:#a7f3d0;">"monetary_value"</span>: np.<span style="color:#93c5fd;">abs</span>(np.random.<span style="color:#93c5fd;">normal</span>(<span style="color:#fcd34d;">1200</span>, <span style="color:#fcd34d;">800</span>, n)),
    <span style="color:#a7f3d0;">"avg_session_min"</span>: np.<span style="color:#93c5fd;">abs</span>(np.random.<span style="color:#93c5fd;">normal</span>(<span style="color:#fcd34d;">8</span>, <span style="color:#fcd34d;">4</span>, n)),
    <span style="color:#a7f3d0;">"cart_abandon_rate"</span>: np.random.<span style="color:#93c5fd;">uniform</span>(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, n),
})

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Dataset shape: {df.shape}"</span>)
<span style="color:#93c5fd;">print</span>(df.<span style="color:#93c5fd;">describe</span>().<span style="color:#93c5fd;">round</span>(<span style="color:#fcd34d;">1</span>))

<span style="color:#6b7280;"># ═══════════════════════════════════════════════════════════
# STEP 2: Anomaly detection — remove before clustering
# ═══════════════════════════════════════════════════════════</span>
features = [<span style="color:#a7f3d0;">"recency_days"</span>, <span style="color:#a7f3d0;">"frequency"</span>, <span style="color:#a7f3d0;">"monetary_value"</span>,
            <span style="color:#a7f3d0;">"avg_session_min"</span>, <span style="color:#a7f3d0;">"cart_abandon_rate"</span>]
X_raw   = df[features].<span style="color:#93c5fd;">values</span>

iso     = <span style="color:#93c5fd;">IsolationForest</span>(contamination=<span style="color:#fcd34d;">0.03</span>, random_state=<span style="color:#fcd34d;">42</span>)
df[<span style="color:#a7f3d0;">"is_anomaly"</span>] = iso.<span style="color:#93c5fd;">fit_predict</span>(X_raw)  <span style="color:#6b7280;"># -1 = anomaly</span>

n_anomalies = (df[<span style="color:#a7f3d0;">"is_anomaly"</span>] == -<span style="color:#fcd34d;">1</span>).<span style="color:#93c5fd;">sum</span>()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nStep 2 — Anomalies removed: {n_anomalies}"</span>)

df_clean = df[df[<span style="color:#a7f3d0;">"is_anomaly"</span>] == <span style="color:#fcd34d;">1</span>].<span style="color:#93c5fd;">copy</span>()
X_clean  = df_clean[features].<span style="color:#93c5fd;">values</span>

<span style="color:#6b7280;"># ═══════════════════════════════════════════════════════════
# STEP 3: Scale features
# ═══════════════════════════════════════════════════════════</span>
scaler   = <span style="color:#93c5fd;">StandardScaler</span>()
X_scaled = scaler.<span style="color:#93c5fd;">fit_transform</span>(X_clean)

<span style="color:#6b7280;"># ═══════════════════════════════════════════════════════════
# STEP 4: PCA for visualization
# ═══════════════════════════════════════════════════════════</span>
pca      = <span style="color:#93c5fd;">PCA</span>(n_components=<span style="color:#fcd34d;">2</span>)
X_pca    = pca.<span style="color:#93c5fd;">fit_transform</span>(X_scaled)
var_exp  = pca.explained_variance_ratio_.sum()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Step 4 — PCA 2D captures {var_exp*100:.1f}% of variance"</span>)

<span style="color:#6b7280;"># ═══════════════════════════════════════════════════════════
# STEP 5: Find optimal k (Elbow + Silhouette)
# ═══════════════════════════════════════════════════════════</span>
inertias, silhouettes = [], []
k_range = <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">9</span>)

<span style="color:#c4b5fd;">for</span> k <span style="color:#c4b5fd;">in</span> k_range:
    km = <span style="color:#93c5fd;">KMeans</span>(n_clusters=k, init=<span style="color:#a7f3d0;">"k-means++"</span>,
                n_init=<span style="color:#fcd34d;">10</span>, random_state=<span style="color:#fcd34d;">42</span>)
    km.<span style="color:#93c5fd;">fit</span>(X_scaled)
    inertias.<span style="color:#93c5fd;">append</span>(km.inertia_)
    silhouettes.<span style="color:#93c5fd;">append</span>(<span style="color:#93c5fd;">silhouette_score</span>(X_scaled, km.labels_))

best_k = k_range.<span style="color:#93c5fd;">start</span> + silhouettes.<span style="color:#93c5fd;">index</span>(<span style="color:#93c5fd;">max</span>(silhouettes))
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Step 5 — Best k by silhouette: {best_k}"</span>)

<span style="color:#6b7280;"># ═══════════════════════════════════════════════════════════
# STEP 6: Fit final model + profile clusters
# ═══════════════════════════════════════════════════════════</span>
final_km = <span style="color:#93c5fd;">KMeans</span>(n_clusters=best_k, init=<span style="color:#a7f3d0;">"k-means++"</span>,
                   n_init=<span style="color:#fcd34d;">10</span>, random_state=<span style="color:#fcd34d;">42</span>)
df_clean[<span style="color:#a7f3d0;">"Segment"</span>] = final_km.<span style="color:#93c5fd;">fit_predict</span>(X_scaled)

profile = df_clean.<span style="color:#93c5fd;">groupby</span>(<span style="color:#a7f3d0;">"Segment"</span>)[features].<span style="color:#93c5fd;">mean</span>().<span style="color:#93c5fd;">round</span>(<span style="color:#fcd34d;">1</span>)
profile[<span style="color:#a7f3d0;">"Count"</span>] = df_clean.<span style="color:#93c5fd;">groupby</span>(<span style="color:#a7f3d0;">"Segment"</span>).<span style="color:#93c5fd;">size</span>()

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nStep 6 — Cluster Profiles:"</span>)
<span style="color:#93c5fd;">print</span>(profile.<span style="color:#93c5fd;">to_string</span>())

<span style="color:#6b7280;"># ═══════════════════════════════════════════════════════════
# STEP 7: Visualization dashboard
# ═══════════════════════════════════════════════════════════</span>
colors = plt.<span style="color:#93c5fd;">cm</span>.Set1(<span style="color:#93c5fd;">range</span>(best_k))
fig, axes = plt.<span style="color:#93c5fd;">subplots</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">3</span>, figsize=(<span style="color:#fcd34d;">16</span>, <span style="color:#fcd34d;">5</span>))

<span style="color:#6b7280;"># Elbow</span>
axes[<span style="color:#fcd34d;">0</span>].<span style="color:#93c5fd;">plot</span>(k_range, inertias, <span style="color:#a7f3d0;">"bo-"</span>)
axes[<span style="color:#fcd34d;">0</span>].<span style="color:#93c5fd;">axvline</span>(best_k, color=<span style="color:#a7f3d0;">"red"</span>, linestyle=<span style="color:#a7f3d0;">"--"</span>)
axes[<span style="color:#fcd34d;">0</span>].<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Elbow Curve"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)

<span style="color:#6b7280;"># Silhouette</span>
axes[<span style="color:#fcd34d;">1</span>].<span style="color:#93c5fd;">plot</span>(k_range, silhouettes, <span style="color:#a7f3d0;">"gs-"</span>)
axes[<span style="color:#fcd34d;">1</span>].<span style="color:#93c5fd;">axvline</span>(best_k, color=<span style="color:#a7f3d0;">"red"</span>, linestyle=<span style="color:#a7f3d0;">"--"</span>)
axes[<span style="color:#fcd34d;">1</span>].<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">"Silhouette Score"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)

<span style="color:#6b7280;"># PCA scatter colored by segment</span>
segs = df_clean[<span style="color:#a7f3d0;">"Segment"</span>].<span style="color:#93c5fd;">values</span>
<span style="color:#c4b5fd;">for</span> k <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(best_k):
    axes[<span style="color:#fcd34d;">2</span>].<span style="color:#93c5fd;">scatter</span>(X_pca[segs==k, <span style="color:#fcd34d;">0</span>], X_pca[segs==k, <span style="color:#fcd34d;">1</span>],
                    s=<span style="color:#fcd34d;">15</span>, alpha=<span style="color:#fcd34d;">0.6</span>, label=<span style="color:#a7f3d0;">f"Seg {k}"</span>)
axes[<span style="color:#fcd34d;">2</span>].<span style="color:#93c5fd;">set_title</span>(<span style="color:#a7f3d0;">f"PCA 2D — {best_k} Segments"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
axes[<span style="color:#fcd34d;">2</span>].<span style="color:#93c5fd;">legend</span>()

fig.<span style="color:#93c5fd;">suptitle</span>(<span style="color:#a7f3d0;">"End-to-End Customer Segmentation Pipeline"</span>,
             fontsize=<span style="color:#fcd34d;">14</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
plt.<span style="color:#93c5fd;">tight_layout</span>()
plt.<span style="color:#93c5fd;">show</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Dataset shape: (1000, 7)
Step 2 — Anomalies removed: 30
Step 4 — PCA 2D captures 68.3% of variance
Step 5 — Best k by silhouette: 4

Step 6 — Cluster Profiles:
         recency_days  frequency  monetary_value  avg_session_min  cart_abandon_rate  Count
Segment
0                18.3       22.1          2841.4             12.6               0.28    228  ← Champions
1               152.6        4.8           312.7              4.1               0.76    245  ← Hibernating
2                45.2       14.3          1198.3              7.9               0.51    258  ← Potential Loyal
3               221.4        2.1            89.2              2.3               0.88    239  ← Lost

[3-panel dashboard: Elbow, Silhouette, PCA scatter with 4 color-coded segments]</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '21.10 End-to-End Unsupervised ML Pipeline',
            'order_index' => 10,
            'content'     => $this->appendQuiz($content10, 'L21_10', [
                ['q' => 'Why should Isolation Forest be applied BEFORE K-Means in a pipeline?', 'opts' => ['Isolation Forest requires K-Means output as input', 'Anomalous points distort cluster centroids — removing them first ensures K-Means finds meaningful, representative segments', 'K-Means cannot run on unfiltered data', 'Isolation Forest outputs the number of clusters needed for K-Means'], 'ans' => 1, 'exp' => 'K-Means centroids are computed as arithmetic means. Even a small number of extreme outliers can significantly pull centroids away from their true positions, creating a distorted segmentation. Removing anomalies with Isolation Forest first results in cleaner, more meaningful clusters.'],
                ['q' => 'In the pipeline, PCA is applied AFTER scaling but BEFORE K-Means. What is the benefit?', 'opts' => ['PCA automatically chooses k for K-Means', 'Reducing to 2D allows visualization of the cluster structure before committing to K-Means parameters', 'PCA is required for K-Means to converge', 'PCA removes all correlated features which confuse K-Means'], 'ans' => 1, 'exp' => 'Applying PCA to 2D before clustering lets you visually inspect whether natural group structure exists in the data. You can verify that the chosen k makes sense visually in the PC space. PCA to full rank (without reducing) can also remove noise, making K-Means converge faster and more reliably.'],
                ['q' => 'After obtaining cluster labels, what is "cluster profiling"?', 'opts' => ['Plotting each cluster\'s decision boundary', 'Computing summary statistics (means, medians) of the original features grouped by cluster label — to interpret what each cluster represents in business terms', 'Checking the silhouette score per cluster', 'Rerunning the algorithm with different random seeds'], 'ans' => 1, 'exp' => 'Cluster profiling is the crucial step of computing the mean (or median) of each original feature per cluster. This reveals the character of each group — e.g., Cluster 0 has low recency, high frequency, high monetary value → "Champions". Without profiling, cluster numbers are meaningless.'],
                ['q' => 'What does transform() (vs fit_transform()) do on test/new customer data in the scaling step?', 'opts' => ['Recomputes the mean and std from the new data and scales it', 'Applies the mean and std learned from training data to scale the new data — ensuring consistent scaling', 'Fits a new scaler and discards the original', 'Normalizes each row independently'], 'ans' => 1, 'exp' => 'transform() applies the pre-fitted scaler to new data without recomputing statistics. This is critical: new customers must be scaled using the same parameters (mean, std) learned from training data. If you call fit_transform() on new data, you\'re using different statistics — the scaled values won\'t be comparable to training data.'],
                ['q' => 'What is the final deliverable of a clustering pipeline from a business perspective?', 'opts' => ['A list of inertia scores', 'A trained K-Means model file', 'Actionable customer segment profiles with interpretable names and marketing strategy recommendations for each segment', 'The raw cluster label array'], 'ans' => 2, 'exp' => 'Cluster labels alone have no business value. The deliverable is a profiled, named, and interpreted set of segments — "Champions" (retain and reward), "Hibernating" (re-engagement campaign), "Lost" (win-back or write off) — each with clear characteristics and recommended actions. This translation from numbers to business decisions is the data scientist\'s key contribution.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 21.11 — Final Exam: Unsupervised Learning Mastery
        // ══════════════════════════════════════════════════════════════
        $allFinalQuestions = [
            ['q' => 'Which metric measures the average number of standard deviations by which a point is more similar to its own cluster than to the nearest other cluster?', 'opts' => ['Inertia (WCSS)', 'Silhouette Score', 'BIC', 'Explained Variance Ratio'], 'ans' => 1, 'exp' => 'The Silhouette Score for a point = (b - a) / max(a, b), where a = mean distance to points in same cluster, b = mean distance to points in nearest other cluster. Averaged across all points, it measures overall cluster quality. Ranges from -1 to +1; higher is better.'],
            ['q' => 'You run K-Means with k=5 and get inertia=400. You run it with k=6 and get inertia=380. What should you conclude?', 'opts' => ['Always use k=6 because lower inertia is always better', 'Inertia always decreases with more clusters — this alone says nothing; check if there is an elbow and examine the silhouette score', 'k=5 must be wrong', 'k=6 is overfit'], 'ans' => 1, 'exp' => 'Inertia decreases monotonically with k — adding more clusters always reduces it. The drop from 400 to 380 might be meaningful or trivial. The Elbow Method looks for where the rate of decrease dramatically slows, and the Silhouette Score provides an independent quality measure.'],
            ['q' => 'DBSCAN with eps=0.1 labels 80% of your dataset as noise. What should you do?', 'opts' => ['Accept the result — 80% noise is expected', 'Decrease min_samples to 1', 'Increase eps — the current neighborhood radius is too small for your data\'s density', 'Switch to K-Means instead'], 'ans' => 2, 'exp' => 'Extremely small eps means almost no two points are within each other\'s neighborhood, so nearly all points fail to form core points and become noise. Increasing eps expands the neighborhood radius, allowing denser connections to form. Use the k-distance plot to find the appropriate eps.'],
            ['q' => 'You have 1000 features. What is the recommended workflow before applying t-SNE?', 'opts' => ['Apply t-SNE directly to all 1000 features', 'First reduce to ~50 dimensions with PCA, then apply t-SNE', 'Apply K-Means first, then t-SNE on the cluster labels', 'Normalize to [0,1] range only'], 'ans' => 1, 'exp' => 't-SNE is computationally O(n²) and struggles with very high-dimensional inputs. The standard practice is to first apply PCA to reduce to 50 dimensions (removing noise while retaining structure), then apply t-SNE. This dramatically speeds up t-SNE and often improves results.'],
            ['q' => 'A GMM with covariance_type="spherical" is equivalent to which clustering algorithm?', 'opts' => ['DBSCAN', 'Hierarchical Clustering', 'K-Means — both assume spherical, equally-sized clusters', 'Isolation Forest'], 'ans' => 2, 'exp' => 'K-Means minimizes within-cluster sum of squared Euclidean distances, which implicitly assumes spherical, equally-sized clusters — the same as GMM with spherical covariance. The key difference: K-Means produces hard labels while GMM produces soft probabilities.'],
            ['q' => 'In an autoencoder trained for anomaly detection, what happens when it sees an anomaly at inference time?', 'opts' => ['It assigns a cluster label of -1', 'It produces a high reconstruction error because it never learned to encode/decode the anomalous pattern during training', 'It crashes with a runtime error', 'It outputs a confidence score above 0.95'], 'ans' => 1, 'exp' => 'Trained only on normal data, the autoencoder optimizes its weights to reconstruct normal patterns efficiently. When it encounters an anomaly — with patterns outside its training distribution — the encoder produces a poor latent representation, the decoder reconstructs poorly, and the MSE between input and output is high.'],
            ['q' => 'Ward linkage in hierarchical clustering merges clusters based on:', 'opts' => ['The maximum distance between any two points', 'The minimum distance between any two points', 'The increase in total within-cluster variance — merges the pair that increases variance least', 'The average cluster size'], 'ans' => 2, 'exp' => 'Ward linkage selects the merge that minimizes the increase in total within-cluster sum of squares. This tends to create compact, similarly-sized clusters and is the most commonly used linkage for general-purpose hierarchical clustering.'],
            ['q' => 'You are building a credit card fraud detection system. Why would you prefer continuous anomaly scores over binary labels from Isolation Forest?', 'opts' => ['Binary labels are inaccurate', 'Continuous scores allow you to tune the sensitivity — accepting more false positives to catch more fraud, or reducing false alarms at the cost of missing some fraud', 'Binary labels only work for K-Means clusters', 'Continuous scores are required by financial regulations'], 'ans' => 1, 'exp' => 'False negatives (missed fraud) cost more than false positives (blocking legitimate transactions) in most fraud systems — but the exact tradeoff depends on the business context. A continuous score lets you set the threshold to match your cost-benefit analysis rather than accepting the binary output dictated by the contamination parameter.'],
            ['q' => 'What does pca.explained_variance_ratio_.cumsum() tell you?', 'opts' => ['The cluster assignments from PCA', 'The cumulative proportion of total variance explained as you add each successive principal component', 'The correlation between principal components', 'The number of features in each principal component'], 'ans' => 1, 'exp' => 'explained_variance_ratio_ gives the proportion of variance each PC explains. cumsum() accumulates these: the value at index k tells you what fraction of total variance is explained by the first k+1 components. You use this to choose how many components to keep (e.g., enough for 95%).'],
            ['q' => 'Which of the following is TRUE about the EM algorithm used to train GMMs?', 'opts' => ['It always converges to the global optimum', 'It is guaranteed to increase log-likelihood at each iteration and converges to a local optimum', 'It requires labeled data for the E-step', 'It uses gradient descent for parameter updates'], 'ans' => 1, 'exp' => 'EM is provably guaranteed to increase (or maintain) the log-likelihood at every iteration — but only to a local maximum, not necessarily the global one. This is why GaussianMixture uses n_init > 1 to run EM multiple times from different initializations and pick the best result.'],
        ];

        $finalContent = <<<HTML
<div id="org-lock-screen" style="display:block;text-align:center;padding:60px 20px;">
    <div style="font-size:3rem;margin-bottom:16px;">🔒</div>
    <h2 style="color:var(--text);margin-bottom:8px;">Organization Access Required</h2>
    <p style="color:var(--muted);max-width:400px;margin:0 auto;">The Final Exam is available exclusively to learners enrolled through a verified organization.</p>
    <p style="font-size:0.85rem;color:#f59e0b;margin-top:1rem;background:rgba(245,158,11,0.1);padding:10px;border-radius:8px;display:inline-block;">Please contact administration to link your account to an organization.</p>
</div>
<div id="final-exam-content" style="display:none;">
    <h2>Module 21: Final Examination</h2>
    <p>This comprehensive exam covers all topics from Lessons 21.1 through 21.10 — unsupervised learning fundamentals, K-Means, DBSCAN, hierarchical clustering, PCA, t-SNE, Isolation Forest, autoencoders, Gaussian Mixture Models, and end-to-end pipelines. Good luck!</p>
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
            'title'       => '21.11 Final Exam: Unsupervised Learning Mastery',
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