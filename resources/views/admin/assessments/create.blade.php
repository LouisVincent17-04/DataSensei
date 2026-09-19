@extends('admin.layout')

@section('title', 'Create Assessment Content')
@section('page_title', 'Create Assessment Content')
@section('page_subtitle', 'Create a reusable assessment template. New versions are inactive by default until reviewed and published.')

@section('content')
  @include('admin.assessments._form', [
    'formAction' => route('admin.assessments.store'),
    'formMethod' => 'POST',
    'submitLabel' => 'Create Assessment Content',
    'cancelUrl' => route('admin.assessments.index'),
    'hasReferences' => false,
  ])
@endsection
