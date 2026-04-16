<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module4ChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (!$category) {
            $this->command->error("Professional category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        Challenge::where('challenge_category_id', $category->id)
                 ->where('title', 'Mathematical Analysis I')
                 ->delete();

        $this->command->info("Creating Module 4 — Mathematical Analysis I (Professional)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Mathematical Analysis I',
            'description'           => 'Professional-grade problems covering functional analysis, real-world numerical methods, production-level mathematical code review, stochastic analysis, and edge cases in computation. Designed for working data scientists, ML engineers, and applied mathematicians.',
            'time_limit_seconds'    => 2400,
            'base_xp'               => 2000,
            'order_index'           => 4,
        ]);

        $this->command->info("Seeding 50 professional-level Mathematical Analysis I questions...");

        $qaData = [

            // ── FUNCTIONAL ANALYSIS ───────────────────────────────────────
            [
                'q' => "A Banach space is a normed vector space that is:",
                'opts' => [
                    ['Finite-dimensional', false],
                    ['Complete with respect to its norm (every Cauchy sequence converges)', true],
                    ['Equipped with an inner product', false],
                    ['Closed under differentiation', false],
                ],
            ],
            [
                'q' => "The Hilbert space L²([a,b]) consists of functions that are:",
                'opts' => [
                    ['Continuous on [a,b]', false],
                    ['Square-integrable: ∫ₐᵇ |f(x)|² dx < ∞', true],
                    ['Differentiable everywhere on [a,b]', false],
                    ['Bounded on [a,b]', false],
                ],
            ],
            [
                'q' => "The Riesz Representation Theorem states that every bounded linear functional on a Hilbert space H can be written as:",
                'opts' => [
                    ['F(x) = ‖x‖', false],
                    ['F(x) = ⟨x, y⟩ for a unique y ∈ H', true],
                    ['F(x) = sup|x|', false],
                    ['F(x) = ∫ x dx', false],
                ],
            ],
            [
                'q' => "In which space does the Fourier transform define an isometry (Parseval's/Plancherel's theorem)?",
                'opts' => [
                    ['C([a,b])', false],
                    ['L¹(ℝ)', false],
                    ['L²(ℝ)', true],
                    ['L∞(ℝ)', false],
                ],
            ],
            [
                'q' => "The Hahn-Banach theorem guarantees:",
                'opts' => [
                    ['Every Hilbert space has an orthonormal basis', false],
                    ['A bounded linear functional on a subspace can be extended to the whole space without increasing its norm', true],
                    ['Every continuous function is Riemann integrable', false],
                    ['Every Banach space is reflexive', false],
                ],
            ],

            // ── MEASURE THEORY & LEBESGUE ─────────────────────────────────
            [
                'q' => "The Dominated Convergence Theorem states: if |fₙ| ≤ g (integrable) and fₙ → f pointwise, then:",
                'opts' => [
                    ['∫ fₙ → 0', false],
                    ['∫ fₙ → ∫ f (limit and integral can be exchanged)', true],
                    ['fₙ converges uniformly', false],
                    ['g must equal f', false],
                ],
            ],
            [
                'q' => "A function is Lebesgue integrable but NOT Riemann integrable. Which is the classic example?",
                'opts' => [
                    ['f(x) = 1/x on (0,1]', false],
                    ['f(x) = sin(x)/x', false],
                    ['The Dirichlet function: f(x) = 1 if x ∈ ℚ, 0 otherwise', true],
                    ['f(x) = x^(−1/2) on (0,1]', false],
                ],
            ],
            [
                'q' => "Egorov's theorem states that for finite measure spaces, pointwise a.e. convergence implies:",
                'opts' => [
                    ['L² convergence', false],
                    ['Uniform convergence on the entire space', false],
                    ['Almost uniform convergence (uniform convergence outside a set of arbitrarily small measure)', true],
                    ['L¹ convergence', false],
                ],
            ],
            [
                'q' => "Fatou's lemma states: for non-negative measurable fₙ,\n∫ lim inf fₙ dμ ≤ ?",
                'opts' => [
                    ['∫ lim sup fₙ dμ', false],
                    ['lim inf ∫ fₙ dμ', true],
                    ['lim sup ∫ fₙ dμ', false],
                    ['∑ ∫ fₙ dμ', false],
                ],
            ],

            // ── PRODUCTION PYTHON — REAL-WORLD CODE REVIEW ───────────────
            [
                'q' => "This production gradient descent has a critical numerical issue. Identify it:\n\nimport numpy as np\n\ndef gradient_descent(grad_f, x0, lr=0.1, n_iter=1000):\n    x = x0\n    for i in range(n_iter):\n        x = x - lr * grad_f(x)\n    return x\n\n# Minimizing f(x) = x^100\nresult = gradient_descent(lambda x: 100 * x**99, x0=np.float32(2.0))\nprint(result)",
                'opts' => [
                    ['lr=0.1 is too large — the gradient 100·2^99 is astronomically large, causing immediate overflow', true],
                    ['Should use float64 not float32', false],
                    ['n_iter=1000 is insufficient', false],
                    ['The lambda gradient formula is wrong', false],
                ],
            ],
            [
                'q' => "A data scientist runs this integral estimation in production. What is the problem?\n\nimport scipy.integrate as integrate\n\ndef compute_kl_divergence(p_samples, q_func):\n    \"\"\"Estimate KL(P||Q) = E_p[log p(x)/q(x)]\"\"\"\n    log_ratios = [np.log(p) - np.log(q_func(x))\n                  for x, p in p_samples]\n    return np.mean(log_ratios)\n\n# q_func can return 0 for out-of-support samples",
                'opts' => [
                    ['np.mean is not appropriate for KL divergence', false],
                    ['log(0) = −∞ when q_func(x) = 0, causing NaN/−inf in production', true],
                    ['Should use scipy.integrate instead of manual summation', false],
                    ['p_samples should be sorted first', false],
                ],
            ],
            [
                'q' => "What is the numerical issue in this Jacobian computation?\n\nimport numpy as np\n\ndef jacobian(f, x, h=1e-8):\n    n = len(x)\n    J = np.zeros((n, n))\n    for i in range(n):\n        x_plus = x.copy(); x_plus[i] += h\n        x_minus = x.copy(); x_minus[i] -= h\n        J[:, i] = (f(x_plus) - f(x_minus)) / (2 * h)\n    return J",
                'opts' => [
                    ['Central differences are wrong — should be forward differences', false],
                    ['The code is correct; central differences with h=1e-8 is standard practice', true],
                    ['Should divide by h² not 2h', false],
                    ['Cannot use numpy for Jacobians', false],
                ],
            ],
            [
                'q' => "This ML pipeline computes a covariance matrix. Identify the edge case failure:\n\ndef compute_cov(X):\n    \"\"\"X: (n_samples, n_features)\"\"\"\n    mean = X.mean(axis=0)\n    centered = X - mean\n    return (centered.T @ centered) / (len(X) - 1)\n\n# Called with X of shape (1, 10)",
                'opts' => [
                    ['The centering step is wrong', false],
                    ['With only 1 sample, len(X) − 1 = 0 → division by zero', true],
                    ['Should divide by len(X) not len(X) − 1', false],
                    ['centered.T @ centered gives the wrong shape', false],
                ],
            ],
            [
                'q' => "A colleague implements the trapezoidal rule for integrating a probability density. What subtle error exists?\n\nimport numpy as np\n\ndef integrate_pdf(pdf, a, b, n=1000):\n    x = np.linspace(a, b, n)\n    y = np.array([pdf(xi) for xi in x])\n    h = (b - a) / n           # <-- here\n    return h * (y[0]/2 + np.sum(y[1:-1]) + y[-1]/2)\n\n# Should integrate to 1.0 for a valid PDF",
                'opts' => [
                    ['Should use n+1 points: h = (b−a)/n but linspace(a,b,n) gives n points with spacing (b−a)/(n−1)', true],
                    ['The boundary terms y[0]/2 and y[-1]/2 are wrong', false],
                    ['PDF values should be summed not averaged', false],
                    ['h should be (b−a)/(n²)', false],
                ],
            ],

            // ── STOCHASTIC ANALYSIS & PROBABILITY ────────────────────────
            [
                'q' => "A standard Brownian motion W(t) satisfies which of the following properties?",
                'opts' => [
                    ['W(t) − W(s) ~ N(0, t−s) for t > s, with independent increments', true],
                    ['W(t) is differentiable everywhere', false],
                    ['W(t) has finite total variation on any interval', false],
                    ['W(t) is deterministic given W(0) = 0', false],
                ],
            ],
            [
                'q' => "Itô's lemma for f(t, W(t)) states:\ndf = ?",
                'opts' => [
                    ['df = ∂f/∂t dt + ∂f/∂W dW', false],
                    ['df = ∂f/∂t dt + ∂f/∂W dW + (1/2)(∂²f/∂W²) dt', true],
                    ['df = f\'(W) dW', false],
                    ['df = (∂f/∂t + ∂f/∂W) dW', false],
                ],
            ],
            [
                'q' => "The central limit theorem states that for i.i.d. random variables X₁,…,Xₙ with mean μ and variance σ², (X̄ − μ)/(σ/√n) converges in distribution to:",
                'opts' => [
                    ['A uniform distribution', false],
                    ['A Poisson distribution', false],
                    ['N(0, 1)', true],
                    ['A t-distribution', false],
                ],
            ],
            [
                'q' => "The Law of Large Numbers (strong version) states that as n → ∞, X̄ₙ converges to μ:",
                'opts' => [
                    ['In distribution', false],
                    ['In probability only', false],
                    ['Almost surely (with probability 1)', true],
                    ['In L∞ norm', false],
                ],
            ],

            // ── NUMERICAL METHODS AT SCALE ────────────────────────────────
            [
                'q' => "Runge-Kutta 4th order (RK4) for ODEs has what global truncation error?",
                'opts' => [
                    ['O(h)', false],
                    ['O(h²)', false],
                    ['O(h⁴)', true],
                    ['O(h⁵)', false],
                ],
            ],
            [
                'q' => "In Gaussian quadrature with n points, the exact integration is achieved for polynomials of degree up to:",
                'opts' => [
                    ['n − 1', false],
                    ['n', false],
                    ['2n − 1', true],
                    ['2n + 1', false],
                ],
            ],
            [
                'q' => "What is the computational advantage of the Fast Fourier Transform (FFT) over the naive DFT?",
                'opts' => [
                    ['FFT reduces O(n²) to O(n log n)', true],
                    ['FFT reduces O(n log n) to O(n)', false],
                    ['FFT avoids floating point entirely', false],
                    ['FFT requires less memory', false],
                ],
            ],
            [
                'q' => "LU decomposition solves Ax = b in O(n³). For which scenario is it most efficient compared to iterative methods?",
                'opts' => [
                    ['Very large sparse systems (n > 10⁶)', false],
                    ['Multiple right-hand sides b with the same A — factorize once, solve many times in O(n²)', true],
                    ['Symmetric positive-definite matrices only', false],
                    ['When A has condition number > 10⁶', false],
                ],
            ],
            [
                'q' => "When applying the conjugate gradient method to solve Ax = b, the matrix A must be:",
                'opts' => [
                    ['Orthogonal', false],
                    ['Diagonal', false],
                    ['Symmetric positive-definite', true],
                    ['Upper triangular', false],
                ],
            ],

            // ── EDGE CASES & REAL-WORLD FAILURES ─────────────────────────
            [
                'q' => "A softmax function in a neural network is implemented as:\n\ndef softmax(x):\n    return np.exp(x) / np.sum(np.exp(x))\n\nFor input x = [1000, 1000, 1000], what happens and how do you fix it?",
                'opts' => [
                    ['Returns correct [1/3, 1/3, 1/3] — no issue', false],
                    ['np.exp(1000) overflows to inf; fix by subtracting max(x): np.exp(x − max(x))', true],
                    ['Returns [0, 0, 0] due to underflow', false],
                    ['Division by zero because all values are equal', false],
                ],
            ],
            [
                'q' => "A production system computes log-likelihood:\n\nlog_lik = np.sum(np.log(probabilities))\n\nWhere `probabilities` is an array of predicted probabilities from a model. What is the critical edge case?",
                'opts' => [
                    ['np.log is not vectorized', false],
                    ['If any probability = 0, log(0) = −∞, crashing the loss computation; clip to np.clip(p, 1e-15, 1)', true],
                    ['Should use log2 not log', false],
                    ['np.sum does not handle large arrays', false],
                ],
            ],
            [
                'q' => "What is catastrophic cancellation in floating-point arithmetic, in the context of numerical analysis?",
                'opts' => [
                    ['Overflow when adding two large numbers', false],
                    ['Significant loss of precision when subtracting two nearly equal floating-point numbers', true],
                    ['Underflow when dividing a small number by a large number', false],
                    ['Rounding error accumulation over millions of iterations', false],
                ],
            ],
            [
                'q' => "A Monte Carlo integration of f(x) on [0,1] uses:\n\nresult = np.mean([f(np.random.uniform()) for _ in range(N)])\n\nThe standard error of this estimate scales as:",
                'opts' => [
                    ['O(1/N)', false],
                    ['O(1/N²)', false],
                    ['O(1/√N)', true],
                    ['O(ln N / N)', false],
                ],
            ],
            [
                'q' => "In production ML training, gradient explosion in deep networks is detected when:",
                'opts' => [
                    ['The gradient norm approaches zero', false],
                    ['The loss decreases too slowly', false],
                    ['Gradient norms become extremely large (NaN/Inf), causing parameter updates to blow up', true],
                    ['The learning rate is too small', false],
                ],
            ],

            // ── ADVANCED OPTIMIZATION ─────────────────────────────────────
            [
                'q' => "In convex optimization, the KKT (Karush-Kuhn-Tucker) conditions are necessary and sufficient for optimality when:",
                'opts' => [
                    ['The objective is linear', false],
                    ['The problem is convex and constraint qualification holds (e.g., Slater\'s condition)', true],
                    ['All constraints are equality constraints', false],
                    ['The Hessian is positive semi-definite at the boundary', false],
                ],
            ],
            [
                'q' => "The proximal gradient method is used when minimizing f(x) + g(x) where:",
                'opts' => [
                    ['Both f and g are smooth', false],
                    ['f is smooth (differentiable) but g is non-smooth (e.g., L1 norm)', true],
                    ['f is convex and g is non-convex', false],
                    ['g is the constraint indicator function only', false],
                ],
            ],
            [
                'q' => "BFGS (Broyden–Fletcher–Goldfarb–Shanno) is a quasi-Newton method that approximates:",
                'opts' => [
                    ['The gradient of the objective', false],
                    ['The inverse Hessian using gradient information, avoiding direct Hessian computation', true],
                    ['The step size using line search only', false],
                    ['The objective function using Taylor series', false],
                ],
            ],
            [
                'q' => "The Adam optimizer maintains which two moment estimates?",
                'opts' => [
                    ['First moment (mean of gradients) and second moment (uncentered variance of gradients)', true],
                    ['Gradient and Hessian', false],
                    ['Gradient and learning rate', false],
                    ['Loss value and gradient norm', false],
                ],
            ],

            // ── SPECTRAL THEORY & LINEAR OPERATORS ───────────────────────
            [
                'q' => "The spectral radius ρ(A) of a matrix A is defined as:",
                'opts' => [
                    ['The largest singular value of A', false],
                    ['The Frobenius norm of A', false],
                    ['The absolute value of the largest eigenvalue of A', true],
                    ['The trace of A divided by n', false],
                ],
            ],
            [
                'q' => "For a symmetric positive-definite matrix A, all eigenvalues are:",
                'opts' => [
                    ['Complex with positive imaginary parts', false],
                    ['Zero', false],
                    ['Strictly positive real numbers', true],
                    ['Between -1 and 1', false],
                ],
            ],
            [
                'q' => "Singular Value Decomposition (SVD): A = UΣVᵀ. The columns of U are:",
                'opts' => [
                    ['The eigenvectors of A', false],
                    ['The left singular vectors (eigenvectors of AAᵀ)', true],
                    ['The diagonal entries of Σ', false],
                    ['The right singular vectors (eigenvectors of AᵀA)', false],
                ],
            ],
            [
                'q' => "In PCA, the principal components are the eigenvectors of which matrix?",
                'opts' => [
                    ['The data matrix X', false],
                    ['The covariance matrix XᵀX (or sample covariance)', true],
                    ['The Gram matrix XXᵀ', false],
                    ['The precision matrix (inverse covariance)', false],
                ],
            ],

            // ── REAL-WORLD APPLIED PROBLEMS ────────────────────────────────
            [
                'q' => "In signal processing, the Nyquist-Shannon sampling theorem states that to perfectly reconstruct a signal with maximum frequency f_max, you must sample at a rate of at least:",
                'opts' => [
                    ['f_max', false],
                    ['2 · f_max', true],
                    ['π · f_max', false],
                    ['f_max / 2', false],
                ],
            ],
            [
                'q' => "A data pipeline computes rolling statistics on a time series. The mathematical property being exploited when using an exponentially weighted moving average (EWMA) is:",
                'opts' => [
                    ['The series is periodic', false],
                    ['The geometric decay of weights allows O(1) updates per step using the recurrence: μₜ = αxₜ + (1−α)μₜ₋₁', true],
                    ['The series has zero mean', false],
                    ['The Fourier transform of the weights is sparse', false],
                ],
            ],
            [
                'q' => "In a recommendation system, you minimize ‖R − UVᵀ‖²_F (matrix factorization). The Frobenius norm is defined as:",
                'opts' => [
                    ['The largest singular value of (R − UVᵀ)', false],
                    ['√(∑ᵢ∑ⱼ aᵢⱼ²)', true],
                    ['The nuclear norm (sum of singular values)', false],
                    ['The trace of (R − UVᵀ)', false],
                ],
            ],
            [
                'q' => "In Gaussian process regression, the predictive mean at a new point x* is given by k(x*, X)[K(X,X) + σ²I]⁻¹y. The dominant computational cost is:",
                'opts' => [
                    ['Computing k(x*, X): O(n)', false],
                    ['Inverting [K(X,X) + σ²I]: O(n³)', true],
                    ['Computing the predictive variance: O(n²)', false],
                    ['The dot product with y: O(n)', false],
                ],
            ],
            [
                'q' => "A Fourier transform is used in time-series anomaly detection. A spike in the frequency domain at frequency f means:",
                'opts' => [
                    ['A single outlier point in the time series', false],
                    ['A periodic pattern with period 1/f repeating throughout the signal', true],
                    ['The signal has mean f', false],
                    ['The derivative of the signal equals f', false],
                ],
            ],
            [
                'q' => "When computing the matrix exponential e^(At) for a stiff ODE system, standard explicit methods (like Euler) require extremely small step sizes because:",
                'opts' => [
                    ['e^(At) grows too fast for explicit methods', false],
                    ['The eigenvalues of A span a wide range; stability requires h < 2/max|λ|, forcing h → 0', true],
                    ['The matrix exponential is not differentiable', false],
                    ['Explicit methods cannot handle matrix inputs', false],
                ],
            ],
            [
                'q' => "In Bayesian inference, the posterior p(θ|X) ∝ p(X|θ)·p(θ). When the posterior is intractable, which method approximates it by finding the closest distribution in a family?",
                'opts' => [
                    ['Markov Chain Monte Carlo (MCMC)', false],
                    ['Rejection sampling', false],
                    ['Variational inference (minimizes KL divergence between approximate q(θ) and true posterior)', true],
                    ['Bootstrap sampling', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 4 — Mathematical Analysis I (Professional).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Professional");
    }
}