<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module16ChallengeSeederAdvanced extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'advanced')->first();

        if (!$category) {
            $this->command->error("Advanced category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 16 — Multivariate Analysis (Advanced)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Multivariate Analysis',
            'description'           => 'Advanced multivariate analysis — debug Python/R code for PCA, LDA, MANOVA and clustering; interpret algorithmic outputs; reason through spectral properties; identify subtle methodological flaws; and work through demanding multi-step statistical derivations.',
            'time_limit_seconds'    => 2400,
            'base_xp'               => 2000,
            'order_index'           => 16,
        ]);

        $this->command->info("Seeding 50 advanced-level questions...");

        $qaData = [

            // ── DEBUGGING PCA CODE ────────────────────────────────────────
            [
                'q' => "This Python code is supposed to perform PCA but gives misleading results. What is the bug?\n\nfrom sklearn.decomposition import PCA\nimport numpy as np\n\nX = np.array([[1000, 1], [2000, 2], [3000, 3], [4000, 4]])\npca = PCA(n_components=2)\npca.fit(X)\nprint(pca.explained_variance_ratio_)",
                'opts' => [
                    ['PCA cannot handle 2 components for 4 observations', false],
                    ['The data was not standardized — the first column (values ~1000–4000) will dominate due to its large scale, making the PCA misleading. Use StandardScaler first.', true],
                    ['PCA requires more rows than columns', false],
                    ['The explained_variance_ratio_ method does not exist', false],
                ],
            ],
            [
                'q' => "What does the following output reveal about this PCA result?\n\nprint(pca.explained_variance_ratio_)\n# Output: [0.9999, 0.0001]\n\nprint(pca.components_)\n# PC1 ≈ [1.0, 0.0002]  — almost entirely the first variable",
                'opts' => [
                    ['The PCA is correct and the first variable is genuinely most important', false],
                    ['This is a scale artifact — the first variable has variance ~10⁶ while the second has variance ~1, so PCA is driven by scale, not information content. Standardise first.', true],
                    ['The second component should be removed', false],
                    ['Both components explain equal variance', false],
                ],
            ],
            [
                'q' => "What is wrong with this PCA pipeline for a train/test split?\n\nfrom sklearn.preprocessing import StandardScaler\nfrom sklearn.decomposition import PCA\n\nX_train_scaled = StandardScaler().fit_transform(X_train)\nX_test_scaled = StandardScaler().fit_transform(X_test)  # Bug here\npca = PCA(n_components=5)\nX_train_pca = pca.fit_transform(X_train_scaled)\nX_test_pca = pca.transform(X_test_scaled)",
                'opts' => [
                    ['PCA should be fit on the test set', false],
                    ['StandardScaler is fitted independently on the test set — this leaks test statistics into scaling. The scaler should be fitted ONLY on X_train, then applied to both.', true],
                    ['PCA cannot use 5 components', false],
                    ['fit_transform should not be used with PCA', false],
                ],
            ],

            // ── DEBUGGING LDA CODE ────────────────────────────────────────
            [
                'q' => "What is the fundamental problem with this code?\n\nfrom sklearn.discriminant_analysis import LinearDiscriminantAnalysis\n\nlda = LinearDiscriminantAnalysis(n_components=5)\nlda.fit(X_train, y_train)  # y_train has 3 classes, X_train has 4 features",
                'opts' => [
                    ['LDA cannot use 3 classes', false],
                    ['n_components=5 is invalid — LDA can extract at most min(n_classes−1, n_features) = min(2, 4) = 2 discriminant functions. Setting n_components=5 will raise an error.', true],
                    ['LDA requires equal sample sizes per class', false],
                    ['y_train should be a continuous variable', false],
                ],
            ],
            [
                'q' => "Inspect this LDA output:\n\nlda.scalings_  # Discriminant function coefficients\n# LD1: [0.001, 0.002, 0.001, 0.002]\n# LD2: [0.001, 0.001, 0.002, 0.001]\n\nBoth discriminants look almost identical and near-zero. The most likely cause is:",
                'opts' => [
                    ['LDA always produces near-zero coefficients', false],
                    ['The features were not standardised before LDA — LDA coefficients are sensitive to scale, and near-identical tiny values suggest all features are measured on very large scales', true],
                    ['The classes are perfectly separated', false],
                    ['There is a bug in sklearn\'s LDA implementation', false],
                ],
            ],

            // ── DEBUGGING CLUSTERING CODE ──────────────────────────────────
            [
                'q' => "This k-Means code runs but always produces different cluster assignments. What is the issue and the fix?\n\nfrom sklearn.cluster import KMeans\n\nfor trial in range(5):\n    km = KMeans(n_clusters=3)\n    labels = km.fit_predict(X)\n    print(labels)",
                'opts' => [
                    ['KMeans should use n_init=1 to be deterministic', false],
                    ['No random_state is set — k-Means uses random initialisation, so results vary each run. Fix: set random_state=42 (or any integer) for reproducibility.', true],
                    ['KMeans cannot be run in a loop', false],
                    ['fit_predict is not valid for KMeans', false],
                ],
            ],
            [
                'q' => "What does this silhouette analysis output reveal?\n\nfrom sklearn.metrics import silhouette_score\nfor k in [2, 3, 4, 5]:\n    labels = KMeans(n_clusters=k, random_state=0).fit_predict(X)\n    score = silhouette_score(X, labels)\n    print(f'k={k}: {score:.3f}')\n\n# k=2: 0.612\n# k=3: 0.587\n# k=4: 0.391\n# k=5: 0.302",
                'opts' => [
                    ['k=5 is optimal because more clusters are always better', false],
                    ['k=2 is optimal — it has the highest silhouette score (0.612), meaning observations are most tightly clustered and well-separated with 2 clusters', true],
                    ['k=4 is optimal because it is in the middle', false],
                    ['None of the k values are acceptable (all scores < 0.7)', false],
                ],
            ],

            // ── PCA: ADVANCED DERIVATIONS ─────────────────────────────────
            [
                'q' => "Prove (conceptually) why PCA components are orthogonal.\n\nThe eigenvectors of a real symmetric matrix (the covariance matrix Σ) corresponding to DISTINCT eigenvalues are always:",
                'opts' => [
                    ['Parallel', false],
                    ['Orthogonal — this is a fundamental theorem of linear algebra (spectral theorem for symmetric matrices)', true],
                    ['Equal in magnitude', false],
                    ['Dependent on the sample size n', false],
                ],
            ],
            [
                'q' => "The reconstruction of a data point x from k principal components is:\n\nx̂ = x̄ + Σᵢ₌₁ᵏ (vᵢᵀ(x − x̄)) vᵢ\n\nThe reconstruction error ‖x − x̂‖² equals:",
                'opts' => [
                    ['The sum of the first k eigenvalues', false],
                    ['The sum of the REMAINING (discarded) eigenvalues — the variance along the dropped components', true],
                    ['Zero for all points when k = p', false],
                    ['The Mahalanobis distance of x from x̄', false],
                ],
            ],
            [
                'q' => "Kernel PCA differs from standard PCA by:\n\n(A) Using a non-linear kernel function to implicitly map data to a higher-dimensional feature space, then performing PCA there\n(B) Using the L1 norm instead of L2\n(C) Applying PCA separately to subsets of the data\n(D) Computing PCA on the distance matrix directly",
                'opts' => [
                    ['(A)', true],
                    ['(B)', false],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "Incremental (online) PCA is needed when:\n\n(A) The dataset fits easily in RAM\n(B) The dataset is too large to fit in memory — Incremental PCA processes data in mini-batches\n(C) The number of variables p is very small\n(D) All variables are categorical",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],

            // ── FACTOR ANALYSIS: ADVANCED ─────────────────────────────────
            [
                'q' => "In confirmatory factor analysis (CFA), the chi-square goodness-of-fit test has a known problem with large samples. Specifically:\n\n(A) It always accepts the model for large n\n(B) It becomes hypersensitive — almost any model is rejected for large n even if fit is practically adequate\n(C) It requires the data to be binary\n(D) It cannot be computed for more than 10 variables",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "The model-implied covariance matrix in CFA/SEM is:\n\nΣ(θ) = Λ Φ Λᵀ + Θ\n\nwhere Λ = factor loadings, Φ = factor correlations, Θ = unique variances.\n\nFit is assessed by comparing Σ(θ) to the observed S. What does it mean when Σ(θ) ≈ S?",
                'opts' => [
                    ['The model has too many parameters', false],
                    ['The model reproduces the observed correlations well — good model fit', true],
                    ['The factors are uncorrelated', false],
                    ['The model is not identified', false],
                ],
            ],

            // ── MANOVA: ADVANCED ──────────────────────────────────────────
            [
                'q' => "Roy's Largest Root test statistic in MANOVA uses only the largest eigenvalue of the matrix B W⁻¹. Compared to Wilks' Lambda, Roy's Largest Root is:\n\n(A) Always more powerful regardless of conditions\n(B) Most powerful when there is ONE dominant dimension of group differences; less robust when departures from assumptions exist\n(C) Equivalent to Wilks' Lambda for 2 groups\n(D) Only used when the number of groups equals the number of DVs",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "A researcher runs a 2×3 factorial MANOVA (2-level factor A × 3-level factor B) with 4 dependent variables.\n\nThe main effect of B is significant (Wilks' Λ = 0.42, p < 0.001) but the interaction A×B is not significant (Λ = 0.91, p = 0.48).\n\nWhat is the correct interpretation?",
                'opts' => [
                    ['Factor B has no effect on the dependent variables', false],
                    ['The effect of factor B on the multivariate outcome is significant and consistent across levels of A (no interaction) — follow up with univariate ANOVAs per DV with Bonferroni correction', true],
                    ['The non-significant interaction means A and B must both be dropped', false],
                    ['Wilks\' Λ cannot be used in factorial designs', false],
                ],
            ],

            // ── LDA: ADVANCED ─────────────────────────────────────────────
            [
                'q' => "Regularised LDA (RDA) modifies the pooled within-group covariance matrix as:\n\nS_W(γ) = (1−γ) S_W + γ I\n\nThis regularisation is needed when:\n\n(A) The number of classes is large\n(B) p > n or S_W is near-singular, making its inverse unstable\n(C) The classes have equal sizes\n(D) The data is perfectly Gaussian",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "The relationship between LDA and PCA:\n\nLDA is supervised; PCA is unsupervised. Which is TRUE about their objectives?\n\n(A) Both maximise total variance\n(B) PCA maximises total variance; LDA maximises class separation (between/within ratio)\n(C) LDA maximises total variance; PCA maximises class separation\n(D) Both minimise reconstruction error",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],

            // ── CANONICAL CORRELATION: ADVANCED ──────────────────────────
            [
                'q' => "The redundancy index in CCA for variable set X is defined as:\n\nRed(X|Y) = r*² × (proportion of X variance explained by canonical variates of X)\n\nIt measures:\n\n(A) The proportion of total X variance predicted by the canonical variates of Y\n(B) The canonical correlation only\n(C) The proportion of Y variance explained by X\n(D) The number of significant canonical pairs",
                'opts' => [
                    ['(A)', true],
                    ['(B)', false],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],

            // ── CLUSTERING: ADVANCED ──────────────────────────────────────
            [
                'q' => "Gaussian Mixture Models (GMM) differ from k-Means by:\n\n(A) GMM makes hard assignments; k-Means makes soft probabilistic assignments\n(B) GMM provides soft (probabilistic) cluster assignments and can model elliptical clusters with different covariances; k-Means makes hard assignments to spherical clusters\n(C) k-Means uses the EM algorithm; GMM does not\n(D) GMM always produces the same number of clusters as k-Means",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "The BIC (Bayesian Information Criterion) is used with GMM to select the number of components. BIC penalises model complexity as:\n\nBIC = −2 ln(L̂) + k ln(n)\n\nwhere k = number of parameters. The preferred model has:\n\n(A) The largest BIC\n(B) The smallest BIC\n(C) BIC closest to zero\n(D) BIC closest to 1",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "HDBSCAN extends DBSCAN by:\n\n(A) Requiring the number of clusters to be specified\n(B) Automatically handling clusters of varying density — it builds a cluster hierarchy and extracts stable clusters without a fixed epsilon\n(C) Using Euclidean distance only\n(D) Performing k-Means as a post-processing step",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],

            // ── t-SNE / UMAP: ADVANCED ────────────────────────────────────
            [
                'q' => "UMAP (Uniform Manifold Approximation and Projection) is often preferred over t-SNE for large datasets because:\n\n(A) UMAP is slower but more accurate\n(B) UMAP is significantly faster, better preserves global structure, and produces deterministic results (with a fixed random seed)\n(C) UMAP requires no hyperparameters\n(D) UMAP always produces 2D output only",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "A key warning when interpreting t-SNE / UMAP plots for cluster sizes and distances:\n\n(A) Cluster sizes in the plot accurately reflect their true relative sizes\n(B) Distances between clusters in the plot accurately represent true inter-cluster distances\n(C) Both cluster sizes and between-cluster distances in t-SNE/UMAP plots are NOT faithfully preserved and should not be interpreted quantitatively\n(D) t-SNE/UMAP plots are only valid when n < 1000",
                'opts' => [
                    ['(A)', false],
                    ['(B)', false],
                    ['(C)', true],
                    ['(D)', false],
                ],
            ],

            // ── MDS: ADVANCED ─────────────────────────────────────────────
            [
                'q' => "In the double-centring step of classical MDS, the squared distance matrix D² is converted to B using:\n\nB = −(1/2) H D² H\n\nwhere H = I − (1/n)11ᵀ is the centring matrix.\n\nWhat does double-centring achieve?",
                'opts' => [
                    ['It normalises all distances to be between 0 and 1', false],
                    ['It converts squared distances into an inner product (Gram) matrix B = XᵀX, making eigendecomposition possible', true],
                    ['It removes negative eigenvalues automatically', false],
                    ['It applies a log transformation to the distances', false],
                ],
            ],

            // ── MULTIVARIATE REGRESSION: ADVANCED ────────────────────────
            [
                'q' => "Ridge regression adds an L2 penalty λ‖β‖² to the OLS objective. As λ → ∞, what happens to the coefficient estimates?",
                'opts' => [
                    ['They explode to ±∞', false],
                    ['They all shrink towards zero (but never exactly zero unless λ = ∞)', true],
                    ['They become the OLS estimates', false],
                    ['They all become exactly equal to each other', false],
                ],
            ],
            [
                'q' => "Lasso regression (L1 penalty) differs from Ridge (L2) in that Lasso:\n\n(A) Shrinks all coefficients equally\n(B) Can set some coefficients exactly to zero — performing automatic variable selection\n(C) Always retains all variables in the model\n(D) Is only applicable when p > n",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "The hat matrix H = X(XᵀX)⁻¹Xᵀ in multiple regression has the property that:\n\nŷ = Hy\n\nThe diagonal entries hᵢᵢ (leverage values) measure:\n\n(A) How large the residual is for observation i\n(B) How influential observation i is in determining the fitted values — high leverage points are far from the centroid in predictor space\n(C) The coefficient for the i-th predictor\n(D) Whether observation i is an outlier in y",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],

            // ── SPECTRAL METHODS ──────────────────────────────────────────
            [
                'q' => "Spectral clustering works by:\n\n1. Computing the graph Laplacian L = D − W (D = degree matrix, W = affinity matrix)\n2. Computing the first k eigenvectors of L\n3. Clustering the n×k matrix of eigenvectors with k-Means\n\nWhy is this more powerful than standard k-Means on the original data?",
                'opts' => [
                    ['It requires less computation', false],
                    ['The eigenvectors embed the data into a space where non-convex, irregularly shaped clusters become linearly separable — k-Means can then find them', true],
                    ['It eliminates the need to choose k', false],
                    ['The graph Laplacian removes all noise from the data', false],
                ],
            ],
            [
                'q' => "The normalised graph Laplacian L_sym = D^(-1/2) L D^(-1/2) is preferred over the unnormalised Laplacian when:\n\n(A) All nodes have the same degree\n(B) The graph has nodes with very different degrees — normalisation prevents high-degree nodes from dominating the embedding\n(C) The dataset has fewer than 100 points\n(D) You want to use cosine similarity",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],

            // ── SEM: ADVANCED ─────────────────────────────────────────────
            [
                'q' => "Model identification in SEM requires:\n\ndegrees of freedom = (p(p+1)/2) − q ≥ 0\n\nwhere p = observed variables and q = free parameters.\n\nA model with p=4 observed variables has p(p+1)/2 = 10 unique variances/covariances.\nIf q = 12 free parameters, the model is:\n\n(A) Just-identified\n(B) Over-identified\n(C) Under-identified (not estimable)\n(D) Saturated",
                'opts' => [
                    ['(A)', false],
                    ['(B)', false],
                    ['(C)', true],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "Mediation analysis in SEM tests the hypothesis that variable M mediates the effect of X on Y.\n\nThe indirect effect is: a × b (where a = X→M path, b = M→Y path)\n\nThe most statistically rigorous method to test whether a×b is significantly different from zero is:\n\n(A) The Sobel test\n(B) Bootstrapped confidence intervals for the product a×b\n(C) A t-test on the residuals\n(D) Comparing AIC values",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],

            // ── ADVANCED MISC ──────────────────────────────────────────────
            [
                'q' => "What does this Python snippet compute, and what method does it implement?\n\nimport numpy as np\nX_c = X - X.mean(axis=0)\nU, S, Vt = np.linalg.svd(X_c, full_matrices=False)\nscores = U * S\nloadings = Vt.T",
                'opts' => [
                    ['LDA via singular value decomposition', false],
                    ['PCA via SVD of the mean-centred data matrix — scores are the principal component scores and loadings are the principal component directions', true],
                    ['CCA via singular value decomposition', false],
                    ['Factor analysis with oblique rotation', false],
                ],
            ],
            [
                'q' => "What is the relationship between PCA via SVD of X and PCA via eigendecomposition of Xᵀ X?\n\nIf X = U S Vᵀ (SVD), then the eigenvalues of (1/(n−1)) Xᵀ X are:",
                'opts' => [
                    ['The diagonal of U', false],
                    ['s²ᵢ / (n−1) — the squared singular values divided by (n−1)', true],
                    ['The columns of V', false],
                    ['s²ᵢ × (n−1)', false],
                ],
            ],
            [
                'q' => "In a high-dimensional dataset (p >> n), applying standard LDA fails because:\n\n(A) There are too many classes\n(B) The within-group scatter matrix S_W is singular (rank ≤ n − k < p), making S_W⁻¹ undefined\n(C) LDA requires equal class sizes\n(D) The discriminant scores are always zero when p > n",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "In PCA, the 'proportion of variance explained' (PVE) is computed as:\n\nPVE_k = λₖ / Σᵢ λᵢ\n\nIf you apply PCA to the CORRELATION matrix (standardised PCA), each variable contributes exactly 1 unit of variance. For p=8 variables, what is the sum of all eigenvalues?",
                'opts' => [
                    ['It depends on the data', false],
                    ['8 — the trace of the correlation matrix is always p', true],
                    ['1', false],
                    ['p² = 64', false],
                ],
            ],
            [
                'q' => "What does negative eigenvalue(s) in classical MDS indicate, and what is the common fix?\n\n(A) The original dissimilarity matrix is non-Euclidean (distances don't satisfy triangle inequality) — fix by adding a constant c to all off-diagonal dissimilarities before MDS\n(B) The data has outliers — fix by removing them\n(C) MDS requires more dimensions — always use p dimensions\n(D) The centering matrix H was applied incorrectly",
                'opts' => [
                    ['(A)', true],
                    ['(B)', false],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "Independent Component Analysis (ICA) differs from PCA in that ICA seeks components that are:\n\n(A) Uncorrelated only\n(B) Statistically independent (higher-order independence, not just uncorrelated), making ICA suitable for blind source separation\n(C) Orthogonal and maximum variance\n(D) Correlated with the original variables",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "In k-Means++, the initialisation algorithm selects the first centroid randomly and subsequent centroids with probability proportional to their squared distance from the nearest existing centroid. This improves upon random initialisation by:\n\n(A) Guaranteeing convergence to the global optimum\n(B) Spreading initial centroids apart — reducing the chance of poor local minima and typically requiring fewer iterations\n(C) Automatically determining k\n(D) Eliminating the need for the assignment step",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "What is the Procrustes problem in multivariate analysis?\n\n(A) Finding the optimal rotation (and possibly scaling/reflection) matrix that best aligns one configuration of points to another in the least-squares sense\n(B) Detecting outliers in a multivariate dataset\n(C) Selecting the optimal number of factors in FA\n(D) Testing the equality of two covariance matrices",
                'opts' => [
                    ['(A)', true],
                    ['(B)', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 16 — Multivariate Analysis (Advanced).");
    }
}