{{--
    A few labelled bars, one figure each (DataSensei Updates 8). Expects
    $group: ['title' => ..., 'items' => [['label', 'percent', 'text'], ...]].
    The colour follows the 70% pass mark: green at or above it, amber from
    50%, red below.
--}}
@php $toneOf = fn ($percent) => \App\Support\Reports\ReportFormat::tone($percent === null ? null : (float) $percent); @endphp
<section class="rp-panel">
  <h3 class="rp-panel-title">{{ $group['title'] }}</h3>
  <div class="rp-bars">
    @foreach($group['items'] as $bar)
      <div class="rp-bar">
        <span class="rp-bar-label">{{ $bar['label'] }}</span>
        <span class="rp-bar-track" aria-hidden="true"><span class="rp-bar-fill rp-fill-{{ $toneOf($bar['percent']) ?? 'none' }}" style="width: {{ max(0, min(100, (float) ($bar['percent'] ?? 0))) }}%"></span></span>
        <span class="rp-bar-text">{{ $bar['text'] }}</span>
      </div>
    @endforeach
  </div>
</section>
