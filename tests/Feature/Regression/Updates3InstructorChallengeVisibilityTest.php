<?php

namespace Tests\Feature\Regression;

use App\Models\Challenge;
use App\Models\ChallengeAttempt;
use App\Models\ChallengeCategory;
use App\Models\ClassChallengeAssignment;
use App\Models\ClassRoom;
use App\Models\CodingSubmission;
use App\Models\Institution;
use App\Models\User;
use App\Services\ChallengePathUnlockService;
use App\Support\AuthSessionFingerprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Updates 3, D3 and D4: what students see of instructor-built challenges. An
 * instructor challenge appears on the University Student map and on the
 * assignments page only for students whose class has a published, open
 * entry for it; a direct URL is refused otherwise; and it never counts
 * toward completing the level.
 */
class Updates3InstructorChallengeVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private ?Institution $institution = null;

    private const SLUG = 'university-student';

    public function test_enrolled_student_sees_the_open_instructor_quiz_on_the_map_and_can_start_it(): void
    {
        [$university, $instructor, $class, $student] = $this->scenario();
        $outsider = $this->makeEnrolledStudent($this->makeClass($this->makeUser(User::ROLE_INSTRUCTOR), 'Other Class'));

        $platform = $this->makeChallenge($university, 'Platform Stats Quiz', false, true);
        $mine = $this->makeChallenge($university, 'Instructor Week 3 Quiz', false, true, $instructor);
        $unassigned = $this->makeChallenge($university, 'Instructor Unassigned Quiz', false, true, $instructor);
        $this->give($class, $mine, $instructor);

        $this->actingAsUser($student)
            ->get(route('challenges.map', self::SLUG))
            ->assertOk()
            ->assertSee('Platform Stats Quiz')
            ->assertSee('Instructor Week 3 Quiz')
            ->assertDontSee('Instructor Unassigned Quiz')
            ->assertSeeInOrder(['Platform Stats Quiz', 'Instructor Week 3 Quiz']);

        $this->actingAsUser($student)
            ->get(route('challenges.quiz', ['slug' => self::SLUG, 'challenge' => $mine->id]))
            ->assertOk()
            ->assertSee('Question?');

        $this->assertSame(1, ChallengeAttempt::where('user_id', $student->id)->where('challenge_id', $mine->id)->count());

        $this->actingAsUser($outsider)
            ->get(route('challenges.map', self::SLUG))
            ->assertOk()
            ->assertSee('Platform Stats Quiz')
            ->assertDontSee('Instructor Week 3 Quiz');

        $this->actingAsUser($outsider)
            ->get(route('challenges.quiz', ['slug' => self::SLUG, 'challenge' => $mine->id]))
            ->assertNotFound();
        $this->actingAsUser($student)
            ->get(route('challenges.quiz', ['slug' => self::SLUG, 'challenge' => $unassigned->id]))
            ->assertNotFound();

        $this->assertSame(0, ChallengeAttempt::where('user_id', $outsider->id)->count());
    }

    public function test_enrolled_student_sees_the_open_instructor_coding_challenge_and_others_get_404(): void
    {
        [$university, $instructor, $class, $student] = $this->scenario();
        $outsider = $this->makeEnrolledStudent($this->makeClass($this->makeUser(User::ROLE_INSTRUCTOR), 'Other Class'));

        $platform = $this->makeChallenge($university, 'Platform Coding Problem', true, true);
        $mine = $this->makeChallenge($university, 'Instructor Coding Problem', true, true, $instructor);
        $this->give($class, $mine, $instructor);

        $this->actingAsUser($student)
            ->get(route('challenges.coding.map', self::SLUG))
            ->assertOk()
            ->assertSee('Platform Coding Problem')
            ->assertSee('Instructor Coding Problem');

        $this->actingAsUser($student)
            ->get(route('challenges.coding.quiz', ['slug' => self::SLUG, 'challenge' => $mine->id]))
            ->assertOk();

        $this->actingAsUser($outsider)
            ->get(route('challenges.coding.map', self::SLUG))
            ->assertOk()
            ->assertSee('Platform Coding Problem')
            ->assertDontSee('Instructor Coding Problem');

        $question = $mine->codingQuestions()->first();
        $this->actingAsUser($outsider)
            ->get(route('challenges.coding.quiz', ['slug' => self::SLUG, 'challenge' => $mine->id]))
            ->assertNotFound();
        $this->actingAsUser($outsider)
            ->post(route('challenges.coding.start', ['slug' => self::SLUG, 'challenge' => $mine->id, 'question' => $question->id]))
            ->assertNotFound();
        $this->actingAsUser($outsider)
            ->postJson(route('challenges.coding.submit', ['slug' => self::SLUG, 'challenge' => $mine->id, 'question' => $question->id]), ['code' => 'print(42)'])
            ->assertNotFound();
    }

    public function test_draft_future_past_due_and_closed_entries_hide_the_challenge(): void
    {
        [$university, $instructor, $class, $student] = $this->scenario();
        $mine = $this->makeChallenge($university, 'Instructor Week 3 Quiz', false, true, $instructor);
        $entry = $this->give($class, $mine, $instructor);

        $hidden = [
            'draft' => ['status' => 'draft', 'available_at' => null, 'due_at' => null],
            'closed' => ['status' => 'closed', 'available_at' => null, 'due_at' => null],
            'not yet open' => ['status' => 'published', 'available_at' => now()->addDay(), 'due_at' => null],
            'past due' => ['status' => 'published', 'available_at' => null, 'due_at' => now()->subMinute()],
        ];

        foreach ($hidden as $label => $state) {
            $entry->update($state);

            $this->actingAsUser($student)
                ->get(route('challenges.map', self::SLUG))
                ->assertOk()
                ->assertDontSee('Instructor Week 3 Quiz');
            $this->actingAsUser($student)
                ->get(route('challenges.quiz', ['slug' => self::SLUG, 'challenge' => $mine->id]))
                ->assertNotFound();
            $this->actingAsUser($student)
                ->get(route('student.assignments.index'))
                ->assertOk()
                ->assertDontSee('Instructor Week 3 Quiz');
        }

        $entry->update(['status' => 'published', 'available_at' => now()->subHour(), 'due_at' => now()->addHour()]);

        $this->actingAsUser($student)
            ->get(route('challenges.map', self::SLUG))
            ->assertOk()
            ->assertSee('Instructor Week 3 Quiz');

        // Making the challenge itself unavailable hides it even with an open entry.
        $mine->update(['is_active' => false]);

        $this->actingAsUser($student)
            ->get(route('challenges.map', self::SLUG))
            ->assertOk()
            ->assertDontSee('Instructor Week 3 Quiz');
        $this->actingAsUser($student)
            ->get(route('student.assignments.index'))
            ->assertOk()
            ->assertDontSee('Instructor Week 3 Quiz');
    }

    public function test_assignments_page_lists_open_instructor_challenges_with_take_links(): void
    {
        [$university, $instructor, $class, $student] = $this->scenario();
        $outsider = $this->makeEnrolledStudent($this->makeClass($this->makeUser(User::ROLE_INSTRUCTOR), 'Other Class'));

        $quiz = $this->makeChallenge($university, 'Instructor Week 3 Quiz', false, true, $instructor);
        $coding = $this->makeChallenge($university, 'Instructor Coding Problem', true, true, $instructor);
        $pool = $this->makeChallenge($university, 'Pool Quiz Given To Class', false, true);

        $this->give($class, $quiz, $instructor, ['title' => 'Week 3 quiz', 'due_at' => now()->addDays(2), 'instructions' => 'One sitting, please.']);
        $this->give($class, $coding, $instructor);
        $this->give($class, $pool, $instructor, ['status' => 'draft']);

        $this->actingAsUser($student)
            ->get(route('student.assignments.index'))
            ->assertOk()
            ->assertSee('Challenges from your instructor')
            ->assertSee('Week 3 quiz')
            ->assertSee('One sitting, please.')
            ->assertSee('Instructor Coding Problem')
            ->assertDontSee('Pool Quiz Given To Class')
            ->assertSee('Quiz')
            ->assertSee('Coding')
            ->assertSee(route('challenges.quiz', ['slug' => self::SLUG, 'challenge' => $quiz->id]), false)
            ->assertSee(route('challenges.coding.quiz', ['slug' => self::SLUG, 'challenge' => $coding->id]), false);

        $this->actingAsUser($outsider)
            ->get(route('student.assignments.index'))
            ->assertOk()
            ->assertSee('Challenges from your instructor')
            ->assertSee('No challenges from your instructor right now')
            ->assertDontSee('Week 3 quiz')
            ->assertDontSee('Instructor Coding Problem');
    }

    public function test_instructor_challenges_never_count_toward_level_completion(): void
    {
        [$university, $instructor, $class, $student] = $this->scenario();
        $intermediate = $this->makeCategory('Intermediate', 'intermediate', 3);

        $platformMcq = $this->makeChallenge($university, 'Platform Stats Quiz', false, true);
        $platformCoding = $this->makeChallenge($university, 'Platform Coding Problem', true, true);
        $instructorMcq = $this->makeChallenge($university, 'Instructor Week 3 Quiz', false, true, $instructor);
        $instructorCoding = $this->makeChallenge($university, 'Instructor Coding Problem', true, true, $instructor);
        $this->give($class, $instructorMcq, $instructor);
        $this->give($class, $instructorCoding, $instructor);

        // The student finished every platform item on the level and none of
        // the instructor's class work.
        ChallengeAttempt::create([
            'user_id' => $student->id,
            'challenge_id' => $platformMcq->id,
            'attempt_no' => 1,
            'mode' => 'ranked',
            'status' => 'submitted',
            'started_at' => now()->subMinutes(10),
            'expires_at' => now()->subMinutes(5),
            'time_limit_seconds' => 600,
            'time_taken_seconds' => 120,
            'score' => 1,
            'total_questions' => 1,
            'is_ranked' => true,
        ]);
        CodingSubmission::create([
            'user_id' => $student->id,
            'coding_question_id' => $platformCoding->codingQuestions()->first()->id,
            'code' => 'print(42)',
            'language' => 'python',
            'status' => 'passed',
            'tests_passed' => 1,
            'tests_total' => 1,
            'xp_earned' => 100,
            'time_taken_seconds' => 60,
            'voided' => false,
        ]);

        $service = app(ChallengePathUnlockService::class);

        $mcq = $service->performanceSummary($student, self::SLUG, 'mcq');
        $this->assertSame(1, $mcq['total_items']);
        $this->assertSame(1, $mcq['completed_items']);
        $this->assertTrue($mcq['completed']);

        $coding = $service->performanceSummary($student, self::SLUG, 'coding');
        $this->assertSame(1, $coding['total_items']);
        $this->assertSame(1, $coding['completed_items']);
        $this->assertTrue($coding['completed']);

        $this->assertTrue($service->lockInfo($student, 'intermediate', 'mcq')['unlocked']);
        $this->assertTrue($service->lockInfo($student, 'intermediate', 'coding')['unlocked']);
    }

    // ── Fixtures ──────────────────────────────────────────────────────

    /**
     * @return array{0: ChallengeCategory, 1: User, 2: ClassRoom, 3: User}
     */
    private function scenario(): array
    {
        $this->makeCategory('Newbie', 'newbie', 1);
        $university = $this->makeCategory('University Student', self::SLUG, 2);
        $instructor = $this->makeUser(User::ROLE_INSTRUCTOR);
        $class = $this->makeClass($instructor);
        $student = $this->makeEnrolledStudent($class);

        return [$university, $instructor, $class, $student];
    }

    private function give(ClassRoom $class, Challenge $challenge, User $instructor, array $overrides = []): ClassChallengeAssignment
    {
        return ClassChallengeAssignment::create(array_merge([
            'class_id' => $class->id,
            'challenge_id' => $challenge->id,
            'assigned_by' => $instructor->id,
            'title' => $challenge->title,
            'status' => 'published',
            'available_at' => null,
            'due_at' => null,
        ], $overrides));
    }

    private function makeEnrolledStudent(ClassRoom $class): User
    {
        $student = $this->makeUser(User::ROLE_USER);
        $class->students()->attach($student->id, ['enrolled_at' => now()]);

        return $student;
    }

    private function makeCategory(string $name, string $slug, int $order): ChallengeCategory
    {
        return ChallengeCategory::create([
            'name' => $name,
            'slug' => $slug,
            'target_audience' => 'Students',
            'description' => $name . ' challenges',
            'order_index' => $order,
        ]);
    }

    private function makeChallenge(ChallengeCategory $category, string $title, bool $coding, bool $active, ?User $owner = null): Challenge
    {
        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title' => $title,
            'description' => 'desc',
            'time_limit_seconds' => 600,
            'base_xp' => 100,
            'order_index' => $owner ? 0 : 1,
            'is_coding_challenge' => $coding,
            'is_active' => $active,
            'visibility' => $owner ? Challenge::VISIBILITY_INSTRUCTOR : Challenge::VISIBILITY_PLATFORM,
            'created_by' => $owner?->id,
            'content_code' => 'T-' . Str::upper(Str::random(10)),
        ]);

        if ($coding) {
            $question = $challenge->codingQuestions()->create([
                'problem_description' => 'Print 42.', 'language' => 'python', 'time_limit_seconds' => 600, 'base_xp' => 100, 'order_index' => 1,
            ]);
            $question->testCases()->create(['input' => null, 'expected_output' => '42', 'is_hidden' => false, 'order_index' => 1]);
        } else {
            $question = $challenge->questions()->create([
                'challenge_category_id' => $category->id, 'question_text' => 'Question?', 'order_index' => 1,
            ]);
            $question->options()->create(['option_text' => 'Yes', 'is_correct' => true, 'order_index' => 1]);
            $question->options()->create(['option_text' => 'No', 'is_correct' => false, 'order_index' => 2]);
        }

        return $challenge;
    }

    private function makeClass(User $instructor, string $name = 'Data Science 101'): ClassRoom
    {
        return ClassRoom::create([
            'instructor_id' => $instructor->id,
            'name' => $name,
            'section' => 'A',
            'is_archived' => false,
        ]);
    }

    /** Instructors need an active institution to pass the `active` middleware. */
    private function makeUser(int $role): User
    {
        $attributes = [
            'name' => 'Updates3 User',
            'email' => 'updates3-' . Str::lower(Str::random(10)) . '@example.test',
            'password' => bcrypt('Secret!2026'),
            'role' => $role,
            'status' => 'active',
        ];

        if ($role === User::ROLE_INSTRUCTOR) {
            $this->institution ??= Institution::create([
                'name' => 'Updates3 Institution',
                'email' => 'updates3-' . Str::lower(Str::random(6)) . '@institution.test',
                'status' => 'active',
            ]);
            $attributes['institution_id'] = $this->institution->id;
        }

        return User::create($attributes);
    }

    private function actingAsUser(User $user)
    {
        return $this->actingAs($user)->withSession([
            AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($user),
        ]);
    }
}
