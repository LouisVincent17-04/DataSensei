<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>IDE — DataSensei</title>
<style>
    /* Colours, type and radius come from partials.design-system. */
    :root {
      --accent3: var(--ds-success); /* page-specific name kept for older markup */
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: var(--ds-font-sans);
      background: var(--bg);
      color: var(--text);
      height: 100vh;
      height: 100dvh;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    /* ── top bar ── */
    .topbar {
      min-height: 52px;
      background: var(--surface);
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 8px 16px;
      padding: 0 16px;
      flex-shrink: 0;
    }

    .topbar-left {
      display: flex;
      align-items: center;
      gap: 10px;
      min-width: 0;
      font-weight: 600;
      font-size: .9375rem;
    }
    .topbar-left svg { width: 20px; height: 20px; color: var(--ds-accent-text); }

    .topbar-right {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 8px;
    }

    /* Return button, hidden until the script finds a lesson to return to. */
    .btn-return {
      display: none;
      align-items: center;
      gap: 6px;
      min-height: 32px;
      padding: 0 12px;
      border: 1px solid var(--ds-accent-border);
      border-radius: var(--radius-sm);
      background: var(--ds-accent-soft);
      color: var(--ds-accent-text);
      font-size: .8125rem;
      font-weight: 500;
      text-decoration: none;
      transition: color .12s ease;
    }
    .btn-return:hover { color: var(--text); }

    .btn-run {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      min-height: 32px;
      padding: 0 12px;
      border: 1px solid var(--accent);
      border-radius: var(--radius-sm);
      background: var(--accent);
      color: #fff;
      font: 500 .8125rem/1.2 var(--ds-font-sans);
      cursor: pointer;
      transition: background .12s ease, border-color .12s ease;
    }
    .btn-run:hover { background: var(--accent-hover); border-color: var(--accent-hover); }

    /* ── workspace ── */
    .workspace {
      display: flex;
      flex: 1;
      min-height: 0;
      overflow: hidden;
    }

    .editor-pane {
      flex: 1;
      min-width: 0;
      display: flex;
      flex-direction: column;
      border-right: 1px solid var(--border);
      background: var(--bg);
    }

    .pane-header {
      min-height: 40px;
      padding: 0 16px;
      background: var(--surface);
      border-bottom: 1px solid var(--border);
      font-size: .8125rem;
      font-weight: 500;
      color: var(--ds-text-secondary);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .editor-pane .pane-header { font-family: var(--ds-font-mono); }

    .code-textarea {
      flex: 1;
      width: 100%;
      background: transparent;
      border: none;
      color: var(--text);
      font-family: var(--ds-font-mono);
      font-size: .875rem;
      line-height: 1.6;
      padding: 16px 20px;
      resize: none;
      outline: none;
    }

    .terminal-pane {
      width: 40%;
      min-width: 0;
      background: var(--surface3);
      display: flex;
      flex-direction: column;
    }

    .terminal-output {
      flex: 1;
      padding: 16px 20px;
      color: var(--muted);
      font-family: var(--ds-font-mono);
      font-size: .8125rem;
      line-height: 1.6;
      overflow-wrap: anywhere;
      overflow-y: auto;
    }

    .terminal-output.running {
      color: var(--ds-text-secondary);
    }

    /* Tablets and phones: the page scrolls and the panes stack. */
    @media (max-width: 900px) {
      body { height: auto; min-height: 100vh; min-height: 100dvh; overflow: visible; overflow-x: hidden; }
      .topbar { padding: 8px 12px; }
      .workspace { flex-direction: column; overflow: visible; }
      .editor-pane { flex: none; border-right: 0; border-bottom: 1px solid var(--border); }
      .code-textarea { flex: none; min-height: 55vh; min-height: 55dvh; }
      .terminal-pane { width: 100%; min-height: 240px; }
    }
  </style>
  @include('partials.ui-polish')
    @include('partials.page-head', ['pageTitle' => 'IDE'])
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
      <div class="pane-header">
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