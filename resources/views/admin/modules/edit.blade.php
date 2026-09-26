@extends('admin.layout')

@section('title', 'Edit Module')
@section('page_title', 'Edit Module')
@section('page_subtitle', 'Change the module\'s details. Its lessons and the challenges created for it are not affected.')

@section('content')
  @include('admin.modules._form', [
    'formAction' => route('admin.modules.update', $module),
    'formMethod' => 'PUT',
    'submitLabel' => 'Save module',
    'cancelUrl' => route('admin.modules.index'),
  ])

  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Content and removal</h2>
        <p class="panel-subtitle">{{ $module->lessons_count }} {{ $module->lessons_count === 1 ? 'lesson' : 'lessons' }}, {{ $module->challenges_count }} linked {{ $module->challenges_count === 1 ? 'challenge' : 'challenges' }}. A module can only be deleted while no student has progress in it; its linked challenges are kept.</p>
      </div>
      <div class="action-row">
        <a class="btn small secondary" href="{{ route('admin.modules.lessons.index', $module) }}">Manage lessons</a>
        <form method="POST" action="{{ route('admin.modules.destroy', $module) }}" onsubmit="return confirm('Delete this module and all of its lessons?');">
          @csrf
          @method('DELETE')
          <button class="btn small danger" type="submit">Delete module</button>
        </form>
      </div>
    </div>
  </section>
@endsection
