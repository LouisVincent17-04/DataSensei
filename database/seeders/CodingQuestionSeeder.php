<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds `coding_questions` for each coding challenge.
 *
 * Each challenge gets 3 questions of increasing difficulty.
 * challenge_id values are looked up dynamically so the seeder
 * is safe to run regardless of auto-increment state.
 */
class CodingQuestionSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('coding_questions')->exists()) {
            $this->command->info('coding_questions already seeded — skipping.');
            return;
        }

        // Fetch IDs in category order
        $challenges = DB::table('challenges')
            ->where('is_coding_challenge', 1)
            ->orderBy('challenge_category_id')
            ->orderBy('order_index')
            ->pluck('id', 'challenge_category_id'); // category_id => challenge_id

        $questions = [];

        // ── Newbie (category 1) ──────────────────────────────────────────────
        $newbie = $challenges[1] ?? null;
        if ($newbie) {
            $questions = array_merge($questions, [
                [
                    'challenge_id'       => $newbie,
                    'problem_description'=> "Print the text Hello, World! to the screen.\n\nYour output must match exactly (no extra spaces or punctuation).",
                    'language'           => 'python',
                    'starter_code'       => "# Write your solution below\n",
                    'order_index'        => 1,
                    'time_limit_seconds' => 600,
                    'base_xp'            => 100,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ],
                [
                    'challenge_id'       => $newbie,
                    'problem_description'=> "Write a program that takes a number as input and prints its square.\n\nExample:\n  Input:  4\n  Output: 16",
                    'language'           => 'python',
                    'starter_code'       => "n = int(input())\n# Write your solution below\n",
                    'order_index'        => 2,
                    'time_limit_seconds' => 600,
                    'base_xp'            => 150,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ],
                [
                    'challenge_id'       => $newbie,
                    'problem_description'=> "Read two integers from input (one per line) and print their sum.\n\nExample:\n  Input:  3\n          7\n  Output: 10",
                    'language'           => 'python',
                    'starter_code'       => "a = int(input())\nb = int(input())\n# Write your solution below\n",
                    'order_index'        => 3,
                    'time_limit_seconds' => 600,
                    'base_xp'            => 150,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ],
            ]);
        }

        // ── University Student (category 2) ─────────────────────────────────
        $uni = $challenges[2] ?? null;
        if ($uni) {
            $questions = array_merge($questions, [
                [
                    'challenge_id'       => $uni,
                    'problem_description'=> "Write a function is_even(n) that returns True if n is even, False otherwise.\nThen print the result for the given input.\n\nExample:\n  Input:  4\n  Output: True",
                    'language'           => 'python',
                    'starter_code'       => "def is_even(n):\n    pass  # your code here\n\nn = int(input())\nprint(is_even(n))\n",
                    'order_index'        => 1,
                    'time_limit_seconds' => 600,
                    'base_xp'            => 150,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ],
                [
                    'challenge_id'       => $uni,
                    'problem_description'=> "Given a list of integers provided as space-separated input, print the maximum value.\n\nExample:\n  Input:  3 1 4 1 5 9 2 6\n  Output: 9",
                    'language'           => 'python',
                    'starter_code'       => "nums = list(map(int, input().split()))\n# Write your solution below\n",
                    'order_index'        => 2,
                    'time_limit_seconds' => 600,
                    'base_xp'            => 200,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ],
                [
                    'challenge_id'       => $uni,
                    'problem_description'=> "Write a function factorial(n) that returns n! (n factorial).\nPrint the result for the given input.\n\nExample:\n  Input:  5\n  Output: 120",
                    'language'           => 'python',
                    'starter_code'       => "def factorial(n):\n    pass  # your code here\n\nn = int(input())\nprint(factorial(n))\n",
                    'order_index'        => 3,
                    'time_limit_seconds' => 600,
                    'base_xp'            => 250,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ],
            ]);
        }

        // ── Intermediate (category 3) ────────────────────────────────────────
        $inter = $challenges[3] ?? null;
        if ($inter) {
            $questions = array_merge($questions, [
                [
                    'challenge_id'       => $inter,
                    'problem_description'=> "Given a string, print the count of each vowel (a, e, i, o, u) found in it (case-insensitive).\nOutput format: one vowel=count pair per line, in alphabetical order, only if count > 0.\n\nExample:\n  Input:  Hello World\n  Output: e=1\n          o=2",
                    'language'           => 'python',
                    'starter_code'       => "s = input()\n# Write your solution below\n",
                    'order_index'        => 1,
                    'time_limit_seconds' => 900,
                    'base_xp'            => 250,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ],
                [
                    'challenge_id'       => $inter,
                    'problem_description'=> "Given a list of integers (space-separated), remove duplicates and print the sorted unique values.\n\nExample:\n  Input:  4 2 7 2 4 1\n  Output: 1 2 4 7",
                    'language'           => 'python',
                    'starter_code'       => "nums = list(map(int, input().split()))\n# Write your solution below\n",
                    'order_index'        => 2,
                    'time_limit_seconds' => 900,
                    'base_xp'            => 300,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ],
                [
                    'challenge_id'       => $inter,
                    'problem_description'=> "Write a function flatten(lst) that takes a nested list (up to 2 levels deep) and returns a flat list.\nPrint the flattened list as space-separated integers.\n\nExample:\n  Input:  1 2,3 4,5\n  (comma separates inner lists)\n  Output: 1 2 3 4 5",
                    'language'           => 'python',
                    'starter_code'       => "raw = input().split(',')\nnested = [list(map(int, g.split())) for g in raw]\n\ndef flatten(lst):\n    pass  # your code here\n\nprint(*flatten(nested))\n",
                    'order_index'        => 3,
                    'time_limit_seconds' => 900,
                    'base_xp'            => 350,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ],
            ]);
        }

        // ── Advanced (category 4) ────────────────────────────────────────────
        $adv = $challenges[4] ?? null;
        if ($adv) {
            $questions = array_merge($questions, [
                [
                    'challenge_id'       => $adv,
                    'problem_description'=> "Given an integer n, print all prime numbers up to and including n, space-separated.\n\nExample:\n  Input:  20\n  Output: 2 3 5 7 11 13 17 19",
                    'language'           => 'python',
                    'starter_code'       => "n = int(input())\n# Write your solution below\n",
                    'order_index'        => 1,
                    'time_limit_seconds' => 1200,
                    'base_xp'            => 350,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ],
                [
                    'challenge_id'       => $adv,
                    'problem_description'=> "Implement binary search. Given a sorted list of integers and a target, print the index of the target or -1 if not found.\n\nInput line 1: space-separated sorted integers\nInput line 2: target integer\n\nExample:\n  Input:  1 3 5 7 9 11\n          7\n  Output: 3",
                    'language'           => 'python',
                    'starter_code'       => "nums = list(map(int, input().split()))\ntarget = int(input())\n\ndef binary_search(arr, target):\n    pass  # your code here\n\nprint(binary_search(nums, target))\n",
                    'order_index'        => 2,
                    'time_limit_seconds' => 1200,
                    'base_xp'            => 400,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ],
                [
                    'challenge_id'       => $adv,
                    'problem_description'=> "Given a string, determine if it is a palindrome (ignoring spaces and case). Print True or False.\n\nExample:\n  Input:  A man a plan a canal Panama\n  Output: True",
                    'language'           => 'python',
                    'starter_code'       => "s = input()\n# Write your solution below\n",
                    'order_index'        => 3,
                    'time_limit_seconds' => 1200,
                    'base_xp'            => 400,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ],
            ]);
        }

        // ── Professional (category 5) ────────────────────────────────────────
        $pro = $challenges[5] ?? null;
        if ($pro) {
            $questions = array_merge($questions, [
                [
                    'challenge_id'       => $pro,
                    'problem_description'=> "Implement a function that computes the nth Fibonacci number using memoization (not recursion without caching). Print the result.\n\nExample:\n  Input:  10\n  Output: 55",
                    'language'           => 'python',
                    'starter_code'       => "import sys\nfrom functools import lru_cache\n\n@lru_cache(maxsize=None)\ndef fib(n):\n    pass  # your code here\n\nn = int(input())\nprint(fib(n))\n",
                    'order_index'        => 1,
                    'time_limit_seconds' => 1200,
                    'base_xp'            => 400,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ],
                [
                    'challenge_id'       => $pro,
                    'problem_description'=> "Given a list of words (space-separated), return a dictionary of word frequencies and print each word with its count, sorted alphabetically.\n\nExample:\n  Input:  the cat sat on the mat the cat\n  Output: cat 2\n          mat 1\n          on 1\n          sat 1\n          the 3",
                    'language'           => 'python',
                    'starter_code'       => "words = input().split()\n# Write your solution below\n",
                    'order_index'        => 2,
                    'time_limit_seconds' => 1200,
                    'base_xp'            => 450,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ],
                [
                    'challenge_id'       => $pro,
                    'problem_description'=> "Implement a stack class with push, pop, and peek methods. Then process a sequence of commands.\n\nCommands (one per line):\n  PUSH <value>  — push integer value\n  POP           — pop and print top (or print EMPTY)\n  PEEK          — print top without removing (or print EMPTY)\n\nInput ends at EOF.\n\nExample:\n  Input:  PUSH 5\n          PUSH 3\n          PEEK\n          POP\n          POP\n          POP\n  Output: 3\n          3\n          5\n          EMPTY",
                    'language'           => 'python',
                    'starter_code'       => "import sys\n\nclass Stack:\n    def __init__(self):\n        pass  # your code here\n\n    def push(self, val):\n        pass\n\n    def pop(self):\n        pass\n\n    def peek(self):\n        pass\n\nstack = Stack()\nfor line in sys.stdin:\n    line = line.strip()\n    if not line:\n        continue\n    if line.startswith('PUSH'):\n        stack.push(int(line.split()[1]))\n    elif line == 'POP':\n        print(stack.pop())\n    elif line == 'PEEK':\n        print(stack.peek())\n",
                    'order_index'        => 3,
                    'time_limit_seconds' => 1500,
                    'base_xp'            => 500,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ],
            ]);
        }

        DB::table('coding_questions')->insert($questions);
        $this->command->info('coding_questions seeded (' . count($questions) . ' rows).');
    }
}