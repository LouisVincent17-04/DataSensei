<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Model Development Roadmap — DataSensei</title>
    @include('student.model-development.partials.styles')
    @include('partials.page-head', ['pageTitle' => 'Model Development Roadmap', 'pageDescription' => 'Build, evaluate, and save a real machine-learning model in ten guided steps.'])
</head>
<body>
<div class="ml-layout">
    @include('partials.sidebar')
    <main class="ml-main">
        <header class="ml-head">
            <div>
                <h1 class="ml-title ds-page-title">Model Development</h1>
                <p class="ml-subtitle">Build a real machine-learning model in ten short steps. No coding needed – DataSensei explains every choice along the way.</p>
            </div>
            <div class="ml-actions">
                <a class="ml-btn secondary" href="{{ route('student.data-toolkit.index') }}">Data Toolkit</a>
            </div>
        </header>

        @if(session('success'))
            <div class="ml-alert">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="ml-alert error">{{ $errors->first() }}</div>
        @endif

        <div class="ml-roadmap-layout">
            <aside class="ml-roadmap-column">
                @include('student.model-development.partials.roadmap', [
                    'roadmapCurrent' => 1,
                    'roadmapCompletedThrough' => 0,
                    'roadmapErrorStep' => $errors->any() ? 1 : null,
                ])
            </aside>

            <div class="ml-roadmap-content">
                <section class="ml-card">
                    @include('student.model-development.partials.step-header', [
                        'stepNumber' => 1,
                        'stepTitle' => 'Choose a dataset',
                        'stepLead' => 'Pick the data your model will learn from. You can preview any dataset before choosing it.',
                    ])

                    <div class="ml-phases" aria-label="How model development works">
                        @foreach(\App\Support\ModelDevelopmentGuide::phases() as $phase)
                            <div class="ml-phase">
                                <h3><b>{{ $loop->iteration }}</b>{{ $phase['title'] }}<small>{{ $phase['steps'] }}</small></h3>
                                <p>{{ $phase['text'] }}</p>
                            </div>
                        @endforeach
                    </div>

                    @include('student.model-development.partials.step-guide', ['guideStep' => 1])
                </section>

                @if($activeTrainingJob)
                    <section class="ml-card ml-starter ml-resume">
                        <div>
                            <h3>A model is still training: {{ $activeTrainingJob->model_name }}</h3>
                            <p class="ml-muted" style="font-size:.8125rem">{{ $activeTrainingJob->stage ?: 'Waiting for the machine-learning worker' }}, {{ $activeTrainingJob->progress }}% complete</p>
                        </div>
                        <a class="ml-btn" href="{{ route('student.model-development.training.show', $activeTrainingJob) }}">View Progress →</a>
                    </section>
                @elseif($starterDataset && $models->isEmpty())
                    <section class="ml-card ml-starter">
                        <div>
                            <h3>First time here? Start with {{ $starterDataset->name }}</h3>
                            <p class="ml-muted" style="font-size:.8125rem">It is small ({{ number_format($starterDataset->row_count) }} rows), already clean, and comes with a recommended target, features, and algorithm, so you can finish all ten steps in a few minutes.</p>
                        </div>
                        <a class="ml-btn" href="{{ route('student.model-development.wizard', ['dataset_type' => 'system', 'dataset_id' => $starterDataset->id, 'step' => 2]) }}">Start with this dataset →</a>
                    </section>
                @endif

                <section class="ml-section">
                    <div class="ml-section-head">
                        <div>
                            <h2 class="ml-section-title">Built-in Dataset Library</h2>
                            <p class="ml-muted">Ready-to-use classroom datasets. Each one suggests a target, features, and an algorithm you can accept with one click.</p>
                        </div>
                    </div>

                    <div class="ml-grid">
                        @forelse($systemDatasets as $dataset)
                            <article class="ml-card ml-dataset-choice">
                                @php $datasetSetup = (array) data_get($dataset->metadata, 'recommended_setup', []); @endphp
                                <div class="ml-section-head">
                                    <h3 class="ml-section-title">{{ $dataset->name }}</h3>
                                    <span class="ml-badge {{ $starterDataset && $starterDataset->id === $dataset->id ? 'good' : '' }}">{{ $starterDataset && $starterDataset->id === $dataset->id ? 'Start here' : ucfirst($dataset->problem_type) }}</span>
                                </div>
                                <p class="ml-muted" style="font-size:.8125rem">{{ $dataset->description }}</p>
                                <div class="ml-meta">
                                    <div><span>Rows</span><strong>{{ number_format($dataset->row_count) }}</strong></div>
                                    <div><span>Columns</span><strong>{{ $dataset->column_count }}</strong></div>
                                    <div><span>Suggested target</span><strong>{{ $dataset->target_column ?: 'None' }}</strong></div>
                                    <div><span>Problem type</span><strong>{{ ucfirst($dataset->problem_type) }}</strong></div>
                                </div>
                                @if(! empty($datasetSetup['algorithm_key']))
                                    <p class="ml-recommended-line">Suggested algorithm: <strong>{{ config('hybrid_ml.algorithms.'.$datasetSetup['algorithm_key'].'.label', str($datasetSetup['algorithm_key'])->replace('_', ' ')->title()) }}</strong>, {{ $dataset->benchmarks->count() }} reference models to compare with</p>
                                @endif
                                <div class="ml-actions">
                                    <a class="ml-btn secondary" href="{{ route('student.model-development.system-datasets.show', $dataset) }}">Preview</a>
                                    <a class="ml-btn" href="{{ route('student.model-development.wizard', ['dataset_type' => 'system', 'dataset_id' => $dataset->id, 'step' => 2]) }}">Use this dataset →</a>
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
                            <h2 class="ml-section-title">Your Uploaded Datasets</h2>
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
                                            <a class="ml-btn small secondary" href="{{ route('student.model-development.user-datasets.show', $dataset) }}">Preview</a>
                                            <a class="ml-btn small" href="{{ route('student.model-development.wizard', ['dataset_type' => 'user', 'dataset_id' => $dataset->id, 'step' => 2]) }}">Choose</a>
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
                    <summary>Upload your own dataset</summary>
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
                            <h2 class="ml-section-title">Model History</h2>
                            <p class="ml-muted">Every model you train is kept here. Changing a setup later never deletes an earlier model or version.</p>
                        </div>
                    </div>

                    <div class="ml-grid two">
                        <div class="ml-card">
                            <h3 class="ml-section-title">Saved Models</h3>
                            <div class="ml-table-wrap" style="margin-top:16px">
                                <table class="ml-table">
                                    <thead><tr><th>Model</th><th>Algorithm</th><th>Version</th><th>Action</th></tr></thead>
                                    <tbody>
                                    @forelse($models as $model)
                                        <tr>
                                            <td>{{ $model->name }}</td>
                                            <td>{{ str($model->algorithm_key)->replace('_', ' ')->title() }}</td>
                                            <td>{{ $model->currentVersion?->version_label ?: 'Pending' }}</td>
                                            <td><a class="ml-btn small secondary" href="{{ route('student.model-development.models.show', ['model' => $model, 'step' => 'evaluate']) }}">Open Results</a></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="ml-muted">No saved models yet. Pick a dataset above to train your first one.</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="ml-card">
                            <h3 class="ml-section-title">Training History</h3>
                            <div class="ml-table-wrap" style="margin-top:16px">
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
                    </div>
                </section>
            </div>
        </div>
    </main>
</div>
</body>
</html>
