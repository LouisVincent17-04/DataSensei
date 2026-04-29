<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 1 — Basics of Python Programming (UniversityStudent) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the UniversityStudent tier
 *   2. coding_questions    — 50 questions covering intermediate–advanced Python
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered:
 *   - Functions & scope
 *   - Recursion
 *   - List comprehensions
 *   - Dictionaries & sets
 *   - Tuples
 *   - String formatting & methods
 *   - Exception handling
 *   - Object-oriented programming (classes)
 *   - File I/O simulation (via stdin/stdout)
 *   - Sorting & searching
 *   - Lambda & higher-order functions (map, filter, sorted)
 *   - 2D lists / matrices
 *   - Stack & queue simulation
 *   - Basic algorithms (GCD, prime check, Fibonacci)
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module1CodingChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────
        // 1. CHALLENGE
        // ─────────────────────────────────────────────────────────────────

        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (! $category) {
            $this->command->error('UniversityStudent category not found! Run ChallengeCategorySeeder first.');
            return;
        }

        $this->command->info('Creating Module 1 — Basics of Python Programming (UniversityStudent) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Intermediate Python Programming',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Solve problems that require a solid grasp of Python — functions, recursion, OOP, data structures, exception handling, and common algorithms. Each task is concise but demands careful thinking.',
                'time_limit_seconds' => 1800,
                'base_xp'            => 1500,
                'order_index'        => 1,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 1: Functions & Scope (Q1–Q5)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Write a function `celsius_to_fahrenheit(c)` that converts Celsius to Fahrenheit using the formula `F = (C × 9/5) + 32`.

Read a float from input and print the result rounded to 2 decimal places.

Example:
```
Input:  100
Output: 212.0
```
MD,
                'starter_code'        => "def celsius_to_fahrenheit(c):\n    pass\n\nc = float(input())\nprint(celsius_to_fahrenheit(c))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Write a function `is_palindrome(s)` that returns `True` if the string `s` is a palindrome (reads the same forwards and backwards), ignoring case. Otherwise return `False`.

Read a string from input.

Example:
```
Input:  Racecar
Output: True
```
MD,
                'starter_code'        => "def is_palindrome(s):\n    pass\n\ns = input()\nprint(is_palindrome(s))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Write a function `count_vowels(s)` that returns the number of vowels (`a, e, i, o, u`, case-insensitive) in the string `s`.

Read a string from input and print the count.

Example:
```
Input:  Hello World
Output: 3
```
MD,
                'starter_code'        => "def count_vowels(s):\n    pass\n\ns = input()\nprint(count_vowels(s))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Write a function `clamp(value, min_val, max_val)` that returns `value` if it is within `[min_val, max_val]`, returns `min_val` if it is too low, or `max_val` if it is too high.

Read three integers from input: `value`, `min_val`, `max_val` (one per line).

Example:
```
Input:
15
0
10
Output: 10
```
MD,
                'starter_code'        => "def clamp(value, min_val, max_val):\n    pass\n\nvalue = int(input())\nmin_val = int(input())\nmax_val = int(input())\nprint(clamp(value, min_val, max_val))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Write a function `apply_twice(f, x)` that applies function `f` to `x` twice (i.e., returns `f(f(x))`).

Use it with a doubling function. Read an integer from input and print the result.

Example:
```
Input:  3
Output: 12
```
(3 doubled = 6, 6 doubled = 12)
MD,
                'starter_code'        => "def apply_twice(f, x):\n    pass\n\ndouble = lambda x: x * 2\nn = int(input())\nprint(apply_twice(double, n))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: Recursion (Q6–Q10)
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Write a recursive function `factorial(n)` that returns `n!`.

Read a non-negative integer from input.

Example:
```
Input:  6
Output: 720
```
MD,
                'starter_code'        => "def factorial(n):\n    pass\n\nn = int(input())\nprint(factorial(n))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Write a recursive function `fibonacci(n)` that returns the nth Fibonacci number (0-indexed).

`fibonacci(0) = 0`, `fibonacci(1) = 1`, `fibonacci(n) = fibonacci(n-1) + fibonacci(n-2)`

Example:
```
Input:  7
Output: 13
```
MD,
                'starter_code'        => "def fibonacci(n):\n    pass\n\nn = int(input())\nprint(fibonacci(n))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Write a recursive function `sum_digits(n)` that returns the sum of all digits of the positive integer `n`.

Example:
```
Input:  1234
Output: 10
```
MD,
                'starter_code'        => "def sum_digits(n):\n    pass\n\nn = int(input())\nprint(sum_digits(n))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Write a recursive function `power(base, exp)` that returns `base` raised to the power `exp` without using the `**` operator or `pow()`.

Assume `exp >= 0`.

Example:
```
Input:
2
10
Output: 1024
```
MD,
                'starter_code'        => "def power(base, exp):\n    pass\n\nbase = int(input())\nexp = int(input())\nprint(power(base, exp))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Write a recursive function `gcd(a, b)` that returns the greatest common divisor of `a` and `b` using the Euclidean algorithm.

`gcd(a, 0) = a` and `gcd(a, b) = gcd(b, a % b)`

Example:
```
Input:
48
18
Output: 6
```
MD,
                'starter_code'        => "def gcd(a, b):\n    pass\n\na = int(input())\nb = int(input())\nprint(gcd(a, b))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: List Comprehensions (Q11–Q14)
            // ═══════════════════════════════════════════════════════════════

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Read `n` integers from input (one per line) and use a list comprehension to produce a new list containing only the even numbers. Print the result.

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
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Use a list comprehension\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Read `n` integers from input (one per line) and use a list comprehension to produce a list of their squares. Print the result.

Example:
```
Input:
4
1
2
3
4
Output: [1, 4, 9, 16]
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Use a list comprehension\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Read a sentence from input and use a list comprehension to produce a list of word lengths. Print the result.

Example:
```
Input:  Hello World Python
Output: [5, 5, 6]
```
MD,
                'starter_code'        => "sentence = input()\n# Use a list comprehension on sentence.split()\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Use a list comprehension to generate a list of all multiples of 3 from 3 to `n` (inclusive).

Read `n` from input and print the result.

Example:
```
Input:  15
Output: [3, 6, 9, 12, 15]
```
MD,
                'starter_code'        => "n = int(input())\n# Use a list comprehension with range()\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Dictionaries & Sets (Q15–Q19)
            // ═══════════════════════════════════════════════════════════════

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Read a sentence from input. Count the frequency of each word and print each word with its count, sorted alphabetically.

Format: `word: count`

Example:
```
Input:  the cat sat on the mat the cat
Output:
cat: 2
mat: 1
on: 1
sat: 1
the: 3
```
MD,
                'starter_code'        => "sentence = input()\n# Use a dictionary\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Read `n` integers from input (one per line). Print `True` if there are any duplicates, otherwise print `False`. Use a set.

Example:
```
Input:
5
1
2
3
2
4
Output: True
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Use a set\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Read two lines of integers separated by spaces. Print the elements that appear in both lines (intersection), sorted in ascending order.

Example:
```
Input:
1 2 3 4 5
3 4 5 6 7
Output: [3, 4, 5]
```
MD,
                'starter_code'        => "a = set(map(int, input().split()))\nb = set(map(int, input().split()))\n# Find intersection\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Read `n` key-value pairs from input (one pair per line, key and value separated by a space). Store them in a dictionary.

Then read a key and print its value, or `Key not found` if it doesn't exist.

Example:
```
Input:
3
name Alice
age 20
city Cebu
age
Output: 20
```
MD,
                'starter_code'        => "n = int(input())\nd = {}\nfor _ in range(n):\n    k, v = input().split()\n    d[k] = v\nkey = input()\n# Print value or 'Key not found'\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Read a string from input and count the frequency of each character. Print each character and its count sorted alphabetically, ignoring spaces.

Format: `char: count`

Example:
```
Input:  hello
Output:
e: 1
h: 1
l: 2
o: 1
```
MD,
                'starter_code'        => "s = input()\n# Use a dictionary\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Tuples (Q20–Q21)
            // ═══════════════════════════════════════════════════════════════

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Read `n` pairs of integers from input (two integers per line). Store each pair as a tuple in a list. Print the list sorted by the second element of each tuple.

Example:
```
Input:
3
3 1
1 3
2 2
Output: [(3, 1), (2, 2), (1, 3)]
```
MD,
                'starter_code'        => "n = int(input())\npairs = [tuple(map(int, input().split())) for _ in range(n)]\n# Sort by second element\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Write a function `min_max(nums)` that returns a tuple `(minimum, maximum)` of the list.

Read `n` integers from input (one per line) and print the result.

Example:
```
Input:
5
3
1
4
1
5
Output: (1, 5)
```
MD,
                'starter_code'        => "def min_max(nums):\n    pass\n\nn = int(input())\nnums = [int(input()) for _ in range(n)]\nprint(min_max(nums))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: String Formatting & Methods (Q22–Q25)
            // ═══════════════════════════════════════════════════════════════

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Read a name and a score (float) from input (one per line). Print a formatted report card line:

`Name: <name> | Score: <score> | Grade: <grade>`

Use the grading scale:
- 90–100 → A
- 80–89  → B
- 70–79  → C
- 60–69  → D
- Below 60 → F

Example:
```
Input:
Alice
88.5
Output: Name: Alice | Score: 88.5 | Grade: B
```
MD,
                'starter_code'        => "name = input()\nscore = float(input())\n# Determine grade and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Read a string from input. Print the string in title case (first letter of each word capitalized).

Example:
```
Input:  hello world from python
Output: Hello World From Python
```
MD,
                'starter_code'        => "s = input()\n# Write your solution\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Read a string from input. Remove all leading and trailing whitespace, replace all multiple spaces between words with a single space, and print the cleaned string.

Example:
```
Input:  "  hello   world  "
Output: "hello world"
```
MD,
                'starter_code'        => "s = input()\n# Clean the string\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Read a template string and a name from input (one per line). Replace every occurrence of `{name}` in the template with the actual name and print the result.

Example:
```
Input:
Hello, {name}! Welcome, {name}.
Alice
Output: Hello, Alice! Welcome, Alice.
```
MD,
                'starter_code'        => "template = input()\nname = input()\n# Replace and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: Exception Handling (Q26–Q28)
            // ═══════════════════════════════════════════════════════════════

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Read two values from input (one per line). Try to divide the first by the second.

- If the second is `0`, print `Error: Division by zero`.
- If either value is not a number, print `Error: Invalid input`.
- Otherwise, print the result as a float rounded to 2 decimal places.

Example:
```
Input:
10
0
Output: Error: Division by zero
```
MD,
                'starter_code'        => "a = input()\nb = input()\n# Use try/except\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Read a list of space-separated integers from input. Read an index from the next line.

Try to print the element at that index. If the index is out of range, print `Error: Index out of range`.

Example:
```
Input:
1 2 3 4 5
10
Output: Error: Index out of range
```
MD,
                'starter_code'        => "nums = list(map(int, input().split()))\nidx = int(input())\n# Use try/except\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Read a string from input. Try to convert it to an integer.

- If successful, print `Integer: <value>`.
- If not, try to convert it to a float.
- If successful, print `Float: <value>`.
- If neither works, print `Not a number`.

Example:
```
Input:  3.14
Output: Float: 3.14
```
MD,
                'starter_code'        => "s = input()\n# Use nested try/except\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Object-Oriented Programming (Q29–Q34)
            // ═══════════════════════════════════════════════════════════════

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Define a class `Rectangle` with:
- `__init__(self, width, height)`
- `area(self)` — returns `width * height`
- `perimeter(self)` — returns `2 * (width + height)`

Read `width` and `height` from input (one per line). Print the area then the perimeter, each on its own line.

Example:
```
Input:
4
5
Output:
20
18
```
MD,
                'starter_code'        => "class Rectangle:\n    pass\n\nw = int(input())\nh = int(input())\nr = Rectangle(w, h)\nprint(r.area())\nprint(r.perimeter())\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Define a class `BankAccount` with:
- `__init__(self, balance=0)`
- `deposit(self, amount)` — adds to balance
- `withdraw(self, amount)` — subtracts from balance; print `Insufficient funds` and do nothing if amount > balance
- `get_balance(self)` — returns current balance

Read a series of commands from input until `END`:
- `deposit <amount>`
- `withdraw <amount>`
- `balance`

Example:
```
Input:
deposit 100
withdraw 30
balance
END
Output: 70
```
MD,
                'starter_code'        => "class BankAccount:\n    pass\n\nacc = BankAccount()\nwhile True:\n    line = input()\n    if line == 'END':\n        break\n    parts = line.split()\n    # Handle commands\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Define a class `Student` with:
- `__init__(self, name, grades)` — `grades` is a list of floats
- `average(self)` — returns the average of grades
- `__str__(self)` — returns `"<name>: <average>"` rounded to 2 decimal places

Read a name, then `n`, then `n` grades from input (one per line). Print the student.

Example:
```
Input:
Alice
3
90
85
92
Output: Alice: 89.0
```
MD,
                'starter_code'        => "class Student:\n    pass\n\nname = input()\nn = int(input())\ngrades = [float(input()) for _ in range(n)]\ns = Student(name, grades)\nprint(s)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Define a base class `Animal` with:
- `__init__(self, name)`
- `speak(self)` — returns `"..."`

Define two subclasses:
- `Dog` — `speak()` returns `"Woof!"`
- `Cat` — `speak()` returns `"Meow!"`

Read `n` lines from input. Each line is either `Dog <name>` or `Cat <name>`. For each animal, print `<name> says <speak()>`.

Example:
```
Input:
2
Dog Rex
Cat Luna
Output:
Rex says Woof!
Luna says Meow!
```
MD,
                'starter_code'        => "class Animal:\n    pass\n\nclass Dog(Animal):\n    pass\n\nclass Cat(Animal):\n    pass\n\nn = int(input())\nfor _ in range(n):\n    line = input().split()\n    kind, name = line[0], line[1]\n    # Create and speak\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Define a class `Stack` with:
- `push(self, item)`
- `pop(self)` — returns and removes the top item; print `Stack is empty` if empty
- `peek(self)` — returns the top item without removing; print `Stack is empty` if empty
- `is_empty(self)` — returns `True` or `False`

Read commands from input until `END`:
- `push <value>`
- `pop`
- `peek`

Print output only for `pop` and `peek`.

Example:
```
Input:
push 1
push 2
peek
pop
pop
pop
END
Output:
2
2
1
Stack is empty
```
MD,
                'starter_code'        => "class Stack:\n    pass\n\ns = Stack()\nwhile True:\n    line = input()\n    if line == 'END':\n        break\n    parts = line.split()\n    # Handle commands\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Define a class `Counter` with:
- `__init__(self, start=0)`
- `increment(self)` — adds 1
- `decrement(self)` — subtracts 1
- `reset(self)` — sets count back to 0
- `value(self)` — returns the current count

Read commands from input until `END`. For each `value` command, print the count.

Example:
```
Input:
increment
increment
increment
decrement
value
reset
value
END
Output:
2
0
```
MD,
                'starter_code'        => "class Counter:\n    pass\n\nc = Counter()\nwhile True:\n    cmd = input()\n    if cmd == 'END':\n        break\n    # Handle commands\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: Sorting & Searching (Q35–Q38)
            // ═══════════════════════════════════════════════════════════════

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Read `n` integers from input (one per line) and implement **bubble sort** to sort them in ascending order. Print the sorted list.

Do not use Python's built-in `sort()` or `sorted()`.

Example:
```
Input:
5
5
3
1
4
2
Output: [1, 2, 3, 4, 5]
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Implement bubble sort\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Read `n` integers (one per line) into a sorted list, then read a target integer. Implement **binary search** to find the index of the target. Print the index, or `-1` if not found.

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
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\ntarget = int(input())\n# Implement binary search\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Read `n` words from input (one per line). Sort them by length (shortest first). If two words have the same length, sort them alphabetically. Print the sorted list.

Example:
```
Input:
5
banana
apple
fig
date
kiwi
Output: ['fig', 'date', 'kiwi', 'apple', 'banana']
```
MD,
                'starter_code'        => "n = int(input())\nwords = [input() for _ in range(n)]\n# Sort with a key\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Read `n` integers from input (one per line). Find and print the **second largest** unique value. If there is no second largest, print `None`.

Example:
```
Input:
5
3
1
4
1
5
Output: 4
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Find the second largest unique value\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: Lambda & Higher-Order Functions (Q39–Q42)
            // ═══════════════════════════════════════════════════════════════

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Read `n` integers from input (one per line). Use `map()` with a lambda to compute the square of each. Print the resulting list.

Example:
```
Input:
4
1
2
3
4
Output: [1, 4, 9, 16]
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Use map() and lambda\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Read `n` integers from input (one per line). Use `filter()` with a lambda to keep only those greater than 10. Print the resulting list.

Example:
```
Input:
6
5
15
3
20
8
12
Output: [15, 20, 12]
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Use filter() and lambda\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Read `n` words from input (one per line). Use `sorted()` with a `key` lambda to sort them by length in descending order. Print the result.

Example:
```
Input:
4
cat
elephant
dog
ox
Output: ['elephant', 'cat', 'dog', 'ox']
```
MD,
                'starter_code'        => "n = int(input())\nwords = [input() for _ in range(n)]\n# Use sorted() with a key\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
Read `n` integers from input (one per line). Use `map()` to convert all to strings, then join them with `" - "` and print the result.

Example:
```
Input:
4
1
2
3
4
Output: 1 - 2 - 3 - 4
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Use map() and join()\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 11: 2D Lists / Matrices (Q43–Q45)
            // ═══════════════════════════════════════════════════════════════

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Read an `n x n` matrix from input (each row on one line, space-separated). Print its transpose (rows become columns).

Example:
```
Input:
3
1 2 3
4 5 6
7 8 9
Output:
1 4 7
2 5 8
3 6 9
```
MD,
                'starter_code'        => "n = int(input())\nmatrix = [list(map(int, input().split())) for _ in range(n)]\n# Transpose and print\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Read an `n x n` matrix and print the sum of the main diagonal (top-left to bottom-right).

Example:
```
Input:
3
1 2 3
4 5 6
7 8 9
Output: 15
```
MD,
                'starter_code'        => "n = int(input())\nmatrix = [list(map(int, input().split())) for _ in range(n)]\n# Sum the diagonal\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Read an `m x n` matrix from input. Print the row with the highest sum. If there is a tie, print the first one.

Each row is a space-separated line of integers. Read `m` and `n` first.

Example:
```
Input:
3
3
1 2 3
9 1 1
4 5 6
Output: 4 5 6
```
MD,
                'starter_code'        => "m = int(input())\nn = int(input())\nmatrix = [list(map(int, input().split())) for _ in range(m)]\n# Find row with max sum\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 12: Queue Simulation (Q46–Q47)
            // ═══════════════════════════════════════════════════════════════

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Simulate a queue using a list. Read commands from input until `END`:
- `enqueue <value>` — add to the back
- `dequeue` — remove from the front and print the value; print `Queue is empty` if empty
- `front` — print the front value without removing; print `Queue is empty` if empty

Example:
```
Input:
enqueue 1
enqueue 2
front
dequeue
dequeue
dequeue
END
Output:
1
1
2
Queue is empty
```
MD,
                'starter_code'        => "from collections import deque\nq = deque()\nwhile True:\n    line = input()\n    if line == 'END':\n        break\n    parts = line.split()\n    # Handle commands\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Use a stack to check if a string of brackets is balanced. Valid bracket pairs: `()`, `[]`, `{}`.

Read a string from input. Print `Balanced` if all brackets are properly closed and nested, otherwise print `Not Balanced`.

Example:
```
Input:  ({[]})
Output: Balanced
```
MD,
                'starter_code'        => "s = input()\n# Use a stack (list)\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 13: Basic Algorithms (Q48–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Read an integer `n` from input. Print all prime numbers from 2 to `n` (inclusive), one per line.

Example:
```
Input:  20
Output:
2
3
5
7
11
13
17
19
```
MD,
                'starter_code'        => "n = int(input())\n# Print primes from 2 to n\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Read `n` integers from input (one per line). Use a dictionary to group them as `even` and `odd`. Print the two groups sorted in ascending order.

Format:
```
even: [...]
odd: [...]
```

Example:
```
Input:
6
1
2
3
4
5
6
Output:
even: [2, 4, 6]
odd: [1, 3, 5]
```
MD,
                'starter_code'        => "n = int(input())\nnums = [int(input()) for _ in range(n)]\n# Group into even and odd\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Write a function `flatten(nested)` that takes a list of lists and returns a single flat list containing all elements in order.

Read `n` rows from input. Each row starts with `k` (number of elements), followed by `k` space-separated integers. Print the flat list.

Example:
```
Input:
3
3 1 2 3
2 4 5
1 6
Output: [1, 2, 3, 4, 5, 6]
```
MD,
                'starter_code'        => "def flatten(nested):\n    pass\n\nn = int(input())\nnested = []\nfor _ in range(n):\n    row = list(map(int, input().split()))\n    nested.append(row[1:])  # skip k\nprint(flatten(nested))\n",
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
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

        // ── Q1: Celsius to Fahrenheit ─────────────────────────────────────
        $seed(1, [
            ['input' => '100',   'expected_output' => '212.0',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '0',     'expected_output' => '32.0',   'is_hidden' => false, 'order_index' => 2],
            ['input' => '37',    'expected_output' => '98.6',   'is_hidden' => true,  'order_index' => 3],
            ['input' => '-40',   'expected_output' => '-40.0',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: Palindrome ────────────────────────────────────────────────
        $seed(2, [
            ['input' => 'Racecar',  'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 1],
            ['input' => 'hello',    'expected_output' => 'False', 'is_hidden' => false, 'order_index' => 2],
            ['input' => 'Madam',    'expected_output' => 'True',  'is_hidden' => true,  'order_index' => 3],
            ['input' => 'Python',   'expected_output' => 'False', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: Count vowels ──────────────────────────────────────────────
        $seed(3, [
            ['input' => 'Hello World',  'expected_output' => '3', 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'Python',       'expected_output' => '2', 'is_hidden' => false, 'order_index' => 2],
            ['input' => 'aeiou',        'expected_output' => '5', 'is_hidden' => true,  'order_index' => 3],
            ['input' => 'rhythm',       'expected_output' => '0', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: Clamp ─────────────────────────────────────────────────────
        $seed(4, [
            ['input' => "15\n0\n10",  'expected_output' => '10', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n0\n10",   'expected_output' => '5',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "-5\n0\n10",  'expected_output' => '0',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "10\n10\n10", 'expected_output' => '10', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: Apply twice ───────────────────────────────────────────────
        $seed(5, [
            ['input' => '3',   'expected_output' => '12',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '1',   'expected_output' => '4',   'is_hidden' => false, 'order_index' => 2],
            ['input' => '5',   'expected_output' => '20',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '10',  'expected_output' => '40',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: Factorial (recursive) ─────────────────────────────────────
        $seed(6, [
            ['input' => '6',  'expected_output' => '720',    'is_hidden' => false, 'order_index' => 1],
            ['input' => '0',  'expected_output' => '1',      'is_hidden' => false, 'order_index' => 2],
            ['input' => '5',  'expected_output' => '120',    'is_hidden' => true,  'order_index' => 3],
            ['input' => '10', 'expected_output' => '3628800','is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: Fibonacci ─────────────────────────────────────────────────
        $seed(7, [
            ['input' => '7',  'expected_output' => '13', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '0',  'expected_output' => '0',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '1',  'expected_output' => '1',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '10', 'expected_output' => '55', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: Sum of digits ─────────────────────────────────────────────
        $seed(8, [
            ['input' => '1234',  'expected_output' => '10', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '9',     'expected_output' => '9',  'is_hidden' => false, 'order_index' => 2],
            ['input' => '999',   'expected_output' => '27', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '100',   'expected_output' => '1',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: Recursive power ───────────────────────────────────────────
        $seed(9, [
            ['input' => "2\n10",  'expected_output' => '1024', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n0",   'expected_output' => '1',    'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n4",   'expected_output' => '81',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "7\n2",   'expected_output' => '49',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: GCD ──────────────────────────────────────────────────────
        $seed(10, [
            ['input' => "48\n18", 'expected_output' => '6',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "100\n25",'expected_output' => '25', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "7\n5",   'expected_output' => '1',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "36\n12", 'expected_output' => '12', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: Filter evens ─────────────────────────────────────────────
        $seed(11, [
            ['input' => "5\n1\n2\n3\n4\n5",   'expected_output' => '[2, 4]',       'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n3\n5\n7",       'expected_output' => '[]',           'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n2\n4\n6\n8",       'expected_output' => '[2, 4, 6, 8]', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n10\n11\n12",       'expected_output' => '[10, 12]',     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: Squares comprehension ────────────────────────────────────
        $seed(12, [
            ['input' => "4\n1\n2\n3\n4",   'expected_output' => '[1, 4, 9, 16]',      'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n6\n7",      'expected_output' => '[25, 36, 49]',        'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n10\n0",        'expected_output' => '[100, 0]',            'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n9",            'expected_output' => '[81]',                'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: Word lengths ─────────────────────────────────────────────
        $seed(13, [
            ['input' => 'Hello World Python',  'expected_output' => '[5, 5, 6]',  'is_hidden' => false, 'order_index' => 1],
            ['input' => 'I love coding',        'expected_output' => '[1, 4, 6]',  'is_hidden' => false, 'order_index' => 2],
            ['input' => 'a bb ccc',             'expected_output' => '[1, 2, 3]',  'is_hidden' => true,  'order_index' => 3],
            ['input' => 'hi',                   'expected_output' => '[2]',        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: Multiples of 3 ───────────────────────────────────────────
        $seed(14, [
            ['input' => '15', 'expected_output' => '[3, 6, 9, 12, 15]', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '6',  'expected_output' => '[3, 6]',            'is_hidden' => false, 'order_index' => 2],
            ['input' => '2',  'expected_output' => '[]',                'is_hidden' => true,  'order_index' => 3],
            ['input' => '9',  'expected_output' => '[3, 6, 9]',         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: Word frequency ───────────────────────────────────────────
        $seed(15, [
            ['input' => 'the cat sat on the mat the cat',
             'expected_output' => "cat: 2\nmat: 1\non: 1\nsat: 1\nthe: 3", 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'a a b',
             'expected_output' => "a: 2\nb: 1",                            'is_hidden' => false, 'order_index' => 2],
            ['input' => 'hello world hello',
             'expected_output' => "hello: 2\nworld: 1",                    'is_hidden' => true,  'order_index' => 3],
            ['input' => 'x',
             'expected_output' => 'x: 1',                                  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: Duplicates check ─────────────────────────────────────────
        $seed(16, [
            ['input' => "5\n1\n2\n3\n2\n4",   'expected_output' => 'True',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1\n2\n3\n4",       'expected_output' => 'False', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n5\n5\n5",          'expected_output' => 'True',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n99",               'expected_output' => 'False', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: Set intersection ─────────────────────────────────────────
        $seed(17, [
            ['input' => "1 2 3 4 5\n3 4 5 6 7",  'expected_output' => '[3, 4, 5]', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2 3\n4 5 6",           'expected_output' => '[]',        'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 2 3\n1 2 3",           'expected_output' => '[1, 2, 3]', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "10 20 30\n20 30 40",     'expected_output' => '[20, 30]',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: Dictionary lookup ────────────────────────────────────────
        $seed(18, [
            ['input' => "3\nname Alice\nage 20\ncity Cebu\nage",    'expected_output' => '20',            'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\ncolor blue\nsize large\nweight",        'expected_output' => 'Key not found', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\nx 42\nx",                               'expected_output' => '42',            'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\nfoo bar\nbaz qux\nbaz",                 'expected_output' => 'qux',           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: Char frequency ───────────────────────────────────────────
        $seed(19, [
            ['input' => 'hello',
             'expected_output' => "e: 1\nh: 1\nl: 2\no: 1", 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'aab',
             'expected_output' => "a: 2\nb: 1",             'is_hidden' => false, 'order_index' => 2],
            ['input' => 'abc',
             'expected_output' => "a: 1\nb: 1\nc: 1",       'is_hidden' => true,  'order_index' => 3],
            ['input' => 'zzz',
             'expected_output' => 'z: 3',                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: Tuples sort by second ────────────────────────────────────
        $seed(20, [
            ['input' => "3\n3 1\n1 3\n2 2",   'expected_output' => '[(3, 1), (2, 2), (1, 3)]', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n5 2\n3 1",        'expected_output' => '[(3, 1), (5, 2)]',          'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n4 4",             'expected_output' => '[(4, 4)]',                  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 9\n2 3\n3 6",  'expected_output' => '[(2, 3), (3, 6), (1, 9)]',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: Min max tuple ────────────────────────────────────────────
        $seed(21, [
            ['input' => "5\n3\n1\n4\n1\n5",   'expected_output' => '(1, 5)',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n7\n7\n7",          'expected_output' => '(7, 7)',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n42",               'expected_output' => '(42, 42)', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n10\n20\n30\n40",   'expected_output' => '(10, 40)', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: Report card ──────────────────────────────────────────────
        $seed(22, [
            ['input' => "Alice\n88.5",  'expected_output' => 'Name: Alice | Score: 88.5 | Grade: B', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "Bob\n55.0",    'expected_output' => 'Name: Bob | Score: 55.0 | Grade: F',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "Carol\n95.0",  'expected_output' => 'Name: Carol | Score: 95.0 | Grade: A', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "Dave\n72.0",   'expected_output' => 'Name: Dave | Score: 72.0 | Grade: C',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: Title case ───────────────────────────────────────────────
        $seed(23, [
            ['input' => 'hello world from python',  'expected_output' => 'Hello World From Python', 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'good morning',             'expected_output' => 'Good Morning',            'is_hidden' => false, 'order_index' => 2],
            ['input' => 'one',                      'expected_output' => 'One',                     'is_hidden' => true,  'order_index' => 3],
            ['input' => 'python is great',          'expected_output' => 'Python Is Great',         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: Clean string ─────────────────────────────────────────────
        $seed(24, [
            ['input' => '  hello   world  ',    'expected_output' => 'hello world',       'is_hidden' => false, 'order_index' => 1],
            ['input' => 'no  extra  spaces',    'expected_output' => 'no extra spaces',   'is_hidden' => false, 'order_index' => 2],
            ['input' => '   a   b   c   ',      'expected_output' => 'a b c',             'is_hidden' => true,  'order_index' => 3],
            ['input' => 'clean',                'expected_output' => 'clean',             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: Template replace ─────────────────────────────────────────
        $seed(25, [
            ['input' => "Hello, {name}! Welcome, {name}.\nAlice",  'expected_output' => 'Hello, Alice! Welcome, Alice.',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "Hi {name}\nBob",                          'expected_output' => 'Hi Bob',                         'is_hidden' => false, 'order_index' => 2],
            ['input' => "{name} loves {name}\nCarol",              'expected_output' => 'Carol loves Carol',               'is_hidden' => true,  'order_index' => 3],
            ['input' => "No placeholder\nAnyone",                  'expected_output' => 'No placeholder',                  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: Safe division ────────────────────────────────────────────
        $seed(26, [
            ['input' => "10\n0",    'expected_output' => 'Error: Division by zero', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "10\n4",    'expected_output' => '2.5',                     'is_hidden' => false, 'order_index' => 2],
            ['input' => "abc\n2",   'expected_output' => 'Error: Invalid input',    'is_hidden' => true,  'order_index' => 3],
            ['input' => "9\n3",     'expected_output' => '3.0',                     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: Index out of range ───────────────────────────────────────
        $seed(27, [
            ['input' => "1 2 3 4 5\n10",  'expected_output' => 'Error: Index out of range', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2 3 4 5\n2",   'expected_output' => '3',                         'is_hidden' => false, 'order_index' => 2],
            ['input' => "10 20 30\n0",    'expected_output' => '10',                        'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n99",          'expected_output' => 'Error: Index out of range', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: Type detection ───────────────────────────────────────────
        $seed(28, [
            ['input' => '3.14',   'expected_output' => 'Float: 3.14',      'is_hidden' => false, 'order_index' => 1],
            ['input' => '42',     'expected_output' => 'Integer: 42',      'is_hidden' => false, 'order_index' => 2],
            ['input' => 'hello',  'expected_output' => 'Not a number',     'is_hidden' => true,  'order_index' => 3],
            ['input' => '0',      'expected_output' => 'Integer: 0',       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: Rectangle class ──────────────────────────────────────────
        $seed(29, [
            ['input' => "4\n5",    'expected_output' => "20\n18", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n3",    'expected_output' => "9\n12",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "10\n2",   'expected_output' => "20\n24", 'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n1",    'expected_output' => "1\n4",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: BankAccount class ────────────────────────────────────────
        $seed(30, [
            ['input' => "deposit 100\nwithdraw 30\nbalance\nEND",         'expected_output' => '70',                    'is_hidden' => false, 'order_index' => 1],
            ['input' => "deposit 50\nwithdraw 100\nbalance\nEND",         'expected_output' => "Insufficient funds\n50",'is_hidden' => false, 'order_index' => 2],
            ['input' => "deposit 200\nwithdraw 200\nbalance\nEND",        'expected_output' => '0',                     'is_hidden' => true,  'order_index' => 3],
            ['input' => "deposit 10\ndeposit 20\nwithdraw 5\nbalance\nEND",'expected_output' => '25',                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: Student class ────────────────────────────────────────────
        $seed(31, [
            ['input' => "Alice\n3\n90\n85\n92",     'expected_output' => 'Alice: 89.0',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "Bob\n2\n70\n80",           'expected_output' => 'Bob: 75.0',    'is_hidden' => false, 'order_index' => 2],
            ['input' => "Carol\n1\n100",            'expected_output' => 'Carol: 100.0', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "Dave\n4\n60\n70\n80\n90",  'expected_output' => 'Dave: 75.0',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: Animal polymorphism ──────────────────────────────────────
        $seed(32, [
            ['input' => "2\nDog Rex\nCat Luna",      'expected_output' => "Rex says Woof!\nLuna says Meow!",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\nDog Buddy",              'expected_output' => 'Buddy says Woof!',                 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nCat Mochi\nCat Socks",   'expected_output' => "Mochi says Meow!\nSocks says Meow!",'is_hidden' => true, 'order_index' => 3],
            ['input' => "3\nDog A\nDog B\nCat C",    'expected_output' => "A says Woof!\nB says Woof!\nC says Meow!",'is_hidden' => true,'order_index' => 4],
        ]);

        // ── Q33: Stack class ──────────────────────────────────────────────
        $seed(33, [
            ['input' => "push 1\npush 2\npeek\npop\npop\npop\nEND",
             'expected_output' => "2\n2\n1\nStack is empty",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "pop\nEND",
             'expected_output' => 'Stack is empty',            'is_hidden' => false, 'order_index' => 2],
            ['input' => "push 5\npush 10\npop\npeek\nEND",
             'expected_output' => "10\n5",                     'is_hidden' => true,  'order_index' => 3],
            ['input' => "push 1\npeek\npop\npeek\nEND",
             'expected_output' => "1\n1\nStack is empty",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: Counter class ────────────────────────────────────────────
        $seed(34, [
            ['input' => "increment\nincrement\nincrement\ndecrement\nvalue\nreset\nvalue\nEND",
             'expected_output' => "2\n0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "value\nEND",
             'expected_output' => '0',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "increment\nvalue\nEND",
             'expected_output' => '1',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "increment\nincrement\ndecrement\ndecrement\nvalue\nEND",
             'expected_output' => '0',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: Bubble sort ──────────────────────────────────────────────
        $seed(35, [
            ['input' => "5\n5\n3\n1\n4\n2",   'expected_output' => '[1, 2, 3, 4, 5]', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n3\n2\n1",          'expected_output' => '[1, 2, 3]',       'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n42",               'expected_output' => '[42]',            'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n4\n4\n4\n4",       'expected_output' => '[4, 4, 4, 4]',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: Binary search ────────────────────────────────────────────
        $seed(36, [
            ['input' => "5\n1\n3\n5\n7\n9\n5",  'expected_output' => '2',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n1\n3\n5\n7\n9\n6",  'expected_output' => '-1', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n2\n4\n6\n2",         'expected_output' => '0',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n1\n2\n3\n4\n4",      'expected_output' => '3',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: Sort by length then alpha ────────────────────────────────
        $seed(37, [
            ['input' => "5\nbanana\napple\nfig\ndate\nkiwi",
             'expected_output' => "['fig', 'date', 'kiwi', 'apple', 'banana']", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\ncat\ndog\nox",
             'expected_output' => "['ox', 'cat', 'dog']",                        'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nhello\nhi",
             'expected_output' => "['hi', 'hello']",                             'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nonly",
             'expected_output' => "['only']",                                    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: Second largest ───────────────────────────────────────────
        $seed(38, [
            ['input' => "5\n3\n1\n4\n1\n5",   'expected_output' => '4',    'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n1\n1",          'expected_output' => 'None', 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n5\n10",            'expected_output' => '5',    'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n9\n8\n7\n6",       'expected_output' => '8',    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: map() squares ────────────────────────────────────────────
        $seed(39, [
            ['input' => "4\n1\n2\n3\n4",    'expected_output' => '[1, 4, 9, 16]',   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n5\n6\n7",       'expected_output' => '[25, 36, 49]',     'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n0\n10",         'expected_output' => '[0, 100]',         'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n3",             'expected_output' => '[9]',              'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: filter() > 10 ────────────────────────────────────────────
        $seed(40, [
            ['input' => "6\n5\n15\n3\n20\n8\n12",  'expected_output' => '[15, 20, 12]', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3",              'expected_output' => '[]',            'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n11\n12\n13\n14",       'expected_output' => '[11, 12, 13, 14]','is_hidden' => true,'order_index' => 3],
            ['input' => "2\n10\n11",               'expected_output' => '[11]',          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: sorted() by length desc ─────────────────────────────────
        $seed(41, [
            ['input' => "4\ncat\nelephant\ndog\nox",  'expected_output' => "['elephant', 'cat', 'dog', 'ox']", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\na\nbb\nccc",              'expected_output' => "['ccc', 'bb', 'a']",               'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nhello\nhi",               'expected_output' => "['hello', 'hi']",                  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nonly",                    'expected_output' => "['only']",                         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: map() join with " - " ────────────────────────────────────
        $seed(42, [
            ['input' => "4\n1\n2\n3\n4",    'expected_output' => '1 - 2 - 3 - 4',     'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n10\n20",        'expected_output' => '10 - 20',            'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n5",             'expected_output' => '5',                  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n7\n8\n9",       'expected_output' => '7 - 8 - 9',         'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: Transpose matrix ─────────────────────────────────────────
        $seed(43, [
            ['input' => "3\n1 2 3\n4 5 6\n7 8 9",  'expected_output' => "1 4 7\n2 5 8\n3 6 9", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 2\n3 4",             'expected_output' => "1 3\n2 4",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n5",                    'expected_output' => '5',                    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n9 8\n7 6",             'expected_output' => "9 7\n8 6",             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: Diagonal sum ─────────────────────────────────────────────
        $seed(44, [
            ['input' => "3\n1 2 3\n4 5 6\n7 8 9",  'expected_output' => '15', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n1 0\n0 1",             'expected_output' => '2',  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n7",                    'expected_output' => '7',  'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n5 0 0\n0 5 0\n0 0 5", 'expected_output' => '15', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: Row with highest sum ─────────────────────────────────────
        $seed(45, [
            ['input' => "3\n3\n1 2 3\n9 1 1\n4 5 6",   'expected_output' => '4 5 6',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n2\n1 2\n3 4",              'expected_output' => '3 4',     'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n3\n5 10 15",              'expected_output' => '5 10 15', 'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n2\n1 1\n2 2\n3 3",        'expected_output' => '3 3',     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: Queue simulation ─────────────────────────────────────────
        $seed(46, [
            ['input' => "enqueue 1\nenqueue 2\nfront\ndequeue\ndequeue\ndequeue\nEND",
             'expected_output' => "1\n1\n2\nQueue is empty",  'is_hidden' => false, 'order_index' => 1],
            ['input' => "dequeue\nEND",
             'expected_output' => 'Queue is empty',           'is_hidden' => false, 'order_index' => 2],
            ['input' => "enqueue 5\nenqueue 10\ndequeue\nfront\nEND",
             'expected_output' => "5\n10",                    'is_hidden' => true,  'order_index' => 3],
            ['input' => "front\nEND",
             'expected_output' => 'Queue is empty',           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: Balanced brackets ────────────────────────────────────────
        $seed(47, [
            ['input' => '({[]})',     'expected_output' => 'Balanced',     'is_hidden' => false, 'order_index' => 1],
            ['input' => '([)]',       'expected_output' => 'Not Balanced', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '{[()]}',     'expected_output' => 'Balanced',     'is_hidden' => true,  'order_index' => 3],
            ['input' => '(((',        'expected_output' => 'Not Balanced', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: Sieve of Eratosthenes (primes) ───────────────────────────
        $seed(48, [
            ['input' => '20',
             'expected_output' => "2\n3\n5\n7\n11\n13\n17\n19", 'is_hidden' => false, 'order_index' => 1],
            ['input' => '10',
             'expected_output' => "2\n3\n5\n7",                 'is_hidden' => false, 'order_index' => 2],
            ['input' => '2',
             'expected_output' => '2',                          'is_hidden' => true,  'order_index' => 3],
            ['input' => '30',
             'expected_output' => "2\n3\n5\n7\n11\n13\n17\n19\n23\n29", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q49: Group even and odd ───────────────────────────────────────
        $seed(49, [
            ['input' => "6\n1\n2\n3\n4\n5\n6",   'expected_output' => "even: [2, 4, 6]\nodd: [1, 3, 5]", 'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n2\n4\n6",            'expected_output' => "even: [2, 4, 6]\nodd: []",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1\n3\n5",            'expected_output' => "even: []\nodd: [1, 3, 5]",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n10\n11\n12\n13",     'expected_output' => "even: [10, 12]\nodd: [11, 13]",  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: Flatten nested list ──────────────────────────────────────
        $seed(50, [
            ['input' => "3\n3 1 2 3\n2 4 5\n1 6",    'expected_output' => '[1, 2, 3, 4, 5, 6]', 'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\n2 10 20\n2 30 40",        'expected_output' => '[10, 20, 30, 40]',   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n3 7 8 9",                 'expected_output' => '[7, 8, 9]',          'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 1\n1 2\n1 3",           'expected_output' => '[1, 2, 3]',          'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 1 Coding (UniversityStudent) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}