<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $challenge->title }} — Challenge Pool</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#0d1320;--surface:#111c2d;--surface2:#1a2638;--border:#1e2f47;--text:#fafafa;--muted:#7f93b0;--accent:#3b82f6;--good:#10b981;--warn:#f59e0b;--radius:14px;--radius-sm:8px}
    *{box-sizing:border-box}
    body{margin:0;font-family:Inter,Arial,sans-serif;background:var(--bg);color:var(--text)}
    .layout{display:flex;min-height:100vh}
    .main{flex:1;padding:32px;min-width:0;background:radial-gradient(circle at top right,rgba(59,130,246,.10),transparent 40%),var(--bg)}
    .top{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;margin-bottom:24px}
    .eyebrow{color:#93c5fd;text-transform:uppercase;letter-spacing:.08em;font-size:.73rem;font-weight:800;margin-bottom:8px}
    .title{font-size:1.8rem;font-weight:800;margin:0;line-height:1.25}
    .subtitle{color:var(--muted);margin-top:8px;line-height:1.65;max-width:850px}
    .card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:20px;margin-bottom:18px}
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:10px 14px;border-radius:var(--radius-sm);background:var(--accent);color:white;text-decoration:none;border:0;font-weight:700;cursor:pointer;font-family:inherit;font-size:.88rem;white-space:nowrap}
    .btn.secondary{background:var(--surface2);border:1px solid var(--border);color:var(--text)}
    .btn:hover{filter:brightness(1.08)}
    .meta-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px}
    .meta{background:var(--surface2);border:1px solid var(--border);border-radius:12px;padding:15px}
    .meta-label{display:block;color:var(--muted);font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;margin-bottom:7px}
    .meta-value{font-size:1rem;font-weight:800;line-height:1.4}
    .section-title{font-size:1.15rem;margin:0}
    .section-note{color:var(--muted);font-size:.88rem;line-height:1.55;margin:7px 0 0}
    .questions{display:grid;gap:12px;margin-top:18px}
    .question{background:var(--surface2);border:1px solid var(--border);border-radius:12px;overflow:hidden}
    .question summary{list-style:none;cursor:pointer;padding:16px 18px;display:flex;align-items:flex-start;justify-content:space-between;gap:18px}
    .question summary::-webkit-details-marker{display:none}
    .question summary:hover{background:rgba(59,130,246,.05)}
    .question-number{display:block;color:#93c5fd;font-size:.75rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px}
    .question-preview{font-weight:700;line-height:1.55}
    .toggle{color:var(--muted);font-size:.8rem;white-space:nowrap;padding-top:3px}
    .question[open] .toggle::after{content:'Hide'}
    .question:not([open]) .toggle::after{content:'View'}
    .question-body{border-top:1px solid var(--border);padding:18px}
    .question-text{margin:0 0 16px;line-height:1.7;color:var(--text)}
    .options{display:grid;gap:9px;margin:0;padding:0;list-style:none}
    .option{display:flex;align-items:flex-start;gap:10px;border:1px solid var(--border);border-radius:10px;padding:12px 14px;color:var(--muted);line-height:1.55}
    .option.correct{border-color:rgba(16,185,129,.45);background:rgba(16,185,129,.08);color:#d1fae5}
    .option-key{display:inline-flex;align-items:center;justify-content:center;flex:0 0 26px;height:26px;border-radius:999px;background:rgba(255,255,255,.06);font-size:.75rem;font-weight:800}
    .correct-tag{margin-left:auto;color:#6ee7b7;font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap;padding-top:3px}
    .empty{padding:30px;text-align:center;color:var(--muted)}
    @media(max-width:1100px){.meta-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media(max-width:700px){.layout{display:block}.main{padding:18px}.top{display:block}.top .btn{margin-top:16px}.meta-grid{grid-template-columns:1fr}.question summary{display:block}.toggle{display:block;margin-top:10px}.correct-tag{margin-left:0}}
  </style>
</head>
<body>
<div class="layout">
  @include('partials.instructor-sidebar')

  <main class="main">
    <div class="top">
      <div>
        <div class="eyebrow">University Student MCQ Content</div>
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
