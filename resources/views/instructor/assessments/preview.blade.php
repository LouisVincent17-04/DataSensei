<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Preview: {{ $assessment->title }} — DataSensei</title>
  @include('instructor.assessments._styles')
  <style>
    .pv-q{padding:16px 18px;border-top:1px solid var(--border)}
    .pv-q:first-child{border-top:0}
    .pv-head{display:flex;justify-content:space-between;gap:12px;color:var(--muted);font-size:.8125rem;font-weight:500}
    .pv-text{margin:6px 0 10px;color:var(--text);font-size:.9375rem;font-weight:600;line-height:1.6;white-space:pre-wrap;overflow-wrap:anywhere}
    .pv-choices{display:grid;gap:6px;margin:0;padding:0;list-style:none}
    .pv-choices label{display:flex;gap:10px;align-items:flex-start;padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);color:var(--ds-text-secondary);font-size:.875rem}
    .pv-key{display:none;margin-left:auto;color:var(--ds-success-text);font-size:.8125rem;font-weight:600;white-space:nowrap}
    .pv-answer{display:none;margin:8px 0 0;color:var(--ds-success-text);font-size:.875rem}
    .show-key .pv-key{display:inline}
    .show-key .pv-answer{display:block}
    .show-key .is-correct{border-color:var(--ds-success-border)}
    .pv-image{display:block;max-width:420px;max-height:260px;margin:0 0 10px;border:1px solid var(--border);border-radius:var(--radius-sm)}
    .toggle{display:inline-flex;align-items:center;gap:8px;color:var(--ds-text-secondary);font-size:.875rem;cursor:pointer}
  </style>
  @include('partials.page-head', ['pageTitle' => 'Assessment Preview', 'pageDescription' => 'The assessment as students see it.'])
</head>
<body>
<div class="layout">
  @include('partials.instructor-sidebar')
  <main class="main">
    <div class="wrap" id="preview-root">
      <a class="crumb" href="{{ route('instructor.assessments.builder', $assessment) }}">&larr; Back to the builder</a>
      <div class="top">
        <div>
          <h1 class="title ds-page-title">{{ $assessment->title }}</h1>
          <p class="subtitle">Preview. This is how students see the assessment. Nothing you do here is saved.</p>
        </div>
        <label class="toggle"><input type="checkbox" id="show-key"> Show the answers</label>
      </div>

      <section class="section">
        <div class="section-body">
          <dl class="facts">
            <div><dt>Class</dt><dd>{{ $assessment->classRoom->name ?? 'No class' }}</dd></div>
            <div><dt>Questions</dt><dd>{{ $assessment->questions->count() }}, {{ (int) $assessment->questions->sum('points') }} points</dd></div>
            <div><dt>Duration</dt><dd>{{ $assessment->time_limit_minutes ? $assessment->time_limit_minutes.' minutes' : 'No time limit' }}</dd></div>
            <div><dt>Due date</dt><dd>{{ $assessment->due_at?->format('M d, Y, g:i A') ?? 'No due date' }}</dd></div>
          </dl>
          @if(filled($assessment->instructions))
            <p class="pv-text" style="font-weight:400;margin-top:14px">{{ $assessment->instructions }}</p>
          @endif
        </div>
      </section>

      <section class="section">
        @forelse($assessment->questions as $question)
          <div class="pv-q">
            <div class="pv-head"><span>Question {{ $question->item_number }}</span><span>{{ $question->points }} {{ (int) $question->points === 1 ? 'point' : 'points' }}</span></div>
            <p class="pv-text">{{ filled($question->question_text) ? $question->question_text : 'This question has not been written yet.' }}</p>
            @if($question->image_path)
              <img class="pv-image" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($question->image_path) }}" alt="Picture for question {{ $question->item_number }}">
            @endif
            @switch($question->question_type)
              @case('multiple_choice')
                <ol class="pv-choices">
                  @foreach($question->options->sortBy('order_index') as $option)
                    <li><label class="{{ $option->is_correct ? 'is-correct' : '' }}"><input type="radio" name="pv{{ $question->id }}" disabled> <span>{{ chr(64 + $loop->iteration) }}. {{ $option->option_text }}</span>@if($option->is_correct)<span class="pv-key">Correct answer</span>@endif</label></li>
                  @endforeach
                </ol>
                @break
              @case('true_false')
                <ol class="pv-choices">
                  @foreach(['true' => 'True', 'false' => 'False'] as $value => $label)
                    <li><label class="{{ strtolower((string) $question->correct_answer) === $value ? 'is-correct' : '' }}"><input type="radio" name="pv{{ $question->id }}" disabled> <span>{{ $label }}</span>@if(strtolower((string) $question->correct_answer) === $value)<span class="pv-key">Correct answer</span>@endif</label></li>
                  @endforeach
                </ol>
                @break
              @case('essay')
                <textarea class="textarea" disabled placeholder="Students write their answer here."></textarea>
                @if(filled($question->rubric_text))<p class="pv-answer">Grading notes: {{ $question->rubric_text }}</p>@endif
                @break
              @default
                <input class="input" disabled placeholder="Students type their answer here.">
                <p class="pv-answer">Accepted: {{ collect(preg_split('/\r\n|\r|\n/', (string) $question->correct_answer))->map(fn ($a) => trim($a))->filter(fn ($a) => $a !== '')->join(', ') ?: 'not set' }}</p>
            @endswitch
          </div>
        @empty
          <p class="pv-q muted">There are no questions yet.</p>
        @endforelse
      </section>
    </div>
  </main>
</div>
<script>
  (function () {
    const box = document.getElementById('show-key');
    const root = document.getElementById('preview-root');
    if (box && root) box.addEventListener('change', () => root.classList.toggle('show-key', box.checked));
  })();
</script>
</body>
</html>
