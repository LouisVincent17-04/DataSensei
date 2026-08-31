@extends('admin.layout')

@section('title', $challenge->title)
@section('eyebrow', 'MCQ Challenge')
@section('page_title', $challenge->title)
@section('page_subtitle', 'Review challenge settings, questions, version controls, and publication status.')

@section('content')
  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">{{ $challenge->version_name }}</h2>
        <p class="panel-subtitle">{{ $challenge->content_code }} · {{ $challenge->version_code }} · {{ $challenge->category?->name }}</p>
      </div>
      <div class="action-row">
        <span class="badge {{ $challenge->is_active ? 'active' : 'disabled' }}">{{ $challenge->is_active ? 'Published' : 'Inactive' }}</span>
        <a class="btn small" href="{{ route('admin.challenges.edit', $challenge) }}">Edit</a>
        <form method="POST" action="{{ route('admin.challenges.status', $challenge) }}">
          @csrf
          @method('PATCH')
          <button class="btn small {{ $challenge->is_active ? 'secondary' : 'green' }}" type="submit">{{ $challenge->is_active ? 'Deactivate' : 'Publish' }}</button>
        </form>
      </div>
    </div>
    <div class="panel-body">
      <div class="grid-2">
        <div>
          <p><strong>Time limit:</strong> {{ number_format($challenge->time_limit_seconds) }} seconds</p>
          <p><strong>Base XP:</strong> {{ number_format($challenge->base_xp) }}</p>
          <p><strong>Map order:</strong> {{ number_format($challenge->order_index) }}</p>
        </div>
        <div>
          <p><strong>Questions:</strong> {{ number_format($challenge->questions->count()) }}</p>
          <p><strong>Attempt records:</strong> {{ number_format($challenge->attempts_count) }}</p>
          <p><strong>Version:</strong> {{ $challenge->version_no }}</p>
        </div>
      </div>
      @if($challenge->description)
        <p style="margin-top:16px;color:var(--muted);line-height:1.65">{{ $challenge->description }}</p>
      @endif
    </div>
  </section>

  <section class="panel" style="margin-top:24px">
    <div class="panel-head"><div class="panel-heading"><h2 class="panel-title">Questions and Correct Answers</h2><p class="panel-subtitle">The correct answer is marked for content-review purposes.</p></div></div>
    <div class="management-list">
      @foreach($challenge->questions as $question)
        <details class="management-item" @if($loop->first) open @endif>
          <summary>
            <div class="management-main"><strong>Question {{ $loop->iteration }}</strong><span>{{ Str::limit($question->question_text, 120) }}</span></div>
            <div class="management-metric"><strong>{{ $question->options->count() }} choices</strong>Order {{ $question->order_index }}</div>
            <div class="summary-status"><span class="badge info">MCQ</span></div>
            <span class="management-toggle">View</span>
          </summary>
          <div class="management-editor">
            <p style="color:var(--text);line-height:1.65">{{ $question->question_text }}</p>
            <ol style="margin:14px 0 0 22px;color:var(--muted);line-height:1.8">
              @foreach($question->options as $option)
                <li style="color:{{ $option->is_correct ? '#a7f3d0' : 'var(--muted)' }}">{{ $option->option_text }} @if($option->is_correct)<strong> — Correct</strong>@endif</li>
              @endforeach
            </ol>
          </div>
        </details>
      @endforeach
    </div>
  </section>

  <section class="panel" style="margin-top:24px">
    <div class="panel-head"><div class="panel-heading"><h2 class="panel-title">Create a New Version</h2><p class="panel-subtitle">Copies the current settings, questions, and answer choices into a separate version.</p></div></div>
    <form class="panel-body" method="POST" action="{{ route('admin.challenges.duplicate', $challenge) }}">
      @csrf
      <div class="form-grid three">
        <div class="field"><label for="duplicate-version-no">Version Number</label><input id="duplicate-version-no" class="input" type="number" name="version_no" min="1" value="{{ $nextVersionNo }}" required></div>
        <div class="field"><label for="duplicate-version-code">Version Code</label><input id="duplicate-version-code" class="input" name="version_code" value="V{{ $nextVersionNo }}" required></div>
        <div class="field"><label for="duplicate-version-name">Version Name</label><input id="duplicate-version-name" class="input" name="version_name" value="Version {{ $nextVersionNo }}" required></div>
        <div class="field"><label for="duplicate-status">Initial Status</label><select id="duplicate-status" class="select" name="is_active"><option value="0">Inactive / Draft</option><option value="1">Published</option></select></div>
      </div>
      <div class="action-row" style="margin-top:14px"><button class="btn" type="submit">Duplicate Version</button></div>
    </form>
  </section>

  <section class="panel" style="margin-top:24px">
    <div class="panel-head"><div class="panel-heading"><h2 class="panel-title">Delete Version</h2><p class="panel-subtitle">Challenges with attempt history are preserved and may only be deactivated.</p></div></div>
    <div class="panel-body">
      @if($hasHistory)
        <p class="dim">This challenge has attempt history and cannot be deleted. Deactivate it if it should no longer be available.</p>
      @else
        <form method="POST" action="{{ route('admin.challenges.destroy', $challenge) }}" onsubmit="return confirm('Permanently delete this MCQ challenge version?');">
          @csrf
          @method('DELETE')
          <button class="btn danger" type="submit">Delete Challenge Version</button>
        </form>
      @endif
    </div>
  </section>
@endsection
