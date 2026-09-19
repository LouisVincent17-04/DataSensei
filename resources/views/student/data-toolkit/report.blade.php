<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EDA Report — {{ $report['dataset']['title'] }}</title>
<style>
    /* Printable EDA report. It stays light on purpose (it is made for paper),
       so its colours live on <body>: partials.page-head defines the dark
       product tokens on :root after this block, and body-scoped values win. */
    *{box-sizing:border-box}
    body{
      --rp-bg:#f5f7fb;--rp-surface:#ffffff;--rp-inset:#f8fafc;--rp-head:#f1f5f9;
      --rp-border:#d8e0ed;--rp-border-strong:#c3cfe0;
      --rp-text:#111827;--rp-text-secondary:#334155;--rp-muted:#52637a;
      --rp-accent:#3b82f6;--rp-accent-strong:#2563eb;--rp-accent-soft:rgba(59,130,246,.08);--rp-accent-border:rgba(59,130,246,.3);
      color-scheme:light;margin:0;background:var(--rp-bg);color:var(--rp-text);font-family:var(--ds-font-sans);
      font-size:.875rem;line-height:1.5}
    body ::selection{color:var(--rp-text)}
    .page{max-width:1120px;margin:0 auto;padding:28px 32px 48px}

    .toolbar{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;margin-bottom:16px}
    .btn{display:inline-flex;align-items:center;justify-content:center;min-height:38px;padding:0 16px;
      border:1px solid var(--rp-border-strong);border-radius:var(--ds-radius-sm);background:var(--rp-surface);color:var(--rp-text);
      font:500 .875rem/1.2 var(--ds-font-sans);text-decoration:none;cursor:pointer;transition:background .12s ease,border-color .12s ease}
    .btn:hover{background:var(--rp-head)}
    .btn.primary{border-color:var(--rp-accent);background:var(--rp-accent);color:#fff}
    .btn.primary:hover{border-color:var(--rp-accent-strong);background:var(--rp-accent-strong)}

    .report{padding:32px;border:1px solid var(--rp-border);border-radius:var(--ds-radius-md);background:var(--rp-surface)}
    .header{margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid var(--rp-border)}
    .report .ds-page-title{color:var(--rp-text);overflow-wrap:anywhere}
    .muted{color:var(--rp-muted);line-height:1.6}
    .header .muted{max-width:72ch;margin:4px 0 12px}
    .chips{display:flex;flex-wrap:wrap;gap:4px 20px}
    .chip{color:var(--rp-muted);font-size:.8125rem;font-variant-numeric:tabular-nums}

    .grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin:16px 0}
    .card{min-width:0;padding:14px 16px;border:1px solid var(--rp-border);border-radius:var(--ds-radius-md);background:var(--rp-surface)}
    .card p{margin:6px 0 0;color:var(--rp-text-secondary);font-size:.875rem;overflow-wrap:anywhere}
    .card .muted{color:var(--rp-muted)}
    .label{color:var(--rp-muted);font-size:.8125rem;font-weight:500;line-height:1.4}
    .metric{margin-top:4px;font-size:1.5rem;font-weight:700;line-height:1.2;letter-spacing:-.02em;font-variant-numeric:tabular-nums;overflow-wrap:anywhere}

    .section{margin-top:32px}
    .section h2{margin:0 0 12px;font-size:1.125rem;font-weight:600;line-height:1.35}
    .section > h2 + .grid{margin-top:0}

    .table-wrap{overflow-x:auto;border:1px solid var(--rp-border);border-radius:var(--ds-radius-md)}
    table{width:100%;border-collapse:collapse;font-variant-numeric:tabular-nums}
    th,td{padding:10px 14px;border-bottom:1px solid var(--rp-border);text-align:left;vertical-align:top}
    th{background:var(--rp-head);color:var(--rp-muted);font-size:.75rem;font-weight:600;white-space:nowrap;text-transform:none;letter-spacing:0}
    td{color:var(--rp-text-secondary);font-size:.875rem}
    tbody tr:last-child td{border-bottom:0}

    .two{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
    .two > div{min-width:0}
    .note{margin:0;padding:12px 16px;border:1px solid var(--rp-accent-border);border-radius:var(--ds-radius-sm);background:var(--rp-accent-soft);
      color:var(--rp-text-secondary);font-size:.875rem;line-height:1.6}
    .section > .note{margin-top:12px}
    .section > h2 + .note{margin-top:0}

    @media(max-width:900px){
      .grid{grid-template-columns:repeat(2,minmax(0,1fr))}
      .two{grid-template-columns:minmax(0,1fr)}
    }
    @media(max-width:760px){
      .table-wrap table:has(th:nth-child(5)){min-width:720px}
    }
    @media(max-width:640px){
      .page{padding:16px 16px 32px}
      .report{padding:20px 16px}
      .toolbar .btn{flex:1 1 auto}
    }
    @media(max-width:420px){
      .grid{grid-template-columns:minmax(0,1fr)}
    }
    @media print{
      body{background:#fff}
      .page{max-width:none;padding:0}
      .toolbar{display:none}
      .report{padding:0;border:0;border-radius:0}
      .grid{grid-template-columns:repeat(4,minmax(0,1fr))}
      .two{grid-template-columns:repeat(2,minmax(0,1fr))}
      .table-wrap{overflow:visible}
      th{-webkit-print-color-adjust:exact;print-color-adjust:exact}
      .section{break-inside:avoid}
      .card{break-inside:avoid}
    }
    @media(prefers-reduced-motion:reduce){.btn{transition:none}}
  </style>
  @include('partials.ui-polish')
    @include('partials.page-head', ['pageDescription' => 'Clean, explore, and profile a dataset before you model it.'])
</head>
<body>
<div class="page">
  <div class="toolbar">
    <a class="btn" href="{{ route('student.data-toolkit.show', $report['dataset']['key']) }}">Back to Dataset</a>
    <button class="btn primary" onclick="window.print()">Print Report</button>
  </div>

  <main class="report">
    <header class="header">
      <h1 class="ds-page-title">{{ $report['dataset']['title'] }}</h1>
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
          <div class="card" style="margin-bottom:8px">
            <div class="label">{{ $summary['column'] }}</div>
            <p><strong>{{ $summary['unique_count'] }}</strong> unique values, Most common: <strong>{{ $summary['most_frequent'] ?? 'N/A' }}</strong></p>
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
          <div class="card" style="margin-top:8px">
            <div class="label">Strongest Pair</div>
            <p><strong>{{ $report['correlation']['strongest_pair']['x'] }}</strong> and <strong>{{ $report['correlation']['strongest_pair']['y'] }}</strong></p>
            <p class="muted">r = {{ $report['correlation']['strongest_pair']['value'] }}, {{ $report['correlation']['strongest_pair']['strength'] }} {{ $report['correlation']['strongest_pair']['direction'] }}</p>
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
