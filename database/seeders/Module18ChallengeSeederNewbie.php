<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module18ChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (!$category) {
            $this->command->error("Newbie category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 18 — Privacy, Ethics & Data Governance (Newbie)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Privacy, Ethics & Data Governance',
            'description'           => 'Test your very first knowledge of data ethics, privacy, and governance — what they mean, why they matter, and the key vocabulary every data professional should know. No prior experience assumed!',
            'time_limit_seconds'    => 900,
            'base_xp'               => 500,
            'order_index'           => 18,
        ]);

        $this->command->info("Seeding 50 newbie-friendly ethics & privacy questions...");

        $qaData = [

            // ── WHY ETHICS & PRIVACY MATTER ───────────────────────────────
            [
                'q' => 'Why does ethics matter in data science?',
                'opts' => [
                    ['It makes code run faster', false],
                    ['It ensures data is used responsibly and does not harm people', true],
                    ['It is only required for government projects', false],
                    ['It only matters when working with numbers', false],
                ],
            ],
            [
                'q' => 'What does "data privacy" mean?',
                'opts' => [
                    ['Keeping your computer password secret', false],
                    ['The right of individuals to control how their personal information is collected and used', true],
                    ['Encrypting only financial data', false],
                    ['Deleting all data after one use', false],
                ],
            ],
            [
                'q' => 'Which of the following is an example of a data ethics violation?',
                'opts' => [
                    ['Storing data in a database', false],
                    ['Sharing a customer\'s personal medical records without their permission', true],
                    ['Using a spreadsheet to analyze sales', false],
                    ['Visualizing public weather data', false],
                ],
            ],
            [
                'q' => 'A data scientist\'s work affects real people. This is why they must consider:',
                'opts' => [
                    ['Only the speed of their code', false],
                    ['The potential harm or benefit their work may cause to individuals and society', true],
                    ['Only the accuracy of their models', false],
                    ['Only the opinions of their manager', false],
                ],
            ],
            [
                'q' => 'Which word describes treating all people fairly and without discrimination in data-driven decisions?',
                'opts' => [
                    ['Speed', false],
                    ['Fairness', true],
                    ['Compression', false],
                    ['Scalability', false],
                ],
            ],
            [
                'q' => '"Transparency" in data science means:',
                'opts' => [
                    ['Making data invisible to users', false],
                    ['Being open about how data is collected, used, and how decisions are made', true],
                    ['Only showing data to managers', false],
                    ['Using clear fonts in reports', false],
                ],
            ],
            [
                'q' => '"Accountability" in data governance means:',
                'opts' => [
                    ['Counting rows in a dataset', false],
                    ['Someone is responsible for ensuring data is handled correctly and ethically', true],
                    ['Giving everyone access to all data', false],
                    ['Automatically approving data requests', false],
                ],
            ],
            [
                'q' => 'A company builds a hiring algorithm that rejects more women than men for no valid reason. This is an example of:',
                'opts' => [
                    ['Good model performance', false],
                    ['Algorithmic bias', true],
                    ['Data compression', false],
                    ['Feature engineering', false],
                ],
            ],

            // ── PERSONAL DATA ─────────────────────────────────────────────
            [
                'q' => '"Personal data" refers to:',
                'opts' => [
                    ['Any number stored in a database', false],
                    ['Any information that can identify a specific individual', true],
                    ['Only a person\'s name', false],
                    ['Company financial records', false],
                ],
            ],
            [
                'q' => 'Which of the following is personal data?',
                'opts' => [
                    ['The average temperature in Manila in July', false],
                    ['A person\'s home address', true],
                    ['The number of cars sold last quarter', false],
                    ['A country\'s GDP', false],
                ],
            ],
            [
                'q' => 'Which of the following is an example of SENSITIVE personal data?',
                'opts' => [
                    ['A person\'s favorite color', false],
                    ['A person\'s health condition or medical records', true],
                    ['A public company\'s stock price', false],
                    ['A product\'s average rating', false],
                ],
            ],
            [
                'q' => '"Anonymization" of data means:',
                'opts' => [
                    ['Making data available only at night', false],
                    ['Removing or altering information so that individuals can no longer be identified', true],
                    ['Encrypting data with a password', false],
                    ['Deleting the entire dataset', false],
                ],
            ],
            [
                'q' => 'Which of the following is NOT considered personal data?',
                'opts' => [
                    ['A person\'s phone number', false],
                    ['A person\'s email address', false],
                    ['The total population of a city', true],
                    ['A person\'s biometric fingerprint', false],
                ],
            ],
            [
                'q' => '"Data minimization" means collecting:',
                'opts' => [
                    ['As much data as possible for future use', false],
                    ['Only the data that is necessary for a specific purpose', true],
                    ['Data only from large databases', false],
                    ['Minimal text data', false],
                ],
            ],
            [
                'q' => 'A website asks for your birth year, name, and credit card number just to show you the weather forecast. This violates the principle of:',
                'opts' => [
                    ['Data accuracy', false],
                    ['Data minimization', true],
                    ['Data compression', false],
                    ['Transparency', false],
                ],
            ],
            [
                'q' => '"Pseudonymization" means:',
                'opts' => [
                    ['Deleting all personal data permanently', false],
                    ['Replacing identifying fields with fake names or codes, but the data can still be re-linked with a key', true],
                    ['Encrypting data with a public key', false],
                    ['Sharing data anonymously online', false],
                ],
            ],

            // ── CONSENT & LEGAL BASIS ─────────────────────────────────────
            [
                'q' => '"Consent" in data privacy means:',
                'opts' => [
                    ['A company can use data however it wants', false],
                    ['An individual freely and clearly agrees to their data being collected and used for a specific purpose', true],
                    ['A user cannot say no to data collection', false],
                    ['Data is shared between two companies', false],
                ],
            ],
            [
                'q' => 'GDPR stands for:',
                'opts' => [
                    ['General Data Protection Regulation', true],
                    ['Global Data Privacy Rules', false],
                    ['Government Data Processing Requirements', false],
                    ['General Digital Privacy Reform', false],
                ],
            ],
            [
                'q' => 'The GDPR primarily protects citizens of:',
                'opts' => [
                    ['The United States', false],
                    ['The European Union', true],
                    ['The Philippines', false],
                    ['China', false],
                ],
            ],
            [
                'q' => 'Under GDPR, individuals have the "right to be forgotten." This means:',
                'opts' => [
                    ['Companies must forget their own passwords', false],
                    ['Individuals can request that a company delete their personal data', true],
                    ['Data is automatically deleted after 1 year', false],
                    ['Employees can erase their work records', false],
                ],
            ],
            [
                'q' => 'Which of the following is a valid "legal basis" for processing personal data under GDPR?',
                'opts' => [
                    ['The data is interesting to the company', false],
                    ['The individual gave their consent', true],
                    ['The company is profitable', false],
                    ['The data was found online', false],
                ],
            ],
            [
                'q' => 'A pre-ticked checkbox on a website that signs you up for marketing emails WITHOUT your active choice violates which principle?',
                'opts' => [
                    ['Data accuracy', false],
                    ['Free and informed consent', true],
                    ['Data minimization', false],
                    ['Accountability', false],
                ],
            ],
            [
                'q' => 'CCPA is a privacy law that protects residents of:',
                'opts' => [
                    ['Canada', false],
                    ['California, USA', true],
                    ['The United Kingdom', false],
                    ['Australia', false],
                ],
            ],
            [
                'q' => 'The Philippines has its own data privacy law. It is called:',
                'opts' => [
                    ['The GDPR Extension Act', false],
                    ['The Data Privacy Act of 2012', true],
                    ['The Philippine Cybersecurity Act', false],
                    ['The National Data Law', false],
                ],
            ],

            // ── ALGORITHMIC BIAS ──────────────────────────────────────────
            [
                'q' => 'Algorithmic bias occurs when:',
                'opts' => [
                    ['An algorithm runs slower than expected', false],
                    ['An algorithm produces systematically unfair outcomes for certain groups of people', true],
                    ['An algorithm uses too much memory', false],
                    ['An algorithm is trained on too much data', false],
                ],
            ],
            [
                'q' => 'Which of the following is a common SOURCE of algorithmic bias?',
                'opts' => [
                    ['Using Python instead of R', false],
                    ['Training data that reflects historical discrimination or stereotypes', true],
                    ['Having too many features in the model', false],
                    ['Using a linear regression model', false],
                ],
            ],
            [
                'q' => 'A facial recognition system works well for light-skinned people but poorly for dark-skinned people. This is an example of:',
                'opts' => [
                    ['Good model accuracy', false],
                    ['Representation bias (unequal representation in training data)', true],
                    ['Overfitting', false],
                    ['Data leakage', false],
                ],
            ],
            [
                'q' => 'To detect bias in a machine learning model, you should:',
                'opts' => [
                    ['Only look at overall accuracy', false],
                    ['Evaluate model performance separately across different demographic groups', true],
                    ['Ignore the training data source', false],
                    ['Train the model on more data from the majority group', false],
                ],
            ],
            [
                'q' => '"Protected attributes" in fairness are characteristics like:',
                'opts' => [
                    ['Model accuracy and F1 score', false],
                    ['Race, gender, age, religion, and disability status', true],
                    ['Column names in a dataset', false],
                    ['Training set size', false],
                ],
            ],

            // ── FAIRNESS FRAMEWORKS ───────────────────────────────────────
            [
                'q' => '"Demographic parity" (equal representation) in a fair model means:',
                'opts' => [
                    ['All demographic groups receive the same positive outcome rate', true],
                    ['All demographic groups have the same population size', false],
                    ['The model uses demographic data as features', false],
                    ['Only one demographic group is modeled', false],
                ],
            ],
            [
                'q' => 'Which of the following is a strategy to reduce algorithmic bias?',
                'opts' => [
                    ['Ignoring demographic information completely', false],
                    ['Auditing model outcomes across demographic groups and rebalancing training data', true],
                    ['Increasing model complexity', false],
                    ['Using only numerical features', false],
                ],
            ],
            [
                'q' => '"Equal opportunity" as a fairness metric focuses on:',
                'opts' => [
                    ['Equal number of features for each group', false],
                    ['Equal true positive rates across groups — qualified candidates are equally likely to be selected', true],
                    ['Equal dataset sizes', false],
                    ['Equal model training time per group', false],
                ],
            ],

            // ── PRIVACY-ENHANCING TECHNOLOGIES ───────────────────────────
            [
                'q' => '"Differential privacy" is a technique that:',
                'opts' => [
                    ['Adds random noise to data outputs to protect individual privacy while preserving overall patterns', true],
                    ['Deletes different types of data', false],
                    ['Compares two datasets for differences', false],
                    ['Encrypts data using two different keys', false],
                ],
            ],
            [
                'q' => '"Federated learning" protects privacy by:',
                'opts' => [
                    ['Sending all user data to a central server', false],
                    ['Training models locally on each device and only sharing model updates, not raw data', true],
                    ['Deleting data after training', false],
                    ['Using only public datasets', false],
                ],
            ],
            [
                'q' => '"Encryption" of data means:',
                'opts' => [
                    ['Making data unreadable to anyone without the correct key', true],
                    ['Compressing data to save storage space', false],
                    ['Deleting sensitive columns from a table', false],
                    ['Backing up data to the cloud', false],
                ],
            ],
            [
                'q' => 'A "data breach" is:',
                'opts' => [
                    ['Intentionally publishing public data', false],
                    ['Unauthorized access to or release of private data', true],
                    ['Backing up data to a new server', false],
                    ['Updating a privacy policy document', false],
                ],
            ],
            [
                'q' => '"K-anonymity" ensures that each individual in a dataset:',
                'opts' => [
                    ['Has a unique identifier', false],
                    ['Is indistinguishable from at least k−1 other individuals based on identifying attributes', true],
                    ['Has their data encrypted k times', false],
                    ['Consented k times before data was collected', false],
                ],
            ],

            // ── DATA GOVERNANCE ───────────────────────────────────────────
            [
                'q' => '"Data governance" refers to:',
                'opts' => [
                    ['The government collecting citizen data', false],
                    ['The policies, processes, and standards for managing data responsibly across an organization', true],
                    ['A specific type of database software', false],
                    ['Storing data on government servers', false],
                ],
            ],
            [
                'q' => 'A "data steward" in an organization is responsible for:',
                'opts' => [
                    ['Writing all the code in the organization', false],
                    ['Ensuring data quality, accessibility, and proper use within their domain', true],
                    ['Only deleting old data', false],
                    ['Managing the CEO\'s calendar', false],
                ],
            ],
            [
                'q' => '"Data lineage" means tracking:',
                'opts' => [
                    ['A dataset\'s file size over time', false],
                    ['Where data came from, how it was transformed, and how it flows through a system', true],
                    ['The age of the data scientists in a team', false],
                    ['The number of columns in a dataset', false],
                ],
            ],
            [
                'q' => 'A "data catalog" is used to:',
                'opts' => [
                    ['List all the employees in a data team', false],
                    ['Document and organize metadata about an organization\'s data assets so they can be found and understood', true],
                    ['Store backup copies of all databases', false],
                    ['Monitor server uptime', false],
                ],
            ],
            [
                'q' => '"Access control" in data governance means:',
                'opts' => [
                    ['Controlling how fast data is accessed', false],
                    ['Defining who is allowed to view, modify, or use specific data', true],
                    ['Giving everyone in the company equal access to all data', false],
                    ['Controlling the internet connection speed', false],
                ],
            ],

            // ── RESPONSIBLE AI ────────────────────────────────────────────
            [
                'q' => '"Explainability" in AI means:',
                'opts' => [
                    ['The AI can speak and explain itself verbally', false],
                    ['Being able to understand and communicate why an AI model made a specific decision', true],
                    ['The AI model documentation is written in simple English', false],
                    ['The AI only makes binary decisions', false],
                ],
            ],
            [
                'q' => 'A "black box" model is one where:',
                'opts' => [
                    ['The model only works in dark mode', false],
                    ['The internal decision-making process is hidden and difficult to interpret', true],
                    ['The model is stored in a black database', false],
                    ['Only one person has access to the model', false],
                ],
            ],
            [
                'q' => 'SHAP and LIME are tools used to help with AI:',
                'opts' => [
                    ['Speed and performance', false],
                    ['Explainability — understanding which features influenced a model\'s prediction', true],
                    ['Data cleaning and preprocessing', false],
                    ['Model deployment and scaling', false],
                ],
            ],
            [
                'q' => '"Responsible AI" includes which of the following principles?',
                'opts' => [
                    ['Maximizing profit above all else', false],
                    ['Fairness, transparency, accountability, and privacy', true],
                    ['Using only the largest models available', false],
                    ['Sharing all model weights publicly', false],
                ],
            ],
            [
                'q' => 'An AI model used in criminal sentencing should be:',
                'opts' => [
                    ['Completely automated with no human review', false],
                    ['Transparent, explainable, and subject to human oversight and appeal', true],
                    ['Only accessible to the judge', false],
                    ['Trained on data from only one country', false],
                ],
            ],

            // ── ETHICAL DATA SCIENCE PRACTICE ─────────────────────────────
            [
                'q' => 'Before collecting data from users, a responsible data scientist should first:',
                'opts' => [
                    ['Build the model and collect data later', false],
                    ['Consider what data is truly needed, how it will be used, and obtain proper consent', true],
                    ['Collect as much data as possible to be safe', false],
                    ['Share the data with partners immediately', false],
                ],
            ],
            [
                'q' => 'When sharing findings from a data analysis, an ethical data scientist should:',
                'opts' => [
                    ['Only report results that support the business goal', false],
                    ['Report results honestly, including limitations and potential biases', true],
                    ['Remove all visualizations to keep it simple', false],
                    ['Only share results with senior management', false],
                ],
            ],
            [
                'q' => 'A data scientist discovers that a model they deployed is discriminating against elderly users. The ethical action is:',
                'opts' => [
                    ['Ignore it as long as overall accuracy is high', false],
                    ['Immediately report it and work to fix or remove the model', true],
                    ['Reduce the model\'s confidence threshold', false],
                    ['Delete the elderly users from the dataset', false],
                ],
            ],
            [
                'q' => '"Privacy by design" means:',
                'opts' => [
                    ['Building a privacy policy document after the product launches', false],
                    ['Building privacy protections into the system from the very beginning, not as an afterthought', true],
                    ['Using a private cloud server', false],
                    ['Designing a website that only registered users can visit', false],
                ],
            ],
            [
                'q' => 'Which action best demonstrates an ethical data science practice?',
                'opts' => [
                    ['Using a model in production without testing for bias', false],
                    ['Documenting data sources, model assumptions, limitations, and fairness audits', true],
                    ['Keeping all model details secret from stakeholders', false],
                    ['Collecting user data without informing them', false],
                ],
            ],
            [
                'q' => 'When in doubt about whether using certain data is ethical, a data scientist should:',
                'opts' => [
                    ['Use the data anyway since it is already collected', false],
                    ['Consult with ethics guidelines, legal teams, or a data ethics board before proceeding', true],
                    ['Delete all data and start over', false],
                    ['Ask only their direct manager and proceed immediately', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 18 — Privacy, Ethics & Data Governance (Newbie).");
    }
}