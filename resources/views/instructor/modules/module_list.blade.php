{{-- resources/views/instructor/modules/module_list.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DataSensei — Module Library</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

  <style>
    :root {
      --bg: #0d1320;
      --surface: #111c2d;
      --surface2: #1a2638;
      --surface3: #0f1928;
      --border: #1e2f47;
      --border-hover: #2c4168;
      --accent: #3b82f6;
      --accent-hover: #2563eb;
      --accent2: #8b5cf6;
      --accent3: #10b981;
      --warn: #ef4444;
      --warn2: #f59e0b;
      --text: #fafafa;
      --muted: #7f93b0;
      --dim: #3d5272;
      --radius: 14px;
      --sidebar-w: 270px;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html, body {
      min-height: 100%;
      font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      background:
        radial-gradient(circle at top left, rgba(59, 130, 246, .12), transparent 34rem),
        radial-gradient(circle at top right, rgba(139, 92, 246, .10), transparent 28rem),
        var(--bg);
      color: var(--text);
      -webkit-font-smoothing: antialiased;
    }

    a {
      color: inherit;
    }

    .ds-shell {
      display: flex;
      min-height: 100vh;
      background:
        linear-gradient(135deg, rgba(59, 130, 246, .045), transparent 40%),
        linear-gradient(315deg, rgba(16, 185, 129, .035), transparent 42%);
    }

    .ds-main {
      flex: 1;
      min-width: 0;
      padding: 28px;
    }

    .library-wrap {
      max-width: 1500px;
      margin: 0 auto;
    }

    .top-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 20px;
      margin-bottom: 22px;
    }

    .page-kicker {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: var(--accent);
      font-size: .72rem;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .09em;
      margin-bottom: 8px;
    }

    .page-kicker::before {
      content: "";
      width: 8px;
      height: 8px;
      background: var(--accent3);
      border-radius: 50%;
      box-shadow: 0 0 18px rgba(16, 185, 129, .8);
    }

    .page-title {
      font-size: clamp(1.8rem, 3vw, 2.7rem);
      font-weight: 900;
      letter-spacing: -.06em;
      line-height: 1.05;
    }

    .page-subtitle {
      color: var(--muted);
      max-width: 780px;
      line-height: 1.65;
      margin-top: 10px;
      font-size: .95rem;
    }

    .header-actions {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      justify-content: flex-end;
    }

    .chip {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      border: 1px solid var(--border);
      background: rgba(255, 255, 255, .035);
      color: var(--muted);
      border-radius: 999px;
      padding: 10px 13px;
      font-size: .78rem;
      font-weight: 800;
      white-space: nowrap;
    }

    .chip strong {
      color: var(--text);
    }

    .alert {
      border-radius: 16px;
      padding: 14px 16px;
      font-weight: 800;
      line-height: 1.5;
      margin-bottom: 16px;
      border: 1px solid transparent;
    }

    .alert.success {
      background: rgba(16, 185, 129, .10);
      border-color: rgba(16, 185, 129, .30);
      color: #a7f3d0;
    }

    .alert.danger {
      background: rgba(239, 68, 68, .10);
      border-color: rgba(239, 68, 68, .30);
      color: #fecaca;
    }

    .library-panel {
      border: 1px solid var(--border);
      background: rgba(17, 28, 45, .92);
      border-radius: 24px;
      overflow: hidden;
      box-shadow: 0 18px 55px rgba(0, 0, 0, .25);
      backdrop-filter: blur(12px);
    }

    .toolbar {
      display: grid;
      grid-template-columns: minmax(260px, 1fr) 280px auto;
      gap: 14px;
      align-items: end;
      padding: 18px;
      background:
        linear-gradient(135deg, rgba(255,255,255,.045), rgba(255,255,255,.015)),
        var(--surface);
      border-bottom: 1px solid var(--border);
    }

    .field label {
      display: block;
      color: var(--dim);
      font-size: .68rem;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .08em;
      margin-bottom: 7px;
    }

    .input,
    .select {
      width: 100%;
      min-height: 42px;
      border: 1px solid var(--border);
      border-radius: 12px;
      background: var(--surface3);
      color: var(--text);
      padding: 10px 12px;
      font: inherit;
      font-size: .88rem;
      outline: none;
      transition: border-color .15s, box-shadow .15s, background .15s;
    }

    .input::placeholder {
      color: var(--dim);
    }

    .input:focus,
    .select:focus {
      border-color: rgba(59, 130, 246, .65);
      box-shadow: 0 0 0 4px rgba(59, 130, 246, .10);
    }

    .btn {
      min-height: 42px;
      border-radius: 12px;
      border: 1px solid transparent;
      padding: 0 16px;
      font: inherit;
      font-size: .82rem;
      font-weight: 900;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: transform .15s, background .15s, border-color .15s, box-shadow .15s;
      white-space: nowrap;
    }

    .btn:hover {
      transform: translateY(-1px);
    }

    .btn.primary {
      background: var(--accent);
      color: #fff;
      border-color: var(--accent);
      box-shadow: 0 12px 26px rgba(59, 130, 246, .24);
    }

    .btn.primary:hover {
      background: var(--accent-hover);
      border-color: var(--accent-hover);
    }

    .btn.secondary {
      background: rgba(255, 255, 255, .035);
      color: var(--text);
      border-color: var(--border);
    }

    .btn.secondary:hover {
      background: rgba(255, 255, 255, .06);
      border-color: var(--border-hover);
    }

    .btn.small {
      min-height: 34px;
      padding: 0 11px;
      font-size: .76rem;
      border-radius: 10px;
    }

    .body-area {
      padding: 18px;
    }

    .year-stack {
      display: grid;
      gap: 18px;
    }

    .year-card {
      border: 1px solid var(--border);
      border-radius: 20px;
      background: rgba(15, 25, 40, .74);
      overflow: hidden;
    }

    .year-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      padding: 16px 18px;
      background: rgba(255, 255, 255, .025);
      border-bottom: 1px solid var(--border);
    }

    .year-head h2 {
      font-size: .95rem;
      font-weight: 900;
      letter-spacing: -.01em;
    }

    .year-head span {
      color: var(--muted);
      font-size: .78rem;
      font-weight: 800;
    }

    .module-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
      gap: 14px;
      padding: 16px;
    }

    .module-card {
      border: 1px solid var(--border);
      background:
        linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.018)),
        var(--surface);
      border-radius: 20px;
      padding: 18px;
      position: relative;
      overflow: hidden;
      transition: transform .16s, border-color .16s, box-shadow .16s;
    }

    .module-card::before {
      content: "";
      position: absolute;
      inset: 0 0 auto 0;
      height: 3px;
      background: linear-gradient(90deg, var(--accent), var(--accent2), var(--accent3));
      opacity: .85;
    }

    .module-card:hover {
      transform: translateY(-2px);
      border-color: var(--border-hover);
      box-shadow: 0 16px 40px rgba(0, 0, 0, .22);
    }

    .module-top {
      display: flex;
      justify-content: space-between;
      gap: 14px;
      align-items: flex-start;
      margin-bottom: 15px;
    }

    .module-no {
      color: var(--accent3);
      font-size: .68rem;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .08em;
      margin-bottom: 6px;
    }

    .module-title {
      color: var(--text);
      text-decoration: none;
      font-size: 1.05rem;
      font-weight: 900;
      line-height: 1.3;
      letter-spacing: -.02em;
    }

    .module-title:hover {
      color: #bfdbfe;
      text-decoration: underline;
      text-underline-offset: 4px;
    }

    .module-desc {
      color: var(--muted);
      line-height: 1.55;
      margin-top: 8px;
      font-size: .84rem;
    }

    .version-list {
      display: grid;
      gap: 8px;
    }

    .version-row {
      display: flex;
      align-items: center;
      gap: 10px;
      border: 1px solid var(--border);
      background: rgba(255,255,255,.025);
      border-radius: 14px;
      padding: 11px;
      cursor: pointer;
      transition: border-color .15s, background .15s;
    }

    .version-row:hover {
      border-color: var(--border-hover);
      background: rgba(255,255,255,.045);
    }

    .version-row input {
      accent-color: var(--accent);
      width: 16px;
      height: 16px;
    }

    .version-info {
      min-width: 0;
      flex: 1;
    }

    .version-info strong {
      display: block;
      color: var(--text);
      font-size: .86rem;
      font-weight: 900;
    }

    .version-info small {
      display: block;
      color: var(--muted);
      font-size: .72rem;
      margin-top: 3px;
      line-height: 1.4;
    }

    .skip-row {
      background: rgba(255,255,255,.015);
    }

    .open-link {
      color: #93c5fd;
      text-decoration: none;
      font-size: .76rem;
      font-weight: 900;
      white-space: nowrap;
    }

    .open-link:hover {
      color: #bfdbfe;
      text-decoration: underline;
      text-underline-offset: 3px;
    }

    .assigned-pill {
      display: none;
      color: #a7f3d0;
      background: rgba(16, 185, 129, .11);
      border: 1px solid rgba(16, 185, 129, .25);
      border-radius: 999px;
      padding: 4px 8px;
      font-size: .66rem;
      font-weight: 900;
      white-space: nowrap;
    }

    .version-row.is-assigned .assigned-pill {
      display: inline-flex;
    }

    .empty-card {
      padding: 42px 24px;
      text-align: center;
      border: 1px dashed var(--border);
      border-radius: 20px;
      background: rgba(255,255,255,.025);
    }

    .empty-card h2 {
      font-size: 1.2rem;
      margin-bottom: 8px;
    }

    .empty-card p {
      color: var(--muted);
    }

    .hidden-by-search {
      display: none !important;
    }

    @media (max-width: 1100px) {
      .toolbar {
        grid-template-columns: 1fr;
      }

      .header-actions {
        justify-content: flex-start;
      }

      .top-row {
        flex-direction: column;
      }
    }

    @media (max-width: 820px) {
      .ds-shell {
        flex-direction: column;
      }

      .ds-main {
        padding: 18px;
      }

      .module-grid {
        grid-template-columns: 1fr;
      }

      .module-top {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>
  <div class="ds-shell">
    @includeIf('partials.instructor-sidebar')

    <main class="ds-main">
      <div class="library-wrap">
        <header class="top-row">
          <div>
            <div class="page-kicker">Instructor Workspace</div>
            <h1 class="page-title">Module Library</h1>
            <p class="page-subtitle">
              Browse module versions, preview NetAcad-style lesson content, and assign the selected versions to one of your classes.
            </p>
          </div>

          <div class="header-actions">
            <div class="chip">
              <strong>{{ $totalModuleTitles }}</strong>
              Module Titles
            </div>
            <div class="chip">
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

        <form method="POST" action="{{ route('modules.module-library.assign') }}">
          @csrf

          <section class="library-panel">
            <div class="toolbar">
              <div class="field">
                <label>Search module</label>
                <input
                  type="search"
                  id="moduleSearch"
                  class="input"
                  placeholder="Search by title, code, description, version..."
                  oninput="filterModules()"
                >
              </div>

              <div class="field">
                <label>Assign to class</label>
                <select name="class_id" id="classSelect" class="select" required onchange="updateAssignedMarks()">
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
              </div>

              <button type="submit" class="btn primary">
                Assign Selected
              </button>
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
                                >

                                <span class="version-info">
                                  <strong>{{ $version->version_name }}</strong>
                                  <small>
                                    {{ $version->module_code }}
                                    · {{ $version->version_code }}
                                    · {{ $version->estimated_minutes }} min
                                  </small>
                                </span>

                                <span class="assigned-pill">Assigned</span>

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
  </script>
</body>
</html>
