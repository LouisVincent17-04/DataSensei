<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Guided EDA Roadmap — DataSensei</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#0d1320;--surface:#111c2d;--surface2:#17243a;--border:#243650;--text:#f8fafc;--muted:#9bacc4;--accent:#3b82f6;--accent2:#60a5fa;--good:#10b981;--danger:#f87171;--radius:18px;--small:11px}
    *{box-sizing:border-box}body{margin:0;background:radial-gradient(circle at 85% 0,rgba(59,130,246,.15),transparent 34%),var(--bg);color:var(--text);font-family:Inter,Arial,sans-serif}.layout{display:flex;min-height:100vh}.content{flex:1;min-width:0;padding:34px}.page{max-width:1240px;margin:0 auto}.hero{max-width:780px;margin-bottom:24px}.eyebrow{font-size:.74rem;color:var(--accent2);font-weight:900;letter-spacing:.13em;text-transform:uppercase}.hero h1{font-size:clamp(2rem,4vw,3.15rem);line-height:1.08;margin:8px 0 12px}.hero p,.muted{color:var(--muted);line-height:1.65}.roadmap{display:grid;grid-template-columns:repeat(8,minmax(105px,1fr));gap:8px;margin:24px 0 30px;overflow-x:auto;padding-bottom:5px}.roadmap-step{min-width:105px;border:1px solid var(--border);background:rgba(17,28,45,.78);border-radius:13px;padding:12px}.roadmap-step span{display:grid;place-items:center;width:24px;height:24px;border-radius:50%;background:rgba(59,130,246,.15);color:var(--accent2);font-size:.75rem;font-weight:900;margin-bottom:8px}.roadmap-step strong{display:block;font-size:.78rem;line-height:1.35}.section-head{display:flex;align-items:end;justify-content:space-between;gap:18px;margin:28px 0 14px}.section-head h2{font-size:1.35rem;margin:0}.section-head p{margin:0;color:var(--muted);font-size:.88rem}.starter-grid{display:grid;grid-template-columns:minmax(0,1.1fr) minmax(0,.9fr);gap:18px}.card{background:linear-gradient(180deg,rgba(255,255,255,.025),transparent),var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:22px;box-shadow:0 18px 45px rgba(0,0,0,.16)}.recommended{border-color:rgba(59,130,246,.55);box-shadow:0 18px 50px rgba(37,99,235,.12)}.badge{display:inline-flex;border-radius:999px;padding:6px 10px;background:rgba(59,130,246,.15);border:1px solid rgba(96,165,250,.3);color:#bfdbfe;font-size:.72rem;font-weight:900;text-transform:uppercase;letter-spacing:.07em}.card h2,.card h3{margin:12px 0 8px}.card p{color:var(--muted);line-height:1.6}.upload-box{border:1px dashed #3b587d;background:rgba(59,130,246,.055);padding:18px;border-radius:14px;margin:16px 0}.upload-box label{display:block;font-size:.87rem;font-weight:800;margin-bottom:9px}.upload-box input{display:block;width:100%;color:var(--muted);font:inherit}.limits{display:flex;gap:8px;flex-wrap:wrap;margin-top:12px}.limit{border:1px solid var(--border);background:var(--surface2);padding:6px 9px;border-radius:999px;color:var(--muted);font-size:.74rem;font-weight:700}.btn{display:inline-flex;align-items:center;justify-content:center;min-height:44px;border:0;border-radius:11px;background:linear-gradient(135deg,var(--accent),#2563eb);color:#fff;text-decoration:none;padding:11px 16px;font:800 .88rem Inter;cursor:pointer}.btn:hover{filter:brightness(1.08)}.btn.full{width:100%}.btn.secondary{background:var(--surface2);border:1px solid var(--border);color:var(--text)}.notice{border:1px solid rgba(16,185,129,.35);background:rgba(16,185,129,.09);color:#bbf7d0;border-radius:12px;padding:12px 14px;margin-bottom:16px}.error{border-color:rgba(248,113,113,.45);background:rgba(248,113,113,.09);color:#fecaca}.datasets{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}.dataset{display:flex;flex-direction:column;min-height:300px}.dataset-top{display:flex;align-items:start;justify-content:space-between;gap:10px}.dataset h3{font-size:1.06rem}.dataset .description{font-size:.84rem;flex:1}.meta{display:flex;gap:8px;margin:4px 0 18px}.meta span{font-size:.75rem;color:var(--muted);border:1px solid var(--border);border-radius:9px;padding:7px 9px;background:var(--surface2)}.dataset-actions{display:grid;grid-template-columns:1fr 1fr;gap:8px}.beginner-note{display:flex;gap:11px;align-items:start;background:rgba(16,185,129,.07);border:1px solid rgba(16,185,129,.25);border-radius:13px;padding:14px;margin-top:16px}.beginner-note strong{color:#a7f3d0}.beginner-note p{margin:3px 0 0;font-size:.82rem}.error-text{color:#fecaca;font-size:.82rem;margin-top:8px}.fine-print{font-size:.76rem;color:var(--muted);margin:12px 0 0}@media(max-width:1100px){.roadmap{grid-template-columns:repeat(4,minmax(130px,1fr))}.datasets{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:800px){.layout{display:block}.content{padding:22px}.starter-grid,.datasets{grid-template-columns:1fr}.roadmap{grid-template-columns:repeat(8,130px)}.section-head{display:block}.section-head p{margin-top:6px}}@media(max-width:430px){.dataset-actions{grid-template-columns:1fr}}
  </style>
</head>
<body>
<div class="layout">
  @include('partials.sidebar')
  <main class="content">
    <div class="page">
      <header class="hero">
        <div class="eyebrow"></div>
        <h1 class="ds-page-title">Follow one clear path through your data.</h1>
        <p>DataSensei will guide you from a simple objective to a complete EDA summary. Each page shows only what you need for the current step.</p>
      </header>

      @if(session('success'))
        <div class="notice">{{ session('success') }}</div>
      @endif
      @if($errors->any())
        <div class="notice error"><strong>We could not load that dataset.</strong><br>{{ $errors->first() }}</div>
      @endif

      <section class="roadmap" aria-label="EDA roadmap">
        @foreach(['Objective','Load Data','Clean Data','Outliers','Univariate','Relationships','Features','Summary'] as $index => $label)
          <div class="roadmap-step"><span>{{ $index + 1 }}</span><strong>{{ $label }}</strong></div>
        @endforeach
      </section>

      <div class="section-head">
        <div><h2>Choose how to begin</h2><p>Predefined datasets are the easiest starting point for first-time learners.</p></div>
      </div>

      <section class="starter-grid">
        <article class="card recommended">
          <span class="badge">Recommended for beginners</span>
          <h2>Use a predefined dataset</h2>
          <p>Start immediately with a dataset prepared to demonstrate cleaning, outlier checks, useful charts, relationships, and feature engineering.</p>
          <div class="beginner-note">
            <div>✓</div>
            <div><strong>No setup required</strong><p>The objective and research questions are already prepared. Choose one of the datasets below.</p></div>
          </div>
          <a class="btn full" href="#predefined" style="margin-top:18px">Choose a dataset ↓</a>
        </article>

        <article class="card">
          <span class="badge">Your own data</span>
          <h2>Upload my dataset</h2>
          <p>Upload a CSV and DataSensei will automatically detect its structure and common data-quality issues.</p>
          <form action="{{ route('student.data-toolkit.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="upload-box">
              <label for="dataset_csv">Choose CSV file</label>
              <input id="dataset_csv" name="dataset_csv" type="file" accept=".csv,text/csv" required>
              @error('dataset_csv')<div class="error-text">{{ $message }}</div>@enderror
              <div class="limits">
                <span class="limit">CSV only</span>
                <span class="limit">Up to {{ $maxUploadMegabytes }} MB</span>
                <span class="limit">Up to {{ number_format($maxRows) }} rows</span>
              </div>
            </div>
            <button class="btn full" type="submit">Load and inspect dataset →</button>
          </form>
          <p class="fine-print">Malformed, empty, unreadable, oversized, or non-CSV files will be rejected with a simple explanation.</p>
        </article>
      </section>

      <div class="section-head" id="predefined">
        <div><h2>Predefined learning datasets</h2><p>Each one includes a clear objective and meaningful questions.</p></div>
      </div>

      <section class="datasets">
        @foreach($datasets as $dataset)
          <article class="card dataset">
            <div class="dataset-top">
              <div>
                <span class="badge">{{ $dataset['category'] }}</span>
                <h3>{{ $dataset['title'] }}</h3>
              </div>
              <span class="limit">{{ $dataset['difficulty'] }}</span>
            </div>
            <p class="description">{{ $dataset['description'] }}</p>
            <div class="meta">
              <span>{{ number_format(count($dataset['rows'])) }} rows</span>
              <span>{{ number_format(count($dataset['columns'])) }} columns</span>
            </div>
            <div class="dataset-actions">
              <button class="btn secondary dataset-modal-trigger" type="button" data-dataset-modal-url="{{ route('student.data-toolkit.rows', $dataset['key']) }}" data-dataset-title="{{ $dataset['title'] }}">Show Dataset</button>
              <a class="btn" href="{{ route('student.data-toolkit.show', ['dataset' => $dataset['key'], 'step' => 1]) }}">Start guided EDA →</a>
            </div>
          </article>
        @endforeach
      </section>
    </div>
  </main>
</div>
@include('student.data-toolkit.dataset-modal')
</body>
</html>
