<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Training Roadmap - DataSensei</title>
    @include('student.model-development.partials.styles')
</head>
<body>
@php
    $recommended = is_array($recommendation ?? null) ? $recommendation : null;
    $resume = (array) ($resumeConfiguration ?? []);
    $selectedProblem = old('problem_type', $problemType);
    $headers = array_values((array) ($profile['headers'] ?? []));
    $columnProfiles = (array) ($profile['columns'] ?? []);
    $defaultTarget = old(
        'target_column',
        $resume['target_column'] ?? ($recommended['target'] ?? $dataset->target_column)
    );
    $fallbackFeatures = array_values(array_filter($headers, function ($column) use ($columnProfiles, $defaultTarget) {
        $columnProfile = (array) ($columnProfiles[$column] ?? []);
        return $column !== $defaultTarget
            && ! ($columnProfile['is_identifier_like'] ?? false)
            && ($columnProfile['type'] ?? '') !== 'empty';
    }));
    $defaultFeatures = old('features') !== null
        ? array_values((array) old('features'))
        : array_values((array) ($resume['features'] ?? ($recommended['features'] ?? $fallbackFeatures)));

    $allAlgorithms = [];
    $allLearningGuides = [];
    foreach ((array) $algorithmsByProblem as $typeDefinitions) {
        foreach ((array) $typeDefinitions as $key => $definition) {
            $allAlgorithms[$key] = $definition;
        }
    }
    foreach ((array) $learningGuidesByProblem as $typeGuides) {
        foreach ((array) $typeGuides as $key => $guide) {
            $allLearningGuides[$key] = $guide;
        }
    }

    $availableAlgorithmKeys = array_keys((array) ($algorithmsByProblem[$selectedProblem] ?? []));
    $recommendedAlgorithm = $recommended && ($recommended['problem_type'] ?? null) === $selectedProblem
        ? ($recommended['algorithm_key'] ?? null)
        : null;
    $defaultAlgorithm = old(
        'algorithm_key',
        $resume['algorithm_key'] ?? $recommendedAlgorithm ?? ($availableAlgorithmKeys[0] ?? null)
    );
    if (! in_array($defaultAlgorithm, $availableAlgorithmKeys, true)) {
        $defaultAlgorithm = $availableAlgorithmKeys[0] ?? null;
    }

    $preprocessing = (array) ($resume['preprocessing'] ?? []);
    $defaultTestSize = old('test_size', $resume['test_size'] ?? 0.20);
    $defaultRandomState = old('random_state', $resume['random_state'] ?? 42);
    $defaultCrossValidation = old('cross_validation', $resume['cross_validation'] ?? 5);
    $defaultScaleMode = old('scale_mode', $preprocessing['scale_mode'] ?? 'auto');
    $defaultImputation = old('numeric_imputation', $preprocessing['numeric_imputation'] ?? 'median');
    $defaultRemoveDuplicates = (bool) old('remove_duplicates', $preprocessing['remove_duplicates'] ?? true);
    $defaultClassId = old('class_id', $resumeJob?->class_id);
    $defaultModelName = old('model_name', $resumeJob?->model_name ?? ($dataset->name.' Experiment'));

    $validationStep = \App\Support\ModelDevelopmentRoadmap::stepForValidationFields(
        array_keys($errors->getMessages())
    );
    $initialStep = $validationStep
        ?: \App\Support\ModelDevelopmentRoadmap::normalizeAuthoringStep($requestedStep ?? 2, 2);
    $initialCompletedThrough = max(1, $initialStep - 1);
    $datasetPage = $datasetType === 'system'
        ? route('student.model-development.system-datasets.show', $dataset)
        : route('student.model-development.user-datasets.show', $dataset);
@endphp

<div class="ml-layout">
    @include('partials.sidebar')
    <main class="ml-main">
        <header class="ml-head">
            <div>
                <h1 class="ml-title ds-page-title">Model Development Roadmap</h1>
                <p class="ml-subtitle">You selected <strong>{{ $dataset->name }}</strong>. Complete one clear stage at a time, and review any finished stage whenever you need to.</p>
            </div>
            <div class="ml-actions">
                <a class="ml-btn secondary" href="{{ $datasetPage }}">Dataset Preview</a>
                <a class="ml-btn secondary" href="{{ route('student.model-development.index') }}">Change Dataset</a>
            </div>
        </header>

        @if($errors->any())
            <div class="ml-alert error">{{ $errors->first() }}</div>
        @endif
        @if($resumeJob)
            <div class="ml-alert">You are reviewing the configuration from {{ $resumeJob->model_name }}. Training again creates a new saved version or model; the earlier history remains available.</div>
        @endif

        <div class="ml-roadmap-layout">
            <aside class="ml-roadmap-column">
                @include('student.model-development.partials.roadmap', [
                    'roadmapCurrent' => $initialStep,
                    'roadmapCompletedThrough' => $initialCompletedThrough,
                    'roadmapErrorStep' => $validationStep,
                    'roadmapInteractive' => true,
                    'roadmapLinks' => [1 => route('student.model-development.index')],
                ])
            </aside>

            <div class="ml-roadmap-content">
                <form id="training-form" method="POST" action="{{ route('student.model-development.training.store') }}" novalidate>
                    @csrf
                    <input type="hidden" name="dataset_type" value="{{ $datasetType }}">
                    <input type="hidden" name="dataset_id" value="{{ $dataset->id }}">

                    <section class="ml-card ml-workflow-step" data-workflow-step="2">
                        <span class="ml-step-kicker">Step 2 of 10</span>
                        <h2 class="ml-section-title">Select Target</h2>
                        <p class="ml-muted">Choose what you want the model to predict. If you want to discover groups instead, select the no-target option.</p>

                        @if($recommended)
                            <div class="ml-recommended-inline" style="margin-top:16px">
                                <strong>Recommended target for this built-in dataset</strong>
                                <div class="ml-feature-pills"><span>🌟 {{ $recommended['target'] }}</span></div>
                                <button class="ml-btn small secondary use-recommended-setup" type="button" style="margin-top:10px">Use Recommended Setup</button>
                            </div>
                        @endif

                        <div class="ml-field" style="margin-top:18px">
                            <label class="ml-label" for="target_column">Target column</label>
                            <select class="ml-select" id="target_column" name="target_column">
                                <option value="">No target - discover groups with clustering</option>
                                @foreach($headers as $column)
                                    @php($targetProfile = (array) ($columnProfiles[$column] ?? []))
                                    <option
                                        value="{{ $column }}"
                                        data-column-type="{{ $targetProfile['type'] ?? 'unknown' }}"
                                        data-unique-count="{{ (int) ($targetProfile['unique_count'] ?? 0) }}"
                                        @selected($defaultTarget === $column)>
                                        {{ $column }} - {{ ucfirst($targetProfile['type'] ?? 'unknown') }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="ml-help">The target cannot also be used as a feature. DataSensei checks this again before training.</div>
                        </div>
                        <div class="ml-alert error ml-step-error" data-step-error="2"></div>
                    </section>

                    <section class="ml-card ml-workflow-step" data-workflow-step="3">
                        <span class="ml-step-kicker">Step 3 of 10</span>
                        <h2 class="ml-section-title">Choose Features</h2>
                        <p class="ml-muted">Choose the information the model should use. Identifier-like and empty columns are unavailable.</p>

                        @if($recommended)
                            <div class="ml-recommended-inline" style="margin-top:16px">
                                <strong>🌟 Recommended features</strong>
                                <div class="ml-feature-pills">
                                    @foreach((array) $recommended['features'] as $feature)
                                        <span>✓ {{ $feature }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="ml-check-grid" style="margin-top:16px">
                            @foreach($headers as $column)
                                @php
                                    $columnProfile = (array) ($columnProfiles[$column] ?? []);
                                    $unavailable = ($columnProfile['is_identifier_like'] ?? false)
                                        || ($columnProfile['type'] ?? '') === 'empty';
                                @endphp
                                <label class="ml-check" data-feature-card="{{ $column }}">
                                    <input
                                        type="checkbox"
                                        name="features[]"
                                        value="{{ $column }}"
                                        data-feature-type="{{ $columnProfile['type'] ?? 'unknown' }}"
                                        data-profile-unavailable="{{ $unavailable ? 'true' : 'false' }}"
                                        @checked(in_array($column, $defaultFeatures, true))
                                        @disabled($unavailable)>
                                    <span>
                                        <strong>{{ $column }}</strong>
                                        @if($recommended && in_array($column, (array) $recommended['features'], true))
                                            <span class="ml-recommended-star" title="Recommended feature">🌟</span>
                                        @endif
                                        <br>
                                        <span class="ml-muted">
                                            {{ ucfirst($columnProfile['type'] ?? 'unknown') }} · {{ number_format((int) ($columnProfile['unique_count'] ?? 0)) }} unique
                                            @if($unavailable) · unavailable for training @endif
                                        </span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <div class="ml-alert error ml-step-error" data-step-error="3"></div>
                    </section>

                    <section class="ml-card ml-workflow-step" data-workflow-step="4">
                        <span class="ml-step-kicker">Step 4 of 10</span>
                        <h2 class="ml-section-title">Choose Problem Type</h2>
                        <p class="ml-muted">Tell DataSensei whether you are predicting a category, predicting a number, or discovering groups.</p>

                        <div class="ml-grid" style="margin-top:18px">
                            @foreach([
                                'classification' => ['Category prediction', 'Use this when the target contains labels such as Yes/No, Pass/Fail, or named classes.'],
                                'regression' => ['Number prediction', 'Use this when the target is a continuous numeric value such as a grade, price, or score.'],
                                'clustering' => ['Group discovery', 'Use this when there is no target and you want to discover similar groups.'],
                            ] as $key => [$label, $description])
                                <label class="ml-card ml-algorithm ml-problem-choice">
                                    <input type="radio" name="problem_type" value="{{ $key }}" @checked($selectedProblem === $key)>
                                    <strong style="display:block;margin:8px 0 5px">{{ $label }}</strong>
                                    <span class="ml-muted" style="font-size:.8rem">{{ $description }}</span>
                                    @if($recommended && ($recommended['problem_type'] ?? null) === $key)
                                        <span class="ml-badge good" style="margin-top:10px">🌟 Recommended</span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                        <div class="ml-inline-note" id="problem-guidance"></div>
                        <div class="ml-alert error ml-step-error" data-step-error="4"></div>
                    </section>

                    <section class="ml-card ml-workflow-step" data-workflow-step="5">
                        <span class="ml-step-kicker">Step 5 of 10</span>
                        <h2 class="ml-section-title">Set the Train/Test Split</h2>
                        <p class="ml-muted">Keep part of the data unseen during training so the finished model can be evaluated fairly.</p>

                        <div class="ml-field" id="split-field" style="margin-top:18px">
                            <label class="ml-label" for="test_size">Training and testing data</label>
                            <select class="ml-select" id="test_size" name="test_size">
                                @foreach($allowedTestSizes as $size)
                                    <option value="{{ $size }}" @selected((float) $defaultTestSize === (float) $size)>
                                        {{ (1 - $size) * 100 }}% training / {{ $size * 100 }}% testing
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="ml-alert" id="clustering-split-note" hidden>Clustering uses all usable rows to discover groups, so a separate test split is not required.</div>

                        <details class="ml-advanced" style="margin-top:18px">
                            <summary>Advanced Options</summary>
                            <p class="ml-help">Beginners can keep these safe defaults. They are hidden so the main roadmap remains simple.</p>
                            <div class="ml-grid two" style="margin-top:16px">
                                <div class="ml-field">
                                    <label class="ml-label" for="random_state">Random state</label>
                                    <select class="ml-select" id="random_state" name="random_state">
                                        @foreach($allowedRandomStates as $seed)
                                            <option value="{{ $seed }}" @selected((int) $defaultRandomState === (int) $seed)>{{ $seed }}</option>
                                        @endforeach
                                    </select>
                                    <div class="ml-help">The same value makes the split reproducible.</div>
                                </div>
                                <div class="ml-field">
                                    <label class="ml-label" for="cross_validation">Cross-validation</label>
                                    <select class="ml-select" id="cross_validation" name="cross_validation">
                                        @foreach($cvFolds as $fold)
                                            <option value="{{ $fold }}" @selected((int) $defaultCrossValidation === (int) $fold)>{{ $fold === 0 ? 'Disabled' : $fold.' folds' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="ml-field">
                                    <label class="ml-label" for="scale_mode">Feature scaling</label>
                                    <select class="ml-select" id="scale_mode" name="scale_mode">
                                        <option value="auto" @selected($defaultScaleMode === 'auto')>Automatic</option>
                                        <option value="standard" @selected($defaultScaleMode === 'standard')>Standard scaling</option>
                                        <option value="none" @selected($defaultScaleMode === 'none')>No scaling</option>
                                    </select>
                                </div>
                                <div class="ml-field">
                                    <label class="ml-label" for="numeric_imputation">Numeric missing values</label>
                                    <select class="ml-select" id="numeric_imputation" name="numeric_imputation">
                                        <option value="median" @selected($defaultImputation === 'median')>Median</option>
                                        <option value="mean" @selected($defaultImputation === 'mean')>Mean</option>
                                    </select>
                                </div>
                                <div class="ml-field">
                                    <label class="ml-check">
                                        <input type="hidden" name="remove_duplicates" value="0">
                                        <input type="checkbox" name="remove_duplicates" value="1" @checked($defaultRemoveDuplicates)>
                                        <span>Remove exact duplicate rows before training</span>
                                    </label>
                                </div>
                            </div>
                        </details>
                        <div class="ml-alert error ml-step-error" data-step-error="5"></div>
                    </section>

                    <section class="ml-card ml-workflow-step" data-workflow-step="6">
                        <span class="ml-step-kicker">Step 6 of 10</span>
                        <h2 class="ml-section-title">Choose Algorithm</h2>
                        <p class="ml-muted">Choose the machine-learning method. Only algorithms compatible with your problem type are shown.</p>

                        @if($recommended)
                            <div class="ml-recommended-model" id="recommended-model" data-recommended-problem="{{ $recommended['problem_type'] }}" style="margin-top:18px">
                                <div class="ml-recommended-model-head">
                                    <div>
                                        <span class="ml-recommended-kicker">🌟 Recommended Model</span>
                                        <h3>{{ $recommended['model_label'] }}</h3>
                                    </div>
                                    <span class="ml-badge good">{{ ucfirst($recommended['problem_type']) }}</span>
                                </div>
                                <p><strong>Why this model?</strong><br>{{ $recommended['reason'] }}</p>
                                <button class="ml-btn use-recommended-model" type="button" data-algorithm="{{ $recommended['algorithm_key'] }}">Use Recommended Model</button>
                            </div>
                        @endif

                        <div class="ml-grid" id="algorithm-grid" style="margin-top:18px">
                            @foreach($allAlgorithms as $key => $algorithm)
                                @php($guide = (array) ($allLearningGuides[$key] ?? []))
                                <label class="ml-card ml-algorithm" data-algorithm-card data-problem="{{ $algorithm['problem_type'] }}">
                                    <input
                                        type="radio"
                                        name="algorithm_key"
                                        value="{{ $key }}"
                                        data-algorithm="{{ $key }}"
                                        @checked($defaultAlgorithm === $key)>
                                    <strong style="display:block;margin:8px 0 5px">{{ $algorithm['label'] }}</strong>
                                    <p class="ml-muted" style="font-size:.78rem">{{ $algorithm['description'] }}</p>
                                    <div class="ml-help"><strong>Why it fits:</strong> {{ $guide['why_suitable'] ?? 'A controlled educational baseline for this problem type.' }}</div>
                                    <div class="ml-help"><strong>Expected behavior:</strong> {{ $guide['expected_behavior'] ?? '' }}</div>
                                </label>
                            @endforeach
                        </div>

                        <details class="ml-advanced" style="margin-top:18px">
                            <summary>Advanced Options</summary>
                            <p class="ml-help">Only the selected algorithm's parameters are submitted. Beginners can keep the safe defaults.</p>
                            @foreach($allAlgorithms as $key => $algorithm)
                                <div class="algorithm-parameters" data-parameters="{{ $key }}" hidden>
                                    <h3 class="ml-section-title" style="margin-top:16px">{{ $algorithm['label'] }} parameters</h3>
                                    @if(empty($algorithm['parameters']))
                                        <p class="ml-muted" style="font-size:.8rem">This model uses a controlled default configuration.</p>
                                    @else
                                        <div class="ml-grid two">
                                            @foreach((array) $algorithm['parameters'] as $parameter => $schema)
                                                @php($parameterValue = old('parameters.'.$parameter, data_get($resume, 'parameters.'.$parameter, $schema['default'] ?? '')))
                                                <div class="ml-field">
                                                    <label class="ml-label" for="parameter-{{ $key }}-{{ $parameter }}">{{ $schema['label'] }}</label>
                                                    @if(($schema['type'] ?? '') === 'select')
                                                        <select class="ml-select" id="parameter-{{ $key }}-{{ $parameter }}" name="parameters[{{ $parameter }}]" data-parameter-input>
                                                            @foreach($schema['options'] as $option)
                                                                <option value="{{ $option }}" @selected((string) $parameterValue === (string) $option)>{{ $option }}</option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <input class="ml-input" id="parameter-{{ $key }}-{{ $parameter }}" type="number" name="parameters[{{ $parameter }}]" data-parameter-input value="{{ $parameterValue }}" min="{{ $schema['min'] ?? '' }}" max="{{ $schema['max'] ?? '' }}" step="{{ $schema['step'] ?? 1 }}">
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </details>
                        <div class="ml-alert error ml-step-error" data-step-error="6"></div>
                    </section>

                    <section class="ml-card ml-workflow-step ml-training-milestone" data-workflow-step="7">
                        <span class="ml-step-kicker">Step 7 of 10</span>
                        <h2 class="ml-section-title">Ready to Train</h2>
                        <p class="ml-muted">Your choices now become an actual machine-learning model. Review the summary before starting the real training job.</p>

                        <div class="ml-training-ready">
                            <span class="ml-training-ready-icon">✓</span>
                            <div><strong>You're ready to train your model.</strong><br><span class="ml-help">The next screen reports actual stages from the trusted Python worker.</span></div>
                        </div>

                        <div class="ml-grid two">
                            <div class="ml-field">
                                <label class="ml-label" for="model_name">Model name</label>
                                <input class="ml-input" id="model_name" name="model_name" value="{{ $defaultModelName }}" maxlength="160" required>
                            </div>
                            <div class="ml-field">
                                <label class="ml-label" for="class_id">Class association</label>
                                <select class="ml-select" id="class_id" name="class_id">
                                    <option value="">Private experiment</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" @selected($defaultClassId == $class->id)>{{ $class->name }}{{ $class->section ? ' · '.$class->section : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="ml-selection-summary" aria-label="Training configuration summary">
                            <div class="ml-summary-row"><span>Dataset</span><strong>{{ $dataset->name }}</strong></div>
                            <div class="ml-summary-row"><span>Target</span><strong id="review-target">-</strong></div>
                            <div class="ml-summary-row full"><span>Features</span><div class="ml-summary-list" id="review-features"></div></div>
                            <div class="ml-summary-row"><span>Problem Type</span><strong id="review-problem">-</strong></div>
                            <div class="ml-summary-row"><span>Algorithm</span><strong id="review-algorithm">-</strong></div>
                            <div class="ml-summary-row"><span>Train/Test</span><strong id="review-split">-</strong></div>
                        </div>

                        <div class="ml-alert error ml-step-error" data-step-error="7"></div>
                        <button class="ml-btn" type="submit" id="train-model-button">Train Model</button>
                    </section>

                    <div class="ml-step-navigation">
                        <button class="ml-btn secondary" type="button" id="previous-step">← Back</button>
                        <div class="ml-actions">
                            <span class="ml-help" id="next-step-hint">Complete this step to continue.</span>
                            <button class="ml-btn" type="button" id="next-step">Continue →</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
(() => {
    const form = document.getElementById('training-form');
    const roadmap = document.querySelector('[data-model-roadmap]');
    const panels = new Map([...document.querySelectorAll('[data-workflow-step]')].map(panel => [Number(panel.dataset.workflowStep), panel]));
    const previous = document.getElementById('previous-step');
    const next = document.getElementById('next-step');
    const nextHint = document.getElementById('next-step-hint');
    const target = document.getElementById('target_column');
    const profile = @json($columnProfiles);
    const recommendation = @json($recommended);
    const algorithmDefinitions = @json($allAlgorithms);
    const algorithmLabels = @json(collect($allAlgorithms)->mapWithKeys(fn ($definition, $key) => [$key => $definition['label'] ?? $key]));
    const datasetUrl = @json($datasetPage);
    const initialError = @json($validationStep);
    let currentStep = {{ $initialStep }};
    let completedThrough = {{ $initialCompletedThrough }};
    let errorStep = initialError ? Number(initialError) : null;

    const currentProblem = () => document.querySelector('input[name="problem_type"]:checked')?.value || 'classification';
    const selectedFeatures = () => [...document.querySelectorAll('input[name="features[]"]:checked:not(:disabled)')];
    const selectedAlgorithm = () => document.querySelector('input[name="algorithm_key"]:checked:not(:disabled)')?.value || '';

    const showError = (step, message) => {
        const box = document.querySelector(`[data-step-error="${step}"]`);
        document.querySelectorAll('[data-step-error]').forEach(item => item.classList.remove('visible'));
        if (box) {
            box.textContent = message;
            box.classList.add('error');
            box.classList.add('visible');
        }
        errorStep = step;
        currentStep = step;
        render();
        return false;
    };

    const clearError = step => {
        document.querySelector(`[data-step-error="${step}"]`)?.classList.remove('visible');
        if (errorStep === step) errorStep = null;
    };

    const validateTargetForProblem = problem => {
        const value = target.value;
        if (problem === 'clustering') return true;
        if (!value || !profile[value]) return false;
        const definition = profile[value] || {};
        if (problem === 'regression') return ['integer', 'decimal'].includes(definition.type);
        return Number(definition.unique_count || 0) >= 2;
    };

    const validate = step => {
        clearError(step);
        const problem = currentProblem();

        if (step === 2 && !validateTargetForProblem(problem)) {
            const message = problem === 'regression'
                ? 'Choose a numeric target column for regression.'
                : (problem === 'classification'
                    ? 'Choose a target column with at least two categories for classification.'
                    : 'Choose a target, or continue with clustering.');
            return showError(step, message);
        }

        if (step === 3) {
            const features = selectedFeatures();
            if (features.length < 1) return showError(step, 'Select at least one usable feature.');
            if (features.some(input => input.value === target.value && problem !== 'clustering')) {
                return showError(step, 'The target column cannot also be selected as a feature.');
            }
            if (problem === 'clustering') {
                if (features.length < 2) return showError(step, 'Clustering requires at least two numeric features.');
                if (features.some(input => !['integer', 'decimal'].includes(input.dataset.featureType))) {
                    return showError(step, 'Clustering accepts numeric features only.');
                }
            }
        }

        if (step === 4 && !validateTargetForProblem(problem)) {
            return showError(step, problem === 'regression'
                ? 'The selected regression target must be numeric. Return to Target and choose another column.'
                : 'The selected problem type does not match the target. Return to Target and choose another column.');
        }

        if (step === 5 && problem !== 'clustering' && !document.getElementById('test_size').value) {
            return showError(step, 'Choose how much data to reserve for testing.');
        }

        if (step === 6) {
            const algorithm = selectedAlgorithm();
            if (!algorithm || algorithmDefinitions[algorithm]?.problem_type !== problem) {
                return showError(step, 'Choose an algorithm that supports the selected problem type.');
            }
        }

        if (step === 7 && !document.getElementById('model_name').value.trim()) {
            return showError(step, 'Enter a name for the model before training.');
        }

        return true;
    };

    const syncTargetFeature = () => {
        document.querySelectorAll('input[name="features[]"]').forEach(input => {
            const unavailableByProfile = input.dataset.profileUnavailable === 'true';
            const isTarget = input.value === target.value && currentProblem() !== 'clustering';
            if (isTarget) input.checked = false;
            input.disabled = Boolean(unavailableByProfile || isTarget);
            input.closest('[data-feature-card]')?.classList.toggle('is-target', isTarget);
        });
    };

    const syncAlgorithms = () => {
        const problem = currentProblem();
        const compatible = [];
        document.querySelectorAll('[data-algorithm-card]').forEach(card => {
            const visible = card.dataset.problem === problem;
            card.hidden = !visible;
            const input = card.querySelector('input[name="algorithm_key"]');
            input.disabled = !visible;
            if (visible) compatible.push(input);
        });

        const recommendedModel = document.getElementById('recommended-model');
        if (recommendedModel) recommendedModel.classList.toggle('is-mismatch', recommendedModel.dataset.recommendedProblem !== problem);

        let selected = document.querySelector('input[name="algorithm_key"]:checked:not(:disabled)');
        if (!selected && recommendation?.problem_type === problem) {
            selected = document.querySelector(`input[name="algorithm_key"][value="${CSS.escape(recommendation.algorithm_key)}"]:not(:disabled)`);
        }
        if (!selected) selected = compatible[0] || null;
        if (selected) selected.checked = true;

        const clustering = problem === 'clustering';
        document.getElementById('split-field').hidden = clustering;
        document.getElementById('clustering-split-note').hidden = !clustering;
        document.getElementById('problem-guidance').textContent = clustering
            ? 'Clustering discovers groups and does not use a target column. Choose at least two numeric features.'
            : (problem === 'regression'
                ? 'Regression requires a numeric target and reports metrics such as R², RMSE, and MAE.'
                : 'Classification requires a categorical target and reports metrics such as accuracy, precision, recall, and F1.');

        syncParameters();
        syncTargetFeature();
        updateReview();
    };

    const syncParameters = () => {
        const algorithm = selectedAlgorithm();
        document.querySelectorAll('[data-parameters]').forEach(container => {
            const active = container.dataset.parameters === algorithm;
            container.hidden = !active;
            container.querySelectorAll('[data-parameter-input]').forEach(input => input.disabled = !active);
        });
    };

    const updateReview = () => {
        const problem = currentProblem();
        const algorithm = selectedAlgorithm();
        const features = selectedFeatures().map(input => input.value);
        document.getElementById('review-target').textContent = problem === 'clustering' ? 'No target (clustering)' : (target.value || 'Not selected');
        document.getElementById('review-problem').textContent = problem.replace('_', ' ').replace(/\b\w/g, character => character.toUpperCase());
        document.getElementById('review-algorithm').textContent = algorithmLabels[algorithm] || 'Not selected';
        document.getElementById('review-split').textContent = problem === 'clustering'
            ? 'All usable rows'
            : document.getElementById('test_size').selectedOptions[0]?.textContent.trim() || 'Not selected';
        const list = document.getElementById('review-features');
        list.replaceChildren(...features.map(feature => {
            const pill = document.createElement('span');
            pill.textContent = feature;
            return pill;
        }));
        if (!features.length) {
            const empty = document.createElement('span');
            empty.textContent = 'No features selected';
            list.appendChild(empty);
        }
    };

    const render = () => {
        panels.forEach((panel, step) => panel.classList.toggle('active', step === currentStep));
        window.DataSenseiModelRoadmap.update(roadmap, {
            current: currentStep,
            completed: completedThrough,
            error: errorStep,
        });

        previous.textContent = currentStep === 2 ? '← Back to Dataset' : '← Back';
        next.hidden = currentStep === 7;
        nextHint.hidden = currentStep === 7;
        if (currentStep < 7) {
            const nextName = ({2: 'Features', 3: 'Problem Type', 4: 'Train/Test Split', 5: 'Algorithm', 6: 'Train Model'})[currentStep];
            next.textContent = `Continue to ${nextName} →`;
        }
        if (currentStep === 7) updateReview();
        document.querySelector('.ml-roadmap-content')?.scrollIntoView({behavior: 'smooth', block: 'start'});
    };

    const goTo = step => {
        if (step < 2 || step > 7 || step > completedThrough + 1) return;
        currentStep = step;
        errorStep = null;
        render();
    };

    const invalidateFrom = step => {
        const completionChanged = step <= completedThrough;
        const pageChanged = currentStep > step;
        if (completionChanged) completedThrough = Math.max(1, step - 1);
        if (pageChanged) currentStep = step;
        errorStep = null;
        if (completionChanged || pageChanged) {
            render();
        } else {
            window.DataSenseiModelRoadmap.update(roadmap, {
                current: currentStep,
                completed: completedThrough,
                error: null,
            });
        }
    };

    const applyRecommendedSetup = () => {
        if (!recommendation) return;
        const problem = document.querySelector(`input[name="problem_type"][value="${CSS.escape(recommendation.problem_type)}"]`);
        if (problem) problem.checked = true;
        target.value = recommendation.target || '';
        const wanted = new Set(recommendation.features || []);
        document.querySelectorAll('input[name="features[]"]').forEach(input => {
            if (!input.disabled || input.value === target.value) input.checked = wanted.has(input.value);
        });
        syncAlgorithms();
        const algorithm = document.querySelector(`input[name="algorithm_key"][value="${CSS.escape(recommendation.algorithm_key)}"]:not(:disabled)`);
        if (algorithm) algorithm.checked = true;
        syncParameters();
        invalidateFrom(2);
        const box = document.querySelector('[data-step-error="2"]');
        box.textContent = `Recommended setup applied: ${recommendation.target}, ${(recommendation.features || []).length} features, ${recommendation.problem_type}, and ${recommendation.model_label}. You can still change each choice.`;
        box.classList.remove('error');
        box.classList.add('visible');
    };

    next.addEventListener('click', () => {
        if (!validate(currentStep)) return;
        completedThrough = Math.max(completedThrough, currentStep);
        currentStep = Math.min(7, currentStep + 1);
        errorStep = null;
        render();
    });

    previous.addEventListener('click', () => {
        if (currentStep === 2) {
            window.location.assign(datasetUrl);
            return;
        }
        currentStep = Math.max(2, currentStep - 1);
        errorStep = null;
        render();
    });

    document.querySelectorAll('[data-roadmap-go]').forEach(button => {
        button.addEventListener('click', () => goTo(Number(button.dataset.roadmapGo)));
    });

    roadmap.querySelector('[data-roadmap-step="1"] a')?.addEventListener('click', event => {
        if (!window.confirm('Choose another dataset? Only this unfinished configuration will reset. Your trained Model History will remain saved.')) {
            event.preventDefault();
        }
    });

    form.addEventListener('change', event => {
        const panel = event.target.closest('[data-workflow-step]');
        if (panel) invalidateFrom(Number(panel.dataset.workflowStep));
        if (event.target === target) syncTargetFeature();
        if (event.target.matches('input[name="problem_type"]')) syncAlgorithms();
        if (event.target.matches('input[name="algorithm_key"]')) syncParameters();
        updateReview();
    });

    form.addEventListener('submit', event => {
        for (let step = 2; step <= 7; step++) {
            if (!validate(step)) {
                event.preventDefault();
                return;
            }
        }
        document.getElementById('train-model-button').disabled = true;
        document.getElementById('train-model-button').textContent = 'Starting Training...';
    });

    document.querySelectorAll('.use-recommended-setup').forEach(button => button.addEventListener('click', applyRecommendedSetup));
    document.querySelectorAll('.use-recommended-model').forEach(button => button.addEventListener('click', () => {
        const radio = document.querySelector(`input[name="algorithm_key"][value="${CSS.escape(button.dataset.algorithm)}"]:not(:disabled)`);
        if (!radio) return;
        radio.checked = true;
        syncParameters();
        invalidateFrom(6);
    }));

    syncAlgorithms();
    syncTargetFeature();
    updateReview();
    render();
})();
</script>
</body>
</html>
