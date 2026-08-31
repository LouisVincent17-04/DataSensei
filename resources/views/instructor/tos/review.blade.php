<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $tos->title }} — Review TOS</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  @include('instructor.tos.partials.styles')
</head>
<body>
<div class="layout">
  @include('partials.instructor-sidebar')
  <main class="main">
    <div class="wrap">
      <div class="top">
        <div>
          <div class="kicker">Step 3 of 3</div>
          <h1 class="title ds-page-title">Review & Generate</h1>
          <p class="subtitle">Confirm the assessment blueprint before moving to the question-writing stage.</p>
        </div>
        <a class="btn secondary" href="{{ route('instructor.tos.show', $tos) }}">← Edit TOS</a>
      </div>

      @include('instructor.tos.partials.wizard', ['currentStep' => 3])

      @if(session('success'))<div class="alert">{{ session('success') }}</div>@endif
      @if($errors->any())<div class="alert error">{{ $errors->first() }}</div>@endif

      @php($statusClass = $status['code'] === 'complete' ? 'good' : ($status['code'] === 'invalid' ? 'bad' : 'warn'))
      <div class="status-panel {{ $statusClass }}">
        <div>
          <strong>{{ $status['label'] }}</strong>
          <div class="muted">{{ $status['message'] }}</div>
        </div>
        <span class="badge {{ $statusClass }}">{{ $tos->rows->sum('item_count') }} / {{ $tos->total_items ?: $tos->rows->sum('item_count') }} items</span>
      </div>

      <div class="grid grid-2" style="margin-top:16px">
        <section class="card">
          <div class="kicker">Assessment Blueprint</div>
          <h2>{{ $tos->title }}</h2>
          <div class="review-row"><span>Subject / Course</span><strong>{{ $tos->classRoom->name ?? 'Template / no class' }}</strong></div>
          <div class="review-row"><span>Coverage</span><strong>{{ $tos->coverage_label }}{{ $module ? ' — '.$module->title : '' }}</strong></div>
          <div class="review-row"><span>Total Number of Items</span><strong>{{ $tos->total_items ?: $tos->rows->sum('item_count') }}</strong></div>
          <div class="review-row"><span>Blueprint Status</span><span class="badge {{ $statusClass }}">{{ $status['label'] }}</span></div>
        </section>

        <section class="card">
          <div class="kicker">Explain this TOS</div>
          <h2>Plain-language summary</h2>
          <p class="muted" style="line-height:1.75">{{ $explanation }}</p>
          <div class="callout">This explanation summarizes the blueprint only. It does not generate, grade, or change any question.</div>
        </section>
      </div>

      @if($isModernBlueprint)
        <section class="card">
          <div class="section-title">
            <div><div class="kicker">Coverage</div><h2>Learning Competencies</h2></div>
          </div>
          <div class="table-wrap">
            <table class="table">
              <thead><tr><th>Learning Competency</th><th>Weight</th><th>Assigned Items</th></tr></thead>
              <tbody>
              @foreach($matrix['groups'] as $group)
                <tr>
                  <td><strong>{{ $group['title'] }}</strong>@if($group['objective'])<div class="muted small" style="margin-top:5px">{{ $group['objective'] }}</div>@endif</td>
                  <td>{{ $group['weight'] }}%</td>
                  <td>{{ $group['total'] }}</td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        </section>

        <section class="card">
          <div class="section-title">
            <div><div class="kicker">Cognitive Distribution</div><h2>How students will be challenged</h2></div>
          </div>
          <div class="grid grid-2">
            @foreach($cognitiveLevels as $slug => $definition)
              @php($count = $matrix['column_totals'][$slug] ?? 0)
              @php($percent = $matrix['target_items'] > 0 ? round(($count / $matrix['target_items']) * 100) : 0)
              <div class="metric">
                <strong>{{ $definition['label'] }} — {{ $percent }}%</strong>
                <span>{{ $count }} item(s) · {{ $definition['explanation'] }}</span>
              </div>
            @endforeach
          </div>
        </section>
      @else
        <section class="card">
          <div class="section-title"><div><div class="kicker">Legacy Blueprint</div><h2>Existing Allocations</h2></div></div>
          <div class="table-wrap">
            <table class="table">
              <thead><tr><th>Topic</th><th>Difficulty</th><th>Cognitive Level</th><th>Items</th></tr></thead>
              <tbody>
              @foreach($tos->rows as $row)
                <tr><td>{{ $row->topic_title }}</td><td>{{ ucwords(str_replace('-', ' ', $row->difficulty_slug)) }}</td><td>{{ $row->cognitive_level ?: '—' }}</td><td>{{ $row->item_count }}</td></tr>
              @endforeach
              </tbody>
            </table>
          </div>
        </section>
      @endif

      <section class="card">
        <div class="kicker">Questions</div>
        <h2>Question writing stays separate</h2>
        <p class="muted" style="line-height:1.7">The TOS decides what the assessment should contain. The existing DataSensei Question Builder remains responsible for the actual question text, question type, choices, correct answers, explanations, images, and rubrics.</p>
        <div class="callout">When you generate the assessment, DataSensei creates the numbered item structure from this TOS. You then write or configure each question in the Question Builder.</div>
      </section>

      <div class="footer-actions">
        <a class="btn secondary" href="{{ route('instructor.tos.show', $tos) }}">← Edit TOS</a>
        @if($status['can_generate'])
          <a class="btn good" href="{{ route('instructor.assessments.create', $tos) }}">Generate Assessment →</a>
        @else
          <div class="actions">
            <span class="muted small">Complete the item distribution before generating an assessment.</span>
            <a class="btn warn" href="{{ route('instructor.tos.show', $tos) }}">Fix Distribution</a>
          </div>
        @endif
      </div>
    </div>
  </main>
</div>
</body>
</html>
