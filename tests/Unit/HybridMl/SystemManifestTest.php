<?php

namespace Tests\Unit\HybridMl;

use Tests\TestCase;

class SystemManifestTest extends TestCase
{
    public function test_system_manifest_contains_ten_datasets_and_pretrained_models(): void
    {
        $path = storage_path('app/ml/system/manifest.json');
        $this->assertFileExists($path);
        $manifest = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        $this->assertCount(10, $manifest['datasets']);
        $modelCount = 0;
        foreach ($manifest['datasets'] as $dataset) {
            $this->assertFileExists(storage_path('app/'.$dataset['storage_path']));
            $this->assertNotEmpty($dataset['models']);

            $recommendation = (array) ($dataset['recommended_setup'] ?? []);
            $this->assertNotEmpty($recommendation, $dataset['slug'].' must define a recommended setup.');
            $this->assertSame($dataset['target'], $recommendation['target']);
            $this->assertSame($dataset['problem_type'], $recommendation['problem_type']);
            $this->assertNotEmpty($recommendation['objective']);
            $this->assertNotEmpty($recommendation['reason']);
            $this->assertNotEmpty($recommendation['features']);
            foreach ($recommendation['features'] as $feature) {
                $this->assertContains($feature, $dataset['features']);
            }

            $algorithm = config('hybrid_ml.algorithms.'.$recommendation['algorithm_key']);
            $this->assertIsArray($algorithm, 'Recommended algorithm must exist in the DataSensei catalog.');
            $this->assertSame($dataset['problem_type'], $algorithm['problem_type']);

            foreach ($dataset['models'] as $model) {
                $this->assertFileExists(storage_path('app/'.$model['artifact_path']));
                $modelCount++;
            }
        }
        $this->assertGreaterThanOrEqual(30, $modelCount);
    }
}
