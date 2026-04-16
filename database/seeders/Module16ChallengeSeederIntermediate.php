<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module16ChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (!$category) {
            $this->command->error("Intermediate category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 16 — Multivariate Analysis (Intermediate)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Multivariate Analysis',
            'description'           => 'Tackle multi-step multivariate problems — compute and interpret PCA by hand, work through LDA projections, evaluate clustering quality metrics, calculate Mahalanobis distances, and reason through MANOVA designs and CCA outputs.',
            'time_limit_seconds'    => 1800,
            'base_xp'               => 1500,
            'order_index'           => 16,
        ]);

        $this->command->info("Seeding 50 intermediate-level questions...");

        $qaData = [

            // ── PCA: MULTI-STEP CALCULATIONS ──────────────────────────────
            [
                'q' => "A dataset has covariance matrix:\nΣ = [[5, 4], [4, 5]]\n\nStep 1: Find the eigenvalues.\nThe characteristic equation is (5−λ)² − 16 = 0.\nWhat are the two eigenvalues?",
                'opts' => [
                    ['λ₁ = 9, λ₂ = 1', true],
                    ['λ₁ = 5, λ₂ = 5', false],
                    ['λ₁ = 10, λ₂ = 0', false],
                    ['λ₁ = 4, λ₂ = 4', false],
                ],
            ],
            [
                'q' => "Using the eigenvalues λ₁ = 9, λ₂ = 1 from Σ = [[5,4],[4,5]]:\n\nWhat percentage of variance does the first PC explain?",
                'opts' => [
                    ['50%', false],
                    ['90%', true],
                    ['75%', false],
                    ['100%', false],
                ],
            ],
            [
                'q' => "For the eigenvector corresponding to λ₁ = 9 in Σ = [[5,4],[4,5]]:\n\n(Σ − 9I)v = 0  →  [[-4, 4],[4, -4]]v = 0\n\nWhat is the normalised eigenvector?",
                'opts' => [
                    ['[1, 0]', false],
                    ['[1/√2, 1/√2]', true],
                    ['[1/√2, -1/√2]', false],
                    ['[0, 1]', false],
                ],
            ],
            [
                'q' => "After PCA on a 10-variable dataset you retain 4 PCs that explain 85% of variance.\n\nYou project the original 200×10 data matrix X onto these 4 PCs.\n\nWhat are the dimensions of the resulting score matrix?",
                'opts' => [
                    ['10 × 4', false],
                    ['200 × 4', true],
                    ['4 × 4', false],
                    ['200 × 10', false],
                ],
            ],
            [
                'q' => "A loading of −0.82 for variable X₃ on PC₁ means:\n\n(A) X₃ has a weak relationship with PC₁\n(B) X₃ has a strong negative relationship with PC₁ — high X₃ scores correspond to low PC₁ scores\n(C) X₃ is uncorrelated with PC₁\n(D) PC₁ causes X₃ to decrease",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "You perform PCA on the correlation matrix R (standardised PCA) rather than the covariance matrix S. The trace of R always equals p (the number of variables). Therefore the average eigenvalue is always:",
                'opts' => [
                    ['0', false],
                    ['p²', false],
                    ['1', true],
                    ['p/2', false],
                ],
            ],

            // ── FACTOR ANALYSIS: MULTI-STEP ───────────────────────────────
            [
                'q' => "In exploratory Factor Analysis with 4 observed variables and 2 factors:\n\nLoadings matrix:\n       F1     F2\nX1:   0.85   0.10\nX2:   0.80   0.15\nX3:   0.05   0.90\nX4:   0.10   0.88\n\nWhat do F1 and F2 likely represent?",
                'opts' => [
                    ['F1 captures X3 and X4; F2 captures X1 and X2', false],
                    ['F1 is mainly driven by X1 and X2; F2 is mainly driven by X3 and X4', true],
                    ['F1 and F2 are uncorrelated with all variables', false],
                    ['F1 = F2 because both explain 50% of variance', false],
                ],
            ],
            [
                'q' => "Compute the communality for X1 given loadings L₁₁ = 0.85 and L₁₂ = 0.10:\n\nh²₁ = L²₁₁ + L²₁₂",
                'opts' => [
                    ['0.85 + 0.10 = 0.95', false],
                    ['0.85² + 0.10² = 0.7225 + 0.01 = 0.7325', true],
                    ['0.85 × 0.10 = 0.085', false],
                    ['√(0.85² + 0.10²) = 0.856', false],
                ],
            ],
            [
                'q' => "Oblique rotation (e.g., Promax) differs from orthogonal rotation (e.g., Varimax) in that:\n\n(A) Oblique rotation forces factors to remain uncorrelated\n(B) Oblique rotation allows factors to be correlated with each other\n(C) Oblique rotation always produces simpler factor structures\n(D) Oblique rotation is only used when communalities are all > 0.9",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],

            // ── MANOVA: MULTI-STEP ────────────────────────────────────────
            [
                'q' => "A one-way MANOVA has 3 groups and 4 dependent variables.\n\nThe total SSCP matrix T = B + W, where B is the between-group SSCP and W is the within-group SSCP.\n\nWilks' Lambda is computed as:\n\nΛ = det(W) / det(B + W) = det(W) / det(T)\n\nIf det(W) = 120 and det(T) = 400, what is Λ?",
                'opts' => [
                    ['400/120 = 3.33', false],
                    ['120/400 = 0.30', true],
                    ['(400−120)/400 = 0.70', false],
                    ['120 × 400 = 48000', false],
                ],
            ],
            [
                'q' => "Λ = 0.30 from the previous question. The corresponding Pillai's Trace (approximate) uses the between-group SSCP.\n\nWhich conclusion is correct about Λ = 0.30?",
                'opts' => [
                    ['There is little evidence of group differences (Λ near 1 is strong separation)', false],
                    ['There is strong evidence of group differences — Λ = 0.30 is much less than 1, indicating large between-group variance relative to within-group variance', true],
                    ['Λ = 0.30 means 30% of variance is explained', false],
                    ['The test is inconclusive because Λ must be exactly 0 for significance', false],
                ],
            ],
            [
                'q' => "Following a significant MANOVA, the recommended follow-up analysis to identify WHICH dependent variables differ between groups is:",
                'opts' => [
                    ['Running separate ANOVAs for each dependent variable with Bonferroni correction', true],
                    ['Re-running MANOVA without some of the dependent variables', false],
                    ['Running a chi-squared test on each variable', false],
                    ['No follow-up is needed if MANOVA is significant', false],
                ],
            ],

            // ── LDA: MULTI-STEP ───────────────────────────────────────────
            [
                'q' => "In LDA with two groups (g=2), the Fisher linear discriminant function for a new observation x is:\n\nScore = wᵀx,  where w = S⁻¹_W (μ₁ − μ₂)\n\nIf S⁻¹_W(μ₁−μ₂) = [2, 3] and x = [1, 2], what is the discriminant score?",
                'opts' => [
                    ['2 + 2 = 4', false],
                    ['2×1 + 3×2 = 8', true],
                    ['2×2 + 3×3 = 13', false],
                    ['(2+3) × (1+2) = 15', false],
                ],
            ],
            [
                'q' => "A new observation scores 8 on the discriminant function. The midpoint (threshold) between group centroids on the discriminant axis is 6. The classification rule assigns it to:",
                'opts' => [
                    ['Group 2 (below threshold)', false],
                    ['Group 1 (above threshold, since 8 > 6)', true],
                    ['Neither group — it is an outlier', false],
                    ['Cannot determine without the covariance matrix', false],
                ],
            ],
            [
                'q' => "The LOOCV (Leave-One-Out Cross-Validation) resubstitution error rate in LDA measures:\n\n(A) How well the model fits the training data (optimistic)\n(B) How well the model generalises to new data (less biased)\n(C) The number of misclassified training observations\n(D) The within-group variance",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],

            // ── MAHALANOBIS DISTANCE: CALCULATION ────────────────────────
            [
                'q' => "Compute the squared Mahalanobis distance for x = [3, 4], μ = [1, 2], Σ⁻¹ = [[1, 0],[0, 1]] (identity):\n\nD² = (x−μ)ᵀ Σ⁻¹ (x−μ)",
                'opts' => [
                    ['(3−1) + (4−2) = 4', false],
                    ['(3−1)² + (4−2)² = 4 + 4 = 8', true],
                    ['√(4 + 4) = 2.83', false],
                    ['(3×4) − (1×2) = 10', false],
                ],
            ],
            [
                'q' => "Now compute D² for the same x = [3,4], μ = [1,2] but Σ⁻¹ = [[2, 0],[0, 0.5]]:\n\nD² = (x−μ)ᵀ Σ⁻¹ (x−μ) = [2,2] [[2,0],[0,0.5]] [2,2]ᵀ",
                'opts' => [
                    ['2×2 + 2×0.5 = 5', false],
                    ['2²×2 + 2²×0.5 = 8 + 2 = 10', true],
                    ['(2+2)×(2+0.5) = 10', false],
                    ['4 + 4 = 8', false],
                ],
            ],

            // ── CLUSTERING: MULTI-STEP ────────────────────────────────────
            [
                'q' => "k-Means iteration:\n\nInitial centroids: C1 = (0,0), C2 = (10,10)\nPoints: A=(1,1), B=(2,3), C=(9,8), D=(11,9)\n\nAfter the first assignment step, which points belong to C1?",
                'opts' => [
                    ['A and B (closer to (0,0) than (10,10))', true],
                    ['A, B, and C', false],
                    ['All four points', false],
                    ['C and D', false],
                ],
            ],
            [
                'q' => "After assigning A=(1,1) and B=(2,3) to C1, what is the updated centroid C1?",
                'opts' => [
                    ['(1, 1)', false],
                    ['(1.5, 2)', true],
                    ['(0, 0)', false],
                    ['(3, 4)', false],
                ],
            ],
            [
                'q' => "The Davies-Bouldin Index for clustering quality:\n\n(A) Measures how spread-out each cluster is relative to cluster separation — LOWER values indicate better clustering\n(B) Measures how many clusters exist — higher is better\n(C) Measures the proportion of variance explained\n(D) Is always between 0 and 1",
                'opts' => [
                    ['(B)', false],
                    ['(C)', false],
                    ['(D)', false],
                    ['(A)', true],
                ],
            ],
            [
                'q' => "Ward's linkage in hierarchical clustering merges the two clusters that minimise:\n\n(A) The maximum distance between any two points\n(B) The increase in total within-cluster sum of squares\n(C) The average distance between all pairs of points\n(D) The distance between the two cluster centroids",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],

            // ── CCA: MULTI-STEP ───────────────────────────────────────────
            [
                'q' => "In CCA, the canonical variates are defined as:\n\nU = aᵀX  and  V = bᵀY\n\nThe canonical correlation r* is maximized by choosing a and b as the solution to which generalised eigenvalue problem?",
                'opts' => [
                    ['Σ_XX a = λ a', false],
                    ['Σ_XX⁻¹ Σ_XY Σ_YY⁻¹ Σ_YX a = λ² a', true],
                    ['(Σ_XY − λI) a = 0', false],
                    ['Σ_XY a = λ Σ_YY b', false],
                ],
            ],
            [
                'q' => "CCA with 3 X-variables and 5 Y-variables produces:\n\n(A) 5 canonical correlations\n(B) 3 canonical correlations (= min(3,5))\n(C) 8 canonical correlations\n(D) 15 canonical correlations",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],

            // ── MDS: MULTI-STEP ───────────────────────────────────────────
            [
                'q' => "Classic (metric) MDS starts from a distance matrix D.\n\nStep 1: Double-centre D² to obtain matrix B.\nStep 2: Eigendecompose B = VΛVᵀ.\n\nThe coordinates of n points in k dimensions are given by:",
                'opts' => [
                    ['The first k rows of V', false],
                    ['Vₖ Λₖ^(1/2) — the first k eigenvectors scaled by the square root of their eigenvalues', true],
                    ['The diagonal of Λ directly', false],
                    ['V⁻¹ Λ', false],
                ],
            ],
            [
                'q' => "Kruskal's stress formula for non-metric MDS is:\n\nStress = √[ Σ(dᵢⱼ − d̂ᵢⱼ)² / Σ dᵢⱼ² ]\n\nwhere dᵢⱼ are the fitted distances and d̂ᵢⱼ are the monotone-transformed original dissimilarities.\n\nA stress value of 0.05 is generally considered:",
                'opts' => [
                    ['Unacceptable', false],
                    ['Excellent fit', true],
                    ['Poor fit (stress must be exactly 0)', false],
                    ['Moderate fit', false],
                ],
            ],

            // ── MULTIVARIATE REGRESSION: MULTI-STEP ──────────────────────
            [
                'q' => "In multiple regression with standardised predictors, the regression equation is:\n\nŶ = 0.60 X₁ + 0.25 X₂ − 0.10 X₃\n\nAll three predictors have VIF < 2. Which predictor has the strongest unique contribution to Y?",
                'opts' => [
                    ['X₃ (largest absolute value)', false],
                    ['X₁ (largest standardised coefficient = 0.60)', true],
                    ['X₂ (middle coefficient = 0.25)', false],
                    ['Cannot be determined without p-values', false],
                ],
            ],
            [
                'q' => "You are building a multivariate regression model and find that X₁ and X₂ have a correlation of 0.95. VIF for X₁ = 18.5.\n\nThe best remedy is:",
                'opts' => [
                    ['Add more predictor variables', false],
                    ['Remove one of the highly collinear predictors, combine them into a single index, or apply ridge regression', true],
                    ['Increase the sample size only', false],
                    ['Standardize the dependent variable', false],
                ],
            ],
            [
                'q' => "Partial regression plots (added variable plots) are used in multiple regression to:\n\n(A) Visualise the marginal relationship between one predictor and the outcome after removing the effects of all other predictors\n(B) Plot all predictors simultaneously\n(C) Check for multicollinearity between predictors\n(D) Compare different regression models",
                'opts' => [
                    ['(B)', false],
                    ['(C)', false],
                    ['(D)', false],
                    ['(A)', true],
                ],
            ],

            // ── MISC INTERMEDIATE ─────────────────────────────────────────
            [
                'q' => "Hotelling's T² statistic for testing H₀: μ = μ₀ with n observations and p variables is:\n\nT² = n (x̄ − μ₀)ᵀ S⁻¹ (x̄ − μ₀)\n\nn = 30, p = 3, x̄ − μ₀ = [1, 0, 2], S⁻¹ = I₃ (identity).\n\nWhat is T²?",
                'opts' => [
                    ['(1 + 0 + 2) × 30 = 90', false],
                    ['30 × (1² + 0² + 2²) = 30 × 5 = 150', true],
                    ['√(1² + 0² + 2²) = √5 ≈ 2.24', false],
                    ['1 × 0 × 2 × 30 = 0', false],
                ],
            ],
            [
                'q' => "T² is converted to an F-statistic for testing using:\n\nF = [(n−p) / (p(n−1))] × T²\n\nWith n=30, p=3, T²=150, what is F?",
                'opts' => [
                    ['[(30−3) / (3×29)] × 150 = [27/87] × 150 = 46.55', true],
                    ['[30 / (3×30)] × 150 = 50', false],
                    ['[150 / (30−3)] = 5.56', false],
                    ['[(30−1) / (3×30)] × 150 = 48.33', false],
                ],
            ],
            [
                'q' => "Structural Equation Modelling (SEM) extends path analysis by additionally allowing:\n\n(A) Only observed variables\n(B) Latent (unobserved) variables with measurement models alongside the structural (path) model\n(C) Only a single dependent variable\n(D) No error terms",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "The RMSEA (Root Mean Square Error of Approximation) fit index in SEM is:\n\n(A) A measure of absolute model fit — values < 0.05 indicate close fit\n(B) Always between 0 and 1\n(C) Equivalent to R²\n(D) Only used for factor analysis models",
                'opts' => [
                    ['(A)', true],
                    ['(B)', false],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "In a biplot from PCA, two original variable arrows (loadings) that point in nearly the same direction indicate:",
                'opts' => [
                    ['The two variables are uncorrelated', false],
                    ['The two variables are strongly positively correlated', true],
                    ['The two variables measure opposite constructs', false],
                    ['The variables cancel each other out in the PCA', false],
                ],
            ],
            [
                'q' => "GAP statistic is used in cluster analysis to determine the optimal number of clusters k by comparing:\n\n(A) The WCSS of the real data to that of a reference null distribution (uniformly distributed data)\n(B) The silhouette scores of different k values\n(C) The dendrogram height at different k cuts\n(D) The eigenvalues of the covariance matrix",
                'opts' => [
                    ['(A)', true],
                    ['(B)', false],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "In t-SNE, the algorithm minimises the KL divergence between:\n\n(A) Two Gaussian distributions\n(B) The high-dimensional pairwise similarity distribution and the low-dimensional Student-t pairwise similarity distribution\n(C) The data distribution and a uniform distribution\n(D) The original and reconstructed data matrices",
                'opts' => [
                    ['(A)', false],
                    ['(B)', true],
                    ['(C)', false],
                    ['(D)', false],
                ],
            ],
            [
                'q' => "Why does t-SNE use a Student-t distribution (heavy tails) in the low-dimensional space but a Gaussian in the high-dimensional space?",
                'opts' => [
                    ['To make the algorithm faster', false],
                    ['To alleviate the crowding problem — the heavy tails allow moderately distant points in high dimensions to be placed further apart in low dimensions, preventing all points from collapsing to the centre', true],
                    ['Because Gaussian distributions are undefined in 2D', false],
                    ['The choice of distribution does not affect the output', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 16 — Multivariate Analysis (Intermediate).");
    }
}