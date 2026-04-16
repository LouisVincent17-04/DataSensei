<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module16ChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (!$category) {
            $this->command->error("Newbie category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 16 — Multivariate Analysis (Newbie)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Multivariate Analysis',
            'description'           => 'Test your knowledge of the very basics of multivariate analysis — what it means to work with multiple variables, what covariance and correlation are, and what methods like PCA and clustering are used for. No prior experience assumed!',
            'time_limit_seconds'    => 900,
            'base_xp'               => 500,
            'order_index'           => 16,
        ]);

        $this->command->info("Seeding 50 newbie-friendly questions...");

        $qaData = [

            // ── INTRODUCTION TO MULTIVARIATE DATA ────────────────────────
            [
                'q' => 'What does "multivariate" mean in statistics?',
                'opts' => [
                    ['Working with only one variable at a time', false],
                    ['Working with two or more variables simultaneously', true],
                    ['Working with very large datasets', false],
                    ['Working with categorical data only', false],
                ],
            ],
            [
                'q' => 'Which of the following is a multivariate dataset?',
                'opts' => [
                    ['A list of 100 heights', false],
                    ['A list of 100 weights', false],
                    ['A table recording each person\'s height, weight, and age', true],
                    ['A single temperature reading', false],
                ],
            ],
            [
                'q' => 'In multivariate analysis, each observation is typically represented as:',
                'opts' => [
                    ['A single number', false],
                    ['A vector of values (one value per variable)', true],
                    ['A pie chart', false],
                    ['A histogram', false],
                ],
            ],
            [
                'q' => 'What is the multivariate normal distribution?',
                'opts' => [
                    ['A normal distribution applied to a single variable', false],
                    ['A generalization of the normal distribution to multiple variables at once', true],
                    ['A distribution that only applies to binary data', false],
                    ['A distribution where all variables must be identical', false],
                ],
            ],
            [
                'q' => 'The shape of a multivariate normal distribution is determined by its mean vector and:',
                'opts' => [
                    ['Its range', false],
                    ['Its mode', false],
                    ['Its covariance matrix', true],
                    ['Its histogram', false],
                ],
            ],

            // ── COVARIANCE & CORRELATION ──────────────────────────────────
            [
                'q' => 'What does covariance measure between two variables?',
                'opts' => [
                    ['How far each variable is from the origin', false],
                    ['How two variables change together (their joint variability)', true],
                    ['The average value of each variable', false],
                    ['The number of observations', false],
                ],
            ],
            [
                'q' => 'If two variables tend to increase together, their covariance is:',
                'opts' => [
                    ['Negative', false],
                    ['Zero', false],
                    ['Positive', true],
                    ['Undefined', false],
                ],
            ],
            [
                'q' => 'The covariance matrix of a dataset with p variables has dimensions:',
                'opts' => [
                    ['p × 1', false],
                    ['1 × p', false],
                    ['p × p', true],
                    ['n × p (where n is the number of observations)', false],
                ],
            ],
            [
                'q' => 'What value appears on the diagonal of a covariance matrix?',
                'opts' => [
                    ['The mean of each variable', false],
                    ['The variance of each variable', true],
                    ['The correlation between pairs of variables', false],
                    ['Zero always', false],
                ],
            ],
            [
                'q' => 'The correlation coefficient between two variables is always between:',
                'opts' => [
                    ['0 and 1', false],
                    ['−1 and 1', true],
                    ['−∞ and +∞', false],
                    ['0 and 100', false],
                ],
            ],
            [
                'q' => 'A correlation of 0 between two variables means:',
                'opts' => [
                    ['They are identical', false],
                    ['They have no linear relationship', true],
                    ['They are perfectly inversely related', false],
                    ['One causes the other', false],
                ],
            ],
            [
                'q' => 'What is the difference between covariance and correlation?',
                'opts' => [
                    ['They measure different things entirely', false],
                    ['Correlation is a standardized version of covariance (scale-free, between −1 and 1)', true],
                    ['Covariance is always larger than correlation', false],
                    ['Correlation is only used for categorical variables', false],
                ],
            ],

            // ── PRINCIPAL COMPONENT ANALYSIS (PCA) ───────────────────────
            [
                'q' => 'What is the main goal of Principal Component Analysis (PCA)?',
                'opts' => [
                    ['To classify data into groups', false],
                    ['To reduce the number of variables while keeping as much variation as possible', true],
                    ['To find the mean of all variables', false],
                    ['To remove all correlations by deleting variables', false],
                ],
            ],
            [
                'q' => 'The first principal component captures:',
                'opts' => [
                    ['The least variance in the data', false],
                    ['The most variance in the data', true],
                    ['The average of all variables', false],
                    ['All the variance in the data', false],
                ],
            ],
            [
                'q' => 'Principal components are always:',
                'opts' => [
                    ['Correlated with each other', false],
                    ['Uncorrelated (orthogonal) with each other', true],
                    ['Equal in variance', false],
                    ['The same as the original variables', false],
                ],
            ],
            [
                'q' => 'Before applying PCA, it is common practice to:',
                'opts' => [
                    ['Remove all negative values', false],
                    ['Standardize (center and scale) the variables', true],
                    ['Convert all variables to binary', false],
                    ['Remove all zero values', false],
                ],
            ],
            [
                'q' => 'In PCA, a "scree plot" is used to:',
                'opts' => [
                    ['Plot the original data points', false],
                    ['Visualize how much variance each principal component explains', true],
                    ['Show the correlation matrix', false],
                    ['Display the mean of each component', false],
                ],
            ],

            // ── FACTOR ANALYSIS ───────────────────────────────────────────
            [
                'q' => 'What is the goal of Factor Analysis?',
                'opts' => [
                    ['To predict a numeric outcome variable', false],
                    ['To discover underlying hidden (latent) factors that explain correlations among observed variables', true],
                    ['To cluster data into groups', false],
                    ['To test if two groups have different means', false],
                ],
            ],
            [
                'q' => 'In Factor Analysis, a "factor loading" represents:',
                'opts' => [
                    ['The mean value of a factor', false],
                    ['The correlation between an observed variable and a latent factor', true],
                    ['The number of variables in the model', false],
                    ['The variance explained by all factors combined', false],
                ],
            ],
            [
                'q' => 'Factor Analysis is different from PCA primarily because:',
                'opts' => [
                    ['Factor Analysis uses more data', false],
                    ['Factor Analysis assumes underlying latent factors cause the observed correlations; PCA is a purely mathematical transformation with no causal assumption', true],
                    ['PCA works on categorical data; Factor Analysis does not', false],
                    ['They are exactly the same method', false],
                ],
            ],

            // ── MANOVA ────────────────────────────────────────────────────
            [
                'q' => 'What does MANOVA stand for?',
                'opts' => [
                    ['Multivariate Analysis of Variance', true],
                    ['Multiple Average Numerical Output Variation Analysis', false],
                    ['Multivariate Assessment of Nominal Variables Analysis', false],
                    ['Mean-Adjusted Null-Output Variance Analysis', false],
                ],
            ],
            [
                'q' => 'How is MANOVA different from ANOVA?',
                'opts' => [
                    ['MANOVA uses fewer groups', false],
                    ['MANOVA tests differences in multiple dependent variables simultaneously; ANOVA tests only one dependent variable at a time', true],
                    ['MANOVA is used for categorical outcomes; ANOVA is for numeric', false],
                    ['There is no difference', false],
                ],
            ],
            [
                'q' => 'MANOVA is appropriate when you want to test whether group means differ across:',
                'opts' => [
                    ['A single dependent variable', false],
                    ['Multiple dependent variables at the same time', true],
                    ['Multiple independent variables only', false],
                    ['A binary outcome', false],
                ],
            ],

            // ── DISCRIMINANT ANALYSIS ─────────────────────────────────────
            [
                'q' => 'What is the main purpose of Linear Discriminant Analysis (LDA)?',
                'opts' => [
                    ['To reduce the number of variables', false],
                    ['To find a linear combination of variables that best separates two or more known groups', true],
                    ['To cluster data into unknown groups', false],
                    ['To compute the covariance matrix', false],
                ],
            ],
            [
                'q' => 'LDA is primarily used for:',
                'opts' => [
                    ['Unsupervised learning (finding hidden structure)', false],
                    ['Supervised classification (predicting which group a new observation belongs to)', true],
                    ['Reducing the number of rows in a dataset', false],
                    ['Computing means and variances', false],
                ],
            ],
            [
                'q' => 'Quadratic Discriminant Analysis (QDA) differs from LDA because:',
                'opts' => [
                    ['QDA uses fewer variables', false],
                    ['QDA allows each group to have its own covariance matrix (more flexible than LDA)', true],
                    ['QDA can only handle two groups', false],
                    ['QDA is unsupervised while LDA is supervised', false],
                ],
            ],

            // ── CLUSTER ANALYSIS ──────────────────────────────────────────
            [
                'q' => 'What is the goal of cluster analysis?',
                'opts' => [
                    ['To predict a known outcome', false],
                    ['To group observations so that items in the same group are more similar to each other than to items in other groups', true],
                    ['To find the mean of all variables', false],
                    ['To test a statistical hypothesis', false],
                ],
            ],
            [
                'q' => 'k-Means clustering requires the user to specify:',
                'opts' => [
                    ['The size of each cluster', false],
                    ['The number of clusters (k) in advance', true],
                    ['The maximum variance allowed', false],
                    ['The labels for each cluster', false],
                ],
            ],
            [
                'q' => 'In k-Means, the algorithm assigns each point to the cluster with the:',
                'opts' => [
                    ['Highest variance', false],
                    ['Nearest centroid (center)', true],
                    ['Most observations', false],
                    ['Smallest index number', false],
                ],
            ],
            [
                'q' => 'Hierarchical clustering produces a tree-like diagram called a:',
                'opts' => [
                    ['Scree plot', false],
                    ['Dendrogram', true],
                    ['Scatter plot', false],
                    ['Box plot', false],
                ],
            ],
            [
                'q' => 'Is cluster analysis supervised or unsupervised?',
                'opts' => [
                    ['Supervised — you need known group labels', false],
                    ['Unsupervised — no group labels are needed', true],
                    ['Semi-supervised only', false],
                    ['Neither — it is a hypothesis test', false],
                ],
            ],

            // ── CANONICAL CORRELATION ANALYSIS ───────────────────────────
            [
                'q' => 'What does Canonical Correlation Analysis (CCA) examine?',
                'opts' => [
                    ['The variance within a single set of variables', false],
                    ['The relationship between two sets of variables', true],
                    ['The difference in means between two groups', false],
                    ['The principal components of a dataset', false],
                ],
            ],
            [
                'q' => 'CCA finds linear combinations of each variable set that are:',
                'opts' => [
                    ['As uncorrelated as possible', false],
                    ['Maximally correlated with each other across the two sets', true],
                    ['Identical to the original variables', false],
                    ['Minimally correlated with the original variables', false],
                ],
            ],

            // ── MDS & t-SNE ───────────────────────────────────────────────
            [
                'q' => 'What is the purpose of Multidimensional Scaling (MDS)?',
                'opts' => [
                    ['To find the mean distance between observations', false],
                    ['To visualize high-dimensional data in a low-dimensional space while preserving pairwise distances', true],
                    ['To classify observations into groups', false],
                    ['To compute the correlation matrix', false],
                ],
            ],
            [
                'q' => 't-SNE is primarily used for:',
                'opts' => [
                    ['Testing hypotheses about group means', false],
                    ['Visualizing high-dimensional data in 2D or 3D', true],
                    ['Computing pairwise correlations', false],
                    ['Performing cluster assignments', false],
                ],
            ],
            [
                'q' => 'Which statement about t-SNE is correct?',
                'opts' => [
                    ['t-SNE results are easy to interpret quantitatively', false],
                    ['t-SNE preserves global distances perfectly', false],
                    ['t-SNE is mainly a visualization tool and distances in the plot should not be over-interpreted', true],
                    ['t-SNE always produces the same result regardless of random seed', false],
                ],
            ],

            // ── MULTIVARIATE REGRESSION ───────────────────────────────────
            [
                'q' => 'Multivariate regression extends simple linear regression by:',
                'opts' => [
                    ['Using a single predictor variable', false],
                    ['Using multiple predictor variables to predict one or more outcome variables', true],
                    ['Removing all correlated predictors', false],
                    ['Applying only to categorical outcomes', false],
                ],
            ],
            [
                'q' => 'In a regression equation Y = β₀ + β₁X₁ + β₂X₂ + ε, what does β₁ represent?',
                'opts' => [
                    ['The intercept when all predictors are zero', false],
                    ['The expected change in Y for a one-unit increase in X₁, holding X₂ constant', true],
                    ['The total variance explained by the model', false],
                    ['The correlation between X₁ and X₂', false],
                ],
            ],
            [
                'q' => 'What is multicollinearity in multivariate regression?',
                'opts' => [
                    ['When the outcome variable has multiple values', false],
                    ['When two or more predictor variables are highly correlated with each other', true],
                    ['When the model has too many observations', false],
                    ['When the residuals are not normally distributed', false],
                ],
            ],

            // ── GENERAL CONCEPTS ──────────────────────────────────────────
            [
                'q' => 'What is a "dimension" in the context of multivariate data?',
                'opts' => [
                    ['The physical size of the dataset file', false],
                    ['Each variable or feature measured for each observation', true],
                    ['The number of rows in the dataset', false],
                    ['A measure of statistical significance', false],
                ],
            ],
            [
                'q' => 'The "curse of dimensionality" refers to:',
                'opts' => [
                    ['Having too many observations', false],
                    ['The difficulty of analysis and the sparsity of data as the number of variables grows very large', true],
                    ['Datasets that are too small', false],
                    ['Having highly correlated variables', false],
                ],
            ],
            [
                'q' => 'Dimensionality reduction methods like PCA are useful because they:',
                'opts' => [
                    ['Always improve prediction accuracy', false],
                    ['Simplify data by reducing the number of variables while retaining key information', true],
                    ['Remove all noise from the data', false],
                    ['Guarantee independence between variables', false],
                ],
            ],
            [
                'q' => 'Which of the following is an UNSUPERVISED multivariate method?',
                'opts' => [
                    ['LDA', false],
                    ['MANOVA', false],
                    ['k-Means Clustering', true],
                    ['Multiple Regression', false],
                ],
            ],
            [
                'q' => 'Which of the following is a SUPERVISED multivariate method?',
                'opts' => [
                    ['PCA', false],
                    ['Hierarchical Clustering', false],
                    ['t-SNE', false],
                    ['Linear Discriminant Analysis (LDA)', true],
                ],
            ],
            [
                'q' => 'A scatter plot matrix (pairs plot) is useful for:',
                'opts' => [
                    ['Viewing all pairwise relationships between variables at once', true],
                    ['Showing the distribution of a single variable', false],
                    ['Ranking variables by importance', false],
                    ['Computing the correlation matrix numerically', false],
                ],
            ],
            [
                'q' => 'What does a heat map of a correlation matrix show?',
                'opts' => [
                    ['The raw values of each observation', false],
                    ['The pairwise correlations between all variables, colour-coded by strength and direction', true],
                    ['The cluster membership of each observation', false],
                    ['The principal component loadings only', false],
                ],
            ],
            [
                'q' => 'In multivariate statistics, an "outlier" can be detected using the Mahalanobis distance because it:',
                'opts' => [
                    ['Measures only the distance from the mean of a single variable', false],
                    ['Accounts for the correlations between variables when measuring how far a point is from the center of the data', true],
                    ['Always equals the Euclidean distance', false],
                    ['Requires no knowledge of the covariance matrix', false],
                ],
            ],
            [
                'q' => 'Path Analysis is used to:',
                'opts' => [
                    ['Find the shortest path between observations', false],
                    ['Test and estimate causal or directional relationships among multiple variables', true],
                    ['Sort variables into hierarchical clusters', false],
                    ['Reduce the dataset to two dimensions', false],
                ],
            ],
            [
                'q' => 'Which multivariate method would you use to understand which variables best separate already-known species groups?',
                'opts' => [
                    ['PCA', false],
                    ['k-Means Clustering', false],
                    ['Linear Discriminant Analysis (LDA)', true],
                    ['MDS', false],
                ],
            ],
            [
                'q' => 'Which multivariate method would you use to explore unknown groupings in customer purchase data?',
                'opts' => [
                    ['MANOVA', false],
                    ['LDA', false],
                    ['Cluster Analysis', true],
                    ['Canonical Correlation Analysis', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 16 — Multivariate Analysis (Newbie).");
    }
}