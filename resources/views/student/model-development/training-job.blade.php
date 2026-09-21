<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Training — DataSensei</title>
    @include('student.model-development.partials.styles')
    @include('partials.page-head', ['pageTitle' => 'Training', 'pageDescription' => 'Train a real machine-learning model in four short steps and use it to make predictions.'])
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
    $resultsUrl = $job->ml_model_id
        ? route('student.model-development.models.show', ['model' => $job->ml_model_id, 'step' => 'results'])
        : null;
    $roadmapLinks = [
        1 => route('student.model-development.index'),
        2 => route('student.model-development.wizard', $wizardParameters),
        3 => $resultsUrl ?: '#',
    ];
    $isAutomatic = (bool) config('hybrid_ml.algorithms.'.$job->algorithm_key.'.is_automatic', false);
    $term = fn (string $key, ?string $text = null) => \App\Support\ModelDevelopmentGlossary::term($key, $text);
    $isComplete = $job->status === 'completed';
    $hasFailed = in_array($job->status, ['failed', 'cancelled'], true);
    $metrics = (array) data_get($job->result, 'metrics', []);
    $durationSeconds = $job->duration_ms !== null ? round($job->duration_ms / 1000, 3) : null;
    $stageDefinitions = [
        ['start' => 0, 'end' => 15, 'label' => 'Reading your data'],
        ['start' => 15, 'end' => 35, 'label' => $isAutomatic ? 'Comparing algorithms' : 'Getting the clues ready'],
        ['start' => 35, 'end' => 58, 'label' => 'Learning from the training rows'],
        ['start' => 58, 'end' => 90, 'label' => 'Taking the final exam on hidden rows'],
        ['start' => 90, 'end' => 100, 'label' => 'Saving your model'],
    ];
    $stageExplanations = \App\Support\ModelDevelopmentGuide::trainingStageExplanations();
    if ($isAutomatic) {
        $stageExplanations[1] = 'Several algorithms sit the same practice exams on your training rows and the best one is kept.';
    }
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
                    {{ $isComplete ? 'Your model is ready' : ($hasFailed ? 'Training stopped' : 'Training your model') }}
                </h1>
                <p class="ml-subtitle"><strong>{{ $job->model_name }}</strong>, using {{ $isAutomatic ? 'the best of several algorithms' : $algorithmLabel }} on {{ $dataset->name }}.</p>
            </div>
            <a class="ml-btn secondary" href="{{ route('student.model-development.index') }}#model-history">My models</a>
        </header>

        @if(session('success'))
            <div class="ml-alert">{{ session('success') }}</div>
        @endif

        @include('student.model-development.partials.roadmap', [
            'roadmapCurrent' => $hasFailed ? 2 : 3,
            'roadmapCompletedThrough' => $hasFailed ? 1 : 2,
            'roadmapErrorStep' => $hasFailed ? 2 : null,
            'roadmapLinks' => $roadmapLinks,
        ])

        <div class="ml-flow">
                <section class="ml-card ml-raised ml-training-milestone">
                    <div style="display:flex;flex-wrap:wrap;justify-content:space-between;gap:4px 16px;align-items:baseline">
                        <strong id="progress-label" style="font-size:1.0625rem">{{ $job->progress }}% done</strong>
                        <span class="ml-help" style="margin:0"><span class="ml-status-dot {{ $job->status }}" id="status-dot"></span><span id="status-text">{{ ucfirst($job->status) }}</span>, <span id="stage">{{ $job->stage }}</span></span>
                    </div>
                    <div class="ml-progress" style="margin-top:10px">
                        <div id="progress-bar" style="width:{{ $job->progress }}%"></div>
                    </div>
                    <p class="ml-help">This bar follows the real training job. It is not a simulated timer, so it can pause for a moment while the computer works.</p>

                    <p class="ml-help" id="retry-details" style="display:{{ $job->status === 'retrying' ? 'block' : 'none' }};margin-top:8px">
                        @if($job->status === 'retrying')
                            Attempt {{ $job->attempt_number }} did not complete. The next attempt is scheduled {{ optional($job->next_retry_at)->diffForHumans() }}.
                        @endif
                    </p>

                    <div class="ml-training-stage-list" id="training-stages" aria-label="Training stages">
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

                    @php
                        $workerState = (string) ($worker['state'] ?? 'ok');
                        $workerMessage = $worker['message'] ?? null;
                    @endphp
                    <div class="ml-alert ml-wait-note {{ $workerMessage && $job->status === 'queued' ? 'visible' : '' }} {{ $workerState === 'worker_missing' ? 'error' : '' }}" id="wait-note" role="status">
                        <span id="wait-note-text">{{ $workerMessage ?: 'Training has not started yet. Jobs run one at a time, so yours may be waiting behind another. You can leave this page open and it will update by itself.' }}</span>
                    </div>

                    <div id="error-box" class="ml-alert error" style="display:{{ $hasFailed ? 'block' : 'none' }}">
                        {{ $job->error_message ?: ($job->status === 'cancelled' ? 'Training was cancelled before a model was created.' : '') }}
                    </div>
                    <div id="failure-help" style="display:{{ $hasFailed ? 'block' : 'none' }};margin-bottom:16px">
                        <h3 class="ml-section-title">Things that usually fix it</h3>
                        <ul class="ml-fix-list ml-muted">
                            <li>Check the answer column. Predicting a number needs a number column, and a category needs at least two different values.</li>
                            <li>Untick clue columns that are mostly empty or mix numbers with text.</li>
                            <li>Finding groups needs at least two number columns and fewer groups than rows.</li>
                            <li>The data needs at least 20 usable rows once rows with an empty answer are removed.</li>
                        </ul>
                    </div>
                    <div id="failure-actions" class="ml-actions" style="display:{{ $hasFailed ? 'flex' : 'none' }};margin-bottom:16px">
                        <a class="ml-btn" href="{{ route('student.model-development.wizard', $wizardParameters) }}">Change the set-up and try again</a>
                    </div>

                    <section id="training-success" class="ml-success-milestone {{ $isComplete ? 'visible' : '' }}" aria-live="polite">
                        <h2 class="ml-success-title">Model trained successfully</h2>
                        <p class="ml-muted" style="margin-top:8px">It was tested on rows it never saw while learning. <span id="redirect-note"></span></p>
                        <div class="ml-success-metrics" id="success-metrics">
                            @if($durationSeconds !== null)
                                <div class="ml-success-metric"><span>Time taken</span><strong>{{ number_format($durationSeconds, 1) }} seconds</strong></div>
                            @endif
                            @foreach($successMetrics as $key => $value)
                                <div class="ml-success-metric">
                                    <span>{{ \App\Support\ModelDevelopmentGuide::metric((string) $key)['label'] }}</span>
                                    <strong>{{ \App\Support\ModelDevelopmentGuide::formatMetric((string) $key, $value) }}</strong>
                                </div>
                            @endforeach
                        </div>
                        <a class="ml-btn" id="evaluation-link" href="{{ $resultsUrl ?: '#' }}">See the results</a>
                    </section>
                </section>

                <details class="ml-details">
                    <summary><span>What you asked for<small>The set-up this training run is using</small></span></summary>
                    <div class="ml-details-body">
                        <div class="ml-selection-summary" aria-label="Training set-up" style="margin:0">
                            <div class="ml-summary-row"><span>Data</span><strong>{{ $dataset->name }}</strong></div>
                            <div class="ml-summary-row"><span>Answer column ({{ $term('target', 'target') }})</span><strong>{{ data_get($configuration, 'target_column') ?: 'None, finding groups' }}</strong></div>
                            <div class="ml-summary-row"><span>Kind of answer</span><strong>{{ ['classification' => 'A category (classification)', 'regression' => 'A number (regression)', 'clustering' => 'Groups (clustering)'][$job->problem_type] ?? ucfirst($job->problem_type) }}</strong></div>
                            <div class="ml-summary-row"><span>{{ $term('algorithm', 'Algorithm') }}</span><strong>{{ $algorithmLabel }}</strong></div>
                            <div class="ml-summary-row"><span>Rows hidden for the final check</span><strong>{{ data_get($configuration, 'test_size') !== null ? (int) round((float) data_get($configuration, 'test_size') * 100).'%' : 'None (finding groups)' }}</strong></div>
                            <div class="ml-summary-row"><span>Who can see it</span><strong>{{ $job->class_id ? 'Me and my instructor' : 'Only me' }}</strong></div>
                            <div class="ml-summary-row full"><span>Clue columns ({{ $term('feature', 'features') }})</span><div class="ml-summary-list">@foreach((array) data_get($configuration, 'features', []) as $feature)<span>{{ $feature }}</span>@endforeach</div></div>
                        </div>
                    </div>
                </details>
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
    let redirectTimer = null;
    const initial = {
        status: @json($job->status),
        progress: Number(@json($job->progress)),
        stage: @json($job->stage),
        error: @json($job->error_message),
        evaluation_url: @json($resultsUrl),
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
            label.textContent = 'Time taken';
            const value = document.createElement('strong');
            value.textContent = `${Number(data.duration_seconds).toLocaleString(undefined, {maximumFractionDigits: 1})} seconds`;
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

        document.getElementById('progress-bar').style.width = `${completed ? 100 : progress}%`;
        document.getElementById('progress-label').textContent = completed ? '100% done' : `${progress}% done`;
        document.getElementById('stage').textContent = data.stage || 'Waiting to start';
        document.getElementById('status-text').textContent = status.charAt(0).toUpperCase() + status.slice(1);
        document.getElementById('status-dot').className = `ml-status-dot ${status}`;
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

        // The server reports whether a worker is running or being started.
        const queued = status === 'queued' && progress === 0;
        const workerMessage = queued ? (data.worker_message || '') : '';
        const stillWaiting = queued && (workerMessage !== '' || (Date.now() - pageOpenedAt) > 25000);
        if (workerMessage) waitNoteText.textContent = workerMessage;
        else if (stillWaiting) waitNoteText.textContent = 'Training has not started yet. Jobs run one at a time, so yours may be waiting behind another. You can leave this page open and it will update by itself.';
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
            document.getElementById('training-title').textContent = 'Training stopped';
            window.DataSenseiModelRoadmap.update(roadmap, {current: 2, completed: 1, error: 2});
        } else {
            errorBox.style.display = 'none';
            failureActions.style.display = 'none';
            failureHelp.style.display = 'none';
        }

        if (completed) {
            document.getElementById('training-title').textContent = 'Your model is ready';
            document.getElementById('training-success').classList.add('visible');
            renderSuccessMetrics(data);
            if (data.evaluation_url) {
                document.getElementById('evaluation-link').href = data.evaluation_url;
                const stripLink = roadmap.querySelector('[data-roadmap-step="3"] [data-roadmap-link]');
                if (stripLink) { stripLink.dataset.href = data.evaluation_url; stripLink.setAttribute('href', data.evaluation_url); }
                // Only move on by itself when the student watched it finish, never when they reopen an old run.
                if (watchedTraining && !redirectTimer) {
                    document.getElementById('redirect-note').textContent = 'Opening the results…';
                    redirectTimer = window.setTimeout(() => { window.location.assign(data.evaluation_url); }, 1600);
                }
            }
            window.DataSenseiModelRoadmap.update(roadmap, {current: 3, completed: 2, error: null});
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

    const watchedTraining = !terminalStatuses.has(initial.status);
    render(initial);
    if (!terminalStatuses.has(initial.status)) window.setTimeout(poll, 1000);
})();
</script>
</body>
</html>
