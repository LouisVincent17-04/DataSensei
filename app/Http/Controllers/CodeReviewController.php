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

    private string $systemPrompt = <<<'PROMPT'
You are a code reviewer. Review the code below and respond using exactly this format:

Status: <Correct | Has Issues>
Issues:
- <issue or "None">
Suggestions:
- <suggestion or "None">

Be brief. One line per point. Do not repeat instructions.
PROMPT;

    /**
     * POST /api/code-review
     * Accepts: { code, language, question? }
     * Returns: { ok: true, message: "..." }
     */
    public function review(Request $request): JsonResponse
    {
        $code     = trim($request->input('code', ''));
        $language = trim($request->input('language', 'python'));
        $question = trim($request->input('question', ''));

        if ($code === '') {
            return response()->json(['ok' => false, 'message' => 'No code provided.'], 422);
        }

        if ($question !== '') {
            $prompt = "{$this->systemPrompt}\n\nCode under review:\n```\n{$code}\n```\n\nFollow-up question: {$question}";
        } else {
            $prompt = "{$this->systemPrompt}\n\nCode to review:\n```\n{$code}\n```";
        }

        $response = Http::timeout($this->timeout)->post($this->ollamaUrl, [
            'model'  => $this->model,
            'prompt' => $prompt,
            'stream' => false,
            'options' => [
                'temperature' => 0.2,
                'num_predict' => 512,
            ],
        ]);

        if ($response->failed()) {
            return response()->json([
                'ok'      => false,
                'message' => "Could not reach Ollama at {$this->ollamaUrl}. Make sure Ollama is running (`ollama serve`) and deepseek-coder is pulled.",
            ], 502);
        }

        $body    = $response->json();
        $message = $body['response'] ?? 'Empty response from Ollama.';

        return response()->json(['ok' => true, 'message' => $message]);
    }
}