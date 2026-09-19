<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Apply as Instructor — DataSensei</title>
<style>
/* Apply as instructor. Colours, type and radius come from partials.design-system;
   the sidebar comes from partials.sidebar-shell. */
* { box-sizing: border-box; }
body { margin: 0; background: var(--bg); color: var(--text); font-family: var(--ds-font-sans); }
.layout { display: flex; min-height: 100vh; }
.main { flex: 1; min-width: 0; padding: 28px 32px 48px; }

.top { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 24px; }
.top > div { min-width: 0; }
.subtitle { max-width: 72ch; margin: 4px 0 0; color: var(--muted); font-size: 0.875rem; line-height: 1.5; }

.card { max-width: 720px; margin-bottom: 16px; padding: 20px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); }
.card > p { margin: 0; font-size: 0.875rem; line-height: 1.5; color: var(--ds-text-secondary); }
.card h3 { margin: 4px 0 8px; font-size: 0.9375rem; font-weight: 600; line-height: 1.35; overflow-wrap: anywhere; }
.muted { color: var(--muted); }
.card > p.muted { color: var(--muted); font-size: 0.8125rem; }

.badge { display: inline-flex; align-items: center; padding: 2px 8px; border: 1px solid var(--ds-border-strong); border-radius: var(--radius-xs); background: var(--surface2); color: var(--ds-text-secondary); font-size: 0.75rem; font-weight: 600; line-height: 1.4; white-space: nowrap; vertical-align: 1px; }
.badge.good { border-color: var(--ds-success-border); background: var(--ds-success-soft); color: var(--ds-success-text); }
.badge.warn { border-color: var(--ds-warning-border); background: var(--ds-warning-soft); color: var(--ds-warning-text); }
.badge.bad { border-color: var(--ds-danger-border); background: var(--ds-danger-soft); color: var(--ds-danger-text); }

.form-row { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
.card > * + .form-row { margin-top: 16px; }
.input, select {
  min-height: 38px;
  padding: 8px 12px;
  background: var(--surface3);
  border: 1px solid var(--ds-input-border);
  border-radius: var(--radius-sm);
  color: var(--text);
  font: 400 0.875rem/1.4 var(--ds-font-sans);
  transition: border-color 0.12s ease, box-shadow 0.12s ease;
}
.input { flex: 1 1 240px; max-width: 320px; }
.input::placeholder { color: var(--dim); }
.input:focus, select:focus { border-color: var(--accent); box-shadow: var(--ds-focus-ring); outline: none; }

.btn { min-height: 38px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 0 16px; border: 1px solid var(--accent); border-radius: var(--radius-sm); background: var(--accent); color: #fff; font: 500 0.875rem/1.2 var(--ds-font-sans); text-decoration: none; white-space: nowrap; cursor: pointer; transition: background 0.12s ease, border-color 0.12s ease; }
.btn:hover { background: var(--accent-hover); border-color: var(--accent-hover); }
.btn.secondary { background: var(--surface2); border-color: var(--ds-border-strong); color: var(--text); }
.btn.secondary:hover { background: var(--ds-surface-hover); }

.alert { max-width: 720px; margin-bottom: 16px; padding: 12px 16px; border: 1px solid var(--ds-success-border); border-radius: var(--radius-sm); background: var(--ds-success-soft); color: #d1fae5; font-size: 0.875rem; line-height: 1.5; }
.alert.error { border-color: var(--ds-danger-border); background: var(--ds-danger-soft); color: #fee2e2; }

@media (max-width: 900px) { .main { padding: 24px 20px 40px; } }
@media (max-width: 640px) {
  .main { padding: 20px 16px 32px; }
  .card { padding: 16px; }
  .input { max-width: none; flex-basis: 100%; }
  .form-row .btn { flex: 1 1 auto; }
}
</style>

    @include('partials.page-head', ['pageTitle' => 'Apply as Instructor', 'pageDescription' => 'Apply for an instructor account on DataSensei.'])
</head>
<body>
<div class="layout">
  @include('partials.instructor-sidebar')
  <main class="main">

    <div class="top">
      <div>
        <h1 class="title ds-page-title">Apply as Instructor</h1>
        <p class="subtitle">Enter the institution code given by your institution administrator.</p>
      </div>
    </div>

    @if(session('success'))<div class="alert">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert error">{{ $errors->first() }}</div>@endif

    <div class="card">
      @if(isset($existingApplication) && $existingApplication)
        <p class="muted">Latest application:</p>
        <h3>{{ $existingApplication->institution->name ?? 'Institution' }}</h3>
        <p>Status: <span class="badge {{ $existingApplication->status === 'approved' ? 'good' : ($existingApplication->status === 'rejected' ? 'bad' : 'warn') }}">{{ ucfirst($existingApplication->status) }}</span></p>
      @endif

      <form method="POST" action="{{ route('instructor.apply.submit') }}" class="form-row">
        @csrf
        <input class="input" name="institution_code" maxlength="6" placeholder="6-character institution code" value="{{ old('institution_code') }}" required>
        <button class="btn" type="submit">Submit Application</button>
      </form>
    </div>

  </main>
</div>
</body>
</html>
