<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ModuleLibraryItem;

class ModuleLibraryPythonSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding detailed Python module library items...');

        $modules = json_decode(<<<'JSON'
[
  {
    "module_no": 1,
    "module_code": "MOD-001-PYTHON-BASIC-V1",
    "title": "Basics of Python Programming",
    "year_level": "Year 1",
    "version_no": 1,
    "version_name": "Version 1: Python Fundamentals",
    "version_code": "PYTHON-BASIC-V1",
    "description": "A complete beginner-friendly Python fundamentals module covering syntax, variables, data types, input/output, operators, strings, collections, conditionals, loops, functions, and debugging through reading-based examples.",
    "estimated_minutes": 120,
    "sort_order": 101,
    "content_sections": [
      {
        "heading": "Module Overview: What Python Is and Why It Matters",
        "body": "Python is a high-level programming language often used for automation, data science, web applications, artificial intelligence, scripting, and problem solving. A high-level language hides many low-level computer details so beginners can focus on logic. In this module, Python is taught as a reading-based skill: students study concepts, trace examples, and answer MCQs instead of running a compiler.",
        "key_points": [
          "Python code is usually written as readable statements.",
          "Python uses indentation to show code blocks.",
          "Python is dynamically typed, meaning variables do not need fixed type declarations.",
          "Python is popular in data science because it is readable and has many libraries."
        ],
        "learning_goal": "Explain what Python is and identify common situations where Python is useful."
      },
      {
        "heading": "Basic Syntax, Statements, and Indentation",
        "body": "A Python program is made of statements. A statement is an instruction, such as assigning a value or printing output. Unlike many languages that use braces to group blocks, Python uses indentation. This makes spacing meaningful. A missing or inconsistent indentation can change the meaning of a program or cause an error.",
        "code_snippet": "if True:\n    print('This line is inside the if block')\nprint('This line is outside the if block')",
        "key_points": [
          "A colon usually introduces an indented block.",
          "Indented lines belong to the block above them.",
          "Consistent indentation is required.",
          "Most Python style guides use four spaces per indentation level."
        ],
        "common_mistake": "Writing an indented line after an if statement, loop, or function inconsistently."
      },
      {
        "heading": "Output with print()",
        "body": "The print() function displays information. It can print text, numbers, variables, and multiple values. By default, print() separates multiple values with a space and ends with a newline. The sep parameter changes the separator, while end changes what appears after the output.",
        "code_snippet": "name = 'Ana'\nscore = 95\nprint(name, score)\nprint('A', 'B', 'C', sep='-')\nprint('Hello', end=' ')\nprint('World')",
        "key_points": [
          "print('Hello') displays Hello.",
          "print(a, b) separates values with a space by default.",
          "sep changes what goes between printed values.",
          "end changes what print places after the output."
        ],
        "trace_result": "The last two print statements display Hello World on one line because end=' ' prevents the first print from ending with a newline."
      },
      {
        "heading": "Comments and Readability",
        "body": "Comments are notes for humans. Python ignores comments during execution. A single-line comment starts with #. Triple-quoted strings are sometimes used as multi-line notes, but they are technically string literals. Good comments explain why code exists, not just what every line obviously does.",
        "code_snippet": "# Convert Celsius to Fahrenheit\ncelsius = 30\nfahrenheit = (celsius * 9 / 5) + 32\nprint(fahrenheit)",
        "key_points": [
          "Use # for single-line comments.",
          "Comments should improve understanding.",
          "Do not over-comment obvious statements.",
          "Use meaningful variable names along with comments."
        ]
      },
      {
        "heading": "Variables and Assignment",
        "body": "A variable is a name that refers to a value. Assignment uses the equals sign. In Python, the equals sign does not mean mathematical equality in the same way; it means store or bind the value on the right side to the name on the left side. Variables can be reassigned to new values.",
        "code_snippet": "age = 18\nage = age + 1\nprint(age)",
        "key_points": [
          "The right side of assignment is evaluated first.",
          "The left side receives the resulting value.",
          "Variables should use descriptive names.",
          "Python variable names cannot start with a number."
        ],
        "trace_result": "age starts as 18. The second line computes 18 + 1 and stores 19 back into age."
      },
      {
        "heading": "Core Data Types",
        "body": "Python values have types. Common beginner types include int for whole numbers, float for decimal numbers, str for text, bool for True/False, and NoneType for the absence of a value. Understanding types helps predict what operations are allowed.",
        "code_snippet": "count = 10\nprice = 19.99\nname = 'DataSensei'\nis_active = True\nmissing_value = None\nprint(type(count).__name__)",
        "key_points": [
          "int stores whole numbers.",
          "float stores decimal numbers.",
          "str stores text.",
          "bool stores True or False.",
          "None represents no value."
        ]
      },
      {
        "heading": "Type Conversion and Input",
        "body": "Input from input() is always a string. If the value should be treated as a number, it must be converted with int() or float(). Type conversion is also called casting. Incorrect conversion causes an error, such as trying to convert 'abc' to an integer.",
        "code_snippet": "age_text = '20'\nage = int(age_text)\nprint(age + 5)",
        "key_points": [
          "input() returns text.",
          "int('5') becomes the integer 5.",
          "float('5.5') becomes the decimal 5.5.",
          "str(10) becomes the text '10'."
        ],
        "common_mistake": "Trying to add a string number to an integer without conversion, such as '5' + 2."
      },
      {
        "heading": "Arithmetic and Comparison Operators",
        "body": "Operators perform actions on values. Arithmetic operators include +, -, *, /, //, %, and **. Comparison operators include ==, !=, >, <, >=, and <=. Comparisons produce boolean values.",
        "code_snippet": "a = 10\nb = 3\nprint(a + b)\nprint(a // b)\nprint(a % b)\nprint(a > b)",
        "key_points": [
          "/ gives division as a float.",
          "// gives floor division.",
          "% gives the remainder.",
          "** performs exponentiation.",
          "== checks equality while = assigns a value."
        ]
      },
      {
        "heading": "Strings and Text Processing",
        "body": "A string is a sequence of characters. Strings can be indexed, sliced, joined, split, formatted, and transformed. String methods return new strings because strings are immutable. That means a string cannot be changed in place.",
        "code_snippet": "text = 'python programming'\nprint(text.upper())\nprint(text[0])\nprint(text[0:6])\nprint(f'Topic: {text.title()}')",
        "key_points": [
          "Indexing starts at 0.",
          "Slicing uses start:stop.",
          "upper(), lower(), and title() transform text.",
          "f-strings make formatted output easier."
        ]
      },
      {
        "heading": "Lists",
        "body": "A list stores multiple values in order. Lists are mutable, so items can be changed, added, or removed. Lists are useful for collections of scores, names, records, and other grouped values.",
        "code_snippet": "scores = [85, 90, 78]\nscores.append(95)\nscores[0] = 88\nprint(scores)",
        "key_points": [
          "Lists use square brackets.",
          "append() adds an item to the end.",
          "len(list_name) returns the number of items.",
          "Lists can contain mixed types, but consistent types are usually cleaner."
        ]
      },
      {
        "heading": "Tuples, Sets, and Dictionaries",
        "body": "Python has several collection types. Tuples are ordered and usually treated as fixed. Sets store unique values with no guaranteed order. Dictionaries store key-value pairs and are very important in data processing because they can represent records.",
        "code_snippet": "student = {'name': 'Ana', 'score': 92}\nprint(student['name'])\nunique_scores = {90, 90, 85}\nprint(unique_scores)",
        "key_points": [
          "Tuple example: (1, 2, 3).",
          "Set example: {1, 2, 3}.",
          "Dictionary example: {'name': 'Ana'}.",
          "Dictionary keys should be unique."
        ]
      },
      {
        "heading": "Conditional Statements",
        "body": "Conditional statements allow programs to make decisions. Python uses if, elif, and else. Conditions are expressions that evaluate to True or False. Indentation determines which statements belong to each branch.",
        "code_snippet": "grade = 87\nif grade >= 90:\n    print('Excellent')\nelif grade >= 75:\n    print('Passed')\nelse:\n    print('Needs improvement')",
        "key_points": [
          "if checks the first condition.",
          "elif checks another condition if the previous one was false.",
          "else runs when no previous condition was true.",
          "Only one branch of an if/elif/else chain runs."
        ]
      },
      {
        "heading": "Loops",
        "body": "Loops repeat actions. A for loop is commonly used when iterating over a sequence, such as a list or range. A while loop repeats while a condition remains true. break stops a loop early, and continue skips the current iteration.",
        "code_snippet": "for number in range(1, 4):\n    print(number)\n\ncount = 0\nwhile count < 3:\n    count += 1\n    print(count)",
        "key_points": [
          "range(3) produces 0, 1, 2.",
          "for loops are ideal for known sequences.",
          "while loops are ideal for condition-based repetition.",
          "Avoid infinite loops by updating the loop condition."
        ]
      },
      {
        "heading": "Functions",
        "body": "A function is a reusable block of code. Functions help organize programs and avoid repeated logic. A function can accept parameters and return a value. If no return statement is used, the function returns None.",
        "code_snippet": "def add(a, b):\n    return a + b\n\nresult = add(3, 4)\nprint(result)",
        "key_points": [
          "def starts a function definition.",
          "Parameters receive input values.",
          "return sends a value back to the caller.",
          "Functions should usually do one clear task."
        ]
      },
      {
        "heading": "Errors and Debugging by Reading",
        "body": "Common Python errors include SyntaxError, TypeError, ValueError, NameError, and IndexError. Reading error messages is a skill. The error type often tells what went wrong, while the line number suggests where the problem occurred.",
        "code_snippet": "numbers = [10, 20]\n# print(numbers[5])  # IndexError because index 5 does not exist",
        "key_points": [
          "SyntaxError means Python cannot parse the code.",
          "NameError means a variable name is not defined.",
          "TypeError often means an operation used an incompatible type.",
          "IndexError often means a list index is outside the valid range."
        ]
      },
      {
        "heading": "Module Summary",
        "body": "By the end of this module, students should understand Python syntax, variables, types, strings, collections, conditionals, loops, functions, and basic debugging. The goal is not only to memorize terms but to trace code and explain why an output happens.",
        "key_points": [
          "Read code from top to bottom.",
          "Track variable changes carefully.",
          "Pay attention to indentation.",
          "Know the difference between assignment and comparison.",
          "Practice output tracing to strengthen reasoning."
        ]
      }
    ],
    "mcq_questions": [
      {
        "question": "Which statement best describes Python?",
        "choices": [
          "A low-level assembly language",
          "A high-level, general-purpose programming language",
          "A database management system",
          "A spreadsheet formula language"
        ],
        "answer": "A high-level, general-purpose programming language",
        "explanation": "Python is high-level and general-purpose, commonly used in data science, automation, web development, and AI."
      },
      {
        "question": "What does indentation control in Python?",
        "choices": [
          "The color of the code editor",
          "Which lines belong to a block",
          "The speed of the program",
          "The file name of the program"
        ],
        "answer": "Which lines belong to a block",
        "explanation": "Python uses indentation to define blocks under if statements, loops, functions, and similar structures."
      },
      {
        "question": "What is the output?\n\nx = 5\nx = x + 2\nprint(x)",
        "choices": [
          "5",
          "7",
          "x + 2",
          "Error"
        ],
        "answer": "7",
        "explanation": "x starts at 5. The second line stores 5 + 2, so x becomes 7."
      },
      {
        "question": "Which symbol starts a single-line comment in Python?",
        "choices": [
          "//",
          "#",
          "/*",
          "<!--"
        ],
        "answer": "#",
        "explanation": "Python single-line comments begin with #."
      },
      {
        "question": "What is the output?\n\nprint('A', 'B', 'C')",
        "choices": [
          "ABC",
          "A B C",
          "A,B,C",
          "['A', 'B', 'C']"
        ],
        "answer": "A B C",
        "explanation": "print() separates multiple values with a space by default."
      },
      {
        "question": "What does input() return by default?",
        "choices": [
          "int",
          "float",
          "str",
          "bool"
        ],
        "answer": "str",
        "explanation": "input() always returns text, even when the user types numbers."
      },
      {
        "question": "Which line correctly converts the string '25' into an integer?",
        "choices": [
          "int('25')",
          "integer('25')",
          "str(25)",
          "float_int('25')"
        ],
        "answer": "int('25')",
        "explanation": "int() converts valid numeric text into an integer."
      },
      {
        "question": "What is the type of 3.14 in Python?",
        "choices": [
          "int",
          "float",
          "str",
          "bool"
        ],
        "answer": "float",
        "explanation": "Decimal numbers are represented as float values."
      },
      {
        "question": "What is the difference between = and ==?",
        "choices": [
          "They are always the same",
          "= assigns, == compares",
          "= compares, == assigns",
          "Both create strings"
        ],
        "answer": "= assigns, == compares",
        "explanation": "A single equals sign assigns a value; double equals checks equality."
      },
      {
        "question": "What is the output?\n\nprint(10 // 3)",
        "choices": [
          "3.3333",
          "3",
          "1",
          "30"
        ],
        "answer": "3",
        "explanation": "// performs floor division, so 10 // 3 gives 3."
      },
      {
        "question": "What is the output?\n\nprint(10 % 3)",
        "choices": [
          "3",
          "1",
          "0",
          "10"
        ],
        "answer": "1",
        "explanation": "% gives the remainder. 10 divided by 3 leaves a remainder of 1."
      },
      {
        "question": "Which expression checks whether age is at least 18?",
        "choices": [
          "age => 18",
          "age >= 18",
          "age = 18+",
          "age >== 18"
        ],
        "answer": "age >= 18",
        "explanation": ">= means greater than or equal to."
      },
      {
        "question": "What is the output?\n\nname = 'Python'\nprint(name[0])",
        "choices": [
          "P",
          "y",
          "n",
          "Python"
        ],
        "answer": "P",
        "explanation": "String indexing starts at 0, so name[0] is 'P'."
      },
      {
        "question": "What is the output?\n\ntext = 'Data'\nprint(text.lower())",
        "choices": [
          "DATA",
          "data",
          "Data",
          "dATA"
        ],
        "answer": "data",
        "explanation": "lower() returns a lowercase version of the string."
      },
      {
        "question": "What does an f-string do?",
        "choices": [
          "Formats strings with embedded expressions",
          "Deletes a string",
          "Finds files only",
          "Freezes a variable"
        ],
        "answer": "Formats strings with embedded expressions",
        "explanation": "An f-string lets you place variables or expressions inside braces within a string."
      },
      {
        "question": "What is the output?\n\nscores = [80, 90]\nscores.append(100)\nprint(len(scores))",
        "choices": [
          "2",
          "3",
          "100",
          "Error"
        ],
        "answer": "3",
        "explanation": "append() adds a new item, so the list has 3 items."
      },
      {
        "question": "Which collection uses key-value pairs?",
        "choices": [
          "list",
          "tuple",
          "dictionary",
          "set only"
        ],
        "answer": "dictionary",
        "explanation": "Dictionaries store values using keys, such as {'name': 'Ana'}."
      },
      {
        "question": "What is the output?\n\ncolors = ['red', 'blue', 'green']\nprint(colors[1])",
        "choices": [
          "red",
          "blue",
          "green",
          "1"
        ],
        "answer": "blue",
        "explanation": "Index 1 refers to the second item because indexing starts at 0."
      },
      {
        "question": "What is a set mainly useful for?",
        "choices": [
          "Storing duplicate values in order",
          "Storing unique values",
          "Running a function",
          "Creating comments"
        ],
        "answer": "Storing unique values",
        "explanation": "Sets automatically keep unique elements."
      },
      {
        "question": "Which branch runs?\n\nscore = 82\nif score >= 90:\n    print('A')\nelif score >= 75:\n    print('Pass')\nelse:\n    print('Fail')",
        "choices": [
          "A",
          "Pass",
          "Fail",
          "No output"
        ],
        "answer": "Pass",
        "explanation": "82 is not at least 90, but it is at least 75."
      },
      {
        "question": "What is the output?\n\nfor i in range(3):\n    print(i)",
        "choices": [
          "1 2 3",
          "0 1 2",
          "0 1 2 3",
          "3 only"
        ],
        "answer": "0 1 2",
        "explanation": "range(3) produces 0, 1, and 2."
      },
      {
        "question": "Which loop is usually best for repeating while a condition remains true?",
        "choices": [
          "while loop",
          "comment loop",
          "dictionary loop",
          "import loop"
        ],
        "answer": "while loop",
        "explanation": "A while loop repeats as long as its condition is true."
      },
      {
        "question": "What does break do inside a loop?",
        "choices": [
          "Skips the current iteration only",
          "Stops the loop early",
          "Creates a function",
          "Prints an error"
        ],
        "answer": "Stops the loop early",
        "explanation": "break exits the loop immediately."
      },
      {
        "question": "What does continue do inside a loop?",
        "choices": [
          "Stops the whole program",
          "Skips to the next iteration",
          "Deletes the loop",
          "Converts text to a number"
        ],
        "answer": "Skips to the next iteration",
        "explanation": "continue ignores the remaining statements in the current iteration and moves to the next one."
      },
      {
        "question": "What keyword starts a function definition?",
        "choices": [
          "function",
          "def",
          "func",
          "method"
        ],
        "answer": "def",
        "explanation": "Python uses def to define a function."
      },
      {
        "question": "What is returned by a function with no return statement?",
        "choices": [
          "0",
          "False",
          "None",
          "The function name"
        ],
        "answer": "None",
        "explanation": "If no return statement is executed, a Python function returns None."
      },
      {
        "question": "What is the output?\n\ndef add(a, b):\n    return a + b\nprint(add(2, 3))",
        "choices": [
          "2",
          "3",
          "5",
          "add"
        ],
        "answer": "5",
        "explanation": "The function returns 2 + 3, which is 5."
      },
      {
        "question": "Which error usually means a variable name was used before being defined?",
        "choices": [
          "NameError",
          "SyntaxError",
          "IndexError",
          "KeyError"
        ],
        "answer": "NameError",
        "explanation": "NameError occurs when Python cannot find a variable or function name."
      },
      {
        "question": "Which error is likely?\n\nnumbers = [1, 2]\nprint(numbers[5])",
        "choices": [
          "NameError",
          "IndexError",
          "TypeError",
          "No error"
        ],
        "answer": "IndexError",
        "explanation": "The list has indexes 0 and 1 only, so index 5 is out of range."
      },
      {
        "question": "Which statement is most important when tracing code?",
        "choices": [
          "Ignore variable updates",
          "Track values line by line",
          "Read only the last line",
          "Assume all loops run once"
        ],
        "answer": "Track values line by line",
        "explanation": "Output tracing depends on following value changes in order."
      }
    ]
  },
  {
    "module_no": 1,
    "module_code": "MOD-001-PYTHON-BASIC-V2",
    "title": "Basics of Python Programming",
    "year_level": "Year 1",
    "version_no": 2,
    "version_name": "Version 2: Python Practice and MCQ Review",
    "version_code": "PYTHON-BASIC-V2",
    "description": "A practice-heavy Python module focused on output tracing, common beginner traps, MCQ reasoning, string/number behavior, list mutation, loop boundaries, functions, dictionaries, and error recognition.",
    "estimated_minutes": 120,
    "sort_order": 102,
    "content_sections": [
      {
        "heading": "Version Focus: Practice and MCQ Review",
        "body": "This version strengthens Python knowledge through code tracing, common traps, and multiple-choice reasoning. Students are expected to read snippets carefully and predict outputs without running code.",
        "key_points": [
          "Trace variables line by line.",
          "Check whether values are strings or numbers.",
          "Notice mutation in lists and dictionaries.",
          "Pay close attention to loop boundaries and indentation."
        ]
      },
      {
        "heading": "Output Tracing Strategy",
        "body": "Output tracing means predicting what code prints. A strong strategy is to create a small mental table of variable values, update the table after each assignment, and only then decide the output.",
        "code_snippet": "x = 2\ny = x + 3\nx = y * 2\nprint(x, y)",
        "key_points": [
          "Assignments happen in order.",
          "The right side is evaluated before assignment.",
          "Old values may be replaced.",
          "print shows the current value at that exact line."
        ]
      },
      {
        "heading": "String and Number Traps",
        "body": "A common beginner mistake is confusing the text '5' with the number 5. The plus operator behaves differently depending on the types involved. For numbers, it adds. For strings, it concatenates.",
        "code_snippet": "print(5 + 5)\nprint('5' + '5')\n# print('5' + 5)  # TypeError",
        "key_points": [
          "5 + 5 is numeric addition.",
          "'5' + '5' is string concatenation.",
          "Mixing string and integer with + causes TypeError.",
          "Use int() or str() when conversion is needed."
        ]
      },
      {
        "heading": "Boolean Reasoning",
        "body": "Boolean expressions are central to conditions. Python uses and, or, and not. The and operator requires both sides to be true. The or operator requires at least one side to be true. not reverses a boolean result.",
        "code_snippet": "age = 20\nhas_id = True\nprint(age >= 18 and has_id)",
        "key_points": [
          "and is true only if both sides are true.",
          "or is true if at least one side is true.",
          "not changes True to False and False to True.",
          "Comparisons return booleans."
        ]
      },
      {
        "heading": "List Mutation and Aliasing",
        "body": "Lists are mutable. If two variables point to the same list, changing the list through one variable also appears through the other variable. This is called aliasing. A slice copy can avoid this.",
        "code_snippet": "a = [1, 2]\nb = a\nb.append(3)\nprint(a)\n\nc = a[:]\nc.append(4)\nprint(a, c)",
        "key_points": [
          "b = a does not copy the list.",
          "a[:] creates a shallow copy.",
          "append() mutates the list.",
          "Aliasing is common in output tracing questions."
        ]
      },
      {
        "heading": "Loop Boundary Practice",
        "body": "Many mistakes come from misunderstanding range(). range(stop) starts at 0 and stops before stop. range(start, stop) starts at start and stops before stop. range(start, stop, step) changes by step.",
        "code_snippet": "print(list(range(4)))\nprint(list(range(1, 5)))\nprint(list(range(2, 10, 2)))",
        "key_points": [
          "range(4) gives 0, 1, 2, 3.",
          "The stop value is excluded.",
          "A step controls the jump between values.",
          "Negative steps count backward."
        ]
      },
      {
        "heading": "Function Call Tracing",
        "body": "When tracing functions, first identify the argument values, then substitute them into the function body. A return statement sends a value back to the call site. print() displays a value but does not return that value.",
        "code_snippet": "def double(n):\n    return n * 2\n\nx = double(4)\nprint(x)",
        "key_points": [
          "Arguments fill parameters.",
          "return provides a result.",
          "print() and return are different.",
          "A function can be called multiple times with different arguments."
        ]
      },
      {
        "heading": "Dictionary Practice",
        "body": "Dictionaries are often used for records. They are tested using key access, get(), updates, and loops over items. Accessing a missing key with brackets causes KeyError, but get() can return a default value.",
        "code_snippet": "student = {'name': 'Ana', 'score': 90}\nprint(student.get('section', 'No section'))",
        "key_points": [
          "dict[key] requires the key to exist.",
          "get(key, default) is safer for optional keys.",
          "Dictionaries use keys, not numeric positions.",
          "items() returns key-value pairs for looping."
        ]
      },
      {
        "heading": "Common Error Patterns",
        "body": "Practice questions often ask what error occurs. TypeError means an operation used an incompatible type. ValueError means a type is correct but the value is invalid. IndexError means a sequence index is invalid. KeyError means a dictionary key is missing.",
        "code_snippet": "int('abc')      # ValueError\n[1, 2][5]       # IndexError\n{'a': 1}['b']   # KeyError",
        "key_points": [
          "Identify the operation causing the problem.",
          "Do not guess based only on the last line.",
          "Check types and indexes carefully.",
          "Some errors occur before print() can display anything."
        ]
      },
      {
        "heading": "Review Routine",
        "body": "For every snippet, answer four questions: What values exist? Which line changes a value? Which branch or loop runs? What exactly is printed? This routine makes MCQ output tracing more reliable.",
        "key_points": [
          "Track state.",
          "Check conditions.",
          "Count loop iterations.",
          "Remember exact formatting."
        ]
      }
    ],
    "mcq_questions": [
      {
        "question": "What is the output?\n\nx = 1\nx = x + 4\nx = x * 2\nprint(x)",
        "choices": [
          "5",
          "10",
          "8",
          "2"
        ],
        "answer": "10",
        "explanation": "x becomes 5, then 5 * 2 gives 10."
      },
      {
        "question": "What is the output?\n\nprint('7' + '3')",
        "choices": [
          "10",
          "73",
          "'10'",
          "Error"
        ],
        "answer": "73",
        "explanation": "Both operands are strings, so + concatenates them."
      },
      {
        "question": "What happens?\n\nprint('7' + 3)",
        "choices": [
          "10",
          "73",
          "TypeError",
          "ValueError"
        ],
        "answer": "TypeError",
        "explanation": "Python cannot add a string and an integer directly."
      },
      {
        "question": "What is the output?\n\nx = 5\nprint(x > 3 and x < 10)",
        "choices": [
          "True",
          "False",
          "5",
          "Error"
        ],
        "answer": "True",
        "explanation": "Both comparisons are true."
      },
      {
        "question": "What is the output?\n\nprint(not (4 == 4))",
        "choices": [
          "True",
          "False",
          "4",
          "Error"
        ],
        "answer": "False",
        "explanation": "4 == 4 is True, and not True is False."
      },
      {
        "question": "What is the output?\n\na = [1, 2]\nb = a\nb.append(3)\nprint(a)",
        "choices": [
          "[1, 2]",
          "[1, 2, 3]",
          "[3]",
          "Error"
        ],
        "answer": "[1, 2, 3]",
        "explanation": "a and b refer to the same list."
      },
      {
        "question": "What is the output?\n\na = [1, 2]\nb = a[:]\nb.append(3)\nprint(a)",
        "choices": [
          "[1, 2]",
          "[1, 2, 3]",
          "[3]",
          "Error"
        ],
        "answer": "[1, 2]",
        "explanation": "a[:] creates a copy, so changing b does not change a."
      },
      {
        "question": "What is the output?\n\nprint(list(range(2, 7)))",
        "choices": [
          "[2, 3, 4, 5, 6]",
          "[2, 3, 4, 5, 6, 7]",
          "[0, 1, 2, 3, 4, 5, 6]",
          "[7]"
        ],
        "answer": "[2, 3, 4, 5, 6]",
        "explanation": "The start is included and the stop is excluded."
      },
      {
        "question": "How many times does the loop run?\n\nfor i in range(5):\n    print(i)",
        "choices": [
          "4",
          "5",
          "6",
          "0"
        ],
        "answer": "5",
        "explanation": "range(5) produces five values: 0 to 4."
      },
      {
        "question": "What is the output?\n\ndef square(n):\n    return n * n\nprint(square(4))",
        "choices": [
          "4",
          "8",
          "16",
          "square"
        ],
        "answer": "16",
        "explanation": "4 * 4 is 16."
      },
      {
        "question": "What is the output?\n\ndef greet():\n    print('Hi')\nresult = greet()\nprint(result)",
        "choices": [
          "Hi only",
          "Hi then None",
          "None then Hi",
          "Error"
        ],
        "answer": "Hi then None",
        "explanation": "greet prints Hi but returns None because it has no return statement."
      },
      {
        "question": "What is the output?\n\nstudent = {'name': 'Ana'}\nprint(student.get('score', 0))",
        "choices": [
          "Ana",
          "0",
          "None",
          "KeyError"
        ],
        "answer": "0",
        "explanation": "score is missing, so get() returns the default 0."
      },
      {
        "question": "What error occurs?\n\nstudent = {'name': 'Ana'}\nprint(student['score'])",
        "choices": [
          "IndexError",
          "KeyError",
          "ValueError",
          "No error"
        ],
        "answer": "KeyError",
        "explanation": "The key 'score' does not exist."
      },
      {
        "question": "What is the output?\n\nx = 0\nwhile x < 3:\n    x += 1\nprint(x)",
        "choices": [
          "0",
          "2",
          "3",
          "4"
        ],
        "answer": "3",
        "explanation": "The loop stops once x is no longer less than 3."
      },
      {
        "question": "What is the output?\n\nfor n in [1, 2, 3, 4]:\n    if n == 3:\n        break\n    print(n, end=' ')",
        "choices": [
          "1 2 ",
          "1 2 3 ",
          "3 4 ",
          "No output"
        ],
        "answer": "1 2 ",
        "explanation": "The loop stops before printing 3."
      },
      {
        "question": "What is the output?\n\nfor n in [1, 2, 3]:\n    if n == 2:\n        continue\n    print(n, end=' ')",
        "choices": [
          "1 2 3 ",
          "1 3 ",
          "2 ",
          "No output"
        ],
        "answer": "1 3 ",
        "explanation": "continue skips printing when n is 2."
      },
      {
        "question": "What is the output?\n\ntext = 'python'\nprint(text[1:4])",
        "choices": [
          "pyt",
          "yth",
          "ytho",
          "tho"
        ],
        "answer": "yth",
        "explanation": "Index 1 to before 4 gives y, t, h."
      },
      {
        "question": "What is the output?\n\nprint(len('Data'))",
        "choices": [
          "3",
          "4",
          "5",
          "Data"
        ],
        "answer": "4",
        "explanation": "The string 'Data' has four characters."
      },
      {
        "question": "What is the output?\n\nnums = [10, 20, 30]\nprint(nums[-1])",
        "choices": [
          "10",
          "20",
          "30",
          "Error"
        ],
        "answer": "30",
        "explanation": "Negative index -1 means the last item."
      },
      {
        "question": "What is the output?\n\ndata = {'a': 1, 'b': 2}\nprint(list(data.keys()))",
        "choices": [
          "['a', 'b']",
          "[1, 2]",
          "[('a', 1), ('b', 2)]",
          "Error"
        ],
        "answer": "['a', 'b']",
        "explanation": "keys() returns the dictionary keys."
      },
      {
        "question": "What is the output?\n\nprint(bool(''))",
        "choices": [
          "True",
          "False",
          "None",
          "Error"
        ],
        "answer": "False",
        "explanation": "An empty string is falsy."
      },
      {
        "question": "What is the output?\n\nprint(bool(' '))",
        "choices": [
          "True",
          "False",
          "None",
          "Error"
        ],
        "answer": "True",
        "explanation": "A space is still a character, so the string is non-empty."
      },
      {
        "question": "What is the output?\n\nx = '10'\ny = int(x)\nprint(y + 5)",
        "choices": [
          "105",
          "15",
          "10 5",
          "Error"
        ],
        "answer": "15",
        "explanation": "int('10') converts the text to the number 10."
      },
      {
        "question": "What is the output?\n\nitems = []\nitems.append('A')\nitems.append('B')\nprint(items)",
        "choices": [
          "['A', 'B']",
          "['B', 'A']",
          "[]",
          "A B"
        ],
        "answer": "['A', 'B']",
        "explanation": "append adds values to the end of the list."
      },
      {
        "question": "What is the output?\n\nx = 3\nif x:\n    print('yes')\nelse:\n    print('no')",
        "choices": [
          "yes",
          "no",
          "3",
          "Error"
        ],
        "answer": "yes",
        "explanation": "Nonzero integers are truthy."
      },
      {
        "question": "Which one is a valid function definition?",
        "choices": [
          "def add(a, b):",
          "function add(a, b):",
          "func add(a, b)",
          "define add(a, b):"
        ],
        "answer": "def add(a, b):",
        "explanation": "Python uses def followed by the function name and parameters."
      },
      {
        "question": "What is the output?\n\nx = [1, 2, 3]\nprint(x.pop())\nprint(x)",
        "choices": [
          "3 then [1, 2]",
          "1 then [2, 3]",
          "[1, 2, 3] then 3",
          "Error"
        ],
        "answer": "3 then [1, 2]",
        "explanation": "pop() removes and returns the last item by default."
      },
      {
        "question": "What is the output?\n\nprint('ha' * 3)",
        "choices": [
          "hahaha",
          "ha3",
          "Error",
          "ha ha ha"
        ],
        "answer": "hahaha",
        "explanation": "String multiplication repeats the string."
      },
      {
        "question": "What error occurs?\n\nint('five')",
        "choices": [
          "ValueError",
          "TypeError",
          "NameError",
          "IndexError"
        ],
        "answer": "ValueError",
        "explanation": "The type requested is valid, but the value 'five' cannot be parsed as an integer."
      },
      {
        "question": "What is the safest first step when solving an output tracing MCQ?",
        "choices": [
          "Guess from the answer choices",
          "Track variables from the first line",
          "Read only the print line",
          "Ignore conditions"
        ],
        "answer": "Track variables from the first line",
        "explanation": "Tracing requires following the program state from top to bottom."
      }
    ]
  },
  {
    "module_no": 1,
    "module_code": "MOD-001-PYTHON-DS-V1",
    "title": "Basics of Python Programming",
    "year_level": "Year 1",
    "version_no": 3,
    "version_name": "Version 3: Python for Data Science",
    "version_code": "PYTHON-DS-V1",
    "description": "A Python-for-data-science version introducing lists, dictionaries, table-like records, filtering, text cleaning, missing values, category counts, simple numeric summaries, and CSV-style thinking.",
    "estimated_minutes": 120,
    "sort_order": 103,
    "content_sections": [
      {
        "heading": "Version Focus: Python for Data Science",
        "body": "This version connects basic Python to data science tasks. It focuses on reading lists, dictionaries, CSV-like records, missing values, simple cleaning, summaries, and beginner data workflow thinking without requiring students to run code.",
        "key_points": [
          "Use lists for columns or sequences.",
          "Use dictionaries for records.",
          "Use loops to summarize data.",
          "Use conditions to clean or filter values."
        ]
      },
      {
        "heading": "Data as Lists",
        "body": "A dataset can be represented as a list of values. For example, a list of scores can be summarized by counting values, computing a total, or identifying the highest and lowest values.",
        "code_snippet": "scores = [80, 90, 75, 95]\naverage = sum(scores) / len(scores)\nprint(average)",
        "key_points": [
          "sum() adds numeric values.",
          "len() counts items.",
          "Average is total divided by count.",
          "Lists preserve order."
        ]
      },
      {
        "heading": "Records as Dictionaries",
        "body": "A dictionary can represent one row or record. Each key is a field name and each value is the field's content. This is useful for representing students, products, survey answers, or transactions.",
        "code_snippet": "student = {'name': 'Ana', 'program': 'BSIT', 'score': 91}\nprint(student['program'])",
        "key_points": [
          "Keys describe fields.",
          "Values store field data.",
          "A list of dictionaries can represent a table.",
          "get() helps handle optional fields."
        ]
      },
      {
        "heading": "Tables as Lists of Dictionaries",
        "body": "In beginner data science, a table can be imagined as a list of dictionaries. Each dictionary is one row. Loops can inspect each row and collect summaries.",
        "code_snippet": "rows = [\n    {'name': 'Ana', 'score': 90},\n    {'name': 'Ben', 'score': 75}\n]\nfor row in rows:\n    print(row['name'], row['score'])",
        "key_points": [
          "Each dictionary is one row.",
          "Each key is like a column name.",
          "Loops process one row at a time.",
          "This structure helps explain CSV data."
        ]
      },
      {
        "heading": "Filtering Data",
        "body": "Filtering means keeping only rows that meet a condition. A filter can be written with an if statement inside a loop or using a list comprehension.",
        "code_snippet": "scores = [50, 82, 91, 60]\npassed = [s for s in scores if s >= 75]\nprint(passed)",
        "key_points": [
          "Filtering keeps selected values.",
          "Conditions define what qualifies.",
          "List comprehensions can make simple filters shorter.",
          "Filtering is common before analysis."
        ]
      },
      {
        "heading": "Cleaning Text Data",
        "body": "Text data often has extra spaces or inconsistent casing. Cleaning may use strip(), lower(), upper(), and title(). This helps make categories consistent.",
        "code_snippet": "program = '  bsit  '\nclean_program = program.strip().upper()\nprint(clean_program)",
        "key_points": [
          "strip() removes surrounding spaces.",
          "upper() standardizes uppercase labels.",
          "lower() standardizes lowercase labels.",
          "Consistent labels reduce counting errors."
        ]
      },
      {
        "heading": "Missing Values",
        "body": "Missing values occur when data is absent. In beginner Python examples, missing values may be represented by None, empty strings, or placeholder text. A cleaning step decides how to handle them.",
        "code_snippet": "value = None\nif value is None:\n    print('Missing')",
        "key_points": [
          "None often means no value.",
          "An empty string may also indicate missing input.",
          "Do not treat missing and zero as always the same.",
          "Missing values should be handled before summary."
        ]
      },
      {
        "heading": "Counting Categories",
        "body": "Counting categories is a common data task. For example, counting how many students are BSIT or BSCS can be done with a dictionary. Each category becomes a key and its count becomes the value.",
        "code_snippet": "programs = ['BSIT', 'BSCS', 'BSIT']\ncounts = {}\nfor program in programs:\n    counts[program] = counts.get(program, 0) + 1\nprint(counts)",
        "key_points": [
          "Dictionaries are useful for frequency counts.",
          "get(key, 0) handles first-time categories.",
          "Counting helps summarize categorical variables.",
          "Clean labels before counting."
        ]
      },
      {
        "heading": "Simple Numeric Summaries",
        "body": "Data science often begins with simple summaries: count, minimum, maximum, total, and average. These summaries help describe a dataset before deeper analysis.",
        "code_snippet": "values = [4, 7, 10]\nprint(min(values), max(values), sum(values) / len(values))",
        "key_points": [
          "min() gives the smallest value.",
          "max() gives the largest value.",
          "Average needs sum and count.",
          "Check for empty lists before dividing."
        ]
      },
      {
        "heading": "CSV Thinking Without File I/O",
        "body": "CSV files store rows of data separated by commas. Even without reading real files, students can understand CSV-like data as rows and fields. A parsed CSV row can become a dictionary.",
        "code_snippet": "header = ['name', 'score']\nrow = ['Ana', '90']\nrecord = {'name': row[0], 'score': int(row[1])}\nprint(record)",
        "key_points": [
          "CSV columns become fields.",
          "Numeric text often needs conversion.",
          "Rows can become dictionaries.",
          "Data cleaning often happens after parsing."
        ]
      },
      {
        "heading": "Code Snippets in Data Science MCQs",
        "body": "Many data science MCQs use short snippets that clean, filter, count, or summarize values. The key is to understand what each line does to the data structure.",
        "key_points": [
          "Identify the data structure first.",
          "Track changes after each loop iteration.",
          "Watch for conversion from string to number.",
          "Check whether the snippet mutates or creates a new value."
        ]
      },
      {
        "heading": "Module Summary",
        "body": "Python for data science starts with lists, dictionaries, loops, conditions, and text cleaning. These are the foundation for future libraries like pandas, NumPy, and scikit-learn. Students should be able to read small data snippets and explain how they transform or summarize data.",
        "key_points": [
          "Lists can represent columns.",
          "Dictionaries can represent rows.",
          "Loops can process datasets.",
          "Cleaning improves data quality.",
          "Summaries help understand data."
        ]
      }
    ],
    "mcq_questions": [
      {
        "question": "Which Python structure is best for representing one record with named fields?",
        "choices": [
          "dictionary",
          "set",
          "integer",
          "comment"
        ],
        "answer": "dictionary",
        "explanation": "A dictionary stores key-value pairs, which can represent field names and values."
      },
      {
        "question": "What is the output?\n\nscores = [80, 90, 100]\nprint(sum(scores) / len(scores))",
        "choices": [
          "90.0",
          "270",
          "3",
          "80"
        ],
        "answer": "90.0",
        "explanation": "The sum is 270 and the count is 3, so the average is 90.0."
      },
      {
        "question": "What is the output?\n\nprogram = '  bsit  '\nprint(program.strip().upper())",
        "choices": [
          "bsit",
          "BSIT",
          "  BSIT  ",
          "Bsit"
        ],
        "answer": "BSIT",
        "explanation": "strip() removes spaces and upper() converts text to uppercase."
      },
      {
        "question": "Which value usually represents absence of a value in Python?",
        "choices": [
          "None",
          "0 always",
          "'0' always",
          "False always"
        ],
        "answer": "None",
        "explanation": "None is commonly used to represent no value or missing data."
      },
      {
        "question": "What is the output?\n\nrows = [{'score': 90}, {'score': 80}]\nprint(rows[0]['score'])",
        "choices": [
          "90",
          "80",
          "score",
          "Error"
        ],
        "answer": "90",
        "explanation": "rows[0] is the first dictionary, and ['score'] accesses 90."
      },
      {
        "question": "What is the output?\n\nscores = [50, 82, 91, 60]\npassed = [s for s in scores if s >= 75]\nprint(passed)",
        "choices": [
          "[82, 91]",
          "[50, 60]",
          "[75]",
          "[50, 82, 91, 60]"
        ],
        "answer": "[82, 91]",
        "explanation": "Only scores at least 75 are kept."
      },
      {
        "question": "What does counts.get(program, 0) do in a counting dictionary?",
        "choices": [
          "Gets the current count or 0 if the program is not yet a key",
          "Deletes the key",
          "Always returns 0",
          "Sorts the dictionary"
        ],
        "answer": "Gets the current count or 0 if the program is not yet a key",
        "explanation": "get() avoids KeyError and provides a starting count."
      },
      {
        "question": "What is the output?\n\nvalues = [4, 7, 10]\nprint(min(values), max(values))",
        "choices": [
          "4 10",
          "10 4",
          "7 10",
          "4 7"
        ],
        "answer": "4 10",
        "explanation": "min gives the smallest value and max gives the largest."
      },
      {
        "question": "Why should text categories be cleaned before counting?",
        "choices": [
          "To avoid treating 'BSIT' and ' bsit ' as different categories",
          "To make numbers larger",
          "To remove all rows",
          "To convert lists into sets only"
        ],
        "answer": "To avoid treating 'BSIT' and ' bsit ' as different categories",
        "explanation": "Inconsistent spacing or casing can create inaccurate counts."
      },
      {
        "question": "What is the output?\n\nprograms = ['BSIT', 'BSCS', 'BSIT']\ncounts = {}\nfor p in programs:\n    counts[p] = counts.get(p, 0) + 1\nprint(counts['BSIT'])",
        "choices": [
          "1",
          "2",
          "3",
          "0"
        ],
        "answer": "2",
        "explanation": "BSIT appears twice."
      },
      {
        "question": "Which operation converts the text '90' to the number 90?",
        "choices": [
          "int('90')",
          "str(90)",
          "list('90')",
          "bool('90')"
        ],
        "answer": "int('90')",
        "explanation": "int() converts valid numeric text to an integer."
      },
      {
        "question": "What is a list of dictionaries commonly used to represent?",
        "choices": [
          "A table of rows",
          "A single character",
          "A boolean only",
          "A comment block"
        ],
        "answer": "A table of rows",
        "explanation": "Each dictionary can represent one row, and the list stores many rows."
      },
      {
        "question": "What is the output?\n\nrecord = {'name': 'Ana', 'score': '90'}\nprint(int(record['score']) + 5)",
        "choices": [
          "95",
          "905",
          "90 5",
          "Error"
        ],
        "answer": "95",
        "explanation": "The score text is converted to integer before adding 5."
      },
      {
        "question": "What is the risk of computing sum(values) / len(values) when values is empty?",
        "choices": [
          "Division by zero",
          "The answer is always 0",
          "It becomes a string",
          "No possible issue"
        ],
        "answer": "Division by zero",
        "explanation": "len([]) is 0, so dividing by zero causes an error."
      },
      {
        "question": "What is filtering?",
        "choices": [
          "Keeping only data that matches a condition",
          "Randomly deleting all values",
          "Changing every number to text",
          "Printing a variable"
        ],
        "answer": "Keeping only data that matches a condition",
        "explanation": "Filtering selects rows or values based on a condition."
      },
      {
        "question": "What is the output?\n\nnames = ['Ana', 'Ben']\nfor name in names:\n    print(name.upper(), end=' ')",
        "choices": [
          "ANA BEN ",
          "Ana Ben ",
          "['ANA', 'BEN']",
          "Error"
        ],
        "answer": "ANA BEN ",
        "explanation": "Each name is converted to uppercase and printed on one line."
      },
      {
        "question": "Which Python method removes spaces at the beginning and end of a string?",
        "choices": [
          "strip()",
          "trim_all()",
          "remove_space()",
          "delete()"
        ],
        "answer": "strip()",
        "explanation": "strip() removes leading and trailing whitespace."
      },
      {
        "question": "What is the output?\n\nrows = [{'program': 'BSIT'}, {'program': 'BSCS'}]\nprint(len(rows))",
        "choices": [
          "1",
          "2",
          "BSIT",
          "Error"
        ],
        "answer": "2",
        "explanation": "The list has two dictionaries."
      },
      {
        "question": "What does a key in a dictionary often represent in data science examples?",
        "choices": [
          "A column or field name",
          "A loop error",
          "A file extension",
          "A compiler"
        ],
        "answer": "A column or field name",
        "explanation": "Keys such as 'name' or 'score' act like field or column names."
      },
      {
        "question": "What is the output?\n\nx = None\nprint(x is None)",
        "choices": [
          "True",
          "False",
          "None",
          "Error"
        ],
        "answer": "True",
        "explanation": "is None is the standard way to check for None."
      },
      {
        "question": "Which task is data cleaning?",
        "choices": [
          "Standardizing 'bsit', 'BSIT', and ' BSIT ' into one label",
          "Making every number random",
          "Deleting the program file",
          "Ignoring missing values always"
        ],
        "answer": "Standardizing 'bsit', 'BSIT', and ' BSIT ' into one label",
        "explanation": "Cleaning makes values consistent for accurate analysis."
      },
      {
        "question": "What is the output?\n\nvalues = [1, 2, 3]\ntotal = 0\nfor v in values:\n    total += v\nprint(total)",
        "choices": [
          "0",
          "3",
          "6",
          "123"
        ],
        "answer": "6",
        "explanation": "The loop adds 1 + 2 + 3."
      },
      {
        "question": "What is a categorical variable example?",
        "choices": [
          "Program such as BSIT or BSCS",
          "Height in centimeters only",
          "Age in years only",
          "Final numeric grade only"
        ],
        "answer": "Program such as BSIT or BSCS",
        "explanation": "Program labels are categories."
      },
      {
        "question": "What is a numeric summary?",
        "choices": [
          "Mean, minimum, maximum, or count",
          "A comment symbol",
          "A function name only",
          "A CSS style"
        ],
        "answer": "Mean, minimum, maximum, or count",
        "explanation": "Numeric summaries describe numeric data."
      },
      {
        "question": "What is the output?\n\nheader = ['name', 'score']\nrow = ['Ana', '90']\nrecord = {'name': row[0], 'score': int(row[1])}\nprint(record['score'])",
        "choices": [
          "90",
          "'90'",
          "Ana",
          "Error"
        ],
        "answer": "90",
        "explanation": "row[1] is converted from text to integer."
      },
      {
        "question": "Why is Python useful before learning pandas?",
        "choices": [
          "It teaches the core logic of lists, dictionaries, loops, and conditions",
          "It replaces all databases",
          "It removes the need to understand data",
          "It only makes graphics"
        ],
        "answer": "It teaches the core logic of lists, dictionaries, loops, and conditions",
        "explanation": "Pandas becomes easier when students understand basic Python data structures first."
      },
      {
        "question": "What is the output?\n\nscores = [90, None, 80]\nclean = [s for s in scores if s is not None]\nprint(clean)",
        "choices": [
          "[90, 80]",
          "[None]",
          "[90, None, 80]",
          "[]"
        ],
        "answer": "[90, 80]",
        "explanation": "The filter removes the None value."
      },
      {
        "question": "What is the output?\n\nrecords = [{'score': 90}, {'score': 80}, {'score': 70}]\nhigh = [r for r in records if r['score'] >= 80]\nprint(len(high))",
        "choices": [
          "1",
          "2",
          "3",
          "0"
        ],
        "answer": "2",
        "explanation": "The records with scores 90 and 80 satisfy the condition."
      },
      {
        "question": "Which is best for counting repeated labels?",
        "choices": [
          "A dictionary of counts",
          "A single float",
          "A comment",
          "A syntax error"
        ],
        "answer": "A dictionary of counts",
        "explanation": "A dictionary can map each label to its frequency."
      },
      {
        "question": "What should you identify first when reading a data science snippet?",
        "choices": [
          "The data structure being used",
          "The color of the code",
          "The file size",
          "The font"
        ],
        "answer": "The data structure being used",
        "explanation": "Understanding whether the data is a list, dictionary, or list of dictionaries helps explain the logic."
      }
    ]
  }
]
JSON, true);

        foreach ($modules as $module) {
            ModuleLibraryItem::updateOrCreate(
                ['module_code' => $module['module_code']],
                [
                    'module_no'          => $module['module_no'],
                    'title'              => $module['title'],
                    'year_level'         => $module['year_level'],
                    'version_no'         => $module['version_no'],
                    'version_name'       => $module['version_name'],
                    'version_code'       => $module['version_code'],
                    'description'        => $module['description'],
                    'estimated_minutes'  => $module['estimated_minutes'],
                    'content_sections'   => json_encode($module['content_sections'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'mcq_questions'      => json_encode($module['mcq_questions'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'sort_order'         => $module['sort_order'],
                    'is_active'          => true,
                ]
            );

            $this->command->info('Seeded ' . $module['module_code'] . ' — ' . $module['version_name']);
        }

        $this->command->info('Done! Detailed Python module library seeder completed.');
    }
}
