<?php

namespace App\Support;

/**
 * The four stops of Model Development.
 *
 * The earlier ten-stage roadmap asked a beginner to make six decisions before
 * anything happened. Those decisions still exist, but they now live on one
 * set-up page with safe defaults, so the journey is: choose data, set up,
 * read the results, make a prediction. Trained models are saved automatically.
 */
final class ModelDevelopmentRoadmap
{
    public const STEP_DATA = 1;
    public const STEP_SETUP = 2;
    public const STEP_RESULTS = 3;
    public const STEP_PREDICT = 4;

    /** @return array<int, array{key:string,label:string,description:string}> */
    public static function steps(): array
    {
        return [
            self::STEP_DATA => [
                'key' => 'data',
                'label' => 'Choose data',
                'description' => 'Pick the table of examples your model will learn from.',
            ],
            self::STEP_SETUP => [
                'key' => 'setup',
                'label' => 'Set up',
                'description' => 'Say what you want to predict, then press Train.',
            ],
            self::STEP_RESULTS => [
                'key' => 'results',
                'label' => 'Results',
                'description' => 'See how often the model is right on rows it never saw.',
            ],
            self::STEP_PREDICT => [
                'key' => 'predict',
                'label' => 'Predict',
                'description' => 'Type in new values and read the model\'s answer.',
            ],
        ];
    }

    public static function totalSteps(): int
    {
        return count(self::steps());
    }

    /** @return array{completed:int,total:int,percent:int} */
    public static function progress(int $completed): array
    {
        $total = self::totalSteps();
        $completed = max(0, min($total, $completed));

        return [
            'completed' => $completed,
            'total' => $total,
            'percent' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
        ];
    }

    /** Every set-up decision is on one page, so old ?step=2..7 links all land there. */
    public static function normalizeAuthoringStep(mixed $step, int $fallback = self::STEP_SETUP): int
    {
        return self::STEP_SETUP;
    }

    /**
     * Which part of the set-up page a validation error belongs to, so the page
     * can open that part and scroll to it.
     *
     * @param array<int, string> $fields
     */
    public static function sectionForValidationFields(array $fields): ?string
    {
        $sections = [
            'target_column' => 'target',
            'problem_type' => 'target',
            'features' => 'columns',
            'algorithm_key' => 'method',
            'parameters' => 'method',
            'tune' => 'method',
            'test_size' => 'method',
            'random_state' => 'method',
            'cross_validation' => 'method',
            'scale_mode' => 'method',
            'numeric_imputation' => 'method',
            'remove_duplicates' => 'method',
            'model_name' => 'name',
            'class_id' => 'name',
        ];
        $order = ['target', 'columns', 'method', 'name'];

        $found = [];
        foreach ($fields as $field) {
            $root = explode('.', (string) $field)[0];
            if (isset($sections[$root])) {
                $found[] = array_search($sections[$root], $order, true);
            }
        }

        return $found === [] ? null : $order[min($found)];
    }

    /** @param array<int, string> $fields */
    public static function stepForValidationFields(array $fields): ?int
    {
        return self::sectionForValidationFields($fields) === null ? null : self::STEP_SETUP;
    }

    /**
     * "save" and "evaluate" are accepted so bookmarks from the ten-stage
     * version keep working. Models are saved as soon as training finishes, so
     * there is no separate save stage to unlock any more.
     */
    public static function resultStep(?string $requested, bool $hasPrediction = false): int
    {
        return match ($requested) {
            'predict', 'save' => self::STEP_PREDICT,
            default => self::STEP_RESULTS,
        };
    }
}
