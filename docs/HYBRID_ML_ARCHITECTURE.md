# DataSensei Hybrid Machine Learning Architecture

## Purpose

The module contains two deliberately independent pipelines. Student training can use the same source CSV as a system benchmark, but it never writes to a system model record or system artifact directory.

### System ML pipeline

Built-in datasets and pretrained benchmark artifacts live under `storage/app/ml/system`. Their database records use `ml_models.pipeline_type = system`, `user_id = null`, `is_read_only = 1`, and immutable `model_versions` records. Students may preview or download the dataset, inspect benchmark metrics and charts, use a benchmark for predictions, and train a separate user-owned experiment from the same dataset. System models cannot be renamed, rolled back, deleted, or overwritten through student endpoints.

### User ML pipeline

Uploaded datasets live under `storage/app/ml/users/{user_id}/datasets/{dataset_uuid}`. Trained artifacts live under `storage/app/ml/users/{user_id}/models/{model_uuid}/v{number}`. Each successful training creates a new immutable `model_versions` row and directory. Activating an older version changes only `ml_models.current_version_id`; it does not overwrite any artifact. A model may be private or linked to a class, but ownership always remains with its student creator.

## Main folders

- `app/Services/HybridMl`: parsing, profiling, quality analysis, algorithm configuration, runner execution, storage, comparison, explanation, prediction, and access checks.
- `app/Jobs/ProcessMlTrainingJob.php`: asynchronous, unique training job.
- `resources/hybrid-ml/trusted_runner.py`: fixed Python training and prediction program. It never executes user-supplied code.
- `resources/hybrid-ml/build_system_assets.py`: deterministic builder for bundled datasets and benchmarks.
- `storage/app/ml/system`: read-only system CSV files, models, metadata, charts, and manifest.
- `storage/app/ml/users`: private user datasets, model versions, metadata, and charts.
- `resources/views/student/model-development`: dataset library, upload, preview, quality report, wizard, progress, model dashboard, comparison, prediction, version history, and printable report.
- `resources/views/instructor/model-development`: class-scoped instructor monitoring.

## Database model

- `datasets`: curated built-in dataset catalog.
- `user_datasets`: private or class-linked uploaded dataset records.
- `dataset_versions`: immutable source snapshots for both dataset pipelines.
- `ml_models`: stable logical model records; `pipeline_type` distinguishes system from user models.
- `model_versions`: immutable Joblib artifact versions, metrics, parameters, charts, and explanations.
- `training_jobs`: queue state, progress, validated configuration, results, and errors.
- `benchmark_models`: ranked benchmark mapping for built-in datasets.
- `prediction_logs`: ownership-aware prediction history.
- `quality_reports`: measured data-quality findings and recommendations.
- `algorithm_configs`: educational algorithm catalog and validated parameter definitions.

All JSON-like values use `LONGTEXT` with Laravel array casts for compatibility with the project’s older MySQL server.

## Upload and validation lifecycle

1. Laravel validates extension, MIME information, configured file size, and ownership/class association.
2. CSV is normalized to UTF-8 and its delimiter is detected. XLSX is read from the first worksheet only.
3. XLSX archives are checked for path traversal, excessive entries, unsafe expansion ratio, XML entity declarations, and formulas.
4. Headers must be present and unique. Row/column limits and minimum-row requirements are enforced.
5. Formula-like cells are neutralized to prevent spreadsheet injection when a canonical CSV is downloaded.
6. The profiler detects numeric, categorical, Boolean, date, and text columns, target candidates, identifier-like fields, and inconsistent mixed types.
7. The quality analyzer measures missing values, duplicate rows, empty/constant fields, outliers, skewness, class imbalance, high cardinality, high correlation, and type inconsistency.
8. Only the sanitized canonical CSV and measured metadata are stored.

## Training lifecycle

1. The student selects a built-in or owned uploaded dataset.
2. The wizard validates target, features, task, algorithm, split, random state, cross-validation, preprocessing, and hyperparameters against a server-side allowlist.
3. Laravel creates a `training_jobs` row and dispatches `ProcessMlTrainingJob` to the dedicated `machine_learning` database connection and `machine-learning` queue.
4. The worker invokes only `trusted_runner.py`, preferably inside a network-disabled, read-only Docker container with CPU, memory, PID, timeout, and capability limits.
5. Python constructs an sklearn preprocessing pipeline, trains the selected estimator, writes incremental progress, and creates metrics, charts, metadata, prediction schema, and a Joblib artifact in an isolated temporary directory.
6. Laravel validates the runner output, copies it into a user-specific model/version directory, and updates records transactionally.
7. Repeating an experiment with the same logical model creates `v2`, `v3`, and later versions without overwriting earlier versions.
8. A user model trained from a built-in dataset is compared first with the read-only benchmark using the same algorithm. When that benchmark does not exist, DataSensei uses the dataset’s primary benchmark and clearly treats the comparison as approximate.

## Algorithms

Classification includes Logistic Regression, Decision Tree, Random Forest, KNN, Gaussian Naive Bayes, SVM, and Gradient Boosting. Regression includes Linear Regression, Decision Tree Regressor, Random Forest Regressor, and Gradient Boosting Regressor. K-Means is available for optional clustering experiments.

The parameter catalog supports validated values such as `max_depth`, `n_estimators`, `learning_rate`, train/test split, `random_state`, and cross-validation. Unsupported keys and out-of-range values are rejected before a job is queued.

## Metrics and visualizations

Classification supports accuracy, weighted precision, weighted recall, weighted F1, ROC AUC when probabilities are available, average precision, confusion matrix, ROC curve, precision-recall curve, class distribution, learning curve, validation curve, feature influence, correlation heatmap, numeric distributions, and missing-value visualizations.

Regression supports MAE, MSE, RMSE, R², actual-vs-predicted, residual plot, learning curve, validation curve, feature influence, correlation heatmap, numeric distributions, and missing-value visualizations.

Clustering supports inertia, silhouette score, cluster sizes, cluster scatter, correlation heatmap, numeric distributions, and missing-value visualizations. Every generated chart is served through an authenticated ownership-checked endpoint and can be downloaded as PNG.

## Explainability

Explanations are deterministic and evidence-based. They use measured row count, quality score, missingness, type inconsistency, class balance, cross-validation variance, train/test gap, selected algorithm, hyperparameters, and feature influence. No external generative service is called.

## Security boundaries

- Student uploads are treated only as tabular data, never executable code.
- Model and dataset paths are hidden from API serialization and stored outside `public`.
- Every user dataset, training job, user model, version, visualization, and prediction is checked against the authenticated user or an authorized instructor/class scope.
- API input accepts logical identifiers, not arbitrary filesystem paths.
- System records are read-only and use a separate storage root.
- Python runs a trusted fixed entry point with a restricted parameter schema.
