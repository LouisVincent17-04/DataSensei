@extends('admin.layout')

@section('title', 'Edit Learning Module')
@section('eyebrow', 'Platform Content')
@section('page_title', 'Edit Learning Module')
@section('page_subtitle', 'Update this module version while preserving class references and seeded content compatibility.')

@section('content')
  @include('admin.module-library._form', [
    'formAction' => route('admin.module-library.update', $module),
    'formMethod' => 'PUT',
    'submitLabel' => 'Save Module Version',
    'cancelUrl' => route('admin.module-library.show', $module),
  ])
@endsection
