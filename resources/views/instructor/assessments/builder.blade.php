<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $assessment->title }} Builder</title>
<style>
    /* Assessment question builder. Colours, type and radius come from partials.design-system.
       Classes toggled by the script below (.hidden, .mcq-panel, .option-row ...) are kept. */
    *{box-sizing:border-box}
    body{margin:0;background:var(--bg);color:var(--text);font-family:var(--ds-font-sans)}
    button,input,select,textarea{font:inherit}
    .layout{display:flex;min-height:100vh}
    .main{flex:1;min-width:0;padding:28px 32px 48px}
    .wrap{max-width:1500px;margin:0 auto}

    /* page header */
    .top{display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px;margin-bottom:24px}
    .top > div:first-child{min-width:0;flex:1 1 360px}
    .subtitle{max-width:72ch;margin:4px 0 0;color:var(--muted);font-size:.875rem;line-height:1.55}
    .actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
    .muted{color:var(--muted)}
    .small{font-size:.8125rem;line-height:1.5}

    /* buttons */
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:38px;padding:0 16px;
      border:1px solid var(--accent);border-radius:var(--radius-sm);background:var(--accent);color:#fff;
      font:500 .875rem/1.2 var(--ds-font-sans);text-decoration:none;white-space:nowrap;cursor:pointer;
      transition:background .12s ease,border-color .12s ease,color .12s ease}
    .btn:hover:not(:disabled){border-color:var(--accent-hover);background:var(--accent-hover)}
    .btn.secondary{border-color:var(--ds-border-strong);background:var(--surface2);color:var(--text)}
    .btn.secondary:hover:not(:disabled){border-color:var(--ds-border-strong);background:var(--ds-surface-hover)}
    .btn.good{border-color:var(--ds-success-border);background:transparent;color:var(--ds-success-text)}
    .btn.good:hover:not(:disabled){border-color:var(--ds-success-border);background:var(--ds-success-soft)}
    .btn.warn{border-color:var(--ds-warning-border);background:transparent;color:var(--ds-warning-text)}
    .btn.warn:hover:not(:disabled){border-color:var(--ds-warning-border);background:var(--ds-warning-soft)}
    .btn:disabled{opacity:.5;cursor:not-allowed}
    .btn.small{min-height:32px;padding:0 12px;font-size:.8125rem}

    /* badges */
    .badge{display:inline-flex;align-items:center;gap:6px;padding:2px 8px;border:1px solid var(--ds-border-strong);border-radius:var(--radius-xs);
      background:var(--surface2);color:var(--ds-text-secondary);font-size:.75rem;font-weight:600;line-height:1.4;white-space:nowrap;
      font-variant-numeric:tabular-nums}
    .badge.good{border-color:var(--ds-success-border);background:var(--ds-success-soft);color:var(--ds-success-text)}
    .badge.warn{border-color:var(--ds-warning-border);background:var(--ds-warning-soft);color:var(--ds-warning-text)}
    .badge.bad{border-color:var(--ds-danger-border);background:var(--ds-danger-soft);color:var(--ds-danger-text)}
    /* the status arrives upper-case; show it in sentence case */
    .top .badge{display:inline-block;text-transform:lowercase}
    .top .badge::first-letter{text-transform:capitalize}

    /* messages */
    .alert{margin-bottom:16px;padding:12px 16px;border:1px solid var(--ds-success-border);border-radius:var(--radius-sm);
      background:var(--ds-success-soft);color:#d1fae5;font-size:.875rem;line-height:1.5}
    .alert.error{border-color:var(--ds-danger-border);background:var(--ds-danger-soft);color:#fee2e2}

    /* cards */
    .card{margin-bottom:16px;padding:20px;border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}

    /* authoring progress: progress bar plus three figures (label above value) */
    .summary{display:grid;grid-template-columns:minmax(0,1.4fr) repeat(3,minmax(0,.55fr));gap:12px;align-items:stretch;margin-bottom:20px}
    .progress-card{display:flex;flex-direction:column;justify-content:center;padding-right:8px}
    .progress-line{display:flex;justify-content:space-between;gap:12px;align-items:baseline;margin-bottom:8px;font-size:.875rem}
    .progress-line strong{font-weight:600}
    .progress-line span{color:var(--ds-text-secondary);font-weight:600;font-variant-numeric:tabular-nums}
    .progress-track{height:6px;overflow:hidden;border-radius:999px;background:var(--surface2)}
    .progress-fill{height:100%;border-radius:inherit;background:var(--accent)}
    .metric{display:flex;flex-direction:column-reverse;justify-content:flex-end;gap:4px;padding:12px 16px;border-radius:var(--radius-sm);background:var(--surface3)}
    .metric strong{font-size:1.5rem;font-weight:700;line-height:1.2;letter-spacing:-.02em;font-variant-numeric:tabular-nums}
    .metric .muted{font-size:.8125rem;font-weight:500}

    /* two columns: item guide beside the editor */
    .builder-grid{display:grid;grid-template-columns:minmax(280px,340px) minmax(0,1fr);gap:20px;align-items:start}
    .builder-side{position:sticky;top:calc(var(--ds-sticky-top, 0px) + 16px);max-height:calc(100vh - var(--ds-sticky-top, 0px) - 32px);overflow:auto;padding-right:4px}
    .side-title{display:flex;justify-content:space-between;gap:8px;align-items:center;margin-bottom:8px}
    .side-title h2,.question-title h2{margin:0;font-size:.9375rem;font-weight:600;line-height:1.35}
    .builder-side .card > p.muted{margin:0 0 4px}

    .tos-group{margin-top:12px;padding:12px;border-radius:var(--radius-sm);background:var(--surface3)}
    .tos-group-head{display:flex;justify-content:space-between;gap:8px;align-items:flex-start}
    .tos-group-title{min-width:0;font-size:.875rem;font-weight:600;line-height:1.4;overflow-wrap:anywhere}
    .tos-group-head .badge{flex-shrink:0}
    .tos-meta{display:flex;gap:4px;flex-wrap:wrap;margin-top:8px}
    .tos-meta span{padding:2px 8px;border:1px solid var(--ds-border-strong);border-radius:var(--radius-xs);background:var(--surface2);
      color:var(--ds-text-secondary);font-size:.75rem;font-weight:500;line-height:1.4}
    .item-nav{display:flex;flex-wrap:wrap;gap:6px;margin-top:10px}
    .item-nav a{width:34px;height:34px;display:flex;align-items:center;justify-content:center;border:1px solid var(--ds-border-strong);
      border-radius:var(--radius-sm);background:var(--surface2);color:var(--muted);font-size:.8125rem;font-weight:600;
      font-variant-numeric:tabular-nums;text-decoration:none;transition:background .12s ease,color .12s ease}
    .item-nav a:hover{background:var(--ds-surface-hover);color:var(--text)}
    .item-nav a.complete{border-color:var(--ds-success-border);background:var(--ds-success-soft);color:var(--ds-success-text)}
    .item-nav a.in_progress{border-color:var(--ds-warning-border);background:var(--ds-warning-soft);color:var(--ds-warning-text)}
    .item-nav a.active{outline:2px solid var(--accent);outline-offset:1px;color:var(--text)}

    /* form controls */
    .quick-form{display:grid;gap:12px;margin-top:12px}
    .quick-form .btn{justify-self:start}
    .field label{display:block;margin-bottom:6px;color:var(--ds-text-secondary);font-size:.8125rem;font-weight:500}
    .input,.select,.textarea{width:100%;min-height:38px;padding:8px 12px;border:1px solid var(--ds-input-border);border-radius:var(--radius-sm);
      background:var(--surface3);color:var(--text);font:400 .875rem/1.4 var(--ds-font-sans);outline:none;
      transition:border-color .12s ease,box-shadow .12s ease}
    .input::placeholder,.textarea::placeholder{color:var(--dim)}
    .input:focus,.select:focus,.textarea:focus{border-color:var(--accent);box-shadow:var(--ds-focus-ring)}
    .input:disabled,.select:disabled,.textarea:disabled{opacity:.6;cursor:not-allowed}
    .textarea{min-height:105px;resize:vertical;line-height:1.55}
    .input[type="file"]{padding:6px 8px;line-height:1.6;color:var(--ds-text-secondary)}
    .input[type="file"]::file-selector-button{margin-right:10px;padding:4px 10px;border:1px solid var(--ds-border-strong);border-radius:var(--radius-xs);
      background:var(--surface2);color:var(--text);font:500 .8125rem/1.4 var(--ds-font-sans);cursor:pointer}
    .field label input[type="checkbox"],form > label input[type="checkbox"]{margin-right:6px;vertical-align:-2px}
    form > label{color:var(--ds-text-secondary);font-size:.875rem}

    /* editor */
    .workspace{min-width:0}
    .workspace-nav{display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:12px;min-height:32px}
    .workspace-nav > span{font-variant-numeric:tabular-nums}
    .question-card{overflow:hidden;border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}
    .question-head{display:flex;justify-content:space-between;gap:16px;align-items:center;padding:14px 20px;border-bottom:1px solid var(--border)}
    .question-head > .muted{flex-shrink:0;font-size:.8125rem}
    .question-title{display:flex;gap:8px;align-items:center;flex-wrap:wrap;min-width:0}
    .question-body{padding:20px}

    .tos-guide{margin-bottom:20px;padding:16px;border-radius:var(--radius-sm);background:var(--surface3)}
    .tos-guide > strong{display:block;font-size:.875rem;font-weight:600}
    .guide-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px 16px;margin-top:12px}
    .guide-item{min-width:0}
    .guide-item b{display:block;margin-bottom:2px;color:var(--muted);font-size:.75rem;font-weight:500}
    .guide-item span{display:block;color:var(--ds-text-secondary);font-size:.875rem;line-height:1.45;overflow-wrap:anywhere}

    .grid{display:grid;gap:16px}
    .grid-2{grid-template-columns:repeat(2,minmax(0,1fr))}
    .full{grid-column:1/-1}

    .editor-section{margin-top:20px;padding-top:16px;border-top:1px solid var(--border)}
    .editor-section h3{margin:0 0 8px;font-size:.9375rem;font-weight:600;line-height:1.35}
    .editor-section .side-title h3{margin:0}
    .side-title .btn{flex-shrink:0;white-space:nowrap}
    .editor-section > p.muted{margin:0 0 4px}
    .option-row{display:grid;grid-template-columns:28px 24px minmax(0,1fr) 36px;gap:8px;align-items:center;margin-top:8px}
    .option-label{color:var(--muted);font-size:.8125rem;font-weight:600;text-align:center}
    .option-row input[type="radio"]{width:16px;height:16px;margin:0 auto}
    .icon-btn{width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;padding:0;
      border:1px solid var(--ds-border-strong);border-radius:var(--radius-sm);background:var(--surface2);color:var(--muted);
      font-size:1.1rem;line-height:1;cursor:pointer;transition:background .12s ease,color .12s ease}
    .icon-btn:hover:not(:disabled){background:var(--ds-surface-hover);color:var(--text)}
    .icon-btn:disabled{opacity:.5;cursor:not-allowed}
    .image-preview{display:block;max-width:min(520px,100%);max-height:320px;margin-top:8px;border:1px solid var(--border);border-radius:var(--radius-sm)}

    .requirements{margin:16px 0 0;padding:12px 16px;border:1px solid var(--ds-warning-border);border-radius:var(--radius-sm);
      background:var(--ds-warning-soft);color:#fef3c7;font-size:.875rem;line-height:1.5}
    .requirements strong{font-weight:600}
    .requirements.good{border-color:var(--ds-success-border);background:var(--ds-success-soft);color:#d1fae5}
    .requirements ul{margin:8px 0 0;padding-left:18px}
    .footer-actions{display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap;margin-top:20px;padding-top:16px;border-top:1px solid var(--border)}
    .save-hint{color:var(--muted);font-size:.75rem}
    .hidden{display:none!important}

    @media(max-width:1180px){
      .builder-grid{grid-template-columns:minmax(0,1fr)}
      .builder-side{position:static;max-height:none;overflow:visible;padding-right:0}
      .summary{grid-template-columns:repeat(3,minmax(0,1fr))}
      .progress-card{grid-column:1/-1;padding-right:0;margin-bottom:4px}
      .guide-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
    }
    @media(max-width:900px){.main{padding:24px 20px 40px}}
    @media(max-width:760px){
      .top,.footer-actions{align-items:stretch;flex-direction:column}
      .top > div:first-child{flex:0 0 auto}
      .grid-2,.guide-grid{grid-template-columns:minmax(0,1fr)}
      .top > .actions > *,.footer-actions .actions > *{flex:1 1 auto}
      .top > .actions form .btn{width:100%}
      .option-row{grid-template-columns:24px 20px minmax(0,1fr) 32px}
      .icon-btn{width:32px;height:32px}
    }
    @media(max-width:640px){
      .main{padding:20px 16px 32px}
      .card{padding:16px}
      .question-head{padding:12px 16px}
      .question-body{padding:16px}
      .btn{white-space:normal;text-align:center}
    }
    @media(max-width:480px){
      .summary{grid-template-columns:minmax(0,1fr);gap:8px}
      .progress-card{margin-bottom:4px}
      .metric{flex-direction:row-reverse;justify-content:space-between;align-items:center;padding:10px 14px}
      .metric strong{font-size:1.125rem}
    }
    @media(prefers-reduced-motion:reduce){.btn,.input,.select,.textarea,.item-nav a,.icon-btn{transition:none}}
  </style>
    @include('partials.page-head', ['pageDescription' => 'Create, publish, and grade assessments for your classes.'])
</head>
<body>
@php
  $isDraft = $assessment->status === 'draft';
  $selectedType = old('question_type', $currentQuestion->question_type);
  $options = $currentQuestion->options->values();
  $optionValues = old('option_texts');
  if (! is_array($optionValues)) {
      $optionValues = $options->pluck('option_text')->all();
  }
  $highestOptionIndex = $optionValues === []
      ? -1
      : max(array_map('intval', array_keys($optionValues)));
  $optionSlots = max(4, min(10, $highestOptionIndex + 1));
  $correctOption = old('correct_option');
  if ($correctOption === null) {
      foreach ($options as $optionIndex => $option) {
          if ($option->is_correct) {
              $correctOption = $optionIndex;
              break;
          }
      }
  }
  $currentErrors = $currentQuestion->authoringErrors();
@endphp
<div class="layout">
  @include('partials.instructor-sidebar')
  <main class="main">
    <div class="wrap">
      <div class="top">
        <div>
          <div class="actions" style="margin-bottom:8px">
            <span class="badge {{ $isDraft ? 'warn' : ($assessment->status === 'published' ? 'good' : '') }}">{{ strtoupper($assessment->status) }}</span>
            @if($isDraft)
              <span class="muted small">Last saved: {{ $assessment->draft_saved_at?->diffForHumans() ?? 'Not saved yet' }}</span>
            @endif
          </div>
          <h1 class="title ds-page-title">{{ $assessment->title }}</h1>
          <p class="subtitle">Build one item at a time while DataSensei keeps every question aligned with its TOS topic, objective, Bloom level, and difficulty.</p>
        </div>
        <div class="actions">
          <a class="btn secondary" href="{{ route('instructor.assessments.index') }}">Assessments</a>
          @if($isDraft)
            <button class="btn secondary" type="submit" form="current-question-form" name="intent" value="draft_exit">Save Draft & Exit</button>
            <form method="POST" action="{{ route('instructor.assessments.publish', $assessment) }}" onsubmit="return confirm('Publish this assessment to students? Questions can no longer be edited after publishing.');">
              @csrf
              @method('PATCH')
              <button class="btn good" type="submit" @disabled(! $readyToPublish) title="{{ $readyToPublish ? 'Publish assessment' : 'Complete every TOS item before publishing' }}">Publish Quiz</button>
            </form>
          @elseif($assessment->status === 'published')
            <form method="POST" action="{{ route('instructor.assessments.close', $assessment) }}">
              @csrf
              @method('PATCH')
              <button class="btn warn" type="submit">Close Assessment</button>
            </form>
          @endif
        </div>
      </div>

      @if(session('success'))
        <div class="alert">{{ session('success') }}</div>
      @endif
      @if($errors->any())
        <div class="alert error">
          @foreach($errors->all() as $error){{ $error }}<br>@endforeach
        </div>
      @endif

      <section class="card summary" aria-label="Assessment authoring progress">
        <div class="progress-card">
          <div class="progress-line">
            <strong>TOS completion</strong>
            <span>{{ $progress['percent'] }}%</span>
          </div>
          <div class="progress-track" role="progressbar" aria-valuenow="{{ $progress['percent'] }}" aria-valuemin="0" aria-valuemax="100">
            <div class="progress-fill" style="width:{{ $progress['percent'] }}%"></div>
          </div>
          <span class="muted small" style="margin-top:8px">{{ $progress['complete'] }} of {{ $progress['total'] }} questions are ready to publish.</span>
        </div>
        <div class="metric"><strong>{{ $progress['complete'] }}</strong><span class="muted">Complete</span></div>
        <div class="metric"><strong>{{ $progress['in_progress'] }}</strong><span class="muted">In progress</span></div>
        <div class="metric"><strong>{{ $progress['not_started'] }}</strong><span class="muted">Not started</span></div>
      </section>

      <div class="builder-grid">
        <aside class="builder-side">
          <section class="card">
            <div class="side-title">
              <h2>TOS Item Guide</h2>
              <span class="badge">{{ $progress['total'] }} items</span>
            </div>
            <p class="muted small">Green items are complete. Yellow items have a saved partial draft. Select any number to continue it.</p>
            @foreach($tosGroups as $group)
              <div class="tos-group">
                <div class="tos-group-head">
                  <div class="tos-group-title">{{ $group['topic'] }}</div>
                  <span class="badge {{ $group['complete'] === $group['target'] ? 'good' : 'warn' }}">{{ $group['complete'] }}/{{ $group['target'] }}</span>
                </div>
                <div class="tos-meta">
                  @if($group['subtopic'])<span>{{ $group['subtopic'] }}</span>@endif
                  @if($group['bloom'])<span>{{ $group['bloom'] }}</span>@endif
                  @if($group['difficulty'])<span>{{ ucwords(str_replace('-', ' ', $group['difficulty'])) }}</span>@endif
                </div>
                <div class="item-nav">
                  @foreach($group['items'] as $item)
                    <a
                      href="{{ route('instructor.assessments.builder', ['assessment' => $assessment, 'item' => $item->item_number]) }}"
                      class="{{ $item->authoring_status }} {{ $item->id === $currentQuestion->id ? 'active' : '' }}"
                      title="Item {{ $item->item_number }} — {{ ucwords(str_replace('_', ' ', $item->authoring_status)) }}">
                      {{ $item->item_number }}
                    </a>
                  @endforeach
                </div>
              </div>
            @endforeach
          </section>

          @if($isDraft)
            <section class="card">
              <div class="side-title"><h2>Quick Setup</h2><span class="badge">Optional</span></div>
              <p class="muted small">Apply a type and point value without changing any TOS coverage. Started questions are never overwritten.</p>
              <form class="quick-form" method="POST" action="{{ route('instructor.assessments.questions.quick-setup', $assessment) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="current_question_id" value="{{ $currentQuestion->id }}">
                <div class="field">
                  <label for="quick-scope">Apply to</label>
                  <select class="select" id="quick-scope" name="scope" required>
                    <option value="tos_group">Not-started items in this TOS section</option>
                    <option value="all_unstarted">All not-started assessment items</option>
                  </select>
                </div>
                <div class="field">
                  <label for="quick-type">Question Type</label>
                  <select class="select" id="quick-type" name="question_type" required>
                    @foreach(\App\Models\AssessmentQuestion::TYPES as $value => $label)
                      <option value="{{ $value }}" @selected(($currentQuestion->question_type === 'unconfigured' ? 'multiple_choice' : $currentQuestion->question_type) === $value)>{{ $label }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="field">
                  <label for="quick-points">Points per Item</label>
                  <input class="input" id="quick-points" type="number" min="1" max="1000" name="points" value="{{ $currentQuestion->points }}" required>
                </div>
                <button class="btn small" type="submit">Apply Quick Setup</button>
              </form>
            </section>
          @endif
        </aside>

        <section class="workspace">
          <nav class="workspace-nav" aria-label="Question navigation">
            <div>
              @if($previousQuestion)
                <a class="btn secondary small" href="{{ route('instructor.assessments.builder', ['assessment' => $assessment, 'item' => $previousQuestion->item_number]) }}">← Previous</a>
              @endif
            </div>
            <span class="muted small">Item {{ $currentQuestion->item_number }} of {{ $progress['total'] }}</span>
            <div>
              @if($nextQuestion)
                <a class="btn secondary small" href="{{ route('instructor.assessments.builder', ['assessment' => $assessment, 'item' => $nextQuestion->item_number]) }}">Next →</a>
              @endif
            </div>
          </nav>

          <article class="question-card">
            <header class="question-head">
              <div class="question-title">
                <h2>Item {{ $currentQuestion->item_number }}</h2>
                <span class="badge {{ $currentQuestion->authoring_status === 'complete' ? 'good' : ($currentQuestion->authoring_status === 'in_progress' ? 'warn' : '') }}">
                  {{ ucwords(str_replace('_', ' ', $currentQuestion->authoring_status)) }}
                </span>
              </div>
              <span class="muted">{{ $currentQuestion->points }} point(s)</span>
            </header>
            <div class="question-body">
              <div class="tos-guide">
                <strong>Follow this TOS requirement</strong>
                <div class="guide-grid">
                  <div class="guide-item"><b>Topic / Subtopic</b><span>{{ $currentQuestion->topic_title }}{{ $currentQuestion->subtopic_title ? ', '.$currentQuestion->subtopic_title : '' }}</span></div>
                  <div class="guide-item"><b>Learning Objective</b><span>{{ $currentQuestion->learning_objective ?: 'Not specified' }}</span></div>
                  <div class="guide-item"><b>Bloom Level</b><span>{{ $currentQuestion->bloom_level ?: 'Not specified' }}</span></div>
                  <div class="guide-item"><b>Difficulty</b><span>{{ $currentQuestion->difficulty_slug ? ucwords(str_replace('-', ' ', $currentQuestion->difficulty_slug)) : 'Not specified' }}</span></div>
                </div>
              </div>

              <form
                id="current-question-form"
                method="POST"
                enctype="multipart/form-data"
                action="{{ route('instructor.assessments.questions.update', [$assessment, $currentQuestion]) }}"
                data-draft-form>
                @csrf
                @method('PATCH')

                <div class="grid grid-2">
                  <div class="field">
                    <label for="question-type">Question Type</label>
                    <select class="select" id="question-type" name="question_type" @disabled(! $isDraft)>
                      <option value="unconfigured" @selected($selectedType === 'unconfigured')>Choose a question type</option>
                      @foreach(\App\Models\AssessmentQuestion::TYPES as $value => $label)
                        <option value="{{ $value }}" @selected($selectedType === $value)>{{ $label }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="field">
                    <label for="question-points">Points</label>
                    <input class="input" id="question-points" type="number" min="1" max="1000" name="points" value="{{ old('points', $currentQuestion->points) }}" @disabled(! $isDraft) required>
                  </div>
                  <div class="field full">
                    <label for="question-text">Question</label>
                    <textarea class="textarea" id="question-text" name="question_text" placeholder="Write a question that measures the TOS objective above." @disabled(! $isDraft)>{{ old('question_text', $currentQuestion->question_text) }}</textarea>
                  </div>
                  <div class="field">
                    <label for="question-image">Optional Image</label>
                    <input class="input" id="question-image" type="file" name="question_image" accept="image/*" @disabled(! $isDraft)>
                    @if($currentQuestion->image_path)
                      <img class="image-preview" src="{{ asset('storage/'.$currentQuestion->image_path) }}" alt="Current illustration for item {{ $currentQuestion->item_number }}">
                      <label style="margin-top:8px;text-transform:none;letter-spacing:0">
                        <input type="checkbox" name="remove_image" value="1" @disabled(! $isDraft)> Remove current image
                      </label>
                    @endif
                  </div>
                  <div class="field">
                    <label for="answer-explanation">Answer Explanation</label>
                    <textarea class="textarea" id="answer-explanation" name="answer_explanation" placeholder="Optional feedback shown after grading." @disabled(! $isDraft)>{{ old('answer_explanation', $currentQuestion->answer_explanation) }}</textarea>
                  </div>
                </div>

                <section class="editor-section type-panel mcq-panel {{ $selectedType === 'multiple_choice' ? '' : 'hidden' }}">
                  <div class="side-title">
                    <h3>Multiple-choice Options</h3>
                    @if($isDraft)<button class="btn secondary small" id="add-option" type="button">+ Add Choice</button>@endif
                  </div>
                  <p class="muted small">Select the radio button beside the one correct answer.</p>
                  <div id="mcq-options" data-next-index="{{ $optionSlots }}">
                    @for($i = 0; $i < $optionSlots; $i++)
                      <div class="option-row" data-option-row>
                        <span class="option-label">{{ chr(65 + $i) }}</span>
                        <input type="radio" name="correct_option" value="{{ $i }}" @checked((string) $correctOption === (string) $i) @disabled(! $isDraft || $selectedType !== 'multiple_choice')>
                        <input class="input" name="option_texts[{{ $i }}]" value="{{ $optionValues[$i] ?? '' }}" placeholder="Choice {{ chr(65 + $i) }}" @disabled(! $isDraft || $selectedType !== 'multiple_choice')>
                        <button class="icon-btn remove-option" type="button" title="Remove this choice" @disabled(! $isDraft || $selectedType !== 'multiple_choice')>×</button>
                      </div>
                    @endfor
                  </div>
                </section>

                <section class="editor-section type-panel true-false-panel {{ $selectedType === 'true_false' ? '' : 'hidden' }}">
                  <h3>Correct Answer</h3>
                  <div class="field">
                    <label for="true-false-answer">Choose True or False</label>
                    <select class="select conditional-answer" id="true-false-answer" name="correct_answer" @disabled(! $isDraft || $selectedType !== 'true_false')>
                      <option value="">Select the correct answer</option>
                      <option value="True" @selected(strtolower((string) old('correct_answer', $currentQuestion->correct_answer)) === 'true')>True</option>
                      <option value="False" @selected(strtolower((string) old('correct_answer', $currentQuestion->correct_answer)) === 'false')>False</option>
                    </select>
                  </div>
                </section>

                <section class="editor-section type-panel text-answer-panel {{ in_array($selectedType, ['fill_blank', 'short_answer'], true) ? '' : 'hidden' }}">
                  <h3>Correct / Accepted Answer</h3>
                  <div class="field">
                    <label for="text-answer">Accepted Answer</label>
                    <textarea class="textarea conditional-answer" id="text-answer" name="correct_answer" placeholder="For short answer, enter one accepted answer per line." @disabled(! $isDraft || ! in_array($selectedType, ['fill_blank', 'short_answer'], true))>{{ old('correct_answer', $currentQuestion->correct_answer) }}</textarea>
                  </div>
                </section>

                <section class="editor-section type-panel essay-panel {{ $selectedType === 'essay' ? '' : 'hidden' }}">
                  <h3>Essay Scoring Rubric</h3>
                  <div class="field">
                    <label for="rubric-text">Rubric</label>
                    <textarea class="textarea" id="rubric-text" name="rubric_text" placeholder="State the expected answer elements and point allocation." @disabled(! $isDraft || $selectedType !== 'essay')>{{ old('rubric_text', $currentQuestion->rubric_text) }}</textarea>
                  </div>
                </section>

                <label style="display:block;margin-top:16px">
                  <input type="checkbox" name="is_required" value="1" @checked(old('is_required', $currentQuestion->is_required)) @disabled(! $isDraft)> Required question
                </label>

                @if($errors->any())
                  <div class="requirements">
                    <strong>Review the message shown above.</strong>
                    <div class="small" style="margin-top:8px">Your entered values are still in the form. Complete the missing requirement or use Save Draft.</div>
                  </div>
                @elseif($currentErrors === [])
                  <div class="requirements good"><strong>This item is complete and ready to publish.</strong></div>
                @else
                  <div class="requirements">
                    <strong>Still needed before this item is complete:</strong>
                    <ul>@foreach($currentErrors as $currentError)<li>{{ $currentError }}</li>@endforeach</ul>
                    <div class="small" style="margin-top:8px">You can still save these partial fields as a draft.</div>
                  </div>
                @endif

                <div class="footer-actions">
                  <span class="save-hint">Tip: press Ctrl+S or ⌘+S to save this item as a draft.</span>
                  @if($isDraft)
                    <div class="actions">
                      <button class="btn secondary" id="save-draft-button" type="submit" name="intent" value="draft">Save Draft</button>
                      <button class="btn" type="submit" name="intent" value="complete_next">Save & Next Item →</button>
                    </div>
                  @endif
                </div>
              </form>
            </div>
          </article>
        </section>
      </div>
    </div>
  </main>
</div>

<script>
(() => {
  const form = document.querySelector('[data-draft-form]');
  if (!form) return;

  const type = document.getElementById('question-type');
  const panels = {
    multiple_choice: document.querySelector('.mcq-panel'),
    true_false: document.querySelector('.true-false-panel'),
    text_answer: document.querySelector('.text-answer-panel'),
    essay: document.querySelector('.essay-panel'),
  };
  let dirty = false;
  let submitting = false;

  const setPanel = (panel, visible) => {
    if (!panel) return;
    panel.classList.toggle('hidden', !visible);
    panel.querySelectorAll('input, select, textarea, button').forEach(control => {
      control.disabled = !visible || {{ $isDraft ? 'false' : 'true' }};
    });
  };

  const syncType = () => {
    const value = type.value;
    setPanel(panels.multiple_choice, value === 'multiple_choice');
    setPanel(panels.true_false, value === 'true_false');
    setPanel(panels.text_answer, ['fill_blank', 'short_answer'].includes(value));
    setPanel(panels.essay, value === 'essay');
  };

  form.addEventListener('input', () => { dirty = true; });
  form.addEventListener('change', () => { dirty = true; });
  form.addEventListener('submit', () => {
    submitting = true;
    dirty = false;
  });

  window.addEventListener('beforeunload', event => {
    if (!dirty || submitting) return;
    event.preventDefault();
    event.returnValue = '';
  });

  document.addEventListener('keydown', event => {
    if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 's' && {{ $isDraft ? 'true' : 'false' }}) {
      event.preventDefault();
      form.requestSubmit(document.getElementById('save-draft-button'));
    }
  });

  const options = document.getElementById('mcq-options');
  const addOption = document.getElementById('add-option');
  const refreshOptionLabels = () => {
    [...options.querySelectorAll('[data-option-row]')].forEach((row, displayIndex) => {
      row.querySelector('.option-label').textContent = String.fromCharCode(65 + displayIndex);
      row.querySelector('input[type="radio"]').value = String(displayIndex);
      const optionInput = row.querySelector('input[type="text"], input:not([type])');
      optionInput.name = `option_texts[${displayIndex}]`;
      optionInput.placeholder = `Choice ${String.fromCharCode(65 + displayIndex)}`;
    });
    options.dataset.nextIndex = String(options.querySelectorAll('[data-option-row]').length);
  };

  options?.addEventListener('click', event => {
    const remove = event.target.closest('.remove-option');
    if (!remove) return;
    remove.closest('[data-option-row]')?.remove();
    dirty = true;
    refreshOptionLabels();
  });

  addOption?.addEventListener('click', () => {
    const rows = options.querySelectorAll('[data-option-row]');
    if (rows.length >= 10) return;
    const index = rows.length;
    const row = document.createElement('div');
    row.className = 'option-row';
    row.dataset.optionRow = '';
    row.innerHTML = `
      <span class="option-label">${String.fromCharCode(65 + rows.length)}</span>
      <input type="radio" name="correct_option" value="${index}">
      <input class="input" name="option_texts[${index}]" placeholder="New choice">
      <button class="icon-btn remove-option" type="button" title="Remove this choice">×</button>`;
    options.appendChild(row);
    refreshOptionLabels();
    row.querySelector('.input').focus();
    dirty = true;
  });

  type?.addEventListener('change', syncType);
  refreshOptionLabels();
  syncType();
})();
</script>
</body>
</html>
