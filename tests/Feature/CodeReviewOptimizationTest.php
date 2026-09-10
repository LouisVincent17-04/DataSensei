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
            'code_execution.ollama.timeout_seconds' => 10,
            'code_execution.ollama.connect_timeout_seconds' => 2,
            'code_execution.ollama.client_timeout_ms' => 14000,
            'code_execution.ollama.keep_alive' => '30m',
            'code_execution.ollama.max_concurrent_requests' => 1,
            'code_execution.ollama.num_ctx' => 4096,
            'code_execution.ollama.review_num_predict' => 220,
            'code_execution.ollama.chat_num_predict' => 320,
            'code_execution.ollama.max_code_chars' => 6000,
            'code_execution.ollama.max_raw_run_output_chars' => 65000,
            'code_execution.ollama.max_run_output_chars' => 1800,
            'code_execution.ollama.max_history_chars' => 1800,
            'code_execution.ollama.max_response_chars' => 6000,
            'code_execution.ollama.review_timeout_seconds' => 8,
            /* Exercised on its own below; the model-path tests opt out. */
            'code_execution.review.fast_path' => false,
            'code_execution.review.max_lines' => 40,
            'code_execution.review.max_chars' => 1500,
        ]);
        $this->learner = new User();
        $this->learner->forceFill(['id' => 707, 'name' => 'IDE Learner', 'email' => 'ide@example.test', 'role' => User::ROLE_USER, 'status' => 'active']);
        $this->learner->exists = true;
    }

    public function test_review_uses_one_bounded_ollama_call_with_fast_settings(): void
    {
        Http::fake(['*' => Http::response($this->completedBody(json_encode([
            'status' => 'Correct', 'feedback' => 'The output matches the operation.',
            'issues' => [], 'suggestions' => ['Use a descriptive variable name.'],
        ], JSON_THROW_ON_ERROR)), 200)]);

        $response = $this->postReview(['mode' => 'review', 'language' => 'python',
            'code' => "value = 2 + 2\nprint(value)", 'run_output' => "STDOUT:\n4\nExit code: 0"]);

        $response->assertOk()->assertJsonPath('ok', true);
        Http::assertSentCount(1);
        Http::assertSent(function (ClientRequest $request): bool {
            $payload = $request->data();
            return $payload['stream'] === false && $payload['format'] === 'json'
                && $payload['keep_alive'] === '30m' && $payload['options']['num_ctx'] === 4096
                && $payload['options']['num_predict'] === 220
                && str_contains($payload['system'], 'Never provide corrected code');
        });
    }

    public function test_simple_successful_python_is_reviewed_locally_without_calling_ollama(): void
    {
        config(['code_execution.review.fast_path' => true]);

        $response = $this->postReview([
            'mode' => 'review',
            'language' => 'python',
            'code' => "name = \"DataSensei Learner\"\nscore = 95\nprint(\"Hello,\", name)\nif score >= 75:\n    print(\"Passed\")\nelse:\n    print(\"Needs improvement\")",
            'run_output' => "STDOUT:\nHello, DataSensei Learner\nPassed\n\nExit code: 0\n\nExecution time: 41ms",
        ]);

        $response->assertOk()
            ->assertJsonPath('source', 'simple_review')
            ->assertJsonPath('fallback', false);

        $message = $response->json('message');
        $this->assertStringContainsString('Status: Correct', $message);
        $this->assertStringContainsString('printed 2 lines', $message);
        $this->assertStringContainsString('true and a false branch', $message);
        Http::assertNothingSent();
    }

    public function test_code_needing_real_analysis_still_reaches_ollama(): void
    {
        config(['code_execution.review.fast_path' => true]);
        Http::fake(['*' => Http::response($this->completedBody(json_encode([
            'status' => 'Correct', 'feedback' => 'The helper returns the expected sum.',
            'issues' => [], 'suggestions' => [],
        ], JSON_THROW_ON_ERROR)), 200)]);

        $response = $this->postReview([
            'mode' => 'review',
            'language' => 'python',
            'code' => "def add(a, b):\n    return a + b\nprint(add(2, 3))",
            'run_output' => "STDOUT:\n5\n\nExit code: 0",
        ]);

        $response->assertOk();
        $this->assertNotSame('simple_review', $response->json('source'));
        Http::assertSentCount(1);
    }

    public function test_review_deadline_is_shorter_than_the_chat_deadline(): void
    {
        $this->assertLessThanOrEqual(
            (int) config('code_execution.ollama.timeout_seconds'),
            (int) config('code_execution.ollama.review_timeout_seconds')
        );
    }

    public function test_python_execution_error_is_explained_without_calling_ollama(): void
    {
        $response = $this->postReview(['mode' => 'review', 'language' => 'python', 'code' => 'print(total)',
            'run_output' => "STDERR:\n  File \"main.py\", line 1\nNameError: name 'total' is not defined\nExit code: 1"]);

        $response->assertOk()->assertJsonPath('source', 'execution_diagnostic')->assertJsonPath('fallback', false);
        $message = $response->json('message');
        $this->assertStringContainsString('Error: NameError', $message);
        $this->assertStringContainsString('Location: Line 1', $message);
        $this->assertStringContainsString('Steps to Fix:', $message);
        $this->assertStringNotContainsString('print(', $message);
        Http::assertNothingSent();
    }

    public function test_sql_execution_error_is_explained_without_calling_ollama(): void
    {
        $response = $this->postReview(['mode' => 'review', 'language' => 'sqlite',
            'code' => 'SELECT missing FROM learners', 'run_output' => 'Query failed: no such column: missing']);

        $response->assertOk()->assertJsonPath('source', 'execution_diagnostic');
        $this->assertStringContainsString('Error: Column not found', $response->json('message'));
        $this->assertStringContainsString('Steps to Fix:', $response->json('message'));
        Http::assertNothingSent();
    }

    public function test_long_terminal_output_keeps_final_error_evidence(): void
    {
        $response = $this->postReview(['mode' => 'review', 'language' => 'python', 'code' => 'print(total)',
            'run_output' => str_repeat('diagnostic context ', 3000)."\nNameError: name 'total' is not defined\nExit code: 1"]);

        $response->assertOk()->assertJsonPath('source', 'execution_diagnostic');
        $this->assertStringContainsString('NameError', $response->json('message'));
        Http::assertNothingSent();
    }

    public function test_service_failure_returns_a_successful_deterministic_fallback(): void
    {
        Http::fake(function (): never { throw new ConnectionException('cURL error 28: Operation timed out'); });
        $response = $this->postReview(['mode' => 'review', 'language' => 'python', 'code' => 'value = 4', 'run_output' => 'Exit code: 0']);
        $response->assertOk()->assertJsonPath('ok', true)->assertJsonPath('fallback', true)->assertJsonPath('source', 'deterministic_fallback');
    }

    public function test_duplicate_request_returns_immediately_with_fallback(): void
    {
        $lock = Cache::lock('datasensei:code-review:user:707', 60);
        $this->assertTrue($lock->get());
        try {
            $response = $this->postReview(['mode' => 'review', 'language' => 'python', 'code' => 'value = 4']);
        } finally { $lock->release(); }
        $response->assertOk()->assertJsonPath('fallback', true);
        Http::assertNothingSent();
    }

    public function test_code_generation_follow_up_is_guidance_only_without_ollama(): void
    {
        $response = $this->postReview(['mode' => 'chat', 'language' => 'sqlite',
            'code' => 'SELECT name FROM learners', 'question' => 'Write the corrected SQL query for me.']);
        $response->assertOk()->assertJsonPath('source', 'policy');
        $this->assertStringContainsString('cannot provide code or a completed query', $response->json('message'));
        Http::assertNothingSent();
    }

    public function test_streamed_chat_buffers_and_sanitizes_before_display(): void
    {
        $stream = implode("\n", [
            json_encode(['response' => 'Check the reported name first. ', 'done' => false], JSON_THROW_ON_ERROR),
            json_encode(['response' => 'Then run the program again.', 'done' => false], JSON_THROW_ON_ERROR),
            json_encode(['response' => '', 'done' => true], JSON_THROW_ON_ERROR),
        ])."\n";
        Http::fake(['*' => Http::response($stream, 200, ['Content-Type' => 'application/x-ndjson'])]);
        $response = $this->actingAs($this->learner)->withHeader('Accept', 'application/json, text/event-stream')
            ->post(route('api.code-review'), ['mode' => 'chat', 'language' => 'python', 'code' => 'value = 4',
                'run_output' => 'Exit code: 0', 'question' => 'What should I inspect?', 'stream' => true]);
        $response->assertOk();
        $content = $response->streamedContent();
        $this->assertStringContainsString('event: status', $content);
        $this->assertStringNotContainsString('event: delta', $content);
        $this->assertStringContainsString('event: done', $content);
        $this->assertStringContainsString('Check the reported name first.', $content);
    }

    private function postReview(array $data)
    {
        return $this->actingAs($this->learner)->postJson(route('api.code-review'), $data);
    }

    private function completedBody(string $response): array
    {
        return ['model' => 'qwen2.5-coder:1.5b-instruct', 'response' => $response, 'done' => true,
            'total_duration' => 1_000_000_000, 'prompt_eval_count' => 80, 'eval_count' => 24];
    }
}
