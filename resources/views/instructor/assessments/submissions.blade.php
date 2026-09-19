<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Assessment Submissions — DataSensei</title><style>
/* Submissions for one assessment. Colours, type and radius come from partials.design-system. */
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

.card{margin-bottom:16px;border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}
.card .table{min-width:680px}
.table .btn{min-height:32px;padding:0 12px;font-size:.8125rem}
.table td:last-child{text-align:right}
.table td:nth-child(4),.table td:nth-child(5){white-space:nowrap}

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
}
@media(prefers-reduced-motion:reduce){.btn{transition:none}}
</style>    @include('partials.page-head', ['pageTitle' => 'Assessment Submissions', 'pageDescription' => 'Create, publish, and grade assessments for your classes.'])
</head><body><div class="layout">@include('partials.instructor-sidebar')<main class="main"><div class="wrap"><div class="top"><div><h1 class="title ds-page-title">Assessment submissions</h1><p class="subtitle">{{ $assessment->title }}</p></div><a class="btn secondary" href="{{ route('instructor.assessments.builder', $assessment) }}">Back to Builder</a></div><div class="card table-wrap"><table class="table"><thead><tr><th>Student</th><th>Attempt</th><th>Status</th><th>Score</th><th>Submitted</th><th></th></tr></thead><tbody>@forelse($submissions as $submission)<tr><td>{{ $submission->student->name ?? 'Unknown' }}</td><td>{{ $submission->attempt_no }}</td><td><span class="badge">{{ ucfirst($submission->status) }}</span></td><td>{{ $submission->score }} / {{ $submission->total_points }}</td><td>{{ optional($submission->submitted_at)->format('M d, Y g:i A') ?? '—' }}</td><td><a class="btn secondary" href="{{ route('instructor.assessments.submissions.show', [$assessment,$submission]) }}">Review</a></td></tr>@empty<tr><td colspan="6" class="muted">No submissions yet.</td></tr>@endforelse</tbody></table></div>{{ $submissions->links() }}</div></main></div></body></html>