# DataSensei Model Development Module

Model Development picks up where Exploratory Data Analysis (EDA) ends. Students train a real scikit-learn model in four short steps and use it to make predictions. They don't write any code, and every machine-learning word has a tooltip with a simple definition.

## The four-step flow

The earlier version asked a beginner to make six decisions across ten screens before anything happened. Those decisions still exist, but they now sit on one set-up page with safe defaults.

| Step | Screen        | What the student does |
|------|---------------|-----------------------|
| 1    | Choose data   | Picks a question such as "Is this tumour breast cancer?". Each built-in dataset is shown as the question it answers. "Look inside" opens the preview and quality check. |
| 2    | Set up        | Chooses the answer column. DataSensei works out the kind of problem, ticks every usable clue column, and selects "Pick the best one for me". Everything else is under "More options". One button: Train my model. |
| 3    | Results       | Reads one sentence ("Right about 96 times out of 100"), the comparison with always guessing the most common answer, and the two kinds of mistake in plain words. The contest between algorithms and the clue ranking are below. All other numbers, charts, versions and the report are in closed sections. |
| 4    | Predict       | Loads a real example or types values, then reads the answer in words ("Likely breast cancer", "Very sure, about 97 out of 100") with a bar for each possible answer. The values stay in the form so one value can be changed and predicted again. |

Models are saved as soon as training ends, so there is no separate save step. Links from the ten-step version (`?step=2..7`, `step=evaluate`, `step=save`) still open the matching new page.

### Tooltips

Every machine-learning word on these pages has a dotted underline. Pointing at it, focusing it with Tab, or tapping it shows a one-sentence definition. The definitions are in `app/Support/ModelDevelopmentGlossary.php`; print a word in a view with `{{ \App\Support\ModelDevelopmentGlossary::term('accuracy') }}`.

### Plain-language answers

`app/Support/ModelDevelopmentOutcome.php` holds the wording for each built-in dataset: the question, what each answer means, friendly names and one-line help for every input, and which answer counts as the "yes" case. Uploaded datasets get wording from rules (yes/no, 0/1 and similar values read as "Likely yes" and "Likely no"). To reword a dataset, edit its entry in `presets()`.

### Accuracy

- **Automatic algorithm choice.** `auto_classification` and `auto_regression` make the trusted runner compare Logistic Regression, Decision Tree, K-Nearest Neighbors, Random Forest, Gradient Boosting and SVM (or the regression equivalents) with 5-fold cross-validation on the training rows only. The hidden test rows are never used for choosing, so the reported score stays honest. When candidates are within half a point, the simpler one wins.
- **Fine-tuning.** A named algorithm tries a few settings the same way unless the student unticks "Fine-tune the settings for me".
- **All usable columns by default.** The old recommended setups used a subset of columns. Columns that give the answer away (for example `quality_score` in Wine Quality) are left out and explained.
- **Baseline.** Every classification result stores what "always answer the most common class" scores, and every regression result stores the miss of "always answer the average". The results page and the verdict use them, so a lazy model is not called strong.
- The search stops after `ML_SEARCH_BUDGET` seconds (default 90) and keeps the best candidate found so far.

Measured here with the local runner (Python 3.11, scikit-learn 1.8), old recommended setup against the new default, same 80/20 split:

| Dataset | Before | After |
|---|---|---|
| Titanic | 67.5% | 74.2% |
| Breast Cancer | 94.7% | 95.6% |
| Housing (R²) | 0.899 | 0.941 |
| Sales (R²) | 0.850 | 0.870 |
| Iris, Student Performance | same | same |
| Wine Quality, Customer Churn | 76.9%, 85.9% | 75.4%, 84.7% (inside the noise of a 130 to 170 row test set) |

The teaching copy lives in `app/Support/ModelDevelopmentGuide.php`, so wording can be changed without touching the views.

## Supported algorithms

Classification: Logistic Regression*, Decision Tree*, Random Forest, K-Nearest Neighbors, Naive Bayes, Support Vector Machine, Gradient Boosting Classifier.

Regression: Linear Regression*, Decision Tree Regressor*, Random Forest Regressor, Gradient Boosting Regressor.

Clustering: K-Means*.

`*` marks the algorithms labelled "easy to explain". By default the set-up page selects "Pick the best one for me", which compares several of these and keeps the most accurate.

## Evaluation output

- **Classification:** accuracy, weighted precision, recall and F1, ROC AUC and average precision when available, confusion matrix, class distribution, and feature importance.
- **Regression:** R², MAE, RMSE (MSE is stored), actual-versus-predicted plot, residual plot, and feature importance.
- **Clustering:** silhouette score, cluster count, inertia, cluster sizes, and a cluster scatter plot.

The quick verdict uses accuracy (F1 for older versions that lack it), R², or silhouette. A classification score within 3 points of the most-common-answer baseline is rated one level lower. The thresholds are classroom rules of thumb: 85/70% for F1, 0.75/0.50 for R², and 0.50/0.25 for silhouette.

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
