<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 1 — Basics of Python Programming (Newbie) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Newbie tier
 *   2. coding_questions    — 50 questions covering all Python basics
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered:
 *   - Print statements
 *   - Variables & data types
 *   - Arithmetic operators
 *   - String operations
 *   - Type casting
 *   - User input
 *   - Comparison & logical operators
 *   - If / elif / else
 *   - While loops
 *   - For loops & range()
 *   - Lists (basic)
 *   - Basic functions
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module1CodingChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────
        // 1. CHALLENGE
        // ─────────────────────────────────────────────────────────────────

        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (! $category) {
            $this->command->error('Newbie category not found! Run ChallengeCategorySeeder first.');
            return;
        }

        $this->command->info('Creating Module 1 — Basics of Python Programming (Newbie) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Basics of Python Programming',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Write simple Python programs from scratch — print messages, declare variables of different types, perform basic arithmetic, work with strings, use conditions and loops, and define simple functions. Tasks are short and self-contained; no prior experience needed.',
                'time_limit_seconds' => 900,
                'base_xp'            => 500,
                'order_index'        => 1,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 1: Print Statements (Q1–Q5)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Print the text `Hello, World!` to the screen.

Your output must match exactly (no extra spaces or punctuation).
MD,
                'starter_code'        => "# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Print your name on the first line and your age on the second line.

Use these exact values:
- Name: `Alice`
- Age: `20`

Expected output:
```
Alice
20
```
MD,
                'starter_code'        => "# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Print the following three lines exactly:
```
Python is fun.
I love coding.
Let's get started!
```
MD,
                'starter_code'        => "# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Print the numbers 1 through 5, each on its own line.

Expected output:
```
1
2
3
4
5
```
MD,
                'starter_code'        => "# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Print a blank line between two messages.

Expected output:
```
Hello

Goodbye
```

Hint: `print()` with no arguments prints a blank line.
MD,
                'starter_code'        => "# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: Variables & Data Types (Q6–Q12)
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Create a variable called `greeting` with the value `"Hello"` and print it.

Expected output:
```
Hello
```
MD,
                'starter_code'        => "# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Create two variables:
- `x` with the value `10`
- `y` with the value `3.5`

Print both variables, each on its own line.

Expected output:
```
10
3.5
```
MD,
                'starter_code'        => "# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Create a boolean variable called `is_sunny` set to `True` and print it.

Expected output:
```
True
```
MD,
                'starter_code'        => "# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Print the data type of the value `42` using `type()`.

Expected output:
```
<class 'int'>
```
MD,
                'starter_code'        => "# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Swap the values of two variables and print both.

Start with:
- `a = 5`
- `b = 10`

After swapping, print `a` then `b`.

Expected output:
```
10
5
```
MD,
                'starter_code'        => "a = 5\nb = 10\n# Swap and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Create a variable `name` and assign it a string, then print a sentence using that variable.

Use `name = "Bob"` and print:
```
My name is Bob.
```
MD,
                'starter_code'        => "name = \"Bob\"\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Assign `None` to a variable called `result` and print it.

Expected output:
```
None
```
MD,
                'starter_code'        => "# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Arithmetic Operators (Q13–Q18)
            // ═══════════════════════════════════════════════════════════════

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Read a number from input and print its square.

Example:
```
Input:  4
Output: 16
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Read two integers (one per line) and print their sum.

Example:
```
Input:
3
7
Output: 10
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Read two integers and print their difference (first minus second).

Example:
```
Input:
10
3
Output: 7
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Read two integers and print their product.

Example:
```
Input:
6
7
Output: 42
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Read two integers and print the quotient (integer division) and the remainder on separate lines.

Example:
```
Input:
10
3
Output:
3
1
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Read a number and print its cube (raised to the power of 3).

Example:
```
Input:  3
Output: 27
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: String Operations (Q19–Q24)
            // ═══════════════════════════════════════════════════════════════

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Read a string from input and print it in all uppercase.

Example:
```
Input:  hello
Output: HELLO
```
MD,
                'starter_code'        => "s = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Read a string from input and print its length.

Example:
```
Input:  python
Output: 6
```
MD,
                'starter_code'        => "s = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Read a first name and a last name from input (one per line) and print the full name separated by a space.

Example:
```
Input:
John
Doe
Output: John Doe
```
MD,
                'starter_code'        => "first = input()\nlast = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Read a string from input and print it reversed.

Example:
```
Input:  hello
Output: olleh
```
MD,
                'starter_code'        => "s = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Read a string from input and print only the first character.

Example:
```
Input:  Python
Output: P
```
MD,
                'starter_code'        => "s = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Read a string and a number from input (one per line). Print the string repeated that many times with no spaces.

Example:
```
Input:
ab
3
Output: ababab
```
MD,
                'starter_code'        => "s = input()\nn = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Type Casting (Q25–Q27)
            // ═══════════════════════════════════════════════════════════════

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Read a decimal number from input and convert it to an integer, then print it.

Example:
```
Input:  3.9
Output: 3
```

Note: `int()` truncates, it does not round.
MD,
                'starter_code'        => "n = float(input())\n# Convert to int and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Read an integer from input and print it as a float.

Example:
```
Input:  5
Output: 5.0
```
MD,
                'starter_code'        => "n = int(input())\n# Convert to float and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Read two numbers as strings from input, convert them to integers, and print their sum.

Example:
```
Input:
4
6
Output: 10
```
MD,
                'starter_code'        => "a = input()\nb = input()\n# Convert and sum\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: Comparison & Logical Operators (Q28–Q31)
            // ═══════════════════════════════════════════════════════════════

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Read two integers and print `True` if the first is greater than the second, otherwise print `False`.

Example:
```
Input:
5
3
Output: True
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Read two integers and print `True` if they are equal, otherwise print `False`.

Example:
```
Input:
4
4
Output: True
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Read two boolean-like integers (0 or 1) from input and print the result of applying `and` to them.

Print `True` if both are 1, otherwise print `False`.

Example:
```
Input:
1
0
Output: False
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Read an integer and print `True` if it is even, otherwise print `False`.

Example:
```
Input:  4
Output: True
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: If / elif / else (Q32–Q37)
            // ═══════════════════════════════════════════════════════════════

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Read an integer from input. If it is positive, print `Positive`. If it is negative, print `Negative`. If it is zero, print `Zero`.

Example:
```
Input:  -3
Output: Negative
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Read an integer from input. Print `Even` if it is even, or `Odd` if it is odd.

Example:
```
Input:  7
Output: Odd
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Read two integers and print the larger one. If they are equal, print `Equal`.

Example:
```
Input:
8
5
Output: 8
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Read a score (integer 0–100) and print the grade:
- 90–100 → `A`
- 80–89  → `B`
- 70–79  → `C`
- 60–69  → `D`
- Below 60 → `F`

Example:
```
Input:  85
Output: B
```
MD,
                'starter_code'        => "score = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Read a number and check if it is divisible by both 2 and 3. Print `Yes` if it is, otherwise print `No`.

Example:
```
Input:  6
Output: Yes
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Read an integer and print `Fizz` if it is divisible by 3, `Buzz` if divisible by 5, `FizzBuzz` if divisible by both, or the number itself if none of the above.

Example:
```
Input:  15
Output: FizzBuzz
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: While Loops (Q38–Q41)
            // ═══════════════════════════════════════════════════════════════

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Print the numbers from 1 to 5 using a `while` loop, each on its own line.

Expected output:
```
1
2
3
4
5
```
MD,
                'starter_code'        => "# Use a while loop\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Read an integer `n` from input. Use a `while` loop to print all numbers from 1 to `n`, each on its own line.

Example:
```
Input:  4
Output:
1
2
3
4
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Use a `while` loop to compute and print the sum of numbers from 1 to `n`.

Read `n` from input.

Example:
```
Input:  5
Output: 15
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Read a number `n` from input and use a `while` loop to count down from `n` to 1, printing each number on its own line.

Example:
```
Input:  3
Output:
3
2
1
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: For Loops & range() (Q42–Q46)
            // ═══════════════════════════════════════════════════════════════

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Use a `for` loop with `range()` to print the numbers 0 through 4, each on its own line.

Expected output:
```
0
1
2
3
4
```
MD,
                'starter_code'        => "# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Read an integer `n` and use a `for` loop to print the multiplication table of `n` from 1 to 5.

Format each line as: `n x i = result`

Example:
```
Input:  3
Output:
3 x 1 = 3
3 x 2 = 6
3 x 3 = 9
3 x 4 = 12
3 x 5 = 15
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Use a `for` loop to print only the even numbers from 2 to 10, each on its own line.

Expected output:
```
2
4
6
8
10
```
MD,
                'starter_code'        => "# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Read an integer `n` and use a `for` loop to compute and print the factorial of `n`.

Recall: `n! = 1 × 2 × ... × n`

Example:
```
Input:  5
Output: 120
```

Assume `n >= 1`.
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Use a `for` loop to iterate over a string character by character and print each character on its own line.

Read the string from input.

Example:
```
Input:  hi
Output:
h
i
```
MD,
                'starter_code'        => "s = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: Lists — Basic (Q47–Q49)
            // ═══════════════════════════════════════════════════════════════

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Read 5 integers from input (one per line), store them in a list, and print the list.

Example:
```
Input:
1
2
3
4
5
Output: [1, 2, 3, 4, 5]
```
MD,
                'starter_code'        => "# Read 5 integers and store in a list\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Read 5 integers from input (one per line) into a list and print the largest value.

Example:
```
Input:
3
1
4
1
5
Output: 5
```

Hint: use `max()`.
MD,
                'starter_code'        => "# Read 5 integers into a list\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Read 5 integers from input (one per line) into a list and print their sum.

Example:
```
Input:
1
2
3
4
5
Output: 15
```

Hint: use `sum()`.
MD,
                'starter_code'        => "# Read 5 integers into a list\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 11: Basic Functions (Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Define a function called `greet` that takes a name as a parameter and prints `Hello, <name>!`.

Call the function with the name read from input.

Example:
```
Input:  Alice
Output: Hello, Alice!
```
MD,
                'starter_code'        => "# Define the greet function\n\nname = input()\n# Call the function\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],
        ];

        // Insert questions; collect IDs
        $questionIds = [];

        foreach ($questionDefs as $def) {
            $row = DB::table('coding_questions')->where([
                'challenge_id' => $challenge->id,
                'order_index'  => $def['order_index'],
            ])->first();

            if (! $row) {
                $id = DB::table('coding_questions')->insertGetId(array_merge(
                    ['challenge_id' => $challenge->id, 'language' => 'python'],
                    $def,
                    ['created_at' => now(), 'updated_at' => now()]
                ));
            } else {
                $id = $row->id;
            }

            $questionIds[$def['order_index']] = $id;
        }

        // ─────────────────────────────────────────────────────────────────
        // 3. TEST CASES (4 per question: 2 visible, 2 hidden)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding test cases...');

        $now = now()->toDateTimeString();

        $seed = function (int $ord, array $cases) use ($questionIds, $now): void {
            $qid = $questionIds[$ord] ?? null;
            if (! $qid) return;

            if (DB::table('test_cases')->where('coding_question_id', $qid)->exists()) {
                $this->command->warn("  test_cases for Q{$ord} already exist — skipping.");
                return;
            }

            $rows = array_map(fn ($c) => array_merge(
                ['coding_question_id' => $qid, 'created_at' => $now, 'updated_at' => $now],
                $c
            ), $cases);

            DB::table('test_cases')->insert($rows);
        };

        // ── Q1: Hello, World! ─────────────────────────────────────────────
        $seed(1, [
            ['input' => null, 'expected_output' => 'Hello, World!', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => 'Hello, World!', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => 'Hello, World!', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => 'Hello, World!', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: Print name and age ────────────────────────────────────────
        $seed(2, [
            ['input' => null, 'expected_output' => "Alice\n20", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Alice\n20", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Alice\n20", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Alice\n20", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: Three lines ───────────────────────────────────────────────
        $seed(3, [
            ['input' => null, 'expected_output' => "Python is fun.\nI love coding.\nLet's get started!", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Python is fun.\nI love coding.\nLet's get started!", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Python is fun.\nI love coding.\nLet's get started!", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Python is fun.\nI love coding.\nLet's get started!", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: Print 1 to 5 ─────────────────────────────────────────────
        $seed(4, [
            ['input' => null, 'expected_output' => "1\n2\n3\n4\n5", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1\n2\n3\n4\n5", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1\n2\n3\n4\n5", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1\n2\n3\n4\n5", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: Blank line between messages ──────────────────────────────
        $seed(5, [
            ['input' => null, 'expected_output' => "Hello\n\nGoodbye", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Hello\n\nGoodbye", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Hello\n\nGoodbye", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Hello\n\nGoodbye", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: Variable greeting ─────────────────────────────────────────
        $seed(6, [
            ['input' => null, 'expected_output' => 'Hello', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => 'Hello', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => 'Hello', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => 'Hello', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: Two variables ─────────────────────────────────────────────
        $seed(7, [
            ['input' => null, 'expected_output' => "10\n3.5", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "10\n3.5", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "10\n3.5", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "10\n3.5", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: Boolean variable ──────────────────────────────────────────
        $seed(8, [
            ['input' => null, 'expected_output' => 'True', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => 'True', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => 'True', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => 'True', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: type() of 42 ──────────────────────────────────────────────
        $seed(9, [
            ['input' => null, 'expected_output' => "<class 'int'>", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "<class 'int'>", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "<class 'int'>", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "<class 'int'>", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: Swap variables ───────────────────────────────────────────
        $seed(10, [
            ['input' => null, 'expected_output' => "10\n5", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "10\n5", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "10\n5", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "10\n5", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: Print with variable ──────────────────────────────────────
        $seed(11, [
            ['input' => null, 'expected_output' => 'My name is Bob.', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => 'My name is Bob.', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => 'My name is Bob.', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => 'My name is Bob.', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: None variable ────────────────────────────────────────────
        $seed(12, [
            ['input' => null, 'expected_output' => 'None', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => 'None', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => 'None', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => 'None', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: Square of a number ───────────────────────────────────────
        $seed(13, [
            ['input' => '4',  'expected_output' => '16',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '3',  'expected_output' => '9',   'is_hidden' => false, 'order_index' => 2],
            ['input' => '0',  'expected_output' => '0',   'is_hidden' => true,  'order_index' => 3],
            ['input' => '10', 'expected_output' => '100', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: Sum of two numbers ───────────────────────────────────────
        $seed(14, [
            ['input' => "3\n7",   'expected_output' => '10',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1",   'expected_output' => '2',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0",   'expected_output' => '0',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "99\n1",  'expected_output' => '100', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: Difference ───────────────────────────────────────────────
        $seed(15, [
            ['input' => "10\n3",  'expected_output' => '7',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n5",   'expected_output' => '0',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "20\n8",  'expected_output' => '12', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n0",   'expected_output' => '1',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: Product ──────────────────────────────────────────────────
        $seed(16, [
            ['input' => "6\n7",  'expected_output' => '42', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n3",  'expected_output' => '6',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n5",  'expected_output' => '0',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "9\n9",  'expected_output' => '81', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: Quotient and remainder ───────────────────────────────────
        $seed(17, [
            ['input' => "10\n3",  'expected_output' => "3\n1",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "8\n2",   'expected_output' => "4\n0",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "7\n4",   'expected_output' => "1\n3",  'is_hidden' => true,  'order_index' => 3],
            ['input' => "20\n6",  'expected_output' => "3\n2",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: Cube ─────────────────────────────────────────────────────
        $seed(18, [
            ['input' => '3',  'expected_output' => '27',   'is_hidden' => false, 'order_index' => 1],
            ['input' => '2',  'expected_output' => '8',    'is_hidden' => false, 'order_index' => 2],
            ['input' => '0',  'expected_output' => '0',    'is_hidden' => true,  'order_index' => 3],
            ['input' => '5',  'expected_output' => '125',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: Uppercase ────────────────────────────────────────────────
        $seed(19, [
            ['input' => 'hello',   'expected_output' => 'HELLO',   'is_hidden' => false, 'order_index' => 1],
            ['input' => 'python',  'expected_output' => 'PYTHON',  'is_hidden' => false, 'order_index' => 2],
            ['input' => 'world',   'expected_output' => 'WORLD',   'is_hidden' => true,  'order_index' => 3],
            ['input' => 'abc',     'expected_output' => 'ABC',     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: Length of string ─────────────────────────────────────────
        $seed(20, [
            ['input' => 'python',  'expected_output' => '6', 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'hi',      'expected_output' => '2', 'is_hidden' => false, 'order_index' => 2],
            ['input' => 'a',       'expected_output' => '1', 'is_hidden' => true,  'order_index' => 3],
            ['input' => 'coding',  'expected_output' => '6', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: Full name ────────────────────────────────────────────────
        $seed(21, [
            ['input' => "John\nDoe",    'expected_output' => 'John Doe',    'is_hidden' => false, 'order_index' => 1],
            ['input' => "Alice\nSmith", 'expected_output' => 'Alice Smith', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "Bob\nLee",     'expected_output' => 'Bob Lee',     'is_hidden' => true,  'order_index' => 3],
            ['input' => "Jane\nDoe",    'expected_output' => 'Jane Doe',    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: Reversed string ──────────────────────────────────────────
        $seed(22, [
            ['input' => 'hello',   'expected_output' => 'olleh',   'is_hidden' => false, 'order_index' => 1],
            ['input' => 'abc',     'expected_output' => 'cba',     'is_hidden' => false, 'order_index' => 2],
            ['input' => 'python',  'expected_output' => 'nohtyp',  'is_hidden' => true,  'order_index' => 3],
            ['input' => 'a',       'expected_output' => 'a',       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: First character ──────────────────────────────────────────
        $seed(23, [
            ['input' => 'Python',  'expected_output' => 'P', 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'hello',   'expected_output' => 'h', 'is_hidden' => false, 'order_index' => 2],
            ['input' => 'abc',     'expected_output' => 'a', 'is_hidden' => true,  'order_index' => 3],
            ['input' => 'xyz',     'expected_output' => 'x', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: Repeat string ────────────────────────────────────────────
        $seed(24, [
            ['input' => "ab\n3",  'expected_output' => 'ababab',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "hi\n2",  'expected_output' => 'hihi',     'is_hidden' => false, 'order_index' => 2],
            ['input' => "x\n5",   'expected_output' => 'xxxxx',    'is_hidden' => true,  'order_index' => 3],
            ['input' => "go\n1",  'expected_output' => 'go',       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: Float to int ─────────────────────────────────────────────
        $seed(25, [
            ['input' => '3.9',  'expected_output' => '3', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '7.1',  'expected_output' => '7', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '0.99', 'expected_output' => '0', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '10.5', 'expected_output' => '10','is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: Int to float ─────────────────────────────────────────────
        $seed(26, [
            ['input' => '5',   'expected_output' => '5.0',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '0',   'expected_output' => '0.0',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '12',  'expected_output' => '12.0', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '100', 'expected_output' => '100.0','is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: String to int sum ────────────────────────────────────────
        $seed(27, [
            ['input' => "4\n6",   'expected_output' => '10', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "10\n20", 'expected_output' => '30', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0",   'expected_output' => '0',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "7\n3",   'expected_output' => '10', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: Greater than comparison ──────────────────────────────────
        $seed(28, [
            ['input' => "5\n3",  'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n4",  'expected_output' => 'False', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "10\n10",'expected_output' => 'False', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n0",  'expected_output' => 'True',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: Equality comparison ──────────────────────────────────────
        $seed(29, [
            ['input' => "4\n4",  'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5",  'expected_output' => 'False', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0",  'expected_output' => 'True',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n2",  'expected_output' => 'False', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: Logical AND ──────────────────────────────────────────────
        $seed(30, [
            ['input' => "1\n0",  'expected_output' => 'False', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1",  'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0",  'expected_output' => 'False', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n1",  'expected_output' => 'False', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: Even check ───────────────────────────────────────────────
        $seed(31, [
            ['input' => '4',  'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '7',  'expected_output' => 'False', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '0',  'expected_output' => 'True',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '11', 'expected_output' => 'False', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: Positive / Negative / Zero ──────────────────────────────
        $seed(32, [
            ['input' => '-3', 'expected_output' => 'Negative', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '5',  'expected_output' => 'Positive', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '0',  'expected_output' => 'Zero',     'is_hidden' => true,  'order_index' => 3],
            ['input' => '10', 'expected_output' => 'Positive', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: Even / Odd ───────────────────────────────────────────────
        $seed(33, [
            ['input' => '7',  'expected_output' => 'Odd',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '4',  'expected_output' => 'Even', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '0',  'expected_output' => 'Even', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '13', 'expected_output' => 'Odd',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: Larger of two ────────────────────────────────────────────
        $seed(34, [
            ['input' => "8\n5",   'expected_output' => '8',     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n3",   'expected_output' => 'Equal', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n9",   'expected_output' => '9',     'is_hidden' => true,  'order_index' => 3],
            ['input' => "10\n1",  'expected_output' => '10',    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: Grade ────────────────────────────────────────────────────
        $seed(35, [
            ['input' => '85',  'expected_output' => 'B', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '55',  'expected_output' => 'F', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '95',  'expected_output' => 'A', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '72',  'expected_output' => 'C', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: Divisible by 2 and 3 ────────────────────────────────────
        $seed(36, [
            ['input' => '6',   'expected_output' => 'Yes', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '4',   'expected_output' => 'No',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '12',  'expected_output' => 'Yes', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '9',   'expected_output' => 'No',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: FizzBuzz ─────────────────────────────────────────────────
        $seed(37, [
            ['input' => '15', 'expected_output' => 'FizzBuzz', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '9',  'expected_output' => 'Fizz',     'is_hidden' => false, 'order_index' => 2],
            ['input' => '10', 'expected_output' => 'Buzz',     'is_hidden' => true,  'order_index' => 3],
            ['input' => '7',  'expected_output' => '7',        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: While 1 to 5 ─────────────────────────────────────────────
        $seed(38, [
            ['input' => null, 'expected_output' => "1\n2\n3\n4\n5", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1\n2\n3\n4\n5", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1\n2\n3\n4\n5", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1\n2\n3\n4\n5", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: While 1 to n ─────────────────────────────────────────────
        $seed(39, [
            ['input' => '4',  'expected_output' => "1\n2\n3\n4",          'is_hidden' => false, 'order_index' => 1],
            ['input' => '1',  'expected_output' => '1',                    'is_hidden' => false, 'order_index' => 2],
            ['input' => '6',  'expected_output' => "1\n2\n3\n4\n5\n6",    'is_hidden' => true,  'order_index' => 3],
            ['input' => '3',  'expected_output' => "1\n2\n3",             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: Sum 1 to n (while) ───────────────────────────────────────
        $seed(40, [
            ['input' => '5',   'expected_output' => '15',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '1',   'expected_output' => '1',   'is_hidden' => false, 'order_index' => 2],
            ['input' => '10',  'expected_output' => '55',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '3',   'expected_output' => '6',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: Countdown ────────────────────────────────────────────────
        $seed(41, [
            ['input' => '3',  'expected_output' => "3\n2\n1",          'is_hidden' => false, 'order_index' => 1],
            ['input' => '5',  'expected_output' => "5\n4\n3\n2\n1",    'is_hidden' => false, 'order_index' => 2],
            ['input' => '1',  'expected_output' => '1',                 'is_hidden' => true,  'order_index' => 3],
            ['input' => '4',  'expected_output' => "4\n3\n2\n1",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: For range 0 to 4 ─────────────────────────────────────────
        $seed(42, [
            ['input' => null, 'expected_output' => "0\n1\n2\n3\n4", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "0\n1\n2\n3\n4", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "0\n1\n2\n3\n4", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "0\n1\n2\n3\n4", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: Multiplication table ─────────────────────────────────────
        $seed(43, [
            ['input' => '3', 'expected_output' => "3 x 1 = 3\n3 x 2 = 6\n3 x 3 = 9\n3 x 4 = 12\n3 x 5 = 15",   'is_hidden' => false, 'order_index' => 1],
            ['input' => '2', 'expected_output' => "2 x 1 = 2\n2 x 2 = 4\n2 x 3 = 6\n2 x 4 = 8\n2 x 5 = 10",    'is_hidden' => false, 'order_index' => 2],
            ['input' => '5', 'expected_output' => "5 x 1 = 5\n5 x 2 = 10\n5 x 3 = 15\n5 x 4 = 20\n5 x 5 = 25", 'is_hidden' => true,  'order_index' => 3],
            ['input' => '1', 'expected_output' => "1 x 1 = 1\n1 x 2 = 2\n1 x 3 = 3\n1 x 4 = 4\n1 x 5 = 5",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: Even numbers 2 to 10 ─────────────────────────────────────
        $seed(44, [
            ['input' => null, 'expected_output' => "2\n4\n6\n8\n10", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "2\n4\n6\n8\n10", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "2\n4\n6\n8\n10", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "2\n4\n6\n8\n10", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: Factorial ────────────────────────────────────────────────
        $seed(45, [
            ['input' => '5',  'expected_output' => '120',   'is_hidden' => false, 'order_index' => 1],
            ['input' => '1',  'expected_output' => '1',     'is_hidden' => false, 'order_index' => 2],
            ['input' => '4',  'expected_output' => '24',    'is_hidden' => true,  'order_index' => 3],
            ['input' => '6',  'expected_output' => '720',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: Iterate string characters ────────────────────────────────
        $seed(46, [
            ['input' => 'hi',      'expected_output' => "h\ni",         'is_hidden' => false, 'order_index' => 1],
            ['input' => 'abc',     'expected_output' => "a\nb\nc",      'is_hidden' => false, 'order_index' => 2],
            ['input' => 'python',  'expected_output' => "p\ny\nt\nh\no\nn", 'is_hidden' => true, 'order_index' => 3],
            ['input' => 'ok',      'expected_output' => "o\nk",         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: Read 5 integers into list ───────────────────────────────
        $seed(47, [
            ['input' => "1\n2\n3\n4\n5",   'expected_output' => '[1, 2, 3, 4, 5]',    'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n4\n3\n2\n1",   'expected_output' => '[5, 4, 3, 2, 1]',    'is_hidden' => false, 'order_index' => 2],
            ['input' => "10\n20\n30\n40\n50", 'expected_output' => '[10, 20, 30, 40, 50]', 'is_hidden' => true, 'order_index' => 3],
            ['input' => "0\n0\n0\n0\n0",   'expected_output' => '[0, 0, 0, 0, 0]',    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: Max of list ──────────────────────────────────────────────
        $seed(48, [
            ['input' => "3\n1\n4\n1\n5",   'expected_output' => '5',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "10\n2\n8\n6\n4",  'expected_output' => '10',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0\n0\n1\n0",   'expected_output' => '1',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "99\n1\n50\n25\n3",'expected_output' => '99',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: Sum of list ──────────────────────────────────────────────
        $seed(49, [
            ['input' => "1\n2\n3\n4\n5",   'expected_output' => '15',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "10\n10\n10\n10\n10", 'expected_output' => '50', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "0\n0\n0\n0\n0",   'expected_output' => '0',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n5\n5\n5\n5",   'expected_output' => '25',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: greet() function ─────────────────────────────────────────
        $seed(50, [
            ['input' => 'Alice',   'expected_output' => 'Hello, Alice!',   'is_hidden' => false, 'order_index' => 1],
            ['input' => 'Bob',     'expected_output' => 'Hello, Bob!',     'is_hidden' => false, 'order_index' => 2],
            ['input' => 'World',   'expected_output' => 'Hello, World!',   'is_hidden' => true,  'order_index' => 3],
            ['input' => 'Python',  'expected_output' => 'Hello, Python!',  'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 1 Coding (Newbie) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}