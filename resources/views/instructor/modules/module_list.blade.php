{{-- resources/views/instructor/modules/module_list.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Module Library — DataSensei</title>
<style>
    /* Module library. Colours, type and radius come from partials.design-system. */
    :root {
      --accent2: var(--ds-accent);
      --accent3: var(--ds-success);
      --warn: var(--ds-danger);
      --warn2: var(--ds-warning);
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html, body {
      min-height: 100%;
      font-family: var(--ds-font-sans);
      background: var(--bg);
      color: var(--text);
    }

    a {
      color: inherit;
    }

    .ds-shell {
      display: flex;
      min-height: 100vh;
    }

    .ds-main {
      flex: 1;
      min-width: 0;
      padding: 28px 32px 48px;
    }

    /* Fills whatever room is left beside the sidebar; the card grid adds columns as it grows. */
    .library-wrap {
      width: 100%;
      max-width: none;
    }

    /* ── Page header ────────────────────────────────────── */
    .top-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      flex-wrap: wrap;
      gap: 16px;
      margin-bottom: 24px;
    }

    .top-row > div:first-child {
      min-width: 0;
      flex: 1 1 320px;
    }

    .page-subtitle {
      max-width: 72ch;
      margin-top: 4px;
      color: var(--muted);
      font-size: .875rem;
      line-height: 1.5;
    }

    .header-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 4px 16px;
    }

    /* Counts shown as plain text. */
    .count-text {
      display: inline-flex;
      align-items: baseline;
      gap: 4px;
      color: var(--muted);
      font-size: .8125rem;
      white-space: nowrap;
    }

    .count-text strong {
      color: var(--text);
      font-weight: 600;
      font-variant-numeric: tabular-nums;
    }

    /* ── Alerts ─────────────────────────────────────────── */
    .alert {
      margin-bottom: 16px;
      padding: 12px 16px;
      border: 1px solid transparent;
      border-radius: var(--radius-sm);
      font-size: .875rem;
      line-height: 1.5;
    }

    .alert.success {
      background: var(--ds-success-soft);
      border-color: var(--ds-success-border);
      color: var(--ds-success-ink, #d1fae5);
    }

    .alert.danger {
      background: var(--ds-danger-soft);
      border-color: var(--ds-danger-border);
      color: var(--ds-danger-ink, #fee2e2);
    }

    .alert[hidden] { display: none; }

    /* Saving state and errors beside the fields (DataSensei Updates 7). */
    .toolbar-actions {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .save-state {
      margin: 0;
      color: var(--muted);
      font-size: .8125rem;
      white-space: nowrap;
    }

    .save-state.is-error { color: var(--ds-danger-text); }

    .field-error {
      margin: 6px 0 0;
      color: var(--ds-danger-text);
      font-size: .8125rem;
      line-height: 1.4;
    }

    .field-error[hidden] { display: none; }

    .select.is-invalid { border-color: var(--ds-danger-border); }

    .toolbar-error {
      grid-column: 1 / -1;
      margin: 0;
    }

    /* ── Filter bar ─────────────────────────────────────── */
    .library-panel {
      display: grid;
      gap: 24px;
    }

    .toolbar {
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(180px, 280px) auto;
      gap: 16px;
      align-items: end;
      padding: 16px 20px;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
    }

    .field label {
      display: block;
      margin-bottom: 6px;
      color: var(--ds-text-secondary);
      font-size: .8125rem;
      font-weight: 500;
      line-height: 1.35;
    }

    .input,
    .select {
      width: 100%;
      min-height: 38px;
      padding: 8px 12px;
      border: 1px solid var(--ds-input-border);
      border-radius: var(--radius-sm);
      background: var(--surface3);
      color: var(--text);
      font: 400 .875rem/1.4 var(--ds-font-sans);
      outline: none;
      transition: border-color .16s ease, box-shadow .16s ease;
    }

    .input::placeholder {
      color: var(--dim);
    }

    .input:focus,
    .select:focus {
      border-color: var(--accent);
      box-shadow: var(--ds-focus-ring);
    }

    /* ── Buttons ────────────────────────────────────────── */
    .btn {
      min-height: 38px;
      padding: 0 16px;
      border: 1px solid transparent;
      border-radius: var(--radius-sm);
      font: 500 .875rem/1.2 var(--ds-font-sans);
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: background .16s ease, border-color .16s ease, color .16s ease;
      white-space: nowrap;
    }

    .btn.primary {
      background: var(--accent);
      color: #fff;
      border-color: var(--accent);
    }

    .btn.primary:hover {
      background: var(--accent-hover);
      border-color: var(--accent-hover);
    }

    .btn.secondary {
      background: var(--surface2);
      color: var(--text);
      border-color: var(--ds-border-strong);
    }

    .btn.secondary:hover {
      background: var(--ds-surface-hover);
    }

    .btn.small {
      min-height: 32px;
      padding: 0 12px;
      font-size: .8125rem;
    }

    /* ── Modules, grouped by year ───────────────────────── */
    .year-stack {
      display: grid;
      gap: 32px;
    }

    .year-card {
      min-width: 0;
    }

    .year-head {
      display: flex;
      align-items: baseline;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 4px 16px;
      padding-bottom: 12px;
      margin-bottom: 16px;
      border-bottom: 1px solid var(--border);
    }

    .year-head h2 {
      font-size: 1rem;
      font-weight: 600;
      line-height: 1.35;
    }

    .year-head span {
      color: var(--muted);
      font-size: .8125rem;
    }

    .module-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(min(360px, 100%), 1fr));
      gap: 16px;
    }

    .module-card {
      min-width: 0;
      padding: 20px;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      background: var(--surface);
    }

    .module-top {
      display: flex;
      justify-content: space-between;
      gap: 16px;
      align-items: flex-start;
      margin-bottom: 16px;
    }

    .module-top > div {
      min-width: 0;
      flex: 1 1 auto;
    }

    .module-top > .btn {
      flex-shrink: 0;
    }

    .module-no {
      margin-bottom: 4px;
      color: var(--muted);
      font-size: .75rem;
      font-weight: 500;
    }

    .module-title {
      color: var(--text);
      text-decoration: none;
      font-size: .9375rem;
      font-weight: 600;
      line-height: 1.35;
      overflow-wrap: anywhere;
    }

    .module-title:hover {
      color: var(--ds-accent-text);
      text-decoration: underline;
      text-underline-offset: 3px;
    }

    .module-desc {
      margin-top: 4px;
      color: var(--muted);
      font-size: .8125rem;
      line-height: 1.5;
    }

    /* Version choices: one list, rows separated by rules. */
    .version-list {
      display: grid;
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      background: var(--surface3);
      overflow: hidden;
    }

    .version-row {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 12px;
      border-top: 1px solid var(--border);
      cursor: pointer;
      transition: background .12s ease;
    }

    .version-row:first-child {
      border-top: 0;
    }

    .version-row:hover {
      background: var(--surface2);
    }

    .version-row input {
      flex-shrink: 0;
      width: 16px;
      height: 16px;
      accent-color: var(--accent);
    }

    .version-info {
      min-width: 0;
      flex: 1;
    }

    .version-info strong {
      display: block;
      color: var(--text);
      font-size: .875rem;
      font-weight: 600;
      line-height: 1.4;
      overflow-wrap: anywhere;
    }

    .version-info small {
      display: block;
      margin-top: 2px;
      color: var(--muted);
      font-size: .75rem;
      line-height: 1.45;
      overflow-wrap: anywhere;
    }

    .skip-row .version-info strong {
      color: var(--ds-text-secondary);
      font-weight: 500;
    }

    .open-link {
      flex-shrink: 0;
      color: var(--ds-accent-text);
      text-decoration: none;
      font-size: .8125rem;
      font-weight: 500;
      white-space: nowrap;
    }

    .open-link:hover {
      color: var(--text);
      text-decoration: underline;
      text-underline-offset: 3px;
    }

    /* Plain text (DataSensei Updates 9). */
    .assigned-label {
      display: none;
      flex-shrink: 0;
      color: var(--ds-success-text);
      font-size: .8125rem;
      font-weight: 600;
      line-height: 1.4;
      white-space: nowrap;
    }

    .remove-link {
      display: none;
      padding: 0;
      border: 0;
      background: none;
      color: var(--ds-danger-text, #fca5a5);
      font: inherit;
      font-size: .8125rem;
      font-weight: 500;
      cursor: pointer;
    }

    .remove-link:hover { text-decoration: underline; }

    .version-row.is-assigned .remove-link { display: inline; }

    .version-row.is-assigned .assigned-label {
      display: inline;
    }

    /* ── Empty ──────────────────────────────────────────── */
    .empty-card {
      padding: 32px 20px;
      text-align: center;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      background: var(--surface);
    }

    .empty-card h2 {
      margin-bottom: 4px;
      font-size: .9375rem;
      font-weight: 600;
    }

    .empty-card p {
      color: var(--muted);
      font-size: .875rem;
      line-height: 1.5;
    }

    .hidden-by-search {
      display: none !important;
    }

    @media (max-width: 900px) {
      .ds-main {
        padding: 24px 20px 40px;
      }
    }

    @media (max-width: 640px) {
      .ds-main {
        padding: 20px 16px 32px;
      }

      .top-row {
        align-items: flex-start;
        flex-direction: column;
        margin-bottom: 20px;
      }

      .top-row > div:first-child {
        flex-basis: auto;
      }

      .toolbar {
        grid-template-columns: minmax(0, 1fr);
        padding: 16px;
      }

      .toolbar-actions {
        flex-direction: column;
        align-items: stretch;
      }

      .toolbar-actions .btn {
        width: 100%;
      }

      .module-card {
        padding: 16px;
      }

      .module-top {
        flex-direction: column;
        gap: 12px;
      }
    }

    @media (prefers-reduced-motion: reduce) {
      .btn, .version-row { transition: none; }
    }
  </style>
  @include('partials.admin-inspired-page-style')
    @include('partials.page-head', ['pageTitle' => 'Module Library', 'pageDescription' => 'The DataSensei module library available to your classes.'])
</head>
<body class="ds-admin-inspired">
  <div class="ds-shell">
    @includeIf('partials.instructor-sidebar')

    <main class="ds-main">
      <div class="library-wrap">
        <header class="top-row">
          <div>
            <h1 class="page-title ds-page-title">Module Library</h1>
            <p class="page-subtitle">
              Browse module versions, preview DataSensei lesson content, and assign the selected versions to one of your classes.
            </p>
          </div>

          <div class="header-actions">
            <div class="count-text">
              <strong>{{ $totalModuleTitles }}</strong>
              Module Titles
            </div>
            <div class="count-text">
              <strong>{{ $totalModuleVersions }}</strong>
              Versions
            </div>
          </div>
        </header>

        @if (session('success'))
          <div class="alert success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
          <div class="alert danger">{{ $errors->first() }}</div>
        @endif

        <div class="alert" role="status" aria-live="polite" data-library-message hidden></div>

        <form method="POST" action="{{ route('modules.module-library.assign') }}" data-library-form>
          @csrf

          <section class="library-panel">
            <div class="toolbar">
              <div class="field">
                <label>Search module</label>
                <input
                  type="search"
                  id="moduleSearch"
                  class="input"
                  placeholder="Search by title, description, or version..."
                  oninput="filterModules()"
                >
              </div>

              <div class="field">
                <label>Assign to class</label>
                <select name="class_id" id="classSelect" class="select" required onchange="updateAssignedMarks()" aria-describedby="classSelectError">
                  <option value="">Select class...</option>
                  @foreach ($classes as $class)
                    <option
                      value="{{ $class->id }}"
                      data-assigned='@json($class->assigned_module_ids ?? [])'
                    >
                      {{ $class->name }}
                    </option>
                  @endforeach
                </select>
                <p class="field-error" id="classSelectError" data-error-for="class_id" hidden></p>
              </div>

              <div class="toolbar-actions">
                <button type="submit" class="btn primary" data-assign-button>
                  Assign Selected
                </button>
                <p class="save-state" data-save-state aria-live="polite"></p>
              </div>

              <p class="field-error toolbar-error" data-error-for="selected_modules" hidden></p>
            </div>

            <div class="body-area">
              <div class="year-stack">
                @forelse ($modulesByYear as $yearLevel => $moduleGroups)
                  <section class="year-card" data-year-card>
                    <div class="year-head">
                      <h2>{{ $yearLevel }}</h2>
                      <span>{{ $moduleGroups->count() }} module title(s)</span>
                    </div>

                    <div class="module-grid">
                      @foreach ($moduleGroups as $moduleNo => $versions)
                        @php
                          $firstVersion = $versions->first();
                          $searchText = strtolower(
                            $firstVersion->title . ' ' .
                            $firstVersion->description . ' ' .
                            $firstVersion->module_code . ' ' .
                            $versions->pluck('version_name')->implode(' ') . ' ' .
                            $versions->pluck('version_code')->implode(' ')
                          );
                        @endphp

                        <article class="module-card" data-module-card data-search="{{ $searchText }}">
                          <div class="module-top">
                            <div>
                              <div class="module-no">Module {{ $moduleNo }}</div>

                              <a
                                href="{{ route('modules.module-library.show', $firstVersion) }}"
                                class="module-title"
                              >
                                {{ $firstVersion->title }}
                              </a>

                              <p class="module-desc">
                                {{ $firstVersion->description }}
                              </p>
                            </div>

                            <a
                              href="{{ route('modules.module-library.show', $firstVersion) }}"
                              class="btn secondary small"
                            >
                              View Contents
                            </a>
                          </div>

                          <div class="version-list">
                            @foreach ($versions as $version)
                              <label class="version-row" data-version-row data-module-id="{{ $version->id }}">
                                <input
                                  type="radio"
                                  name="selected_modules[{{ $moduleNo }}]"
                                  value="{{ $version->id }}"
                                  @disabled(! $version->is_active)
                                >

                                <span class="version-info">
                                  <strong>{{ $version->version_name }}</strong>
                                  <small>
                                    About {{ $version->estimated_minutes }} min
                                    @unless ($version->is_active)
                                     , retired (existing classes only)
                                    @endunless
                                  </small>
                                </span>

                                <span class="assigned-label">Assigned</span>

                                <button
                                  type="submit"
                                  class="remove-link"
                                  formaction="{{ route('modules.module-library.unassign') }}"
                                  formnovalidate
                                  name="remove_module_id"
                                  value="{{ $version->id }}"
                                  onclick="return confirm('Remove this module from the selected class? Its students will no longer see it.');"
                                >
                                  Remove from class
                                </button>

                                <a
                                  href="{{ route('modules.module-library.show', $version) }}"
                                  class="open-link"
                                  onclick="event.stopPropagation();"
                                >
                                  Open
                                </a>
                              </label>
                            @endforeach

                            <label class="version-row skip-row">
                              <input
                                type="radio"
                                name="selected_modules[{{ $moduleNo }}]"
                                value=""
                                checked
                              >

                              <span class="version-info">
                                <strong>Do not include</strong>
                                <small>Skip this module for now</small>
                              </span>
                            </label>
                          </div>
                        </article>
                      @endforeach
                    </div>
                  </section>
                @empty
                  <section class="empty-card">
                    <h2>No modules available</h2>
                    <p>Seed your module library first, then refresh this page.</p>
                  </section>
                @endforelse
              </div>
            </div>
          </section>
        </form>
      </div>
    </main>
  </div>

  <script>
    function filterModules() {
      const query = document.getElementById('moduleSearch').value.trim().toLowerCase();
      const cards = document.querySelectorAll('[data-module-card]');

      cards.forEach(card => {
        const haystack = card.dataset.search || '';
        card.classList.toggle('hidden-by-search', query && !haystack.includes(query));
      });

      document.querySelectorAll('[data-year-card]').forEach(yearCard => {
        const visibleCards = yearCard.querySelectorAll('[data-module-card]:not(.hidden-by-search)');
        yearCard.classList.toggle('hidden-by-search', visibleCards.length === 0);
      });
    }

    function updateAssignedMarks() {
      const select = document.getElementById('classSelect');
      const selected = select.options[select.selectedIndex];

      let assignedIds = [];

      try {
        assignedIds = JSON.parse(selected.dataset.assigned || '[]').map(String);
      } catch (error) {
        assignedIds = [];
      }

      document.querySelectorAll('[data-version-row]').forEach(row => {
        const isAssigned = assignedIds.includes(String(row.dataset.moduleId));
        row.classList.toggle('is-assigned', isAssigned);
      });
    }

    document.addEventListener('DOMContentLoaded', updateAssignedMarks);

    // Assigning and removing modules save in the background (DataSensei
    // Updates 7): the page stays where it is, the class stays selected and
    // the Assigned marks update. Without fetch the form posts as before.
    (function () {
      const form = document.querySelector('[data-library-form]');
      if (!form || !window.fetch || !window.FormData) return;

      const select = document.getElementById('classSelect');
      const state = form.querySelector('[data-save-state]');
      const message = document.querySelector('[data-library-message]');
      const token = (form.querySelector('input[name="_token"]') || {}).value || '';
      let busy = false;

      function setState(text, isError) {
        state.textContent = text;
        state.classList.toggle('is-error', !!isError);
      }

      function showMessage(text, kind) {
        message.textContent = text || '';
        message.className = 'alert ' + (kind || 'success');
        message.hidden = !text;
      }

      function clearErrors() {
        form.querySelectorAll('[data-error-for]').forEach(node => { node.hidden = true; node.textContent = ''; });
        select.classList.remove('is-invalid');
      }

      function showErrors(errors) {
        let shown = false;
        Object.keys(errors || {}).forEach(field => {
          const key = field.split('.')[0];
          const target = form.querySelector('[data-error-for="' + key + '"]') || form.querySelector('[data-error-for="selected_modules"]');
          const text = [].concat(errors[field])[0];
          if (!target || !text) return;
          target.textContent = text;
          target.hidden = false;
          if (key === 'class_id') select.classList.add('is-invalid');
          shown = true;
        });
        return shown;
      }

      function setBusy(value) {
        busy = value;
        form.querySelectorAll('button[type="submit"]').forEach(button => { button.disabled = value; });
        form.setAttribute('aria-busy', value ? 'true' : 'false');
      }

      select.addEventListener('change', () => {
        select.classList.remove('is-invalid');
        const error = form.querySelector('[data-error-for="class_id"]');
        error.hidden = true;
      });

      form.addEventListener('submit', event => {
        const submitter = event.submitter || document.activeElement;
        const removing = !!(submitter && submitter.name === 'remove_module_id');
        event.preventDefault();
        if (busy) return;

        clearErrors();
        showMessage('');

        if (!select.value) {
          showErrors({ class_id: ['Select a class first.'] });
          setState('Failed to save', true);
          select.focus();
          return;
        }

        const data = new FormData(form);
        let url = form.action;
        if (removing) {
          url = submitter.getAttribute('formaction');
          data.set('remove_module_id', submitter.value);
        }

        setBusy(true);
        setState('Saving...');

        fetch(url, {
          method: 'POST',
          body: data,
          credentials: 'same-origin',
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token },
        })
          .then(response => response.json().catch(() => ({})).then(json => ({ response, json })))
          .then(({ response, json }) => {
            if (response.ok) {
              const option = select.querySelector('option[value="' + json.class_id + '"]');
              if (option) option.dataset.assigned = JSON.stringify(json.assigned_module_ids || []);
              updateAssignedMarks();
              if (!removing) {
                form.querySelectorAll('.skip-row input[type="radio"]').forEach(radio => { radio.checked = true; });
              }
              setState('Saved');
              showMessage(json.message, 'success');
              return;
            }

            setState('Failed to save', true);
            if (response.status === 422) {
              if (!showErrors(json.errors)) showMessage(json.message || 'The changes could not be saved.', 'danger');
            } else if (response.status === 419) {
              showMessage('Your session has expired. Reload the page, then try again.', 'danger');
            } else if (response.status === 401) {
              showMessage('You have been signed out. Sign in again, then try again.', 'danger');
            } else {
              showMessage(json.message || 'The changes could not be saved (error ' + response.status + '). Try again.', 'danger');
            }
          })
          .catch(() => {
            setState('Failed to save', true);
            showMessage('Could not reach the server. Check your connection and try again.', 'danger');
          })
          .finally(() => setBusy(false));
      });
    })();
  </script>
</body>
</html>
