<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\AuthSessionFingerprint;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AccountDeactivationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        config()->set('session.driver', 'array');
        config()->set('cache.default', 'array');

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

        Schema::create('password_reset_otps', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->dateTime('consumed_at')->nullable();
            $table->char('reset_token_hash', 64)->nullable();
            $table->dateTime('reset_token_expires_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });

        Schema::create('class_student', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('student_id');
            $table->dateTime('enrolled_at')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('class_student');
        Schema::dropIfExists('password_reset_otps');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    public function test_learner_deactivation_preserves_user_and_academic_history(): void
    {
        $user = $this->createUser(User::ROLE_USER);
        DB::table('class_student')->insert([
            'class_id' => 77,
            'student_id' => $user->id,
            'enrolled_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('password_reset_otps')->insert([
            'user_id' => $user->id,
            'reset_token_hash' => str_repeat('a', 64),
            'reset_token_expires_at' => now()->addMinutes(5),
        ]);

        $this->actingAs($user)
            ->withSession($this->activeSession($user))
            ->delete(route('profile.delete'), ['delete_password' => 'OldPassword!123'])
            ->assertRedirect('/login')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => 'disabled',
        ]);
        $this->assertDatabaseHas('class_student', [
            'class_id' => 77,
            'student_id' => $user->id,
        ]);
        $this->assertNotNull(DB::table('password_reset_otps')->where('user_id', $user->id)->value('consumed_at'));
        $this->assertGuest();
    }

    public function test_wrong_password_cannot_deactivate_learner(): void
    {
        $user = $this->createUser(User::ROLE_USER);

        $this->actingAs($user)
            ->withSession($this->activeSession($user))
            ->from(route('profile', ['tab' => 'security']))
            ->delete(route('profile.delete'), ['delete_password' => 'WrongPassword!123'])
            ->assertSessionHasErrors('delete_password');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'status' => 'active']);
    }

    public function test_staff_account_cannot_self_deactivate(): void
    {
        $staff = $this->createUser(User::ROLE_ADMIN);

        $this->actingAs($staff)
            ->withSession($this->activeSession($staff))
            ->delete(route('profile.delete'), ['delete_password' => 'OldPassword!123'])
            ->assertSessionHasErrors('delete_password');

        $this->assertDatabaseHas('users', ['id' => $staff->id, 'status' => 'active']);
    }

    public function test_logout_is_post_only_and_post_logs_user_out(): void
    {
        $this->get('/logout')->assertStatus(405);

        $user = $this->createUser(User::ROLE_USER);
        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    private function createUser(int $role): User
    {
        return User::create([
            'name' => 'Repair Test User',
            'email' => 'repair-'.$role.'@example.test',
            'password' => Hash::make('OldPassword!123'),
            'role' => $role,
            'status' => 'active',
        ]);
    }

    /** @return array<string, string> */
    private function activeSession(User $user): array
    {
        return [
            AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($user),
        ];
    }
}
