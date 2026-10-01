<?php

namespace App\Services;

/**
 * Turns the block editor's blocks into the HTML the learning room shows.
 *
 * This is the one place lesson markup is produced: the admin's live preview
 * and the saved lessons.content both come through here, so what the admin
 * sees while editing is exactly what students get. The markup deliberately
 * matches the hand-written lessons (code windows, console output, the
 * "Try in Compiler →" button that learning-room.blade.php wires up) so a new
 * block sits beside an old one without looking different.
 *
 * Block shapes (all keys optional unless noted):
 *
 *   code   {label, language: python|sql|text, code*, output, try_in_compiler}
 *   text   {font: sans|mono|serif, size: sm|md|lg|xl, body*}
 *          body is light markup: "## Heading", "### Subheading", "- item",
 *          blank line between paragraphs, **bold**, *italic*, `code`
 *   table  {label, columns*: [..], rows*: [[..], ..], note}
 *   image  {src*, alt, caption, width: 25..100}
 *   html   {html*}  raw HTML, used for lessons written before the editor
 *   section {heading, subheading, body, code, walkthrough[], activity,
 *           common_mistakes[], key_points[], check_your_understanding[],
 *           html}  one lesson written with the module editor's section
 *           card (DataSensei Updates 5); html keeps a lesson's original
 *           markup, and a section holding only that markup renders it
 *           byte for byte
 */
class LessonBlockRenderer
{
    public const TYPES = [
        'code', 'text', 'table', 'image', 'html', 'section',
        // The module builder's content blocks (DataSensei Updates 6).
        'heading', 'subheading', 'paragraph', 'bulleted_list', 'numbered_list',
        'note', 'example', 'python_code', 'sql_code', 'code_snippet',
        'walkthrough', 'activity', 'mistakes', 'key_points', 'check',
        'quiz', 'preserved',
    ];

    /**
     * The content blocks the module builder offers (DataSensei Updates 6),
     * in menu order, with the name admins see. "table" and "image" are
     * shared with the older block editor; "preserved" holds original
     * formatting that could not be split into blocks and is never offered
     * in the menu.
     */
    public const BUILDER_TYPES = [
        'heading' => 'Heading',
        'subheading' => 'Subheading',
        'paragraph' => 'Paragraph',
        'bulleted_list' => 'Bulleted List',
        'numbered_list' => 'Numbered List',
        'note' => 'Important Note',
        'example' => 'Example',
        'image' => 'Image',
        'python_code' => 'Python Code',
        'sql_code' => 'SQL Code',
        'code_snippet' => 'General Code',
        'walkthrough' => 'Step-by-Step Walkthrough',
        'activity' => 'Practice Activity',
        'mistakes' => 'Common Mistakes',
        'key_points' => 'Key Points',
        'check' => 'Check Your Understanding',
        'table' => 'Table',
        'quiz' => 'Knowledge Check',
        'preserved' => 'Original Formatting',
    ];

    /** Builder blocks whose content is a list of items. */
    public const ITEM_TYPES = ['bulleted_list', 'numbered_list', 'walkthrough', 'mistakes', 'key_points', 'check'];

    public const MAX_ITEMS = 200;

    public const MAX_QUIZ_QUESTIONS = 50;

    /** The quiz styles the original lessons ship with (unchanged). */
    public const QUIZ_STYLE = <<<'HTML'
<style>
            .quiz-wrapper{display:flex;flex-direction:column;gap:24px;margin-top:40px;}
            .quiz-card{background:var(--surface2);border:1px solid var(--border);border-radius:10px;overflow:hidden;}
            .quiz-card-header{background:rgba(0,0,0,0.2);padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:flex-start;gap:12px;}
            .quiz-q-num{background:var(--accent);color:#fff;font-size:0.7rem;font-weight:700;padding:3px 8px;border-radius:4px;font-family:"JetBrains Mono",monospace;white-space:nowrap;margin-top:2px;}
            .quiz-q-text{font-size:0.95rem;font-weight:600;color:var(--text);line-height:1.5;}
            .quiz-options{padding:16px 20px;display:flex;flex-direction:column;gap:10px;}
            .quiz-option{display:flex;align-items:flex-start;gap:12px;padding:12px 16px;border-radius:7px;border:1px solid var(--border);cursor:pointer;transition:all 0.15s;font-size:0.875rem;color:var(--muted);background:transparent;text-align:left;width:100%;font-family:"Inter",sans-serif;}
            .quiz-option:hover:not(.locked){border-color:var(--border-hover);background:var(--bg);color:var(--text);}
            .quiz-option .opt-key{width:22px;height:22px;border-radius:4px;border:1px solid var(--dim);font-size:0.7rem;font-weight:700;font-family:"JetBrains Mono",monospace;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;transition:all 0.15s;}
            .quiz-option.correct{border-color:#10b981;background:rgba(16,185,129,0.08);color:var(--text);}
            .quiz-option.correct .opt-key{background:#10b981;border-color:#10b981;color:#fff;}
            .quiz-option.wrong{border-color:#ef4444;background:rgba(239,68,68,0.08);color:var(--muted);opacity:0.7;}
            .quiz-option.locked{cursor:default;}
            .quiz-explanation{display:none;margin:0 20px 20px;padding:14px 16px;background:rgba(59,130,246,0.07);border:1px solid rgba(59,130,246,0.25);border-radius:7px;font-size:0.875rem;color:var(--muted);line-height:1.7;}
            .quiz-explanation strong{color:var(--text);}
            .quiz-score-bar{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:var(--surface2);border:1px solid var(--border);border-radius:10px;font-size:0.875rem;color:var(--muted);font-weight:600;}
            .quiz-score-val{font-size:1.1rem;font-weight:700;color:#f59e0b;font-family:"JetBrains Mono",monospace;}
        </style>
HTML;

    /** The quiz script the original lessons ship with (unchanged). */
    public const QUIZ_SCRIPT = <<<'HTML'
<script>
if(typeof window.answeredQuizzes==='undefined'){window.answeredQuizzes={};}
if(typeof window.quizScores==='undefined'){window.quizScores={};}
window.checkAnswer=function(btn,qId,isCorrect,prefix){
    if(window.answeredQuizzes[qId])return;
    window.answeredQuizzes[qId]=true;
    if(typeof window.quizScores[prefix]==='undefined')window.quizScores[prefix]=0;
    const card=document.getElementById(qId);
    const allOpts=card.querySelectorAll('.quiz-option');
    allOpts.forEach(o=>o.classList.add('locked'));
    if(isCorrect){
        btn.classList.add('correct');
        window.quizScores[prefix]++;
    } else {
        btn.classList.add('wrong');
        allOpts.forEach(o=>{if(o.getAttribute('onclick').includes(',true,'))o.classList.add('correct');});
    }
    document.getElementById(qId+'-exp').style.display='block';
    document.getElementById('score_'+prefix).textContent=window.quizScores[prefix];
};
</script>
HTML;

    /** List fields of a section block, with their headings. */
    public const SECTION_LISTS = [
        'walkthrough' => 'Walkthrough',
        'common_mistakes' => 'Common Mistakes',
        'key_points' => 'Key Points',
        'check_your_understanding' => 'Check Your Understanding',
    ];

    public const FONTS = [
        'sans' => "'Inter', ui-sans-serif, system-ui, sans-serif",
        'mono' => "'JetBrains Mono', ui-monospace, Menlo, monospace",
        'serif' => "Georgia, 'Times New Roman', serif",
    ];

    public const SIZES = [
        'sm' => '0.875rem',
        'md' => '1rem',
        'lg' => '1.125rem',
        'xl' => '1.3rem',
    ];

    /** @param  list<array<string, mixed>>  $blocks */
    public function render(array $blocks): string
    {
        $html = [];

        foreach ($blocks as $block) {
            if (! is_array($block)) {
                continue;
            }

            $rendered = $this->renderBlock($block);

            if ($rendered !== '') {
                $html[] = $rendered;
            }
        }

        return implode("\n\n", $html);
    }

    /**
     * A public module lesson written with the module builder: its title is
     * the lesson heading unless the lesson starts with a Heading block of its
     * own (as the original hand-written lessons do).
     *
     * @param  list<array<string, mixed>>  $blocks
     */
    public function renderLesson(string $title, array $blocks): string
    {
        $firstType = null;
        foreach ($blocks as $block) {
            if (is_array($block) && $this->renderBlock($block) !== '') {
                $firstType = $block['type'] ?? null;
                break;
            }
        }

        $body = $this->render($blocks);

        if (trim($title) === '' || $firstType === 'heading') {
            return $body;
        }

        return '<h2>'.e(trim($title)).'</h2>'.($body !== '' ? "\n\n".$body : '');
    }

    /** @param  array<string, mixed>  $block */
    public function renderBlock(array $block): string
    {
        return match ((string) ($block['type'] ?? '')) {
            'code' => $this->code($block),
            'text' => $this->text($block),
            'table' => $this->table($block),
            'image' => $this->image($block),
            'html' => (string) ($block['html'] ?? ''),
            'section' => $this->section($block),
            'heading' => $this->plainTag('h2', $block['text'] ?? ''),
            'subheading' => $this->plainTag('h3', $block['text'] ?? ''),
            'paragraph' => $this->paragraphs((string) ($block['text'] ?? '')),
            'bulleted_list' => $this->itemList($block['items'] ?? [], 'ul'),
            'numbered_list' => $this->itemList($block['items'] ?? [], 'ol'),
            'note' => $this->textCallout($block, 'Important Note', 'accent'),
            'example' => $this->textCallout($block, 'Example', 'neutral'),
            'python_code' => $this->codeBlock($block, 'python', 'output', 'Console Output'),
            'sql_code' => $this->codeBlock($block, 'sql', 'result', 'Expected Result'),
            'code_snippet' => $this->codeBlock($block, 'text', 'output', 'Output'),
            'walkthrough' => $this->listCallout($block, 'Walkthrough', 'neutral', 'ol'),
            'activity' => $this->textCallout(['title' => '', 'text' => $block['text'] ?? ''], 'Practice Activity', 'accent'),
            'mistakes' => $this->listCallout($block, 'Common Mistakes', 'warning'),
            'key_points' => $this->listCallout($block, 'Key Points', 'success'),
            'check' => $this->listCallout($block, 'Check Your Understanding', 'neutral'),
            'quiz' => $this->quiz($block),
            'preserved' => (string) ($block['html'] ?? ''),
            default => '',
        };
    }

    /**
     * Cleans blocks coming from the editor: unknown types dropped, strings
     * trimmed, numbers bounded. The result is what gets stored.
     *
     * @param  mixed  $blocks
     * @return list<array<string, mixed>>
     */
    public function normalize(mixed $blocks): array
    {
        if (is_string($blocks)) {
            $blocks = json_decode($blocks, true);
        }

        if (! is_array($blocks)) {
            return [];
        }

        $clean = [];

        foreach ($blocks as $block) {
            if (! is_array($block)) {
                continue;
            }

            $type = (string) ($block['type'] ?? '');

            if (! in_array($type, self::TYPES, true)) {
                continue;
            }

            $clean[] = match ($type) {
                'code' => [
                    'type' => 'code',
                    'label' => $this->str($block['label'] ?? '', 160),
                    'language' => in_array($block['language'] ?? '', ['python', 'sql', 'text'], true) ? $block['language'] : 'python',
                    'code' => $this->str($block['code'] ?? '', 50000, false),
                    'output' => $this->str($block['output'] ?? '', 20000, false),
                    'try_in_compiler' => (bool) ($block['try_in_compiler'] ?? true),
                ],
                'text' => [
                    'type' => 'text',
                    'font' => array_key_exists($block['font'] ?? '', self::FONTS) ? $block['font'] : 'sans',
                    'size' => array_key_exists($block['size'] ?? '', self::SIZES) ? $block['size'] : 'md',
                    'body' => $this->str($block['body'] ?? '', 50000, false),
                ],
                'table' => [
                    'type' => 'table',
                    'label' => $this->str($block['label'] ?? '', 160),
                    'columns' => array_values(array_map(fn ($c) => $this->str($c, 500), array_slice((array) ($block['columns'] ?? []), 0, 12))),
                    'rows' => array_values(array_map(
                        fn ($row) => array_values(array_map(fn ($c) => $this->str($c, 2000), array_slice((array) $row, 0, 12))),
                        array_slice((array) ($block['rows'] ?? []), 0, 200)
                    )),
                    'note' => $this->str($block['note'] ?? '', 5000, false),
                ],
                'image' => [
                    'type' => 'image',
                    'src' => $this->imageSource((string) ($block['src'] ?? '')),
                    'alt' => $this->str($block['alt'] ?? '', 300),
                    'caption' => $this->str($block['caption'] ?? '', 1000),
                    'width' => max(25, min(100, (int) ($block['width'] ?? 100))),
                ],
                'html' => [
                    'type' => 'html',
                    'html' => (string) ($block['html'] ?? ''),
                ],
                'section' => $this->normalizeSection($block),
                default => $this->normalizeBuilderBlock($type, $block),
            };
        }

        return $clean;
    }

    /**
     * One content block of the module builder, cleaned. Every block keeps
     * exactly the fields of its type, so the same content always normalizes
     * to the same array (the editors rely on that to tell whether a section
     * was changed).
     *
     * @param  array<string, mixed>  $b
     * @return array<string, mixed>
     */
    public function normalizeBuilderBlock(string $type, array $b): array
    {
        $items = fn ($value) => array_values(array_filter(
            array_map(fn ($item) => $this->str($item, 5000, false), array_slice(is_array($value) ? array_values($value) : [], 0, self::MAX_ITEMS)),
            fn (string $item) => trim($item) !== ''
        ));
        $multi = fn ($value, int $max = 50000) => $this->str($value, $max, false);

        return match ($type) {
            'heading', 'subheading' => ['type' => $type, 'text' => $this->str($b['text'] ?? '', 500)],
            'paragraph', 'activity' => ['type' => $type, 'text' => $multi($b['text'] ?? '', 50000)],
            'note', 'example' => ['type' => $type, 'title' => $this->str($b['title'] ?? '', 189), 'text' => $multi($b['text'] ?? '', 50000)],
            'bulleted_list', 'numbered_list', 'walkthrough', 'mistakes', 'key_points', 'check' => ['type' => $type, 'items' => $items($b['items'] ?? [])],
            'python_code', 'code_snippet' => [
                'type' => $type,
                'title' => $this->str($b['title'] ?? '', 189),
                'code' => $multi($b['code'] ?? ''),
                'explanation' => $multi($b['explanation'] ?? '', 20000),
                'output' => $multi($b['output'] ?? '', 20000),
            ],
            'sql_code' => [
                'type' => $type,
                'title' => $this->str($b['title'] ?? '', 189),
                'code' => $multi($b['code'] ?? ''),
                'explanation' => $multi($b['explanation'] ?? '', 20000),
                'result' => $multi($b['result'] ?? '', 20000),
            ],
            'quiz' => $this->normalizeQuiz($b),
            'preserved' => [
                'type' => $type,
                'label' => $this->str($b['label'] ?? '', 189),
                'html' => (string) ($b['html'] ?? ''),
            ],
            default => ['type' => $type],
        };
    }

    /**
     * @param  array<string, mixed>  $b
     * @return array<string, mixed>
     */
    private function normalizeQuiz(array $b): array
    {
        $questions = [];

        foreach (array_slice(is_array($b['questions'] ?? null) ? array_values($b['questions']) : [], 0, self::MAX_QUIZ_QUESTIONS) as $question) {
            if (! is_array($question)) {
                continue;
            }

            $choices = array_values(array_map(
                fn ($choice) => $this->str($choice, 1000),
                array_slice(is_array($question['choices'] ?? null) ? array_values($question['choices']) : [], 0, 8)
            ));
            $answer = (int) ($question['answer'] ?? -1);

            $questions[] = [
                'question' => $this->str($question['question'] ?? '', 2000),
                'choices' => $choices,
                'answer' => $answer >= 0 && $answer < count($choices) ? $answer : -1,
                'explanation' => $this->str($question['explanation'] ?? '', 5000, false),
            ];
        }

        $prefix = preg_replace('/[^A-Za-z0-9_]/', '', (string) ($b['prefix'] ?? '')) ?? '';

        if ($prefix === '') {
            // A stable id for the quiz's elements, derived from its content.
            $prefix = 'kc_'.substr(md5((string) json_encode($questions)), 0, 8);
        }

        return [
            'type' => 'quiz',
            'title' => $this->str($b['title'] ?? '', 189),
            'prefix' => substr($prefix, 0, 40),
            'questions' => $questions,
        ];
    }

    /**
     * A section block from the module editor, cleaned. Lists keep only
     * non-empty lines.
     *
     * @param  array<string, mixed>  $block
     * @return array<string, mixed>
     */
    public function normalizeSection(array $block): array
    {
        $section = [
            'type' => 'section',
            'heading' => $this->str($block['heading'] ?? '', 189),
            'subheading' => $this->str($block['subheading'] ?? '', 500),
            'body' => $this->str($block['body'] ?? '', 50000, false),
            'code' => $this->str($block['code'] ?? '', 50000, false),
            'activity' => $this->str($block['activity'] ?? '', 10000, false),
            'html' => (string) ($block['html'] ?? ''),
        ];

        foreach (array_keys(self::SECTION_LISTS) as $list) {
            $section[$list] = array_values(array_filter(
                array_map(fn ($item) => $this->str($item, 2000), array_slice((array) ($block[$list] ?? []), 0, 100)),
                fn (string $item) => $item !== ''
            ));
        }

        return $section;
    }

    // ── Blocks ─────────────────────────────────────────────────────────

    /**
     * One lesson written as a section card. A section that holds only the
     * lesson's original HTML renders it unchanged, so opening and saving an
     * older lesson in the new editor does not change what students see.
     *
     * @param  array<string, mixed>  $b
     */
    private function section(array $b): string
    {
        $html = (string) ($b['html'] ?? '');
        $heading = trim((string) ($b['heading'] ?? ''));
        $subheading = trim((string) ($b['subheading'] ?? ''));
        $body = (string) ($b['body'] ?? '');
        $code = (string) ($b['code'] ?? '');
        $activity = trim((string) ($b['activity'] ?? ''));

        $lists = [];
        foreach (array_keys(self::SECTION_LISTS) as $list) {
            $lists[$list] = array_values(array_filter(array_map(
                fn ($item) => is_string($item) ? trim($item) : '',
                (array) ($b[$list] ?? [])
            ), fn (string $item) => $item !== ''));
        }

        $hasStructured = $subheading !== '' || trim($body) !== '' || trim($code) !== '' || $activity !== ''
            || array_filter($lists) !== [];

        if ($html !== '' && ! $hasStructured) {
            return $html;
        }

        $parts = [];

        if ($html !== '') {
            $parts[] = $html;
        } else {
            if ($heading !== '') {
                $parts[] = '<h2>'.e($heading).'</h2>';
            }
            if ($subheading !== '') {
                $parts[] = '<p class="lesson-subtitle" style="color:var(--muted);">'.e($subheading).'</p>';
            }
        }

        if (trim($body) !== '') {
            $parts[] = '<div class="lesson-text">'."\n".$this->markup($body)."\n".'</div>';
        }

        if (trim($code) !== '') {
            $isSql = preg_match('/^\s*(?:--[^\n]*\n\s*)*(?:SELECT|WITH|INSERT|UPDATE|DELETE|CREATE|DROP|ALTER)\b/i', $code) === 1;
            $parts[] = $this->code([
                'language' => $isSql ? 'sql' : 'python',
                'label' => 'Example',
                'code' => $code,
                'output' => '',
                'try_in_compiler' => true,
            ]);
        }

        if ($lists['walkthrough'] !== []) {
            $parts[] = $this->callout('Walkthrough', $this->listItems($lists['walkthrough'], 'ol'));
        }

        if ($activity !== '') {
            $parts[] = $this->callout('Practice Activity', '<p style="margin:0;">'.$this->inline($activity).'</p>', 'accent');
        }

        if ($lists['common_mistakes'] !== []) {
            $parts[] = $this->callout('Common Mistakes', $this->listItems($lists['common_mistakes']), 'warning');
        }

        if ($lists['key_points'] !== []) {
            $parts[] = $this->callout('Key Points', $this->listItems($lists['key_points']), 'success');
        }

        if ($lists['check_your_understanding'] !== []) {
            $parts[] = $this->callout('Check Your Understanding', $this->listItems($lists['check_your_understanding']));
        }

        return implode("\n\n", $parts);
    }

    private function listItems(array $items, string $tag = 'ul'): string
    {
        return '<'.$tag.' style="margin:0;padding-left:20px;line-height:1.6;">'
            .implode('', array_map(fn (string $item) => '<li>'.$this->inline($item).'</li>', $items))
            .'</'.$tag.'>';
    }

    private function callout(string $title, string $inner, string $tone = 'neutral'): string
    {
        [$border, $background] = match ($tone) {
            'warning' => ['var(--ds-warning-border)', 'var(--ds-warning-soft)'],
            'success' => ['var(--ds-success-border)', 'var(--ds-success-soft)'],
            'accent' => ['var(--ds-accent-border)', 'var(--ds-accent-soft)'],
            default => ['var(--border)', 'var(--surface2)'],
        };

        return '<div class="lesson-callout" style="margin:0 0 24px;padding:14px 18px;border:1px solid '.$border.';border-radius:12px;background:'.$background.';">'
            ."\n  ".'<h4 style="margin:0 0 8px;font-size:0.95rem;">'.e($title).'</h4>'
            ."\n  ".$inner
            ."\n".'</div>';
    }

    /** @param  array<string, mixed>  $b */
    private function code(array $b): string
    {
        $language = (string) ($b['language'] ?? 'python');
        $label = trim((string) ($b['label'] ?? ''));
        $prefix = strtoupper($language === 'text' ? 'CODE' : $language);

        // learning-room.blade.php routes a window to the SQL sandbox when the
        // label starts with "SQL", and to the Python IDE otherwise.
        if ($label === '') {
            $label = $prefix;
        } elseif (stripos($label, $prefix) !== 0) {
            $label = $prefix.' — '.$label;
        }

        $code = (string) ($b['code'] ?? '');
        $output = (string) ($b['output'] ?? '');
        $button = ($b['try_in_compiler'] ?? true) && $language !== 'text';

        $header = '<div style="background:rgba(0,0,0,0.2);padding:8px 16px;'
            .($button ? 'display:flex;justify-content:space-between;align-items:center;' : '')
            .'border-bottom:1px solid var(--border);">'
            .'<span style="font-size:0.75rem;color:var(--muted);font-family:\'JetBrains Mono\',monospace;">'.e($label).'</span>';

        if ($button) {
            $header .= '<button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>';
        }

        $header .= '</div>';

        $body = '<div class="code-content" style="color:#e5e7eb;'
            .($output !== '' ? 'padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;' : '')
            .'overflow-x:auto;white-space:pre;font-family:\'JetBrains Mono\',monospace;font-size:0.9rem;">'
            .$this->highlight($code, $language)
            .'</div>';

        if ($output !== '') {
            $body .= "\n".'<div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:\'JetBrains Mono\',monospace;">'
                ."\n".'<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:\'Inter\',sans-serif;font-weight:600;">'.e((string) ($b['output_label'] ?? 'Console Output')).'</span>'
                .e($output).'</div>';
        }

        return '<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">'
            ."\n  ".$header
            ."\n  ".'<div style="padding:16px;">'."\n    ".$body."\n  ".'</div>'
            ."\n".'</div>';
    }

    /** @param  array<string, mixed>  $b */
    private function text(array $b): string
    {
        $font = self::FONTS[$b['font'] ?? 'sans'] ?? self::FONTS['sans'];
        $size = self::SIZES[$b['size'] ?? 'md'] ?? self::SIZES['md'];
        $inner = $this->markup((string) ($b['body'] ?? ''));

        if ($inner === '') {
            return '';
        }

        return '<div class="lesson-text" style="font-family:'.$font.';font-size:'.$size.';">'."\n".$inner."\n".'</div>';
    }

    /** @param  array<string, mixed>  $b */
    private function table(array $b): string
    {
        $columns = array_values((array) ($b['columns'] ?? []));
        $rows = array_values((array) ($b['rows'] ?? []));
        $label = trim((string) ($b['label'] ?? ''));
        $note = trim((string) ($b['note'] ?? ''));

        if ($columns === [] && $rows === []) {
            return '';
        }

        $html = '<div class="code-window lesson-table" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">';

        if ($label !== '') {
            $html .= "\n  ".'<div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">'
                .'<span style="font-size:0.75rem;color:var(--muted);font-family:\'JetBrains Mono\',monospace;">'.e($label).'</span></div>';
        }

        $html .= "\n  ".'<div style="padding:16px;overflow-x:auto;">'
            ."\n    ".'<table style="width:100%;border-collapse:collapse;font-family:\'JetBrains Mono\',monospace;font-size:0.875rem;">';

        if ($columns !== []) {
            $html .= "\n      <thead><tr>";
            foreach ($columns as $column) {
                $html .= '<th style="text-align:left;padding:8px 12px;border-bottom:1px solid var(--border);color:#93c5fd;font-weight:600;">'.e((string) $column).'</th>';
            }
            $html .= '</tr></thead>';
        }

        if ($rows !== []) {
            $html .= "\n      <tbody>";
            foreach ($rows as $row) {
                $html .= "\n        <tr>";
                foreach (array_values((array) $row) as $index => $cell) {
                    $style = 'padding:8px 12px;border-bottom:1px solid rgba(255,255,255,0.04);vertical-align:top;color:#e5e7eb;';
                    if ($index === 0) {
                        $style .= 'color:#a7f3d0;white-space:nowrap;';
                    }
                    $html .= '<td style="'.$style.'">'.$this->inline((string) $cell).'</td>';
                }
                $html .= '</tr>';
            }
            $html .= "\n      </tbody>";
        }

        $html .= "\n    </table>";

        if ($note !== '') {
            $html .= "\n    ".'<div style="margin-top:14px;color:#9ca3af;font-size:0.85rem;font-family:\'JetBrains Mono\',monospace;white-space:pre-wrap;">'.$this->inline($note).'</div>';
        }

        return $html."\n  </div>\n</div>";
    }

    /** @param  array<string, mixed>  $b */
    private function image(array $b): string
    {
        $src = $this->imageSource((string) ($b['src'] ?? ''));

        if ($src === '') {
            return '';
        }

        $width = max(25, min(100, (int) ($b['width'] ?? 100)));
        $caption = trim((string) ($b['caption'] ?? ''));

        $html = '<figure class="lesson-image" style="margin:0 0 32px;">'
            ."\n  ".'<img src="'.e($src).'" alt="'.e((string) ($b['alt'] ?? '')).'" style="display:block;max-width:100%;width:'.$width.'%;height:auto;border-radius:8px;border:1px solid var(--border);">';

        if ($caption !== '') {
            $html .= "\n  ".'<figcaption style="margin-top:8px;color:var(--muted);font-size:0.85rem;">'.e($caption).'</figcaption>';
        }

        return $html."\n</figure>";
    }

    // ── Module builder blocks (DataSensei Updates 6) ───────────────────

    private function plainTag(string $tag, mixed $text): string
    {
        $text = trim((string) $text);

        return $text === '' ? '' : '<'.$tag.'>'.$this->inline($text).'</'.$tag.'>';
    }

    /**
     * Lesson text: a blank line starts a new paragraph, a single line break
     * stays a line break. **bold**, *italic* and `code` work inside.
     */
    public function paragraphs(string $text): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $out = [];

        foreach (preg_split('/\n\s*\n/', $text) ?: [] as $paragraph) {
            $lines = array_values(array_filter(array_map('rtrim', explode("\n", trim($paragraph))), fn ($line) => $line !== ''));

            if ($lines !== []) {
                $out[] = '<p>'.implode('<br>', array_map(fn ($line) => $this->inline(trim($line)), $lines)).'</p>';
            }
        }

        return implode("\n", $out);
    }

    /** @param  mixed  $items */
    private function itemList(mixed $items, string $tag): string
    {
        $items = array_values(array_filter(array_map(fn ($item) => is_scalar($item) ? trim((string) $item) : '', (array) $items), fn ($item) => $item !== ''));

        return $items === [] ? '' : '<'.$tag.'>'.implode('', array_map(fn ($item) => '<li>'.$this->inline($item).'</li>', $items)).'</'.$tag.'>';
    }

    /** @param  array<string, mixed>  $b */
    private function textCallout(array $b, string $defaultTitle, string $tone): string
    {
        $text = trim((string) ($b['text'] ?? ''));

        if ($text === '') {
            return '';
        }

        $title = trim((string) ($b['title'] ?? '')) ?: $defaultTitle;

        return $this->callout($title, str_replace('<p>', '<p style="margin:0 0 8px;">', $this->paragraphs($text)), $tone);
    }

    /** @param  array<string, mixed>  $b */
    private function listCallout(array $b, string $title, string $tone, string $tag = 'ul'): string
    {
        $items = array_values(array_filter(array_map(fn ($item) => is_scalar($item) ? trim((string) $item) : '', (array) ($b['items'] ?? [])), fn ($item) => $item !== ''));

        return $items === [] ? '' : $this->callout($title, $this->listItems($items, $tag), $tone);
    }

    /**
     * A Python, SQL or general code block: the same code window as the
     * hand-written lessons ("Try in Compiler" for Python, the SQL sandbox for
     * SQL), the expected output inside it, and the explanation below.
     *
     * @param  array<string, mixed>  $b
     */
    private function codeBlock(array $b, string $language, string $outputKey, string $outputLabel): string
    {
        $code = (string) ($b['code'] ?? '');
        $output = (string) ($b[$outputKey] ?? '');
        $explanation = trim((string) ($b['explanation'] ?? ''));

        if (trim($code) === '' && trim($output) === '' && $explanation === '') {
            return '';
        }

        $html = trim($code) === '' ? '' : $this->code([
            'language' => $language,
            'label' => (string) ($b['title'] ?? ''),
            'code' => $code,
            'output' => $output,
            'output_label' => $outputLabel,
            'try_in_compiler' => $language !== 'text',
        ]);

        if (trim($code) === '' && trim($output) !== '') {
            $html = $this->callout($outputLabel, '<div style="white-space:pre-wrap;font-family:\'JetBrains Mono\',monospace;font-size:0.85rem;">'.e($output).'</div>');
        }

        if ($explanation !== '') {
            $html .= ($html === '' ? '' : "\n").'<div class="lesson-text">'."\n".$this->paragraphs($explanation)."\n".'</div>';
        }

        return $html;
    }

    /**
     * A knowledge check: the same markup, styles and script as the quizzes in
     * the original lessons, so a converted quiz behaves exactly as before.
     *
     * @param  array<string, mixed>  $b
     */
    private function quiz(array $b): string
    {
        $block = $this->normalizeQuiz($b);
        $questions = array_values(array_filter($block['questions'], fn (array $q) => $q['question'] !== '' && count($q['choices']) >= 2));

        if ($questions === []) {
            return '';
        }

        $prefix = $block['prefix'];
        $title = $block['title'] !== '' ? $block['title'] : 'Knowledge Check';

        $html = self::QUIZ_STYLE
            .'<div class="quiz-wrapper" id="wrap_'.$prefix.'"><div class="quiz-score-bar"><span>'.e($title).'</span>'
            .'<span class="quiz-score-val"><span id="score_'.$prefix.'">0</span> / '.count($questions).'</span></div>';

        foreach ($questions as $index => $question) {
            $qid = $prefix.'_q'.($index + 1);
            $html .= '<div class="quiz-card" id="'.$qid.'"><div class="quiz-card-header"><span class="quiz-q-num">Q'.($index + 1).'</span>'
                .'<span class="quiz-q-text">'.e($question['question']).'</span></div><div class="quiz-options">';

            foreach ($question['choices'] as $choiceIndex => $choice) {
                $html .= '<button class="quiz-option" onclick="checkAnswer(this,\''.$qid.'\','.($choiceIndex === $question['answer'] ? 'true' : 'false').',\''.$prefix.'\')">'
                    .'<span class="opt-key">'.chr(65 + $choiceIndex).'</span> '.e($choice).'</button>';
            }

            $html .= '</div>';

            if (trim($question['explanation']) !== '') {
                $html .= '<div class="quiz-explanation" id="'.$qid.'-exp"><strong>Explanation:</strong> '.e($question['explanation']).'</div>';
            } else {
                $html .= '<div class="quiz-explanation" id="'.$qid.'-exp"></div>';
            }

            $html .= '</div>';
        }

        return $html.'</div>'.self::QUIZ_SCRIPT;
    }

    // ── Light markup for text blocks ───────────────────────────────────

    /**
     * Escapes first, then applies the markup, so nothing an author types can
     * become a tag.
     */
    public function markup(string $body): string
    {
        $lines = preg_split('/\R/', str_replace("\t", '    ', $body)) ?: [];
        $out = [];
        $paragraph = [];
        $list = [];

        $flushParagraph = function () use (&$paragraph, &$out): void {
            if ($paragraph !== []) {
                $out[] = '<p>'.implode('<br>', $paragraph).'</p>';
                $paragraph = [];
            }
        };
        $flushList = function () use (&$list, &$out): void {
            if ($list !== []) {
                $out[] = '<ul>'.implode('', array_map(fn ($item) => '<li>'.$item.'</li>', $list)).'</ul>';
                $list = [];
            }
        };

        foreach ($lines as $line) {
            $trimmed = rtrim($line);

            if (trim($trimmed) === '') {
                $flushParagraph();
                $flushList();
                continue;
            }

            if (preg_match('/^###\s+(.+)$/', $trimmed, $m)) {
                $flushParagraph();
                $flushList();
                $out[] = '<h3>'.$this->inline($m[1]).'</h3>';
                continue;
            }

            if (preg_match('/^##\s+(.+)$/', $trimmed, $m)) {
                $flushParagraph();
                $flushList();
                $out[] = '<h2>'.$this->inline($m[1]).'</h2>';
                continue;
            }

            if (preg_match('/^\s*[-*]\s+(.+)$/', $trimmed, $m)) {
                $flushParagraph();
                $list[] = $this->inline($m[1]);
                continue;
            }

            $flushList();
            $paragraph[] = $this->inline(trim($trimmed));
        }

        $flushParagraph();
        $flushList();

        return implode("\n", $out);
    }

    /** Inline markup on an escaped string: **bold**, *italic*, `code`. */
    public function inline(string $text): string
    {
        $escaped = e($text);

        // Code first, and set aside, so nothing inside backticks (such as
        // *args or **kwargs) is treated as emphasis.
        $codes = [];
        $escaped = preg_replace_callback('/`([^`]+)`/', function (array $m) use (&$codes): string {
            $codes[] = '<code>'.$m[1].'</code>';

            return "\u{E000}".(count($codes) - 1)."\u{E001}";
        }, $escaped) ?? $escaped;
        $escaped = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $escaped) ?? $escaped;
        $escaped = preg_replace('/(?<![*\w])\*(?!\s)(.+?)(?<!\s)\*(?![*\w])/s', '<em>$1</em>', $escaped) ?? $escaped;

        return preg_replace_callback("/\u{E000}(\d+)\u{E001}/u", fn (array $m) => $codes[(int) $m[1]] ?? '', $escaped) ?? $escaped;
    }

    // ── Syntax colouring, matching the hand-written lessons ────────────

    /**
     * The seeded lessons colour code by hand with the same handful of spans.
     * This reproduces that palette so an editor-written block is
     * indistinguishable from a seeded one.
     */
    public function highlight(string $code, string $language = 'python'): string
    {
        $code = str_replace(["\r\n", "\r"], "\n", $code);

        if ($language === 'text') {
            return e($code);
        }

        $keywords = $language === 'sql'
            ? ['SELECT', 'FROM', 'WHERE', 'AND', 'OR', 'NOT', 'IN', 'IS', 'NULL', 'AS', 'JOIN', 'LEFT', 'RIGHT', 'INNER', 'OUTER', 'ON', 'GROUP', 'BY', 'ORDER', 'HAVING', 'LIMIT', 'OFFSET', 'INSERT', 'INTO', 'VALUES', 'UPDATE', 'SET', 'DELETE', 'CREATE', 'TABLE', 'DROP', 'ALTER', 'DISTINCT', 'COUNT', 'SUM', 'AVG', 'MIN', 'MAX', 'CASE', 'WHEN', 'THEN', 'ELSE', 'END', 'WITH', 'UNION', 'ASC', 'DESC', 'LIKE', 'BETWEEN', 'EXISTS', 'PRIMARY', 'KEY', 'INT', 'VARCHAR', 'TEXT']
            : ['import', 'from', 'as', 'def', 'return', 'if', 'elif', 'else', 'for', 'while', 'in', 'not', 'and', 'or', 'is', 'None', 'True', 'False', 'class', 'try', 'except', 'finally', 'with', 'lambda', 'yield', 'pass', 'break', 'continue', 'raise', 'global', 'nonlocal', 'del', 'assert', 'async', 'await'];

        $builtins = $language === 'sql'
            ? []
            : ['print', 'len', 'range', 'input', 'int', 'float', 'str', 'list', 'dict', 'set', 'tuple', 'sum', 'min', 'max', 'abs', 'round', 'sorted', 'enumerate', 'zip', 'map', 'filter', 'open', 'type', 'isinstance', 'bool', 'any', 'all'];

        $comment = $language === 'sql' ? '--' : '#';

        $out = '';
        $lines = explode("\n", $code);

        foreach ($lines as $index => $line) {
            if ($index > 0) {
                $out .= "\n";
            }

            $commentAt = strpos($line, $comment);
            $codePart = $line;
            $commentPart = '';

            if ($commentAt !== false && ! $this->insideString($line, $commentAt)) {
                $codePart = substr($line, 0, $commentAt);
                $commentPart = substr($line, $commentAt);
            }

            $out .= $this->colourTokens($codePart, $keywords, $builtins, $language === 'sql');

            if ($commentPart !== '') {
                $out .= '<span style="color:#6b7280;">'.e($commentPart).'</span>';
            }
        }

        return $out;
    }

    private function insideString(string $line, int $position): bool
    {
        $single = substr_count(substr($line, 0, $position), "'") % 2 === 1;
        $double = substr_count(substr($line, 0, $position), '"') % 2 === 1;

        return $single || $double;
    }

    /** @param  list<string>  $keywords  @param  list<string>  $builtins */
    private function colourTokens(string $code, array $keywords, array $builtins, bool $caseInsensitive): string
    {
        $pattern = '/("(?:[^"\\\\]|\\\\.)*"|\'(?:[^\'\\\\]|\\\\.)*\')|(\b\d+(?:\.\d+)?\b)|(\b[A-Za-z_][A-Za-z0-9_]*\b)/';
        $out = '';
        $offset = 0;

        preg_match_all($pattern, $code, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

        foreach ($matches as $match) {
            [$token, $at] = $match[0];
            $out .= e(substr($code, $offset, $at - $offset));

            if (isset($match[1]) && $match[1][1] !== -1 && $match[1][0] !== '') {
                $out .= '<span style="color:#a7f3d0;">'.e($token).'</span>';
            } elseif (isset($match[2]) && $match[2][1] !== -1 && $match[2][0] !== '') {
                $out .= '<span style="color:#fcd34d;">'.e($token).'</span>';
            } else {
                $compare = $caseInsensitive ? strtoupper($token) : $token;

                if (in_array($compare, $keywords, true)) {
                    $out .= '<span style="color:#c4b5fd;">'.e($token).'</span>';
                } elseif (in_array($token, $builtins, true)) {
                    $out .= '<span style="color:#93c5fd;">'.e($token).'</span>';
                } else {
                    $out .= e($token);
                }
            }

            $offset = $at + strlen($token);
        }

        return $out.e(substr($code, $offset));
    }

    // ── Helpers ────────────────────────────────────────────────────────

    private function str(mixed $value, int $max, bool $singleLine = true): string
    {
        $value = is_scalar($value) ? (string) $value : '';
        $value = str_replace("\r\n", "\n", $value);

        if ($singleLine) {
            $value = trim(preg_replace('/\s+/', ' ', $value) ?? $value);
        }

        return mb_substr($value, 0, $max);
    }

    /** Only uploaded lesson images or absolute http(s) URLs; nothing else. */
    public function imageSource(string $src): string
    {
        $src = trim($src);

        if ($src === '') {
            return '';
        }

        if (preg_match('#^/uploads/lessons/[A-Za-z0-9_\-./]+\.(?:png|jpe?g|gif|webp)$#i', $src)) {
            return $src;
        }

        if (preg_match('#^https?://[^\s"\'<>]+$#i', $src)) {
            return $src;
        }

        return '';
    }
}
