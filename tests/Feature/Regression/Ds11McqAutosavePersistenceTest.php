<?php

namespace Tests\Feature\Regression;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Regression\Concerns\BuildsMcqChallengeWorkflow;
use Tests\TestCase;

/**
 * DS-10 / DS-11 server side: per-question autosaves persist independently and
 * the quiz page is wired to the extracted timer/autosave client.
 */
class Ds11McqAutosavePersistenceTest extends TestCase
{
    use BuildsMcqChallengeWorkflow;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([ValidateCsrfToken::class]);
    }

    public function test_two_autosaves_for_different_questions_both_persist_and_are_restored_on_refresh(): void
    {
        $student = $this->mcqStudent();
        [$challenge, $questions] = $this->mcqChallenge();
        $attempt = $this->startMcqAttempt($student, $challenge);

        // Same sequence numbers on different questions are independent.
        $this->postJson($this->quizUrl($challenge, '/autosave'), [
            'attempt_id' => $attempt->id,
            'question_id' => $questions[0]['question']->id,
            'option_id' => $questions[0]['correct']->id,
            'seq' => 1,
        ])->assertOk()->assertJson(['ok' => true, 'answered_count' => 1]);

        $this->postJson($this->quizUrl($challenge, '/autosave'), [
            'attempt_id' => $attempt->id,
            'question_id' => $questions[1]['question']->id,
            'option_id' => $questions[1]['wrong']->id,
            'seq' => 1,
        ])->assertOk()->assertJson(['ok' => true, 'answered_count' => 2]);

        $this->assertSame($questions[0]['correct']->id, $this->savedOption($attempt, $questions[0]['question']));
        $this->assertSame($questions[1]['wrong']->id, $this->savedOption($attempt, $questions[1]['question']));

        // Refresh: same attempt, both selections restored from the server.
        $page = $this->get($this->quizUrl($challenge))->assertOk();
        $html = $page->getContent();

        $this->assertSame($attempt->id, $page->viewData('attempt')->id);
        $this->assertEquals([
            $questions[0]['question']->id => $questions[0]['correct']->id,
            $questions[1]['question']->id => $questions[1]['wrong']->id,
        ], $page->viewData('savedAnswers'));
        $this->assertMatchesRegularExpression(
            '/name="answers\[' . $questions[0]['question']->id . '\]" value="' . $questions[0]['correct']->id . '" checked/',
            $html
        );
        $this->assertMatchesRegularExpression(
            '/name="answers\[' . $questions[1]['question']->id . '\]" value="' . $questions[1]['wrong']->id . '" checked/',
            $html
        );
        $this->assertSame(1, $page->viewData('autosaveSeqBase'));
    }

    public function test_quiz_page_uses_the_extracted_clock_and_per_question_queue(): void
    {
        $student = $this->mcqStudent();
        [$challenge] = $this->mcqChallenge();
        $this->startMcqAttempt($student, $challenge);

        $html = $this->get($this->quizUrl($challenge))->assertOk()->getContent();

        $this->assertStringContainsString('js/challenge-quiz-client.js', $html);
        $this->assertStringContainsString('createQuizClock', $html);
        $this->assertStringContainsString('createAutosaveQueue', $html);
        // DS-10: no page-load anchor that outlives a heartbeat re-sync.
        $this->assertStringNotContainsString('clientLoadedMs', $html);
        // DS-11: no single debounce timer shared by every question.
        $this->assertStringNotContainsString('autosaveTimer', $html);
    }

    public function test_heartbeat_reports_server_time_and_the_unchanged_deadline(): void
    {
        $student = $this->mcqStudent();
        [$challenge] = $this->mcqChallenge();
        $attempt = $this->startMcqAttempt($student, $challenge);

        $response = $this->postJson($this->quizUrl($challenge, '/heartbeat'), ['attempt_id' => $attempt->id])
            ->assertOk()
            ->assertJson(['ok' => true, 'status' => 'in_progress', 'should_submit' => false]);

        $this->assertSame($attempt->expires_at->getTimestamp() * 1000, $response->json('expires_at_ms'));
        $this->assertEqualsWithDelta(now()->getTimestampMs(), $response->json('server_now_ms'), 5000);
    }
}
