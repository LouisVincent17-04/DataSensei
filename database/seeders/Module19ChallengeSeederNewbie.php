<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;

class Module19ChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (!$category) {
            $this->command->error("Newbie category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 19 — Introduction to Artificial Intelligence (Newbie)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introduction to Artificial Intelligence',
            'description'           => 'Test your knowledge of the very basics of Artificial Intelligence — what it is, where it came from, how it relates to machine learning and deep learning, and where it shows up in everyday life. No math or coding experience needed!',
            'time_limit_seconds'    => 900,
            'base_xp'               => 500,
            'order_index'           => 19,
        ]);

        $this->command->info("Seeding 30 newbie-friendly questions...");

        $qaData = [

            // ── WHAT IS AI ────────────────────────────────────────────────
            [
                'q' => 'What is Artificial Intelligence (AI)?',
                'opts' => [
                    ['A robot that looks and acts exactly like a human', false],
                    ['The simulation of human intelligence by computer systems', true],
                    ['A type of internet connection that works automatically', false],
                    ['Software that only performs basic arithmetic', false],
                ],
            ],
            [
                'q' => 'Which of the following best describes the goal of AI?',
                'opts' => [
                    ['To replace all humans in every job immediately', false],
                    ['To build machines that can perform tasks that normally require human intelligence', true],
                    ['To make computers run faster', false],
                    ['To store as much data as possible on a hard drive', false],
                ],
            ],
            [
                'q' => 'Who is widely considered the "father of Artificial Intelligence"?',
                'opts' => [
                    ['Albert Einstein', false],
                    ['Alan Turing', true],
                    ['Bill Gates', false],
                    ['Nikola Tesla', false],
                ],
            ],
            [
                'q' => 'The term "Artificial Intelligence" was officially coined at which event?',
                'opts' => [
                    ['The World Technology Summit of 1960', false],
                    ['The Dartmouth Conference of 1956', true],
                    ['The MIT Computer Science Symposium of 1970', false],
                    ['The Silicon Valley Tech Expo of 1980', false],
                ],
            ],
            [
                'q' => 'The Turing Test was proposed to evaluate whether a machine can:',
                'opts' => [
                    ['Run faster than a human', false],
                    ['Exhibit intelligent behaviour indistinguishable from a human in conversation', true],
                    ['Solve mathematical equations without errors', false],
                    ['Store more data than a human brain', false],
                ],
            ],
            [
                'q' => 'Which of the following is a real-world example of AI in everyday life?',
                'opts' => [
                    ['A manual typewriter', false],
                    ['A voice assistant like Siri or Alexa', true],
                    ['A regular electric fan', false],
                    ['A printed newspaper', false],
                ],
            ],

            // ── MACHINE LEARNING FUNDAMENTALS ─────────────────────────────
            [
                'q' => 'Machine Learning is a subset of which broader field?',
                'opts' => [
                    ['Computer Hardware', false],
                    ['Artificial Intelligence', true],
                    ['Networking', false],
                    ['Database Administration', false],
                ],
            ],
            [
                'q' => 'What does a machine learning model do?',
                'opts' => [
                    ['It follows a fixed set of hand-written rules forever', false],
                    ['It learns patterns from data to make predictions or decisions', true],
                    ['It searches the internet for answers', false],
                    ['It only processes text documents', false],
                ],
            ],
            [
                'q' => 'In "supervised learning," training data includes:',
                'opts' => [
                    ['Only raw data with no labels', false],
                    ['Input data paired with correct output labels', true],
                    ['Data that has never been seen before', false],
                    ['Data collected from social media only', false],
                ],
            ],
            [
                'q' => 'Which type of machine learning has NO labelled answers in the training data?',
                'opts' => [
                    ['Supervised learning', false],
                    ['Reinforcement learning', false],
                    ['Unsupervised learning', true],
                    ['Transfer learning', false],
                ],
            ],
            [
                'q' => 'Reinforcement learning teaches an AI by:',
                'opts' => [
                    ['Giving it labelled examples of correct answers', false],
                    ['Rewarding it for good actions and penalizing it for bad actions', true],
                    ['Downloading answers from the internet', false],
                    ['Copying patterns from another trained model', false],
                ],
            ],
            [
                'q' => 'Which of the following is an example of a supervised learning task?',
                'opts' => [
                    ['Grouping customers by shopping behaviour', false],
                    ['Predicting whether an email is spam or not spam', true],
                    ['A robot learning to walk through trial and error', false],
                    ['Finding hidden patterns in unlabelled sensor data', false],
                ],
            ],

            // ── NEURAL NETWORKS & DEEP LEARNING ───────────────────────────
            [
                'q' => 'A neural network is loosely inspired by:',
                'opts' => [
                    ['The structure of a computer hard drive', false],
                    ['Neurons and connections in the human brain', true],
                    ['The layout of a spreadsheet', false],
                    ['The design of a calculator', false],
                ],
            ],
            [
                'q' => 'What makes "deep learning" deep?',
                'opts' => [
                    ['It uses very large datasets only', false],
                    ['It uses neural networks with many layers', true],
                    ['It learns more slowly than other methods', false],
                    ['It requires human supervision at every step', false],
                ],
            ],
            [
                'q' => 'Which of the following is a popular application of deep learning?',
                'opts' => [
                    ['Sorting files alphabetically', false],
                    ['Recognizing faces in photos', true],
                    ['Sending emails', false],
                    ['Browsing the web', false],
                ],
            ],

            // ── COMPUTER VISION ───────────────────────────────────────────
            [
                'q' => 'What is Computer Vision in AI?',
                'opts' => [
                    ['A screen setting that improves monitor clarity', false],
                    ['The ability of machines to interpret and understand visual information like images and video', true],
                    ['Software that helps people with poor eyesight read text', false],
                    ['A type of camera used in self-driving cars', false],
                ],
            ],
            [
                'q' => 'CNNs (Convolutional Neural Networks) are most commonly used for:',
                'opts' => [
                    ['Processing audio files', false],
                    ['Image recognition and visual tasks', true],
                    ['Translating languages', false],
                    ['Playing board games', false],
                ],
            ],
            [
                'q' => 'Object detection AI can:',
                'opts' => [
                    ['Only count the number of objects in an image', false],
                    ['Identify and locate multiple objects within an image', true],
                    ['Only describe images in text form', false],
                    ['Only detect objects in video, not still images', false],
                ],
            ],

            // ── NLP & LLMs ────────────────────────────────────────────────
            [
                'q' => 'What does NLP stand for in AI?',
                'opts' => [
                    ['Networked Learning Protocol', false],
                    ['Natural Language Processing', true],
                    ['Neural Loop Processing', false],
                    ['Numeric Logic Program', false],
                ],
            ],
            [
                'q' => 'Which of the following is an example of an NLP task?',
                'opts' => [
                    ['Classifying images of cats and dogs', false],
                    ['Translating text from English to Spanish', true],
                    ['Detecting obstacles in a self-driving car', false],
                    ['Predicting stock prices from charts', false],
                ],
            ],
            [
                'q' => 'A Large Language Model (LLM) like ChatGPT is primarily trained on:',
                'opts' => [
                    ['Video game scores', false],
                    ['Massive amounts of text from books, websites, and other written sources', true],
                    ['Medical X-ray images', false],
                    ['Satellite imagery', false],
                ],
            ],
            [
                'q' => 'What is a "Transformer" in the context of AI language models?',
                'opts' => [
                    ['A device that converts electrical voltage', false],
                    ['A neural network architecture that powers modern language AI using an attention mechanism', true],
                    ['A tool that transforms images into text automatically', false],
                    ['A method to compress large datasets', false],
                ],
            ],

            // ── GENERATIVE AI ─────────────────────────────────────────────
            [
                'q' => 'What does "Generative AI" do?',
                'opts' => [
                    ['It only classifies existing content into categories', false],
                    ['It creates new content such as text, images, audio, or video', true],
                    ['It monitors and reports on data security threats', false],
                    ['It speeds up internet search results', false],
                ],
            ],
            [
                'q' => 'Which of the following is an example of Generative AI?',
                'opts' => [
                    ['A spam filter', false],
                    ['An AI that creates realistic images from text descriptions', true],
                    ['A weather forecasting system', false],
                    ['A barcode scanner', false],
                ],
            ],

            // ── REINFORCEMENT LEARNING ────────────────────────────────────
            [
                'q' => 'AlphaGo, the AI that defeated world champions at the board game Go, primarily used which type of learning?',
                'opts' => [
                    ['Supervised learning only', false],
                    ['Reinforcement learning combined with deep learning', true],
                    ['Unsupervised learning', false],
                    ['Simple rule-based programming', false],
                ],
            ],
            [
                'q' => 'In reinforcement learning, the AI system that makes decisions is called the:',
                'opts' => [
                    ['Dataset', false],
                    ['Agent', true],
                    ['Label', false],
                    ['Epoch', false],
                ],
            ],

            // ── AI ETHICS ─────────────────────────────────────────────────
            [
                'q' => 'AI "bias" refers to:',
                'opts' => [
                    ['The speed at which an AI processes data', false],
                    ['Unfair or skewed outputs caused by imbalanced or unrepresentative training data', true],
                    ['A feature that makes AI prefer certain programming languages', false],
                    ['The angle at which a camera captures an image', false],
                ],
            ],
            [
                'q' => 'Why is "Responsible AI" important?',
                'opts' => [
                    ['To make AI models run faster on older hardware', false],
                    ['To ensure AI is used fairly, safely, and without causing harm to individuals or society', true],
                    ['To reduce the cost of training AI models', false],
                    ['To prevent AI from learning new information after deployment', false],
                ],
            ],
            [
                'q' => 'Which of the following is a concern related to AI ethics?',
                'opts' => [
                    ['AI taking too long to boot up', false],
                    ['Facial recognition systems being less accurate for certain demographic groups', true],
                    ['AI models using too much screen brightness', false],
                    ['AI programs saving files in outdated formats', false],
                ],
            ],

            // ── AI IN THE REAL WORLD ──────────────────────────────────────
            [
                'q' => 'Which industry is AI NOT commonly used in today?',
                'opts' => [
                    ['Healthcare', false],
                    ['Finance', false],
                    ['Education', false],
                    ['AI is used across all of these industries', true],
                ],
            ],
            [
                'q' => 'Self-driving cars are an example of AI combining which two major fields?',
                'opts' => [
                    ['NLP and Reinforcement Learning', false],
                    ['Computer Vision and Decision-Making AI', true],
                    ['Generative AI and Database Management', false],
                    ['Supervised Learning and Spreadsheet Analysis', false],
                ],
            ],

        ];

        foreach ($qaData as $data) {
            $question = ChallengeQuestion::create([
                'challenge_id'          => $challenge->id,
                'question_text'         => $data['q'],
                'challenge_category_id' => $category->id,
            ]);

            foreach ($data['opts'] as $opt) {
                ChallengeOption::create([
                    'challenge_question_id' => $question->id,
                    'option_text'           => $opt[0],
                    'is_correct'            => $opt[1],
                ]);
            }
        }

        $this->command->info("✅ Done! 30 questions seeded for Module 19 — Introduction to Artificial Intelligence (Newbie).");
    }
}