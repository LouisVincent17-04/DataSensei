<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DataSensei — Challenge Map</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <script>
    window.USER_ORG_ID = @json(auth()->check() ? auth()->user()->organization_id : null);
</script>
  <style>
    :root {
      /* DataSensei Original Core Palette */
      --bg:           #0d1320;
      --surface:      #111c2d;
      --surface2:     #1a2638;
      --border:       #1e2f47;
      --border-hover: #2c4168;
      
      /* Gamification Accents */
      --accent:       #3b82f6; /* Blue - Active */
      --accent-hover: #2563eb;
      --accent2:      #8b5cf6; /* Purple - Epic/Boss */
      --accent3:      #10b981; /* Green - Completed */
      --warn:         #ef4444; /* Red - Hard/Danger */
      --warn2:        #f59e0b; /* Orange - Medium */
      
      /* Typography */
      --text:         #fafafa;
      --muted:        #7f93b0;
      --dim:          #3d5272;
      
      /* Geometry */
      --radius:       8px;
      --radius-sm:    6px;
      --topbar-h:     64px;
      --sidebar-w:    260px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      overflow-x: hidden;
      -webkit-font-smoothing: antialiased;
    }

    /* ── SIDEBAR STYLES (Required for the include to format properly) ── */
    .sidebar { width: var(--sidebar-w); background: var(--surface); border-right: 1px solid var(--border); display: flex; flex-direction: column; flex-shrink: 0; height: 100vh; position: sticky; top: 0; }
    .sidebar-logo { padding: 24px; border-bottom: 1px solid var(--border); }
    .sidebar-logo .wordmark { font-weight: 700; font-size: 1.25rem; color: var(--text); display: flex; align-items: center; gap: 8px; }
    .sidebar-logo .wordmark span { color: var(--accent); }
    .sidebar-logo .tagline { font-size: 0.75rem; color: var(--muted); margin-top: 4px; font-weight: 500; }
    .nav-group { padding: 24px 16px 0; }
    .nav-label { font-size: 0.75rem; font-weight: 600; color: var(--dim); letter-spacing: 0.05em; text-transform: uppercase; padding: 0 12px; margin-bottom: 8px; }
    .nav-item { display: flex; align-items: center; gap: 12px; padding: 8px 12px; border-radius: var(--radius-sm); cursor: pointer; font-size: 0.875rem; font-weight: 500; color: var(--muted); text-decoration: none; margin-bottom: 2px; transition: all 0.15s; }
    .nav-item:hover { background: var(--surface2); color: var(--text); }
    .nav-item.active { background: var(--surface2); color: var(--text); border-left: 3px solid var(--accent); border-radius: 0 var(--radius-sm) var(--radius-sm) 0; }
    .nav-item .icon { width: 18px; height: 18px; color: inherit; }
    .sidebar-footer { margin-top: auto; padding: 16px; border-top: 1px solid var(--border); display: flex; flex-direction: column; gap: 8px; }
    .user-card { display: flex; align-items: center; gap: 12px; padding: 8px; border-radius: var(--radius-sm); cursor: pointer; transition: background 0.15s; }
    .user-card:hover { background: var(--surface2); }
    .user-card .avatar { width: 36px; height: 36px; border-radius: var(--radius-sm); background: var(--surface2); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.875rem; }
    .user-card .name { font-size: 0.875rem; font-weight: 600; }
    .user-card .role { font-size: 0.75rem; color: var(--muted); }
    
    .logout-form { width: 100%; }
    .logout-btn { display: flex; align-items: center; gap: 10px; width: 100%; padding: 8px 12px; border-radius: var(--radius-sm); background: transparent; border: 1px solid var(--border); color: var(--muted); font-size: 0.875rem; font-weight: 500; font-family: 'Inter', sans-serif; cursor: pointer; transition: all 0.15s ease; text-align: left; }
    .logout-btn:hover { background: rgba(239,68,68,0.08); border-color: rgba(239,68,68,0.3); color: var(--warn); }

    /* ── BADGE FIX ── */
    .badge {
      margin-left: auto;
      background: var(--surface2);
      border: 1px solid var(--border);
      color: var(--text);
      font-size: 0.7rem;
      font-weight: 600;
      padding: 2px 8px;
      border-radius: 12px;
    }

    /* ── MAIN APP AREA ── */
    .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; position: relative; }

    /* ── TOPBAR ── */
    .topbar { height: var(--topbar-h); background: var(--surface); border-bottom: 1px solid var(--border); display: flex; align-items: center; padding: 0 32px; gap: 16px; flex-shrink: 0; z-index: 10; }
    .topbar h1 { font-size: 1.125rem; font-weight: 600; color: var(--text); flex: 1; }
    .stats-pill { display: flex; align-items: center; gap: 12px; background: var(--surface2); border: 1px solid var(--border); padding: 6px 14px; border-radius: 20px; font-size: 0.8125rem; font-weight: 600; }
    .stats-pill span { color: var(--accent2); }

    /* ── MAP CONTAINER (Background Pattern) ── */
    .map-viewport {
      flex: 1;
      overflow-y: auto;
      overflow-x: hidden;
      position: relative;
      background-image: 
        linear-gradient(to right, rgba(30, 47, 71, 0.4) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(30, 47, 71, 0.4) 1px, transparent 1px);
      background-size: 40px 40px;
    }

    /* ── MAP HEADER ── */
    .map-header {
      text-align: center;
      padding: 60px 20px 20px;
    }
    .map-header h2 { font-size: 2rem; font-weight: 700; letter-spacing: -0.02em; }
    .map-header p { color: var(--muted); margin-top: 8px; font-size: 0.9rem; max-width: 600px; margin-inline: auto; line-height: 1.5; }

    /* ── THE METRO-STYLE TIMELINE MAP ── */
    .map-system {
      position: relative;
      max-width: 1000px;
      margin: 40px auto 100px;
      padding: 0 20px;
    }

    /* Central Track Line */
    .map-track {
      position: absolute;
      top: 0; bottom: 0; left: 50%;
      width: 4px;
      background: var(--border);
      transform: translateX(-50%);
      border-radius: 4px;
      z-index: 1;
    }
    /* Fill line showing progress */
    .map-track-fill {
      position: absolute;
      top: 0; left: 0; width: 100%;
      height: 2%; 
      background: var(--accent);
      border-radius: 4px;
      box-shadow: 0 0 12px rgba(59, 130, 246, 0.4);
    }

    /* Individual Map Rows */
    .map-node-row {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 80px;
      position: relative;
      z-index: 2;
      width: 100%;
    }
    .map-node-row:last-child { margin-bottom: 0; }

    /* Columns for staggering Left/Right */
    .map-col { width: 50%; padding: 0 40px; position: relative; }
    .map-col.empty { padding: 0; }
    .map-col.left  { display: flex; justify-content: flex-end; text-align: right; }
    .map-col.right { display: flex; justify-content: flex-start; text-align: left; }

    /* The glowing intersection marker */
    .node-marker {
      position: absolute;
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      width: 24px; height: 24px;
      border-radius: 50%;
      background: var(--bg);
      border: 4px solid var(--border);
      display: flex; align-items: center; justify-content: center;
      transition: all 0.3s;
      z-index: 10;
    }
    .node-marker svg { width: 12px; height: 12px; display: none; }

    /* ── NODE STATES ── */
    /* Active (Current) */
    @keyframes pulseActive {
      0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
      70% { box-shadow: 0 0 0 15px rgba(59, 130, 246, 0); }
      100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
    }
    .map-node-row.active .node-marker {
      width: 32px; height: 32px;
      border-color: var(--accent);
      border-width: 5px;
      animation: pulseActive 2s infinite;
    }
    .map-node-row.active .node-marker::after {
      content: ''; width: 10px; height: 10px; background: var(--accent); border-radius: 50%;
    }

    /* Boss / Milestone Node */
    .map-node-row.boss .node-marker {
      width: 36px; height: 36px;
      border-radius: 8px;
      transform: translate(-50%, -50%) rotate(45deg);
      border-color: var(--border);
    }
    .map-node-row.boss .node-marker > * { transform: rotate(-45deg); }
    .map-node-row.boss.locked .node-marker { background: var(--surface2); }
    .map-node-row.boss.active .node-marker { background: var(--accent2); border-color: var(--accent2); box-shadow: 0 0 20px rgba(139, 92, 246, 0.4); }

    /* Horizontal connection lines from marker to card */
    .map-col.left .node-card::after {
      content: ''; position: absolute; top: 50%; right: -40px; width: 40px; height: 2px; background: var(--border); transform: translateY(-50%); z-index: -1;
    }
    .map-col.right .node-card::after {
      content: ''; position: absolute; top: 50%; left: -40px; width: 40px; height: 2px; background: var(--border); transform: translateY(-50%); z-index: -1;
    }
    .map-node-row.active .map-col.left .node-card::after,
    .map-node-row.active .map-col.right .node-card::after { background: var(--accent); }

    /* ── THE NODE CARD ── */
    .node-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 20px;
      width: 100%;
      max-width: 380px;
      position: relative;
      cursor: pointer;
      transition: all 0.25s ease;
      text-align: left;
    }

    .node-card:hover { border-color: var(--border-hover); transform: translateY(-4px); box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
    
    .map-node-row.active .node-card { border-color: var(--accent); box-shadow: 0 0 20px rgba(59, 130, 246, 0.15); }
    .map-node-row.active .node-card:hover { box-shadow: 0 10px 30px rgba(59, 130, 246, 0.25); }

    .map-node-row.boss .node-card { border-color: var(--dim); background: linear-gradient(145deg, var(--surface), var(--surface2)); }

    .map-node-row.locked .node-card { opacity: 0.6; cursor: not-allowed; }
    .map-node-row.locked .node-card:hover { transform: none; box-shadow: none; border-color: var(--border); }

    /* Card Content */
    .card-meta { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
    .year-badge { font-size: 0.65rem; font-weight: 700; padding: 4px 8px; border-radius: 4px; letter-spacing: 0.05em; text-transform: uppercase; background: var(--surface2); color: var(--muted); border: 1px solid var(--border); }
    .diff-boss { background: rgba(139, 92, 246, 0.15); color: var(--accent2); border: 1px solid rgba(139, 92, 246, 0.3); }

    .pts-badge { font-size: 0.75rem; font-weight: 600; color: var(--accent); font-family: 'JetBrains Mono', monospace; }
    .map-node-row.locked .pts-badge { color: var(--muted); }

    .card-title { font-size: 1rem; font-weight: 600; color: var(--text); line-height: 1.4; margin-bottom: 6px; }
    .card-desc { font-size: 0.8125rem; color: var(--muted); line-height: 1.5; }

    /* Tags inside card */
    .card-tags { display: flex; gap: 6px; margin-top: 16px; flex-wrap: wrap; }
    .card-tag { font-size: 0.65rem; color: var(--dim); border: 1px solid var(--border); padding: 2px 6px; border-radius: 4px; background: var(--bg); }

    /* Lock Icon Overlay */
    .lock-icon { position: absolute; top: 20px; right: 20px; color: var(--dim); }
    .map-node-row.active .lock-icon { display: none; }

    /* ── SIDE BRIEFING PANEL (Gamified Drawer) ── */
    .briefing-panel {
      position: absolute;
      top: 0; right: 0; bottom: 0;
      width: 450px;
      background: var(--surface);
      border-left: 1px solid var(--border);
      box-shadow: -10px 0 30px rgba(0,0,0,0.5);
      z-index: 100;
      transform: translateX(100%);
      transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      display: flex;
      flex-direction: column;
    }
    .briefing-panel.open { transform: translateX(0); }

    .bp-header { padding: 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: flex-start; }
    .bp-title { font-size: 1.25rem; font-weight: 700; color: var(--text); margin-bottom: 8px; line-height: 1.3;}
    .bp-close { background: transparent; border: none; color: var(--muted); cursor: pointer; transition: color 0.15s; }
    .bp-close:hover { color: var(--text); }

    .bp-body { padding: 24px; flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 24px; }
    
    .bp-section h4 { font-size: 0.75rem; text-transform: uppercase; color: var(--dim); letter-spacing: 0.05em; margin-bottom: 12px; font-weight: 600; }
    .bp-lore { font-size: 0.875rem; color: var(--muted); line-height: 1.6; padding-left: 12px; border-left: 2px solid var(--accent); font-style: italic; }
    
    .bp-tasks { display: flex; flex-direction: column; gap: 10px; }
    .bp-task { display: flex; gap: 10px; font-size: 0.875rem; color: var(--text); line-height: 1.4; }
    .bp-task svg { color: var(--accent); flex-shrink: 0; margin-top: 2px; }

    .bp-footer { padding: 24px; border-top: 1px solid var(--border); background: var(--bg); }
    .btn-launch { width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; background: var(--accent); color: #fff; border: none; padding: 12px; border-radius: var(--radius); font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.15s; font-family: inherit; }
    .btn-launch:hover { background: var(--accent-hover); }

    /* Overlay background when panel is open */
    .panel-overlay {
      position: absolute; inset: 0; background: rgba(0,0,0,0.4); backdrop-filter: blur(2px);
      z-index: 90; opacity: 0; pointer-events: none; transition: opacity 0.3s;
    }
    .panel-overlay.open { opacity: 1; pointer-events: auto; }

    /* ── RESPONSIVE COMPACTION ── */
    @media (max-width: 900px) {
      .map-track { left: 40px; }
      .map-col { width: 100%; padding: 0 0 0 80px; }
      .map-col.empty { display: none; }
      .node-marker { left: 40px; }
      .map-col.left .node-card::after, .map-col.right .node-card::after { left: -40px; width: 40px; right: auto; }
      .map-col.left { text-align: left; justify-content: flex-start; }
      .briefing-panel { width: 100%; }
    }
    @media (max-width: 700px) { .sidebar { display: none; } }
  </style>
</head>
<body>

  @php
    $modules = [
        // Year 1
        ['title' => 'Basics of Python Programming', 'desc' => 'Variables, data types, control flow, functions, and file I/O.', 'xp' => 600, 'year' => 'Year 1', 'boss' => false, 'tags' => ['variables', 'loops', 'functions']],
        ['title' => 'Basics of Statistics', 'desc' => 'Descriptive statistics, probability fundamentals, and distributions.', 'xp' => 500, 'year' => 'Year 1', 'boss' => false, 'tags' => ['probability', 'mean', 'variance']],
        ['title' => 'Introduction to Data Science', 'desc' => 'Overview of the data science lifecycle, tools, and real-world applications.', 'xp' => 400, 'year' => 'Year 1', 'boss' => false, 'tags' => ['lifecycle', 'tools', 'industry']],
        ['title' => 'Mathematical Analysis I', 'desc' => 'Limits, continuity, differentiation, and integration.', 'xp' => 800, 'year' => 'Year 1', 'boss' => false, 'tags' => ['calculus', 'derivatives', 'integrals']],
        ['title' => 'Methods of Proof', 'desc' => 'Logic, set theory, mathematical induction, and proof techniques.', 'xp' => 500, 'year' => 'Year 1', 'boss' => false, 'tags' => ['logic', 'set theory', 'induction']],
        ['title' => 'Modeling and Simulation', 'desc' => 'Building computational models, discrete event simulation, and Monte Carlo.', 'xp' => 1200, 'year' => 'Year 1', 'boss' => true, 'tags' => ['monte carlo', 'simulation', 'systems']],
        
        // Year 2
        ['title' => 'Algorithms & Data Structures', 'desc' => 'Arrays, trees, graphs, sorting, searching, and complexity analysis.', 'xp' => 800, 'year' => 'Year 2', 'boss' => false, 'tags' => ['Big-O', 'graphs', 'trees']],
        ['title' => 'Statistical Methods & Design', 'desc' => 'ANOVA, regression analysis, experimental design, and sampling.', 'xp' => 700, 'year' => 'Year 2', 'boss' => false, 'tags' => ['ANOVA', 'regression', 'sampling']],
        ['title' => 'Applied Matrix Analysis', 'desc' => 'Linear algebra — vectors, eigenvalues, SVD, PCA, and decomposition.', 'xp' => 700, 'year' => 'Year 2', 'boss' => false, 'tags' => ['linear algebra', 'PCA', 'eigenvalues']],
        ['title' => 'Database Management', 'desc' => 'Relational databases, SQL querying, NoSQL, and schema design.', 'xp' => 500, 'year' => 'Year 2', 'boss' => false, 'tags' => ['SQL', 'NoSQL', 'schemas']],
        ['title' => 'Intro to Bayesian Analysis', 'desc' => 'Bayes theorem, prior/posterior distributions, and MCMC sampling.', 'xp' => 600, 'year' => 'Year 2', 'boss' => false, 'tags' => ['Bayes', 'MCMC', 'inference']],
        ['title' => 'Introductory Forecasting', 'desc' => 'Time series analysis, trend decomposition, and ARIMA.', 'xp' => 1500, 'year' => 'Year 2', 'boss' => true, 'tags' => ['ARIMA', 'time series', 'trends']],

        // Year 3
        ['title' => 'Intro to Optimization', 'desc' => 'Gradient descent, convex optimization, and linear programming.', 'xp' => 600, 'year' => 'Year 3', 'boss' => false, 'tags' => ['gradient descent', 'convex', 'LP']],
        ['title' => 'Machine Learning 1: Supervised', 'desc' => 'Logistic regression, decision trees, SVMs, and ensemble methods.', 'xp' => 1000, 'year' => 'Year 3', 'boss' => false, 'tags' => ['SVM', 'Random Forest', 'classification']],
        ['title' => 'Data Visualization', 'desc' => 'Principles of visual encoding, interactive charts, and storytelling.', 'xp' => 500, 'year' => 'Year 3', 'boss' => false, 'tags' => ['matplotlib', 'seaborn', 'dashboards']],
        ['title' => 'Multivariate Analysis', 'desc' => 'Factor analysis, cluster analysis, and discriminant analysis.', 'xp' => 600, 'year' => 'Year 3', 'boss' => false, 'tags' => ['clustering', 'factor analysis']],
        ['title' => 'Deep Learning', 'desc' => 'Neural networks, CNNs, RNNs, transformers, and backpropagation.', 'xp' => 1200, 'year' => 'Year 3', 'boss' => false, 'tags' => ['neural networks', 'PyTorch', 'CNNs']],
        ['title' => 'Privacy, Ethics & Governance', 'desc' => 'Data privacy laws, algorithmic fairness, and responsible data use.', 'xp' => 1800, 'year' => 'Year 3', 'boss' => true, 'tags' => ['GDPR', 'bias', 'ethics']],

        // Year 4
        ['title' => 'Intro to Artificial Intelligence', 'desc' => 'Search algorithms, knowledge representation, and planning.', 'xp' => 800, 'year' => 'Year 4', 'boss' => false, 'tags' => ['search', 'A*', 'heuristics']],
        ['title' => 'Analysis of Unstructured Data', 'desc' => 'Text mining, NLP, sentiment analysis, and image processing.', 'xp' => 700, 'year' => 'Year 4', 'boss' => false, 'tags' => ['NLP', 'text mining', 'CV']],
        ['title' => 'Machine Learning 2: Unsupervised', 'desc' => 'Clustering, anomaly detection, GANs, and autoencoders.', 'xp' => 900, 'year' => 'Year 4', 'boss' => false, 'tags' => ['GANs', 'autoencoders', 'anomaly']],
        ['title' => 'Big Data & Cloud Computing', 'desc' => 'Hadoop, Spark, distributed computing, and cloud platforms.', 'xp' => 700, 'year' => 'Year 4', 'boss' => false, 'tags' => ['Spark', 'AWS', 'distributed']],
        ['title' => 'Data Warehousing', 'desc' => 'Star/snowflake schemas, ETL pipelines, and OLAP cubes.', 'xp' => 500, 'year' => 'Year 4', 'boss' => false, 'tags' => ['ETL', 'OLAP', 'star schema']],
        ['title' => 'Sequential Decision Making', 'desc' => 'Markov decision processes, reinforcement learning, and Q-learning.', 'xp' => 2500, 'year' => 'Year 4', 'boss' => true, 'tags' => ['RL', 'MDP', 'Q-learning']],
    ];
  @endphp

  @include('partials.sidebar')

  <div class="main">
    
    <header class="topbar">
      <h1>Curriculum Expedition</h1>
      <div class="stats-pill" title="Total EXP Earned">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--accent2)" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        0 XP
      </div>
      <div class="stats-pill" title="Global Rank">
        <span>#--</span> Global Rank
      </div>
    </header>

    <div class="map-viewport">
      
      <div class="map-header">
        <h2>The DataSensei Roadmap</h2>
        <p>Complete the entire 4-year Data Science curriculum. Unlock modules, earn experience points, and master the algorithms.</p>
      </div>

      <div class="map-system">
        
        <div class="map-track">
          <div class="map-track-fill"></div>
        </div>

        @foreach($modules as $index => $module)
          @php
            $isLeft = $index % 2 == 0;
            $statusClass = $index === 0 ? 'active' : 'locked';
            $bossClass   = $module['boss'] ? 'boss' : '';
          @endphp

          <div class="map-node-row {{ $statusClass }} {{ $bossClass }}">
            
            <div class="node-marker">
              @if($statusClass === 'locked')
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:block; width:12px; color:var(--dim)"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
              @endif
            </div>

            <div class="map-col {{ $isLeft ? 'left' : 'empty' }}">
              @if($isLeft)
                <div class="node-card" @if($statusClass === 'active') onclick="openBriefing({{ $index }}, '{{ addslashes($module['title']) }}')" @endif>
                  <svg class="lock-icon" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                  <div class="card-meta">
                    <span class="year-badge {{ $module['boss'] ? 'diff-boss' : '' }}">{{ $module['boss'] ? 'MILESTONE' : $module['year'] }}</span>
                    <span class="pts-badge">+{{ $module['xp'] }} XP</span>
                  </div>
                  <div class="card-title">{{ $module['title'] }}</div>
                  <div class="card-desc">{{ $module['desc'] }}</div>
                  <div class="card-tags">
                    @foreach($module['tags'] as $tag)
                      <span class="card-tag">{{ $tag }}</span>
                    @endforeach
                  </div>
                </div>
              @endif
            </div>

            <div class="map-col {{ !$isLeft ? 'right' : 'empty' }}">
              @if(!$isLeft)
                <div class="node-card" @if($statusClass === 'active') onclick="openBriefing({{ $index }}, '{{ addslashes($module['title']) }}')" @endif>
                  <svg class="lock-icon" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                  <div class="card-meta">
                    <span class="year-badge {{ $module['boss'] ? 'diff-boss' : '' }}">{{ $module['boss'] ? 'MILESTONE' : $module['year'] }}</span>
                    <span class="pts-badge">+{{ $module['xp'] }} XP</span>
                  </div>
                  <div class="card-title">{{ $module['title'] }}</div>
                  <div class="card-desc">{{ $module['desc'] }}</div>
                  <div class="card-tags">
                    @foreach($module['tags'] as $tag)
                      <span class="card-tag">{{ $tag }}</span>
                    @endforeach
                  </div>
                </div>
              @endif
            </div>

          </div>
        @endforeach

      </div>
    </div> <div class="panel-overlay" id="panelOverlay" onclick="closeBriefing()"></div>

    <div class="briefing-panel" id="briefingPanel">
      <div class="bp-header">
        <div>
          <span class="year-badge" style="margin-bottom:8px; display:inline-block;" id="bp-badge">ACTIVE MODULE</span>
          <h3 class="bp-title" id="bp-title">Module Title</h3>
          <div class="pts-badge" style="font-size: 0.85rem;" id="bp-xp">Reward: +??? XP</div>
        </div>
        <button class="bp-close" onclick="closeBriefing()">
          <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      
      <div class="bp-body">
        <div class="bp-section">
          <h4>System Initialization</h4>
          <p class="bp-lore" id="bp-lore">
            "Welcome to DataSensei. Before we can analyze complex datasets or build predictive models, you need to understand the fundamental language of the system. Your first mission is to master Python logic."
          </p>
        </div>

        <div class="bp-section">
          <h4>Module Objectives</h4>
          <div class="bp-tasks">
            <div class="bp-task">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/></svg>
              Master variables and primitive data types.
            </div>
            <div class="bp-task">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/></svg>
              Implement logical control flow (if/else, loops).
            </div>
            <div class="bp-task">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/></svg>
              Write reusable functional blocks.
            </div>
          </div>
        </div>
      </div>

      <div class="bp-footer">
        <button class="btn-launch" onclick="window.location.href='#'">
          <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
          Enter Learning Environment
        </button>
      </div>
    </div>

  </div>

  <script>
    const panel = document.getElementById('briefingPanel');
    const overlay = document.getElementById('panelOverlay');

    function openBriefing(index, title) {
      document.getElementById('bp-title').textContent = title;
      
      panel.classList.add('open');
      overlay.classList.add('open');
    }

    function closeBriefing() {
      panel.classList.remove('open');
      overlay.classList.remove('open');
    }
  </script>
</body>
</html>