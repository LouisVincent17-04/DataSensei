<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module21ChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (!$category) {
            $this->command->error("University Student category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Remove existing challenges for this category (cascades to questions/options)
        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 21 — Machine Learning 2: Unsupervised Learning (University Student)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Machine Learning 2: Unsupervised Learning',
            'description'           => 'Sharpen your analytical understanding of unsupervised learning — compare algorithms, trace through steps, interpret metrics, and evaluate design decisions across clustering, dimensionality reduction, and anomaly detection.',
            'time_limit_seconds'    => 1200,
            'base_xp'               => 1000,
            'order_index'           => 21,
        ]);

        $this->command->info("Seeding 50 university-level questions on Unsupervised Learning...");

        $qaData = [

            // ── 21.1 WHAT IS UNSUPERVISED LEARNING? ──────────────────────
            [
                'q' => 'A retailer has a dataset of purchase histories with no category labels. They want to group customers with similar buying patterns. Which learning paradigm applies?',
                'opts' => [
                    ['Supervised learning — because the data is labelled by purchase amount', false],
                    ['Reinforcement learning — because the model earns rewards for correct groupings', false],
                    ['Unsupervised learning — because no ground-truth group labels exist', true],
                    ['Semi-supervised learning — because some customers have loyalty cards', false],
                ],
            ],
            [
                'q' => 'Which two broad categories of tasks does unsupervised learning mainly cover?',
                'opts' => [
                    ['Classification and regression', false],
                    ['Clustering and dimensionality reduction', true],
                    ['Prediction and translation', false],
                    ['Reinforcement and imitation', false],
                ],
            ],
            [
                'q' => 'A data scientist applies unsupervised learning to a gene expression dataset to find groups of genes with similar activation patterns. This task is best described as:',
                'opts' => [
                    ['Regression — predicting gene expression levels', false],
                    ['Clustering — discovering natural groupings in the data without predefined labels', true],
                    ['Classification — assigning each gene to a known pathway', false],
                    ['Imputation — filling in missing gene expression values', false],
                ],
            ],
            [
                'q' => 'Why is evaluation harder in unsupervised learning compared to supervised learning?',
                'opts' => [
                    ['Unsupervised algorithms are too complex to measure', false],
                    ['There are no ground-truth labels to compare predictions against — success is often subjective or domain-dependent', true],
                    ['Unsupervised models always produce random outputs', false],
                    ['Computers cannot compute distances without labels', false],
                ],
            ],
            [
                'q' => 'Semi-supervised learning combines labelled and unlabelled data. If only 1% of a dataset is labelled, why might an unsupervised pre-training step improve a subsequent supervised model?',
                'opts' => [
                    ['Unsupervised pre-training creates synthetic labels for all unlabelled data', false],
                    ['Unsupervised pre-training learns useful feature representations from all the data before fine-tuning on the few labelled examples', true],
                    ['Unsupervised pre-training removes noisy labelled examples', false],
                    ['Pre-training increases the dataset size by duplicating labelled rows', false],
                ],
            ],

            // ── 21.2 K-MEANS CLUSTERING ───────────────────────────────────
            [
                'q' => 'K-Means uses Euclidean distance to assign points to centroids. If a dataset has features on very different scales (e.g., age: 20–60, salary: 20,000–200,000), what problem arises?',
                'opts' => [
                    ['K-Means will ignore the age feature entirely', false],
                    ['Salary will dominate the distance calculation, causing age to have almost no effect on clustering', true],
                    ['K-Means will fail to converge at all', false],
                    ['Salary values will be automatically normalised by K-Means', false],
                ],
            ],
            [
                'q' => 'K-Means is guaranteed to converge, but it may converge to a local minimum rather than the global minimum. What causes this?',
                'opts' => [
                    ['K-Means stops after a fixed number of iterations regardless of convergence', false],
                    ['The random initialisation of centroids can lead to different final clusterings — some may be poor', true],
                    ['K-Means always finds the globally optimal solution', false],
                    ['Local minima occur only when K > 10', false],
                ],
            ],
            [
                'q' => 'What does the K-Means objective function (inertia) measure?',
                'opts' => [
                    ['The average distance between cluster centroids', false],
                    ['The sum of squared distances from each point to its assigned centroid', true],
                    ['The number of points in the largest cluster', false],
                    ['The total number of iterations until convergence', false],
                ],
            ],
            [
                'q' => 'You run K-Means with K=3 twice with different random seeds and get different cluster assignments. The best run to keep is the one with:',
                'opts' => [
                    ['The most iterations before convergence', false],
                    ['The lowest inertia (sum of squared distances to centroids)', true],
                    ['The highest inertia', false],
                    ['The run that finished fastest', false],
                ],
            ],
            [
                'q' => 'The Elbow Method plots inertia against K. At K=4 the inertia drops sharply; at K=5 the drop is much smaller. What does this suggest?',
                'opts' => [
                    ['K=5 is always better because lower inertia is better', false],
                    ['K=4 is likely the optimal number of clusters — adding more clusters beyond 4 gives diminishing returns', true],
                    ['The data has no natural clusters', false],
                    ['K should be set to the square root of n', false],
                ],
            ],

            // ── 21.3 DBSCAN ───────────────────────────────────────────────
            [
                'q' => 'DBSCAN has two key hyperparameters: epsilon (ε) and min_samples. What does epsilon control?',
                'opts' => [
                    ['The number of clusters to find', false],
                    ['The radius of the neighbourhood around a point — how close two points must be to be considered neighbours', true],
                    ['The minimum size of the entire dataset', false],
                    ['The maximum number of iterations', false],
                ],
            ],
            [
                'q' => 'What is a "core point" in DBSCAN?',
                'opts' => [
                    ['The centroid of a cluster', false],
                    ['A point that has at least min_samples neighbours within radius ε', true],
                    ['The first point assigned to a cluster', false],
                    ['A point that is equidistant from two cluster centres', false],
                ],
            ],
            [
                'q' => 'If you set ε very large in DBSCAN, what is likely to happen?',
                'opts' => [
                    ['Every point becomes a noise point', false],
                    ['All points end up in a single cluster because every point is within ε of every other', true],
                    ['DBSCAN creates more clusters with smaller ε', false],
                    ['The algorithm fails to run', false],
                ],
            ],
            [
                'q' => 'A dataset contains two ring-shaped clusters. K-Means with K=2 assigns points randomly across both rings. Why does DBSCAN succeed where K-Means fails here?',
                'opts' => [
                    ['DBSCAN uses Euclidean distance more accurately than K-Means', false],
                    ['DBSCAN finds dense regions of points, capturing arbitrary shapes; K-Means assumes convex, sphere-like clusters defined by centroids', true],
                    ['DBSCAN always outperforms K-Means', false],
                    ['K-Means cannot handle datasets with more than 100 points', false],
                ],
            ],

            // ── 21.4 HIERARCHICAL CLUSTERING & DENDROGRAMS ────────────────
            [
                'q' => 'In agglomerative hierarchical clustering, what is the first merge operation?',
                'opts' => [
                    ['Merge all clusters into one immediately', false],
                    ['Merge the two clusters (or individual points) that are closest to each other', true],
                    ['Merge the two largest clusters', false],
                    ['Merge a randomly selected pair of clusters', false],
                ],
            ],
            [
                'q' => 'What does the height of a merge in a dendrogram represent?',
                'opts' => [
                    ['The number of data points in the merged cluster', false],
                    ['The distance (dissimilarity) between the two clusters at the point they were merged', true],
                    ['The accuracy of the clustering at that step', false],
                    ['The number of remaining clusters after the merge', false],
                ],
            ],
            [
                'q' => 'How do you choose the number of clusters from a dendrogram?',
                'opts' => [
                    ['Always cut at height = 1.0', false],
                    ['Cut the dendrogram horizontally at a chosen height — the number of vertical lines crossed is the number of clusters', true],
                    ['The dendrogram automatically marks the best cut point', false],
                    ['Cut at the bottom of the dendrogram for the most clusters', false],
                ],
            ],
            [
                'q' => 'What is "linkage" in hierarchical clustering?',
                'opts' => [
                    ['A method of connecting the internet to a clustering server', false],
                    ['The criterion used to define the distance between two clusters when deciding which to merge next', true],
                    ['The name for the final output cluster assignment', false],
                    ['The number of features used in the distance metric', false],
                ],
            ],

            // ── 21.5 PRINCIPAL COMPONENT ANALYSIS (PCA) ──────────────────
            [
                'q' => 'PCA requires data to be centred (mean subtracted) before computing components. Why?',
                'opts' => [
                    ['Centring makes all features equal to zero', false],
                    ['Centring ensures PCA finds directions of variance around the mean, not directions influenced by the overall offset of the data', true],
                    ['Centring speeds up the computation', false],
                    ['Centring is optional and does not affect PCA results', false],
                ],
            ],
            [
                'q' => 'A dataset has 50 features. After PCA, you keep the top 5 principal components that explain 90% of the variance. What does this mean?',
                'opts' => [
                    ['The original 50 features are discarded and can never be recovered', false],
                    ['90% of the information (variance) in the data is captured by just 5 new components, reducing the data from 50D to 5D', true],
                    ['The model will be 90% accurate with only 5 features', false],
                    ['Only 5 of the original 50 features are selected and retained', false],
                ],
            ],
            [
                'q' => 'What is "explained variance ratio" in PCA?',
                'opts' => [
                    ['The ratio of training samples to features', false],
                    ['The fraction of the total variance in the data captured by each principal component', true],
                    ['The ratio of noise to signal in the dataset', false],
                    ['The correlation between the first and last principal component', false],
                ],
            ],
            [
                'q' => 'PCA is a linear dimensionality reduction method. This means it:',
                'opts' => [
                    ['Can only be applied to datasets stored in lists', false],
                    ['Finds new axes that are linear combinations of the original features — it cannot capture curved or non-linear structure', true],
                    ['Always reduces data to exactly 1 dimension', false],
                    ['Works by training a linear regression model on the features', false],
                ],
            ],
            [
                'q' => 'Before applying PCA to a mixed-scale dataset (e.g. age, income, years of experience), what preprocessing step is essential?',
                'opts' => [
                    ['One-hot encode all features', false],
                    ['Standardise features to zero mean and unit variance — otherwise high-scale features dominate the principal components', true],
                    ['Remove all features with variance below 0.5', false],
                    ['Sort the dataset by the first feature', false],
                ],
            ],

            // ── 21.6 t-SNE & UMAP ─────────────────────────────────────────
            [
                'q' => 't-SNE plots are excellent for visualisation but are NOT suitable for:',
                'opts' => [
                    ['Showing cluster separation in image embeddings', false],
                    ['Transforming new, unseen data points into the same low-dimensional space (it cannot generalise to new points)', true],
                    ['Revealing local neighbourhood structure in data', false],
                    ['Exploring high-dimensional NLP embeddings', false],
                ],
            ],
            [
                'q' => 'The "perplexity" hyperparameter in t-SNE roughly controls:',
                'opts' => [
                    ['The number of output dimensions', false],
                    ['The balance between focusing on local neighbourhood structure vs. global structure — typically set between 5 and 50', true],
                    ['The learning rate of the gradient descent optimisation', false],
                    ['The number of iterations before the algorithm stops', false],
                ],
            ],
            [
                'q' => 'UMAP is generally preferred over t-SNE in production because:',
                'opts' => [
                    ['UMAP always produces more visually appealing plots', false],
                    ['UMAP is significantly faster, preserves more global structure, and supports transforming new data points after fitting', true],
                    ['UMAP requires no hyperparameter tuning', false],
                    ['UMAP works only on tabular data, not images', false],
                ],
            ],
            [
                'q' => 'In a t-SNE plot of word embeddings, two words appear very close together. This suggests:',
                'opts' => [
                    ['The words have the same spelling', false],
                    ['The words have similar high-dimensional embeddings — they are used in similar contexts', true],
                    ['The words are antonyms', false],
                    ['The words appear with the same frequency in the corpus', false],
                ],
            ],

            // ── 21.7 ANOMALY DETECTION: ISOLATION FOREST ─────────────────
            [
                'q' => 'In an Isolation Forest, the anomaly score is based on the average path length to isolate a point. Anomalies have:',
                'opts' => [
                    ['Longer average path lengths — they are harder to isolate', false],
                    ['Shorter average path lengths — they are easier to isolate because they are rare and different', true],
                    ['Path lengths equal to the tree depth', false],
                    ['Path lengths of exactly 1', false],
                ],
            ],
            [
                'q' => 'Isolation Forest requires no distance or density computation. Why is this an advantage for high-dimensional data?',
                'opts' => [
                    ['High-dimensional data has no outliers, so computation is trivial', false],
                    ['Distance-based methods suffer from the "curse of dimensionality" where all points become equidistant; Isolation Forest avoids this by using random partitioning', true],
                    ['Isolation Forest uses GPU acceleration by default', false],
                    ['High-dimensional data always has shorter path lengths', false],
                ],
            ],
            [
                'q' => 'The `contamination` parameter in Isolation Forest controls:',
                'opts' => [
                    ['How much noise is added to the training data', false],
                    ['The expected proportion of anomalies in the dataset — used to set the decision threshold', true],
                    ['The depth of each tree in the forest', false],
                    ['The number of trees in the ensemble', false],
                ],
            ],
            [
                'q' => 'Which scenario is LEAST suitable for Isolation Forest?',
                'opts' => [
                    ['Detecting network intrusions in server logs', false],
                    ['Identifying manufacturing defects in sensor readings', false],
                    ['Classifying emails into 20 topic categories (a known multi-class problem)', true],
                    ['Flagging unusual patient vitals in a hospital system', false],
                ],
            ],

            // ── 21.8 AUTOENCODERS FOR UNSUPERVISED LEARNING ──────────────
            [
                'q' => 'Why is an autoencoder considered an unsupervised learning method, even though it uses a neural network (typically associated with supervised learning)?',
                'opts' => [
                    ['Because autoencoders do not use backpropagation', false],
                    ['Because the autoencoder uses the INPUT as both the input AND the target output — no external labels are needed', true],
                    ['Because autoencoders are only trained on unlabelled images', false],
                    ['Because autoencoders do not have a loss function', false],
                ],
            ],
            [
                'q' => 'A Variational Autoencoder (VAE) differs from a standard autoencoder because:',
                'opts' => [
                    ['A VAE has only an encoder, no decoder', false],
                    ['A VAE encodes inputs as probability distributions (mean and variance) rather than single points, enabling it to generate new data by sampling', true],
                    ['A VAE uses supervised loss to train the decoder', false],
                    ['A VAE uses convolutional layers only', false],
                ],
            ],
            [
                'q' => 'An autoencoder is trained on normal manufacturing sensor data. At inference time, a defective product passes through the encoder-decoder. Its reconstruction error is very high. What does this indicate?',
                'opts' => [
                    ['The autoencoder is underfitting the normal data', false],
                    ['The defective product is anomalous — the autoencoder was never trained to reconstruct such patterns, so reconstruction fails', true],
                    ['The autoencoder has memorised all training examples', false],
                    ['The model needs more hidden layers', false],
                ],
            ],
            [
                'q' => 'What is the relationship between PCA and a linear autoencoder (with linear activations, one hidden layer)?',
                'opts' => [
                    ['They produce identical results only when K=2', false],
                    ['A linear autoencoder learns the same subspace as PCA — both find the directions of maximum variance, though PCA does so analytically', true],
                    ['PCA is always superior to a linear autoencoder', false],
                    ['There is no relationship between the two methods', false],
                ],
            ],

            // ── 21.9 GAUSSIAN MIXTURE MODELS & SOFT CLUSTERING ───────────
            [
                'q' => 'GMMs are trained using the Expectation-Maximisation (EM) algorithm. What does the E-step compute?',
                'opts' => [
                    ['The covariance matrices of each Gaussian component', false],
                    ['The probability (responsibility) that each data point was generated by each Gaussian component', true],
                    ['The number of Gaussian components to use', false],
                    ['The gradient of the log-likelihood', false],
                ],
            ],
            [
                'q' => 'What does the M-step of the EM algorithm update in a GMM?',
                'opts' => [
                    ['The cluster assignments for each data point', false],
                    ['The parameters of each Gaussian component (means, covariances, mixture weights) to maximise the expected log-likelihood', true],
                    ['The number of components in the model', false],
                    ['The reconstruction loss of the model', false],
                ],
            ],
            [
                'q' => 'A GMM with full covariance matrices can model which cluster shapes?',
                'opts' => [
                    ['Only spherical clusters (same variance in all directions)', false],
                    ['Any elliptical shape — including elongated, rotated ellipsoids', true],
                    ['Only square-shaped clusters', false],
                    ['Only clusters aligned with the coordinate axes', false],
                ],
            ],
            [
                'q' => 'BIC (Bayesian Information Criterion) is used with GMMs to select the number of components. You prefer the model with:',
                'opts' => [
                    ['The highest BIC — more components are always better', false],
                    ['The lowest BIC — it balances model fit with complexity, penalising extra components', true],
                    ['The BIC closest to zero', false],
                    ['The BIC that equals the number of data points', false],
                ],
            ],

            // ── 21.10 END-TO-END UNSUPERVISED ML PIPELINE ────────────────
            [
                'q' => 'In an end-to-end pipeline for clustering customer segments, why should PCA or UMAP be applied BEFORE K-Means rather than after?',
                'opts' => [
                    ['K-Means cannot handle more than 10 features', false],
                    ['Reducing dimensions before clustering removes noise and the curse of dimensionality, making distance calculations more meaningful', true],
                    ['PCA and UMAP require cluster labels to function correctly', false],
                    ['Applying dimensionality reduction after clustering reverses the cluster assignments', false],
                ],
            ],
            [
                'q' => 'The Silhouette Score for a clustering result is 0.02. What does this indicate?',
                'opts' => [
                    ['The clustering is perfect — all points are well assigned', false],
                    ['The clusters are barely meaningful — points are nearly as close to neighbouring clusters as to their own', true],
                    ['The model has overfit and needs regularisation', false],
                    ['Only 2% of data points are correctly clustered', false],
                ],
            ],
            [
                'q' => 'A pipeline first standardises data, then applies PCA (keeping 95% variance), then runs K-Means. A new batch of raw data arrives. In what order must the transformations be applied?',
                'opts' => [
                    ['K-Means prediction → PCA transform → standardise', false],
                    ['Standardise → PCA transform → K-Means predict', true],
                    ['PCA transform → standardise → K-Means predict', false],
                    ['K-Means predict → standardise → PCA transform', false],
                ],
            ],
            [
                'q' => 'A data scientist discovers that after clustering, one cluster contains 95% of all data points while others are tiny. This most likely indicates:',
                'opts' => [
                    ['K-Means found the optimal solution', false],
                    ['K is too small, poor initialisation, or the data has one dominant dense region — try different K, scaling, or DBSCAN', true],
                    ['The Silhouette Score will be close to +1', false],
                    ['The data has no outliers', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 21 — Unsupervised Learning (University Student).");
    }
}