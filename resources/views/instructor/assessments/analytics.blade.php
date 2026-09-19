<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Assessment Diagnostics — DataSensei</title><style>
/* Assessment learning diagnostics. Colours, type and radius come from partials.design-system. */
*{box-sizing:border-box}
body{margin:0;background:var(--bg);color:var(--text);font-family:var(--ds-font-sans)}
.layout{display:flex;min-height:100vh}
.main{flex:1;min-width:0;padding:28px 32px 48px}
.wrap{max-width:1480px;margin:0 auto}

/* page header */
.top{display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px;margin-bottom:24px}
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

/* one card per breakdown: title row, then the table edge to edge */
.card{margin-bottom:16px;overflow:hidden;border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}
.card > h2{margin:0;padding:14px 20px;border-bottom:1px solid var(--border);font-size:.9375rem;font-weight:600;line-height:1.35}
.card .table{min-width:640px}
.card .table td:first-child{min-width:180px}
.card .table th:first-child,.card .table td:first-child{padding-left:20px}

/* tables */
.table-wrap{overflow-x:auto}
.table{width:100%;border-collapse:collapse}
.table th{padding:10px 14px;border-bottom:1px solid var(--border);background:var(--surface3);color:var(--muted);
  font-size:.75rem;font-weight:600;text-align:left;white-space:nowrap}
.table td{padding:12px 14px;border-bottom:1px solid var(--border);color:var(--ds-text-secondary);font-size:.875rem;
  line-height:1.45;vertical-align:middle}
.table tbody tr:last-child td{border-bottom:0}
.table tbody tr:hover td{background:rgba(255,255,255,.02)}
.table strong{color:var(--text);font-weight:600}
.table td.muted[colspan]{padding:32px 20px;color:var(--muted);text-align:center}
.badge{display:inline-flex;align-items:center;padding:2px 8px;border:1px solid var(--ds-border-strong);border-radius:var(--radius-xs);
  background:var(--surface2);color:var(--ds-text-secondary);font-size:.75rem;font-weight:600;line-height:1.4;white-space:nowrap}

@media(max-width:900px){.main{padding:24px 20px 40px}}
@media(max-width:640px){
  .main{padding:20px 16px 32px}
  .top{align-items:stretch;flex-direction:column}
  .top > div:first-child{flex:0 0 auto}
  .top > .btn{width:100%}
  .table td.muted[colspan]{text-align:left}
  .card > h2{padding:12px 16px}
  .card .table th:first-child,.card .table td:first-child{padding-left:16px}
}
@media(prefers-reduced-motion:reduce){.btn{transition:none}}
</style>    @include('partials.page-head', ['pageTitle' => 'Assessment Diagnostics', 'pageDescription' => 'Create, publish, and grade assessments for your classes.'])
</head><body><div class="layout">@include('partials.instructor-sidebar')<main class="main"><div class="wrap"><div class="top"><div><h1 class="title ds-page-title">Learning diagnostics</h1><p class="subtitle">{{ $assessment->title }}. This page identifies where students are weak by topic, learning objective, and Bloom's Taxonomy level.</p></div><a class="btn secondary" href="{{ route('instructor.assessments.builder', $assessment) }}">Back to Builder</a></div>@foreach([['Topic',$byTopic],['Learning Objective',$byObjective],["Bloom's Taxonomy",$byBloom]] as [$heading,$rows])<div class="card"><h2>{{ $heading }}</h2><div class="table-wrap"><table class="table"><thead><tr><th>{{ $heading }}</th><th>Students</th><th>Average Mastery</th><th>Needs Support</th><th>Pending Review</th></tr></thead><tbody>@forelse($rows as $row)<tr><td>{{ $row['label'] }}</td><td>{{ $row['students'] }}</td><td><strong>{{ $row['average_mastery'] }}%</strong></td><td>{{ $row['needs_support'] }}</td><td>{{ $row['pending_review'] }}</td></tr>@empty<tr><td colspan="5" class="muted">No diagnostic data yet.</td></tr>@endforelse</tbody></table></div></div>@endforeach<div class="card"><h2>Per-student diagnostic records</h2><div class="table-wrap"><table class="table"><thead><tr><th>Student</th><th>Topic</th><th>Objective</th><th>Bloom</th><th>Mastery</th><th>Interpretation</th></tr></thead><tbody>@forelse($diagnostics as $row)<tr><td>{{ $row->student->name ?? 'Unknown' }}</td><td>{{ $row->topic_title }}</td><td>{{ $row->learning_objective ?: '—' }}</td><td>{{ $row->bloom_level ?: '—' }}</td><td>{{ $row->mastery_percent }}%</td><td><span class="badge">{{ $row->proficiency_label }}</span></td></tr>@empty<tr><td colspan="6" class="muted">No diagnostic data yet.</td></tr>@endforelse</tbody></table></div></div></div></main></div></body></html>