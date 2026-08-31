<?php

namespace Tests\Unit;

use App\Services\DataToolkit\EdaAnalysisService;
use App\Services\DataToolkit\EdaWorkspaceService;
use App\Services\DataToolkit\StatisticsService;
use PHPUnit\Framework\TestCase;

class EdaWorkspaceServiceTest extends TestCase
{
    private EdaWorkspaceService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $statistics = new StatisticsService();
        $this->service = new EdaWorkspaceService(
            $statistics,
            new EdaAnalysisService($statistics)
        );
    }

    public function test_it_applies_beginner_friendly_cleaning_recommendations(): void
    {
        $dataset = [
            'columns' => ['group', 'score'],
            'rows' => [
                ['group' => 'A', 'score' => 10],
                ['group' => 'A', 'score' => 10],
                ['group' => '', 'score' => 20],
                ['group' => 'B', 'score' => 'not-a-number'],
            ],
        ];

        $cleaned = $this->service->cleanDataset($dataset, ['score'], ['group']);

        $this->assertCount(3, $cleaned['rows']);
        $this->assertSame(1, $cleaned['cleaning_summary']['duplicates_removed']);
        $this->assertSame(2, $cleaned['cleaning_summary']['values_filled']);
        $this->assertSame(1, $cleaned['cleaning_summary']['invalid_numeric_values_replaced']);
        $this->assertSame('A', $cleaned['rows'][1]['group']);
        $this->assertSame(15, $cleaned['rows'][2]['score']);
    }

    public function test_it_removes_only_rows_flagged_in_selected_outlier_columns(): void
    {
        $rows = [];
        foreach ([10, 11, 12, 13, 14, 15, 16, 17, 18, 100] as $score) {
            $rows[] = ['score' => $score, 'age' => 20];
        }

        $updated = $this->service->removeOutliers(
            ['columns' => ['score', 'age'], 'rows' => $rows],
            ['score'],
            ['score', 'age']
        );

        $this->assertCount(9, $updated['rows']);
        $this->assertSame(1, $updated['outlier_summary']['removed_rows']);
        $this->assertSame(['score'], $updated['outlier_summary']['columns']);
    }

    public function test_it_respects_explicit_cleaning_choices_and_standardizes_categories(): void
    {
        $dataset = [
            'columns' => ['group', 'score'],
            'rows' => [
                ['group' => 'Alpha', 'score' => 10],
                ['group' => 'alpha', 'score' => 200],
                ['group' => ' Alpha ', 'score' => null],
            ],
        ];

        $cleaned = $this->service->cleanDataset(
            $dataset,
            ['score'],
            ['group'],
            [
                'duplicates' => 'keep',
                'missing' => 'keep',
                'categories' => 'standardize',
                'invalid' => 'remove',
            ],
            ['numeric_ranges' => ['score' => ['min' => 0, 'max' => 100]]]
        );

        $this->assertCount(2, $cleaned['rows']);
        $this->assertSame(['Alpha', 'Alpha'], array_column($cleaned['rows'], 'group'));
        $this->assertNull($cleaned['rows'][1]['score']);
        $this->assertSame(2, $cleaned['cleaning_summary']['categories_standardized']);
        $this->assertSame(1, $cleaned['cleaning_summary']['invalid_rows_removed']);
        $this->assertSame(0, $cleaned['cleaning_summary']['missing_values_filled']);
    }

    public function test_it_creates_an_explained_feature_without_changing_source_columns(): void
    {
        $dataset = [
            'columns' => ['quiz_score', 'project_score'],
            'rows' => [['quiz_score' => 80, 'project_score' => 90]],
        ];
        $suggestion = [
            'name' => 'assessment_average',
            'formula' => 'assessment_average = mean(quiz_score, project_score)',
            'explanation' => 'Combines two assessment scores.',
            'operation' => 'mean',
            'columns' => ['quiz_score', 'project_score'],
        ];

        $updated = $this->service->addFeature($dataset, $suggestion);

        $this->assertSame(80, $updated['rows'][0]['quiz_score']);
        $this->assertSame(90, $updated['rows'][0]['project_score']);
        $this->assertSame(85, $updated['rows'][0]['assessment_average']);
        $this->assertContains('assessment_average', $updated['columns']);
        $this->assertSame('assessment_average', $updated['created_features'][0]['name']);
    }

    public function test_profile_reports_values_that_conflict_with_a_detected_numeric_type(): void
    {
        $statistics = new StatisticsService();
        $profile = (new EdaAnalysisService($statistics))->profile(
            [
                ['score' => 10],
                ['score' => 11],
                ['score' => 12],
                ['score' => 13],
                ['score' => 'incorrect'],
            ],
            ['score'],
            ['score'],
            [],
            ['columns' => ['score'], 'matrix' => [], 'strongest_pair' => null, 'interpretation' => '']
        );

        $this->assertSame(1, $profile['data_types']['score']['invalid_count']);
        $this->assertSame(1, $profile['type_issues'][0]['invalid_count']);
    }

    public function test_profile_reports_category_variants_and_values_outside_expected_ranges(): void
    {
        $statistics = new StatisticsService();
        $profile = (new EdaAnalysisService($statistics))->profile(
            [
                ['group' => 'North', 'score' => 10],
                ['group' => 'north', 'score' => 11],
                ['group' => ' North ', 'score' => 150],
            ],
            ['group', 'score'],
            ['score'],
            ['group'],
            ['columns' => ['score'], 'matrix' => [], 'strongest_pair' => null, 'interpretation' => ''],
            ['numeric_ranges' => ['score' => ['min' => 0, 'max' => 100]]]
        );

        $this->assertSame(2, $profile['category_issues']['total_inconsistent']);
        $this->assertSame('North', $profile['category_issues']['columns']['group']['groups'][0]['canonical']);
        $this->assertSame(1, $profile['invalid_values']['total_invalid']);
        $this->assertSame('Above the maximum of 100', $profile['invalid_values']['by_column']['score']['sample_values'][0]['reason']);
    }
}
