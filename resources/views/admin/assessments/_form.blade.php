<form method="POST" action="{{ $formAction }}">
  @csrf
  @if($formMethod !== 'POST')
    @method($formMethod)
  @endif

  @if($hasReferences ?? false)
    <div class="notice error">
      This assessment version is already used by a class assignment. Metadata may be corrected, but changing questions or answers requires duplicating it as a new version.
    </div>
  @endif

  <section class="panel">
    <div class="panel-head"><div class="panel-heading"><h2 class="panel-title">Assessment Identity and Version</h2><p class="panel-subtitle">Each assessment-library row represents one reusable curriculum version.</p></div></div>
    <div class="panel-body">
      <div class="form-grid three">
        <div class="field"><label for="module-no">Module Number</label><input id="module-no" class="input" type="number" name="module_no" min="1" value="{{ old('module_no', $assessment->module_no) }}" required></div>
        <div class="field"><label for="assignment-code">Unique Assignment Code</label><input id="assignment-code" class="input" name="assignment_code" value="{{ old('assignment_code', $assessment->assignment_code) }}" placeholder="M01-ASSESS-V1" required></div>
        <div class="field"><label for="year-level">Year Level</label><input id="year-level" class="input" name="year_level" value="{{ old('year_level', $assessment->year_level) }}" placeholder="Year 1" required></div>
        <div class="field"><label for="version-no">Version Number</label><input id="version-no" class="input" type="number" name="version_no" min="1" value="{{ old('version_no', $assessment->version_no) }}" required></div>
        <div class="field"><label for="version-name">Version Name</label><input id="version-name" class="input" name="version_name" value="{{ old('version_name', $assessment->version_name) }}" required></div>
        <div class="field"><label for="version-code">Version Code</label><input id="version-code" class="input" name="version_code" value="{{ old('version_code', $assessment->version_code) }}" placeholder="V1" required></div>
      </div>
    </div>
  </section>

  <section class="panel" style="margin-top:24px">
    <div class="panel-head"><div class="panel-heading"><h2 class="panel-title">Assessment Settings</h2><p class="panel-subtitle">This information is presented to instructors when they create class assignments.</p></div></div>
    <div class="panel-body">
      <div class="form-grid three">
        <div class="field" style="grid-column:span 2"><label for="assessment-title">Title</label><input id="assessment-title" class="input" name="title" value="{{ old('title', $assessment->title) }}" required></div>
        <div class="field"><label for="topic-title">Topic Title</label><input id="topic-title" class="input" name="topic_title" value="{{ old('topic_title', $assessment->topic_title) }}" required></div>
        <div class="field">
          <label for="assignment-type">Assessment Type</label>
          <select id="assignment-type" class="select" name="assignment_type" required>
            <option value="mcq" @selected(old('assignment_type', $assessment->assignment_type) === 'mcq')>MCQ</option>
            <option value="fill_blank" @selected(old('assignment_type', $assessment->assignment_type) === 'fill_blank')>Fill in the Blanks</option>
            <option value="mixed" @selected(old('assignment_type', $assessment->assignment_type) === 'mixed')>Mixed</option>
          </select>
        </div>
        <div class="field"><label for="time-limit">Time Limit in Minutes</label><input id="time-limit" class="input" type="number" name="time_limit_minutes" min="1" max="1440" value="{{ old('time_limit_minutes', $assessment->time_limit_minutes ?? 20) }}" required></div>
        <div class="field"><label for="sort-order">Sort Order</label><input id="sort-order" class="input" type="number" name="sort_order" min="0" value="{{ old('sort_order', $assessment->sort_order ?? 0) }}" required></div>
        <div class="field"><label for="assessment-status">Publication Status</label><select id="assessment-status" class="select" name="is_active"><option value="0" @selected(!old('is_active', $assessment->is_active))>Inactive / Draft</option><option value="1" @selected((bool) old('is_active', $assessment->is_active))>Published</option></select></div>
      </div>
      <div class="field" style="margin-top:14px"><label for="description">Description</label><textarea id="description" class="textarea" name="description">{{ old('description', $assessment->description) }}</textarea></div>
      <div class="field" style="margin-top:14px"><label for="instructions">Instructions</label><textarea id="instructions" class="textarea" name="instructions">{{ old('instructions', $assessment->instructions) }}</textarea></div>
    </div>
  </section>

  <section class="panel" style="margin-top:24px" data-question-editor>
    <div class="panel-head">
      <div class="panel-heading"><h2 class="panel-title">Assessment Questions</h2><p class="panel-subtitle">Map a question only to ILOs it genuinely measures. Unmapped questions do not count as ILO mastery evidence.</p></div>
      <div class="action-row"><button class="btn small" type="button" data-add-question="mcq">Add MCQ</button><button class="btn small secondary" type="button" data-add-question="fill_blank">Add Fill Blank</button></div>
    </div>

    <div class="question-list" data-question-list>
      @foreach($questions as $questionIndex => $question)
        <article class="question-item">
          <div class="question-head">
            <strong>Question <span data-question-number>{{ $loop->iteration }}</span></strong>
            <div class="action-row"><button class="btn small secondary" type="button" data-move-up>Move Up</button><button class="btn small secondary" type="button" data-move-down>Move Down</button><button class="btn small danger" type="button" data-remove-question>Remove</button></div>
          </div>
          <div class="form-grid three">
            <div class="field"><label>Question Type</label><select class="select" data-field="question_type" name="questions[{{ $questionIndex }}][question_type]"><option value="mcq" @selected(($question['question_type'] ?? 'mcq') === 'mcq')>MCQ</option><option value="fill_blank" @selected(($question['question_type'] ?? '') === 'fill_blank')>Fill in the Blanks</option></select></div>
            <div class="field"><label>Points</label><input class="input" type="number" min="1" data-field="points" name="questions[{{ $questionIndex }}][points]" value="{{ $question['points'] ?? 1 }}" required></div>
            <div class="field">
              <label>ILO Evidence</label>
              <select class="select ilo-select" multiple size="4" data-field="ilo_ids" name="questions[{{ $questionIndex }}][ilo_ids][]">
                @foreach($ilos as $ilo)
                  <option value="{{ $ilo->id }}" @selected(in_array((int) $ilo->id, array_map('intval', $question['ilo_ids'] ?? []), true))>M{{ str_pad((string) $ilo->module_no, 2, '0', STR_PAD_LEFT) }} · {{ $ilo->ilo_code }} — {{ $ilo->title }}</option>
                @endforeach
              </select>
              <span class="field-hint">Choose only ILOs from this assessment's module. Leave blank when the item is not valid evidence.</span>
            </div>
          </div>
          <div class="field" style="margin-top:12px"><label>Question Text</label><textarea class="textarea" data-field="question_text" name="questions[{{ $questionIndex }}][question_text]" required>{{ $question['question_text'] ?? '' }}</textarea></div>
          <div class="field" style="margin-top:12px"><label>Explanation</label><textarea class="textarea" data-field="explanation" name="questions[{{ $questionIndex }}][explanation]">{{ $question['explanation'] ?? '' }}</textarea></div>

          <div data-options-block>
            <div class="option-list" data-option-list>
              @foreach(($question['options'] ?? []) as $optionIndex => $option)
                <div class="option-item">
                  <label class="correct-choice"><input type="radio" data-field="correct_option" name="questions[{{ $questionIndex }}][correct_option]" value="{{ $optionIndex }}" @checked((int) ($question['correct_option'] ?? 0) === (int) $optionIndex)><span data-option-number>{{ $loop->iteration }}</span></label>
                  <input class="input" data-field="option_text" name="questions[{{ $questionIndex }}][options][{{ $optionIndex }}][option_text]" value="{{ $option['option_text'] ?? '' }}" placeholder="Answer choice">
                  <button class="btn small secondary" type="button" data-move-up>↑</button><button class="btn small secondary" type="button" data-move-down>↓</button><button class="btn small danger" type="button" data-remove-option>Remove</button>
                </div>
              @endforeach
            </div>
            <button class="btn small secondary" type="button" data-add-option>Add Choice</button>
          </div>

          <div data-answers-block>
            <div class="answer-list" data-answer-list>
              @foreach(($question['blank_answers'] ?? []) as $answerIndex => $answer)
                <div class="answer-item">
                  <span class="answer-number" data-answer-number>{{ $loop->iteration }}</span>
                  <input class="input" data-field="answer_text" name="questions[{{ $questionIndex }}][blank_answers][{{ $answerIndex }}][answer_text]" value="{{ $answer['answer_text'] ?? '' }}" placeholder="Accepted answer">
                  <label class="case-check"><input type="checkbox" value="1" data-field="is_case_sensitive" name="questions[{{ $questionIndex }}][blank_answers][{{ $answerIndex }}][is_case_sensitive]" @checked($answer['is_case_sensitive'] ?? false)> Case-sensitive</label>
                  <button class="btn small secondary" type="button" data-move-up>↑</button><button class="btn small secondary" type="button" data-move-down>↓</button><button class="btn small danger" type="button" data-remove-answer>Remove</button>
                </div>
              @endforeach
            </div>
            <button class="btn small secondary" type="button" data-add-answer>Add Accepted Answer</button>
          </div>
        </article>
      @endforeach
    </div>

    <template data-question-template>
      <article class="question-item">
        <div class="question-head"><strong>Question <span data-question-number></span></strong><div class="action-row"><button class="btn small secondary" type="button" data-move-up>Move Up</button><button class="btn small secondary" type="button" data-move-down>Move Down</button><button class="btn small danger" type="button" data-remove-question>Remove</button></div></div>
        <div class="form-grid three"><div class="field"><label>Question Type</label><select class="select" data-field="question_type"><option value="mcq">MCQ</option><option value="fill_blank">Fill in the Blanks</option></select></div><div class="field"><label>Points</label><input class="input" type="number" min="1" value="1" data-field="points" required></div><div class="field"><label>ILO Evidence</label><select class="select ilo-select" multiple size="4" data-field="ilo_ids">@foreach($ilos as $ilo)<option value="{{ $ilo->id }}">M{{ str_pad((string) $ilo->module_no, 2, '0', STR_PAD_LEFT) }} · {{ $ilo->ilo_code }} — {{ $ilo->title }}</option>@endforeach</select><span class="field-hint">Choose only ILOs from this assessment's module.</span></div></div>
        <div class="field" style="margin-top:12px"><label>Question Text</label><textarea class="textarea" data-field="question_text" required></textarea></div>
        <div class="field" style="margin-top:12px"><label>Explanation</label><textarea class="textarea" data-field="explanation"></textarea></div>
        <div data-options-block><div class="option-list" data-option-list>
          @for($i = 0; $i < 4; $i++)
            <div class="option-item"><label class="correct-choice"><input type="radio" data-field="correct_option" @checked($i === 0)><span data-option-number>{{ $i + 1 }}</span></label><input class="input" data-field="option_text" placeholder="Answer choice"><button class="btn small secondary" type="button" data-move-up>↑</button><button class="btn small secondary" type="button" data-move-down>↓</button><button class="btn small danger" type="button" data-remove-option>Remove</button></div>
          @endfor
        </div><button class="btn small secondary" type="button" data-add-option>Add Choice</button></div>
        <div data-answers-block><div class="answer-list" data-answer-list><div class="answer-item"><span class="answer-number" data-answer-number>1</span><input class="input" data-field="answer_text" placeholder="Accepted answer"><label class="case-check"><input type="checkbox" value="1" data-field="is_case_sensitive"> Case-sensitive</label><button class="btn small secondary" type="button" data-move-up>↑</button><button class="btn small secondary" type="button" data-move-down>↓</button><button class="btn small danger" type="button" data-remove-answer>Remove</button></div></div><button class="btn small secondary" type="button" data-add-answer>Add Accepted Answer</button></div>
      </article>
    </template>

    <template data-option-template><div class="option-item"><label class="correct-choice"><input type="radio" data-field="correct_option"><span data-option-number></span></label><input class="input" data-field="option_text" placeholder="Answer choice"><button class="btn small secondary" type="button" data-move-up>↑</button><button class="btn small secondary" type="button" data-move-down>↓</button><button class="btn small danger" type="button" data-remove-option>Remove</button></div></template>
    <template data-answer-template><div class="answer-item"><span class="answer-number" data-answer-number></span><input class="input" data-field="answer_text" placeholder="Accepted answer"><label class="case-check"><input type="checkbox" value="1" data-field="is_case_sensitive"> Case-sensitive</label><button class="btn small secondary" type="button" data-move-up>↑</button><button class="btn small secondary" type="button" data-move-down>↓</button><button class="btn small danger" type="button" data-remove-answer>Remove</button></div></template>
  </section>

  <div class="action-row" style="margin-top:24px"><button class="btn" type="submit">{{ $submitLabel }}</button><a class="btn secondary" href="{{ $cancelUrl }}">Cancel</a></div>
</form>

@push('head')
<style>
  .question-list { display:grid; gap:16px; padding:18px; }
  .question-item { padding:18px; border:1px solid var(--border); border-radius:var(--radius); background:var(--surface3); }
  .question-head { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:14px; }
  .option-list, .answer-list { display:grid; gap:10px; margin:14px 0; }
  .option-item { display:grid; grid-template-columns:auto minmax(180px,1fr) auto auto auto; align-items:center; gap:8px; }
  .answer-item { display:grid; grid-template-columns:auto minmax(180px,1fr) auto auto auto auto; align-items:center; gap:8px; }
  .correct-choice, .answer-number { min-width:34px; height:34px; display:flex; align-items:center; justify-content:center; gap:4px; border:1px solid var(--border); border-radius:var(--radius-sm); color:var(--muted); }
  .correct-choice { cursor:pointer; }
  .correct-choice input { accent-color:var(--accent3); }
  .case-check { display:flex; align-items:center; gap:7px; color:var(--muted); font-size:.75rem; white-space:nowrap; }
  .case-check input { accent-color:var(--accent); }
  .ilo-select { min-height:104px; }
  .field-hint { display:block; margin-top:6px; color:var(--muted); font-size:.72rem; line-height:1.45; }
  [hidden] { display:none !important; }
  @media (max-width:850px) { .option-item, .answer-item { grid-template-columns:auto 1fr; } .question-head { align-items:flex-start; flex-direction:column; } }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/admin-content-manager.js') }}"></script>
@endpush
