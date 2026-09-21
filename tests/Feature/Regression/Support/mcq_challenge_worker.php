<?php

/**
 * Child process for Ds16McqChallengeConcurrencyMysqlTest.
 *
 * Boots the real application against the MySQL/MariaDB test database (DB_*
 * environment variables come from the parent), waits for a shared start time
 * so all workers fire together, then runs one real controller action.
 *
 * argv: <action> <userId> <challengeId> <startAtMicrotime> <payloadJson>
 * actions: autosave | submit | start | publish
 */

use App\Http\Controllers\AdminMcqChallengeController;
use App\Http\Controllers\ChallengesController;
use App\Models\Challenge;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

require __DIR__ . '/../../../../vendor/autoload.php';

[$script, $action, $userId, $challengeId, $startAt, $payloadJson] = $argv;

$app = require __DIR__ . '/../../../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$result = ['pid' => getmypid(), 'action' => $action];

try {
    Auth::loginUsingId((int) $userId);
    $payload = json_decode($payloadJson, true) ?: [];

    $request = Request::create('/race', in_array($action, ['start'], true) ? 'GET' : 'POST', $payload);
    $request->headers->set('Accept', 'application/json');
    $request->setLaravelSession($app['session.store']);
    $app->instance('request', $request);

    $wait = (float) $startAt - microtime(true);
    if ($wait > 0) {
        usleep((int) ($wait * 1000000));
    }

    $controller = $app->make(ChallengesController::class);

    if ($action === 'autosave') {
        $response = $app->call([$controller, 'autosaveQuiz'], ['request' => $request, 'slug' => 'newbie', 'challenge_id' => (int) $challengeId]);
    } elseif ($action === 'submit') {
        $response = $app->call([$controller, 'submitQuiz'], ['request' => $request, 'slug' => 'newbie', 'challenge_id' => (int) $challengeId]);
    } elseif ($action === 'start') {
        $response = $app->call([$controller, 'showQuiz'], ['slug' => 'newbie', 'challenge_id' => (int) $challengeId]);
    } elseif ($action === 'publish') {
        $response = $app->call([$app->make(AdminMcqChallengeController::class), 'toggleStatus'], ['challenge' => Challenge::findOrFail((int) $challengeId)]);
    } else {
        throw new InvalidArgumentException('Unknown action ' . $action);
    }

    $result['outcome'] = 'response';
    if ($response instanceof \Illuminate\Contracts\View\View) {
        $result['status'] = 200;
        $result['attempt_id'] = $response->getData()['attempt']->id ?? null;
        $result['attempt_challenge_id'] = $response->getData()['attempt']->challenge_id ?? null;
    } else {
        $result['status'] = $response->getStatusCode();
        $result['location'] = $response->headers->get('Location');
        $result['body'] = method_exists($response, 'getData') ? $response->getData(true) : null;
    }
    $result['flash'] = $app['session.store']->get('success');
    $result['flash_error'] = $app['session.store']->get('error');
} catch (HttpExceptionInterface $exception) {
    $result['outcome'] = 'http_exception';
    $result['status'] = $exception->getStatusCode();
} catch (Throwable $exception) {
    $result['outcome'] = 'exception';
    $result['class'] = get_class($exception);
    $result['message'] = $exception->getMessage();
}

echo json_encode($result), PHP_EOL;
