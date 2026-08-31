@once
<style id="datasensei-ui-polish">
  :root {
    --ds-focus: rgba(59, 130, 246, 0.45);
    --ds-card-shadow: none;
    --ds-soft-shadow: none;
    --ds-control-height: 42px;
  }

  html {
    text-size-adjust: 100%;
    -webkit-text-size-adjust: 100%;
    scroll-behavior: smooth;
  }

  body {
    overflow-x: hidden;
    text-rendering: optimizeLegibility;
  }

  img, svg, video, canvas {
    max-width: 100%;
  }

  svg {
    flex-shrink: 0;
  }

  a, button, input, select, textarea {
    -webkit-tap-highlight-color: transparent;
  }

  button, input, select, textarea {
    font: inherit;
  }

  button:focus-visible,
  a:focus-visible,
  input:focus-visible,
  select:focus-visible,
  textarea:focus-visible,
  [tabindex]:focus-visible {
    outline: 3px solid var(--ds-focus);
    outline-offset: 2px;
  }

  ::selection {
    background: rgba(59, 130, 246, 0.35);
    color: var(--text, #fff);
  }

  .layout,
  .shell,
  .ds-shell,
  .admin-shell,
  .page-layout-wrapper,
  .app,
  .workspace {
    min-width: 0;
  }

  .main,
  .content,
  .ds-main,
  .page-challenges-main,
  .challenge-map-main,
  .page-quiz-main,
  .page-profile-main,
  .coding-main,
  .student-mod-page,
  .lesson-main,
  .report,
  .wrap {
    min-width: 0;
  }

  .sidebar,
  .admin-sidebar,
  .lesson-nav {
    scrollbar-width: thin;
    scrollbar-color: rgba(127, 147, 176, 0.35) transparent;
  }

  .sidebar::-webkit-scrollbar,
  .admin-sidebar::-webkit-scrollbar,
  .lesson-nav::-webkit-scrollbar,
  .content::-webkit-scrollbar,
  .main::-webkit-scrollbar,
  .ds-main::-webkit-scrollbar {
    width: 10px;
    height: 10px;
  }

  .sidebar::-webkit-scrollbar-thumb,
  .admin-sidebar::-webkit-scrollbar-thumb,
  .lesson-nav::-webkit-scrollbar-thumb,
  .content::-webkit-scrollbar-thumb,
  .main::-webkit-scrollbar-thumb,
  .ds-main::-webkit-scrollbar-thumb {
    background: rgba(127, 147, 176, 0.28);
    border-radius: 999px;
    border: 3px solid transparent;
    background-clip: padding-box;
  }

  .sidebar-logo,
  .admin-sidebar-logo {
    min-width: 0;
  }

  .nav-item,
  .admin-nav-item,
  .logout-btn {
    min-width: 0;
    line-height: 1.35;
  }

  .nav-item .badge,
  .admin-nav-item .badge {
    flex: 0 0 auto;
  }

  .icon,
  .nav-item svg,
  .admin-nav-item svg,
  .btn svg,
  .btn-primary svg,
  .btn-ghost svg,
  .topbar-btn svg {
    width: 1.125rem;
    height: 1.125rem;
  }

  .topbar,
  .top,
  .top-row,
  .topbar-row,
  .header,
  .section-head,
  .card-header,
  .panel-head,
  .toolbar,
  .actions,
  .action-row,
  .welcome-cta,
  .modal-footer {
    min-width: 0;
  }

  .topbar > *,
  .top > *,
  .top-row > *,
  .header > *,
  .section-head > *,
  .card-header > *,
  .panel-head > * {
    min-width: 0;
  }

  .title,
  .page-title,
  .card-title,
  .panel-title,
  h1, h2, h3 {
    overflow-wrap: anywhere;
  }

  .subtitle,
  .page-subtitle,
  .muted,
  .dim,
  .card-subtitle,
  .card-sub,
  .section-sub,
  .note {
    line-height: 1.65;
  }

  .card,
  .panel,
  .stat-card,
  .stat,
  .metric,
  .definition-card,
  .page-profile-card,
  .strip-card,
  .empty,
  .empty-state,
  .notice,
  .flash,
  .alert {
    max-width: 100%;
    overflow-wrap: anywhere;
    box-shadow: var(--ds-soft-shadow);
  }

  .card,
  .panel,
  .stat-card,
  .stat,
  .metric,
  .definition-card,
  .page-profile-card,
  .strip-card {
    transition: border-color 0.18s ease, transform 0.18s ease, box-shadow 0.18s ease;
  }

  @media (hover: hover) {
    .card:hover,
    .panel:hover,
    .stat-card:hover,
    .stat:hover,
    .metric:hover,
    .definition-card:hover,
    .page-profile-card:hover,
    .strip-card:hover {
      box-shadow: var(--ds-card-shadow);
    }
  }

  .btn,
  .btn-primary,
  .btn-secondary,
  .btn-ghost,
  .btn-danger,
  .btn-accent,
  .page-profile-btn,
  .tb-btn,
  .modal-btn,
  .node-act-btn,
  .topbar-btn,
  .link-sm {
    align-items: center;
    justify-content: center;
    min-height: 36px;
    line-height: 1.25;
    text-decoration-thickness: 0.08em;
    text-underline-offset: 0.18em;
    white-space: normal;
  }

  .btn,
  .btn-primary,
  .btn-secondary,
  .btn-ghost,
  .btn-danger,
  .btn-accent,
  .page-profile-btn,
  .tb-btn,
  .modal-btn,
  .node-act-btn {
    user-select: none;
  }

  .toolbar,
  .actions,
  .action-row,
  .welcome-cta,
  .modal-footer,
  .topbar-actions,
  .filter-row,
  .form-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
  }

  .input,
  .select,
  .textarea,
  .form-control,
  input[type="text"],
  input[type="email"],
  input[type="password"],
  input[type="number"],
  input[type="date"],
  input[type="datetime-local"],
  input[type="search"],
  input[type="url"],
  select,
  textarea {
    max-width: 100%;
    min-width: 0;
  }

  input:not([type="checkbox"]):not([type="radio"]):not([type="range"]):not([type="file"]),
  select,
  textarea,
  .input,
  .select,
  .textarea,
  .form-control {
    min-height: var(--ds-control-height);
  }

  textarea,
  .textarea {
    line-height: 1.55;
  }

  label,
  .label,
  .field-label,
  .lbl {
    line-height: 1.35;
  }

  .form-row,
  .form-grid,
  .grid,
  .grid-2,
  .grid-3,
  .stats-row,
  .stat-grid,
  .stats-grid,
  .cards-grid,
  .dashboard-grid,
  .split,
  .health,
  .page-profile-stats,
  .definition-grid {
    min-width: 0;
  }

  .form-row > *,
  .form-grid > *,
  .grid > *,
  .grid-2 > *,
  .grid-3 > *,
  .stats-row > *,
  .stat-grid > *,
  .stats-grid > *,
  .cards-grid > *,
  .dashboard-grid > *,
  .split > *,
  .health > *,
  .page-profile-stats > *,
  .definition-grid > * {
    min-width: 0;
  }

  .table-wrap,
  .table-responsive,
  .tbl-wrap,
  .data-table-wrap,
  .overflow-x-auto {
    width: 100%;
    max-width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
  }

  .table-wrap table,
  .table-responsive table,
  .tbl-wrap table,
  .data-table-wrap table,
  table.tbl,
  table.table {
    width: 100%;
  }

  th,
  td {
    overflow-wrap: anywhere;
  }

  th:last-child,
  td:last-child {
    white-space: normal;
  }

  .badge,
  .pill,
  .badge-pill,
  .chip,
  .page-profile-badge,
  .challenge-map-node-status {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    line-height: 1.15;
    white-space: nowrap;
  }

  .empty,
  .empty-state {
    text-align: center;
    padding: clamp(22px, 4vw, 38px);
  }

  .modal,
  .modal-card,
  .dialog,
  [role="dialog"] {
    max-width: min(94vw, 760px);
    max-height: min(90vh, 900px);
  }

  .modal-body,
  .dialog-body {
    max-height: 65vh;
    overflow-y: auto;
  }

  .modal-overlay,
  .overlay {
    padding: 18px;
  }

  .tooltip,
  [data-tooltip] {
    overflow-wrap: normal;
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

  @media (max-width: 1180px) {
    .stats-row,
    .stat-grid,
    .stats-grid,
    .grid.cards,
    .health,
    .page-profile-stats {
      grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }

    .grid-3,
    .dashboard-grid {
      grid-template-columns: 1fr !important;
    }
  }

  @media (max-width: 860px) {
    .layout,
    .ds-shell,
    .admin-shell,
    .page-layout-wrapper {
      display: block !important;
    }

    .main,
    .content,
    .ds-main,
    .page-challenges-main,
    .challenge-map-main,
    .page-quiz-main,
    .page-profile-main,
    .coding-main,
    .student-mod-page {
      width: 100% !important;
      max-width: 100% !important;
      padding: 20px !important;
    }

    .content {
      overflow: visible;
    }

    .topbar,
    .top,
    .top-row,
    .header,
    .section-head,
    .card-header,
    .panel-head,
    .hero,
    .welcome-banner,
    .challenge-map-topbar,
    .page-profile-topbar,
    .coding-header {
      height: auto !important;
      min-height: 0 !important;
      align-items: flex-start !important;
      flex-wrap: wrap !important;
      gap: 14px !important;
    }

    .topbar,
    .page-profile-topbar,
    .challenge-map-topbar,
    .coding-header {
      padding: 16px 20px !important;
    }

    .topbar-search,
    .search,
    .search-box,
    .filter-search {
      width: 100% !important;
      max-width: none !important;
    }

    .grid,
    .grid.cards,
    .grid-2,
    .grid-3,
    .stats-row,
    .stat-grid,
    .stats-grid,
    .cards-grid,
    .dashboard-grid,
    .split,
    .health,
    .form-row,
    .form-grid,
    .form-grid.three,
    .page-profile-stats,
    .definition-grid {
      grid-template-columns: 1fr !important;
    }

    .card-header,
    .panel-head,
    .section-head {
      align-items: flex-start !important;
    }

    .toolbar > *,
    .actions > *,
    .action-row > *,
    .welcome-cta > *,
    .modal-footer > *,
    .form-actions > * {
      min-width: 0;
    }

    .btn,
    .btn-primary,
    .btn-secondary,
    .btn-ghost,
    .btn-danger,
    .btn-accent,
    .page-profile-btn,
    .tb-btn,
    .modal-btn,
    .node-act-btn {
      width: auto;
      max-width: 100%;
    }

    table {
      min-width: 680px;
    }
  }

  @media (max-width: 560px) {
    .main,
    .content,
    .ds-main,
    .page-challenges-main,
    .challenge-map-main,
    .page-quiz-main,
    .page-profile-main,
    .coding-main,
    .student-mod-page {
      padding: 16px !important;
    }

    .card,
    .panel,
    .stat-card,
    .stat,
    .metric,
    .page-profile-card {
      border-radius: 8px !important;
    }

    .card-header,
    .card-body,
    .panel,
    .page-profile-card-body {
      padding-left: 16px !important;
      padding-right: 16px !important;
    }

    .title,
    .page-title,
    h1 {
      font-size: clamp(1.45rem, 9vw, 2rem) !important;
      line-height: 1.1 !important;
    }

    .toolbar,
    .actions,
    .action-row,
    .welcome-cta,
    .modal-footer,
    .form-actions {
      flex-direction: column;
      align-items: stretch !important;
    }

    .toolbar .btn,
    .actions .btn,
    .action-row .btn,
    .welcome-cta .btn,
    .modal-footer .btn,
    .form-actions .btn,
    .toolbar button,
    .actions button,
    .action-row button,
    .welcome-cta button,
    .modal-footer button,
    .form-actions button,
    .toolbar a,
    .actions a,
    .action-row a,
    .welcome-cta a,
    .modal-footer a,
    .form-actions a {
      width: 100%;
    }

    .badge,
    .pill,
    .badge-pill,
    .chip,
    .page-profile-badge {
      white-space: normal;
      text-align: center;
    }
  }

  @media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
      animation-duration: 0.001ms !important;
      animation-iteration-count: 1 !important;
      scroll-behavior: auto !important;
      transition-duration: 0.001ms !important;
    }
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
    padding: 2px;
    border: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
    color: inherit;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 12px;
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
    padding: 13px 14px;
    border: 1px solid rgba(96, 165, 250, 0.34);
    border-radius: var(--radius-sm, 8px);
    background: rgba(17, 28, 45, 0.98);
    color: #dbeafe;
    box-shadow: 0 18px 55px rgba(0, 0, 0, 0.38);
    font: 600 0.84rem/1.5 Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
  }

  .ds-runtime-notification--success {
    border-color: rgba(16, 185, 129, 0.36);
    color: #a7f3d0;
  }

  .ds-runtime-notification--warning {
    border-color: rgba(245, 158, 11, 0.42);
    color: #fde68a;
  }

  .ds-runtime-notification--error {
    border-color: rgba(239, 68, 68, 0.4);
    color: #fecaca;
  }

  .ds-runtime-notification__content {
    min-width: 0;
  }

  .ds-runtime-notification__title {
    display: block;
    margin-bottom: 3px;
    color: #fff;
  }

  .ds-runtime-notification__close {
    width: 26px;
    height: 26px;
    min-height: 26px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: -5px -6px -5px 0;
    padding: 0;
    border: 0;
    border-radius: 6px;
    background: transparent;
    color: currentColor;
    cursor: pointer;
    font: 700 1.1rem/1 sans-serif;
    opacity: 0.78;
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
      gap: 10px;
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
