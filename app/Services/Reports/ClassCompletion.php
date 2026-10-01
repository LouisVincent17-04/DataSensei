<?php

namespace App\Services\Reports;

use App\Models\Assessment;
use App\Models\ClassRoom;

/**
 * Completion of a class's required work (DataSensei Combined Certificate and
 * Gradebook Requirements).
 *
 * The required work of a class is everything its instructor assigned to it:
 *   modules       the class modules assigned to the class (active)
 *   activities    the MCQ and coding challenges given to the class
 *                 (published or closed)
 *   assessments   the class's published and closed assessments
 *
 * An item is complete by the same rules Class Analytics uses
 * (App\Services\Reports\ClassProgress), read from the saved records:
 *   module               completed
 *   MCQ challenge        passed (70% or more on the best attempt)
 *   coding challenge     every problem solved
 *   assessment           turned in and graded (an attempt still awaiting a
 *                        grade or held for an integrity review is not yet
 *                        complete)
 *
 * A student has completed the class when every required item is complete.
 * Public challenges, personal practice and modules outside the class never
 * count. The final grade is the student's assessment average, the same rule
 * as the gradebooks and Class Analytics: the best graded attempt of each
 * graded assessment, each assessment counting equally.
 */
class ClassCompletion
{
    public const GROUPS = [
        'modules' => 'Modules',
        'activities' => 'Activities',
        'assessments' => 'Assessments',
    ];

    public function __construct(private readonly ClassProgress $progress)
    {
    }

    /**
     * @param  list<int>|null  $onlyStudents  read only these enrolled students
     * @return array{requirements: array<string, list<array<string, mixed>>>, total: int, students: array<int, array<string, mixed>>}
     */
    public function forClass(ClassRoom $class, ?array $onlyStudents = null): array
    {
        $snapshot = $this->progress->forClass($class, null, $onlyStudents);
        $requirements = $this->requirements($snapshot);
        $total = array_sum(array_map('count', $requirements));

        $students = [];
        foreach ($snapshot['perStudent'] as $studentId => $row) {
            $students[(int) $studentId] = $this->studentRow($row, $requirements, $total);
        }

        return ['requirements' => $requirements, 'total' => $total, 'students' => $students];
    }

    /**
     * One student's completion. The caller passes an enrolled student's id
     * it is allowed to read (the signed-in student, or the instructor's own
     * class).
     *
     * @return array<string, mixed>|null  null when the student is not enrolled
     */
    public function forStudent(ClassRoom $class, int $studentId): ?array
    {
        $book = $this->forClass($class, [$studentId]);

        return isset($book['students'][$studentId])
            ? $book['students'][$studentId] + ['requirements' => $book['requirements']]
            : null;
    }

    /**
     * The required items of a class, as stored with a certificate's
     * requirement version.
     *
     * @param  array<string, mixed>  $snapshot  from ClassProgress::forClass
     * @return array<string, list<array<string, mixed>>>
     */
    public function requirements(array $snapshot): array
    {
        $modules = [];
        foreach ($snapshot['modules'] as $module) {
            $modules[] = [
                'type' => 'module',
                'id' => (int) $module->id,
                'title' => trim($module->title.($module->version_name ? ' ('.$module->version_name.')' : '')),
                'kind' => 'Module',
            ];
        }

        $activities = [];
        foreach ($snapshot['challenges'] as $challenge) {
            $activities[] = ['type' => 'challenge', 'id' => (int) $challenge->id, 'title' => (string) ($challenge->given_title ?: $challenge->title), 'kind' => 'MCQ challenge'];
        }
        foreach ($snapshot['coding'] as $challenge) {
            $activities[] = ['type' => 'coding', 'id' => (int) $challenge->id, 'title' => (string) ($challenge->given_title ?: $challenge->title), 'kind' => 'Coding challenge'];
        }

        $assessments = [];
        foreach ($snapshot['assessments'] as $assessment) {
            $assessments[] = [
                'type' => 'assessment',
                'id' => (int) $assessment->id,
                'title' => (string) $assessment->title,
                'kind' => Assessment::PURPOSES[$assessment->purpose ?? ''] ?? 'Assessment',
            ];
        }

        return ['modules' => $modules, 'activities' => $activities, 'assessments' => $assessments];
    }

    /**
     * @param  array<string, mixed>  $row  ClassProgress per-student row
     * @param  array<string, list<array<string, mixed>>>  $requirements
     * @return array<string, mixed>
     */
    private function studentRow(array $row, array $requirements, int $total): array
    {
        $groups = [];
        $missing = [];
        $done = 0;

        foreach ($requirements as $group => $items) {
            $met = 0;
            foreach ($items as $item) {
                [$complete, $detail] = $this->itemState($row, $item);
                if ($complete) {
                    $met++;
                } else {
                    $missing[] = ['group' => $group, 'title' => $item['title'], 'kind' => $item['kind'], 'detail' => $detail];
                }
            }
            $groups[$group] = ['done' => $met, 'total' => count($items)];
            $done += $met;
        }

        $graded = collect($row['assessments'])->filter(fn (array $a) => $a['best'] !== null)->count();

        return [
            'student' => $row['student'],
            'groups' => $groups,
            'done' => $done,
            'total' => $total,
            'complete' => $total > 0 && $done === $total,
            'missing' => $missing,
            'final_grade' => $row['summary']['assessment_average'],
            'graded' => $graded,
            'assessments_total' => count($requirements['assessments']),
        ];
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  array<string, mixed>  $item
     * @return array{0: bool, 1: string}  complete, and why not
     */
    private function itemState(array $row, array $item): array
    {
        switch ($item['type']) {
            case 'module':
                $state = $row['modules'][$item['id']]['state'] ?? 'not_started';

                return [$state === 'completed', $state === 'started' ? 'Started, not completed' : 'Not started'];

            case 'challenge':
                $state = $row['challenges'][$item['id']]['state'] ?? 'not_started';

                return [$state === 'passed', $state === 'attempted' ? 'Attempted, not passed yet' : 'Not attempted'];

            case 'coding':
                $coding = $row['coding'][$item['id']] ?? null;
                $state = $coding['state'] ?? 'not_started';

                return [$state === 'completed', $state === 'in_progress' ? 'Solved '.$coding['solved'].' of '.$coding['problems'].' problems' : 'Not started'];

            case 'assessment':
                $assessment = $row['assessments'][$item['id']] ?? null;
                $graded = $assessment !== null && $assessment['state'] === 'completed' && ! $assessment['awaiting_review'];
                $why = match (true) {
                    $assessment === null, $assessment['state'] === 'not_started' => 'Not turned in',
                    $assessment['state'] === 'in_progress' => 'Started, not turned in',
                    default => (string) $assessment['score_text'],
                };

                return [$graded, $why];
        }

        return [false, ''];
    }
}
