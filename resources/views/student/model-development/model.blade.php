<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $model->name }} - DataSensei</title>
    @include('student.model-development.partials.styles')
</head>
<body>
@php
    $metrics = (array) $version->metrics;
    $predictionSchema = (array) data_get($version->explanations, 'prediction_schema', []);
    $predictions = $version->predictions;
    $trainingJob = $version->trainingJob;
    $datasetType = $model->dataset_id ? 'system' : 'user';
    $dataset = $model->dataset ?: $model->userDataset;
    $datasetPage = $datasetType === 'system'
        ? route('student.model-development.system-datasets.show', $dataset)
        : route('student.model-development.user-datasets.show', $dataset);
    $wizardBase = [
        'dataset_type' => $datasetType,
        'dataset_id' => $dataset->id,
    ];
    if ($trainingJob) $wizardBase['training_job_id'] = $trainingJob->id;

    $roadmapLinks = [
        1 => $datasetPage,
        8 => route('student.model-development.models.show', ['model' => $model, 'step' => 'evaluate']),
        9 => route('student.model-development.models.show', ['model' => $model, 'step' => 'predict']),
        10 => route('student.model-development.models.show', ['model' => $model, 'step' => 'save']),
    ];
    foreach (range(2, 6) as $step) {
        $roadmapLinks[$step] = route('student.model-development.wizard', array_merge($wizardBase, ['step' => $step]));
    }
    $roadmapLinks[7] = $trainingJob
        ? route('student.model-development.training.show', $trainingJob)
        : route('student.model-development.wizard', array_merge($wizardBase, ['step' => 7]));

    $roadmapCompletedThrough = match ($roadmapCurrent) {
        10 => 10,
        9 => 8,
        default => 7,
    };
    $isPercentMetric = static fn (string $key): bool => in_array(
        $key,
        ['accuracy', 'precision', 'recall', 'f1', 'roc_auc', 'average_precision'],
        true
    );
    $predictionResult = (array) session('prediction_result', []);
@endphp

<div class="ml-layout">
    @include('partials.sidebar')
    <main class="ml-main">
        <header class="ml-head">
            <div>
                <span class="ml-badge {{ $model->isSystemModel() ? 'good' : '' }}">
                    {{ $model->isSystemModel() ? 'System benchmark · Read-only' : 'User model · Private' }}
                </span>
                <h1 class="ml-title ds-page-title" style="margin-top:10px">{{ $model->name }}</h1>
                <p class="ml-subtitle">{{ str($model->algorithm_key)->replace('_', ' ')->title() }} · {{ ucfirst($model->problem_type) }} · Active {{ $version->version_label }}</p>
            </div>
            <a class="ml-btn secondary" href="{{ route('student.model-development.index') }}#model-history">Model History</a>
        </header>

        @if(session('success'))
            <div class="ml-alert">{{ session('success') }}</div>
        @endif
        @if($roadmapNotice)
            <div class="ml-alert error">{{ $roadmapNotice }}</div>
        @endif
        @if($errors->any())
            <div class="ml-alert error">{{ $errors->first() }}</div>
        @endif

        <div class="ml-roadmap-layout">
            <aside class="ml-roadmap-column">
                @include('student.model-development.partials.roadmap', [
                    'roadmapCurrent' => $roadmapCurrent,
                    'roadmapCompletedThrough' => $roadmapCompletedThrough,
                    'roadmapErrorStep' => $errors->any() ? $roadmapCurrent : null,
                    'roadmapLinks' => $roadmapLinks,
                ])
            </aside>

            <div class="ml-roadmap-content">
                <section class="ml-result-section" @if($roadmapCurrent !== 8) hidden @endif data-result-step="evaluate">
                    <div class="ml-card">
                        <span class="ml-step-kicker">Step 8 of 10</span>
                        <h2 class="ml-section-title">Evaluate the Trained Model</h2>
                        <p class="ml-muted">These measurements and charts were calculated from the actual training result. Use them to decide whether the model is useful before making a new prediction.</p>
                    </div>

                    <div class="ml-grid four" style="margin-top:16px">
                        @forelse($metrics as $key => $value)
                            @if(is_numeric($value) && $key !== 'mse')
                                <div class="ml-card ml-stat">
                                    <span>{{ str($key)->replace('_', ' ')->title() }}</span>
                                    <strong>{{ number_format((float) $value, $isPercentMetric($key) ? 2 : 4) }}{{ $isPercentMetric($key) ? '%' : '' }}</strong>
                                </div>
                            @endif
                        @empty
                            <div class="ml-card"><p class="ml-muted">No numeric metrics are available for this version.</p></div>
                        @endforelse
                    </div>

                    <section class="ml-section ml-grid two">
                        <div class="ml-card">
                            <h3 class="ml-section-title">What the Results Mean</h3>
                            <ul class="ml-list">
                                @forelse((array) data_get($version->explanations, 'educational', []) as $item)
                                    <li>{{ $item }}</li>
                                @empty
                                    <li>Evaluation explanations are generated from the model metrics and dataset quality characteristics.</li>
                                @endforelse
                            </ul>
                            <div class="ml-meta">
                                <div><span>Training time</span><strong>{{ number_format($version->training_time_ms / 1000, 3) }} seconds</strong></div>
                                <div><span>Features</span><strong>{{ count((array) $version->feature_names) }}</strong></div>
                                <div><span>Target</span><strong>{{ $version->target_column ?: 'No target' }}</strong></div>
                                <div><span>Runtime</span><strong>Python {{ $version->python_version ?: 'Unknown' }} · sklearn {{ $version->sklearn_version ?: 'Unknown' }}</strong></div>
                            </div>
                        </div>

                        <div class="ml-card">
                            <h3 class="ml-section-title">Feature Influence</h3>
                            @php($importance = (array) data_get($version->explanations, 'feature_importance', []))
                            <div class="ml-table-wrap" style="margin-top:14px">
                                <table class="ml-table">
                                    <thead><tr><th>Feature</th><th>Influence</th><th>Method</th></tr></thead>
                                    <tbody>
                                    @forelse(array_slice($importance, 0, 12) as $item)
                                        <tr>
                                            <td>{{ $item['feature'] }}</td>
                                            <td>{{ number_format(abs((float) $item['value']), 5) }}</td>
                                            <td>{{ str($item['kind'] ?? 'importance')->replace('_', ' ')->title() }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="ml-muted">This algorithm did not expose a stable feature-influence value.</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>

                    @if($comparison)
                        <section class="ml-section">
                            <div class="ml-section-head">
                                <div>
                                    <h3 class="ml-section-title">System Benchmark Comparison</h3>
                                    <p class="ml-muted">Compare this trained result with the read-only reference for the same built-in dataset.</p>
                                </div>
                            </div>
                            <div class="ml-card">
                                <div class="ml-table-wrap">
                                    <table class="ml-table">
                                        <thead><tr><th>Metric</th><th>Your model</th><th>System benchmark</th><th>Difference</th><th>Result</th></tr></thead>
                                        <tbody>
                                        @foreach($comparison['rows'] as $row)
                                            <tr>
                                                <td>{{ str($row['metric'])->replace('_', ' ')->title() }}</td>
                                                <td>{{ number_format((float) $row['user'], 3) }}</td>
                                                <td>{{ number_format((float) $row['system'], 3) }}</td>
                                                <td>{{ ($row['difference'] >= 0 ? '+' : '').number_format((float) $row['difference'], 3) }}</td>
                                                <td><span class="ml-badge {{ $row['status'] === 'better' ? 'good' : ($row['status'] === 'worse' ? 'bad' : '') }}">{{ ucfirst($row['status']) }}</span></td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <ul class="ml-list">@foreach($comparison['explanation'] as $item)<li>{{ $item }}</li>@endforeach</ul>
                            </div>
                        </section>
                    @endif

                    <section class="ml-section">
                        <div class="ml-section-head">
                            <div>
                                <h3 class="ml-section-title">Evaluation Charts</h3>
                                <p class="ml-muted">Open or download any chart generated for this model version.</p>
                            </div>
                        </div>
                        <div class="ml-grid two">
                            @forelse((array) $version->visualizations as $key => $path)
                                <article class="ml-card">
                                    <div class="ml-section-head">
                                        <h4 class="ml-section-title">{{ str($key)->replace('_', ' ')->title() }}</h4>
                                        <a class="ml-btn small secondary" href="{{ route('student.model-development.visualizations.show', [$version, $key, 'download' => 1]) }}">Download</a>
                                    </div>
                                    <img class="ml-chart" loading="lazy" src="{{ route('student.model-development.visualizations.show', [$version, $key]) }}" alt="{{ str($key)->replace('_', ' ')->title() }}">
                                </article>
                            @empty
                                <div class="ml-card"><p class="ml-muted">No charts are available for this model version.</p></div>
                            @endforelse
                        </div>
                    </section>

                    <div class="ml-step-navigation">
                        @if($trainingJob)
                            <a class="ml-btn secondary" href="{{ route('student.model-development.training.show', $trainingJob) }}">← Back to Train Model</a>
                        @else
                            <a class="ml-btn secondary" href="{{ $roadmapLinks[7] }}">← Back to Train Model</a>
                        @endif
                        <a class="ml-btn" href="{{ $roadmapLinks[9] }}">Continue to Predict →</a>
                    </div>
                </section>

                <section class="ml-result-section" @if($roadmapCurrent !== 9) hidden @endif data-result-step="predict">
                    <div class="ml-card">
                        <span class="ml-step-kicker">Step 9 of 10</span>
                        <h2 class="ml-section-title">Make a New Prediction</h2>
                        <p class="ml-muted">Enter values for the same features used during training. DataSensei runs the saved model artifact and records your result.</p>

                        @if($predictionSchema === [])
                            <div class="ml-alert error" style="margin-top:16px">This model version does not contain a prediction schema.</div>
                        @else
                            <form method="POST" action="{{ route('student.model-development.models.predict', $model) }}" style="margin-top:18px">
                                @csrf
                                <div class="ml-grid two">
                                    @foreach($predictionSchema as $feature => $definition)
                                        <div class="ml-field">
                                            <label class="ml-label" for="prediction-{{ $loop->index }}">{{ $feature }}</label>
                                            @if(($definition['type'] ?? '') === 'category' && count((array) ($definition['options'] ?? [])) <= 50)
                                                <select class="ml-select" id="prediction-{{ $loop->index }}" name="input_values[{{ $feature }}]">
                                                    <option value="">Missing value</option>
                                                    @foreach((array) $definition['options'] as $option)
                                                        <option value="{{ $option }}" @selected((string) old('input_values.'.$feature) === (string) $option)>{{ $option }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input
                                                    class="ml-input"
                                                    id="prediction-{{ $loop->index }}"
                                                    name="input_values[{{ $feature }}]"
                                                    value="{{ old('input_values.'.$feature) }}"
                                                    type="{{ ($definition['type'] ?? '') === 'number' ? 'number' : 'text' }}"
                                                    step="any"
                                                    placeholder="{{ ($definition['type'] ?? '') === 'number' ? 'Typical value: '.($definition['median'] ?? '') : 'Enter a value' }}">
                                            @endif
                                            <div class="ml-help">{{ ucfirst($definition['type'] ?? 'value') }} input</div>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="ml-btn" type="submit">Generate Prediction & Continue to Save →</button>
                            </form>
                        @endif
                    </div>

                    <section class="ml-section ml-card">
                        <h3 class="ml-section-title">Your Recent Predictions</h3>
                        <p class="ml-muted">Only predictions made under your account are shown here.</p>
                        <div class="ml-table-wrap" style="margin-top:14px">
                            <table class="ml-table">
                                <thead><tr><th>Result</th><th>Confidence / probabilities</th><th>Response time</th><th>Date</th></tr></thead>
                                <tbody>
                                @forelse($predictions as $prediction)
                                    <tr>
                                        <td><strong>{{ $prediction->predicted_value }}</strong></td>
                                        <td>{{ $prediction->probabilities ? collect($prediction->probabilities)->map(fn ($value, $key) => $key.': '.number_format((float) $value, 2).'%')->implode(' · ') : 'Not available' }}</td>
                                        <td>{{ $prediction->latency_ms }} ms</td>
                                        <td>{{ $prediction->created_at->format('M j, Y g:i A') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="ml-muted">No predictions yet. Complete the form above to unlock Save Model.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <div class="ml-step-navigation">
                        <a class="ml-btn secondary" href="{{ $roadmapLinks[8] }}">← Back to Evaluate</a>
                        @if($hasPrediction)
                            <a class="ml-btn" href="{{ $roadmapLinks[10] }}">Continue to Save Model →</a>
                        @else
                            <span class="ml-help">Make a prediction to unlock Save Model.</span>
                        @endif
                    </div>
                </section>

                <section class="ml-result-section" @if($roadmapCurrent !== 10) hidden @endif data-result-step="save">
                    <div class="ml-saved-milestone">
                        <span class="ml-step-kicker">Step 10 of 10</span>
                        <h2>{{ $model->isSystemModel() ? 'Reference Model Ready' : '✓ MODEL SAVED IN MODEL HISTORY' }}</h2>
                        <p class="ml-muted">
                            @if($model->isSystemModel())
                                This benchmark remains available as a shared read-only reference. Your prediction was recorded under your account.
                            @else
                                {{ $model->name }} {{ $version->version_label }} is active and ready for later predictions, comparison, reporting, or retraining.
                            @endif
                        </p>
                    </div>

                    @if($predictionResult !== [])
                        <div class="ml-alert" style="margin-top:16px">
                            <strong>Latest prediction: {{ $predictionResult['predicted_value'] ?? 'Completed' }}</strong>
                            @if(isset($predictionResult['confidence']))
                                <br>Confidence: {{ number_format((float) $predictionResult['confidence'], 2) }}%
                            @endif
                            @if(! empty($predictionResult['explanation']))
                                <ul class="ml-list">@foreach((array) $predictionResult['explanation'] as $item)<li>{{ $item }}</li>@endforeach</ul>
                            @endif
                        </div>
                    @endif

                    <section class="ml-section ml-grid two">
                        <div class="ml-card">
                            <h3 class="ml-section-title">Saved Version</h3>
                            <div class="ml-meta">
                                <div><span>Version</span><strong>{{ $version->version_label }}</strong></div>
                                <div><span>Status</span><strong>{{ ucfirst($version->status) }}</strong></div>
                                <div><span>Algorithm</span><strong>{{ str($model->algorithm_key)->replace('_', ' ')->title() }}</strong></div>
                                <div><span>Recent predictions</span><strong>{{ $predictions->count() }}</strong></div>
                            </div>
                            <div class="ml-actions">
                                <a class="ml-btn secondary" href="{{ route('student.model-development.models.report', $model) }}" target="_blank">Open Report</a>
                                @if(! $model->isSystemModel())
                                    <a class="ml-btn" href="{{ route('student.model-development.wizard', array_merge($wizardBase, ['step' => 2])) }}">Train New Version</a>
                                @endif
                            </div>
                        </div>

                        <div class="ml-card">
                            <h3 class="ml-section-title">Version History</h3>
                            <p class="ml-muted">Earlier versions remain saved when you change a configuration and train again.</p>
                            <div class="ml-table-wrap" style="margin-top:14px">
                                <table class="ml-table">
                                    <thead><tr><th>Version</th><th>Date</th><th>Status</th><th>Action</th></tr></thead>
                                    <tbody>
                                    @foreach($model->versions as $item)
                                        <tr>
                                            <td>{{ $item->version_label }}</td>
                                            <td>{{ $item->created_at->format('M j, Y g:i A') }}</td>
                                            <td>{{ $item->is_active ? 'Active' : 'Available' }}</td>
                                            <td>
                                                @if(! $model->isSystemModel() && ! $item->is_active)
                                                    <form method="POST" action="{{ route('student.model-development.models.versions.activate', [$model, $item]) }}">
                                                        @csrf
                                                        <button class="ml-btn small secondary" type="submit">Activate</button>
                                                    </form>
                                                @elseif($item->is_active)
                                                    <span class="ml-badge good">Current</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>

                    @if(! $model->isSystemModel())
                        <details class="ml-advanced ml-section">
                            <summary>Model Management</summary>
                            <p class="ml-help">Deleting removes this model and every saved version. This does not delete the source dataset.</p>
                            <form method="POST" action="{{ route('student.model-development.models.destroy', $model) }}" onsubmit="return confirm('Delete this model and every saved version? This cannot be undone.')" style="margin-top:14px">
                                @csrf
                                @method('DELETE')
                                <button class="ml-btn danger" type="submit">Delete Model</button>
                            </form>
                        </details>
                    @endif

                    <div class="ml-step-navigation">
                        <a class="ml-btn secondary" href="{{ $roadmapLinks[9] }}">← Back to Predict</a>
                        <a class="ml-btn" href="{{ route('student.model-development.index') }}#model-history">Finish & View Model History</a>
                    </div>
                </section>
            </div>
        </div>
    </main>
</div>
</body>
</html>
