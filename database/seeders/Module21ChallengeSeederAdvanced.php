<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module21ChallengeSeederAdvanced extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'advanced')->first();

        if (!$category) {
            $this->command->error("Advanced category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Remove existing challenges for this category (cascades to questions/options)
        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 21 — Machine Learning 2: Unsupervised Learning (Advanced)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Machine Learning 2: Unsupervised Learning',
            'description'           => 'Debug, optimise, and deeply reason about unsupervised learning code and algorithms. Questions involve subtle implementation bugs, mathematical derivations, hyperparameter sensitivity, and production-grade design decisions across all unsupervised techniques.',
            'time_limit_seconds'    => 1800,
            'base_xp'               => 2000,
            'order_index'           => 21,
        ]);

        $this->command->info("Seeding 50 advanced-level questions on Unsupervised Learning...");

        $qaData = [

            // ── 21.1 WHAT IS UNSUPERVISED LEARNING? ──────────────────────
            [
                'q' => "A self-supervised learning model (e.g. BERT) is pre-trained by masking words and predicting them from context — no human labels are used. How does self-supervised learning relate to unsupervised learning?\n\nA) They are identical — self-supervised learning IS unsupervised learning\nB) Self-supervised learning creates its own supervisory signal from data structure; it is a specialised form of unsupervised representation learning\nC) Self-supervised learning is a form of supervised learning requiring 50% labels\nD) Self-supervised learning and unsupervised learning have no overlap",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "The following code trains a self-supervised contrastive model (SimCLR-style) without labels:\n\nfor (x_i, x_j) in augmented_pairs(X):\n    z_i = encoder(x_i)\n    z_j = encoder(x_j)\n    loss = contrastive_loss(z_i, z_j, negatives)\n    loss.backward()\n\nWhat does the contrastive loss encourage?\n\nA) z_i and z_j (two augmented views of the same image) to be far apart\nB) z_i and z_j to be similar while pushing representations of different images apart\nC) The encoder to output zero vectors to minimise loss\nD) The encoder to memorise each training image",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "Which internal validation metric does NOT require ground-truth labels and measures both cohesion and separation simultaneously?",
                'opts' => [
                    ['Adjusted Rand Index (ARI)', false],
                    ['Normalised Mutual Information (NMI)', false],
                    ['Silhouette Score', true],
                    ['V-Measure', false],
                ],
            ],

            // ── 21.2 K-MEANS CLUSTERING ───────────────────────────────────
            [
                'q' => "The following K-Means implementation has a subtle bug:\n\ndef kmeans(X, K, iters=100):\n    centroids = X[np.random.choice(len(X), K, replace=False)]\n    for _ in range(iters):\n        dists = np.linalg.norm(X[:, None] - centroids[None, :], axis=2)\n        labels = np.argmin(dists, axis=1)\n        centroids = np.array([X[labels == k].mean(axis=0) for k in range(K)])\n    return labels, centroids\n\nWhat happens when a cluster becomes empty during an iteration?",
                'opts' => [
                    ['np.mean on an empty array returns 0 silently', false],
                    ['np.mean on an empty array returns NaN, causing centroid to become NaN and all subsequent distance calculations to produce NaN — the algorithm silently produces incorrect results', true],
                    ['An IndexError is raised immediately', false],
                    ['The empty cluster is automatically merged with the nearest centroid', false],
                ],
            ],
            [
                'q' => "K-Means minimises within-cluster sum of squares (WCSS). Show the WCSS calculation for:\nCluster 1: points [1, 3], centroid = 2\nCluster 2: points [7, 9], centroid = 8\n\nWCSS = ?",
                'opts' => [
                    ['(1-2)² + (3-2)² + (7-8)² + (9-8)² = 1 + 1 + 1 + 1 = 4', true],
                    ['(1+3)/2 + (7+9)/2 = 2 + 8 = 10', false],
                    ['|1-2| + |3-2| + |7-8| + |9-8| = 4', false],
                    ['(2-8)² = 36', false],
                ],
            ],
            [
                'q' => "K-Means with cosine distance (instead of Euclidean) is preferred for which data type?",
                'opts' => [
                    ['Geographical coordinates', false],
                    ['TF-IDF or word embedding vectors — cosine similarity measures directional similarity, making it robust to document length differences', true],
                    ['Time series with fixed-length windows', false],
                    ['Image pixel arrays normalised to [0,1]', false],
                ],
            ],
            [
                'q' => "Mini-Batch K-Means differs from standard K-Means by:\n\nA) Using only a random subset (mini-batch) of data per iteration to update centroids — much faster for large datasets at a small cost to cluster quality\nB) Running K-Means on multiple GPUs simultaneously\nC) Using a different distance metric (L1 instead of L2)\nD) Automatically selecting K via cross-validation",
                'opts' => [
                    ['A', true],
                    ['B', false],
                    ['C', false],
                    ['D', false],
                ],
            ],

            // ── 21.3 DBSCAN ───────────────────────────────────────────────
            [
                'q' => "The following DBSCAN result shows all points assigned to cluster -1 (noise):\n\nfrom sklearn.cluster import DBSCAN\ndb = DBSCAN(eps=0.001, min_samples=5)\nlabels = db.fit_predict(X_scaled)\nprint(np.unique(labels))  # [-1]\n\nWhat is the most likely cause and fix?",
                'opts' => [
                    ['min_samples is too high — reduce to 1', false],
                    ['ε=0.001 is too small for the data scale — no point has 5 neighbours within such a tiny radius. Use a k-distance plot to estimate a suitable ε', true],
                    ['DBSCAN requires standardised data — X is not scaled', false],
                    ['All points are genuine outliers — no fix needed', false],
                ],
            ],
            [
                'q' => "OPTICS (Ordering Points To Identify the Clustering Structure) extends DBSCAN by:\n\nA) Fixing the sensitivity to ε by producing a reachability plot that reveals cluster structure across all ε values simultaneously\nB) Running DBSCAN in parallel across multiple cores\nC) Adding a supervised signal to guide density estimation\nD) Replacing min_samples with a K parameter like K-Means",
                'opts' => [
                    ['A', true],
                    ['B', false],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "DBSCAN time complexity is O(n log n) with a spatial index (e.g. k-d tree) but degrades to O(n²) in high dimensions. Why?",
                'opts' => [
                    ['High-dimensional data has more features, increasing memory allocation', false],
                    ['The curse of dimensionality renders k-d tree pruning ineffective — nearly all points fall within ε of each other, forcing brute-force neighbour searches', true],
                    ['DBSCAN\'s min_samples parameter scales with dimensionality', false],
                    ['Distance computations are O(d²) for d-dimensional data', false],
                ],
            ],

            // ── 21.4 HIERARCHICAL CLUSTERING & DENDROGRAMS ────────────────
            [
                'q' => "Single linkage (nearest neighbour) hierarchical clustering is prone to 'chaining.' What is chaining and why is it problematic?",
                'opts' => [
                    ['Chaining occurs when the algorithm merges too many clusters at once, losing resolution', false],
                    ['Chaining occurs when a few bridge points connect two distant groups — single linkage creates a chain of merges that lumps elongated or noise-connected groups into one large cluster, obscuring natural structure', true],
                    ['Chaining refers to the dendogram becoming too deep to visualise', false],
                    ['Chaining happens only when using cosine distance', false],
                ],
            ],
            [
                'q' => "The agglomerative clustering time complexity is O(n³) for naive implementations and O(n² log n) with optimised linkage. For a dataset of n=50,000 samples, what practical alternative should be considered?",
                'opts' => [
                    ['Increase RAM and run full agglomerative clustering', false],
                    ['Use BIRCH (Balanced Iterative Reducing and Clustering using Hierarchies) or mini-batch approaches that approximate hierarchical clustering in O(n) to O(n log n)', true],
                    ['Convert the data to 1D before clustering', false],
                    ['Use only complete linkage, which is O(n) always', false],
                ],
            ],
            [
                'q' => "Cophenetic correlation coefficient measures:\n\nA) How well the dendrogram distances represent the original pairwise distances between data points\nB) The average silhouette score across all clusters in the hierarchy\nC) The percentage of variance explained by the first principal component\nD) The number of clusters at the optimal cut height",
                'opts' => [
                    ['A', true],
                    ['B', false],
                    ['C', false],
                    ['D', false],
                ],
            ],

            // ── 21.5 PRINCIPAL COMPONENT ANALYSIS (PCA) ──────────────────
            [
                'q' => "PCA is implemented via Singular Value Decomposition (SVD): X = UΣVᵀ. The principal components are the columns of V (right singular vectors). What does Σ represent?\n\nA) The covariance matrix of X\nB) A diagonal matrix of singular values — their squares divided by (n-1) are the eigenvalues, proportional to explained variance\nC) The matrix of principal component scores\nD) The mean-centred version of X",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "The following pipeline applies PCA then classifies:\n\nfrom sklearn.pipeline import Pipeline\nfrom sklearn.decomposition import PCA\nfrom sklearn.linear_model import LogisticRegression\n\npipe = Pipeline([('pca', PCA(n_components=50)), ('clf', LogisticRegression())])\npipe.fit(X_train, y_train)\n\nWhy is it critical that the Pipeline is used rather than fitting PCA separately on all data (X_train + X_test) before splitting?",
                'opts' => [
                    ['Pipelines are slower and should only be used for convenience', false],
                    ['Fitting PCA on all data (including test) causes data leakage — the test set information influences the learned principal components, inflating validation performance', true],
                    ['PCA cannot be combined with classifiers', false],
                    ['LogisticRegression cannot accept PCA-transformed features', false],
                ],
            ],
            [
                'q' => "Kernel PCA extends standard PCA to non-linear data by:\n\nA) Using the kernel trick to compute PCA in a high-dimensional (possibly infinite) feature space without explicitly transforming the data\nB) Applying K-Means before PCA to find non-linear clusters\nC) Using gradient descent instead of SVD\nD) Applying PCA multiple times with different random seeds",
                'opts' => [
                    ['A', true],
                    ['B', false],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "Incremental PCA (sklearn.decomposition.IncrementalPCA) is used instead of standard PCA when:\n\nA) The dataset is too large to fit in RAM — IncrementalPCA processes data in mini-batches and updates components incrementally\nB) You want exactly 1 principal component always\nC) The data has more features than samples\nD) The data is already low-dimensional",
                'opts' => [
                    ['A', true],
                    ['B', false],
                    ['C', false],
                    ['D', false],
                ],
            ],

            // ── 21.6 t-SNE & UMAP ─────────────────────────────────────────
            [
                'q' => "t-SNE minimises the KL divergence between the high-dimensional and low-dimensional pairwise similarity distributions. A common artefact in t-SNE plots is that cluster SIZES are not meaningful. Why?",
                'opts' => [
                    ['t-SNE normalises all cluster sizes to appear equal', false],
                    ['t-SNE preserves local neighbourhood structure, not global distances — a large well-separated cluster in high-D may appear small in 2D if its internal density is low', true],
                    ['Cluster sizes are always proportional to the number of points', false],
                    ['t-SNE compresses all clusters to the same number of pixels', false],
                ],
            ],
            [
                'q' => "The following code produces different t-SNE plots on every run even with the same data:\n\nfrom sklearn.manifold import TSNE\nX_2d = TSNE(n_components=2).fit_transform(X)\n\nWhat must be added to make results reproducible?",
                'opts' => [
                    ['init=\"pca\" parameter', false],
                    ['random_state=42 — t-SNE uses stochastic gradient descent which depends on random initialisation', true],
                    ['n_iter=250 to cap at 250 iterations', false],
                    ['learning_rate=\"auto\"', false],
                ],
            ],
            [
                'q' => "Parametric UMAP trains a neural network to learn the UMAP embedding. Its main advantage over standard UMAP is:\n\nA) It is faster to compute on the training data\nB) It can embed new, unseen data points without refitting the entire model — the neural network generalises to new inputs\nC) It requires fewer hyperparameters\nD) It always produces better cluster separation",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],

            // ── 21.7 ANOMALY DETECTION: ISOLATION FOREST ─────────────────
            [
                'q' => "The following Isolation Forest code has a performance issue for streaming anomaly detection:\n\nfor new_point in data_stream:\n    clf = IsolationForest(n_estimators=100)\n    clf.fit(all_historical_data)\n    score = clf.decision_function([new_point])\n\nWhat is wrong and how should it be fixed?",
                'opts' => [
                    ['IsolationForest cannot score individual points', false],
                    ['Refitting on all historical data for every new point is O(n) per point — use a pre-trained model and call decision_function([new_point]) only, retraining periodically', true],
                    ['n_estimators=100 is too low for streaming data', false],
                    ['decision_function is not available for Isolation Forest', false],
                ],
            ],
            [
                'q' => "Extended Isolation Forest (EIF) improves upon standard Isolation Forest because:\n\nA) It uses deeper trees for better anomaly scoring\nB) Standard Isolation Forest has artefacts from axis-parallel cuts — EIF uses random hyperplanes at arbitrary angles, producing more uniform anomaly scores without artificial boundaries\nC) EIF runs on GPUs natively\nD) EIF requires fewer trees to converge",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "An Isolation Forest returns an anomaly score of -0.42 for a new data point. Using `predict()` this returns -1 (anomaly). The business wants a ranked list of the top 100 most suspicious points from 10,000 daily records. Which approach is correct?",
                'opts' => [
                    ['Use predict() and count the number of -1 labels', false],
                    ['Use decision_function() to get continuous scores, then sort ascending and take the 100 lowest scores (most anomalous)', true],
                    ['Set contamination=0.01 and all returned -1 labels are the top 100', false],
                    ['Multiply anomaly scores by -1 and sort descending', false],
                ],
            ],

            // ── 21.8 AUTOENCODERS FOR UNSUPERVISED LEARNING ──────────────
            [
                'q' => "The following VAE loss is implemented:\n\nreconstruction_loss = F.mse_loss(x_hat, x, reduction='sum')\nkl_loss = -0.5 * torch.sum(1 + log_var - mu.pow(2) - log_var.exp())\nloss = reconstruction_loss + beta * kl_loss\n\nWhat does the KL divergence term enforce, and what does β control?",
                'opts' => [
                    ['KL loss enforces smooth reconstruction; β controls learning rate', false],
                    ['KL loss regularises the latent space toward a standard normal distribution; β controls the trade-off between reconstruction quality (low β) and disentanglement/regularity of the latent space (high β)', true],
                    ['KL loss prevents overfitting by adding noise; β is the noise level', false],
                    ['KL loss measures the encoder\'s classification accuracy; β adjusts the decoder weight', false],
                ],
            ],
            [
                'q' => "An autoencoder is trained for anomaly detection. During training, reconstruction loss on the validation set stops decreasing and spikes. What is the most likely cause and fix?",
                'opts' => [
                    ['The bottleneck is too large — add more hidden units', false],
                    ['The learning rate is too high — the optimiser overshoots the loss minimum; reduce learning rate or use a scheduler', true],
                    ['The decoder architecture is too deep', false],
                    ['MSE loss is incorrect for autoencoders — switch to cross-entropy', false],
                ],
            ],
            [
                'q' => "A sparse autoencoder adds an L1 penalty on the bottleneck activations:\n\nloss = reconstruction_loss + lambda * torch.mean(torch.abs(latent))\n\nWhat effect does this penalty have on the learned representations?",
                'opts' => [
                    ['It forces all latent dimensions to be exactly zero', false],
                    ['It encourages most latent neurons to be inactive (near zero) for any given input, forcing the model to represent each input with a sparse, selective subset of features', true],
                    ['It prevents the reconstruction from overfitting', false],
                    ['It regularises the decoder weights, not the encoder', false],
                ],
            ],

            // ── 21.9 GAUSSIAN MIXTURE MODELS & SOFT CLUSTERING ───────────
            [
                'q' => "The EM algorithm for GMMs is guaranteed to:\n\nA) Converge to the global maximum of the log-likelihood\nB) Never decrease the log-likelihood at each step — it converges, but possibly to a local maximum\nC) Converge in exactly K·n iterations\nD) Always find the same solution regardless of initialisation",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "The following GMM is fit with 'spherical' covariance:\n\nfrom sklearn.mixture import GaussianMixture\ngmm = GaussianMixture(n_components=3, covariance_type='spherical')\ngmm.fit(X)\n\nHow does 'spherical' covariance constrain the model compared to 'full'?",
                'opts' => [
                    ['Spherical allows each component to have a different elliptical shape', false],
                    ['Spherical constrains each Gaussian to have equal variance in all directions (circular clusters) — fewer parameters than full covariance which allows arbitrary ellipsoidal shapes', true],
                    ['Spherical GMM is equivalent to K-Means', false],
                    ['Spherical covariance allows different variances per feature but not per cluster', false],
                ],
            ],
            [
                'q' => "A GMM degeneracy occurs when one Gaussian component collapses onto a single data point with near-zero variance, making its log-likelihood term go to infinity. What technique prevents this?",
                'opts' => [
                    ['Increase the number of EM iterations', false],
                    ['Add a regularisation term to the covariance matrix (reg_covar in sklearn) — a small value added to the diagonal prevents singular covariance matrices', true],
                    ['Use K-Means to initialise the GMM and it will never degenerate', false],
                    ['Reduce the number of components by 1 at each epoch', false],
                ],
            ],

            // ── 21.10 END-TO-END UNSUPERVISED ML PIPELINE ────────────────
            [
                'q' => "The following pipeline applies StandardScaler then KMeans, but there is a subtle data leakage bug:\n\nscaler = StandardScaler()\nX_all_scaled = scaler.fit_transform(X_all)  # fit on ALL data\n\nX_train_scaled = X_all_scaled[:n_train]\nX_test_scaled  = X_all_scaled[n_train:]\n\nkm = KMeans(n_clusters=5)\nkm.fit(X_train_scaled)\n\nWhat is the leakage?",
                'opts' => [
                    ['KMeans should not be applied to scaled data', false],
                    ['StandardScaler was fit on all data including X_test — test set statistics (mean, std) influenced the scaler, leaking test information into the training pipeline', true],
                    ['The split should use random_state', false],
                    ['KMeans should be fit on X_all_scaled, not X_train_scaled', false],
                ],
            ],
            [
                'q' => "Cluster stability analysis involves running K-Means multiple times with different random seeds and comparing cluster assignments using Adjusted Rand Index (ARI). A mean ARI of 0.95 across 20 runs vs. 0.30 suggests:\n\nARI=0.95 vs ARI=0.30?",
                'opts' => [
                    ['ARI=0.95: unstable clusters sensitive to initialisation; ARI=0.30: very stable', false],
                    ['ARI=0.95: highly stable clusters — different initialisations converge to the same solution; ARI=0.30: unstable — K may be wrong or data has no strong cluster structure', true],
                    ['Both indicate the same cluster quality', false],
                    ['ARI is not applicable to K-Means evaluations', false],
                ],
            ],
            [
                'q' => "An ML engineer builds a pipeline that uses K-Means cluster labels as features for a downstream regression model. What is the risk of this approach?",
                'opts' => [
                    ['K-Means labels are continuous, not categorical', false],
                    ['If K-Means is re-trained (e.g. on new data), cluster IDs may be permuted (cluster 0 and cluster 1 swap meaning) — the downstream model will receive different semantics for the same numerical label', true],
                    ['Regression models cannot accept categorical features', false],
                    ['K-Means labels always improve regression performance', false],
                ],
            ],
            [
                'q' => "A production unsupervised pipeline monitors Silhouette Score on a rolling weekly basis. The score degrades smoothly from 0.61 to 0.44 over 3 months. What monitoring strategy is most appropriate?\n\nA) Retrain every time the Silhouette Score drops by 0.01\nB) Set a threshold (e.g. 0.50) and trigger automated retraining when the rolling score drops below it, with alerting for sudden drops\nC) Switch to a supervised model once labels become available\nD) Ignore the degradation — Silhouette Score is an unreliable metric",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 21 — Unsupervised Learning (Advanced).");
    }
}