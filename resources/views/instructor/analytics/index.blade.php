@extends('instructor.layout')

@use('App\Services\Reports\ClassProgress')
@use('App\Support\Reports\PerformanceBands')
@use('App\Support\Reports\ReportFormat', 'F')

@section('title', 'Class Analytics & At-Risk')
@section('page_title', 'Class Analytics & At-Risk')
@section('page_subtitle', 'Choose a class to see how it is progressing, how students perform (Low, Moderate or High) and which students are at risk, with the reason for each. Everything is read from the class\'s saved work when you open the page.')

@section('content')
  @include('reports.partials.styles')

  <div class="rp">
    <form method="GET" action="{{ route('instructor.analytics.index') }}" class="rp-filters">
      <div class="rp-field rp-field-wide">
        <label for="an-class">Class</label>
        <select id="an-class" class="rp-input" name="class_id" onchange="this.form.submit()">
          <option value="">Choose a class</option>
          @foreach($classes as $option)
            <option value="{{ $option->id }}" @selected($class && $class->id === $option->id)>{{ $option->name }}{{ $option->section ? ', '.$option->section : '' }}{{ $option->is_archived ? ' (archived)' : '' }}</option>
          @endforeach
        </select>
      </div>
      <div class="rp-filter-actions"><button type="submit" class="rp-btn">Open class</button></div>
    </form>

    @if($class === null)
      @if($classes->isEmpty())
        <div class="rp-panel"><div class="rp-panel-body">You have no classes yet. Create a class and enrol students, then come back here.</div></div>
      @else
        <div class="rp-classes">
          @foreach($classes as $option)
            @php $o = $overviews[$option->id]; @endphp
            <a class="rp-class" href="{{ route('instructor.analytics.index', ['class_id' => $option->id]) }}">
              <strong>{{ $option->name }}{{ $option->section ? ', '.$option->section : '' }}</strong>
              @if($option->is_archived)<span class="rp-meta">Archived</span>@endif
              <dl>
                <dt>Students</dt><dd>{{ $o['students'] }}</dd>
                <dt>Active in the last {{ ClassProgress::ACTIVE_DAYS }} days</dt><dd>{{ $o['active'] }}</dd>
                <dt>Module completion</dt><dd>{{ F::pct($o['module_completion']) }}</dd>
                <dt>Assessment average</dt><dd>{{ F::pct($o['assessment_average']) }}</dd>
                <dt>Low / Moderate / High</dt><dd>{{ $o['performance'][PerformanceBands::LOW] }} / {{ $o['performance'][PerformanceBands::MODERATE] }} / {{ $o['performance'][PerformanceBands::HIGH] }}</dd>
                <dt>At risk (may need attention)</dt><dd>{{ $o['attention'] }}</dd>
              </dl>
            </a>
          @endforeach
        </div>
      @endif
    @else
      @php
        $o = $overview;
        $base = ['class_id' => $class->id];
      @endphp

      <nav class="rp-nav" aria-label="Class Analytics">
        @foreach($tabs as $key => $label)
          <a href="{{ route('instructor.analytics.index', $base + ['tab' => $key]) }}" class="rp-nav-link {{ $tab === $key ? 'is-current' : '' }}" @if($tab === $key) aria-current="page" @endif>{{ $label }}</a>
        @endforeach
      </nav>

      @if($class->is_archived)
        <p class="rp-meta">This class is archived. Its saved work can still be reviewed.</p>
      @endif

      {{-- Only the filters that apply to this tab. --}}
      @if(in_array($tab, ['students', 'modules', 'assessments', 'challenges', 'coding'], true))
        <form method="GET" action="{{ route('instructor.analytics.index') }}" class="rp-filters">
          <input type="hidden" name="class_id" value="{{ $class->id }}">
          <input type="hidden" name="tab" value="{{ $tab }}">
          @if($tab === 'students')
            <div class="rp-field rp-field-wide">
              <label for="an-q">Student</label>
              <input id="an-q" class="rp-input" type="search" name="q" value="{{ $filters->search }}" maxlength="100" placeholder="Name or email">
            </div>
            <div class="rp-field">
              <label for="an-status">Show</label>
              <select id="an-status" class="rp-input" name="status">
                <option value="">All students</option>
                <option value="attention" @selected($filters->status === 'attention')>Only at-risk students (may need attention)</option>
                @foreach([PerformanceBands::LOW, PerformanceBands::MODERATE, PerformanceBands::HIGH, PerformanceBands::NOT_GRADED] as $group)
                  <option value="{{ $group }}" @selected($filters->status === $group)>{{ $group === PerformanceBands::NOT_GRADED ? 'Not yet graded' : PerformanceBands::label($group).' performance' }}</option>
                @endforeach
              </select>
            </div>
          @elseif($tab === 'modules')
            <div class="rp-field rp-field-wide">
              <label for="an-module">Module</label>
              <select id="an-module" class="rp-input" name="module_id">
                <option value="">All assigned modules</option>
                @foreach($content['moduleChoices'] as $id => $title)
                  <option value="{{ $id }}" @selected($filters->moduleId === (int) $id)>{{ $title }}</option>
                @endforeach
              </select>
            </div>
          @else
            @if($tab === 'assessments')
              <div class="rp-field">
                <label for="an-status">Status</label>
                <select id="an-status" class="rp-input" name="status">
                  <option value="">Open and closed</option>
                  <option value="open" @selected($filters->status === 'open')>Open</option>
                  <option value="closed" @selected($filters->status === 'closed')>Closed</option>
                </select>
              </div>
            @endif
            <div class="rp-field">
              <label for="an-from">Due from</label>
              <input id="an-from" class="rp-input" type="date" name="from" value="{{ $filters->from?->format('Y-m-d') }}">
            </div>
            <div class="rp-field">
              <label for="an-to">Due to</label>
              <input id="an-to" class="rp-input" type="date" name="to" value="{{ $filters->to?->format('Y-m-d') }}">
            </div>
          @endif
          <div class="rp-filter-actions">
            <button type="submit" class="rp-btn">Apply</button>
            @if(array_diff_key($filters->query(), ['class_id' => true]) !== [])
              <a class="rp-btn rp-btn-secondary" href="{{ route('instructor.analytics.index', $base + ['tab' => $tab]) }}">Clear</a>
            @endif
          </div>
        </form>
      @endif

      @if($errors->any())
        <p class="rp-error" role="alert">{{ $errors->first() }}</p>
      @endif

      @if($tab === 'overview')
        <section class="rp-summary rp-summary-4" aria-label="Class summary">
          <div class="rp-tile"><span class="rp-tile-label">Enrolled students</span><strong class="rp-tile-value">{{ F::number($o['students']) }}</strong></div>
          <div class="rp-tile"><span class="rp-tile-label">Active students</span><strong class="rp-tile-value">{{ F::number($o['active']) }}</strong><span class="rp-tile-note">Class activity in the last {{ ClassProgress::ACTIVE_DAYS }} days</span></div>
          <div class="rp-tile"><span class="rp-tile-label">Assigned modules</span><strong class="rp-tile-value">{{ F::number($o['modules']) }}</strong></div>
          <div class="rp-tile"><span class="rp-tile-label">Module completion</span><strong class="rp-tile-value">{{ F::pct($o['module_completion']) }}</strong></div>
          {{-- Assignments were merged into assessments; converted rows are already counted here. --}}
          <div class="rp-tile"><span class="rp-tile-label">Assessment performance</span><strong class="rp-tile-value">{{ F::pct($o['assessment_average']) }}</strong><span class="rp-tile-note">Average best score, {{ F::pct($o['assessment_completion']) }} completed, {{ $o['assessments_missing'] }} missing</span></div>
          <div class="rp-tile"><span class="rp-tile-label">Challenge performance</span><strong class="rp-tile-value">{{ F::pct($o['challenge_completion']) }}</strong><span class="rp-tile-note">Completed, average best score {{ F::pct($o['challenge_average']) }}</span></div>
          <div class="rp-tile"><span class="rp-tile-label">Coding challenge performance</span><strong class="rp-tile-value">{{ F::pct($o['coding_completion']) }}</strong><span class="rp-tile-note">Completed, {{ F::pct($o['coding_score']) }} of tests passed</span></div>
        </section>

        {{-- Class Analytics and At-Risk in one place (DataSensei Updates 12). --}}
        <div class="rp-split">
          @include('reports.partials.bars', ['group' => $content['performance']])

          <section class="rp-panel" id="attention">
            <div class="rp-panel-head">
              <h3 class="rp-panel-title" id="at-risk">At-risk students</h3>
              <span class="rp-count">{{ count($content['attention']) }} of {{ $o['students'] }}</span>
            </div>
            <p class="rp-note">Students who may need attention. A student is listed only when one of these rules is true for their saved work; each reason is shown next to their name:</p>
            <ul class="rp-rules">
              @foreach(ClassProgress::attentionRules() as $rule)
                <li>{{ $rule }}</li>
              @endforeach
            </ul>
            @if($content['attention'] === [])
              <div class="rp-panel-body">No student matches these rules right now.</div>
            @else
              <ul class="rp-attention">
                @foreach($content['attention'] as $row)
                  <li>
                    <div><a class="rp-link" href="{{ $row['url'] }}">{{ $row['name'] }}</a><div class="rp-meta">{{ $row['average'] !== null ? $row['group'].' performance, average '.F::pct($row['average']) : 'No graded assessment yet' }}</div></div>
                    <span>{{ implode('; ', $row['reasons']) }}</span>
                  </li>
                @endforeach
              </ul>
            @endif
          </section>
        </div>

        @if($content['bars'] !== [])
          <div class="rp-split">
            @foreach($content['bars'] as $group)
              @include('reports.partials.bars', ['group' => $group])
            @endforeach
          </div>
        @endif
      @else
        @foreach($content['bars'] ?? [] as $group)
          @include('reports.partials.bars', ['group' => $group])
        @endforeach
        @include('reports.partials.table', ['table' => $content['table']])
        @if(! empty($content['detail']))
          @include('reports.partials.table', ['table' => $content['detail']])
        @endif
      @endif
    @endif
  </div>
@endsection
