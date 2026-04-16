<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;

class Module17ChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (!$category) {
            $this->command->error("Newbie category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 17 — Introduction to Deep Learning (Newbie)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introduction to Deep Learning',
            'description'           => 'Test your knowledge of the very basics of deep learning — what neural networks are, how they learn, and the key vocabulary used across the field. No math or coding experience required!',
            'time_limit_seconds'    => 900,
            'base_xp'               => 500,
            'order_index'           => 17,
        ]);

        $this->command->info("Seeding 30 newbie-friendly questions...");

        $qaData = [

            // ── WHAT IS DEEP LEARNING ─────────────────────────────────────
            [
                'q' => 'What is deep learning?',
                'opts' => [
                    ['A way to teach computers using many layers of artificial neurons', true],
                    ['A method of memorizing large amounts of data manually', false],
                    ['A technique for searching the internet faster', false],
                    ['A type of database management system', false],
                ],
            ],
            [
                'q' => 'Deep learning is a subset of which broader field?',
                'opts' => [
                    ['Database management', false],
                    ['Machine learning', true],
                    ['Operating systems', false],
                    ['Web development', false],
                ],
            ],
            [
                'q' => 'What does "deep" refer to in deep learning?',
                'opts' => [
                    ['The depth of the dataset used', false],
                    ['The many layers in a neural network', true],
                    ['How difficult the problem is to solve', false],
                    ['The speed of the computer running the model', false],
                ],
            ],
            [
                'q' => 'Which of the following is a common real-world use of deep learning?',
                'opts' => [
                    ['Sorting files alphabetically', false],
                    ['Recognizing faces in photos', true],
                    ['Calculating simple averages', false],
                    ['Creating spreadsheets', false],
                ],
            ],
            [
                'q' => 'A neural network is loosely inspired by which biological structure?',
                'opts' => [
                    ['The human heart', false],
                    ['Plant cells', false],
                    ['The human brain', true],
                    ['The digestive system', false],
                ],
            ],
            [
                'q' => 'Which of these is NOT a common application of deep learning?',
                'opts' => [
                    ['Speech recognition', false],
                    ['Image classification', false],
                    ['Sorting a list of names alphabetically', true],
                    ['Machine translation', false],
                ],
            ],

            // ── THE NEURON & FEEDFORWARD NETWORKS ─────────────────────────
            [
                'q' => 'What is the basic building block of a neural network?',
                'opts' => [
                    ['A pixel', false],
                    ['A neuron (also called a node)', true],
                    ['A database table', false],
                    ['A decision tree', false],
                ],
            ],
            [
                'q' => 'In a neural network, what does a neuron do?',
                'opts' => [
                    ['It stores files on a hard drive', false],
                    ['It receives inputs, processes them, and passes an output forward', true],
                    ['It deletes incorrect data automatically', false],
                    ['It connects to the internet to find answers', false],
                ],
            ],
            [
                'q' => 'What is a "layer" in a neural network?',
                'opts' => [
                    ['A single training example', false],
                    ['A group of neurons at the same level that process information together', true],
                    ['A type of activation function', false],
                    ['The output of the entire model', false],
                ],
            ],
            [
                'q' => 'Which layer receives the raw data (like an image or text) in a neural network?',
                'opts' => [
                    ['Output layer', false],
                    ['Hidden layer', false],
                    ['Input layer', true],
                    ['Loss layer', false],
                ],
            ],
            [
                'q' => 'Which layer produces the final prediction of a neural network?',
                'opts' => [
                    ['Input layer', false],
                    ['Output layer', true],
                    ['Hidden layer', false],
                    ['Dropout layer', false],
                ],
            ],
            [
                'q' => 'A "feedforward" network means that information flows in which direction?',
                'opts' => [
                    ['Backwards from output to input', false],
                    ['In circles between layers', false],
                    ['Forward from input to output, one direction only', true],
                    ['Randomly across all layers at once', false],
                ],
            ],

            // ── BACKPROPAGATION & GRADIENT DESCENT ────────────────────────
            [
                'q' => 'What is the purpose of training a neural network?',
                'opts' => [
                    ['To make the network as large as possible', false],
                    ['To adjust the network\'s weights so it makes better predictions', true],
                    ['To delete unnecessary neurons', false],
                    ['To speed up the computer it runs on', false],
                ],
            ],
            [
                'q' => 'What does a "loss" (or "error") measure in deep learning?',
                'opts' => [
                    ['How many neurons are in the network', false],
                    ['How wrong the model\'s prediction is compared to the correct answer', true],
                    ['How fast the model runs', false],
                    ['The number of layers in the network', false],
                ],
            ],
            [
                'q' => 'What is "backpropagation" in simple terms?',
                'opts' => [
                    ['Feeding data into the network from back to front', false],
                    ['The process of calculating how much each weight contributed to the error', true],
                    ['Removing neurons from the last layer', false],
                    ['Running the model on test data', false],
                ],
            ],
            [
                'q' => 'What does "gradient descent" help a neural network do?',
                'opts' => [
                    ['Download data faster', false],
                    ['Gradually reduce the loss by adjusting the weights', true],
                    ['Add more layers to the network', false],
                    ['Visualize the training data', false],
                ],
            ],

            // ── WEIGHTS & ACTIVATIONS ─────────────────────────────────────
            [
                'q' => 'What are "weights" in a neural network?',
                'opts' => [
                    ['The physical size of the server running the model', false],
                    ['Numbers that determine how strongly inputs influence the output of a neuron', true],
                    ['The labels attached to training data', false],
                    ['The total number of neurons in the network', false],
                ],
            ],
            [
                'q' => 'What is an "activation function" used for in a neuron?',
                'opts' => [
                    ['To start the training process', false],
                    ['To decide whether and how strongly a neuron should "fire" (pass output forward)', true],
                    ['To delete unused neurons', false],
                    ['To connect the network to the internet', false],
                ],
            ],
            [
                'q' => 'Which of the following is one of the most commonly used activation functions in deep learning?',
                'opts' => [
                    ['SORT', false],
                    ['ReLU (Rectified Linear Unit)', true],
                    ['AVERAGE', false],
                    ['CONCAT', false],
                ],
            ],
            [
                'q' => 'What does ReLU output when given a negative number?',
                'opts' => [
                    ['The number itself (negative)', false],
                    ['1', false],
                    ['0', true],
                    ['-1', false],
                ],
            ],

            // ── REGULARIZATION & OVERFITTING ──────────────────────────────
            [
                'q' => 'What does "overfitting" mean in deep learning?',
                'opts' => [
                    ['The model trains too slowly', false],
                    ['The model learns the training data too well and performs poorly on new data', true],
                    ['The model has too few neurons', false],
                    ['The model runs out of memory', false],
                ],
            ],
            [
                'q' => 'What is "regularization" used for in deep learning?',
                'opts' => [
                    ['To make the training data larger', false],
                    ['To help prevent the model from overfitting', true],
                    ['To speed up training', false],
                    ['To add more layers to the network', false],
                ],
            ],
            [
                'q' => '"Dropout" is a regularization technique that works by:',
                'opts' => [
                    ['Removing entire layers from the network permanently', false],
                    ['Randomly turning off some neurons during training so the network doesn\'t rely on any single neuron', true],
                    ['Deleting incorrectly labelled data', false],
                    ['Lowering the learning rate automatically', false],
                ],
            ],

            // ── CNNs, RNNs, TRANSFORMERS (BASIC) ─────────────────────────
            [
                'q' => 'What type of data are Convolutional Neural Networks (CNNs) best known for processing?',
                'opts' => [
                    ['Audio recordings only', false],
                    ['Images and visual data', true],
                    ['Spreadsheets of numbers', false],
                    ['Plain text documents only', false],
                ],
            ],
            [
                'q' => 'Recurrent Neural Networks (RNNs) are especially useful for:',
                'opts' => [
                    ['Processing images', false],
                    ['Sequential data like text and time series, because they have memory of previous inputs', true],
                    ['Sorting databases', false],
                    ['Compressing files', false],
                ],
            ],
            [
                'q' => 'What is a "Transformer" in deep learning most famous for?',
                'opts' => [
                    ['Transforming images into videos', false],
                    ['Powering modern language models like ChatGPT using a mechanism called attention', true],
                    ['Converting file formats between different types', false],
                    ['Speeding up training on CPUs', false],
                ],
            ],
            [
                'q' => 'What does "transfer learning" mean?',
                'opts' => [
                    ['Moving a model from one computer to another', false],
                    ['Using a model already trained on one task as a starting point for a different task', true],
                    ['Transferring data between two datasets', false],
                    ['Training two models at the same time', false],
                ],
            ],

            // ── GENERATIVE MODELS & GENERAL ───────────────────────────────
            [
                'q' => 'What does a Generative Adversarial Network (GAN) do?',
                'opts' => [
                    ['Finds errors in existing images', false],
                    ['Generates new, realistic-looking data (like images) by having two networks compete', true],
                    ['Classifies images into categories', false],
                    ['Speeds up training of other models', false],
                ],
            ],
            [
                'q' => 'In a GAN, the "generator" and "discriminator" have which relationship?',
                'opts' => [
                    ['They work together on the same task with shared weights', false],
                    ['The generator creates fake data; the discriminator tries to tell fake from real', true],
                    ['The discriminator generates data; the generator checks quality', false],
                    ['They are identical networks running in parallel', false],
                ],
            ],
            [
                'q' => 'Which hardware is most commonly used to speed up deep learning training?',
                'opts' => [
                    ['CPU (Central Processing Unit)', false],
                    ['GPU (Graphics Processing Unit)', true],
                    ['Hard disk drive (HDD)', false],
                    ['RAM (Random Access Memory)', false],
                ],
            ],
            [
                'q' => 'What is a "learning rate" in deep learning?',
                'opts' => [
                    ['How many images the model sees per second', false],
                    ['A number that controls how big a step the model takes when updating weights', true],
                    ['The speed of the GPU during training', false],
                    ['The number of neurons added per training round', false],
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

        $this->command->info("✅ Done! 30 questions seeded for Module 17 — Introduction to Deep Learning (Newbie).");
    }
}