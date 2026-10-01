<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $assessment->title }} — Assessment Builder</title>
  @include('instructor.assessments._styles')
  <style>
    .q-list{margin:0;padding:0;list-style:none}
    .q-item{padding:16px 18px;border-top:1px solid var(--border);scroll-margin-top:80px}
    .q-item:first-child{border-top:0}
    .q-head{display:flex;justify-content:space-between;align-items:baseline;flex-wrap:wrap;gap:6px 12px}
    .q-head h3{margin:0;font-size:.9375rem;font-weight:600}
    .q-meta{color:var(--muted);font-size:.8125rem}
    .q-text{margin:8px 0 10px;color:var(--text);font-size:.9375rem;line-height:1.6;white-space:pre-wrap;overflow-wrap:anywhere}
    .q-text.empty{color:var(--muted);font-style:italic}
    .q-plan{margin:4px 0 0;color:var(--muted);font-size:.8125rem}
    .q-show-choices{display:grid;gap:4px;margin:0 0 8px;padding:0;list-style:none}
    .q-show-choices li{display:flex;gap:8px;padding:6px 10px;border:1px solid var(--border);border-radius:var(--radius-xs,4px);color:var(--ds-text-secondary);font-size:.875rem}
    .q-show-choices li b{color:var(--muted)}
    .q-show-choices li.correct{border-color:var(--ds-success-border);color:var(--text)}
    .q-show-choices li em{margin-left:auto;color:var(--ds-success-text);font-style:normal;font-size:.8125rem;font-weight:600;white-space:nowrap}
    .q-answer{margin:0 0 8px;color:var(--ds-text-secondary);font-size:.875rem}
    .q-answer strong{color:var(--ds-success-text)}
    .q-row-actions{display:flex;flex-wrap:wrap;gap:8px;margin-top:10px}
    .q-edit{margin-top:12px}
    .q-edit > summary{display:inline-flex;list-style:none;cursor:pointer}
    .q-edit > summary::-webkit-details-marker{display:none}
    .q-edit[open] > summary{display:none}
    .q-edit .q-form{margin-top:4px;padding:14px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--surface2)}
    .q-choices{display:grid;gap:8px;margin:0 0 8px;padding:0;list-style:none}
    .q-choice{display:grid;grid-template-columns:auto minmax(0,1fr) auto;gap:8px;align-items:center}
    .q-correct{display:inline-flex;align-items:center;gap:6px;cursor:pointer}
    .q-letter{display:inline-block;width:16px;color:var(--muted);font-weight:600}
    .q-inline{display:inline-flex;align-items:center;gap:6px;margin-right:16px;color:var(--ds-text-secondary);font-size:.875rem;cursor:pointer}
    .q-image{display:block;max-width:320px;max-height:200px;margin:4px 0 8px;border:1px solid var(--border);border-radius:var(--radius-sm)}
    .q-block[hidden]{display:none}
    .publish-note{margin:0;color:var(--muted);font-size:.8125rem}
    .empty-list{padding:18px;color:var(--muted);font-size:.875rem}
  </style>
  @include('partials.page-head', ['pageTitle' => 'Assessment Builder', 'pageDescription' => 'Write the questions of an assessment, preview it and publish it.'])
</head>
<body>
<div class="layout">
  @include('partials.instructor-sidebar')
  <main class="main">
    <div class="wrap">
      <a class="crumb" href="{{ route('instructor.assessments.index') }}">&larr; Assessments</a>
      <div class="top">
        <div>
          <h1 class="title ds-page-title">{{ $assessment->title }}</h1>
          <p class="subtitle">
            {{ ucfirst($assessment->status) }}. {{ $assessment->classRoom->name ?? 'No class' }}.
            {{ $questions->count() }} {{ $questions->count() === 1 ? 'question' : 'questions' }}, {{ (int) $questions->sum('points') }} points.
          </p>
        </div>
        <div class="actions">
          <a class="btn" href="{{ route('instructor.assessments.preview', $assessment) }}">Preview</a>
          @if($assessment->status === 'draft')
            <form method="POST" action="{{ route('instructor.assessments.publish', $assessment) }}" onsubmit="return confirm('Publish this assessment? Students in the class can take it once it opens, and the questions can no longer be changed.');">
              @csrf @method('PATCH')
              <button class="btn primary" type="submit" @disabled(! $readyToPublish)>Publish</button>
            </form>
          @elseif($assessment->status === 'published')
            <form method="POST" action="{{ route('instructor.assessments.close', $assessment) }}" onsubmit="return confirm('Close this assessment? Students can no longer start it.');">
              @csrf @method('PATCH')
              <button class="btn" type="submit">Close</button>
            </form>
          @endif
          <a class="btn" href="{{ route('instructor.assessments.submissions', $assessment) }}">Submissions</a>
          @if($assessment->status !== 'draft')
            <a class="btn" href="{{ route('instructor.assessments.analytics', $assessment) }}">Diagnostics</a>
          @endif
        </div>
      </div>

      @if(session('success'))<div class="alert success" role="status">{{ session('success') }}</div>@endif
      @if(session('error'))<div class="alert danger" role="alert">{{ session('error') }}</div>@endif
      @if($errors->has('assessment'))
        <div class="alert danger" role="alert"><strong>The assessment cannot be published yet.</strong>
          <ul>@foreach($errors->get('assessment') as $messages)@foreach((array) $messages as $message)<li>{{ $message }}</li>@endforeach @endforeach</ul>
        </div>
      @elseif($errors->any() && ! old('form_key'))
        <div class="alert danger" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
      @endif

      @if($assessment->status === 'draft')
        <p class="publish-note" style="margin:-8px 0 16px">
          @if($questions->isEmpty())
            Add at least one question, then publish.
          @elseif($readyToPublish)
            Every question is complete. Preview it, then publish when you are ready.
          @else
            {{ $incomplete->count() }} {{ $incomplete->count() === 1 ? 'question needs' : 'questions need' }} finishing before you can publish: question {{ $incomplete->pluck('item_number')->join(', ', ' and ') }}.
          @endif
        </p>
      @else
        <div class="alert">This assessment is {{ $assessment->status }}, so its questions can no longer be changed.</div>
      @endif

      {{-- Settings --}}
      <section class="section" id="settings" aria-labelledby="settings-title">
        <div class="section-head">
          <h2 id="settings-title">Details</h2>
          @if($assessment->tos)<p>Planned with the Table of Specifications "{{ $assessment->tos->title }}".</p>@endif
        </div>
        <div class="section-body">
          @if($editable)
            <form method="POST" action="{{ route('instructor.assessments.settings.update', $assessment) }}">
              @csrf @method('PATCH')
              @include('instructor.assessments._settings_fields', ['assessment' => $assessment, 'tos' => null])
              <div class="form-foot"><button class="btn" type="submit">Save details</button></div>
            </form>
          @else
            <dl class="facts">
              <div><dt>Class</dt><dd>{{ $assessment->classRoom->name ?? 'No class' }}</dd></div>
              <div><dt>Purpose</dt><dd>{{ $assessment->purposeLabel() ?? 'No label' }}</dd></div>
              @if($assessment->topic_title)<div><dt>Topic</dt><dd>{{ $assessment->topic_title }}</dd></div>@endif
              @if($assessment->passing_score_percent)<div><dt>Passing score</dt><dd>{{ $assessment->passing_score_percent }}%</dd></div>@endif
              <div><dt>Duration</dt><dd>{{ $assessment->time_limit_minutes ? $assessment->time_limit_minutes.' minutes' : 'No time limit' }}</dd></div>
              <div><dt>Available from</dt><dd>{{ $assessment->available_at?->format('M d, Y, g:i A') ?? 'When published' }}</dd></div>
              <div><dt>Due date</dt><dd>{{ $assessment->due_at?->format('M d, Y, g:i A') ?? 'No due date' }}</dd></div>
              <div><dt>Attempts allowed</dt><dd>{{ $assessment->max_attempts }}</dd></div>
              <div><dt>Published</dt><dd>{{ $assessment->published_at?->format('M d, Y, g:i A') ?? 'Not yet' }}</dd></div>
            </dl>
            @if(filled($assessment->instructions))
              <p class="q-text" style="margin-top:14px">{{ $assessment->instructions }}</p>
            @endif
          @endif
        </div>
      </section>

      {{-- Questions --}}
      <section class="section" id="questions" aria-labelledby="questions-title">
        <div class="section-head">
          <h2 id="questions-title">Questions ({{ $questions->count() }})</h2>
          <p>{{ (int) $questions->sum('points') }} points in total.</p>
        </div>

        @if($questions->isEmpty())
          <p class="empty-list">No questions yet. Add the first one below.</p>
        @else
          <ol class="q-list">
            @foreach($questions as $question)
              @php
                $problems = $question->authoringErrors();
                $openThis = old('form_key') === 'q'.$question->id || ($openItem && $openItem === (int) $question->item_number) || ($editable && $question->question_type === 'unconfigured' && $loop->first && ! old('form_key'));
              @endphp
              <li class="q-item" id="question-{{ $question->id }}">
                <div class="q-head">
                  <h3>Question {{ $question->item_number }}</h3>
                  <span class="q-meta">
                    {{ $question->question_type === 'unconfigured' ? 'Type not chosen' : ($types[$question->question_type] ?? $question->type_label) }},
                    {{ $question->points }} {{ (int) $question->points === 1 ? 'point' : 'points' }}.
                    @if($problems === [])
                      <span class="state-good">Complete</span>
                    @else
                      <span class="state-warn">Needs: {{ collect($problems)->map(fn ($p) => lcfirst(rtrim($p, '.')))->join('; ') }}</span>
                    @endif
                  </span>
                </div>
                @if($question->table_of_specification_row_id && filled($question->topic_title))
                  <p class="q-plan">Planned topic: {{ $question->topic_title }}{{ $question->subtopic_title ? ', '.$question->subtopic_title : '' }}{{ $question->bloom_level ? '. Level: '.$question->bloom_level : '' }}{{ $question->difficulty_slug ? ', '.ucfirst($question->difficulty_slug) : '' }}.</p>
                @endif

                <p class="q-text {{ filled($question->question_text) ? '' : 'empty' }}">{{ filled($question->question_text) ? $question->question_text : 'Not written yet.' }}</p>
                @if($question->image_path)
                  <img class="q-image" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($question->image_path) }}" alt="Picture for question {{ $question->item_number }}">
                @endif

                @if($question->question_type === 'multiple_choice')
                  <ol class="q-show-choices">
                    @foreach($question->options->sortBy('order_index') as $option)
                      <li class="{{ $option->is_correct ? 'correct' : '' }}"><b>{{ chr(64 + $loop->iteration) }}.</b> <span>{{ $option->option_text }}</span>@if($option->is_correct)<em>Correct answer</em>@endif</li>
                    @endforeach
                  </ol>
                @elseif($question->question_type === 'true_false')
                  <p class="q-answer">Correct answer: <strong>{{ $question->correct_answer !== null && $question->correct_answer !== '' ? ucfirst($question->correct_answer) : 'not set' }}</strong></p>
                @elseif(in_array($question->question_type, ['short_answer', 'fill_blank'], true))
                  @php $accepted = collect(preg_split('/\r\n|\r|\n/', (string) $question->correct_answer))->map(fn ($a) => trim($a))->filter(fn ($a) => $a !== '')->values(); @endphp
                  <p class="q-answer">Correct answer: <strong>{{ $accepted->isEmpty() ? 'not set' : $accepted->join(', ') }}</strong></p>
                @elseif($question->question_type === 'essay')
                  <p class="q-answer">Checked by you.@if(filled($question->rubric_text)) Grading notes: {{ \Illuminate\Support\Str::limit($question->rubric_text, 200) }}@endif</p>
                @endif

                @if($editable)
                  <details class="q-edit" @if($openThis) open @endif>
                    <summary class="btn small">Edit question</summary>
                    @include('instructor.assessments._question_form', [
                      'question' => $question,
                      'action' => route('instructor.assessments.questions.update', [$assessment, $question]),
                      'method' => 'PATCH',
                      'submitLabel' => 'Save question',
                      'cancelHref' => '#question-'.$question->id,
                    ])
                    @if(old('form_key') === 'q'.$question->id && $errors->any())
                      <div class="alert danger" style="margin-top:10px"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                    @endif
                  </details>
                  <div class="q-row-actions">
                    <form method="POST" action="{{ route('instructor.assessments.questions.destroy', [$assessment, $question]) }}" onsubmit="return confirm('Remove question {{ $question->item_number }}?');">
                      @csrf @method('DELETE')
                      <button class="btn small danger" type="submit">Remove</button>
                    </form>
                  </div>
                @endif
              </li>
            @endforeach
          </ol>
        @endif
      </section>

      @if($editable)
        <section class="section" id="add-question" aria-labelledby="add-title">
          <div class="section-head">
            <h2 id="add-title">Add a question</h2>
            <p>Write one here, or copy finished questions from your bank.</p>
            <a class="btn small" href="{{ route('instructor.assessments.bank', $assessment) }}">Add from Question Bank</a>
          </div>
          <div class="section-body">
            @if(old('form_key') === 'new' && $errors->any())
              <div class="alert danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            @include('instructor.assessments._question_form', [
              'question' => null,
              'action' => route('instructor.assessments.questions.store', $assessment),
              'method' => 'POST',
              'submitLabel' => 'Add question',
            ])
          </div>
        </section>
      @endif
    </div>
  </main>
</div>

<script>
  (function () {
    const MAX_CHOICES = 10;

    const setupForm = (form) => {
      const select = form.querySelector('[data-type-select]');
      const blocks = form.querySelectorAll('.q-block');
      const choices = form.querySelector('[data-choices]');

      const showType = () => {
        const type = select ? select.value : '';
        blocks.forEach((block) => {
          const on = (block.dataset.for || '').split(' ').includes(type);
          block.hidden = !on;
          // Hidden fields are not sent, so True/False and short answer never overwrite each other.
          block.querySelectorAll('input, textarea, select, button').forEach((el) => { el.disabled = !on; });
        });
      };

      const renumber = () => {
        if (!choices) return;
        choices.querySelectorAll('.q-choice').forEach((row, index) => {
          const letter = String.fromCharCode(65 + index);
          row.querySelector('.q-letter').textContent = letter;
          row.querySelector('input[type="radio"]').value = String(index);
          const text = row.querySelector('input.input');
          text.name = 'option_texts[' + index + ']';
          text.placeholder = 'Choice ' + letter;
        });
      };

      form.addEventListener('click', (event) => {
        const add = event.target.closest('[data-add-choice]');
        const remove = event.target.closest('[data-remove-choice]');
        if (add && choices) {
          const rows = choices.querySelectorAll('.q-choice');
          if (rows.length >= MAX_CHOICES) return;
          const copy = rows[rows.length - 1].cloneNode(true);
          copy.querySelector('input.input').value = '';
          copy.querySelector('input[type="radio"]').checked = false;
          choices.appendChild(copy);
          renumber();
          copy.querySelector('input.input').focus();
        }
        if (remove && choices) {
          const rows = choices.querySelectorAll('.q-choice');
          if (rows.length <= 2) return;
          remove.closest('.q-choice').remove();
          renumber();
        }
      });

      const cancel = form.querySelector('[data-cancel-edit]');
      if (cancel) {
        cancel.addEventListener('click', () => {
          const details = form.closest('details');
          if (details) details.open = false;
        });
      }

      if (select) select.addEventListener('change', showType);
      showType();
    };

    document.querySelectorAll('[data-question-form]').forEach(setupForm);

    const open = document.querySelector('.q-edit[open]');
    if (open && !window.location.hash) open.closest('.q-item')?.scrollIntoView({ block: 'start' });
  })();
</script>
</body>
</html>
