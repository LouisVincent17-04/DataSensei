<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Assignment Anti-Cheat Settings — DataSensei</title>
<style>
    /* Assignment anti-cheat settings. Colours, type and radius come from partials.design-system. */
    *{box-sizing:border-box;margin:0;padding:0}
    body{min-height:100vh;background:var(--bg);color:var(--text);font-family:var(--ds-font-sans)}
    .shell{display:flex;min-height:100vh}
    .main{flex:1;min-width:0;padding:28px 32px 48px}
    .wrap{max-width:1480px;margin:0 auto}

    /* page header */
    .top{display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px;margin-bottom:24px}
    .top > div:first-child{min-width:0;flex:1 1 320px}
    .subtitle{max-width:72ch;margin-top:4px;color:var(--muted);font-size:.875rem;line-height:1.55}

    /* messages */
    .alert{margin-bottom:16px;padding:12px 16px;border:1px solid var(--ds-accent-border);border-radius:var(--radius-sm);
      background:var(--ds-accent-soft);color:#dbeafe;font-size:.875rem;line-height:1.5}
    .alert.success{border-color:var(--ds-success-border);background:var(--ds-success-soft);color:#d1fae5}
    .note{margin-bottom:24px;padding:12px 16px;border:1px solid var(--ds-warning-border);border-radius:var(--radius-sm);
      background:var(--ds-warning-soft);color:#fef3c7;font-size:.875rem;line-height:1.55}

    /* summary figures: label above value */
    .grid{display:grid;gap:12px}
    .stats{grid-template-columns:repeat(3,minmax(0,1fr));margin-bottom:24px}
    .stat{display:flex;flex-direction:column-reverse;justify-content:flex-end;gap:4px;padding:16px 18px;
      border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}
    .stat .num{font-size:1.5rem;font-weight:700;line-height:1.2;letter-spacing:-.02em;font-variant-numeric:tabular-nums}
    .stat .label{color:var(--muted);font-size:.8125rem;font-weight:500}

    /* cards */
    .card{margin-bottom:20px;overflow:hidden;border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}
    .card-head{display:flex;justify-content:space-between;align-items:center;gap:16px;padding:14px 20px;border-bottom:1px solid var(--border)}
    .card-head > div{min-width:0}
    .card-title{font-size:.9375rem;font-weight:600;line-height:1.35}
    .card-sub{max-width:80ch;margin-top:2px;color:var(--muted);font-size:.8125rem;line-height:1.5}
    .card-body{padding:20px}

    /* form */
    .form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
    .field label{display:block;margin-bottom:6px;color:var(--ds-text-secondary);font-size:.8125rem;font-weight:500}
    .input,.select{width:100%;min-height:38px;padding:8px 12px;border:1px solid var(--ds-input-border);border-radius:var(--radius-sm);
      background:var(--surface3);color:var(--text);font:400 .875rem/1.4 var(--ds-font-sans);outline:none;
      transition:border-color .12s ease,box-shadow .12s ease}
    .input:focus,.select:focus{border-color:var(--accent);box-shadow:var(--ds-focus-ring)}

    /* rule switches: checkbox options, the checked ones tinted */
    .toggles{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;margin-top:20px}
    .toggle{display:flex;align-items:flex-start;gap:10px;padding:12px;border:1px solid var(--border);border-radius:var(--radius-sm);
      cursor:pointer;transition:background .12s ease,border-color .12s ease}
    .toggle:hover{border-color:var(--ds-border-strong)}
    .toggle:has(input:checked){border-color:var(--ds-accent-border);background:var(--ds-accent-soft)}
    .toggle input{width:16px;height:16px;flex-shrink:0;margin-top:2px}
    .toggle > div{min-width:0}
    .toggle strong{display:block;color:var(--text);font-size:.875rem;font-weight:500;line-height:1.4}
    .toggle span{display:block;margin-top:2px;color:var(--muted);font-size:.75rem;line-height:1.45}

    /* buttons */
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:38px;padding:0 16px;
      border:1px solid var(--ds-border-strong);border-radius:var(--radius-sm);background:var(--surface2);color:var(--text);
      font:500 .875rem/1.2 var(--ds-font-sans);text-decoration:none;white-space:nowrap;cursor:pointer;
      transition:background .12s ease,border-color .12s ease,color .12s ease}
    .btn:hover{background:var(--ds-surface-hover)}
    .btn.primary{border-color:var(--accent);background:var(--accent);color:#fff}
    .btn.primary:hover{border-color:var(--accent-hover);background:var(--accent-hover)}
    .btn.danger{border-color:var(--ds-danger-border);background:transparent;color:var(--ds-danger-text)}
    .btn.danger:hover{background:var(--ds-danger-soft)}
    .actions{display:flex;gap:8px;flex-wrap:wrap;margin-top:20px;padding-top:16px;border-top:1px solid var(--border)}

    /* tables */
    .table{width:100%;border-collapse:collapse}
    .card .table{min-width:720px}
    .table th{padding:10px 14px;border-bottom:1px solid var(--border);background:var(--surface3);color:var(--muted);
      font-size:.75rem;font-weight:600;text-align:left;white-space:nowrap}
    .table td{padding:12px 14px;border-bottom:1px solid var(--border);color:var(--ds-text-secondary);font-size:.875rem;
      line-height:1.45;vertical-align:middle}
    .table tbody tr:last-child td{border-bottom:0}
    .table tbody tr:hover td{background:rgba(255,255,255,.02)}
    .table strong{color:var(--text);font-weight:600}
    .table td > span:not(.pill){color:var(--muted);font-size:.8125rem;overflow-wrap:anywhere}
    .table .btn{min-height:32px;padding:0 12px;font-size:.8125rem}

    .pill{display:inline-flex;align-items:center;margin:2px 4px 2px 0;padding:2px 8px;border:1px solid var(--ds-border-strong);border-radius:var(--radius-xs);
      background:var(--surface2);color:var(--ds-text-secondary);font-size:.75rem;font-weight:600;line-height:1.4;white-space:nowrap}
    .pill.good{border-color:var(--ds-success-border);background:var(--ds-success-soft);color:var(--ds-success-text)}
    .pill.warn{border-color:var(--ds-warning-border);background:var(--ds-warning-soft);color:var(--ds-warning-text)}
    .pill.danger{border-color:var(--ds-danger-border);background:var(--ds-danger-soft);color:var(--ds-danger-text)}
    .empty{padding:32px 20px;color:var(--muted);font-size:.875rem;line-height:1.5;text-align:center}

    @media(max-width:1100px){.toggles{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media(max-width:900px){.main{padding:24px 20px 40px}}
    @media(max-width:760px){
      .form-grid{grid-template-columns:minmax(0,1fr)}
    }
    @media(max-width:640px){
      .main{padding:20px 16px 32px}
      .card-head{padding:12px 16px}
      .card-body{padding:16px}
      .toggles{grid-template-columns:minmax(0,1fr)}
      .actions .btn{width:100%}
    }
    @media(max-width:560px){.stats{grid-template-columns:minmax(0,1fr)}}
    @media(prefers-reduced-motion:reduce){.btn,.input,.select,.toggle{transition:none}}
  </style>
    @include('partials.page-head', ['pageTitle' => 'Assignment Anti-Cheat Settings', 'pageDescription' => 'Anti-cheat settings and the events recorded during assessments.'])
</head>
<body>
  <div class="shell">
    @include('partials.instructor-sidebar')
    <main class="main">
      <div class="wrap">
        <div class="top">
          <div>
            <h1 class="title ds-page-title">Assignment Anti-Cheat Settings</h1>
            <p class="subtitle">Configure anti-cheat safeguards for assignments you give to your classes. These settings do not affect public MCQ Challenges or public Coding Challenges.</p>
          </div>
        </div>

        @if(session('success')) <div class="alert success">{{ session('success') }}</div> @endif

        <div class="note">
          These controls apply only when a student takes an instructor-assigned assignment. Public practice challenges and public coding challenges remain unaffected. Browser-based proctoring can reduce cheating, but it cannot perfectly prove intent.
        </div>

        <div class="grid stats">
          <div class="stat"><div class="num">{{ $stats['settings'] }}</div><div class="label">Saved Configurations</div></div>
          <div class="stat"><div class="num">{{ $stats['events_today'] }}</div><div class="label">Events Today</div></div>
          <div class="stat"><div class="num">{{ $stats['critical_today'] }}</div><div class="label">Critical Today</div></div>
        </div>

        <section class="card">
          <div class="card-head">
            <div>
              <div class="card-title">Create or Update Configuration</div>
              <div class="card-sub">Use “All classes” as your default assignment anti-cheat policy, or choose a specific class for a class-level override.</div>
            </div>
          </div>
          <div class="card-body">
            <form method="POST" action="{{ route('instructor.anti-cheat.store') }}">
              @csrf
              <div class="form-grid">
                <div class="field">
                  <label>Class</label>
                  <select class="select" name="class_id">
                    <option value="">All classes / instructor default</option>
                    @foreach($classes as $class)
                      <option value="{{ $class->id }}">{{ $class->name }} {{ $class->section ? '— '.$class->section : '' }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="field">
                  <label>Allowed Tab Switches</label>
                  <input class="input" type="number" min="0" max="20" name="max_tab_switches" value="2">
                </div>
              </div>

              <div class="toggles">
                <label class="toggle"><input type="checkbox" name="enabled" value="1" checked><div><strong>Enable anti-cheat</strong><span>Apply monitoring to matching attempts.</span></div></label>
                <label class="toggle"><input type="checkbox" name="allow_tab_switch" value="1"><div><strong>Allow tab switching</strong><span>If off, switching tabs/focus is counted as a violation.</span></div></label>
                <label class="toggle"><input type="checkbox" name="block_on_tab_limit" value="1" checked><div><strong>Lock after tab limit</strong><span>Blocks attempt after the allowed count is exceeded.</span></div></label>
                <label class="toggle"><input type="checkbox" name="require_fullscreen" value="1"><div><strong>Require fullscreen</strong><span>Student must enter fullscreen before continuing.</span></div></label>
                <label class="toggle"><input type="checkbox" name="detect_dual_monitor" value="1" checked><div><strong>Detect dual monitor</strong><span>Logs multiple screens when browser support permits.</span></div></label>
                <label class="toggle"><input type="checkbox" name="block_dual_monitor" value="1"><div><strong>Block dual monitor</strong><span>Locks attempt when multiple screens are detected.</span></div></label>
                <label class="toggle"><input type="checkbox" name="allow_copy" value="1" checked><div><strong>Allow copying</strong><span>Permit copying text/code inside the attempt.</span></div></label>
                <label class="toggle"><input type="checkbox" name="allow_paste" value="1"><div><strong>Allow paste</strong><span>If off, all paste actions are blocked.</span></div></label>
                <label class="toggle"><input type="checkbox" name="block_external_paste" value="1" checked><div><strong>Block external paste</strong><span>Allows internal copy/paste only when paste is enabled.</span></div></label>
                <label class="toggle"><input type="checkbox" name="allow_right_click" value="1"><div><strong>Allow right click</strong><span>If off, context menu is blocked and logged.</span></div></label>
                <label class="toggle"><input type="checkbox" name="allow_devtools_shortcuts" value="1"><div><strong>Allow developer shortcuts</strong><span>If off, F12/Ctrl+Shift+I/Ctrl+U shortcuts are blocked.</span></div></label>
                <label class="toggle"><input type="checkbox" name="show_warnings" value="1" checked><div><strong>Show student warnings</strong><span>Display warning toasts for violations.</span></div></label>
                <label class="toggle"><input type="checkbox" name="auto_submit_mcq_on_violation" value="1"><div><strong>Auto-submit MCQ when locked</strong><span>For severe violations, submits current MCQ answers.</span></div></label>
                <label class="toggle"><input type="checkbox" name="lock_screen_on_violation" value="1" checked><div><strong>Lock screen on critical violation</strong><span>Disables inputs after a critical event.</span></div></label>
              </div>

              <div class="actions"><button class="btn primary" type="submit">Save Assignment Anti-Cheat</button></div>
            </form>
          </div>
        </section>

        <section class="card">
          <div class="card-head"><div><div class="card-title">Saved Configurations</div><div class="card-sub">Class-specific settings override your all-classes assignment default.</div></div></div>
          @if($settings->count())
            <table class="table">
              <thead><tr><th>Scope</th><th>Rules</th><th>Actions</th></tr></thead>
              <tbody>
                @foreach($settings as $setting)
                  <tr>
                    <td><strong>{{ $setting->classRoom?->name ?? 'All Classes' }}</strong><br><span>{{ $setting->classRoom?->section ?? 'Instructor default' }}</span></td>
                    <td>
                      <span class="pill {{ $setting->enabled ? 'good' : 'danger' }}">{{ $setting->enabled ? 'Enabled' : 'Disabled' }}</span>
                      <span class="pill {{ $setting->allow_tab_switch ? 'warn' : 'danger' }}">Tabs: {{ $setting->allow_tab_switch ? 'Allowed' : 'Blocked after '.$setting->max_tab_switches }}</span>
                      <span class="pill {{ $setting->allow_paste ? 'warn' : 'danger' }}">Paste: {{ $setting->allow_paste ? 'Allowed' : 'Blocked' }}</span>
                      <span class="pill {{ $setting->require_fullscreen ? 'danger' : '' }}">Fullscreen: {{ $setting->require_fullscreen ? 'Required' : 'Optional' }}</span>
                      <span class="pill {{ $setting->block_dual_monitor ? 'danger' : 'warn' }}">Dual monitor: {{ $setting->block_dual_monitor ? 'Blocked' : ($setting->detect_dual_monitor ? 'Logged' : 'Ignored') }}</span>
                    </td>
                    <td>
                      <form method="POST" action="{{ route('instructor.anti-cheat.destroy', $setting) }}" onsubmit="return confirm('Remove this anti-cheat configuration?');">
                        @csrf @method('DELETE')
                        <button class="btn danger" type="submit">Delete</button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @else
            <div class="empty">No assignment anti-cheat configuration yet. Assignments will run without anti-cheat until you save a configuration.</div>
          @endif
        </section>

        <section class="card">
          <div class="card-head"><div><div class="card-title">Recent Anti-Cheat Events</div><div class="card-sub">Review assignment anti-cheat warnings and critical events from students in your classes.</div></div></div>
          @if($recentEvents->count())
            <table class="table">
              <thead><tr><th>Student</th><th>Class</th><th>Assessment</th><th>Event</th><th>Severity</th><th>Time</th></tr></thead>
              <tbody>
                @foreach($recentEvents as $event)
                  <tr>
                    <td><strong>{{ $event->user?->name ?? 'Student' }}</strong><br><span>{{ $event->user?->email }}</span></td>
                    <td>{{ $event->classRoom?->name ?? '—' }}</td>
                    <td>Assignment<br><span>{{ $event->classAssignment?->title ?? '—' }}</span></td>
                    <td><strong>{{ str_replace('_', ' ', ucwords($event->event_type, '_')) }}</strong></td>
                    <td><span class="pill {{ $event->severity === 'critical' ? 'danger' : ($event->severity === 'warning' ? 'warn' : '') }}">{{ ucfirst($event->severity) }}</span></td>
                    <td>{{ $event->created_at?->format('M d, Y h:i A') }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @else
            <div class="empty">No anti-cheat events recorded yet.</div>
          @endif
        </section>
      </div>
    </main>
  </div>
</body>
</html>
