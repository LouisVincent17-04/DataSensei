<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;

class Module17ChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (!$category) {
            $this->command->error("Professional category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 17 — Introduction to Deep Learning (Professional)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introduction to Deep Learning',
            'description'           => 'Professional deep learning challenges drawn from production ML systems — model serving latency, training at scale, distributed strategies, quantization trade-offs, adversarial robustness, LLM fine-tuning pipelines, and real-world architectural decisions.',
            'time_limit_seconds'    => 2400,
            'base_xp'               => 2000,
            'order_index'           => 17,
        ]);

        $this->command->info("Seeding 30 professional-level questions...");

        $qaData = [

            // ── PRODUCTION SERVING ────────────────────────────────────────
            [
                'q' => "You need to serve a ResNet-50 model with <20ms p99 latency on CPU for 1000 RPS. Profiling shows the bottleneck is matrix multiplication in the fully connected layers. The best optimization strategy is:\n\n```\nCurrent: FP32 weights, batch_size=1, no optimization\nTarget: <20ms, CPU inference\n```",
                'opts' => [
                    ['Switch to a GPU — CPUs cannot meet this latency target for ResNets', false],
                    ['Apply INT8 post-training quantization with ONNX Runtime or TorchScript — reduces memory bandwidth and uses CPU SIMD instructions', true],
                    ['Increase batch_size to 32 to improve throughput at the cost of latency', false],
                    ['Replace ResNet-50 with a Transformer — Transformers are faster on CPU', false],
                ],
            ],
            [
                'q' => "Dynamic vs. Static quantization in PyTorch:\n\n```python\n# Static quantization requires calibration data:\ntorch.quantization.prepare(model, inplace=True)\nrun_calibration(model, calibration_loader)  # collect activation ranges\ntorch.quantization.convert(model, inplace=True)\n\n# Dynamic quantization — no calibration:\ntorch.quantization.quantize_dynamic(model, {nn.Linear}, dtype=torch.qint8)\n```\n\nStatic quantization is preferred for production because:",
                'opts' => [
                    ['Static quantization is simpler to implement with no extra steps', false],
                    ['Static quantization pre-computes activation scales from real data — lower latency since scales are fixed at inference time', true],
                    ['Dynamic quantization requires GPU hardware for calibration', false],
                    ['Static quantization supports all layer types; dynamic only supports Linear', false],
                ],
            ],
            [
                'q' => "A model deployed in production has unexpectedly high memory usage causing OOM crashes under load:\n\n```python\nclass InferenceService:\n    def predict(self, inputs):\n        with torch.no_grad():\n            outputs = self.model(inputs)\n            return outputs.numpy()  # BUG\n```\n\nWhat memory issue exists here?",
                'opts' => [
                    ['torch.no_grad() prevents memory from being freed — remove it', false],
                    ['outputs.numpy() retains a reference to the underlying tensor memory — use outputs.detach().cpu().numpy() and del outputs after use', true],
                    ['.numpy() requires the tensor to be on GPU first', false],
                    ['self.model should be re-instantiated per request to clear memory', false],
                ],
            ],
            [
                'q' => "You profile a Transformer inference pipeline and find attention computation is the bottleneck at sequence length 4096. The most effective optimization is:\n\n```\nStandard attention: O(n²) memory and time complexity\n```",
                'opts' => [
                    ['Reduce the number of attention heads from 16 to 8', false],
                    ['Use FlashAttention — a hardware-aware exact attention algorithm with O(n) memory using tiling and recomputation', true],
                    ['Apply dropout to the attention weights at inference time', false],
                    ['Switch from multi-head to single-head attention', false],
                ],
            ],

            // ── DISTRIBUTED TRAINING ──────────────────────────────────────
            [
                'q' => "You are training a large model across 8 GPUs using PyTorch DDP (Distributed Data Parallel). Each GPU processes a mini-batch of 32 samples. The effective batch size is 256. After each backward pass, DDP:\n\n```\nGPU 0: grad_0  ──┐\nGPU 1: grad_1  ──┤\n...                ├── AllReduce → averaged gradient → applied on all GPUs\nGPU 7: grad_7  ──┘\n```",
                'opts' => [
                    ['Sends gradients only from GPU 0 to all others (broadcast)', false],
                    ['Performs an AllReduce operation to average gradients across all GPUs, ensuring all model replicas stay synchronized', true],
                    ['Each GPU updates its own weights independently with local gradients', false],
                    ['Gradients are summed (not averaged) before being applied to weights', false],
                ],
            ],
            [
                'q' => "Model Parallelism splits model layers across multiple GPUs, unlike Data Parallelism which replicates the full model. Model parallelism is necessary when:\n\n```\nModel size: 175B parameters (GPT-3)\nSingle A100 GPU VRAM: 80GB\nFP16 model memory: 175B × 2 bytes ≈ 350GB\n```",
                'opts' => [
                    ['You want to train faster on a single GPU', false],
                    ['The model is too large to fit on a single GPU — layers must be distributed across multiple devices', true],
                    ['Data parallelism produces poor gradient quality for large models', false],
                    ['Model parallelism always outperforms data parallelism for any model size', false],
                ],
            ],
            [
                'q' => "Gradient checkpointing (activation recomputation) trades compute for memory by:\n\n```python\nfrom torch.utils.checkpoint import checkpoint\n\noutput = checkpoint(layer_function, input)  # Activations not stored\n```",
                'opts' => [
                    ['Storing all activations in CPU RAM instead of GPU VRAM', false],
                    ['Not storing intermediate activations during the forward pass — recomputing them during backward pass when needed, at ~33% extra compute cost', true],
                    ['Quantizing all activations to INT8 during the forward pass', false],
                    ['Skipping the backward pass for selected layers to save memory', false],
                ],
            ],

            // ── LLM FINE-TUNING ───────────────────────────────────────────
            [
                'q' => "LoRA (Low-Rank Adaptation) fine-tunes LLMs by:\n\n```\nOriginal weight: W ∈ ℝ^(d×k)\nLoRA: W' = W + BA, where B ∈ ℝ^(d×r), A ∈ ℝ^(r×k), r << d\n```\n\nFor a weight matrix W with d=4096, k=4096, r=16:\nTrainable parameters in LoRA: 2 × 4096 × 16 = 131,072\nOriginal parameters: 4096² = 16,777,216\n\nThe parameter reduction is approximately:",
                'opts' => [
                    ['10× reduction', false],
                    ['128× reduction', true],
                    ['4096× reduction', false],
                    ['2× reduction', false],
                ],
            ],
            [
                'q' => "Instruction fine-tuning a pre-trained LLM using RLHF (Reinforcement Learning from Human Feedback) has three stages. What is the correct order?\n\n```\nA) Fine-tune with PPO using the reward model signal\nB) Train a reward model on human preference rankings  \nC) Supervised fine-tuning on demonstration data\n```",
                'opts' => [
                    ['A → B → C', false],
                    ['B → C → A', false],
                    ['C → B → A', true],
                    ['C → A → B', false],
                ],
            ],
            [
                'q' => "QLoRA combines quantization with LoRA. The base model weights are loaded in 4-bit NF4 format while LoRA adapters remain in BF16. The key advantage over standard LoRA is:\n\n```\nStandard LoRA: base model in FP16 (~7B model ≈ 14GB)\nQLoRA: base model in 4-bit (~7B model ≈ 4GB) + adapters in BF16\n```",
                'opts' => [
                    ['QLoRA trains faster because 4-bit arithmetic is faster than FP16', false],
                    ['QLoRA dramatically reduces VRAM — fine-tuning 65B parameter models on a single 48GB GPU becomes feasible', true],
                    ['QLoRA eliminates the need for human feedback data', false],
                    ['QLoRA produces higher quality models than standard LoRA in all cases', false],
                ],
            ],

            // ── ADVERSARIAL ROBUSTNESS ────────────────────────────────────
            [
                'q' => "FGSM (Fast Gradient Sign Method) creates adversarial examples as:\n\nx_adv = x + ε × sign(∇_x L(f(x), y))\n\nA model deployed in a security-critical system is vulnerable to FGSM attacks with ε=0.01 that are imperceptible to humans. The most principled defense is:",
                'opts' => [
                    ['Increase model size — larger models are inherently more robust', false],
                    ['Adversarial training: augment training data with FGSM examples so the model learns robust features', true],
                    ['Apply Gaussian blur to all inputs at inference time', false],
                    ['Use a different loss function that is not differentiable with respect to the input', false],
                ],
            ],
            [
                'q' => "Certified robustness via randomized smoothing guarantees that a model's prediction is stable within a certified radius r around input x:\n\nFor a model f and Gaussian noise σ, the certified radius is:\nr = σ/2 × (Φ⁻¹(p_A) − Φ⁻¹(p_B))\n\nIncreasing σ:\n```",
                'opts' => [
                    ['Increases clean accuracy and increases certified radius', false],
                    ['Decreases clean accuracy but increases certified robustness radius — a fundamental trade-off', true],
                    ['Has no effect on clean accuracy', false],
                    ['Decreases both clean accuracy and certified radius', false],
                ],
            ],

            // ── NEURAL ARCHITECTURE SEARCH ────────────────────────────────
            [
                'q' => "DARTS (Differentiable Architecture Search) makes NAS tractable by:\n\n```\nInstead of searching over discrete architectures,\nDARTS relaxes the search space to be continuous:\no(x) = Σ exp(α_op) / Σ exp(α_op') × op(x)\n```\n\nThis allows architecture search using:",
                'opts' => [
                    ['Reinforcement learning with policy gradients', false],
                    ['Standard gradient descent — architecture parameters α are optimized jointly with weight parameters', true],
                    ['Evolutionary algorithms with mutation operators', false],
                    ['Bayesian optimization over discrete architecture graphs', false],
                ],
            ],
            [
                'q' => "EfficientNet uses a compound scaling method that scales width (W), depth (D), and resolution (R) together:\n\nD = α^φ, W = β^φ, R = γ^φ\nsubject to α × β² × γ² ≈ 2\n\nThe constraint α × β² × γ² ≈ 2 ensures:",
                'opts' => [
                    ['The model parameters increase by exactly 2× with each scaling step', false],
                    ['Total FLOPS roughly double with each compound scaling step φ, maintaining a predictable compute budget', true],
                    ['Width and depth always scale by the same factor', false],
                    ['The model converges in exactly φ epochs', false],
                ],
            ],

            // ── SELF-SUPERVISED & CONTRASTIVE LEARNING ────────────────────
            [
                'q' => "SimCLR (Simple Framework for Contrastive Learning) trains a model by:\n\n```\n1. Apply two random augmentations to each image → (xᵢ, xⱼ)\n2. Encode both: (zᵢ, zⱼ) = f(xᵢ), f(xⱼ)\n3. Maximize agreement between (zᵢ, zⱼ) vs. other pairs (negatives)\nUsing NT-Xent loss (normalized temperature-scaled cross entropy)\n```\n\nLarger batch sizes dramatically improve SimCLR because:",
                'opts' => [
                    ['Larger batches reduce training time proportionally', false],
                    ['More negatives per batch create a harder contrastive task, forcing the model to learn finer-grained representations', true],
                    ['Gradient variance decreases with larger batches, allowing a higher learning rate', false],
                    ['SimCLR requires exact pairs and larger batches provide more exact pairs', false],
                ],
            ],
            [
                'q' => "BYOL (Bootstrap Your Own Latent) achieves state-of-the-art self-supervised performance WITHOUT negative pairs, using:\n\n```\nOnline network: encoder + projector + predictor (trained via gradient)\nTarget network: encoder + projector (updated via EMA of online network)\n```\n\nThe EMA (Exponential Moving Average) target network prevents collapse because:",
                'opts' => [
                    ['EMA makes the target network identical to the online network at each step', false],
                    ['The slowly-updating target provides a stable training target — the online network must predict a target that is always slightly ahead', true],
                    ['EMA applies weight decay, acting as an implicit regularizer', false],
                    ['The target network uses a different architecture, preventing symmetry', false],
                ],
            ],

            // ── KNOWLEDGE DISTILLATION ────────────────────────────────────
            [
                'q' => "Knowledge distillation trains a small student model using soft targets from a large teacher:\n\nL_distill = α × L_CE(y_hard, y_student) + (1−α) × T² × KL(σ(z_teacher/T) || σ(z_student/T))\n\nThe temperature T > 1 in the soft targets:\n```",
                'opts' => [
                    ['Sharpens the teacher distribution, emphasizing the most probable class', false],
                    ['Softens the teacher distribution, exposing the student to richer inter-class similarity information in the teacher\'s probability distribution', true],
                    ['Replaces the need for the student to see hard labels', false],
                    ['Acts as a learning rate multiplier for the distillation loss', false],
                ],
            ],
            [
                'q' => "Feature distillation (intermediate layer matching) transfers knowledge beyond the final logits:\n\n```python\nL_feat = MSE(student_feature, teacher_feature.detach())\n```\n\nThe .detach() on the teacher features is critical because:",
                'opts' => [
                    ['Teacher features must be on CPU for MSE computation', false],
                    ['Without detach(), gradients would flow into the teacher network during the student\'s backward pass, corrupting the teacher\'s weights', true],
                    ['Detaching improves numerical stability of MSE', false],
                    ['Teacher features are not differentiable and must be detached', false],
                ],
            ],

            // ── REAL-WORLD EDGE CASES ─────────────────────────────────────
            [
                'q' => "A production image classifier deployed globally shows high accuracy (94%) on the test set but only 71% accuracy for users in certain geographic regions. The root cause and fix is:\n\n```\nTraining data: 90% images from North America and Europe\nTest set: drawn from same distribution as training data\n```",
                'opts' => [
                    ['The model architecture is too simple — add more layers', false],
                    ['Distribution shift — the model was not trained on data representative of all user groups. Collect and augment training data from underrepresented regions', true],
                    ['The learning rate was too high, causing the model to memorize regional features', false],
                    ['The test set should also be restricted to North America/Europe data to match training', false],
                ],
            ],
            [
                'q' => "Catastrophic forgetting occurs when a model fine-tuned on Task B forgets Task A. The most principled solution for continual learning is:\n\n```\nEWC (Elastic Weight Consolidation) adds a penalty:\nL_total = L_B + λ/2 × Σ Fᵢ × (θᵢ − θᵢ*)²\nwhere Fᵢ = Fisher Information (importance of weight i for Task A)\nθᵢ* = optimal weights for Task A\n```",
                'opts' => [
                    ['Penalizing ALL weight changes equally — use L2 regularization from θ*', false],
                    ['Penalizing changes to weights that were IMPORTANT for Task A — the Fisher information matrix identifies which weights matter most', true],
                    ['Replaying all of Task A\'s data during Task B fine-tuning', false],
                    ['Using a higher learning rate for Task B so the model adapts faster', false],
                ],
            ],
            [
                'q' => "In a real-time video understanding system, you need to process 30 FPS with a model that takes 40ms per frame. The system is falling behind. Without changing hardware, the best architectural solution is:\n\n```\nRequired: 30 FPS → 33ms budget per frame\nCurrent: 40ms per frame (ResNet-50)\n```",
                'opts' => [
                    ['Train the model for more epochs to improve speed', false],
                    ['Switch to a lightweight model (MobileNetV3 or EfficientNet-B0) with knowledge distillation from ResNet-50 to preserve accuracy', true],
                    ['Reduce the video resolution to 1 FPS and interpolate', false],
                    ['Batch multiple frames together to improve GPU utilization', false],
                ],
            ],

            // ── ADVANCED GENERATIVE MODELS ────────────────────────────────
            [
                'q' => "Diffusion models (DDPM) generate images through a reverse denoising process. Training objective:\n\nL = E_{t, x₀, ε}[||ε − ε_θ(x_t, t)||²]\n\nwhere ε_θ is the noise prediction network. What does the network learn to predict?",
                'opts' => [
                    ['The clean image x₀ directly from the noisy image x_t', false],
                    ['The noise ε that was added to x₀ to produce x_t — the reverse process denoises step by step', true],
                    ['The timestep t from the noisy image x_t', false],
                    ['The final denoised image in a single forward pass', false],
                ],
            ],
            [
                'q' => "Classifier-Free Guidance (CFG) in diffusion models computes:\n\nε_guided = ε_uncond + w × (ε_cond − ε_uncond)\n\nA guidance scale w = 7.5 (typical for Stable Diffusion) compared to w = 1.0 will produce:",
                'opts' => [
                    ['More diverse, random samples that ignore the conditioning', false],
                    ['Sharper, more conditional samples that closely match the prompt — at the cost of diversity and sometimes realism', true],
                    ['Faster sampling with fewer denoising steps', false],
                    ['Samples identical to unconditional generation', false],
                ],
            ],
            [
                'q' => "CLIP (Contrastive Language-Image Pretraining) enables zero-shot image classification by:\n\n```\nText encoder: e_text = CLIP_text('a photo of a cat')\nImage encoder: e_image = CLIP_image(image)\nSimilarity: cos(e_text, e_image)\n```\n\nFor zero-shot classification across N classes, you compute similarity against N text embeddings and take argmax. The key requirement for this to work is:",
                'opts' => [
                    ['The image encoder must be a CNN — Transformers cannot be used', false],
                    ['Text and image embeddings must lie in a shared semantic space — achieved by contrastive training on (image, text) pairs at web scale', true],
                    ['The model must be fine-tuned on each new set of classes before inference', false],
                    ['All N class names must have appeared in the ImageNet training set', false],
                ],
            ],

            // ── PROFESSIONAL DECISION-MAKING ──────────────────────────────
            [
                'q' => "You are tasked with improving a production NLP model that classifies customer support tickets into 50 categories. The current model has 78% accuracy. You have a budget of 2 GPU-weeks. The training set has 10,000 labelled examples. Which approach maximizes expected accuracy gain?\n\n```\nOptions:\nA) Train GPT-2 (117M) from scratch on the 10K examples\nB) Fine-tune a domain-specific BERT (e.g. DistilBERT) on the 10K examples\nC) Generate 1M synthetic examples using an LLM, then train from scratch\nD) Fine-tune GPT-4 via OpenAI API with 10K examples\n```",
                'opts' => [
                    ['A — training from scratch gives full control over the architecture', false],
                    ['B — fine-tuning a pre-trained language model leverages strong representations and is optimal for this data size and compute budget', true],
                    ['C — synthetic data always improves performance more than pre-training', false],
                    ['D — GPT-4 fine-tuning is always the best option regardless of cost', false],
                ],
            ],
            [
                'q' => "A medical imaging model achieves 97% overall accuracy in detecting a rare cancer (prevalence 1%). However, the clinical team rejects it. The reason is most likely:\n\n```\nClass 0 (No cancer): 98% of data → model accuracy: 99%\nClass 1 (Cancer): 2% of data → model accuracy: 31%\n```",
                'opts' => [
                    ['97% accuracy is too low — the benchmark is 99%', false],
                    ['The model is a majority-class predictor — catastrophic performance on the rare cancer class makes it clinically dangerous despite high overall accuracy', true],
                    ['The model architecture is too complex for medical imaging', false],
                    ['The training data was not normalized correctly', false],
                ],
            ],
            [
                'q' => "The clinical team from above insists on: Recall (Sensitivity) ≥ 95% for cancer class at the cost of lower precision. To achieve this in production:\n\n```python\n# Current threshold:\nprediction = (model_output > 0.5).int()\n\n# Adjusted threshold:\nprediction = (model_output > threshold).int()\n```\n\nTo increase recall to ≥95%, you should:",
                'opts' => [
                    ['Increase the threshold above 0.5 — fewer positive predictions means fewer false positives', false],
                    ['Lower the threshold below 0.5 — the model flags more cases as cancer, catching more true positives at the cost of more false positives', true],
                    ['Retrain with a higher learning rate to increase sensitivity', false],
                    ['Apply softmax to the model output before thresholding', false],
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

        $this->command->info("✅ Done! 30 questions seeded for Module 17 — Introduction to Deep Learning (Professional).");
    }
}