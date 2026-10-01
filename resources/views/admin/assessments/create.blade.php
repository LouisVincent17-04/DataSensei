@extends('admin.layout')

@section('title', 'Create Assessment')
@section('page_title', 'Create Assessment')
@section('page_subtitle', 'Write a reusable assessment that instructors can give to their classes. It stays inactive until you publish it.')

@section('content')
  @include('admin.assessments._form', [
    'formAction' => route('admin.assessments.store'),
    'formMethod' => 'POST',
    'submitLabel' => 'Create assessment',
    'cancelUrl' => route('admin.assessments.index'),
    'hasReferences' => false,
  ])
@endsection
