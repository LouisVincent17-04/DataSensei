@extends('admin.layout')

@section('title', 'Content Management')
@section('eyebrow', 'Platform Content')
@section('page_title', 'Content Management')
@section('page_subtitle', 'Maintain reusable learning modules, challenge categories, and challenge metadata used across DataSensei.')

@section('content')
  <section class="split">
    <div class="panel">
      <h2 class="panel-title">Module Library Search</h2>
      <form class="toolbar" method="GET" action="{{ route('admin.content.index') }}">
        <input class="input" name="module_search" value="{{ request('module_search') }}" placeholder="Search modules, codes, versions">
        <button class="btn" type="submit">Search Modules</button>
      </form>
    </div>
    <div class="panel">
      <h2 class="panel-title">Challenge Search</h2>
      <form class="toolbar" method="GET" action="{{ route('admin.content.index') }}">
        <input class="input" name="challenge_search" value="{{ request('challenge_search') }}" placeholder="Search challenges">
        <button class="btn" type="submit">Search Challenges</button>
      </form>
    </div>
  </section>

  <section class="panel">
    <h2 class="panel-title">Module Library Items</h2>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Module</th><th>Version</th><th>Status</th><th>Update</th></tr></thead>
        <tbody>
          @forelse($modules as $module)
            <tr>
              <td><strong>{{ $module->title }}</strong><br><span class="dim">{{ $module->module_code }} · Module {{ $module->module_no }}</span></td>
              <td>{{ $module->version_name }}<br><span class="dim">{{ $module->version_code }}</span></td>
              <td><span class="badge {{ $module->is_active ? 'active' : 'disabled' }}">{{ $module->is_active ? 'Active' : 'Inactive' }}</span></td>
              <td>
                <form method="POST" action="{{ route('admin.content.modules.update', $module) }}">
                  @csrf
                  @method('PUT')
                  <div class="form-grid three">
                    <div class="field"><label>Title</label><input class="input" name="title" value="{{ $module->title }}" required></div>
                    <div class="field"><label>Year Level</label><input class="input" name="year_level" value="{{ $module->year_level }}"></div>
                    <div class="field"><label>Version Name</label><input class="input" name="version_name" value="{{ $module->version_name }}"></div>
                    <div class="field"><label>Estimated Minutes</label><input class="input" type="number" name="estimated_minutes" value="{{ $module->estimated_minutes }}" min="0"></div>
                    <div class="field"><label>Sort Order</label><input class="input" type="number" name="sort_order" value="{{ $module->sort_order }}" min="0"></div>
                    <div class="field"><label>Active</label><select class="select" name="is_active"><option value="1" @selected($module->is_active)>Active</option><option value="0" @selected(!$module->is_active)>Inactive</option></select></div>
                  </div>
                  <div class="field" style="margin-top:10px"><label>Description</label><textarea class="textarea" name="description">{{ $module->description }}</textarea></div>
                  <div class="action-row" style="margin-top:10px"><button class="btn small" type="submit">Save Module</button></div>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="4">No modules found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="pagination">{{ $modules->links() }}</div>
  </section>

  <section class="panel">
    <h2 class="panel-title">Challenge Categories</h2>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Category</th><th>Audience</th><th>Update</th></tr></thead>
        <tbody>
          @forelse($categories as $category)
            <tr>
              <td><strong>{{ $category->name }}</strong><br><span class="dim">{{ $category->slug }}</span></td>
              <td>{{ $category->target_audience }}</td>
              <td>
                <form method="POST" action="{{ route('admin.content.categories.update', $category) }}">
                  @csrf
                  @method('PUT')
                  <div class="form-grid three">
                    <div class="field"><label>Name</label><input class="input" name="name" value="{{ $category->name }}" required></div>
                    <div class="field"><label>Slug</label><input class="input" name="slug" value="{{ $category->slug }}" required></div>
                    <div class="field"><label>Audience</label><input class="input" name="target_audience" value="{{ $category->target_audience }}"></div>
                    <div class="field"><label>Order</label><input class="input" type="number" name="order_index" value="{{ $category->order_index }}" min="0"></div>
                  </div>
                  <div class="field" style="margin-top:10px"><label>Description</label><textarea class="textarea" name="description">{{ $category->description }}</textarea></div>
                  <div class="action-row" style="margin-top:10px"><button class="btn small" type="submit">Save Category</button></div>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="3">No categories found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

  <section class="panel">
    <h2 class="panel-title">Challenges</h2>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Challenge</th><th>Category</th><th>Type</th><th>Update</th></tr></thead>
        <tbody>
          @forelse($challenges as $challenge)
            <tr>
              <td><strong>{{ $challenge->title }}</strong><br><span class="dim">{{ $challenge->time_limit_seconds }} sec · {{ $challenge->base_xp }} XP</span></td>
              <td>{{ $challenge->category?->name ?? 'Uncategorized' }}</td>
              <td><span class="badge info">{{ $challenge->is_coding_challenge ? 'Coding' : 'MCQ' }}</span></td>
              <td>
                <form method="POST" action="{{ route('admin.content.challenges.update', $challenge) }}">
                  @csrf
                  @method('PUT')
                  <div class="form-grid three">
                    <div class="field"><label>Title</label><input class="input" name="title" value="{{ $challenge->title }}" required></div>
                    <div class="field"><label>Category</label><select class="select" name="challenge_category_id">@foreach($categories as $category)<option value="{{ $category->id }}" @selected((int)$challenge->challenge_category_id === (int)$category->id)>{{ $category->name }}</option>@endforeach</select></div>
                    <div class="field"><label>Time Limit Seconds</label><input class="input" type="number" name="time_limit_seconds" value="{{ $challenge->time_limit_seconds }}" min="60" required></div>
                    <div class="field"><label>Base XP</label><input class="input" type="number" name="base_xp" value="{{ $challenge->base_xp }}" min="0" required></div>
                    <div class="field"><label>Order</label><input class="input" type="number" name="order_index" value="{{ $challenge->order_index }}" min="0"></div>
                    <div class="field"><label>Type</label><select class="select" name="is_coding_challenge"><option value="0" @selected(!$challenge->is_coding_challenge)>MCQ</option><option value="1" @selected($challenge->is_coding_challenge)>Coding</option></select></div>
                  </div>
                  <div class="field" style="margin-top:10px"><label>Description</label><textarea class="textarea" name="description">{{ $challenge->description }}</textarea></div>
                  <div class="action-row" style="margin-top:10px"><button class="btn small" type="submit">Save Challenge</button></div>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="4">No challenges found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="pagination">{{ $challenges->links() }}</div>
  </section>
@endsection
