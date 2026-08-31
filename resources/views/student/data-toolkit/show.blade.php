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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
  <style>
    :root{--bg:#0d1320;--surface:#111c2d;--surface2:#17243a;--surface3:#1d2c43;--border:#263a56;--text:#f8fafc;--muted:#9bacc4;--dim:#6f829e;--accent:#3b82f6;--accent2:#60a5fa;--good:#10b981;--warn:#f59e0b;--danger:#f87171;--radius:18px}
    *{box-sizing:border-box}body{margin:0;background:radial-gradient(circle at 85% 0,rgba(59,130,246,.14),transparent 34%),var(--bg);color:var(--text);font-family:Inter,Arial,sans-serif}.layout{display:flex;min-height:100vh}.content{flex:1;min-width:0;padding:30px}.page{max-width:1180px;margin:0 auto}.topbar{display:flex;justify-content:space-between;align-items:flex-start;gap:20px;margin-bottom:20px}.eyebrow{color:var(--accent2);font-size:.73rem;font-weight:900;letter-spacing:.12em;text-transform:uppercase}.topbar h1{font-size:clamp(1.7rem,3vw,2.45rem);margin:7px 0}.topbar p{margin:0;color:var(--muted)}.exit-link{color:var(--muted);text-decoration:none;font-size:.84rem;font-weight:800;border:1px solid var(--border);border-radius:10px;padding:10px 12px;background:var(--surface)}.roadmap{display:grid;grid-template-columns:repeat(8,minmax(0,1fr));gap:7px;margin-bottom:20px;overflow-x:auto;padding-bottom:5px}.roadmap-item{position:relative;min-width:104px;border:1px solid var(--border);background:rgba(17,28,45,.82);border-radius:13px;padding:11px 10px;color:var(--dim)}.roadmap-item .number{display:grid;place-items:center;width:25px;height:25px;border-radius:50%;background:var(--surface3);font-size:.72rem;font-weight:900;margin-bottom:7px}.roadmap-item strong{display:block;font-size:.74rem;line-height:1.32}.roadmap-item.completed{color:#a7f3d0;border-color:rgba(16,185,129,.32);background:rgba(16,185,129,.07)}.roadmap-item.completed .number{background:rgba(16,185,129,.2)}.roadmap-item.current{color:#dbeafe;border-color:rgba(96,165,250,.75);background:rgba(59,130,246,.14);box-shadow:0 0 0 2px rgba(59,130,246,.12)}.roadmap-item.current .number{background:var(--accent);color:#fff}.roadmap-item a{color:inherit;text-decoration:none}.flash{border:1px solid rgba(16,185,129,.35);background:rgba(16,185,129,.09);color:#bbf7d0;padding:12px 14px;border-radius:12px;margin-bottom:14px}.flash.error{border-color:rgba(248,113,113,.4);background:rgba(248,113,113,.08);color:#fecaca}.step-card{background:linear-gradient(180deg,rgba(255,255,255,.025),transparent),var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:0 20px 50px rgba(0,0,0,.18);overflow:hidden}.step-head{padding:24px 26px 20px;border-bottom:1px solid var(--border);display:flex;gap:16px;align-items:flex-start}.step-number{display:grid;place-items:center;flex:0 0 43px;height:43px;border-radius:13px;background:rgba(59,130,246,.16);border:1px solid rgba(96,165,250,.35);color:#bfdbfe;font-weight:900}.step-head h2{font-size:1.45rem;margin:0 0 6px}.step-head p{color:var(--muted);line-height:1.55;margin:0}.step-body{padding:26px}.callout{border:1px solid rgba(59,130,246,.28);background:rgba(59,130,246,.08);border-radius:14px;padding:17px}.callout.good{border-color:rgba(16,185,129,.3);background:rgba(16,185,129,.07)}.callout.warn{border-color:rgba(245,158,11,.35);background:rgba(245,158,11,.07)}.callout h3{margin:0 0 7px;font-size:1rem}.callout p{margin:0;color:var(--muted);line-height:1.6}.label{font-size:.72rem;color:var(--dim);text-transform:uppercase;letter-spacing:.08em;font-weight:900}.objective{font-size:1.15rem;line-height:1.65;margin:9px 0 0}.questions{display:grid;gap:9px;margin:16px 0 0;padding:0;list-style:none}.questions li{border:1px solid var(--border);background:var(--surface2);border-radius:11px;padding:11px 13px;color:#dce6f4;font-size:.88rem}.questions li::before{content:"?";display:inline-grid;place-items:center;width:21px;height:21px;border-radius:50%;background:rgba(59,130,246,.18);color:#93c5fd;font-weight:900;margin-right:8px}.field{margin-top:18px}.field label{display:block;font-size:.84rem;font-weight:800;margin-bottom:8px}.field textarea{width:100%;min-height:105px;resize:vertical;background:#0d1726;border:1px solid var(--border);border-radius:12px;color:var(--text);font:inherit;padding:13px}.field textarea:focus{outline:2px solid rgba(59,130,246,.35);border-color:var(--accent)}.stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:11px;margin:16px 0}.stat{border:1px solid var(--border);background:var(--surface2);border-radius:13px;padding:14px}.stat strong{display:block;font-size:1.2rem;margin-bottom:5px}.stat span{color:var(--muted);font-size:.77rem}.table-wrap{overflow:auto;border:1px solid var(--border);border-radius:13px;margin-top:15px}table{width:100%;border-collapse:collapse;min-width:620px}th,td{padding:11px 13px;border-bottom:1px solid var(--border);text-align:left;font-size:.8rem;white-space:nowrap}th{background:#142137;color:#b9c8dc;font-size:.72rem;text-transform:uppercase;letter-spacing:.05em}td{color:#d8e2f0}tr:last-child td{border-bottom:0}.checks{display:grid;gap:10px}.check{display:flex;gap:12px;align-items:flex-start;border:1px solid var(--border);border-radius:13px;padding:14px;background:var(--surface2)}.check-icon{display:grid;place-items:center;flex:0 0 27px;height:27px;border-radius:50%;background:rgba(16,185,129,.15);color:#6ee7b7;font-weight:900}.check.issue .check-icon{background:rgba(245,158,11,.15);color:#fcd34d}.check strong{display:block;font-size:.88rem;margin-bottom:3px}.check p{margin:0;color:var(--muted);font-size:.8rem;line-height:1.5}.recommendation{margin-top:16px;border-left:3px solid var(--accent);background:rgba(59,130,246,.07);padding:13px 15px;border-radius:0 11px 11px 0;color:#dbeafe;font-size:.84rem;line-height:1.55}.outlier-grid,.variable-grid,.relationship-grid,.feature-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:13px}.outlier-card,.variable-card,.relationship-card,.feature-option{border:1px solid var(--border);background:var(--surface2);border-radius:14px;padding:16px}.outlier-card.has-outliers{border-color:rgba(245,158,11,.38)}.outlier-card h3,.variable-card h3,.relationship-card h3{margin:0 0 7px;font-size:.95rem}.outlier-card p,.variable-card p,.relationship-card p{margin:0;color:var(--muted);font-size:.8rem;line-height:1.5}.box-line{position:relative;height:32px;margin:15px 5px 4px}.box-line::before{content:"";position:absolute;top:15px;left:4%;right:4%;height:2px;background:#4a6385}.box{position:absolute;top:6px;left:28%;width:44%;height:20px;border:2px solid var(--accent2);background:rgba(59,130,246,.18)}.median{position:absolute;top:4px;bottom:4px;left:50%;width:2px;background:#dbeafe}.chart-wrap{height:210px;margin-top:13px}.metrics{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:7px;margin-top:12px}.metric{border:1px solid var(--border);border-radius:9px;padding:9px;background:#132035}.metric strong{display:block;font-size:.88rem}.metric span{font-size:.68rem;color:var(--muted)}.relationship-value{font-size:1.45rem;font-weight:900;color:#bfdbfe}.feature-option{display:flex;gap:11px;cursor:pointer}.feature-option input{margin-top:4px}.feature-option strong{display:block;margin-bottom:6px}.formula{display:inline-block;font-family:ui-monospace,SFMono-Regular,Consolas,monospace;color:#bfdbfe;background:#101c2e;border:1px solid var(--border);border-radius:8px;padding:7px 9px;font-size:.78rem;margin:5px 0}.feature-option p{font-size:.8rem;color:var(--muted);line-height:1.5;margin:4px 0 0}.summary-list{display:grid;gap:9px;list-style:none;padding:0;margin:15px 0}.summary-list li{display:flex;gap:9px;border:1px solid var(--border);background:var(--surface2);border-radius:11px;padding:12px;color:#dce6f4;font-size:.84rem;line-height:1.5}.summary-list li::before{content:"✓";color:#6ee7b7;font-weight:900}.created{margin-top:16px}.step-nav{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:19px 26px;border-top:1px solid var(--border);background:rgba(9,17,29,.38)}.btn{display:inline-flex;align-items:center;justify-content:center;min-height:43px;border:0;border-radius:11px;background:linear-gradient(135deg,var(--accent),#2563eb);color:#fff;text-decoration:none;padding:10px 16px;font:800 .85rem Inter;cursor:pointer}.btn:hover{filter:brightness(1.08)}.btn.secondary{background:var(--surface2);border:1px solid var(--border);color:#dbeafe}.btn.danger{background:rgba(248,113,113,.12);border:1px solid rgba(248,113,113,.4);color:#fecaca}.actions{display:flex;gap:9px;flex-wrap:wrap}.empty{border:1px dashed var(--border);border-radius:13px;padding:24px;text-align:center;color:var(--muted)}.small{font-size:.78rem;color:var(--muted)}.error-text{color:#fecaca;font-size:.8rem;margin:9px 0}.checkbox-list{display:grid;gap:8px;margin-top:14px}.checkbox-list label{display:flex;gap:9px;align-items:center;border:1px solid var(--border);background:#132035;border-radius:10px;padding:10px;font-size:.82rem}.complete-mark{display:grid;place-items:center;width:72px;height:72px;border-radius:50%;background:rgba(16,185,129,.16);border:1px solid rgba(16,185,129,.45);color:#6ee7b7;font-size:2rem;font-weight:900;margin-bottom:15px}@media(max-width:1100px){.roadmap{grid-template-columns:repeat(8,112px)}.stats{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:800px){.layout{display:block}.content{padding:20px}.topbar{display:block}.exit-link{display:inline-flex;margin-top:14px}.step-head,.step-body,.step-nav{padding-left:18px;padding-right:18px}.outlier-grid,.variable-grid,.relationship-grid,.feature-grid{grid-template-columns:1fr}.metrics{grid-template-columns:repeat(2,minmax(0,1fr))}.step-nav{align-items:stretch;flex-direction:column-reverse}.step-nav .actions,.step-nav .btn{width:100%}}
  </style>
</head>
<body>
<div class="layout">
  @include('partials.sidebar')
  <main class="content">
    <div class="page">
      <header class="topbar">
        <div>
          <div class="eyebrow">Guided EDA Roadmap</div>
          <h1 class="ds-page-title">{{ $overview['dataset']['title'] }}</h1>
          <p>{{ number_format($overview['row_count']) }} rows · {{ number_format($overview['column_count']) }} columns · Step {{ $step }} of 8</p>
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
const chartColors = ['#60a5fa','#34d399','#fbbf24','#a78bfa','#fb7185','#22d3ee','#f97316','#94a3b8','#4ade80','#c084fc'];

function makeChart(id, type, labels, values, label, extra = {}) {
  const canvas = document.getElementById(id);
  if (!canvas || typeof Chart === 'undefined') return;
  new Chart(canvas, {
    type,
    data: {labels, datasets: [{label, data: values, backgroundColor: type === 'bar' ? chartColors : '#60a5fa', borderColor: '#60a5fa', borderWidth: 2, pointRadius: type === 'scatter' ? 3 : 2, tension: .25}]},
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {legend: {display: false}, tooltip: {backgroundColor: '#0d1726'}},
      scales: {
        x: {ticks: {color: '#9bacc4', maxRotation: 35}, grid: {color: 'rgba(155,172,196,.10)'}},
        y: {ticks: {color: '#9bacc4'}, grid: {color: 'rgba(155,172,196,.10)'}}
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
      data: {datasets: [{label: `${edaChartPayload.scatter.x} vs ${edaChartPayload.scatter.y}`, data: edaChartPayload.scatter.points, backgroundColor: '#60a5fa'}]},
      options: {
        responsive: true,
        maintainAspectRatio: false,
        parsing: false,
        plugins: {legend: {display: false}, tooltip: {backgroundColor: '#0d1726'}},
        scales: {
          x: {type: 'linear', position: 'bottom', title: {display: true, text: edaChartPayload.scatter.x, color: '#9bacc4'}, ticks: {color: '#9bacc4'}, grid: {color: 'rgba(155,172,196,.10)'}},
          y: {title: {display: true, text: edaChartPayload.scatter.y, color: '#9bacc4'}, ticks: {color: '#9bacc4'}, grid: {color: 'rgba(155,172,196,.10)'}}
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
