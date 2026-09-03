<?php

namespace Tests\Feature;

use App\Models\TableOfSpecification;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class InstructorAssessmentClassBindingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        config()->set('session.driver', 'array');
        $this->withoutMiddleware();
        $this->createTables();
    }

    protected function tearDown(): void
    {
        foreach ([
            'assessments',
            'table_of_specification_rows',
            'table_of_specifications',
            'classes',
            'users',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        parent::tearDown();
    }

    public function test_class_specific_tos_is_rejected_for_another_owned_class(): void
    {
        $instructor = User::create([
            'name' => 'TOS Instructor',
            'email' => 'tos-instructor@example.test',
            'password' => 'TestPassword!123',
            'role' => User::ROLE_INSTRUCTOR,
            'status' => 'active',
        ]);
        $sourceClassId = $this->createClass($instructor->id, 'Source Class', 'SOURCE1');
        $otherClassId = $this->createClass($instructor->id, 'Other Class', 'OTHER01');
        $tos = TableOfSpecification::create([
            'class_id' => $sourceClassId,
            'module_no' => 1,
            'total_items' => 1,
            'title' => 'Class-bound TOS',
            'status' => 'draft',
            'cognitive_distribution' => [],
            'created_by' => $instructor->id,
        ]);
        $returnUrl = route('instructor.assessments.create', $tos);

        $response = $this->actingAs($instructor)
            ->from($returnUrl)
            ->post(route('instructor.assessments.store', $tos), [
                'class_id' => $otherClassId,
                'title' => 'Wrong-class assessment',
                'max_attempts' => 1,
            ]);

        $response
            ->assertRedirect($returnUrl)
            ->assertSessionHasErrors([
                'class_id' => 'This class-specific TOS can only be used with the class it was created for.',
            ]);
        $this->assertDatabaseCount('assessments', 0);
        $this->assertFalse($tos->canBeUsedForClass($otherClassId));
        $this->assertTrue($tos->canBeUsedForClass($sourceClassId));
        $this->assertTrue(
            (new TableOfSpecification(['class_id' => null]))->canBeUsedForClass($otherClassId)
        );
    }

    private function createClass(int $instructorId, string $name, string $code): int
    {
        return DB::table('classes')->insertGetId([
            'instructor_id' => $instructorId,
            'name' => $name,
            'class_code' => $code,
            'is_archived' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createTables(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->tinyInteger('role');
            $table->string('status')->default('active');
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('classes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('instructor_id');
            $table->string('name');
            $table->string('class_code', 8);
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
        });
        Schema::create('table_of_specifications', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedInteger('module_no')->nullable();
            $table->unsignedInteger('total_items')->default(1);
            $table->string('title');
            $table->string('status')->default('draft');
            $table->longText('cognitive_distribution')->nullable();
            $table->timestamps();
        });
        Schema::create('table_of_specification_rows', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('table_of_specification_id');
            $table->unsignedBigInteger('ilo_id')->nullable();
            $table->unsignedInteger('item_count')->default(0);
            $table->timestamps();
        });
        Schema::create('assessments', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('table_of_specification_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('title');
            $table->timestamps();
        });
    }
}
