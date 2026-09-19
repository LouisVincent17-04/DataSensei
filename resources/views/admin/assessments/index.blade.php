@extends('admin.layout')

@section('title', 'Assessment Content')
@section('page_title', 'Assessment Content')
@section('page_subtitle', 'Create and version reusable assessment templates that instructors can assign to their classes.')

@section('content')
  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Assessment Library</h2>
        <p class="panel-subtitle">Search assessment versions and filter by question type or publication status.</p>
      </div>
      <div class="action-row">
        <span class="badge info">{{ number_format($assessments->total()) }} versions</span>
        <a class="btn small" href="{{ route('admin.assessments.create') }}">Create Assessment</a>
      </div>
    </div>

    <form class="toolbar" method="GET" action="{{ route('admin.assessments.index') }}">
      <div class="field">
        <label for="assessment-search">Search</label>
        <input id="assessment-search" class="input" name="search" value="{{ $search }}" placeholder="Title, topic, assignment code, or module">
      </div>
      <div class="field">
        <label for="assessment-type">Type</label>
        <select id="assessment-type" class="select" name="type">
          <option value="all" @selected($type === 'all')>All types</option>
          <option value="mcq" @selected($type === 'mcq')>MCQ</option>
          <option value="fill_blank" @selected($type === 'fill_blank')>Fill in the Blanks</option>
          <option value="mixed" @selected($type === 'mixed')>Mixed</option>
        </select>
      </div>
      <div class="field">
        <label for="assessment-status">Status</label>
        <select id="assessment-status" class="select" name="status">
          <option value="all" @selected($status === 'all')>All statuses</option>
          <option value="active" @selected($status === 'active')>Published</option>
          <option value="inactive" @selected($status === 'inactive')>Inactive</option>
        </select>
      </div>
      <button class="btn" type="submit">Apply Filters</button>
      @if($search !== '' || $status !== 'all' || $type !== 'all')
        <a class="btn secondary" href="{{ route('admin.assessments.index') }}">Clear</a>
      @endif
    </form>
  </section>

  <section class="panel">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Assessment</th>
            <th>Module</th>
            <th>Version</th>
            <th>Questions</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($assessments as $assessment)
            <tr>
              <td>
                <strong>{{ $assessment->title }}</strong>
                <div class="dim">{{ $assessment->assignment_code }}, {{ $assessment->topic_title }}</div>
              </td>
              <td>Module {{ $assessment->module_no }}<div class="dim">{{ $assessment->year_level }}</div></td>
              <td><strong>{{ $assessment->version_name }}</strong><div class="dim">{{ $assessment->version_code }}, V{{ $assessment->version_no }}</div></td>
              <td>{{ number_format($assessment->questions_count) }}<div class="dim">{{ $assessment->type_label }}, {{ number_format($assessment->total_points) }} points</div></td>
              <td>
                <span class="badge {{ $assessment->is_active ? 'active' : 'disabled' }}">{{ $assessment->is_active ? 'Published' : 'Inactive' }}</span>
                @if($assessment->class_assignments_count > 0)
                  <div class="dim">Used by {{ $assessment->class_assignments_count }} class assignment(s)</div>
                @endif
              </td>
              <td>
                <div class="action-row">
                  <a class="btn small secondary" href="{{ route('admin.assessments.show', $assessment) }}">View</a>
                  <a class="btn small" href="{{ route('admin.assessments.edit', $assessment) }}">Edit</a>
                  <form method="POST" action="{{ route('admin.assessments.status', $assessment) }}">
                    @csrf
                    @method('PATCH')
                    <button class="btn small {{ $assessment->is_active ? 'secondary' : 'green' }}" type="submit">{{ $assessment->is_active ? 'Deactivate' : 'Publish' }}</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="empty-cell">No assessment versions match the current filters.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="pagination">{{ $assessments->links('vendor.pagination.admin') }}</div>
  </section>
@endsection
