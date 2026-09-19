<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Guided EDA Roadmap — DataSensei</title>
<style>
    /* Layout and components for the EDA toolkit landing page. Colours, type
       and radius come from partials.design-system. */
    *{box-sizing:border-box}
    body{margin:0;background:var(--bg);color:var(--text);font-family:var(--ds-font-sans)}
    .layout{display:flex;min-height:100vh}
    .content{flex:1;min-width:0;padding:28px 32px 48px}
    .page{max-width:1240px;margin:0 auto}

    /* ── page header ───────────────────────────────────────────── */
    .page-head{display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:16px;margin-bottom:24px}
    .page-head > div{min-width:0;flex:1 1 320px}
    .page-head p{max-width:72ch;margin:4px 0 0;color:var(--muted);font-size:.875rem;line-height:1.55}
    .muted{color:var(--muted);line-height:1.55}

    .notice{margin-bottom:16px;padding:12px 16px;border:1px solid var(--ds-success-border);border-radius:var(--radius-sm);
      background:var(--ds-success-soft);color:#d1fae5;font-size:.875rem;line-height:1.5}
    .notice.error{border-color:var(--ds-danger-border);background:var(--ds-danger-soft);color:#fee2e2}
    .notice strong{font-weight:600}

    /* ── roadmap: eight steps in one hairline strip ─────────────── */
    .roadmap{display:grid;grid-template-columns:repeat(8,minmax(0,1fr));gap:1px;margin:0 0 32px;
      border:1px solid var(--border);border-radius:var(--radius);background:var(--border);overflow:hidden}
    .roadmap-step{display:flex;flex-direction:column;align-items:flex-start;gap:6px;min-width:0;padding:12px;background:var(--surface)}
    .roadmap-step span{display:inline-grid;place-items:center;flex:0 0 22px;width:22px;height:22px;border:1px solid var(--ds-border-strong);
      border-radius:var(--radius-xs);background:var(--surface2);color:var(--muted);font-size:.75rem;font-weight:600;
      font-variant-numeric:tabular-nums}
    .roadmap-step strong{min-width:0;color:var(--ds-text-secondary);font-size:.8125rem;font-weight:500;line-height:1.3;overflow-wrap:break-word}

    /* ── section headings ──────────────────────────────────────── */
    .section-head{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;margin:32px 0 16px}
    .roadmap + .section-head{margin-top:0}
    .section-head h2{margin:0;font-size:1.125rem;font-weight:600;line-height:1.35}
    .section-head p{margin:4px 0 0;color:var(--muted);font-size:.875rem;line-height:1.5}

    /* ── cards ─────────────────────────────────────────────────── */
    .starter-grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:16px}
    .card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:20px}
    .recommended{border-color:var(--ds-accent-border)}
    .card h2,.card h3{margin:12px 0 6px;font-size:1rem;font-weight:600;line-height:1.35}
    .card p{margin:0;color:var(--muted);font-size:.875rem;line-height:1.55}

    .badge{display:inline-flex;align-items:center;padding:2px 8px;border:1px solid var(--ds-border-strong);border-radius:var(--radius-xs);
      background:var(--surface2);color:var(--ds-text-secondary);font-size:.75rem;font-weight:600;line-height:1.4;white-space:nowrap}
    .recommended > .badge{border-color:var(--ds-accent-border);background:var(--ds-accent-soft);color:var(--ds-accent-text)}

    .beginner-note{display:flex;gap:10px;align-items:flex-start;margin-top:16px;padding:12px 14px;
      border:1px solid var(--ds-success-border);border-radius:var(--radius-sm);background:var(--ds-success-soft)}
    .beginner-note > div:first-child{flex:0 0 auto;color:var(--ds-success-text);font-weight:600;line-height:1.4}
    .beginner-note strong{display:block;color:#d1fae5;font-size:.875rem;font-weight:600;line-height:1.4}
    .beginner-note p{margin:2px 0 0;color:var(--ds-text-secondary);font-size:.8125rem}

    .upload-box{margin:16px 0;padding:16px;border:1px dashed var(--ds-border-strong);border-radius:var(--radius-sm);background:var(--surface3)}
    .upload-box label{display:block;margin-bottom:6px;color:var(--ds-text-secondary);font-size:.8125rem;font-weight:500}
    .upload-box input{display:block;width:100%;color:var(--muted);font:inherit;font-size:.875rem;overflow-wrap:anywhere}
    .upload-box input::file-selector-button{min-height:32px;margin-right:12px;padding:0 12px;border:1px solid var(--ds-border-strong);
      border-radius:var(--radius-sm);background:var(--surface2);color:var(--text);font:500 .8125rem var(--ds-font-sans);cursor:pointer;
      transition:background .12s ease}
    .upload-box input::file-selector-button:hover{background:var(--ds-surface-hover)}
    .upload-box input:focus-visible{outline:none;box-shadow:none}
    .upload-box input:focus-visible::file-selector-button{box-shadow:var(--ds-focus-ring)}
    .limits{margin-top:10px;color:var(--muted);font-size:.75rem;line-height:1.5}
    .limits .limit:not(:last-child)::after{content:","}
    .error-text{margin-top:6px;color:var(--ds-danger-text);font-size:.75rem;line-height:1.4}
    .fine-print{margin:12px 0 0 !important;color:var(--muted);font-size:.75rem !important;line-height:1.5}

    /* ── buttons ───────────────────────────────────────────────── */
    .btn{display:inline-flex;align-items:center;justify-content:center;min-height:38px;padding:0 16px;
      border:1px solid var(--accent);border-radius:var(--radius-sm);background:var(--accent);color:#fff;
      font:500 .875rem/1.2 var(--ds-font-sans);text-align:center;text-decoration:none;cursor:pointer;
      transition:background .12s ease,border-color .12s ease}
    .btn:hover{border-color:var(--accent-hover);background:var(--accent-hover)}
    .btn.full{width:100%}
    .btn.secondary{border-color:var(--ds-border-strong);background:var(--surface2);color:var(--text)}
    .btn.secondary:hover{background:var(--ds-surface-hover)}

    /* ── predefined datasets ───────────────────────────────────── */
    .datasets{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
    .dataset{display:flex;flex-direction:column}
    .dataset-top{display:flex;align-items:flex-start;justify-content:space-between;gap:12px}
    .dataset-top > div{min-width:0;flex:1 1 auto}
    .dataset-top > .limit{flex-shrink:0;display:inline-flex;align-items:center;padding:2px 8px;border:1px solid var(--ds-border-strong);
      border-radius:var(--radius-xs);background:var(--surface2);color:var(--ds-text-secondary);font-size:.75rem;font-weight:600;
      line-height:1.4;white-space:nowrap}
    .dataset h3{margin:10px 0 0;font-size:.9375rem}
    .dataset .description{flex:1;margin-top:8px;font-size:.875rem}
    .meta{display:flex;flex-wrap:wrap;gap:4px;margin:12px 0 16px;color:var(--muted);font-size:.8125rem;font-variant-numeric:tabular-nums}
    .meta span:not(:last-child)::after{content:","}
    .dataset-actions{display:flex;flex-wrap:wrap;gap:8px}
    .dataset-actions .btn{flex:1 1 auto;padding:0 12px}

    @media(max-width:1280px){
      .roadmap{grid-template-columns:repeat(4,minmax(0,1fr))}
      .roadmap-step{flex-direction:row;align-items:center;gap:8px;padding:12px 14px}
    }
    @media(max-width:1100px){
      .datasets{grid-template-columns:repeat(2,minmax(0,1fr))}
    }
    @media(max-width:900px){
      .content{padding:24px 20px 40px}
      .starter-grid{grid-template-columns:minmax(0,1fr)}
    }
    @media(max-width:640px){
      .content{padding:20px 16px 32px}
      .card{padding:16px}
      .datasets{grid-template-columns:minmax(0,1fr)}
      .section-head{flex-direction:column;align-items:flex-start;gap:0}
    }
    @media(max-width:560px){
      .roadmap{grid-template-columns:repeat(2,minmax(0,1fr))}
    }
    @media(max-width:420px){
      .dataset-actions .btn{width:100%}
    }
    @media(prefers-reduced-motion:reduce){.btn,.upload-box input::file-selector-button{transition:none}}
  </style>
    @include('partials.page-head', ['pageTitle' => 'Guided EDA Roadmap', 'pageDescription' => 'Clean, explore, and profile a dataset before you model it.'])
</head>
<body>
<div class="layout">
  @include('partials.sidebar')
  <main class="content">
    <div class="page">
      <header class="page-head">
        <div>
          <h1 class="ds-page-title">EDA &amp; Data Toolkit</h1>
          <p>Work from a simple objective to a complete EDA summary, one guided step at a time.</p>
        </div>
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
          <a class="btn full" href="#predefined" style="margin-top:16px">Choose a dataset ↓</a>
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
