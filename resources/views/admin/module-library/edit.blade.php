@extends('admin.layout')

@section('title', 'Edit Library Module')
@section('page_title', 'Edit Library Module')
@section('page_subtitle', 'A module version in the Instructor Module Library, used by classes only. Edit its details, learning outcomes, sections and review questions, then save or publish.')

@section('content')
  @include('admin.partials.module-editor', [
    'editorMode' => 'library',
    'formAction' => route('admin.module-library.update', $module),
    'formMethod' => 'PUT',
    'cancelUrl' => route('admin.module-library.show', $module),
    'basicPartial' => 'admin.module-library._form',
    'isPublished' => (bool) $module->is_active,
    'outcomes' => $module->learning_outcomes,
    'previewUrl' => route('admin.module-library.preview-draft'),
    'contentLocked' => $hasReferences,
    'lockedNotice' => 'This version is assigned to at least one class, so its sections and review questions cannot change here: students may be part way through it. Its details and learning outcomes can still be edited. To change the content, create a new version from the module\'s page and assign that one.',
    'audience' => 'A published version appears in every instructor\'s module library, ready to assign to a class. Students only see it through a class it is assigned to. Unpublishing it stops new assignments; classes that already have it keep it.',
  ])
@endsection
