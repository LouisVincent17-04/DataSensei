@extends('admin.layout')

@section('title', $assessment->title)
@section('page_title', $assessment->title)
@section('page_subtitle', 'Review assessment metadata, questions, answer keys, version controls, and publication status.')

@section('content')
  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading"><h2 class="panel-title">{{ $assessment->version_name }}</h2><p class="panel-subtitle">{{ $assessment->assignment_code }}, {{ $assessment->version_code }}, Module {{ $assessment->module_no }}</p></div>
      <div class="action-row">
        <span class="badge {{ $assessment->is_active ? 'active' : 'disabled' }}">{{ $assessment->is_active ? 'Published' : 'Inactive' }}</span>
        <a class="btn small" href="{{ route('admin.assessments.edit', $assessment) }}">Edit</a>
        <form method="POST" action="{{ route('admin.assessments.status', $assessment) }}">@csrf @method('PATCH')<button class="btn small {{ $assessment->is_active ? 'secondary' : 'green' }}" type="submit">{{ $assessment->is_active ? 'Deactivate' : 'Publish' }}</button></form>
      </div>
    </div>
    <div class="panel-body">
      <div class="grid-2">
        <div><p><strong>Topic:</strong> {{ $assessment->topic_title }}</p><p><strong>Type:</strong> {{ $assessment->type_label }}</p><p><strong>Year level:</strong> {{ $assessment->year_level }}</p></div>
        <div><p><strong>Questions:</strong> {{ number_format($assessment->questions->count()) }}</p><p><strong>Total points:</strong> {{ number_format($assessment->total_points) }}</p><p><strong>Class references:</strong> {{ number_format($assessment->class_assignments_count) }}</p></div>
      </div>
      @if($assessment->description)<p style="margin-top:16px;color:var(--muted);line-height:1.65">{{ $assessment->description }}</p>@endif
      @if($assessment->instructions)<p style="margin-top:12px;color:var(--muted);line-height:1.65"><strong style="color:var(--text)">Instructions:</strong> {{ $assessment->instructions }}</p>@endif
    </div>
  </section>

  <section class="panel">
    <div class="panel-head"><div class="panel-heading"><h2 class="panel-title">Questions and Answer Keys</h2><p class="panel-subtitle">Correct options and accepted blank answers are shown for content review.</p></div></div>
    <div class="management-list">
      @foreach($assessment->questions as $question)
        <details class="management-item" @if($loop->first) open @endif>
          <summary>
            <div class="management-main"><strong>Question {{ $loop->iteration }}</strong><span>{{ Str::limit($question->question_text, 120) }}</span></div>
            <div class="management-metric"><strong>{{ $question->type_label }}</strong>{{ $question->points }} point(s)</div>
            <div class="summary-status"><span class="badge info">Order {{ $question->order_index }}</span></div>
            <span class="management-toggle">View</span>
          </summary>
          <div class="management-editor">
            <p style="color:var(--text);line-height:1.65">{{ $question->question_text }}</p>
            <div style="margin-top:8px">
              <strong>ILO evidence:</strong>
              @forelse($question->iloMappings as $mapping)
                <span class="badge info">{{ $mapping->ilo?->ilo_code ?? ('ILO #' . $mapping->ilo_id) }}</span>
              @empty
                <span class="dim">Not mapped — this item does not contribute to ILO mastery.</span>
              @endforelse
            </div>
            @if($question->question_type === 'mcq')
              <ol style="margin:16px 0 0 24px;color:var(--muted);line-height:1.8">@foreach($question->options as $option)<li style="color:{{ $option->is_correct ? '#a7f3d0' : 'var(--muted)' }}">{{ $option->option_text }} @if($option->is_correct)<strong> — Correct</strong>@endif</li>@endforeach</ol>
            @else
              <div style="margin-top:16px"><strong>Accepted answers:</strong><ul style="margin:8px 0 0 24px;color:var(--muted);line-height:1.8">@foreach($question->blankAnswers as $answer)<li>{{ $answer->answer_text }} {{ $answer->is_case_sensitive ? '(case-sensitive)' : '(case-insensitive)' }}</li>@endforeach</ul></div>
            @endif
            @if($question->explanation)<p style="margin-top:16px;color:var(--muted)"><strong style="color:var(--text)">Explanation:</strong> {{ $question->explanation }}</p>@endif
          </div>
        </details>
      @endforeach
    </div>
  </section>

  <section class="panel">
    <div class="panel-head"><div class="panel-heading"><h2 class="panel-title">Create a New Version</h2><p class="panel-subtitle">Copies all metadata, questions, answer choices, and accepted answers into a separate version.</p></div></div>
    <form class="panel-body" method="POST" action="{{ route('admin.assessments.duplicate', $assessment) }}">
      @csrf
      <div class="form-grid three">
        <div class="field"><label for="duplicate-code">New Assignment Code</label><input id="duplicate-code" class="input" name="assignment_code" value="{{ $assessment->assignment_code }}-V{{ $nextVersionNo }}" required></div>
        <div class="field"><label for="duplicate-version-no">Version Number</label><input id="duplicate-version-no" class="input" type="number" name="version_no" min="1" value="{{ $nextVersionNo }}" required></div>
        <div class="field"><label for="duplicate-version-code">Version Code</label><input id="duplicate-version-code" class="input" name="version_code" value="V{{ $nextVersionNo }}" required></div>
        <div class="field"><label for="duplicate-version-name">Version Name</label><input id="duplicate-version-name" class="input" name="version_name" value="Version {{ $nextVersionNo }}" required></div>
        <div class="field"><label for="duplicate-status">Initial Status</label><select id="duplicate-status" class="select" name="is_active"><option value="0">Inactive / Draft</option><option value="1">Published</option></select></div>
      </div>
      <div class="action-row" style="margin-top:16px"><button class="btn" type="submit">Duplicate Version</button></div>
    </form>
  </section>

  <section class="panel">
    <div class="panel-head"><div class="panel-heading"><h2 class="panel-title">Delete Version</h2><p class="panel-subtitle">Assessment versions already used by instructors are preserved and may only be deactivated.</p></div></div>
    <div class="panel-body">
      @if($hasReferences)
        <p class="dim">This assessment has class-assignment references and cannot be deleted. Deactivate it if it should no longer be selectable.</p>
      @else
        <form method="POST" action="{{ route('admin.assessments.destroy', $assessment) }}" onsubmit="return confirm('Permanently delete this assessment version?');">@csrf @method('DELETE')<button class="btn danger" type="submit">Delete Assessment Version</button></form>
      @endif
    </div>
  </section>
@endsection
