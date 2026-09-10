<?php

namespace Tests\Unit;

use App\Services\DataToolkit\BuiltInDatasetService;
use App\Services\DataToolkit\CategoricalAnalysisService;
use App\Services\DataToolkit\CorrelationService;
use App\Services\DataToolkit\DataToolkitAnalysisService;
use App\Services\DataToolkit\RegressionService;
use App\Services\DataToolkit\StatisticsService;
use Tests\TestCase;

class DataToolkitCoreServicesTest extends TestCase
{
    public function test_statistics_ignore_invalid_values_and_report_the_full_distribution(): void
    {
        $statistics = new StatisticsService();
        $rows = [
            ['score' => 1],
            ['score' => '2'],
            ['score' => 2],
            ['score' => ''],
            ['score' => null],
            ['score' => 'not numeric'],
        ];

        $summary = $statistics->describeColumn($rows, 'score');

        $this->assertSame(3, $summary['count']);
        $this->assertSame(2, $summary['missing_count']);
        $this->assertSame(1.67, $summary['mean']);
        $this->assertSame(2, $summary['median']);
        $this->assertSame(2, $summary['mode']);
        $this->assertSame(1, $summary['minimum']);
        $this->assertSame(2, $summary['maximum']);
        $this->assertSame(0.22, $summary['variance']);
        $this->assertSame(0.47, $summary['standard_deviation']);
        $this->assertSame(1.5, $summary['q1']);
        $this->assertSame(2, $summary['q3']);
        $this->assertSame([1.0, 2.0, 2.0], $statistics->numericValues($rows, 'score'));
    }

    public function test_statistics_handle_empty_columns_and_interpolate_percentiles(): void
    {
        $statistics = new StatisticsService();
        $summary = $statistics->describeColumn([['score' => null], ['score' => 'unknown']], 'score');

        $this->assertSame(0, $summary['count']);
        $this->assertNull($summary['mean']);
        $this->assertNull($summary['median']);
        $this->assertSame(1.75, $statistics->percentile([1.0, 2.0, 3.0, 4.0], 25));
        $this->assertNull($statistics->percentile([], 50));
        $this->assertTrue($statistics->isMissing('   '));
        $this->assertFalse($statistics->isMissing(0));
    }

    public function test_correlation_handles_positive_negative_and_undefined_relationships(): void
    {
        $correlation = new CorrelationService(new StatisticsService());

        $this->assertEqualsWithDelta(1.0, $correlation->pearson([1, 2, 3], [2, 4, 6]), 0.000001);
        $this->assertEqualsWithDelta(-1.0, $correlation->pearson([1, 2, 3], [6, 4, 2]), 0.000001);
        $this->assertNull($correlation->pearson([1], [2]));
        $this->assertNull($correlation->pearson([1, 1, 1], [2, 3, 4]));

        $interpretation = $correlation->interpret(-0.75);
        $this->assertSame('strong', $interpretation['strength']);
        $this->assertSame('negative', $interpretation['direction']);
    }

    public function test_correlation_matrix_skips_missing_pairs_and_marks_constant_columns(): void
    {
        $correlation = new CorrelationService(new StatisticsService());
        $matrix = $correlation->matrix([
            ['a' => 1, 'b' => 2, 'constant' => 5],
            ['a' => 2, 'b' => 4, 'constant' => 5],
            ['a' => '', 'b' => 8, 'constant' => 5],
            ['a' => 3, 'b' => 6, 'constant' => 5],
        ], ['a', 'b', 'constant']);

        $this->assertSame(1.0, $matrix['matrix']['a']['b']['value']);
        $this->assertNull($matrix['matrix']['constant']['constant']['value']);
        $this->assertNotNull($matrix['strongest_pair']);
    }

    public function test_linear_regression_calculates_a_known_line_and_handles_constant_x(): void
    {
        $regression = new RegressionService(new StatisticsService());
        $result = $regression->simpleLinear([
            ['x' => 1, 'y' => 3],
            ['x' => 2, 'y' => 5],
            ['x' => 3, 'y' => 7],
            ['x' => '', 'y' => 100],
        ], 'x', 'y');

        $this->assertSame(3, $result['count']);
        $this->assertSame(2, $result['slope']);
        $this->assertSame(1, $result['intercept']);
        $this->assertSame(1, $result['r_squared']);
        $this->assertSame(9.0, $regression->predict(4, 2, 1));

        $constant = $regression->simpleLinear([
            ['x' => 2, 'y' => 3],
            ['x' => 2, 'y' => 8],
        ], 'x', 'y');
        $this->assertNull($constant['slope']);
        $this->assertNull($constant['r_squared']);
    }

    public function test_categorical_analysis_counts_missing_and_frequent_values(): void
    {
        $categorical = new CategoricalAnalysisService(new StatisticsService());
        $summary = $categorical->summarizeColumn([
            ['color' => 'red'],
            ['color' => 'red'],
            ['color' => 'blue'],
            ['color' => ''],
            ['color' => null],
        ], 'color');

        $this->assertSame(2, $summary['unique_count']);
        $this->assertSame(2, $summary['missing_count']);
        $this->assertSame('red', $summary['most_frequent']);
        $this->assertSame(['label' => 'red', 'count' => 2, 'percent' => 66.7], $summary['distribution'][0]);
        $this->assertFalse($summary['distribution_truncated']);
    }

    public function test_every_builtin_dataset_has_consistent_columns_and_enough_rows(): void
    {
        $datasets = new BuiltInDatasetService();

        $this->assertCount(6, $datasets->all());
        foreach ($datasets->all() as $key => $dataset) {
            $this->assertSame($key, $dataset['key']);
            $this->assertGreaterThanOrEqual(90, count($dataset['rows']), "{$key} needs at least 90 rows.");
            $this->assertNotSame([], $dataset['columns']);
            $this->assertSame(
                $dataset['columns'],
                array_keys($dataset['rows'][0]),
                "{$key} row keys must match its declared columns."
            );
            $this->assertSame(3, count($datasets->preview($key, 3)));
        }

        $this->assertFalse($datasets->exists('missing-dataset'));
        $this->assertSame([], $datasets->preview('missing-dataset'));
    }

    public function test_numeric_column_detection_excludes_identifiers_and_tolerates_one_bad_value(): void
    {
        $analysis = $this->app->make(DataToolkitAnalysisService::class);
        $rows = [
            ['student_id' => 'S-1', 'score' => 90, 'program' => 'BSIT'],
            ['student_id' => 'S-2', 'score' => 85, 'program' => 'BSCS'],
            ['student_id' => 'S-3', 'score' => 80, 'program' => 'BSIT'],
            ['student_id' => 'S-4', 'score' => 75, 'program' => 'BSCS'],
            ['student_id' => 'S-5', 'score' => 'review', 'program' => 'BSIT'],
        ];

        $this->assertSame(
            ['score'],
            $analysis->numericColumns($rows, ['student_id', 'score', 'program'])
        );
    }
}
