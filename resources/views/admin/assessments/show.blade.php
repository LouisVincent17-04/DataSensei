@extends('admin.layout')

@section('title', $assessment->title)
@section('page_title', $assessment->title)
@section('page_subtitle', 'The assessment as instructors will give it, with every question and answer.')

@section('content')
  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading"><h2 class="panel-title">Details</h2><p class="panel-subtitle">{{ $assessment->is_active ? 'Published. Instructors can choose it.' : 'Inactive. Instructors cannot choose it.' }}</p></div>
      <div class="action-row">
        <a class="btn small secondary" href="{{ route('admin.assessments.edit', $assessment) }}">Edit</a>
        <form method="POST" action="{{ route('admin.assessments.status', $assessment) }}">@csrf @method('PATCH')<button class="btn small {{ $assessment->is_active ? 'secondary' : '' }}" type="submit">{{ $assessment->is_active ? 'Deactivate' : 'Publish' }}</button></form>
      </div>
    </div>
    <div class="panel-body">
      <dl class="as-facts">
        <div><dt>Topic</dt><dd>{{ $assessment->topic_title }}</dd></div>
        <div><dt>Type</dt><dd>{{ $assessment->assignment_type === 'mcq' ? 'Multiple choice' : ($assessment->assignment_type === 'fill_blank' ? 'Fill in the blanks' : 'Mixed') }}</dd></div>
        <div><dt>Module</dt><dd>{{ $assessment->module_no }}</dd></div>
        <div><dt>Year level</dt><dd>{{ $assessment->year_level }}</dd></div>
        <div><dt>Questions</dt><dd>{{ number_format($assessment->questions->count()) }}</dd></div>
        <div><dt>Total points</dt><dd>{{ number_format($assessment->total_points) }}</dd></div>
        <div><dt>Time limit</dt><dd>{{ $assessment->time_limit_minutes }} minutes</dd></div>
        <div><dt>Used by</dt><dd>{{ $assessment->class_assignments_count }} {{ (int) $assessment->class_assignments_count === 1 ? 'class assignment' : 'class assignments' }}</dd></div>
      </dl>
      @if($assessment->description)<p class="as-text">{{ $assessment->description }}</p>@endif
      @if($assessment->instructions)<p class="as-text"><strong>Instructions:</strong> {{ $assessment->instructions }}</p>@endif
    </div>
  </section>

  <section class="panel">
    <div class="panel-head"><div class="panel-heading"><h2 class="panel-title">Questions and answers</h2><p class="panel-subtitle">Correct choices and accepted answers are marked.</p></div></div>
    <ol class="as-questions">
      @foreach($assessment->questions as $question)
        <li>
          <div class="as-q-head"><strong>Question {{ $loop->iteration }}</strong><span>{{ $question->question_type === 'mcq' ? 'Multiple choice' : 'Fill in the blank' }}, {{ $question->points }} {{ (int) $question->points === 1 ? 'point' : 'points' }}</span></div>
          <p class="as-q-text">{{ $question->question_text }}</p>
          @if($question->question_type === 'mcq')
            <ol class="as-choices">
              @foreach($question->options as $option)
                <li class="{{ $option->is_correct ? 'is-correct' : '' }}"><b>{{ chr(64 + $loop->iteration) }}.</b> <span>{{ $option->option_text }}</span>@if($option->is_correct)<em>Correct answer</em>@endif</li>
              @endforeach
            </ol>
          @else
            <p class="as-answer">Accepted answers: @foreach($question->blankAnswers as $answer)<strong>{{ $answer->answer_text }}</strong>{{ $answer->is_case_sensitive ? ' (capital letters must match)' : '' }}@if(! $loop->last), @endif @endforeach</p>
          @endif
          @if($question->explanation)<p class="as-expl"><strong>Explanation:</strong> {{ $question->explanation }}</p>@endif
        </li>
      @endforeach
    </ol>
  </section>

  <section class="panel">
    <div class="panel-head"><div class="panel-heading"><h2 class="panel-title">Make a copy</h2><p class="panel-subtitle">Copies the details, questions and answers into a new inactive assessment you can change freely. Use it when instructors already use this one.</p></div></div>
    <form class="panel-body" method="POST" action="{{ route('admin.assessments.duplicate', $assessment) }}">
      @csrf
      <button class="btn secondary" type="submit">Make a copy</button>
    </form>
  </section>

  <section class="panel">
    <div class="panel-head"><div class="panel-heading"><h2 class="panel-title">Delete</h2><p class="panel-subtitle">An assessment instructors already use cannot be deleted; deactivate it instead.</p></div></div>
    <div class="panel-body">
      @if($hasReferences)
        <p class="dim">Instructors use this assessment, so it cannot be deleted.</p>
      @else
        <form method="POST" action="{{ route('admin.assessments.destroy', $assessment) }}" onsubmit="return confirm('Delete this assessment permanently?');">@csrf @method('DELETE')<button class="btn danger" type="submit">Delete assessment</button></form>
      @endif
    </div>
  </section>
@endsection

@push('head')
<style>
  .as-facts { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px 20px; margin:0; }
  .as-facts dt { color:var(--muted); font-size:.8125rem; font-weight:500; }
  .as-facts dd { margin:2px 0 0; color:var(--text); font-size:.9375rem; font-weight:500; overflow-wrap:anywhere; }
  .as-text { margin:14px 0 0; color:var(--ds-text-secondary); line-height:1.6; }
  .as-text strong { color:var(--text); }
  .as-questions { margin:0; padding:0; list-style:none; }
  .as-questions > li { padding:16px 20px; border-top:1px solid var(--border); }
  .as-q-head { display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; color:var(--muted); font-size:.8125rem; }
  .as-q-head strong { color:var(--text); font-size:.875rem; }
  .as-q-text { margin:6px 0 10px; color:var(--text); line-height:1.6; white-space:pre-wrap; }
  .as-choices { display:grid; gap:6px; margin:0; padding:0; list-style:none; }
  .as-choices li { display:flex; gap:8px; padding:7px 12px; border:1px solid var(--border); border-radius:var(--radius-sm); color:var(--ds-text-secondary); font-size:.875rem; }
  .as-choices li b { color:var(--muted); }
  .as-choices li.is-correct { border-color:var(--ds-success-border); color:var(--text); }
  .as-choices li em { margin-left:auto; color:var(--ds-success-text); font-style:normal; font-weight:600; font-size:.8125rem; white-space:nowrap; }
  .as-answer, .as-expl { margin:6px 0 0; color:var(--ds-text-secondary); font-size:.875rem; line-height:1.55; }
  .as-answer strong { color:var(--ds-success-text); }
  .as-expl strong { color:var(--text); }
  @media (max-width:800px) { .as-facts { grid-template-columns:repeat(2,minmax(0,1fr)); } }
</style>
@endpush
