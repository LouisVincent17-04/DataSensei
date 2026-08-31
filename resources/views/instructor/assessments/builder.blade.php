<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $assessment->title }} Builder</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#0d1320;--surface:#111c2d;--surface2:#1a2638;--surface3:#0f1928;--border:#263854;--text:#f8fafc;--muted:#91a4bf;--dim:#68809f;--accent:#3b82f6;--good:#10b981;--warn:#f59e0b;--bad:#ef4444;--radius:16px}
    *{box-sizing:border-box}
    body{margin:0;font-family:Inter,Arial,sans-serif;background:var(--bg);color:var(--text)}
    button,input,select,textarea{font:inherit}
    .layout{display:flex;min-height:100vh}
    .main{flex:1;padding:28px;min-width:0}
    .wrap{max-width:1500px;margin:0 auto}
    .top{display:flex;justify-content:space-between;align-items:flex-start;gap:20px;margin-bottom:20px}
    .title{font-size:2rem;font-weight:900;margin:0}
    .subtitle{color:var(--muted);line-height:1.6;margin:8px 0 0}
    .actions{display:flex;gap:9px;flex-wrap:wrap;align-items:center}
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;border:1px solid transparent;border-radius:10px;padding:10px 14px;font-weight:800;text-decoration:none;cursor:pointer;background:var(--accent);color:#fff}
    .btn.secondary{background:var(--surface2);border-color:var(--border);color:var(--text)}
    .btn.good{background:var(--good)}
    .btn.warn{background:var(--warn);color:#111827}
    .btn:disabled{opacity:.45;cursor:not-allowed}
    .btn.small{padding:8px 10px;font-size:.82rem}
    .badge{display:inline-flex;align-items:center;gap:6px;padding:5px 10px;border-radius:999px;border:1px solid var(--border);background:var(--surface2);font-size:.74rem;font-weight:800}
    .badge.good{color:#a7f3d0;border-color:rgba(16,185,129,.45);background:rgba(16,185,129,.10)}
    .badge.warn{color:#fde68a;border-color:rgba(245,158,11,.45);background:rgba(245,158,11,.10)}
    .badge.bad{color:#fecaca;border-color:rgba(239,68,68,.45);background:rgba(239,68,68,.10)}
    .muted{color:var(--muted)}
    .small{font-size:.82rem}
    .alert{padding:13px 15px;border-radius:12px;margin-bottom:16px;border:1px solid rgba(16,185,129,.35);background:rgba(16,185,129,.10);color:#a7f3d0}
    .alert.error{border-color:rgba(239,68,68,.35);background:rgba(239,68,68,.10);color:#fecaca}
    .card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:18px;margin-bottom:16px}
    .summary{display:grid;grid-template-columns:1.4fr repeat(3,minmax(130px,.55fr));gap:12px;align-items:stretch}
    .progress-card{display:flex;flex-direction:column;justify-content:center}
    .progress-line{display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:9px}
    .progress-track{height:10px;background:var(--surface3);border:1px solid var(--border);border-radius:999px;overflow:hidden}
    .progress-fill{height:100%;background:linear-gradient(90deg,var(--accent),var(--good));border-radius:inherit}
    .metric{background:var(--surface2);border:1px solid var(--border);border-radius:13px;padding:14px}
    .metric strong{font-size:1.55rem;display:block;margin-bottom:3px}
    .builder-grid{display:grid;grid-template-columns:minmax(280px,340px) minmax(0,1fr);gap:18px;align-items:start}
    .builder-side{position:sticky;top:18px;max-height:calc(100vh - 36px);overflow:auto;padding-right:3px}
    .side-title{display:flex;justify-content:space-between;gap:10px;align-items:center;margin-bottom:12px}
    .side-title h2,.question-title h2{font-size:1.05rem;margin:0}
    .tos-group{border:1px solid var(--border);border-radius:12px;background:var(--surface3);padding:12px;margin-top:10px}
    .tos-group-head{display:flex;justify-content:space-between;gap:8px;align-items:flex-start}
    .tos-group-title{font-weight:800;line-height:1.35}
    .tos-meta{display:flex;gap:5px;flex-wrap:wrap;margin-top:7px}
    .tos-meta span{font-size:.69rem;color:var(--muted);border:1px solid var(--border);border-radius:999px;padding:3px 6px}
    .item-nav{display:flex;flex-wrap:wrap;gap:6px;margin-top:10px}
    .item-nav a{width:34px;height:34px;border-radius:8px;display:flex;align-items:center;justify-content:center;text-decoration:none;background:var(--surface2);border:1px solid var(--border);color:var(--muted);font-size:.78rem;font-weight:900}
    .item-nav a.complete{color:#a7f3d0;border-color:rgba(16,185,129,.5);background:rgba(16,185,129,.08)}
    .item-nav a.in_progress{color:#fde68a;border-color:rgba(245,158,11,.5);background:rgba(245,158,11,.08)}
    .item-nav a.active{outline:2px solid var(--accent);outline-offset:1px;color:#fff}
    .quick-form{display:grid;gap:10px}
    .field label{display:block;color:var(--dim);font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;margin-bottom:7px}
    .input,.select,.textarea{width:100%;background:var(--surface3);border:1px solid var(--border);color:var(--text);border-radius:10px;padding:10px 12px}
    .input:focus,.select:focus,.textarea:focus{outline:2px solid rgba(59,130,246,.45);border-color:var(--accent)}
    .input:disabled,.select:disabled,.textarea:disabled{opacity:.65;cursor:not-allowed}
    .textarea{min-height:105px;resize:vertical;line-height:1.5}
    .workspace-nav{display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:12px}
    .question-card{background:var(--surface);border:1px solid var(--border);border-radius:18px;overflow:hidden}
    .question-head{padding:16px 18px;background:var(--surface2);display:flex;justify-content:space-between;gap:14px;align-items:center}
    .question-title{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
    .question-body{padding:18px}
    .tos-guide{border-left:4px solid var(--accent);background:rgba(59,130,246,.08);border-radius:12px;padding:14px;margin-bottom:16px}
    .guide-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-top:10px}
    .guide-item{background:var(--surface3);border:1px solid var(--border);border-radius:10px;padding:10px;min-width:0}
    .guide-item b{display:block;font-size:.67rem;text-transform:uppercase;letter-spacing:.06em;color:var(--dim);margin-bottom:5px}
    .guide-item span{display:block;overflow-wrap:anywhere;line-height:1.4}
    .grid{display:grid;gap:14px}
    .grid-2{grid-template-columns:repeat(2,minmax(0,1fr))}
    .full{grid-column:1/-1}
    .editor-section{border:1px solid var(--border);background:var(--surface3);border-radius:12px;padding:14px;margin-top:14px}
    .editor-section h3{font-size:.95rem;margin:0 0 10px}
    .option-row{display:grid;grid-template-columns:34px 28px minmax(0,1fr) 36px;gap:8px;align-items:center;margin-top:8px}
    .option-label{font-weight:900;color:var(--muted);text-align:center}
    .icon-btn{width:34px;height:34px;border:1px solid var(--border);border-radius:8px;background:var(--surface2);color:var(--muted);cursor:pointer}
    .image-preview{display:block;max-width:520px;max-height:320px;border-radius:12px;border:1px solid var(--border);margin-top:10px}
    .requirements{margin:14px 0 0;padding:12px 14px;border:1px solid rgba(245,158,11,.35);background:rgba(245,158,11,.08);border-radius:12px;color:#fde68a}
    .requirements.good{border-color:rgba(16,185,129,.35);background:rgba(16,185,129,.08);color:#a7f3d0}
    .requirements ul{margin:7px 0 0;padding-left:18px}
    .footer-actions{display:flex;justify-content:space-between;gap:12px;align-items:center;margin-top:18px;padding-top:16px;border-top:1px solid var(--border)}
    .save-hint{font-size:.78rem;color:var(--muted)}
    .hidden{display:none!important}
    @media(max-width:1180px){.builder-grid{grid-template-columns:1fr}.builder-side{position:static;max-height:none}.summary{grid-template-columns:repeat(3,1fr)}.progress-card{grid-column:1/-1}.guide-grid{grid-template-columns:repeat(2,1fr)}}
    @media(max-width:760px){.main{padding:18px}.top,.workspace-nav,.footer-actions{align-items:stretch;flex-direction:column}.grid-2,.summary,.guide-grid{grid-template-columns:1fr}.progress-card{grid-column:auto}.actions .btn,.actions form,.actions form .btn{width:100%}.option-row{grid-template-columns:30px 24px minmax(0,1fr) 34px}.question-body{padding:14px}}
  </style>
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
                  <div class="guide-item"><b>Topic / Subtopic</b><span>{{ $currentQuestion->topic_title }}{{ $currentQuestion->subtopic_title ? ' · '.$currentQuestion->subtopic_title : '' }}</span></div>
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

                <label style="display:block;margin-top:14px">
                  <input type="checkbox" name="is_required" value="1" @checked(old('is_required', $currentQuestion->is_required)) @disabled(! $isDraft)> Required question
                </label>

                @if($errors->any())
                  <div class="requirements">
                    <strong>Review the message shown above.</strong>
                    <div class="small" style="margin-top:7px">Your entered values are still in the form. Complete the missing requirement or use Save Draft.</div>
                  </div>
                @elseif($currentErrors === [])
                  <div class="requirements good"><strong>This item is complete and ready to publish.</strong></div>
                @else
                  <div class="requirements">
                    <strong>Still needed before this item is complete:</strong>
                    <ul>@foreach($currentErrors as $currentError)<li>{{ $currentError }}</li>@endforeach</ul>
                    <div class="small" style="margin-top:7px">You can still save these partial fields as a draft.</div>
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
