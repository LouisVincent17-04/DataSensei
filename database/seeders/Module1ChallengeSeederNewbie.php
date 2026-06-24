<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;

class Module1ChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (! $category) {
            $this->command->error('Newbie category not found. Run ChallengeCategorySeeder first.');
            return;
        }

        $title = 'Basics of Python Programming';

        Challenge::where('challenge_category_id', $category->id)
            ->where('title', $title)
            ->where('is_coding_challenge', 0)
            ->delete();

        $this->command->info('Creating Module 1 — Basics of Python Programming (Newbie) [MCQ]...');

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title' => $title,
            'description' => 'A detailed 50-item Newbie MCQ challenge for Basics of Python Programming. Items include concepts, scenarios, debugging, interpretation, and decision-making.',
            'time_limit_seconds' => 1200,
            'base_xp' => 500,
            'order_index' => 1,
            'is_coding_challenge' => 0,
        ]);

        $qaData = [
            [
                'q' => 'What is the exact output of this code? `name = "Ana"; print("Hi", name)`',
                'opts' => [
                    ['text' => 'Hi Ana', 'correct' => true],
                    ['text' => 'HiAna', 'correct' => false],
                    ['text' => '"Hi" Ana', 'correct' => false],
                    ['text' => 'Error because print cannot use two values', 'correct' => false],
                ],
            ],
            [
                'q' => 'Which line correctly creates an integer variable named `score` with value 10?',
                'opts' => [
                    ['text' => 'score == 10', 'correct' => false],
                    ['text' => 'score = 10', 'correct' => true],
                    ['text' => 'int score = 10', 'correct' => false],
                    ['text' => 'let score = 10', 'correct' => false],
                ],
            ],
            [
                'q' => 'What is the output of `print(7 // 2)`?',
                'opts' => [
                    ['text' => '3.5', 'correct' => false],
                    ['text' => '3', 'correct' => true],
                    ['text' => '4', 'correct' => false],
                    ['text' => '1', 'correct' => false],
                ],
            ],
            [
                'q' => 'What is the output of `print(7 % 2)`?',
                'opts' => [
                    ['text' => '3', 'correct' => false],
                    ['text' => '1', 'correct' => true],
                    ['text' => '0', 'correct' => false],
                    ['text' => '3.5', 'correct' => false],
                ],
            ],
            [
                'q' => 'Which code prints the value of variable `age` instead of the word age?',
                'opts' => [
                    ['text' => 'print("age")', 'correct' => false],
                    ['text' => 'print(age)', 'correct' => true],
                    ['text' => 'echo(age)', 'correct' => false],
                    ['text' => 'display age', 'correct' => false],
                ],
            ],
            [
                'q' => 'What happens when Python runs `print("5" + "2")`?',
                'opts' => [
                    ['text' => 'It prints 7', 'correct' => false],
                    ['text' => 'It prints 52', 'correct' => true],
                    ['text' => 'It prints 5 2 on separate lines', 'correct' => false],
                    ['text' => 'It always causes an error', 'correct' => false],
                ],
            ],
            [
                'q' => 'Which conversion should be used before adding the input text `"8"` as a number?',
                'opts' => [
                    ['text' => 'str("8")', 'correct' => false],
                    ['text' => 'int("8")', 'correct' => true],
                    ['text' => 'list("8")', 'correct' => false],
                    ['text' => 'bool("8")', 'correct' => false],
                ],
            ],
            [
                'q' => 'What is the output? `x = 4; x = x + 3; print(x)`',
                'opts' => [
                    ['text' => '4', 'correct' => false],
                    ['text' => '3', 'correct' => false],
                    ['text' => '7', 'correct' => true],
                    ['text' => '43', 'correct' => false],
                ],
            ],
            [
                'q' => 'Which variable name is valid in Python?',
                'opts' => [
                    ['text' => '2_total', 'correct' => false],
                    ['text' => 'total-score', 'correct' => false],
                    ['text' => 'total_score', 'correct' => true],
                    ['text' => 'total score', 'correct' => false],
                ],
            ],
            [
                'q' => 'What does `# this is a note` represent in Python?',
                'opts' => [
                    ['text' => 'A string', 'correct' => false],
                    ['text' => 'A comment ignored by Python', 'correct' => true],
                    ['text' => 'A variable', 'correct' => false],
                    ['text' => 'A required command', 'correct' => false],
                ],
            ],
            [
                'q' => 'What is the output? `print(type(3.5).__name__)`',
                'opts' => [
                    ['text' => 'int', 'correct' => false],
                    ['text' => 'float', 'correct' => true],
                    ['text' => 'str', 'correct' => false],
                    ['text' => 'bool', 'correct' => false],
                ],
            ],
            [
                'q' => 'Which value is a Boolean literal in Python?',
                'opts' => [
                    ['text' => '"True"', 'correct' => false],
                    ['text' => 'true', 'correct' => false],
                    ['text' => 'True', 'correct' => true],
                    ['text' => 'YES', 'correct' => false],
                ],
            ],
            [
                'q' => 'What is the output? `print(len("Data"))`',
                'opts' => [
                    ['text' => '3', 'correct' => false],
                    ['text' => '4', 'correct' => true],
                    ['text' => '5', 'correct' => false],
                    ['text' => 'Data', 'correct' => false],
                ],
            ],
            [
                'q' => 'What is the output? `text = "Python"; print(text[0])`',
                'opts' => [
                    ['text' => 'P', 'correct' => true],
                    ['text' => 'y', 'correct' => false],
                    ['text' => 'Python', 'correct' => false],
                    ['text' => '0', 'correct' => false],
                ],
            ],
            [
                'q' => 'What is the output? `text = "Python"; print(text[-1])`',
                'opts' => [
                    ['text' => 'P', 'correct' => false],
                    ['text' => 'n', 'correct' => true],
                    ['text' => 'o', 'correct' => false],
                    ['text' => 'Error', 'correct' => false],
                ],
            ],
            [
                'q' => 'Which expression checks if `age` is at least 18?',
                'opts' => [
                    ['text' => 'age => 18', 'correct' => false],
                    ['text' => 'age >= 18', 'correct' => true],
                    ['text' => 'age = 18', 'correct' => false],
                    ['text' => 'age ==< 18', 'correct' => false],
                ],
            ],
            [
                'q' => 'What is the output? `print(3 > 2 and 2 > 5)`',
                'opts' => [
                    ['text' => 'True', 'correct' => false],
                    ['text' => 'False', 'correct' => true],
                    ['text' => '3', 'correct' => false],
                    ['text' => 'Error', 'correct' => false],
                ],
            ],
            [
                'q' => 'What is the output? `print(3 > 2 or 2 > 5)`',
                'opts' => [
                    ['text' => 'True', 'correct' => true],
                    ['text' => 'False', 'correct' => false],
                    ['text' => 'None', 'correct' => false],
                    ['text' => 'Error', 'correct' => false],
                ],
            ],
            [
                'q' => 'Which block is correctly indented for an if statement?',
                'opts' => [
                    ['text' => 'if x > 0:
print(x)', 'correct' => false],
                    ['text' => 'if x > 0:
    print(x)', 'correct' => true],
                    ['text' => 'if x > 0
    print(x)', 'correct' => false],
                    ['text' => 'if (x > 0) then print(x)', 'correct' => false],
                ],
            ],
            [
                'q' => 'What is the output? `x = 5; if x > 3: print("A") else: print("B")`',
                'opts' => [
                    ['text' => 'A', 'correct' => true],
                    ['text' => 'B', 'correct' => false],
                    ['text' => 'AB', 'correct' => false],
                    ['text' => 'Error because x is not a string', 'correct' => false],
                ],
            ],
            [
                'q' => 'What is the output of `list(range(3))`?',
                'opts' => [
                    ['text' => '[1, 2, 3]', 'correct' => false],
                    ['text' => '[0, 1, 2]', 'correct' => true],
                    ['text' => '[0, 1, 2, 3]', 'correct' => false],
                    ['text' => '[3]', 'correct' => false],
                ],
            ],
            [
                'q' => 'How many times does this loop run? `for i in range(5): print(i)`',
                'opts' => [
                    ['text' => '4', 'correct' => false],
                    ['text' => '5', 'correct' => true],
                    ['text' => '6', 'correct' => false],
                    ['text' => 'It never stops', 'correct' => false],
                ],
            ],
            [
                'q' => 'What is the output? `total = 0; for n in [1,2,3]: total += n; print(total)`',
                'opts' => [
                    ['text' => '0', 'correct' => false],
                    ['text' => '3', 'correct' => false],
                    ['text' => '6', 'correct' => true],
                    ['text' => '123', 'correct' => false],
                ],
            ],
            [
                'q' => 'Which statement adds value 9 to the end of list `nums`?',
                'opts' => [
                    ['text' => 'nums.add(9)', 'correct' => false],
                    ['text' => 'nums.append(9)', 'correct' => true],
                    ['text' => 'append(nums, 9)', 'correct' => false],
                    ['text' => 'nums.push(9)', 'correct' => false],
                ],
            ],
            [
                'q' => 'What is the output? `nums=[10,20,30]; print(nums[1])`',
                'opts' => [
                    ['text' => '10', 'correct' => false],
                    ['text' => '20', 'correct' => true],
                    ['text' => '30', 'correct' => false],
                    ['text' => '1', 'correct' => false],
                ],
            ],
            [
                'q' => 'Which function definition is valid Python?',
                'opts' => [
                    ['text' => 'function add(a,b):', 'correct' => false],
                    ['text' => 'def add(a, b):', 'correct' => true],
                    ['text' => 'func add(a,b)', 'correct' => false],
                    ['text' => 'define add(a,b):', 'correct' => false],
                ],
            ],
            [
                'q' => 'What does a function usually use to send a value back to the caller?',
                'opts' => [
                    ['text' => 'print', 'correct' => false],
                    ['text' => 'return', 'correct' => true],
                    ['text' => 'input', 'correct' => false],
                    ['text' => 'break', 'correct' => false],
                ],
            ],
            [
                'q' => 'What is the output? `def f(x): return x*2; print(f(4))`',
                'opts' => [
                    ['text' => '4', 'correct' => false],
                    ['text' => '8', 'correct' => true],
                    ['text' => 'x*2', 'correct' => false],
                    ['text' => 'None', 'correct' => false],
                ],
            ],
            [
                'q' => 'Which line reads user input into variable `name`?',
                'opts' => [
                    ['text' => 'name = input()', 'correct' => true],
                    ['text' => 'input = name()', 'correct' => false],
                    ['text' => 'read(name)', 'correct' => false],
                    ['text' => 'scan name', 'correct' => false],
                ],
            ],
            [
                'q' => 'Why can `int(input())` be useful?',
                'opts' => [
                    ['text' => 'It converts typed text to an integer', 'correct' => true],
                    ['text' => 'It prints text only', 'correct' => false],
                    ['text' => 'It creates a list', 'correct' => false],
                    ['text' => 'It stops the program', 'correct' => false],
                ],
            ],
            [
                'q' => 'What error is likely from `print("Age: " + 18)`?',
                'opts' => [
                    ['text' => 'NameError', 'correct' => false],
                    ['text' => 'TypeError from joining string and integer', 'correct' => true],
                    ['text' => 'SyntaxError because print is banned', 'correct' => false],
                    ['text' => 'No error; prints Age: 18', 'correct' => false],
                ],
            ],
            [
                'q' => 'Which line safely prints text with a number variable `age`?',
                'opts' => [
                    ['text' => 'print("Age: " + age)', 'correct' => false],
                    ['text' => 'print("Age:", age)', 'correct' => true],
                    ['text' => 'print("Age:" age)', 'correct' => false],
                    ['text' => 'echo("Age", age)', 'correct' => false],
                ],
            ],
            [
                'q' => 'What is the output? `print("ha" * 3)`',
                'opts' => [
                    ['text' => 'ha3', 'correct' => false],
                    ['text' => 'hahaha', 'correct' => true],
                    ['text' => 'ha ha ha with spaces', 'correct' => false],
                    ['text' => 'Error', 'correct' => false],
                ],
            ],
            [
                'q' => 'What is the output? `print(2 ** 3)`',
                'opts' => [
                    ['text' => '6', 'correct' => false],
                    ['text' => '8', 'correct' => true],
                    ['text' => '9', 'correct' => false],
                    ['text' => '23', 'correct' => false],
                ],
            ],
            [
                'q' => 'Which loop is best when you know you need values 1 through 5?',
                'opts' => [
                    ['text' => 'for n in range(1, 6):', 'correct' => true],
                    ['text' => 'while forever:', 'correct' => false],
                    ['text' => 'for n in 5:', 'correct' => false],
                    ['text' => 'repeat 5 times:', 'correct' => false],
                ],
            ],
            [
                'q' => 'What does `break` do inside a loop?',
                'opts' => [
                    ['text' => 'Skips the current iteration only', 'correct' => false],
                    ['text' => 'Stops the loop', 'correct' => true],
                    ['text' => 'Deletes the variable', 'correct' => false],
                    ['text' => 'Restarts the program', 'correct' => false],
                ],
            ],
            [
                'q' => 'What does `continue` do inside a loop?',
                'opts' => [
                    ['text' => 'Stops the loop permanently', 'correct' => false],
                    ['text' => 'Skips to the next iteration', 'correct' => true],
                    ['text' => 'Returns from every function', 'correct' => false],
                    ['text' => 'Prints a blank line', 'correct' => false],
                ],
            ],
            [
                'q' => 'Which expression is True when `x` is between 1 and 10 inclusive?',
                'opts' => [
                    ['text' => 'x >= 1 and x <= 10', 'correct' => true],
                    ['text' => 'x >= 1 or x <= 10', 'correct' => false],
                    ['text' => 'x = 1 and x = 10', 'correct' => false],
                    ['text' => '1 > x > 10', 'correct' => false],
                ],
            ],
            [
                'q' => 'What is the output? `x = "Data"; print(x.lower())`',
                'opts' => [
                    ['text' => 'DATA', 'correct' => false],
                    ['text' => 'data', 'correct' => true],
                    ['text' => 'Data', 'correct' => false],
                    ['text' => 'Error', 'correct' => false],
                ],
            ],
            [
                'q' => 'What is the output? `print("  hi  ".strip())`',
                'opts' => [
                    ['text' => '  hi  ', 'correct' => false],
                    ['text' => 'hi', 'correct' => true],
                    ['text' => 'h i', 'correct' => false],
                    ['text' => 'Error', 'correct' => false],
                ],
            ],
            [
                'q' => 'Which statement checks equality?',
                'opts' => [
                    ['text' => '=', 'correct' => false],
                    ['text' => '==', 'correct' => true],
                    ['text' => '===', 'correct' => false],
                    ['text' => 'is equal', 'correct' => false],
                ],
            ],
            [
                'q' => 'What is a common cause of `NameError`?',
                'opts' => [
                    ['text' => 'Using a variable before defining it', 'correct' => true],
                    ['text' => 'Dividing by 1', 'correct' => false],
                    ['text' => 'Printing a string', 'correct' => false],
                    ['text' => 'Using comments', 'correct' => false],
                ],
            ],
            [
                'q' => 'What is a common cause of `SyntaxError`?',
                'opts' => [
                    ['text' => 'Wrong grammar such as missing colon after if', 'correct' => true],
                    ['text' => 'A correct loop', 'correct' => false],
                    ['text' => 'A valid variable name', 'correct' => false],
                    ['text' => 'A printed result', 'correct' => false],
                ],
            ],
            [
                'q' => 'What is the output? `items = []; print(len(items))`',
                'opts' => [
                    ['text' => '0', 'correct' => true],
                    ['text' => '1', 'correct' => false],
                    ['text' => 'None', 'correct' => false],
                    ['text' => 'Error', 'correct' => false],
                ],
            ],
            [
                'q' => 'Which expression creates a list of three numbers?',
                'opts' => [
                    ['text' => '(1, 2, 3)', 'correct' => false],
                    ['text' => '[1, 2, 3]', 'correct' => true],
                    ['text' => '{1, 2, 3}', 'correct' => false],
                    ['text' => '<1, 2, 3>', 'correct' => false],
                ],
            ],
            [
                'q' => 'What is the output? `print("cat" in "concatenate")`',
                'opts' => [
                    ['text' => 'True', 'correct' => true],
                    ['text' => 'False', 'correct' => false],
                    ['text' => 'cat', 'correct' => false],
                    ['text' => 'Error', 'correct' => false],
                ],
            ],
            [
                'q' => 'Which code prints only even numbers from list `nums`?',
                'opts' => [
                    ['text' => 'if n % 2 == 0: print(n)', 'correct' => true],
                    ['text' => 'if n / 2: print(n)', 'correct' => false],
                    ['text' => 'if n % 2 == 1: print(n)', 'correct' => false],
                    ['text' => 'if n == even: print(n)', 'correct' => false],
                ],
            ],
            [
                'q' => 'What is the output? `a=2; b=3; print(a,b)`',
                'opts' => [
                    ['text' => '23', 'correct' => false],
                    ['text' => '2 3', 'correct' => true],
                    ['text' => 'a b', 'correct' => false],
                    ['text' => '5', 'correct' => false],
                ],
            ],
            [
                'q' => 'What should you test when solving a beginner Python problem?',
                'opts' => [
                    ['text' => 'Only the sample input', 'correct' => false],
                    ['text' => 'Sample cases plus edge cases like zero, empty text, or small values', 'correct' => true],
                    ['text' => 'Only the fastest computer', 'correct' => false],
                    ['text' => 'Nothing if the code runs once', 'correct' => false],
                ],
            ],
            [
                'q' => 'Which habit best prevents beginner Python bugs?',
                'opts' => [
                    ['text' => 'Guessing outputs without running', 'correct' => false],
                    ['text' => 'Using meaningful variable names and checking each step', 'correct' => true],
                    ['text' => 'Ignoring errors', 'correct' => false],
                    ['text' => 'Putting all code on one long line', 'correct' => false],
                ],
            ],
        ];

        foreach ($qaData as $item) {
            $question = ChallengeQuestion::create([
                'challenge_id' => $challenge->id,
                'challenge_category_id' => $category->id,
                'question_text' => $item['q'],
            ]);

            $correctCount = 0;
            foreach ($item['opts'] as $option) {
                if ($option['correct']) {
                    $correctCount++;
                }

                ChallengeOption::create([
                    'challenge_question_id' => $question->id,
                    'option_text' => $option['text'],
                    'is_correct' => $option['correct'],
                ]);
            }

            if ($correctCount !== 1) {
                throw new \RuntimeException('Each MCQ item must have exactly one correct answer. Failed question: ' . $item['q']);
            }
        }

        $this->command->info('Module 1 MCQ (Newbie) seeded — 1 challenge, 50 questions, 200 options.');
    }
}
