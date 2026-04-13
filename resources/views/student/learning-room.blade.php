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
      /* DataSensei Original Core Palette */
      --bg:           #0d1320;
      --surface:      #111c2d;
      --surface2:     #1a2638;
      --border:       #1e2f47;
      --border-hover: #2c4168;
      
      /* Accents */
      --accent:       #3b82f6; 
      --accent-hover: #2563eb;
      --accent3:      #10b981; /* Green for completion */
      
      /* Typography */
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
      -webkit-font-smoothing: antialiased;
    }

    /* ── TOP NAVIGATION BAR ── */
    .topbar {
      height: 64px;
      background: var(--surface);
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 24px;
      flex-shrink: 0;
      z-index: 50; /* Keep above sidebar */
      position: relative;
    }
    .topbar-left {
      display: flex;
      align-items: center;
      gap: 16px;
      font-weight: 600;
      font-size: 1.125rem;
    }
    .topbar-left svg { color: var(--accent); }
    
    /* MOBILE MENU BUTTON (Hidden on Desktop) */
    .mobile-menu-btn {
      display: none;
      background: transparent;
      border: none;
      color: var(--text);
      cursor: pointer;
      padding: 4px;
      border-radius: 4px;
      transition: background 0.15s;
    }
    .mobile-menu-btn:hover { background: var(--surface2); }
    
    .exit-btn {
      background: transparent;
      border: 1px solid var(--border);
      color: var(--muted);
      padding: 8px 16px;
      border-radius: 6px;
      cursor: pointer;
      text-decoration: none;
      font-size: 0.875rem;
      font-weight: 500;
      transition: all 0.15s;
    }
    .exit-btn:hover {
      background: var(--surface2);
      color: var(--text);
      border-color: var(--border-hover);
    }

    /* ── MAIN WORKSPACE ── */
    .workspace {
      display: flex;
      flex: 1;
      overflow: hidden;
      position: relative; /* For absolute positioning of mobile sidebar */
    }

    /* ── LEFT SIDEBAR (Course Outline) ── */
    .course-sidebar {
      width: 320px;
      background: var(--surface);
      border-right: 1px solid var(--border);
      display: flex;
      flex-direction: column;
      flex-shrink: 0;
      z-index: 40;
      transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .sidebar-header {
      padding: 24px;
      border-bottom: 1px solid var(--border);
      background: var(--surface2);
    }
    .module-label {
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--dim);
      font-weight: 600;
      margin-bottom: 8px;
    }
    .module-title {
      font-size: 0.95rem;
      font-weight: 600;
      color: var(--text);
      margin-bottom: 16px;
      line-height: 1.4;
    }
    
    /* Progress Bar */
    .progress-wrap { display: flex; align-items: center; gap: 12px; }
    .progress-bar { flex: 1; height: 6px; background: var(--bg); border-radius: 4px; overflow: hidden; }
    .progress-fill { height: 100%; background: var(--accent3); transition: width 0.4s; }
    .progress-text { font-size: 0.75rem; font-weight: 600; color: var(--accent3); font-family: 'JetBrains Mono', monospace; }

    /* Lesson Links */
    .lesson-list { flex: 1; overflow-y: auto; padding: 12px 0; }
    .lesson-item {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 14px 24px;
      text-decoration: none;
      color: var(--muted);
      font-size: 0.875rem;
      border-left: 3px solid transparent;
      transition: all 0.15s;
    }
    .lesson-item:hover { background: var(--bg); color: var(--text); }
    .lesson-item.active { background: rgba(59, 130, 246, 0.08); border-left-color: var(--accent); color: var(--text); font-weight: 600; }
    
    /* Checkmark Status */
    .check-icon {
      width: 18px; height: 18px; border-radius: 50%; border: 2px solid var(--dim);
      display: flex; align-items: center; justify-content: center; color: transparent; flex-shrink: 0; transition: all 0.2s;
    }
    .lesson-item.completed .check-icon { background: var(--accent3); border-color: var(--accent3); color: #fff; }

    /* ── RIGHT CONTENT AREA (The Lecture) ── */
    .content-area {
      flex: 1; display: flex; flex-direction: column; overflow-y: auto; position: relative; background: var(--bg);
    }
    
    .content-inner {
      max-width: 850px; margin: 0 auto; padding: 60px 40px; width: 100%; flex: 1;
    }
    
    /* Formatting for the database HTML content */
    .lesson-body { color: var(--text); }
    
    .lesson-body h2 {
      font-size: 2rem; font-weight: 700; margin-bottom: 24px; color: #fff; letter-spacing: -0.02em;
    }
    
    /* THE NEW H3 SPACING FIX */
    .lesson-body h3 {
      font-size: 1.35rem; font-weight: 600; color: var(--text); margin-top: 36px; margin-bottom: 16px;
    }
    
    .lesson-body p {
      font-size: 1.05rem; color: var(--muted); line-height: 1.8; margin-bottom: 20px;
    }
    
    .lesson-body strong { color: var(--text); }
    
    .lesson-body div[style*="JetBrains Mono"] { line-height: 1.5; }

    /* ── BOTTOM NAVIGATION FOOTER ── */
    .lesson-footer {
      background: var(--surface); border-top: 1px solid var(--border); padding: 20px 40px; display: flex; justify-content: flex-end; align-items: center; position: sticky; bottom: 0;
    }
    .btn-complete {
      display: inline-flex; align-items: center; gap: 10px; background: var(--accent); color: #fff; border: none; padding: 12px 28px; border-radius: var(--radius-sm); font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: background 0.15s; font-family: 'Inter', sans-serif;
    }
    .btn-complete:hover { background: var(--accent-hover); }

    /* Scrollbars */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: var(--surface2); border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--dim); }

    /* MOBILE OVERLAY (Hidden by default) */
    .mobile-overlay {
      display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); z-index: 30; opacity: 0; transition: opacity 0.3s;
    }

    /* ── RESPONSIVE MEDIA QUERIES ── */
    @media (max-width: 900px) {
      .mobile-menu-btn { display: block; }
      
      .course-sidebar {
        position: absolute;
        left: -320px; /* Hidden off-screen to the left */
        top: 0;
        height: 100%;
        box-shadow: 5px 0 25px rgba(0,0,0,0.5);
      }
      
      /* Classes toggled by JavaScript */
      .course-sidebar.open { left: 0; }
      .mobile-overlay.open { display: block; opacity: 1; }
      
      .content-inner { padding: 40px 24px; }
      .lesson-body h2 { font-size: 1.6rem; }
      .lesson-body h3 { font-size: 1.2rem; }
    }

    @media (max-width: 500px) {
      .topbar-left { font-size: 1rem; gap: 10px; }
      .exit-btn { padding: 6px 12px; font-size: 0.8rem; }
      .lesson-footer { padding: 16px 24px; justify-content: center; }
      .btn-complete { width: 100%; justify-content: center; }
    }
  </style>
</head>
<body>

  <header class="topbar">
    <div class="topbar-left">
      <button id="mobileMenuBtn" class="mobile-menu-btn">
        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>

      <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
      </svg>
      <span class="header-title">Learning Environment</span>
    </div>
    <a href="{{ route('module') }}" class="exit-btn">Exit Course</a>
  </header>

  <div class="workspace">
    
    <div id="mobileOverlay" class="mobile-overlay"></div>

    <div id="courseSidebar" class="course-sidebar">
      <div class="sidebar-header">
        <div class="module-label">Current Module</div>
        <div class="module-title">{{ $module->title }}</div>
        
        <div class="progress-wrap">
          <div class="progress-bar">
            <div class="progress-fill" style="width: {{ $progressPct }}%;"></div>
          </div>
          <div class="progress-text">{{ $progressPct }}%</div>
        </div>
      </div>
      
      <div class="lesson-list">
        @foreach($module->lessons as $lesson)
          @php
            $isCompleted = in_array($lesson->id, $completedLessonIds);
            $isActive = $activeLesson->id === $lesson->id;
          @endphp
          
          <a href="{{ route('lesson.show', ['module' => $module->id, 'lesson' => $lesson->id]) }}" 
             class="lesson-item {{ $isCompleted ? 'completed' : '' }} {{ $isActive ? 'active' : '' }}">
            
            <div class="check-icon">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <polyline points="20 6 9 17 4 12"/>
              </svg>
            </div>
            
            {{ $lesson->title }}
          </a>
        @endforeach
      </div>
    </div>

    <div class="content-area">
      <div class="content-inner">
        <div class="lesson-body">
          {!! $activeLesson->content !!}
        </div>
      </div>

      <div class="lesson-footer">
        <form action="{{ route('lesson.complete', $activeLesson->id) }}" method="POST" style="width: 100%; display: flex; justify-content: flex-end;">
          @csrf
          <button type="submit" class="btn-complete">
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

      // Toggle sidebar when clicking the hamburger menu
      menuBtn.addEventListener('click', function() {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('open');
      });

      // Close sidebar when clicking the dark background overlay
      overlay.addEventListener('click', function() {
        sidebar.classList.remove('open');
        overlay.classList.remove('open');
      });
    });

    // ── LAUNCH TO COMPILER ENGINE ──
    function launchIDE(button) {
      const windowContainer = button.closest('.code-window');
      const codeElement = windowContainer.querySelector('.code-content');
      const rawCode = codeElement.innerText;

      // Save the code
      sessionStorage.setItem('datasensei_pending_code', rawCode.trim());
      
      // NEW: Save the exact lesson URL the user is currently looking at!
      sessionStorage.setItem('datasensei_return_url', window.location.href);

      // Redirect to IDE
      window.location.href = "{{ route('ide.index') }}";
    }
  </script>

</body>
</html>