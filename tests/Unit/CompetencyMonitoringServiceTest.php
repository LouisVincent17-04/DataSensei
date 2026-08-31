<?php

namespace Tests\Unit;

use App\Services\CompetencyMonitoringService;
use PHPUnit\Framework\TestCase;

class CompetencyMonitoringServiceTest extends TestCase
{
    public function test_competency_levels_follow_the_required_thresholds(): void
    {
        $service = new CompetencyMonitoringService();

        $this->assertSame('Not Assessed', $service->levelFor(0, 0));
        $this->assertSame('Beginner', $service->levelFor(39.99));
        $this->assertSame('Developing', $service->levelFor(40));
        $this->assertSame('Competent', $service->levelFor(55));
        $this->assertSame('Advanced', $service->levelFor(70));
        $this->assertSame('Expert', $service->levelFor(85));
    }
}
