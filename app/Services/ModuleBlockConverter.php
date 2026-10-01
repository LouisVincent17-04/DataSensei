<?php

namespace App\Services;

use App\Models\Lesson;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;

/**
 * Turns existing module content into the module builder's content blocks
 * (DataSensei Updates 6).
 *
 * Nothing here changes stored content. The editors call it to show an
 * existing module as blocks, the module viewers call it to display a library
 * section, and the controllers call it to tell whether a section was edited
 * (a section whose blocks still equal its converted content is left exactly
 * as it was stored).
 *
 * Sources:
 *   lesson HTML         the public modules' hand-written lessons
 *   older blocks        lessons saved with the earlier block editor or the
 *                       section editor (text, code, table, image, html,
 *                       section)
 *   library sections    content_sections of the Instructor Module Library
 *                       (heading, subheading, body, code, walkthrough,
 *                       activity, common_mistakes, key_points,
 *                       check_your_understanding)
 *
 * HTML that cannot be split into blocks (custom layouts, interactive parts
 * with their own scripts) is kept whole in an "Original Formatting" block, so
 * no text, image, code, table or quiz is ever dropped. A module's Final Exam
 * is a heading, a paragraph and a knowledge check like any other lesson
 * since the old organization lock was removed (DataSensei Updates 7).
 */
class ModuleBlockConverter
{
    /** Library section keys whose content moves into blocks when edited. */
    public const LIBRARY_CONTENT_KEYS = [
        'subheading', 'body', 'code', 'code_snippet', 'walkthrough', 'common_mistakes',
        'key_points', 'check_your_understanding', 'html', 'activity',
        'learning_activity', 'guided_activity', 'lesson_activity', 'datasensei_activity',
    ];

    private const INLINE_TAGS = ['strong', 'b', 'em', 'i', 'code', 'br', 'span', 'u', 'small', 'sup', 'sub', 'mark'];

    private const SQL_START = '/^\s*(?:--[^\n]*\n\s*)*(?:SELECT|WITH|INSERT|UPDATE|DELETE|CREATE|DROP|ALTER)\b/i';

    public function __construct(private readonly LessonBlockRenderer $renderer)
    {
    }

    // ── Entry points ───────────────────────────────────────────────────

    /**
     * A public module lesson as builder blocks.
     *
     * @return list<array<string, mixed>>
     */
    public function fromLesson(Lesson $lesson): array
    {
        $stored = is_string($lesson->blocks) && $lesson->blocks !== '' ? json_decode($lesson->blocks, true) : null;

        if (is_array($stored) && array_is_list($stored)) {
            return $this->fromStoredBlocks($stored, true);
        }

        return $this->fromHtml((string) $lesson->content);
    }

    /**
     * A library section's content as builder blocks. Its heading is the
     * section title and is not part of the blocks.
     *
     * @param  array<string, mixed>  $section
     * @return list<array<string, mixed>>
     */
    public function fromLibrarySection(array $section): array
    {
        if (isset($section['blocks']) && is_array($section['blocks'])) {
            return $this->fromStoredBlocks(array_values($section['blocks']), false);
        }

        return $this->fromSection($section, false);
    }

    /**
     * A library section rebuilt from the builder: the title and blocks
     * replace its content fields; every other key (lesson_no, ilo_codes,
     * difficulty_level, ...) is kept.
     *
     * @param  array<string, mixed>  $original
     * @param  list<array<string, mixed>>  $blocks
     * @return array<string, mixed>
     */
    public function librarySectionFromBlocks(array $original, string $title, array $blocks): array
    {
        $section = $original;

        foreach (array_merge(self::LIBRARY_CONTENT_KEYS, ModuleEditorContent::libraryActivityKeys()) as $key) {
            unset($section[$key]);
        }

        $section['heading'] = $title;
        $section['blocks'] = $this->renderer->normalize($blocks);

        return $section;
    }

    /**
     * Blocks as the builder stores them: cleaned, older block types
     * converted.
     *
     * @param  list<mixed>  $blocks
     * @return list<array<string, mixed>>
     */
    public function fromStoredBlocks(array $blocks, bool $markupBodies): array
    {
        $out = [];

        foreach ($blocks as $block) {
            if (! is_array($block)) {
                continue;
            }

            $type = (string) ($block['type'] ?? '');

            array_push($out, ...match ($type) {
                'text' => $this->fromMarkup((string) ($block['body'] ?? '')),
                'code' => [$this->codeBlock((string) ($block['language'] ?? 'python'), (string) ($block['label'] ?? ''), (string) ($block['code'] ?? ''), (string) ($block['output'] ?? ''))],
                'html' => $this->fromHtml((string) ($block['html'] ?? '')),
                'section' => $this->fromSection($block, true),
                default => in_array($type, LessonBlockRenderer::TYPES, true) ? [$block] : [],
            });
        }

        return $this->renderer->normalize($out);
    }

    // ── Library / section fields ───────────────────────────────────────

    /**
     * The fixed fields of a section (library content, or a lesson saved with
     * the Updates 5 section editor) as blocks, in the order they are shown.
     *
     * @param  array<string, mixed>  $section
     * @return list<array<string, mixed>>
     */
    public function fromSection(array $section, bool $markupBody): array
    {
        $blocks = [];

        if (is_string($section['html'] ?? null) && trim($section['html']) !== '') {
            array_push($blocks, ...$this->fromHtml($section['html']));
        }

        $subheading = $this->text($section['subheading'] ?? '');
        if (trim($subheading) !== '') {
            $blocks[] = ['type' => 'subheading', 'text' => $subheading];
        }

        $body = $this->text($section['body'] ?? '');
        if (trim($body) !== '') {
            array_push($blocks, ...($markupBody ? $this->fromMarkup($body) : [['type' => 'paragraph', 'text' => $body]]));
        }

        foreach (['code', 'code_snippet'] as $codeKey) {
            $code = $this->text($section[$codeKey] ?? '');
            if (trim($code) !== '') {
                $blocks[] = $this->codeBlock(preg_match(self::SQL_START, $code) === 1 ? 'sql' : 'python', '', $code, '');
                break;
            }
        }

        $lists = [
            'walkthrough' => 'walkthrough',
        ];
        foreach ($lists as $key => $type) {
            $items = $this->items($section[$key] ?? []);
            if ($items !== []) {
                $blocks[] = ['type' => $type, 'items' => $items];
            }
        }

        foreach (array_merge(['activity'], ModuleEditorContent::libraryActivityKeys()) as $key) {
            $activity = $this->text($section[$key] ?? '');
            if (trim($activity) !== '') {
                $blocks[] = ['type' => 'activity', 'text' => $activity];
                break;
            }
        }

        foreach (['common_mistakes' => 'mistakes', 'key_points' => 'key_points', 'check_your_understanding' => 'check'] as $key => $type) {
            $items = $this->items($section[$key] ?? []);
            if ($items !== []) {
                $blocks[] = ['type' => $type, 'items' => $items];
            }
        }

        return $this->renderer->normalize($blocks);
    }

    /**
     * The earlier editors' light markup ("## Heading", "### Subheading",
     * "- item", paragraphs) as blocks.
     *
     * @return list<array<string, mixed>>
     */
    public function fromMarkup(string $body): array
    {
        $blocks = [];
        $paragraph = [];
        $list = [];

        $flushParagraph = function () use (&$paragraph, &$blocks): void {
            if ($paragraph !== []) {
                $last = end($blocks);
                if ($last !== false && $last['type'] === 'paragraph' && ($last['_open'] ?? false)) {
                    $blocks[key($blocks)]['text'] .= "\n\n".implode("\n", $paragraph);
                } else {
                    $blocks[] = ['type' => 'paragraph', 'text' => implode("\n", $paragraph), '_open' => true];
                }
                $paragraph = [];
            }
        };
        $flushList = function () use (&$list, &$blocks): void {
            if ($list !== []) {
                $blocks[] = ['type' => 'bulleted_list', 'items' => $list];
                $list = [];
            }
        };
        $close = function () use (&$blocks): void {
            foreach ($blocks as $i => $block) {
                unset($blocks[$i]['_open']);
            }
        };

        foreach (preg_split('/\R/', str_replace("\t", '    ', $body)) ?: [] as $line) {
            $trimmed = rtrim($line);

            if (trim($trimmed) === '') {
                $flushParagraph();
                $flushList();
                continue;
            }

            if (preg_match('/^(#{2,3})\s+(.+)$/', $trimmed, $m)) {
                $flushParagraph();
                $flushList();
                $close();
                $blocks[] = ['type' => strlen($m[1]) === 2 ? 'heading' : 'subheading', 'text' => trim($m[2])];
                continue;
            }

            if (preg_match('/^\s*[-*]\s+(.+)$/', $trimmed, $m)) {
                $flushParagraph();
                $close();
                $list[] = trim($m[1]);
                continue;
            }

            $flushList();
            $paragraph[] = trim($trimmed);
        }

        $flushParagraph();
        $flushList();
        $close();

        return $this->renderer->normalize($blocks);
    }

    // ── HTML ───────────────────────────────────────────────────────────

    /**
     * Hand-written lesson HTML as blocks. Headings, paragraphs, lists, code
     * windows, tables, images and knowledge checks become their own blocks;
     * anything else is kept whole in an "Original Formatting" block.
     *
     * @return list<array<string, mixed>>
     */
    public function fromHtml(string $html): array
    {
        if (trim($html) === '') {
            return [];
        }

        $doc = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"?><div id="ds-block-root">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET | LIBXML_COMPACT);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $doc->getElementById('ds-block-root');

        if (! $root) {
            return [['type' => 'preserved', 'label' => 'Original formatting', 'html' => $html]];
        }

        $blocks = $this->convertChildren($doc, $root);

        return $this->renderer->normalize($blocks);
    }

    /** @return list<array<string, mixed>> */
    private function convertChildren(DOMDocument $doc, DOMNode $parent): array
    {
        $xpath = new DOMXPath($doc);
        $nodes = [];
        foreach ($parent->childNodes as $node) {
            $nodes[] = $node;
        }

        // Quiz styles and script are supplied by the quiz block itself when
        // they are the standard ones the lessons were written with.
        $hasQuiz = false;
        foreach ($nodes as $node) {
            if ($node instanceof DOMElement && $this->hasClass($node, 'quiz-wrapper') && $this->quizFrom($xpath, $node) !== null) {
                $hasQuiz = true;
                break;
            }
        }

        $blocks = [];
        $preserved = [];

        $flush = function () use (&$preserved, &$blocks): void {
            if ($preserved === []) {
                return;
            }

            $html = trim(implode('', $preserved));
            if ($html !== '') {
                $blocks[] = ['type' => 'preserved', 'label' => $this->preservedLabel($html), 'html' => $html];
            }
            $preserved = [];
        };

        foreach ($nodes as $node) {
            if ($node->nodeType === XML_COMMENT_NODE) {
                continue;
            }

            if ($node->nodeType === XML_TEXT_NODE || $node->nodeType === XML_CDATA_SECTION_NODE) {
                $text = trim(preg_replace('/\s+/u', ' ', $node->textContent) ?? '');
                if ($text === '') {
                    if ($preserved !== []) {
                        $preserved[] = $node->textContent;
                    }
                    continue;
                }

                if ($this->faithful($text, $node)) {
                    $flush();
                    $blocks[] = ['type' => 'paragraph', 'text' => $text];
                } else {
                    $preserved[] = htmlspecialchars($node->textContent, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                }
                continue;
            }

            if (! $node instanceof DOMElement) {
                continue;
            }

            $converted = $this->convertElement($doc, $xpath, $node, $hasQuiz);

            if ($converted === null) {
                $preserved[] = $doc->saveHTML($node);
                continue;
            }

            $flush();
            array_push($blocks, ...$converted);
        }

        $flush();

        return $blocks;
    }

    /**
     * One top-level element as blocks, or null to keep it as original
     * formatting.
     *
     * @return list<array<string, mixed>>|null
     */
    private function convertElement(DOMDocument $doc, DOMXPath $xpath, DOMElement $node, bool $hasQuiz): ?array
    {
        $tag = strtolower($node->nodeName);

        switch ($tag) {
            case 'h1':
            case 'h2':
                $text = $this->inlineMarkup($node);

                return $text === null ? null : ($text === '' ? [] : [['type' => 'heading', 'text' => $this->oneLine($text)]]);
            case 'h3':
            case 'h4':
            case 'h5':
            case 'h6':
                $text = $this->inlineMarkup($node);

                return $text === null ? null : ($text === '' ? [] : [['type' => 'subheading', 'text' => $this->oneLine($text)]]);
            case 'p':
                $text = $this->inlineMarkup($node);

                return $text === null ? null : (trim($text) === '' ? [] : [['type' => 'paragraph', 'text' => $this->cleanParagraph($text)]]);
            case 'ul':
            case 'ol':
                $items = $this->listItems($node);

                return $items === null ? null : [['type' => $tag === 'ul' ? 'bulleted_list' : 'numbered_list', 'items' => $items]];
            case 'blockquote':
                $text = $this->inlineMarkup($node);

                return $text === null || trim($text) === '' ? null : [['type' => 'note', 'title' => '', 'text' => $this->cleanParagraph($text)]];
            case 'pre':
                $code = $node->textContent;

                return trim($code) === '' ? [] : [$this->codeBlock($this->languageOf($node, $code), '', $code, '')];
            case 'img':
                $src = $this->renderer->imageSource($node->getAttribute('src'));

                return $src === '' ? null : [['type' => 'image', 'src' => $src, 'alt' => $node->getAttribute('alt'), 'caption' => '', 'width' => 100]];
            case 'table':
                $table = $this->tableFrom($node, '');

                return $table === null ? null : [$table];
            case 'style':
                return $hasQuiz && $this->isStandardQuizAsset($node->textContent, LessonBlockRenderer::QUIZ_STYLE) ? [] : null;
            case 'script':
                return $hasQuiz && $this->isStandardQuizAsset($node->textContent, LessonBlockRenderer::QUIZ_SCRIPT) ? [] : null;
            case 'div':
            case 'section':
            case 'article':
                if ($this->hasClass($node, 'quiz-wrapper')) {
                    $quiz = $this->quizFrom($xpath, $node);

                    return $quiz === null ? null : [$quiz];
                }

                if ($this->hasClass($node, 'code-window')) {
                    return $this->codeWindowFrom($xpath, $node);
                }

                if ($this->hasClass($node, 'lesson-text') || $this->hasClass($node, 'lesson-callout') === false && $this->isPlainWrapper($node)) {
                    return $this->convertChildren($doc, $node);
                }

                // A styled box holding only a table (the lessons' comparison tables).
                $tables = $xpath->query('.//table', $node);
                if ($tables !== false && $tables->length === 1 && $this->onlyContains($node, $tables->item(0))) {
                    $table = $this->tableFrom($tables->item(0), '');

                    return $table === null ? null : [$table];
                }

                return null;
            default:
                return null;
        }
    }

    /**
     * The lessons' code windows: "PYTHON — Title" / "SQL — Title" header,
     * the code, and the console output below it; or a table window.
     *
     * @return list<array<string, mixed>>|null
     */
    private function codeWindowFrom(DOMXPath $xpath, DOMElement $window): ?array
    {
        $label = '';
        $headerNotes = [];
        $labelNode = $xpath->query('./div[1]//span', $window);
        if ($labelNode !== false && $labelNode->length > 0) {
            $label = trim($labelNode->item(0)->textContent);
            // Any other text in the header (such as "Reference example. Needs
            // plotly ...") is kept as the explanation.
            for ($i = 1; $i < $labelNode->length; $i++) {
                $note = trim(preg_replace('/\s+/u', ' ', $labelNode->item($i)->textContent) ?? '');
                if ($note !== '') {
                    $headerNotes[] = $note;
                }
            }
        }

        $tables = $xpath->query('.//table', $window);
        if ($tables !== false && $tables->length === 1) {
            $table = $this->tableFrom($tables->item(0), $label);
            if ($table === null) {
                return null;
            }

            $note = $xpath->query('.//table/following-sibling::div', $window);
            if ($note !== false && $note->length === 1) {
                $table['note'] = trim($note->item(0)->textContent);
            }

            return [$table];
        }

        $codeNodes = $xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " code-content ")]', $window);
        if ($codeNodes === false || $codeNodes->length !== 1) {
            return null;
        }

        $codeNode = $codeNodes->item(0);
        $code = $codeNode->textContent;

        $output = '';
        $sibling = $codeNode->nextSibling;
        while ($sibling && $sibling->nodeType !== XML_ELEMENT_NODE) {
            $sibling = $sibling->nextSibling;
        }
        if ($sibling instanceof DOMElement) {
            $clone = $sibling->cloneNode(true);
            // Drop the "Console Output" caption.
            foreach (iterator_to_array($clone->childNodes) as $child) {
                if ($child instanceof DOMElement && strtolower($child->nodeName) === 'span' && preg_match('/output|result/i', $child->textContent)) {
                    $clone->removeChild($child);
                    break;
                }
            }
            $output = preg_replace('/^\R/', '', $clone->textContent) ?? $clone->textContent;
        }

        $language = 'python';
        $title = $label;
        if (preg_match('/^\s*(PYTHON|SQL|CODE|TEXT)\b\s*(?:[—–\-:|·]\s*)?(.*)$/iu', $label, $m)) {
            $language = match (strtoupper($m[1])) {
                'SQL' => 'sql',
                'PYTHON' => 'python',
                default => 'text',
            };
            $title = trim($m[2]);
        } elseif (preg_match(self::SQL_START, $code) === 1) {
            $language = 'sql';
        }

        $block = $this->codeBlock($language, $title, $code, $output);
        if ($headerNotes !== []) {
            $block['explanation'] = implode("\n\n", $headerNotes);
        }

        return [$block];
    }

    /** @return array<string, mixed> */
    private function codeBlock(string $language, string $title, string $code, string $output): array
    {
        $code = str_replace(["\r\n", "\r"], "\n", $code);
        $output = rtrim(str_replace(["\r\n", "\r"], "\n", $output));

        return match ($language) {
            'sql' => ['type' => 'sql_code', 'title' => $title, 'code' => $code, 'explanation' => '', 'result' => $output],
            'text' => ['type' => 'code_snippet', 'title' => $title, 'code' => $code, 'explanation' => '', 'output' => $output],
            default => ['type' => 'python_code', 'title' => $title, 'code' => $code, 'explanation' => '', 'output' => $output],
        };
    }

    /**
     * A knowledge check (quiz-wrapper) as a quiz block, or null when its
     * markup is not the standard one.
     *
     * @return array<string, mixed>|null
     */
    private function quizFrom(DOMXPath $xpath, DOMElement $wrapper): ?array
    {
        $prefix = preg_replace('/^wrap_/', '', $wrapper->getAttribute('id')) ?? '';
        $title = '';
        $titleNode = $xpath->query('./div[contains(@class,"quiz-score-bar")]/span[1]', $wrapper);
        if ($titleNode !== false && $titleNode->length > 0) {
            $title = trim($titleNode->item(0)->textContent);
        }

        $cards = $xpath->query('./div[contains(concat(" ", normalize-space(@class), " "), " quiz-card ")]', $wrapper);
        if ($cards === false || $cards->length === 0) {
            return null;
        }

        $questions = [];
        foreach ($cards as $card) {
            $text = $xpath->query('.//*[contains(@class,"quiz-q-text")]', $card);
            $options = $xpath->query('.//button[contains(@class,"quiz-option")]', $card);
            if ($text === false || $text->length !== 1 || $options === false || $options->length < 2) {
                return null;
            }

            $choices = [];
            $answer = -1;
            foreach ($options as $index => $option) {
                $clone = $option->cloneNode(true);
                foreach (iterator_to_array($clone->childNodes) as $child) {
                    if ($child instanceof DOMElement && str_contains($child->getAttribute('class'), 'opt-key')) {
                        $clone->removeChild($child);
                    }
                }
                $choices[] = trim(preg_replace('/\s+/u', ' ', $clone->textContent) ?? '');
                if (str_contains(str_replace(' ', '', $option->getAttribute('onclick')), ',true,')) {
                    $answer = $index;
                }
            }

            $explanation = '';
            $exp = $xpath->query('.//*[contains(@class,"quiz-explanation")]', $card);
            if ($exp !== false && $exp->length > 0) {
                $explanation = trim(preg_replace('/^\s*Explanation:\s*/i', '', preg_replace('/\s+/u', ' ', $exp->item(0)->textContent) ?? '') ?? '');
            }

            $questions[] = [
                'question' => trim(preg_replace('/\s+/u', ' ', $text->item(0)->textContent) ?? ''),
                'choices' => $choices,
                'answer' => $answer,
                'explanation' => $explanation,
            ];
        }

        return ['type' => 'quiz', 'title' => $title === 'Knowledge Check' ? '' : $title, 'prefix' => $prefix, 'questions' => $questions];
    }

    /**
     * A plain table as a table block (first row of header cells as the
     * columns), or null when it has merged or nested cells.
     *
     * @return array<string, mixed>|null
     */
    private function tableFrom(DOMElement $table, string $label): ?array
    {
        $columns = [];
        $rows = [];

        foreach ($table->getElementsByTagName('tr') as $tr) {
            $cells = [];
            $isHeader = true;

            foreach ($tr->childNodes as $cell) {
                if (! $cell instanceof DOMElement) {
                    continue;
                }

                $tag = strtolower($cell->nodeName);
                if (! in_array($tag, ['td', 'th'], true) || $cell->hasAttribute('colspan') || $cell->hasAttribute('rowspan')) {
                    return null;
                }

                $text = $this->inlineMarkup($cell);
                if ($text === null) {
                    return null;
                }

                $isHeader = $isHeader && $tag === 'th';
                $cells[] = $this->oneLine($text);
            }

            if ($cells === []) {
                continue;
            }

            if ($columns === [] && $rows === [] && $isHeader) {
                $columns = $cells;
            } else {
                $rows[] = $cells;
            }
        }

        if ($columns === [] && $rows === []) {
            return null;
        }

        return ['type' => 'table', 'label' => $label, 'columns' => $columns, 'rows' => $rows, 'note' => ''];
    }

    /**
     * The list items of a ul/ol, or null when an item holds more than text.
     *
     * @return list<string>|null
     */
    private function listItems(DOMElement $list): ?array
    {
        $items = [];

        foreach ($list->childNodes as $child) {
            if ($child->nodeType === XML_TEXT_NODE && trim($child->textContent) === '') {
                continue;
            }

            if (! $child instanceof DOMElement || strtolower($child->nodeName) !== 'li') {
                return null;
            }

            $text = $this->inlineMarkup($child);
            if ($text === null) {
                return null;
            }

            $text = $this->oneLine($text);
            if ($text !== '') {
                $items[] = $text;
            }
        }

        return $items;
    }

    /**
     * An element's content as light markup (**bold**, *italic*, `code`,
     * line breaks), or null when it holds anything that markup cannot keep
     * (links, images, nested blocks) or text the markup would change.
     */
    private function inlineMarkup(DOMNode $node): ?string
    {
        $markup = $this->buildInline($node);

        return $markup !== null && $this->faithful($markup, $node) ? $markup : null;
    }

    /**
     * True when the markup shows exactly the element's text: no asterisk or
     * backtick in the text is taken for formatting and nothing is lost.
     */
    private function faithful(string $markup, DOMNode $node): bool
    {
        $rendered = str_replace(['<br>', "\n"], ' ', $this->renderer->inline(str_replace("\n", ' ', $markup)));
        $shown = html_entity_decode(strip_tags($rendered), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $squash = fn (string $text) => preg_replace('/\s+/u', '', $text) ?? $text;

        return $squash($shown) === $squash($node->textContent);
    }

    private function buildInline(DOMNode $node): ?string
    {
        $out = '';

        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_TEXT_NODE || $child->nodeType === XML_CDATA_SECTION_NODE) {
                // Source line breaks are only spacing in HTML; <br> makes a real one.
                $out .= preg_replace('/[ \t\r\n\f]+/', ' ', $child->textContent) ?? $child->textContent;
                continue;
            }

            if ($child->nodeType === XML_COMMENT_NODE) {
                continue;
            }

            if (! $child instanceof DOMElement) {
                return null;
            }

            $tag = strtolower($child->nodeName);
            if (! in_array($tag, self::INLINE_TAGS, true)) {
                return null;
            }

            if ($tag === 'br') {
                $out .= "\n";
                continue;
            }

            if ($tag === 'code') {
                $code = $child->textContent;
                if (str_contains($code, '`') || str_contains($code, "\n")) {
                    return null;
                }
                $out .= $code === '' ? '' : '`'.$code.'`';
                continue;
            }

            $inner = $this->buildInline($child);
            if ($inner === null) {
                return null;
            }

            $out .= match ($tag) {
                'strong', 'b' => trim($inner) === '' ? $inner : $this->wrapEmphasis($inner, '**'),
                'em', 'i' => trim($inner) === '' ? $inner : $this->wrapEmphasis($inner, '*'),
                default => $inner,
            };
        }

        return $out;
    }

    /** "**text**" keeping surrounding spaces outside the markers. */
    private function wrapEmphasis(string $inner, string $marker): string
    {
        preg_match('/^(\s*)(.*?)(\s*)$/su', $inner, $m);

        return $m[1].$marker.$m[2].$marker.$m[3];
    }

    private function cleanParagraph(string $text): string
    {
        $lines = array_map(fn ($line) => trim(preg_replace('/[ \t\x{00A0}]+/u', ' ', $line) ?? $line), explode("\n", str_replace(["\r\n", "\r"], "\n", $text)));

        return trim(implode("\n", $lines));
    }

    private function oneLine(string $text): string
    {
        return trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
    }

    private function isStandardQuizAsset(string $content, string $standard): bool
    {
        $normalize = fn (string $value) => preg_replace('/\s+/', ' ', trim(preg_replace('#^\s*<(style|script)>|</(style|script)>\s*$#', '', trim($value)) ?? '')) ?? '';

        return $normalize($content) === $normalize($standard);
    }

    private function hasClass(DOMElement $node, string $class): bool
    {
        return in_array($class, preg_split('/\s+/', trim($node->getAttribute('class'))) ?: [], true);
    }

    /** A div with no attributes that only groups other blocks. */
    private function isPlainWrapper(DOMElement $node): bool
    {
        return ! $node->hasAttributes();
    }

    /** True when the element holds only this descendant (and whitespace). */
    private function onlyContains(DOMElement $node, DOMNode $descendant): bool
    {
        $text = trim($node->textContent);

        return $text === trim($descendant->textContent);
    }

    private function languageOf(DOMElement $node, string $code): string
    {
        $classes = strtolower($node->getAttribute('class').' '.($node->firstChild instanceof DOMElement ? $node->firstChild->getAttribute('class') : ''));

        if (str_contains($classes, 'sql') || preg_match(self::SQL_START, $code) === 1) {
            return 'sql';
        }

        return str_contains($classes, 'python') ? 'python' : 'text';
    }

    private function preservedLabel(string $html): string
    {
        return match (true) {
            str_contains($html, 'quiz-wrapper') => 'Knowledge check (interactive)',
            str_contains($html, '<table') => 'Table with original formatting',
            str_contains($html, '<img') => 'Image with original formatting',
            default => 'Original formatting',
        };
    }

    private function text(mixed $value): string
    {
        return is_scalar($value) ? str_replace(["\r\n", "\r"], "\n", (string) $value) : '';
    }

    /** @return list<string> */
    private function items(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return array_values(array_filter(array_map(fn ($item) => is_scalar($item) ? (string) $item : '', $value), fn (string $item) => trim($item) !== ''));
    }
}
