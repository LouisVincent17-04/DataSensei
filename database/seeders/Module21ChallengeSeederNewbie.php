<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module21ChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (!$category) {
            $this->command->error("Newbie category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Remove existing challenges for this category (cascades to questions/options)
        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 21 — Machine Learning 2: Unsupervised Learning (Newbie)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Machine Learning 2: Unsupervised Learning',
            'description'           => 'Test your basic understanding of unsupervised learning — what it is, why we use it, and the key algorithms behind clustering and dimensionality reduction. No machine learning experience required!',
            'time_limit_seconds'    => 900,
            'base_xp'               => 500,
            'order_index'           => 21,
        ]);

        $this->command->info("Seeding 50 newbie-friendly questions on Unsupervised Learning...");

        $qaData = [

            // ── 21.1 WHAT IS UNSUPERVISED LEARNING? ──────────────────────
            [
                'q' => 'What is unsupervised learning?',
                'opts' => [
                    ['A type of machine learning where the model is given correct answers to learn from', false],
                    ['A type of machine learning where the model finds patterns in data WITHOUT labelled examples', true],
                    ['A method of teaching humans how to program machines', false],
                    ['A technique that only works on images', false],
                ],
            ],
            [
                'q' => 'Which of the following is the KEY difference between supervised and unsupervised learning?',
                'opts' => [
                    ['Supervised learning uses computers; unsupervised learning uses humans', false],
                    ['Supervised learning has labelled training data; unsupervised learning does not', true],
                    ['Unsupervised learning is always more accurate than supervised learning', false],
                    ['Supervised learning can only classify images', false],
                ],
            ],
            [
                'q' => 'Which of the following is an example of an unsupervised learning task?',
                'opts' => [
                    ['Predicting whether an email is spam or not spam', false],
                    ['Identifying the price of a house given its features', false],
                    ['Grouping customers into segments based on purchasing behaviour', true],
                    ['Classifying photos as cats or dogs', false],
                ],
            ],
            [
                'q' => 'In unsupervised learning, what does "unlabelled data" mean?',
                'opts' => [
                    ['Data that has been corrupted and cannot be used', false],
                    ['Data without any pre-assigned categories or correct answers', true],
                    ['Data stored without a file name', false],
                    ['Data collected from unlicensed sources', false],
                ],
            ],
            [
                'q' => 'Which of the following is the most common goal of unsupervised learning?',
                'opts' => [
                    ['To memorise the training dataset exactly', false],
                    ['To discover hidden structure or patterns in data', true],
                    ['To label data for future supervised learning only', false],
                    ['To reduce the speed of a machine learning model', false],
                ],
            ],

            // ── 21.2 K-MEANS CLUSTERING ───────────────────────────────────
            [
                'q' => 'What does K-Means clustering do?',
                'opts' => [
                    ['It predicts a numerical value for each data point', false],
                    ['It groups data points into K clusters based on similarity', true],
                    ['It sorts data in alphabetical order', false],
                    ['It removes outliers from the dataset', false],
                ],
            ],
            [
                'q' => 'In K-Means clustering, what is a "centroid"?',
                'opts' => [
                    ['The most extreme point in a cluster', false],
                    ['The centre point of a cluster, calculated as the average of all points in that cluster', true],
                    ['The first data point assigned to a cluster', false],
                    ['A point that does not belong to any cluster', false],
                ],
            ],
            [
                'q' => 'What does the "K" in K-Means represent?',
                'opts' => [
                    ['The number of features in the dataset', false],
                    ['The number of clusters you want to create', true],
                    ['The number of data points', false],
                    ['The number of iterations the algorithm runs', false],
                ],
            ],
            [
                'q' => 'What is the first step of the K-Means algorithm?',
                'opts' => [
                    ['Calculate the distance between every pair of data points', false],
                    ['Sort the data from smallest to largest', false],
                    ['Randomly place K centroids in the data space', true],
                    ['Remove all duplicate data points', false],
                ],
            ],
            [
                'q' => 'After assigning each data point to its nearest centroid in K-Means, what is the next step?',
                'opts' => [
                    ['Stop the algorithm — one assignment is enough', false],
                    ['Recalculate each centroid as the average of all points assigned to it', true],
                    ['Delete the data points that are farthest from their centroid', false],
                    ['Increase K by 1 and start over', false],
                ],
            ],
            [
                'q' => 'K-Means clustering requires you to specify K BEFORE running the algorithm. What is the challenge with this?',
                'opts' => [
                    ['K must always equal the number of features', false],
                    ['You often do not know the true number of clusters in advance', true],
                    ['K-Means fails if K is greater than 2', false],
                    ['Setting K is always straightforward because data has obvious groups', false],
                ],
            ],

            // ── 21.3 DBSCAN ───────────────────────────────────────────────
            [
                'q' => 'What does DBSCAN stand for?',
                'opts' => [
                    ['Data-Based Scanning and Clustering Algorithm Network', false],
                    ['Density-Based Spatial Clustering of Applications with Noise', true],
                    ['Distance-Bound Segment Clustering and Normalisation', false],
                    ['Dynamic Binary Search Cluster Analysis Nodes', false],
                ],
            ],
            [
                'q' => 'DBSCAN groups data points into clusters based on:',
                'opts' => [
                    ['The alphabetical order of data labels', false],
                    ['The density of nearby points — regions with many close points form clusters', true],
                    ['The colour of data points on a chart', false],
                    ['The average value of all features', false],
                ],
            ],
            [
                'q' => 'What does DBSCAN call a data point that does not belong to any cluster?',
                'opts' => [
                    ['A centroid', false],
                    ['A leaf node', false],
                    ['A noise point (outlier)', true],
                    ['A core point', false],
                ],
            ],
            [
                'q' => 'What is a major advantage DBSCAN has over K-Means?',
                'opts' => [
                    ['DBSCAN is always faster than K-Means', false],
                    ['DBSCAN does not require you to specify the number of clusters in advance', true],
                    ['DBSCAN works only on image data', false],
                    ['DBSCAN always finds perfectly round clusters', false],
                ],
            ],
            [
                'q' => 'Which type of cluster shape can DBSCAN find that K-Means typically cannot?',
                'opts' => [
                    ['Only perfectly circular clusters', false],
                    ['Only square-shaped clusters', false],
                    ['Arbitrarily shaped clusters (e.g., crescent or ring shapes)', true],
                    ['Only clusters with exactly 10 points', false],
                ],
            ],

            // ── 21.4 HIERARCHICAL CLUSTERING & DENDROGRAMS ────────────────
            [
                'q' => 'What is hierarchical clustering?',
                'opts' => [
                    ['A method that sorts data in ascending order', false],
                    ['A clustering method that builds a tree of nested clusters by progressively merging or splitting groups', true],
                    ['A technique that requires exactly 5 clusters', false],
                    ['A type of neural network for image classification', false],
                ],
            ],
            [
                'q' => 'What is a dendrogram?',
                'opts' => [
                    ['A type of decision tree used in classification', false],
                    ['A tree diagram that shows how clusters are merged step by step in hierarchical clustering', true],
                    ['A plot of model accuracy over time', false],
                    ['A histogram of feature distributions', false],
                ],
            ],
            [
                'q' => 'In agglomerative (bottom-up) hierarchical clustering, how does the algorithm start?',
                'opts' => [
                    ['With all data points in a single large cluster', false],
                    ['With each data point as its own individual cluster', true],
                    ['With K randomly selected centroids', false],
                    ['By sorting all data points by their distance from the origin', false],
                ],
            ],
            [
                'q' => 'What is one advantage of hierarchical clustering over K-Means?',
                'opts' => [
                    ['It is always faster', false],
                    ['You do not need to specify the number of clusters before running it', true],
                    ['It can only be used for text data', false],
                    ['It requires less memory than K-Means', false],
                ],
            ],

            // ── 21.5 PRINCIPAL COMPONENT ANALYSIS (PCA) ──────────────────
            [
                'q' => 'What is the main purpose of Principal Component Analysis (PCA)?',
                'opts' => [
                    ['To classify data points into categories', false],
                    ['To reduce the number of features (dimensions) in a dataset while keeping as much information as possible', true],
                    ['To increase the number of data points by generating synthetic samples', false],
                    ['To find the best split point in a decision tree', false],
                ],
            ],
            [
                'q' => 'What is a "principal component" in PCA?',
                'opts' => [
                    ['The most important feature in the original dataset', false],
                    ['A new axis (direction) that captures the most variance in the data', true],
                    ['The average value of all features', false],
                    ['A cluster centroid', false],
                ],
            ],
            [
                'q' => 'PCA is described as a "dimensionality reduction" technique. What does "reducing dimensions" mean?',
                'opts' => [
                    ['Making the computer screen smaller', false],
                    ['Decreasing the number of features used to represent the data', true],
                    ['Reducing the number of rows in a dataset', false],
                    ['Converting 3D data into 4D data', false],
                ],
            ],
            [
                'q' => 'Which of the following is a common use of PCA in data science?',
                'opts' => [
                    ['Adding new features to improve model accuracy', false],
                    ['Visualising high-dimensional data in 2D or 3D', true],
                    ['Labelling unlabelled data automatically', false],
                    ['Increasing the size of the training dataset', false],
                ],
            ],
            [
                'q' => 'PCA finds new axes called principal components. The FIRST principal component captures:',
                'opts' => [
                    ['The least amount of variance in the data', false],
                    ['The greatest amount of variance in the data', true],
                    ['The average of all features', false],
                    ['The feature with the highest correlation to the target variable', false],
                ],
            ],

            // ── 21.6 t-SNE & UMAP ─────────────────────────────────────────
            [
                'q' => 'What is t-SNE primarily used for?',
                'opts' => [
                    ['Training deep learning models faster', false],
                    ['Visualising high-dimensional data in 2D or 3D', true],
                    ['Predicting numerical values in regression tasks', false],
                    ['Sorting datasets by date', false],
                ],
            ],
            [
                'q' => 'What does t-SNE stand for?',
                'opts' => [
                    ['Total Statistical Norm Estimation', false],
                    ['t-distributed Stochastic Neighbour Embedding', true],
                    ['Temporal Standard Neural Encoding', false],
                    ['Tree-Structured Numerical Expansion', false],
                ],
            ],
            [
                'q' => 'What is UMAP?',
                'opts' => [
                    ['A type of clustering algorithm that requires K clusters', false],
                    ['Uniform Manifold Approximation and Projection — a dimensionality reduction method faster than t-SNE', true],
                    ['A supervised classification algorithm for text', false],
                    ['A graph database format for storing embeddings', false],
                ],
            ],
            [
                'q' => 'Compared to PCA, t-SNE and UMAP are better at:',
                'opts' => [
                    ['Preserving the global linear structure of data', false],
                    ['Capturing non-linear relationships and revealing local cluster structure in visualisations', true],
                    ['Running faster on very large datasets', false],
                    ['Producing results that can be used for new (unseen) data directly', false],
                ],
            ],
            [
                'q' => 'A data scientist plots MNIST handwritten digit embeddings using t-SNE and sees 10 distinct blobs. What does each blob likely represent?',
                'opts' => [
                    ['One iteration of the training process', false],
                    ['One of the 10 digit classes (0–9), showing that similar digits cluster together', true],
                    ['One feature in the dataset', false],
                    ['One outlier in the dataset', false],
                ],
            ],

            // ── 21.7 ANOMALY DETECTION: ISOLATION FOREST ─────────────────
            [
                'q' => 'What is anomaly detection in machine learning?',
                'opts' => [
                    ['A method to improve model accuracy by removing features', false],
                    ['The task of identifying data points that are unusual or significantly different from the rest', true],
                    ['A technique for labelling data automatically', false],
                    ['A way to increase the training dataset size', false],
                ],
            ],
            [
                'q' => 'What is an Isolation Forest?',
                'opts' => [
                    ['A forest of decision trees used for classifying plants', false],
                    ['An ensemble of random trees that isolates anomalies by partitioning data — anomalies are isolated faster (in fewer splits)', true],
                    ['A type of hierarchical clustering for geographic data', false],
                    ['A regularisation method for neural networks', false],
                ],
            ],
            [
                'q' => 'Why are anomalies (outliers) easier to isolate in an Isolation Forest?',
                'opts' => [
                    ['Because they have the highest feature values', false],
                    ['Because anomalies are rare and different — they require fewer random partitions to be separated from all other points', true],
                    ['Because the algorithm sorts data first to find the extremes', false],
                    ['Because anomalies always appear at the edges of the dataset', false],
                ],
            ],
            [
                'q' => 'Which of the following is a real-world application of anomaly detection?',
                'opts' => [
                    ['Sorting products alphabetically in an online store', false],
                    ['Detecting fraudulent credit card transactions', true],
                    ['Predicting tomorrow\'s weather', false],
                    ['Translating text between languages', false],
                ],
            ],

            // ── 21.8 AUTOENCODERS FOR UNSUPERVISED LEARNING ──────────────
            [
                'q' => 'What is an autoencoder?',
                'opts' => [
                    ['A machine that automatically writes computer code', false],
                    ['A neural network that learns to compress data into a smaller representation and then reconstruct it', true],
                    ['A type of decision tree that selects features automatically', false],
                    ['A supervised learning algorithm for text classification', false],
                ],
            ],
            [
                'q' => 'What are the two main parts of an autoencoder?',
                'opts' => [
                    ['Input layer and output layer only', false],
                    ['Encoder (compresses data) and decoder (reconstructs data)', true],
                    ['Training set and test set', false],
                    ['Features and labels', false],
                ],
            ],
            [
                'q' => 'What is the "bottleneck" layer in an autoencoder?',
                'opts' => [
                    ['The layer with the most neurons', false],
                    ['The compressed middle layer that holds the compact representation of the input', true],
                    ['The final output layer', false],
                    ['The layer that introduces noise for regularisation', false],
                ],
            ],
            [
                'q' => 'An autoencoder is trained to minimise what kind of error?',
                'opts' => [
                    ['Classification error — how often it misclassifies data', false],
                    ['Reconstruction error — how different the output is from the original input', true],
                    ['Prediction error — how far off its future predictions are', false],
                    ['Centroid error — how far data points are from their cluster centres', false],
                ],
            ],
            [
                'q' => 'What is one common use of autoencoders in data science?',
                'opts' => [
                    ['Sorting datasets in ascending order', false],
                    ['Removing noise from images (denoising)', true],
                    ['Increasing dataset size by copying rows', false],
                    ['Converting regression problems into classification', false],
                ],
            ],

            // ── 21.9 GAUSSIAN MIXTURE MODELS & SOFT CLUSTERING ───────────
            [
                'q' => 'What is a Gaussian Mixture Model (GMM)?',
                'opts' => [
                    ['A clustering algorithm that assigns each point to exactly one cluster, like K-Means', false],
                    ['A probabilistic model that assumes data comes from a mixture of several Gaussian (normal) distributions', true],
                    ['A type of neural network that uses Gaussian activation functions', false],
                    ['A method for sampling from a uniform distribution', false],
                ],
            ],
            [
                'q' => 'What is "soft clustering" in the context of GMMs?',
                'opts' => [
                    ['Clustering data that has been smoothed or filtered first', false],
                    ['Assigning each data point a probability of belonging to each cluster, rather than a hard single assignment', true],
                    ['A clustering method designed for soft (non-rigid) objects like clothing', false],
                    ['Clustering that uses very small values of K', false],
                ],
            ],
            [
                'q' => 'In K-Means, each point belongs to exactly ONE cluster. How is GMM different?',
                'opts' => [
                    ['GMM assigns each point to no cluster at all', false],
                    ['GMM gives each point a probability of belonging to EACH cluster — a point can partially belong to multiple clusters', true],
                    ['GMM requires more clusters than K-Means', false],
                    ['GMM only works on 2D data', false],
                ],
            ],
            [
                'q' => 'What shape of cluster can a Gaussian Mixture Model capture that K-Means cannot?',
                'opts' => [
                    ['Only perfectly circular clusters', false],
                    ['Elliptical (elongated/oval) shaped clusters', true],
                    ['Only square-shaped clusters', false],
                    ['No special shapes — GMM and K-Means are equivalent', false],
                ],
            ],

            // ── 21.10 END-TO-END UNSUPERVISED ML PIPELINE ────────────────
            [
                'q' => 'What is the typical FIRST step in an unsupervised machine learning pipeline?',
                'opts' => [
                    ['Train the clustering algorithm immediately on raw data', false],
                    ['Collect and preprocess data — handle missing values, scale features, etc.', true],
                    ['Select the number of clusters K', false],
                    ['Visualise the final clusters', false],
                ],
            ],
            [
                'q' => 'Why is feature scaling (e.g., standardisation) important before running K-Means?',
                'opts' => [
                    ['K-Means only works on text data, so scaling converts numbers to text', false],
                    ['K-Means uses distance calculations — features on larger scales would unfairly dominate the clustering', true],
                    ['Scaling increases the number of data points', false],
                    ['K-Means automatically ignores unscaled features', false],
                ],
            ],
            [
                'q' => 'What is the "Elbow Method" used for in an unsupervised ML pipeline?',
                'opts' => [
                    ['Choosing the best machine learning library to use', false],
                    ['Choosing the optimal number of clusters K in K-Means by looking for where adding more clusters gives diminishing returns', true],
                    ['Detecting outliers in the dataset', false],
                    ['Measuring the accuracy of a supervised model', false],
                ],
            ],
            [
                'q' => 'After clustering customer data, a business analyst wants to understand what makes each cluster unique. What should the data scientist do?',
                'opts' => [
                    ['Delete all clusters and start over with a different algorithm', false],
                    ['Analyse the mean feature values of each cluster to describe its characteristics', true],
                    ['Train a neural network on the cluster labels', false],
                    ['Add more features until the clusters disappear', false],
                ],
            ],
            [
                'q' => 'Which metric is commonly used to evaluate the quality of clusters when no ground-truth labels are available?',
                'opts' => [
                    ['Accuracy', false],
                    ['F1-Score', false],
                    ['Silhouette Score', true],
                    ['Mean Squared Error', false],
                ],
            ],
            [
                'q' => 'What does a Silhouette Score close to +1 indicate?',
                'opts' => [
                    ['The clusters are random and meaningless', false],
                    ['Each point is well inside its own cluster and far from neighbouring clusters — a good clustering', true],
                    ['The model has overfit the training data', false],
                    ['All data points are assigned to the same cluster', false],
                ],
            ],
            [
                'q' => 'In a complete unsupervised ML pipeline, which step comes LAST?',
                'opts' => [
                    ['Scaling the features', false],
                    ['Choosing the algorithm', false],
                    ['Interpreting and communicating the discovered patterns to stakeholders', true],
                    ['Splitting data into training and test sets', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 21 — Unsupervised Learning (Newbie).");
    }
}