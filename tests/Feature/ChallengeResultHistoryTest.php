<?php

namespace Tests\Feature;

use App\Models\Challenge;
use App\Models\ChallengeAttempt;
use App\Models\ChallengeAttemptAnswer;
use App\Models\ChallengeCategory;
use App\Models\ChallengeOption;
use App\Models\ChallengeQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChallengeResultHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_review_a_finished_attempt_and_see_attempt_history(): void
    {
        $this->withoutMiddleware();

        $student = User::factory()->create([
            'role' => User::ROLE_USER,
            'status' => 'active',
        ]);

        [$challenge, $question, $correctOption, $wrongOption] = $this->makeChallenge();

        $firstAttempt = $this->makeAttempt($student, $challenge, $question, [$correctOption, $wrongOption], [
            'attempt_no' => 1,
            'mode' => 'ranked',
            'is_ranked' => true,
            'score' => 1,
            'xp_awarded' => 100,
        ]);

        ChallengeAttemptAnswer::create([
            'challenge_attempt_id' => $firstAttempt->id,
            'challenge_question_id' => $question->id,
            'selected_option_id' => $correctOption->id,
            'answered_at' => now()->subMinutes(3),
        ]);

        $secondAttempt = $this->makeAttempt($student, $challenge, $question, [$correctOption, $wrongOption], [
            'attempt_no' => 2,
            'mode' => 'practice',
            'is_ranked' => false,
            'score' => 0,
            'xp_awarded' => 0,
            'status' => 'expired',
        ]);

        ChallengeAttemptAnswer::create([
            'challenge_attempt_id' => $secondAttempt->id,
            'challenge_question_id' => $question->id,
            'selected_option_id' => $wrongOption->id,
            'answered_at' => now()->subMinute(),
        ]);

        $response = $this->actingAs($student)->get(route('challenges.quiz.result', [
            'slug' => 'newbie',
            'challenge' => $challenge->id,
            'attempt' => $secondAttempt->id,
        ]));

        $response->assertOk()
            // The page heading is the challenge title; the former "Challenge Result"
            // eyebrow label was removed from the view, the document title remains.
            ->assertSee('Python Basics Result')
            ->assertSee('Retake Challenge')
            ->assertSee('Attempt History')
            ->assertSee('Attempt #2')
            ->assertSee('Correct answer:')
            ->assertSee($correctOption->option_text);
    }

    public function test_student_cannot_view_another_students_attempt(): void
    {
        $this->withoutMiddleware();

        $owner = User::factory()->create(['role' => User::ROLE_USER, 'status' => 'active']);
        $otherStudent = User::factory()->create(['role' => User::ROLE_USER, 'status' => 'active']);
        [$challenge, $question, $correctOption, $wrongOption] = $this->makeChallenge();

        $attempt = $this->makeAttempt($owner, $challenge, $question, [$correctOption, $wrongOption]);

        $response = $this->actingAs($otherStudent)->get(route('challenges.quiz.result', [
            'slug' => 'newbie',
            'challenge' => $challenge->id,
            'attempt' => $attempt->id,
        ]));

        $response->assertNotFound();
    }

    private function makeChallenge(): array
    {
        $category = ChallengeCategory::create([
            'name' => 'Newbie',
            'slug' => 'newbie',
            'target_audience' => 'Beginners',
            'description' => 'Beginner challenges',
            'order_index' => 1,
        ]);

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title' => 'Python Basics',
            'description' => 'Check basic Python knowledge.',
            'time_limit_seconds' => 600,
            'base_xp' => 100,
            'order_index' => 1,
            'is_coding_challenge' => false,
            'is_active' => true,
        ]);

        $question = ChallengeQuestion::create([
            'challenge_id' => $challenge->id,
            'challenge_category_id' => $category->id,
            'question_text' => 'Which keyword creates a function?',
            'order_index' => 1,
        ]);

        $correctOption = ChallengeOption::create([
            'challenge_question_id' => $question->id,
            'option_text' => 'def',
            'is_correct' => true,
            'order_index' => 1,
        ]);

        $wrongOption = ChallengeOption::create([
            'challenge_question_id' => $question->id,
            'option_text' => 'function',
            'is_correct' => false,
            'order_index' => 2,
        ]);

        return [$challenge, $question, $correctOption, $wrongOption];
    }

    private function makeAttempt(
        User $student,
        Challenge $challenge,
        ChallengeQuestion $question,
        array $options,
        array $overrides = []
    ): ChallengeAttempt {
        return ChallengeAttempt::create(array_merge([
            'user_id' => $student->id,
            'challenge_id' => $challenge->id,
            'attempt_no' => 1,
            'mode' => 'ranked',
            'status' => 'submitted',
            'started_at' => now()->subMinutes(5),
            'expires_at' => now()->addMinutes(5),
            'submitted_at' => now()->subMinutes(2),
            'last_seen_at' => now()->subMinutes(2),
            'time_limit_seconds' => 600,
            'time_taken_seconds' => 180,
            'score' => 1,
            'total_questions' => 1,
            'xp_awarded' => 100,
            'is_ranked' => true,
            'is_leaderboard_eligible' => true,
            'suspicious_event_count' => 0,
            'question_order' => [$question->id],
            'option_order' => [
                $question->id => collect($options)->pluck('id')->all(),
            ],
        ], $overrides));
    }
}
