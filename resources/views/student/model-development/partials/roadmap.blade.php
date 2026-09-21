@php
    $roadmapSteps = \App\Support\ModelDevelopmentRoadmap::steps();
    $roadmapTotal = count($roadmapSteps);
    $roadmapCurrent = max(1, min($roadmapTotal, (int) ($roadmapCurrent ?? 1)));
    $roadmapCompletedThrough = max(0, min($roadmapTotal, (int) ($roadmapCompletedThrough ?? 0)));
    $roadmapErrorStep = isset($roadmapErrorStep) ? (int) $roadmapErrorStep : null;
    $roadmapLinks = (array) ($roadmapLinks ?? []);
    $roadmapStateText = [
        'complete' => 'Done',
        'current' => 'You are here',
        'error' => 'Needs a fix',
        'upcoming' => '',
    ];
@endphp

<nav
    class="ml-steps"
    data-model-roadmap
    data-current-step="{{ $roadmapCurrent }}"
    data-completed-through="{{ $roadmapCompletedThrough }}"
    data-error-step="{{ $roadmapErrorStep }}"
    aria-label="Model Development steps">
    <ol>
        @foreach($roadmapSteps as $number => $definition)
            @php
                $state = $number === $roadmapErrorStep
                    ? 'error'
                    : ($number === $roadmapCurrent ? 'current' : ($number <= $roadmapCompletedThrough ? 'complete' : 'upcoming'));
                $link = $roadmapLinks[$number] ?? null;
                $clickable = is_string($link) && $link !== '' && $link !== '#' && $state !== 'upcoming';
            @endphp
            <li class="ml-steps-item {{ $state }}" data-roadmap-step="{{ $number }}" data-state="{{ $state }}" @if($state === 'current') aria-current="step" @endif>
                <a class="ml-steps-link" data-roadmap-link @if($clickable) href="{{ $link }}" @else aria-disabled="true" tabindex="-1" @if(is_string($link) && $link !== '') data-href="{{ $link }}" @endif @endif>
                    <span class="ml-steps-num">{{ str_pad((string) $number, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="ml-steps-copy">
                        <strong>{{ $definition['label'] }}</strong>
                        <small data-roadmap-state-label>{{ $roadmapStateText[$state] !== '' ? $roadmapStateText[$state] : $definition['description'] }}</small>
                    </span>
                </a>
            </li>
        @endforeach
    </ol>
</nav>

@once
<script id="datasensei-model-roadmap-script">
(() => {
    if (window.DataSenseiModelRoadmap) return;

    const total = {{ $roadmapTotal }};
    const definitions = @json($roadmapSteps);
    const stateText = @json($roadmapStateText);

    // Lets the training page move the strip forward when the worker finishes.
    const update = (root, configuration = {}) => {
        if (typeof root === 'string') root = document.querySelector(root);
        if (!(root instanceof Element)) return;

        const current = Math.max(1, Math.min(total, Number(configuration.current || root.dataset.currentStep || 1)));
        const completed = Math.max(0, Math.min(total, Number(configuration.completed ?? root.dataset.completedThrough ?? 0)));
        const error = configuration.error ? Number(configuration.error) : null;

        root.dataset.currentStep = String(current);
        root.dataset.completedThrough = String(completed);
        root.dataset.errorStep = error ? String(error) : '';

        root.querySelectorAll('[data-roadmap-step]').forEach(item => {
            const step = Number(item.dataset.roadmapStep);
            let state = 'upcoming';
            if (error === step) state = 'error';
            else if (step === current) state = 'current';
            else if (step <= completed) state = 'complete';

            item.dataset.state = state;
            item.classList.remove('complete', 'current', 'upcoming', 'error');
            item.classList.add(state);
            if (state === 'current') item.setAttribute('aria-current', 'step');
            else item.removeAttribute('aria-current');
            item.querySelector('[data-roadmap-state-label]').textContent = stateText[state] || definitions[step]?.description || '';

            const link = item.querySelector('[data-roadmap-link]');
            const href = link?.dataset.href || link?.getAttribute('href');
            if (!link || !href || href === '#') return;
            if (state === 'upcoming') {
                link.dataset.href = href;
                link.removeAttribute('href');
                link.setAttribute('aria-disabled', 'true');
                link.setAttribute('tabindex', '-1');
            } else {
                link.setAttribute('href', href);
                link.removeAttribute('aria-disabled');
                link.removeAttribute('tabindex');
            }
        });
    };

    window.DataSenseiModelRoadmap = {update};
})();
</script>
@endonce
