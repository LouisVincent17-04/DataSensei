<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Create Assessment Draft — DataSensei</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#0d1320;--surface:#111c2d;--surface2:#1a2638;--surface3:#0f1928;--border:#263854;--text:#f8fafc;--muted:#91a4bf;--dim:#68809f;--accent:#3b82f6;--good:#10b981;--bad:#ef4444;--radius:16px}
    *{box-sizing:border-box}body{margin:0;font-family:Inter,Arial,sans-serif;background:var(--bg);color:var(--text)}
    .layout{display:flex;min-height:100vh}.main{flex:1;padding:28px;min-width:0}.wrap{max-width:1100px;margin:0 auto}.top{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;margin-bottom:22px}.title{font-size:2rem;font-weight:900;margin:0}.subtitle{color:var(--muted);line-height:1.6;margin-top:8px}.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:20px}.grid{display:grid;gap:14px}.grid-2{grid-template-columns:repeat(2,minmax(0,1fr))}.field label{display:block;color:var(--dim);font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;margin-bottom:7px}.input,.select,.textarea{width:100%;background:var(--surface3);border:1px solid var(--border);color:var(--text);border-radius:10px;padding:10px 12px;font:inherit}.textarea{min-height:105px;resize:vertical}.btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;border:1px solid transparent;border-radius:10px;padding:10px 14px;font-weight:800;text-decoration:none;cursor:pointer;background:var(--accent);color:#fff}.btn.secondary{background:var(--surface2);border-color:var(--border);color:var(--text)}.btn.good{background:var(--good)}.actions{display:flex;gap:9px;flex-wrap:wrap}.alert{padding:13px 15px;border-radius:12px;margin-bottom:16px;border:1px solid rgba(239,68,68,.35);background:rgba(239,68,68,.10);color:#fecaca}.draft-note{border-left:4px solid var(--accent);background:rgba(59,130,246,.08);border-radius:10px;padding:12px 14px;margin-bottom:18px;color:var(--muted);line-height:1.5}.draft-note strong{color:var(--text)}
    @media(max-width:760px){.layout{display:block}.main{padding:18px}.top{flex-direction:column}.grid-2{grid-template-columns:1fr}}
  </style>
</head>
<body>
<div class="layout">
  @include('partials.instructor-sidebar')
  <main class="main">
    <div class="wrap">
      <div class="top">
        <div>
          <h1 class="title ds-page-title">Create assessment draft</h1>
          <p class="subtitle">DataSensei will create {{ $totalItems }} authoring slots from the selected TOS and preserve each slot's topic, objective, Bloom level, and difficulty.</p>
        </div>
        <a class="btn secondary" href="{{ route('instructor.tos.show', $tos) }}">Back to TOS</a>
      </div>

      @if($errors->any())
        <div class="alert">@foreach($errors->all() as $error){{ $error }}<br>@endforeach</div>
      @endif

      <form method="POST" action="{{ route('instructor.assessments.store', $tos) }}" class="card">
        @csrf
        <div class="draft-note">
          <strong>This starts as a real draft.</strong> You can save incomplete questions, leave the builder, and continue from your last item later. Students cannot see it until every item is complete and you publish it.
        </div>
        <div class="grid grid-2">
          <div class="field">
            <label for="class_id">Class</label>
            <select class="select" id="class_id" name="class_id" required>
              <option value="">Select class</option>
              @foreach($classes as $class)
                <option value="{{ $class->id }}" @selected(old('class_id', $tos->class_id) == $class->id)>{{ $class->name }} {{ $class->section ? '— '.$class->section : '' }}</option>
              @endforeach
            </select>
          </div>
          <div class="field">
            <label for="title">Title</label>
            <input class="input" id="title" name="title" value="{{ old('title', $tos->title.' Assessment') }}" required>
          </div>
          <div class="field">
            <label for="time_limit_minutes">Time Limit (minutes)</label>
            <input class="input" id="time_limit_minutes" type="number" min="1" max="1440" name="time_limit_minutes" value="{{ old('time_limit_minutes', 60) }}">
          </div>
          <div class="field">
            <label for="max_attempts">Maximum Attempts</label>
            <input class="input" id="max_attempts" type="number" min="1" max="10" name="max_attempts" value="{{ old('max_attempts', 1) }}" required>
          </div>
          <div class="field">
            <label for="available_at">Available At</label>
            <input class="input" id="available_at" type="datetime-local" name="available_at" value="{{ old('available_at') }}">
          </div>
          <div class="field">
            <label for="due_at">Due At</label>
            <input class="input" id="due_at" type="datetime-local" name="due_at" value="{{ old('due_at') }}">
          </div>
          <div class="field">
            <label for="description">Description</label>
            <textarea class="textarea" id="description" name="description">{{ old('description') }}</textarea>
          </div>
          <div class="field">
            <label for="instructions">Student Instructions</label>
            <textarea class="textarea" id="instructions" name="instructions">{{ old('instructions') }}</textarea>
          </div>
        </div>
        <div class="actions" style="justify-content:flex-end;margin-top:18px">
          <button class="btn good" type="submit">Create Draft & Start Authoring</button>
        </div>
      </form>
    </div>
  </main>
</div>
</body>
</html>
