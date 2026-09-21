@php
    $headerStep = (int) ($stepNumber ?? 1);
@endphp

<div class="ml-step-head">
    <span class="ml-step-disc" aria-hidden="true">{{ $headerStep }}</span>
    <div class="ml-step-head-copy">
        <h2 class="ml-section-title">{{ $stepTitle ?? '' }}</h2>
        @if(! empty($stepLead))
            <p class="ml-muted">{{ $stepLead }}</p>
        @endif
    </div>
</div>
