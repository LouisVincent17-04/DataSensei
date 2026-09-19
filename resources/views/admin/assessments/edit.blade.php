@extends('admin.layout')

@section('title', 'Edit Assessment Content')
@section('page_title', 'Edit Assessment Content')
@section('page_subtitle', 'Update this assessment version while preserving class-assignment and submission integrity.')

@section('content')
  @include('admin.assessments._form', [
    'formAction' => route('admin.assessments.update', $assessment),
    'formMethod' => 'PUT',
    'submitLabel' => 'Save Assessment Content',
    'cancelUrl' => route('admin.assessments.show', $assessment),
  ])
@endsection
