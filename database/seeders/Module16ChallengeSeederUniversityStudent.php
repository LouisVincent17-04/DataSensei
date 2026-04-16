<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module16ChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (!$category) {
            $this->command->error("University Student category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 16 — Multivariate Analysis (University Student)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Multivariate Analysis',
            'description'           => 'Deepen your understanding of multivariate methods — interpret covariance matrices, trace PCA outputs, reason about LDA assumptions, evaluate clustering results, and apply analytical thinking to MANOVA and regression scenarios.',
            'time_limit_seconds'    => 1200,
            'base_xp'               => 1000,
            'order_index'           => 16,
        ]);

        $this->command->info("Seeding 50 university-level questions...");

        $qaData = [

            // ── MULTIVARIATE NORMAL ───────────────────────────────────────
            [
                'q' => "The multivariate normal distribution for a p-dimensional random vector X is fully described by:\n\n(A) its mean vector μ only\n(B) its covariance matrix Σ only\n(C) both its mean vector μ and covariance matrix Σ\n(D) only the number of dimensions p",
                'opts' => [
                    ['(A)', false],
                    ['(B)', false],
                    ['(C)', true],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "For a bivariate normal distribution with equal variances σ² = 1 and correlation ρ = 0, the covariance matrix Σ is:",
                'opts' => [
                    ['[[1, 1], [1, 1]]', false],
                    ['[[1, 0], [0, 1]] — the identity matrix', true],
                    ['[[0, 1], [1, 0]]', false],
                    ['[[ρ, 0], [0, ρ]]', false],
                ],
            ],
            [
                'q' => "If X ~ N(μ, Σ), every linear combination aᵀX follows what distribution?",
                'opts' => [
                    ['A chi-squared distribution', false],
                    ['A t-distribution', false],
                    ['A univariate normal distribution', true],
                    ['A uniform distribution', false],
                ],
            ],

            // ── COVARIANCE MATRIX: ANALYTICAL ────────────────────────────
            [
                'q' => "Given the covariance matrix:\nΣ = [[4, 2], [2, 9]]\n\nWhat is the variance of the first variable?",
                'opts' => [
                    ['2', false],
                    ['9', false],
                    ['4', true],
                    ['√4 = 2', false],
                ],
            ],
            [
                'q' => "Using Σ = [[4, 2], [2, 9]], what is the correlation between the two variables?",
                'opts' => [
                    ['2 / (4 × 9) = 0.056', false],
                    ['2 / √(4 × 9) = 2/6 = 0.333', true],
                    ['4 / 9 = 0.444', false],
                    ['√2 = 1.414', false],
                ],
            ],
            [
                'q' => "A covariance matrix must always be:\n\n(A) Symmetric\n(B) Positive semi-definite\n(C) Both A and B\n(D) Neither",
                'opts' => [
                    ['(A) only', false],
                    ['(B) only', false],
                    ['(C) — symmetric and positive semi-definite', true],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "The sample covariance matrix S is computed as:\n\nS = (1/(n−1)) × Σᵢ (xᵢ − x̄)(xᵢ − x̄)ᵀ\n\nWhy is n−1 used instead of n?",
                'opts' => [
                    ['To make the computation faster', false],
                    ['To produce an unbiased estimate of the population covariance matrix', true],
                    ['Because x̄ is unknown', false],
                    ['To ensure the matrix is positive definite', false],
                ],
            ],

            // ── PCA: ANALYTICAL ───────────────────────────────────────────
            [
                'q' => "PCA finds principal components by computing the eigenvectors and eigenvalues of the:\n\n(A) Data matrix X directly\n(B) Covariance matrix (or correlation matrix)\n(C) Mean vector\n(D) Distance matrix",
                'opts' => [
                    ['(A)', false],
                    ['(B) — eigenvectors of the covariance/correlation matrix', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "Given eigenvalues λ₁ = 6, λ₂ = 3, λ₃ = 1, what proportion of total variance does the first principal component explain?",
                'opts' => [
                    ['6/3 = 2.0 (200%)', false],
                    ['6/(6+3+1) = 0.60 (60%)', true],
                    ['1/3 = 0.33 (33%)', false],
                    ['6/1 = 6 (600%)', false],
                ],
            ],
            [
                'q' => "How many principal components can be extracted from a dataset with p = 5 variables and n = 100 observations?",
                'opts' => [
                    ['100', false],
                    ['5', true],
                    ['Unlimited', false],
                    ['1', false],
                ],
            ],
            [
                'q' => "The Kaiser criterion for selecting the number of principal components retains components with eigenvalues:",
                'opts' => [
                    ['Greater than the mean eigenvalue (> 1 when using the correlation matrix)', true],
                    ['Greater than zero', false],
                    ['Less than 1', false],
                    ['Equal to the largest eigenvalue', false],
                ],
            ],
            [
                'q' => "If you apply PCA to unstandardized variables where one variable is measured in kilometres and another in millimetres, what problem arises?",
                'opts' => [
                    ['PCA cannot handle more than one variable', false],
                    ['PCA will be dominated by the variable with the largest variance (millimetres) even if it is not the most informative — variables should be standardized first', true],
                    ['PCA requires all variables to be in the same unit', false],
                    ['No problem arises — PCA is scale-invariant', false],
                ],
            ],

            // ── FACTOR ANALYSIS: ANALYTICAL ───────────────────────────────
            [
                'q' => "In Factor Analysis, the 'communality' of a variable is:",
                'opts' => [
                    ['The portion of its variance NOT explained by the common factors', false],
                    ['The portion of its variance explained by the common factors', true],
                    ['The correlation between two factors', false],
                    ['The number of factors retained', false],
                ],
            ],
            [
                'q' => "The 'uniqueness' (or specific variance) in Factor Analysis is:\n\nuniqueᵢ = 1 − communalityᵢ\n\nIf communality = 0.75, what is the uniqueness?",
                'opts' => [
                    ['0.75', false],
                    ['0.25', true],
                    ['1.75', false],
                    ['0.50', false],
                ],
            ],
            [
                'q' => "Factor rotation (e.g., Varimax) is applied in Factor Analysis to:",
                'opts' => [
                    ['Increase the number of factors', false],
                    ['Make factor loadings easier to interpret by making each variable load highly on as few factors as possible', true],
                    ['Remove correlated factors', false],
                    ['Standardize the factor scores', false],
                ],
            ],

            // ── MANOVA: ANALYTICAL ────────────────────────────────────────
            [
                'q' => "MANOVA tests the null hypothesis that:",
                'opts' => [
                    ['All variables have equal variance', false],
                    ['The mean vectors of all groups are equal across all dependent variables simultaneously', true],
                    ['The covariance matrices of all groups are identical', false],
                    ['Each dependent variable is normally distributed', false],
                ],
            ],
            [
                'q' => "Which of the following is a test statistic used in MANOVA?",
                'opts' => [
                    ['F-ratio only', false],
                    ['Wilks\' Lambda', true],
                    ['Pearson\'s r', false],
                    ['Chi-squared', false],
                ],
            ],
            [
                'q' => "Wilks' Lambda (Λ) ranges from 0 to 1. A value close to 0 indicates:",
                'opts' => [
                    ['The groups are very similar (null hypothesis supported)', false],
                    ['Strong group separation (large between-group variance relative to within-group variance)', true],
                    ['All eigenvalues are equal', false],
                    ['The assumption of equal covariance matrices is violated', false],
                ],
            ],
            [
                'q' => "One key assumption of MANOVA is the homogeneity of covariance matrices, tested by:",
                'opts' => [
                    ['Levene\'s test', false],
                    ['Box\'s M test', true],
                    ['Bartlett\'s test', false],
                    ['Mauchly\'s test', false],
                ],
            ],

            // ── LDA / QDA: ANALYTICAL ─────────────────────────────────────
            [
                'q' => "LDA maximizes the ratio of:\n\n(A) Within-group variance to between-group variance\n(B) Between-group variance to within-group variance\n(C) Total variance to within-group variance\n(D) Between-group covariance to total covariance",
                'opts' => [
                    ['(A)', false],
                    ['(B) — between / within, i.e., Fisher\'s criterion', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "How many linear discriminant functions can be extracted when classifying k groups with p variables?",
                'opts' => [
                    ['min(k, p)', false],
                    ['max(k, p)', false],
                    ['min(k−1, p)', true],
                    ['k × p', false],
                ],
            ],
            [
                'q' => "LDA assumes equal covariance matrices across all groups. When this assumption is seriously violated, the preferred alternative is:",
                'opts' => [
                    ['MANOVA', false],
                    ['PCA', false],
                    ['QDA (Quadratic Discriminant Analysis)', true],
                    ['k-Means clustering', false],
                ],
            ],

            // ── CLUSTER ANALYSIS: ANALYTICAL ──────────────────────────────
            [
                'q' => "The Within-Cluster Sum of Squares (WCSS) is minimized by k-Means. As k increases, WCSS:",
                'opts' => [
                    ['Increases monotonically', false],
                    ['Decreases monotonically (reaches 0 when k = n)', true],
                    ['Stays constant', false],
                    ['First increases then decreases', false],
                ],
            ],
            [
                'q' => "The 'elbow method' for choosing k in k-Means looks for:",
                'opts' => [
                    ['The k value where WCSS is highest', false],
                    ['The k value where adding another cluster produces diminishing reductions in WCSS', true],
                    ['The k value where all clusters have equal size', false],
                    ['The k value where the silhouette score is lowest', false],
                ],
            ],
            [
                'q' => "In hierarchical clustering, 'complete linkage' defines the distance between two clusters as:",
                'opts' => [
                    ['The distance between the two nearest points in different clusters', false],
                    ['The distance between the two farthest points in different clusters', true],
                    ['The average of all pairwise distances between the two clusters', false],
                    ['The distance between the centroids of the two clusters', false],
                ],
            ],
            [
                'q' => "The silhouette score for an observation measures how similar it is to its own cluster compared to neighbouring clusters. A score close to +1 means:",
                'opts' => [
                    ['The observation is poorly clustered', false],
                    ['The observation is well-matched to its own cluster and poorly matched to neighbouring clusters', true],
                    ['The observation is equidistant from all clusters', false],
                    ['The cluster has only one observation', false],
                ],
            ],

            // ── CCA: ANALYTICAL ───────────────────────────────────────────
            [
                'q' => "In Canonical Correlation Analysis between variable set X (p variables) and set Y (q variables), the maximum number of canonical variate pairs is:",
                'opts' => [
                    ['p + q', false],
                    ['p × q', false],
                    ['min(p, q)', true],
                    ['max(p, q)', false],
                ],
            ],
            [
                'q' => "The first canonical correlation r₁* is always:",
                'opts' => [
                    ['Negative', false],
                    ['The largest canonical correlation — it is the maximum linear correlation achievable between any linear combination of X and any linear combination of Y', true],
                    ['Equal to Pearson\'s correlation between X₁ and Y₁', false],
                    ['Less than the second canonical correlation', false],
                ],
            ],

            // ── MDS & t-SNE: ANALYTICAL ───────────────────────────────────
            [
                'q' => "In Metric MDS, the algorithm tries to place n points in a low-dimensional space so that the _____ between them match the original dissimilarities.",
                'opts' => [
                    ['Angles', false],
                    ['Euclidean distances', true],
                    ['Variances', false],
                    ['Correlations', false],
                ],
            ],
            [
                'q' => "The 'stress' value in MDS measures:",
                'opts' => [
                    ['The number of dimensions used', false],
                    ['How well the low-dimensional distances reproduce the original dissimilarities — lower stress = better fit', true],
                    ['The variance explained by the first dimension', false],
                    ['The number of outliers in the data', false],
                ],
            ],
            [
                'q' => "The perplexity parameter in t-SNE controls:",
                'opts' => [
                    ['The number of dimensions in the output', false],
                    ['A balance between local and global structure — roughly the number of nearest neighbours each point considers', true],
                    ['The learning rate of the gradient descent', false],
                    ['The number of random restarts', false],
                ],
            ],

            // ── MULTIVARIATE REGRESSION: ANALYTICAL ──────────────────────
            [
                'q' => "In multiple regression Y = β₀ + β₁X₁ + β₂X₂ + ε, the Variance Inflation Factor (VIF) for X₁ is used to detect:",
                'opts' => [
                    ['Outliers in Y', false],
                    ['Multicollinearity — whether X₁ is highly correlated with the other predictors', true],
                    ['Non-normality of residuals', false],
                    ['Heteroscedasticity', false],
                ],
            ],
            [
                'q' => "A VIF value greater than _____ is commonly considered a sign of serious multicollinearity.",
                'opts' => [
                    ['1', false],
                    ['2', false],
                    ['10', true],
                    ['100', false],
                ],
            ],
            [
                'q' => "Adjusted R² is preferred over R² in multiple regression because:",
                'opts' => [
                    ['It is always larger than R²', false],
                    ['It penalizes for adding uninformative predictors, preventing artificial inflation of R²', true],
                    ['It is easier to compute', false],
                    ['It ranges from −1 to 1', false],
                ],
            ],

            // ── MAHALANOBIS DISTANCE ──────────────────────────────────────
            [
                'q' => "The Mahalanobis distance from point x to mean μ with covariance Σ is:\n\nD²_M = (x − μ)ᵀ Σ⁻¹ (x − μ)\n\nIf Σ = I (identity matrix), the Mahalanobis distance reduces to:",
                'opts' => [
                    ['Zero', false],
                    ['The Euclidean distance', true],
                    ['The Manhattan distance', false],
                    ['The cosine distance', false],
                ],
            ],
            [
                'q' => "Under multivariate normality, the squared Mahalanobis distance D²_M follows approximately a _____ distribution.",
                'opts' => [
                    ['Normal distribution', false],
                    ['t-distribution', false],
                    ['Chi-squared distribution with p degrees of freedom', true],
                    ['F-distribution', false],
                ],
            ],

            // ── MISC ANALYTICAL ───────────────────────────────────────────
            [
                'q' => "Hotelling's T² test is the multivariate analogue of the:",
                'opts' => [
                    ['Chi-squared test', false],
                    ['One-sample or two-sample t-test', true],
                    ['F-test', false],
                    ['ANOVA', false],
                ],
            ],
            [
                'q' => "The Bartlett test of sphericity in Factor Analysis / PCA tests whether:",
                'opts' => [
                    ['All factors have equal variance', false],
                    ['The correlation matrix is significantly different from the identity matrix (i.e., there are meaningful correlations worth extracting)', true],
                    ['The data follows a multivariate normal distribution', false],
                    ['All variables have equal means', false],
                ],
            ],
            [
                'q' => "The Kaiser-Meyer-Olkin (KMO) measure in Factor Analysis assesses:",
                'opts' => [
                    ['How many factors to retain', false],
                    ['The sampling adequacy — whether the patterns of correlations are compact enough for factor analysis to produce distinct, reliable factors', true],
                    ['Whether the factors are orthogonal', false],
                    ['The proportion of variance explained', false],
                ],
            ],
            [
                'q' => "In path analysis, a 'path coefficient' is essentially a:",
                'opts' => [
                    ['Correlation coefficient between two observed variables', false],
                    ['Standardized regression coefficient representing a direct causal effect', true],
                    ['Factor loading in Factor Analysis', false],
                    ['Canonical correlation between two variable sets', false],
                ],
            ],
            [
                'q' => "Which of the following describes an 'indirect effect' in path analysis?",
                'opts' => [
                    ['The direct relationship between predictor and outcome', false],
                    ['The effect of one variable on another transmitted through a mediating variable', true],
                    ['The total variance explained by the model', false],
                    ['A non-significant path in the diagram', false],
                ],
            ],
            [
                'q' => "DBSCAN clustering differs from k-Means because DBSCAN:\n\n(A) Requires k to be specified in advance\n(B) Can find clusters of arbitrary shape and identify noise points\n(C) Always produces spherical clusters\n(D) Cannot handle large datasets",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "In the context of PCA, a 'biplot' simultaneously displays:",
                'opts' => [
                    ['Only the observations in PC space', false],
                    ['Both the observations (scores) and the original variable directions (loadings) in the principal component space', true],
                    ['Only the variable loadings', false],
                    ['The eigenvalues and eigenvectors numerically', false],
                ],
            ],
            [
                'q' => "Canonical Correlation Analysis generalises which simpler analysis?",
                'opts' => [
                    ['t-test', false],
                    ['Simple (bivariate) Pearson correlation — CCA extends it to two sets of variables', true],
                    ['ANOVA', false],
                    ['PCA', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 16 — Multivariate Analysis (University Student).");
    }
}