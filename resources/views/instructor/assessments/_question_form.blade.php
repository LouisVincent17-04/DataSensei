{{-- One question's form (add or edit). Needs $types, $action, $submitLabel;
     optional $question (existing AssessmentQuestion with options), $method.
     Only the fields of the chosen type are sent; the others are disabled. --}}
@php
  $q = $question ?? null;
  $prefix = $q ? 'q'.$q->id : 'new';
  $isOld = old('form_key') === $prefix;
  $type = $isOld ? old('question_type') : ($q->question_type ?? 'multiple_choice');
  $typeOptions = $types;
  if ($type === 'fill_blank') { $typeOptions = ['fill_blank' => 'Fill in the blank'] + $typeOptions; }
  $choices = $isOld
      ? array_values((array) old('option_texts', []))
      : ($q ? $q->options->sortBy('order_index')->pluck('option_text')->all() : []);
  $correctChoice = $isOld ? old('correct_option') : null;
  if (! $isOld && $q) {
    foreach ($q->options->sortBy('order_index')->values() as $optionIndex => $option) {
      if ($option->is_correct) { $correctChoice = $optionIndex; }
    }
  }
  while (count($choices) < 4) { $choices[] = ''; }
  $answer = $isOld ? old('correct_answer') : ($q->correct_answer ?? '');
  $tf = strtolower(trim((string) $answer));
@endphp
<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="q-form" data-question-form>
  @csrf
  @if(($method ?? 'POST') !== 'POST') @method($method) @endif
  <input type="hidden" name="form_key" value="{{ $prefix }}">
  <input type="hidden" name="intent" value="save">

  <div class="form-grid three">
    <div class="field">
      <label for="{{ $prefix }}-type">Question type</label>
      <select class="select" id="{{ $prefix }}-type" name="question_type" data-type-select>
        @if($type === 'unconfigured')<option value="unconfigured" selected>Choose a type</option>@endif
        @foreach($typeOptions as $value => $label)
          <option value="{{ $value }}" @selected($type === $value)>{{ $label }}</option>
        @endforeach
      </select>
    </div>
    <div class="field">
      <label for="{{ $prefix }}-points">Points</label>
      <input class="input" id="{{ $prefix }}-points" type="number" name="points" min="1" max="1000" value="{{ $isOld ? old('points') : ($q->points ?? 1) }}" required>
    </div>
  </div>

  <div class="field" style="margin-top:14px">
    <label for="{{ $prefix }}-text">Question</label>
    <textarea class="textarea" id="{{ $prefix }}-text" name="question_text" maxlength="30000" placeholder="Type the question students will answer.">{{ $isOld ? old('question_text') : ($q->question_text ?? '') }}</textarea>
  </div>

  {{-- Multiple choice --}}
  <div class="field q-block" data-for="multiple_choice" style="margin-top:14px">
    <span class="label">Choices <span class="muted">(select the correct one)</span></span>
    <ol class="q-choices" data-choices>
      @foreach($choices as $index => $choice)
        <li class="q-choice">
          <label class="q-correct" title="Correct answer">
            <input type="radio" name="correct_option" value="{{ $index }}" @checked($correctChoice !== null && (string) $correctChoice === (string) $index)>
            <span class="q-letter">{{ chr(65 + $index) }}</span>
          </label>
          <input class="input" name="option_texts[{{ $index }}]" maxlength="5000" value="{{ $choice }}" placeholder="Choice {{ chr(65 + $index) }}">
          <button class="btn small" type="button" data-remove-choice aria-label="Remove choice {{ chr(65 + $index) }}">Remove</button>
        </li>
      @endforeach
    </ol>
    <button class="btn small" type="button" data-add-choice>Add choice</button>
  </div>

  {{-- True or false --}}
  <div class="field q-block" data-for="true_false" style="margin-top:14px">
    <span class="label">Correct answer</span>
    <label class="q-inline"><input type="radio" name="correct_answer" value="true" @checked($tf === 'true')> True</label>
    <label class="q-inline"><input type="radio" name="correct_answer" value="false" @checked($tf === 'false')> False</label>
  </div>

  {{-- Short answer / fill in the blank --}}
  <div class="field q-block" data-for="short_answer fill_blank" style="margin-top:14px">
    <label for="{{ $prefix }}-answer">Correct answer</label>
    <textarea class="textarea" id="{{ $prefix }}-answer" name="correct_answer" placeholder="For short answer, enter one accepted answer per line." >{{ $answer }}</textarea>
    <span class="hint">Put each accepted answer on its own line. Capital letters do not matter.</span>
  </div>

  {{-- Essay --}}
  <div class="field q-block" data-for="essay" style="margin-top:14px">
    <label for="{{ $prefix }}-rubric">Notes for grading <span class="muted">(optional, only you see them)</span></label>
    <textarea class="textarea" id="{{ $prefix }}-rubric" name="rubric_text" maxlength="30000" placeholder="What a full-credit answer includes.">{{ $isOld ? old('rubric_text') : ($q->rubric_text ?? '') }}</textarea>
  </div>

  <div class="field" style="margin-top:14px">
    <label for="{{ $prefix }}-image">Picture <span class="muted">(optional)</span></label>
    @if($q && $q->image_path)
      <img class="q-image" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($q->image_path) }}" alt="Picture for question {{ $q->item_number }}">
      <label class="q-inline"><input type="checkbox" name="remove_image" value="1"> Remove this picture</label>
    @endif
    <input class="input" id="{{ $prefix }}-image" type="file" name="question_image" accept="image/png,image/jpeg,image/webp,image/gif">
  </div>

  <div class="form-foot">
    @unless($q)
      <label class="q-inline" style="margin-right:auto"><input type="checkbox" name="save_to_bank" value="1" @checked($isOld && old('save_to_bank'))> Also save it to my Question Bank for reuse</label>
    @endunless
    @isset($cancelHref)<a class="btn" href="{{ $cancelHref }}" data-cancel-edit>Cancel</a>@endisset
    <button class="btn primary" type="submit">{{ $submitLabel }}</button>
  </div>
</form>
