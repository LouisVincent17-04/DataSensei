<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CodeReviewOptimizationTest extends TestCase
{
    private User $learner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware();
        Cache::flush();

        config([
            'cache.default' => 'array',
            'code_execution.ollama.url' => 'http://127.0.0.1:11434/api/generate',
            'code_execution.ollama.model' => 'qwen2.5-coder:1.5b-instruct',
            'code_execution.ollama.timeout_seconds' => 40,
            'code_execution.ollama.connect_timeout_seconds' => 2,
            'code_execution.ollama.keep_alive' => '30m',
            'code_execution.ollama.max_concurrent_requests' => 1,
            'code_execution.ollama.num_ctx' => 4096,
            'code_execution.ollama.review_num_predict' => 220,
            'code_execution.ollama.chat_num_predict' => 320,
            'code_execution.ollama.max_code_chars' => 6000,
            'code_execution.ollama.max_run_output_chars' => 1800,
            'code_execution.ollama.max_history_chars' => 1800,
            'code_execution.ollama.max_response_chars' => 6000,
        ]);

        $this->learner = new User();
        $this->learner->forceFill([
            'id' => 707,
            'name' => 'IDE Learner',
            'email' => 'ide-learner@example.test',
            'role' => User::ROLE_USER,
            'status' => 'active',
        ]);
        $this->learner->exists = true;
    }

    public function test_review_uses_one_bounded_ollama_call_with_fast_settings(): void
    {
        Http::fake([
            '*' => Http::response($this->completedOllamaBody(json_encode([
                'status' => 'Correct',
                'feedback' => 'The code prints the expected value.',
                'issues' => [],
                'suggestions' => ['Keep the variable name descriptive.'],
            ], JSON_THROW_ON_ERROR)), 200),
        ]);

        $response = $this->actingAs($this->learner)->postJson(route('api.code-review'), [
            'mode' => 'review',
            'language' => 'python',
            'code' => "value = 2 + 2\nprint(value)",
            'run_output' => "STDOUT:\n4\n\nExit code: 0",
        ]);

        $response->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('mode', 'review')
            ->assertJsonPath('message', "Status: Correct\nFeedback: The code prints the expected value.\nSuggestions:\n- Keep the variable name descriptive.");

        Http::assertSentCount(1);
        Http::assertSent(function (ClientRequest $request): bool {
            $payload = $request->data();

            return $request->url() === 'http://127.0.0.1:11434/api/generate'
                && $payload['model'] === 'qwen2.5-coder:1.5b-instruct'
                && $payload['stream'] === false
                && $payload['format'] === 'json'
                && $payload['keep_alive'] === '30m'
                && $payload['options']['num_ctx'] === 4096
                && $payload['options']['num_predict'] === 220;
        });
    }

    public function test_large_context_is_reduced_without_losing_the_latest_evidence(): void
    {
        Http::fake([
            '*' => Http::response($this->completedOllamaBody('The latest traceback points to FINAL_CALL().'), 200),
        ]);

        $code = "BEGIN_IMPORTS\n".str_repeat('x', 9900)."\nFINAL_CALL()";
        $runOutput = "FIRST_OUTPUT\n".str_repeat('y', 9700)."\nLATEST_ERROR";
        $history = "OLDEST_CONTEXT\n".str_repeat('z', 7700)."\nRECENT_CONTEXT";

        $response = $this->actingAs($this->learner)->postJson(route('api.code-review'), [
            'mode' => 'chat',
            'language' => 'python',
            'code' => $code,
            'run_output' => $runOutput,
            'previous_context' => $history,
            'question' => 'Where should I look first?',
            'stream' => false,
        ]);

        $response->assertOk()->assertJsonPath('ok', true);

        Http::assertSent(function (ClientRequest $request): bool {
            $payload = $request->data();
            $prompt = $payload['prompt'];

            return str_contains($prompt, 'BEGIN_IMPORTS')
                && str_contains($prompt, 'FINAL_CALL()')
                && str_contains($prompt, '[... middle of code omitted ...]')
                && str_contains($prompt, 'LATEST_ERROR')
                && str_contains($prompt, '[... older conversation omitted ...]')
                && str_contains($prompt, 'RECENT_CONTEXT')
                && mb_strlen($prompt) < 12000
                && $payload['options']['num_predict'] === 320;
        });
    }

    public function test_duplicate_in_flight_request_is_rejected_before_ollama_is_called(): void
    {
        $lock = Cache::lock('datasensei:code-review:user:707', 60);
        $this->assertTrue($lock->get());

        try {
            $response = $this->actingAs($this->learner)->postJson(route('api.code-review'), [
                'mode' => 'review',
                'language' => 'python',
                'code' => 'print("hello")',
            ]);
        } finally {
            $lock->release();
        }

        $response->assertStatus(429)
            ->assertJsonPath('ok', false)
            ->assertJsonPath('message', 'An AI review is already running for you. Please wait for it to finish.');
        Http::assertNothingSent();
    }

    public function test_timeout_returns_a_clean_gateway_timeout_message(): void
    {
        Http::fake(function (): never {
            throw new ConnectionException('cURL error 28: Operation timed out');
        });

        $response = $this->actingAs($this->learner)->postJson(route('api.code-review'), [
            'mode' => 'review',
            'language' => 'python',
            'code' => 'print("hello")',
        ]);

        $response->assertStatus(504)
            ->assertJsonPath('ok', false)
            ->assertJsonPath('message', 'The code feedback request took too long and was stopped. Try a shorter question or run it again.');
    }

    public function test_missing_model_returns_a_specific_safe_message_without_retrying(): void
    {
        Http::fake([
            '*' => Http::response(['error' => 'model qwen2.5-coder:1.5b-instruct not found'], 404),
        ]);

        $response = $this->actingAs($this->learner)->postJson(route('api.code-review'), [
            'mode' => 'review',
            'language' => 'python',
            'code' => 'print("hello")',
        ]);

        $response->assertStatus(503)
            ->assertJsonPath('ok', false)
            ->assertJsonPath('message', 'The configured AI model is unavailable. Ask an administrator to verify OLLAMA_MODEL and the installed Ollama models.');
        Http::assertSentCount(1);
    }

    public function test_offline_ollama_returns_a_clean_unavailable_message(): void
    {
        Http::fake(function (): never {
            throw new ConnectionException('cURL error 7: Failed to connect: Connection refused');
        });

        $response = $this->actingAs($this->learner)->postJson(route('api.code-review'), [
            'mode' => 'review',
            'language' => 'python',
            'code' => 'print("hello")',
        ]);

        $response->assertStatus(503)
            ->assertJsonPath('ok', false)
            ->assertJsonPath('message', 'The local AI service is unavailable. Make sure Ollama is running, then try again.');
    }

    public function test_model_loading_failure_returns_a_specific_safe_message(): void
    {
        Http::fake([
            '*' => Http::response(['error' => 'failed to load model: not enough memory'], 500),
        ]);

        $response = $this->actingAs($this->learner)->postJson(route('api.code-review'), [
            'mode' => 'review',
            'language' => 'python',
            'code' => 'print("hello")',
        ]);

        $response->assertStatus(503)
            ->assertJsonPath('ok', false)
            ->assertJsonPath('message', 'The AI model could not be loaded on this device. Free memory or select a smaller installed model.');
    }

    public function test_malformed_or_empty_ollama_output_is_not_reported_as_complete(): void
    {
        Http::fakeSequence()
            ->push('not-json', 200)
            ->push($this->completedOllamaBody(''), 200);

        $malformed = $this->actingAs($this->learner)->postJson(route('api.code-review'), [
            'mode' => 'review',
            'language' => 'python',
            'code' => 'print("hello")',
        ]);

        $empty = $this->actingAs($this->learner)->postJson(route('api.code-review'), [
            'mode' => 'review',
            'language' => 'python',
            'code' => 'print("hello")',
        ]);

        $malformed->assertStatus(502)
            ->assertJsonPath('message', 'The code feedback service returned a malformed response. Please try again.');
        $empty->assertStatus(502)
            ->assertJsonPath('message', 'The code feedback model returned an empty response. Please try again.');
        Http::assertSentCount(2);
    }

    public function test_chat_follow_up_streams_deltas_and_a_complete_final_message(): void
    {
        $streamBody = implode("\n", [
            json_encode(['response' => 'Use ', 'done' => false], JSON_THROW_ON_ERROR),
            json_encode(['response' => 'print()', 'done' => false], JSON_THROW_ON_ERROR),
            json_encode([
                'response' => '',
                'done' => true,
                'total_duration' => 2_000_000_000,
                'load_duration' => 50_000_000,
                'prompt_eval_duration' => 400_000_000,
                'eval_duration' => 1_500_000_000,
                'prompt_eval_count' => 80,
                'eval_count' => 12,
            ], JSON_THROW_ON_ERROR),
        ])."\n";

        Http::fake([
            '*' => Http::response($streamBody, 200, ['Content-Type' => 'application/x-ndjson']),
        ]);

        $response = $this->actingAs($this->learner)
            ->withHeader('Accept', 'application/json, text/event-stream')
            ->post(route('api.code-review'), [
                'mode' => 'chat',
                'language' => 'python',
                'code' => 'value = 4',
                'run_output' => 'Exit code: 0',
                'question' => 'How do I display value?',
                'stream' => true,
            ]);

        $response->assertOk();
        $content = $response->streamedContent();

        $this->assertStringContainsString("event: status\n", $content);
        $this->assertStringContainsString('event: delta', $content);
        $this->assertStringContainsString('data: {"text":"Use "}', $content);
        $this->assertStringContainsString('data: {"text":"print()"}', $content);
        $this->assertStringContainsString('event: done', $content);
        $this->assertStringContainsString('data: {"message":"Use print()"}', $content);

        Http::assertSentCount(1);
        Http::assertSent(fn (ClientRequest $request): bool => $request->data()['stream'] === true);
    }

    private function completedOllamaBody(string $response): array
    {
        return [
            'model' => 'qwen2.5-coder:1.5b-instruct',
            'response' => $response,
            'done' => true,
            'total_duration' => 5_000_000_000,
            'load_duration' => 100_000_000,
            'prompt_eval_duration' => 900_000_000,
            'eval_duration' => 3_800_000_000,
            'prompt_eval_count' => 160,
            'eval_count' => 48,
        ];
    }
}
