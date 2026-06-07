<?php

namespace Database\Seeders;

use App\Models\Challenge;
use App\Models\ChallengeCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Module 23 — Data Warehousing (Advanced) — CODING variant
 *
 * Auto-generated to replace a missing/empty coding challenge seeder.
 * Seeds:
 *   - 1 coding challenge row in challenges
 *   - 50 coding_questions
 *   - 200 test_cases, 4 per question
 *
 * Safe to re-run: questions are updated by challenge/order_index and
 * test cases are refreshed for the matching coding questions.
 */
class Module23CodingChallengeSeederAdvanced extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'advanced')->first();

        if (! $category) {
            $this->command->error('Advanced category not found! Run ChallengeCategorySeeder first.');
            return;
        }

        $this->command->info('Creating Module 23 — Data Warehousing (Advanced) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title' => 'Data Warehousing',
                'is_coding_challenge' => 1,
            ],
            [
                'description' => 'Hands-on Python coding tasks for Data Warehousing. This Advanced track contains 50 judge-tested problems covering ETL, dimensions, facts, warehouse schemas, and analytical aggregates. Each task includes visible and hidden test cases and is safe to reseed.',
                'time_limit_seconds' => 3000,
                'base_xp' => 1200,
                'order_index' => 23,
            ]
        );

        $questionDefs = [
            [
                'order_index' => 1,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 1: Count even observations

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read n, then n integers. Print how many are even.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
n = int(input())
# Count even numbers
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '5\n1\n2\n3\n4\n5', 'expected_output' => '2', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '4\n2\n4\n6\n8', 'expected_output' => '4', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '3\n1\n3\n5', 'expected_output' => '0', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '6\n0\n-2\n-3\n7\n10\n11', 'expected_output' => '3', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 2,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 2: Count odd observations

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read n, then n integers. Print how many are odd.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
n = int(input())
# Count odd numbers
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '5\n1\n2\n3\n4\n5', 'expected_output' => '3', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '4\n2\n4\n6\n8', 'expected_output' => '0', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '3\n1\n3\n5', 'expected_output' => '3', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '6\n0\n-2\n-3\n7\n10\n11', 'expected_output' => '3', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 3,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 3: Find the maximum

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read n, then n integers. Print the maximum value.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
n = int(input())
# Read numbers and print max
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '5\n3\n1\n4\n1\n5', 'expected_output' => '5', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '3\n-10\n-2\n-7', 'expected_output' => '-2', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '1\n42', 'expected_output' => '42', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '4\n9\n9\n8\n7', 'expected_output' => '9', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 4,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 4: Find the minimum

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read n, then n integers. Print the minimum value.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
n = int(input())
# Read numbers and print min
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '5\n3\n1\n4\n1\n5', 'expected_output' => '1', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '3\n-10\n-2\n-7', 'expected_output' => '-10', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '1\n42', 'expected_output' => '42', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '4\n9\n9\n8\n7', 'expected_output' => '7', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 5,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 5: Compute a range

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read n, then n integers. Print maximum minus minimum.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
n = int(input())
# Print max - min
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '5\n3\n1\n4\n1\n5', 'expected_output' => '4', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '3\n10\n20\n30', 'expected_output' => '20', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '4\n7\n7\n7\n7', 'expected_output' => '0', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '3\n-5\n0\n5', 'expected_output' => '10', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 6,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 6: Median of odd-length data

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read an odd n, then n integers. Sort the values and print the median.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
n = int(input())
# Print the median value
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '5\n3\n1\n4\n1\n5', 'expected_output' => '3', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '3\n10\n20\n30', 'expected_output' => '20', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '1\n99', 'expected_output' => '99', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '7\n7\n6\n5\n4\n3\n2\n1', 'expected_output' => '4', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 7,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 7: Population variance

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read n, then n numbers. Print the population variance rounded to 2 decimals.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
n = int(input())
# Print population variance
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '4\n1\n2\n3\n4', 'expected_output' => '1.25', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '3\n2\n2\n2', 'expected_output' => '0.0', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '2\n0\n2', 'expected_output' => '1.0', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '5\n1\n1\n2\n2\n4', 'expected_output' => '1.2', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 8,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 8: Normalize with min-max scaling

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read n, then n numbers. Print the min-max normalized values rounded to 2 decimals as a Python list. If all values are equal, print zeros.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
n = int(input())
# Print normalized list
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '3\n10\n20\n30', 'expected_output' => '[0.0, 0.5, 1.0]', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '4\n5\n5\n5\n5', 'expected_output' => '[0, 0, 0, 0]', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '3\n2\n4\n6', 'expected_output' => '[0.0, 0.5, 1.0]', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '2\n-1\n1', 'expected_output' => '[0.0, 1.0]', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 9,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 9: Count values above a threshold

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read a threshold, then n, then n numbers. Print how many values are greater than the threshold.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
threshold = float(input())
n = int(input())
# Count values greater than threshold
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '10\n5\n8\n10\n11\n15\n4', 'expected_output' => '2', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '0\n3\n-1\n0\n1', 'expected_output' => '1', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '5\n4\n6\n7\n8\n9', 'expected_output' => '4', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '2\n3\n2\n2\n2', 'expected_output' => '0', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 10,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 10: Filter values at least threshold

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read threshold, n, then n integers. Print a Python list of values greater than or equal to the threshold.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
threshold = int(input())
n = int(input())
# Print filtered list
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '10\n5\n8\n10\n11\n15\n4', 'expected_output' => '[10, 11, 15]', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '0\n3\n-1\n0\n1', 'expected_output' => '[0, 1]', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '5\n4\n6\n7\n8\n9', 'expected_output' => '[6, 7, 8, 9]', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '2\n3\n1\n1\n1', 'expected_output' => '[]', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 11,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 11: Reverse token order

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read one line of space-separated tokens and print them in reverse order separated by spaces.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
line = input()
# Reverse the token order
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => 'data science is fun', 'expected_output' => 'fun is science data', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => 'one two', 'expected_output' => 'two one', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => 'a b c d', 'expected_output' => 'd c b a', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => 'solo', 'expected_output' => 'solo', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 12,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 12: Count non-space characters

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read one line and print the number of characters excluding spaces.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
text = input()
# Count characters that are not spaces
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => 'data science', 'expected_output' => '11', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => 'a b c', 'expected_output' => '3', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => 'no_spaces', 'expected_output' => '9', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => 'two  spaces', 'expected_output' => '9', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 13,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 13: Word frequency table

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read one line. Print each unique word and its count as word:count, sorted alphabetically, one per line.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
line = input()
# Print sorted word frequency table
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => 'data data model', 'expected_output' => 'data:2\nmodel:1', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => 'b a b c a b', 'expected_output' => 'a:2\nb:3\nc:1', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => 'one', 'expected_output' => 'one:1', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => 'red blue red', 'expected_output' => 'blue:1\nred:2', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 14,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 14: Unique sorted values

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read n, then n strings. Print the unique values sorted alphabetically as a Python list.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
n = int(input())
# Print sorted unique values
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '5\nred\nblue\nred\ngreen\nblue', 'expected_output' => '[\'blue\', \'green\', \'red\']', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '3\na\na\na', 'expected_output' => '[\'a\']', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '4\nz\ny\nx\ny', 'expected_output' => '[\'x\', \'y\', \'z\']', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '1\nsolo', 'expected_output' => '[\'solo\']', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 15,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 15: Dot product

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read n, then n numbers for vector A and n numbers for vector B. Print their dot product.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
n = int(input())
# Read two vectors and print dot product
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '3\n1\n2\n3\n4\n5\n6', 'expected_output' => '32', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '2\n10\n20\n1\n2', 'expected_output' => '50', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '3\n0\n1\n0\n5\n6\n7', 'expected_output' => '6', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '4\n1\n1\n1\n1\n2\n2\n2\n2', 'expected_output' => '8', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 16,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 16: Euclidean distance in 2D

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read x1, y1, x2, y2. Print the Euclidean distance rounded to 2 decimals.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
x1 = float(input())
y1 = float(input())
x2 = float(input())
y2 = float(input())
# Print distance
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '0\n0\n3\n4', 'expected_output' => '5.0', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '1\n1\n1\n1', 'expected_output' => '0.0', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '-1\n-1\n2\n3', 'expected_output' => '5.0', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '0\n0\n1\n1', 'expected_output' => '1.41', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 17,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 17: Matrix row sums

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read r and c, then r*c integers row by row. Print each row sum on its own line.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
r = int(input())
c = int(input())
# Print row sums
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '2\n3\n1\n2\n3\n4\n5\n6', 'expected_output' => '6\n15', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '1\n4\n1\n1\n1\n1', 'expected_output' => '4', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '3\n2\n1\n2\n3\n4\n5\n6', 'expected_output' => '3\n7\n11', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '2\n2\n0\n0\n5\n5', 'expected_output' => '0\n10', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 18,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 18: Matrix column sums

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read r and c, then r*c integers row by row. Print column sums as a Python list.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
r = int(input())
c = int(input())
# Print column sums list
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '2\n3\n1\n2\n3\n4\n5\n6', 'expected_output' => '[5, 7, 9]', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '1\n4\n1\n1\n1\n1', 'expected_output' => '[1, 1, 1, 1]', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '3\n2\n1\n2\n3\n4\n5\n6', 'expected_output' => '[9, 12]', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '2\n2\n0\n0\n5\n5', 'expected_output' => '[5, 5]', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 19,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 19: Transpose a matrix

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read r and c, then r*c integers row by row. Print the transpose as rows of space-separated values.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
r = int(input())
c = int(input())
# Print transposed matrix
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '2\n3\n1\n2\n3\n4\n5\n6', 'expected_output' => '1 4\n2 5\n3 6', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '1\n3\n7\n8\n9', 'expected_output' => '7\n8\n9', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '3\n2\n1\n2\n3\n4\n5\n6', 'expected_output' => '1 3 5\n2 4 6', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '2\n2\n1\n0\n0\n1', 'expected_output' => '1 0\n0 1', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 20,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 20: Simple linear slope

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read n pairs x,y. Compute the simple linear regression slope rounded to 2 decimals.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
n = int(input())
# Read x,y pairs and print slope
PY,
                'time_limit_seconds' => 1200,
                'base_xp' => 350,
                'test_cases' => [
                        ['input' => '3\n1\n2\n2\n4\n3\n6', 'expected_output' => '2.0', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '3\n1\n1\n2\n2\n3\n3', 'expected_output' => '1.0', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '4\n1\n3\n2\n5\n3\n7\n4\n9', 'expected_output' => '2.0', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '3\n1\n5\n2\n5\n3\n5', 'expected_output' => '0.0', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 21,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 21: Moving average window 3

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read n, then n values. Print the moving averages with window size 3 rounded to 2 decimals as a Python list.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
n = int(input())
# Print moving average list
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '5\n1\n2\n3\n4\n5', 'expected_output' => '[2.0, 3.0, 4.0]', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '3\n10\n20\n30', 'expected_output' => '[20.0]', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '4\n2\n4\n6\n8', 'expected_output' => '[4.0, 6.0]', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '6\n1\n1\n1\n2\n2\n2', 'expected_output' => '[1.0, 1.33, 1.67, 2.0]', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 22,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 22: Cumulative sum

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read n, then n integers. Print the cumulative sums as a Python list.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
n = int(input())
# Print cumulative sum list
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '5\n1\n2\n3\n4\n5', 'expected_output' => '[1, 3, 6, 10, 15]', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '3\n10\n-5\n2', 'expected_output' => '[10, 5, 7]', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '1\n7', 'expected_output' => '[7]', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '4\n0\n0\n1\n1', 'expected_output' => '[0, 0, 1, 2]', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 23,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 23: Binary threshold labels

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read a threshold, n, then n scores. Print a list of 1 if score >= threshold else 0.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
threshold = float(input())
n = int(input())
# Print binary labels
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '0.5\n4\n0.2\n0.5\n0.7\n0.1', 'expected_output' => '[0, 1, 1, 0]', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '10\n3\n9\n10\n11', 'expected_output' => '[0, 1, 1]', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '1\n3\n1\n1\n0', 'expected_output' => '[1, 1, 0]', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '0\n2\n-1\n1', 'expected_output' => '[0, 1]', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 24,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 24: Confusion matrix counts

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read n, then n actual labels and n predicted labels. Print TP FP TN FN each on its own line.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
n = int(input())
# Read actual labels then predicted labels
# Print TP, FP, TN, FN
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 325,
                'test_cases' => [
                        ['input' => '4\n1\n0\n1\n0\n1\n1\n0\n0', 'expected_output' => '1\n1\n1\n1', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '3\n1\n1\n0\n1\n0\n0', 'expected_output' => '1\n0\n1\n1', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '2\n0\n0\n0\n1', 'expected_output' => '0\n1\n1\n0', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '2\n1\n1\n1\n1', 'expected_output' => '2\n0\n0\n0', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 25,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 25: Accuracy percentage

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read n, then n actual labels and n predicted labels. Print accuracy as a percentage rounded to 2 decimals.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
n = int(input())
# Print accuracy percentage
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '4\n1\n0\n1\n0\n1\n1\n0\n0', 'expected_output' => '50.0', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '3\n1\n1\n0\n1\n0\n0', 'expected_output' => '66.67', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '2\n0\n0\n0\n1', 'expected_output' => '50.0', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '2\n1\n1\n1\n1', 'expected_output' => '100.0', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 26,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 26: Precision score

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read TP and FP. Print precision rounded to 2 decimals. If denominator is zero, print 0.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
tp = int(input())
fp = int(input())
# Print precision
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '8\n2', 'expected_output' => '0.8', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '0\n0', 'expected_output' => '0', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '3\n1', 'expected_output' => '0.75', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '1\n3', 'expected_output' => '0.25', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 27,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 27: Recall score

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read TP and FN. Print recall rounded to 2 decimals. If denominator is zero, print 0.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
tp = int(input())
fn = int(input())
# Print recall
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '8\n2', 'expected_output' => '0.8', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '0\n0', 'expected_output' => '0', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '3\n1', 'expected_output' => '0.75', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '1\n3', 'expected_output' => '0.25', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 28,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 28: Group sum by category

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read n records, each with category and integer value. Print category totals sorted alphabetically as category:total.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
n = int(input())
# Read category and value pairs
# Print sorted totals
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 300,
                'test_cases' => [
                        ['input' => '4\nA\n10\nB\n5\nA\n2\nB\n1', 'expected_output' => 'A:12\nB:6', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '3\nx\n1\ny\n2\nx\n3', 'expected_output' => 'x:4\ny:2', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '1\nsolo\n9', 'expected_output' => 'solo:9', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '5\nb\n1\na\n2\nb\n3\nc\n4\na\n1', 'expected_output' => 'a:3\nb:4\nc:4', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 29,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 29: Group average by category

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read n records, each with category and numeric value. Print category averages rounded to 2 decimals sorted alphabetically.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
n = int(input())
# Read category and value pairs
# Print sorted averages
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 325,
                'test_cases' => [
                        ['input' => '4\nA\n10\nB\n5\nA\n2\nB\n1', 'expected_output' => 'A:6.0\nB:3.0', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '3\nx\n1\ny\n2\nx\n3', 'expected_output' => 'x:2.0\ny:2.0', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '1\nsolo\n9', 'expected_output' => 'solo:9.0', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '5\nb\n1\na\n2\nb\n3\nc\n4\na\n1', 'expected_output' => 'a:1.5\nb:2.0\nc:4.0', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 30,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 30: Top category by total

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read n category/value records. Print the category with the highest total. Break ties alphabetically.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
n = int(input())
# Print top category by total
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 325,
                'test_cases' => [
                        ['input' => '4\nA\n10\nB\n5\nA\n2\nB\n1', 'expected_output' => 'A', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '3\nx\n1\ny\n2\nx\n3', 'expected_output' => 'x', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '2\nb\n5\na\n5', 'expected_output' => 'a', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '5\na\n1\nb\n2\nc\n3\nb\n2\na\n10', 'expected_output' => 'a', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 31,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 31: Training/test split counts

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read total row count and test percentage. Print train_count and test_count, with test_count = round(total * percent / 100).

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
total = int(input())
percent = float(input())
# Print train count then test count
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '100\n20', 'expected_output' => '80\n20', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '50\n10', 'expected_output' => '45\n5', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '33\n30', 'expected_output' => '23\n10', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '10\n0', 'expected_output' => '10\n0', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 32,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 32: Standardize scores

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read n, then n numbers. Print z-scores rounded to 2 decimals as a Python list. Use population standard deviation; if std is zero, print zeros.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
n = int(input())
# Print z-score list
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 350,
                'test_cases' => [
                        ['input' => '3\n1\n2\n3', 'expected_output' => '[-1.22, 0.0, 1.22]', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '3\n5\n5\n5', 'expected_output' => '[0, 0, 0]', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '2\n0\n2', 'expected_output' => '[-1.0, 1.0]', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '4\n1\n1\n3\n3', 'expected_output' => '[-1.0, -1.0, 1.0, 1.0]', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 33,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 33: CSV line to trimmed list

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read one comma-separated line. Print a Python list of trimmed values.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
line = input()
# Print cleaned list
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => 'a,b,c', 'expected_output' => '[\'a\', \'b\', \'c\']', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => 'red, blue,green', 'expected_output' => '[\'red\', \'blue\', \'green\']', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => 'one', 'expected_output' => '[\'one\']', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => 'x, y , z ', 'expected_output' => '[\'x\', \'y\', \'z\']', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 34,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 34: Replace missing values

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read one comma-separated line. Replace NA and empty values with 0, then print the cleaned list of strings.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
line = input()
# Replace missing values and print list
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '1,NA,3', 'expected_output' => '[\'1\', \'0\', \'3\']', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => 'NA,NA', 'expected_output' => '[\'0\', \'0\']', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '1,,2', 'expected_output' => '[\'1\', \'0\', \'2\']', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '5,6,7', 'expected_output' => '[\'5\', \'6\', \'7\']', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 35,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 35: Count outliers by z-score

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read n, then n values. Count values with absolute z-score greater than 2. Use population standard deviation; if std is zero, output 0.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
n = int(input())
# Count outliers
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 350,
                'test_cases' => [
                        ['input' => '5\n10\n10\n10\n10\n100', 'expected_output' => '0', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '6\n1\n1\n1\n1\n1\n20', 'expected_output' => '1', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '3\n5\n5\n5', 'expected_output' => '0', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '8\n1\n2\n2\n2\n2\n2\n2\n20', 'expected_output' => '1', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 36,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 36: Naive next forecast

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read n, then n values. Forecast the next value as last value plus the average consecutive difference, rounded to 2 decimals.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
n = int(input())
# Print next forecast
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 325,
                'test_cases' => [
                        ['input' => '4\n10\n12\n14\n16', 'expected_output' => '18.0', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '3\n5\n7\n10', 'expected_output' => '12.5', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '2\n1\n1', 'expected_output' => '1.0', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '5\n100\n90\n80\n70\n60', 'expected_output' => '50.0', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 37,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 37: Risk band classification

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read a numeric score from 0 to 100. Print Low for 0-39, Medium for 40-69, and High for 70-100.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
score = float(input())
# Print risk band
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '25', 'expected_output' => 'Low', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '55', 'expected_output' => 'Medium', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '90', 'expected_output' => 'High', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '70', 'expected_output' => 'High', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 38,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 38: Rule-based recommendation

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read accuracy and latency. Print Deploy if accuracy >= 0.85 and latency <= 200, otherwise Review.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
accuracy = float(input())
latency = float(input())
# Print Deploy or Review
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '0.9\n150', 'expected_output' => 'Deploy', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '0.8\n150', 'expected_output' => 'Review', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '0.95\n250', 'expected_output' => 'Review', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '0.85\n200', 'expected_output' => 'Deploy', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 39,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 39: Calculate weighted score

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read three component scores and three weights. Print the weighted score rounded to 2 decimals.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
s1 = float(input())
s2 = float(input())
s3 = float(input())
w1 = float(input())
w2 = float(input())
w3 = float(input())
# Print weighted score
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '80\n90\n100\n0.2\n0.3\n0.5', 'expected_output' => '93.0', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '10\n20\n30\n0.3\n0.3\n0.4', 'expected_output' => '21.0', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '100\n50\n0\n0.5\n0.25\n0.25', 'expected_output' => '62.5', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '1\n2\n3\n1\n0\n0', 'expected_output' => '1.0', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 40,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 40: Check monotonic increase

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read n, then n numbers. Print Yes if the sequence is strictly increasing, otherwise No.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
n = int(input())
# Print Yes or No
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '4\n1\n2\n3\n4', 'expected_output' => 'Yes', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '4\n1\n2\n2\n3', 'expected_output' => 'No', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '1\n9', 'expected_output' => 'Yes', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '3\n5\n4\n6', 'expected_output' => 'No', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 41,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 41: Detect duplicate values

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read n, then n strings. Print Yes if any duplicate exists, otherwise No.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
n = int(input())
# Print Yes if duplicates exist
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '4\na\nb\nc\na', 'expected_output' => 'Yes', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '3\na\nb\nc', 'expected_output' => 'No', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '1\nx', 'expected_output' => 'No', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '5\n1\n2\n3\n4\n4', 'expected_output' => 'Yes', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 42,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 42: Echo the dataset label

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read one line and print it exactly. This checks input handling before larger data tasks.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
label = input()
# Print the label exactly
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => 'sales', 'expected_output' => 'sales', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => 'risk_score', 'expected_output' => 'risk_score', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => 'model_v2', 'expected_output' => 'model_v2', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => 'dataset_alpha', 'expected_output' => 'dataset_alpha', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 43,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 43: Add two measurements

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read two numbers and print their sum as an integer.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
a = int(input())
b = int(input())
# Print the sum
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '3\n4', 'expected_output' => '7', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '10\n25', 'expected_output' => '35', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '-5\n12', 'expected_output' => '7', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '100\n200', 'expected_output' => '300', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 44,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 44: Compute a difference

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read baseline and current values. Print current minus baseline.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
baseline = int(input())
current = int(input())
# Print current - baseline
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '10\n15', 'expected_output' => '5', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '100\n80', 'expected_output' => '-20', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '0\n9', 'expected_output' => '9', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '-4\n6', 'expected_output' => '10', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 45,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 45: Multiply feature values

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read two integers and print their product.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
x = int(input())
y = int(input())
# Print the product
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '3\n5', 'expected_output' => '15', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '7\n8', 'expected_output' => '56', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '-2\n9', 'expected_output' => '-18', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '0\n99', 'expected_output' => '0', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 46,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 46: Round a ratio

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read numerator and denominator. Print numerator / denominator rounded to 2 decimal places.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
num = float(input())
den = float(input())
# Print rounded ratio
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '1\n2', 'expected_output' => '0.5', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '10\n4', 'expected_output' => '2.5', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '7\n3', 'expected_output' => '2.33', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '22\n7', 'expected_output' => '3.14', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 47,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 47: Choose the larger score

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read two scores and print the larger one.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
a = int(input())
b = int(input())
# Print the larger score
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '8\n3', 'expected_output' => '8', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '2\n9', 'expected_output' => '9', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '5\n5', 'expected_output' => '5', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '-1\n-7', 'expected_output' => '-1', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 48,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 48: Choose the smaller score

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read two scores and print the smaller one.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
a = int(input())
b = int(input())
# Print the smaller score
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '8\n3', 'expected_output' => '3', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '2\n9', 'expected_output' => '2', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '5\n5', 'expected_output' => '5', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '-1\n-7', 'expected_output' => '-7', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 49,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 49: Sum a variable-length list

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read n, then read n integers. Print their total.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
n = int(input())
# Read n numbers and print their sum
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '3\n1\n2\n3', 'expected_output' => '6', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '5\n10\n0\n-2\n4\n8', 'expected_output' => '20', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '1\n99', 'expected_output' => '99', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '4\n-1\n-2\n-3\n6', 'expected_output' => '0', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
            [
                'order_index' => 50,
                'problem_description' => <<<'MD'
### Data Warehousing Coding Task 50: Average a list

This exercise is part of the **Advanced** coding track for **Data Warehousing**. The surrounding topic focuses on ETL, dimensions, facts, warehouse schemas, and analytical aggregates.

Read n, then read n numbers. Print the average rounded to 2 decimal places.

Follow the exact input and output format. Do not print extra labels unless the task asks for them.
MD,
                'starter_code' => <<<'PY'
n = int(input())
# Read n numbers and print the average
PY,
                'time_limit_seconds' => 900,
                'base_xp' => 250,
                'test_cases' => [
                        ['input' => '4\n1\n2\n3\n4', 'expected_output' => '2.5', 'is_hidden' => false, 'order_index' => 1],
                        ['input' => '3\n10\n20\n30', 'expected_output' => '20.0', 'is_hidden' => false, 'order_index' => 2],
                        ['input' => '2\n5\n6', 'expected_output' => '5.5', 'is_hidden' => true, 'order_index' => 3],
                        ['input' => '5\n1\n1\n1\n1\n1', 'expected_output' => '1.0', 'is_hidden' => true, 'order_index' => 4],
                    ],
            ],
        ];

        DB::transaction(function () use ($challenge, $questionDefs): void {
            foreach ($questionDefs as $def) {
                $testCases = $def['test_cases'];
                unset($def['test_cases']);

                $row = DB::table('coding_questions')->where([
                    'challenge_id' => $challenge->id,
                    'order_index' => $def['order_index'],
                ])->first();

                $payload = array_merge(
                    ['challenge_id' => $challenge->id, 'language' => 'python'],
                    $def,
                    ['updated_at' => now()]
                );

                if ($row) {
                    DB::table('coding_questions')->where('id', $row->id)->update($payload);
                    $questionId = $row->id;
                } else {
                    $questionId = DB::table('coding_questions')->insertGetId(array_merge($payload, ['created_at' => now()]));
                }

                DB::table('test_cases')->where('coding_question_id', $questionId)->delete();

                $rows = array_map(fn ($case) => array_merge(
                    ['coding_question_id' => $questionId, 'created_at' => now(), 'updated_at' => now()],
                    $case
                ), $testCases);

                DB::table('test_cases')->insert($rows);
            }
        });

        $this->command->info('✅ Module 23 Coding (Advanced) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}
