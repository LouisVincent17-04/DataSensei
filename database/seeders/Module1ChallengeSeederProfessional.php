<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module1ChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {

        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (!$category) {
            $this->command->error("Professional category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 1 — Basics of Python Programming (Professional)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Basics of Python Programming',
            'description'           => 'Expert-level Python questions covering the same foundational topics — syntax, variables, strings, collections, logic, loops, and functions — but tested through tricky code snippets, edge cases, and deep CPython behavior. No hand-holding.',
            'time_limit_seconds'    => 1800,
            'base_xp'               => 2000,
            'order_index'           => 1,
        ]);

        $this->command->info("Seeding 50 professional-level questions across the 10 foundational topics...");

        $qaData = [

            // ══════════════════════════════════════════════════════════════
            // 1.1 FOUNDATIONS: SYNTAX, OUTPUT, COMMENTS  (5 questions)
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output of this code?\n\nprint(\"Line 1\", \"Line 2\", sep=\"\\n\", end=\"\")\nprint(\" Done\")",
                'opts' => [
                    ["Line 1\nLine 2\n Done", false],
                    ["Line 1\nLine 2 Done", true],
                    ["Line 1 Line 2 Done", false],
                    ["Line 1\nLine 2\nDone", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nx = 10\ny = 20\nprint(x, y, sep=\", \", end=\" | \")\nprint(y, x)",
                'opts' => [
                    ["10, 20 | 20 10", true],
                    ["10, 20 | \n20 10", false],
                    ["10 20 | 20 10", false],
                    ["10, 20\n20 10 |", false],
                ],
            ],
            [
                'q' => "Which of the following is a valid multi-line comment approach in Python (used in practice, not just theory)?",
                'opts' => [
                    ["/* This is a comment */", false],
                    ["A triple-quoted string (\"\"\"...\"\"\") assigned to nothing — Python parses it as an expression statement and discards it, effectively acting as a comment", true],
                    ["// This is a comment", false],
                    ["<!-- This is a comment -->", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nprint(type(print(\"Hello\")))",
                'opts' => [
                    ["Hello\n<class 'NoneType'>", true],
                    ["<class 'builtin_function_or_method'>", false],
                    ["Hello\n<class 'str'>", false],
                    ["Hello", false],
                ],
            ],
            [
                'q' => "What does this output?\n\na = 1; b = 2; c = 3\nprint(a, b, c)",
                'opts' => [
                    ["123", false],
                    ["1, 2, 3", false],
                    ["1 2 3", true],
                    ["SyntaxError: invalid syntax", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.2 MEMORY: VARIABLES, TYPES, CASTING  (5 questions)
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\na = 256\nb = 256\nprint(a is b)\n\na = 257\nb = 257\nprint(a is b)",
                'opts' => [
                    ["True\nTrue", false],
                    ["True\nFalse", true],
                    ["False\nFalse", false],
                    ["False\nTrue", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nx = \"5\"\ny = int(x) + float(x)\nprint(type(y), y)",
                'opts' => [
                    ["<class 'int'> 10", false],
                    ["<class 'float'> 10.0", true],
                    ["<class 'str'> 10.0", false],
                    ["TypeError", false],
                ],
            ],
            [
                'q' => "What is the output?\n\na = b = c = []\nc.append(99)\nprint(a, b, c)",
                'opts' => [
                    ["[] [] [99]", false],
                    ["[99] [] []", false],
                    ["[99] [99] [99]", true],
                    ["[] [] []", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nprint(bool(\"\"), bool(\" \"), bool(0), bool([]), bool([0]))",
                'opts' => [
                    ["False False False False False", false],
                    ["False True False False True", true],
                    ["False False False False True", false],
                    ["True True False False True", false],
                ],
            ],
            [
                'q' => "What is the result of this?\n\nx = None\nprint(type(x).__name__, x is None, x == False)",
                'opts' => [
                    ["NoneType True False", true],
                    ["NoneType True True", false],
                    ["NoneType False False", false],
                    ["bool True True", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.3 TEXT PROCESSING: STRINGS & FORMATTING  (5 questions)
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What does this print?\n\ns = \"hello world\"\nprint(s.replace(\"l\", \"L\", 2))",
                'opts' => [
                    ["heLLo worLd", false],
                    ["heLLo world", true],
                    ["heLlo world", false],
                    ["hello world", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nname = \"Alice\"\nscore = 98.5\nprint(f\"{name!r} scored {score:.1f} — {'Pass' if score >= 50 else 'Fail'}\")",
                'opts' => [
                    ["Alice scored 98.5 — Pass", false],
                    ["'Alice' scored 98.5 — Pass", true],
                    ["'Alice' scored 98.50 — Pass", false],
                    ["Alice scored 98.50 — Pass", false],
                ],
            ],
            [
                'q' => "What does this print?\n\ns = \"   data science   \"\nprint(repr(s.strip().title()))",
                'opts' => [
                    ["'data science'", false],
                    ["'Data Science'", true],
                    ["Data Science", false],
                    ["'   Data Science   '", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nwords = \"one:two:three\"\nprint(words.split(\":\", 1))",
                'opts' => [
                    ["['one', 'two', 'three']", false],
                    ["['one', 'two:three']", true],
                    ["['one:two', 'three']", false],
                    ["['one', 'two']", false],
                ],
            ],
            [
                'q' => "What does this print?\n\ns = \"Python\"\nprint(s[::-1], s[1:4], s[-1])",
                'opts' => [
                    ["nohtyP yth n", false],
                    ["nohtyP yth P", false],
                    ["nohtyP yth n", false],
                    ["nohtyP yth n", true],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.4 LOGIC: BOOLEANS & OPERATORS  (5 questions)
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\nprint(0 or \"\" or [] or \"fallback\" or 42)",
                'opts' => [
                    ["False", false],
                    ["42", false],
                    ["fallback", true],
                    ["[]", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nx = 5\nprint(1 < x < 10, x == 5 == 5, x is 5)",
                'opts' => [
                    ["True True True", false],
                    ["True True False", false],
                    ["True True True — CPython interns small ints so `is` returns True", true],
                    ["True False True", false],
                ],
            ],
            [
                'q' => "What is the output?\n\na = True\nb = False\nprint(a + b, a * 10, int(a and not b))",
                'opts' => [
                    ["1 10 1", true],
                    ["True 10 True", false],
                    ["1 True 1", false],
                    ["True 10 1", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nprint(not not not True, not not False)",
                'opts' => [
                    ["True False", false],
                    ["False True", false],
                    ["False False", true],
                    ["True True", false],
                ],
            ],
            [
                'q' => "What is the result?\n\nx = None\ny = \"default\"\nresult = x or y\nprint(result, type(result).__name__)",
                'opts' => [
                    ["None NoneType", false],
                    ["default str", true],
                    ["True bool", false],
                    ["None str", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.5 COLLECTIONS I: LISTS & ARRAYS  (5 questions)
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What does this print?\n\na = [1, 2, 3]\nb = a[:]\nb.append(4)\nprint(a, b)",
                'opts' => [
                    ["[1, 2, 3, 4] [1, 2, 3, 4]", false],
                    ["[1, 2, 3] [1, 2, 3, 4]", true],
                    ["[1, 2, 3] [1, 2, 3]", false],
                    ["[1, 2, 3, 4] [1, 2, 3]", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nnums = [5, 3, 8, 1, 9, 2]\nprint(sorted(nums, reverse=True)[:3])",
                'opts' => [
                    ["[9, 8, 5]", true],
                    ["[1, 2, 3]", false],
                    ["[5, 3, 8]", false],
                    ["[9, 8, 3]", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nx = [0] * 3\ny = [[]] * 3\ny[0].append(1)\nprint(x, y)",
                'opts' => [
                    ["[0, 0, 0] [[1], [], []]", false],
                    ["[0, 0, 0] [[1], [1], [1]]", true],
                    ["[1, 1, 1] [[1], [1], [1]]", false],
                    ["[0, 0, 0] [[], [], []]", false],
                ],
            ],
            [
                'q' => "What is the output?\n\ndata = [1, 2, 3, 4, 5]\ndata[1:4] = [20, 30]\nprint(data)",
                'opts' => [
                    ["[1, 20, 30, 4, 5]", false],
                    ["[1, 20, 30, 5]", true],
                    ["[1, 20, 30, 3, 4, 5]", false],
                    ["[20, 30]", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nlst = [3, 1, 4, 1, 5]\nlst.sort()\nprint(lst.pop(), lst)",
                'opts' => [
                    ["5 [1, 1, 3, 4]", true],
                    ["3 [1, 1, 4, 5]", false],
                    ["5 [3, 1, 4, 1]", false],
                    ["1 [1, 3, 4, 5]", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.6 COLLECTIONS II: TUPLES & SETS  (5 questions)
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\nt = (1, 2, [3, 4])\nt[2].append(5)\nprint(t)",
                'opts' => [
                    ["(1, 2, [3, 4, 5])", true],
                    ["TypeError: 'tuple' object does not support item assignment", false],
                    ["(1, 2, [3, 4])", false],
                    ["(1, 2, [5])", false],
                ],
            ],
            [
                'q' => "What does this print?\n\na = (1,)\nb = (1)\nc = ()\nprint(type(a).__name__, type(b).__name__, type(c).__name__)",
                'opts' => [
                    ["tuple tuple tuple", false],
                    ["tuple int tuple", true],
                    ["tuple tuple NoneType", false],
                    ["list int tuple", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nA = {1, 2, 3, 4}\nB = {3, 4, 5, 6}\nprint(A - B, A & B, A ^ B)",
                'opts' => [
                    ["{1, 2} {3, 4} {1, 2, 5, 6}", true],
                    ["{1, 2} {3, 4} {3, 4}", false],
                    ["{3, 4} {1, 2} {1, 2, 5, 6}", false],
                    ["{1, 2, 5, 6} {3, 4} {1, 2}", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nx, *y, z = (10, 20, 30, 40, 50)\nprint(x, y, z)",
                'opts' => [
                    ["10 [20, 30, 40] 50", true],
                    ["10 (20, 30, 40) 50", false],
                    ["10 [20, 30, 40, 50] 50", false],
                    ["10 20 50", false],
                ],
            ],
            [
                'q' => "What is the output?\n\ns = {3, 1, 4, 1, 5, 9, 2, 6}\nprint(len(s), 1 in s, type(s).__name__)",
                'opts' => [
                    ["8 True set", false],
                    ["7 True set", true],
                    ["7 False set", false],
                    ["8 True frozenset", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.7 COLLECTIONS III: DICTIONARIES  (5 questions)
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What does this print?\n\nd = {\"a\": 1, \"b\": 2, \"c\": 3}\nprint({v: k for k, v in d.items()})",
                'opts' => [
                    ["{1: 'a', 2: 'b', 3: 'c'}", true],
                    ["{'a': 1, 'b': 2, 'c': 3}", false],
                    ["{('a', 1), ('b', 2), ('c', 3)}", false],
                    ["KeyError", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nd = {\"x\": 10}\nresult = d.get(\"y\", d.get(\"x\", 0))\nprint(result)",
                'opts' => [
                    ["0", false],
                    ["None", false],
                    ["10", true],
                    ["KeyError", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nd1 = {\"a\": 1, \"b\": 2}\nd2 = {\"b\": 99, \"c\": 3}\nprint({**d1, **d2})",
                'opts' => [
                    ["{'a': 1, 'b': 2, 'c': 3}", false],
                    ["{'a': 1, 'b': 99, 'c': 3}", true],
                    ["{'a': 1, 'b': 2, 'b': 99, 'c': 3}", false],
                    ["TypeError: duplicate keys", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nd = {\"a\": 1, \"b\": 2, \"c\": 3}\nfor k in list(d.keys()):\n    if d[k] % 2 != 0:\n        del d[k]\nprint(d)",
                'opts' => [
                    ["{'b': 2}", true],
                    ["{'a': 1, 'c': 3}", false],
                    ["RuntimeError: dictionary changed size during iteration", false],
                    ["{}", false],
                ],
            ],
            [
                'q' => "What does this print?\n\nfrom collections import Counter\nc = Counter(\"mississippi\")\nprint(c.most_common(2))",
                'opts' => [
                    ["[('s', 4), ('i', 4)]", true],
                    ["[('m', 1), ('i', 4)]", false],
                    ["[('i', 4), ('p', 2)]", false],
                    ["[('s', 4), ('p', 2)]", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.8 ADVANCED FLOW: MATCH & TRY/EXCEPT  (5 questions)
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\ntry:\n    x = 1 / 0\nexcept ZeroDivisionError as e:\n    print(\"caught:\", e)\nfinally:\n    print(\"finally\")",
                'opts' => [
                    ["caught: division by zero\nfinally", true],
                    ["finally\ncaught: division by zero", false],
                    ["caught: division by zero", false],
                    ["ZeroDivisionError", false],
                ],
            ],
            [
                'q' => "What does this print?\n\ndef risky():\n    try:\n        return \"try\"\n    finally:\n        return \"finally\"\n\nprint(risky())",
                'opts' => [
                    ["try", false],
                    ["finally", true],
                    ["try\nfinally", false],
                    ["None", false],
                ],
            ],
            [
                'q' => "What is the output (Python 3.10+)?\n\npoint = (0, 1)\nmatch point:\n    case (0, 0):\n        print(\"Origin\")\n    case (0, y):\n        print(f\"Y={y}\")\n    case (x, 0):\n        print(f\"X={x}\")\n    case _:\n        print(\"Other\")",
                'opts' => [
                    ["Origin", false],
                    ["Y=1", true],
                    ["X=0", false],
                    ["Other", false],
                ],
            ],
            [
                'q' => "What does this print?\n\ntry:\n    int(\"abc\")\nexcept (ValueError, TypeError) as e:\n    print(type(e).__name__)\nelse:\n    print(\"no error\")",
                'opts' => [
                    ["no error", false],
                    ["TypeError", false],
                    ["ValueError", true],
                    ["Exception", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nfor i in range(3):\n    try:\n        if i == 1:\n            raise ValueError\n    except ValueError:\n        print(\"err\", end=\" \")\n    else:\n        print(\"ok\", end=\" \")\n    finally:\n        print(i, end=\" \")",
                'opts' => [
                    ["ok 0 err 1 ok 2", false],
                    ["ok 0 err 1 ok 2 ", true],
                    ["0 ok 1 err 2 ok", false],
                    ["ok 0 1 err ok 2", false],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.9 LOOPS: FOR, WHILE, RANGE & ITERATORS  (5 questions)
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What does this print?\n\nresult = [x**2 for x in range(10) if x % 3 == 0]\nprint(result)",
                'opts' => [
                    ["[0, 9, 36, 81]", true],
                    ["[0, 3, 6, 9]", false],
                    ["[9, 36, 81]", false],
                    ["[0, 9, 36]", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nfor i in range(5):\n    if i == 3:\n        break\nelse:\n    print(\"complete\")\nprint(\"done\")",
                'opts' => [
                    ["complete\ndone", false],
                    ["done", true],
                    ["complete", false],
                    ["Nothing is printed", false],
                ],
            ],
            [
                'q' => "What does this print?\n\ndata = [(1, 'a'), (2, 'b'), (3, 'c')]\nfor i, (num, char) in enumerate(data, start=1):\n    print(i, num, char)",
                'opts' => [
                    ["0 1 a\n1 2 b\n2 3 c", false],
                    ["1 1 a\n2 2 b\n3 3 c", true],
                    ["1 a\n2 b\n3 c", false],
                    ["(1, (1, 'a'))\n(2, (2, 'b'))\n(3, (3, 'c'))", false],
                ],
            ],
            [
                'q' => "What is the output?\n\nfrom itertools import islice\ngen = (x * 2 for x in range(100))\nprint(list(islice(gen, 4)))",
                'opts' => [
                    ["[0, 2, 4, 6]", true],
                    ["[0, 2, 4, 6, 8]", false],
                    ["[2, 4, 6, 8]", false],
                    ["[0, 1, 2, 3]", false],
                ],
            ],
            [
                'q' => "What does this print?\n\ni = 0\nwhile i < 5:\n    i += 1\n    if i % 2 == 0:\n        continue\n    print(i, end=\" \")",
                'opts' => [
                    ["1 2 3 4 5", false],
                    ["2 4", false],
                    ["1 3 5", true],
                    ["1 3 5 ", true],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // 1.10 FUNCTIONS: DEF, ARGS, KWARGS & LAMBDAS  (5 questions)
            // ══════════════════════════════════════════════════════════════
            [
                'q' => "What is the output?\n\ndef f(a, b=2, *args, **kwargs):\n    print(a, b, args, kwargs)\n\nf(1, 3, 4, 5, x=10, y=20)",
                'opts' => [
                    ["1 3 (4, 5) {'x': 10, 'y': 20}", true],
                    ["1 2 (3, 4, 5) {'x': 10, 'y': 20}", false],
                    ["1 3 [4, 5] {'x': 10, 'y': 20}", false],
                    ["1 3 (4, 5) {}", false],
                ],
            ],
            [
                'q' => "What does this print?\n\ndef make_adder(n):\n    return lambda x: x + n\n\nadd5 = make_adder(5)\nadd10 = make_adder(10)\nprint(add5(3), add10(3), add5(add10(1)))",
                'opts' => [
                    ["8 13 16", true],
                    ["8 13 15", false],
                    ["5 10 11", false],
                    ["3 3 1", false],
                ],
            ],
            [
                'q' => "What is the output?\n\ndef append_to(element, to=None):\n    if to is None:\n        to = []\n    to.append(element)\n    return to\n\nprint(append_to(1))\nprint(append_to(2))\nprint(append_to(3))",
                'opts' => [
                    ["[1]\n[1, 2]\n[1, 2, 3]", false],
                    ["[1]\n[2]\n[3]", true],
                    ["[1]\n[2]\n[3, 3, 3]", false],
                    ["None\nNone\nNone", false],
                ],
            ],
            [
                'q' => "What does this print?\n\ndef outer():\n    x = 10\n    def inner():\n        nonlocal x\n        x += 5\n        return x\n    return inner\n\nf = outer()\nprint(f(), f(), f())",
                'opts' => [
                    ["15 15 15", false],
                    ["15 20 25", true],
                    ["10 15 20", false],
                    ["UnboundLocalError", false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 1 — Basics of Python Programming (Professional).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Professional");
    }
}