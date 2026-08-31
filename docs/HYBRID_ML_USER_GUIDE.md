# Student Guide: Model Development

## Built-in learning workflow

Open **Model Development**, choose a dataset in the Built-in Dataset Library, and review its source, feature list, detected data types, preview, target, quality score, and benchmark table. Open a benchmark to inspect its metrics, charts, feature influence, and prediction behavior. Select **Train Your Version** to create a separate user model from the same CSV. The system benchmark remains unchanged.

Ten datasets are provided: Iris, Titanic, Wine Quality, Breast Cancer Wisconsin, Customer Churn, Student Performance, Housing Prices, Sales Forecast, Diabetes, and Heart Disease. Dataset metadata identifies whether a dataset is sourced or generated for educational use.

## Uploaded dataset workflow

Upload CSV or XLSX from the Model Development page. DataSensei rejects unreadable files, invalid encodings, duplicate or missing headers, empty datasets, excessive rows or columns, unsafe XLSX archives, and XLSX formulas. Formula-like spreadsheet cells are neutralized before the canonical CSV can be downloaded.

Review the quality report before training. It identifies missing values, duplicates, empty and constant fields, inconsistent data types, outliers, class imbalance, high-cardinality fields, high correlation, skewness, and recommended preprocessing. The score is guidance, not proof that a dataset is scientifically valid.

## Six-step training wizard

1. Confirm the selected built-in or uploaded dataset.
2. Choose a target and feature columns.
3. Confirm classification, regression, or optional clustering.
4. Select an algorithm and read why it may or may not fit the measured dataset.
5. Configure validated hyperparameters, train/test split, random state, cross-validation, and preprocessing.
6. Review and enqueue the experiment.

Classification includes Logistic Regression, Decision Tree, Random Forest, KNN, Naive Bayes, SVM, and Gradient Boosting. Regression includes Linear Regression, Decision Tree Regressor, Random Forest Regressor, and Gradient Boosting Regressor. K-Means is available for clustering.

## Training and versions

Training runs asynchronously on the machine-learning queue. The progress page reports the current stage. Closing the page does not cancel a queued job. A successful run creates a new model version. Training again with the same model name, dataset, algorithm, and class scope creates `v2`, `v3`, and later versions without overwriting earlier files. Use Version History to activate an older ready version.

## Evaluation and comparison

Classification reports accuracy, precision, recall, F1, ROC AUC when available, average precision, and confusion matrix. Regression reports MAE, MSE, RMSE, and R². Clustering reports inertia and silhouette score.

A user model trained from a built-in dataset is compared first with the read-only benchmark using the same algorithm. If that benchmark was not pretrained, DataSensei falls back to the dataset’s primary benchmark. Better, worse, and equal labels account for whether a metric should be maximized or minimized. Differences can still result from preprocessing, random sampling, cross-validation, and hyperparameters.

## Explainability and charts

The model dashboard explains measured overfitting risk, class imbalance, small-sample concerns, cross-validation stability, training time, and influential features. Available charts may include confusion matrix, ROC, precision-recall, learning and validation curves, feature importance, correlation, distributions, missing-value views, class distribution, actual-vs-predicted, residuals, and cluster plots. Each chart can be downloaded as PNG.

## Predictions

Open a ready model and complete the Prediction Console. Classification can display class probabilities and confidence. Each prediction is stored with the selected version, sanitized input, result, evidence-based explanation, and latency. System benchmarks may be used for learning predictions, but their artifacts remain read-only.
