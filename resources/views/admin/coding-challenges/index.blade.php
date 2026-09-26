@extends('admin.layout')

@section('title', 'Coding Challenges')
@section('page_title', 'Coding Challenges')
@section('page_subtitle', 'Create and version coding challenges. Each challenge holds one or more problems, and every problem is graded by its own test cases.')

@section('content')
  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Coding Challenge Library</h2>
        <p class="panel-subtitle">Search challenge versions and filter by difficulty category or availability.</p>
      </div>
      <div class="action-row">
        <span class="dim">{{ number_format($challenges->total()) }} versions</span>
        <a class="btn small" href="{{ route('admin.coding-challenges.create') }}">Create Coding Challenge</a>
      </div>
    </div>

    <form class="toolbar" method="GET" action="{{ route('admin.coding-challenges.index') }}">
      <div class="field">
        <label for="challenge-search">Search</label>
        <input id="challenge-search" class="input" name="search" value="{{ $search }}" placeholder="Title, content code, or version">
      </div>
      <div class="field">
        <label for="challenge-category">Category</label>
        <select id="challenge-category" class="select" name="category_id">
          <option value="">All categories</option>
          @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected((int) $categoryId === (int) $category->id)>{{ $category->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="field">
        <label for="challenge-status">Status</label>
        <select id="challenge-status" class="select" name="status">
          <option value="all" @selected($status === 'all')>All statuses</option>
          <option value="active" @selected($status === 'active')>Available</option>
          <option value="inactive" @selected($status === 'inactive')>Unavailable</option>
        </select>
      </div>
      <button class="btn" type="submit">Apply Filters</button>
      @if($search !== '' || $status !== 'all' || $categoryId > 0)
        <a class="btn secondary" href="{{ route('admin.coding-challenges.index') }}">Clear</a>
      @endif
    </form>
  </section>

  <section class="panel">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Challenge</th>
            <th>Category</th>
            <th>Module</th>
            <th>Version</th>
            <th>Problems</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($challenges as $challenge)
            <tr>
              <td>
                <strong>{{ $challenge->title }}</strong>
                <div class="dim">{{ $challenge->content_code }}, {{ number_format($challenge->time_limit_seconds) }} sec, {{ number_format($challenge->base_xp) }} XP</div>
              </td>
              <td>{{ $challenge->category?->name ?? 'Uncategorized' }}</td>
              <td>{{ $challenge->module_id ? ($challenge->module?->title ?? 'Module ' . $challenge->module_id) : 'None' }}</td>
              <td>
                <strong>{{ $challenge->version_name }}</strong>
                <div class="dim">{{ $challenge->version_code }}, V{{ $challenge->version_no }}</div>
              </td>
              <td>{{ number_format($challenge->coding_questions_count) }}</td>
              <td>{{ $challenge->is_active ? 'Available' : 'Unavailable' }}</td>
              <td>
                <div class="action-row">
                  <a class="btn small secondary" href="{{ route('admin.coding-challenges.show', $challenge) }}">View</a>
                  <a class="btn small" href="{{ route('admin.coding-challenges.edit', $challenge) }}">Edit</a>
                  <form method="POST" action="{{ route('admin.coding-challenges.status', $challenge) }}">
                    @csrf
                    @method('PATCH')
                    <button class="btn small {{ $challenge->is_active ? 'secondary' : 'green' }}" type="submit">{{ $challenge->is_active ? 'Make Unavailable' : 'Publish' }}</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="empty-cell">No coding challenge versions match the current filters.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="pagination">{{ $challenges->links('vendor.pagination.admin') }}</div>
  </section>
@endsection
