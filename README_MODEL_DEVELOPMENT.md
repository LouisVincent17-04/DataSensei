# DataSensei Model Development Module

The Model Development module continues the student workflow after Exploratory Data Analysis (EDA).

## Student workflow

1. Select a built-in dataset.
2. Choose classification, regression, or clustering.
3. Select approved feature columns and a target for supervised tasks.
4. Configure a controlled training/testing split and preprocessing options.
5. Train one approved algorithm in the existing isolated Python sandbox.
6. Review task-appropriate evaluation metrics and charts.
7. Enter new values to generate reproducible predictions.

## Supported algorithms

Classification:
- Logistic Regression
- Decision Tree
- Random Forest
- K-Nearest Neighbors
- Gaussian Naïve Bayes

Regression:
- Linear Regression
- Ridge Regression
- Lasso Regression

Clustering:
- K-Means

## Evaluation output

Classification: Accuracy, weighted Precision, weighted Recall, weighted F1 Score, confusion matrix, and feature contribution.

Regression: MAE, MSE, RMSE, R², actual-versus-predicted plot, residual plot, and coefficients.

Clustering: Inertia, Silhouette Score, cluster sizes, cluster centers, and a two-feature cluster plot.

## Enforced scope limitations

- Built-in tabular datasets only.
- At most 1,000 rows and 12 selected features by default.
- No arbitrary Python source input in this module.
- No uploaded datasets, packages, model files, serialized estimators, deep learning, image models, or LLM training.
- Whitelisted algorithms and bounded hyperparameters only.
- CPU-only, network-disabled, memory-limited, process-limited, and time-limited Docker execution.
- At most 30 predictions per model run by default.
- Models are not serialized. Prediction requests deterministically retrain the saved configuration on the same built-in dataset.

## Installation

After copying the module files:

```bash
php artisan optimize:clear
php artisan migrate
```

Rebuild the Python runner because the Docker image now includes scikit-learn, SciPy, Joblib, and Threadpoolctl:

```bash
docker build --no-cache -t datasensei-python-runner:latest docker/python-runner
```

Then verify that the image exists:

```bash
docker images datasensei-python-runner
```

Do not run `php artisan migrate:fresh` on a database containing real users or project records.
