<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Assignment — DataSensei</title>
<style>
    /* Create / edit a class assignment. Colours, type and radius come from partials.design-system. */
    *{box-sizing:border-box;margin:0;padding:0}
    body{min-height:100vh;background:var(--bg);color:var(--text);font-family:var(--ds-font-sans)}
    a{color:inherit}
    .ds-shell{display:flex;min-height:100vh}
    .ds-main{flex:1;min-width:0;padding:28px 32px 48px}
    .wrap{max-width:1450px;margin:0 auto}

    /* page header */
    .top-row{display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px;margin-bottom:24px}
    .top-row > div:first-child{min-width:0;flex:1 1 320px}
    .page-subtitle{max-width:72ch;margin-top:4px;color:var(--muted);font-size:.875rem;line-height:1.55}

    /* buttons */
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:38px;padding:0 16px;
      border:1px solid var(--ds-border-strong);border-radius:var(--radius-sm);background:var(--surface2);color:var(--text);
      font:500 .875rem/1.2 var(--ds-font-sans);text-decoration:none;white-space:nowrap;cursor:pointer;
      transition:background .12s ease,border-color .12s ease,color .12s ease}
    .btn:hover{background:var(--ds-surface-hover)}
    .btn.primary{border-color:var(--accent);background:var(--accent);color:#fff}
    .btn.primary:hover{border-color:var(--accent-hover);background:var(--accent-hover)}
    .btn.good{border-color:var(--ds-success-border);background:transparent;color:var(--ds-success-text)}
    .btn.good:hover{background:var(--ds-success-soft)}
    .actions{display:flex;flex-wrap:wrap;gap:8px}

    /* messages */
    .alert{margin-bottom:16px;padding:12px 16px;border:1px solid var(--ds-accent-border);border-radius:var(--radius-sm);
      background:var(--ds-accent-soft);color:#dbeafe;font-size:.875rem;line-height:1.5}
    .alert strong{font-weight:600}
    .alert.success{border-color:var(--ds-success-border);background:var(--ds-success-soft);color:#d1fae5}
    .alert.danger{border-color:var(--ds-danger-border);background:var(--ds-danger-soft);color:#fee2e2}

    .card{border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}
    .card-pad{padding:20px}

    /* form */
    .form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
    .span-2{grid-column:1/-1}
    .field label{display:block;margin-bottom:6px;color:var(--ds-text-secondary);font-size:.8125rem;font-weight:500}
    .input,.select,.field textarea{width:100%;min-height:38px;padding:8px 12px;border:1px solid var(--ds-input-border);border-radius:var(--radius-sm);
      background:var(--surface3);color:var(--text);font:400 .875rem/1.4 var(--ds-font-sans);outline:none;
      transition:border-color .12s ease,box-shadow .12s ease}
    .input::placeholder,.field textarea::placeholder{color:var(--dim)}
    .input:focus,.select:focus,.field textarea:focus{border-color:var(--accent);box-shadow:var(--ds-focus-ring)}
    .field textarea{min-height:110px;resize:vertical;line-height:1.55}
    .card-pad > .actions{padding-top:16px;border-top:1px solid var(--border)}

    @media(max-width:900px){
      .ds-main{padding:24px 20px 40px}
    }
    @media(max-width:760px){
      .form-grid{grid-template-columns:minmax(0,1fr)}
    }
    .select-row{display:flex;gap:8px;align-items:stretch}
    .select-row .select{flex:1 1 auto;min-width:0}
    .hint{display:block;margin-top:6px;color:var(--muted);font-size:.8125rem;line-height:1.5}
    .btn.is-disabled{opacity:.5;pointer-events:auto;cursor:not-allowed}
    @media(max-width:640px){
      .ds-main{padding:20px 16px 32px}
      .card-pad{padding:16px}
      .top-row{align-items:stretch;flex-direction:column}
      .top-row > div:first-child{flex:0 0 auto}
      .top-row > .btn{width:100%}
      .card-pad > .actions .btn{flex:1 1 auto}
    }
    @media(prefers-reduced-motion:reduce){.btn,.input,.select,textarea{transition:none}}
  </style>
  @include('partials.admin-inspired-page-style')
    @include('partials.page-head', ['pageTitle' => 'Create Assignment', 'pageDescription' => 'Create assignments, review submissions, and return feedback.'])
</head>
<body class="ds-admin-inspired">
  <div class="ds-shell">
    @include('partials.instructor-sidebar')
    <main class="ds-main">

      @php
        $isEdit = isset($classAssignment);
        $selectedLibrary = old('assignment_library_item_id', $isEdit ? $classAssignment->assignment_library_item_id : ($preselectedItem ?? ''));
      @endphp
      <div class="wrap">
        <div class="top-row">
          <div>
            <h1 class="page-title ds-page-title">{{ $isEdit ? 'Edit assignment' : 'Create assignment' }}</h1>
            <p class="page-subtitle">Choose the assignment and the class, set the dates, then save it as a draft or publish it. Use Preview to read every question and answer first.</p>
          </div>
          <a href="{{ route('instructor.assignments.index') }}" class="btn secondary">Back</a>
        </div>

        @if($errors->any())
          <div class="alert danger"><strong>Please fix the following:</strong><br>@foreach($errors->all() as $error) {{ $error }}<br>@endforeach</div>
        @endif

        <form class="card card-pad" method="POST" action="{{ $isEdit ? route('instructor.assignments.update', $classAssignment) : route('instructor.assignments.store') }}">
          @csrf
          @if($isEdit) @method('PUT') @endif
          <div class="form-grid">
            <div class="field">
              <label>Class</label>
              <select name="class_id" class="select" required>
                <option value="">Choose class</option>
                @foreach($classes as $class)
                  <option value="{{ $class->id }}" @selected(old('class_id', $isEdit ? $classAssignment->class_id : '') == $class->id)>{{ $class->name }} {{ $class->section ? '— '.$class->section : '' }}{{ $class->is_archived ? ' (archived class, kept for this assignment)' : '' }}</option>
                @endforeach
              </select>
            </div>

            <div class="field">
              <label>Status</label>
              @if($isEdit)
                {{-- Saving never changes the status. Publish and Close are the
                     dedicated actions on the assignment page. --}}
                <input type="hidden" name="status" value="{{ $classAssignment->status }}">
                <input type="text" class="input" value="{{ ucfirst($classAssignment->status) }}" readonly aria-readonly="true">
                <span style="display:block;margin-top:6px;color:var(--muted);font-size:.8125rem;line-height:1.5">
                  @if($classAssignment->status === 'draft')
                    Use Publish on the assignment page to release this draft to students.
                  @elseif($classAssignment->status === 'published')
                    Use Close on the assignment page to stop new attempts.
                  @else
                    A closed assignment cannot be reopened from this form.
                  @endif
                </span>
              @else
                <select name="status" class="select" required>
                  @foreach(['draft' => 'Draft', 'published' => 'Published'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', 'published') === $value)>{{ $label }}</option>
                  @endforeach
                </select>
              @endif
            </div>

            <div class="field span-2">
              <label for="library-item">Assignment</label>
              <div class="select-row">
                <select id="library-item" name="assignment_library_item_id" class="select" required onchange="fillAssignmentTitle(this)">
                  <option value="">Choose an assignment</option>
                  @foreach($libraryItems->groupBy('year_level') as $year => $items)
                    <optgroup label="{{ $year }}">
                      @foreach($items as $item)
                        <option value="{{ $item->id }}" data-title="{{ $item->title }}" data-preview="{{ route('instructor.assignments.library-preview', $item) }}" @selected((string)$selectedLibrary === (string)$item->id)>
                          {{ $item->title }} ({{ $item->type_label === 'MCQ' ? 'multiple choice' : strtolower($item->type_label) }}, {{ $item->questions_count }} {{ $item->questions_count === 1 ? 'question' : 'questions' }}){{ $item->is_active ? '' : ', no longer offered' }}
                        </option>
                      @endforeach
                    </optgroup>
                  @endforeach
                </select>
                <a id="preview-link" class="btn secondary" href="#" target="_blank" rel="noopener" aria-disabled="true">Preview</a>
              </div>
              <span class="hint">Preview opens the full assignment, with the correct and expected answers, in a new tab.</span>
            </div>

            <div class="field span-2">
              <label>Assignment Title</label>
              <input id="titleInput" type="text" name="title" class="input" value="{{ old('title', $isEdit ? $classAssignment->title : '') }}" placeholder="Example: Python Variables MCQ Practice" required>
            </div>

            <div class="field span-2">
              <label>Instructions</label>
              <textarea name="instructions" placeholder="Additional class-specific instructions...">{{ old('instructions', $isEdit ? $classAssignment->instructions : '') }}</textarea>
            </div>

            <div class="field">
              <label>Available At</label>
              <input type="datetime-local" name="available_at" class="input" value="{{ old('available_at', $isEdit && $classAssignment->available_at ? $classAssignment->available_at->format('Y-m-d\TH:i') : '') }}">
            </div>

            <div class="field">
              <label>Due At</label>
              <input type="datetime-local" name="due_at" class="input" value="{{ old('due_at', $isEdit && $classAssignment->due_at ? $classAssignment->due_at->format('Y-m-d\TH:i') : '') }}">
            </div>

            <div class="field">
              <label>Max Attempts</label>
              <input type="number" name="max_attempts" min="1" max="10" class="input" value="{{ old('max_attempts', $isEdit ? $classAssignment->max_attempts : 1) }}" required>
            </div>
          </div>

          <div class="actions" style="margin-top:16px;justify-content:flex-end">
            <a href="{{ route('instructor.assignments.index') }}" class="btn secondary">Cancel</a>
            <button type="submit" class="btn primary">{{ $isEdit ? 'Save Changes' : 'Create Assignment' }}</button>
          </div>
        </form>
      </div>

      <script>
        function fillAssignmentTitle(select) {
          const titleInput = document.getElementById('titleInput');
          const option = select.options[select.selectedIndex];
          if (option && option.dataset.title && !titleInput.value.trim()) {
            titleInput.value = option.dataset.title;
          }
          updatePreviewLink(select);
        }

        function updatePreviewLink(select) {
          const link = document.getElementById('preview-link');
          const option = select.options[select.selectedIndex];
          const url = option ? option.dataset.preview : '';
          if (url) {
            link.href = url;
            link.removeAttribute('aria-disabled');
            link.classList.remove('is-disabled');
          } else {
            link.href = '#';
            link.setAttribute('aria-disabled', 'true');
            link.classList.add('is-disabled');
          }
        }

        (function () {
          const select = document.getElementById('library-item');
          const link = document.getElementById('preview-link');
          if (!select || !link) return;
          updatePreviewLink(select);
          if (select.value) fillAssignmentTitle(select);
          link.addEventListener('click', (event) => {
            if (link.getAttribute('aria-disabled') === 'true') event.preventDefault();
          });
        })();
      </script>

    </main>
  </div>
</body>
</html>
