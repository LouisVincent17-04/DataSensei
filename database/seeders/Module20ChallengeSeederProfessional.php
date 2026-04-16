<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module20ChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (!$category) {
            $this->command->error("Professional category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        Challenge::where('challenge_category_id', $category->id)
                 ->where('title', 'Analysis of Unstructured Data')
                 ->delete();

        $this->command->info("Creating Module 20 — Analysis of Unstructured Data (Professional)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Analysis of Unstructured Data',
            'description'           => 'Professional-grade problems on production NLP/CV/audio system design, LLM deployment at scale, edge cases in unstructured data pipelines, responsible AI, and real-world architectural decisions. For working ML engineers, NLP researchers, and AI architects.',
            'time_limit_seconds'    => 2400,
            'base_xp'               => 2000,
            'order_index'           => 20,
        ]);

        $this->command->info("Seeding 50 professional-level Analysis of Unstructured Data questions...");

        $qaData = [

            // ── PRODUCTION NLP PIPELINES ──────────────────────────────────
            [
                'q' => "A production sentiment analysis service handles 10,000 requests/second. The BERT model takes 120ms per request on CPU. Which deployment strategy achieves sub-10ms p99 latency at scale?",
                'opts' => [
                    ['Deploy more CPU servers', false],
                    ['Use GPU with dynamic batching (accumulate requests into batches), apply INT8 quantisation, and optionally distil to a smaller model (DistilBERT/TinyBERT) — reducing inference time by 4-8x', true],
                    ['Cache all possible inputs and their predictions', false],
                    ['Switch to a simpler TF-IDF + logistic regression pipeline', false],
                ],
            ],
            [
                'q' => "A company deploys a named entity recognition system. After 6 months, F1 score on live data drops from 0.91 to 0.74. No code changes were made. The most likely cause is:",
                'opts' => [
                    ['A Python version update broke the model', false],
                    ['Concept drift — the distribution of entity types, domains, or language in production has shifted from the training distribution', true],
                    ['The model weights became corrupted over time', false],
                    ['The evaluation metric was calculated incorrectly at launch', false],
                ],
            ],
            [
                'q' => "You are building a real-time content moderation pipeline for a social platform with 100M posts/day. Which architectural pattern is correct?",
                'opts' => [
                    ['Run a single BERT model on each post synchronously', false],
                    ['Stream posts through Kafka → lightweight fast classifier for high-recall filtering → async heavy model review for flagged posts → human review queue for edge cases', true],
                    ['Store all posts and batch classify once per day', false],
                    ['Use keyword matching only for simplicity', false],
                ],
            ],
            [
                'q' => "A document search system using dense retrieval (bi-encoder + FAISS) returns semantically related but irrelevant documents. The best fix is:",
                'opts' => [
                    ['Increase the vector dimension', false],
                    ['Add a cross-encoder re-ranker on the top-k retrieved candidates — the bi-encoder trades accuracy for speed; the cross-encoder jointly encodes query and document for precise relevance scoring', true],
                    ['Reduce the number of indexed documents', false],
                    ['Switch to BM25 keyword search instead', false],
                ],
            ],
            [
                'q' => "Your multilingual NLP system performs well on English (94% F1) but poorly on Swahili (51% F1) in a zero-shot setting using mBERT. The correct approach is:",
                'opts' => [
                    ['mBERT cannot support Swahili — use a different model', false],
                    ['Switch to a model trained on more multilingual data with better low-resource language coverage (e.g. XLM-R trained on 100 languages with dedicated Swahili data) and/or translate Swahili training examples', true],
                    ['Use language detection to skip Swahili inputs', false],
                    ['Increase the number of attention heads', false],
                ],
            ],

            // ── LLM DEPLOYMENT & OPTIMISATION ────────────────────────────
            [
                'q' => "A 70B parameter LLM requires approximately how much GPU VRAM to serve in FP16 precision?",
                'opts' => [
                    ['35 GB (FP16 = 2 bytes/param, 70B × 2 ≈ 140 GB, not 35 GB)', false],
                    ['~140 GB just for weights — typically requiring multiple A100/H100 GPUs with tensor parallelism', true],
                    ['7 GB — models are compressed by default', false],
                    ['70 GB exactly', false],
                ],
            ],
            [
                'q' => "What is speculative decoding in LLM inference and what performance gain does it provide?",
                'opts' => [
                    ['Generating multiple hypotheses simultaneously (beam search)', false],
                    ['A small draft model generates several tokens speculatively; the large target model verifies them in parallel — accepted tokens are kept, rejected ones regenerated. Achieves 2-3x speedup with identical output quality', true],
                    ['Quantising the model during inference', false],
                    ['Skipping attention computation for non-informative tokens', false],
                ],
            ],
            [
                'q' => "FlashAttention improves transformer training/inference efficiency by:",
                'opts' => [
                    ['Reducing the number of attention heads', false],
                    ['Reordering attention computation to be IO-aware — tiling the Q, K, V matrices to fit in fast SRAM, avoiding slow HBM reads/writes of the full n×n attention matrix', true],
                    ['Approximating attention with a low-rank matrix', false],
                    ['Using 4-bit quantisation for attention weights', false],
                ],
            ],
            [
                'q' => "A production RAG system suffers from 'lost in the middle' — the LLM ignores relevant context when it is placed in the middle of a long prompt. The evidence-based fix is:",
                'opts' => [
                    ['Always retrieve fewer documents', false],
                    ['Place the most relevant retrieved chunks at the beginning and end of the context window, not in the middle — LLMs have recency and primacy biases', true],
                    ['Increase the context window size', false],
                    ['Use a larger embedding model', false],
                ],
            ],
            [
                'q' => "Quantisation-aware training (QAT) is preferred over post-training quantisation (PTQ) when:",
                'opts' => [
                    ['The model has fewer than 1B parameters', false],
                    ['The deployment target requires aggressive quantisation (INT4 or lower) — QAT simulates quantisation noise during training, allowing the model to adapt, whereas PTQ may cause significant accuracy degradation', true],
                    ['PTQ is always insufficient', false],
                    ['The model needs to run on CPU only', false],
                ],
            ],

            // ── TEXT DATA — PRODUCTION EDGE CASES ────────────────────────
            [
                'q' => "A text classifier is evaluated with macro-F1 = 0.92 on a balanced test set, then deployed. In production, class distribution shifts from 20% each (5 classes) to one class at 95%. What happens to perceived performance?",
                'opts' => [
                    ['Performance stays the same', false],
                    ['Micro-F1 (accuracy-weighted) will be high even if the model completely fails on minority classes — macro-F1 and per-class metrics must be monitored separately in production', true],
                    ['The model automatically adapts to the new distribution', false],
                    ['F1 becomes undefined with this distribution shift', false],
                ],
            ],
            [
                'q' => "A production NLP system tokenises user input with a fixed vocabulary. A user submits a 50,000-character input filled with rare Unicode characters. What is the production risk?",
                'opts' => [
                    ['The tokeniser will crash on non-ASCII input', false],
                    ['BPE/SentencePiece tokenises unknown characters into many individual tokens — a 50,000-char input could exceed the model\'s max sequence length (512/2048) and need truncation, losing critical information', true],
                    ['Unicode input causes gradient explosion', false],
                    ['The model cannot process inputs over 1,000 characters', false],
                ],
            ],
            [
                'q' => "You are building a PII (Personally Identifiable Information) redaction system using NER. An evaluation shows 99% entity recall. Why might this still be unacceptable for production?",
                'opts' => [
                    ['99% recall is always sufficient for production', false],
                    ['In PII redaction, a 1% miss rate on millions of daily documents means thousands of PII leaks — the system needs near-perfect recall (>99.9%) with human review for edge cases, or a conservative approach that prefers false positives', true],
                    ['The problem requires higher precision, not recall', false],
                    ['NER cannot detect PII — use regex only', false],
                ],
            ],
            [
                'q' => "Your LLM-powered customer support system occasionally generates harmful outputs. Listing the correct layered defence strategy:",
                'opts' => [
                    ['Block all outputs containing specific keywords', false],
                    ['Input guardrails (classifier on user prompt) → RLHF/instruction-tuned model → output guardrails (toxicity classifier + PII detector) → confidence thresholding → human escalation path', true],
                    ['Use a smaller model that cannot generate harmful content', false],
                    ['Filter outputs over 100 tokens', false],
                ],
            ],

            // ── IMAGE & VISION — PRODUCTION ───────────────────────────────
            [
                'q' => "A medical imaging AI achieves 96% accuracy on the held-out test set but is rejected during clinical validation. The most likely reason is:",
                'opts' => [
                    ['The model is too complex', false],
                    ['Shortcuts/dataset bias — the model learned spurious correlations (e.g. image acquisition device, hospital watermarks, patient demographics) rather than clinically relevant features. Clinical validation reveals this generalisation failure', true],
                    ['The accuracy threshold for medical AI is 99%', false],
                    ['The model was trained on too many images', false],
                ],
            ],
            [
                'q' => "In a real-time video surveillance system, you need to run object detection at 30 FPS on embedded hardware. Which consideration is most important?",
                'opts' => [
                    ['Maximising mAP on COCO benchmark', false],
                    ['Optimising the latency-accuracy Pareto front — use a lightweight detector (MobileNet-SSD, YOLO-nano), apply TensorRT/ONNX optimisation, and profile memory bandwidth vs compute balance for the target device', true],
                    ['Using the largest model that fits in GPU memory', false],
                    ['Processing every other frame to halve computation', false],
                ],
            ],
            [
                'q' => "A self-supervised vision model (DINO, MAE) is preferred over supervised pre-training when:",
                'opts' => [
                    ['Labelled data is abundant', false],
                    ['Large amounts of unlabelled images are available but labels are scarce or expensive — self-supervised models learn general visual representations that transfer well to downstream tasks with few labels', true],
                    ['The task is purely classification', false],
                    ['GPU memory is very limited', false],
                ],
            ],
            [
                'q' => "What is the difference between semantic segmentation and instance segmentation?",
                'opts' => [
                    ['Semantic segmentation is faster; instance segmentation is more accurate', false],
                    ['Semantic segmentation assigns a class label to each pixel without distinguishing instances of the same class. Instance segmentation identifies each individual object instance separately (e.g. Mask R-CNN)', true],
                    ['Instance segmentation only works on person detection', false],
                    ['They are identical tasks with different names', false],
                ],
            ],

            // ── AUDIO & SPEECH — PRODUCTION ───────────────────────────────
            [
                'q' => "An ASR system achieves 3% WER (Word Error Rate) on clean speech but 35% WER on noisy factory-floor audio. The production engineering solution is:",
                'opts' => [
                    ['Accept the degradation and set user expectations', false],
                    ['Add a speech enhancement / noise suppression preprocessing stage (e.g. RNNoise, DeepFilterNet), collect and fine-tune on domain-specific noisy audio, and apply multi-condition training', true],
                    ['Increase microphone sampling rate', false],
                    ['Switch from Whisper to a rules-based ASR', false],
                ],
            ],
            [
                'q' => "A real-time speaker diarisation system ('who spoke when') fails when two speakers overlap simultaneously. The correct technical approach is:",
                'opts' => [
                    ['Ignore overlapping speech segments', false],
                    ['Use a multi-speaker separation model (e.g. SepFormer) to isolate speaker signals before diarisation, and train the diarisation model on overlapping speech data', true],
                    ['Assign overlapping segments to the most recent speaker', false],
                    ['Increase the MFCC window size', false],
                ],
            ],

            // ── RESPONSIBLE AI & FAIRNESS ─────────────────────────────────
            [
                'q' => "A hiring résumé screening NLP model shows significantly lower recall for female candidates. The audit reveals the training data consisted of historical hires which were 85% male. The correct intervention is:",
                'opts' => [
                    ['Remove gender words from résumés only', false],
                    ['Conduct a full fairness audit: debias training data (resampling/reweighting), apply fairness constraints during training (equalized odds), evaluate with demographic parity and equalised recall across groups, and consider human review', true],
                    ['Increase the decision threshold for male candidates', false],
                    ['Train separate models per gender', false],
                ],
            ],
            [
                'q' => "What is 'data poisoning' as an adversarial attack on NLP systems?",
                'opts' => [
                    ['Corrupting the model weights post-deployment', false],
                    ['Injecting malicious training examples that cause the model to learn a hidden backdoor behaviour — e.g. always predicting a specific label when a trigger phrase is present', true],
                    ['Flooding the API with adversarial prompts', false],
                    ['Deleting training data after model deployment', false],
                ],
            ],
            [
                'q' => "Differential privacy in NLP model training adds noise to:",
                'opts' => [
                    ['The input text during inference', false],
                    ['The gradients during training (DP-SGD) to provide mathematical guarantees that the trained model cannot reveal information about individual training examples', true],
                    ['The model weights after training', false],
                    ['The tokeniser vocabulary', false],
                ],
            ],
            [
                'q' => "Membership inference attacks on LLMs aim to determine:",
                'opts' => [
                    ['Which GPU was used for training', false],
                    ['Whether a specific text sample was part of the model\'s training data — a privacy concern as models may memorise sensitive training content', true],
                    ['The architecture of the model', false],
                    ['The training loss at convergence', false],
                ],
            ],

            // ── SYSTEM DESIGN & ARCHITECTURE ─────────────────────────────
            [
                'q' => "You are designing a semantic search system for 50 million legal documents. Rank the correct architecture choices:",
                'opts' => [
                    ['TF-IDF index → full-text keyword search → keyword re-ranking', false],
                    ['Offline: encode all documents with a bi-encoder → index in FAISS with IVF-PQ for approximate nearest-neighbour search → Online: encode query → ANN search → cross-encoder re-ranking of top-100 → return top-10', true],
                    ['Re-encode all documents per query at runtime', false],
                    ['Use only a cross-encoder for all 50M documents', false],
                ],
            ],
            [
                'q' => "FAISS IVF (Inverted File Index) with PQ (Product Quantisation) reduces memory and search time by:",
                'opts' => [
                    ['Storing only the top-k most important vectors', false],
                    ['IVF clusters vectors into cells and searches only nearby cells; PQ compresses each vector by decomposing it into sub-vector codes — together enabling billion-scale ANN search with acceptable recall', true],
                    ['Reducing vector dimensions via PCA first', false],
                    ['Using 32-bit integers instead of floats', false],
                ],
            ],
            [
                'q' => "What is the embedding model cold-start problem in a new product recommendation system using semantic embeddings?",
                'opts' => [
                    ['The embedding model takes too long to load', false],
                    ['New items with no interaction history cannot be represented by collaborative-filtering embeddings. Solution: use content-based embeddings (text/image descriptions) as a warm start, then blend with interaction-based embeddings as data accumulates', true],
                    ['New users have no browsing history', false],
                    ['The vocabulary does not contain new product names', false],
                ],
            ],
            [
                'q' => "A vector database (Pinecone, Weaviate, Qdrant) is preferred over storing embeddings in a traditional relational DB because:",
                'opts' => [
                    ['Vector databases support SQL queries', false],
                    ['They provide native ANN (approximate nearest-neighbour) indexing for fast similarity search at scale — relational DBs require computing exact pairwise distances across all rows which does not scale', true],
                    ['They compress vectors more efficiently', false],
                    ['They support real-time training of embedding models', false],
                ],
            ],

            // ── EMERGING TECHNIQUES ───────────────────────────────────────
            [
                'q' => "Mixture of Experts (MoE) LLMs (e.g. Mixtral 8x7B) achieve efficiency by:",
                'opts' => [
                    ['Running all 8 expert sub-networks in parallel always', false],
                    ['Routing each token to only a subset of expert feed-forward networks (e.g. top-2 of 8) via a learned gating mechanism — total parameters are large but FLOPs per token remain constant', true],
                    ['Using 8 separate models and ensembling their outputs', false],
                    ['Splitting the model across 8 GPUs', false],
                ],
            ],
            [
                'q' => "Constitutional AI (CAI) from Anthropic trains LLMs to be helpful, harmless, and honest by:",
                'opts' => [
                    ['Hard-coding rules into the model weights', false],
                    ['Using a set of principles (the constitution) to generate critiques and revisions of model outputs during RLAIF (RL from AI Feedback) — the model learns to self-critique using the principles without relying solely on human labels', true],
                    ['Filtering all harmful outputs post-generation', false],
                    ['Training exclusively on curated Wikipedia data', false],
                ],
            ],
            [
                'q' => "What is 'emergent behaviour' in large language models and why is it scientifically important?",
                'opts' => [
                    ['The model generates creative content unprompted', false],
                    ['Abilities (like multi-step reasoning, translation, code generation) that appear sharply and unexpectedly only above certain model scale thresholds — they cannot be predicted by simply extrapolating from smaller models', true],
                    ['The model learns new facts after deployment', false],
                    ['The model develops preferences for certain output styles', false],
                ],
            ],
            [
                'q' => "What is the primary motivation for sparse autoencoders (SAEs) in mechanistic interpretability research on LLMs?",
                'opts' => [
                    ['To compress LLM weights for deployment', false],
                    ['To decompose superimposed neuron activations into interpretable, monosemantic features — neurons in LLMs often represent multiple unrelated concepts (polysemanticity); SAEs separate them into sparse, human-interpretable directions', true],
                    ['To improve LLM performance on benchmarks', false],
                    ['To train LLMs without labelled data', false],
                ],
            ],
            [
                'q' => "In a production unstructured data platform, data versioning (DVC, MLflow artifacts) is critical because:",
                'opts' => [
                    ['It speeds up model training', false],
                    ['Unstructured data (text, images, audio) changes over time — versioning ensures reproducibility of experiments, the ability to audit model decisions with the exact training data used, and rollback capability if new data degrades performance', true],
                    ['It prevents data from being accidentally deleted', false],
                    ['Unstructured data cannot be stored in relational databases', false],
                ],
            ],
            [
                'q' => "What is the key challenge in evaluating generative LLM outputs (e.g. summaries, open-ended QA) compared to classification tasks?",
                'opts' => [
                    ['Generative evaluation requires more compute', false],
                    ['There is no single correct answer — evaluation requires reference-free metrics (GPT-4 as judge, G-Eval), human evaluation with rubrics, or task-specific automated metrics. Ground-truth comparison alone is insufficient', true],
                    ['Classification metrics (F1, accuracy) can be directly applied', false],
                    ['Generative models always outperform classification models', false],
                ],
            ],
            [
                'q' => "A law firm deploys an LLM to summarise case documents. The system occasionally 'hallucinates' citations to non-existent court cases. The production-safe solution is:",
                'opts' => [
                    ['Use a larger LLM with fewer hallucinations', false],
                    ['Implement grounded generation with RAG (only cite documents retrieved from a verified legal database), add a citation verification step that checks every generated reference against the actual database, and set confidence thresholds for human review', true],
                    ['Instruct the model to only cite cases it is confident about', false],
                    ['Disable citation generation entirely', false],
                ],
            ],
            [
                'q' => "When chunking long documents for a RAG system, fixed-size character chunking (e.g. every 500 characters) has a critical problem. What is it, and what is the better approach?",
                'opts' => [
                    ['Fixed chunks are too slow to process', false],
                    ['Fixed-size chunks may split sentences or paragraphs mid-thought, destroying semantic coherence. Better: use recursive character splitting respecting natural boundaries (sentences, paragraphs), or semantic chunking that groups contextually related content', true],
                    ['500 characters is always too small', false],
                    ['Fixed chunking does not support Unicode', false],
                ],
            ],
            [
                'q' => "In a large-scale unstructured data ML platform, feature stores for NLP serve what critical production function?",
                'opts' => [
                    ['They replace the need for model training', false],
                    ['They provide consistent, precomputed, versioned text features and embeddings that can be shared across multiple models/teams — eliminating training-serving skew where offline features differ from online feature computation', true],
                    ['They store raw text documents only', false],
                    ['They automatically label unlabelled text data', false],
                ],
            ],
            [
                'q' => "What is the 'training-serving skew' problem in NLP production systems and how is it prevented?",
                'opts' => [
                    ['The model trains faster than it serves', false],
                    ['The preprocessing/feature extraction code used during training differs from the code used in production serving — causing silent accuracy degradation. Prevention: use the same pipeline code (same library versions, same tokeniser) in both training and serving, enforced via containerisation', true],
                    ['The training data is larger than the serving data', false],
                    ['The model weights change between training and serving', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 20 — Analysis of Unstructured Data (Professional).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Professional");
    }
}