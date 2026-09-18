@php
    $roadmapSteps = \App\Support\ModelDevelopmentRoadmap::steps();
    $roadmapCurrent = max(1, min(count($roadmapSteps), (int) ($roadmapCurrent ?? 1)));
    $roadmapCompletedThrough = max(0, min(count($roadmapSteps), (int) ($roadmapCompletedThrough ?? 0)));
    $roadmapErrorStep = isset($roadmapErrorStep) ? (int) $roadmapErrorStep : null;
    $roadmapLinks = (array) ($roadmapLinks ?? []);
    $roadmapInteractive = (bool) ($roadmapInteractive ?? false);
    $roadmapProgress = \App\Support\ModelDevelopmentRoadmap::progress($roadmapCompletedThrough);
@endphp

<section
    class="ml-roadmap-panel"
    data-model-roadmap
    data-current-step="{{ $roadmapCurrent }}"
    data-completed-through="{{ $roadmapCompletedThrough }}"
    data-error-step="{{ $roadmapErrorStep }}"
    aria-label="Model Development Roadmap">
    <div class="ml-roadmap-heading">
        <div>
            <strong class="ml-roadmap-title">Your progress</strong>
            <small class="ml-roadmap-sub" data-roadmap-progress-text>
                {{ $roadmapProgress['completed'] }} of {{ $roadmapProgress['total'] }} steps completed
            </small>
        </div>
        <span class="ml-roadmap-percent" data-roadmap-percent>{{ $roadmapProgress['percent'] }}%</span>
    </div>

    <div
        class="ml-progress ml-roadmap-progress"
        role="progressbar"
        aria-label="Model Development completion"
        aria-valuemin="0"
        aria-valuemax="100"
        aria-valuenow="{{ $roadmapProgress['percent'] }}">
        <div data-roadmap-progress-bar style="width:{{ $roadmapProgress['percent'] }}%"></div>
    </div>

    <p class="ml-roadmap-position" data-roadmap-position>
        Step {{ $roadmapCurrent }} of {{ count($roadmapSteps) }} - {{ $roadmapSteps[$roadmapCurrent]['label'] }}
    </p>

    <ol class="ml-roadmap-list">
        @foreach($roadmapSteps as $number => $definition)
            @php
                $isCurrent = $number === $roadmapCurrent;
                $isCompleted = $number <= $roadmapCompletedThrough;
                $isError = $number === $roadmapErrorStep;
                $state = $isError
                    ? 'error'
                    : ($isCurrent && $isCompleted
                        ? 'current-complete'
                        : ($isCurrent ? 'current' : ($isCompleted ? 'complete' : 'locked')));
                $stateLabel = match ($state) {
                    'complete' => '✓ Completed',
                    'current-complete' => '✓ Completed · Current step',
                    'current' => 'CURRENT STEP',
                    'error' => '⚠ Needs attention',
                    default => '🔒 Not available yet',
                };
                $link = $roadmapLinks[$number] ?? null;
                $isAvailable = in_array($state, ['complete', 'current-complete', 'current', 'error'], true);
            @endphp

            <li class="ml-roadmap-item {{ $state }}" data-roadmap-step="{{ $number }}" data-state="{{ $state }}">
                @if(is_string($link) && $link !== '')
                    <a
                        class="ml-roadmap-action"
                        href="{{ $link }}"
                        data-roadmap-link
                        @if(! $isAvailable) aria-disabled="true" tabindex="-1" @endif>
                        <span class="ml-roadmap-step-number">{{ $number }}</span>
                        <span class="ml-roadmap-copy">
                            <strong>{{ $definition['label'] }}</strong>
                            <small>{{ $definition['description'] }}</small>
                            <span class="ml-roadmap-state" data-roadmap-state-label>{{ $stateLabel }}</span>
                        </span>
                    </a>
                @elseif($roadmapInteractive && $number >= 2 && $number <= 7)
                    <button
                        class="ml-roadmap-action"
                        type="button"
                        data-roadmap-go="{{ $number }}"
                        @disabled(! $isAvailable)>
                        <span class="ml-roadmap-step-number">{{ $number }}</span>
                        <span class="ml-roadmap-copy">
                            <strong>{{ $definition['label'] }}</strong>
                            <small>{{ $definition['description'] }}</small>
                            <span class="ml-roadmap-state" data-roadmap-state-label>{{ $stateLabel }}</span>
                        </span>
                    </button>
                @else
                    <div class="ml-roadmap-action" aria-disabled="true">
                        <span class="ml-roadmap-step-number">{{ $number }}</span>
                        <span class="ml-roadmap-copy">
                            <strong>{{ $definition['label'] }}</strong>
                            <small>{{ $definition['description'] }}</small>
                            <span class="ml-roadmap-state" data-roadmap-state-label>{{ $stateLabel }}</span>
                        </span>
                    </div>
                @endif
            </li>
        @endforeach
    </ol>
</section>

@once
<script id="datasensei-model-roadmap-script">
(() => {
    if (window.DataSenseiModelRoadmap) return;

    const total = {{ count($roadmapSteps) }};
    const definitions = @json($roadmapSteps);

    const stateLabel = state => ({
        complete: '✓ Completed',
        'current-complete': '✓ Completed · Current step',
        current: 'CURRENT STEP',
        error: '⚠ Needs attention',
        locked: '🔒 Not available yet',
    })[state] || '🔒 Not available yet';

    const update = (root, configuration = {}) => {
        if (typeof root === 'string') root = document.querySelector(root);
        if (!(root instanceof Element)) return;

        const current = Math.max(1, Math.min(total, Number(configuration.current || root.dataset.currentStep || 1)));
        const completed = Math.max(0, Math.min(total, Number(configuration.completed ?? root.dataset.completedThrough ?? 0)));
        const error = configuration.error ? Number(configuration.error) : null;
        const percent = Math.round((completed / total) * 100);

        root.dataset.currentStep = String(current);
        root.dataset.completedThrough = String(completed);
        root.dataset.errorStep = error ? String(error) : '';

        root.querySelectorAll('[data-roadmap-step]').forEach(item => {
            const step = Number(item.dataset.roadmapStep);
            let state = 'locked';
            if (error === step) state = 'error';
            else if (step === current && step <= completed) state = 'current-complete';
            else if (step === current) state = 'current';
            else if (step <= completed) state = 'complete';

            item.dataset.state = state;
            item.classList.remove('complete', 'current-complete', 'current', 'locked', 'error');
            item.classList.add(state);
            item.querySelector('[data-roadmap-state-label]').textContent = stateLabel(state);

            const available = ['complete', 'current-complete', 'current', 'error'].includes(state);
            const button = item.querySelector('button[data-roadmap-go]');
            if (button) button.disabled = !available;
            const link = item.querySelector('[data-roadmap-link]');
            if (link) {
                link.setAttribute('aria-disabled', available ? 'false' : 'true');
                if (available) link.removeAttribute('tabindex');
                else link.setAttribute('tabindex', '-1');
            }
        });

        root.querySelector('[data-roadmap-progress-text]').textContent = `${completed} of ${total} steps completed`;
        root.querySelector('[data-roadmap-percent]').textContent = `${percent}%`;
        root.querySelector('[data-roadmap-progress-bar]').style.width = `${percent}%`;
        const progress = root.querySelector('[role="progressbar"]');
        if (progress) progress.setAttribute('aria-valuenow', String(percent));
        const label = definitions[current]?.label || 'Roadmap';
        root.querySelector('[data-roadmap-position]').textContent = `Step ${current} of ${total} - ${label}`;
    };

    document.addEventListener('click', event => {
        const lockedLink = event.target.closest('[data-roadmap-link][aria-disabled="true"]');
        if (lockedLink) event.preventDefault();
    });

    window.DataSenseiModelRoadmap = {update};
})();
</script>
@endonce
