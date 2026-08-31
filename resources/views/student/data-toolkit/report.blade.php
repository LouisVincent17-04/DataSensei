<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EDA Report — {{ $report['dataset']['title'] }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
    :root{--bg:#f5f7fb;--surface:#ffffff;--border:#d8e0ed;--text:#111827;--muted:#52637a;--accent:#2563eb;--good:#047857;--radius:14px}*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--text);font-family:Inter,Arial,sans-serif}.page{max-width:1120px;margin:0 auto;padding:28px}.toolbar{display:flex;justify-content:space-between;gap:12px;margin-bottom:18px}.btn{display:inline-flex;align-items:center;justify-content:center;border:1px solid var(--border);background:var(--surface);color:var(--text);border-radius:10px;padding:10px 14px;text-decoration:none;font-weight:800;cursor:pointer}.btn.primary{background:var(--accent);border-color:var(--accent);color:white}.report{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:30px;box-shadow:0 20px 45px rgba(15,23,42,.08)}.header{border-bottom:2px solid var(--border);padding-bottom:18px;margin-bottom:22px}.eyebrow{font-size:.75rem;font-weight:900;text-transform:uppercase;letter-spacing:.12em;color:var(--accent)}h1{margin:6px 0 8px;font-size:2rem}.muted{color:var(--muted);line-height:1.65}.grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin:16px 0}.card{border:1px solid var(--border);border-radius:12px;padding:14px;background:#fbfdff}.label{font-size:.72rem;color:var(--muted);font-weight:900;text-transform:uppercase;letter-spacing:.08em}.metric{font-size:1.5rem;font-weight:900;margin-top:4px}.section{margin-top:26px}.section h2{font-size:1.25rem;margin:0 0 10px}.table-wrap{overflow:auto;border:1px solid var(--border);border-radius:12px}table{width:100%;border-collapse:collapse}th,td{padding:10px 12px;border-bottom:1px solid var(--border);text-align:left;font-size:.86rem}th{background:#f1f5f9;color:#475569;text-transform:uppercase;letter-spacing:.06em;font-size:.72rem}tr:last-child td{border-bottom:0}.two{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.note{border-left:4px solid var(--accent);background:#eef5ff;border-radius:10px;padding:12px;color:#334155;line-height:1.55}.chips{display:flex;gap:8px;flex-wrap:wrap}.chip{border:1px solid var(--border);background:#f8fafc;border-radius:999px;padding:6px 10px;font-size:.78rem;font-weight:800;color:#475569}@media(max-width:900px){.grid,.two{grid-template-columns:1fr}.toolbar{display:block}.toolbar .btn{margin-bottom:8px}}@media print{body{background:white}.page{max-width:none;padding:0}.toolbar{display:none}.report{box-shadow:none;border:0;border-radius:0}.section{break-inside:avoid}.card{break-inside:avoid}}
  </style>
  @include('partials.ui-polish')
</head>
<body>
<div class="page">
  <div class="toolbar">
    <a class="btn" href="{{ route('student.data-toolkit.show', $report['dataset']['key']) }}">Back to Dataset</a>
    <button class="btn primary" onclick="window.print()">Print Report</button>
  </div>

  <main class="report">
    <header class="header">
      <div class="eyebrow">DataSensei Exploratory Data Analysis Report</div>
      <h1>{{ $report['dataset']['title'] }}</h1>
      <p class="muted">{{ $report['dataset']['description'] }}</p>
      <div class="chips">
        <span class="chip">Student: {{ $student->name ?? 'Student' }}</span>
        <span class="chip">Generated: {{ $report['generated_at']->format('M d, Y h:i A') }}</span>
        <span class="chip">Dataset: Built-in</span>
      </div>
    </header>

    <section class="grid">
      <div class="card"><div class="label">Rows</div><div class="metric">{{ number_format($report['row_count']) }}</div></div>
      <div class="card"><div class="label">Columns</div><div class="metric">{{ number_format($report['column_count']) }}</div></div>
      <div class="card"><div class="label">Numeric Columns</div><div class="metric">{{ count($report['numeric_columns']) }}</div></div>
      <div class="card"><div class="label">Categorical Columns</div><div class="metric">{{ count($report['categorical_columns']) }}</div></div>
    </section>

    <section class="section">
      <h2>Learning Objective</h2>
      <p class="note">{{ $report['dataset']['learning_objective'] }}</p>
    </section>

    <section class="section">
      <h2>Dataset Preview</h2>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              @foreach($report['columns'] as $column)
                <th>{{ $column }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @foreach($report['preview'] as $row)
              <tr>
                @foreach($report['columns'] as $column)
                  <td>{{ $row[$column] ?? '' }}</td>
                @endforeach
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </section>

    <section class="section">
      <h2>EDA Data-Quality Summary</h2>
      <div class="grid">
        <div class="card"><div class="label">Quality Score</div><div class="metric">{{ number_format($report['eda']['quality_score'], 1) }}%</div></div>
        <div class="card"><div class="label">Missing Cells</div><div class="metric">{{ number_format($report['eda']['missing']['total_missing']) }}</div></div>
        <div class="card"><div class="label">Duplicate Records</div><div class="metric">{{ number_format($report['eda']['duplicates']['duplicate_records']) }}</div></div>
        <div class="card"><div class="label">Possible Outliers</div><div class="metric">{{ number_format($report['eda']['outliers']['total_outliers']) }}</div></div>
      </div>
      <p class="note">{{ $report['eda']['summary'] }}</p>
    </section>

    <section class="section">
      <h2>Column Profile</h2>
      <div class="table-wrap"><table><thead><tr><th>Column</th><th>Type</th><th>Non-missing</th><th>Missing</th><th>Unique</th></tr></thead><tbody>
      @foreach($report['eda']['data_types'] as $profile)
        <tr><td>{{ $profile['column'] }}</td><td>{{ $profile['type'] }}</td><td>{{ $profile['non_missing_count'] }}</td><td>{{ $profile['missing_count'] }} ({{ number_format($profile['missing_percent'], 2) }}%)</td><td>{{ $profile['unique_count'] }}</td></tr>
      @endforeach
      </tbody></table></div>
    </section>

    <section class="section">
      <h2>Outlier Findings</h2>
      <div class="table-wrap"><table><thead><tr><th>Column</th><th>Outliers</th><th>Q1</th><th>Median</th><th>Q3</th><th>IQR</th><th>Interpretation</th></tr></thead><tbody>
      @foreach($report['eda']['outliers']['columns'] as $outlier)
        <tr><td>{{ $outlier['column'] }}</td><td>{{ $outlier['outlier_count'] }} ({{ number_format($outlier['outlier_percent'], 2) }}%)</td><td>{{ $outlier['q1'] ?? 'N/A' }}</td><td>{{ $outlier['median'] ?? 'N/A' }}</td><td>{{ $outlier['q3'] ?? 'N/A' }}</td><td>{{ $outlier['iqr'] ?? 'N/A' }}</td><td>{{ $outlier['interpretation'] }}</td></tr>
      @endforeach
      </tbody></table></div>
    </section>

    <section class="section">
      <h2>Descriptive Statistics</h2>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Column</th><th>Count</th><th>Mean</th><th>Median</th><th>Std. Dev.</th><th>Min</th><th>Max</th><th>IQR</th>
            </tr>
          </thead>
          <tbody>
            @forelse($report['descriptive'] as $row)
              <tr>
                <td>{{ $row['column'] }}</td>
                <td>{{ $row['count'] }}</td>
                <td>{{ $row['mean'] }}</td>
                <td>{{ $row['median'] }}</td>
                <td>{{ $row['standard_deviation'] }}</td>
                <td>{{ $row['minimum'] }}</td>
                <td>{{ $row['maximum'] }}</td>
                <td>{{ $row['interquartile_range'] }}</td>
              </tr>
            @empty
              <tr><td colspan="8">No numeric columns are available.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>

    <section class="section two">
      <div>
        <h2>Categorical Summary</h2>
        @forelse($report['categorical'] as $summary)
          <div class="card" style="margin-bottom:10px">
            <div class="label">{{ $summary['column'] }}</div>
            <p><strong>{{ $summary['unique_count'] }}</strong> unique values · Most common: <strong>{{ $summary['most_frequent'] ?? 'N/A' }}</strong></p>
            <p class="muted">{{ $summary['interpretation'] }}</p>
          </div>
        @empty
          <p class="muted">No categorical columns are available.</p>
        @endforelse
      </div>

      <div>
        <h2>Correlation Findings</h2>
        <p class="note">{{ $report['correlation']['interpretation'] }}</p>
        @if($report['correlation']['strongest_pair'])
          <div class="card" style="margin-top:10px">
            <div class="label">Strongest Pair</div>
            <p><strong>{{ $report['correlation']['strongest_pair']['x'] }}</strong> and <strong>{{ $report['correlation']['strongest_pair']['y'] }}</strong></p>
            <p class="muted">r = {{ $report['correlation']['strongest_pair']['value'] }} · {{ $report['correlation']['strongest_pair']['strength'] }} {{ $report['correlation']['strongest_pair']['direction'] }}</p>
          </div>
        @endif
      </div>
    </section>

    <section class="section">
      <h2>Linear Regression</h2>
      @if($report['report_regression'])
        <div class="grid">
          <div class="card"><div class="label">Equation</div><p><strong>{{ $report['report_regression']['equation'] }}</strong></p></div>
          <div class="card"><div class="label">Slope</div><div class="metric">{{ $report['report_regression']['slope'] ?? 'N/A' }}</div></div>
          <div class="card"><div class="label">Intercept</div><div class="metric">{{ $report['report_regression']['intercept'] ?? 'N/A' }}</div></div>
          <div class="card"><div class="label">R-squared</div><div class="metric">{{ $report['report_regression']['r_squared'] ?? 'N/A' }}</div></div>
        </div>
        <p class="note">{{ $report['report_regression']['interpretation'] }}</p>
      @else
        <p class="muted">Regression could not be generated because this dataset needs at least two numeric columns.</p>
      @endif
    </section>

    <section class="section">
      <h2>Learning Interpretation</h2>
      <div class="two">
        <p class="note">{{ $report['explanations']['mean'] }}</p>
        <p class="note">{{ $report['explanations']['standard_deviation'] }}</p>
        <p class="note">{{ $report['explanations']['correlation'] }}</p>
        <p class="note">{{ $report['explanations']['r_squared'] }}</p>
      </div>
    </section>
  </main>
</div>
</body>
</html>
