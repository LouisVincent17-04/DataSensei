@extends('admin.layout')

@section('title', 'Create Learning Module')
@section('eyebrow', 'Platform Content')
@section('page_title', 'Create Learning Module')
@section('page_subtitle', 'Create a seeded-compatible module version. New content is inactive by default until it is reviewed and published.')

@section('content')
  @include('admin.module-library._form', [
    'formAction' => route('admin.module-library.store'),
    'formMethod' => 'POST',
    'submitLabel' => 'Create Module Version',
    'cancelUrl' => route('admin.module-library.index'),
    'hasReferences' => false,
  ])
@endsection
