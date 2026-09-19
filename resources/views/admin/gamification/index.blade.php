@extends('admin.layout')

@section('title', 'Gamification Management')
@section('page_title', 'Gamification Management')
@section('page_subtitle', 'Review ranks and maintain achievement and mission definitions. Editors stay collapsed until a record is selected, keeping the page easier to scan.')

@section('content')
  @php
    $badgeColors = [
      'blue' => ['label' => 'Blue', 'hex' => '#3b82f6'],
      'green' => ['label' => 'Green', 'hex' => '#10b981'],
      'amber' => ['label' => 'Amber', 'hex' => '#f59e0b'],
      'purple' => ['label' => 'Purple', 'hex' => '#3b82f6'],
      'teal' => ['label' => 'Teal', 'hex' => '#14b8a6'],
      'indigo' => ['label' => 'Indigo', 'hex' => '#6366f1'],
      'gold' => ['label' => 'Gold', 'hex' => '#eab308'],
      'yellow' => ['label' => 'Yellow', 'hex' => '#facc15'],
      'cyan' => ['label' => 'Cyan', 'hex' => '#06b6d4'],
      'orange' => ['label' => 'Orange', 'hex' => '#f97316'],
      'red' => ['label' => 'Red', 'hex' => '#ef4444'],
    ];
  @endphp

  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Rank Tiers</h2>
        <p class="panel-subtitle">Read-only reference for the experience thresholds used by the ranking system.</p>
      </div>
      <span class="badge info">Seeded configuration</span>
    </div>
    <div class="table-wrap">
      <table class="compact-table">
        <thead><tr><th>Rank</th><th>Required XP</th></tr></thead>
        <tbody>
          @forelse($ranks as $rank)
            <tr>
              <td data-label="Rank"><strong>{{ $rank->rank_name }}</strong></td>
              <td data-label="Required XP">{{ number_format($rank->exp_required) }} XP</td>
            </tr>
          @empty
            <tr><td class="empty-cell" colspan="2">No rank tiers found. Run the rank seeder or migration.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

  <section class="panel section-anchor" id="achievements">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Achievements</h2>
        <p class="panel-subtitle">Select an achievement to reveal its editor. Existing update routes and validation remain unchanged.</p>
      </div>
      <span class="badge info">{{ number_format($achievements->total()) }} achievements</span>
    </div>

    <div class="management-list">
      @forelse($achievements as $achievement)
        @php
          $currentColor = strtolower((string) $achievement->badge_color);
          $swatch = $badgeColors[$currentColor]['hex'] ?? '#3b82f6';
        @endphp
        <details class="management-item">
          <summary>
            <div class="management-main">
              <strong>{{ $achievement->name }}</strong>
              <span>{{ $achievement->achievement_key }}</span>
            </div>

            <div class="management-metric">
              <strong>{{ $achievement->criteria_type }} ≥ {{ number_format($achievement->criteria_value) }}</strong>
              {{ number_format($achievement->xp_reward) }} XP reward
            </div>

            <div class="summary-status">
              <span class="badge {{ $achievement->is_active ? 'active' : 'disabled' }}">{{ $achievement->is_active ? 'Active' : 'Inactive' }}</span>
              <span class="badge"><span class="color-preview" style="--swatch:{{ $swatch }}"></span>{{ ucfirst($currentColor ?: 'blue') }}</span>
              <span class="management-meta">{{ number_format($achievement->unlocks_count) }} unlocked</span>
            </div>

            <span class="management-toggle">Edit</span>
          </summary>

          <div class="management-editor">
            <form method="POST" action="{{ route('admin.gamification.achievements.update', $achievement) }}">
              @csrf
              @method('PUT')

              <div class="form-grid three">
                <div class="field">
                  <label for="achievement-key-{{ $achievement->id }}">Key</label>
                  <input id="achievement-key-{{ $achievement->id }}" class="input" name="achievement_key" value="{{ $achievement->achievement_key }}" required>
                </div>

                <div class="field">
                  <label for="achievement-name-{{ $achievement->id }}">Name</label>
                  <input id="achievement-name-{{ $achievement->id }}" class="input" name="name" value="{{ $achievement->name }}" required>
                </div>

                <div class="field">
                  <label for="achievement-icon-{{ $achievement->id }}">Code Mark</label>
                  <input id="achievement-icon-{{ $achievement->id }}" class="input" name="icon" value="{{ $achievement->icon }}">
                </div>

                <div class="field">
                  <label for="achievement-color-{{ $achievement->id }}">Badge Color</label>
                  <select id="achievement-color-{{ $achievement->id }}" class="select" name="badge_color">
                    @unless(array_key_exists($currentColor, $badgeColors))
                      <option value="{{ $achievement->badge_color }}" selected>{{ ucfirst((string) $achievement->badge_color) }} (current)</option>
                    @endunless
                    @foreach($badgeColors as $value => $color)
                      <option value="{{ $value }}" @selected($currentColor === $value)>{{ $color['label'] }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="field">
                  <label for="achievement-xp-{{ $achievement->id }}">XP Reward</label>
                  <input id="achievement-xp-{{ $achievement->id }}" class="input" type="number" name="xp_reward" value="{{ $achievement->xp_reward }}" min="0" required>
                </div>

                <div class="field">
                  <label for="achievement-criteria-type-{{ $achievement->id }}">Criteria Type</label>
                  <input id="achievement-criteria-type-{{ $achievement->id }}" class="input" name="criteria_type" value="{{ $achievement->criteria_type }}" required>
                </div>

                <div class="field">
                  <label for="achievement-criteria-value-{{ $achievement->id }}">Criteria Value</label>
                  <input id="achievement-criteria-value-{{ $achievement->id }}" class="input" type="number" name="criteria_value" value="{{ $achievement->criteria_value }}" min="0" required>
                </div>

                <div class="field">
                  <label for="achievement-order-{{ $achievement->id }}">Sort Order</label>
                  <input id="achievement-order-{{ $achievement->id }}" class="input" type="number" name="sort_order" value="{{ $achievement->sort_order }}" min="0">
                </div>

                <div class="field">
                  <label for="achievement-status-{{ $achievement->id }}">Status</label>
                  <select id="achievement-status-{{ $achievement->id }}" class="select" name="is_active">
                    <option value="1" @selected($achievement->is_active)>Active</option>
                    <option value="0" @selected(!$achievement->is_active)>Inactive</option>
                  </select>
                </div>
              </div>

              <div class="field" style="margin-top:16px">
                <label for="achievement-description-{{ $achievement->id }}">Description</label>
                <textarea id="achievement-description-{{ $achievement->id }}" class="textarea" name="description">{{ $achievement->description }}</textarea>
              </div>

              <div class="action-row">
                <button class="btn small" type="submit">Save Achievement</button>
              </div>
            </form>
          </div>
        </details>
      @empty
        <div class="empty-cell">No achievements found.</div>
      @endforelse
    </div>

    <div class="pagination">{{ $achievements->links('vendor.pagination.admin', ['fragment' => 'achievements']) }}</div>
  </section>

  <section class="panel section-anchor" id="missions">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Missions</h2>
        <p class="panel-subtitle">Select a mission to edit its schedule, target, reward, ordering, and status.</p>
      </div>
      <span class="badge info">{{ number_format($missions->total()) }} missions</span>
    </div>

    <div class="management-list">
      @forelse($missions as $mission)
        <details class="management-item">
          <summary>
            <div class="management-main">
              <strong>{{ $mission->title }}</strong>
              <span>{{ $mission->mission_key }}</span>
            </div>

            <div class="management-metric">
              <strong>{{ ucfirst($mission->period_type) }}, {{ $mission->target_type }}</strong>
              Target {{ number_format($mission->target_count) }}, {{ number_format($mission->xp_reward) }} XP
            </div>

            <div class="summary-status">
              <span class="badge {{ $mission->is_active ? 'active' : 'disabled' }}">{{ $mission->is_active ? 'Active' : 'Inactive' }}</span>
              <span class="management-meta">{{ number_format($mission->progress_count) }} progress records</span>
            </div>

            <span class="management-toggle">Edit</span>
          </summary>

          <div class="management-editor">
            <form method="POST" action="{{ route('admin.gamification.missions.update', $mission) }}">
              @csrf
              @method('PUT')

              <div class="form-grid three">
                <div class="field">
                  <label for="mission-key-{{ $mission->id }}">Key</label>
                  <input id="mission-key-{{ $mission->id }}" class="input" name="mission_key" value="{{ $mission->mission_key }}" required>
                </div>

                <div class="field">
                  <label for="mission-title-{{ $mission->id }}">Title</label>
                  <input id="mission-title-{{ $mission->id }}" class="input" name="title" value="{{ $mission->title }}" required>
                </div>

                <div class="field">
                  <label for="mission-period-{{ $mission->id }}">Period</label>
                  <select id="mission-period-{{ $mission->id }}" class="select" name="period_type">
                    <option value="daily" @selected($mission->period_type === 'daily')>Daily</option>
                    <option value="weekly" @selected($mission->period_type === 'weekly')>Weekly</option>
                  </select>
                </div>

                <div class="field">
                  <label for="mission-target-type-{{ $mission->id }}">Target Type</label>
                  <input id="mission-target-type-{{ $mission->id }}" class="input" name="target_type" value="{{ $mission->target_type }}" required>
                </div>

                <div class="field">
                  <label for="mission-target-count-{{ $mission->id }}">Target Count</label>
                  <input id="mission-target-count-{{ $mission->id }}" class="input" type="number" name="target_count" value="{{ $mission->target_count }}" min="1" required>
                </div>

                <div class="field">
                  <label for="mission-xp-{{ $mission->id }}">XP Reward</label>
                  <input id="mission-xp-{{ $mission->id }}" class="input" type="number" name="xp_reward" value="{{ $mission->xp_reward }}" min="0" required>
                </div>

                <div class="field">
                  <label for="mission-order-{{ $mission->id }}">Sort Order</label>
                  <input id="mission-order-{{ $mission->id }}" class="input" type="number" name="sort_order" value="{{ $mission->sort_order }}" min="0">
                </div>

                <div class="field">
                  <label for="mission-status-{{ $mission->id }}">Status</label>
                  <select id="mission-status-{{ $mission->id }}" class="select" name="is_active">
                    <option value="1" @selected($mission->is_active)>Active</option>
                    <option value="0" @selected(!$mission->is_active)>Inactive</option>
                  </select>
                </div>
              </div>

              <div class="field" style="margin-top:16px">
                <label for="mission-description-{{ $mission->id }}">Description</label>
                <textarea id="mission-description-{{ $mission->id }}" class="textarea" name="description">{{ $mission->description }}</textarea>
              </div>

              <div class="action-row">
                <button class="btn small" type="submit">Save Mission</button>
              </div>
            </form>
          </div>
        </details>
      @empty
        <div class="empty-cell">No missions found.</div>
      @endforelse
    </div>

    <div class="pagination">{{ $missions->links('vendor.pagination.admin', ['fragment' => 'missions']) }}</div>
  </section>
@endsection
