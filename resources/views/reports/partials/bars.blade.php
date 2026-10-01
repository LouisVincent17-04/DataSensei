{{--
    A few labelled bars, one figure each (DataSensei Updates 8). Expects
    $group: ['title' => ..., 'items' => [['label', 'percent', 'text'], ...]].
    The colour follows the 70% pass mark: green at or above it, amber from
    50%, red below. A bar may set its own 'tone' (good, warn, bad) instead,
    and a group may add a 'note' shown under the bars (DataSensei Updates 12).
--}}
@php $toneOf = fn ($percent) => \App\Support\Reports\ReportFormat::tone($percent === null ? null : (float) $percent); @endphp
<section class="rp-panel"@if(! empty($group['id'])) id="{{ $group['id'] }}"@endif>
  <h3 class="rp-panel-title">{{ $group['title'] }}</h3>
  <div class="rp-bars">
    @foreach($group['items'] as $bar)
      <div class="rp-bar">
        <span class="rp-bar-label">{{ $bar['label'] }}</span>
        <span class="rp-bar-track" aria-hidden="true"><span class="rp-bar-fill rp-fill-{{ (array_key_exists('tone', $bar) ? $bar['tone'] : $toneOf($bar['percent'])) ?? 'none' }}" style="width: {{ max(0, min(100, (float) ($bar['percent'] ?? 0))) }}%"></span></span>
        <span class="rp-bar-text">{{ $bar['text'] }}</span>
      </div>
    @endforeach
  </div>
  @if(! empty($group['note']))
    <p class="rp-note" style="margin:0 18px 16px">{{ $group['note'] }}</p>
  @endif
</section>
