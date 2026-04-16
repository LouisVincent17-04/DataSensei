<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module16ChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (!$category) {
            $this->command->error("Professional category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 16 — Multivariate Analysis (Professional)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Multivariate Analysis',
            'description'           => 'Real-world professional-grade multivariate analysis — production dimensionality reduction pipelines, high-dimensional inference, robust estimation, scalable clustering, Bayesian multivariate models, model selection, edge cases in SEM, and performance engineering for large-scale multivariate workflows.',
            'time_limit_seconds'    => 3600,
            'base_xp'               => 3000,
            'order_index'           => 16,
        ]);

        $this->command->info("Seeding 50 professional-level questions...");

        $qaData = [

            // ── PRODUCTION PCA PIPELINES ───────────────────────────────────
            [
                'q' => "You are deploying a PCA-based anomaly detection system for IoT sensor data (p=200 features, ~10M rows/day). The PCA model is retrained weekly.\n\nA new week's data shows that the top-2 PCs now explain only 28% of variance (down from 71% last week).\n\nWhat does this indicate, and what is the correct operational response?",
                'opts' => [
                    ['Increase the number of components to 3 and redeploy', false],
                    ['The covariance structure of the sensor data has fundamentally changed (concept drift or sensor failure) — trigger an alert, investigate root cause, retrain the full pipeline on recent representative data before redeployment', true],
                    ['The sensors are working better than before', false],
                    ['This is expected variance from week to week — no action needed', false],
                ],
            ],
            [
                'q' => "A production ML pipeline applies PCA(n_components=50) to reduce 500 features before classification. After 6 months in production, model performance degrades. Investigation reveals new features were added to the upstream data pipeline.\n\nWhat is the correct fix?",
                'opts' => [
                    ['Retrain only the downstream classifier with the new features', false],
                    ['The PCA must be retrained from scratch on the full feature set including new features — the saved eigenvectors are stale and have no projection defined for the new features', true],
                    ['Simply drop the new features to maintain compatibility', false],
                    ['Increase n_components to accommodate new features without retraining', false],
                ],
            ],
            [
                'q' => "For a p=1000 feature dataset with n=500 observations, computing the full p×p covariance matrix is memory-intensive (1000×1000 float64 = 8 MB) and the eigendecomposition is O(p³).\n\nWhat is the computationally efficient alternative for PCA when n << p?",
                'opts' => [
                    ['Use the full covariance matrix but in sparse format', false],
                    ['Compute PCA via the n×n gram matrix XᵀX (500×500) instead — its eigendecomposition is O(n³) << O(p³), then recover p-dimensional eigenvectors. Or use TruncatedSVD/randomized PCA for even faster computation.', true],
                    ['Apply PCA column-by-column independently', false],
                    ['Use only the diagonal of the covariance matrix', false],
                ],
            ],

            // ── ROBUST ESTIMATION ─────────────────────────────────────────
            [
                'q' => "The Minimum Covariance Determinant (MCD) estimator computes the sample mean and covariance matrix from the subset of h observations (h ≈ 0.75n) whose classical covariance matrix has the SMALLEST determinant.\n\nWhy is this robust?",
                'opts' => [
                    ['Smaller subsets are always more representative', false],
                    ['By excluding the h observations most likely to be outliers (those that inflate the determinant), MCD produces a covariance estimate that is resistant to multivariate outliers', true],
                    ['MCD uses the median instead of the mean', false],
                    ['MCD computes the covariance on projected data', false],
                ],
            ],
            [
                'q' => "The Mahalanobis distance computed using the MCD robust estimates (μ_MCD, Σ_MCD) instead of classical (x̄, S) is more useful for outlier detection because:\n\n(A) MCD estimates are always larger, making outliers easier to see\n(B) Classical Mahalanobis distances can be 'masked' — outliers inflate S and thus appear to have small distances. MCD estimates are not inflated by the very outliers being detected.\n(C) MCD is faster to compute\n(D) MCD produces integer distance values",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],

            // ── HIGH-DIMENSIONAL INFERENCE ────────────────────────────────
            [
                'q' => "In high-dimensional MANOVA (p >> n), standard Wilks' Lambda breaks down because:\n\n(A) The within-group scatter matrix W is singular — det(W) = 0 and W⁻¹ is undefined\n(B) The F-approximation requires p = 1\n(C) High-dimensional data violates the normality assumption completely\n(D) The eigenvalues of W are all equal when p > n",
                'opts' => [
                    ['(A)', true],
                    ['(B)', false],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "Which test is appropriate for high-dimensional two-sample mean comparison (p >> n) where Hotelling's T² is infeasible?",
                'opts' => [
                    ['Standard Hotelling\'s T²', false],
                    ['Bai-Saranadasa or Chen-Qin test — designed for high-dimensional settings where p/n → c > 0, using sum-of-squares statistics that bypass covariance matrix inversion', true],
                    ['Paired t-test on each variable independently without correction', false],
                    ['MANOVA with ridge regularisation on W', false],
                ],
            ],
            [
                'q' => "Random Matrix Theory predicts that for a p×n matrix of i.i.d. entries (null case), the eigenvalue distribution of the sample covariance matrix follows the Marchenko-Pastur law.\n\nIn practice this is used in PCA to:\n\n(A) Confirm all components are informative\n(B) Identify components whose eigenvalues exceed the Marchenko-Pastur upper bound — these are considered statistically significant signal components, not noise\n(C) Set the number of factors in factor analysis to p\n(D) Normalise eigenvalues to sum to 1",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],

            // ── SCALABLE CLUSTERING ────────────────────────────────────────
            [
                'q' => "You need to cluster 50 million observations with p=100 features. Standard k-Means is infeasible due to memory constraints.\n\nWhich scalable approach is most appropriate?",
                'opts' => [
                    ['Hierarchical clustering with Ward linkage (O(n²) space)', false],
                    ['Mini-batch k-Means — processes random mini-batches of data, updating centroids incrementally. Near-identical results to full k-Means but O(batch_size × k) memory per step', true],
                    ['Load all data into RAM and run standard k-Means', false],
                    ['Apply DBSCAN (which requires the full pairwise distance matrix)', false],
                ],
            ],
            [
                'q' => "For clustering genomic data with 20,000 genes (features) and 500 samples, which preprocessing step is CRITICAL before applying k-Means?\n\n(A) Log-transform and standardise the gene expression values — k-Means is distance-based and will be dominated by high-variance genes without standardisation\n(B) Remove all genes with zero variance\n(C) Apply PCA to reduce to 2 components before clustering\n(D) Use only the top 10 most variable genes",
                'opts' => [
                    ['(A)', true],
                    ['(B)', false],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "Optimal Transport (Wasserstein distance) based clustering is preferred over k-Means when:\n\n(A) The clusters are perfectly spherical\n(B) The data lies on a manifold and cluster comparison requires accounting for the underlying geometry of the space rather than Euclidean distance\n(C) n < 100 observations\n(D) The number of clusters is unknown",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],

            // ── BAYESIAN MULTIVARIATE MODELS ──────────────────────────────
            [
                'q' => "In Bayesian Factor Analysis, the conjugate prior for the factor loading matrix Λ is typically:\n\n(A) A uniform distribution\n(B) A matrix-normal or column-wise normal prior — combined with an Inverse-Wishart prior on the unique variance matrix Θ\n(C) A Dirichlet distribution\n(D) A Poisson distribution",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "Bayesian non-parametric factor analysis using the Indian Buffet Process (IBP) prior has the key advantage of:\n\n(A) Requiring the number of factors to be fixed at k\n(B) Allowing the NUMBER of factors to be inferred from the data rather than pre-specified\n(C) Reducing computation time to O(1)\n(D) Guaranteeing orthogonal factors",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "The Inverse-Wishart distribution IW(Ψ, ν) is used as a prior for a covariance matrix Σ because:\n\n(A) It generates positive definite matrices — which is required for a valid covariance matrix\n(B) It generates diagonal matrices only\n(C) It guarantees eigenvalues are between 0 and 1\n(D) It is the only tractable distribution for matrices",
                'opts' => [
                    ['(A)', true],
                    ['(B)', false],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],

            // ── ADVANCED SEM & PATH ANALYSIS ──────────────────────────────
            [
                'q' => "You fit two SEM models: Model A (hypothesised) and Model B (nested, more constrained).\n\nΔχ² = χ²_B − χ²_A = 8.3,  Δdf = 3\n\nAt α = 0.05, the critical value χ²(3) = 7.81.\n\nWhat is the conclusion?",
                'opts' => [
                    ['Model B fits significantly better than Model A', false],
                    ['Model A fits significantly better — removing the 3 paths in Model B significantly worsens fit (Δχ² = 8.3 > 7.81, p < 0.05)', true],
                    ['The models are equivalent', false],
                    ['Neither model fits the data adequately', false],
                ],
            ],
            [
                'q' => "In SEM, the difference between formative and reflective measurement models is:\n\n(A) Formative: indicators cause the latent variable (arrows point from indicators TO the latent variable); Reflective: the latent variable causes the indicators\n(B) Formative models require more parameters\n(C) Reflective indicators are always uncorrelated\n(D) Formative models cannot be estimated",
                'opts' => [
                    ['(A)', true],
                    ['(B)', false],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "Equivalent models in SEM are models with:\n\n(A) The same number of paths\n(B) Identical fit indices (same χ², df, and thus the same RMSEA, CFI) but different causal structures — they are statistically indistinguishable from the data alone\n(C) The same theoretical interpretation\n(D) All paths significant",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],

            // ── MATRIX DERIVATIVES & OPTIMIZATION ────────────────────────
            [
                'q' => "The PCA objective is to maximise wᵀ S w subject to ‖w‖ = 1.\n\nUsing Lagrange multipliers:\n\n∂/∂w [wᵀ S w − λ(wᵀw − 1)] = 0\n\n2Sw − 2λw = 0  →  Sw = λw\n\nThis shows the optimal w is an _____ of S.",
                'opts' => [
                    ['Inverse', false],
                    ['Eigenvector', true],
                    ['Singular value', false],
                    ['Null vector', false],
                ],
            ],
            [
                'q' => "The CCA objective is equivalent to finding the canonical correlations as the square roots of the eigenvalues of:\n\nA = Σ_XX⁻¹ Σ_XY Σ_YY⁻¹ Σ_YX\n\nFor numerical stability when Σ_XX is near-singular (high-dimensional X), the recommended solution is:\n\n(A) Add a ridge penalty εI to Σ_XX before inversion\n(B) Apply kernel CCA\n(C) Both (A) and (B) are valid approaches\n(D) Use the Moore-Penrose pseudoinverse of Σ_XX",
                'opts' => [
                    ['(A) only', false],
                    ['(B) only', false],
                    ['(C) — ridge regularisation and kernel CCA are both valid regularised approaches', true],
                    ['(D) only', false],
                ],
            ],

            // ── MANIFOLD LEARNING ──────────────────────────────────────────
            [
                'q' => "Isomap extends MDS by:\n\n(A) Using Euclidean distances between all pairs of points\n(B) Approximating geodesic (along-manifold) distances using shortest paths on a nearest-neighbour graph before applying MDS — correctly handling curved manifolds\n(C) Using a kernel matrix instead of a distance matrix\n(D) Performing dimensionality reduction in stages",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "Locally Linear Embedding (LLE) assumes:\n\n(A) Each point lies on a global linear subspace\n(B) Each point can be reconstructed as a linear combination of its k nearest neighbours, AND this reconstruction should be preserved in the low-dimensional embedding\n(C) The data is drawn from a Gaussian distribution\n(D) All eigenvalues of the reconstruction matrix are positive",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],

            // ── MULTIVARIATE TIME SERIES ───────────────────────────────────
            [
                'q' => "A Vector Autoregression model VAR(p) for a k-dimensional time series Yₜ is:\n\nYₜ = c + A₁Yₜ₋₁ + A₂Yₜ₋₂ + ... + AₚYₜ₋ₚ + εₜ\n\nThe total number of parameters to estimate (excluding the intercept vector) when k=3, p=2 is:",
                'opts' => [
                    ['3 × 2 = 6', false],
                    ['3² × 2 = 18 (k² coefficients per lag × p lags)', true],
                    ['3 + 2 = 5', false],
                    ['3 × 2² = 12', false],
                ],
            ],
            [
                'q' => "Granger causality in a VAR model tests whether:\n\n(A) Variable X physically causes variable Y\n(B) Past values of X contain information that improves forecasts of Y beyond what past Y alone provides\n(C) X and Y are contemporaneously correlated\n(D) The VAR residuals are uncorrelated",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],

            // ── LATENT VARIABLE MODELS ────────────────────────────────────
            [
                'q' => "Variational Autoencoders (VAE) perform probabilistic dimensionality reduction. The loss function has two terms:\n\nL = E[log p(x|z)] − KL(q(z|x) || p(z))\n\nThe KL divergence term acts as:\n\n(A) A regulariser that keeps the latent space close to a standard normal distribution\n(B) A reconstruction error term\n(C) A sparsity penalty on the decoder weights\n(D) A measure of classification accuracy",
                'opts' => [
                    ['(A)', true],
                    ['(B)', false],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "Non-negative Matrix Factorisation (NMF) decomposes X ≈ W H with the constraint that W, H ≥ 0.\n\nThe non-negativity constraint leads to:\n\n(A) The same components as PCA\n(B) Parts-based representations — components are additive and interpretable as 'parts' of the data (e.g., face parts, topic words), unlike PCA which allows cancellations\n(C) Orthogonal components always\n(D) Exactly the same result as ICA",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],

            // ── EDGE CASES & PRACTICAL PITFALLS ──────────────────────────
            [
                'q' => "You run PCA on a dataset with a binary (0/1) variable mixed with continuous variables. The result is dominated by the first PC which perfectly separates the two binary classes.\n\nThe methodological problem is:\n\n(A) PCA assumes all variables are continuous — applying PCA to binary variables treats the 0/1 codes as if they were interval-level measurements, which is methodologically inappropriate. Use dedicated methods (e.g., MCA, FAMD) for mixed-type data.\n(B) Binary variables must be removed before PCA\n(C) PCA cannot handle more than one binary variable\n(D) This is correct and expected behaviour",
                'opts' => [
                    ['(A)', true],
                    ['(B)', false],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "In k-Means clustering, you notice one cluster has n=1 observation (a singleton cluster) after convergence. The best operational fix is:\n\n(A) Accept the result — singleton clusters are always meaningful\n(B) Re-run with k-Means++ initialisation and multiple restarts (n_init > 1) to avoid degenerate local minima, or consider DBSCAN which labels isolated points as noise rather than forcing them into a cluster\n(C) Remove the observation and rerun with the same k\n(D) Increase k by 1 to accommodate the outlier",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "A researcher computes pairwise correlations among p=500 variables and finds 112 pairs with |r| > 0.30 at p < 0.05.\n\nThe critical methodological problem is:\n\n(A) The sample size is too large\n(B) With 500×499/2 = 124,750 tests, we expect ~6,237 false positives at α=0.05 by chance alone — no multiple testing correction was applied. Bonferroni-corrected α = 0.05/124750 ≈ 4×10⁻⁷ would be required.\n(C) Correlations above 0.30 are always meaningful regardless of p-value\n(D) 500 variables is too few for this analysis",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "In Factor Analysis, 'Heywood cases' refer to:\n\n(A) Communalities exactly equal to 1.0 or unique variances ≤ 0 — an inadmissible solution indicating model misspecification, too many factors, or insufficient sample size\n(B) Factors with more than 10 high loadings\n(C) Factors that are perfectly correlated after rotation\n(D) Solutions where all communalities are below 0.3",
                'opts' => [
                    ['(A)', true],
                    ['(B)', false],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],

            // ── ADVANCED DISCRIMINANT ANALYSIS ────────────────────────────
            [
                'q' => "Flexible Discriminant Analysis (FDA) extends LDA by:\n\n(A) Using kernel methods or splines to create non-linear decision boundaries while maintaining the discriminant framework\n(B) Using more classes than standard LDA\n(C) Applying LDA to the correlation matrix instead of the covariance matrix\n(D) Removing regularisation from the within-group covariance estimate",
                'opts' => [
                    ['(A)', true],
                    ['(B)', false],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "Penalised Discriminant Analysis (PDA) is especially useful in functional data or image classification because:\n\n(A) It requires no training data\n(B) It adds a roughness penalty to the discriminant function coefficients — enforcing smoothness across spatially/temporally adjacent features, yielding more interpretable and regularised classifiers for high-dimensional structured predictors\n(C) It automatically selects the number of discriminant functions\n(D) It is equivalent to logistic regression",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],

            // ── PROFESSIONAL MISC ──────────────────────────────────────────
            [
                'q' => "The 'replication crisis' in psychology and social sciences has specific implications for multivariate methods. The key concern is:\n\n(A) Exploratory FA/SEM models are sometimes reported as confirmatory without validation on independent samples — overfitting to idiosyncratic covariance structure of the original sample produces results that do not replicate\n(B) MANOVA is too powerful and always finds significant effects\n(C) PCA automatically overfits to the training set\n(D) Cluster analysis produces too many clusters in large samples",
                'opts' => [
                    ['(A)', true],
                    ['(B)', false],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "Bootstrap confidence intervals for a PCA loading are constructed by:\n\n1. Resample n observations with replacement B times\n2. Run PCA on each bootstrap sample\n3. Record the loading of interest\n4. Use the 2.5th and 97.5th percentiles as the 95% CI\n\nA major complication specific to PCA bootstrapping is:\n\n(A) The bootstrap is too slow for PCA\n(B) Sign and order indeterminacy — eigenvectors can flip sign or swap order across bootstrap samples, requiring alignment (e.g., by Procrustes rotation) before computing CIs\n(C) Bootstrap samples must be of size n/2\n(D) PCA does not produce loadings in sklearn",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "In a production recommendation system, you use collaborative filtering based on matrix factorisation X ≈ UV ᵀ (X = user × item matrix with many missing entries).\n\nAfter deployment, new users have no ratings (cold-start problem). The most principled multivariate approach to handle cold-start is:\n\n(A) Assign all new users the same global-average latent vector\n(B) Use side-information (content features, demographics) in a hybrid model — e.g., warm-start with a regression from user features to latent factors trained on existing users\n(C) Retrain the entire matrix factorisation model from scratch for every new user\n(D) Exclude new users from recommendations entirely",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "Tensor decomposition (e.g., Tucker / HOSVD) generalises matrix SVD to multi-way arrays. A 3-way tensor (Users × Items × Time) decomposed via Tucker(R₁, R₂, R₃) produces:\n\n(A) R₁ + R₂ + R₃ components\n(B) A core tensor G of size R₁×R₂×R₃ and three factor matrices capturing structure in each mode — allowing temporal patterns and latent user/item factors to be jointly identified\n(C) A single diagonal matrix\n(D) An output identical to running matrix PCA on each time slice",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "A practitioner fits an SEM with CFI = 0.96, RMSEA = 0.04, SRMR = 0.06 but χ²(df=120) = 310, p < 0.001.\n\nWhich interpretation is correct?",
                'opts' => [
                    ['The model must be rejected because χ² is significant', false],
                    ['The model fits well — with large n, χ² is almost always significant (oversensitive). The practical fit indices (CFI > 0.95, RMSEA < 0.05, SRMR < 0.08) all indicate good fit. Report all indices and rely primarily on practical fit measures.', true],
                    ['The model must be accepted because CFI > 0.95', false],
                    ['The conflicting indices make interpretation impossible', false],
                ],
            ],
            [
                'q' => "What is the 'curse of multicollinearity' specifically in the context of Canonical Correlation Analysis, and how is it addressed in modern practice?\n\n(A) When Σ_XX or Σ_YY are singular, the CCA matrices are undefined — addressed with regularised CCA (rCCA) which adds ridge penalties εI to each block, or sparse CCA (sCCA) which additionally enforces sparsity in canonical weights\n(B) Multicollinearity causes CCA to converge to the null solution\n(C) CCA becomes equivalent to PCA when variables are collinear\n(D) Collinear variables must always be removed before CCA",
                'opts' => [
                    ['(A)', true],
                    ['(B)', false],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "In federated learning for multivariate analysis (e.g., PCA across hospitals without sharing patient data), the key challenge is:\n\n(A) PCA is undefined in distributed settings\n(B) Computing the global covariance matrix requires sharing raw data — private versions (e.g., federated PCA via power iteration on local covariance matrices with secure aggregation, or differentially private SVD) must be used to preserve data privacy\n(C) Each hospital must use a different number of principal components\n(D) Federated PCA always produces worse results than centralised PCA",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 16 — Multivariate Analysis (Professional).");
    }
}