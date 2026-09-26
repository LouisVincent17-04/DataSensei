@extends('admin.layout')

@section('title', 'Add Module')
@section('page_title', 'Add Module')
@section('page_subtitle', 'The new module is placed last in the curriculum. It also gets one locked MCQ challenge on every challenge level, ready for questions.')

@section('content')
  @include('admin.modules._form', [
    'formAction' => route('admin.modules.store'),
    'formMethod' => 'POST',
    'submitLabel' => 'Create module',
    'cancelUrl' => route('admin.modules.index'),
  ])
@endsection
