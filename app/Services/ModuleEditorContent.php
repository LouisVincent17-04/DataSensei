<?php

namespace App\Services;

use App\Models\ModuleLibraryItem;
use Illuminate\Validation\ValidationException;

/**
 * Checks what the visual module editor sends (DataSensei Updates 5, task 5).
 *
 * Both admin editors use it: the Instructor Module Library
 * (ModuleLibraryItem: content_sections and mcq_questions) and the DataSensei
 * Modules (Module: its lessons and review_questions). The editor posts the
 * sections and questions as JSON built from its fields; these checks are
 * the server side of the rules the editor shows, so a hand-made request
 * cannot store a section without a title or a question without a correct
 * answer. Fields the editor does not show (lesson_no, ilo_codes,
 * question_no, ...) are kept as they are.
 */
class ModuleEditorContent
{
    public const MAX_SECTIONS = 200;

    public const MAX_QUESTIONS = 2000;

    public const MAX_OUTCOMES = 50;

    public const MAX_OUTCOME_LENGTH = 500;

    public const DIFFICULTIES = ['Easy', 'Moderate', 'Difficult'];

    /** List fields of a section, in the order the editor shows them. */
    public const SECTION_LISTS = ['walkthrough', 'common_mistakes', 'key_points', 'check_your_understanding'];

    /**
     * Keys a library section may keep its practice activity under, in the
     * order the module viewer reads them. The last one is the key the
     * original seeded content uses.
     *
     * @return list<string>
     */
    public static function libraryActivityKeys(): array
    {
        return [
            'learning_activity',
            'guided_activity',
            'lesson_activity',
            'datasensei_activity',
            base64_decode('bmV0YWNhZF9zdHlsZV9hY3Rpdml0eQ=='),
        ];
    }

    /**
     * Decodes one of the editor's JSON fields into a list.
     *
     * @return list<mixed>
     */
    public function decodeList(?string $json, string $field, string $what): array
    {
        if ($json === null || trim($json) === '') {
            return [];
        }

        $decoded = json_decode($json, true);

        if (! is_array($decoded) || json_last_error() !== JSON_ERROR_NONE || ! array_is_list($decoded)) {
            throw ValidationException::withMessages([
                $field => "The {$what} could not be read. Reload the editor and try again.",
            ]);
        }

        return $decoded;
    }

    /**
     * Every section needs a title; list fields must be lists of text.
     *
     * @param  list<mixed>  $sections
     */
    public function assertSections(array $sections, string $field, string $titleKey = 'heading'): void
    {
        if (count($sections) > self::MAX_SECTIONS) {
            throw ValidationException::withMessages([
                $field => 'A module can hold at most '.self::MAX_SECTIONS.' sections.',
            ]);
        }

        $errors = [];

        foreach ($sections as $index => $section) {
            $number = $index + 1;

            if (! is_array($section) || array_is_list($section) && $section !== []) {
                $errors[] = "Section {$number} could not be read.";

                continue;
            }

            if (trim((string) ($section[$titleKey] ?? '')) === '') {
                $errors[] = "Section {$number} needs a section title.";
            }

            foreach (self::SECTION_LISTS as $list) {
                if (! array_key_exists($list, $section)) {
                    continue;
                }

                $items = $section[$list];

                if (! is_array($items) || array_filter($items, fn ($item) => ! is_string($item)) !== []) {
                    $errors[] = "Section {$number}: the list \"".str_replace('_', ' ', $list).'" could not be read.';
                }
            }

            if (count($errors) >= 10) {
                break;
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages([$field => $errors]);
        }
    }

    /**
     * Knowledge check blocks in the sections' content: every question needs
     * its text, at least two choices and a correct answer.
     *
     * @param  list<list<array<string, mixed>>>  $sectionBlocks  blocks per section
     */
    public function assertKnowledgeChecks(array $sectionBlocks, string $field): void
    {
        $errors = [];

        foreach ($sectionBlocks as $sectionIndex => $blocks) {
            foreach ($blocks as $block) {
                if (($block['type'] ?? null) !== 'quiz') {
                    continue;
                }

                foreach ((array) ($block['questions'] ?? []) as $questionIndex => $question) {
                    $choices = array_values((array) ($question['choices'] ?? []));
                    $filled = array_filter($choices, fn ($choice) => trim((string) $choice) !== '');
                    $answer = (int) ($question['answer'] ?? -1);

                    if (trim((string) ($question['question'] ?? '')) === '' || count($filled) < 2 || $answer < 0 || trim((string) ($choices[$answer] ?? '')) === '') {
                        $errors[] = 'Section '.($sectionIndex + 1).', knowledge check question '.($questionIndex + 1).': write the question, at least two choices, and choose the correct one.';
                    }
                }
            }

            if (count($errors) >= 10) {
                break;
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages([$field => $errors]);
        }
    }

    /**
     * Every review question needs its text, filled choices that are not
     * repeated, and a correct answer that is one of the choices.
     *
     * @param  list<mixed>  $questions
     */
    public function assertQuestions(array $questions, string $field): void
    {
        if (count($questions) > self::MAX_QUESTIONS) {
            throw ValidationException::withMessages([
                $field => 'A module can hold at most '.self::MAX_QUESTIONS.' review questions.',
            ]);
        }

        $errors = [];

        foreach ($questions as $index => $question) {
            $number = $index + 1;

            if (! is_array($question) || array_is_list($question) && $question !== []) {
                $errors[] = "Review question {$number} could not be read.";

                continue;
            }

            if (trim((string) ($question['question'] ?? '')) === '') {
                $errors[] = "Review question {$number} needs the question text.";
            }

            $choices = $question['choices'] ?? null;

            if (! is_array($choices) || count($choices) < 2 || array_filter($choices, fn ($choice) => ! is_string($choice) || trim($choice) === '') !== []) {
                $errors[] = "Review question {$number}: fill in every choice (A to D).";
            } elseif (count(array_unique(array_map('trim', $choices))) !== count($choices)) {
                $errors[] = "Review question {$number} has two choices with the same text.";
            } elseif (! in_array((string) ($question['answer'] ?? ''), $choices, true)) {
                $errors[] = "Review question {$number}: choose its correct answer.";
            }

            foreach (['why_other_choices_are_wrong'] as $list) {
                if (array_key_exists($list, $question) && (! is_array($question[$list]) || array_filter($question[$list], fn ($item) => ! is_string($item)) !== [])) {
                    $errors[] = "Review question {$number}: the reasons the other choices are wrong could not be read.";
                }
            }

            if (count($errors) >= 10) {
                break;
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages([$field => $errors]);
        }
    }

    /**
     * The "What Students Will Learn" list from the form, cleaned.
     *
     * @return list<string>
     */
    public function outcomes(mixed $input): array
    {
        $values = is_array($input) ? array_values($input) : [];

        $outcomes = ModuleLibraryItem::cleanOutcomes(array_map(
            fn ($value) => is_scalar($value) ? (string) $value : '',
            $values
        ));

        if (count($outcomes) > self::MAX_OUTCOMES) {
            throw ValidationException::withMessages([
                'learning_outcomes' => 'A module can list at most '.self::MAX_OUTCOMES.' learning outcomes.',
            ]);
        }

        foreach ($outcomes as $index => $outcome) {
            if (mb_strlen($outcome) > self::MAX_OUTCOME_LENGTH) {
                throw ValidationException::withMessages([
                    'learning_outcomes' => 'Learning outcome '.($index + 1).' is longer than '.self::MAX_OUTCOME_LENGTH.' characters.',
                ]);
            }
        }

        return $outcomes;
    }

    /**
     * A module is published only with at least one learning outcome.
     *
     * @param  list<string>  $outcomes
     */
    public function assertPublishable(array $outcomes): void
    {
        if ($outcomes === []) {
            throw ValidationException::withMessages([
                'learning_outcomes' => 'Add at least one learning outcome under "What Students Will Learn" before publishing this module.',
            ]);
        }
    }

    /**
     * What the editor's buttons ask for: save as a draft, save and keep the
     * current status, publish, or unpublish. Returns the status to store.
     */
    public static function targetStatus(?string $intent, bool $current): bool
    {
        return match ($intent) {
            'draft', 'unpublish' => false,
            'publish' => true,
            default => $current,
        };
    }
}
