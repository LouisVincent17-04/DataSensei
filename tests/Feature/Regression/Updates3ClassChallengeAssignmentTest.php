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
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Updates 3, D2: giving a challenge to a class. An instructor may give one of
 * their own challenges, or an available University Student platform
 * challenge, to one of their own classes, once per class; only the owner of
 * the class can change or remove the entry.
 */
class Updates3ClassChallengeAssignmentTest extends TestCase
{
    use RefreshDatabase;

    private ?Institution $institution = null;

    public function test_index_lists_my_classes_with_their_challenge_entries_and_the_add_form(): void
    {
        $university = $this->makeUniversityCategory();
        $instructor = $this->makeUser(User::ROLE_INSTRUCTOR);
        $other = $this->makeUser(User::ROLE_INSTRUCTOR);

        $mine = $this->makeClass($instructor, 'Data Science 101');
        $archived = $this->makeClass($instructor, 'Old Cohort', true);
        $theirs = $this->makeClass($other, 'Someone Else Class');

        $own = $this->makeChallenge($university, 'My Quiz', false, true, $instructor);
        $pool = $this->makeChallenge($university, 'Pool Coding Problem', true, true);
        $inactivePool = $this->makeChallenge($university, 'Unavailable Pool Quiz', false, false);
        $otherOwn = $this->makeChallenge($university, 'Other Instructor Quiz', false, true, $other);

        ClassChallengeAssignment::create([
            'class_id' => $mine->id, 'challenge_id' => $own->id, 'assigned_by' => $instructor->id,
            'title' => 'Week 3 quiz', 'status' => 'published', 'due_at' => now()->addDays(3),
        ]);
        ClassChallengeAssignment::create([
            'class_id' => $mine->id, 'challenge_id' => $pool->id, 'assigned_by' => $instructor->id,
            'title' => 'Pool Coding Problem', 'status' => 'draft',
        ]);
        ClassChallengeAssignment::create([
            'class_id' => $theirs->id, 'challenge_id' => $otherOwn->id, 'assigned_by' => $other->id,
            'title' => 'Not mine entry', 'status' => 'published',
        ]);

        $this->actingAsUser($instructor)
            ->get(route('instructor.class-challenges.index'))
            ->assertOk()
            ->assertSee('Class Challenges')
            ->assertSee('Data Science 101')
            ->assertDontSee('Old Cohort')
            ->assertDontSee('Someone Else Class')
            ->assertSee('Week 3 quiz')
            ->assertSee('Open now')
            ->assertSee('Hidden (draft)')
            ->assertDontSee('Not mine entry')
            ->assertSee('My Quiz (Quiz)')
            ->assertSee('Pool Coding Problem (Coding)')
            ->assertDontSee('Unavailable Pool Quiz')
            ->assertDontSee('Other Instructor Quiz')
            ->assertSee(route('instructor.class-challenges.store'), false)
            ->assertSee(route('instructor.challenge-builder.index'), false);
    }

    public function test_store_gives_my_challenge_to_my_class(): void
    {
        $university = $this->makeUniversityCategory();
        $instructor = $this->makeUser(User::ROLE_INSTRUCTOR);
        $class = $this->makeClass($instructor);
        $own = $this->makeChallenge($university, 'My Quiz', false, true, $instructor);

        $this->actingAsUser($instructor)
            ->post(route('instructor.class-challenges.store'), [
                'class_id' => $class->id,
                'challenge_id' => $own->id,
                'title' => '',
                'instructions' => 'Finish before Friday.',
                'available_at' => '2026-10-01T08:00',
                'due_at' => '2026-10-08T23:59',
                'status' => 'published',
            ])
            ->assertRedirect(route('instructor.class-challenges.index'))
            ->assertSessionHas('success');

        $row = ClassChallengeAssignment::query()->firstOrFail();
        $this->assertSame((int) $class->id, (int) $row->class_id);
        $this->assertSame((int) $own->id, (int) $row->challenge_id);
        $this->assertSame((int) $instructor->id, (int) $row->assigned_by);
        $this->assertSame('My Quiz', $row->title, 'An empty title falls back to the challenge title.');
        $this->assertSame('Finish before Friday.', $row->instructions);
        $this->assertSame('2026-10-01 08:00', $row->available_at->format('Y-m-d H:i'));
        $this->assertSame('2026-10-08 23:59', $row->due_at->format('Y-m-d H:i'));
        $this->assertSame('published', $row->status);
    }

    public function test_store_allows_the_platform_pool_but_rejects_other_levels_inactive_and_foreign_challenges(): void
    {
        $university = $this->makeUniversityCategory();
        $newbie = $this->makeCategory('Newbie', 'newbie', 1);
        $instructor = $this->makeUser(User::ROLE_INSTRUCTOR);
        $other = $this->makeUser(User::ROLE_INSTRUCTOR);
        $class = $this->makeClass($instructor);

        $pool = $this->makeChallenge($university, 'Pool Coding Problem', true, true);
        $newbiePool = $this->makeChallenge($newbie, 'Newbie Quiz', false, true);
        $inactivePool = $this->makeChallenge($university, 'Unavailable Pool Quiz', false, false);
        $otherOwn = $this->makeChallenge($university, 'Other Instructor Quiz', false, true, $other);

        $this->actingAsUser($instructor)
            ->post(route('instructor.class-challenges.store'), $this->payload($class, $pool))
            ->assertRedirect(route('instructor.class-challenges.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('class_challenge_assignments', ['class_id' => $class->id, 'challenge_id' => $pool->id]);

        foreach ([$newbiePool, $inactivePool, $otherOwn] as $rejected) {
            $this->actingAsUser($instructor)
                ->from(route('instructor.class-challenges.index'))
                ->post(route('instructor.class-challenges.store'), $this->payload($class, $rejected))
                ->assertRedirect(route('instructor.class-challenges.index'))
                ->assertSessionHasErrors(['challenge_id']);
        }

        $this->actingAsUser($instructor)
            ->from(route('instructor.class-challenges.index'))
            ->post(route('instructor.class-challenges.store'), $this->payload($class, $pool, ['challenge_id' => 999999]))
            ->assertSessionHasErrors(['challenge_id']);

        $this->assertSame(1, ClassChallengeAssignment::count());
    }

    public function test_store_rejects_someone_elses_or_archived_class(): void
    {
        $university = $this->makeUniversityCategory();
        $instructor = $this->makeUser(User::ROLE_INSTRUCTOR);
        $other = $this->makeUser(User::ROLE_INSTRUCTOR);
        $theirs = $this->makeClass($other);
        $archived = $this->makeClass($instructor, 'Old Cohort', true);
        $own = $this->makeChallenge($university, 'My Quiz', false, true, $instructor);

        $this->actingAsUser($instructor)
            ->post(route('instructor.class-challenges.store'), $this->payload($theirs, $own))
            ->assertNotFound();

        $this->actingAsUser($instructor)
            ->post(route('instructor.class-challenges.store'), $this->payload($archived, $own))
            ->assertNotFound();

        $this->assertSame(0, ClassChallengeAssignment::count());
    }

    public function test_store_rejects_duplicates_and_validates_dates_and_status(): void
    {
        $university = $this->makeUniversityCategory();
        $instructor = $this->makeUser(User::ROLE_INSTRUCTOR);
        $class = $this->makeClass($instructor);
        $own = $this->makeChallenge($university, 'My Quiz', false, true, $instructor);

        $this->actingAsUser($instructor)
            ->post(route('instructor.class-challenges.store'), $this->payload($class, $own))
            ->assertRedirect(route('instructor.class-challenges.index'))
            ->assertSessionHas('success');

        $this->actingAsUser($instructor)
            ->from(route('instructor.class-challenges.index'))
            ->post(route('instructor.class-challenges.store'), $this->payload($class, $own))
            ->assertRedirect(route('instructor.class-challenges.index'))
            ->assertSessionHasErrors(['challenge_id']);

        $this->actingAsUser($instructor)
            ->from(route('instructor.class-challenges.index'))
            ->post(route('instructor.class-challenges.store'), $this->payload($class, $own, [
                'available_at' => '2026-10-08T10:00',
                'due_at' => '2026-10-01T10:00',
                'status' => 'archived',
            ]))
            ->assertSessionHasErrors(['due_at', 'status']);

        $this->assertSame(1, ClassChallengeAssignment::count());
    }

    public function test_update_changes_status_dates_title_and_instructions_for_the_owner_only(): void
    {
        $university = $this->makeUniversityCategory();
        $instructor = $this->makeUser(User::ROLE_INSTRUCTOR);
        $other = $this->makeUser(User::ROLE_INSTRUCTOR);
        $class = $this->makeClass($instructor);
        $own = $this->makeChallenge($university, 'My Quiz', false, true, $instructor);

        $entry = ClassChallengeAssignment::create([
            'class_id' => $class->id, 'challenge_id' => $own->id, 'assigned_by' => $instructor->id,
            'title' => 'My Quiz', 'status' => 'draft',
        ]);

        $this->actingAsUser($other)
            ->put(route('instructor.class-challenges.update', $entry), ['status' => 'published'])
            ->assertNotFound();
        $this->actingAsUser($this->makeUser(User::ROLE_USER))
            ->put(route('instructor.class-challenges.update', $entry), ['status' => 'published'])
            ->assertForbidden();
        $this->assertSame('draft', $entry->fresh()->status);

        $this->actingAsUser($instructor)
            ->put(route('instructor.class-challenges.update', $entry), [
                'status' => 'published',
                'title' => 'Week 3 quiz',
                'instructions' => 'One attempt counts.',
                'available_at' => '',
                'due_at' => '2026-11-01T18:00',
                // Never accepted on update.
                'class_id' => $this->makeClass($other)->id,
                'challenge_id' => 999,
            ])
            ->assertRedirect(route('instructor.class-challenges.index'))
            ->assertSessionHas('success');

        $entry->refresh();
        $this->assertSame('published', $entry->status);
        $this->assertSame('Week 3 quiz', $entry->title);
        $this->assertSame('One attempt counts.', $entry->instructions);
        $this->assertNull($entry->available_at);
        $this->assertSame('2026-11-01 18:00', $entry->due_at->format('Y-m-d H:i'));
        $this->assertSame((int) $class->id, (int) $entry->class_id);
        $this->assertSame((int) $own->id, (int) $entry->challenge_id);

        $this->actingAsUser($instructor)
            ->from(route('instructor.class-challenges.index'))
            ->put(route('instructor.class-challenges.update', $entry), ['status' => 'closed', 'available_at' => '2026-12-01T00:00', 'due_at' => '2026-11-01T00:00'])
            ->assertSessionHasErrors(['due_at']);

        $this->actingAsUser($instructor)
            ->put(route('instructor.class-challenges.update', $entry), ['status' => 'closed'])
            ->assertRedirect(route('instructor.class-challenges.index'));

        $this->assertSame('closed', $entry->fresh()->status);
    }

    public function test_destroy_removes_the_entry_for_the_owner_only(): void
    {
        $university = $this->makeUniversityCategory();
        $instructor = $this->makeUser(User::ROLE_INSTRUCTOR);
        $other = $this->makeUser(User::ROLE_INSTRUCTOR);
        $class = $this->makeClass($instructor);
        $own = $this->makeChallenge($university, 'My Quiz', false, true, $instructor);

        $entry = ClassChallengeAssignment::create([
            'class_id' => $class->id, 'challenge_id' => $own->id, 'assigned_by' => $instructor->id,
            'title' => 'My Quiz', 'status' => 'published',
        ]);

        $this->actingAsUser($other)->delete(route('instructor.class-challenges.destroy', $entry))->assertNotFound();
        $this->actingAsUser($this->makeUser(User::ROLE_ADMIN))->delete(route('instructor.class-challenges.destroy', $entry))->assertForbidden();
        $this->assertDatabaseHas('class_challenge_assignments', ['id' => $entry->id]);

        $this->actingAsUser($instructor)
            ->delete(route('instructor.class-challenges.destroy', $entry))
            ->assertRedirect(route('instructor.class-challenges.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('class_challenge_assignments', ['id' => $entry->id]);
        // Removing the class entry never deletes the challenge itself.
        $this->assertDatabaseHas('challenges', ['id' => $own->id]);
    }

    public function test_students_cannot_reach_the_class_challenge_pages(): void
    {
        $this->makeUniversityCategory();
        $student = $this->makeUser(User::ROLE_USER);

        $this->get(route('instructor.class-challenges.index'))->assertRedirect(route('login'));
        $this->actingAsUser($student)->get(route('instructor.class-challenges.index'))->assertForbidden();
        $this->actingAsUser($student)->post(route('instructor.class-challenges.store'), ['class_id' => 1, 'challenge_id' => 1, 'status' => 'published'])->assertForbidden();
    }

    // ── Fixtures ──────────────────────────────────────────────────────

    private function payload(ClassRoom $class, Challenge $challenge, array $overrides = []): array
    {
        return array_merge([
            'class_id' => $class->id,
            'challenge_id' => $challenge->id,
            'title' => '',
            'instructions' => '',
            'available_at' => '',
            'due_at' => '',
            'status' => 'published',
        ], $overrides);
    }

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
