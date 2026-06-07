<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DataSensei — {{ $category->name }} Path</title>
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
      --text:         #fafafa;
      --muted:        #8b9ebb;
      --dim:          #4a5f82;
      --radius:       16px;
      --radius-sm:    8px;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    /* ── STANDARDIZED LAYOUT ── */
    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      margin: 0;
      -webkit-font-smoothing: antialiased;
    }
    .page-layout-wrapper { display: flex; min-height: 100vh; }

    /* ── NAMESPACED MAP COMPONENTS ── */
    .challenge-map-main { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }

    /* Topbar */
    .challenge-map-topbar { height: 70px; background: rgba(17, 28, 45, 0.8); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-bottom: 1px solid var(--border); display: flex; align-items: center; padding: 0 32px; gap: 16px; flex-shrink: 0; z-index: 50; }
    .challenge-map-topbar-title { font-weight: 700; font-size: 1.25rem; flex: 1; letter-spacing: -0.01em; color: var(--text); }
    .challenge-map-topbar-title small { color: var(--muted); font-weight: 500; font-size: 0.875rem; margin-left: 8px; background: var(--surface2); padding: 4px 8px; border-radius: 6px; }
    .challenge-map-topbar-btn { padding: 8px 16px; background: transparent; border: 1px solid var(--border); border-radius: var(--radius-sm); color: var(--text); font-size: 0.875rem; font-weight: 500; text-decoration: none; transition: all 0.2s; display: flex; align-items: center; gap: 8px; }
    .challenge-map-topbar-btn:hover { background: var(--surface2); border-color: var(--accent); }

    /* Alert */
    .challenge-map-alert { background: rgba(16,185,129,0.1); border-bottom: 1px solid rgba(16,185,129,0.3); color: var(--accent3); padding: 12px 32px; font-weight: 500; font-size: 0.9rem; display: flex; align-items: center; gap: 10px; flex-shrink: 0; z-index: 40; }

    /* Map Canvas */
    .challenge-map-container { 
        flex: 1; position: relative; overflow: hidden; 
        background-color: var(--bg);
        background-image: 
            radial-gradient(ellipse at 50% 0%, var(--surface2) 0%, transparent 70%),
            linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
        background-size: 100% 100%, 40px 40px, 40px 40px;
    }
    .challenge-map-viewport { width: 100%; height: 100%; overflow: auto; cursor: grab; scroll-behavior: smooth; }
    .challenge-map-viewport:active { cursor: grabbing; }
    .challenge-map-content { position: relative; /* Width/Height applied inline dynamically */ }

    /* SVG Connector Paths */
    .challenge-map-svg-paths { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1; }
    .challenge-map-path-line { fill: none; stroke: var(--border); stroke-width: 4; stroke-dasharray: 12 8; opacity: 0.4; transition: all 0.5s ease; }
    .challenge-map-path-line.passed  { stroke: var(--accent3); stroke-dasharray: none; opacity: 0.8; filter: drop-shadow(0 0 6px rgba(16,185,129,0.4)); }
    
    @keyframes flow { from { stroke-dashoffset: 24; } to { stroke-dashoffset: 0; } }
    .challenge-map-path-line.current { 
        stroke: var(--accent); 
        stroke-dasharray: 12 12; 
        opacity: 1; 
        animation: flow 1s linear infinite; 
        filter: drop-shadow(0 0 8px rgba(59,130,246,0.6)); 
    }

    /* Nodes */
    .challenge-map-node { position: absolute; z-index: 10; display: flex; flex-direction: column; align-items: center; gap: 12px; transform: translate(-50%, -50%); transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .challenge-map-node:hover { z-index: 20; transform: translate(-50%, -52%) scale(1.02); }

    .challenge-map-node-icon-wrap { position: relative; }
    .challenge-map-node-icon { width: 76px; height: 76px; border-radius: 50%; background: var(--surface); border: 4px solid var(--border); display: flex; align-items: center; justify-content: center; color: var(--dim); box-shadow: 0 8px 24px rgba(0,0,0,0.5); transition: all 0.4s ease; z-index: 2; position: relative; }
    
    .challenge-map-node.state-completed .challenge-map-node-icon { background: rgba(16,185,129,0.15); border-color: var(--accent3); color: var(--accent3); box-shadow: 0 0 30px rgba(16,185,129,0.25); }
    .challenge-map-node.state-active    .challenge-map-node-icon { background: rgba(59,130,246,0.15); border-color: var(--accent); color: var(--accent); box-shadow: 0 0 40px rgba(59,130,246,0.4); animation: pulse-ring 2.5s infinite; }
    .challenge-map-node.state-failed    .challenge-map-node-icon { background: rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.5); color: #ef4444; }
    .challenge-map-node.state-locked    .challenge-map-node-icon { opacity: 0.5; background: var(--bg); }

    .challenge-map-node-badge { position: absolute; top: -6px; right: -6px; background: var(--accent3); color: #fff; font-size: 0.65rem; font-weight: 700; padding: 4px 8px; border-radius: 12px; border: 3px solid var(--surface); display: none; z-index: 3; letter-spacing: 0.05em; box-shadow: 0 4px 10px rgba(0,0,0,0.3); }
    .challenge-map-node.state-completed .challenge-map-node-badge { display: flex; align-items: center; gap: 4px; }

    /* Info Cards */
    .challenge-map-node-info { 
        background: rgba(17, 28, 45, 0.65); 
        backdrop-filter: blur(12px); 
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.08); 
        padding: 18px; 
        border-radius: var(--radius); 
        text-align: center; 
        width: 240px; 
        opacity: 0.85; 
        transition: all 0.3s ease; 
        pointer-events: auto; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.4); 
    }
    .challenge-map-node:hover .challenge-map-node-info { opacity: 1; border-color: rgba(255,255,255,0.15); box-shadow: 0 15px 40px rgba(0,0,0,0.5); }
    .challenge-map-node.state-active    .challenge-map-node-info { opacity: 1; border-color: rgba(59,130,246,0.4); background: rgba(17, 28, 45, 0.8); }
    .challenge-map-node.state-completed .challenge-map-node-info { border-color: rgba(16,185,129,0.2); }
    .challenge-map-node.state-failed    .challenge-map-node-info { opacity: 1; border-color: rgba(239,68,68,0.3); }

    .challenge-map-node-number { font-size: 0.7rem; font-weight: 700; color: var(--accent); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 6px; }
    .challenge-map-node.state-completed .challenge-map-node-number { color: var(--accent3); }
    .challenge-map-node.state-locked .challenge-map-node-number { color: var(--dim); }
    
    .challenge-map-node-title  { font-size: 0.95rem; font-weight: 600; color: var(--text); margin-bottom: 8px; line-height: 1.4; letter-spacing: -0.01em; }
    .challenge-map-node-xp     { font-size: 0.8rem; color: var(--warn); font-weight: 700; margin-bottom: 12px; display: inline-flex; align-items: center; gap: 4px; background: rgba(245, 158, 11, 0.1); padding: 4px 10px; border-radius: 20px; }
    .challenge-map-node-score  { font-size: 0.75rem; color: var(--muted); margin-bottom: 12px; font-weight: 500; }
    .challenge-map-node-status { font-size: 0.75rem; color: var(--dim); margin-top: 4px; font-weight: 500; }

    /* Buttons */
    .challenge-map-btn-start {
      display: flex; align-items: center; justify-content: center; gap: 6px;
      width: 100%; padding: 10px 14px; 
      background: linear-gradient(135deg, var(--accent), #2563eb); 
      color: #fff; text-decoration: none; font-size: 0.85rem; font-weight: 600;
      border-radius: var(--radius-sm); transition: all 0.2s;
      box-shadow: 0 4px 12px rgba(59,130,246,0.3); border: 1px solid rgba(255,255,255,0.1);
    }
    .challenge-map-btn-start:hover { background: linear-gradient(135deg, #4f93f7, var(--accent)); transform: translateY(-2px); box-shadow: 0 6px 16px rgba(59,130,246,0.4); }

    .challenge-map-btn-retry {
      display: flex; align-items: center; justify-content: center; gap: 6px;
      width: 100%; padding: 10px 14px;
      background: rgba(255,255,255,0.03); border: 1px solid var(--border);
      color: var(--text); text-decoration: none; font-size: 0.85rem; font-weight: 600;
      border-radius: var(--radius-sm); transition: all 0.2s; margin-top: 8px;
    }
    .challenge-map-btn-retry:hover { background: rgba(255,255,255,0.08); border-color: var(--muted); transform: translateY(-1px); }

    @keyframes pulse-ring {
      0%   { box-shadow: 0 0 0 0   rgba(59,130,246,0.6); }
      70%  { box-shadow: 0 0 0 20px rgba(59,130,246,0);   }
      100% { box-shadow: 0 0 0 0   rgba(59,130,246,0);   }
    }

    /* ── LEGEND ── */
    .challenge-map-legend { position: absolute; bottom: 32px; left: 32px; background: rgba(17, 28, 45, 0.7); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.08); border-radius: 100px; padding: 12px 24px; display: flex; gap: 24px; z-index: 50; pointer-events: none; box-shadow: 0 8px 24px rgba(0,0,0,0.4); }
    .challenge-map-legend-item { display: flex; align-items: center; gap: 8px; font-size: 0.75rem; color: var(--text); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
    .challenge-map-legend-dot { width: 12px; height: 12px; border-radius: 50%; border: 2px solid; flex-shrink: 0; }
    .challenge-map-legend-dot.passed  { background: rgba(16,185,129,0.2); border-color: var(--accent3); box-shadow: 0 0 8px var(--accent3); }
    .challenge-map-legend-dot.current { background: rgba(59,130,246,0.2); border-color: var(--accent); box-shadow: 0 0 8px var(--accent); }
    .challenge-map-legend-dot.failed  { background: rgba(239,68,68,0.1); border-color: #ef4444; }
    .challenge-map-legend-dot.locked  { background: var(--surface2); border-color: var(--border); }

    /* Scrollbars */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: var(--surface2); border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--dim); }

    @media (max-width: 768px) {
      .challenge-map-topbar { padding: 0 16px; }
      .challenge-map-legend { bottom: 16px; left: 50%; transform: translateX(-50%); width: 90%; justify-content: center; gap: 12px; padding: 12px; border-radius: var(--radius-sm); flex-wrap: wrap; }
    }
  </style>
</head>
<body>

<div class="page-layout-wrapper">
  @include('partials.sidebar')

  <div class="challenge-map-main">

    <div class="challenge-map-topbar">
      <h1 class="challenge-map-topbar-title">
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
        <div class="challenge-map-alert" style="background:rgba(245,158,11,0.12); border-bottom-color:rgba(245,158,11,0.35); color:#fbbf24;">
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
                  <svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                  </svg>
                  Passed
                </div>
              </div>

              <div class="challenge-map-node-info">
                <div class="challenge-map-node-number">Module {{ $i + 1 }}</div>
                <div class="challenge-map-node-title">{{ $ch->title }}</div>
                
                <div class="challenge-map-node-xp">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    {{ $ch->base_xp }} XP
                </div>

                @if($best)
                  <div class="challenge-map-node-score">
                    Best: {{ $best['score'] }}/{{ $totalQ }}
                    ({{ $totalQ > 0 ? round(($best['score'] / $totalQ) * 100) : 0 }}%)
                  </div>
                @endif

                @if($state === 'completed')
                  <div class="challenge-map-node-status" style="color: var(--accent3); margin-bottom: 8px;">✅ Challenge Completed</div>
                  <a href="{{ route('challenges.quiz', ['slug' => $slug, 'challenge' => $ch->id]) }}" class="challenge-map-btn-retry">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Retry for Better Score
                  </a>

                @elseif($state === 'failed')
                  <div class="challenge-map-node-status" style="color: #ef4444; margin-bottom: 8px;">❌ Needs 70% to Pass</div>
                  <a href="{{ route('challenges.quiz', ['slug' => $slug, 'challenge' => $ch->id]) }}" class="challenge-map-btn-start">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Try Again
                  </a>

                @elseif($state === 'active')
                  <a href="{{ route('challenges.quiz', ['slug' => $slug, 'challenge' => $ch->id]) }}" class="challenge-map-btn-start">
                    Start Challenge
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                  </a>

                @else
                  <div class="challenge-map-node-status">🔒 Complete prior modules to unlock</div>
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