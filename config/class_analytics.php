<?php

/*
|--------------------------------------------------------------------------
| Class Analytics & At-Risk (DataSensei Additional Requirements, part 3)
|--------------------------------------------------------------------------
|
| The instructor's Class Analytics page groups each student by their
| assessment average (their best attempt on every graded assessment of the
| class) and lists the students who may be at risk, with the rule that
| flagged them. Every number used for that is set here, so nothing is
| hard-coded in the page.
|
| Performance groups (agreed rules, DataSensei Updates 12):
|   Low       assessment average below low_below            (default 70,
|             the project pass mark, ReportFormat::PASS_PERCENT)
|   Moderate  from low_below up to just under high_from
|   High      high_from or more                             (default 90,
|             the score StudentPerformanceClusteringService already
|             uses for an "Exceptional Performer")
| A student with no graded assessment yet is shown as "Not yet graded" and
| is not put in any group.
|
| Change a value in .env (for example ANALYTICS_LOW_BELOW=75) and run
| "php artisan config:clear". Invalid values (not 1 to 100, or low_below not
| below high_from) fall back to the defaults; see
| App\Support\Reports\PerformanceBands.
*/

return [

    'performance' => [
        'low_below' => env('ANALYTICS_LOW_BELOW', 70),
        'high_from' => env('ANALYTICS_HIGH_FROM', 90),
    ],

    /*
    | At-risk rules. A student is listed as at risk when at least one rule is
    | true for their saved class work; each rule shows as a reason.
    |
    |   missing_assessments      past-due assessments not completed
    |   challenge_average_below  average best score on the class's MCQ
    |                            challenges
    |   failed_attempts          failed attempts on one coding problem, MCQ
    |                            challenge or assessment that is still not
    |                            passed
    |   module_completion_below  percent of the class's modules completed,
    |                            counting modules assigned at least
    |                            module_grace_days ago
    |
    | The "low assessment scores" rule is the Low performance group above, so
    | the bar graph and the at-risk list always agree.
    */
    'at_risk' => [
        'missing_assessments' => env('AT_RISK_MISSING_ASSESSMENTS', 2),
        'challenge_average_below' => env('AT_RISK_CHALLENGE_AVERAGE_BELOW', 70),
        'failed_attempts' => env('AT_RISK_FAILED_ATTEMPTS', 3),
        'module_completion_below' => env('AT_RISK_MODULE_COMPLETION_BELOW', 25),
        'module_grace_days' => env('AT_RISK_MODULE_GRACE_DAYS', 7),
    ],

];
