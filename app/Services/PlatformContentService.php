<?php

namespace App\Services;

use App\Models\AssignmentLibraryItem;
use App\Models\Challenge;
use App\Models\ModuleLibraryItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PlatformContentService
{
    public function decodeJsonArray(?string $json, string $field): array
    {
        if ($json === null || trim($json) === '') {
            return [];
        }

        $decoded = json_decode($json, true);

        if (!is_array($decoded) || json_last_error() !== JSON_ERROR_NONE) {
            throw ValidationException::withMessages([
                $field => 'Enter valid JSON containing an array. ' . json_last_error_msg(),
            ]);
        }

        return $decoded;
    }

    public function prettyJson(mixed $value): string
    {
        if (blank($value)) {
            return "[]";
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : [];
        }

        return (string) json_encode(
            is_array($value) ? $value : [],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }

    public function makeCode(string $prefix, string $title, int $versionNo): string
    {
        $slug = strtoupper(Str::slug($title, '-'));
        $slug = $slug !== '' ? $slug : 'CONTENT';

        return substr($prefix . '-' . $slug . '-V' . $versionNo, 0, 100);
    }

    public function nextModuleVersion(int $moduleNo): int
    {
        return ((int) ModuleLibraryItem::where('module_no', $moduleNo)->max('version_no')) + 1;
    }

    public function nextAssessmentVersion(int $moduleNo): int
    {
        return ((int) AssignmentLibraryItem::where('module_no', $moduleNo)->max('version_no')) + 1;
    }

    public function nextChallengeVersion(string $contentCode): int
    {
        return ((int) Challenge::where('content_code', $contentCode)->max('version_no')) + 1;
    }

    /**
     * Publish one version and stop every other version with the same content
     * code from accepting NEW attempts.
     *
     * Learners who already started an attempt on a version that is deactivated
     * here are not interrupted: the student endpoints let an owned in-progress
     * attempt resume, autosave, heartbeat and submit against its original
     * version. The return value is the number of such in-progress attempts
     * across ALL deactivated versions, so the caller can tell the administrator.
     *
     * Every version row is locked first. Starting an attempt locks the same
     * challenge row, so a start either commits before publication (and is
     * counted here) or runs afterwards and sees the version as inactive.
     */
    public function publishChallengeVersion(Challenge $challenge): int
    {
        return (int) DB::transaction(function () use ($challenge): int {
            $versions = Challenge::query()
                ->where('content_code', $challenge->content_code)
                ->where('is_coding_challenge', (bool) $challenge->is_coding_challenge)
                ->orderBy('id')
                ->lockForUpdate()
                ->get(['id', 'is_active']);

            $deactivatedIds = $versions
                ->filter(fn (Challenge $version): bool => (int) $version->id !== (int) $challenge->getKey()
                    && (bool) $version->is_active)
                ->pluck('id');

            $continuingAttempts = 0;
            if ($deactivatedIds->isNotEmpty() && ! $challenge->is_coding_challenge) {
                $continuingAttempts = DB::table('challenge_attempts')
                    ->whereIn('challenge_id', $deactivatedIds->all())
                    ->where('status', 'in_progress')
                    ->where('expires_at', '>', now())
                    ->count();
            }

            Challenge::query()
                ->whereIn('id', $versions->pluck('id'))
                ->where('id', '<>', $challenge->getKey())
                ->update(['is_active' => false]);

            Challenge::query()
                ->whereKey($challenge->getKey())
                ->update(['is_active' => true]);

            $challenge->forceFill(['is_active' => true])->syncOriginalAttribute('is_active');

            return $continuingAttempts;
        }, 3);
    }

    public function moduleHasReferences(ModuleLibraryItem $module): bool
    {
        return $module->classAssignments()->exists();
    }

    public function assessmentHasReferences(AssignmentLibraryItem $assessment): bool
    {
        return $assessment->classAssignments()->exists();
    }

    public function challengeHasHistory(Challenge $challenge): bool
    {
        if ($challenge->attempts()->exists()) {
            return true;
        }

        if (Schema::hasTable('challenge_user')) {
            return DB::table('challenge_user')->where('challenge_id', $challenge->id)->exists();
        }

        return false;
    }

    public function moduleContentChanged(ModuleLibraryItem $module, array $sections, array $questions): bool
    {
        return $this->canonical($module->content_sections) !== $this->canonical($sections)
            || $this->canonical($module->mcq_questions) !== $this->canonical($questions);
    }

    public function challengeQuestionsChanged(Challenge $challenge, array $questions): bool
    {
        $current = $challenge->questions()
            ->with('options')
            ->get()
            ->map(function ($question): array {
                return [
                    'question_text' => trim((string) $question->question_text),
                    'options' => $question->options->map(function ($option): array {
                        return [
                            'option_text' => trim((string) $option->option_text),
                            'is_correct' => (bool) $option->is_correct,
                        ];
                    })->values()->all(),
                ];
            })->values()->all();

        $submitted = collect($questions)->map(function (array $question): array {
            $correctIndex = (int) ($question['correct_option'] ?? -1);

            return [
                'question_text' => trim((string) ($question['question_text'] ?? '')),
                'options' => collect($question['options'] ?? [])->values()->map(function ($option, int $index) use ($correctIndex): array {
                    return [
                        'option_text' => trim((string) ($option['option_text'] ?? '')),
                        'is_correct' => $index === $correctIndex,
                    ];
                })->all(),
            ];
        })->values()->all();

        return $this->canonical($current) !== $this->canonical($submitted);
    }

    public function assessmentQuestionsChanged(AssignmentLibraryItem $assessment, array $questions): bool
    {
        $current = $assessment->questions()
            ->with(['options', 'blankAnswers', 'iloMappings'])
            ->get()
            ->map(function ($question): array {
                return [
                    'question_type' => $question->question_type,
                    'question_text' => trim((string) $question->question_text),
                    'points' => (int) $question->points,
                    'explanation' => trim((string) ($question->explanation ?? '')),
                    'options' => $question->options->map(fn ($option): array => [
                        'option_text' => trim((string) $option->option_text),
                        'is_correct' => (bool) $option->is_correct,
                    ])->values()->all(),
                    'blank_answers' => $question->blankAnswers->map(fn ($answer): array => [
                        'answer_text' => trim((string) $answer->answer_text),
                        'is_case_sensitive' => (bool) $answer->is_case_sensitive,
                    ])->values()->all(),
                    'ilo_ids' => $question->iloMappings
                        ->pluck('ilo_id')
                        ->map(fn ($id): int => (int) $id)
                        ->sort()
                        ->values()
                        ->all(),
                ];
            })->values()->all();

        $submitted = collect($questions)->map(function (array $question): array {
            $type = (string) ($question['question_type'] ?? 'mcq');
            $correctIndex = (int) ($question['correct_option'] ?? -1);

            return [
                'question_type' => $type,
                'question_text' => trim((string) ($question['question_text'] ?? '')),
                'points' => (int) ($question['points'] ?? 1),
                'explanation' => trim((string) ($question['explanation'] ?? '')),
                'options' => $type === 'mcq'
                    ? collect($question['options'] ?? [])->values()->map(fn ($option, int $index): array => [
                        'option_text' => trim((string) ($option['option_text'] ?? '')),
                        'is_correct' => $index === $correctIndex,
                    ])->all()
                    : [],
                'blank_answers' => $type === 'fill_blank'
                    ? collect($question['blank_answers'] ?? [])->values()->map(fn ($answer): array => [
                        'answer_text' => trim((string) ($answer['answer_text'] ?? '')),
                        'is_case_sensitive' => filter_var($answer['is_case_sensitive'] ?? false, FILTER_VALIDATE_BOOL),
                    ])->all()
                    : [],
                'ilo_ids' => collect($question['ilo_ids'] ?? [])
                    ->map(fn ($id): int => (int) $id)
                    ->unique()
                    ->sort()
                    ->values()
                    ->all(),
            ];
        })->values()->all();

        return $this->canonical($current) !== $this->canonical($submitted);
    }

    private function canonical(mixed $value): string
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : [];
        }

        return (string) json_encode($value ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
