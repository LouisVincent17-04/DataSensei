<?php

namespace Tests\Feature\Regression;

use App\Models\Challenge;
use App\Models\ChallengeAttempt;
use App\Models\ChallengeCategory;
use App\Models\ClassChallengeAssignment;
use App\Models\ClassRoom;
use App\Models\CodingQuestionAttempt;
use App\Models\Institution;
use App\Models\User;
use App\Services\CodingChallengeTestRunner;
use App\Support\AuthSessionFingerprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Updates 3, D1: the instructor challenge builder. An instructor builds MCQ
 * and coding challenges that live on the University Student level with
 * visibility = instructor; only the owner can edit or delete them, graded
 * content freezes once students have worked on them, and the reference
 * solution check and picture upload behave like the admin managers.
 */
class Updates3InstructorBuilderTest extends TestCase
{
    use RefreshDatabase;

    /** @var array<int, string> */
    private array $uploadedFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->uploadedFiles as $path) {
            if (is_file($path)) {
                @unlink($path);
            }
        }

        parent::tearDown();
    }

    // ── Pages ─────────────────────────────────────────────────────────

    public function test_index_lists_only_the_instructors_own_challenges_with_counts_and_links(): void
    {
        $category = $this->makeUniversityCategory();
        $instructor = $this->makeUser(User::ROLE_INSTRUCTOR);
        $other = $this->makeUser(User::ROLE_INSTRUCTOR);

        $mine = $this->makeInstructorMcq($category, $instructor, 'My Statistics Quiz', true);
        $mineCoding = $this->makeInstructorCoding($category, $instructor, 'My Sorting Problem', false);
        $theirs = $this->makeInstructorMcq($category, $other, 'Another Instructor Quiz', true);
        $platform = $this->makePlatformMcq($category, 'Platform Pool Quiz');

        $class = $this->makeClass($instructor);
        ClassChallengeAssignment::create([
            'class_id' => $class->id, 'challenge_id' => $mine->id, 'assigned_by' => $instructor->id,
            'title' => 'My Statistics Quiz', 'status' => 'published',
        ]);

        $this->actingAsUser($instructor)
            ->get(route('instructor.challenge-builder.index'))
            ->assertOk()
            ->assertSee('Challenge Builder')
            ->assertSee('My Statistics Quiz')
            ->assertSee('My Sorting Problem')
            ->assertDontSee('Another Instructor Quiz')
            ->assertDontSee('Platform Pool Quiz')
            ->assertSee('Available')
            ->assertSee('Unavailable')
            ->assertSee('1 class')
            ->assertSee('No class yet')
            ->assertSee(route('instructor.challenge-builder.edit', $mine), false)
            ->assertSee(route('instructor.challenge-builder.destroy', $mineCoding), false)
            ->assertSee(route('instructor.challenges.index'), false)
            ->assertSee(route('instructor.class-challenges.index'), false)
            ->assertSee(route('instructor.challenge-builder.create', ['type' => 'mcq']), false)
            ->assertSee(route('instructor.challenge-builder.create', ['type' => 'coding']), false);
    }

    public function test_create_pages_render_the_mcq_and_coding_editors_with_preview_upload_and_check(): void
    {
        $this->makeUniversityCategory();
        $instructor = $this->makeUser(User::ROLE_INSTRUCTOR);

        $this->actingAsUser($instructor)
            ->get(route('instructor.challenge-builder.create', ['type' => 'mcq']))
            ->assertOk()
            ->assertSee('New quiz challenge')
            ->assertSee('data-mcq-form', false)
            ->assertSee('data-mcq-preview', false)
            ->assertSee(route('instructor.challenge-builder.images.store'), false)
            ->assertSee('js/instructor-challenge-builder.js', false)
            ->assertSee('name="questions[0][question_text]"', false)
            ->assertSee('name="questions[0][image_path]"', false)
            ->assertDontSee('name="challenge_category_id"', false)
            ->assertDontSee('name="version_no"', false)
            ->assertDontSee('name="content_code"', false);

        $this->actingAsUser($instructor)
            ->get(route('instructor.challenge-builder.create', ['type' => 'coding']))
            ->assertOk()
            ->assertSee('New coding challenge')
            ->assertSee('data-coding-form', false)
            ->assertSee('data-coding-preview', false)
            ->assertSee('data-check-tests', false)
            ->assertSee(route('instructor.challenge-builder.check-tests'), false)
            ->assertSee('name="questions[0][problem_description]"', false)
            ->assertSee('name="questions[0][test_cases][0][expected_output]"', false)
            ->assertDontSee('name="version_no"', false);

        $this->actingAsUser($instructor)
            ->get(route('instructor.challenge-builder.create', ['type' => 'essay']))
            ->assertNotFound();
    }

    // ── Store ─────────────────────────────────────────────────────────

    public function test_store_creates_an_mcq_challenge_owned_by_the_instructor_on_the_university_level(): void
    {
        $university = $this->makeUniversityCategory();
        $newbie = $this->makeCategory('Newbie', 'newbie', 1);
        $instructor = $this->makeUser(User::ROLE_INSTRUCTOR);

        $this->actingAsUser($instructor)
            ->post(route('instructor.challenge-builder.store'), $this->mcqPayload([
                'type' => 'mcq',
                // Ignored: the level, owner and visibility are never taken from the form.
                'challenge_category_id' => $newbie->id,
                'visibility' => 'platform',
                'created_by' => 999,
                'is_active' => 1,
            ]))
            ->assertRedirect(route('instructor.challenge-builder.index'))
            ->assertSessionHas('success');

        $challenge = Challenge::query()->firstOrFail();
        $this->assertSame('Descriptive Statistics', $challenge->title);
        $this->assertSame((int) $university->id, (int) $challenge->challenge_category_id);
        $this->assertSame(Challenge::VISIBILITY_INSTRUCTOR, $challenge->visibility);
        $this->assertSame((int) $instructor->id, (int) $challenge->created_by);
        $this->assertFalse((bool) $challenge->is_coding_challenge);
        $this->assertTrue((bool) $challenge->is_active);
        $this->assertSame(1, (int) $challenge->version_no);
        $this->assertSame('V1', $challenge->version_code);
        $this->assertNotSame('', (string) $challenge->content_code);

        $questions = $challenge->questions()->with('options')->get();
        $this->assertCount(2, $questions);
        $this->assertSame('What is the mean of 2, 4 and 6?', $questions[0]->question_text);
        $this->assertSame('/uploads/challenges/mean-chart.png', $questions[0]->image_path);
        $this->assertNull($questions[1]->image_path);
        $this->assertSame([false, true, false], $questions[0]->options->pluck('is_correct')->map(fn ($v) => (bool) $v)->all());
        $this->assertSame([true, false], $questions[1]->options->pluck('is_correct')->map(fn ($v) => (bool) $v)->all());
    }

    public function test_two_challenges_with_the_same_title_do_not_collide_on_the_content_code_index(): void
    {
        $this->makeUniversityCategory();
        $instructor = $this->makeUser(User::ROLE_INSTRUCTOR);
        $other = $this->makeUser(User::ROLE_INSTRUCTOR);

        $this->actingAsUser($instructor)->post(route('instructor.challenge-builder.store'), $this->mcqPayload(['type' => 'mcq']))->assertRedirect();
        $this->actingAsUser($instructor)->post(route('instructor.challenge-builder.store'), $this->mcqPayload(['type' => 'mcq']))->assertRedirect();
        $this->actingAsUser($other)->post(route('instructor.challenge-builder.store'), $this->mcqPayload(['type' => 'mcq']))->assertRedirect();

        $this->assertSame(3, Challenge::count());
        $this->assertSame(3, Challenge::query()->distinct()->count('content_code'));
    }

    public function test_store_creates_a_coding_challenge_with_problems_and_test_cases(): void
    {
        $university = $this->makeUniversityCategory();
        $instructor = $this->makeUser(User::ROLE_INSTRUCTOR);

        $this->actingAsUser($instructor)
            ->post(route('instructor.challenge-builder.store', ['type' => 'coding']), $this->codingPayload(['is_active' => 0]))
            ->assertRedirect(route('instructor.challenge-builder.index'))
            ->assertSessionHas('success');

        $challenge = Challenge::query()->firstOrFail();
        $this->assertTrue((bool) $challenge->is_coding_challenge);
        $this->assertFalse((bool) $challenge->is_active);
        $this->assertSame((int) $university->id, (int) $challenge->challenge_category_id);
        $this->assertSame(Challenge::VISIBILITY_INSTRUCTOR, $challenge->visibility);
        $this->assertSame((int) $instructor->id, (int) $challenge->created_by);

        $questions = $challenge->codingQuestions()->with('testCases')->get();
        $this->assertCount(1, $questions);
        $this->assertSame('Sum of Two Numbers', $questions[0]->title);
        $this->assertSame("a, b = map(int, input().split())\nprint(a + b)", $questions[0]->reference_solution);
        $this->assertSame('python', $questions[0]->language);
        $this->assertCount(2, $questions[0]->testCases);
        $this->assertSame('1 2', $questions[0]->testCases[0]->input);
        $this->assertSame('3', $questions[0]->testCases[0]->expected_output);
        $this->assertFalse((bool) $questions[0]->testCases[0]->is_hidden);
        $this->assertTrue((bool) $questions[0]->testCases[1]->is_hidden);
    }

    public function test_store_validates_questions_pictures_and_test_cases(): void
    {
        $this->makeUniversityCategory();
        $instructor = $this->makeUser(User::ROLE_INSTRUCTOR);

        $this->actingAsUser($instructor)
            ->from(route('instructor.challenge-builder.create', ['type' => 'mcq']))
            ->post(route('instructor.challenge-builder.store'), $this->mcqPayload([
                'type' => 'mcq',
                'title' => '',
                'time_limit_seconds' => 10,
                'questions' => [
                    [
                        'question_text' => 'Only one choice?',
                        'image_path' => '/uploads/challenges/../../.env',
                        'correct_option' => 0,
                        'options' => [['option_text' => 'A']],
                    ],
                ],
            ]))
            ->assertRedirect(route('instructor.challenge-builder.create', ['type' => 'mcq']))
            ->assertSessionHasErrors(['title', 'time_limit_seconds', 'questions.0.image_path', 'questions.0.options']);

        $this->actingAsUser($instructor)
            ->from(route('instructor.challenge-builder.create', ['type' => 'coding']))
            ->post(route('instructor.challenge-builder.store', ['type' => 'coding']), $this->codingPayload([
                'questions' => [
                    [
                        'title' => 'No cases',
                        'problem_description' => '',
                        'language' => 'python',
                        'time_limit_seconds' => 600,
                        'base_xp' => 50,
                        'test_cases' => [],
                    ],
                ],
            ]))
            ->assertRedirect(route('instructor.challenge-builder.create', ['type' => 'coding']))
            ->assertSessionHasErrors(['questions.0.problem_description', 'questions.0.test_cases']);

        $this->assertSame(0, Challenge::count());
    }

    // ── Ownership ─────────────────────────────────────────────────────

    public function test_students_and_admins_cannot_reach_the_builder(): void
    {
        $category = $this->makeUniversityCategory();
        $instructor = $this->makeUser(User::ROLE_INSTRUCTOR);
        $challenge = $this->makeInstructorMcq($category, $instructor, 'Owned Quiz', true);

        $this->get(route('instructor.challenge-builder.index'))->assertRedirect(route('login'));

        foreach ([User::ROLE_USER, User::ROLE_ADMIN] as $role) {
            $user = $this->makeUser($role);

            $this->actingAsUser($user)->get(route('instructor.challenge-builder.index'))->assertForbidden();
            $this->actingAsUser($user)->get(route('instructor.challenge-builder.create', ['type' => 'mcq']))->assertForbidden();
            $this->actingAsUser($user)->post(route('instructor.challenge-builder.store'), $this->mcqPayload(['type' => 'mcq']))->assertForbidden();
            $this->actingAsUser($user)->postJson(route('instructor.challenge-builder.check-tests'), [
                'reference_solution' => 'print(1)', 'test_cases' => [['input' => '', 'expected_output' => '1']],
            ])->assertForbidden();
            $this->actingAsUser($user)->postJson(route('instructor.challenge-builder.images.store'), [
                'image' => UploadedFile::fake()->image('chart.png', 40, 40),
            ])->assertForbidden();
            $this->actingAsUser($user)->get(route('instructor.challenge-builder.edit', $challenge))->assertForbidden();
            $this->actingAsUser($user)->put(route('instructor.challenge-builder.update', $challenge), $this->mcqPayload(['title' => 'Hacked']))->assertForbidden();
            $this->actingAsUser($user)->delete(route('instructor.challenge-builder.destroy', $challenge))->assertForbidden();
        }

        $this->assertSame(1, Challenge::count());
        $this->assertSame('Owned Quiz', $challenge->fresh()->title);
    }

    public function test_another_instructor_and_platform_challenges_are_not_found(): void
    {
        $category = $this->makeUniversityCategory();
        $owner = $this->makeUser(User::ROLE_INSTRUCTOR);
        $intruder = $this->makeUser(User::ROLE_INSTRUCTOR);
        $mine = $this->makeInstructorMcq($category, $owner, 'Owned Quiz', true);
        $platform = $this->makePlatformMcq($category, 'Platform Quiz');

        foreach ([$mine, $platform] as $challenge) {
            $this->actingAsUser($intruder)->get(route('instructor.challenge-builder.edit', $challenge))->assertNotFound();
            $this->actingAsUser($intruder)->put(route('instructor.challenge-builder.update', $challenge), $this->mcqPayload(['title' => 'Hacked']))->assertNotFound();
            $this->actingAsUser($intruder)->delete(route('instructor.challenge-builder.destroy', $challenge))->assertNotFound();
        }

        // The owner cannot touch platform content through the builder either.
        $this->actingAsUser($owner)->get(route('instructor.challenge-builder.edit', $platform))->assertNotFound();
        $this->actingAsUser($owner)->delete(route('instructor.challenge-builder.destroy', $platform))->assertNotFound();

        $this->assertSame(2, Challenge::count());
        $this->assertSame('Owned Quiz', $mine->fresh()->title);
        $this->assertSame('Platform Quiz', $platform->fresh()->title);
    }

    // ── Update and delete ─────────────────────────────────────────────

    public function test_owner_can_edit_and_update_questions_without_history(): void
    {
        $category = $this->makeUniversityCategory();
        $instructor = $this->makeUser(User::ROLE_INSTRUCTOR);
        $challenge = $this->makeInstructorMcq($category, $instructor, 'Owned Quiz', false);

        $this->actingAsUser($instructor)
            ->get(route('instructor.challenge-builder.edit', $challenge))
            ->assertOk()
            ->assertSee('Edit quiz challenge')
            ->assertSee('Owned Quiz')
            ->assertSee('Original question?');

        $this->actingAsUser($instructor)
            ->put(route('instructor.challenge-builder.update', $challenge), $this->mcqPayload([
                'title' => 'Renamed Quiz',
                'is_active' => 1,
                'time_limit_seconds' => 900,
            ]))
            ->assertRedirect(route('instructor.challenge-builder.index'))
            ->assertSessionHas('success');

        $challenge->refresh();
        $this->assertSame('Renamed Quiz', $challenge->title);
        $this->assertTrue((bool) $challenge->is_active);
        $this->assertSame(900, (int) $challenge->time_limit_seconds);
        $this->assertSame((int) $category->id, (int) $challenge->challenge_category_id);
        $this->assertSame(Challenge::VISIBILITY_INSTRUCTOR, $challenge->visibility);
        $this->assertSame((int) $instructor->id, (int) $challenge->created_by);
        $this->assertSame(
            ['What is the mean of 2, 4 and 6?', 'Which is a measure of spread?'],
            $challenge->questions()->pluck('question_text')->all()
        );
    }

    public function test_attempt_history_freezes_mcq_questions_but_allows_title_and_availability(): void
    {
        $category = $this->makeUniversityCategory();
        $instructor = $this->makeUser(User::ROLE_INSTRUCTOR);
        $challenge = $this->makeInstructorMcq($category, $instructor, 'Owned Quiz', true);
        $this->recordMcqAttempt($challenge);

        $frozen = [
            'title' => 'Owned Quiz',
            'description' => 'desc',
            'time_limit_seconds' => 600,
            'base_xp' => 100,
            'is_active' => 1,
            'questions' => [
                ['question_text' => 'Original question?', 'image_path' => '', 'correct_option' => 0, 'options' => [['option_text' => 'Yes'], ['option_text' => 'No']]],
            ],
        ];

        $this->actingAsUser($instructor)
            ->from(route('instructor.challenge-builder.edit', $challenge))
            ->put(route('instructor.challenge-builder.update', $challenge), array_merge($frozen, [
                'questions' => [
                    ['question_text' => 'Changed question?', 'image_path' => '', 'correct_option' => 0, 'options' => [['option_text' => 'Yes'], ['option_text' => 'No']]],
                ],
            ]))
            ->assertRedirect(route('instructor.challenge-builder.edit', $challenge))
            ->assertSessionHasErrors(['questions']);

        $this->actingAsUser($instructor)
            ->from(route('instructor.challenge-builder.edit', $challenge))
            ->put(route('instructor.challenge-builder.update', $challenge), array_merge($frozen, ['time_limit_seconds' => 1200]))
            ->assertSessionHasErrors(['time_limit_seconds']);

        $this->assertSame('Original question?', $challenge->questions()->first()->question_text);

        $this->actingAsUser($instructor)
            ->put(route('instructor.challenge-builder.update', $challenge), array_merge($frozen, ['title' => 'Owned Quiz, week 3', 'is_active' => 0]))
            ->assertRedirect(route('instructor.challenge-builder.index'))
            ->assertSessionHas('success');

        $challenge->refresh();
        $this->assertSame('Owned Quiz, week 3', $challenge->title);
        $this->assertFalse((bool) $challenge->is_active);
        $this->assertSame('Original question?', $challenge->questions()->first()->question_text);

        $this->actingAsUser($instructor)
            ->get(route('instructor.challenge-builder.edit', $challenge))
            ->assertOk()
            ->assertSee('frozen');
    }

    public function test_coding_history_freezes_graded_content_but_allows_reference_solution(): void
    {
        $category = $this->makeUniversityCategory();
        $instructor = $this->makeUser(User::ROLE_INSTRUCTOR);
        $challenge = $this->makeInstructorCoding($category, $instructor, 'Owned Problem', true);
        $question = $challenge->codingQuestions()->first();

        CodingQuestionAttempt::create([
            'user_id' => $this->makeUser(User::ROLE_USER)->id,
            'coding_question_id' => $question->id,
            'started_at' => now()->subMinutes(3),
            'expired' => false,
        ]);

        $base = $this->codingPayload([
            'title' => 'Owned Problem',
            'questions' => [[
                'title' => 'Print it',
                'problem_description' => 'Print 42.',
                'language' => 'python',
                'starter_code' => '',
                'reference_solution' => 'print(42)',
                'time_limit_seconds' => 600,
                'base_xp' => 100,
                'test_cases' => [['input' => '', 'expected_output' => '42', 'is_hidden' => 0]],
            ]],
        ]);

        $changedTests = $base;
        $changedTests['questions'][0]['test_cases'][0]['expected_output'] = '43';

        $this->actingAsUser($instructor)
            ->from(route('instructor.challenge-builder.edit', $challenge))
            ->put(route('instructor.challenge-builder.update', $challenge), $changedTests)
            ->assertRedirect(route('instructor.challenge-builder.edit', $challenge))
            ->assertSessionHasErrors(['questions']);

        $allowed = $base;
        $allowed['questions'][0]['title'] = 'Print the answer';
        $allowed['questions'][0]['reference_solution'] = "print(40 + 2)";
        $allowed['title'] = 'Owned Problem, revised';

        $this->actingAsUser($instructor)
            ->put(route('instructor.challenge-builder.update', $challenge), $allowed)
            ->assertRedirect(route('instructor.challenge-builder.index'))
            ->assertSessionHas('success');

        $question->refresh();
        $this->assertSame('Print the answer', $question->title);
        $this->assertSame('print(40 + 2)', $question->reference_solution);
        $this->assertSame('42', $question->testCases()->first()->expected_output);
        $this->assertSame('Owned Problem, revised', $challenge->fresh()->title);
    }

    public function test_destroy_removes_the_challenge_and_its_class_entries_but_not_with_history(): void
    {
        $category = $this->makeUniversityCategory();
        $instructor = $this->makeUser(User::ROLE_INSTRUCTOR);
        $class = $this->makeClass($instructor);

        $fresh = $this->makeInstructorMcq($category, $instructor, 'Fresh Quiz', true);
        $used = $this->makeInstructorMcq($category, $instructor, 'Used Quiz', true);
        foreach ([$fresh, $used] as $challenge) {
            ClassChallengeAssignment::create([
                'class_id' => $class->id, 'challenge_id' => $challenge->id, 'assigned_by' => $instructor->id,
                'title' => $challenge->title, 'status' => 'published',
            ]);
        }
        $this->recordMcqAttempt($used);

        $this->actingAsUser($instructor)
            ->delete(route('instructor.challenge-builder.destroy', $fresh))
            ->assertRedirect(route('instructor.challenge-builder.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('challenges', ['id' => $fresh->id]);
        $this->assertDatabaseMissing('class_challenge_assignments', ['challenge_id' => $fresh->id]);
        $this->assertDatabaseMissing('challenge_questions', ['challenge_id' => $fresh->id]);

        $this->actingAsUser($instructor)
            ->from(route('instructor.challenge-builder.index'))
            ->delete(route('instructor.challenge-builder.destroy', $used))
            ->assertRedirect(route('instructor.challenge-builder.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('challenges', ['id' => $used->id]);
        $this->assertDatabaseHas('class_challenge_assignments', ['challenge_id' => $used->id]);
    }

    // ── Check tests and pictures ──────────────────────────────────────

    public function test_check_tests_grades_the_reference_solution_through_the_bound_runner(): void
    {
        $this->makeUniversityCategory();
        $instructor = $this->makeUser(User::ROLE_INSTRUCTOR);
        $calls = [];

        $this->app->instance(CodingChallengeTestRunner::class, new CodingChallengeTestRunner(
            function (string $code, string $stdin, array $options) use (&$calls): array {
                $calls[] = [$code, $stdin, $options];

                return match ($stdin) {
                    'slow' => ['stdout' => '', 'stderr' => '', 'exit_code' => 124, 'failed' => true, 'timed_out' => true],
                    default => ['stdout' => strtoupper($stdin) . "\n", 'stderr' => '', 'exit_code' => 0, 'failed' => false, 'timed_out' => false],
                };
            }
        ));

        $this->actingAsUser($instructor)
            ->postJson(route('instructor.challenge-builder.check-tests'), [
                'reference_solution' => "import sys\r\nprint(input().upper())",
                'time_limit_seconds' => 600,
                'test_cases' => [
                    ['input' => 'abc', 'expected_output' => "ABC\r\n", 'is_hidden' => 0],
                    ['input' => 'xyz', 'expected_output' => 'wrong', 'is_hidden' => 1],
                    ['input' => 'slow', 'expected_output' => ''],
                ],
            ])
            ->assertOk()
            ->assertJsonPath('summary.passed', 1)
            ->assertJsonPath('summary.total', 3)
            ->assertJsonPath('results.0.passed', true)
            ->assertJsonPath('results.1.passed', false)
            ->assertJsonPath('results.1.is_hidden', true)
            ->assertJsonPath('results.2.timed_out', true);

        $this->assertCount(3, $calls);
        $this->assertSame("import sys\nprint(input().upper())", $calls[0][0]);
        $this->assertSame(10, $calls[0][2]['timeout']);
        $this->assertSame(0, Challenge::count());

        $this->actingAsUser($instructor)
            ->postJson(route('instructor.challenge-builder.check-tests'), ['reference_solution' => '', 'test_cases' => []])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['reference_solution', 'test_cases']);
    }

    public function test_upload_image_stores_a_png_and_rejects_text_files(): void
    {
        $this->makeUniversityCategory();
        $instructor = $this->makeUser(User::ROLE_INSTRUCTOR);

        $response = $this->actingAsUser($instructor)
            ->postJson(route('instructor.challenge-builder.images.store'), [
                'image' => UploadedFile::fake()->image('chart.png', 320, 200),
            ])
            ->assertOk()
            ->assertJsonStructure(['url']);

        $url = (string) $response->json('url');
        $this->assertMatchesRegularExpression('#^/uploads/challenges/[a-z0-9]+\.png$#', $url);
        $this->assertSame(1, preg_match(\App\Http\Controllers\InstructorChallengeBuilderController::IMAGE_PATH_PATTERN, $url));

        $path = public_path(ltrim($url, '/'));
        $this->uploadedFiles[] = $path;
        $this->assertFileExists($path);

        $this->actingAsUser($instructor)
            ->postJson(route('instructor.challenge-builder.images.store'), [
                'image' => UploadedFile::fake()->create('notes.txt', 12, 'text/plain'),
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['image']);

        $this->actingAsUser($instructor)
            ->postJson(route('instructor.challenge-builder.images.store'), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['image']);
    }

    // ── Fixtures ──────────────────────────────────────────────────────

    private function mcqPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Descriptive Statistics',
            'description' => 'Means, medians and spread.',
            'time_limit_seconds' => 600,
            'base_xp' => 100,
            'is_active' => 0,
            'questions' => [
                [
                    'question_text' => 'What is the mean of 2, 4 and 6?',
                    'image_path' => '/uploads/challenges/mean-chart.png',
                    'correct_option' => 1,
                    'options' => [['option_text' => '3'], ['option_text' => '4'], ['option_text' => '6']],
                ],
                [
                    'question_text' => 'Which is a measure of spread?',
                    'image_path' => '',
                    'correct_option' => 0,
                    'options' => [['option_text' => 'Standard deviation'], ['option_text' => 'Mode']],
                ],
            ],
        ], $overrides);
    }

    private function codingPayload(array $overrides = []): array
    {
        return array_merge([
            'type' => 'coding',
            'title' => 'Sum Two Numbers',
            'description' => 'Read two integers and print their sum.',
            'time_limit_seconds' => 1800,
            'base_xp' => 150,
            'is_active' => 1,
            'questions' => [
                [
                    'title' => 'Sum of Two Numbers',
                    'problem_description' => 'Read two integers separated by a space and print their sum.',
                    'language' => 'python',
                    'starter_code' => '# read the input',
                    'reference_solution' => "a, b = map(int, input().split())\r\nprint(a + b)",
                    'time_limit_seconds' => 600,
                    'base_xp' => 50,
                    'test_cases' => [
                        ['input' => '1 2', 'expected_output' => '3', 'is_hidden' => 0],
                        ['input' => '10 -4', 'expected_output' => '6', 'is_hidden' => 1],
                    ],
                ],
            ],
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

    private function makeInstructorMcq(ChallengeCategory $category, User $instructor, string $title, bool $active): Challenge
    {
        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title' => $title,
            'description' => 'desc',
            'time_limit_seconds' => 600,
            'base_xp' => 100,
            'order_index' => 0,
            'is_coding_challenge' => false,
            'is_active' => $active,
            'visibility' => Challenge::VISIBILITY_INSTRUCTOR,
            'created_by' => $instructor->id,
            'content_code' => 'T-' . Str::upper(Str::random(10)),
        ]);

        $question = $challenge->questions()->create([
            'challenge_category_id' => $category->id,
            'question_text' => 'Original question?',
            'order_index' => 1,
        ]);
        $question->options()->create(['option_text' => 'Yes', 'is_correct' => true, 'order_index' => 1]);
        $question->options()->create(['option_text' => 'No', 'is_correct' => false, 'order_index' => 2]);

        return $challenge;
    }

    private function makeInstructorCoding(ChallengeCategory $category, User $instructor, string $title, bool $active): Challenge
    {
        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title' => $title,
            'description' => 'desc',
            'time_limit_seconds' => 1800,
            'base_xp' => 100,
            'order_index' => 0,
            'is_coding_challenge' => true,
            'is_active' => $active,
            'visibility' => Challenge::VISIBILITY_INSTRUCTOR,
            'created_by' => $instructor->id,
            'content_code' => 'T-' . Str::upper(Str::random(10)),
        ]);

        $question = $challenge->codingQuestions()->create([
            'title' => 'Print it',
            'problem_description' => 'Print 42.',
            'language' => 'python',
            'starter_code' => null,
            'reference_solution' => 'print(42)',
            'time_limit_seconds' => 600,
            'base_xp' => 100,
            'order_index' => 1,
        ]);
        $question->testCases()->create(['input' => null, 'expected_output' => '42', 'is_hidden' => false, 'order_index' => 1]);

        return $challenge;
    }

    private function makePlatformMcq(ChallengeCategory $category, string $title): Challenge
    {
        return Challenge::create([
            'challenge_category_id' => $category->id,
            'title' => $title,
            'description' => 'desc',
            'time_limit_seconds' => 600,
            'base_xp' => 100,
            'order_index' => 1,
            'is_coding_challenge' => false,
            'is_active' => true,
            'content_code' => 'P-' . Str::upper(Str::random(10)),
        ]);
    }

    private function recordMcqAttempt(Challenge $challenge): ChallengeAttempt
    {
        return ChallengeAttempt::create([
            'user_id' => $this->makeUser(User::ROLE_USER)->id,
            'challenge_id' => $challenge->id,
            'attempt_no' => 1,
            'mode' => 'ranked',
            'status' => 'submitted',
            'started_at' => now()->subMinutes(10),
            'expires_at' => now()->subMinutes(5),
            'time_limit_seconds' => 600,
            'total_questions' => 1,
        ]);
    }

    private function makeClass(User $instructor): ClassRoom
    {
        return ClassRoom::create([
            'instructor_id' => $instructor->id,
            'name' => 'Data Science 101',
            'section' => 'A',
            'is_archived' => false,
        ]);
    }

    private ?Institution $institution = null;

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
