<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $model->name }} — DataSensei</title>
    @include('student.model-development.partials.styles')
    @include('partials.page-head', ['pageDescription' => 'Build, evaluate, and save a real machine-learning model in ten guided steps.'])
</head>
<body>
@php
    $guide = \App\Support\ModelDevelopmentGuide::class;
    $metrics = (array) $version->metrics;
    $problemType = (string) $model->problem_type;
    $displayMetrics = $guide::displayMetrics($problemType, $metrics);
    $verdict = $guide::verdict($problemType, $metrics);
    $predictionSchema = (array) data_get($version->explanations, 'prediction_schema', []);
    $predictions = $version->predictions;
    $trainingJob = $version->trainingJob;
    $trainingSummary = (array) (data_get($version->explanations, 'training_summary') ?: data_get($model->metadata, 'training_summary', []));
    $confusion = (array) data_get($metrics, 'confusion_matrix', []);
    $confusionLabels = array_values((array) ($confusion['labels'] ?? []));
    $confusionValues = array_values((array) ($confusion['values'] ?? []));
    $clusterSizes = (array) data_get($metrics, 'cluster_sizes', []);
    $importance = array_slice((array) data_get($version->explanations, 'feature_importance', []), 0, 12);
    $maxImportance = max(array_merge([0.000001], array_map(static fn ($item) => abs((float) ($item['value'] ?? 0)), $importance)));
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
    $predictionResult = (array) session('prediction_result', []);
    $algorithmLabel = (string) config('hybrid_ml.algorithms.'.$model->algorithm_key.'.label', str($model->algorithm_key)->replace('_', ' ')->title());
@endphp

<div class="ml-layout">
    @include('partials.sidebar')
    <main class="ml-main">
        <header class="ml-head">
            <div>
                <h1 class="ml-title ds-page-title">{{ $model->name }}</h1>
                <div class="ml-subtitle-row">
                    <p class="ml-subtitle">{{ $algorithmLabel }}, {{ ucfirst($problemType) }}, Active {{ $version->version_label }}</p>
                    <span class="ml-badge {{ $model->isSystemModel() ? 'good' : '' }}">
                        {{ $model->isSystemModel() ? 'System benchmark, read-only' : 'Your model, private' }}
                    </span>
                </div>
            </div>
            <div class="ml-actions">
                <a class="ml-btn secondary" href="{{ route('student.model-development.models.report', $model) }}" target="_blank" rel="noopener">Open Report</a>
                <a class="ml-btn secondary" href="{{ route('student.model-development.index') }}#model-history">Model History</a>
            </div>
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
                {{-- STEP 8: EVALUATE --}}
                <section class="ml-result-section" @if($roadmapCurrent !== 8) hidden @endif data-result-step="evaluate">
                    <div class="ml-card">
                        @include('student.model-development.partials.step-header', [
                            'stepNumber' => 8,
                            'stepTitle' => 'How well did your model do?',
                            'stepLead' => $problemType === 'clustering'
                                ? 'Clustering has no right answers, so these scores describe how clearly the groups are separated. Start with the quick verdict, then look at the details.'
                                : 'These scores were measured on test rows the model never saw during training. Start with the quick verdict, then look at the details.',
                        ])
                        @include('student.model-development.partials.step-guide', ['guideStep' => 8])
                    </div>

                    <section class="ml-verdict {{ $verdict['level'] }}" style="margin-top:16px" aria-label="Quick verdict">
                        <div class="ml-verdict-score">
                            <strong>{{ $verdict['value'] }}</strong>
                            <span>{{ $verdict['metric'] }}</span>
                        </div>
                        <div>
                            <h3>{{ $verdict['title'] }}</h3>
                            <p>{{ $verdict['summary'] }}</p>
                            <ul>
                                @foreach($verdict['next_steps'] as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                            <div class="ml-verdict-foot">This quick rating is a classroom rule of thumb. Whether a score is "good enough" depends on the real-world use.</div>
                        </div>
                    </section>

                    <div class="ml-grid four" style="margin-top:16px">
                        @forelse($displayMetrics as $key => $value)
                            @php $metricInfo = $guide::metric((string) $key); @endphp
                            <div class="ml-card ml-stat">
                                <span>{{ $metricInfo['label'] }}</span>
                                <strong>{{ $guide::formatMetric((string) $key, $value) }}</strong>
                                <small>{{ $metricInfo['plain'] }}</small>
                                @if($metricInfo['better'])
                                    <span class="ml-better">{{ $metricInfo['better'] === 'higher' ? 'Higher is better' : 'Lower is better' }}</span>
                                @endif
                            </div>
                        @empty
                            <div class="ml-card"><p class="ml-muted">No numeric metrics are available for this version.</p></div>
                        @endforelse
                    </div>

                    <section class="ml-section ml-grid {{ $importance === [] && $problemType === 'clustering' ? '' : 'two' }}" @if($importance === [] && $problemType === 'clustering') style="grid-template-columns:1fr" @endif>
                        <div class="ml-card">
                            <h3 class="ml-section-title">What the results mean</h3>
                            <ul class="ml-list">
                                @forelse((array) data_get($version->explanations, 'educational', []) as $item)
                                    <li>{{ $item }}</li>
                                @empty
                                    <li>Evaluation explanations are generated from the model metrics and dataset quality characteristics.</li>
                                @endforelse
                            </ul>
                            <div class="ml-meta">
                                @if(isset($trainingSummary['train_rows']))
                                    <div><span>Rows used to learn</span><strong>{{ number_format((int) $trainingSummary['train_rows']) }}</strong></div>
                                    <div><span>Rows used to test</span><strong>{{ (int) ($trainingSummary['test_rows'] ?? 0) > 0 ? number_format((int) $trainingSummary['test_rows']) : 'All rows (clustering)' }}</strong></div>
                                @endif
                                <div><span>Training time</span><strong>{{ number_format(($version->training_time_ms ?? 0) / 1000, 3) }} seconds</strong></div>
                                <div><span>Features used</span><strong>{{ count((array) $version->feature_names) }}</strong></div>
                                <div><span>Target</span><strong>{{ $version->target_column ?: 'No target' }}</strong></div>
                                <div><span>Runtime</span><strong>Python {{ $version->python_version ?: 'Unknown' }}, sklearn {{ $version->sklearn_version ?: 'Unknown' }}</strong></div>
                            </div>
                        </div>

                        @if($importance !== [] || $problemType !== 'clustering')
                        <div class="ml-card">
                            <h3 class="ml-section-title">Which columns mattered most?</h3>
                            <p class="ml-muted" style="font-size:.8125rem">Longer bars had more influence on this model's decisions. Influence is not the same as cause.</p>
                            <div class="ml-table-wrap" style="margin-top:16px">
                                <table class="ml-table">
                                    <thead><tr><th>Feature</th><th>Influence</th><th>Method</th></tr></thead>
                                    <tbody>
                                    @forelse($importance as $item)
                                        @php $influence = abs((float) ($item['value'] ?? 0)); @endphp
                                        <tr>
                                            <td>{{ $item['feature'] ?? 'Feature' }}</td>
                                            <td>
                                                <div style="display:flex;align-items:center;gap:8px">
                                                    <div class="ml-progress" style="width:64px;height:8px;flex:none"><div style="width:{{ round(($influence / $maxImportance) * 100) }}%"></div></div>
                                                    {{ number_format($influence, 4) }}
                                                </div>
                                            </td>
                                            <td>{{ ['native_importance' => 'Built-in', 'coefficient_magnitude' => 'Coefficient', 'importance' => 'Permutation'][$item['kind'] ?? 'importance'] ?? str($item['kind'] ?? 'importance')->replace('_', ' ')->title() }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="ml-muted">This algorithm does not report which columns mattered most.</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                    </section>

                    @if($confusionLabels !== [] && $confusionValues !== [])
                        <section class="ml-section ml-card">
                            <h3 class="ml-section-title">Where did the model get confused?</h3>
                            <p class="ml-muted" style="font-size:.8125rem">Each row is the real class and each column is what the model predicted. Green cells are correct; red cells are mistakes.</p>
                            <div class="ml-cm-wrap">
                                <table class="ml-cm">
                                    <thead>
                                    <tr>
                                        <th class="row">Real ↓ / Predicted →</th>
                                        @foreach($confusionLabels as $label)
                                            <th>{{ $label }}</th>
                                        @endforeach
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($confusionValues as $rowIndex => $row)
                                        <tr>
                                            <th class="row">{{ $confusionLabels[$rowIndex] ?? $rowIndex }}</th>
                                            @foreach((array) $row as $columnIndex => $count)
                                                <td class="{{ (int) $count > 0 ? ($rowIndex === $columnIndex ? 'hit' : 'miss') : '' }}">{{ (int) $count }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    @endif

                    @if($clusterSizes !== [])
                        <section class="ml-section ml-card">
                            <h3 class="ml-section-title">How big is each group?</h3>
                            <p class="ml-muted" style="font-size:.8125rem">Cluster numbers are just names. Look at the rows in each group to decide what they have in common.</p>
                            <div class="ml-meta">
                                @foreach($clusterSizes as $clusterName => $clusterCount)
                                    <div><span>{{ $clusterName }}</span><strong>{{ number_format((int) $clusterCount) }} rows</strong></div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if($comparison)
                        <section class="ml-section">
                            <div class="ml-section-head">
                                <div>
                                    <h3 class="ml-section-title">How do you compare with the reference model?</h3>
                                    <p class="ml-muted">DataSensei trained a reference model on the same built-in dataset. "Better" already accounts for whether a higher or lower number is better.</p>
                                </div>
                            </div>
                            <div class="ml-card">
                                <div class="ml-table-wrap">
                                    <table class="ml-table">
                                        <thead><tr><th>Metric</th><th>Your model</th><th>Reference</th><th>Difference</th><th>Result</th></tr></thead>
                                        <tbody>
                                        @foreach($comparison['rows'] as $row)
                                            <tr>
                                                <td>{{ $guide::metric((string) $row['metric'])['label'] }}</td>
                                                <td>{{ $guide::formatMetric((string) $row['metric'], $row['user']) }}</td>
                                                <td>{{ $guide::formatMetric((string) $row['metric'], $row['system']) }}</td>
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
                                <h3 class="ml-section-title">Evaluation charts</h3>
                                <p class="ml-muted">Each chart has a short note on how to read it. Open or download any chart for your report.</p>
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
                                    <p class="ml-chart-caption">{{ $guide::chartGuide((string) $key) }}</p>
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
                        <a class="ml-btn" href="{{ $roadmapLinks[9] }}">Try a prediction →</a>
                    </div>
                </section>

                {{-- STEP 9: PREDICT --}}
                <section class="ml-result-section" @if($roadmapCurrent !== 9) hidden @endif data-result-step="predict">
                    <div class="ml-card">
                        @include('student.model-development.partials.step-header', [
                            'stepNumber' => 9,
                            'stepTitle' => 'Try your model on a new example',
                            'stepLead' => 'Fill in the same columns the model learned from. The ranges show what the model saw during training.',
                        ])

                        @if($predictionSchema === [])
                            <div class="ml-alert error" style="margin-top:16px">This model version does not contain a prediction schema.</div>
                        @else
                            <form method="POST" action="{{ route('student.model-development.models.predict', $model) }}" style="margin-top:16px" id="prediction-form">
                                @csrf
                                <div class="ml-toolbar" style="margin:0 0 16px">
                                    <button class="ml-btn small secondary" type="button" id="fill-typical">Fill with typical values</button>
                                    <button class="ml-btn small secondary" type="button" id="clear-values">Clear all</button>
                                    <span class="ml-counter">Empty fields are filled in automatically, just like during training.</span>
                                </div>
                                <div class="ml-grid two">
                                    @foreach($predictionSchema as $feature => $definition)
                                        @php
                                            $fieldType = (string) ($definition['type'] ?? '');
                                            $options = array_values((array) ($definition['options'] ?? []));
                                            $minimum = $definition['minimum'] ?? null;
                                            $maximum = $definition['maximum'] ?? null;
                                            $median = $definition['median'] ?? null;
                                            $typical = $fieldType === 'number' ? $median : ($options[0] ?? '');
                                        @endphp
                                        <div class="ml-field">
                                            <label class="ml-label" for="prediction-{{ $loop->index }}">{{ $feature }}</label>
                                            @if($fieldType === 'category' && count($options) <= 50)
                                                <select class="ml-select" id="prediction-{{ $loop->index }}" name="input_values[{{ $feature }}]" data-typical="{{ $typical }}">
                                                    <option value="">Leave empty</option>
                                                    @foreach($options as $option)
                                                        <option value="{{ $option }}" @selected((string) old('input_values.'.$feature) === (string) $option)>{{ $option }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="ml-help">Category, most common: {{ $options[0] ?? '—' }}</div>
                                            @else
                                                <input
                                                    class="ml-input"
                                                    id="prediction-{{ $loop->index }}"
                                                    name="input_values[{{ $feature }}]"
                                                    value="{{ old('input_values.'.$feature) }}"
                                                    type="{{ $fieldType === 'number' ? 'number' : 'text' }}"
                                                    step="any"
                                                    data-typical="{{ $typical }}"
                                                    @if($fieldType === 'number' && is_numeric($minimum)) data-min="{{ $minimum }}" @endif
                                                    @if($fieldType === 'number' && is_numeric($maximum)) data-max="{{ $maximum }}" @endif
                                                    placeholder="{{ $fieldType === 'number' ? ($median !== null ? 'Typical: '.rtrim(rtrim(number_format((float) $median, 4, '.', ''), '0'), '.') : 'Enter a number') : 'Enter a value' }}">
                                                @if($fieldType === 'number' && is_numeric($minimum) && is_numeric($maximum))
                                                    <div class="ml-range">
                                                        <span>Seen in training: {{ rtrim(rtrim(number_format((float) $minimum, 4, '.', ''), '0'), '.') }} to {{ rtrim(rtrim(number_format((float) $maximum, 4, '.', ''), '0'), '.') }}</span>
                                                        <span>Number</span>
                                                    </div>
                                                    <div class="ml-range-warn" data-range-warning>This value is outside what the model saw, so the prediction may be less reliable.</div>
                                                @else
                                                    <div class="ml-help">{{ $fieldType === 'number' ? 'Number' : 'Text' }} input</div>
                                                @endif
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <button class="ml-btn" type="submit" id="predict-button">Predict & Continue to Save →</button>
                            </form>
                        @endif

                        @include('student.model-development.partials.step-guide', ['guideStep' => 9])
                    </div>

                    <section class="ml-section ml-card">
                        <h3 class="ml-section-title">Your recent predictions</h3>
                        <p class="ml-muted">Only predictions made under your account are shown here. Confidence shows how sure the model was, not a guarantee.</p>
                        <div class="ml-table-wrap" style="margin-top:16px">
                            <table class="ml-table">
                                <thead><tr><th>Result</th><th>Confidence / probabilities</th><th>Response time</th><th>Date</th></tr></thead>
                                <tbody>
                                @forelse($predictions as $prediction)
                                    <tr>
                                        <td><strong>{{ $prediction->predicted_value }}</strong></td>
                                        <td>{{ $prediction->probabilities ? collect($prediction->probabilities)->map(fn ($value, $key) => $key.': '.number_format((float) $value, 2).'%')->implode(', ') : 'Not available' }}</td>
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

                {{-- STEP 10: SAVE --}}
                <section class="ml-result-section" @if($roadmapCurrent !== 10) hidden @endif data-result-step="save">
                    <div class="ml-saved-milestone">
                        @include('student.model-development.partials.step-header', [
                            'stepNumber' => 10,
                            'stepTitle' => $model->isSystemModel() ? 'Reference model ready' : 'Model saved in your history',
                            'stepLead' => $model->isSystemModel()
                                ? 'This benchmark stays available as a shared read-only reference. Your prediction was recorded under your account.'
                                : $model->name.' '.$version->version_label.' is active and ready for later predictions, comparison, reporting, or retraining.',
                        ])

                        @if(! $model->isSystemModel())
                            <ul class="ml-recap" aria-label="What you completed">
                                <li>Chose a dataset and a target</li>
                                <li>Selected {{ count((array) $version->feature_names) }} features</li>
                                <li>Trained a {{ $algorithmLabel }} model</li>
                                <li>Checked it on unseen test rows</li>
                                <li>Made {{ $predictions->count() }} {{ \Illuminate\Support\Str::plural('prediction', $predictions->count()) }}</li>
                                <li>Saved {{ $version->version_label }} in Model History</li>
                            </ul>
                        @endif
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
                            <h3 class="ml-section-title">Saved version</h3>
                            <div class="ml-meta">
                                <div><span>Version</span><strong>{{ $version->version_label }}</strong></div>
                                <div><span>Status</span><strong>{{ ucfirst($version->status) }}</strong></div>
                                <div><span>Algorithm</span><strong>{{ $algorithmLabel }}</strong></div>
                                <div><span>Recent predictions</span><strong>{{ $predictions->count() }}</strong></div>
                            </div>
                            <p class="ml-muted" style="font-size:.8125rem">Ideas to keep learning: train the same setup with a different algorithm, or remove one feature and see how the score changes.</p>
                            <div class="ml-actions" style="margin-top:12px">
                                <a class="ml-btn secondary" href="{{ route('student.model-development.models.report', $model) }}" target="_blank" rel="noopener">Open Report</a>
                                @if(! $model->isSystemModel())
                                    <a class="ml-btn" href="{{ route('student.model-development.wizard', array_merge($wizardBase, ['step' => 6])) }}">Try Another Algorithm</a>
                                    <a class="ml-btn secondary" href="{{ route('student.model-development.wizard', array_merge($wizardBase, ['step' => 2])) }}">Train New Version</a>
                                @endif
                            </div>
                        </div>

                        <div class="ml-card">
                            <h3 class="ml-section-title">Version History</h3>
                            <p class="ml-muted">Earlier versions remain saved when you change a configuration and train again.</p>
                            <div class="ml-table-wrap" style="margin-top:16px">
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

                    @include('student.model-development.partials.step-guide', ['guideStep' => 10])

                    @if(! $model->isSystemModel())
                        <details class="ml-advanced ml-section">
                            <summary>Model Management</summary>
                            <p class="ml-help">Deleting removes this model and every saved version. This does not delete the source dataset.</p>
                            <form method="POST" action="{{ route('student.model-development.models.destroy', $model) }}" onsubmit="return confirm('Delete this model and every saved version? This cannot be undone.')" style="margin-top:16px">
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

<script>
(() => {
    const form = document.getElementById('prediction-form');
    if (!form) return;

    const fields = [...form.querySelectorAll('[name^="input_values["]')];

    const checkRange = input => {
        const warning = input.parentElement.querySelector('[data-range-warning]');
        if (!warning) return;
        const value = input.value.trim();
        const min = Number(input.dataset.min);
        const max = Number(input.dataset.max);
        const number = Number(value);
        const outside = value !== '' && Number.isFinite(number) && (number < min || number > max);
        warning.classList.toggle('visible', outside);
    };

    document.getElementById('fill-typical')?.addEventListener('click', () => {
        fields.forEach(field => {
            const typical = field.dataset.typical ?? '';
            if (typical === '') return;
            if (field.tagName === 'INPUT' && field.type === 'number') {
                const number = Number(typical);
                field.value = Number.isFinite(number) ? String(Math.round(number * 10000) / 10000) : '';
            } else {
                field.value = typical;
            }
            checkRange(field);
        });
    });

    document.getElementById('clear-values')?.addEventListener('click', () => {
        fields.forEach(field => {
            field.value = '';
            checkRange(field);
        });
    });

    fields.forEach(field => {
        field.addEventListener('input', () => checkRange(field));
        checkRange(field);
    });

    form.addEventListener('submit', event => {
        if (fields.every(field => field.value.trim() === '')) {
            event.preventDefault();
            window.alert('Enter at least one value, or use "Fill with typical values" to start.');
            return;
        }
        const button = document.getElementById('predict-button');
        button.disabled = true;
        button.textContent = 'Predicting...';
    });
})();
</script>
</body>
</html>
