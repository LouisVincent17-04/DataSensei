<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module21ChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (!$category) {
            $this->command->error("Professional category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Remove existing challenges for this category (cascades to questions/options)
        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 21 — Machine Learning 2: Unsupervised Learning (Professional)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Machine Learning 2: Unsupervised Learning',
            'description'           => 'Professional-grade unsupervised ML challenges rooted in production system design, scalability constraints, mathematical rigour, edge cases, and real-world failure modes. Demands expert command of all unsupervised techniques and their interplay with downstream systems.',
            'time_limit_seconds'    => 2400,
            'base_xp'               => 3000,
            'order_index'           => 21,
        ]);

        $this->command->info("Seeding 50 professional-level questions on Unsupervised Learning...");

        $qaData = [

            // ── 21.1 WHAT IS UNSUPERVISED LEARNING? ──────────────────────
            [
                'q' => "A Spotify-scale recommendation system must cluster 500 million listening sessions daily without labels. The system uses a two-stage approach:\n1. Offline: train UMAP + K-Means on a 1% sample\n2. Online: assign new sessions to clusters in real-time\n\nWhat is the critical correctness requirement for Stage 2 to be valid?",
                'opts' => [
                    ['Stage 2 must re-run UMAP on all new sessions each day', false],
                    ['The UMAP model from Stage 1 must be saved and used to transform new sessions into the same embedding space — if refitted on new data, the coordinate system changes and the Stage 1 centroids no longer apply', true],
                    ['Stage 2 must use a different K than Stage 1', false],
                    ['K-Means centroids must be re-serialised every hour', false],
                ],
            ],
            [
                'q' => "In federated unsupervised learning, client devices train local clustering models on private data. A central server aggregates model updates without seeing raw data. Which aggregation approach is most principled for K-Means federation?\n\nA) Average the cluster centroids from all clients weighted by their dataset size\nB) Re-run K-Means on the server using the centroids as pseudo-data points\nC) Select the best client model by lowest local inertia\nD) Merge all client clusters then prune to K total clusters",
                'opts' => [
                    ['A', true],
                    ['B', false],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "Adjusted Mutual Information (AMI) and Adjusted Rand Index (ARI) both measure clustering quality against ground truth. Under which condition does AMI give more informative results than ARI?\n\nA) When clusters are highly imbalanced in size — ARI is sensitive to large dominant clusters, AMI better reflects information overlap\nB) When no ground truth is available\nC) When comparing more than 3 clustering algorithms\nD) AMI and ARI are always identical",
                'opts' => [
                    ['A', true],
                    ['B', false],
                    ['C', false],
                    ['D', false],
                ],
            ],

            // ── 21.2 K-MEANS CLUSTERING ───────────────────────────────────
            [
                'q' => "Bisecting K-Means is an alternative to standard K-Means. It works by:\n\nA) Running K=2 on the full dataset, then recursively bisecting the largest (or worst) cluster until K clusters are reached\nB) Running K-Means K times with K=1,2,...,K and selecting the best\nC) Starting with K clusters and merging the two closest\nD) Applying K-Means only to the outliers identified by Isolation Forest",
                'opts' => [
                    ['A', true],
                    ['B', false],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "A data team notices K-Means clusters shift every time the model is retrained on new data, breaking a downstream dashboard that uses cluster IDs as category labels. What is the professional solution?\n\nA) Fix random_state=42 to make K-Means deterministic\nB) After each retraining, align new clusters to old clusters using the Hungarian algorithm on centroid distances, then remap IDs consistently\nC) Use DBSCAN instead — it does not use random initialisation\nD) Never retrain the K-Means model",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "K-Medoids (PAM — Partitioning Around Medoids) uses actual data points as cluster centres instead of mean vectors. The key advantage over K-Means for a dataset of customer transaction IDs (categorical features with no meaningful mean) is:\n\nA) K-Medoids is always faster than K-Means\nB) Medoids are actual data points and thus interpretable; K-Means centroids (averages of categorical data) are meaningless\nC) K-Medoids automatically selects K\nD) K-Medoids handles missing values natively",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "A K-Means model is deployed to assign new retail products to clusters. After 6 months, 30% of products are assigned to a single cluster that was originally only 5% of products. What metric should be tracked to detect this distribution shift, and what does it indicate?\n\nA) Silhouette Score drops — model has too many clusters\nB) Inertia increases — centroids moved away from data\nC) Cluster membership distribution diverges from training baseline (Jensen-Shannon divergence between historical and current cluster proportions) — indicates concept drift requiring retraining\nD) Reconstruction error exceeds threshold — bottleneck too small",
                'opts' => [
                    ['A', false],
                    ['B', false],
                    ['C', true],
                    ['D', false],
                ],
            ],

            // ── 21.3 DBSCAN ───────────────────────────────────────────────
            [
                'q' => "A DBSCAN model trained on GPS coordinates (lat/lon in degrees) is deployed. In polar regions, 1 degree of longitude covers only 50 km, but at the equator it covers 111 km. What problem arises and how should it be fixed?",
                'opts' => [
                    ['DBSCAN cannot handle lat/lon coordinates — use a flat grid', false],
                    ['ε in Euclidean degrees means different physical distances at different latitudes — use Haversine distance metric and express ε in kilometres to ensure geographic consistency', true],
                    ['Increase min_samples at higher latitudes', false],
                    ['Cluster lat and lon separately with two DBSCAN models', false],
                ],
            ],
            [
                'q' => "HDBSCAN (Hierarchical DBSCAN) improves over DBSCAN by:\n\nA) Finding only spherical clusters\nB) Building a hierarchy of clusters across all ε values and extracting the most stable clusters — it handles varying density without requiring a fixed ε, making it robust to multi-density data\nC) Running DBSCAN once for each value of ε from 0.01 to 1.0 and voting\nD) Using a random forest to classify core vs. noise points",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "The following DBSCAN pipeline is applied to a 10 million row dataset with 128-dimensional embeddings:\n\nfrom sklearn.cluster import DBSCAN\ndb = DBSCAN(eps=0.5, min_samples=10, metric='euclidean', algorithm='ball_tree')\ndb.fit(X_embeddings)\n\nThis fails with a MemoryError. What is the root cause and solution?\n\nA) ball_tree requires O(n²) memory for the distance matrix at 128 dimensions; use `algorithm='auto'` with an approximate nearest neighbour index (e.g. FAISS) and batch processing\nB) min_samples=10 is too small for 10M rows\nC) DBSCAN cannot process more than 1M rows regardless of algorithm\nD) 128 dimensions requires eps to be reduced to 0.001",
                'opts' => [
                    ['A', true],
                    ['B', false],
                    ['C', false],
                    ['D', false],
                ],
            ],

            // ── 21.4 HIERARCHICAL CLUSTERING & DENDROGRAMS ────────────────
            [
                'q' => "A bioinformatics team uses hierarchical clustering with complete linkage on a gene expression matrix (5000 genes × 200 samples). They observe that two biologically unrelated gene groups are always merged early in the dendrogram. What is the most likely methodological issue?\n\nA) Complete linkage is poorly suited — it should be Ward linkage\nB) The distance metric (e.g. Euclidean on raw counts) is inappropriate — RNA-seq data requires count-normalisation and a correlation-based distance metric to reflect biological similarity\nC) The dendrogram needs to be cut at a lower height\nD) 5000 genes is too many for hierarchical clustering",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "Dynamic Time Warping (DTW) distance is used in hierarchical clustering of time series (e.g. stock price trajectories) instead of Euclidean distance because:\n\nA) DTW is always faster to compute than Euclidean distance\nB) DTW aligns sequences that are similar in shape but shifted in time — Euclidean distance penalises temporal misalignment heavily, grouping differently-timed patterns incorrectly\nC) Euclidean distance cannot be computed on time series\nD) DTW automatically determines the number of clusters",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "A consulting team delivers a dendrogram to a client who asks to cut it to get 'meaningful segments' without specifying a number. The team uses the 'inconsistency coefficient' to select the cut height automatically. This method selects the cut where:\n\nA) The dendrogram has the most horizontal merges\nB) The merge height is most inconsistent with the average height of its sub-merges — a large inconsistency coefficient indicates a natural cluster boundary\nC) The total number of clusters equals the square root of n\nD) The first merge above height=1 is used",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],

            // ── 21.5 PRINCIPAL COMPONENT ANALYSIS (PCA) ──────────────────
            [
                'q' => "A PCA pipeline is retrained monthly. The team notices that even when the underlying data distribution is stable, the sign of some principal component loadings flips between months (e.g. PC1 loadings change from [0.6, 0.8] to [-0.6, -0.8]). What causes this and how is it handled?\n\nA) The data scaler is changing the sign of features each month\nB) PCA eigenvectors are only defined up to sign — a flip in sign is mathematically equivalent. Downstream systems should be sign-corrected by anchoring the dominant loading to always be positive\nC) The covariance matrix has changed sign, indicating data corruption\nD) PCA should be re-run until sign stabilises",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "Randomised SVD (sklearn.utils.extmath.randomized_svd) is used in TruncatedSVD and fast PCA for large matrices. Its computational advantage over exact SVD is:\n\nA) It computes only the top k singular values/vectors in O(n·d·k) instead of full O(min(n,d)³) — massively faster for large n, d and small k\nB) It uses GPU acceleration automatically\nC) It avoids centring the data, preventing numerical issues\nD) It produces more accurate components than exact SVD for k=50",
                'opts' => [
                    ['A', true],
                    ['B', false],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "A data scientist uses PCA for feature extraction before training a classifier. After deployment, the model's performance degrades. Investigation reveals the explained variance ratio of the top 10 PCs has dropped from 91% to 64%. What does this indicate and what action is needed?\n\nA) The classifier is overfitting — add dropout\nB) The data distribution has changed (concept drift) — the principal components trained on old data no longer explain the new data well. Refit PCA and the classifier on recent data\nC) More principal components should have been retained initially\nD) The StandardScaler is malfunctioning",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],

            // ── 21.6 t-SNE & UMAP ─────────────────────────────────────────
            [
                'q' => "A team uses UMAP to project 1 million product embeddings (dim=256) to 2D for clustering. The naive UMAP call runs out of memory. What architectural change makes this feasible?\n\nA) Reduce n_components from 2 to 1\nB) Use a two-stage approach: train UMAP on a representative 100K sample, then use the fitted model's transform() to embed all 1M points — reduces memory from O(n²) to O(n·k) where k is n_neighbors\nC) Increase n_neighbors to reduce memory usage\nD) Apply UMAP directly without StandardScaler to save memory",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "A researcher publishes a paper claiming their new drug target compound clusters distinctly from known non-targets in a t-SNE plot. A reviewer raises concerns. What is the fundamental validity issue?\n\nA) t-SNE should only be used with more than 10,000 compounds\nB) t-SNE plots are not suitable for statistical inference — apparent separation may be a visualisation artefact caused by perplexity choice, not real high-dimensional separability. The claim must be validated in the original high-dimensional space\nC) The paper should have used UMAP instead of t-SNE\nD) t-SNE requires supervised labels to produce meaningful plots",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "In a real-time anomaly detection pipeline using UMAP, a new point x arrives and must be classified as normal or anomalous based on its 2D UMAP position relative to known normal clusters. What is the critical limitation of this system?\n\nA) UMAP cannot embed single points — only batches of at least 100\nB) The `transform()` of a single point into the UMAP space can be unstable — small perturbations in the high-dimensional input may cause large position changes in 2D, making the 2D threshold unreliable as an anomaly boundary\nC) The Euclidean distance in 2D UMAP space is always meaningful\nD) UMAP transform is O(n) for each new point",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],

            // ── 21.7 ANOMALY DETECTION: ISOLATION FOREST ─────────────────
            [
                'q' => "A credit card fraud team uses Isolation Forest with contamination=0.001 (0.1% expected fraud rate). In production, they observe the model flags 8% of transactions as fraud — far above 0.001. Investigation shows the data distribution in production is heavily right-skewed with many large transactions. What is the most likely cause?\n\nA) The contamination parameter should be set to 0.08\nB) The Isolation Forest was trained on a different distribution (possibly balanced or normalised data) — the production skew means many legitimate high-value transactions are anomalous relative to the training distribution, causing over-flagging\nC) Isolation Forest cannot handle financial data\nD) n_estimators is too low, causing random flag selection",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "Local Outlier Factor (LOF) measures anomalies differently from Isolation Forest. LOF flags a point as anomalous when:\n\nA) Its feature values are outside 3 standard deviations from the mean\nB) Its local density is significantly lower than that of its k nearest neighbours — it is in a sparse region relative to its neighbourhood, regardless of global data structure\nC) The Isolation Forest scores it below -0.5\nD) It appears fewer than min_samples times in the dataset",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "An Isolation Forest model is evaluated with Precision@K (precision of the top K most anomalous points). At contamination=0.05, K=500 from n=10,000:\n• True positives in top 500: 320\n• True anomalies in dataset: 400\n\nCompute Precision@K and Recall@K.",
                'opts' => [
                    ['Precision@K = 320/500 = 0.64; Recall@K = 320/400 = 0.80', true],
                    ['Precision@K = 400/500 = 0.80; Recall@K = 320/500 = 0.64', false],
                    ['Precision@K = 320/400 = 0.80; Recall@K = 320/500 = 0.64', false],
                    ['Precision@K = 500/10000 = 0.05; Recall@K = 320/400 = 0.80', false],
                ],
            ],

            // ── 21.8 AUTOENCODERS FOR UNSUPERVISED LEARNING ──────────────
            [
                'q' => "A production VAE generates synthetic patient records for data augmentation. The model shows posterior collapse — all latent dimensions produce the same standard normal output regardless of input. What causes posterior collapse and how is it fixed?\n\nA) Posterior collapse occurs when the decoder is too powerful and learns to ignore the latent code — the KL term drives the posterior to the prior without the decoder needing the latent signal. Fix: use KL annealing (gradually increase β from 0 to 1) or use a weaker/constrained decoder\nB) Posterior collapse means the encoder has too few layers\nC) Posterior collapse occurs when batch size is too small\nD) Posterior collapse is normal in VAEs trained on medical data",
                'opts' => [
                    ['A', true],
                    ['B', false],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "A convolutional autoencoder for image anomaly detection achieves low reconstruction error on both normal AND anomalous test images. The anomaly detection AUC-ROC is only 0.52 (near random). What is the most likely cause?\n\nA) The autoencoder has too few layers\nB) The autoencoder capacity (bottleneck size) is too large — it memorises individual images including anomalies, reconstructing them well. The bottleneck must be constrained enough to force learning only normal patterns\nC) The learning rate is too high\nD) MSE reconstruction loss is incompatible with convolutional layers",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "A VQ-VAE (Vector Quantised VAE) replaces the continuous latent space of a standard VAE with a discrete codebook. In a speech generation system, what advantage does the discrete latent representation provide?\n\nA) Faster inference due to integer arithmetic\nB) The discrete code naturally represents phonemes or speech units — the model learns a compressed, structured discrete vocabulary of speech patterns, making it more suitable for sequence modelling with autoregressive priors (like PixelCNN or Transformers over the codebook)\nC) VQ-VAE eliminates the need for a decoder\nD) Discrete codes prevent posterior collapse automatically in all cases",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],

            // ── 21.9 GAUSSIAN MIXTURE MODELS & SOFT CLUSTERING ───────────
            [
                'q' => "A GMM is used as a density estimator for anomaly detection. The log-likelihood of a new point x is:\n\nlog p(x) = log Σₖ πₖ · N(x | μₖ, Σₖ)\n\nA point has log p(x) = -142. Normal training samples have mean log p = -28 and std = 12. What is the z-score and should this point be flagged?\n\nz = (-142 - (-28)) / 12 = -114 / 12 = -9.5",
                'opts' => [
                    ['z = -9.5 — extremely unlikely under the normal distribution, far below any reasonable threshold (e.g. z < -3); this point should definitely be flagged as anomalous', true],
                    ['z = -9.5 — slightly below average; monitoring recommended but not flagged', false],
                    ['z = +9.5 — the point is highly normal', false],
                    ['z = -0.95 — borderline; requires human review', false],
                ],
            ],
            [
                'q' => "Dirichlet Process Mixture Models (DPMM) extend GMMs by:\n\nA) Automatically determining the number of components from data — the Dirichlet Process prior allows the model to 'grow' new components as needed, avoiding manual selection of K\nB) Using the Dirichlet distribution for feature encoding\nC) Running EM with a Dirichlet regularisation on the covariance matrices\nD) Constraining GMM to only use diagonal covariance matrices",
                'opts' => [
                    ['A', true],
                    ['B', false],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "A GMM is trained on customer LTV (Lifetime Value) data that is heavily right-skewed (most customers: LTV < $100; a few: LTV > $10,000). Standard GMM with 3 components fits poorly. What is the principled fix?\n\nA) Apply a log transformation to LTV before fitting the GMM, making the distribution more Gaussian-like\nB) Increase the number of components to 30\nC) Use full covariance to capture the skew\nD) Normalise LTV to [0,1] with MinMaxScaler",
                'opts' => [
                    ['A', true],
                    ['B', false],
                    ['C', false],
                    ['D', false],
                ],
            ],

            // ── 21.10 END-TO-END UNSUPERVISED ML PIPELINE ────────────────
            [
                'q' => "A team builds a full unsupervised ML pipeline using sklearn.pipeline.Pipeline. They want to perform GridSearchCV to tune PCA n_components and KMeans n_clusters simultaneously, but there is no y (labels) to score against. What is the correct approach?\n\nA) GridSearchCV cannot be used without labels — try all combinations manually\nB) Define a custom scorer using make_scorer with a label-free metric (e.g. negative Davies-Bouldin score or Silhouette Score) and pass it to GridSearchCV with a dummy y=None\nC) Use accuracy_score with cluster labels as ground truth\nD) Grid search only PCA — K-Means does not have tunable parameters",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "A real-time product recommendation system uses online K-Means (Mini-Batch K-Means) to cluster active sessions into interest segments. A new session arrives every 50ms. What makes MiniBatchKMeans better than standard KMeans for this use case, and what monitoring is essential?\n\nA) MiniBatchKMeans updates centroids incrementally from mini-batches without refitting on all data — O(1) per update vs O(n). Monitor centroid drift (distance of current centroids from baseline) to detect when a full retraining is needed\nB) MiniBatchKMeans requires no hyperparameter tuning\nC) MiniBatchKMeans automatically selects K based on session volume\nD) Standard KMeans is better because it guarantees lower inertia",
                'opts' => [
                    ['A', true],
                    ['B', false],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "A data platform team needs to ensure their unsupervised ML pipeline is reproducible across environments (local, staging, production). Which combination of practices ensures full reproducibility?\n\nA) Fix random_state only in clustering algorithms\nB) Fix random_state in all stochastic steps (PCA via SVD solver, K-Means, UMAP, train/test split), pin all library versions (requirements.txt + docker image), serialise fitted transformers with version tags, and log all hyperparameters to an experiment tracker\nC) Use the same machine for all environments\nD) Log only the final Silhouette Score to compare runs",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "An e-commerce company segments customers into 8 clusters using K-Means on RFM features (Recency, Frequency, Monetary). The marketing team complains that 'Cluster 3' changes its composition dramatically after each monthly retraining — some months it is 'high-value loyalists', other months it is 'churned customers'. What system design change resolves this?\n\nA) Fix the random seed so clusters never change\nB) After each retraining, compute cluster centroids and characterise each cluster by its dominant RFM profile, then match new cluster IDs to historical profiles by centroid similarity (Hungarian matching) and rename using stable semantic labels (e.g. 'VIP', 'Churned') based on thresholds rather than cluster index\nC) Reduce to K=4 clusters for stability\nD) Add cluster ID as a feature in the next month's training",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "A senior ML engineer audits an anomaly detection system and finds:\n• Isolation Forest trained on 2019–2020 data\n• No retraining since deployment\n• Precision dropped from 0.81 to 0.34 over 18 months\n• The fraud team manually reviews 3,000 flagged cases/day but true fraud rate is ~0.1%\n\nWhich combination of fixes addresses the root causes?\n\nA) Retrain with contamination=0.34 to match the new false positive rate\nB) Implement: (1) quarterly retraining on rolling 12-month window, (2) a feedback loop incorporating analyst decisions as labels for a semi-supervised layer, (3) a precision monitoring dashboard triggering retraining when precision drops below 0.65, (4) add temporal features to capture drift\nC) Switch to a supervised fraud classifier with the analyst labels\nD) Increase n_estimators from 100 to 500",
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

        $this->command->info("✅ Done! 50 questions seeded for Module 21 — Unsupervised Learning (Professional).");
    }
}