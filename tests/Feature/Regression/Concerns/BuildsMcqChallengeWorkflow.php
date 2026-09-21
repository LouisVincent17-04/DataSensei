<?php

namespace Tests\Feature\Regression\Concerns;

use App\Models\Challenge;
use App\Models\ChallengeAttempt;
use App\Models\ChallengeCategory;
use App\Models\ChallengeOption;
use App\Models\ChallengeQuestion;
use App\Models\User;
use App\Support\AuthSessionFingerprint;
use Illuminate\Support\Facades\DB;

trait BuildsMcqChallengeWorkflow
{
    protected function mcqStudent(): User
    {
        return User::factory()->create(['role' => User::ROLE_USER, 'status' => 'active']);
    }

    protected function actAsStudent(User $student): static
    {
        return $this->actingAs($student)->withSession([
            AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($student),
        ]);
    }

    protected function mcqCategory(): ChallengeCategory
    {
        return ChallengeCategory::firstOrCreate(['slug' => 'newbie'], [
            'name' => 'Newbie',
            'target_audience' => 'Beginners',
            'description' => 'Beginner challenges',
            'order_index' => 1,
        ]);
    }

    /**
     * @return array{0: Challenge, 1: array<int, array{question: ChallengeQuestion, correct: ChallengeOption, wrong: ChallengeOption}>}
     */
    protected function mcqChallenge(array $overrides = [], int $questionCount = 2): array
    {
        $category = $this->mcqCategory();

        $challenge = Challenge::create(array_merge([
            'challenge_category_id' => $category->id,
            'content_code' => 'MCQ-DS-REGRESSION',
            'title' => 'Python Basics',
            'description' => 'Check basic Python knowledge.',
            'time_limit_seconds' => 600,
            'base_xp' => 100,
            'order_index' => 1,
            'is_coding_challenge' => false,
            'version_no' => 1,
            'version_name' => 'Version 1',
            'version_code' => 'V1',
            'is_active' => true,
        ], $overrides));

        $questions = [];
        for ($i = 1; $i <= $questionCount; $i++) {
            $question = ChallengeQuestion::create([
                'challenge_id' => $challenge->id,
                'challenge_category_id' => $category->id,
                'question_text' => "Question {$i} of {$challenge->version_code}?",
                'order_index' => $i,
            ]);

            $questions[] = [
                'question' => $question,
                'correct' => ChallengeOption::create([
                    'challenge_question_id' => $question->id,
                    'option_text' => "right {$i}",
                    'is_correct' => true,
                    'order_index' => 1,
                ]),
                'wrong' => ChallengeOption::create([
                    'challenge_question_id' => $question->id,
                    'option_text' => "wrong {$i}",
                    'is_correct' => false,
                    'order_index' => 2,
                ]),
            ];
        }

        return [$challenge, $questions];
    }

    protected function quizUrl(Challenge $challenge, string $suffix = ''): string
    {
        return "/challenges/newbie/quiz/{$challenge->id}{$suffix}";
    }

    protected function startMcqAttempt(User $student, Challenge $challenge): ChallengeAttempt
    {
        $this->actAsStudent($student)->get($this->quizUrl($challenge))->assertOk();

        return ChallengeAttempt::where('user_id', $student->id)
            ->where('challenge_id', $challenge->id)
            ->where('status', 'in_progress')
            ->latest('id')
            ->firstOrFail();
    }

    protected function savedOption(ChallengeAttempt $attempt, ChallengeQuestion $question): ?int
    {
        $value = DB::table('challenge_attempt_answers')
            ->where('challenge_attempt_id', $attempt->id)
            ->where('challenge_question_id', $question->id)
            ->value('selected_option_id');

        return $value === null ? null : (int) $value;
    }
}
