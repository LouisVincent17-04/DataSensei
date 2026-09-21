<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Model Development — DataSensei</title>
    @include('student.model-development.partials.styles')
    @include('partials.page-head', ['pageTitle' => 'Model Development', 'pageDescription' => 'Train a real machine-learning model in four short steps and use it to make predictions.'])
</head>
<body>
<div class="ml-layout">
    @include('partials.sidebar')
    <main class="ml-main">
        @php
            $term = fn (string $key, ?string $text = null) => \App\Support\ModelDevelopmentGlossary::term($key, $text);
            $presets = \App\Support\ModelDevelopmentOutcome::presets();
        @endphp
        <header class="ml-head">
            <div>
                <h1 class="ml-title ds-page-title">Model Development</h1>
                <p class="ml-subtitle">Teach a computer to answer a question from examples. Pick some data, say what you want predicted, and try it out. No coding needed.</p>
            </div>
            <div class="ml-actions">
                @if($models->isNotEmpty())
                    <a class="ml-btn secondary" href="#model-history">My models</a>
                @endif
                <a class="ml-btn secondary" href="{{ route('student.data-toolkit.index') }}">Data Toolkit</a>
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
        ])

        <div class="ml-flow">
                @if($activeTrainingJob)
                    <section class="ml-card ml-starter ml-resume">
                        <div>
                            <h3>A model is still training: {{ $activeTrainingJob->model_name }}</h3>
                            <p class="ml-muted" style="font-size:.8125rem">{{ $activeTrainingJob->stage ?: 'Waiting to start' }}, {{ $activeTrainingJob->progress }}% done</p>
                        </div>
                        <a class="ml-btn" href="{{ route('student.model-development.training.show', $activeTrainingJob) }}">See progress</a>
                    </section>
                @endif

                <section>
                    <div class="ml-section-head">
                        <div>
                            <h2 class="ml-section-title">Pick a question to answer</h2>
                            <p class="ml-muted">Each {{ $term('dataset', 'dataset') }} below comes with a question a {{ $term('model', 'model') }} can learn to answer. Everything is set up for you, so you can train in one click and change things later.</p>
                        </div>
                    </div>

                    <div class="ml-grid">
                        @forelse($systemDatasets as $dataset)
                            @php
                                $preset = (array) ($presets[$dataset->slug] ?? []);
                                $isNumberAnswer = $dataset->problem_type === 'regression';
                            @endphp
                            <article class="ml-card ml-data-card ml-raised">
                                <div>
                                    <h3>{{ $preset['question'] ?? $dataset->name }}</h3>
                                    <span class="ml-data-name">{{ $dataset->name }}@if($starterDataset && $starterDataset->id === $dataset->id), a good first one @endif</span>
                                </div>
                                <p class="ml-muted" style="font-size:.8125rem">{{ $preset['story'] ?? $dataset->description }}</p>
                                <p class="ml-data-facts">
                                    {{ number_format($dataset->row_count) }} examples, {{ max(0, (int) $dataset->column_count - 1) }} clues each.
                                    The answer is {{ $isNumberAnswer ? 'a number' : 'a category' }}
                                    ({{ $isNumberAnswer ? $term('regression', 'regression') : $term('classification', 'classification') }}).
                                </p>
                                <div class="ml-actions">
                                    <a class="ml-btn" href="{{ route('student.model-development.wizard', ['dataset_type' => 'system', 'dataset_id' => $dataset->id]) }}">Use this data</a>
                                    <a class="ml-btn secondary" href="{{ route('student.model-development.system-datasets.show', $dataset) }}">Look inside</a>
                                </div>
                            </article>
                        @empty
                            <div class="ml-card">
                                <p class="ml-muted">No built-in datasets are registered. Run <code>php artisan ml:install</code> to install the bundled learning datasets.</p>
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="ml-section">
                    <div class="ml-section-head">
                        <div>
                            <h2 class="ml-section-title">Your own data</h2>
                            <p class="ml-muted">Your private files that passed the structure and quality checks. Only you can see them.</p>
                        </div>
                    </div>

                    <div class="ml-table-wrap">
                        <table class="ml-table">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Rows</th>
                                <th>Columns</th>
                                <th>Quality</th>
                                <th>Class</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($userDatasets as $dataset)
                                <tr>
                                    <td>{{ $dataset->name }}</td>
                                    <td>{{ number_format($dataset->row_count) }}</td>
                                    <td>{{ $dataset->column_count }}</td>
                                    <td>{{ number_format((float) $dataset->quality_score, 1) }}/100</td>
                                    <td>{{ $dataset->class_id ? 'Class linked' : 'Private' }}</td>
                                    <td>
                                        <div class="ml-actions">
                                            <a class="ml-btn small secondary" href="{{ route('student.model-development.user-datasets.show', $dataset) }}">Look inside</a>
                                            <a class="ml-btn small" href="{{ route('student.model-development.wizard', ['dataset_type' => 'user', 'dataset_id' => $dataset->id]) }}">Use</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="ml-muted">No uploaded datasets yet. Use the upload option below.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <details class="ml-advanced ml-section" @if($errors->has('dataset_file')) open @endif>
                    <summary>Upload your own data</summary>
                    <p class="ml-help">Use this when your data is not listed above. DataSensei checks the file, cleans unsafe content, and stores it privately.</p>
                    <ul class="ml-readiness">
                        <li><i class="ok">1</i><span>Save it as <strong>CSV</strong> or <strong>XLSX</strong> with a single header row at the top.</span></li>
                        <li><i class="ok">2</i><span>One record per row and at least <strong>20 rows</strong>. Remove totals, notes, and merged cells.</span></li>
                        <li><i class="ok">3</i><span>Include the column you want to predict (or none, if you only want to find groups).</span></li>
                    </ul>

                    <form method="POST" action="{{ route('student.model-development.datasets.upload') }}" enctype="multipart/form-data" style="margin-top:16px">
                        @csrf
                        <div class="ml-grid two">
                            <div class="ml-field">
                                <label class="ml-label" for="dataset_file">Dataset file</label>
                                <input class="ml-file" id="dataset_file" name="dataset_file" type="file" accept=".csv,.xlsx,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                                <div class="ml-help">Maximum {{ number_format($maxUploadKilobytes / 1024, 1) }} MB. At least 20 rows.</div>
                            </div>
                            <div class="ml-field">
                                <label class="ml-label" for="name">Dataset name</label>
                                <input class="ml-input" id="name" name="name" maxlength="160" value="{{ old('name') }}" placeholder="Example: Customer Churn Experiment">
                            </div>
                            <div class="ml-field">
                                <label class="ml-label" for="class_id">Class association</label>
                                <select class="ml-select" id="class_id" name="class_id">
                                    <option value="">Private, no class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" @selected(old('class_id') == $class->id)>{{ $class->name }}{{ $class->section ? ', '.$class->section : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button class="ml-btn" type="submit">Upload & Check Dataset</button>
                    </form>
                </details>

                <section class="ml-history-section" id="model-history">
                    <div class="ml-section-head">
                        <div>
                            <h2 class="ml-section-title">My models</h2>
                            <p class="ml-muted">Every model you train is saved here automatically. Training again never deletes an earlier one.</p>
                        </div>
                    </div>

                        <div class="ml-card">
                            <h3 class="ml-section-title">Saved models</h3>
                            <div class="ml-table-wrap" style="margin-top:16px">
                                <table class="ml-table">
                                    <thead><tr><th>Model</th><th>Learning method</th><th>Version</th><th></th></tr></thead>
                                    <tbody>
                                    @forelse($models as $model)
                                        <tr>
                                            <td>{{ $model->name }}</td>
                                            <td>{{ data_get($model->currentVersion?->explanations, 'training_summary.selected_label') ?: config('hybrid_ml.algorithms.'.$model->algorithm_key.'.label', str($model->algorithm_key)->replace('_', ' ')->title()) }}</td>
                                            <td>{{ $model->currentVersion?->version_label ?: 'Pending' }}</td>
                                            <td style="white-space:nowrap"><a class="ml-btn small secondary" href="{{ route('student.model-development.models.show', ['model' => $model, 'step' => 'results']) }}">Open</a>
                                                <a class="ml-btn small secondary" href="{{ route('student.model-development.models.show', ['model' => $model, 'step' => 'predict']) }}">Predict</a>
                                                @if(! $model->isSystemModel() && (int) $model->user_id === (int) auth()->id())
                                                    <form method="POST" action="{{ route('student.model-development.models.destroy', $model) }}" style="display:inline" onsubmit="return confirm('Delete “{{ addslashes($model->name) }}” and every saved version? This cannot be undone.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="ml-btn small danger" type="submit">Delete</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="ml-muted">No saved models yet. Pick a dataset above to train your first one.</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    <details class="ml-details" style="margin-top:16px">
                        <summary><span>Training runs<small>Every time you pressed Train, including runs that failed</small></span></summary>
                        <div class="ml-details-body">
                            <div class="ml-table-wrap">
                                <table class="ml-table">
                                    <thead><tr><th>Model</th><th>Status</th><th>Progress</th><th>Action</th></tr></thead>
                                    <tbody>
                                    @forelse($trainingJobs as $job)
                                        <tr>
                                            <td>{{ $job->model_name }}</td>
                                            <td><span class="ml-status-dot {{ $job->status }}"></span>{{ ucfirst($job->status) }}</td>
                                            <td>{{ $job->progress }}%</td>
                                            <td><a class="ml-btn small secondary" href="{{ route('student.model-development.training.show', $job) }}">View</a></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="ml-muted">No training jobs yet.</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </details>
                </section>
        </div>
    </main>
</div>
</body>
</html>
