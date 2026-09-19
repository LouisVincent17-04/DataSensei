@extends('admin.layout')

@section('title', 'MCQ Challenges')
@section('page_title', 'MCQ Challenges')
@section('page_subtitle', 'Create and version multiple-choice challenges while keeping coding challenges under developer control.')

@section('content')
  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">MCQ Challenge Library</h2>
        <p class="panel-subtitle">Search challenge versions and filter by difficulty category or publication status.</p>
      </div>
      <div class="action-row">
        <span class="badge info">{{ number_format($challenges->total()) }} versions</span>
        <a class="btn small" href="{{ route('admin.challenges.create') }}">Create MCQ Challenge</a>
      </div>
    </div>

    <form class="toolbar" method="GET" action="{{ route('admin.challenges.index') }}">
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
          <option value="active" @selected($status === 'active')>Published</option>
          <option value="inactive" @selected($status === 'inactive')>Inactive</option>
        </select>
      </div>
      <button class="btn" type="submit">Apply Filters</button>
      @if($search !== '' || $status !== 'all' || $categoryId > 0)
        <a class="btn secondary" href="{{ route('admin.challenges.index') }}">Clear</a>
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
            <th>Version</th>
            <th>Questions</th>
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
              <td>
                <strong>{{ $challenge->version_name }}</strong>
                <div class="dim">{{ $challenge->version_code }}, V{{ $challenge->version_no }}</div>
              </td>
              <td>{{ number_format($challenge->questions_count) }}</td>
              <td>
                <span class="badge {{ $challenge->is_active ? 'active' : 'disabled' }}">{{ $challenge->is_active ? 'Published' : 'Inactive' }}</span>
                @if($challenge->attempts_count > 0)
                  <div class="dim">Has attempt history</div>
                @endif
              </td>
              <td>
                <div class="action-row">
                  <a class="btn small secondary" href="{{ route('admin.challenges.show', $challenge) }}">View</a>
                  <a class="btn small" href="{{ route('admin.challenges.edit', $challenge) }}">Edit</a>
                  <form method="POST" action="{{ route('admin.challenges.status', $challenge) }}">
                    @csrf
                    @method('PATCH')
                    <button class="btn small {{ $challenge->is_active ? 'secondary' : 'green' }}" type="submit">{{ $challenge->is_active ? 'Deactivate' : 'Publish' }}</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="empty-cell">No MCQ challenge versions match the current filters.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="pagination">{{ $challenges->links('vendor.pagination.admin') }}</div>
  </section>
@endsection
