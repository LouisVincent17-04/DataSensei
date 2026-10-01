<?php

namespace App\Support\Certificates;

use Illuminate\Support\HtmlString;

/**
 * Draws a certificate layout for the screen as inline SVG (DataSensei
 * Updates 13). The SVG uses the page's own coordinates (842 x 595 points), so
 * it scales to any width and prints exactly as the PDF looks: the same
 * instructions from CertificateLayouts, the same line breaks, text placed on
 * the same baselines. Every value is escaped; nothing is rendered as markup.
 */
final class CertificateHtml
{
    /** @param list<array<string, mixed>> $items */
    public static function render(array $items, string $label = 'Certificate'): HtmlString
    {
        $w = self::n(CertificateLayouts::WIDTH);
        $h = self::n(CertificateLayouts::HEIGHT);
        $svg = '<svg class="ds-certificate" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 '.$w.' '.$h.'" role="img" aria-label="'.e($label).'" preserveAspectRatio="xMidYMid meet">';

        foreach ($items as $item) {
            $svg .= match ($item['type']) {
                'rect' => self::rect($item),
                'line' => '<line x1="'.self::n($item['x1']).'" y1="'.self::n($item['y1']).'" x2="'.self::n($item['x2']).'" y2="'.self::n($item['y2']).'" stroke="'.e($item['color']).'" stroke-width="'.self::n($item['width']).'"/>',
                'text' => self::text($item),
                'image' => self::image($item),
                default => '',
            };
        }

        return new HtmlString($svg.'</svg>');
    }

    private static function rect(array $r): string
    {
        return '<rect x="'.self::n($r['x']).'" y="'.self::n($r['y']).'" width="'.self::n($r['w']).'" height="'.self::n($r['h']).'"'
            .' fill="'.e($r['fill'] ?? 'none').'"'
            .($r['stroke'] !== null ? ' stroke="'.e($r['stroke']).'" stroke-width="'.self::n($r['line']).'"' : '').'/>';
    }

    private static function text(array $t): string
    {
        $css = FontMetrics::CSS[$t['font']] ?? FontMetrics::CSS[FontMetrics::SANS];
        [$x, $anchor] = match ($t['align']) {
            'center' => [$t['x'] + $t['width'] / 2, 'middle'],
            'right' => [$t['x'] + $t['width'], 'end'],
            default => [$t['x'], 'start'],
        };

        return '<text x="'.self::n($x).'" y="'.self::n($t['y']).'" text-anchor="'.$anchor.'"'
            .' font-family="'.e($css['family']).'" font-size="'.self::n($t['size']).'" font-weight="'.$css['weight'].'" font-style="'.$css['style'].'"'
            .((float) $t['spacing'] > 0 ? ' letter-spacing="'.self::n($t['spacing']).'"' : '')
            .' fill="'.e($t['color']).'" xml:space="preserve">'.e($t['text']).'</text>';
    }

    private static function image(array $i): string
    {
        if (! is_string($i['data']) || base64_decode($i['data'], true) === false) {
            return '';
        }

        return '<image x="'.self::n($i['x']).'" y="'.self::n($i['y']).'" width="'.self::n($i['w']).'" height="'.self::n($i['h']).'"'
            .' preserveAspectRatio="xMidYMid meet" href="data:image/jpeg;base64,'.e($i['data']).'"/>';
    }

    private static function n(float|int $value): string
    {
        $formatted = rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');

        return $formatted === '' || $formatted === '-0' ? '0' : $formatted;
    }
}
