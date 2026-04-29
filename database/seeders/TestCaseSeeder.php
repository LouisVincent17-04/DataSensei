<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds `test_cases` for every coding_question.
 *
 * Each question gets:
 *   - 2 visible sample cases (is_hidden = false) — shown on the quiz UI
 *   - 2 hidden judge cases  (is_hidden = true)   — revealed only in results
 *
 * Questions are matched by (challenge_category_id + order_index) via a join,
 * so this seeder is safe regardless of auto-increment IDs.
 */
class TestCaseSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('test_cases')->exists()) {
            $this->command->info('test_cases already seeded — skipping.');
            return;
        }

        // Resolve question IDs: keyed as "category_slug:order_index"
        $rows = DB::table('coding_questions as cq')
            ->join('challenges as c', 'c.id', '=', 'cq.challenge_id')
            ->join('challenge_categories as cc', 'cc.id', '=', 'c.challenge_category_id')
            ->where('c.is_coding_challenge', 1)
            ->select('cq.id as qid', 'cc.slug as cat', 'cq.order_index as ord')
            ->get();

        $qMap = []; // "slug:ord" => qid
        foreach ($rows as $r) {
            $qMap["{$r->cat}:{$r->ord}"] = $r->qid;
        }

        $cases = [];

        // ────────────────────────────────────────────────────────────────────
        // NEWBIE
        // ────────────────────────────────────────────────────────────────────

        // Q1: Hello, World!
        if ($qid = $qMap['newbie:1'] ?? null) {
            $cases = array_merge($cases, [
                ['coding_question_id' => $qid, 'input' => null,  'expected_output' => 'Hello, World!', 'is_hidden' => false, 'order_index' => 1],
                ['coding_question_id' => $qid, 'input' => null,  'expected_output' => 'Hello, World!', 'is_hidden' => false, 'order_index' => 2],
                ['coding_question_id' => $qid, 'input' => null,  'expected_output' => 'Hello, World!', 'is_hidden' => true,  'order_index' => 3],
                ['coding_question_id' => $qid, 'input' => null,  'expected_output' => 'Hello, World!', 'is_hidden' => true,  'order_index' => 4],
            ]);
        }

        // Q2: Square of a number
        if ($qid = $qMap['newbie:2'] ?? null) {
            $cases = array_merge($cases, [
                ['coding_question_id' => $qid, 'input' => '4',   'expected_output' => '16',  'is_hidden' => false, 'order_index' => 1],
                ['coding_question_id' => $qid, 'input' => '3',   'expected_output' => '9',   'is_hidden' => false, 'order_index' => 2],
                ['coding_question_id' => $qid, 'input' => '0',   'expected_output' => '0',   'is_hidden' => true,  'order_index' => 3],
                ['coding_question_id' => $qid, 'input' => '10',  'expected_output' => '100', 'is_hidden' => true,  'order_index' => 4],
            ]);
        }

        // Q3: Sum of two numbers
        if ($qid = $qMap['newbie:3'] ?? null) {
            $cases = array_merge($cases, [
                ['coding_question_id' => $qid, 'input' => "3\n7",   'expected_output' => '10', 'is_hidden' => false, 'order_index' => 1],
                ['coding_question_id' => $qid, 'input' => "1\n1",   'expected_output' => '2',  'is_hidden' => false, 'order_index' => 2],
                ['coding_question_id' => $qid, 'input' => "0\n0",   'expected_output' => '0',  'is_hidden' => true,  'order_index' => 3],
                ['coding_question_id' => $qid, 'input' => "99\n1",  'expected_output' => '100','is_hidden' => true,  'order_index' => 4],
            ]);
        }

        // ────────────────────────────────────────────────────────────────────
        // UNIVERSITY STUDENT
        // ────────────────────────────────────────────────────────────────────

        // Q1: is_even
        if ($qid = $qMap['university-student:1'] ?? null) {
            $cases = array_merge($cases, [
                ['coding_question_id' => $qid, 'input' => '4',  'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 1],
                ['coding_question_id' => $qid, 'input' => '7',  'expected_output' => 'False', 'is_hidden' => false, 'order_index' => 2],
                ['coding_question_id' => $qid, 'input' => '0',  'expected_output' => 'True',  'is_hidden' => true,  'order_index' => 3],
                ['coding_question_id' => $qid, 'input' => '-3', 'expected_output' => 'False', 'is_hidden' => true,  'order_index' => 4],
            ]);
        }

        // Q2: max of list
        if ($qid = $qMap['university-student:2'] ?? null) {
            $cases = array_merge($cases, [
                ['coding_question_id' => $qid, 'input' => '3 1 4 1 5 9 2 6', 'expected_output' => '9',   'is_hidden' => false, 'order_index' => 1],
                ['coding_question_id' => $qid, 'input' => '10 20 30',         'expected_output' => '30',  'is_hidden' => false, 'order_index' => 2],
                ['coding_question_id' => $qid, 'input' => '-5 -1 -3',         'expected_output' => '-1',  'is_hidden' => true,  'order_index' => 3],
                ['coding_question_id' => $qid, 'input' => '42',               'expected_output' => '42',  'is_hidden' => true,  'order_index' => 4],
            ]);
        }

        // Q3: factorial
        if ($qid = $qMap['university-student:3'] ?? null) {
            $cases = array_merge($cases, [
                ['coding_question_id' => $qid, 'input' => '5',  'expected_output' => '120',       'is_hidden' => false, 'order_index' => 1],
                ['coding_question_id' => $qid, 'input' => '3',  'expected_output' => '6',          'is_hidden' => false, 'order_index' => 2],
                ['coding_question_id' => $qid, 'input' => '0',  'expected_output' => '1',          'is_hidden' => true,  'order_index' => 3],
                ['coding_question_id' => $qid, 'input' => '10', 'expected_output' => '3628800',    'is_hidden' => true,  'order_index' => 4],
            ]);
        }

        // ────────────────────────────────────────────────────────────────────
        // INTERMEDIATE
        // ────────────────────────────────────────────────────────────────────

        // Q1: vowel count
        if ($qid = $qMap['intermediate:1'] ?? null) {
            $cases = array_merge($cases, [
                ['coding_question_id' => $qid, 'input' => 'Hello World',      'expected_output' => "e=1\no=2",       'is_hidden' => false, 'order_index' => 1],
                ['coding_question_id' => $qid, 'input' => 'aeiou',            'expected_output' => "a=1\ne=1\ni=1\no=1\nu=1", 'is_hidden' => false, 'order_index' => 2],
                ['coding_question_id' => $qid, 'input' => 'Python',           'expected_output' => "o=1",           'is_hidden' => true,  'order_index' => 3],
                ['coding_question_id' => $qid, 'input' => 'rhythm',           'expected_output' => '',              'is_hidden' => true,  'order_index' => 4],
            ]);
        }

        // Q2: deduplicate and sort
        if ($qid = $qMap['intermediate:2'] ?? null) {
            $cases = array_merge($cases, [
                ['coding_question_id' => $qid, 'input' => '4 2 7 2 4 1',  'expected_output' => '1 2 4 7', 'is_hidden' => false, 'order_index' => 1],
                ['coding_question_id' => $qid, 'input' => '5 5 5',        'expected_output' => '5',       'is_hidden' => false, 'order_index' => 2],
                ['coding_question_id' => $qid, 'input' => '3 1 2',        'expected_output' => '1 2 3',   'is_hidden' => true,  'order_index' => 3],
                ['coding_question_id' => $qid, 'input' => '10',           'expected_output' => '10',      'is_hidden' => true,  'order_index' => 4],
            ]);
        }

        // Q3: flatten nested list
        if ($qid = $qMap['intermediate:3'] ?? null) {
            $cases = array_merge($cases, [
                ['coding_question_id' => $qid, 'input' => '1 2,3 4,5',    'expected_output' => '1 2 3 4 5', 'is_hidden' => false, 'order_index' => 1],
                ['coding_question_id' => $qid, 'input' => '10,20,30',     'expected_output' => '10 20 30',   'is_hidden' => false, 'order_index' => 2],
                ['coding_question_id' => $qid, 'input' => '1 2 3,4 5 6',  'expected_output' => '1 2 3 4 5 6','is_hidden' => true,  'order_index' => 3],
                ['coding_question_id' => $qid, 'input' => '7',            'expected_output' => '7',           'is_hidden' => true,  'order_index' => 4],
            ]);
        }

        // ────────────────────────────────────────────────────────────────────
        // ADVANCED
        // ────────────────────────────────────────────────────────────────────

        // Q1: primes up to n
        if ($qid = $qMap['advanced:1'] ?? null) {
            $cases = array_merge($cases, [
                ['coding_question_id' => $qid, 'input' => '20',  'expected_output' => '2 3 5 7 11 13 17 19', 'is_hidden' => false, 'order_index' => 1],
                ['coding_question_id' => $qid, 'input' => '10',  'expected_output' => '2 3 5 7',             'is_hidden' => false, 'order_index' => 2],
                ['coding_question_id' => $qid, 'input' => '2',   'expected_output' => '2',                   'is_hidden' => true,  'order_index' => 3],
                ['coding_question_id' => $qid, 'input' => '50',  'expected_output' => '2 3 5 7 11 13 17 19 23 29 31 37 41 43 47', 'is_hidden' => true, 'order_index' => 4],
            ]);
        }

        // Q2: binary search
        if ($qid = $qMap['advanced:2'] ?? null) {
            $cases = array_merge($cases, [
                ['coding_question_id' => $qid, 'input' => "1 3 5 7 9 11\n7",   'expected_output' => '3',  'is_hidden' => false, 'order_index' => 1],
                ['coding_question_id' => $qid, 'input' => "1 3 5 7 9 11\n1",   'expected_output' => '0',  'is_hidden' => false, 'order_index' => 2],
                ['coding_question_id' => $qid, 'input' => "1 3 5 7 9 11\n4",   'expected_output' => '-1', 'is_hidden' => true,  'order_index' => 3],
                ['coding_question_id' => $qid, 'input' => "2 4 6 8 10\n10",    'expected_output' => '4',  'is_hidden' => true,  'order_index' => 4],
            ]);
        }

        // Q3: palindrome check
        if ($qid = $qMap['advanced:3'] ?? null) {
            $cases = array_merge($cases, [
                ['coding_question_id' => $qid, 'input' => 'A man a plan a canal Panama', 'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 1],
                ['coding_question_id' => $qid, 'input' => 'racecar',                     'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 2],
                ['coding_question_id' => $qid, 'input' => 'hello',                       'expected_output' => 'False', 'is_hidden' => true,  'order_index' => 3],
                ['coding_question_id' => $qid, 'input' => 'Was it a car or a cat I saw', 'expected_output' => 'True',  'is_hidden' => true,  'order_index' => 4],
            ]);
        }

        // ────────────────────────────────────────────────────────────────────
        // PROFESSIONAL
        // ────────────────────────────────────────────────────────────────────

        // Q1: fibonacci with memoization
        if ($qid = $qMap['professional:1'] ?? null) {
            $cases = array_merge($cases, [
                ['coding_question_id' => $qid, 'input' => '10',  'expected_output' => '55',      'is_hidden' => false, 'order_index' => 1],
                ['coding_question_id' => $qid, 'input' => '1',   'expected_output' => '1',       'is_hidden' => false, 'order_index' => 2],
                ['coding_question_id' => $qid, 'input' => '0',   'expected_output' => '0',       'is_hidden' => true,  'order_index' => 3],
                ['coding_question_id' => $qid, 'input' => '20',  'expected_output' => '6765',    'is_hidden' => true,  'order_index' => 4],
            ]);
        }

        // Q2: word frequency
        if ($qid = $qMap['professional:2'] ?? null) {
            $cases = array_merge($cases, [
                ['coding_question_id' => $qid, 'input' => 'the cat sat on the mat the cat', 'expected_output' => "cat 2\nmat 1\non 1\nsat 1\nthe 3", 'is_hidden' => false, 'order_index' => 1],
                ['coding_question_id' => $qid, 'input' => 'hello hello world',              'expected_output' => "hello 2\nworld 1",                   'is_hidden' => false, 'order_index' => 2],
                ['coding_question_id' => $qid, 'input' => 'a b a c b a',                   'expected_output' => "a 3\nb 2\nc 1",                        'is_hidden' => true,  'order_index' => 3],
                ['coding_question_id' => $qid, 'input' => 'one',                            'expected_output' => "one 1",                               'is_hidden' => true,  'order_index' => 4],
            ]);
        }

        // Q3: stack simulation
        if ($qid = $qMap['professional:3'] ?? null) {
            $cases = array_merge($cases, [
                ['coding_question_id' => $qid, 'input' => "PUSH 5\nPUSH 3\nPEEK\nPOP\nPOP\nPOP",  'expected_output' => "3\n3\n5\nEMPTY",  'is_hidden' => false, 'order_index' => 1],
                ['coding_question_id' => $qid, 'input' => "POP",                                   'expected_output' => "EMPTY",            'is_hidden' => false, 'order_index' => 2],
                ['coding_question_id' => $qid, 'input' => "PUSH 1\nPUSH 2\nPUSH 3\nPOP\nPEEK",   'expected_output' => "3\n2",             'is_hidden' => true,  'order_index' => 3],
                ['coding_question_id' => $qid, 'input' => "PUSH 99\nPEEK\nPEEK",                  'expected_output' => "99\n99",           'is_hidden' => true,  'order_index' => 4],
            ]);
        }

        // Add timestamps to all rows
        $now = now()->toDateTimeString();
        $cases = array_map(fn($c) => array_merge($c, ['created_at' => $now, 'updated_at' => $now]), $cases);

        DB::table('test_cases')->insert($cases);
        $this->command->info('test_cases seeded (' . count($cases) . ' rows).');
    }
}