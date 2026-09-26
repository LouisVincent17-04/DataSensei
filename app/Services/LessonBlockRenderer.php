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
 */
class LessonBlockRenderer
{
    public const TYPES = ['code', 'text', 'table', 'image', 'html'];

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

    /** @param  array<string, mixed>  $block */
    public function renderBlock(array $block): string
    {
        return match ((string) ($block['type'] ?? '')) {
            'code' => $this->code($block),
            'text' => $this->text($block),
            'table' => $this->table($block),
            'image' => $this->image($block),
            'html' => (string) ($block['html'] ?? ''),
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
            };
        }

        return $clean;
    }

    // ── Blocks ─────────────────────────────────────────────────────────

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
                ."\n".'<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:\'Inter\',sans-serif;font-weight:600;">Console Output</span>'
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

        // Code first, so nothing inside backticks is treated as emphasis.
        $escaped = preg_replace('/`([^`]+)`/', '<code>$1</code>', $escaped) ?? $escaped;
        $escaped = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $escaped) ?? $escaped;
        $escaped = preg_replace('/(?<![*\w])\*(?!\s)(.+?)(?<!\s)\*(?![*\w])/s', '<em>$1</em>', $escaped) ?? $escaped;

        return $escaped;
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
    private function imageSource(string $src): string
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
