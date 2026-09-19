@extends('admin.layout')

@section('title', 'Create MCQ Challenge')
@section('page_title', 'Create MCQ Challenge')
@section('page_subtitle', 'Create a versioned multiple-choice challenge. New challenges are inactive by default until reviewed and published.')

@section('content')
  @include('admin.challenges._form', [
    'formAction' => route('admin.challenges.store'),
    'formMethod' => 'POST',
    'submitLabel' => 'Create MCQ Challenge',
    'cancelUrl' => route('admin.challenges.index'),
    'hasHistory' => false,
  ])
@endsection
