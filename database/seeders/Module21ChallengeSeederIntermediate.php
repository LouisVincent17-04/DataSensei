<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module21ChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (!$category) {
            $this->command->error("Intermediate category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Remove existing challenges for this category (cascades to questions/options)
        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 21 — Machine Learning 2: Unsupervised Learning (Intermediate)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Machine Learning 2: Unsupervised Learning',
            'description'           => 'Apply unsupervised learning through multi-step reasoning, code tracing, metric calculations, and algorithm design decisions. Covers real scikit-learn usage, mathematical intuition, and practical debugging scenarios.',
            'time_limit_seconds'    => 1500,
            'base_xp'               => 1500,
            'order_index'           => 21,
        ]);

        $this->command->info("Seeding 50 intermediate-level questions on Unsupervised Learning...");

        $qaData = [

            // ── 21.1 WHAT IS UNSUPERVISED LEARNING? ──────────────────────
            [
                'q' => "A data scientist has a dataset of 50,000 news articles with no topic labels. She applies LDA (Latent Dirichlet Allocation) and discovers 8 coherent topics. Which statement best describes this workflow?\n\nA) Supervised — she trains LDA using 8 known topic labels\nB) Unsupervised — LDA discovers topic structure without any predefined labels\nC) Semi-supervised — she manually labels 100 articles to guide the model\nD) Reinforcement — the model receives a reward for each coherent topic found",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "Identify the unsupervised learning tasks among the following:\n\n1. Predicting churn probability for each customer\n2. Grouping products by purchase co-occurrence\n3. Compressing 512-dimensional face embeddings to 2D for visualisation\n4. Detecting unusual server request patterns\n5. Classifying sentiment as positive or negative",
                'opts' => [
                    ['1, 4, 5', false],
                    ['2, 3, 4', true],
                    ['1, 2, 3', false],
                    ['3, 4, 5', false],
                ],
            ],
            [
                'q' => "The reconstruction loss of an autoencoder trained on normal data is:\n• Normal samples: mean loss = 0.02\n• Anomalous samples: mean loss = 0.89\n\nA threshold of 0.50 is set. What precision and recall trade-off does lowering the threshold to 0.30 create?",
                'opts' => [
                    ['Higher precision, lower recall — fewer anomalies are flagged, but those flagged are more likely true anomalies', true],
                    ['Lower precision, higher recall — more anomalies are flagged, including more false positives', false],
                    ['Both precision and recall increase simultaneously', false],
                    ['The threshold has no effect on precision or recall in unsupervised settings', false],
                ],
            ],

            // ── 21.2 K-MEANS CLUSTERING ───────────────────────────────────
            [
                'q' => "Trace through K-Means (K=2) on these 1D points: [1, 2, 8, 9, 10]\nInitial centroids: c1=1, c2=9\n\nAfter Step 1 (assign points to nearest centroid), which points go to c1?",
                'opts' => [
                    ['[1, 2, 8]', false],
                    ['[1, 2]', true],
                    ['[1]', false],
                    ['[1, 2, 8, 9, 10]', false],
                ],
            ],
            [
                'q' => "Continuing the trace: After Step 1, c1 has points [1, 2] and c2 has [8, 9, 10].\nWhat are the new centroids after Step 2 (recompute)?",
                'opts' => [
                    ['c1=1.5, c2=9.0', true],
                    ['c1=2.0, c2=8.0', false],
                    ['c1=1.0, c2=10.0', false],
                    ['c1=3.0, c2=9.0', false],
                ],
            ],
            [
                'q' => "K-Means++ improves upon random centroid initialisation by:\n\nA) Trying all possible K starting points and picking the best\nB) Selecting centroids sequentially, with each new centroid chosen with probability proportional to its squared distance from the nearest existing centroid\nC) Using PCA to find the K principal component directions as initial centroids\nD) Starting with the K points that have the highest variance",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "You run K-Means with K values from 1 to 10 and record inertia:\nK=1: 9800, K=2: 4100, K=3: 1500, K=4: 1450, K=5: 1420\n\nBased on the Elbow Method, what is the optimal K and why?",
                'opts' => [
                    ['K=1 — lowest computational cost', false],
                    ['K=3 — the largest drop in inertia occurs between K=2 and K=3; beyond K=3 the decrease is minimal', true],
                    ['K=5 — the lowest inertia always wins', false],
                    ['K=2 — the first split always captures the most structure', false],
                ],
            ],
            [
                'q' => "What does the following scikit-learn code produce?\n\nfrom sklearn.cluster import KMeans\nkm = KMeans(n_clusters=3, random_state=42)\nkm.fit(X)\nprint(km.inertia_)",
                'opts' => [
                    ['The Silhouette Score of the clustering', false],
                    ['The sum of squared distances from each point to its assigned cluster centroid', true],
                    ['The number of iterations until convergence', false],
                    ['The explained variance ratio of the clusters', false],
                ],
            ],

            // ── 21.3 DBSCAN ───────────────────────────────────────────────
            [
                'q' => "Given points in 2D space and DBSCAN with ε=1.5, min_samples=3:\n• Point A has 4 neighbours within ε → core point\n• Point B has 1 neighbour within ε → border point of A's cluster\n• Point C has 0 neighbours within ε → ?\n\nWhat is Point C classified as?",
                'opts' => [
                    ['A core point', false],
                    ['A border point of the nearest cluster', false],
                    ['A noise point (outlier)', true],
                    ['A centroid', false],
                ],
            ],
            [
                'q' => "DBSCAN is applied to a dataset of GPS coordinates with ε=500 metres, min_samples=10. What does this configuration find?",
                'opts' => [
                    ['Exactly 10 clusters regardless of data distribution', false],
                    ['Dense urban areas (at least 10 GPS pings within 500m of each other) as clusters, with sparse rural pings marked as noise', true],
                    ['The 10 most-visited locations only', false],
                    ['A grid of 500×500m squares with at least 10 points', false],
                ],
            ],
            [
                'q' => "What does the following code output given the described dataset (two blobs + 5 outliers)?\n\nfrom sklearn.cluster import DBSCAN\nimport numpy as np\ndb = DBSCAN(eps=0.5, min_samples=5)\nlabels = db.fit_predict(X)\nprint(np.unique(labels))",
                'opts' => [
                    ['[0, 1] — two clusters, no noise', false],
                    ['[-1, 0, 1] — noise label (-1) plus two cluster labels', true],
                    ['[1, 2] — DBSCAN labels start at 1', false],
                    ['[0] — all points in one cluster', false],
                ],
            ],

            // ── 21.4 HIERARCHICAL CLUSTERING & DENDROGRAMS ────────────────
            [
                'q' => "Ward linkage in hierarchical clustering merges clusters to minimise:\n\nA) The maximum distance between any two points in the merged cluster\nB) The increase in total within-cluster variance after merging\nC) The average distance between all pairs across the merged cluster\nD) The distance between the two closest points across the merged cluster",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "The following scipy code runs hierarchical clustering:\n\nfrom scipy.cluster.hierarchy import linkage, fcluster\nZ = linkage(X, method='ward')\nlabels = fcluster(Z, t=3, criterion='maxclust')\n\nWhat does `t=3` and `criterion='maxclust'` specify?",
                'opts' => [
                    ['Cut the dendrogram at height 3', false],
                    ['Form exactly 3 clusters by cutting the dendrogram at the appropriate height', true],
                    ['Run 3 iterations of the merging algorithm', false],
                    ['Require a minimum of 3 points per cluster', false],
                ],
            ],
            [
                'q' => "You have a dendrogram where the longest vertical line (before any merge) has height 8.5. You cut at height 5.0 and get 4 clusters. If you cut at height 2.0 instead, you get 7 clusters. What general rule does this illustrate?",
                'opts' => [
                    ['Higher cuts always produce more clusters', false],
                    ['Lower cuts produce more clusters — you interrupt merging earlier, keeping groups that would otherwise be combined', true],
                    ['The cut height must equal the number of data points', false],
                    ['The number of clusters is always the square root of the cut height', false],
                ],
            ],

            // ── 21.5 PRINCIPAL COMPONENT ANALYSIS (PCA) ──────────────────
            [
                'q' => "A dataset with 4 features has the following explained variance ratios after PCA:\nPC1: 0.62, PC2: 0.21, PC3: 0.12, PC4: 0.05\n\nHow many components should you keep to explain at least 90% of the variance?",
                'opts' => [
                    ['1 component (PC1 alone: 62%)', false],
                    ['2 components (PC1+PC2: 62+21=83%)', false],
                    ['3 components (PC1+PC2+PC3: 62+21+12=95%)', true],
                    ['4 components — always keep all', false],
                ],
            ],
            [
                'q' => "What does the following scikit-learn code do?\n\nfrom sklearn.decomposition import PCA\npca = PCA(n_components=0.95)\nX_reduced = pca.fit_transform(X_scaled)\nprint(X_reduced.shape)",
                'opts' => [
                    ['Reduces X to exactly 95 components', false],
                    ['Reduces X to the minimum number of components needed to explain 95% of variance, then transforms X', true],
                    ['Reduces X to 5% of its original features', false],
                    ['Raises an error — n_components must be an integer', false],
                ],
            ],
            [
                'q' => "The covariance matrix of a 3-feature standardised dataset is:\n[[1.0, 0.8, 0.1],\n [0.8, 1.0, 0.2],\n [0.1, 0.2, 1.0]]\n\nWhich pair of features contributes most to the first principal component?",
                'opts' => [
                    ['Feature 1 and Feature 3 (covariance 0.1)', false],
                    ['Feature 2 and Feature 3 (covariance 0.2)', false],
                    ['Feature 1 and Feature 2 (covariance 0.8 — highest covariance)', true],
                    ['All three contribute equally', false],
                ],
            ],
            [
                'q' => "After applying PCA to reduce 100 features to 10 components, a data scientist tries to run PCA again on the 10 components. What happens?",
                'opts' => [
                    ['PCA further reduces the 10 components to 1', false],
                    ['The principal components are already orthogonal and uncorrelated — a second PCA produces nearly identical components with no further reduction in dimensionality', true],
                    ['PCA doubles the components to 20', false],
                    ['PCA cannot be applied to its own output', false],
                ],
            ],

            // ── 21.6 t-SNE & UMAP ─────────────────────────────────────────
            [
                'q' => "A researcher applies t-SNE to a 512-dimensional image embedding dataset. The resulting 2D plot shows 5 tight clusters. She trains a K-Means model directly on the t-SNE output and gets excellent cluster separation. What potential issue exists with this approach?",
                'opts' => [
                    ['K-Means cannot be applied after t-SNE', false],
                    ['t-SNE coordinates are not stable across runs and do not generalise to new data — clustering on t-SNE output cannot be applied to new embeddings', true],
                    ['t-SNE outputs are not Euclidean, so distance-based clustering fails', false],
                    ['K-Means with 5 clusters always fails on 2D data', false],
                ],
            ],
            [
                'q' => "What is the effect of increasing the `n_neighbors` parameter in UMAP?",
                'opts' => [
                    ['UMAP focuses more on local structure, creating tighter, more separated clusters', false],
                    ['UMAP considers a wider neighbourhood per point, preserving more global structure at the cost of local detail', true],
                    ['Increasing n_neighbors always improves the visualisation quality', false],
                    ['UMAP runs faster with larger n_neighbors', false],
                ],
            ],
            [
                'q' => "The following code trains UMAP and transforms test data:\n\nimport umap\nreducer = umap.UMAP(n_components=2, random_state=42)\nX_train_2d = reducer.fit_transform(X_train)\nX_test_2d = reducer.transform(X_test)\n\nWhy is `fit_transform` used on train data and `transform` on test data (not `fit_transform` on both)?",
                'opts' => [
                    ['fit_transform is slower and should only be used when necessary', false],
                    ['Using fit_transform on test data would learn a new embedding, making train and test incomparable — transform applies the learned mapping consistently', true],
                    ['transform produces better quality embeddings for test data', false],
                    ['fit_transform cannot be called twice on the same object', false],
                ],
            ],

            // ── 21.7 ANOMALY DETECTION: ISOLATION FOREST ─────────────────
            [
                'q' => "The following code trains an Isolation Forest and predicts anomalies:\n\nfrom sklearn.ensemble import IsolationForest\nclf = IsolationForest(n_estimators=100, contamination=0.05, random_state=42)\npreds = clf.fit_predict(X)\nprint((preds == -1).sum())\n\nIf X has 1000 samples, what does the output likely print?",
                'opts' => [
                    ['100 — 10% of samples are anomalies', false],
                    ['50 — 5% of 1000 samples are flagged as anomalies (label = -1)', true],
                    ['1 — only the most extreme outlier is flagged', false],
                    ['0 — no anomalies found by default', false],
                ],
            ],
            [
                'q' => "An Isolation Forest is trained on network traffic data and tested on data from a different network. The anomaly detection rate drops dramatically. What is the most likely cause?",
                'opts' => [
                    ['Isolation Forest cannot handle numerical features', false],
                    ['The model has overfit to the specific distribution of the training network — the decision boundaries do not generalise to the new network\'s traffic patterns', true],
                    ['The n_estimators parameter is too low', false],
                    ['The contamination rate should always be set to 0.5', false],
                ],
            ],
            [
                'q' => "Isolation Forest anomaly scores from `decision_function()` return:\n• Values near 0: ambiguous\n• Negative values (e.g. -0.3): anomalies\n• Positive values (e.g. +0.2): normal\n\nA point returns score = -0.05. What is the correct interpretation?",
                'opts' => [
                    ['Strongly anomalous — the negative score confirms it is an outlier', false],
                    ['Slightly more anomalous than average but borderline — close to the decision boundary; context-dependent', true],
                    ['Definitely normal — negative scores indicate normality in Isolation Forest', false],
                    ['The score is meaningless unless the contamination is set to 0.05', false],
                ],
            ],

            // ── 21.8 AUTOENCODERS FOR UNSUPERVISED LEARNING ──────────────
            [
                'q' => "The following autoencoder architecture is defined:\n\nEncoder: 784 → 256 → 64 → 16\nDecoder: 16 → 64 → 256 → 784\n\nThis is applied to MNIST images (28×28 = 784 pixels). What is the compression ratio?",
                'opts' => [
                    ['784/256 ≈ 3x', false],
                    ['784/16 = 49x compression', true],
                    ['784/64 ≈ 12x', false],
                    ['16/784 ≈ 2% (not a valid ratio)', false],
                ],
            ],
            [
                'q' => "An autoencoder is trained with Mean Squared Error (MSE) loss between the input and reconstruction. A denoising autoencoder differs because:\n\nA) It uses a different activation function\nB) Corrupted (noisy) inputs are fed to the encoder, but the target reconstruction is the CLEAN original — forcing the network to learn robust features\nC) The decoder has more layers than the encoder\nD) It uses cross-entropy loss instead of MSE",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "After training an autoencoder on customer transaction data, the latent space (bottleneck) vectors are extracted and used as input features for a downstream fraud classifier. What benefit does this provide?",
                'opts' => [
                    ['The latent vectors always have higher predictive power than raw features', false],
                    ['The autoencoder has learned a compressed, informative representation of the raw data — the latent features capture underlying patterns and may improve classifier performance on limited labelled data', true],
                    ['Latent vectors eliminate the need for any labelled data in the classifier', false],
                    ['The autoencoder removes all noise, guaranteeing perfect classifier accuracy', false],
                ],
            ],

            // ── 21.9 GAUSSIAN MIXTURE MODELS & SOFT CLUSTERING ───────────
            [
                'q' => "A GMM with 3 components is fit to data. For a given point x, the model outputs:\nP(cluster 1 | x) = 0.70, P(cluster 2 | x) = 0.25, P(cluster 3 | x) = 0.05\n\nIf hard cluster assignment is required, which cluster is assigned, and what information is lost?",
                'opts' => [
                    ['Cluster 3 — outlier assignment', false],
                    ['Cluster 1 — but the uncertainty information (0.25 probability of cluster 2) is discarded', true],
                    ['Cluster 2 — the second-highest probability is chosen for robustness', false],
                    ['None — GMMs always use soft assignments', false],
                ],
            ],
            [
                'q' => "GMM vs K-Means: A dataset of astronomical objects has two overlapping groups — young and old stars — that overlap significantly in colour-magnitude space. Which algorithm better handles this overlap?",
                'opts' => [
                    ['K-Means — because it assigns each star to exactly one group', false],
                    ['GMM — because it models the overlap probabilistically; a star near the boundary gets partial membership in both groups reflecting real uncertainty', true],
                    ['Both perform identically on overlapping clusters', false],
                    ['K-Means because it uses distance rather than probability', false],
                ],
            ],
            [
                'q' => "The log-likelihood of a GMM increases as you add more components. Why do you NOT always choose the model with the most components?",
                'opts' => [
                    ['More components always make EM slower to converge', false],
                    ['Adding too many components leads to overfitting — some components collapse onto individual points (degenerate solutions); model selection criteria like BIC penalise complexity', true],
                    ['GMM cannot have more components than data points', false],
                    ['Log-likelihood cannot increase with more components', false],
                ],
            ],

            // ── 21.10 END-TO-END UNSUPERVISED ML PIPELINE ────────────────
            [
                'q' => "An end-to-end pipeline for customer segmentation is:\n1. Load raw CRM data\n2. Impute missing values\n3. StandardScaler\n4. PCA(n_components=10)\n5. KMeans(n_clusters=5)\n\nA new customer's raw features arrive. Which scikit-learn object correctly applies ALL transformations in sequence?",
                'opts' => [
                    ['Apply each step manually in a for-loop', false],
                    ['sklearn.pipeline.Pipeline — wraps all steps and calls transform/predict in the correct order', true],
                    ['sklearn.compose.ColumnTransformer — for mixed column types only', false],
                    ['A single KMeans call handles all preprocessing internally', false],
                ],
            ],
            [
                'q' => "The Calinski-Harabasz Index (Variance Ratio Criterion) evaluates clustering quality. A HIGHER score indicates:",
                'opts' => [
                    ['More noise points detected', false],
                    ['Denser, better-separated clusters — high between-cluster variance relative to within-cluster variance', true],
                    ['Fewer clusters are needed', false],
                    ['The model is overfitting', false],
                ],
            ],
            [
                'q' => "After deploying a customer segmentation model, the data distribution shifts (new product launches change buying behaviour). Silhouette Score drops from 0.58 to 0.21. What is the correct response?",
                'opts' => [
                    ['Lower the Silhouette Score threshold to 0.20 so the model passes evaluation', false],
                    ['Retrain the clustering model on recent data, re-evaluate K, and re-deploy — models on drifting data must be periodically retrained', true],
                    ['The drop in Silhouette Score is expected and can be ignored', false],
                    ['Switch from K-Means to a supervised classifier', false],
                ],
            ],
            [
                'q' => "A pipeline using DBSCAN for anomaly detection in sensor data flags 0.1% of readings as noise. After a firmware update, 15% of readings are flagged. Without changing ε or min_samples, what is the most likely cause?",
                'opts' => [
                    ['DBSCAN randomly flags 15% of readings by default', false],
                    ['The firmware update changed the sensor data distribution — the density structure that DBSCAN learned no longer matches the new data', true],
                    ['DBSCAN accumulates flagged points across runs', false],
                    ['min_samples must have been accidentally changed', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 21 — Unsupervised Learning (Intermediate).");
    }
}