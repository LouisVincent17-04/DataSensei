{{--
    The visual module editor (DataSensei Updates 5, task 5), shared by the two
    admin module editors:

      editorMode "library"  Instructor Module Library (ModuleLibraryItem):
                            content_sections + mcq_questions, class-only
      editorMode "public"   DataSensei Modules (Module): its lessons as
                            section cards + review_questions, open to all

    Learning Content is a form-builder of sections and content blocks
    (DataSensei Updates 6): each block (heading, paragraph, list, note,
    Python/SQL code, walkthrough, knowledge check, ...) is its own card and
    "+ Add Content" adds one. Existing content arrives already converted to
    blocks (ModuleBlockConverter); no HTML or JSON is shown. Review questions
    are edited as question cards. public/js/admin-module-editor.js sends it
    all back as JSON in hidden fields; the learning outcomes are ordinary form
    fields (learning_outcomes[]).

    Saving happens in the background (DataSensei Updates 7): the form is sent
    with fetch, the page does not reload, and the admin stays on the same tab,
    section and scroll position. The action bar shows "Saving...", "Saved" or
    "Failed to save"; server validation errors appear beside the fields they
    belong to. Without fetch the form still posts normally.

    Expects: $editorMode, $module, $formAction, $formMethod, $cancelUrl,
    $basicPartial (the Basic Information fields), $isPublished, $sections,
    $questions, $outcomes, $previewUrl, $contentLocked, $lockedNotice,
    $audience (who sees a published module), $sectionPreviewUrl (public only).
--}}
@php
  $isPublic = $editorMode === 'public';
  $sectionsField = $isPublic ? 'lessons_json' : 'content_sections_json';
  $questionsField = $isPublic ? 'review_questions_json' : 'mcq_questions_json';
  $hasOld = session()->hasOldInput();

  $decodeOld = function (string $field, array $fallback): array {
      if (! session()->hasOldInput($field)) {
          return $fallback;
      }
      $decoded = json_decode((string) old($field), true);

      return is_array($decoded) ? array_values($decoded) : $fallback;
  };

  $initialSections = $decodeOld($sectionsField, array_values($sections ?? []));
  $initialQuestions = $decodeOld($questionsField, array_values($questions ?? []));
  $initialOutcomes = $hasOld ? array_values((array) old('learning_outcomes', [])) : array_values($outcomes ?? []);

  $firstErrorKey = $errors->keys()[0] ?? null;
  $errorTab = null;
  if ($firstErrorKey !== null) {
      $errorTab = match (true) {
          str_starts_with($firstErrorKey, 'learning_outcomes') => 'outcomes',
          $firstErrorKey === $sectionsField => 'content',
          $firstErrorKey === $questionsField => 'questions',
          default => 'basic',
      };
  }

  $editorConfig = [
      'mode' => $editorMode,
      'contentLocked' => (bool) ($contentLocked ?? false),
      'previewUrl' => $previewUrl,
      'sectionPreviewUrl' => $sectionPreviewUrl ?? null,
      'blockPreviewUrl' => route('admin.lessons.preview'),
      'imageUploadUrl' => route('admin.lessons.images.store'),
      'moduleId' => $isPublic && $module->exists ? $module->id : null,
      'difficulties' => \App\Services\ModuleEditorContent::DIFFICULTIES,
      'errorTab' => $errorTab,
      'isPublished' => (bool) $isPublished,
  ];
  $jsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE;
@endphp

<form method="POST" action="{{ $formAction }}" class="me-form" data-module-editor-form novalidate>
  @csrf
  @if($formMethod !== 'POST')
    @method($formMethod)
  @endif

  <input type="hidden" name="{{ $sectionsField }}" value="" disabled data-sections-input>
  <input type="hidden" name="{{ $questionsField }}" value="" disabled data-questions-input>

  <div class="me" data-module-editor>
    <div class="notice error me-problems" role="alert" data-editor-problems hidden></div>
    <div class="notice me-saved-notice" role="status" data-editor-notice hidden></div>

    <div class="me-tabs" role="tablist" aria-label="Module editor">
      <button type="button" class="me-tab" role="tab" id="me-tab-basic" aria-controls="me-panel-basic" aria-selected="true" data-tab="basic">Basic Information</button>
      <button type="button" class="me-tab" role="tab" id="me-tab-outcomes" aria-controls="me-panel-outcomes" aria-selected="false" tabindex="-1" data-tab="outcomes">Intended Learning Outcomes (<span data-count="outcomes">{{ count(array_filter($initialOutcomes, fn ($o) => trim((string) $o) !== '')) }}</span>)</button>
      <button type="button" class="me-tab" role="tab" id="me-tab-content" aria-controls="me-panel-content" aria-selected="false" tabindex="-1" data-tab="content">Learning Content (<span data-count="sections">{{ count($initialSections) }}</span>)</button>
      <button type="button" class="me-tab" role="tab" id="me-tab-questions" aria-controls="me-panel-questions" aria-selected="false" tabindex="-1" data-tab="questions">Embedded Review Questions (<span data-count="questions">{{ count($initialQuestions) }}</span>)</button>
      <button type="button" class="me-tab" role="tab" id="me-tab-publish" aria-controls="me-panel-publish" aria-selected="false" tabindex="-1" data-tab="publish">Preview &amp; Publish</button>
    </div>

    {{-- Basic Information --}}
    <div class="me-panel" role="tabpanel" id="me-panel-basic" aria-labelledby="me-tab-basic" data-tab-panel="basic">
      @include($basicPartial)
    </div>

    {{-- What Students Will Learn --}}
    <div class="me-panel" role="tabpanel" id="me-panel-outcomes" aria-labelledby="me-tab-outcomes" data-tab-panel="outcomes" hidden>
      <section class="panel">
        <div class="panel-head">
          <div class="panel-heading">
            <h2 class="panel-title">What Students Will Learn</h2>
            <p class="panel-subtitle">The module's intended learning outcomes: short statements of what a learner will be able to do after it. Students see them at the start of the module. They only describe the module; nothing is scored or tracked against them. A module needs at least one before it can be published.</p>
          </div>
        </div>
        <div class="panel-body">
          <ol class="me-outcomes" data-outcome-list>
            @foreach($initialOutcomes as $outcome)
              <li class="me-outcome" data-outcome>
                <textarea class="textarea" name="learning_outcomes[]" rows="2" maxlength="{{ \App\Services\ModuleEditorContent::MAX_OUTCOME_LENGTH }}" aria-label="Learning outcome">{{ $outcome }}</textarea>
                <div class="me-row-actions">
                  <button type="button" class="btn small secondary" data-outcome-act="up" title="Move up" aria-label="Move outcome up">↑</button>
                  <button type="button" class="btn small secondary" data-outcome-act="down" title="Move down" aria-label="Move outcome down">↓</button>
                  <button type="button" class="btn small danger" data-outcome-act="remove">Remove</button>
                </div>
              </li>
            @endforeach
          </ol>
          <p class="me-empty" data-outcome-empty @if(count($initialOutcomes) > 0) hidden @endif>No learning outcomes yet.</p>
          <button type="button" class="btn small secondary" data-add-outcome>+ Add outcome</button>
          <p class="field-help">Start each one with what the learner will do, for example "Explain how Python runs a script line by line."</p>
        </div>
      </section>
      <template data-outcome-template>
        <li class="me-outcome" data-outcome>
          <textarea class="textarea" name="learning_outcomes[]" rows="2" maxlength="{{ \App\Services\ModuleEditorContent::MAX_OUTCOME_LENGTH }}" aria-label="Learning outcome"></textarea>
          <div class="me-row-actions">
            <button type="button" class="btn small secondary" data-outcome-act="up" title="Move up" aria-label="Move outcome up">↑</button>
            <button type="button" class="btn small secondary" data-outcome-act="down" title="Move down" aria-label="Move outcome down">↓</button>
            <button type="button" class="btn small danger" data-outcome-act="remove">Remove</button>
          </div>
        </li>
      </template>
    </div>

    {{-- Learning Content --}}
    <div class="me-panel" role="tabpanel" id="me-panel-content" aria-labelledby="me-tab-content" data-tab-panel="content" hidden>
      <section class="panel me-list-panel">
        <div class="panel-head">
          <div class="panel-heading">
            <h2 class="panel-title">Learning Content</h2>
            <p class="panel-subtitle">
              @if($isPublic)
                Each section is one lesson students read in order in the learning room. Open a section to see its content blocks; use + Add Content to add text, lists, notes, Python or SQL code, images and more. Drag by the handle or use the arrows to reorder.
              @else
                The sections students and instructors read in this module, in order. Open a section to see its content blocks; use + Add Content to add text, lists, notes, Python or SQL code, images and more. Drag by the handle or use the arrows to reorder.
              @endif
            </p>
          </div>
          <div class="action-row">
            <button type="button" class="btn small secondary" data-collapse-all="sections">Collapse all</button>
            @unless($contentLocked ?? false)
              <button type="button" class="btn small" data-add-section>+ Add section</button>
            @endunless
          </div>
        </div>
        @if($contentLocked ?? false)
          <div class="panel-body me-locked">{{ $lockedNotice }}</div>
        @endif
        <div class="me-cards" data-section-list aria-live="polite"></div>
        <p class="me-empty me-empty-pad" data-section-empty hidden>No sections yet. Add the first one.</p>
        <noscript><p class="me-empty me-empty-pad">The module editor needs JavaScript. Turn it on to edit the learning content.</p></noscript>
        @unless($contentLocked ?? false)
          <div class="me-list-foot"><button type="button" class="btn small secondary" data-add-section>+ Add section</button></div>
        @endunless
      </section>
    </div>

    {{-- Embedded Review Questions --}}
    <div class="me-panel" role="tabpanel" id="me-panel-questions" aria-labelledby="me-tab-questions" data-tab-panel="questions" hidden>
      <section class="panel me-list-panel">
        <div class="panel-head">
          <div class="panel-heading">
            <h2 class="panel-title">Embedded Review Questions</h2>
            <p class="panel-subtitle">Practice questions inside the module. Students pick an answer and check it themselves; nothing is scored or recorded.</p>
          </div>
          <div class="action-row">
            <input type="search" class="input me-filter" placeholder="Find a question or topic" aria-label="Find a question or topic" data-question-filter data-no-dirty>
            <button type="button" class="btn small secondary" data-collapse-all="questions">Collapse all</button>
            @unless($contentLocked ?? false)
              <button type="button" class="btn small" data-add-question>+ Add question</button>
            @endunless
          </div>
        </div>
        @if($contentLocked ?? false)
          <div class="panel-body me-locked">{{ $lockedNotice }}</div>
        @endif
        <p class="me-filter-status" data-question-filter-status hidden></p>
        <div class="me-cards" data-question-list aria-live="polite"></div>
        <p class="me-empty me-empty-pad" data-question-empty hidden>No review questions yet.</p>
        @unless($contentLocked ?? false)
          <div class="me-list-foot"><button type="button" class="btn small secondary" data-add-question>+ Add question</button></div>
        @endunless
      </section>
    </div>

    {{-- Preview & Publish --}}
    <div class="me-panel" role="tabpanel" id="me-panel-publish" aria-labelledby="me-tab-publish" data-tab-panel="publish" hidden>
      <section class="panel">
        <div class="panel-head">
          <div class="panel-heading">
            <h2 class="panel-title">Preview &amp; Publish</h2>
            <p class="panel-subtitle">{{ $audience }}</p>
          </div>
        </div>
        <div class="panel-body">
          <dl class="me-summary">
            <div><dt>Status</dt><dd data-status-text>{{ $isPublished ? 'Published' : 'Draft, not published' }}</dd></div>
            <div><dt>What Students Will Learn</dt><dd><span data-count="outcomes">{{ count(array_filter($initialOutcomes, fn ($o) => trim((string) $o) !== '')) }}</span> outcomes<span class="me-warning" data-outcome-warning @if(count(array_filter($initialOutcomes, fn ($o) => trim((string) $o) !== '')) > 0) hidden @endif>. Add at least one before publishing.</span></dd></div>
            <div><dt>Learning Content</dt><dd><span data-count="sections">{{ count($initialSections) }}</span> sections</dd></div>
            <div><dt>Embedded Review Questions</dt><dd><span data-count="questions">{{ count($initialQuestions) }}</span> questions</dd></div>
          </dl>
          <p class="field-help">Preview Module opens what is in the editor now, saved or not, in a new tab, the way students will see it.</p>
          <div class="action-row" style="margin-top:12px">
            <button type="button" class="btn secondary" data-preview-module>Preview Module</button>
          </div>
        </div>
      </section>
    </div>

    <div class="me-actionbar">
      <p class="me-state" data-editor-state role="status" aria-live="polite">{{ $isPublished ? 'Published' : 'Draft' }}</p>
      <div class="action-row">
        <a class="btn secondary me-cancel" href="{{ $cancelUrl }}" data-editor-cancel>Cancel</a>
        <button type="button" class="btn secondary" data-preview-module>Preview Module</button>
        {{-- Both sets are rendered; the editor shows the one for the current
             status, and switches after a background save publishes or
             unpublishes the module. --}}
        <button class="btn" type="submit" name="intent" value="save" data-when="published" @unless($isPublished) hidden disabled @endunless>Save Changes</button>
        <button class="btn secondary" type="submit" name="intent" value="unpublish" data-when="published" data-confirm="{{ $isPublic ? 'Unpublish this module? Students will no longer see it.' : 'Unpublish this module version? Instructors will no longer be able to assign it.' }}" @unless($isPublished) hidden disabled @endunless>Unpublish</button>
        <button class="btn secondary" type="submit" name="intent" value="draft" data-when="draft" @if($isPublished) hidden disabled @endif>Save Draft</button>
        <button class="btn green" type="submit" name="intent" value="publish" data-when="draft" @if($isPublished) hidden disabled @endif>Publish</button>
      </div>
    </div>
  </div>
</form>

<script type="application/json" id="module-editor-config">{!! json_encode($editorConfig, $jsonFlags) !!}</script>
<script type="application/json" id="module-editor-data">{!! json_encode(['sections' => $initialSections, 'questions' => $initialQuestions], $jsonFlags) !!}</script>

@push('head')
<link rel="stylesheet" href="{{ asset('vendor/codemirror/codemirror.bundle.css') }}">
<style>
  /* Keeps focused fields clear of the sticky top bar and save bar. */
  html { scroll-padding-top:80px; scroll-padding-bottom:96px; }
  .me-form { padding-bottom:8px; }
  .me-tabs { display:flex; gap:4px; flex-wrap:wrap; margin:0 0 20px; border-bottom:1px solid var(--border); }
  .me-tab { position:relative; min-height:40px; padding:0 14px; border:0; background:none; color:var(--muted); font:inherit; font-size:.875rem; font-weight:500; cursor:pointer; }
  .me-tab:hover { color:var(--text); }
  .me-tab[aria-selected="true"] { color:var(--text); }
  .me-tab[aria-selected="true"]::after { content:''; position:absolute; left:10px; right:10px; bottom:-1px; height:2px; border-radius:2px; background:var(--accent); }
  .me-tab:focus-visible { outline:0; box-shadow:var(--ds-focus-ring); border-radius:6px; }
  .me-problems ul { margin:8px 0 0 18px; }

  .me-outcomes { display:grid; grid-template-columns:minmax(0,1fr); gap:10px; margin:0 0 12px; padding:0; list-style:none; counter-reset:outcome; }
  .me-outcome { display:grid; grid-template-columns:28px minmax(0,1fr) auto; gap:10px; align-items:start; counter-increment:outcome; }
  .me-outcome::before { content:counter(outcome) "."; padding-top:9px; color:var(--muted); font-size:.875rem; font-variant-numeric:tabular-nums; text-align:right; }
  .me-outcome .textarea { min-height:60px; }
  .me-row-actions { display:flex; gap:6px; flex-wrap:wrap; }
  .me-empty { margin:0 0 12px; color:var(--muted); font-size:.875rem; }
  .me-empty-pad { padding:16px 20px; margin:0; }
  .field-help { margin-top:8px; color:var(--muted); font-size:.75rem; line-height:1.5; }
  .me-locked { color:var(--ds-warning-text, var(--text)); background:var(--ds-warning-soft); border-bottom:1px solid var(--ds-warning-border); font-size:.875rem; line-height:1.55; }

  .me-list-panel { overflow:visible; }
  .me-cards { display:grid; grid-template-columns:minmax(0,1fr); gap:8px; padding:16px 20px; }
  .me-cards:empty { display:none; }
  .me-list-foot { padding:0 20px 16px; }
  .me-filter { width:220px; min-height:var(--ds-control-h-sm); }
  .me-filter-status { margin:0; padding:12px 20px 0; color:var(--muted); font-size:.8125rem; }

  .me-card { min-width:0; border:1px solid var(--border); border-radius:12px; background:var(--surface3); }
  .me-card.is-open { border-color:var(--ds-accent-border); background:var(--surface); }
  .me-card.is-dragging { opacity:.5; }
  .me-card.drop-before { box-shadow:0 -2px 0 0 var(--accent); }
  .me-card.drop-after { box-shadow:0 2px 0 0 var(--accent); }
  .me-card.has-problem { border-color:var(--ds-danger-border); }
  .me-card-head { display:flex; align-items:center; gap:8px; padding:8px 10px 8px 8px; }
  .me-drag { display:inline-flex; align-items:center; justify-content:center; flex:0 0 auto; width:24px; height:32px; border-radius:6px; color:var(--dim); cursor:grab; }
  .me-drag:hover { color:var(--text); background:var(--surface2); }
  .me-card-toggle { display:flex; align-items:center; gap:12px; flex:1 1 auto; min-width:0; padding:6px 8px; border:0; border-radius:6px; background:none; color:inherit; font:inherit; text-align:left; cursor:pointer; }
  .me-card-toggle:hover { background:var(--surface2); }
  .me-card-toggle:focus-visible { outline:0; box-shadow:var(--ds-focus-ring); }
  .me-card-no { flex:0 0 auto; min-width:24px; color:var(--muted); font-size:.8125rem; font-variant-numeric:tabular-nums; }
  .me-card-text { min-width:0; }
  .me-card-text strong { display:block; overflow:hidden; color:var(--text); font-size:.875rem; font-weight:600; text-overflow:ellipsis; white-space:nowrap; }
  .me-card-text span { display:block; overflow:hidden; margin-top:2px; color:var(--muted); font-size:.75rem; text-overflow:ellipsis; white-space:nowrap; }
  .me-card-actions { display:flex; gap:6px; flex:0 0 auto; }
  .me-card-body { padding:16px; border-top:1px solid var(--border); }
  .me-fields { display:grid; grid-template-columns:minmax(0,1fr); gap:16px; }
  .me-fields .field > label, .me-fields .me-list-label { display:block; margin-bottom:6px; color:var(--ds-text-secondary); font-size:.8125rem; font-weight:500; }
  .me-fields .field-help { margin-top:6px; }
  .me-mono { font-family:var(--ds-font-mono); font-size:.8125rem; tab-size:4; }
  .me-two { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:16px; }
  .me-list-items { display:grid; grid-template-columns:minmax(0,1fr); gap:8px; margin:0 0 8px; padding:0; list-style:none; }
  .me-list-item { display:grid; grid-template-columns:minmax(0,1fr) auto; gap:8px; align-items:start; }
  .me-list-item .textarea { min-height:40px; }
  .me-choices { display:grid; grid-template-columns:minmax(0,1fr); gap:8px; min-width:0; margin:0; padding:0; border:0; }
  .me-choices legend { margin-bottom:6px; padding:0; color:var(--ds-text-secondary); font-size:.8125rem; font-weight:500; }
  .me-choice { display:grid; grid-template-columns:auto 24px minmax(0,1fr); gap:10px; align-items:center; }
  .me-choice-correct { display:inline-flex; align-items:center; gap:6px; min-width:84px; color:var(--muted); font-size:.8125rem; cursor:pointer; }
  .me-choice-correct input { accent-color:var(--ds-success); width:16px; height:16px; }
  .me-choice-letter { color:var(--text); font-size:.875rem; font-weight:600; text-align:center; }
  .me-choice.is-correct .me-choice-correct { color:var(--ds-success-text); }
  .me-choice.is-correct .input { border-color:var(--ds-success-border); }
  .me-legacy-note { margin:0; padding:10px 14px; border:1px solid var(--border); border-radius:12px; background:var(--surface2); color:var(--ds-text-secondary); font-size:.8125rem; line-height:1.55; }
  .me-section-preview { margin-top:4px; }
  .me-section-preview iframe { width:100%; min-height:240px; border:1px solid var(--border); border-radius:12px; background:var(--bg); }

  .me [hidden] { display:none !important; }

  /* Content blocks: each block is its own card inside a section. */
  .me-block-toolbar { display:flex; align-items:center; justify-content:space-between; gap:8px; flex-wrap:wrap; margin-top:4px; }
  .me-blocks { display:grid; grid-template-columns:minmax(0,1fr); gap:8px; }
  .me-blocks:empty { display:none; }
  .me-block { min-width:0; border:1px solid var(--border); border-left:3px solid var(--ds-border-strong); border-radius:12px; background:var(--surface2); }
  .me-block.is-open { border-color:var(--ds-accent-border); border-left-color:var(--accent); background:var(--surface3); }
  .me-block.is-dragging { opacity:.5; }
  .me-block.drop-before { box-shadow:0 -2px 0 0 var(--accent); }
  .me-block.drop-after { box-shadow:0 2px 0 0 var(--accent); }
  .me-block.has-problem, .me-card.has-problem { border-color:var(--ds-danger-border); }
  .me-block .me-card-head { padding:6px 8px 6px 6px; }
  .me-block .me-card-text strong { font-size:.8125rem; color:var(--ds-text-secondary); }
  .me-block .me-card-text span { font-size:.8125rem; color:var(--text); }
  .me-block .me-card-body { padding:14px; }
  .me-section-footer { display:flex; align-items:flex-start; gap:8px; flex-wrap:wrap; }
  .me-add-content { position:relative; }
  .me-add-menu { position:absolute; left:0; top:calc(100% + 6px); z-index:30; display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:4px; width:min(560px, 86vw); max-height:420px; overflow:auto; padding:8px; border:1px solid var(--ds-border-strong); border-radius:12px; background:var(--surface); box-shadow:0 18px 40px rgba(0,0,0,.45); }
  .me-fields > .btn, .me-fields > .action-row, .me-quiz-question > .btn, .me-quiz-question > .action-row { justify-self:start; }
  .me-format { display:flex; gap:4px; margin:0 0 6px; }
  .me-format button { min-width:32px; min-height:28px; padding:0 8px; border:1px solid var(--ds-border-strong); border-radius:6px; background:var(--surface2); color:var(--ds-text-secondary); font:600 .75rem/1 var(--ds-font-sans); cursor:pointer; }
  .me-format button:hover { color:var(--text); background:var(--ds-surface-hover, var(--surface2)); }
  .me-format button:focus-visible { outline:0; box-shadow:var(--ds-focus-ring); }
  .me-add-option { display:grid; align-content:start; gap:2px; padding:8px 10px; border:0; border-radius:6px; background:none; color:inherit; font:inherit; text-align:left; cursor:pointer; }
  .me-add-option:hover, .me-add-option:focus-visible { outline:0; background:var(--surface2); }
  .me-add-option strong { color:var(--text); font-size:.8125rem; font-weight:600; }
  .me-add-option span { color:var(--muted); font-size:.75rem; line-height:1.4; }
  .me-code + .CodeMirror, .me-fields .CodeMirror { height:auto; min-height:120px; border:1px solid var(--ds-input-border); border-radius:6px; font-family:var(--ds-font-mono); font-size:.8125rem; line-height:1.6; }
  .me-fields .CodeMirror-scroll { min-height:120px; max-height:480px; }
  .me-fields .cm-s-dracula.CodeMirror, .me-fields .cm-s-dracula .CodeMirror-gutters { background-color:var(--bg) !important; }
  .me-fields .cm-s-dracula .CodeMirror-gutters { border-right:1px solid var(--border); }
  .me-image-thumb { display:block; max-width:320px; max-height:200px; border:1px solid var(--border); border-radius:6px; object-fit:contain; background:var(--bg); }
  .me-table-grid { overflow-x:auto; }
  .me-table-grid table { border-collapse:separate; border-spacing:4px; }
  .me-table-grid th, .me-table-grid td { min-width:140px; padding:0; vertical-align:top; }
  .me-table-grid .me-table-head { font-weight:600; }
  .me-table-remove { margin-top:4px; }
  .me-quiz { display:grid; gap:12px; }
  .me-quiz-question { display:grid; gap:10px; min-width:0; margin:0; padding:12px; border:1px solid var(--border); border-radius:12px; background:var(--surface2); }
  .me-quiz-question legend { padding:0 4px; color:var(--ds-text-secondary); font-size:.8125rem; font-weight:600; }
  .me-quiz-question .me-choice { grid-template-columns:auto 24px minmax(0,1fr) auto; }

  .me-summary { display:grid; gap:10px; margin:0 0 12px; }
  .me-summary div { display:grid; grid-template-columns:220px minmax(0,1fr); gap:12px; }
  .me-summary dt { color:var(--muted); font-size:.875rem; }
  .me-summary dd { margin:0; color:var(--text); font-size:.875rem; }
  .me-warning { color:var(--ds-danger-text); }

  .me-actionbar { position:sticky; bottom:0; z-index:20; display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-top:8px; padding:12px 16px; border:1px solid var(--border); border-radius:12px; background:var(--surface); box-shadow:0 -8px 24px rgba(0,0,0,.25); }
  .me-state { margin:0; color:var(--muted); font-size:.875rem; }
  .me-state.is-dirty { color:var(--ds-warning-text, var(--text)); }
  .me-state.is-saving { color:var(--text); }
  .me-state.is-saved { color:var(--ds-success-text, var(--text)); }
  .me-state.is-error { color:var(--ds-danger-text); }
  .me-actionbar [aria-busy="true"], .me-actionbar .btn:disabled { cursor:progress; }
  .me-field-error { margin:6px 0 0; color:var(--ds-danger-text); font-size:.8125rem; line-height:1.4; }
  .me .is-invalid { border-color:var(--ds-danger-border) !important; }
  .me-card > .me-card-error { margin:0 16px 12px; }
  .me-card-error { margin:0 0 12px; padding:10px 12px; border:1px solid var(--ds-danger-border); border-radius:6px; background:var(--ds-danger-soft); color:#fee2e2; font-size:.8125rem; line-height:1.45; }

  @media (max-width: 900px) {
    .me-card-head { flex-wrap:wrap; }
    .me-card-actions { width:100%; justify-content:flex-end; }
    .me-two { grid-template-columns:1fr; }
    .me-summary div { grid-template-columns:1fr; gap:2px; }
    .me-filter { width:100%; }
  }
  @media (max-width: 640px) {
    .me-tabs { flex-wrap:nowrap; overflow-x:auto; scrollbar-width:thin; }
    .me-tab { flex:0 0 auto; white-space:nowrap; }
    .me-actionbar { padding:8px; gap:6px; }
    .me-state, .me-cancel { display:none; }
    .me-state.is-saving, .me-state.is-saved, .me-state.is-error { display:block; width:100%; font-size:.8125rem; }
    .me-actionbar .action-row { width:100%; display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:6px; }
    .me-actionbar .btn { min-width:0; padding:0 6px; font-size:.8125rem; white-space:nowrap; }
    .me-outcome { grid-template-columns:minmax(0,1fr); }
    .me-outcome::before { display:none; }
    .me-choice { grid-template-columns:auto minmax(0,1fr); }
    .me-choice-letter { display:none; }
    .me-list-item { grid-template-columns:minmax(0,1fr); }
  }
</style>
@endpush

@push('scripts')
{{-- CodeMirror (bundled locally, as in the Python IDE) colours the code blocks. --}}
<script src="{{ asset('vendor/codemirror/codemirror.bundle.js') }}"></script>
<script src="{{ asset('js/admin-module-editor.js') }}?v=u6"></script>
@endpush
