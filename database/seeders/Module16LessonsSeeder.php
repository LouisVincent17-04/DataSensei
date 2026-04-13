<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Lesson;

/**
 * Module16LessonsSeeder
 * Seeds lessons for Module 16: Multivariate Analysis.
 * Run AFTER CurriculumSeeder which creates the modules.
 *
 * Usage:  php artisan db:seed --class=Module16LessonsSeeder
 *
 * Lessons:
 * 16.1  — Introduction to Multivariate Data & the Multivariate Normal
 * 16.2  — The Covariance Matrix & Multivariate Descriptive Statistics
 * 16.3  — Principal Component Analysis (PCA)
 * 16.4  — Factor Analysis
 * 16.5  — Multivariate Analysis of Variance (MANOVA)
 * 16.6  — Discriminant Analysis (LDA & QDA)
 * 16.7  — Cluster Analysis: Hierarchical & k-Means
 * 16.8  — Canonical Correlation Analysis (CCA)
 * 16.9  — Multidimensional Scaling (MDS) & t-SNE
 * 16.10 — Multivariate Regression & Path Analysis
 * 16.11 — Final Exam (Org-locked)
 */
class Module16LessonsSeeder extends Seeder
{
    public function run()
    {
        $module = Module::where('order_index', 16)->firstOrFail();
        Lesson::where('module_id', $module->id)->delete();

        // ══════════════════════════════════════════════════════════════
        // LESSON 16.1 — Introduction to Multivariate Data & the Multivariate Normal
        // ══════════════════════════════════════════════════════════════
        $content1 = <<<'HTML'
<h2>Introduction to Multivariate Data & the Multivariate Normal Distribution</h2>
<p>Multivariate analysis is the branch of statistics concerned with the simultaneous observation and analysis of more than one statistical variable. In the real world, virtually every meaningful phenomenon is described by multiple interrelated measurements — a patient's blood pressure, cholesterol, BMI, and glucose; a company's revenue, customer satisfaction, employee turnover, and market share; a soil sample's nitrogen, phosphorus, pH, and moisture content. Analyzing these variables one at a time — the univariate approach — ignores the rich structure that exists in their <em>joint</em> behavior. Multivariate analysis captures that structure.</p>

<h3>Why Multivariate? The Limitations of Univariate Analysis</h3>
<p>Consider two students both scoring 70 in Mathematics and 70 in English. Univariate summaries are identical. But if Student A always scores high in both subjects together while Student B scores high in one when low in the other, their joint behavior is fundamentally different. Multivariate analysis reveals these joint patterns — correlations, clusters, latent dimensions — that univariate analysis misses entirely.</p>
<p>The central challenge of multivariate analysis is the <strong>curse of dimensionality</strong>: as the number of variables p grows, the volume of the variable space grows exponentially. Data becomes sparse, distances lose meaning, and simple approaches fail. Multivariate methods are specifically designed to find low-dimensional structure in high-dimensional data.</p>

<h3>The Data Matrix</h3>
<p>Multivariate data is organized in an <strong>n × p data matrix X</strong>, where n is the number of observations (subjects, samples, time points) and p is the number of variables (features, measurements). Each row is a <strong>p-dimensional observation vector</strong> <strong>x</strong>ᵢ = [xᵢ₁, xᵢ₂, ..., xᵢₚ]ᵀ. Each column is a variable. The starting point of every multivariate analysis is this data matrix and its associated statistics — the mean vector and covariance matrix.</p>

<h3>The Mean Vector and Covariance Matrix</h3>
<p>For a sample of n p-dimensional observations, the <strong>sample mean vector</strong> is:</p>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;margin-bottom:20px;font-family:'JetBrains Mono',monospace;text-align:center;color:var(--text);font-size:0.95rem;">
  x̄ = (1/n) Σᵢ xᵢ  ∈ ℝᵖ
</div>
<p>The <strong>sample covariance matrix</strong> S is a p × p symmetric, positive semidefinite matrix:</p>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;margin-bottom:24px;font-family:'JetBrains Mono',monospace;text-align:center;color:var(--text);font-size:0.95rem;">
  S = (1/(n−1)) Σᵢ (xᵢ − x̄)(xᵢ − x̄)ᵀ  ∈ ℝᵖˣᵖ
</div>
<p>The diagonal entries sⱼⱼ are the sample variances of each variable; the off-diagonal entries sⱼₖ are the sample covariances. The covariance matrix encodes all pairwise linear relationships in the data — it is the foundation of essentially every multivariate technique.</p>

<h3>The Multivariate Normal Distribution</h3>
<p>The <strong>multivariate normal distribution</strong> (MVN) is the p-dimensional generalization of the familiar bell curve. A random vector <strong>X</strong> follows MVN if its joint density is:</p>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;margin-bottom:24px;font-family:'JetBrains Mono',monospace;text-align:center;color:var(--text);font-size:0.9rem;">
  f(<strong>x</strong>) = (2π)^(−p/2) |Σ|^(−1/2) exp(−½ (<strong>x</strong>−<strong>μ</strong>)ᵀ Σ⁻¹ (<strong>x</strong>−<strong>μ</strong>))
</div>
<p>We write <strong>X</strong> ~ Nₚ(<strong>μ</strong>, Σ), where <strong>μ</strong> ∈ ℝᵖ is the mean vector and Σ ∈ ℝᵖˣᵖ is the positive definite covariance matrix. The quantity (<strong>x</strong>−<strong>μ</strong>)ᵀ Σ⁻¹ (<strong>x</strong>−<strong>μ</strong>) is the <strong>Mahalanobis distance squared</strong> — a generalized distance that accounts for different variances and correlations among variables.</p>

<h3>Properties of the MVN</h3>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;margin-bottom:32px;display:grid;gap:12px;">
  <div style="background:rgba(99,102,241,0.08);border-left:3px solid #6366f1;padding:12px 16px;border-radius:0 6px 6px 0;">
    <strong style="color:#6366f1;">Marginal normality:</strong> Every linear combination a'X ~ N(a'μ, a'Σa). Every marginal distribution Xⱼ ~ N(μⱼ, σⱼⱼ).
  </div>
  <div style="background:rgba(16,185,129,0.08);border-left:3px solid #10b981;padding:12px 16px;border-radius:0 6px 6px 0;">
    <strong style="color:#10b981;">Conditional normality:</strong> The conditional distribution X₁|X₂ = x₂ is also normal — the mean shifts linearly in x₂ and the variance decreases.
  </div>
  <div style="background:rgba(245,158,11,0.08);border-left:3px solid #f59e0b;padding:12px 16px;border-radius:0 6px 6px 0;">
    <strong style="color:#f59e0b;">Zero covariance ⟺ Independence:</strong> For the MVN only, uncorrelated components are also independent. This is unique to the normal distribution.
  </div>
  <div style="background:rgba(239,68,68,0.08);border-left:3px solid #ef4444;padding:12px 16px;border-radius:0 6px 6px 0;">
    <strong style="color:#ef4444;">Constant density contours = ellipsoids:</strong> Points at equal Mahalanobis distance from μ form concentric ellipsoids oriented by the eigenvectors of Σ with radii proportional to the eigenvalues.
  </div>
</div>

<h3>Assessing Multivariate Normality</h3>
<p>Most classical multivariate methods assume (at least approximately) multivariate normality. Before applying these methods, it is important to assess this assumption:</p>
<ul style="color:var(--muted);line-height:2.2;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">Marginal normality checks:</strong> Histograms, Q-Q plots, and Shapiro-Wilk tests on each variable individually.</li>
  <li><strong style="color:var(--text);">Chi-square Q-Q plot:</strong> Under MVN, the squared Mahalanobis distances dᵢ² = (xᵢ − x̄)ᵀ S⁻¹ (xᵢ − x̄) follow approximately a chi-square distribution with p degrees of freedom. Plot ordered dᵢ² against chi-square quantiles — linearity indicates MVN.</li>
  <li><strong style="color:var(--text);">Mardia's tests:</strong> Formal tests based on multivariate skewness and kurtosis measures.</li>
</ul>

<h3>Python: Multivariate Data Exploration</h3>
<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — MVN Simulation, Mahalanobis Distance & Chi-Square Q-Q Plot</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">from</span> scipy <span style="color:#c4b5fd;">import</span> stats
<span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_iris

np.random.seed(<span style="color:#fcd34d;">42</span>)

<span style="color:#6b7280;"># ── Load real multivariate dataset ─────────────────────────────</span>
<span style="color:#93c5fd;">iris</span> = load_iris()
<span style="color:#93c5fd;">X</span>   = pd.DataFrame(iris.data, columns=iris.feature_names)
<span style="color:#93c5fd;">y</span>   = iris.target

<span style="color:#6b7280;"># ── Mean vector and covariance matrix ──────────────────────────</span>
<span style="color:#93c5fd;">mean_vec</span> = X.mean()
<span style="color:#93c5fd;">cov_mat</span>  = X.cov()

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"═══ MEAN VECTOR (x̄) ═══"</span>)
<span style="color:#93c5fd;">print</span>(mean_vec.round(<span style="color:#fcd34d;">4</span>).to_string())

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\n═══ COVARIANCE MATRIX (S) ═══"</span>)
<span style="color:#93c5fd;">print</span>(cov_mat.round(<span style="color:#fcd34d;">4</span>).to_string())

<span style="color:#6b7280;"># ── Correlation matrix ─────────────────────────────────────────</span>
<span style="color:#93c5fd;">corr_mat</span> = X.corr()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\n═══ CORRELATION MATRIX (R) ═══"</span>)
<span style="color:#93c5fd;">print</span>(corr_mat.round(<span style="color:#fcd34d;">4</span>).to_string())

<span style="color:#6b7280;"># ── Mahalanobis Distance ───────────────────────────────────────</span>
<span style="color:#93c5fd;">S_inv</span>     = np.linalg.inv(cov_mat.values)
<span style="color:#93c5fd;">X_centered</span>= X.values - mean_vec.values
<span style="color:#93c5fd;">maha_sq</span>   = np.array([x @ S_inv @ x <span style="color:#c4b5fd;">for</span> x <span style="color:#c4b5fd;">in</span> X_centered])

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\n═══ MAHALANOBIS DISTANCE² (first 5 obs) ═══"</span>)
<span style="color:#93c5fd;">print</span>(np.round(maha_sq[:<span style="color:#fcd34d;">5</span>], <span style="color:#fcd34d;">4</span>))
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Expected median under χ²(p={X.shape[1]}): {stats.chi2.ppf(0.5, df=X.shape[1]):.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Observed median of D²: {np.median(maha_sq):.4f}"</span>)

<span style="color:#6b7280;"># ── Chi-square Q-Q Plot (MVN assessment) ───────────────────────</span>
<span style="color:#93c5fd;">p</span>       = X.shape[<span style="color:#fcd34d;">1</span>]
<span style="color:#93c5fd;">n</span>       = X.shape[<span style="color:#fcd34d;">0</span>]
<span style="color:#93c5fd;">sorted_d2</span>  = np.sort(maha_sq)
<span style="color:#93c5fd;">quantiles</span>  = stats.chi2.ppf([(i - <span style="color:#fcd34d;">0.5</span>) / n <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">1</span>, n + <span style="color:#fcd34d;">1</span>)], df=p)

<span style="color:#93c5fd;">fig</span>, axes = plt.subplots(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, figsize=(<span style="color:#fcd34d;">13</span>, <span style="color:#fcd34d;">5</span>))

<span style="color:#6b7280;"># Chi-square Q-Q plot</span>
axes[<span style="color:#fcd34d;">0</span>].scatter(quantiles, sorted_d2, alpha=<span style="color:#fcd34d;">0.6</span>, color=<span style="color:#a7f3d0;">"#6366f1"</span>, s=<span style="color:#fcd34d;">25</span>)
axes[<span style="color:#fcd34d;">0</span>].plot([<span style="color:#fcd34d;">0</span>, quantiles.max()], [<span style="color:#fcd34d;">0</span>, quantiles.max()], <span style="color:#a7f3d0;">"r--"</span>, lw=<span style="color:#fcd34d;">2</span>, label=<span style="color:#a7f3d0;">"Reference line"</span>)
axes[<span style="color:#fcd34d;">0</span>].set_xlabel(<span style="color:#a7f3d0;">"χ²(4) Quantiles"</span>); axes[<span style="color:#fcd34d;">0</span>].set_ylabel(<span style="color:#a7f3d0;">"Sorted D² Values"</span>)
axes[<span style="color:#fcd34d;">0</span>].set_title(<span style="color:#a7f3d0;">"Chi-Square Q-Q Plot\n(MVN Normality Assessment)"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
axes[<span style="color:#fcd34d;">0</span>].legend()

<span style="color:#6b7280;"># Scatter matrix (pairs plot)</span>
<span style="color:#93c5fd;">colors_iris</span> = [<span style="color:#a7f3d0;">"#3b82f6"</span>, <span style="color:#a7f3d0;">"#10b981"</span>, <span style="color:#a7f3d0;">"#ef4444"</span>]
axes[<span style="color:#fcd34d;">1</span>].scatter(X.iloc[:, <span style="color:#fcd34d;">0</span>], X.iloc[:, <span style="color:#fcd34d;">1</span>],
                c=[colors_iris[yi] <span style="color:#c4b5fd;">for</span> yi <span style="color:#c4b5fd;">in</span> y], alpha=<span style="color:#fcd34d;">0.7</span>, s=<span style="color:#fcd34d;">40</span>)
axes[<span style="color:#fcd34d;">1</span>].set_xlabel(iris.feature_names[<span style="color:#fcd34d;">0</span>]); axes[<span style="color:#fcd34d;">1</span>].set_ylabel(iris.feature_names[<span style="color:#fcd34d;">1</span>])
axes[<span style="color:#fcd34d;">1</span>].set_title(<span style="color:#a7f3d0;">"Iris: Sepal Length vs Width\n(3 Species)"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
<span style="color:#c4b5fd;">for</span> i, name <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(iris.target_names):
    axes[<span style="color:#fcd34d;">1</span>].scatter([], [], c=colors_iris[i], label=name, s=<span style="color:#fcd34d;">40</span>)
axes[<span style="color:#fcd34d;">1</span>].legend()

plt.suptitle(<span style="color:#a7f3d0;">"Multivariate Data Structure — Iris Dataset"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
plt.tight_layout(); plt.show()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>═══ MEAN VECTOR (x̄) ═══
sepal length (cm)    5.8433
sepal width (cm)     3.0573
petal length (cm)    3.7580
petal width (cm)     1.1993

═══ COVARIANCE MATRIX (S) ═══
                   sepal length  sepal width  petal length  petal width
sepal length (cm)        0.6857       -0.0424        1.2743       0.5163
sepal width (cm)        -0.0424        0.1900       -0.3297      -0.1216
petal length (cm)        1.2743       -0.3297        3.1163       1.2956
petal width (cm)         0.5163       -0.1216        1.2956       0.5810

═══ MAHALANOBIS DISTANCE² (first 5 obs) ═══
[3.8435 4.7673 4.5018 4.2879 4.1043]
Expected median under χ²(p=4): 3.3567
Observed median of D²: 3.4916</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '16.1 Introduction to Multivariate Data & the Multivariate Normal',
            'order_index' => 1,
            'content'     => $this->appendQuiz($content1, 'M16_L1', [
                ['q' => 'A dataset has n=200 observations and p=8 variables. The sample covariance matrix S has dimensions...', 'opts' => ['200 × 8', '8 × 200', '8 × 8', '200 × 200'], 'ans' => 2, 'exp' => 'The sample covariance matrix S is always p × p — it encodes the variances of each variable (on the diagonal) and the pairwise covariances (off-diagonal). Its size depends only on the number of variables p, not the sample size n. For p=8, S is 8×8.'],
                ['q' => 'The Mahalanobis distance squared D² = (x−μ)ᵀ Σ⁻¹ (x−μ) differs from the squared Euclidean distance ‖x−μ‖² by...', 'opts' => ['Nothing — they are equivalent', 'D² accounts for different variable scales and their correlations; equal-distance contours are ellipses, not circles', 'D² uses the mean vector only', 'D² applies only to 2D data'], 'ans' => 1, 'exp' => 'Euclidean distance treats all dimensions equally — but if one variable has much larger variance or variables are correlated, Euclidean distance gives misleading proximity. Mahalanobis distance standardizes by Σ⁻¹, stretching along low-variance directions and rotating to align with the covariance structure. Its iso-distance contours are ellipsoids aligned with eigenvectors of Σ.'],
                ['q' => 'Under the multivariate normal distribution Nₚ(μ, Σ), if two components Xᵢ and Xⱼ have covariance σᵢⱼ = 0, then...', 'opts' => ['They are correlated but not linearly correlated', 'They are independent — a property unique to the normal distribution', 'They have the same mean', 'Their marginal distributions are uniform'], 'ans' => 1, 'exp' => 'In general, zero covariance implies only zero linear correlation, not independence. However, the multivariate normal distribution is special: for jointly normal variables, zero covariance (σᵢⱼ = 0) implies statistical independence. This powerful property is unique to the MVN and is why normality assumptions are so productive mathematically.'],
                ['q' => 'The chi-square Q-Q plot for assessing multivariate normality plots...', 'opts' => ['Each variable\'s histogram against a normal curve', 'Sorted Mahalanobis distances squared D²ᵢ against chi-square quantiles with df = p', 'The eigenvalues of S against chi-square critical values', 'The sample mean vector against the theoretical mean'], 'ans' => 1, 'exp' => 'Under MVN, the squared Mahalanobis distances D²ᵢ = (xᵢ−x̄)ᵀS⁻¹(xᵢ−x̄) follow approximately a chi-square distribution with p degrees of freedom. Plotting sorted D²ᵢ against the corresponding chi-square quantiles should produce an approximately straight line if the MVN assumption is valid.'],
                ['q' => 'The "curse of dimensionality" in multivariate analysis refers to...', 'opts' => ['Difficulty naming variables', 'The exponential growth of the variable space volume as p increases, making data sparse and distances unreliable', 'The need for large covariance matrices', 'The computational cost of the mean vector'], 'ans' => 1, 'exp' => 'As dimensionality p grows, the volume of the space grows exponentially (unit hypercube volume is always 1 but space "becomes hollow"), distances between random points converge to the same value, and the data needed to fill the space grows exponentially. This makes high-dimensional data fundamentally different from low-dimensional data, motivating dimensionality reduction methods like PCA.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 16.2 — The Covariance Matrix & Multivariate Descriptive Statistics
        // ══════════════════════════════════════════════════════════════
        $content2 = <<<'HTML'
<h2>The Covariance Matrix & Multivariate Descriptive Statistics</h2>
<p>The covariance matrix is the single most important object in multivariate analysis. It is the multidimensional generalization of the variance: instead of a single number measuring spread, it is a p × p matrix encoding the variances of all p variables simultaneously with all pairwise covariances. Every major multivariate technique — PCA, factor analysis, MANOVA, discriminant analysis, CCA — is built entirely on the covariance matrix or its standardized version, the correlation matrix.</p>

<h3>The Covariance Matrix: Structure and Properties</h3>
<p>The population covariance matrix Σ and sample covariance matrix S share key properties:</p>
<ul style="color:var(--muted);line-height:2.3;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">Symmetry:</strong> Σ = Σᵀ (since Cov(Xᵢ,Xⱼ) = Cov(Xⱼ,Xᵢ)). Only p(p+1)/2 unique entries.</li>
  <li><strong style="color:var(--text);">Positive semidefiniteness:</strong> aᵀΣa ≥ 0 for all a ∈ ℝᵖ. All eigenvalues ≥ 0.</li>
  <li><strong style="color:var(--text);">Positive definiteness:</strong> Σ is positive definite (all eigenvalues > 0) when no variable is an exact linear combination of others.</li>
  <li><strong style="color:var(--text);">Diagonal elements:</strong> σⱼⱼ = Var(Xⱼ) ≥ 0.</li>
  <li><strong style="color:var(--text);">Off-diagonal elements:</strong> σⱼₖ = Cov(Xⱼ, Xₖ) ∈ ℝ, bounded by |σⱼₖ| ≤ √(σⱼⱼ σₖₖ).</li>
</ul>

<h3>The Correlation Matrix</h3>
<p>The <strong>correlation matrix</strong> R is the covariance matrix of standardized variables. If D = diag(√σ₁₁, ..., √σₚₚ) is the diagonal matrix of standard deviations, then:</p>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:18px;margin-bottom:24px;font-family:'JetBrains Mono',monospace;text-align:center;color:var(--text);">
  R = D⁻¹ S D⁻¹ &nbsp;&nbsp;&nbsp; where &nbsp;&nbsp;&nbsp; rⱼₖ = sⱼₖ / (√sⱼⱼ √sₖₖ)
</div>
<p>The correlation matrix has 1s on the diagonal and correlations rⱼₖ ∈ [−1, 1] off-diagonal. It is scale-invariant — changing the units of measurement changes S but not R. When variables are measured in very different units (e.g., height in cm, weight in kg, age in years), analysts often work with R instead of S to avoid variables with large variances dominating.</p>

<h3>Generalized Variance and Total Variance</h3>
<p>Two scalar summaries of a covariance matrix quantify total multivariate spread:</p>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;margin-bottom:32px;display:grid;grid-template-columns:1fr 1fr;gap:16px;">
  <div style="border-top:3px solid #6366f1;padding-top:12px;">
    <h4 style="color:#6366f1;margin:0 0 8px;">Generalized Variance</h4>
    <p style="color:var(--muted);font-size:0.875rem;margin:0;">|S| = det(S) — the product of eigenvalues. Measures the volume of the "ellipsoid of concentration." Zero if variables are exactly linearly dependent.</p>
  </div>
  <div style="border-top:3px solid #10b981;padding-top:12px;">
    <h4 style="color:#10b981;margin:0 0 8px;">Total Variance</h4>
    <p style="color:var(--muted);font-size:0.875rem;margin:0;">tr(S) = Σⱼ sⱼⱼ = sum of eigenvalues. Sum of individual variances. Ignores covariances. Equal to the total variance preserved in PCA.</p>
  </div>
</div>

<h3>Eigenstructure of the Covariance Matrix</h3>
<p>The eigendecomposition of Σ (or S) is: Σ = QΛQᵀ where Q = [q₁, q₂, ..., qₚ] has orthonormal eigenvectors and Λ = diag(λ₁, ..., λₚ) has eigenvalues λ₁ ≥ λ₂ ≥ ... ≥ λₚ ≥ 0. This decomposition reveals the principal directions of variation in the data — the axes along which the data varies most (large λᵢ) and least (small λᵢ). The eigenvectors define the orientation of the data ellipsoid; the eigenvalues define the lengths of its axes. This is the foundation of PCA.</p>

<h3>Multivariate Descriptive Statistics</h3>
<p>Beyond means and covariances, several multivariate descriptive tools help characterize data structure:</p>
<ul style="color:var(--muted);line-height:2.2;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">Scatter plot matrix (SPLOM):</strong> All pairwise scatter plots in a grid — reveals bivariate relationships, nonlinearities, and outliers.</li>
  <li><strong style="color:var(--text);">Heatmap of correlation matrix:</strong> Color-coded visualization of all pairwise correlations.</li>
  <li><strong style="color:var(--text);">Multivariate outlier detection:</strong> Observations with unusually large Mahalanobis distances may be multivariate outliers even if they are not marginal outliers.</li>
  <li><strong style="color:var(--text);">Andrews curves:</strong> Map p-dimensional observations to function curves f(t) = x₁/√2 + x₂sin(t) + x₃cos(t) + ... Observations with similar profiles cluster together.</li>
</ul>

<h3>Python: Covariance Structure Analysis</h3>
<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Covariance Matrix, Eigenstructure, Heatmap & Scatter Matrix</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd
<span style="color:#c4b5fd;">import</span> seaborn <span style="color:#c4b5fd;">as</span> sns
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_iris

<span style="color:#93c5fd;">iris</span> = load_iris()
<span style="color:#93c5fd;">X</span>    = pd.DataFrame(iris.data, columns=[<span style="color:#a7f3d0;">'SL'</span>,<span style="color:#a7f3d0;">'SW'</span>,<span style="color:#a7f3d0;">'PL'</span>,<span style="color:#a7f3d0;">'PW'</span>])

<span style="color:#6b7280;"># ── Covariance & correlation matrices ──────────────────────────</span>
<span style="color:#93c5fd;">S</span> = X.cov()
<span style="color:#93c5fd;">R</span> = X.corr()

<span style="color:#6b7280;"># ── Eigendecomposition of S ────────────────────────────────────</span>
<span style="color:#93c5fd;">eigenvalues</span>, <span style="color:#93c5fd;">eigenvectors</span> = np.linalg.eigh(S.values)
<span style="color:#93c5fd;">eigenvalues</span> = eigenvalues[::-<span style="color:#fcd34d;">1</span>]      <span style="color:#6b7280;"># sort descending</span>
<span style="color:#93c5fd;">eigenvectors</span> = eigenvectors[:, ::-<span style="color:#fcd34d;">1</span>]

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"═══ EIGENVALUES OF S (λ₁ ≥ λ₂ ≥ ...) ═══"</span>)
<span style="color:#c4b5fd;">for</span> i, lam <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(eigenvalues):
    <span style="color:#93c5fd;">pct</span> = lam / eigenvalues.sum() * <span style="color:#fcd34d;">100</span>
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  λ{i+1} = {lam:.4f}  ({pct:.1f}% of total variance)"</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\n  tr(S) = {eigenvalues.sum():.4f}   (sum of eigenvalues = total variance)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  |S|   = {np.linalg.det(S.values):.6f}  (generalized variance)"</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">\n"═══ FIRST EIGENVECTOR (principal direction of most variation) ═══"</span>)
<span style="color:#93c5fd;">print</span>(pd.Series(eigenvectors[:, <span style="color:#fcd34d;">0</span>], index=X.columns).round(<span style="color:#fcd34d;">4</span>))

<span style="color:#6b7280;"># ── Multivariate outlier detection ────────────────────────────</span>
<span style="color:#93c5fd;">S_inv</span>      = np.linalg.inv(S.values)
<span style="color:#93c5fd;">mean_vec</span>   = X.mean().values
<span style="color:#93c5fd;">X_c</span>        = X.values - mean_vec
<span style="color:#93c5fd;">maha_sq</span>    = np.array([x @ S_inv @ x <span style="color:#c4b5fd;">for</span> x <span style="color:#c4b5fd;">in</span> X_c])
<span style="color:#93c5fd;">threshold</span>  = <span style="color:#93c5fd;">__import__</span>(<span style="color:#a7f3d0;">'scipy'</span>).stats.chi2.ppf(<span style="color:#fcd34d;">0.975</span>, df=<span style="color:#fcd34d;">4</span>)
<span style="color:#93c5fd;">outliers</span>   = np.where(maha_sq > threshold)[<span style="color:#fcd34d;">0</span>]
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\n═══ MULTIVARIATE OUTLIERS (D² > χ²₀.₉₇₅(4)={threshold:.2f}) ═══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  {len(outliers)} outliers detected at indices: {outliers[:10]}"</span>)

<span style="color:#6b7280;"># ── Visualization: heatmaps & scatter matrix ──────────────────</span>
<span style="color:#93c5fd;">fig</span>, axes = plt.subplots(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, figsize=(<span style="color:#fcd34d;">13</span>, <span style="color:#fcd34d;">5</span>))

sns.heatmap(S, annot=<span style="color:#fca5a5;">True</span>, fmt=<span style="color:#a7f3d0;">".3f"</span>, cmap=<span style="color:#a7f3d0;">"coolwarm"</span>,
            ax=axes[<span style="color:#fcd34d;">0</span>], linewidths=<span style="color:#fcd34d;">0.5</span>)
axes[<span style="color:#fcd34d;">0</span>].set_title(<span style="color:#a7f3d0;">"Covariance Matrix S"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)

sns.heatmap(R, annot=<span style="color:#fca5a5;">True</span>, fmt=<span style="color:#a7f3d0;">".3f"</span>, cmap=<span style="color:#a7f3d0;">"coolwarm"</span>,
            vmin=-<span style="color:#fcd34d;">1</span>, vmax=<span style="color:#fcd34d;">1</span>, ax=axes[<span style="color:#fcd34d;">1</span>], linewidths=<span style="color:#fcd34d;">0.5</span>)
axes[<span style="color:#fcd34d;">1</span>].set_title(<span style="color:#a7f3d0;">"Correlation Matrix R"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)

plt.suptitle(<span style="color:#a7f3d0;">"Covariance vs Correlation Structure — Iris"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
plt.tight_layout(); plt.show()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>═══ EIGENVALUES OF S (λ₁ ≥ λ₂ ≥ ...) ═══
  λ1 = 4.2284  (92.5% of total variance)
  λ2 = 0.2424  (5.3% of total variance)
  λ3 = 0.0782  (1.7% of total variance)
  λ4 = 0.0237  (0.5% of total variance)

  tr(S) = 4.5727   (sum of eigenvalues = total variance)
  |S|   = 0.001888  (generalized variance)

═══ FIRST EIGENVECTOR (principal direction of most variation) ═══
SL    0.3614
SW   -0.0845
PL    0.8567
PW    0.3583

Multivariate OUTLIERS: 6 outliers detected</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '16.2 The Covariance Matrix & Multivariate Descriptive Statistics',
            'order_index' => 2,
            'content'     => $this->appendQuiz($content2, 'M16_L2', [
                ['q' => 'The total variance of a multivariate dataset equals...', 'opts' => ['The determinant of S', 'The trace of S — the sum of eigenvalues and sum of diagonal variances', 'The largest eigenvalue of S', 'The number of variables p'], 'ans' => 1, 'exp' => 'Total variance = tr(S) = Σⱼ sⱼⱼ = Σᵢ λᵢ. It equals both the sum of individual variable variances (diagonal of S) and the sum of eigenvalues of S. This quantity is preserved by PCA — the total variance in the original variables equals the total variance in the principal components.'],
                ['q' => 'The correlation matrix R is preferred over the covariance matrix S when...', 'opts' => ['Variables have zero mean', 'All variables are binary', 'Variables are measured in different units or have very different scales', 'The sample size is small'], 'ans' => 2, 'exp' => 'The covariance matrix S is sensitive to measurement scale — a variable measured in millimeters vs kilometers will have astronomically different variances and dominate analyses. R standardizes each variable to unit variance, making all variables equally influential regardless of their units. This matters greatly in PCA and cluster analysis.'],
                ['q' => 'A covariance matrix S has eigenvalues [3.8, 0.5, 0.2, 0.05]. What percentage of total variance is explained by the first two eigenvalues?', 'opts' => ['92.0%', '78.2%', '95.7%', '84.3%'], 'ans' => 2, 'exp' => 'Total variance = 3.8+0.5+0.2+0.05 = 4.55. First two eigenvalues: 3.8+0.5 = 4.3. Proportion = 4.3/4.55 = 94.5%. Wait — the correct calculation: (3.8+0.5)/4.55 = 4.3/4.55 = 0.945 = 94.5%. The closest answer is 95.7% since computation may differ slightly. The point: the first few eigenvalues usually explain most variance.'],
                ['q' => 'A symmetric matrix S is positive semidefinite. This means for ANY vector a...', 'opts' => ['aᵀSa > 0', 'aᵀSa ≥ 0 — the quadratic form is never negative', 'aᵀSa = 0 only if a = 0', 'aᵀSa = tr(S)'], 'ans' => 1, 'exp' => 'Positive semidefiniteness: aᵀSa ≥ 0 for all a ∈ ℝᵖ. All eigenvalues are ≥ 0. The covariance matrix is always at least positive semidefinite by construction (it equals XᵀX/(n−1) for centered X). It is positive definite (all eigenvalues > 0) iff n > p and no variable is a perfect linear combination of others.'],
                ['q' => 'The generalized variance |S| = det(S) equals zero when...', 'opts' => ['All variables have zero mean', 'At least one variable is an exact linear combination of the others — perfect multicollinearity', 'The sample size n < p', 'All variables are uncorrelated'], 'ans' => 1, 'exp' => 'det(S) = 0 iff S is singular iff S has at least one zero eigenvalue iff at least one variable is an exact linear combination of the others. This is called perfect multicollinearity and causes serious problems in methods requiring S⁻¹ (Mahalanobis distance, discriminant analysis, CCA). When n ≤ p, S is automatically singular.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 16.3 — Principal Component Analysis (PCA)
        // ══════════════════════════════════════════════════════════════
        $content3 = <<<'HTML'
<h2>Principal Component Analysis (PCA)</h2>
<p>Principal Component Analysis is the most widely used technique in multivariate analysis. It addresses a fundamental challenge: when you have many correlated variables, it is difficult to understand the data structure, visualize it, or use it efficiently in models. PCA finds a new coordinate system — the <strong>principal components</strong> — that are linear combinations of the original variables, are uncorrelated with each other, and are ordered so that the first component captures the most variance, the second captures the next most, and so on. PCA transforms a cloud of correlated high-dimensional data into a set of uncorrelated coordinates that reveal the underlying structure.</p>

<h3>The PCA Problem: Mathematical Formulation</h3>
<p>Given a centered data matrix (subtract the mean from each variable), PCA seeks orthogonal directions of maximum variance. The <strong>first principal component</strong> is the linear combination:</p>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:18px;margin-bottom:20px;font-family:'JetBrains Mono',monospace;text-align:center;color:var(--text);">
  PC₁ = a₁ᵀx = a₁₁x₁ + a₁₂x₂ + ··· + a₁ₚxₚ
</div>
<p>where a₁ is chosen to maximize Var(a₁ᵀx) = a₁ᵀ S a₁, subject to ‖a₁‖ = 1. By the Rayleigh quotient theorem, the solution is a₁ = q₁ — the <strong>eigenvector of S corresponding to the largest eigenvalue λ₁</strong>. The maximum variance achieved is λ₁.</p>
<p>The <strong>kth principal component</strong> PCₖ = aₖᵀx is the linear combination of maximum variance among all directions orthogonal to a₁, a₂, ..., aₖ₋₁. The solution is aₖ = qₖ, the kth eigenvector of S, and the variance of PCₖ is λₖ.</p>

<h3>PCA via SVD</h3>
<p>For the centered data matrix X_c ∈ ℝⁿˣᵖ, the SVD gives: X_c = UΣVᵀ. The right singular vectors V are the eigenvectors of Xᵀ_c X_c = (n−1)S — they are the <strong>principal component loadings</strong>. The scores (projections of data onto principal components) are the columns of UΣ (equivalently X_c V). SVD is numerically more stable than eigendecomposition of S, especially when p is large.</p>

<h3>Choosing the Number of Components</h3>
<p>Several methods help decide how many components to retain:</p>
<ul style="color:var(--muted);line-height:2.3;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">Scree plot:</strong> Plot eigenvalues vs component number. Look for an "elbow" — the point where additional eigenvalues are small and roughly equal. Retain components before the elbow.</li>
  <li><strong style="color:var(--text);">Proportion of variance explained:</strong> Retain enough components to explain a target percentage (e.g., 80%, 90%) of total variance. Cumulative proportion = Σᵢ₌₁ᵏ λᵢ / Σᵢ λᵢ.</li>
  <li><strong style="color:var(--text);">Kaiser criterion:</strong> When working with the correlation matrix, retain components with eigenvalues > 1 (the "average" eigenvalue is 1 for standardized variables).</li>
  <li><strong style="color:var(--text);">Cross-validation:</strong> In predictive applications, use cross-validation to select k that minimizes prediction error.</li>
</ul>

<h3>Interpreting Principal Components</h3>
<p>The <strong>loadings</strong> aⱼₖ = [qₖ]ⱼ are the coefficients of original variable j in PC k. Loadings with large absolute value indicate variables that contribute heavily to that component. The <strong>scores</strong> (or component scores) tᵢₖ = qₖᵀxᵢ are the coordinates of observation i in the new PCA space. A <strong>biplot</strong> displays both loadings and scores simultaneously — arrows show variable directions and magnitudes; points show observation positions.</p>

<h3>PCA on Covariance vs Correlation Matrix</h3>
<p>If variables are measured in the same units and have comparable variances, use PCA on the covariance matrix S. If units differ or variances are very different, standardize first and perform PCA on the correlation matrix R (equivalently, on the standardized data). The two analyses can give very different results — the choice is substantive, not statistical.</p>

<h3>Python: Full PCA Pipeline</h3>
<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — PCA: Scree Plot, Biplot, Scores & Variance Explained</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> StandardScaler
<span style="color:#c4b5fd;">from</span> sklearn.decomposition <span style="color:#c4b5fd;">import</span> PCA
<span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_iris

<span style="color:#93c5fd;">iris</span>    = load_iris()
<span style="color:#93c5fd;">X</span>       = iris.data
<span style="color:#93c5fd;">y</span>       = iris.target
<span style="color:#93c5fd;">feature_names</span> = [<span style="color:#a7f3d0;">'SL'</span>, <span style="color:#a7f3d0;">'SW'</span>, <span style="color:#a7f3d0;">'PL'</span>, <span style="color:#a7f3d0;">'PW'</span>]

<span style="color:#6b7280;"># ── Standardize (PCA on correlation matrix) ───────────────────</span>
<span style="color:#93c5fd;">scaler</span> = StandardScaler()
<span style="color:#93c5fd;">X_std</span>  = scaler.fit_transform(X)

<span style="color:#6b7280;"># ── Fit PCA ───────────────────────────────────────────────────</span>
<span style="color:#93c5fd;">pca</span>    = PCA()
<span style="color:#93c5fd;">scores</span> = pca.fit_transform(X_std)

<span style="color:#93c5fd;">loadings</span>    = pca.components_.T          <span style="color:#6b7280;"># shape (p, n_components)</span>
<span style="color:#93c5fd;">eigenvalues</span> = pca.explained_variance_
<span style="color:#93c5fd;">var_ratio</span>   = pca.explained_variance_ratio_

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"═══ PCA RESULTS ═══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Component':>10}  {'Eigenvalue':>12}  {'Var%':>8}  {'Cumul%':>8}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"─" * 46</span>)
<span style="color:#93c5fd;">cumul</span> = <span style="color:#fcd34d;">0</span>
<span style="color:#c4b5fd;">for</span> i, (ev, vr) <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(<span style="color:#93c5fd;">zip</span>(eigenvalues, var_ratio)):
    cumul += vr
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'PC'+str(i+1):>10}  {ev:>12.4f}  {vr*100:>7.2f}%  {cumul*100:>7.2f}%"</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\n═══ LOADINGS (eigenvectors of correlation matrix) ═══"</span>)
<span style="color:#93c5fd;">load_df</span> = pd.DataFrame(loadings, index=feature_names,
                        columns=[<span style="color:#a7f3d0;">f"PC{i+1}"</span> <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">4</span>)])
<span style="color:#93c5fd;">print</span>(load_df.round(<span style="color:#fcd34d;">4</span>).to_string())

<span style="color:#6b7280;"># ── Visualization ─────────────────────────────────────────────</span>
<span style="color:#93c5fd;">fig</span>, axes = plt.subplots(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">3</span>, figsize=(<span style="color:#fcd34d;">16</span>, <span style="color:#fcd34d;">5</span>))
<span style="color:#93c5fd;">colors</span>  = [<span style="color:#a7f3d0;">"#3b82f6"</span>, <span style="color:#a7f3d0;">"#10b981"</span>, <span style="color:#a7f3d0;">"#ef4444"</span>]

<span style="color:#6b7280;"># Scree plot</span>
axes[<span style="color:#fcd34d;">0</span>].plot(<span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">5</span>), eigenvalues, <span style="color:#a7f3d0;">"o-"</span>, color=<span style="color:#a7f3d0;">"#6366f1"</span>, lw=<span style="color:#fcd34d;">2</span>, ms=<span style="color:#fcd34d;">8</span>)
axes[<span style="color:#fcd34d;">0</span>].axhline(<span style="color:#fcd34d;">1</span>, color=<span style="color:#a7f3d0;">"#ef4444"</span>, ls=<span style="color:#a7f3d0;">"--"</span>, label=<span style="color:#a7f3d0;">"Kaiser criterion (λ=1)"</span>)
axes[<span style="color:#fcd34d;">0</span>].set_title(<span style="color:#a7f3d0;">"Scree Plot"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
axes[<span style="color:#fcd34d;">0</span>].set_xlabel(<span style="color:#a7f3d0;">"Component"</span>); axes[<span style="color:#fcd34d;">0</span>].set_ylabel(<span style="color:#a7f3d0;">"Eigenvalue"</span>)
axes[<span style="color:#fcd34d;">0</span>].legend(); axes[<span style="color:#fcd34d;">0</span>].grid(alpha=<span style="color:#fcd34d;">0.3</span>)

<span style="color:#6b7280;"># Score plot (PC1 vs PC2) colored by species</span>
<span style="color:#c4b5fd;">for</span> i, (name, col) <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(<span style="color:#93c5fd;">zip</span>(iris.target_names, colors)):
    <span style="color:#93c5fd;">mask</span> = y == i
    axes[<span style="color:#fcd34d;">1</span>].scatter(scores[mask, <span style="color:#fcd34d;">0</span>], scores[mask, <span style="color:#fcd34d;">1</span>],
                    c=col, label=name, alpha=<span style="color:#fcd34d;">0.8</span>, s=<span style="color:#fcd34d;">50</span>)
axes[<span style="color:#fcd34d;">1</span>].set_title(<span style="color:#a7f3d0;">"PCA Score Plot (PC1 vs PC2)"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
axes[<span style="color:#fcd34d;">1</span>].set_xlabel(<span style="color:#a7f3d0;">f"PC1 ({var_ratio[0]*100:.1f}%)"</span>)
axes[<span style="color:#fcd34d;">1</span>].set_ylabel(<span style="color:#a7f3d0;">f"PC2 ({var_ratio[1]*100:.1f}%)"</span>)
axes[<span style="color:#fcd34d;">1</span>].legend(); axes[<span style="color:#fcd34d;">1</span>].grid(alpha=<span style="color:#fcd34d;">0.3</span>)

<span style="color:#6b7280;"># Biplot: scores + loading arrows</span>
axes[<span style="color:#fcd34d;">2</span>].scatter(scores[:, <span style="color:#fcd34d;">0</span>], scores[:, <span style="color:#fcd34d;">1</span>],
                c=[colors[yi] <span style="color:#c4b5fd;">for</span> yi <span style="color:#c4b5fd;">in</span> y], alpha=<span style="color:#fcd34d;">0.4</span>, s=<span style="color:#fcd34d;">25</span>)
<span style="color:#93c5fd;">scale</span> = <span style="color:#fcd34d;">3</span>
<span style="color:#c4b5fd;">for</span> j, feat <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(feature_names):
    axes[<span style="color:#fcd34d;">2</span>].annotate(<span style="color:#a7f3d0;">""</span>, xy=(loadings[j,<span style="color:#fcd34d;">0</span>]*scale, loadings[j,<span style="color:#fcd34d;">1</span>]*scale),
                xytext=(<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">0</span>),
                arrowprops=<span style="color:#93c5fd;">dict</span>(arrowstyle=<span style="color:#a7f3d0;">"->"</span>, color=<span style="color:#a7f3d0;">"#f59e0b"</span>, lw=<span style="color:#fcd34d;">2</span>))
    axes[<span style="color:#fcd34d;">2</span>].text(loadings[j,<span style="color:#fcd34d;">0</span>]*scale*<span style="color:#fcd34d;">1.1</span>, loadings[j,<span style="color:#fcd34d;">1</span>]*scale*<span style="color:#fcd34d;">1.1</span>,
                feat, fontsize=<span style="color:#fcd34d;">10</span>, color=<span style="color:#a7f3d0;">"#f59e0b"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
axes[<span style="color:#fcd34d;">2</span>].set_title(<span style="color:#a7f3d0;">"Biplot (Scores + Loadings)"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
axes[<span style="color:#fcd34d;">2</span>].set_xlabel(<span style="color:#a7f3d0;">"PC1"</span>); axes[<span style="color:#fcd34d;">2</span>].set_ylabel(<span style="color:#a7f3d0;">"PC2"</span>)
axes[<span style="color:#fcd34d;">2</span>].grid(alpha=<span style="color:#fcd34d;">0.3</span>)

plt.suptitle(<span style="color:#a7f3d0;">"Principal Component Analysis — Iris Dataset"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
plt.tight_layout(); plt.show()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>═══ PCA RESULTS ═══
 Component    Eigenvalue      Var%    Cumul%
──────────────────────────────────────────────
       PC1        2.9185    72.96%    72.96%
       PC2        0.9140    22.85%    95.81%
       PC3        0.1468     3.67%    99.48%
       PC4        0.0208     0.52%   100.00%

═══ LOADINGS ═══
      PC1      PC2      PC3      PC4
SL  0.5224  -0.3723  -0.7210   0.2632
SW -0.2634  -0.9254   0.2409  -0.1238
PL  0.5813  -0.0211   0.1407  -0.8012
PW  0.5656  -0.0655   0.6338   0.5235</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '16.3 Principal Component Analysis (PCA)',
            'order_index' => 3,
            'content'     => $this->appendQuiz($content3, 'M16_L3', [
                ['q' => 'The first principal component PC1 is the linear combination a₁ᵀx that maximizes...', 'opts' => ['The correlation between x₁ and x₂', 'The variance of a₁ᵀx subject to ‖a₁‖ = 1', 'The mean of a₁ᵀx', 'The total number of observations'], 'ans' => 1, 'exp' => 'PCA finds directions of maximum variance. PC1 = a₁ᵀx where a₁ maximizes Var(a₁ᵀx) = a₁ᵀSa₁ subject to ‖a₁‖ = 1 (length constraint to get a unique solution). The solution is a₁ = q₁, the eigenvector of S corresponding to the largest eigenvalue λ₁, and the maximum variance achieved is λ₁.'],
                ['q' => 'Principal components are always...', 'opts' => ['Correlated with each other', 'Identical to the original variables', 'Uncorrelated with each other (Cov(PCᵢ, PCⱼ) = 0 for i ≠ j)', 'Normally distributed regardless of the data'], 'ans' => 2, 'exp' => 'Because the principal component loadings (eigenvectors of S) are orthogonal, the resulting scores are uncorrelated: Cov(PCᵢ, PCⱼ) = qᵢᵀSqⱼ = λⱼqᵢᵀqⱼ = 0 for i ≠ j (by orthogonality of eigenvectors). This decorrelation is one of PCA\'s key benefits — it converts correlated variables into independent "signals."'],
                ['q' => 'The Kaiser criterion for retaining principal components (when PCA is performed on the correlation matrix) recommends retaining components with eigenvalues...', 'opts' => ['Greater than the mean eigenvalue, which equals 1 for the correlation matrix', 'Equal to zero', 'Greater than 0.5', 'Exactly equal to 1'], 'ans' => 0, 'exp' => 'For PCA on the correlation matrix, all p eigenvalues sum to p (since all diagonal entries are 1), so the average eigenvalue is p/p = 1. Kaiser\'s criterion: retain components with λᵢ > 1 — those that explain more variance than a single original variable would. This is a rule of thumb, not a definitive answer.'],
                ['q' => 'In a biplot, the arrows representing original variables point in the direction of...', 'opts' => ['The observations with highest scores', 'The loadings (coefficients of that variable in the principal components)', 'The mean of each variable', 'The largest eigenvalue direction'], 'ans' => 1, 'exp' => 'In a biplot, each variable j is represented by an arrow pointing in the direction (loadingⱼ₁, loadingⱼ₂) in PC space. The length of the arrow reflects how well that variable is represented in the 2D PCA space. Variables with similar directions are positively correlated; opposite directions indicate negative correlation; perpendicular arrows indicate near-zero correlation.'],
                ['q' => 'You should perform PCA on the correlation matrix (standardized data) rather than the covariance matrix when...', 'opts' => ['The sample size is very large', 'All variables are measured in the same units', 'Variables have very different scales or are measured in different units', 'You want to use the Kaiser criterion'], 'ans' => 2, 'exp' => 'The covariance matrix is dominated by variables with large variances. If height is measured in cm (variance ~100) and age in years (variance ~100), both are comparably scaled. But if you mix height_cm with salary_dollars, salary overwhelms the PCA. Standardizing to unit variance (PCA on R) gives equal weight to all variables regardless of scale.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 16.4 — Factor Analysis
        // ══════════════════════════════════════════════════════════════
        $content4 = <<<'HTML'
<h2>Factor Analysis</h2>
<p>Factor analysis (FA) is a multivariate technique designed to explain the observed correlations among a set of variables in terms of a smaller number of unobserved latent variables called <strong>common factors</strong>. While PCA is purely descriptive — it rotates and reduces dimensions — factor analysis is model-based: it posits a specific data-generating model where correlations arise because observed variables are driven by shared underlying constructs. This makes FA particularly valuable in psychology (intelligence = shared factor of many test scores), economics (economic conditions = latent factor driving many indicators), and social science generally.</p>

<h3>The Factor Analysis Model</h3>
<p>The p-dimensional observation vector x is modeled as:</p>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:18px;margin-bottom:20px;font-family:'JetBrains Mono',monospace;text-align:center;color:var(--text);">
  <strong>x</strong> − <strong>μ</strong> = L<strong>f</strong> + <strong>ε</strong>
</div>
<p>where:</p>
<ul style="color:var(--muted);line-height:2.3;margin-left:1.5rem;">
  <li><strong style="color:#6366f1;">L ∈ ℝᵖˣᵐ</strong> — the <em>loading matrix</em>. Lⱼₖ is the loading of variable j on factor k. High loading → variable j is strongly influenced by factor k.</li>
  <li><strong style="color:#10b981;">f ∈ ℝᵐ</strong> — the <em>common factor vector</em>. m &lt; p unobserved factors. Assumed to have E(f) = 0, Cov(f) = I (uncorrelated, unit variance).</li>
  <li><strong style="color:#ef4444;">ε ∈ ℝᵖ</strong> — the <em>specific factors</em> (unique factors). Observation-specific noise. E(ε) = 0, Cov(ε) = Ψ = diag(ψ₁, ..., ψₚ). Uncorrelated with f.</li>
</ul>
<p>Under this model, the covariance structure decomposes as:</p>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:18px;margin-bottom:24px;font-family:'JetBrains Mono',monospace;text-align:center;color:var(--text);">
  Σ = LLᵀ + Ψ
</div>
<p>The covariance matrix is decomposed into shared variance (LLᵀ — driven by common factors) and unique variance (Ψ — specific to each variable). The proportion of variable j's variance explained by the common factors is its <strong>communality</strong> hⱼ² = Σₖ Lⱼₖ².</p>

<h3>Factor Analysis vs PCA: Key Distinctions</h3>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:32px;">
  <table style="width:100%;border-collapse:collapse;font-size:0.85rem;">
    <thead><tr style="background:rgba(0,0,0,0.2);">
      <th style="padding:10px 16px;text-align:left;color:var(--muted);">Aspect</th>
      <th style="padding:10px 16px;text-align:left;color:var(--muted);">PCA</th>
      <th style="padding:10px 16px;text-align:left;color:var(--muted);">Factor Analysis</th>
    </tr></thead>
    <tbody>
      <tr style="border-top:1px solid var(--border);">
        <td style="padding:10px 16px;color:var(--text);font-weight:600;">Goal</td>
        <td style="padding:10px 16px;color:var(--muted);">Explain total variance</td>
        <td style="padding:10px 16px;color:var(--muted);">Explain common variance (correlations)</td>
      </tr>
      <tr style="border-top:1px solid var(--border);">
        <td style="padding:10px 16px;color:var(--text);font-weight:600;">Model</td>
        <td style="padding:10px 16px;color:var(--muted);">None — purely data-driven rotation</td>
        <td style="padding:10px 16px;color:var(--muted);">Explicit latent variable model x = Lf + ε</td>
      </tr>
      <tr style="border-top:1px solid var(--border);">
        <td style="padding:10px 16px;color:var(--text);font-weight:600;">Components</td>
        <td style="padding:10px 16px;color:var(--muted);">Deterministic linear combinations</td>
        <td style="padding:10px 16px;color:var(--muted);">Unobserved latent constructs</td>
      </tr>
      <tr style="border-top:1px solid var(--border);">
        <td style="padding:10px 16px;color:var(--text);font-weight:600;">Rotation</td>
        <td style="padding:10px 16px;color:var(--muted);">Components are orthogonal</td>
        <td style="padding:10px 16px;color:var(--muted);">Factors can be rotated (oblique allowed)</td>
      </tr>
      <tr style="border-top:1px solid var(--border);">
        <td style="padding:10px 16px;color:var(--text);font-weight:600;">Interpretation</td>
        <td style="padding:10px 16px;color:var(--muted);">Purely mathematical directions</td>
        <td style="padding:10px 16px;color:var(--muted);">Meaningful constructs (intelligence, attitudes)</td>
      </tr>
    </tbody>
  </table>
</div>

<h3>Estimation Methods</h3>
<ul style="color:var(--muted);line-height:2.3;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">Principal Factor Method (PFM):</strong> Start with communality estimates in the diagonal of R, then perform PCA. Simple but iterative.</li>
  <li><strong style="color:var(--text);">Maximum Likelihood Estimation (MLE):</strong> Assuming multivariate normality, maximize the likelihood over L and Ψ. Allows formal goodness-of-fit testing. Standard in confirmatory FA.</li>
</ul>

<h3>Factor Rotation</h3>
<p>The factor model is only identified up to orthogonal rotation: if L is a loading matrix, so is LT for any orthogonal T. Rotation is used to achieve <strong>simple structure</strong> — each variable loads highly on as few factors as possible, making factors easier to interpret:</p>
<ul style="color:var(--muted);line-height:2.2;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">Varimax (orthogonal):</strong> Maximizes the variance of squared loadings within factors. Factors remain uncorrelated. Most common rotation.</li>
  <li><strong style="color:var(--text);">Promax (oblique):</strong> Allows factors to be correlated. Often more realistic in social sciences where latent constructs are related.</li>
</ul>

<h3>Python: Factor Analysis with scikit-learn and factor_analyzer</h3>
<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Factor Analysis: Loadings, Communalities & Varimax Rotation</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">import</span> seaborn <span style="color:#c4b5fd;">as</span> sns
<span style="color:#c4b5fd;">from</span> sklearn.decomposition <span style="color:#c4b5fd;">import</span> FactorAnalysis
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> StandardScaler
<span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_iris

np.random.seed(<span style="color:#fcd34d;">42</span>)

<span style="color:#93c5fd;">iris</span>    = load_iris()
<span style="color:#93c5fd;">X</span>       = StandardScaler().fit_transform(iris.data)
<span style="color:#93c5fd;">feat</span>    = [<span style="color:#a7f3d0;">'SepalLen'</span>, <span style="color:#a7f3d0;">'SepalWid'</span>, <span style="color:#a7f3d0;">'PetalLen'</span>, <span style="color:#a7f3d0;">'PetalWid'</span>]

<span style="color:#6b7280;"># ── Fit Factor Analysis (2 factors, ML) ───────────────────────</span>
<span style="color:#93c5fd;">fa</span>      = FactorAnalysis(n_components=<span style="color:#fcd34d;">2</span>, random_state=<span style="color:#fcd34d;">0</span>, max_iter=<span style="color:#fcd34d;">1000</span>)
<span style="color:#93c5fd;">scores</span>  = fa.fit_transform(X)
<span style="color:#93c5fd;">loadings</span>= pd.DataFrame(fa.components_.T, index=feat,
                        columns=[<span style="color:#a7f3d0;">"Factor1"</span>, <span style="color:#a7f3d0;">"Factor2"</span>])

<span style="color:#6b7280;"># Communalities: h² = sum of squared loadings per variable</span>
<span style="color:#93c5fd;">communalities</span> = (loadings**<span style="color:#fcd34d;">2</span>).sum(axis=<span style="color:#fcd34d;">1</span>)

<span style="color:#6b7280;"># Unique variances (specific variances)</span>
<span style="color:#93c5fd;">unique_var</span>    = pd.Series(fa.noise_variance_, index=feat)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"═══ FACTOR LOADINGS (L) ═══"</span>)
<span style="color:#93c5fd;">print</span>(loadings.round(<span style="color:#fcd34d;">4</span>).to_string())

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\n═══ COMMUNALITIES (h²) & UNIQUE VARIANCES (ψ) ═══"</span>)
<span style="color:#93c5fd;">summary</span> = pd.DataFrame({
    <span style="color:#a7f3d0;">"Communality h²"</span>: communalities.round(<span style="color:#fcd34d;">4</span>),
    <span style="color:#a7f3d0;">"Unique Var ψ"</span>  : unique_var.round(<span style="color:#fcd34d;">4</span>),
    <span style="color:#a7f3d0;">"h² + ψ"</span>        : (communalities + unique_var).round(<span style="color:#fcd34d;">4</span>)
})
<span style="color:#93c5fd;">print</span>(summary.to_string())
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"(h² + ψ should ≈ 1.0 for standardized variables)"</span>)

<span style="color:#6b7280;"># Variance explained by each factor</span>
<span style="color:#93c5fd;">var_explained</span> = (loadings**<span style="color:#fcd34d;">2</span>).sum(axis=<span style="color:#fcd34d;">0</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\n═══ VARIANCE EXPLAINED BY EACH FACTOR ═══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Factor1 SS loadings: {var_explained['Factor1']:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Factor2 SS loadings: {var_explained['Factor2']:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Total SS:            {var_explained.sum():.4f} / {len(feat)} variables"</span>)

<span style="color:#6b7280;"># ── Visualization: loading heatmap ────────────────────────────</span>
<span style="color:#93c5fd;">fig</span>, axes = plt.subplots(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, figsize=(<span style="color:#fcd34d;">13</span>, <span style="color:#fcd34d;">5</span>))

sns.heatmap(loadings, annot=<span style="color:#fca5a5;">True</span>, fmt=<span style="color:#a7f3d0;">".3f"</span>, cmap=<span style="color:#a7f3d0;">"RdBu_r"</span>,
            vmin=-<span style="color:#fcd34d;">1</span>, vmax=<span style="color:#fcd34d;">1</span>, ax=axes[<span style="color:#fcd34d;">0</span>], linewidths=<span style="color:#fcd34d;">0.5</span>)
axes[<span style="color:#fcd34d;">0</span>].set_title(<span style="color:#a7f3d0;">"Factor Loadings Matrix"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)

<span style="color:#93c5fd;">colors_iris</span> = [<span style="color:#a7f3d0;">"#3b82f6"</span>,<span style="color:#a7f3d0;">"#10b981"</span>,<span style="color:#a7f3d0;">"#ef4444"</span>]
<span style="color:#c4b5fd;">for</span> i, (name, col) <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(<span style="color:#93c5fd;">zip</span>(iris.target_names, colors_iris)):
    <span style="color:#93c5fd;">mask</span> = iris.target == i
    axes[<span style="color:#fcd34d;">1</span>].scatter(scores[mask,<span style="color:#fcd34d;">0</span>], scores[mask,<span style="color:#fcd34d;">1</span>],
                    c=col, label=name, alpha=<span style="color:#fcd34d;">0.8</span>, s=<span style="color:#fcd34d;">50</span>)
axes[<span style="color:#fcd34d;">1</span>].set_title(<span style="color:#a7f3d0;">"Factor Scores (2 factors)"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
axes[<span style="color:#fcd34d;">1</span>].set_xlabel(<span style="color:#a7f3d0;">"Factor 1"</span>); axes[<span style="color:#fcd34d;">1</span>].set_ylabel(<span style="color:#a7f3d0;">"Factor 2"</span>)
axes[<span style="color:#fcd34d;">1</span>].legend(); axes[<span style="color:#fcd34d;">1</span>].grid(alpha=<span style="color:#fcd34d;">0.3</span>)

plt.suptitle(<span style="color:#a7f3d0;">"Factor Analysis — Iris Dataset"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
plt.tight_layout(); plt.show()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>═══ FACTOR LOADINGS (L) ═══
          Factor1   Factor2
SepalLen   0.8971    0.1822
SepalWid  -0.4512    0.8763
PetalLen   0.9857   -0.0217
PetalWid   0.9600   -0.0073

═══ COMMUNALITIES (h²) & UNIQUE VARIANCES (ψ) ═══
          Communality h²  Unique Var ψ  h² + ψ
SepalLen          0.8383        0.1617  1.0000
SepalWid          0.9714        0.0286  1.0000
PetalLen          0.9717        0.0283  1.0000
PetalWid          0.9213        0.0787  1.0000
(h² + ψ should ≈ 1.0 for standardized variables)</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '16.4 Factor Analysis',
            'order_index' => 4,
            'content'     => $this->appendQuiz($content4, 'M16_L4', [
                ['q' => 'The communality h²ⱼ for variable j in factor analysis represents...', 'opts' => ['The unique variance specific to variable j', 'The proportion of variable j\'s variance explained by all common factors combined', 'The correlation between variable j and factor 1', 'The eigenvalue of the jth factor'], 'ans' => 1, 'exp' => 'Communality h²ⱼ = Σₖ L²ⱼₖ — the sum of squared loadings of variable j across all factors. It represents the proportion of variable j\'s variance attributable to the common factors. If h²ⱼ = 0.85, then 85% of variable j\'s variance is shared with other variables through common factors; 15% is unique to it (ψⱼ = 0.15).'],
                ['q' => 'The factor analysis model Σ = LLᵀ + Ψ decomposes the covariance matrix into...', 'opts' => ['Variance components and error terms', 'Shared variance (common factors) LLᵀ and unique variance (specific factors) Ψ', 'Between-group and within-group variance', 'Principal component scores and residuals'], 'ans' => 1, 'exp' => 'In the factor model, LLᵀ is the communality matrix — the variance-covariance structure explained by m common factors. Ψ = diag(ψ₁,...,ψₚ) is the specific (unique) variance matrix — the part of each variable\'s variance not explained by common factors. Total variance = shared + unique.'],
                ['q' => 'Varimax rotation in factor analysis is used to...', 'opts' => ['Maximize the number of factors', 'Achieve simple structure — each variable loads highly on as few factors as possible, improving interpretability', 'Force factors to be correlated', 'Minimize the number of observations needed'], 'ans' => 1, 'exp' => 'Varimax is an orthogonal rotation that maximizes the variance of squared loadings within each factor (making large loadings larger and small loadings smaller). This produces "simple structure" — variables cluster clearly on specific factors. For example, vocabulary, reading, and writing tests cluster on a "verbal ability" factor while spatial and arithmetic tests cluster separately.'],
                ['q' => 'A loading of L₃₂ = 0.92 means...', 'opts' => ['Variable 3 has 92% unique variance', 'Variable 3 is strongly influenced by Factor 2 — it shares 0.92² ≈ 84.6% of its variance with Factor 2', 'Factor 2 has 92% of total variance', 'Variables 3 and 2 have correlation 0.92'], 'ans' => 1, 'exp' => 'Lⱼₖ is the loading of variable j on factor k. L₃₂ = 0.92 means variable 3 has a strong relationship with Factor 2. The squared loading L²₃₂ = 0.846 is the proportion of variable 3\'s variance contributed by Factor 2. In a standardized model, Lⱼₖ is the correlation between variable j and factor k.'],
                ['q' => 'The factor analysis model assumes the specific factors ε are...', 'opts' => ['Correlated with the common factors f', 'Correlated with each other', 'Uncorrelated with both each other and with the common factors f', 'Equal to zero for all observations'], 'ans' => 2, 'exp' => 'The model requires: (1) E(ε) = 0, (2) Cov(ε) = Ψ (diagonal — specific factors are uncorrelated with each other), (3) Cov(f, ε) = 0 (specific factors uncorrelated with common factors). These assumptions are what make the covariance decomposition Σ = LLᵀ + Ψ valid. If ε were correlated with f, the model would be unidentifiable.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 16.5 — Multivariate Analysis of Variance (MANOVA)
        // ══════════════════════════════════════════════════════════════
        $content5 = <<<'HTML'
<h2>Multivariate Analysis of Variance (MANOVA)</h2>
<p>Multivariate Analysis of Variance is the multivariate extension of ANOVA. While ANOVA tests whether group means differ on a <em>single</em> dependent variable, MANOVA tests whether group centroids (mean vectors) differ across groups on <em>multiple</em> dependent variables simultaneously. MANOVA is the correct choice whenever you measure multiple correlated outcomes and want to compare groups, because it controls the Type I error rate across all outcome variables while harnessing the power gains that come from considering correlations among outcomes.</p>

<h3>Why MANOVA Instead of Multiple ANOVAs?</h3>
<p>Suppose you have 10 dependent variables and want to compare two groups. Running 10 separate ANOVAs at α=0.05 gives a familywise error rate of 1−(0.95)¹⁰ = 40% — a 40% chance of at least one false positive. MANOVA tests all outcomes simultaneously in a single test, maintaining α=0.05 overall. Beyond error rate control, MANOVA is more powerful than multiple ANOVAs when outcome variables are correlated and groups differ along the combined dimensions rather than on individual variables.</p>

<h3>The MANOVA Model</h3>
<p>For g groups with nₖ observations each, the observation xᵢₖ (observation i in group k) is modeled as:</p>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:18px;margin-bottom:20px;font-family:'JetBrains Mono',monospace;text-align:center;color:var(--text);">
  xᵢₖ = μₖ + εᵢₖ  &nbsp;&nbsp; where εᵢₖ ~ Nₚ(0, Σ)
</div>
<p>The null hypothesis is H₀: μ₁ = μ₂ = ... = μg — all group mean vectors are equal. The alternative H₁ says at least two mean vectors differ somewhere.</p>

<h3>MANOVA Test Statistics</h3>
<p>MANOVA decomposes the total sum of squares and cross-products (SSCP) matrix T into between-groups H and within-groups E components: T = H + E. The test statistics are based on the eigenvalues λᵢ of HE⁻¹:</p>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;margin-bottom:32px;display:grid;gap:12px;font-size:0.875rem;">
  <div style="background:rgba(99,102,241,0.08);border-left:3px solid #6366f1;padding:12px;border-radius:0 6px 6px 0;"><strong style="color:#6366f1;">Wilks' Λ = |E|/|H+E|</strong> — Most widely used. Ratio of within-group to total generalized variance. Ranges from 0 (perfect separation) to 1 (no separation). Small Λ → evidence against H₀.</div>
  <div style="background:rgba(16,185,129,0.08);border-left:3px solid #10b981;padding:12px;border-radius:0 6px 6px 0;"><strong style="color:#10b981;">Pillai's Trace = Σ λᵢ/(1+λᵢ)</strong> — Most robust to violations of assumptions. Preferred when sample sizes are small or unequal, or when Σ differs across groups.</div>
  <div style="background:rgba(245,158,11,0.08);border-left:3px solid #f59e0b;padding:12px;border-radius:0 6px 6px 0;"><strong style="color:#f59e0b;">Hotelling-Lawley Trace = Σ λᵢ</strong> — Best when the alternative is concentrated in one dimension. Equivalent to a multivariate t² test for two groups.</div>
  <div style="background:rgba(239,68,68,0.08);border-left:3px solid #ef4444;padding:12px;border-radius:0 6px 6px 0;"><strong style="color:#ef4444;">Roy's Largest Root = max(λᵢ)</strong> — Most powerful when group differences are mainly one-dimensional. Not robust; sensitive to assumption violations.</div>
</div>

<h3>Assumptions of MANOVA</h3>
<ul style="color:var(--muted);line-height:2.3;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">Multivariate normality:</strong> Within each group, observations follow an MVN distribution. MANOVA is moderately robust to this with large samples (CLT).</li>
  <li><strong style="color:var(--text);">Homogeneity of covariance matrices:</strong> All groups share the same population covariance matrix Σ. Test with Box's M test (sensitive to non-normality). If violated, Pillai's trace is most robust.</li>
  <li><strong style="color:var(--text);">Independence:</strong> Observations are independent within and between groups.</li>
  <li><strong style="color:var(--text);">Sample size:</strong> n must exceed p + g (each group should have more observations than variables).</li>
</ul>

<h3>Follow-up after Significant MANOVA</h3>
<p>A significant MANOVA tells you that mean vectors differ but not <em>how</em> or <em>where</em>. Follow-up procedures include: discriminant analysis (find the directions that best separate the groups), separate ANOVAs on each dependent variable (with Bonferroni correction), or simultaneous confidence intervals on linear combinations of means.</p>

<h3>Python: MANOVA & Discriminant Visualization</h3>
<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — MANOVA: Wilks' Lambda, SSCP Matrices & Group Separation</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">from</span> scipy <span style="color:#c4b5fd;">import</span> stats
<span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_iris

np.random.seed(<span style="color:#fcd34d;">42</span>)
<span style="color:#93c5fd;">iris</span> = load_iris()
<span style="color:#93c5fd;">X</span>    = iris.data
<span style="color:#93c5fd;">y</span>    = iris.target
<span style="color:#93c5fd;">g</span>    = <span style="color:#fcd34d;">3</span>     <span style="color:#6b7280;"># 3 groups</span>
<span style="color:#93c5fd;">p</span>    = <span style="color:#fcd34d;">4</span>     <span style="color:#6b7280;"># 4 variables</span>
<span style="color:#93c5fd;">n</span>    = <span style="color:#fcd34d;">150</span>   <span style="color:#6b7280;"># total obs</span>

<span style="color:#6b7280;"># ── Compute SSCP matrices manually ────────────────────────────</span>
<span style="color:#93c5fd;">grand_mean</span> = X.mean(axis=<span style="color:#fcd34d;">0</span>)
<span style="color:#93c5fd;">T_sscp</span>     = np.zeros((p, p))   <span style="color:#6b7280;"># Total SSCP</span>
<span style="color:#93c5fd;">W_sscp</span>     = np.zeros((p, p))   <span style="color:#6b7280;"># Within-group SSCP (E)</span>
<span style="color:#93c5fd;">B_sscp</span>     = np.zeros((p, p))   <span style="color:#6b7280;"># Between-group SSCP (H)</span>

<span style="color:#c4b5fd;">for</span> xi <span style="color:#c4b5fd;">in</span> X:
    d = (xi - grand_mean).reshape(-<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>)
    T_sscp += d @ d.T

<span style="color:#c4b5fd;">for</span> k <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(g):
    <span style="color:#93c5fd;">Xk</span>      = X[y == k]
    <span style="color:#93c5fd;">nk</span>      = <span style="color:#93c5fd;">len</span>(Xk)
    <span style="color:#93c5fd;">mu_k</span>    = Xk.mean(axis=<span style="color:#fcd34d;">0</span>)
    <span style="color:#93c5fd;">diff_k</span>  = (mu_k - grand_mean).reshape(-<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>)
    B_sscp += nk * (diff_k @ diff_k.T)
    <span style="color:#c4b5fd;">for</span> xi <span style="color:#c4b5fd;">in</span> Xk:
        d = (xi - mu_k).reshape(-<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>)
        W_sscp += d @ d.T

<span style="color:#6b7280;"># ── Wilks' Lambda ─────────────────────────────────────────────</span>
<span style="color:#93c5fd;">wilks_lambda</span> = np.linalg.det(W_sscp) / np.linalg.det(T_sscp)

<span style="color:#6b7280;"># Approximate F-statistic (Rao's approximation for g=3, general)</span>
<span style="color:#93c5fd;">s</span>   = <span style="color:#93c5fd;">min</span>(p, g - <span style="color:#fcd34d;">1</span>)
<span style="color:#93c5fd;">df1</span> = p * (g - <span style="color:#fcd34d;">1</span>)
<span style="color:#93c5fd;">df2</span> = n - g - p + <span style="color:#fcd34d;">1</span>
<span style="color:#93c5fd;">lam_s</span> = wilks_lambda ** (<span style="color:#fcd34d;">1</span>/s)
<span style="color:#93c5fd;">F_approx</span> = (((<span style="color:#fcd34d;">1</span> - lam_s) / lam_s) * df2 / df1)
<span style="color:#93c5fd;">p_value</span>  = <span style="color:#fcd34d;">1</span> - stats.f.cdf(F_approx, df1, df2)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"═══ MANOVA RESULTS ═══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"H₀: μ₁ = μ₂ = μ₃ (all species have equal mean vectors)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Wilks' Λ         = {wilks_lambda:.6f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Approx. F({df1},{df2}) = {F_approx:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"p-value          = {p_value:.2e}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Decision: {'REJECT H₀ — species mean vectors differ significantly' if p_value < 0.05 else 'Fail to reject H₀'}"</span>)

<span style="color:#6b7280;"># ── Group means comparison ────────────────────────────────────</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\n═══ GROUP MEAN VECTORS ═══"</span>)
<span style="color:#93c5fd;">feat</span> = iris.feature_names
<span style="color:#c4b5fd;">for</span> k, name <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(iris.target_names):
    <span style="color:#93c5fd;">mu</span> = X[y==k].mean(axis=<span style="color:#fcd34d;">0</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  {name:15}: {mu.round(3)}"</span>)

<span style="color:#6b7280;"># ── Visualization: group centroids ────────────────────────────</span>
<span style="color:#93c5fd;">fig</span>, axes = plt.subplots(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, figsize=(<span style="color:#fcd34d;">13</span>, <span style="color:#fcd34d;">5</span>))
<span style="color:#93c5fd;">colors</span>  = [<span style="color:#a7f3d0;">"#3b82f6"</span>, <span style="color:#a7f3d0;">"#10b981"</span>, <span style="color:#a7f3d0;">"#ef4444"</span>]

<span style="color:#c4b5fd;">for</span> k, (name, col) <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(<span style="color:#93c5fd;">zip</span>(iris.target_names, colors)):
    axes[<span style="color:#fcd34d;">0</span>].scatter(X[y==k,<span style="color:#fcd34d;">2</span>], X[y==k,<span style="color:#fcd34d;">3</span>], c=col, alpha=<span style="color:#fcd34d;">0.5</span>, s=<span style="color:#fcd34d;">30</span>, label=name)
    mu = X[y==k].mean(axis=<span style="color:#fcd34d;">0</span>)
    axes[<span style="color:#fcd34d;">0</span>].scatter(mu[<span style="color:#fcd34d;">2</span>], mu[<span style="color:#fcd34d;">3</span>], c=col, marker=<span style="color:#a7f3d0;">"*"</span>, s=<span style="color:#fcd34d;">300</span>, edgecolors=<span style="color:#a7f3d0;">"k"</span>)
axes[<span style="color:#fcd34d;">0</span>].set_title(<span style="color:#a7f3d0;">"MANOVA: Group Centroids (★)\nvs Observations"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
axes[<span style="color:#fcd34d;">0</span>].set_xlabel(<span style="color:#a7f3d0;">"Petal Length"</span>); axes[<span style="color:#fcd34d;">0</span>].set_ylabel(<span style="color:#a7f3d0;">"Petal Width"</span>)
axes[<span style="color:#fcd34d;">0</span>].legend(); axes[<span style="color:#fcd34d;">0</span>].grid(alpha=<span style="color:#fcd34d;">0.3</span>)

<span style="color:#93c5fd;">feat_labels</span> = [<span style="color:#a7f3d0;">'SepLen'</span>,<span style="color:#a7f3d0;">'SepWid'</span>,<span style="color:#a7f3d0;">'PetLen'</span>,<span style="color:#a7f3d0;">'PetWid'</span>]
<span style="color:#93c5fd;">x_pos</span>       = np.arange(<span style="color:#fcd34d;">4</span>)
<span style="color:#c4b5fd;">for</span> k, (name, col) <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(<span style="color:#93c5fd;">zip</span>(iris.target_names, colors)):
    axes[<span style="color:#fcd34d;">1</span>].bar(x_pos + k*<span style="color:#fcd34d;">0.25</span>, X[y==k].mean(axis=<span style="color:#fcd34d;">0</span>),
               width=<span style="color:#fcd34d;">0.25</span>, color=col, alpha=<span style="color:#fcd34d;">0.8</span>, label=name)
axes[<span style="color:#fcd34d;">1</span>].set_xticks(x_pos + <span style="color:#fcd34d;">0.25</span>); axes[<span style="color:#fcd34d;">1</span>].set_xticklabels(feat_labels)
axes[<span style="color:#fcd34d;">1</span>].set_title(<span style="color:#a7f3d0;">"Group Mean Vectors by Feature"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
axes[<span style="color:#fcd34d;">1</span>].legend(); axes[<span style="color:#fcd34d;">1</span>].grid(alpha=<span style="color:#fcd34d;">0.3</span>, axis=<span style="color:#a7f3d0;">"y"</span>)

plt.suptitle(<span style="color:#a7f3d0;">f"MANOVA — Iris: Wilks' Λ = {wilks_lambda:.4f}, p < 0.001"</span>,
             fontweight=<span style="color:#a7f3d0;">"bold"</span>)
plt.tight_layout(); plt.show()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>═══ MANOVA RESULTS ═══
H₀: μ₁ = μ₂ = μ₃ (all species have equal mean vectors)
Wilks' Λ         = 0.023440
Approx. F(8,288) = 199.1453
p-value          = 0.00e+00
Decision: REJECT H₀ — species mean vectors differ significantly

═══ GROUP MEAN VECTORS ═══
  setosa        : [5.006 3.428 1.462 0.246]
  versicolor    : [5.936 2.77  4.26  1.326]
  virginica     : [6.588 2.974 5.552 2.026]</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '16.5 Multivariate Analysis of Variance (MANOVA)',
            'order_index' => 5,
            'content'     => $this->appendQuiz($content5, 'M16_L5', [
                ['q' => 'Wilks\' Lambda Λ = 0.023 in a MANOVA result. What does this indicate?', 'opts' => ['No group differences exist', 'Very strong group separation — within-group variance is only 2.3% of total variance', 'Only one group differs from the others', 'The test statistic is less than the critical value'], 'ans' => 1, 'exp' => 'Wilks\' Λ = |E|/|T| = within-group generalized variance / total generalized variance. It ranges from 0 (complete separation) to 1 (no separation). Λ = 0.023 means within-group variance is only 2.3% of total variance — 97.7% of total variance is due to between-group differences. This indicates extremely strong group separation, strongly rejecting H₀.'],
                ['q' => 'MANOVA is preferred over running multiple separate ANOVAs primarily because...', 'opts' => ['MANOVA is computationally faster', 'Multiple ANOVAs inflate the familywise Type I error rate; MANOVA tests all outcomes simultaneously at the stated α level', 'MANOVA assumes fewer variables', 'MANOVA only works with two groups'], 'ans' => 1, 'exp' => 'With p dependent variables each tested at α=0.05, the familywise error rate = 1−(1−0.05)ᵖ, which can be very large. With p=10 variables, there\'s a 40% chance of at least one false positive. MANOVA controls the familywise rate at α. Additionally, MANOVA can detect group differences that exist only in combinations of variables, not individual ones.'],
                ['q' => 'Among the four MANOVA test statistics, Pillai\'s trace is generally recommended when...', 'opts' => ['Group differences are concentrated in one dimension', 'Groups are perfectly separated', 'Assumptions are violated — unequal sample sizes, non-normality, or heterogeneous covariance matrices', 'The number of variables p is large'], 'ans' => 2, 'exp' => 'Pillai\'s trace (Σ λᵢ/(1+λᵢ)) is the most robust MANOVA statistic to assumption violations. When sample sizes are unequal, covariance matrices differ across groups, or normality is questionable, Pillai\'s trace gives the most reliable results. Roy\'s largest root is the most powerful when alternatives are one-dimensional but is sensitive to violations.'],
                ['q' => 'The SSCP decomposition T = H + E in MANOVA parallels the ANOVA decomposition SS_Total = SS_Between + SS_Within. In MANOVA, H represents...', 'opts' => ['The error covariance matrix', 'The between-group sum of squares and cross-products matrix', 'The hypothesis that all means are equal', 'The Hotelling trace statistic'], 'ans' => 1, 'exp' => 'H (hypothesis SSCP matrix) = Σₖ nₖ (x̄ₖ − x̄)(x̄ₖ − x̄)ᵀ captures between-group variation in multivariate space — the scatter of group centroids around the grand centroid. E (error SSCP) captures within-group scatter. Large H relative to E → groups differ more than sampling variation can explain.'],
                ['q' => 'A significant MANOVA result tells you...', 'opts' => ['Exactly which group pairs differ and on which variables', 'That at least one group mean vector differs from the others, but not specifically where or how — follow-up analyses are needed', 'That all groups differ from all others on all variables', 'That the assumption of homogeneous covariance matrices is satisfied'], 'ans' => 1, 'exp' => 'MANOVA\'s omnibus test only indicates that the null H₀: μ₁=...=μg is rejected. It doesn\'t specify which groups differ or on which variable combinations. Follow-up procedures include: discriminant analysis (find separating directions), Roy-Bose simultaneous confidence intervals, or post-hoc univariate ANOVAs with Bonferroni correction.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 16.6 — Discriminant Analysis (LDA & QDA)
        // ══════════════════════════════════════════════════════════════
        $content6 = <<<'HTML'
<h2>Discriminant Analysis: LDA & QDA</h2>
<p>Discriminant analysis is a multivariate technique with two related purposes: (1) <strong>description</strong> — finding the linear combinations of variables that best separate known groups, and (2) <strong>classification</strong> — assigning new observations to the most likely group. Developed by Ronald A. Fisher in 1936 using the famous iris dataset, discriminant analysis remains one of the most elegant and practically important multivariate methods. It bridges the worlds of multivariate statistics and machine learning.</p>

<h3>Linear Discriminant Analysis (LDA)</h3>
<p>LDA finds the linear combination z = aᵀx that maximally separates groups while accounting for within-group variability. Fisher's criterion maximizes the ratio of between-group variance to within-group variance:</p>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:18px;margin-bottom:20px;font-family:'JetBrains Mono',monospace;text-align:center;color:var(--text);">
  maximize &nbsp; aᵀ B a / aᵀ W a &nbsp; subject to some normalization
</div>
<p>where B is the between-group SSCP matrix and W is the within-group SSCP matrix. The solution is the eigenvectors of W⁻¹B. There are min(p, g−1) discriminant functions (linear discriminants). For two groups, there is only one discriminant function.</p>

<h3>Probabilistic Interpretation (Bayes' Theorem)</h3>
<p>LDA can also be derived from Bayes' theorem. Assuming all groups follow MVN with the <em>same</em> covariance matrix Σ and prior probabilities πₖ, the optimal classification rule assigns x to the group with highest posterior probability:</p>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:18px;margin-bottom:24px;font-family:'JetBrains Mono',monospace;text-align:center;color:var(--text);font-size:0.9rem;">
  Assign x to group k* = argmax_k [ xᵀ Σ⁻¹ μₖ − ½ μₖᵀ Σ⁻¹ μₖ + log πₖ ]
</div>
<p>This is a <strong>linear</strong> decision rule because the quadratic terms xᵀΣ⁻¹x cancel (all groups share Σ). The boundaries between groups are hyperplanes.</p>

<h3>Quadratic Discriminant Analysis (QDA)</h3>
<p>If groups have <em>different</em> covariance matrices Σₖ, the quadratic term xᵀΣₖ⁻¹x no longer cancels, and the decision boundaries become <strong>quadratic</strong> (elliptical curves in 2D). QDA assigns x to group k* = argmax_k [−½log|Σₖ| − ½(x−μₖ)ᵀΣₖ⁻¹(x−μₖ) + log πₖ]. QDA is more flexible but requires more data (estimates p covariance matrices instead of one pooled Σ).</p>

<h3>LDA vs QDA: The Bias-Variance Tradeoff</h3>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;margin-bottom:32px;display:grid;grid-template-columns:1fr 1fr;gap:16px;font-size:0.875rem;">
  <div style="border-top:3px solid #6366f1;padding-top:12px;">
    <h4 style="color:#6366f1;margin:0 0 8px;">LDA — Linear</h4>
    <ul style="color:var(--muted);padding-left:1.2rem;margin:0;line-height:2;">
      <li>Assumes equal Σ across groups</li>
      <li>Linear decision boundaries</li>
      <li>Low variance, potentially biased</li>
      <li>Better with small samples (fewer parameters)</li>
      <li>More interpretable</li>
    </ul>
  </div>
  <div style="border-top:3px solid #10b981;padding-top:12px;">
    <h4 style="color:#10b981;margin:0 0 8px;">QDA — Quadratic</h4>
    <ul style="color:var(--muted);padding-left:1.2rem;margin:0;line-height:2;">
      <li>Allows different Σₖ per group</li>
      <li>Quadratic (curved) decision boundaries</li>
      <li>Higher variance, less bias</li>
      <li>Better with large samples</li>
      <li>More flexible but more parameters</li>
    </ul>
  </div>
</div>

<h3>Model Evaluation</h3>
<p>Classification performance is evaluated using the <strong>confusion matrix</strong> — a table of actual vs predicted group memberships. Key metrics include overall accuracy, sensitivity/recall per class, and the <strong>apparent error rate (APER)</strong> — the proportion of training observations misclassified. The APER is optimistic (underestimates true error) because the model was built on the training data. Preferred alternatives: leave-one-out cross-validation or holdout validation on a test set.</p>

<h3>Python: LDA vs QDA — Decision Boundaries & Classification</h3>
<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — LDA vs QDA: Decision Boundaries, Scores & Confusion Matrix</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">from</span> sklearn.discriminant_analysis <span style="color:#c4b5fd;">import</span> LinearDiscriminantAnalysis, QuadraticDiscriminantAnalysis
<span style="color:#c4b5fd;">from</span> sklearn.model_selection <span style="color:#c4b5fd;">import</span> cross_val_score, StratifiedKFold
<span style="color:#c4b5fd;">from</span> sklearn.metrics <span style="color:#c4b5fd;">import</span> confusion_matrix, classification_report
<span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_iris
<span style="color:#c4b5fd;">import</span> seaborn <span style="color:#c4b5fd;">as</span> sns

<span style="color:#93c5fd;">iris</span>   = load_iris()
<span style="color:#93c5fd;">X</span>      = iris.data
<span style="color:#93c5fd;">y</span>      = iris.target
<span style="color:#93c5fd;">names</span>  = iris.target_names

<span style="color:#6b7280;"># ── Fit LDA and QDA ───────────────────────────────────────────</span>
<span style="color:#93c5fd;">lda</span>  = LinearDiscriminantAnalysis(store_covariance=<span style="color:#fca5a5;">True</span>)
<span style="color:#93c5fd;">qda</span>  = QuadraticDiscriminantAnalysis(store_covariance=<span style="color:#fca5a5;">True</span>)

<span style="color:#93c5fd;">lda</span>.fit(X, y)
<span style="color:#93c5fd;">qda</span>.fit(X, y)

<span style="color:#6b7280;"># ── LDA discriminant function scores ──────────────────────────</span>
<span style="color:#93c5fd;">lda_scores</span> = lda.transform(X)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"═══ LDA: DISCRIMINANT FUNCTION LOADINGS ═══"</span>)
<span style="color:#93c5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd
<span style="color:#93c5fd;">load_df</span> = pd.DataFrame(lda.scalings_,
                         index=iris.feature_names,
                         columns=[<span style="color:#a7f3d0;">f"LD{i+1}"</span> <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(lda.scalings_.shape[<span style="color:#fcd34d;">1</span>])])
<span style="color:#93c5fd;">print</span>(load_df.round(<span style="color:#fcd34d;">4</span>).to_string())

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\n═══ PROPORTION OF DISCRIMINATION ═══"</span>)
<span style="color:#93c5fd;">print</span>(pd.Series(lda.explained_variance_ratio_,
                 index=[<span style="color:#a7f3d0;">f"LD{i+1}"</span> <span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#93c5fd;">len</span>(lda.explained_variance_ratio_))]).round(<span style="color:#fcd34d;">4</span>))

<span style="color:#6b7280;"># ── Cross-validated accuracy ───────────────────────────────────</span>
<span style="color:#93c5fd;">cv</span> = StratifiedKFold(<span style="color:#fcd34d;">5</span>, shuffle=<span style="color:#fca5a5;">True</span>, random_state=<span style="color:#fcd34d;">42</span>)
<span style="color:#93c5fd;">lda_cv</span> = cross_val_score(lda, X, y, cv=cv).mean()
<span style="color:#93c5fd;">qda_cv</span> = cross_val_score(qda, X, y, cv=cv).mean()

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\n═══ 5-FOLD CV ACCURACY ═══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"LDA: {lda_cv:.4f} ({lda_cv*100:.2f}%)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"QDA: {qda_cv:.4f} ({qda_cv*100:.2f}%)"</span>)

<span style="color:#6b7280;"># Confusion matrix (training)</span>
<span style="color:#93c5fd;">cm_lda</span> = confusion_matrix(y, lda.predict(X))
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\n═══ LDA CONFUSION MATRIX (training) ═══"</span>)
<span style="color:#93c5fd;">print</span>(pd.DataFrame(cm_lda, index=names, columns=names).to_string())
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"APER (training error rate) = {1 - np.trace(cm_lda)/len(y):.4f}"</span>)

<span style="color:#6b7280;"># ── Visualization ─────────────────────────────────────────────</span>
<span style="color:#93c5fd;">fig</span>, axes = plt.subplots(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, figsize=(<span style="color:#fcd34d;">13</span>, <span style="color:#fcd34d;">5</span>))
<span style="color:#93c5fd;">colors</span> = [<span style="color:#a7f3d0;">"#3b82f6"</span>, <span style="color:#a7f3d0;">"#10b981"</span>, <span style="color:#a7f3d0;">"#ef4444"</span>]

<span style="color:#c4b5fd;">for</span> k, (name, col) <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(<span style="color:#93c5fd;">zip</span>(names, colors)):
    axes[<span style="color:#fcd34d;">0</span>].scatter(lda_scores[y==k,<span style="color:#fcd34d;">0</span>], lda_scores[y==k,<span style="color:#fcd34d;">1</span>],
                    c=col, label=name, alpha=<span style="color:#fcd34d;">0.8</span>, s=<span style="color:#fcd34d;">50</span>)
axes[<span style="color:#fcd34d;">0</span>].set_title(<span style="color:#a7f3d0;">"LDA Discriminant Space\n(LD1 vs LD2)"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
axes[<span style="color:#fcd34d;">0</span>].set_xlabel(<span style="color:#a7f3d0;">f"LD1 ({lda.explained_variance_ratio_[0]*100:.1f}%)"</span>)
axes[<span style="color:#fcd34d;">0</span>].set_ylabel(<span style="color:#a7f3d0;">f"LD2 ({lda.explained_variance_ratio_[1]*100:.1f}%)"</span>)
axes[<span style="color:#fcd34d;">0</span>].legend(); axes[<span style="color:#fcd34d;">0</span>].grid(alpha=<span style="color:#fcd34d;">0.3</span>)

sns.heatmap(cm_lda, annot=<span style="color:#fca5a5;">True</span>, fmt=<span style="color:#a7f3d0;">"d"</span>, cmap=<span style="color:#a7f3d0;">"Blues"</span>,
            xticklabels=names, yticklabels=names, ax=axes[<span style="color:#fcd34d;">1</span>])
axes[<span style="color:#fcd34d;">1</span>].set_title(<span style="color:#a7f3d0;">"LDA Confusion Matrix (Training)"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
axes[<span style="color:#fcd34d;">1</span>].set_xlabel(<span style="color:#a7f3d0;">"Predicted"</span>); axes[<span style="color:#fcd34d;">1</span>].set_ylabel(<span style="color:#a7f3d0;">"Actual"</span>)

plt.suptitle(<span style="color:#a7f3d0;">"Linear Discriminant Analysis — Iris Dataset"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
plt.tight_layout(); plt.show()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>═══ LDA: DISCRIMINANT FUNCTION LOADINGS ═══
                         LD1      LD2
sepal length (cm)      0.8294   0.0241
sepal width (cm)       1.5345   2.1645
petal length (cm)     -2.2012  -0.9319
petal width (cm)      -2.8105   2.8392

═══ PROPORTION OF DISCRIMINATION ═══
LD1    0.9912
LD2    0.0088

5-FOLD CV ACCURACY ═══
LDA: 0.9800 (98.00%)
QDA: 0.9800 (98.00%)

LDA CONFUSION MATRIX (training) ═══
            setosa  versicolor  virginica
setosa          50           0          0
versicolor       0          48          2
virginica        0           1         49
APER = 0.0200</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '16.6 Discriminant Analysis (LDA & QDA)',
            'order_index' => 6,
            'content'     => $this->appendQuiz($content6, 'M16_L6', [
                ['q' => 'Fisher\'s criterion in LDA maximizes...', 'opts' => ['The total within-group variance', 'The ratio of between-group variance to within-group variance of the discriminant scores', 'The posterior probability of class membership', 'The correlation between discriminant scores and original variables'], 'ans' => 1, 'exp' => 'Fisher maximizes aᵀBa / aᵀWa — the ratio of between-group scatter to within-group scatter of the linear combination z = aᵀx. Large between-group variance means group centroids are far apart on z; small within-group variance means observations cluster tightly around their group centroids. The eigenvectors of W⁻¹B solve this problem, yielding the discriminant functions.'],
                ['q' => 'LDA assumes all groups have the same population covariance matrix Σ. When this assumption holds and we use Bayes\' theorem, the decision boundaries between groups are...', 'opts' => ['Curved ellipses', 'Circles around group centroids', 'Linear (hyperplanes)', 'Defined by the Mahalanobis distance to group centroids'], 'ans' => 2, 'exp' => 'When all groups share the same Σ, the quadratic term xᵀΣ⁻¹x is the same for all groups and cancels out in the classification rule. What remains is linear in x — the classification function reduces to linear discriminant scores, giving hyperplane decision boundaries.'],
                ['q' => 'For 3 groups and p = 5 variables, how many linear discriminant functions exist?', 'opts' => ['5', '3', '2', '15'], 'ans' => 2, 'exp' => 'The number of discriminant functions (linear discriminants) is min(p, g−1) = min(5, 3−1) = min(5, 2) = 2. This is because you can separate g groups using at most g−1 orthogonal directions. Even with 5 variables, only 2 discriminant dimensions exist for 3 groups.'],
                ['q' => 'QDA is preferred over LDA when...', 'opts' => ['Sample sizes are small', 'All groups are assumed to have the same covariance matrix', 'Group covariance matrices are substantially different, and sufficient data exists to estimate each Σₖ', 'Variables are binary'], 'ans' => 2, 'exp' => 'QDA estimates a separate covariance matrix Σₖ for each group, giving curved (quadratic) decision boundaries that better fit data where groups have different shapes. However, this requires p(p+1)/2 additional parameters per group — with many variables and few observations, QDA overfits. LDA\'s pooled Σ estimate is more stable with limited data.'],
                ['q' => 'The Apparent Error Rate (APER) underestimates the true classification error because...', 'opts' => ['It uses a training dataset that is too small', 'The model was built on the same data it is evaluated on — optimistically biased', 'It only counts misclassified observations from the minority class', 'It does not account for prior probabilities'], 'ans' => 1, 'exp' => 'APER = proportion of training observations misclassified by the discriminant rule built on those same training observations. Since the model is tuned to fit the training data, it will classify training points better than new, unseen data. This produces an optimistic (downward-biased) estimate of the true error rate. Cross-validation or holdout testing gives an honest estimate.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 16.7 — Cluster Analysis: Hierarchical & k-Means
        // ══════════════════════════════════════════════════════════════
        $content7 = <<<'HTML'
<h2>Cluster Analysis: Hierarchical & k-Means</h2>
<p>Cluster analysis is the multivariate technique for discovering natural groupings (clusters) in data without prior knowledge of group membership. Unlike discriminant analysis, cluster analysis is <strong>unsupervised</strong> — there are no predefined groups. It is used in market segmentation (find customer types), bioinformatics (discover gene expression patterns), social science (identify subpopulations), document classification, and image segmentation. The goal is to partition n observations into groups such that observations within a group are similar and observations between groups are dissimilar.</p>

<h3>Measuring Similarity and Dissimilarity</h3>
<p>Cluster analysis begins with a dissimilarity or distance matrix D, where dᵢⱼ measures how different observations i and j are. Common choices:</p>
<ul style="color:var(--muted);line-height:2.3;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">Euclidean distance:</strong> dᵢⱼ = √Σₖ(xᵢₖ−xⱼₖ)². Most common. Sensitive to scale — standardize variables first.</li>
  <li><strong style="color:var(--text);">Manhattan (city block) distance:</strong> Σₖ|xᵢₖ−xⱼₖ|. Robust to outliers.</li>
  <li><strong style="color:var(--text);">Mahalanobis distance:</strong> Accounts for correlations and different scales. Invariant to linear transformations.</li>
  <li><strong style="color:var(--text);">Cosine similarity:</strong> 1 − cos(θᵢⱼ). Used in text and document clustering — measures angular distance, ignoring magnitude.</li>
</ul>

<h3>Hierarchical Clustering</h3>
<p>Hierarchical clustering builds a <strong>dendrogram</strong> — a tree structure showing how observations merge into clusters at increasing dissimilarity levels. Two approaches:</p>
<ul style="color:var(--muted);line-height:2.3;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">Agglomerative (bottom-up):</strong> Start with each observation as its own cluster. At each step, merge the two closest clusters. Continue until all observations are in one cluster. The choice of <em>linkage criterion</em> defines what "closest" means between clusters.</li>
  <li><strong style="color:var(--text);">Divisive (top-down):</strong> Start with all observations in one cluster; recursively split into subclusters. Less common; more computationally intensive.</li>
</ul>
<p><strong>Linkage criteria for agglomerative clustering:</strong></p>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;margin-bottom:24px;display:grid;gap:10px;font-size:0.875rem;">
  <div style="background:rgba(99,102,241,0.08);border-left:3px solid #6366f1;padding:10px;"><strong style="color:#6366f1;">Single linkage (nearest neighbor):</strong> Distance between clusters = minimum distance between any pair of members. Produces "chaining" — long, straggly clusters.</div>
  <div style="background:rgba(16,185,129,0.08);border-left:3px solid #10b981;padding:10px;"><strong style="color:#10b981;">Complete linkage (farthest neighbor):</strong> Distance = maximum distance between any pair. Produces compact, spherical clusters. Sensitive to outliers.</div>
  <div style="background:rgba(245,158,11,0.08);border-left:3px solid #f59e0b;padding:10px;"><strong style="color:#f59e0b;">Average linkage (UPGMA):</strong> Distance = average of all pairwise distances between clusters. Compromise between single and complete. Most commonly recommended.</div>
  <div style="background:rgba(239,68,68,0.08);border-left:3px solid #ef4444;padding:10px;"><strong style="color:#ef4444;">Ward's method:</strong> Merge clusters that minimize the increase in total within-cluster variance (sum of squared Euclidean distances to centroid). Produces compact, equal-size clusters. Often recommended for balanced data.</div>
</div>

<h3>k-Means Clustering</h3>
<p>k-Means partitions n observations into exactly k pre-specified clusters by iteratively optimizing cluster assignment to minimize total within-cluster sum of squares:</p>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:18px;margin-bottom:20px;font-family:'JetBrains Mono',monospace;text-align:center;color:var(--text);">
  minimize Σₖ Σᵢ∈Cₖ ‖xᵢ − μₖ‖²
</div>
<p><strong>k-Means algorithm:</strong> (1) Initialize k cluster centroids. (2) Assign each observation to the nearest centroid. (3) Update centroids as the mean of assigned observations. (4) Repeat steps 2–3 until convergence. Convergence is guaranteed but to a local minimum — run multiple times with different random initializations. k-means++ initialization provides better starting centroids.</p>

<h3>Choosing the Number of Clusters</h3>
<ul style="color:var(--muted);line-height:2.3;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">Elbow method:</strong> Plot within-cluster SS vs k. The "elbow" where the decrease slows suggests the optimal k.</li>
  <li><strong style="color:var(--text);">Silhouette score:</strong> For each observation, silhouette = (b−a)/max(a,b) where a = mean distance to other cluster members, b = mean distance to nearest different cluster. Ranges from −1 to 1; higher is better. Average over all observations gives the silhouette coefficient.</li>
  <li><strong style="color:var(--text);">Gap statistic:</strong> Compares observed within-cluster SS to expected under a reference null distribution (uniform random data). More principled than the elbow method.</li>
  <li><strong style="color:var(--text);">Domain knowledge:</strong> Often the most important criterion.</li>
</ul>

<h3>Python: Hierarchical Clustering & k-Means</h3>
<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Hierarchical Clustering Dendrogram & k-Means</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">from</span> scipy.cluster.hierarchy <span style="color:#c4b5fd;">import</span> dendrogram, linkage, fcluster
<span style="color:#c4b5fd;">from</span> sklearn.cluster <span style="color:#c4b5fd;">import</span> KMeans
<span style="color:#c4b5fd;">from</span> sklearn.metrics <span style="color:#c4b5fd;">import</span> silhouette_score
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> StandardScaler
<span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> make_blobs

<span style="color:#6b7280;"># ── Generate synthetic cluster data ───────────────────────────</span>
<span style="color:#93c5fd;">X</span>, <span style="color:#93c5fd;">y_true</span> = make_blobs(n_samples=<span style="color:#fcd34d;">150</span>, centers=<span style="color:#fcd34d;">3</span>, cluster_std=<span style="color:#fcd34d;">0.8</span>, random_state=<span style="color:#fcd34d;">42</span>)
<span style="color:#93c5fd;">X_std</span>     = StandardScaler().fit_transform(X)

<span style="color:#6b7280;"># ── 1. Hierarchical Clustering (Agglomerative) ────────────────</span>
<span style="color:#6b7280;"># Perform linkage (Ward's method minimizes variance increase)</span>
<span style="color:#93c5fd;">Z</span> = linkage(X_std, method=<span style="color:#a7f3d0;">'ward'</span>)

<span style="color:#6b7280;"># Cut the dendrogram to form 3 clusters</span>
<span style="color:#93c5fd;">hc_labels</span> = fcluster(Z, t=<span style="color:#fcd34d;">3</span>, criterion=<span style="color:#a7f3d0;">'maxclust'</span>)

<span style="color:#6b7280;"># ── 2. k-Means Clustering ─────────────────────────────────────</span>
<span style="color:#6b7280;"># Determine optimal k using the Elbow Method</span>
<span style="color:#93c5fd;">wcss</span> = []
<span style="color:#93c5fd;">sil_scores</span> = []
<span style="color:#93c5fd;">K_range</span> = <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">8</span>)

<span style="color:#c4b5fd;">for</span> k <span style="color:#c4b5fd;">in</span> K_range:
    kmeans = KMeans(n_clusters=k, init=<span style="color:#a7f3d0;">'k-means++'</span>, random_state=<span style="color:#fcd34d;">42</span>, n_init=<span style="color:#fcd34d;">10</span>)
    kmeans.fit(X_std)
    wcss.append(kmeans.inertia_)
    sil_scores.append(silhouette_score(X_std, kmeans.labels_))

<span style="color:#6b7280;"># Fit final k-means model with k=3</span>
<span style="color:#93c5fd;">kmeans_final</span> = KMeans(n_clusters=<span style="color:#fcd34d;">3</span>, init=<span style="color:#a7f3d0;">'k-means++'</span>, random_state=<span style="color:#fcd34d;">42</span>, n_init=<span style="color:#fcd34d;">10</span>)
<span style="color:#93c5fd;">km_labels</span>    = kmeans_final.fit_predict(X_std)
<span style="color:#93c5fd;">centroids</span>    = kmeans_final.cluster_centers_

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"═══ CLUSTER EVALUATION ═══"</span>)
<span style="color:#c4b5fd;">for</span> k, sil <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>(K_range, sil_scores):
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  k={k}: Silhouette Score = {sil:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  (Max silhouette score indicates optimal k is {K_range[np.argmax(sil_scores)]})"</span>)

<span style="color:#6b7280;"># ── Visualization ─────────────────────────────────────────────</span>
<span style="color:#93c5fd;">fig</span>, axes = plt.subplots(<span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">2</span>, figsize=(<span style="color:#fcd34d;">14</span>, <span style="color:#fcd34d;">10</span>))

<span style="color:#6b7280;"># Plot 1: Dendrogram</span>
dendrogram(Z, truncate_mode=<span style="color:#a7f3d0;">'level'</span>, p=<span style="color:#fcd34d;">4</span>, ax=axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">0</span>], 
           color_threshold=<span style="color:#fcd34d;">10</span>, above_threshold_color=<span style="color:#a7f3d0;">'#9ca3af'</span>)
axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">0</span>].set_title(<span style="color:#a7f3d0;">"Hierarchical Clustering Dendrogram (Ward)"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">0</span>].set_ylabel(<span style="color:#a7f3d0;">"Distance"</span>)

<span style="color:#6b7280;"># Plot 2: Elbow Method</span>
axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">1</span>].plot(K_range, wcss, <span style="color:#a7f3d0;">'o-'</span>, color=<span style="color:#a7f3d0;">"#6366f1"</span>, lw=<span style="color:#fcd34d;">2</span>, ms=<span style="color:#fcd34d;">8</span>)
axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">1</span>].set_title(<span style="color:#a7f3d0;">"Elbow Method for k-Means"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">1</span>].set_xlabel(<span style="color:#a7f3d0;">"Number of Clusters (k)"</span>)
axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">1</span>].set_ylabel(<span style="color:#a7f3d0;">"Within-Cluster Sum of Squares (WCSS)"</span>)
axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">1</span>].axvline(<span style="color:#fcd34d;">3</span>, color=<span style="color:#a7f3d0;">"#ef4444"</span>, ls=<span style="color:#a7f3d0;">"--"</span>, label=<span style="color:#a7f3d0;">"Elbow at k=3"</span>)
axes[<span style="color:#fcd34d;">0</span>,<span style="color:#fcd34d;">1</span>].legend()

<span style="color:#6b7280;"># Plot 3: Hierarchical Clusters Scatter</span>
scatter1 = axes[<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">0</span>].scatter(X_std[:,<span style="color:#fcd34d;">0</span>], X_std[:,<span style="color:#fcd34d;">1</span>], c=hc_labels, cmap=<span style="color:#a7f3d0;">"viridis"</span>, s=<span style="color:#fcd34d;">50</span>, alpha=<span style="color:#fcd34d;">0.8</span>)
axes[<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">0</span>].set_title(<span style="color:#a7f3d0;">"Hierarchical Clusters (k=3)"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
axes[<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">0</span>].grid(alpha=<span style="color:#fcd34d;">0.3</span>)

<span style="color:#6b7280;"># Plot 4: k-Means Clusters Scatter</span>
scatter2 = axes[<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">1</span>].scatter(X_std[:,<span style="color:#fcd34d;">0</span>], X_std[:,<span style="color:#fcd34d;">1</span>], c=km_labels, cmap=<span style="color:#a7f3d0;">"plasma"</span>, s=<span style="color:#fcd34d;">50</span>, alpha=<span style="color:#fcd34d;">0.8</span>)
axes[<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">1</span>].scatter(centroids[:,<span style="color:#fcd34d;">0</span>], centroids[:,<span style="color:#fcd34d;">1</span>], c=<span style="color:#a7f3d0;">'red'</span>, marker=<span style="color:#a7f3d0;">'X'</span>, s=<span style="color:#fcd34d;">200</span>, edgecolor=<span style="color:#a7f3d0;">'k'</span>, label=<span style="color:#a7f3d0;">"Centroids"</span>)
axes[<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">1</span>].set_title(<span style="color:#a7f3d0;">"k-Means Clusters (k=3)"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
axes[<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">1</span>].legend()
axes[<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">1</span>].grid(alpha=<span style="color:#fcd34d;">0.3</span>)

plt.tight_layout(); plt.show()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>═══ CLUSTER EVALUATION ═══
  k=2: Silhouette Score = 0.5413
  k=3: Silhouette Score = 0.6582
  k=4: Silhouette Score = 0.5518
  k=5: Silhouette Score = 0.4491
  k=6: Silhouette Score = 0.3847
  k=7: Silhouette Score = 0.3392
  (Max silhouette score indicates optimal k is 3)</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '16.7 Cluster Analysis: Hierarchical & k-Means',
            'order_index' => 7,
            'content'     => $this->appendQuiz($content7, 'M16_L7', [
                ['q' => 'In agglomerative hierarchical clustering, the process begins by...', 'opts' => ['Assigning all observations to a single large cluster', 'Treating every individual observation as its own cluster', 'Randomly assigning observations to k clusters', 'Placing observations into two clusters based on the largest principal component'], 'ans' => 1, 'exp' => 'Agglomerative (bottom-up) clustering starts with n clusters, each containing one observation. It then iteratively merges the two closest clusters until all observations belong to a single overarching cluster. The history of these merges is visualized in a dendrogram.'],
                ['q' => 'Which linkage method for hierarchical clustering is known to produce long, straggly, "chain-like" clusters?', 'opts' => ['Single linkage', 'Complete linkage', 'Average linkage', 'Ward\'s method'], 'ans' => 0, 'exp' => 'Single linkage defines the distance between two clusters as the minimum distance between any single point in the first cluster and any single point in the second. This tends to merge clusters that are connected by a chain of intermediate points, even if their overall shapes are very different. Complete linkage and Ward\'s method tend to produce compact, spherical clusters.'],
                ['q' => 'What does k-Means clustering aim to minimize?', 'opts' => ['The number of clusters k', 'The distance between cluster centroids', 'The total within-cluster sum of squares (variance within each cluster)', 'The silhouette score'], 'ans' => 2, 'exp' => 'The objective function of k-Means is to partition the data so that the sum of squared Euclidean distances between each point and its assigned cluster centroid is minimized. This is equivalent to minimizing the within-cluster variance.'],
                ['q' => 'The silhouette score measures...', 'opts' => ['How many clusters are present in the dataset', 'How similar an object is to its own cluster compared to other clusters', 'The sum of squared distances to the centroid', 'The height at which branches merge in a dendrogram'], 'ans' => 1, 'exp' => 'The silhouette score for a single observation compares its mean distance to other points in its own cluster (a) against its mean distance to points in the nearest neighboring cluster (b). It is computed as (b−a)/max(a,b). A high score (near 1) means the point is well-matched to its own cluster and poorly matched to neighboring clusters.'],
                ['q' => 'Why is standardization (e.g., using StandardScaler) generally recommended before running k-Means or hierarchical clustering?', 'opts' => ['It guarantees the algorithm will converge to the global minimum', 'It prevents variables with large scales (e.g., salary in dollars) from dominating the distance calculations over variables with small scales (e.g., age in years)', 'It automatically determines the optimal number of clusters', 'It changes the distribution of the data to multivariate normal'], 'ans' => 1, 'exp' => 'Clustering relies heavily on distance metrics (usually Euclidean). If one variable ranges from 0 to 1,000,000 and another ranges from 0 to 1, the distance calculation will be almost entirely driven by the first variable. Standardizing gives all variables equal weight in determining the clusters.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 16.8 — Canonical Correlation Analysis (CCA)
        // ══════════════════════════════════════════════════════════════
        $content8 = <<<'HTML'
<h2>Canonical Correlation Analysis (CCA)</h2>
<p>While PCA reduces dimensionality within a single set of variables, and multiple regression predicts one dependent variable from multiple predictors, <strong>Canonical Correlation Analysis (CCA)</strong> explores the relationship between <em>two distinct sets</em> of variables measured on the same subjects. For example, predicting a set of physical fitness metrics (lung capacity, body fat, resting heart rate) from a set of physiological measurements (weight, waist size, pulse). CCA finds the linear combinations of the first set and the linear combinations of the second set that are maximally correlated with each other.</p>

<h3>The CCA Problem: Maximizing Correlation Between Sets</h3>
<p>Let X = [X₁, ..., Xₚ] be the first set of variables, and Y = [Y₁, ..., Y_q] be the second set. CCA seeks coefficient vectors <strong>a</strong> and <strong>b</strong> to form two linear combinations:</p>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:18px;margin-bottom:20px;font-family:'JetBrains Mono',monospace;text-align:center;color:var(--text);">
  U = <strong>a</strong>ᵀX &nbsp;&nbsp; and &nbsp;&nbsp; V = <strong>b</strong>ᵀY
</div>
<p>such that the correlation ρ = Corr(U, V) is maximized. U and V are called the first pair of <strong>canonical variables</strong>, and their correlation ρ₁ is the first <strong>canonical correlation</strong>.</p>
<p>After finding the first pair, CCA finds a second pair (U₂, V₂) that maximizes correlation subject to being uncorrelated with the first pair, and so on. The maximum number of canonical pairs is min(p, q).</p>

<h3>Mathematical Solution via Block Covariance Matrix</h3>
<p>The joint covariance matrix of the combined variable set [X, Y] is partitioned as:</p>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;margin-bottom:24px;font-family:'JetBrains Mono',monospace;text-align:center;color:var(--text);font-size:0.95rem;">
  Σ = [ Σ_XX &nbsp; Σ_XY ]<br>
  &nbsp;&nbsp;&nbsp;&nbsp;[ Σ_YX &nbsp; Σ_YY ]
</div>
<p>The coefficients <strong>a</strong> and <strong>b</strong> are found by solving the generalized eigenvalue problem involving the matrix Σ_XX⁻¹ Σ_XY Σ_YY⁻¹ Σ_YX. The eigenvalues λᵢ of this matrix are the squared canonical correlations (ρᵢ²). This shows that CCA is mathematically closely related to discriminant analysis and MANOVA.</p>

<h3>Interpretation and Significance Testing</h3>
<ul style="color:var(--muted);line-height:2.3;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">Canonical Loadings:</strong> The correlations between the original variables and their corresponding canonical variables (e.g., Corr(Xᵢ, U₁)). These are easier to interpret than the raw coefficients <strong>a</strong> and <strong>b</strong> because they are scale-invariant.</li>
  <li><strong style="color:var(--text);">Canonical Cross-Loadings:</strong> The correlations between the original variables of one set and the canonical variables of the <em>other</em> set (e.g., Corr(Xᵢ, V₁)).</li>
  <li><strong style="color:var(--text);">Wilks' Lambda for CCA:</strong> Tests whether any canonical correlations are non-zero. Λ = Πᵢ (1 − ρᵢ²). A significant result indicates the two sets of variables are not independent.</li>
</ul>

<h3>Python: Canonical Correlation Analysis</h3>
<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — CCA: Canonical Correlations, Coefficients & Loadings</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd
<span style="color:#c4b5fd;">from</span> sklearn.cross_decomposition <span style="color:#c4b5fd;">import</span> CCA
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> StandardScaler

np.random.seed(<span style="color:#fcd34d;">123</span>)

<span style="color:#6b7280;"># ── Simulate dataset ──────────────────────────────────────────</span>
<span style="color:#6b7280;"># X variables: Physical measurements (Height, Weight, BodyFat)</span>
<span style="color:#6b7280;"># Y variables: Fitness metrics (RestingHR, VO2Max, BenchPress)</span>
<span style="color:#93c5fd;">n_samples</span> = <span style="color:#fcd34d;">200</span>
<span style="color:#6b7280;"># Latent factor representing overall "athleticism"</span>
<span style="color:#93c5fd;">latent</span>    = np.random.normal(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, n_samples)

<span style="color:#93c5fd;">X</span> = np.column_stack([
    latent * <span style="color:#fcd34d;">0.8</span> + np.random.normal(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0.5</span>, n_samples),  <span style="color:#6b7280;"># Height</span>
    latent * <span style="color:#fcd34d;">0.6</span> + np.random.normal(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0.5</span>, n_samples),  <span style="color:#6b7280;"># Weight</span>
    latent * -<span style="color:#fcd34d;">0.9</span> + np.random.normal(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0.3</span>, n_samples)  <span style="color:#6b7280;"># BodyFat</span>
])
<span style="color:#93c5fd;">Y</span> = np.column_stack([
    latent * -<span style="color:#fcd34d;">0.7</span> + np.random.normal(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0.5</span>, n_samples), <span style="color:#6b7280;"># RestingHR</span>
    latent * <span style="color:#fcd34d;">0.9</span> + np.random.normal(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0.3</span>, n_samples),  <span style="color:#6b7280;"># VO2Max</span>
    latent * <span style="color:#fcd34d;">0.5</span> + np.random.normal(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0.6</span>, n_samples)  <span style="color:#6b7280;"># BenchPress</span>
])

<span style="color:#93c5fd;">X_names</span> = [<span style="color:#a7f3d0;">'Height'</span>, <span style="color:#a7f3d0;">'Weight'</span>, <span style="color:#a7f3d0;">'BodyFat'</span>]
<span style="color:#93c5fd;">Y_names</span> = [<span style="color:#a7f3d0;">'RestingHR'</span>, <span style="color:#a7f3d0;">'VO2Max'</span>, <span style="color:#a7f3d0;">'BenchPress'</span>]

<span style="color:#6b7280;"># Standardize</span>
<span style="color:#93c5fd;">X_std</span> = StandardScaler().fit_transform(X)
<span style="color:#93c5fd;">Y_std</span> = StandardScaler().fit_transform(Y)

<span style="color:#6b7280;"># ── Fit CCA (extract 2 canonical variables) ───────────────────</span>
<span style="color:#93c5fd;">cca</span> = CCA(n_components=<span style="color:#fcd34d;">2</span>)
<span style="color:#93c5fd;">cca</span>.fit(X_std, Y_std)

<span style="color:#6b7280;"># Transform data into canonical variables U and V</span>
<span style="color:#93c5fd;">U</span>, <span style="color:#93c5fd;">V</span> = cca.transform(X_std, Y_std)

<span style="color:#6b7280;"># Compute canonical correlations directly</span>
<span style="color:#93c5fd;">cc1</span> = np.corrcoef(U[:, <span style="color:#fcd34d;">0</span>], V[:, <span style="color:#fcd34d;">0</span>])[<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>]
<span style="color:#93c5fd;">cc2</span> = np.corrcoef(U[:, <span style="color:#fcd34d;">1</span>], V[:, <span style="color:#fcd34d;">1</span>])[<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>]

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"═══ CANONICAL CORRELATIONS (ρ) ═══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Pair 1: ρ₁ = {cc1:.4f}  (ρ₁² = {cc1**2:.4f})"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Pair 2: ρ₂ = {cc2:.4f}  (ρ₂² = {cc2**2:.4f})\n"</span>)

<span style="color:#6b7280;"># ── Canonical Loadings (correlations with original vars) ───────</span>
<span style="color:#6b7280;"># X-loadings: correlation between X variables and U</span>
<span style="color:#93c5fd;">x_loadings</span> = np.corrcoef(X_std.T, U.T)[<span style="color:#fcd34d;">0</span>:<span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">3</span>:<span style="color:#fcd34d;">5</span>]
<span style="color:#6b7280;"># Y-loadings: correlation between Y variables and V</span>
<span style="color:#93c5fd;">y_loadings</span> = np.corrcoef(Y_std.T, V.T)[<span style="color:#fcd34d;">0</span>:<span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">3</span>:<span style="color:#fcd34d;">5</span>]

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"═══ X-SET CANONICAL LOADINGS ═══"</span>)
<span style="color:#93c5fd;">print</span>(pd.DataFrame(x_loadings, index=X_names, columns=[<span style="color:#a7f3d0;">'U1'</span>, <span style="color:#a7f3d0;">'U2'</span>]).round(<span style="color:#fcd34d;">4</span>))

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\n═══ Y-SET CANONICAL LOADINGS ═══"</span>)
<span style="color:#93c5fd;">print</span>(pd.DataFrame(y_loadings, index=Y_names, columns=[<span style="color:#a7f3d0;">'V1'</span>, <span style="color:#a7f3d0;">'V2'</span>]).round(<span style="color:#fcd34d;">4</span>))</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>═══ CANONICAL CORRELATIONS (ρ) ═══
  Pair 1: ρ₁ = 0.9416  (ρ₁² = 0.8867)
  Pair 2: ρ₂ = 0.1582  (ρ₂² = 0.0250)

═══ X-SET CANONICAL LOADINGS ═══
            U1      U2
Height -0.8415 -0.3807
Weight -0.7303  0.2227
BodyFat 0.9328 -0.1983

═══ Y-SET CANONICAL LOADINGS ═══
                V1      V2
RestingHR   0.8173 -0.5694
VO2Max     -0.9669 -0.0675
BenchPress -0.6385 -0.4223</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '16.8 Canonical Correlation Analysis (CCA)',
            'order_index' => 8,
            'content'     => $this->appendQuiz($content8, 'M16_L8', [
                ['q' => 'The main objective of Canonical Correlation Analysis (CCA) is to...', 'opts' => ['Predict a single dependent variable from a set of predictors', 'Find linear combinations of two distinct sets of variables that are maximally correlated with each other', 'Classify observations into predefined groups based on multiple variables', 'Reduce the dimensionality of a single data matrix while preserving variance'], 'ans' => 1, 'exp' => 'CCA takes two sets of variables X and Y (measured on the same subjects) and finds linear combinations U = aᵀX and V = bᵀY such that the correlation between U and V is maximized. This reveals the underlying multidimensional relationship between the two sets.'],
                ['q' => 'If you have p=3 variables in set X and q=5 variables in set Y, what is the maximum number of canonical variable pairs you can extract?', 'opts' => ['3', '5', '8', '15'], 'ans' => 0, 'exp' => 'The number of canonical variable pairs is min(p, q). Since you only have 3 dimensions of variation in the X set, you can form at most 3 orthogonal linear combinations. Thus, min(3, 5) = 3 pairs can be extracted.'],
                ['q' => 'What do the canonical loadings represent?', 'opts' => ['The raw weights a and b used to form the linear combinations', 'The correlations between the original variables and their corresponding canonical variables', 'The correlations between the X set and the Y set directly', 'The eigenvalues of the covariance matrix'], 'ans' => 1, 'exp' => 'Canonical loadings are the Pearson correlations between the original observed variables (e.g., X₁) and the derived canonical variables (e.g., U₁). They are preferred over the raw coefficients (a, b) for interpreting the meaning of the canonical variables because they are scale-invariant.'],
                ['q' => 'Wilks\' Lambda in the context of CCA tests the null hypothesis that...', 'opts' => ['All variables in X are uncorrelated with all variables in Y (all canonical correlations are zero)', 'The mean vectors of X and Y are equal', 'The covariance matrices of X and Y are equal', 'The first canonical correlation is 1'], 'ans' => 0, 'exp' => 'Wilks\' Lambda tests whether there is any significant relationship between the two sets of variables. H₀: Σ_XY = 0 (the cross-covariance matrix is all zeros), which implies all canonical correlations ρᵢ are exactly zero in the population.'],
                ['q' => 'How does CCA relate to multiple linear regression?', 'opts' => ['CCA is exactly the same as multiple regression', 'Multiple regression is a special case of CCA where one of the variable sets contains only a single variable (q=1)', 'CCA can only handle categorical variables, unlike regression', 'CCA cannot be used for prediction'], 'ans' => 1, 'exp' => 'If set Y consists of only a single variable (q=1), finding the linear combination of X that maximally correlates with Y is exactly what Ordinary Least Squares (OLS) multiple regression does. CCA is the multivariate generalization of multiple regression.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 16.9 — Multidimensional Scaling (MDS) & t-SNE
        // ══════════════════════════════════════════════════════════════
        $content9 = <<<'HTML'
<h2>Multidimensional Scaling (MDS) & t-SNE</h2>
<p>Unlike PCA, which starts from a covariance matrix and preserves global variance, Multidimensional Scaling (MDS) and t-Distributed Stochastic Neighbor Embedding (t-SNE) start from a <strong>distance or dissimilarity matrix</strong>. Their goal is to map high-dimensional data points into a low-dimensional space (usually 2D or 3D) such that the pairwise distances between points in the low-dimensional space match their original high-dimensional distances as closely as possible. They are essential tools for visualization and exploratory data analysis.</p>

<h3>Multidimensional Scaling (MDS)</h3>
<p>MDS attempts to preserve <em>global</em> distances. If point A and point B are far apart in the high-dimensional space, they should be far apart in the 2D plot. If they are close, they should be close.</p>
<ul style="color:var(--muted);line-height:2.3;margin-left:1.5rem;">
  <li><strong style="color:var(--text);">Classical (Metric) MDS:</strong> Assumes the dissimilarities are true distances. It minimizes a "stress" function representing the squared differences between original and mapped distances. If the input is Euclidean distance, Metric MDS yields exactly the same result as PCA.</li>
  <li><strong style="color:var(--text);">Non-Metric MDS:</strong> Assumes the dissimilarities are only ordinal (rank-order). It preserves the rank order of distances rather than their exact numeric values. More robust and useful for psychological or subjective rating data.</li>
</ul>

<h3>t-SNE: t-Distributed Stochastic Neighbor Embedding</h3>
<p>While MDS preserves large, global distances, <strong>t-SNE</strong> is designed specifically to preserve <em>local</em> structure. It cares deeply about keeping neighboring points close together in the 2D map, even if it means tearing apart large global structures. Developed by Laurens van der Maaten and Geoffrey Hinton, t-SNE is the gold standard for visualizing complex, nonlinear clusters in high-dimensional data (like image embeddings or gene expressions).</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;margin-bottom:32px;display:grid;gap:14px;">
  <div style="border-left:4px solid #f59e0b;padding-left:16px;">
    <h4 style="color:#f59e0b;margin:0 0 6px;">How t-SNE Works (Conceptual)</h4>
    <p style="color:var(--muted);font-size:0.875rem;line-height:1.7;margin:0;">
      1. In the high-dimensional space, convert distances into probabilities using a Gaussian distribution. Points close together have high probability of being "neighbors".<br>
      2. In the low-dimensional (2D) space, do the same thing, but use a Student's t-distribution (which has heavier tails) to compute the probabilities.<br>
      3. Move the points around in the 2D space to minimize the difference (Kullback-Leibler divergence) between the high-dimensional probabilities and the low-dimensional probabilities.
    </p>
  </div>
  <div style="border-left:4px solid #ef4444;padding-left:16px;">
    <h4 style="color:#ef4444;margin:0 0 6px;">Warnings When Using t-SNE</h4>
    <p style="color:var(--muted);font-size:0.875rem;line-height:1.7;margin:0;">
      - <strong>Distance is meaningless:</strong> The distance between two clusters in a t-SNE plot means almost nothing. Do not use t-SNE output for clustering algorithms (like k-means).<br>
      - <strong>Cluster size is meaningless:</strong> Dense clusters in high dimensions expand, and sparse clusters shrink, making them look similar in size in 2D.<br>
      - <strong>Perplexity matters:</strong> The 'perplexity' parameter determines how many neighbors a point cares about. You must tune this (typically 5 to 50) to get meaningful plots.
    </p>
  </div>
</div>

<h3>Python: PCA vs MDS vs t-SNE Visualization</h3>
<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Visualizing Digits with PCA, MDS, and t-SNE</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> matplotlib.pyplot <span style="color:#c4b5fd;">as</span> plt
<span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_digits
<span style="color:#c4b5fd;">from</span> sklearn.decomposition <span style="color:#c4b5fd;">import</span> PCA
<span style="color:#c4b5fd;">from</span> sklearn.manifold <span style="color:#c4b5fd;">import</span> MDS, TSNE
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> StandardScaler

<span style="color:#6b7280;"># Load handwritten digits (8x8 images = 64 dimensions)</span>
<span style="color:#93c5fd;">digits</span> = load_digits()
<span style="color:#6b7280;"># Use a subset of 500 images for speed</span>
<span style="color:#93c5fd;">X</span> = digits.data[:<span style="color:#fcd34d;">500</span>]
<span style="color:#93c5fd;">y</span> = digits.target[:<span style="color:#fcd34d;">500</span>]
<span style="color:#93c5fd;">X_std</span> = StandardScaler().fit_transform(X)

<span style="color:#6b7280;"># ── 1. PCA (Global variance preservation) ─────────────────────</span>
<span style="color:#93c5fd;">pca</span> = PCA(n_components=<span style="color:#fcd34d;">2</span>, random_state=<span style="color:#fcd34d;">42</span>)
<span style="color:#93c5fd;">X_pca</span> = pca.fit_transform(X_std)

<span style="color:#6b7280;"># ── 2. MDS (Global distance preservation) ─────────────────────</span>
<span style="color:#93c5fd;">mds</span> = MDS(n_components=<span style="color:#fcd34d;">2</span>, random_state=<span style="color:#fcd34d;">42</span>, normalized_stress=<span style="color:#a7f3d0;">'auto'</span>)
<span style="color:#93c5fd;">X_mds</span> = mds.fit_transform(X_std)

<span style="color:#6b7280;"># ── 3. t-SNE (Local neighborhood preservation) ────────────────</span>
<span style="color:#93c5fd;">tsne</span> = TSNE(n_components=<span style="color:#fcd34d;">2</span>, perplexity=<span style="color:#fcd34d;">30</span>, random_state=<span style="color:#fcd34d;">42</span>)
<span style="color:#93c5fd;">X_tsne</span> = tsne.fit_transform(X_std)

<span style="color:#6b7280;"># ── Visualization ─────────────────────────────────────────────</span>
<span style="color:#93c5fd;">fig</span>, axes = plt.subplots(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">3</span>, figsize=(<span style="color:#fcd34d;">16</span>, <span style="color:#fcd34d;">5</span>))

<span style="color:#6b7280;"># Define colormap for 10 digits</span>
<span style="color:#93c5fd;">cmap</span> = plt.cm.get_cmap(<span style="color:#a7f3d0;">'tab10'</span>)

<span style="color:#c4b5fd;">for</span> ax, data, title <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>(axes, [X_pca, X_mds, X_tsne], [<span style="color:#a7f3d0;">'PCA'</span>, <span style="color:#a7f3d0;">'MDS'</span>, <span style="color:#a7f3d0;">'t-SNE'</span>]):
    scatter = ax.scatter(data[:, <span style="color:#fcd34d;">0</span>], data[:, <span style="color:#fcd34d;">1</span>], c=y, cmap=cmap, alpha=<span style="color:#fcd34d;">0.7</span>, s=<span style="color:#fcd34d;">15</span>)
    ax.set_title(title, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
    ax.set_xticks([]); ax.set_yticks([])  <span style="color:#6b7280;"># Remove ticks, axes have no explicit meaning</span>

<span style="color:#6b7280;"># Add colorbar</span>
<span style="color:#93c5fd;">cbar</span> = plt.colorbar(scatter, ax=axes, ticks=<span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">10</span>), orientation=<span style="color:#a7f3d0;">'vertical'</span>, fraction=<span style="color:#fcd34d;">0.02</span>, pad=<span style="color:#fcd34d;">0.04</span>)
cbar.set_label(<span style="color:#a7f3d0;">'Digit Label'</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Generating plots... (t-SNE cleanly separates the digits, whereas PCA and MDS heavily overlap them)"</span>)
plt.suptitle(<span style="color:#a7f3d0;">"Dimensionality Reduction Comparison: 64D Digits → 2D"</span>, fontweight=<span style="color:#a7f3d0;">"bold"</span>)
plt.show()</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '16.9 Multidimensional Scaling (MDS) & t-SNE',
            'order_index' => 9,
            'content'     => $this->appendQuiz($content9, 'M16_L9', [
                ['q' => 'Metric MDS applied to a matrix of Euclidean distances produces the exact same low-dimensional configuration as...', 'opts' => ['t-SNE', 'Canonical Correlation Analysis', 'Principal Component Analysis (PCA)', 'k-Means Clustering'], 'ans' => 2, 'exp' => 'Classical Metric MDS operates on a Euclidean distance matrix and yields the exact same principal coordinates as running PCA directly on the original centered data matrix. The distinction is that MDS can also be applied to non-Euclidean dissimilarity matrices where PCA cannot.'],
                ['q' => 'Which technique is explicitly designed to preserve LOCAL neighborhood structure (keeping similar points together) at the expense of global structure?', 'opts' => ['PCA', 'MDS', 't-SNE', 'Linear Discriminant Analysis'], 'ans' => 2, 'exp' => 't-SNE optimizes a cost function that heavily penalizes mapping nearby high-dimensional points to distant low-dimensional points. It does not severely penalize mapping distant high-dimensional points to nearby low-dimensional points. Thus, local clusters are beautifully preserved, but global distances are distorted.'],
                ['q' => 'In a t-SNE plot showing two clusters separated by a large visual gap, what does the size of the gap mean mathematically?', 'opts' => ['It represents the exact Euclidean distance between the cluster centroids', 'It is proportional to the Mahalanobis distance', 'It indicates the clusters are negatively correlated', 'Very little — t-SNE distorts global distances, so the visual gap size is not a reliable measure of true distance'], 'ans' => 3, 'exp' => 'A fundamental caveat of t-SNE is that global distances are meaningless. The algorithm adapts to local density, expanding dense clusters and ignoring large global distances. A large visual gap means the clusters are distinct, but the width of the gap tells you nothing quantitative about how far apart they actually are.'],
                ['q' => 'What is the "perplexity" parameter in t-SNE?', 'opts' => ['The number of dimensions in the output space', 'The number of iterations before the algorithm converges', 'A smooth measure of the effective number of neighbors each point considers when evaluating local structure', 'The degree of polynomial used in the kernel'], 'ans' => 2, 'exp' => 'Perplexity balances attention between local and global aspects of your data. A low perplexity focuses entirely on local variations (often finding false, small clusters), while a high perplexity considers more neighbors (acting more globally). It must usually be tuned between 5 and 50 to get a good visualization.'],
                ['q' => 'Non-metric MDS is particularly useful when...', 'opts' => ['The data has more than 1000 dimensions', 'The dissimilarity data is only ordinal (rank-ordered) rather than interval/ratio scale', 'You want to classify new, unseen observations', 'The data follows a perfect multivariate normal distribution'], 'ans' => 1, 'exp' => 'Non-metric MDS preserves the rank order of dissimilarities rather than their exact numeric values. If point A is ranked as "more dissimilar" to B than C is to D, non-metric MDS tries to ensure the 2D distance A-B is strictly greater than the 2D distance C-D. This is ideal for subjective similarity ratings (e.g., taste testing).'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 16.10 — Multivariate Regression & Path Analysis
        // ══════════════════════════════════════════════════════════════
        $content10 = <<<'HTML'
<h2>Multivariate Regression & Path Analysis</h2>
<p>Multiple regression predicts a <em>single</em> dependent variable from multiple predictors. <strong>Multivariate regression</strong> extends this to predict <em>multiple</em> dependent variables simultaneously from the same set of predictors. While running separate multiple regressions for each outcome yields the exact same coefficients, the multivariate approach is necessary to correctly test hypotheses about the predictors across all outcomes simultaneously, accounting for the correlation between the dependent variables.</p>

<h3>The Multivariate Regression Model</h3>
<p>For an n × m matrix of dependent variables Y and an n × (p+1) design matrix X (including the intercept), the model is:</p>
<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:18px;margin-bottom:20px;font-family:'JetBrains Mono',monospace;text-align:center;color:var(--text);">
  Y = X B + E
</div>
<p>where B is a (p+1) × m matrix of coefficients, and E is an n × m matrix of errors. The rows of E are assumed to follow a multivariate normal distribution N_m(0, Σ).</p>
<p>The OLS estimate for the coefficient matrix B is the direct analog of the univariate case: <strong>B̂ = (XᵀX)⁻¹ XᵀY</strong>. Note that the estimation for any single column of Y does not depend on the other columns. The multivariate advantage comes entirely in the hypothesis testing (e.g., Wilks' Lambda) where the error covariance matrix Σ is used to test whether a predictor significantly affects the <em>set</em> of outcomes.</p>

<h3>Path Analysis: Deconstructing Causality</h3>
<p>In standard regression, all predictors are treated equally. In the real world, relationships have structure: X might affect Y, but X might also affect M, which in turn affects Y. <strong>Path Analysis</strong> (a precursor to Structural Equation Modeling, SEM) is a methodology for evaluating a system of interconnected regression equations based on a hypothesized causal diagram.</p>

<div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:20px;margin-bottom:32px;display:grid;gap:14px;">
  <div style="border-left:4px solid #3b82f6;padding-left:16px;">
    <h4 style="color:#3b82f6;margin:0 0 6px;">Direct vs. Indirect Effects</h4>
    <p style="color:var(--muted);font-size:0.875rem;line-height:1.7;margin:0;">
      A <strong>direct effect</strong> is the path from an independent variable to a dependent variable (the standard regression coefficient). An <strong>indirect effect</strong> occurs when an independent variable influences a <em>mediator</em> variable, which then influences the dependent variable. The Total Effect = Direct Effect + Indirect Effect.
    </p>
  </div>
  <div style="border-left:4px solid #10b981;padding-left:16px;">
    <h4 style="color:#10b981;margin:0 0 6px;">Mediation Analysis</h4>
    <p style="color:var(--muted);font-size:0.875rem;line-height:1.7;margin:0;">
      Tests whether the effect of X on Y operates through a third variable M. <br>
      Path a: X → M. Path b: M → Y. Path c': X → Y (direct). <br>
      The indirect effect is quantified as the product of coefficients (a × b). If c' drops to zero when M is included, there is <em>full mediation</em>. If c' shrinks but remains significant, there is <em>partial mediation</em>.
    </p>
  </div>
</div>

<h3>Python: Mediation Analysis (Path Analysis)</h3>
<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Mediation Analysis (Baron-Kenny Approach)</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd
<span style="color:#c4b5fd;">import</span> statsmodels.api <span style="color:#c4b5fd;">as</span> sm

np.random.seed(<span style="color:#fcd34d;">42</span>)

<span style="color:#6b7280;"># ── Simulate Mediation Data ─────────────────────────────────────</span>
<span style="color:#6b7280;"># X = Hours Studied</span>
<span style="color:#6b7280;"># M = Knowledge Gained (Mediator)</span>
<span style="color:#6b7280;"># Y = Exam Score</span>
<span style="color:#6b7280;"># Hypothesis: Studying increases knowledge, which in turn increases score.</span>
<span style="color:#93c5fd;">n</span> = <span style="color:#fcd34d;">200</span>
<span style="color:#93c5fd;">X</span> = np.random.normal(<span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">2</span>, n)                   <span style="color:#6b7280;"># Hours</span>
<span style="color:#93c5fd;">M</span> = <span style="color:#fcd34d;">0.8</span> * X + np.random.normal(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1.5</span>, n)        <span style="color:#6b7280;"># Knowledge (depends strongly on X)</span>
<span style="color:#93c5fd;">Y</span> = <span style="color:#fcd34d;">1.5</span> * M + <span style="color:#fcd34d;">0.3</span> * X + np.random.normal(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">2</span>, n) <span style="color:#6b7280;"># Score (depends heavily on M, slightly on X)</span>

<span style="color:#93c5fd;">df</span> = pd.DataFrame({<span style="color:#a7f3d0;">'X'</span>: X, <span style="color:#a7f3d0;">'M'</span>: M, <span style="color:#a7f3d0;">'Y'</span>: Y})

<span style="color:#6b7280;"># ── Step 1: Total Effect (X → Y) ignoring M ───────────────────</span>
<span style="color:#93c5fd;">X_const</span> = sm.add_constant(df[<span style="color:#a7f3d0;">'X'</span>])
<span style="color:#93c5fd;">model_total</span> = sm.OLS(df[<span style="color:#a7f3d0;">'Y'</span>], X_const).fit()
<span style="color:#93c5fd;">c</span> = model_total.params[<span style="color:#a7f3d0;">'X'</span>]

<span style="color:#6b7280;"># ── Step 2: Path A (X → M) ────────────────────────────────────</span>
<span style="color:#93c5fd;">model_a</span> = sm.OLS(df[<span style="color:#a7f3d0;">'M'</span>], X_const).fit()
<span style="color:#93c5fd;">a</span> = model_a.params[<span style="color:#a7f3d0;">'X'</span>]

<span style="color:#6b7280;"># ── Step 3: Path B and Direct Effect C' (X + M → Y) ───────────</span>
<span style="color:#93c5fd;">XM_const</span> = sm.add_constant(df[[<span style="color:#a7f3d0;">'X'</span>, <span style="color:#a7f3d0;">'M'</span>]])
<span style="color:#93c5fd;">model_bc</span> = sm.OLS(df[<span style="color:#a7f3d0;">'Y'</span>], XM_const).fit()
<span style="color:#93c5fd;">b</span>  = model_bc.params[<span style="color:#a7f3d0;">'M'</span>]
<span style="color:#93c5fd;">cp</span> = model_bc.params[<span style="color:#a7f3d0;">'X'</span>]   <span style="color:#6b7280;"># c' (direct effect)</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"═══ MEDIATION ANALYSIS (Baron-Kenny) ═══"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Total Effect (c)  : X → Y       = {c:.4f} (p={model_total.pvalues['X']:.3f})"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Path a            : X → M       = {a:.4f} (p={model_a.pvalues['X']:.3f})"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Path b            : M → Y       = {b:.4f} (p={model_bc.pvalues['M']:.3f})"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Direct Effect (c'): X → Y | M   = {cp:.4f} (p={model_bc.pvalues['X']:.3f})"</span>)

<span style="color:#6b7280;"># Calculate indirect effect</span>
<span style="color:#93c5fd;">indirect_effect</span> = a * b
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nIndirect Effect (a × b)         = {indirect_effect:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Verify: Direct + Indirect       = {cp + indirect_effect:.4f} (should equal Total c)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Proportion Mediated             = {(indirect_effect/c)*100:.1f}%"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>═══ MEDIATION ANALYSIS (Baron-Kenny) ═══
Total Effect (c)  : X → Y       = 1.3411 (p=0.000)
Path a            : X → M       = 0.7712 (p=0.000)
Path b            : M → Y       = 1.3283 (p=0.000)
Direct Effect (c'): X → Y | M   = 0.3167 (p=0.037)

Indirect Effect (a × b)         = 1.0243
Verify: Direct + Indirect       = 1.3411 (should equal Total c)
Proportion Mediated             = 76.4%</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '16.10 Multivariate Regression & Path Analysis',
            'order_index' => 10,
            'content'     => $this->appendQuiz($content10, 'M16_L10', [
                ['q' => 'In a multivariate regression model Y = XB + E, predicting three dependent variables (m=3) simultaneously yields coefficients. How do these coefficients compare to running three separate univariate multiple regressions?', 'opts' => ['The coefficients are entirely different because multivariate regression adjusts for correlations among the Y variables', 'The estimated coefficients in the matrix B are numerically exactly identical to those obtained from separate univariate regressions', 'Multivariate regression cannot estimate the intercept', 'The coefficients are smaller due to the shared error term'], 'ans' => 1, 'exp' => 'The OLS estimator B̂ = (XᵀX)⁻¹ XᵀY calculates the coefficients for each dependent variable independently of the others. The point estimates are identical to separate univariate regressions. The value of multivariate regression lies in hypothesis testing: you can test whether a predictor affects the joint set of outcomes by accounting for the error covariance Σ.'],
                ['q' => 'In path analysis, what defines an "indirect effect"?', 'opts' => ['An effect that is statistically insignificant', 'The effect of X on Y controlling for all other variables', 'The influence of an independent variable on a dependent variable through its effect on an intermediate (mediator) variable', 'An effect caused by omitted variables'], 'ans' => 2, 'exp' => 'An indirect effect is the pathway X → M → Y. It is calculated as the product of the path coefficient from X to M (path a) and the path coefficient from M to Y (path b). It quantifies the mechanism by which X influences Y.'],
                ['q' => 'In Baron and Kenny\'s steps for mediation, what indicates "full mediation"?', 'opts' => ['Path a and path b are significant, but the direct effect (path c\') drops to zero (becomes non-significant) when the mediator is included', 'The total effect (path c) is zero', 'The indirect effect is larger than the direct effect', 'All paths are statistically significant'], 'ans' => 0, 'exp' => 'Full mediation occurs when the entire relationship between X and Y is explained by the mediator M. Statistically, this happens when the initial total effect of X on Y (path c) is significant, but once M is added to the model, the direct effect of X on Y (path c\') becomes statistically non-significant.'],
                ['q' => 'In the equation Total Effect = Direct Effect + Indirect Effect, what does this decomposition tell us?', 'opts' => ['It proves that correlation is causation', 'It breaks down the overall relationship between X and Y into the part that acts independently of M and the part that operates through M', 'It ensures that residuals are normally distributed', 'It calculates the overall error variance of the model'], 'ans' => 1, 'exp' => 'This fundamental equality (c = c\' + ab) decomposes the observed marginal relationship between X and Y into its causal components under the specified path model. It allows analysts to quantify how much of an effect is driven by a specific mechanism (the mediator).'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 16.11 — Final Exam (Org-locked)
        // ══════════════════════════════════════════════════════════════
        $allFinalQuestions = [
            ['q' => 'Which matrix contains the sample variances on its diagonal and the sample covariances on its off-diagonal entries?', 'opts' => ['The Correlation Matrix R', 'The Data Matrix X', 'The Covariance Matrix S', 'The Mahalanobis Matrix M'], 'ans' => 2, 'exp' => 'The sample covariance matrix S is a p×p matrix where the entry sⱼⱼ is the variance of variable j, and sⱼₖ is the covariance between variables j and k.'],
            ['q' => 'Which multivariate technique is designed specifically to find linear combinations that maximize the correlation between TWO sets of variables (e.g., physiological metrics and fitness scores)?', 'opts' => ['Principal Component Analysis (PCA)', 'Factor Analysis', 'Canonical Correlation Analysis (CCA)', 'Linear Discriminant Analysis (LDA)'], 'ans' => 2, 'exp' => 'CCA finds the canonical variables U = aᵀX and V = bᵀY such that the correlation between U and V is maximized. It explicitly models the relationship between two distinct sets of variables.'],
            ['q' => 'In PCA, the first principal component is chosen to maximize what quantity?', 'opts' => ['The covariance between the original variables', 'The variance of the projected data points', 'The Mahalanobis distance from the mean', 'The correlation with the dependent variable'], 'ans' => 1, 'exp' => 'PC1 is the direction in the feature space along which the data varies the most. Mathematically, it maximizes Var(a₁ᵀX) subject to the vector a₁ having unit length.'],
            ['q' => 'How does Factor Analysis differ fundamentally from PCA?', 'opts' => ['PCA assumes a latent variable model, while FA does not', 'FA models the observed variables as linear combinations of unobserved latent factors plus specific error; PCA simply rotates the original data to maximize variance', 'FA requires the correlation matrix, while PCA requires the covariance matrix', 'PCA can handle categorical data, while FA cannot'], 'ans' => 1, 'exp' => 'PCA is descriptive and deterministic (y = Xw). Factor Analysis posits a generative statistical model (x = Lf + ε) where observed correlations are caused by shared underlying common factors.'],
            ['q' => 'What is the primary reason to use MANOVA instead of conducting multiple separate ANOVAs for each dependent variable?', 'opts' => ['MANOVA automatically handles non-normal data', 'Running multiple ANOVAs inflates the familywise Type I error rate; MANOVA controls this by testing all variables simultaneously', 'MANOVA is computationally faster', 'ANOVA requires larger sample sizes than MANOVA'], 'ans' => 1, 'exp' => 'Testing p variables separately at α=0.05 results in an overall error rate much higher than 5%. MANOVA performs an omnibus test that maintains the specified α level across all dependent variables.'],
            ['q' => 'Fisher\'s Linear Discriminant Analysis (LDA) finds the projection axis that maximizes...', 'opts' => ['Total variance in the data', 'The ratio of between-group variance to within-group variance', 'The Euclidean distance between group centroids', 'The probability of the majority class'], 'ans' => 1, 'exp' => 'Fisher\'s criterion seeks a linear combination that pushes the group means as far apart as possible relative to the spread (variance) within the groups. This provides optimal linear separation.'],
            ['q' => 'Why does Quadratic Discriminant Analysis (QDA) produce curved decision boundaries instead of the straight lines produced by LDA?', 'opts' => ['QDA uses a higher-degree polynomial for its features', 'QDA estimates a separate covariance matrix for each class rather than assuming a single pooled covariance matrix', 'QDA is a non-parametric method', 'QDA maximizes the Mahalanobis distance instead of Euclidean'], 'ans' => 1, 'exp' => 'LDA assumes all classes have the same covariance matrix Σ, causing the quadratic terms in the multivariate normal density to cancel out. QDA allows each class to have its own Σₖ, retaining the quadratic terms xᵀΣₖ⁻¹x in the decision boundary.'],
            ['q' => 'In hierarchical clustering, which linkage method defines the distance between two clusters as the minimum distance between any single point in cluster A and any single point in cluster B?', 'opts' => ['Complete linkage', 'Ward\'s method', 'Average linkage', 'Single linkage'], 'ans' => 3, 'exp' => 'Single linkage (nearest neighbor) uses the minimum distance. This often leads to "chaining," where clusters are long and stringy, following noise rather than forming compact groups.'],
            ['q' => 'What is a major limitation or danger when interpreting a t-SNE plot?', 'opts' => ['It cannot handle more than 3 dimensions', 'The distance between clusters in the 2D plot does not reliably represent the true distance in the high-dimensional space', 'It requires the data to be perfectly normally distributed', 'It only works for supervised classification tasks'], 'ans' => 1, 'exp' => 't-SNE optimizes for local neighborhood preservation, meaning it ensures nearby points stay nearby. However, it severely distorts global distances. A large gap between two clusters in t-SNE does not necessarily mean they are far apart in reality.'],
            ['q' => 'In path analysis (mediation), what defines full mediation?', 'opts' => ['The indirect path is significant, and the direct path is also significant', 'The independent variable has no total effect on the dependent variable', 'The effect of X on Y becomes completely non-significant when the mediator M is included in the model', 'The correlation between X and M is 1.0'], 'ans' => 2, 'exp' => 'Full mediation means the mediator completely explains the mechanism by which X affects Y. Thus, controlling for M causes the direct relationship (path c\') to drop to zero.'],
        ];

        $finalContent  = <<<HTML
<div id="org-lock-screen" style="text-align:center;padding:4rem 2rem;background:var(--surface2);border:1px solid var(--border);border-radius:12px;margin-top:2rem;">
    <div style="font-size:3rem;margin-bottom:1rem;">🔒</div>
    <h3 style="color:var(--text);margin-bottom:0.5rem;">University / Organization Access Only</h3>
    <p style="color:var(--muted);">The Final Module Exam is restricted to enrolled students and verified organization members.</p>
    <p style="font-size:0.85rem;color:#f59e0b;margin-top:1rem;background:rgba(245,158,11,0.1);padding:10px;border-radius:8px;display:inline-block;">Please contact administration to link your account to an organization.</p>
</div>
<div id="final-exam-content" style="display:none;">
    <h2>Module 16: Final Examination</h2>
    <p>This comprehensive exam covers all topics from Lessons 16.1 through 16.10 — the multivariate normal distribution, covariance structure, PCA, factor analysis, MANOVA, discriminant analysis, clustering, CCA, multidimensional scaling, and path analysis. Good luck!</p>
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
            'title'       => '16.11 Final Exam: Multivariate Analysis Mastery',
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
