@extends('admin.layout')

@section('title', 'Edit MCQ Challenge')
@section('eyebrow', 'Platform Content')
@section('page_title', 'Edit MCQ Challenge')
@section('page_subtitle', 'Update this challenge version without affecting coding challenges or completed attempt history.')

@section('content')
  @include('admin.challenges._form', [
    'formAction' => route('admin.challenges.update', $challenge),
    'formMethod' => 'PUT',
    'submitLabel' => 'Save MCQ Challenge',
    'cancelUrl' => route('admin.challenges.show', $challenge),
  ])
@endsection
