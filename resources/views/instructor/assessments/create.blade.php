<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Create Assessment Draft — DataSensei</title>
<style>
    /* New assessment draft form. Colours, type and radius come from partials.design-system. */
    *{box-sizing:border-box}
    body{margin:0;background:var(--bg);color:var(--text);font-family:var(--ds-font-sans)}
    .layout{display:flex;min-height:100vh}
    .main{flex:1;min-width:0;padding:28px 32px 48px}
    .wrap{max-width:1100px;margin:0 auto}

    /* page header */
    .top{display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px;margin-bottom:24px}
    .top > div:first-child{min-width:0;flex:1 1 320px}
    .subtitle{max-width:72ch;margin:4px 0 0;color:var(--muted);font-size:.875rem;line-height:1.55}

    .card{padding:20px;border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}
    .grid{display:grid;gap:16px}
    .grid-2{grid-template-columns:repeat(2,minmax(0,1fr))}

    /* form */
    .field label{display:block;margin-bottom:6px;color:var(--ds-text-secondary);font-size:.8125rem;font-weight:500}
    .input,.select,.textarea{width:100%;min-height:38px;padding:8px 12px;border:1px solid var(--ds-input-border);border-radius:var(--radius-sm);
      background:var(--surface3);color:var(--text);font:400 .875rem/1.4 var(--ds-font-sans);outline:none;
      transition:border-color .12s ease,box-shadow .12s ease}
    .input::placeholder,.textarea::placeholder{color:var(--dim)}
    .input:focus,.select:focus,.textarea:focus{border-color:var(--accent);box-shadow:var(--ds-focus-ring)}
    .textarea{min-height:105px;resize:vertical;line-height:1.55}

    .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:38px;padding:0 16px;
      border:1px solid var(--accent);border-radius:var(--radius-sm);background:var(--accent);color:#fff;
      font:500 .875rem/1.2 var(--ds-font-sans);text-decoration:none;white-space:nowrap;cursor:pointer;
      transition:background .12s ease,border-color .12s ease}
    .btn:hover{border-color:var(--accent-hover);background:var(--accent-hover)}
    .btn.secondary{border-color:var(--ds-border-strong);background:var(--surface2);color:var(--text)}
    .btn.secondary:hover{background:var(--ds-surface-hover)}
    /* The form's only submit button is its primary action. */
    .btn.good{border-color:var(--accent);background:var(--accent);color:#fff}
    .btn.good:hover{border-color:var(--accent-hover);background:var(--accent-hover)}
    .actions{display:flex;gap:8px;flex-wrap:wrap}
    form.card > .actions{padding-top:16px;border-top:1px solid var(--border)}

    .alert{margin-bottom:16px;padding:12px 16px;border:1px solid var(--ds-danger-border);border-radius:var(--radius-sm);
      background:var(--ds-danger-soft);color:#fee2e2;font-size:.875rem;line-height:1.5}
    .draft-note{margin-bottom:20px;padding:12px 16px;border:1px solid var(--ds-accent-border);border-radius:var(--radius-sm);
      background:var(--ds-accent-soft);color:#dbeafe;font-size:.875rem;line-height:1.55}
    .draft-note strong{color:#fff;font-weight:600}

    @media(max-width:900px){.main{padding:24px 20px 40px}}
    @media(max-width:760px){.grid-2{grid-template-columns:minmax(0,1fr)}}
    @media(max-width:640px){
      .main{padding:20px 16px 32px}
      .card{padding:16px}
      .top{align-items:stretch;flex-direction:column}
      .top > div:first-child{flex:0 0 auto}
      .top > .btn,form.card > .actions .btn{width:100%}
      .btn{white-space:normal;text-align:center}
    }
    @media(prefers-reduced-motion:reduce){.btn,.input,.select,.textarea{transition:none}}
  </style>
    @include('partials.page-head', ['pageTitle' => 'Create Assessment Draft', 'pageDescription' => 'Create, publish, and grade assessments for your classes.'])
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
        <div class="actions" style="justify-content:flex-end;margin-top:16px">
          <button class="btn good" type="submit">Create Draft & Start Authoring</button>
        </div>
      </form>
    </div>
  </main>
</div>
</body>
</html>
