@extends('admin.layout')

@section('title', 'Module Library')
@section('eyebrow', 'Learning Content')
@section('page_title', 'Module Library')
@section('page_subtitle', 'Review and maintain module metadata and availability from a dedicated administrator workspace.')

@section('content')
  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Find a Module</h2>
        <p class="panel-subtitle">Search by title, module code, version code, or module number.</p>
      </div>
      <span class="badge info">{{ number_format($modules->total()) }} items</span>
    </div>

    <form class="toolbar" method="GET" action="{{ route('admin.module-library.index') }}">
      <div class="field">
        <label for="module-search">Module Search</label>
        <input id="module-search" class="input" name="module_search" value="{{ request('module_search') }}" placeholder="Search modules, codes, versions">
      </div>
      <button class="btn" type="submit">Search Modules</button>
      @if(request('module_search'))
        <a class="btn secondary" href="{{ route('admin.module-library.index') }}">Clear</a>
      @endif
    </form>
  </section>

  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Administrator Module Catalog</h2>
        <p class="panel-subtitle">Expand a module to update its display information, duration, order, and active status.</p>
      </div>
    </div>

    <div class="management-list">
      @forelse($modules as $module)
        <details class="management-item">
          <summary>
            <div class="management-main">
              <strong>{{ $module->title }}</strong>
              <span>{{ $module->module_code }} · Module {{ $module->module_no }}</span>
            </div>

            <div class="management-metric">
              <strong>{{ $module->version_name ?: 'Unnamed Version' }}</strong>
              {{ $module->version_code }} · {{ $module->year_level ?: 'No year level' }}
            </div>

            <div class="summary-status">
              <span class="badge {{ $module->is_active ? 'active' : 'disabled' }}">{{ $module->is_active ? 'Active' : 'Inactive' }}</span>
              <span class="management-meta">{{ number_format((int) $module->estimated_minutes) }} min</span>
            </div>

            <span class="management-toggle">Edit</span>
          </summary>

          <div class="management-editor">
            <form method="POST" action="{{ route('admin.content.modules.update', $module) }}">
              @csrf
              @method('PUT')

              <div class="form-grid three">
                <div class="field">
                  <label for="module-title-{{ $module->id }}">Title</label>
                  <input id="module-title-{{ $module->id }}" class="input" name="title" value="{{ $module->title }}" required>
                </div>

                <div class="field">
                  <label for="module-year-{{ $module->id }}">Year Level</label>
                  <input id="module-year-{{ $module->id }}" class="input" name="year_level" value="{{ $module->year_level }}">
                </div>

                <div class="field">
                  <label for="module-version-{{ $module->id }}">Version Name</label>
                  <input id="module-version-{{ $module->id }}" class="input" name="version_name" value="{{ $module->version_name }}">
                </div>

                <div class="field">
                  <label for="module-minutes-{{ $module->id }}">Estimated Minutes</label>
                  <input id="module-minutes-{{ $module->id }}" class="input" type="number" name="estimated_minutes" value="{{ $module->estimated_minutes }}" min="0">
                </div>

                <div class="field">
                  <label for="module-order-{{ $module->id }}">Sort Order</label>
                  <input id="module-order-{{ $module->id }}" class="input" type="number" name="sort_order" value="{{ $module->sort_order }}" min="0">
                </div>

                <div class="field">
                  <label for="module-status-{{ $module->id }}">Status</label>
                  <select id="module-status-{{ $module->id }}" class="select" name="is_active">
                    <option value="1" @selected($module->is_active)>Active</option>
                    <option value="0" @selected(!$module->is_active)>Inactive</option>
                  </select>
                </div>
              </div>

              <div class="field" style="margin-top:14px">
                <label for="module-description-{{ $module->id }}">Description</label>
                <textarea id="module-description-{{ $module->id }}" class="textarea" name="description">{{ $module->description }}</textarea>
              </div>

              <div class="action-row">
                <button class="btn small" type="submit">Save Module</button>
              </div>
            </form>
          </div>
        </details>
      @empty
        <div class="empty-cell">No modules found.</div>
      @endforelse
    </div>

    <div class="pagination">{{ $modules->links('vendor.pagination.admin') }}</div>
  </section>
@endsection
