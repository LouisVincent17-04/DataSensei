<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\CodingQuestion;
use App\Models\TestCase;

class Module7CodingChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (! $category) {
            $this->command->error('Professional category not found. Run ChallengeCategorySeeder first.');
            return;
        }

        $title = 'Algorithms & Data Structures for Data Scientists';

        Challenge::where('challenge_category_id', $category->id)
            ->where('title', $title)
            ->where('is_coding_challenge', 1)
            ->delete();

        $this->command->info('Creating Module 7 — Algorithms & Data Structures for Data Scientists (Professional) [Coding]...');

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title' => $title,
            'description' => 'A detailed 50-item coding challenge for Algorithms & Data Structures for Data Scientists at the Professional level. Each item includes visible and hidden tests, standard input parsing, and exact output requirements.',
            'time_limit_seconds' => 4800,
            'base_xp' => 1700,
            'order_index' => 7,
            'is_coding_challenge' => 1,
        ]);

        $questionDefs = [
            [
                'order_index' => 1,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 1: Transform values

Read integers and print each value doubled, separated by spaces.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Double each value
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '1 2 3', 'expected_output' => '2 4 6', 'is_hidden' => false],
                    ['input' => '0 -2', 'expected_output' => '0 -4', 'is_hidden' => false],
                    ['input' => '5', 'expected_output' => '10', 'is_hidden' => true],
                    ['input' => '10 20', 'expected_output' => '20 40', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 2,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 2: Sum a list of numbers

Read space-separated integers and print their sum.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Read integers from input and print the sum
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '1 2 3', 'expected_output' => '6', 'is_hidden' => false],
                    ['input' => '10 -5 4', 'expected_output' => '9', 'is_hidden' => false],
                    ['input' => '0', 'expected_output' => '0', 'is_hidden' => true],
                    ['input' => '100 200 300', 'expected_output' => '600', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 3,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 3: Compute a mean

Read space-separated numbers and print the arithmetic mean rounded to 2 decimal places.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Compute the mean rounded to 2 decimals
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '1 2 3', 'expected_output' => '2.00', 'is_hidden' => false],
                    ['input' => '10 20', 'expected_output' => '15.00', 'is_hidden' => false],
                    ['input' => '5', 'expected_output' => '5.00', 'is_hidden' => true],
                    ['input' => '2 2 5', 'expected_output' => '3.00', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 4,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 4: Count values above threshold

First line is a threshold. Second line contains integers. Print how many values are greater than the threshold.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Count values greater than the threshold
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '5
1 6 7 5', 'expected_output' => '2', 'is_hidden' => false],
                    ['input' => '10
10 11 9 12', 'expected_output' => '2', 'is_hidden' => false],
                    ['input' => '0
-1 0 1', 'expected_output' => '1', 'is_hidden' => true],
                    ['input' => '3
4 5 6', 'expected_output' => '3', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 5,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 5: Compute range

Read integers and print the difference between the maximum and minimum value.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Print max minus min
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '1 5 9', 'expected_output' => '8', 'is_hidden' => false],
                    ['input' => '10 10', 'expected_output' => '0', 'is_hidden' => false],
                    ['input' => '-3 7 2', 'expected_output' => '10', 'is_hidden' => true],
                    ['input' => '100', 'expected_output' => '0', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 6,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 6: Min-max normalize values

Read integers. Print min-max normalized values rounded to 2 decimals separated by spaces. If all values are equal, print zeros.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Normalize values to 0..1
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '0 5 10', 'expected_output' => '0.00 0.50 1.00', 'is_hidden' => false],
                    ['input' => '2 2 2', 'expected_output' => '0.00 0.00 0.00', 'is_hidden' => false],
                    ['input' => '1 3', 'expected_output' => '0.00 1.00', 'is_hidden' => true],
                    ['input' => '-1 0 1', 'expected_output' => '0.00 0.50 1.00', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 7,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 7: Count words

Read one line of text and print the number of words.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Count words in a line
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => 'data science is fun', 'expected_output' => '4', 'is_hidden' => false],
                    ['input' => 'hello', 'expected_output' => '1', 'is_hidden' => false],
                    ['input' => 'one two  three', 'expected_output' => '3', 'is_hidden' => true],
                    ['input' => 'Python coding challenge', 'expected_output' => '3', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 8,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 8: Reverse word order

Read one line and print the words in reverse order separated by spaces.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Reverse the words
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => 'data science is fun', 'expected_output' => 'fun is science data', 'is_hidden' => false],
                    ['input' => 'hello world', 'expected_output' => 'world hello', 'is_hidden' => false],
                    ['input' => 'a b c', 'expected_output' => 'c b a', 'is_hidden' => true],
                    ['input' => 'single', 'expected_output' => 'single', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 9,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 9: Count unique values

Read space-separated values and print the number of unique values.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Count unique values
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => 'a b a c', 'expected_output' => '3', 'is_hidden' => false],
                    ['input' => '1 1 1', 'expected_output' => '1', 'is_hidden' => false],
                    ['input' => 'x y z', 'expected_output' => '3', 'is_hidden' => true],
                    ['input' => 'red blue red', 'expected_output' => '2', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 10,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 10: Find most frequent value

Read space-separated values and print the value that appears most often. If tied, print the value that appears first.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Find the mode with first-tie rule
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => 'a b a c', 'expected_output' => 'a', 'is_hidden' => false],
                    ['input' => '1 2 2 1', 'expected_output' => '1', 'is_hidden' => false],
                    ['input' => 'x y y z', 'expected_output' => 'y', 'is_hidden' => true],
                    ['input' => 'red blue red blue red', 'expected_output' => 'red', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 11,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 11: Filter even numbers

Read integers and print only the even values separated by spaces. Print NONE if there are no even values.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Print even values or NONE
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '1 2 3 4', 'expected_output' => '2 4', 'is_hidden' => false],
                    ['input' => '1 3 5', 'expected_output' => 'NONE', 'is_hidden' => false],
                    ['input' => '0 2 8', 'expected_output' => '0 2 8', 'is_hidden' => true],
                    ['input' => '-2 -1 6', 'expected_output' => '-2 6', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 12,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 12: Two-point moving average

Read numbers and print the average of every adjacent pair rounded to 2 decimals.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Adjacent-pair averages
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '2 4 6', 'expected_output' => '3.00 5.00', 'is_hidden' => false],
                    ['input' => '1 2', 'expected_output' => '1.50', 'is_hidden' => false],
                    ['input' => '10 20 40 80', 'expected_output' => '15.00 30.00 60.00', 'is_hidden' => true],
                    ['input' => '5 5 5', 'expected_output' => '5.00 5.00', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 13,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 13: Compute accuracy

First line has true labels. Second line has predicted labels. Print accuracy as a percentage rounded to 2 decimals.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Compute classification accuracy
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => 'A B A
A B B', 'expected_output' => '66.67', 'is_hidden' => false],
                    ['input' => '1 0 1 1
1 0 1 1', 'expected_output' => '100.00', 'is_hidden' => false],
                    ['input' => 'cat dog
dog dog', 'expected_output' => '50.00', 'is_hidden' => true],
                    ['input' => 'yes no yes
no no yes', 'expected_output' => '66.67', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 14,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 14: Dot product

Read vector A on the first line and vector B on the second line. Print their dot product.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Compute dot product
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '1 2 3
4 5 6', 'expected_output' => '32', 'is_hidden' => false],
                    ['input' => '2 0
3 4', 'expected_output' => '6', 'is_hidden' => false],
                    ['input' => '-1 2
3 -4', 'expected_output' => '-11', 'is_hidden' => true],
                    ['input' => '5
6', 'expected_output' => '30', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 15,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 15: Matrix trace

First line contains n. Next n lines contain an n x n matrix. Print the sum of the main diagonal.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Compute matrix trace
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '2
1 2
3 4', 'expected_output' => '5', 'is_hidden' => false],
                    ['input' => '3
1 0 0
0 2 0
0 0 3', 'expected_output' => '6', 'is_hidden' => false],
                    ['input' => '1
9', 'expected_output' => '9', 'is_hidden' => true],
                    ['input' => '2
-1 5
7 -2', 'expected_output' => '-3', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 16,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 16: Weighted score

First line contains scores. Second line contains matching weights. Print weighted average rounded to 2 decimals.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Weighted average
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '80 90
0.5 0.5', 'expected_output' => '85.00', 'is_hidden' => false],
                    ['input' => '1 2 3
1 1 1', 'expected_output' => '2.00', 'is_hidden' => false],
                    ['input' => '10 20
2 1', 'expected_output' => '13.33', 'is_hidden' => true],
                    ['input' => '100 50
0.25 0.75', 'expected_output' => '62.50', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 17,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 17: Simple linear forecast

Read numbers representing a sequence with a constant step. Print the next value.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Forecast next value in an arithmetic sequence
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '2 4 6 8', 'expected_output' => '10', 'is_hidden' => false],
                    ['input' => '10 7 4', 'expected_output' => '1', 'is_hidden' => false],
                    ['input' => '5 5 5', 'expected_output' => '5', 'is_hidden' => true],
                    ['input' => '1 3', 'expected_output' => '5', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 18,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 18: Greedy coin count

Read an amount in pesos. Using denominations 20, 10, 5, 1, print the minimum coin/bill count.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Greedy count for 20,10,5,1
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '37', 'expected_output' => '5', 'is_hidden' => false],
                    ['input' => '40', 'expected_output' => '2', 'is_hidden' => false],
                    ['input' => '4', 'expected_output' => '4', 'is_hidden' => true],
                    ['input' => '0', 'expected_output' => '0', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 19,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 19: Jaccard similarity

Read set A on the first line and set B on the second line. Print Jaccard similarity rounded to 2 decimals.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Compute Jaccard similarity
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => 'a b c
b c d', 'expected_output' => '0.50', 'is_hidden' => false],
                    ['input' => 'x y
x y', 'expected_output' => '1.00', 'is_hidden' => false],
                    ['input' => 'a
b', 'expected_output' => '0.00', 'is_hidden' => true],
                    ['input' => '1 2 3
3 4', 'expected_output' => '0.25', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 20,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 20: CSV column sum

Read comma-separated rows. Each row has name,value. Print the sum of the numeric values.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Sum numeric column from name,value rows
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => 'a,2
b,3', 'expected_output' => '5', 'is_hidden' => false],
                    ['input' => 'x,10', 'expected_output' => '10', 'is_hidden' => false],
                    ['input' => 'p,-1
q,5', 'expected_output' => '4', 'is_hidden' => true],
                    ['input' => 'one,1
two,2
three,3', 'expected_output' => '6', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 21,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 21: Transform values

Read integers and print each value doubled, separated by spaces.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Double each value
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '1 2 3', 'expected_output' => '2 4 6', 'is_hidden' => false],
                    ['input' => '0 -2', 'expected_output' => '0 -4', 'is_hidden' => false],
                    ['input' => '5', 'expected_output' => '10', 'is_hidden' => true],
                    ['input' => '10 20', 'expected_output' => '20 40', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 22,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 22: Sum a list of numbers

Read space-separated integers and print their sum.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Read integers from input and print the sum
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '1 2 3', 'expected_output' => '6', 'is_hidden' => false],
                    ['input' => '10 -5 4', 'expected_output' => '9', 'is_hidden' => false],
                    ['input' => '0', 'expected_output' => '0', 'is_hidden' => true],
                    ['input' => '100 200 300', 'expected_output' => '600', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 23,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 23: Compute a mean

Read space-separated numbers and print the arithmetic mean rounded to 2 decimal places.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Compute the mean rounded to 2 decimals
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '1 2 3', 'expected_output' => '2.00', 'is_hidden' => false],
                    ['input' => '10 20', 'expected_output' => '15.00', 'is_hidden' => false],
                    ['input' => '5', 'expected_output' => '5.00', 'is_hidden' => true],
                    ['input' => '2 2 5', 'expected_output' => '3.00', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 24,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 24: Count values above threshold

First line is a threshold. Second line contains integers. Print how many values are greater than the threshold.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Count values greater than the threshold
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '5
1 6 7 5', 'expected_output' => '2', 'is_hidden' => false],
                    ['input' => '10
10 11 9 12', 'expected_output' => '2', 'is_hidden' => false],
                    ['input' => '0
-1 0 1', 'expected_output' => '1', 'is_hidden' => true],
                    ['input' => '3
4 5 6', 'expected_output' => '3', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 25,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 25: Compute range

Read integers and print the difference between the maximum and minimum value.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Print max minus min
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '1 5 9', 'expected_output' => '8', 'is_hidden' => false],
                    ['input' => '10 10', 'expected_output' => '0', 'is_hidden' => false],
                    ['input' => '-3 7 2', 'expected_output' => '10', 'is_hidden' => true],
                    ['input' => '100', 'expected_output' => '0', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 26,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 26: Min-max normalize values

Read integers. Print min-max normalized values rounded to 2 decimals separated by spaces. If all values are equal, print zeros.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Normalize values to 0..1
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '0 5 10', 'expected_output' => '0.00 0.50 1.00', 'is_hidden' => false],
                    ['input' => '2 2 2', 'expected_output' => '0.00 0.00 0.00', 'is_hidden' => false],
                    ['input' => '1 3', 'expected_output' => '0.00 1.00', 'is_hidden' => true],
                    ['input' => '-1 0 1', 'expected_output' => '0.00 0.50 1.00', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 27,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 27: Count words

Read one line of text and print the number of words.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Count words in a line
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => 'data science is fun', 'expected_output' => '4', 'is_hidden' => false],
                    ['input' => 'hello', 'expected_output' => '1', 'is_hidden' => false],
                    ['input' => 'one two  three', 'expected_output' => '3', 'is_hidden' => true],
                    ['input' => 'Python coding challenge', 'expected_output' => '3', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 28,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 28: Reverse word order

Read one line and print the words in reverse order separated by spaces.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Reverse the words
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => 'data science is fun', 'expected_output' => 'fun is science data', 'is_hidden' => false],
                    ['input' => 'hello world', 'expected_output' => 'world hello', 'is_hidden' => false],
                    ['input' => 'a b c', 'expected_output' => 'c b a', 'is_hidden' => true],
                    ['input' => 'single', 'expected_output' => 'single', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 29,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 29: Count unique values

Read space-separated values and print the number of unique values.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Count unique values
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => 'a b a c', 'expected_output' => '3', 'is_hidden' => false],
                    ['input' => '1 1 1', 'expected_output' => '1', 'is_hidden' => false],
                    ['input' => 'x y z', 'expected_output' => '3', 'is_hidden' => true],
                    ['input' => 'red blue red', 'expected_output' => '2', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 30,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 30: Find most frequent value

Read space-separated values and print the value that appears most often. If tied, print the value that appears first.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Find the mode with first-tie rule
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => 'a b a c', 'expected_output' => 'a', 'is_hidden' => false],
                    ['input' => '1 2 2 1', 'expected_output' => '1', 'is_hidden' => false],
                    ['input' => 'x y y z', 'expected_output' => 'y', 'is_hidden' => true],
                    ['input' => 'red blue red blue red', 'expected_output' => 'red', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 31,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 31: Filter even numbers

Read integers and print only the even values separated by spaces. Print NONE if there are no even values.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Print even values or NONE
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '1 2 3 4', 'expected_output' => '2 4', 'is_hidden' => false],
                    ['input' => '1 3 5', 'expected_output' => 'NONE', 'is_hidden' => false],
                    ['input' => '0 2 8', 'expected_output' => '0 2 8', 'is_hidden' => true],
                    ['input' => '-2 -1 6', 'expected_output' => '-2 6', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 32,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 32: Two-point moving average

Read numbers and print the average of every adjacent pair rounded to 2 decimals.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Adjacent-pair averages
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '2 4 6', 'expected_output' => '3.00 5.00', 'is_hidden' => false],
                    ['input' => '1 2', 'expected_output' => '1.50', 'is_hidden' => false],
                    ['input' => '10 20 40 80', 'expected_output' => '15.00 30.00 60.00', 'is_hidden' => true],
                    ['input' => '5 5 5', 'expected_output' => '5.00 5.00', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 33,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 33: Compute accuracy

First line has true labels. Second line has predicted labels. Print accuracy as a percentage rounded to 2 decimals.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Compute classification accuracy
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => 'A B A
A B B', 'expected_output' => '66.67', 'is_hidden' => false],
                    ['input' => '1 0 1 1
1 0 1 1', 'expected_output' => '100.00', 'is_hidden' => false],
                    ['input' => 'cat dog
dog dog', 'expected_output' => '50.00', 'is_hidden' => true],
                    ['input' => 'yes no yes
no no yes', 'expected_output' => '66.67', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 34,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 34: Dot product

Read vector A on the first line and vector B on the second line. Print their dot product.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Compute dot product
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '1 2 3
4 5 6', 'expected_output' => '32', 'is_hidden' => false],
                    ['input' => '2 0
3 4', 'expected_output' => '6', 'is_hidden' => false],
                    ['input' => '-1 2
3 -4', 'expected_output' => '-11', 'is_hidden' => true],
                    ['input' => '5
6', 'expected_output' => '30', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 35,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 35: Matrix trace

First line contains n. Next n lines contain an n x n matrix. Print the sum of the main diagonal.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Compute matrix trace
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '2
1 2
3 4', 'expected_output' => '5', 'is_hidden' => false],
                    ['input' => '3
1 0 0
0 2 0
0 0 3', 'expected_output' => '6', 'is_hidden' => false],
                    ['input' => '1
9', 'expected_output' => '9', 'is_hidden' => true],
                    ['input' => '2
-1 5
7 -2', 'expected_output' => '-3', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 36,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 36: Weighted score

First line contains scores. Second line contains matching weights. Print weighted average rounded to 2 decimals.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Weighted average
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '80 90
0.5 0.5', 'expected_output' => '85.00', 'is_hidden' => false],
                    ['input' => '1 2 3
1 1 1', 'expected_output' => '2.00', 'is_hidden' => false],
                    ['input' => '10 20
2 1', 'expected_output' => '13.33', 'is_hidden' => true],
                    ['input' => '100 50
0.25 0.75', 'expected_output' => '62.50', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 37,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 37: Simple linear forecast

Read numbers representing a sequence with a constant step. Print the next value.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Forecast next value in an arithmetic sequence
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '2 4 6 8', 'expected_output' => '10', 'is_hidden' => false],
                    ['input' => '10 7 4', 'expected_output' => '1', 'is_hidden' => false],
                    ['input' => '5 5 5', 'expected_output' => '5', 'is_hidden' => true],
                    ['input' => '1 3', 'expected_output' => '5', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 38,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 38: Greedy coin count

Read an amount in pesos. Using denominations 20, 10, 5, 1, print the minimum coin/bill count.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Greedy count for 20,10,5,1
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '37', 'expected_output' => '5', 'is_hidden' => false],
                    ['input' => '40', 'expected_output' => '2', 'is_hidden' => false],
                    ['input' => '4', 'expected_output' => '4', 'is_hidden' => true],
                    ['input' => '0', 'expected_output' => '0', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 39,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 39: Jaccard similarity

Read set A on the first line and set B on the second line. Print Jaccard similarity rounded to 2 decimals.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Compute Jaccard similarity
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => 'a b c
b c d', 'expected_output' => '0.50', 'is_hidden' => false],
                    ['input' => 'x y
x y', 'expected_output' => '1.00', 'is_hidden' => false],
                    ['input' => 'a
b', 'expected_output' => '0.00', 'is_hidden' => true],
                    ['input' => '1 2 3
3 4', 'expected_output' => '0.25', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 40,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 40: CSV column sum

Read comma-separated rows. Each row has name,value. Print the sum of the numeric values.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Sum numeric column from name,value rows
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => 'a,2
b,3', 'expected_output' => '5', 'is_hidden' => false],
                    ['input' => 'x,10', 'expected_output' => '10', 'is_hidden' => false],
                    ['input' => 'p,-1
q,5', 'expected_output' => '4', 'is_hidden' => true],
                    ['input' => 'one,1
two,2
three,3', 'expected_output' => '6', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 41,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 41: Transform values

Read integers and print each value doubled, separated by spaces.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Double each value
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '1 2 3', 'expected_output' => '2 4 6', 'is_hidden' => false],
                    ['input' => '0 -2', 'expected_output' => '0 -4', 'is_hidden' => false],
                    ['input' => '5', 'expected_output' => '10', 'is_hidden' => true],
                    ['input' => '10 20', 'expected_output' => '20 40', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 42,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 42: Sum a list of numbers

Read space-separated integers and print their sum.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Read integers from input and print the sum
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '1 2 3', 'expected_output' => '6', 'is_hidden' => false],
                    ['input' => '10 -5 4', 'expected_output' => '9', 'is_hidden' => false],
                    ['input' => '0', 'expected_output' => '0', 'is_hidden' => true],
                    ['input' => '100 200 300', 'expected_output' => '600', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 43,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 43: Compute a mean

Read space-separated numbers and print the arithmetic mean rounded to 2 decimal places.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Compute the mean rounded to 2 decimals
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '1 2 3', 'expected_output' => '2.00', 'is_hidden' => false],
                    ['input' => '10 20', 'expected_output' => '15.00', 'is_hidden' => false],
                    ['input' => '5', 'expected_output' => '5.00', 'is_hidden' => true],
                    ['input' => '2 2 5', 'expected_output' => '3.00', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 44,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 44: Count values above threshold

First line is a threshold. Second line contains integers. Print how many values are greater than the threshold.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Count values greater than the threshold
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '5
1 6 7 5', 'expected_output' => '2', 'is_hidden' => false],
                    ['input' => '10
10 11 9 12', 'expected_output' => '2', 'is_hidden' => false],
                    ['input' => '0
-1 0 1', 'expected_output' => '1', 'is_hidden' => true],
                    ['input' => '3
4 5 6', 'expected_output' => '3', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 45,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 45: Compute range

Read integers and print the difference between the maximum and minimum value.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Print max minus min
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '1 5 9', 'expected_output' => '8', 'is_hidden' => false],
                    ['input' => '10 10', 'expected_output' => '0', 'is_hidden' => false],
                    ['input' => '-3 7 2', 'expected_output' => '10', 'is_hidden' => true],
                    ['input' => '100', 'expected_output' => '0', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 46,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 46: Min-max normalize values

Read integers. Print min-max normalized values rounded to 2 decimals separated by spaces. If all values are equal, print zeros.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Normalize values to 0..1
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => '0 5 10', 'expected_output' => '0.00 0.50 1.00', 'is_hidden' => false],
                    ['input' => '2 2 2', 'expected_output' => '0.00 0.00 0.00', 'is_hidden' => false],
                    ['input' => '1 3', 'expected_output' => '0.00 1.00', 'is_hidden' => true],
                    ['input' => '-1 0 1', 'expected_output' => '0.00 0.50 1.00', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 47,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 47: Count words

Read one line of text and print the number of words.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Count words in a line
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => 'data science is fun', 'expected_output' => '4', 'is_hidden' => false],
                    ['input' => 'hello', 'expected_output' => '1', 'is_hidden' => false],
                    ['input' => 'one two  three', 'expected_output' => '3', 'is_hidden' => true],
                    ['input' => 'Python coding challenge', 'expected_output' => '3', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 48,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 48: Reverse word order

Read one line and print the words in reverse order separated by spaces.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Reverse the words
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => 'data science is fun', 'expected_output' => 'fun is science data', 'is_hidden' => false],
                    ['input' => 'hello world', 'expected_output' => 'world hello', 'is_hidden' => false],
                    ['input' => 'a b c', 'expected_output' => 'c b a', 'is_hidden' => true],
                    ['input' => 'single', 'expected_output' => 'single', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 49,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 49: Count unique values

Read space-separated values and print the number of unique values.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Count unique values
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => 'a b a c', 'expected_output' => '3', 'is_hidden' => false],
                    ['input' => '1 1 1', 'expected_output' => '1', 'is_hidden' => false],
                    ['input' => 'x y z', 'expected_output' => '3', 'is_hidden' => true],
                    ['input' => 'red blue red', 'expected_output' => '2', 'is_hidden' => true],
                ],
            ],
            [
                'order_index' => 50,
                'problem_description' => '### Module 7: Algorithms & Data Structures for Data Scientists
### Difficulty: Professional
### Task 50: Find most frequent value

Read space-separated values and print the value that appears most often. If tied, print the value that appears first.

Write maintainable, production-style logic with clear parsing and no hardcoded sample outputs.

Input must be read from standard input. Output must match exactly. Avoid hardcoding the sample values because hidden tests use different data.',
                'starter_code' => '# Find the mode with first-tie rule
import sys

# Your code starts here
',
                'time_limit_seconds' => 1800,
                'base_xp' => 250,
                'test_cases' => [
                    ['input' => 'a b a c', 'expected_output' => 'a', 'is_hidden' => false],
                    ['input' => '1 2 2 1', 'expected_output' => '1', 'is_hidden' => false],
                    ['input' => 'x y y z', 'expected_output' => 'y', 'is_hidden' => true],
                    ['input' => 'red blue red blue red', 'expected_output' => 'red', 'is_hidden' => true],
                ],
            ],
        ];

        foreach ($questionDefs as $questionDef) {
            $codingQuestion = CodingQuestion::create([
                'challenge_id' => $challenge->id,
                'problem_description' => $questionDef['problem_description'],
                'language' => 'python',
                'starter_code' => $questionDef['starter_code'],
                'order_index' => $questionDef['order_index'],
                'time_limit_seconds' => $questionDef['time_limit_seconds'],
                'base_xp' => $questionDef['base_xp'],
            ]);

            foreach ($questionDef['test_cases'] as $caseIndex => $testCase) {
                TestCase::create([
                    'coding_question_id' => $codingQuestion->id,
                    'input' => $testCase['input'],
                    'expected_output' => $testCase['expected_output'],
                    'is_hidden' => $testCase['is_hidden'],
                    'order_index' => $caseIndex + 1,
                ]);
            }
        }

        $this->command->info('Module 7 Coding (Professional) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}
