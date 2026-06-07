<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DataSensei — Assignment Anti-Cheat Settings</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#0d1320;--surface:#111c2d;--surface2:#1a2638;--surface3:#0f1928;--border:#1e2f47;--border-hover:#2c4168;--accent:#3b82f6;--accent-hover:#2563eb;--green:#10b981;--red:#ef4444;--amber:#f59e0b;--text:#fafafa;--muted:#7f93b0;--dim:#3d5272;--radius:16px}*{box-sizing:border-box;margin:0;padding:0}html,body{min-height:100%;font-family:'Inter',system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;background:radial-gradient(circle at top left,rgba(59,130,246,.12),transparent 34rem),radial-gradient(circle at top right,rgba(239,68,68,.08),transparent 25rem),var(--bg);color:var(--text);-webkit-font-smoothing:antialiased}.shell{display:flex;min-height:100vh}.main{flex:1;min-width:0;padding:28px}.wrap{max-width:1480px;margin:0 auto}.top{display:flex;justify-content:space-between;align-items:flex-start;gap:20px;margin-bottom:22px}.kicker{color:var(--accent);font-size:.72rem;font-weight:900;text-transform:uppercase;letter-spacing:.09em;margin-bottom:8px}.title{font-size:clamp(1.8rem,3vw,2.7rem);font-weight:900;letter-spacing:-.06em;line-height:1.05}.subtitle{color:var(--muted);max-width:850px;line-height:1.65;margin-top:10px;font-size:.95rem}.grid{display:grid;gap:16px}.stats{grid-template-columns:repeat(3,minmax(0,1fr));margin-bottom:16px}.stat{border:1px solid var(--border);background:rgba(17,28,45,.92);border-radius:20px;padding:18px;box-shadow:0 18px 50px rgba(0,0,0,.18)}.stat .num{font-size:1.75rem;font-weight:900}.stat .label{color:var(--muted);font-size:.75rem;font-weight:900;text-transform:uppercase;letter-spacing:.08em;margin-top:4px}.card{border:1px solid var(--border);background:rgba(17,28,45,.92);border-radius:22px;box-shadow:0 18px 55px rgba(0,0,0,.22);overflow:hidden;margin-bottom:16px}.card-head{padding:18px 20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;gap:16px;align-items:center}.card-title{font-size:1rem;font-weight:900}.card-sub{font-size:.82rem;color:var(--muted);margin-top:4px;line-height:1.5}.card-body{padding:20px}.form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.field label{display:block;color:var(--dim);font-size:.68rem;font-weight:900;text-transform:uppercase;letter-spacing:.08em;margin-bottom:7px}.input,.select{width:100%;min-height:42px;border:1px solid var(--border);border-radius:12px;background:var(--surface3);color:var(--text);padding:10px 12px;font:inherit;font-size:.88rem;outline:none}.input:focus,.select:focus{border-color:rgba(59,130,246,.65);box-shadow:0 0 0 4px rgba(59,130,246,.10)}.toggles{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin-top:16px}.toggle{display:flex;align-items:flex-start;gap:10px;border:1px solid var(--border);background:rgba(255,255,255,.025);border-radius:14px;padding:13px;min-height:78px}.toggle input{margin-top:3px;width:18px;height:18px;accent-color:var(--accent);flex-shrink:0}.toggle strong{display:block;font-size:.84rem}.toggle span{display:block;color:var(--muted);font-size:.75rem;line-height:1.45;margin-top:3px}.btn{min-height:42px;border-radius:12px;border:1px solid transparent;padding:0 16px;font:inherit;font-size:.82rem;font-weight:900;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:8px;transition:.15s;white-space:nowrap}.btn:hover{transform:translateY(-1px)}.btn.primary{background:var(--accent);color:#fff;border-color:var(--accent);box-shadow:0 12px 26px rgba(59,130,246,.24)}.btn.secondary{background:rgba(255,255,255,.035);color:var(--text);border-color:var(--border)}.btn.danger{background:rgba(239,68,68,.10);color:#fecaca;border-color:rgba(239,68,68,.35)}.actions{display:flex;gap:8px;flex-wrap:wrap;margin-top:16px}.table{width:100%;border-collapse:collapse}.table th,.table td{text-align:left;padding:13px 16px;border-bottom:1px solid var(--border);vertical-align:top}.table th{font-size:.72rem;text-transform:uppercase;letter-spacing:.07em;color:var(--dim);background:rgba(255,255,255,.025)}.table td{font-size:.86rem;color:var(--muted)}.table strong{color:var(--text)}.pill{display:inline-flex;align-items:center;border:1px solid var(--border);background:rgba(255,255,255,.04);border-radius:999px;padding:5px 9px;font-size:.72rem;font-weight:900;color:var(--muted);white-space:nowrap}.pill.good{color:#a7f3d0;border-color:rgba(16,185,129,.35);background:rgba(16,185,129,.09)}.pill.warn{color:#fde68a;border-color:rgba(245,158,11,.35);background:rgba(245,158,11,.09)}.pill.danger{color:#fecaca;border-color:rgba(239,68,68,.35);background:rgba(239,68,68,.09)}.alert{border-radius:16px;padding:14px 16px;font-weight:800;line-height:1.5;margin-bottom:16px;border:1px solid transparent}.alert.success{background:rgba(16,185,129,.10);border-color:rgba(16,185,129,.30);color:#a7f3d0}.note{border:1px solid rgba(245,158,11,.35);background:rgba(245,158,11,.08);border-radius:16px;padding:14px 16px;color:#fde68a;line-height:1.55;margin-bottom:16px;font-size:.88rem}.empty{text-align:center;padding:34px;color:var(--muted)}@media(max-width:980px){.main{padding:18px}.top{flex-direction:column}.stats,.form-grid,.toggles{grid-template-columns:1fr}.table{display:block;overflow-x:auto}}
  </style>
</head>
<body>
  <div class="shell">
    @include('partials.instructor-sidebar')
    <main class="main">
      <div class="wrap">
        <div class="top">
          <div>
            <div class="kicker">Instructor Assignment Proctoring</div>
            <h1 class="title">Assignment Anti-Cheat Settings</h1>
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
