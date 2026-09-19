<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  {{-- FIX: $editing declared once here, removed duplicate declaration below --}}
  @php $editing = isset($class); @endphp
  <title>{{ $editing ? 'Edit Class' : 'Create Class' }} — DataSensei</title>

<style>
    /* Create / edit class. Colours, type and radius come from
       partials.design-system. */
    :root {
      --accent2: var(--ds-accent);
      --accent3: var(--ds-success);
      --accent4: var(--ds-warning);
      --warn:    var(--ds-danger);
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: var(--ds-font-sans);
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      overflow-x: hidden;
    }
    .main { flex: 1; display: flex; flex-direction: column; min-width: 0; }

    /* ── Title bar ──────────────────────────────────────── */
    .topbar {
      min-height: 60px;
      background: var(--bg);
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      padding: 0 32px;
      gap: 16px;
      flex-shrink: 0;
    }
    .topbar-back {
      display: flex; align-items: center; gap: 6px; flex-shrink: 0;
      color: var(--muted); text-decoration: none;
      font-size: .875rem; font-weight: 500;
      transition: color .16s ease;
    }
    .topbar-back:hover { color: var(--text); }
    .topbar-divider { width: 1px; height: 20px; flex-shrink: 0; background: var(--border); }
    .topbar h1 { flex: 1; min-width: 0; }

    /* ── Content ────────────────────────────────────────── */
    .content { flex: 1; padding: 28px 32px 48px; }

    /* ── Form layout ────────────────────────────────────── */
    .form-layout {
      width: 100%; max-width: 1040px;
      display: grid;
      grid-template-columns: minmax(0, 1fr) 300px;
      gap: 20px;
      align-items: start;
    }
    .form-layout > * { min-width: 0; }

    /* ── Lead-in ────────────────────────────────────────── */
    .form-page-header { grid-column: 1 / -1; margin-bottom: 4px; }
    .form-page-header h2 { font-size: 1.125rem; font-weight: 600; line-height: 1.35; letter-spacing: -0.01em; overflow-wrap: anywhere; }
    .form-page-header p  { max-width: 72ch; font-size: .875rem; color: var(--muted); margin-top: 4px; line-height: 1.5; }

    /* ── Card ───────────────────────────────────────────── */
    .card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      overflow: hidden;
    }
    .card-header {
      padding: 14px 20px;
      border-bottom: 1px solid var(--border);
      display: flex; align-items: center; gap: 12px;
    }
    .card-title { font-weight: 600; font-size: .9375rem; line-height: 1.35; }
    .card-subtitle { font-size: .8125rem; color: var(--muted); margin-top: 2px; line-height: 1.45; }
    .card-body { padding: 20px; display: flex; flex-direction: column; gap: 16px; }

    .card.danger-zone { border-color: var(--ds-danger-border); }
    .card.danger-zone .card-header { border-bottom-color: var(--border); }
    .card.danger-zone .card-title { color: var(--ds-danger-text); }

    /* ── Form fields ────────────────────────────────────── */
    .field { display: flex; flex-direction: column; gap: 6px; }
    .field-row   { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 16px; }
    .field-row-3 { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }

    label {
      font-size: .8125rem; font-weight: 500; line-height: 1.35; color: var(--ds-text-secondary);
      display: flex; align-items: baseline; gap: 8px;
    }
    label .required { font-size: .75rem; font-weight: 500; color: var(--ds-danger-text); }
    label .optional { font-size: .75rem; font-weight: 400; color: var(--muted); }

    input[type="text"],
    input[type="number"],
    input[type="email"],
    .field select,
    .field textarea {
      width: 100%;
      min-height: 38px;
      padding: 8px 12px;
      background: var(--surface3);
      border: 1px solid var(--ds-input-border);
      border-radius: var(--radius-sm);
      color: var(--text);
      font: 400 .875rem/1.4 var(--ds-font-sans);
      outline: none;
      transition: border-color .16s ease, box-shadow .16s ease;
    }
    input:focus, select:focus, textarea:focus {
      border-color: var(--accent);
      box-shadow: var(--ds-focus-ring);
    }
    input::placeholder, textarea::placeholder { color: var(--dim); }
    input.error, select.error, textarea.error { border-color: var(--ds-danger); }
    .field textarea { resize: vertical; min-height: 96px; line-height: 1.55; }
    .field select {
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' viewBox='0 0 24 24' stroke='%238aa0bd' stroke-width='2'%3E%3Cpath d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 12px center;
      padding-right: 36px;
    }
    select option { background: var(--surface); }

    .field-hint  { font-size: .75rem; color: var(--muted); line-height: 1.5; }
    .field-error { font-size: .75rem; color: var(--ds-danger-text); line-height: 1.5; display: flex; align-items: center; gap: 4px; }

    /* ── Join code ──────────────────────────────────────── */
    .code-preview {
      padding: 16px; background: var(--surface3); border: 1px solid var(--border);
      border-radius: var(--radius-sm); text-align: center;
    }
    .code-value {
      font: 600 1.5rem/1.2 var(--ds-font-mono);
      letter-spacing: .01em;
      color: var(--text);
      overflow-wrap: anywhere;
    }
    .code-label { font-size: .75rem; color: var(--muted); font-weight: 500; margin-top: 6px; }

    /* ── Side column ────────────────────────────────────── */
    .form-aside { display: flex; flex-direction: column; gap: 16px; position: sticky; top: 24px; }

    .info-list { display: flex; flex-direction: column; }
    .info-row  { display: flex; gap: 8px; padding: 12px 0; border-top: 1px solid var(--border); }
    .info-row:first-child { padding-top: 0; border-top: 0; }
    .info-row:last-child { padding-bottom: 0; }
    .info-text  { flex: 1; min-width: 0; }
    .info-title { font-size: .8125rem; font-weight: 600; line-height: 1.4; }
    .info-desc  { font-size: .8125rem; color: var(--muted); line-height: 1.5; margin-top: 2px; }

    /* ── Buttons ────────────────────────────────────────── */
    .btn {
      display: inline-flex; align-items: center; justify-content: center;
      gap: 8px; min-height: 38px; padding: 0 16px; border-radius: var(--radius-sm);
      font: 500 .875rem/1.2 var(--ds-font-sans); cursor: pointer;
      border: 1px solid transparent;
      transition: background .16s ease, border-color .16s ease, color .16s ease;
      text-decoration: none; white-space: nowrap;
    }
    .btn-primary,
    .btn-accent  { background: var(--accent); color: #fff; border-color: var(--accent); }
    .btn-primary:hover,
    .btn-accent:hover  { background: var(--accent-hover); border-color: var(--accent-hover); }
    .btn-ghost   { background: var(--surface2); color: var(--text); border-color: var(--ds-border-strong); }
    .btn-ghost:hover   { background: var(--ds-surface-hover); }
    .btn-danger  { background: transparent; color: var(--ds-danger-text); border-color: var(--ds-danger-border); }
    .btn-danger:hover  { background: var(--ds-danger-soft); }
    .btn-block   { width: 100%; }

    /* ── Form actions ───────────────────────────────────── */
    .form-actions {
      display: flex; align-items: center; justify-content: flex-end;
      flex-wrap: wrap; gap: 8px; padding-top: 20px;
      border-top: 1px solid var(--border);
      margin-top: 20px;
    }

    /* ── Character counter ──────────────────────────────── */
    .char-counter { font-size: .75rem; color: var(--muted); text-align: right; font-variant-numeric: tabular-nums; }
    .char-counter:empty { display: none; }
    .char-counter.near { color: var(--ds-warning-text); }
    .char-counter.over { color: var(--ds-danger-text); }

    /* Used by the submit button's loading icon. */
    @keyframes spin { to { transform: rotate(360deg); } }

    @media (max-width: 900px) {
      .content { padding: 24px 20px 40px; }
      .topbar { min-height: 56px; padding: 0 20px; }
      .form-layout { grid-template-columns: minmax(0, 1fr); }
      .form-aside { position: static; }
    }
    @media (max-width: 640px) {
      .content { padding: 20px 16px 32px; }
      .topbar { padding: 0 16px; gap: 12px; }
      .card-header { padding: 12px 16px; }
      .card-body { padding: 16px; }
      .field-row, .field-row-3 { grid-template-columns: minmax(0, 1fr); }
    }
  </style>
    @include('partials.page-head', ['pageDescription' => 'Manage your classes, join codes, and enrolled students.'])
</head>
<body>
  @include('partials.instructor-sidebar')

  {{-- FIX: removed duplicate @php $editing = isset($class); @endphp that was here --}}

  <div class="main">
    <header class="topbar">
      <a href="{{ route('instructor.classes.index') }}" class="topbar-back">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
        My Classes
      </a>
      <div class="topbar-divider"></div>
      <h1 class="ds-page-title">{{ $editing ? 'Edit Class' : 'Create New Class' }}</h1>
    </header>

    <main class="content">
      <div class="form-layout">

        {{-- Page header --}}
        <div class="form-page-header">
          <h2>{{ $editing ? 'Edit ' . $class->name : 'Create a New Class' }}</h2>
          <p>
            {{ $editing
              ? 'Update your class details below. Students already enrolled will not be affected.'
              : 'Fill in the details below. A unique join code will be generated automatically for your students.' }}
          </p>
        </div>

        {{-- ── LEFT COLUMN ─────────────────────────────── --}}
        <div style="display:flex; flex-direction:column; gap:20px;">

          <form
            method="POST"
            action="{{ $editing ? route('instructor.classes.update', $class) : route('instructor.classes.store') }}"
            id="classForm"
          >
            @csrf
            @if($editing) @method('PUT') @endif

            {{-- Basic Details --}}
            <div class="card">
              <div class="card-header">
                <div>
                  <div class="card-title">Basic Information</div>
                  <div class="card-subtitle">Core details that identify your class</div>
                </div>
              </div>
              <div class="card-body">

                {{-- Class Name --}}
                <div class="field">
                  <label for="name">
                    Class Name
                    <span class="required">Required</span>
                  </label>
                  <input
                    type="text" id="name" name="name"
                    value="{{ old('name', $class->name ?? '') }}"
                    placeholder="e.g. Introduction to Data Science"
                    class="{{ $errors->has('name') ? 'error' : '' }}"
                    maxlength="189"
                    data-counter="name-counter"
                    autocomplete="off"
                  />
                  <div class="char-counter" id="name-counter"></div>
                  @error('name')
                    <div class="field-error">
                      <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                      </svg>
                      {{ $message }}
                    </div>
                  @enderror
                </div>

                {{-- Section + Subject Code --}}
                <div class="field-row">
                  <div class="field">
                    <label for="section">
                      Section
                      <span class="optional">Optional</span>
                    </label>
                    <input
                      type="text" id="section" name="section"
                      value="{{ old('section', $class->section ?? '') }}"
                      placeholder="e.g. BSIT 3-A"
                      class="{{ $errors->has('section') ? 'error' : '' }}"
                      maxlength="189"
                    />
                    @error('section')
                      <div class="field-error">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="field">
                    <label for="subject_code">
                      Subject Code
                      <span class="optional">Optional</span>
                    </label>
                    <input
                      type="text" id="subject_code" name="subject_code"
                      value="{{ old('subject_code', $class->subject_code ?? '') }}"
                      placeholder="e.g. IT 301"
                      class="{{ $errors->has('subject_code') ? 'error' : '' }}"
                      maxlength="50"
                    />
                    @error('subject_code')
                      <div class="field-error">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                {{-- Description --}}
                <div class="field">
                  <label for="description">
                    Description
                    <span class="optional">Optional</span>
                  </label>
                  <textarea
                    id="description" name="description"
                    placeholder="Brief overview of what students will learn in this class…"
                    class="{{ $errors->has('description') ? 'error' : '' }}"
                    maxlength="1000"
                    data-counter="desc-counter"
                  >{{ old('description', $class->description ?? '') }}</textarea>
                  <div class="char-counter" id="desc-counter"></div>
                  @error('description')
                    <div class="field-error">{{ $message }}</div>
                  @enderror
                </div>

              </div>
            </div>

            {{-- Schedule & Term --}}
            <div class="card" style="margin-top:16px;">
              <div class="card-header">
                <div>
                  <div class="card-title">Schedule & Term</div>
                  <div class="card-subtitle">Academic period this class belongs to</div>
                </div>
              </div>
              <div class="card-body">
                <div class="field-row">
                  <div class="field">
                    <label for="term">
                      Term / Semester
                      <span class="optional">Optional</span>
                    </label>
                    <select id="term" name="term" class="{{ $errors->has('term') ? 'error' : '' }}">
                      <option value="">— Select Term —</option>
                      @php
                        $terms = [
                          '1st Semester' => '1st Semester',
                          '2nd Semester' => '2nd Semester',
                          'Summer'       => 'Summer',
                          'Full Year'    => 'Full Year',
                          'Q1'           => 'Quarter 1',
                          'Q2'           => 'Quarter 2',
                          'Q3'           => 'Quarter 3',
                          'Q4'           => 'Quarter 4',
                        ];
                        $selectedTerm = old('term', $class->term ?? '');
                      @endphp
                      @foreach ($terms as $value => $label)
                        <option value="{{ $value }}" {{ $selectedTerm === $value ? 'selected' : '' }}>
                          {{ $label }}
                        </option>
                      @endforeach
                    </select>
                    @error('term')
                      <div class="field-error">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="field">
                    <label for="academic_year">
                      Academic Year
                      <span class="optional">Optional</span>
                    </label>
                    <input
                      type="text" id="academic_year" name="academic_year"
                      value="{{ old('academic_year', $class->academic_year ?? '') }}"
                      placeholder="e.g. 2025-2026"
                      class="{{ $errors->has('academic_year') ? 'error' : '' }}"
                      maxlength="20"
                    />
                    @error('academic_year')
                      <div class="field-error">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>
            </div>

            {{-- Enrolment Settings --}}
            <div class="card" style="margin-top:16px;">
              <div class="card-header">
                <div>
                  <div class="card-title">Enrolment Settings</div>
                  <div class="card-subtitle">Set capacity for instructor-managed enrolment</div>
                </div>
              </div>
              <div class="card-body">
                <div class="field">
                  <label for="max_students">
                    Max Students
                    <span class="optional">Optional</span>
                  </label>
                  <input
                    type="number" id="max_students" name="max_students"
                    value="{{ old('max_students', $class->max_students ?? '') }}"
                    placeholder="Leave blank for unlimited"
                    min="1" max="1000"
                    class="{{ $errors->has('max_students') ? 'error' : '' }}"
                  />
                  <span class="field-hint">Leave empty to allow unlimited enrolments.</span>
                  @error('max_students')
                    <div class="field-error">{{ $message }}</div>
                  @enderror
                </div>

              </div>
            </div>

            {{-- Form Actions --}}
            {{-- FIX: removed bogus style="grid-column:auto" — this is inside a flex column, not a grid --}}
            <div class="form-actions">
              <a href="{{ route('instructor.classes.index') }}" class="btn btn-ghost">Cancel</a>
              <button type="submit" class="btn btn-accent" id="submitBtn">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  @if($editing)
                    <path d="M5 13l4 4L19 7"/>
                  @else
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                  @endif
                </svg>
                {{ $editing ? 'Save Changes' : 'Create Class' }}
              </button>
            </div>
          </form>

        </div>
        {{-- END LEFT COLUMN --}}

        {{-- ── RIGHT SIDEBAR ───────────────────────────── --}}
        <div class="form-aside">

          {{-- Class Code card (edit only) --}}
          @if($editing)
            <div class="card">
              <div class="card-header">
                <div>
                  <div class="card-title">Class Code</div>
                  <div class="card-subtitle">Students use this to join</div>
                </div>
              </div>
              <div class="card-body" style="gap:12px;">
                <div class="code-preview">
                  <div class="code-value">{{ $class->class_code }}</div>
                  <div class="code-label">Join Code</div>
                </div>

                <form method="POST" action="{{ route('instructor.classes.regenerate-code', $class) }}">
                  @csrf @method('PATCH')
                  <button type="submit" class="btn btn-ghost btn-block"
                    onclick="return confirm('Regenerate code? The old code will stop working immediately.')">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Regenerate Code
                  </button>
                </form>
              </div>
            </div>
          @else
            {{-- Tips card for create flow --}}
            <div class="card">
              <div class="card-header">
                <div>
                  <div class="card-title">Auto-Generated Code</div>
                  <div class="card-subtitle">How joining works</div>
                </div>
              </div>
              <div class="card-body">
                <div class="info-list">
                  <div class="info-row">
                    <div class="info-text">
                      <div class="info-title">Unique 7-letter code</div>
                      <div class="info-desc">Generated automatically when you create the class.</div>
                    </div>
                  </div>
                  <div class="info-row">
                    <div class="info-text">
                      <div class="info-title">Students self-enrol</div>
                      <div class="info-desc">Share the code and students can join instantly.</div>
                    </div>
                  </div>
                  <div class="info-row">
                    <div class="info-text">
                      <div class="info-title">Regenerate anytime</div>
                      <div class="info-desc">Old codes expire immediately when regenerated.</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @endif

          {{-- Danger Zone (edit only) --}}
          @if($editing)
            <div class="card danger-zone">
              <div class="card-header">
                <div>
                  <div class="card-title">Danger Zone</div>
                  <div class="card-subtitle">Irreversible actions</div>
                </div>
              </div>
              <div class="card-body" style="gap:8px;">
                @if(!$class->is_archived)
                  <form method="POST" action="{{ route('instructor.classes.archive', $class) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-ghost btn-block">
                      <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8"/>
                      </svg>
                      Archive This Class
                    </button>
                  </form>
                @endif

                {{-- FIX: btn-danger now has CSS — delete button renders correctly --}}
                <form method="POST" action="{{ route('instructor.classes.destroy', $class) }}"
                      onsubmit="return confirm('Permanently delete this class and all its data? This CANNOT be undone.')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-block">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete Permanently
                  </button>
                </form>
              </div>
            </div>
          @endif

        </div>
        {{-- END SIDEBAR --}}

      </div>
    </main>
  </div>

  <script>
    // ── Character counters ─────────────────────────────────────────────────
    function initCounter(inputEl, counterId, max) {
      const counter = document.getElementById(counterId);
      if (!counter || !inputEl) return;
      function update() {
        const len = inputEl.value.length;
        counter.textContent = `${len} / ${max}`;
        counter.className = 'char-counter';
        if (len > max * 0.85) counter.classList.add('near');
        if (len >= max)       counter.classList.add('over');
      }
      inputEl.addEventListener('input', update);
      update();
    }

    document.querySelectorAll('[data-counter]').forEach(el => {
      const max = parseInt(el.getAttribute('maxlength')) || 200;
      initCounter(el, el.dataset.counter, max);
    });

    // ── Submit button loading state ────────────────────────────────────────
    document.getElementById('classForm').addEventListener('submit', function () {
      const btn = document.getElementById('submitBtn');
      btn.disabled = true;
      btn.innerHTML = `
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
             style="animation:spin .8s linear infinite">
          <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        {{ $editing ? 'Saving…' : 'Creating…' }}
      `;
    });

    // ── Auto-fill Academic Year placeholder ───────────────────────────────
    const ayInput = document.getElementById('academic_year');
    if (ayInput && !ayInput.value) {
      const y = new Date().getFullYear();
      ayInput.placeholder = `e.g. ${y}-${y + 1}`;
    }
  </script>
</body>
</html>
