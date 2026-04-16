<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module1ChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        // Schema::disableForeignKeyConstraints();
        // DB::table('challenge_options')->truncate();
        // DB::table('challenge_questions')->truncate();
        // Schema::enableForeignKeyConstraints();

        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (!$category) {
            $this->command->error("Intermediate category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 1 — Basics of Python Programming (Intermediate)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Basics of Python Programming',
            'description'           => 'The same foundational Python topics but tested with trickier snippets, common pitfalls, and questions that require you to trace code carefully. Watch out for gotchas.',
            'time_limit_seconds'    => 1500,
            'base_xp'               => 1200,
            'order_index'           => 1,
        ]);

        $this->command->info("Seeding 50 intermediate-level questions across 10 topics...");

        $qaData = [

            // ══════════════════════════════════════════════════════════════
            // 1.1 FOUNDATIONS: SYNTAX, OUTPUT, COMMENTS
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\nprint(\"a\", \"b\", \"c\", sep=\"-\", end=\"!\")\nprint(\"done\")",
                'opts' => [
                    ["a-b-c!\ndone", true],
                    ["a-b-c!\ndone\n", false],
                    ["a b c!\ndone", false],
                    ["a-b-c!done", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nx = 5\ny = 10\nprint(x, y, x + y)",
                'opts' => [
                    ["5 10 15", true],
                    ["5, 10, 15", false],
                    ["x y 15", false],
                    ["5 10 x+y", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nprint(\"Line1\\nLine2\")",
                'opts' => [
                    ["Line1\\nLine2", false],
                    ["Line1\nLine2", true],
                    ["Line1 Line2", false],
                    ["'Line1\\nLine2'", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nprint(\"Result:\", 2 ** 8)",
                'opts' => [
                    ["Result: 16", false],
                    ["Result: 28", false],
                    ["Result: 256", true],
                    ["Result: 28", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nfor i in range(1, 6, 2):\n    print(i, end=\",\")",
                'opts' => [
                    ["1,3,5,", true],
                    ["1,2,3,4,5,", false],
                    ["2,4,6,", false],
                    ["1,3,5,7,", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.2 MEMORY: VARIABLES, TYPES, CASTING
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\nx = 10\ny = x\nx += 5\nprint(x, y)",
                'opts' => [
                    ["15 15", false],
                    ["15 10", true],
                    ["10 10", false],
                    ["10 15", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nprint(int(True), int(False), float(True))",
                'opts' => [
                    ["True False True", false],
                    ["1 0 1.0", true],
                    ["1 0 1", false],
                    ["1 1 1.0", false],
                ],
            ],
            [
                'q' => "What is the output?\n\na = \"3\"\nb = 4\nprint(int(a) * b, a * b)",
                'opts' => [
                    ["12 12", false],
                    ["12 3333", true],
                    ["34 3333", false],
                    ["12 34", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nx = 0\ny = 0.0\nprint(x == y, x is y, type(x) == type(y))",
                'opts' => [
                    ["True True True", false],
                    ["True False False", true],
                    ["False False False", false],
                    ["True True False", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nval = \"007\"\nprint(int(val), str(int(val)))",
                'opts' => [
                    ["007 007", false],
                    ["7 7", false],
                    ["7 '7'", false],
                    ["7 7", true],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.3 TEXT PROCESSING: STRINGS & FORMATTING
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\ns = \"banana\"\nprint(s.count(\"a\"), s.index(\"n\"))",
                'opts' => [
                    ["3 2", true],
                    ["3 3", false],
                    ["2 2", false],
                    ["3 1", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nwords = \"  hello world  \"\nprint(words.strip().split())",
                'opts' => [
                    ["['hello', 'world']", true],
                    ["['  hello', 'world  ']", false],
                    ["'hello world'", false],
                    ["['hello world']", false],
                ],
            ],
            [
                'q' => "What is the output?\n\npi = 3.14159\nprint(f\"{pi:.2f}\")",
                'opts' => [
                    ["3.14159", false],
                    ["3.14", true],
                    ["3.1", false],
                    ["3.142", false],
                ],
            ],
            [
                'q' => "What does this print?\n\ns = \"abcdef\"\nprint(s[::2], s[1::2])",
                'opts' => [
                    ["ace bdf", true],
                    ["abc def", false],
                    ["acf bde", false],
                    ["ace bde", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nprint(\"-\".join([\"2024\", \"01\", \"15\"]))",
                'opts' => [
                    ["2024 01 15", false],
                    ["['2024', '01', '15']", false],
                    ["2024-01-15", true],
                    ["2024, 01, 15", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.4 LOGIC: BOOLEANS & OPERATORS
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\nprint(\"yes\" if 0 else \"no\")",
                'opts' => [
                    ["yes", false],
                    ["no", true],
                    ["0", false],
                    ["True", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nx = []\nprint(\"empty\" if not x else \"has items\")",
                'opts' => [
                    ["has items", false],
                    ["False", false],
                    ["empty", true],
                    ["[]", false],
                ],
            ],
            [
                'q' => "What is the output?\n\na = 5\nb = 10\nprint(a < b < 15, a < b > 3)",
                'opts' => [
                    ["True True", true],
                    ["True False", false],
                    ["False True", false],
                    ["False False", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nprint(3 & 5, 3 | 5, 3 ^ 5)",
                'opts' => [
                    ["1 7 6", true],
                    ["8 8 6", false],
                    ["1 8 7", false],
                    ["15 15 6", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nx = 4\nresult = \"even\" if x % 2 == 0 else \"odd\"\nprint(result)",
                'opts' => [
                    ["odd", false],
                    ["0", false],
                    ["even", true],
                    ["True", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.5 COLLECTIONS I: LISTS & ARRAYS
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\na = [1, 2, 3, 4, 5]\nprint(a[1:4:2])",
                'opts' => [
                    ["[2, 4]", true],
                    ["[1, 3, 5]", false],
                    ["[2, 3, 4]", false],
                    ["[1, 4]", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nnums = [3, 1, 4, 1, 5, 9]\nprint(nums.index(1), nums.count(1))",
                'opts' => [
                    ["1 2", true],
                    ["1 1", false],
                    ["3 2", false],
                    ["0 2", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nresult = [x for x in range(10) if x % 2 == 0]\nprint(result)",
                'opts' => [
                    ["[1, 3, 5, 7, 9]", false],
                    ["[0, 2, 4, 6, 8, 10]", false],
                    ["[0, 2, 4, 6, 8]", true],
                    ["[2, 4, 6, 8]", false],
                ],
            ],
            [
                'q' => "What does this print?\n\na = [1, [2, 3], 4]\na[1].append(99)\nprint(a)",
                'opts' => [
                    ["[1, [2, 3], 4, 99]", false],
                    ["[1, [2, 3, 99], 4]", true],
                    ["[1, [2, 3], 4]", false],
                    ["[1, 99, 4]", false],
                ],
            ],
            [
                'q' => "What is the output?\n\ndata = [10, 20, 30]\ndata[0], data[-1] = data[-1], data[0]\nprint(data)",
                'opts' => [
                    ["[10, 20, 30]", false],
                    ["[30, 20, 10]", false],
                    ["[20, 10, 30]", false],
                    ["[30, 20, 10]", true],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.6 COLLECTIONS II: TUPLES & SETS
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\nt = (1, 2, 3)\nprint(t + (4, 5))",
                'opts' => [
                    ["(1, 2, 3, 4, 5)", true],
                    ["[1, 2, 3, 4, 5]", false],
                    ["(1, 2, 3)(4, 5)", false],
                    ["TypeError", false],
                ],
            ],
            [
                'q' => "What does this print?\n\na = (1, 2, 3)\nb = list(a)\nb.append(4)\nprint(a, b)",
                'opts' => [
                    ["(1, 2, 3, 4) [1, 2, 3, 4]", false],
                    ["(1, 2, 3) [1, 2, 3, 4]", true],
                    ["(1, 2, 3) [1, 2, 3]", false],
                    ["TypeError", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nA = {1, 2, 3}\nB = {2, 3, 4}\nprint(A - B, B - A)",
                'opts' => [
                    ["{1} {4}", true],
                    ["{1, 2} {3, 4}", false],
                    ["{2, 3} {2, 3}", false],
                    ["{1} {2}", false],
                ],
            ],
            [
                'q' => "What does this print?\n\ncoords = (3, 4)\nx, y = coords\nprint(x ** 2 + y ** 2)",
                'opts' => [
                    ["7", false],
                    ["12", false],
                    ["25", true],
                    ["49", false],
                ],
            ],
            [
                'q' => "What is the output?\n\ns = frozenset([1, 2, 3])\nprint(2 in s, type(s).__name__)",
                'opts' => [
                    ["True set", false],
                    ["True frozenset", true],
                    ["False frozenset", false],
                    ["True list", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.7 COLLECTIONS III: DICTIONARIES
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\nd = {i: i**2 for i in range(1, 5)}\nprint(d)",
                'opts' => [
                    ["{1: 1, 2: 4, 3: 9, 4: 16}", true],
                    ["{1: 2, 2: 4, 3: 6, 4: 8}", false],
                    ["{1, 4, 9, 16}", false],
                    ["[1, 4, 9, 16]", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nd = {\"a\": [1, 2], \"b\": [3, 4]}\nd[\"a\"].append(99)\nprint(d)",
                'opts' => [
                    ["{'a': [1, 2], 'b': [3, 4]}", false],
                    ["{'a': [1, 2, 99], 'b': [3, 4]}", true],
                    ["{'a': 99, 'b': [3, 4]}", false],
                    ["{'a': [1, 2, 99], 'b': [3, 4, 99]}", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nd = {\"name\": \"Leo\", \"score\": 88}\nprint([(k, v) for k, v in d.items()])",
                'opts' => [
                    ["[('name', 'Leo'), ('score', 88)]", true],
                    ["['name', 'score']", false],
                    ["['Leo', 88]", false],
                    ["{'name': 'Leo', 'score': 88}", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nd = {\"a\": 1, \"b\": 2, \"c\": 3}\nprint(max(d, key=lambda k: d[k]))",
                'opts' => [
                    ["c", true],
                    ["3", false],
                    ["a", false],
                    ["{'c': 3}", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nd = {}\nfor ch in \"hello\":\n    d[ch] = d.get(ch, 0) + 1\nprint(d)",
                'opts' => [
                    ["{'h': 1, 'e': 1, 'l': 1, 'o': 1}", false],
                    ["{'h': 1, 'e': 1, 'l': 2, 'o': 1}", true],
                    ["{'h': 1, 'e': 1, 'l': 3, 'o': 1}", false],
                    ["{'hello': 1}", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.8 ADVANCED FLOW: MATCH & TRY/EXCEPT
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\ndef safe_div(a, b):\n    try:\n        return a / b\n    except ZeroDivisionError:\n        return None\n\nprint(safe_div(10, 2), safe_div(10, 0))",
                'opts' => [
                    ["5.0 0", false],
                    ["5.0 None", true],
                    ["5 None", false],
                    ["None None", false],
                ],
            ],
            [
                'q' => "What does this print?\n\ntry:\n    raise ValueError(\"oops\")\nexcept ValueError as e:\n    msg = str(e)\nprint(msg)",
                'opts' => [
                    ["ValueError", false],
                    ["oops", true],
                    ["e", false],
                    ["None", false],
                ],
            ],
            [
                'q' => "What is the output (Python 3.10+)?\n\nval = [1, 2]\nmatch val:\n    case [1, x]:\n        print(f\"matched x={x}\")\n    case _:\n        print(\"no match\")",
                'opts' => [
                    ["no match", false],
                    ["matched x=2", true],
                    ["matched x=[1, 2]", false],
                    ["matched x=1", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nfor i in range(3):\n    try:\n        if i == 1: raise RuntimeError\n    except RuntimeError:\n        print(\"err\", end=\" \")\n    else:\n        print(\"ok\", end=\" \")",
                'opts' => [
                    ["ok err ok", true],
                    ["err ok ok", false],
                    ["ok ok ok", false],
                    ["ok err err", false],
                ],
            ],
            [
                'q' => "What is the output?\n\ntry:\n    x = int(\"5\")\nexcept ValueError:\n    print(\"bad\")\nelse:\n    print(\"good:\", x)\nfinally:\n    print(\"done\")",
                'opts' => [
                    ["bad\ndone", false],
                    ["good: 5\ndone", true],
                    ["good: 5", false],
                    ["done\ngood: 5", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.9 LOOPS: FOR, WHILE, RANGE & ITERATORS
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\nresult = []\nfor i in range(3):\n    for j in range(3):\n        if i == j:\n            result.append((i, j))\nprint(result)",
                'opts' => [
                    ["[(0, 0), (1, 1), (2, 2)]", true],
                    ["[(0, 1), (1, 2)]", false],
                    ["[(0, 0), (1, 1)]", false],
                    ["[(1, 1), (2, 2)]", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nfor i in range(5):\n    if i == 4:\n        break\nelse:\n    print(\"no break\")\nprint(\"end\")",
                'opts' => [
                    ["no break\nend", false],
                    ["end", true],
                    ["no break", false],
                    ["Nothing", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nprint(list(zip([1,2,3], [\"a\",\"b\",\"c\"])))",
                'opts' => [
                    ["[(1, 'a'), (2, 'b'), (3, 'c')]", true],
                    ["[[1, 'a'], [2, 'b'], [3, 'c']]", false],
                    ["[1, 2, 3, 'a', 'b', 'c']", false],
                    ["{1: 'a', 2: 'b', 3: 'c'}", false],
                ],
            ],
            [
                'q' => "What does this print?\n\ni = 1\nwhile i < 100:\n    i *= 2\nprint(i)",
                'opts' => [
                    ["64", false],
                    ["100", false],
                    ["128", true],
                    ["256", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nprint(list(map(lambda x: x**2, filter(lambda x: x%2==0, range(6)))))",
                'opts' => [
                    ["[0, 4, 16]", true],
                    ["[0, 1, 4, 9, 16, 25]", false],
                    ["[4, 16]", false],
                    ["[0, 2, 4]", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.10 FUNCTIONS: DEF, ARGS, KWARGS & LAMBDAS
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\ndef f(x, y=10, *args):\n    return x + y + sum(args)\n\nprint(f(1, 2, 3, 4))",
                'opts' => [
                    ["10", false],
                    ["3", false],
                    ["10", false],
                    ["10", true],
                ],
            ],
            [
                'q' => "What does this print?\n\ndef counter(start=0):\n    count = [start]\n    def inc():\n        count[0] += 1\n        return count[0]\n    return inc\n\nc = counter(10)\nprint(c(), c(), c())",
                'opts' => [
                    ["10 11 12", false],
                    ["11 12 13", true],
                    ["1 2 3", false],
                    ["10 10 10", false],
                ],
            ],
            [
                'q' => "What is the output?\n\ndouble = lambda x: x * 2\ntriple = lambda x: x * 3\nprint(list(map(double, filter(lambda x: x > 2, [1,2,3,4]))))",
                'opts' => [
                    ["[6, 8]", true],
                    ["[2, 4, 6, 8]", false],
                    ["[3, 4]", false],
                    ["[6, 8, 10]", false],
                ],
            ],
            [
                'q' => "What does this print?\n\ndef f(a, /, b, *, c):\n    return a + b + c\n\nprint(f(1, 2, c=3))",
                'opts' => [
                    ["6", true],
                    ["TypeError", false],
                    ["1 2 3", false],
                    ["None", false],
                ],
            ],
            [
                'q' => "What is the output?\n\ndef apply(func, lst):\n    return [func(x) for x in lst]\n\nresult = apply(lambda x: x ** 2 - x, [1, 2, 3, 4])\nprint(result)",
                'opts' => [
                    ["[0, 2, 6, 12]", true],
                    ["[0, 4, 9, 16]", false],
                    ["[1, 4, 9, 16]", false],
                    ["[0, 2, 6, 8]", false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 1 — Basics of Python Programming (Intermediate).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Intermediate");
    }
}