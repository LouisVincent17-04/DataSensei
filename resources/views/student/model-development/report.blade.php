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

<h2>Model Metrics</h2>
<table class="table">
<thead><tr><th>Metric</th><th>Value</th><th>Interpretation</th></tr></thead>
<tbody>
@foreach($metrics as $key=>$value)
@if(is_numeric($value))
<tr>
    <td>{{ str($key)->replace('_',' ')->title() }}</td>
    <td>{{ number_format((float)$value,4) }}{{ in_array($key,['accuracy','precision','recall','f1','roc_auc','average_precision']) ? '%' : '' }}</td>
    <td>@switch($key)
        @case('accuracy') Share of test records classified correctly. @break
        @case('precision') Reliability of positive predictions, weighted across classes. @break
        @case('recall') Share of actual class cases recovered, weighted across classes. @break
        @case('f1') Balance between precision and recall. @break
        @case('roc_auc') Ability to rank classes across probability thresholds. @break
        @case('rmse') Typical regression error with larger errors penalized more strongly. Lower is better. @break
        @case('mae') Average absolute regression error. Lower is better. @break
        @case('r2') Share of target variation explained on the test split. @break
        @case('silhouette') Separation and cohesion of clusters. Higher is better. @break
        @default Measured during the stored evaluation run.
    @endswitch</td>
</tr>
@endif
@endforeach
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
    <td>{{ str($row['metric'])->replace('_',' ')->title() }}</td>
    <td>{{ number_format((float)$row['user'],3) }}</td>
    <td>{{ number_format((float)$row['system'],3) }}</td>
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
<h3>Hyperparameters</h3>
<table class="table"><thead><tr><th>Parameter</th><th>Value</th></tr></thead><tbody>@forelse((array)$version->hyperparameters as $key=>$value)<tr><td>{{ $key }}</td><td>{{ is_array($value) ? json_encode($value) : (is_bool($value) ? ($value?'true':'false') : $value) }}</td></tr>@empty<tr><td colspan="2">Default parameters were used.</td></tr>@endforelse</tbody></table>
</body>
</html>
