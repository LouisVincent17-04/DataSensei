<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

         $this->call([
            Module1LessonsSeeder::class,
            Module2LessonsSeeder::class,
            Module3LessonsSeeder::class,
            Module4LessonsSeeder::class,
            Module5LessonsSeeder::class,
            Module6LessonsSeeder::class,
            Module7LessonsSeeder::class,
            Module8LessonsSeeder::class,    
            Module9LessonsSeeder::class,
            Module10LessonsSeeder::class,
            Module11LessonsSeeder::class,
            Module12LessonsSeeder::class,
            Module13LessonsSeeder::class,
            Module14LessonsSeeder::class,
            Module15LessonsSeeder::class,
            Module16LessonsSeeder::class,
            Module17LessonsSeeder::class,
            Module18LessonsSeeder::class,
            Module19LessonsSeeder::class,
            Module20LessonsSeeder::class,
            Module21LessonsSeeder::class,
            Module22LessonsSeeder::class,
            Module23LessonsSeeder::class,
            Module24LessonsSeeder::class,
        ]);
    }

    
}
