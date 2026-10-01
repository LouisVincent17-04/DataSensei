@extends('admin.layout')

@section('title', $module->title)
@section('page_title', $module->title)
@section('page_subtitle', 'A module version in the Instructor Module Library, used by classes only. Review it, preview it as students see it, publish it, or create a new version.')

@section('content')
  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">{{ $module->version_name }}</h2>
        <p class="panel-subtitle">Module {{ $module->module_no }}, version {{ $module->version_no }}</p>
      </div>
      <div class="action-row">
        <span class="dim">{{ $module->is_active ? 'Published' : 'Draft' }}</span>
        <a class="btn small secondary" href="{{ route('admin.module-library.preview', $module) }}" target="_blank" rel="noopener">Preview Module</a>
        <a class="btn small" href="{{ route('admin.module-library.edit', $module) }}">Edit</a>
        <form method="POST" action="{{ route('admin.module-library.status', $module) }}">
          @csrf
          @method('PATCH')
          <button class="btn small {{ $module->is_active ? 'secondary' : 'green' }}" type="submit">{{ $module->is_active ? 'Unpublish' : 'Publish' }}</button>
        </form>
      </div>
    </div>
    <div class="panel-body">
      <div class="grid-2">
        <div>
          @if(trim((string) $module->year_level) !== '')
            <p><strong>Curriculum year:</strong> {{ $module->year_level }}</p>
          @endif
          <p><strong>Estimated duration:</strong> {{ number_format($module->estimated_minutes) }} minutes</p>
          <p><strong>Sort order:</strong> {{ number_format($module->sort_order) }}</p>
        </div>
        <div>
          <p><strong>Content sections:</strong> {{ count(is_array($module->content_sections) ? $module->content_sections : []) }}</p>
          <p><strong>Review questions:</strong> {{ count(is_array($module->mcq_questions) ? $module->mcq_questions : []) }}</p>
          <p><strong>Class references:</strong> {{ number_format($module->class_assignments_count) }}</p>
        </div>
      </div>
      @if($module->description)
        <p style="margin-top:16px;color:var(--muted);line-height:1.65">{{ $module->description }}</p>
      @endif
    </div>
  </section>

  <section class="panel">
    <div class="panel-head"><div class="panel-heading"><h2 class="panel-title">Create a New Version</h2><p class="panel-subtitle">Copies all current sections and embedded review questions into a separate editable version.</p></div></div>
    <form class="panel-body" method="POST" action="{{ route('admin.module-library.duplicate', $module) }}">
      @csrf
      <div class="form-grid three">
        <div class="field"><label for="duplicate-version-no">Version Number</label><input id="duplicate-version-no" class="input" type="number" name="version_no" min="1" value="{{ $nextVersionNo }}" required></div>
        <div class="field"><label for="duplicate-version-name">Version Name</label><input id="duplicate-version-name" class="input" name="version_name" value="Version {{ $nextVersionNo }}" required></div>
        <div class="field"><label for="duplicate-status">Initial Status</label><select id="duplicate-status" class="select" name="is_active"><option value="0">Draft</option><option value="1">Published</option></select></div>
      </div>
      <div class="action-row" style="margin-top:16px"><button class="btn" type="submit">Duplicate Version</button></div>
    </form>
  </section>

  <section class="panel">
    <div class="panel-head"><div class="panel-heading"><h2 class="panel-title">What Students Will Learn</h2><p class="panel-subtitle">The module's learning outcomes, shown to students at the start of the module. A version needs at least one to be published.</p></div></div>
    <div class="panel-body">
      @if(count($module->learning_outcomes) > 0)
        <ol class="outcome-summary">
          @foreach($module->learning_outcomes as $outcome)
            <li>{{ $outcome }}</li>
          @endforeach
        </ol>
      @else
        <p class="dim">No learning outcomes yet. Add them in the editor under What Students Will Learn.</p>
      @endif
    </div>
  </section>

  <section class="panel">
    <div class="panel-head"><div class="panel-heading"><h2 class="panel-title">Learning Content</h2><p class="panel-subtitle">{{ count($module->content_sections) }} sections and {{ count($module->mcq_questions) }} embedded review questions. Open the editor to change them, or Preview Module to read them as students do.</p></div></div>
    <div class="panel-body">
      @if(count($module->content_sections) > 0)
        <ol class="outcome-summary">
          @foreach($module->content_sections as $section)
            <li>{{ is_array($section) ? ($section['heading'] ?? 'Untitled section') : 'Untitled section' }}</li>
          @endforeach
        </ol>
      @else
        <p class="dim">No sections yet.</p>
      @endif
    </div>
  </section>

  <section class="panel">
    <div class="panel-head"><div class="panel-heading"><h2 class="panel-title">Delete Version</h2><p class="panel-subtitle">Deletion is allowed only when the version is not assigned to any class.</p></div></div>
    <div class="panel-body">
      @if($hasReferences)
        <p class="dim">This version has class references and cannot be deleted. Unpublish it if it should no longer be offered.</p>
      @else
        <form method="POST" action="{{ route('admin.module-library.destroy', $module) }}" onsubmit="return confirm('Permanently delete this module version?');">
          @csrf
          @method('DELETE')
          <button class="btn danger" type="submit">Delete Module Version</button>
        </form>
      @endif
    </div>
  </section>
@endsection

@push('head')
<style>
  .outcome-summary { margin:0; padding-left:20px; color:var(--ds-text-secondary); font-size:.875rem; line-height:1.6; }
  .outcome-summary li + li { margin-top:4px; }
</style>
@endpush
