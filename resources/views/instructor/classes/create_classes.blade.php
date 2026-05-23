<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  {{-- FIX: $editing declared once here, removed duplicate declaration below --}}
  @php $editing = isset($class); @endphp
  <title>{{ $editing ? 'Edit Class' : 'Create Class' }} — DataSensei</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <style>
    :root {
      --bg:          #0d1320;
      --surface:     #111c2d;
      --surface2:    #1a2638;
      --surface3:    #223149;
      --border:      #1e2f47;
      --border-hover:#2c4168;
      --accent:      #3b82f6;
      --accent-hover:#2563eb;
      --accent2:     #8b5cf6;
      --accent3:     #10b981;
      --accent4:     #f59e0b;
      --warn:        #ef4444;
      --text:        #fafafa;
      --muted:       #7f93b0;
      --dim:         #3d5272;
      --radius:      8px;
      --radius-sm:   6px;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      overflow-x: hidden;
      -webkit-font-smoothing: antialiased;
    }
    .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }

    /* ── Topbar ─────────────────────────────────────────── */
    .topbar {
      height: 64px;
      background: var(--bg);
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      padding: 0 32px;
      gap: 14px;
      flex-shrink: 0;
    }
    .topbar-back {
      display: flex; align-items: center; gap: 8px;
      color: var(--muted); text-decoration: none;
      font-size: .875rem; font-weight: 600;
      transition: color .15s;
    }
    .topbar-back:hover { color: var(--text); }
    .topbar-divider { width: 1px; height: 20px; background: var(--border); }
    .topbar h1 { font-size: 1.125rem; font-weight: 700; flex: 1; letter-spacing: -0.01em; }

    /* ── Content ────────────────────────────────────────── */
    .content {
      flex: 1; overflow-y: auto; padding: 40px 32px;
      display: flex; flex-direction: column; gap: 0;
      align-items: center;
    }

    /* ── Form layout ────────────────────────────────────── */
    .form-layout {
      width: 100%; max-width: 860px;
      display: grid;
      grid-template-columns: 1fr 320px;
      gap: 24px;
      align-items: start;
    }

    /* ── Section titles ─────────────────────────────────── */
    .form-page-header { grid-column: 1 / -1; margin-bottom: 4px; }
    .form-page-header h2 { font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em; }
    .form-page-header p  { font-size: .875rem; color: var(--muted); margin-top: 6px; line-height: 1.6; }

    /* ── Card ───────────────────────────────────────────── */
    .card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      overflow: hidden;
    }
    .card-header {
      padding: 18px 24px;
      border-bottom: 1px solid var(--border);
      display: flex; align-items: center; gap: 12px;
    }
    .card-header-icon {
      width: 34px; height: 34px; border-radius: var(--radius-sm);
      background: var(--surface2); border: 1px solid var(--border);
      display: flex; align-items: center; justify-content: center;
      color: var(--accent); flex-shrink: 0;
    }
    .card-title { font-weight: 700; font-size: .9375rem; }
    .card-subtitle { font-size: .75rem; color: var(--muted); margin-top: 3px; }
    .card-body { padding: 24px; display: flex; flex-direction: column; gap: 20px; }

    /* ── Form fields ────────────────────────────────────── */
    .field { display: flex; flex-direction: column; gap: 6px; }
    .field-row   { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .field-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }

    label {
      font-size: .8125rem; font-weight: 600; color: var(--muted);
      display: flex; align-items: center; gap: 6px;
    }
    label .required {
      font-size: .65rem; color: var(--warn);
      background: rgba(239,68,68,.1); border-radius: 999px;
      padding: 1px 5px; font-weight: 700; letter-spacing: .04em;
    }
    label .optional {
      font-size: .65rem; color: var(--dim);
      background: var(--surface3); border-radius: 999px;
      padding: 1px 5px; font-weight: 600;
    }

    input[type="text"],
    input[type="number"],
    input[type="email"],
    select,
    textarea {
      background: var(--bg);
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      color: var(--text);
      font-family: inherit;
      font-size: .875rem;
      padding: 10px 14px;
      outline: none;
      transition: border-color .15s, box-shadow .15s;
      width: 100%;
    }
    input:focus, select:focus, textarea:focus {
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(59,130,246,.12);
    }
    input::placeholder, textarea::placeholder { color: var(--dim); }
    input.error, select.error, textarea.error { border-color: var(--warn); }
    textarea { resize: vertical; min-height: 96px; line-height: 1.6; }
    select {
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' viewBox='0 0 24 24' stroke='%237f93b0' stroke-width='2'%3E%3Cpath d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 14px center;
      padding-right: 38px;
    }
    select option { background: var(--surface); }

    .field-hint  { font-size: .75rem; color: var(--dim); line-height: 1.5; margin-top: 2px; }
    .field-error { font-size: .75rem; color: var(--warn); margin-top: 2px; display: flex; align-items: center; gap: 4px; }

    /* ── Toggle ─────────────────────────────────────────── */
    .toggle-row {
      display: flex; align-items: center; justify-content: space-between;
      padding: 14px 16px;
      background: var(--bg); border: 1px solid var(--border);
      border-radius: var(--radius-sm); gap: 16px;
    }
    .toggle-info  { flex: 1; }
    .toggle-label { font-size: .875rem; font-weight: 600; }
    .toggle-desc  { font-size: .75rem; color: var(--muted); margin-top: 3px; line-height: 1.5; }
    .toggle-switch { position: relative; width: 40px; height: 22px; flex-shrink: 0; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; position: absolute; }
    .toggle-track {
      position: absolute; inset: 0; border-radius: 999px;
      background: var(--surface3); border: 1px solid var(--border);
      cursor: pointer; transition: background .2s;
    }
    .toggle-track::after {
      content: ''; position: absolute;
      left: 3px; top: 50%; transform: translateY(-50%);
      width: 14px; height: 14px; border-radius: 50%;
      background: var(--dim); transition: left .2s, background .2s;
    }
    .toggle-switch input:checked + .toggle-track { background: var(--accent); border-color: var(--accent); }
    .toggle-switch input:checked + .toggle-track::after { left: 21px; background: #fff; }

    /* ── Code preview ───────────────────────────────────── */
    .code-preview {
      padding: 16px; background: var(--bg); border: 1px solid var(--border);
      border-radius: var(--radius-sm); text-align: center;
    }
    .code-value {
      font-size: 1.75rem; font-weight: 800; letter-spacing: .18em;
      color: var(--accent); font-family: 'JetBrains Mono', 'Courier New', monospace;
      line-height: 1;
    }
    .code-label { font-size: .7rem; color: var(--dim); font-weight: 600; text-transform: uppercase; letter-spacing: .06em; margin-top: 6px; }

    /* ── Sidebar sticky cards ───────────────────────────── */
    .sidebar { display: flex; flex-direction: column; gap: 16px; position: sticky; top: 24px; }

    .info-list { display: flex; flex-direction: column; gap: 12px; }
    .info-row  { display: flex; gap: 10px; }
    .info-icon {
      width: 28px; height: 28px; flex-shrink: 0; border-radius: var(--radius-sm);
      background: var(--surface2); border: 1px solid var(--border);
      display: flex; align-items: center; justify-content: center; color: var(--accent);
    }
    .info-text  { flex: 1; }
    .info-title { font-size: .8125rem; font-weight: 600; }
    .info-desc  { font-size: .75rem; color: var(--muted); line-height: 1.5; margin-top: 2px; }

    /* ── Buttons ────────────────────────────────────────── */
    .btn {
      display: inline-flex; align-items: center; justify-content: center;
      gap: 7px; padding: 10px 18px; border-radius: var(--radius-sm);
      font-size: .875rem; font-weight: 600; cursor: pointer;
      border: 1px solid transparent; transition: all .15s;
      font-family: inherit; text-decoration: none; white-space: nowrap;
    }
    .btn-primary { background: var(--text); color: var(--bg); }
    .btn-primary:hover { background: #e4e4e7; }
    .btn-accent  { background: var(--accent); color: #fff; border-color: var(--accent); }
    .btn-accent:hover  { background: var(--accent-hover); }
    .btn-ghost   { background: var(--surface2); color: var(--text); border-color: var(--border); }
    .btn-ghost:hover   { background: var(--border); }
    {{-- FIX: btn-danger was used in the Danger Zone delete button but never defined --}}
    .btn-danger  { background: rgba(239,68,68,.1); color: var(--warn); border-color: rgba(239,68,68,.2); }
    .btn-danger:hover  { background: rgba(239,68,68,.2); }
    .btn-block   { width: 100%; }

    /* ── Form actions ───────────────────────────────────── */
    .form-actions {
      display: flex; align-items: center; justify-content: flex-end;
      gap: 12px; padding-top: 20px;
      border-top: 1px solid var(--border);
      margin-top: 8px;
    }

    /* ── Char counter ───────────────────────────────────── */
    .char-counter { font-size: .72rem; color: var(--dim); text-align: right; margin-top: 2px; }
    .char-counter.near { color: var(--accent4); }
    .char-counter.over { color: var(--warn); }

    /* ── Scrollbar ──────────────────────────────────────── */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: var(--surface2); border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--dim); }

    @keyframes spin { to { transform: rotate(360deg); } }

    @media (max-width: 900px) {
      .form-layout { grid-template-columns: 1fr; }
      .sidebar { position: static; }
    }
    @media (max-width: 640px) {
      .content { padding: 24px 20px; }
      .topbar { padding: 0 20px; }
      .field-row, .field-row-3 { grid-template-columns: 1fr; }
    }
  </style>
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
      <h1>{{ $editing ? 'Edit Class' : 'Create New Class' }}</h1>
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
                <div class="card-header-icon">
                  <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                  </svg>
                </div>
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
                    maxlength="191"
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
                      maxlength="191"
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
                <div class="card-header-icon" style="color:var(--accent4)">
                  <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                  </svg>
                </div>
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
                <div class="card-header-icon" style="color:var(--accent3)">
                  <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m8-6a4 4 0 11-8 0 4 4 0 018 0z"/>
                  </svg>
                </div>
                <div>
                  <div class="card-title">Enrolment Settings</div>
                  <div class="card-subtitle">Control how students can join</div>
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

                <div class="toggle-row">
                  <div class="toggle-info">
                    <div class="toggle-label">Allow Self-Enrolment</div>
                    <div class="toggle-desc">Students can join using the class code without manual approval.</div>
                  </div>
                  <label class="toggle-switch">
                    <input
                      type="checkbox" name="allow_self_enroll" value="1"
                      {{ old('allow_self_enroll', $class->allow_self_enroll ?? true) ? 'checked' : '' }}
                    />
                    <span class="toggle-track"></span>
                  </label>
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
        <div class="sidebar">

          {{-- Class Code card (edit only) --}}
          @if($editing)
            <div class="card">
              <div class="card-header">
                <div class="card-header-icon" style="color:var(--accent2)">
                  <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                  </svg>
                </div>
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
                <div class="card-header-icon" style="color:var(--accent2)">
                  <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                  </svg>
                </div>
                <div>
                  <div class="card-title">Auto-Generated Code</div>
                  <div class="card-subtitle">How joining works</div>
                </div>
              </div>
              <div class="card-body">
                <div class="info-list">
                  <div class="info-row">
                    <div class="info-icon">
                      <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                      </svg>
                    </div>
                    <div class="info-text">
                      <div class="info-title">Unique 7-letter code</div>
                      <div class="info-desc">Generated automatically when you create the class.</div>
                    </div>
                  </div>
                  <div class="info-row">
                    <div class="info-icon" style="color:var(--accent3)">
                      <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m8-6a4 4 0 11-8 0 4 4 0 018 0z"/>
                      </svg>
                    </div>
                    <div class="info-text">
                      <div class="info-title">Students self-enrol</div>
                      <div class="info-desc">Share the code and students can join instantly.</div>
                    </div>
                  </div>
                  <div class="info-row">
                    <div class="info-icon" style="color:var(--accent4)">
                      <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                      </svg>
                    </div>
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
            <div class="card" style="border-color: rgba(239,68,68,.2);">
              <div class="card-header" style="border-bottom-color: rgba(239,68,68,.15);">
                <div class="card-header-icon" style="color:var(--warn); border-color:rgba(239,68,68,.2);">
                  <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                  </svg>
                </div>
                <div>
                  <div class="card-title" style="color:var(--warn)">Danger Zone</div>
                  <div class="card-subtitle">Irreversible actions</div>
                </div>
              </div>
              <div class="card-body" style="gap:10px;">
                @if(!$class->is_archived)
                  <form method="POST" action="{{ route('instructor.classes.archive', $class) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-ghost btn-block" style="color:var(--accent4); border-color:rgba(245,158,11,.2);">
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