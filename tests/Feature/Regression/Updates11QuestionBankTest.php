<?php

namespace Tests\Feature\Regression;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentSubmission;
use App\Models\QuestionBankItem;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Regression\Concerns\BuildsClassAssessmentWorkflow;
use Tests\TestCase;

/**
 * DataSensei Updates 11: the instructor Question Bank.
 *
 * Instructors keep reusable questions (their own plus a read-only shared
 * pool), copy them into draft assessments, and save builder questions back.
 * Every copy is a snapshot: later bank edits or archiving never change an
 * assessment, an attempt or a grade. A TOS-planned assessment only takes
 * bank questions that really fit a missing planned item, so the plan is
 * never reported as covered when it is not. Students never reach the bank.
 */
class Updates11QuestionBankTest extends TestCase
{
    use BuildsClassAssessmentWorkflow;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([ValidateCsrfToken::class]);
        $this->seedAssessmentActors();
    }

    // ── Bank management ────────────────────────────────────────────────

    public function test_an_instructor_creates_a_complete_multiple_choice_question(): void
    {
        $this->actAs($this->instructor)
            ->post(route('instructor.question-bank.store'), $this->mcqPayload('Which pandas method reads a CSV file?'))
            ->assertRedirect(route('instructor.question-bank.index'));

        $item = QuestionBankItem::with('options')->where('question_text', 'Which pandas method reads a CSV file?')->firstOrFail();
        $this->assertSame((int) $this->instructor->id, (int) $item->instructor_id);
        $this->assertSame('multiple_choice', $item->question_type);
        $this->assertSame(3, $item->points);
        $this->assertSame('Apply', $item->bloom_level);
        $this->assertSame(['read_csv()', 'to_csv()', 'head()'], $item->options->pluck('option_text')->all());
        $this->assertSame([true, false, false], $item->options->pluck('is_correct')->map(fn ($v) => (bool) $v)->all());

        $this->actAs($this->instructor)
            ->get(route('instructor.question-bank.index'))
            ->assertOk()
            ->assertSee('Which pandas method reads a CSV file?')
            ->assertSee('Thinking level: Apply');
    }

    public function test_incomplete_or_unsupported_questions_are_refused(): void
    {
        $before = QuestionBankItem::count();

        // No correct choice.
        $this->actAs($this->instructor)
            ->post(route('instructor.question-bank.store'), array_merge($this->mcqPayload('No answer marked'), ['correct_option' => null]))
            ->assertSessionHasErrors();
        // A type the assessment system cannot grade.
        $this->actAs($this->instructor)
            ->post(route('instructor.question-bank.store'), array_merge($this->mcqPayload('Coding question'), ['question_type' => 'coding']))
            ->assertSessionHasErrors('question_type');
        // A thinking level outside the TOS vocabulary.
        $this->actAs($this->instructor)
            ->post(route('instructor.question-bank.store'), array_merge($this->mcqPayload('Odd level'), ['bloom_level' => 'Memorize']))
            ->assertSessionHasErrors('bloom_level');
        // A fill-in-the-blank question without an accepted answer.
        $this->actAs($this->instructor)
            ->post(route('instructor.question-bank.store'), [
                'form_key' => 'bank-new', 'question_type' => 'fill_blank', 'question_text' => 'Blank without answer', 'points' => 1, 'correct_answer' => '',
            ])
            ->assertSessionHasErrors();

        $this->assertSame($before, QuestionBankItem::count());
    }

    public function test_instructors_manage_only_their_own_questions_and_never_see_others_private_ones(): void
    {
        $mine = $this->bankItem($this->instructor->id, 'My private question');
        $theirs = $this->bankItem($this->otherInstructor->id, 'Another instructor private question');
        $shared = $this->bankItem(null, 'Shared pool question');

        $this->actAs($this->instructor)
            ->get(route('instructor.question-bank.index'))
            ->assertOk()
            ->assertSee('My private question')
            ->assertSee('Shared pool question')
            ->assertDontSee('Another instructor private question');

        // Editing or archiving another instructor's or the shared pool's question is refused.
        foreach ([$theirs, $shared] as $foreign) {
            $this->actAs($this->instructor)
                ->patch(route('instructor.question-bank.update', $foreign), $this->mcqPayload('Hijacked'))
                ->assertForbidden();
            $this->actAs($this->instructor)
                ->patch(route('instructor.question-bank.archive', $foreign))
                ->assertForbidden();
        }
        $this->assertSame('Another instructor private question', $theirs->fresh()->question_text);
        $this->assertFalse($shared->fresh()->is_archived);

        // Editing one's own works.
        $this->actAs($this->instructor)
            ->patch(route('instructor.question-bank.update', $mine), $this->mcqPayload('My edited question'))
            ->assertRedirect();
        $this->assertSame('My edited question', $mine->fresh()->question_text);

        // Another instructor's private question cannot be copied in either.
        $draft = $this->makeAssessment(null, ['status' => 'draft']);
        $this->actAs($this->instructor)
            ->post(route('instructor.assessments.bank.add', $draft['assessment']), ['question_ids' => [$theirs->id]])
            ->assertSessionHasErrors('question_ids');
        $this->assertSame(2, AssessmentQuestion::where('assessment_id', $draft['assessment'])->count());
    }

    public function test_archived_questions_leave_the_lists_and_cannot_be_added(): void
    {
        $item = $this->bankItem($this->instructor->id, 'Question to archive');

        $this->actAs($this->instructor)
            ->patch(route('instructor.question-bank.archive', $item))
            ->assertRedirect();
        $this->assertTrue($item->fresh()->is_archived);

        $this->actAs($this->instructor)->get(route('instructor.question-bank.index'))->assertDontSee('Question to archive');
        $this->actAs($this->instructor)->get(route('instructor.question-bank.index', ['archived' => 1]))->assertSee('Question to archive');

        $draft = $this->makeAssessment(null, ['status' => 'draft']);
        $this->actAs($this->instructor)->get(route('instructor.assessments.bank', $draft['assessment']))->assertOk()->assertDontSee('Question to archive');
        $this->actAs($this->instructor)
            ->post(route('instructor.assessments.bank.add', $draft['assessment']), ['question_ids' => [$item->id]])
            ->assertSessionHasErrors('question_ids');

        // Restoring brings it back.
        $this->actAs($this->instructor)->patch(route('instructor.question-bank.archive', $item))->assertRedirect();
        $this->assertFalse($item->fresh()->is_archived);
    }

    public function test_students_never_reach_the_bank(): void
    {
        $item = $this->bankItem($this->instructor->id, 'Secret bank question');
        $draft = $this->makeAssessment(null, ['status' => 'draft']);

        $this->actAs($this->student)->get(route('instructor.question-bank.index'))->assertForbidden();
        $this->actAs($this->student)->post(route('instructor.question-bank.store'), $this->mcqPayload('Student write'))->assertForbidden();
        $this->actAs($this->student)->patch(route('instructor.question-bank.update', $item), $this->mcqPayload('Student edit'))->assertForbidden();
        $this->actAs($this->student)->get(route('instructor.assessments.bank', $draft['assessment']))->assertForbidden();
        $this->actAs($this->student)->post(route('instructor.assessments.bank.add', $draft['assessment']), ['question_ids' => [$item->id]])->assertForbidden();

        $this->assertSame('Secret bank question', $item->fresh()->question_text);
        $this->assertSame(0, QuestionBankItem::where('question_text', 'Student write')->count());
    }

    // ── Copying into assessments ───────────────────────────────────────

    public function test_bank_questions_are_appended_to_a_simple_assessment_as_copies(): void
    {
        $draft = $this->makeAssessment(null, ['status' => 'draft']);
        $mcq = $this->bankItem($this->instructor->id, 'Copied MCQ', ['points' => 4, 'ilo_id' => $this->ilo()]);
        $blank = $this->bankItem(null, 'Copied blank', ['question_type' => 'fill_blank', 'correct_answer' => "mean\naverage", 'points' => 2], false);

        $this->actAs($this->instructor)
            ->post(route('instructor.assessments.bank.add', $draft['assessment']), ['question_ids' => [$mcq->id, $blank->id]])
            ->assertRedirect()
            ->assertSessionHas('success', '2 questions added.');

        $questions = AssessmentQuestion::with('options')->where('assessment_id', $draft['assessment'])->orderBy('item_number')->get();
        $this->assertSame([1, 2, 3, 4], $questions->pluck('item_number')->all());
        $copiedMcq = $questions->firstWhere('question_text', 'Copied MCQ');
        $copiedBlank = $questions->firstWhere('question_text', 'Copied blank');
        $this->assertSame(4, (int) $copiedMcq->points);
        $this->assertSame(['A', 'B', 'C'], $copiedMcq->options->pluck('option_label')->all());
        $this->assertSame("mean\naverage", $copiedBlank->correct_answer);
        // The bank's learning outcome is an organizing tag only; assessment
        // questions stay unlinked from ILOs (DataSensei Updates 5).
        $this->assertNull($copiedMcq->ilo_id);
        $this->assertSame([], $copiedMcq->authoringErrors());

        $assessment = Assessment::findOrFail($draft['assessment']);
        $this->assertSame(4, (int) $assessment->total_items);
        $this->assertSame(16, (int) $assessment->total_points);
    }

    public function test_only_draft_assessments_of_the_instructor_accept_bank_questions(): void
    {
        $item = $this->bankItem($this->instructor->id, 'Late addition');
        $published = $this->makeAssessment(null);

        $this->actAs($this->instructor)
            ->post(route('instructor.assessments.bank.add', $published['assessment']), ['question_ids' => [$item->id]])
            ->assertStatus(422);
        $this->actAs($this->instructor)->get(route('instructor.assessments.bank', $published['assessment']))->assertStatus(422);

        $draft = $this->makeAssessment(null, ['status' => 'draft']);
        $this->actAs($this->otherInstructor)
            ->post(route('instructor.assessments.bank.add', $draft['assessment']), ['question_ids' => [$item->id]])
            ->assertForbidden();

        $this->assertSame(2, AssessmentQuestion::where('assessment_id', $published['assessment'])->count());
        $this->assertSame(2, AssessmentQuestion::where('assessment_id', $draft['assessment'])->count());
    }

    public function test_bank_edits_and_archiving_never_change_published_work_attempts_or_grades(): void
    {
        $draft = $this->makeAssessment(null, ['status' => 'draft']);
        $item = $this->bankItem($this->instructor->id, 'Original wording');
        $this->actAs($this->instructor)
            ->post(route('instructor.assessments.bank.add', $draft['assessment']), ['question_ids' => [$item->id]])
            ->assertRedirect();
        $this->actAs($this->instructor)->patch(route('instructor.assessments.publish', $draft['assessment']))->assertRedirect();

        $copied = AssessmentQuestion::with('options')->where('assessment_id', $draft['assessment'])->where('question_text', 'Original wording')->firstOrFail();
        $copiedCorrect = $copied->options->firstWhere('is_correct', true);

        // The student answers every question correctly and is graded.
        $attempt = $this->makeAttempt($draft['assessment']);
        $this->actAs($this->student)
            ->post(route('student.assessments.submit', [$draft['assessment'], $attempt]), ['answers' => [
                $draft['mcq'] => (string) $draft['correct'],
                $draft['blank'] => 'pandas',
                $copied->id => (string) $copiedCorrect->id,
            ]])
            ->assertRedirect();
        $graded = $attempt->fresh();
        $this->assertSame('graded', $graded->status);
        $this->assertSame('11.00', $graded->score);

        // The bank question is rewritten (new text, another correct choice) and archived.
        $this->actAs($this->instructor)
            ->patch(route('instructor.question-bank.update', $item), array_merge($this->mcqPayload('Rewritten wording'), ['correct_option' => 2]))
            ->assertRedirect();
        $this->actAs($this->instructor)->patch(route('instructor.question-bank.archive', $item))->assertRedirect();

        $copied->refresh()->load('options');
        $this->assertSame('Original wording', $copied->question_text);
        $this->assertSame($copiedCorrect->id, $copied->options->firstWhere('is_correct', true)->id);
        $this->assertSame('11.00', $graded->fresh()->score);
        $this->assertSame('graded', $graded->fresh()->status);

        $this->actAs($this->student)
            ->get(route('student.assessments.result', [$draft['assessment'], $attempt]))
            ->assertOk()
            ->assertSee('Original wording')
            ->assertDontSee('Rewritten wording');
    }

    public function test_a_builder_question_can_be_saved_to_the_bank_as_its_own_copy(): void
    {
        $draft = $this->makeAssessment(null, ['status' => 'draft']);

        $this->actAs($this->instructor)
            ->post(route('instructor.assessments.questions.store', $draft['assessment']), [
                'question_type' => 'true_false',
                'question_text' => 'A DataFrame can hold several columns.',
                'correct_answer' => 'true',
                'points' => 2,
                'save_to_bank' => 1,
            ])
            ->assertRedirect()
            ->assertSessionHas('success', fn ($message) => str_contains($message, 'also saved to your Question Bank'));

        $banked = QuestionBankItem::where('question_text', 'A DataFrame can hold several columns.')->firstOrFail();
        $this->assertSame((int) $this->instructor->id, (int) $banked->instructor_id);
        $this->assertSame('true', $banked->correct_answer);

        // Editing the assessment question later leaves the bank copy alone.
        $question = AssessmentQuestion::where('assessment_id', $draft['assessment'])->where('question_text', 'A DataFrame can hold several columns.')->firstOrFail();
        $question->update(['question_text' => 'Changed in the builder']);
        $this->assertSame('A DataFrame can hold several columns.', $banked->fresh()->question_text);

        // An incomplete question is not banked.
        $this->actAs($this->instructor)
            ->post(route('instructor.assessments.questions.store', $draft['assessment']), [
                'question_type' => 'multiple_choice',
                'question_text' => '',
                'points' => 1,
                'save_to_bank' => 1,
            ])
            ->assertRedirect()
            ->assertSessionHas('success', fn ($message) => str_contains($message, 'not saved to your Question Bank'));
        $this->assertSame(1, QuestionBankItem::where('instructor_id', $this->instructor->id)->count());
    }

    // ── Table of Specifications ────────────────────────────────────────

    public function test_a_tos_assessment_only_takes_bank_questions_that_fit_a_missing_planned_item(): void
    {
        [$assessmentId, $slots] = $this->tosAssessment(moduleNo: 2, plan: [
            ['topic' => 'Loops', 'level' => 'Remember', 'items' => 1, 'points' => 2],
            ['topic' => 'Functions', 'level' => 'Apply', 'items' => 2, 'points' => 5],
        ]);

        $apply = $this->bankItem($this->instructor->id, 'Apply question', ['bloom_level' => 'Apply', 'module_no' => 2, 'points' => 1]);
        $untagged = $this->bankItem($this->instructor->id, 'Untagged question');
        $otherModule = $this->bankItem($this->instructor->id, 'Other module question', ['bloom_level' => 'Apply', 'module_no' => 7]);
        $evaluate = $this->bankItem($this->instructor->id, 'Evaluate question', ['bloom_level' => 'Evaluate']);
        $remember = $this->bankItem(null, 'Shared remember question', ['bloom_level' => 'Remember']);

        // The picker lists the missing items and starts with fitting questions only.
        $page = $this->actAs($this->instructor)->get(route('instructor.assessments.bank', $assessmentId))->assertOk();
        $page->assertSee('0 filled, 3 missing.')
            ->assertSee('Missing planned items')
            ->assertSee('Apply question')
            ->assertSee('Shared remember question')
            ->assertDontSee('Untagged question')
            ->assertDontSee('Other module question')
            ->assertDontSee('Evaluate question')
            ->assertSee('Fills planned item 2 (Functions, Apply).')
            ->assertSee('Fills planned item 1 (Loops, Remember).');
        $all = $this->actAs($this->instructor)->get(route('instructor.assessments.bank', [$assessmentId, 'fit' => 'all']))->assertOk();
        $all->assertSee('Untagged question')->assertSee('Does not match a missing planned item.');

        // Adding everything fills only what fits; the rest is reported, not forced in.
        $this->actAs($this->instructor)
            ->post(route('instructor.assessments.bank.add', $assessmentId), ['question_ids' => [$apply->id, $untagged->id, $otherModule->id, $evaluate->id, $remember->id]])
            ->assertRedirect()
            ->assertSessionHas('success', '2 questions added. 3 not added: they do not match the module or thinking level of any missing planned item.');

        $filled = AssessmentQuestion::whereIn('id', $slots)->orderBy('item_number')->get();
        // Item 1 (Remember) took the shared Remember question, item 2 (Apply) the Apply one;
        // both keep their planned topic, level and points, and the count never grows.
        $this->assertSame(['Shared remember question', 'Apply question', null], $filled->pluck('question_text')->all());
        $this->assertSame(['Loops', 'Functions', 'Functions'], $filled->pluck('topic_title')->all());
        $this->assertSame(['Remember', 'Apply', 'Apply'], $filled->pluck('bloom_level')->all());
        $this->assertSame([2, 5, 5], $filled->pluck('points')->map(fn ($p) => (int) $p)->all());
        $this->assertSame('unconfigured', $filled[2]->question_type);
        $this->assertSame(3, AssessmentQuestion::where('assessment_id', $assessmentId)->count());

        // The plan is not reported as covered, and publishing is still refused.
        $this->actAs($this->instructor)->get(route('instructor.assessments.bank', $assessmentId))->assertSee('2 filled, 1 missing.');
        $this->actAs($this->instructor)->patch(route('instructor.assessments.publish', $assessmentId))->assertSessionHasErrors('assessment');
        $this->assertSame('draft', Assessment::findOrFail($assessmentId)->status);

        // Once every item is filled, extra questions are refused as "already filled".
        $secondApply = $this->bankItem($this->instructor->id, 'Second apply question', ['bloom_level' => 'Apply']);
        $thirdApply = $this->bankItem($this->instructor->id, 'Third apply question', ['bloom_level' => 'Apply']);
        $this->actAs($this->instructor)
            ->post(route('instructor.assessments.bank.add', $assessmentId), ['question_ids' => [$secondApply->id, $thirdApply->id]])
            ->assertSessionHas('success', '1 question added. 1 not added: every planned TOS item is already filled.');
        $this->actAs($this->instructor)->get(route('instructor.assessments.bank', $assessmentId))
            ->assertSee('3 filled, 0 missing.')
            ->assertSee('Every planned item is filled');
        $this->assertSame(3, AssessmentQuestion::where('assessment_id', $assessmentId)->count());

        $this->actAs($this->instructor)->patch(route('instructor.assessments.publish', $assessmentId))->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('published', Assessment::findOrFail($assessmentId)->status);
    }

    // ── Helpers ────────────────────────────────────────────────────────

    /** @return array<string, mixed> */
    private function mcqPayload(string $text): array
    {
        return [
            'form_key' => 'bank-new',
            'question_type' => 'multiple_choice',
            'question_text' => $text,
            'points' => 3,
            'option_texts' => ['read_csv()', 'to_csv()', 'head()', ''],
            'correct_option' => 0,
            'answer_explanation' => 'read_csv() loads a CSV file into a DataFrame.',
            'module_no' => 2,
            'topic_title' => 'Reading data',
            'difficulty_slug' => 'newbie',
            'bloom_level' => 'Apply',
        ];
    }

    /** @param array<string, mixed> $overrides */
    private function bankItem(?int $instructorId, string $text, array $overrides = [], bool $withOptions = true): QuestionBankItem
    {
        $item = QuestionBankItem::create(array_merge([
            'instructor_id' => $instructorId,
            'question_type' => 'multiple_choice',
            'question_text' => $text,
            'points' => 1,
            'is_archived' => false,
        ], $overrides));

        if ($withOptions && $item->question_type === 'multiple_choice') {
            foreach ([['Right', true], ['Wrong', false], ['Also wrong', false]] as $index => [$optionText, $correct]) {
                $item->options()->create(['option_text' => $optionText, 'is_correct' => $correct, 'order_index' => $index + 1]);
            }
        }

        return $item;
    }

    private function ilo(): int
    {
        return (int) DB::table('intended_learning_outcomes')->insertGetId([
            'module_no' => 2,
            'ilo_code' => 'M2-ILO1',
            'title' => 'Write small functions',
            'description' => 'x',
            'sort_order' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * A draft assessment generated from a TOS: one unconfigured planned item
     * per TOS item, carrying the row's topic, thinking level and points.
     *
     * @param list<array{topic: string, level: string, items: int, points: int}> $plan
     * @return array{0: int, 1: list<int>}
     */
    private function tosAssessment(int $moduleNo, array $plan): array
    {
        $total = array_sum(array_column($plan, 'items'));
        $tosId = (int) DB::table('table_of_specifications')->insertGetId([
            'class_id' => $this->classId,
            'module_no' => $moduleNo,
            'title' => 'Module plan',
            'total_items' => $total,
            'status' => 'final',
            'created_by' => $this->instructor->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $assessmentId = (int) DB::table('assessments')->insertGetId([
            'table_of_specification_id' => $tosId,
            'class_id' => $this->classId,
            'created_by' => $this->instructor->id,
            'title' => 'Planned quiz',
            'status' => 'draft',
            'purpose' => 'quiz',
            'total_items' => $total,
            'total_points' => array_sum(array_map(fn ($row) => $row['items'] * $row['points'], $plan)),
            'max_attempts' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $slots = [];
        $number = 1;
        foreach ($plan as $row) {
            $rowId = (int) DB::table('table_of_specification_rows')->insertGetId([
                'table_of_specification_id' => $tosId,
                'topic_title' => $row['topic'],
                'cognitive_level' => $row['level'],
                'difficulty_slug' => 'newbie',
                'item_count' => $row['items'],
                'default_points' => $row['points'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            for ($i = 0; $i < $row['items']; $i++) {
                $slots[] = (int) DB::table('assessment_questions')->insertGetId([
                    'assessment_id' => $assessmentId,
                    'table_of_specification_row_id' => $rowId,
                    'item_number' => $number++,
                    'question_type' => 'unconfigured',
                    'points' => $row['points'],
                    'is_required' => true,
                    'authoring_touched' => false,
                    'topic_title' => $row['topic'],
                    'bloom_level' => $row['level'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return [$assessmentId, $slots];
    }
}
