@extends('instructor.layout')

@use('App\Services\ClassCertificateService')
@use('App\Support\Certificates\Placeholders')
@use('App\Support\Glossary')

@section('title', $definition->exists ? 'Edit certificate' : 'New certificate')
@section('page_title', $definition->exists ? 'Edit certificate' : 'New Certificate of Completion')
@section('page_subtitle', 'A Certificate of Completion for one of your classes: everything you assigned to it, not one module. Choose a layout, then fill in the details while the preview updates. At the end of the semester you issue it to the students who completed the class.')

@section('content')
  @include('certificates._styles')

  @php
    $selectedClass = (int) old('class_id', $definition->class_id ?: ($classes->firstWhere('is_archived', false)?->id ?? $classes->first()?->id ?? 0));
    $selectedLayout = (string) old('layout_key', (string) $definition->layout_key);
    $invalid = fn (string $field) => $errors->has($field) ? ' is-invalid' : '';
    $course = $courses[$selectedClass] ?? null;
    $show = fn ($value) => $value !== '' && $value !== null ? $value : 'Not set in the class';
  @endphp

  @push('head')
  <style>
    /* Builder: fields on the left, live preview on the right. */
    .ct-builder { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1.05fr); gap: 18px; align-items: start; }
    .ct-builder-fields { display: grid; gap: 18px; min-width: 0; }
    .ct-builder-preview { position: sticky; top: 16px; min-width: 0; }
    @media (max-width: 1100px) { .ct-builder { grid-template-columns: minmax(0, 1fr); } .ct-builder-preview { position: static; order: -1; } }
    .ct-live-empty { display: flex; align-items: center; justify-content: center; min-height: 220px; padding: 18px; color: #4b5563; font-size: .875rem; line-height: 1.5; text-align: center; }
    .ct-step { color: var(--muted); font-weight: 500; }
    .ct-course { margin: 0; }
    .ct-builder .ct-placeholders { grid-template-columns: minmax(0, 1fr); }
    .ct-builder .ct-placeholders code { white-space: nowrap; }
    .ct-course code { color: var(--muted); font-family: var(--ds-font-mono); font-size: .75rem; white-space: nowrap; }
  </style>
  @endpush

  @if($classes->isEmpty())
    <section class="ct-section"><div class="ct-empty">Create a class first; a Certificate of Completion always belongs to one of your classes.</div></section>
  @else
  <form id="ct-form" method="POST" action="{{ $definition->exists ? route('instructor.certificates.update', $definition) : route('instructor.certificates.store') }}" class="ct-form" data-preview-url="{{ route('instructor.certificates.live-preview') }}" novalidate>
    @csrf
    @if($definition->exists) @method('PUT') @endif

    <section class="ct-section">
      <h2><span class="ct-step">Step 1.</span> {!! Glossary::help('certificate_layout', 'Choose a layout') !!}</h2>
      <div class="ct-section-body">
        <div class="ct-layouts" role="radiogroup" aria-label="Layout">
          @foreach($layouts as $key => $layout)
            <label class="ct-layout">
              <span class="ct-thumb" aria-hidden="true">{{ $layout['thumbnail'] }}</span>
              <span><input type="radio" name="layout_key" value="{{ $key }}" @checked($selectedLayout === $key)><span class="ct-layout-name">{{ $layout['name'] }}</span></span>
              <span class="ct-layout-desc">{{ $layout['description'] }}@if(! $layout['enabled']) This layout was turned off by an administrator.@endif</span>
            </label>
          @endforeach
        </div>
        @error('layout_key')<p class="ct-error" role="alert">{{ $message }}</p>@enderror
        <p class="ct-help">The five layouts are fixed designs; you only fill in the words. The preview below shows your choice.</p>
      </div>
    </section>

    <div class="ct-builder">
      <div class="ct-builder-fields">
        <section class="ct-section">
          <h2><span class="ct-step">Step 2.</span> Course / class</h2>
          <div class="ct-section-body">
            <div class="ct-field">
              <label for="ct-class">Class</label>
              @if($locked)
                <input type="hidden" name="class_id" value="{{ $definition->class_id }}">
                <input id="ct-class" class="ct-input" value="{{ $classes->firstWhere('id', $definition->class_id)?->name }}{{ $classes->firstWhere('id', $definition->class_id)?->section ? ', '.$classes->firstWhere('id', $definition->class_id)->section : '' }}" readonly>
                <p class="ct-help">Certificates were already issued for this class, so the class is locked.</p>
              @else
                <select id="ct-class" class="ct-select{{ $invalid('class_id') }}" name="class_id" required>
                  @foreach($classes as $class)
                    <option value="{{ $class->id }}" @selected($selectedClass === (int) $class->id)>{{ $class->name }}{{ $class->section ? ', '.$class->section : '' }}{{ $class->is_archived ? ' (archived)' : '' }}</option>
                  @endforeach
                </select>
              @endif
              @error('class_id')<p class="ct-error" role="alert">{{ $message }}</p>@enderror
            </div>

            <dl class="ct-dl ct-course" aria-live="polite">
              <dt>Course name <code>[Course Name]</code></dt><dd data-course="course">{{ $show($course['course'] ?? '') }}</dd>
              <dt>Course code <code>[Course Code]</code></dt><dd data-course="course_code">{{ $show($course['course_code'] ?? '') }}</dd>
              <dt>Section <code>[Section]</code></dt><dd data-course="section">{{ $show($course['section'] ?? '') }}</dd>
              <dt>Semester <code>[Semester]</code></dt><dd data-course="semester">{{ $show($course['semester'] ?? '') }}</dd>
            </dl>
            <p class="ct-help">The certificate is for completing this class: every module, activity and assessment you assign to it. It does not stand for one module or for every DataSensei module. The course details come from the class record (edit them under Classes) and reach the certificate through the placeholders shown.</p>
          </div>
        </section>

        <section class="ct-section">
          <h2><span class="ct-step">Step 3.</span> Certificate details</h2>
          <div class="ct-section-body">
            <div class="ct-field">
              <label for="ct-name">Certificate name</label>
              <input id="ct-name" class="ct-input{{ $invalid('name') }}" name="name" maxlength="120" value="{{ old('name', $definition->name) }}" required aria-describedby="ct-name-help">
              <p class="ct-help" id="ct-name-help">For example Certificate of Completion, or Python Fundamentals Completion Certificate.</p>
              @error('name')<p class="ct-error" role="alert">{{ $message }}</p>@enderror
            </div>
            <div class="ct-grid">
              <div class="ct-field">
                <span class="ct-label">Instructor name</span>
                <input class="ct-input" value="{{ $instructor->name }}" readonly aria-label="Instructor name">
                <p class="ct-help">Always your own name, from your account.</p>
              </div>
              <div class="ct-field">
                <label for="ct-title">Instructor title</label>
                <input id="ct-title" class="ct-input{{ $invalid('signatory_title') }}" name="signatory_title" maxlength="120" value="{{ old('signatory_title', $definition->signatory_title) }}" required>
                <p class="ct-help">For example Data Science Instructor.</p>
                @error('signatory_title')<p class="ct-error" role="alert">{{ $message }}</p>@enderror
              </div>
            </div>
            <div class="ct-field">
              <span class="ct-label">Issuer / institution</span>
              <input class="ct-input" value="{{ $issuer }}" readonly aria-label="Issuer or institution">
              <p class="ct-help">Your institution, or the DataSensei issuer set by an administrator.</p>
            </div>
            <div class="ct-field">
              <label for="ct-statement">Certificate statement</label>
              <textarea id="ct-statement" class="ct-textarea{{ $invalid('statement') }}" name="statement" maxlength="600" required>{{ old('statement', $definition->statement) }}</textarea>
              @error('statement')<p class="ct-error" role="alert">{{ $message }}</p>@enderror
              <p class="ct-error" id="ct-statement-live" role="status" hidden></p>
            </div>
            <div class="ct-field">
              <span class="ct-label">{!! Glossary::help('certificate_placeholder', 'Placeholders') !!} <span class="ct-muted">(click one to insert it)</span></span>
              <ul class="ct-placeholders">
                @foreach(Placeholders::SHOWN as $token)
                  <li><button type="button" class="ct-link-button" data-insert="{{ $token }}" title="Insert {{ $token }}"><code>{{ $token }}</code></button><span>{{ Placeholders::ALL[$token] }}</span></li>
                @endforeach
              </ul>
            </div>
          </div>
        </section>
      </div>

      <aside class="ct-builder-preview" aria-label="Live preview">
        <section class="ct-section">
          <h2>Live preview</h2>
          <div class="ct-section-body">
            <div class="ct-frame" id="ct-live" aria-live="polite">
              @if($preview)
                {{ $preview }}
              @else
                <div class="ct-live-empty">Choose a layout in step 1 to see the certificate here.</div>
              @endif
            </div>
            <p class="ct-help">With a sample learner, today's date and a sample certificate ID. It changes as you edit; nothing is saved until you press Save.</p>
          </div>
        </section>
      </aside>
    </div>

    <div class="ct-actions">
      <button class="ct-btn" type="submit">{{ $definition->exists ? 'Save' : 'Save draft' }}</button>
      <a class="ct-btn ct-btn-secondary" href="{{ $definition->exists ? route('instructor.certificates.show', $definition) : route('instructor.certificates.index') }}">Cancel</a>
    </div>
  </form>
  @endif
@endsection

@push('scripts')
<script>
  (function () {
    var form = document.getElementById('ct-form');
    if (!form) { return; }
    var courses = @json($courses);
    var live = document.getElementById('ct-live');
    var warning = document.getElementById('ct-statement-live');
    var statement = document.getElementById('ct-statement');
    var classSelect = document.getElementById('ct-class');
    var timer = null;
    var sequence = 0;

    function showCourse() {
      if (!classSelect || classSelect.tagName !== 'SELECT') { return; }
      var course = courses[classSelect.value] || {};
      Array.prototype.forEach.call(form.querySelectorAll('[data-course]'), function (node) {
        var value = course[node.getAttribute('data-course')];
        node.textContent = value ? value : 'Not set in the class';
      });
    }

    function render() {
      var data = new FormData(form);
      data.delete('_method');
      var mine = ++sequence;
      fetch(form.getAttribute('data-preview-url'), {
        method: 'POST',
        body: data,
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
      }).then(function (response) {
        return response.ok ? response.json() : null;
      }).then(function (result) {
        if (!result || mine !== sequence) { return; }
        if (result.svg) {
          live.innerHTML = result.svg;
        } else {
          live.innerHTML = '<div class="ct-live-empty">Choose a layout in step 1 to see the certificate here.</div>';
        }
        if (warning) {
          warning.hidden = !result.unknown || result.unknown.length === 0;
          warning.textContent = warning.hidden ? '' : 'Not a placeholder: ' + result.unknown.join(', ') + '. Use one of the placeholders listed below.';
        }
      }).catch(function () { /* keep the last preview */ });
    }

    function schedule() {
      window.clearTimeout(timer);
      timer = window.setTimeout(render, 200);
    }

    form.addEventListener('input', schedule);
    form.addEventListener('change', function (event) {
      if (event.target === classSelect) { showCourse(); }
      schedule();
    });

    Array.prototype.forEach.call(form.querySelectorAll('[data-insert]'), function (button) {
      button.addEventListener('click', function () {
        if (!statement) { return; }
        var token = button.getAttribute('data-insert');
        var start = statement.selectionStart || statement.value.length;
        var end = statement.selectionEnd || start;
        statement.value = statement.value.slice(0, start) + token + statement.value.slice(end);
        statement.focus();
        statement.selectionStart = statement.selectionEnd = start + token.length;
        schedule();
      });
    });
  })();
</script>
@endpush
