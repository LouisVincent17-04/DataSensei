{{-- One bank question's form (add or edit). Needs $types, $ilos, $action;
     optional $item (existing QuestionBankItem with options), $method. --}}
@php
  $q = $item ?? null;
  $prefix = $q ? 'bank'.$q->id : 'bank-new';
  $isOld = old('form_key') === $prefix;
  $type = $isOld ? old('question_type') : ($q->question_type ?? 'multiple_choice');
  $choices = $isOld
      ? array_values((array) old('option_texts', []))
      : ($q ? $q->options->pluck('option_text')->all() : []);
  $correctChoice = $isOld ? old('correct_option') : null;
  if (! $isOld && $q) {
      foreach ($q->options->values() as $optionIndex => $option) {
          if ($option->is_correct) { $correctChoice = $optionIndex; }
      }
  }
  while (count($choices) < 4) { $choices[] = ''; }
  $answer = $isOld ? old('correct_answer') : ($q->correct_answer ?? '');
  $tf = strtolower(trim((string) $answer));
  $bloomLevels = \App\Models\QuestionBankItem::THINKING_LEVELS;
  $difficulties = \App\Models\QuestionBankItem::DIFFICULTIES;
@endphp
<form method="POST" action="{{ $action }}" class="q-form" data-bank-form>
  @csrf
  @if(($method ?? 'POST') !== 'POST') @method($method) @endif
  <input type="hidden" name="form_key" value="{{ $prefix }}">

  <div class="form-grid three">
    <div class="field">
      <label for="{{ $prefix }}-type">Question type</label>
      <select class="select" id="{{ $prefix }}-type" name="question_type" data-type-select>
        @foreach($types as $value => $label)
          <option value="{{ $value }}" @selected($type === $value)>{{ $label }}</option>
        @endforeach
      </select>
    </div>
    <div class="field">
      <label for="{{ $prefix }}-points">Default points</label>
      <input class="input" id="{{ $prefix }}-points" type="number" name="points" min="1" max="1000" value="{{ $isOld ? old('points') : ($q->points ?? 1) }}" required>
    </div>
    <div class="field">
      <label for="{{ $prefix }}-module">Module number</label>
      <input class="input" id="{{ $prefix }}-module" type="number" name="module_no" min="1" max="9999" value="{{ $isOld ? old('module_no') : ($q->module_no ?? '') }}" placeholder="Optional">
    </div>
  </div>

  <div class="field" style="margin-top:14px">
    <label for="{{ $prefix }}-text">Question</label>
    <textarea class="textarea" id="{{ $prefix }}-text" name="question_text" maxlength="30000" placeholder="Type the question students will answer." required>{{ $isOld ? old('question_text') : ($q->question_text ?? '') }}</textarea>
  </div>

  <div class="field q-block" data-for="multiple_choice" style="margin-top:14px">
    <span class="label">Choices <span class="muted">(select the correct one; blank rows are ignored)</span></span>
    <ol class="q-choices">
      @foreach($choices as $index => $choice)
        <li class="q-choice">
          <label class="q-correct" title="Correct answer">
            <input type="radio" name="correct_option" value="{{ $index }}" @checked($correctChoice !== null && (string) $correctChoice === (string) $index)>
            <span class="q-letter">{{ chr(65 + $index) }}</span>
          </label>
          <input class="input" name="option_texts[{{ $index }}]" maxlength="5000" value="{{ $choice }}" placeholder="Choice {{ chr(65 + $index) }}">
        </li>
      @endforeach
    </ol>
  </div>

  <div class="field q-block" data-for="true_false" style="margin-top:14px">
    <span class="label">Correct answer</span>
    <label class="q-inline"><input type="radio" name="correct_answer" value="true" @checked($tf === 'true')> True</label>
    <label class="q-inline"><input type="radio" name="correct_answer" value="false" @checked($tf === 'false')> False</label>
  </div>

  <div class="field q-block" data-for="short_answer fill_blank" style="margin-top:14px">
    <label for="{{ $prefix }}-answer">Correct answer</label>
    <textarea class="textarea" id="{{ $prefix }}-answer" name="correct_answer" placeholder="Enter one accepted answer per line.">{{ is_string($answer) && ! in_array($tf, ['true', 'false'], true) ? $answer : '' }}</textarea>
    <span class="hint">Put each accepted answer on its own line. Capital letters do not matter.</span>
  </div>

  <div class="field q-block" data-for="essay" style="margin-top:14px">
    <label for="{{ $prefix }}-rubric">Notes for grading <span class="muted">(optional, only you see them)</span></label>
    <textarea class="textarea" id="{{ $prefix }}-rubric" name="rubric_text" maxlength="30000" placeholder="What a full-credit answer includes.">{{ $isOld ? old('rubric_text') : ($q->rubric_text ?? '') }}</textarea>
  </div>

  <div class="field" style="margin-top:14px">
    <label for="{{ $prefix }}-explanation">Explanation shown after grading <span class="muted">(optional)</span></label>
    <textarea class="textarea" id="{{ $prefix }}-explanation" name="answer_explanation" maxlength="10000" placeholder="Why the correct answer is correct.">{{ $isOld ? old('answer_explanation') : ($q->answer_explanation ?? '') }}</textarea>
  </div>

  <details style="margin-top:14px" @if($isOld || ($q && ($q->topic_title || $q->difficulty_slug || $q->ilo_id || $q->bloom_level))) open @endif>
    <summary class="hint" style="cursor:pointer">Organize this question (topic, difficulty, {!! \App\Support\Glossary::help('learning_outcome', 'ILO') !!}, {!! \App\Support\Glossary::help('bloom_level', 'thinking level') !!})</summary>
    <div class="form-grid three" style="margin-top:12px">
      <div class="field">
        <label for="{{ $prefix }}-topic">Topic</label>
        <input class="input" id="{{ $prefix }}-topic" name="topic_title" maxlength="189" value="{{ $isOld ? old('topic_title') : ($q->topic_title ?? '') }}" placeholder="Example: Descriptive Statistics">
      </div>
      <div class="field">
        <label for="{{ $prefix }}-difficulty">Difficulty</label>
        <select class="select" id="{{ $prefix }}-difficulty" name="difficulty_slug">
          <option value="">Not set</option>
          @foreach($difficulties as $slug => $label)
            <option value="{{ $slug }}" @selected(($isOld ? old('difficulty_slug') : $q?->difficulty_slug) === $slug)>{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div class="field">
        <label for="{{ $prefix }}-ilo">Learning outcome {!! \App\Support\Glossary::help('learning_outcome') !!}</label>
        <select class="select" id="{{ $prefix }}-ilo" name="ilo_id">
          <option value="">Not linked</option>
          @foreach($ilos as $ilo)
            <option value="{{ $ilo->id }}" @selected((string) ($isOld ? old('ilo_id') : $q?->ilo_id) === (string) $ilo->id)>Module {{ $ilo->module_no }}: {{ \Illuminate\Support\Str::limit($ilo->title, 70) }}</option>
          @endforeach
        </select>
      </div>
      <div class="field">
        <label for="{{ $prefix }}-bloom">Thinking level {!! \App\Support\Glossary::help('bloom_level') !!}</label>
        <select class="select" id="{{ $prefix }}-bloom" name="bloom_level">
          <option value="">Not set</option>
          @foreach($bloomLevels as $level)
            <option value="{{ $level }}" @selected(($isOld ? old('bloom_level') : $q?->bloom_level) === $level)>{{ $level }}</option>
          @endforeach
        </select>
      </div>
    </div>
  </details>

  <div class="form-foot">
    @isset($cancelHref)<a class="btn" href="{{ $cancelHref }}">Cancel</a>@endisset
    <button class="btn primary" type="submit">{{ $submitLabel }}</button>
  </div>
</form>
