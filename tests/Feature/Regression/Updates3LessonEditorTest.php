<?php

namespace Tests\Feature\Regression;

use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use App\Support\AuthSessionFingerprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Updates 3, A2: the lessons of a public module, their rendered preview and
 * image uploads. Since Updates 5 the lessons are edited as section cards in
 * the module editor (AdminPublicModuleController); the guarantees stay: admin
 * only, every block type renders, a lesson written before the editor saves
 * byte for byte the same, and a lesson students started cannot be deleted.
 */
class Updates3LessonEditorTest extends TestCase
{
    use RefreshDatabase;

    /** @var list<string> */
    private array $uploadedFiles = [];

    protected function tearDown(): void
    {
        // Only what this test uploaded; the folders may hold real uploads.
        foreach ($this->uploadedFiles as $path) {
            File::delete($path);
            $directory = dirname($path);
            if (File::isDirectory($directory) && File::files($directory) === [] && File::directories($directory) === []) {
                File::deleteDirectory($directory);
            }
        }

        parent::tearDown();
    }

    public function test_lesson_pages_are_admin_only(): void
    {
        $module = $this->makeModule();
        $lesson = $this->makeLegacyLesson($module, '<p>Hi</p>');
        $student = $this->makeUser(User::ROLE_USER);

        $this->actingAsUser($student)->get(route('admin.modules.lessons.index', $module))->assertForbidden();
        $this->actingAsUser($student)->get(route('admin.modules.lessons.create', $module))->assertForbidden();
        $this->actingAsUser($student)->get(route('admin.modules.lessons.edit', [$module, $lesson]))->assertForbidden();
        $this->actingAsUser($student)->put(route('admin.modules.update', $module), $this->moduleForm($module, ['lessons_json' => '[]']))->assertForbidden();
        $this->actingAsUser($student)->post(route('admin.lessons.preview'), ['blocks_json' => '[]'])->assertForbidden();
        $this->actingAsUser($student)->post(route('admin.lessons.images.store'), ['image' => UploadedFile::fake()->image('a.png')])->assertForbidden();

        $this->assertSame('<p>Hi</p>', $lesson->fresh()->content);
    }

    /**
     * Updates 5: a module's lessons are its sections in the module editor.
     * The old lesson pages open that editor.
     */
    public function test_the_old_lesson_pages_open_the_module_editor(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $module = $this->makeModule();
        $other = $this->makeModule(2);
        $lesson = $this->makeLegacyLesson($module, '<h2>Old</h2>', 'Old Lesson');
        $foreign = $this->makeLegacyLesson($other, '<p>Other</p>');
        $editor = route('admin.modules.edit', $module).'#content';

        $this->actingAsUser($admin)->get(route('admin.modules.lessons.index', $module))->assertRedirect($editor);
        $this->actingAsUser($admin)->get(route('admin.modules.lessons.create', $module))->assertRedirect($editor);
        $this->actingAsUser($admin)->get(route('admin.modules.lessons.edit', [$module, $lesson]))->assertRedirect($editor);
        $this->actingAsUser($admin)->get(route('admin.modules.lessons.edit', [$module, $foreign]))->assertNotFound();

        $this->actingAsUser($admin)
            ->get(route('admin.modules.edit', $module))
            ->assertOk()
            ->assertSee('Old Lesson')
            ->assertSee('js/admin-module-editor.js', false)
            ->assertSee('name="_token"', false);
    }

    public function test_the_renderer_renders_every_block_type(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $blocks = [
            ['type' => 'code', 'label' => 'Hello', 'language' => 'python', 'code' => "print('hi')  # greet", 'output' => 'hi', 'try_in_compiler' => true],
            ['type' => 'text', 'font' => 'sans', 'size' => 'md', 'body' => "## Heading\n\nA **bold** word and `code`.\n\n- one\n- two"],
            ['type' => 'table', 'label' => 'Scores', 'columns' => ['Name', 'Score'], 'rows' => [['Ana', '90'], ['Ben', '85']], 'note' => 'Out of 100'],
            ['type' => 'image', 'src' => '/uploads/lessons/1/abc.png', 'alt' => 'A chart', 'caption' => 'Figure 1', 'width' => 50],
            ['type' => 'html', 'html' => '<div class="custom-note">Raw <em>html</em></div>'],
        ];

        $stored = app(\App\Services\LessonBlockRenderer::class)->normalize($blocks);
        $this->assertSame(['code', 'text', 'table', 'image', 'html'], array_column($stored, 'type'));
        $this->assertSame("print('hi')  # greet", $stored[0]['code']);
        $this->assertSame([['Ana', '90'], ['Ben', '85']], $stored[2]['rows']);
        $this->assertSame(50, $stored[3]['width']);

        $content = $this->actingAsUser($admin)
            ->post(route('admin.lessons.preview'), ['blocks_json' => json_encode($blocks)])
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('class="code-window"', $content);
        $this->assertStringContainsString('PYTHON — Hello', $content);
        $this->assertStringContainsString('launchIDE(this)', $content);
        $this->assertStringContainsString('Console Output', $content);
        $this->assertStringContainsString('<h2>Heading</h2>', $content);
        $this->assertStringContainsString('<strong>bold</strong>', $content);
        $this->assertStringContainsString('<code>code</code>', $content);
        $this->assertStringContainsString('<li>one</li><li>two</li>', $content);
        $this->assertStringContainsString('<table', $content);
        $this->assertStringContainsString('<th style=', $content);
        $this->assertStringContainsString('Ana', $content);
        $this->assertStringContainsString('<img src="/uploads/lessons/1/abc.png"', $content);
        $this->assertStringContainsString('width:50%', $content);
        $this->assertStringContainsString('<div class="custom-note">Raw <em>html</em></div>', $content);
    }

    public function test_legacy_lesson_opens_as_one_card_and_saves_byte_identical(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $module = $this->makeModule();

        $original = "<h2>Intro to SQL</h2>\r\n<p>Some \"quoted\" text &amp; more — with unicode ✓</p>\n"
            .'<div class="code-window"><div class="code-content">SELECT * FROM t; -- keep</div></div>'
            ."\n<script>alert('legacy');</script>\n  ";
        $lesson = $this->makeLegacyLesson($module, $original, 'Legacy');
        // A lesson built with the older block editor.
        $built = Lesson::create([
            'module_id' => $module->id,
            'title' => 'Built Lesson',
            'content' => '<p>x</p>',
            'blocks' => json_encode([['type' => 'text', 'body' => 'x'], ['type' => 'html', 'html' => '<p>y</p>']]),
            'order_index' => 2,
        ]);

        $page = $this->actingAsUser($admin)
            ->get(route('admin.modules.edit', $module))
            ->assertOk()
            ->assertSee('Legacy');

        // The editor is seeded with one section per lesson, as content blocks
        // (Updates 6); the script it cannot split is kept in one block.
        preg_match('#<script type="application/json" id="module-editor-data">(.*?)</script>#s', $page->getContent(), $match);
        $this->assertNotEmpty($match, 'The editor did not receive its sections.');
        $cards = json_decode($match[1], true)['sections'];
        $this->assertSame('Legacy', $cards[0]['title']);
        $this->assertSame(['type' => 'heading', 'text' => 'Intro to SQL'], $cards[0]['blocks'][0]);
        $this->assertContains('preserved', array_column($cards[0]['blocks'], 'type'));
        $this->assertSame([['type' => 'paragraph', 'text' => 'x'], ['type' => 'paragraph', 'text' => 'y']], $cards[1]['blocks']);

        // Saving what the editor was given, untouched, leaves both lessons as they were.
        $this->actingAsUser($admin)
            ->put(route('admin.modules.update', $module), $this->moduleForm($module, ['intent' => 'save', 'lessons_json' => json_encode($cards)]))
            ->assertRedirect(route('admin.modules.edit', $module))
            ->assertSessionHas('success');

        $fresh = $lesson->fresh();
        $this->assertTrue($original === $fresh->content, 'The saved content changed.');
        $this->assertSame(strlen($original), strlen($fresh->content));
        $this->assertTrue($fresh->usesLegacyHtml());
        $this->assertSame('<p>x</p>', $built->fresh()->content);
        $this->assertSame([['type' => 'text', 'body' => 'x'], ['type' => 'html', 'html' => '<p>y</p>']], json_decode($built->fresh()->blocks, true));
    }

    public function test_update_can_add_fields_after_the_original_html(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $module = $this->makeModule();
        $lesson = $this->makeLegacyLesson($module, '<p>Original</p>', 'Legacy');

        $this->actingAsUser($admin)
            ->put(route('admin.modules.update', $module), $this->moduleForm($module, [
                'intent' => 'save',
                'lessons_json' => json_encode([[
                    'id' => $lesson->id,
                    'title' => 'Legacy plus',
                    'blocks' => [
                        ['type' => 'heading', 'text' => 'Legacy plus'],
                        ['type' => 'paragraph', 'text' => 'Original'],
                        ['type' => 'subheading', 'text' => 'New part'],
                    ],
                ]]),
            ]))
            ->assertRedirect(route('admin.modules.edit', $module));

        $fresh = $lesson->fresh();
        $this->assertSame('Legacy plus', $fresh->title);
        $this->assertStringContainsString('<p>Original</p>', $fresh->content);
        $this->assertStringContainsString('<h3>New part</h3>', $fresh->content);
        $this->assertFalse($fresh->usesLegacyHtml());
    }

    public function test_reorder_and_destroy(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $student = $this->makeUser(User::ROLE_USER);
        $module = $this->makeModule();
        $a = $this->makeLegacyLesson($module, '<p>a</p>', 'A', 1);
        $b = $this->makeLegacyLesson($module, '<p>b</p>', 'B', 2);
        $c = $this->makeLegacyLesson($module, '<p>c</p>', 'C', 3);
        $card = fn (Lesson $lesson) => ['id' => $lesson->id, 'heading' => $lesson->title, 'html' => $lesson->content];

        $this->actingAsUser($admin)
            ->put(route('admin.modules.update', $module), $this->moduleForm($module, ['intent' => 'save', 'lessons_json' => json_encode([$card($b), $card($c), $card($a)])]))
            ->assertSessionHasNoErrors();

        $this->assertSame(1, $b->fresh()->order_index);
        $this->assertSame(2, $c->fresh()->order_index);
        $this->assertSame(3, $a->fresh()->order_index);
        $this->assertSame('<p>a</p>', $a->fresh()->content);

        DB::table('lesson_user')->insert([
            'user_id' => $student->id,
            'lesson_id' => $a->id,
            'is_completed' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAsUser($admin)
            ->put(route('admin.modules.update', $module), $this->moduleForm($module, ['intent' => 'save', 'lessons_json' => json_encode([$card($b), $card($c)])]))
            ->assertSessionHasErrors('lessons_json');
        $this->assertDatabaseHas('lessons', ['id' => $a->id]);

        $this->actingAsUser($admin)
            ->put(route('admin.modules.update', $module), $this->moduleForm($module, ['intent' => 'save', 'lessons_json' => json_encode([$card($c), $card($a)])]))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('lessons', ['id' => $b->id]);
    }

    public function test_preview_returns_the_rendered_lesson_inside_the_learning_room_body(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $response = $this->actingAsUser($admin)->post(route('admin.lessons.preview'), [
            'blocks_json' => json_encode([
                ['type' => 'text', 'body' => '## Preview heading'],
                ['type' => 'code', 'label' => 'Demo', 'language' => 'sql', 'code' => 'SELECT 1;'],
            ]),
        ]);

        $response->assertOk()->assertHeader('Content-Type', 'text/html; charset=UTF-8');
        $html = $response->getContent();

        $this->assertStringContainsString('<div class="page-learning-lesson-body">', $html);
        $this->assertStringContainsString('.page-learning-lesson-body h2', $html);
        $this->assertStringContainsString('datasensei-design-system', $html);
        $this->assertStringContainsString('<h2>Preview heading</h2>', $html);
        $this->assertStringContainsString('class="code-window"', $html);
        $this->assertStringContainsString('SQL — Demo', $html);
        $this->assertStringContainsString('function launchIDE', $html);

        // JSON bodies work too.
        $this->actingAsUser($admin)
            ->postJson(route('admin.lessons.preview'), ['blocks_json' => json_encode([['type' => 'text', 'body' => 'From JSON']])])
            ->assertOk()
            ->assertSee('From JSON');
    }

    public function test_image_upload_stores_under_public_uploads_lessons(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $module = $this->makeModule();

        $response = $this->actingAsUser($admin)->post(route('admin.lessons.images.store'), [
            'image' => UploadedFile::fake()->image('diagram.png', 120, 80),
            'module_id' => $module->id,
        ]);

        $response->assertOk()->assertJsonStructure(['url']);
        $url = $response->json('url');
        $this->uploadedFiles[] = public_path(ltrim($url, '/'));
        $this->assertMatchesRegularExpression('#^/uploads/lessons/'.$module->id.'/[a-z0-9]{24}\.png$#', $url);
        $this->assertFileExists(public_path(ltrim($url, '/')));

        // The renderer accepts exactly this kind of path.
        $rendered = app(\App\Services\LessonBlockRenderer::class)->render([['type' => 'image', 'src' => $url, 'alt' => 'd']]);
        $this->assertStringContainsString('<img src="'.$url.'"', $rendered);
    }

    public function test_image_upload_without_module_goes_to_shared_and_rejects_non_images(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $url = $this->actingAsUser($admin)
            ->post(route('admin.lessons.images.store'), ['image' => UploadedFile::fake()->image('photo.jpg')])
            ->assertOk()
            ->json('url');
        $this->uploadedFiles[] = public_path(ltrim($url, '/'));
        $this->assertStringStartsWith('/uploads/lessons/shared/', $url);
        $this->assertStringEndsWith('.jpg', $url);
        $this->assertFileExists(public_path(ltrim($url, '/')));

        $this->actingAsUser($admin)
            ->postJson(route('admin.lessons.images.store'), ['image' => UploadedFile::fake()->create('notes.txt', 10, 'text/plain')])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['image']);

        $this->actingAsUser($admin)
            ->postJson(route('admin.lessons.images.store'), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['image']);
    }

    // ── Fixtures ─────────────────────────────────────────────────────

    private function makeModule(int $order = 1): Module
    {
        return Module::create([
            'title' => 'Content Module '.$order,
            'description' => 'A module.',
            'order_index' => $order,
            'year_level' => 'Year 1',
            'xp_reward' => 100,
            'is_boss' => false,
            // A published module keeps at least one learning outcome (Updates 5).
            'learning_outcomes' => ['Read the module.'],
        ]);
    }

    private function makeLegacyLesson(Module $module, string $html, string $title = 'Legacy Lesson', int $order = 1): Lesson
    {
        $lesson = Lesson::create([
            'module_id' => $module->id,
            'title' => $title,
            'content' => $html,
            'order_index' => $order,
        ]);

        $this->assertTrue($lesson->fresh()->usesLegacyHtml());

        return $lesson;
    }

    private function moduleForm(Module $module, array $overrides = []): array
    {
        return array_merge([
            'title' => $module->title,
            'description' => $module->description,
            'year_level' => $module->year_level,
            'xp_reward' => $module->xp_reward,
            'is_boss' => 0,
            'has_coding_exercises' => 0,
            'learning_outcomes' => $module->learning_outcomes,
        ], $overrides);
    }

    private function makeUser(int $role): User
    {
        return User::create([
            'name' => 'Updates3 User',
            'email' => 'updates3-'.Str::lower(Str::random(10)).'@example.test',
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
