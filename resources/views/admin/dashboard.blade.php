@extends('admin.layout')

@section('title', 'Admin Dashboard')
@section('eyebrow', 'Platform Operations')
@section('page_title', 'Overview')
@section('page_subtitle', 'Monitor DataSensei usage, support users, manage learning content, and review operational issues.')

@section('content')
  <section class="welcome-banner">
    <div class="welcome-text">
      <h2>Welcome back, {{ auth()->check() ? auth()->user()->name : 'Admin' }}!</h2>
      <p>Monitor platform activity, maintain DataSensei learning resources, support user accounts, and review operational events from one workspace.</p>
      <div class="welcome-cta">
        <a class="btn" href="{{ route('admin.users.index') }}">Manage Users</a>
        <a class="btn secondary" href="{{ route('admin.reports.index') }}">Open Reports</a>
      </div>
    </div>
  </section>

  <section class="grid cards" aria-label="Platform summary">
    @foreach($analytics['cards'] as $card)
      <article class="stat tone-{{ $card['tone'] }}">
        <div class="stat-header">
          <span class="label">{{ $card['label'] }}</span>
          <span class="stat-icon" aria-hidden="true">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path d="M4 19V9m5 10V5m5 14v-7m5 7V3"/>
            </svg>
          </span>
        </div>
        <div class="value">{{ number_format($card['value']) }}</div>
        <div class="sub">{{ $card['sub'] }}</div>
        <div class="stat-bar"><span></span></div>
      </article>
    @endforeach
  </section>

  <section class="split">
    <article class="panel">
      <div class="panel-head">
        <div class="panel-heading">
          <h2 class="panel-title">14-Day Platform Activity</h2>
          <p class="panel-subtitle">Combined account, challenge, coding, and assignment activity.</p>
        </div>
        <span class="badge info">Last 14 days</span>
      </div>
      <div class="panel-body">
        @php $maxActivity = max(1, collect($analytics['activityTrend'])->max('total')); @endphp
        <div class="chart-bars" aria-label="Fourteen-day platform activity chart">
          @foreach($analytics['activityTrend'] as $day)
            <div class="bar" title="{{ $day['date'] }}: {{ $day['total'] }} activities" style="height: {{ max(6, ($day['total'] / $maxActivity) * 100) }}%;">
              <span>{{ \Carbon\Carbon::parse($day['date'])->format('M j') }}</span>
            </div>
          @endforeach
        </div>
      </div>
    </article>

    <article class="panel">
      <div class="panel-head">
        <div class="panel-heading">
          <h2 class="panel-title">Role Distribution</h2>
          <p class="panel-subtitle">Current registered accounts grouped by system role.</p>
        </div>
      </div>
      <div class="table-wrap">
        <table class="compact-table">
          <thead><tr><th>Role</th><th>Total</th></tr></thead>
          <tbody>
            @forelse($analytics['roleDistribution'] as $role)
              <tr>
                <td data-label="Role"><strong>{{ $role['label'] }}</strong></td>
                <td data-label="Total">{{ number_format($role['total']) }}</td>
              </tr>
            @empty
              <tr><td class="empty-cell" colspan="2">No role data available.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </article>
  </section>

  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">System Health</h2>
        <p class="panel-subtitle">Operational configuration and execution environment indicators.</p>
      </div>
    </div>
    <div class="health">
      @foreach($analytics['systemHealth'] as $item)
        <article class="health-card {{ $item['status'] }}">
          <div class="label">{{ $item['label'] }}</div>
          <div class="value">{{ $item['value'] }}</div>
          <div class="note">{{ $item['note'] }}</div>
        </article>
      @endforeach
    </div>
  </section>

  <section class="split">
    <article class="panel">
      <div class="panel-head">
        <div class="panel-heading">
          <h2 class="panel-title">Recent Accounts</h2>
          <p class="panel-subtitle">The latest learner, instructor, and institution-admin registrations.</p>
        </div>
        <a class="btn secondary small" href="{{ route('admin.users.index') }}">View Users</a>
      </div>
      <div class="table-wrap">
        <table class="compact-table">
          <thead><tr><th>Name</th><th>Role</th><th>Status</th></tr></thead>
          <tbody>
            @forelse($analytics['recentUsers'] as $row)
              <tr>
                <td data-label="Name"><strong>{{ $row['name'] }}</strong><br><span class="dim">{{ $row['email'] }}</span></td>
                <td data-label="Role">{{ \App\Models\User::ROLE_LABELS[(int)$row['role']] ?? 'Role ' . $row['role'] }}</td>
                <td data-label="Status"><span class="badge {{ $row['status'] === 'active' ? 'active' : 'disabled' }}">{{ ucfirst($row['status']) }}</span></td>
              </tr>
            @empty
              <tr><td class="empty-cell" colspan="3">No account activity yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </article>

    <article class="panel">
      <div class="panel-head">
        <div class="panel-heading">
          <h2 class="panel-title">Recent Security Flags</h2>
          <p class="panel-subtitle">Recent assignment and challenge events requiring review.</p>
        </div>
        <a class="btn secondary small" href="{{ route('admin.reports.index') }}">Open Reports</a>
      </div>
      <div class="table-wrap">
        <table class="compact-table">
          <thead><tr><th>User</th><th>Event</th><th>Severity</th></tr></thead>
          <tbody>
            @php $flags = array_slice(array_merge($analytics['recentAntiCheat'], $analytics['recentChallengeFlags']), 0, 8); @endphp
            @forelse($flags as $row)
              <tr>
                <td data-label="User"><strong>{{ $row['user_name'] ?? 'Unknown' }}</strong><br><span class="dim">{{ $row['user_email'] ?? 'No email' }}</span></td>
                <td data-label="Event">{{ $row['event_type'] ?? 'event' }}</td>
                <td data-label="Severity"><span class="badge disabled">{{ ucfirst($row['severity'] ?? 'info') }}</span></td>
              </tr>
            @empty
              <tr><td class="empty-cell" colspan="3">No security flags found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </article>
  </section>

  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Admin Actions</h2>
        <p class="panel-subtitle">Quick access to the platform areas used most often.</p>
      </div>
    </div>
    <div class="quick-actions">
      <a class="quick-action" href="{{ route('admin.users.index') }}"><strong>Manage Users</strong><span>Create, update, filter, and enable or disable managed accounts.</span></a>
      <a class="quick-action" href="{{ route('admin.content.index') }}"><strong>Manage Content</strong><span>Maintain module, category, and challenge metadata.</span></a>
      <a class="quick-action" href="{{ route('admin.gamification.index') }}"><strong>Manage Gamification</strong><span>Review rank tiers and update achievements and missions.</span></a>
      <a class="quick-action" href="{{ route('admin.reports.index') }}"><strong>Open Reports</strong><span>Review submissions, security events, and operational activity.</span></a>
    </div>
  </section>
@endsection
