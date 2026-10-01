@extends('admin.layout')

@section('title', 'Gamification Management')
@section('page_title', 'Gamification Management')
@section('page_subtitle', 'Achievements are fixed system records and are shown here for reference. Missions can be renamed, given a different EXP and description, and set to daily or weekly. XP comes only from DataSensei content, so class work never counts toward these rewards.')

@section('content')
  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Rank Tiers</h2>
        <p class="panel-subtitle">Read-only reference for the experience thresholds used by the ranking system.</p>
      </div>
      <span class="gm-note">Set by the system</span>
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
        <h2 class="panel-title">{!! \App\Support\Glossary::help('achievement', 'Achievements') !!}</h2>
        <p class="panel-subtitle">Achievements are predefined by DataSensei, so their name, description, rule and EXP cannot be changed. An achievement can be removed only while no student has earned it.</p>
      </div>
      <div class="panel-actions gm-actions">
        <span class="gm-note">{{ number_format($achievements->total()) }} {{ $achievements->total() === 1 ? 'achievement' : 'achievements' }}</span>
        <form method="POST" action="{{ route('admin.gamification.achievements.sync') }}" onsubmit="this.querySelector('button').disabled = true; this.querySelector('button').textContent = 'Syncing…';">
          @csrf
          <button class="btn small" type="submit">Sync achievements for all students</button>
        </form>
      </div>
    </div>

    <div class="table-wrap">
      <table class="compact-table gm-table">
        <thead>
          <tr><th>Achievement</th><th>How it is earned</th><th>EXP</th><th>Earned by</th><th></th></tr>
        </thead>
        <tbody>
          @forelse($achievements as $achievement)
            @php $earned = (int) $achievement->unlocks_count; @endphp
            <tr id="achievement-{{ $achievement->id }}" class="gm-row">
              <td data-label="Achievement"><strong>{{ $achievement->name }}</strong></td>
              <td data-label="How it is earned">{{ $achievement->description ?: 'No description.' }}</td>
              <td data-label="EXP">{{ number_format($achievement->xp_reward) }}</td>
              <td data-label="Earned by">{{ number_format($earned) }} {{ $earned === 1 ? 'student' : 'students' }}</td>
              <td data-label="" class="gm-row-action">
                @if($earned > 0)
                  <span class="gm-note" title="{{ \App\Http\Controllers\AdminGamificationController::EARNED_ACHIEVEMENT_MESSAGE }}">Kept, already earned</span>
                @else
                  <form method="POST" action="{{ route('admin.gamification.achievements.destroy', $achievement) }}" onsubmit="return confirm('Remove this achievement? Students will no longer be able to earn it.');">
                    @csrf
                    @method('DELETE')
                    <button class="btn small danger" type="submit">Remove</button>
                  </form>
                @endif
              </td>
            </tr>
          @empty
            <tr><td class="empty-cell" colspan="5">No achievements found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="pagination">{{ $achievements->links('vendor.pagination.admin', ['fragment' => 'achievements']) }}</div>
  </section>

  <section class="panel section-anchor" id="missions">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">{!! \App\Support\Glossary::help('mission', 'Missions') !!}</h2>
        <p class="panel-subtitle">Daily and weekly tasks students complete for EXP. Open one to change its name, EXP, description or frequency.</p>
      </div>
      <span class="gm-note">{{ number_format($missions->total()) }} {{ $missions->total() === 1 ? 'mission' : 'missions' }}</span>
    </div>

    <div class="management-list">
      @forelse($missions as $mission)
        @php
          $formKey = 'mission_'.$mission->id;
          $bag = $errors->getBag($formKey);
          $isOld = old('form_key') === $formKey;
          $frequency = $isOld ? old('period_type') : $mission->period_type;
        @endphp
        <details class="management-item gm-item" id="mission-{{ $mission->id }}" @if($isOld || $bag->any() || session('saved_item') === 'mission-'.$mission->id) open @endif>
          <summary>
            <div class="management-main">
              <strong>{{ $mission->title }}</strong>
              <span>{{ $mission->description ?: 'No description.' }}</span>
            </div>
            <div class="management-metric">
              <strong>{{ number_format($mission->xp_reward) }} EXP</strong>
              {{ $frequencies[$mission->period_type] ?? ucfirst((string) $mission->period_type) }}
            </div>
            <span class="management-toggle">Edit</span>
          </summary>

          <div class="management-editor">
            <form method="POST" action="{{ route('admin.gamification.missions.update', $mission) }}" novalidate>
              @csrf
              @method('PUT')
              <input type="hidden" name="form_key" value="{{ $formKey }}">

              <div class="form-grid three">
                <div class="field">
                  <label for="mission-title-{{ $mission->id }}">Mission name</label>
                  <input id="mission-title-{{ $mission->id }}" class="input @if($bag->has('title')) is-invalid @endif" name="title" maxlength="189" value="{{ $isOld ? old('title') : $mission->title }}" required>
                  @if($bag->has('title'))<p class="gm-error">{{ $bag->first('title') }}</p>@endif
                </div>

                <div class="field">
                  <label for="mission-xp-{{ $mission->id }}">EXP</label>
                  <input id="mission-xp-{{ $mission->id }}" class="input @if($bag->has('xp_reward')) is-invalid @endif" type="number" name="xp_reward" min="0" max="100000" value="{{ $isOld ? old('xp_reward') : $mission->xp_reward }}" required>
                  @if($bag->has('xp_reward'))<p class="gm-error">{{ $bag->first('xp_reward') }}</p>@endif
                </div>

                <div class="field">
                  <label for="mission-period-{{ $mission->id }}">Frequency</label>
                  <select id="mission-period-{{ $mission->id }}" class="select @if($bag->has('period_type')) is-invalid @endif" name="period_type" required>
                    @foreach($frequencies as $value => $label)
                      <option value="{{ $value }}" @selected($frequency === $value)>{{ $label }}</option>
                    @endforeach
                  </select>
                  @if($bag->has('period_type'))<p class="gm-error">{{ $bag->first('period_type') }}</p>@endif
                </div>
              </div>

              <div class="field gm-field">
                <label for="mission-description-{{ $mission->id }}">Description</label>
                <textarea id="mission-description-{{ $mission->id }}" class="textarea @if($bag->has('description')) is-invalid @endif" name="description" maxlength="2000">{{ $isOld ? old('description') : $mission->description }}</textarea>
                @if($bag->has('description'))<p class="gm-error">{{ $bag->first('description') }}</p>@endif
              </div>

              <div class="action-row">
                @if(session('saved_item') === 'mission-'.$mission->id)
                  <span class="gm-note gm-saved" role="status">Saved</span>
                @endif
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

@push('head')
<style>
  .gm-actions { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
  .gm-note { margin:0; color:var(--muted); font-size:.8125rem; line-height:1.45; }
  .gm-saved { align-self:center; color:var(--ds-success-text); }
  .gm-item, .section-anchor { scroll-margin-top:84px; }
  .gm-item > summary { grid-template-columns:minmax(0,1fr) auto auto; }
  .gm-item .management-main span { white-space:normal; }
  .gm-item .management-metric { text-align:right; }
  .gm-grid { grid-template-columns:minmax(0,2fr) minmax(0,1fr); }
  .gm-field { margin-top:16px; }
  .gm-error { margin:6px 0 0; color:var(--ds-danger-text); font-size:.8125rem; }
  .gm-item .is-invalid { border-color:var(--ds-danger-border); }
  .gm-row { scroll-margin-top:84px; }
  .gm-table td { vertical-align:top; }
  .gm-table td:nth-child(3), .gm-table td:nth-child(4) { white-space:nowrap; }
  .gm-row-action { text-align:right; white-space:nowrap; }
  .gm-remove { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-top:16px; padding-top:16px; border-top:1px solid var(--border); }
  @media (max-width:640px) {
    .gm-item > summary { grid-template-columns:minmax(0,1fr) auto; }
    .gm-item > summary .management-metric { grid-column:1 / -1; grid-row:2; text-align:left; }
    .gm-grid { grid-template-columns:1fr; }
  }
</style>
@endpush

@push('scripts')
<script>
  // Back at the achievement or mission that was just saved (or that has an
  // error), opened, below the page header.
  (function () {
    var target = document.querySelector('.gm-item[open]') || (window.location.hash ? document.getElementById(window.location.hash.slice(1)) : null);
    if (!target || !target.classList.contains('gm-item')) return;
    target.open = true;
    var place = function () { target.scrollIntoView({ block: 'start' }); };
    place();
    window.addEventListener('load', function () { window.setTimeout(place, 0); });
  })();
</script>
@endpush
