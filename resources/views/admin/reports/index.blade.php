@extends('admin.layout')

@section('title', 'Reports & Moderation')
@section('eyebrow', 'Operational Review')
@section('page_title', 'Reports & Moderation')
@section('page_subtitle', 'Review recent submissions, code execution outcomes, account activity, and suspicious events for platform support.')

@section('content')
  <section class="grid cards">
    @foreach($reports['cards'] as $card)
      <div class="stat tone-{{ $card['tone'] }}">
        <div class="label">{{ $card['label'] }}</div>
        <div class="value">{{ number_format($card['value']) }}</div>
        <div class="sub">{{ $card['sub'] }}</div>
      </div>
    @endforeach
  </section>

  <section class="panel" style="margin-top:20px">
    <h2 class="panel-title">System Health</h2>
    <div class="health">
      @foreach($reports['systemHealth'] as $item)
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
      <h2 class="panel-title">Assignment Anti-Cheat Events</h2>
      <div class="table-wrap">
        <table>
          <thead><tr><th>User</th><th>Assignment</th><th>Event</th><th>Date</th></tr></thead>
          <tbody>
            @forelse($reports['recentAntiCheat'] as $row)
              <tr>
                <td><strong>{{ $row['user_name'] ?? 'Unknown' }}</strong><br><span class="dim">{{ $row['user_email'] ?? 'No email' }}</span></td>
                <td>{{ $row['assignment_title'] ?? 'Assignment' }}</td>
                <td><span class="badge disabled">{{ $row['event_type'] ?? 'event' }} · {{ $row['severity'] ?? 'info' }}</span></td>
                <td>{{ $row['occurred_at'] ?? $row['created_at'] ?? 'N/A' }}</td>
              </tr>
            @empty
              <tr><td colspan="4">No assignment anti-cheat events found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="panel">
      <h2 class="panel-title">Challenge Attempt Flags</h2>
      <div class="table-wrap">
        <table>
          <thead><tr><th>User</th><th>Challenge</th><th>Event</th><th>Date</th></tr></thead>
          <tbody>
            @forelse($reports['recentChallengeFlags'] as $row)
              <tr>
                <td><strong>{{ $row['user_name'] ?? 'Unknown' }}</strong><br><span class="dim">{{ $row['user_email'] ?? 'No email' }}</span></td>
                <td>{{ $row['challenge_title'] ?? 'Challenge' }}</td>
                <td><span class="badge disabled">{{ $row['event_type'] ?? 'event' }} · {{ $row['severity'] ?? 'info' }}</span></td>
                <td>{{ $row['occurred_at'] ?? $row['created_at'] ?? 'N/A' }}</td>
              </tr>
            @empty
              <tr><td colspan="4">No challenge flags found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <section class="split">
    <div class="panel">
      <h2 class="panel-title">Recent Coding Submissions</h2>
      <div class="table-wrap">
        <table>
          <thead><tr><th>User</th><th>Challenge</th><th>Status</th><th>Tests</th></tr></thead>
          <tbody>
            @forelse($reports['recentCodingSubmissions'] as $row)
              <tr>
                <td><strong>{{ $row['user_name'] ?? 'Unknown' }}</strong><br><span class="dim">{{ $row['user_email'] ?? 'No email' }}</span></td>
                <td>{{ $row['challenge_title'] ?? 'Coding Challenge' }}</td>
                <td><span class="badge info">{{ ucfirst($row['status'] ?? 'unknown') }}</span></td>
                <td>{{ $row['tests_passed'] ?? 0 }} / {{ $row['tests_total'] ?? 0 }}</td>
              </tr>
            @empty
              <tr><td colspan="4">No coding submissions found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="panel">
      <h2 class="panel-title">Recent Assignment Submissions</h2>
      <div class="table-wrap">
        <table>
          <thead><tr><th>User</th><th>Assignment</th><th>Status</th><th>Score</th></tr></thead>
          <tbody>
            @forelse($reports['recentAssignmentSubmissions'] as $row)
              <tr>
                <td><strong>{{ $row['user_name'] ?? 'Unknown' }}</strong><br><span class="dim">{{ $row['user_email'] ?? 'No email' }}</span></td>
                <td>{{ $row['assignment_title'] ?? 'Assignment' }}</td>
                <td><span class="badge info">{{ ucfirst($row['status'] ?? 'unknown') }}</span></td>
                <td>{{ $row['score'] ?? 0 }} / {{ $row['total_points'] ?? 0 }}</td>
              </tr>
            @empty
              <tr><td colspan="4">No assignment submissions found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <section class="panel">
    <h2 class="panel-title">Recent Account Activity</h2>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th></tr></thead>
        <tbody>
          @forelse($reports['recentUsers'] as $row)
            <tr>
              <td>{{ $row['name'] }}</td>
              <td>{{ $row['email'] }}</td>
              <td>{{ \App\Models\User::ROLE_LABELS[(int)$row['role']] ?? 'Role ' . $row['role'] }}</td>
              <td><span class="badge {{ $row['status'] === 'active' ? 'active' : 'disabled' }}">{{ ucfirst($row['status']) }}</span></td>
              <td>{{ $row['created_at'] }}</td>
            </tr>
          @empty
            <tr><td colspan="5">No recent accounts found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
@endsection
