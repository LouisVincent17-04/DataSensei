<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Challenge Pool — DataSensei</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#0d1320;--surface:#111c2d;--surface2:#1a2638;--border:#1e2f47;--text:#fafafa;--muted:#7f93b0;--accent:#3b82f6;--good:#10b981;--warn:#f59e0b;--bad:#ef4444;--radius:14px;--radius-sm:8px}
    *{box-sizing:border-box}
    body{margin:0;font-family:Inter,Arial,sans-serif;background:var(--bg);color:var(--text)}
    .layout{display:flex;min-height:100vh}
    .main{flex:1;padding:32px;min-width:0;background:radial-gradient(circle at top right,rgba(59,130,246,.10),transparent 40%),var(--bg)}
    .top{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;margin-bottom:24px}
    .title{font-size:1.8rem;font-weight:800;margin:0}
    .subtitle{color:var(--muted);margin-top:8px;line-height:1.6}
    .card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:20px;margin-bottom:18px}
    .table-wrap{overflow-x:auto}
    .table{width:100%;border-collapse:collapse;min-width:820px}
    .table th,.table td{padding:14px 12px;border-bottom:1px solid var(--border);text-align:left;vertical-align:middle}
    .table th{color:var(--muted);font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap}
    .table tbody tr:hover{background:rgba(59,130,246,.04)}
    .challenge-title{font-weight:700;color:var(--text);line-height:1.45}
    .challenge-meta{display:block;color:var(--muted);font-size:.78rem;margin-top:4px}
    .badge{display:inline-flex;align-items:center;padding:4px 8px;border-radius:999px;font-size:.75rem;font-weight:700;border:1px solid var(--border);background:var(--surface2);color:var(--text)}
    .badge.good{color:var(--good);border-color:rgba(16,185,129,.35);background:rgba(16,185,129,.08)}
    .badge.warn{color:var(--warn);border-color:rgba(245,158,11,.35);background:rgba(245,158,11,.08)}
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:10px 14px;border-radius:var(--radius-sm);background:var(--accent);color:white;text-decoration:none;border:0;font-weight:700;cursor:pointer;font-family:inherit;font-size:.88rem;white-space:nowrap}
    .btn:hover{filter:brightness(1.08)}
    .btn.secondary{background:var(--surface2);border:1px solid var(--border);color:var(--text)}
    .btn.small{padding:8px 11px;font-size:.8rem}
    .form-row{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
    .input,select{background:var(--bg);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:var(--radius-sm);font:inherit;min-width:180px}
    .muted{color:var(--muted)}
    .empty{padding:34px 16px;text-align:center;color:var(--muted)}
    .pager{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-top:20px;padding-top:18px;border-top:1px solid var(--border)}
    .pager-info{color:var(--muted);font-size:.85rem}
    .pager-controls{display:flex;align-items:center;gap:6px;flex-wrap:wrap}
    .page-link{display:inline-flex;align-items:center;justify-content:center;min-width:38px;height:38px;padding:0 11px;border:1px solid var(--border);border-radius:8px;background:var(--surface2);color:var(--text);text-decoration:none;font-size:.85rem;font-weight:700}
    .page-link:hover{border-color:var(--accent);color:#bfdbfe}
    .page-link.current{background:var(--accent);border-color:var(--accent);color:white;pointer-events:none}
    .page-link.disabled{opacity:.42;pointer-events:none}
    .page-gap{display:inline-flex;align-items:center;justify-content:center;min-width:26px;color:var(--muted)}
    @media(max-width:1000px){.main{padding:20px}}
    @media(max-width:700px){.layout{display:block}.main{padding:18px}.top{display:block}.form-row>*{width:100%}.input,select{min-width:0}.pager{align-items:flex-start}.pager-controls{width:100%}.page-link{min-width:36px}}
  </style>
</head>
<body>
<div class="layout">
  @include('partials.instructor-sidebar')

  <main class="main">
    <div class="top">
      <div>
        <h1 class="title ds-page-title">Challenge Pool</h1>
        <p class="subtitle">Review only the University Student MCQ and coding challenges used for university-level instruction. Other learner paths are not accessible from the instructor portal.</p>
      </div>
    </div>

    <div class="card">
      <form method="GET" class="form-row" action="{{ route('instructor.challenges.index') }}">
        <span class="badge good" aria-label="Accessible challenge category">
          {{ $universityCategory?->name ?? 'University Student' }} only
        </span>

        <select name="type" aria-label="Filter by challenge type">
          <option value="">All types</option>
          <option value="mcq" @selected(request('type') === 'mcq')>MCQ</option>
          <option value="coding" @selected(request('type') === 'coding')>Coding</option>
        </select>

        <button class="btn" type="submit">Apply Filters</button>

        @if(request()->filled('type'))
          <a class="btn secondary" href="{{ route('instructor.challenges.index') }}">Clear</a>
        @endif
      </form>
    </div>

    <div class="card table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th>Title</th>
            <th>Category</th>
            <th>Type</th>
            <th>Items</th>
            <th>XP</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        @forelse($challenges as $challenge)
          <tr>
            <td>
              <span class="challenge-title">{{ $challenge->title }}</span>
              @if($challenge->version_name || $challenge->version_code)
                <span class="challenge-meta">{{ $challenge->version_name ?: 'Version '.$challenge->version_no }} · {{ $challenge->version_code ?: 'V'.$challenge->version_no }}</span>
              @endif
            </td>
            <td>{{ $challenge->category->name ?? '—' }}</td>
            <td>
              <span class="badge {{ $challenge->is_coding_challenge ? 'warn' : 'good' }}">
                {{ $challenge->is_coding_challenge ? 'Coding' : 'MCQ' }}
              </span>
            </td>
            <td>{{ $challenge->is_coding_challenge ? $challenge->coding_questions_count : $challenge->questions_count }}</td>
            <td>{{ number_format($challenge->base_xp) }}</td>
            <td>
              @if(!$challenge->is_coding_challenge)
                <a class="btn secondary small" href="{{ route('instructor.challenges.show', array_merge(['challenge' => $challenge], request()->only(['type', 'page']))) }}">View Content</a>
              @else
                <span class="muted">Developer-managed</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6">
              <div class="empty">No active University Student challenges match the selected filters.</div>
            </td>
          </tr>
        @endforelse
        </tbody>
      </table>

      @if($challenges->hasPages())
        @php
          $current = $challenges->currentPage();
          $last = $challenges->lastPage();
          $start = max(1, $current - 2);
          $end = min($last, $current + 2);
        @endphp

        <nav class="pager" aria-label="Challenge pool pagination">
          <div class="pager-info">
            Showing {{ number_format($challenges->firstItem()) }}–{{ number_format($challenges->lastItem()) }} of {{ number_format($challenges->total()) }} challenges
          </div>

          <div class="pager-controls">
            @if($challenges->onFirstPage())
              <span class="page-link disabled" aria-disabled="true">Previous</span>
            @else
              <a class="page-link" href="{{ $challenges->previousPageUrl() }}" rel="prev">Previous</a>
            @endif

            @if($start > 1)
              <a class="page-link" href="{{ $challenges->url(1) }}">1</a>
              @if($start > 2)<span class="page-gap">…</span>@endif
            @endif

            @for($page = $start; $page <= $end; $page++)
              @if($page === $current)
                <span class="page-link current" aria-current="page">{{ $page }}</span>
              @else
                <a class="page-link" href="{{ $challenges->url($page) }}">{{ $page }}</a>
              @endif
            @endfor

            @if($end < $last)
              @if($end < $last - 1)<span class="page-gap">…</span>@endif
              <a class="page-link" href="{{ $challenges->url($last) }}">{{ $last }}</a>
            @endif

            @if($challenges->hasMorePages())
              <a class="page-link" href="{{ $challenges->nextPageUrl() }}" rel="next">Next</a>
            @else
              <span class="page-link disabled" aria-disabled="true">Next</span>
            @endif
          </div>
        </nav>
      @elseif($challenges->total() > 0)
        <div class="pager" style="justify-content:flex-start">
          <div class="pager-info">Showing all {{ number_format($challenges->total()) }} challenges</div>
        </div>
      @endif
    </div>
  </main>
</div>
</body>
</html>
