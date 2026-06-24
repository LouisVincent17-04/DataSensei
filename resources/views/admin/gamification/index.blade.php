@extends('admin.layout')

@section('title', 'Gamification Management')
@section('eyebrow', 'Platform Motivation')
@section('page_title', 'Gamification Management')
@section('page_subtitle', 'Maintain achievement and mission definitions used by the learner motivation system. Rank tiers are shown for verification and remain controlled by migrations/seeders.')

@section('content')
  <section class="panel">
    <h2 class="panel-title">Rank Tiers</h2>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Rank</th><th>Required XP</th></tr></thead>
        <tbody>
          @forelse($ranks as $rank)
            <tr>
              <td><strong>{{ $rank->rank_name }}</strong></td>
              <td>{{ number_format($rank->exp_required) }} XP</td>
            </tr>
          @empty
            <tr><td colspan="2">No rank tiers found. Run the rank seeder/migration.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

  <section class="panel">
    <h2 class="panel-title">Achievements</h2>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Achievement</th><th>Rules</th><th>Unlocked</th><th>Update</th></tr></thead>
        <tbody>
          @forelse($achievements as $achievement)
            <tr>
              <td><strong>{{ $achievement->name }}</strong><br><span class="dim">{{ $achievement->achievement_key }}</span><br><span class="badge {{ $achievement->is_active ? 'active' : 'disabled' }}">{{ $achievement->is_active ? 'Active' : 'Inactive' }}</span></td>
              <td>{{ $achievement->criteria_type }} ≥ {{ $achievement->criteria_value }}<br><span class="dim">{{ $achievement->xp_reward }} XP reward</span></td>
              <td>{{ number_format($achievement->unlocks_count) }}</td>
              <td>
                <form method="POST" action="{{ route('admin.gamification.achievements.update', $achievement) }}">
                  @csrf
                  @method('PUT')
                  <div class="form-grid three">
                    <div class="field"><label>Key</label><input class="input" name="achievement_key" value="{{ $achievement->achievement_key }}" required></div>
                    <div class="field"><label>Name</label><input class="input" name="name" value="{{ $achievement->name }}" required></div>
                    <div class="field"><label>Code Mark</label><input class="input" name="icon" value="{{ $achievement->icon }}"></div>
                    <div class="field"><label>Badge Color</label><input class="input" name="badge_color" value="{{ $achievement->badge_color }}"></div>
                    <div class="field"><label>XP Reward</label><input class="input" type="number" name="xp_reward" value="{{ $achievement->xp_reward }}" min="0" required></div>
                    <div class="field"><label>Criteria Type</label><input class="input" name="criteria_type" value="{{ $achievement->criteria_type }}" required></div>
                    <div class="field"><label>Criteria Value</label><input class="input" type="number" name="criteria_value" value="{{ $achievement->criteria_value }}" min="0" required></div>
                    <div class="field"><label>Sort Order</label><input class="input" type="number" name="sort_order" value="{{ $achievement->sort_order }}" min="0"></div>
                    <div class="field"><label>Status</label><select class="select" name="is_active"><option value="1" @selected($achievement->is_active)>Active</option><option value="0" @selected(!$achievement->is_active)>Inactive</option></select></div>
                  </div>
                  <div class="field" style="margin-top:10px"><label>Description</label><textarea class="textarea" name="description">{{ $achievement->description }}</textarea></div>
                  <div class="action-row" style="margin-top:10px"><button class="btn small" type="submit">Save Achievement</button></div>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="4">No achievements found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="pagination">{{ $achievements->links() }}</div>
  </section>

  <section class="panel">
    <h2 class="panel-title">Missions</h2>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Mission</th><th>Target</th><th>Progress Records</th><th>Update</th></tr></thead>
        <tbody>
          @forelse($missions as $mission)
            <tr>
              <td><strong>{{ $mission->title }}</strong><br><span class="dim">{{ $mission->mission_key }}</span><br><span class="badge {{ $mission->is_active ? 'active' : 'disabled' }}">{{ $mission->is_active ? 'Active' : 'Inactive' }}</span></td>
              <td>{{ ucfirst($mission->period_type) }} · {{ $mission->target_type }} × {{ $mission->target_count }}<br><span class="dim">{{ $mission->xp_reward }} XP reward</span></td>
              <td>{{ number_format($mission->progress_count) }}</td>
              <td>
                <form method="POST" action="{{ route('admin.gamification.missions.update', $mission) }}">
                  @csrf
                  @method('PUT')
                  <div class="form-grid three">
                    <div class="field"><label>Key</label><input class="input" name="mission_key" value="{{ $mission->mission_key }}" required></div>
                    <div class="field"><label>Title</label><input class="input" name="title" value="{{ $mission->title }}" required></div>
                    <div class="field"><label>Period</label><select class="select" name="period_type"><option value="daily" @selected($mission->period_type === 'daily')>Daily</option><option value="weekly" @selected($mission->period_type === 'weekly')>Weekly</option></select></div>
                    <div class="field"><label>Target Type</label><input class="input" name="target_type" value="{{ $mission->target_type }}" required></div>
                    <div class="field"><label>Target Count</label><input class="input" type="number" name="target_count" value="{{ $mission->target_count }}" min="1" required></div>
                    <div class="field"><label>XP Reward</label><input class="input" type="number" name="xp_reward" value="{{ $mission->xp_reward }}" min="0" required></div>
                    <div class="field"><label>Sort Order</label><input class="input" type="number" name="sort_order" value="{{ $mission->sort_order }}" min="0"></div>
                    <div class="field"><label>Status</label><select class="select" name="is_active"><option value="1" @selected($mission->is_active)>Active</option><option value="0" @selected(!$mission->is_active)>Inactive</option></select></div>
                  </div>
                  <div class="field" style="margin-top:10px"><label>Description</label><textarea class="textarea" name="description">{{ $mission->description }}</textarea></div>
                  <div class="action-row" style="margin-top:10px"><button class="btn small" type="submit">Save Mission</button></div>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="4">No missions found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="pagination">{{ $missions->links() }}</div>
  </section>
@endsection
