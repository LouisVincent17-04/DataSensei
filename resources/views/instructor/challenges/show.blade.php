<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $challenge->title }} — Challenge Pool</title>
<style>
    /* Read-only challenge content and answer key. Colours, type and radius come from partials.design-system. */
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

    .card{margin-bottom:16px;padding:20px;border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}

    /* challenge facts: flat insets, label above value */
    .meta-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px}
    .meta{min-width:0;padding:12px 16px;border-radius:var(--radius-sm);background:var(--surface3)}
    .meta-label{display:block;margin-bottom:4px;color:var(--muted);font-size:.8125rem;font-weight:500}
    .meta-value{display:block;font-size:1rem;font-weight:600;line-height:1.4;font-variant-numeric:tabular-nums;overflow-wrap:anywhere}

    .section-title{margin:0;font-size:1rem;font-weight:600;line-height:1.35}
    .section-note{margin:4px 0 0;color:var(--muted);font-size:.875rem;line-height:1.55}

    /* questions: rows separated by rules, running edge to edge in the card */
    .questions{display:grid;margin:16px -20px -20px;border-top:1px solid var(--border)}
    .question{border-bottom:1px solid var(--border)}
    .question:last-child{border-bottom:0}
    .question summary{list-style:none;cursor:pointer;padding:14px 20px;display:flex;align-items:flex-start;justify-content:space-between;gap:16px;
      transition:background .12s ease}
    .question summary::-webkit-details-marker{display:none}
    .question summary:hover{background:var(--surface2)}
    .question summary > div{min-width:0;flex:1 1 auto}
    .question-number{display:block;margin-bottom:2px;color:var(--muted);font-size:.75rem;font-weight:600}
    .question-preview{font-size:.875rem;font-weight:600;line-height:1.5;overflow-wrap:anywhere}
    .toggle{flex-shrink:0;padding-top:2px;color:var(--ds-accent-text);font-size:.8125rem;font-weight:500;white-space:nowrap}
    .question[open] .toggle::after{content:'Hide'}
    .question:not([open]) .toggle::after{content:'View'}
    .question-body{padding:4px 20px 20px}
    .question-text{margin:0 0 12px;color:var(--text);font-size:.875rem;line-height:1.65;overflow-wrap:anywhere}
    .options{display:grid;gap:8px;margin:0;padding:0;list-style:none}
    .option{display:flex;align-items:flex-start;gap:10px;padding:10px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);
      color:var(--ds-text-secondary);font-size:.875rem;line-height:1.5}
    .option > span:nth-child(2){min-width:0;flex:1 1 auto;overflow-wrap:anywhere}
    .option.correct{border-color:var(--ds-success-border);background:var(--ds-success-soft);color:#d1fae5}
    .option-key{display:inline-flex;align-items:center;justify-content:center;flex:0 0 22px;height:22px;border-radius:var(--radius-xs);
      background:var(--surface2);color:var(--ds-text-secondary);font-size:.75rem;font-weight:600}
    .option.correct .option-key{background:rgba(16,185,129,.2);color:var(--ds-success-text)}
    .correct-tag{flex-shrink:0;margin-left:auto;color:var(--ds-success-text);font-size:.75rem;font-weight:600;white-space:nowrap;padding-top:2px}
    .empty{padding:32px 20px;color:var(--muted);font-size:.875rem;text-align:center}

    @media(max-width:1100px){.meta-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
    @media(max-width:900px){.main{padding:24px 20px 40px}}
    @media(max-width:700px){.meta-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media(max-width:640px){
      .main{padding:20px 16px 32px}
      .card{padding:16px}
      .questions{margin:16px -16px -16px}
      .question summary{padding:12px 16px}
      .question-body{padding:4px 16px 16px}
      .top{align-items:stretch;flex-direction:column}
      .top > div:first-child{flex:0 0 auto}
      .top > .btn{width:100%}
      .option{flex-wrap:wrap}
      .correct-tag{flex-basis:calc(100% - 32px);margin-left:32px;padding-top:0}
    }
    @media(max-width:420px){.meta-grid{grid-template-columns:minmax(0,1fr)}}
    @media(prefers-reduced-motion:reduce){.btn,.question summary{transition:none}}
  </style>
    @include('partials.page-head', ['pageDescription' => 'Browse and manage the coding and quiz challenge pool.'])
</head>
<body>
<div class="layout">
  @include('partials.instructor-sidebar')

  <main class="main">
    <div class="top">
      <div>
        <h1 class="title ds-page-title">{{ $challenge->title }}</h1>
        <p class="subtitle">{{ $challenge->description ?: 'Review the published questions and answer key available to students in this challenge.' }}</p>
      </div>

      <a class="btn secondary" href="{{ route('instructor.challenges.index', request()->only(['type', 'page'])) }}">Back to Challenge Pool</a>
    </div>

    <section class="card">
      <div class="meta-grid">
        <div class="meta">
          <span class="meta-label">Category</span>
          <span class="meta-value">{{ $challenge->category->name ?? 'Uncategorized' }}</span>
        </div>
        <div class="meta">
          <span class="meta-label">Questions</span>
          <span class="meta-value">{{ number_format($challenge->questions->count()) }}</span>
        </div>
        <div class="meta">
          <span class="meta-label">Time Limit</span>
          <span class="meta-value">{{ max(1, (int) ceil($challenge->time_limit_seconds / 60)) }} min</span>
        </div>
        <div class="meta">
          <span class="meta-label">Base XP</span>
          <span class="meta-value">{{ number_format($challenge->base_xp) }}</span>
        </div>
        <div class="meta">
          <span class="meta-label">Version</span>
          <span class="meta-value">{{ $challenge->version_name ?: 'Version '.$challenge->version_no }}</span>
        </div>
      </div>
    </section>

    <section class="card">
      <h2 class="section-title">Questions and Answer Key</h2>
      <p class="section-note">This page is read-only. Correct answers are highlighted for instructor review and lesson planning.</p>

      <div class="questions">
        @forelse($challenge->questions as $question)
          <details class="question" @if($loop->first) open @endif>
            <summary>
              <div>
                <span class="question-number">Question {{ $loop->iteration }}</span>
                <span class="question-preview">{{ \Illuminate\Support\Str::limit($question->question_text, 145) }}</span>
              </div>
              <span class="toggle" aria-hidden="true"></span>
            </summary>

            <div class="question-body">
              <p class="question-text">{{ $question->question_text }}</p>

              <ol class="options">
                @foreach($question->options as $option)
                  <li class="option {{ $option->is_correct ? 'correct' : '' }}">
                    <span class="option-key">{{ chr(64 + $loop->iteration) }}</span>
                    <span>{{ $option->option_text }}</span>
                    @if($option->is_correct)
                      <span class="correct-tag">Correct answer</span>
                    @endif
                  </li>
                @endforeach
              </ol>
            </div>
          </details>
        @empty
          <div class="empty">This active challenge does not contain any MCQ questions.</div>
        @endforelse
      </div>
    </section>
  </main>
</div>
</body>
</html>
