<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>DataSensei — {{ $module->title }}</title>
<style>
    /* Learning room: lesson outline beside a reading column. Colours, type
       and radius come from partials.design-system. Lesson bodies are rich
       HTML from the database; the rules under "lesson content" keep that
       markup readable without changing it. */
    :root {
      --accent3: var(--ds-success);
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    /* Desktop: an app-height screen; the outline and the lesson scroll
       independently. Below 900px the page scrolls normally. */
    body {
      height: 100vh;
      height: 100dvh;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      background: var(--bg);
      color: var(--text);
      font-family: var(--ds-font-sans);
    }

    /* ── title bar ─────────────────────────────────────────────── */
    .page-learning-topbar {
      position: relative;
      z-index: 50;
      flex-shrink: 0;
      min-height: 60px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      padding: 0 32px;
      background: var(--bg);
      border-bottom: 1px solid var(--border);
    }

    .page-learning-topbar-left {
      min-width: 0;
      flex: 1 1 auto;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .page-learning-mobile-btn {
      display: none;
      width: 36px;
      height: 36px;
      flex: 0 0 36px;
      align-items: center;
      justify-content: center;
      border: 1px solid var(--ds-border-strong);
      border-radius: var(--radius-sm);
      background: var(--surface2);
      color: var(--text);
      cursor: pointer;
      transition: background-color .12s ease;
    }

    .page-learning-mobile-btn:hover { background: var(--ds-surface-hover); }
    .page-learning-mobile-btn svg { width: 18px; height: 18px; }

    .page-learning-exit-btn {
      flex-shrink: 0;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 38px;
      padding: 0 16px;
      border: 1px solid var(--ds-border-strong);
      border-radius: var(--radius-sm);
      background: var(--surface2);
      color: var(--text);
      font-size: .875rem;
      font-weight: 500;
      line-height: 1.2;
      text-decoration: none;
      white-space: nowrap;
      transition: background-color .12s ease;
    }

    .page-learning-exit-btn:hover { background: var(--ds-surface-hover); }

    /* ── workspace ─────────────────────────────────────────────── */
    .page-learning-workspace {
      position: relative;
      flex: 1;
      min-height: 0;
      display: flex;
      overflow: hidden;
    }

    /* Lesson outline */
    .page-learning-sidebar {
      width: 288px;
      flex-shrink: 0;
      display: flex;
      flex-direction: column;
      min-height: 0;
      background: var(--surface);
      border-right: 1px solid var(--border);
    }

    .page-learning-sidebar-header {
      padding: 20px;
      border-bottom: 1px solid var(--border);
    }

    .page-learning-module-title {
      margin-bottom: 12px;
      color: var(--text);
      font-size: .9375rem;
      font-weight: 600;
      line-height: 1.4;
      overflow-wrap: break-word;
    }

    .page-learning-progress-wrap { display: flex; align-items: center; gap: 12px; }
    .page-learning-progress-bar { flex: 1; height: 6px; overflow: hidden; border-radius: 999px; background: var(--surface2); }
    .page-learning-progress-fill { height: 100%; border-radius: inherit; background: var(--accent3); }
    .page-learning-progress-text {
      min-width: 3ch;
      color: var(--ds-success-text);
      font-size: .75rem;
      font-weight: 600;
      text-align: right;
      font-variant-numeric: tabular-nums;
    }

    .page-learning-lesson-list {
      flex: 1;
      min-height: 0;
      overflow-y: auto;
      overscroll-behavior: contain;
      padding: 8px 0;
      scrollbar-width: thin;
    }

    .page-learning-lesson-item {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      padding: 10px 20px 10px 17px;
      border-left: 3px solid transparent;
      color: var(--muted);
      font-size: .875rem;
      font-weight: 500;
      line-height: 1.4;
      text-decoration: none;
      overflow-wrap: break-word;
      transition: background-color .12s ease, color .12s ease;
    }

    .page-learning-lesson-item:hover { background: var(--surface2); color: var(--text); }

    .page-learning-lesson-item.is-active {
      border-left-color: var(--accent);
      background: var(--surface2);
      color: var(--text);
    }

    .page-learning-check-icon {
      width: 18px;
      height: 18px;
      flex-shrink: 0;
      margin-top: 1px;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 1.5px solid var(--ds-border-strong);
      border-radius: 50%;
      color: transparent;
    }

    .page-learning-lesson-item.is-active .page-learning-check-icon { border-color: var(--accent); }

    .page-learning-lesson-item.is-completed .page-learning-check-icon {
      border-color: var(--accent3);
      background: var(--accent3);
      color: #fff;
    }

    /* What you will learn (the module's descriptive learning outcomes) */
    .page-learning-outcomes { margin-top: 14px; }
    .page-learning-outcomes summary {
      color: var(--ds-text-secondary);
      font-size: .8125rem;
      font-weight: 600;
    }
    .page-learning-outcomes ul { margin: 8px 0 0; padding-left: 18px; color: var(--muted); font-size: .8125rem; line-height: 1.5; }
    .page-learning-outcomes li + li { margin-top: 4px; }

    /* Review questions: instant feedback, nothing is scored */
    .page-learning-review-intro { margin: 4px 0 24px; color: var(--muted); font-size: .9375rem; line-height: 1.55; }
    .page-learning-question { margin-bottom: 20px; padding: 18px 20px; border: 1px solid var(--border); border-radius: var(--radius); background: var(--surface); }
    .page-learning-question-meta { color: var(--dim); font-size: .75rem; font-weight: 600; }
    .page-learning-question h3 { margin: 6px 0 10px; font-size: 1rem; line-height: 1.45; }
    .page-learning-question-scenario { margin: 0 0 12px; color: var(--muted); font-size: .875rem; line-height: 1.55; }
    .page-learning-choice { display: flex; align-items: flex-start; gap: 10px; margin-top: 8px; padding: 10px 12px; border: 1px solid var(--border); border-radius: 6px; cursor: pointer; font-size: .9rem; line-height: 1.45; }
    .page-learning-choice input { margin-top: 3px; }
    .page-learning-choice.is-correct { border-color: var(--ds-success-border); background: var(--ds-success-soft); }
    .page-learning-choice.is-wrong { border-color: var(--ds-danger-border); background: var(--ds-danger-soft); }
    .page-learning-check { margin-top: 12px; min-height: 36px; padding: 0 14px; border: 1px solid var(--accent); border-radius: 6px; background: var(--accent); color: #fff; font: inherit; font-size: .875rem; font-weight: 500; cursor: pointer; }
    .page-learning-feedback { margin-top: 12px; color: var(--ds-text-secondary); font-size: .875rem; line-height: 1.55; }
    .page-learning-feedback strong { color: var(--text); }

    /* Reading column */
    .page-learning-content-area {
      position: relative;
      flex: 1;
      min-width: 0;
      display: flex;
      flex-direction: column;
      overflow-y: auto;
      background: var(--bg);
    }

    .page-learning-content-inner {
      flex: 1;
      width: 100%;
      max-width: 840px;
      margin: 0 auto;
      padding: 28px 32px 48px;
    }

    .page-learning-lesson-footer {
      position: sticky;
      bottom: 0;
      z-index: 5;
      display: flex;
      align-items: center;
      justify-content: flex-end;
      padding: 12px 32px;
      background: var(--surface);
      border-top: 1px solid var(--border);
    }

    .page-learning-btn-complete {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      min-height: 38px;
      padding: 0 16px;
      border: 1px solid var(--accent);
      border-radius: var(--radius-sm);
      background: var(--accent);
      color: #fff;
      font: 500 .875rem/1.2 var(--ds-font-sans);
      cursor: pointer;
      transition: background-color .12s ease, border-color .12s ease;
    }

    .page-learning-btn-complete:hover { border-color: var(--accent-hover); background: var(--accent-hover); }
    .page-learning-btn-complete svg { width: 16px; height: 16px; }

    /* Drawer backdrop for the outline on small screens. */
    .page-learning-mobile-overlay {
      display: none;
      position: fixed;
      inset: 0;
      z-index: 1000;
      background: var(--ds-overlay);
    }

    /* A knowledge check or final exam, once every question is answered. */
    .page-learning-quiz-result {
      margin: 16px 0 0;
      padding: 12px 16px;
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      background: var(--surface2);
      color: var(--text);
      font-size: .9375rem;
      line-height: 1.5;
    }

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
      color: var(--ds-success-ink, #d1fae5);
    }

    .page-learning-lesson-body .quiz-option.correct .opt-key {
      border-color: var(--ds-success);
      background: var(--ds-success);
      color: #fff;
    }

    .page-learning-lesson-body .quiz-option.wrong {
      border-color: var(--ds-danger-border);
      background: var(--ds-danger-soft);
      color: var(--ds-danger-ink, #fee2e2);
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
      color: var(--ds-accent-ink, #dbeafe);
      font-size: .875rem;
      line-height: 1.6;
    }

    .page-learning-lesson-body .quiz-explanation strong { color: #fff; }

    /* ── small screens ─────────────────────────────────────────── */
    @media (max-width: 900px) {
      body {
        height: auto;
        min-height: 100vh;
        min-height: 100dvh;
        display: block;
        overflow: visible;
      }

      .page-learning-topbar {
        position: sticky;
        top: var(--ds-sticky-top, 0px);
        min-height: 56px;
        padding: 0 20px;
      }

      .page-learning-mobile-btn { display: inline-flex; }

      .page-learning-workspace {
        display: block;
        overflow: visible;
      }

      /* The outline becomes a drawer opened from the menu button. */
      .page-learning-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        z-index: 1010;
        width: min(300px, 86vw);
        height: 100vh;
        height: 100dvh;
        border-right: 1px solid var(--ds-border-strong);
        transform: translateX(-100%);
        visibility: hidden;
        transition: transform .22s var(--ds-ease), visibility 0s linear .22s;
      }

      .page-learning-sidebar.is-open {
        transform: none;
        visibility: visible;
        box-shadow: var(--ds-shadow-lg);
        transition: transform .22s var(--ds-ease), visibility 0s;
      }

      .page-learning-mobile-overlay.is-open { display: block; }

      .page-learning-content-area {
        min-height: calc(100vh - 56px);
        min-height: calc(100dvh - 56px);
        overflow: visible;
      }

      .page-learning-content-inner { padding: 24px 20px 40px; }
      .page-learning-lesson-footer { padding: 12px 20px; }
    }

    @media (max-width: 640px) {
      .page-learning-topbar { padding: 0 16px; gap: 8px; }
      .page-learning-topbar-left { gap: 8px; }
      .page-learning-exit-btn { min-height: 32px; padding: 0 12px; font-size: .8125rem; }
      .page-learning-content-inner { padding: 20px 16px 32px; }
      .page-learning-lesson-footer { padding: 12px 16px; }
      .page-learning-btn-complete { width: 100%; }

      .page-learning-lesson-body table { min-width: 480px; }
      .page-learning-lesson-body .quiz-card-header,
      .page-learning-lesson-body .quiz-options,
      .page-learning-lesson-body .quiz-score-bar { padding-left: 16px; padding-right: 16px; }
      .page-learning-lesson-body .quiz-explanation { margin: 0 16px 16px; }
    }

    @media (prefers-reduced-motion: reduce) {
      .page-learning-sidebar,
      .page-learning-sidebar.is-open { transition: none; }
    }
  </style>
  @include('partials.ui-polish')
    @include('partials.page-head', ['pageDescription' => 'A focused space for working through a lesson with your class.'])
</head>
<body>

  <header class="page-learning-topbar">
    <div class="page-learning-topbar-left">
      <button type="button" id="mobileMenuBtn" class="page-learning-mobile-btn" aria-label="Open lesson navigation">
        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>

      <h1 class="ds-page-title">Learning Environment</h1>
    </div>
    <a href="{{ route('modules.index') }}" class="page-learning-exit-btn">Exit Course</a>
  </header>

  <div class="page-learning-workspace">
    
    <div id="mobileOverlay" class="page-learning-mobile-overlay"></div>

    <div id="courseSidebar" class="page-learning-sidebar">
      <div class="page-learning-sidebar-header">
        <div class="page-learning-module-title">{{ $module->title }}</div>
        
        <div class="page-learning-progress-wrap">
          <div class="page-learning-progress-bar">
            <div class="page-learning-progress-fill" style="width: {{ $progressPct }}%;"></div>
          </div>
          <div class="page-learning-progress-text">{{ $progressPct }}%</div>
        </div>

        @if(! empty($learningOutcomes))
          {{-- Closed by default; learners open it when they want (Updates 6). --}}
          @include('partials.learning-outcomes-toggle', ['outcomes' => $learningOutcomes, 'class' => 'page-learning-outcomes'])
        @endif
      </div>
      
      <div class="page-learning-lesson-list">
        @foreach($module->lessons as $lesson)
          @php
            $isCompleted = in_array($lesson->id, $completedLessonIds);
            $isActive = $activeLesson && $activeLesson->id === $lesson->id;
          @endphp
          
          <a href="{{ route('lesson.show', ['module' => $module->id, 'lesson' => $lesson->id]) }}" 
             class="page-learning-lesson-item {{ $isCompleted ? 'is-completed' : '' }} {{ $isActive ? 'is-active' : '' }}">
            
            <div class="page-learning-check-icon">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <polyline points="20 6 9 17 4 12"/>
              </svg>
            </div>
            
            {{ $lesson->title }}
          </a>
        @endforeach

        @if(! empty($reviewQuestions))
          <a href="{{ route('lesson.review', $module) }}"
             class="page-learning-lesson-item {{ ($reviewMode ?? false) ? 'is-active' : '' }}">
            <div class="page-learning-check-icon"></div>
            Review questions
          </a>
        @endif
      </div>
    </div>

    <div class="page-learning-content-area">
      <div class="page-learning-content-inner">
        @if($reviewMode ?? false)
          @if(session('success'))
            <p class="page-learning-review-intro"><strong>{{ session('success') }}</strong></p>
          @endif
          <div class="page-learning-lesson-body">
            <h2>Review questions</h2>
            <p class="page-learning-review-intro">Check what you learned in this module. Pick an answer, then check it. These questions are for practice only: nothing is scored or saved.</p>
          </div>
          @foreach($reviewQuestions as $qIndex => $question)
            @php
              $choices = array_values((array) ($question['choices'] ?? []));
              $answer = (string) ($question['answer'] ?? '');
            @endphp
            <article class="page-learning-question" data-review-question data-answer="{{ $answer }}">
              <div class="page-learning-question-meta">Question {{ $qIndex + 1 }}@if(! empty($question['topic'])), {{ $question['topic'] }}@endif</div>
              <h3>{{ $question['question'] ?? '' }}</h3>
              @if(! empty($question['scenario']))
                <p class="page-learning-question-scenario">{{ $question['scenario'] }}</p>
              @endif
              @foreach($choices as $cIndex => $choice)
                <label class="page-learning-choice" data-choice="{{ $choice }}">
                  <input type="radio" name="review-{{ $qIndex }}" value="{{ $choice }}">
                  <span>{{ chr(65 + $cIndex) }}. {{ $choice }}</span>
                </label>
              @endforeach
              <button type="button" class="page-learning-check" onclick="checkReviewAnswer(this)">Check answer</button>
              <div class="page-learning-feedback" data-feedback hidden>
                <p data-verdict></p>
                @if(! empty($question['explanation']))
                  <p><strong>Explanation:</strong> {{ $question['explanation'] }}</p>
                @endif
                @if(! empty($question['learning_tip']))
                  <p><strong>Learning tip:</strong> {{ $question['learning_tip'] }}</p>
                @endif
              </div>
            </article>
          @endforeach
        @else
        <div class="page-learning-lesson-body">
          {!! $activeLesson->content !!}
        </div>
        @endif
      </div>

      @unless($reviewMode ?? false)
      <div class="page-learning-lesson-footer">
        <form action="{{ route('lesson.complete', $activeLesson->id) }}" method="POST" style="width: 100%; display: flex; justify-content: flex-end;">
          @csrf
          <button type="submit" class="page-learning-btn-complete">
            Mark as Complete & Continue
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
          </button>
        </form>
      </div>
      @endunless
    </div>

  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const menuBtn = document.getElementById('mobileMenuBtn');
      const sidebar = document.getElementById('courseSidebar');
      const overlay = document.getElementById('mobileOverlay');

      menuBtn.addEventListener('click', function() {
        sidebar.classList.toggle('is-open');
        overlay.classList.toggle('is-open');
      });

      overlay.addEventListener('click', function() {
        sidebar.classList.remove('is-open');
        overlay.classList.remove('is-open');
      });
    });

    function checkReviewAnswer(button) {
      const card = button.closest('[data-review-question]');
      const picked = card.querySelector('input[type="radio"]:checked');
      const feedback = card.querySelector('[data-feedback]');
      const verdict = card.querySelector('[data-verdict]');

      if (!picked) {
        feedback.hidden = false;
        verdict.textContent = 'Pick an answer first.';
        return;
      }

      const answer = card.dataset.answer;
      card.querySelectorAll('[data-choice]').forEach(choice => {
        choice.classList.toggle('is-correct', choice.dataset.choice === answer);
        choice.classList.toggle('is-wrong', choice.dataset.choice === picked.value && picked.value !== answer);
      });

      feedback.hidden = false;
      verdict.innerHTML = picked.value === answer
        ? '<strong>Correct.</strong>'
        : '<strong>Not quite.</strong> The correct answer is highlighted.';
    }

    // Knowledge checks and the module's Final Exam give their result once
    // every question has been answered (DataSensei Updates 7). The Final Exam
    // is open to every learner; completing the lesson records it.
    document.addEventListener('click', function (event) {
      const option = event.target.closest('.page-learning-lesson-body .quiz-option');
      const wrapper = option ? option.closest('.quiz-wrapper') : null;
      if (wrapper) window.setTimeout(function () { showQuizResult(wrapper); }, 0);
    });

    function showQuizResult(wrapper) {
      const cards = Array.prototype.slice.call(wrapper.querySelectorAll('.quiz-card'));
      if (!cards.length || cards.some(card => !card.querySelector('.quiz-option.locked'))) return;

      const correct = cards.filter(card => !card.querySelector('.quiz-option.wrong')).length;
      const percent = Math.round((correct / cards.length) * 100);
      const isExam = /FINAL/i.test(wrapper.id || '');
      let result = wrapper.nextElementSibling;
      if (!result || !result.classList.contains('page-learning-quiz-result')) {
        result = document.createElement('p');
        result.className = 'page-learning-quiz-result';
        result.setAttribute('role', 'status');
        wrapper.insertAdjacentElement('afterend', result);
      }
      result.textContent = (isExam ? 'Final exam result: ' : 'Result: ')
        + correct + ' of ' + cards.length + ' correct (' + percent + '%).'
        + (isExam ? ' Select "Mark as Complete & Continue" to record that you finished the exam.' : '');
    }

    function launchIDE(button) {
      const windowContainer = button.closest('.code-window');

      // Read the language label from the header bar (e.g. "SQL — SELECT Basics"
      // or "PYTHON — Connect to SQLite"). This is the first <span> inside the
      // dark header div that sits directly before the button's parent row.
      const headerBar  = windowContainer.querySelector('div:first-child');
      const labelSpan  = headerBar ? headerBar.querySelector('span') : null;
      const labelText  = labelSpan ? labelSpan.innerText.trim().toUpperCase() : '';

      const codeElement = windowContainer.querySelector('.code-content');
      const rawCode = codeElement.innerText.trim();

      // Always store the return URL so both destinations can show "Back to Lesson"
      sessionStorage.setItem('datasensei_return_url', window.location.href);

      // ── Route decision ────────────────────────────────────────────────────
      // Labels that begin with "SQL" contain pure SQL — send to the SQL Sandbox.
      // Everything else (PYTHON, or mixed Python+SQL) goes to the Python IDE.
      if (labelText.startsWith('SQL')) {
        sessionStorage.setItem('datasensei_pending_sql_code', rawCode);
        window.location.href = "{{ route('sql-sandbox.index') }}";
      } else {
        sessionStorage.setItem('datasensei_pending_code', rawCode);
        window.location.href = "{{ route('ide.index') }}";
      }
    }
  </script>

</body>
</html>
