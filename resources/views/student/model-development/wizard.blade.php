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
    $totalRows = (int) ($rowCount ?? ($profile['row_count'] ?? 0));
    $numericTypes = ['integer', 'decimal'];
    $typeLabels = [
        'integer' => 'Whole number',
        'decimal' => 'Decimal number',
        'categorical' => 'Category',
        'boolean' => 'Yes / No',
        'text' => 'Text',
        'date' => 'Date',
        'empty' => 'Empty',
    ];
    $problemLabels = [
        'classification' => 'Classification',
        'regression' => 'Regression',
        'clustering' => 'Clustering',
    ];

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
    // The server accepts at most 50 features, so the automatic preselection never exceeds that.
    $fallbackFeatures = array_slice($fallbackFeatures, 0, 50);
    $defaultFeatures = old('features') !== null
        ? array_values((array) old('features'))
        : array_values((array) ($resume['features'] ?? ($recommended['features'] ?? $fallbackFeatures)));
    $recommendedFeatures = array_values((array) ($recommended['features'] ?? []));

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

    // Built here (not inline in @json) because Blade's @json splits its argument on commas.
    $algorithmLabels = [];
    foreach ($allAlgorithms as $algorithmKey => $algorithmDefinition) {
        $algorithmLabels[$algorithmKey] = (string) ($algorithmDefinition['label'] ?? $algorithmKey);
    }

    $availableAlgorithmKeys = array_keys((array) ($algorithmsByProblem[$selectedProblem] ?? []));
    $beginnerAlgorithmKeys = array_values(array_filter(
        $availableAlgorithmKeys,
        static fn ($key) => \App\Support\ModelDevelopmentGuide::isBeginnerAlgorithm((string) $key)
    ));
    $recommendedAlgorithm = $recommended && ($recommended['problem_type'] ?? null) === $selectedProblem
        ? ($recommended['algorithm_key'] ?? null)
        : null;
    $defaultAlgorithm = old(
        'algorithm_key',
        $resume['algorithm_key'] ?? $recommendedAlgorithm ?? ($beginnerAlgorithmKeys[0] ?? ($availableAlgorithmKeys[0] ?? null))
    );
    if (! in_array($defaultAlgorithm, $availableAlgorithmKeys, true)) {
        $defaultAlgorithm = $beginnerAlgorithmKeys[0] ?? ($availableAlgorithmKeys[0] ?? null);
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
                <h1 class="ml-title ds-page-title">Build Your Model</h1>
                <p class="ml-subtitle">You are working with <strong>{{ $dataset->name }}</strong> ({{ number_format($totalRows) }} rows). Complete one short step at a time. You can go back to any finished step from the progress list.</p>
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
            <div class="ml-alert">You are reviewing the settings from <strong>{{ $resumeJob->model_name }}</strong>. Training again creates a new version; your earlier results stay in Model History.</div>
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

                    {{-- STEP 2: TARGET --}}
                    <section class="ml-card ml-workflow-step" data-workflow-step="2">
                        @include('student.model-development.partials.step-header', [
                            'stepNumber' => 2,
                            'stepTitle' => 'What do you want to predict?',
                            'stepLead' => 'Pick the column that holds the answer you want the model to learn. This column is called the target.',
                        ])

                        @if($recommended)
                            <div class="ml-recommended-inline" style="margin-top:16px">
                                <strong>Suggested for this dataset: {{ $recommended['target'] }}</strong>
                                <p class="ml-help" style="margin-top:4px">{{ $recommended['objective'] ?? '' }}</p>
                                <button class="ml-btn small secondary use-recommended-setup" type="button" style="margin-top:10px">Use the whole recommended setup</button>
                            </div>
                        @endif

                        <div class="ml-field" style="margin-top:18px">
                            <label class="ml-label" for="target_column">Target column</label>
                            <select class="ml-select" id="target_column" name="target_column">
                                <option value="">No target – let the model discover groups (clustering)</option>
                                @foreach($headers as $column)
                                    @php
                                        $targetProfile = (array) ($columnProfiles[$column] ?? []);
                                        $targetType = (string) ($targetProfile['type'] ?? 'unknown');
                                        $targetNote = ($targetProfile['is_identifier_like'] ?? false) ? ' · looks like an ID' : '';
                                    @endphp
                                    <option
                                        value="{{ $column }}"
                                        data-column-type="{{ $targetType }}"
                                        data-unique-count="{{ (int) ($targetProfile['unique_count'] ?? 0) }}"
                                        @disabled($targetType === 'empty')
                                        @selected($defaultTarget === $column)>
                                        {{ $column }} ({{ $typeLabels[$targetType] ?? ucfirst($targetType) }}{{ $targetNote }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="ml-help">The target is never used as a feature. DataSensei checks this again before training.</div>
                        </div>

                        <div class="ml-insight" id="target-insight" aria-live="polite">
                            <div class="ml-insight-cell"><span>Column type</span><strong data-insight="type">—</strong></div>
                            <div class="ml-insight-cell"><span>Different values</span><strong data-insight="unique">—</strong></div>
                            <div class="ml-insight-cell"><span>Empty cells</span><strong data-insight="missing">—</strong></div>
                            <div class="ml-insight-cell"><span>Suggested problem type</span><strong data-insight="problem">—</strong></div>
                            <div class="ml-insight-note" data-insight="note"></div>
                        </div>

                        @include('student.model-development.partials.step-guide', ['guideStep' => 2])
                        <div class="ml-alert error ml-step-error" data-step-error="2"></div>
                    </section>

                    {{-- STEP 3: FEATURES --}}
                    <section class="ml-card ml-workflow-step" data-workflow-step="3">
                        @include('student.model-development.partials.step-header', [
                            'stepNumber' => 3,
                            'stepTitle' => 'Which columns can the model use?',
                            'stepLead' => 'Tick the columns that give useful clues about the target. These are the model\'s features.',
                        ])

                        <div class="ml-toolbar" role="toolbar" aria-label="Feature selection shortcuts">
                            <button class="ml-btn small secondary" type="button" data-feature-action="all">Select all usable</button>
                            <button class="ml-btn small secondary" type="button" data-feature-action="none">Clear all</button>
                            @if($recommendedFeatures !== [])
                                <button class="ml-btn small secondary" type="button" data-feature-action="recommended">Use recommended ({{ count($recommendedFeatures) }})</button>
                            @endif
                            <span class="ml-counter" id="feature-counter" aria-live="polite"></span>
                        </div>
                        <div class="ml-inline-note" id="feature-mode-note" hidden></div>

                        <div class="ml-check-grid" style="margin-top:14px">
                            @foreach($headers as $column)
                                @php
                                    $columnProfile = (array) ($columnProfiles[$column] ?? []);
                                    $columnType = (string) ($columnProfile['type'] ?? 'unknown');
                                    $isIdentifier = (bool) ($columnProfile['is_identifier_like'] ?? false);
                                    $unavailable = $isIdentifier || $columnType === 'empty';
                                    $unavailableReason = $isIdentifier
                                        ? 'Looks like an ID, so it cannot help predictions.'
                                        : ($columnType === 'empty' ? 'This column has no values.' : '');
                                    $isNumericColumn = in_array($columnType, $numericTypes, true);
                                @endphp
                                <label class="ml-check" data-feature-card="{{ $column }}">
                                    <input
                                        type="checkbox"
                                        name="features[]"
                                        value="{{ $column }}"
                                        data-feature-type="{{ $columnType }}"
                                        data-profile-unavailable="{{ $unavailable ? 'true' : 'false' }}"
                                        data-profile-reason="{{ $unavailableReason }}"
                                        @checked(in_array($column, $defaultFeatures, true))
                                        @disabled($unavailable)>
                                    <span>
                                        <strong>{{ $column }}</strong>
                                        @if(in_array($column, $recommendedFeatures, true))
                                            <span class="ml-badge good" style="padding:2px 7px;margin-left:4px" title="Recommended feature">Recommended</span>
                                        @endif
                                        <br>
                                        <span class="ml-type-chip {{ $isNumericColumn ? 'num' : (in_array($columnType, ['categorical', 'boolean'], true) ? 'cat' : '') }}">{{ $typeLabels[$columnType] ?? ucfirst($columnType) }}</span>
                                        <span class="ml-muted" style="font-size:.72rem">{{ number_format((int) ($columnProfile['unique_count'] ?? 0)) }} different values</span>
                                        <span class="ml-check-reason" data-feature-reason>{{ $unavailableReason }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        @include('student.model-development.partials.step-guide', ['guideStep' => 3])
                        <div class="ml-alert error ml-step-error" data-step-error="3"></div>
                    </section>

                    {{-- STEP 4: PROBLEM TYPE --}}
                    <section class="ml-card ml-workflow-step" data-workflow-step="4">
                        @include('student.model-development.partials.step-header', [
                            'stepNumber' => 4,
                            'stepTitle' => 'What kind of answer is it?',
                            'stepLead' => 'DataSensei picked a problem type from your target. Keep it unless you have a clear reason to change it.',
                        ])

                        <div class="ml-grid" style="margin-top:18px">
                            @foreach([
                                'classification' => ['Classification', 'Predict a category', 'The answer is a label such as Yes/No, Pass/Fail, or a species name.'],
                                'regression' => ['Regression', 'Predict a number', 'The answer is a number on a scale, such as a price, a score, or a temperature.'],
                                'clustering' => ['Clustering', 'Discover groups', 'There is no answer column. The model finds rows that are similar to each other.'],
                            ] as $key => [$label, $headline, $description])
                                <label class="ml-card ml-algorithm ml-problem-choice">
                                    <div class="ml-choice-top">
                                        <strong>{{ $label }}</strong>
                                        <input type="radio" name="problem_type" value="{{ $key }}" @checked($selectedProblem === $key)>
                                    </div>
                                    <strong style="display:block;margin:8px 0 5px;font-size:.84rem;color:#dbeafe">{{ $headline }}</strong>
                                    <span class="ml-muted" style="font-size:.8rem">{{ $description }}</span>
                                    <div class="ml-badge-row">
                                        <span class="ml-badge blue" data-problem-suggested="{{ $key }}" hidden>Matches your target</span>
                                        @if($recommended && ($recommended['problem_type'] ?? null) === $key)
                                            <span class="ml-badge good">Recommended</span>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <div class="ml-inline-note" id="problem-guidance"></div>

                        @include('student.model-development.partials.step-guide', ['guideStep' => 4])
                        <div class="ml-alert error ml-step-error" data-step-error="4"></div>
                    </section>

                    {{-- STEP 5: TRAIN / TEST SPLIT --}}
                    <section class="ml-card ml-workflow-step" data-workflow-step="5">
                        @include('student.model-development.partials.step-header', [
                            'stepNumber' => 5,
                            'stepTitle' => 'Keep some rows for testing',
                            'stepLead' => 'The model learns from the training rows. The test rows stay hidden until the end, like a final exam.',
                        ])

                        <div id="split-field">
                            <div class="ml-field" style="margin-top:18px">
                                <label class="ml-label" for="test_size">Training and testing data</label>
                                <select class="ml-select" id="test_size" name="test_size">
                                    @foreach($allowedTestSizes as $size)
                                        @php $testPercent = (int) round((float) $size * 100); @endphp
                                        <option value="{{ $size }}" @selected((float) $defaultTestSize === (float) $size)>
                                            {{ 100 - $testPercent }}% training / {{ $testPercent }}% testing{{ $testPercent === 20 ? ' (recommended)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="ml-split-visual" aria-hidden="true">
                                <div class="ml-split-bar">
                                    <div class="ml-split-train" id="split-train-bar" style="width:80%">Training</div>
                                    <div class="ml-split-test" id="split-test-bar">Testing</div>
                                </div>
                                <div class="ml-split-legend">
                                    <span>About <strong id="split-train-rows">—</strong> rows to learn from</span>
                                    <span>About <strong id="split-test-rows">—</strong> rows for the final check</span>
                                </div>
                            </div>
                            <p class="ml-help">Row counts are estimates. Duplicate rows and rows with an empty target are removed before the split.</p>
                        </div>
                        <div class="ml-alert" id="clustering-split-note" hidden>Clustering has no right answers to check, so it uses all usable rows and does not need a test split. You can continue.</div>

                        <details class="ml-advanced" style="margin-top:18px">
                            <summary>Advanced Options</summary>
                            <p class="ml-help">You can skip these for your first model. The defaults are safe.</p>
                            <div class="ml-grid two" style="margin-top:16px">
                                <div class="ml-field">
                                    <label class="ml-label" for="random_state">Random state</label>
                                    <select class="ml-select" id="random_state" name="random_state">
                                        @foreach($allowedRandomStates as $seed)
                                            <option value="{{ $seed }}" @selected((int) $defaultRandomState === (int) $seed)>{{ $seed }}</option>
                                        @endforeach
                                    </select>
                                    <div class="ml-help">Shuffles rows the same way every time, so you can repeat an experiment and get the same split.</div>
                                </div>
                                <div class="ml-field">
                                    <label class="ml-label" for="cross_validation">Cross-validation</label>
                                    <select class="ml-select" id="cross_validation" name="cross_validation">
                                        @foreach($cvFolds as $fold)
                                            <option value="{{ $fold }}" @selected((int) $defaultCrossValidation === (int) $fold)>{{ (int) $fold === 0 ? 'Disabled' : $fold.' folds' }}</option>
                                        @endforeach
                                    </select>
                                    <div class="ml-help">Trains and checks the model several times on different slices of the training rows for a steadier score.</div>
                                </div>
                                <div class="ml-field">
                                    <label class="ml-label" for="scale_mode">Feature scaling</label>
                                    <select class="ml-select" id="scale_mode" name="scale_mode">
                                        <option value="auto" @selected($defaultScaleMode === 'auto')>Automatic (recommended)</option>
                                        <option value="standard" @selected($defaultScaleMode === 'standard')>Always scale</option>
                                        <option value="none" @selected($defaultScaleMode === 'none')>No scaling</option>
                                    </select>
                                    <div class="ml-help">Puts numbers on a similar scale. Automatic turns it on for algorithms that measure distance.</div>
                                </div>
                                <div class="ml-field">
                                    <label class="ml-label" for="numeric_imputation">Fill empty numbers with</label>
                                    <select class="ml-select" id="numeric_imputation" name="numeric_imputation">
                                        <option value="median" @selected($defaultImputation === 'median')>Median (middle value)</option>
                                        <option value="mean" @selected($defaultImputation === 'mean')>Mean (average)</option>
                                    </select>
                                    <div class="ml-help">The median is less affected by unusually large or small values.</div>
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

                        @include('student.model-development.partials.step-guide', ['guideStep' => 5])
                        <div class="ml-alert error ml-step-error" data-step-error="5"></div>
                    </section>

                    {{-- STEP 6: ALGORITHM --}}
                    <section class="ml-card ml-workflow-step" data-workflow-step="6">
                        @include('student.model-development.partials.step-header', [
                            'stepNumber' => 6,
                            'stepTitle' => 'Choose how the model learns',
                            'stepLead' => 'Only algorithms that fit your problem type are shown. Beginner-friendly ones are easier to explain.',
                        ])

                        @if($recommended)
                            <div class="ml-recommended-model" id="recommended-model" data-recommended-problem="{{ $recommended['problem_type'] }}" style="margin-top:18px">
                                <div class="ml-recommended-model-head">
                                    <div>
                                        <h3>Recommended for this dataset: {{ $recommended['model_label'] }}</h3>
                                    </div>
                                    <span class="ml-badge good">{{ $problemLabels[$recommended['problem_type']] ?? ucfirst($recommended['problem_type']) }}</span>
                                </div>
                                <p><strong>Why this model?</strong><br>{{ $recommended['reason'] }}</p>
                                <button class="ml-btn use-recommended-model" type="button" data-algorithm="{{ $recommended['algorithm_key'] }}">Use {{ $recommended['model_label'] }}</button>
                            </div>
                        @endif

                        <div class="ml-grid" id="algorithm-grid" style="margin-top:18px">
                            @foreach($allAlgorithms as $key => $algorithm)
                                @php $guide = (array) ($allLearningGuides[$key] ?? []); @endphp
                                <label class="ml-card ml-algorithm" data-algorithm-card data-problem="{{ $algorithm['problem_type'] }}">
                                    <div class="ml-choice-top">
                                        <strong>{{ $algorithm['label'] }}</strong>
                                        <input
                                            type="radio"
                                            name="algorithm_key"
                                            value="{{ $key }}"
                                            data-algorithm="{{ $key }}"
                                            data-beginner="{{ ($guide['beginner_friendly'] ?? false) ? 'true' : 'false' }}"
                                            @checked($defaultAlgorithm === $key)>
                                    </div>
                                    <div class="ml-badge-row" style="margin-top:6px">
                                        @if($guide['beginner_friendly'] ?? false)
                                            <span class="ml-badge blue">Beginner friendly</span>
                                        @endif
                                        @if($recommended && ($recommended['algorithm_key'] ?? null) === $key)
                                            <span class="ml-badge good">Recommended</span>
                                        @endif
                                    </div>
                                    <p class="ml-algo-analogy">{{ $guide['analogy'] ?? $algorithm['description'] }}</p>
                                    <div class="ml-help"><strong>Why it fits:</strong> {{ $guide['why_suitable'] ?? 'A controlled educational baseline for this problem type.' }}</div>
                                    <div class="ml-help"><strong>Training time:</strong> {{ $guide['expected_training_time'] ?? ($algorithm['expected_training_time'] ?? 'Depends on dataset size.') }}</div>
                                    <details class="ml-algo-more">
                                        <summary>Strengths and limits</summary>
                                        @if(! empty($guide['strengths']))
                                            <span class="ml-mini-title">Good at</span>
                                            <ul class="ml-mini-list">@foreach($guide['strengths'] as $item)<li>{{ $item }}</li>@endforeach</ul>
                                        @endif
                                        @if(! empty($guide['weaknesses']))
                                            <span class="ml-mini-title">Watch out for</span>
                                            <ul class="ml-mini-list">@foreach($guide['weaknesses'] as $item)<li>{{ $item }}</li>@endforeach</ul>
                                        @endif
                                        @if(! empty($guide['expected_behavior']))
                                            <span class="ml-mini-title">What to expect</span>
                                            <p class="ml-help" style="margin-top:4px">{{ $guide['expected_behavior'] }}</p>
                                        @endif
                                    </details>
                                </label>
                            @endforeach
                        </div>

                        <details class="ml-advanced" style="margin-top:18px">
                            <summary>Advanced Options</summary>
                            <p class="ml-help">Only the selected algorithm's settings are sent. Leave the defaults for your first model, then change one setting at a time.</p>
                            @foreach($allAlgorithms as $key => $algorithm)
                                <div class="algorithm-parameters" data-parameters="{{ $key }}" hidden>
                                    <h3 class="ml-section-title" style="margin-top:16px">{{ $algorithm['label'] }} settings</h3>
                                    @if(empty($algorithm['parameters']))
                                        <p class="ml-muted" style="font-size:.8rem">This algorithm has no settings to tune here.</p>
                                    @else
                                        <div class="ml-grid two">
                                            @foreach((array) $algorithm['parameters'] as $parameter => $schema)
                                                @php $parameterValue = old('parameters.'.$parameter, data_get($resume, 'parameters.'.$parameter, $schema['default'] ?? '')); @endphp
                                                <div class="ml-field">
                                                    <label class="ml-label" for="parameter-{{ $key }}-{{ $parameter }}">{{ $schema['label'] }}</label>
                                                    @if(($schema['type'] ?? '') === 'select')
                                                        <select class="ml-select" id="parameter-{{ $key }}-{{ $parameter }}" name="parameters[{{ $parameter }}]" data-parameter-input>
                                                            @foreach($schema['options'] as $option)
                                                                <option value="{{ $option }}" @selected((string) $parameterValue === (string) $option)>{{ $option }}{{ (string) $option === (string) ($schema['default'] ?? '') ? ' (default)' : '' }}</option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <input class="ml-input" id="parameter-{{ $key }}-{{ $parameter }}" type="number" name="parameters[{{ $parameter }}]" data-parameter-input value="{{ $parameterValue }}" min="{{ $schema['min'] ?? '' }}" max="{{ $schema['max'] ?? '' }}" step="{{ ($schema['type'] ?? '') === 'float' ? 'any' : ($schema['step'] ?? 1) }}">
                                                    @endif
                                                    <div class="ml-help">
                                                        {{ \App\Support\ModelDevelopmentGuide::parameterHint((string) $parameter) }}
                                                        @if(isset($schema['min'], $schema['max']))
                                                            Allowed: {{ $schema['min'] }} to {{ $schema['max'] }}. Default: {{ $schema['default'] ?? '—' }}.
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </details>

                        @include('student.model-development.partials.step-guide', ['guideStep' => 6])
                        <div class="ml-alert error ml-step-error" data-step-error="6"></div>
                    </section>

                    {{-- STEP 7: REVIEW AND TRAIN --}}
                    <section class="ml-card ml-workflow-step ml-training-milestone" data-workflow-step="7">
                        @include('student.model-development.partials.step-header', [
                            'stepNumber' => 7,
                            'stepTitle' => 'Review and train',
                            'stepLead' => 'Check your choices below. Use Edit to fix anything, then give your model a name and start training.',
                        ])

                        <div class="ml-review-list" id="review-list" aria-label="Training configuration summary">
                            <div class="ml-review-item"><span class="ml-review-mark">✓</span><div><span class="ml-review-label">Dataset</span><span class="ml-review-value">{{ $dataset->name }}</span></div><span></span></div>
                            <div class="ml-review-item" data-review-step="2"><span class="ml-review-mark" data-review-mark>✓</span><div><span class="ml-review-label">Target</span><span class="ml-review-value" id="review-target">-</span></div><button class="ml-link-btn" type="button" data-edit-step="2">Edit</button></div>
                            <div class="ml-review-item" data-review-step="3"><span class="ml-review-mark" data-review-mark>✓</span><div><span class="ml-review-label">Features</span><span class="ml-review-value"><span class="ml-summary-list" id="review-features"></span></span></div><button class="ml-link-btn" type="button" data-edit-step="3">Edit</button></div>
                            <div class="ml-review-item" data-review-step="4"><span class="ml-review-mark" data-review-mark>✓</span><div><span class="ml-review-label">Problem type</span><span class="ml-review-value" id="review-problem">-</span></div><button class="ml-link-btn" type="button" data-edit-step="4">Edit</button></div>
                            <div class="ml-review-item" data-review-step="5"><span class="ml-review-mark" data-review-mark>✓</span><div><span class="ml-review-label">Train/Test</span><span class="ml-review-value" id="review-split">-</span></div><button class="ml-link-btn" type="button" data-edit-step="5">Edit</button></div>
                            <div class="ml-review-item" data-review-step="6"><span class="ml-review-mark" data-review-mark>✓</span><div><span class="ml-review-label">Algorithm</span><span class="ml-review-value" id="review-algorithm">-</span></div><button class="ml-link-btn" type="button" data-edit-step="6">Edit</button></div>
                        </div>

                        <div class="ml-grid two">
                            <div class="ml-field">
                                <label class="ml-label" for="model_name">Model name</label>
                                <input class="ml-input" id="model_name" name="model_name" value="{{ $defaultModelName }}" maxlength="160" required>
                                <div class="ml-help">Pick a name you will recognise later, for example "{{ $dataset->name }} – first try".</div>
                            </div>
                            <div class="ml-field">
                                <label class="ml-label" for="class_id">Share with a class</label>
                                <select class="ml-select" id="class_id" name="class_id">
                                    <option value="">Keep private</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" @selected($defaultClassId == $class->id)>{{ $class->name }}{{ $class->section ? ' · '.$class->section : '' }}</option>
                                    @endforeach
                                </select>
                                <div class="ml-help">Linking a class lets your instructor see this experiment.</div>
                            </div>
                        </div>

                        <div class="ml-training-ready" id="training-ready">
                            <span class="ml-training-ready-icon">✓</span>
                            <div><strong id="training-ready-title">You're ready to train your model.</strong><br><span class="ml-help">Next, you will watch the real training progress. It usually takes a few seconds to a minute.</span></div>
                        </div>

                        <div class="ml-alert error ml-step-error" data-step-error="7"></div>
                        <button class="ml-btn" type="submit" id="train-model-button">Train Model</button>

                        @include('student.model-development.partials.step-guide', ['guideStep' => 7])
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
    const testSize = document.getElementById('test_size');
    const profile = @json((object) $columnProfiles);
    const rowCount = Number(@json($totalRows)) || 0;
    const recommendation = @json($recommended);
    const algorithmDefinitions = @json((object) $allAlgorithms);
    const algorithmLabels = @json((object) $algorithmLabels);
    const typeLabels = @json($typeLabels);
    const problemLabels = @json($problemLabels);
    const datasetUrl = @json($datasetPage);
    const initialError = @json($validationStep);
    const numericTypes = ['integer', 'decimal'];
    const maxFeatures = 50;
    const maxClasses = 100;
    let currentStep = {{ $initialStep }};
    let completedThrough = {{ $initialCompletedThrough }};
    let errorStep = initialError ? Number(initialError) : null;
    let firstRender = true;

    const currentProblem = () => document.querySelector('input[name="problem_type"]:checked')?.value || 'classification';
    const featureInputs = () => [...document.querySelectorAll('input[name="features[]"]')];
    const selectedFeatures = () => featureInputs().filter(input => input.checked && !input.disabled);
    const selectedAlgorithm = () => document.querySelector('input[name="algorithm_key"]:checked:not(:disabled)')?.value || '';
    const titleCase = value => String(value || '').replace(/_/g, ' ').replace(/\b\w/g, character => character.toUpperCase());

    // Mirrors DatasetProfiler::detectProblemType so the suggestion matches the server's detection.
    const suggestProblem = column => {
        if (!column || !profile[column]) return 'clustering';
        const definition = profile[column];
        const unique = Number(definition.unique_count || 0);
        if (['boolean', 'categorical', 'text'].includes(definition.type)) return 'classification';
        if (numericTypes.includes(definition.type)) {
            const limit = Math.min(20, Math.max(2, Math.floor(rowCount * 0.10)));
            return unique >= 2 && unique <= limit ? 'classification' : 'regression';
        }
        return null;
    };

    const setProblem = problem => {
        const radio = document.querySelector(`input[name="problem_type"][value="${CSS.escape(problem)}"]`);
        if (radio && !radio.checked) {
            radio.checked = true;
            syncAlgorithms();
            return true;
        }
        return false;
    };

    // Returns a plain-language problem for a step, or an empty string when the step is valid.
    const problemFor = step => {
        const problem = currentProblem();
        const value = target.value;
        const definition = profile[value] || null;

        if (step === 2 || step === 4) {
            if (problem === 'clustering') {
                return value && step === 4 ? 'Clustering does not use a target. Go back to Target and choose "No target", or pick Classification/Regression.' : '';
            }
            if (!value || !definition) return 'Choose a target column, or pick "No target" to discover groups.';
            if (['date', 'empty'].includes(definition.type)) return 'This column cannot be predicted here. Choose a category or number column.';
            if (problem === 'regression' && !numericTypes.includes(definition.type)) {
                return 'Regression needs a number target. Choose a numeric column or switch to Classification.';
            }
            if (problem === 'classification') {
                const unique = Number(definition.unique_count || 0);
                if (unique < 2) return 'Classification needs a target with at least two different values.';
                if (unique > maxClasses) return `This target has ${unique.toLocaleString()} different values. That is too many categories – use Regression for numbers.`;
            }
            return '';
        }

        if (step === 3) {
            const features = selectedFeatures();
            if (features.length < 1) return 'Select at least one feature column.';
            if (features.length > maxFeatures) return `Select no more than ${maxFeatures} features for one training run (you have ${features.length}).`;
            if (problem !== 'clustering' && features.some(input => input.value === value)) {
                return 'The target column cannot also be a feature.';
            }
            if (problem === 'clustering') {
                if (features.length < 2) return 'Clustering needs at least two number columns.';
                if (features.some(input => !numericTypes.includes(input.dataset.featureType))) return 'Clustering can only use number columns.';
            }
            return '';
        }

        if (step === 5) {
            return problem !== 'clustering' && !testSize.value ? 'Choose how much data to keep for testing.' : '';
        }

        if (step === 6) {
            const algorithm = selectedAlgorithm();
            return !algorithm || algorithmDefinitions[algorithm]?.problem_type !== problem
                ? 'Choose an algorithm that matches your problem type.'
                : '';
        }

        if (step === 7) {
            return document.getElementById('model_name').value.trim() ? '' : 'Give your model a name before training.';
        }

        return '';
    };

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

    const showInfo = (step, message) => {
        const box = document.querySelector(`[data-step-error="${step}"]`);
        if (!box) return;
        box.textContent = message;
        box.classList.remove('error');
        box.classList.add('visible');
    };

    const validate = step => {
        clearError(step);
        // Choosing "No target" is a valid beginner path: switch to clustering instead of blocking.
        if (step === 2 && !target.value && currentProblem() !== 'clustering') {
            setProblem('clustering');
            syncFeatureAvailability();
        }
        const message = problemFor(step);
        return message ? showError(step, message) : true;
    };

    const renderTargetInsight = () => {
        const set = (key, text) => {
            const node = document.querySelector(`[data-insight="${key}"]`);
            if (node) node.textContent = text;
        };
        const note = document.querySelector('[data-insight="note"]');
        const value = target.value;
        const definition = profile[value] || null;
        const suggestion = suggestProblem(value);
        let message = '';
        let warn = false;

        if (!value) {
            set('type', 'No target');
            set('unique', '—');
            set('missing', '—');
            set('problem', problemLabels.clustering);
            message = 'No target selected. The model will look for groups of similar rows instead of predicting an answer. You will need at least two number columns in the next step.';
        } else if (definition) {
            const unique = Number(definition.unique_count || 0);
            const samples = (definition.sample_values || []).slice(0, 4).join(', ');
            set('type', typeLabels[definition.type] || titleCase(definition.type));
            set('unique', unique.toLocaleString());
            set('missing', `${Number(definition.missing_percent || 0).toFixed(1)}%`);
            set('problem', suggestion ? problemLabels[suggestion] : 'Not suitable');

            if (suggestion === 'classification') {
                message = `"${value}" has ${unique.toLocaleString()} different values${samples ? ` (for example ${samples})` : ''}, so the model will learn to choose a category.`;
            } else if (suggestion === 'regression') {
                const range = definition.minimum !== null && definition.maximum !== null && definition.minimum !== undefined
                    ? ` from ${Number(definition.minimum).toLocaleString()} to ${Number(definition.maximum).toLocaleString()}`
                    : '';
                message = `"${value}" is a number${range}, so the model will learn to estimate a value on that scale.`;
            } else {
                message = 'This column type cannot be used as a target. Choose a category or number column.';
                warn = true;
            }

            if (definition.is_identifier_like) {
                message += ' Careful: this column looks like an ID. IDs are unique labels and usually cannot be learned.';
                warn = true;
            }
            if (suggestion === 'classification' && unique > maxClasses) {
                message += ` It has more than ${maxClasses} categories, which is too many for classification.`;
                warn = true;
            }
            if (Number(definition.missing_percent || 0) > 0) {
                message += ' Rows with an empty target are skipped during training.';
            }
        }

        note.textContent = message;
        note.classList.toggle('warn', warn);

        document.querySelectorAll('[data-problem-suggested]').forEach(badge => {
            badge.hidden = badge.dataset.problemSuggested !== suggestion;
        });
    };

    const syncFeatureAvailability = () => {
        const problem = currentProblem();
        const clustering = problem === 'clustering';
        featureInputs().forEach(input => {
            const unavailableByProfile = input.dataset.profileUnavailable === 'true';
            const isTarget = !clustering && input.value === target.value;
            const notNumeric = clustering && !numericTypes.includes(input.dataset.featureType);
            const disabled = unavailableByProfile || isTarget || notNumeric;
            if (disabled) input.checked = false;
            input.disabled = disabled;

            const card = input.closest('[data-feature-card]');
            card?.classList.toggle('is-target', isTarget);
            const reason = card?.querySelector('[data-feature-reason]');
            if (reason) {
                reason.textContent = unavailableByProfile
                    ? (input.dataset.profileReason || 'Not available for training.')
                    : (isTarget ? 'This is your target, so it cannot be a feature.' : (notNumeric ? 'Clustering can only use number columns.' : ''));
            }
        });

        const note = document.getElementById('feature-mode-note');
        note.hidden = !clustering;
        note.textContent = clustering
            ? 'You chose clustering, so only number columns can be selected. Pick at least two.'
            : '';
        updateFeatureCounter();
    };

    const updateFeatureCounter = () => {
        const usable = featureInputs().filter(input => !input.disabled).length;
        const selected = selectedFeatures().length;
        const counter = document.getElementById('feature-counter');
        counter.innerHTML = '';
        const strong = document.createElement('strong');
        strong.textContent = String(selected);
        counter.append(strong, ` of ${usable} usable columns selected`);
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
        if (!selected) selected = compatible.find(input => input.dataset.beginner === 'true') || compatible[0] || null;
        if (selected) selected.checked = true;

        const clustering = problem === 'clustering';
        document.getElementById('split-field').hidden = clustering;
        document.getElementById('clustering-split-note').hidden = !clustering;

        const guidance = document.getElementById('problem-guidance');
        const suggestion = suggestProblem(target.value);
        let text = clustering
            ? 'Clustering finds groups on its own and does not use a target. It is scored with the silhouette score.'
            : (problem === 'regression'
                ? 'Regression predicts a number. It is scored with R² (how much is explained) and the typical error (RMSE, MAE).'
                : 'Classification predicts a category. It is scored with accuracy, precision, recall, and F1.');
        if (suggestion && suggestion !== problem) {
            text += ` Your target looks like a ${problemLabels[suggestion]} problem, so double-check this choice.`;
        }
        guidance.textContent = text;

        syncParameters();
        syncFeatureAvailability();
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

    const updateSplitVisual = () => {
        const size = Number(testSize.value) || 0.2;
        const trainPercent = Math.round((1 - size) * 100);
        const trainRows = Math.round(rowCount * (1 - size));
        document.getElementById('split-train-bar').style.width = `${trainPercent}%`;
        document.getElementById('split-train-bar').textContent = `Training ${trainPercent}%`;
        document.getElementById('split-test-bar').textContent = `Testing ${100 - trainPercent}%`;
        document.getElementById('split-train-rows').textContent = trainRows.toLocaleString();
        document.getElementById('split-test-rows').textContent = Math.max(0, rowCount - trainRows).toLocaleString();
    };

    const updateReview = () => {
        const problem = currentProblem();
        const algorithm = selectedAlgorithm();
        const features = selectedFeatures().map(input => input.value);
        document.getElementById('review-target').textContent = problem === 'clustering' ? 'No target (clustering)' : (target.value || 'Not selected');
        document.getElementById('review-problem').textContent = problemLabels[problem] || titleCase(problem);
        document.getElementById('review-algorithm').textContent = algorithmLabels[algorithm] || 'Not selected';
        document.getElementById('review-split').textContent = problem === 'clustering'
            ? 'All usable rows (no split needed)'
            : testSize.selectedOptions[0]?.textContent.trim() || 'Not selected';
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

        let allGood = true;
        document.querySelectorAll('[data-review-step]').forEach(item => {
            const message = problemFor(Number(item.dataset.reviewStep));
            item.classList.toggle('bad', Boolean(message));
            item.querySelector('[data-review-mark]').textContent = message ? '!' : '✓';
            item.title = message;
            if (message) allGood = false;
        });
        const ready = document.getElementById('training-ready');
        ready.style.display = allGood ? 'flex' : 'none';
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
            const nextName = ({2: 'Features', 3: 'Problem Type', 4: 'Train/Test Split', 5: 'Algorithm', 6: 'Review & Train'})[currentStep];
            next.textContent = `Continue to ${nextName} →`;
            nextHint.textContent = `Step ${currentStep} of 10`;
        }
        if (currentStep === 7) updateReview();
        if (!firstRender) {
            document.querySelector('.ml-roadmap-content')?.scrollIntoView({behavior: 'smooth', block: 'start'});
        }
        firstRender = false;
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

    const applyRecommendedFeatures = () => {
        const wanted = new Set(recommendation?.features || []);
        syncFeatureAvailability();
        featureInputs().forEach(input => {
            if (!input.disabled) input.checked = wanted.has(input.value);
        });
        updateFeatureCounter();
    };

    const applyRecommendedSetup = () => {
        if (!recommendation) return;
        const problem = document.querySelector(`input[name="problem_type"][value="${CSS.escape(recommendation.problem_type)}"]`);
        if (problem) problem.checked = true;
        target.value = recommendation.target || '';
        syncAlgorithms();
        applyRecommendedFeatures();
        const algorithm = document.querySelector(`input[name="algorithm_key"][value="${CSS.escape(recommendation.algorithm_key)}"]:not(:disabled)`);
        if (algorithm) algorithm.checked = true;
        syncParameters();
        renderTargetInsight();
        invalidateFrom(2);
        updateReview();
        showInfo(2, `Recommended setup applied: target "${recommendation.target}", ${(recommendation.features || []).length} features, ${problemLabels[recommendation.problem_type] || recommendation.problem_type}, and ${recommendation.model_label}. Press Continue to check each step – you can still change anything.`);
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

    document.querySelectorAll('[data-edit-step]').forEach(button => {
        button.addEventListener('click', () => goTo(Number(button.dataset.editStep)));
    });

    roadmap.querySelector('[data-roadmap-step="1"] a')?.addEventListener('click', event => {
        if (!window.confirm('Choose another dataset? Only this unfinished setup will reset. Your trained models stay in Model History.')) {
            event.preventDefault();
        }
    });

    document.querySelectorAll('[data-feature-action]').forEach(button => {
        button.addEventListener('click', () => {
            const action = button.dataset.featureAction;
            if (action === 'recommended') {
                applyRecommendedFeatures();
            } else {
                syncFeatureAvailability();
                featureInputs().forEach(input => {
                    if (!input.disabled) input.checked = action === 'all';
                });
            }
            updateFeatureCounter();
            invalidateFrom(3);
            updateReview();
        });
    });

    form.addEventListener('change', event => {
        const panel = event.target.closest('[data-workflow-step]');
        if (panel) invalidateFrom(Number(panel.dataset.workflowStep));

        if (event.target === target) {
            const suggestion = suggestProblem(target.value);
            const previousProblem = currentProblem();
            if (suggestion) setProblem(suggestion);
            syncFeatureAvailability();
            renderTargetInsight();
            if (suggestion && suggestion !== previousProblem) {
                showInfo(2, target.value
                    ? `Problem type was set to ${problemLabels[suggestion]} to match this target. You can review it in Step 4.`
                    : 'No target selected, so the problem type was set to Clustering. You can review it in Step 4.');
            } else {
                clearError(2);
            }
        }
        if (event.target.matches('input[name="problem_type"]')) {
            syncAlgorithms();
            renderTargetInsight();
        }
        if (event.target.matches('input[name="algorithm_key"]')) syncParameters();
        if (event.target.matches('input[name="features[]"]')) updateFeatureCounter();
        if (event.target === testSize) updateSplitVisual();
        updateReview();
    });

    form.addEventListener('input', event => {
        if (event.target.id === 'model_name') updateReview();
    });

    // Pressing Enter inside a field must never start training before the review step.
    form.addEventListener('keydown', event => {
        if (event.key !== 'Enter') return;
        const element = event.target;
        if (element instanceof HTMLTextAreaElement || element instanceof HTMLButtonElement) return;
        if (currentStep !== 7) {
            event.preventDefault();
            next.click();
        }
    });

    form.addEventListener('submit', event => {
        for (let step = 2; step <= 7; step++) {
            if (!validate(step)) {
                event.preventDefault();
                return;
            }
        }
        const button = document.getElementById('train-model-button');
        button.disabled = true;
        button.textContent = 'Starting training...';
    });

    document.querySelectorAll('.use-recommended-setup').forEach(button => button.addEventListener('click', applyRecommendedSetup));
    document.querySelectorAll('.use-recommended-model').forEach(button => button.addEventListener('click', () => {
        const radio = document.querySelector(`input[name="algorithm_key"][value="${CSS.escape(button.dataset.algorithm)}"]:not(:disabled)`);
        if (!radio) return;
        radio.checked = true;
        syncParameters();
        invalidateFrom(6);
        updateReview();
    }));

    syncAlgorithms();
    syncFeatureAvailability();
    renderTargetInsight();
    updateSplitVisual();
    updateReview();
    render();
})();
</script>
</body>
</html>
