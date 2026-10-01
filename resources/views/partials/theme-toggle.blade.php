{{--
    Dark / light mode switch (DataSensei Updates 13).

    One plain button. The choice is stored on this device (localStorage) and
    applied by partials.design-system before the page paints. Dark mode is the
    default. Usage: @include('partials.theme-toggle', ['block' => true]) for a
    full-width button in a sidebar footer.
--}}
<button type="button" class="ds-theme-toggle{{ ! empty($block) ? ' is-block' : '' }}" data-ds-theme-toggle aria-label="Switch between dark and light mode">
  <span class="ds-theme-icon-light">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
    Light mode
  </span>
  <span class="ds-theme-icon-dark">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 12.8A9 9 0 1111.2 3a7 7 0 009.8 9.8z"/></svg>
    Dark mode
  </span>
</button>
@once
<style>
  .ds-theme-toggle > span { display: inline-flex; align-items: center; gap: 8px; }
  .ds-theme-toggle.is-block { width: 100%; justify-content: flex-start; min-height: 36px; margin-bottom: 8px; padding: 0 12px; }
</style>
<script>
  (function () {
    function apply(theme) {
      document.documentElement.setAttribute('data-theme', theme);
      try { localStorage.setItem('datasensei.theme', theme); } catch (e) {}
    }
    document.addEventListener('click', function (event) {
      var button = event.target.closest ? event.target.closest('[data-ds-theme-toggle]') : null;
      if (!button) { return; }
      apply(document.documentElement.getAttribute('data-theme') === 'light' ? 'dark' : 'light');
    });
  })();
</script>
@endonce
