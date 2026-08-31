<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\TableOfSpecificationService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TableOfSpecificationCustomCoverageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');

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

        Schema::create('intended_learning_outcomes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('module_no');
            $table->string('ilo_code', 80)->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('table_of_specifications', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedInteger('module_no');
            $table->string('custom_coverage', 191)->nullable();
            $table->unsignedInteger('total_items');
            $table->string('title');
            $table->string('status', 30)->default('draft');
            $table->text('cognitive_distribution')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('table_of_specification_rows', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('table_of_specification_id');
            $table->unsignedBigInteger('ilo_id')->nullable();
            $table->string('topic_title');
            $table->string('subtopic_title')->nullable();
            $table->text('learning_objective')->nullable();
            $table->string('difficulty_slug', 40);
            $table->unsignedInteger('item_count')->default(0);
            $table->unsignedInteger('default_points')->default(1);
            $table->string('cognitive_level', 80)->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('table_of_specification_rows');
        Schema::dropIfExists('table_of_specifications');
        Schema::dropIfExists('intended_learning_outcomes');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    public function test_custom_coverage_is_saved_displayed_and_used_for_generated_rows(): void
    {
        $instructor = User::create([
            'name' => 'Coverage Instructor',
            'email' => 'coverage@example.test',
            'password' => 'TestPassword!123',
            'role' => User::ROLE_INSTRUCTOR,
            'status' => 'active',
        ]);
        $this->actingAs($instructor);

        $tos = app(TableOfSpecificationService::class)->createBlueprint(
            null,
            0,
            'Responsible AI Quiz',
            20,
            'Data Ethics and Responsible AI'
        );

        $this->assertSame(0, $tos->module_no);
        $this->assertSame('Data Ethics and Responsible AI', $tos->custom_coverage);
        $this->assertSame('Data Ethics and Responsible AI', $tos->coverage_label);
        $this->assertCount(4, $tos->rows);
        $this->assertSame(['Data Ethics and Responsible AI'], $tos->rows->pluck('topic_title')->unique()->values()->all());
        $this->assertSame(20, (int) $tos->rows->sum('item_count'));
        $this->assertTrue($tos->rows->every(fn ($row) => $row->ilo_id === null));
    }
}
