<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leaderboard — DataSensei</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#0d1320;--surface:#111c2d;--surface2:#1a2638;--border:#1e2f47;--accent:#3b82f6;--gold:#f59e0b;--text:#fafafa;--muted:#7f93b0;--radius:12px;--radius-sm:8px}*{box-sizing:border-box;margin:0;padding:0}body{font-family:Inter,sans-serif;background:var(--bg);color:var(--text)}.layout{display:flex;min-height:100vh}.main{flex:1;min-width:0;padding:32px;background:radial-gradient(circle at top,var(--surface2),var(--bg) 55%)}.header{display:flex;justify-content:space-between;align-items:flex-end;gap:20px;flex-wrap:wrap;margin-bottom:24px}.eyebrow{color:var(--accent);font-size:.78rem;font-weight:900;letter-spacing:.08em;text-transform:uppercase}.title{font-size:2rem;font-weight:900;margin-top:4px}.subtitle{color:var(--muted);margin-top:8px}.panel{background:rgba(17,28,45,.9);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden}.rank-card{background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.3);padding:14px 18px;border-radius:var(--radius);font-weight:800}table{width:100%;border-collapse:collapse}th,td{padding:15px 18px;text-align:left;border-bottom:1px solid var(--border)}th{font-size:.76rem;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;background:rgba(0,0,0,.12)}td{font-size:.92rem}.rank{font-weight:900;color:var(--gold)}.name{font-weight:800}.muted{color:var(--muted)}.pagination{padding:16px;color:var(--muted)}a{color:var(--accent);text-decoration:none}@media(max-width:700px){.layout{display:block}.main{padding:20px}th:nth-child(4),td:nth-child(4){display:none}}
  </style>
</head>
<body>
<div class="layout">
  @include('partials.sidebar')
  <main class="main">
    <div class="header">
      <div>
        <div class="eyebrow">Gamification</div>
        <h1 class="title">Leaderboard</h1>
        <p class="subtitle">Rankings are based on XP, streaks, and unlocked achievements.</p>
      </div>
      <div class="rank-card">Your estimated rank: #{{ number_format($currentRank) }}</div>
    </div>

    <section class="panel">
      <table>
        <thead><tr><th>Rank</th><th>Student</th><th>XP</th><th>Streak</th><th>Badges</th></tr></thead>
        <tbody>
          @forelse($users as $index => $student)
            <tr>
              <td class="rank">#{{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}</td>
              <td><div class="name">{{ $student->name }}</div><div class="muted">{{ $student->email }}</div></td>
              <td>{{ number_format($student->xp ?? 0) }}</td>
              <td>{{ number_format($student->streak ?? 0) }} days</td>
              <td>{{ number_format($student->achievements_count ?? 0) }}</td>
            </tr>
          @empty
            <tr><td colspan="5" class="muted">No student records yet.</td></tr>
          @endforelse
        </tbody>
      </table>
      <div class="pagination">{{ $users->links() }}</div>
    </section>
  </main>
</div>
</body>
</html>
