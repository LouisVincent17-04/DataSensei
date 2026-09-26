@extends('admin.layout')

@section('title', 'Module Manager')
@section('page_title', 'Module Manager')
@section('page_subtitle', 'The public curriculum students work through in order. Reorder modules, edit their details, or open one to manage its lessons.')

@section('content')
  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Modules</h2>
        <p class="panel-subtitle">{{ $modules->count() }} {{ $modules->count() === 1 ? 'module' : 'modules' }} across {{ count(array_filter($groups)) }} {{ count(array_filter($groups)) === 1 ? 'year level' : 'year levels' }}. Students unlock them in the order shown here. A new module also gets one locked challenge on each of the {{ $levelCount }} challenge {{ $levelCount === 1 ? 'level' : 'levels' }}.</p>
      </div>
      <div class="action-row">
        <a class="btn small" href="{{ route('admin.modules.create') }}">Add module</a>
      </div>
    </div>

    @php
      $ordered = $modules->pluck('id')->values()->all();
    @endphp

    @foreach($groups as $level => $levelModules)
      @if(count($levelModules))
        <div class="module-group">
          <div class="module-group-title">{{ $level }}</div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th style="width:56px">Order</th>
                  <th>Module</th>
                  <th>Lessons</th>
                  <th>XP</th>
                  <th>Type</th>
                  <th>Coding exercises</th>
                  <th style="width:250px">Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($levelModules as $module)
                  @php
                    $position = array_search($module->id, $ordered, true);
                    $prevId = $position > 0 ? $ordered[$position - 1] : null;
                    $nextId = $position !== false && $position < count($ordered) - 1 ? $ordered[$position + 1] : null;
                  @endphp
                  <tr>
                    <td class="dim">{{ $module->order_index }}</td>
                    <td>
                      <strong>{{ $module->title }}</strong>
                      @if($module->description !== '')
                        <div class="dim module-description">{{ \Illuminate\Support\Str::limit($module->description, 140) }}</div>
                      @endif
                    </td>
                    <td>{{ $module->lessons_count }}</td>
                    <td>{{ $module->xp_reward }}</td>
                    <td>{{ $module->is_boss ? 'Boss module' : 'Standard' }}</td>
                    <td>{{ $module->has_coding_exercises ? 'Yes' : 'No' }}</td>
                    <td>
                      <div class="action-row">
                        @if($prevId !== null)
                          <form method="POST" action="{{ route('admin.modules.reorder') }}">
                            @csrf
                            @foreach($ordered as $id)
                              @php
                                $swapped = $id === $module->id ? $prevId : ($id === $prevId ? $module->id : $id);
                              @endphp
                              <input type="hidden" name="order[]" value="{{ $swapped }}">
                            @endforeach
                            <button class="btn small secondary" type="submit" title="Move up" aria-label="Move {{ $module->title }} up">↑</button>
                          </form>
                        @endif
                        @if($nextId !== null)
                          <form method="POST" action="{{ route('admin.modules.reorder') }}">
                            @csrf
                            @foreach($ordered as $id)
                              @php
                                $swapped = $id === $module->id ? $nextId : ($id === $nextId ? $module->id : $id);
                              @endphp
                              <input type="hidden" name="order[]" value="{{ $swapped }}">
                            @endforeach
                            <button class="btn small secondary" type="submit" title="Move down" aria-label="Move {{ $module->title }} down">↓</button>
                          </form>
                        @endif
                        <a class="btn small secondary" href="{{ route('admin.modules.lessons.index', $module) }}">Lessons</a>
                        <a class="btn small" href="{{ route('admin.modules.edit', $module) }}">Edit</a>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @endif
    @endforeach

    @if($modules->isEmpty())
      <div class="empty-cell">No modules yet. Add the first one to start the curriculum.</div>
    @endif
  </section>
@endsection

@push('head')
<style>
  .module-group + .module-group { border-top:1px solid var(--border); }
  .module-group-title { padding:14px 20px 10px; color:var(--text); font-size:.875rem; font-weight:600; }
  .module-description { margin-top:2px; max-width:60ch; font-size:.8125rem; line-height:1.45; }
  .action-row form { display:inline; }
</style>
@endpush
