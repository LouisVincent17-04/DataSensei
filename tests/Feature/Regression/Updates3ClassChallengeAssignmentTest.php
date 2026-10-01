<?php

namespace Tests\Feature\Regression;

use App\Models\Challenge;
use App\Models\ChallengeCategory;
use App\Models\ClassChallengeAssignment;
use App\Models\ClassRoom;
use App\Models\Institution;
use App\Models\User;
use App\Support\AuthSessionFingerprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Challenges shared with a class for practice (DataSensei Updates 9; this
 * replaced the Updates 3 "Class Challenges" page, which duplicated
 * assignments with its own titles, due dates and draft/published/closed
 * status). An instructor shares one of their own challenges, or an
 * available University Student platform challenge, with their own active
 * classes. Nothing is due and nothing is graded.
 */
class Updates3ClassChallengeAssignmentTest extends TestCase
{
    use RefreshDatabase;

    private ?Institution $institution = null;

    public function test_the_class_challenges_page_is_gone(): void
    {
        foreach (['index', 'store', 'update', 'destroy'] as $name) {
            $this->assertFalse(Route::has('instructor.class-challenges.'.$name));
        }
        $this->assertFileDoesNotExist(app_path('Http/Controllers/InstructorClassChallengeController.php'));
        $this->assertFileDoesNotExist(resource_path('views/instructor/class-challenges/index.blade.php'));

        $this->makeUniversityCategory();
        $this->actingAsUser($this->makeUser(User::ROLE_INSTRUCTOR))
            ->get(route('instructor.dashboard'))
            ->assertOk()
            ->assertDontSee('Class Challenges');
    }

    public function test_the_builder_edit_page_lists_my_active_classes_to_share_with(): void
    {
        $university = $this->makeUniversityCategory();
        $instructor = $this->makeUser(User::ROLE_INSTRUCTOR);
        $other = $this->makeUser(User::ROLE_INSTRUCTOR);
        $mine = $this->makeClass($instructor, 'Data Science 101');
        $this->makeClass($instructor, 'Old Cohort', true);
        $this->makeClass($other, 'Someone Else Class');
        $own = $this->makeChallenge($university, 'My Quiz', false, true, $instructor);

        $html = $this->actingAsUser($instructor)
            ->get(route('instructor.challenge-builder.edit', $own))
            ->assertOk()
            ->assertSee('Classes that can practice this')
            ->assertSee('Data Science 101')
            ->assertDontSee('Old Cohort')
            ->assertDontSee('Someone Else Class')
            ->assertSee(route('instructor.challenges.classes.update', $own), false)
            ->getContent();

        $this->assertStringNotContainsString('name="due_at"', $html);
        $this->assertStringNotContainsString('name="available_at"', $html);
        $this->assertSame(1, substr_count($html, 'name="class_ids[]" value="'.$mine->id.'"'));
    }

    public function test_sharing_my_challenge_opens_it_for_the_class_without_a_due_date(): void
    {
        $university = $this->makeUniversityCategory();
        $instructor = $this->makeUser(User::ROLE_INSTRUCTOR);
        $class = $this->makeClass($instructor);
        $own = $this->makeChallenge($university, 'My Quiz', false, true, $instructor);

        $this->actingAsUser($instructor)
            ->from(route('instructor.challenge-builder.edit', $own))
            ->put(route('instructor.challenges.classes.update', $own), ['class_ids' => [$class->id]])
            ->assertRedirect(route('instructor.challenge-builder.edit', $own).'#classes')
            ->assertSessionHas('success', 'Shared with Data Science 101.');

        $entry = ClassChallengeAssignment::query()->firstOrFail();
        $this->assertSame([$class->id, $own->id, 'published', null, null, null], [
            (int) $entry->class_id, (int) $entry->challenge_id, $entry->status, $entry->due_at, $entry->available_at, $entry->title,
        ]);
        $this->assertTrue($entry->isOpenNow());
    }

    public function test_the_platform_pool_can_be_shared_but_not_other_levels_inactive_or_foreign_challenges(): void
    {
        $university = $this->makeUniversityCategory();
        $newbie = $this->makeCategory('Newbie', 'newbie', 1);
        $instructor = $this->makeUser(User::ROLE_INSTRUCTOR);
        $other = $this->makeUser(User::ROLE_INSTRUCTOR);
        $class = $this->makeClass($instructor);

        $pool = $this->makeChallenge($university, 'Pool Coding Problem', true, true);
        $this->actingAsUser($instructor)
            ->put(route('instructor.challenges.classes.update', $pool), ['class_ids' => [$class->id]])
            ->assertRedirect();
        $this->assertDatabaseHas('class_challenge_assignments', ['class_id' => $class->id, 'challenge_id' => $pool->id, 'status' => 'published']);

        foreach ([
            $this->makeChallenge($newbie, 'Newbie Quiz', false, true),
            $this->makeChallenge($university, 'Unavailable Pool Quiz', false, false),
            $this->makeChallenge($university, 'Other Instructor Quiz', false, true, $other),
        ] as $rejected) {
            $this->actingAsUser($instructor)
                ->put(route('instructor.challenges.classes.update', $rejected), ['class_ids' => [$class->id]])
                ->assertNotFound();
            $this->assertDatabaseMissing('class_challenge_assignments', ['challenge_id' => $rejected->id]);
        }
    }

    public function test_someone_elses_or_archived_class_is_ignored(): void
    {
        $university = $this->makeUniversityCategory();
        $instructor = $this->makeUser(User::ROLE_INSTRUCTOR);
        $other = $this->makeUser(User::ROLE_INSTRUCTOR);
        $theirs = $this->makeClass($other, 'Someone Else Class');
        $archived = $this->makeClass($instructor, 'Old Cohort', true);
        $own = $this->makeChallenge($university, 'My Quiz', false, true, $instructor);

        $this->actingAsUser($instructor)
            ->put(route('instructor.challenges.classes.update', $own), ['class_ids' => [$theirs->id, $archived->id]])
            ->assertRedirect()
            ->assertSessionHas('success', 'No change to the classes.');

        $this->assertSame(0, ClassChallengeAssignment::count());
    }

    public function test_unsharing_keeps_the_history_and_sharing_again_reopens_it(): void
    {
        $university = $this->makeUniversityCategory();
        $instructor = $this->makeUser(User::ROLE_INSTRUCTOR);
        $class = $this->makeClass($instructor);
        $own = $this->makeChallenge($university, 'My Quiz', false, true, $instructor);
        // An entry from the old page, with a window and a custom title.
        ClassChallengeAssignment::create([
            'class_id' => $class->id, 'challenge_id' => $own->id, 'assigned_by' => $instructor->id,
            'title' => 'Week 3 quiz', 'status' => 'published', 'due_at' => now()->addDays(3),
        ]);

        $this->actingAsUser($instructor)
            ->put(route('instructor.challenges.classes.update', $own), ['class_ids' => []])
            ->assertSessionHas('success', 'No longer shared with Data Science 101.');
        $entry = ClassChallengeAssignment::query()->sole();
        $this->assertSame('closed', $entry->status, 'Kept, so the class reports still count the attempts.');
        $this->assertFalse($entry->isOpenNow());

        $this->actingAsUser($instructor)
            ->put(route('instructor.challenges.classes.update', $own), ['class_ids' => [$class->id]])
            ->assertSessionHas('success', 'Shared with Data Science 101.');
        $entry = ClassChallengeAssignment::query()->sole();
        $this->assertSame(['published', null, null], [$entry->status, $entry->due_at, $entry->title]);
        $this->assertDatabaseHas('challenges', ['id' => $own->id]);
    }

    public function test_students_and_guests_cannot_share(): void
    {
        $university = $this->makeUniversityCategory();
        $instructor = $this->makeUser(User::ROLE_INSTRUCTOR);
        $class = $this->makeClass($instructor);
        $own = $this->makeChallenge($university, 'My Quiz', false, true, $instructor);
        $student = $this->makeUser(User::ROLE_USER);

        $this->put(route('instructor.challenges.classes.update', $own), ['class_ids' => [$class->id]])->assertRedirect(route('login'));
        $this->actingAsUser($student)->put(route('instructor.challenges.classes.update', $own), ['class_ids' => [$class->id]])->assertForbidden();
        $this->assertSame(0, ClassChallengeAssignment::count());
    }

    // ── Fixtures ──────────────────────────────────────────────────────

    private function makeUniversityCategory(): ChallengeCategory
    {
        return $this->makeCategory('University Student', 'university-student', 2);
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
            'order_index' => 1,
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

    private function makeClass(User $instructor, string $name = 'Data Science 101', bool $archived = false): ClassRoom
    {
        return ClassRoom::create([
            'instructor_id' => $instructor->id,
            'name' => $name,
            'section' => 'A',
            'is_archived' => $archived,
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
