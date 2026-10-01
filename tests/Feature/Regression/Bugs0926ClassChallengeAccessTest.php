<?php

namespace Tests\Feature\Regression;

use App\Models\Challenge;
use App\Models\ChallengeCategory;
use App\Models\ClassChallengeAssignment;
use App\Models\ClassRoom;
use App\Models\Institution;
use App\Models\Notification;
use App\Models\User;
use App\Support\AuthSessionFingerprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Reported 2026-09-26 (bugs2.pdf, Error.docx): a challenge an instructor gave
 * to a class showed "Opens later" after its start time, appeared on the
 * student dashboard but not in the challenge pages, and was locked on the
 * student's map.
 */
class Bugs0926ClassChallengeAccessTest extends TestCase
{
    use RefreshDatabase;

    private const SLUG = 'university-student';

    private ?Institution $institution = null;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_the_application_clock_is_philippine_time_unless_configured(): void
    {
        if (env('APP_TIMEZONE') !== null) {
            $this->markTestSkipped('APP_TIMEZONE is set in this environment.');
        }

        $this->assertSame('Asia/Manila', config('app.timezone'));
        $this->assertSame('Asia/Manila', date_default_timezone_get());
    }

    public function test_a_start_time_saved_in_local_time_opens_at_that_local_time(): void
    {
        // Rows given before DataSensei Updates 9 can still carry a window.
        [$university, $instructor, $class, $student] = $this->scenario();
        $quiz = $this->makeChallenge($university, 'Quick Easy Quiz', false, true, $instructor);

        // 4:05 PM in Manila; the window is 4:00 PM to 6:15 PM local time.
        Carbon::setTestNow(Carbon::parse('2026-09-26 16:05:00', 'Asia/Manila'));
        $entry = $this->give($class, $quiz, $instructor, [
            'available_at' => Carbon::parse('2026-09-26 16:00:00', 'Asia/Manila'),
            'due_at' => Carbon::parse('2026-09-26 18:15:00', 'Asia/Manila'),
        ]);

        $this->assertTrue($entry->fresh()->isOpenNow(), 'The window started five minutes ago in local time.');

        $this->actingAsUser($student)
            ->get(route('challenges.map', self::SLUG))
            ->assertOk()
            ->assertSee('Quick Easy Quiz');
    }

    public function test_class_challenge_on_the_mcq_map_is_open_while_the_path_is_unfinished(): void
    {
        [$university, $instructor, $class, $student] = $this->scenario();
        $this->makeChallenge($university, 'Platform Quiz One', false, true);
        $this->makeChallenge($university, 'Platform Quiz Two', false, true);
        $mine = $this->makeChallenge($university, 'Prelim Quiz', false, true, $instructor);
        $this->give($class, $mine, $instructor);

        $html = $this->actingAsUser($student)
            ->get(route('challenges.map', self::SLUG))
            ->assertOk()
            ->assertSee('Class challenge')
            ->getContent();

        $this->assertSame('active', $this->nodeState($html, 'Platform Quiz One'));
        $this->assertSame('locked', $this->nodeState($html, 'Platform Quiz Two'), 'Platform order is unchanged.');
        $this->assertSame('active', $this->nodeState($html, 'Prelim Quiz'), 'The class challenge must not be locked.');
    }

    public function test_class_challenge_on_the_coding_map_is_open_while_the_path_is_unfinished(): void
    {
        [$university, $instructor, $class, $student] = $this->scenario();
        $this->makeChallenge($university, 'Platform Coding One', true, true);
        $this->makeChallenge($university, 'Platform Coding Two', true, true);
        $mine = $this->makeChallenge($university, 'PRELIM EXAM', true, true, $instructor);
        $this->give($class, $mine, $instructor);

        $html = $this->actingAsUser($student)
            ->get(route('challenges.coding.map', self::SLUG))
            ->assertOk()
            ->getContent();

        $this->assertSame('active', $this->nodeState($html, 'Platform Coding One'));
        $this->assertSame('locked', $this->nodeState($html, 'Platform Coding Two'));
        $this->assertSame('active', $this->nodeState($html, 'PRELIM EXAM'));

        $this->actingAsUser($student)
            ->get(route('challenges.coding.quiz', ['slug' => self::SLUG, 'challenge' => $mine->id]))
            ->assertOk();
    }

    public function test_challenge_pages_list_what_the_class_was_given(): void
    {
        [$university, $instructor, $class, $student] = $this->scenario();
        $coding = $this->makeChallenge($university, 'PRELIM EXAM', true, true, $instructor);
        $quiz = $this->makeChallenge($university, 'Quick Easy Quiz', false, true);
        $hidden = $this->makeChallenge($university, 'Draft Exam', true, true, $instructor);
        $this->give($class, $coding, $instructor);
        $this->give($class, $quiz, $instructor);
        $this->give($class, $hidden, $instructor, ['status' => 'closed']);

        // Shared challenges are practice (DataSensei Updates 9): the challenge's
        // own title, no due date.
        $this->actingAsUser($student)
            ->get(route('challenges.coding'))
            ->assertOk()
            ->assertSee('From your classes')
            ->assertSee('PRELIM EXAM')
            ->assertSee(route('challenges.coding.quiz', ['slug' => self::SLUG, 'challenge' => $coding->id]), false)
            ->assertDontSee('Draft Exam')
            ->assertDontSee('Quick Easy Quiz')
            ->assertDontSee('No due date');

        $this->actingAsUser($student)
            ->get(route('challenges'))
            ->assertOk()
            ->assertSee('From your classes')
            ->assertSee('Quick Easy Quiz')
            ->assertSee(route('challenges.quiz', ['slug' => self::SLUG, 'challenge' => $quiz->id]), false)
            ->assertDontSee('PRELIM EXAM');

        $outsider = $this->makeEnrolledStudent($this->makeClass($this->makeUser(User::ROLE_INSTRUCTOR), 'Other Class'));
        $this->actingAsUser($outsider)
            ->get(route('challenges.coding'))
            ->assertOk()
            ->assertDontSee('From your classes')
            ->assertDontSee('PRELIM EXAM');
    }

    public function test_dashboard_deadlines_leave_out_practice_challenges(): void
    {
        // Challenges shared with a class have no due date since DataSensei
        // Updates 9, so the deadline list shows assignments and assessments only.
        [$university, $instructor, $class, $student] = $this->scenario();
        $given = $this->makeChallenge($university, 'Given Coding Exam', true, true, $instructor);
        $this->give($class, $given, $instructor, ['due_at' => now()->addHours(5)]);

        $this->actingAsUser($student)
            ->get(route('studentDashboard'))
            ->assertOk()
            ->assertDontSee('Coding challenge for Data Science 101');
    }

    public function test_sharing_a_challenge_with_a_class_notifies_it_with_a_direct_link(): void
    {
        [$university, $instructor, $class, $student] = $this->scenario();
        $coding = $this->makeChallenge($university, 'PRELIM EXAM', true, true, $instructor);

        $this->actingAsUser($instructor)
            ->from(route('instructor.challenge-builder.edit', $coding))
            ->put(route('instructor.challenges.classes.update', $coding), ['class_ids' => [$class->id]])
            ->assertRedirect(route('instructor.challenge-builder.edit', $coding).'#classes');

        $entry = ClassChallengeAssignment::query()->firstOrFail();
        $this->assertSame('published', $entry->status);
        $this->assertNull($entry->due_at);

        $notice = Notification::where('user_id', $student->id)->firstOrFail();
        $this->assertSame('class_challenge_published', $notice->type);
        $this->assertStringContainsString('PRELIM EXAM', $notice->notification_text);
        $this->assertStringContainsString('/challenges/coding/'.self::SLUG.'/challenge/'.$coding->id, (string) $notice->action_url);

        // Saving the same classes again announces nothing new.
        $this->actingAsUser($instructor)
            ->put(route('instructor.challenges.classes.update', $coding), ['class_ids' => [$class->id]])
            ->assertRedirect();
        $this->assertSame(1, Notification::where('user_id', $student->id)->count());
    }

    // ── Fixtures ──────────────────────────────────────────────────────

    private function nodeState(string $html, string $title): ?string
    {
        $position = strpos($html, '>'.$title.'<');
        $this->assertNotFalse($position, $title.' is not on the map.');

        $before = substr($html, 0, $position);
        preg_match_all('/challenge-map-node state-([a-z]+)/', $before, $matches);

        return end($matches[1]) ?: null;
    }

    /** @return array{0: ChallengeCategory, 1: User, 2: ClassRoom, 3: User} */
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
            'description' => $name.' challenges',
            'order_index' => $order,
        ]);
    }

    private function makeChallenge(ChallengeCategory $category, string $title, bool $coding, bool $active, ?User $owner = null): Challenge
    {
        static $order = 0;
        $order++;

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title' => $title,
            'description' => 'desc',
            'time_limit_seconds' => 600,
            'base_xp' => 100,
            'order_index' => $order,
            'is_coding_challenge' => $coding,
            'is_active' => $active,
            'visibility' => $owner ? Challenge::VISIBILITY_INSTRUCTOR : Challenge::VISIBILITY_PLATFORM,
            'created_by' => $owner?->id,
            'content_code' => 'T-'.Str::upper(Str::random(10)),
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

    private function makeUser(int $role): User
    {
        $attributes = [
            'name' => 'Bugs0926 User',
            'email' => 'bugs0926-'.Str::lower(Str::random(10)).'@example.test',
            'password' => bcrypt('Secret!2026'),
            'role' => $role,
            'status' => 'active',
        ];

        if ($role === User::ROLE_INSTRUCTOR) {
            $this->institution ??= Institution::create([
                'name' => 'Bugs0926 Institution',
                'email' => 'bugs0926-'.Str::lower(Str::random(6)).'@institution.test',
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
