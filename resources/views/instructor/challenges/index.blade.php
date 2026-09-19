<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Challenge Pool — DataSensei</title>
<style>
    /* Instructor challenge pool. Colours, type and radius come from partials.design-system. */
    *{box-sizing:border-box}
    body{margin:0;background:var(--bg);color:var(--text);font-family:var(--ds-font-sans)}
    .layout{display:flex;min-height:100vh}
    .main{flex:1;min-width:0;padding:28px 32px 48px}

    /* page header */
    .top{display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:16px;margin-bottom:24px}
    .top > div:first-child{min-width:0;flex:1 1 320px}
    .subtitle{max-width:72ch;margin:4px 0 0;color:var(--muted);font-size:.875rem;line-height:1.55}

    .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:38px;padding:0 16px;
      border:1px solid var(--accent);border-radius:var(--radius-sm);background:var(--accent);color:#fff;
      font:500 .875rem/1.2 var(--ds-font-sans);text-decoration:none;white-space:nowrap;cursor:pointer;
      transition:background .12s ease,border-color .12s ease}
    .btn:hover{border-color:var(--accent-hover);background:var(--accent-hover)}
    .btn.secondary{border-color:var(--ds-border-strong);background:var(--surface2);color:var(--text)}
    .btn.secondary:hover{background:var(--ds-surface-hover)}
    .muted{color:var(--muted)}
    .btn.small{min-height:32px;padding:0 12px;font-size:.8125rem}

    .card{margin-bottom:16px;padding:16px 20px;border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}

    /* filter row */
    .form-row{display:flex;gap:8px 12px;flex-wrap:wrap;align-items:center}
    /* the category scope is information, not a control: plain text */
    .input,select{min-width:180px;min-height:38px;padding:8px 12px;border:1px solid var(--ds-input-border);border-radius:var(--radius-sm);
      background:var(--surface3);color:var(--text);font:400 .875rem/1.4 var(--ds-font-sans);outline:none;
      transition:border-color .12s ease,box-shadow .12s ease}
    .input:focus,select:focus{border-color:var(--accent);box-shadow:var(--ds-focus-ring)}

    /* table card: rows run edge to edge */
    .card.table-wrap{padding:0;overflow-x:auto}
    .table{width:100%;min-width:760px;border-collapse:collapse}
    .table th{padding:10px 14px;border-bottom:1px solid var(--border);background:var(--surface3);color:var(--muted);
      font-size:.75rem;font-weight:600;text-align:left;white-space:nowrap}
    .table td{padding:12px 14px;border-bottom:1px solid var(--border);color:var(--ds-text-secondary);font-size:.875rem;
      line-height:1.45;vertical-align:middle}
    .table tbody tr:last-child td{border-bottom:0}
    .table tbody tr:hover td{background:rgba(255,255,255,.02)}
    .table td:nth-child(4),.table td:nth-child(5){font-variant-numeric:tabular-nums}
    .challenge-title{color:var(--text);font-weight:600;line-height:1.45}
    .challenge-meta{display:block;margin-top:2px;color:var(--muted);font-size:.8125rem}

    .badge{display:inline-flex;align-items:center;padding:2px 8px;border:1px solid var(--ds-border-strong);border-radius:var(--radius-xs);
      background:var(--surface2);color:var(--ds-text-secondary);font-size:.75rem;font-weight:600;line-height:1.4;white-space:nowrap}
    .badge.good{border-color:var(--ds-success-border);background:var(--ds-success-soft);color:var(--ds-success-text)}
    .badge.warn{border-color:var(--ds-warning-border);background:var(--ds-warning-soft);color:var(--ds-warning-text)}
    .form-row .badge,.form-row .badge.good{padding:0;border:0;background:none;color:var(--muted);font-size:.8125rem;font-weight:500;margin-right:4px}
    .empty{padding:32px 20px;color:var(--muted);font-size:.875rem;line-height:1.5;text-align:center}
    .table tbody tr:hover td[colspan]{background:none}

    /* pagination: same control as the shared pagination */
    .pager{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;padding:14px 20px;border-top:1px solid var(--border)}
    .pager-info{color:var(--muted);font-size:.8125rem;font-variant-numeric:tabular-nums}
    .pager-controls{display:flex;align-items:center;gap:6px;flex-wrap:wrap}
    .page-link{display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 8px;
      border:1px solid var(--ds-border-strong);border-radius:var(--radius-sm);background:var(--surface2);color:var(--ds-text-secondary);
      font-size:.8125rem;font-weight:500;font-variant-numeric:tabular-nums;text-decoration:none;transition:background .12s ease,color .12s ease}
    a.page-link:hover{background:var(--ds-surface-hover);color:var(--text)}
    .page-link.current{border-color:var(--accent);background:var(--accent);color:#fff;pointer-events:none}
    .page-link.disabled{opacity:.45;pointer-events:none}
    .page-gap{display:inline-flex;align-items:center;justify-content:center;min-width:20px;color:var(--muted)}

    @media(max-width:900px){.main{padding:24px 20px 40px}}
    @media(max-width:640px){
      .main{padding:20px 16px 32px}
      .card{padding:16px}
      .card.table-wrap{padding:0}
      .top{align-items:stretch;flex-direction:column}
      .top > div:first-child{flex:0 0 auto}
      .form-row > *{width:100%}
      .form-row .badge{width:auto}
      .input,select{min-width:0}
      .pager{align-items:flex-start;flex-direction:column;padding:12px 16px}
      .table td[colspan] .empty{padding:24px 0;text-align:left}
    }
    @media(prefers-reduced-motion:reduce){.btn,.page-link,.input,select{transition:none}}
  </style>
    @include('partials.page-head', ['pageTitle' => 'Challenge Pool', 'pageDescription' => 'Browse and manage the coding and quiz challenge pool.'])
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
                <span class="challenge-meta">{{ $challenge->version_name ?: 'Version '.$challenge->version_no }}, {{ $challenge->version_code ?: 'V'.$challenge->version_no }}</span>
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
