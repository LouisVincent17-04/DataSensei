<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Table of Specifications — DataSensei</title>
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
          <div class="kicker">Assessment Blueprint</div>
          <h1 class="title ds-page-title">Table of Specifications</h1>
          <p class="subtitle">Plan what an assessment should cover first. Write the actual questions only after the blueprint is ready.</p>
        </div>
        <a class="btn" href="{{ route('instructor.tos.create') }}">+ Create TOS</a>
      </div>

      @if(session('success'))<div class="alert">{{ session('success') }}</div>@endif
      @if(session('error'))<div class="alert error">{{ session('error') }}</div>@endif

      <div class="card">
        <div class="section-title">
          <div>
            <h3>Your assessment blueprints</h3>
            <div class="muted small">Guidance first: Create TOS → Set Distribution → Review & Generate.</div>
          </div>
        </div>

        <div class="table-wrap">
          <table class="table">
            <thead>
              <tr>
                <th>Assessment</th>
                <th>Subject / Course</th>
                <th>Coverage</th>
                <th>Items</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
            @forelse($tosList as $tos)
              @php($summary = $tos->blueprint_status)
              <tr>
                <td>
                  <strong>{{ $tos->title }}</strong>
                  @if($tos->assessments_count > 0)
                    <div class="muted small">Linked to {{ $tos->assessments_count }} assessment(s)</div>
                  @endif
                </td>
                <td>{{ $tos->classRoom->name ?? 'Template / no class' }}</td>
                <td>{{ $tos->coverage_label }}</td>
                <td>{{ (int) $tos->assigned_items }} / {{ (int) ($tos->total_items ?: $tos->assigned_items) }}</td>
                <td>
                  <span class="badge {{ $summary['code'] === 'complete' ? 'good' : ($summary['code'] === 'invalid' ? 'bad' : 'warn') }}">
                    {{ $summary['label'] }}
                  </span>
                </td>
                <td>
                  <div class="actions">
                    <a class="btn secondary" href="{{ route('instructor.tos.show', $tos) }}">Open</a>
                    <a class="btn secondary" href="{{ route('instructor.tos.review', $tos) }}">Review</a>
                    @if($tos->assessments_count === 0)
                      <form method="POST" action="{{ route('instructor.tos.destroy', $tos) }}" onsubmit="return confirm('Delete this Table of Specification? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button class="btn bad" type="submit">Delete</button>
                      </form>
                    @else
                      <span class="badge">Protected</span>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6">
                  <div style="padding:30px 10px;text-align:center">
                    <strong>No TOS yet.</strong>
                    <p class="muted">Create your first assessment blueprint using the 3-step wizard.</p>
                    <a class="btn" href="{{ route('instructor.tos.create') }}">Create TOS</a>
                  </div>
                </td>
              </tr>
            @endforelse
            </tbody>
          </table>
        </div>
        <div class="pagination">{{ $tosList->links() }}</div>
      </div>
    </div>
  </main>
</div>
</body>
</html>
