<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>DataSensei — IDE</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
  
  <style>
    :root {
      /* DataSensei Core Palette */
      --bg:           #0d1320;
      --surface:      #111c2d;
      --surface2:     #1a2638;
      --border:       #1e2f47;
      --border-hover: #2c4168;
      --accent:       #3b82f6; 
      --accent-hover: #2563eb;
      --accent3:      #10b981; /* Green for Run button */
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
      height: 60px;
      background: var(--surface);
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 24px;
      flex-shrink: 0;
    }
    
    .topbar-left {
      display: flex;
      align-items: center;
      gap: 16px;
      font-weight: 600;
      font-size: 1.125rem;
    }
    .topbar-left svg { color: var(--accent); }

    .topbar-right {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    /* The magical Return button (Hidden by default) */
    .btn-return {
      display: none; /* Will be unhidden by JS if needed */
      align-items: center;
      gap: 8px;
      background: rgba(59, 130, 246, 0.1);
      color: var(--accent);
      border: 1px solid rgba(59, 130, 246, 0.3);
      padding: 8px 16px;
      border-radius: 6px;
      font-size: 0.875rem;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.2s;
    }
    .btn-return:hover {
      background: rgba(59, 130, 246, 0.2);
    }

    .btn-run {
      display: flex;
      align-items: center;
      gap: 8px;
      background: var(--accent3);
      color: #fff;
      border: none;
      padding: 8px 20px;
      border-radius: 6px;
      font-size: 0.875rem;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.15s;
      font-family: 'Inter', sans-serif;
    }
    .btn-run:hover { background: #059669; }

    /* ── IDE WORKSPACE ── */
    .workspace {
      display: flex;
      flex: 1;
      overflow: hidden;
    }

    /* Code Editor Panel */
    .editor-pane {
      flex: 1;
      display: flex;
      flex-direction: column;
      border-right: 1px solid var(--border);
      background: var(--bg);
    }
    
    .pane-header {
      padding: 12px 24px;
      background: var(--surface2);
      border-bottom: 1px solid var(--border);
      font-size: 0.75rem;
      font-weight: 600;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: 0.05em;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .code-textarea {
      flex: 1;
      width: 100%;
      background: transparent;
      border: none;
      color: #e5e7eb;
      font-family: 'JetBrains Mono', monospace;
      font-size: 0.95rem;
      line-height: 1.6;
      padding: 24px;
      resize: none;
      outline: none;
    }

    /* Terminal Output Panel */
    .terminal-pane {
      width: 40%;
      background: #090e17; /* Slightly darker for terminal feel */
      display: flex;
      flex-direction: column;
    }

    .terminal-output {
      flex: 1;
      padding: 24px;
      color: #9ca3af;
      font-family: 'JetBrains Mono', monospace;
      font-size: 0.85rem;
      line-height: 1.5;
      overflow-y: auto;
    }

    .terminal-output.running {
      color: var(--text);
    }
  </style>
</head>
<body>

  <header class="topbar">
    <div class="topbar-left">
      <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
      </svg>
      DataSensei Compiler
    </div>
    
    <div class="topbar-right">
      <a href="#" id="returnToLessonBtn" class="btn-return">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Return to Lesson
      </a>

      <button id="runCodeBtn" class="btn-run">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
          <path d="M8 5v14l11-7z"/>
        </svg>
        Run Code
      </button>
    </div>
  </header>

  <div class="workspace">
    
    <div class="editor-pane">
      <div class="pane-header">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        main.py
      </div>
      
      <textarea id="codeEditor" class="code-textarea" spellcheck="false"># Write your Python code here...
print("Hello, DataSensei!")</textarea>
    </div>

    <div class="terminal-pane">
      <div class="pane-header" style="background: #0d1320;">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        Terminal Output
      </div>
      <div id="terminalOutput" class="terminal-output">
        Ready. Waiting for execution...
      </div>
    </div>

  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        const editor = document.getElementById('codeEditor');
        const returnBtn = document.getElementById('returnToLessonBtn');

        // 1. Check for Pending Code sent from the Learning Room
        const pendingCode = sessionStorage.getItem('datasensei_pending_code');
        if (pendingCode) {
            // Paste the code into the editor area
            editor.value = pendingCode;
            
            // Wipe the memory so it doesn't paste again if the user refreshes
            sessionStorage.removeItem('datasensei_pending_code');
        }

        // 2. Check for a Return URL
        const returnUrl = sessionStorage.getItem('datasensei_return_url');
        if (returnUrl) {
            // Unhide the button and set the link
            returnBtn.style.display = 'inline-flex'; 
            returnBtn.href = returnUrl;              
            
            // Clear the memory when they click it, so the IDE goes back to normal next time
            returnBtn.addEventListener('click', function() {
                sessionStorage.removeItem('datasensei_return_url');
            });
        }

        // 3. Mock Run Button Logic (Visual feedback)
        document.getElementById('runCodeBtn').addEventListener('click', function() {
            const terminal = document.getElementById('terminalOutput');
            terminal.innerHTML = '<span style="color: var(--accent);">Executing script...</span>';
            terminal.classList.remove('running');
            
            setTimeout(() => {
                terminal.innerHTML = 'Hello, DataSensei!\n\n<span style="color: var(--dim);">Process finished with exit code 0</span>';
                terminal.classList.add('running');
            }, 600);
        });
    });
  </script>

</body>
</html>