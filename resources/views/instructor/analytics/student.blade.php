@extends('instructor.layout')

@use('App\Services\Reports\ClassProgress')
@use('App\Support\Reports\ReportFormat', 'F')

@section('title', $student->name.' in '.$class->name)
@section('page_title', $student->name)
@section('page_subtitle', 'Progress in '.$class->name.($class->section ? ', '.$class->section : '').': the modules, assignments, assessments and challenges given to this class.')

@section('content')
  @include('reports.partials.styles')

  <div class="rp">
    <div>
      <a class="rp-back" href="{{ route('instructor.analytics.index', ['class_id' => $class->id, 'tab' => 'students']) }}">← Back to {{ $class->name }}</a>
      <p class="rp-meta" style="margin-top:8px">{{ $student->email }}. Last class activity: {{ $summary['last_activity'] ? F::dateTime($summary['last_activity']) : 'none yet' }}.</p>
    </div>

    <section class="rp-summary rp-summary-3" aria-label="Student summary">
      <div class="rp-tile"><span class="rp-tile-label">Overall class progress</span><strong class="rp-tile-value">{{ F::pct($summary['overall']) }}</strong></div>
      <div class="rp-tile"><span class="rp-tile-label">Modules</span><strong class="rp-tile-value">{{ $summary['modules_completed'] }} of {{ $summary['modules_total'] }}</strong><span class="rp-tile-note">Completed; {{ $summary['modules_started'] }} started</span></div>
      <div class="rp-tile"><span class="rp-tile-label">Assignments</span><strong class="rp-tile-value">{{ $summary['assignments_submitted'] }} of {{ $summary['assignments_total'] }}</strong><span class="rp-tile-note">Submitted; {{ $summary['assignments_missing'] }} missing, {{ $summary['assignments_late'] }} late</span></div>
      <div class="rp-tile"><span class="rp-tile-label">Assessment average</span><strong class="rp-tile-value">{{ F::pct($summary['assessment_average']) }}</strong><span class="rp-tile-note">{{ $summary['assessments_completed'] }} of {{ $summary['assessments_total'] }} completed</span></div>
      <div class="rp-tile"><span class="rp-tile-label">Challenge average</span><strong class="rp-tile-value">{{ F::pct($summary['challenge_average']) }}</strong><span class="rp-tile-note">{{ $summary['challenges_passed'] }} of {{ $summary['challenges_total'] }} completed</span></div>
      <div class="rp-tile"><span class="rp-tile-label">Coding challenges</span><strong class="rp-tile-value">{{ $summary['coding_completed'] }} of {{ $summary['coding_total'] }}</strong><span class="rp-tile-note">Completed; {{ $summary['coding_submissions'] }} {{ $summary['coding_submissions'] === 1 ? 'submission' : 'submissions' }}</span></div>
    </section>

    @if($summary['attention'] !== [])
      <section class="rp-panel">
        <h3 class="rp-panel-title">May need attention</h3>
        <ul class="rp-attention">
          @foreach($summary['attention'] as $key => $reason)
            <li><span>{{ $reason }}</span><span class="rp-meta">{{ ClassProgress::ATTENTION_RULES[$key] ?? '' }}</span></li>
          @endforeach
        </ul>
      </section>
    @endif

    @foreach($tables as $table)
      @include('reports.partials.table', ['table' => $table])
    @endforeach

    <section class="rp-panel" id="activity">
      <div class="rp-panel-head">
        <h3 class="rp-panel-title">Recent learning activity</h3>
        <span class="rp-count">{{ count($activity) }} shown</span>
      </div>
      <form method="GET" action="{{ route('instructor.analytics.student', ['class' => $class->id, 'student' => $student->id]) }}#activity" class="rp-filters" style="margin:12px 18px 0;border:0;padding:0;background:none">
        <div class="rp-field">
          <label for="act-type">Activity type</label>
          <select id="act-type" class="rp-input" name="type">
            <option value="">All activity</option>
            @foreach($activityTypes as $value => $label)
              <option value="{{ $value }}" @selected($filters->type === $value)>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div class="rp-field">
          <label for="act-from">From</label>
          <input id="act-from" class="rp-input" type="date" name="from" value="{{ $filters->from?->format('Y-m-d') }}">
        </div>
        <div class="rp-field">
          <label for="act-to">To</label>
          <input id="act-to" class="rp-input" type="date" name="to" value="{{ $filters->to?->format('Y-m-d') }}">
        </div>
        <div class="rp-filter-actions"><button type="submit" class="rp-btn">Apply</button></div>
      </form>
      @if($errors->any())
        <p class="rp-error" style="margin:8px 18px 0" role="alert">{{ $errors->first() }}</p>
      @endif
      <div class="rp-table-wrap">
        <table class="rp-table">
          <thead><tr><th scope="col">When</th><th scope="col">Type</th><th scope="col">Activity</th><th scope="col">What happened</th></tr></thead>
          <tbody>
            @forelse($activity as $item)
              <tr>
                <td>{{ F::dateTime($item['when']) }}</td>
                <td>{{ ['module' => 'Module', 'assignment' => 'Assignment', 'assessment' => 'Assessment', 'challenge' => 'Challenge', 'coding' => 'Coding challenge'][$item['type']] ?? ucfirst($item['type']) }}</td>
                <td>{{ $item['title'] }}</td>
                <td>{{ $item['detail'] }}</td>
              </tr>
            @empty
              <tr><td class="rp-empty" colspan="4">No activity matches these filters.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>
  </div>
@endsection
