<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Admin Workspace') — DataSensei</title>
  @include('partials.brand-head')
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#0d1320;--surface:#111c2d;--surface2:#17243a;--surface3:#1d2c45;--border:#243550;--text:#f8fafc;--muted:#94a3b8;--dim:#64748b;--accent:#3b82f6;--accent2:#8b5cf6;--green:#22c55e;--orange:#f97316;--red:#ef4444;--radius:18px;--radius-sm:12px;--shadow:0 20px 55px rgba(0,0,0,.28)}*{box-sizing:border-box}body{margin:0;min-height:100vh;font-family:Inter,Arial,sans-serif;background:radial-gradient(circle at 15% 0,rgba(59,130,246,.14),transparent 32%),radial-gradient(circle at 85% 5%,rgba(139,92,246,.12),transparent 34%),var(--bg);color:var(--text)}.admin-shell{display:flex;min-height:100vh}.main{flex:1;min-width:0;padding:30px}.topbar{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;margin-bottom:24px}.eyebrow{font-size:.78rem;color:var(--accent);text-transform:uppercase;letter-spacing:.1em;font-weight:900}.page-title{margin:6px 0 4px;font-size:clamp(1.75rem,3vw,2.45rem);letter-spacing:-.045em;font-weight:950}.page-subtitle{margin:0;color:var(--muted);line-height:1.6}.panel{background:linear-gradient(180deg,rgba(17,28,45,.96),rgba(12,20,34,.96));border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);padding:22px;margin-bottom:20px}.panel-title{font-size:1.05rem;font-weight:900;margin:0 0 14px}.grid{display:grid;gap:16px}.grid.cards{grid-template-columns:repeat(5,minmax(0,1fr))}.stat{background:linear-gradient(145deg,rgba(255,255,255,.035),rgba(255,255,255,.015));border:1px solid var(--border);border-radius:16px;padding:18px}.stat .label{color:var(--muted);font-size:.82rem;font-weight:800}.stat .value{font-size:1.85rem;font-weight:950;margin-top:8px}.stat .sub{font-size:.78rem;color:var(--dim);margin-top:6px}.tone-blue{border-color:rgba(59,130,246,.35)}.tone-green{border-color:rgba(34,197,94,.35)}.tone-purple{border-color:rgba(139,92,246,.35)}.tone-orange{border-color:rgba(249,115,22,.35)}.tone-red{border-color:rgba(239,68,68,.35)}.toolbar{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:16px}.input,.select,.textarea{width:100%;border:1px solid var(--border);background:#0b1322;color:var(--text);border-radius:12px;padding:10px 12px;font:inherit}.textarea{min-height:84px;resize:vertical}.input.small,.select.small{width:auto;min-width:155px}.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:0;border-radius:12px;padding:10px 14px;font:inherit;font-weight:850;cursor:pointer;text-decoration:none;background:var(--accent);color:#fff}.btn.secondary{background:var(--surface3);border:1px solid var(--border);color:var(--text)}.btn.danger{background:rgba(239,68,68,.14);border:1px solid rgba(239,68,68,.32);color:#fca5a5}.btn.green{background:rgba(34,197,94,.14);border:1px solid rgba(34,197,94,.32);color:#86efac}.btn.small{padding:7px 10px;border-radius:9px;font-size:.8rem}.table-wrap{overflow:auto;border:1px solid var(--border);border-radius:16px}table{width:100%;border-collapse:collapse;min-width:840px}th,td{padding:13px 14px;border-bottom:1px solid var(--border);text-align:left;vertical-align:top}th{font-size:.74rem;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;background:rgba(255,255,255,.025)}td{font-size:.88rem;color:#e2e8f0}tr:last-child td{border-bottom:0}.badge{display:inline-flex;align-items:center;gap:6px;border:1px solid var(--border);border-radius:999px;padding:4px 9px;color:var(--muted);font-size:.72rem;font-weight:850}.badge.active{color:#86efac;border-color:rgba(34,197,94,.32);background:rgba(34,197,94,.08)}.badge.disabled{color:#fca5a5;border-color:rgba(239,68,68,.32);background:rgba(239,68,68,.08)}.badge.info{color:#bfdbfe;border-color:rgba(59,130,246,.32);background:rgba(59,130,246,.08)}.split{display:grid;grid-template-columns:1fr 1fr;gap:16px}.form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.form-grid.three{grid-template-columns:repeat(3,minmax(0,1fr))}.field label{display:block;color:var(--muted);font-size:.78rem;font-weight:850;margin-bottom:6px}.muted{color:var(--muted)}.dim{color:var(--dim)}.notice{border:1px solid rgba(59,130,246,.3);background:rgba(59,130,246,.08);color:#bfdbfe;border-radius:14px;padding:12px 14px;margin-bottom:16px}.notice.error{border-color:rgba(239,68,68,.35);background:rgba(239,68,68,.08);color:#fecaca}.pagination{margin-top:16px}.action-row{display:flex;gap:8px;flex-wrap:wrap}.chart-bars{display:flex;align-items:end;gap:8px;height:140px;padding:12px;border:1px solid var(--border);border-radius:16px;background:rgba(0,0,0,.12)}.bar{flex:1;min-width:18px;border-radius:9px 9px 2px 2px;background:linear-gradient(180deg,var(--accent),rgba(59,130,246,.35));position:relative}.bar span{position:absolute;bottom:-24px;left:50%;transform:translateX(-50%);font-size:.68rem;color:var(--dim);white-space:nowrap}.health{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}.health-card{border:1px solid var(--border);border-radius:14px;padding:14px;background:rgba(255,255,255,.025)}.health-card.ok{border-color:rgba(34,197,94,.32)}.health-card.warning{border-color:rgba(249,115,22,.35)}.health-card.danger{border-color:rgba(239,68,68,.35)}.health-card .label{color:var(--muted);font-size:.75rem;font-weight:900}.health-card .value{font-weight:950;margin-top:5px}.health-card .note{color:var(--dim);font-size:.74rem;margin-top:5px}@media(max-width:1200px){.grid.cards{grid-template-columns:repeat(2,minmax(0,1fr))}.health{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:760px){.admin-shell{display:block}.main{padding:18px}.topbar{display:block}.split,.form-grid,.form-grid.three,.health{grid-template-columns:1fr}.grid.cards{grid-template-columns:1fr}}
  </style>
  @stack('head')
</head>
<body>
<div class="admin-shell">
  @include('partials.admin-sidebar')
  <main class="main">
    <div class="topbar">
      <div>
        <div class="eyebrow">@yield('eyebrow', 'Admin Workspace')</div>
        <h1 class="page-title">@yield('page_title', 'Dashboard')</h1>
        <p class="page-subtitle">@yield('page_subtitle', 'Manage day-to-day DataSensei platform operations.')</p>
      </div>
      <div class="action-row">
        <a class="btn secondary" href="{{ route('profile') }}">Profile</a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="btn danger" type="submit">Sign Out</button>
        </form>
      </div>
    </div>

    @if(session('success'))
      <div class="notice">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="notice error">{{ session('error') }}</div>
    @endif

    @if($errors->any())
      <div class="notice error">
        <strong>Fix the following:</strong>
        <ul>
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @yield('content')
  </main>
</div>
@stack('scripts')
</body>
</html>
