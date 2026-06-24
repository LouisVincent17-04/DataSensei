<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Toolkit — DataSensei</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#0d1320;--surface:#111c2d;--surface2:#1a2638;--border:#1e2f47;--text:#fafafa;--muted:#8aa0bd;--dim:#4b6080;--accent:#3b82f6;--good:#10b981;--warn:#f59e0b;--radius:16px;--radius-sm:10px}*{box-sizing:border-box}body{margin:0;background:radial-gradient(circle at top right,rgba(59,130,246,.13),transparent 38%),var(--bg);color:var(--text);font-family:Inter,Arial,sans-serif}.layout{display:flex;min-height:100vh}.content{flex:1;padding:32px;overflow:auto}.hero{display:flex;justify-content:space-between;align-items:flex-start;gap:18px;margin-bottom:24px}.eyebrow{font-size:.75rem;font-weight:900;text-transform:uppercase;letter-spacing:.12em;color:var(--accent)}h1{font-size:2.1rem;margin:6px 0 8px}.muted{color:var(--muted);line-height:1.65}.grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}.card{background:linear-gradient(180deg,rgba(255,255,255,.025),transparent),var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:20px;box-shadow:0 20px 45px rgba(0,0,0,.18)}.dataset-card{display:flex;flex-direction:column;min-height:310px}.card-top{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:14px}.tag{display:inline-flex;align-items:center;border:1px solid var(--border);background:var(--surface2);border-radius:999px;padding:5px 10px;font-size:.75rem;font-weight:800;color:var(--muted)}.title{font-size:1.15rem;font-weight:900;margin:0}.objective{font-size:.88rem;color:var(--muted);line-height:1.55;margin:0 0 16px}.meta{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin:12px 0 18px}.meta-box{border:1px solid var(--border);background:rgba(255,255,255,.025);border-radius:var(--radius-sm);padding:12px}.meta-label{font-size:.7rem;color:var(--dim);font-weight:800;text-transform:uppercase;letter-spacing:.06em}.meta-value{font-size:.9rem;font-weight:800;margin-top:4px}.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;background:linear-gradient(135deg,var(--accent),#2563eb);border:0;color:white;text-decoration:none;border-radius:var(--radius-sm);padding:11px 14px;font-weight:900;margin-top:auto}.btn:hover{filter:brightness(1.06)}.panel{margin-bottom:18px}.steps{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}.step{padding:14px;border:1px solid var(--border);background:var(--surface);border-radius:var(--radius-sm)}.step strong{display:block;margin-bottom:5px}.step span{font-size:.82rem;color:var(--muted);line-height:1.45}@media(max-width:1100px){.grid{grid-template-columns:repeat(2,minmax(0,1fr))}.steps{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:760px){.layout{display:block}.content{padding:22px}.hero{display:block}.grid,.steps{grid-template-columns:1fr}}
  </style>
</head>
<body>
<div class="layout">
  @include('partials.sidebar')
  <main class="content">
    <div class="hero">
      <div>
        <div class="eyebrow">Statistical Lab</div>
        <h1>Data Analysis Toolkit</h1>
        <p class="muted">Choose a built-in dataset, inspect its columns, run statistics, generate charts, and print a clean analysis report.</p>
      </div>
      <a class="btn" href="{{ route('student.analytics.index') }}">View Analytics</a>
    </div>

    <section class="panel steps">
      <div class="step"><strong>1. Select</strong><span>Pick a safe built-in dataset designed for data science practice.</span></div>
      <div class="step"><strong>2. Inspect</strong><span>Preview rows, identify numeric columns, and review categories.</span></div>
      <div class="step"><strong>3. Analyze</strong><span>Run descriptive statistics, correlation, and regression.</span></div>
      <div class="step"><strong>4. Report</strong><span>Generate a printable summary for documentation or review.</span></div>
    </section>

    <section class="grid">
      @foreach($datasets as $dataset)
        <article class="card dataset-card">
          <div class="card-top">
            <div>
              <h2 class="title">{{ $dataset['title'] }}</h2>
              <p class="muted" style="margin:6px 0 0">{{ $dataset['category'] }}</p>
            </div>
            <span class="tag">{{ $dataset['difficulty'] }}</span>
          </div>

          <p class="objective">{{ $dataset['description'] }}</p>
          <p class="objective"><strong style="color:var(--text)">Learning focus:</strong> {{ $dataset['learning_objective'] }}</p>

          <div class="meta">
            <div class="meta-box">
              <div class="meta-label">Rows</div>
              <div class="meta-value">{{ count($dataset['rows']) }}</div>
            </div>
            <div class="meta-box">
              <div class="meta-label">Columns</div>
              <div class="meta-value">{{ count($dataset['columns']) }}</div>
            </div>
          </div>

          <a class="btn" href="{{ route('student.data-toolkit.show', $dataset['key']) }}">Open Dataset</a>
        </article>
      @endforeach
    </section>
  </main>
</div>
</body>
</html>
