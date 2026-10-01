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
      <div class="panel-heading">
        <h2 class="panel-title">Delete module</h2>
        <p class="panel-subtitle">{{ $module->lessons_count }} {{ $module->lessons_count === 1 ? 'section' : 'sections' }}, {{ $module->challenges_count }} linked {{ $module->challenges_count === 1 ? 'challenge' : 'challenges' }}. A module can only be deleted while no student has progress in it; its linked challenges are kept. To hide it instead, unpublish it.</p>
      </div>
      <div class="action-row">
        <form method="POST" action="{{ route('admin.modules.destroy', $module) }}" onsubmit="return confirm('Delete this module and all of its sections?');">
          @csrf
          @method('DELETE')
          <button class="btn small danger" type="submit">Delete module</button>
        </form>
      </div>
    </div>
  </section>
@endsection
