@extends('admin.layout')

@section('title', 'Reports & Moderation')
@section('page_title', 'Reports & Moderation')
@section('page_subtitle', 'Review recent submissions, code execution outcomes, account activity, and suspicious events for platform support.')

@section('content')
  <section class="grid cards" aria-label="Report summary">
    @foreach($reports['cards'] as $card)
      <article class="stat tone-{{ $card['tone'] }}">
        <div class="label">{{ $card['label'] }}</div>
        <div class="value">{{ number_format($card['value']) }}</div>
        <div class="sub">{{ $card['sub'] }}</div>
      </article>
    @endforeach
  </section>

  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">System Health</h2>
        <p class="panel-subtitle">Current application, sandbox, session, queue, and cache configuration.</p>
      </div>
    </div>
    <div class="health">
      @foreach($reports['systemHealth'] as $item)
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
          <h2 class="panel-title">Assignment Anti-Cheat Events</h2>
          <p class="panel-subtitle">Recent browser and policy events recorded during protected assignments.</p>
        </div>
      </div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>User</th><th>Assignment</th><th>Event</th><th>Date</th></tr></thead>
          <tbody>
            @forelse($reports['recentAntiCheat'] as $row)
              <tr>
                <td data-label="User"><strong>{{ $row['user_name'] ?? 'Unknown' }}</strong><br><span class="dim">{{ $row['user_email'] ?? 'No email' }}</span></td>
                <td data-label="Assignment">{{ $row['assignment_title'] ?? 'Assignment' }}</td>
                <td data-label="Event"><span class="badge disabled">{{ $row['event_type'] ?? 'event' }}, {{ $row['severity'] ?? 'info' }}</span></td>
                <td data-label="Date">{{ $row['occurred_at'] ?? $row['created_at'] ?? 'N/A' }}</td>
              </tr>
            @empty
              <tr><td class="empty-cell" colspan="4">No assignment anti-cheat events found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </article>

    <article class="panel">
      <div class="panel-head">
        <div class="panel-heading">
          <h2 class="panel-title">Challenge Attempt Flags</h2>
          <p class="panel-subtitle">Recent suspicious events recorded during timed challenge attempts.</p>
        </div>
      </div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>User</th><th>Challenge</th><th>Event</th><th>Date</th></tr></thead>
          <tbody>
            @forelse($reports['recentChallengeFlags'] as $row)
              <tr>
                <td data-label="User"><strong>{{ $row['user_name'] ?? 'Unknown' }}</strong><br><span class="dim">{{ $row['user_email'] ?? 'No email' }}</span></td>
                <td data-label="Challenge">{{ $row['challenge_title'] ?? 'Challenge' }}</td>
                <td data-label="Event"><span class="badge disabled">{{ $row['event_type'] ?? 'event' }}, {{ $row['severity'] ?? 'info' }}</span></td>
                <td data-label="Date">{{ $row['occurred_at'] ?? $row['created_at'] ?? 'N/A' }}</td>
              </tr>
            @empty
              <tr><td class="empty-cell" colspan="4">No challenge flags found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </article>
  </section>

  <section class="split">
    <article class="panel">
      <div class="panel-head">
        <div class="panel-heading">
          <h2 class="panel-title">Recent Coding Submissions</h2>
          <p class="panel-subtitle">Latest coding results, statuses, and test-case totals.</p>
        </div>
      </div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>User</th><th>Challenge</th><th>Status</th><th>Tests</th></tr></thead>
          <tbody>
            @forelse($reports['recentCodingSubmissions'] as $row)
              <tr>
                <td data-label="User"><strong>{{ $row['user_name'] ?? 'Unknown' }}</strong><br><span class="dim">{{ $row['user_email'] ?? 'No email' }}</span></td>
                <td data-label="Challenge">{{ $row['challenge_title'] ?? 'Coding Challenge' }}</td>
                <td data-label="Status"><span class="badge info">{{ ucfirst($row['status'] ?? 'unknown') }}</span></td>
                <td data-label="Tests">{{ $row['tests_passed'] ?? 0 }} / {{ $row['tests_total'] ?? 0 }}</td>
              </tr>
            @empty
              <tr><td class="empty-cell" colspan="4">No coding submissions found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </article>

    <article class="panel">
      <div class="panel-head">
        <div class="panel-heading">
          <h2 class="panel-title">Recent Assignment Submissions</h2>
          <p class="panel-subtitle">Latest assignment status and score information.</p>
        </div>
      </div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>User</th><th>Assignment</th><th>Status</th><th>Score</th></tr></thead>
          <tbody>
            @forelse($reports['recentAssignmentSubmissions'] as $row)
              <tr>
                <td data-label="User"><strong>{{ $row['user_name'] ?? 'Unknown' }}</strong><br><span class="dim">{{ $row['user_email'] ?? 'No email' }}</span></td>
                <td data-label="Assignment">{{ $row['assignment_title'] ?? 'Assignment' }}</td>
                <td data-label="Status"><span class="badge info">{{ ucfirst($row['status'] ?? 'unknown') }}</span></td>
                <td data-label="Score">{{ $row['score'] ?? 0 }} / {{ $row['total_points'] ?? 0 }}</td>
              </tr>
            @empty
              <tr><td class="empty-cell" colspan="4">No assignment submissions found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </article>
  </section>

  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Recent Account Activity</h2>
        <p class="panel-subtitle">Most recently created managed accounts and their current access status.</p>
      </div>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th></tr></thead>
        <tbody>
          @forelse($reports['recentUsers'] as $row)
            <tr>
              <td data-label="Name"><strong>{{ $row['name'] }}</strong></td>
              <td data-label="Email">{{ $row['email'] }}</td>
              <td data-label="Role">{{ \App\Models\User::ROLE_LABELS[(int)$row['role']] ?? 'Role ' . $row['role'] }}</td>
              <td data-label="Status"><span class="badge {{ $row['status'] === 'active' ? 'active' : 'disabled' }}">{{ ucfirst($row['status']) }}</span></td>
              <td data-label="Created">{{ $row['created_at'] }}</td>
            </tr>
          @empty
            <tr><td class="empty-cell" colspan="5">No recent accounts found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
@endsection
