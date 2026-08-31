@extends('admin.layout')

@section('title', 'Learning Modules')
@section('eyebrow', 'Platform Content')
@section('page_title', 'Learning Modules')
@section('page_subtitle', 'Create, version, publish, and maintain the module-library content used by students and instructors.')

@section('content')
  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Module Library</h2>
        <p class="panel-subtitle">Search module versions or filter them by publication status.</p>
      </div>
      <div class="action-row">
        <span class="badge info">{{ number_format($modules->total()) }} versions</span>
        <a class="btn small" href="{{ route('admin.module-library.create') }}">Create Module Version</a>
      </div>
    </div>

    <form class="toolbar" method="GET" action="{{ route('admin.module-library.index') }}">
      <div class="field">
        <label for="module-search">Search</label>
        <input id="module-search" class="input" name="search" value="{{ $search }}" placeholder="Title, module code, version, or number">
      </div>
      <div class="field">
        <label for="module-status">Status</label>
        <select id="module-status" class="select" name="status">
          <option value="all" @selected($status === 'all')>All statuses</option>
          <option value="active" @selected($status === 'active')>Published</option>
          <option value="inactive" @selected($status === 'inactive')>Inactive</option>
        </select>
      </div>
      <button class="btn" type="submit">Apply Filters</button>
      @if($search !== '' || $status !== 'all')
        <a class="btn secondary" href="{{ route('admin.module-library.index') }}">Clear</a>
      @endif
    </form>
  </section>

  <section class="panel" style="margin-top:24px">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Module</th>
            <th>Version</th>
            <th>Year</th>
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
                <div class="dim">Module {{ $module->module_no }} · {{ $module->module_code }}</div>
              </td>
              <td>
                <strong>{{ $module->version_name }}</strong>
                <div class="dim">{{ $module->version_code }} · V{{ $module->version_no }}</div>
              </td>
              <td>{{ $module->year_level }}</td>
              <td>
                {{ count(is_array($module->content_sections) ? $module->content_sections : []) }} sections<br>
                <span class="dim">{{ count(is_array($module->mcq_questions) ? $module->mcq_questions : []) }} review questions</span>
              </td>
              <td>
                <span class="badge {{ $module->is_active ? 'active' : 'disabled' }}">
                  {{ $module->is_active ? 'Published' : 'Inactive' }}
                </span>
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
                      {{ $module->is_active ? 'Deactivate' : 'Publish' }}
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="empty-cell">No module versions match the current filters.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="pagination">{{ $modules->links('vendor.pagination.admin') }}</div>
  </section>
@endsection
