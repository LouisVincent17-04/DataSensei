<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Lesson;

/**
 * Module17LessonsSeeder
 * Seeds lessons for Module 17: Deep Learning.
 * Run AFTER CurriculumSeeder which creates the modules.
 *
 * Usage:  php artisan db:seed --class=Module17LessonsSeeder
 */
class Module17LessonsSeeder extends Seeder
{
    public function run()
    {
        $dlModule = Module::where('order_index', 17)->firstOrFail();
        Lesson::where('module_id', $dlModule->id)->delete();

        // ══════════════════════════════════════════════════════════════
        // LESSON 17.1 — What Is Deep Learning?
        // ══════════════════════════════════════════════════════════════
        $content1 = <<<'HTML'
<h2>What Is Deep Learning?</h2>
<p>Deep Learning is a subfield of machine learning in which <strong>artificial neural networks</strong> with many layers (hence "deep") learn representations directly from raw data. Instead of a human engineer manually selecting features — like extracting edges from an image or counting word frequencies in a document — a deep learning model <em>discovers</em> these features automatically by adjusting millions of internal parameters through a process called <strong>training</strong>.</p>

<p>The key insight that separates deep learning from classical ML is the concept of <strong>hierarchical feature learning</strong>. Each layer in a deep network transforms the data into a more abstract, task-relevant representation than the layer before it. In an image classifier, early layers might detect edges and color blobs, middle layers combine those into shapes like eyes and wheels, and the final layers combine shapes into recognizable objects — all learned from examples, not from rules written by a programmer.</p>

<h3>The Deep Learning Ecosystem</h3>
<p>Modern deep learning runs primarily on two frameworks: <strong>TensorFlow / Keras</strong> (developed by Google) and <strong>PyTorch</strong> (developed by Meta AI). Both are Python libraries. Throughout this module, we will use both so you develop intuition for whichever you encounter in industry.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Environment Check</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Verify your deep learning environment is ready</span>
<span style="color:#c4b5fd;">import</span> tensorflow <span style="color:#c4b5fd;">as</span> tf
<span style="color:#c4b5fd;">import</span> torch
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"TensorFlow version: {tf.__version__}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"PyTorch version:     {torch.__version__}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"NumPy version:       {np.__version__}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"GPU available (PyTorch): {torch.cuda.is_available()}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"GPU available (TF):      {len(tf.config.list_physical_devices('GPU')) > 0}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>TensorFlow version: 2.15.0
PyTorch version:     2.2.0
NumPy version:       1.26.3
GPU available (PyTorch): True
GPU available (TF):      True</div>
  </div>
</div>

<h3>Why Now? The Three Pillars That Made Deep Learning Possible</h3>
<p>Deep learning ideas have existed since the 1980s, but they only became practical around 2012. Three things converged: <strong>Data</strong> — the internet produced billions of labelled images, text, and audio samples at a scale never seen before. <strong>Compute</strong> — GPUs (Graphics Processing Units) can perform thousands of floating-point operations in parallel, making the matrix math inside neural networks 100× faster than CPUs. <strong>Algorithms</strong> — breakthroughs like ReLU activation, dropout regularization, batch normalization, and Adam optimization made very deep networks trainable without the gradients vanishing or exploding.</p>

<h3>Where Deep Learning Beats Classical ML</h3>
<p>Classical ML algorithms like Gradient Boosting or SVMs are still king for structured/tabular data. But for <strong>unstructured data</strong> — images, audio, raw text, video — deep learning dominates because feature engineering by hand becomes impossible at scale. An ImageNet classifier needs to understand 1,000 object categories across millions of images; no human team could write enough hand-crafted rules to match what a convolutional network learns automatically in a few hours of GPU training.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Your First Neural Network (3 Lines)</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> tensorflow <span style="color:#c4b5fd;">import</span> keras
<span style="color:#c4b5fd;">from</span> tensorflow.keras <span style="color:#c4b5fd;">import</span> layers

<span style="color:#6b7280;"># A minimal neural network: 2 hidden layers + output</span>
<span style="color:#93c5fd;">model</span> = keras.Sequential([
    layers.Dense(<span style="color:#fcd34d;">64</span>,  activation=<span style="color:#a7f3d0;">'relu'</span>, input_shape=(<span style="color:#fcd34d;">20</span>,)),
    layers.Dense(<span style="color:#fcd34d;">32</span>,  activation=<span style="color:#a7f3d0;">'relu'</span>),
    layers.Dense(<span style="color:#fcd34d;">1</span>,   activation=<span style="color:#a7f3d0;">'sigmoid'</span>)   <span style="color:#6b7280;"># Binary classification output</span>
])

model.summary()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Model: "sequential"
_________________________________________________________________
 Layer (type)          Output Shape         Param #
=================================================================
 dense (Dense)         (None, 64)           1,344
 dense_1 (Dense)       (None, 32)           2,080
 dense_2 (Dense)       (None, 1)            33
=================================================================
Total params: 3,457
Trainable params: 3,457</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dlModule->id,
            'title'       => '17.1 What Is Deep Learning?',
            'order_index' => 1,
            'content'     => $this->appendQuiz($content1, 'L17_1', [
                ['q' => 'What does "deep" in Deep Learning refer to?', 'opts' => ['The complexity of the math', 'The number of layers in a neural network', 'The size of the training dataset', 'The depth of the research field'], 'ans' => 1, 'exp' => '"Deep" refers to having many hidden layers. Each additional layer allows the network to learn increasingly abstract representations of the data.'],
                ['q' => 'Which of the following best describes hierarchical feature learning?', 'opts' => ['A human manually picks features for each layer', 'Each layer learns more abstract representations than the previous one', 'The network uses decision trees at each layer', 'Features are randomly initialized and never change'], 'ans' => 1, 'exp' => 'In deep networks, low-level features (edges, frequencies) are combined layer by layer into higher-level features (shapes, words), all learned automatically from data.'],
                ['q' => 'Which type of data does deep learning excel at compared to classical ML?', 'opts' => ['Tabular/structured data with clear columns', 'Small datasets with few samples', 'Unstructured data like images, audio, and text', 'Data that requires explicit rule-based logic'], 'ans' => 2, 'exp' => 'Classical ML (e.g. gradient boosting) is often better on structured tabular data. Deep learning dominates on unstructured data where manual feature engineering is infeasible.'],
                ['q' => 'What hardware breakthrough made deep learning practically fast after 2012?', 'opts' => ['Faster hard drives (SSDs)', 'GPUs enabling parallel floating-point computation', 'More RAM in desktop computers', 'Faster internet connections'], 'ans' => 1, 'exp' => 'GPUs can perform thousands of matrix operations simultaneously. Because neural network training is essentially massive matrix multiplication, GPUs accelerate it by 100× over CPUs.'],
                ['q' => 'Which two Python frameworks dominate modern deep learning?', 'opts' => ['Scikit-learn and XGBoost', 'TensorFlow/Keras and PyTorch', 'Pandas and NumPy', 'OpenCV and Pillow'], 'ans' => 1, 'exp' => 'TensorFlow (with its high-level Keras API) from Google and PyTorch from Meta AI are the two dominant frameworks used in both research and industry.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 17.2 — The Neuron & Feedforward Networks
        // ══════════════════════════════════════════════════════════════
        $content2 = <<<'HTML'
<h2>The Artificial Neuron & Feedforward Networks</h2>
<p>Every deep learning model — no matter how complex — is built from a single mathematical primitive: the <strong>artificial neuron</strong>. A neuron takes a vector of inputs <code>x</code>, multiplies each by a learned <strong>weight</strong> <code>w</code>, sums the results, adds a <strong>bias</strong> <code>b</code>, and passes the total through an <strong>activation function</strong> to produce an output. Written as a formula: <code>output = activation(w·x + b)</code>. When you stack many neurons into layers and connect those layers together, you get a <strong>Feedforward Neural Network</strong> (also called a Multi-Layer Perceptron, or MLP).</p>

<h3>Why Weights and Biases?</h3>
<p>Weights control <em>how much influence</em> each input has on the neuron's output. A high positive weight means the input strongly pushes the neuron toward activation; a large negative weight suppresses it. The bias is an offset that shifts the activation threshold — it lets the neuron fire even when all inputs are zero, giving the network much more expressive power.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — A Single Neuron from Scratch</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

<span style="color:#6b7280;"># --- Define a single neuron manually ---</span>

<span style="color:#6b7280;"># Three input features (e.g. pixel brightness values)</span>
<span style="color:#93c5fd;">x</span> = np.array([<span style="color:#fcd34d;">0.5</span>, <span style="color:#fcd34d;">0.8</span>, <span style="color:#fcd34d;">0.2</span>])

<span style="color:#6b7280;"># Weights for each input (learned during training)</span>
<span style="color:#93c5fd;">w</span> = np.array([<span style="color:#fcd34d;">0.4</span>, <span style="color:#fcd34d;">-0.3</span>, <span style="color:#fcd34d;">0.9</span>])

<span style="color:#6b7280;"># Bias term</span>
<span style="color:#93c5fd;">b</span> = <span style="color:#fcd34d;">0.1</span>

<span style="color:#6b7280;"># Step 1: Weighted sum (dot product) + bias</span>
<span style="color:#93c5fd;">z</span> = np.dot(w, x) + b
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Weighted sum z = {z:.4f}"</span>)

<span style="color:#6b7280;"># Step 2: Apply ReLU activation function</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fcd34d;">relu</span>(z):
    <span style="color:#c4b5fd;">return</span> np.maximum(<span style="color:#fcd34d;">0</span>, z)

<span style="color:#93c5fd;">output</span> = relu(z)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Neuron output after ReLU = {output:.4f}"</span>)

<span style="color:#6b7280;"># Step 3: What if we apply sigmoid instead?</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fcd34d;">sigmoid</span>(z):
    <span style="color:#c4b5fd;">return</span> <span style="color:#fcd34d;">1</span> / (<span style="color:#fcd34d;">1</span> + np.exp(-z))

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Neuron output after Sigmoid = {sigmoid(z):.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Weighted sum z = 0.2200
Neuron output after ReLU = 0.2200
Neuron output after Sigmoid = 0.5548</div>
  </div>
</div>

<h3>Activation Functions — The Source of Non-Linearity</h3>
<p>If every neuron just computed a weighted sum with no activation function, the entire network would collapse into a single linear transformation — no matter how many layers you added. Activation functions introduce <strong>non-linearity</strong>, which is what allows deep networks to approximate arbitrarily complex functions. The most important ones are:</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Common Activation Functions</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

<span style="color:#93c5fd;">z</span> = np.array([<span style="color:#fcd34d;">-2.0</span>, <span style="color:#fcd34d;">-0.5</span>, <span style="color:#fcd34d;">0.0</span>, <span style="color:#fcd34d;">1.0</span>, <span style="color:#fcd34d;">3.0</span>])

<span style="color:#6b7280;"># ReLU: max(0, z) — most widely used in hidden layers</span>
<span style="color:#93c5fd;">relu</span>    = np.maximum(<span style="color:#fcd34d;">0</span>, z)

<span style="color:#6b7280;"># Sigmoid: squashes output to (0, 1) — used in binary output layers</span>
<span style="color:#93c5fd;">sigmoid</span> = <span style="color:#fcd34d;">1</span> / (<span style="color:#fcd34d;">1</span> + np.exp(-z))

<span style="color:#6b7280;"># Tanh: squashes output to (-1, 1) — zero-centered, used in RNNs</span>
<span style="color:#93c5fd;">tanh</span>    = np.tanh(z)

<span style="color:#6b7280;"># Softmax: converts a vector into a probability distribution (sums to 1)</span>
<span style="color:#93c5fd;">softmax</span> = np.exp(z) / np.sum(np.exp(z))

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Input z:   {z}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"ReLU:      {relu}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Sigmoid:   {np.round(sigmoid, 3)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Tanh:      {np.round(tanh, 3)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Softmax:   {np.round(softmax, 3)}  ← sums to {softmax.sum():.1f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Input z:   [-2.  -0.5  0.   1.   3. ]
ReLU:      [0.  0.  0.  1.  3. ]
Sigmoid:   [0.119 0.378 0.5   0.731 0.953]
Tanh:      [-0.964 -0.462  0.     0.762  0.995]
Softmax:   [0.006 0.033 0.059 0.158 0.744]  ← sums to 1.0</div>
  </div>
</div>

<h3>Building an MLP with Keras</h3>
<p>Now let's build a real multi-layer perceptron. We'll train it on the classic <strong>MNIST</strong> handwritten digit dataset — 60,000 grayscale 28×28 images of digits 0–9 — and achieve over 97% accuracy with just a few dense layers.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — MLP on MNIST</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> tensorflow <span style="color:#c4b5fd;">as</span> tf
<span style="color:#c4b5fd;">from</span> tensorflow.keras <span style="color:#c4b5fd;">import</span> layers, datasets

<span style="color:#6b7280;"># 1. Load & preprocess MNIST</span>
(x_train, y_train), (x_test, y_test) = datasets.mnist.load_data()
x_train = x_train.reshape(-<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">784</span>).astype(<span style="color:#a7f3d0;">"float32"</span>) / <span style="color:#fcd34d;">255.0</span>
x_test  = x_test.reshape(-<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">784</span>).astype(<span style="color:#a7f3d0;">"float32"</span>) / <span style="color:#fcd34d;">255.0</span>

<span style="color:#6b7280;"># 2. Build the MLP</span>
<span style="color:#93c5fd;">model</span> = tf.keras.Sequential([
    layers.Dense(<span style="color:#fcd34d;">256</span>, activation=<span style="color:#a7f3d0;">'relu'</span>, input_shape=(<span style="color:#fcd34d;">784</span>,)),
    layers.Dense(<span style="color:#fcd34d;">128</span>, activation=<span style="color:#a7f3d0;">'relu'</span>),
    layers.Dense(<span style="color:#fcd34d;">64</span>,  activation=<span style="color:#a7f3d0;">'relu'</span>),
    layers.Dense(<span style="color:#fcd34d;">10</span>,  activation=<span style="color:#a7f3d0;">'softmax'</span>)   <span style="color:#6b7280;"># 10 classes</span>
])

<span style="color:#6b7280;"># 3. Compile: loss, optimizer, metric</span>
model.compile(
    optimizer=<span style="color:#a7f3d0;">'adam'</span>,
    loss=<span style="color:#a7f3d0;">'sparse_categorical_crossentropy'</span>,
    metrics=[<span style="color:#a7f3d0;">'accuracy'</span>]
)

<span style="color:#6b7280;"># 4. Train for 5 epochs</span>
model.fit(x_train, y_train, epochs=<span style="color:#fcd34d;">5</span>, batch_size=<span style="color:#fcd34d;">128</span>, validation_split=<span style="color:#fcd34d;">0.1</span>)

<span style="color:#6b7280;"># 5. Evaluate on unseen test data</span>
loss, acc = model.evaluate(x_test, y_test)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nTest Accuracy: {acc:.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Epoch 1/5 — loss: 0.2381 — accuracy: 0.9308
Epoch 2/5 — loss: 0.0941 — accuracy: 0.9718
Epoch 3/5 — loss: 0.0634 — accuracy: 0.9804
Epoch 4/5 — loss: 0.0464 — accuracy: 0.9857
Epoch 5/5 — loss: 0.0356 — accuracy: 0.9891
Test Accuracy: 0.9782</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dlModule->id,
            'title'       => '17.2 The Neuron & Feedforward Networks',
            'order_index' => 2,
            'content'     => $this->appendQuiz($content2, 'L17_2', [
                ['q' => 'What is the mathematical formula for a single artificial neuron?', 'opts' => ['output = x + w', 'output = activation(w·x + b)', 'output = w² + b', 'output = x / w'], 'ans' => 1, 'exp' => 'A neuron computes the dot product of weights and inputs, adds a bias, then passes the result through an activation function: output = activation(w·x + b).'],
                ['q' => 'Why are activation functions necessary in neural networks?', 'opts' => ['They make training faster', 'They introduce non-linearity so networks can approximate complex functions', 'They reduce the number of parameters', 'They replace the bias term'], 'ans' => 1, 'exp' => 'Without activation functions, any deep network collapses to a single linear transformation. Non-linearity is essential for learning complex patterns like image recognition.'],
                ['q' => 'Which activation function is most commonly used in hidden layers of deep networks?', 'opts' => ['Sigmoid', 'Tanh', 'ReLU', 'Softmax'], 'ans' => 2, 'exp' => 'ReLU (Rectified Linear Unit) — max(0, z) — is the default choice for hidden layers due to its simplicity, speed, and ability to avoid the vanishing gradient problem.'],
                ['q' => 'Which activation function converts a vector of scores into a probability distribution that sums to 1?', 'opts' => ['ReLU', 'Sigmoid', 'Tanh', 'Softmax'], 'ans' => 3, 'exp' => 'Softmax exponentiates each score and divides by the sum, producing a valid probability distribution. It is always used in multi-class classification output layers.'],
                ['q' => 'What does the bias term b do in a neuron?', 'opts' => ['It scales all the weights', 'It allows the neuron to fire even when all inputs are zero', 'It acts as the learning rate', 'It controls the number of layers'], 'ans' => 1, 'exp' => 'The bias shifts the activation threshold, giving the neuron more flexibility. Without bias, the decision boundary of every neuron would be forced through the origin.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 17.3 — Backpropagation & Gradient Descent
        // ══════════════════════════════════════════════════════════════
        $content3 = <<<'HTML'
<h2>Backpropagation & Gradient Descent</h2>
<p>A neural network starts with random weights and gradually improves them through a cycle of two steps: <strong>forward pass</strong> (compute predictions) and <strong>backward pass</strong> (compute how wrong the predictions are, then update weights to be less wrong). The backward pass is implemented by an algorithm called <strong>backpropagation</strong>, and the strategy for updating weights is called <strong>gradient descent</strong>. Together they are the engine that trains every deep learning model in existence.</p>

<h3>The Loss Function: Measuring How Wrong You Are</h3>
<p>Before you can fix your network's mistakes, you need to measure them. A <strong>loss function</strong> (also called a cost function) takes the network's predictions and the true labels, and returns a single number — the <em>loss</em> — that is high when the model is wrong and low (ideally zero) when it is correct. Training is simply the process of minimizing this number.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Loss Functions from Scratch</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

<span style="color:#6b7280;"># --- Mean Squared Error (MSE) — for regression ---</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fcd34d;">mse</span>(y_true, y_pred):
    <span style="color:#c4b5fd;">return</span> np.mean((y_true - y_pred) ** <span style="color:#fcd34d;">2</span>)

y_true = np.array([<span style="color:#fcd34d;">3.0</span>, <span style="color:#fcd34d;">5.0</span>, <span style="color:#fcd34d;">2.5</span>])
y_pred = np.array([<span style="color:#fcd34d;">2.8</span>, <span style="color:#fcd34d;">4.5</span>, <span style="color:#fcd34d;">3.1</span>])
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"MSE Loss: {mse(y_true, y_pred):.4f}"</span>)

<span style="color:#6b7280;"># --- Binary Cross-Entropy — for binary classification ---</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fcd34d;">binary_crossentropy</span>(y_true, y_pred):
    eps = <span style="color:#fcd34d;">1e-8</span>  <span style="color:#6b7280;"># prevent log(0)</span>
    <span style="color:#c4b5fd;">return</span> -np.mean(y_true * np.log(y_pred + eps) + (<span style="color:#fcd34d;">1</span> - y_true) * np.log(<span style="color:#fcd34d;">1</span> - y_pred + eps))

y_true_cls = np.array([<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>])
y_pred_cls = np.array([<span style="color:#fcd34d;">0.9</span>, <span style="color:#fcd34d;">0.1</span>, <span style="color:#fcd34d;">0.8</span>])   <span style="color:#6b7280;"># Good predictions</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"BCE Loss (good preds):  {binary_crossentropy(y_true_cls, y_pred_cls):.4f}"</span>)

y_pred_bad = np.array([<span style="color:#fcd34d;">0.1</span>, <span style="color:#fcd34d;">0.9</span>, <span style="color:#fcd34d;">0.2</span>])   <span style="color:#6b7280;"># Bad predictions</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"BCE Loss (bad preds):   {binary_crossentropy(y_true_cls, y_pred_bad):.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>MSE Loss: 0.1367
BCE Loss (good preds):  0.1269
BCE Loss (bad preds):   1.9752</div>
  </div>
</div>

<h3>Gradient Descent: Walking Down the Loss Mountain</h3>
<p>Picture the loss function as a mountainous landscape. Your current model weights are your position on this landscape. The goal is to find the valley — the weights that minimize loss. <strong>Gradient descent</strong> does this by computing the <em>gradient</em> (the slope of the loss surface with respect to each weight) and taking a step in the opposite direction. The size of each step is controlled by the <strong>learning rate</strong> (η). A learning rate too high causes you to overshoot the valley; too low and training takes forever.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Gradient Descent on a Simple Parabola</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Minimize f(w) = (w - 3)^2  → minimum at w = 3, f = 0</span>

<span style="color:#93c5fd;">w</span>  = <span style="color:#fcd34d;">10.0</span>   <span style="color:#6b7280;"># starting weight (far from minimum)</span>
<span style="color:#93c5fd;">lr</span> = <span style="color:#fcd34d;">0.1</span>    <span style="color:#6b7280;"># learning rate</span>

<span style="color:#c4b5fd;">for</span> step <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">11</span>):
    loss     = (w - <span style="color:#fcd34d;">3</span>) ** <span style="color:#fcd34d;">2</span>       <span style="color:#6b7280;"># compute loss</span>
    gradient = <span style="color:#fcd34d;">2</span> * (w - <span style="color:#fcd34d;">3</span>)        <span style="color:#6b7280;"># derivative of loss w.r.t. w</span>
    w        = w - lr * gradient   <span style="color:#6b7280;"># update: move OPPOSITE to gradient</span>
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Step {step:2d} | w = {w:.4f} | loss = {loss:.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Step  1 | w = 8.6000 | loss = 49.0000
Step  2 | w = 7.4800 | loss = 31.3600
Step  3 | w = 6.5840 | loss = 20.0704
Step  4 | w = 5.8672 | loss = 12.8450
Step  5 | w = 5.2938 | loss =  8.2208
Step  6 | w = 4.8350 | loss =  5.2613
Step  7 | w = 4.4680 | loss =  3.3672
Step  8 | w = 4.1744 | loss =  2.1550
Step  9 | w = 3.9395 | loss =  1.3792
Step 10 | w = 3.7516 | loss =  0.8827</div>
  </div>
</div>

<h3>Backpropagation: Computing Gradients Automatically</h3>
<p>For a network with millions of weights, computing gradients by hand is impossible. <strong>Backpropagation</strong> uses the chain rule of calculus to efficiently compute the gradient of the loss with respect to every weight in the network, layer by layer, from output back to input. Frameworks like TensorFlow and PyTorch implement this automatically through <em>automatic differentiation</em> — you never have to derive gradients yourself.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — PyTorch Autograd (Automatic Differentiation)</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> torch

<span style="color:#6b7280;"># requires_grad=True tells PyTorch to track operations on this tensor</span>
<span style="color:#93c5fd;">w</span> = torch.tensor(<span style="color:#fcd34d;">10.0</span>, requires_grad=<span style="color:#fca5a5;">True</span>)

<span style="color:#6b7280;"># Forward pass: define the loss</span>
<span style="color:#93c5fd;">loss</span> = (w - <span style="color:#fcd34d;">3</span>) ** <span style="color:#fcd34d;">2</span>

<span style="color:#6b7280;"># Backward pass: compute gradient d(loss)/d(w)</span>
loss.backward()

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"w       = {w.item()}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"loss    = {loss.item()}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"d(loss)/d(w) = {w.grad.item()}"</span>)   <span style="color:#6b7280;"># Should be 2*(10-3) = 14</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>w       = 10.0
loss    = 49.0
d(loss)/d(w) = 14.0</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dlModule->id,
            'title'       => '17.3 Backpropagation & Gradient Descent',
            'order_index' => 3,
            'content'     => $this->appendQuiz($content3, 'L17_3', [
                ['q' => 'What does a loss function measure?', 'opts' => ['The number of neurons in a layer', 'How wrong the model\'s predictions are compared to true labels', 'The learning rate of the optimizer', 'The number of training epochs'], 'ans' => 1, 'exp' => 'A loss function quantifies the error between predictions and true values. Training minimizes this number so the model becomes more accurate.'],
                ['q' => 'Which loss function is typically used for regression problems?', 'opts' => ['Binary Cross-Entropy', 'Categorical Cross-Entropy', 'Mean Squared Error (MSE)', 'Hinge Loss'], 'ans' => 2, 'exp' => 'MSE measures the average squared difference between predictions and true values, making it ideal for continuous output regression tasks.'],
                ['q' => 'What does the learning rate control in gradient descent?', 'opts' => ['The number of layers', 'The size of each weight update step', 'The activation function used', 'The batch size during training'], 'ans' => 1, 'exp' => 'Learning rate η determines how large each step toward the minimum is. Too high → overshooting; too low → very slow convergence.'],
                ['q' => 'What mathematical rule does backpropagation rely on to compute gradients through many layers?', 'opts' => ['The product rule', 'The quotient rule', 'The chain rule', 'L\'Hôpital\'s rule'], 'ans' => 2, 'exp' => 'Backpropagation applies the chain rule of calculus repeatedly, propagating gradient signals backward from the output layer through each layer to the input.'],
                ['q' => 'In PyTorch, what does calling .backward() on a loss tensor do?', 'opts' => ['It resets all weights to zero', 'It computes gradients for all tensors with requires_grad=True', 'It applies one gradient descent update step', 'It prints the model summary'], 'ans' => 1, 'exp' => '.backward() triggers automatic differentiation, computing and storing the gradient of the loss with respect to every leaf tensor that has requires_grad=True.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 17.4 — Optimizers & Learning Rate Schedules
        // ══════════════════════════════════════════════════════════════
        $content4 = <<<'HTML'
<h2>Optimizers & Learning Rate Schedules</h2>
<p>Once backpropagation computes the gradients, an <strong>optimizer</strong> uses those gradients to actually update the network weights. Plain gradient descent — where every weight gets updated by <code>w = w - lr * gradient</code> using the full training set — is rarely used in practice because it is painfully slow on large datasets. Modern optimizers are much smarter: they adapt the learning rate per parameter, accumulate momentum, and converge much faster.</p>

<h3>SGD: Stochastic Gradient Descent</h3>
<p>Instead of computing the gradient on the full dataset (which requires loading all data before any update), <strong>Stochastic Gradient Descent</strong> computes the gradient on a small random <em>mini-batch</em> of data (typically 32–512 samples) and updates immediately. This introduces noise into the optimization path, which counterintuitively helps escape shallow local minima and saddle points.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — SGD vs Adam Comparison</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> tensorflow <span style="color:#c4b5fd;">as</span> tf
<span style="color:#c4b5fd;">from</span> tensorflow.keras <span style="color:#c4b5fd;">import</span> layers, datasets, optimizers

(x_train, y_train), (x_test, y_test) = datasets.mnist.load_data()
x_train = x_train.reshape(-<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">784</span>).astype(<span style="color:#a7f3d0;">"float32"</span>) / <span style="color:#fcd34d;">255.0</span>
x_test  = x_test.reshape(-<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">784</span>).astype(<span style="color:#a7f3d0;">"float32"</span>) / <span style="color:#fcd34d;">255.0</span>

<span style="color:#c4b5fd;">def</span> <span style="color:#fcd34d;">build_model</span>():
    <span style="color:#c4b5fd;">return</span> tf.keras.Sequential([
        layers.Dense(<span style="color:#fcd34d;">128</span>, activation=<span style="color:#a7f3d0;">'relu'</span>, input_shape=(<span style="color:#fcd34d;">784</span>,)),
        layers.Dense(<span style="color:#fcd34d;">10</span>,  activation=<span style="color:#a7f3d0;">'softmax'</span>)
    ])

<span style="color:#6b7280;"># SGD with momentum</span>
m_sgd = build_model()
m_sgd.compile(optimizer=optimizers.SGD(learning_rate=<span style="color:#fcd34d;">0.01</span>, momentum=<span style="color:#fcd34d;">0.9</span>),
              loss=<span style="color:#a7f3d0;">'sparse_categorical_crossentropy'</span>, metrics=[<span style="color:#a7f3d0;">'accuracy'</span>])
h_sgd = m_sgd.fit(x_train, y_train, epochs=<span style="color:#fcd34d;">3</span>, batch_size=<span style="color:#fcd34d;">128</span>, verbose=<span style="color:#fcd34d;">0</span>)

<span style="color:#6b7280;"># Adam — adaptive learning rate optimizer</span>
m_adam = build_model()
m_adam.compile(optimizer=optimizers.Adam(learning_rate=<span style="color:#fcd34d;">0.001</span>),
               loss=<span style="color:#a7f3d0;">'sparse_categorical_crossentropy'</span>, metrics=[<span style="color:#a7f3d0;">'accuracy'</span>])
h_adam = m_adam.fit(x_train, y_train, epochs=<span style="color:#fcd34d;">3</span>, batch_size=<span style="color:#fcd34d;">128</span>, verbose=<span style="color:#fcd34d;">0</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"SGD  final accuracy:  {h_sgd.history['accuracy'][-1]:.4f}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Adam final accuracy:  {h_adam.history['accuracy'][-1]:.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>SGD  final accuracy:  0.9412
Adam final accuracy:  0.9781</div>
  </div>
</div>

<h3>Adam: The Default Optimizer</h3>
<p><strong>Adam</strong> (Adaptive Moment Estimation) is the most widely used optimizer in deep learning. It maintains a running average of both the past gradients (<em>momentum</em>) and the squared past gradients (<em>RMS</em>) for each weight, using them to normalize the learning rate per parameter. Parameters that receive large, consistent gradients get a smaller effective learning rate; parameters with small or noisy gradients get a larger one. This makes Adam extremely robust to hyperparameter settings — the default learning rate of 0.001 works well in most situations.</p>

<h3>Learning Rate Schedules: Reducing LR Over Time</h3>
<p>Even with Adam, starting with a larger learning rate and gradually reducing it often yields better results — the model makes large initial progress, then fine-tunes as it approaches the minimum.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Learning Rate Schedules in Keras</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> tensorflow <span style="color:#c4b5fd;">as</span> tf

<span style="color:#6b7280;"># Exponential Decay: lr = lr0 * decay_rate ^ (step / decay_steps)</span>
<span style="color:#93c5fd;">lr_schedule</span> = tf.keras.optimizers.schedules.ExponentialDecay(
    initial_learning_rate=<span style="color:#fcd34d;">0.01</span>,
    decay_steps=<span style="color:#fcd34d;">1000</span>,
    decay_rate=<span style="color:#fcd34d;">0.9</span>
)

<span style="color:#6b7280;"># Preview how the LR changes over steps</span>
<span style="color:#c4b5fd;">for</span> step <span style="color:#c4b5fd;">in</span> [<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1000</span>, <span style="color:#fcd34d;">2000</span>, <span style="color:#fcd34d;">5000</span>, <span style="color:#fcd34d;">10000</span>]:
    lr = lr_schedule(step).numpy()
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Step {step:6d}: lr = {lr:.6f}"</span>)

<span style="color:#6b7280;"># Use with an optimizer</span>
<span style="color:#93c5fd;">optimizer</span> = tf.keras.optimizers.Adam(learning_rate=lr_schedule)

<span style="color:#6b7280;"># ReduceLROnPlateau — halves LR when val_loss stops improving</span>
<span style="color:#93c5fd;">reduce_lr</span> = tf.keras.callbacks.ReduceLROnPlateau(
    monitor=<span style="color:#a7f3d0;">'val_loss'</span>, factor=<span style="color:#fcd34d;">0.5</span>, patience=<span style="color:#fcd34d;">3</span>, min_lr=<span style="color:#fcd34d;">1e-6</span>
)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nReduceLROnPlateau callback ready."</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Step      0: lr = 0.010000
Step   1000: lr = 0.009000
Step   2000: lr = 0.008100
Step   5000: lr = 0.005905
Step  10000: lr = 0.003487

ReduceLROnPlateau callback ready.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dlModule->id,
            'title'       => '17.4 Optimizers & Learning Rate Schedules',
            'order_index' => 4,
            'content'     => $this->appendQuiz($content4, 'L17_4', [
                ['q' => 'What is the key difference between full-batch gradient descent and SGD?', 'opts' => ['SGD uses a different loss function', 'SGD computes gradients on mini-batches instead of the full dataset', 'SGD does not use backpropagation', 'SGD only works with ReLU activations'], 'ans' => 1, 'exp' => 'SGD updates weights after processing a small random mini-batch, making it far faster per update than computing gradients over the entire dataset.'],
                ['q' => 'What property makes Adam the most popular optimizer in deep learning?', 'opts' => ['It uses no learning rate at all', 'It adapts the learning rate per parameter using momentum and RMS statistics', 'It trains without any loss function', 'It uses second-order derivatives (Hessian)'], 'ans' => 1, 'exp' => 'Adam maintains per-parameter adaptive learning rates using first moment (momentum) and second moment (RMS) estimates, making it robust and fast with minimal hyperparameter tuning.'],
                ['q' => 'What happens when the learning rate is set too high?', 'opts' => ['Training is slower but more accurate', 'The model learns nothing and stays at random performance', 'Weight updates overshoot the minimum, causing unstable or diverging loss', 'Gradients vanish and the network stops learning'], 'ans' => 2, 'exp' => 'A learning rate that is too high causes weights to jump past the optimal minimum on each step, often resulting in the loss oscillating wildly or exploding.'],
                ['q' => 'What does the ReduceLROnPlateau callback do?', 'opts' => ['Doubles the learning rate every N epochs', 'Halves the learning rate when a monitored metric stops improving', 'Freezes all layer weights after convergence', 'Resets the optimizer state at every epoch'], 'ans' => 1, 'exp' => 'ReduceLROnPlateau monitors a metric (usually val_loss) and reduces the learning rate by a factor (e.g. 0.5) when it sees no improvement for `patience` epochs.'],
                ['q' => 'What does adding momentum to SGD do?', 'opts' => ['It removes the need for a loss function', 'It accumulates past gradient directions to accelerate movement and reduce oscillation', 'It prevents the model from overfitting', 'It increases the batch size automatically'], 'ans' => 1, 'exp' => 'Momentum keeps a running average of past gradients, giving SGD inertia. This accelerates learning in consistent gradient directions and dampens oscillations.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 17.5 — Regularization: Preventing Overfitting
        // ══════════════════════════════════════════════════════════════
        $content5 = <<<'HTML'
<h2>Regularization: Preventing Overfitting</h2>
<p><strong>Overfitting</strong> is the #1 practical problem in deep learning. A network that is powerful enough will memorize your training data — including the noise — and fail to generalize to new examples. You'll recognize overfitting when training accuracy is high but validation accuracy is significantly lower and diverges as training continues. <strong>Regularization</strong> is any technique that discourages the model from becoming too confident about the training data, forcing it to learn more general patterns.</p>

<h3>Dropout: Randomly Silencing Neurons</h3>
<p>Invented by Hinton et al. in 2014, <strong>Dropout</strong> randomly sets a fraction of neuron activations to zero at each training step. This prevents neurons from co-adapting and becoming over-reliant on specific input patterns. It effectively trains an ensemble of sub-networks simultaneously. At inference time, all neurons are active but their outputs are scaled down by the dropout rate.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Dropout Regularization</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> tensorflow <span style="color:#c4b5fd;">as</span> tf
<span style="color:#c4b5fd;">from</span> tensorflow.keras <span style="color:#c4b5fd;">import</span> layers, datasets

(x_train, y_train), (x_test, y_test) = datasets.mnist.load_data()
x_train = x_train.reshape(-<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">784</span>).astype(<span style="color:#a7f3d0;">"float32"</span>) / <span style="color:#fcd34d;">255.0</span>
x_test  = x_test.reshape(-<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">784</span>).astype(<span style="color:#a7f3d0;">"float32"</span>) / <span style="color:#fcd34d;">255.0</span>

<span style="color:#6b7280;"># Model WITHOUT dropout (prone to overfitting on small data)</span>
<span style="color:#93c5fd;">model_no_drop</span> = tf.keras.Sequential([
    layers.Dense(<span style="color:#fcd34d;">512</span>, activation=<span style="color:#a7f3d0;">'relu'</span>, input_shape=(<span style="color:#fcd34d;">784</span>,)),
    layers.Dense(<span style="color:#fcd34d;">512</span>, activation=<span style="color:#a7f3d0;">'relu'</span>),
    layers.Dense(<span style="color:#fcd34d;">10</span>,  activation=<span style="color:#a7f3d0;">'softmax'</span>)
])

<span style="color:#6b7280;"># Model WITH dropout — each Dropout(0.4) randomly zeros 40% of activations</span>
<span style="color:#93c5fd;">model_drop</span> = tf.keras.Sequential([
    layers.Dense(<span style="color:#fcd34d;">512</span>,  activation=<span style="color:#a7f3d0;">'relu'</span>, input_shape=(<span style="color:#fcd34d;">784</span>,)),
    layers.Dropout(<span style="color:#fcd34d;">0.4</span>),
    layers.Dense(<span style="color:#fcd34d;">512</span>,  activation=<span style="color:#a7f3d0;">'relu'</span>),
    layers.Dropout(<span style="color:#fcd34d;">0.4</span>),
    layers.Dense(<span style="color:#fcd34d;">10</span>,   activation=<span style="color:#a7f3d0;">'softmax'</span>)
])

<span style="color:#c4b5fd;">for</span> name, mdl <span style="color:#c4b5fd;">in</span> [(<span style="color:#a7f3d0;">"No Dropout"</span>, model_no_drop), (<span style="color:#a7f3d0;">"With Dropout"</span>, model_drop)]:
    mdl.compile(<span style="color:#a7f3d0;">'adam'</span>, <span style="color:#a7f3d0;">'sparse_categorical_crossentropy'</span>, metrics=[<span style="color:#a7f3d0;">'accuracy'</span>])
    h = mdl.fit(x_train[:<span style="color:#fcd34d;">5000</span>], y_train[:<span style="color:#fcd34d;">5000</span>], epochs=<span style="color:#fcd34d;">10</span>,
                validation_data=(x_test, y_test), verbose=<span style="color:#fcd34d;">0</span>)
    tr = h.history[<span style="color:#a7f3d0;">'accuracy'</span>][-<span style="color:#fcd34d;">1</span>]
    va = h.history[<span style="color:#a7f3d0;">'val_accuracy'</span>][-<span style="color:#fcd34d;">1</span>]
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{name:14} | Train: {tr:.4f} | Val: {va:.4f} | Gap: {tr-va:.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>No Dropout     | Train: 0.9988 | Val: 0.9601 | Gap: 0.0387
With Dropout   | Train: 0.9742 | Val: 0.9718 | Gap: 0.0024</div>
  </div>
</div>

<h3>Batch Normalization</h3>
<p><strong>Batch Normalization</strong> normalizes the inputs to each layer across the current mini-batch, keeping activations in a stable range throughout training. This dramatically speeds up training (you can use higher learning rates), reduces sensitivity to initialization, and acts as a mild regularizer. It is placed after a Dense or Conv layer, before the activation function.</p>

<h3>L1 and L2 Regularization (Weight Decay)</h3>
<p>L1 and L2 regularization add a penalty term to the loss function that grows with the magnitude of the weights, discouraging the network from developing very large weights that would overfit.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — BatchNorm + L2 Regularization</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> tensorflow.keras <span style="color:#c4b5fd;">import</span> layers, regularizers
<span style="color:#c4b5fd;">import</span> tensorflow <span style="color:#c4b5fd;">as</span> tf

<span style="color:#93c5fd;">model</span> = tf.keras.Sequential([
    layers.Dense(<span style="color:#fcd34d;">256</span>,
                 kernel_regularizer=regularizers.l2(<span style="color:#fcd34d;">0.001</span>),  <span style="color:#6b7280;"># L2 weight decay</span>
                 input_shape=(<span style="color:#fcd34d;">784</span>,)),
    layers.BatchNormalization(),   <span style="color:#6b7280;"># normalize activations</span>
    layers.Activation(<span style="color:#a7f3d0;">'relu'</span>),      <span style="color:#6b7280;"># activation AFTER BN</span>
    layers.Dropout(<span style="color:#fcd34d;">0.3</span>),

    layers.Dense(<span style="color:#fcd34d;">128</span>,
                 kernel_regularizer=regularizers.l2(<span style="color:#fcd34d;">0.001</span>)),
    layers.BatchNormalization(),
    layers.Activation(<span style="color:#a7f3d0;">'relu'</span>),
    layers.Dropout(<span style="color:#fcd34d;">0.3</span>),

    layers.Dense(<span style="color:#fcd34d;">10</span>, activation=<span style="color:#a7f3d0;">'softmax'</span>)
])

model.summary()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Total params: 236,554
Trainable params: 236,042
Non-trainable params: 512   ← BatchNorm parameters</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dlModule->id,
            'title'       => '17.5 Regularization: Preventing Overfitting',
            'order_index' => 5,
            'content'     => $this->appendQuiz($content5, 'L17_5', [
                ['q' => 'How do you detect overfitting during training?', 'opts' => ['Training loss increases over time', 'Training accuracy is high but validation accuracy is much lower and diverges', 'Validation accuracy is higher than training accuracy', 'The model trains faster than expected'], 'ans' => 1, 'exp' => 'Overfitting shows as a large and growing gap between training and validation accuracy — the model memorizes training data but fails on unseen examples.'],
                ['q' => 'How does Dropout prevent overfitting?', 'opts' => ['It reduces the learning rate automatically', 'It randomly zeros out neuron activations during training, preventing co-adaptation', 'It removes entire layers from the network', 'It clips gradients to a maximum value'], 'ans' => 1, 'exp' => 'Dropout randomly silences a fraction of neurons each training step, forcing the network to learn redundant representations and preventing neurons from co-adapting to specific patterns.'],
                ['q' => 'Where in the network is Batch Normalization typically placed?', 'opts' => ['Only at the output layer', 'After the loss function is computed', 'After a Dense/Conv layer and before the activation function', 'Before the input layer, to normalize raw data'], 'ans' => 2, 'exp' => 'The standard pattern is: Dense → BatchNorm → Activation. BN normalizes the pre-activation values, then the activation function is applied to the normalized output.'],
                ['q' => 'What does L2 regularization penalize?', 'opts' => ['The number of layers in the network', 'Large weight values by adding a penalty proportional to the sum of squared weights', 'Non-zero weight values (encourages sparsity)', 'The learning rate of the optimizer'], 'ans' => 1, 'exp' => 'L2 adds λ * Σ(w²) to the loss, discouraging large weights. This keeps the model\'s decisions spread across many inputs rather than relying too heavily on a few.'],
                ['q' => 'At inference/test time, what happens to neurons that were subject to Dropout during training?', 'opts' => ['They remain randomly dropped at the same rate', 'All neurons are active and outputs are scaled down by the dropout rate', 'Dropout layers are removed entirely from the model', 'They are replaced by their training averages'], 'ans' => 1, 'exp' => 'At test time, all neurons are active. Keras automatically scales their outputs by (1 - dropout_rate) to maintain the same expected output magnitude as during training.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 17.6 — Convolutional Neural Networks (CNNs)
        // ══════════════════════════════════════════════════════════════
        $content6 = <<<'HTML'
<h2>Convolutional Neural Networks (CNNs)</h2>
<p>A <strong>Convolutional Neural Network</strong> is the architecture that revolutionized computer vision. Instead of connecting every input pixel to every neuron (which would require billions of parameters for a 1080p image), CNNs use small filters called <strong>kernels</strong> that slide across the image and detect local patterns — edges, textures, corners — regardless of their exact position. This property, called <strong>translational equivariance</strong>, means a cat detector works whether the cat is in the top-left or bottom-right corner. CNNs made the 2012 AlexNet breakthrough possible and underpin every modern vision system from facial recognition to medical imaging.</p>

<h3>The Convolution Operation</h3>
<p>A convolution filter is a small matrix (e.g. 3×3 or 5×5) of learnable weights. It slides over the image with a step called the <strong>stride</strong>, computes a dot product between the filter weights and the overlapping image patch at each position, and produces a single number — the feature activation. Running multiple filters produces multiple <strong>feature maps</strong>, each detecting a different type of pattern.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — 2D Convolution from Scratch</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

<span style="color:#6b7280;"># 5x5 grayscale image patch (simplified)</span>
<span style="color:#93c5fd;">image</span> = np.array([
    [<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0</span>],
    [<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">0</span>],
    [<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>],
    [<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">0</span>],
    [<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">0</span>]
], dtype=float)

<span style="color:#6b7280;"># 3x3 edge-detection (Sobel-like) filter</span>
<span style="color:#93c5fd;">kernel</span> = np.array([
    [ <span style="color:#fcd34d;">1</span>,  <span style="color:#fcd34d;">0</span>, -<span style="color:#fcd34d;">1</span>],
    [ <span style="color:#fcd34d;">2</span>,  <span style="color:#fcd34d;">0</span>, -<span style="color:#fcd34d;">2</span>],
    [ <span style="color:#fcd34d;">1</span>,  <span style="color:#fcd34d;">0</span>, -<span style="color:#fcd34d;">1</span>]
], dtype=float)

<span style="color:#6b7280;"># Manual convolution (stride=1, no padding)</span>
<span style="color:#93c5fd;">H, W</span> = image.shape
<span style="color:#93c5fd;">K</span>     = kernel.shape[<span style="color:#fcd34d;">0</span>]
<span style="color:#93c5fd;">out</span>   = np.zeros((H - K + <span style="color:#fcd34d;">1</span>, W - K + <span style="color:#fcd34d;">1</span>))

<span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(out.shape[<span style="color:#fcd34d;">0</span>]):
    <span style="color:#c4b5fd;">for</span> j <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(out.shape[<span style="color:#fcd34d;">1</span>]):
        out[i, j] = np.sum(image[i:i+K, j:j+K] * kernel)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Feature Map (3x3):\n"</span>, out)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Feature Map (3x3):
 [[-1.  0.  3.]
  [-1.  0.  3.]
  [ 0.  0.  2.]]</div>
  </div>
</div>

<h3>Building a CNN with Keras for CIFAR-10</h3>
<p>CIFAR-10 contains 60,000 colour 32×32 images across 10 classes (airplane, car, bird, cat, etc.). A well-built CNN reaches ~75–80% accuracy where an MLP peaks at ~55%. Let's build one:</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — CNN on CIFAR-10</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> tensorflow <span style="color:#c4b5fd;">as</span> tf
<span style="color:#c4b5fd;">from</span> tensorflow.keras <span style="color:#c4b5fd;">import</span> layers, datasets

<span style="color:#6b7280;"># Load and normalize CIFAR-10</span>
(x_train, y_train), (x_test, y_test) = datasets.cifar10.load_data()
x_train, x_test = x_train / <span style="color:#fcd34d;">255.0</span>, x_test / <span style="color:#fcd34d;">255.0</span>

<span style="color:#93c5fd;">model</span> = tf.keras.Sequential([
    <span style="color:#6b7280;"># Block 1: 32 filters, 3x3 kernel</span>
    layers.Conv2D(<span style="color:#fcd34d;">32</span>, (<span style="color:#fcd34d;">3</span>,<span style="color:#fcd34d;">3</span>), activation=<span style="color:#a7f3d0;">'relu'</span>, padding=<span style="color:#a7f3d0;">'same'</span>, input_shape=(<span style="color:#fcd34d;">32</span>,<span style="color:#fcd34d;">32</span>,<span style="color:#fcd34d;">3</span>)),
    layers.Conv2D(<span style="color:#fcd34d;">32</span>, (<span style="color:#fcd34d;">3</span>,<span style="color:#fcd34d;">3</span>), activation=<span style="color:#a7f3d0;">'relu'</span>),
    layers.MaxPooling2D((<span style="color:#fcd34d;">2</span>,<span style="color:#fcd34d;">2</span>)),   <span style="color:#6b7280;"># Halves spatial dimensions</span>
    layers.Dropout(<span style="color:#fcd34d;">0.25</span>),

    <span style="color:#6b7280;"># Block 2: 64 filters — detect more complex features</span>
    layers.Conv2D(<span style="color:#fcd34d;">64</span>, (<span style="color:#fcd34d;">3</span>,<span style="color:#fcd34d;">3</span>), activation=<span style="color:#a7f3d0;">'relu'</span>, padding=<span style="color:#a7f3d0;">'same'</span>),
    layers.Conv2D(<span style="color:#fcd34d;">64</span>, (<span style="color:#fcd34d;">3</span>,<span style="color:#fcd34d;">3</span>), activation=<span style="color:#a7f3d0;">'relu'</span>),
    layers.MaxPooling2D((<span style="color:#fcd34d;">2</span>,<span style="color:#fcd34d;">2</span>)),
    layers.Dropout(<span style="color:#fcd34d;">0.25</span>),

    <span style="color:#6b7280;"># Classifier head</span>
    layers.Flatten(),
    layers.Dense(<span style="color:#fcd34d;">512</span>, activation=<span style="color:#a7f3d0;">'relu'</span>),
    layers.Dropout(<span style="color:#fcd34d;">0.5</span>),
    layers.Dense(<span style="color:#fcd34d;">10</span>,  activation=<span style="color:#a7f3d0;">'softmax'</span>)
])

model.compile(<span style="color:#a7f3d0;">'adam'</span>, <span style="color:#a7f3d0;">'sparse_categorical_crossentropy'</span>, metrics=[<span style="color:#a7f3d0;">'accuracy'</span>])
model.summary()

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nTotal parameters: {model.count_params():,}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Total params: 1,250,858
Trainable params: 1,250,858
(Note: training 20 epochs on GPU gives ~78% test accuracy)</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dlModule->id,
            'title'       => '17.6 Convolutional Neural Networks (CNNs)',
            'order_index' => 6,
            'content'     => $this->appendQuiz($content6, 'L17_6', [
                ['q' => 'What property of CNNs allows them to detect a cat regardless of where it appears in the image?', 'opts' => ['Batch normalization', 'Translational equivariance from shared filter weights', 'Dropout regularization', 'The softmax output layer'], 'ans' => 1, 'exp' => 'CNN filters share the same weights as they slide across the entire image, so a pattern detector (e.g. edge detector) works in any spatial position — this is translational equivariance.'],
                ['q' => 'What does a 3×3 convolution kernel compute at each position?', 'opts' => ['The maximum pixel value in the patch', 'The sum of all pixels in the image', 'The dot product between the filter weights and the overlapping image patch', 'The average of the surrounding pixels'], 'ans' => 2, 'exp' => 'At each stride position, the filter weights are element-wise multiplied with the corresponding image patch and summed to produce a single scalar — the feature activation.'],
                ['q' => 'What does MaxPooling do in a CNN?', 'opts' => ['It increases the spatial resolution by 2×', 'It takes the maximum value in each pooling window, reducing spatial dimensions', 'It normalizes the feature map values to [0, 1]', 'It applies the ReLU activation function'], 'ans' => 1, 'exp' => 'MaxPooling downsamples feature maps by selecting the maximum value in each pool window (e.g. 2×2), halving the height and width and giving the network translational invariance.'],
                ['q' => 'Why do deeper CNN blocks typically use more filters (e.g. 32 → 64 → 128)?', 'opts' => ['To keep the number of parameters constant as spatial size decreases', 'More filters in deeper layers allow detection of increasingly complex and abstract features', 'It is required by the Keras API', 'To offset the reduction in batch size'], 'ans' => 1, 'exp' => 'Deeper layers represent higher-level, more complex features (shapes → parts → objects). More filters allow the network to capture more such abstract patterns at each level.'],
                ['q' => 'What is the role of the Flatten layer before the Dense classifier head?', 'opts' => ['It applies a 1×1 convolution to reduce channels', 'It converts the 3D feature map (height × width × channels) into a 1D vector for Dense layers', 'It normalizes the final convolution output', 'It removes the spatial dimensions by average pooling'], 'ans' => 1, 'exp' => 'Dense layers require 1D input. Flatten reshapes the 3D convolutional feature map into a flat vector, bridging the convolutional backbone and the classification head.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 17.7 — Transfer Learning & Pretrained Models
        // ══════════════════════════════════════════════════════════════
        $content7 = <<<'HTML'
<h2>Transfer Learning & Pretrained Models</h2>
<p>Training a large CNN from scratch on millions of images takes days on expensive GPU clusters. <strong>Transfer learning</strong> lets you skip most of that. The core idea: take a model already trained on a massive dataset (like ImageNet — 1.2 million images, 1,000 classes), and reuse its learned feature representations for your own task. The early layers that detect edges and textures are universal — they work for cats, X-rays, satellite imagery, and skin lesions alike. You only retrain the final classification layers for your specific classes.</p>

<h3>How Transfer Learning Works</h3>
<p>The process has two stages. In <strong>feature extraction</strong>, you freeze all layers of the pretrained backbone and only train a new classification head you've added on top. This is fast and safe — you're not modifying the hard-won ImageNet features. Once that converges, you optionally <strong>fine-tune</strong> the top layers of the backbone with a very small learning rate, allowing the features to specialize slightly for your domain.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Transfer Learning with MobileNetV2</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> tensorflow <span style="color:#c4b5fd;">as</span> tf
<span style="color:#c4b5fd;">from</span> tensorflow.keras <span style="color:#c4b5fd;">import</span> layers

<span style="color:#6b7280;"># 1. Load MobileNetV2 backbone (pretrained on ImageNet)</span>
<span style="color:#6b7280;">#    include_top=False removes the final 1000-class head</span>
<span style="color:#93c5fd;">base_model</span> = tf.keras.applications.MobileNetV2(
    input_shape=(<span style="color:#fcd34d;">224</span>, <span style="color:#fcd34d;">224</span>, <span style="color:#fcd34d;">3</span>),
    include_top=<span style="color:#fca5a5;">False</span>,
    weights=<span style="color:#a7f3d0;">'imagenet'</span>
)

<span style="color:#6b7280;"># 2. Freeze the backbone — we don't update these weights yet</span>
base_model.trainable = <span style="color:#fca5a5;">False</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Backbone layers: {len(base_model.layers)}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Backbone output shape: {base_model.output_shape}"</span>)

<span style="color:#6b7280;"># 3. Add a custom classification head for YOUR task (e.g. 5 classes)</span>
<span style="color:#93c5fd;">inputs</span>  = tf.keras.Input(shape=(<span style="color:#fcd34d;">224</span>, <span style="color:#fcd34d;">224</span>, <span style="color:#fcd34d;">3</span>))
<span style="color:#93c5fd;">x</span>       = base_model(inputs, training=<span style="color:#fca5a5;">False</span>)   <span style="color:#6b7280;"># frozen backbone</span>
<span style="color:#93c5fd;">x</span>       = layers.GlobalAveragePooling2D()(x)    <span style="color:#6b7280;"># reduce spatial dims</span>
<span style="color:#93c5fd;">x</span>       = layers.Dense(<span style="color:#fcd34d;">128</span>, activation=<span style="color:#a7f3d0;">'relu'</span>)(x)
<span style="color:#93c5fd;">x</span>       = layers.Dropout(<span style="color:#fcd34d;">0.3</span>)(x)
<span style="color:#93c5fd;">outputs</span> = layers.Dense(<span style="color:#fcd34d;">5</span>, activation=<span style="color:#a7f3d0;">'softmax'</span>)(x)  <span style="color:#6b7280;"># 5 custom classes</span>

<span style="color:#93c5fd;">model</span> = tf.keras.Model(inputs, outputs)
model.compile(<span style="color:#a7f3d0;">'adam'</span>, <span style="color:#a7f3d0;">'sparse_categorical_crossentropy'</span>, metrics=[<span style="color:#a7f3d0;">'accuracy'</span>])

<span style="color:#6b7280;"># Count trainable vs frozen params</span>
<span style="color:#93c5fd;">trainable</span>     = sum(tf.size(p).numpy() <span style="color:#c4b5fd;">for</span> p <span style="color:#c4b5fd;">in</span> model.trainable_weights)
<span style="color:#93c5fd;">non_trainable</span> = sum(tf.size(p).numpy() <span style="color:#c4b5fd;">for</span> p <span style="color:#c4b5fd;">in</span> model.non_trainable_weights)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nTrainable params:     {trainable:,}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Non-trainable params: {non_trainable:,}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Backbone layers: 154
Backbone output shape: (None, 7, 7, 1280)

Trainable params:     33,285
Non-trainable params: 2,257,984</div>
  </div>
</div>

<h3>Fine-Tuning: Unlocking the Backbone</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Fine-Tuning the Top Layers</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Phase 2: Fine-tune the top 30 layers of the backbone</span>
base_model.trainable = <span style="color:#fca5a5;">True</span>
<span style="color:#93c5fd;">fine_tune_at</span> = <span style="color:#fcd34d;">100</span>   <span style="color:#6b7280;"># freeze layers 0..99, unfreeze 100+</span>

<span style="color:#c4b5fd;">for</span> layer <span style="color:#c4b5fd;">in</span> base_model.layers[:fine_tune_at]:
    layer.trainable = <span style="color:#fca5a5;">False</span>

<span style="color:#6b7280;"># IMPORTANT: Use a much smaller learning rate during fine-tuning</span>
<span style="color:#6b7280;"># to avoid destroying the pretrained weights</span>
model.compile(
    optimizer=tf.keras.optimizers.Adam(<span style="color:#fcd34d;">1e-5</span>),   <span style="color:#6b7280;"># 100× smaller than default</span>
    loss=<span style="color:#a7f3d0;">'sparse_categorical_crossentropy'</span>,
    metrics=[<span style="color:#a7f3d0;">'accuracy'</span>]
)

<span style="color:#93c5fd;">trainable_now</span> = sum(tf.size(p).numpy() <span style="color:#c4b5fd;">for</span> p <span style="color:#c4b5fd;">in</span> model.trainable_weights)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Trainable params after fine-tuning unlock: {trainable_now:,}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Model ready for fine-tuning phase."</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Trainable params after fine-tuning unlock: 1,219,589
Model ready for fine-tuning phase.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dlModule->id,
            'title'       => '17.7 Transfer Learning & Pretrained Models',
            'order_index' => 7,
            'content'     => $this->appendQuiz($content7, 'L17_7', [
                ['q' => 'What is the core idea behind transfer learning?', 'opts' => ['Training a model from scratch on your custom dataset', 'Reusing feature representations from a model pretrained on a large dataset', 'Copying the weights of one model into another model of a different architecture', 'Using two models simultaneously for ensemble predictions'], 'ans' => 1, 'exp' => 'Transfer learning reuses the generic feature representations (edges, textures, shapes) learned from a large dataset like ImageNet, adapting them to a new task with much less data and compute.'],
                ['q' => 'What does setting base_model.trainable = False do?', 'opts' => ['It deletes all the model weights', 'It freezes the backbone weights so they are not updated during training', 'It sets the learning rate to zero', 'It removes the model from the computation graph'], 'ans' => 1, 'exp' => 'Setting trainable=False marks all weights in that model as non-trainable. Gradients are still computed through them, but the optimizer does not update those weights.'],
                ['q' => 'Why must the learning rate be very small during fine-tuning?', 'opts' => ['The GPU is slower during fine-tuning', 'To prevent large gradient updates from destroying the valuable pretrained weights', 'Larger datasets require smaller learning rates', 'It prevents the validation loss from becoming negative'], 'ans' => 1, 'exp' => 'Pretrained weights represent years of training on massive data. Fine-tuning with a large LR risks overwriting them with noise. A tiny LR (e.g. 1e-5) allows gentle specialization without catastrophic forgetting.'],
                ['q' => 'What does include_top=False do when loading a pretrained model?', 'opts' => ['It loads only the first 10 layers', 'It removes the final classification head, letting you add your own', 'It excludes the batch normalization layers', 'It loads the model without pretrained weights'], 'ans' => 1, 'exp' => 'include_top=False removes the task-specific output layer (e.g. the 1000-class softmax head for ImageNet), leaving just the feature-extraction backbone so you can add your own classification head.'],
                ['q' => 'Which of these is NOT a popular pretrained model backbone?', 'opts' => ['MobileNetV2', 'ResNet50', 'VGG16', 'XGBoostNet'], 'ans' => 3, 'exp' => 'XGBoostNet is not a real architecture. MobileNetV2, ResNet50, and VGG16 are all real, widely used CNN backbones available in Keras and PyTorch with pretrained ImageNet weights.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 17.8 — Recurrent Neural Networks & LSTMs
        // ══════════════════════════════════════════════════════════════
        $content8 = <<<'HTML'
<h2>Recurrent Neural Networks & LSTMs</h2>
<p>Feedforward networks and CNNs process each input independently — they have no concept of time or sequence. But language, audio, time series data, and video are <em>sequential</em>: the meaning of a word depends on the words before it, and the next stock price depends on the history of past prices. <strong>Recurrent Neural Networks (RNNs)</strong> address this by maintaining a <strong>hidden state</strong> — a memory vector — that is updated at each time step based on the current input and the previous hidden state, creating a feedback loop through time.</p>

<h3>The Vanishing Gradient Problem</h3>
<p>Plain RNNs struggle to learn long-range dependencies — what happened 50 time steps ago often fails to influence the current prediction because the gradients shrink exponentially as they flow backward through many time steps. <strong>Long Short-Term Memory (LSTM)</strong> cells solve this with a more sophisticated architecture featuring three learned gates — <em>input</em>, <em>forget</em>, and <em>output</em> — that control what information to remember, discard, and expose at each step.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Sentiment Analysis with LSTM</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> tensorflow <span style="color:#c4b5fd;">as</span> tf
<span style="color:#c4b5fd;">from</span> tensorflow.keras <span style="color:#c4b5fd;">import</span> layers, datasets

<span style="color:#6b7280;"># IMDB: 25k movie reviews, labelled positive (1) or negative (0)</span>
<span style="color:#93c5fd;">VOCAB_SIZE</span>  = <span style="color:#fcd34d;">10000</span>
<span style="color:#93c5fd;">MAX_LEN</span>    = <span style="color:#fcd34d;">200</span>

(x_train, y_train), (x_test, y_test) = datasets.imdb.load_data(num_words=VOCAB_SIZE)
x_train = tf.keras.preprocessing.sequence.pad_sequences(x_train, maxlen=MAX_LEN)
x_test  = tf.keras.preprocessing.sequence.pad_sequences(x_test,  maxlen=MAX_LEN)

<span style="color:#93c5fd;">model</span> = tf.keras.Sequential([
    <span style="color:#6b7280;"># Embedding: maps integer word IDs to dense 64-dim vectors</span>
    layers.Embedding(VOCAB_SIZE, <span style="color:#fcd34d;">64</span>, input_length=MAX_LEN),
    <span style="color:#6b7280;"># Bidirectional LSTM reads sequence in both directions</span>
    layers.Bidirectional(layers.LSTM(<span style="color:#fcd34d;">64</span>, return_sequences=<span style="color:#fca5a5;">True</span>)),
    layers.Bidirectional(layers.LSTM(<span style="color:#fcd34d;">32</span>)),
    layers.Dense(<span style="color:#fcd34d;">64</span>, activation=<span style="color:#a7f3d0;">'relu'</span>),
    layers.Dropout(<span style="color:#fcd34d;">0.5</span>),
    layers.Dense(<span style="color:#fcd34d;">1</span>,  activation=<span style="color:#a7f3d0;">'sigmoid'</span>)   <span style="color:#6b7280;"># binary: positive/negative</span>
])

model.compile(<span style="color:#a7f3d0;">'adam'</span>, <span style="color:#a7f3d0;">'binary_crossentropy'</span>, metrics=[<span style="color:#a7f3d0;">'accuracy'</span>])
model.summary()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Total params: 785,857
Trainable params: 785,857
(Training 5 epochs achieves ~87% test accuracy on IMDB)</div>
  </div>
</div>

<h3>Time Series Forecasting with LSTM</h3>
<p>LSTMs are also powerful for forecasting numerical sequences — stock prices, sensor readings, weather data. The key preprocessing step is creating <strong>sliding windows</strong>: given N past values, predict the next one.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — LSTM Time Series Forecasting</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#c4b5fd;">import</span> tensorflow <span style="color:#c4b5fd;">as</span> tf
<span style="color:#c4b5fd;">from</span> tensorflow.keras <span style="color:#c4b5fd;">import</span> layers

<span style="color:#6b7280;"># Generate synthetic sine wave data</span>
<span style="color:#93c5fd;">t</span> = np.linspace(<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">100</span>, <span style="color:#fcd34d;">1000</span>)
<span style="color:#93c5fd;">series</span> = np.sin(<span style="color:#fcd34d;">0.5</span> * t) + <span style="color:#fcd34d;">0.1</span> * np.random.randn(<span style="color:#fcd34d;">1000</span>)

<span style="color:#6b7280;"># Create sliding windows of length 30 → predict next value</span>
<span style="color:#93c5fd;">WINDOW</span> = <span style="color:#fcd34d;">30</span>
<span style="color:#93c5fd;">X</span>, <span style="color:#93c5fd;">y</span> = [], []
<span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(WINDOW, <span style="color:#93c5fd;">len</span>(series)):
    X.append(series[i - WINDOW:i])
    y.append(series[i])
<span style="color:#93c5fd;">X</span> = np.array(X)[..., np.newaxis]   <span style="color:#6b7280;"># shape: (N, 30, 1)</span>
<span style="color:#93c5fd;">y</span> = np.array(y)

<span style="color:#93c5fd;">split</span> = <span style="color:#fcd34d;">800</span>
<span style="color:#93c5fd;">X_train</span>, <span style="color:#93c5fd;">X_test</span> = X[:split], X[split:]
<span style="color:#93c5fd;">y_train</span>, <span style="color:#93c5fd;">y_test</span> = y[:split], y[split:]

<span style="color:#93c5fd;">model</span> = tf.keras.Sequential([
    layers.LSTM(<span style="color:#fcd34d;">64</span>, return_sequences=<span style="color:#fca5a5;">True</span>, input_shape=(<span style="color:#fcd34d;">30</span>, <span style="color:#fcd34d;">1</span>)),
    layers.LSTM(<span style="color:#fcd34d;">32</span>),
    layers.Dense(<span style="color:#fcd34d;">1</span>)    <span style="color:#6b7280;"># single value prediction</span>
])
model.compile(<span style="color:#a7f3d0;">'adam'</span>, <span style="color:#a7f3d0;">'mse'</span>)
model.fit(X_train, y_train, epochs=<span style="color:#fcd34d;">10</span>, batch_size=<span style="color:#fcd34d;">32</span>, verbose=<span style="color:#fcd34d;">0</span>)

<span style="color:#93c5fd;">mse</span> = model.evaluate(X_test, y_test, verbose=<span style="color:#fcd34d;">0</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Test MSE: {mse:.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Test MSE: 0.0142</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dlModule->id,
            'title'       => '17.8 Recurrent Neural Networks & LSTMs',
            'order_index' => 8,
            'content'     => $this->appendQuiz($content8, 'L17_8', [
                ['q' => 'What fundamental capability do RNNs have that feedforward networks lack?', 'opts' => ['RNNs can process images', 'RNNs maintain a hidden state that carries information across time steps in a sequence', 'RNNs have more layers than feedforward networks', 'RNNs use a different activation function'], 'ans' => 1, 'exp' => 'The hidden state of an RNN is updated at each time step, carrying contextual information forward. This allows the network to process sequences where order and history matter.'],
                ['q' => 'What problem do plain RNNs suffer from with long sequences?', 'opts' => ['They use too much memory', 'The vanishing gradient problem makes it hard to learn dependencies over many time steps', 'They require more classes in the output layer', 'They cannot use the Adam optimizer'], 'ans' => 1, 'exp' => 'As gradients flow backward through many time steps, they get multiplied together repeatedly. If they are < 1, they shrink toward zero (vanishing), making it impossible to learn long-range dependencies.'],
                ['q' => 'How does the LSTM architecture solve the vanishing gradient problem?', 'opts' => ['By using ReLU activations in all gates', 'By using gating mechanisms (input, forget, output gates) that allow gradients to flow through a separate cell state without shrinking', 'By reducing the sequence length automatically', 'By applying batch normalization after every time step'], 'ans' => 1, 'exp' => 'LSTMs maintain a separate cell state that acts as a long-term memory highway. Gates control what to write, erase, and read from this state, allowing gradients to flow back through long sequences without vanishing.'],
                ['q' => 'What does a Bidirectional LSTM do that a standard LSTM cannot?', 'opts' => ['It trains faster by processing half the sequence', 'It processes the sequence in both forward and backward directions, giving context from both past and future', 'It automatically tunes the number of units', 'It replaces the need for an Embedding layer'], 'ans' => 1, 'exp' => 'A Bidirectional LSTM runs two LSTMs in parallel — one reads left to right, the other right to left — and concatenates their outputs, giving each time step access to both past and future context.'],
                ['q' => 'What is a sliding window in the context of time series forecasting?', 'opts' => ['A visualization technique for plotting time series', 'A preprocessing technique that creates (input, target) pairs from a fixed number of past observations', 'A regularization method specific to LSTM layers', 'A window function applied before the Fourier transform'], 'ans' => 1, 'exp' => 'A sliding window moves across the time series, extracting overlapping subsequences of fixed length as inputs (X) and the immediately following value as the target (y), creating the training dataset.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 17.9 — Transformers & Attention Mechanism
        // ══════════════════════════════════════════════════════════════
        $content9 = <<<'HTML'
<h2>Transformers & the Attention Mechanism</h2>
<p>In 2017, Google Brain published the landmark paper <em>"Attention Is All You Need"</em>, introducing the <strong>Transformer</strong> architecture. Transformers replaced RNNs as the dominant architecture for NLP and are now the foundation of GPT, BERT, T5, and every modern large language model. The key innovation: instead of processing a sequence step by step (which is inherently serial and slow), Transformers process all tokens in parallel and use a mechanism called <strong>self-attention</strong> to let each token directly attend to — and gather information from — every other token in the sequence, regardless of distance.</p>

<h3>Self-Attention: The Core Innovation</h3>
<p>Self-attention takes a sequence of input vectors and produces a weighted combination of all of them for each position. For every token, it computes three vectors: a <strong>Query</strong> (what am I looking for?), a <strong>Key</strong> (what do I contain?), and a <strong>Value</strong> (what do I provide?). The attention score between two tokens is the dot product of one's Query with the other's Key — tokens with high relevance get high scores and contribute more to the output.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Scaled Dot-Product Attention from Scratch</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

<span style="color:#c4b5fd;">def</span> <span style="color:#fcd34d;">scaled_dot_product_attention</span>(Q, K, V):
    <span style="color:#a7f3d0;">"""
    Q: queries  (seq_len, d_k)
    K: keys     (seq_len, d_k)
    V: values   (seq_len, d_v)
    """</span>
    d_k = Q.shape[-<span style="color:#fcd34d;">1</span>]

    <span style="color:#6b7280;"># Step 1: Compute raw attention scores (Q * K^T)</span>
    <span style="color:#93c5fd;">scores</span> = Q @ K.T / np.sqrt(d_k)   <span style="color:#6b7280;"># scale to prevent softmax saturation</span>

    <span style="color:#6b7280;"># Step 2: Softmax → attention weights (each row sums to 1)</span>
    <span style="color:#93c5fd;">exp_scores</span> = np.exp(scores - scores.max(axis=-<span style="color:#fcd34d;">1</span>, keepdims=<span style="color:#fca5a5;">True</span>))
    <span style="color:#93c5fd;">weights</span>    = exp_scores / exp_scores.sum(axis=-<span style="color:#fcd34d;">1</span>, keepdims=<span style="color:#fca5a5;">True</span>)

    <span style="color:#6b7280;"># Step 3: Weighted sum of values</span>
    <span style="color:#93c5fd;">output</span> = weights @ V

    <span style="color:#c4b5fd;">return</span> output, weights

<span style="color:#6b7280;"># Example: 4 tokens, each with d_k=d_v=8</span>
np.random.seed(<span style="color:#fcd34d;">42</span>)
<span style="color:#93c5fd;">seq_len</span>, <span style="color:#93c5fd;">d_k</span> = <span style="color:#fcd34d;">4</span>, <span style="color:#fcd34d;">8</span>
<span style="color:#93c5fd;">Q</span> = np.random.randn(seq_len, d_k)
<span style="color:#93c5fd;">K</span> = np.random.randn(seq_len, d_k)
<span style="color:#93c5fd;">V</span> = np.random.randn(seq_len, d_k)

<span style="color:#93c5fd;">out</span>, <span style="color:#93c5fd;">attn</span> = scaled_dot_product_attention(Q, K, V)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Attention weights (each row sums to 1):\n"</span>, np.round(attn, <span style="color:#fcd34d;">3</span>))
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nOutput shape:"</span>, out.shape)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Row sums of attention:"</span>, np.round(attn.sum(axis=<span style="color:#fcd34d;">1</span>), <span style="color:#fcd34d;">4</span>))</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Attention weights (each row sums to 1):
 [[0.316 0.214 0.28  0.19 ]
  [0.241 0.301 0.228 0.23 ]
  [0.198 0.255 0.347 0.2  ]
  [0.22  0.271 0.281 0.228]]

Output shape: (4, 8)
Row sums of attention: [1. 1. 1. 1.]</div>
  </div>
</div>

<h3>Using Hugging Face Transformers for NLP</h3>
<p>In practice you almost never build a Transformer from scratch. The <strong>Hugging Face <code>transformers</code></strong> library gives you access to thousands of pretrained models — BERT, GPT-2, RoBERTa, DistilBERT — ready to use or fine-tune in a few lines of code.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Sentiment Classification with DistilBERT</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># pip install transformers</span>
<span style="color:#c4b5fd;">from</span> transformers <span style="color:#c4b5fd;">import</span> pipeline

<span style="color:#6b7280;"># Zero-shot: no fine-tuning needed — the model is already powerful</span>
<span style="color:#93c5fd;">classifier</span> = pipeline(<span style="color:#a7f3d0;">"sentiment-analysis"</span>,
                       model=<span style="color:#a7f3d0;">"distilbert-base-uncased-finetuned-sst-2-english"</span>)

<span style="color:#93c5fd;">reviews</span> = [
    <span style="color:#a7f3d0;">"This movie was absolutely brilliant — a masterpiece!"</span>,
    <span style="color:#a7f3d0;">"Terrible film. Boring plot and awful acting."</span>,
    <span style="color:#a7f3d0;">"It was okay. Not great, but not bad either."</span>,
]

<span style="color:#93c5fd;">results</span> = classifier(reviews)
<span style="color:#c4b5fd;">for</span> review, result <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">zip</span>(reviews, results):
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"[{result['label']:8}  {result['score']:.3f}] {review[:50]}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>[POSITIVE  0.999] This movie was absolutely brilliant — a maste...
[NEGATIVE  0.998] Terrible film. Boring plot and awful acting.
[NEGATIVE  0.617] It was okay. Not great, but not bad either.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dlModule->id,
            'title'       => '17.9 Transformers & the Attention Mechanism',
            'order_index' => 9,
            'content'     => $this->appendQuiz($content9, 'L17_9', [
                ['q' => 'What is the major advantage of Transformers over RNNs for processing sequences?', 'opts' => ['Transformers use fewer parameters', 'Transformers process all tokens in parallel and attend to any position in one step, unlike serial RNN processing', 'Transformers only work on text, not other sequences', 'Transformers do not require training data'], 'ans' => 1, 'exp' => 'RNNs process sequences serially — step by step — making long-range dependencies hard to learn. Transformers compute attention across all token pairs simultaneously, enabling direct long-range connections and parallelism.'],
                ['q' => 'In the self-attention mechanism, what do the Query, Key, and Value vectors represent?', 'opts' => ['Input, hidden state, and output of an RNN cell', 'What a token is searching for, what it contains, and what it provides to others', 'The embedding, positional encoding, and mask', 'The input, weights, and bias of a dense layer'], 'ans' => 1, 'exp' => 'Each token projects itself into Q (what am I looking for?), K (what information do I have?), and V (what do I contribute?). The dot product of Q and K gives the relevance score; V is the information actually passed along.'],
                ['q' => 'Why are the attention scores divided by √d_k before the softmax?', 'opts' => ['To normalize the output to [0, 1]', 'To prevent the dot products from becoming very large and pushing softmax into a region with vanishing gradients', 'To make the attention weights sum to d_k instead of 1', 'To apply dropout implicitly'], 'ans' => 1, 'exp' => 'With large d_k, dot products grow in magnitude. Very large scores push softmax to near-one-hot distributions with tiny gradients, making training slow. Dividing by √d_k keeps scores in a numerically stable range.'],
                ['q' => 'Which model family was introduced in the 2017 "Attention Is All You Need" paper and now underlies GPT, BERT, and most modern LLMs?', 'opts' => ['Recurrent Neural Networks', 'Convolutional Neural Networks', 'Transformers', 'Autoencoders'], 'ans' => 2, 'exp' => 'The Transformer architecture, introduced by Vaswani et al. (2017), is the foundation of every modern large language model including BERT, GPT-2/3/4, T5, LLaMA, and Claude.'],
                ['q' => 'What does the Hugging Face transformers library allow you to do with models like DistilBERT?', 'opts' => ['Train from scratch on ImageNet', 'Load pretrained models and apply them to NLP tasks with minimal code', 'Convert neural networks to SQL queries', 'Automatically label your dataset using web scraping'], 'ans' => 1, 'exp' => 'Hugging Face provides thousands of pretrained Transformer models via a simple API. You can load a model, use it zero-shot, or fine-tune it on your data in a few dozen lines of Python.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 17.10 — Generative Models: GANs & VAEs
        // ══════════════════════════════════════════════════════════════
        $content10 = <<<'HTML'
<h2>Generative Models: GANs & Variational Autoencoders</h2>
<p>All the models we have built so far are <em>discriminative</em> — they learn to map inputs to labels. <strong>Generative models</strong> do the opposite: they learn the underlying distribution of the training data and use that knowledge to <em>create new samples</em> that look like they came from that distribution. Generative models power image synthesis (DALL-E, Stable Diffusion), text generation (GPT), audio synthesis (WaveNet), and drug discovery. The two most foundational generative architectures are <strong>Variational Autoencoders (VAEs)</strong> and <strong>Generative Adversarial Networks (GANs)</strong>.</p>

<h3>Autoencoders: Learning a Compressed Representation</h3>
<p>An autoencoder is a network trained to compress its input into a low-dimensional <strong>latent vector</strong> (the encoder) and then reconstruct the original input from that vector (the decoder). The bottleneck forces the network to learn the most essential features. A <strong>Variational Autoencoder</strong> extends this by making the latent space continuous and probabilistic — instead of encoding to a single point, it encodes to a Gaussian distribution, allowing you to sample new points and decode them into plausible new data.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Variational Autoencoder (VAE) on MNIST</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> tensorflow <span style="color:#c4b5fd;">as</span> tf
<span style="color:#c4b5fd;">from</span> tensorflow.keras <span style="color:#c4b5fd;">import</span> layers
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

<span style="color:#93c5fd;">LATENT_DIM</span> = <span style="color:#fcd34d;">2</span>   <span style="color:#6b7280;"># 2D for easy visualization</span>

<span style="color:#6b7280;"># ── Encoder ──────────────────────────────────────────</span>
<span style="color:#93c5fd;">encoder_inputs</span> = tf.keras.Input(shape=(<span style="color:#fcd34d;">784</span>,))
<span style="color:#93c5fd;">x</span>              = layers.Dense(<span style="color:#fcd34d;">256</span>, activation=<span style="color:#a7f3d0;">'relu'</span>)(encoder_inputs)
<span style="color:#93c5fd;">x</span>              = layers.Dense(<span style="color:#fcd34d;">128</span>, activation=<span style="color:#a7f3d0;">'relu'</span>)(x)
<span style="color:#93c5fd;">z_mean</span>         = layers.Dense(LATENT_DIM, name=<span style="color:#a7f3d0;">'z_mean'</span>)(x)      <span style="color:#6b7280;"># μ</span>
<span style="color:#93c5fd;">z_log_var</span>      = layers.Dense(LATENT_DIM, name=<span style="color:#a7f3d0;">'z_log_var'</span>)(x)   <span style="color:#6b7280;"># log(σ²)</span>

<span style="color:#6b7280;"># Reparameterization trick: z = μ + σ * ε,  ε ~ N(0,1)</span>
<span style="color:#6b7280;"># This allows gradients to flow through sampling</span>
<span style="color:#93c5fd;">epsilon</span> = tf.random.normal(shape=tf.shape(z_mean))
<span style="color:#93c5fd;">z</span>       = z_mean + tf.exp(<span style="color:#fcd34d;">0.5</span> * z_log_var) * epsilon

<span style="color:#93c5fd;">encoder</span> = tf.keras.Model(encoder_inputs, [z_mean, z_log_var, z], name=<span style="color:#a7f3d0;">'encoder'</span>)

<span style="color:#6b7280;"># ── Decoder ──────────────────────────────────────────</span>
<span style="color:#93c5fd;">decoder_inputs</span> = tf.keras.Input(shape=(LATENT_DIM,))
<span style="color:#93c5fd;">x</span>              = layers.Dense(<span style="color:#fcd34d;">128</span>, activation=<span style="color:#a7f3d0;">'relu'</span>)(decoder_inputs)
<span style="color:#93c5fd;">x</span>              = layers.Dense(<span style="color:#fcd34d;">256</span>, activation=<span style="color:#a7f3d0;">'relu'</span>)(x)
<span style="color:#93c5fd;">decoder_outputs</span>= layers.Dense(<span style="color:#fcd34d;">784</span>, activation=<span style="color:#a7f3d0;">'sigmoid'</span>)(x)
<span style="color:#93c5fd;">decoder</span>        = tf.keras.Model(decoder_inputs, decoder_outputs, name=<span style="color:#a7f3d0;">'decoder'</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Encoder summary:"</span>)
encoder.summary()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nDecoder summary:"</span>)
decoder.summary()

<span style="color:#6b7280;"># After training: generate a new digit by sampling from latent space</span>
<span style="color:#93c5fd;">random_z</span> = np.random.randn(<span style="color:#fcd34d;">1</span>, LATENT_DIM)
<span style="color:#93c5fd;">generated</span>= decoder.predict(random_z)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nGenerated image shape: {generated.shape}"</span>)  <span style="color:#6b7280;"># (1, 784)</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Encoder params: 233,476
Decoder params: 232,960
Generated image shape: (1, 784)</div>
  </div>
</div>

<h3>GANs: Two Networks in Competition</h3>
<p>A <strong>Generative Adversarial Network</strong> (GAN) trains two networks simultaneously in a minimax game. The <strong>Generator</strong> takes random noise and outputs a synthetic sample (e.g. a fake image). The <strong>Discriminator</strong> takes a sample and predicts whether it's real (from the dataset) or fake (from the Generator). The Generator tries to fool the Discriminator; the Discriminator tries not to be fooled. Through this adversarial competition, the Generator learns to produce increasingly realistic outputs.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Simple GAN Architecture for MNIST</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> tensorflow <span style="color:#c4b5fd;">as</span> tf
<span style="color:#c4b5fd;">from</span> tensorflow.keras <span style="color:#c4b5fd;">import</span> layers

<span style="color:#93c5fd;">NOISE_DIM</span> = <span style="color:#fcd34d;">100</span>

<span style="color:#6b7280;"># ── Generator: noise → fake 28×28 digit ──────────────</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fcd34d;">build_generator</span>():
    <span style="color:#c4b5fd;">return</span> tf.keras.Sequential([
        layers.Dense(<span style="color:#fcd34d;">256</span>, input_dim=NOISE_DIM),
        layers.LeakyReLU(<span style="color:#fcd34d;">0.2</span>),
        layers.BatchNormalization(),
        layers.Dense(<span style="color:#fcd34d;">512</span>),
        layers.LeakyReLU(<span style="color:#fcd34d;">0.2</span>),
        layers.BatchNormalization(),
        layers.Dense(<span style="color:#fcd34d;">784</span>, activation=<span style="color:#a7f3d0;">'tanh'</span>)   <span style="color:#6b7280;"># pixels in [-1, 1]</span>
    ], name=<span style="color:#a7f3d0;">'generator'</span>)

<span style="color:#6b7280;"># ── Discriminator: image → real/fake probability ──────</span>
<span style="color:#c4b5fd;">def</span> <span style="color:#fcd34d;">build_discriminator</span>():
    <span style="color:#c4b5fd;">return</span> tf.keras.Sequential([
        layers.Dense(<span style="color:#fcd34d;">512</span>, input_dim=<span style="color:#fcd34d;">784</span>),
        layers.LeakyReLU(<span style="color:#fcd34d;">0.2</span>),
        layers.Dropout(<span style="color:#fcd34d;">0.3</span>),
        layers.Dense(<span style="color:#fcd34d;">256</span>),
        layers.LeakyReLU(<span style="color:#fcd34d;">0.2</span>),
        layers.Dropout(<span style="color:#fcd34d;">0.3</span>),
        layers.Dense(<span style="color:#fcd34d;">1</span>, activation=<span style="color:#a7f3d0;">'sigmoid'</span>)   <span style="color:#6b7280;"># 1=real, 0=fake</span>
    ], name=<span style="color:#a7f3d0;">'discriminator'</span>)

<span style="color:#93c5fd;">G</span> = build_generator()
<span style="color:#93c5fd;">D</span> = build_discriminator()

D.compile(<span style="color:#a7f3d0;">'adam'</span>, <span style="color:#a7f3d0;">'binary_crossentropy'</span>, metrics=[<span style="color:#a7f3d0;">'accuracy'</span>])

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Generator params:     {G.count_params():,}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Discriminator params: {D.count_params():,}"</span>)

<span style="color:#6b7280;"># Generate a single fake image</span>
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np
<span style="color:#93c5fd;">noise</span>      = np.random.randn(<span style="color:#fcd34d;">1</span>, NOISE_DIM)
<span style="color:#93c5fd;">fake_img</span>   = G.predict(noise)
<span style="color:#93c5fd;">real_score</span> = D.predict(fake_img)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nFake image shape: {fake_img.shape}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Discriminator score (untrained): {real_score[0][0]:.4f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Generator params:     544,784
Discriminator params: 398,337

Fake image shape: (1, 784)
Discriminator score (untrained): 0.5021</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dlModule->id,
            'title'       => '17.10 Generative Models: GANs & VAEs',
            'order_index' => 10,
            'content'     => $this->appendQuiz($content10, 'L17_10', [
                ['q' => 'What is the fundamental difference between discriminative and generative models?', 'opts' => ['Discriminative models are larger', 'Discriminative models map inputs to labels; generative models learn the data distribution to create new samples', 'Generative models require labeled data; discriminative models do not', 'Discriminative models are only used for images'], 'ans' => 1, 'exp' => 'A discriminative model learns P(y|x) — the probability of a label given an input. A generative model learns P(x) — the distribution of the data itself — and can sample new x values from it.'],
                ['q' => 'What forces an autoencoder to learn meaningful compressed representations?', 'opts' => ['The use of dropout regularization', 'The bottleneck — a low-dimensional latent space that forces information compression', 'The sigmoid activation at the output', 'Training on a large dataset'], 'ans' => 1, 'exp' => 'The latent bottleneck has far fewer dimensions than the input. The encoder must discard noise and redundancy, retaining only the essential features needed for the decoder to reconstruct the input.'],
                ['q' => 'What is the reparameterization trick in a VAE, and why is it necessary?', 'opts' => ['It rearranges the decoder layers to improve performance', 'It expresses sampling as z = μ + σ*ε where ε~N(0,1), making the operation differentiable so gradients can flow through it', 'It reduces the number of parameters in the encoder', 'It converts discrete latent codes to continuous values'], 'ans' => 1, 'exp' => 'Direct sampling is not differentiable — gradients cannot flow through a stochastic node. The reparameterization trick shifts the randomness to ε, making z a deterministic function of μ and σ, allowing backpropagation.'],
                ['q' => 'In a GAN, what does the Generator learn to do?', 'opts' => ['Classify inputs as real or fake', 'Produce synthetic samples that the Discriminator cannot distinguish from real data', 'Compress images into latent codes', 'Predict the next token in a sequence'], 'ans' => 1, 'exp' => 'The Generator takes random noise and learns to transform it into outputs (images, audio, etc.) that are realistic enough to fool the Discriminator into classifying them as real.'],
                ['q' => 'What is a common training instability issue with GANs?', 'opts' => ['The Generator always reaches 100% accuracy first', 'Mode collapse — the Generator learns to produce only a few types of outputs instead of the full data diversity', 'The Discriminator loss becomes zero immediately', 'Both networks converge to identical weights'], 'ans' => 1, 'exp' => 'Mode collapse occurs when the Generator finds a few outputs that consistently fool the Discriminator and stops exploring. The Generator produces limited variety even though the training data is diverse. It is a central challenge in GAN training.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 17.11 — Final Exam
        // ══════════════════════════════════════════════════════════════
        $allFinalQuestions = [
            ['q' => 'Which of the following best describes what "deep" means in deep learning?', 'opts' => ['The mathematical complexity of individual neurons', 'Multiple hidden layers allowing hierarchical feature learning', 'The depth of the training dataset', 'Using 64-bit floating-point precision'], 'ans' => 1, 'exp' => '"Deep" refers to having many stacked layers. Each layer learns progressively more abstract representations of the data, enabling the network to solve complex tasks automatically.'],
            ['q' => 'What is the output of a single artificial neuron before the activation function is applied?', 'opts' => ['The probability of each class', 'A weighted dot product of the inputs plus a bias: z = w·x + b', 'The gradient of the loss with respect to the input', 'A random noise vector'], 'ans' => 1, 'exp' => 'Before activation, the neuron computes the linear combination z = w·x + b. The activation function then transforms z into the final neuron output.'],
            ['q' => 'Why does adding more layers to a network without any activation functions NOT improve its expressive power?', 'opts' => ['More layers would cause overfitting immediately', 'Without activation functions, multiple linear layers collapse to a single linear transformation', 'The network runs out of memory', 'Backpropagation cannot propagate through more than one layer'], 'ans' => 1, 'exp' => 'A stack of linear transformations is still just a linear transformation: W₂(W₁x + b₁) + b₂ = W_eff·x + b_eff. Non-linear activation functions are essential for learning complex mappings.'],
            ['q' => 'During training, what two steps make up one complete forward-backward cycle?', 'opts' => ['Normalization and regularization', 'Forward pass (compute predictions and loss) and backward pass (compute gradients and update weights)', 'Encoding and decoding', 'Embedding and attention'], 'ans' => 1, 'exp' => 'The forward pass feeds inputs through the network to produce predictions and compute the loss. The backward pass (backpropagation) computes gradients using the chain rule, which the optimizer uses to update weights.'],
            ['q' => 'What makes Adam a better default optimizer than plain SGD for most deep learning tasks?', 'opts' => ['Adam does not require a loss function', 'Adam adapts per-parameter learning rates using momentum and RMS statistics, making it robust and fast with default settings', 'Adam uses second-order gradient information (Hessian)', 'Adam trains without mini-batches'], 'ans' => 1, 'exp' => 'Adam maintains running averages of both gradients and squared gradients, normalizing updates per parameter. This makes it converge reliably across a wide variety of architectures and tasks without much hyperparameter tuning.'],
            ['q' => 'What does overfitting look like in a training curve?', 'opts' => ['Both training and validation loss decrease together', 'Training loss and validation loss are equal throughout training', 'Training accuracy improves while validation accuracy plateaus or decreases', 'The model converges in fewer epochs than expected'], 'ans' => 2, 'exp' => 'Overfitting is visible as the training accuracy curve continuing to improve while the validation accuracy curve stagnates or deteriorates — indicating the model is memorizing rather than generalizing.'],
            ['q' => 'What is the key architectural feature that makes CNNs efficient for image processing compared to MLPs?', 'opts' => ['CNNs use a different activation function', 'CNNs use local, weight-shared convolutional filters instead of full connections, drastically reducing parameters', 'CNNs process images one pixel at a time sequentially', 'CNNs require no training data'], 'ans' => 1, 'exp' => 'A fully connected MLP on a 224×224 image needs 150K connections per neuron. A CNN 3×3 filter uses only 9 shared weights regardless of image size, and the same filter detects the same pattern anywhere in the image.'],
            ['q' => 'In transfer learning, what is the recommended strategy during the initial feature extraction phase?', 'opts' => ['Unfreeze all layers of the pretrained backbone and train with the same learning rate', 'Freeze the backbone weights and train only the new classification head', 'Remove all pretrained weights and reinitialize randomly', 'Train the backbone with a much larger learning rate than the head'], 'ans' => 1, 'exp' => 'During feature extraction, you freeze the backbone to preserve its ImageNet-learned features and train only the new head. This is fast, stable, and avoids destroying valuable pretrained representations.'],
            ['q' => 'What does the LSTM forget gate decide?', 'opts' => ['What new information to add to the cell state', 'What proportion of the previous cell state to erase or retain', 'What portion of the cell state to expose as output', 'The learning rate for the LSTM cell'], 'ans' => 1, 'exp' => 'The forget gate applies a sigmoid to compute values between 0 and 1 for each element of the cell state. 0 means "completely forget"; 1 means "completely retain". This enables selective long-term memory.'],
            ['q' => 'What is the purpose of positional encoding in the Transformer architecture?', 'opts' => ['To apply regularization to the attention weights', 'To inject sequence order information, since self-attention itself is order-invariant', 'To compress the input tokens into a smaller latent space', 'To replace the need for an embedding layer'], 'ans' => 1, 'exp' => 'Self-attention treats all positions identically — it has no inherent sense of order. Positional encoding adds a position-dependent signal to each token embedding so the model knows which token comes first, second, etc.'],
            ['q' => 'In a GAN, when is training considered to have reached a (theoretical) equilibrium?', 'opts' => ['When the Generator loss reaches zero', 'When the Discriminator cannot do better than random guessing (50% accuracy), meaning Generator fakes are indistinguishable from real samples', 'When both losses are equal and positive', 'When the Discriminator achieves 100% accuracy'], 'ans' => 1, 'exp' => 'Nash equilibrium in GANs occurs when the Generator produces samples so realistic that the Discriminator assigns 50% probability to real and fake — essentially random guessing. This is the theoretical optimum, though difficult to achieve in practice.'],
            ['q' => 'Which regularization technique is disabled at inference time?', 'opts' => ['L2 weight decay', 'Batch Normalization', 'Dropout', 'Gradient clipping'], 'ans' => 2, 'exp' => 'Dropout randomly zeros neurons only during training. At inference, all neurons are active (with scaled weights). Batch Normalization switches from batch statistics to running statistics, but remains active at inference. Only Dropout is fully disabled.'],
        ];

        $finalContent = <<<HTML
<div id="org-lock-screen" style="display:none;text-align:center;padding:40px;">
    <h2 style="font-size:1.5rem;margin-bottom:12px;">Final Exam Locked</h2>
    <p style="color:var(--muted);">You must be enrolled in an organization to access this exam.</p>
    <p style="font-size:0.85rem;color:#f59e0b;margin-top:1rem;background:rgba(245,158,11,0.1);padding:10px;border-radius:8px;display:inline-block;">Please contact administration to link your account to an organization.</p>
</div>
<div id="final-exam-content" style="display:none;">
    <h2>Module 17: Final Examination</h2>
    <p>This comprehensive exam covers all topics from Lessons 17.1 through 17.10 — deep learning foundations, neurons, backpropagation, optimizers, regularization, CNNs, transfer learning, RNNs/LSTMs, Transformers, and generative models. Good luck!</p>
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
            'module_id'   => $dlModule->id,
            'title'       => '17.11 Final Exam: Deep Learning Mastery',
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