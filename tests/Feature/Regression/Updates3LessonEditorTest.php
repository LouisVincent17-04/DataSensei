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
 * Updates 3, A2: the Module Content Manager, the block editor for the lessons
 * of a public module, its live preview and its image uploads.
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
        $this->actingAsUser($student)->post(route('admin.modules.lessons.store', $module), ['title' => 'x', 'blocks_json' => '[]'])->assertForbidden();
        $this->actingAsUser($student)->put(route('admin.modules.lessons.update', [$module, $lesson]), ['title' => 'x', 'blocks_json' => '[]'])->assertForbidden();
        $this->actingAsUser($student)->delete(route('admin.modules.lessons.destroy', [$module, $lesson]))->assertForbidden();
        $this->actingAsUser($student)->post(route('admin.modules.lessons.reorder', $module), ['order' => [$lesson->id]])->assertForbidden();
        $this->actingAsUser($student)->post(route('admin.lessons.preview'), ['blocks_json' => '[]'])->assertForbidden();
        $this->actingAsUser($student)->post(route('admin.lessons.images.store'), ['image' => UploadedFile::fake()->image('a.png')])->assertForbidden();

        $this->assertSame('<p>Hi</p>', $lesson->fresh()->content);
    }

    public function test_index_lists_lessons_with_block_counts_and_original_content_marker(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $module = $this->makeModule();
        $legacy = $this->makeLegacyLesson($module, '<h2>Old</h2>', 'Old Lesson');
        $built = Lesson::create([
            'module_id' => $module->id,
            'title' => 'Built Lesson',
            'content' => '<p>x</p>',
            'blocks' => json_encode([['type' => 'text', 'body' => 'x'], ['type' => 'html', 'html' => '<p>y</p>']]),
            'order_index' => 2,
        ]);

        $this->actingAsUser($admin)
            ->get(route('admin.modules.lessons.index', $module))
            ->assertOk()
            ->assertSee('Old Lesson')
            ->assertSee('Original content')
            ->assertSee('Built Lesson')
            ->assertSee('2 blocks')
            ->assertSee(route('admin.modules.lessons.edit', [$module, $legacy]), false)
            ->assertSee(route('admin.modules.lessons.edit', [$module, $built]), false)
            ->assertSee(route('admin.modules.lessons.create', $module), false);

        $this->actingAsUser($admin)
            ->get(route('admin.modules.lessons.create', $module))
            ->assertOk()
            ->assertSee('Add lesson')
            ->assertSee('data-add-block="code"', false)
            ->assertSee('data-preview-frame', false)
            ->assertSee('js/admin-lesson-editor.js', false)
            ->assertSee('<meta name="csrf-token"', false);
    }

    public function test_store_renders_every_block_type_and_keeps_the_blocks(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $module = $this->makeModule();

        $blocks = [
            ['type' => 'code', 'label' => 'Hello', 'language' => 'python', 'code' => "print('hi')  # greet", 'output' => 'hi', 'try_in_compiler' => true],
            ['type' => 'text', 'font' => 'sans', 'size' => 'md', 'body' => "## Heading\n\nA **bold** word and `code`.\n\n- one\n- two"],
            ['type' => 'table', 'label' => 'Scores', 'columns' => ['Name', 'Score'], 'rows' => [['Ana', '90'], ['Ben', '85']], 'note' => 'Out of 100'],
            ['type' => 'image', 'src' => '/uploads/lessons/1/abc.png', 'alt' => 'A chart', 'caption' => 'Figure 1', 'width' => 50],
            ['type' => 'html', 'html' => '<div class="custom-note">Raw <em>html</em></div>'],
        ];

        $this->actingAsUser($admin)
            ->post(route('admin.modules.lessons.store', $module), [
                'title' => 'Block Lesson',
                'blocks_json' => json_encode($blocks),
            ])
            ->assertRedirect(route('admin.modules.lessons.index', $module))
            ->assertSessionHas('success');

        $lesson = Lesson::where('module_id', $module->id)->where('title', 'Block Lesson')->firstOrFail();
        $this->assertSame(1, $lesson->order_index);
        $this->assertFalse($lesson->usesLegacyHtml());

        $stored = json_decode($lesson->blocks, true);
        $this->assertIsArray($stored);
        $this->assertSame(['code', 'text', 'table', 'image', 'html'], array_column($stored, 'type'));
        $this->assertSame("print('hi')  # greet", $stored[0]['code']);
        $this->assertSame([['Ana', '90'], ['Ben', '85']], $stored[2]['rows']);
        $this->assertSame(50, $stored[3]['width']);

        $content = $lesson->content;
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

        // The learning room reads lessons.content as-is, so it must be there.
        $this->assertStringContainsString('code-window', DB::table('lessons')->where('id', $lesson->id)->value('content'));
    }

    public function test_store_validates_title_and_blocks(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $module = $this->makeModule();

        $this->actingAsUser($admin)
            ->from(route('admin.modules.lessons.create', $module))
            ->post(route('admin.modules.lessons.store', $module), ['title' => '', 'blocks_json' => 'not json'])
            ->assertRedirect(route('admin.modules.lessons.create', $module))
            ->assertSessionHasErrors(['title', 'blocks_json']);

        $this->actingAsUser($admin)
            ->post(route('admin.modules.lessons.store', $module), ['title' => 'Too long '.str_repeat('x', 200), 'blocks_json' => '[]'])
            ->assertSessionHasErrors(['title']);

        $tooMany = json_encode(array_fill(0, 201, ['type' => 'text', 'body' => 'x']));
        $this->actingAsUser($admin)
            ->post(route('admin.modules.lessons.store', $module), ['title' => 'Many', 'blocks_json' => $tooMany])
            ->assertSessionHasErrors(['blocks_json']);

        $this->actingAsUser($admin)
            ->post(route('admin.modules.lessons.store', $module), ['title' => 'Object', 'blocks_json' => '"just a string"'])
            ->assertSessionHasErrors(['blocks_json']);

        $this->assertSame(0, Lesson::count());
    }

    public function test_legacy_lesson_opens_as_one_html_block_and_saves_byte_identical(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $module = $this->makeModule();

        $original = "<h2>Intro to SQL</h2>\r\n<p>Some \"quoted\" text &amp; more — with unicode ✓</p>\n"
            .'<div class="code-window"><div class="code-content">SELECT * FROM t; -- keep</div></div>'
            ."\n<script>alert('legacy');</script>\n  ";
        $lesson = $this->makeLegacyLesson($module, $original, 'Legacy');

        $page = $this->actingAsUser($admin)
            ->get(route('admin.modules.lessons.edit', [$module, $lesson]))
            ->assertOk()
            ->assertSee('Legacy');

        // The editor is seeded with exactly one html block holding the original.
        preg_match('#<script type="application/json" id="lesson-editor-blocks">(.*?)</script>#s', $page->getContent(), $match);
        $this->assertNotEmpty($match, 'The editor did not receive its initial blocks.');
        $seeded = json_decode($match[1], true);
        $this->assertSame([['type' => 'html', 'html' => $original]], $seeded);
        $this->assertStringContainsString('"legacy":true', $page->getContent());

        // Saving what the editor was given, untouched, leaves content byte for byte the same.
        $this->actingAsUser($admin)
            ->put(route('admin.modules.lessons.update', [$module, $lesson]), [
                'title' => 'Legacy',
                'blocks_json' => json_encode($seeded),
            ])
            ->assertRedirect(route('admin.modules.lessons.index', $module))
            ->assertSessionHas('success');

        $fresh = $lesson->fresh();
        $this->assertTrue($original === $fresh->content, 'The saved content changed.');
        $this->assertSame(strlen($original), strlen($fresh->content));
        $this->assertFalse($fresh->usesLegacyHtml());
        $this->assertSame([['type' => 'html', 'html' => $original]], json_decode($fresh->blocks, true));
    }

    public function test_update_can_add_blocks_around_the_original_html(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $module = $this->makeModule();
        $lesson = $this->makeLegacyLesson($module, '<p>Original</p>', 'Legacy');

        $this->actingAsUser($admin)
            ->put(route('admin.modules.lessons.update', [$module, $lesson]), [
                'title' => 'Legacy plus',
                'blocks_json' => json_encode([
                    ['type' => 'text', 'body' => '## New intro'],
                    ['type' => 'html', 'html' => '<p>Original</p>'],
                ]),
            ])
            ->assertRedirect(route('admin.modules.lessons.index', $module));

        $fresh = $lesson->fresh();
        $this->assertSame('Legacy plus', $fresh->title);
        $this->assertStringContainsString('<h2>New intro</h2>', $fresh->content);
        $this->assertStringContainsString('<p>Original</p>', $fresh->content);
        $this->assertStringStartsWith('<div class="lesson-text"', $fresh->content);
    }

    public function test_a_lesson_from_another_module_is_not_reachable_through_this_module(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $module = $this->makeModule();
        $other = $this->makeModule(2);
        $lesson = $this->makeLegacyLesson($other, '<p>Other</p>');

        $this->actingAsUser($admin)->get(route('admin.modules.lessons.edit', [$module, $lesson]))->assertNotFound();
        $this->actingAsUser($admin)
            ->put(route('admin.modules.lessons.update', [$module, $lesson]), ['title' => 'x', 'blocks_json' => '[]'])
            ->assertNotFound();
        $this->actingAsUser($admin)->delete(route('admin.modules.lessons.destroy', [$module, $lesson]))->assertNotFound();

        $this->assertSame('<p>Other</p>', $lesson->fresh()->content);
    }

    public function test_reorder_and_destroy(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $student = $this->makeUser(User::ROLE_USER);
        $module = $this->makeModule();
        $a = $this->makeLegacyLesson($module, '<p>a</p>', 'A', 1);
        $b = $this->makeLegacyLesson($module, '<p>b</p>', 'B', 2);
        $c = $this->makeLegacyLesson($module, '<p>c</p>', 'C', 3);

        $this->actingAsUser($admin)
            ->post(route('admin.modules.lessons.reorder', $module), ['order' => [$b->id, $c->id, $a->id]])
            ->assertRedirect(route('admin.modules.lessons.index', $module));

        $this->assertSame(1, $b->fresh()->order_index);
        $this->assertSame(2, $c->fresh()->order_index);
        $this->assertSame(3, $a->fresh()->order_index);

        DB::table('lesson_user')->insert([
            'user_id' => $student->id,
            'lesson_id' => $a->id,
            'is_completed' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAsUser($admin)
            ->delete(route('admin.modules.lessons.destroy', [$module, $a]))
            ->assertRedirect(route('admin.modules.lessons.index', $module))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('lessons', ['id' => $a->id]);

        $this->actingAsUser($admin)
            ->delete(route('admin.modules.lessons.destroy', [$module, $b]))
            ->assertRedirect(route('admin.modules.lessons.index', $module))
            ->assertSessionHas('success');
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
