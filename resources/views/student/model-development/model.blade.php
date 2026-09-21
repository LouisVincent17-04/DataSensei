<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $model->name }} — DataSensei</title>
    @include('student.model-development.partials.styles')
    @include('partials.page-head', ['pageDescription' => 'Train a real machine-learning model in four short steps and use it to make predictions.'])
</head>
<body>
@php
    $guide = \App\Support\ModelDevelopmentGuide::class;
    $outcome = \App\Support\ModelDevelopmentOutcome::class;
    $roadmap = \App\Support\ModelDevelopmentRoadmap::class;
    $term = fn (string $key, ?string $text = null) => \App\Support\ModelDevelopmentGlossary::term($key, $text);

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
    $importance = array_slice((array) data_get($version->explanations, 'feature_importance', []), 0, 8);
    $maxImportance = max(array_merge([0.000001], array_map(static fn ($item) => abs((float) ($item['value'] ?? 0)), $importance)));
    $search = (array) data_get($metrics, 'model_search', []);
    $searchCandidates = array_values((array) ($search['candidates'] ?? []));
    $crossValidation = (array) data_get($metrics, 'cross_validation', []);

    $datasetType = $model->dataset_id ? 'system' : 'user';
    $dataset = $model->dataset ?: $model->userDataset;
    $wizardBase = ['dataset_type' => $datasetType, 'dataset_id' => $dataset->id];
    if ($trainingJob) $wizardBase['training_job_id'] = $trainingJob->id;

    $resultsUrl = route('student.model-development.models.show', ['model' => $model, 'step' => 'results']);
    $predictUrl = route('student.model-development.models.show', ['model' => $model, 'step' => 'predict']);
    $roadmapLinks = [
        1 => route('student.model-development.index'),
        2 => route('student.model-development.wizard', $wizardBase),
        3 => $resultsUrl,
        4 => $predictUrl,
    ];
    $onPredict = $roadmapCurrent === $roadmap::STEP_PREDICT;

    $configuredLabel = (string) config('hybrid_ml.algorithms.'.$model->algorithm_key.'.label', str($model->algorithm_key)->replace('_', ' ')->title());
    $selectedKey = (string) ($trainingSummary['selected_algorithm'] ?? $model->algorithm_key);
    $algorithmLabel = (string) ($trainingSummary['selected_label'] ?? $configuredLabel);
    $wasAutomatic = (bool) config('hybrid_ml.algorithms.'.$model->algorithm_key.'.is_automatic', false);

    // The dial shows the one number a beginner can picture.
    $dialValue = match ($problemType) {
        'classification' => is_numeric($metrics['accuracy'] ?? null) ? (float) $metrics['accuracy'] : null,
        'regression' => is_numeric($metrics['r2'] ?? null) ? max(0.0, (float) $metrics['r2'] * 100) : null,
        default => is_numeric($metrics['silhouette'] ?? null) ? max(0.0, (float) $metrics['silhouette'] * 100) : null,
    };
    $dialCaption = match ($problemType) {
        'classification' => 'right answers',
        'regression' => 'of the pattern explained',
        default => 'group separation',
    };

    $predictionResult = (array) session('prediction_result', []);
    $described = (array) ($predictionResult['described'] ?? []);

    // The most influential columns come first in the form; the rest are optional.
    $importanceOrder = [];
    foreach ((array) data_get($version->explanations, 'feature_importance', []) as $item) {
        $name = (string) ($item['feature'] ?? '');
        foreach (array_keys($predictionSchema) as $column) {
            if ($name === $column || str_starts_with($name, $column.'_')) {
                $importanceOrder[$column] = true;
            }
        }
    }
    $orderedColumns = array_values(array_unique(array_merge(array_keys($importanceOrder), array_keys($predictionSchema))));
    $mainColumns = count($orderedColumns) > 8 ? array_slice($orderedColumns, 0, 6) : $orderedColumns;
    $extraColumns = array_values(array_diff($orderedColumns, $mainColumns));
    $trim = static fn ($number) => rtrim(rtrim(number_format((float) $number, 4, '.', ''), '0'), '.');
@endphp

<div class="ml-layout">
    @include('partials.sidebar')
    <main class="ml-main">
        <header class="ml-head">
            <div>
                <h1 class="ml-title ds-page-title">{{ $model->name }}</h1>
                <p class="ml-subtitle">{{ $wording['question'] }} Learned with {{ $term(\App\Support\ModelDevelopmentGlossary::algorithmKey($selectedKey), $algorithmLabel) }}, {{ $version->version_label }}{{ $model->isSystemModel() ? ', a shared reference model' : '' }}.</p>
            </div>
            <div class="ml-actions">
                @if($onPredict)
                    <a class="ml-btn secondary" href="{{ $resultsUrl }}">See the results</a>
                @else
                    <a class="ml-btn" href="{{ $predictUrl }}">Make a prediction</a>
                @endif
                <a class="ml-btn secondary" href="{{ route('student.model-development.index') }}#model-history">My models</a>
            </div>
        </header>

        @if(session('success'))
            <div class="ml-alert">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="ml-alert error">{{ $errors->first() }}</div>
        @endif

        @include('student.model-development.partials.roadmap', [
            'roadmapCurrent' => $roadmapCurrent,
            'roadmapCompletedThrough' => $hasPrediction ? 4 : 3,
            'roadmapErrorStep' => null,
            'roadmapLinks' => $roadmapLinks,
        ])

        <div class="ml-flow">
            {{-- STEP 3: RESULTS --}}
            <section class="ml-result-section" @if($onPredict) hidden @endif data-result-step="evaluate">
                <section class="ml-score {{ $verdict['level'] }}" aria-label="How well the model did">
                    <div class="ml-score-dial" style="--fill:{{ $dialValue !== null ? round(min(100, $dialValue), 1) : 0 }}">
                        <span><b>{{ $dialValue !== null ? (int) round($dialValue).'%' : '–' }}</b><small>{{ $dialCaption }}</small></span>
                    </div>
                    <div>
                        <h2>{{ $resultSummary['headline'] }}</h2>
                        @foreach($resultSummary['lines'] as $line)
                            <p>{{ $line }}</p>
                        @endforeach
                        <p class="ml-score-verdict">{{ $verdict['title'] }}. {{ $verdict['summary'] }}</p>
                    </div>
                </section>

                @if($resultSummary['suspicious'])
                    <div class="ml-alert error">A score this close to perfect is unusual. Check that none of the clue columns is the answer in disguise ({{ $term('data_leakage', 'data leakage') }}).</div>
                @endif

                @if($resultSummary['mistakes'])
                    <section class="ml-card" style="margin-top:16px">
                        <h3 class="ml-section-title">The two kinds of mistake</h3>
                        <p class="ml-muted" style="font-size:.8125rem">They are not equally bad. Ask yourself which one would hurt more in real life.</p>
                        <div class="ml-mistakes">
                            <div class="ml-mistake">
                                <b>{{ number_format($resultSummary['mistakes']['missed']) }}</b>
                                <span>{{ $term('false_negative', 'Missed cases') }}: {{ $resultSummary['mistakes']['missed_text'] }}.</span>
                            </div>
                            <div class="ml-mistake">
                                <b>{{ number_format($resultSummary['mistakes']['false_alarms']) }}</b>
                                <span>{{ $term('false_positive', 'False alarms') }}: {{ $resultSummary['mistakes']['false_alarm_text'] }}.</span>
                            </div>
                        </div>
                    </section>
                @endif

                <div class="ml-train-bar ml-card" style="margin-top:16px">
                    <p class="ml-muted" style="flex:1 1 320px">The model is saved. Now ask it about something new.</p>
                    <a class="ml-btn" href="{{ $predictUrl }}">Make a prediction</a>
                </div>

                @if($searchCandidates !== [])
                    <details class="ml-details" style="margin-top:16px" open>
                        <summary><span>{{ $wasAutomatic ? 'The contest: which algorithm won?' : 'Settings that were tried' }}<small>Scored with {{ (int) ($search['folds'] ?? 0) }} practice exams on the training rows only</small></span></summary>
                        <div class="ml-details-body">
                            <p class="ml-muted" style="font-size:.8125rem">
                                Each algorithm sat the same practice exams ({{ $term('cross_validation', 'cross-validation') }}). When two are about equal, the simpler one wins because it is easier to explain.
                                @if($search['stopped_early'] ?? false) The contest stopped early to stay inside the time limit. @endif
                            </p>
                            <ul class="ml-contest">
                                @php
                                    $isPercentScore = ($search['scoring'] ?? '') === 'accuracy';
                                    // Scores are usually close together, so the bars start just below the lowest one.
                                    $scores = array_map(static fn ($item) => (float) ($item['score'] ?? 0) * ($isPercentScore ? 1 : 100), $searchCandidates);
                                    $barFloor = max(0.0, min($scores) - 8.0);
                                    $barSpan = max(1.0, max($scores) - $barFloor);
                                @endphp
                                @foreach($searchCandidates as $candidate)
                                    @php
                                        $score = (float) ($candidate['score'] ?? 0);
                                        $width = ((($isPercentScore ? $score : $score * 100) - $barFloor) / $barSpan) * 100;
                                    @endphp
                                    <li class="{{ ($candidate['selected'] ?? false) ? 'won' : '' }}">
                                        <span>{{ $term(\App\Support\ModelDevelopmentGlossary::algorithmKey((string) $candidate['algorithm_key']), (string) $candidate['label']) }}{{ ($candidate['selected'] ?? false) ? ', chosen' : '' }}</span>
                                        <span class="bar"><i style="width:{{ round(max(2, min(100, $width)), 1) }}%"></i></span>
                                        <span class="val">{{ $isPercentScore ? number_format($score, 1).'%' : number_format($score, 3) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </details>
                @endif

                @if($importance !== [])
                    <details class="ml-details" style="margin-top:16px" open>
                        <summary><span>Which clues mattered most?<small>Longer bars influenced the answer more</small></span></summary>
                        <div class="ml-details-body">
                            <ul class="ml-contest">
                                @foreach($importance as $item)
                                    @php
                                        $influence = abs((float) ($item['value'] ?? 0));
                                        $rawName = (string) ($item['feature'] ?? 'Feature');
                                        $niceName = $outcome::field($wording, $rawName)['label'];
                                    @endphp
                                    <li>
                                        <span>{{ $niceName }}</span>
                                        <span class="bar"><i style="width:{{ round(($influence / $maxImportance) * 100) }}%;background:var(--accent)"></i></span>
                                        <span class="val">{{ (int) round(($influence / $maxImportance) * 100) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            <p class="ml-help">This is {{ $term('feature_importance', 'feature importance') }}, scaled so the strongest clue is 100. Influence is not proof that one thing causes another.</p>
                        </div>
                    </details>
                @endif

                <details class="ml-details" style="margin-top:16px">
                    <summary><span>All the numbers<small>Every score, the mistakes table, comparison and charts</small></span></summary>
                    <div class="ml-details-body">
                        <div class="ml-grid four">
                            @forelse($displayMetrics as $key => $value)
                                @php $metricInfo = $guide::metric((string) $key); @endphp
                                <div class="ml-card ml-stat">
                                    <span>{{ $term((string) $key, $metricInfo['label']) }}</span>
                                    <strong>{{ $guide::formatMetric((string) $key, $value) }}</strong>
                                    @if($metricInfo['better'])
                                        <small>{{ $metricInfo['better'] === 'higher' ? 'Higher is better' : 'Lower is better' }}</small>
                                    @endif
                                </div>
                            @empty
                                <p class="ml-muted">No scores were stored for this version.</p>
                            @endforelse
                        </div>

                        @if(is_numeric($crossValidation['mean'] ?? null) && (int) ($crossValidation['folds'] ?? 0) > 1)
                            <p class="ml-muted" style="font-size:.8125rem">
                                {{ $term('cross_validation', 'Cross-validation') }} over {{ (int) $crossValidation['folds'] }} rounds gave
                                <strong>{{ ($crossValidation['scoring'] ?? '') === 'f1_weighted' ? number_format((float) $crossValidation['mean'] * 100, 1).'% F1' : number_format(abs((float) $crossValidation['mean']), 3).' typical miss' }}</strong>.
                                If this is close to the score above, the result was not a lucky split.
                            </p>
                        @endif

                        @if($confusionLabels !== [] && $confusionValues !== [])
                            <div>
                                <h3 class="ml-section-title">{{ $term('confusion_matrix', 'Confusion matrix') }}</h3>
                                <p class="ml-muted" style="font-size:.8125rem">Rows are the real answer and columns are what the model said. Green is correct, red is a mistake.</p>
                                <div class="ml-cm-wrap">
                                    <table class="ml-cm">
                                        <thead>
                                        <tr>
                                            <th class="row">Real ↓ / Model said →</th>
                                            @foreach($confusionLabels as $label)
                                                <th>{{ $wording['labels'][(string) $label]['short'] ?? $label }}</th>
                                            @endforeach
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($confusionValues as $rowIndex => $row)
                                            <tr>
                                                <th class="row">{{ $wording['labels'][(string) ($confusionLabels[$rowIndex] ?? '')]['short'] ?? ($confusionLabels[$rowIndex] ?? $rowIndex) }}</th>
                                                @foreach((array) $row as $columnIndex => $count)
                                                    <td class="{{ (int) $count > 0 ? ($rowIndex === $columnIndex ? 'hit' : 'miss') : '' }}">{{ (int) $count }}</td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        @if($clusterSizes !== [])
                            <div>
                                <h3 class="ml-section-title">How big is each group?</h3>
                                <div class="ml-meta">
                                    @foreach($clusterSizes as $clusterName => $clusterCount)
                                        <div><span>{{ $clusterName }}</span><strong>{{ number_format((int) $clusterCount) }} rows</strong></div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div>
                            <h3 class="ml-section-title">How it was trained</h3>
                            <div class="ml-meta">
                                @if(isset($trainingSummary['train_rows']))
                                    <div><span>{{ $term('training_rows', 'Training rows') }}</span><strong>{{ number_format((int) $trainingSummary['train_rows']) }}</strong></div>
                                    <div><span>{{ $term('test_rows', 'Test rows') }}</span><strong>{{ (int) ($trainingSummary['test_rows'] ?? 0) > 0 ? number_format((int) $trainingSummary['test_rows']) : 'None (finding groups)' }}</strong></div>
                                @endif
                                <div><span>{{ $term('algorithm', 'Algorithm') }}</span><strong>{{ $algorithmLabel }}{{ $wasAutomatic ? ' (chosen automatically)' : '' }}</strong></div>
                                <div><span>{{ $term('target', 'Answer column') }}</span><strong>{{ $version->target_column ?: 'None' }}</strong></div>
                                <div><span>{{ $term('feature', 'Clue columns') }}</span><strong>{{ count((array) $version->feature_names) }}</strong></div>
                                <div><span>{{ $term('scaling', 'Scaling') }}</span><strong>{{ ($trainingSummary['scaling_applied'] ?? false) ? 'Yes' : 'Not needed' }}</strong></div>
                                <div><span>Training time</span><strong>{{ number_format(($version->training_time_ms ?? 0) / 1000, 1) }} seconds</strong></div>
                                <div><span>Software</span><strong>Python {{ $version->python_version ?: '?' }}, scikit-learn {{ $version->sklearn_version ?: '?' }}</strong></div>
                            </div>
                            @if((array) data_get($version->explanations, 'educational', []) !== [])
                                <ul class="ml-list">
                                    @foreach((array) data_get($version->explanations, 'educational', []) as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        @if($comparison)
                            <div>
                                <h3 class="ml-section-title">Compared with DataSensei's reference model</h3>
                                <div class="ml-table-wrap">
                                    <table class="ml-table">
                                        <thead><tr><th>Score</th><th>Yours</th><th>Reference</th><th>Difference</th><th></th></tr></thead>
                                        <tbody>
                                        @foreach($comparison['rows'] as $row)
                                            <tr>
                                                <td>{{ $term((string) $row['metric'], $guide::metric((string) $row['metric'])['label']) }}</td>
                                                <td>{{ $guide::formatMetric((string) $row['metric'], $row['user']) }}</td>
                                                <td>{{ $guide::formatMetric((string) $row['metric'], $row['system']) }}</td>
                                                <td>{{ ($row['difference'] >= 0 ? '+' : '').number_format((float) $row['difference'], 3) }}</td>
                                                <td><span class="ml-plain-tag {{ $row['status'] === 'better' ? 'good' : ($row['status'] === 'worse' ? 'bad' : '') }}">{{ ucfirst($row['status']) }}</span></td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        @if((array) $version->visualizations !== [])
                            <div>
                                <h3 class="ml-section-title">Charts</h3>
                                <div class="ml-grid two">
                                    @foreach((array) $version->visualizations as $key => $path)
                                        <article class="ml-card">
                                            <div class="ml-section-head">
                                                <h4 class="ml-section-title">{{ str($key)->replace('_', ' ')->ucfirst() }}</h4>
                                                <a class="ml-btn small secondary" href="{{ route('student.model-development.visualizations.show', [$version, $key, 'download' => 1]) }}">Download</a>
                                            </div>
                                            <img class="ml-chart" loading="lazy" src="{{ route('student.model-development.visualizations.show', [$version, $key]) }}" alt="{{ str($key)->replace('_', ' ')->ucfirst() }}">
                                            <p class="ml-chart-caption">{{ $guide::chartGuide((string) $key) }}</p>
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </details>

                <details class="ml-details" style="margin-top:16px">
                    <summary><span>Version History<small>Train again, switch version, open the report or delete</small></span></summary>
                    <div class="ml-details-body">
                        <div class="ml-actions">
                            <a class="ml-btn secondary" href="{{ route('student.model-development.models.report', $model) }}" target="_blank" rel="noopener">Open the report</a>
                            @if(! $model->isSystemModel())
                                <a class="ml-btn secondary" href="{{ route('student.model-development.wizard', $wizardBase) }}">Change the set-up and train again</a>
                            @endif
                        </div>
                        <div class="ml-table-wrap">
                            <table class="ml-table">
                                <thead><tr><th>{{ $term('version', 'Version') }}</th><th>Trained</th><th>Status</th><th></th></tr></thead>
                                <tbody>
                                @foreach($model->versions as $item)
                                    <tr>
                                        <td>{{ $item->version_label }}</td>
                                        <td>{{ $item->created_at->format('M j, Y g:i A') }}</td>
                                        <td>{{ $item->is_active ? 'In use' : 'Saved' }}</td>
                                        <td>
                                            @if(! $model->isSystemModel() && ! $item->is_active)
                                                <form method="POST" action="{{ route('student.model-development.models.versions.activate', [$model, $item]) }}">
                                                    @csrf
                                                    <button class="ml-btn small secondary" type="submit">Use this one</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{-- The controller also requires ownership, so an instructor
                             looking at a student's class-linked model never sees a
                             button that would only be refused. --}}
                        @if(! $model->isSystemModel() && (int) $model->user_id === (int) auth()->id())
                            <form method="POST" action="{{ route('student.model-development.models.destroy', $model) }}" onsubmit="return confirm('Delete this model and every saved version? This cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <p class="ml-help">Deleting removes this model and all of its versions. The data it learned from is kept.</p>
                                <button class="ml-btn danger" type="submit" style="margin-top:8px">Delete this model</button>
                            </form>
                        @endif
                    </div>
                </details>

                @include('student.model-development.partials.step-guide', ['guideStep' => 3])
            </section>

            {{-- STEP 4: PREDICT --}}
            <section class="ml-result-section" @if(! $onPredict) hidden @endif data-result-step="predict">
                @if($described !== [])
                    <section class="ml-answer {{ $described['tone'] ?? 'neutral' }}" id="answer" aria-live="polite">
                        <p class="ml-answer-q">{{ $described['question'] }}</p>
                        <h2>{{ $described['headline'] }}</h2>
                        <p>{{ $described['detail'] }}</p>

                        @if(($described['confidence'] ?? null) !== null)
                            <div class="ml-sure">
                                <div class="ml-sure-head">
                                    <span>How sure is the model? ({{ $term('confidence', 'confidence') }})</span>
                                    <strong>{{ $described['confidence_label'] }}, {{ lcfirst((string) $described['confidence_text']) }}</strong>
                                </div>
                                <ul class="ml-chances">
                                    @foreach((array) $described['chances'] as $chance)
                                        <li class="{{ $chance['chosen'] ? 'chosen' : '' }}">
                                            <span>{{ $chance['label'] }}</span>
                                            <span class="bar"><i style="width:{{ $chance['percent'] }}%"></i></span>
                                            <span class="val">{{ number_format((float) $chance['percent'], 0) }}%</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @elseif(! empty($described['confidence_text']))
                            <p style="margin-top:10px">{{ $described['confidence_text'] }}</p>
                        @endif

                        @if(($predictionResult['known_answer'] ?? null) !== null)
                            <p style="margin-top:12px"><strong>What really happened: {{ $predictionResult['known_answer'] }}.</strong>
                                @if(($predictionResult['agrees'] ?? null) === true) The model got this one right.
                                @elseif(($predictionResult['agrees'] ?? null) === false) The model got this one wrong. Even a good model misses sometimes.
                                @endif
                            </p>
                        @endif

                        <p class="ml-answer-caution">
                            {{ $described['caution'] ?? 'This is an estimate based on past examples, not a guarantee.' }}
                            Change a value below and predict again to see what moves the answer.
                        </p>
                    </section>
                @endif

                <section class="ml-card ml-raised" style="margin-top:16px">
                    <h2 class="ml-setup-q" style="margin-bottom:4px">{{ $wording['question'] }}</h2>
                    <p class="ml-muted">Describe {{ $wording['subject'] }} and the model will answer. Anything you leave empty is filled with a typical value.</p>

                    @if($predictionSchema === [])
                        <div class="ml-alert error" style="margin-top:16px">This version cannot make predictions because its input list is missing. Train it again to fix this.</div>
                    @else
                        <form method="POST" action="{{ route('student.model-development.models.predict', $model) }}" style="margin-top:16px" id="prediction-form">
                            @csrf
                            <div class="ml-examples">
                                <span>Quick start:</span>
                                @foreach($examples as $index => $example)
                                    <button class="ml-btn small secondary" type="button" data-example="{{ $index }}">{{ $example['title'] }}</button>
                                @endforeach
                                <button class="ml-btn small secondary" type="button" id="fill-typical">Typical values</button>
                                <button class="ml-btn small secondary" type="button" id="clear-values">Clear</button>
                            </div>
                            <p class="ml-example-note" id="example-note" hidden></p>
                            <input type="hidden" name="known_answer" id="known_answer" value="">

                            @foreach(['main' => $mainColumns, 'extra' => $extraColumns] as $group => $groupColumns)
                                @continue($groupColumns === [])
                                @if($group === 'extra')
                                    <details class="ml-advanced" style="margin-top:16px" id="extra-fields">
                                        <summary>{{ count($extraColumns) }} more details (optional)</summary>
                                        <p class="ml-help">These mattered less to this model. Leave them empty to use typical values.</p>
                                @endif
                                <div class="ml-grid two" style="margin-top:16px">
                                    @foreach($groupColumns as $feature)
                                        @php
                                            $definition = (array) ($predictionSchema[$feature] ?? []);
                                            $fieldType = (string) ($definition['type'] ?? '');
                                            $options = array_values((array) ($definition['options'] ?? []));
                                            $choices = array_values((array) ($definition['choices'] ?? []));
                                            $minimum = $definition['minimum'] ?? null;
                                            $maximum = $definition['maximum'] ?? null;
                                            $median = $definition['median'] ?? null;
                                            $isList = ($fieldType === 'category' && $options !== []) || ($fieldType === 'number' && $choices !== []);
                                            $listValues = $fieldType === 'category' ? $options : $choices;
                                            $typical = $fieldType === 'number'
                                                ? ($median !== null ? (($definition['whole'] ?? false) ? (string) (int) round((float) $median) : $trim($median)) : '')
                                                : ($options[0] ?? '');
                                            $field = $outcome::field($wording, (string) $feature);
                                            $inputId = 'prediction-'.md5((string) $feature);
                                            $current = old('input_values.'.$feature);
                                        @endphp
                                        <div class="ml-field">
                                            <label class="ml-label" for="{{ $inputId }}">{{ $field['label'] }}</label>
                                            @if($isList)
                                                <select class="ml-select" id="{{ $inputId }}" name="input_values[{{ $feature }}]" data-feature="{{ $feature }}" data-typical="{{ $typical }}">
                                                    <option value="">Not sure (use a typical value)</option>
                                                    @foreach($listValues as $option)
                                                        <option value="{{ $option }}" @selected($current !== null && (string) $current === (string) $option)>{{ $option }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input
                                                    class="ml-input"
                                                    id="{{ $inputId }}"
                                                    name="input_values[{{ $feature }}]"
                                                    data-feature="{{ $feature }}"
                                                    value="{{ $current }}"
                                                    type="{{ $fieldType === 'number' ? 'number' : 'text' }}"
                                                    step="any"
                                                    inputmode="{{ $fieldType === 'number' ? 'decimal' : 'text' }}"
                                                    data-typical="{{ $typical }}"
                                                    @if($fieldType === 'number' && is_numeric($minimum)) data-min="{{ $minimum }}" @endif
                                                    @if($fieldType === 'number' && is_numeric($maximum)) data-max="{{ $maximum }}" @endif
                                                    placeholder="{{ $typical !== '' ? 'Typical: '.$typical : '' }}">
                                            @endif
                                            @if($field['help'] !== '')
                                                <div class="ml-field-help">{{ $field['help'] }}</div>
                                            @endif
                                            @if(! $isList && $fieldType === 'number' && is_numeric($minimum) && is_numeric($maximum))
                                                <div class="ml-field-range"><span>Usually {{ $trim($minimum) }} to {{ $trim($maximum) }}</span><span class="ml-field-name">{{ $feature }}</span></div>
                                                <div class="ml-range-warn" data-range-warning>Outside anything the model has seen, so the answer is less reliable.</div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                @if($group === 'extra')
                                    </details>
                                @endif
                            @endforeach

                            <div class="ml-train-bar" style="margin-top:18px">
                                <span class="ml-help" style="margin:0">Your model is already saved. Predict as often as you like.</span>
                                <button class="ml-btn" type="submit" id="predict-button">Predict</button>
                            </div>
                        </form>
                    @endif
                </section>

                @if($predictions->isNotEmpty())
                    <details class="ml-details" style="margin-top:16px">
                        <summary><span>Your earlier predictions<small>Only you can see these</small></span></summary>
                        <div class="ml-details-body">
                            <div class="ml-table-wrap">
                                <table class="ml-table">
                                    <thead><tr><th>Answer</th><th>How sure</th><th>When</th></tr></thead>
                                    <tbody>
                                    @foreach($predictions as $prediction)
                                        @php
                                            $pastLabel = (string) $prediction->predicted_value;
                                            $pastText = $problemType === 'classification'
                                                ? ($wording['labels'][$pastLabel]['short'] ?? $pastLabel)
                                                : ($problemType === 'regression' && is_numeric($pastLabel) ? $outcome::formatNumber((float) $pastLabel) : $pastLabel);
                                            $pastSure = $prediction->probabilities ? max(array_map('floatval', (array) $prediction->probabilities)) : null;
                                        @endphp
                                        <tr>
                                            <td><strong>{{ $pastText }}</strong></td>
                                            <td>{{ $pastSure !== null ? number_format($pastSure, 0).'%' : 'Not available' }}</td>
                                            <td>{{ $prediction->created_at->format('M j, g:i A') }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </details>
                @endif

                @include('student.model-development.partials.step-guide', ['guideStep' => 4])
            </section>
        </div>
    </main>
</div>

<script>
(() => {
    const form = document.getElementById('prediction-form');
    if (!form) return;

    const examples = @json($examples);
    const fields = [...form.querySelectorAll('[name^="input_values["]')];
    const note = document.getElementById('example-note');
    const extra = document.getElementById('extra-fields');
    const known = document.getElementById('known_answer');

    const checkRange = input => {
        const warning = input.parentElement.querySelector('[data-range-warning]');
        if (!warning) return;
        const value = input.value.trim();
        const number = Number(value);
        const outside = value !== '' && Number.isFinite(number) && (number < Number(input.dataset.min) || number > Number(input.dataset.max));
        warning.classList.toggle('visible', outside);
    };

    const setValue = (field, value) => {
        if (field.tagName === 'SELECT') {
            const match = [...field.options].find(option => option.value === String(value) || (value !== '' && Number(option.value) === Number(value)));
            field.value = match ? match.value : '';
        } else if (field.type === 'number') {
            const number = Number(value);
            field.value = value !== '' && Number.isFinite(number) ? String(Math.round(number * 10000) / 10000) : '';
        } else {
            field.value = value;
        }
        checkRange(field);
    };

    form.querySelectorAll('[data-example]').forEach(button => button.addEventListener('click', () => {
        const example = examples[Number(button.dataset.example)];
        if (!example) return;
        fields.forEach(field => setValue(field, example.values[field.dataset.feature] ?? ''));
        if (extra) extra.open = true;
        known.value = example.raw_answer ?? '';
        note.hidden = false;
        note.textContent = '';
        note.append(`${example.title} is a real row from the data. `);
        if (example.answer !== null) {
            const strong = document.createElement('strong');
            strong.textContent = example.answer;
            note.append('What really happened: ', strong, '. Press Predict and see whether the model agrees. The model may have studied this row, so treat it as a quick check rather than a test.');
        }
    }));

    document.getElementById('fill-typical')?.addEventListener('click', () => {
        fields.forEach(field => setValue(field, field.dataset.typical ?? ''));
        known.value = '';
        note.hidden = true;
    });

    document.getElementById('clear-values')?.addEventListener('click', () => {
        fields.forEach(field => setValue(field, ''));
        known.value = '';
        note.hidden = true;
    });

    fields.forEach(field => {
        // Once a value is edited it is no longer the real row, so its real answer no longer applies.
        field.addEventListener('input', () => { checkRange(field); known.value = ''; note.hidden = true; });
        checkRange(field);
    });
    if (extra && [...extra.querySelectorAll('[name^="input_values["]')].some(field => field.value !== '')) extra.open = true;

    form.addEventListener('submit', event => {
        if (fields.every(field => field.value.trim() === '')) {
            event.preventDefault();
            window.alert('Fill in at least one value, or press "Typical values" to start.');
            return;
        }
        const button = document.getElementById('predict-button');
        button.disabled = true;
        button.textContent = 'Thinking…';
    });
})();
</script>
</body>
</html>
