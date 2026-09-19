<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $model->name }} Report</title>
<style>
/* Printable model report. It stays light on purpose (it is made for paper),
   so its colours live on <body>: partials.page-head defines the dark product
   tokens on :root after this block, and body-scoped values win. */
*{box-sizing:border-box}
body{
  --rp-bg:#ffffff;--rp-head:#f1f5f9;--rp-border:#d8e0ed;--rp-border-strong:#c3cfe0;
  --rp-text:#111827;--rp-text-secondary:#334155;--rp-muted:#52637a;
  --rp-accent:#3b82f6;--rp-accent-strong:#2563eb;
  color-scheme:light;max-width:1040px;margin:0 auto;padding:28px 32px 48px;background:var(--rp-bg);color:var(--rp-text);
  font-family:var(--ds-font-sans);font-size:.875rem;line-height:1.5}
body ::selection{color:var(--rp-text)}
h1.ds-page-title{margin:0 0 4px;color:var(--rp-text);overflow-wrap:anywhere}
h2{margin:32px 0 12px;padding-bottom:8px;border-bottom:1px solid var(--rp-border);font-size:1rem;font-weight:600;line-height:1.35}
h3{margin:20px 0 8px;font-size:.875rem;font-weight:600;line-height:1.4}
p{margin:0 0 8px;color:var(--rp-text-secondary)}
p strong{color:var(--rp-text)}
.muted{color:var(--rp-muted)}
body > .muted{font-size:.875rem;font-variant-numeric:tabular-nums;overflow-wrap:anywhere}
.grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px}
.box{min-width:0;min-height:52px;padding:10px 12px;border:1px solid var(--rp-border);border-radius:var(--ds-radius-md)}
.box span{display:block;color:var(--rp-muted);font-size:.75rem;font-weight:500;line-height:1.4}
.box strong{display:block;margin-top:2px;font-size:.9375rem;font-weight:600;line-height:1.35;font-variant-numeric:tabular-nums;overflow-wrap:anywhere}
.table-wrap{width:100%;overflow-x:auto;border:1px solid var(--rp-border);border-radius:var(--ds-radius-md)}
.table{width:100%;border-collapse:collapse;font-variant-numeric:tabular-nums}
.table th,.table td{padding:10px 14px;border-bottom:1px solid var(--rp-border);text-align:left;vertical-align:top}
.table th{background:var(--rp-head);color:var(--rp-muted);font-size:.75rem;font-weight:600;white-space:nowrap}
.table td{color:var(--rp-text-secondary);font-size:.875rem;overflow-wrap:break-word}
.table tbody tr:last-child th,.table tbody tr:last-child td{border-bottom:0}
.table tbody th{width:180px}
ul{margin:8px 0;padding-left:20px;color:var(--rp-text-secondary)}
li + li{margin-top:4px}
.print{display:flex;align-items:center;justify-content:center;width:fit-content;min-height:38px;margin:0 0 16px auto;padding:0 16px;
  border:1px solid var(--rp-accent);border-radius:var(--ds-radius-sm);background:var(--rp-accent);color:#fff;
  font:500 .875rem/1.2 var(--ds-font-sans);cursor:pointer;transition:background .12s ease,border-color .12s ease}
.print:hover{border-color:var(--rp-accent-strong);background:var(--rp-accent-strong)}
.status{font-weight:600}
.better{color:#047857}
.worse{color:#b91c1c}
.equal{color:var(--rp-muted)}
@media(max-width:800px){.grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:640px){body{padding:16px 16px 32px}.print{width:100%}.table tbody th{width:auto}.table:has(thead th:nth-child(3)){min-width:560px}}
@media(max-width:360px){.grid{grid-template-columns:minmax(0,1fr)}}
@media print{
  .print{display:none}
  body{max-width:none;margin:12mm;padding:0}
  .grid{grid-template-columns:repeat(4,minmax(0,1fr))}
  .table-wrap{overflow:visible}
  .table th{-webkit-print-color-adjust:exact;print-color-adjust:exact}
}
@media(prefers-reduced-motion:reduce){.print{transition:none}}
</style>
    @include('partials.page-head', ['pageDescription' => 'Build, evaluate, and save a real machine-learning model in ten guided steps.'])
</head>
<body>
@php
    $summary = (array) ($quality?->summary ?? []);
    $metrics = (array) $version->metrics;
    $recommendations = (array) ($quality?->recommendations ?? []);
    $recommendedAlgorithms = (array) ($summary['recommended_algorithms'] ?? []);
    $classBalance = (array) ($summary['class_balance'] ?? []);
    $datasetName = $model->dataset?->name ?? $model->userDataset?->name ?? 'Dataset';
    $guide = \App\Support\ModelDevelopmentGuide::class;
    $verdict = $guide::verdict((string) $model->problem_type, $metrics);
    $reportMetrics = $guide::displayMetrics((string) $model->problem_type, $metrics);
    $hyperparameters = (array) $version->hyperparameters;
    // Stored as {parameters, preprocessing, configuration}; only the first two are meaningful in a report.
    $parameterRows = (array) ($hyperparameters['parameters'] ?? []);
    $preprocessingRows = array_filter(
        (array) ($hyperparameters['preprocessing'] ?? []),
        static fn ($value) => is_scalar($value) || $value === null
    );
    if ($parameterRows === [] && ! array_key_exists('parameters', $hyperparameters) && ! array_key_exists('preprocessing', $hyperparameters)) {
        $parameterRows = array_filter($hyperparameters, static fn ($value) => is_scalar($value) || $value === null);
    }
    $configuration = (array) ($hyperparameters['configuration'] ?? []);
    $formatValue = static fn ($value) => is_bool($value) ? ($value ? 'Yes' : 'No') : ($value === null ? '—' : (is_array($value) ? json_encode($value) : (string) $value));
@endphp
<button class="print" onclick="window.print()">Print Report</button>
<h1 class="ds-page-title">{{ $model->name }}</h1>
<div class="muted">{{ $model->isSystemModel() ? 'Read-only system benchmark' : 'User-trained model' }}, {{ str($model->algorithm_key)->replace('_',' ')->title() }}, {{ $version->version_label }}, Generated {{ now()->format('F j, Y g:i A') }}</div>

<h2>Educational Benchmark Summary</h2>
<div class="grid">
    <div class="box"><span>Dataset</span><strong>{{ $datasetName }}</strong></div>
    <div class="box"><span>Dataset Quality</span><strong>{{ number_format((float)($quality?->quality_score ?? 0),1) }}/100, {{ $quality?->grade ?? 'Not graded' }}</strong></div>
    <div class="box"><span>Rows and Columns</span><strong>{{ number_format((int)($summary['rows'] ?? 0)) }} × {{ number_format((int)($summary['columns'] ?? 0)) }}</strong></div>
    <div class="box"><span>Problem Type</span><strong>{{ ucfirst($model->problem_type) }}</strong></div>
    <div class="box"><span>Missing Values</span><strong>{{ number_format((float)($summary['missing_percent'] ?? 0),2) }}%</strong></div>
    <div class="box"><span>Duplicate Rows</span><strong>{{ number_format((int)($summary['duplicate_rows'] ?? 0)) }}</strong></div>
    <div class="box"><span>Mixed-type Columns</span><strong>{{ count((array)($summary['mixed_type_columns'] ?? [])) }}</strong></div>
    <div class="box"><span>Class Balance</span><strong>{{ $classBalance['label'] ?? 'Not applicable' }}</strong></div>
</div>

<h2>Quick Verdict</h2>
<p><strong>{{ $verdict['title'] }}</strong> ({{ $verdict['metric'] }}: {{ $verdict['value'] }}). {{ $verdict['summary'] }}</p>
<ul>@foreach($verdict['next_steps'] as $item)<li>{{ $item }}</li>@endforeach</ul>
<p class="muted">The verdict is a classroom rule of thumb, not a guarantee of real-world performance.</p>

<h2>Model Metrics</h2>
<div class="table-wrap"><table class="table">
<thead><tr><th>Metric</th><th>Value</th><th>Interpretation</th></tr></thead>
<tbody>
@forelse($reportMetrics as $key=>$value)
@php $metricInfo = $guide::metric((string) $key); @endphp
<tr>
    <td>{{ $metricInfo['label'] }}</td>
    <td>{{ $guide::formatMetric((string) $key, $value) }}</td>
    <td>{{ $metricInfo['plain'] }}@if($metricInfo['better']) {{ $metricInfo['better'] === 'higher' ? 'Higher is better.' : 'Lower is better.' }}@endif</td>
</tr>
@empty
<tr><td colspan="3">No numeric metrics were recorded for this version.</td></tr>
@endforelse
</tbody>
</table></div>

@if($comparison)
<h2>System Benchmark Comparison</h2>
<p class="muted">Compared with {{ $comparison['benchmark_model']?->name ?? 'the available system benchmark' }}. A same-algorithm benchmark is preferred; otherwise the dataset’s primary benchmark is used.</p>
<div class="table-wrap"><table class="table">
<thead><tr><th>Metric</th><th>Your Model</th><th>System</th><th>Difference</th><th>Result</th></tr></thead>
<tbody>
@foreach($comparison['rows'] as $row)
<tr>
    <td>{{ $guide::metric((string) $row['metric'])['label'] }}</td>
    <td>{{ $guide::formatMetric((string) $row['metric'], $row['user']) }}</td>
    <td>{{ $guide::formatMetric((string) $row['metric'], $row['system']) }}</td>
    <td>{{ ($row['difference'] >= 0 ? '+' : '').number_format((float)$row['difference'],3) }}</td>
    <td class="status {{ $row['status'] }}">{{ ucfirst($row['status']) }}</td>
</tr>
@endforeach
</tbody>
</table></div>
<ul>@foreach($comparison['explanation'] as $item)<li>{{ $item }}</li>@endforeach</ul>
@endif

<h2>Evidence-based Explanation</h2>
<ul>@forelse((array)data_get($version->explanations,'educational',[]) as $item)<li>{{ $item }}</li>@empty<li>No additional explanation was generated for this version.</li>@endforelse</ul>

@if($recommendedAlgorithms !== [])
<h2>Dataset Recommendations</h2>
<p><strong>Algorithms suggested by the measured dataset characteristics:</strong> {{ collect($recommendedAlgorithms)->map(fn($key)=>str($key)->replace('_',' ')->title())->implode(', ') }}.</p>
@endif
@if($recommendations !== [])
<ul>@foreach($recommendations as $recommendation)<li>{{ $recommendation }}</li>@endforeach</ul>
@endif

<h2>Feature Influence</h2>
<div class="table-wrap"><table class="table">
<thead><tr><th>Feature</th><th>Influence</th><th>Method</th></tr></thead>
<tbody>
@forelse(array_slice((array)data_get($version->explanations,'feature_importance',[]),0,15) as $item)
<tr><td>{{ $item['feature'] ?? 'Feature' }}</td><td>{{ number_format(abs((float)($item['value'] ?? 0)),6) }}</td><td>{{ str($item['kind'] ?? 'importance')->replace('_',' ')->title() }}</td></tr>
@empty<tr><td colspan="3">This estimator did not expose a stable feature-influence value.</td></tr>@endforelse
</tbody>
</table></div>

<h2>Training Configuration and Reproducibility</h2>
<div class="grid">
    <div class="box"><span>Features</span><strong>{{ count((array)$version->feature_names) }}</strong></div>
    <div class="box"><span>Target</span><strong>{{ $version->target_column ?: 'None' }}</strong></div>
    <div class="box"><span>Training Time</span><strong>{{ number_format(($version->training_time_ms ?? 0)/1000,3) }} s</strong></div>
    <div class="box"><span>Runtime</span><strong>Python {{ $version->python_version }} / sklearn {{ $version->sklearn_version }}</strong></div>
</div>
@if($configuration !== [])
<h3>Setup Choices</h3>
<div class="table-wrap"><table class="table"><tbody>
<tr><th>Features</th><td>{{ implode(', ', (array) ($configuration['features'] ?? $version->feature_names ?? [])) }}</td></tr>
<tr><th>Train/Test Split</th><td>{{ isset($configuration['test_size']) ? (100 - (int) round((float) $configuration['test_size'] * 100)).'% training / '.(int) round((float) $configuration['test_size'] * 100).'% testing' : 'All rows (clustering)' }}</td></tr>
<tr><th>Random State</th><td>{{ $formatValue($configuration['random_state'] ?? null) }}</td></tr>
<tr><th>Cross-validation</th><td>{{ (int) ($configuration['cross_validation'] ?? 0) > 0 ? (int) $configuration['cross_validation'].' folds' : 'Disabled' }}</td></tr>
</tbody></table></div>
@endif
<h3>Hyperparameters</h3>
<div class="table-wrap"><table class="table"><thead><tr><th>Parameter</th><th>Value</th></tr></thead><tbody>@forelse($parameterRows as $key=>$value)<tr><td>{{ str($key)->replace('_',' ')->title() }}</td><td>{{ $formatValue($value) }}</td></tr>@empty<tr><td colspan="2">Default parameters were used.</td></tr>@endforelse</tbody></table></div>
@if($preprocessingRows !== [])
<h3>Preprocessing</h3>
<div class="table-wrap"><table class="table"><thead><tr><th>Step</th><th>Value</th></tr></thead><tbody>@foreach($preprocessingRows as $key=>$value)<tr><td>{{ str($key)->replace('_',' ')->title() }}</td><td>{{ $formatValue($value) }}</td></tr>@endforeach</tbody></table></div>
@endif
</body>
</html>
