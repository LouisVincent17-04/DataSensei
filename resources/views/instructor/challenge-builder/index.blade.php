<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Challenge Builder — DataSensei</title>
  @include('instructor.challenge-builder._styles')
  @include('partials.admin-inspired-page-style')
  @include('partials.page-head', ['pageTitle' => 'Challenge Builder', 'pageDescription' => 'Build your own quiz and coding challenges and give them to your classes.'])
</head>
<body class="ds-admin-inspired">
  <div class="ds-shell">
    @include('partials.instructor-sidebar')
    <main class="ds-main">
      <div class="wrap">
        <div class="top-row">
          <div>
            <h1 class="page-title ds-page-title">Challenge Builder</h1>
            <p class="page-subtitle">Quiz and coding challenges you built. They live on the University Student level and reach students only through the classes you give them to.</p>
            <div class="page-links">
              <a href="{{ route('instructor.class-challenges.index') }}">Give a challenge to a class</a>
              <a href="{{ route('instructor.challenges.index') }}">Browse the platform challenge pool</a>
            </div>
          </div>
          <div class="actions">
            <a class="btn" href="{{ route('instructor.challenge-builder.create', ['type' => 'mcq']) }}">New quiz challenge</a>
            <a class="btn secondary" href="{{ route('instructor.challenge-builder.create', ['type' => 'coding']) }}">New coding challenge</a>
          </div>
        </div>

        @if(session('success'))
          <div class="notice" role="alert">{{ session('success') }}</div>
        @endif
        @if(session('error'))
          <div class="notice error" role="alert">{{ session('error') }}</div>
        @endif
        @if(! $category)
          <div class="notice error" role="alert">The University Student challenge level has not been set up yet, so new challenges cannot be created. Ask an administrator to add it.</div>
        @endif

        <div class="card table-wrap">
          <table class="table">
            <thead>
              <tr>
                <th>Title</th>
                <th>Type</th>
                <th>Items</th>
                <th>Availability</th>
                <th>Given to</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
            @forelse($challenges as $challenge)
              <tr>
                <td>
                  <strong>{{ $challenge->title }}</strong>
                  @if($challenge->description)
                    <span class="row-meta">{{ \Illuminate\Support\Str::limit($challenge->description, 90) }}</span>
                  @endif
                </td>
                <td>{{ $challenge->is_coding_challenge ? 'Coding' : 'Quiz' }}</td>
                <td>{{ $challenge->is_coding_challenge ? $challenge->coding_questions_count : $challenge->questions_count }}</td>
                <td>{{ $challenge->is_active ? 'Available' : 'Unavailable' }}</td>
                <td>
                  @if($challenge->class_assignments_count > 0)
                    {{ $challenge->class_assignments_count }} {{ $challenge->class_assignments_count === 1 ? 'class' : 'classes' }}
                  @else
                    <span class="muted">No class yet</span>
                  @endif
                </td>
                <td>
                  <div class="action-row">
                    <a class="btn secondary small" href="{{ route('instructor.challenge-builder.edit', $challenge) }}">Edit</a>
                    <form method="POST" action="{{ route('instructor.challenge-builder.destroy', $challenge) }}" onsubmit="return confirm('Delete this challenge? Class entries that use it are removed as well.');">
                      @csrf
                      @method('DELETE')
                      <button class="btn danger small" type="submit">Delete</button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6">
                  <div class="empty">You have not built any challenges yet. Start with a quiz or a coding challenge above.</div>
                </td>
              </tr>
            @endforelse
            </tbody>
          </table>

          @if($challenges->hasPages())
            <div class="pager">
              <span>Showing {{ number_format($challenges->firstItem()) }}–{{ number_format($challenges->lastItem()) }} of {{ number_format($challenges->total()) }} challenges</span>
              <nav aria-label="Challenge pagination">
                @if($challenges->onFirstPage())
                  <span class="btn secondary small" aria-disabled="true">Previous</span>
                @else
                  <a class="btn secondary small" href="{{ $challenges->previousPageUrl() }}" rel="prev">Previous</a>
                @endif
                @if($challenges->hasMorePages())
                  <a class="btn secondary small" href="{{ $challenges->nextPageUrl() }}" rel="next">Next</a>
                @else
                  <span class="btn secondary small" aria-disabled="true">Next</span>
                @endif
              </nav>
            </div>
          @endif
        </div>
      </div>
    </main>
  </div>
</body>
</html>
