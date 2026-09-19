@php
  $displayNumeric = array_slice($overview['numeric_columns'], 0, 6);
  $displayCategorical = [];
  foreach ($overview['categorical'] as $column => $summary) {
      $type = $overview['eda']['data_types'][$column]['type'] ?? 'Text';
      if ($type !== 'Identifier' && ($summary['unique_count'] ?? 0) > 0 && ($summary['unique_count'] ?? 0) <= 20) {
          $displayCategorical[] = $column;
      }
      if (count($displayCategorical) >= 6) break;
  }
  $chartPayload = [
      'numeric' => array_map(fn ($column) => [
          'column' => $column,
          'labels' => $overview['eda']['histograms'][$column]['labels'] ?? [],
          'values' => $overview['eda']['histograms'][$column]['values'] ?? [],
      ], $displayNumeric),
      'categorical' => array_map(fn ($column) => [
          'column' => $column,
          'labels' => array_slice(array_column($overview['categorical'][$column]['distribution'] ?? [], 'label'), 0, 10),
          'values' => array_slice(array_column($overview['categorical'][$column]['distribution'] ?? [], 'count'), 0, 10),
      ], $displayCategorical),
      'scatter' => $overview['regression'] ? [
          'x' => $overview['regression']['x_column'],
          'y' => $overview['regression']['y_column'],
          'points' => array_slice($overview['regression']['points'] ?? [], 0, 300),
      ] : null,
      'group' => $overview['group_comparison'] ? [
          'category' => $overview['group_comparison']['category_column'],
          'numeric' => $overview['group_comparison']['numeric_column'],
          'labels' => array_column($overview['group_comparison']['groups'], 'label'),
          'values' => array_column($overview['group_comparison']['groups'], 'mean'),
      ] : null,
  ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $overview['dataset']['title'] }} — Guided EDA</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
  <style>
    /* Guided EDA workspace (this page and the eda-panel partial). Colours, type
       and radius come from partials.design-system. */
    :root{--good:var(--ds-success);--warn:var(--ds-warning);--danger:var(--ds-danger)}
    *{box-sizing:border-box}
    body{margin:0;background:var(--bg);color:var(--text);font-family:var(--ds-font-sans)}
    .layout{display:flex;min-height:100vh}
    .content{flex:1;min-width:0;padding:28px 32px 48px}
    .page{max-width:1180px;margin:0 auto}

    /* ── page header ───────────────────────────────────────────── */
    .topbar{display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px;margin-bottom:24px}
    .topbar > div{min-width:0;flex:1 1 320px}
    .topbar h1{overflow-wrap:anywhere}
    .topbar p{margin:4px 0 0;color:var(--muted);font-size:.875rem;line-height:1.5;font-variant-numeric:tabular-nums}
    .exit-link{display:inline-flex;align-items:center;justify-content:center;min-height:38px;padding:0 16px;flex-shrink:0;
      border:1px solid var(--ds-border-strong);border-radius:var(--radius-sm);background:var(--surface2);color:var(--text);
      font-size:.875rem;font-weight:500;line-height:1.2;text-decoration:none;white-space:nowrap;transition:background .12s ease}
    .exit-link:hover{background:var(--ds-surface-hover)}

    /* ── roadmap: eight steps in one hairline strip ─────────────── */
    .roadmap{display:grid;grid-template-columns:repeat(8,minmax(0,1fr));gap:1px;margin-bottom:20px;
      border:1px solid var(--border);border-radius:var(--radius);background:var(--border);overflow:hidden}
    .roadmap-item{position:relative;display:flex;flex-direction:column;align-items:flex-start;gap:6px;min-width:0;padding:10px 12px;
      background:var(--surface);color:var(--dim)}
    .roadmap-item .number{display:inline-grid;place-items:center;flex:0 0 22px;width:22px;height:22px;border:1px solid var(--ds-border-strong);
      border-radius:var(--radius-xs);background:var(--surface2);color:var(--muted);font-size:.75rem;font-weight:600;
      font-variant-numeric:tabular-nums}
    .roadmap-item strong{min-width:0;font-size:.8125rem;font-weight:500;line-height:1.3;overflow-wrap:break-word}
    .roadmap-item a{display:flex;flex-direction:inherit;align-items:inherit;gap:inherit;flex:1 1 auto;min-width:0;align-self:stretch;margin:-10px -12px;padding:10px 12px;
      color:inherit;text-decoration:none;transition:background .12s ease,color .12s ease}
    .roadmap-item a:hover{background:var(--surface2);color:var(--text)}
    .roadmap-item.completed{color:var(--ds-text-secondary)}
    .roadmap-item.completed .number{border-color:var(--ds-success-border);background:var(--ds-success-soft);color:var(--ds-success-text)}
    .roadmap-item.current{background:var(--ds-accent-soft);color:var(--text);box-shadow:inset 0 -2px 0 var(--accent)}
    .roadmap-item.current strong{font-weight:600}
    .roadmap-item.current .number{border-color:var(--accent);background:var(--accent);color:#fff}

    .flash{margin-bottom:16px;padding:12px 16px;border:1px solid var(--ds-success-border);border-radius:var(--radius-sm);
      background:var(--ds-success-soft);color:#d1fae5;font-size:.875rem;line-height:1.5}
    .flash.error{border-color:var(--ds-danger-border);background:var(--ds-danger-soft);color:#fee2e2}

    /* ── step card ─────────────────────────────────────────────── */
    .step-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden}
    .step-head{display:flex;gap:12px;align-items:flex-start;padding:16px 20px;border-bottom:1px solid var(--border)}
    .step-head > div{min-width:0}
    .step-number{display:grid;place-items:center;flex:0 0 32px;height:32px;border:1px solid var(--ds-accent-border);border-radius:var(--radius-sm);
      background:var(--ds-accent-soft);color:var(--ds-accent-text);font-size:.875rem;font-weight:600;font-variant-numeric:tabular-nums}
    .step-head h2{margin:0;font-size:1.125rem;font-weight:600;line-height:1.35}
    .step-head p{margin:2px 0 0;color:var(--muted);font-size:.875rem;line-height:1.5}
    .step-body{padding:20px}
    .step-body > form > .step-nav{margin:24px -20px -20px}

    .callout{padding:14px 16px;border:1px solid var(--ds-accent-border);border-radius:var(--radius-sm);background:var(--ds-accent-soft)}
    .callout.good{border-color:var(--ds-success-border);background:var(--ds-success-soft)}
    .callout.warn{border-color:var(--ds-warning-border);background:var(--ds-warning-soft)}
    .callout h3{margin:0 0 4px;font-size:.9375rem;font-weight:600;line-height:1.4}
    .callout p{margin:0;color:var(--ds-text-secondary);font-size:.875rem;line-height:1.55}
    .callout .label{margin-bottom:4px}

    .label{color:var(--ds-text-secondary);font-size:.8125rem;font-weight:600;line-height:1.4}
    .objective{margin:4px 0 0!important;color:var(--text)!important;font-size:1rem!important;line-height:1.6!important}

    .questions{display:grid;gap:8px;margin:10px 0 0;padding:0;list-style:none}
    .questions li{padding:10px 14px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--surface3);
      color:var(--ds-text-secondary);font-size:.875rem;line-height:1.5}

    .field{margin-top:16px}
    .field label{display:block;margin-bottom:6px;color:var(--ds-text-secondary);font-size:.8125rem;font-weight:500}
    .field textarea{display:block;width:100%;min-height:104px;padding:8px 12px;resize:vertical;border:1px solid var(--ds-input-border);
      border-radius:var(--radius-sm);background:var(--surface3);color:var(--text);font:400 .875rem/1.55 var(--ds-font-sans);
      transition:border-color .12s ease,box-shadow .12s ease}
    .field textarea::placeholder{color:var(--dim)}
    .field textarea:focus{outline:none;border-color:var(--accent);box-shadow:var(--ds-focus-ring)}

    /* ── figures ───────────────────────────────────────────────── */
    .stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin:16px 0}
    .stat{display:flex;flex-direction:column;gap:4px;min-width:0;padding:14px 16px;border:1px solid var(--border);
      border-radius:var(--radius-sm);background:var(--surface3)}
    .stat strong{font-size:1.5rem;font-weight:700;line-height:1.2;letter-spacing:-.02em;font-variant-numeric:tabular-nums}
    .stat span{color:var(--muted);font-size:.8125rem;line-height:1.45}
    .stats.compare{grid-template-columns:repeat(2,minmax(0,1fr));margin-top:10px}
    .stats.compare .stat strong{font-size:.9375rem;font-weight:600;letter-spacing:0;line-height:1.4}

    .table-wrap{margin-top:10px;overflow:auto;border:1px solid var(--border);border-radius:var(--radius)}
    .table-wrap table{width:100%;min-width:640px;border-collapse:collapse;font-variant-numeric:tabular-nums}
    .table-wrap th,.table-wrap td{padding:12px 14px;border-bottom:1px solid var(--border);text-align:left;white-space:nowrap}
    .table-wrap th{padding:10px 14px;background:var(--surface3);color:var(--muted);font-size:.75rem;font-weight:600;text-transform:none;letter-spacing:0}
    .table-wrap td{color:var(--ds-text-secondary);font-size:.875rem;vertical-align:middle}
    .table-wrap tbody tr:last-child td{border-bottom:0}
    .table-wrap tbody tr:hover td{background:rgba(255,255,255,.02)}

    /* ── cleaning checks: one list, rows separated by rules ─────── */
    .checks{display:grid;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden}
    .check{display:flex;gap:12px;align-items:flex-start;padding:14px 16px;border-top:1px solid var(--border);background:var(--surface)}
    .check:first-child{border-top:0}
    .check > div{min-width:0}
    .check-icon{display:grid;place-items:center;flex:0 0 22px;height:22px;border:1px solid var(--ds-success-border);border-radius:var(--radius-xs);
      background:var(--ds-success-soft);color:var(--ds-success-text);font-size:.75rem;font-weight:700}
    .check.issue .check-icon{border-color:var(--ds-warning-border);background:var(--ds-warning-soft);color:var(--ds-warning-text)}
    .check strong{display:block;margin-bottom:2px;font-size:.875rem;font-weight:600;line-height:1.4}
    .check p{margin:0;color:var(--muted);font-size:.8125rem;line-height:1.5}

    .recommendation{margin-top:16px;padding:12px 16px;border:1px solid var(--ds-accent-border);border-radius:var(--radius-sm);
      background:var(--ds-accent-soft);color:#dbeafe;font-size:.875rem;line-height:1.55}
    .recommendation strong{font-weight:600}

    /* ── two-up panels inside a step ───────────────────────────── */
    .outlier-grid,.variable-grid,.relationship-grid,.feature-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
    .outlier-card,.variable-card,.relationship-card,.feature-option{min-width:0;padding:16px;border:1px solid var(--border);
      border-radius:var(--radius);background:var(--surface3)}
    .outlier-card.has-outliers{border-color:var(--ds-warning-border)}
    .outlier-card h3,.variable-card h3,.relationship-card h3{margin:0 0 6px;font-size:.9375rem;font-weight:600;line-height:1.4;overflow-wrap:anywhere}
    .outlier-card p,.variable-card p,.relationship-card p{margin:0;color:var(--muted);font-size:.8125rem;line-height:1.5}
    .variable-card .label,.relationship-card .label{margin-bottom:2px;color:var(--muted);font-size:.75rem;font-weight:500}
    .outlier-card details p{margin-top:8px}

    .box-line{position:relative;height:32px;margin:14px 4px 6px}
    .box-line::before{content:"";position:absolute;top:15px;left:4%;right:4%;height:2px;background:var(--ds-border-strong)}
    .box{position:absolute;top:6px;left:28%;width:44%;height:20px;border:2px solid var(--accent);border-radius:2px;background:var(--ds-accent-soft)}
    .median{position:absolute;top:2px;bottom:2px;left:50%;width:2px;background:var(--ds-accent-text)}

    .chart-wrap{height:210px;margin-top:12px}
    .metrics{display:grid;grid-template-columns:repeat(auto-fit,minmax(84px,1fr));gap:8px;margin-top:12px}
    .metric{min-width:0;padding:8px 10px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--surface)}
    .metric strong{display:block;font-size:.875rem;font-weight:600;line-height:1.35;font-variant-numeric:tabular-nums;overflow-wrap:anywhere}
    .metric span{display:block;color:var(--muted);font-size:.75rem;line-height:1.35}
    .relationship-value{margin:4px 0 8px;color:var(--text);font-size:1.5rem;font-weight:700;line-height:1.2;letter-spacing:-.02em;font-variant-numeric:tabular-nums}

    .feature-option{display:flex;align-items:flex-start;gap:10px;color:var(--ds-text-secondary);font-size:.875rem;line-height:1.5;cursor:pointer;
      transition:border-color .12s ease,background .12s ease}
    label.feature-option:hover{border-color:var(--ds-border-strong)}
    label.feature-option:has(input:checked){border-color:var(--ds-accent-border);background:var(--ds-accent-soft)}
    .feature-option input{flex-shrink:0;margin:3px 0 0}
    .feature-option > span{min-width:0}
    .feature-option strong{display:block;margin-bottom:6px;color:var(--text);font-size:.875rem;font-weight:600}
    .feature-option label{color:var(--ds-text-secondary);font-size:.875rem;cursor:pointer}
    .feature-option label input{margin:0 6px 0 0;vertical-align:-2px}
    .formula{display:inline-block;max-width:100%;margin:2px 0;padding:2px 8px;border:1px solid var(--border);border-radius:var(--radius-xs);
      background:var(--surface);color:var(--ds-accent-text);font-family:var(--ds-font-mono);font-size:.8125rem;overflow-wrap:anywhere}
    .feature-option p{margin:4px 0 0;color:var(--muted);font-size:.8125rem;line-height:1.5}
    .step-body code{font-family:var(--ds-font-mono);font-size:.8125rem;color:var(--ds-accent-text);overflow-wrap:anywhere}

    .summary-list{display:grid;gap:8px;margin:10px 0 16px;padding:0;list-style:none}
    .summary-list li{display:flex;gap:10px;padding:10px 14px;border:1px solid var(--border);border-radius:var(--radius-sm);
      background:var(--surface3);color:var(--ds-text-secondary);font-size:.875rem;line-height:1.5}
    .summary-list li::before{content:"✓";flex-shrink:0;color:var(--ds-success-text);font-weight:600}
    .created{margin-top:16px}
    .created p + p{margin-top:4px}

    .eyebrow{display:inline-flex;align-items:center;padding:2px 8px;border:1px solid var(--ds-success-border);border-radius:var(--radius-xs);
      background:var(--ds-success-soft);color:var(--ds-success-text);font-size:.75rem;font-weight:600;line-height:1.4;white-space:nowrap}
    .summary-title{margin:12px 0 4px;font-size:1.125rem;font-weight:600;line-height:1.35}

    .step-nav{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px 12px;padding:16px 20px;border-top:1px solid var(--border)}
    .step-nav form{margin:0}

    /* ── buttons ───────────────────────────────────────────────── */
    .btn{display:inline-flex;align-items:center;justify-content:center;min-height:38px;padding:0 16px;
      border:1px solid var(--accent);border-radius:var(--radius-sm);background:var(--accent);color:#fff;
      font:500 .875rem/1.2 var(--ds-font-sans);text-align:center;text-decoration:none;cursor:pointer;
      transition:background .12s ease,border-color .12s ease}
    .btn:hover{border-color:var(--accent-hover);background:var(--accent-hover)}
    .btn.secondary{border-color:var(--ds-border-strong);background:var(--surface2);color:var(--text)}
    .btn.secondary:hover{background:var(--ds-surface-hover)}
    .btn.danger{border-color:var(--ds-danger-border);background:transparent;color:var(--ds-danger-text)}
    .btn.danger:hover{background:var(--ds-danger-soft)}
    .actions{display:flex;gap:8px;flex-wrap:wrap}

    .empty{padding:32px 20px;border-radius:var(--radius-sm);background:var(--surface3);color:var(--muted);font-size:.875rem;line-height:1.5;text-align:center}
    .small{color:var(--muted);font-size:.8125rem}
    .error-text{margin:6px 0;color:var(--ds-danger-text);font-size:.75rem;line-height:1.4}
    .checkbox-list{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:8px;margin-top:10px}
    .checkbox-list label{display:flex;gap:8px;align-items:center;min-width:0;min-height:38px;padding:8px 12px;border:1px solid var(--border);
      border-radius:var(--radius-sm);background:var(--surface3);color:var(--ds-text-secondary);font-size:.875rem;line-height:1.4;cursor:pointer;overflow-wrap:anywhere}
    .checkbox-list label:has(input:checked){border-color:var(--ds-accent-border);background:var(--ds-accent-soft)}

    @media(max-width:1280px){
      .roadmap{grid-template-columns:repeat(4,minmax(0,1fr))}
      .roadmap-item{flex-direction:row;align-items:center;gap:8px;min-height:44px}
    }
    @media(max-width:1100px){
      .stats{grid-template-columns:repeat(2,minmax(0,1fr))}
    }
    @media(max-width:900px){
      .content{padding:24px 20px 40px}
      .outlier-grid,.variable-grid,.relationship-grid,.feature-grid{grid-template-columns:minmax(0,1fr)}
    }
    @media(max-width:640px){
      .content{padding:20px 16px 32px}
      .step-head{padding:14px 16px}
      .step-body{padding:16px}
      .step-body > form > .step-nav{margin:20px -16px -16px}
      .step-nav{align-items:stretch;flex-direction:column-reverse;padding:14px 16px}
      .step-nav .actions,.step-nav .btn,.step-nav form{width:100%}
      .stats.compare{grid-template-columns:minmax(0,1fr)}
      .topbar{align-items:flex-start}
    }
    @media(max-width:560px){
      .roadmap{grid-template-columns:repeat(2,minmax(0,1fr))}
    }
    @media(max-width:420px){
      .stats{grid-template-columns:minmax(0,1fr)}
      .checkbox-list{grid-template-columns:minmax(0,1fr)}
    }
    @media(prefers-reduced-motion:reduce){.btn,.exit-link,.roadmap-item a,.feature-option{transition:none}}
  </style>
    @include('partials.page-head', ['pageDescription' => 'Clean, explore, and profile a dataset before you model it.'])
</head>
<body>
<div class="layout">
  @include('partials.sidebar')
  <main class="content">
    <div class="page">
      <header class="topbar">
        <div>
          <h1 class="ds-page-title">{{ $overview['dataset']['title'] }}</h1>
          <p>{{ number_format($overview['row_count']) }} rows, {{ number_format($overview['column_count']) }} columns, Step {{ $step }} of 8</p>
        </div>
        <a class="exit-link" href="{{ route('student.data-toolkit.index') }}">Choose another dataset</a>
      </header>

      @include('student.data-toolkit.eda-panel')
    </div>
  </main>
</div>
@include('student.data-toolkit.dataset-modal')
<script>
const edaChartPayload = @json($chartPayload);
const chartColors = ['#3b82f6','#10b981','#f59e0b','#ef4444','#93c5fd','#6ee7b7','#8aa0bd'];

function makeChart(id, type, labels, values, label, extra = {}) {
  const canvas = document.getElementById(id);
  if (!canvas || typeof Chart === 'undefined') return;
  new Chart(canvas, {
    type,
    data: {labels, datasets: [{label, data: values, backgroundColor: type === 'bar' ? chartColors : '#3b82f6', borderColor: '#0f1928', borderWidth: 2, pointRadius: type === 'scatter' ? 3 : 2, tension: .25}]},
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {legend: {display: false}, tooltip: {backgroundColor: '#111c2d'}},
      scales: {
        x: {ticks: {color: '#8aa0bd', maxRotation: 35}, grid: {color: '#1e2f47'}},
        y: {ticks: {color: '#8aa0bd'}, grid: {color: '#1e2f47'}}
      },
      ...extra
    }
  });
}

edaChartPayload.numeric.forEach((chart, index) => makeChart(`numericChart${index}`, 'bar', chart.labels, chart.values, chart.column));
edaChartPayload.categorical.forEach((chart, index) => makeChart(`categoryChart${index}`, 'bar', chart.labels, chart.values, chart.column));

if (edaChartPayload.scatter) {
  const scatterCanvas = document.getElementById('relationshipScatter');
  if (scatterCanvas && typeof Chart !== 'undefined') {
    new Chart(scatterCanvas, {
      type: 'scatter',
      data: {datasets: [{label: `${edaChartPayload.scatter.x} vs ${edaChartPayload.scatter.y}`, data: edaChartPayload.scatter.points, backgroundColor: '#3b82f6'}]},
      options: {
        responsive: true,
        maintainAspectRatio: false,
        parsing: false,
        plugins: {legend: {display: false}, tooltip: {backgroundColor: '#111c2d'}},
        scales: {
          x: {type: 'linear', position: 'bottom', title: {display: true, text: edaChartPayload.scatter.x, color: '#8aa0bd'}, ticks: {color: '#8aa0bd'}, grid: {color: '#1e2f47'}},
          y: {title: {display: true, text: edaChartPayload.scatter.y, color: '#8aa0bd'}, ticks: {color: '#8aa0bd'}, grid: {color: '#1e2f47'}}
        }
      }
    });
  }
}

if (edaChartPayload.group) {
  makeChart('groupComparisonChart', 'bar', edaChartPayload.group.labels, edaChartPayload.group.values, edaChartPayload.group.numeric);
}
</script>
</body>
</html>
