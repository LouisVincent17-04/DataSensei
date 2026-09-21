<?php

/**
 * DS-15 race reproduction worker — TEST ONLY. Runs as its own PHP process
 * against the MariaDB/MySQL database named by the DB_* environment variables.
 *
 *   php ds15_coding_race_worker.php submit <userId> <challengeId> <questionId> <flagDir>
 *   php ds15_coding_race_worker.php retake <userId> <challengeId> <questionId> <flagDir>
 *
 * "submit" grades through a fake sandbox that signals <flagDir>/grading.started
 * and then blocks until <flagDir>/retake.done exists: the delay is injected
 * through the container binding, production code contains no sleep.
 * "retake" waits for grading.started, performs the retake, starts the new run
 * and signals retake.done.
 */

use App\Http\Controllers\CodingQuizController;
use App\Models\Challenge;
use App\Models\CodingQuestion;
use App\Services\ChallengePathUnlockService;
use App\Services\GamificationService;
use App\Services\PythonCodePolicyService;
use App\Services\PythonSandboxService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

[$script, $mode, $userId, $challengeId, $questionId, $flagDir] = $argv + [null, null, null, null, null, null];

require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$waitFor = static function (string $file, int $seconds = 30): void {
    $deadline = microtime(true) + $seconds;
    while (!file_exists($file)) {
        if (microtime(true) > $deadline) {
            fwrite(STDERR, "timeout waiting for {$file}\n");
            exit(3);
        }
        usleep(50000);
    }
};

$unlock = Mockery::mock(ChallengePathUnlockService::class);
$unlock->shouldReceive('lockInfo')->andReturn(['unlocked' => true]);
$unlock->shouldReceive('notifyExceptionalUnlocks')->andReturn([]);
$app->instance(ChallengePathUnlockService::class, $unlock);

$gamification = Mockery::mock(GamificationService::class);
$gamification->shouldReceive('awardForCodingSubmission')->andReturn([]);
$app->instance(GamificationService::class, $gamification);

$app->instance(PythonSandboxService::class, new class($flagDir, $waitFor) extends PythonSandboxService {
    public function __construct(private string $flagDir, private Closure $waitFor)
    {
        parent::__construct(new PythonCodePolicyService());
    }

    public function runInline(string $code, string $stdin = '', array $options = []): array
    {
        touch($this->flagDir . '/grading.started');
        ($this->waitFor)($this->flagDir . '/retake.done');

        return ['stdout' => '42', 'stderr' => '', 'exit_code' => 0, 'failed' => false, 'timed_out' => false, 'plots' => []];
    }
});

Auth::loginUsingId((int) $userId);
$challenge = Challenge::findOrFail((int) $challengeId);
$question = CodingQuestion::findOrFail((int) $questionId);
$slug = $challenge->category->slug;
$controller = $app->make(CodingQuizController::class);

if ($mode === 'submit') {
    $request = Request::create('/race', 'POST', ['code' => 'print(42)']);
    $app->instance('request', $request);
    $response = $controller->submit($request, $slug, $challenge, $question, $gamification);
    echo json_encode(['http' => $response->getStatusCode(), 'body' => $response->getData(true)]);
    exit(0);
}

if ($mode === 'retake') {
    $waitFor($flagDir . '/grading.started');
    $controller->retake($slug, $challenge);
    $controller->start($slug, $challenge, $question);
    touch($flagDir . '/retake.done');
    echo json_encode(['retake' => 'done']);
    exit(0);
}

fwrite(STDERR, "unknown mode\n");
exit(2);
