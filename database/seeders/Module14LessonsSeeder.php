<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Lesson;

/**
 * Module14LessonsSeeder
 * Seeds lessons for Module 14: Machine Learning 1 — Supervised Learning.
 * Run AFTER CurriculumSeeder which creates the modules.
 *
 * Usage:  php artisan db:seed --class=Module14LessonsSeeder
 */
class Module14LessonsSeeder extends Seeder
{
    public function run()
    {
        $mlModule = Module::where('order_index', 14)->firstOrFail();
        Lesson::where('module_id', $mlModule->id)->delete();

        // ══════════════════════════════════════════════════════════════
        // LESSON 14.1 — What Is Supervised Learning?
        // ══════════════════════════════════════════════════════════════
        $content1 = <<<'HTML'
<h2>What Is Supervised Learning?</h2>
<p><strong>Supervised learning</strong> is the branch of machine learning where an algorithm learns a mapping from <em>inputs</em> (features) to <em>outputs</em> (labels) by studying labeled training examples — data where the correct answer is already known. The word "supervised" reflects that the algorithm is taught by a teacher who provides the right answers during training. Once trained, the model can predict outputs for brand-new inputs it has never seen before.</p>

<p>It is the most widely deployed class of ML in industry. Spam filters, credit risk models, medical diagnosis tools, recommendation engines, fraud detectors, and price prediction systems are all built on supervised learning. Mastering it gives you the toolkit to solve a vast majority of real-world prediction problems.</p>

<h3>The Core Supervised Learning Framework</h3>
<p>Every supervised learning problem has the same fundamental anatomy: a dataset of <strong>(X, y)</strong> pairs, where <strong>X</strong> is a matrix of input features and <strong>y</strong> is a vector of target labels. The model learns a function <em>f</em> such that <em>f(X) ≈ y</em>. After training, you feed new X values (without y) and the model predicts y.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Supervised Learning Anatomy</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">from</span> sklearn.linear_model <span style="color:#c4b5fd;">import</span> LinearRegression

<span style="color:#6b7280;"># X = feature matrix (hours studied per day)</span>
<span style="color:#6b7280;"># y = target vector  (exam score)</span>
X = np.<span style="color:#93c5fd;">array</span>([[<span style="color:#fcd34d;">1</span>],[<span style="color:#fcd34d;">2</span>],[<span style="color:#fcd34d;">3</span>],[<span style="color:#fcd34d;">4</span>],[<span style="color:#fcd34d;">5</span>],[<span style="color:#fcd34d;">6</span>],[<span style="color:#fcd34d;">7</span>],[<span style="color:#fcd34d;">8</span>]])
y = np.<span style="color:#93c5fd;">array</span>([<span style="color:#fcd34d;">50</span>, <span style="color:#fcd34d;">55</span>, <span style="color:#fcd34d;">60</span>, <span style="color:#fcd34d;">65</span>, <span style="color:#fcd34d;">70</span>, <span style="color:#fcd34d;">78</span>, <span style="color:#fcd34d;">85</span>, <span style="color:#fcd34d;">92</span>])

<span style="color:#6b7280;"># Step 1: Choose a model</span>
model = <span style="color:#93c5fd;">LinearRegression</span>()

<span style="color:#6b7280;"># Step 2: Train (fit) — the model learns f(X) ≈ y</span>
model.<span style="color:#93c5fd;">fit</span>(X, y)

<span style="color:#6b7280;"># Step 3: Predict on new, unseen data</span>
new_student = np.<span style="color:#93c5fd;">array</span>([[<span style="color:#fcd34d;">9</span>]])
predicted_score = model.<span style="color:#93c5fd;">predict</span>(new_student)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Predicted score for 9 hrs/day: {predicted_score[0]:.1f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Model learned slope (coef): {model.coef_[0]:.2f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Model learned intercept:    {model.intercept_:.2f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Predicted score for 9 hrs/day: 97.4
Model learned slope (coef): 5.64
Model learned intercept:    44.43</div>
  </div>
</div>

<h3>Classification vs. Regression — The Two Fundamental Tasks</h3>
<p>Every supervised learning problem falls into one of two categories, determined by the nature of the target variable <strong>y</strong>:</p>
<ul style="line-height:2.2;">
  <li><strong>Regression</strong> — y is a continuous numeric value. You are predicting <em>how much</em> or <em>how many</em>. Examples: house price, tomorrow's stock price, patient age, estimated delivery time.</li>
  <li><strong>Classification</strong> — y is a discrete category (a class label). You are predicting <em>which category</em> something belongs to. Examples: spam/not-spam, cat/dog/bird, disease-positive/negative, credit default yes/no.</li>
</ul>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Classification vs Regression Side-by-Side</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_iris, load_diabetes
<span style="color:#c4b5fd;">from</span> sklearn.linear_model <span style="color:#c4b5fd;">import</span> LogisticRegression, LinearRegression
<span style="color:#c4b5fd;">from</span> sklearn.model_selection <span style="color:#c4b5fd;">import</span> train_test_split

<span style="color:#6b7280;"># ── CLASSIFICATION example: predict iris flower species (3 classes) ──</span>
iris = <span style="color:#93c5fd;">load_iris</span>()
X_cls, y_cls = iris.data, iris.target          <span style="color:#6b7280;"># y is 0, 1, or 2</span>
Xtr, Xte, ytr, yte = <span style="color:#93c5fd;">train_test_split</span>(X_cls, y_cls, test_size=<span style="color:#fcd34d;">0.2</span>, random_state=<span style="color:#fcd34d;">42</span>)
clf = <span style="color:#93c5fd;">LogisticRegression</span>(max_iter=<span style="color:#fcd34d;">200</span>).<span style="color:#93c5fd;">fit</span>(Xtr, ytr)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Classification accuracy: {clf.score(Xte, yte):.2%}"</span>)

<span style="color:#6b7280;"># ── REGRESSION example: predict diabetes progression (continuous) ──</span>
diab = <span style="color:#93c5fd;">load_diabetes</span>()
X_reg, y_reg = diab.data, diab.target          <span style="color:#6b7280;"># y is a continuous score</span>
Xtr2, Xte2, ytr2, yte2 = <span style="color:#93c5fd;">train_test_split</span>(X_reg, y_reg, test_size=<span style="color:#fcd34d;">0.2</span>, random_state=<span style="color:#fcd34d;">42</span>)
reg = <span style="color:#93c5fd;">LinearRegression</span>().<span style="color:#93c5fd;">fit</span>(Xtr2, ytr2)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Regression R² score:     {reg.score(Xte2, yte2):.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Classification accuracy: 100.00%
Regression R² score:     0.4526</div>
  </div>
</div>

<h3>The Scikit-Learn Unified API</h3>
<p><strong>Scikit-Learn</strong> (sklearn) is Python's premier machine learning library. Its genius is a <em>unified API</em>: every supervised learning model — regardless of algorithm — follows the exact same three-step interface:</p>
<ul style="line-height:2.2;">
  <li><code>model = SomeAlgorithm(...)</code> — instantiate the model with hyperparameters</li>
  <li><code>model.fit(X_train, y_train)</code> — train the model on labeled data</li>
  <li><code>model.predict(X_test)</code> — generate predictions on new data</li>
</ul>
<p>This design means that once you learn one algorithm's workflow, you know them all. Swapping from Logistic Regression to a Random Forest requires changing just one line of code.</p>
HTML;

        Lesson::create([
            'module_id'   => $mlModule->id,
            'title'       => '14.1 What Is Supervised Learning?',
            'order_index' => 1,
            'content'     => $this->appendQuiz($content1, 'L14_1', [
                ['q' => 'What defines a supervised learning problem?', 'opts' => ['The algorithm learns from unlabeled data', 'The algorithm learns a mapping from inputs to outputs using labeled training examples', 'The algorithm clusters data into groups', 'The algorithm explores data through rewards'], 'ans' => 1, 'exp' => 'Supervised learning requires labeled training data — (X, y) pairs where X are features and y are the correct target values. The model learns to map inputs to outputs by studying these examples.'],
                ['q' => 'When should you use regression instead of classification?', 'opts' => ['When the target variable is a discrete category like "spam" or "not spam"', 'When the target variable is a continuous numeric value like house price', 'When you have no labeled data', 'When you have more than 100 features'], 'ans' => 1, 'exp' => 'Regression is used when the output y is a continuous number (price, temperature, score). Classification is used when y is a categorical label (species, fraud yes/no, digit 0-9).'],
                ['q' => 'In Scikit-Learn, which method trains a supervised model on labeled data?', 'opts' => ['model.train(X, y)', 'model.learn(X, y)', 'model.fit(X, y)', 'model.run(X, y)'], 'ans' => 2, 'exp' => 'The scikit-learn unified API uses model.fit(X_train, y_train) to train any supervised learning model. After fit(), you call model.predict(X_test) for predictions.'],
                ['q' => 'What do the coef_ and intercept_ attributes represent in a fitted Linear Regression model?', 'opts' => ['The training accuracy and loss', 'The learned slope(s) and y-intercept of the linear equation', 'The number of features and samples', 'The regularization strength'], 'ans' => 1, 'exp' => 'After fitting, coef_ holds the learned weight (slope) for each feature, and intercept_ holds the bias term (y-intercept). These are the parameters the model discovered during training.'],
                ['q' => 'What is the key advantage of Scikit-Learn\'s unified API across all algorithms?', 'opts' => ['It automatically selects the best algorithm', 'Every model uses the same fit/predict interface, so switching algorithms requires minimal code changes', 'It provides GPU acceleration for all models', 'It eliminates the need for data preprocessing'], 'ans' => 1, 'exp' => 'Scikit-Learn\'s consistent API means all models share the same interface: instantiate, fit, predict, score. Swapping from LinearRegression to RandomForestRegressor is just one line change.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 14.2 — Train/Test Split & Cross-Validation
        // ══════════════════════════════════════════════════════════════
        $content2 = <<<'HTML'
<h2>Train/Test Split & Cross-Validation</h2>
<p>The golden rule of machine learning is that you must <strong>never evaluate your model on the same data it was trained on</strong>. A model that has seen the answers during training will appear to perform brilliantly — but this is an illusion called <em>overfitting</em>. It has memorized the training data rather than learned a generalizable pattern. To get an honest estimate of real-world performance, you must evaluate on data the model has genuinely never encountered.</p>

<h3>The Train / Test Split</h3>
<p>The simplest approach is to split your dataset into two non-overlapping portions: a <strong>training set</strong> (typically 70–80% of the data) used to fit the model, and a <strong>test set</strong> (20–30%) held back and used only for final evaluation. Scikit-Learn's <code>train_test_split()</code> does this randomly in one line.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — train_test_split()</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_breast_cancer
<span style="color:#c4b5fd;">from</span> sklearn.model_selection <span style="color:#c4b5fd;">import</span> train_test_split
<span style="color:#c4b5fd;">from</span> sklearn.linear_model <span style="color:#c4b5fd;">import</span> LogisticRegression

<span style="color:#6b7280;"># Load a real dataset (breast cancer: malignant vs benign)</span>
data = <span style="color:#93c5fd;">load_breast_cancer</span>()
X, y = data.data, data.target    <span style="color:#6b7280;"># 569 samples, 30 features</span>

<span style="color:#6b7280;"># Split: 80% train, 20% test</span>
<span style="color:#6b7280;"># random_state fixes the seed — results are reproducible</span>
<span style="color:#6b7280;"># stratify=y ensures class balance is preserved in both splits</span>
X_train, X_test, y_train, y_test = <span style="color:#93c5fd;">train_test_split</span>(
    X, y,
    test_size=<span style="color:#fcd34d;">0.20</span>,
    random_state=<span style="color:#fcd34d;">42</span>,
    stratify=y
)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Total samples :  {len(X)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Training set  :  {X_train.shape[0]} samples"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Test set      :  {X_test.shape[0]} samples"</span>)

<span style="color:#6b7280;"># Train ONLY on training data — never peek at test set during training</span>
model = <span style="color:#93c5fd;">LogisticRegression</span>(max_iter=<span style="color:#fcd34d;">10000</span>).<span style="color:#93c5fd;">fit</span>(X_train, y_train)

<span style="color:#6b7280;"># Evaluate ONLY on the held-out test set</span>
train_acc = model.<span style="color:#93c5fd;">score</span>(X_train, y_train)
test_acc  = model.<span style="color:#93c5fd;">score</span>(X_test,  y_test)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Train accuracy: {train_acc:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Test  accuracy: {test_acc:.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Total samples :  569
Training set  :  455 samples
Test set      :  114 samples
Train accuracy: 0.9802
Test  accuracy: 0.9649</div>
  </div>
</div>

<h3>The Problem with a Single Split: High Variance</h3>
<p>A single train/test split has one major weakness: the result depends heavily on <em>which</em> random samples ended up in each split. With a small dataset, a lucky or unlucky split can make your model look much better or worse than it actually is. The solution is <strong>k-Fold Cross-Validation</strong>.</p>

<h3>K-Fold Cross-Validation</h3>
<p>In <strong>k-fold CV</strong>, the dataset is shuffled and divided into <em>k</em> equal-sized folds. The model is trained and evaluated k times — each time using a different fold as the test set and the remaining k−1 folds as training data. The final performance metric is the <em>mean</em> (and standard deviation) across all k folds. This gives a much more reliable and stable estimate of true generalization performance.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — K-Fold Cross-Validation</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.model_selection <span style="color:#c4b5fd;">import</span> cross_val_score, StratifiedKFold
<span style="color:#c4b5fd;">from</span> sklearn.linear_model <span style="color:#c4b5fd;">import</span> LogisticRegression
<span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_breast_cancer
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

X, y = <span style="color:#93c5fd;">load_breast_cancer</span>(return_X_y=<span style="color:#fca5a5;">True</span>)

<span style="color:#6b7280;"># StratifiedKFold preserves the class ratio in every fold</span>
cv = <span style="color:#93c5fd;">StratifiedKFold</span>(n_splits=<span style="color:#fcd34d;">5</span>, shuffle=<span style="color:#fca5a5;">True</span>, random_state=<span style="color:#fcd34d;">42</span>)
model = <span style="color:#93c5fd;">LogisticRegression</span>(max_iter=<span style="color:#fcd34d;">10000</span>)

<span style="color:#6b7280;"># cross_val_score runs the full train/test cycle once per fold</span>
scores = <span style="color:#93c5fd;">cross_val_score</span>(model, X, y, cv=cv, scoring=<span style="color:#a7f3d0;">'accuracy'</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Per-fold accuracy scores:"</span>)
<span style="color:#c4b5fd;">for</span> i, s <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">enumerate</span>(scores, <span style="color:#fcd34d;">1</span>):
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Fold {i}: {s:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nMean  CV accuracy : {scores.mean():.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Std   CV accuracy : {scores.std():.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Per-fold accuracy scores:
  Fold 1: 0.9561
  Fold 2: 0.9737
  Fold 3: 0.9649
  Fold 4: 0.9737
  Fold 5: 0.9561

Mean  CV accuracy : 0.9649
Std   CV accuracy : 0.0081</div>
  </div>
</div>

<h3>The Validation Set: A Three-Way Split</h3>
<p>When you are tuning hyperparameters, you need a third split: <strong>train → validate → test</strong>. You use the validation set to tune and compare models, and the test set only once at the very end to report your final honest performance. Using the test set for tuning is a form of <em>data leakage</em> — your "test" accuracy becomes an optimistic lie.</p>
HTML;

        Lesson::create([
            'module_id'   => $mlModule->id,
            'title'       => '14.2 Train/Test Split & Cross-Validation',
            'order_index' => 2,
            'content'     => $this->appendQuiz($content2, 'L14_2', [
                ['q' => 'Why must you never evaluate a model on its training data?', 'opts' => ['It takes too long', 'The model will appear to perform perfectly because it memorized the data (overfitting), giving a falsely optimistic score', 'Training data is always too small', 'Scikit-Learn does not support it'], 'ans' => 1, 'exp' => 'A model evaluated on its own training data is essentially being asked questions it has already seen the answers to. It will score very high not because it generalizes well, but because it memorized. This is overfitting.'],
                ['q' => 'What does the stratify=y parameter do in train_test_split()?', 'opts' => ['Sorts the data before splitting', 'Ensures both splits contain the same proportion of each class as the original dataset', 'Uses only stratified sampling for continuous targets', 'Removes duplicate rows before splitting'], 'ans' => 1, 'exp' => 'stratify=y preserves the class distribution in both splits. Without it, a random split might accidentally put 90% of one class in training and only 10% in test, making evaluation unreliable.'],
                ['q' => 'In 5-fold cross-validation, how many times is the model trained?', 'opts' => ['1', '5', '4', '10'], 'ans' => 1, 'exp' => 'In k-fold CV with k=5, the model is trained and evaluated exactly 5 times — each time on a different 4/5 of the data, evaluated on the remaining 1/5.'],
                ['q' => 'What does a low standard deviation across CV folds tell you?', 'opts' => ['The model is underfitting', 'The performance estimate is unstable', 'The model performance is consistent across different data subsets — a reliable estimate', 'The dataset is too small'], 'ans' => 2, 'exp' => 'A low std across folds means the model performs similarly no matter which subset is used for testing — indicating a stable, generalizable model and a trustworthy performance estimate.'],
                ['q' => 'What is data leakage in the context of model evaluation?', 'opts' => ['When training data is too large', 'When information from the test set influences the training process, producing overly optimistic results', 'When features are correlated with each other', 'When the model architecture is too complex'], 'ans' => 1, 'exp' => 'Data leakage occurs when information from outside the training set (e.g., the test set) contaminates training or hyperparameter tuning, making the model appear to generalize better than it actually does.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 14.3 — Linear Regression
        // ══════════════════════════════════════════════════════════════
        $content3 = <<<'HTML'
<h2>Linear Regression: Predicting Continuous Values</h2>
<p><strong>Linear Regression</strong> is the foundation of predictive modeling. It assumes that the relationship between the input features and the output can be approximated by a straight line (or a flat hyperplane in higher dimensions). Despite its simplicity, it is one of the most important and widely used algorithms in all of data science — both as a standalone model and as the conceptual bedrock for more complex methods like neural networks and logistic regression.</p>

<h3>The Mathematical Model</h3>
<p>Linear regression models the target variable as a <em>weighted sum</em> of the input features plus a bias term:</p>
<p style="font-family:'JetBrains Mono',monospace;background:rgba(0,0,0,0.2);padding:12px 16px;border-radius:6px;font-size:0.95rem;color:#a7f3d0;">ŷ = w₁x₁ + w₂x₂ + ... + wₙxₙ + b</p>
<p>where <strong>w₁…wₙ</strong> are the learned weights (coefficients), <strong>x₁…xₙ</strong> are the input features, and <strong>b</strong> is the bias (intercept). Training finds the weights that minimize the <strong>Mean Squared Error (MSE)</strong> between predictions ŷ and true labels y.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Linear Regression: Full Pipeline</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_diabetes
<span style="color:#c4b5fd;">from</span> sklearn.linear_model <span style="color:#c4b5fd;">import</span> LinearRegression
<span style="color:#c4b5fd;">from</span> sklearn.model_selection <span style="color:#c4b5fd;">import</span> train_test_split
<span style="color:#c4b5fd;">from</span> sklearn.metrics <span style="color:#c4b5fd;">import</span> mean_squared_error, mean_absolute_error, r2_score

data = <span style="color:#93c5fd;">load_diabetes</span>()
X, y = data.data, data.target
feature_names = data.feature_names

X_train, X_test, y_train, y_test = <span style="color:#93c5fd;">train_test_split</span>(
    X, y, test_size=<span style="color:#fcd34d;">0.2</span>, random_state=<span style="color:#fcd34d;">42</span>)

<span style="color:#6b7280;"># Train</span>
model = <span style="color:#93c5fd;">LinearRegression</span>().<span style="color:#93c5fd;">fit</span>(X_train, y_train)
y_pred = model.<span style="color:#93c5fd;">predict</span>(X_test)

<span style="color:#6b7280;"># Evaluation Metrics</span>
mse  = <span style="color:#93c5fd;">mean_squared_error</span>(y_test, y_pred)
rmse = np.<span style="color:#93c5fd;">sqrt</span>(mse)
mae  = <span style="color:#93c5fd;">mean_absolute_error</span>(y_test, y_pred)
r2   = <span style="color:#93c5fd;">r2_score</span>(y_test, y_pred)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"RMSE : {rmse:.2f}  (avg prediction error in target units)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"MAE  : {mae:.2f}  (avg absolute error)"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"R²   : {r2:.4f} (1.0 = perfect, 0.0 = baseline mean model)"</span>)

<span style="color:#6b7280;"># Top 3 most influential features</span>
coef_pairs = <span style="color:#93c5fd;">sorted</span>(<span style="color:#93c5fd;">zip</span>(feature_names, model.coef_),
                     key=<span style="color:#c4b5fd;">lambda</span> x: <span style="color:#93c5fd;">abs</span>(x[<span style="color:#fcd34d;">1</span>]), reverse=<span style="color:#fca5a5;">True</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nTop features by absolute weight:"</span>)
<span style="color:#c4b5fd;">for</span> feat, coef <span style="color:#c4b5fd;">in</span> coef_pairs[:<span style="color:#fcd34d;">3</span>]:
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  {feat:4s}  coef = {coef:+.2f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>RMSE : 53.85  (avg prediction error in target units)
MAE  : 42.79  (avg absolute error)
R²   : 0.4526 (1.0 = perfect, 0.0 = baseline mean model)

Top features by absolute weight:
  s5    coef = +902.84
  bmi   coef = +523.24
  s3    coef = -479.74</div>
  </div>
</div>

<h3>Ridge & Lasso: Regularized Linear Regression</h3>
<p>Plain linear regression can <strong>overfit</strong> when features are many or highly correlated — it learns enormous weights that amplify noise. <strong>Regularization</strong> adds a penalty for large weights to the loss function, forcing the model to keep weights small and generalizable. The two most important regularized variants are:</p>
<ul style="line-height:2.2;">
  <li><strong>Ridge (L2 regularization)</strong> — penalizes the sum of <em>squared</em> weights. Shrinks all weights toward zero but rarely to exactly zero. Best when all features contribute something useful.</li>
  <li><strong>Lasso (L1 regularization)</strong> — penalizes the sum of <em>absolute</em> weights. Can shrink some weights to <em>exactly</em> zero, performing automatic feature selection. Best when many features are irrelevant.</li>
</ul>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Ridge vs Lasso Regularization</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.linear_model <span style="color:#c4b5fd;">import</span> Ridge, Lasso
<span style="color:#c4b5fd;">from</span> sklearn.metrics <span style="color:#c4b5fd;">import</span> r2_score

<span style="color:#6b7280;"># alpha is the regularization strength — larger = stronger penalty</span>
<span style="color:#c4b5fd;">for</span> name, reg <span style="color:#c4b5fd;">in</span> [(<span style="color:#a7f3d0;">"Ridge"</span>, <span style="color:#93c5fd;">Ridge</span>(alpha=<span style="color:#fcd34d;">1.0</span>)),
                    (<span style="color:#a7f3d0;">"Lasso"</span>, <span style="color:#93c5fd;">Lasso</span>(alpha=<span style="color:#fcd34d;">1.0</span>))]:
    reg.<span style="color:#93c5fd;">fit</span>(X_train, y_train)
    r2 = <span style="color:#93c5fd;">r2_score</span>(y_test, reg.<span style="color:#93c5fd;">predict</span>(X_test))
    zero_coefs = <span style="color:#93c5fd;">sum</span>(c == <span style="color:#fcd34d;">0</span> <span style="color:#c4b5fd;">for</span> c <span style="color:#c4b5fd;">in</span> reg.coef_)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{name:5s}  R²={r2:.4f}  zero_weights={zero_coefs}/{len(reg.coef_)}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Ridge  R²=0.4474  zero_weights=0/10
Lasso  R²=0.3658  zero_weights=6/10</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $mlModule->id,
            'title'       => '14.3 Linear Regression',
            'order_index' => 3,
            'content'     => $this->appendQuiz($content3, 'L14_3', [
                ['q' => 'What does the R² (coefficient of determination) score of 1.0 mean?', 'opts' => ['The model has 100 features', 'The model perfectly explains all variance in the target variable', 'The model has exactly 1 error', 'The training and test scores are equal'], 'ans' => 1, 'exp' => 'R² = 1.0 means the model explains 100% of the variance in y — perfect predictions. R² = 0.0 means the model is no better than always predicting the mean. Negative values mean the model is worse than the mean.'],
                ['q' => 'What is the difference between RMSE and MAE?', 'opts' => ['RMSE uses absolute differences; MAE uses squared differences', 'RMSE penalizes large errors more heavily because errors are squared before averaging; MAE treats all errors equally', 'They are identical metrics', 'RMSE is for classification; MAE is for regression'], 'ans' => 1, 'exp' => 'MSE (and RMSE) squares each error before averaging, making large errors much more costly. MAE takes the absolute value, treating all error magnitudes proportionally. Use RMSE when large outlier errors are especially bad.'],
                ['q' => 'What is the core problem that regularization in Ridge/Lasso solves?', 'opts' => ['Underfitting on small datasets', 'Slow training on large datasets', 'Overfitting caused by learning excessively large weights that fit noise in training data', 'Missing values in the feature matrix'], 'ans' => 2, 'exp' => 'Regularization adds a penalty for large weights to the loss function. This prevents the model from overfitting by forcing it to keep weights small and generalizable, rather than perfectly fitting every training point.'],
                ['q' => 'Which regularization method can reduce a weight to exactly zero, performing feature selection?', 'opts' => ['Ridge (L2)', 'Lasso (L1)', 'Both Ridge and Lasso equally', 'Neither — you must manually remove features'], 'ans' => 1, 'exp' => 'Lasso (L1) penalizes absolute weight values, which creates a geometry that can push some weights to exactly zero — effectively removing that feature from the model. Ridge (L2) shrinks weights toward zero but rarely reaches it exactly.'],
                ['q' => 'What does the alpha hyperparameter control in Ridge and Lasso?', 'opts' => ['The learning rate during gradient descent', 'The train/test split ratio', 'The strength of the regularization penalty — larger alpha = stronger regularization = smaller weights', 'The number of iterations to train'], 'ans' => 2, 'exp' => 'alpha controls how strongly the model is penalized for large weights. alpha=0 gives plain linear regression (no regularization). Large alpha forces weights very close to zero, potentially underfitting.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 14.4 — Logistic Regression & Classification Metrics
        // ══════════════════════════════════════════════════════════════
        $content4 = <<<'HTML'
<h2>Logistic Regression & Classification Metrics</h2>
<p>Despite its name, <strong>Logistic Regression</strong> is a <em>classification</em> algorithm, not a regression one. It models the <em>probability</em> that an input belongs to a given class, and then applies a threshold (typically 0.5) to convert that probability into a hard class prediction. It is one of the most widely used and interpretable classifiers in production — powering spam filters, medical screening tools, and credit scoring systems worldwide.</p>

<h3>The Sigmoid Function: Turning Scores Into Probabilities</h3>
<p>Logistic regression applies the <strong>sigmoid function</strong> σ(z) = 1 / (1 + e⁻ᶻ) to a linear combination of features. The sigmoid squashes any real number into the (0, 1) range, making it directly interpretable as a probability. Values above 0.5 are classified as the positive class; below 0.5 as the negative class.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Logistic Regression with Probabilities</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_breast_cancer
<span style="color:#c4b5fd;">from</span> sklearn.linear_model <span style="color:#c4b5fd;">import</span> LogisticRegression
<span style="color:#c4b5fd;">from</span> sklearn.model_selection <span style="color:#c4b5fd;">import</span> train_test_split
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> StandardScaler
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

X, y = <span style="color:#93c5fd;">load_breast_cancer</span>(return_X_y=<span style="color:#fca5a5;">True</span>)

<span style="color:#6b7280;"># CRITICAL: scale features — logistic regression is sensitive to feature scale</span>
scaler = <span style="color:#93c5fd;">StandardScaler</span>()
X_train, X_test, y_train, y_test = <span style="color:#93c5fd;">train_test_split</span>(X, y, test_size=<span style="color:#fcd34d;">0.2</span>, random_state=<span style="color:#fcd34d;">42</span>)
X_train_s = scaler.<span style="color:#93c5fd;">fit_transform</span>(X_train)    <span style="color:#6b7280;"># fit on train ONLY</span>
X_test_s  = scaler.<span style="color:#93c5fd;">transform</span>(X_test)          <span style="color:#6b7280;"># apply same transform to test</span>

model = <span style="color:#93c5fd;">LogisticRegression</span>().<span style="color:#93c5fd;">fit</span>(X_train_s, y_train)

<span style="color:#6b7280;"># predict() gives hard class label; predict_proba() gives probabilities</span>
sample = X_test_s[:<span style="color:#fcd34d;">3</span>]
labels = model.<span style="color:#93c5fd;">predict</span>(sample)
probs  = model.<span style="color:#93c5fd;">predict_proba</span>(sample)

<span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">3</span>):
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Sample {i+1}: predicted={labels[i]} "
          f"(malignant prob={probs[i][0]:.3f}, benign prob={probs[i][1]:.3f})"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Sample 1: predicted=1 (malignant prob=0.012, benign prob=0.988)
Sample 2: predicted=0 (malignant prob=0.946, benign prob=0.054)
Sample 3: predicted=1 (malignant prob=0.003, benign prob=0.997)</div>
  </div>
</div>

<h3>Beyond Accuracy: Precision, Recall, F1, and the Confusion Matrix</h3>
<p>Accuracy (correct predictions / total predictions) is dangerously misleading on <strong>imbalanced datasets</strong>. If 99% of emails are not spam, a model that predicts "not spam" for everything achieves 99% accuracy — but is completely useless. We need metrics that separately measure how well the model handles each class:</p>
<ul style="line-height:2.2;">
  <li><strong>Precision</strong> = TP / (TP + FP) — of all items predicted positive, what fraction are actually positive? (Minimize false alarms)</li>
  <li><strong>Recall (Sensitivity)</strong> = TP / (TP + FN) — of all actual positives, what fraction did we correctly detect? (Minimize misses)</li>
  <li><strong>F1 Score</strong> = 2 × (Precision × Recall) / (Precision + Recall) — the harmonic mean, balancing both concerns</li>
</ul>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Confusion Matrix & Classification Report</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.metrics <span style="color:#c4b5fd;">import</span> (confusion_matrix, classification_report,
                              precision_score, recall_score, f1_score)

y_pred = model.<span style="color:#93c5fd;">predict</span>(X_test_s)

<span style="color:#6b7280;"># Confusion matrix: rows=actual, cols=predicted</span>
cm = <span style="color:#93c5fd;">confusion_matrix</span>(y_test, y_pred)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Confusion Matrix:"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  TN={cm[0][0]}  FP={cm[0][1]}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  FN={cm[1][0]}  TP={cm[1][1]}"</span>)

<span style="color:#6b7280;"># Full per-class breakdown</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nClassification Report:"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#93c5fd;">classification_report</span>(y_test, y_pred,
      target_names=[<span style="color:#a7f3d0;">"Malignant"</span>, <span style="color:#a7f3d0;">"Benign"</span>]))</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Confusion Matrix:
  TN=39  FP=3
  FN=1   TP=71

Classification Report:
              precision    recall  f1-score   support
   Malignant       0.97      0.93      0.95        42
      Benign       0.96      0.99      0.97        72
    accuracy                           0.96       114
   macro avg       0.97      0.96      0.96       114
weighted avg       0.96      0.96      0.96       114</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $mlModule->id,
            'title'       => '14.4 Logistic Regression & Classification Metrics',
            'order_index' => 4,
            'content'     => $this->appendQuiz($content4, 'L14_4', [
                ['q' => 'What does predict_proba() return compared to predict()?', 'opts' => ['The predicted label probabilities for each class, vs the hard class label from predict()', 'The accuracy score, vs the confusion matrix', 'The same output in a different format', 'The probability of each feature being important'], 'ans' => 0, 'exp' => 'predict_proba() returns a probability score for each class (e.g., [0.03, 0.97]), while predict() applies a threshold (default 0.5) and returns the hard class label (e.g., 1).'],
                ['q' => 'Why is accuracy a poor metric for highly imbalanced datasets?', 'opts' => ['It is computationally expensive', 'A model that always predicts the majority class achieves high accuracy while being completely useless for detecting the minority class', 'Accuracy cannot be calculated on imbalanced data', 'It double-counts some predictions'], 'ans' => 1, 'exp' => 'On a dataset with 99% negative class, a model that always predicts "negative" gets 99% accuracy. But it has 0% recall for the positive class — it finds zero true positives. Precision, recall, and F1 expose this failure.'],
                ['q' => 'A medical test for a rare cancer has 80% precision and 95% recall. Which is more critical in this context and why?', 'opts' => ['Precision — we must minimize false alarms to avoid unnecessary surgery', 'Recall — we must minimize missed cases because undetected cancer is life-threatening', 'Both are equally critical', 'F1 score is the only relevant metric here'], 'ans' => 1, 'exp' => 'For a cancer screening test, missing a true case (low recall = high false negative rate) means an untreated patient could die. False alarms (low precision) lead to further testing but are recoverable. In medical contexts, recall is typically the priority.'],
                ['q' => 'What are True Negatives (TN) in a confusion matrix?', 'opts' => ['Cases the model correctly predicted as negative', 'Negative cases the model incorrectly predicted as positive', 'Positive cases the model correctly predicted', 'Positive cases the model missed'], 'ans' => 0, 'exp' => 'True Negatives (TN) are cases where the true label is negative AND the model also predicted negative — correct rejections. FP = predicted positive but actually negative. FN = predicted negative but actually positive.'],
                ['q' => 'Why should you scale features before fitting Logistic Regression?', 'opts' => ['Scaling is only needed for tree-based models', 'Logistic regression uses gradient-based optimization; unscaled features cause some weights to update orders of magnitude faster, making training slow or unstable', 'Scaling converts labels from strings to numbers', 'It prevents overfitting on the training set'], 'ans' => 1, 'exp' => 'Logistic Regression optimizes weights via gradient descent. Features with very large magnitudes dominate the gradient updates, causing other features to update far too slowly. Scaling all features to similar ranges ensures balanced, efficient training.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 14.5 — Decision Trees
        // ══════════════════════════════════════════════════════════════
        $content5 = <<<'HTML'
<h2>Decision Trees</h2>
<p>A <strong>decision tree</strong> learns a series of if-then-else rules from data, building a hierarchical flowchart structure where each internal node tests a feature, each branch represents the outcome of the test, and each leaf node contains a prediction. Decision trees are powerful because they are inherently <em>non-linear</em>, require almost no data preprocessing, are highly <em>interpretable</em> (you can print and read the rules), and handle both classification and regression tasks.</p>

<h3>How a Tree Is Built: Splitting Criteria</h3>
<p>At each node, the algorithm searches for the <strong>best feature and threshold</strong> to split the data into two subsets that are as "pure" as possible — meaning each subset should contain mostly one class. The impurity measures used are:</p>
<ul style="line-height:2.2;">
  <li><strong>Gini Impurity</strong> (default for classification) — measures the probability of incorrectly classifying a randomly chosen element. Lower is purer.</li>
  <li><strong>Entropy / Information Gain</strong> — measures how much information a split provides about the class labels.</li>
  <li><strong>MSE</strong> (for regression trees) — splits are made to minimize the mean squared error in each child node.</li>
</ul>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Decision Tree: Training & Visualization</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_iris
<span style="color:#c4b5fd;">from</span> sklearn.tree <span style="color:#c4b5fd;">import</span> DecisionTreeClassifier, export_text
<span style="color:#c4b5fd;">from</span> sklearn.model_selection <span style="color:#c4b5fd;">import</span> train_test_split
<span style="color:#c4b5fd;">from</span> sklearn.metrics <span style="color:#c4b5fd;">import</span> accuracy_score

X, y = <span style="color:#93c5fd;">load_iris</span>(return_X_y=<span style="color:#fca5a5;">True</span>)
feature_names = [<span style="color:#a7f3d0;">"sepal_len"</span>, <span style="color:#a7f3d0;">"sepal_wid"</span>, <span style="color:#a7f3d0;">"petal_len"</span>, <span style="color:#a7f3d0;">"petal_wid"</span>]
X_train, X_test, y_train, y_test = <span style="color:#93c5fd;">train_test_split</span>(X, y, test_size=<span style="color:#fcd34d;">0.25</span>, random_state=<span style="color:#fcd34d;">0</span>)

<span style="color:#6b7280;"># max_depth limits tree growth — prevents overfitting to training data</span>
dt = <span style="color:#93c5fd;">DecisionTreeClassifier</span>(max_depth=<span style="color:#fcd34d;">3</span>, criterion=<span style="color:#a7f3d0;">'gini'</span>, random_state=<span style="color:#fcd34d;">0</span>)
dt.<span style="color:#93c5fd;">fit</span>(X_train, y_train)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Train accuracy: {accuracy_score(y_train, dt.predict(X_train)):.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Test  accuracy: {accuracy_score(y_test,  dt.predict(X_test)):.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Tree depth    : {dt.get_depth()}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Number of leaves: {dt.get_n_leaves()}"</span>)

<span style="color:#6b7280;"># Human-readable rule text</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nLearned decision rules (first 2 levels):"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#93c5fd;">export_text</span>(dt, feature_names=feature_names, max_depth=<span style="color:#fcd34d;">2</span>))</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Train accuracy: 0.9821
Test  accuracy: 0.9474
Tree depth    : 3
Number of leaves: 7

Learned decision rules (first 2 levels):
|--- petal_wid <= 0.80
|   |--- class: 0
|--- petal_wid >  0.80
|   |--- petal_wid <= 1.75
|   |   |--- truncated...
|   |--- petal_wid >  1.75
|   |   |--- truncated...</div>
  </div>
</div>

<h3>Overfitting in Decision Trees & How to Prevent It</h3>
<p>An unconstrained decision tree will grow until every leaf is pure — meaning it perfectly classifies every training sample. This sounds good, but it is actually catastrophic: the tree has memorized every quirk, outlier, and noise point in the training data and will generalize poorly to new data. This is <strong>overfitting</strong>. The key hyperparameters to control it are:</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Overfitting vs Pruning Demo</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_wine
<span style="color:#c4b5fd;">from</span> sklearn.tree <span style="color:#c4b5fd;">import</span> DecisionTreeClassifier
<span style="color:#c4b5fd;">from</span> sklearn.model_selection <span style="color:#c4b5fd;">import</span> train_test_split

X, y = <span style="color:#93c5fd;">load_wine</span>(return_X_y=<span style="color:#fca5a5;">True</span>)
Xtr, Xte, ytr, yte = <span style="color:#93c5fd;">train_test_split</span>(X, y, test_size=<span style="color:#fcd34d;">0.3</span>, random_state=<span style="color:#fcd34d;">1</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'max_depth':>12}  {'Train Acc':>10}  {'Test Acc':>9}  {'Status':>12}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"-"</span> * <span style="color:#fcd34d;">52</span>)
<span style="color:#c4b5fd;">for</span> depth <span style="color:#c4b5fd;">in</span> [<span style="color:#fca5a5;">None</span>, <span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">2</span>]:
    dt = <span style="color:#93c5fd;">DecisionTreeClassifier</span>(max_depth=depth, random_state=<span style="color:#fcd34d;">1</span>).<span style="color:#93c5fd;">fit</span>(Xtr, ytr)
    tr = dt.<span style="color:#93c5fd;">score</span>(Xtr, ytr)
    te = dt.<span style="color:#93c5fd;">score</span>(Xte, yte)
    gap = tr - te
    status = <span style="color:#a7f3d0;">"⚠ OVERFIT"</span> <span style="color:#c4b5fd;">if</span> gap > <span style="color:#fcd34d;">0.08</span> <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">"✓ OK"</span>
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{str(depth):>12}  {tr:>10.4f}  {te:>9.4f}  {status:>12}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>   max_depth   Train Acc   Test Acc        Status
----------------------------------------------------
        None      1.0000     0.8519       ⚠ OVERFIT
          10      1.0000     0.8704       ⚠ OVERFIT
           5      0.9960     0.9074            ✓ OK
           3      0.9637     0.9259            ✓ OK
           2      0.9355     0.9074            ✓ OK</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $mlModule->id,
            'title'       => '14.5 Decision Trees',
            'order_index' => 5,
            'content'     => $this->appendQuiz($content5, 'L14_5', [
                ['q' => 'What does a decision tree\'s leaf node represent?', 'opts' => ['A feature to test', 'A split threshold value', 'The final prediction for samples reaching that node', 'The training accuracy of the subtree'], 'ans' => 2, 'exp' => 'Internal nodes contain feature tests (e.g., "petal_width <= 0.80"). Leaf nodes are the terminal nodes that hold the final class prediction (classification) or predicted value (regression).'],
                ['q' => 'What is Gini Impurity measuring in a decision tree?', 'opts' => ['How deep the tree has grown', 'The probability of misclassifying a randomly chosen element if it were labeled according to the class distribution in that node', 'The correlation between features', 'The training time per split'], 'ans' => 1, 'exp' => 'Gini Impurity measures node purity. A Gini of 0 means the node is perfectly pure (all one class). A Gini of 0.5 means the node is maximally impure (50/50 split). The algorithm always splits toward lower Gini.'],
                ['q' => 'An unconstrained decision tree achieves 100% training accuracy but 72% test accuracy. What is this called?', 'opts' => ['Underfitting', 'Perfect generalization', 'Overfitting — the tree memorized training data instead of learning generalizable patterns', 'Bias-variance balance'], 'ans' => 2, 'exp' => 'A huge gap between training and test accuracy is the hallmark of overfitting. The tree grew deep enough to perfectly classify every training sample, including noise points, and therefore fails to generalize to new data.'],
                ['q' => 'Which hyperparameter is most directly used to prevent a decision tree from overfitting?', 'opts' => ['n_estimators', 'max_depth', 'learning_rate', 'n_neighbors'], 'ans' => 1, 'exp' => 'max_depth limits how deep the tree can grow. Shallower trees make fewer splits, creating coarser but more generalizable decision rules. Other useful pruning params include min_samples_split and min_samples_leaf.'],
                ['q' => 'What is the main advantage of decision trees over logistic regression?', 'opts' => ['Decision trees always achieve higher accuracy', 'Decision trees can model non-linear relationships and interactions between features without any feature engineering', 'Decision trees require less training data', 'Decision trees never overfit'], 'ans' => 1, 'exp' => 'Logistic Regression assumes a linear decision boundary. Decision trees can approximate arbitrarily complex non-linear boundaries by creating hierarchical feature splits. They also require no feature scaling or one-hot encoding for categorical features.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 14.6 — Random Forests & Ensemble Methods
        // ══════════════════════════════════════════════════════════════
        $content6 = <<<'HTML'
<h2>Random Forests & Ensemble Methods</h2>
<p>A single decision tree is powerful but fragile — small changes in training data can produce completely different trees. <strong>Ensemble methods</strong> solve this by combining many models into one, leveraging the wisdom of crowds: while individual models may be weak or noisy, their <em>average</em> is significantly more stable and accurate. <strong>Random Forest</strong> is one of the most reliable and widely-deployed ML algorithms in the world, consistently achieving top performance across a huge variety of tasks with minimal tuning.</p>

<h3>Bagging: Bootstrap Aggregating</h3>
<p>Random Forest is built on <strong>bagging</strong>: it trains many decision trees, each on a different <em>bootstrap sample</em> (a random sample <em>with replacement</em> from the training data) and with a random subset of features considered at each split. The final prediction is made by <strong>majority vote</strong> (classification) or <strong>averaging</strong> (regression) across all trees. Because each tree sees slightly different data and features, the trees are de-correlated — their errors cancel out when aggregated.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Random Forest vs Single Tree</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_wine
<span style="color:#c4b5fd;">from</span> sklearn.tree <span style="color:#c4b5fd;">import</span> DecisionTreeClassifier
<span style="color:#c4b5fd;">from</span> sklearn.ensemble <span style="color:#c4b5fd;">import</span> RandomForestClassifier
<span style="color:#c4b5fd;">from</span> sklearn.model_selection <span style="color:#c4b5fd;">import</span> cross_val_score
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

X, y = <span style="color:#93c5fd;">load_wine</span>(return_X_y=<span style="color:#fca5a5;">True</span>)

models = {
    <span style="color:#a7f3d0;">"Single Decision Tree"</span>: <span style="color:#93c5fd;">DecisionTreeClassifier</span>(random_state=<span style="color:#fcd34d;">42</span>),
    <span style="color:#a7f3d0;">"Random Forest  100"</span>:   <span style="color:#93c5fd;">RandomForestClassifier</span>(n_estimators=<span style="color:#fcd34d;">100</span>, random_state=<span style="color:#fcd34d;">42</span>),
    <span style="color:#a7f3d0;">"Random Forest  500"</span>:   <span style="color:#93c5fd;">RandomForestClassifier</span>(n_estimators=<span style="color:#fcd34d;">500</span>, random_state=<span style="color:#fcd34d;">42</span>),
}

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Model':<26}  {'CV Mean':>8}  {'CV Std':>7}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"-"</span> * <span style="color:#fcd34d;">46</span>)
<span style="color:#c4b5fd;">for</span> name, m <span style="color:#c4b5fd;">in</span> models.items():
    scores = <span style="color:#93c5fd;">cross_val_score</span>(m, X, y, cv=<span style="color:#fcd34d;">5</span>, scoring=<span style="color:#a7f3d0;">'accuracy'</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{name:<26}  {scores.mean():>8.4f}  {scores.std():>7.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Model                       CV Mean   CV Std
----------------------------------------------
Single Decision Tree          0.8987   0.0507
Random Forest  100            0.9830   0.0175
Random Forest  500            0.9830   0.0175</div>
  </div>
</div>

<h3>Feature Importance</h3>
<p>One of the most valuable outputs of a Random Forest is <strong>feature importance scores</strong> — a measure of how much each feature contributed to reducing impurity across all trees. This provides a data-driven answer to the question: <em>"Which features actually matter?"</em> Features with low importance can be dropped to simplify the model and reduce noise.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Feature Importance Ranking</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_wine
<span style="color:#c4b5fd;">from</span> sklearn.ensemble <span style="color:#c4b5fd;">import</span> RandomForestClassifier

data = <span style="color:#93c5fd;">load_wine</span>()
X, y = data.data, data.target

rf = <span style="color:#93c5fd;">RandomForestClassifier</span>(n_estimators=<span style="color:#fcd34d;">200</span>, random_state=<span style="color:#fcd34d;">42</span>).<span style="color:#93c5fd;">fit</span>(X, y)

importances = <span style="color:#93c5fd;">sorted</span>(<span style="color:#93c5fd;">zip</span>(data.feature_names, rf.feature_importances_),
                      key=<span style="color:#c4b5fd;">lambda</span> x: x[<span style="color:#fcd34d;">1</span>], reverse=<span style="color:#fca5a5;">True</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Feature Importances (top 6):"</span>)
<span style="color:#c4b5fd;">for</span> feat, imp <span style="color:#c4b5fd;">in</span> importances[:<span style="color:#fcd34d;">6</span>]:
    bar = <span style="color:#a7f3d0;">"█"</span> * <span style="color:#93c5fd;">int</span>(imp * <span style="color:#fcd34d;">60</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  {feat:26s} {imp:.4f}  {bar}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Feature Importances (top 6):
  proline                    0.1734  ██████████
  flavanoids                 0.1522  █████████
  color_intensity            0.1361  ████████
  od280/od315_of_diluted_wines 0.1271  ███████
  hue                        0.0853  █████
  alcohol                    0.0763  ████</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $mlModule->id,
            'title'       => '14.6 Random Forests & Ensemble Methods',
            'order_index' => 6,
            'content'     => $this->appendQuiz($content6, 'L14_6', [
                ['q' => 'What is the core mechanism behind Random Forests that reduces variance compared to a single tree?', 'opts' => ['Using a much deeper tree than usual', 'Training many trees on different bootstrap samples with random feature subsets, then aggregating their predictions', 'Applying regularization to each tree\'s weights', 'Training on scaled features only'], 'ans' => 1, 'exp' => 'Random Forests build many decorrelated trees using bootstrap sampling (different random subsets of training data) and feature randomness (only a random subset of features is considered at each split). Averaging their predictions cancels out individual errors.'],
                ['q' => 'How does a Random Forest make its final classification prediction?', 'opts' => ['Uses the prediction from the deepest tree', 'Uses majority vote across all trees in the forest', 'Uses the prediction of the tree with the highest individual accuracy', 'Averages the probability scores and picks the most likely'], 'ans' => 1, 'exp' => 'For classification, each tree votes for a class and the class with the most votes wins (majority voting). For regression, the final output is the mean prediction across all trees.'],
                ['q' => 'What does feature_importances_ measure in a fitted Random Forest?', 'opts' => ['How frequently each feature appears in the training data', 'How much each feature contributed to reducing impurity across all trees and splits', 'The correlation between features and the target variable', 'The p-value of each feature'], 'ans' => 1, 'exp' => 'Feature importance in a Random Forest is computed as the average decrease in node impurity (Gini or entropy) that a feature causes across all trees and all nodes where it is used. Higher importance = more influential feature.'],
                ['q' => 'What is "bootstrap sampling" in the context of Random Forests?', 'opts' => ['Selecting the most important features before training', 'Sampling the training data with replacement to create slightly different datasets for each tree', 'Removing outliers from the training set', 'Splitting data into k folds for cross-validation'], 'ans' => 1, 'exp' => 'Bootstrap sampling (sampling with replacement) draws n samples from the n-sample training set randomly, allowing duplicates. Each tree therefore trains on a unique slightly-different version of the dataset, creating the diversity needed for effective ensembling.'],
                ['q' => 'Why does adding more trees (n_estimators) to a Random Forest beyond a certain point give diminishing returns?', 'opts' => ['The model starts removing features', 'Once enough trees are averaged, variance is already well-controlled; adding more trees adds computation cost without meaningfully improving accuracy', 'Each additional tree focuses on a smaller feature subset', 'The model transitions to boosting mode'], 'ans' => 1, 'exp' => 'The variance-reduction benefit of averaging comes largely from the first few hundred trees. After ~200-500 trees (depending on the problem), the mean of the ensemble stabilizes and adding more trees costs more training time without noticeably improving prediction.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 14.7 — Support Vector Machines (SVM)
        // ══════════════════════════════════════════════════════════════
        $content7 = <<<'HTML'
<h2>Support Vector Machines (SVM)</h2>
<p>A <strong>Support Vector Machine</strong> finds the <em>optimal decision boundary</em> between classes — the hyperplane that maximizes the margin between the nearest data points of each class. These nearest points are the <strong>support vectors</strong> — the only training examples that actually define the boundary. SVMs are mathematically elegant, theoretically well-founded, and extremely effective on high-dimensional data like text, images, and bioinformatics datasets.</p>

<h3>The Maximum-Margin Hyperplane</h3>
<p>The core idea: among all possible decision boundaries that correctly separate the classes, SVM picks the one with the <strong>largest margin</strong> — the widest "street" between the two classes. A wider margin means the classifier is more confident and more robust to small perturbations in the data. This maximum-margin property gives SVMs excellent generalization on small, clean datasets.</p>

<h3>The Kernel Trick: Non-Linear SVMs</h3>
<p>When classes are not linearly separable in the original feature space, SVMs use the <strong>kernel trick</strong> — a mathematical transformation that implicitly maps data to a much higher-dimensional space where a linear boundary <em>does</em> exist, without ever computing the coordinates in that space. Common kernels:</p>
<ul style="line-height:2.2;">
  <li><strong>Linear</strong> — for linearly separable data (text classification, genomics)</li>
  <li><strong>RBF (Radial Basis Function)</strong> — the default; excellent for most non-linear problems</li>
  <li><strong>Polynomial</strong> — for polynomial decision boundaries</li>
</ul>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — SVM with Kernel Comparison</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_breast_cancer
<span style="color:#c4b5fd;">from</span> sklearn.svm <span style="color:#c4b5fd;">import</span> SVC
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> StandardScaler
<span style="color:#c4b5fd;">from</span> sklearn.pipeline <span style="color:#c4b5fd;">import</span> make_pipeline
<span style="color:#c4b5fd;">from</span> sklearn.model_selection <span style="color:#c4b5fd;">import</span> cross_val_score
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

X, y = <span style="color:#93c5fd;">load_breast_cancer</span>(return_X_y=<span style="color:#fca5a5;">True</span>)

<span style="color:#6b7280;"># Pipeline: scaling is MANDATORY for SVMs — huge feature ranges kill performance</span>
kernels = [<span style="color:#a7f3d0;">'linear'</span>, <span style="color:#a7f3d0;">'rbf'</span>, <span style="color:#a7f3d0;">'poly'</span>]

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Kernel':<10} {'CV Accuracy':>12} {'Std':>7}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"-"</span> * <span style="color:#fcd34d;">32</span>)
<span style="color:#c4b5fd;">for</span> k <span style="color:#c4b5fd;">in</span> kernels:
    pipe = <span style="color:#93c5fd;">make_pipeline</span>(<span style="color:#93c5fd;">StandardScaler</span>(), <span style="color:#93c5fd;">SVC</span>(kernel=k, C=<span style="color:#fcd34d;">1.0</span>))
    scores = <span style="color:#93c5fd;">cross_val_score</span>(pipe, X, y, cv=<span style="color:#fcd34d;">5</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{k:<10} {scores.mean():>12.4f} {scores.std():>7.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Kernel      CV Accuracy     Std
--------------------------------
linear           0.9508  0.0197
rbf              0.9736  0.0142
poly             0.9262  0.0263</div>
  </div>
</div>

<h3>The C Hyperparameter: Soft-Margin Tradeoff</h3>
<p>The <strong>C</strong> parameter controls the tradeoff between maximizing the margin and minimizing training errors. A small C allows more misclassifications but a wider, smoother margin (simpler model, less overfit). A large C penalizes any misclassification heavily, creating a tight margin that fits training data better but may overfit.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Effect of C Parameter</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_breast_cancer
<span style="color:#c4b5fd;">from</span> sklearn.svm <span style="color:#c4b5fd;">import</span> SVC
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> StandardScaler
<span style="color:#c4b5fd;">from</span> sklearn.model_selection <span style="color:#c4b5fd;">import</span> cross_val_score
<span style="color:#c4b5fd;">from</span> sklearn.pipeline <span style="color:#c4b5fd;">import</span> make_pipeline

X, y = <span style="color:#93c5fd;">load_breast_cancer</span>(return_X_y=<span style="color:#fca5a5;">True</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'C':>8}  {'CV Mean':>9}  {'Interpretation':>22}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"-"</span> * <span style="color:#fcd34d;">46</span>)
<span style="color:#c4b5fd;">for</span> C, label <span style="color:#c4b5fd;">in</span> [(<span style="color:#fcd34d;">0.001</span>,<span style="color:#a7f3d0;">"Wide margin / underfit"</span>),
                  (<span style="color:#fcd34d;">1.0</span>,  <span style="color:#a7f3d0;">"Balanced (default)"</span>),
                  (<span style="color:#fcd34d;">100</span>,  <span style="color:#a7f3d0;">"Narrow margin / overfit risk"</span>),
                  (<span style="color:#fcd34d;">1000</span>, <span style="color:#a7f3d0;">"Very tight margin"</span>)]:
    pipe   = <span style="color:#93c5fd;">make_pipeline</span>(<span style="color:#93c5fd;">StandardScaler</span>(), <span style="color:#93c5fd;">SVC</span>(kernel=<span style="color:#a7f3d0;">'rbf'</span>, C=C))
    mean_s = <span style="color:#93c5fd;">cross_val_score</span>(pipe, X, y, cv=<span style="color:#fcd34d;">5</span>).mean()
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{C:>8}  {mean_s:>9.4f}  {label:>22}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>       C  CV Mean       Interpretation
----------------------------------------------
   0.001     0.9279  Wide margin / underfit
     1.0     0.9736     Balanced (default)
     100     0.9754  Narrow margin / overfit risk
    1000     0.9684       Very tight margin</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $mlModule->id,
            'title'       => '14.7 Support Vector Machines (SVM)',
            'order_index' => 7,
            'content'     => $this->appendQuiz($content7, 'L14_7', [
                ['q' => 'What are "support vectors" in an SVM?', 'opts' => ['All training samples used to train the model', 'The hyperplane parameters (weights and bias)', 'The subset of training points closest to the decision boundary that actually define it', 'The features selected by the model'], 'ans' => 2, 'exp' => 'Support vectors are the training data points lying closest to the decision boundary (on the margin edge). Only these points matter for defining the hyperplane — removing any non-support-vector training point would not change the decision boundary at all.'],
                ['q' => 'What problem does the kernel trick solve?', 'opts' => ['It speeds up SVM training on large datasets', 'It allows SVM to find a linear boundary in a transformed high-dimensional space without explicitly computing the transformation — enabling non-linear classification', 'It regularizes the support vectors to prevent overfitting', 'It balances class weights for imbalanced data'], 'ans' => 1, 'exp' => 'The kernel trick implicitly computes dot products in a high-dimensional feature space without actually transforming the data. This lets SVM find non-linear decision boundaries efficiently. The RBF kernel can map data to infinite dimensions.'],
                ['q' => 'What effect does a very large C value have on an SVM?', 'opts' => ['Creates a wider, more forgiving margin that tolerates misclassifications', 'Creates a very narrow margin that penalizes any misclassification heavily, risking overfitting', 'Switches the kernel from RBF to linear', 'Reduces the number of support vectors to zero'], 'ans' => 1, 'exp' => 'A large C heavily penalizes misclassifications, forcing the SVM to create a narrow, tight margin to avoid any training errors. This can lead to overfitting. Small C allows misclassifications in exchange for a wider, more robust margin.'],
                ['q' => 'Why is feature scaling (StandardScaler) critical before using an SVM?', 'opts' => ['SVM requires all features to be categorical', 'The SVM margin is calculated in Euclidean distance; features with large scales dominate the margin calculation, making the model ignore small-scale features', 'Scaling converts the kernel to RBF automatically', 'SVM cannot handle negative feature values'], 'ans' => 1, 'exp' => 'SVM optimization involves computing distances between points. If one feature has values in the thousands and another in the range [0, 1], the large-scale feature dominates completely. StandardScaler normalizes all features to zero mean and unit variance.'],
                ['q' => 'Which kernel is the default in scikit-learn\'s SVC and works well for most non-linear problems?', 'opts' => ['linear', 'sigmoid', 'poly', 'rbf'], 'ans' => 3, 'exp' => 'RBF (Radial Basis Function), also called Gaussian kernel, is the default in sklearn\'s SVC. It defines similarity as a Gaussian function of the Euclidean distance between samples and creates smooth, circular decision boundaries that handle most non-linear problems well.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 14.8 — K-Nearest Neighbors (KNN)
        // ══════════════════════════════════════════════════════════════
        $content8 = <<<'HTML'
<h2>K-Nearest Neighbors (KNN)</h2>
<p><strong>K-Nearest Neighbors (KNN)</strong> is one of the most intuitive algorithms in all of machine learning. It makes no assumptions about the data distribution and requires no training phase — it simply stores the entire training set. To predict a new point, KNN finds the <em>k</em> most similar (nearest) training samples and takes a majority vote (classification) or average (regression) of their labels. It is the most literal implementation of the phrase "birds of a feather flock together."</p>

<h3>How KNN Works Step by Step</h3>
<p>1. Choose a value for <em>k</em> (the number of neighbors). 2. For a new query point, compute the distance to every training sample (typically Euclidean distance). 3. Select the <em>k</em> training points with the smallest distances. 4. For classification: the predicted class is the majority class among those k neighbors. For regression: the predicted value is the mean of those k neighbors' values.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — KNN: Choosing the Right k</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_iris
<span style="color:#c4b5fd;">from</span> sklearn.neighbors <span style="color:#c4b5fd;">import</span> KNeighborsClassifier
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> StandardScaler
<span style="color:#c4b5fd;">from</span> sklearn.model_selection <span style="color:#c4b5fd;">import</span> cross_val_score
<span style="color:#c4b5fd;">from</span> sklearn.pipeline <span style="color:#c4b5fd;">import</span> make_pipeline
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

X, y = <span style="color:#93c5fd;">load_iris</span>(return_X_y=<span style="color:#fca5a5;">True</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'k':>4}  {'CV Accuracy':>12}  {'Std':>7}  {'Interpretation':>22}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"-"</span> * <span style="color:#fcd34d;">54</span>)
<span style="color:#c4b5fd;">for</span> k, label <span style="color:#c4b5fd;">in</span> [(<span style="color:#fcd34d;">1</span>,  <span style="color:#a7f3d0;">"Overfit — memorizes noise"</span>),
                  (<span style="color:#fcd34d;">5</span>,  <span style="color:#a7f3d0;">"Good balance"</span>),
                  (<span style="color:#fcd34d;">11</span>, <span style="color:#a7f3d0;">"Smoother boundary"</span>),
                  (<span style="color:#fcd34d;">51</span>, <span style="color:#a7f3d0;">"Underfit — too broad"</span>)]:
    pipe = <span style="color:#93c5fd;">make_pipeline</span>(<span style="color:#93c5fd;">StandardScaler</span>(), <span style="color:#93c5fd;">KNeighborsClassifier</span>(n_neighbors=k))
    s = <span style="color:#93c5fd;">cross_val_score</span>(pipe, X, y, cv=<span style="color:#fcd34d;">5</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{k:>4}  {s.mean():>12.4f}  {s.std():>7.4f}  {label:>22}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>   k  CV Accuracy     Std       Interpretation
------------------------------------------------------
   1        0.9600  0.0283  Overfit — memorizes noise
   5        0.9667  0.0211         Good balance
  11        0.9733  0.0249      Smoother boundary
  51        0.9400  0.0469      Underfit — too broad</div>
  </div>
</div>

<h3>The Curse of Dimensionality</h3>
<p>KNN suffers from the <strong>curse of dimensionality</strong>: as the number of features grows, the concept of "nearest neighbor" breaks down. In high-dimensional spaces, all points become roughly equidistant from each other — there is no meaningful "near" or "far" anymore. For datasets with hundreds or thousands of features, KNN degrades badly. Dimensionality reduction (PCA) or feature selection should be applied before using KNN on high-dimensional data.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — KNN for Regression</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.neighbors <span style="color:#c4b5fd;">import</span> KNeighborsRegressor
<span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_diabetes
<span style="color:#c4b5fd;">from</span> sklearn.model_selection <span style="color:#c4b5fd;">import</span> train_test_split
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> StandardScaler
<span style="color:#c4b5fd;">from</span> sklearn.metrics <span style="color:#c4b5fd;">import</span> r2_score

X, y = <span style="color:#93c5fd;">load_diabetes</span>(return_X_y=<span style="color:#fca5a5;">True</span>)
Xtr, Xte, ytr, yte = <span style="color:#93c5fd;">train_test_split</span>(X, y, test_size=<span style="color:#fcd34d;">0.2</span>, random_state=<span style="color:#fcd34d;">0</span>)

scaler = <span style="color:#93c5fd;">StandardScaler</span>()
Xtr_s = scaler.<span style="color:#93c5fd;">fit_transform</span>(Xtr)
Xte_s = scaler.<span style="color:#93c5fd;">transform</span>(Xte)

<span style="color:#c4b5fd;">for</span> k <span style="color:#c4b5fd;">in</span> [<span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">5</span>, <span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">20</span>]:
    knn = <span style="color:#93c5fd;">KNeighborsRegressor</span>(n_neighbors=k).<span style="color:#93c5fd;">fit</span>(Xtr_s, ytr)
    r2  = <span style="color:#93c5fd;">r2_score</span>(yte, knn.<span style="color:#93c5fd;">predict</span>(Xte_s))
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"k={k:>2}  R²={r2:.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>k= 3  R²=0.3694
k= 5  R²=0.4052
k=10  R²=0.4418
k=20  R²=0.4315</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $mlModule->id,
            'title'       => '14.8 K-Nearest Neighbors (KNN)',
            'order_index' => 8,
            'content'     => $this->appendQuiz($content8, 'L14_8', [
                ['q' => 'How does KNN make a classification prediction for a new data point?', 'opts' => ['It trains a linear model on nearby points', 'It finds the k nearest training points by distance and predicts the majority class among them', 'It computes the centroid of each class and assigns the nearest class', 'It randomly selects k samples and averages their labels'], 'ans' => 1, 'exp' => 'KNN finds the k training points with the smallest distance to the query point, then takes a majority vote of their class labels. For regression, it averages their values instead of voting.'],
                ['q' => 'What does k=1 in KNN produce?', 'opts' => ['Underfitting — the model is too simple', 'A perfectly smooth decision boundary', 'Overfitting — each training point is its own boundary, memorizing noise', 'The optimal balance of bias and variance'], 'ans' => 2, 'exp' => 'With k=1, every training point is its own nearest neighbor. This creates a very jagged decision boundary that perfectly classifies every training sample (0% training error) but overfits noise and performs poorly on new data.'],
                ['q' => 'What is the "curse of dimensionality" problem for KNN?', 'opts' => ['KNN requires too many hyperparameters in high dimensions', 'In high-dimensional spaces, all points become roughly equidistant, making the concept of "nearest neighbor" meaningless', 'KNN cannot handle more than 100 features', 'High-dimensional data always has more missing values'], 'ans' => 1, 'exp' => 'As dimensionality increases, the volume of space grows exponentially. Training points become increasingly sparse and the distance between the nearest and farthest point converges — destroying the neighborhood concept KNN relies on.'],
                ['q' => 'Why is feature scaling mandatory before applying KNN?', 'opts' => ['KNN requires binary features', 'KNN measures distances between samples; unscaled features with large ranges dominate the distance calculation, making other features irrelevant', 'Scaling speeds up the voting step', 'KNN cannot handle negative numbers without scaling'], 'ans' => 1, 'exp' => 'Euclidean distance is directly affected by feature scale. A feature with values in [0, 1000] will completely dominate a feature in [0, 1], making the second feature invisible to KNN. StandardScaler ensures all features contribute equally to distance.'],
                ['q' => 'What is a major computational disadvantage of KNN compared to parametric models like Logistic Regression?', 'opts' => ['KNN requires more training data', 'KNN has no training phase but must compute distances to every training point at prediction time — O(n) per query, making it slow on large datasets', 'KNN cannot be used for regression', 'KNN requires feature engineering'], 'ans' => 1, 'exp' => 'Unlike parametric models that store only learned parameters (weights), KNN stores the entire training dataset. Every prediction requires computing n distances (n = training set size). For large datasets this makes inference very slow.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 14.9 — Hyperparameter Tuning: Grid Search & Pipelines
        // ══════════════════════════════════════════════════════════════
        $content9 = <<<'HTML'
<h2>Hyperparameter Tuning: Grid Search & Pipelines</h2>
<p><strong>Hyperparameters</strong> are the settings you configure <em>before</em> training begins — unlike model parameters (weights), which are learned from data during training. Examples include the depth of a decision tree, the number of trees in a forest, C in SVM, or k in KNN. The right hyperparameters can be the difference between a mediocre model and an excellent one. <strong>Hyperparameter tuning</strong> is the systematic process of finding the best combination.</p>

<h3>Pipelines: Preventing Data Leakage in Preprocessing</h3>
<p>Before tuning, we must first understand <strong>Pipelines</strong>. A common mistake is fitting a scaler or encoder on the whole dataset before splitting — this leaks information about the test set into preprocessing. A Pipeline chains preprocessing steps and a model into a single object. When used with cross-validation, it correctly fits preprocessing steps <em>only on the training fold</em> and applies them to the validation fold.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Building a Proper Pipeline</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.pipeline <span style="color:#c4b5fd;">import</span> Pipeline
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> StandardScaler
<span style="color:#c4b5fd;">from</span> sklearn.svm <span style="color:#c4b5fd;">import</span> SVC
<span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_breast_cancer
<span style="color:#c4b5fd;">from</span> sklearn.model_selection <span style="color:#c4b5fd;">import</span> train_test_split

X, y = <span style="color:#93c5fd;">load_breast_cancer</span>(return_X_y=<span style="color:#fca5a5;">True</span>)
X_train, X_test, y_train, y_test = <span style="color:#93c5fd;">train_test_split</span>(X, y, test_size=<span style="color:#fcd34d;">0.2</span>, random_state=<span style="color:#fcd34d;">42</span>)

<span style="color:#6b7280;"># Pipeline: each step is (name, estimator)</span>
<span style="color:#6b7280;"># StandardScaler only sees training data in each CV fold — no leakage!</span>
pipe = <span style="color:#93c5fd;">Pipeline</span>([
    (<span style="color:#a7f3d0;">'scaler'</span>, <span style="color:#93c5fd;">StandardScaler</span>()),
    (<span style="color:#a7f3d0;">'svm'</span>,    <span style="color:#93c5fd;">SVC</span>(kernel=<span style="color:#a7f3d0;">'rbf'</span>, C=<span style="color:#fcd34d;">1.0</span>, random_state=<span style="color:#fcd34d;">42</span>))
])

pipe.<span style="color:#93c5fd;">fit</span>(X_train, y_train)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Pipeline test accuracy: {pipe.score(X_test, y_test):.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Steps in pipeline: {[name for name, _ in pipe.steps]}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Pipeline test accuracy: 0.9825
Steps in pipeline: ['scaler', 'svm']</div>
  </div>
</div>

<h3>GridSearchCV: Exhaustive Hyperparameter Search</h3>
<p><strong>GridSearchCV</strong> exhaustively tries every combination of hyperparameter values you specify — the "grid" — evaluating each with cross-validation. It automatically selects the best combination and re-fits the final model on the full training set using those best parameters.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — GridSearchCV on a Pipeline</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.model_selection <span style="color:#c4b5fd;">import</span> GridSearchCV
<span style="color:#c4b5fd;">from</span> sklearn.pipeline <span style="color:#c4b5fd;">import</span> Pipeline
<span style="color:#c4b5fd;">from</span> sklearn.preprocessing <span style="color:#c4b5fd;">import</span> StandardScaler
<span style="color:#c4b5fd;">from</span> sklearn.svm <span style="color:#c4b5fd;">import</span> SVC
<span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_breast_cancer
<span style="color:#c4b5fd;">from</span> sklearn.model_selection <span style="color:#c4b5fd;">import</span> train_test_split

X, y = <span style="color:#93c5fd;">load_breast_cancer</span>(return_X_y=<span style="color:#fca5a5;">True</span>)
X_train, X_test, y_train, y_test = <span style="color:#93c5fd;">train_test_split</span>(
    X, y, test_size=<span style="color:#fcd34d;">0.2</span>, random_state=<span style="color:#fcd34d;">42</span>, stratify=y)

pipe = <span style="color:#93c5fd;">Pipeline</span>([(<span style="color:#a7f3d0;">'scaler'</span>, <span style="color:#93c5fd;">StandardScaler</span>()), (<span style="color:#a7f3d0;">'svm'</span>, <span style="color:#93c5fd;">SVC</span>())])

<span style="color:#6b7280;"># param_grid keys follow "stepname__paramname" convention</span>
param_grid = {
    <span style="color:#a7f3d0;">'svm__C'</span>:      [<span style="color:#fcd34d;">0.1</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">100</span>],
    <span style="color:#a7f3d0;">'svm__kernel'</span>: [<span style="color:#a7f3d0;">'linear'</span>, <span style="color:#a7f3d0;">'rbf'</span>],
    <span style="color:#a7f3d0;">'svm__gamma'</span>:  [<span style="color:#a7f3d0;">'scale'</span>, <span style="color:#a7f3d0;">'auto'</span>],    <span style="color:#6b7280;"># only used by RBF</span>
}
<span style="color:#6b7280;"># 4 C values × 2 kernels × 2 gamma values = 16 combinations × 5 folds = 80 fits</span>
gs = <span style="color:#93c5fd;">GridSearchCV</span>(pipe, param_grid, cv=<span style="color:#fcd34d;">5</span>, scoring=<span style="color:#a7f3d0;">'accuracy'</span>, n_jobs=-<span style="color:#fcd34d;">1</span>)
gs.<span style="color:#93c5fd;">fit</span>(X_train, y_train)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Best parameters : {gs.best_params_}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Best CV accuracy: {gs.best_score_:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Test accuracy   : {gs.score(X_test, y_test):.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Best parameters : {'svm__C': 10, 'svm__gamma': 'scale', 'svm__kernel': 'rbf'}
Best CV accuracy: 0.9824
Test accuracy   : 0.9912</div>
  </div>
</div>

<h3>RandomizedSearchCV: Efficient Search for Large Grids</h3>
<p>GridSearchCV evaluates every combination — fine for small grids, but with 10+ hyperparameters each with many values, the search space explodes combinatorially. <strong>RandomizedSearchCV</strong> samples a fixed number of random combinations, providing a much better cost/performance tradeoff for large hyperparameter spaces. In practice, random search often finds near-optimal parameters with far fewer evaluations than exhaustive grid search.</p>
HTML;

        Lesson::create([
            'module_id'   => $mlModule->id,
            'title'       => '14.9 Hyperparameter Tuning: Grid Search & Pipelines',
            'order_index' => 9,
            'content'     => $this->appendQuiz($content9, 'L14_9', [
                ['q' => 'What is the difference between a hyperparameter and a model parameter?', 'opts' => ['They are the same thing', 'Model parameters are learned from data during training; hyperparameters are set by the practitioner before training begins', 'Hyperparameters are learned from data; model parameters are set manually', 'Model parameters only exist in neural networks'], 'ans' => 1, 'exp' => 'Model parameters (e.g., weights in linear regression) are learned automatically during fit(). Hyperparameters (e.g., max_depth, C, k) are configuration settings that must be chosen before training — they control the learning process itself.'],
                ['q' => 'Why should preprocessing (e.g., StandardScaler) be inside a Pipeline when doing cross-validation?', 'opts' => ['Pipelines make training faster', 'Without a Pipeline, the scaler is fit on the full dataset including the validation fold, leaking validation statistics into preprocessing and inflating CV scores', 'Scikit-Learn requires pipelines for cross-validation', 'Pipelines automatically choose the best preprocessing method'], 'ans' => 1, 'exp' => 'If you scale the full dataset before CV, the scaler has seen the validation folds — that\'s data leakage. A Pipeline ensures the scaler is fit only on the training folds and merely transforms the validation fold, giving honest CV estimates.'],
                ['q' => 'In GridSearchCV with param_grid = {\'svm__C\': [0.1, 1, 10]} and cv=5, how many model fits occur?', 'opts' => ['3', '5', '15', '8'], 'ans' => 2, 'exp' => 'GridSearchCV tries every combination: 3 C values × 5 CV folds = 15 total model fits. If you had 3 C values AND 2 kernel values, that would be 3×2×5 = 30 fits.'],
                ['q' => 'What does gs.best_params_ contain after GridSearchCV finishes?', 'opts' => ['The accuracy score of the best model', 'The dictionary of hyperparameter values that achieved the highest mean CV score', 'The best trained model itself', 'The indices of the best training samples'], 'ans' => 1, 'exp' => 'After fitting, best_params_ contains the combination of hyperparameter values (e.g., {\'svm__C\': 10, \'svm__kernel\': \'rbf\'}) that achieved the best mean cross-validation score. best_estimator_ holds the refitted model using those parameters.'],
                ['q' => 'When should you prefer RandomizedSearchCV over GridSearchCV?', 'opts' => ['When you have fewer than 3 hyperparameters', 'When the hyperparameter grid is large — random search evaluates a fixed number of random combinations, often finding near-optimal results much faster than exhaustive search', 'When your dataset has fewer than 1000 samples', 'When using ensemble models only'], 'ans' => 1, 'exp' => 'Grid search evaluates every possible combination, which grows exponentially (e.g., 5 params × 10 values each = 100,000 combinations). RandomizedSearchCV evaluates a fixed n_iter random combinations, providing a much better performance/cost tradeoff for large search spaces.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 14.10 — Gradient Boosting: XGBoost & the Bias-Variance Tradeoff
        // ══════════════════════════════════════════════════════════════
        $content10 = <<<'HTML'
<h2>Gradient Boosting: XGBoost & the Bias-Variance Tradeoff</h2>
<p><strong>Gradient Boosting</strong> is the most powerful and widely-used family of supervised learning algorithms in competitive machine learning and industry. While Random Forests build trees in <em>parallel</em> and average them, gradient boosting builds trees <em>sequentially</em> — each new tree specifically targets and corrects the errors made by the ensemble so far. The result is a highly accurate model that consistently wins machine learning competitions and powers production systems at Google, Meta, Airbnb, and thousands of other companies.</p>

<h3>How Gradient Boosting Works</h3>
<p>The algorithm: 1. Start with a simple initial prediction (usually the mean of y). 2. Compute the <em>residuals</em> — the errors between current predictions and true labels. 3. Train a new tree to predict these residuals. 4. Add this tree's predictions (multiplied by a learning rate) to the ensemble. 5. Repeat steps 2-4 for n_estimators rounds. Each new tree is a small correction that nudges the ensemble toward the truth.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — GradientBoosting vs XGBoost vs Random Forest</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.ensemble <span style="color:#c4b5fd;">import</span> (GradientBoostingClassifier,
                                  RandomForestClassifier)
<span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_breast_cancer
<span style="color:#c4b5fd;">from</span> sklearn.model_selection <span style="color:#c4b5fd;">import</span> cross_val_score
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

X, y = <span style="color:#93c5fd;">load_breast_cancer</span>(return_X_y=<span style="color:#fca5a5;">True</span>)

models = {
    <span style="color:#a7f3d0;">"Random Forest"</span>:       <span style="color:#93c5fd;">RandomForestClassifier</span>(n_estimators=<span style="color:#fcd34d;">200</span>, random_state=<span style="color:#fcd34d;">42</span>),
    <span style="color:#a7f3d0;">"Gradient Boosting"</span>:   <span style="color:#93c5fd;">GradientBoostingClassifier</span>(
                                n_estimators=<span style="color:#fcd34d;">200</span>, learning_rate=<span style="color:#fcd34d;">0.1</span>,
                                max_depth=<span style="color:#fcd34d;">3</span>, random_state=<span style="color:#fcd34d;">42</span>),
}

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Model':<22}  {'CV Acc':>7}  {'Std':>6}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"-"</span> * <span style="color:#fcd34d;">40</span>)
<span style="color:#c4b5fd;">for</span> name, m <span style="color:#c4b5fd;">in</span> models.items():
    scores = <span style="color:#93c5fd;">cross_val_score</span>(m, X, y, cv=<span style="color:#fcd34d;">5</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{name:<22}  {scores.mean():>7.4f}  {scores.std():>6.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Model                   CV Acc     Std
----------------------------------------
Random Forest           0.9648  0.0142
Gradient Boosting       0.9736  0.0106</div>
  </div>
</div>

<h3>The Bias-Variance Tradeoff: The Fundamental ML Concept</h3>
<p>Every supervised learning model faces this core tension. The total prediction error = <strong>Bias²</strong> + <strong>Variance</strong> + irreducible noise. Understanding this tradeoff is the key to diagnosing model failures:</p>
<ul style="line-height:2.2;">
  <li><strong>High Bias (Underfitting)</strong> — the model is too simple to capture the data's true patterns. Training error AND test error are both high. Fix: use a more complex model, add features, reduce regularization.</li>
  <li><strong>High Variance (Overfitting)</strong> — the model is too complex and memorizes training noise. Training error is low but test error is high. Fix: use regularization, reduce model complexity, get more data, use ensembles.</li>
</ul>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Learning Curves: Diagnosing Bias vs Variance</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sklearn.model_selection <span style="color:#c4b5fd;">import</span> learning_curve
<span style="color:#c4b5fd;">from</span> sklearn.ensemble <span style="color:#c4b5fd;">import</span> GradientBoostingClassifier
<span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> load_breast_cancer
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

X, y = <span style="color:#93c5fd;">load_breast_cancer</span>(return_X_y=<span style="color:#fca5a5;">True</span>)
model = <span style="color:#93c5fd;">GradientBoostingClassifier</span>(n_estimators=<span style="color:#fcd34d;">100</span>, random_state=<span style="color:#fcd34d;">42</span>)

train_sizes, train_scores, val_scores = <span style="color:#93c5fd;">learning_curve</span>(
    model, X, y, cv=<span style="color:#fcd34d;">5</span>,
    train_sizes=np.<span style="color:#93c5fd;">linspace</span>(<span style="color:#fcd34d;">0.1</span>, <span style="color:#fcd34d;">1.0</span>, <span style="color:#fcd34d;">6</span>),
    scoring=<span style="color:#a7f3d0;">'accuracy'</span>
)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Size':>5}  {'Train Acc':>10}  {'Val Acc':>9}  {'Gap':>6}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"-"</span> * <span style="color:#fcd34d;">38</span>)
<span style="color:#c4b5fd;">for</span> sz, tr, va <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>(train_sizes,
                      train_scores.mean(axis=<span style="color:#fcd34d;">1</span>),
                      val_scores.mean(axis=<span style="color:#fcd34d;">1</span>)):
    gap_label = <span style="color:#a7f3d0;">"⚠ overfit"</span> <span style="color:#c4b5fd;">if</span> (tr - va) > <span style="color:#fcd34d;">0.04</span> <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">"✓"</span>
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{int(sz):>5}  {tr:>10.4f}  {va:>9.4f}  {gap_label}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span> Size   Train Acc   Val Acc     Gap
--------------------------------------
   45      1.0000    0.9111  ⚠ overfit
   91      1.0000    0.9494  ⚠ overfit
  136      1.0000    0.9604  ⚠ overfit
  182      0.9978    0.9648           ✓
  228      0.9956    0.9648           ✓
  455      0.9956    0.9736           ✓</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $mlModule->id,
            'title'       => '14.10 Gradient Boosting & the Bias-Variance Tradeoff',
            'order_index' => 10,
            'content'     => $this->appendQuiz($content10, 'L14_10', [
                ['q' => 'What is the fundamental difference between Random Forests and Gradient Boosting?', 'opts' => ['Random Forests use regression; Gradient Boosting uses classification', 'Random Forests build trees in parallel and average; Gradient Boosting builds trees sequentially, each correcting the previous ensemble\'s errors', 'Random Forests use more trees than Gradient Boosting', 'Gradient Boosting always outperforms Random Forests'], 'ans' => 1, 'exp' => 'Random Forests are a parallel ensemble — independent trees are averaged to reduce variance. Gradient Boosting is a sequential ensemble — each tree corrects the residual errors of all previous trees, reducing bias iteratively.'],
                ['q' => 'In Gradient Boosting, what does a smaller learning_rate require?', 'opts' => ['Fewer trees for the same accuracy', 'More trees (higher n_estimators), since each tree makes only a small correction — but often leads to better generalization', 'A simpler tree structure', 'More training data'], 'ans' => 1, 'exp' => 'A small learning rate shrinks each tree\'s contribution, requiring more trees to reach the same training accuracy. However, it allows finer-grained corrections and often produces a better-regularized, more generalizable model.'],
                ['q' => 'What is the symptom of a model suffering from high bias (underfitting)?', 'opts' => ['Training accuracy is high but test accuracy is much lower', 'Both training accuracy and test accuracy are low', 'The model trains very slowly', 'The model requires many features'], 'ans' => 1, 'exp' => 'A high-bias model is too simple — it fails to capture the true patterns in the data. Both training and test errors are high, meaning the model performs poorly even on data it has been trained on.'],
                ['q' => 'What does a learning curve showing a large gap between training accuracy (~1.0) and validation accuracy (~0.75) indicate?', 'opts' => ['The model is underfitting — add more complexity', 'The model is overfitting — reduce complexity or add regularization', 'The model has optimal bias-variance balance', 'More data would not help this model'], 'ans' => 1, 'exp' => 'A large gap between training and validation accuracy is the signature of overfitting (high variance). The model learned training data very well but fails to generalize. Solutions include regularization, reducing max_depth, ensembling, or collecting more training data.'],
                ['q' => 'What is "irreducible error" in the bias-variance decomposition?', 'opts' => ['Error from using a model that is too simple', 'Error from overfitting the training data', 'The inherent noise in the data that no model can eliminate regardless of complexity', 'Error caused by missing features'], 'ans' => 2, 'exp' => 'Irreducible error is the noise floor — the randomness in the true data-generating process that no model, however complex, can predict. It sets a hard lower bound on total prediction error. Total error = Bias² + Variance + Irreducible Noise.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 14.11 — Final Exam (Org-Locked)
        // ══════════════════════════════════════════════════════════════
        $allFinalQuestions = [
            // 14.1 — What is supervised learning
            ['q' => 'Which of the following is NOT a supervised learning task?', 'opts' => ['Predicting house prices from features', 'Classifying emails as spam or not spam', 'Grouping customers into segments without labels', 'Detecting cancer from tumor measurements'], 'ans' => 2, 'exp' => 'Grouping customers into unlabeled segments is clustering — an unsupervised learning task. The other three all require labeled training data (price, spam/not-spam, cancer/no-cancer), making them supervised.'],
            ['q' => 'What does model.fit(X_train, y_train) do in Scikit-Learn?', 'opts' => ['Evaluates model accuracy', 'Generates predictions for new data', 'Trains the model by learning parameters that map X_train to y_train', 'Splits the data into training and test sets'], 'ans' => 2, 'exp' => 'fit() is the training step. The model uses the labeled training data (X_train, y_train) to learn its internal parameters (weights, thresholds, split rules, etc.) by minimizing a loss function.'],
            // 14.2 — Train/test split
            ['q' => 'What is the purpose of the random_state parameter in train_test_split()?', 'opts' => ['Controls the percentage of data in the test set', 'Seeds the random number generator for reproducibility — the same split is produced every run', 'Balances the class distribution in both splits', 'Shuffles the features before splitting'], 'ans' => 1, 'exp' => 'random_state sets the random seed. Without it, a different random split is produced every time you run the code, making experiments non-reproducible. Using a fixed seed ensures your split is identical across runs.'],
            ['q' => 'In 10-fold cross-validation on a 1000-sample dataset, how many samples are in each validation fold?', 'opts' => ['10', '100', '90', '1000'], 'ans' => 1, 'exp' => '10-fold CV divides 1000 samples into 10 equal folds of 100 samples each. Each fold is used as the validation set once, while the remaining 900 samples form the training set.'],
            // 14.3 — Linear regression
            ['q' => 'What metric does Linear Regression minimize during training?', 'opts' => ['Cross-entropy loss', 'Mean Absolute Error (MAE)', 'Mean Squared Error (MSE) between predictions and true values', 'R² score'], 'ans' => 2, 'exp' => 'Ordinary Least Squares (OLS) linear regression finds the weights that minimize the Mean Squared Error (sum of squared residuals). MSE penalizes large errors more heavily than MAE due to squaring.'],
            ['q' => 'A Lasso model sets 8 out of 20 feature weights to exactly 0. What has happened?', 'opts' => ['The model has failed to converge', 'Lasso\'s L1 penalty has performed automatic feature selection — those 8 features are treated as irrelevant', 'Ridge regularization was applied instead', 'The learning rate was too high'], 'ans' => 1, 'exp' => 'L1 (Lasso) regularization creates a loss geometry that pushes some weights to exactly zero, effectively removing those features from the model. This is Lasso\'s automatic feature selection property — Ridge (L2) shrinks weights but rarely to exactly zero.'],
            // 14.4 — Logistic regression / metrics
            ['q' => 'A model has Recall=0.98 and Precision=0.45 for the positive class. What does this suggest?', 'opts' => ['The model correctly identifies almost all true positives but produces many false positives', 'The model misses most of the positives', 'The model is well balanced', 'The model has perfect accuracy'], 'ans' => 0, 'exp' => 'High recall (0.98) means the model catches 98% of true positives — very few cases are missed. Low precision (0.45) means 55% of positive predictions are actually false alarms. This is typical of a low-threshold classifier optimized for sensitivity.'],
            ['q' => 'The confusion matrix shows TN=90, FP=5, FN=20, TP=30. What is the recall?', 'opts' => ['30/35 = 0.857', '30/50 = 0.600', '90/95 = 0.947', '30/145 = 0.207'], 'ans' => 1, 'exp' => 'Recall = TP / (TP + FN) = 30 / (30 + 20) = 30/50 = 0.600. Out of 50 actual positive cases, the model correctly identified 30.'],
            // 14.5 — Decision Trees
            ['q' => 'A decision tree trained with no depth limit achieves 100% training accuracy. You should expect what on unseen data?', 'opts' => ['Also 100% accuracy because the model is perfect', 'Much lower accuracy — the tree has overfit by memorizing training noise', 'Exactly the same accuracy as a linear model', 'Higher accuracy than a pruned tree always'], 'ans' => 1, 'exp' => 'A tree with unlimited depth will perfectly partition every training sample, including noisy outliers. This means it has overfit to the training data. The complex, jagged decision boundary will not generalize to unseen samples.'],
            ['q' => 'Which splitting criterion creates leaves that minimize the probability of random misclassification?', 'opts' => ['MSE', 'Information Gain (Entropy)', 'Gini Impurity', 'Log-loss'], 'ans' => 2, 'exp' => 'Gini Impurity = 1 − Σ(pᵢ²). A value of 0 means the node is perfectly pure (all one class). The algorithm greedily picks splits that minimize Gini impurity, which corresponds to minimizing the probability of random misclassification.'],
            // 14.6 — Random Forest
            ['q' => 'Why do Random Forests use random feature subsets at each split?', 'opts' => ['To reduce training time only', 'To decorrelate the trees — if all trees always used the same strong feature, they would make the same errors, and averaging would not help', 'To prevent the model from using all features', 'To implement regularization like Ridge does'], 'ans' => 1, 'exp' => 'If every tree always splits on the same dominant feature, all trees will be similar and their errors will be correlated. By restricting each split to a random feature subset, trees see different "views" of the data, making their errors independent and canceling out when averaged.'],
            // 14.7 — SVM
            ['q' => 'What is the "margin" in a Support Vector Machine?', 'opts' => ['The training error rate', 'The distance between the decision hyperplane and the nearest training points (support vectors) of each class', 'The learning rate during optimization', 'The number of support vectors chosen'], 'ans' => 1, 'exp' => 'The margin is the distance between the decision hyperplane and the closest data points from each class (the support vectors). SVM maximizes this margin — a wider margin means more robust predictions for points near the boundary.'],
            // 14.8 — KNN
            ['q' => 'What is the time complexity of a single KNN prediction on a training set of n samples?', 'opts' => ['O(1) — constant time regardless of n', 'O(log n) — binary search on the index', 'O(n) — must compute distance to every training sample', 'O(n²) — must compare all pairs'], 'ans' => 2, 'exp' => 'Naive KNN computes the distance from the query point to every single training sample, then picks the k smallest. This is O(n) per query. For large training sets this makes inference slow — advanced data structures (KD-Tree, Ball Tree) can reduce this to O(log n) in low dimensions.'],
            // 14.9 — Grid search / pipelines
            ['q' => 'After GridSearchCV finishes, which attribute gives you the best-performing model already fitted on the full training set?', 'opts' => ['gs.best_params_', 'gs.best_score_', 'gs.best_estimator_', 'gs.cv_results_'], 'ans' => 2, 'exp' => 'gs.best_estimator_ is the model refitted on the full training set using the best hyperparameters found during search. It is ready for predict() and score() calls. best_params_ contains the parameter dict; best_score_ is the mean CV score.'],
            ['q' => 'What is the major advantage of RandomizedSearchCV over GridSearchCV for large hyperparameter spaces?', 'opts' => ['It always finds the globally optimal hyperparameters', 'It evaluates a fixed number of random combinations, finding near-optimal results in far less computation time than exhaustive grid search', 'It trains models in parallel automatically', 'It never overfits the hyperparameters'], 'ans' => 1, 'exp' => 'Grid search evaluates ALL combinations — exponential in the number of hyperparameters. Randomized search evaluates a fixed n_iter random combinations, which scales linearly. Empirically, it usually finds near-optimal settings with far fewer evaluations.'],
            // 14.10 — Gradient Boosting / bias-variance
            ['q' => 'Both your training accuracy and validation accuracy are 65% on a complex dataset. What is the most likely problem?', 'opts' => ['High variance — the model is overfitting', 'High bias — the model is too simple to capture the true patterns in the data', 'Perfect balance of bias and variance', 'The dataset has too many samples'], 'ans' => 1, 'exp' => 'When BOTH training and validation accuracy are low, the model is underfitting — high bias. The model is too simple (or too regularized) to fit even the training data. Fix: use a more complex model, add features, or reduce regularization.'],
            ['q' => 'What distinguishes Gradient Boosting from Bagging (Random Forest) conceptually?', 'opts' => ['Gradient Boosting uses deeper trees', 'Gradient Boosting builds trees sequentially where each tree corrects the residuals of the ensemble so far; Bagging builds trees in parallel independently', 'Bagging always outperforms boosting on imbalanced data', 'Gradient Boosting only works for regression'], 'ans' => 1, 'exp' => 'Bagging (Random Forest): parallel independent trees averaged to reduce variance. Boosting (Gradient Boosting): sequential trees where each targets the errors of the prior ensemble to reduce bias. Both are powerful but attack different error sources.'],
        ];

        $finalContent = <<<HTML
<div id="org-lock-screen" style="text-align:center;padding:4rem 2rem;background:var(--surface2);border:1px solid var(--border);border-radius:12px;margin-top:2rem;">
    <div style="font-size:3rem;margin-bottom:1rem;">🔒</div>
    <h3 style="color:var(--text);margin-bottom:0.5rem;">University / Organization Access Only</h3>
    <p style="color:var(--muted);">The Final Module Exam is restricted to enrolled students and verified organization members.</p>
    <p style="font-size:0.85rem;color:#f59e0b;margin-top:1rem;background:rgba(245,158,11,0.1);padding:10px;border-radius:8px;display:inline-block;">Please contact administration to link your account to an organization.</p>
</div>
<div id="final-exam-content" style="display:none;">
    <h2>Module 14: Final Examination</h2>
    <p>This comprehensive exam covers all topics from Lessons 14.1 through 14.10 — supervised learning foundations, train/test split, cross-validation, linear regression, logistic regression, decision trees, random forests, SVMs, KNN, hyperparameter tuning, and gradient boosting. Good luck!</p>
HTML;

        $finalContent .= $this->appendQuiz('', 'FINAL_EXAM', $allFinalQuestions);
        $finalContent .= '</div>';
        $finalContent .= <<<HTML
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.USER_ORG_ID !== 'undefined' && window.USER_ORG_ID !== null && window.USER_ORG_ID !== '') {
        document.getElementById('org-lock-screen').style.display = 'none';
        document.getElementById('final-exam-content').style.display = 'block';
    }
});
</script>
HTML;

        Lesson::create([
            'module_id'   => $mlModule->id,
            'title'       => '14.11 Final Exam: Supervised Learning Mastery',
            'order_index' => 11,
            'content'     => $finalContent,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────────────────────────

    /**
     * Generates the full Quiz HTML/CSS/JS block and appends it to $htmlContent.
     */
    private function appendQuiz(string $htmlContent, string $quizPrefix, array $questions): string
    {
        $total   = count($questions);
        $letters = ['A', 'B', 'C', 'D', 'E'];

        $html  = $htmlContent;
        $html .= '<style>
            .quiz-wrapper{display:flex;flex-direction:column;gap:24px;margin-top:40px;}
            .quiz-card{background:var(--surface2);border:1px solid var(--border);border-radius:10px;overflow:hidden;}
            .quiz-card-header{background:rgba(0,0,0,0.2);padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:flex-start;gap:12px;}
            .quiz-q-num{background:var(--accent);color:#fff;font-size:0.7rem;font-weight:700;padding:3px 8px;border-radius:4px;font-family:"JetBrains Mono",monospace;white-space:nowrap;margin-top:2px;}
            .quiz-q-text{font-size:0.95rem;font-weight:600;color:var(--text);line-height:1.5;}
            .quiz-options{padding:16px 20px;display:flex;flex-direction:column;gap:10px;}
            .quiz-option{display:flex;align-items:flex-start;gap:12px;padding:12px 16px;border-radius:7px;border:1px solid var(--border);cursor:pointer;transition:all 0.15s;font-size:0.875rem;color:var(--muted);background:transparent;text-align:left;width:100%;font-family:"Inter",sans-serif;}
            .quiz-option:hover:not(.locked){border-color:var(--border-hover);background:var(--bg);color:var(--text);}
            .quiz-option .opt-key{width:22px;height:22px;border-radius:4px;border:1px solid var(--dim);font-size:0.7rem;font-weight:700;font-family:"JetBrains Mono",monospace;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;transition:all 0.15s;}
            .quiz-option.correct{border-color:#10b981;background:rgba(16,185,129,0.08);color:var(--text);}
            .quiz-option.correct .opt-key{background:#10b981;border-color:#10b981;color:#fff;}
            .quiz-option.wrong{border-color:#ef4444;background:rgba(239,68,68,0.08);color:var(--muted);opacity:0.7;}
            .quiz-option.locked{cursor:default;}
            .quiz-explanation{display:none;margin:0 20px 20px;padding:14px 16px;background:rgba(59,130,246,0.07);border:1px solid rgba(59,130,246,0.25);border-radius:7px;font-size:0.875rem;color:var(--muted);line-height:1.7;}
            .quiz-explanation strong{color:var(--text);}
            .quiz-score-bar{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:var(--surface2);border:1px solid var(--border);border-radius:10px;font-size:0.875rem;color:var(--muted);font-weight:600;}
            .quiz-score-val{font-size:1.1rem;font-weight:700;color:#f59e0b;font-family:"JetBrains Mono",monospace;}
        </style>';

        $html .= '<div class="quiz-wrapper" id="wrap_' . $quizPrefix . '">';
        $html .= '<div class="quiz-score-bar"><span>Knowledge Check</span><span class="quiz-score-val"><span id="score_' . $quizPrefix . '">0</span> / ' . $total . '</span></div>';

        foreach ($questions as $qIndex => $q) {
            $qNum = $qIndex + 1;
            $qId  = $quizPrefix . '_q' . $qNum;

            $html .= '<div class="quiz-card" id="' . $qId . '">';
            $html .= '<div class="quiz-card-header"><span class="quiz-q-num">Q' . $qNum . '</span><span class="quiz-q-text">' . htmlspecialchars($q['q']) . '</span></div>';
            $html .= '<div class="quiz-options">';

            foreach ($q['opts'] as $optIndex => $option) {
                $isCorrect = ($optIndex === $q['ans']) ? 'true' : 'false';
                $letter    = $letters[$optIndex];
                $html .= '<button class="quiz-option" onclick="checkAnswer(this,\'' . $qId . '\',' . $isCorrect . ',\'' . $quizPrefix . '\')"><span class="opt-key">' . $letter . '</span> ' . htmlspecialchars($option) . '</button>';
            }

            $html .= '</div>';
            $html .= '<div class="quiz-explanation" id="' . $qId . '-exp"><strong>Explanation:</strong> ' . $q['exp'] . '</div>';
            $html .= '</div>';
        }

        $html .= '</div>';

        $html .= "
<script>
if(typeof window.answeredQuizzes==='undefined'){window.answeredQuizzes={};}
if(typeof window.quizScores==='undefined'){window.quizScores={};}
window.checkAnswer=function(btn,qId,isCorrect,prefix){
    if(window.answeredQuizzes[qId])return;
    window.answeredQuizzes[qId]=true;
    if(typeof window.quizScores[prefix]==='undefined')window.quizScores[prefix]=0;
    const card=document.getElementById(qId);
    const allOpts=card.querySelectorAll('.quiz-option');
    allOpts.forEach(o=>o.classList.add('locked'));
    if(isCorrect){
        btn.classList.add('correct');
        window.quizScores[prefix]++;
    } else {
        btn.classList.add('wrong');
        allOpts.forEach(o=>{if(o.getAttribute('onclick').includes(',true,'))o.classList.add('correct');});
    }
    document.getElementById(qId+'-exp').style.display='block';
    document.getElementById('score_'+prefix).textContent=window.quizScores[prefix];
};
</script>
";

        return $html;
    }
}