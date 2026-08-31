@extends('admin.layout')

@section('title', $module->title)
@section('eyebrow', 'Learning Module')
@section('page_title', $module->title)
@section('page_subtitle', 'Review module metadata, content, version history controls, and publication status.')

@section('content')
  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">{{ $module->version_name }}</h2>
        <p class="panel-subtitle">Module {{ $module->module_no }} · {{ $module->module_code }} · {{ $module->version_code }}</p>
      </div>
      <div class="action-row">
        <span class="badge {{ $module->is_active ? 'active' : 'disabled' }}">{{ $module->is_active ? 'Published' : 'Inactive' }}</span>
        <a class="btn small" href="{{ route('admin.module-library.edit', $module) }}">Edit</a>
        <form method="POST" action="{{ route('admin.module-library.status', $module) }}">
          @csrf
          @method('PATCH')
          <button class="btn small {{ $module->is_active ? 'secondary' : 'green' }}" type="submit">{{ $module->is_active ? 'Deactivate' : 'Publish' }}</button>
        </form>
      </div>
    </div>
    <div class="panel-body">
      <div class="grid-2">
        <div>
          <p><strong>Year level:</strong> {{ $module->year_level }}</p>
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

  <section class="panel" style="margin-top:24px">
    <div class="panel-head"><div class="panel-heading"><h2 class="panel-title">Create a New Version</h2><p class="panel-subtitle">Copies all current sections and embedded review questions into a separate editable version.</p></div></div>
    <form class="panel-body" method="POST" action="{{ route('admin.module-library.duplicate', $module) }}">
      @csrf
      <div class="form-grid three">
        <div class="field"><label for="duplicate-module-code">New Module Code</label><input id="duplicate-module-code" class="input" name="module_code" value="{{ $module->module_code }}-V{{ $nextVersionNo }}" required></div>
        <div class="field"><label for="duplicate-version-no">Version Number</label><input id="duplicate-version-no" class="input" type="number" name="version_no" min="1" value="{{ $nextVersionNo }}" required></div>
        <div class="field"><label for="duplicate-version-code">Version Code</label><input id="duplicate-version-code" class="input" name="version_code" value="V{{ $nextVersionNo }}" required></div>
        <div class="field"><label for="duplicate-version-name">Version Name</label><input id="duplicate-version-name" class="input" name="version_name" value="Version {{ $nextVersionNo }}" required></div>
        <div class="field"><label for="duplicate-status">Initial Status</label><select id="duplicate-status" class="select" name="is_active"><option value="0">Inactive / Draft</option><option value="1">Published</option></select></div>
      </div>
      <div class="action-row" style="margin-top:14px"><button class="btn" type="submit">Duplicate Version</button></div>
    </form>
  </section>

  <section class="panel" style="margin-top:24px">
    <div class="panel-head"><div class="panel-heading"><h2 class="panel-title">Learning Sections JSON</h2><p class="panel-subtitle">Read-only formatted view of the stored seeded-compatible structure.</p></div></div>
    <div class="panel-body"><pre class="json-preview">{{ $contentSectionsJson }}</pre></div>
  </section>

  <section class="panel" style="margin-top:24px">
    <div class="panel-head"><div class="panel-heading"><h2 class="panel-title">Review Questions JSON</h2><p class="panel-subtitle">Read-only formatted view of the embedded instructional review questions.</p></div></div>
    <div class="panel-body"><pre class="json-preview">{{ $mcqQuestionsJson }}</pre></div>
  </section>

  <section class="panel" style="margin-top:24px">
    <div class="panel-head"><div class="panel-heading"><h2 class="panel-title">Delete Version</h2><p class="panel-subtitle">Deletion is allowed only when the version is not assigned to any class.</p></div></div>
    <div class="panel-body">
      @if($hasReferences)
        <p class="dim">This version has class references and cannot be deleted. Deactivate it if it should no longer be offered.</p>
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
  .json-preview { max-height:520px; overflow:auto; padding:16px; border:1px solid var(--border); border-radius:var(--radius); background:var(--bg); color:#cbd5e1; font:12px/1.55 Consolas, Monaco, monospace; white-space:pre-wrap; overflow-wrap:anywhere; }
</style>
@endpush
