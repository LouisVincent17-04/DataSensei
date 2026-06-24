@extends('admin.layout')

@section('title', 'Admin Dashboard')
@section('eyebrow', 'Platform Operations')
@section('page_title', 'Admin Dashboard')
@section('page_subtitle', 'Monitor DataSensei usage, support users, manage learning content, and review operational issues without superadmin-level destructive access.')

@section('content')
  <section class="grid cards">
    @foreach($analytics['cards'] as $card)
      <div class="stat tone-{{ $card['tone'] }}">
        <div class="label">{{ $card['label'] }}</div>
        <div class="value">{{ number_format($card['value']) }}</div>
        <div class="sub">{{ $card['sub'] }}</div>
      </div>
    @endforeach
  </section>

  <section class="split" style="margin-top:20px">
    <div class="panel">
      <h2 class="panel-title">14-Day Platform Activity</h2>
      @php $maxActivity = max(1, collect($analytics['activityTrend'])->max('total')); @endphp
      <div class="chart-bars">
        @foreach($analytics['activityTrend'] as $day)
          <div class="bar" title="{{ $day['date'] }}: {{ $day['total'] }} activities" style="height: {{ max(6, ($day['total'] / $maxActivity) * 100) }}%;">
            <span>{{ \Carbon\Carbon::parse($day['date'])->format('M j') }}</span>
          </div>
        @endforeach
      </div>
    </div>

    <div class="panel">
      <h2 class="panel-title">Role Distribution</h2>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Role</th><th>Total</th></tr></thead>
          <tbody>
            @forelse($analytics['roleDistribution'] as $role)
              <tr>
                <td>{{ $role['label'] }}</td>
                <td>{{ number_format($role['total']) }}</td>
              </tr>
            @empty
              <tr><td colspan="2">No role data available.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <section class="panel">
    <h2 class="panel-title">System Health</h2>
    <div class="health">
      @foreach($analytics['systemHealth'] as $item)
        <div class="health-card {{ $item['status'] }}">
          <div class="label">{{ $item['label'] }}</div>
          <div class="value">{{ $item['value'] }}</div>
          <div class="note">{{ $item['note'] }}</div>
        </div>
      @endforeach
    </div>
  </section>

  <section class="split">
    <div class="panel">
      <h2 class="panel-title">Recent Accounts</h2>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Name</th><th>Role</th><th>Status</th></tr></thead>
          <tbody>
            @forelse($analytics['recentUsers'] as $row)
              <tr>
                <td><strong>{{ $row['name'] }}</strong><br><span class="dim">{{ $row['email'] }}</span></td>
                <td>{{ \App\Models\User::ROLE_LABELS[(int)$row['role']] ?? 'Role ' . $row['role'] }}</td>
                <td><span class="badge {{ $row['status'] === 'active' ? 'active' : 'disabled' }}">{{ ucfirst($row['status']) }}</span></td>
              </tr>
            @empty
              <tr><td colspan="3">No account activity yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="panel">
      <h2 class="panel-title">Recent Security Flags</h2>
      <div class="table-wrap">
        <table>
          <thead><tr><th>User</th><th>Event</th><th>Severity</th></tr></thead>
          <tbody>
            @php $flags = array_slice(array_merge($analytics['recentAntiCheat'], $analytics['recentChallengeFlags']), 0, 8); @endphp
            @forelse($flags as $row)
              <tr>
                <td><strong>{{ $row['user_name'] ?? 'Unknown' }}</strong><br><span class="dim">{{ $row['user_email'] ?? 'No email' }}</span></td>
                <td>{{ $row['event_type'] ?? 'event' }}</td>
                <td><span class="badge disabled">{{ ucfirst($row['severity'] ?? 'info') }}</span></td>
              </tr>
            @empty
              <tr><td colspan="3">No security flags found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <section class="panel">
    <h2 class="panel-title">Admin Actions</h2>
    <div class="action-row">
      <a class="btn" href="{{ route('admin.users.index') }}">Manage Users</a>
      <a class="btn secondary" href="{{ route('admin.content.index') }}">Manage Content</a>
      <a class="btn secondary" href="{{ route('admin.gamification.index') }}">Manage Gamification</a>
      <a class="btn secondary" href="{{ route('admin.reports.index') }}">Open Reports</a>
    </div>
  </section>
@endsection
