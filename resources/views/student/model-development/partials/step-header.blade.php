@php
    $headerStep = (int) ($stepNumber ?? 1);
    $headerTotal = \App\Support\ModelDevelopmentRoadmap::totalSteps();
@endphp

<div class="ml-step-head">
    <span class="ml-step-disc" aria-hidden="true">{{ $headerStep }}</span>
    <div class="ml-step-head-copy">
        <h2 class="ml-section-title">{{ $stepTitle ?? '' }}</h2>
        @if(! empty($stepLead))
            <p class="ml-muted">{{ $stepLead }}</p>
        @endif
    </div>
    <span class="ml-step-count">Step {{ $headerStep }} of {{ $headerTotal }}</span>
</div>
