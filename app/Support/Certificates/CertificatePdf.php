<?php

namespace App\Support\Certificates;

/**
 * Draws a certificate layout as a one-page A4 landscape PDF (DataSensei
 * Updates 13). Dependency-free, like the report PDFs (PdfDocument): the six
 * standard PDF fonts, vector lines and rectangles, and a JPEG logo when the
 * layout has one. The drawing instructions come from CertificateLayouts, the
 * same ones the screen preview uses.
 */
final class CertificatePdf
{
    private const FONT_IDS = [
        FontMetrics::SANS => 'F1',
        FontMetrics::SANS_BOLD => 'F2',
        FontMetrics::SANS_ITALIC => 'F3',
        FontMetrics::SERIF => 'F4',
        FontMetrics::SERIF_BOLD => 'F5',
        FontMetrics::SERIF_ITALIC => 'F6',
    ];

    /**
     * @param  list<array<string, mixed>>  $items  CertificateLayouts::compose()
     */
    public static function render(array $items, string $title = 'Certificate'): string
    {
        $content = '';
        $images = [];

        foreach ($items as $item) {
            $content .= match ($item['type']) {
                'rect' => self::rect($item),
                'line' => self::line($item),
                'text' => self::text($item),
                'image' => self::image($item, $images),
                default => '',
            };
        }

        return self::document($content, $images, $title);
    }

    private static function rect(array $r): string
    {
        $y = CertificateLayouts::HEIGHT - $r['y'] - $r['h'];
        $ops = 'q ';
        if ($r['fill'] !== null) {
            $ops .= self::color($r['fill'], 'rg');
        }
        if ($r['stroke'] !== null) {
            $ops .= self::color($r['stroke'], 'RG').self::n($r['line']).' w ';
        }
        $paint = $r['fill'] !== null && $r['stroke'] !== null ? 'B' : ($r['fill'] !== null ? 'f' : 'S');

        return $ops.self::n($r['x']).' '.self::n($y).' '.self::n($r['w']).' '.self::n($r['h']).' re '.$paint." Q\n";
    }

    private static function line(array $l): string
    {
        $h = CertificateLayouts::HEIGHT;

        return 'q '.self::color($l['color'], 'RG').self::n($l['width']).' w '
            .self::n($l['x1']).' '.self::n($h - $l['y1']).' m '.self::n($l['x2']).' '.self::n($h - $l['y2'])." l S Q\n";
    }

    private static function text(array $t): string
    {
        $font = isset(self::FONT_IDS[$t['font']]) ? $t['font'] : FontMetrics::SANS;
        $width = FontMetrics::width($t['text'], $font, (float) $t['size'], (float) $t['spacing']);
        $x = match ($t['align']) {
            'center' => $t['x'] + ($t['width'] - $width) / 2,
            'right' => $t['x'] + $t['width'] - $width,
            default => $t['x'],
        };

        return 'BT '.self::color($t['color'], 'rg').'/'.self::FONT_IDS[$font].' '.self::n($t['size']).' Tf '
            .self::n($t['spacing']).' Tc '.self::n($x).' '.self::n(CertificateLayouts::HEIGHT - $t['y']).' Td ('
            .self::escape(FontMetrics::encode($t['text'])).") Tj ET\n";
    }

    /** @param list<array{data: string, width: int, height: int, gray: bool}> $images */
    private static function image(array $i, array &$images): string
    {
        $jpeg = base64_decode((string) $i['data'], true);
        $size = $jpeg !== false ? @getimagesizefromstring($jpeg) : false;
        if ($jpeg === false || $size === false || ($size[2] ?? null) !== IMAGETYPE_JPEG) {
            return '';
        }

        $images[] = ['data' => $jpeg, 'width' => (int) $size[0], 'height' => (int) $size[1], 'gray' => ($size['channels'] ?? 3) === 1];
        $name = 'Im'.count($images);
        $y = CertificateLayouts::HEIGHT - $i['y'] - $i['h'];

        return 'q '.self::n($i['w']).' 0 0 '.self::n($i['h']).' '.self::n($i['x']).' '.self::n($y).' cm /'.$name." Do Q\n";
    }

    /** @param list<array{data: string, width: int, height: int, gray: bool}> $images */
    private static function document(string $content, array $images, string $title): string
    {
        $objects = [];
        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[2] = '<< /Type /Pages /Kids [3 0 R] /Count 1 >>';

        $fontRefs = '';
        $next = 5;
        foreach (self::FONT_IDS as $baseFont => $id) {
            $objects[$next] = '<< /Type /Font /Subtype /Type1 /BaseFont /'.$baseFont.' /Encoding /WinAnsiEncoding >>';
            $fontRefs .= '/'.$id.' '.$next.' 0 R ';
            $next++;
        }

        $imageRefs = '';
        foreach ($images as $index => $image) {
            $objects[$next] = "<< /Type /XObject /Subtype /Image /Width {$image['width']} /Height {$image['height']} /ColorSpace /"
                .($image['gray'] ? 'DeviceGray' : 'DeviceRGB').' /BitsPerComponent 8 /Filter /DCTDecode /Length '.strlen($image['data'])." >>\nstream\n"
                .$image['data']."\nendstream";
            $imageRefs .= '/Im'.($index + 1).' '.$next.' 0 R ';
            $next++;
        }

        $infoId = $next;
        $objects[$infoId] = '<< /Title ('.self::escape(FontMetrics::encode($title)).') /Producer (DataSensei) /CreationDate (D:'.gmdate('YmdHis')."Z) >>";

        $resources = '<< /Font << '.$fontRefs.'>>'.($imageRefs !== '' ? ' /XObject << '.$imageRefs.'>>' : '').' >>';
        $objects[3] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 '.self::n(CertificateLayouts::WIDTH).' '.self::n(CertificateLayouts::HEIGHT)
            .'] /Resources '.$resources.' /Contents 4 0 R >>';

        $stream = $content;
        $filter = '';
        if (function_exists('gzcompress')) {
            $compressed = gzcompress($content, 6);
            if ($compressed !== false) {
                $stream = $compressed;
                $filter = ' /Filter /FlateDecode';
            }
        }
        $objects[4] = '<< /Length '.strlen($stream).$filter." >>\nstream\n".$stream."\nendstream";

        ksort($objects);
        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [];
        foreach ($objects as $id => $body) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id." 0 obj\n".$body."\nendobj\n";
        }

        $count = max(array_keys($objects)) + 1;
        $xref = strlen($pdf);
        $pdf .= "xref\n0 ".$count."\n0000000000 65535 f \n";
        for ($id = 1; $id < $count; $id++) {
            $pdf .= isset($offsets[$id]) ? sprintf("%010d 00000 n \n", $offsets[$id]) : "0000000000 65535 f \n";
        }
        $pdf .= "trailer\n<< /Size ".$count.' /Root 1 0 R /Info '.$infoId." 0 R >>\nstartxref\n".$xref."\n%%EOF\n";

        return $pdf;
    }

    private static function color(string $hex, string $operator): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        [$r, $g, $b] = array_map(fn ($pair) => hexdec($pair) / 255, str_split(str_pad(substr($hex, 0, 6), 6, '0'), 2));

        return self::n($r).' '.self::n($g).' '.self::n($b).' '.$operator.' ';
    }

    private static function n(float|int $value): string
    {
        $formatted = rtrim(rtrim(number_format((float) $value, 3, '.', ''), '0'), '.');

        return $formatted === '-0' || $formatted === '' ? '0' : $formatted;
    }

    private static function escape(string $text): string
    {
        return str_replace(['\\', '(', ')', "\r", "\n"], ['\\\\', '\\(', '\\)', ' ', ' '], $text);
    }
}
