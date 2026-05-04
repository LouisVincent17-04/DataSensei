<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;

class CodeReviewController extends Controller
{
    private string $ollamaUrl = 'http://localhost:11434/api/generate';
    private string $model     = 'deepseek-coder';
    private int    $timeout   = 90;

    // ── Used for initial auto-review on every Run ──────────────────────────
    private string $reviewPrompt = <<<'PROMPT'
You are a strict code and SQL reviewer for an educational platform.

Your ONLY job is to review code that is submitted to you. Always respond using exactly this format:

Status: <Correct | Has Issues>
Issues:
- <issue or "None">
Suggestions:
- <suggestion or "None">

Rules:
- Be brief. One line per point.
- Do not add preamble or closing remarks.
- NEVER write, generate, produce, or provide code or SQL of any kind — not even examples or fixes.
  If asked to generate code, respond only with: "I'm a reviewer, not a code generator."
PROMPT;

    // ── Used for follow-up questions ───────────────────────────────────────
    private string $chatPrompt = <<<'PROMPT'
You are a helpful code review assistant for an educational platform.

Your role is to answer follow-up questions about code or SQL that has already been reviewed.

Rules:
- NEVER write, generate, produce, or provide code or SQL of any kind — not even examples, fixes, or snippets.
  If asked to generate code, respond only with: "I'm a reviewer, not a code generator. I can only help you understand your existing code."
- You may explain what is wrong and WHY, describe what a fix should conceptually do, or point to relevant concepts — but never produce working code.
- Keep answers clear and concise.
PROMPT;

    /**
     * POST /api/code-review
     *
     * Body fields:
     *   mode     — "review" (initial auto-review) or "chat" (follow-up question)
     *   code     — the code or SQL being discussed
     *   language — "python" | "mysql" (informational, used in prompt context)
     *   question — required when mode=chat
     */
    public function review(Request $request): JsonResponse
    {
        $request->validate([
            'mode'     => 'required|in:review,chat',
            'code'     => 'required|string|max:10000',
            'language' => 'nullable|string|max:20',
            'question' => 'nullable|string|max:2000',
        ]);

        $mode     = $request->input('mode');
        $code     = trim($request->input('code'));
        $language = trim($request->input('language', 'python'));
        $question = trim($request->input('question', ''));

        // ── Build prompt based on mode ─────────────────────────────────────
        if ($mode === 'chat') {
            if ($question === '') {
                return response()->json([
                    'ok'      => false,
                    'message' => 'A follow-up question is required for chat mode.',
                ], 422);
            }

            $prompt = "{$this->chatPrompt}\n\n"
                    . "Context — the {$language} code being discussed:\n```\n{$code}\n```\n\n"
                    . "User question: {$question}";

            $temperature = 0.4;
            $numPredict  = 768;

        } else {
            // mode === 'review'
            $prompt = "{$this->reviewPrompt}\n\n"
                    . "Code to review ({$language}):\n```\n{$code}\n```";

            $temperature = 0.2;
            $numPredict  = 512;
        }

        // ── Call Ollama ────────────────────────────────────────────────────
        $response = Http::timeout($this->timeout)->post($this->ollamaUrl, [
            'model'   => $this->model,
            'prompt'  => $prompt,
            'stream'  => false,
            'options' => [
                'temperature' => $temperature,
                'num_predict' => $numPredict,
            ],
        ]);

        if ($response->failed()) {
            return response()->json([
                'ok'      => false,
                'message' => "Could not reach Ollama at {$this->ollamaUrl}. "
                           . "Make sure Ollama is running (`ollama serve`) and {$this->model} is pulled.",
            ], 502);
        }

        $body    = $response->json();
        $message = $body['response'] ?? 'Empty response from Ollama.';

        return response()->json(['ok' => true, 'message' => trim($message)]);
    }
}