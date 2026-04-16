<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module1ChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {

        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (!$category) {
            $this->command->error("Newbie category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        /*
         * MODULE ORDERING FIX:
         * The map page orders challenges by `id ASC`. If other challenges were
         * seeded before this one (e.g. from the old ChallengeSeeder), they'd
         * get lower IDs and shove Python Basics out of the Module 1 spot.
         *
         * Solution: add an `order_index` column to the challenges table and
         * order by that instead. See the migration note at the bottom of this
         * file. For now we also delete any existing challenges for this category
         * and re-insert so IDs are clean.
         */

        // Remove existing challenges for this category (cascades to questions/options)
        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 1 — Basics of Python Programming (Newbie)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Basics of Python Programming',
            'description'           => 'Test your knowledge of the very basics of Python — variables, data types, simple operations, and beginner-level syntax. No prior programming experience assumed!',
            'time_limit_seconds'    => 900, // 15 minutes for 50 questions
            'base_xp'               => 500,
            'order_index'           => 1, // Ensure this appears first on the map
        ]);

        $this->command->info("Seeding 50 newbie-friendly questions...");

        $qaData = [

            // ── VARIABLES & OUTPUT ────────────────────────────────────────
            [
                'q' => 'Which of the following is the correct way to display "Hello, World!" in Python?',
                'opts' => [
                    ['echo "Hello, World!"', false],
                    ['print("Hello, World!")', true],
                    ['console.log("Hello, World!")', false],
                    ['say("Hello, World!")', false],
                ],
            ],
            [
                'q' => 'How do you create a variable called `age` and store the value 18 in it?',
                'opts' => [
                    ['int age = 18', false],
                    ['var age = 18', false],
                    ['age = 18', true],
                    ['age := 18', false],
                ],
            ],
            [
                'q' => 'What symbol is used to write a comment (a note Python ignores) in Python?',
                'opts' => [
                    ['//', false],
                    ['/* */', false],
                    ['#', true],
                    ['--', false],
                ],
            ],
            [
                'q' => 'What will `print(2 + 3)` output?',
                'opts' => [
                    ['"2 + 3"', false],
                    ['2 + 3', false],
                    ['5', true],
                    ['23', false],
                ],
            ],
            [
                'q' => 'Which of these is a valid Python variable name?',
                'opts' => [
                    ['2name', false],
                    ['my-name', false],
                    ['my_name', true],
                    ['my name', false],
                ],
            ],
            [
                'q' => 'What does the `input()` function do in Python?',
                'opts' => [
                    ['Displays text on screen', false],
                    ['Reads text typed by the user and returns it', true],
                    ['Performs a math calculation', false],
                    ['Creates a new variable automatically', false],
                ],
            ],
            [
                'q' => 'What will `print("5" + "3")` output?',
                'opts' => [
                    ['8', false],
                    ['53', true],
                    ['Error', false],
                    ['"5+3"', false],
                ],
            ],
            [
                'q' => 'How do you print the value stored in a variable called `name`?',
                'opts' => [
                    ['echo name', false],
                    ['print name', false],
                    ['print(name)', true],
                    ['display(name)', false],
                ],
            ],

            // ── DATA TYPES ────────────────────────────────────────────────
            [
                'q' => 'What data type is the value `42` in Python?',
                'opts' => [
                    ['str', false],
                    ['float', false],
                    ['int', true],
                    ['bool', false],
                ],
            ],
            [
                'q' => 'What data type is the value `3.14` in Python?',
                'opts' => [
                    ['int', false],
                    ['float', true],
                    ['str', false],
                    ['double', false],
                ],
            ],
            [
                'q' => 'What data type is `"Hello"` in Python?',
                'opts' => [
                    ['int', false],
                    ['list', false],
                    ['bool', false],
                    ['str', true],
                ],
            ],
            [
                'q' => 'What are the only two possible values of a `bool` (boolean) in Python?',
                'opts' => [
                    ['Yes and No', false],
                    ['1 and 0', false],
                    ['True and False', true],
                    ['On and Off', false],
                ],
            ],
            [
                'q' => 'Which function tells you the data type of a value in Python?',
                'opts' => [
                    ['datatype()', false],
                    ['typeof()', false],
                    ['type()', true],
                    ['kind()', false],
                ],
            ],
            [
                'q' => 'What is the result of `type(True)` in Python?',
                'opts' => [
                    ["<class 'int'>", false],
                    ["<class 'bool'>", true],
                    ["<class 'str'>", false],
                    ["<class 'NoneType'>", false],
                ],
            ],
            [
                'q' => 'How do you convert the string `"25"` into the integer `25`?',
                'opts' => [
                    ['str("25")', false],
                    ['float("25")', false],
                    ['int("25")', true],
                    ['num("25")', false],
                ],
            ],
            [
                'q' => 'What does `str(100)` return?',
                'opts' => [
                    ['100 (as an integer)', false],
                    ['"100" (as a string)', true],
                    ['100.0 (as a float)', false],
                    ['An error', false],
                ],
            ],

            // ── ARITHMETIC OPERATORS ──────────────────────────────────────
            [
                'q' => 'What is the result of `10 % 3` in Python? (% gives the remainder)',
                'opts' => [
                    ['3', false],
                    ['1', true],
                    ['0', false],
                    ['3.33', false],
                ],
            ],
            [
                'q' => 'What does `**` mean in Python? For example: `2 ** 3`',
                'opts' => [
                    ['Multiply twice', false],
                    ['Raise to the power (exponent)', true],
                    ['Integer division', false],
                    ['Greater than or equal to', false],
                ],
            ],
            [
                'q' => 'What is the result of `10 // 3` in Python? (`//` is floor division)',
                'opts' => [
                    ['3.33', false],
                    ['1', false],
                    ['3', true],
                    ['4', false],
                ],
            ],
            [
                'q' => 'What will `print(10 - 3 * 2)` output? (Python follows normal math order of operations)',
                'opts' => [
                    ['14', false],
                    ['4', true],
                    ['1', false],
                    ['0', false],
                ],
            ],

            // ── STRINGS ───────────────────────────────────────────────────
            [
                'q' => 'What does `len("Python")` return?',
                'opts' => [
                    ['5', false],
                    ['7', false],
                    ['6', true],
                    ['"Python"', false],
                ],
            ],
            [
                'q' => 'How do you access the first character of a string `s = "Hello"`?',
                'opts' => [
                    ['s[1]', false],
                    ['s.first()', false],
                    ['s[0]', true],
                    ['s(-1)', false],
                ],
            ],
            [
                'q' => 'What will `"hello".upper()` return?',
                'opts' => [
                    ['hello', false],
                    ['Hello', false],
                    ['HELLO', true],
                    ['HeLLo', false],
                ],
            ],
            [
                'q' => 'What symbol is used to join (concatenate) two strings together?',
                'opts' => [
                    ['&', false],
                    ['.', false],
                    ['+', true],
                    ['*', false],
                ],
            ],
            [
                'q' => 'What will `"Data" + " " + "Science"` produce?',
                'opts' => [
                    ['"DataScience"', false],
                    ['"Data Science"', true],
                    ['An error', false],
                    ['"Data" " " "Science"', false],
                ],
            ],
            [
                'q' => 'Which string method removes extra whitespace from both ends?',
                'opts' => [
                    ['.clean()', false],
                    ['.strip()', true],
                    ['.trim()', false],
                    ['.remove()', false],
                ],
            ],

            // ── LISTS ─────────────────────────────────────────────────────
            [
                'q' => 'Which of the following correctly creates a list of three numbers in Python?',
                'opts' => [
                    ['(1, 2, 3)', false],
                    ['{1, 2, 3}', false],
                    ['[1, 2, 3]', true],
                    ['<1, 2, 3>', false],
                ],
            ],
            [
                'q' => 'Given `fruits = ["apple", "banana", "cherry"]`, what is `fruits[1]`?',
                'opts' => [
                    ['"apple"', false],
                    ['"banana"', true],
                    ['"cherry"', false],
                    ['An error', false],
                ],
            ],
            [
                'q' => 'How do you add the item "mango" to the end of a list called `fruits`?',
                'opts' => [
                    ['fruits.add("mango")', false],
                    ['fruits.push("mango")', false],
                    ['fruits.insert("mango")', false],
                    ['fruits.append("mango")', true],
                ],
            ],
            [
                'q' => 'What does `len([10, 20, 30, 40])` return?',
                'opts' => [
                    ['3', false],
                    ['10', false],
                    ['4', true],
                    ['40', false],
                ],
            ],
            [
                'q' => 'How do you remove and return the last item from a list?',
                'opts' => [
                    ['list.remove_last()', false],
                    ['list.delete()', false],
                    ['list.pop()', true],
                    ['list.drop()', false],
                ],
            ],
            [
                'q' => 'What is the index of the last element in a list of 5 items?',
                'opts' => [
                    ['5', false],
                    ['4', true],
                    ['0', false],
                    ['1', false],
                ],
            ],

            // ── IF STATEMENTS ──────────────────────────────────────────────
            [
                'q' => 'Which keyword starts a conditional (if) statement in Python?',
                'opts' => [
                    ['when', false],
                    ['check', false],
                    ['if', true],
                    ['case', false],
                ],
            ],
            [
                'q' => 'What does the `else` block in an if-else statement do?',
                'opts' => [
                    ['Runs when the if condition is True', false],
                    ['Runs when the if condition is False', true],
                    ['Always runs regardless', false],
                    ['Stops the program', false],
                ],
            ],
            [
                'q' => 'Which comparison operator checks if two values are EQUAL in Python?',
                'opts' => [
                    ['=', false],
                    ['!=', false],
                    ['==', true],
                    [':=', false],
                ],
            ],
            [
                'q' => "What will this print?\n\nx = 10\nif x > 5:\n    print(\"Big\")\nelse:\n    print(\"Small\")",
                'opts' => [
                    ['Small', false],
                    ['Big', true],
                    ['10', false],
                    ['Nothing', false],
                ],
            ],
            [
                'q' => 'What keyword checks a second condition when the first `if` is False?',
                'opts' => [
                    ['else if', false],
                    ['orif', false],
                    ['elif', true],
                    ['otherwise', false],
                ],
            ],
            [
                'q' => 'Which operator means "not equal to" in Python?',
                'opts' => [
                    ['<>', false],
                    ['!=', true],
                    ['=/=', false],
                    ['NOT=', false],
                ],
            ],

            // ── LOOPS ─────────────────────────────────────────────────────
            [
                'q' => 'Which loop is best when you know exactly how many times to repeat something?',
                'opts' => [
                    ['while loop', false],
                    ['for loop', true],
                    ['do-while loop', false],
                    ['repeat loop', false],
                ],
            ],
            [
                'q' => "How many times will this loop run?\n\nfor i in range(5):\n    print(i)",
                'opts' => [
                    ['4', false],
                    ['6', false],
                    ['5', true],
                    ['1', false],
                ],
            ],
            [
                'q' => 'What does `range(1, 6)` generate?',
                'opts' => [
                    ['Numbers 1 through 6 including 6', false],
                    ['Numbers 1 through 5 (not including 6)', true],
                    ['Numbers 0 through 5', false],
                    ['Just the number 6', false],
                ],
            ],
            [
                'q' => 'Which keyword immediately exits a loop in Python?',
                'opts' => [
                    ['exit', false],
                    ['stop', false],
                    ['return', false],
                    ['break', true],
                ],
            ],
            [
                'q' => 'What does the `continue` keyword do inside a loop?',
                'opts' => [
                    ['Stops the loop entirely', false],
                    ['Skips to the next loop iteration', true],
                    ['Restarts the loop from the beginning', false],
                    ['Pauses the loop', false],
                ],
            ],
            [
                'q' => "What will this print?\n\nfor letter in \"abc\":\n    print(letter)",
                'opts' => [
                    ['abc all on one line', false],
                    ['a, b, c each on a new line', true],
                    ['"abc"', false],
                    ['An error', false],
                ],
            ],

            // ── FUNCTIONS ─────────────────────────────────────────────────
            [
                'q' => 'Which keyword is used to define a function in Python?',
                'opts' => [
                    ['function', false],
                    ['define', false],
                    ['def', true],
                    ['func', false],
                ],
            ],
            [
                'q' => 'What does the `return` statement inside a function do?',
                'opts' => [
                    ['Prints the result to the screen', false],
                    ['Sends a value back to the caller', true],
                    ['Ends the entire program', false],
                    ['Creates a new variable', false],
                ],
            ],
            [
                'q' => 'How do you call (run) a function named `greet` with no arguments?',
                'opts' => [
                    ['call greet', false],
                    ['run greet()', false],
                    ['greet()', true],
                    ['greet', false],
                ],
            ],

            // ── GENERAL / BEGINNER CONCEPTS ───────────────────────────────
            [
                'q' => 'What happens when Python encounters an IndentationError?',
                'opts' => [
                    ['Python ignores spacing and runs fine', false],
                    ['Python stops and shows an error', true],
                    ['Python skips only the broken line', false],
                    ['Python adds spacing automatically', false],
                ],
            ],
            [
                'q' => 'In Python, how many spaces are the standard for one level of indentation?',
                'opts' => [
                    ['2', false],
                    ['4', true],
                    ['8', false],
                    ['1', false],
                ],
            ],
            [
                'q' => 'What is the value of `x` after running `x = 5` then `x = x + 3`?',
                'opts' => [
                    ['5', false],
                    ['3', false],
                    ['8', true],
                    ['53', false],
                ],
            ],
            [
                'q' => 'Which of the following is NOT a Python keyword?',
                'opts' => [
                    ['if', false],
                    ['for', false],
                    ['int', true],
                    ['while', false],
                ],
            ],
            [
                'q' => 'What will `print(type("hello"))` output?',
                'opts' => [
                    ["<class 'int'>", false],
                    ["string", false],
                    ["<class 'str'>", true],
                    ["hello", false],
                ],
            ],

        ];

        foreach ($qaData as $data) {
            $question = ChallengeQuestion::create([
                'challenge_id'  => $challenge->id,
                'question_text' => $data['q'],
                'challenge_category_id' => $category->id, 
            ]);

            foreach ($data['opts'] as $opt) {
                ChallengeOption::create([
                    'challenge_question_id' => $question->id,
                    'option_text'           => $opt[0],
                    'is_correct'            => $opt[1],
                ]);
            }
        }

        // $this->command->info("✅ Done! 50 questions seeded for Module 1 — Basics of Python Programming.");
        // $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Newbie");
        // $this->command->newLine();
        // $this->command->comment("NOTE: If Module 1 doesn't appear first on the map, add an `order_index`");
        // $this->command->comment("column to the `challenges` table and update the map controller query:");
        // $this->command->comment("  ->orderBy('order_index', 'asc')");
        // $this->command->comment("Then set order_index = 1 for this challenge.");
    }
}

/*
 * ─── OPTIONAL MIGRATION (if ordering is still wrong after a fresh seed) ──────
 *
 * If you already have challenges with lower IDs (from the old ChallengeSeeder),
 * add an order_index column so you can control the display order independently:
 *
 *   Schema::table('challenges', function (Blueprint $table) {
 *       $table->integer('order_index')->default(0)->after('base_xp');
 *   });
 *
 * Then in ChallengesController::map(), change:
 *   ->orderBy('id', 'asc')
 * to:
 *   ->orderBy('order_index', 'asc')->orderBy('id', 'asc')
 *
 * And set order_index = 1 on this challenge after seeding:
 *   Challenge::where('title', 'Basics of Python Programming')->update(['order_index' => 1]);
 */