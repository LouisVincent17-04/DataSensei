<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Train Model — DataSensei</title>
    @include('student.model-development.partials.styles')
    @include('partials.page-head', ['pageTitle' => 'Train Model', 'pageDescription' => 'Build, evaluate, and save a real machine-learning model in ten guided steps.'])
</head>
<body>
@php
    $configuration = (array) $job->configuration;
    $trainingRecommendation = (array) data_get($configuration, 'recommendation', []);
    $isRecommendedModel = (bool) ($trainingRecommendation['is_recommended_model'] ?? false);
    $algorithmLabel = (string) data_get(
        $configuration,
        'learning_mode.algorithm.label',
        config('hybrid_ml.algorithms.'.$job->algorithm_key.'.label', str($job->algorithm_key)->replace('_', ' ')->title())
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
    $stageExplanations = \App\Support\ModelDevelopmentGuide::trainingStageExplanations();
    $successMetrics = array_slice(\App\Support\ModelDevelopmentGuide::displayMetrics((string) $job->problem_type, $metrics), 0, 3, true);
    $metricLabels = collect(\App\Support\ModelDevelopmentGuide::metricGlossary())->map(fn ($item) => $item['label'])->all();
    $metricOrder = array_keys(\App\Support\ModelDevelopmentGuide::displayMetrics((string) $job->problem_type, [
        'accuracy' => 0, 'f1' => 0, 'precision' => 0, 'recall' => 0, 'roc_auc' => 0, 'average_precision' => 0,
        'r2' => 0, 'mae' => 0, 'rmse' => 0, 'silhouette' => 0, 'cluster_count' => 0, 'inertia' => 0,
    ]));
@endphp

<div class="ml-layout">
    @include('partials.sidebar')
    <main class="ml-main">
        <header class="ml-head">
            <div>
                <h1 class="ml-title ds-page-title" id="training-title">
                    {{ $isComplete ? 'Model Trained Successfully' : ($hasFailed ? 'Training Needs Attention' : 'Training Your Model') }}
                </h1>
                <div class="ml-subtitle-row">
                    <p class="ml-subtitle"><strong>{{ $algorithmLabel }}</strong>, {{ $job->model_name }}</p>
                    @if($isRecommendedModel)
                        <span class="ml-badge good">Recommended model</span>
                    @endif
                </div>
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
                    @include('student.model-development.partials.step-header', [
                        'stepNumber' => 7,
                        'stepTitle' => $isComplete ? 'Your model has finished learning' : ($hasFailed ? 'Training stopped' : 'Your model is learning'),
                        'stepLead' => 'The model is studying the training rows. This progress comes directly from the training worker. It is not a simulated timer.',
                    ])

                    <div class="ml-section-head" style="margin-top:16px">
                        <p class="ml-muted" style="margin:0">Current stage: <strong id="stage" style="color:var(--ml-text)">{{ $job->stage }}</strong></p>
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

                    <div style="display:flex;flex-wrap:wrap;justify-content:space-between;gap:4px 16px;align-items:center;margin-top:16px">
                        <strong id="progress-label">{{ $job->progress }}% complete</strong>
                        <span class="ml-help" id="worker-stage">Worker status: {{ $job->stage }}</span>
                    </div>
                    <div class="ml-progress" style="margin-top:8px">
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
                                <span>
                                    {{ $definition['label'] }}
                                    <span class="ml-stage-copy">{{ $stageExplanations[$index] ?? '' }}</span>
                                </span>
                            </div>
                        @endforeach
                    </div>

                    @if($isRecommendedModel && ! empty($trainingRecommendation['reason']))
                        <div class="ml-inline-note"><strong>Why this model?</strong> {{ $trainingRecommendation['reason'] }}</div>
                    @endif

                    @php
                        $workerState = (string) ($worker['state'] ?? 'ok');
                        $workerMessage = $worker['message'] ?? null;
                    @endphp
                    <div class="ml-alert ml-wait-note {{ $workerMessage && $job->status === 'queued' ? 'visible' : '' }} {{ $workerState === 'worker_missing' ? 'error' : '' }}" id="wait-note" role="status">
                        <span id="wait-note-text">{{ $workerMessage ?: 'Training has not started yet. Jobs are processed one at a time, so yours may be waiting behind others. You can leave this page open – it updates by itself.' }}</span>
                    </div>

                    <div id="error-box" class="ml-alert error" style="display:{{ $hasFailed ? 'block' : 'none' }}">
                        {{ $job->error_message ?: ($job->status === 'cancelled' ? 'Training was cancelled before a model was created.' : '') }}
                    </div>
                    <div id="failure-help" class="ml-card" style="display:{{ $hasFailed ? 'block' : 'none' }};margin-bottom:16px">
                        <h3 class="ml-section-title">Common fixes</h3>
                        <ul class="ml-fix-list ml-muted">
                            <li>Make sure the target matches the problem type (a number for Regression, a category for Classification).</li>
                            <li>Remove features that are mostly empty or contain mixed numbers and text.</li>
                            <li>For clustering, choose at least two number columns and fewer clusters than rows.</li>
                            <li>Check that the dataset still has at least 20 usable rows after empty targets are removed.</li>
                        </ul>
                    </div>
                    <div id="failure-actions" class="ml-actions" style="display:{{ $hasFailed ? 'flex' : 'none' }};margin-bottom:16px">
                        <a class="ml-btn" href="{{ route('student.model-development.wizard', array_merge($wizardParameters, ['step' => 7])) }}">Review Settings & Try Again</a>
                        <a class="ml-btn secondary" href="{{ route('student.model-development.wizard', array_merge($wizardParameters, ['step' => 2])) }}">Start From Target</a>
                    </div>

                    <div class="ml-selection-summary" aria-label="Training configuration">
                        <div class="ml-summary-row"><span>Dataset</span><strong>{{ $dataset->name }}</strong></div>
                        <div class="ml-summary-row"><span>Problem Type</span><strong>{{ ucfirst($job->problem_type) }}</strong></div>
                        <div class="ml-summary-row"><span>Train/Test</span><strong>{{ data_get($configuration, 'test_size') !== null ? (100 - (int) round((float) data_get($configuration, 'test_size') * 100)).'% / '.(int) round((float) data_get($configuration, 'test_size') * 100).'%' : 'All rows (clustering)' }}</strong></div>
                        <div class="ml-summary-row"><span>Class</span><strong>{{ $job->class_id ? 'Shared with class' : 'Private' }}</strong></div>
                        <div class="ml-summary-row"><span>Target</span><strong>{{ data_get($configuration, 'target_column') ?: 'No target (clustering)' }}</strong></div>
                        <div class="ml-summary-row"><span>Algorithm</span><strong>{{ $algorithmLabel }}</strong></div>
                        <div class="ml-summary-row full"><span>Features</span><div class="ml-summary-list">@foreach((array) data_get($configuration, 'features', []) as $feature)<span>{{ $feature }}</span>@endforeach</div></div>
                    </div>

                    <section id="training-success" class="ml-success-milestone {{ $isComplete ? 'visible' : '' }}" aria-live="polite">
                        <h2 class="ml-success-title">✓ Model trained successfully</h2>
                        <p class="ml-muted" style="margin-top:8px">Your model finished learning and was tested on the rows it never saw. Next, find out what these scores mean.</p>
                        <div class="ml-success-metrics" id="success-metrics">
                            @if($durationSeconds !== null)
                                <div class="ml-success-metric"><span>Duration</span><strong>{{ number_format($durationSeconds, 3) }} seconds</strong></div>
                            @endif
                            @foreach($successMetrics as $key => $value)
                                <div class="ml-success-metric">
                                    <span>{{ \App\Support\ModelDevelopmentGuide::metric((string) $key)['label'] }}</span>
                                    <strong>{{ \App\Support\ModelDevelopmentGuide::formatMetric((string) $key, $value) }}</strong>
                                </div>
                            @endforeach
                        </div>
                        <a
                            class="ml-btn"
                            id="evaluation-link"
                            href="{{ $job->ml_model_id ? route('student.model-development.models.show', ['model' => $job->ml_model_id, 'step' => 'evaluate']) : '#' }}">
                            View Evaluation →
                        </a>
                    </section>

                    @include('student.model-development.partials.step-guide', ['guideStep' => 7])
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
    const metricLabels = @json((object) $metricLabels);
    const metricOrder = @json($metricOrder);
    const waitNote = document.getElementById('wait-note');
    const waitNoteText = document.getElementById('wait-note-text');
    const pageOpenedAt = Date.now();
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
        worker_state: @json($workerState),
        worker_message: @json($workerMessage),
    };

    const formatLabel = key => metricLabels[key] || String(key).replaceAll('_', ' ').replace(/\b\w/g, character => character.toUpperCase());
    const formatMetric = (key, value) => {
        const number = Number(value);
        if (!Number.isFinite(number)) return String(value ?? '—');
        if (key === 'cluster_count') return number.toLocaleString(undefined, {maximumFractionDigits: 0});
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
        const metrics = data.metrics || {};
        const entries = metricOrder
            .filter(key => metrics[key] !== null && metrics[key] !== undefined && typeof metrics[key] !== 'object' && Number.isFinite(Number(metrics[key])))
            .slice(0, 3)
            .map(key => [key, metrics[key]]);
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

        const stepHeading = document.querySelector('.ml-training-milestone .ml-step-head h2');
        if (stepHeading) {
            stepHeading.textContent = completed ? 'Your model has finished learning' : (failed ? 'Training stopped' : 'Your model is learning');
        }

        // The server reports whether a worker is running or being started.
        const queued = status === 'queued' && progress === 0;
        const workerMessage = queued ? (data.worker_message || '') : '';
        const stillWaiting = queued && (workerMessage !== '' || (Date.now() - pageOpenedAt) > 25000);
        if (workerMessage) waitNoteText.textContent = workerMessage;
        else if (stillWaiting) waitNoteText.textContent = 'Training has not started yet. Jobs are processed one at a time, so yours may be waiting behind others. You can leave this page open – it updates by itself.';
        waitNote.classList.toggle('error', queued && data.worker_state === 'worker_missing');
        waitNote.classList.toggle('visible', stillWaiting);

        const errorBox = document.getElementById('error-box');
        const failureActions = document.getElementById('failure-actions');
        const failureHelp = document.getElementById('failure-help');
        if (failed) {
            errorBox.textContent = data.error || (status === 'cancelled' ? 'Training was cancelled before a model was created.' : 'Training could not be completed. Review the configuration and try again.');
            errorBox.style.display = 'block';
            failureActions.style.display = 'flex';
            failureHelp.style.display = 'block';
            document.getElementById('training-title').textContent = 'Training Needs Attention';
            window.DataSenseiModelRoadmap.update(roadmap, {current: 7, completed: 6, error: 7});
        } else {
            errorBox.style.display = 'none';
            failureActions.style.display = 'none';
            failureHelp.style.display = 'none';
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
