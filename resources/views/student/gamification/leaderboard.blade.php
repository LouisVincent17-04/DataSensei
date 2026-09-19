<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leaderboard — DataSensei</title>
<style>
    /* Student leaderboard. Colours, type and radius come from partials.design-system. */
    :root{--gold:var(--ds-warning)}
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:var(--ds-font-sans);background:var(--bg);color:var(--text)}
    .layout{display:flex;min-height:100vh}
    .main{flex:1;min-width:0;padding:28px 32px 48px;background:var(--bg)}

    /* page header */
    .header{display:flex;justify-content:space-between;align-items:flex-end;gap:16px;flex-wrap:wrap;margin-bottom:24px}
    .header > div:first-child{min-width:0;flex:1 1 320px}
    .subtitle{margin-top:4px;max-width:72ch;color:var(--muted);font-size:.875rem;line-height:1.55}
    .rank-card{flex-shrink:0;padding:10px 16px;border:1px solid var(--border);border-radius:var(--radius);background:var(--surface);
      color:var(--ds-text-secondary);font-size:.875rem;font-weight:500;font-variant-numeric:tabular-nums}

    /* ranking table */
    .panel{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden}
    .panel table{width:100%;border-collapse:collapse}
    .panel th,.panel td{text-align:left;border-bottom:1px solid var(--border);vertical-align:middle}
    .panel th{padding:10px 14px;background:var(--surface3);color:var(--muted);font-size:.75rem;font-weight:600;white-space:nowrap}
    .panel td{padding:12px 14px;color:var(--ds-text-secondary);font-size:.875rem;font-variant-numeric:tabular-nums}
    .panel tbody tr:last-child td{border-bottom:0}
    .panel tbody tr:hover td{background:rgba(255,255,255,.02)}
    .panel td.rank{width:72px;color:var(--text);font-weight:600}
    .panel td .name{color:var(--text);font-weight:600;overflow-wrap:anywhere}
    .panel td .muted{margin-top:2px;font-size:.8125rem;overflow-wrap:anywhere}
    .muted{color:var(--muted)}
    .pagination{padding:14px 20px;border-top:1px solid var(--border);color:var(--muted)}
    .pagination:empty{display:none}
    .main a{color:var(--ds-accent-text);text-decoration:none}

    @media(max-width:900px){.main{padding:24px 20px 40px}}
    @media(max-width:640px){
      .main{padding:20px 16px 32px}
      .rank-card{width:100%}
      /* four columns fit a phone once the streak column is hidden */
      main .panel table{min-width:0}
      .panel th:nth-child(4),.panel td:nth-child(4){display:none}
      .panel th,.panel td{padding-left:12px;padding-right:12px}
      .panel td.rank{width:48px}
      .pagination{padding:14px 16px}
    }
  </style>
  @include('partials.admin-inspired-page-style')
    @include('partials.page-head', ['pageTitle' => 'Leaderboard', 'pageDescription' => 'Achievements, streaks, and the class leaderboard.'])
</head>
<body class="ds-admin-inspired">
<div class="layout">
  @include('partials.sidebar')
  <main class="main">
    <div class="header">
      <div>
        <h1 class="title ds-page-title">Leaderboard</h1>
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
