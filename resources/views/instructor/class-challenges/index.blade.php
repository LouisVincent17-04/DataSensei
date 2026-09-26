<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Class Challenges — DataSensei</title>
  @include('instructor.challenge-builder._styles')
  <style>
    .class-head{display:flex;align-items:baseline;justify-content:space-between;flex-wrap:wrap;gap:8px 16px}
    .class-count{color:var(--muted);font-size:.8125rem}
    details.edit-entry summary{cursor:pointer;color:var(--ds-accent-text,#93c5fd);font-size:.8125rem;font-weight:500;list-style:none;display:inline-flex;align-items:center;gap:6px}
    details.edit-entry summary::-webkit-details-marker{display:none}
    details.edit-entry summary::before{content:"";width:0;height:0;border-left:5px solid currentColor;border-top:4px solid transparent;border-bottom:4px solid transparent;transition:transform .12s ease}
    details.edit-entry[open] summary::before{transform:rotate(90deg)}
    details.edit-entry .edit-body{margin-top:12px;padding:16px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--surface3)}
    .table td.edit-cell{padding-top:4px}
    .table tbody tr.edit-row td{border-bottom:1px solid var(--border)}
    .table tbody tr.edit-row:hover td{background:none}
    .inline-form{display:inline}
    .status-text{white-space:nowrap}
  </style>
  @include('partials.admin-inspired-page-style')
  @include('partials.page-head', ['pageTitle' => 'Class Challenges', 'pageDescription' => 'Give quiz and coding challenges to your classes and set when they open and close.'])
</head>
<body class="ds-admin-inspired">
  @php
    $statusLabels = ['draft' => 'Draft', 'published' => 'Published', 'closed' => 'Closed'];
    $addFormSubmitted = old('class_id') !== null;
    $editingId = (int) old('edit_id', 0);
    $formatLocal = fn ($value) => $value ? $value->format('Y-m-d\TH:i') : '';
  @endphp
  <div class="ds-shell">
    @include('partials.instructor-sidebar')
    <main class="ds-main">
      <div class="wrap">
        <div class="top-row">
          <div>
            <h1 class="page-title ds-page-title">Class Challenges</h1>
            <p class="page-subtitle">Give one of your own challenges, or an available University Student challenge from the pool, to a class. Students see it on the University Student challenge map and on their assignments page while it is published and inside its window.</p>
            <div class="page-links">
              <a href="{{ route('instructor.challenge-builder.index') }}">Build your own challenges</a>
              <a href="{{ route('instructor.challenges.index') }}">Browse the platform challenge pool</a>
            </div>
          </div>
        </div>

        @if(session('success'))
          <div class="notice" role="alert">{{ session('success') }}</div>
        @endif
        @if(session('error'))
          <div class="notice error" role="alert">{{ session('error') }}</div>
        @endif
        @if($errors->any())
          <div class="notice error" role="alert">
            <strong>Please fix the following:</strong>
            <ul>
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <section class="panel">
          <div class="panel-head">
            <div class="panel-heading">
              <h2 class="panel-title">Give a challenge to a class</h2>
              <p class="panel-subtitle">Each challenge can be given to a class once. Leave the dates empty to keep it open for as long as it is published.</p>
            </div>
          </div>
          <div class="panel-body">
            @if($classes->isEmpty())
              <p class="muted">You have no active classes yet. Create a class first, then give it a challenge here.</p>
            @elseif($ownChallenges->isEmpty() && $poolChallenges->isEmpty())
              <p class="muted">There is nothing to give yet. <a href="{{ route('instructor.challenge-builder.create', ['type' => 'mcq']) }}">Build a challenge</a> or wait for an administrator to publish University Student challenges to the pool.</p>
            @else
              <form method="POST" action="{{ route('instructor.class-challenges.store') }}">
                @csrf
                <div class="form-grid three">
                  <div class="field">
                    <label for="add-class">Class</label>
                    <select id="add-class" class="select" name="class_id" required>
                      <option value="">Choose class</option>
                      @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected($addFormSubmitted && (int) old('class_id') === (int) $class->id)>{{ $class->name }}{{ $class->section ? ', ' . $class->section : '' }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="field">
                    <label for="add-challenge">Challenge</label>
                    <select id="add-challenge" class="select" name="challenge_id" required>
                      <option value="">Choose challenge</option>
                      @if($ownChallenges->isNotEmpty())
                        <optgroup label="My challenges">
                          @foreach($ownChallenges as $own)
                            <option value="{{ $own->id }}" @selected($addFormSubmitted && (int) old('challenge_id') === (int) $own->id)>{{ $own->title }} ({{ $own->is_coding_challenge ? 'Coding' : 'Quiz' }}{{ $own->is_active ? '' : ', unavailable' }})</option>
                          @endforeach
                        </optgroup>
                      @endif
                      @if($poolChallenges->isNotEmpty())
                        <optgroup label="Platform pool, University Student">
                          @foreach($poolChallenges as $pool)
                            <option value="{{ $pool->id }}" @selected($addFormSubmitted && (int) old('challenge_id') === (int) $pool->id)>{{ $pool->title }} ({{ $pool->is_coding_challenge ? 'Coding' : 'Quiz' }})</option>
                          @endforeach
                        </optgroup>
                      @endif
                    </select>
                  </div>
                  <div class="field">
                    <label for="add-status">Status</label>
                    <select id="add-status" class="select" name="status" required>
                      @foreach($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected(($addFormSubmitted ? old('status') : 'published') === $value)>{{ $label }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="field" style="grid-column:span 2">
                    <label for="add-title">Title shown to students (optional)</label>
                    <input id="add-title" class="input" name="title" maxlength="189" value="{{ $addFormSubmitted ? old('title') : '' }}" placeholder="Defaults to the challenge title">
                  </div>
                  <div class="field">
                    <label for="add-available">Available from (optional)</label>
                    <input id="add-available" class="input" type="datetime-local" name="available_at" value="{{ $addFormSubmitted ? old('available_at') : '' }}">
                  </div>
                  <div class="field" style="grid-column:span 2">
                    <label for="add-instructions">Instructions (optional)</label>
                    <textarea id="add-instructions" class="textarea" name="instructions" maxlength="5000" placeholder="Anything the class should know before starting">{{ $addFormSubmitted ? old('instructions') : '' }}</textarea>
                  </div>
                  <div class="field">
                    <label for="add-due">Due (optional)</label>
                    <input id="add-due" class="input" type="datetime-local" name="due_at" value="{{ $addFormSubmitted ? old('due_at') : '' }}">
                    <span class="hint">After the due date the challenge disappears from the class again.</span>
                  </div>
                </div>
                <div class="action-row" style="margin-top:16px">
                  <button class="btn" type="submit">Give to class</button>
                </div>
              </form>
            @endif
          </div>
        </section>

        @foreach($classes as $class)
          @php $entries = $assignmentsByClass->get($class->id, collect()); @endphp
          <section class="panel">
            <div class="panel-head">
              <div class="panel-heading">
                <h2 class="panel-title">{{ $class->name }}{{ $class->section ? ', ' . $class->section : '' }}</h2>
                <p class="panel-subtitle">{{ $entries->count() }} {{ $entries->count() === 1 ? 'challenge' : 'challenges' }} given to this class</p>
              </div>
            </div>
            <div class="table-wrap" style="overflow-x:auto">
              <table class="table">
                <thead>
                  <tr>
                    <th>Challenge</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Available from</th>
                    <th>Due</th>
                    <th>Students</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                @forelse($entries as $entry)
                  @php
                    $given = $entry->challenge;
                    $isEditing = $editingId === (int) $entry->id;
                    $oldOr = fn (string $key, $fallback) => $isEditing ? old($key, $fallback) : $fallback;
                  @endphp
                  <tr>
                    <td>
                      <strong>{{ $entry->title ?: ($given?->title ?? 'Challenge') }}</strong>
                      <span class="row-meta">
                        @if($given)
                          {{ $given->title }}{{ $given->isInstructorOwned() ? ' (yours)' : ' (platform pool)' }}{{ $given->is_active ? '' : ', currently unavailable' }}
                        @else
                          The challenge no longer exists
                        @endif
                      </span>
                    </td>
                    <td>{{ $given ? ($given->is_coding_challenge ? 'Coding' : 'Quiz') : '—' }}</td>
                    <td class="status-text">{{ $statusLabels[$entry->status] ?? ucfirst($entry->status) }}</td>
                    <td>{{ $entry->available_at ? $entry->available_at->format('M d, Y, h:i A') : 'Immediately' }}</td>
                    <td>{{ $entry->due_at ? $entry->due_at->format('M d, Y, h:i A') : 'No due date' }}</td>
                    <td>
                      @if($entry->isOpenNow() && $given && $given->is_active)
                        Open now
                      @elseif($entry->status !== 'published')
                        Hidden ({{ strtolower($statusLabels[$entry->status] ?? $entry->status) }})
                      @elseif($given && ! $given->is_active)
                        Hidden (challenge unavailable)
                      @elseif($entry->available_at && $entry->available_at->isFuture())
                        Opens later
                      @else
                        Closed (past due)
                      @endif
                    </td>
                    <td>
                      <form class="inline-form" method="POST" action="{{ route('instructor.class-challenges.destroy', $entry) }}" onsubmit="return confirm('Remove this challenge from the class? Attempts students already made are kept.');">
                        @csrf
                        @method('DELETE')
                        <button class="btn danger small" type="submit">Remove</button>
                      </form>
                    </td>
                  </tr>
                  <tr class="edit-row">
                    <td class="edit-cell" colspan="7">
                      <details class="edit-entry" @if($isEditing) open @endif>
                        <summary>Edit status, dates, title or instructions</summary>
                        <form class="edit-body" method="POST" action="{{ route('instructor.class-challenges.update', $entry) }}">
                          @csrf
                          @method('PUT')
                          <input type="hidden" name="edit_id" value="{{ $entry->id }}">
                          <div class="form-grid three">
                            <div class="field">
                              <label for="status-{{ $entry->id }}">Status</label>
                              <select id="status-{{ $entry->id }}" class="select" name="status" required>
                                @foreach($statusLabels as $value => $label)
                                  <option value="{{ $value }}" @selected($oldOr('status', $entry->status) === $value)>{{ $label }}</option>
                                @endforeach
                              </select>
                            </div>
                            <div class="field">
                              <label for="available-{{ $entry->id }}">Available from</label>
                              <input id="available-{{ $entry->id }}" class="input" type="datetime-local" name="available_at" value="{{ $oldOr('available_at', $formatLocal($entry->available_at)) }}">
                            </div>
                            <div class="field">
                              <label for="due-{{ $entry->id }}">Due</label>
                              <input id="due-{{ $entry->id }}" class="input" type="datetime-local" name="due_at" value="{{ $oldOr('due_at', $formatLocal($entry->due_at)) }}">
                            </div>
                            <div class="field" style="grid-column:span 3">
                              <label for="title-{{ $entry->id }}">Title shown to students</label>
                              <input id="title-{{ $entry->id }}" class="input" name="title" maxlength="189" value="{{ $oldOr('title', $entry->title) }}" placeholder="Defaults to the challenge title">
                            </div>
                            <div class="field" style="grid-column:span 3">
                              <label for="instructions-{{ $entry->id }}">Instructions</label>
                              <textarea id="instructions-{{ $entry->id }}" class="textarea" name="instructions" maxlength="5000">{{ $oldOr('instructions', $entry->instructions) }}</textarea>
                            </div>
                          </div>
                          <div class="action-row" style="margin-top:12px">
                            <button class="btn small" type="submit">Save changes</button>
                          </div>
                        </form>
                      </details>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7"><div class="empty">No challenge has been given to this class yet.</div></td>
                  </tr>
                @endforelse
                </tbody>
              </table>
            </div>
          </section>
        @endforeach

        @if($classes->isEmpty())
          <section class="card">
            <div class="empty">You have no active classes. Once you create a class, you can give it challenges from here.</div>
          </section>
        @endif
      </div>
    </main>
  </div>
</body>
</html>
