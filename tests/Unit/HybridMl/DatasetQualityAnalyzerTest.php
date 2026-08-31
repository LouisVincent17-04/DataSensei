<?php

namespace Tests\Unit\HybridMl;

use App\Services\HybridMl\DatasetProfiler;
use App\Services\HybridMl\DatasetQualityAnalyzer;
use Tests\TestCase;

class DatasetQualityAnalyzerTest extends TestCase
{
    public function test_it_profiles_and_scores_actual_quality_problems(): void
    {
        $rows = [];
        for ($i = 1; $i <= 30; $i++) {
            $rows[] = [
                'student_id' => 'S'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'hours' => $i === 30 ? 120 : ($i % 8) + 1,
                'program' => $i % 3 === 0 ? null : ($i % 2 === 0 ? 'BSIT' : 'BSCS'),
                'passed' => $i <= 4 ? 'no' : 'yes',
                'constant' => 'same',
            ];
        }
        $rows[] = $rows[0];

        $profile = (new DatasetProfiler())->profile(array_keys($rows[0]), $rows);
        $report = (new DatasetQualityAnalyzer())->analyze($rows, $profile, 'passed', 'classification');

        $this->assertLessThan(100, $report['quality_score']);
        $this->assertGreaterThan(0, $report['summary']['missing_values']);
        $this->assertSame(1, $report['summary']['duplicate_rows']);
        $this->assertContains('constant', $report['summary']['constant_columns']);
        $this->assertTrue($report['summary']['class_balance']['applicable']);
        $this->assertNotEmpty($report['recommendations']);
    }

    public function test_it_reports_columns_with_inconsistent_data_types(): void
    {
        $rows = [];
        for ($i = 1; $i <= 20; $i++) {
            $rows[] = [
                'age' => $i === 20 ? 'unknown' : (string) (18 + $i),
                'plan' => $i <= 10 ? (string) $i : 'premium',
                'churn' => $i % 2 === 0 ? 'yes' : 'no',
            ];
        }

        $profile = (new DatasetProfiler())->profile(['age', 'plan', 'churn'], $rows);
        $report = (new DatasetQualityAnalyzer())->analyze($rows, $profile, 'churn', 'classification');

        $this->assertTrue($profile['columns']['age']['has_mixed_types']);
        $this->assertContains('plan', $profile['mixed_type_columns']);
        $this->assertNotEmpty($report['summary']['mixed_type_columns']);
        $this->assertLessThan(100, $report['quality_score']);
        $this->assertStringContainsString(
            'Standardize inconsistent data types',
            implode(' ', $report['recommendations'])
        );
    }
}
