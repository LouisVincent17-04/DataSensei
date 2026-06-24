@props([
    'title' => 'Summary',
    'rows' => [],
])

<div class="card">
  <div class="card-header">
    <div>
      <div class="card-title">{{ $title }}</div>
      <div class="card-sub">Grouped count summary.</div>
    </div>
  </div>
  <div class="card-body">
    @php $max = max(1, collect($rows)->max('total') ?? 1); @endphp
    <div class="bar-list">
      @forelse($rows as $row)
        <div class="bar-row">
          <div class="bar-label">{{ $row['label'] ?: 'Unknown' }}</div>
          <div class="bar-shell"><div class="bar-fill" style="width: {{ max(3, ($row['total'] / $max) * 100) }}%"></div></div>
          <div class="bar-value">{{ number_format($row['total']) }}</div>
        </div>
      @empty
        <div class="empty">No grouped data yet.</div>
      @endforelse
    </div>
  </div>
</div>
