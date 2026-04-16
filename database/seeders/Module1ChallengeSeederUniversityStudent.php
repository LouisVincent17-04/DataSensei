<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module1ChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        // Schema::disableForeignKeyConstraints();
        // DB::table('challenge_options')->truncate();
        // DB::table('challenge_questions')->truncate();
        // Schema::enableForeignKeyConstraints();

        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (!$category) {
            $this->command->error("University Student category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 1 — Basics of Python Programming (University Student)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Basics of Python Programming',
            'description'           => 'Python fundamentals tested through short code snippets and concept questions. Covers syntax, variables, strings, collections, logic, loops, and functions — expect a few traps.',
            'time_limit_seconds'    => 1200,
            'base_xp'               => 800,
            'order_index'           => 1,
        ]);

        $this->command->info("Seeding 50 university-level questions across 10 topics...");

        $qaData = [

            // ══════════════════════════════════════════════════════════════
            // 1.1 FOUNDATIONS: SYNTAX, OUTPUT, COMMENTS
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\nprint(\"Hello\", \"World\", sep=\"-\")",
                'opts' => [
                    ["Hello World", false],
                    ["Hello-World", true],
                    ["Hello - World", false],
                    ["HelloWorld", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nfor i in range(3):\n    print(i, end=\" \")",
                'opts' => [
                    ["0 1 2 ", true],
                    ["1 2 3 ", false],
                    ["0\n1\n2", false],
                    ["0 1 2", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nprint(\"Score:\", 95, sep=\"\")",
                'opts' => [
                    ["Score: 95", false],
                    ["Score:95", true],
                    ["Score 95", false],
                    ["Score,95", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nx = 10\n# x = 20\nprint(x)",
                'opts' => [
                    ["20", false],
                    ["10", true],
                    ["None", false],
                    ["SyntaxError", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nprint(\"A\")\nprint(\"B\", end=\"\")\nprint(\"C\")",
                'opts' => [
                    ["A\nB\nC", false],
                    ["A\nBC", true],
                    ["ABC", false],
                    ["A\nB C", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.2 MEMORY: VARIABLES, TYPES, CASTING
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\nx = \"10\"\ny = int(x) + 5\nprint(y)",
                'opts' => [
                    ["105", false],
                    ["15", true],
                    ["10 5", false],
                    ["TypeError", false],
                ],
            ],
            [
                'q' => "What does this print?\n\na = 5\nb = a\na = 10\nprint(b)",
                'opts' => [
                    ["10", false],
                    ["5", true],
                    ["None", false],
                    ["15", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nprint(type(3.0).__name__)",
                'opts' => [
                    ["int", false],
                    ["double", false],
                    ["float", true],
                    ["number", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nx = int(9.9)\nprint(x)",
                'opts' => [
                    ["10", false],
                    ["9.9", false],
                    ["9", true],
                    ["Error", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nprint(bool(0), bool(1), bool(-1))",
                'opts' => [
                    ["False True True", true],
                    ["False True False", false],
                    ["True False True", false],
                    ["False False True", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.3 TEXT PROCESSING: STRINGS & FORMATTING
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\ns = \"Hello, World!\"\nprint(s[7:12])",
                'opts' => [
                    ["World", true],
                    ["World!", false],
                    ["ello,", false],
                    ["Hello", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nname = \"alice\"\nprint(name.capitalize())",
                'opts' => [
                    ["ALICE", false],
                    ["alice", false],
                    ["Alice", true],
                    ["aLICE", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nage = 21\nprint(f\"I am {age} years old\")",
                'opts' => [
                    ["I am age years old", false],
                    ["I am {age} years old", false],
                    ["I am 21 years old", true],
                    ["I am '21' years old", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nprint(\"python\".upper().replace(\"O\", \"0\"))",
                'opts' => [
                    ["PYTH0N", true],
                    ["python", false],
                    ["PYTHON", false],
                    ["pyth0n", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nprint(len(\"  hello  \".strip()))",
                'opts' => [
                    ["9", false],
                    ["7", false],
                    ["5", true],
                    ["3", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.4 LOGIC: BOOLEANS & OPERATORS
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\nx = 15\nprint(x > 10 and x < 20)",
                'opts' => [
                    ["1", false],
                    ["True", true],
                    ["False", false],
                    ["None", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nprint(5 != 5, 5 == 5.0)",
                'opts' => [
                    ["True True", false],
                    ["False True", true],
                    ["False False", false],
                    ["True False", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nx = 7\nprint(x % 2 == 0 or x > 5)",
                'opts' => [
                    ["False", false],
                    ["True", true],
                    ["1", false],
                    ["7", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nprint(not True or False)",
                'opts' => [
                    ["True", false],
                    ["False", true],
                    ["None", false],
                    ["Error", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nx = 10\nprint(x > 5 and x < 8)",
                'opts' => [
                    ["True", false],
                    ["False", true],
                    ["None", false],
                    ["Error", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.5 COLLECTIONS I: LISTS & ARRAYS
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\nnums = [10, 20, 30, 40]\nnums.append(50)\nprint(nums[2])",
                'opts' => [
                    ["20", false],
                    ["30", true],
                    ["40", false],
                    ["50", false],
                ],
            ],
            [
                'q' => "What does this print?\n\na = [1, 2, 3]\na.insert(1, 99)\nprint(a)",
                'opts' => [
                    ["[1, 2, 3, 99]", false],
                    ["[99, 1, 2, 3]", false],
                    ["[1, 99, 2, 3]", true],
                    ["[1, 2, 99, 3]", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nx = [5, 3, 1, 4, 2]\nx.sort()\nprint(x[0], x[-1])",
                'opts' => [
                    ["5 2", false],
                    ["1 5", true],
                    ["3 4", false],
                    ["1 4", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nletters = ['a', 'b', 'c', 'd']\nprint(letters[-2:])",
                'opts' => [
                    ["['a', 'b']", false],
                    ["['c', 'd']", true],
                    ["['b', 'c']", false],
                    ["['d']", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nnums = [1, 2, 3]\nprint(nums * 2)",
                'opts' => [
                    ["[2, 4, 6]", false],
                    ["[1, 2, 3, 1, 2, 3]", true],
                    ["[1, 1, 2, 2, 3, 3]", false],
                    ["6", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.6 COLLECTIONS II: TUPLES & SETS
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\nt = (10, 20, 30)\nprint(t[1])",
                'opts' => [
                    ["10", false],
                    ["20", true],
                    ["30", false],
                    ["(20,)", false],
                ],
            ],
            [
                'q' => "What does this print?\n\na, b, c = (1, 2, 3)\nprint(b)",
                'opts' => [
                    ["1", false],
                    ["3", false],
                    ["2", true],
                    ["(1, 2, 3)", false],
                ],
            ],
            [
                'q' => "What is the output?\n\ns = {1, 2, 3, 2, 1}\nprint(len(s))",
                'opts' => [
                    ["5", false],
                    ["3", true],
                    ["2", false],
                    ["4", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nA = {1, 2, 3}\nB = {2, 3, 4}\nprint(A | B)",
                'opts' => [
                    ["{2, 3}", false],
                    ["{1, 2, 3, 4}", true],
                    ["{1, 4}", false],
                    ["{1, 2, 3, 2, 3, 4}", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nt = (5,)\nprint(type(t).__name__, len(t))",
                'opts' => [
                    ["int 1", false],
                    ["tuple 1", true],
                    ["list 1", false],
                    ["tuple 5", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.7 COLLECTIONS III: DICTIONARIES
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\nd = {\"a\": 1, \"b\": 2}\nd[\"c\"] = 3\nprint(len(d))",
                'opts' => [
                    ["2", false],
                    ["3", true],
                    ["4", false],
                    ["Error", false],
                ],
            ],
            [
                'q' => "What does this print?\n\ninfo = {\"name\": \"Bob\", \"age\": 22}\nprint(info.get(\"grade\", \"N/A\"))",
                'opts' => [
                    ["None", false],
                    ["KeyError", false],
                    ["N/A", true],
                    ["grade", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nd = {\"x\": 10, \"y\": 20, \"z\": 30}\nprint(list(d.keys()))",
                'opts' => [
                    ["['x', 'y', 'z']", true],
                    ["['10', '20', '30']", false],
                    ["[10, 20, 30]", false],
                    ["dict_keys(['x', 'y', 'z'])", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nd = {\"a\": 1, \"b\": 2, \"c\": 3}\nprint(sum(d.values()))",
                'opts' => [
                    ["abc", false],
                    ["6", true],
                    ["3", false],
                    ["Error", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nd = {\"a\": 1}\nd.update({\"b\": 2, \"a\": 99})\nprint(d)",
                'opts' => [
                    ["{'a': 1, 'b': 2}", false],
                    ["{'a': 99, 'b': 2}", true],
                    ["{'a': 1, 'a': 99, 'b': 2}", false],
                    ["{'b': 2}", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.8 ADVANCED FLOW: MATCH & TRY/EXCEPT
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\ntry:\n    x = int(\"abc\")\nexcept ValueError:\n    print(\"Bad input\")",
                'opts' => [
                    ["0", false],
                    ["Bad input", true],
                    ["abc", false],
                    ["None", false],
                ],
            ],
            [
                'q' => "What does this print?\n\ntry:\n    print(10 / 2)\nexcept ZeroDivisionError:\n    print(\"Error\")",
                'opts' => [
                    ["Error", false],
                    ["5", false],
                    ["5.0", true],
                    ["None", false],
                ],
            ],
            [
                'q' => "What is the output (Python 3.10+)?\n\nstatus = 404\nmatch status:\n    case 200:\n        print(\"OK\")\n    case 404:\n        print(\"Not Found\")\n    case _:\n        print(\"Other\")",
                'opts' => [
                    ["OK", false],
                    ["Not Found", true],
                    ["Other", false],
                    ["404", false],
                ],
            ],
            [
                'q' => "What does this print?\n\ntry:\n    x = 1 / 0\nexcept:\n    print(\"caught\")\nfinally:\n    print(\"done\")",
                'opts' => [
                    ["caught", false],
                    ["done", false],
                    ["caught\ndone", true],
                    ["done\ncaught", false],
                ],
            ],
            [
                'q' => "What is the output?\n\ntry:\n    pass\nexcept Exception:\n    print(\"error\")\nelse:\n    print(\"no error\")",
                'opts' => [
                    ["error", false],
                    ["no error", true],
                    ["None", false],
                    ["pass", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.9 LOOPS: FOR, WHILE, RANGE & ITERATORS
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\nfor i in range(2, 10, 3):\n    print(i, end=\" \")",
                'opts' => [
                    ["2 5 8 ", true],
                    ["2 4 6 8 ", false],
                    ["3 6 9 ", false],
                    ["2 5 8 11 ", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nnums = [10, 20, 30]\nfor i, v in enumerate(nums):\n    print(i, v)",
                'opts' => [
                    ["0 10\n1 20\n2 30", true],
                    ["1 10\n2 20\n3 30", false],
                    ["10 0\n20 1\n30 2", false],
                    ["0 1 2\n10 20 30", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nx = 10\nwhile x > 0:\n    x -= 3\nprint(x)",
                'opts' => [
                    ["0", false],
                    ["-1", false],
                    ["-2", true],
                    ["1", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nfor i in range(5):\n    if i == 3:\n        continue\n    print(i, end=\" \")",
                'opts' => [
                    ["0 1 2 3 4 ", false],
                    ["0 1 2 4 ", true],
                    ["0 1 2 ", false],
                    ["3 ", false],
                ],
            ],
            [
                'q' => "What is the output?\n\ntotal = 0\nfor n in range(1, 6):\n    total += n\nprint(total)",
                'opts' => [
                    ["10", false],
                    ["14", false],
                    ["15", true],
                    ["20", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.10 FUNCTIONS: DEF, ARGS, KWARGS & LAMBDAS
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\ndef greet(name=\"World\"):\n    return f\"Hello, {name}!\"\n\nprint(greet())\nprint(greet(\"Alice\"))",
                'opts' => [
                    ["Hello, World!\nHello, Alice!", true],
                    ["Hello, name!\nHello, Alice!", false],
                    ["None\nHello, Alice!", false],
                    ["Hello!\nHello, Alice!", false],
                ],
            ],
            [
                'q' => "What does this print?\n\ndef add(*nums):\n    return sum(nums)\n\nprint(add(1, 2, 3, 4))",
                'opts' => [
                    ["[1, 2, 3, 4]", false],
                    ["10", true],
                    ["(1, 2, 3, 4)", false],
                    ["Error", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nsquare = lambda x: x ** 2\nprint(square(5))",
                'opts' => [
                    ["10", false],
                    ["52", false],
                    ["25", true],
                    ["x ** 2", false],
                ],
            ],
            [
                'q' => "What does this print?\n\ndef show(**info):\n    for k, v in info.items():\n        print(k, v)\n\nshow(name=\"Ana\", age=20)",
                'opts' => [
                    ["name Ana\nage 20", true],
                    ["Ana 20", false],
                    ["{'name': 'Ana', 'age': 20}", false],
                    ["Error", false],
                ],
            ],
            [
                'q' => "What is the output?\n\ndef calc(a, b, op=\"add\"):\n    if op == \"add\":\n        return a + b\n    return a - b\n\nprint(calc(10, 3))\nprint(calc(10, 3, \"sub\"))",
                'opts' => [
                    ["13\n7", true],
                    ["7\n13", false],
                    ["13\n13", false],
                    ["None\n7", false],
                ],
            ],
        ];

        foreach ($qaData as $data) {
            $question = ChallengeQuestion::create([
                'challenge_id'          => $challenge->id,
                'question_text'         => $data['q'],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 1 — Basics of Python Programming (University Student).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: University Student");
    }
}