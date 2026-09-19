@once
@include('partials.design-system')
<style id="datasensei-ui-polish">
  /*
   * Product-wide guard rails that every page receives. They only fix
   * behaviour (overflow, wrapping, responsive stacking); the look of each
   * component comes from the design system tokens.
   */
  html { scroll-behavior: smooth; }

  body {
    overflow-x: hidden;
    text-rendering: optimizeLegibility;
  }

  img, svg, video, canvas, iframe { max-width: 100%; }
  img, video { height: auto; }
  svg { flex-shrink: 0; }

  a, button, input, select, textarea { -webkit-tap-highlight-color: transparent; }
  :where(button, input, select, textarea) { font: inherit; }

  ::selection {
    background: rgba(59, 130, 246, 0.35);
    color: #ffffff;
  }

  /* Flex and grid children may shrink below their content width, so long
     words and wide tables never push the page sideways. */
  :where(.layout, .shell, .ds-shell, .admin-shell, .page-layout-wrapper, .app, .workspace, .ml-layout, .bar-shell, .result-layout, .form-layout),
  :where(.main, .content, .ds-main, .ml-main, .page-challenges-main, .challenge-map-main, .page-quiz-main, .page-profile-main, .page-modules-main, .coding-main, .student-mod-page, .lesson-main, .result-main, .report, .wrap) {
    min-width: 0;
  }

  :where(.topbar, .top, .top-row, .header, .section-head, .card-header, .panel-head, .toolbar, .actions, .action-row, .modal-footer) > * {
    min-width: 0;
  }

  :where(h1, h2, h3, h4, .title, .page-title, .card-title, .panel-title) {
    overflow-wrap: break-word;
  }

  :where(p, li, td, dd, .muted, .note, .meta, .desc, .description) {
    overflow-wrap: break-word;
  }

  :where(pre) {
    max-width: 100%;
    overflow-x: auto;
  }

  /* Scrollbars stay quiet. */
  .sidebar, .admin-sidebar, .lesson-nav { scrollbar-width: thin; scrollbar-color: rgba(127, 147, 176, 0.3) transparent; }
  ::-webkit-scrollbar { width: 10px; height: 10px; }
  ::-webkit-scrollbar-track { background: transparent; }
  ::-webkit-scrollbar-thumb {
    background: rgba(127, 147, 176, 0.26);
    border: 3px solid transparent;
    border-radius: 10px;
    background-clip: padding-box;
  }
  ::-webkit-scrollbar-thumb:hover { background-color: rgba(127, 147, 176, 0.4); }

  /* Controls never overflow their column. */
  :where(input:not([type="checkbox"]):not([type="radio"]):not([type="range"]), select, textarea, .input, .select, .textarea, .form-control) {
    max-width: 100%;
    min-width: 0;
  }

  :where(textarea) { resize: vertical; }

  :where(.toolbar, .actions, .action-row, .modal-footer, .topbar-actions, .filter-row, .form-actions, .btn-row, .button-row) {
    flex-wrap: wrap;
  }

  :where(.form-row, .form-grid, .grid, .grid-2, .grid-3, .stats-row, .stat-grid, .stats-grid, .cards-grid, .dashboard-grid, .split, .health) > * {
    min-width: 0;
  }

  /* Tables: wide tables scroll inside their own box (see the design system
     script), never the page. */
  :where(.table-wrap, .table-responsive, .tbl-wrap, .data-table-wrap, .table-scroll, .overflow-x-auto) {
    width: 100%;
    max-width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  :where(th, td) { overflow-wrap: normal; word-break: normal; }
  :where(td) { overflow-wrap: break-word; }

  /* Dialogs always fit the viewport and scroll inside. */
  :where(.modal, .modal-card, .modal-box, .modal-content, .dialog, .dialog-card) {
    max-width: calc(100vw - 32px);
    max-height: calc(100vh - 32px);
    max-height: calc(100dvh - 32px);
    overflow-y: auto;
  }

  .sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
  }

  /* ── Small screens ─────────────────────────────────────────────────
     Wide tables (4+ columns, tagged by the design system script) keep a
     readable width and scroll inside their own box. */
  @media (max-width: 760px) {
    table.ds-table--wide { min-width: 640px; }
  }

  @media (max-width: 560px) {
    :where(.modal-footer, .form-actions) > :where(.btn, button, a) { flex: 1 1 auto; }
    :where(.modal-overlay, .overlay, .modal-backdrop) { padding: 12px; }
  }
</style>
@endonce

@once
@php
  $dsGlobalFlashNotifications = [];
  $dsGlobalFlashTypes = [
    'success' => 'success',
    'error' => 'error',
    'warning' => 'warning',
    'info' => 'info',
    'status' => 'info',
    'message' => 'info',
  ];

  foreach ($dsGlobalFlashTypes as $dsFlashKey => $dsFlashType) {
      $dsFlashMessage = session($dsFlashKey);
      $dsFlashCanRender = is_scalar($dsFlashMessage)
          || (is_object($dsFlashMessage) && method_exists($dsFlashMessage, '__toString'));

      if ($dsFlashCanRender && trim((string) $dsFlashMessage) !== '') {
          $dsGlobalFlashNotifications[] = [
              'type' => $dsFlashType,
              'message' => trim((string) $dsFlashMessage),
          ];
      }
  }

  $dsGlobalValidationMessages = isset($errors) && $errors->any()
      ? array_values(array_filter(array_map(
          static fn ($message) => trim((string) $message),
          $errors->all()
      )))
      : [];
@endphp
<style id="datasensei-global-notifications-style">
  #ds-global-notification-stack {
    position: relative !important;
    inset: auto !important;
    z-index: 10000;
    width: min(420px, calc(100% - 32px));
    max-width: calc(100% - 32px);
    margin: 16px max(16px, env(safe-area-inset-right, 0px)) 16px auto !important;
    padding: 0;
    border: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
    color: inherit;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 8px;
    overflow: visible;
    pointer-events: none;
    box-sizing: border-box;
    align-self: flex-end;
    justify-self: end;
    grid-column: 1 / -1;
    flex: 0 0 auto;
    clear: both;
    isolation: isolate;
  }

  #ds-global-notification-stack[hidden] {
    display: none !important;
  }

  #ds-global-notification-stack > [data-ds-global-notification] {
    position: relative !important;
    inset: auto !important;
    top: auto !important;
    right: auto !important;
    bottom: auto !important;
    left: auto !important;
    float: none !important;
    flex: 0 1 auto;
    margin: 0 !important;
    z-index: auto !important;
    max-inline-size: 100%;
    overflow-wrap: anywhere;
    pointer-events: auto;
    box-sizing: border-box;
  }

  .ds-runtime-notification {
    width: min(390px, 100%);
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: start;
    gap: 12px;
    padding: 12px 14px;
    border: 1px solid var(--ds-accent-border, rgba(59, 130, 246, 0.4));
    border-radius: var(--ds-radius-sm, 6px);
    background: var(--ds-surface, #111c2d);
    color: #dbeafe;
    box-shadow: var(--ds-shadow-md);
    font: 500 0.8125rem/1.5 var(--ds-font-sans, Inter, Arial, Helvetica, sans-serif);
  }

  .ds-runtime-notification--success {
    border-color: var(--ds-success-border, rgba(16, 185, 129, 0.35));
    color: #d1fae5;
  }

  .ds-runtime-notification--warning {
    border-color: var(--ds-warning-border, rgba(245, 158, 11, 0.38));
    color: #fef3c7;
  }

  .ds-runtime-notification--error {
    border-color: var(--ds-danger-border, rgba(239, 68, 68, 0.4));
    color: #fee2e2;
  }

  .ds-runtime-notification__content {
    min-width: 0;
  }

  .ds-runtime-notification__title {
    display: block;
    margin-bottom: 2px;
    color: #fff;
    font-weight: 600;
  }

  .ds-runtime-notification__close {
    width: 26px;
    height: 26px;
    min-height: 26px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: -4px -6px -4px 0;
    padding: 0;
    border: 0;
    border-radius: var(--ds-radius-xs, 4px);
    background: transparent;
    color: currentColor;
    cursor: pointer;
    font: 600 1.1rem/1 var(--ds-font-sans, Inter, Arial, Helvetica, sans-serif);
    opacity: 0.75;
  }

  .ds-runtime-notification__close:hover {
    background: rgba(255, 255, 255, 0.08);
    opacity: 1;
  }

  @media (max-width: 560px) {
    #ds-global-notification-stack {
      width: calc(100% - 24px);
      max-width: calc(100% - 24px);
      margin: 12px max(12px, env(safe-area-inset-right, 0px)) 12px auto !important;
      gap: 8px;
    }
  }
</style>

<script id="datasensei-global-notifications-script">
(() => {
  if (window.__dataSenseiGlobalNotificationsLoaded) return;
  window.__dataSenseiGlobalNotificationsLoaded = true;

  const flashNotifications = @json($dsGlobalFlashNotifications);
  const validationMessages = @json($dsGlobalValidationMessages);
  const queuedCalls = [];
  const api = window.DataSenseiNotifications || {};

  api.ready = false;
  api.show = (...args) => {
    queuedCalls.push(args);
    return null;
  };
  window.DataSenseiNotifications = api;

  const initialize = () => {
    if (api.ready || !document.body) return;

    const normalize = value => String(value ?? '').replace(/\s+/g, ' ').trim();
    const serverMessages = [
      ...flashNotifications.map(item => normalize(item.message)),
      ...validationMessages.map(normalize),
    ].filter(Boolean);
    // The notification inbox, field-level errors, confirmations, and contextual
    // result/status panels remain attached to their controls. This selector is
    // intentionally limited to transient global messages and flash summaries.
    const candidateSelector = [
      '[data-ds-global-notification]',
      '.alert',
      '.flash',
      '.notice',
      '.ml-alert',
      '.page-profile-alert',
      '.page-challenges-alert',
      '.challenge-map-alert',
      '.result-notice',
    ].join(',');
    const mounted = new WeakSet();
    const navigationSelector = [
      'nav',
      'header',
      '[role="navigation"]',
      '.navbar',
      '.topbar',
      '.sidebar',
      '.page-learning-topbar',
      '.page-profile-topbar',
      '.page-modules-topbar',
      '.challenge-map-topbar',
      '.page-quiz-header',
      '.coding-header',
    ].join(',');
    const pageHostSelector = [
      '.auth-page-notification-host',
      'main.content',
      'main.ds-main',
      'main.ml-main',
      'main.lesson-content',
      'main.page-profile-content',
      'main.page-modules-content',
      'main.result-main',
      '.main > .content',
      '.admin-shell > .main > .content',
      '.page-learning-content-area',
      'main:not(.card):not(.panel):not(.modal)',
      '.ds-main',
      '.ml-main',
      '.lesson-content',
      '.page-challenges-main',
      '.challenge-map-main',
      '.page-quiz-main',
      '.coding-main',
      '.result-main',
      '.page-profile-main',
      '.page-modules-main',
      '.app',
    ].join(',');

    const canHostPageNotifications = element => {
      if (!(element instanceof Element) || element.matches(navigationSelector)) return false;
      if (element.closest(navigationSelector)) return false;
      if (element.matches('dialog, [role="dialog"], .modal, .card, .panel, .wrap')) return false;

      const style = window.getComputedStyle(element);
      if (style.display === 'none' || style.visibility === 'hidden') return false;
      if (['flex', 'inline-flex'].includes(style.display) && style.flexDirection.startsWith('row')) return false;

      return true;
    };

    const pageHost = Array.from(document.querySelectorAll(pageHostSelector)).find(canHostPageNotifications)
      || document.body;

    const stack = document.createElement('section');
    stack.id = 'ds-global-notification-stack';
    stack.hidden = true;
    stack.setAttribute('data-ds-page-flow-notifications', '');
    stack.setAttribute('role', 'region');
    stack.setAttribute('aria-label', 'Notifications');
    stack.setAttribute('aria-live', 'polite');
    stack.setAttribute('aria-relevant', 'additions text');
    pageHost.setAttribute('data-ds-notification-page-host', '');

    let insertionHost = pageHost;
    let navigationBoundary = Array.from(pageHost.children).find(child => child.matches(navigationSelector));

    if (!navigationBoundary) {
      const pageForm = Array.from(pageHost.children).find(child => child.matches('form'));
      const formNavigation = pageForm
        ? Array.from(pageForm.children).find(child => child.matches(navigationSelector))
        : null;

      if (formNavigation) {
        insertionHost = pageForm;
        navigationBoundary = formNavigation;
      }
    }

    if (navigationBoundary) navigationBoundary.insertAdjacentElement('afterend', stack);
    else insertionHost.prepend(stack);

    const isVisibleNotification = element => {
      if (!(element instanceof Element) || element.hidden || normalize(element.textContent) === '') return false;
      const style = window.getComputedStyle(element);
      return style.display !== 'none' && style.visibility !== 'hidden' && Number.parseFloat(style.opacity || '1') > 0;
    };

    const syncStackVisibility = () => {
      const shouldHide = !Array.from(stack.children).some(isVisibleNotification);
      if (stack.hidden !== shouldHide) stack.hidden = shouldHide;
    };

    const inferRole = element => {
      if (element.hasAttribute('role')) return;
      const classes = String(element.className || '').toLowerCase();
      const assertive = /error|danger|warning|warn/.test(classes);
      element.setAttribute('role', assertive ? 'alert' : 'status');
    };

    const mount = element => {
      if (!(element instanceof Element) || element === stack || mounted.has(element)) return element;

      element.setAttribute('data-ds-global-notification', '');
      inferRole(element);
      mounted.add(element);
      stack.appendChild(element);
      syncStackVisibility();
      return element;
    };

    const isGlobalCandidate = element => {
      if (!(element instanceof Element)) return false;
      if (element.hasAttribute('data-ds-global-notification')) return true;

      const text = normalize(element.textContent);
      return text !== '' && serverMessages.some(message => text === message || text.includes(message));
    };

    const scan = root => {
      if (!(root instanceof Document || root instanceof Element)) return;

      if (root instanceof Element && root.matches(candidateSelector) && isGlobalCandidate(root)) {
        mount(root);
      }

      root.querySelectorAll(candidateSelector).forEach(element => {
        if (isGlobalCandidate(element)) mount(element);
      });
    };

    const dismiss = element => {
      if (!(element instanceof Element)) return;
      element.remove();
      syncStackVisibility();
    };

    const show = (message, options = {}) => {
      const text = normalize(message);
      if (!text) return null;

      const type = ['success', 'error', 'warning', 'info'].includes(options.type)
        ? options.type
        : 'info';
      const notification = document.createElement('div');
      notification.className = `ds-runtime-notification ds-runtime-notification--${type}`;
      notification.setAttribute('data-ds-global-notification', '');
      notification.setAttribute('role', type === 'error' || type === 'warning' ? 'alert' : 'status');

      const content = document.createElement('div');
      content.className = 'ds-runtime-notification__content';

      if (options.title) {
        const title = document.createElement('strong');
        title.className = 'ds-runtime-notification__title';
        title.textContent = String(options.title);
        content.appendChild(title);
      }

      const body = document.createElement('span');
      body.textContent = text;
      content.appendChild(body);

      const close = document.createElement('button');
      close.type = 'button';
      close.className = 'ds-runtime-notification__close';
      close.setAttribute('aria-label', 'Dismiss notification');
      close.textContent = '×';
      close.addEventListener('click', () => dismiss(notification));

      notification.append(content, close);
      mount(notification);

      const duration = Number(options.duration);
      if (Number.isFinite(duration) && duration > 0) {
        window.setTimeout(() => dismiss(notification), duration);
      }

      return notification;
    };

    scan(document);

    flashNotifications.forEach(notification => {
      const expected = normalize(notification.message);
      const alreadyRendered = Array.from(stack.children).some(element => normalize(element.textContent).includes(expected));
      if (!alreadyRendered) show(notification.message, {type: notification.type, duration: 0});
    });

    const contentObserver = new MutationObserver(records => {
      records.forEach(record => record.addedNodes.forEach(node => {
        if (node instanceof Element) scan(node);
      }));
      syncStackVisibility();
    });
    contentObserver.observe(document.body, {
      childList: true,
      subtree: true,
      attributes: true,
      characterData: true,
      attributeFilter: ['class', 'style', 'hidden'],
    });
    syncStackVisibility();

    api.ready = true;
    api.stack = stack;
    api.host = pageHost;
    api.mount = mount;
    api.show = show;
    api.dismiss = dismiss;

    queuedCalls.splice(0).forEach(args => show(...args));
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialize, {once: true});
  } else {
    initialize();
  }
})();
</script>
@endonce

@auth
  @include('partials.session-timeout')
@endauth
