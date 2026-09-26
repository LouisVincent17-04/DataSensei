@extends('admin.layout')

@section('title', 'Lessons: '.$module->title)
@section('page_title', $module->title)
@section('page_subtitle', 'The lessons students read in this module, in order. Open one to edit its content with the block editor and live preview.')

@section('content')
  @php
    $ordered = $rows->pluck('lesson.id')->values()->all();
  @endphp

  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Lessons</h2>
        <p class="panel-subtitle">{{ $rows->count() }} {{ $rows->count() === 1 ? 'lesson' : 'lessons' }} in {{ $module->year_level }}. Lessons marked "original content" still carry their hand-written HTML; open one to edit it or build around it.</p>
      </div>
      <div class="action-row">
        <a class="btn small secondary" href="{{ route('admin.modules.index') }}">All modules</a>
        <a class="btn small secondary" href="{{ route('admin.modules.edit', $module) }}">Module details</a>
        <a class="btn small" href="{{ route('admin.modules.lessons.create', $module) }}">Add lesson</a>
      </div>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th style="width:56px">Order</th>
            <th>Lesson</th>
            <th>Content</th>
            <th>Student progress</th>
            <th style="width:260px">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($rows as $index => $row)
            @php
              $lesson = $row['lesson'];
              $prevId = $index > 0 ? $ordered[$index - 1] : null;
              $nextId = $index < count($ordered) - 1 ? $ordered[$index + 1] : null;
            @endphp
            <tr>
              <td class="dim">{{ $lesson->order_index }}</td>
              <td><strong>{{ $lesson->title }}</strong></td>
              <td>
                @if($row['legacy'])
                  Original content
                @else
                  {{ $row['blockCount'] }} {{ $row['blockCount'] === 1 ? 'block' : 'blocks' }}
                @endif
              </td>
              <td>{{ $row['progress'] > 0 ? $row['progress'].' '.($row['progress'] === 1 ? 'student' : 'students') : 'None yet' }}</td>
              <td>
                <div class="action-row">
                  @if($prevId !== null)
                    <form method="POST" action="{{ route('admin.modules.lessons.reorder', $module) }}">
                      @csrf
                      @foreach($ordered as $id)
                        @php
                          $swapped = $id === $lesson->id ? $prevId : ($id === $prevId ? $lesson->id : $id);
                        @endphp
                        <input type="hidden" name="order[]" value="{{ $swapped }}">
                      @endforeach
                      <button class="btn small secondary" type="submit" title="Move up" aria-label="Move {{ $lesson->title }} up">↑</button>
                    </form>
                  @endif
                  @if($nextId !== null)
                    <form method="POST" action="{{ route('admin.modules.lessons.reorder', $module) }}">
                      @csrf
                      @foreach($ordered as $id)
                        @php
                          $swapped = $id === $lesson->id ? $nextId : ($id === $nextId ? $lesson->id : $id);
                        @endphp
                        <input type="hidden" name="order[]" value="{{ $swapped }}">
                      @endforeach
                      <button class="btn small secondary" type="submit" title="Move down" aria-label="Move {{ $lesson->title }} down">↓</button>
                    </form>
                  @endif
                  <a class="btn small" href="{{ route('admin.modules.lessons.edit', [$module, $lesson]) }}">Edit</a>
                  @if($row['progress'] === 0)
                    <form method="POST" action="{{ route('admin.modules.lessons.destroy', [$module, $lesson]) }}" onsubmit="return confirm('Delete this lesson?');">
                      @csrf
                      @method('DELETE')
                      <button class="btn small danger" type="submit">Delete</button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="empty-cell">This module has no lessons yet. Add the first one.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
@endsection

@push('head')
<style>
  .action-row form { display:inline; }
</style>
@endpush
