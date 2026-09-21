<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $dataset->name }} — DataSensei</title>
    @include('student.model-development.partials.styles')
    @include('partials.page-head', ['pageDescription' => 'Train a real machine-learning model in four short steps and use it to make predictions.'])
</head>
<body>
@php
    $downloadUrl = $datasetType === 'system'
        ? route('student.model-development.system-datasets.download', $dataset)
        : route('student.model-development.user-datasets.download', $dataset);
    $continueUrl = route('student.model-development.wizard', [
        'dataset_type' => $datasetType,
        'dataset_id' => $dataset->id,
    ]);
    $term = fn (string $key, ?string $text = null) => \App\Support\ModelDevelopmentGlossary::term($key, $text);
    $preset = $datasetType === 'system' ? (array) (\App\Support\ModelDevelopmentOutcome::presets()[$dataset->slug] ?? []) : [];
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
            ? ['ok', number_format($rowTotal).' rows, enough examples to learn from and test on.']
            : ['warn', 'Only '.number_format($rowTotal).' rows. Results can change a lot between runs, so keep the model simple.'],
        $missingPercent <= 0
            ? ['ok', 'No empty cells.']
            : ($missingPercent <= 5
                ? ['ok', number_format($missingPercent, 1).'% of cells are empty. They will be filled in automatically.']
                : ['warn', number_format($missingPercent, 1).'% of cells are empty. They will be filled in, but results depend on how.']),
        $duplicatePercent <= 0
            ? ['ok', 'No duplicate rows.']
            : ['warn', number_format($duplicatePercent, 1).'% of rows are duplicates. DataSensei removes them before training.'],
        $mixedColumns === 0
            ? ['ok', 'Every column holds one consistent type of value.']
            : ['warn', $mixedColumns.' column(s) mix numbers and text. Consider leaving them out as clues.'],
        $numericColumnCount >= 2
            ? ['ok', $numericColumnCount.' number columns, so finding groups (clustering) is also possible.']
            : ['warn', 'Fewer than two number columns, so finding groups (clustering) is not available.'],
        $suggestedTarget
            ? ['ok', 'Suggested answer column: '.$suggestedTarget.'. You can change it on the next page.']
            : ['warn', 'No obvious answer column. You will choose one on the next page, or let the model find groups.'],
    ];
@endphp

<div class="ml-layout">
    @include('partials.sidebar')
    <main class="ml-main">
        <header class="ml-head">
            <div>
                <h1 class="ml-title ds-page-title">{{ $dataset->name }}</h1>
                <p class="ml-subtitle">{{ $preset['story'] ?? ($dataset->description ?? 'Look at the columns and a few sample rows before you use this data.') }} {{ $datasetType === 'system' ? 'Built-in data, read-only.' : 'Your upload, only you can see it.' }}</p>
            </div>
            <div class="ml-actions">
                <a class="ml-btn secondary" href="{{ route('student.model-development.index') }}">Choose other data</a>
                <a class="ml-btn secondary" href="{{ $downloadUrl }}">Download CSV</a>
            </div>
        </header>

        @if(session('success'))
            <div class="ml-alert">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="ml-alert error">{{ $errors->first() }}</div>
        @endif

        @include('student.model-development.partials.roadmap', [
            'roadmapCurrent' => 1,
            'roadmapCompletedThrough' => 0,
            'roadmapErrorStep' => $errors->any() ? 1 : null,
            'roadmapLinks' => [1 => route('student.model-development.index')],
        ])

        <div class="ml-flow">
                <section class="ml-card ml-raised">
                    <h2 class="ml-setup-q">{{ $preset['question'] ?? 'Is this the right data?' }}</h2>
                    <p class="ml-muted">Skim the checklist and the sample rows. If it looks right, carry on and choose what to predict.</p>

                    <ul class="ml-readiness" aria-label="Dataset readiness">
                        @foreach($readiness as [$state, $message])
                            <li><i class="{{ $state }}">{{ $state === 'ok' ? '✓' : '!' }}</i><span>{{ $message }}</span></li>
                        @endforeach
                    </ul>

                    <div class="ml-actions" style="margin-top:16px">
                        <a class="ml-btn" href="{{ $continueUrl }}">Use this data</a>
                    </div>

                    @include('student.model-development.partials.step-guide', ['guideStep' => 1])
                </section>

                <section class="ml-grid four" style="margin-top:16px">
                    <div class="ml-card ml-stat"><span>Rows</span><strong>{{ number_format($dataset->row_count) }}</strong></div>
                    <div class="ml-card ml-stat"><span>Columns</span><strong>{{ $dataset->column_count }}</strong></div>
                    <div class="ml-card ml-stat"><span>{{ $term('target', 'Answer column') }}</span><strong style="font-size:1rem;overflow-wrap:anywhere">{{ $dataset->target_column ?: 'You choose next' }}</strong></div>
                    <div class="ml-card ml-stat"><span>Kind of answer</span><strong style="font-size:1rem;overflow-wrap:anywhere">{{ $dataset->problem_type ? $term((string) $dataset->problem_type, ['classification' => 'A category', 'regression' => 'A number', 'clustering' => 'Groups'][$dataset->problem_type] ?? ucfirst($dataset->problem_type)) : 'Worked out for you' }}</strong></div>
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
                            <thead><tr><th>Column</th><th>Detected type</th><th>Role</th><th>Missing</th><th>Unique</th><th>Type quality</th><th>Examples</th></tr></thead>
                            <tbody>
                            @foreach((array) ($profile['columns'] ?? []) as $columnName => $column)
                                <tr>
                                    <td><strong>{{ $columnName }}</strong></td>
                                    <td>{{ ucfirst((string) ($column['type'] ?? 'unknown')) }}</td>
                                    <td>
                                        @if($columnName === $suggestedTarget)
                                            <span class="ml-plain-tag good">The answer</span>
                                        @elseif($column['is_identifier_like'] ?? false)
                                            <span class="ml-plain-tag warn">ID, not usable</span>
                                        @elseif(($column['type'] ?? '') === 'empty')
                                            <span class="ml-plain-tag warn">Empty, not usable</span>
                                        @else
                                            Clue
                                        @endif
                                    </td>
                                    <td>{{ number_format((float) ($column['missing_percent'] ?? 0), 2) }}%</td>
                                    <td>{{ number_format((int) ($column['unique_count'] ?? 0)) }}</td>
                                    <td>
                                        @if($column['has_mixed_types'] ?? false)
                                            <span class="ml-plain-tag bad">{{ number_format((float) ($column['type_consistency_percent'] ?? 0), 1) }}% consistent</span>
                                        @else
                                            <span class="ml-plain-tag good">Consistent</span>
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
                                <h2 class="ml-section-title">Reference models</h2>
                                <p class="ml-muted">Models DataSensei already trained on this data. Your results page compares your score with them.</p>
                            </div>
                        </div>
                        <div class="ml-grid">
                            @forelse($benchmarks as $benchmark)
                                <article class="ml-card">
                                    <div class="ml-section-head">
                                        <h3 class="ml-section-title">{{ str($benchmark->algorithm_key)->replace('_', ' ')->title() }}</h3>
                                        <span class="ml-plain-tag {{ $benchmark->is_primary ? 'good' : '' }}">Rank {{ $benchmark->benchmark_rank }}</span>
                                    </div>
                                    @php $benchmarkMetrics = (array) $benchmark->metrics; @endphp
                                    <div class="ml-meta">
                                        @foreach(array_slice(\App\Support\ModelDevelopmentGuide::displayMetrics((string) $dataset->problem_type, $benchmarkMetrics), 0, 4, true) as $key => $value)
                                            <div><span>{{ \App\Support\ModelDevelopmentGuide::metric((string) $key)['label'] }}</span><strong>{{ \App\Support\ModelDevelopmentGuide::formatMetric((string) $key, $value) }}</strong></div>
                                        @endforeach
                                    </div>
                                    <a class="ml-btn secondary" href="{{ route('student.model-development.models.show', ['model' => $benchmark->ml_model_id, 'step' => 'results']) }}">See its results</a>
                                </article>
                            @empty
                                <div class="ml-card"><p class="ml-muted">No benchmark models are registered for this dataset.</p></div>
                            @endforelse
                        </div>
                    </section>
                @endif

                <div class="ml-step-navigation ml-section">
                    <a class="ml-btn secondary" href="{{ route('student.model-development.index') }}">Back</a>
                    <a class="ml-btn" href="{{ $continueUrl }}">Use this data</a>
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
    </main>
</div>
</body>
</html>
