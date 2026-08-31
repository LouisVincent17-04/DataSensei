@props([
    'title' => 'Table',
    'subtitle' => null,
    'rows' => [],
    'columns' => [],
    'danger' => false,
])

<div class="card">
  <div class="card-header">
    <div>
      <div class="card-title">{{ $title }}</div>
      @if($subtitle)
        <div class="card-sub">{{ $subtitle }}</div>
      @endif
    </div>
    @if($danger)
      <span class="pill pill-red">Review</span>
    @endif
  </div>
  <div class="tbl-wrap">
    <table class="tbl">
      <thead>
        <tr>
          @foreach($columns as $label)
            <th>{{ $label }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $row)
          <tr>
            @foreach($columns as $key => $label)
              @php $value = data_get($row, $key); @endphp
              <td>
                @if(str_contains((string) $key, 'time_seconds') && is_numeric($value))
                  {{ gmdate('i:s', max(0, (int) $value)) }}
                @elseif(is_numeric($value) && ! str_contains((string) $key, 'id'))
                  <span class="strong">{{ number_format((float) $value, str_contains((string) $key, 'avg') ? 2 : 0) }}</span>
                @elseif($key === 'status')
                  <span class="pill {{ $value === 'active' || $value === 'published' || $value === 'submitted' || $value === 'graded' ? 'pill-green' : 'pill-orange' }}">{{ $value ?: '—' }}</span>
                @elseif($key === 'severity')
                  <span class="pill {{ $value === 'critical' || $value === 'severe' ? 'pill-red' : ($value === 'warning' ? 'pill-orange' : 'pill-blue') }}">{{ $value ?: '—' }}</span>
                @elseif(str_contains((string) $key, 'activity') || str_contains((string) $key, 'created') || str_contains((string) $key, 'occurred'))
                  <span class="muted">{{ $value ? \Carbon\Carbon::parse($value)->format('M d, Y h:i A') : '—' }}</span>
                @else
                  {{ $value !== null && $value !== '' ? $value : '—' }}
                @endif
              </td>
            @endforeach
          </tr>
        @empty
          <tr><td colspan="{{ max(1, count($columns)) }}" class="empty">No records available for this section.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
