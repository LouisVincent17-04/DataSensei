<?php

namespace Database\Seeders;

use App\Models\IntendedLearningOutcome;
use Illuminate\Database\Seeder;

class IntendedLearningOutcomeSeeder extends Seeder
{
    public function run(): void
    {
        $topics = [
            1 => 'Basics of Python Programming',
            2 => 'Basics of Statistics',
            3 => 'Introduction to Data Science',
            4 => 'Mathematical Analysis I',
            5 => 'Methods of Proof',
            6 => 'Modeling and Simulation',
            7 => 'Algorithms & Data Structures for Data Scientists',
            8 => 'Statistical Methods & Experimental Design',
            9 => 'Applied Matrix Analysis',
            10 => 'Database Management for Data Science',
            11 => 'Introduction to Bayesian Data Analysis',
            12 => 'Introductory Forecasting',
            13 => 'Introduction to Optimization Techniques',
            14 => 'Machine Learning 1: Supervised Learning',
            15 => 'Data Visualization',
            16 => 'Multivariate Analysis',
            17 => 'Deep Learning',
            18 => 'Privacy, Ethics & Data Governance',
            19 => 'Introduction to Artificial Intelligence',
            20 => 'Analysis of Unstructured Data',
            21 => 'Machine Learning 2: Unsupervised Learning',
            22 => 'Big Data & Cloud Computing',
            23 => 'Data Warehousing',
            24 => 'Sequential Decision Making',
        ];

        foreach ($topics as $moduleNo => $topic) {
            $templates = [
                ['Understand core concepts', 'Explain the key vocabulary, purpose, and basic ideas of ' . $topic . '.'],
                ['Apply procedures', 'Use appropriate steps, formulas, code, or tools to solve guided problems in ' . $topic . '.'],
                ['Analyze results', 'Interpret outputs, identify patterns, and connect results to the data problem.'],
                ['Evaluate quality', 'Check assumptions, limitations, correctness, and reliability of work.'],
                ['Create solution', 'Produce a small task, model, query, visualization, or decision based on ' . $topic . '.'],
            ];

            foreach ($templates as $index => [$title, $description]) {
                $sort = $index + 1;
                IntendedLearningOutcome::updateOrCreate(
                    ['ilo_code' => 'M' . str_pad((string) $moduleNo, 2, '0', STR_PAD_LEFT) . '-ILO' . $sort],
                    [
                        'module_no' => $moduleNo,
                        'title' => $title,
                        'description' => $description,
                        'mastery_threshold' => 75,
                        'sort_order' => ($moduleNo * 10) + $sort,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
