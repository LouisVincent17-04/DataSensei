<?php

namespace Tests\Unit;

use App\Services\DataToolkit\CorrelationService;
use App\Services\DataToolkit\EdaAnalysisService;
use App\Services\DataToolkit\StatisticsService;
use PHPUnit\Framework\TestCase;

class EdaAnalysisServiceTest extends TestCase
{
    public function test_it_detects_types_missing_duplicates_and_outliers(): void
    {
        $rows = [
            ['id' => 'A1', 'group' => 'A', 'score' => 10],
            ['id' => 'A2', 'group' => 'A', 'score' => 11],
            ['id' => 'A3', 'group' => 'B', 'score' => 12],
            ['id' => 'A4', 'group' => 'B', 'score' => 13],
            ['id' => 'A5', 'group' => 'B', 'score' => 14],
            ['id' => 'A6', 'group' => '', 'score' => 15],
            ['id' => 'A7', 'group' => 'C', 'score' => 100],
            ['id' => 'A3', 'group' => 'B', 'score' => 12],
        ];
        $columns = ['id', 'group', 'score'];
        $numeric = ['score'];
        $statistics = new StatisticsService();
        $correlation = (new CorrelationService($statistics))->matrix($rows, $numeric);
        $service = new EdaAnalysisService($statistics);

        $profile = $service->profile($rows, $columns, $numeric, ['id', 'group'], $correlation);

        $this->assertSame('Integer', $profile['data_types']['score']['type']);
        $this->assertSame('Identifier', $profile['data_types']['id']['type']);
        $this->assertSame(1, $profile['missing']['total_missing']);
        $this->assertSame(1, $profile['duplicates']['duplicate_records']);
        $this->assertSame(1, $profile['duplicates']['duplicate_groups']);
        $this->assertGreaterThanOrEqual(1, $profile['outliers']['columns']['score']['outlier_count']);
        $this->assertNotEmpty($profile['histograms']['score']['labels']);
        $this->assertArrayHasKey('score', $profile['box_plots']);
    }
}
