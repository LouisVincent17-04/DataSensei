<?php

namespace App\Support\Reports;

/**
 * A small, dependency-free PDF writer for report exports (DataSensei
 * Updates 8). It lays out a title, lines of text, a summary and tables on
 * A4 landscape pages with the standard Helvetica fonts, wraps long cell text,
 * repeats a table's header on each new page and numbers the pages.
 *
 * Text is written in Windows-1252, the encoding the standard PDF fonts use;
 * characters outside it are replaced, so any name can be exported safely.
 */
final class PdfDocument
{
    private const PAGE_WIDTH = 841.89;

    private const PAGE_HEIGHT = 595.28;

    private const MARGIN = 36.0;

    private const FOOTER = 22.0;

    private const CELL_PAD = 4.0;

    /** Advance widths (1/1000 em), Windows-1252 codes 0-255. */
    private const WIDTHS = [
        'regular' => [
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
            278, 278, 355, 556, 556, 889, 667, 191, 333, 333, 389, 584, 278, 333, 278, 278,
            556, 556, 556, 556, 556, 556, 556, 556, 556, 556, 278, 278, 584, 584, 584, 556,
            1015, 667, 667, 722, 722, 667, 611, 778, 722, 278, 500, 667, 556, 833, 722, 778,
            667, 778, 722, 667, 611, 722, 667, 944, 667, 667, 611, 278, 278, 278, 469, 556,
            333, 556, 556, 500, 556, 556, 278, 556, 556, 222, 222, 500, 222, 833, 556, 556,
            556, 556, 333, 500, 278, 556, 500, 722, 500, 500, 500, 334, 260, 334, 584, 761,
            556, 0, 222, 556, 333, 1000, 556, 556, 333, 1000, 667, 333, 1000, 0, 611, 0,
            0, 222, 222, 333, 333, 350, 556, 1000, 333, 1000, 500, 333, 944, 0, 500, 667,
            278, 333, 556, 556, 556, 556, 260, 556, 333, 737, 370, 556, 584, 333, 737, 333,
            400, 584, 333, 333, 333, 556, 537, 278, 333, 333, 365, 556, 834, 834, 834, 611,
            667, 667, 667, 667, 667, 667, 1000, 722, 667, 667, 667, 667, 278, 278, 278, 278,
            722, 722, 778, 778, 778, 778, 778, 584, 778, 722, 722, 722, 722, 667, 667, 611,
            556, 556, 556, 556, 556, 556, 889, 500, 556, 556, 556, 556, 278, 278, 278, 278,
            556, 556, 556, 556, 556, 556, 556, 584, 611, 556, 556, 556, 556, 500, 556, 500,
        ],
        'bold' => [
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
            278, 333, 474, 556, 556, 889, 722, 238, 333, 333, 389, 584, 278, 333, 278, 278,
            556, 556, 556, 556, 556, 556, 556, 556, 556, 556, 333, 333, 584, 584, 584, 611,
            975, 722, 722, 722, 722, 667, 611, 778, 722, 278, 556, 722, 611, 833, 722, 778,
            667, 778, 722, 667, 611, 722, 667, 944, 667, 667, 611, 333, 278, 333, 584, 556,
            333, 556, 611, 556, 611, 556, 333, 611, 611, 278, 278, 556, 278, 889, 611, 611,
            611, 611, 389, 556, 333, 611, 556, 778, 556, 556, 500, 389, 280, 389, 584, 761,
            556, 0, 278, 556, 500, 1000, 556, 556, 333, 1000, 667, 333, 1000, 0, 611, 0,
            0, 278, 278, 500, 500, 350, 556, 1000, 333, 1000, 556, 333, 944, 0, 500, 667,
            278, 333, 556, 556, 556, 556, 280, 556, 333, 737, 370, 556, 584, 333, 737, 333,
            400, 584, 333, 333, 333, 611, 556, 278, 333, 333, 365, 556, 834, 834, 834, 611,
            722, 722, 722, 722, 722, 722, 1000, 722, 667, 667, 667, 667, 278, 278, 278, 278,
            722, 722, 778, 778, 778, 778, 778, 584, 778, 722, 722, 722, 722, 667, 667, 611,
            556, 556, 556, 556, 556, 556, 889, 556, 556, 556, 556, 556, 278, 278, 278, 278,
            611, 611, 611, 611, 611, 611, 611, 584, 611, 611, 611, 611, 611, 556, 611, 556,
        ],
    ];

    /** @var list<string> finished page content streams */
    private array $pages = [];

    private string $page = '';

    private float $y = 0.0;

    public function __construct(private readonly string $title, private readonly string $generatedNote = '')
    {
        $this->newPage();
        $this->text($this->title, 16, true, [0.07, 0.09, 0.13]);
        $this->y -= 4;
    }

    /** A line (or wrapped paragraph) of text. */
    public function line(string $text, float $size = 9.0, bool $bold = false, bool $muted = false): self
    {
        $color = $muted ? [0.38, 0.42, 0.48] : [0.1, 0.12, 0.16];
        foreach ($this->wrap($text, $size, $bold, $this->contentWidth()) as $row) {
            $this->ensureSpace($size + 4);
            $this->text($row, $size, $bold, $color);
        }

        return $this;
    }

    public function heading(string $text): self
    {
        $this->y -= 8;
        $this->ensureSpace(40);

        return $this->line($text, 12, true);
    }

    public function gap(float $points = 6.0): self
    {
        $this->y -= $points;

        return $this;
    }

    /**
     * A table. Long text wraps inside its column; the header row is repeated
     * at the top of every page the table continues on.
     *
     * @param  list<string>  $headers
     * @param  list<list<string>>  $rows
     */
    public function table(array $headers, array $rows): self
    {
        $count = count($headers);
        if ($count === 0) {
            return $this;
        }

        $widths = $this->columnWidths($headers, $rows);
        $headerLines = array_map(fn (string $h, float $w) => $this->wrap($h, 8.0, true, $w - 2 * self::CELL_PAD), $headers, $widths);
        $headerHeight = max(array_map('count', $headerLines)) * 10.0 + 2 * self::CELL_PAD;

        $drawHeader = function () use ($headerLines, $widths, $headerHeight): void {
            $x = self::MARGIN;
            $top = $this->y;
            $this->page .= sprintf("0.925 0.937 0.953 rg %.2F %.2F %.2F %.2F re f\n", self::MARGIN, $top - $headerHeight, array_sum($widths), $headerHeight);
            foreach ($headerLines as $index => $lines) {
                $lineY = $top - self::CELL_PAD - 7.5;
                foreach ($lines as $line) {
                    $this->textAt($line, $x + self::CELL_PAD, $lineY, 8.0, true, [0.1, 0.12, 0.16]);
                    $lineY -= 10.0;
                }
                $x += $widths[$index];
            }
            $this->y = $top - $headerHeight;
        };

        $this->ensureSpace($headerHeight + 24);
        $drawHeader();

        foreach ($rows as $row) {
            $cells = [];
            $lines = 1;
            for ($i = 0; $i < $count; $i++) {
                $cells[$i] = $this->wrap((string) ($row[$i] ?? ''), 8.0, false, $widths[$i] - 2 * self::CELL_PAD);
                $lines = max($lines, count($cells[$i]));
            }
            $height = $lines * 10.0 + 2 * self::CELL_PAD;

            if ($this->y - $height < self::MARGIN + self::FOOTER) {
                $this->newPage();
                $drawHeader();
            }

            $x = self::MARGIN;
            $top = $this->y;
            foreach ($cells as $i => $cellLines) {
                $lineY = $top - self::CELL_PAD - 7.5;
                foreach ($cellLines as $line) {
                    $this->textAt($line, $x + self::CELL_PAD, $lineY, 8.0, false, [0.13, 0.15, 0.19]);
                    $lineY -= 10.0;
                }
                $x += $widths[$i];
            }
            $this->y = $top - $height;
            $this->page .= sprintf("0.85 0.87 0.9 RG 0.5 w %.2F %.2F m %.2F %.2F l S\n", self::MARGIN, $this->y, self::MARGIN + array_sum($widths), $this->y);
        }

        $this->y -= 6;

        return $this;
    }

    /** The finished PDF file. */
    public function render(): string
    {
        $pages = $this->pages;
        $pages[] = $this->page;
        $total = count($pages);

        $objects = [];
        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $objects[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';

        $kids = [];
        $next = 5;
        foreach ($pages as $index => $content) {
            $footer = $this->footer($index + 1, $total);
            $stream = $content.$footer;
            $filter = '';
            if (function_exists('gzcompress')) {
                $stream = (string) gzcompress($stream, 6);
                $filter = ' /Filter /FlateDecode';
            }
            $pageId = $next++;
            $contentId = $next++;
            $kids[] = $pageId.' 0 R';
            $objects[$pageId] = sprintf(
                '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %.2F %.2F] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents %d 0 R >>',
                self::PAGE_WIDTH,
                self::PAGE_HEIGHT,
                $contentId
            );
            $objects[$contentId] = '<< /Length '.strlen($stream).$filter." >>\nstream\n".$stream."\nendstream";
        }
        $objects[2] = '<< /Type /Pages /Kids ['.implode(' ', $kids).'] /Count '.$total.' >>';
        $infoId = $next;
        $objects[$infoId] = '<< /Title ('.$this->escape("\xFE\xFF".mb_convert_encoding($this->title, 'UTF-16BE', 'UTF-8')).') /Producer (DataSensei) /CreationDate (D:'.gmdate('YmdHis').'Z) >>';
        ksort($objects);

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [];
        foreach ($objects as $id => $body) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id." 0 obj\n".$body."\nendobj\n";
        }

        $xref = strlen($pdf);
        $size = max(array_keys($objects)) + 1;
        $pdf .= "xref\n0 ".$size."\n0000000000 65535 f \n";
        for ($id = 1; $id < $size; $id++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$id] ?? 0);
        }
        $pdf .= "trailer\n<< /Size ".$size.' /Root 1 0 R /Info '.$infoId." 0 R >>\nstartxref\n".$xref."\n%%EOF\n";

        return $pdf;
    }

    // ── Layout ───────────────────────────────────────────────────────

    private function newPage(): void
    {
        if ($this->page !== '') {
            $this->pages[] = $this->page;
        }
        $this->page = '';
        $this->y = self::PAGE_HEIGHT - self::MARGIN;

        if ($this->pages !== []) {
            // Later pages carry the report title in small type.
            $this->text($this->title, 8, false, [0.45, 0.48, 0.54]);
            $this->y -= 4;
        }
    }

    private function ensureSpace(float $height): void
    {
        if ($this->y - $height < self::MARGIN + self::FOOTER) {
            $this->newPage();
        }
    }

    private function contentWidth(): float
    {
        return self::PAGE_WIDTH - 2 * self::MARGIN;
    }

    private function footer(int $page, int $total): string
    {
        $left = $this->generatedNote;
        $right = 'Page '.$page.' of '.$total;
        $rightWidth = $this->width($right, 7.5, false);

        return $this->textCommand($left, self::MARGIN, self::MARGIN - 6, 7.5, false, [0.45, 0.48, 0.54])
            .$this->textCommand($right, self::PAGE_WIDTH - self::MARGIN - $rightWidth, self::MARGIN - 6, 7.5, false, [0.45, 0.48, 0.54]);
    }

    /**
     * Column widths from the widest text in each column, fitted to the page.
     *
     * @param  list<string>  $headers
     * @param  list<list<string>>  $rows
     * @return list<float>
     */
    private function columnWidths(array $headers, array $rows): array
    {
        $available = $this->contentWidth();
        $natural = [];
        foreach ($headers as $i => $header) {
            $widest = $this->longestWord($header, 8.0, true);
            $full = $this->width($header, 8.0, true);
            foreach (array_slice($rows, 0, 300) as $row) {
                $full = max($full, $this->width((string) ($row[$i] ?? ''), 8.0, false));
            }
            $natural[$i] = [
                'want' => min($full, $available * 0.45) + 2 * self::CELL_PAD,
                'min' => max(36.0, min($widest + 2 * self::CELL_PAD, 90.0)),
            ];
        }

        $want = array_sum(array_column($natural, 'want'));
        if ($want <= $available) {
            // A small table keeps its natural size; a wider one fills the page.
            $scale = $want < $available * 0.6 ? 1.0 : $available / max($want, 1);

            return array_map(fn ($n) => $n['want'] * $scale, $natural);
        }

        // Too wide: every column keeps its minimum, the rest is shared by
        // how much text each column holds.
        $mins = array_sum(array_column($natural, 'min'));
        $extra = max(0.0, $available - $mins);
        $spare = array_sum(array_map(fn ($n) => max(0.0, $n['want'] - $n['min']), $natural));

        return array_map(
            fn ($n) => $n['min'] + ($spare > 0 ? $extra * max(0.0, $n['want'] - $n['min']) / $spare : 0.0),
            $natural
        );
    }

    // ── Text ─────────────────────────────────────────────────────────

    /** @param  array{0: float, 1: float, 2: float}  $color */
    private function text(string $text, float $size, bool $bold, array $color): void
    {
        $this->y -= $size + 2;
        $this->textAt($text, self::MARGIN, $this->y, $size, $bold, $color);
        $this->y -= 2;
    }

    /** @param  array{0: float, 1: float, 2: float}  $color */
    private function textAt(string $text, float $x, float $y, float $size, bool $bold, array $color): void
    {
        $this->page .= $this->textCommand($text, $x, $y, $size, $bold, $color);
    }

    /** @param  array{0: float, 1: float, 2: float}  $color */
    private function textCommand(string $text, float $x, float $y, float $size, bool $bold, array $color): string
    {
        if ($text === '') {
            return '';
        }

        return sprintf(
            "BT /%s %.1F Tf %.3F %.3F %.3F rg %.2F %.2F Td (%s) Tj ET\n",
            $bold ? 'F2' : 'F1',
            $size,
            $color[0],
            $color[1],
            $color[2],
            $x,
            $y,
            $this->escape($this->encode($text))
        );
    }

    /**
     * The text broken into lines that fit the width. Words longer than a line
     * are split.
     *
     * @return list<string>
     */
    private function wrap(string $text, float $size, bool $bold, float $width): array
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text) ?? '');
        if ($text === '') {
            return [''];
        }

        $lines = [];
        $current = '';
        foreach (explode(' ', $text) as $word) {
            while ($this->width($word, $size, $bold) > $width && mb_strlen($word) > 1) {
                $cut = mb_strlen($word);
                while ($cut > 1 && $this->width(mb_substr($word, 0, $cut), $size, $bold) > $width) {
                    $cut--;
                }
                if ($current !== '') {
                    $lines[] = $current;
                    $current = '';
                }
                $lines[] = mb_substr($word, 0, $cut);
                $word = mb_substr($word, $cut);
            }

            $candidate = $current === '' ? $word : $current.' '.$word;
            if ($current !== '' && $this->width($candidate, $size, $bold) > $width) {
                $lines[] = $current;
                $current = $word;
            } else {
                $current = $candidate;
            }
        }
        $lines[] = $current;

        return $lines;
    }

    private function width(string $text, float $size, bool $bold): float
    {
        $table = self::WIDTHS[$bold ? 'bold' : 'regular'];
        $encoded = $this->encode($text);
        $total = 0;
        $length = strlen($encoded);
        for ($i = 0; $i < $length; $i++) {
            $total += $table[ord($encoded[$i])] ?: 556;
        }

        return $total * $size / 1000;
    }

    private function longestWord(string $text, float $size, bool $bold): float
    {
        $widest = 0.0;
        foreach (preg_split('/\s+/u', trim($text)) ?: [] as $word) {
            $widest = max($widest, $this->width($word, $size, $bold));
        }

        return $widest;
    }

    /** UTF-8 to Windows-1252, the encoding of the standard fonts. */
    private function encode(string $text): string
    {
        $text = strtr($text, ['→' => '->', '←' => '<-', '≥' => '>=', '≤' => '<=', '✓' => 'v', "\t" => ' ']);
        $encoded = @mb_convert_encoding($text, 'Windows-1252', 'UTF-8');

        return is_string($encoded) ? $encoded : preg_replace('/[^\x20-\x7E]/', '?', $text);
    }

    private function escape(string $text): string
    {
        return strtr($text, ['\\' => '\\\\', '(' => '\\(', ')' => '\\)', "\r" => '', "\n" => ' ']);
    }
}
