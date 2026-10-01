@extends('admin.layout')

@section('title', 'Create Library Module')
@section('page_title', 'Create Library Module')
@section('page_subtitle', 'A module version for the Instructor Module Library. Instructors assign it to their classes; only students of those classes can open it. It stays a draft until you publish it.')

@section('content')
  @include('admin.partials.module-editor', [
    'editorMode' => 'library',
    'formAction' => route('admin.module-library.store'),
    'formMethod' => 'POST',
    'cancelUrl' => route('admin.module-library.index'),
    'basicPartial' => 'admin.module-library._form',
    'isPublished' => false,
    'outcomes' => $module->learning_outcomes,
    'previewUrl' => route('admin.module-library.preview-draft'),
    'contentLocked' => false,
    'lockedNotice' => null,
    'audience' => 'A published version appears in every instructor\'s module library, ready to assign to a class. Students only see it through a class it is assigned to. A draft is visible to admins only.',
    'hasReferences' => false,
  ])
@endsection
