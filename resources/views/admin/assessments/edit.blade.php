@extends('admin.layout')

@section('title', 'Edit Assessment')
@section('page_title', 'Edit Assessment')
@section('page_subtitle', 'Change this assessment. Classes that already use it keep their students' submissions.')

@section('content')
  @include('admin.assessments._form', [
    'formAction' => route('admin.assessments.update', $assessment),
    'formMethod' => 'PUT',
    'submitLabel' => 'Save changes',
    'cancelUrl' => route('admin.assessments.show', $assessment),
  ])
@endsection
