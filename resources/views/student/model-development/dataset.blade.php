<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $dataset->name }} — DataSensei</title>
    @include('student.model-development.partials.styles')
    @include('partials.page-head', ['pageDescription' => 'Build, evaluate, and save a real machine-learning model in ten guided steps.'])
</head>
<body>
@php
    $downloadUrl = $datasetType === 'system'
        ? route('student.model-development.system-datasets.download', $dataset)
        : route('student.model-development.user-datasets.download', $dataset);
    $continueUrl = route('student.model-development.wizard', [
        'dataset_type' => $datasetType,
        'dataset_id' => $dataset->id,
        'step' => 2,
    ]);
    $summary = (array) ($quality?->summary ?? []);
    $rowTotal = (int) $dataset->row_count;
    $missingPercent = (float) ($summary['missing_percent'] ?? 0);
    $duplicatePercent = (float) ($summary['duplicate_percent'] ?? 0);
    $mixedColumns = count((array) ($summary['mixed_type_columns'] ?? []));
    $numericColumnCount = count((array) ($profile['numeric_columns'] ?? []));
    $suggestedTarget = $dataset->target_column ?: ($profile['suggested_target'] ?? null);

    // Plain-language readiness checklist so beginners know what DataSensei will handle for them.
    $readiness = [
        $rowTotal >= 100
            ? ['ok', number_format($rowTotal).' rows – enough examples to learn from and test on.']
            : ['warn', 'Only '.number_format($rowTotal).' rows. Results can change a lot between runs, so keep the model simple.'],
        $missingPercent <= 0
            ? ['ok', 'No empty cells.']
            : ($missingPercent <= 5
                ? ['ok', number_format($missingPercent, 1).'% of cells are empty. They will be filled in automatically.']
                : ['warn', number_format($missingPercent, 1).'% of cells are empty. They will be filled in, but results depend on how.']),
        $duplicatePercent <= 0
            ? ['ok', 'No duplicate rows.']
            : ['warn', number_format($duplicatePercent, 1).'% of rows are duplicates. Keep "Remove duplicate rows" switched on.'],
        $mixedColumns === 0
            ? ['ok', 'Every column holds one consistent type of value.']
            : ['warn', $mixedColumns.' column(s) mix numbers and text. Consider leaving them out as features.'],
        $numericColumnCount >= 2
            ? ['ok', $numericColumnCount.' number columns – clustering is also possible.']
            : ['warn', 'Fewer than two number columns, so clustering is not available.'],
        $suggestedTarget
            ? ['ok', 'Suggested target: '.$suggestedTarget.'. You can change it in Step 2.']
            : ['warn', 'No obvious target column. You will choose one in Step 2, or pick clustering.'],
    ];
@endphp

<div class="ml-layout">
    @include('partials.sidebar')
    <main class="ml-main">
        <header class="ml-head">
            <div>
                <h1 class="ml-title ds-page-title">{{ $dataset->name }}</h1>
                <p class="ml-subtitle">{{ $dataset->description ?? 'Review the structure, quality, and sample records before using this dataset.' }}</p>
                <div class="ml-subtitle-row">
                    <span class="ml-badge {{ $datasetType === 'system' ? 'good' : '' }}">
                        {{ $datasetType === 'system' ? 'Built-in dataset, read-only' : 'Uploaded dataset, private' }}
                    </span>
                </div>
            </div>
            <div class="ml-actions">
                <a class="ml-btn secondary" href="{{ route('student.model-development.index') }}">Choose Another Dataset</a>
                <a class="ml-btn secondary" href="{{ $downloadUrl }}">Download CSV</a>
            </div>
        </header>

        @if(session('success'))
            <div class="ml-alert">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="ml-alert error">{{ $errors->first() }}</div>
        @endif

        <div class="ml-roadmap-layout">
            <aside class="ml-roadmap-column">
                @include('student.model-development.partials.roadmap', [
                    'roadmapCurrent' => 1,
                    'roadmapCompletedThrough' => 0,
                    'roadmapErrorStep' => $errors->any() ? 1 : null,
                ])
            </aside>

            <div class="ml-roadmap-content">
                <section class="ml-card">
                    @include('student.model-development.partials.step-header', [
                        'stepNumber' => 1,
                        'stepTitle' => 'Is this the right dataset?',
                        'stepLead' => 'Skim the checklist and the sample rows below. When it looks right, continue to choose what to predict.',
                    ])

                    <ul class="ml-readiness" aria-label="Dataset readiness">
                        @foreach($readiness as [$state, $message])
                            <li><i class="{{ $state }}">{{ $state === 'ok' ? '✓' : '!' }}</i><span>{{ $message }}</span></li>
                        @endforeach
                    </ul>

                    <div class="ml-actions" style="margin-top:16px">
                        <a class="ml-btn" href="{{ $continueUrl }}">Use this dataset & continue →</a>
                    </div>

                    @include('student.model-development.partials.step-guide', ['guideStep' => 1])
                </section>

                <section class="ml-grid four" style="margin-top:16px">
                    <div class="ml-card ml-stat"><span>Rows</span><strong>{{ number_format($dataset->row_count) }}</strong></div>
                    <div class="ml-card ml-stat"><span>Columns</span><strong>{{ $dataset->column_count }}</strong></div>
                    <div class="ml-card ml-stat"><span>Suggested target</span><strong style="font-size:1rem;overflow-wrap:anywhere">{{ $dataset->target_column ?: 'Choose in Step 2' }}</strong></div>
                    <div class="ml-card ml-stat"><span>Suggested problem</span><strong style="font-size:1rem;overflow-wrap:anywhere">{{ ucfirst($dataset->problem_type ?: 'Choose in Step 4') }}</strong></div>
                </section>

                <section class="ml-section ml-grid two">
                    <div class="ml-card">
                        <div class="ml-section-head">
                            <div>
                                <h2 class="ml-section-title">Dataset Quality</h2>
                                <p class="ml-muted">Calculated from the stored data, not estimated.</p>
                            </div>
                            <div class="ml-quality">{{ number_format((float) ($quality?->quality_score ?? $dataset->quality_score ?? 0), 0) }}<span style="font-size:.875rem;font-weight:500;color:var(--ds-text-muted)">/100</span></div>
                        </div>
                        <div class="ml-meta">
                            <div><span>Missing values</span><strong>{{ number_format((float) ($summary['missing_percent'] ?? 0), 2) }}%</strong></div>
                            <div><span>Duplicate rows</span><strong>{{ number_format((float) ($summary['duplicate_percent'] ?? 0), 2) }}%</strong></div>
                            <div><span>Mixed-type columns</span><strong>{{ count((array) ($summary['mixed_type_columns'] ?? [])) }}</strong></div>
                            <div><span>Outliers</span><strong>{{ number_format((int) ($summary['outliers'] ?? 0)) }}</strong></div>
                            <div><span>Class balance</span><strong>{{ data_get($summary, 'class_balance.label', 'Not applicable') }}</strong></div>
                            <div><span>Quality grade</span><strong>{{ $quality?->grade ?? 'Not assessed' }}</strong></div>
                        </div>
                        @if($quality?->recommendations)
                            <h3 class="ml-section-title" style="font-size:.875rem;font-weight:600;margin-top:16px">Recommended preprocessing</h3>
                            <ul class="ml-list">@foreach($quality->recommendations as $recommendation)<li>{{ $recommendation }}</li>@endforeach</ul>
                        @endif
                    </div>

                    <div class="ml-card">
                        <h2 class="ml-section-title">Dataset Information</h2>
                        <div class="ml-meta">
                            <div><span>Version</span><strong>{{ $dataset->version_label ?? 'v1' }}</strong></div>
                            <div><span>Source</span><strong>{{ $dataset->source_name ?? 'User upload' }}</strong></div>
                            <div><span>Numerical columns</span><strong>{{ count((array) ($profile['numeric_columns'] ?? [])) }}</strong></div>
                            <div><span>Categorical columns</span><strong>{{ count((array) ($profile['categorical_columns'] ?? [])) }}</strong></div>
                        </div>
                        @if($datasetType === 'system')
                            <p class="ml-muted" style="font-size:.8125rem">Built-in datasets and benchmark artifacts are shared and read-only. Training creates your own model and never changes this dataset.</p>
                        @else
                            <p class="ml-muted" style="font-size:.8125rem">This dataset is stored under your account{{ $dataset->class_id ? ' and associated with a class' : '' }}. Other students cannot access it.</p>
                        @endif
                    </div>
                </section>

                <section class="ml-section">
                    <div class="ml-section-head">
                        <div>
                            <h2 class="ml-section-title">Columns You Can Use</h2>
                            <p class="ml-muted">Every column is a possible feature. ID-like and empty columns are flagged because they cannot help a model learn.</p>
                        </div>
                    </div>
                    <div class="ml-table-wrap">
                        <table class="ml-table">
                            <thead><tr><th>Column</th><th>Detected type</th><th>Suggested role</th><th>Missing</th><th>Unique</th><th>Type quality</th><th>Examples</th></tr></thead>
                            <tbody>
                            @foreach((array) ($profile['columns'] ?? []) as $columnName => $column)
                                <tr>
                                    <td><strong>{{ $columnName }}</strong></td>
                                    <td>{{ ucfirst((string) ($column['type'] ?? 'unknown')) }}</td>
                                    <td>
                                        @if($columnName === $suggestedTarget)
                                            <span class="ml-badge good">Suggested target</span>
                                        @elseif($column['is_identifier_like'] ?? false)
                                            <span class="ml-badge warn">ID – not usable</span>
                                        @elseif(($column['type'] ?? '') === 'empty')
                                            <span class="ml-badge warn">Empty – not usable</span>
                                        @else
                                            Feature
                                        @endif
                                    </td>
                                    <td>{{ number_format((float) ($column['missing_percent'] ?? 0), 2) }}%</td>
                                    <td>{{ number_format((int) ($column['unique_count'] ?? 0)) }}</td>
                                    <td>
                                        @if($column['has_mixed_types'] ?? false)
                                            <span class="ml-badge bad">{{ number_format((float) ($column['type_consistency_percent'] ?? 0), 1) }}% consistent</span>
                                        @else
                                            <span class="ml-badge good">Consistent</span>
                                        @endif
                                    </td>
                                    <td>{{ implode(', ', array_slice((array) ($column['sample_values'] ?? []), 0, 4)) ?: '—' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="ml-section">
                    <div class="ml-section-head">
                        <div>
                            <h2 class="ml-section-title">Dataset Preview</h2>
                            <p class="ml-muted">The first {{ count($preview) }} records from the stored canonical CSV.</p>
                        </div>
                    </div>
                    <div class="ml-table-wrap">
                        <table class="ml-table">
                            <thead><tr>@foreach(array_keys($preview[0] ?? []) as $column)<th>{{ $column }}</th>@endforeach</tr></thead>
                            <tbody>
                            @forelse($preview as $row)
                                <tr>@foreach($row as $value)<td>{{ $value === null || $value === '' ? '—' : $value }}</td>@endforeach</tr>
                            @empty
                                <tr><td class="ml-muted">Preview unavailable.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                @if($datasetType === 'system')
                    <section class="ml-section">
                        <div class="ml-section-head">
                            <div>
                                <h2 class="ml-section-title">Read-only System Benchmarks</h2>
                                <p class="ml-muted">Models DataSensei already trained on this data. After Step 8 you can compare your own score with them.</p>
                            </div>
                        </div>
                        <div class="ml-grid">
                            @forelse($benchmarks as $benchmark)
                                <article class="ml-card">
                                    <div class="ml-section-head">
                                        <h3 class="ml-section-title">{{ str($benchmark->algorithm_key)->replace('_', ' ')->title() }}</h3>
                                        <span class="ml-badge {{ $benchmark->is_primary ? 'good' : '' }}">Rank {{ $benchmark->benchmark_rank }}</span>
                                    </div>
                                    @php $benchmarkMetrics = (array) $benchmark->metrics; @endphp
                                    <div class="ml-meta">
                                        @foreach(array_slice(\App\Support\ModelDevelopmentGuide::displayMetrics((string) $dataset->problem_type, $benchmarkMetrics), 0, 4, true) as $key => $value)
                                            <div><span>{{ \App\Support\ModelDevelopmentGuide::metric((string) $key)['label'] }}</span><strong>{{ \App\Support\ModelDevelopmentGuide::formatMetric((string) $key, $value) }}</strong></div>
                                        @endforeach
                                    </div>
                                    <a class="ml-btn secondary" href="{{ route('student.model-development.models.show', ['model' => $benchmark->ml_model_id, 'step' => 'evaluate']) }}">View Benchmark Evaluation</a>
                                </article>
                            @empty
                                <div class="ml-card"><p class="ml-muted">No benchmark models are registered for this dataset.</p></div>
                            @endforelse
                        </div>
                    </section>
                @endif

                <div class="ml-step-navigation ml-section">
                    <a class="ml-btn secondary" href="{{ route('student.model-development.index') }}">← Back to Dataset Library</a>
                    <a class="ml-btn" href="{{ $continueUrl }}">Use this dataset & continue →</a>
                </div>

                @if($datasetType === 'user' && ! $dataset->models->count())
                    <details class="ml-advanced ml-section">
                        <summary>Dataset Management</summary>
                        <p class="ml-help">Delete is available only before this dataset has training or model history.</p>
                        <form method="POST" action="{{ route('student.model-development.user-datasets.destroy', $dataset) }}" onsubmit="return confirm('Delete this dataset permanently?')" style="margin-top:16px">
                            @csrf
                            @method('DELETE')
                            <button class="ml-btn danger" type="submit">Delete Dataset</button>
                        </form>
                    </details>
                @endif
            </div>
        </div>
    </main>
</div>
</body>
</html>
