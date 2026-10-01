@extends('admin.layout')

@section('title', 'Add DataSensei Module')
@section('page_title', 'Add DataSensei Module')
@section('page_subtitle', 'A public DataSensei Module, open to every learner with or without a class. It is placed last in the curriculum, gets one locked MCQ challenge on every challenge level, and stays a draft until you publish it.')

@section('content')
  @include('admin.partials.module-editor', [
    'editorMode' => 'public',
    'formAction' => route('admin.modules.store'),
    'formMethod' => 'POST',
    'cancelUrl' => route('admin.modules.index'),
    'basicPartial' => 'admin.modules._form',
    'isPublished' => false,
    'outcomes' => $module->learning_outcomes,
    'previewUrl' => route('admin.modules.preview-draft'),
    'sectionPreviewUrl' => route('admin.lessons.preview'),
    'contentLocked' => false,
    'lockedNotice' => null,
    'audience' => 'A published DataSensei Module is shown to every learner on the Modules page under its year level, and unlocks in curriculum order. A draft is visible to admins only.',
  ])
@endsection
