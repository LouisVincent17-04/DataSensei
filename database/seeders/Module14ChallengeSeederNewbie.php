<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;

class Module14ChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (!$category) {
            $this->command->error("Newbie category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        $this->command->info("Creating Module 14 — Supervised Learning (Newbie)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Machine Learning 1: Supervised Learning',
            'description'           => 'Learn the very basics of supervised machine learning — what it is, how it works, and the key ideas behind training a model to make predictions. No coding experience required!',
            'time_limit_seconds'    => 900,
            'base_xp'               => 500,
            'order_index'           => 14,
        ]);

        $this->command->info("Seeding 50 newbie-friendly supervised learning questions...");

        $qaData = [

            // ── WHAT IS MACHINE LEARNING ──────────────────────────────────
            [
                'q' => 'What is machine learning?',
                'opts' => [
                    ['A robot that thinks like a human', false],
                    ['A way for computers to learn patterns from data without being explicitly programmed for every rule', true],
                    ['A programming language like Python or Java', false],
                    ['A type of database for storing large files', false],
                ],
            ],
            [
                'q' => 'What is supervised learning?',
                'opts' => [
                    ['A type of learning where the computer figures things out with no data at all', false],
                    ['Training a model using labeled examples — where the correct answer is already known for each input', true],
                    ['A model that learns by playing games against itself', false],
                    ['Learning that happens without any training process', false],
                ],
            ],
            [
                'q' => 'In supervised learning, what does "labeled data" mean?',
                'opts' => [
                    ['Data that has been sorted alphabetically', false],
                    ['Data where each example already has the correct answer (label) attached to it', true],
                    ['Data stored in a spreadsheet with colored tabs', false],
                    ['Data that has been encrypted for security', false],
                ],
            ],
            [
                'q' => 'Which of the following is the best real-world example of supervised learning?',
                'opts' => [
                    ['A model that groups customers into unknown clusters with no labels', false],
                    ['A spam filter trained on emails already marked as "spam" or "not spam"', true],
                    ['A model that discovers hidden patterns in unlabeled data', false],
                    ['A self-driving car that explores roads it has never seen', false],
                ],
            ],
            [
                'q' => 'What are the two main types of supervised learning tasks?',
                'opts' => [
                    ['Searching and sorting', false],
                    ['Classification and regression', true],
                    ['Clustering and compression', false],
                    ['Training and testing', false],
                ],
            ],

            // ── CLASSIFICATION VS REGRESSION ──────────────────────────────
            [
                'q' => 'What is a classification task in machine learning?',
                'opts' => [
                    ['Predicting a number, like tomorrow\'s temperature', false],
                    ['Predicting which category or class an input belongs to', true],
                    ['Finding groups in data without labels', false],
                    ['Compressing data to save storage space', false],
                ],
            ],
            [
                'q' => 'What is a regression task in machine learning?',
                'opts' => [
                    ['Predicting a discrete category label', false],
                    ['Predicting a continuous numerical value', true],
                    ['Grouping similar data points together', false],
                    ['Removing outliers from a dataset', false],
                ],
            ],
            [
                'q' => 'Which of the following is a CLASSIFICATION problem?',
                'opts' => [
                    ['Predicting the price of a house', false],
                    ['Predicting how many customers will visit a shop tomorrow', false],
                    ['Predicting whether a tumor is malignant or benign', true],
                    ['Predicting the weight of a package', false],
                ],
            ],
            [
                'q' => 'Which of the following is a REGRESSION problem?',
                'opts' => [
                    ['Detecting if an email is spam or not spam', false],
                    ['Classifying a photo as a cat or dog', false],
                    ['Predicting the final exam score of a student based on study hours', true],
                    ['Identifying the language of a text document', false],
                ],
            ],
            [
                'q' => 'A model predicts the exact selling price of a car. This is an example of:',
                'opts' => [
                    ['Classification', false],
                    ['Clustering', false],
                    ['Regression', true],
                    ['Reinforcement learning', false],
                ],
            ],

            // ── TRAINING DATA & LABELS ────────────────────────────────────
            [
                'q' => 'In machine learning, what is a "feature"?',
                'opts' => [
                    ['The final answer or prediction the model makes', false],
                    ['An input variable used to make a prediction', true],
                    ['A type of algorithm used for training', false],
                    ['A graph showing model performance', false],
                ],
            ],
            [
                'q' => 'In machine learning, what is a "label" (also called target or output)?',
                'opts' => [
                    ['An input variable given to the model', false],
                    ['The correct answer that the model is trying to predict', true],
                    ['A column used for normalization', false],
                    ['The name of the dataset', false],
                ],
            ],
            [
                'q' => 'You are building a model to predict if a patient has diabetes. The patient\'s age, weight, and blood sugar are all examples of:',
                'opts' => [
                    ['Labels', false],
                    ['Features (inputs)', true],
                    ['Predictions', false],
                    ['Algorithms', false],
                ],
            ],
            [
                'q' => 'In the diabetes prediction example, whether the patient HAS or DOES NOT HAVE diabetes is the:',
                'opts' => [
                    ['Feature', false],
                    ['Algorithm', false],
                    ['Label (target/output)', true],
                    ['Hyperparameter', false],
                ],
            ],
            [
                'q' => 'What do we call the full collection of examples (rows) used to train a machine learning model?',
                'opts' => [
                    ['The algorithm', false],
                    ['The training dataset', true],
                    ['The prediction output', false],
                    ['The confusion matrix', false],
                ],
            ],

            // ── TRAIN / TEST SPLIT ────────────────────────────────────────
            [
                'q' => 'Why do we split data into a training set and a test set?',
                'opts' => [
                    ['To make the dataset smaller so it loads faster', false],
                    ['To train the model on one portion and evaluate how well it generalizes on unseen data', true],
                    ['To remove duplicate rows from the data', false],
                    ['Because machine learning algorithms only work on small datasets', false],
                ],
            ],
            [
                'q' => 'The training set is used to:',
                'opts' => [
                    ['Measure how well the model performs on new data', false],
                    ['Teach the model by showing it labeled examples', true],
                    ['Store the final predictions', false],
                    ['Display charts and graphs', false],
                ],
            ],
            [
                'q' => 'The test set is used to:',
                'opts' => [
                    ['Train the model so it learns patterns', false],
                    ['Tune hyperparameters during training', false],
                    ['Evaluate the trained model\'s performance on data it has never seen before', true],
                    ['Store raw, uncleaned data', false],
                ],
            ],
            [
                'q' => 'A common train/test split ratio is:',
                'opts' => [
                    ['50% train / 50% test', false],
                    ['80% train / 20% test', true],
                    ['10% train / 90% test', false],
                    ['99% train / 1% test', false],
                ],
            ],
            [
                'q' => 'If you accidentally train your model on the test set, what is the main problem?',
                'opts' => [
                    ['The model will be too slow to run', false],
                    ['You will get an unfair, overly optimistic evaluation — the model already "saw" the answers', true],
                    ['The model will refuse to make predictions', false],
                    ['The training will take longer', false],
                ],
            ],

            // ── OVERFITTING & UNDERFITTING ────────────────────────────────
            [
                'q' => 'What is "overfitting" in machine learning?',
                'opts' => [
                    ['When a model performs poorly on both training and test data', false],
                    ['When a model learns the training data so well — including noise — that it performs poorly on new data', true],
                    ['When the model is too simple to capture any patterns', false],
                    ['When a dataset has too many features', false],
                ],
            ],
            [
                'q' => 'What is "underfitting" in machine learning?',
                'opts' => [
                    ['When a model memorizes the training data perfectly', false],
                    ['When a model is too simple to capture the underlying patterns in the data', true],
                    ['When a model performs perfectly on the test set but not on training', false],
                    ['When the dataset is too large for the algorithm', false],
                ],
            ],
            [
                'q' => 'An overfitted model will typically:',
                'opts' => [
                    ['Perform well on training data but poorly on test data', true],
                    ['Perform poorly on both training and test data', false],
                    ['Perform well on test data but poorly on training data', false],
                    ['Have a very simple decision boundary', false],
                ],
            ],
            [
                'q' => 'An underfitted model will typically:',
                'opts' => [
                    ['Score perfectly on the training set', false],
                    ['Perform poorly on both training and test data because it is too simple', true],
                    ['Only make errors on the test set', false],
                    ['Have very complex decision boundaries', false],
                ],
            ],
            [
                'q' => 'What do we call the ideal balance between underfitting and overfitting?',
                'opts' => [
                    ['Data balance', false],
                    ['Generalization', true],
                    ['Normalization', false],
                    ['Regularization overflow', false],
                ],
            ],

            // ── LINEAR REGRESSION ─────────────────────────────────────────
            [
                'q' => 'What does linear regression do?',
                'opts' => [
                    ['It groups data into clusters', false],
                    ['It draws a straight line through data to predict a continuous value', true],
                    ['It classifies data into categories', false],
                    ['It removes outliers from a dataset', false],
                ],
            ],
            [
                'q' => 'In linear regression, the formula is: ŷ = mx + b. What does "m" represent?',
                'opts' => [
                    ['The y-intercept — where the line crosses the y-axis', false],
                    ['The slope — how much y changes for each 1-unit increase in x', true],
                    ['The prediction error', false],
                    ['The number of data points', false],
                ],
            ],
            [
                'q' => 'In the formula ŷ = mx + b, what does "b" represent?',
                'opts' => [
                    ['The slope of the line', false],
                    ['The predicted output value', false],
                    ['The y-intercept — the value of ŷ when x = 0', true],
                    ['The error term', false],
                ],
            ],
            [
                'q' => 'Linear regression is best suited for which type of output?',
                'opts' => [
                    ['A category like "yes" or "no"', false],
                    ['A continuous number like price or temperature', true],
                    ['An image classification result', false],
                    ['A cluster label', false],
                ],
            ],
            [
                'q' => 'What does it mean to "fit" a linear regression model to data?',
                'opts' => [
                    ['Visualizing the data on a scatter plot', false],
                    ['Finding the line (values of m and b) that best matches the data by minimizing errors', true],
                    ['Removing all outliers from the dataset', false],
                    ['Converting the data into a different format', false],
                ],
            ],

            // ── LOGISTIC REGRESSION ───────────────────────────────────────
            [
                'q' => 'Despite its name, logistic regression is used for:',
                'opts' => [
                    ['Predicting continuous values like house prices', false],
                    ['Classification — predicting which class an input belongs to', true],
                    ['Clustering data into groups', false],
                    ['Reducing the number of features', false],
                ],
            ],
            [
                'q' => 'Logistic regression outputs a value between 0 and 1. What does this value represent?',
                'opts' => [
                    ['The exact numerical prediction', false],
                    ['A probability — how likely an input belongs to the positive class', true],
                    ['The distance from the decision boundary', false],
                    ['The number of training iterations completed', false],
                ],
            ],
            [
                'q' => 'If a logistic regression model outputs 0.85, what does this typically mean?',
                'opts' => [
                    ['The model is 85% complete', false],
                    ['There is an 85% probability the input belongs to the positive class', true],
                    ['The model made 85 errors', false],
                    ['The output feature has a value of 85', false],
                ],
            ],

            // ── DECISION TREES ────────────────────────────────────────────
            [
                'q' => 'What is a decision tree?',
                'opts' => [
                    ['A tree diagram showing a company\'s organizational structure', false],
                    ['A model that makes predictions by asking a series of yes/no questions about the data', true],
                    ['A neural network with tree-shaped layers', false],
                    ['A chart that shows model accuracy over time', false],
                ],
            ],
            [
                'q' => 'In a decision tree, what is the very first question asked (top of the tree)?',
                'opts' => [
                    ['The leaf node', false],
                    ['The root node', true],
                    ['The branch node', false],
                    ['The terminal node', false],
                ],
            ],
            [
                'q' => 'In a decision tree, what is a "leaf node"?',
                'opts' => [
                    ['A question that splits the data', false],
                    ['The starting question at the top of the tree', false],
                    ['A final decision point that gives the prediction or class label', true],
                    ['A node that connects to more than two branches', false],
                ],
            ],
            [
                'q' => 'Decision trees can be used for:',
                'opts' => [
                    ['Only regression tasks', false],
                    ['Only classification tasks', false],
                    ['Both classification and regression tasks', true],
                    ['Only unsupervised learning', false],
                ],
            ],

            // ── K-NEAREST NEIGHBORS ───────────────────────────────────────
            [
                'q' => 'How does the K-Nearest Neighbors (KNN) algorithm classify a new data point?',
                'opts' => [
                    ['It draws a straight line and sees which side the point falls on', false],
                    ['It finds the K most similar (nearest) training examples and uses their labels to vote on the prediction', true],
                    ['It calculates the average of all training data', false],
                    ['It randomly assigns a class label', false],
                ],
            ],
            [
                'q' => 'In KNN, what does the "K" represent?',
                'opts' => [
                    ['The number of features in the dataset', false],
                    ['The number of nearest neighbors to look at when making a prediction', true],
                    ['The number of training epochs', false],
                    ['The kernel type used in the algorithm', false],
                ],
            ],
            [
                'q' => 'If K = 1 in KNN, what happens?',
                'opts' => [
                    ['The model predicts using all training points', false],
                    ['The model uses only the single nearest neighbor to make the prediction', true],
                    ['The model averages all neighbor predictions', false],
                    ['The model becomes a linear classifier', false],
                ],
            ],

            // ── MODEL ACCURACY & EVALUATION ───────────────────────────────
            [
                'q' => 'What is model accuracy?',
                'opts' => [
                    ['The number of features used by the model', false],
                    ['The percentage of predictions the model gets correct', true],
                    ['The speed at which a model makes predictions', false],
                    ['The amount of training data used', false],
                ],
            ],
            [
                'q' => 'A model makes 100 predictions and gets 90 correct. What is its accuracy?',
                'opts' => [
                    ['0.09', false],
                    ['90%', true],
                    ['10%', false],
                    ['9', false],
                ],
            ],
            [
                'q' => 'What is a confusion matrix used for?',
                'opts' => [
                    ['To confuse the model during training', false],
                    ['To show how many predictions were correct or incorrect for each class', true],
                    ['To store the training data', false],
                    ['To visualize how features relate to each other', false],
                ],
            ],
            [
                'q' => 'In a binary confusion matrix, what is a "True Positive"?',
                'opts' => [
                    ['The model predicted negative and was correct', false],
                    ['The model predicted positive but was wrong', false],
                    ['The model predicted positive and was correct', true],
                    ['The model predicted negative but was wrong', false],
                ],
            ],
            [
                'q' => 'In a binary confusion matrix, what is a "False Positive"?',
                'opts' => [
                    ['The model predicted positive and was correct', false],
                    ['The model predicted positive but the actual answer was negative (a false alarm)', true],
                    ['The model predicted negative and was correct', false],
                    ['The model predicted negative but the actual answer was positive', false],
                ],
            ],

            // ── RANDOM FORESTS & ENSEMBLE ─────────────────────────────────
            [
                'q' => 'What is a Random Forest?',
                'opts' => [
                    ['A single very deep decision tree', false],
                    ['A collection of many decision trees that vote together to make a prediction', true],
                    ['A neural network with random weights', false],
                    ['A forest-themed data visualization tool', false],
                ],
            ],
            [
                'q' => 'Why is a Random Forest often more accurate than a single decision tree?',
                'opts' => [
                    ['Because it uses more features than a single tree can', false],
                    ['Because many diverse trees voting together reduces errors and overfitting', true],
                    ['Because it trains faster on large datasets', false],
                    ['Because it does not require labeled data', false],
                ],
            ],

            // ── GENERAL SUPERVISED LEARNING CONCEPTS ─────────────────────
            [
                'q' => 'What is the goal of training a supervised learning model?',
                'opts' => [
                    ['To memorize the training data perfectly', false],
                    ['To find a function that accurately maps inputs to outputs, and generalizes to new data', true],
                    ['To create the largest possible dataset', false],
                    ['To eliminate all errors during testing', false],
                ],
            ],
            [
                'q' => 'What does "generalization" mean in machine learning?',
                'opts' => [
                    ['The ability of a model to perform well on data it has not seen before', true],
                    ['The process of training a model on all available data', false],
                    ['Using general-purpose algorithms instead of specialized ones', false],
                    ['Predicting the same output for all inputs', false],
                ],
            ],
            [
                'q' => 'What is a "hyperparameter" in machine learning?',
                'opts' => [
                    ['A parameter learned automatically from the training data', false],
                    ['A setting or configuration you choose BEFORE training that controls how the model learns', true],
                    ['A feature with a very high correlation to the target', false],
                    ['A type of activation function in neural networks', false],
                ],
            ],
            [
                'q' => 'In KNN, the value of K is an example of:',
                'opts' => [
                    ['A feature', false],
                    ['A label', false],
                    ['A hyperparameter', true],
                    ['A training sample', false],
                ],
            ],
            [
                'q' => 'Which of the following best describes "cross-validation"?',
                'opts' => [
                    ['Training on the test set to improve accuracy', false],
                    ['A technique where data is split into multiple folds so the model is trained and evaluated several times on different subsets', true],
                    ['Comparing two different datasets from different sources', false],
                    ['Removing cross-shaped outliers from the data', false],
                ],
            ],
            [
                'q' => 'What does "feature engineering" mean?',
                'opts' => [
                    ['Building a physical machine that collects features', false],
                    ['Creating, selecting, or transforming input variables to improve model performance', true],
                    ['Designing the output layer of a neural network', false],
                    ['Sorting features in alphabetical order', false],
                ],
            ],
            [
                'q' => 'Which of the following is NOT a supervised learning algorithm?',
                'opts' => [
                    ['Linear Regression', false],
                    ['Logistic Regression', false],
                    ['K-Means Clustering', true],
                    ['Decision Tree', false],
                ],
            ],

        ];

        foreach ($qaData as $data) {
            $question = ChallengeQuestion::create([
                'challenge_id'  => $challenge->id,
                'question_text' => $data['q'],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 14 — Supervised Learning (Newbie).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Newbie");
    }
}