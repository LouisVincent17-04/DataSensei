{{--
    Live preview for the lesson block editor. This is the learning room's
    reading column on its own: the same head partials (fonts, design system,
    polish) and the same .page-learning-lesson-body rules, copied from
    resources/views/student/learning-room.blade.php, so the preview is what
    students will see. Keep the two rule sets in step.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <base href="{{ url('/') }}/">
  <title>Lesson preview</title>
  @include('partials.page-head', ['pageTitle' => 'Lesson preview', 'pageDescription' => 'Live preview of a lesson as students will see it.'])
<style>
    :root {
      --accent3: var(--ds-success);
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      min-height: 100vh;
      background: var(--bg);
      color: var(--text);
      font-family: var(--ds-font-sans);
    }

    /* Same padding as the learning room's reading column. */
    .page-learning-content-inner { width: 100%; max-width: 840px; margin: 0 auto; padding: 28px 32px 48px; }

    /* ── lesson content (HTML stored with each lesson) ─────────── */
    .page-learning-lesson-body {
      color: var(--ds-text-secondary);
      font-size: 1rem;
      line-height: 1.65;
      overflow-wrap: break-word;
    }

    .page-learning-lesson-body h2 {
      margin: 0 0 12px;
      color: var(--text);
      font-size: 1.25rem;
      font-weight: 700;
      line-height: 1.3;
      letter-spacing: -.015em;
    }

    .page-learning-lesson-body h3 {
      margin: 32px 0 8px;
      color: var(--text);
      font-size: 1.0625rem;
      font-weight: 600;
      line-height: 1.4;
    }

    .page-learning-lesson-body h4 {
      margin: 24px 0 8px;
      color: var(--text);
      font-size: .9375rem;
      font-weight: 600;
    }

    .page-learning-lesson-body p {
      max-width: 75ch;
      margin-bottom: 16px;
      color: var(--ds-text-secondary);
      font-size: 1rem;
      line-height: 1.65;
    }

    .page-learning-lesson-body strong { color: var(--text); font-weight: 600; }
    .page-learning-lesson-body a { color: var(--ds-accent-text); }

    .page-learning-lesson-body ul,
    .page-learning-lesson-body ol {
      max-width: 75ch;
      margin: 0 0 16px !important;
      padding-left: 1.25rem;
      line-height: 1.65 !important;
    }

    .page-learning-lesson-body li + li { margin-top: 4px; }

    .page-learning-lesson-body :not(pre) > code {
      padding: .1em .35em;
      border: 1px solid var(--border);
      border-radius: var(--radius-xs);
      background: var(--surface2);
      color: var(--text);
      font-family: var(--ds-font-mono);
      font-size: .875em;
    }

    .page-learning-lesson-body pre {
      max-width: 100%;
      margin: 0 0 20px;
      padding: 16px;
      overflow-x: auto;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      background: var(--surface3);
      font-family: var(--ds-font-mono);
      font-size: .8125rem;
      line-height: 1.6;
    }

    /* Code examples: the code scrolls sideways inside its own box. */
    .page-learning-lesson-body .code-window { max-width: 100%; }
    .page-learning-lesson-body div[style*="JetBrains Mono"] { line-height: 1.6; tab-size: 4; }

    .page-learning-lesson-body .code-window button {
      display: inline-flex;
      align-items: center;
      flex-shrink: 0;
      min-height: 32px;
      padding: 0 12px !important;
      border: 1px solid var(--accent) !important;
      border-radius: var(--radius-sm) !important;
      font-family: var(--ds-font-sans);
      font-size: .8125rem !important;
      font-weight: 500 !important;
      white-space: nowrap;
      transition: background-color .12s ease;
    }

    .page-learning-lesson-body .code-window button:hover { background: var(--accent-hover) !important; }

    .page-learning-lesson-body .code-window > div:first-child { gap: 12px; flex-wrap: wrap; }

    .page-learning-lesson-body span[style*="text-transform:uppercase"] {
      font-size: .75rem !important;
      letter-spacing: 0 !important;
      text-transform: none !important;
    }

    .page-learning-lesson-body table { font-size: .875rem; }
    .page-learning-lesson-body th { font-weight: 600 !important; white-space: nowrap; }

    /* Knowledge checks: restyles the quiz markup that ships inside lessons. */
    .page-learning-lesson-body .quiz-wrapper { gap: 16px; margin-top: 32px; }

    .page-learning-lesson-body .quiz-score-bar {
      padding: 12px 20px;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      background: var(--surface);
      color: var(--text);
      font-size: .9375rem;
      font-weight: 600;
    }

    .page-learning-lesson-body .quiz-score-val {
      color: var(--ds-text-secondary);
      font-family: var(--ds-font-sans);
      font-size: .875rem;
      font-weight: 600;
      font-variant-numeric: tabular-nums;
    }

    .page-learning-lesson-body .quiz-card {
      border: 1px solid var(--border);
      border-radius: var(--radius);
      background: var(--surface);
    }

    .page-learning-lesson-body .quiz-card-header {
      gap: 12px;
      padding: 14px 20px;
      border-bottom: 1px solid var(--border);
      background: transparent;
    }

    .page-learning-lesson-body .quiz-q-num {
      margin-top: 1px;
      padding: 2px 8px;
      border: 1px solid var(--ds-accent-border);
      border-radius: var(--radius-xs);
      background: var(--ds-accent-soft);
      color: var(--ds-accent-text);
      font-family: var(--ds-font-sans);
      font-size: .75rem;
      font-weight: 600;
      line-height: 1.4;
      font-variant-numeric: tabular-nums;
    }

    .page-learning-lesson-body .quiz-q-text {
      min-width: 0;
      color: var(--text);
      font-size: .9375rem;
      font-weight: 600;
      line-height: 1.5;
    }

    .page-learning-lesson-body .quiz-options { gap: 8px; padding: 16px 20px; }

    .page-learning-lesson-body .quiz-option {
      align-items: flex-start;
      gap: 10px;
      min-height: 40px;
      padding: 9px 12px;
      border: 1px solid var(--ds-input-border);
      border-radius: var(--radius-sm);
      background: var(--surface3);
      color: var(--ds-text-secondary);
      font-family: var(--ds-font-sans);
      font-size: .875rem;
      line-height: 1.5;
      transition: background-color .12s ease, border-color .12s ease, color .12s ease;
    }

    .page-learning-lesson-body .quiz-option:hover:not(.locked) {
      border-color: var(--border-hover);
      background: var(--surface2);
      color: var(--text);
    }

    .page-learning-lesson-body .quiz-option .opt-key {
      width: 22px;
      height: 22px;
      margin-top: 0;
      border: 1px solid var(--ds-border-strong);
      border-radius: var(--radius-xs);
      background: var(--surface2);
      color: var(--muted);
      font-family: var(--ds-font-sans);
      font-size: .75rem;
      font-weight: 600;
      transition: none;
    }

    .page-learning-lesson-body .quiz-option.correct {
      border-color: var(--ds-success-border);
      background: var(--ds-success-soft);
      color: #d1fae5;
    }

    .page-learning-lesson-body .quiz-option.correct .opt-key {
      border-color: var(--ds-success);
      background: var(--ds-success);
      color: #fff;
    }

    .page-learning-lesson-body .quiz-option.wrong {
      border-color: var(--ds-danger-border);
      background: var(--ds-danger-soft);
      color: #fee2e2;
      opacity: 1;
    }

    .page-learning-lesson-body .quiz-option.wrong .opt-key {
      border-color: var(--ds-danger);
      background: var(--ds-danger);
      color: #fff;
    }

    .page-learning-lesson-body .quiz-explanation {
      margin: 0 20px 20px;
      padding: 12px 16px;
      border: 1px solid var(--ds-accent-border);
      border-radius: var(--radius-sm);
      background: var(--ds-accent-soft);
      color: #dbeafe;
      font-size: .875rem;
      line-height: 1.6;
    }

    .page-learning-lesson-body .quiz-explanation strong { color: #fff; }

    @media (max-width: 900px) {
      .page-learning-content-inner { padding: 24px 20px 40px; }
    }

    @media (max-width: 640px) {
      .page-learning-content-inner { padding: 20px 16px 32px; }
      .page-learning-lesson-body table { min-width: 480px; }
      .page-learning-lesson-body .quiz-card-header,
      .page-learning-lesson-body .quiz-options,
      .page-learning-lesson-body .quiz-score-bar { padding-left: 16px; padding-right: 16px; }
      .page-learning-lesson-body .quiz-explanation { margin: 0 16px 16px; }
    }
  </style>
</head>
<body>
  <div class="page-learning-content-inner">
    <div class="page-learning-lesson-body">
      @if(trim((string) $html) === '')
        <p style="color:var(--muted);">Nothing to show yet. Add a block on the left and it appears here as students will see it.</p>
      @else
        {!! $html !!}
      @endif
    </div>
  </div>

  <script>
    // The learning room opens the IDE from "Try in Compiler". In the preview
    // the button only needs to exist and not throw.
    function launchIDE(button) {
      if (button && button.blur) button.blur();
      return false;
    }
  </script>
</body>
</html>
