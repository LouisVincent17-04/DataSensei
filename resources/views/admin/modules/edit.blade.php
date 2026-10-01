@extends('admin.layout')

@section('title', 'Edit DataSensei Module')
@section('page_title', 'Edit DataSensei Module')
@section('page_subtitle', 'A public DataSensei Module, open to every learner by year level. Edit its details, learning outcomes, sections and review questions, then save or publish. The challenges created for it are not affected.')

@section('content')
  @include('admin.partials.module-editor', [
    'editorMode' => 'public',
    'formAction' => route('admin.modules.update', $module),
    'formMethod' => 'PUT',
    'cancelUrl' => route('admin.modules.index'),
    'basicPartial' => 'admin.modules._form',
    'isPublished' => (bool) ($module->is_published ?? true),
    'outcomes' => $module->learning_outcomes,
    'previewUrl' => route('admin.modules.preview-draft'),
    'sectionPreviewUrl' => route('admin.lessons.preview'),
    'contentLocked' => false,
    'lockedNotice' => null,
    'audience' => 'A published DataSensei Module is shown to every learner on the Modules page under its year level, and unlocks in curriculum order. Unpublishing hides it from students; their progress is kept.',
  ])

  <section class="panel" style="margin-top:24px">
    <div class="panel-head">
      @if($module->isCore())
        <div class="panel-heading">
          <h2 class="panel-title">Core / System Module</h2>
          <p class="panel-subtitle">This is one of the 24 Core Modules ({{ $module->module_key }}). Its title and identity are fixed and it cannot be deleted; the core certificates depend on it. You can still edit its content here, and publish or unpublish it: unpublishing hides it from learners and keeps their progress and certificates.</p>
        </div>
        <div class="action-row">
          <button class="btn small danger" type="button" disabled title="Core Modules cannot be deleted" style="opacity:.45;cursor:not-allowed">Delete module</button>
        </div>
      @elseif($dependents !== [])
        <div class="panel-heading">
          <h2 class="panel-title">Archive module</h2>
          <p class="panel-subtitle">This Custom module is in use ({{ implode(', ', $dependents) }}), so it cannot be deleted. Archive it to hide it from learners; everything that depends on it is kept, and it can be restored later.</p>
        </div>
        <div class="action-row">
          @if($module->isArchived())
            <form method="POST" action="{{ route('admin.modules.restore', $module) }}">
              @csrf
              @method('PATCH')
              <button class="btn small secondary" type="submit">Restore module</button>
            </form>
          @else
            <form method="POST" action="{{ route('admin.modules.archive', $module) }}" onsubmit="return confirm('Archive this module? Learners will no longer see it; their progress is kept.');">
              @csrf
              @method('PATCH')
              <button class="btn small danger" type="submit">Archive module</button>
            </form>
          @endif
        </div>
      @else
        <div class="panel-heading">
          <h2 class="panel-title">Delete module</h2>
          <p class="panel-subtitle">{{ $module->lessons_count }} {{ $module->lessons_count === 1 ? 'section' : 'sections' }}, {{ $module->challenges_count }} linked {{ $module->challenges_count === 1 ? 'challenge' : 'challenges' }}. A Custom module can only be deleted while nothing depends on it (no learner progress, challenge attempts or class assignments); its linked challenges are kept. To hide it instead, unpublish it.</p>
        </div>
        <div class="action-row">
          <form method="POST" action="{{ route('admin.modules.destroy', $module) }}" onsubmit="return confirm('Delete this module and all of its sections?');">
            @csrf
            @method('DELETE')
            <button class="btn small danger" type="submit">Delete module</button>
          </form>
        </div>
      @endif
    </div>
  </section>
@endsection
