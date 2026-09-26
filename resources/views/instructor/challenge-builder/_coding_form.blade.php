{{-- Instructor coding challenge form. Adapted from admin/coding-challenges/_form:
     no version or identity fields, the level is always University Student. --}}
@php
  $locked = (bool) ($hasHistory ?? false);
  $emptyTestCase = ['input' => '', 'expected_output' => '', 'is_hidden' => false];
  $emptyQuestion = [
    'title' => '', 'problem_description' => '', 'language' => 'python', 'starter_code' => '',
    'reference_solution' => '', 'time_limit_seconds' => 600, 'base_xp' => 100, 'test_cases' => [$emptyTestCase],
  ];
  // The last entry renders inside <template> and is cloned by the editor script.
  $questionRows = [];
  foreach (array_values($questions) as $qi => $q) { $questionRows[] = ['index' => $qi, 'question' => $q, 'template' => false]; }
  $questionRows[] = ['index' => '__Q__', 'question' => $emptyQuestion, 'template' => true];
@endphp
<div class="cc-editor-layout">
<form method="POST" action="{{ $formAction }}" data-coding-form data-check-url="{{ route('instructor.challenge-builder.check-tests') }}" data-locked="{{ $locked ? '1' : '0' }}">
  @csrf
  @if($formMethod !== 'POST')
    @method($formMethod)
  @endif
  <input type="hidden" name="type" value="coding">

  @if($locked)
    <div class="notice error">
      Students have already worked on this challenge. The title, description, availability, problem titles and reference solutions may be corrected, but problem descriptions, starter code, limits and test cases are frozen. Build a new challenge to change them.
    </div>
  @endif

  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Challenge Settings</h2>
        <p class="panel-subtitle">The title and description students see on the map, plus the challenge-level timer and XP. The challenge sits on the University Student level and is visible only to the classes you give it to.</p>
      </div>
    </div>
    <div class="panel-body">
      <div class="form-grid three">
        <div class="field" style="grid-column:span 2">
          <label for="challenge-title">Title</label>
          <input id="challenge-title" class="input" name="title" maxlength="189" value="{{ old('title', $challenge->title) }}" required>
        </div>
        <div class="field">
          <label for="challenge-status">Availability</label>
          <select id="challenge-status" class="select" name="is_active">
            <option value="0" @selected(!old('is_active', $challenge->is_active))>Unavailable / Draft</option>
            <option value="1" @selected((bool) old('is_active', $challenge->is_active))>Available to your classes</option>
          </select>
        </div>
        <div class="field">
          <label for="time-limit">Time Limit in Seconds</label>
          <input id="time-limit" class="input" type="number" name="time_limit_seconds" min="60" max="7200" value="{{ old('time_limit_seconds', $challenge->time_limit_seconds ?? 1800) }}" required>
        </div>
        <div class="field">
          <label for="base-xp">Base XP</label>
          <input id="base-xp" class="input" type="number" name="base_xp" min="0" max="10000" value="{{ old('base_xp', $challenge->base_xp ?? 100) }}" required>
        </div>
      </div>
      <div class="field" style="margin-top:16px">
        <label for="challenge-description">Description</label>
        <textarea id="challenge-description" class="textarea" name="description" maxlength="10000">{{ old('description', $challenge->description) }}</textarea>
      </div>
    </div>
  </section>

  <section class="panel" data-question-editor>
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Problems</h2>
        <p class="panel-subtitle">Students solve the problems in order. Each problem is graded against its test cases: a case passes when the program's output matches the expected output exactly, ignoring trailing whitespace.</p>
      </div>
      @unless($locked)
        <button class="btn small" type="button" data-add-question>Add Problem</button>
      @endunless
    </div>

    <div class="question-list" data-question-list>
      @foreach($questionRows as $row)
        @php
          $qi = $row['index'];
          $question = $row['question'];
          $isTemplate = $row['template'];
          $caseRows = [];
          foreach (array_values($question['test_cases'] ?? []) as $ci => $tc) { $caseRows[] = ['index' => $ci, 'case' => $tc]; }
        @endphp
        @if($isTemplate)<template data-question-template>@endif
        <article class="question-item cc-question">
          <div class="question-head">
            <strong>Problem <span data-question-number>{{ $isTemplate ? '' : $qi + 1 }}</span></strong>
            @unless($locked)
              <div class="action-row">
                <button class="btn small secondary" type="button" data-move-up>Move Up</button>
                <button class="btn small secondary" type="button" data-move-down>Move Down</button>
                <button class="btn small danger" type="button" data-remove-question>Remove</button>
              </div>
            @endunless
          </div>

          <div class="form-grid three">
            <div class="field" style="grid-column:span 2">
              <label>Problem Title</label>
              <input class="input" data-field="title" name="questions[{{ $qi }}][title]" maxlength="189" value="{{ $question['title'] ?? '' }}" placeholder="Optional, for example Sum of Two Numbers">
            </div>
            <div class="field">
              <label>Language</label>
              <select class="select" data-field="language" name="questions[{{ $qi }}][language]">
                <option value="python" selected>Python</option>
              </select>
            </div>
          </div>

          <div class="field" style="margin-top:16px">
            <label>Problem Description</label>
            <textarea class="textarea cc-tall" data-field="problem_description" name="questions[{{ $qi }}][problem_description]" maxlength="20000" required @readonly($locked) placeholder="Describe the task, the input format and the expected output. Plain text; line breaks are kept.">{{ $question['problem_description'] ?? '' }}</textarea>
          </div>

          <div class="form-grid" style="margin-top:16px">
            <div class="field">
              <label>Starter Code</label>
              <textarea class="textarea cc-code" data-field="starter_code" name="questions[{{ $qi }}][starter_code]" maxlength="50000" spellcheck="false" @readonly($locked) placeholder="# Code the student starts from (optional)">{{ $question['starter_code'] ?? '' }}</textarea>
            </div>
            <div class="field">
              <label>Reference Solution</label>
              <textarea class="textarea cc-code" data-field="reference_solution" name="questions[{{ $qi }}][reference_solution]" maxlength="50000" spellcheck="false" placeholder="# A solution that passes every test case (optional, never shown to students)">{{ $question['reference_solution'] ?? '' }}</textarea>
            </div>
          </div>

          <div class="form-grid" style="margin-top:16px">
            <div class="field">
              <label>Problem Time Limit in Seconds</label>
              <input class="input" type="number" data-field="time_limit_seconds" name="questions[{{ $qi }}][time_limit_seconds]" min="60" max="7200" value="{{ $question['time_limit_seconds'] ?? 600 }}" required @readonly($locked)>
            </div>
            <div class="field">
              <label>Problem Base XP</label>
              <input class="input" type="number" data-field="base_xp" name="questions[{{ $qi }}][base_xp]" min="0" max="10000" value="{{ $question['base_xp'] ?? 100 }}" required @readonly($locked)>
            </div>
          </div>

          <div class="cc-cases-head">
            <strong>Test Cases</strong>
            <span class="dim">Visible cases are shown to students as samples. Hidden cases are graded but never shown.</span>
          </div>
          <div class="cc-case-list" data-test-case-list>
            @foreach($caseRows as $caseRow)
              @php $ci = $caseRow['index']; $tc = $caseRow['case']; @endphp
              <div class="cc-case" data-test-case>
                <div class="cc-case-head">
                  <strong>Test case <span data-test-case-number>{{ $ci + 1 }}</span></strong>
                  <label class="cc-check">
                    <input type="checkbox" data-field="is_hidden" name="questions[{{ $qi }}][test_cases][{{ $ci }}][is_hidden]" value="1" @checked(filter_var($tc['is_hidden'] ?? false, FILTER_VALIDATE_BOOL)) @if($locked) onclick="return false" @endif>
                    Hidden from students
                  </label>
                  @unless($locked)
                    <div class="action-row">
                      <button class="btn small secondary" type="button" data-move-up aria-label="Move test case up">↑</button>
                      <button class="btn small secondary" type="button" data-move-down aria-label="Move test case down">↓</button>
                      <button class="btn small danger" type="button" data-remove-test-case>Remove</button>
                    </div>
                  @endunless
                </div>
                <div class="form-grid">
                  <div class="field">
                    <label>Input (stdin)</label>
                    <textarea class="textarea cc-code cc-short" data-field="input" name="questions[{{ $qi }}][test_cases][{{ $ci }}][input]" maxlength="10000" spellcheck="false" @readonly($locked) placeholder="Leave empty when the program reads no input">{{ $tc['input'] ?? '' }}</textarea>
                  </div>
                  <div class="field">
                    <label>Expected Output</label>
                    <textarea class="textarea cc-code cc-short" data-field="expected_output" name="questions[{{ $qi }}][test_cases][{{ $ci }}][expected_output]" maxlength="10000" spellcheck="false" required @readonly($locked)>{{ $tc['expected_output'] ?? '' }}</textarea>
                  </div>
                </div>
                <div class="cc-case-result" data-case-result hidden></div>
              </div>
            @endforeach
          </div>

          <div class="action-row cc-case-actions">
            @unless($locked)
              <button class="btn small secondary" type="button" data-add-test-case>Add Test Case</button>
            @endunless
            <button class="btn small" type="button" data-check-tests>Check test cases</button>
            <span class="dim" data-check-status aria-live="polite"></span>
          </div>
        </article>
        @if($isTemplate)</template>@endif
      @endforeach
    </div>

    <template data-test-case-template>
      <div class="cc-case" data-test-case>
        <div class="cc-case-head">
          <strong>Test case <span data-test-case-number></span></strong>
          <label class="cc-check">
            <input type="checkbox" data-field="is_hidden" value="1">
            Hidden from students
          </label>
          <div class="action-row">
            <button class="btn small secondary" type="button" data-move-up aria-label="Move test case up">↑</button>
            <button class="btn small secondary" type="button" data-move-down aria-label="Move test case down">↓</button>
            <button class="btn small danger" type="button" data-remove-test-case>Remove</button>
          </div>
        </div>
        <div class="form-grid">
          <div class="field">
            <label>Input (stdin)</label>
            <textarea class="textarea cc-code cc-short" data-field="input" maxlength="10000" spellcheck="false" placeholder="Leave empty when the program reads no input"></textarea>
          </div>
          <div class="field">
            <label>Expected Output</label>
            <textarea class="textarea cc-code cc-short" data-field="expected_output" maxlength="10000" spellcheck="false" required></textarea>
          </div>
        </div>
        <div class="cc-case-result" data-case-result hidden></div>
      </div>
    </template>
  </section>

  <div class="action-row">
    <button class="btn" type="submit">{{ $submitLabel }}</button>
    <a class="btn secondary" href="{{ $cancelUrl }}">Cancel</a>
  </div>
</form>

<aside class="cc-preview" data-coding-preview aria-live="polite">
  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Live Preview</h2>
        <p class="panel-subtitle">How students will see this challenge. Updates as you type. Reference solutions and hidden test cases are never shown to students.</p>
      </div>
    </div>
    <div class="panel-body">
      <h3 class="cc-preview-title" data-preview-title>Untitled challenge</h3>
      <p class="cc-preview-desc" data-preview-description hidden></p>
      <p class="cc-preview-meta"><span data-preview-xp>0</span> Base XP, <span data-preview-time>0</span> min time limit, <span data-preview-count>0</span> problems</p>
      <div data-preview-problems>
        <p class="cc-preview-empty">Add a problem to see it here.</p>
      </div>
    </div>
  </section>
</aside>
</div>

<style>
  .question-list { display:grid; gap:16px; padding:16px; }
  .question-item { padding:16px; border:1px solid var(--border); border-radius:var(--radius); background:var(--surface3); }
  .question-head { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:16px; }
  .question-head strong { color:var(--text); }
  .cc-tall { min-height:140px; }
  .cc-code { font-family:var(--ds-font-mono, ui-monospace, SFMono-Regular, Menlo, Consolas, monospace); font-size:.8125rem; line-height:1.5; white-space:pre; overflow:auto; tab-size:4; }
  .cc-code.cc-short { min-height:72px; }
  .cc-cases-head { display:flex; align-items:baseline; gap:12px; flex-wrap:wrap; margin:20px 0 10px; color:var(--text); }
  .cc-case-list { display:grid; gap:10px; }
  .cc-case { padding:12px; border:1px solid var(--border); border-radius:var(--radius-sm); background:var(--surface2); }
  .cc-case-head { display:flex; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:10px; color:var(--text); }
  .cc-case-head .action-row { margin-left:auto; }
  .cc-check { display:inline-flex; align-items:center; gap:6px; color:var(--muted); font-size:.8125rem; cursor:pointer; }
  .cc-check input { accent-color:var(--accent3); }
  .cc-case-actions { margin-top:12px; }
  .cc-case-result { margin-top:10px; padding:10px 12px; border:1px solid var(--border); border-radius:var(--radius-sm); font-size:.8125rem; line-height:1.5; }
  .cc-case-result.is-pass { border-color:var(--ds-success-border); background:var(--ds-success-soft); color:var(--ds-success-text); }
  .cc-case-result.is-fail { border-color:var(--ds-danger-border); background:var(--ds-danger-soft); color:var(--ds-danger-text); }
  .cc-case-result strong { display:block; margin-bottom:6px; }
  .cc-case-result-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:8px; color:var(--text); }
  .cc-case-result-grid span { display:block; margin-bottom:4px; color:var(--muted); font-size:.75rem; }
  .cc-case-result pre { margin:0; padding:8px; border:1px solid var(--border); border-radius:var(--radius-sm); background:var(--surface3); color:var(--text); font-family:var(--ds-font-mono, ui-monospace, SFMono-Regular, Menlo, Consolas, monospace); font-size:.75rem; line-height:1.5; white-space:pre-wrap; overflow-wrap:anywhere; max-height:200px; overflow:auto; }
  .cc-case-result .cc-stderr { margin-top:8px; }

  .cc-editor-layout { display:grid; grid-template-columns:minmax(0,1fr) minmax(320px,440px); gap:24px; align-items:start; }
  .cc-editor-layout > form { min-width:0; }
  .cc-preview { position:sticky; top:16px; }
  .cc-preview .panel-body { max-height:calc(100vh - 140px); overflow:auto; }
  .cc-preview-title { margin:0; color:var(--text); font-size:1.05rem; font-weight:600; line-height:1.35; overflow-wrap:anywhere; }
  .cc-preview-desc { margin:8px 0 0; color:var(--muted); font-size:.875rem; line-height:1.55; white-space:pre-wrap; overflow-wrap:anywhere; }
  .cc-preview-meta { margin:10px 0 0; color:var(--muted); font-size:.8125rem; }
  .cc-preview-empty { color:var(--muted); font-size:.875rem; }
  .cc-preview-problem { margin-top:16px; padding:14px; border:1px solid var(--border); border-radius:var(--radius); background:var(--surface3); }
  .cc-preview-qnum { margin-bottom:6px; color:var(--muted); font-size:.75rem; font-weight:500; }
  .cc-preview-ptitle { margin:0 0 8px; color:var(--text); font-size:.9375rem; font-weight:600; line-height:1.4; overflow-wrap:anywhere; }
  .cc-preview-pdesc { margin:0; color:var(--ds-text-secondary, var(--muted)); font-size:.875rem; line-height:1.6; white-space:pre-wrap; overflow-wrap:anywhere; }
  .cc-preview-limits { margin:10px 0 0; color:var(--muted); font-size:.75rem; }
  .cc-preview-label { margin:14px 0 6px; color:var(--muted); font-size:.75rem; font-weight:600; text-transform:uppercase; letter-spacing:.02em; }
  .cc-preview-code { margin:0; padding:10px 12px; border:1px solid var(--border); border-radius:var(--radius-sm); background:var(--surface2); color:var(--text); font-family:var(--ds-font-mono, ui-monospace, SFMono-Regular, Menlo, Consolas, monospace); font-size:.75rem; line-height:1.55; white-space:pre-wrap; overflow-wrap:anywhere; max-height:220px; overflow:auto; }
  .cc-preview-case { margin-top:8px; padding:10px 12px; border:1px solid var(--border); border-radius:var(--radius-sm); background:var(--surface2); font-size:.8125rem; line-height:1.5; }
  .cc-preview-case-row { display:flex; gap:8px; align-items:flex-start; }
  .cc-preview-case-row + .cc-preview-case-row { margin-top:4px; }
  .cc-preview-case-key { flex:0 0 auto; color:var(--muted); font-weight:600; }
  .cc-preview-case-val { min-width:0; color:var(--text); font-family:var(--ds-font-mono, ui-monospace, SFMono-Regular, Menlo, Consolas, monospace); font-size:.75rem; white-space:pre-wrap; overflow-wrap:anywhere; }
  .cc-preview-hidden { margin:10px 0 0; color:var(--muted); font-size:.8125rem; }
  @media (max-width:700px) { .question-head { align-items:flex-start; flex-direction:column; } .cc-case-head .action-row { margin-left:0; } .cc-case-result-grid { grid-template-columns:minmax(0,1fr); } }
  @media (max-width:1100px) { .cc-editor-layout { grid-template-columns:minmax(0,1fr); } .cc-preview { position:static; } .cc-preview .panel-body { max-height:none; } }
</style>
