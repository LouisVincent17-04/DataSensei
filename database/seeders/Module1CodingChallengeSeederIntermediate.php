<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 1 — Basics of Python Programming (Intermediate) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Intermediate tier
 *   2. coding_questions    — 50 questions covering applied Python concepts
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered:
 *   - String slicing & formatting
 *   - List operations & comprehensions
 *   - Dictionaries & sets
 *   - Functions with parameters & return values
 *   - Recursion (basic)
 *   - Nested loops
 *   - Exception handling (try/except)
 *   - File-like string processing
 *   - Sorting & searching
 *   - Lambda & map/filter
 *   - Tuples
 *   - Modules (math, random concepts)
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module1CodingChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────
        // 1. CHALLENGE
        // ─────────────────────────────────────────────────────────────────

        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (! $category) {
            $this->command->error('Intermediate category not found! Run ChallengeCategorySeeder first.');
            return;
        }

        $this->command->info('Creating Module 1 — Basics of Python Programming (Intermediate) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Basics of Python Programming',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Apply core Python concepts to solve practical problems — manipulate strings and collections, write reusable functions, handle exceptions, and use list comprehensions. Tasks require multi-step logic and an understanding of Python\'s built-in tools.',
                'time_limit_seconds' => 1200,
                'base_xp'            => 1000,
                'order_index'        => 1,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 1: String Slicing & Formatting (Q1–Q6)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Read a string from input and print it **reversed**.

Example:
```
Input:  hello
Output: olleh
```
MD,
                'starter_code'        => "s = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Read a string and print every **second character** starting from index 0.

Example:
```
Input:  abcdef
Output: ace
```
MD,
                'starter_code'        => "s = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Read a string and print it in **title case** (first letter of each word capitalised).

Example:
```
Input:  the quick brown fox
Output: The Quick Brown Fox
```
MD,
                'starter_code'        => "s = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Read a name and an age from input (one per line). Print the formatted string:

```
My name is <name> and I am <age> years old.
```

Use an **f-string**.

Example:
```
Input:
Alice
30
Output: My name is Alice and I am 30 years old.
```
MD,
                'starter_code'        => "name = input()\nage = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Read a string and print `True` if it is a **palindrome** (reads the same forwards and backwards, case-insensitive), otherwise print `False`.

Example:
```
Input:  Racecar
Output: True
```
MD,
                'starter_code'        => "s = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Read a sentence from input and print the **number of vowels** (a, e, i, o, u — case-insensitive) it contains.

Example:
```
Input:  Hello World
Output: 3
```
MD,
                'starter_code'        => "s = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: Lists & List Comprehensions (Q7–Q13)
            // ═══════════════════════════════════════════════════════════════

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Read `n` integers (first line is `n`, then one integer per line). Print the list **sorted in ascending order**.

Example:
```
Input:
4
3
1
4
2
Output: [1, 2, 3, 4]
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Read `n` integers and print a new list containing only the **even** numbers, preserving order.

Example:
```
Input:
5
1
2
3
4
5
Output: [2, 4]
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Read `n` integers and print the list with each element **squared**, using a list comprehension.

Example:
```
Input:
3
2
3
4
Output: [4, 9, 16]
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Read `n` integers and print their **average** rounded to 2 decimal places.

Example:
```
Input:
4
10
20
30
40
Output: 25.00
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Read `n` integers and print the **second largest** unique value.

Example:
```
Input:
5
4
1
3
2
5
Output: 4
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Read `n` integers and print the list **flattened** — remove duplicates and sort in ascending order.

Example:
```
Input:
6
3
1
2
3
1
4
Output: [1, 2, 3, 4]
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Read `n` integers and **rotate** the list to the left by 1 position, then print it.

Example:
```
Input:
4
1
2
3
4
Output: [2, 3, 4, 1]
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Dictionaries & Sets (Q14–Q19)
            // ═══════════════════════════════════════════════════════════════

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Read a sentence from input and print each **word** and its **frequency**, one per line, sorted alphabetically.

Format: `word: count`

Example:
```
Input:  the cat sat on the mat
Output:
cat: 1
mat: 1
on: 1
sat: 1
the: 2
```
MD,
                'starter_code'        => "s = input().split()\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Read `n` key-value pairs (one per line, separated by a space). Store them in a dictionary and print the value for a given key (provided on the last line). Print `Not found` if the key does not exist.

Example:
```
Input:
3
name Alice
age 30
city Manila
age
Output: 30
```
MD,
                'starter_code'        => "n = int(input())\nd = {}\nfor _ in range(n):\n    k, v = input().split()\n    d[k] = v\nkey = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Read two lines of space-separated integers. Print the **intersection** (common elements) as a sorted list.

Example:
```
Input:
1 2 3 4 5
3 4 5 6 7
Output: [3, 4, 5]
```
MD,
                'starter_code'        => "a = set(map(int, input().split()))\nb = set(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Read two lines of space-separated integers. Print the **union** as a sorted list.

Example:
```
Input:
1 2 3
3 4 5
Output: [1, 2, 3, 4, 5]
```
MD,
                'starter_code'        => "a = set(map(int, input().split()))\nb = set(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Read a sentence and print the **most frequent character** (ignoring spaces). If there is a tie, print the one that comes first alphabetically.

Example:
```
Input:  hello world
Output: l
```
MD,
                'starter_code'        => "s = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Read a list of words (space-separated) and print a dictionary mapping each word to its **length**, sorted by key.

Format: `{'word': length, ...}`

Example:
```
Input:  cat elephant bat
Output: {'bat': 3, 'cat': 3, 'elephant': 8}
```
MD,
                'starter_code'        => "words = input().split()\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Functions with Parameters & Return Values (Q20–Q25)
            // ═══════════════════════════════════════════════════════════════

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Write a function `is_prime(n)` that returns `True` if `n` is a prime number, otherwise `False`. Read an integer from input and print the result.

Example:
```
Input:  7
Output: True
```
MD,
                'starter_code'        => "def is_prime(n):\n    # Write your solution here\n    pass\n\nn = int(input())\nprint(is_prime(n))\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Write a function `celsius_to_fahrenheit(c)` that converts Celsius to Fahrenheit using the formula `F = c * 9/5 + 32`. Read a float and print the result rounded to 2 decimal places.

Example:
```
Input:  100
Output: 212.00
```
MD,
                'starter_code'        => "def celsius_to_fahrenheit(c):\n    # Write your solution here\n    pass\n\nc = float(input())\nprint(celsius_to_fahrenheit(c))\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Write a function `count_words(sentence)` that returns the number of words in a sentence. Read a line from input and print the result.

Example:
```
Input:  The quick brown fox
Output: 4
```
MD,
                'starter_code'        => "def count_words(sentence):\n    # Write your solution here\n    pass\n\nsentence = input()\nprint(count_words(sentence))\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Write a function `gcd(a, b)` that returns the **greatest common divisor** of two integers using the Euclidean algorithm. Read two integers and print the result.

Example:
```
Input:
48
18
Output: 6
```
MD,
                'starter_code'        => "def gcd(a, b):\n    # Write your solution here\n    pass\n\na = int(input())\nb = int(input())\nprint(gcd(a, b))\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Write a function `power(base, exp)` that returns `base` raised to `exp` **without using `**` or `pow()`**. Read two integers and print the result.

Example:
```
Input:
2
10
Output: 1024
```
MD,
                'starter_code'        => "def power(base, exp):\n    # Write your solution here\n    pass\n\nbase = int(input())\nexp = int(input())\nprint(power(base, exp))\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Write a function `flatten(nested)` that takes a **list of lists** and returns a single flat list. Read `n` rows (first line is `n`), each containing space-separated integers. Print the flattened list.

Example:
```
Input:
3
1 2 3
4 5
6
Output: [1, 2, 3, 4, 5, 6]
```
MD,
                'starter_code'        => "def flatten(nested):\n    # Write your solution here\n    pass\n\nn = int(input())\nnested = [list(map(int, input().split())) for _ in range(n)]\nprint(flatten(nested))\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Recursion (Q26–Q30)
            // ═══════════════════════════════════════════════════════════════

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Write a **recursive** function `factorial(n)` that returns `n!`. Read an integer and print the result.

Example:
```
Input:  6
Output: 720
```
MD,
                'starter_code'        => "def factorial(n):\n    # Write your recursive solution here\n    pass\n\nn = int(input())\nprint(factorial(n))\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Write a **recursive** function `fibonacci(n)` that returns the `n`-th Fibonacci number (0-indexed: `fibonacci(0) = 0`, `fibonacci(1) = 1`). Read an integer and print the result.

Example:
```
Input:  7
Output: 13
```
MD,
                'starter_code'        => "def fibonacci(n):\n    # Write your recursive solution here\n    pass\n\nn = int(input())\nprint(fibonacci(n))\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Write a **recursive** function `sum_digits(n)` that returns the sum of all digits of a non-negative integer. Read an integer and print the result.

Example:
```
Input:  493
Output: 16
```
MD,
                'starter_code'        => "def sum_digits(n):\n    # Write your recursive solution here\n    pass\n\nn = int(input())\nprint(sum_digits(n))\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Write a **recursive** function `reverse_string(s)` that returns the reversed string. Read a string and print the result.

Example:
```
Input:  python
Output: nohtyp
```
MD,
                'starter_code'        => "def reverse_string(s):\n    # Write your recursive solution here\n    pass\n\ns = input()\nprint(reverse_string(s))\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Write a **recursive** function `power(base, exp)` that returns `base ** exp`. Read two integers and print the result.

Example:
```
Input:
3
4
Output: 81
```
MD,
                'starter_code'        => "def power(base, exp):\n    # Write your recursive solution here\n    pass\n\nbase = int(input())\nexp = int(input())\nprint(power(base, exp))\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: Nested Loops & Patterns (Q31–Q34)
            // ═══════════════════════════════════════════════════════════════

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Read an integer `n` and print a right-angled triangle of stars with `n` rows.

Example:
```
Input:  4
Output:
*
**
***
****
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Read an integer `n` and print the multiplication table for numbers 1 through `n` (each row space-separated).

Example:
```
Input:  3
Output:
1 2 3
2 4 6
3 6 9
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Read an integer `n` and print a **number pyramid** of `n` rows.

Example:
```
Input:  4
Output:
1
1 2
1 2 3
1 2 3 4
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Read an integer `n` and print all **prime numbers** from 2 to `n` (inclusive), one per line.

Example:
```
Input:  10
Output:
2
3
5
7
```
MD,
                'starter_code'        => "n = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: Exception Handling (Q35–Q37)
            // ═══════════════════════════════════════════════════════════════

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Read a string from input. Try to convert it to an integer. If successful, print the integer. If it raises a `ValueError`, print `Invalid input`.

Example:
```
Input:  abc
Output: Invalid input
```
MD,
                'starter_code'        => "s = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Read two numbers from input (one per line). Try to divide the first by the second. If the divisor is zero, print `Division by zero`. Otherwise, print the result rounded to 2 decimal places.

Example:
```
Input:
10
0
Output: Division by zero
```
MD,
                'starter_code'        => "a = float(input())\nb = float(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Read an integer `n` and a list index `i` (one per line). Try to access index `i` of the list `[10, 20, 30, 40, 50]`. If `IndexError` occurs, print `Index out of range`. Otherwise print the value.

Example:
```
Input:
7
Output: Index out of range
```
MD,
                'starter_code'        => "lst = [10, 20, 30, 40, 50]\ni = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Sorting & Searching (Q38–Q41)
            // ═══════════════════════════════════════════════════════════════

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Read `n` integers and print them sorted in **descending** order.

Example:
```
Input:
5
3
1
4
1
5
Output: [5, 4, 3, 1, 1]
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Read `n` words (one per line) and print them sorted by **length** (shortest first). If two words have the same length, sort them alphabetically.

Example:
```
Input:
4
banana
fig
apple
kiwi
Output: ['fig', 'kiwi', 'apple', 'banana']
```
MD,
                'starter_code'        => "n = int(input())\nwords = [input() for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Implement **binary search**. Read `n` sorted integers (one per line after `n`), then a target value. Print the **index** of the target if found, otherwise print `-1`.

Example:
```
Input:
5
1
3
5
7
9
5
Output: 2
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\ntarget = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Read `n` integers and print the **index of the minimum** value. If there are duplicates, print the index of the first occurrence.

Example:
```
Input:
5
4
2
7
2
9
Output: 1
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: Lambda, map & filter (Q42–Q44)
            // ═══════════════════════════════════════════════════════════════

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Read a line of space-separated integers. Use `map()` with a **lambda** to print each value multiplied by 3, space-separated.

Example:
```
Input:  1 2 3 4
Output: 3 6 9 12
```
MD,
                'starter_code'        => "nums = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Read a line of space-separated integers. Use `filter()` with a **lambda** to print only the values **greater than 10**, space-separated.

Example:
```
Input:  5 15 8 20 3
Output: 15 20
```
MD,
                'starter_code'        => "nums = list(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Read `n` words (one per line). Use `sorted()` with a **lambda key** to print them sorted by their **last character** alphabetically.

Example:
```
Input:
3
banana
cherry
apple
Output: ['apple', 'banana', 'cherry']
```
MD,
                'starter_code'        => "n = int(input())\nwords = [input() for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: Tuples (Q45–Q46)
            // ═══════════════════════════════════════════════════════════════

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Read three integers from input (one per line). Store them in a **tuple**, sort the tuple, and print it.

Example:
```
Input:
5
2
8
Output: (2, 5, 8)
```
MD,
                'starter_code'        => "a = int(input())\nb = int(input())\nc = int(input())\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Read a line of space-separated integers. Using **tuple unpacking**, print the first and last elements on separate lines.

Example:
```
Input:  10 20 30 40 50
Output:
10
50
```
MD,
                'starter_code'        => "nums = tuple(map(int, input().split()))\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 11: String Processing (Q47–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Read a sentence and print each word on a new line with its **character count** appended.

Format: `word (n)`

Example:
```
Input:  Hello World
Output:
Hello (5)
World (5)
```
MD,
                'starter_code'        => "s = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Read a string and **remove all duplicate characters** while preserving the order of first occurrence. Print the result.

Example:
```
Input:  programming
Output: progamin
```
MD,
                'starter_code'        => "s = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 200,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Read a string and print it with the **first and last characters swapped**. If the string has fewer than 2 characters, print it unchanged.

Example:
```
Input:  python
Output: nythoP
```
MD,
                'starter_code'        => "s = input()\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 150,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Read `n` strings (one per line). Print the **longest common prefix** shared by all of them. If there is no common prefix, print an empty string.

Example:
```
Input:
3
flower
flow
flight
Output: fl
```
MD,
                'starter_code'        => "n = int(input())\nwords = [input() for _ in range(n)]\n# Write your solution below\n",
                'time_limit_seconds'  => 900,
                'base_xp'             => 250,
            ],
        ];

        // ─────────────────────────────────────────────────────────────────
        // 3. INSERT QUESTIONS
        // ─────────────────────────────────────────────────────────────────

        $questionIds = [];

        foreach ($questionDefs as $qd) {
            $row = DB::table('coding_questions')->where([
                'challenge_id' => $challenge->id,
                'order_index'  => $qd['order_index'],
            ])->first();

            if (! $row) {
                $id = DB::table('coding_questions')->insertGetId([
                    'challenge_id'        => $challenge->id,
                    'order_index'         => $qd['order_index'],
                    'problem_description' => $qd['problem_description'],
                    'starter_code'        => $qd['starter_code'],
                    'time_limit_seconds'  => $qd['time_limit_seconds'],
                    'base_xp'             => $qd['base_xp'],
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
            } else {
                $id = $row->id;
            }

            $questionIds[$qd['order_index']] = $id;
        }

        $this->command->info('Questions inserted/verified. Seeding test cases...');

        // ─────────────────────────────────────────────────────────────────
        // 4. TEST CASES (4 per question)
        // ─────────────────────────────────────────────────────────────────

        $seed = function (int $orderIndex, array $cases) use ($questionIds): void {
            $qid = $questionIds[$orderIndex] ?? null;
            if (! $qid) return;

            foreach ($cases as $c) {
                $exists = DB::table('test_cases')->where([
                    'coding_question_id' => $qid,
                    'order_index'        => $c['order_index'],
                ])->exists();

                if (! $exists) {
                    DB::table('test_cases')->insert([
                        'coding_question_id' => $qid,
                        'input'              => $c['input'],
                        'expected_output'    => $c['expected_output'],
                        'is_hidden'          => $c['is_hidden'],
                        'order_index'        => $c['order_index'],
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ]);
                }
            }
        };

        // ── Q1: Reverse string ────────────────────────────────────────────
        $seed(1, [
            ['input' => 'hello',   'expected_output' => 'olleh',   'is_hidden' => false, 'order_index' => 1],
            ['input' => 'python',  'expected_output' => 'nohtyp',  'is_hidden' => false, 'order_index' => 2],
            ['input' => 'abcde',   'expected_output' => 'edcba',   'is_hidden' => true,  'order_index' => 3],
            ['input' => 'a',       'expected_output' => 'a',       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: Every second character ────────────────────────────────────
        $seed(2, [
            ['input' => 'abcdef',   'expected_output' => 'ace',    'is_hidden' => false, 'order_index' => 1],
            ['input' => 'python',   'expected_output' => 'pto',    'is_hidden' => false, 'order_index' => 2],
            ['input' => '12345678', 'expected_output' => '1357',   'is_hidden' => true,  'order_index' => 3],
            ['input' => 'abcd',     'expected_output' => 'ac',     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: Title case ────────────────────────────────────────────────
        $seed(3, [
            ['input' => 'the quick brown fox',    'expected_output' => 'The Quick Brown Fox',    'is_hidden' => false, 'order_index' => 1],
            ['input' => 'hello world',            'expected_output' => 'Hello World',            'is_hidden' => false, 'order_index' => 2],
            ['input' => 'i love python',          'expected_output' => 'I Love Python',          'is_hidden' => true,  'order_index' => 3],
            ['input' => 'open ai is awesome',     'expected_output' => 'Open Ai Is Awesome',     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: f-string name and age ─────────────────────────────────────
        $seed(4, [
            ['input' => "Alice\n30",  'expected_output' => 'My name is Alice and I am 30 years old.',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "Bob\n25",    'expected_output' => 'My name is Bob and I am 25 years old.',    'is_hidden' => false, 'order_index' => 2],
            ['input' => "Maria\n22",  'expected_output' => 'My name is Maria and I am 22 years old.',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "John\n40",   'expected_output' => 'My name is John and I am 40 years old.',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: Palindrome ────────────────────────────────────────────────
        $seed(5, [
            ['input' => 'Racecar',  'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 1],
            ['input' => 'hello',    'expected_output' => 'False', 'is_hidden' => false, 'order_index' => 2],
            ['input' => 'Madam',    'expected_output' => 'True',  'is_hidden' => true,  'order_index' => 3],
            ['input' => 'python',   'expected_output' => 'False', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: Count vowels ──────────────────────────────────────────────
        $seed(6, [
            ['input' => 'Hello World',    'expected_output' => '3',  'is_hidden' => false, 'order_index' => 1],
            ['input' => 'python',         'expected_output' => '1',  'is_hidden' => false, 'order_index' => 2],
            ['input' => 'aeiou',          'expected_output' => '5',  'is_hidden' => true,  'order_index' => 3],
            ['input' => 'rhythm',         'expected_output' => '0',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: Sort ascending ────────────────────────────────────────────
        $seed(7, [
            ['input' => "4\n3\n1\n4\n2",    'expected_output' => '[1, 2, 3, 4]',       'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n9\n5\n7",        'expected_output' => '[5, 7, 9]',          'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n10\n1\n5\n3\n7", 'expected_output' => '[1, 3, 5, 7, 10]',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n2\n2",           'expected_output' => '[2, 2]',             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: Filter evens ──────────────────────────────────────────────
        $seed(8, [
            ['input' => "5\n1\n2\n3\n4\n5",    'expected_output' => '[2, 4]',        'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n10\n15\n20\n25",   'expected_output' => '[10, 20]',      'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n7\n9\n11",          'expected_output' => '[]',            'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n2\n4\n6",           'expected_output' => '[2, 4, 6]',    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: Square list comprehension ─────────────────────────────────
        $seed(9, [
            ['input' => "3\n2\n3\n4",    'expected_output' => '[4, 9, 16]',    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n5\n6",       'expected_output' => '[25, 36]',      'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1\n2\n3\n4", 'expected_output' => '[1, 4, 9, 16]', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n10",         'expected_output' => '[100]',         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: Average ──────────────────────────────────────────────────
        $seed(10, [
            ['input' => "4\n10\n20\n30\n40",  'expected_output' => '25.00', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n5\n15",           'expected_output' => '10.00', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n2\n3",         'expected_output' => '2.00',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n0\n0\n0\n0\n10",  'expected_output' => '2.00',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: Second largest ───────────────────────────────────────────
        $seed(11, [
            ['input' => "5\n4\n1\n3\n2\n5",   'expected_output' => '4',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n10\n20\n30",       'expected_output' => '20', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n7\n7\n3\n5",       'expected_output' => '5',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n100\n99",          'expected_output' => '99', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: Remove duplicates & sort ─────────────────────────────────
        $seed(12, [
            ['input' => "6\n3\n1\n2\n3\n1\n4",  'expected_output' => '[1, 2, 3, 4]',    'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n5\n5\n5\n5",         'expected_output' => '[5]',             'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n9\n7\n5\n3\n1",      'expected_output' => '[1, 3, 5, 7, 9]', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n4\n4\n2",            'expected_output' => '[2, 4]',          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: Rotate left ──────────────────────────────────────────────
        $seed(13, [
            ['input' => "4\n1\n2\n3\n4",   'expected_output' => '[2, 3, 4, 1]',    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n6\n7",      'expected_output' => '[6, 7, 5]',       'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1\n2\n3\n4\n5",'expected_output' => '[2, 3, 4, 5, 1]', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n9",            'expected_output' => '[9]',             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: Word frequency ───────────────────────────────────────────
        $seed(14, [
            ['input' => 'the cat sat on the mat',  'expected_output' => "cat: 1\nmat: 1\non: 1\nsat: 1\nthe: 2",  'is_hidden' => false, 'order_index' => 1],
            ['input' => 'go go go',                'expected_output' => "go: 3",                                    'is_hidden' => false, 'order_index' => 2],
            ['input' => 'a b a b c',               'expected_output' => "a: 2\nb: 2\nc: 1",                        'is_hidden' => true,  'order_index' => 3],
            ['input' => 'one two three',           'expected_output' => "one: 1\nthree: 1\ntwo: 1",                'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: Dict lookup ──────────────────────────────────────────────
        $seed(15, [
            ['input' => "3\nname Alice\nage 30\ncity Manila\nage",    'expected_output' => '30',       'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\ncolor blue\nsize large\ncolor",           'expected_output' => 'blue',     'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\nfoo bar\nbaz",                            'expected_output' => 'Not found','is_hidden' => true,  'order_index' => 3],
            ['input' => "2\nx 10\ny 20\ny",                          'expected_output' => '20',       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: Intersection ─────────────────────────────────────────────
        $seed(16, [
            ['input' => "1 2 3 4 5\n3 4 5 6 7",  'expected_output' => '[3, 4, 5]',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2 3\n4 5 6",           'expected_output' => '[]',         'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 2 3 4\n3 4 5 6",       'expected_output' => '[3, 4]',     'is_hidden' => true,  'order_index' => 3],
            ['input' => "10 20 30\n20 30 40",      'expected_output' => '[20, 30]',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: Union ────────────────────────────────────────────────────
        $seed(17, [
            ['input' => "1 2 3\n3 4 5",    'expected_output' => '[1, 2, 3, 4, 5]',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2\n3 4",        'expected_output' => '[1, 2, 3, 4]',     'is_hidden' => false, 'order_index' => 2],
            ['input' => "5 6\n5 7",        'expected_output' => '[5, 6, 7]',        'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n1",            'expected_output' => '[1]',              'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: Most frequent character ─────────────────────────────────
        $seed(18, [
            ['input' => 'hello world',  'expected_output' => 'l',  'is_hidden' => false, 'order_index' => 1],
            ['input' => 'aabbcc',       'expected_output' => 'a',  'is_hidden' => false, 'order_index' => 2],
            ['input' => 'mississippi',  'expected_output' => 'i',  'is_hidden' => true,  'order_index' => 3],
            ['input' => 'abcabc',       'expected_output' => 'a',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: Word-length dict ─────────────────────────────────────────
        $seed(19, [
            ['input' => 'cat elephant bat',  'expected_output' => "{'bat': 3, 'cat': 3, 'elephant': 8}",  'is_hidden' => false, 'order_index' => 1],
            ['input' => 'hi hello',          'expected_output' => "{'hello': 5, 'hi': 2}",               'is_hidden' => false, 'order_index' => 2],
            ['input' => 'a bb ccc',          'expected_output' => "{'a': 1, 'bb': 2, 'ccc': 3}",         'is_hidden' => true,  'order_index' => 3],
            ['input' => 'python java c',     'expected_output' => "{'c': 1, 'java': 4, 'python': 6}",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: is_prime ─────────────────────────────────────────────────
        $seed(20, [
            ['input' => '7',   'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '10',  'expected_output' => 'False', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '2',   'expected_output' => 'True',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '1',   'expected_output' => 'False', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: celsius_to_fahrenheit ────────────────────────────────────
        $seed(21, [
            ['input' => '100',  'expected_output' => '212.00',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '0',    'expected_output' => '32.00',   'is_hidden' => false, 'order_index' => 2],
            ['input' => '37',   'expected_output' => '98.60',   'is_hidden' => true,  'order_index' => 3],
            ['input' => '-40',  'expected_output' => '-40.00',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: count_words ──────────────────────────────────────────────
        $seed(22, [
            ['input' => 'The quick brown fox',  'expected_output' => '4',  'is_hidden' => false, 'order_index' => 1],
            ['input' => 'hello',                'expected_output' => '1',  'is_hidden' => false, 'order_index' => 2],
            ['input' => 'one two three four',   'expected_output' => '4',  'is_hidden' => true,  'order_index' => 3],
            ['input' => 'a b c d e',            'expected_output' => '5',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: gcd ──────────────────────────────────────────────────────
        $seed(23, [
            ['input' => "48\n18",  'expected_output' => '6',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "12\n8",   'expected_output' => '4',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "100\n25", 'expected_output' => '25',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "17\n5",   'expected_output' => '1',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: power (no ** or pow) ─────────────────────────────────────
        $seed(24, [
            ['input' => "2\n10",  'expected_output' => '1024',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n3",   'expected_output' => '27',    'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n4",   'expected_output' => '625',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n100", 'expected_output' => '1',     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: flatten ──────────────────────────────────────────────────
        $seed(25, [
            ['input' => "3\n1 2 3\n4 5\n6",     'expected_output' => '[1, 2, 3, 4, 5, 6]',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 2\n3 4",           'expected_output' => '[1, 2, 3, 4]',        'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n10 20 30",           'expected_output' => '[10, 20, 30]',        'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n0\n0\n0",            'expected_output' => '[0, 0, 0]',           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: recursive factorial ──────────────────────────────────────
        $seed(26, [
            ['input' => '6',  'expected_output' => '720',   'is_hidden' => false, 'order_index' => 1],
            ['input' => '0',  'expected_output' => '1',     'is_hidden' => false, 'order_index' => 2],
            ['input' => '5',  'expected_output' => '120',   'is_hidden' => true,  'order_index' => 3],
            ['input' => '10', 'expected_output' => '3628800','is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q27: recursive fibonacci ──────────────────────────────────────
        $seed(27, [
            ['input' => '7',  'expected_output' => '13',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '0',  'expected_output' => '0',   'is_hidden' => false, 'order_index' => 2],
            ['input' => '1',  'expected_output' => '1',   'is_hidden' => true,  'order_index' => 3],
            ['input' => '10', 'expected_output' => '55',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: recursive sum_digits ─────────────────────────────────────
        $seed(28, [
            ['input' => '493',  'expected_output' => '16',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '0',    'expected_output' => '0',   'is_hidden' => false, 'order_index' => 2],
            ['input' => '999',  'expected_output' => '27',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '12345','expected_output' => '15',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: recursive reverse_string ────────────────────────────────
        $seed(29, [
            ['input' => 'python',   'expected_output' => 'nohtyp',  'is_hidden' => false, 'order_index' => 1],
            ['input' => 'hello',    'expected_output' => 'olleh',   'is_hidden' => false, 'order_index' => 2],
            ['input' => 'abcde',    'expected_output' => 'edcba',   'is_hidden' => true,  'order_index' => 3],
            ['input' => 'a',        'expected_output' => 'a',       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: recursive power ──────────────────────────────────────────
        $seed(30, [
            ['input' => "3\n4",   'expected_output' => '81',    'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n8",   'expected_output' => '256',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n0",   'expected_output' => '1',     'is_hidden' => true,  'order_index' => 3],
            ['input' => "10\n3",  'expected_output' => '1000',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: Star triangle ────────────────────────────────────────────
        $seed(31, [
            ['input' => '4', 'expected_output' => "*\n**\n***\n****",               'is_hidden' => false, 'order_index' => 1],
            ['input' => '2', 'expected_output' => "*\n**",                          'is_hidden' => false, 'order_index' => 2],
            ['input' => '5', 'expected_output' => "*\n**\n***\n****\n*****",        'is_hidden' => true,  'order_index' => 3],
            ['input' => '1', 'expected_output' => "*",                              'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: Multiplication table (n×n) ───────────────────────────────
        $seed(32, [
            ['input' => '3', 'expected_output' => "1 2 3\n2 4 6\n3 6 9",                                             'is_hidden' => false, 'order_index' => 1],
            ['input' => '2', 'expected_output' => "1 2\n2 4",                                                        'is_hidden' => false, 'order_index' => 2],
            ['input' => '4', 'expected_output' => "1 2 3 4\n2 4 6 8\n3 6 9 12\n4 8 12 16",                          'is_hidden' => true,  'order_index' => 3],
            ['input' => '1', 'expected_output' => "1",                                                               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: Number pyramid ───────────────────────────────────────────
        $seed(33, [
            ['input' => '4', 'expected_output' => "1\n1 2\n1 2 3\n1 2 3 4",                'is_hidden' => false, 'order_index' => 1],
            ['input' => '2', 'expected_output' => "1\n1 2",                                 'is_hidden' => false, 'order_index' => 2],
            ['input' => '5', 'expected_output' => "1\n1 2\n1 2 3\n1 2 3 4\n1 2 3 4 5",    'is_hidden' => true,  'order_index' => 3],
            ['input' => '1', 'expected_output' => "1",                                      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: Primes up to n ───────────────────────────────────────────
        $seed(34, [
            ['input' => '10',  'expected_output' => "2\n3\n5\n7",             'is_hidden' => false, 'order_index' => 1],
            ['input' => '20',  'expected_output' => "2\n3\n5\n7\n11\n13\n17\n19", 'is_hidden' => false, 'order_index' => 2],
            ['input' => '5',   'expected_output' => "2\n3\n5",               'is_hidden' => true,  'order_index' => 3],
            ['input' => '2',   'expected_output' => "2",                     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: try/except int conversion ───────────────────────────────
        $seed(35, [
            ['input' => 'abc',  'expected_output' => 'Invalid input',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '42',   'expected_output' => '42',             'is_hidden' => false, 'order_index' => 2],
            ['input' => '3.5',  'expected_output' => 'Invalid input',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '-7',   'expected_output' => '-7',             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: safe division ────────────────────────────────────────────
        $seed(36, [
            ['input' => "10\n0",   'expected_output' => 'Division by zero',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "10\n4",   'expected_output' => '2.50',              'is_hidden' => false, 'order_index' => 2],
            ['input' => "7\n2",    'expected_output' => '3.50',              'is_hidden' => true,  'order_index' => 3],
            ['input' => "0\n5",    'expected_output' => '0.00',              'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: index access ─────────────────────────────────────────────
        $seed(37, [
            ['input' => '7',   'expected_output' => 'Index out of range',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '2',   'expected_output' => '30',                  'is_hidden' => false, 'order_index' => 2],
            ['input' => '0',   'expected_output' => '10',                  'is_hidden' => true,  'order_index' => 3],
            ['input' => '-1',  'expected_output' => '50',                  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: Sort descending ──────────────────────────────────────────
        $seed(38, [
            ['input' => "5\n3\n1\n4\n1\n5",    'expected_output' => '[5, 4, 3, 1, 1]',    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n7\n2\n9",           'expected_output' => '[9, 7, 2]',          'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n10\n5\n15\n0",      'expected_output' => '[15, 10, 5, 0]',     'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1\n2",              'expected_output' => '[2, 1]',             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: Sort by length ───────────────────────────────────────────
        $seed(39, [
            ['input' => "4\nbanana\nfig\napple\nkiwi",  'expected_output' => "['fig', 'kiwi', 'apple', 'banana']",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\ncat\ndog\nelephant",         'expected_output' => "['cat', 'dog', 'elephant']",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nhello\nhi",                  'expected_output' => "['hi', 'hello']",                    'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\naaa\nbbb\nccc",              'expected_output' => "['aaa', 'bbb', 'ccc']",              'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: Binary search ────────────────────────────────────────────
        $seed(40, [
            ['input' => "5\n1\n3\n5\n7\n9\n5",   'expected_output' => '2',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n2\n4\n6\n8\n6",      'expected_output' => '2',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n10\n20\n30\n15",      'expected_output' => '-1',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n1\n3\n5\n7\n9\n1",   'expected_output' => '0',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: Index of minimum ─────────────────────────────────────────
        $seed(41, [
            ['input' => "5\n4\n2\n7\n2\n9",  'expected_output' => '1',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n3\n8",        'expected_output' => '1',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n9\n8\n7\n6",     'expected_output' => '3',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1\n1",           'expected_output' => '0',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: map + lambda ×3 ──────────────────────────────────────────
        $seed(42, [
            ['input' => '1 2 3 4',     'expected_output' => '3 6 9 12',     'is_hidden' => false, 'order_index' => 1],
            ['input' => '5 10 15',     'expected_output' => '15 30 45',     'is_hidden' => false, 'order_index' => 2],
            ['input' => '0 1 2 3 4',   'expected_output' => '0 3 6 9 12',   'is_hidden' => true,  'order_index' => 3],
            ['input' => '100',         'expected_output' => '300',          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: filter + lambda >10 ──────────────────────────────────────
        $seed(43, [
            ['input' => '5 15 8 20 3',    'expected_output' => '15 20',     'is_hidden' => false, 'order_index' => 1],
            ['input' => '1 2 3',          'expected_output' => '',          'is_hidden' => false, 'order_index' => 2],
            ['input' => '10 11 12',       'expected_output' => '11 12',     'is_hidden' => true,  'order_index' => 3],
            ['input' => '100 200 5',      'expected_output' => '100 200',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: sort by last char ────────────────────────────────────────
        $seed(44, [
            ['input' => "3\nbanana\ncherry\napple",     'expected_output' => "['apple', 'banana', 'cherry']",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\ncat\ndog\nbat",             'expected_output' => "['bat', 'cat', 'dog']",              'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nzero\none",                 'expected_output' => "['one', 'zero']",                    'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\nabc\ndef\nghi\njkl",        'expected_output' => "['abc', 'def', 'ghi', 'jkl']",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: Tuple sort ───────────────────────────────────────────────
        $seed(45, [
            ['input' => "5\n2\n8",    'expected_output' => '(2, 5, 8)',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "9\n1\n5",    'expected_output' => '(1, 5, 9)',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n3\n3",    'expected_output' => '(3, 3, 3)',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "10\n0\n5",   'expected_output' => '(0, 5, 10)',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: Tuple unpack first/last ──────────────────────────────────
        $seed(46, [
            ['input' => '10 20 30 40 50',  'expected_output' => "10\n50",   'is_hidden' => false, 'order_index' => 1],
            ['input' => '1 2 3',           'expected_output' => "1\n3",     'is_hidden' => false, 'order_index' => 2],
            ['input' => '99 100',          'expected_output' => "99\n100",  'is_hidden' => true,  'order_index' => 3],
            ['input' => '5 6 7 8 9',       'expected_output' => "5\n9",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: Word length annotation ───────────────────────────────────
        $seed(47, [
            ['input' => 'Hello World',       'expected_output' => "Hello (5)\nWorld (5)",          'is_hidden' => false, 'order_index' => 1],
            ['input' => 'I love Python',     'expected_output' => "I (1)\nlove (4)\nPython (6)",   'is_hidden' => false, 'order_index' => 2],
            ['input' => 'hi',                'expected_output' => "hi (2)",                        'is_hidden' => true,  'order_index' => 3],
            ['input' => 'a bb ccc',          'expected_output' => "a (1)\nbb (2)\nccc (3)",        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: Remove duplicate characters ─────────────────────────────
        $seed(48, [
            ['input' => 'programming',  'expected_output' => 'progamin',   'is_hidden' => false, 'order_index' => 1],
            ['input' => 'hello',        'expected_output' => 'helo',       'is_hidden' => false, 'order_index' => 2],
            ['input' => 'aabbcc',       'expected_output' => 'abc',        'is_hidden' => true,  'order_index' => 3],
            ['input' => 'python',       'expected_output' => 'python',     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: Swap first and last characters ───────────────────────────
        $seed(49, [
            ['input' => 'python',   'expected_output' => 'nythoP',   'is_hidden' => false, 'order_index' => 1],
            ['input' => 'hello',    'expected_output' => 'oellh',    'is_hidden' => false, 'order_index' => 2],
            ['input' => 'a',        'expected_output' => 'a',        'is_hidden' => true,  'order_index' => 3],
            ['input' => 'ab',       'expected_output' => 'ba',       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: Longest common prefix ────────────────────────────────────
        $seed(50, [
            ['input' => "3\nflower\nflow\nflight",      'expected_output' => 'fl',     'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\ndog\nracecar\ncar",         'expected_output' => '',       'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\ninterest\ninterface",       'expected_output' => 'inter',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\nabc\nabc\nabc",             'expected_output' => 'abc',    'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 1 Coding (Intermediate) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}