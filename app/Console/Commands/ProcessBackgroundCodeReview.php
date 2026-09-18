<?php

namespace App\Console\Commands;

use App\Http\Controllers\CodeReviewController;
use App\Services\CodeReview\BackgroundCodeReviewService;
use Illuminate\Console\Command;
use Throwable;

class ProcessBackgroundCodeReview extends Command
{
    protected $signature = 'code-review:process {review : Background review id}';

    protected $description = 'Finish an AI code review that took longer than the web request time limit.';

    public function handle(BackgroundCodeReviewService $reviews): int
    {
        $id = (string) $this->argument('review');
        $job = $reviews->find($id);

        if ($job === null || $job['status'] !== 'pending' || ! $reviews->claim($id)) {
            return self::SUCCESS;
        }

        // A newer Run replaced this review, so the model is not asked again.
        if ($job['mode'] === 'review' && $reviews->isSuperseded($job)) {
            $reviews->complete($id, "Status: Not Reviewed\nFeedback: A newer run replaced this review.", 'superseded', true);

            return self::SUCCESS;
        }

        $reviews->markStarted($id);

        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }

        try {
            // Reuses the controller's prompt handling and no-code sanitizers so a
            // background answer is held to exactly the same rules.
            $result = app(CodeReviewController::class)->completeBackgroundReview($job);
        } catch (Throwable $exception) {
            report($exception);
            $result = [
                'outcome' => 'background_failed',
                'message' => "Status: Not Reviewed\nFeedback: The AI reviewer failed while finishing this review. Nothing is wrong with your run.",
                'fallback' => true,
            ];
        }

        $reviews->complete($id, (string) $result['message'], (string) $result['outcome'], (bool) $result['fallback']);

        return self::SUCCESS;
    }
}
