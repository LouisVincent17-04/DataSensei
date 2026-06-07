<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DataSensei — {{ $category->name }} Coding Path</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    :root {
      --bg:           #0a0f18;
      --surface:      #111c2d;
      --surface2:     #1a2638;
      --border:       #1e2f47;
      --border-hover: #2c4168;
      --accent:       #3b82f6;
      --accent-hover: #2563eb;
      --accent3:      #10b981;
      --warn:         #f59e0b;
      --amber:        #f59e0b;
      --red:          #ef4444;
      --text:         #fafafa;
      --muted:        #8b9ebb;
      --dim:          #4a5f82;
      --radius:       16px;
      --radius-sm:    8px;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; -webkit-font-smoothing: antialiased; }
    .page-layout-wrapper { display: flex; min-height: 100vh; }

    /* ── MAIN ── */
    .challenge-map-main { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }

    /* Topbar */
    .challenge-map-topbar { height: 70px; background: rgba(17,28,45,0.8); backdrop-filter: blur(16px); border-bottom: 1px solid var(--border); display: flex; align-items: center; padding: 0 32px; gap: 16px; flex-shrink: 0; z-index: 50; }
    .challenge-map-topbar-title { font-weight: 700; font-size: 1.25rem; flex: 1; letter-spacing: -0.01em; }
    .challenge-map-topbar-title small { color: var(--muted); font-weight: 500; font-size: 0.875rem; margin-left: 8px; background: var(--surface2); padding: 4px 8px; border-radius: 6px; }
    .challenge-map-topbar-btn { padding: 8px 16px; background: transparent; border: 1px solid var(--border); border-radius: var(--radius-sm); color: var(--text); font-size: 0.875rem; font-weight: 500; text-decoration: none; transition: all 0.2s; display: flex; align-items: center; gap: 8px; }
    .challenge-map-topbar-btn:hover { background: var(--surface2); border-color: var(--accent); }

    /* Alert */
    .challenge-map-alert { background: rgba(16,185,129,0.1); border-bottom: 1px solid rgba(16,185,129,0.3); color: var(--accent3); padding: 12px 32px; font-weight: 500; font-size: 0.9rem; display: flex; align-items: center; gap: 10px; flex-shrink: 0; }

    /* Map Canvas */
    .challenge-map-container {
      flex: 1; position: relative; overflow: hidden;
      background-color: var(--bg);
      background-image:
        radial-gradient(ellipse at 50% 0%, var(--surface2) 0%, transparent 70%),
        linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
      background-size: 100% 100%, 40px 40px, 40px 40px;
    }
    .challenge-map-viewport { width: 100%; height: 100%; overflow: auto; cursor: grab; scroll-behavior: smooth; }
    .challenge-map-viewport:active { cursor: grabbing; }
    .challenge-map-content { position: relative; }

    /* SVG paths */
    .challenge-map-svg-paths { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1; }
    .challenge-map-path-line { fill: none; stroke: var(--border); stroke-width: 4; stroke-dasharray: 12 8; opacity: 0.4; transition: all 0.5s; }
    .challenge-map-path-line.passed   { stroke: var(--accent3); stroke-dasharray: none; opacity: 0.8; filter: drop-shadow(0 0 6px rgba(16,185,129,0.4)); }
    .challenge-map-path-line.inprog  { stroke: var(--amber); stroke-dasharray: 8 8; opacity: 0.7; }
    @keyframes flow { from { stroke-dashoffset: 24; } to { stroke-dashoffset: 0; } }
    .challenge-map-path-line.current  { stroke: var(--accent); stroke-dasharray: 12 12; opacity: 1; animation: flow 1s linear infinite; filter: drop-shadow(0 0 8px rgba(59,130,246,0.6)); }

    /* Nodes */
    .challenge-map-node { position: absolute; z-index: 10; display: flex; flex-direction: column; align-items: center; gap: 12px; transform: translate(-50%, -50%); transition: transform 0.3s cubic-bezier(0.4,0,0.2,1); }
    .challenge-map-node:hover { z-index: 20; transform: translate(-50%, -52%) scale(1.02); }

    .challenge-map-node-icon-wrap { position: relative; }
    .challenge-map-node-icon { width: 76px; height: 76px; border-radius: 50%; background: var(--surface); border: 4px solid var(--border); display: flex; align-items: center; justify-content: center; color: var(--dim); box-shadow: 0 8px 24px rgba(0,0,0,0.5); transition: all 0.4s; z-index: 2; position: relative; }

    .challenge-map-node.state-completed .challenge-map-node-icon { background: rgba(16,185,129,0.15); border-color: var(--accent3); color: var(--accent3); box-shadow: 0 0 30px rgba(16,185,129,0.25); }
    .challenge-map-node.state-inprogress .challenge-map-node-icon { background: rgba(245,158,11,0.12); border-color: var(--amber); color: var(--amber); box-shadow: 0 0 30px rgba(245,158,11,0.25); animation: pulse-amber 2.5s infinite; }
    .challenge-map-node.state-active    .challenge-map-node-icon { background: rgba(59,130,246,0.15); border-color: var(--accent); color: var(--accent); box-shadow: 0 0 40px rgba(59,130,246,0.4); animation: pulse-ring 2.5s infinite; }
    .challenge-map-node.state-locked    .challenge-map-node-icon { opacity: 0.5; background: var(--bg); }

    @keyframes pulse-ring  { 0%,100%{box-shadow:0 0 40px rgba(59,130,246,.4)} 50%{box-shadow:0 0 60px rgba(59,130,246,.7)} }
    @keyframes pulse-amber { 0%,100%{box-shadow:0 0 30px rgba(245,158,11,.25)} 50%{box-shadow:0 0 50px rgba(245,158,11,.5)} }

    /* Badge */
    .challenge-map-node-badge { position: absolute; top: -6px; right: -6px; font-size: 0.65rem; font-weight: 700; padding: 4px 8px; border-radius: 12px; border: 3px solid var(--surface); display: none; z-index: 3; letter-spacing: 0.05em; box-shadow: 0 4px 10px rgba(0,0,0,0.3); }
    .challenge-map-node.state-completed  .challenge-map-node-badge { display: flex; align-items: center; gap: 4px; background: var(--accent3); color: #fff; }
    .challenge-map-node.state-inprogress .challenge-map-node-badge { display: flex; align-items: center; gap: 4px; background: var(--amber); color: #000; }

    /* Info card */
    .challenge-map-node-info { background: rgba(17,28,45,0.65); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.08); padding: 18px; border-radius: var(--radius); text-align: center; width: 240px; opacity: 0.85; transition: all 0.3s; pointer-events: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.4); }
    .challenge-map-node:hover .challenge-map-node-info { opacity: 1; border-color: rgba(255,255,255,0.15); }
    .challenge-map-node.state-active     .challenge-map-node-info { opacity: 1; border-color: rgba(59,130,246,0.4); background: rgba(17,28,45,0.8); }
    .challenge-map-node.state-inprogress .challenge-map-node-info { opacity: 1; border-color: rgba(245,158,11,0.35); background: rgba(17,28,45,0.85); }
    .challenge-map-node.state-completed  .challenge-map-node-info { border-color: rgba(16,185,129,0.2); }

    .challenge-map-node-number { font-size: 0.7rem; font-weight: 700; color: var(--accent); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 6px; }
    .challenge-map-node.state-completed  .challenge-map-node-number { color: var(--accent3); }
    .challenge-map-node.state-inprogress .challenge-map-node-number { color: var(--amber); }
    .challenge-map-node.state-locked     .challenge-map-node-number { color: var(--dim); }

    .challenge-map-node-title  { font-size: 0.95rem; font-weight: 600; color: var(--text); margin-bottom: 8px; line-height: 1.4; }
    .challenge-map-node-xp     { font-size: 0.8rem; color: var(--warn); font-weight: 700; margin-bottom: 12px; display: inline-flex; align-items: center; gap: 4px; background: rgba(245,158,11,0.1); padding: 4px 10px; border-radius: 20px; }
    .challenge-map-node-score  { font-size: 0.75rem; color: var(--muted); margin-bottom: 10px; font-weight: 500; }
    .challenge-map-node-status { font-size: 0.75rem; color: var(--dim); margin-bottom: 8px; font-weight: 500; }

    /* ── BUTTONS ── */
    /* Start (blue) */
    .btn-map-start { display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%; padding: 10px 14px; background: linear-gradient(135deg, var(--accent), #2563eb); color: #fff; text-decoration: none; font-size: 0.85rem; font-weight: 600; border-radius: var(--radius-sm); transition: all 0.2s; box-shadow: 0 4px 12px rgba(59,130,246,0.3); border: 1px solid rgba(255,255,255,0.1); }
    .btn-map-start:hover { background: linear-gradient(135deg, #4f93f7, var(--accent)); transform: translateY(-2px); box-shadow: 0 6px 16px rgba(59,130,246,0.4); }

    /* Continue (amber) */
    .btn-map-continue { display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%; padding: 10px 14px; background: linear-gradient(135deg, var(--amber), #d97706); color: #000; text-decoration: none; font-size: 0.85rem; font-weight: 700; border-radius: var(--radius-sm); transition: all 0.2s; box-shadow: 0 4px 12px rgba(245,158,11,0.3); border: 1px solid rgba(255,255,255,0.1); }
    .btn-map-continue:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(245,158,11,0.45); }

    /* Retry (ghost) */
    .btn-map-retry { display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%; padding: 10px 14px; background: rgba(255,255,255,0.03); border: 1px solid var(--border); color: var(--text); text-decoration: none; font-size: 0.85rem; font-weight: 600; border-radius: var(--radius-sm); transition: all 0.2s; }
    .btn-map-retry:hover { background: var(--surface2); border-color: var(--border-hover); transform: translateY(-1px); }

    /* Retake (red — timer expired, fresh attempt) */
    .btn-map-retake { display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%; padding: 10px 14px; background: linear-gradient(135deg, var(--red), #dc2626); color: #fff; font-size: 0.85rem; font-weight: 600; border-radius: var(--radius-sm); border: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(239,68,68,0.3); font-family: 'Inter', sans-serif; }
    .btn-map-retake:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(239,68,68,0.45); }

    /* Retake exhausted (no attempts left) */
    .btn-map-exhausted { display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%; padding: 10px 14px; background: rgba(239,68,68,0.07); border: 1px solid rgba(239,68,68,0.25); color: var(--red); opacity: 0.55; font-size: 0.85rem; font-weight: 600; border-radius: var(--radius-sm); cursor: not-allowed; }

    /* Retakes-remaining label */
    .retake-meta { font-size: 0.7rem; color: var(--muted); text-align: center; margin-bottom: 8px; letter-spacing: 0.02em; }

    /* ── EXPIRED node state ── */
    .challenge-map-node.state-expired .challenge-map-node-icon  { background: rgba(239,68,68,0.12); border-color: var(--red); color: var(--red); box-shadow: 0 0 30px rgba(239,68,68,.2); }
    .challenge-map-node.state-expired .challenge-map-node-badge { display: flex; align-items: center; gap: 4px; background: var(--red); color: #fff; }
    .challenge-map-node.state-expired .challenge-map-node-number { color: var(--red); }
    .challenge-map-node.state-expired .challenge-map-node-info  { opacity: 1; border-color: rgba(239,68,68,0.35); background: rgba(17,28,45,0.85); }

    /* Empty state */
    .challenge-map-empty-state {
      position: absolute;
      inset: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 32px;
      text-align: center;
      z-index: 20;
    }
    .challenge-map-empty-card {
      width: min(520px, 100%);
      background: rgba(17,28,45,0.86);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 28px;
      box-shadow: 0 16px 45px rgba(0,0,0,.35);
    }
    .challenge-map-empty-card h2 {
      font-size: 1.25rem;
      margin-bottom: 10px;
    }
    .challenge-map-empty-card p {
      color: var(--muted);
      line-height: 1.65;
      font-size: .9rem;
      margin-bottom: 18px;
    }

    /* Legend */
    .challenge-map-legend { position: absolute; bottom: 20px; right: 24px; background: rgba(17,28,45,0.85); backdrop-filter: blur(10px); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 12px 16px; display: flex; flex-direction: column; gap: 8px; z-index: 30; }
    .challenge-map-legend-item { display: flex; align-items: center; gap: 8px; font-size: 0.75rem; color: var(--muted); font-weight: 500; }
    .challenge-map-legend-dot { width: 10px; height: 10px; border-radius: 50%; }
    .challenge-map-legend-dot.passed   { background: var(--accent3); }
    .challenge-map-legend-dot.inprog   { background: var(--amber); }
    .challenge-map-legend-dot.current  { background: var(--accent); }
    .challenge-map-legend-dot.locked   { background: var(--dim); }
  </style>
</head>
<body>
<div class="page-layout-wrapper">
  @include('partials.sidebar')

  <div class="challenge-map-main">

    <div class="challenge-map-topbar">
      <div class="challenge-map-topbar-title">
        {{ $category->name }} Coding Path
        <small>🐍 Python</small>
      </div>
      <a href="{{ route('challenges.coding') }}" class="challenge-map-topbar-btn">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Paths
      </a>
    </div>

    @if(session('success'))
      <div class="challenge-map-alert">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div class="challenge-map-alert" style="background:rgba(239,68,68,0.1); border-bottom-color:rgba(239,68,68,0.3); color:var(--red);">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ session('error') }}
      </div>
    @endif

    @if(!empty($exceptionalNotifications))
      @foreach($exceptionalNotifications as $notification)
        <div class="challenge-map-alert" style="background:rgba(245,158,11,0.12); border-bottom-color:rgba(245,158,11,0.35); color:#fbbf24;">
          <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
          {{ $notification }}
        </div>
      @endforeach
    @endif

    <div class="challenge-map-container">
      <div class="challenge-map-viewport" id="challenge-map-viewport">

        @php
          $count      = $challenges->count();
          $nodeWidth  = 260;
          $nodeHeight = 220;

          /*
           * Prevent DivisionByZeroError when this category has no coding challenges.
           * Before:
           * $cols = min(3, $count);
           * If $count is 0, $cols becomes 0, then ceil($count / $cols) crashes.
           */
          $cols       = max(1, min(3, $count));
          $rows       = $count > 0 ? (int) ceil($count / $cols) : 1;
          $canvasW    = max(900,  $cols * $nodeWidth  + 200);
          $canvasH    = max(600,  $rows * $nodeHeight + 200);

          // Zigzag positions
          $positions = [];
          foreach ($challenges as $idx => $ch) {
              $row = intdiv($idx, $cols);
              $col = $idx % $cols;
              // Alternate direction each row
              if ($row % 2 === 1) { $col = ($cols - 1) - $col; }
              $x = 100 + $col * $nodeWidth  + ($nodeWidth  / 2);
              $y = 120 + $row * $nodeHeight + ($nodeHeight / 2);
              $positions[$idx] = compact('x', 'y');
          }
        @endphp

        <div class="challenge-map-content" style="width:{{ $canvasW }}px; height:{{ $canvasH }}px;">

          @if($count === 0)
            <div class="challenge-map-empty-state">
              <div class="challenge-map-empty-card">
                <h2>No coding challenges yet</h2>
                <p>
                  This path is available, but there are no coding challenge records under this category yet.
                  Add coding challenges for this category first, then refresh this page.
                </p>
                <a href="{{ route('challenges.coding') }}" class="challenge-map-topbar-btn" style="display:inline-flex;">
                  Back to Coding Paths
                </a>
              </div>
            </div>
          @endif

          {{-- SVG connector lines --}}
          <svg class="challenge-map-svg-paths" viewBox="0 0 {{ $canvasW }} {{ $canvasH }}" preserveAspectRatio="none">
            @foreach($challenges as $idx => $ch)
              @if($idx > 0)
                @php
                  $from = $positions[$idx - 1];
                  $to   = $positions[$idx];
                  $isCompleted = in_array($challenges[$idx - 1]->id, $completedChallengeIds);
                  $isInProg    = in_array($challenges[$idx - 1]->id, $inProgressChallengeIds);
                  $lineCls     = $isCompleted ? 'passed' : ($isInProg ? 'inprog' : '');

                  // Current = first non-completed, non-inprogress
                  $prevCompleted = in_array($challenges[$idx - 1]->id, $completedChallengeIds);
                  $thisActive    = !in_array($ch->id, $completedChallengeIds)
                                   && !in_array($ch->id, $inProgressChallengeIds)
                                   && $prevCompleted;
                  if ($thisActive) { $lineCls = 'current'; }
                @endphp
                <path class="challenge-map-path-line {{ $lineCls }}"
                      d="M {{ $from['x'] }} {{ $from['y'] }} C {{ $from['x'] }} {{ ($from['y'] + $to['y']) / 2 }}, {{ $to['x'] }} {{ ($from['y'] + $to['y']) / 2 }}, {{ $to['x'] }} {{ $to['y'] }}"
                />
              @endif
            @endforeach
          </svg>

          {{-- Nodes --}}
          @php $firstActiveSet = false; @endphp
          @foreach($challenges as $idx => $ch)
          @php
              $pos          = $positions[$idx];
              $isCompleted  = in_array($ch->id, $completedChallengeIds);
              $isInProgress = in_array($ch->id, $inProgressChallengeIds);
              $best         = $bestScores[$ch->id] ?? null;
              $totalQ       = $ch->codingQuestions()->count();

              // ── Detect expired timer & retake availability ───────────────
              // We compute this fresh from the DB so it stays accurate even
              // when the user pressed Back mid-quiz without submitting.
              // (The timer is always server-side in started_at, so there is
              //  no way for the client to fool it or reset it by going back.)
              $isExpired   = false;
              $retakeCount = 0;
              $canRetake   = false;

              if ($isInProgress) {
                  $userId    = auth()->id();
                  $questions = $ch->codingQuestions()->get(['id', 'time_limit_seconds']);
                  $qIds      = $questions->pluck('id');

                  // Find the earliest non-expired attempt (the active question's timer).
                  $activeAttempt = \App\Models\CodingQuestionAttempt::where('user_id', $userId)
                      ->whereIn('coding_question_id', $qIds)
                      ->orderBy('started_at')
                      ->first();

                  if ($activeAttempt) {
                      $q       = $questions->firstWhere('id', $activeAttempt->coding_question_id);
                      $elapsed = max(0, now()->timestamp - $activeAttempt->started_at->timestamp);
                      // Expired if DB flag is set OR wall-clock has passed the limit
                      $isExpired = $activeAttempt->expired
                                || ($q && $elapsed >= $q->time_limit_seconds);
                  }

                  $retakeRecord = \App\Models\CodingChallengeRetake::where('user_id', $userId)
                      ->where('challenge_id', $ch->id)
                      ->first();
                  $retakeCount = $retakeRecord?->retake_count ?? 0;
                  $canRetake   = $retakeCount < \App\Models\CodingChallengeRetake::MAX_RETAKES;
              }

              // ── Determine visual state ────────────────────────────────────
              if ($isCompleted) {
                  $state = 'completed';
              } elseif ($isInProgress && $isExpired) {
                  $state = 'expired';       // new — red icon, Retake button
              } elseif ($isInProgress) {
                  $state = 'inprogress';
              } elseif ($idx === 0) {
                  $state = 'active';
              } else {
                  $prevCompleted = in_array($challenges[$idx - 1]->id, $completedChallengeIds);
                  $prevInProg    = in_array($challenges[$idx - 1]->id, $inProgressChallengeIds);
                  $state = ($prevCompleted || $prevInProg) ? 'active' : 'locked';
              }

              $isActiveNode = ($state === 'active' && !$firstActiveSet);
              if ($isActiveNode) $firstActiveSet = true;
          @endphp

            <div class="challenge-map-node state-{{ $state }}"
                 style="left: {{ $pos['x'] }}px; top: {{ $pos['y'] }}px;"
                 {{ $isActiveNode ? 'id=challenge-map-active-node' : '' }}>

              <div class="challenge-map-node-icon-wrap">
                <div class="challenge-map-node-icon">
                  @if($isCompleted)
                    <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                  @elseif($isInProgress && $isExpired)
                    {{-- Clock with X — time ran out --}}
                    <svg width="30" height="30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l3 3M15 9l-6 6M9 9l6 6"/></svg>
                  @elseif($isInProgress)
                    {{-- Play/resume icon --}}
                    <svg width="28" height="28" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                  @elseif($state === 'locked')
                    <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                  @else
                    <svg width="30" height="30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                  @endif
                </div>

                <div class="challenge-map-node-badge">
                  @if($isCompleted)
                    <svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    Done
                  @elseif($isInProgress && $isExpired)
                    ⏱ Time's Up
                  @elseif($isInProgress)
                    ⏱ In Progress
                  @endif
                </div>
              </div>

              <div class="challenge-map-node-info">
                <div class="challenge-map-node-number">Challenge {{ $idx + 1 }}</div>
                <div class="challenge-map-node-title">{{ $ch->title }}</div>

                <div class="challenge-map-node-xp">
                  <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                  {{ $ch->base_xp }} XP · {{ $totalQ }} problems
                </div>

                @if($best)
                  <div class="challenge-map-node-score">
                    Best: {{ $best['score'] }} tests · {{ $best['xp'] }} XP earned
                  </div>
                @endif

                {{-- ── BUTTON LOGIC ── --}}
                @if($isCompleted)
                  <div class="challenge-map-node-status" style="color:var(--accent3);">✅ All problems solved</div>
                  <a href="{{ route('challenges.coding.quiz', ['slug' => $slug, 'challenge' => $ch->id]) }}"
                     class="btn-map-retry">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Practice Again
                  </a>

                @elseif($isInProgress && $isExpired)
                  {{-- ── Timer expired — offer Retake (max 3) ── --}}
                  <div class="challenge-map-node-status" style="color:var(--red);">⏱ Time's up — round ended</div>

                  @if($canRetake)
                    <div class="retake-meta">
                      {{ \App\Models\CodingChallengeRetake::MAX_RETAKES - $retakeCount }}
                      retake{{ (\App\Models\CodingChallengeRetake::MAX_RETAKES - $retakeCount) === 1 ? '' : 's' }}
                      remaining
                    </div>
                    <form method="POST"
                          action="{{ route('challenges.coding.retake', ['slug' => $slug, 'challenge' => $ch->id]) }}"
                          onsubmit="return confirm('This will reset all your progress for this challenge and start fresh. Continue?')"
                          style="width:100%">
                      @csrf
                      <button type="submit" class="btn-map-retake">
                        🔄 Retake Challenge
                      </button>
                    </form>
                  @else
                    <div class="retake-meta">No retakes remaining ({{ \App\Models\CodingChallengeRetake::MAX_RETAKES }}/{{ \App\Models\CodingChallengeRetake::MAX_RETAKES }} used)</div>
                    <div class="btn-map-exhausted">
                      ❌ No More Retakes
                    </div>
                  @endif

                @elseif($isInProgress)
                  {{-- ── Timer is still running — Continue ── --}}
                  <div class="challenge-map-node-status" style="color:var(--amber);">⏱ Timer is running — pick up where you left off</div>
                  <a href="{{ route('challenges.coding.quiz', ['slug' => $slug, 'challenge' => $ch->id]) }}"
                     class="btn-map-continue">
                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    Continue Challenge
                  </a>

                @elseif($state === 'active')
                  <a href="{{ route('challenges.coding.quiz', ['slug' => $slug, 'challenge' => $ch->id]) }}"
                     class="btn-map-start">
                    Start Challenge
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                  </a>

                @else
                  <div class="challenge-map-node-status">🔒 Complete the previous challenge to unlock</div>
                @endif

              </div>
            </div>
          @endforeach

        </div>{{-- .challenge-map-content --}}
      </div>{{-- .challenge-map-viewport --}}

      <div class="challenge-map-legend">
        <div class="challenge-map-legend-item"><div class="challenge-map-legend-dot passed"></div> Completed</div>
        <div class="challenge-map-legend-item"><div class="challenge-map-legend-dot inprog"></div> In Progress</div>
        <div class="challenge-map-legend-item"><div class="challenge-map-legend-dot current"></div> Available</div>
        <div class="challenge-map-legend-item"><div class="challenge-map-legend-dot locked"></div> Locked</div>
      </div>

    </div>{{-- .challenge-map-container --}}
  </div>{{-- .challenge-map-main --}}
</div>

<script>
  const viewport = document.getElementById('challenge-map-viewport');
  let isDragging = false, startX, startY, scrollLeft, scrollTop;

  viewport.addEventListener('mousedown', e => {
    if (e.target.closest('a, button')) return;
    isDragging = true;
    startX = e.pageX - viewport.offsetLeft;
    startY = e.pageY - viewport.offsetTop;
    scrollLeft = viewport.scrollLeft;
    scrollTop  = viewport.scrollTop;
    viewport.style.cursor = 'grabbing';
    viewport.style.userSelect = 'none';
  });
  window.addEventListener('mouseup', () => {
    isDragging = false;
    viewport.style.cursor = 'grab';
    viewport.style.userSelect = '';
  });
  window.addEventListener('mousemove', e => {
    if (!isDragging) return;
    e.preventDefault();
    viewport.scrollLeft = scrollLeft - (e.pageX - viewport.offsetLeft - startX);
    viewport.scrollTop  = scrollTop  - (e.pageY - viewport.offsetTop  - startY);
  });

  window.addEventListener('load', () => {
    const active = document.getElementById('challenge-map-active-node');
    if (active) {
      viewport.scrollTo({
        left: active.offsetLeft - viewport.clientWidth  / 2,
        top:  active.offsetTop  - viewport.clientHeight / 2,
        behavior: 'smooth',
      });
    }
  });

  // ── bfcache prevention ────────────────────────────────────────────────────
  // Without this, pressing Back from the quiz page restores the cached MAP,
  // which still shows "Start Challenge" even though an attempt now exists.
  // Reloading forces the server to re-query the DB and render the correct
  // "Continue Challenge" button.
  window.addEventListener('pageshow', e => {
    if (e.persisted) window.location.reload();
  });
</script>
</body>
</html>