@extends('admin.layout')

@php
  $isNew = ! $lesson->exists;
  $pageTitle = $isNew ? 'Add Lesson' : 'Edit Lesson';
@endphp

@section('title', $pageTitle.': '.$module->title)
@section('page_title', $pageTitle)
@section('page_subtitle', ($isNew ? 'A new lesson at the end of ' : 'Editing a lesson in ').$module->title.'. Build the lesson from blocks on the left; the preview on the right shows it exactly as students will read it.')

@section('content')
  @include('admin.modules.lessons._editor')
@endsection
