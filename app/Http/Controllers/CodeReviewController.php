<?php

namespace App\Http\Controllers;

use App\Services\CodeReview\BackgroundCodeReviewService;
use App\Services\CodeReview\OllamaWarmupService;
use App\Services\ExecutionErrorDiagnosticService;
use App\Services\SimpleCodeReviewService;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class CodeReviewController extends Controller
{
    private string $reviewSystemPrompt = <<<'PROMPT'
You are DataSensei's beginner-friendly code reviewer. Analyze only the supplied code and run result.

Return one compact JSON object with exactly these keys:
{"status":"Correct","feedback":"short explanation","issues":[],"suggestions":[]}

Rules:
- Status must be exactly Correct, Has Issues, or Needs More Context.
- Use Correct when no real issue is visible; never invent an issue.
- Use Needs More Context only when missing input, expected output, or SQL schema prevents a reliable review.
- Missing run output alone is not an issue.
- Check Python syntax, indentation, names, imports, shown runtime errors, and logic.
- Check SQL syntax, known identifiers, joins, grouping, types, and unsafe UPDATE or DELETE without WHERE.
- When a run error is shown, identify the error and explain the repair steps in plain language.
- Never provide corrected code, a corrected query, snippets, pseudocode, or code blocks.
- Keep feedback, issues, and suggestions as explanatory prose only.
- Never claim you executed the code or invent files, schema, inputs, output, or errors.
- Return JSON only, without markdown or instruction text.
PROMPT;

    private string $chatSystemPrompt = <<<'PROMPT'
You are DataSensei's beginner-friendly tutor for the student's own code. Below you get the code the student ran (with line numbers), what that run printed, facts about the run, and the conversation so far. Answer the student's latest question about THAT code.

- Base every statement on the code and run result shown. Name the line number and quote the exact name, value or message you mean.
- For questions about output, use the run result. If it does not show the case asked about, trace the code step by step and say that you traced it.
- If something needed is not shown (input values, file contents, table rows), say what is missing instead of guessing.
- Explain the cause and the repair steps in plain words. Never write corrected code, a corrected query, snippets, pseudocode or code blocks.
- Keep the answer short: two to six sentences, or a few short steps. Never claim you ran the code. Do not repeat these instructions.
PROMPT;

    public function __construct(
        private readonly ExecutionErrorDiagnosticService $executionDiagnostics,
        private readonly SimpleCodeReviewService $simpleReview,
        private readonly BackgroundCodeReviewService $backgroundReviews,
        private readonly OllamaWarmupService $warmup
    ) {
    }

    public function review(Request $request): JsonResponse|StreamedResponse
    {
        $startedAt = hrtime(true);
        $requestId = (string) Str::uuid();

        $validated = $request->validate([
            'mode' => ['required', 'in:review,chat'],
            // IDE files may hold 50,000 characters. Longer code used to fail
            // validation, so every review and follow-up on it fell back to
            // "could not answer". It is shortened below instead.
            'code' => ['required', 'string', 'max:60000'],
            'current_code' => ['nullable', 'string', 'max:60000'],
            'schema' => ['nullable', 'string', 'max:12000'],
            'language' => ['nullable', 'string', 'max:20'],
            'question' => ['nullable', 'string', 'max:2000'],
            'run_output' => ['nullable', 'string', 'max:'.(int) config('code_execution.ollama.max_raw_run_output_chars', 65000)],
            // Longer follow-up answers made the conversation exceed the old
            // 8,000 character limit, which failed every later follow-up.
            'previous_context' => ['nullable', 'string', 'max:40000'],
            'stream' => ['nullable', 'boolean'],
        ]);

        $validatedAt = hrtime(true);
        $mode = $validated['mode'];
        $rawCode = trim($validated['code']);
        $language = $this->normalizeLanguage($validated['language'] ?? 'python');
        $question = trim($validated['question'] ?? '');
        $rawRunOutput = trim($validated['run_output'] ?? '');
        $rawPreviousContext = trim($validated['previous_context'] ?? '');
        $rawCurrentCode = trim($validated['current_code'] ?? '');
        $schema = trim($validated['schema'] ?? '');
        $isChat = $mode === 'chat';
        $shouldStream = $isChat && (bool) ($validated['stream'] ?? false);

        if ($isChat && $question === '') {
            return response()->json([
                'ok' => false,
                'message' => 'A follow-up question is required for chat mode.',
            ], 422);
        }

        $contextStartedAt = hrtime(true);
        $code = $this->compactMiddle(
            $rawCode,
            (int) config('code_execution.ollama.max_code_chars', 6000),
            'middle of code omitted',
            0.4
        );
        $runOutput = $this->compactMiddle(
            $rawRunOutput,
            (int) config('code_execution.ollama.max_run_output_chars', 1800),
            'middle of run output omitted',
            0.2
        );
        $previousContext = $this->compactTail(
            $rawPreviousContext,
            (int) config('code_execution.ollama.max_history_chars', 1800),
            'older conversation omitted'
        );
        $contextPreparedAt = hrtime(true);

        $promptStartedAt = hrtime(true);
        $prompt = $isChat
            ? $this->buildChatPrompt($language, $rawCode, $rawRunOutput, $rawPreviousContext, $question, $rawCurrentCode, $schema)
            : $this->buildReviewPrompt($language, $code, $runOutput, $schema);
        $payload = $this->buildPayload($isChat, $shouldStream, $prompt);
        $promptBuiltAt = hrtime(true);

        $baseTiming = [
            'request_id' => $requestId,
            'user_id' => $request->user()?->getAuthIdentifier(),
            'mode' => $mode,
            'model' => $payload['model'],
            'streamed' => $shouldStream,
            'request_processing_ms' => $this->elapsedMilliseconds($startedAt, $validatedAt),
            'context_preparation_ms' => $this->elapsedMilliseconds($contextStartedAt, $contextPreparedAt),
            'prompt_construction_ms' => $this->elapsedMilliseconds($promptStartedAt, $promptBuiltAt),
            'code_chars_input' => Str::length($rawCode),
            'code_chars_sent' => Str::length($code),
            'run_output_chars_input' => Str::length($rawRunOutput),
            'run_output_chars_sent' => Str::length($runOutput),
            'history_chars_input' => Str::length($rawPreviousContext),
            'history_chars_sent' => Str::length($previousContext),
            'prompt_chars' => Str::length($prompt),
        ];

        $diagnostic = $this->executionDiagnostics->diagnose($language, $rawRunOutput);
        if (! $isChat && $diagnostic !== null) {
            $message = $this->executionDiagnostics->format($diagnostic);
            $this->recordTiming($baseTiming, $startedAt, 'execution_diagnostic', [
                'ollama_generation_ms' => 0.0,
                'response_processing_ms' => 0.0,
                'time_to_first_token_ms' => 0.0,
            ]);

            return response()->json([
                'ok' => true,
                'mode' => $mode,
                'message' => $message,
                'source' => 'execution_diagnostic',
                'fallback' => false,
            ]);
        }

        // A clean run of straight-line beginner code is judged here, in
        // milliseconds, instead of waiting on the model for every Run.
        if (! $isChat) {
            $simpleReview = $this->simpleReview->review($language, $rawCode, $rawRunOutput);

            if ($simpleReview !== null) {
                $this->recordTiming($baseTiming, $startedAt, 'simple_review', [
                    'ollama_generation_ms' => 0.0,
                    'response_processing_ms' => 0.0,
                    'time_to_first_token_ms' => 0.0,
                ]);

                return response()->json([
                    'ok' => true,
                    'mode' => $mode,
                    'message' => $simpleReview,
                    'source' => 'simple_review',
                    'fallback' => false,
                ]);
            }
        }

        if ($isChat && $this->questionRequestsCode($question)) {
            $message = 'Status: Guidance Only'."\n"
                .'Feedback: I can explain the issue and the steps needed to correct it, but I cannot provide code or a completed query.';
            $this->recordTiming($baseTiming, $startedAt, 'code_generation_rejected', [
                'ollama_generation_ms' => 0.0,
                'response_processing_ms' => 0.0,
                'time_to_first_token_ms' => 0.0,
            ]);

            return response()->json([
                'ok' => true,
                'mode' => $mode,
                'message' => $message,
                'source' => 'policy',
                'fallback' => false,
            ]);
        }

        // Built per failure, so the panel names the real cause (service down,
        // model missing, timeout) instead of always blaming the deadline.
        $fallbackFor = fn (string $outcome): string => $this->executionDiagnostics->fallback(
            $language,
            $rawRunOutput,
            $isChat,
            $outcome
        );

        $lockStartedAt = hrtime(true);
        $lockResult = $this->acquireRequestLocks($request);
        $baseTiming['slot_wait_ms'] = $this->elapsedMilliseconds($lockStartedAt);

        if ($lockResult['locks'] === null) {
            // Another review holds the model (often this student's previous
            // review that is still finishing in the background). The answer
            // used to be replaced at once by a canned "could not answer" note;
            // it now waits its turn in the background and the page polls.
            if ($lockResult['reason'] === 'capacity') {
                $backgroundId = $this->backgroundReviews->start(
                    $this->reviewerKey($request),
                    $mode,
                    $payload,
                    $language,
                    $rawRunOutput
                );

                if ($backgroundId !== null) {
                    return $this->pendingResponse($mode, $backgroundId, $baseTiming, $startedAt, hrtime(true));
                }
            }

            return $this->jsonFallback(
                $mode,
                $fallbackFor,
                $baseTiming,
                $startedAt,
                $lockResult['reason'] === 'duplicate' ? 'duplicate_fallback' : 'capacity_fallback'
            );
        }

        /** @var array<int, Lock> $locks */
        $locks = $lockResult['locks'];

        $continueInBackground = fn (string $outcome): ?string => $outcome === 'timeout'
            ? $this->backgroundReviews->start(
                $this->reviewerKey($request),
                $mode,
                $payload,
                $language,
                $rawRunOutput
            )
            : null;

        if ($shouldStream) {
            return $this->streamChatResponse($payload, $locks, $baseTiming, $startedAt, $fallbackFor, $continueInBackground);
        }

        $ollamaStartedAt = hrtime(true);

        try {
            $response = $this->sendOllamaRequest($payload, false, $isChat);
        } catch (Throwable $exception) {
            $this->releaseLocks($locks);
            $failure = $this->exceptionFailure($exception);
            $this->logServiceFailure($baseTiming, $failure['outcome'], $exception::class, $exception->getMessage());

            // The model is still working: keep reviewing in the background
            // instead of reporting "Not Reviewed".
            $backgroundId = $continueInBackground($failure['outcome']);
            if ($backgroundId !== null) {
                return $this->pendingResponse($mode, $backgroundId, $baseTiming, $startedAt, $ollamaStartedAt);
            }

            return $this->jsonFallback(
                $mode,
                $fallbackFor,
                $baseTiming,
                $startedAt,
                $failure['outcome'],
                ['ollama_generation_ms' => $this->elapsedMilliseconds($ollamaStartedAt)]
            );
        }

        $ollamaCompletedAt = hrtime(true);
        $this->releaseLocks($locks);
        $ollamaWallMs = $this->elapsedMilliseconds($ollamaStartedAt, $ollamaCompletedAt);

        $responseProcessingStartedAt = hrtime(true);
        $result = $this->interpretOllamaResponse($response, $isChat, $baseTiming);
        $processingTiming = [
            'ollama_generation_ms' => $ollamaWallMs,
            'response_processing_ms' => $this->elapsedMilliseconds($responseProcessingStartedAt),
        ];

        if ($result['outcome'] !== 'success') {
            $backgroundId = $continueInBackground($result['outcome']);
            if ($backgroundId !== null) {
                return $this->pendingResponse($mode, $backgroundId, $baseTiming, $startedAt, $ollamaStartedAt);
            }

            return $this->jsonFallback(
                $mode,
                $fallbackFor,
                $baseTiming,
                $startedAt,
                $result['outcome'],
                $processingTiming
            );
        }

        $this->recordTiming(
            $baseTiming,
            $startedAt,
            'success',
            array_merge([
                'ollama_generation_ms' => $ollamaWallMs,
                'response_processing_ms' => $processingTiming['response_processing_ms'],
                'time_to_first_token_ms' => null,
            ], $this->ollamaMetrics($result['body']))
        );

        return response()->json([
            'ok' => true,
            'mode' => $mode,
            'message' => $result['message'],
        ]);
    }

    /**
     * Called when the IDE or SQL Sandbox opens (and every few minutes while it
     * stays open) so the model is already in memory when the student runs code.
     */
    public function warm(): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'started' => $this->warmup->warmInBackground(),
        ]);
    }

    /**
     * Status of a review that continued in the background.
     */
    public function status(Request $request, string $review): JsonResponse
    {
        $status = $this->backgroundReviews->statusFor($review, $this->reviewerKey($request));

        if ($status === null) {
            return response()->json([
                'ok' => false,
                'status' => 'missing',
                'message' => 'This review is no longer available. Run the program again to request a new review.',
            ], 404);
        }

        return response()->json(array_merge(['ok' => true, 'review_id' => $review], $status));
    }

    /**
     * Finishes a background review with no web deadline. Called by the
     * code-review:process command.
     *
     * @param array<string, mixed> $job
     * @return array{outcome: string, message: string, fallback: bool}
     */
    public function completeBackgroundReview(array $job): array
    {
        $isChat = ($job['mode'] ?? 'review') === 'chat';
        $language = (string) ($job['language'] ?? 'python');
        $runOutput = (string) ($job['run_output'] ?? '');
        $payload = (array) ($job['payload'] ?? []);
        $payload['stream'] = false;
        $context = ['request_id' => (string) ($job['id'] ?? ''), 'mode' => $isChat ? 'chat' : 'review', 'background' => true];
        $fallback = fn (string $outcome): array => [
            'outcome' => $outcome,
            'message' => $this->executionDiagnostics->fallback($language, $runOutput, $isChat, $outcome),
            'fallback' => true,
        ];

        $timeout = max(0, (int) config('code_execution.ollama.background_timeout_seconds', 0));
        $slotLock = $this->waitForBackgroundSlot($timeout);

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->connectTimeout((int) config('code_execution.ollama.connect_timeout_seconds', 3))
                ->timeout($timeout)
                ->post((string) config('code_execution.ollama.url', 'http://127.0.0.1:11434/api/generate'), $payload);
        } catch (Throwable $exception) {
            $failure = $this->exceptionFailure($exception);
            $this->logServiceFailure($context, $failure['outcome'], $exception::class, $exception->getMessage());

            return $fallback($failure['outcome']);
        } finally {
            if ($slotLock instanceof Lock) {
                $this->releaseLocks([$slotLock]);
            }
        }

        $result = $this->interpretOllamaResponse($response, $isChat, $context);

        return $result['outcome'] === 'success'
            ? ['outcome' => 'success', 'message' => (string) $result['message'], 'fallback' => false]
            : $fallback($result['outcome']);
    }

    /**
     * Shared by the live request and the background command.
     *
     * @param array<string, mixed> $logContext
     * @return array{outcome: string, message: ?string, body: array<string, mixed>}
     */
    private function interpretOllamaResponse(Response $response, bool $isChat, array $logContext): array
    {
        $result = static fn (string $outcome, ?string $message = null, array $body = []): array => [
            'outcome' => $outcome,
            'message' => $message,
            'body' => $body,
        ];

        if ($response->failed()) {
            $failure = $this->responseFailure($response);
            $this->logServiceFailure($logContext, $failure['outcome'], 'http_'.$response->status(), $failure['detail']);

            return $result($failure['outcome']);
        }

        $body = json_decode($response->body(), true);
        if (! is_array($body)) {
            return $result('malformed_response');
        }

        $embeddedError = trim((string) ($body['error'] ?? ''));
        if ($embeddedError !== '') {
            $failure = $this->failureForDetail(500, $embeddedError);
            $this->logServiceFailure($logContext, $failure['outcome'], 'ollama_error', $embeddedError);

            return $result($failure['outcome']);
        }

        if (($body['done'] ?? null) !== true) {
            return $result('incomplete_response');
        }

        $rawMessage = trim((string) ($body['response'] ?? ''));
        if ($rawMessage === '') {
            return $result('empty_response');
        }

        if (Str::length($rawMessage) > (int) config('code_execution.ollama.max_response_chars', 6000)) {
            return $result('response_too_large');
        }

        if ($isChat) {
            $message = $this->cleanChatResponse($rawMessage, ($body['done_reason'] ?? '') === 'length');
        } else {
            $review = $this->decodeReview($rawMessage);
            if ($review === null) {
                return $result('invalid_review');
            }

            $message = $this->formatReview($review);
        }

        if ($message === '') {
            return $result('empty_processed_response');
        }

        return $result('success', $message, $body);
    }

    /** Background work waits its turn instead of overloading the local model. */
    private function waitForBackgroundSlot(int $timeoutSeconds): ?Lock
    {
        $slotCount = max(1, min(2, (int) config('code_execution.ollama.max_concurrent_requests', 1)));
        $ttl = $timeoutSeconds > 0 ? $timeoutSeconds + 30 : 1800;
        $waitUntil = time() + 120;

        try {
            do {
                for ($slot = 0; $slot < $slotCount; $slot++) {
                    $lock = Cache::lock('datasensei:code-review:slot:'.$slot, $ttl);
                    if ($lock->get()) {
                        return $lock;
                    }
                }

                usleep(500000);
            } while (time() < $waitUntil);
        } catch (Throwable) {
            // Continue without a slot rather than never finishing the review.
        }

        return null;
    }

    /** @param array<string, mixed> $baseTiming */
    private function pendingResponse(string $mode, string $reviewId, array $baseTiming, int $startedAt, int $ollamaStartedAt): JsonResponse
    {
        $this->recordTiming($baseTiming, $startedAt, 'background_continuation', [
            'ollama_generation_ms' => $this->elapsedMilliseconds($ollamaStartedAt),
            'response_processing_ms' => 0.0,
            'time_to_first_token_ms' => null,
        ]);

        return response()->json([
            'ok' => true,
            'mode' => $mode,
            'pending' => true,
            'review_id' => $reviewId,
            'status_url' => route('api.code-review.status', ['review' => $reviewId]),
            'message' => $this->pendingMessage($mode === 'chat'),
            'source' => 'background_review',
            'fallback' => false,
        ]);
    }

    private function pendingMessage(bool $isChat): string
    {
        return $isChat
            ? 'The AI reviewer needs more time for this answer. It is still working and the answer will appear here.'
            : 'The AI reviewer needs more time for this program. It is still reviewing and the result will appear here.';
    }

    private function reviewerKey(Request $request): string
    {
        $reviewer = $request->user()?->getAuthIdentifier();

        return $reviewer !== null
            ? 'user:'.$reviewer
            : 'ip:'.hash('sha256', (string) $request->ip());
    }

    private function buildPayload(bool $isChat, bool $stream, string $prompt): array
    {
        $payload = [
            'model' => (string) config('code_execution.ollama.model', 'qwen2.5-coder:1.5b-instruct'),
            'system' => $isChat ? $this->chatSystemPrompt : $this->reviewSystemPrompt,
            'prompt' => $prompt,
            'stream' => $stream,
            'keep_alive' => OllamaWarmupService::keepAlive(),
            'options' => [
                'temperature' => 0.1,
                'num_ctx' => (int) config('code_execution.ollama.num_ctx', 4096),
                'num_predict' => $isChat
                    ? (int) config('code_execution.ollama.chat_num_predict', 320)
                    : (int) config('code_execution.ollama.review_num_predict', 220),
                'repeat_penalty' => 1.05,
                'top_k' => 20,
                'top_p' => 0.9,
            ],
        ];

        if (! $isChat) {
            // JSON mode is supported by older Ollama releases and avoids a
            // schema-rejection retry, keeping every review to one model call.
            $payload['format'] = 'json';
        }

        return $payload;
    }

    private function sendOllamaRequest(array $payload, bool $stream, bool $isChat = true): Response
    {
        $request = Http::acceptJson()
            ->asJson()
            ->connectTimeout((int) config('code_execution.ollama.connect_timeout_seconds', 3))
            ->timeout($this->deadlineSeconds($isChat));

        if ($stream) {
            $request = $request->withOptions(['stream' => true]);
        }

        return $request->post(
            (string) config('code_execution.ollama.url', 'http://127.0.0.1:11434/api/generate'),
            $payload
        );
    }

    /**
     * Auto-review runs on every Run and must feel immediate, so it gets a
     * tighter budget than a follow-up question the student chose to ask.
     */
    private function deadlineSeconds(bool $isChat): int
    {
        $chat = max(5, (int) config('code_execution.ollama.timeout_seconds', 30));

        if ($isChat) {
            return $chat;
        }

        return max(5, min($chat, (int) config('code_execution.ollama.review_timeout_seconds', 20)));
    }

    /**
     * @return array{locks: ?array<int, Lock>, reason: ?string}
     */
    private function acquireRequestLocks(Request $request): array
    {
        $timeout = (int) config('code_execution.ollama.timeout_seconds', 30);
        $ttl = max(15, $timeout + 15);
        $reviewerKey = $this->reviewerKey($request);
        $userLock = null;

        try {
            $userLock = Cache::lock('datasensei:code-review:'.$reviewerKey, $ttl);
            if (! $userLock->get()) {
                return ['locks' => null, 'reason' => 'duplicate'];
            }

            $slotCount = max(1, min(2, (int) config('code_execution.ollama.max_concurrent_requests', 1)));
            $start = random_int(0, $slotCount - 1);

            for ($offset = 0; $offset < $slotCount; $offset++) {
                $slot = ($start + $offset) % $slotCount;
                $slotLock = Cache::lock('datasensei:code-review:slot:'.$slot, $ttl);

                if ($slotLock->get()) {
                    return ['locks' => [$userLock, $slotLock], 'reason' => null];
                }
            }

            $this->releaseLocks([$userLock]);

            return ['locks' => null, 'reason' => 'capacity'];
        } catch (Throwable $exception) {
            if ($userLock instanceof Lock) {
                $this->releaseLocks([$userLock]);
            }

            $this->logServiceFailure([], 'lock_failure', $exception::class, $exception->getMessage());

            return ['locks' => null, 'reason' => 'capacity'];
        }
    }

    /** @param array<int, Lock> $locks */
    private function releaseLocks(array $locks): void
    {
        foreach (array_reverse($locks) as $lock) {
            try {
                $lock->release();
            } catch (Throwable) {
                // Each lock has a short TTL and will recover automatically.
            }
        }
    }

    /** @param array<int, Lock> $locks */
    private function streamChatResponse(
        array $payload,
        array $locks,
        array $baseTiming,
        int $startedAt,
        callable $fallbackFor,
        ?callable $continueInBackground = null
    ): StreamedResponse
    {
        return response()->stream(function () use ($payload, $locks, $baseTiming, $startedAt, $fallbackFor, $continueInBackground): void {
            // A slow answer keeps going in a background process; the browser
            // switches to polling instead of showing "Not Reviewed".
            $emitPendingOrFallback = function (string $outcome) use ($fallbackFor, $continueInBackground): void {
                $backgroundId = $continueInBackground !== null ? $continueInBackground($outcome) : null;

                if ($backgroundId !== null) {
                    $this->emitStreamEvent('pending', [
                        'review_id' => $backgroundId,
                        'status_url' => route('api.code-review.status', ['review' => $backgroundId]),
                        'message' => $this->pendingMessage(true),
                    ]);

                    return;
                }

                $this->emitStreamEvent('done', ['message' => $fallbackFor($outcome), 'fallback' => true]);
            };

            $outcome = 'stream_failed';
            $ollamaStartedAt = null;
            $ollamaWallMs = 0.0;
            $responseProcessingMs = 0.0;
            $timeToFirstTokenMs = null;
            $ollamaMetrics = [];

            try {
                $this->emitStreamEvent('status', ['message' => 'AI is thinking...']);
                $ollamaStartedAt = hrtime(true);
                $response = $this->sendOllamaRequest($payload, true);

                if ($response->failed()) {
                    $ollamaWallMs = $this->elapsedMilliseconds($ollamaStartedAt);
                    $failure = $this->responseFailure($response);
                    $outcome = $failure['outcome'];
                    $this->logServiceFailure($baseTiming, $outcome, 'http_'.$response->status(), $failure['detail']);
                    $this->emitStreamEvent('done', ['message' => $fallbackFor($outcome), 'fallback' => true]);

                    return;
                }

                $body = $response->toPsrResponse()->getBody();
                $buffer = '';
                $rawMessage = '';
                $finalFrame = [];
                $sawDone = false;
                $malformed = false;
                $tooLarge = false;
                $clientAborted = false;
                $generationTimedOut = false;
                $streamError = '';
                $maxResponseChars = (int) config('code_execution.ollama.max_response_chars', 6000);
                $generationTimeoutMs = $this->deadlineSeconds(true) * 1000;

                $consumeFrame = function (string $line) use (
                    &$rawMessage,
                    &$finalFrame,
                    &$sawDone,
                    &$malformed,
                    &$tooLarge,
                    &$streamError,
                    &$timeToFirstTokenMs,
                    $ollamaStartedAt,
                    $maxResponseChars
                ): void {
                    $line = trim($line);
                    if ($line === '') {
                        return;
                    }

                    $frame = json_decode($line, true);
                    if (! is_array($frame)) {
                        $malformed = true;

                        return;
                    }

                    $frameError = trim((string) ($frame['error'] ?? ''));
                    if ($frameError !== '') {
                        $streamError = $frameError;

                        return;
                    }

                    $delta = (string) ($frame['response'] ?? '');
                    if ($delta !== '') {
                        if ($timeToFirstTokenMs === null) {
                            $timeToFirstTokenMs = $this->elapsedMilliseconds($ollamaStartedAt);
                        }

                        $rawMessage .= $delta;
                        if (Str::length($rawMessage) > $maxResponseChars) {
                            $tooLarge = true;

                            return;
                        }

                        // Keep streamed model text server-side until it has
                        // passed the no-code response sanitizer.
                    }

                    if (($frame['done'] ?? false) === true) {
                        $sawDone = true;
                        $finalFrame = $frame;
                    }
                };

                while (! $body->eof() && ! $malformed && ! $tooLarge && $streamError === '') {
                    if (connection_aborted()) {
                        $clientAborted = true;
                        break;
                    }

                    if ($this->elapsedMilliseconds($ollamaStartedAt) >= $generationTimeoutMs) {
                        $generationTimedOut = true;
                        break;
                    }

                    $chunk = $body->read(2048);
                    if ($chunk === '') {
                        continue;
                    }

                    $buffer .= str_replace("\r\n", "\n", $chunk);

                    while (($newline = strpos($buffer, "\n")) !== false) {
                        $line = substr($buffer, 0, $newline);
                        $buffer = substr($buffer, $newline + 1);
                        $consumeFrame($line);

                        if ($malformed || $tooLarge || $streamError !== '') {
                            break;
                        }
                    }
                }

                if (! $malformed && ! $tooLarge && $streamError === '' && trim($buffer) !== '') {
                    $consumeFrame($buffer);
                }

                $body->close();
                $ollamaWallMs = $this->elapsedMilliseconds($ollamaStartedAt);

                if ($clientAborted) {
                    $outcome = 'client_aborted';

                    return;
                }

                if ($generationTimedOut) {
                    $outcome = 'timeout';
                    $this->releaseLocks($locks);
                    $emitPendingOrFallback($outcome);

                    return;
                }

                if ($streamError !== '') {
                    $failure = $this->failureForDetail(500, $streamError);
                    $outcome = $failure['outcome'];
                    $this->logServiceFailure($baseTiming, $outcome, 'ollama_stream_error', $streamError);
                    $this->emitStreamEvent('done', ['message' => $fallbackFor($outcome), 'fallback' => true]);

                    return;
                }

                if ($tooLarge) {
                    $outcome = 'response_too_large';
                    $this->emitStreamEvent('done', ['message' => $fallbackFor($outcome), 'fallback' => true]);

                    return;
                }

                if ($malformed || ! $sawDone) {
                    $outcome = $malformed ? 'malformed_stream' : 'incomplete_stream';
                    $this->emitStreamEvent('done', ['message' => $fallbackFor($outcome), 'fallback' => true]);

                    return;
                }

                if (trim($rawMessage) === '') {
                    $outcome = 'empty_response';
                    $this->emitStreamEvent('done', ['message' => $fallbackFor($outcome), 'fallback' => true]);

                    return;
                }

                $responseProcessingStartedAt = hrtime(true);
                $message = $this->cleanChatResponse($rawMessage, (($finalFrame['done_reason'] ?? '') === 'length'));
                $responseProcessingMs = $this->elapsedMilliseconds($responseProcessingStartedAt);

                if ($message === '') {
                    $outcome = 'empty_processed_response';
                    $this->emitStreamEvent('done', ['message' => $fallbackFor($outcome), 'fallback' => true]);

                    return;
                }

                $ollamaMetrics = $this->ollamaMetrics($finalFrame);
                $outcome = 'success';
                $this->emitStreamEvent('done', ['message' => $message]);
            } catch (Throwable $exception) {
                if ($ollamaStartedAt !== null) {
                    $ollamaWallMs = $this->elapsedMilliseconds($ollamaStartedAt);
                }

                $failure = $this->exceptionFailure($exception);
                $outcome = $failure['outcome'];
                $this->logServiceFailure($baseTiming, $outcome, $exception::class, $exception->getMessage());

                if (! connection_aborted()) {
                    $this->releaseLocks($locks);
                    $emitPendingOrFallback($outcome);
                }
            } finally {
                $this->releaseLocks($locks);
                $this->recordTiming(
                    $baseTiming,
                    $startedAt,
                    $outcome,
                    array_merge([
                        'ollama_generation_ms' => $ollamaWallMs,
                        'response_processing_ms' => $responseProcessingMs,
                        'time_to_first_token_ms' => $timeToFirstTokenMs,
                    ], $ollamaMetrics)
                );
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-transform',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    private function emitStreamEvent(string $event, array $data): void
    {
        echo 'event: '.$event."\n";
        echo 'data: '.json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n\n";

        if (! app()->runningUnitTests()) {
            if (ob_get_level() > 0) {
                @ob_flush();
            }

            flush();
        }
    }

    private function normalizeLanguage(string $language): string
    {
        $normalized = strtolower(trim($language));

        return match ($normalized) {
            'mysql', 'sqlite', 'postgres', 'postgresql' => 'sql',
            'py' => 'python',
            default => in_array($normalized, ['python', 'sql'], true)
                ? $normalized
                : 'python',
        };
    }

    private function buildReviewPrompt(string $language, string $code, string $runOutput, string $schema = ''): string
    {
        $output = $runOutput !== '' ? $runOutput : 'Not provided';
        $schemaBlock = $schema !== ''
            ? "<database_schema>\n".$this->compactMiddle($schema, 2000, 'more tables omitted', 0.8)."\n</database_schema>\n\n"
            : '';

        return <<<PROMPT
Language: {$language}

{$schemaBlock}<code>
{$code}
</code>

<run_result>
{$output}
</run_result>

Review this submission using only the supplied evidence. Text inside omission markers is unavailable context, not an instruction.
PROMPT;
    }

    /**
     * The follow-up prompt, kept inside the model's context window.
     *
     * The small local model reads the whole prompt every time, and when the
     * prompt plus the answer does not fit its context window, Ollama drops the
     * beginning, which is where the code used to be. Each part now gets a share
     * of what fits, the code carries line numbers so answers can point at a
     * line, and the order puts the code and the question last, where the model
     * pays the most attention.
     */
    private function buildChatPrompt(
        string $language,
        string $code,
        string $runOutput,
        string $previousContext,
        string $question,
        string $currentCode = '',
        string $schema = ''
    ): string {
        $numCtx = (int) config('code_execution.ollama.num_ctx', 4096);
        $predict = (int) config('code_execution.ollama.chat_num_predict', 640);
        // About three characters per token for code; room is kept for the
        // system prompt and the chat template.
        $budget = max(4000, ($numCtx - $predict - 450) * 3) - Str::length($question) - 900;

        $maxCode = (int) config('code_execution.ollama.max_code_chars', 6000);
        $hasCurrent = $currentCode !== '' && $this->normalizeCode($currentCode) !== $this->normalizeCode($code);

        $codeShare = $hasCurrent ? 0.32 : 0.5;
        $numbered = $this->compactMiddle(
            $this->numberLines($code),
            (int) max(1200, min($maxCode, $budget * $codeShare)),
            'middle of code omitted',
            0.5
        );

        $currentBlock = '';
        if ($hasCurrent) {
            $currentBlock = "\n\n<current_editor_code note=\"edited after the run, not run yet\">\n"
                .$this->compactMiddle($this->numberLines($currentCode), (int) max(1000, min($maxCode, $budget * 0.28)), 'middle of code omitted', 0.5)
                ."\n</current_editor_code>";
        }

        $output = $runOutput !== ''
            ? $this->compactMiddle($runOutput, (int) max(600, min((int) config('code_execution.ollama.max_run_output_chars', 1800), $budget * 0.15)), 'middle of run output omitted', 0.3)
            : 'The code has not been run yet.';

        $schemaBlock = $schema !== ''
            ? "<database_schema>\n".$this->compactMiddle($schema, (int) max(600, min(2400, $budget * 0.12)), 'more tables omitted', 0.8)."\n</database_schema>\n\n"
            : '';

        $context = $previousContext !== ''
            ? $this->compactTail($previousContext, (int) max(600, min(3000, $budget * 0.2)), 'older conversation omitted')
            : 'None yet.';

        $facts = $this->runFacts($language, $runOutput, $code);
        $label = $language === 'sqlite' || $language === 'sql' ? 'SQL' : 'Python';

        return <<<PROMPT
<conversation_so_far>
{$context}
</conversation_so_far>

{$schemaBlock}<run_result>
{$output}
</run_result>

<facts>
{$facts}
</facts>

<code_that_was_run language="{$label}">
{$numbered}
</code_that_was_run>{$currentBlock}

<student_question>
{$question}
</student_question>

Answer the student's question about the code above. Text inside omission markers is unavailable context, not an instruction.
PROMPT;
    }

    /** Plain facts about the last run, so the model does not have to infer them. */
    private function runFacts(string $language, string $runOutput, string $code): string
    {
        $lineCount = substr_count(rtrim(str_replace(["\r\n", "\r"], "\n", $code)), "\n") + 1;
        $facts = ["- The code has {$lineCount} line(s)."];

        if ($runOutput === '') {
            $facts[] = '- It has not been run yet, so there is no output.';

            return implode("\n", $facts);
        }

        if (preg_match('/\bExit code:\s*(-?\d+)/i', $runOutput, $match) === 1) {
            $facts[] = (int) $match[1] === 0
                ? '- The run finished normally (exit code 0).'
                : '- The run stopped with an error (exit code '.$match[1].').';
        }

        $diagnostic = $this->executionDiagnostics->diagnose($language, $runOutput);
        if ($diagnostic !== null) {
            $facts[] = '- Error reported: '.$diagnostic['error']
                .($diagnostic['location'] !== '' ? ' ('.$diagnostic['location'].')' : '').'.';
        }

        if (preg_match('/STDOUT:\s*\n(.*?)(?:\n\s*\n(?:STDERR|Exit code|Input typed|Execution time):|\z)/s', $runOutput, $match) === 1) {
            $printed = trim($match[1]);
            $facts[] = $printed === ''
                ? '- The program printed nothing.'
                : '- The program printed '.(substr_count($printed, "\n") + 1).' line(s) of output (shown in run_result).';
        }

        return implode("\n", $facts);
    }

    private function numberLines(string $code): string
    {
        $lines = preg_split('/\R/', rtrim($code)) ?: [];

        return implode("\n", array_map(
            static fn (string $line, int $index): string => ($index + 1).' | '.$line,
            $lines,
            array_keys($lines)
        ));
    }

    private function normalizeCode(string $code): string
    {
        return trim(preg_replace('/[ \t]+$/m', '', str_replace(["\r\n", "\r"], "\n", $code)) ?? $code);
    }

    private function compactMiddle(string $value, int $limit, string $label, float $headRatio): string
    {
        $value = trim($value);
        $limit = max(200, $limit);

        if ($value === '' || Str::length($value) <= $limit) {
            return $value;
        }

        $marker = "\n[... {$label} ...]\n";
        $available = max(2, $limit - Str::length($marker));
        $headLength = max(1, (int) floor($available * $headRatio));
        $tailLength = max(1, $available - $headLength);

        return Str::substr($value, 0, $headLength)
            .$marker
            .Str::substr($value, -$tailLength);
    }

    private function compactTail(string $value, int $limit, string $label): string
    {
        $value = trim($value);
        $limit = max(200, $limit);

        if ($value === '' || Str::length($value) <= $limit) {
            return $value;
        }

        $marker = "[... {$label} ...]\n";
        $tailLength = max(1, $limit - Str::length($marker));

        return $marker.Str::substr($value, -$tailLength);
    }

    private function decodeReview(string $rawMessage): ?array
    {
        $cleaned = trim($rawMessage);
        $cleaned = preg_replace('/^```(?:json)?\s*/i', '', $cleaned) ?? $cleaned;
        $cleaned = preg_replace('/\s*```$/', '', $cleaned) ?? $cleaned;
        $decoded = json_decode($cleaned, true);

        // A small local model often wraps the object in a sentence. Recover the
        // outermost JSON object rather than discarding a usable review.
        if (! is_array($decoded)) {
            $start = strpos($cleaned, '{');
            $end = strrpos($cleaned, '}');

            if ($start !== false && $end !== false && $end > $start) {
                $decoded = json_decode(substr($cleaned, $start, $end - $start + 1), true);
            }
        }

        if (! is_array($decoded)) {
            return null;
        }

        $status = $this->normalizeStatus((string) ($decoded['status'] ?? ''));
        $feedback = $this->cleanText($decoded['feedback'] ?? '');
        $issues = $this->cleanList($decoded['issues'] ?? []);
        $suggestions = $this->cleanList($decoded['suggestions'] ?? []);

        if ($status === null) {
            if ($feedback === '' && $issues === []) {
                return null;
            }

            $status = $issues !== [] ? 'Has Issues' : 'Correct';
        }

        if ($status === 'Correct') {
            $issues = [];
            $feedback = $feedback !== ''
                ? $feedback
                : 'The submitted code has no clear syntax or logic issue.';
        }

        // An issue list that the sanitizer emptied still deserves the written
        // explanation; only a completely silent review is unusable.
        if ($status === 'Has Issues' && $issues === []) {
            if ($feedback === '') {
                return null;
            }

            $status = 'Needs More Context';
        }

        if ($status === 'Needs More Context' && $feedback === '') {
            $feedback = 'Additional input, expected output, or database schema is needed for a reliable review.';
        }

        return compact('status', 'feedback', 'issues', 'suggestions');
    }

    /**
     * Accept the wording variations small models produce ("correct.", "HAS
     * ISSUES", "incorrect") instead of failing the whole review on casing.
     */
    private function normalizeStatus(string $status): ?string
    {
        $normalized = strtolower(trim($status));
        $normalized = trim(preg_replace('/[^a-z ]+/', ' ', $normalized) ?? $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;

        if ($normalized === '') {
            return null;
        }

        return match (true) {
            in_array($normalized, ['correct', 'ok', 'okay', 'valid', 'no issues', 'pass', 'passed'], true) => 'Correct',
            in_array($normalized, ['has issues', 'issues', 'incorrect', 'error', 'errors', 'fail', 'failed', 'invalid'], true) => 'Has Issues',
            in_array($normalized, ['needs more context', 'more context', 'needs context', 'unclear', 'insufficient context'], true) => 'Needs More Context',
            default => null,
        };
    }

    private function cleanList(mixed $items): array
    {
        if (! is_array($items)) {
            return [];
        }

        $cleaned = [];

        foreach ($items as $item) {
            $text = $this->cleanText($item);

            if ($text === '' || in_array($text, $cleaned, true)) {
                continue;
            }

            $cleaned[] = $text;

            if (count($cleaned) === 3) {
                break;
            }
        }

        return $cleaned;
    }

    private function cleanText(mixed $value): string
    {
        $text = trim((string) $value);
        $text = preg_replace('/```.*?```/s', '', $text) ?? $text;
        $text = str_replace('`', '', $text);
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;
        $lower = strtolower($text);

        foreach ([
            'rules:',
            'system prompt',
            'review rules',
            'style rules',
            'python review rules',
            'sql review rules',
        ] as $blockedFragment) {
            if (str_contains($lower, $blockedFragment)) {
                return '';
            }
        }

        if ($this->looksLikeCodeLine($text)) {
            return '';
        }

        return $text;
    }

    private function formatReview(array $review): string
    {
        $lines = ['Status: '.$review['status']];

        if ($review['feedback'] !== '') {
            $lines[] = 'Feedback: '.$review['feedback'];
        }

        if ($review['issues'] !== []) {
            $lines[] = 'Issues:';

            foreach ($review['issues'] as $issue) {
                $lines[] = '- '.$issue;
            }
        }

        if ($review['suggestions'] !== []) {
            $lines[] = 'Suggestions:';

            foreach ($review['suggestions'] as $suggestion) {
                $lines[] = '- '.$suggestion;
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Keep the reviewer's explanation and drop any code it wrote.
     *
     * The earlier filter removed every fenced block and every line that
     * mentioned a call such as print() or len(), so prose like "The print()
     * function shows the text" disappeared, program output inside a block was
     * lost, and answers were cut down to "This will output:" or emptied
     * completely (shown as an incomplete answer). Now only statement-shaped
     * lines and blocks that contain code are removed; sentences and program
     * output are kept. An answer that reached the token limit is ended at its
     * last complete sentence instead of mid-word.
     */
    private function cleanChatResponse(string $rawMessage, bool $truncated = false): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", trim($rawMessage));

        // A fence the model never closed (cut off mid-block) runs to the end.
        $text = preg_replace_callback(
            '/```[^\n`]*\n?(.*?)(?:```|\z)/s',
            function (array $match): string {
                $block = trim($match[1], "\n");

                return $this->isCodeBlock($block) ? "\n" : "\n".$block."\n";
            },
            $text
        ) ?? '';
        $text = str_replace('`', '', $text);

        $cleaned = [];

        foreach (preg_split('/\n/', $text) ?: [] as $line) {
            $trimmed = trim($line);
            $lower = strtolower($trimmed);

            if (
                str_starts_with($lower, 'rules:')
                || str_starts_with($lower, 'system:')
                || str_starts_with($lower, 'instructions:')
                || $this->looksLikeCodeLine($line)
            ) {
                continue;
            }

            $cleaned[] = rtrim($line);
        }

        $message = trim(preg_replace("/\n{3,}/", "\n\n", implode("\n", $cleaned)) ?? '');

        return $truncated ? $this->endAtCompleteSentence($message) : $message;
    }

    /** A fenced block is code when any of its lines is a code statement. */
    private function isCodeBlock(string $block): bool
    {
        foreach (preg_split('/\n/', $block) ?: [] as $line) {
            if ($this->looksLikeCodeLine($line)) {
                return true;
            }
        }

        return false;
    }

    /**
     * True for a line that is a Python or SQL statement rather than prose.
     * Sentences that merely name a function ("Use strip() to remove the
     * spaces.") are prose and are kept.
     */
    private function looksLikeCodeLine(string $line): bool
    {
        $raw = rtrim($line);
        $line = trim($line);

        if ($line === '') {
            return false;
        }

        $wordCount = preg_match_all('/[A-Za-z]{2,}/', $line);
        $endsLikeSentence = preg_match('/[.!?]["\')]?$/', $line) === 1;

        if ($endsLikeSentence && $wordCount >= 4) {
            return false;
        }

        // Python statements (keywords are lower case; prose sentences start
        // with a capital letter).
        $python = [
            '/^(?:async\s+)?def\s+[A-Za-z_]\w*\s*\(/',
            '/^class\s+[A-Za-z_]\w*\s*[(:]/',
            '/^(?:for|while|if|elif|with|except)\b.*:$/',
            '/^(?:else|try|finally)\s*:$/',
            '/^import\s+[A-Za-z_][\w.]*(?:\s+as\s+\w+)?(?:\s*,\s*[A-Za-z_][\w.]*(?:\s+as\s+\w+)?)*$/',
            '/^from\s+[\w.]+\s+import\s+\S/',
            '/^(?:return|raise|yield|pass|break|continue)\b(?:\s+[^.]*)?$/',
            // A whole line that is one call: print(x), df.head(), main()
            '/^[A-Za-z_][\w.]*\(.*\)\s*;?$/',
            // Assignment: total = price * qty, items[0] += 1
            '/^[A-Za-z_][\w.]*(?:\[[^\]]*\])?\s*(?:[+\-*\/%&|^]|\/\/|\*\*)?=\s*[^=\s]/',
        ];

        foreach ($python as $pattern) {
            if (preg_match($pattern, $line) === 1) {
                return true;
            }
        }

        // SQL statements: upper-case keywords or a terminating semicolon.
        if (preg_match('/^(?:select|insert|update|delete|create|alter|drop|pragma|with)\b/i', $line) === 1) {
            $upperKeyword = preg_match('/^(?:SELECT|INSERT|UPDATE|DELETE|CREATE|ALTER|DROP|PRAGMA|WITH)\b/', $line) === 1;
            $structured = preg_match('/\b(?:FROM|INTO|SET|TABLE|VALUES|WHERE|JOIN|INDEX|VIEW)\b|\*/i', $line) === 1;

            if ($structured && ($upperKeyword || str_ends_with($line, ';'))) {
                return true;
            }
        }

        // Indented (markdown code) lines with code punctuation.
        return preg_match('/^(?: {4}|\t)\S/', $raw) === 1
            && ! $endsLikeSentence
            && preg_match('/[=():\[\]]/', $line) === 1;
    }

    /** End a length-limited answer at its last complete sentence. */
    private function endAtCompleteSentence(string $message): string
    {
        if ($message === '' || preg_match('/[.!?]["\')]?$/', $message) === 1) {
            return $message;
        }

        if (preg_match_all('/[.!?]["\')]?(?=\s)/', $message, $matches, PREG_OFFSET_CAPTURE) > 0) {
            $last = end($matches[0]);
            $cut = $last[1] + strlen($last[0]);

            if ($cut >= (int) (strlen($message) * 0.4)) {
                return rtrim(substr($message, 0, $cut));
            }
        }

        return rtrim($message, " ,;:-").'…';
    }

    /**
     * Only a direct request for finished code gets the policy note. It used to
     * catch ordinary questions about the student's own code, such as "Did I
     * write the query correctly?" or "Why does my loop make the program
     * slow?", and those were never answered. Anything else goes to the
     * reviewer, whose answer is still stripped of code.
     */
    private function questionRequestsCode(string $question): bool
    {
        $q = strtolower(trim($question));

        // Asking for a hint or an explanation is always answered.
        if (preg_match('/\b(?:hint|hints|clue|tip|tips|idea|explain|explanation|reason|why)\b/', $q) === 1) {
            return false;
        }

        $object = '(?:code|function|class|script|program|solution|snippet|query|sql|statement|version|answer)';
        $verb = '(?:write|generate|create|give|show|send|make|build|implement|produce|provide|rewrite|type)';

        $patterns = [
            // "Write me the code ...", "Give me the fixed query", "please generate a function ..."
            '/^(?:(?:please|pls|kindly|now|then|ok|okay)[,\s]+)*'.$verb.'\s+(?:me\s+|us\s+)?(?:\w+\s+){0,4}'.$object.'\b/',
            // "Can you write the corrected code?", "could you give me the query"
            '/\b(?:can|could|would|will)\s+you\s+(?:please\s+)?'.$verb.'\s+(?:me\s+|us\s+)?(?:\w+\s+){0,4}'.$object.'\b/',
            // "fix it for me", "do it for me"
            '/\b(?:fix|correct|solve|do|finish|complete)\s+(?:it|this|that|the\s+\w+|my\s+\w+)\s+for\s+me\b/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $q) === 1) {
                return true;
            }
        }

        return false;
    }

    /** @return array{message: string, status: int, outcome: string} */
    private function exceptionFailure(Throwable $exception): array
    {
        $detail = strtolower($exception->getMessage());

        if (
            str_contains($detail, 'curl error 28')
            || str_contains($detail, 'timed out')
            || str_contains($detail, 'timeout')
        ) {
            return [
                'message' => 'The code feedback request took too long and was stopped. Try a shorter question or run it again.',
                'status' => 504,
                'outcome' => 'timeout',
            ];
        }

        if (
            $exception instanceof ConnectionException
            || str_contains($detail, 'curl error 7')
            || str_contains($detail, 'connection refused')
            || str_contains($detail, 'failed to connect')
        ) {
            return [
                'message' => 'The local AI service is unavailable. Make sure Ollama is running, then try again.',
                'status' => 503,
                'outcome' => 'connection_unavailable',
            ];
        }

        return [
            'message' => 'The code feedback service is currently unavailable. Please try again later.',
            'status' => 502,
            'outcome' => 'request_exception',
        ];
    }

    /** @return array{message: string, status: int, outcome: string, detail: string} */
    private function responseFailure(Response $response): array
    {
        $body = json_decode($response->body(), true);
        $detail = is_array($body)
            ? trim((string) ($body['error'] ?? ''))
            : '';

        if ($detail === '') {
            $detail = trim(Str::limit($response->body(), 500, ''));
        }

        return array_merge(
            $this->failureForDetail($response->status(), $detail),
            ['detail' => $detail]
        );
    }

    /** @return array{message: string, status: int, outcome: string} */
    private function failureForDetail(int $httpStatus, string $detail): array
    {
        $normalized = strtolower($detail);

        if (
            $httpStatus === 404
            || (str_contains($normalized, 'model') && (
                str_contains($normalized, 'not found')
                || str_contains($normalized, 'does not exist')
                || str_contains($normalized, 'pull model')
            ))
        ) {
            return [
                'message' => 'The configured AI model is unavailable. Ask an administrator to verify OLLAMA_MODEL and the installed Ollama models.',
                'status' => 503,
                'outcome' => 'model_unavailable',
            ];
        }

        if (
            str_contains($normalized, 'out of memory')
            || str_contains($normalized, 'not enough memory')
            || str_contains($normalized, 'failed to load')
            || str_contains($normalized, 'error loading model')
        ) {
            return [
                'message' => 'The AI model could not be loaded on this device. Free memory or select a smaller installed model.',
                'status' => 503,
                'outcome' => 'model_load_failure',
            ];
        }

        if (
            in_array($httpStatus, [408, 504], true)
            || str_contains($normalized, 'timed out')
            || str_contains($normalized, 'timeout')
        ) {
            return [
                'message' => 'The code feedback request took too long and was stopped. Try a shorter question or run it again.',
                'status' => 504,
                'outcome' => 'timeout',
            ];
        }

        if ($httpStatus >= 500) {
            return [
                'message' => 'Ollama returned a server error while reviewing the code. Please try again.',
                'status' => 502,
                'outcome' => 'ollama_server_error',
            ];
        }

        if ($httpStatus === 400 || $httpStatus === 422) {
            return [
                'message' => 'The configured AI model rejected this request. Ask an administrator to verify the Ollama model settings.',
                'status' => 502,
                'outcome' => 'ollama_request_rejected',
            ];
        }

        return [
            'message' => 'The code feedback service could not complete this request. Please try again later.',
            'status' => 502,
            'outcome' => 'ollama_http_error',
        ];
    }

    private function ollamaMetrics(array $body): array
    {
        $metrics = [];

        foreach ([
            'total_duration' => 'ollama_total_ms',
            'load_duration' => 'ollama_load_ms',
            'prompt_eval_duration' => 'ollama_prompt_eval_ms',
            'eval_duration' => 'ollama_eval_ms',
        ] as $source => $target) {
            if (is_numeric($body[$source] ?? null)) {
                $metrics[$target] = round(((float) $body[$source]) / 1_000_000, 2);
            }
        }

        foreach ([
            'prompt_eval_count' => 'prompt_tokens',
            'eval_count' => 'output_tokens',
        ] as $source => $target) {
            if (is_numeric($body[$source] ?? null)) {
                $metrics[$target] = (int) $body[$source];
            }
        }

        return $metrics;
    }

    private function jsonFallback(
        string $mode,
        callable $fallbackFor,
        array $baseTiming,
        int $startedAt,
        string $outcome,
        array $extraTiming = []
    ): JsonResponse {
        $this->recordTiming($baseTiming, $startedAt, $outcome, $extraTiming);

        return response()->json([
            'ok' => true,
            'mode' => $mode,
            'message' => $fallbackFor($outcome),
            'outcome' => $outcome,
            'source' => 'deterministic_fallback',
            'fallback' => true,
        ]);
    }

    private function recordTiming(
        array $baseTiming,
        int $startedAt,
        string $outcome,
        array $extraTiming = []
    ): void {
        $totalMs = $this->elapsedMilliseconds($startedAt);
        $context = array_merge($baseTiming, $extraTiming, [
            'outcome' => $outcome,
            'total_ms' => $totalMs,
        ]);
        $context['backend_processing_ms'] = round(max(
            0,
            $totalMs - (float) ($context['ollama_generation_ms'] ?? 0)
        ), 2);

        try {
            $logger = Log::channel('ollama_performance');
            $slowThreshold = (int) config('code_execution.ollama.slow_request_ms', 10000);

            if ($outcome !== 'success' || $totalMs >= $slowThreshold) {
                $logger->warning('IDE AI request timing.', $context);
            } else {
                $logger->info('IDE AI request timing.', $context);
            }
        } catch (Throwable) {
            // Timing telemetry must never interrupt the IDE response.
        }
    }

    private function logServiceFailure(array $context, string $outcome, string $type, string $detail): void
    {
        try {
            Log::warning('IDE AI request failed.', array_merge($context, [
                'outcome' => $outcome,
                'failure_type' => $type,
                'detail' => Str::limit($detail, 500, ''),
            ]));
        } catch (Throwable) {
            // Error logging must never replace the clean user-facing response.
        }
    }

    private function elapsedMilliseconds(int $startedAt, ?int $endedAt = null): float
    {
        return round((($endedAt ?? hrtime(true)) - $startedAt) / 1_000_000, 2);
    }
}
