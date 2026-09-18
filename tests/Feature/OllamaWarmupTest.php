<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\CodeReview\OllamaWarmupService;
use App\Support\BackgroundArtisanLauncher;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OllamaWarmupTest extends TestCase
{
    /** @var array<int, array<int, string>> */
    private array $launches = [];

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        config([
            'cache.default' => 'array',
            'code_execution.ollama.url' => 'http://127.0.0.1:11434/api/generate',
            'code_execution.ollama.model' => 'qwen2.5-coder:1.5b-instruct',
            'code_execution.ollama.num_ctx' => 4096,
            'code_execution.ollama.keep_alive' => '-1',
            'code_execution.ollama.warmup' => true,
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
    }

    public function test_keep_alive_values_are_sent_in_a_form_ollama_accepts(): void
    {
        config(['code_execution.ollama.keep_alive' => '-1']);
        $this->assertSame(-1, OllamaWarmupService::keepAlive());

        config(['code_execution.ollama.keep_alive' => '300']);
        $this->assertSame(300, OllamaWarmupService::keepAlive());

        config(['code_execution.ollama.keep_alive' => '30m']);
        $this->assertSame('30m', OllamaWarmupService::keepAlive());

        config(['code_execution.ollama.keep_alive' => '']);
        $this->assertSame(-1, OllamaWarmupService::keepAlive());
    }

    public function test_an_unloaded_model_is_loaded_with_the_same_context_size_as_reviews(): void
    {
        Http::fake([
            '*/api/ps' => Http::response(['models' => []]),
            '*/api/generate' => Http::response(['model' => 'qwen2.5-coder:1.5b-instruct', 'response' => '', 'done' => true, 'done_reason' => 'load']),
        ]);

        $result = app(OllamaWarmupService::class)->warm();

        $this->assertSame('loaded', $result['status']);
        Http::assertSent(function (ClientRequest $request): bool {
            if (! str_ends_with($request->url(), '/api/generate')) {
                return false;
            }
            $payload = $request->data();

            return $payload['prompt'] === ''
                && $payload['keep_alive'] === -1
                && $payload['options'] === ['num_ctx' => 4096]
                && $payload['model'] === 'qwen2.5-coder:1.5b-instruct';
        });
    }

    public function test_a_loaded_model_only_has_its_timer_refreshed(): void
    {
        Http::fake([
            '*/api/ps' => Http::response(['models' => [['name' => 'qwen2.5-coder:1.5b-instruct', 'model' => 'qwen2.5-coder:1.5b-instruct']]]),
            '*/api/generate' => Http::response(['done' => true, 'done_reason' => 'load']),
        ]);

        $this->assertSame('ready', app(OllamaWarmupService::class)->warm()['status']);
        $this->assertTrue(app(OllamaWarmupService::class)->isLoaded());
    }

    public function test_a_stopped_ollama_is_reported_without_trying_to_load(): void
    {
        Http::fake(function (): never { throw new ConnectionException('cURL error 7: Connection refused'); });

        $this->assertSame('unavailable', app(OllamaWarmupService::class)->warm()['status']);
    }

    public function test_a_missing_model_is_reported_with_the_pull_command(): void
    {
        Http::fake([
            '*/api/ps' => Http::response(['models' => []]),
            '*/api/generate' => Http::response(['error' => "model 'qwen2.5-coder:1.5b-instruct' not found, try pulling it first"], 404),
        ]);

        $result = app(OllamaWarmupService::class)->warm();

        $this->assertSame('missing_model', $result['status']);
        $this->assertStringContainsString('ollama pull qwen2.5-coder:1.5b-instruct', $result['message']);
    }

    public function test_the_warm_endpoint_starts_one_background_warm_up_per_minute(): void
    {
        $this->withoutMiddleware();
        $user = new User();
        $user->forceFill(['id' => 515, 'name' => 'Warm Student', 'email' => 'warm@example.test', 'role' => User::ROLE_USER, 'status' => 'active']);
        $user->exists = true;

        $this->actingAs($user)->postJson(route('api.code-review.warm'))->assertOk()->assertJsonPath('started', true);
        $this->actingAs($user)->postJson(route('api.code-review.warm'))->assertOk();

        $this->assertSame([['code-review:warm']], $this->launches);
    }

    public function test_review_requests_use_the_same_keep_alive_value(): void
    {
        $this->withoutMiddleware();
        Http::fake(['*' => Http::response([
            'model' => 'qwen2.5-coder:1.5b-instruct',
            'response' => json_encode(['status' => 'Correct', 'feedback' => 'Fine.', 'issues' => [], 'suggestions' => []]),
            'done' => true,
        ])]);
        $user = new User();
        $user->forceFill(['id' => 516, 'name' => 'Review Student', 'email' => 'review@example.test', 'role' => User::ROLE_USER, 'status' => 'active']);
        $user->exists = true;

        $this->actingAs($user)->postJson(route('api.code-review'), [
            'mode' => 'review', 'language' => 'python',
            'code' => "for i in range(3):\n    if i:\n        print(i)", 'run_output' => "STDOUT:\n1\n2\nExit code: 0",
        ])->assertOk();

        Http::assertSent(fn (ClientRequest $request): bool => ($request->data()['keep_alive'] ?? null) === -1);
    }

    public function test_warm_up_requests_do_not_keep_an_idle_session_signed_in(): void
    {
        $middleware = (string) file_get_contents(app_path('Http/Middleware/EnforceIdleSessionTimeout.php'));
        $this->assertStringContainsString("'api.code-review.warm'", $middleware);

        foreach (['ide/index', 'ide/sql_sandbox'] as $view) {
            $source = (string) file_get_contents(resource_path("views/{$view}.blade.php"));
            $this->assertStringContainsString("route('api.code-review.warm')", $source);
            $this->assertStringContainsString("'X-DataSensei-Background': '1'", $source);
        }

        $sql = (string) file_get_contents(resource_path('views/ide/sql_sandbox.blade.php'));
        $this->assertStringContainsString('_awaitBackground', $sql);
        $this->assertStringContainsString("event.event === 'pending'", $sql);
    }
}
