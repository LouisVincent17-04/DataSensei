<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleLibraryItem extends Model
{
    use HasFactory;

    protected $table = 'module_library_items';

    protected $fillable = [
        'module_no',
        'module_code',
        'title',
        'year_level',
        'version_no',
        'version_name',
        'version_code',
        'description',
        'estimated_minutes',
        'content_sections',
        'mcq_questions',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'module_no' => 'integer',
        'version_no' => 'integer',
        'estimated_minutes' => 'integer',
        'content_sections' => 'array',
        'mcq_questions' => 'array',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];


    public function classAssignments()
    {
        return $this->hasMany(ClassModuleAssignment::class, 'module_library_item_id');
    }

    public static function defaultLibrary(): array
    {
        return [
            [
                'module_no' => 1,
                'title' => 'Basics of Python Programming',
                'year_level' => 'Year 1',
                'versions' => [
                    [
                        'version_no' => 1,
                        'version_name' => 'Version 1: Python Fundamentals',
                        'version_code' => 'PYTHON-BASIC-V1',
                        'description' => 'Covers variables, data types, input/output, operators, conditionals, loops, functions, and basic code reading.',
                    ],
                    [
                        'version_no' => 2,
                        'version_name' => 'Version 2: Python Practice and MCQ Review',
                        'version_code' => 'PYTHON-BASIC-V2',
                        'description' => 'A more practice-based version with code snippets, output tracing, and multiple-choice questions.',
                    ],
                    [
                        'version_no' => 3,
                        'version_name' => 'Version 3: Python for Data Science',
                        'version_code' => 'PYTHON-DS-V1',
                        'description' => 'Focuses on Python basics used in data science, including lists, dictionaries, CSV handling, and simple data cleaning snippets.',
                    ],
                ],
            ],
            [
                'module_no' => 2,
                'title' => 'Basics of Statistics',
                'year_level' => 'Year 1',
                'versions' => [
                    [
                        'version_no' => 1,
                        'version_name' => 'Version 1: Descriptive Statistics',
                        'version_code' => 'STAT-BASIC-V1',
                        'description' => 'Covers mean, median, mode, range, variance, standard deviation, and basic probability.',
                    ],
                    [
                        'version_no' => 2,
                        'version_name' => 'Version 2: Applied Statistics',
                        'version_code' => 'STAT-BASIC-V2',
                        'description' => 'Uses small dataset examples, interpretation questions, and multiple-choice exercises.',
                    ],
                ],
            ],
            [
                'module_no' => 3,
                'title' => 'Introduction to Data Science',
                'year_level' => 'Year 1',
                'versions' => [
                    [
                        'version_no' => 1,
                        'version_name' => 'Version 1: Foundations',
                        'version_code' => 'DS-INTRO-V1',
                        'description' => 'Introduces data science, data lifecycle, datasets, common tools, and roles in data projects.',
                    ],
                    [
                        'version_no' => 2,
                        'version_name' => 'Version 2: Project-Based Introduction',
                        'version_code' => 'DS-INTRO-V2',
                        'description' => 'Introduces data science through simple scenarios, mini case studies, and MCQ-based interpretation.',
                    ],
                ],
            ],
            ['module_no'=>4,'title'=>'Mathematical Analysis I','year_level'=>'Year 1','versions'=>[['version_no'=>1,'version_name'=>'Version 1: Foundations of Analysis','version_code'=>'MATH-ANALYSIS1-V1','description'=>'Covers functions, limits, continuity, sequences, and basic mathematical reasoning.']]],
            ['module_no'=>5,'title'=>'Methods of Proof','year_level'=>'Year 1','versions'=>[['version_no'=>1,'version_name'=>'Version 1: Proof Techniques','version_code'=>'PROOF-V1','description'=>'Covers direct proof, contradiction, contrapositive, induction, and logic-based MCQ items.']]],
            ['module_no'=>6,'title'=>'Modeling and Simulation','year_level'=>'Year 1','versions'=>[['version_no'=>1,'version_name'=>'Version 1: Simulation Basics','version_code'=>'MODELING-SIM-V1','description'=>'Introduces model assumptions, variables, simulation flow, and interpretation of simulated results.']]],
            ['module_no'=>7,'title'=>'Algorithms & Data Structures for Data Scientists','year_level'=>'Year 2','versions'=>[['version_no'=>1,'version_name'=>'Version 1: Algorithmic Thinking','version_code'=>'ALGO-DS-V1','description'=>'Covers arrays, stacks, queues, dictionaries, searching, sorting, and algorithm efficiency.']]],
            ['module_no'=>8,'title'=>'Statistical Methods & Experimental Design','year_level'=>'Year 2','versions'=>[['version_no'=>1,'version_name'=>'Version 1: Statistical Testing','version_code'=>'STAT-EXP-V1','description'=>'Covers sampling, hypothesis testing, confidence intervals, experimental design, and interpretation.']]],
            ['module_no'=>9,'title'=>'Applied Matrix Analysis','year_level'=>'Year 2','versions'=>[['version_no'=>1,'version_name'=>'Version 1: Matrix Applications','version_code'=>'MATRIX-V1','description'=>'Covers matrices, vectors, linear systems, transformations, and data science applications.']]],
            ['module_no'=>10,'title'=>'Database Management for Data Science','year_level'=>'Year 2','versions'=>[['version_no'=>1,'version_name'=>'Version 1: SQL and Data Storage','version_code'=>'DB-DS-V1','description'=>'Covers relational databases, SQL queries, joins, grouping, filtering, and data extraction.']]],
            ['module_no'=>11,'title'=>'Introduction to Bayesian Data Analysis','year_level'=>'Year 2','versions'=>[['version_no'=>1,'version_name'=>'Version 1: Bayesian Foundations','version_code'=>'BAYES-V1','description'=>'Covers prior, likelihood, posterior, Bayes theorem, and introductory Bayesian reasoning.']]],
            ['module_no'=>12,'title'=>'Introductory Forecasting','year_level'=>'Year 2','versions'=>[['version_no'=>1,'version_name'=>'Version 1: Forecasting Basics','version_code'=>'FORECAST-V1','description'=>'Covers time series concepts, trend, seasonality, moving averages, and basic forecast evaluation.']]],
            ['module_no'=>13,'title'=>'Introduction to Optimization Techniques','year_level'=>'Year 3','versions'=>[['version_no'=>1,'version_name'=>'Version 1: Optimization Concepts','version_code'=>'OPTIMIZATION-V1','description'=>'Covers objective functions, constraints, feasible regions, and basic optimization interpretation.']]],
            [
                'module_no'=>14,
                'title'=>'Machine Learning 1: Supervised Learning',
                'year_level'=>'Year 3',
                'versions'=>[
                    ['version_no'=>1,'version_name'=>'Version 1: Supervised Learning Foundations','version_code'=>'ML1-V1','description'=>'Covers regression, classification, train-test split, model evaluation, and supervised workflow.'],
                    ['version_no'=>2,'version_name'=>'Version 2: Project and Interpretation','version_code'=>'ML1-PROJECT-V1','description'=>'Uses dataset scenarios, confusion matrix interpretation, and MCQ-based model evaluation.'],
                ],
            ],
            ['module_no'=>15,'title'=>'Data Visualization','year_level'=>'Year 3','versions'=>[['version_no'=>1,'version_name'=>'Version 1: Visual Storytelling','version_code'=>'DATAVIZ-V1','description'=>'Covers chart types, visual encoding, interpretation, and choosing the right visualization.']]],
            ['module_no'=>16,'title'=>'Multivariate Analysis','year_level'=>'Year 3','versions'=>[['version_no'=>1,'version_name'=>'Version 1: Multiple Variable Analysis','version_code'=>'MULTIVARIATE-V1','description'=>'Covers relationships among multiple variables, correlation, dimensionality, and interpretation.']]],
            ['module_no'=>17,'title'=>'Deep Learning','year_level'=>'Year 3','versions'=>[['version_no'=>1,'version_name'=>'Version 1: Neural Network Basics','version_code'=>'DEEPLEARNING-V1','description'=>'Covers neurons, layers, activation functions, loss, training, and deep learning concepts.']]],
            ['module_no'=>18,'title'=>'Privacy, Ethics & Data Governance','year_level'=>'Year 3','versions'=>[['version_no'=>1,'version_name'=>'Version 1: Responsible Data Use','version_code'=>'ETHICS-V1','description'=>'Covers data privacy, consent, bias, responsible AI, governance, and ethical scenarios.']]],
            ['module_no'=>19,'title'=>'Introduction to Artificial Intelligence','year_level'=>'Year 4','versions'=>[['version_no'=>1,'version_name'=>'Version 1: AI Concepts','version_code'=>'AI-INTRO-V1','description'=>'Covers intelligent agents, search, reasoning, knowledge representation, and AI applications.']]],
            ['module_no'=>20,'title'=>'Analysis of Unstructured Data','year_level'=>'Year 4','versions'=>[['version_no'=>1,'version_name'=>'Version 1: Text and Media Data','version_code'=>'UNSTRUCTURED-V1','description'=>'Covers text, images, documents, preprocessing, and non-tabular data analysis.']]],
            ['module_no'=>21,'title'=>'Machine Learning 2: Unsupervised Learning','year_level'=>'Year 4','versions'=>[['version_no'=>1,'version_name'=>'Version 1: Unsupervised Learning Foundations','version_code'=>'ML2-V1','description'=>'Covers clustering, dimensionality reduction, anomaly detection, and unsupervised workflow.']]],
            ['module_no'=>22,'title'=>'Big Data & Cloud Computing','year_level'=>'Year 4','versions'=>[['version_no'=>1,'version_name'=>'Version 1: Scalable Data Systems','version_code'=>'BIGDATA-CLOUD-V1','description'=>'Covers big data concepts, cloud services, distributed systems, and scalable processing.']]],
            ['module_no'=>23,'title'=>'Data Warehousing','year_level'=>'Year 4','versions'=>[['version_no'=>1,'version_name'=>'Version 1: Warehousing and ETL','version_code'=>'WAREHOUSE-V1','description'=>'Covers data warehouses, ETL, star schema, fact tables, dimension tables, and reporting.']]],
            ['module_no'=>24,'title'=>'Sequential Decision Making','year_level'=>'Year 4','versions'=>[['version_no'=>1,'version_name'=>'Version 1: Decision Processes','version_code'=>'SDM-V1','description'=>'Covers sequential decisions, rewards, policies, and reinforcement learning basics.']]],
        ];
    }

    public static function seedDefaultLibrary(): void
    {
        foreach (self::defaultLibrary() as $module) {
            foreach ($module['versions'] as $version) {
                $moduleCode = 'MOD-' . str_pad((string) $module['module_no'], 3, '0', STR_PAD_LEFT) . '-' . $version['version_code'];

                self::updateOrCreate(
                    ['module_code' => $moduleCode],
                    [
                        'module_no' => $module['module_no'],
                        'module_code' => $moduleCode,
                        'title' => $module['title'],
                        'year_level' => $module['year_level'],
                        'version_no' => $version['version_no'],
                        'version_name' => $version['version_name'],
                        'version_code' => $version['version_code'],
                        'description' => $version['description'],
                        'estimated_minutes' => 45,
                        'content_sections' => self::defaultContentSections($module['title']),
                        'mcq_questions' => self::defaultMcqQuestions($module['title']),
                        'sort_order' => ($module['module_no'] * 100) + $version['version_no'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    private static function defaultContentSections(string $title): array
    {
        return [
            [
                'heading' => 'Learning Overview',
                'body' => 'Specific module contents to be followed. This module is reading-based and supports examples, short explanations, and code snippets.',
            ],
            [
                'heading' => 'Code Snippet Notes',
                'body' => 'Code snippets are for reading, tracing, and interpretation only. Learners will not run code inside a compiler.',
                'code_snippet' => self::sampleSnippetFor($title),
            ],
            [
                'heading' => 'Assessment Style',
                'body' => 'Assessment is mainly multiple-choice questions, output tracing, concept identification, and scenario-based interpretation.',
            ],
        ];
    }

    private static function defaultMcqQuestions(string $title): array
    {
        return [
            [
                'question' => 'Sample question for ' . $title . '. Replace this with the final module-specific question.',
                'choices' => ['Choice A', 'Choice B', 'Choice C', 'Choice D'],
                'answer' => 'Choice A',
                'explanation' => 'Specific explanation to be followed.',
            ],
        ];
    }

    private static function sampleSnippetFor(string $title): ?string
    {
        if ($title === 'Basics of Python Programming') {
            return "x = 5\nx = x + 2\nprint(x)";
        }

        if ($title === 'Database Management for Data Science') {
            return "SELECT program, COUNT(*) AS total\nFROM students\nGROUP BY program;";
        }

        if (str_contains($title, 'Machine Learning')) {
            return "accuracy = correct_predictions / total_predictions";
        }

        return null;
    }
}
