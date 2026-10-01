{{--
    "What you will learn" (DataSensei Updates 6, task 1): a module's learning
    outcomes behind a toggle that starts closed. Learners open it when they
    want to check the outcomes. The arrow next to the label nudges for two
    seconds when the page opens, then stops.

    Parameters:
      outcomes   list of outcome sentences (required)
      label      toggle text, default "What you will learn"
      class      extra classes on the <details>, for the page's own styles
      heading    wrap the label in this tag (for example "h3"), optional
      intro      a sentence shown above the list when opened, optional
      listClass  classes on the <ul>, optional
      id         id of the <details>, optional
--}}
@once
<style>
  .ds-outcomes > summary { display:flex; align-items:center; gap:8px; list-style:none; cursor:pointer; user-select:none; }
  .ds-outcomes > summary::-webkit-details-marker { display:none; }
  .ds-outcomes > summary::marker { content:''; }
  .ds-outcomes > summary > h1, .ds-outcomes > summary > h2, .ds-outcomes > summary > h3, .ds-outcomes > summary > h4 { margin:0; }
  .ds-outcomes > summary:focus-visible { outline:0; box-shadow:var(--ds-focus-ring); border-radius:6px; }
  .ds-outcomes-arrow { display:inline-flex; flex:0 0 auto; width:16px; height:16px; color:var(--ds-accent-text, var(--accent)); transition:transform .2s ease; }
  .ds-outcomes[open] > summary .ds-outcomes-arrow { transform:rotate(90deg); }
  /* Four half-second nudges: the hint runs for two seconds, then stops. */
  .ds-outcomes:not([open]) > summary .ds-outcomes-arrow { animation:ds-outcomes-nudge .5s ease-in-out 4; }
  @keyframes ds-outcomes-nudge {
    0%, 100% { transform:translateX(0); }
    50% { transform:translateX(5px); }
  }
  @media (prefers-reduced-motion: reduce) {
    .ds-outcomes:not([open]) > summary .ds-outcomes-arrow { animation:none; }
    .ds-outcomes-arrow { transition:none; }
  }
</style>
@endonce
@php
  $outcomesLabel = $label ?? 'What you will learn';
  $outcomesTag = isset($heading) && in_array($heading, ['h2', 'h3', 'h4'], true) ? $heading : null;
@endphp
<details class="ds-outcomes {{ $class ?? '' }}" @if(! empty($id)) id="{{ $id }}" @endif>
  <summary>
    @if($outcomesTag)
      <{{ $outcomesTag }}>{{ $outcomesLabel }}</{{ $outcomesTag }}>
    @else
      <span>{{ $outcomesLabel }}</span>
    @endif
    <svg class="ds-outcomes-arrow" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 3.5 10.5 8 6 12.5"/></svg>
  </summary>
  @if(! empty($intro))
    <p class="ds-outcomes-intro">{{ $intro }}</p>
  @endif
  <ul class="{{ $listClass ?? '' }}">
    @foreach($outcomes as $outcome)
      <li>{{ $outcome }}</li>
    @endforeach
  </ul>
</details>
