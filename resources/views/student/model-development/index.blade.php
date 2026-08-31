<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Model Development Roadmap - DataSensei</title>
    @include('student.model-development.partials.styles')
</head>
<body>
<div class="ml-layout">
    @include('partials.sidebar')
    <main class="ml-main">
        <header class="ml-head">
            <div>
                <h1 class="ml-title ds-page-title">Model Development Roadmap</h1>
                <p class="ml-subtitle">Follow one clear step at a time to build, evaluate, test, and save a genuine machine-learning model.</p>
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
                    <span class="ml-step-kicker">Step 1 of 10</span>
                    <h2 class="ml-section-title">Choose Dataset</h2>
                    <p class="ml-muted">Choose the data you want to use. DataSensei will guide you to the target only after a dataset has been selected.</p>
                </section>

                <section class="ml-section">
                    <div class="ml-section-head">
                        <div>
                            <h2 class="ml-section-title">Built-in Dataset Library</h2>
                            <p class="ml-muted">Curated classroom datasets include a beginner-friendly recommended target, features, problem type, and model.</p>
                        </div>
                    </div>

                    <div class="ml-grid">
                        @forelse($systemDatasets as $dataset)
                            <article class="ml-card ml-dataset-choice">
                                <div class="ml-section-head">
                                    <h3 class="ml-section-title">{{ $dataset->name }}</h3>
                                    <span class="ml-badge">{{ ucfirst($dataset->problem_type) }}</span>
                                </div>
                                <p class="ml-muted" style="font-size:.8rem">{{ $dataset->description }}</p>
                                <div class="ml-meta">
                                    <div><span>Rows</span><strong>{{ number_format($dataset->row_count) }}</strong></div>
                                    <div><span>Columns</span><strong>{{ $dataset->column_count }}</strong></div>
                                    <div><span>Suggested target</span><strong>{{ $dataset->target_column ?: 'None' }}</strong></div>
                                    <div><span>Benchmarks</span><strong>{{ $dataset->benchmarks->count() }}</strong></div>
                                </div>
                                <div class="ml-actions">
                                    <a class="ml-btn secondary" href="{{ route('student.model-development.system-datasets.show', $dataset) }}">Preview</a>
                                    <a class="ml-btn" href="{{ route('student.model-development.wizard', ['dataset_type' => 'system', 'dataset_id' => $dataset->id, 'step' => 2]) }}">Choose & Continue to Target</a>
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
                            <p class="ml-muted">Private datasets that have already passed structure and quality checks.</p>
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
                    <summary>Upload a New Dataset</summary>
                    <p class="ml-help">Open this only when your dataset is not already listed. CSV and XLSX files are validated, sanitized, profiled, and stored privately.</p>

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
                                        <option value="{{ $class->id }}" @selected(old('class_id') == $class->id)>{{ $class->name }}{{ $class->section ? ' · '.$class->section : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button class="ml-btn" type="submit">Validate Dataset</button>
                    </form>
                </details>

                <section class="ml-history-section" id="model-history">
                    <div class="ml-section-head">
                        <div>
                            <h2 class="ml-section-title">Model History</h2>
                            <p class="ml-muted">Changing a roadmap choice never deletes a previously trained model or version.</p>
                        </div>
                    </div>

                    <div class="ml-grid two">
                        <div class="ml-card">
                            <h3 class="ml-section-title">Saved Models</h3>
                            <div class="ml-table-wrap" style="margin-top:14px">
                                <table class="ml-table">
                                    <thead><tr><th>Model</th><th>Algorithm</th><th>Version</th><th>Action</th></tr></thead>
                                    <tbody>
                                    @forelse($models as $model)
                                        <tr>
                                            <td>{{ $model->name }}</td>
                                            <td>{{ str($model->algorithm_key)->replace('_', ' ')->title() }}</td>
                                            <td>{{ $model->currentVersion?->version_label ?: 'Pending' }}</td>
                                            <td><a class="ml-btn small secondary" href="{{ route('student.model-development.models.show', ['model' => $model, 'step' => 'evaluate']) }}">Open Roadmap</a></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="ml-muted">No saved models yet.</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="ml-card">
                            <h3 class="ml-section-title">Training History</h3>
                            <div class="ml-table-wrap" style="margin-top:14px">
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
