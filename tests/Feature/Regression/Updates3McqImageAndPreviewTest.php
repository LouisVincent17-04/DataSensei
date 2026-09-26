<?php

namespace Tests\Feature\Regression;

use App\Models\Challenge;
use App\Models\ChallengeAttempt;
use App\Models\ChallengeCategory;
use App\Models\ChallengeOption;
use App\Models\ChallengeQuestion;
use App\Models\User;
use App\Support\AuthSessionFingerprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Updates 3, B2: a picture per MCQ question, the picture upload endpoint, the
 * live preview panel in the editor and the picture on the learner's quiz page.
 */
class Updates3McqImageAndPreviewTest extends TestCase
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

    public function test_store_persists_a_question_image_path_and_the_editor_reloads_it(): void
    {
        $category = $this->makeCategory();
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $this->actingAsUser($admin)
            ->post(route('admin.challenges.store'), $this->payload($category, [
                'questions' => [
                    $this->question('What does this chart show?', '/uploads/challenges/abc123.png'),
                    $this->question('No picture here', null),
                ],
            ]))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $challenge = Challenge::query()->where('title', 'Pictures MCQ')->firstOrFail();
        $questions = $challenge->questions()->get();

        $this->assertCount(2, $questions);
        $this->assertSame('/uploads/challenges/abc123.png', $questions[0]->image_path);
        $this->assertNull($questions[1]->image_path);

        $this->actingAsUser($admin)
            ->get(route('admin.challenges.edit', $challenge))
            ->assertOk()
            ->assertSee('name="questions[0][image_path]" value="/uploads/challenges/abc123.png"', false)
            ->assertSee('data-mcq-preview', false)
            ->assertSee('js/admin-mcq-preview.js', false)
            ->assertSee(route('admin.challenges.images.store'), false);

        $this->actingAsUser($admin)
            ->get(route('admin.challenges.show', $challenge))
            ->assertOk()
            ->assertSee('src="/uploads/challenges/abc123.png"', false);
    }

    public function test_create_page_renders_image_controls_and_live_preview(): void
    {
        $this->makeCategory();

        $this->actingAsUser($this->makeUser(User::ROLE_ADMIN))
            ->get(route('admin.challenges.create'))
            ->assertOk()
            ->assertSee('data-image-file', false)
            ->assertSee('data-remove-image', false)
            ->assertSee('data-field="image_path"', false)
            ->assertSee('Live Preview')
            ->assertSee('js/admin-mcq-preview.js', false);
    }

    public function test_invalid_image_paths_are_rejected(): void
    {
        $category = $this->makeCategory();
        $admin = $this->makeUser(User::ROLE_ADMIN);

        foreach ([
            'javascript:alert(1)',
            'http://evil.example/uploads/challenges/a.png',
            '/uploads/challenges/../../index.php',
            '/uploads/challenges/../other/a.png',
            '/uploads/other/a.png',
            '/uploads/challenges/a.txt',
            '/uploads/challenges/a.png.php',
            'uploads/challenges/a.png',
        ] as $badPath) {
            $this->actingAsUser($admin)
                ->postJson(route('admin.challenges.store'), $this->payload($category, [
                    'questions' => [$this->question('Bad picture', $badPath)],
                ]))
                ->assertStatus(422)
                ->assertJsonValidationErrors(['questions.0.image_path']);
        }

        $this->assertSame(0, Challenge::count());
    }

    public function test_update_keeps_image_path_and_rejects_bad_ones(): void
    {
        $category = $this->makeCategory();
        $admin = $this->makeUser(User::ROLE_ADMIN);
        [$challenge] = $this->makeChallenge($category, '/uploads/challenges/first.png');

        $this->actingAsUser($admin)
            ->put(route('admin.challenges.update', $challenge), $this->payload($category, [
                'questions' => [$this->question('Question 1?', '/uploads/challenges/second.webp')],
            ]))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertSame('/uploads/challenges/second.webp', $challenge->questions()->first()->image_path);

        $this->actingAsUser($admin)
            ->putJson(route('admin.challenges.update', $challenge), $this->payload($category, [
                'questions' => [$this->question('Question 1?', 'javascript:alert(1)')],
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['questions.0.image_path']);

        $this->assertSame('/uploads/challenges/second.webp', $challenge->questions()->first()->image_path);
    }

    public function test_picture_can_be_corrected_on_a_challenge_with_attempt_history_without_touching_questions(): void
    {
        $category = $this->makeCategory();
        $admin = $this->makeUser(User::ROLE_ADMIN);
        [$challenge, $withImage] = $this->makeChallenge($category, '/uploads/challenges/first.png');
        $optionIds = $withImage->options()->pluck('id')->all();

        ChallengeAttempt::create([
            'user_id' => $this->makeUser(User::ROLE_USER)->id,
            'challenge_id' => $challenge->id,
            'attempt_no' => 1,
            'mode' => 'ranked',
            'status' => 'submitted',
            'started_at' => now()->subMinutes(10),
            'expires_at' => now()->subMinutes(5),
            'time_limit_seconds' => 600,
            'total_questions' => 2,
        ]);

        $payload = $this->payload($category, [
            'is_active' => 1,
            'questions' => [
                $this->question('Question 1?', '/uploads/challenges/replacement.jpg'),
                $this->question('Question 2?', null),
            ],
        ]);

        $this->actingAsUser($admin)
            ->put(route('admin.challenges.update', $challenge), $payload)
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $withImage->refresh();
        $this->assertSame('/uploads/challenges/replacement.jpg', $withImage->image_path);
        $this->assertSame($optionIds, $withImage->options()->pluck('id')->all(), 'Frozen questions keep their rows.');
        $this->assertSame(2, $challenge->questions()->count());

        // The frozen-content rule still applies to the question text itself.
        $payload['questions'][0]['question_text'] = 'A different question?';
        $this->actingAsUser($admin)
            ->from(route('admin.challenges.edit', $challenge))
            ->put(route('admin.challenges.update', $challenge), $payload)
            ->assertRedirect(route('admin.challenges.edit', $challenge))
            ->assertSessionHasErrors(['questions']);
    }

    public function test_upload_image_stores_a_png_under_public_uploads_challenges(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $response = $this->actingAsUser($admin)
            ->postJson(route('admin.challenges.images.store'), [
                'image' => UploadedFile::fake()->image('chart.png', 320, 200),
            ])
            ->assertOk()
            ->assertJsonStructure(['url']);

        $url = (string) $response->json('url');
        $this->assertMatchesRegularExpression('#^/uploads/challenges/[a-z0-9]+\.png$#', $url);

        $path = public_path(ltrim($url, '/'));
        $this->uploadedFiles[] = $path;

        $this->assertFileExists($path);
        $this->assertSame(1, preg_match('/^\/uploads\/challenges\/(?!.*\.\.)[A-Za-z0-9_\-.\/]+\.(png|jpe?g|gif|webp)$/i', $url));
    }

    public function test_upload_image_rejects_non_image_files_and_missing_files(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $this->actingAsUser($admin)
            ->postJson(route('admin.challenges.images.store'), [
                'image' => UploadedFile::fake()->create('notes.txt', 12, 'text/plain'),
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['image']);

        $this->actingAsUser($admin)
            ->postJson(route('admin.challenges.images.store'), [
                'image' => UploadedFile::fake()->create('script.php', 12, 'application/x-php'),
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['image']);

        $this->actingAsUser($admin)
            ->postJson(route('admin.challenges.images.store'), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['image']);

        $directory = public_path('uploads/challenges');
        if (is_dir($directory)) {
            $this->assertSame([], File::glob($directory . '/*.txt'));
            $this->assertSame([], File::glob($directory . '/*.php'));
        }
    }

    public function test_students_cannot_upload_question_images(): void
    {
        $student = $this->makeUser(User::ROLE_USER);

        $this->actingAsUser($student)
            ->postJson(route('admin.challenges.images.store'), [
                'image' => UploadedFile::fake()->image('chart.png', 40, 40),
            ])
            ->assertForbidden();
    }

    public function test_quiz_page_shows_the_question_picture_only_when_one_is_set(): void
    {
        $category = $this->makeCategory();
        [$challenge, $withImage, $withoutImage] = $this->makeChallenge($category, '/uploads/challenges/shown.png');
        $student = $this->makeUser(User::ROLE_USER);

        $this->actingAsUser($student)
            ->get(route('challenges.quiz', ['slug' => 'newbie', 'challenge' => $challenge->id]))
            ->assertOk()
            ->assertSee('Question 1?')
            ->assertSee('Question 2?')
            ->assertSee('<img class="page-quiz-question-image" src="/uploads/challenges/shown.png" alt="">', false);

        $this->assertSame(1, ChallengeAttempt::where('user_id', $student->id)->where('challenge_id', $challenge->id)->count());

        $withImage->update(['image_path' => null]);

        $this->actingAsUser($student)
            ->get(route('challenges.quiz', ['slug' => 'newbie', 'challenge' => $challenge->id]))
            ->assertOk()
            ->assertSee('Question 1?')
            ->assertDontSee('page-quiz-question-image"', false)
            ->assertDontSee('/uploads/challenges/', false);
    }

    private function makeCategory(): ChallengeCategory
    {
        return ChallengeCategory::create([
            'name' => 'Newbie',
            'slug' => 'newbie',
            'target_audience' => 'Beginners',
            'description' => 'Beginner challenges',
            'order_index' => 1,
        ]);
    }

    /**
     * @return array{0: Challenge, 1: ChallengeQuestion, 2: ChallengeQuestion}
     */
    private function makeChallenge(ChallengeCategory $category, ?string $imagePath): array
    {
        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'content_code' => 'MCQ-PICTURES',
            'title' => 'Pictures MCQ',
            'description' => 'Look at the picture.',
            'time_limit_seconds' => 600,
            'base_xp' => 100,
            'order_index' => 1,
            'is_coding_challenge' => false,
            'version_no' => 1,
            'version_name' => 'Version 1',
            'version_code' => 'V1',
            'is_active' => true,
        ]);

        $questions = [];
        foreach ([1 => $imagePath, 2 => null] as $i => $path) {
            $question = ChallengeQuestion::create([
                'challenge_id' => $challenge->id,
                'challenge_category_id' => $category->id,
                'question_text' => "Question {$i}?",
                'order_index' => $i,
                'image_path' => $path,
            ]);
            ChallengeOption::create(['challenge_question_id' => $question->id, 'option_text' => 'right', 'is_correct' => true, 'order_index' => 1]);
            ChallengeOption::create(['challenge_question_id' => $question->id, 'option_text' => 'wrong', 'is_correct' => false, 'order_index' => 2]);
            $questions[] = $question;
        }

        return [$challenge, $questions[0], $questions[1]];
    }

    private function payload(ChallengeCategory $category, array $overrides = []): array
    {
        return array_merge([
            'challenge_category_id' => $category->id,
            'content_code' => 'MCQ-PICTURES',
            'title' => 'Pictures MCQ',
            'description' => 'Look at the picture.',
            'time_limit_seconds' => 600,
            'base_xp' => 100,
            'order_index' => 1,
            'version_no' => 1,
            'version_name' => 'Version 1',
            'version_code' => 'V1',
            'is_active' => 0,
            'questions' => [$this->question('Question 1?', null)],
        ], $overrides);
    }

    private function question(string $text, ?string $imagePath): array
    {
        return [
            'question_text' => $text,
            'image_path' => $imagePath,
            'correct_option' => 0,
            'options' => [
                ['option_text' => 'right'],
                ['option_text' => 'wrong'],
            ],
        ];
    }

    private function makeUser(int $role): User
    {
        return User::create([
            'name' => 'Updates3 User',
            'email' => 'updates3-' . Str::lower(Str::random(10)) . '@example.test',
            'password' => bcrypt('Secret!2026'),
            'role' => $role,
            'status' => 'active',
        ]);
    }

    private function actingAsUser(User $user)
    {
        return $this->actingAs($user)->withSession([
            AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($user),
        ]);
    }
}
