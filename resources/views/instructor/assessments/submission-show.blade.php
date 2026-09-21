<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Grade Submission — DataSensei</title><style>
/* Grade one assessment submission. Colours, type and radius come from partials.design-system. */
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

/* the form's only submit button is its primary action */
.btn.good{border-color:var(--accent);background:var(--accent);color:#fff}
.btn.good:hover{border-color:var(--accent-hover);background:var(--accent-hover)}
.actions{display:flex;gap:8px;flex-wrap:wrap}

.alert{margin-bottom:16px;padding:12px 16px;border:1px solid var(--ds-success-border);border-radius:var(--radius-sm);
  background:var(--ds-success-soft);color:#d1fae5;font-size:.875rem;line-height:1.5}
.alert.error{border-color:var(--ds-danger-border);background:var(--ds-danger-soft);color:#fee2e2}

/* one card per answer */
.card{margin-bottom:16px;padding:20px;border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}
.card > h3{margin:0 0 8px;font-size:.9375rem;font-weight:600;line-height:1.4}
.card > p{margin:0 0 12px;color:var(--ds-text-secondary);font-size:.875rem;line-height:1.6;overflow-wrap:anywhere}
.card > p.muted{margin:12px 0 0;color:var(--muted);font-size:.8125rem;font-variant-numeric:tabular-nums}
.meta{padding:12px 14px;border-radius:var(--radius-sm);background:var(--surface3);color:var(--ds-text-secondary);
  font-size:.875rem;line-height:1.55;white-space:pre-wrap;overflow-wrap:anywhere}
.meta b{display:block;margin-bottom:4px;color:var(--muted);font-size:.75rem;font-weight:500;white-space:normal}

/* grading fields */
.field label{display:block;margin-bottom:6px;color:var(--ds-text-secondary);font-size:.8125rem;font-weight:500}
.input,.textarea{width:100%;min-height:38px;padding:8px 12px;border:1px solid var(--ds-input-border);border-radius:var(--radius-sm);
  background:var(--surface3);color:var(--text);font:400 .875rem/1.4 var(--ds-font-sans);outline:none;
  transition:border-color .12s ease,box-shadow .12s ease}
.input:focus,.textarea:focus{border-color:var(--accent);box-shadow:var(--ds-focus-ring)}
.input[type="number"]{max-width:200px;font-variant-numeric:tabular-nums}
.textarea{min-height:100px;resize:vertical;line-height:1.55}

@media(max-width:900px){.main{padding:24px 20px 40px}}
@media(max-width:640px){
  .main{padding:20px 16px 32px}
  .top{align-items:stretch;flex-direction:column}
  .top > div:first-child{flex:0 0 auto}
  .top > .btn{width:100%}
  .card{padding:16px}
  .actions .btn{width:100%;white-space:normal;text-align:center}
}
@media(prefers-reduced-motion:reduce){.btn{transition:none}}
</style>    @include('partials.page-head', ['pageTitle' => 'Grade Submission', 'pageDescription' => 'Create, publish, and grade assessments for your classes.'])
</head><body><div class="layout">@include('partials.instructor-sidebar')<main class="main"><div class="wrap"><div class="top"><div><h1 class="title ds-page-title">{{ $submission->student->name }}</h1><p class="subtitle">{{ $assessment->title }}, attempt {{ $submission->attempt_no }}</p></div><a class="btn secondary" href="{{ route('instructor.assessments.submissions', $assessment) }}">Back</a></div>@if(session('success'))<div class="alert">{{ session('success') }}</div>@endif @if($errors->any())<div class="alert error">{{ $errors->first() }}</div>@endif<form method="POST" action="{{ route('instructor.assessments.submissions.grade', [$assessment,$submission]) }}">@csrf @method('PATCH')@foreach($submission->answers->sortBy(fn($a)=>$a->question->item_number) as $answer)<div class="card"><h3>Item {{ $answer->question->item_number }} — {{ $answer->question->type_label }}</h3><p>{{ $answer->question->question_text }}</p><div class="meta"><b>Student answer</b>{{ $answer->selectedOption->option_text ?? (trim((string) $answer->answer_text) !== '' ? $answer->answer_text : 'No answer') }}</div>@if($answer->question->question_type==='essay')<div class="field" style="margin-top:16px"><label>Score (max {{ $answer->question->points }})</label><input class="input" type="number" step="0.01" min="0" max="{{ $answer->question->points }}" name="scores[{{ $answer->id }}]" value="{{ old('scores.'.$answer->id, $answer->is_correct === null ? '' : $answer->points_awarded) }}"></div><div class="field" style="margin-top:16px"><label>Item Feedback</label><textarea class="textarea" name="feedbacks[{{ $answer->id }}]">{{ old('feedbacks.'.$answer->id, $answer->instructor_feedback) }}</textarea></div><div class="meta" style="margin-top:16px"><b>Rubric</b>{{ $answer->question->rubric_text }}</div>@else<p class="muted">Auto-graded: {{ $answer->is_correct ? 'Correct' : 'Incorrect' }}, {{ $answer->points_awarded }}/{{ $answer->question->points }}</p>@endif</div>@endforeach<div class="card"><div class="field"><label>Overall Feedback</label><textarea class="textarea" name="feedback">{{ old('feedback', $submission->feedback) }}</textarea></div><div class="actions" style="justify-content:flex-end;margin-top:16px"><button class="btn good" type="submit">Save Grade and Recalculate Diagnostics</button></div></div></form></div></main></div></body></html>
