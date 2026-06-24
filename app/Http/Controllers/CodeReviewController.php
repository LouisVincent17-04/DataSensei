<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CodeReviewController extends Controller
{
    private string $reviewPrompt = <<<'PROMPT'
You are DataSensei's Code Feedback Assistant.

Your job is to review Python or SQL code submitted by a student after they run it.
Use the latest code and the latest run output/error as the source of truth.
Do not pretend you executed anything yourself; only use the provided output/error.

When the user just ran code, always respond using exactly this format:

Status: <Correct | Has Issues | Needs More Context>
Issues:
- <issue or "None">
Suggestions:
- <suggestion or "None">

Rules:
- Be brief and beginner-friendly.
- One line per point.
- For Python: check syntax, indentation, input handling, imports, logic, output, and matplotlib/file issues.
- For SQL: check syntax, table/column names, joins, WHERE clauses, grouping, unsafe UPDATE/DELETE, and data type issues.
- If the code is correct but output is missing, say that no output was produced.
- Do not generate a full solution unless the student clearly asks for a sample or explanation.
PROMPT;

    private string $chatPrompt = <<<'PROMPT'
You are DataSensei's Code Feedback Assistant.

The student is asking a follow-up question about the latest Python or SQL code they ran.
Stay anchored to the latest code, latest run output/error, and previous context provided.
Do not lose the topic. If the student asks "why", "how", "what does this mean", or "fix this", answer using the latest code context.

Rules:
- Answer normally, not in the structured review format.
- Keep the answer short, clear, and beginner-friendly.
- You may show a small corrected fragment only when it is necessary to explain the fix.
- Do not invent database tables, columns, files, outputs, or previous messages that were not provided.
PROMPT;

    public function review(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mode' => ['required', 'in:review,chat'],
            'code' => ['required', 'string', 'max:10000'],
            'language' => ['nullable', 'string', 'max:20'],
            'question' => ['nullable', 'string', 'max:2000'],
            'run_output' => ['nullable', 'string', 'max:10000'],
            'previous_context' => ['nullable', 'string', 'max:8000'],
        ]);

        $mode = $validated['mode'];
        $code = trim($validated['code']);
        $language = trim($validated['language'] ?? 'python');
        $question = trim($validated['question'] ?? '');
        $runOutput = trim($validated['run_output'] ?? '');
        $previousContext = trim($validated['previous_context'] ?? '');

        if ($mode === 'chat' && $question === '') {
            return response()->json([
                'ok' => false,
                'message' => 'A follow-up question is required for chat mode.',
            ], 422);
        }

        [$prompt, $temperature, $numPredict] = $mode === 'chat'
            ? $this->buildChatPrompt($language, $code, $runOutput, $previousContext, $question)
            : $this->buildReviewPrompt($language, $code, $runOutput);

        try {
            $response = Http::timeout((int) config('code_execution.ollama.timeout_seconds', 90))->post(
                (string) config('code_execution.ollama.url', 'http://localhost:11434/api/generate'),
                [
                    'model' => (string) config('code_execution.ollama.model', 'deepseek-coder'),
                    'prompt' => $prompt,
                    'stream' => false,
                    'options' => [
                        'temperature' => $temperature,
                        'num_predict' => $numPredict,
                    ],
                ]
            );
        } catch (\Throwable $exception) {
            return response()->json([
                'ok' => false,
                'message' => 'Code feedback service is currently unavailable: ' . $exception->getMessage(),
            ], 502);
        }

        if ($response->failed()) {
            return response()->json([
                'ok' => false,
                'message' => 'Code feedback service could not be reached. Make sure Ollama is running and the configured model is available.',
            ], 502);
        }

        $body = $response->json();
        $message = trim((string) ($body['response'] ?? ''));

        return response()->json([
            'ok' => true,
            'message' => $message !== '' ? $message : 'Empty response from the code feedback service.',
        ]);
    }

    private function buildChatPrompt(string $language, string $code, string $runOutput, string $previousContext, string $question): array
    {
        $prompt = "{$this->chatPrompt}\n\n"
            . "Language: {$language}\n\n"
            . "Previous context:\n" . ($previousContext !== '' ? $previousContext : 'None') . "\n\n"
            . "Latest code being discussed:\n```{$language}\n{$code}\n```\n\n"
            . "Latest run output/error:\n```text\n" . ($runOutput !== '' ? $runOutput : 'None') . "\n```\n\n"
            . "Student question: {$question}";

        return [$prompt, 0.35, 768];
    }

    private function buildReviewPrompt(string $language, string $code, string $runOutput): array
    {
        $prompt = "{$this->reviewPrompt}\n\n"
            . "Language: {$language}\n\n"
            . "Code to review:\n```{$language}\n{$code}\n```\n\n"
            . "Latest run output/error:\n```text\n" . ($runOutput !== '' ? $runOutput : 'None') . "\n```";

        return [$prompt, 0.2, 512];
    }
}
