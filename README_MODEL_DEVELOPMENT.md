# DataSensei Model Development Module

Model Development picks up where Exploratory Data Analysis (EDA) ends. Students build a real scikit-learn model in ten guided steps. They don't write any code, and every step explains itself in plain language.

## The ten-step beginner roadmap

| Phase   | Step | Screen                          | What the student does                                                                 |
|---------|------|---------------------------------|---------------------------------------------------------------------------------------|
| Prepare | 1    | Dataset library / preview       | Picks a built-in or uploaded dataset and checks a plain-language readiness checklist. |
| Prepare | 2    | What do you want to predict?    | Picks the target. A live "target insight" panel explains the column and suggests a problem type. Choosing "No target" switches to clustering. |
| Prepare | 3    | Which columns can the model use?| Ticks features, using Select all / Clear / Use recommended. The page explains why ID, empty, target, or (for clustering) non-numeric columns are locked. |
| Prepare | 4    | What kind of answer is it?      | Confirms Classification, Regression, or Clustering. The type that matches the target is labelled, and a mismatch shows a warning. |
| Prepare | 5    | Keep some rows for testing      | Chooses the split. A visual bar shows the estimated training and testing row counts. |
| Prepare | 6    | Choose how the model learns     | Picks an algorithm. Each card has a one-line analogy, a "Beginner friendly" or "Recommended" badge, and a strengths-and-limits section. Advanced settings include plain hints. |
| Train   | 7    | Review and train / progress     | Reviews a checklist with Edit links, names the model, and trains it. Real worker stages are shown with plain explanations, a note if the job is still waiting, and common fixes if it fails. |
| Use     | 8    | How well did your model do?     | Sees a quick verdict (strong / fair / needs improvement) and metric cards with plain meanings. Also: HTML confusion matrix, cluster sizes, train/test row counts, benchmark comparison, and a short "how to read" note per chart. |
| Use     | 9    | Try your model on a new example | Enters values. Each field shows the range seen in training, "Fill with typical values" fills them in, and a warning appears for values outside that range. |
| Use     | 10   | Model saved in your history     | Sees a recap of the completed work, the version history, the report, and "Try Another Algorithm" / "Train New Version" shortcuts. |

Every step has a collapsible **quick guide** (what you are doing, why it matters, an example, a beginner tip, and a common mistake). It stays collapsed on later visits if the student closes it.

The teaching copy lives in `app/Support/ModelDevelopmentGuide.php`, so wording can be changed without touching the views.

## Supported algorithms

Classification: Logistic Regression*, Decision Tree*, Random Forest, K-Nearest Neighbors, Naive Bayes, Support Vector Machine, Gradient Boosting Classifier.

Regression: Linear Regression*, Decision Tree Regressor*, Random Forest Regressor, Gradient Boosting Regressor.

Clustering: K-Means*.

`*` marks the algorithms labelled "Beginner friendly". The wizard selects one of them by default when a dataset has no recommended setup.

## Evaluation output

- **Classification:** accuracy, weighted precision, recall and F1, ROC AUC and average precision when available, confusion matrix, class distribution, and feature importance.
- **Regression:** R², MAE, RMSE (MSE is stored), actual-versus-predicted plot, residual plot, and feature importance.
- **Clustering:** silhouette score, cluster count, inertia, cluster sizes, and a cluster scatter plot.

The quick verdict uses F1 (or accuracy), R², or silhouette. The thresholds are classroom rules of thumb: 85/70% for F1, 0.75/0.50 for R², and 0.50/0.25 for silhouette.

## Guard rails

- The wizard validates each step before moving on. The server checks everything again in `AlgorithmCatalogService::normalizeTrainingConfiguration()`.
- A classification target with more than 100 distinct values is rejected before it is queued. This matches the trusted runner's limit.
- At most 50 features per run. The automatic preselection never goes above this.
- Pressing Enter inside a field moves to the next step. It never starts training early.
- If a prediction fails because of a runner or artifact problem, the student returns to the Predict step with a friendly message instead of a server error page.

## Installation / update

After copying the updated files:

```bash
php artisan optimize:clear
php artisan view:clear
```

No new migrations are required. Models trained after this update also store their train/test row counts. Older versions simply don't show those two fields.

## Training worker

Training jobs run on the `machine_learning` queue. `start-ml-worker.bat` (or `start-defense.bat`) is still the recommended way to run the worker.

If no worker is running when a student trains a model, DataSensei now starts a short-lived one (`queue:work machine_learning --stop-when-empty`). That worker exits as soon as the queue is empty. The training page says whether a worker is starting, or whether it could not start one.

A training job that is queued but has no queue row (for example, after the `jobs` table was cleared) is put back on the queue automatically.

Settings (`.env`):

- `ML_AUTO_START_WORKER=true`: set this to `false` to require the worker window instead.
- `DATASENSEI_PHP_BINARY=`: leave this empty when the site runs through `php artisan serve`. When it runs under Apache, set it to your PHP executable, for example `C:\xampp\php\php.exe`.

Do not run `php artisan migrate:fresh` on a database containing real users or project records.
