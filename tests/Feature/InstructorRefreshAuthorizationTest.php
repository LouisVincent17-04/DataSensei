<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\CompetencyMonitoringService;
use App\Services\StudentPerformanceClusteringService;
use App\Support\AuthSessionFingerprint;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class InstructorRefreshAuthorizationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        config()->set('session.driver', 'array');
        config()->set('cache.default', 'array');

        Schema::create('institutions', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('status')->default('active');
            $table->timestamps();
        });
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->tinyInteger('role')->default(User::ROLE_USER);
            $table->string('status')->nullable()->default('active');
            $table->unsignedBigInteger('institution_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('classes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('instructor_id');
            $table->unsignedBigInteger('institution_id')->nullable();
            $table->string('name');
            $table->string('class_code', 8);
            $table->boolean('is_archived')->default(false);
            $table->boolean('allow_self_enroll')->default(false);
            $table->timestamps();
        });

        DB::table('institutions')->insert([
            'id' => 1,
            'name' => 'Test Institution',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('classes');
        Schema::dropIfExists('users');
        Schema::dropIfExists('institutions');

        parent::tearDown();
    }

    public function test_instructor_can_refresh_only_an_owned_active_class(): void
    {
        $owner = $this->createUser(User::ROLE_INSTRUCTOR, 'owner@example.test');
        $other = $this->createUser(User::ROLE_INSTRUCTOR, 'other@example.test');
        $ownedClassId = $this->createClass((int) $owner->id, 'OWNED01');
        $otherClassId = $this->createClass((int) $other->id, 'OTHER01');

        $segmentation = $this->mock(StudentPerformanceClusteringService::class);
        $segmentation->shouldReceive('refreshForClass')->twice();
        $competencies = $this->mock(CompetencyMonitoringService::class);
        $competencies->shouldReceive('refreshClass')->once();

        $client = $this->actingAs($owner)->withSession($this->activeSession($owner));
        $client->post(route('instructor.analytics.refresh'), ['class_id' => $ownedClassId])
            ->assertRedirect(route('instructor.analytics.index', ['class_id' => $ownedClassId]));
        $client->post(route('instructor.risk.refresh'), ['class_id' => $ownedClassId])
            ->assertRedirect(route('instructor.risk.index', ['class_id' => $ownedClassId]));
        $client->post(route('instructor.competencies.refresh'), ['class_id' => $ownedClassId])
            ->assertRedirect(route('instructor.competencies.index', ['class_id' => $ownedClassId]));

        $client->post(route('instructor.analytics.refresh'), ['class_id' => $otherClassId])
            ->assertNotFound();
    }

    public function test_malformed_class_id_is_rejected_before_refresh(): void
    {
        $owner = $this->createUser(User::ROLE_INSTRUCTOR, 'valid-owner@example.test');

        $this->mock(StudentPerformanceClusteringService::class)
            ->shouldNotReceive('refreshForClass');

        $this->actingAs($owner)
            ->withSession($this->activeSession($owner))
            ->post(route('instructor.analytics.refresh'), ['class_id' => 'not-a-number'])
            ->assertSessionHasErrors('class_id');
    }

    public function test_student_role_cannot_call_instructor_refresh_route(): void
    {
        $student = $this->createUser(User::ROLE_USER, 'student@example.test');

        $this->actingAs($student)
            ->withSession($this->activeSession($student))
            ->post(route('instructor.analytics.refresh'), ['class_id' => 1])
            ->assertForbidden();
    }

    private function createUser(int $role, string $email): User
    {
        return User::create([
            'name' => 'Refresh Test User',
            'email' => $email,
            'password' => Hash::make('TestPassword!123'),
            'role' => $role,
            'status' => 'active',
            'institution_id' => $role === User::ROLE_INSTRUCTOR ? 1 : null,
        ]);
    }

    private function createClass(int $instructorId, string $code): int
    {
        return (int) DB::table('classes')->insertGetId([
            'instructor_id' => $instructorId,
            'institution_id' => 1,
            'name' => 'Test Class '.$code,
            'class_code' => $code,
            'is_archived' => false,
            'allow_self_enroll' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /** @return array<string, string> */
    private function activeSession(User $user): array
    {
        return [AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($user)];
    }
}
