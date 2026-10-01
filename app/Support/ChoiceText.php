<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

/**
 * Shows answer choices whose spaces matter.
 *
 * HTML collapses runs of spaces and drops leading and trailing ones, so the
 * choices "hi" and "  hi  " (for a question about str.strip()) looked like two
 * identical "hi" answers, and indented code choices lost their indentation.
 * A choice whose whitespace is significant is shown in the code font with its
 * spaces kept; spaces at the edges or in runs are drawn as "␣" so they can be
 * seen. When any choice of a question needs this, every choice of that
 * question is shown the same way so they can be compared side by side.
 */
final class ChoiceText
{
    /** True when HTML would hide or collapse whitespace in the text. */
    public static function hasSignificantWhitespace(?string $text): bool
    {
        $text = (string) $text;

        if ($text === '') {
            return false;
        }

        return $text !== trim($text, " \t\n\r\0\x0B")
            || preg_match('/ {2,}|\t|\R/u', $text) === 1;
    }

    /**
     * @param  iterable<int, mixed>  $options  models or arrays with option_text
     */
    public static function anySignificant(iterable $options): bool
    {
        foreach ($options as $option) {
            $text = is_array($option) ? ($option['option_text'] ?? '') : ($option->option_text ?? '');

            if (self::hasSignificantWhitespace((string) $text)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Escaped HTML for one choice. With $literal (or significant whitespace)
     * the text is shown as code with its spaces kept.
     */
    public static function html(?string $text, bool $literal = false): HtmlString
    {
        $text = (string) $text;

        if (! $literal && ! self::hasSignificantWhitespace($text)) {
            return new HtmlString(e($text));
        }

        $normalized = str_replace(["\r\n", "\r"], "\n", $text);

        // Multi-line choices are code: keep the layout, no space markers.
        if (str_contains($normalized, "\n")) {
            return new HtmlString('<code class="ds-choice-literal is-block">'.e($normalized).'</code>');
        }

        $marked = preg_replace_callback(
            '/^[ \t]+|[ \t]+$|[ \t]{2,}/u',
            static function (array $match): string {
                $marks = '';
                foreach (str_split($match[0]) as $character) {
                    $marks .= $character === "\t"
                        ? '<span class="ds-choice-space" title="tab">⇥</span>'
                        : '<span class="ds-choice-space" title="space">␣</span>';
                }

                return $marks;
            },
            e($normalized)
        );

        return new HtmlString('<code class="ds-choice-literal">'.$marked.'</code>');
    }
}
