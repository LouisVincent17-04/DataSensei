<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Change Password — DataSensei</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
    :root{--bg:#0d1320;--surface:#111c2d;--surface2:#1a2638;--border:#1e2f47;--text:#fafafa;--muted:#7f93b0;--accent:#3b82f6;--good:#10b981;--bad:#ef4444;--radius:14px;--radius-sm:8px}*{box-sizing:border-box}body{margin:0;font-family:Inter,Arial,sans-serif;background:var(--bg);color:var(--text)}.wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}.card{width:min(540px,100%);background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:26px;box-shadow:0 20px 60px rgba(0,0,0,.25)}h1{margin:0 0 8px;font-size:1.55rem}.muted{color:var(--muted);line-height:1.6}.alert{padding:12px 14px;border-radius:var(--radius-sm);margin:16px 0;border:1px solid rgba(16,185,129,.25);background:rgba(16,185,129,.08);color:var(--good)}.alert.error{border-color:rgba(239,68,68,.25);background:rgba(239,68,68,.08);color:var(--bad)}.field{margin-top:14px}label{display:block;color:var(--muted);font-size:.85rem;margin-bottom:7px}input{width:100%;background:var(--bg);border:1px solid var(--border);color:var(--text);padding:11px 12px;border-radius:var(--radius-sm)}.actions{display:flex;justify-content:flex-end;gap:10px;margin-top:18px}.btn{border:0;border-radius:var(--radius-sm);padding:10px 14px;font-weight:800;cursor:pointer;text-decoration:none}.btn.primary{background:var(--accent);color:white}.btn.secondary{background:var(--surface2);border:1px solid var(--border);color:var(--text)}
  </style>
  @include('partials.ui-polish')
</head>
<body>
<div class="wrap">
  <div class="card">
    <h1>Change Password</h1>
    <p class="muted">Update your account password securely.</p>

    @if(session('success'))<div class="alert">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert error">{{ $errors->first() }}</div>@endif

    <form method="POST" action="{{ route('profile.password.update') }}">
      @csrf
      @method('PATCH')
      <div class="field">
        <label>Current Password</label>
        <input type="password" name="current_password" required autocomplete="current-password">
      </div>
      <div class="field">
        <label>New Password</label>
        <input type="password" name="password" required autocomplete="new-password">
      </div>
      <div class="field">
        <label>Confirm New Password</label>
        <input type="password" name="password_confirmation" required autocomplete="new-password">
      </div>
      <div class="actions">
        <a class="btn secondary" href="{{ url()->previous() }}">Back</a>
        <button class="btn primary" type="submit">Update Password</button>
      </div>
    </form>
  </div>
</div>
</body>
</html>
