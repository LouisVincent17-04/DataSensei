<?php

namespace Tests\Feature\Regression;

use App\Models\ChallengeAttempt;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Regression\Concerns\BuildsMcqChallengeWorkflow;
use Tests\TestCase;

/**
 * DS-01 (MCQ challenges): answers freeze at the server deadline.
 */
class Ds01McqChallengeDeadlineTest extends TestCase
{
    use BuildsMcqChallengeWorkflow;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([ValidateCsrfToken::class]);
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:00:00'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_late_replacement_answers_cannot_improve_score_or_xp(): void
    {
        $student = $this->mcqStudent();
        [$challenge, $questions] = $this->mcqChallenge();
        $attempt = $this->startMcqAttempt($student, $challenge);

        // Before the deadline: one wrong answer is autosaved, question 2 stays blank.
        $this->postJson($this->quizUrl($challenge, '/autosave'), [
            'attempt_id' => $attempt->id,
            'question_id' => $questions[0]['question']->id,
            'option_id' => $questions[0]['wrong']->id,
        ])->assertOk();

        Carbon::setTestNow(Carbon::parse('2026-09-20 09:10:05'));

        // After the deadline the student posts all-correct answers.
        $this->post($this->quizUrl($challenge, '/submit'), [
            'attempt_id' => $attempt->id,
            'answers' => [
                $questions[0]['question']->id => $questions[0]['correct']->id,
                $questions[1]['question']->id => $questions[1]['correct']->id,
            ],
        ])->assertRedirect();

        $attempt->refresh();
        $this->assertSame('expired', $attempt->status);
        $this->assertSame(0, (int) $attempt->score, 'Late answers must not be graded.');
        $this->assertSame(0, (int) $attempt->xp_awarded, 'Late answers must not earn XP.');
        $this->assertSame(0, (int) $student->fresh()->xp);
        $this->assertSame($questions[0]['wrong']->id, $this->savedOption($attempt, $questions[0]['question']));
        $this->assertNull($this->savedOption($attempt, $questions[1]['question']));
        $this->assertSame(0, (int) DB::table('challenge_user')->where('user_id', $student->id)->value('xp_awarded'));
    }

    public function test_draft_saved_before_expiry_keeps_its_score_when_finalized_late(): void
    {
        $student = $this->mcqStudent();
        [$challenge, $questions] = $this->mcqChallenge();
        $attempt = $this->startMcqAttempt($student, $challenge);

        foreach ($questions as $row) {
            $this->postJson($this->quizUrl($challenge, '/autosave'), [
                'attempt_id' => $attempt->id,
                'question_id' => $row['question']->id,
                'option_id' => $row['correct']->id,
            ])->assertOk();
        }

        Carbon::setTestNow(Carbon::parse('2026-09-20 09:10:05'));

        // The late submit tries to blank / worsen nothing and improve nothing:
        // whatever it posts is ignored, the eligible saved work is graded.
        $this->post($this->quizUrl($challenge, '/submit'), [
            'attempt_id' => $attempt->id,
            'answers' => [
                $questions[0]['question']->id => $questions[0]['wrong']->id,
            ],
        ])->assertRedirect();

        $attempt->refresh();
        $this->assertSame('expired', $attempt->status);
        $this->assertSame(2, (int) $attempt->score);
        // Ranked, eligible work saved in time earns base XP; an expired attempt
        // has used the full time limit so there is no speed bonus.
        $this->assertSame(100, (int) $attempt->xp_awarded);
        $this->assertSame(100, (int) $student->fresh()->xp);
    }

    public function test_autosave_after_expiry_is_refused_and_changes_nothing(): void
    {
        $student = $this->mcqStudent();
        [$challenge, $questions] = $this->mcqChallenge();
        $attempt = $this->startMcqAttempt($student, $challenge);

        $this->postJson($this->quizUrl($challenge, '/autosave'), [
            'attempt_id' => $attempt->id,
            'question_id' => $questions[0]['question']->id,
            'option_id' => $questions[0]['wrong']->id,
        ])->assertOk();

        Carbon::setTestNow(Carbon::parse('2026-09-20 09:10:00'));

        $this->postJson($this->quizUrl($challenge, '/autosave'), [
            'attempt_id' => $attempt->id,
            'question_id' => $questions[0]['question']->id,
            'option_id' => $questions[0]['correct']->id,
        ])->assertStatus(409)->assertJson(['ok' => false, 'status' => 'expired']);

        $this->assertSame($questions[0]['wrong']->id, $this->savedOption($attempt, $questions[0]['question']));
    }

    public function test_duplicate_submit_is_idempotent(): void
    {
        $student = $this->mcqStudent();
        [$challenge, $questions] = $this->mcqChallenge();
        $attempt = $this->startMcqAttempt($student, $challenge);

        Carbon::setTestNow(Carbon::parse('2026-09-20 09:05:00'));

        $payload = [
            'attempt_id' => $attempt->id,
            'answers' => [
                $questions[0]['question']->id => $questions[0]['correct']->id,
                $questions[1]['question']->id => $questions[1]['correct']->id,
            ],
        ];

        $first = $this->post($this->quizUrl($challenge, '/submit'), $payload)->assertRedirect();
        $xpAfterFirst = (int) $student->fresh()->xp;
        $this->assertGreaterThan(0, $xpAfterFirst);

        // Replay, this time trying to change answers as well.
        $payload['answers'][$questions[0]['question']->id] = $questions[0]['wrong']->id;
        $second = $this->post($this->quizUrl($challenge, '/submit'), $payload)->assertRedirect();

        $this->assertSame($first->headers->get('Location'), $second->headers->get('Location'));
        $this->assertSame($xpAfterFirst, (int) $student->fresh()->xp);
        $this->assertSame(1, ChallengeAttempt::where('user_id', $student->id)->count());
        $this->assertSame(1, DB::table('challenge_user')->where('user_id', $student->id)->count());
        $this->assertSame(2, (int) $attempt->fresh()->score);
        $this->assertSame($questions[0]['correct']->id, $this->savedOption($attempt, $questions[0]['question']));
    }

    public function test_another_students_attempt_identity_is_rejected(): void
    {
        $owner = $this->mcqStudent();
        $intruder = $this->mcqStudent();
        [$challenge, $questions] = $this->mcqChallenge();
        $attempt = $this->startMcqAttempt($owner, $challenge);

        $this->actAsStudent($intruder);

        $this->postJson($this->quizUrl($challenge, '/autosave'), [
            'attempt_id' => $attempt->id,
            'question_id' => $questions[0]['question']->id,
            'option_id' => $questions[0]['correct']->id,
        ])->assertNotFound();

        $this->postJson($this->quizUrl($challenge, '/heartbeat'), ['attempt_id' => $attempt->id])->assertNotFound();

        $this->post($this->quizUrl($challenge, '/submit'), [
            'attempt_id' => $attempt->id,
            'answers' => [$questions[0]['question']->id => $questions[0]['correct']->id],
        ])->assertNotFound();

        $this->assertSame('in_progress', $attempt->fresh()->status);
        $this->assertNull($this->savedOption($attempt, $questions[0]['question']));
    }

    public function test_attempt_id_of_a_different_challenge_is_rejected(): void
    {
        $student = $this->mcqStudent();
        [$challenge] = $this->mcqChallenge();
        [$other, $otherQuestions] = $this->mcqChallenge([
            'content_code' => 'MCQ-DS-OTHER',
            'title' => 'Other challenge',
        ]);
        $attempt = $this->startMcqAttempt($student, $challenge);

        $this->postJson($this->quizUrl($other, '/autosave'), [
            'attempt_id' => $attempt->id,
            'question_id' => $otherQuestions[0]['question']->id,
            'option_id' => $otherQuestions[0]['correct']->id,
        ])->assertNotFound();

        $this->post($this->quizUrl($other, '/submit'), ['attempt_id' => $attempt->id])->assertNotFound();
        $this->assertSame('in_progress', $attempt->fresh()->status);
    }

    public function test_stale_autosave_does_not_roll_back_a_newer_edit(): void
    {
        $student = $this->mcqStudent();
        [$challenge, $questions] = $this->mcqChallenge();
        $attempt = $this->startMcqAttempt($student, $challenge);
        $question = $questions[0]['question'];

        $this->postJson($this->quizUrl($challenge, '/autosave'), [
            'attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'option_id' => $questions[0]['correct']->id,
            'seq' => 5,
        ])->assertOk()->assertJson(['ok' => true, 'stale' => false]);

        // The older edit (seq 4) was delayed on the network and arrives last.
        $this->postJson($this->quizUrl($challenge, '/autosave'), [
            'attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'option_id' => $questions[0]['wrong']->id,
            'seq' => 4,
        ])->assertOk()->assertJson(['ok' => true, 'stale' => true]);

        $this->assertSame($questions[0]['correct']->id, $this->savedOption($attempt, $question));
    }
}
