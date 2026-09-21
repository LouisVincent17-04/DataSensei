<?php

/**
 * Child process for Ds01AssignmentSubmitRaceMysqlTest.
 *
 * Boots the real application against the MySQL/MariaDB test database (DB_*
 * environment variables from the parent), loads the in-progress attempt, waits
 * for a shared start time so that all workers fire together, then runs the
 * real StudentAssignmentController::submit() for the same submission.
 *
 * argv: <assignmentId> <submissionId> <studentId> <startAtMicrotime> <payloadJson>
 */

use App\Http\Controllers\StudentAssignmentController;
use App\Models\AssignmentSubmission;
use App\Models\ClassAssignment;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

require __DIR__ . '/../../../../vendor/autoload.php';

[$script, $assignmentId, $submissionId, $studentId, $startAt, $payloadJson] = $argv;

$app = require __DIR__ . '/../../../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$result = ['pid' => getmypid()];

try {
    Auth::loginUsingId((int) $studentId);
    $assignment = ClassAssignment::findOrFail((int) $assignmentId);
    $submission = AssignmentSubmission::findOrFail((int) $submissionId);
    $result['status_seen_before'] = $submission->status;

    $request = Request::create('/race', 'POST', json_decode($payloadJson, true));
    $request->setLaravelSession($app['session.store']);
    $app->instance('request', $request);

    // Barrier: every worker already holds an in_progress model, passes the
    // pre-transaction checks and collides on the row lock inside submit().
    $wait = (float) $startAt - microtime(true);
    if ($wait > 0) {
        usleep((int) ($wait * 1000000));
    }

    $response = $app->call(
        [$app->make(StudentAssignmentController::class), 'submit'],
        ['request' => $request, 'assignment' => $assignment, 'submission' => $submission]
    );

    $result['outcome'] = 'response';
    $result['location'] = $response->headers->get('Location');
    $result['flash'] = $app['session.store']->get('success');
} catch (ValidationException $exception) {
    $result['outcome'] = 'validation';
    $result['errors'] = $exception->errors();
} catch (Throwable $exception) {
    $result['outcome'] = 'exception';
    $result['class'] = get_class($exception);
    $result['message'] = $exception->getMessage();
}

echo json_encode($result), PHP_EOL;
