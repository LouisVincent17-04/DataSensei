<?php

/**
 * Child process for Ds01AssessmentSubmitRaceMysqlTest.
 *
 * Boots the real application against the MySQL/MariaDB test database (taken
 * from the DB_* environment variables the parent passes), waits for a shared
 * start time so that all workers fire together, then runs the real
 * StudentAssessmentController::submit() for the same submission.
 *
 * argv: <assessmentId> <submissionId> <studentId> <startAtMicrotime> <answersJson>
 */

use App\Http\Controllers\StudentAssessmentController;
use App\Models\Assessment;
use App\Models\AssessmentSubmission;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

require __DIR__ . '/../../../../vendor/autoload.php';

[$script, $assessmentId, $submissionId, $studentId, $startAt, $answersJson] = $argv;

$app = require __DIR__ . '/../../../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$result = ['pid' => getmypid()];

try {
    Auth::loginUsingId((int) $studentId);
    $assessment = Assessment::findOrFail((int) $assessmentId);
    $submission = AssessmentSubmission::findOrFail((int) $submissionId);
    $result['status_seen_before'] = $submission->status;

    $request = Request::create('/race', 'POST', ['answers' => json_decode($answersJson, true)]);
    $request->setLaravelSession($app['session.store']);
    $app->instance('request', $request);

    // Barrier: every worker has already loaded an in_progress submission, so
    // each of them passes the pre-transaction checks and they collide on the
    // row lock inside submit().
    $wait = (float) $startAt - microtime(true);
    if ($wait > 0) {
        usleep((int) ($wait * 1000000));
    }

    $response = $app->call(
        [$app->make(StudentAssessmentController::class), 'submit'],
        ['request' => $request, 'assessment' => $assessment, 'submission' => $submission]
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
