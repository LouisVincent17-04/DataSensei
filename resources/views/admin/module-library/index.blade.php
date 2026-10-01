@extends('admin.layout')

@section('title', 'Instructor Module Library')
@section('page_title', 'Instructor Module Library')
@section('page_subtitle', 'Modules for classes only. Instructors assign a published version to their classes, and only the students of those classes can open it. The open DataSensei Modules every learner sees are managed under DataSensei Modules.')

@section('content')
  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Library versions</h2>
        <p class="panel-subtitle">Search module versions or filter them by publication status.</p>
      </div>
      <div class="action-row">
        <span class="dim">{{ number_format($modules->total()) }} versions</span>
        <a class="btn small" href="{{ route('admin.module-library.create') }}">Create Module Version</a>
      </div>
    </div>

    <form class="toolbar" method="GET" action="{{ route('admin.module-library.index') }}">
      <div class="field">
        <label for="module-search">Search</label>
        <input id="module-search" class="input" name="search" value="{{ $search }}" placeholder="Title, version, or module number">
      </div>
      <div class="field">
        <label for="module-status">Status</label>
        <select id="module-status" class="select" name="status">
          <option value="all" @selected($status === 'all')>All statuses</option>
          <option value="active" @selected($status === 'active')>Published</option>
          <option value="inactive" @selected($status === 'inactive')>Draft</option>
        </select>
      </div>
      <button class="btn" type="submit">Apply Filters</button>
      @if($search !== '' || $status !== 'all')
        <a class="btn secondary" href="{{ route('admin.module-library.index') }}">Clear</a>
      @endif
    </form>
  </section>

  <section class="panel">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Module</th>
            <th>Version</th>
            <th>Content</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($modules as $module)
            <tr>
              <td>
                <strong>{{ $module->title }}</strong>
                <div class="dim">Module {{ $module->module_no }}</div>
              </td>
              <td>
                <strong>{{ $module->version_name }}</strong>
                <div class="dim">Version {{ $module->version_no }}</div>
              </td>
              <td>
                {{ count(is_array($module->content_sections) ? $module->content_sections : []) }} sections<br>
                <span class="dim">{{ count(is_array($module->mcq_questions) ? $module->mcq_questions : []) }} review questions, {{ count($module->learning_outcomes) }} learning outcomes</span>
              </td>
              <td>
                {{ $module->is_active ? 'Published' : 'Draft' }}
                @if($module->class_assignments_count > 0)
                  <div class="dim">Used by {{ $module->class_assignments_count }} class(es)</div>
                @endif
              </td>
              <td>
                <div class="action-row">
                  <a class="btn small secondary" href="{{ route('admin.module-library.show', $module) }}">View</a>
                  <a class="btn small" href="{{ route('admin.module-library.edit', $module) }}">Edit</a>
                  <form method="POST" action="{{ route('admin.module-library.status', $module) }}">
                    @csrf
                    @method('PATCH')
                    <button class="btn small {{ $module->is_active ? 'secondary' : 'green' }}" type="submit">
                      {{ $module->is_active ? 'Unpublish' : 'Publish' }}
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="empty-cell">No module versions match the current filters.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="pagination">{{ $modules->links('vendor.pagination.admin') }}</div>
  </section>
@endsection
