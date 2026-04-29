<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>DataSensei — {{ $module->title }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <script>
    window.USER_ORG_ID = @json(auth()->check() ? auth()->user()->organization_id : null);
  </script>
  <style>
    :root {
      --bg:           #0d1320;
      --surface:      #111c2d;
      --surface2:     #1a2638;
      --border:       #1e2f47;
      --border-hover: #2c4168;
      --accent:       #3b82f6; 
      --accent-hover: #2563eb;
      --accent3:      #10b981;
      --text:         #fafafa;
      --muted:        #7f93b0;
      --dim:          #3d5272;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      height: 100vh;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      margin: 0;
      -webkit-font-smoothing: antialiased;
    }

    /* ── NAMESPACED LEARNING ROOM COMPONENTS ── */

    /* Topbar */
    .page-learning-topbar { height: 64px; background: var(--surface); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 24px; flex-shrink: 0; z-index: 50; position: relative; }
    .page-learning-topbar-left { display: flex; align-items: center; gap: 16px; font-weight: 600; font-size: 1.125rem; }
    .page-learning-topbar-left svg { color: var(--accent); }
    
    .page-learning-mobile-btn { display: none; background: transparent; border: none; color: var(--text); cursor: pointer; padding: 4px; border-radius: 4px; transition: background 0.15s; }
    .page-learning-mobile-btn:hover { background: var(--surface2); }
    
    .page-learning-exit-btn { background: transparent; border: 1px solid var(--border); color: var(--muted); padding: 8px 16px; border-radius: 6px; cursor: pointer; text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: all 0.15s; }
    .page-learning-exit-btn:hover { background: var(--surface2); color: var(--text); border-color: var(--border-hover); }

    /* Workspace */
    .page-learning-workspace { display: flex; flex: 1; overflow: hidden; position: relative; }

    /* Left Sidebar (Course Outline) */
    .page-learning-sidebar { width: 320px; background: var(--surface); border-right: 1px solid var(--border); display: flex; flex-direction: column; flex-shrink: 0; z-index: 40; transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .page-learning-sidebar-header { padding: 24px; border-bottom: 1px solid var(--border); background: var(--surface2); }
    .page-learning-module-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--dim); font-weight: 600; margin-bottom: 8px; }
    .page-learning-module-title { font-size: 0.95rem; font-weight: 600; color: var(--text); margin-bottom: 16px; line-height: 1.4; }
    
    /* Progress Bar */
    .page-learning-progress-wrap { display: flex; align-items: center; gap: 12px; }
    .page-learning-progress-bar { flex: 1; height: 6px; background: var(--bg); border-radius: 4px; overflow: hidden; }
    .page-learning-progress-fill { height: 100%; background: var(--accent3); transition: width 0.4s; }
    .page-learning-progress-text { font-size: 0.75rem; font-weight: 600; color: var(--accent3); font-family: 'JetBrains Mono', monospace; }

    /* Lesson Links */
    .page-learning-lesson-list { flex: 1; overflow-y: auto; padding: 12px 0; }
    .page-learning-lesson-item { display: flex; align-items: center; gap: 14px; padding: 14px 24px; text-decoration: none; color: var(--muted); font-size: 0.875rem; border-left: 3px solid transparent; transition: all 0.15s; }
    .page-learning-lesson-item:hover { background: var(--bg); color: var(--text); }
    .page-learning-lesson-item.is-active { background: rgba(59, 130, 246, 0.08); border-left-color: var(--accent); color: var(--text); font-weight: 600; }
    
    .page-learning-check-icon { width: 18px; height: 18px; border-radius: 50%; border: 2px solid var(--dim); display: flex; align-items: center; justify-content: center; color: transparent; flex-shrink: 0; transition: all 0.2s; }
    .page-learning-lesson-item.is-completed .page-learning-check-icon { background: var(--accent3); border-color: var(--accent3); color: #fff; }

    /* Content Area */
    .page-learning-content-area { flex: 1; display: flex; flex-direction: column; overflow-y: auto; position: relative; background: var(--bg); }
    .page-learning-content-inner { max-width: 850px; margin: 0 auto; padding: 60px 40px; width: 100%; flex: 1; }
    
    /* Database HTML Content Formatting */
    .page-learning-lesson-body { color: var(--text); }
    .page-learning-lesson-body h2 { font-size: 2rem; font-weight: 700; margin-bottom: 24px; color: #fff; letter-spacing: -0.02em; }
    .page-learning-lesson-body h3 { font-size: 1.35rem; font-weight: 600; color: var(--text); margin-top: 36px; margin-bottom: 16px; }
    .page-learning-lesson-body p { font-size: 1.05rem; color: var(--muted); line-height: 1.8; margin-bottom: 20px; }
    .page-learning-lesson-body strong { color: var(--text); }
    .page-learning-lesson-body div[style*="JetBrains Mono"] { line-height: 1.5; }

    /* Footer */
    .page-learning-lesson-footer { background: var(--surface); border-top: 1px solid var(--border); padding: 20px 40px; display: flex; justify-content: flex-end; align-items: center; position: sticky; bottom: 0; }
    .page-learning-btn-complete { display: inline-flex; align-items: center; gap: 10px; background: var(--accent); color: #fff; border: none; padding: 12px 28px; border-radius: 6px; font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: background 0.15s; font-family: 'Inter', sans-serif; }
    .page-learning-btn-complete:hover { background: var(--accent-hover); }

    /* Scrollbars */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: var(--surface2); border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--dim); }

    /* Mobile Overlay */
    .page-learning-mobile-overlay { display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); z-index: 30; opacity: 0; transition: opacity 0.3s; }

    @media (max-width: 900px) {
      .page-learning-mobile-btn { display: block; }
      .page-learning-sidebar { position: absolute; left: -320px; top: 0; height: 100%; box-shadow: 5px 0 25px rgba(0,0,0,0.5); }
      .page-learning-sidebar.is-open { left: 0; }
      .page-learning-mobile-overlay.is-open { display: block; opacity: 1; }
      .page-learning-content-inner { padding: 40px 24px; }
      .page-learning-lesson-body h2 { font-size: 1.6rem; }
      .page-learning-lesson-body h3 { font-size: 1.2rem; }
    }

    @media (max-width: 500px) {
      .page-learning-topbar-left { font-size: 1rem; gap: 10px; }
      .page-learning-exit-btn { padding: 6px 12px; font-size: 0.8rem; }
      .page-learning-lesson-footer { padding: 16px 24px; justify-content: center; }
      .page-learning-btn-complete { width: 100%; justify-content: center; }
    }
  </style>
</head>
<body>

  <header class="page-learning-topbar">
    <div class="page-learning-topbar-left">
      <button id="mobileMenuBtn" class="page-learning-mobile-btn">
        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>

      <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
      </svg>
      <span>Learning Environment</span>
    </div>
    <a href="{{ route('modules.index') }}" class="page-learning-exit-btn">Exit Course</a>
  </header>

  <div class="page-learning-workspace">
    
    <div id="mobileOverlay" class="page-learning-mobile-overlay"></div>

    <div id="courseSidebar" class="page-learning-sidebar">
      <div class="page-learning-sidebar-header">
        <div class="page-learning-module-label">Current Module</div>
        <div class="page-learning-module-title">{{ $module->title }}</div>
        
        <div class="page-learning-progress-wrap">
          <div class="page-learning-progress-bar">
            <div class="page-learning-progress-fill" style="width: {{ $progressPct }}%;"></div>
          </div>
          <div class="page-learning-progress-text">{{ $progressPct }}%</div>
        </div>
      </div>
      
      <div class="page-learning-lesson-list">
        @foreach($module->lessons as $lesson)
          @php
            $isCompleted = in_array($lesson->id, $completedLessonIds);
            $isActive = $activeLesson->id === $lesson->id;
          @endphp
          
          <a href="{{ route('lesson.show', ['module' => $module->id, 'lesson' => $lesson->id]) }}" 
             class="page-learning-lesson-item {{ $isCompleted ? 'is-completed' : '' }} {{ $isActive ? 'is-active' : '' }}">
            
            <div class="page-learning-check-icon">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <polyline points="20 6 9 17 4 12"/>
              </svg>
            </div>
            
            {{ $lesson->title }}
          </a>
        @endforeach
      </div>
    </div>

    <div class="page-learning-content-area">
      <div class="page-learning-content-inner">
        <div class="page-learning-lesson-body">
          {!! $activeLesson->content !!}
        </div>
      </div>

      <div class="page-learning-lesson-footer">
        <form action="{{ route('lesson.complete', $activeLesson->id) }}" method="POST" style="width: 100%; display: flex; justify-content: flex-end;">
          @csrf
          <button type="submit" class="page-learning-btn-complete">
            Mark as Complete & Continue
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
          </button>
        </form>
      </div>
    </div>

  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const menuBtn = document.getElementById('mobileMenuBtn');
      const sidebar = document.getElementById('courseSidebar');
      const overlay = document.getElementById('mobileOverlay');

      menuBtn.addEventListener('click', function() {
        sidebar.classList.toggle('is-open');
        overlay.classList.toggle('is-open');
      });

      overlay.addEventListener('click', function() {
        sidebar.classList.remove('is-open');
        overlay.classList.remove('is-open');
      });
    });

    function launchIDE(button) {
      const windowContainer = button.closest('.code-window');

      // Read the language label from the header bar (e.g. "SQL — SELECT Basics"
      // or "PYTHON — Connect to SQLite"). This is the first <span> inside the
      // dark header div that sits directly before the button's parent row.
      const headerBar  = windowContainer.querySelector('div:first-child');
      const labelSpan  = headerBar ? headerBar.querySelector('span') : null;
      const labelText  = labelSpan ? labelSpan.innerText.trim().toUpperCase() : '';

      const codeElement = windowContainer.querySelector('.code-content');
      const rawCode = codeElement.innerText.trim();

      // Always store the return URL so both destinations can show "Back to Lesson"
      sessionStorage.setItem('datasensei_return_url', window.location.href);

      // ── Route decision ────────────────────────────────────────────────────
      // Labels that begin with "SQL" contain pure SQL — send to the SQL Sandbox.
      // Everything else (PYTHON, or mixed Python+SQL) goes to the Python IDE.
      if (labelText.startsWith('SQL')) {
        sessionStorage.setItem('datasensei_pending_sql_code', rawCode);
        window.location.href = "{{ route('sql-sandbox.index') }}";
      } else {
        sessionStorage.setItem('datasensei_pending_code', rawCode);
        window.location.href = "{{ route('ide.index') }}";
      }
    }
  </script>

</body>
</html>