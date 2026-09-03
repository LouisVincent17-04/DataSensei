<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Train Model - DataSensei</title>
    @include('student.model-development.partials.styles')
</head>
<body>
@php
    $configuration = (array) $job->configuration;
    $trainingRecommendation = (array) data_get($configuration, 'recommendation', []);
    $isRecommendedModel = (bool) ($trainingRecommendation['is_recommended_model'] ?? false);
    $algorithmLabel = (string) data_get(
        $configuration,
        'learning_mode.algorithm.label',
        str($job->algorithm_key)->replace('_', ' ')->title()
    );
    $datasetType = $job->dataset_id ? 'system' : 'user';
    $dataset = $job->dataset ?: $job->userDataset;
    $datasetPage = $datasetType === 'system'
        ? route('student.model-development.system-datasets.show', $dataset)
        : route('student.model-development.user-datasets.show', $dataset);
    $wizardParameters = [
        'dataset_type' => $datasetType,
        'dataset_id' => $dataset->id,
        'training_job_id' => $job->id,
    ];
    $roadmapLinks = [1 => $datasetPage];
    foreach (range(2, 7) as $step) {
        $roadmapLinks[$step] = route('student.model-development.wizard', array_merge($wizardParameters, ['step' => $step]));
    }
    $roadmapLinks[8] = $job->ml_model_id
        ? route('student.model-development.models.show', ['model' => $job->ml_model_id, 'step' => 'evaluate'])
        : '#';
    $isComplete = $job->status === 'completed';
    $hasFailed = in_array($job->status, ['failed', 'cancelled'], true);
    $metrics = (array) data_get($job->result, 'metrics', []);
    $durationSeconds = $job->duration_ms !== null ? round($job->duration_ms / 1000, 3) : null;
    $stageDefinitions = [
        ['start' => 0, 'end' => 15, 'label' => 'Preparing dataset'],
        ['start' => 15, 'end' => 35, 'label' => 'Processing features'],
        ['start' => 35, 'end' => 58, 'label' => 'Training '.$algorithmLabel],
        ['start' => 58, 'end' => 90, 'label' => 'Evaluating model'],
        ['start' => 90, 'end' => 100, 'label' => 'Finalizing results'],
    ];
    $formatMetric = static function (string $key, mixed $value): string {
        if (! is_numeric($value)) return (string) $value;
        $isPercent = in_array($key, ['accuracy', 'precision', 'recall', 'f1', 'roc_auc', 'average_precision'], true);
        return number_format((float) $value, $isPercent ? 2 : 4).($isPercent ? '%' : '');
    };
@endphp

<div class="ml-layout">
    @include('partials.sidebar')
    <main class="ml-main">
        <header class="ml-head">
            <div>
                <span class="ml-badge {{ $isRecommendedModel ? 'good' : '' }}">{{ $isRecommendedModel ? '🌟 Recommended model' : 'Selected model' }}</span>
                <h1 class="ml-title ds-page-title" id="training-title" style="margin-top:10px">
                    {{ $isComplete ? 'Model Trained Successfully' : ($hasFailed ? 'Training Needs Attention' : 'Training Your Model') }}
                </h1>
                <p class="ml-subtitle"><strong>{{ $algorithmLabel }}</strong> · {{ $job->model_name }}</p>
            </div>
            <a class="ml-btn secondary" href="{{ route('student.model-development.index') }}">Model History</a>
        </header>

        @if(session('success'))
            <div class="ml-alert">{{ session('success') }}</div>
        @endif

        <div class="ml-roadmap-layout">
            <aside class="ml-roadmap-column">
                @include('student.model-development.partials.roadmap', [
                    'roadmapCurrent' => $isComplete ? 8 : 7,
                    'roadmapCompletedThrough' => $isComplete ? 7 : 6,
                    'roadmapErrorStep' => $hasFailed ? 7 : null,
                    'roadmapLinks' => $roadmapLinks,
                ])
            </aside>

            <div class="ml-roadmap-content">
                <section class="ml-card ml-training-milestone">
                    <span class="ml-step-kicker">Step 7 of 10</span>
                    <div class="ml-section-head">
                        <div>
                            <h2 class="ml-section-title" id="stage">{{ $job->stage }}</h2>
                            <p class="ml-muted">This progress comes directly from the training worker. It is not a simulated timer.</p>
                        </div>
                        <span class="ml-badge" id="status-badge">
                            <span class="ml-status-dot {{ $job->status }}"></span>
                            <span id="status-text">{{ ucfirst($job->status) }}</span>
                        </span>
                    </div>

                    <p class="ml-help" id="retry-details" style="display:{{ $job->status === 'retrying' ? 'block' : 'none' }};margin-top:8px">
                        @if($job->status === 'retrying')
                            Attempt {{ $job->attempt_number }} did not complete. The next attempt is scheduled {{ optional($job->next_retry_at)->diffForHumans() }}.
                        @endif
                    </p>

                    <div style="display:flex;justify-content:space-between;gap:14px;align-items:center;margin-top:18px">
                        <strong id="progress-label">{{ $job->progress }}% complete</strong>
                        <span class="ml-help" id="worker-stage">Worker status: {{ $job->stage }}</span>
                    </div>
                    <div class="ml-progress" style="margin-top:9px">
                        <div id="progress-bar" style="width:{{ $job->progress }}%"></div>
                    </div>

                    <div class="ml-training-stage-list" id="training-stages" aria-label="Real training stages">
                        @foreach($stageDefinitions as $index => $definition)
                            @php
                                $stageComplete = $isComplete || $job->progress >= $definition['end'];
                                $stageCurrent = ! $isComplete && $job->progress >= $definition['start'] && $job->progress < $definition['end'];
                                $stageError = $hasFailed && $stageCurrent;
                            @endphp
                            <div
                                class="ml-training-stage {{ $stageComplete ? 'complete' : ($stageError ? 'error' : ($stageCurrent ? 'current' : '')) }}"
                                data-training-stage="{{ $index }}"
                                data-start="{{ $definition['start'] }}"
                                data-end="{{ $definition['end'] }}">
                                <span class="ml-training-stage-marker">{{ $stageComplete ? '✓' : ($stageError ? '!' : $index + 1) }}</span>
                                <span>{{ $definition['label'] }}</span>
                            </div>
                        @endforeach
                    </div>

                    @if($isRecommendedModel && ! empty($trainingRecommendation['reason']))
                        <div class="ml-inline-note"><strong>Why this model?</strong> {{ $trainingRecommendation['reason'] }}</div>
                    @endif

                    <div id="error-box" class="ml-alert error" style="display:{{ $hasFailed ? 'block' : 'none' }}">
                        {{ $job->error_message ?: ($job->status === 'cancelled' ? 'Training was cancelled before a model was created.' : '') }}
                    </div>
                    <div id="failure-actions" class="ml-actions" style="display:{{ $hasFailed ? 'flex' : 'none' }};margin-bottom:18px">
                        <a class="ml-btn secondary" href="{{ route('student.model-development.wizard', array_merge($wizardParameters, ['step' => 2])) }}">Review Configuration</a>
                    </div>

                    <div class="ml-selection-summary" aria-label="Training configuration">
                        <div class="ml-summary-row"><span>Dataset</span><strong>{{ $dataset->name }}</strong></div>
                        <div class="ml-summary-row"><span>Problem Type</span><strong>{{ ucfirst($job->problem_type) }}</strong></div>
                        <div class="ml-summary-row"><span>Target</span><strong>{{ data_get($configuration, 'target_column') ?: 'No target (clustering)' }}</strong></div>
                        <div class="ml-summary-row"><span>Algorithm</span><strong>{{ $algorithmLabel }}</strong></div>
                        <div class="ml-summary-row full"><span>Features</span><div class="ml-summary-list">@foreach((array) data_get($configuration, 'features', []) as $feature)<span>{{ $feature }}</span>@endforeach</div></div>
                    </div>

                    <section id="training-success" class="ml-success-milestone {{ $isComplete ? 'visible' : '' }}" aria-live="polite">
                        <h2 class="ml-success-title">✓ MODEL TRAINED SUCCESSFULLY</h2>
                        <p class="ml-muted" style="margin-top:7px">The saved artifact and evaluation results were produced by the completed training job.</p>
                        <div class="ml-success-metrics" id="success-metrics">
                            @if($durationSeconds !== null)
                                <div class="ml-success-metric"><span>Duration</span><strong>{{ number_format($durationSeconds, 3) }} seconds</strong></div>
                            @endif
                            @foreach(array_slice($metrics, 0, 3, true) as $key => $value)
                                @if(is_numeric($value))
                                    <div class="ml-success-metric">
                                        <span>{{ str($key)->replace('_', ' ')->title() }}</span>
                                        <strong>{{ $formatMetric($key, $value) }}</strong>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <a
                            class="ml-btn"
                            id="evaluation-link"
                            href="{{ $job->ml_model_id ? route('student.model-development.models.show', ['model' => $job->ml_model_id, 'step' => 'evaluate']) : '#' }}">
                            View Evaluation →
                        </a>
                    </section>
                </section>
            </div>
        </div>
    </main>
</div>

<script>
(() => {
    const terminalStatuses = new Set(['completed', 'failed', 'cancelled']);
    const roadmap = document.querySelector('[data-model-roadmap]');
    const metricPercentKeys = new Set(['accuracy', 'precision', 'recall', 'f1', 'roc_auc', 'average_precision']);
    const initial = {
        status: @json($job->status),
        progress: Number(@json($job->progress)),
        stage: @json($job->stage),
        error: @json($job->error_message),
        evaluation_url: @json($job->ml_model_id ? route('student.model-development.models.show', ['model' => $job->ml_model_id, 'step' => 'evaluate']) : null),
        metrics: @json($metrics),
        duration_seconds: @json($durationSeconds),
        attempt_number: Number(@json($job->attempt_number)),
        next_retry_at: @json(optional($job->next_retry_at)->toIso8601String()),
    };

    const formatLabel = key => String(key).replaceAll('_', ' ').replace(/\b\w/g, character => character.toUpperCase());
    const formatMetric = (key, value) => {
        const number = Number(value);
        if (!Number.isFinite(number)) return String(value ?? '—');
        return number.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: metricPercentKeys.has(key) ? 2 : 4,
        }) + (metricPercentKeys.has(key) ? '%' : '');
    };

    const renderStages = (progress, status) => {
        const completed = status === 'completed';
        const failed = status === 'failed' || status === 'cancelled';
        document.querySelectorAll('[data-training-stage]').forEach((stage, index) => {
            const start = Number(stage.dataset.start);
            const end = Number(stage.dataset.end);
            const isComplete = completed || progress >= end;
            const isCurrent = !completed && progress >= start && progress < end;
            const isError = failed && isCurrent;
            stage.classList.toggle('complete', isComplete);
            stage.classList.toggle('current', isCurrent && !isError);
            stage.classList.toggle('error', isError);
            stage.querySelector('.ml-training-stage-marker').textContent = isComplete ? '✓' : (isError ? '!' : String(index + 1));
        });
    };

    const renderSuccessMetrics = data => {
        const container = document.getElementById('success-metrics');
        const entries = Object.entries(data.metrics || {}).filter(([, value]) => Number.isFinite(Number(value))).slice(0, 3);
        const cards = [];

        if (data.duration_seconds !== null && data.duration_seconds !== '' && Number.isFinite(Number(data.duration_seconds))) {
            const card = document.createElement('div');
            card.className = 'ml-success-metric';
            const label = document.createElement('span');
            label.textContent = 'Duration';
            const value = document.createElement('strong');
            value.textContent = `${Number(data.duration_seconds).toLocaleString(undefined, {maximumFractionDigits: 3})} seconds`;
            card.append(label, value);
            cards.push(card);
        }

        entries.forEach(([key, metric]) => {
            const card = document.createElement('div');
            card.className = 'ml-success-metric';
            const label = document.createElement('span');
            label.textContent = formatLabel(key);
            const value = document.createElement('strong');
            value.textContent = formatMetric(key, metric);
            card.append(label, value);
            cards.push(card);
        });

        container.replaceChildren(...cards);
    };

    const render = data => {
        const status = String(data.status || 'queued');
        const progress = Math.max(0, Math.min(100, Number(data.progress) || 0));
        const completed = status === 'completed';
        const failed = status === 'failed' || status === 'cancelled';

        document.getElementById('progress-bar').style.width = `${progress}%`;
        document.getElementById('progress-label').textContent = completed ? '100% complete' : `${progress}% complete`;
        document.getElementById('stage').textContent = data.stage || 'Waiting for the machine-learning worker';
        document.getElementById('worker-stage').textContent = `Worker status: ${data.stage || status}`;
        document.getElementById('status-text').textContent = status.charAt(0).toUpperCase() + status.slice(1);
        document.querySelector('#status-badge .ml-status-dot').className = `ml-status-dot ${status}`;
        const retryDetails = document.getElementById('retry-details');
        if (status === 'retrying') {
            const attempt = Math.max(1, Number(data.attempt_number) || 1);
            const retryAt = data.next_retry_at ? new Date(data.next_retry_at) : null;
            const scheduled = retryAt && !Number.isNaN(retryAt.getTime())
                ? ` at ${retryAt.toLocaleTimeString([], {hour: 'numeric', minute: '2-digit'})}`
                : ' shortly';
            retryDetails.textContent = `Attempt ${attempt} did not complete. The next attempt is scheduled${scheduled}.`;
            retryDetails.style.display = 'block';
        } else {
            retryDetails.textContent = '';
            retryDetails.style.display = 'none';
        }
        renderStages(progress, status);

        const errorBox = document.getElementById('error-box');
        const failureActions = document.getElementById('failure-actions');
        if (failed) {
            errorBox.textContent = data.error || (status === 'cancelled' ? 'Training was cancelled before a model was created.' : 'Training could not be completed. Review the configuration and try again.');
            errorBox.style.display = 'block';
            failureActions.style.display = 'flex';
            document.getElementById('training-title').textContent = 'Training Needs Attention';
            window.DataSenseiModelRoadmap.update(roadmap, {current: 7, completed: 6, error: 7});
        } else {
            errorBox.style.display = 'none';
            failureActions.style.display = 'none';
        }

        if (completed) {
            document.getElementById('training-title').textContent = 'Model Trained Successfully';
            document.getElementById('training-success').classList.add('visible');
            renderSuccessMetrics(data);
            if (data.evaluation_url) {
                document.getElementById('evaluation-link').href = data.evaluation_url;
                const roadmapEvaluationLink = roadmap.querySelector('[data-roadmap-step="8"] [data-roadmap-link]');
                if (roadmapEvaluationLink) roadmapEvaluationLink.href = data.evaluation_url;
            }
            window.DataSenseiModelRoadmap.update(roadmap, {current: 8, completed: 7, error: null});
        }
    };

    const poll = async () => {
        try {
            const response = await fetch(@json(route('student.model-development.training.status', $job)), {
                headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
            });
            if (!response.ok) {
                window.setTimeout(poll, 5000);
                return;
            }
            const data = await response.json();
            render(data);
            if (!terminalStatuses.has(data.status)) window.setTimeout(poll, 2000);
        } catch (error) {
            window.setTimeout(poll, 5000);
        }
    };

    render(initial);
    if (!terminalStatuses.has(initial.status)) window.setTimeout(poll, 1000);
})();
</script>
</body>
</html>
