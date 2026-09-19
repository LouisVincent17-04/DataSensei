{{--
    Shared application navigation shell.

    Every role sidebar (student, instructor, institution admin, admin, super
    admin) includes this partial, so all roles get the same sidebar geometry,
    the same active and hover states, and the same mobile behaviour:

      > 900px   the sidebar sits beside the page and stays in view.
      <= 900px  the sidebar becomes a drawer. A slim bar with a menu button
                and the DataSensei mark is fixed to the top of the screen.

    Layers: mobile bar 900, drawer backdrop 950, drawer 960. Page dialogs
    use 1000 and above, so they always cover the bar.

    $navHome is the link used by the logo in the mobile bar.
    Screens that already render their own menu button (the admin layout's
    #admin-menu-button, or a page-level #js-menu-btn) keep it; the shared bar
    removes itself there so there is only ever one menu button.
--}}
@include('partials.page-heading-style')
@include('partials.ui-polish')

@once
<div class="ds-mobilebar" data-ds-mobilebar>
  <button type="button" class="ds-mobilebar__toggle" data-ds-nav-toggle aria-label="Open navigation" aria-expanded="false">
    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
  </button>
  @include('partials.brand-logo', [
    'variant' => 'topbar',
    'size' => 'compact',
    'href' => $navHome ?? null,
  ])
</div>

<style id="datasensei-sidebar-shell">
  /* ── Sidebar (all roles) ─────────────────────────────────────────── */
  aside.sidebar {
    width: 256px;
    flex: 0 0 256px;
    height: 100vh;
    height: 100dvh;
    min-height: 0;
    position: sticky;
    top: 0;
    z-index: 100;
    display: flex;
    flex-direction: column;
    overflow-x: hidden;
    overflow-y: auto;
    overscroll-behavior: contain;
    background: var(--ds-surface);
    border-right: 1px solid var(--ds-border);
    font-family: var(--ds-font-sans);
  }

  .sidebar .sidebar-logo {
    padding: 18px 20px;
    border-bottom: 1px solid var(--ds-border);
  }

  /* Leave room for the student notification bell beside the logo. */
  .ds-notification-center--sidebar ~ .sidebar .sidebar-logo { padding-right: 64px; }

  .sidebar .nav-group { padding: 16px 12px 0; }

  .sidebar .sidebar-nav {
    flex: 1 0 auto;
    display: flex;
    flex-direction: column;
    gap: 2px;
    padding: 4px 12px 16px;
  }

  .sidebar .nav-label {
    margin: 0 0 6px;
    padding: 0 10px;
    color: var(--ds-text-dim);
    font-size: 0.75rem;
    font-weight: 600;
    line-height: 1.4;
    letter-spacing: 0;
    text-transform: none;
  }

  .sidebar .sidebar-nav .nav-label { margin: 12px 0 4px; }

  .sidebar .nav-item {
    display: flex;
    align-items: center;
    gap: 10px;
    min-height: 36px;
    margin: 0 0 2px;
    padding: 7px 10px;
    border: 0;
    border-radius: var(--ds-radius-sm);
    color: var(--ds-text-muted);
    font-size: 0.875rem;
    font-weight: 500;
    line-height: 1.3;
    text-decoration: none;
    transition: background var(--ds-dur-1) ease, color var(--ds-dur-1) ease;
  }

  .sidebar .nav-item:hover {
    background: var(--ds-surface-2);
    color: var(--ds-text);
  }

  .sidebar .nav-item.active {
    background: var(--ds-surface-2);
    color: var(--ds-text);
    border-radius: 0 var(--ds-radius-sm) var(--ds-radius-sm) 0;
    box-shadow: inset 3px 0 0 var(--ds-accent);
  }

  .sidebar .nav-item svg,
  .sidebar .nav-item .icon {
    width: 18px;
    height: 18px;
    flex: 0 0 18px;
    color: var(--ds-text-dim);
    transition: color var(--ds-dur-1) ease;
  }

  .sidebar .nav-item:hover svg,
  .sidebar .nav-item:hover .icon { color: var(--ds-text-secondary); }

  .sidebar .nav-item.active svg,
  .sidebar .nav-item.active .icon { color: var(--ds-accent); }

  .sidebar .nav-item[aria-disabled="true"] { opacity: 0.5; pointer-events: none; }

  /* Counts next to a navigation link. */
  .sidebar .nav-item .badge {
    flex: 0 0 auto;
    min-width: 20px;
    margin-left: auto;
    padding: 1px 6px;
    border: 1px solid var(--ds-border-strong);
    border-radius: var(--ds-radius-xs);
    background: var(--ds-surface-2);
    color: var(--ds-text-secondary);
    font-size: 0.6875rem;
    font-weight: 600;
    line-height: 1.5;
    text-align: center;
  }

  .sidebar .nav-item .badge[hidden] { display: none; }

  .sidebar .nav-item .badge-warn,
  #institution-admin-sidebar .nav-item .badge {
    border-color: var(--ds-danger-border);
    background: var(--ds-danger-soft);
    color: var(--ds-danger-text);
  }

  .sidebar .sidebar-footer {
    margin-top: auto;
    padding: 12px;
    border-top: 1px solid var(--ds-border);
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .sidebar .user-card {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
    padding: 6px 8px;
    border-radius: var(--ds-radius-sm);
  }

  .sidebar .avatar {
    width: 34px;
    height: 34px;
    flex: 0 0 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--ds-border-strong);
    border-radius: var(--ds-radius-sm);
    background: var(--ds-surface-2);
    color: var(--ds-text);
    font-size: 0.8125rem;
    font-weight: 600;
  }

  .sidebar .avatar-super {
    border-color: var(--ds-accent-border);
    background: var(--ds-accent-soft);
    color: var(--ds-accent-text);
  }

  .sidebar .user-info { min-width: 0; overflow: hidden; }

  .sidebar .user-info .name {
    overflow: hidden;
    color: var(--ds-text);
    font-size: 0.875rem;
    font-weight: 600;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .sidebar .user-info .role {
    margin-top: 1px;
    overflow: hidden;
    color: var(--ds-text-muted);
    font-size: 0.75rem;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .sidebar .logout-form,
  .sidebar .sidebar-footer form { width: 100%; margin: 0; }

  .sidebar .logout-btn {
    width: 100%;
    min-height: 36px;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 7px 10px;
    border: 1px solid var(--ds-border);
    border-radius: var(--ds-radius-sm);
    background: transparent;
    color: var(--ds-text-muted);
    font: 500 0.875rem/1.3 var(--ds-font-sans);
    text-align: left;
    cursor: pointer;
    transition: background var(--ds-dur-1) ease, border-color var(--ds-dur-1) ease, color var(--ds-dur-1) ease;
  }

  .sidebar .logout-btn svg { width: 16px; height: 16px; flex: 0 0 16px; }

  .sidebar .logout-btn:hover {
    border-color: var(--ds-danger-border);
    background: var(--ds-danger-soft);
    color: var(--ds-danger-text);
  }

  /* ── Mobile bar and drawer ───────────────────────────────────────── */
  .ds-mobilebar { display: none; }

  .ds-nav-backdrop {
    position: fixed;
    inset: 0;
    z-index: 950;
    display: block;
    border: 0;
    padding: 0;
    background: var(--ds-overlay);
    opacity: 0;
    visibility: hidden;
    transition: opacity var(--ds-dur-3) ease, visibility 0s linear var(--ds-dur-3);
  }

  html.ds-nav-open .ds-nav-backdrop {
    opacity: 1;
    visibility: visible;
    transition: opacity var(--ds-dur-3) ease, visibility 0s;
  }

  @media (max-width: 900px) {
    .ds-mobilebar {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 900;
      height: var(--ds-mobilebar-h);
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 0 12px;
      padding-left: max(12px, env(safe-area-inset-left));
      padding-right: max(64px, env(safe-area-inset-right));
      background: var(--ds-surface);
      border-bottom: 1px solid var(--ds-border);
    }

    .ds-mobilebar__toggle {
      width: 38px;
      height: 38px;
      flex: 0 0 38px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0;
      border: 1px solid var(--ds-border-strong);
      border-radius: var(--ds-radius-sm);
      background: var(--ds-surface-2);
      color: var(--ds-text);
      cursor: pointer;
    }

    .ds-mobilebar__toggle:hover { background: var(--ds-surface-hover); }

    html.ds-has-mobilebar body { padding-top: var(--ds-mobilebar-h); }

    aside.sidebar {
      position: fixed;
      top: 0;
      left: 0;
      bottom: 0;
      width: min(288px, 86vw);
      flex-basis: auto;
      height: 100vh;
      height: 100dvh;
      z-index: 960;
      transform: translateX(-100%);
      visibility: hidden;
      transition: transform var(--ds-dur-3) var(--ds-ease), visibility 0s linear var(--ds-dur-3);
    }

    aside.sidebar.is-open {
      transform: none;
      visibility: visible;
      box-shadow: var(--ds-shadow-lg);
      transition: transform var(--ds-dur-3) var(--ds-ease), visibility 0s;
    }
  }
</style>

<script id="datasensei-sidebar-shell-script">
(() => {
  if (window.__dsSidebarShell) return;
  window.__dsSidebarShell = true;

  const ready = () => {
    const bar = document.querySelector('[data-ds-mobilebar]');
    const sidebar = document.getElementById('admin-sidebar')
      || document.getElementById('institution-admin-sidebar')
      || document.querySelector('aside.sidebar');

    // The first brand-logo include prints its one-time <style> inside the bar;
    // keep those rules when the bar itself is dropped.
    const dropBar = () => {
      if (!bar) return;
      bar.querySelectorAll('style').forEach((style) => document.head.appendChild(style));
      bar.remove();
    };

    // Screens with their own menu button keep it; one button per screen.
    if (document.getElementById('admin-menu-button') || document.getElementById('js-menu-btn')) {
      dropBar();
      return;
    }

    if (!bar || !sidebar) {
      dropBar();
      return;
    }

    // Sit directly under <body> so page layouts never treat it as a column.
    document.body.prepend(bar);
    document.documentElement.classList.add('ds-has-mobilebar');

    const toggle = bar.querySelector('[data-ds-nav-toggle]');
    const compact = window.matchMedia('(max-width: 900px)');
    const backdrop = document.createElement('button');
    backdrop.type = 'button';
    backdrop.className = 'ds-nav-backdrop';
    backdrop.tabIndex = -1;
    backdrop.setAttribute('aria-label', 'Close navigation');
    document.body.appendChild(backdrop);

    sidebar.id ||= 'ds-role-navigation';
    toggle.setAttribute('aria-controls', sidebar.id);

    let open = false;
    let previousOverflow = '';

    const close = (restoreFocus = true) => {
      if (!open) return;
      open = false;
      sidebar.classList.remove('is-open');
      document.documentElement.classList.remove('ds-nav-open');
      document.body.style.overflow = previousOverflow;
      toggle.setAttribute('aria-expanded', 'false');
      toggle.setAttribute('aria-label', 'Open navigation');
      if (restoreFocus) toggle.focus();
    };

    const show = () => {
      if (!compact.matches || open) return;
      open = true;
      previousOverflow = document.body.style.overflow;
      sidebar.classList.add('is-open');
      document.documentElement.classList.add('ds-nav-open');
      document.body.style.overflow = 'hidden';
      toggle.setAttribute('aria-expanded', 'true');
      toggle.setAttribute('aria-label', 'Close navigation');
      const first = sidebar.querySelector('a[href], button:not(:disabled)');
      first?.focus({ preventScroll: true });
    };

    toggle.addEventListener('click', () => (open ? close() : show()));
    backdrop.addEventListener('click', () => close(false));
    sidebar.addEventListener('click', (event) => {
      if (event.target.closest('a[href]') && compact.matches) close(false);
    });
    document.addEventListener('keydown', (event) => {
      if (open && event.key === 'Escape') close();
    });
    const onChange = () => { if (!compact.matches) close(false); };
    compact.addEventListener ? compact.addEventListener('change', onChange) : compact.addListener(onChange);
  };

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', ready, { once: true });
  else ready();
})();
</script>
@endonce
