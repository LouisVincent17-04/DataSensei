<?php

namespace Database\Seeders;

use App\Models\ModuleLibraryItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleLibraryPythonBasicV1Seeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            ModuleLibraryItem::updateOrCreate(
                ['module_code' => 'PYTHON-BASIC-V1'],
                [
                    'module_no' => 1,
                    'title' => 'Basics of Python Programming',
                    'year_level' => 'Year 1',
                    'version_no' => 1,
                    'version_name' => 'Python Fundamentals',
                    'version_code' => 'V1',
                    'description' => 'Covers variables, data types, input/output, operators, conditionals, loops, functions, and basic code reading.',
                    'estimated_minutes' => 360,
                    'content_sections' => json_encode($this->contentSections(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                    'mcq_questions' => json_encode($this->mcqQuestions(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                    'sort_order' => 1,
                    'is_active' => true,
                ]
            );
        });
    }

    private function contentSections(): array
    {
        return [
            [
                'heading' => 'Intended Learning Outcomes',
                'subheading' => 'What students should be able to do after completing Python Fundamentals',
                'body' => 'After completing Python Fundamentals, students should be able to understand, write, trace, and debug beginner-level Python programs. This module focuses on foundational programming concepts such as variables, data types, input/output, operators, conditional statements, loops, functions, and basic code reading. These outcomes guide the lessons, activities, code examples, and knowledge checks in this module.',
                'code' => '',
                'walkthrough' => [
                    'Step 1: Read each learning outcome before starting the lesson so you understand the expected skills.',
                    'Step 2: Connect every lesson topic to one or more outcomes, such as using variables, tracing code, or debugging errors.',
                    'Step 3: After each section, check whether you can explain the concept, apply it in code, and predict the output.',
                    'Step 4: At the end of the module, use the outcomes as a checklist for review before taking assessments.',
                ],
                'netacad_style_activity' => 'Outcome Review Activity: Before starting the module, copy the intended learning outcomes into your notes. After each lesson, mark which outcomes you practiced and write one code example that proves your understanding.',
                'common_mistakes' => [
                    'Reading the module without checking what skills you are expected to demonstrate.',
                    'Memorizing definitions without practicing code tracing and problem solving.',
                    'Skipping debugging practice even though error reading is part of the expected outcome.',
                    'Thinking that completing the lesson means mastery without testing yourself through examples and questions.',
                ],
                'key_points' => [
                    'Identify Python variables and explain how values are assigned, stored, and updated.',
                    'Differentiate common Python data types such as int, float, str, bool, list, tuple, dictionary, and None.',
                    'Use print() and input() to create simple interactive Python programs.',
                    'Apply arithmetic, comparison, and logical operators in Python expressions.',
                    'Construct conditional statements using if, elif, and else to solve decision-making problems.',
                    'Use for loops and while loops to repeat tasks and process collections of values.',
                    'Define and call simple Python functions using parameters, arguments, return values, and clear function names.',
                    'Trace beginner-level Python code line by line and predict the correct output.',
                    'Debug common beginner errors such as SyntaxError, NameError, TypeError, ValueError, and IndentationError.',
                    'Develop a simple Python program that combines variables, input/output, operators, conditionals, loops, and functions.',
                ],
                'check_your_understanding' => [
                    'Can you explain what you should be able to do after completing this module?',
                    'Which learning outcome do you think is easiest for you right now?',
                    'Which learning outcome needs the most practice?',
                    'Can you write a simple Python program that demonstrates at least three of the listed outcomes?',
                ],
            ],
            [
                'heading' => 'Welcome to Python Fundamentals',
                'subheading' => 'Why python matters, where it is used, and how this module is organized',
                'body' => 'Welcome to Python Fundamentals focuses on why Python matters, where it is used, and how this module is organized. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study welcome to python fundamentals, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'name = "Alex"
age = 18
print(name)
print(age)',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about welcome to python fundamentals.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses welcome to python fundamentals. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Welcome to Python Fundamentals is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of welcome to python fundamentals?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'How Python Runs Code',
                'subheading' => 'Interpreters, scripts, statements, expressions, and execution order',
                'body' => 'How Python Runs Code focuses on interpreters, scripts, statements, expressions, and execution order. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study how python runs code, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'score = 87
if score >= 75:
    print("Passed")
else:
    print("Try again")',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about how python runs code.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses how python runs code. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'How Python Runs Code is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of how python runs code?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Writing Your First Program',
                'subheading' => 'Print(), strings, numbers, and reading output carefully',
                'body' => 'Writing Your First Program focuses on print(), strings, numbers, and reading output carefully. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study writing your first program, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'numbers = [4, 8, 12]
total = 0
for number in numbers:
    total = total + number
print(total)',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about writing your first program.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses writing your first program. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Writing Your First Program is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of writing your first program?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Comments and Readability',
                'subheading' => 'Single-line comments, notes for humans, and clean beginner code',
                'body' => 'Comments and Readability focuses on single-line comments, notes for humans, and clean beginner code. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study comments and readability, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'def calculate_average(values):
    return sum(values) / len(values)

scores = [88, 90, 79]
print(calculate_average(scores))',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about comments and readability.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses comments and readability. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Comments and Readability is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of comments and readability?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Variables as Named Storage',
                'subheading' => 'Assignment, reassignment, memory idea, and naming values',
                'body' => 'Variables as Named Storage focuses on assignment, reassignment, memory idea, and naming values. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study variables as named storage, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'raw_age = input("Enter age: ")
age = int(raw_age)
print("Next year:", age + 1)',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about variables as named storage.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses variables as named storage. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Variables as Named Storage is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of variables as named storage?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Variable Naming Rules',
                'subheading' => 'Valid identifiers, snake_case, reserved words, and case sensitivity',
                'body' => 'Variable Naming Rules focuses on valid identifiers, snake_case, reserved words, and case sensitivity. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study variable naming rules, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'message = "  Python Basics  "
cleaned = message.strip().upper()
print(cleaned)',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about variable naming rules.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses variable naming rules. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Variable Naming Rules is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of variable naming rules?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Core Data Types',
                'subheading' => 'Int, float, str, bool, and none as foundational value categories',
                'body' => 'Core Data Types focuses on int, float, str, bool, and None as foundational value categories. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study core data types, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'student = {"name": "Mia", "score": 94}
print(student["name"])
print(student["score"])',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about core data types.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses core data types. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Core Data Types is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of core data types?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Using type() for Inspection',
                'subheading' => 'Checking values, debugging assumptions, and reading type output',
                'body' => 'Using type() for Inspection focuses on checking values, debugging assumptions, and reading type output. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study using type() for inspection, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'count = 1
while count <= 3:
    print("Attempt", count)
    count = count + 1',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about using type() for inspection.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses using type() for inspection. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Using type() for Inspection is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of using type() for inspection?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'String Fundamentals',
                'subheading' => 'Quotation marks, concatenation, repetition, indexing, and length',
                'body' => 'String Fundamentals focuses on quotation marks, concatenation, repetition, indexing, and length. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study string fundamentals, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'for i in range(1, 6):
    if i == 3:
        continue
    print(i)',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about string fundamentals.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses string fundamentals. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'String Fundamentals is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of string fundamentals?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Useful String Methods',
                'subheading' => 'Upper(), lower(), strip(), replace(), split(), and common cleaning tasks',
                'body' => 'Useful String Methods focuses on upper(), lower(), strip(), replace(), split(), and common cleaning tasks. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study useful string methods, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'print("Hello, DataSensei!")
print("Python is readable.")',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about useful string methods.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses useful string methods. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Useful String Methods is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of useful string methods?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Numbers and Arithmetic',
                'subheading' => 'Addition, subtraction, multiplication, division, floor division, modulo, and powers',
                'body' => 'Numbers and Arithmetic focuses on addition, subtraction, multiplication, division, floor division, modulo, and powers. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study numbers and arithmetic, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'name = "Alex"
age = 18
print(name)
print(age)',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about numbers and arithmetic.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses numbers and arithmetic. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Numbers and Arithmetic is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of numbers and arithmetic?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Operator Precedence',
                'subheading' => 'Order of operations, parentheses, and tracing arithmetic expressions',
                'body' => 'Operator Precedence focuses on order of operations, parentheses, and tracing arithmetic expressions. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study operator precedence, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'score = 87
if score >= 75:
    print("Passed")
else:
    print("Try again")',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about operator precedence.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses operator precedence. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Operator Precedence is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of operator precedence?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Input with input()',
                'subheading' => 'Reading user input, prompts, and why input is always text',
                'body' => 'Input with input() focuses on reading user input, prompts, and why input is always text. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study input with input(), always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'numbers = [4, 8, 12]
total = 0
for number in numbers:
    total = total + number
print(total)',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about input with input().',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses input with input(). Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Input with input() is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of input with input()?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Type Conversion',
                'subheading' => 'Int(), float(), str(), conversion errors, and safe conversion habits',
                'body' => 'Type Conversion focuses on int(), float(), str(), conversion errors, and safe conversion habits. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study type conversion, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'def calculate_average(values):
    return sum(values) / len(values)

scores = [88, 90, 79]
print(calculate_average(scores))',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about type conversion.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses type conversion. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Type Conversion is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of type conversion?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Boolean Values',
                'subheading' => 'True, false, boolean expressions, and truth values',
                'body' => 'Boolean Values focuses on True, False, boolean expressions, and truth values. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study boolean values, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'raw_age = input("Enter age: ")
age = int(raw_age)
print("Next year:", age + 1)',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about boolean values.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses boolean values. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Boolean Values is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of boolean values?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Comparison Operators',
                'subheading' => '==, !=, >, <, >=, <= and the difference between assignment and comparison',
                'body' => 'Comparison Operators focuses on ==, !=, >, <, >=, <= and the difference between assignment and comparison. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study comparison operators, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'message = "  Python Basics  "
cleaned = message.strip().upper()
print(cleaned)',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about comparison operators.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses comparison operators. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Comparison Operators is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of comparison operators?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Logical Operators',
                'subheading' => 'And, or, not, combining conditions, and reading complex boolean logic',
                'body' => 'Logical Operators focuses on and, or, not, combining conditions, and reading complex boolean logic. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study logical operators, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'student = {"name": "Mia", "score": 94}
print(student["name"])
print(student["score"])',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about logical operators.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses logical operators. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Logical Operators is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of logical operators?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'If Statements',
                'subheading' => 'Decision-making, indentation, and basic branching',
                'body' => 'If Statements focuses on decision-making, indentation, and basic branching. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study if statements, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'count = 1
while count <= 3:
    print("Attempt", count)
    count = count + 1',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about if statements.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses if statements. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'If Statements is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of if statements?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'If-Elif-Else Chains',
                'subheading' => 'Multiple possible outcomes and first-true-branch behavior',
                'body' => 'If-Elif-Else Chains focuses on multiple possible outcomes and first-true-branch behavior. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study if-elif-else chains, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'for i in range(1, 6):
    if i == 3:
        continue
    print(i)',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about if-elif-else chains.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses if-elif-else chains. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'If-Elif-Else Chains is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of if-elif-else chains?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Nested Conditionals',
                'subheading' => 'Conditions inside conditions and when to avoid too much nesting',
                'body' => 'Nested Conditionals focuses on conditions inside conditions and when to avoid too much nesting. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study nested conditionals, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'print("Hello, DataSensei!")
print("Python is readable.")',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about nested conditionals.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses nested conditionals. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Nested Conditionals is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of nested conditionals?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Lists as Ordered Collections',
                'subheading' => 'Creating lists, indexes, len(), and common beginner list operations',
                'body' => 'Lists as Ordered Collections focuses on creating lists, indexes, len(), and common beginner list operations. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study lists as ordered collections, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'name = "Alex"
age = 18
print(name)
print(age)',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about lists as ordered collections.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses lists as ordered collections. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Lists as Ordered Collections is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of lists as ordered collections?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'List Mutation',
                'subheading' => 'Append(), insert(), remove(), pop(), and changing items by index',
                'body' => 'List Mutation focuses on append(), insert(), remove(), pop(), and changing items by index. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study list mutation, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'score = 87
if score >= 75:
    print("Passed")
else:
    print("Try again")',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about list mutation.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses list mutation. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'List Mutation is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of list mutation?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'List Slicing',
                'subheading' => 'Start, stop, step, and extracting parts of a sequence',
                'body' => 'List Slicing focuses on start, stop, step, and extracting parts of a sequence. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study list slicing, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'numbers = [4, 8, 12]
total = 0
for number in numbers:
    total = total + number
print(total)',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about list slicing.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses list slicing. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'List Slicing is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of list slicing?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Tuples and When Data Should Not Change',
                'subheading' => 'Tuple basics and comparing tuples with lists',
                'body' => 'Tuples and When Data Should Not Change focuses on tuple basics and comparing tuples with lists. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study tuples and when data should not change, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'def calculate_average(values):
    return sum(values) / len(values)

scores = [88, 90, 79]
print(calculate_average(scores))',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about tuples and when data should not change.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses tuples and when data should not change. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Tuples and When Data Should Not Change is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of tuples and when data should not change?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Dictionaries as Labeled Data',
                'subheading' => 'Key-value pairs, records, access, update, and common errors',
                'body' => 'Dictionaries as Labeled Data focuses on key-value pairs, records, access, update, and common errors. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study dictionaries as labeled data, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'raw_age = input("Enter age: ")
age = int(raw_age)
print("Next year:", age + 1)',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about dictionaries as labeled data.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses dictionaries as labeled data. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Dictionaries as Labeled Data is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of dictionaries as labeled data?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'For Loops',
                'subheading' => 'Iterating over lists, strings, dictionaries, and range()',
                'body' => 'For Loops focuses on iterating over lists, strings, dictionaries, and range(). In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study for loops, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'message = "  Python Basics  "
cleaned = message.strip().upper()
print(cleaned)',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about for loops.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses for loops. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'For Loops is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of for loops?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'range() Deep Dive',
                'subheading' => 'Start, stop, step, excluded stop value, and loop counting',
                'body' => 'range() Deep Dive focuses on start, stop, step, excluded stop value, and loop counting. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study range() deep dive, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'student = {"name": "Mia", "score": 94}
print(student["name"])
print(student["score"])',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about range() deep dive.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses range() deep dive. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'range() Deep Dive is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of range() deep dive?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'While Loops',
                'subheading' => 'Condition-controlled repetition and avoiding infinite loops',
                'body' => 'While Loops focuses on condition-controlled repetition and avoiding infinite loops. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study while loops, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'count = 1
while count <= 3:
    print("Attempt", count)
    count = count + 1',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about while loops.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses while loops. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'While Loops is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of while loops?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'break and continue',
                'subheading' => 'Controlling loop flow safely and clearly',
                'body' => 'break and continue focuses on controlling loop flow safely and clearly. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study break and continue, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'for i in range(1, 6):
    if i == 3:
        continue
    print(i)',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about break and continue.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses break and continue. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'break and continue is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of break and continue?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Accumulation Patterns',
                'subheading' => 'Running totals, counters, and collecting results',
                'body' => 'Accumulation Patterns focuses on running totals, counters, and collecting results. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study accumulation patterns, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'print("Hello, DataSensei!")
print("Python is readable.")',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about accumulation patterns.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses accumulation patterns. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Accumulation Patterns is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of accumulation patterns?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Functions',
                'subheading' => 'Def, parameters, arguments, return, and reusable logic',
                'body' => 'Functions focuses on def, parameters, arguments, return, and reusable logic. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study functions, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'name = "Alex"
age = 18
print(name)
print(age)',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about functions.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses functions. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Functions is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of functions?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Function Design',
                'subheading' => 'Single responsibility, naming functions, default values, and docstrings',
                'body' => 'Function Design focuses on single responsibility, naming functions, default values, and docstrings. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study function design, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'score = 87
if score >= 75:
    print("Passed")
else:
    print("Try again")',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about function design.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses function design. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Function Design is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of function design?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Scope',
                'subheading' => 'Local variables, global variables, and why scope prevents confusion',
                'body' => 'Scope focuses on local variables, global variables, and why scope prevents confusion. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study scope, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'numbers = [4, 8, 12]
total = 0
for number in numbers:
    total = total + number
print(total)',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about scope.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses scope. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Scope is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of scope?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Basic Error Types',
                'subheading' => 'Syntaxerror, nameerror, typeerror, valueerror, and indentationerror',
                'body' => 'Basic Error Types focuses on SyntaxError, NameError, TypeError, ValueError, and IndentationError. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study basic error types, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'def calculate_average(values):
    return sum(values) / len(values)

scores = [88, 90, 79]
print(calculate_average(scores))',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about basic error types.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses basic error types. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Basic Error Types is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of basic error types?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Debugging by Reading Tracebacks',
                'subheading' => 'Understanding error messages and line numbers',
                'body' => 'Debugging by Reading Tracebacks focuses on understanding error messages and line numbers. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study debugging by reading tracebacks, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'raw_age = input("Enter age: ")
age = int(raw_age)
print("Next year:", age + 1)',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about debugging by reading tracebacks.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses debugging by reading tracebacks. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Debugging by Reading Tracebacks is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of debugging by reading tracebacks?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Code Reading Strategy',
                'subheading' => 'Tracing variables, outputs, branches, and loop iterations',
                'body' => 'Code Reading Strategy focuses on tracing variables, outputs, branches, and loop iterations. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study code reading strategy, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'message = "  Python Basics  "
cleaned = message.strip().upper()
print(cleaned)',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about code reading strategy.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses code reading strategy. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Code Reading Strategy is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of code reading strategy?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Mini Project: Grade Classifier',
                'subheading' => 'Combining input, conversion, conditionals, and formatted output',
                'body' => 'Mini Project: Grade Classifier focuses on combining input, conversion, conditionals, and formatted output. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study mini project: grade classifier, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'student = {"name": "Mia", "score": 94}
print(student["name"])
print(student["score"])',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about mini project: grade classifier.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses mini project: grade classifier. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Mini Project: Grade Classifier is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of mini project: grade classifier?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Mini Project: Simple List Analyzer',
                'subheading' => 'Using lists, loops, totals, min, max, and average',
                'body' => 'Mini Project: Simple List Analyzer focuses on using lists, loops, totals, min, max, and average. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study mini project: simple list analyzer, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'count = 1
while count <= 3:
    print("Attempt", count)
    count = count + 1',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about mini project: simple list analyzer.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses mini project: simple list analyzer. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Mini Project: Simple List Analyzer is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of mini project: simple list analyzer?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Mini Project: Login Attempt Checker',
                'subheading' => 'While loops, counters, break, and user feedback',
                'body' => 'Mini Project: Login Attempt Checker focuses on while loops, counters, break, and user feedback. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study mini project: login attempt checker, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'for i in range(1, 6):
    if i == 3:
        continue
    print(i)',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about mini project: login attempt checker.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses mini project: login attempt checker. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Mini Project: Login Attempt Checker is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of mini project: login attempt checker?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
            [
                'heading' => 'Module Summary and Review Map',
                'subheading' => 'Connecting all concepts into a beginner python mental model',
                'body' => 'Module Summary and Review Map focuses on connecting all concepts into a beginner Python mental model. In a beginner Python course, this topic should not be treated as a short definition only. You should understand what problem the concept solves, how it appears in real code, and what mistakes usually happen when a new programmer uses it.

Python programs are read from top to bottom. Every line either creates a value, stores a value, makes a decision, repeats an action, or organizes logic for reuse. When you study module summary and review map, always ask three questions: What values exist before this line? What operation is being performed? What values exist after this line?

This topic is also important for code tracing. In quizzes and coding exercises, the correct answer usually depends on careful reading, not memorization. Pay attention to quotation marks, capitalization, parentheses, indentation, and whether a value is text or a number. These small details often change the final output.',
                'code' => 'print("Hello, DataSensei!")
print("Python is readable.")',
                'walkthrough' => [
                    'Step 1: Identify the values or variables introduced in this section about module summary and review map.',
                    'Step 2: Read each line in order and write down what changes after that line runs.',
                    'Step 3: Check whether the code displays output, stores data, makes a decision, or repeats a task.',
                    'Step 4: Compare your predicted output with the actual output and explain any mismatch.',
                ],
                'netacad_style_activity' => 'Practice Activity: Create a small example that uses module summary and review map. Then write a one-sentence explanation of each line. This forces you to read your own code like a programmer.',
                'common_mistakes' => [
                    'Reading code too quickly and ignoring small symbols such as quotation marks or colons.',
                    'Confusing the displayed output with the internal value stored in a variable.',
                    'Forgetting that Python is case-sensitive.',
                    'Forgetting that indentation defines code blocks.',
                ],
                'key_points' => [
                    'Module Summary and Review Map is part of the foundation needed before solving larger Python problems.',
                    'Python code should be read line by line from top to bottom unless a function or loop changes the flow.',
                    'Correct answers in beginner Python often depend on data type, order of execution, and indentation.',
                    'You should practice both writing code and predicting output.',
                ],
                'check_your_understanding' => [
                    'What is the main purpose of module summary and review map?',
                    'What is one syntax detail that beginners might forget?',
                    'Can you write a two-line code example that demonstrates the concept?',
                ],
            ],
        ];
    }

    private function mcqQuestions(): array
    {
        return [
            [
                'question' => 'Question 1: In Python Fundamentals, which answer best demonstrates print() when the goal is displaying information to the user?',
                'scenario' => 'A beginner is reviewing output. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of print() for displaying information to the user',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of print() for displaying information to the user',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for print(). When solving questions about output, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master output, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Output',
            ],
            [
                'question' => 'Question 2: In Python Fundamentals, which answer best demonstrates assignment when the goal is storing and updating values?',
                'scenario' => 'A beginner is reviewing variables. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of assignment for storing and updating values',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of assignment for storing and updating values',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for assignment. When solving questions about variables, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master variables, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Variables',
            ],
            [
                'question' => 'Question 3: In Python Fundamentals, which answer best demonstrates int, float, str, bool when the goal is recognizing value categories?',
                'scenario' => 'A beginner is reviewing data types. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of int, float, str, bool for recognizing value categories',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of int, float, str, bool for recognizing value categories',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for int, float, str, bool. When solving questions about data types, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master data types, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Data Types',
            ],
            [
                'question' => 'Question 4: In Python Fundamentals, which answer best demonstrates input() when the goal is reading user text?',
                'scenario' => 'A beginner is reviewing input. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of input() for reading user text',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of input() for reading user text',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for input(). When solving questions about input, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master input, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Input',
            ],
            [
                'question' => 'Question 5: In Python Fundamentals, which answer best demonstrates int(), float(), str() when the goal is changing data types safely?',
                'scenario' => 'A beginner is reviewing conversion. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of int(), float(), str() for changing data types safely',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of int(), float(), str() for changing data types safely',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for int(), float(), str(). When solving questions about conversion, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master conversion, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Conversion',
            ],
            [
                'question' => 'Question 6: In Python Fundamentals, which answer best demonstrates + - * / // % ** when the goal is performing calculations?',
                'scenario' => 'A beginner is reviewing operators. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of + - * / // % ** for performing calculations',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of + - * / // % ** for performing calculations',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for + - * / // % **. When solving questions about operators, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master operators, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Operators',
            ],
            [
                'question' => 'Question 7: In Python Fundamentals, which answer best demonstrates indexing and methods when the goal is working with text?',
                'scenario' => 'A beginner is reviewing strings. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of indexing and methods for working with text',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of indexing and methods for working with text',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for indexing and methods. When solving questions about strings, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master strings, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Strings',
            ],
            [
                'question' => 'Question 8: In Python Fundamentals, which answer best demonstrates ordered collections when the goal is storing multiple values?',
                'scenario' => 'A beginner is reviewing lists. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of ordered collections for storing multiple values',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of ordered collections for storing multiple values',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for ordered collections. When solving questions about lists, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master lists, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Lists',
            ],
            [
                'question' => 'Question 9: In Python Fundamentals, which answer best demonstrates if/elif/else when the goal is decision-making?',
                'scenario' => 'A beginner is reviewing conditions. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of if/elif/else for decision-making',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of if/elif/else for decision-making',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for if/elif/else. When solving questions about conditions, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master conditions, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Conditions',
            ],
            [
                'question' => 'Question 10: In Python Fundamentals, which answer best demonstrates for and while when the goal is repetition?',
                'scenario' => 'A beginner is reviewing loops. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of for and while for repetition',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of for and while for repetition',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for for and while. When solving questions about loops, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master loops, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Loops',
            ],
            [
                'question' => 'Question 11: In Python Fundamentals, which answer best demonstrates def and return when the goal is reusable code?',
                'scenario' => 'A beginner is reviewing functions. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of def and return for reusable code',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of def and return for reusable code',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for def and return. When solving questions about functions, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master functions, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Functions',
            ],
            [
                'question' => 'Question 12: In Python Fundamentals, which answer best demonstrates tracebacks and errors when the goal is finding and fixing mistakes?',
                'scenario' => 'A beginner is reviewing debugging. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of tracebacks and errors for finding and fixing mistakes',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of tracebacks and errors for finding and fixing mistakes',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for tracebacks and errors. When solving questions about debugging, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master debugging, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Debugging',
            ],
            [
                'question' => 'Question 13: In Python Fundamentals, which answer best demonstrates print() when the goal is displaying information to the user?',
                'scenario' => 'A beginner is reviewing output. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of print() for displaying information to the user',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of print() for displaying information to the user',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for print(). When solving questions about output, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master output, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Output',
            ],
            [
                'question' => 'Question 14: In Python Fundamentals, which answer best demonstrates assignment when the goal is storing and updating values?',
                'scenario' => 'A beginner is reviewing variables. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of assignment for storing and updating values',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of assignment for storing and updating values',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for assignment. When solving questions about variables, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master variables, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Variables',
            ],
            [
                'question' => 'Question 15: In Python Fundamentals, which answer best demonstrates int, float, str, bool when the goal is recognizing value categories?',
                'scenario' => 'A beginner is reviewing data types. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of int, float, str, bool for recognizing value categories',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of int, float, str, bool for recognizing value categories',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for int, float, str, bool. When solving questions about data types, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master data types, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Data Types',
            ],
            [
                'question' => 'Question 16: In Python Fundamentals, which answer best demonstrates input() when the goal is reading user text?',
                'scenario' => 'A beginner is reviewing input. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of input() for reading user text',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of input() for reading user text',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for input(). When solving questions about input, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master input, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Input',
            ],
            [
                'question' => 'Question 17: In Python Fundamentals, which answer best demonstrates int(), float(), str() when the goal is changing data types safely?',
                'scenario' => 'A beginner is reviewing conversion. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of int(), float(), str() for changing data types safely',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of int(), float(), str() for changing data types safely',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for int(), float(), str(). When solving questions about conversion, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master conversion, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Conversion',
            ],
            [
                'question' => 'Question 18: In Python Fundamentals, which answer best demonstrates + - * / // % ** when the goal is performing calculations?',
                'scenario' => 'A beginner is reviewing operators. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of + - * / // % ** for performing calculations',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of + - * / // % ** for performing calculations',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for + - * / // % **. When solving questions about operators, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master operators, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Operators',
            ],
            [
                'question' => 'Question 19: In Python Fundamentals, which answer best demonstrates indexing and methods when the goal is working with text?',
                'scenario' => 'A beginner is reviewing strings. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of indexing and methods for working with text',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of indexing and methods for working with text',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for indexing and methods. When solving questions about strings, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master strings, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Strings',
            ],
            [
                'question' => 'Question 20: In Python Fundamentals, which answer best demonstrates ordered collections when the goal is storing multiple values?',
                'scenario' => 'A beginner is reviewing lists. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of ordered collections for storing multiple values',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of ordered collections for storing multiple values',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for ordered collections. When solving questions about lists, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master lists, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Lists',
            ],
            [
                'question' => 'Question 21: In Python Fundamentals, which answer best demonstrates if/elif/else when the goal is decision-making?',
                'scenario' => 'A beginner is reviewing conditions. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of if/elif/else for decision-making',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of if/elif/else for decision-making',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for if/elif/else. When solving questions about conditions, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master conditions, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Conditions',
            ],
            [
                'question' => 'Question 22: In Python Fundamentals, which answer best demonstrates for and while when the goal is repetition?',
                'scenario' => 'A beginner is reviewing loops. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of for and while for repetition',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of for and while for repetition',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for for and while. When solving questions about loops, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master loops, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Loops',
            ],
            [
                'question' => 'Question 23: In Python Fundamentals, which answer best demonstrates def and return when the goal is reusable code?',
                'scenario' => 'A beginner is reviewing functions. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of def and return for reusable code',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of def and return for reusable code',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for def and return. When solving questions about functions, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master functions, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Functions',
            ],
            [
                'question' => 'Question 24: In Python Fundamentals, which answer best demonstrates tracebacks and errors when the goal is finding and fixing mistakes?',
                'scenario' => 'A beginner is reviewing debugging. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of tracebacks and errors for finding and fixing mistakes',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of tracebacks and errors for finding and fixing mistakes',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for tracebacks and errors. When solving questions about debugging, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master debugging, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Debugging',
            ],
            [
                'question' => 'Question 25: In Python Fundamentals, which answer best demonstrates print() when the goal is displaying information to the user?',
                'scenario' => 'A beginner is reviewing output. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of print() for displaying information to the user',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of print() for displaying information to the user',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for print(). When solving questions about output, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master output, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Output',
            ],
            [
                'question' => 'Question 26: In Python Fundamentals, which answer best demonstrates assignment when the goal is storing and updating values?',
                'scenario' => 'A beginner is reviewing variables. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of assignment for storing and updating values',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of assignment for storing and updating values',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for assignment. When solving questions about variables, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master variables, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Variables',
            ],
            [
                'question' => 'Question 27: In Python Fundamentals, which answer best demonstrates int, float, str, bool when the goal is recognizing value categories?',
                'scenario' => 'A beginner is reviewing data types. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of int, float, str, bool for recognizing value categories',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of int, float, str, bool for recognizing value categories',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for int, float, str, bool. When solving questions about data types, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master data types, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Data Types',
            ],
            [
                'question' => 'Question 28: In Python Fundamentals, which answer best demonstrates input() when the goal is reading user text?',
                'scenario' => 'A beginner is reviewing input. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of input() for reading user text',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of input() for reading user text',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for input(). When solving questions about input, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master input, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Input',
            ],
            [
                'question' => 'Question 29: In Python Fundamentals, which answer best demonstrates int(), float(), str() when the goal is changing data types safely?',
                'scenario' => 'A beginner is reviewing conversion. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of int(), float(), str() for changing data types safely',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of int(), float(), str() for changing data types safely',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for int(), float(), str(). When solving questions about conversion, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master conversion, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Conversion',
            ],
            [
                'question' => 'Question 30: In Python Fundamentals, which answer best demonstrates + - * / // % ** when the goal is performing calculations?',
                'scenario' => 'A beginner is reviewing operators. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of + - * / // % ** for performing calculations',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of + - * / // % ** for performing calculations',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for + - * / // % **. When solving questions about operators, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master operators, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Operators',
            ],
            [
                'question' => 'Question 31: In Python Fundamentals, which answer best demonstrates indexing and methods when the goal is working with text?',
                'scenario' => 'A beginner is reviewing strings. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of indexing and methods for working with text',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of indexing and methods for working with text',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for indexing and methods. When solving questions about strings, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master strings, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Strings',
            ],
            [
                'question' => 'Question 32: In Python Fundamentals, which answer best demonstrates ordered collections when the goal is storing multiple values?',
                'scenario' => 'A beginner is reviewing lists. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of ordered collections for storing multiple values',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of ordered collections for storing multiple values',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for ordered collections. When solving questions about lists, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master lists, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Lists',
            ],
            [
                'question' => 'Question 33: In Python Fundamentals, which answer best demonstrates if/elif/else when the goal is decision-making?',
                'scenario' => 'A beginner is reviewing conditions. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of if/elif/else for decision-making',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of if/elif/else for decision-making',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for if/elif/else. When solving questions about conditions, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master conditions, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Conditions',
            ],
            [
                'question' => 'Question 34: In Python Fundamentals, which answer best demonstrates for and while when the goal is repetition?',
                'scenario' => 'A beginner is reviewing loops. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of for and while for repetition',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of for and while for repetition',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for for and while. When solving questions about loops, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master loops, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Loops',
            ],
            [
                'question' => 'Question 35: In Python Fundamentals, which answer best demonstrates def and return when the goal is reusable code?',
                'scenario' => 'A beginner is reviewing functions. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of def and return for reusable code',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of def and return for reusable code',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for def and return. When solving questions about functions, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master functions, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Functions',
            ],
            [
                'question' => 'Question 36: In Python Fundamentals, which answer best demonstrates tracebacks and errors when the goal is finding and fixing mistakes?',
                'scenario' => 'A beginner is reviewing debugging. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of tracebacks and errors for finding and fixing mistakes',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of tracebacks and errors for finding and fixing mistakes',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for tracebacks and errors. When solving questions about debugging, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master debugging, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Debugging',
            ],
            [
                'question' => 'Question 37: In Python Fundamentals, which answer best demonstrates print() when the goal is displaying information to the user?',
                'scenario' => 'A beginner is reviewing output. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of print() for displaying information to the user',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of print() for displaying information to the user',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for print(). When solving questions about output, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master output, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Output',
            ],
            [
                'question' => 'Question 38: In Python Fundamentals, which answer best demonstrates assignment when the goal is storing and updating values?',
                'scenario' => 'A beginner is reviewing variables. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of assignment for storing and updating values',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of assignment for storing and updating values',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for assignment. When solving questions about variables, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master variables, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Variables',
            ],
            [
                'question' => 'Question 39: In Python Fundamentals, which answer best demonstrates int, float, str, bool when the goal is recognizing value categories?',
                'scenario' => 'A beginner is reviewing data types. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of int, float, str, bool for recognizing value categories',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of int, float, str, bool for recognizing value categories',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for int, float, str, bool. When solving questions about data types, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master data types, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Data Types',
            ],
            [
                'question' => 'Question 40: In Python Fundamentals, which answer best demonstrates input() when the goal is reading user text?',
                'scenario' => 'A beginner is reviewing input. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of input() for reading user text',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of input() for reading user text',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for input(). When solving questions about input, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master input, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Input',
            ],
            [
                'question' => 'Question 41: In Python Fundamentals, which answer best demonstrates int(), float(), str() when the goal is changing data types safely?',
                'scenario' => 'A beginner is reviewing conversion. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of int(), float(), str() for changing data types safely',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of int(), float(), str() for changing data types safely',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for int(), float(), str(). When solving questions about conversion, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master conversion, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Conversion',
            ],
            [
                'question' => 'Question 42: In Python Fundamentals, which answer best demonstrates + - * / // % ** when the goal is performing calculations?',
                'scenario' => 'A beginner is reviewing operators. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of + - * / // % ** for performing calculations',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of + - * / // % ** for performing calculations',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for + - * / // % **. When solving questions about operators, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master operators, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Operators',
            ],
            [
                'question' => 'Question 43: In Python Fundamentals, which answer best demonstrates indexing and methods when the goal is working with text?',
                'scenario' => 'A beginner is reviewing strings. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of indexing and methods for working with text',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of indexing and methods for working with text',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for indexing and methods. When solving questions about strings, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master strings, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Strings',
            ],
            [
                'question' => 'Question 44: In Python Fundamentals, which answer best demonstrates ordered collections when the goal is storing multiple values?',
                'scenario' => 'A beginner is reviewing lists. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of ordered collections for storing multiple values',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of ordered collections for storing multiple values',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for ordered collections. When solving questions about lists, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master lists, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Lists',
            ],
            [
                'question' => 'Question 45: In Python Fundamentals, which answer best demonstrates if/elif/else when the goal is decision-making?',
                'scenario' => 'A beginner is reviewing conditions. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of if/elif/else for decision-making',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of if/elif/else for decision-making',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for if/elif/else. When solving questions about conditions, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master conditions, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Conditions',
            ],
            [
                'question' => 'Question 46: In Python Fundamentals, which answer best demonstrates for and while when the goal is repetition?',
                'scenario' => 'A beginner is reviewing loops. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of for and while for repetition',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of for and while for repetition',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for for and while. When solving questions about loops, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master loops, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Loops',
            ],
            [
                'question' => 'Question 47: In Python Fundamentals, which answer best demonstrates def and return when the goal is reusable code?',
                'scenario' => 'A beginner is reviewing functions. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of def and return for reusable code',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of def and return for reusable code',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for def and return. When solving questions about functions, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master functions, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Functions',
            ],
            [
                'question' => 'Question 48: In Python Fundamentals, which answer best demonstrates tracebacks and errors when the goal is finding and fixing mistakes?',
                'scenario' => 'A beginner is reviewing debugging. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of tracebacks and errors for finding and fixing mistakes',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of tracebacks and errors for finding and fixing mistakes',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for tracebacks and errors. When solving questions about debugging, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master debugging, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Debugging',
            ],
            [
                'question' => 'Question 49: In Python Fundamentals, which answer best demonstrates print() when the goal is displaying information to the user?',
                'scenario' => 'A beginner is reviewing output. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of print() for displaying information to the user',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of print() for displaying information to the user',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for print(). When solving questions about output, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master output, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Output',
            ],
            [
                'question' => 'Question 50: In Python Fundamentals, which answer best demonstrates assignment when the goal is storing and updating values?',
                'scenario' => 'A beginner is reviewing variables. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of assignment for storing and updating values',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of assignment for storing and updating values',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for assignment. When solving questions about variables, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master variables, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Variables',
            ],
            [
                'question' => 'Question 51: In Python Fundamentals, which answer best demonstrates int, float, str, bool when the goal is recognizing value categories?',
                'scenario' => 'A beginner is reviewing data types. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of int, float, str, bool for recognizing value categories',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of int, float, str, bool for recognizing value categories',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for int, float, str, bool. When solving questions about data types, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master data types, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Data Types',
            ],
            [
                'question' => 'Question 52: In Python Fundamentals, which answer best demonstrates input() when the goal is reading user text?',
                'scenario' => 'A beginner is reviewing input. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of input() for reading user text',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of input() for reading user text',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for input(). When solving questions about input, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master input, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Input',
            ],
            [
                'question' => 'Question 53: In Python Fundamentals, which answer best demonstrates int(), float(), str() when the goal is changing data types safely?',
                'scenario' => 'A beginner is reviewing conversion. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of int(), float(), str() for changing data types safely',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of int(), float(), str() for changing data types safely',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for int(), float(), str(). When solving questions about conversion, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master conversion, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Conversion',
            ],
            [
                'question' => 'Question 54: In Python Fundamentals, which answer best demonstrates + - * / // % ** when the goal is performing calculations?',
                'scenario' => 'A beginner is reviewing operators. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of + - * / // % ** for performing calculations',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of + - * / // % ** for performing calculations',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for + - * / // % **. When solving questions about operators, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master operators, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Operators',
            ],
            [
                'question' => 'Question 55: In Python Fundamentals, which answer best demonstrates indexing and methods when the goal is working with text?',
                'scenario' => 'A beginner is reviewing strings. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of indexing and methods for working with text',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of indexing and methods for working with text',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for indexing and methods. When solving questions about strings, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master strings, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Strings',
            ],
            [
                'question' => 'Question 56: In Python Fundamentals, which answer best demonstrates ordered collections when the goal is storing multiple values?',
                'scenario' => 'A beginner is reviewing lists. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of ordered collections for storing multiple values',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of ordered collections for storing multiple values',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for ordered collections. When solving questions about lists, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master lists, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Lists',
            ],
            [
                'question' => 'Question 57: In Python Fundamentals, which answer best demonstrates if/elif/else when the goal is decision-making?',
                'scenario' => 'A beginner is reviewing conditions. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of if/elif/else for decision-making',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of if/elif/else for decision-making',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for if/elif/else. When solving questions about conditions, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master conditions, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Conditions',
            ],
            [
                'question' => 'Question 58: In Python Fundamentals, which answer best demonstrates for and while when the goal is repetition?',
                'scenario' => 'A beginner is reviewing loops. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of for and while for repetition',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of for and while for repetition',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for for and while. When solving questions about loops, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master loops, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Loops',
            ],
            [
                'question' => 'Question 59: In Python Fundamentals, which answer best demonstrates def and return when the goal is reusable code?',
                'scenario' => 'A beginner is reviewing functions. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of def and return for reusable code',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of def and return for reusable code',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for def and return. When solving questions about functions, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master functions, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Functions',
            ],
            [
                'question' => 'Question 60: In Python Fundamentals, which answer best demonstrates tracebacks and errors when the goal is finding and fixing mistakes?',
                'scenario' => 'A beginner is reviewing debugging. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of tracebacks and errors for finding and fixing mistakes',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of tracebacks and errors for finding and fixing mistakes',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for tracebacks and errors. When solving questions about debugging, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master debugging, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner',
                'topic' => 'Debugging',
            ],
            [
                'question' => 'Question 61: In Python Fundamentals, which answer best demonstrates print() when the goal is displaying information to the user?',
                'scenario' => 'A beginner is reviewing output. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of print() for displaying information to the user',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of print() for displaying information to the user',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for print(). When solving questions about output, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master output, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Output',
            ],
            [
                'question' => 'Question 62: In Python Fundamentals, which answer best demonstrates assignment when the goal is storing and updating values?',
                'scenario' => 'A beginner is reviewing variables. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of assignment for storing and updating values',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of assignment for storing and updating values',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for assignment. When solving questions about variables, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master variables, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Variables',
            ],
            [
                'question' => 'Question 63: In Python Fundamentals, which answer best demonstrates int, float, str, bool when the goal is recognizing value categories?',
                'scenario' => 'A beginner is reviewing data types. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of int, float, str, bool for recognizing value categories',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of int, float, str, bool for recognizing value categories',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for int, float, str, bool. When solving questions about data types, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master data types, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Data Types',
            ],
            [
                'question' => 'Question 64: In Python Fundamentals, which answer best demonstrates input() when the goal is reading user text?',
                'scenario' => 'A beginner is reviewing input. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of input() for reading user text',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of input() for reading user text',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for input(). When solving questions about input, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master input, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Input',
            ],
            [
                'question' => 'Question 65: In Python Fundamentals, which answer best demonstrates int(), float(), str() when the goal is changing data types safely?',
                'scenario' => 'A beginner is reviewing conversion. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of int(), float(), str() for changing data types safely',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of int(), float(), str() for changing data types safely',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for int(), float(), str(). When solving questions about conversion, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master conversion, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Conversion',
            ],
            [
                'question' => 'Question 66: In Python Fundamentals, which answer best demonstrates + - * / // % ** when the goal is performing calculations?',
                'scenario' => 'A beginner is reviewing operators. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of + - * / // % ** for performing calculations',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of + - * / // % ** for performing calculations',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for + - * / // % **. When solving questions about operators, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master operators, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Operators',
            ],
            [
                'question' => 'Question 67: In Python Fundamentals, which answer best demonstrates indexing and methods when the goal is working with text?',
                'scenario' => 'A beginner is reviewing strings. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of indexing and methods for working with text',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of indexing and methods for working with text',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for indexing and methods. When solving questions about strings, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master strings, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Strings',
            ],
            [
                'question' => 'Question 68: In Python Fundamentals, which answer best demonstrates ordered collections when the goal is storing multiple values?',
                'scenario' => 'A beginner is reviewing lists. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of ordered collections for storing multiple values',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of ordered collections for storing multiple values',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for ordered collections. When solving questions about lists, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master lists, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Lists',
            ],
            [
                'question' => 'Question 69: In Python Fundamentals, which answer best demonstrates if/elif/else when the goal is decision-making?',
                'scenario' => 'A beginner is reviewing conditions. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of if/elif/else for decision-making',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of if/elif/else for decision-making',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for if/elif/else. When solving questions about conditions, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master conditions, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Conditions',
            ],
            [
                'question' => 'Question 70: In Python Fundamentals, which answer best demonstrates for and while when the goal is repetition?',
                'scenario' => 'A beginner is reviewing loops. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of for and while for repetition',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of for and while for repetition',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for for and while. When solving questions about loops, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master loops, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Loops',
            ],
            [
                'question' => 'Question 71: In Python Fundamentals, which answer best demonstrates def and return when the goal is reusable code?',
                'scenario' => 'A beginner is reviewing functions. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of def and return for reusable code',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of def and return for reusable code',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for def and return. When solving questions about functions, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master functions, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Functions',
            ],
            [
                'question' => 'Question 72: In Python Fundamentals, which answer best demonstrates tracebacks and errors when the goal is finding and fixing mistakes?',
                'scenario' => 'A beginner is reviewing debugging. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of tracebacks and errors for finding and fixing mistakes',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of tracebacks and errors for finding and fixing mistakes',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for tracebacks and errors. When solving questions about debugging, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master debugging, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Debugging',
            ],
            [
                'question' => 'Question 73: In Python Fundamentals, which answer best demonstrates print() when the goal is displaying information to the user?',
                'scenario' => 'A beginner is reviewing output. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of print() for displaying information to the user',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of print() for displaying information to the user',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for print(). When solving questions about output, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master output, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Output',
            ],
            [
                'question' => 'Question 74: In Python Fundamentals, which answer best demonstrates assignment when the goal is storing and updating values?',
                'scenario' => 'A beginner is reviewing variables. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of assignment for storing and updating values',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of assignment for storing and updating values',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for assignment. When solving questions about variables, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master variables, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Variables',
            ],
            [
                'question' => 'Question 75: In Python Fundamentals, which answer best demonstrates int, float, str, bool when the goal is recognizing value categories?',
                'scenario' => 'A beginner is reviewing data types. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of int, float, str, bool for recognizing value categories',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of int, float, str, bool for recognizing value categories',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for int, float, str, bool. When solving questions about data types, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master data types, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Data Types',
            ],
            [
                'question' => 'Question 76: In Python Fundamentals, which answer best demonstrates input() when the goal is reading user text?',
                'scenario' => 'A beginner is reviewing input. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of input() for reading user text',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of input() for reading user text',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for input(). When solving questions about input, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master input, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Input',
            ],
            [
                'question' => 'Question 77: In Python Fundamentals, which answer best demonstrates int(), float(), str() when the goal is changing data types safely?',
                'scenario' => 'A beginner is reviewing conversion. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of int(), float(), str() for changing data types safely',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of int(), float(), str() for changing data types safely',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for int(), float(), str(). When solving questions about conversion, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master conversion, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Conversion',
            ],
            [
                'question' => 'Question 78: In Python Fundamentals, which answer best demonstrates + - * / // % ** when the goal is performing calculations?',
                'scenario' => 'A beginner is reviewing operators. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of + - * / // % ** for performing calculations',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of + - * / // % ** for performing calculations',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for + - * / // % **. When solving questions about operators, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master operators, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Operators',
            ],
            [
                'question' => 'Question 79: In Python Fundamentals, which answer best demonstrates indexing and methods when the goal is working with text?',
                'scenario' => 'A beginner is reviewing strings. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of indexing and methods for working with text',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of indexing and methods for working with text',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for indexing and methods. When solving questions about strings, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master strings, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Strings',
            ],
            [
                'question' => 'Question 80: In Python Fundamentals, which answer best demonstrates ordered collections when the goal is storing multiple values?',
                'scenario' => 'A beginner is reviewing lists. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of ordered collections for storing multiple values',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of ordered collections for storing multiple values',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for ordered collections. When solving questions about lists, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master lists, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Lists',
            ],
            [
                'question' => 'Question 81: In Python Fundamentals, which answer best demonstrates if/elif/else when the goal is decision-making?',
                'scenario' => 'A beginner is reviewing conditions. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of if/elif/else for decision-making',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of if/elif/else for decision-making',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for if/elif/else. When solving questions about conditions, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master conditions, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Conditions',
            ],
            [
                'question' => 'Question 82: In Python Fundamentals, which answer best demonstrates for and while when the goal is repetition?',
                'scenario' => 'A beginner is reviewing loops. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of for and while for repetition',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of for and while for repetition',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for for and while. When solving questions about loops, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master loops, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Loops',
            ],
            [
                'question' => 'Question 83: In Python Fundamentals, which answer best demonstrates def and return when the goal is reusable code?',
                'scenario' => 'A beginner is reviewing functions. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of def and return for reusable code',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of def and return for reusable code',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for def and return. When solving questions about functions, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master functions, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Functions',
            ],
            [
                'question' => 'Question 84: In Python Fundamentals, which answer best demonstrates tracebacks and errors when the goal is finding and fixing mistakes?',
                'scenario' => 'A beginner is reviewing debugging. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of tracebacks and errors for finding and fixing mistakes',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of tracebacks and errors for finding and fixing mistakes',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for tracebacks and errors. When solving questions about debugging, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master debugging, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Debugging',
            ],
            [
                'question' => 'Question 85: In Python Fundamentals, which answer best demonstrates print() when the goal is displaying information to the user?',
                'scenario' => 'A beginner is reviewing output. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of print() for displaying information to the user',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of print() for displaying information to the user',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for print(). When solving questions about output, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master output, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Output',
            ],
            [
                'question' => 'Question 86: In Python Fundamentals, which answer best demonstrates assignment when the goal is storing and updating values?',
                'scenario' => 'A beginner is reviewing variables. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of assignment for storing and updating values',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of assignment for storing and updating values',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for assignment. When solving questions about variables, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master variables, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Variables',
            ],
            [
                'question' => 'Question 87: In Python Fundamentals, which answer best demonstrates int, float, str, bool when the goal is recognizing value categories?',
                'scenario' => 'A beginner is reviewing data types. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of int, float, str, bool for recognizing value categories',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of int, float, str, bool for recognizing value categories',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for int, float, str, bool. When solving questions about data types, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master data types, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Data Types',
            ],
            [
                'question' => 'Question 88: In Python Fundamentals, which answer best demonstrates input() when the goal is reading user text?',
                'scenario' => 'A beginner is reviewing input. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of input() for reading user text',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of input() for reading user text',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for input(). When solving questions about input, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master input, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Input',
            ],
            [
                'question' => 'Question 89: In Python Fundamentals, which answer best demonstrates int(), float(), str() when the goal is changing data types safely?',
                'scenario' => 'A beginner is reviewing conversion. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of int(), float(), str() for changing data types safely',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of int(), float(), str() for changing data types safely',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for int(), float(), str(). When solving questions about conversion, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master conversion, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Conversion',
            ],
            [
                'question' => 'Question 90: In Python Fundamentals, which answer best demonstrates + - * / // % ** when the goal is performing calculations?',
                'scenario' => 'A beginner is reviewing operators. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of + - * / // % ** for performing calculations',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of + - * / // % ** for performing calculations',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for + - * / // % **. When solving questions about operators, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master operators, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Operators',
            ],
            [
                'question' => 'Question 91: In Python Fundamentals, which answer best demonstrates indexing and methods when the goal is working with text?',
                'scenario' => 'A beginner is reviewing strings. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of indexing and methods for working with text',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of indexing and methods for working with text',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for indexing and methods. When solving questions about strings, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master strings, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Strings',
            ],
            [
                'question' => 'Question 92: In Python Fundamentals, which answer best demonstrates ordered collections when the goal is storing multiple values?',
                'scenario' => 'A beginner is reviewing lists. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of ordered collections for storing multiple values',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of ordered collections for storing multiple values',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for ordered collections. When solving questions about lists, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master lists, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Lists',
            ],
            [
                'question' => 'Question 93: In Python Fundamentals, which answer best demonstrates if/elif/else when the goal is decision-making?',
                'scenario' => 'A beginner is reviewing conditions. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of if/elif/else for decision-making',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of if/elif/else for decision-making',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for if/elif/else. When solving questions about conditions, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master conditions, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Conditions',
            ],
            [
                'question' => 'Question 94: In Python Fundamentals, which answer best demonstrates for and while when the goal is repetition?',
                'scenario' => 'A beginner is reviewing loops. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of for and while for repetition',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of for and while for repetition',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for for and while. When solving questions about loops, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master loops, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Loops',
            ],
            [
                'question' => 'Question 95: In Python Fundamentals, which answer best demonstrates def and return when the goal is reusable code?',
                'scenario' => 'A beginner is reviewing functions. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of def and return for reusable code',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of def and return for reusable code',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for def and return. When solving questions about functions, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master functions, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Functions',
            ],
            [
                'question' => 'Question 96: In Python Fundamentals, which answer best demonstrates tracebacks and errors when the goal is finding and fixing mistakes?',
                'scenario' => 'A beginner is reviewing debugging. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of tracebacks and errors for finding and fixing mistakes',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of tracebacks and errors for finding and fixing mistakes',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for tracebacks and errors. When solving questions about debugging, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master debugging, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Debugging',
            ],
            [
                'question' => 'Question 97: In Python Fundamentals, which answer best demonstrates print() when the goal is displaying information to the user?',
                'scenario' => 'A beginner is reviewing output. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of print() for displaying information to the user',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of print() for displaying information to the user',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for print(). When solving questions about output, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master output, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Output',
            ],
            [
                'question' => 'Question 98: In Python Fundamentals, which answer best demonstrates assignment when the goal is storing and updating values?',
                'scenario' => 'A beginner is reviewing variables. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of assignment for storing and updating values',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of assignment for storing and updating values',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for assignment. When solving questions about variables, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master variables, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Variables',
            ],
            [
                'question' => 'Question 99: In Python Fundamentals, which answer best demonstrates int, float, str, bool when the goal is recognizing value categories?',
                'scenario' => 'A beginner is reviewing data types. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of int, float, str, bool for recognizing value categories',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of int, float, str, bool for recognizing value categories',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for int, float, str, bool. When solving questions about data types, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master data types, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Data Types',
            ],
            [
                'question' => 'Question 100: In Python Fundamentals, which answer best demonstrates input() when the goal is reading user text?',
                'scenario' => 'A beginner is reviewing input. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of input() for reading user text',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of input() for reading user text',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for input(). When solving questions about input, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master input, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Input',
            ],
            [
                'question' => 'Question 101: In Python Fundamentals, which answer best demonstrates int(), float(), str() when the goal is changing data types safely?',
                'scenario' => 'A beginner is reviewing conversion. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of int(), float(), str() for changing data types safely',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of int(), float(), str() for changing data types safely',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for int(), float(), str(). When solving questions about conversion, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master conversion, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Conversion',
            ],
            [
                'question' => 'Question 102: In Python Fundamentals, which answer best demonstrates + - * / // % ** when the goal is performing calculations?',
                'scenario' => 'A beginner is reviewing operators. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of + - * / // % ** for performing calculations',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of + - * / // % ** for performing calculations',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for + - * / // % **. When solving questions about operators, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master operators, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Operators',
            ],
            [
                'question' => 'Question 103: In Python Fundamentals, which answer best demonstrates indexing and methods when the goal is working with text?',
                'scenario' => 'A beginner is reviewing strings. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of indexing and methods for working with text',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of indexing and methods for working with text',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for indexing and methods. When solving questions about strings, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master strings, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Strings',
            ],
            [
                'question' => 'Question 104: In Python Fundamentals, which answer best demonstrates ordered collections when the goal is storing multiple values?',
                'scenario' => 'A beginner is reviewing lists. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of ordered collections for storing multiple values',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of ordered collections for storing multiple values',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for ordered collections. When solving questions about lists, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master lists, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Lists',
            ],
            [
                'question' => 'Question 105: In Python Fundamentals, which answer best demonstrates if/elif/else when the goal is decision-making?',
                'scenario' => 'A beginner is reviewing conditions. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of if/elif/else for decision-making',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of if/elif/else for decision-making',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for if/elif/else. When solving questions about conditions, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master conditions, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Conditions',
            ],
            [
                'question' => 'Question 106: In Python Fundamentals, which answer best demonstrates for and while when the goal is repetition?',
                'scenario' => 'A beginner is reviewing loops. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of for and while for repetition',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of for and while for repetition',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for for and while. When solving questions about loops, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master loops, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Loops',
            ],
            [
                'question' => 'Question 107: In Python Fundamentals, which answer best demonstrates def and return when the goal is reusable code?',
                'scenario' => 'A beginner is reviewing functions. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of def and return for reusable code',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of def and return for reusable code',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for def and return. When solving questions about functions, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master functions, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Functions',
            ],
            [
                'question' => 'Question 108: In Python Fundamentals, which answer best demonstrates tracebacks and errors when the goal is finding and fixing mistakes?',
                'scenario' => 'A beginner is reviewing debugging. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of tracebacks and errors for finding and fixing mistakes',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of tracebacks and errors for finding and fixing mistakes',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for tracebacks and errors. When solving questions about debugging, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master debugging, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Debugging',
            ],
            [
                'question' => 'Question 109: In Python Fundamentals, which answer best demonstrates print() when the goal is displaying information to the user?',
                'scenario' => 'A beginner is reviewing output. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of print() for displaying information to the user',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of print() for displaying information to the user',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for print(). When solving questions about output, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master output, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Output',
            ],
            [
                'question' => 'Question 110: In Python Fundamentals, which answer best demonstrates assignment when the goal is storing and updating values?',
                'scenario' => 'A beginner is reviewing variables. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of assignment for storing and updating values',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of assignment for storing and updating values',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for assignment. When solving questions about variables, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master variables, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Variables',
            ],
            [
                'question' => 'Question 111: In Python Fundamentals, which answer best demonstrates int, float, str, bool when the goal is recognizing value categories?',
                'scenario' => 'A beginner is reviewing data types. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of int, float, str, bool for recognizing value categories',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of int, float, str, bool for recognizing value categories',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for int, float, str, bool. When solving questions about data types, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master data types, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Data Types',
            ],
            [
                'question' => 'Question 112: In Python Fundamentals, which answer best demonstrates input() when the goal is reading user text?',
                'scenario' => 'A beginner is reviewing input. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of input() for reading user text',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of input() for reading user text',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for input(). When solving questions about input, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master input, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Input',
            ],
            [
                'question' => 'Question 113: In Python Fundamentals, which answer best demonstrates int(), float(), str() when the goal is changing data types safely?',
                'scenario' => 'A beginner is reviewing conversion. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of int(), float(), str() for changing data types safely',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of int(), float(), str() for changing data types safely',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for int(), float(), str(). When solving questions about conversion, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master conversion, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Conversion',
            ],
            [
                'question' => 'Question 114: In Python Fundamentals, which answer best demonstrates + - * / // % ** when the goal is performing calculations?',
                'scenario' => 'A beginner is reviewing operators. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of + - * / // % ** for performing calculations',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of + - * / // % ** for performing calculations',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for + - * / // % **. When solving questions about operators, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master operators, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Operators',
            ],
            [
                'question' => 'Question 115: In Python Fundamentals, which answer best demonstrates indexing and methods when the goal is working with text?',
                'scenario' => 'A beginner is reviewing strings. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of indexing and methods for working with text',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of indexing and methods for working with text',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for indexing and methods. When solving questions about strings, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master strings, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Strings',
            ],
            [
                'question' => 'Question 116: In Python Fundamentals, which answer best demonstrates ordered collections when the goal is storing multiple values?',
                'scenario' => 'A beginner is reviewing lists. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of ordered collections for storing multiple values',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of ordered collections for storing multiple values',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for ordered collections. When solving questions about lists, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master lists, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Lists',
            ],
            [
                'question' => 'Question 117: In Python Fundamentals, which answer best demonstrates if/elif/else when the goal is decision-making?',
                'scenario' => 'A beginner is reviewing conditions. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of if/elif/else for decision-making',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of if/elif/else for decision-making',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for if/elif/else. When solving questions about conditions, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master conditions, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Conditions',
            ],
            [
                'question' => 'Question 118: In Python Fundamentals, which answer best demonstrates for and while when the goal is repetition?',
                'scenario' => 'A beginner is reviewing loops. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of for and while for repetition',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of for and while for repetition',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for for and while. When solving questions about loops, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master loops, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Loops',
            ],
            [
                'question' => 'Question 119: In Python Fundamentals, which answer best demonstrates def and return when the goal is reusable code?',
                'scenario' => 'A beginner is reviewing functions. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of def and return for reusable code',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of def and return for reusable code',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for def and return. When solving questions about functions, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master functions, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Functions',
            ],
            [
                'question' => 'Question 120: In Python Fundamentals, which answer best demonstrates tracebacks and errors when the goal is finding and fixing mistakes?',
                'scenario' => 'A beginner is reviewing debugging. The student sees a short code fragment and must decide which option follows Python syntax and produces the intended behavior. Focus on whether the values are strings or numbers, whether the statement is properly indented, and whether the Python keyword or function is spelled correctly.',
                'choices' => [
                    'The correct Python use of tracebacks and errors for finding and fixing mistakes',
                    'A JavaScript-style answer that looks familiar but is not Python',
                    'A choice that ignores data types or indentation',
                    'A choice that would create a syntax or logic error',
                ],
                'answer' => 'The correct Python use of tracebacks and errors for finding and fixing mistakes',
                'explanation' => 'The first choice is correct because it uses Python\'s expected syntax for tracebacks and errors. When solving questions about debugging, do not rely on what looks familiar from other languages. Instead, check the exact Python rule: correct keywords, correct punctuation, correct data type, and correct indentation.',
                'why_other_choices_are_wrong' => [
                    'The second choice resembles another programming language or informal pseudocode rather than valid Python.',
                    'The third choice misses an important Python behavior, usually related to type, indentation, or execution order.',
                    'The fourth choice would either fail with an error or produce a result that does not match the problem requirement.',
                ],
                'learning_tip' => 'To master debugging, write a tiny example, predict the output before running it, then explain why Python produced that result.',
                'difficulty' => 'Beginner to Intermediate',
                'topic' => 'Debugging',
            ],
            [
                'question' => 'Extended Review Question 121: Which option best applies print() when solving a output task in Python?',
                'scenario' => 'This extended review item is designed to make the learner slow down and analyze a realistic beginner mistake. The task involves output, and the answer depends on recognizing how Python evaluates the statement step by step.',
                'choices' => [
                    'The Python-correct approach for print() in a beginner output problem',
                    'A misleading answer that treats Python like another language',
                    'A partially correct idea that fails because of type or indentation',
                    'A result that might look right but does not follow the code execution order',
                ],
                'answer' => 'The Python-correct approach for print() in a beginner output problem',
                'explanation' => 'The correct answer follows Python syntax and respects the behavior of print(). A strong learner should be able to explain not only what the answer is, but why each incorrect option fails.',
                'why_other_choices_are_wrong' => [
                    'It borrows syntax or assumptions from another programming language.',
                    'It ignores how Python handles data types or indentation.',
                    'It predicts the final result without tracing the program in order.',
                ],
                'learning_tip' => 'Build one tiny program about output, change one symbol, and observe how the output or error changes.',
                'difficulty' => 'Extended Beginner Review',
                'topic' => 'Output',
            ],
            [
                'question' => 'Extended Review Question 122: Which option best applies assignment when solving a variables task in Python?',
                'scenario' => 'This extended review item is designed to make the learner slow down and analyze a realistic beginner mistake. The task involves variables, and the answer depends on recognizing how Python evaluates the statement step by step.',
                'choices' => [
                    'The Python-correct approach for assignment in a beginner variables problem',
                    'A misleading answer that treats Python like another language',
                    'A partially correct idea that fails because of type or indentation',
                    'A result that might look right but does not follow the code execution order',
                ],
                'answer' => 'The Python-correct approach for assignment in a beginner variables problem',
                'explanation' => 'The correct answer follows Python syntax and respects the behavior of assignment. A strong learner should be able to explain not only what the answer is, but why each incorrect option fails.',
                'why_other_choices_are_wrong' => [
                    'It borrows syntax or assumptions from another programming language.',
                    'It ignores how Python handles data types or indentation.',
                    'It predicts the final result without tracing the program in order.',
                ],
                'learning_tip' => 'Build one tiny program about variables, change one symbol, and observe how the output or error changes.',
                'difficulty' => 'Extended Beginner Review',
                'topic' => 'Variables',
            ],
            [
                'question' => 'Extended Review Question 123: Which option best applies int, float, str, bool when solving a data types task in Python?',
                'scenario' => 'This extended review item is designed to make the learner slow down and analyze a realistic beginner mistake. The task involves data types, and the answer depends on recognizing how Python evaluates the statement step by step.',
                'choices' => [
                    'The Python-correct approach for int, float, str, bool in a beginner data types problem',
                    'A misleading answer that treats Python like another language',
                    'A partially correct idea that fails because of type or indentation',
                    'A result that might look right but does not follow the code execution order',
                ],
                'answer' => 'The Python-correct approach for int, float, str, bool in a beginner data types problem',
                'explanation' => 'The correct answer follows Python syntax and respects the behavior of int, float, str, bool. A strong learner should be able to explain not only what the answer is, but why each incorrect option fails.',
                'why_other_choices_are_wrong' => [
                    'It borrows syntax or assumptions from another programming language.',
                    'It ignores how Python handles data types or indentation.',
                    'It predicts the final result without tracing the program in order.',
                ],
                'learning_tip' => 'Build one tiny program about data types, change one symbol, and observe how the output or error changes.',
                'difficulty' => 'Extended Beginner Review',
                'topic' => 'Data Types',
            ],
            [
                'question' => 'Extended Review Question 124: Which option best applies input() when solving a input task in Python?',
                'scenario' => 'This extended review item is designed to make the learner slow down and analyze a realistic beginner mistake. The task involves input, and the answer depends on recognizing how Python evaluates the statement step by step.',
                'choices' => [
                    'The Python-correct approach for input() in a beginner input problem',
                    'A misleading answer that treats Python like another language',
                    'A partially correct idea that fails because of type or indentation',
                    'A result that might look right but does not follow the code execution order',
                ],
                'answer' => 'The Python-correct approach for input() in a beginner input problem',
                'explanation' => 'The correct answer follows Python syntax and respects the behavior of input(). A strong learner should be able to explain not only what the answer is, but why each incorrect option fails.',
                'why_other_choices_are_wrong' => [
                    'It borrows syntax or assumptions from another programming language.',
                    'It ignores how Python handles data types or indentation.',
                    'It predicts the final result without tracing the program in order.',
                ],
                'learning_tip' => 'Build one tiny program about input, change one symbol, and observe how the output or error changes.',
                'difficulty' => 'Extended Beginner Review',
                'topic' => 'Input',
            ],
            [
                'question' => 'Extended Review Question 125: Which option best applies int(), float(), str() when solving a conversion task in Python?',
                'scenario' => 'This extended review item is designed to make the learner slow down and analyze a realistic beginner mistake. The task involves conversion, and the answer depends on recognizing how Python evaluates the statement step by step.',
                'choices' => [
                    'The Python-correct approach for int(), float(), str() in a beginner conversion problem',
                    'A misleading answer that treats Python like another language',
                    'A partially correct idea that fails because of type or indentation',
                    'A result that might look right but does not follow the code execution order',
                ],
                'answer' => 'The Python-correct approach for int(), float(), str() in a beginner conversion problem',
                'explanation' => 'The correct answer follows Python syntax and respects the behavior of int(), float(), str(). A strong learner should be able to explain not only what the answer is, but why each incorrect option fails.',
                'why_other_choices_are_wrong' => [
                    'It borrows syntax or assumptions from another programming language.',
                    'It ignores how Python handles data types or indentation.',
                    'It predicts the final result without tracing the program in order.',
                ],
                'learning_tip' => 'Build one tiny program about conversion, change one symbol, and observe how the output or error changes.',
                'difficulty' => 'Extended Beginner Review',
                'topic' => 'Conversion',
            ],
            [
                'question' => 'Extended Review Question 126: Which option best applies + - * / // % ** when solving a operators task in Python?',
                'scenario' => 'This extended review item is designed to make the learner slow down and analyze a realistic beginner mistake. The task involves operators, and the answer depends on recognizing how Python evaluates the statement step by step.',
                'choices' => [
                    'The Python-correct approach for + - * / // % ** in a beginner operators problem',
                    'A misleading answer that treats Python like another language',
                    'A partially correct idea that fails because of type or indentation',
                    'A result that might look right but does not follow the code execution order',
                ],
                'answer' => 'The Python-correct approach for + - * / // % ** in a beginner operators problem',
                'explanation' => 'The correct answer follows Python syntax and respects the behavior of + - * / // % **. A strong learner should be able to explain not only what the answer is, but why each incorrect option fails.',
                'why_other_choices_are_wrong' => [
                    'It borrows syntax or assumptions from another programming language.',
                    'It ignores how Python handles data types or indentation.',
                    'It predicts the final result without tracing the program in order.',
                ],
                'learning_tip' => 'Build one tiny program about operators, change one symbol, and observe how the output or error changes.',
                'difficulty' => 'Extended Beginner Review',
                'topic' => 'Operators',
            ],
            [
                'question' => 'Extended Review Question 127: Which option best applies indexing and methods when solving a strings task in Python?',
                'scenario' => 'This extended review item is designed to make the learner slow down and analyze a realistic beginner mistake. The task involves strings, and the answer depends on recognizing how Python evaluates the statement step by step.',
                'choices' => [
                    'The Python-correct approach for indexing and methods in a beginner strings problem',
                    'A misleading answer that treats Python like another language',
                    'A partially correct idea that fails because of type or indentation',
                    'A result that might look right but does not follow the code execution order',
                ],
                'answer' => 'The Python-correct approach for indexing and methods in a beginner strings problem',
                'explanation' => 'The correct answer follows Python syntax and respects the behavior of indexing and methods. A strong learner should be able to explain not only what the answer is, but why each incorrect option fails.',
                'why_other_choices_are_wrong' => [
                    'It borrows syntax or assumptions from another programming language.',
                    'It ignores how Python handles data types or indentation.',
                    'It predicts the final result without tracing the program in order.',
                ],
                'learning_tip' => 'Build one tiny program about strings, change one symbol, and observe how the output or error changes.',
                'difficulty' => 'Extended Beginner Review',
                'topic' => 'Strings',
            ],
            [
                'question' => 'Extended Review Question 128: Which option best applies ordered collections when solving a lists task in Python?',
                'scenario' => 'This extended review item is designed to make the learner slow down and analyze a realistic beginner mistake. The task involves lists, and the answer depends on recognizing how Python evaluates the statement step by step.',
                'choices' => [
                    'The Python-correct approach for ordered collections in a beginner lists problem',
                    'A misleading answer that treats Python like another language',
                    'A partially correct idea that fails because of type or indentation',
                    'A result that might look right but does not follow the code execution order',
                ],
                'answer' => 'The Python-correct approach for ordered collections in a beginner lists problem',
                'explanation' => 'The correct answer follows Python syntax and respects the behavior of ordered collections. A strong learner should be able to explain not only what the answer is, but why each incorrect option fails.',
                'why_other_choices_are_wrong' => [
                    'It borrows syntax or assumptions from another programming language.',
                    'It ignores how Python handles data types or indentation.',
                    'It predicts the final result without tracing the program in order.',
                ],
                'learning_tip' => 'Build one tiny program about lists, change one symbol, and observe how the output or error changes.',
                'difficulty' => 'Extended Beginner Review',
                'topic' => 'Lists',
            ],
        ];
    }
}


