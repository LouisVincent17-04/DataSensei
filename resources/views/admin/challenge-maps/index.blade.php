@extends('admin.layout')

@section('title', 'Challenge Maps')
@section('page_title', 'Challenge Maps')
@section('page_subtitle', 'Every challenge level with its MCQ and coding challenges. Edit the level text, set its position, and arrange the challenges learners meet on each map.')

@section('content')
  @if($maps->isEmpty())
    <section class="panel">
      <div class="panel-body"><p class="dim">No challenge levels exist yet.</p></div>
    </section>
  @endif

  @foreach($maps as $map)
    @php
      $category = $map['category'];
      $editing = $errors->any() && (int) old('_category_id') === (int) $category->id;
    @endphp
    <section class="panel challenge-map" id="map-{{ $category->id }}">
      <div class="panel-head">
        <div class="panel-heading">
          <h2 class="panel-title">{{ $category->name }}</h2>
          <p class="panel-subtitle">Slug {{ $category->slug }} (fixed, used in learner URLs). Position {{ $category->order_index }}. {{ $map['mcq']->count() }} MCQ, {{ $map['coding']->count() }} coding.</p>
        </div>
      </div>

      <form class="panel-body challenge-map-form" method="POST" action="{{ route('admin.challenge-maps.update', $category) }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="_category_id" value="{{ $category->id }}">
        <div class="form-grid three">
          <div class="field">
            <label for="map-name-{{ $category->id }}">Level Name</label>
            <input id="map-name-{{ $category->id }}" class="input" name="name" maxlength="189" value="{{ $editing ? old('name') : $category->name }}" required>
          </div>
          <div class="field">
            <label for="map-audience-{{ $category->id }}">Target Audience</label>
            <input id="map-audience-{{ $category->id }}" class="input" name="target_audience" maxlength="189" value="{{ $editing ? old('target_audience') : $category->target_audience }}">
          </div>
          <div class="field">
            <label for="map-order-{{ $category->id }}">Position</label>
            <input id="map-order-{{ $category->id }}" class="input" type="number" name="order_index" min="0" max="1000" value="{{ $editing ? old('order_index') : $category->order_index }}" required>
          </div>
        </div>
        <div class="field" style="margin-top:12px">
          <label for="map-description-{{ $category->id }}">Description</label>
          <textarea id="map-description-{{ $category->id }}" class="textarea challenge-map-description" name="description">{{ $editing ? old('description') : $category->description }}</textarea>
        </div>
        <div class="action-row" style="margin-top:12px">
          <button class="btn small" type="submit">Save Level</button>
        </div>
      </form>

      @foreach(['mcq' => 'MCQ Challenges', 'coding' => 'Coding Challenges'] as $type => $label)
        @php
          $list = $map[$type];
          $ids = $list->pluck('id')->map(fn ($id) => (int) $id)->values();
        @endphp
        <div class="challenge-map-list">
          <div class="challenge-map-list-head">
            <strong>{{ $label }}</strong>
            @if($type === 'mcq')
              <a class="btn small secondary" href="{{ route('admin.challenges.create') }}">Create MCQ Challenge</a>
            @else
              <a class="btn small secondary" href="{{ route('admin.coding-challenges.create') }}">Create Coding Challenge</a>
            @endif
          </div>
          @if($list->isEmpty())
            <p class="dim challenge-map-empty">No {{ strtolower($label) }} in this level yet.</p>
          @else
            <div class="table-wrap">
              <table class="compact-table">
                <thead>
                  <tr>
                    <th>Order</th>
                    <th>Challenge</th>
                    <th>Module</th>
                    <th>Version</th>
                    <th>Questions</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($list as $index => $challenge)
                    @php
                      $editUrl = $type === 'mcq'
                        ? route('admin.challenges.edit', $challenge)
                        : route('admin.coding-challenges.edit', $challenge);
                      $statusUrl = $type === 'mcq'
                        ? route('admin.challenges.status', $challenge)
                        : route('admin.coding-challenges.status', $challenge);
                      $questionCount = $type === 'mcq' ? $challenge->questions_count : $challenge->coding_questions_count;
                      $upOrder = $ids->all();
                      $downOrder = $ids->all();
                      if ($index > 0) { [$upOrder[$index - 1], $upOrder[$index]] = [$upOrder[$index], $upOrder[$index - 1]]; }
                      if ($index < count($downOrder) - 1) { [$downOrder[$index], $downOrder[$index + 1]] = [$downOrder[$index + 1], $downOrder[$index]]; }
                    @endphp
                    <tr>
                      <td>
                        <div class="challenge-map-order">
                          <span class="dim">{{ $challenge->order_index }}</span>
                          <form method="POST" action="{{ route('admin.challenge-maps.reorder', $category) }}">
                            @csrf
                            <input type="hidden" name="type" value="{{ $type }}">
                            @foreach($upOrder as $id)
                              <input type="hidden" name="order[]" value="{{ $id }}">
                            @endforeach
                            <button class="btn small secondary" type="submit" aria-label="Move up" @disabled($index === 0)>↑</button>
                          </form>
                          <form method="POST" action="{{ route('admin.challenge-maps.reorder', $category) }}">
                            @csrf
                            <input type="hidden" name="type" value="{{ $type }}">
                            @foreach($downOrder as $id)
                              <input type="hidden" name="order[]" value="{{ $id }}">
                            @endforeach
                            <button class="btn small secondary" type="submit" aria-label="Move down" @disabled($index === $list->count() - 1)>↓</button>
                          </form>
                        </div>
                      </td>
                      <td>
                        <strong>{{ $challenge->title }}</strong>
                        <div class="dim">{{ $challenge->content_code }}</div>
                      </td>
                      <td>{{ $challenge->module?->title ?? '—' }}</td>
                      <td>{{ $challenge->version_name }} ({{ $challenge->version_code }})</td>
                      <td>{{ number_format((int) $questionCount) }}</td>
                      <td>{{ $challenge->is_active ? 'Available' : 'Unavailable' }}</td>
                      <td>
                        <div class="action-row">
                          <a class="btn small secondary" href="{{ $editUrl }}">Edit</a>
                          <form method="POST" action="{{ $statusUrl }}">
                            @csrf
                            @method('PATCH')
                            <button class="btn small {{ $challenge->is_active ? 'secondary' : 'green' }}" type="submit">{{ $challenge->is_active ? 'Make unavailable' : 'Make available' }}</button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      @endforeach
    </section>
  @endforeach
@endsection

@push('head')
<style>
  .challenge-map { margin-bottom:24px; }
  .challenge-map-form { border-bottom:1px solid var(--border); }
  .challenge-map-description { min-height:72px; }
  .challenge-map-list { border-bottom:1px solid var(--border); }
  .challenge-map-list:last-child { border-bottom:0; }
  .challenge-map-list-head { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; padding:12px 20px; }
  .challenge-map-list-head strong { color:var(--text); font-size:.875rem; }
  .challenge-map-empty { padding:0 20px 16px; font-size:.875rem; }
  .challenge-map-order { display:flex; align-items:center; gap:6px; }
  .challenge-map-order form { display:inline; }
  .challenge-map-order .btn { min-width:32px; padding:0 8px; }
</style>
@endpush
