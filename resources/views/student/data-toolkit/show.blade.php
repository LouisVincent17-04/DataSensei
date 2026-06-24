<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $overview['dataset']['title'] }} — Data Toolkit</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <style>
    :root {
      --bg: #0d1320;
      --surface: #111c2d;
      --surface2: #1a2638;
      --surface3: #223149;
      --border: #1e2f47;
      --text: #fafafa;
      --muted: #8aa0bd;
      --dim: #4b6080;
      --accent: #3b82f6;
      --good: #10b981;
      --warn: #f59e0b;
      --bad: #ef4444;
      --radius: 16px;
      --radius-sm: 10px;
    }
    * { box-sizing: border-box; }
    body { margin: 0; background: radial-gradient(circle at top right, rgba(59,130,246,.13), transparent 38%), var(--bg); color: var(--text); font-family: Inter, Arial, sans-serif; }
    .layout { display: flex; min-height: 100vh; }
    .content { flex: 1; padding: 32px; overflow: auto; }
    .hero { display: flex; justify-content: space-between; align-items: flex-start; gap: 18px; margin-bottom: 22px; }
    .eyebrow { font-size: .75rem; font-weight: 900; text-transform: uppercase; letter-spacing: .12em; color: var(--accent); }
    h1 { font-size: 2rem; margin: 6px 0 8px; }
    h2 { margin: 0; }
    .muted { color: var(--muted); line-height: 1.65; }
    .grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; }
    .card { background: linear-gradient(180deg, rgba(255,255,255,.025), transparent), var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 18px; box-shadow: 0 20px 45px rgba(0,0,0,.18); }
    .wide { grid-column: span 2; }
    .full { grid-column: 1 / -1; }
    .label { font-size: .74rem; color: var(--muted); font-weight: 900; text-transform: uppercase; letter-spacing: .08em; }
    .metric { font-size: 1.8rem; font-weight: 900; margin-top: 4px; }
    .pill, .chip { display: inline-flex; align-items: center; gap: 6px; border: 1px solid var(--border); background: var(--surface2); border-radius: 999px; padding: 6px 10px; color: var(--muted); font-size: .78rem; font-weight: 800; text-decoration: none; }
    .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; background: linear-gradient(135deg, var(--accent), #2563eb); border: 0; color: white; text-decoration: none; border-radius: var(--radius-sm); padding: 10px 14px; font-weight: 900; cursor: pointer; font-family: inherit; }
    .btn.secondary { background: var(--surface2); border: 1px solid var(--border); color: var(--text); }
    .btn:disabled { opacity: .55; cursor: not-allowed; }
    .preview-toolbar { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-top: 12px; }
    .preview-controls { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .preview-controls .btn { padding: 8px 11px; font-size: .78rem; }
    .preview-controls .btn.active { background: linear-gradient(135deg, var(--accent), #2563eb); border-color: transparent; color: #fff; box-shadow: 0 0 0 3px rgba(59,130,246,.14); }
    .table-wrap { overflow: auto; border: 1px solid var(--border); border-radius: var(--radius-sm); margin-top: 12px; }
    table { width: 100%; border-collapse: collapse; min-width: 760px; }
    th, td { padding: 11px 12px; border-bottom: 1px solid var(--border); text-align: left; font-size: .86rem; vertical-align: top; }
    th { color: var(--muted); font-size: .74rem; text-transform: uppercase; letter-spacing: .06em; background: rgba(255,255,255,.025); }
    tr:last-child td { border-bottom: 0; }
    .columns { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
    .form-grid { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 10px; margin-top: 14px; }
    .field label { display: flex; align-items: center; gap: 6px; font-size: .72rem; color: var(--muted); font-weight: 900; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 6px; }
    .field select { width: 100%; border: 1px solid var(--border); background: var(--surface2); color: var(--text); border-radius: var(--radius-sm); padding: 10px 11px; font-family: inherit; }
    .result-box { margin-top: 14px; border: 1px solid var(--border); background: rgba(255,255,255,.025); border-radius: var(--radius-sm); padding: 14px; min-height: 92px; }
    .result-header { display: flex; justify-content: space-between; gap: 12px; align-items: flex-start; margin-bottom: 12px; }
    .result-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; }
    .mini { border: 1px solid var(--border); background: var(--surface2); border-radius: var(--radius-sm); padding: 11px; }
    .mini strong { display: block; font-size: 1rem; word-break: break-word; }
    .mini span { display: block; color: var(--muted); font-size: .74rem; margin-top: 3px; }
    .chart-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
    .chart-card { height: 330px; }
    .chart-card canvas { max-height: 260px; }
    .notice { border: 1px solid rgba(245,158,11,.35); background: rgba(245,158,11,.08); color: #fbbf24; border-radius: var(--radius-sm); padding: 12px; margin-top: 12px; }
    .success-note { border-color: rgba(16,185,129,.35); background: rgba(16,185,129,.08); color: #6ee7b7; }
    .small { font-size: .82rem; color: var(--muted); }
    .tooltip { position: relative; display: inline-flex; align-items: center; gap: 5px; cursor: help; border-bottom: 1px dotted rgba(138,160,189,.75); }
    .tooltip::after { content: attr(data-tooltip); position: absolute; left: 0; bottom: calc(100% + 10px); width: min(290px, 80vw); background: #020617; color: #f8fafc; border: 1px solid #334155; border-radius: 12px; padding: 10px 12px; font-size: .78rem; font-weight: 600; line-height: 1.45; text-transform: none; letter-spacing: normal; box-shadow: 0 18px 38px rgba(0,0,0,.35); opacity: 0; transform: translateY(4px); pointer-events: none; transition: opacity .15s ease, transform .15s ease; z-index: 50; }
    .tooltip::before { content: ''; position: absolute; left: 12px; bottom: calc(100% + 4px); border-width: 6px 6px 0 6px; border-style: solid; border-color: #334155 transparent transparent transparent; opacity: 0; transition: opacity .15s ease; z-index: 51; }
    .tooltip:hover::after, .tooltip:focus::after, .tooltip:hover::before, .tooltip:focus::before { opacity: 1; transform: translateY(0); }
    .tip-dot { display: inline-grid; place-items: center; width: 17px; height: 17px; border-radius: 999px; background: rgba(59,130,246,.16); color: #93c5fd; font-size: .68rem; font-weight: 900; }
    .history-list { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; margin-top: 12px; }
    .history-card { border: 1px solid var(--border); background: var(--surface2); border-radius: var(--radius-sm); padding: 12px; }
    .history-card strong { display: block; margin-bottom: 4px; }
    .history-card button { margin-top: 8px; }
    .definition-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; margin-top: 12px; }
    .definition-card { border: 1px solid var(--border); background: var(--surface2); border-radius: var(--radius-sm); padding: 12px; }
    .definition-card strong { display: block; margin-bottom: 6px; }
    @media (max-width: 1180px) { .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .wide { grid-column: span 2; } .form-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .chart-grid, .history-list, .definition-grid { grid-template-columns: 1fr; } }
    @media (max-width: 760px) { .layout { display: block; } .content { padding: 22px; } .grid, .form-grid { grid-template-columns: 1fr; } .wide, .full { grid-column: span 1; } .hero, .result-header { display: block; } }
  </style>
</head>
<body>
<div class="layout">
  @include('partials.sidebar')
  <main class="content">
    <div class="hero">
      <div>
        <div class="eyebrow">Data Toolkit</div>
        <h1>{{ $overview['dataset']['title'] }}</h1>
        <p class="muted">{{ $overview['dataset']['description'] }}</p>
      </div>
      <div style="display:flex;gap:10px;flex-wrap:wrap">
        <a class="btn secondary" href="{{ route('student.data-toolkit.index') }}">All Datasets</a>
        <a class="btn" href="{{ route('student.data-toolkit.report', $overview['dataset']['key']) }}">Printable Report</a>
      </div>
    </div>

    <section class="grid">
      <article class="card">
        <div class="label">Rows</div>
        <div class="metric">{{ number_format($overview['row_count']) }}</div>
        <p class="small">records available for analysis</p>
      </article>
      <article class="card">
        <div class="label">Columns</div>
        <div class="metric">{{ number_format($overview['column_count']) }}</div>
        <p class="small">fields detected from the dataset</p>
      </article>
      <article class="card">
        <div class="label">Numeric</div>
        <div class="metric">{{ count($overview['numeric_columns']) }}</div>
        <p class="small">columns for statistics and regression</p>
      </article>
      <article class="card">
        <div class="label">Categorical</div>
        <div class="metric">{{ count($overview['categorical_columns']) }}</div>
        <p class="small">columns for grouping and frequency counts</p>
      </article>

      <article class="card wide">
        <div class="label">Learning Objective</div>
        <p class="muted">{{ $overview['dataset']['learning_objective'] }}</p>
        <div class="columns">
          <span class="chip">{{ $overview['dataset']['category'] }}</span>
          <span class="chip">{{ $overview['dataset']['difficulty'] }}</span>
          <span class="chip">Built-in dataset</span>
          <span class="chip">100+ rows</span>
        </div>
      </article>

      <article class="card wide">
        <div class="label">Column Detection</div>
        <p class="small">Numeric columns can be used for descriptive statistics, correlation, regression, line charts, histograms, and scatter plots. Categorical columns can be used for frequency analysis and bar charts.</p>
        <div class="columns">
          @foreach($overview['numeric_columns'] as $column)
            <span class="chip">{{ $column }} · numeric</span>
          @endforeach
          @foreach($overview['categorical_columns'] as $column)
            <span class="chip">{{ $column }} · category</span>
          @endforeach
        </div>
      </article>

      <article class="card full">
        <div class="label">Dataset Preview</div>
        <div class="preview-toolbar">
          <p id="previewStatus" class="small" style="margin:0">Previewing first {{ count($overview['preview']) }} rows out of {{ number_format($overview['row_count']) }} total records. Statistical analysis still uses the full dataset.</p>
          <div class="preview-controls" aria-label="Dataset preview size options">
            <button class="btn secondary active" type="button" data-preview-limit="10">Show 10</button>
            <button class="btn secondary" type="button" data-preview-limit="25">Show 25</button>
            <button class="btn secondary" type="button" data-preview-limit="50">Show 50</button>
            <button class="btn secondary" type="button" data-preview-limit="all">Show All</button>
          </div>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                @foreach($overview['columns'] as $column)
                  <th>{{ $column }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody id="datasetPreviewBody">
              @foreach($overview['preview'] as $row)
                <tr>
                  @foreach($overview['columns'] as $column)
                    <td>{{ $row[$column] ?? '' }}</td>
                  @endforeach
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <p class="small" style="margin-top:10px">Use the buttons above to inspect more records. The charts and statistical results are already calculated from all {{ number_format($overview['row_count']) }} rows.</p>
      </article>

      <article class="card full">
        <div class="label">Run Analysis</div>
        <p class="small">Choose an analysis type and optional columns. You can run the same analysis repeatedly or switch columns to compare results without reloading the page.</p>
        <form id="analysisForm" class="form-grid">
          <div class="field">
            <label for="analysis_type"><span class="tooltip" tabindex="0" data-tooltip="Choose what kind of statistical result you want to calculate from the selected dataset.">Analysis <span class="tip-dot">?</span></span></label>
            <select id="analysis_type" name="analysis_type">
              <option value="descriptive">Descriptive Statistics</option>
              <option value="categorical">Categorical Summary</option>
              <option value="correlation">Correlation Matrix</option>
              <option value="regression">Linear Regression</option>
            </select>
          </div>
          <div class="field">
            <label for="numeric_column"><span class="tooltip" tabindex="0" data-tooltip="A numeric column contains numbers, so it can be used for mean, median, standard deviation, correlation, and regression.">Numeric Column <span class="tip-dot">?</span></span></label>
            <select id="numeric_column" name="numeric_column">
              <option value="">All numeric columns</option>
              @foreach($overview['numeric_columns'] as $column)
                <option value="{{ $column }}">{{ $column }}</option>
              @endforeach
            </select>
          </div>
          <div class="field">
            <label for="categorical_column"><span class="tooltip" tabindex="0" data-tooltip="A categorical column contains groups or labels, such as program, condition, category, or platform used.">Category Column <span class="tip-dot">?</span></span></label>
            <select id="categorical_column" name="categorical_column">
              <option value="">All category columns</option>
              @foreach($overview['categorical_columns'] as $column)
                <option value="{{ $column }}">{{ $column }}</option>
              @endforeach
            </select>
          </div>
          <div class="field">
            <label for="x_column"><span class="tooltip" tabindex="0" data-tooltip="The X column is the input or predictor in linear regression. It is used to estimate the Y column.">X Column <span class="tip-dot">?</span></span></label>
            <select id="x_column" name="x_column">
              @foreach($overview['numeric_columns'] as $column)
                <option value="{{ $column }}" @selected($column === ($overview['regression']['x_column'] ?? null))>{{ $column }}</option>
              @endforeach
            </select>
          </div>
          <div class="field">
            <label for="y_column"><span class="tooltip" tabindex="0" data-tooltip="The Y column is the output or target in linear regression. This is the value the model tries to estimate.">Y Column <span class="tip-dot">?</span></span></label>
            <select id="y_column" name="y_column">
              @foreach($overview['numeric_columns'] as $column)
                <option value="{{ $column }}" @selected($column === ($overview['regression']['y_column'] ?? null))>{{ $column }}</option>
              @endforeach
            </select>
          </div>
          <button id="runAnalysisButton" class="btn" type="submit" style="grid-column:1/-1">Run Selected Analysis</button>
        </form>
        <div id="analysisMessage" class="notice" style="display:none"></div>
        <div id="analysisResult" class="result-box"></div>
        <div id="analysisHistoryWrap" style="display:none;margin-top:16px">
          <div class="label">Recent Analysis Runs</div>
          <p class="small">Use this history to compare different selected columns and repeated analysis runs.</p>
          <div id="analysisHistory" class="history-list"></div>
        </div>
      </article>

      <article class="card full">
        <div class="label">Charts</div>
        <p class="small">The charts below show categorical frequency, numeric sequence trend, two-column scatter patterns, and histogram distribution.</p>
        <div class="chart-grid" style="margin-top:14px">
          <section class="chart-card card"><div class="label"><span class="tooltip" tabindex="0" data-tooltip="A bar chart compares counts across categories. Taller bars mean more records in that group.">Categorical Bar Chart <span class="tip-dot">?</span></span></div><canvas id="barChart"></canvas></section>
          <section class="chart-card card"><div class="label"><span class="tooltip" tabindex="0" data-tooltip="A line chart shows how numeric values move across the dataset order. It helps you spot upward, downward, or unstable patterns.">Numeric Line Chart <span class="tip-dot">?</span></span></div><canvas id="lineChart"></canvas></section>
          <section class="chart-card card"><div class="label"><span class="tooltip" tabindex="0" data-tooltip="A scatter plot shows the relationship between two numeric columns. Points forming a clear direction may indicate a relationship.">Scatter Plot <span class="tip-dot">?</span></span></div><canvas id="scatterChart"></canvas></section>
          <section class="chart-card card"><div class="label"><span class="tooltip" tabindex="0" data-tooltip="A histogram groups numeric values into ranges. It helps you see whether values are clustered, spread out, or skewed.">Histogram <span class="tip-dot">?</span></span></div><canvas id="histogramChart"></canvas></section>
        </div>
      </article>

      <article class="card wide">
        <div class="label"><span class="tooltip" tabindex="0" data-tooltip="Missing values are blank or unavailable entries. Too many missing values can make analysis less reliable.">Missing Values <span class="tip-dot">?</span></span></div>
        <div class="result-grid" style="margin-top:12px">
          @foreach($overview['missing_values'] as $column => $count)
            <div class="mini"><strong>{{ $count }}</strong><span>{{ $column }}</span></div>
          @endforeach
        </div>
        <p class="small" style="margin-top:12px">{{ $overview['explanations']['missing_values'] }}</p>
      </article>

      <article class="card wide">
        <div class="label">Statistical Terms</div>
        <div class="definition-grid">
          <div class="definition-card"><strong><span class="tooltip" tabindex="0" data-tooltip="The mean is the average. Add all values, then divide by how many values there are.">Mean <span class="tip-dot">?</span></span></strong><span class="small">Average value.</span></div>
          <div class="definition-card"><strong><span class="tooltip" tabindex="0" data-tooltip="The median is the middle value after sorting the numbers. It is useful when extreme values affect the average.">Median <span class="tip-dot">?</span></span></strong><span class="small">Middle value.</span></div>
          <div class="definition-card"><strong><span class="tooltip" tabindex="0" data-tooltip="Standard deviation shows how spread out values are. A small value means records are close to the mean; a large value means they vary more.">Standard Deviation <span class="tip-dot">?</span></span></strong><span class="small">Spread around the mean.</span></div>
          <div class="definition-card"><strong><span class="tooltip" tabindex="0" data-tooltip="Correlation describes how two numeric columns move together. Positive means both tend to increase together; negative means one tends to decrease as the other increases.">Correlation <span class="tip-dot">?</span></span></strong><span class="small">Relationship between variables.</span></div>
          <div class="definition-card"><strong><span class="tooltip" tabindex="0" data-tooltip="R-squared tells how much of the Y column pattern is explained by the regression line. Closer to 1 means stronger model fit.">R-squared <span class="tip-dot">?</span></span></strong><span class="small">Regression fit score.</span></div>
          <div class="definition-card"><strong><span class="tooltip" tabindex="0" data-tooltip="The interquartile range is Q3 minus Q1. It shows the spread of the middle half of the data.">IQR <span class="tip-dot">?</span></span></strong><span class="small">Middle-half spread.</span></div>
          <div class="definition-card"><strong><span class="tooltip" tabindex="0" data-tooltip="Variance is another measure of spread. It is similar to standard deviation, but squared.">Variance <span class="tip-dot">?</span></span></strong><span class="small">Squared spread value.</span></div>
          <div class="definition-card"><strong><span class="tooltip" tabindex="0" data-tooltip="The mode is the value that appears most often. A column can have more than one mode.">Mode <span class="tip-dot">?</span></span></strong><span class="small">Most repeated value.</span></div>
        </div>
      </article>
    </section>
  </main>
</div>
<script>
const overview = @json($overview);
const datasetRows = @json($overview['dataset']['rows']);
const datasetColumns = @json($overview['columns']);
const analyzeUrl = @json(route('student.data-toolkit.analyze', $overview['dataset']['key']));
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
let charts = {};
let analysisRunCounter = 0;
let latestRequestId = 0;
let lastPayloadByRun = {};

const tooltipDefinitions = {
  count: 'Count is the number of valid numeric values used in the calculation.',
  missing_count: 'Missing count is the number of blank or unavailable values in this column.',
  mean: 'Mean is the average. Add all values, then divide by the number of values.',
  median: 'Median is the middle value after sorting the values from smallest to largest.',
  mode: 'Mode is the value that appears most often in the column.',
  minimum: 'Minimum is the smallest value found in the column.',
  maximum: 'Maximum is the largest value found in the column.',
  range: 'Range is maximum minus minimum. It shows the full distance between the smallest and largest values.',
  variance: 'Variance measures spread by averaging squared differences from the mean.',
  standard_deviation: 'Standard deviation shows how far values usually are from the mean.',
  q1: 'Q1 is the first quartile. About 25% of the values are at or below this value.',
  q3: 'Q3 is the third quartile. About 75% of the values are at or below this value.',
  interquartile_range: 'Interquartile range is Q3 minus Q1. It shows the spread of the middle 50% of values.',
  unique_count: 'Unique count is the number of different values found in a categorical column.',
  most_frequent: 'Most frequent value is the category that appears the most times.',
  percent: 'Percent shows the share of records that belong to a category.',
  correlation: 'Correlation ranges from -1 to 1 and shows how strongly two numeric columns move together.',
  r_squared: 'R-squared ranges from 0 to 1 and shows how well the regression line explains the Y values.',
  slope: 'Slope shows how much Y usually changes when X increases by 1 unit.',
  intercept: 'Intercept is the predicted Y value when X is 0.',
  equation: 'The regression equation is the formula used to predict Y from X.'
};

function renderDatasetPreview(limit = 10) {
  const body = document.getElementById('datasetPreviewBody');
  const status = document.getElementById('previewStatus');

  if (!body || !Array.isArray(datasetRows) || !Array.isArray(datasetColumns)) {
    return;
  }

  const totalRows = datasetRows.length;
  const numericLimit = limit === 'all' ? totalRows : Math.max(1, parseInt(limit, 10) || 10);
  const visibleRows = datasetRows.slice(0, Math.min(numericLimit, totalRows));

  body.innerHTML = visibleRows.map(row => {
    const cells = datasetColumns.map(column => `<td>${escapeHtml(row[column] ?? '')}</td>`).join('');
    return `<tr>${cells}</tr>`;
  }).join('');

  if (status) {
    status.textContent = `Previewing ${visibleRows.length} of ${totalRows} total records. Statistical analysis and charts use all ${totalRows} rows.`;
  }
}

function setActivePreviewButton(selectedButton) {
  document.querySelectorAll('[data-preview-limit]').forEach(button => {
    button.classList.toggle('active', button === selectedButton);
  });
}

function escapeHtml(value) {
  return String(value ?? '').replace(/[&<>'"]/g, function (char) {
    return {'&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'}[char];
  });
}

function formatValue(value) {
  if (value === null || value === undefined || value === '') return 'N/A';
  if (typeof value === 'number') return Number.isInteger(value) ? value.toString() : value.toFixed(3).replace(/0+$/, '').replace(/\.$/, '');
  return String(value);
}

function titleCase(value) {
  return String(value).replaceAll('_', ' ').replace(/\b\w/g, letter => letter.toUpperCase());
}

function tip(label, definitionKey, customText = null) {
  const definition = customText || tooltipDefinitions[definitionKey] || 'This is a statistical value produced by the selected analysis.';
  return `<span class="tooltip" tabindex="0" data-tooltip="${escapeHtml(definition)}">${escapeHtml(label)} <span class="tip-dot">?</span></span>`;
}

function selectedOptionText(selectorId) {
  const element = document.getElementById(selectorId);
  if (!element || element.selectedIndex < 0) return '';
  return element.options[element.selectedIndex].text;
}

function buildPayloadFromForm(form) {
  const analysisType = document.getElementById('analysis_type').value;
  const payload = { analysis_type: analysisType };

  if (analysisType === 'descriptive') {
    payload.numeric_column = document.getElementById('numeric_column').value;
  }

  if (analysisType === 'categorical') {
    payload.categorical_column = document.getElementById('categorical_column').value;
  }

  if (analysisType === 'regression') {
    payload.x_column = document.getElementById('x_column').value;
    payload.y_column = document.getElementById('y_column').value;
  }

  return payload;
}

function describePayload(payload) {
  if (payload.analysis_type === 'descriptive') return payload.numeric_column ? `Column: ${payload.numeric_column}` : 'All numeric columns';
  if (payload.analysis_type === 'categorical') return payload.categorical_column ? `Column: ${payload.categorical_column}` : 'All category columns';
  if (payload.analysis_type === 'regression') return `${payload.x_column} predicts ${payload.y_column}`;
  if (payload.analysis_type === 'correlation') return 'All numeric columns';
  return 'Selected dataset';
}

function renderDescriptive(result) {
  let html = '<div class="result-grid">';
  Object.values(result).forEach(row => {
    html += `<div class="mini"><strong>${escapeHtml(row.column)}</strong><span>${escapeHtml(row.interpretation)}</span></div>`;
    ['count', 'missing_count', 'mean', 'median', 'mode', 'minimum', 'maximum', 'range', 'variance', 'standard_deviation', 'q1', 'q3', 'interquartile_range'].forEach(key => {
      html += `<div class="mini"><strong>${formatValue(row[key])}</strong><span>${tip(titleCase(key), key)}</span></div>`;
    });
  });
  html += '</div>';
  return html;
}

function renderCategorical(result) {
  let html = '';
  Object.values(result).forEach(row => {
    html += `<div class="mini" style="margin-bottom:10px"><strong>${escapeHtml(row.column)}</strong><span>${escapeHtml(row.interpretation)}</span></div>`;
    html += '<div class="result-grid" style="margin-bottom:12px">';
    html += `<div class="mini"><strong>${formatValue(row.unique_count)}</strong><span>${tip('Unique Count', 'unique_count')}</span></div>`;
    html += `<div class="mini"><strong>${escapeHtml(row.most_frequent ?? 'N/A')}</strong><span>${tip('Most Frequent', 'most_frequent')}</span></div>`;
    html += `<div class="mini"><strong>${formatValue(row.missing_count)}</strong><span>${tip('Missing Count', 'missing_count')}</span></div>`;
    html += '</div>';
    html += '<div class="table-wrap"><table><thead><tr><th>Value</th><th>Count</th><th>Percent</th></tr></thead><tbody>';
    row.distribution.forEach(item => {
      html += `<tr><td>${escapeHtml(item.label)}</td><td>${formatValue(item.count)}</td><td>${formatValue(item.percent)}%</td></tr>`;
    });
    html += '</tbody></table></div>';
  });
  return html || '<p class="small">No categorical data available.</p>';
}

function renderCorrelation(result) {
  if (!result.columns || result.columns.length < 2) {
    return `<p class="small">${escapeHtml(result.interpretation)}</p>`;
  }
  let html = `<p class="small">${escapeHtml(result.interpretation)} ${tip('What is correlation?', 'correlation')}</p><div class="table-wrap"><table><thead><tr><th>Column</th>`;
  result.columns.forEach(column => html += `<th>${escapeHtml(column)}</th>`);
  html += '</tr></thead><tbody>';
  result.columns.forEach(rowColumn => {
    html += `<tr><th>${escapeHtml(rowColumn)}</th>`;
    result.columns.forEach(colColumn => {
      const cell = result.matrix[rowColumn][colColumn];
      html += `<td>${formatValue(cell.value)}<br><span class="small">${escapeHtml(cell.strength)} ${escapeHtml(cell.direction)}</span></td>`;
    });
    html += '</tr>';
  });
  html += '</tbody></table></div>';
  return html;
}

function renderRegression(result) {
  return `<div class="result-grid">
    <div class="mini"><strong>${escapeHtml(result.equation)}</strong><span>${tip('Predicted Equation', 'equation')}</span></div>
    <div class="mini"><strong>${formatValue(result.slope)}</strong><span>${tip('Slope', 'slope')}</span></div>
    <div class="mini"><strong>${formatValue(result.intercept)}</strong><span>${tip('Intercept', 'intercept')}</span></div>
    <div class="mini"><strong>${formatValue(result.r_squared)}</strong><span>${tip('R-squared', 'r_squared')}</span></div>
  </div><p class="small" style="margin-top:12px">${escapeHtml(result.interpretation)}</p>`;
}

function renderAnalysis(payload, runNumber = null) {
  const box = document.getElementById('analysisResult');
  const title = titleCase(payload.analysis_type || 'analysis');
  const timestamp = payload.generated_at || 'Initial view';
  let body = '';

  if (payload.analysis_type === 'descriptive') body = renderDescriptive(payload.result);
  if (payload.analysis_type === 'categorical') body = renderCategorical(payload.result);
  if (payload.analysis_type === 'correlation') body = renderCorrelation(payload.result);
  if (payload.analysis_type === 'regression') body = renderRegression(payload.result);

  box.innerHTML = `<div class="result-header"><div><div class="label">${runNumber ? 'Run #' + runNumber : 'Current Result'}</div><h2>${escapeHtml(title)}</h2></div><span class="pill">${escapeHtml(timestamp)}</span></div>${body}`;
}

function showMessage(message, success = false) {
  const messageBox = document.getElementById('analysisMessage');
  messageBox.textContent = message;
  messageBox.classList.toggle('success-note', success);
  messageBox.style.display = message ? 'block' : 'none';
}

function addHistory(payload, requestPayload, runNumber) {
  const wrap = document.getElementById('analysisHistoryWrap');
  const list = document.getElementById('analysisHistory');
  const historyKey = `run_${runNumber}`;
  lastPayloadByRun[historyKey] = payload;
  wrap.style.display = 'block';

  const card = document.createElement('div');
  card.className = 'history-card';
  card.innerHTML = `<strong>Run #${runNumber}: ${escapeHtml(titleCase(payload.analysis_type))}</strong>
    <span class="small">${escapeHtml(describePayload(requestPayload))}</span><br>
    <span class="small">${escapeHtml(payload.generated_at || '')}</span><br>
    <button class="btn secondary" type="button" data-history-key="${historyKey}">View Result</button>`;
  list.prepend(card);

  while (list.children.length > 6) {
    list.removeChild(list.lastElementChild);
  }
}

document.getElementById('analysisHistory').addEventListener('click', function (event) {
  const button = event.target.closest('button[data-history-key]');
  if (!button) return;
  const payload = lastPayloadByRun[button.dataset.historyKey];
  if (payload) {
    renderAnalysis(payload);
    showMessage('Previous analysis result loaded for comparison.', true);
  }
});

document.querySelectorAll('[data-preview-limit]').forEach(button => {
  button.addEventListener('click', function () {
    setActivePreviewButton(button);
    renderDatasetPreview(button.dataset.previewLimit || '10');
  });
});

document.getElementById('analysisForm').addEventListener('submit', async function (event) {
  event.preventDefault();
  const requestId = ++latestRequestId;
  const button = document.getElementById('runAnalysisButton');
  const payload = buildPayloadFromForm(event.currentTarget);
  showMessage('Running analysis. Please wait...', true);
  button.disabled = true;
  button.textContent = 'Running Analysis...';

  try {
    const response = await fetch(analyzeUrl, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(payload)
    });
    const data = await response.json();

    if (requestId !== latestRequestId) {
      return;
    }

    if (!response.ok) {
      showMessage(data.message || 'The analysis could not be completed.');
      return;
    }

    const runNumber = ++analysisRunCounter;
    renderAnalysis(data, runNumber);
    addHistory(data, payload, runNumber);
    showMessage(`Analysis run #${runNumber} completed. You can run another analysis for comparison.`, true);
  } catch (error) {
    if (requestId === latestRequestId) {
      showMessage('Network error. Please refresh and try again.');
    }
  } finally {
    if (requestId === latestRequestId) {
      button.disabled = false;
      button.textContent = 'Run Selected Analysis';
    }
  }
});

function createChart(id, type, data, options = {}) {
  const element = document.getElementById(id);
  if (!element || typeof Chart === 'undefined') return;
  if (charts[id]) charts[id].destroy();
  charts[id] = new Chart(element, {
    type,
    data,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { labels: { color: '#8aa0bd' } } },
      scales: {
        x: { ticks: { color: '#8aa0bd' }, grid: { color: 'rgba(138,160,189,.12)' } },
        y: { ticks: { color: '#8aa0bd' }, grid: { color: 'rgba(138,160,189,.12)' } }
      },
      ...options
    }
  });
}

function renderCharts() {
  const chartData = overview.charts;
  createChart('barChart', 'bar', { labels: chartData.bar.labels, datasets: [{ label: chartData.category_column, data: chartData.bar.values }] });
  createChart('lineChart', 'line', { labels: chartData.line.labels, datasets: [{ label: chartData.numeric_column, data: chartData.line.values, tension: .3 }] });
  createChart('histogramChart', 'bar', { labels: chartData.histogram.labels, datasets: [{ label: chartData.numeric_column + ' distribution', data: chartData.histogram.values }] });
  createChart('scatterChart', 'scatter', { datasets: [{ label: `${chartData.scatter.x_column} vs ${chartData.scatter.y_column}`, data: chartData.scatter.points }] }, { parsing: false });
}

renderDatasetPreview(10);
renderAnalysis({ analysis_type: 'descriptive', result: overview.descriptive, generated_at: 'Initial default result' });
renderCharts();
</script>
</body>
</html>
