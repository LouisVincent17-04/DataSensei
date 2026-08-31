<?php

namespace Database\Seeders;

use App\Models\AssessmentQuestionIlo;
use App\Models\AssignmentLibraryItem;
use App\Models\AssignmentSubmission;
use App\Models\AssignmentSubmissionAnswer;
use App\Models\Challenge;
use App\Models\ChallengeCategory;
use App\Models\ClassAssignment;
use App\Models\ClassRoom;
use App\Models\Institution;
use App\Models\IntendedLearningOutcome;
use App\Models\Module;
use App\Models\User;
use App\Services\CompetencyMonitoringService;
use App\Services\IloMasteryService;
use App\Services\StudentPerformanceClusteringService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;

class DefenseDemoSeeder extends Seeder
{
    /**
     * A deliberately safe, deterministic defense dataset.
     *
     * It refuses production and refuses any database that already contains
     * learner/account history. This prevents a convenient demo command from
     * becoming an accidental data-destruction command.
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('DefenseDemoSeeder is disabled in production.');
        }

        $protectedTables = [
            'institutions',
            'users',
            'classes',
            'class_student',
            'assignment_library_items',
            'class_assignments',
            'assignment_submissions',
            'assessment_submissions',
            'challenge_attempts',
            'coding_submissions',
            'training_jobs',
            'user_datasets',
        ];

        foreach ($protectedTables as $table) {
            if (Schema::hasTable($table) && DB::table($table)->exists()) {
                throw new RuntimeException(
                    "DefenseDemoSeeder stopped because {$table} already contains data. Use a separate empty demo database."
                );
            }
        }

        // Reuse safe platform seeders. CurriculumSeeder truncates module data,
        // so call it only when that reference area is genuinely empty.
        $this->call(DatabaseSeeder::class);
        $this->call(IntendedLearningOutcomeSeeder::class);

        if (Schema::hasTable('modules') && DB::table('modules')->count() === 0) {
            $this->call(CurriculumSeeder::class);
            $this->call([
                Module1LessonsSeeder::class,
                Module2LessonsSeeder::class,
                Module3LessonsSeeder::class,
                Module14LessonsSeeder::class,
                Module15LessonsSeeder::class,
            ]);
        }

        $demo = DB::transaction(function (): array {
            $institution = Institution::create([
                'name' => 'DataSensei Demo University',
                'slug' => 'datasensei-demo-university',
                'email' => 'demo-university@datasensei.test',
                'address' => 'Defense Demo Campus',
                'status' => 'active',
                'notes' => 'Non-production defense data created by DefenseDemoSeeder.',
                'institution_code' => 'DEMO26',
            ]);

            $password = Hash::make('Demo!2026Secure');
            User::create([
                'name' => 'Demo Superadmin',
                'email' => 'superadmin@datasensei.test',
                'password' => $password,
                'role' => User::ROLE_SUPERADMIN,
                'status' => 'active',
            ]);
            User::create([
                'name' => 'Demo Admin',
                'email' => 'admin@datasensei.test',
                'password' => $password,
                'role' => User::ROLE_ADMIN,
                'status' => 'active',
            ]);
            $instructor = User::create([
                'name' => 'Prof. Demo Instructor',
                'email' => 'instructor@datasensei.test',
                'password' => $password,
                'role' => User::ROLE_INSTRUCTOR,
                'status' => 'active',
                'institution_id' => $institution->id,
            ]);
            $learner = User::create([
                'name' => 'Demo Learner',
                'email' => 'learner@datasensei.test',
                'password' => $password,
                'role' => User::ROLE_USER,
                'status' => 'active',
                'institution_id' => $institution->id,
            ]);
            $sampleLearner = User::create([
                'name' => 'Sample Learner',
                'email' => 'sample@datasensei.test',
                'password' => $password,
                'role' => User::ROLE_USER,
                'status' => 'active',
                'institution_id' => $institution->id,
            ]);

            $class = ClassRoom::create([
                'instructor_id' => $instructor->id,
                'institution_id' => $institution->id,
                'name' => 'Data Science Defense Class',
                'section' => 'DEMO-A',
                'term' => 'Defense Demo',
                'academic_year' => '2026',
                'description' => 'Small deterministic class for end-to-end defense demonstrations.',
                'class_code' => 'DEMO2026',
                'subject_code' => 'DS-DEMO',
                'max_students' => 40,
                'is_archived' => false,
                'allow_self_enroll' => false,
            ]);
            $class->students()->attach([$learner->id, $sampleLearner->id], ['enrolled_at' => now()]);

            $module = Module::query()->where('order_index', 1)->first();
            if ($module) {
                foreach ([$learner, $sampleLearner] as $student) {
                    $student->modules()->syncWithoutDetaching([
                        $module->id => ['is_unlocked' => true, 'is_completed' => false],
                    ]);
                }
            }

            $ilos = IntendedLearningOutcome::active()
                ->where('module_no', 1)
                ->orderBy('sort_order')
                ->take(3)
                ->get();
            if ($ilos->count() < 3) {
                throw new RuntimeException('Module 1 ILOs are missing from the demo database.');
            }

            $libraryItem = AssignmentLibraryItem::create([
                'module_no' => 1,
                'assignment_code' => 'DEMO-M01-TIMED',
                'title' => 'Python Foundations — Timed Demo',
                'topic_title' => 'Python Foundations',
                'year_level' => 'Year 1',
                'assignment_type' => 'mcq',
                'version_no' => 1,
                'version_name' => 'Defense Version',
                'version_code' => 'V1',
                'description' => 'Three specific questions for demonstrating timed assessment, grading, and ILO evidence.',
                'instructions' => 'Answer all three items before the five-minute server timer expires.',
                'time_limit_minutes' => 5,
                'total_points' => 3,
                'sort_order' => 1,
                'is_active' => true,
            ]);

            $questionDefinitions = [
                [
                    'text' => 'Which Python data type stores key-value pairs?',
                    'options' => ['Dictionary' => true, 'List' => false, 'Tuple' => false, 'Set' => false],
                ],
                [
                    'text' => 'What does len([10, 20, 30]) return?',
                    'options' => ['2' => false, '3' => true, '30' => false, '60' => false],
                ],
                [
                    'text' => 'Why should a data-science script validate input before analysis?',
                    'options' => [
                        'To make the file larger' => false,
                        'To hide errors from the user' => false,
                        'To catch invalid or missing values before they distort results' => true,
                        'To guarantee every model is accurate' => false,
                    ],
                ],
            ];

            $questions = collect();
            foreach ($questionDefinitions as $index => $definition) {
                $question = $libraryItem->questions()->create([
                    'question_type' => 'mcq',
                    'question_text' => $definition['text'],
                    'points' => 1,
                    'order_index' => $index + 1,
                    'explanation' => 'Defense demo item with explicit evidence mapping.',
                ]);
                foreach ($definition['options'] as $text => $correct) {
                    $question->options()->create([
                        'option_text' => $text,
                        'is_correct' => $correct,
                        'order_index' => $question->options()->count() + 1,
                    ]);
                }
                AssessmentQuestionIlo::create([
                    'ilo_id' => $ilos[$index]->id,
                    'assessment_source' => 'assignment',
                    'question_id' => $question->id,
                    'weight' => 1,
                ]);
                $questions->push($question->fresh('options'));
            }

            $classAssignment = ClassAssignment::create([
                'class_id' => $class->id,
                'assignment_library_item_id' => $libraryItem->id,
                'assigned_by' => $instructor->id,
                'title' => 'Python Foundations — Timed Demo',
                'instructions' => 'Use this to demonstrate server-side timing and evidence mapping.',
                'available_at' => now()->subMinute(),
                'due_at' => now()->addDays(7),
                'max_attempts' => 2,
                'status' => 'published',
                'assigned_at' => now(),
            ]);

            $sampleSubmission = AssignmentSubmission::create([
                'class_assignment_id' => $classAssignment->id,
                'student_id' => $sampleLearner->id,
                'attempt_no' => 1,
                'status' => 'graded',
                'score' => 2,
                'total_points' => 3,
                'started_at' => now()->subMinutes(4),
                'submitted_at' => now()->subMinutes(2),
                'graded_at' => now()->subMinutes(2),
                'anti_cheat_session_id' => Str::random(64),
            ]);

            foreach ($questions as $index => $question) {
                $correct = $question->options->firstWhere('is_correct', true);
                $selected = $index < 2
                    ? $correct
                    : $question->options->first(fn ($option) => ! $option->is_correct);
                AssignmentSubmissionAnswer::create([
                    'assignment_submission_id' => $sampleSubmission->id,
                    'assignment_question_id' => $question->id,
                    'selected_option_id' => $selected?->id,
                    'answer_text' => null,
                    'is_correct' => (bool) ($selected?->is_correct),
                    'points_awarded' => $selected?->is_correct ? 1 : 0,
                ]);
            }

            $this->createCuratedChallenge();

            return compact('class', 'sampleSubmission');
        }, 3);

        app(IloMasteryService::class)->refreshForAssignmentSubmission($demo['sampleSubmission']->fresh());
        app(StudentPerformanceClusteringService::class)->refreshForClass($demo['class']->fresh());
        app(CompetencyMonitoringService::class)->refreshClass($demo['class']->fresh());

        $this->command?->info('Defense demo ready. Password for all demo accounts: Demo!2026Secure');
    }

    private function createCuratedChallenge(): void
    {
        $category = ChallengeCategory::query()->where('slug', 'university-student')->firstOrFail();
        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'content_code' => 'DEMO-M01-MCQ-V1',
            'title' => 'Python Reasoning — Defense Demo',
            'description' => 'Five concise Python questions selected for a clean defense demonstration.',
            'time_limit_seconds' => 300,
            'base_xp' => 100,
            'order_index' => 1,
            'is_coding_challenge' => false,
            'version_no' => 1,
            'version_name' => 'Defense Version',
            'version_code' => 'V1',
            'is_active' => true,
        ]);

        $items = [
            ['What is the value of 2 ** 3 in Python?', ['6' => false, '8' => true, '9' => false, '23' => false]],
            ['Which expression safely gets key "score" from dictionary d without raising KeyError when it is missing?', ['d["score"]' => false, 'd.get("score")' => true, 'd.score()' => false, 'get(d, "score")' => false]],
            ['What does range(3) produce for a loop?', ['1, 2, 3' => false, '0, 1, 2' => true, '0, 1, 2, 3' => false, '3 only' => false]],
            ['Why use a function for repeated data-cleaning logic?', ['To avoid testing' => false, 'To reuse one clear, testable operation' => true, 'To make variables global' => false, 'To remove all errors automatically' => false]],
            ['A numeric column contains a text value by mistake. What should happen before computing its mean?', ['Ignore the problem silently' => false, 'Validate or clean the invalid value' => true, 'Duplicate the row' => false, 'Round every value' => false]],
        ];

        foreach ($items as $questionIndex => [$text, $options]) {
            $question = $challenge->questions()->create([
                'challenge_category_id' => $category->id,
                'question_text' => $text,
                'order_index' => $questionIndex + 1,
            ]);
            $optionIndex = 1;
            foreach ($options as $optionText => $correct) {
                $question->options()->create([
                    'option_text' => $optionText,
                    'is_correct' => $correct,
                    'order_index' => $optionIndex++,
                ]);
            }
        }
    }
}
