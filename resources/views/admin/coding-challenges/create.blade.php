@extends('admin.layout')

@section('title', 'Create Coding Challenge')
@section('page_title', 'Create Coding Challenge')
@section('page_subtitle', 'Create a versioned coding challenge with one or more problems and their test cases. New challenges are unavailable until published.')

@section('content')
  @include('admin.coding-challenges._form', [
    'formAction' => route('admin.coding-challenges.store'),
    'formMethod' => 'POST',
    'submitLabel' => 'Create Coding Challenge',
    'cancelUrl' => route('admin.coding-challenges.index'),
    'hasHistory' => false,
  ])
@endsection
