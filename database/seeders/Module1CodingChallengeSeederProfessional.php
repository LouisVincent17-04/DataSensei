<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 1 — Basics of Python Programming (Professional) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Professional tier
 *   2. coding_questions    — 50 questions covering intermediate-to-advanced Python
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered:
 *   - OOP (classes, dunder methods, properties)
 *   - File-like abstractions & context managers
 *   - Comprehensions & generator expressions
 *   - Recursion & memoization (functools.lru_cache)
 *   - Sorting & searching (built-ins + custom key)
 *   - String manipulation & formatting
 *   - Error handling (custom exceptions)
 *   - Collections (defaultdict, Counter, deque)
 *   - Functional tools (map, filter, zip, enumerate)
 *   - Basic algorithms (binary search, two-pointer, sliding window)
 *   - Data transformation (nested dicts, JSON-like structures)
 *   - Itertools & combinatorics
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module1CodingChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────
        // 1. CHALLENGE
        // ─────────────────────────────────────────────────────────────────

        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (! $category) {
            $this->command->error('Professional category not found! Run ChallengeCategorySeeder first.');
            return;
        }

        $this->command->info('Creating Module 1 — Basics of Python Programming (Professional) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Basics of Python Programming',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Solve intermediate Python challenges spanning OOP, recursion, comprehensions, collections, custom exceptions, functional tools, and classic algorithms. Problems expect clean, idiomatic Python and solid understanding of the standard library.',
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
            // TOPIC 1: OOP & Dunder Methods (Q1–Q8)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
Define a class `Circle` with:
- `__init__(self, radius)`
- `area()` → returns π × radius² (use `math.pi`, round to 2 decimal places)
- `circumference()` → returns 2 × π × radius (round to 2 decimal places)
- `__str__` → `Circle(radius=R)`
- `__lt__(self, other)` → compares by area

Read `n` circles (one radius per line), sort them in ascending order by area, then print each one using `__str__`.

Example:
```
Input:
3
5
1
3
Output:
Circle(radius=1)
Circle(radius=3)
Circle(radius=5)
```
MD,
                'starter_code'        => "import math\n\nclass Circle:\n    pass\n\nn = int(input())\ncircles = [Circle(float(input())) for _ in range(n)]\ncircles.sort()\nfor c in circles:\n    print(c)\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
Define a class `Vector2D` with:
- `__init__(self, x, y)`
- `__add__(self, other)` → returns a new `Vector2D`
- `__mul__(self, scalar)` → scalar multiplication, returns new `Vector2D`
- `magnitude()` → returns the Euclidean length (round to 4 decimal places)
- `__str__` → `Vector2D(x=X, y=Y)`

Read two vectors (each as two floats on one line) and a scalar. Print:
1. Their sum
2. The first vector multiplied by the scalar
3. The magnitude of the first vector

Example:
```
Input:
3.0 4.0
1.0 2.0
2
Output:
Vector2D(x=4.0, y=6.0)
Vector2D(x=6.0, y=8.0)
5.0
```
MD,
                'starter_code'        => "import math\n\nclass Vector2D:\n    pass\n\na = Vector2D(*map(float, input().split()))\nb = Vector2D(*map(float, input().split()))\nscalar = int(input())\nprint(a + b)\nprint(a * scalar)\nprint(a.magnitude())\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Define a class `Fraction` with:
- `__init__(self, numerator, denominator)` — auto-reduce using GCD; raise `ValueError` if denominator is 0
- `__add__(self, other)` → returns reduced `Fraction`
- `__str__` → `N/D`
- `__eq__(self, other)` → compares reduced fractions

Read two fractions (each as `N D` on one line). Print their sum.

Example:
```
Input:
1 2
1 3
Output:
5/6
```
MD,
                'starter_code'        => "from math import gcd\n\nclass Fraction:\n    pass\n\na = Fraction(*map(int, input().split()))\nb = Fraction(*map(int, input().split()))\nprint(a + b)\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Define a class `Temperature` with:
- `__init__(self, celsius)`
- Property `fahrenheit` → returns Celsius × 9/5 + 32 (read-only)
- Property `kelvin` → returns Celsius + 273.15 (read-only)
- `__str__` → `T°C`

Read a temperature in Celsius. Print the string representation, then fahrenheit (2 decimal places), then kelvin (2 decimal places).

Example:
```
Input: 100
Output:
100°C
212.00
373.15
```
MD,
                'starter_code'        => "class Temperature:\n    pass\n\nt = Temperature(float(input()))\nprint(t)\nprint(f'{t.fahrenheit:.2f}')\nprint(f'{t.kelvin:.2f}')\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Define a class `Person` with:
- `__init__(self, name, age)`
- `__repr__` → `Person('name', age)`
- `__eq__` → equal if same name and age
- `__hash__` → hash based on name and age (so Person objects can be used in sets)

Read `n` person records (each `name age` on one line). Print the number of **unique** people.

Example:
```
Input:
4
Alice 30
Bob 25
Alice 30
Charlie 40
Output: 3
```
MD,
                'starter_code'        => "class Person:\n    pass\n\nn = int(input())\npeople = set()\nfor _ in range(n):\n    name, age = input().split()\n    people.add(Person(name, int(age)))\nprint(len(people))\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
Define a class `ShoppingCart` with:
- `add(item, price, qty=1)` — adds or updates item
- `remove(item)` — removes item (do nothing if missing)
- `total()` → returns total price (sum of price × qty for each item)
- `__str__` → each item on its own line as `item: qty × price = subtotal`, lines sorted alphabetically, then a final `Total: X` line (all monetary values to 2 decimal places)

Read `n` commands: `add ITEM PRICE [QTY]`, `remove ITEM`. Print the cart.

Example:
```
Input:
4
add apple 1.50 3
add banana 0.75
remove banana
add milk 2.00 2
Output:
apple: 3 × 1.50 = 4.50
milk: 2 × 2.00 = 4.00
Total: 8.50
```
MD,
                'starter_code'        => "class ShoppingCart:\n    def __init__(self):\n        self.items = {}\n\n    # Implement add, remove, total, __str__\n\ncart = ShoppingCart()\nn = int(input())\nfor _ in range(n):\n    parts = input().split()\n    if parts[0] == 'add':\n        item, price = parts[1], float(parts[2])\n        qty = int(parts[3]) if len(parts) > 3 else 1\n        cart.add(item, price, qty)\n    else:\n        cart.remove(parts[1])\nprint(cart)\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Define a class `Student` with:
- `__init__(self, name, grades)` where `grades` is a list of floats
- `average()` → mean of grades (round to 2 decimal places)
- `highest()` / `lowest()` → max/min grade
- `__str__` → `name: avg=A, high=H, low=L`
- `__lt__` → sort by average descending (so the highest average comes first)

Read `n` students. First line of each student is the name; second is space-separated grades. Print students sorted by average (descending).

Example:
```
Input:
3
Alice
90 85 92
Bob
78 80 76
Carol
95 88 91
Output:
Carol: avg=91.33, high=95, low=88
Alice: avg=89.0, high=92, low=85
Bob: avg=78.0, high=80, low=76
```
MD,
                'starter_code'        => "class Student:\n    pass\n\nn = int(input())\nstudents = []\nfor _ in range(n):\n    name = input()\n    grades = list(map(float, input().split()))\n    students.append(Student(name, grades))\nstudents.sort()\nfor s in students:\n    print(s)\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Define a class `EventEmitter` with:
- `on(event, callback)` — registers a callback for an event
- `emit(event, *args)` — calls all registered callbacks for the event with the given args
- `off(event, callback)` — removes a specific callback

Read `n` commands:
- `on EVENT FUNC` — register (FUNC is a name; use a lookup dict)
- `off EVENT FUNC`
- `emit EVENT ARG`

Pre-define two functions: `shout(x)` prints `x.upper()` and `whisper(x)` prints `x.lower()`.

Example:
```
Input:
5
on greet shout
on greet whisper
emit greet Hello
off greet whisper
emit greet Hello
Output:
HELLO
hello
HELLO
```
MD,
                'starter_code'        => "class EventEmitter:\n    def __init__(self):\n        self._listeners = {}\n\n    # Implement on, off, emit\n\ndef shout(x): print(x.upper())\ndef whisper(x): print(x.lower())\nFUNCS = {'shout': shout, 'whisper': whisper}\n\nee = EventEmitter()\nn = int(input())\nfor _ in range(n):\n    parts = input().split()\n    cmd = parts[0]\n    if cmd == 'on':\n        ee.on(parts[1], FUNCS[parts[2]])\n    elif cmd == 'off':\n        ee.off(parts[1], FUNCS[parts[2]])\n    else:\n        ee.emit(parts[1], parts[2])\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: Recursion & Memoization (Q9–Q13)
            // ═══════════════════════════════════════════════════════════════

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Write a **recursive** function `power(base, exp)` that computes `base ** exp` without using the `**` operator or `pow()`. Use fast exponentiation (divide-and-conquer).

Read `base` and `exp` (both integers, exp ≥ 0). Print the result.

Example:
```
Input:
2
10
Output: 1024
```
MD,
                'starter_code'        => "def power(base, exp):\n    # Recursive fast exponentiation\n    pass\n\nbase = int(input())\nexp  = int(input())\nprint(power(base, exp))\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Write a **recursive** function `flatten(lst)` that takes a nested list (arbitrarily deep) and returns a flat list of all integers.

Read a line of the nested list in Python literal format (e.g. `[[1,2],[3,[4,5]]]`) and print the flattened list.

Example:
```
Input: [[1, 2], [3, [4, 5]], 6]
Output: [1, 2, 3, 4, 5, 6]
```
MD,
                'starter_code'        => "import ast\n\ndef flatten(lst):\n    # Recursive flatten\n    pass\n\nnested = ast.literal_eval(input())\nprint(flatten(nested))\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Use `functools.lru_cache` to memoize a recursive `catalan(n)` function.

The n-th Catalan number: `C(0) = 1`, `C(n) = Σ C(i)*C(n-1-i)` for i=0..n-1.

Read `n` and print `C(n)`.

Example:
```
Input: 5
Output: 42
```
MD,
                'starter_code'        => "from functools import lru_cache\n\n@lru_cache(maxsize=None)\ndef catalan(n):\n    # Implement with lru_cache\n    pass\n\nn = int(input())\nprint(catalan(n))\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Write a recursive function `count_paths(m, n)` that counts unique paths in an m×n grid moving only right or down from top-left to bottom-right.

Read `m` and `n`. Print the count.

Example:
```
Input:
3
3
Output: 6
```
MD,
                'starter_code'        => "from functools import lru_cache\n\n@lru_cache(maxsize=None)\ndef count_paths(m, n):\n    # Base case + recursion\n    pass\n\nm = int(input())\nn = int(input())\nprint(count_paths(m, n))\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Write a recursive function `tower_of_hanoi(n, source, target, auxiliary)` that prints the moves to solve the Tower of Hanoi puzzle for `n` disks. Each move is printed as `Move disk from X to Y`.

Read `n`. Print all moves.

Example:
```
Input: 2
Output:
Move disk from A to B
Move disk from A to C
Move disk from B to C
```
MD,
                'starter_code'        => "def tower_of_hanoi(n, source, target, auxiliary):\n    # Implement recursion\n    pass\n\nn = int(input())\ntower_of_hanoi(n, 'A', 'C', 'B')\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Comprehensions & Generators (Q14–Q18)
            // ═══════════════════════════════════════════════════════════════

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Using a **single list comprehension**, generate all prime numbers up to `n` (inclusive).

Read `n`. Print the primes space-separated on one line.

Example:
```
Input: 20
Output: 2 3 5 7 11 13 17 19
```
MD,
                'starter_code'        => "n = int(input())\n# One-liner list comprehension\nprimes = []\nprint(*primes)\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Write a **generator function** `prime_gen()` that yields prime numbers indefinitely.

Read `n` and print the first `n` primes, one per line.

Example:
```
Input: 5
Output:
2
3
5
7
11
```
MD,
                'starter_code'        => "def prime_gen():\n    # Infinite prime generator\n    pass\n\nn = int(input())\ngen = prime_gen()\nfor _ in range(n):\n    print(next(gen))\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Using a **dict comprehension**, read `n` key-value pairs and produce a new dictionary where values are squared.

Read `n`, then `n` lines each with `key value` (key is a string, value is an int). Print the resulting dict (Python repr, keys sorted).

Example:
```
Input:
3
a 3
b 4
c 5
Output: {'a': 9, 'b': 16, 'c': 25}
```
MD,
                'starter_code'        => "n = int(input())\npairs = [input().split() for _ in range(n)]\n# Dict comprehension with sorted keys\nresult = {}\nprint(result)\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 150,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Using **nested list comprehensions**, create a multiplication table of size n×n.

Read `n`. Print each row space-separated.

Example:
```
Input: 3
Output:
1 2 3
2 4 6
3 6 9
```
MD,
                'starter_code'        => "n = int(input())\ntable = []  # nested list comprehension\nfor row in table:\n    print(*row)\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 150,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Write a **generator expression** that lazily computes the squares of odd numbers from 1 to `n` (inclusive).

Read `n`. Print the values one per line.

Example:
```
Input: 10
Output:
1
9
25
49
81
```
MD,
                'starter_code'        => "n = int(input())\ngen = ()  # generator expression here\nfor v in gen:\n    print(v)\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Error Handling & Custom Exceptions (Q19–Q22)
            // ═══════════════════════════════════════════════════════════════

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Define a custom exception `NegativeValueError(ValueError)` with message `Value must be non-negative`.

Write a function `safe_sqrt(x)` that raises `NegativeValueError` for negative input, else returns the square root rounded to 4 decimal places.

Read `n` numbers. For each, print the result or the exception message.

Example:
```
Input:
3
16
-4
9
Output:
4.0
Value must be non-negative
3.0
```
MD,
                'starter_code'        => "import math\n\nclass NegativeValueError(ValueError):\n    pass\n\ndef safe_sqrt(x):\n    pass\n\nn = int(input())\nfor _ in range(n):\n    x = float(input())\n    try:\n        print(safe_sqrt(x))\n    except NegativeValueError as e:\n        print(e)\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Define a custom exception hierarchy:
- `AppError(Exception)` — base
- `ValidationError(AppError)` — raised when input is invalid
- `NotFoundError(AppError)` — raised when a key is missing

Write a function `lookup(data, key)` that raises `NotFoundError('Key not found: KEY')` if the key is absent.
Write a function `parse_int(s)` that raises `ValidationError('Not an integer: S')` if `s` is not a valid integer.

Read `n` commands: `lookup KEY` or `parse VALUE`. Maintain a dict populated by previous successful `parse` results keyed by their string form. Print results or error messages.

Example:
```
Input:
4
parse 42
lookup 42
lookup 99
parse hello
Output:
42
42
Key not found: 99
Not an integer: hello
```
MD,
                'starter_code'        => "class AppError(Exception): pass\nclass ValidationError(AppError): pass\nclass NotFoundError(AppError): pass\n\ndef parse_int(s):\n    pass\n\ndef lookup(data, key):\n    pass\n\ndata = {}\nn = int(input())\nfor _ in range(n):\n    parts = input().split()\n    cmd, val = parts[0], parts[1]\n    try:\n        if cmd == 'parse':\n            result = parse_int(val)\n            data[val] = result\n            print(result)\n        else:\n            print(lookup(data, val))\n    except AppError as e:\n        print(e)\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Write a function `safe_divide(a, b)` that raises `ZeroDivisionError` with message `Cannot divide by zero` if `b == 0`, else returns `a / b` rounded to 4 decimal places.

Use a `try / except / else / finally` block in the caller:
- `else`: print `Result: X`
- `except`: print the error message
- `finally`: always print `Done`

Read `n` pairs (a and b, each on its own line). Process each with the full block.

Example:
```
Input:
2
10
2
5
0
Output:
Result: 5.0
Done
Cannot divide by zero
Done
```
MD,
                'starter_code'        => "def safe_divide(a, b):\n    pass\n\nn = int(input())\nfor _ in range(n):\n    a = float(input())\n    b = float(input())\n    try:\n        result = safe_divide(a, b)\n    except ZeroDivisionError as e:\n        print(e)\n    else:\n        print(f'Result: {result}')\n    finally:\n        print('Done')\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Implement a context manager class `Timer` (using `__enter__` and `__exit__`) that measures the elapsed time of a block in milliseconds. On exit, print `Elapsed: X ms` where X is a non-negative float (rounded to 2 decimal places).

Use it to time a loop that sums 1..N.

Read `N`. Print the sum, then the timer output.

Example:
```
Input: 1000000
Output:
500000500000
Elapsed: X ms    ← any non-negative float
```

Note: The exact time value is not tested; any non-negative float is accepted.
MD,
                'starter_code'        => "import time\n\nclass Timer:\n    def __enter__(self):\n        # Record start time\n        pass\n\n    def __exit__(self, *args):\n        # Print elapsed time\n        pass\n\nn = int(input())\nwith Timer():\n    print(sum(range(1, n + 1)))\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Collections (Q23–Q27)
            // ═══════════════════════════════════════════════════════════════

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Using `collections.Counter`, read a sentence and print the **top k** most common words (case-insensitive, ignore punctuation) in descending order of frequency. For ties, sort alphabetically.

Read `k`, then the sentence on the next line. Print each word and count as `word: count`.

Example:
```
Input:
3
the quick brown fox jumps over the lazy dog the fox
Output:
the: 3
fox: 2
brown: 1
```
MD,
                'starter_code'        => "from collections import Counter\nimport re\n\nk = int(input())\nsentence = input()\n# Count words and print top-k\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Using `collections.defaultdict`, build an inverted index: given `n` lines each starting with a document ID followed by words, create a mapping from word → sorted list of document IDs that contain it.

Read `n`, then `n` lines. Print each word (sorted alphabetically) and its sorted doc-ID list.

Example:
```
Input:
2
doc1 python is great
doc2 python is fun
Output:
fun: ['doc2']
great: ['doc1']
is: ['doc1', 'doc2']
python: ['doc1', 'doc2']
```
MD,
                'starter_code'        => "from collections import defaultdict\n\nn = int(input())\nindex = defaultdict(set)\nfor _ in range(n):\n    parts = input().split()\n    doc_id = parts[0]\n    words = parts[1:]\n    # Build index\n\nfor word in sorted(index):\n    print(f'{word}: {sorted(index[word])}')\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
Implement a **sliding window maximum** using `collections.deque`. Given an array of integers and window size `k`, print the maximum value of each window.

Read `n`, then `n` integers on the next line, then `k`. Print the maxima space-separated.

Example:
```
Input:
8
1 3 -1 -3 5 3 6 7
3
Output: 3 3 5 5 6 7
```
MD,
                'starter_code'        => "from collections import deque\n\nn = int(input())\nnums = list(map(int, input().split()))\nk = int(input())\n# Sliding window max with deque\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
Using `collections.OrderedDict`, implement a **simple cache** that keeps the most recently accessed items. When an item is accessed via `get`, move it to the end. When capacity is exceeded on `put`, remove the oldest (least recently used) item.

Read `capacity`, then `n` commands (`get KEY` or `put KEY VALUE`). Print results for `get` (-1 if missing).

Example:
```
Input:
2
5
put a 1
put b 2
get a
put c 3
get b
Output:
1
-1
```
MD,
                'starter_code'        => "from collections import OrderedDict\n\ncapacity = int(input())\nn = int(input())\ncache = OrderedDict()\n\nfor _ in range(n):\n    parts = input().split()\n    if parts[0] == 'get':\n        key = parts[1]\n        if key in cache:\n            cache.move_to_end(key)\n            print(cache[key])\n        else:\n            print(-1)\n    else:\n        key, val = parts[1], parts[2]\n        # Implement put\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Using `collections.namedtuple`, define a `Point3D` with fields `x`, `y`, `z`.

Write a function `distance(p1, p2)` → Euclidean distance between two 3D points (round to 4 decimal places).

Read `n` pairs of points (each point as `x y z` on one line). Print the distance for each pair.

Example:
```
Input:
2
0 0 0
1 1 1
1 2 3
4 5 6
Output:
1.7321
5.196
```
MD,
                'starter_code'        => "from collections import namedtuple\nimport math\n\nPoint3D = namedtuple('Point3D', ['x', 'y', 'z'])\n\ndef distance(p1, p2):\n    pass\n\nn = int(input())\nfor _ in range(n):\n    p1 = Point3D(*map(float, input().split()))\n    p2 = Point3D(*map(float, input().split()))\n    print(distance(p1, p2))\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: Functional Tools (Q28–Q32)
            // ═══════════════════════════════════════════════════════════════

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
Using `map` and `filter` (no list comprehensions), read `n` integers and:
1. Filter to keep only even numbers
2. Map each even number to its square

Print the resulting list.

Example:
```
Input:
6
1 2 3 4 5 6
Output: [4, 16, 36]
```
MD,
                'starter_code'        => "n = int(input())\nnums = list(map(int, input().split()))\n# Use filter then map\nresult = list()\nprint(result)\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 150,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Using `functools.reduce`, compute the **product** of a list of integers without using a loop or `math.prod`.

Read `n` integers on one line. Print their product.

Example:
```
Input: 1 2 3 4 5
Output: 120
```
MD,
                'starter_code'        => "from functools import reduce\n\nnums = list(map(int, input().split()))\n# Use reduce\nresult = 0\nprint(result)\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 150,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Using `zip` and `enumerate`, read two lists of the same length and print a numbered table of pairs.

Format: `i: (a, b)` where `i` is 1-indexed.

Read `n`, then two lines each with `n` space-separated values (strings).

Example:
```
Input:
3
Alice Bob Carol
90 85 92
Output:
1: (Alice, 90)
2: (Bob, 85)
3: (Carol, 92)
```
MD,
                'starter_code'        => "n = int(input())\nnames = input().split()\nscores = input().split()\n# Use zip and enumerate\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 150,
            ],

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Using `functools.partial`, create a pre-configured `power_of_2(n)` function that always raises `2` to the power `n`.

Read `k` integers. For each, print `power_of_2(n)`.

Example:
```
Input:
4
0
1
8
10
Output:
1
2
256
1024
```
MD,
                'starter_code'        => "from functools import partial\n\ndef power(base, exp):\n    return base ** exp\n\npower_of_2 = partial(power, 2)\n\nk = int(input())\nfor _ in range(k):\n    print(power_of_2(int(input())))\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 150,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Using `sorted` with a **custom key**, sort a list of records. Each record is `name score age`. Sort by score descending, then by name ascending for ties.

Read `n` records. Print each sorted record as `name score age`.

Example:
```
Input:
4
Alice 90 20
Bob 85 22
Carol 90 19
Dave 75 21
Output:
Alice 90 20
Carol 90 19
Bob 85 22
Dave 75 21
```
MD,
                'starter_code'        => "n = int(input())\nrecords = [input().split() for _ in range(n)]\n# Sort with custom key\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: String Manipulation & Formatting (Q33–Q37)
            // ═══════════════════════════════════════════════════════════════

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
Write a function `caesar_cipher(text, shift)` that applies a Caesar cipher (shift each letter by `shift` positions, wrapping around; preserve case; leave non-letters unchanged).

Read the text (one line) and the shift (integer, may be negative). Print the ciphered text.

Example:
```
Input:
Hello, World!
3
Output: Khoor, Zruog!
```
MD,
                'starter_code'        => "def caesar_cipher(text, shift):\n    pass\n\ntext = input()\nshift = int(input())\nprint(caesar_cipher(text, shift))\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
Write a function `is_pangram(sentence)` that returns `True` if the sentence contains every letter of the alphabet at least once (case-insensitive), else `False`.

Read `n` sentences. Print `True` or `False` for each.

Example:
```
Input:
2
The quick brown fox jumps over the lazy dog
Hello World
Output:
True
False
```
MD,
                'starter_code'        => "def is_pangram(sentence):\n    pass\n\nn = int(input())\nfor _ in range(n):\n    print(is_pangram(input()))\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 150,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Write a function `word_frequency_table(text)` that returns a formatted table of word frequencies. Columns: `Word` (left-aligned, width 15) and `Count` (right-aligned, width 5). Header with a separator line of dashes. Rows sorted by frequency descending, then alphabetically.

Read a paragraph (single line). Print the table.

Example:
```
Input: to be or not to be
Output:
Word             Count
--------------------
be                   2
to                   2
not                  1
or                   1
```
MD,
                'starter_code'        => "def word_frequency_table(text):\n    pass\n\nprint(word_frequency_table(input()))\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Write a function `title_case_smart(sentence)` that converts a sentence to title case but keeps small words (`a, an, the, and, but, or, for, nor, on, at, to, by, in`) lowercase unless they are the first or last word.

Read a sentence. Print the result.

Example:
```
Input: the cat sat on a mat by the door
Output: The Cat Sat on a Mat by the Door
```
MD,
                'starter_code'        => "SMALL = {'a','an','the','and','but','or','for','nor','on','at','to','by','in'}\n\ndef title_case_smart(sentence):\n    pass\n\nprint(title_case_smart(input()))\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Write a function `compress(s)` that compresses a string using counts only when compression saves space. Return the compressed form if shorter, else the original. (Same rules as run-length encoding but only use compressed form if it's strictly shorter.)

Example:
```
Input: aabcccccaaa
Output: a2b1c5a3
```
```
Input: abc
Output: abc
```
Read `n` strings. Print each result.
MD,
                'starter_code'        => "def compress(s):\n    pass\n\nn = int(input())\nfor _ in range(n):\n    print(compress(input()))\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Algorithms — Binary Search & Two Pointers (Q38–Q43)
            // ═══════════════════════════════════════════════════════════════

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Implement **binary search** that returns the index of a target in a sorted array, or `-1` if not found.

Read `n`, then `n` sorted integers on one line, then the target. Print the index.

Example:
```
Input:
5
1 3 5 7 9
5
Output: 2
```
MD,
                'starter_code'        => "def binary_search(arr, target):\n    # Iterative binary search\n    pass\n\nn = int(input())\narr = list(map(int, input().split()))\ntarget = int(input())\nprint(binary_search(arr, target))\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Using the **two-pointer** technique, find all pairs in a sorted array that sum to a target value. Print pairs in ascending order of first element. Each pair printed as `(a, b)`.

Read `n`, then `n` integers (already sorted), then the target.

Example:
```
Input:
6
1 2 3 4 5 6
7
Output:
(1, 6)
(2, 5)
(3, 4)
```
MD,
                'starter_code'        => "n = int(input())\narr = list(map(int, input().split()))\ntarget = int(input())\n# Two-pointer approach\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
**Sliding Window** — Find the length of the longest substring without repeating characters.

Read `n` strings. For each, print the length.

Example:
```
Input:
3
abcabcbb
bbbbb
pwwkew
Output:
3
1
3
```
MD,
                'starter_code'        => "n = int(input())\nfor _ in range(n):\n    s = input()\n    # Sliding window\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 250,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Use **binary search** to find the **insertion position** (`bisect_left` behaviour) for a target in a sorted array.

Read `n`, then the sorted array, then `q` queries (targets). Print the insertion index for each.

Example:
```
Input:
5
1 3 5 7 9
3
2
5
10
Output:
1
2
5
```
MD,
                'starter_code'        => "n = int(input())\narr = list(map(int, input().split()))\nq = int(input())\nfor _ in range(q):\n    target = int(input())\n    # Binary search for insertion point\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
**Three Sum** — Find all unique triplets in an array that sum to zero. Print each triplet sorted, triplets sorted lexicographically.

Read `n` integers on one line.

Example:
```
Input: -1 0 1 2 -1 -4
Output:
[-1, -1, 2]
[-1, 0, 1]
```
MD,
                'starter_code'        => "nums = list(map(int, input().split()))\n# Find all unique triplets summing to 0\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 350,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
**Merge Intervals** — Given a list of intervals, merge all overlapping ones. Print the merged intervals sorted by start.

Read `n`, then `n` lines each `start end`. Print each merged interval as `[start, end]`.

Example:
```
Input:
4
1 3
2 6
8 10
15 18
Output:
[1, 6]
[8, 10]
[15, 18]
```
MD,
                'starter_code'        => "n = int(input())\nintervals = [list(map(int, input().split())) for _ in range(n)]\n# Merge overlapping intervals\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: Data Transformation & Itertools (Q44–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Using `itertools.groupby`, group a sorted list of words by their first letter and print each group.

Read `n` words (one per line, already sorted). Print `LETTER: word1, word2, ...` for each group.

Example:
```
Input:
6
apple
avocado
banana
blueberry
cherry
coconut
Output:
a: apple, avocado
b: banana, blueberry
c: cherry, coconut
```
MD,
                'starter_code'        => "from itertools import groupby\n\nn = int(input())\nwords = [input() for _ in range(n)]\n# Use groupby\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Using `itertools.combinations`, print all combinations of size `k` from a list, one per line.

Read `n` items (one per line), then `k`. Print each combination as a tuple.

Example:
```
Input:
4
a
b
c
d
2
Output:
('a', 'b')
('a', 'c')
('a', 'd')
('b', 'c')
('b', 'd')
('c', 'd')
```
MD,
                'starter_code'        => "from itertools import combinations\n\nn = int(input())\nitems = [input() for _ in range(n)]\nk = int(input())\nfor combo in combinations(items, k):\n    print(combo)\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 150,
            ],

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Using `itertools.permutations`, print all permutations of a string's characters in lexicographic order (no duplicates).

Read a string. Print each unique permutation.

Example:
```
Input: ABC
Output:
ABC
ACB
BAC
BCA
CAB
CBA
```
MD,
                'starter_code'        => "from itertools import permutations\n\ns = input()\nfor p in sorted(set(permutations(s))):\n    print(''.join(p))\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 150,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
Write a function `transpose(matrix)` that transposes an n×m matrix (without using `zip` or numpy).

Read `n m`, then `n` rows. Print the transposed matrix, rows space-separated.

Example:
```
Input:
2 3
1 2 3
4 5 6
Output:
1 4
2 5
3 6
```
MD,
                'starter_code'        => "def transpose(matrix):\n    pass\n\nn, m = map(int, input().split())\nmatrix = [list(map(int, input().split())) for _ in range(n)]\nfor row in transpose(matrix):\n    print(*row)\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 200,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Write a function `deep_merge(d1, d2)` that recursively merges two dicts. For duplicate keys: if both values are dicts, merge them recursively; otherwise, `d2`'s value wins.

Read two Python dict literals (one per line). Print the merged dict (sorted keys at each level).

Example:
```
Input:
{'a': 1, 'b': {'x': 10, 'y': 20}}
{'b': {'y': 99, 'z': 30}, 'c': 3}
Output: {'a': 1, 'b': {'x': 10, 'y': 99, 'z': 30}, 'c': 3}
```
MD,
                'starter_code'        => "import ast\n\ndef deep_merge(d1, d2):\n    pass\n\nd1 = ast.literal_eval(input())\nd2 = ast.literal_eval(input())\nprint(deep_merge(d1, d2))\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Write a function `chunk(lst, size)` that splits a list into chunks of at most `size` elements.

Read `n` integers on one line, then `size`. Print each chunk on its own line.

Example:
```
Input:
1 2 3 4 5 6 7
3
Output:
[1, 2, 3]
[4, 5, 6]
[7]
```
MD,
                'starter_code'        => "def chunk(lst, size):\n    pass\n\nnums = list(map(int, input().split()))\nsize = int(input())\nfor c in chunk(nums, size):\n    print(c)\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 150,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
**Pipeline** — Write a function `pipeline(*funcs)` that returns a function applying each function in sequence to an input.

Pre-defined functions: `double(x)` returns `x * 2`, `increment(x)` returns `x + 1`, `square(x)` returns `x ** 2`.

Read `n` pipeline descriptions. Each is a space-separated list of function names followed by an integer input on the next line. Print the result.

Example:
```
Input:
2
double increment square
3
square increment double
2
Output:
49
10
```
MD,
                'starter_code'        => "def pipeline(*funcs):\n    def apply(x):\n        for f in funcs:\n            x = f(x)\n        return x\n    return apply\n\ndef double(x): return x * 2\ndef increment(x): return x + 1\ndef square(x): return x ** 2\nFUNCS = {'double': double, 'increment': increment, 'square': square}\n\nn = int(input())\nfor _ in range(n):\n    func_names = input().split()\n    x = int(input())\n    p = pipeline(*[FUNCS[name] for name in func_names])\n    print(p(x))\n",
                'time_limit_seconds'  => 1200,
                'base_xp'             => 300,
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

        // ── Q1: Circle sort ───────────────────────────────────────────────
        $seed(1, [
            ['input' => "3\n5\n1\n3",         'expected_output' => "Circle(radius=1)\nCircle(radius=3)\nCircle(radius=5)",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n7",               'expected_output' => "Circle(radius=7)",                                       'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n2\n4\n1\n3",      'expected_output' => "Circle(radius=1)\nCircle(radius=2)\nCircle(radius=3)\nCircle(radius=4)", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "2\n10\n0.5",         'expected_output' => "Circle(radius=0.5)\nCircle(radius=10)",                 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: Vector2D ──────────────────────────────────────────────────
        $seed(2, [
            ['input' => "3.0 4.0\n1.0 2.0\n2",  'expected_output' => "Vector2D(x=4.0, y=6.0)\nVector2D(x=6.0, y=8.0)\n5.0",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "0.0 0.0\n1.0 1.0\n5",  'expected_output' => "Vector2D(x=1.0, y=1.0)\nVector2D(x=0.0, y=0.0)\n0.0",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1.0 0.0\n0.0 1.0\n3",  'expected_output' => "Vector2D(x=1.0, y=1.0)\nVector2D(x=3.0, y=0.0)\n1.0",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3.0 4.0\n0.0 0.0\n1",  'expected_output' => "Vector2D(x=3.0, y=4.0)\nVector2D(x=3.0, y=4.0)\n5.0",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: Fraction ──────────────────────────────────────────────────
        $seed(3, [
            ['input' => "1 2\n1 3",   'expected_output' => "5/6",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 4\n1 4",   'expected_output' => "1/2",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 3\n1 6",   'expected_output' => "5/6",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "3 4\n1 8",   'expected_output' => "7/8",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: Temperature ───────────────────────────────────────────────
        $seed(4, [
            ['input' => "100",   'expected_output' => "100°C\n212.00\n373.15",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0",     'expected_output' => "0°C\n32.00\n273.15",      'is_hidden' => false, 'order_index' => 2],
            ['input' => "-40",   'expected_output' => "-40°C\n-40.00\n233.15",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "37",    'expected_output' => "37°C\n98.60\n310.15",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: Person set ────────────────────────────────────────────────
        $seed(5, [
            ['input' => "4\nAlice 30\nBob 25\nAlice 30\nCharlie 40",   'expected_output' => "3",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nAna 20\nAna 20",                           'expected_output' => "1",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nX 1\nY 2\nZ 3",                           'expected_output' => "3",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nSolo 99",                                   'expected_output' => "1",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: ShoppingCart ──────────────────────────────────────────────
        $seed(6, [
            ['input' => "4\nadd apple 1.50 3\nadd banana 0.75\nremove banana\nadd milk 2.00 2",  'expected_output' => "apple: 3 × 1.50 = 4.50\nmilk: 2 × 2.00 = 4.00\nTotal: 8.50",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\nadd bread 2.50",                                                      'expected_output' => "bread: 1 × 2.50 = 2.50\nTotal: 2.50",                            'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nadd egg 0.20 12\nadd egg 0.20 6\nremove bread",                      'expected_output' => "egg: 6 × 0.20 = 1.20\nTotal: 1.20",                              'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\nadd item 5.00\nremove item",                                          'expected_output' => "Total: 0.00",                                                    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: Student sort ──────────────────────────────────────────────
        $seed(7, [
            ['input' => "3\nAlice\n90 85 92\nBob\n78 80 76\nCarol\n95 88 91",   'expected_output' => "Carol: avg=91.33, high=95, low=88\nAlice: avg=89.0, high=92, low=85\nBob: avg=78.0, high=80, low=76",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\nSolo\n100",                                           'expected_output' => "Solo: avg=100.0, high=100, low=100",                                                                        'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nA\n60 70\nB\n80 90",                                 'expected_output' => "B: avg=85.0, high=90, low=80\nA: avg=65.0, high=70, low=60",                                               'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\nX\n50 50 50\nY\n50 50 50",                           'expected_output' => "X: avg=50.0, high=50, low=50\nY: avg=50.0, high=50, low=50",                                               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: EventEmitter ──────────────────────────────────────────────
        $seed(8, [
            ['input' => "5\non greet shout\non greet whisper\nemit greet Hello\noff greet whisper\nemit greet Hello",   'expected_output' => "HELLO\nhello\nHELLO",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\non greet whisper\nemit greet World",                                                         'expected_output' => "world",                'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\non x shout\non x shout\nemit x hi",                                                         'expected_output' => "HI\nHI",               'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nemit greet nobody",                                                                          'expected_output' => "",                     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: Fast power ────────────────────────────────────────────────
        $seed(9, [
            ['input' => "2\n10",   'expected_output' => "1024",          'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n0",    'expected_output' => "1",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n5",    'expected_output' => "3125",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "7\n3",    'expected_output' => "343",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: Flatten ──────────────────────────────────────────────────
        $seed(10, [
            ['input' => "[[1, 2], [3, [4, 5]], 6]",          'expected_output' => "[1, 2, 3, 4, 5, 6]",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "[1]",                                 'expected_output' => "[1]",                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "[[[[1]]]]",                           'expected_output' => "[1]",                  'is_hidden' => true,  'order_index' => 3],
            ['input' => "[1, [2, [3, [4, [5]]]]]",            'expected_output' => "[1, 2, 3, 4, 5]",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: Catalan ──────────────────────────────────────────────────
        $seed(11, [
            ['input' => "5",    'expected_output' => "42",        'is_hidden' => false, 'order_index' => 1],
            ['input' => "0",    'expected_output' => "1",         'is_hidden' => false, 'order_index' => 2],
            ['input' => "3",    'expected_output' => "5",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "7",    'expected_output' => "429",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: Count paths ──────────────────────────────────────────────
        $seed(12, [
            ['input' => "3\n3",   'expected_output' => "6",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1",   'expected_output' => "1",    'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n4",   'expected_output' => "20",   'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n5",   'expected_output' => "5",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: Tower of Hanoi ───────────────────────────────────────────
        $seed(13, [
            ['input' => "2",  'expected_output' => "Move disk from A to B\nMove disk from A to C\nMove disk from B to C",                                                                                                                                                                                                     'is_hidden' => false, 'order_index' => 1],
            ['input' => "1",  'expected_output' => "Move disk from A to C",                                                                                                                                                                                                                                                   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3",  'expected_output' => "Move disk from A to C\nMove disk from A to B\nMove disk from C to B\nMove disk from A to C\nMove disk from B to A\nMove disk from B to C\nMove disk from A to C",                                                                                                         'is_hidden' => true,  'order_index' => 3],
            ['input' => "4",  'expected_output' => "Move disk from A to B\nMove disk from A to C\nMove disk from B to C\nMove disk from A to B\nMove disk from C to A\nMove disk from C to B\nMove disk from A to B\nMove disk from A to C\nMove disk from B to C\nMove disk from B to A\nMove disk from C to A\nMove disk from B to C\nMove disk from A to B\nMove disk from A to C\nMove disk from B to C", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q14: Primes comprehension ─────────────────────────────────────
        $seed(14, [
            ['input' => "20",   'expected_output' => "2 3 5 7 11 13 17 19",               'is_hidden' => false, 'order_index' => 1],
            ['input' => "2",    'expected_output' => "2",                                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "30",   'expected_output' => "2 3 5 7 11 13 17 19 23 29",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "1",    'expected_output' => "",                                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: Prime generator ──────────────────────────────────────────
        $seed(15, [
            ['input' => "5",    'expected_output' => "2\n3\n5\n7\n11",                    'is_hidden' => false, 'order_index' => 1],
            ['input' => "1",    'expected_output' => "2",                                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "10",   'expected_output' => "2\n3\n5\n7\n11\n13\n17\n19\n23\n29",'is_hidden' => true,  'order_index' => 3],
            ['input' => "3",    'expected_output' => "2\n3\n5",                            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: Dict comprehension squares ──────────────────────────────
        $seed(16, [
            ['input' => "3\na 3\nb 4\nc 5",     'expected_output' => "{'a': 9, 'b': 16, 'c': 25}",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\nx 10",              'expected_output' => "{'x': 100}",                    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nm 2\nn 3",          'expected_output' => "{'m': 4, 'n': 9}",              'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\nz 0\na 1",          'expected_output' => "{'a': 1, 'z': 0}",              'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: Multiplication table ─────────────────────────────────────
        $seed(17, [
            ['input' => "3",  'expected_output' => "1 2 3\n2 4 6\n3 6 9",                               'is_hidden' => false, 'order_index' => 1],
            ['input' => "1",  'expected_output' => "1",                                                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "4",  'expected_output' => "1 2 3 4\n2 4 6 8\n3 6 9 12\n4 8 12 16",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "2",  'expected_output' => "1 2\n2 4",                                           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: Odd squares generator ────────────────────────────────────
        $seed(18, [
            ['input' => "10",   'expected_output' => "1\n9\n25\n49\n81",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1",    'expected_output' => "1",                   'is_hidden' => false, 'order_index' => 2],
            ['input' => "5",    'expected_output' => "1\n9\n25",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "15",   'expected_output' => "1\n9\n25\n49\n81\n121\n169", 'is_hidden' => true, 'order_index' => 4],
        ]);

        // ── Q19: safe_sqrt ────────────────────────────────────────────────
        $seed(19, [
            ['input' => "3\n16\n-4\n9",     'expected_output' => "4.0\nValue must be non-negative\n3.0",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0",             'expected_output' => "0.0",                                     'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n2\n-1",         'expected_output' => "1.4142\nValue must be non-negative",      'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n100",           'expected_output' => "10.0",                                    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: lookup/parse ─────────────────────────────────────────────
        $seed(20, [
            ['input' => "4\nparse 42\nlookup 42\nlookup 99\nparse hello",   'expected_output' => "42\n42\nKey not found: 99\nNot an integer: hello",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "2\nparse 5\nlookup 5",                              'expected_output' => "5\n5",                                                'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\nlookup x",                                       'expected_output' => "Key not found: x",                                    'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\nparse abc\nparse 7",                             'expected_output' => "Not an integer: abc\n7",                              'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: try/except/else/finally ──────────────────────────────────
        $seed(21, [
            ['input' => "2\n10\n2\n5\n0",   'expected_output' => "Result: 5.0\nDone\nCannot divide by zero\nDone",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1\n1",           'expected_output' => "Result: 1.0\nDone",                                'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n0\n0",           'expected_output' => "Cannot divide by zero\nDone",                      'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n9\n3",           'expected_output' => "Result: 3.0\nDone",                                'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: Timer context manager ────────────────────────────────────
        $seed(22, [
            ['input' => "1000000",   'expected_output' => "500000500000\nElapsed:",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "100",       'expected_output' => "5050\nElapsed:",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "10",        'expected_output' => "55\nElapsed:",             'is_hidden' => true,  'order_index' => 3],
            ['input' => "1",         'expected_output' => "1\nElapsed:",              'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: Counter top-k ────────────────────────────────────────────
        $seed(23, [
            ['input' => "3\nthe quick brown fox jumps over the lazy dog the fox",   'expected_output' => "the: 3\nfox: 2\nbrown: 1",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\nhello world hello",                                      'expected_output' => "hello: 2",                    'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\na a b b c",                                             'expected_output' => "a: 2\nb: 2",                  'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nonly",                                                   'expected_output' => "only: 1",                     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: Inverted index ───────────────────────────────────────────
        $seed(24, [
            ['input' => "2\ndoc1 python is great\ndoc2 python is fun",   'expected_output' => "fun: ['doc2']\ngreat: ['doc1']\nis: ['doc1', 'doc2']\npython: ['doc1', 'doc2']",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\ndoc1 hello world",                            'expected_output' => "hello: ['doc1']\nworld: ['doc1']",                                                 'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nd1 cat\nd2 cat",                              'expected_output' => "cat: ['d1', 'd2']",                                                                'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nd1 a b c",                                    'expected_output' => "a: ['d1']\nb: ['d1']\nc: ['d1']",                                                  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: Sliding window max ───────────────────────────────────────
        $seed(25, [
            ['input' => "8\n1 3 -1 -3 5 3 6 7\n3",   'expected_output' => "3 3 5 5 6 7",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 2 3 4\n1",              'expected_output' => "1 2 3 4",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n5 5 5\n2",                'expected_output' => "5 5",            'is_hidden' => true,  'order_index' => 3],
            ['input' => "5\n4 3 2 1 5\n3",            'expected_output' => "4 3 5",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: OrderedDict LRU ──────────────────────────────────────────
        $seed(26, [
            ['input' => "2\n5\nput a 1\nput b 2\nget a\nput c 3\nget b",   'expected_output' => "1\n-1",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n3\nput x 10\nget x\nput y 20\nget x",          'expected_output' => "10\n-1",  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\n2\nput p 1\nput q 2\nget p\nget q",            'expected_output' => "1\n2",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n1\nget z",                                       'expected_output' => "-1",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: Point3D distance ─────────────────────────────────────────
        $seed(27, [
            ['input' => "2\n0 0 0\n1 1 1\n1 2 3\n4 5 6",   'expected_output' => "1.7321\n5.196",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0 0 0\n0 0 0",                  'expected_output' => "0.0",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n1 0 0\n0 0 0",                  'expected_output' => "1.0",             'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n3 4 0\n0 0 0",                  'expected_output' => "5.0",             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: map/filter squares ───────────────────────────────────────
        $seed(28, [
            ['input' => "6\n1 2 3 4 5 6",    'expected_output' => "[4, 16, 36]",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 3 5",           'expected_output' => "[]",            'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n2 4 6 8",         'expected_output' => "[4, 16, 36, 64]",'is_hidden' => true, 'order_index' => 3],
            ['input' => "1\n0",               'expected_output' => "[0]",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: reduce product ───────────────────────────────────────────
        $seed(29, [
            ['input' => "1 2 3 4 5",   'expected_output' => "120",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1",           'expected_output' => "1",     'is_hidden' => false, 'order_index' => 2],
            ['input' => "2 3 4",       'expected_output' => "24",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "10 10",       'expected_output' => "100",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: zip enumerate ────────────────────────────────────────────
        $seed(30, [
            ['input' => "3\nAlice Bob Carol\n90 85 92",   'expected_output' => "1: (Alice, 90)\n2: (Bob, 85)\n3: (Carol, 92)",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\nAna\n100",                    'expected_output' => "1: (Ana, 100)",                                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nX Y\n1 2",                   'expected_output' => "1: (X, 1)\n2: (Y, 2)",                           'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\na b c d\n10 20 30 40",       'expected_output' => "1: (a, 10)\n2: (b, 20)\n3: (c, 30)\n4: (d, 40)",'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: partial power_of_2 ───────────────────────────────────────
        $seed(31, [
            ['input' => "4\n0\n1\n8\n10",   'expected_output' => "1\n2\n256\n1024",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n0",             'expected_output' => "1",                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n3\n5\n7",       'expected_output' => "8\n32\n128",         'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n16\n20",        'expected_output' => "65536\n1048576",     'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: sorted custom key ────────────────────────────────────────
        $seed(32, [
            ['input' => "4\nAlice 90 20\nBob 85 22\nCarol 90 19\nDave 75 21",   'expected_output' => "Alice 90 20\nCarol 90 19\nBob 85 22\nDave 75 21",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\nSolo 50 30",                                          'expected_output' => "Solo 50 30",                                         'is_hidden' => false, 'order_index' => 2],
            ['input' => "2\nA 70 1\nB 70 2",                                     'expected_output' => "A 70 1\nB 70 2",                                     'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\nZ 100 1\nA 100 2\nM 80 3",                          'expected_output' => "A 100 2\nZ 100 1\nM 80 3",                            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: Caesar cipher ────────────────────────────────────────────
        $seed(33, [
            ['input' => "Hello, World!\n3",   'expected_output' => "Khoor, Zruog!",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "abc\n0",             'expected_output' => "abc",             'is_hidden' => false, 'order_index' => 2],
            ['input' => "xyz\n3",             'expected_output' => "abc",             'is_hidden' => true,  'order_index' => 3],
            ['input' => "Khoor\n-3",          'expected_output' => "Hello",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: Pangram ──────────────────────────────────────────────────
        $seed(34, [
            ['input' => "2\nThe quick brown fox jumps over the lazy dog\nHello World",   'expected_output' => "True\nFalse",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\nPack my box with five dozen liquor jugs",                    'expected_output' => "True",           'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\nabc",                                                         'expected_output' => "False",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nHow vexingly quick daft zebras jump",                        'expected_output' => "True",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: word_frequency_table ─────────────────────────────────────
        $seed(35, [
            ['input' => "to be or not to be",                     'expected_output' => "Word             Count\n--------------------\nbe                   2\nto                   2\nnot                  1\nor                   1",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "hello",                                    'expected_output' => "Word             Count\n--------------------\nhello                1",                                                                           'is_hidden' => false, 'order_index' => 2],
            ['input' => "a a a",                                   'expected_output' => "Word             Count\n--------------------\na                    3",                                                                           'is_hidden' => true,  'order_index' => 3],
            ['input' => "cat bat sat",                             'expected_output' => "Word             Count\n--------------------\nbat                  1\ncat                  1\nsat                  1",                             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: title_case_smart ─────────────────────────────────────────
        $seed(36, [
            ['input' => "the cat sat on a mat by the door",   'expected_output' => "The Cat Sat on a Mat by the Door",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "a tale of two cities",               'expected_output' => "A Tale of Two Cities",               'is_hidden' => false, 'order_index' => 2],
            ['input' => "the end",                            'expected_output' => "The End",                            'is_hidden' => true,  'order_index' => 3],
            ['input' => "pride and prejudice",                'expected_output' => "Pride and Prejudice",                'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: compress ─────────────────────────────────────────────────
        $seed(37, [
            ['input' => "2\naabcccccaaa\nabc",   'expected_output' => "a2b1c5a3\nabc",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\naaaa",               'expected_output' => "a4",               'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\nab",                 'expected_output' => "ab",               'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\naabb",               'expected_output' => "a2b2",             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: Binary search ────────────────────────────────────────────
        $seed(38, [
            ['input' => "5\n1 3 5 7 9\n5",    'expected_output' => "2",    'is_hidden' => false, 'order_index' => 1],
            ['input' => "5\n1 3 5 7 9\n4",    'expected_output' => "-1",   'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\n42\n42",           'expected_output' => "0",    'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\n2 4 6 8\n6",      'expected_output' => "2",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: Two-pointer pairs ────────────────────────────────────────
        $seed(39, [
            ['input' => "6\n1 2 3 4 5 6\n7",   'expected_output' => "(1, 6)\n(2, 5)\n(3, 4)",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "4\n1 2 3 4\n10",       'expected_output' => "",                          'is_hidden' => false, 'order_index' => 2],
            ['input' => "4\n1 2 3 4\n5",        'expected_output' => "(1, 4)\n(2, 3)",           'is_hidden' => true,  'order_index' => 3],
            ['input' => "3\n1 1 2\n2",          'expected_output' => "(1, 1)",                   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: Longest substring no repeat ─────────────────────────────
        $seed(40, [
            ['input' => "3\nabcabcbb\nbbbbb\npwwkew",   'expected_output' => "3\n1\n3",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\na",                          'expected_output' => "1",          'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\nabcdef",                     'expected_output' => "6",          'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\naab",                        'expected_output' => "2",          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: Insertion position ───────────────────────────────────────
        $seed(41, [
            ['input' => "5\n1 3 5 7 9\n3\n2\n5\n10",   'expected_output' => "1\n2\n5",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1 2 3\n1\n0",              'expected_output' => "0\n0",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "5\n1 3 5 7 9\n1\n9",          'expected_output' => "2\n4",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n5\n2\n3",                  'expected_output' => "0\n0",       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: Three Sum ────────────────────────────────────────────────
        $seed(42, [
            ['input' => "-1 0 1 2 -1 -4",   'expected_output' => "[-1, -1, 2]\n[-1, 0, 1]",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "0 0 0",             'expected_output' => "[0, 0, 0]",                  'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 2 3",             'expected_output' => "",                            'is_hidden' => true,  'order_index' => 3],
            ['input' => "-2 0 1 1 2",        'expected_output' => "[-2, 0, 2]\n[-2, 1, 1]",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: Merge intervals ──────────────────────────────────────────
        $seed(43, [
            ['input' => "4\n1 3\n2 6\n8 10\n15 18",   'expected_output' => "[1, 6]\n[8, 10]\n[15, 18]",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\n1 4",                       'expected_output' => "[1, 4]",                       'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\n1 4\n4 5\n6 8",            'expected_output' => "[1, 5]\n[6, 8]",              'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\n1 10\n2 3",                'expected_output' => "[1, 10]",                      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: itertools groupby ────────────────────────────────────────
        $seed(44, [
            ['input' => "6\napple\navocado\nbanana\nblueberry\ncherry\ncoconut",   'expected_output' => "a: apple, avocado\nb: banana, blueberry\nc: cherry, coconut",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\nzebra",                                                 'expected_output' => "z: zebra",                                                      'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nact\narc\nbat",                                        'expected_output' => "a: act, arc\nb: bat",                                            'is_hidden' => true,  'order_index' => 3],
            ['input' => "4\ndog\ndoor\neel\nearly",                                'expected_output' => "d: dog, door\ne: eel, early",                                    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: itertools combinations ───────────────────────────────────
        $seed(45, [
            ['input' => "4\na\nb\nc\nd\n2",   'expected_output' => "('a', 'b')\n('a', 'c')\n('a', 'd')\n('b', 'c')\n('b', 'd')\n('c', 'd')",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "3\n1\n2\n3\n3",      'expected_output' => "('1', '2', '3')",                                                            'is_hidden' => false, 'order_index' => 2],
            ['input' => "3\nx\ny\nz\n1",      'expected_output' => "('x',)\n('y',)\n('z',)",                                                     'is_hidden' => true,  'order_index' => 3],
            ['input' => "2\na\nb\n2",         'expected_output' => "('a', 'b')",                                                                  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: permutations ─────────────────────────────────────────────
        $seed(46, [
            ['input' => "ABC",   'expected_output' => "ABC\nACB\nBAC\nBCA\nCAB\nCBA",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "A",     'expected_output' => "A",                               'is_hidden' => false, 'order_index' => 2],
            ['input' => "AB",    'expected_output' => "AB\nBA",                          'is_hidden' => true,  'order_index' => 3],
            ['input' => "AAB",   'expected_output' => "AAB\nABA\nBAA",                  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: transpose ────────────────────────────────────────────────
        $seed(47, [
            ['input' => "2 3\n1 2 3\n4 5 6",    'expected_output' => "1 4\n2 5\n3 6",      'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 1\n9",               'expected_output' => "9",                   'is_hidden' => false, 'order_index' => 2],
            ['input' => "3 2\n1 2\n3 4\n5 6",  'expected_output' => "1 3 5\n2 4 6",        'is_hidden' => true,  'order_index' => 3],
            ['input' => "2 2\n1 2\n3 4",        'expected_output' => "1 3\n2 4",            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: deep_merge ───────────────────────────────────────────────
        $seed(48, [
            ['input' => "{'a': 1, 'b': {'x': 10, 'y': 20}}\n{'b': {'y': 99, 'z': 30}, 'c': 3}",   'expected_output' => "{'a': 1, 'b': {'x': 10, 'y': 99, 'z': 30}, 'c': 3}",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "{}\n{'a': 1}",                                                               'expected_output' => "{'a': 1}",                                              'is_hidden' => false, 'order_index' => 2],
            ['input' => "{'a': 1}\n{'a': 2}",                                                        'expected_output' => "{'a': 2}",                                              'is_hidden' => true,  'order_index' => 3],
            ['input' => "{'x': {'y': 1}}\n{'x': {'z': 2}}",                                        'expected_output' => "{'x': {'y': 1, 'z': 2}}",                               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: chunk ────────────────────────────────────────────────────
        $seed(49, [
            ['input' => "1 2 3 4 5 6 7\n3",   'expected_output' => "[1, 2, 3]\n[4, 5, 6]\n[7]",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1 2\n5",              'expected_output' => "[1, 2]",                       'is_hidden' => false, 'order_index' => 2],
            ['input' => "1 2 3 4\n2",          'expected_output' => "[1, 2]\n[3, 4]",              'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\n1",                'expected_output' => "[1]",                          'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: pipeline ─────────────────────────────────────────────────
        $seed(50, [
            ['input' => "2\ndouble increment square\n3\nsquare increment double\n2",   'expected_output' => "49\n10",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "1\ndouble\n5",                                                 'expected_output' => "10",       'is_hidden' => false, 'order_index' => 2],
            ['input' => "1\nsquare double increment\n3",                               'expected_output' => "19",       'is_hidden' => true,  'order_index' => 3],
            ['input' => "1\nincrement increment double\n4",                            'expected_output' => "12",       'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 1 Coding (Professional) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}