<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds the `challenges` table with coding-challenge entries (is_coding_challenge = 1).
 *
 * One coding challenge per category (5 total). The core topic is always "Python Fundamentals",
 * but the complexity, edge cases, and performance constraints scale drastically with each tier.
 *
 * Column order: id, challenge_category_id, title, description,
 * time_limit_seconds, base_xp, order_index, is_coding_challenge,
 * created_at, updated_at
 */
class CodingChallengeSeeder extends Seeder
{
    public function run(): void
    {
        // Guard: skip if coding challenges already exist
        if (DB::table('challenges')->where('is_coding_challenge', 1)->exists()) {
            $this->command->info('Coding challenges already seeded — skipping.');
            return;
        }

        DB::table('challenges')->insert([
            // ── Newbie ──────────────────────────────────────────────────────
            [
                'challenge_category_id' => 1,
                'title'                 => 'Python Fundamentals: The Basics',
                'description'           => 'Write your first lines of Python! We will start simple: print text to the screen, use variables, and write basic if/else statements. Do not worry about speed or elegance—just get it to work!',
                'time_limit_seconds'    => 1800, // 30 mins
                'base_xp'               => 100,
                'order_index'           => 1,
                'is_coding_challenge'   => 1,
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
            // ── University Student ───────────────────────────────────────────
            [
                'challenge_category_id' => 2,
                'title'                 => 'Python Fundamentals: Logic & Formatting',
                'description'           => 'Apply core concepts to slightly trickier problems. You will need to use standard loops, format your print outputs correctly, and handle basic lists and dictionaries without breaking a sweat.',
                'time_limit_seconds'    => 1800, // 30 mins
                'base_xp'               => 300,
                'order_index'           => 1,
                'is_coding_challenge'   => 1,
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
            // ── Intermediate ─────────────────────────────────────────────────
            [
                'challenge_category_id' => 3,
                'title'                 => 'Python Fundamentals: Data Manipulation',
                'description'           => 'Standard fundamentals, but with a twist. The inputs are messier, and the expected outputs require combining control flow with efficient dictionary/list manipulation. Clean, readable code is expected.',
                'time_limit_seconds'    => 2400, // 40 mins
                'base_xp'               => 600,
                'order_index'           => 1,
                'is_coding_challenge'   => 1,
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
            // ── Advanced ─────────────────────────────────────────────────────
            [
                'challenge_category_id' => 4,
                'title'                 => 'Python Fundamentals: Edge Cases & Optimization',
                'description'           => 'You know the basics, but can you handle the edge cases? These fundamental problems will test your code against tricky corner cases. A simple nested loop will not cut it here; you need optimized logic.',
                'time_limit_seconds'    => 2400, // 40 mins
                'base_xp'               => 1000,
                'order_index'           => 1,
                'is_coding_challenge'   => 1,
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
            // ── Professional ─────────────────────────────────────────────────
            [
                'challenge_category_id' => 5,
                'title'                 => 'Python Fundamentals: Production-Grade',
                'description'           => 'A true test of mastery. It might just be printing or data mapping, but you must pass strict time constraints, handle extreme memory limits, and write bulletproof code that accounts for every possible failure state.',
                'time_limit_seconds'    => 3600, // 60 mins
                'base_xp'               => 1500,
                'order_index'           => 1,
                'is_coding_challenge'   => 1,
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
        ]);

        $this->command->info('Coding challenges seeded (5 rows).');
    }
}