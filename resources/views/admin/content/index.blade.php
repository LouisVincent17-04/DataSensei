@extends('admin.layout')

@section('title', 'Platform Content')
@section('eyebrow', 'Content Manager')
@section('page_title', 'Platform Content')
@section('page_subtitle', 'Create, version, publish, and maintain learning modules, MCQ challenges, and assessment-library content.')

@section('content')
  <section class="grid cards">
    <a class="stat" href="{{ route('admin.module-library.index') }}">
      <div class="stat-header">
        <span class="label">Learning Modules</span>
        <span class="stat-icon" aria-hidden="true">M</span>
      </div>
      <div class="value">{{ number_format($summary['modules']) }}</div>
      <div class="sub">{{ number_format($summary['active_modules']) }} published versions</div>
      <div class="stat-bar"><span></span></div>
    </a>

    <a class="stat tone-purple" href="{{ route('admin.challenges.index') }}">
      <div class="stat-header">
        <span class="label">MCQ Challenges</span>
        <span class="stat-icon" aria-hidden="true">Q</span>
      </div>
      <div class="value">{{ number_format($summary['mcq_challenges']) }}</div>
      <div class="sub">{{ number_format($summary['active_mcq_challenges']) }} published versions</div>
      <div class="stat-bar"><span></span></div>
    </a>

    <a class="stat tone-green" href="{{ route('admin.assessments.index') }}">
      <div class="stat-header">
        <span class="label">Assessment Content</span>
        <span class="stat-icon" aria-hidden="true">A</span>
      </div>
      <div class="value">{{ number_format($summary['assessments']) }}</div>
      <div class="sub">{{ number_format($summary['active_assessments']) }} published versions</div>
      <div class="stat-bar"><span></span></div>
    </a>
  </section>

  <section class="panel" style="margin-top:24px">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Content Workspaces</h2>
        <p class="panel-subtitle">Use a dedicated workspace for each content type. Coding challenges remain developer-managed.</p>
      </div>
    </div>

    <div class="quick-actions">
      <a class="quick-action" href="{{ route('admin.module-library.create') }}">
        <strong>Create Learning Module</strong>
        <span>Add a module version with seeded-compatible content sections and review questions.</span>
      </a>
      <a class="quick-action" href="{{ route('admin.challenges.create') }}">
        <strong>Create MCQ Challenge</strong>
        <span>Create versioned MCQ content, questions, choices, scoring, and publishing status.</span>
      </a>
      <a class="quick-action" href="{{ route('admin.assessments.create') }}">
        <strong>Create Assessment Content</strong>
        <span>Create MCQ, fill-in-the-blank, or mixed assessment templates for instructors.</span>
      </a>
      <a class="quick-action" href="#challenge-categories">
        <strong>Manage Difficulty Categories</strong>
        <span>Maintain the challenge path names, audience labels, descriptions, and order.</span>
      </a>
    </div>
  </section>

  <section class="panel section-anchor" id="challenge-categories" style="margin-top:24px">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">MCQ Challenge Categories</h2>
        <p class="panel-subtitle">These categories are shared by the existing seeded MCQ and coding challenge paths.</p>
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
              <strong>{{ $category->target_audience }}</strong>
              {{ number_format($category->mcq_challenges_count) }} MCQ challenge versions
            </div>
            <div class="summary-status">
              <span class="badge info">Order {{ number_format((int) $category->order_index) }}</span>
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
                  <label for="category-audience-{{ $category->id }}">Target Audience</label>
                  <input id="category-audience-{{ $category->id }}" class="input" name="target_audience" value="{{ $category->target_audience }}" required>
                </div>
                <div class="field">
                  <label for="category-order-{{ $category->id }}">Display Order</label>
                  <input id="category-order-{{ $category->id }}" class="input" type="number" name="order_index" value="{{ $category->order_index }}" min="0" required>
                </div>
              </div>

              <div class="field" style="margin-top:14px">
                <label for="category-description-{{ $category->id }}">Description</label>
                <textarea id="category-description-{{ $category->id }}" class="textarea" name="description" required>{{ $category->description }}</textarea>
              </div>

              <div class="action-row">
                <button class="btn small" type="submit">Save Category</button>
              </div>
            </form>
          </div>
        </details>
      @empty
        <div class="empty-cell">No challenge categories are available.</div>
      @endforelse
    </div>
  </section>
@endsection
