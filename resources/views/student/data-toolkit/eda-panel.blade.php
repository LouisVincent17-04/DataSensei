@php
  $datasetKey = $overview['dataset']['key'];
  $steps = [
      1 => ['Objective', 'Define what you want to learn'],
      2 => ['Dataset Overview', 'The rows and columns you loaded'],
      3 => ['Missing Values', 'Fix blanks, duplicates, and typos'],
      4 => ['Outliers', 'Review unusual values'],
      5 => ['Summary Statistics', 'One variable at a time'],
      6 => ['Relationships', 'How variables move together'],
      7 => ['Features', 'Create a useful variable'],
      8 => ['Summary', 'Review what you discovered'],
  ];
  $help = fn (string $key, ?string $text = null) => \App\Support\Glossary::help($key, $text);
  $eda = $overview['eda'];
  $source = $overview['dataset']['source'] ?? 'predefined';
@endphp

<nav class="roadmap" aria-label="EDA progress">
  @foreach($steps as $number => [$label, $description])
    @php $state = $number < $step ? 'completed' : ($number === $step ? 'current' : 'upcoming'); @endphp
    <div class="roadmap-item {{ $state }}" @if($number === $step) aria-current="step" @endif>
      @if($number < $step)
        <a href="{{ route('student.data-toolkit.show', ['dataset' => $datasetKey, 'step' => $number]) }}">
          <span class="number">✓</span><strong>{{ $label }}</strong>
        </a>
      @else
        <span class="number">{{ $number }}</span><strong>{{ $label }}</strong>
      @endif
    </div>
  @endforeach
</nav>

@if(session('success'))
  <div class="flash">{{ session('success') }}</div>
@endif
@if($errors->any())
  <div class="flash error">{{ $errors->first() }}</div>
@endif

<section class="step-card">
  <header class="step-head">
    <div class="step-number">{{ $step }}</div>
    <div>
      <h2>{{ $steps[$step][0] }}</h2>
      <p>{{ $steps[$step][1] }}</p>
    </div>
  </header>

  @if($step === 1)
    <div class="step-body">
      @if($source === 'uploaded_csv')
        <div class="callout">
          <h3>What do you want to learn from this dataset?</h3>
          <p>Write one short objective. You can use the suggested questions below as a guide.</p>
        </div>
        <form action="{{ route('student.data-toolkit.objective', $datasetKey) }}" method="POST">
          @csrf
          <div class="field">
            <label for="objective">Analysis objective</label>
            <textarea id="objective" name="objective" maxlength="600" placeholder="Example: Understand the factors connected with customer satisfaction." required>{{ old('objective', $overview['objective']) }}</textarea>
            @error('objective')<div class="error-text">{{ $message }}</div>@enderror
          </div>
          <div class="label" style="margin-top:20px">Suggested research questions</div>
          <ul class="questions">
            @foreach($overview['research_questions'] as $question)<li>{{ $question }}</li>@endforeach
          </ul>
          <div class="step-nav">
            <a class="btn secondary" href="{{ route('student.data-toolkit.index') }}">← Previous</a>
            <button class="btn" type="submit">Save objective and continue →</button>
          </div>
        </form>
      @else
        <div class="callout good">
          <div class="label">Prepared objective</div>
          <p class="objective">{{ $overview['objective'] }}</p>
        </div>
        <div class="label" style="margin-top:20px">Research questions to guide you</div>
        <ul class="questions">
          @foreach($overview['research_questions'] as $question)<li>{{ $question }}</li>@endforeach
        </ul>
      @endif
    </div>
    @if($source !== 'uploaded_csv')
      <footer class="step-nav">
        <a class="btn secondary" href="{{ route('student.data-toolkit.index') }}">← Previous</a>
        <a class="btn" href="{{ route('student.data-toolkit.show', ['dataset' => $datasetKey, 'step' => 2]) }}">Continue to data →</a>
      </footer>
    @endif
  @endif

  @if($step === 2)
    <div class="step-body">
      <div class="callout good">
        <h3>Dataset loaded</h3>
        <p>{{ $overview['dataset']['description'] }}</p>
      </div>
      <div class="stats">
        <div class="stat"><strong>{{ number_format($overview['row_count']) }}</strong><span>Rows</span></div>
        <div class="stat"><strong>{{ number_format($overview['column_count']) }}</strong><span>Columns</span></div>
        <div class="stat"><strong>{{ number_format(count($overview['numeric_columns'])) }}</strong><span>{!! $help('numeric_column', 'Numeric columns') !!}</span></div>
        <div class="stat"><strong>{{ number_format(count($overview['categorical_columns'])) }}</strong><span>{!! $help('categorical_column', 'Categorical / other columns') !!}</span></div>
      </div>
      <div class="actions" style="margin-bottom:16px">
        <button class="btn secondary dataset-modal-trigger" type="button" data-dataset-modal-url="{{ route('student.data-toolkit.rows', $datasetKey) }}" data-dataset-title="{{ $overview['dataset']['title'] }}">Show Dataset</button>
      </div>
      <div class="label">First {{ min(5, count($overview['preview'])) }} rows</div>
      <div class="table-wrap">
        <table>
          <thead><tr>@foreach($overview['columns'] as $column)<th>{{ $column }}</th>@endforeach</tr></thead>
          <tbody>
            @foreach(array_slice($overview['preview'], 0, 5) as $row)
              <tr>@foreach($overview['columns'] as $column)<td>{{ ($row[$column] ?? null) === null ? 'Missing' : $row[$column] }}</td>@endforeach</tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="label" style="margin-top:20px">Columns</div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Column</th><th>Detected type</th><th>Unique values</th><th>{!! $help('missing_value', 'Missing') !!}</th><th>Example</th></tr></thead>
          <tbody>
            @foreach($eda['data_types'] as $profile)
              <tr><td>{{ $profile['column'] }}</td><td>{{ $profile['type'] }}</td><td>{{ number_format($profile['unique_count']) }}</td><td>{{ number_format($profile['missing_count']) }}</td><td>{{ $profile['example'] ?? 'N/A' }}</td></tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <footer class="step-nav">
      <a class="btn secondary" href="{{ route('student.data-toolkit.show', ['dataset' => $datasetKey, 'step' => 1]) }}">← Previous</a>
      <a class="btn" href="{{ route('student.data-toolkit.show', ['dataset' => $datasetKey, 'step' => 3]) }}">Continue to cleaning →</a>
    </footer>
  @endif

  @if($step === 3)
    @php
      $missingCount = (int) $eda['missing']['total_missing'];
      $duplicateCount = (int) $eda['duplicates']['duplicate_records'];
      $typeIssueCount = (int) array_sum(array_column($eda['type_issues'], 'invalid_count'));
      $invalidCount = max($typeIssueCount, (int) ($eda['invalid_values']['total_invalid'] ?? 0));
      $categoryIssueCount = (int) ($eda['category_issues']['total_inconsistent'] ?? 0);
      $needsCleaning = $missingCount > 0 || $duplicateCount > 0 || $invalidCount > 0 || $categoryIssueCount > 0;
      $cleaningHistory = $overview['cleaning_history'];
      $lastCleaning = $cleaningHistory !== [] ? $cleaningHistory[array_key_last($cleaningHistory)] : null;
      $originalSnapshot = $overview['original_snapshot'];
      $currentSnapshot = $overview['current_snapshot'];
    @endphp
    <div class="step-body">
      @if($cleaningHistory !== [])
        <div class="label">Before and after cleaning</div>
        <div class="stats compare">
          <div class="stat">
            <strong>Original dataset</strong>
            <span>{{ number_format($originalSnapshot['rows']) }} rows, {{ number_format($originalSnapshot['missing_values']) }} missing, {{ number_format($originalSnapshot['duplicate_rows']) }} duplicates, {{ number_format($originalSnapshot['inconsistent_categories']) }} category issues, {{ number_format($originalSnapshot['invalid_values']) }} invalid</span>
          </div>
          <div class="stat">
            <strong>Current working data</strong>
            <span>{{ number_format($currentSnapshot['rows']) }} rows, {{ number_format($currentSnapshot['missing_values']) }} missing, {{ number_format($currentSnapshot['duplicate_rows']) }} duplicates, {{ number_format($currentSnapshot['inconsistent_categories']) }} category issues, {{ number_format($currentSnapshot['invalid_values']) }} invalid</span>
          </div>
        </div>
        @if($lastCleaning)
          <div class="callout good" style="margin-bottom:16px">
            <h3>Last cleaning result</h3>
            <p>{{ number_format($lastCleaning['duplicates_removed']) }} duplicate row(s) removed, {{ number_format($lastCleaning['missing_values_filled']) }} missing value(s) filled, {{ number_format($lastCleaning['categories_standardized']) }} category value(s) standardized, {{ number_format($lastCleaning['invalid_values_corrected']) }} invalid value(s) replaced, and {{ number_format($lastCleaning['invalid_rows_removed']) }} invalid row(s) removed.</p>
          </div>
        @endif
      @endif

      <div class="checks">
        <div class="check {{ $missingCount > 0 ? 'issue' : '' }}"><span class="check-icon">{{ $missingCount > 0 ? '!' : '✓' }}</span><div><strong>{{ $missingCount > 0 ? number_format($missingCount).' missing value(s) found' : 'No missing values found' }}</strong><p>{{ $missingCount > 0 ? 'Missing numeric values can be filled with the median, while category values can use the most common category.' : 'Every current cell contains a value.' }}</p></div></div>
        <div class="check {{ $duplicateCount > 0 ? 'issue' : '' }}"><span class="check-icon">{{ $duplicateCount > 0 ? '!' : '✓' }}</span><div><strong>{{ $duplicateCount > 0 ? number_format($duplicateCount).' duplicate row(s) found' : 'No duplicate rows found' }}</strong><p>{{ $duplicateCount > 0 ? 'Removing exact duplicates prevents repeated records from affecting counts and averages.' : 'No row exactly repeats another row.' }}</p></div></div>
        <div class="check {{ $categoryIssueCount > 0 ? 'issue' : '' }}"><span class="check-icon">{{ $categoryIssueCount > 0 ? '!' : '✓' }}</span><div><strong>{{ $categoryIssueCount > 0 ? number_format($categoryIssueCount).' inconsistent category label(s) found' : 'Category labels are consistent' }}</strong><p>{{ $categoryIssueCount > 0 ? 'These labels differ only by capitalization or extra spacing and can be merged into one category.' : 'No capitalization or spacing variants split the current categories.' }}</p></div></div>
        <div class="check {{ $invalidCount > 0 ? 'issue' : '' }}"><span class="check-icon">{{ $invalidCount > 0 ? '!' : '✓' }}</span><div><strong>{{ $invalidCount > 0 ? number_format($invalidCount).' invalid or type-conflicting value(s) found' : 'Values match their expected types and ranges' }}</strong><p>{{ $invalidCount > 0 ? 'These entries are not numeric where expected or fall outside a reasonable range for this dataset.' : 'No obvious type or range conflicts were found.' }}</p></div></div>
        @if(($overview['dataset']['upload_metadata']['empty_rows_skipped'] ?? 0) > 0)
          <div class="check"><span class="check-icon">✓</span><div><strong>{{ number_format($overview['dataset']['upload_metadata']['empty_rows_skipped']) }} empty row(s) already skipped</strong><p>Completely empty CSV rows were ignored while the file was loaded.</p></div></div>
        @endif
      </div>

      @if($needsCleaning)
        <form action="{{ route('student.data-toolkit.clean', $datasetKey) }}" method="POST" style="margin-top:20px">
          @csrf
          <div class="label">Choose how to handle each issue</div>
          <div class="feature-grid" style="margin-top:12px">
            @if($missingCount > 0)
              <div class="feature-option" style="display:block">
                <strong>Missing values</strong>
                <label style="display:block;margin-top:8px"><input type="radio" name="missing_action" value="fill" {{ old('missing_action', 'fill') === 'fill' ? 'checked' : '' }}> Fill with the {!! $help('median', 'median') !!} / most common category (recommended)</label>
                <label style="display:block;margin-top:8px"><input type="radio" name="missing_action" value="keep" {{ old('missing_action') === 'keep' ? 'checked' : '' }}> Keep missing values</label>
              </div>
            @else
              <input type="hidden" name="missing_action" value="keep">
            @endif

            @if($duplicateCount > 0)
              <div class="feature-option" style="display:block">
                <strong>Exact duplicates</strong>
                <label style="display:block;margin-top:8px"><input type="radio" name="duplicates_action" value="remove" {{ old('duplicates_action', 'remove') === 'remove' ? 'checked' : '' }}> Remove repeated rows (recommended)</label>
                <label style="display:block;margin-top:8px"><input type="radio" name="duplicates_action" value="keep" {{ old('duplicates_action') === 'keep' ? 'checked' : '' }}> Keep repeated rows</label>
              </div>
            @else
              <input type="hidden" name="duplicates_action" value="keep">
            @endif

            @if($categoryIssueCount > 0)
              <div class="feature-option" style="display:block">
                <strong>Category labels</strong>
                <label style="display:block;margin-top:8px"><input type="radio" name="categories_action" value="standardize" {{ old('categories_action', 'standardize') === 'standardize' ? 'checked' : '' }}> Standardize capitalization and spacing (recommended)</label>
                <label style="display:block;margin-top:8px"><input type="radio" name="categories_action" value="keep" {{ old('categories_action') === 'keep' ? 'checked' : '' }}> Keep labels as entered</label>
                @foreach(array_slice($eda['category_issues']['columns'], 0, 2, true) as $categoryIssue)
                  @foreach(array_slice($categoryIssue['groups'], 0, 1) as $group)
                    <p style="margin-top:8px">Example in {{ $categoryIssue['column'] }}: @foreach($group['variants'] as $variant)<code>“{{ $variant['value'] }}”</code>@if(!$loop->last), @endif @endforeach → <code>“{{ $group['canonical'] }}”</code></p>
                  @endforeach
                @endforeach
              </div>
            @else
              <input type="hidden" name="categories_action" value="keep">
            @endif

            @if($invalidCount > 0)
              <div class="feature-option" style="display:block">
                <strong>Invalid or type-conflicting values</strong>
                <label style="display:block;margin-top:8px"><input type="radio" name="invalid_action" value="replace" {{ old('invalid_action', 'replace') === 'replace' ? 'checked' : '' }}> Replace with the valid median (recommended)</label>
                <label style="display:block;margin-top:8px"><input type="radio" name="invalid_action" value="remove" {{ old('invalid_action') === 'remove' ? 'checked' : '' }}> Remove affected rows</label>
                <label style="display:block;margin-top:8px"><input type="radio" name="invalid_action" value="keep" {{ old('invalid_action') === 'keep' ? 'checked' : '' }}> Keep for investigation</label>
                @foreach(array_slice($eda['invalid_values']['by_column'] ?? [], 0, 2, true) as $invalidIssue)
                  @if(isset($invalidIssue['sample_values'][0]))
                    <p style="margin-top:8px">Example: row {{ $invalidIssue['sample_values'][0]['row_number'] }}, {{ $invalidIssue['column'] }} = <code>{{ $invalidIssue['sample_values'][0]['value'] }}</code> ({{ $invalidIssue['sample_values'][0]['reason'] }}).</p>
                  @endif
                @endforeach
              </div>
            @else
              <input type="hidden" name="invalid_action" value="keep">
            @endif
          </div>
          <div class="recommendation"><strong>Your original source remains unchanged.</strong> Later steps use this cleaned working copy, so charts and findings will reflect the choices you make here.</div>
          <button class="btn" style="margin-top:16px" type="submit">Apply selected cleaning actions</button>
        </form>
      @else
        <div class="callout good" style="margin-top:16px"><h3>Your data is ready</h3><p>No current missing values, duplicates, category variants, or invalid values need a cleaning decision. You can continue to outlier detection.</p></div>
      @endif
    </div>
    <footer class="step-nav">
      <a class="btn secondary" href="{{ route('student.data-toolkit.show', ['dataset' => $datasetKey, 'step' => 2]) }}">← Previous</a>
      <a class="btn {{ $needsCleaning ? 'secondary' : '' }}" href="{{ route('student.data-toolkit.show', ['dataset' => $datasetKey, 'step' => 4]) }}">{{ $needsCleaning ? 'Continue without more cleaning' : 'Continue' }} →</a>
    </footer>
  @endif

  @if($step === 4)
    @php
      $out = $eda['outliers'];
      $columnsWithOutliers = array_filter($out['columns'], fn ($item) => $item['outlier_count'] > 0);
      $shownSamples = collect($columnsWithOutliers)->sum(fn ($item) => count($item['sample_outliers']));
      $reason = fn (string $direction) => $direction === 'high' ? 'Much higher than most values' : 'Much lower than most values';
    @endphp
    <div class="step-body">
      <div class="callout warn">
        <h3>{!! $help('outlier', 'Outliers') !!} are not automatically errors</h3>
        <p>An unusual value may be a valid observation, a data-entry mistake, or a rare real-world case. Review the result before removing anything.</p>
      </div>
      @if($overview['numeric_columns'] === [])
        <div class="empty" style="margin-top:16px">This dataset has no usable {!! $help('numeric_column', 'numeric columns') !!}, so an outlier check is not applicable.</div>
      @elseif($columnsWithOutliers === [])
        <div class="callout good" style="margin-top:16px"><h3>✓ Outliers found: 0</h3><p>The {!! $help('iqr', 'IQR check') !!} found no values that are unusually far from the rest.</p></div>
      @else
        <p class="o-count">Outliers found: <strong>{{ number_format($out['total_outliers']) }}</strong></p>
        <p class="o-note">
          {{ number_format($out['rows_with_outliers']) }} of {{ number_format($overview['row_count']) }} rows
          ({{ $out['rows_percent'] }}%) hold at least one unusual value, in
          {{ $out['columns_with_outliers'] }} {{ $out['columns_with_outliers'] === 1 ? 'column' : 'columns' }}:
          @foreach($columnsWithOutliers as $column => $outlier){{ $column }} ({{ number_format($outlier['outlier_count']) }}, {{ $outlier['outlier_percent'] }}% of its values)@if(!$loop->last), @endif @endforeach
        </p>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Column</th><th>Row</th><th>Unusual value</th><th>Reason</th></tr></thead>
            <tbody>
              @foreach($columnsWithOutliers as $column => $outlier)
                @foreach($outlier['sample_outliers'] as $sample)
                  <tr>
                    <td>{{ $column }}</td>
                    <td>{{ number_format($sample['row_number']) }}</td>
                    <td><strong>{{ $sample['value'] }}</strong></td>
                    <td>{{ $reason($sample['direction'] ?? 'high') }}</td>
                  </tr>
                @endforeach
              @endforeach
            </tbody>
          </table>
        </div>
        @if($out['total_outliers'] > $shownSamples)
          <p class="o-columns">Showing the first {{ number_format($shownSamples) }} of {{ number_format($out['total_outliers']) }} flagged values, up to 12 per column.</p>
        @endif
        <p class="o-columns">A value is flagged when it falls unusually far below or above the middle half of its column ({!! $help('iqr', 'the IQR method') !!}).</p>

        <details class="advanced">
          <summary>Advanced view: box plots</summary>
          <div class="advanced-body">
            <p>A {!! $help('box_plot', 'box plot') !!} shows the same check visually: the box covers the middle half of the values ({!! $help('quartile', 'Q1 to Q3') !!}), the line inside it is the {!! $help('median', 'median') !!}, and each dot is a flagged value.</p>
            @foreach($columnsWithOutliers as $column => $outlier)
              @php
                $bp = $outlier['box_plot'];
                $lo = (float) ($bp['minimum'] ?? 0);
                $hi = (float) ($bp['maximum'] ?? 1);
                $span = max($hi - $lo, 1e-9);
                $pos = fn ($value) => round((((float) $value) - $lo) / $span * 100, 2);
              @endphp
              <div class="bp">
                <p class="bp-title">{{ $column }}</p>
                <div class="bp-lane" role="img" aria-label="Box plot for {{ $column }}: median {{ $bp['median'] }}, box from {{ $bp['q1'] }} to {{ $bp['q3'] }}, {{ number_format($outlier['outlier_count']) }} flagged value(s)">
                  <span class="bp-whisker" style="left:{{ $pos($bp['whisker_minimum']) }}%;width:{{ max(0, $pos($bp['q1']) - $pos($bp['whisker_minimum'])) }}%"></span>
                  <span class="bp-whisker" style="left:{{ $pos($bp['q3']) }}%;width:{{ max(0, $pos($bp['whisker_maximum']) - $pos($bp['q3'])) }}%"></span>
                  <span class="bp-box" style="left:{{ $pos($bp['q1']) }}%;width:{{ max(0.4, $pos($bp['q3']) - $pos($bp['q1'])) }}%"></span>
                  <span class="bp-median" style="left:{{ $pos($bp['median']) }}%"></span>
                  @foreach($bp['outliers'] as $value)
                    <span class="bp-dot" style="left:{{ $pos($value) }}%" title="{{ $value }}"></span>
                  @endforeach
                </div>
                <div class="bp-scale"><span>{{ $bp['minimum'] }}</span><span>{{ $bp['maximum'] }}</span></div>
              </div>
            @endforeach
          </div>
        </details>

        <form action="{{ route('student.data-toolkit.outliers', $datasetKey) }}" method="POST" style="margin-top:16px">
          @csrf
          <div class="label">Remove only if the values are not useful for your objective</div>
          <div class="checkbox-list">
            @foreach($columnsWithOutliers as $column => $outlier)
              <label><input type="checkbox" name="columns[]" value="{{ $column }}"> {{ $column }}, {{ number_format($outlier['outlier_count']) }} flagged value(s)</label>
            @endforeach
          </div>
          @error('columns')<div class="error-text">{{ $message }}</div>@enderror
          <div class="actions" style="margin-top:16px">
            <button class="btn danger" name="action" value="remove" type="submit">Remove selected outliers</button>
          </div>
        </form>
      @endif
    </div>
    <footer class="step-nav">
      <a class="btn secondary" href="{{ route('student.data-toolkit.show', ['dataset' => $datasetKey, 'step' => 3]) }}">← Previous</a>
      <form action="{{ route('student.data-toolkit.outliers', $datasetKey) }}" method="POST">@csrf<input type="hidden" name="action" value="keep"><button class="btn" type="submit">{{ $columnsWithOutliers === [] ? 'Continue' : 'Keep outliers' }} →</button></form>
    </footer>
  @endif

  @if($step === 5)
    <div class="step-body">
      <div class="callout"><h3>What did we find?</h3><p>DataSensei looked at each useful variable on its own ({!! $help('univariate', 'univariate') !!} analysis) and picked the right summary and chart for it.</p></div>
      @if($displayNumeric === [] && $displayCategorical === [])
        <div class="empty" style="margin-top:16px">There are no suitable variables for a basic distribution chart.</div>
      @else
        <div class="variable-grid" style="margin-top:16px">
          @foreach($displayNumeric as $index => $column)
            @php $summary = $overview['descriptive'][$column]; @endphp
            <article class="variable-card">
              <div class="label">Numerical variable</div><h3>{{ $column }}</h3><p>{{ $summary['interpretation'] }}</p>
              <div class="label" style="margin-top:12px">Summary statistics</div>
              <div class="metrics">
                <div class="metric"><strong>{{ $summary['mean'] ?? 'N/A' }}</strong><span>{!! $help('mean') !!}</span></div>
                <div class="metric"><strong>{{ $summary['median'] ?? 'N/A' }}</strong><span>{!! $help('median') !!}</span></div>
                <div class="metric"><strong>{{ $summary['minimum'] ?? 'N/A' }}</strong><span>Minimum</span></div>
                <div class="metric"><strong>{{ $summary['maximum'] ?? 'N/A' }}</strong><span>Maximum</span></div>
                <div class="metric"><strong>{{ $summary['standard_deviation'] ?? 'N/A' }}</strong><span>{!! $help('standard_deviation', 'Std. deviation') !!}</span></div>
              </div>
              <div class="label" style="margin-top:12px">{!! $help('distribution', 'Distribution') !!}</div>
              <div class="chart-wrap"><canvas id="numericChart{{ $index }}"></canvas></div>
            </article>
          @endforeach
          @foreach($displayCategorical as $index => $column)
            @php $summary = $overview['categorical'][$column]; @endphp
            <article class="variable-card">
              <div class="label">Categorical variable</div><h3>{{ $column }}</h3><p>{{ $summary['interpretation'] }}</p>
              <div class="metrics" style="grid-template-columns:repeat(3,minmax(0,1fr))">
                <div class="metric"><strong>{{ $summary['unique_count'] }}</strong><span>Categories</span></div>
                <div class="metric"><strong>{{ $summary['most_frequent'] ?? 'N/A' }}</strong><span>Most common</span></div>
                <div class="metric"><strong>{{ $summary['missing_count'] }}</strong><span>Missing</span></div>
              </div>
              <div class="chart-wrap"><canvas id="categoryChart{{ $index }}"></canvas></div>
            </article>
          @endforeach
        </div>
      @endif
      <div class="recommendation"><strong>Meaning:</strong> A {!! $help('distribution', 'distribution') !!} shows what is typical, how values vary, and whether one category dominates. <strong>Next:</strong> Compare variables to look for useful relationships.</div>
    </div>
    <footer class="step-nav"><a class="btn secondary" href="{{ route('student.data-toolkit.show', ['dataset' => $datasetKey, 'step' => 4]) }}">← Previous</a><a class="btn" href="{{ route('student.data-toolkit.show', ['dataset' => $datasetKey, 'step' => 6]) }}">Continue to relationships →</a></footer>
  @endif

  @if($step === 6)
    <div class="step-body">
      @if($overview['relationships'] === [])
        <div class="empty">At least two changing numerical variables are needed for a meaningful correlation. The roadmap can still continue.</div>
      @else
        <div class="relationship-grid">
          @foreach(array_slice($overview['relationships'], 0, 4) as $relationship)
            <article class="relationship-card">
              <div class="label">{{ ucfirst($relationship['strength']) }} {{ $relationship['direction'] }} {!! $help('correlation', 'correlation') !!}</div>
              <h3>{{ $relationship['x'] }} and {{ $relationship['y'] }}</h3>
              <div class="relationship-value">{!! $help('correlation_coefficient', 'r') !!} = {{ $relationship['value'] }}</div>
              <p>{{ $relationship['finding'] }}</p>
            </article>
          @endforeach
        </div>
        @if($overview['regression'])
          <div class="relationship-card" style="margin-top:16px"><h3>{{ $overview['regression']['x_column'] }} compared with {{ $overview['regression']['y_column'] }}</h3><p>{{ $overview['regression']['interpretation'] }}</p><div class="chart-wrap" style="height:280px"><canvas id="relationshipScatter"></canvas></div></div>
        @endif
      @endif
      @if($overview['group_comparison'])
        <div class="relationship-card" style="margin-top:16px"><h3>Group comparison</h3><p>{{ $overview['group_comparison']['finding'] }}</p><div class="chart-wrap" style="height:260px"><canvas id="groupComparisonChart"></canvas></div></div>
      @endif
      <div class="callout warn" style="margin-top:16px"><h3>{!! $help('correlation', 'Correlation') !!} does not prove {!! $help('causation', 'causation') !!}</h3><p>Two variables can move together without one directly causing the other. Other variables or real-world conditions may explain the pattern.</p></div>
      <div class="recommendation"><strong>Next:</strong> Use what you observed to consider a simple new feature.</div>
    </div>
    <footer class="step-nav"><a class="btn secondary" href="{{ route('student.data-toolkit.show', ['dataset' => $datasetKey, 'step' => 5]) }}">← Previous</a><a class="btn" href="{{ route('student.data-toolkit.show', ['dataset' => $datasetKey, 'step' => 7]) }}">Continue to features →</a></footer>
  @endif

  @if($step === 7)
    <div class="step-body">
      <div class="callout"><h3>Feature engineering, kept simple</h3><p>A feature is a useful new variable made from existing variables. DataSensei only suggests transformations that match recognizable columns in this dataset.</p></div>
      @if($overview['feature_suggestions'] === [])
        <div class="empty" style="margin-top:16px">No safe, obvious feature transformation was detected. Skipping is the right choice when a new variable would not have a clear meaning.</div>
      @else
        <form action="{{ route('student.data-toolkit.feature', $datasetKey) }}" method="POST" style="margin-top:16px">
          @csrf
          <input type="hidden" name="action" value="accept">
          <div class="feature-grid">
            @foreach($overview['feature_suggestions'] as $index => $suggestion)
              <label class="feature-option"><input type="radio" name="suggestion_key" value="{{ $suggestion['key'] }}" {{ old('suggestion_key', $index === 0 ? $suggestion['key'] : '') === $suggestion['key'] ? 'checked' : '' }}><span><strong>{{ $suggestion['name'] }}</strong><span class="formula">{{ $suggestion['formula'] }}</span><p>{{ $suggestion['explanation'] }}</p></span></label>
            @endforeach
          </div>
          @error('suggestion_key')<div class="error-text">{{ $message }}</div>@enderror
          <button class="btn" style="margin-top:16px" type="submit">Accept suggestion →</button>
        </form>
      @endif
    </div>
    <footer class="step-nav"><a class="btn secondary" href="{{ route('student.data-toolkit.show', ['dataset' => $datasetKey, 'step' => 6]) }}">← Previous</a><a class="btn {{ $overview['feature_suggestions'] === [] ? '' : 'secondary' }}" href="{{ route('student.data-toolkit.show', ['dataset' => $datasetKey, 'step' => 8]) }}">Skip to summary →</a></footer>
  @endif

  @if($step === 8)
    <div class="step-body">
      <p class="done-note">✓ {!! $help('eda', 'EDA') !!} complete</p>
      <h2 class="summary-title">You followed the full exploratory data analysis process.</h2>
      <p class="small" style="font-size:.875rem;line-height:1.6;margin:0">Your current working dataset contains {{ number_format($overview['row_count']) }} records and {{ number_format($overview['column_count']) }} variables.</p>
      <div class="label" style="margin-top:20px">Original dataset compared with your final working data</div>
      <div class="stats compare">
        <div class="stat">
          <strong>Original, {{ number_format($overview['original_snapshot']['rows']) }} rows</strong>
          <span>{{ number_format($overview['original_snapshot']['missing_values']) }} missing, {{ number_format($overview['original_snapshot']['duplicate_rows']) }} duplicates, {{ number_format($overview['original_snapshot']['inconsistent_categories']) }} category issues, {{ number_format($overview['original_snapshot']['invalid_values']) }} invalid, {{ number_format($overview['original_snapshot']['potential_outliers']) }} potential outliers</span>
        </div>
        <div class="stat">
          <strong>Final, {{ number_format($overview['current_snapshot']['rows']) }} rows</strong>
          <span>{{ number_format($overview['current_snapshot']['missing_values']) }} missing, {{ number_format($overview['current_snapshot']['duplicate_rows']) }} duplicates, {{ number_format($overview['current_snapshot']['inconsistent_categories']) }} category issues, {{ number_format($overview['current_snapshot']['invalid_values']) }} invalid, {{ number_format($overview['current_snapshot']['potential_outliers']) }} potential outliers</span>
        </div>
      </div>
      <div class="label" style="margin-top:20px">Key findings</div>
      <ul class="summary-list">@foreach($overview['key_findings'] as $finding)<li>{{ $finding }}</li>@endforeach</ul>

      @if($overview['eda_actions'] !== [])
        <div class="label" style="margin-top:20px">Actions you applied</div>
        <ul class="summary-list">@foreach($overview['eda_actions'] as $action)<li>{{ $action['label'] }}</li>@endforeach</ul>
      @endif

      @if($overview['created_features'] !== [])
        <div class="created callout good"><h3>Created features</h3>@foreach($overview['created_features'] as $feature)<p><strong>{{ $feature['name'] }}</strong>, {{ $feature['formula'] }}</p>@endforeach</div>
      @endif

      <div class="recommendation"><strong>What this means:</strong> EDA helps you understand data quality and patterns before making conclusions or building a machine-learning model. Findings should still be checked using subject knowledge.</div>
    </div>
    <footer class="step-nav"><a class="btn secondary" href="{{ route('student.data-toolkit.show', ['dataset' => $datasetKey, 'step' => 7]) }}">← Previous</a><a class="btn" href="{{ route('student.data-toolkit.index') }}">Analyze another dataset</a></footer>
  @endif
</section>
