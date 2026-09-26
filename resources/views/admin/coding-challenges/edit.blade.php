@extends('admin.layout')

@section('title', 'Edit Coding Challenge')
@section('page_title', 'Edit Coding Challenge')
@section('page_subtitle', 'Update this coding challenge version. Problems and test cases are frozen once learners have submission history.')

@section('content')
  @include('admin.coding-challenges._form', [
    'formAction' => route('admin.coding-challenges.update', $challenge),
    'formMethod' => 'PUT',
    'submitLabel' => 'Save Coding Challenge',
    'cancelUrl' => route('admin.coding-challenges.show', $challenge),
  ])
@endsection
