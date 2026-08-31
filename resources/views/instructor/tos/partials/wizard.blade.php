@php($currentStep = $currentStep ?? 1)
<div class="wizard" aria-label="TOS creation progress">
  <div class="wizard-step {{ $currentStep === 1 ? 'active' : ($currentStep > 1 ? 'done' : '') }}">
    <div class="num">{{ $currentStep > 1 ? '✓' : '1' }}</div>
    <div><strong>Create TOS</strong><span>Basic information</span></div>
  </div>
  <div class="wizard-step {{ $currentStep === 2 ? 'active' : ($currentStep > 2 ? 'done' : '') }}">
    <div class="num">{{ $currentStep > 2 ? '✓' : '2' }}</div>
    <div><strong>Set Distribution</strong><span>Build the blueprint</span></div>
  </div>
  <div class="wizard-step {{ $currentStep === 3 ? 'active' : '' }}">
    <div class="num">3</div>
    <div><strong>Review & Generate</strong><span>Check before questions</span></div>
  </div>
</div>
