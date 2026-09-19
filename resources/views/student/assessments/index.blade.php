<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Assessments — DataSensei</title><style>
/* Student assessment list. Colours, type and radius come from partials.design-system. */
:root{--good:var(--ds-success);--warn:var(--ds-warning);--bad:var(--ds-danger)}
*{box-sizing:border-box}
body{margin:0;font-family:var(--ds-font-sans);background:var(--bg);color:var(--text)}
.layout{display:flex;min-height:100vh}
.main{flex:1;min-width:0;padding:28px 32px 48px}
.wrap{max-width:1480px;margin:0 auto}

/* page header */
.top{display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px;margin-bottom:24px}
.top > div{min-width:0;flex:1 1 320px}
.subtitle{margin:4px 0 0;max-width:72ch;color:var(--muted);font-size:.875rem;line-height:1.55}

/* assessment cards */
.grid{display:grid;gap:16px}
.grid-3{grid-template-columns:repeat(3,minmax(0,1fr))}
.card{min-width:0;display:flex;flex-direction:column;align-items:flex-start;gap:8px;padding:20px;
  background:var(--surface);border:1px solid var(--border);border-radius:var(--radius)}
.card h3{margin:4px 0 0;font-size:.9375rem;font-weight:600;line-height:1.4;overflow-wrap:anywhere}
.card p{margin:0 0 8px;font-size:.8125rem;line-height:1.5}
.card .btn{margin-top:auto}
.card.empty{grid-column:1/-1;align-items:center;padding:32px 20px;text-align:center}
.card.empty p{margin:0;font-size:.875rem}
.muted{color:var(--muted)}

.card .badge{display:inline-flex;align-items:center;padding:2px 8px;border:1px solid var(--ds-border-strong);border-radius:var(--radius-xs);
  background:var(--surface2);color:var(--ds-text-secondary);font-size:.75rem;font-weight:600;line-height:1.4;white-space:nowrap}

.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:38px;padding:0 16px;
  border:1px solid var(--accent);border-radius:var(--radius-sm);background:var(--accent);color:#fff;
  font-size:.875rem;font-weight:500;line-height:1.2;text-decoration:none;white-space:nowrap;cursor:pointer;
  transition:background .12s ease,border-color .12s ease}
.btn:hover{background:var(--accent-hover);border-color:var(--accent-hover)}

.wrap > .admin-pagination{margin-top:20px}

@media(max-width:1100px){.grid-3{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:900px){.main{padding:24px 20px 40px}}
@media(max-width:640px){
  .main{padding:20px 16px 32px}
  .grid-3{grid-template-columns:minmax(0,1fr)}
  .card{padding:16px}
}
@media(prefers-reduced-motion:reduce){.btn{transition:none}}
</style>    @include('partials.page-head', ['pageTitle' => 'Assessments', 'pageDescription' => 'Take assessments set by your instructor and review your results.'])
</head><body><div class="layout">@include('partials.sidebar')<main class="main"><div class="wrap"><div class="top"><div><h1 class="title ds-page-title">Assessments</h1><p class="subtitle">Instructor-created assessment forms based on the Table of Specifications.</p></div></div><div class="grid grid-3">@forelse($assessments as $assessment)<div class="card"><span class="badge">{{ ucfirst($assessment->status) }}</span><h3>{{ $assessment->title }}</h3><p class="muted">{{ $assessment->classRoom->name ?? 'Class' }}, {{ $assessment->total_items }} items, {{ $assessment->total_points }} points</p><a class="btn" href="{{ route('student.assessments.show',$assessment) }}">Open</a></div>@empty<div class="card empty"><p class="muted">No assessments are available.</p></div>@endforelse</div>{{ $assessments->links() }}</div></main></div></body></html>