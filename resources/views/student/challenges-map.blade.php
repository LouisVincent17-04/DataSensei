<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DataSensei — {{ $category->name }} Path</title>
<style>
    /* Challenge path map. Colours, type and radius come from partials.design-system.
       Node positions are computed in PHP below (4 columns 420px apart, rows 380px
       apart, each node centred on its point). Keep the node box at or under
       76px icon + 12px gap + info card, 240px wide, or neighbouring nodes overlap. */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      margin: 0;
      background: var(--bg);
      color: var(--text);
      font-family: var(--ds-font-sans);
    }
    .page-layout-wrapper { display: flex; min-height: 100vh; min-height: calc(100dvh - var(--ds-sticky-top, 0px)); }

    /* ── Page frame: title bar on top, the map fills the rest ── */
    .challenge-map-main { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }

    .challenge-map-topbar {
      min-height: 60px; padding: 10px 32px; flex-shrink: 0;
      display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px 16px;
      background: var(--bg); border-bottom: 1px solid var(--border);
    }
    .challenge-map-topbar-title { flex: 1 1 auto; min-width: 0; }
    .challenge-map-topbar-title small {
      margin-left: 8px; color: var(--muted); font-size: .875rem; font-weight: 500;
      letter-spacing: 0; white-space: nowrap; font-variant-numeric: tabular-nums;
    }
    .challenge-map-topbar-btn {
      display: inline-flex; align-items: center; justify-content: center; gap: 8px; flex-shrink: 0;
      min-height: 38px; padding: 0 16px;
      border: 1px solid var(--ds-border-strong); border-radius: var(--radius-sm);
      background: var(--surface2); color: var(--text);
      font-size: .875rem; font-weight: 500; line-height: 1.2; text-decoration: none; white-space: nowrap;
      transition: background .12s ease;
    }
    .challenge-map-topbar-btn svg { width: 16px; height: 16px; }
    .challenge-map-topbar-btn:hover { background: var(--ds-surface-hover); }

    /* Flash messages (the shared notification script usually moves these into its stack) */
    .challenge-map-alert {
      display: flex; align-items: flex-start; gap: 8px; flex-shrink: 0;
      margin: 16px 32px 0; padding: 12px 16px;
      border: 1px solid var(--ds-success-border); border-radius: var(--radius-sm);
      background: var(--ds-success-soft); color: #d1fae5;
      font-size: .875rem; line-height: 1.5;
    }
    .challenge-map-alert svg { width: 16px; height: 16px; flex: 0 0 16px; margin-top: 2px; }

    /* ── Map canvas: scrolls (and drags) inside its own box ── */
    .challenge-map-container { flex: 1 1 auto; position: relative; min-height: 360px; overflow: hidden; background: var(--bg); }
    .challenge-map-viewport { position: absolute; inset: 0; overflow: auto; cursor: grab; scroll-behavior: smooth; }
    .challenge-map-viewport:active { cursor: grabbing; }
    .challenge-map-content { position: relative; /* width and height come from the PHP layout */ }

    /* Connectors */
    .challenge-map-svg-paths { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1; }
    .challenge-map-path-line { fill: none; stroke: var(--ds-border-strong); stroke-width: 2; stroke-dasharray: 6 6; }
    .challenge-map-path-line.passed  { stroke: var(--ds-success); stroke-dasharray: none; }
    .challenge-map-path-line.current { stroke: var(--accent); }

    /* ── Nodes ── */
    .challenge-map-node { position: absolute; z-index: 10; display: flex; flex-direction: column; align-items: center; gap: 12px; transform: translate(-50%, -50%); }
    .challenge-map-node:hover { z-index: 20; }

    .challenge-map-node-icon-wrap { position: relative; }
    .challenge-map-node-icon {
      position: relative; z-index: 2; width: 76px; height: 76px;
      display: flex; align-items: center; justify-content: center;
      border: 2px solid var(--ds-border-strong); border-radius: 50%;
      background: var(--surface); color: var(--muted);
    }
    .challenge-map-node-icon svg { width: 28px; height: 28px; }

    .challenge-map-node.state-completed .challenge-map-node-icon { border-color: var(--ds-success); background: color-mix(in srgb, var(--ds-success) 14%, var(--ds-surface)); color: var(--ds-success-text); }
    .challenge-map-node.state-active    .challenge-map-node-icon { border-color: var(--accent); background: color-mix(in srgb, var(--ds-accent) 14%, var(--ds-surface)); color: var(--ds-accent-text); }
    .challenge-map-node.state-failed    .challenge-map-node-icon { border-color: var(--ds-danger); background: color-mix(in srgb, var(--ds-danger) 12%, var(--ds-surface)); color: var(--ds-danger-text); }
    .challenge-map-node.state-locked    .challenge-map-node-icon { border-color: var(--border); background: var(--surface3); color: var(--dim); }

    /* State label on the node ring */
    .challenge-map-node-badge {
      position: absolute; left: 50%; bottom: -4px; z-index: 3; transform: translateX(-50%);
      display: none; align-items: center; gap: 4px; padding: 1px 6px;
      border: 1px solid var(--ds-success-border); border-radius: var(--radius-xs);
      background: color-mix(in srgb, var(--ds-success) 18%, var(--ds-surface)); color: var(--ds-success-text);
      font-size: .75rem; font-weight: 600; line-height: 1.4; white-space: nowrap;
    }
    .challenge-map-node.state-completed .challenge-map-node-badge { display: flex; }

    .challenge-map-node.state-active    .challenge-map-node-info { border-color: var(--ds-accent-border); }
    .challenge-map-node.state-completed .challenge-map-node-info { border-color: var(--ds-success-border); }
    .challenge-map-node.state-failed    .challenge-map-node-info { border-color: var(--ds-danger-border); }

    /* Info card */
    .challenge-map-node-info {
      width: 240px; padding: 14px 16px; pointer-events: auto; text-align: center;
      border: 1px solid var(--border); border-radius: var(--radius); background: var(--surface);
    }
    .challenge-map-node-number { margin-bottom: 2px; color: var(--muted); font-size: .75rem; font-weight: 500; line-height: 1.4; }
    .challenge-map-node-title  { margin-bottom: 2px; color: var(--text); font-size: .9375rem; font-weight: 600; line-height: 1.35; overflow-wrap: anywhere; }
    .challenge-map-node.state-locked .challenge-map-node-title { color: var(--muted); }
    .challenge-map-node-xp     { margin-bottom: 10px; color: var(--muted); font-size: .8125rem; font-weight: 500; line-height: 1.4; font-variant-numeric: tabular-nums; }
    .challenge-map-node-score  { margin-bottom: 6px; color: var(--ds-text-secondary); font-size: .8125rem; line-height: 1.4; font-variant-numeric: tabular-nums; }
    .challenge-map-node-status { margin-bottom: 8px; color: var(--muted); font-size: .8125rem; font-weight: 500; line-height: 1.4; overflow-wrap: anywhere; }
    .challenge-map-node-status:last-child { margin-bottom: 0; }
    .challenge-map-node.state-completed .challenge-map-node-status { color: var(--ds-success-text); }
    .challenge-map-node.state-failed    .challenge-map-node-status { color: var(--ds-danger-text); }

    /* Buttons */
    .challenge-map-btn-start {
      display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%;
      min-height: 32px; padding: 6px 12px; border: 1px solid; border-radius: var(--radius-sm);
      font: 500 .8125rem/1.3 var(--ds-font-sans); text-align: center; text-decoration: none;
      transition: background .12s ease, border-color .12s ease;
      border-color: var(--accent); background: var(--accent); color: #fff;
    }
    .challenge-map-btn-start:hover { border-color: var(--accent-hover); background: var(--accent-hover); }
    .challenge-map-btn-start svg { width: 14px; height: 14px; }

    .challenge-map-btn-retry {
      display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%;
      min-height: 32px; padding: 6px 12px; border: 1px solid; border-radius: var(--radius-sm);
      font: 500 .8125rem/1.3 var(--ds-font-sans); text-align: center; text-decoration: none;
      transition: background .12s ease, border-color .12s ease;
      margin-top: 6px; border-color: var(--ds-border-strong); background: var(--surface2); color: var(--text);
    }
    .challenge-map-btn-retry:hover { background: var(--ds-surface-hover); }
    .challenge-map-btn-retry svg { width: 14px; height: 14px; }


    /* ── Legend ── */
    .challenge-map-legend {
      position: absolute; left: 32px; bottom: 24px; z-index: 50; max-width: calc(100% - 64px);
      display: flex; flex-wrap: wrap; align-items: center; gap: 8px 16px; padding: 8px 12px;
      border: 1px solid var(--ds-border-strong); border-radius: var(--radius); background: var(--surface);
      pointer-events: none;
    }
    .challenge-map-legend-item { display: flex; align-items: center; gap: 6px; color: var(--ds-text-secondary); font-size: .75rem; font-weight: 500; line-height: 1.4; white-space: nowrap; }
    .challenge-map-legend-dot { width: 10px; height: 10px; flex-shrink: 0; border: 2px solid; border-radius: 50%; }
    .challenge-map-legend-dot.passed  { border-color: var(--ds-success); background: var(--ds-success); }
    .challenge-map-legend-dot.current { border-color: var(--accent); background: var(--ds-accent-soft); }
    .challenge-map-legend-dot.locked  { border-color: var(--ds-border-strong); background: var(--surface3); }
    .challenge-map-legend-dot.failed  { border-color: var(--ds-danger); background: var(--ds-danger-soft); }

    @media (max-width: 900px) {
      .challenge-map-topbar { min-height: 56px; padding: 8px 20px; }
      .challenge-map-alert { margin: 12px 20px 0; }
    }
    @media (max-width: 640px) {
      .challenge-map-topbar { padding: 8px 16px; }
      .challenge-map-topbar-btn { min-height: 32px; padding: 0 12px; font-size: .8125rem; }
      .challenge-map-alert { margin: 12px 16px 0; }
      .challenge-map-legend { left: 16px; right: 16px; bottom: 16px; max-width: none; justify-content: center; gap: 6px 12px; }
    }
  </style>
    @include('partials.page-head', ['pageDescription' => 'Practise data science challenges and track your mastery.'])
</head>
<body>

<div class="page-layout-wrapper">
  @include('partials.sidebar')

  <div class="challenge-map-main">

    <div class="challenge-map-topbar">
      <h1 class="challenge-map-topbar-title ds-page-title">
        {{ $category->name }} Path
        <small>{{ $challenges->count() }} modules</small>
      </h1>
      <a href="{{ route('challenges') }}" class="challenge-map-topbar-btn">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        All Paths
      </a>
    </div>

    @if(session('success'))
      <div class="challenge-map-alert">
        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
      </div>
    @endif

    @if(!empty($exceptionalNotifications))
      @foreach($exceptionalNotifications as $notification)
        <div class="challenge-map-alert" data-ds-global-notification role="status" style="background:var(--ds-warning-soft); border-color:var(--ds-warning-border); color:#fef3c7;">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
          </svg>
          {{ $notification }}
        </div>
      @endforeach
    @endif

    <div class="challenge-map-container">
      <div class="challenge-map-viewport" id="challenge-map-viewport">
        
        @php
          // Layout Math
          $nodesPerRow = 4;
          $xSlots      = [260, 680, 1100, 1520]; 
          $yStep       = 380; 
          $yStart      = 240;
          $positions   = [];

          foreach ($challenges as $i => $ch) {
              $row   = intval($i / $nodesPerRow);
              $col   = $i % $nodesPerRow;
              $isOdd = $row % 2 === 1;
              $xIdx  = $isOdd ? ($nodesPerRow - 1 - $col) : $col;
              $positions[] = [
                  'x' => $xSlots[$xIdx],
                  'y' => $yStart + $row * $yStep,
              ];
          }

          // DYNAMIC HEIGHT & WIDTH CALCULATION 
          $totalRows = ceil($challenges->count() / $nodesPerRow);
          $contentHeight = $yStart + ($totalRows * $yStep) + 200; // Extra padding at bottom
          
          $maxX = max(array_column($positions, 'x'));
          $contentWidth = $maxX + 350; // Extra padding on the right
        @endphp

        <div class="challenge-map-content" style="height: {{ $contentHeight }}px; width: {{ $contentWidth }}px;">

          {{-- ── SVG CONNECTOR PATHS ── --}}
          <svg class="challenge-map-svg-paths" xmlns="http://www.w3.org/2000/svg">
            @foreach($challenges as $i => $ch)
              @if(!$loop->last)
                @php
                  $curr = $positions[$i];
                  $next = $positions[$i + 1];

                  $currPassed = in_array($ch->id, $completedChallengeIds);
                  $nextPassed = in_array($challenges[$i + 1]->id, $completedChallengeIds);
                  $lineClass  = $currPassed ? ($nextPassed ? 'passed' : 'current') : '';
                @endphp
                <path
                  class="challenge-map-path-line {{ $lineClass }}"
                  d="M {{ $curr['x'] }} {{ $curr['y'] }}
                     C {{ ($curr['x']+$next['x'])/2 }} {{ $curr['y'] }},
                       {{ ($curr['x']+$next['x'])/2 }} {{ $next['y'] }},
                       {{ $next['x'] }} {{ $next['y'] }}"
                />
              @endif
            @endforeach
          </svg>

          {{-- ── CHALLENGE NODES ── --}}
          @php $firstIncomplete = true; @endphp

          @foreach($challenges as $i => $ch)
            @php
              $pos     = $positions[$i];
              $passed  = in_array($ch->id, $completedChallengeIds);
              $tried   = isset($bestScores[$ch->id]);
              $hasActiveAttempt = in_array($ch->id, $activeAttemptIds ?? []); 

              if ($passed) {
                  $state = 'completed';
              } elseif ($firstIncomplete) {
                  $state = $tried ? 'failed' : 'active'; 
                  $firstIncomplete = false;
              } else {
                  $state = 'locked';
              }

              $totalQ   = $ch->questions->count();
              $best     = $bestScores[$ch->id] ?? null;
            @endphp

            <div class="challenge-map-node state-{{ $state }}"
                 style="top: {{ $pos['y'] }}px; left: {{ $pos['x'] }}px;"
                 @if($state === 'active' || $state === 'failed') id="challenge-map-active-node" @endif>

              <div class="challenge-map-node-icon-wrap">
                <div class="challenge-map-node-icon">
                  @if($state === 'completed')
                    <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                  @elseif($state === 'failed')
                    <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                  @elseif($state === 'locked')
                    <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                      <path d="M7 11V7a5 5 0 0110 0v4"/>
                    </svg>
                  @else
                    <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                  @endif
                </div>

                <div class="challenge-map-node-badge">
                  Passed
                </div>
              </div>

              <div class="challenge-map-node-info">
                <div class="challenge-map-node-number">Module {{ $i + 1 }}</div>
                <div class="challenge-map-node-title">{{ $ch->title }}</div>
                
                <div class="challenge-map-node-xp">
                    {{ $ch->base_xp }} XP
                </div>

                @if($best)
                  <div class="challenge-map-node-score">
                    Best: {{ $best['score'] }}/{{ $totalQ }}
                    ({{ $totalQ > 0 ? round(($best['score'] / $totalQ) * 100) : 0 }}%)
                  </div>
                @endif

                @if($state === 'completed')
                  <div class="challenge-map-node-status">Challenge Completed</div>
                  @if(isset($latestResultAttemptIds[$ch->id]))
                    <a href="{{ route('challenges.quiz.result', ['slug' => $slug, 'challenge' => $ch->id, 'attempt' => $latestResultAttemptIds[$ch->id]]) }}" class="challenge-map-btn-start">
                      View Results & History
                    </a>
                  @endif
                  <a href="{{ route('challenges.quiz', ['slug' => $slug, 'challenge' => $ch->id]) }}" class="challenge-map-btn-retry">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    {{ $hasActiveAttempt ? 'Resume Practice' : 'Practice Again' }}
                  </a>

                @elseif($state === 'failed')
                  <div class="challenge-map-node-status">Needs 70% to Pass</div>
                  <a href="{{ route('challenges.quiz', ['slug' => $slug, 'challenge' => $ch->id]) }}" class="challenge-map-btn-start">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    {{ $hasActiveAttempt ? 'Resume Attempt' : 'Try Again' }}
                  </a>
                  @if(isset($latestResultAttemptIds[$ch->id]))
                    <a href="{{ route('challenges.quiz.result', ['slug' => $slug, 'challenge' => $ch->id, 'attempt' => $latestResultAttemptIds[$ch->id]]) }}" class="challenge-map-btn-retry">
                      View Results & History
                    </a>
                  @endif

                @elseif($state === 'active')
                  <a href="{{ route('challenges.quiz', ['slug' => $slug, 'challenge' => $ch->id]) }}" class="challenge-map-btn-start">
                    {{ $hasActiveAttempt ? 'Resume Attempt' : 'Start Challenge' }}
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                  </a>
                  @if(isset($latestResultAttemptIds[$ch->id]))
                    <a href="{{ route('challenges.quiz.result', ['slug' => $slug, 'challenge' => $ch->id, 'attempt' => $latestResultAttemptIds[$ch->id]]) }}" class="challenge-map-btn-retry">
                      View Results & History
                    </a>
                  @endif

                @else
                  <div class="challenge-map-node-status">Complete prior modules to unlock</div>
                @endif

              </div>
            </div>
          @endforeach

        </div>{{-- .challenge-map-content --}}
      </div>{{-- .challenge-map-viewport --}}

      <div class="challenge-map-legend">
        <div class="challenge-map-legend-item"><div class="challenge-map-legend-dot passed"></div> Passed</div>
        <div class="challenge-map-legend-item"><div class="challenge-map-legend-dot current"></div> Current</div>
        <div class="challenge-map-legend-item"><div class="challenge-map-legend-dot failed"></div> Failed</div>
        <div class="challenge-map-legend-item"><div class="challenge-map-legend-dot locked"></div> Locked</div>
      </div>

    </div>{{-- .challenge-map-container --}}
  </div>{{-- .challenge-map-main --}}
</div>{{-- .page-layout-wrapper --}}

  <script>
    const viewport = document.getElementById('challenge-map-viewport');
    let isDragging = false, startX, startY, scrollLeft, scrollTop;

    viewport.addEventListener('mousedown', (e) => {
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
    
    window.addEventListener('mousemove', (e) => {
      if (!isDragging) return;
      e.preventDefault();
      viewport.scrollLeft = scrollLeft - (e.pageX - viewport.offsetLeft - startX);
      viewport.scrollTop  = scrollTop  - (e.pageY - viewport.offsetTop  - startY);
    });

    window.addEventListener('load', () => {
      const active = document.getElementById('challenge-map-active-node');
      if (active) {
        viewport.scrollTo({
            left: active.offsetLeft - viewport.clientWidth / 2,
            top: active.offsetTop - viewport.clientHeight / 2,
            behavior: 'smooth'
        });
      }
    });
  </script>
</body>
</html>
