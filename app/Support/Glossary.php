<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

/**
 * Plain-language definitions for the statistical and technical words that
 * appear across DataSensei (DataSensei Updates 10). Every definition is one
 * or two short sentences a beginner can read in a breath. A term can also
 * carry a formula and a purpose line; only Standard Deviation uses them now.
 *
 * A view prints a term with a small "?" beside it:
 *
 *   {!! Glossary::help('standard_deviation') !!}          the label + ?
 *   {!! Glossary::help('iqr', 'IQR method') !!}           custom text + ?
 *
 * Pointing at the "?", focusing it with the keyboard, or tapping it opens
 * the definition. The markup pairs with partials/glossary-tips.blade.php,
 * which must be included once per page that uses it.
 */
final class Glossary
{
    private static int $sequence = 0;

    /** @return array<string, array{label:string,definition:string,formula?:string,purpose?:string}> */
    public static function terms(): array
    {
        return [
            // Descriptive statistics
            'mean' => ['label' => 'Mean', 'definition' => 'The average value: add every value, then divide by how many there are.'],
            'median' => ['label' => 'Median', 'definition' => 'The middle value after the numbers are arranged in order.'],
            'mode' => ['label' => 'Mode', 'definition' => 'The value that appears most often.'],
            'standard_deviation' => [
                'label' => 'Standard deviation',
                'definition' => 'Shows how spread out the values are from the average.',
                'formula' => 'σ = √(Σ(x − μ)² / N)',
                'purpose' => 'Helps show whether the values are close together or spread far apart.',
            ],
            'variance' => ['label' => 'Variance', 'definition' => 'A measure of spread: the average of the squared distances from the mean. The standard deviation is its square root.'],
            'minimum' => ['label' => 'Minimum', 'definition' => 'The smallest value in the column.'],
            'maximum' => ['label' => 'Maximum', 'definition' => 'The largest value in the column.'],
            'range' => ['label' => 'Range', 'definition' => 'The distance from the smallest value to the largest one.'],
            'count' => ['label' => 'Count', 'definition' => 'How many usable values the column has.'],
            'sum' => ['label' => 'Sum', 'definition' => 'All of the values added together.'],
            'quartile' => ['label' => 'Quartile', 'definition' => 'One of three cut points that split the sorted values into four equal parts, called Q1, Q2 (the median), and Q3.'],
            'percentile' => ['label' => 'Percentile', 'definition' => 'The value below which a given percent of the data falls. The 90th percentile is higher than 90% of the values.'],
            'iqr' => ['label' => 'IQR', 'definition' => 'A method the system uses to find values that are unusually far from most of the data.'],
            'outlier' => ['label' => 'Outlier', 'definition' => 'A value that is very different from most other values.'],
            'skewness' => ['label' => 'Skewness', 'definition' => 'Whether the values lean to one side. A few very large values pull the average up; a few very small ones pull it down.'],
            'distribution' => ['label' => 'Distribution', 'definition' => 'The shape of the data: which values are common, which are rare, and how far they spread.'],
            'histogram' => ['label' => 'Histogram', 'definition' => 'A bar chart that groups numbers into ranges, so you can see which ranges hold the most values.'],
            'box_plot' => ['label' => 'Box plot', 'definition' => 'A compact chart of the middle half of the data: the box spans Q1 to Q3 with a line at the median, and dots mark unusual values.'],
            'univariate' => ['label' => 'Univariate', 'definition' => 'Looking at one column at a time.'],
            'bivariate' => ['label' => 'Bivariate', 'definition' => 'Looking at two columns together to see whether they move with each other.'],

            // Relationships
            'correlation' => ['label' => 'Correlation', 'definition' => 'How strongly two columns move together, from −1 to 1. Near 0 means little connection. It does not prove one causes the other.'],
            'correlation_coefficient' => ['label' => 'r', 'definition' => 'The correlation number, from −1 to 1. Positive means the columns rise together; negative means one falls as the other rises.'],
            'regression' => ['label' => 'Regression', 'definition' => 'Fitting a line through the points to describe and predict how one number changes with another.'],
            'r_squared' => ['label' => 'R²', 'definition' => 'How much of the change in one column the other column explains, from 0 to 1. Higher means a better fit.'],
            'scatter_plot' => ['label' => 'Scatter plot', 'definition' => 'A chart with one dot per row, used to see whether two number columns are related.'],
            'causation' => ['label' => 'Causation', 'definition' => 'One thing directly making another happen. Two columns can move together without either causing the other.'],

            // Data quality
            'missing_value' => ['label' => 'Missing value', 'definition' => 'A cell with no value in it.'],
            'duplicate' => ['label' => 'Duplicate', 'definition' => 'A row that exactly repeats another row.'],
            'invalid_value' => ['label' => 'Invalid value', 'definition' => 'A value of the wrong kind for its column, such as text where a number belongs, or a number outside any reasonable range.'],
            'numeric_column' => ['label' => 'Numeric column', 'definition' => 'A column that holds numbers you can calculate with, such as age or price.'],
            'categorical_column' => ['label' => 'Categorical column', 'definition' => 'A column that holds groups or labels, such as city or gender, rather than numbers to calculate with.'],
            'identifier' => ['label' => 'Identifier', 'definition' => 'A column that only names each row, like an ID number. It is not useful for analysis.'],
            'imputation' => ['label' => 'Filling in', 'definition' => 'Replacing a missing value with a sensible stand-in, such as the median of the column.'],
            'data_cleaning' => ['label' => 'Data cleaning', 'definition' => 'Fixing problems in the data, such as missing values, repeated rows, and mistyped entries, before analyzing it.'],
            'feature_engineering' => ['label' => 'Feature engineering', 'definition' => 'Making a useful new column from existing ones, such as a total from two amounts.'],
            'eda' => ['label' => 'EDA', 'definition' => 'Exploratory data analysis: getting to know a dataset with counts, summaries, and simple charts before drawing conclusions.'],

            // Assessment and teaching terms
            'table_of_specifications' => ['label' => 'Table of Specifications', 'definition' => 'A planning table that spreads test items across topics and thinking levels before the questions are written.'],
            'bloom_level' => ['label' => 'Thinking level', 'definition' => 'How deeply a question makes students think, from remembering a fact to creating something new (Bloom\'s taxonomy).'],
            'learning_outcome' => ['label' => 'Learning outcome', 'definition' => 'A short statement of what a student should be able to do after finishing the material.'],
            'formative' => ['label' => 'Formative', 'definition' => 'Practice used to check progress while learning, usually with low or no grade weight.'],
            'summative' => ['label' => 'Summative', 'definition' => 'An assessment of what was learned at the end, such as an exam, usually graded.'],
            'rubric' => ['label' => 'Grading notes', 'definition' => 'Notes that describe what a good answer includes, used to grade written answers consistently.'],
            'anti_cheat' => ['label' => 'Anti-cheat', 'definition' => 'Checks that record events like leaving the test tab or pasting text while a student works.'],

            // Platform and gamification
            'xp' => ['label' => 'XP', 'definition' => 'Experience points, earned by finishing work. XP decides a student\'s place on the leaderboard.'],
            'streak' => ['label' => 'Streak', 'definition' => 'How many days in a row the student has been active.'],
            'mission' => ['label' => 'Mission', 'definition' => 'A small repeating goal, such as running code once a day, that awards XP when completed.'],
            'achievement' => ['label' => 'Achievement', 'definition' => 'A one-time award a student earns by reaching a milestone. Its rule and XP are fixed.'],
            'competency' => ['label' => 'Competency', 'definition' => 'A skill area, such as Python basics, with a score showing how well the student is doing in it.'],
            'engagement' => ['label' => 'Engagement', 'definition' => 'How actively a student uses the platform: signing in, finishing work, and practicing.'],
            'percentile_rank' => ['label' => 'Percentile rank', 'definition' => 'The percent of students this student scores above.'],
            'sandbox' => ['label' => 'Sandbox', 'definition' => 'A safe, isolated space where code runs without touching anything outside it.'],

            // Assessments and anti-cheat (DataSensei Updates 11)
            'time_limit' => ['label' => 'Time limit', 'definition' => 'How long an attempt may last once started. If there is also a due date, the attempt ends at whichever comes first. Leave it empty for untimed work.'],
            'passing_score' => ['label' => 'Passing score', 'definition' => 'The lowest percentage that counts as a pass. Leave it empty when the assessment has no pass mark.'],
            'provisional_score' => ['label' => 'Provisional score', 'definition' => 'The score the attempt would get if the instructor releases it. It counts for nothing until then.'],
            'held_for_review' => ['label' => 'Held for review', 'definition' => 'An attempt the anti-cheat checks locked. The answers are kept, but it has no credit until the instructor releases it or keeps it blocked.'],
            'focus_loss' => ['label' => 'Tab switch', 'definition' => 'Leaving the assessment window, for example by switching tabs or clicking another app.'],
            'dual_monitor' => ['label' => 'Dual monitor', 'definition' => 'A second screen connected to the computer, which could show notes during an attempt.'],
            'question_bank' => ['label' => 'Question Bank', 'definition' => 'Your reusable questions. Adding one to an assessment copies it, so later edits in the bank never change that assessment.'],
            'performance_segment' => ['label' => 'Performance segment', 'definition' => 'A group of students with similar results, found automatically by comparing scores, activity and missing work.'],

            // Certificates (DataSensei Updates 13)
            'certificate_layout' => ['label' => 'Layout', 'definition' => 'One of five fixed certificate designs. You choose one and fill in the words; the design itself cannot be changed, so every certificate looks consistent.'],
            'certificate_placeholder' => ['label' => 'Placeholder', 'definition' => 'A word in square brackets, such as [Learner Name], that is replaced with the real value when the certificate is issued.'],
            'certificate_status' => ['label' => 'Status', 'definition' => 'Draft: being prepared. Active: ready to issue to the students who completed the class. Inactive: cannot be issued; certificates already issued stay valid.'],
            'certificate_id' => ['label' => 'Certificate ID', 'definition' => 'The certificate\'s unique code. It is never reused, and anyone can check it on the verification page.'],
            'certificate_verification' => ['label' => 'Verification', 'definition' => 'A public page where anyone can enter a certificate ID to confirm it is genuine. It shows only the name, certificate, issuer and date.'],
            'certificate_requirement' => ['label' => 'Completion requirement', 'definition' => 'What a learner must finish to receive the certificate; for a class certificate, every module, activity and assessment assigned to the class. Checked on the server from saved progress.'],
            'final_grade' => ['label' => 'Final grade', 'definition' => 'The class grade: the average of the student\'s graded assessments in this class, each assessment counting equally and using its best graded attempt. Practice and public challenges are not included.'],
            'class_completion' => ['label' => 'Class completion', 'definition' => 'How much of the required class work is done: the modules, activities (challenges given to the class) and assessments the instructor assigned. An assessment counts once it is graded.'],
            'certificate_revocation' => ['label' => 'Revoke', 'definition' => 'Withdraw a certificate. It stays on record as revoked and the verification page says so. Reissuing creates a new copy with a new certificate ID.'],
        ] + self::thinkingLevels();
    }

    /**
     * The Table of Specifications thinking levels, worded exactly as the TOS
     * defines them (thinking_remember, thinking_understand, ...).
     *
     * @return array<string, array{label:string,definition:string}>
     */
    private static function thinkingLevels(): array
    {
        $terms = [];
        foreach (\App\Services\TableOfSpecificationService::COGNITIVE_LEVELS as $slug => $level) {
            $terms['thinking_'.$slug] = ['label' => $level['label'], 'definition' => $level['explanation']];
        }

        return $terms;
    }

    /** @return array{label:string,definition:string,formula?:string,purpose?:string}|null */
    public static function find(string $key): ?array
    {
        return self::terms()[strtolower(str_replace(['-', ' '], '_', trim($key)))] ?? null;
    }

    public static function definition(string $key): string
    {
        return self::find($key)['definition'] ?? '';
    }

    /**
     * The term's label (or the given text) followed by a small "?" button
     * that shows the definition on hover, keyboard focus, or tap. Unknown
     * keys print as plain escaped text, so a typo never breaks a page.
     */
    public static function help(string $key, ?string $text = null): HtmlString
    {
        $entry = self::find($key);
        $word = e($text ?? ($entry['label'] ?? $key));
        if ($entry === null) {
            return new HtmlString($word);
        }

        $id = 'ds-tip-'.(++self::$sequence);
        $body = '<b>'.e($entry['label']).'</b>'.e($entry['definition']);
        if (($entry['formula'] ?? '') !== '') {
            $body .= '<span class="ds-tip-formula">Formula: '.e($entry['formula']).'</span>';
        }
        if (($entry['purpose'] ?? '') !== '') {
            $body .= '<span class="ds-tip-purpose">Purpose: '.e($entry['purpose']).'</span>';
        }

        return new HtmlString(
            '<span class="ds-term" data-ds-term>'.$word
            .'<button type="button" class="ds-term-help" aria-expanded="false" aria-describedby="'.$id.'" aria-label="What does '.e($entry['label']).' mean?">?</button>'
            .'<span class="ds-term-tip" role="tooltip" id="'.$id.'">'.$body.'</span></span>'
        );
    }

    /**
     * Only the "?" button, for a spot where the word is already on the page,
     * such as a table header. Unknown keys print nothing.
     */
    public static function mark(string $key): HtmlString
    {
        $entry = self::find($key);
        if ($entry === null) {
            return new HtmlString('');
        }

        return self::help($key, '');
    }
}
