<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module1ChallengeSeederAdvanced extends Seeder
{
    public function run(): void
    {
        // Schema::disableForeignKeyConstraints();
        // DB::table('challenge_options')->truncate();
        // DB::table('challenge_questions')->truncate();
        // Schema::enableForeignKeyConstraints();

        $category = ChallengeCategory::where('slug', 'advanced')->first();

        if (!$category) {
            $this->command->error("Advanced category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        // $challenge = Challenge::where('challenge_category_id', $category->id)
        //     ->where('title', 'Basics of Python Programming')
        //     ->first();

        $this->command->info("Creating Module 1 — Basics of Python Programming (Advanced)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Basics of Python Programming',
            'description'           => 'Python fundamentals pushed to their limits — deep edge cases, subtle CPython behaviors, and multi-step code snippets on syntax, variables, strings, collections, logic, loops, and functions. You need to think carefully.',
            'time_limit_seconds'    => 1800,
            'base_xp'               => 1600,
            'order_index'           => 1,
        ]);

        $this->command->info("Seeding 50 advanced-level questions across 10 topics...");

        $qaData = [

            // ══════════════════════════════════════════════════════════════
            // 1.1 FOUNDATIONS: SYNTAX, OUTPUT, COMMENTS
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\nprint(*[1, 2, 3], sep=\"-\", end=\"\\n\")\nprint(*range(3))",
                'opts' => [
                    ["1-2-3\n0 1 2", true],
                    ["[1,2,3]\n0 1 2", false],
                    ["1-2-3\n012", false],
                    ["1 2 3\n0-1-2", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nprint(0.1 + 0.2 == 0.3, round(0.1 + 0.2, 1) == 0.3)",
                'opts' => [
                    ["True True", false],
                    ["False True", true],
                    ["False False", false],
                    ["True False", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nprint(10 / 3, 10 // 3, -10 // 3, -10 % 3)",
                'opts' => [
                    ["3.3333333333333335 3 -3 -1", false],
                    ["3.3333333333333335 3 -4 2", true],
                    ["3.3333333333333335 3 -3 1", false],
                    ["3.3333333333333335 4 -3 -1", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nx = (1 +\n     2 +\n     3)\ny = 1 \\\n    + 2 \\\n    + 3\nprint(x, y)",
                'opts' => [
                    ["SyntaxError SyntaxError", false],
                    ["6 6", true],
                    ["6 SyntaxError", false],
                    ["None 6", false],
                ],
            ],
            [
                'q' => "What does this print?\n\na = b = 0\na += 1\nb += 2\nprint(a, b, a is b)",
                'opts' => [
                    ["1 2 False", true],
                    ["1 2 True", false],
                    ["3 3 True", false],
                    ["1 2 True — small ints are cached", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.2 MEMORY: VARIABLES, TYPES, CASTING
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\nx = [1, 2, 3]\ny = x\nz = x[:]\nx[0] = 99\nprint(y[0], z[0])",
                'opts' => [
                    ["1 1", false],
                    ["99 1", true],
                    ["99 99", false],
                    ["1 99", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nprint(type(True).__mro__)",
                'opts' => [
                    ["(<class 'bool'>, <class 'int'>, <class 'object'>)", true],
                    ["(<class 'bool'>, <class 'object'>)", false],
                    ["(<class 'bool'>, <class 'int'>)", false],
                    ["(<class 'bool'>,)", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nx = None\ny = 0\nz = \"\"\nprint(x == y, x == z, y == z, x is y)",
                'opts' => [
                    ["True True True True", false],
                    ["False False True False", true],
                    ["False False False False", false],
                    ["True False False False", false],
                ],
            ],
            [
                'q' => "What does this print?\n\na = 1000\nb = 1000\nprint(a == b, a is b)",
                'opts' => [
                    ["True True", false],
                    ["True False", true],
                    ["False False", false],
                    ["True True — CPython always interns integers", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nimport copy\na = [[1, 2], [3, 4]]\nb = copy.copy(a)\nb[0][0] = 99\nprint(a[0][0], b[0][0])",
                'opts' => [
                    ["1 99", false],
                    ["99 99", true],
                    ["99 1", false],
                    ["1 1", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.3 TEXT PROCESSING: STRINGS & FORMATTING
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\ns = \"abcdef\"\nprint(s[2:-2], s[-4:-1], s[::3])",
                'opts' => [
                    ["cd cde ad", true],
                    ["cde bcde adg", false],
                    ["cd bcd ace", false],
                    ["cde cde ad", false],
                ],
            ],
            [
                'q' => "What does this print?\n\ndata = {\"price\": 49.99, \"qty\": 3}\nprint(\"Total: \${price:.2f} x {qty} = \${total:.2f}\".format(**data, total=data['price']*data['qty']))",
                'opts' => [
                    ["Total: $49.99 x 3 = $149.97", true],
                    ["Total: $49.99 x 3 = $150.00", false],
                    ["KeyError: total", false],
                    ["Total: $50.00 x 3 = $149.97", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nprint(\"\".join(reversed(\"stressed\")))",
                'opts' => [
                    ["stressed", false],
                    ["desserts", true],
                    ["detssers", false],
                    ["TypeError", false],
                ],
            ],
            [
                'q' => "What does this print?\n\ns = \"hello\"\nprint(s.center(11, \"*\"), s.ljust(9, \"-\"))",
                'opts' => [
                    ["***hello*** hello----", true],
                    ["**hello** hello----", false],
                    ["***hello*** hello-----", false],
                    ["**hello*** hello----", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nparts = \"one::two:::three\"\nprint(parts.split(\":\"))\nprint(parts.split(\":\", 2))",
                'opts' => [
                    ["['one', 'two', 'three']\n['one', 'two', 'three']", false],
                    ["['one', '', 'two', '', '', 'three']\n['one', '', 'two:::three']", true],
                    ["['one', 'two', 'three']\n['one', '', 'two:::three']", false],
                    ["['one', '', 'two', '', '', 'three']\n['one', 'two', 'three']", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.4 LOGIC: BOOLEANS & OPERATORS
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\nprint(True + True + False, True * 5, False ** 0)",
                'opts' => [
                    ["2 5 1", true],
                    ["True 5 False", false],
                    ["2 True 0", false],
                    ["2 5 0", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nprint(all([1, 2, 3]), all([1, 0, 3]), any([0, 0, 0]), any([0, 1, 0]))",
                'opts' => [
                    ["True False False True", true],
                    ["True True False True", false],
                    ["True False True True", false],
                    ["True False False False", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nx = 12\nprint(x & 10, x | 5, x ^ 10, ~x)",
                'opts' => [
                    ["8 13 6 -13", true],
                    ["8 12 6 -12", false],
                    ["2 14 6 -13", false],
                    ["8 13 2 -13", false],
                ],
            ],
            [
                'q' => "What does this print?\n\na = []\nb = {}\nc = 0.0\nd = \"0\"\nprint(not a, not b, not c, not d)",
                'opts' => [
                    ["True True True True", false],
                    ["True True True False", true],
                    ["False False False False", false],
                    ["True True False False", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nx = 5\nresult = \"high\" if x > 10 else \"mid\" if x > 3 else \"low\"\nprint(result)",
                'opts' => [
                    ["high", false],
                    ["mid", true],
                    ["low", false],
                    ["None", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.5 COLLECTIONS I: LISTS & ARRAYS
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\nnums = [1, 2, 3, 4, 5]\nnums[1:3] = [20, 30, 40]\nprint(len(nums), nums[2])",
                'opts' => [
                    ["5 30", false],
                    ["6 40", true],
                    ["6 30", false],
                    ["5 40", false],
                ],
            ],
            [
                'q' => "What does this print?\n\ndata = [3, 1, 4, 1, 5, 9]\nprint(sorted(data, key=lambda x: -x)[:3])",
                'opts' => [
                    ["[9, 5, 4]", true],
                    ["[1, 1, 3]", false],
                    ["[3, 1, 4]", false],
                    ["[9, 5, 3]", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nlst = [0, 1, 2, 3, 4]\ndel lst[::2]\nprint(lst)",
                'opts' => [
                    ["[1, 3]", true],
                    ["[0, 2, 4]", false],
                    ["[1, 2, 3]", false],
                    ["[0, 1, 2]", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nlst = [[1, 2], [3, 4], [5, 6]]\nflat = [x for row in lst for x in row if x % 2 == 0]\nprint(flat)",
                'opts' => [
                    ["[1, 3, 5]", false],
                    ["[2, 4, 6]", true],
                    ["[2, 4]", false],
                    ["[1, 2, 3, 4, 5, 6]", false],
                ],
            ],
            [
                'q' => "What is the output?\n\na = [1, 2, 3]\nb = [4, 5, 6]\na, b = b, a[:]\nprint(a, b)",
                'opts' => [
                    ["[4, 5, 6] [1, 2, 3]", true],
                    ["[1, 2, 3] [4, 5, 6]", false],
                    ["[4, 5, 6] [4, 5, 6]", false],
                    ["[1, 2, 3] [1, 2, 3]", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.6 COLLECTIONS II: TUPLES & SETS
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\nt = (1, 2, 3)\ntry:\n    t[0] = 10\nexcept TypeError as e:\n    print(\"Error:\", type(e).__name__)\nprint(t)",
                'opts' => [
                    ["Error: TypeError\n(1, 2, 3)", true],
                    ["Error: TypeError\n(10, 2, 3)", false],
                    ["(10, 2, 3)", false],
                    ["Error: AttributeError\n(1, 2, 3)", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nA = frozenset([1, 2, 3])\nB = frozenset([2, 3, 4])\nprint(A & B, type(A & B).__name__)",
                'opts' => [
                    ["frozenset({2, 3}) frozenset", true],
                    ["{2, 3} set", false],
                    ["frozenset({2, 3}) set", false],
                    ["TypeError", false],
                ],
            ],
            [
                'q' => "What is the output?\n\ns1 = {1, 2, 3}\ns2 = {1, 2, 3, 4}\nprint(s1 < s2, s1 <= s1, s1 == s2)",
                'opts' => [
                    ["True True False", true],
                    ["True False False", false],
                    ["False True False", false],
                    ["True True True", false],
                ],
            ],
            [
                'q' => "What does this print?\n\ndata = [(\"b\", 2), (\"a\", 3), (\"c\", 1)]\nprint(sorted(data, key=lambda x: x[1]))",
                'opts' => [
                    ["[('a', 3), ('b', 2), ('c', 1)]", false],
                    ["[('c', 1), ('b', 2), ('a', 3)]", true],
                    ["[('b', 2), ('a', 3), ('c', 1)]", false],
                    ["[('a', 1), ('b', 2), ('c', 3)]", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nt = tuple(range(5))\nprint(t[1::2], t[-2:])",
                'opts' => [
                    ["(1, 3) (3, 4)", true],
                    ["(1, 2, 3) (4,)", false],
                    ["(2, 4) (3, 4)", false],
                    ["(1, 3) (4,)", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.7 COLLECTIONS III: DICTIONARIES
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\nd = {True: \"yes\", 1: \"no\", 1.0: \"maybe\"}\nprint(len(d), d[True])",
                'opts' => [
                    ["3 yes", false],
                    ["1 maybe", true],
                    ["2 no", false],
                    ["3 maybe", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nd = {\"a\": {\"x\": 1}, \"b\": {\"x\": 2}}\nresult = {k: v[\"x\"] * 2 for k, v in d.items() if v[\"x\"] > 1}\nprint(result)",
                'opts' => [
                    ["{'a': 2, 'b': 4}", false],
                    ["{'b': 4}", true],
                    ["{'a': 1, 'b': 2}", false],
                    ["{'b': 2}", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nd = {\"a\": 1, \"b\": 2}\npopped = d.pop(\"a\")\nd.setdefault(\"c\", 3)\nprint(popped, d)",
                'opts' => [
                    ["1 {'b': 2, 'c': 3}", true],
                    ["{'a': 1} {'b': 2, 'c': 3}", false],
                    ["1 {'a': 1, 'b': 2, 'c': 3}", false],
                    ["None {'b': 2}", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nd = {\"a\": 1, \"b\": 2, \"c\": 3, \"d\": 4}\nresult = dict(sorted(d.items(), key=lambda x: x[1], reverse=True)[:2])\nprint(result)",
                'opts' => [
                    ["{'a': 1, 'b': 2}", false],
                    ["{'d': 4, 'c': 3}", true],
                    ["{'c': 3, 'd': 4}", false],
                    ["{'b': 2, 'c': 3}", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nfrom collections import defaultdict\nd = defaultdict(list)\nfor k, v in [(\"a\", 1), (\"b\", 2), (\"a\", 3)]:\n    d[k].append(v)\nprint(dict(d))",
                'opts' => [
                    ["{'a': 1, 'b': 2}", false],
                    ["{'a': [1, 3], 'b': [2]}", true],
                    ["{'a': [1], 'b': [2], 'a': [3]}", false],
                    ["{'a': 3, 'b': 2}", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.8 ADVANCED FLOW: MATCH & TRY/EXCEPT
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\ndef f():\n    try:\n        raise ValueError(\"v\")\n    except ValueError:\n        raise RuntimeError(\"r\") from None\n\ntry:\n    f()\nexcept RuntimeError as e:\n    print(e, e.__cause__)",
                'opts' => [
                    ["r ValueError('v')", false],
                    ["r None", true],
                    ["v None", false],
                    ["ValueError RuntimeError", false],
                ],
            ],
            [
                'q' => "What does this print (Python 3.10+)?\n\npoint = {\"x\": 0, \"y\": 5}\nmatch point:\n    case {\"x\": 0, \"y\": y} if y > 0:\n        print(f\"Up: {y}\")\n    case {\"x\": 0, \"y\": y}:\n        print(f\"Down: {y}\")\n    case _:\n        print(\"Other\")",
                'opts' => [
                    ["Up: 5", true],
                    ["Down: 5", false],
                    ["Other", false],
                    ["SyntaxError", false],
                ],
            ],
            [
                'q' => "What is the output?\n\ndef risky(lst):\n    try:\n        return lst[0] / lst[1]\n    except (IndexError, ZeroDivisionError) as e:\n        return type(e).__name__\n\nprint(risky([10, 2]), risky([10, 0]), risky([10]))",
                'opts' => [
                    ["5.0 ZeroDivisionError IndexError", true],
                    ["5.0 0 None", false],
                    ["5 ZeroDivisionError IndexError", false],
                    ["5.0 None IndexError", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nclass CustomError(ValueError):\n    pass\n\ntry:\n    raise CustomError(\"custom\")\nexcept ValueError as e:\n    print(type(e).__name__, isinstance(e, ValueError))",
                'opts' => [
                    ["ValueError True", false],
                    ["CustomError True", true],
                    ["CustomError False", false],
                    ["ValueError False", false],
                ],
            ],
            [
                'q' => "What is the output?\n\ndef f():\n    try:\n        return \"try\"\n    except:\n        return \"except\"\n    finally:\n        print(\"finally\")\n\nprint(f())",
                'opts' => [
                    ["try\nfinally", false],
                    ["finally\ntry", true],
                    ["finally", false],
                    ["try", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.9 LOOPS: FOR, WHILE, RANGE & ITERATORS
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\ngen = (x ** 2 for x in range(5))\nnext(gen)\nnext(gen)\nprint(list(gen))",
                'opts' => [
                    ["[0, 1, 4, 9, 16]", false],
                    ["[4, 9, 16]", true],
                    ["[9, 16]", false],
                    ["[0, 1]", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nfrom functools import reduce\nprint(reduce(lambda a, b: a * b, range(1, 6)))",
                'opts' => [
                    ["15", false],
                    ["120", true],
                    ["24", false],
                    ["720", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nresult = {x: x**2 for x in range(5) if x % 2 != 0}\nprint(result)",
                'opts' => [
                    ["{1: 1, 3: 9}", true],
                    ["{0: 0, 2: 4, 4: 16}", false],
                    ["{1: 1, 2: 4, 3: 9}", false],
                    ["{1: 1, 3: 9, 5: 25}", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nfor i in range(5):\n    if i == 3:\n        continue\n    if i == 4:\n        break\nelse:\n    print(\"complete\")\nprint(\"end\")",
                'opts' => [
                    ["complete\nend", false],
                    ["end", true],
                    ["complete", false],
                    ["Nothing is printed", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nfrom itertools import accumulate\ndata = [1, 2, 3, 4, 5]\nprint(list(accumulate(data)))",
                'opts' => [
                    ["[1, 2, 3, 4, 5]", false],
                    ["[1, 3, 6, 10, 15]", true],
                    ["[15, 10, 6, 3, 1]", false],
                    ["15", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.10 FUNCTIONS: DEF, ARGS, KWARGS & LAMBDAS
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\ndef f(x, /, y, *, z):\n    return x + y + z\n\nprint(f(1, 2, z=3))",
                'opts' => [
                    ["TypeError: f() missing keyword-only argument", false],
                    ["6", true],
                    ["1 2 3", false],
                    ["SyntaxError", false],
                ],
            ],
            [
                'q' => "What does this print?\n\ndef make_counter():\n    count = 0\n    def counter():\n        nonlocal count\n        count += 1\n        return count\n    return counter\n\nc1 = make_counter()\nc2 = make_counter()\nprint(c1(), c1(), c2())",
                'opts' => [
                    ["1 2 1", true],
                    ["1 2 3", false],
                    ["1 1 1", false],
                    ["0 1 0", false],
                ],
            ],
            [
                'q' => "What is the output?\n\ndef decorator(func):\n    def wrapper(*args, **kwargs):\n        print(\"before\")\n        result = func(*args, **kwargs)\n        print(\"after\")\n        return result\n    return wrapper\n\n@decorator\ndef add(a, b):\n    return a + b\n\nprint(add(2, 3))",
                'opts' => [
                    ["before\nafter\n5", true],
                    ["5", false],
                    ["before\n5\nafter", false],
                    ["before\nafter", false],
                ],
            ],
            [
                'q' => "What does this print?\n\ndef f(*args, **kwargs):\n    return sum(args) + sum(kwargs.values())\n\ndata = {\"x\": 10, \"y\": 20}\nprint(f(1, 2, 3, **data))",
                'opts' => [
                    ["36", true],
                    ["6", false],
                    ["30", false],
                    ["TypeError", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nfns = []\nfor i in range(3):\n    fns.append(lambda x, i=i: x + i)\nprint([f(10) for f in fns])",
                'opts' => [
                    ["[12, 12, 12]", false],
                    ["[10, 11, 12]", true],
                    ["[10, 10, 10]", false],
                    ["[11, 12, 13]", false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 1 — Basics of Python Programming (Advanced).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Advanced");
    }
}