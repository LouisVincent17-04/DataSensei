<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $model->name }} Report</title>
<style>
body{font-family:Arial,sans-serif;color:#111;margin:32px;line-height:1.45}h1{font-size:22px;margin:0 0 5px}h2{font-size:15px;margin-top:26px;border-bottom:1px solid #bbb;padding-bottom:6px}h3{font-size:13px;margin:18px 0 8px}.muted{color:#555}.grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px}.box{border:1px solid #bbb;padding:10px;min-height:52px}.box span{font-size:10px;text-transform:uppercase;color:#555}.box strong{display:block;margin-top:4px}.table{width:100%;border-collapse:collapse}.table th,.table td{border:1px solid #bbb;padding:7px;text-align:left;font-size:12px;vertical-align:top}ul{font-size:13px}.print{position:fixed;right:25px;top:20px}.status{font-weight:bold}.better{color:#176b33}.worse{color:#9e2424}.equal{color:#555}@media(max-width:800px){.grid{grid-template-columns:repeat(2,1fr)}}@media print{.print{display:none}body{margin:12mm}.grid{grid-template-columns:repeat(4,1fr)}}
</style>
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
<h1>{{ $model->name }}</h1>
<div class="muted">{{ $model->isSystemModel() ? 'Read-only system benchmark' : 'User-trained model' }} · {{ str($model->algorithm_key)->replace('_',' ')->title() }} · {{ $version->version_label }} · Generated {{ now()->format('F j, Y g:i A') }}</div>

<h2>Educational Benchmark Summary</h2>
<div class="grid">
    <div class="box"><span>Dataset</span><strong>{{ $datasetName }}</strong></div>
    <div class="box"><span>Dataset Quality</span><strong>{{ number_format((float)($quality?->quality_score ?? 0),1) }}/100 · {{ $quality?->grade ?? 'Not graded' }}</strong></div>
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
<table class="table">
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
</table>

@if($comparison)
<h2>System Benchmark Comparison</h2>
<p class="muted">Compared with {{ $comparison['benchmark_model']?->name ?? 'the available system benchmark' }}. A same-algorithm benchmark is preferred; otherwise the dataset’s primary benchmark is used.</p>
<table class="table">
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
</table>
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
<table class="table">
<thead><tr><th>Feature</th><th>Influence</th><th>Method</th></tr></thead>
<tbody>
@forelse(array_slice((array)data_get($version->explanations,'feature_importance',[]),0,15) as $item)
<tr><td>{{ $item['feature'] ?? 'Feature' }}</td><td>{{ number_format(abs((float)($item['value'] ?? 0)),6) }}</td><td>{{ str($item['kind'] ?? 'importance')->replace('_',' ')->title() }}</td></tr>
@empty<tr><td colspan="3">This estimator did not expose a stable feature-influence value.</td></tr>@endforelse
</tbody>
</table>

<h2>Training Configuration and Reproducibility</h2>
<div class="grid">
    <div class="box"><span>Features</span><strong>{{ count((array)$version->feature_names) }}</strong></div>
    <div class="box"><span>Target</span><strong>{{ $version->target_column ?: 'None' }}</strong></div>
    <div class="box"><span>Training Time</span><strong>{{ number_format(($version->training_time_ms ?? 0)/1000,3) }} s</strong></div>
    <div class="box"><span>Runtime</span><strong>Python {{ $version->python_version }} / sklearn {{ $version->sklearn_version }}</strong></div>
</div>
@if($configuration !== [])
<h3>Setup Choices</h3>
<table class="table"><tbody>
<tr><th>Features</th><td>{{ implode(', ', (array) ($configuration['features'] ?? $version->feature_names ?? [])) }}</td></tr>
<tr><th>Train/Test Split</th><td>{{ isset($configuration['test_size']) ? (100 - (int) round((float) $configuration['test_size'] * 100)).'% training / '.(int) round((float) $configuration['test_size'] * 100).'% testing' : 'All rows (clustering)' }}</td></tr>
<tr><th>Random State</th><td>{{ $formatValue($configuration['random_state'] ?? null) }}</td></tr>
<tr><th>Cross-validation</th><td>{{ (int) ($configuration['cross_validation'] ?? 0) > 0 ? (int) $configuration['cross_validation'].' folds' : 'Disabled' }}</td></tr>
</tbody></table>
@endif
<h3>Hyperparameters</h3>
<table class="table"><thead><tr><th>Parameter</th><th>Value</th></tr></thead><tbody>@forelse($parameterRows as $key=>$value)<tr><td>{{ str($key)->replace('_',' ')->title() }}</td><td>{{ $formatValue($value) }}</td></tr>@empty<tr><td colspan="2">Default parameters were used.</td></tr>@endforelse</tbody></table>
@if($preprocessingRows !== [])
<h3>Preprocessing</h3>
<table class="table"><thead><tr><th>Step</th><th>Value</th></tr></thead><tbody>@foreach($preprocessingRows as $key=>$value)<tr><td>{{ str($key)->replace('_',' ')->title() }}</td><td>{{ $formatValue($value) }}</td></tr>@endforeach</tbody></table>
@endif
</body>
</html>
