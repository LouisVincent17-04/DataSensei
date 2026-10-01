<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Assessments — DataSensei</title>
  @include('instructor.assessments._styles')
  <style>
    .filter{display:flex;align-items:flex-end;gap:10px;flex-wrap:wrap;padding:12px 18px;border-bottom:1px solid var(--border)}
    .filter .field{min-width:180px}
    .table td.actions-cell{white-space:nowrap;text-align:right}
    .pagination{padding:12px 18px}
    .empty-list{padding:24px 18px;color:var(--muted);font-size:.875rem}
    @media(max-width:760px){
      .table thead{display:none}
      .table,.table tbody,.table tr,.table td{display:block;width:100%}
      .table tr{padding:10px 14px;border-top:1px solid var(--border)}
      .table td{padding:2px 0;border:0}
      .table td[data-label]::before{content:attr(data-label) ": ";color:var(--muted)}
      .table td.actions-cell{text-align:left;padding-top:8px}
    }
  </style>
  @include('partials.page-head', ['pageTitle' => 'Assessments', 'pageDescription' => 'Create, publish, and grade assessments for your classes.'])
</head>
<body>
<div class="layout">
  @include('partials.instructor-sidebar')
  <main class="main">
    <div class="wrap wide">
      <div class="top">
        <div>
          <h1 class="title ds-page-title">Assessments</h1>
          <p class="subtitle">Quizzes and exams for your classes. Create one, add the questions, preview it, then publish it.</p>
        </div>
        <div class="actions">
          <a class="btn" href="{{ route('instructor.tos.index') }}">Plan with a Table of Specifications</a>
          <a class="btn primary" href="{{ route('instructor.assessments.new') }}">Create assessment</a>
        </div>
      </div>

      @if(session('success'))<div class="alert success" role="status">{{ session('success') }}</div>@endif

      <section class="section">
        <form class="filter" method="GET" action="{{ route('instructor.assessments.index') }}">
          <div class="field">
            <label for="status">Status</label>
            <select class="select" id="status" name="status">
              <option value="">All</option>
              @foreach(['draft' => 'Draft', 'published' => 'Published', 'closed' => 'Closed'] as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <button class="btn" type="submit">Filter</button>
        </form>

        @if($assessments->isEmpty())
          <p class="empty-list">No assessments yet. Choose Create assessment to make your first one.</p>
        @else
          <table class="table">
            <thead>
              <tr><th>Assessment</th><th>Class</th><th>Questions</th><th>Due</th><th>Status</th><th>Submissions</th><th></th></tr>
            </thead>
            <tbody>
              @foreach($assessments as $assessment)
                <tr>
                  <td>
                    <strong>{{ $assessment->title }}</strong>
                    @if($assessment->status === 'draft' && $assessment->draft_saved_at)
                      <span class="sub">Draft saved {{ $assessment->draft_saved_at->diffForHumans() }}</span>
                    @endif
                  </td>
                  <td data-label="Class">{{ $assessment->classRoom->name ?? 'No class' }}</td>
                  <td data-label="Questions">{{ $assessment->questions_count }}, {{ (int) $assessment->total_points }} points</td>
                  <td data-label="Due">{{ $assessment->due_at?->format('M d, Y, g:i A') ?? 'No due date' }}</td>
                  <td data-label="Status"><span class="{{ $assessment->status === 'published' ? 'state-good' : ($assessment->status === 'draft' ? 'state-warn' : '') }}">{{ ucfirst($assessment->status) }}</span></td>
                  <td data-label="Submissions">{{ $assessment->submissions_count }}</td>
                  <td class="actions-cell">
                    <div class="actions" style="justify-content:flex-end">
                      <a class="btn small {{ $assessment->status === 'draft' ? 'primary' : '' }}" href="{{ route('instructor.assessments.builder', $assessment) }}">{{ $assessment->status === 'draft' ? 'Continue editing' : 'Open' }}</a>
                      <a class="btn small" href="{{ route('instructor.assessments.preview', $assessment) }}">Preview</a>
                      <a class="btn small" href="{{ route('instructor.assessments.submissions', $assessment) }}">Submissions</a>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
          <div class="pagination">{{ $assessments->links() }}</div>
        @endif
      </section>
    </div>
  </main>
</div>
</body>
</html>
