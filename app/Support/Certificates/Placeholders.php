<?php

namespace App\Support\Certificates;

/**
 * The placeholders a certificate statement may use (DataSensei Updates 13;
 * the course placeholders replace the module one, since a Certificate of
 * Completion is for a whole class, never one module).
 * They are replaced with the issued values when a certificate is issued;
 * anything else in square brackets is refused when the statement is saved,
 * so a typo never reaches a learner's certificate.
 */
final class Placeholders
{
    public const LEARNER = '[Learner Name]';

    public const COURSE = '[Course Name]';

    public const COURSE_CODE = '[Course Code]';

    public const SECTION = '[Section]';

    public const SEMESTER = '[Semester]';

    /** Kept from Updates 13: on a class certificate it shows the course name. */
    public const MODULE = '[Module Name]';

    public const CERTIFICATE = '[Certificate Name]';

    public const INSTRUCTOR = '[Instructor Name]';

    public const INSTRUCTOR_TITLE = '[Instructor Title]';

    public const ISSUER = '[Issuer/Institution]';

    public const DATE = '[Issue Date]';

    public const ID = '[Certificate ID]';

    /** Every placeholder a statement may use, with what it becomes. */
    public const ALL = [
        self::LEARNER => 'The learner\'s name',
        self::COURSE => 'The class\'s course name, from the class record',
        self::COURSE_CODE => 'The class\'s subject code, for example IT 301',
        self::SECTION => 'The class section',
        self::SEMESTER => 'The class term, for example 1st Semester 2026-2027',
        self::MODULE => 'Same as [Course Name] on a class certificate',
        self::CERTIFICATE => 'The certificate name above',
        self::INSTRUCTOR => 'Your name, as the signatory',
        self::INSTRUCTOR_TITLE => 'Your title, as entered below',
        self::ISSUER => 'Your institution, or DataSensei',
        self::DATE => 'The date the certificate is issued',
        self::ID => 'The certificate\'s unique ID',
    ];

    /** The placeholders listed for instructors ([Module Name] still works). */
    public const SHOWN = [
        self::LEARNER, self::COURSE, self::COURSE_CODE, self::SECTION, self::SEMESTER, self::CERTIFICATE,
        self::INSTRUCTOR, self::INSTRUCTOR_TITLE, self::ISSUER, self::DATE, self::ID,
    ];

    /**
     * The value of every placeholder for one certificate.
     *
     * @param  array<string, mixed>  $content  what the certificate is issued with
     * @return array<string, string>
     */
    public static function values(array $content, string $learner, string $date, string $certificateId): array
    {
        $text = fn (string $key): string => trim((string) ($content[$key] ?? ''));
        $course = $text('course') !== '' ? $text('course') : $text('module');

        return [
            self::LEARNER => $learner,
            self::COURSE => $course,
            self::COURSE_CODE => $text('course_code'),
            self::SECTION => $text('section'),
            self::SEMESTER => $text('semester'),
            self::MODULE => $text('module') !== '' ? $text('module') : $course,
            self::CERTIFICATE => $text('name'),
            self::INSTRUCTOR => $text('signatory_name'),
            self::INSTRUCTOR_TITLE => $text('signatory_title'),
            self::ISSUER => $text('issuer_name'),
            self::DATE => $date,
            self::ID => $certificateId,
        ];
    }

    /** @return list<string> bracketed tokens that are not placeholders */
    public static function unknown(string $statement): array
    {
        preg_match_all('/\[[^\[\]]*\]/u', $statement, $matches);
        $known = array_map('mb_strtolower', array_keys(self::ALL));

        return array_values(array_unique(array_filter(
            $matches[0],
            fn (string $token) => ! in_array(mb_strtolower($token), $known, true)
        )));
    }

    /** @param array<string, string> $values placeholder => value */
    public static function resolve(string $statement, array $values): string
    {
        $result = $statement;
        foreach (self::ALL as $token => $description) {
            $result = (string) preg_replace('/'.preg_quote($token, '/').'/iu', str_replace(['\\', '$'], ['\\\\', '\\$'], (string) ($values[$token] ?? '')), $result);
        }

        // A value that is empty (a class with no term, say) leaves no gap.
        $result = (string) preg_replace(['/\(\s*\)/u', '/\s+([,.;:])/u', '/,\s*,/u'], ['', '$1', ','], $result);

        return trim(preg_replace('/\s+/u', ' ', $result) ?? $result);
    }
}
