@extends('admin.layout')

@section('title', 'DataSensei Modules')
@section('page_title', 'DataSensei Modules')
@section('page_subtitle', 'The public DataSensei curriculum: open to every learner with or without a class, grouped by year level and unlocked in the order shown here. Modules instructors assign to their classes are managed separately in the Instructor Module Library.')

@section('content')
  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Modules</h2>
        <p class="panel-subtitle">{{ $modules->count() }} {{ $modules->count() === 1 ? 'module' : 'modules' }} ({{ $modules->filter->isCore()->count() }} core, {{ $modules->reject->isCore()->count() }} custom) across {{ count(array_filter($groups)) }} {{ count(array_filter($groups)) === 1 ? 'year level' : 'year levels' }}. Students unlock published modules in the order shown here; drafts are skipped. A new module also gets one locked challenge on each of the {{ $levelCount }} challenge {{ $levelCount === 1 ? 'level' : 'levels' }}. Core Modules keep their title and cannot be deleted; new modules are Custom modules.</p>
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
                  <th>Content</th>
                  <th>Status</th>
                  <th>XP</th>
                  <th>Type</th>
                  <th>Module set</th>
                  <th style="width:330px">Actions</th>
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
                    <td>
                      {{ $module->lessons_count }} {{ $module->lessons_count === 1 ? 'section' : 'sections' }}
                      <div class="dim">{{ count($module->learning_outcomes) }} learning {{ count($module->learning_outcomes) === 1 ? 'outcome' : 'outcomes' }}</div>
                    </td>
                    <td>
                      @if($module->isArchived())
                        Archived
                        <div class="dim">Hidden; progress kept</div>
                      @else
                        {{ ($module->is_published ?? true) ? 'Published' : 'Draft' }}
                      @endif
                    </td>
                    <td>{{ $module->xp_reward }}</td>
                    <td>{{ $module->is_boss ? 'Boss module' : 'Standard' }}@if($module->has_coding_exercises)<div class="dim">Coding exercises</div>@endif</td>
                    <td>
                      {{ $module->typeLabel() }}
                      @if($module->isCore())<div class="dim" title="Title locked; cannot be deleted">Protected</div>@endif
                    </td>
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
                        <a class="btn small secondary" href="{{ route('admin.modules.preview', $module) }}" target="_blank" rel="noopener">Preview</a>
                        <a class="btn small" href="{{ route('admin.modules.edit', $module) }}">Edit</a>
                        @if($module->isArchived())
                          <form method="POST" action="{{ route('admin.modules.restore', $module) }}">
                            @csrf
                            @method('PATCH')
                            <button class="btn small secondary" type="submit">Restore</button>
                          </form>
                        @else
                          <form method="POST" action="{{ route('admin.modules.status', $module) }}">
                            @csrf
                            @method('PATCH')
                            <button class="btn small {{ ($module->is_published ?? true) ? 'secondary' : 'green' }}" type="submit">{{ ($module->is_published ?? true) ? 'Unpublish' : 'Publish' }}</button>
                          </form>
                        @endif
                        @if($module->isCore())
                          <button class="btn small danger" type="button" disabled title="Core Modules cannot be deleted" aria-label="{{ $module->title }} is a Core Module and cannot be deleted">Delete</button>
                        @endif
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
  .module-group td .action-row { flex-wrap:nowrap; }
  .action-row .btn[disabled] { opacity:.45; cursor:not-allowed; }
</style>
@endpush
