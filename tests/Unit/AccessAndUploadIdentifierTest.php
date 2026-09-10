<?php

namespace Tests\Unit;

use App\Models\MlModel;
use App\Models\TrainingJob;
use App\Models\User;
use App\Models\UserDataset;
use App\Services\HybridMl\MlAccessService;
use App\Services\ModelDevelopment\UploadedCsvDatasetService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AccessAndUploadIdentifierTest extends TestCase
{
    public function test_ml_access_allows_system_resources_owners_and_platform_staff(): void
    {
        $service = new MlAccessService();
        $owner = $this->user(10, User::ROLE_USER);
        $otherStudent = $this->user(11, User::ROLE_USER);
        $admin = $this->user(12, User::ROLE_ADMIN);

        $dataset = new UserDataset(['user_id' => 10, 'class_id' => null]);
        $userModel = new MlModel(['user_id' => 10, 'pipeline_type' => 'user', 'class_id' => null]);
        $systemModel = new MlModel(['user_id' => null, 'pipeline_type' => 'system', 'class_id' => null]);
        $job = new TrainingJob(['user_id' => 10, 'class_id' => null]);

        $this->assertTrue($service->canUseClass($owner, null));
        $this->assertTrue($service->canUseClass($admin, 999));
        $this->assertTrue($service->canViewUserDataset($owner, $dataset));
        $this->assertFalse($service->canViewUserDataset($otherStudent, $dataset));
        $this->assertTrue($service->canViewModel($owner, $userModel));
        $this->assertFalse($service->canViewModel($otherStudent, $userModel));
        $this->assertTrue($service->canViewModel($otherStudent, $systemModel));
        $this->assertTrue($service->canViewTrainingJob($owner, $job));
        $this->assertFalse($service->canViewTrainingJob($otherStudent, $job));
    }

    #[DataProvider('uploadedDatasetKeyProvider')]
    public function test_uploaded_dataset_keys_accept_only_safe_uuid_identifiers(string $key, bool $expected): void
    {
        $service = new UploadedCsvDatasetService();

        $this->assertSame($expected, $service->isUploadedKey($key));
    }

    public function test_valid_uploaded_csv_can_be_stored_read_and_deleted_for_its_owner(): void
    {
        $userId = 987654321;
        $directory = storage_path('app/model_development/uploads/'.$userId);
        $csv = "study_hours,final_score\n";
        foreach (range(1, 20) as $row) {
            $csv .= $row.','.($row * 4)."\n";
        }

        $service = new UploadedCsvDatasetService();
        $file = UploadedFile::fake()->createWithContent('student_scores.csv', $csv);

        try {
            $stored = $service->store($file, $userId);
            $this->assertTrue($service->isUploadedKey($stored['key']));
            $this->assertSame('Student Scores', $stored['title']);
            $this->assertSame(['study_hours', 'final_score'], $stored['columns']);
            $this->assertCount(20, $stored['rows']);
            $this->assertSame($stored, $service->find($stored['key'], $userId));

            $service->delete($stored['key'], $userId);
            $this->assertNull($service->find($stored['key'], $userId));
        } finally {
            File::deleteDirectory($directory);
        }
    }

    /** @return iterable<string, array{string, bool}> */
    public static function uploadedDatasetKeyProvider(): iterable
    {
        yield 'valid lowercase uuid' => ['csv-123e4567-e89b-42d3-a456-426614174000', true];
        yield 'valid uppercase uuid' => ['csv-123E4567-E89B-42D3-A456-426614174000', true];
        yield 'missing prefix' => ['123e4567-e89b-42d3-a456-426614174000', false];
        yield 'invalid uuid version' => ['csv-123e4567-e89b-02d3-a456-426614174000', false];
        yield 'path traversal' => ['csv-../../storage/private', false];
        yield 'extra suffix' => ['csv-123e4567-e89b-42d3-a456-426614174000.json', false];
    }

    private function user(int $id, int $role): User
    {
        return (new User(['role' => $role, 'status' => 'active']))->forceFill(['id' => $id]);
    }
}
