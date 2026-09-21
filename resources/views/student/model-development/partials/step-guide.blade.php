@php
    $guideStepNumber = (int) ($guideStep ?? 1);
    $stepGuideCopy = \App\Support\ModelDevelopmentGuide::stepGuide($guideStepNumber);
@endphp

{{-- Closed by default: the page should already make sense without it. --}}
<details class="ml-guide" data-step-guide="{{ $guideStepNumber }}">
    <summary>New to this? A 20-second explanation</summary>
    <div class="ml-guide-body">
        <p>{{ $stepGuideCopy['what'] }} {{ $stepGuideCopy['why'] }}</p>
        <p><strong>For example:</strong> {{ $stepGuideCopy['example'] }}</p>
        <p><strong>Tip:</strong> {{ $stepGuideCopy['tip'] }}</p>
        <p><strong>Watch out for:</strong> {{ $stepGuideCopy['mistake'] }}</p>
    </div>
</details>
