@extends('admin.layout')

@section('title', 'Assessments')
@section('page_title', 'Assessments')
@section('page_subtitle', 'Reusable assessments that instructors can give to their classes as assignments.')

@section('content')
  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Assessment library</h2>
        <p class="panel-subtitle">{{ number_format($assessments->total()) }} {{ $assessments->total() === 1 ? 'assessment' : 'assessments' }}. Only published ones can be chosen by instructors.</p>
      </div>
      <div class="action-row">
        <a class="btn small" href="{{ route('admin.assessments.create') }}">Create assessment</a>
      </div>
    </div>

    <form class="toolbar" method="GET" action="{{ route('admin.assessments.index') }}">
      <div class="field">
        <label for="assessment-search">Search</label>
        <input id="assessment-search" class="input" name="search" value="{{ $search }}" placeholder="Title or topic">
      </div>
      <div class="field">
        <label for="assessment-type">Type</label>
        <select id="assessment-type" class="select" name="type">
          <option value="all" @selected($type === 'all')>All types</option>
          <option value="mcq" @selected($type === 'mcq')>Multiple choice</option>
          <option value="fill_blank" @selected($type === 'fill_blank')>Fill in the blanks</option>
          <option value="mixed" @selected($type === 'mixed')>Mixed</option>
        </select>
      </div>
      <div class="field">
        <label for="assessment-status">Status</label>
        <select id="assessment-status" class="select" name="status">
          <option value="all" @selected($status === 'all')>All</option>
          <option value="active" @selected($status === 'active')>Published</option>
          <option value="inactive" @selected($status === 'inactive')>Inactive</option>
        </select>
      </div>
      <button class="btn" type="submit">Apply</button>
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
            <th>Year level</th>
            <th>Type</th>
            <th>Questions</th>
            <th>Status</th>
            <th>Used by</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse($assessments as $assessment)
            <tr>
              <td data-label="Assessment">
                <strong>{{ $assessment->title }}</strong>
                @if($assessment->topic_title && $assessment->topic_title !== $assessment->title)
                  <div class="dim">{{ $assessment->topic_title }}, module {{ $assessment->module_no }}</div>
                @else
                  <div class="dim">Module {{ $assessment->module_no }}</div>
                @endif
              </td>
              <td data-label="Year level">{{ $assessment->year_level }}</td>
              <td data-label="Type">{{ $assessment->assignment_type === 'mcq' ? 'Multiple choice' : ($assessment->assignment_type === 'fill_blank' ? 'Fill in the blanks' : 'Mixed') }}</td>
              <td data-label="Questions">{{ number_format($assessment->questions_count) }}<div class="dim">{{ number_format($assessment->total_points) }} points</div></td>
              <td data-label="Status">{{ $assessment->is_active ? 'Published' : 'Inactive' }}</td>
              <td data-label="Used by">{{ $assessment->class_assignments_count > 0 ? $assessment->class_assignments_count.' '.($assessment->class_assignments_count === 1 ? 'class assignment' : 'class assignments') : 'Not used yet' }}</td>
              <td>
                <div class="action-row">
                  <a class="btn small secondary" href="{{ route('admin.assessments.show', $assessment) }}">View</a>
                  <a class="btn small secondary" href="{{ route('admin.assessments.edit', $assessment) }}">Edit</a>
                  <form method="POST" action="{{ route('admin.assessments.status', $assessment) }}">
                    @csrf
                    @method('PATCH')
                    <button class="btn small {{ $assessment->is_active ? 'secondary' : '' }}" type="submit">{{ $assessment->is_active ? 'Deactivate' : 'Publish' }}</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="empty-cell">No assessments match these filters.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="pagination">{{ $assessments->links('vendor.pagination.admin') }}</div>
  </section>
@endsection
