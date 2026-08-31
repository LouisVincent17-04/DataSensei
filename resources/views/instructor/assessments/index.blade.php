<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Assessments — DataSensei</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#0d1320;--surface:#111c2d;--surface2:#1a2638;--surface3:#0f1928;--border:#263854;--text:#f8fafc;--muted:#91a4bf;--dim:#68809f;--accent:#3b82f6;--good:#10b981;--warn:#f59e0b;--bad:#ef4444;--radius:16px}
    *{box-sizing:border-box}
    body{margin:0;font-family:Inter,Arial,sans-serif;background:var(--bg);color:var(--text)}
    .layout{display:flex;min-height:100vh}.main{flex:1;padding:28px;min-width:0}.wrap{max-width:1480px;margin:0 auto}
    .top{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;margin-bottom:22px}.title{font-size:2rem;font-weight:900;margin:0}.subtitle{color:var(--muted);line-height:1.6;margin-top:8px}
    .grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:18px;display:flex;flex-direction:column}.card h3{margin:16px 0 8px}.card-actions{margin-top:auto;padding-top:15px}
    .actions{display:flex;gap:9px;flex-wrap:wrap;align-items:center}.between{justify-content:space-between}.btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;border:1px solid transparent;border-radius:10px;padding:10px 14px;font-weight:800;text-decoration:none;cursor:pointer;background:var(--accent);color:#fff}.btn.secondary{background:var(--surface2);border-color:var(--border);color:var(--text)}
    .badge{display:inline-flex;padding:5px 10px;border-radius:999px;border:1px solid var(--border);background:var(--surface2);font-size:.74rem;font-weight:800}.badge.draft{color:#fde68a;border-color:rgba(245,158,11,.45);background:rgba(245,158,11,.10)}.badge.published{color:#a7f3d0;border-color:rgba(16,185,129,.45);background:rgba(16,185,129,.10)}
    .muted{color:var(--muted)}.small{font-size:.82rem}.draft-note{padding:10px 12px;border-radius:10px;border:1px solid rgba(245,158,11,.3);background:rgba(245,158,11,.07);color:#fde68a;margin-top:13px;line-height:1.5}
    .alert{padding:13px 15px;border-radius:12px;margin-bottom:16px;border:1px solid rgba(16,185,129,.35);background:rgba(16,185,129,.10);color:#a7f3d0}
    .pagination{margin-top:18px}
    @media(max-width:1050px){.grid{grid-template-columns:repeat(2,1fr)}}@media(max-width:700px){.layout{display:block}.main{padding:18px}.top{flex-direction:column}.grid{grid-template-columns:1fr}}
  </style>
</head>
<body>
<div class="layout">
  @include('partials.instructor-sidebar')
  <main class="main">
    <div class="wrap">
      <div class="top">
        <div>
          <h1 class="title ds-page-title">Assessments</h1>
          <p class="subtitle">Create TOS-guided assessments, save unfinished quizzes as drafts, and resume from the last item at any time.</p>
        </div>
        <a class="btn secondary" href="{{ route('instructor.tos.index') }}">Choose a TOS</a>
      </div>

      @if(session('success'))<div class="alert">{{ session('success') }}</div>@endif

      <div class="grid">
        @forelse($assessments as $assessment)
          <article class="card">
            <div class="actions between">
              <span class="badge {{ $assessment->status }}">{{ strtoupper($assessment->status) }}</span>
              <span class="muted small">{{ $assessment->questions_count }} items</span>
            </div>
            <h3>{{ $assessment->title }}</h3>
            <p class="muted">{{ $assessment->classRoom->name ?? 'No class' }} · {{ $assessment->total_points }} points · {{ $assessment->submissions_count }} submissions</p>

            @if($assessment->status === 'draft')
              <div class="draft-note">
                <strong>Saved draft</strong><br>
                @if($assessment->draft_saved_at)
                  Last saved {{ $assessment->draft_saved_at->diffForHumans() }}
                  @if($assessment->draft_last_item) · Resume at item {{ $assessment->draft_last_item }} @endif
                @else
                  Ready to continue authoring.
                @endif
              </div>
            @endif

            <div class="actions card-actions">
              <a class="btn" href="{{ route('instructor.assessments.builder', ['assessment' => $assessment, 'item' => $assessment->status === 'draft' ? $assessment->draft_last_item : null]) }}">
                {{ $assessment->status === 'draft' ? 'Continue Draft' : 'View Builder' }}
              </a>
              <a class="btn secondary" href="{{ route('instructor.assessments.analytics', $assessment) }}">Diagnostics</a>
            </div>
          </article>
        @empty
          <div class="card">
            <p class="muted">No assessments yet. Open a TOS and click Create Assessment Form.</p>
          </div>
        @endforelse
      </div>
      <div class="pagination">{{ $assessments->links() }}</div>
    </div>
  </main>
</div>
</body>
</html>
