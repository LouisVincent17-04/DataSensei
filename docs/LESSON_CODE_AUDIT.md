# Lesson code audit (21 September 2026)

Every code example of the 24 lesson seeders that has a "Try in Compiler" button and goes to the Python IDE was run through the real sandbox runner (`docker/python-runner/datasensei_runner.py`) with numpy 2.4, pandas 3.0, matplotlib 3.11, scipy 1.17, scikit-learn 1.8, seaborn 0.13 and statsmodels 0.15. The 6,231 distinct snippets of the module library seeders were run the same way.

Before: 375 runnable lesson examples, 114 failed. After: 332 runnable, 0 fail (two ask for input() and work when answered); 43 examples that can never run in the sandbox no longer offer the button.

Re-run the audit any time with `scripts/audit-lesson-code.py`. Apply the new lesson text to an existing database with `php artisan lessons:sync-content` (never re-seed a module that learners have started: re-seeding deletes its lessons and their progress with them).

## 1. Sandbox fixes (no lesson text involved)

- `import pandas` failed for every lesson: pandas 3 opens `/usr/share/zoneinfo/UTC` while importing and the sandbox blocked it. scikit-learn, seaborn and statsmodels import pandas, so they failed too. Read-only time zone data is now allowed.
- A standby container that failed to preload pandas kept serving runs, which turned the real error into the confusing `partially initialized module 'pandas' ... _pandas_datetime_CAPI`. Such a container now never takes a run.
- KMeans, GaussianMixture, DBSCAN, PCA and friends failed with "blocked: open" / "blocked: ctypes.dlopen": threadpoolctl reads `/proc/self/maps` and opens the OpenMP/BLAS libraries. Allowed for installed library code only; a learner's own `open('/proc/self/maps')` is still blocked (tested).
- joblib printed "will operate in serial mode" into every scikit-learn run. Silenced by telling joblib up front that the sandbox has no shared memory.
- seaborn and statsmodels were not in the image (37 examples). Added, and all library versions are now pinned so a rebuild cannot silently change them again.
- Blocked paths/libraries are now named in the error message.

## 2. Code corrected in place

- Module3LessonsSeeder.php (block at original line 662): `boxplot(datasets, labels=` → `boxplot(datasets, tick_labels=`
- Module3LessonsSeeder.php (block at original line 796): `salaries.ptp()` → `np.ptp(salaries)`
- Module3LessonsSeeder.php (block at original line 1323): `import mean_squared_error, r2_score` → `import root_mean_squared_error, r2_score`; `mean_squared_error(y_te, y_pred_lr, squared=False)` → `root_mean_squared_error(y_te, y_pred_lr)`; `mean_squared_error(y_te, poly_pipe.predict(X_te), squared=Fa` → `root_mean_squared_error(y_te, poly_pipe.predict(X_te))`
- Module6LessonsSeeder.php (block at original line 1093): `N_mc     = 2_000` → `N_mc     = 400`
- Module8LessonsSeeder.php (block at original line 997): `fert_D], labels=[` → `fert_D], tick_labels=[`
- Module8LessonsSeeder.php (block at original line 1484): `boxplot(groups, labels=` → `boxplot(groups, tick_labels=`
- Module11LessonsSeeder.php (block at original line 878): `metropolis_hastings(n_samples=20000)` → `metropolis_hastings(n_samples=6000)`; `burnin = 2000` → `burnin = 1000`
- Module11LessonsSeeder.php (block at original line 1053): `def run_mcmc(n_samples=15000):` → `def run_mcmc(n_samples=5000):`; `burnin  = 3000` → `burnin  = 1000`
- Module14LessonsSeeder.php (block at original line 144): `from sklearn.linear_model import LogisticRegression` → `from sklearn.pipeline import make_pipeline`; `model = LogisticRegression(max_iter=10000).fit(X_train, y_tr` → `model = make_pipeline(StandardScaler(), LogisticRegression(max_iter=10`
- Module14LessonsSeeder.php (block at original line 195): `from sklearn.linear_model import LogisticRegression` → `from sklearn.pipeline import make_pipeline`; `model = LogisticRegression(max_iter=10000)` → `model = make_pipeline(StandardScaler(), LogisticRegression(max_iter=10`
- Module14LessonsSeeder.php (block at original line 580): `"Random Forest  500":   RandomForestClassifier(n_estimators=` → `"Random Forest  300":   RandomForestClassifier(n_estimators=300, rando`; `cross_val_score(m, X, y, cv=5, scoring='accuracy')` → `cross_val_score(m, X, y, cv=3, scoring='accuracy')`
- Module14LessonsSeeder.php (block at original line 965): `RandomForestClassifier(n_estimators=200, random_state=42)` → `RandomForestClassifier(n_estimators=60, random_state=42)`; `n_estimators=200, learning_rate=0.1,` → `n_estimators=60, learning_rate=0.1,`; `cross_val_score(m, X, y, cv=5)` → `cross_val_score(m, X, y, cv=3)`
- Module14LessonsSeeder.php (block at original line 1006): `GradientBoostingClassifier(n_estimators=100, random_state=42` → `GradientBoostingClassifier(n_estimators=40, random_state=42)`; `model, X, y, cv=5,` → `model, X, y, cv=3,`
- Module15LessonsSeeder.php (block at original line 417): `palette="Set2", ax=ax1)` → `hue="department", palette="Set2", legend=False, ax=ax1)`; `palette="Set2", inner="box", ax=ax2)` → `hue="department", palette="Set2", legend=False, inner="box", ax=ax2)`
- Module15LessonsSeeder.php (block at original line 864): `palette="Set3", ax=axes[1, 0])` → `hue="day", palette="Set3", legend=False, ax=axes[1, 0])`
- Module16LessonsSeeder.php (block at original line 255): `print(\n"═══ FIRST` → `print("\n═══ FIRST`
- Module16LessonsSeeder.php (block at original line 1305): `MDS(n_components=2, random_state=42, normalized_stress='auto` → `MDS(n_components=2, n_init=1, init='random', max_iter=120, random_stat`; `plt.cm.get_cmap('tab10')` → `plt.get_cmap('tab10')`
- Module20LessonsSeeder.php (block at original line 1040): `defaultdict(0.__class__)` → `defaultdict(int)`
- Module21LessonsSeeder.php (block at original line 851): `n_iter=1000` → `max_iter=1000`
- Module21LessonsSeeder.php (block at original line 1219): `n_init=5, random_state=42)` → `n_init=2, random_state=42)`
- Module24LessonsSeeder.php (block at original line 439): `self.size=(self.goal:=(3,3)) and 4` → `self.size=4`
- Module16LessonsSeeder.php: `__import__('scipy').stats...` (blocked by policy) → `from scipy import stats`; digits demo trimmed (MDS/t-SNE iterations).
- Module21LessonsSeeder.php: t-SNE demo uses 600 of the 1,797 digits.


## 3. Examples that depended on an earlier example (NameError)

These now carry the imports/definitions they need at the top, taken from the earlier example of the same lesson, so each one runs on its own.

- Module3LessonsSeeder.php: NUMPY — Vectorized Math (1 setup lines)
- Module3LessonsSeeder.php: NUMPY — Indexing & Boolean Masking (1 setup lines)
- Module3LessonsSeeder.php: NUMPY — Descriptive Statistics (1 setup lines)
- Module3LessonsSeeder.php: NUMPY — Random Number Generation (1 setup lines)
- Module3LessonsSeeder.php: PANDAS — DataFrame Creation (1 setup lines)
- Module3LessonsSeeder.php: PANDAS — Selection & Filtering (7 setup lines)
- Module3LessonsSeeder.php: PANDAS — Missing Data (1 setup lines)
- Module3LessonsSeeder.php: PANDAS — GroupBy Aggregation (1 setup lines)
- Module3LessonsSeeder.php: MATPLOTLIB — Multiple Subplots in a Grid (2 setup lines)
- Module3LessonsSeeder.php: SEABORN — Correlation Heatmap & Pairplot (1 setup lines)
- Module3LessonsSeeder.php: STATS — Key Distributions with scipy.stats (1 setup lines)
- Module3LessonsSeeder.php: STATS — t-Test, ANOVA, Chi-Square (2 setup lines)
- Module3LessonsSeeder.php: FEATURE ENGINEERING — Creating Powerful Features (2 setup lines)
- Module3LessonsSeeder.php: SKLEARN — Classification Pipeline (10 setup lines)
- Module3LessonsSeeder.php: SKLEARN — Regression & Evaluation Metrics (1 setup lines)
- Module3LessonsSeeder.php: PCA — Dimensionality Reduction (1 setup lines)
- Module3LessonsSeeder.php: TIME SERIES — Seasonal Decomposition & Visualization (9 setup lines)
- Module6LessonsSeeder.php: PYTHON — Monte Carlo Uncertainty Analysis (10 setup lines)
- Module6LessonsSeeder.php: PYTHON — t-Test: Comparing Two Queue Configurations (19 setup lines)
- Module7LessonsSeeder.php: PYTHON — Floyd's Cycle Detection (Tortoise & Hare) (54 setup lines)
- Module7LessonsSeeder.php: PYTHON — Full BST: Insert, Search, Delete, Range Query (8 setup lines)
- Module11LessonsSeeder.php: PYTHON — MCMC Diagnostics (35 setup lines)
- Module11LessonsSeeder.php: PYTHON — Posterior Predictive Check (40 setup lines)
- Module12LessonsSeeder.php: PYTHON — Forecast Evaluation Framework (6 setup lines)
- Module12LessonsSeeder.php: PYTHON — Additive vs. Multiplicative: Quick Test (11 setup lines)
- Module12LessonsSeeder.php: PYTHON — STL Decomposition (11 setup lines)
- Module12LessonsSeeder.php: PYTHON — ADF Test Before and After Differencing (6 setup lines)
- Module12LessonsSeeder.php: PYTHON — Log Transform + Differencing for Variance Stabilisation (12 setup lines)
- Module12LessonsSeeder.php: REFERENCE — ACF/PACF Identification Guide (3 setup lines)
- Module12LessonsSeeder.php: PYTHON — Holt's Linear Trend & Damped Trend Comparison (3 setup lines)
- Module12LessonsSeeder.php: PYTHON — Holt-Winters Additive Model (4 setup lines)
- Module12LessonsSeeder.php: PYTHON — AIC-Based ARIMA Grid Search (2 setup lines)
- Module12LessonsSeeder.php: PYTHON — Bias Detection with Mean Error (ME) (4 setup lines)
- Module12LessonsSeeder.php: PYTHON — Comparing Two Models with Walk-Forward CV (8 setup lines)
- Module13LessonsSeeder.php: PYTHON — Genetic Algorithm for Continuous Optimization (42 setup lines)
- Module14LessonsSeeder.php: PYTHON — Ridge vs Lasso Regularization (6 setup lines)
- Module14LessonsSeeder.php: PYTHON — Confusion Matrix & Classification Report (10 setup lines)
- Module15LessonsSeeder.php: PYTHON — Saving Charts (1 setup lines)
- Module19LessonsSeeder.php: PYTHON — Unsupervised Learning: K-Means Clustering (2 setup lines)
- Module19LessonsSeeder.php: PYTHON — Overfitting vs. Underfitting: Depth vs. Accuracy (9 setup lines)
- Module19LessonsSeeder.php: PYTHON — Sentiment Classification with TF-IDF + Logistic Regression (1 setup lines)
- Module21LessonsSeeder.php: PYTHON — Elbow Method + Silhouette Score (8 setup lines)
- Module21LessonsSeeder.php: PYTHON — Customer Segmentation with K-Means (2 setup lines)
- Module21LessonsSeeder.php: PYTHON — k-Distance Plot for eps Selection (6 setup lines)
- Module21LessonsSeeder.php: PYTHON — PCA with n_components as Variance Threshold (2 setup lines)
- Module21LessonsSeeder.php: PYTHON — Anomaly Score Distribution (22 setup lines)
- Module21LessonsSeeder.php: PYTHON — BIC/AIC for GMM Component Selection (3 setup lines)
- Module24LessonsSeeder.php: PYTHON — Q-Learning from Scratch (11 setup lines)
- Module24LessonsSeeder.php: PYTHON — REINFORCE Algorithm from Scratch (11 setup lines)
- Module24LessonsSeeder.php: PYTHON — A2C Core Update Loop (42 setup lines)
- Module10LessonsSeeder.php: the two `customers`/`orders` examples create their sample SQLite tables first.


## 4. Reference only (button replaced by a plain note)

- Module10LessonsSeeder.php: PYTHON — SQLAlchemy + pandas. Reference example. Needs sqlalchemy, which the practice sandbox does not include.
- Module10LessonsSeeder.php: PYTHON — MongoDB (Document Store). Reference example. Needs pymongo, which the practice sandbox does not include.
- Module10LessonsSeeder.php: PYTHON — Redis Key-Value Cache. Reference example. Needs redis, which the practice sandbox does not include.
- Module12LessonsSeeder.php: PYTHON — Prophet: Basic Fit & Forecast. Reference example. Needs prophet, which the practice sandbox does not include.
- Module15LessonsSeeder.php: PYTHON — Plotly Express Interactive Charts. Reference example. Needs plotly, which the practice sandbox does not include.
- Module15LessonsSeeder.php: PYTHON — Plotly Interactive Bar & Line. Reference example. Needs plotly, which the practice sandbox does not include.
- Module17LessonsSeeder.php: PYTHON — Environment Check. Reference example. Needs tensorflow, which the practice sandbox does not include.
- Module17LessonsSeeder.php: PYTHON — Your First Neural Network (3 Lines). Reference example. Needs tensorflow, which the practice sandbox does not include.
- Module17LessonsSeeder.php: PYTHON — MLP on MNIST. Reference example. Needs tensorflow, which the practice sandbox does not include.
- Module17LessonsSeeder.php: PYTHON — PyTorch Autograd (Automatic Differentiation). Reference example. Needs torch, which the practice sandbox does not include.
- Module17LessonsSeeder.php: PYTHON — SGD vs Adam Comparison. Reference example. Needs tensorflow, which the practice sandbox does not include.
- Module17LessonsSeeder.php: PYTHON — Learning Rate Schedules in Keras. Reference example. Needs tensorflow, which the practice sandbox does not include.
- Module17LessonsSeeder.php: PYTHON — Dropout Regularization. Reference example. Needs tensorflow, which the practice sandbox does not include.
- Module17LessonsSeeder.php: PYTHON — BatchNorm + L2 Regularization. Reference example. Needs tensorflow, which the practice sandbox does not include.
- Module17LessonsSeeder.php: PYTHON — CNN on CIFAR-10. Reference example. Needs tensorflow, which the practice sandbox does not include.
- Module17LessonsSeeder.php: PYTHON — Transfer Learning with MobileNetV2. Reference example. Needs tensorflow, which the practice sandbox does not include.
- Module17LessonsSeeder.php: PYTHON — Sentiment Analysis with LSTM. Reference example. Needs tensorflow, which the practice sandbox does not include.
- Module17LessonsSeeder.php: PYTHON — LSTM Time Series Forecasting. Reference example. Needs tensorflow, which the practice sandbox does not include.
- Module17LessonsSeeder.php: PYTHON — Sentiment Classification with DistilBERT. Reference example. Needs transformers, which the practice sandbox does not include.
- Module17LessonsSeeder.php: PYTHON — Variational Autoencoder (VAE) on MNIST. Reference example. Needs tensorflow, which the practice sandbox does not include.
- Module17LessonsSeeder.php: PYTHON — Simple GAN Architecture for MNIST. Reference example. Needs tensorflow, which the practice sandbox does not include.
- Module19LessonsSeeder.php: PYTHON — Building & Training an MLP with Keras. Reference example. Needs tensorflow, which the practice sandbox does not include.
- Module19LessonsSeeder.php: PYTHON — Building a CNN for MNIST Digit Classification. Reference example. Needs tensorflow, which the practice sandbox does not include.
- Module19LessonsSeeder.php: PYTHON — Transfer Learning with MobileNetV2. Reference example. Needs tensorflow, which the practice sandbox does not include.
- Module19LessonsSeeder.php: PYTHON — GAN Architecture: Generator & Discriminator with Keras. Reference example. Needs tensorflow, which the practice sandbox does not include.
- Module19LessonsSeeder.php: PYTHON — RL Environment: OpenAI Gym CartPole. Reference example. Needs gymnasium, which the practice sandbox does not include.
- Module19LessonsSeeder.php: PYTHON — Feature Importance with SHAP Values. Reference example. Needs shap, which the practice sandbox does not include.
- Module21LessonsSeeder.php: PYTHON — Autoencoder for Anomaly Detection (Keras). Reference example. Needs tensorflow, which the practice sandbox does not include.
- Module22LessonsSeeder.php: BASH — HDFS Core Commands. Shell commands. Run them in a terminal.
- Module22LessonsSeeder.php: PYTHON — Interacting with AWS S3 using boto3. Reference example. Needs boto3, which the practice sandbox does not include.
- Module22LessonsSeeder.php: PYTHON — PySpark: DataFrame Operations & Spark SQL. Reference example. Needs pyspark, which the practice sandbox does not include.
- Module22LessonsSeeder.php: PYTHON — Kafka Producer & Consumer with kafka-python. Reference example. Needs kafka, which the practice sandbox does not include.
- Module22LessonsSeeder.php: BASH — Kafka Admin: Creating Topics & Inspecting Consumer Lag. Shell commands. Run them in a terminal.
- Module22LessonsSeeder.php: PYTHON — BigQuery: Python Client, Cost Estimation & Partitioned Tables. Reference example. Needs google, which the practice sandbox does not include.
- Module22LessonsSeeder.php: PYTHON — Airflow DAG: Daily Sales Pipeline with Sensors & Operators. Reference example. Needs airflow, which the practice sandbox does not include.
- Module22LessonsSeeder.php: PYTHON — Delta Lake: ACID Writes, MERGE & Time Travel with PySpark. Reference example. Needs pyspark, which the practice sandbox does not include.
- Module22LessonsSeeder.php: PYTHON — AWS IAM: Least-Privilege Role Policy & S3 Bucket Hardening. Reference example. Needs boto3, which the practice sandbox does not include.
- Module22LessonsSeeder.php: PYTHON — MLflow: Experiment Tracking, Model Registry & Serving. Reference example. Needs mlflow, which the practice sandbox does not include.
- Module22LessonsSeeder.php: PYTHON — Serving an MLflow Model via FastAPI REST Endpoint. Reference example. Needs fastapi, which the practice sandbox does not include.
- Module22LessonsSeeder.php: PYTHON — CSV vs Parquet: File Size & Read Speed Comparison. Reference example. It writes a 5-million-row file and needs pyarrow, so run it on your own machine.
- Module12 (prophet), Module17 (tensorflow) and Module22 (pyspark) continuation examples, for the same reason.


## 5. Module library seeders

All Python snippets run. The 540 snippets of the two database modules are SQL but were labelled "Python Example" and sent to the Python compiler (always `SyntaxError`). The four module viewers now label them "SQL Example" and open them in the SQL sandbox together with the practice tables they query.
