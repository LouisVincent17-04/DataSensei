<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Set Up Your Model — DataSensei</title>
    @include('student.model-development.partials.styles')
    @include('partials.page-head', ['pageTitle' => 'Set Up Your Model', 'pageDescription' => 'Train a real machine-learning model in four short steps and use it to make predictions.'])
</head>
<body>
@php
    $outcome = \App\Support\ModelDevelopmentOutcome::class;
    $guide = \App\Support\ModelDevelopmentGuide::class;
    $term = fn (string $key, ?string $text = null) => \App\Support\ModelDevelopmentGlossary::term($key, $text);

    $recommended = is_array($recommendation ?? null) ? $recommendation : null;
    $resume = (array) ($resumeConfiguration ?? []);
    $headers = array_values((array) ($profile['headers'] ?? []));
    $columnProfiles = (array) ($profile['columns'] ?? []);
    $totalRows = (int) ($rowCount ?? ($profile['row_count'] ?? 0));
    $numericTypes = ['integer', 'decimal'];
    $typeLabels = [
        'integer' => 'whole numbers',
        'decimal' => 'decimal numbers',
        'categorical' => 'categories',
        'boolean' => 'yes / no',
        'text' => 'free text',
        'date' => 'dates',
        'empty' => 'empty',
    ];
    $leakColumns = array_values((array) ($wording['leak_columns'] ?? []));

    $defaultTarget = (string) old('target_column', $resume['target_column'] ?? ($recommended['target'] ?? ($dataset->target_column ?? data_get($profile, 'suggested_target', ''))));

    // Every usable column is a clue by default: more honest clues usually means a more accurate model.
    $usableColumns = array_values(array_filter($headers, function ($column) use ($columnProfiles, $leakColumns) {
        $columnProfile = (array) ($columnProfiles[$column] ?? []);

        return ! ($columnProfile['is_identifier_like'] ?? false)
            && ($columnProfile['type'] ?? '') !== 'empty'
            && ! (($columnProfile['type'] ?? '') === 'text' && (int) ($columnProfile['unique_count'] ?? 0) > 50)
            && ! in_array($column, $leakColumns, true);
    }));
    $chosenFeatures = old('features') !== null
        ? array_values((array) old('features'))
        : (isset($resume['features']) ? array_values((array) $resume['features']) : null);

    $allAlgorithms = [];
    foreach ((array) $algorithmsByProblem as $typeDefinitions) {
        foreach ((array) $typeDefinitions as $key => $definition) {
            $allAlgorithms[$key] = $definition;
        }
    }
    $automaticKeys = array_filter((array) ($automaticKeys ?? []));
    $resumeAlgorithm = (string) old('algorithm_key', $resume['algorithm_key'] ?? '');
    $manualAlgorithm = $resumeAlgorithm !== '' && ! in_array($resumeAlgorithm, $automaticKeys, true) && isset($allAlgorithms[$resumeAlgorithm])
        ? $resumeAlgorithm
        : '';

    $preprocessing = (array) ($resume['preprocessing'] ?? []);
    $defaultTestSize = old('test_size', $resume['test_size'] ?? 0.20);
    $defaultRandomState = old('random_state', $resume['random_state'] ?? 42);
    $defaultCrossValidation = old('cross_validation', $resume['cross_validation'] ?? 5);
    $defaultScaleMode = old('scale_mode', $preprocessing['scale_mode'] ?? 'auto');
    $defaultImputation = old('numeric_imputation', $preprocessing['numeric_imputation'] ?? 'median');
    $defaultRemoveDuplicates = (bool) old('remove_duplicates', $preprocessing['remove_duplicates'] ?? true);
    $defaultTune = (bool) old('tune', $resume['tune'] ?? true);
    $defaultProblemChoice = (string) old('problem_choice', '');
    $defaultClassId = old('class_id', $resumeJob?->class_id);
    $defaultModelName = old('model_name', $resumeJob?->model_name ?? ($dataset->name.' model'));

    $errorSection = \App\Support\ModelDevelopmentRoadmap::sectionForValidationFields(array_keys($errors->getMessages()));
    $datasetPage = $datasetType === 'system'
        ? route('student.model-development.system-datasets.show', $dataset)
        : route('student.model-development.user-datasets.show', $dataset);

    // Passed to the page script. Built here because Blade's @json splits on commas.
    $columnsForScript = [];
    foreach ($headers as $column) {
        $columnProfile = (array) ($columnProfiles[$column] ?? []);
        $columnsForScript[$column] = [
            'label' => $outcome::field($wording, $column)['label'],
            'type' => (string) ($columnProfile['type'] ?? 'text'),
            'unique' => (int) ($columnProfile['unique_count'] ?? 0),
            'samples' => array_slice(array_map('strval', (array) ($columnProfile['sample_values'] ?? [])), 0, 4),
        ];
    }
    $algorithmsForScript = [];
    foreach ($allAlgorithms as $key => $definition) {
        $algorithmsForScript[$key] = [
            'problem' => (string) $definition['problem_type'],
            'automatic' => (bool) ($definition['is_automatic'] ?? false),
            'how' => $guide::algorithmAnalogy((string) $key),
        ];
    }
@endphp

<div class="ml-layout">
    @include('partials.sidebar')
    <main class="ml-main">
        <header class="ml-head">
            <div>
                <h1 class="ml-title ds-page-title">Set up your model</h1>
                <p class="ml-subtitle">Using <strong>{{ $dataset->name }}</strong>, {{ number_format($totalRows) }} examples. Answer the first question and press Train. The rest is already filled in for you.</p>
            </div>
            <div class="ml-actions">
                <a class="ml-btn secondary" href="{{ $datasetPage }}">Look inside the data</a>
                <a class="ml-btn secondary" href="{{ route('student.model-development.index') }}">Change data</a>
            </div>
        </header>

        @if($errors->any())
            <div class="ml-alert error">{{ $errors->first() }}</div>
        @endif
        @if($resumeJob)
            <div class="ml-alert">These are the settings from <strong>{{ $resumeJob->model_name }}</strong>. Training again makes a new {{ $term('version', 'version') }} and keeps the old one.</div>
        @endif

        @include('student.model-development.partials.roadmap', [
            'roadmapCurrent' => 2,
            'roadmapCompletedThrough' => 1,
            'roadmapErrorStep' => $errors->any() ? 2 : null,
            'roadmapLinks' => [1 => route('student.model-development.index')],
        ])

        <form class="ml-flow" id="training-form" method="POST" action="{{ route('student.model-development.training.store') }}" novalidate data-error-section="{{ $errorSection }}">
            @csrf
            <input type="hidden" name="dataset_type" value="{{ $datasetType }}">
            <input type="hidden" name="dataset_id" value="{{ $dataset->id }}">
            <input type="hidden" name="problem_type" id="problem_type" value="{{ $problemType }}">

            {{-- 1. WHAT TO PREDICT --}}
            <section class="ml-card ml-raised ml-setup-block" data-setup-section="target">
                <h2 class="ml-setup-q"><span class="n">1</span>What should the model predict?</h2>
                <p class="ml-muted">Pick the column that holds the answer. This column is called the {{ $term('target', 'target') }}.</p>

                <div class="ml-field" style="margin-top:14px;max-width:520px">
                    <label class="ml-label" for="target_column">Answer column</label>
                    <select class="ml-select" id="target_column" name="target_column">
                        @foreach($headers as $column)
                            @php
                                $targetProfile = (array) ($columnProfiles[$column] ?? []);
                                $targetType = (string) ($targetProfile['type'] ?? 'text');
                                $targetUnusable = $targetType === 'empty' || ($targetProfile['is_identifier_like'] ?? false);
                            @endphp
                            <option value="{{ $column }}" @disabled($targetUnusable) @selected($defaultTarget === $column)>
                                {{ $outcome::field($wording, $column)['label'] }}{{ $recommended && ($recommended['target'] ?? null) === $column ? ' (suggested)' : '' }}
                            </option>
                        @endforeach
                        <option value="" @selected($defaultTarget === '')>No answer column, just find groups of similar rows</option>
                    </select>
                </div>

                <div class="ml-answer-note" id="answer-note" aria-live="polite"></div>
                <div class="ml-alert error ml-step-error" data-section-error="target"></div>
            </section>

            {{-- 2. CLUES --}}
            <section class="ml-card ml-raised ml-setup-block" data-setup-section="columns">
                <h2 class="ml-setup-q"><span class="n">2</span>Which clues may it use?</h2>
                <p class="ml-muted">Clue columns are called {{ $term('feature', 'features') }}. <span id="feature-summary"></span></p>

                <details class="ml-advanced" id="feature-details" style="margin-top:14px" @if($errorSection === 'columns') open @endif>
                    <summary>Choose the columns yourself</summary>
                    <div class="ml-toolbar" role="toolbar" aria-label="Column shortcuts" style="margin-top:12px">
                        <button class="ml-btn small secondary" type="button" data-feature-action="all">Use every column</button>
                        <button class="ml-btn small secondary" type="button" data-feature-action="none">Clear</button>
                    </div>
                    <div class="ml-check-grid" style="margin-top:12px">
                        @foreach($headers as $column)
                            @php
                                $columnProfile = (array) ($columnProfiles[$column] ?? []);
                                $columnType = (string) ($columnProfile['type'] ?? 'text');
                                $lockedReason = match (true) {
                                    (bool) ($columnProfile['is_identifier_like'] ?? false) => 'Looks like an ID number, which tells the model nothing.',
                                    $columnType === 'empty' => 'This column has no values.',
                                    default => '',
                                };
                                $offReason = match (true) {
                                    in_array($column, $leakColumns, true) => 'Left out: it is the answer in another form, so using it would be cheating.',
                                    $columnType === 'text' && (int) ($columnProfile['unique_count'] ?? 0) > 50 => 'Left out: free text with too many different values.',
                                    default => '',
                                };
                                $checked = $chosenFeatures !== null
                                    ? in_array($column, $chosenFeatures, true)
                                    : in_array($column, $usableColumns, true);
                                $field = $outcome::field($wording, $column);
                            @endphp
                            <label class="ml-check" data-feature-card="{{ $column }}">
                                <input
                                    type="checkbox"
                                    name="features[]"
                                    value="{{ $column }}"
                                    data-feature-type="{{ $columnType }}"
                                    data-profile-unavailable="{{ $lockedReason !== '' ? 'true' : 'false' }}"
                                    data-default-on="{{ in_array($column, $usableColumns, true) ? 'true' : 'false' }}"
                                    @checked($checked && $lockedReason === '')
                                    @disabled($lockedReason !== '')>
                                <span>
                                    <strong>{{ $field['label'] }}</strong>
                                    <span class="ml-muted" style="display:block;font-size:.75rem">{{ $typeLabels[$columnType] ?? $columnType }}@if($field['help'] !== ''), {{ lcfirst($field['help']) }}@endif</span>
                                    <span class="ml-col-reason" data-feature-reason data-fixed-reason="{{ $lockedReason !== '' ? $lockedReason : $offReason }}">{{ $lockedReason !== '' ? $lockedReason : $offReason }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </details>
                <div class="ml-alert error ml-step-error" data-section-error="columns"></div>
            </section>

            {{-- 3. HOW IT LEARNS --}}
            <section class="ml-card ml-raised ml-setup-block" data-setup-section="method">
                <h2 class="ml-setup-q"><span class="n">3</span>How should it learn?</h2>
                <p class="ml-muted">The learning method is called an {{ $term('algorithm', 'algorithm') }}. If you are not sure, keep the first choice.</p>

                <div class="ml-method" id="method-choices">
                    <label class="ml-method-choice">
                        <input type="radio" name="method_mode" value="auto" @checked($manualAlgorithm === '')>
                        <strong>Pick the best one for me</strong>
                        <span>DataSensei holds a small contest between several algorithms and keeps the most accurate. Takes a little longer.</span>
                    </label>
                    <label class="ml-method-choice">
                        <input type="radio" name="method_mode" value="manual" @checked($manualAlgorithm !== '')>
                        <strong>I want to choose</strong>
                        <span>Good when your lesson is about one particular algorithm.</span>
                    </label>
                </div>
                <div class="ml-answer-note" id="grouping-note" hidden>Finding groups always uses {{ $term('kmeans', 'K-Means') }}, so there is nothing to choose here.</div>

                <input type="hidden" name="algorithm_key" id="algorithm_key" value="{{ $resumeAlgorithm }}">

                <div id="manual-method" style="margin-top:14px" hidden>
                    <div class="ml-field" style="max-width:520px">
                        <label class="ml-label" for="manual_algorithm">Algorithm</label>
                        <select class="ml-select" id="manual_algorithm">
                            @foreach($allAlgorithms as $key => $algorithm)
                                @continue($algorithm['is_automatic'] ?? false)
                                <option value="{{ $key }}" data-problem="{{ $algorithm['problem_type'] }}" @selected($manualAlgorithm === $key)>
                                    {{ $algorithm['label'] }}{{ $guide::isBeginnerAlgorithm((string) $key) ? ' (easy to explain)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <div class="ml-help" id="algorithm-how" aria-live="polite"></div>
                    </div>

                    <details class="ml-advanced" style="margin-top:14px" @if($errors->has('parameters.*')) open @endif>
                        <summary>Algorithm settings</summary>
                        <label class="ml-check" style="margin-top:12px">
                            <input type="hidden" name="tune" value="0">
                            <input type="checkbox" name="tune" id="tune" value="1" @checked($defaultTune)>
                            <span><strong>Fine-tune the settings for me</strong><span class="ml-muted" style="display:block;font-size:.75rem">DataSensei tries a few values and keeps the best ({{ $term('tuning', 'what is this?') }}). Untick to use exactly the numbers below.</span></span>
                        </label>
                        @foreach($allAlgorithms as $key => $algorithm)
                            @continue(empty($algorithm['parameters']))
                            <div class="algorithm-parameters ml-grid two" data-parameters="{{ $key }}" style="margin-top:14px" hidden>
                                @foreach((array) $algorithm['parameters'] as $parameter => $schema)
                                    @php $parameterValue = old('parameters.'.$parameter, data_get($resume, 'parameters.'.$parameter, $schema['default'] ?? '')); @endphp
                                    <div class="ml-field">
                                        <label class="ml-label" for="parameter-{{ $key }}-{{ $parameter }}">{{ $schema['label'] }}</label>
                                        @if(($schema['type'] ?? '') === 'select')
                                            <select class="ml-select" id="parameter-{{ $key }}-{{ $parameter }}" name="parameters[{{ $parameter }}]" data-parameter-input>
                                                @foreach($schema['options'] as $option)
                                                    <option value="{{ $option }}" @selected((string) $parameterValue === (string) $option)>{{ $option }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input class="ml-input" id="parameter-{{ $key }}-{{ $parameter }}" type="number" name="parameters[{{ $parameter }}]" data-parameter-input value="{{ $parameterValue }}" min="{{ $schema['min'] ?? '' }}" max="{{ $schema['max'] ?? '' }}" step="{{ ($schema['type'] ?? '') === 'float' ? 'any' : ($schema['step'] ?? 1) }}">
                                        @endif
                                        <div class="ml-help">{{ $guide::parameterHint((string) $parameter) }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </details>
                </div>

                <details class="ml-advanced" style="margin-top:14px" @if($errorSection === 'method') open @endif>
                    <summary>More options</summary>
                    <p class="ml-help">You can skip all of these. The defaults are what a careful data scientist would start with.</p>
                    <div class="ml-grid two" style="margin-top:14px">
                        <div class="ml-field" id="problem-choice-field">
                            <label class="ml-label" for="problem_choice">Kind of answer</label>
                            <select class="ml-select" id="problem_choice" name="problem_choice">
                                <option value="" @selected($defaultProblemChoice === '')>Work it out from the answer column</option>
                                <option value="classification" @selected($defaultProblemChoice === 'classification')>A category (classification)</option>
                                <option value="regression" @selected($defaultProblemChoice === 'regression')>A number (regression)</option>
                            </select>
                            <div class="ml-help">Only change this for something like a 1 to 5 rating, which could be treated either way.</div>
                        </div>
                        <div class="ml-field" data-needs-answer>
                            <label class="ml-label" for="test_size">Rows hidden for the final check</label>
                            <select class="ml-select" id="test_size" name="test_size">
                                @foreach($allowedTestSizes as $size)
                                    @php $testPercent = (int) round((float) $size * 100); @endphp
                                    <option value="{{ $size }}" @selected((float) $defaultTestSize === (float) $size)>{{ $testPercent }}% of rows{{ $testPercent === 20 ? ' (usual choice)' : '' }}</option>
                                @endforeach
                            </select>
                            <div class="ml-help">These {{ $term('test_rows', 'test rows') }} are never shown to the model while it learns. About <strong id="split-test-rows">…</strong> rows here.</div>
                        </div>
                        <div class="ml-field" data-needs-answer>
                            <label class="ml-label" for="cross_validation">Practice exams</label>
                            <select class="ml-select" id="cross_validation" name="cross_validation">
                                @foreach($cvFolds as $fold)
                                    <option value="{{ $fold }}" @selected((int) $defaultCrossValidation === (int) $fold)>{{ (int) $fold === 0 ? 'None' : $fold.' rounds' }}</option>
                                @endforeach
                            </select>
                            <div class="ml-help">This is {{ $term('cross_validation', 'cross-validation') }}: extra checks that make the score steadier.</div>
                        </div>
                        <div class="ml-field">
                            <label class="ml-label" for="random_state">Shuffle number</label>
                            <select class="ml-select" id="random_state" name="random_state">
                                @foreach($allowedRandomStates as $seed)
                                    <option value="{{ $seed }}" @selected((int) $defaultRandomState === (int) $seed)>{{ $seed }}</option>
                                @endforeach
                            </select>
                            <div class="ml-help">A {{ $term('random_state', 'random seed') }}. The same number gives the same shuffle, so results can be repeated.</div>
                        </div>
                        <div class="ml-field">
                            <label class="ml-label" for="scale_mode">Put numbers on the same scale</label>
                            <select class="ml-select" id="scale_mode" name="scale_mode">
                                <option value="auto" @selected($defaultScaleMode === 'auto')>Only when the algorithm needs it (recommended)</option>
                                <option value="standard" @selected($defaultScaleMode === 'standard')>Always</option>
                                <option value="none" @selected($defaultScaleMode === 'none')>Never</option>
                            </select>
                            <div class="ml-help">This is {{ $term('scaling', 'scaling') }}.</div>
                        </div>
                        <div class="ml-field">
                            <label class="ml-label" for="numeric_imputation">Fill empty number cells with</label>
                            <select class="ml-select" id="numeric_imputation" name="numeric_imputation">
                                <option value="median" @selected($defaultImputation === 'median')>The middle value (median)</option>
                                <option value="mean" @selected($defaultImputation === 'mean')>The average (mean)</option>
                            </select>
                            <div class="ml-help">{{ $term('imputation', 'Why fill them in?') }}</div>
                        </div>
                        <div class="ml-field">
                            <label class="ml-check">
                                <input type="hidden" name="remove_duplicates" value="0">
                                <input type="checkbox" name="remove_duplicates" value="1" @checked($defaultRemoveDuplicates)>
                                <span>Remove rows that are exact copies of another row</span>
                            </label>
                        </div>
                    </div>
                </details>
                <div class="ml-alert error ml-step-error" data-section-error="method"></div>
            </section>

            {{-- TRAIN --}}
            <section class="ml-card ml-raised ml-setup-block" data-setup-section="name">
                <div class="ml-grid two">
                    <div class="ml-field">
                        <label class="ml-label" for="model_name">Name your model</label>
                        <input class="ml-input" id="model_name" name="model_name" value="{{ $defaultModelName }}" maxlength="160" required>
                    </div>
                    <div class="ml-field">
                        <label class="ml-label" for="class_id">Who can see it</label>
                        <select class="ml-select" id="class_id" name="class_id">
                            <option value="">Only me</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" @selected($defaultClassId == $class->id)>Me and my instructor in {{ $class->name }}{{ $class->section ? ', '.$class->section : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="ml-alert error ml-step-error" data-section-error="name"></div>
                <div class="ml-train-bar" style="margin-top:6px">
                    <p class="ml-muted" id="train-summary" aria-live="polite" style="flex:1 1 320px"></p>
                    <button class="ml-btn" type="submit" id="train-model-button">Train my model</button>
                </div>
            </section>

            @include('student.model-development.partials.step-guide', ['guideStep' => 2])
        </form>
    </main>
</div>

<script>
(() => {
    const form = document.getElementById('training-form');
    const target = document.getElementById('target_column');
    const problemInput = document.getElementById('problem_type');
    const problemChoice = document.getElementById('problem_choice');
    const algorithmInput = document.getElementById('algorithm_key');
    const manualSelect = document.getElementById('manual_algorithm');
    const manualBox = document.getElementById('manual-method');
    const methodChoices = document.getElementById('method-choices');
    const groupingNote = document.getElementById('grouping-note');
    const answerNote = document.getElementById('answer-note');
    const columns = @json((object) $columnsForScript);
    const algorithms = @json((object) $algorithmsForScript);
    const automaticKeys = @json((object) $automaticKeys);
    const rowCount = Number(@json($totalRows)) || 0;
    const numericTypes = ['integer', 'decimal'];

    const featureInputs = () => [...form.querySelectorAll('input[name="features[]"]')];

    // Same rule the server uses: words, yes/no and numbers with only a few
    // different values are categories; other numbers are amounts.
    const inferProblem = () => {
        const column = columns[target.value];
        if (!target.value || !column) return 'clustering';
        if (problemChoice.value) return problemChoice.value;
        if (['boolean', 'categorical', 'text'].includes(column.type)) return 'classification';
        const limit = Math.min(20, Math.max(2, Math.floor(rowCount * 0.10)));
        return column.unique >= 2 && column.unique <= limit ? 'classification' : 'regression';
    };

    const describeAnswer = problem => {
        const column = columns[target.value];
        answerNote.classList.remove('warn');
        if (problem === 'clustering') {
            answerNote.innerHTML = 'With no answer column, the model cannot be marked right or wrong. It will sort the rows into <strong>groups of similar rows</strong> instead. This is called clustering, and it only uses number columns.';
            return;
        }
        const name = document.createElement('strong');
        name.textContent = column.label;
        const samples = column.samples.length ? ` (for example ${column.samples.slice(0, 3).join(', ')})` : '';
        if (problem === 'classification') {
            answerNote.innerHTML = `${name.outerHTML} has <strong>${column.unique}</strong> possible answers${escapeHtml(samples)}. The model will learn to <strong>choose one of them</strong>. Choosing between categories is called classification.`;
            if (column.unique > 100) {
                answerNote.classList.add('warn');
                answerNote.innerHTML += ' That is too many categories to learn. Pick a different answer column.';
            }
        } else {
            answerNote.innerHTML = `${name.outerHTML} is a number${escapeHtml(samples)}. The model will learn to <strong>estimate that number</strong>. Predicting a number is called regression.`;
            if (!numericTypes.includes(column.type)) {
                answerNote.classList.add('warn');
                answerNote.innerHTML += ' This column is not made of numbers, so choose "A category" instead.';
            }
        }
    };

    const escapeHtml = text => text.replace(/[&<>"']/g, character => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[character]));

    const syncFeatures = problem => {
        featureInputs().forEach(input => {
            const reason = input.closest('label').querySelector('[data-feature-reason]');
            const fixed = reason.dataset.fixedReason || '';
            const locked = input.dataset.profileUnavailable === 'true';
            const isTarget = input.value === target.value && target.value !== '';
            const notNumeric = problem === 'clustering' && !numericTypes.includes(input.dataset.featureType);

            if (isTarget || notNumeric) {
                if (input.checked) input.dataset.wasChecked = 'true';
                input.checked = false;
                input.disabled = true;
                reason.textContent = isTarget ? 'This is the answer, so it cannot also be a clue.' : 'Finding groups only works with number columns.';
            } else {
                input.disabled = locked;
                // A column that was only switched off for being the answer comes back when the answer changes.
                if (input.dataset.wasChecked === 'true' || (input.dataset.releasedTarget === 'true' && input.dataset.defaultOn === 'true')) {
                    input.checked = !locked;
                }
                delete input.dataset.wasChecked;
                reason.textContent = input.checked ? '' : fixed;
                if (locked) reason.textContent = fixed;
            }
            input.dataset.releasedTarget = isTarget ? 'true' : 'false';
        });
        updateSummary();
    };

    const updateSummary = () => {
        const inputs = featureInputs();
        const chosen = inputs.filter(input => input.checked && !input.disabled);
        const available = inputs.filter(input => !input.disabled);
        document.getElementById('feature-summary').textContent = chosen.length === available.length
            ? `DataSensei will use all ${chosen.length} useful columns.`
            : `Using ${chosen.length} of ${available.length} useful columns.`;

        const problem = problemInput.value;
        const name = columns[target.value]?.label || '';
        const mode = form.querySelector('input[name="method_mode"]:checked')?.value;
        const method = problem === 'clustering'
            ? 'K-Means'
            : (mode === 'manual' ? manualSelect.selectedOptions[0]?.textContent.trim().replace(/\s*\(easy to explain\)$/, '') : 'the best of several algorithms');
        document.getElementById('train-summary').textContent = problem === 'clustering'
            ? `Ready: find groups of similar rows from ${chosen.length} number columns with ${method}.`
            : `Ready: learn to predict “${name}” from ${chosen.length} clue columns using ${method}. Usually takes under a minute.`;

        const hidden = Math.round(rowCount * Number(document.getElementById('test_size').value || 0.2));
        document.getElementById('split-test-rows').textContent = hidden.toLocaleString();
    };

    const syncMethod = problem => {
        const clustering = problem === 'clustering';
        methodChoices.hidden = clustering;
        groupingNote.hidden = !clustering;
        document.querySelectorAll('[data-needs-answer]').forEach(field => { field.hidden = clustering; });
        document.getElementById('problem-choice-field').hidden = clustering;

        [...manualSelect.options].forEach(option => {
            const fits = option.dataset.problem === problem;
            option.hidden = !fits;
            option.disabled = !fits;
        });
        if (manualSelect.selectedOptions[0]?.disabled) {
            const first = [...manualSelect.options].find(option => !option.disabled);
            if (first) manualSelect.value = first.value;
        }

        const mode = form.querySelector('input[name="method_mode"]:checked')?.value || 'auto';
        const manual = !clustering && mode === 'manual';
        // Finding groups has one setting that matters (how many groups), so its settings stay visible.
        manualBox.hidden = !(manual || clustering);
        manualSelect.closest('.ml-field').hidden = clustering;
        document.getElementById('tune').closest('label').hidden = clustering;
        const settings = manualBox.querySelector('details');
        if (clustering) { settings.open = true; settings.dataset.openedForGroups = '1'; }
        else if (settings.dataset.openedForGroups === '1') { settings.open = false; delete settings.dataset.openedForGroups; }
        algorithmInput.value = clustering ? 'kmeans' : (manual ? manualSelect.value : (automaticKeys[problem] || manualSelect.value));

        document.getElementById('algorithm-how').textContent = algorithms[manualSelect.value]?.how || '';
        document.querySelectorAll('[data-parameters]').forEach(box => {
            const active = (manual || clustering) && box.dataset.parameters === algorithmInput.value;
            box.hidden = !active;
            box.querySelectorAll('[data-parameter-input]').forEach(input => { input.disabled = !active; });
        });
    };

    const refresh = () => {
        const problem = inferProblem();
        problemInput.value = problem;
        describeAnswer(problem);
        syncFeatures(problem);
        syncMethod(problem);
        updateSummary();
    };

    const showError = (section, message) => {
        const box = form.querySelector(`[data-section-error="${section}"]`);
        box.textContent = message;
        box.classList.add('visible');
        const block = form.querySelector(`[data-setup-section="${section}"]`);
        block.querySelectorAll('details').forEach(details => { if (section === 'columns') details.open = true; });
        block.scrollIntoView({behavior: 'smooth', block: 'center'});
    };

    form.addEventListener('change', event => {
        form.querySelectorAll('[data-section-error]').forEach(box => box.classList.remove('visible'));
        if (event.target.matches('input[name="features[]"]')) {
            const reason = event.target.closest('label').querySelector('[data-feature-reason]');
            reason.textContent = event.target.checked ? '' : (reason.dataset.fixedReason || '');
            updateSummary();
            return;
        }
        refresh();
    });

    form.querySelectorAll('[data-feature-action]').forEach(button => button.addEventListener('click', () => {
        const on = button.dataset.featureAction === 'all';
        featureInputs().forEach(input => {
            if (input.disabled) return;
            input.checked = on;
            const reason = input.closest('label').querySelector('[data-feature-reason]');
            reason.textContent = on ? '' : (reason.dataset.fixedReason || '');
        });
        updateSummary();
    }));

    // Enter in the name box must not start training before the student has looked at the page.
    form.addEventListener('keydown', event => {
        if (event.key === 'Enter' && event.target.matches('input:not([type="checkbox"]):not([type="radio"])')) event.preventDefault();
    });

    form.addEventListener('submit', event => {
        const problem = problemInput.value;
        const chosen = featureInputs().filter(input => input.checked && !input.disabled);
        let problemFound = null;
        if (problem === 'classification' && (columns[target.value]?.unique || 0) > 100) problemFound = ['target', 'This answer column has more than 100 different values. Pick another column, or choose "A number" under More options.'];
        else if (problem === 'classification' && (columns[target.value]?.unique || 0) < 2) problemFound = ['target', 'This answer column only ever has one value, so there is nothing to learn. Pick another column.'];
        else if (problem === 'regression' && !numericTypes.includes(columns[target.value]?.type)) problemFound = ['target', 'Predicting a number needs an answer column made of numbers.'];
        else if (chosen.length === 0) problemFound = ['columns', 'Tick at least one clue column.'];
        else if (chosen.length > 50) problemFound = ['columns', 'Use at most 50 clue columns in one model.'];
        else if (problem === 'clustering' && chosen.length < 2) problemFound = ['columns', 'Finding groups needs at least two number columns.'];
        else if (!document.getElementById('model_name').value.trim()) problemFound = ['name', 'Give your model a name.'];

        if (problemFound) {
            event.preventDefault();
            showError(problemFound[0], problemFound[1]);
            return;
        }
        const button = document.getElementById('train-model-button');
        button.disabled = true;
        button.textContent = 'Starting…';
    });

    refresh();

    const errorSection = form.dataset.errorSection;
    if (errorSection) form.querySelector(`[data-setup-section="${errorSection}"]`)?.scrollIntoView({block: 'center'});
})();
</script>
</body>
</html>
