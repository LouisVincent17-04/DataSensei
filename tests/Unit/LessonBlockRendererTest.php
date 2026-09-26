<?php

namespace Tests\Unit;

use App\Services\LessonBlockRenderer;
use PHPUnit\Framework\TestCase;

/**
 * The renderer is the single source of lesson markup: the admin's live
 * preview and the saved lessons.content both come from it. What is pinned
 * here is what a student ends up seeing.
 */
class LessonBlockRendererTest extends TestCase
{
    private LessonBlockRenderer $renderer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->renderer = new LessonBlockRenderer();
    }

    public function test_a_legacy_lesson_passes_through_untouched(): void
    {
        $html = '<h2>Kept</h2><div class="code-window"><script>window.x=1</script></div>';

        $this->assertSame($html, $this->renderer->render([['type' => 'html', 'html' => $html]]));
    }

    public function test_a_code_block_matches_the_seeded_lesson_markup(): void
    {
        $html = $this->renderer->render([[
            'type' => 'code',
            'label' => 'Basic Output',
            'language' => 'python',
            'code' => "print(\"Hello\")  # greet\nx = 42",
            'output' => 'Hello',
        ]]);

        $this->assertStringContainsString('class="code-window"', $html);
        $this->assertStringContainsString('PYTHON — Basic Output', $html);
        $this->assertStringContainsString('onclick="launchIDE(this)"', $html, 'The Try in Compiler button the learning room wires up.');
        $this->assertStringContainsString('class="code-content"', $html);
        $this->assertStringContainsString('Console Output</span>Hello', $html);
        // The same colour spans the hand-written lessons use.
        $this->assertStringContainsString('<span style="color:#93c5fd;">print</span>', $html);
        $this->assertStringContainsString('<span style="color:#a7f3d0;">&quot;Hello&quot;</span>', $html);
        $this->assertStringContainsString('<span style="color:#6b7280;"># greet</span>', $html);
        $this->assertStringContainsString('<span style="color:#fcd34d;">42</span>', $html);
    }

    public function test_an_sql_block_is_labelled_so_the_page_routes_it_to_the_sql_sandbox(): void
    {
        $html = $this->renderer->render([[
            'type' => 'code', 'language' => 'sql', 'label' => 'Select basics', 'code' => 'select * from t',
        ]]);

        $this->assertStringContainsString('>SQL — Select basics<', $html);
        $this->assertStringContainsString('<span style="color:#c4b5fd;">select</span>', $html, 'SQL keywords colour case-insensitively.');
    }

    public function test_a_hash_inside_a_string_is_not_a_comment(): void
    {
        $html = $this->renderer->highlight('print("#1")');

        $this->assertStringNotContainsString('color:#6b7280', $html);
    }

    public function test_text_markup_is_escaped_before_it_is_styled(): void
    {
        $html = $this->renderer->render([[
            'type' => 'text', 'font' => 'serif', 'size' => 'lg',
            'body' => "## Title <script>alert(1)</script>\n\nUse **bold**, *italic* and `printf()` here.\n\n- one\n- two",
        ]]);

        $this->assertStringContainsString('<h2>Title &lt;script&gt;alert(1)&lt;/script&gt;</h2>', $html);
        $this->assertStringContainsString('<strong>bold</strong>', $html);
        $this->assertStringContainsString('<em>italic</em>', $html);
        $this->assertStringContainsString('<code>printf()</code>', $html);
        $this->assertStringContainsString('<ul><li>one</li><li>two</li></ul>', $html);
        $this->assertStringContainsString("font-family:Georgia, 'Times New Roman', serif;font-size:1.125rem;", $html);
        $this->assertStringNotContainsString('<script>', $html);
    }

    public function test_a_table_block_renders_every_cell_escaped(): void
    {
        $html = $this->renderer->render([[
            'type' => 'table', 'label' => 'DEFINITIONS — Core Proof Vocabulary',
            'columns' => ['Term', 'Meaning'],
            'rows' => [['Axiom', 'Accepted <b>without</b> proof.'], ['Lemma', 'A `helper` theorem.']],
            'note' => 'Example: Fermat',
        ]]);

        $this->assertStringContainsString('DEFINITIONS — Core Proof Vocabulary', $html);
        $this->assertStringContainsString('<th style="text-align:left;', $html);
        $this->assertStringContainsString('Axiom', $html);
        $this->assertStringContainsString('Accepted &lt;b&gt;without&lt;/b&gt; proof.', $html);
        $this->assertStringContainsString('<code>helper</code>', $html);
        $this->assertStringContainsString('Example: Fermat', $html);
    }

    public function test_an_image_only_accepts_uploaded_or_absolute_sources(): void
    {
        $good = $this->renderer->render([['type' => 'image', 'src' => '/uploads/lessons/3/pic.png', 'alt' => 'A chart', 'caption' => 'Figure 1', 'width' => 60]]);
        $this->assertStringContainsString('<img src="/uploads/lessons/3/pic.png" alt="A chart"', $good);
        $this->assertStringContainsString('width:60%', $good);
        $this->assertStringContainsString('<figcaption', $good);

        $bad = $this->renderer->render([['type' => 'image', 'src' => 'javascript:alert(1)']]);
        $this->assertSame('', $bad);

        $traversal = $this->renderer->render([['type' => 'image', 'src' => '/uploads/lessons/../../.env']]);
        $this->assertSame('', $traversal);
    }

    public function test_normalize_drops_unknown_blocks_and_bounds_values(): void
    {
        $blocks = $this->renderer->normalize(json_encode([
            ['type' => 'evil', 'x' => 1],
            ['type' => 'image', 'src' => '/uploads/lessons/a.png', 'width' => 500],
            ['type' => 'text', 'font' => 'comic', 'size' => 'huge', 'body' => 'hi'],
            'not an array',
        ]));

        $this->assertCount(2, $blocks);
        $this->assertSame(100, $blocks[0]['width']);
        $this->assertSame('sans', $blocks[1]['font']);
        $this->assertSame('md', $blocks[1]['size']);
    }

    public function test_empty_blocks_render_nothing_rather_than_empty_frames(): void
    {
        $this->assertSame('', $this->renderer->render([
            ['type' => 'text', 'body' => "  \n\n "],
            ['type' => 'table', 'columns' => [], 'rows' => []],
            ['type' => 'image', 'src' => ''],
        ]));
    }
}
