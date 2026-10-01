<?php

namespace App\Support\Certificates;

/**
 * The five predefined certificate layouts (DataSensei Updates 13).
 *
 * Instructors pick one of these; they fill in the content fields but never
 * the structure. A layout is written once, here, as a list of drawing
 * instructions on an A4 landscape page (842 x 595 points, origin at the top
 * left): rectangles, lines, text and an optional logo. The same list is drawn
 * by CertificateHtml for the screen preview and by CertificatePdf for the PDF,
 * so both show the same certificate, line breaks included.
 *
 * Text is never trusted as markup: every value is drawn as plain text.
 *
 * Content fields a layout receives (all plain strings):
 *   title, learner, statement, module, issuer_name, issuer_line,
 *   signatory_name, signatory_title, date, certificate_id, verify_url,
 *   logo (a JPEG as base64, or null)
 */
final class CertificateLayouts
{
    public const WIDTH = 841.89;

    public const HEIGHT = 595.28;

    public const ACADEMIC_CLASSIC = 'academic_classic';

    public const INSTITUTIONAL = 'institutional';

    public const MINIMAL_PROFESSIONAL = 'minimal_professional';

    public const FORMAL_BORDER = 'formal_border';

    public const MODERN_ACADEMIC = 'modern_academic';

    public const DEFAULT = self::ACADEMIC_CLASSIC;

    public const LAYOUTS = [
        self::ACADEMIC_CLASSIC => [
            'name' => 'Academic Classic',
            'description' => 'Traditional academic certificate with a centered title and a formal signature area.',
        ],
        self::INSTITUTIONAL => [
            'name' => 'Institutional',
            'description' => 'Institution-focused layout with the logo and issuer at the top and a formal completion statement.',
        ],
        self::MINIMAL_PROFESSIONAL => [
            'name' => 'Minimal Professional',
            'description' => 'Simple, clean layout with minimal decoration and strong readability.',
        ],
        self::FORMAL_BORDER => [
            'name' => 'Formal Border',
            'description' => 'Traditional bordered certificate for completion and achievement awards.',
        ],
        self::MODERN_ACADEMIC => [
            'name' => 'Modern Academic',
            'description' => 'A restrained contemporary academic layout, without gradients or decorative effects.',
        ],
    ];

    /** @var list<array<string, mixed>> */
    private array $items = [];

    public static function exists(?string $key): bool
    {
        return $key !== null && array_key_exists($key, self::LAYOUTS);
    }

    public static function name(?string $key): string
    {
        return self::LAYOUTS[$key]['name'] ?? self::LAYOUTS[self::DEFAULT]['name'];
    }

    /**
     * The drawing instructions of a layout filled with content.
     *
     * @param  array<string, mixed>  $data
     * @return list<array<string, mixed>>
     */
    public static function compose(string $key, array $data): array
    {
        $data = self::normalize($data);
        $layout = new self();
        $layout->rect(0, 0, self::WIDTH, self::HEIGHT, null, '#ffffff');

        match (self::exists($key) ? $key : self::DEFAULT) {
            self::INSTITUTIONAL => $layout->institutional($data),
            self::MINIMAL_PROFESSIONAL => $layout->minimalProfessional($data),
            self::FORMAL_BORDER => $layout->formalBorder($data),
            self::MODERN_ACADEMIC => $layout->modernAcademic($data),
            default => $layout->academicClassic($data),
        };

        return $layout->items;
    }

    /** @param array<string, mixed> $data @return array<string, string|null> */
    private static function normalize(array $data): array
    {
        $clean = fn ($value) => trim(preg_replace('/\s+/u', ' ', (string) $value) ?? '');

        return [
            'title' => $clean($data['title'] ?? 'Certificate of Completion'),
            'learner' => $clean($data['learner'] ?? ''),
            'statement' => $clean($data['statement'] ?? ''),
            'module' => $clean($data['module'] ?? ''),
            'issuer_name' => $clean($data['issuer_name'] ?? 'DataSensei'),
            'issuer_line' => $clean($data['issuer_line'] ?? ''),
            'signatory_name' => $clean($data['signatory_name'] ?? ''),
            'signatory_title' => $clean($data['signatory_title'] ?? ''),
            'date' => $clean($data['date'] ?? ''),
            'certificate_id' => $clean($data['certificate_id'] ?? ''),
            'verify_url' => $clean($data['verify_url'] ?? ''),
            'logo' => is_string($data['logo'] ?? null) && $data['logo'] !== '' ? $data['logo'] : null,
        ];
    }

    // ── Layout 1: Academic Classic ───────────────────────────────────

    private function academicClassic(array $d): void
    {
        $navy = '#1f3a5f';
        $cx = self::WIDTH / 2;

        $this->rect(24, 24, self::WIDTH - 48, self::HEIGHT - 48, $navy, null, 1.6);
        $this->rect(32, 32, self::WIDTH - 64, self::HEIGHT - 64, $navy, null, 0.6);

        $this->center(mb_strtoupper($d['issuer_name']), $cx, 90, FontMetrics::SERIF, 12, '#4b5563', 680, 2.2);
        $y = $this->centerBlock($d['title'], $cx, 146, FontMetrics::SERIF_BOLD, 34, $navy, 660, 2, 38, 24);
        $this->line($cx - 50, $y + 18, $cx + 50, $y + 18, $navy, 0.8);

        $this->center('This is presented to', $cx, $y + 48, FontMetrics::SERIF_ITALIC, 13, '#374151', 600);
        $nameSize = FontMetrics::fit($d['learner'], FontMetrics::SERIF_BOLD, 30, 600, 18);
        $this->center($d['learner'], $cx, $y + 86, FontMetrics::SERIF_BOLD, $nameSize, '#111827', 640);
        $this->line($cx - 200, $y + 98, $cx + 200, $y + 98, '#9ca3af', 0.6);

        $this->centerBlock($d['statement'], $cx, $y + 128, FontMetrics::SERIF, 14, '#1f2937', 600, 4, 20, 11);

        $this->signature($cx - 170, 462, 220, 'Date of issue', $d['date'], FontMetrics::SERIF_BOLD, FontMetrics::SERIF_ITALIC, 'center');
        $this->signature($cx + 170, 462, 220, $d['signatory_title'], $d['signatory_name'], FontMetrics::SERIF_BOLD, FontMetrics::SERIF_ITALIC, 'center');

        $this->center($this->footerLine($d), $cx, 540, FontMetrics::SANS, 8, '#6b7280', 740);
    }

    // ── Layout 2: Institutional ──────────────────────────────────────

    private function institutional(array $d): void
    {
        $ink = '#0f3d3e';
        $left = 56.0;
        $right = self::WIDTH - 56;

        $this->rect(0, 0, self::WIDTH, 8, null, $ink);

        if ($d['logo'] !== null) {
            $this->image($d['logo'], $left, 46, 64, 64);
        } else {
            $this->rect($left, 46, 64, 64, $ink, null, 1.2);
            $this->text($this->initials($d['issuer_name']), $left, 86, 64, FontMetrics::SANS_BOLD, 20, $ink, 'center');
        }

        $issuer = FontMetrics::wrap($d['issuer_name'], FontMetrics::SANS_BOLD, 17, 520, 2);
        $y = 70.0;
        foreach ($issuer as $line) {
            $this->text($line, $left + 82, $y, 520, FontMetrics::SANS_BOLD, 17, '#111827');
            $y += 20;
        }
        if ($d['issuer_line'] !== '') {
            $this->text($d['issuer_line'], $left + 82, $y + 2, 600, FontMetrics::SANS, 10.5, '#4b5563');
        }
        $this->line($left, 136, $right, 136, '#d1d5db', 0.8);

        $this->text('CERTIFICATE', $left, 176, 300, FontMetrics::SANS_BOLD, 10, '#6b7280', 'left', 2.4);
        $y = $this->leftBlock($d['title'], $left, 212, FontMetrics::SANS_BOLD, 30, $ink, 720, 2, 34, 20);

        $this->text('Awarded to', $left, $y + 30, 300, FontMetrics::SANS, 11, '#6b7280');
        $nameSize = FontMetrics::fit($d['learner'], FontMetrics::SANS_BOLD, 26, 720, 16);
        $this->text($d['learner'], $left, $y + 62, 720, FontMetrics::SANS_BOLD, $nameSize, '#111827');
        $this->leftBlock($d['statement'], $left, $y + 96, FontMetrics::SANS, 13, '#1f2937', 700, 4, 19, 10);

        $this->label('Issued', $left, 470);
        $this->text($d['date'], $left, 488, 200, FontMetrics::SANS_BOLD, 12, '#111827');
        $this->label('Certificate ID', $left + 220, 470);
        $this->text($d['certificate_id'], $left + 220, 488, 240, FontMetrics::SANS_BOLD, 12, '#111827');
        $this->signature($right - 113, 462, 226, $d['signatory_title'], $d['signatory_name'], FontMetrics::SANS_BOLD, FontMetrics::SANS, 'left');

        if ($d['verify_url'] !== '') {
            $this->text('Verify this certificate at '.$d['verify_url'], $left, 552, 720, FontMetrics::SANS, 8, '#6b7280');
        }
    }

    // ── Layout 3: Minimal Professional ───────────────────────────────

    private function minimalProfessional(array $d): void
    {
        $accent = '#2563eb';
        $left = 84.0;

        $this->line(60, 72, 60, 520, $accent, 2);
        $this->text('CERTIFICATE', $left, 96, 300, FontMetrics::SANS_BOLD, 10, '#6b7280', 'left', 2.4);
        $this->text($d['issuer_name'], self::WIDTH - 360 - 60, 96, 360, FontMetrics::SANS, 10, '#6b7280', 'right');

        $y = $this->leftBlock($d['title'], $left, 160, FontMetrics::SANS_BOLD, 32, '#111827', 680, 2, 36, 20);
        $nameSize = FontMetrics::fit($d['learner'], FontMetrics::SANS, 26, 680, 16);
        $this->text($d['learner'], $left, $y + 52, 680, FontMetrics::SANS, $nameSize, '#111827');
        $this->line($left, $y + 66, $left + 320, $y + 66, '#d1d5db', 0.8);
        $this->leftBlock($d['statement'], $left, $y + 100, FontMetrics::SANS, 13, '#374151', 640, 4, 20, 10);

        $this->label('Issued', $left, 470);
        $this->text($d['date'], $left, 488, 180, FontMetrics::SANS, 12, '#111827');
        $this->label('Certificate ID', $left + 200, 470);
        $this->text($d['certificate_id'], $left + 200, 488, 240, FontMetrics::SANS, 12, '#111827');
        $this->label('Signed by', $left + 470, 470);
        $this->text($d['signatory_name'], $left + 470, 488, 230, FontMetrics::SANS_BOLD, 12, '#111827');
        $this->text($d['signatory_title'], $left + 470, 503, 230, FontMetrics::SANS, 10, '#4b5563');

        if ($d['verify_url'] !== '') {
            $this->text('Verify: '.$d['verify_url'], $left, 548, 680, FontMetrics::SANS, 8, '#6b7280');
        }
    }

    // ── Layout 4: Formal Border ──────────────────────────────────────

    private function formalBorder(array $d): void
    {
        $wine = '#5b2333';
        $cx = self::WIDTH / 2;

        $this->rect(18, 18, self::WIDTH - 36, self::HEIGHT - 36, $wine, null, 6);
        $this->rect(32, 32, self::WIDTH - 64, self::HEIGHT - 64, $wine, null, 1);
        foreach ([[32, 32], [self::WIDTH - 32, 32], [32, self::HEIGHT - 32], [self::WIDTH - 32, self::HEIGHT - 32]] as [$x, $y]) {
            $this->rect($x - 5, $y - 5, 10, 10, null, $wine);
        }

        $this->center(mb_strtoupper($d['issuer_name']), $cx, 92, FontMetrics::SERIF, 11, '#4b5563', 660, 2.4);
        $y = $this->centerBlock($d['title'], $cx, 152, FontMetrics::SERIF_BOLD, 36, $wine, 640, 2, 40, 24);
        $this->center('is hereby awarded to', $cx, $y + 40, FontMetrics::SERIF_ITALIC, 13, '#374151', 600);
        $nameSize = FontMetrics::fit($d['learner'], FontMetrics::SERIF_BOLD, 32, 600, 18);
        $this->center($d['learner'], $cx, $y + 84, FontMetrics::SERIF_BOLD, $nameSize, '#111827', 640);
        $this->line($cx - 90, $y + 98, $cx + 90, $y + 98, $wine, 0.8);
        $this->centerBlock($d['statement'], $cx, $y + 126, FontMetrics::SERIF, 14, '#1f2937', 580, 4, 20, 11);

        $this->signature($cx - 170, 458, 220, 'Date of issue', $d['date'], FontMetrics::SERIF_BOLD, FontMetrics::SERIF_ITALIC, 'center');
        $this->signature($cx + 170, 458, 220, $d['signatory_title'], $d['signatory_name'], FontMetrics::SERIF_BOLD, FontMetrics::SERIF_ITALIC, 'center');

        $this->center($this->footerLine($d), $cx, 536, FontMetrics::SANS, 8, '#6b7280', 720);
    }

    // ── Layout 5: Modern Academic ────────────────────────────────────

    private function modernAcademic(array $d): void
    {
        $panel = '#1e3a5f';
        $panelWidth = 220.0;
        $left = $panelWidth + 44;
        $width = self::WIDTH - $left - 56;

        $this->rect(0, 0, $panelWidth, self::HEIGHT, null, $panel);
        $issuer = FontMetrics::wrap($d['issuer_name'], FontMetrics::SANS_BOLD, 15, $panelWidth - 56, 3);
        $y = 74.0;
        foreach ($issuer as $line) {
            $this->text($line, 28, $y, $panelWidth - 56, FontMetrics::SANS_BOLD, 15, '#ffffff');
            $y += 19;
        }
        if ($d['issuer_line'] !== '') {
            foreach (FontMetrics::wrap($d['issuer_line'], FontMetrics::SANS, 9.5, $panelWidth - 56, 3) as $line) {
                $this->text($line, 28, $y + 4, $panelWidth - 56, FontMetrics::SANS, 9.5, '#c7d2e0');
                $y += 13;
            }
        }
        $this->line(28, 430, $panelWidth - 28, 430, '#3b5a80', 0.8);
        $this->text('ISSUED', 28, 456, 160, FontMetrics::SANS_BOLD, 8, '#c7d2e0', 'left', 1.6);
        $this->text($d['date'], 28, 472, $panelWidth - 56, FontMetrics::SANS, 11, '#ffffff');
        $this->text('CERTIFICATE ID', 28, 500, 160, FontMetrics::SANS_BOLD, 8, '#c7d2e0', 'left', 1.6);
        $idSize = FontMetrics::fit($d['certificate_id'], FontMetrics::SANS, 11, $panelWidth - 56, 7);
        $this->text($d['certificate_id'], 28, 516, $panelWidth - 56, FontMetrics::SANS, $idSize, '#ffffff');

        $this->text('Certificate', $left, 108, 300, FontMetrics::SANS_BOLD, 11, '#2f5d8a', 'left', 1.2);
        $y = $this->leftBlock($d['title'], $left, 150, FontMetrics::SANS_BOLD, 30, '#111827', $width, 2, 34, 20);
        $this->text('Awarded to', $left, $y + 32, 300, FontMetrics::SANS, 11, '#6b7280');
        $nameSize = FontMetrics::fit($d['learner'], FontMetrics::SERIF_BOLD, 30, $width, 18);
        $this->text($d['learner'], $left, $y + 68, $width, FontMetrics::SERIF_BOLD, $nameSize, '#111827');
        $this->leftBlock($d['statement'], $left, $y + 104, FontMetrics::SANS, 13, '#374151', $width - 20, 4, 20, 10);

        $this->signature($left + 113, 462, 226, $d['signatory_title'], $d['signatory_name'], FontMetrics::SANS_BOLD, FontMetrics::SANS, 'left');

        if ($d['verify_url'] !== '') {
            $this->text('Verify this certificate at '.$d['verify_url'], $left, 552, $width, FontMetrics::SANS, 8, '#6b7280');
        }
    }

    // ── Building blocks ──────────────────────────────────────────────

    /** A signature line with a name above it and a caption under it. */
    private function signature(float $centerX, float $lineY, float $width, string $caption, string $value, string $valueFont, string $captionFont, string $align): void
    {
        $x = $centerX - $width / 2;
        $this->line($x, $lineY, $x + $width, $lineY, '#6b7280', 0.7);
        $valueSize = FontMetrics::fit($value, $valueFont, 12.5, $width, 8);
        $this->text($value, $x, $lineY - 8, $width, $valueFont, $valueSize, '#111827', $align);
        $this->text($caption, $x, $lineY + 16, $width, $captionFont, 10.5, '#4b5563', $align);
    }

    private function label(string $text, float $x, float $y): void
    {
        $this->text(mb_strtoupper($text), $x, $y, 220, FontMetrics::SANS_BOLD, 8, '#6b7280', 'left', 1.4);
    }

    private function footerLine(array $d): string
    {
        $parts = array_filter([
            $d['certificate_id'] !== '' ? 'Certificate ID '.$d['certificate_id'] : null,
            $d['verify_url'] !== '' ? 'Verify at '.$d['verify_url'] : null,
        ]);

        return implode('   |   ', $parts);
    }

    private function initials(string $name): string
    {
        $letters = '';
        foreach (preg_split('/\s+/u', $name) ?: [] as $word) {
            if ($word !== '' && preg_match('/^\p{Lu}/u', $word)) {
                $letters .= mb_strtoupper(mb_substr($word, 0, 1));
            }
            if (mb_strlen($letters) === 3) {
                break;
            }
        }

        return $letters !== '' ? $letters : 'DS';
    }

    /** Centered, wrapped lines; returns the baseline of the last line. */
    private function centerBlock(string $text, float $cx, float $y, string $font, float $size, string $color, float $width, int $maxLines, float $leading, float $minSize): float
    {
        [$lines, $size, $leading] = $this->fitBlock($text, $font, $size, $width, $maxLines, $leading, $minSize);
        foreach ($lines as $index => $line) {
            $this->center($line, $cx, $y + $index * $leading, $font, $size, $color, $width);
        }

        return $y + max(0, count($lines) - 1) * $leading;
    }

    private function leftBlock(string $text, float $x, float $y, string $font, float $size, string $color, float $width, int $maxLines, float $leading, float $minSize): float
    {
        [$lines, $size, $leading] = $this->fitBlock($text, $font, $size, $width, $maxLines, $leading, $minSize);
        foreach ($lines as $index => $line) {
            $this->text($line, $x, $y + $index * $leading, $width, $font, $size, $color);
        }

        return $y + max(0, count($lines) - 1) * $leading;
    }

    /** Shrinks the type until the text fits in $maxLines, down to $minSize. */
    private function fitBlock(string $text, string $font, float $size, float $width, int $maxLines, float $leading, float $minSize): array
    {
        $ratio = $leading / $size;
        while ($size > $minSize && count(FontMetrics::wrap($text, $font, $size, $width)) > $maxLines) {
            $size -= 1;
        }

        return [FontMetrics::wrap($text, $font, $size, $width, $maxLines), $size, round($size * $ratio, 2)];
    }

    private function center(string $text, float $cx, float $y, string $font, float $size, string $color, float $width, float $spacing = 0.0): void
    {
        $this->text($text, $cx - $width / 2, $y, $width, $font, $size, $color, 'center', $spacing);
    }

    private function text(string $text, float $x, float $y, float $width, string $font, float $size, string $color, string $align = 'left', float $spacing = 0.0): void
    {
        if (trim($text) === '') {
            return;
        }

        $this->items[] = ['type' => 'text', 'text' => $text, 'x' => $x, 'y' => $y, 'width' => $width, 'font' => $font, 'size' => $size, 'color' => $color, 'align' => $align, 'spacing' => $spacing];
    }

    private function rect(float $x, float $y, float $w, float $h, ?string $stroke, ?string $fill, float $lineWidth = 1.0): void
    {
        $this->items[] = ['type' => 'rect', 'x' => $x, 'y' => $y, 'w' => $w, 'h' => $h, 'stroke' => $stroke, 'fill' => $fill, 'line' => $lineWidth];
    }

    private function line(float $x1, float $y1, float $x2, float $y2, string $color, float $width): void
    {
        $this->items[] = ['type' => 'line', 'x1' => $x1, 'y1' => $y1, 'x2' => $x2, 'y2' => $y2, 'color' => $color, 'width' => $width];
    }

    private function image(string $jpegBase64, float $x, float $y, float $w, float $h): void
    {
        $this->items[] = ['type' => 'image', 'data' => $jpegBase64, 'x' => $x, 'y' => $y, 'w' => $w, 'h' => $h];
    }
}
