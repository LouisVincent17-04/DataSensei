<script id="datasensei-workspace-interactions">
(() => {
  if (window.__dsWorkspaceReady) return;
  window.__dsWorkspaceReady = true;
  const ready = () => {
    const sidebar = document.querySelector('.ds-navigation');
    if (sidebar) {
      const toggle = document.querySelector('[data-ds-menu-toggle]');
      const closeButton = sidebar.querySelector('[data-ds-menu-close]');
      const backdrop = document.createElement('button');
      backdrop.type = 'button';
      backdrop.className = 'ds-nav-backdrop';
      backdrop.setAttribute('aria-label', 'Close navigation');
      backdrop.tabIndex = -1;
      document.body.appendChild(backdrop);
      sidebar.id ||= 'ds-role-navigation';
      toggle?.setAttribute('aria-controls', sidebar.id);
      const compact = window.matchMedia('(max-width:900px)');
      let previousOverflow = '';
      let active = false;
      const close = (restoreFocus = true) => {
        if (!active) return;
        active = false;
        document.body.classList.remove('ds-nav-open');
        document.body.style.overflow = previousOverflow;
        toggle?.setAttribute('aria-expanded', 'false');
        sidebar.removeAttribute('role');
        sidebar.removeAttribute('aria-modal');
        if (restoreFocus) toggle?.focus();
      };
      const open = () => {
        if (!compact.matches) return;
        previousOverflow = document.body.style.overflow;
        active = true;
        document.body.classList.add('ds-nav-open');
        document.body.style.overflow = 'hidden';
        toggle?.setAttribute('aria-expanded', 'true');
        sidebar.setAttribute('role', 'dialog');
        sidebar.setAttribute('aria-modal', 'true');
        closeButton?.focus();
      };
      toggle?.addEventListener('click', () => active ? close() : open());
      closeButton?.addEventListener('click', () => close());
      backdrop.addEventListener('click', () => close());
      compact.addEventListener('change', () => { if (!compact.matches) close(false); });
      document.addEventListener('keydown', (event) => {
        if (!active) return;
        if (event.key === 'Escape') { event.preventDefault(); close(); return; }
        if (event.key !== 'Tab') return;
        const controls = Array.from(sidebar.querySelectorAll('a[href],button:not(:disabled),input:not(:disabled),[tabindex="0"]')).filter(el => el.getClientRects().length);
        const first = controls[0], last = controls[controls.length - 1];
        if (!first) return;
        if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
        else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
      });
      sidebar.querySelectorAll('a.active').forEach(link => link.setAttribute('aria-current', 'page'));
      sidebar.querySelectorAll('a[href="#"]').forEach(link => {
        // A route that is unavailable must not masquerade as a working link.
        link.removeAttribute('href');
        link.setAttribute('aria-disabled', 'true');
        link.title = 'This section is not available';
      });
      const main = document.querySelector('main') || document.querySelector('.main,.ds-main,.page-modules-main');
      if (main) {
        main.id ||= 'ds-main-content';
        if (!main.hasAttribute('tabindex')) main.tabIndex = -1;
        const skip = document.createElement('a');
        skip.className = 'ds-skip-link'; skip.href = '#' + main.id; skip.textContent = 'Skip to content';
        document.body.prepend(skip);
      }
    }
    const notifications = document.querySelector('.ds-notification-center--sidebar');
    const header = document.querySelector('.topbar,.page-modules-topbar,.page-profile-topbar,.challenge-map-topbar,.ml-head');
    if (notifications && header) {
      (header.querySelector('.ml-actions') || header).appendChild(notifications);
      notifications.classList.add('ds-notification-in-header');
    }
    // Contain wide server-rendered tables without changing cells or datasets.
    document.querySelectorAll('table').forEach(table => {
      if (table.closest('.CodeMirror,.cm-editor,[contenteditable="true"],.ds-table-scroll')) return;
      const parent = table.parentElement;
      if (!parent || getComputedStyle(parent).overflowX === 'auto' || getComputedStyle(parent).overflowX === 'scroll') return;
      const wrapper = document.createElement('div');
      wrapper.className = 'ds-table-scroll';
      wrapper.tabIndex = 0;
      wrapper.setAttribute('role', 'region');
      const label = table.getAttribute('aria-label') || table.querySelector('caption')?.textContent?.trim() || 'Data table';
      wrapper.setAttribute('aria-label', label);
      table.before(wrapper); wrapper.appendChild(table);
    });
  };
  // Defer loading state until all existing validation/AJAX submit handlers ran.
  document.addEventListener('submit', event => {
    const form = event.target;
    const button = event.submitter;
    if (!(form instanceof HTMLFormElement) || !button || form.target === '_blank') return;
    queueMicrotask(() => {
      if (event.defaultPrevented || !button.isConnected || button.disabled || button.closest('[data-ds-no-loading]')) return;
      button.dataset.dsLoading = 'true';
      button.setAttribute('aria-busy', 'true');
    });
  });
  window.addEventListener('pageshow', () => {
    document.querySelectorAll('[data-ds-loading="true"]').forEach(button => {
      delete button.dataset.dsLoading; button.removeAttribute('aria-busy');
    });
  });
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', ready, {once:true});
  else ready();
})();
</script>
