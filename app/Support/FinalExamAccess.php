<?php

namespace App\Support;

/**
 * DataSensei Updates 7, task 2: the Final Exam of a public DataSensei Module
 * is open to everyone.
 *
 * The seeded final exam lessons used to start with a "University /
 * Organization Access Only" lock screen, kept the exam itself in a hidden
 * <div id="final-exam-content" style="display:none;">, and ended with a script
 * that only showed the exam when window.USER_ORG_ID was set. Nothing ever set
 * that value, so no learner could take a final exam.
 *
 * open() removes that restriction from a lesson's HTML: the lock screen and
 * the unlocking script are taken out and the exam is no longer wrapped in the
 * hidden div. Everything else (the exam's title, introduction, questions and
 * the knowledge check script) is kept byte for byte. HTML without the lock is
 * returned unchanged, so calling it again changes nothing.
 *
 * Used by the Lesson model (so a lesson is never shown locked, even before
 * the migration has run) and by the 2026_10_01 migration that rewrites the
 * stored lessons.
 */
final class FinalExamAccess
{
    private const LOCK_ID = 'org-lock-screen';

    private const EXAM_ID = 'final-exam-content';

    /** True when the HTML still carries the old institution-only lock. */
    public static function isRestricted(?string $html): bool
    {
        if ($html === null || $html === '') {
            return false;
        }

        return str_contains($html, self::LOCK_ID)
            || str_contains($html, self::EXAM_ID)
            || str_contains($html, 'USER_ORG_ID');
    }

    public static function open(?string $html): ?string
    {
        if (! self::isRestricted($html)) {
            return $html;
        }

        $html = self::removeLockScripts($html);
        $html = self::removeElement($html, self::LOCK_ID);
        $html = self::unwrapElement($html, self::EXAM_ID);

        // Hand-edited HTML whose divs do not close cleanly: the lock screen is
        // still dropped (up to the exam) and the exam is still shown.
        if (preg_match(self::tagPattern(self::LOCK_ID), $html, $lock, PREG_OFFSET_CAPTURE)) {
            $start = $lock[0][1];
            $html = preg_match(self::tagPattern(self::EXAM_ID), $html, $exam, PREG_OFFSET_CAPTURE, $start)
                ? substr($html, 0, $start).substr($html, $exam[0][1])
                : substr_replace($html, '<div hidden>', $start, strlen($lock[0][0]));
        }

        return preg_replace(self::tagPattern(self::EXAM_ID), '<div>', $html) ?? $html;
    }

    /** The opening tag of the div with this id. */
    private static function tagPattern(string $id): string
    {
        return '#<div\b[^>]*\bid\s*=\s*(["\'])'.preg_quote($id, '#').'\1[^>]*>#i';
    }

    /**
     * The scripts that toggled the lock screen and the hidden exam.
     */
    private static function removeLockScripts(string $html): string
    {
        return preg_replace_callback(
            '#[ \t]*<script\b[^>]*>(.*?)</script\s*>[ \t]*\r?\n?#is',
            static function (array $match): string {
                $body = $match[1];

                return str_contains($body, 'USER_ORG_ID') || str_contains($body, self::LOCK_ID) || str_contains($body, self::EXAM_ID)
                    ? ''
                    : $match[0];
            },
            $html
        ) ?? $html;
    }

    /** Removes the <div id="..."> element with everything inside it. */
    private static function removeElement(string $html, string $id): string
    {
        while (($range = self::divRange($html, $id)) !== null) {
            [$start, , , $end] = $range;
            [$start, $end] = self::withLine($html, $start, $end);
            $html = substr($html, 0, $start).substr($html, $end);
        }

        return $html;
    }

    /** Removes the <div id="..."> tags but keeps what was inside. */
    private static function unwrapElement(string $html, string $id): string
    {
        while (($range = self::divRange($html, $id)) !== null) {
            [$openStart, $openEnd, $closeStart, $closeEnd] = $range;
            [$openStart, $openEnd] = self::withLine($html, $openStart, $openEnd);
            [$closeStart, $closeEnd] = self::withLine($html, $closeStart, $closeEnd);
            $html = substr($html, 0, $openStart)
                .substr($html, $openEnd, $closeStart - $openEnd)
                .substr($html, $closeEnd);
        }

        return $html;
    }

    /**
     * Where the div with this id starts and ends:
     * [open tag start, open tag end, close tag start, close tag end],
     * or null when there is none (or it is never closed).
     *
     * Nested divs are counted; <script>, <style> and comments are skipped so
     * text inside them is never taken for a tag.
     *
     * @return array{0: int, 1: int, 2: int, 3: int}|null
     */
    private static function divRange(string $html, string $id): ?array
    {
        if (! preg_match(self::tagPattern($id), $html, $open, PREG_OFFSET_CAPTURE)) {
            return null;
        }

        $openStart = $open[0][1];
        $openEnd = $openStart + strlen($open[0][0]);
        $depth = 1;
        $offset = $openEnd;

        while (preg_match('#<script\b|<style\b|<!--|<div\b[^>]*>|</div\s*>#i', $html, $token, PREG_OFFSET_CAPTURE, $offset)) {
            $tag = strtolower($token[0][0]);
            $at = $token[0][1];

            if (str_starts_with($tag, '<script') || str_starts_with($tag, '<style')) {
                $name = str_starts_with($tag, '<script') ? 'script' : 'style';
                if (! preg_match('#</'.$name.'\s*>#i', $html, $close, PREG_OFFSET_CAPTURE, $at)) {
                    return null;
                }
                $offset = $close[0][1] + strlen($close[0][0]);

                continue;
            }

            if ($tag === '<!--') {
                $end = strpos($html, '-->', $at + 4);
                if ($end === false) {
                    return null;
                }
                $offset = $end + 3;

                continue;
            }

            $offset = $at + strlen($token[0][0]);

            if (str_starts_with($tag, '</div')) {
                $depth--;
                if ($depth === 0) {
                    return [$openStart, $openEnd, $at, $offset];
                }

                continue;
            }

            $depth++;
        }

        return null;
    }

    /**
     * Widens a range to its whole line when nothing else is on that line, so
     * no empty indented line is left behind.
     *
     * @return array{0: int, 1: int}
     */
    private static function withLine(string $html, int $start, int $end): array
    {
        $lineStart = $start;
        while ($lineStart > 0 && ($html[$lineStart - 1] === ' ' || $html[$lineStart - 1] === "\t")) {
            $lineStart--;
        }

        $lineEnd = $end;
        while ($lineEnd < strlen($html) && ($html[$lineEnd] === ' ' || $html[$lineEnd] === "\t")) {
            $lineEnd++;
        }

        $atLineStart = $lineStart === 0 || $html[$lineStart - 1] === "\n";
        $atLineEnd = $lineEnd === strlen($html) || $html[$lineEnd] === "\n" || $html[$lineEnd] === "\r";

        if (! $atLineStart || ! $atLineEnd) {
            return [$start, $end];
        }

        if ($lineEnd < strlen($html) && $html[$lineEnd] === "\r") {
            $lineEnd++;
        }
        if ($lineEnd < strlen($html) && $html[$lineEnd] === "\n") {
            $lineEnd++;
        }

        return [$lineStart, $lineEnd];
    }
}
