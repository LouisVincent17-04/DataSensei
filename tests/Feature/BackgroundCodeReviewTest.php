<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\CodeReview\BackgroundCodeReviewService;
use App\Support\BackgroundArtisanLauncher;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BackgroundCodeReviewTest extends TestCase
{
    private User $learner;

    /** @var array<int, array<int, string>> */
    private array $launches = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
        Cache::flush();
        config([
            'cache.default' => 'array',
            'code_execution.ollama.timeout_seconds' => 10,
            'code_execution.ollama.review_timeout_seconds' => 8,
            'code_execution.ollama.max_concurrent_requests' => 1,
            'code_execution.ollama.background_continuation' => true,
            'code_execution.ollama.background_timeout_seconds' => 0,
            'code_execution.review.fast_path' => false,
        ]);

        $launches = &$this->launches;
        $this->app->instance(BackgroundArtisanLauncher::class, new class($launches) extends BackgroundArtisanLauncher {
            public function __construct(private array &$launches)
            {
            }

            public function launch(array $arguments): bool
            {
                $this->launches[] = $arguments;

                return true;
            }
        });

        $this->learner = $this->makeUser(808);
    }

    public function test_a_review_that_hits_the_time_limit_keeps_going_in_the_background(): void
    {
        Http::fakeSequence()
            ->pushFailedConnection('cURL error 28: Operation timed out after 8000 milliseconds')
            ->push($this->completedBody(json_encode([
                'status' => 'Has Issues',
                'feedback' => 'The loop never prints the last score.',
                'issues' => ['The range stops one item early.'],
                'suggestions' => [],
            ], JSON_THROW_ON_ERROR)));

        $response = $this->actingAs($this->learner)->postJson(route('api.code-review'), [
            'mode' => 'review', 'language' => 'python',
            'code' => "for i in range(len(xs) - 1):\n    print(xs[i])", 'run_output' => "STDOUT:\n1\nExit code: 0",
        ]);

        $response->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('pending', true)
            ->assertJsonPath('source', 'background_review');
        $reviewId = (string) $response->json('review_id');
        $this->assertSame([['code-review:process', $reviewId]], $this->launches);

        $this->getJson(route('api.code-review.status', ['review' => $reviewId]))
            ->assertOk()
            ->assertJsonPath('status', 'pending');

        $this->artisan('code-review:process', ['review' => $reviewId])->assertExitCode(0);

        $done = $this->getJson(route('api.code-review.status', ['review' => $reviewId]));
        $done->assertOk()->assertJsonPath('status', 'done')->assertJsonPath('fallback', false);
        $this->assertStringContainsString('Status: Has Issues', (string) $done->json('message'));
        $this->assertStringContainsString('range stops one item early', (string) $done->json('message'));

        // The background call has no time limit and never streams.
        Http::assertSent(fn (ClientRequest $request): bool => $request->data()['stream'] === false);
        Http::assertSentCount(2);
    }

    public function test_another_student_cannot_read_a_background_review(): void
    {
        Http::fakeSequence()->pushFailedConnection('cURL error 28: Operation timed out');

        $reviewId = (string) $this->actingAs($this->learner)->postJson(route('api.code-review'), [
            'mode' => 'review', 'language' => 'python', 'code' => 'value = 4', 'run_output' => 'Exit code: 0',
        ])->json('review_id');

        $this->assertNotSame('', $reviewId);
        $this->actingAs($this->makeUser(909))
            ->getJson(route('api.code-review.status', ['review' => $reviewId]))
            ->assertNotFound()
            ->assertJsonPath('status', 'missing');
    }

    public function test_a_newer_run_replaces_a_waiting_review_without_calling_the_model_again(): void
    {
        $calls = 0;
        Http::fake(function () use (&$calls): never {
            $calls++;
            throw new ConnectionException('cURL error 28: Operation timed out');
        });

        $first = (string) $this->actingAs($this->learner)->postJson(route('api.code-review'), [
            'mode' => 'review', 'language' => 'python', 'code' => 'value = 4', 'run_output' => 'Exit code: 0',
        ])->json('review_id');
        $second = (string) $this->actingAs($this->learner)->postJson(route('api.code-review'), [
            'mode' => 'review', 'language' => 'python', 'code' => 'value = 5', 'run_output' => 'Exit code: 0',
        ])->json('review_id');
        $this->assertNotSame($first, $second);
        $this->assertSame(2, $calls);

        $this->artisan('code-review:process', ['review' => $first])->assertExitCode(0);

        $this->assertSame(2, $calls, 'A replaced review must not call the model.');
        $status = app(BackgroundCodeReviewService::class)->statusFor($first, 'user:808');
        $this->assertSame('done', $status['status']);
        $this->assertStringContainsString('newer run', (string) $status['message']);
    }

    public function test_a_slow_follow_up_answer_switches_the_stream_to_polling(): void
    {
        Http::fake(function (): never { throw new ConnectionException('cURL error 28: Operation timed out'); });

        $response = $this->actingAs($this->learner)
            ->withHeader('Accept', 'application/json, text/event-stream')
            ->post(route('api.code-review'), [
                'mode' => 'chat', 'language' => 'python', 'code' => 'value = 4', 'run_output' => 'Exit code: 0',
                'question' => 'Why is my loop slow?', 'stream' => true,
            ]);

        $response->assertOk();
        $content = $response->streamedContent();
        $this->assertStringContainsString('event: pending', $content);
        $this->assertStringNotContainsString('event: done', $content);
        $this->assertCount(1, $this->launches);
        $this->assertSame('code-review:process', $this->launches[0][0]);
    }

    public function test_connection_failures_are_still_reported_immediately(): void
    {
        Http::fake(function (): never { throw new ConnectionException('cURL error 7: Failed to connect to 127.0.0.1 port 11434: Connection refused'); });

        $this->actingAs($this->learner)->postJson(route('api.code-review'), [
            'mode' => 'review', 'language' => 'python', 'code' => 'value = 4', 'run_output' => 'Exit code: 0',
        ])->assertOk()->assertJsonPath('fallback', true)->assertJsonMissingPath('pending');

        $this->assertSame([], $this->launches);
    }

    public function test_a_review_whose_process_never_starts_is_closed_after_the_retries(): void
    {
        $service = app(BackgroundCodeReviewService::class);
        $id = $service->start('user:808', 'review', ['model' => 'x', 'prompt' => 'y'], 'python', 'Exit code: 0');
        $this->assertNotNull($id);

        $this->travel(16)->seconds();
        $this->assertSame('pending', $service->statusFor($id, 'user:808')['status']);
        $this->travel(16)->seconds();
        $this->assertSame('pending', $service->statusFor($id, 'user:808')['status']);
        $this->assertCount(3, $this->launches);

        $this->travel(21)->seconds();
        $status = $service->statusFor($id, 'user:808');
        $this->assertSame('done', $status['status']);
        $this->assertTrue($status['fallback']);
    }

    public function test_the_ide_polls_for_background_reviews(): void
    {
        $view = (string) file_get_contents(resource_path('views/ide/index.blade.php'));

        $this->assertStringContainsString('_awaitBackground', $view);
        $this->assertStringContainsString("event === 'pending'", $view);
        $this->assertStringContainsString('the review keeps going', $view);
    }

    public function test_saving_in_the_ide_never_pushes_the_editor_down(): void
    {
        $view = (string) file_get_contents(resource_path('views/ide/index.blade.php'));

        // "Saved" used to be a page-flow notification inserted under the top bar.
        $this->assertDoesNotMatchRegularExpression('/id="save-indicator"[^>]*data-ds-global-notification/', $view);
        $this->assertStringContainsString('.app > #ds-global-notification-stack', $view);
        $this->assertStringContainsString('position: fixed !important;', $view);
        $this->assertStringContainsString('id="btn-save-label"', $view);
    }

    public function test_the_launcher_starts_a_detached_artisan_process(): void
    {
        $launcher = new BackgroundArtisanLauncher();
        $this->assertNotNull($launcher->phpBinary());
        $this->assertTrue($launcher->launch(['list', '--raw']));

        config(['background.enabled' => false]);
        $this->assertFalse($launcher->launch(['list']));
    }

    private function makeUser(int $id): User
    {
        $user = new User();
        $user->forceFill(['id' => $id, 'name' => 'Learner '.$id, 'email' => "learner{$id}@example.test", 'role' => User::ROLE_USER, 'status' => 'active']);
        $user->exists = true;

        return $user;
    }

    private function completedBody(string $response): array
    {
        return ['model' => 'qwen2.5-coder:1.5b-instruct', 'response' => $response, 'done' => true,
            'total_duration' => 1_000_000_000, 'prompt_eval_count' => 80, 'eval_count' => 24];
    }
}
