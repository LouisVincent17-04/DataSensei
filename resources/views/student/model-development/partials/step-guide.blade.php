@php
    $guideStepNumber = (int) ($guideStep ?? 1);
    $stepGuideCopy = \App\Support\ModelDevelopmentGuide::stepGuide($guideStepNumber);
@endphp

<details class="ml-guide" data-step-guide="{{ $guideStepNumber }}" open>
    <summary>New to this step? Read the quick guide</summary>
    <div class="ml-guide-body">
        <div class="ml-guide-item">
            <h4>What you are doing</h4>
            <p>{{ $stepGuideCopy['what'] }}</p>
        </div>
        <div class="ml-guide-item">
            <h4>Why it matters</h4>
            <p>{{ $stepGuideCopy['why'] }}</p>
        </div>
        <div class="ml-guide-item wide">
            <h4>Example</h4>
            <p>{{ $stepGuideCopy['example'] }}</p>
        </div>
        <div class="ml-guide-item tip">
            <h4>Beginner tip</h4>
            <p>{{ $stepGuideCopy['tip'] }}</p>
        </div>
        <div class="ml-guide-item warn">
            <h4>Common mistake</h4>
            <p>{{ $stepGuideCopy['mistake'] }}</p>
        </div>
    </div>
</details>

@once
<script id="datasensei-step-guide-script">
(() => {
    // Remember whether a student collapsed the guides. Storage can be unavailable, so every access is guarded.
    const key = 'datasensei.modelGuide.collapsed';
    let collapsed = false;
    try { collapsed = window.localStorage.getItem(key) === '1'; } catch (error) { collapsed = false; }

    const apply = () => {
        document.querySelectorAll('details.ml-guide').forEach(guide => {
            if (collapsed) guide.removeAttribute('open');
            if (guide.dataset.guideBound === '1') return;
            guide.dataset.guideBound = '1';
            guide.addEventListener('toggle', () => {
                collapsed = !guide.open;
                try { window.localStorage.setItem(key, collapsed ? '1' : '0'); } catch (error) { /* ignore */ }
            });
        });
    };

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', apply);
    else apply();
})();
</script>
@endonce
