@extends('admin.layout')

@section('title', $challenge->title)
@section('page_title', $challenge->title)
@section('page_subtitle', 'Review the challenge settings, its problems and their test cases, and its availability to learners.')

@section('content')
  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">{{ $challenge->version_name }}</h2>
        <p class="panel-subtitle">{{ $challenge->content_code }}, {{ $challenge->version_code }}, {{ $challenge->category?->name ?? 'Uncategorized' }}</p>
      </div>
      <div class="action-row">
        <span class="dim">{{ $challenge->is_active ? 'Available' : 'Unavailable' }}</span>
        <a class="btn small" href="{{ route('admin.coding-challenges.edit', $challenge) }}">Edit</a>
        <form method="POST" action="{{ route('admin.coding-challenges.status', $challenge) }}">
          @csrf
          @method('PATCH')
          <button class="btn small {{ $challenge->is_active ? 'secondary' : 'green' }}" type="submit">{{ $challenge->is_active ? 'Make Unavailable' : 'Publish' }}</button>
        </form>
      </div>
    </div>
    <div class="panel-body">
      <div class="grid-2">
        <div>
          <p><strong>Time limit:</strong> {{ number_format($challenge->time_limit_seconds) }} seconds</p>
          <p><strong>Base XP:</strong> {{ number_format($challenge->base_xp) }}</p>
          <p><strong>Map order:</strong> {{ number_format($challenge->order_index) }}</p>
          @if($challenge->module_id)
            <p><strong>Module:</strong> {{ $challenge->module?->title ?? 'Module ' . $challenge->module_id }}</p>
          @endif
        </div>
        <div>
          <p><strong>Problems:</strong> {{ number_format($challenge->codingQuestions->count()) }}</p>
          <p><strong>Submissions:</strong> {{ number_format($submissionCount) }}</p>
          <p><strong>Version:</strong> {{ $challenge->version_no }}</p>
        </div>
      </div>
      @if($challenge->description)
        <p style="margin-top:16px;color:var(--muted);line-height:1.65;white-space:pre-wrap">{{ $challenge->description }}</p>
      @endif
    </div>
  </section>

  <section class="panel">
    <div class="panel-head"><div class="panel-heading"><h2 class="panel-title">Problems and Test Cases</h2><p class="panel-subtitle">Hidden test cases are graded but never shown to learners. Reference solutions are shown here for review only.</p></div></div>
    <div class="management-list">
      @forelse($challenge->codingQuestions as $question)
        <details class="management-item" @if($loop->first) open @endif>
          <summary>
            <div class="management-main"><strong>Problem {{ $loop->iteration }}</strong><span>{{ Str::limit($question->title ?: $question->problem_description, 120) }}</span></div>
            <div class="management-metric"><strong>{{ $question->testCases->count() }} test cases</strong>{{ $question->testCases->where('is_hidden', true)->count() }} hidden</div>
            <div class="summary-status"><span class="dim">{{ ucfirst($question->language) }}, {{ number_format($question->time_limit_seconds) }} sec, {{ number_format($question->base_xp) }} XP</span></div>
            <span class="management-toggle">View</span>
          </summary>
          <div class="management-editor">
            @if($question->title)
              <p style="color:var(--text);font-weight:600;margin-bottom:8px">{{ $question->title }}</p>
            @endif
            <p style="color:var(--text);line-height:1.65;white-space:pre-wrap">{{ $question->problem_description }}</p>

            @if(filled($question->starter_code))
              <p class="dim" style="margin-top:16px">Starter code</p>
              <pre class="cc-show-code">{{ $question->starter_code }}</pre>
            @endif

            @if(filled($question->reference_solution))
              <p class="dim" style="margin-top:16px">Reference solution (not shown to learners)</p>
              <pre class="cc-show-code">{{ $question->reference_solution }}</pre>
            @endif

            <p class="dim" style="margin-top:16px">Test cases</p>
            <div class="cc-show-cases">
              @foreach($question->testCases as $case)
                <div class="cc-show-case">
                  <div class="cc-show-case-head">
                    <strong>Test case {{ $loop->iteration }}</strong>
                    <span class="dim">{{ $case->is_hidden ? 'hidden' : 'visible to learners' }}</span>
                  </div>
                  <div class="cc-show-case-grid">
                    <div>
                      <span class="dim">Input</span>
                      <pre class="cc-show-code">{{ $case->input ?? '' }}</pre>
                    </div>
                    <div>
                      <span class="dim">Expected output</span>
                      <pre class="cc-show-code">{{ $case->expected_output }}</pre>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </details>
      @empty
        <p class="empty-cell">This challenge has no problems yet.</p>
      @endforelse
    </div>
  </section>

  <section class="panel">
    <div class="panel-head"><div class="panel-heading"><h2 class="panel-title">Delete Version</h2><p class="panel-subtitle">Challenges with submission history are preserved and may only be made unavailable.</p></div></div>
    <div class="panel-body">
      @if($hasHistory)
        <p class="dim">This challenge has submission history and cannot be deleted. Make it unavailable if learners should no longer see it.</p>
      @else
        <form method="POST" action="{{ route('admin.coding-challenges.destroy', $challenge) }}" onsubmit="return confirm('Permanently delete this coding challenge version, its problems and test cases?');">
          @csrf
          @method('DELETE')
          <button class="btn danger" type="submit">Delete Challenge Version</button>
        </form>
      @endif
    </div>
  </section>
@endsection

@push('head')
<style>
  .cc-show-code { margin:6px 0 0; padding:10px 12px; border:1px solid var(--border); border-radius:var(--radius-sm); background:var(--surface3); color:var(--text); font-family:var(--ds-font-mono, ui-monospace, SFMono-Regular, Menlo, Consolas, monospace); font-size:.8125rem; line-height:1.55; white-space:pre-wrap; overflow-wrap:anywhere; max-height:320px; overflow:auto; }
  .cc-show-cases { display:grid; gap:10px; margin-top:8px; }
  .cc-show-case { padding:12px; border:1px solid var(--border); border-radius:var(--radius-sm); background:var(--surface2); }
  .cc-show-case-head { display:flex; gap:12px; align-items:baseline; margin-bottom:8px; color:var(--text); }
  .cc-show-case-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; }
  @media (max-width:700px) { .cc-show-case-grid { grid-template-columns:minmax(0,1fr); } }
</style>
@endpush
