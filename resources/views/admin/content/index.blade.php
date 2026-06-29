@extends('admin.layout')

@section('title', 'Content Management')
@section('eyebrow', 'Learning Content')
@section('page_title', 'Content Management')
@section('page_subtitle', 'Maintain challenge categories and challenge settings without changing existing questions, grading rules, or progression logic.')

@section('content')
  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Challenge Search</h2>
        <p class="panel-subtitle">Find coding and multiple-choice challenges by title or description.</p>
      </div>
    </div>

    <form class="toolbar" method="GET" action="{{ route('admin.content.index') }}#challenges">
      <div class="field">
        <label for="challenge-search">Challenge Search</label>
        <input id="challenge-search" class="input" name="challenge_search" value="{{ request('challenge_search') }}" placeholder="Search challenges">
      </div>
      <button class="btn" type="submit">Search Challenges</button>
    </form>
  </section>

  <section class="panel section-anchor" id="challenge-categories">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Challenge Categories</h2>
        <p class="panel-subtitle">Select a category to edit its identity, audience, description, and display order.</p>
      </div>
      <span class="badge info">{{ number_format($categories->count()) }} categories</span>
    </div>

    <div class="management-list">
      @forelse($categories as $category)
        <details class="management-item">
          <summary>
            <div class="management-main">
              <strong>{{ $category->name }}</strong>
              <span>{{ $category->slug }}</span>
            </div>

            <div class="management-metric">
              <strong>{{ $category->target_audience ?: 'General audience' }}</strong>
              Display order {{ number_format((int) $category->order_index) }}
            </div>

            <div class="summary-status">
              <span class="badge info">Category</span>
            </div>

            <span class="management-toggle">Edit</span>
          </summary>

          <div class="management-editor">
            <form method="POST" action="{{ route('admin.content.categories.update', $category) }}">
              @csrf
              @method('PUT')

              <div class="form-grid three">
                <div class="field">
                  <label for="category-name-{{ $category->id }}">Name</label>
                  <input id="category-name-{{ $category->id }}" class="input" name="name" value="{{ $category->name }}" required>
                </div>

                <div class="field">
                  <label for="category-slug-{{ $category->id }}">Slug</label>
                  <input id="category-slug-{{ $category->id }}" class="input" name="slug" value="{{ $category->slug }}" required>
                </div>

                <div class="field">
                  <label for="category-audience-{{ $category->id }}">Audience</label>
                  <input id="category-audience-{{ $category->id }}" class="input" name="target_audience" value="{{ $category->target_audience }}">
                </div>

                <div class="field">
                  <label for="category-order-{{ $category->id }}">Order</label>
                  <input id="category-order-{{ $category->id }}" class="input" type="number" name="order_index" value="{{ $category->order_index }}" min="0">
                </div>
              </div>

              <div class="field" style="margin-top:14px">
                <label for="category-description-{{ $category->id }}">Description</label>
                <textarea id="category-description-{{ $category->id }}" class="textarea" name="description">{{ $category->description }}</textarea>
              </div>

              <div class="action-row">
                <button class="btn small" type="submit">Save Category</button>
              </div>
            </form>
          </div>
        </details>
      @empty
        <div class="empty-cell">No categories found.</div>
      @endforelse
    </div>
  </section>

  <section class="panel section-anchor" id="challenges">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Challenges</h2>
        <p class="panel-subtitle">Select a challenge to update its metadata while preserving its existing questions and grading logic.</p>
      </div>
      <span class="badge info">{{ number_format($challenges->total()) }} challenges</span>
    </div>

    <div class="management-list">
      @forelse($challenges as $challenge)
        <details class="management-item">
          <summary>
            <div class="management-main">
              <strong>{{ $challenge->title }}</strong>
              <span>{{ $challenge->category?->name ?? 'Uncategorized' }}</span>
            </div>

            <div class="management-metric">
              <strong>{{ $challenge->is_coding_challenge ? 'Coding Challenge' : 'MCQ Challenge' }}</strong>
              {{ number_format((int) $challenge->time_limit_seconds) }} sec · {{ number_format((int) $challenge->base_xp) }} XP
            </div>

            <div class="summary-status">
              <span class="badge info">{{ $challenge->is_coding_challenge ? 'Coding' : 'MCQ' }}</span>
              <span class="management-meta">Order {{ number_format((int) $challenge->order_index) }}</span>
            </div>

            <span class="management-toggle">Edit</span>
          </summary>

          <div class="management-editor">
            <form method="POST" action="{{ route('admin.content.challenges.update', $challenge) }}">
              @csrf
              @method('PUT')

              <div class="form-grid three">
                <div class="field">
                  <label for="challenge-title-{{ $challenge->id }}">Title</label>
                  <input id="challenge-title-{{ $challenge->id }}" class="input" name="title" value="{{ $challenge->title }}" required>
                </div>

                <div class="field">
                  <label for="challenge-category-{{ $challenge->id }}">Category</label>
                  <select id="challenge-category-{{ $challenge->id }}" class="select" name="challenge_category_id">
                    @foreach($categories as $category)
                      <option value="{{ $category->id }}" @selected((int) $challenge->challenge_category_id === (int) $category->id)>{{ $category->name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="field">
                  <label for="challenge-time-{{ $challenge->id }}">Time Limit Seconds</label>
                  <input id="challenge-time-{{ $challenge->id }}" class="input" type="number" name="time_limit_seconds" value="{{ $challenge->time_limit_seconds }}" min="60" required>
                </div>

                <div class="field">
                  <label for="challenge-xp-{{ $challenge->id }}">Base XP</label>
                  <input id="challenge-xp-{{ $challenge->id }}" class="input" type="number" name="base_xp" value="{{ $challenge->base_xp }}" min="0" required>
                </div>

                <div class="field">
                  <label for="challenge-order-{{ $challenge->id }}">Order</label>
                  <input id="challenge-order-{{ $challenge->id }}" class="input" type="number" name="order_index" value="{{ $challenge->order_index }}" min="0">
                </div>

                <div class="field">
                  <label for="challenge-type-{{ $challenge->id }}">Type</label>
                  <select id="challenge-type-{{ $challenge->id }}" class="select" name="is_coding_challenge">
                    <option value="0" @selected(!$challenge->is_coding_challenge)>MCQ</option>
                    <option value="1" @selected($challenge->is_coding_challenge)>Coding</option>
                  </select>
                </div>
              </div>

              <div class="field" style="margin-top:14px">
                <label for="challenge-description-{{ $challenge->id }}">Description</label>
                <textarea id="challenge-description-{{ $challenge->id }}" class="textarea" name="description">{{ $challenge->description }}</textarea>
              </div>

              <div class="action-row">
                <button class="btn small" type="submit">Save Challenge</button>
              </div>
            </form>
          </div>
        </details>
      @empty
        <div class="empty-cell">No challenges found.</div>
      @endforelse
    </div>

    <div class="pagination">{{ $challenges->links('vendor.pagination.admin', ['fragment' => 'challenges']) }}</div>
  </section>
@endsection
