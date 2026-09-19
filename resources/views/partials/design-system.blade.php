@once
{{--
    DataSensei design system.

    Every screen loads this file (through partials.page-head, partials.ui-polish,
    the sidebars, and the brand logo), so it is the one place where the shared
    decisions live:

      Type      Inter for all interface text. JetBrains Mono only for code.
      Colour    The existing DataSensei navy surfaces and #3b82f6 blue, written
                down once. Green, amber and red are reserved for status.
      Gradient  One: --ds-gradient-brand (blue to dark blue). Used sparingly.
      Radius    4px badges, 6px controls, 8px cards and panels, 10px dialogs.
      Shadow    Hairline only on cards; real elevation only on menus and dialogs.
      Spacing   4-point steps.

    Pages keep their own layout CSS. The legacy variable names that page styles
    already use (--bg, --surface, --muted, --accent, --radius ...) are defined
    here too, so every page reads the same values.
--}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
{{-- One request for the whole product: Inter for the interface, JetBrains Mono for code. --}}
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<style id="datasensei-design-system">
  :root {
    color-scheme: dark;

    /* Spacing — 4-point rhythm. */
    --ds-space-1: 4px;
    --ds-space-2: 8px;
    --ds-space-3: 12px;
    --ds-space-4: 16px;
    --ds-space-5: 20px;
    --ds-space-6: 24px;
    --ds-space-8: 32px;
    --ds-space-10: 40px;
    --ds-space-12: 48px;
    --ds-space-16: 64px;

    /* Radius — small and consistent. The pill value is kept for progress
       tracks, toggles and avatars only. */
    --ds-radius-xs: 4px;
    --ds-radius-sm: 6px;
    --ds-radius-md: 8px;
    --ds-radius-lg: 10px;
    --ds-radius-xl: 10px;
    --ds-radius-pill: 999px;

    /* Type */
    --ds-font-sans: 'Inter', Arial, Helvetica, sans-serif;
    --ds-font-mono: 'JetBrains Mono', ui-monospace, SFMono-Regular, Consolas, monospace;
    --ds-text-2xs: 0.6875rem;  /* 11px — only for dense chart labels */
    --ds-text-xs: 0.75rem;     /* 12px — meta, table headers, badges */
    --ds-text-sm: 0.8125rem;   /* 13px — labels, secondary text */
    --ds-text-base: 0.875rem;  /* 14px — body */
    --ds-text-md: 1rem;        /* 16px — card and section titles */
    --ds-text-lg: 1.125rem;    /* 18px — large section titles */
    --ds-text-xl: 1.375rem;    /* 22px — page titles */
    --ds-text-2xl: 1.75rem;    /* 28px — large numbers */
    --ds-text-3xl: 2rem;
    --ds-leading-tight: 1.25;
    --ds-leading-snug: 1.4;
    --ds-leading-normal: 1.55;

    /* Motion */
    --ds-dur-1: 0.12s;
    --ds-dur-2: 0.16s;
    --ds-dur-3: 0.22s;
    --ds-ease: cubic-bezier(0.2, 0.6, 0.3, 1);

    /* Elevation — neutral only; colour carries meaning, never depth. */
    --ds-shadow-xs: 0 1px 2px rgba(3, 8, 18, 0.28);
    --ds-shadow-sm: 0 1px 3px rgba(3, 8, 18, 0.34);
    --ds-shadow-md: 0 10px 24px -12px rgba(3, 8, 18, 0.7), 0 2px 6px rgba(3, 8, 18, 0.28);
    --ds-shadow-lg: 0 24px 48px -20px rgba(3, 8, 18, 0.85), 0 4px 12px rgba(3, 8, 18, 0.32);

    /* Palette — the colours DataSensei already uses. */
    --ds-bg: #0d1320;
    --ds-surface: #111c2d;
    --ds-surface-2: #1a2638;
    --ds-surface-3: #0f1928;
    --ds-surface-hover: #1f2d44;
    --ds-border: #1e2f47;
    --ds-border-strong: #2c4168;
    --ds-input-border: #263854;
    --ds-text: #f8fafc;
    --ds-text-secondary: #c8d5e8;
    --ds-text-muted: #8aa0bd;
    --ds-text-dim: #68809f;
    --ds-accent: #3b82f6;
    --ds-accent-strong: #2563eb;
    --ds-accent-text: #93c5fd;
    --ds-accent-soft: rgba(59, 130, 246, 0.12);
    --ds-accent-border: rgba(59, 130, 246, 0.4);
    --ds-success: #10b981;
    --ds-success-text: #6ee7b7;
    --ds-success-soft: rgba(16, 185, 129, 0.12);
    --ds-success-border: rgba(16, 185, 129, 0.35);
    --ds-warning: #f59e0b;
    --ds-warning-text: #fcd34d;
    --ds-warning-soft: rgba(245, 158, 11, 0.12);
    --ds-warning-border: rgba(245, 158, 11, 0.38);
    --ds-danger: #ef4444;
    --ds-danger-strong: #dc2626;
    --ds-danger-text: #fca5a5;
    --ds-danger-soft: rgba(239, 68, 68, 0.12);
    --ds-danger-border: rgba(239, 68, 68, 0.4);
    --ds-overlay: rgba(3, 8, 18, 0.72);
    --ds-focus-ring: 0 0 0 3px rgba(59, 130, 246, 0.35);

    /* The one brand gradient. Blue into dark blue, nothing else. */
    --ds-gradient-brand: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);

    /* Control sizes */
    --ds-control-h: 38px;
    --ds-control-h-sm: 32px;

    /* Sticky offsets: 0 on desktop; the mobile navigation bar height below 900px. */
    --ds-mobilebar-h: 52px;
    --ds-sticky-top: 0px;

    /* Legacy names used by existing page styles, mapped to the tokens above
       so every page resolves to the same values. Page-specific or ambiguous
       names (--warn, --accent2 ...) stay with the page that defines them. */
    --bg: var(--ds-bg);
    --surface: var(--ds-surface);
    --surface2: var(--ds-surface-2);
    --surface3: var(--ds-surface-3);
    --border: var(--ds-border);
    --border-hover: var(--ds-border-strong);
    --text: var(--ds-text);
    --muted: var(--ds-text-muted);
    --dim: var(--ds-text-dim);
    --accent: var(--ds-accent);
    --accent-hover: var(--ds-accent-strong);
    --radius: var(--ds-radius-md);
    --radius-sm: var(--ds-radius-sm);
    --radius-xs: var(--ds-radius-xs);
    --mono: var(--ds-font-mono);
    --font-body: var(--ds-font-sans);
    --font-display: var(--ds-font-sans);
    --font-mono: var(--ds-font-mono);
    --num: var(--ds-font-sans);
  }

  @media (max-width: 900px) {
    :root { --ds-sticky-top: 0px; }
    html.ds-has-mobilebar { --ds-sticky-top: var(--ds-mobilebar-h); }
  }

  /* ── Base ─────────────────────────────────────────────────────────── */
  html {
    -webkit-text-size-adjust: 100%;
    text-size-adjust: 100%;
  }

  body {
    font-family: var(--ds-font-sans);
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }

  :where(h1, h2, h3, h4, h5, h6, button, input, select, textarea, table, th, td, label, legend, optgroup) {
    font-family: var(--ds-font-sans);
  }

  :where(h1, h2, h3, h4, h5, h6) {
    letter-spacing: -0.01em;
    line-height: var(--ds-leading-tight);
  }

  :where(code, pre, kbd, samp, .code, .mono) {
    font-family: var(--ds-font-mono);
  }

  :where(table) {
    font-variant-numeric: tabular-nums;
  }

  /* One visible focus treatment for the whole product. */
  :where(a, button, input, select, textarea, summary, [tabindex]):focus-visible {
    outline: 2px solid transparent;
    outline-offset: 2px;
    box-shadow: var(--ds-focus-ring);
  }

  :where(button, [role="button"], a, summary, label[for], input[type="checkbox"], input[type="radio"]):not(:disabled) {
    cursor: pointer;
  }

  :where(button, a)[aria-disabled="true"],
  :where(button, input, select, textarea):disabled {
    cursor: not-allowed;
  }

  :where(input[type="checkbox"], input[type="radio"], input[type="range"], progress) {
    accent-color: var(--ds-accent);
  }

  /* ── Page header ──────────────────────────────────────────────────── */
  .ds-page-title {
    margin: 0;
    color: var(--ds-text);
    font-family: var(--ds-font-sans);
    font-size: var(--ds-text-xl);
    font-weight: 700;
    line-height: 1.3;
    letter-spacing: -0.015em;
  }

  .ds-page-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: var(--ds-space-4);
    margin-bottom: var(--ds-space-6);
  }

  .ds-page-header__text { min-width: 0; flex: 1 1 320px; }

  .ds-page-subtitle {
    margin: var(--ds-space-1) 0 0;
    max-width: 72ch;
    color: var(--ds-text-muted);
    font-size: var(--ds-text-base);
    line-height: var(--ds-leading-normal);
  }

  .ds-page-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: var(--ds-space-2);
  }

  .ds-section-title {
    margin: 0 0 var(--ds-space-3);
    color: var(--ds-text);
    font-size: var(--ds-text-md);
    font-weight: 600;
    line-height: 1.35;
  }

  /* ── Card ─────────────────────────────────────────────────────────── */
  .ds-card {
    background: var(--ds-surface);
    border: 1px solid var(--ds-border);
    border-radius: var(--ds-radius-md);
    padding: var(--ds-space-5);
  }

  .ds-card__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: var(--ds-space-3);
    padding: var(--ds-space-4) var(--ds-space-5);
    border-bottom: 1px solid var(--ds-border);
  }

  .ds-card__title {
    margin: 0;
    color: var(--ds-text);
    font-size: 0.9375rem;
    font-weight: 600;
    line-height: 1.35;
  }

  .ds-card__body { padding: var(--ds-space-5); }

  /* ── Buttons ──────────────────────────────────────────────────────── */
  .ds-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--ds-space-2);
    min-height: var(--ds-control-h);
    padding: 0 var(--ds-space-4);
    border: 1px solid var(--ds-accent);
    border-radius: var(--ds-radius-sm);
    background: var(--ds-accent);
    color: #ffffff;
    font: 500 var(--ds-text-base) / 1.2 var(--ds-font-sans);
    text-decoration: none;
    white-space: nowrap;
    transition: background var(--ds-dur-2) var(--ds-ease), border-color var(--ds-dur-2) var(--ds-ease), color var(--ds-dur-2) var(--ds-ease);
  }

  .ds-btn:hover { background: var(--ds-accent-strong); border-color: var(--ds-accent-strong); }
  .ds-btn--secondary { background: var(--ds-surface-2); border-color: var(--ds-border-strong); color: var(--ds-text); }
  .ds-btn--secondary:hover { background: var(--ds-surface-hover); border-color: var(--ds-border-strong); }
  .ds-btn--ghost { background: transparent; border-color: transparent; color: var(--ds-text-muted); }
  .ds-btn--ghost:hover { background: var(--ds-surface-2); border-color: transparent; color: var(--ds-text); }
  .ds-btn--danger { background: transparent; border-color: var(--ds-danger-border); color: var(--ds-danger-text); }
  .ds-btn--danger:hover { background: var(--ds-danger-soft); border-color: var(--ds-danger); }
  .ds-btn--sm { min-height: var(--ds-control-h-sm); padding: 0 var(--ds-space-3); font-size: var(--ds-text-sm); }
  .ds-btn:disabled, .ds-btn[aria-disabled="true"] { opacity: 0.55; }

  /* ── Forms ────────────────────────────────────────────────────────── */
  .ds-input, .ds-select, .ds-textarea {
    width: 100%;
    min-height: var(--ds-control-h);
    padding: 8px 12px;
    border: 1px solid var(--ds-input-border);
    border-radius: var(--ds-radius-sm);
    background: var(--ds-surface-3);
    color: var(--ds-text);
    font: 400 var(--ds-text-base) / var(--ds-leading-snug) var(--ds-font-sans);
    transition: border-color var(--ds-dur-2) var(--ds-ease), box-shadow var(--ds-dur-2) var(--ds-ease);
  }

  .ds-textarea { min-height: 96px; resize: vertical; line-height: var(--ds-leading-normal); }
  .ds-input::placeholder, .ds-textarea::placeholder { color: var(--ds-text-dim); }

  .ds-input:focus, .ds-select:focus, .ds-textarea:focus {
    border-color: var(--ds-accent);
    box-shadow: var(--ds-focus-ring);
    outline: none;
  }

  .ds-label {
    display: block;
    margin-bottom: 6px;
    color: var(--ds-text-secondary);
    font: 500 var(--ds-text-sm) / 1.35 var(--ds-font-sans);
  }

  .ds-help {
    margin-top: var(--ds-space-1);
    color: var(--ds-text-muted);
    font-size: var(--ds-text-xs);
    line-height: var(--ds-leading-snug);
  }

  .ds-field-error {
    margin-top: var(--ds-space-1);
    color: var(--ds-danger-text);
    font-size: var(--ds-text-xs);
    line-height: var(--ds-leading-snug);
  }

  /* ── Badges — compact status labels, not capsules ─────────────────── */
  .ds-badge {
    display: inline-flex;
    align-items: center;
    gap: var(--ds-space-1);
    padding: 2px 8px;
    border: 1px solid var(--ds-border-strong);
    border-radius: var(--ds-radius-xs);
    background: var(--ds-surface-2);
    color: var(--ds-text-secondary);
    font: 600 var(--ds-text-xs) / 1.4 var(--ds-font-sans);
    white-space: nowrap;
  }

  .ds-badge--info { color: var(--ds-accent-text); border-color: var(--ds-accent-border); background: var(--ds-accent-soft); }
  .ds-badge--success { color: var(--ds-success-text); border-color: var(--ds-success-border); background: var(--ds-success-soft); }
  .ds-badge--warning { color: var(--ds-warning-text); border-color: var(--ds-warning-border); background: var(--ds-warning-soft); }
  .ds-badge--danger { color: var(--ds-danger-text); border-color: var(--ds-danger-border); background: var(--ds-danger-soft); }

  /* ── Alerts ───────────────────────────────────────────────────────── */
  .ds-alert {
    padding: 12px 16px;
    border: 1px solid var(--ds-accent-border);
    border-radius: var(--ds-radius-sm);
    background: var(--ds-accent-soft);
    color: #dbeafe;
    font-size: var(--ds-text-base);
    line-height: var(--ds-leading-normal);
  }

  .ds-alert--success { border-color: var(--ds-success-border); background: var(--ds-success-soft); color: #d1fae5; }
  .ds-alert--warning { border-color: var(--ds-warning-border); background: var(--ds-warning-soft); color: #fef3c7; }
  .ds-alert--danger { border-color: var(--ds-danger-border); background: var(--ds-danger-soft); color: #fee2e2; }

  /* ── Tables ───────────────────────────────────────────────────────── */
  .ds-table-scroll {
    width: 100%;
    max-width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .ds-table { width: 100%; border-collapse: collapse; }

  .ds-table th {
    padding: 10px 14px;
    border-bottom: 1px solid var(--ds-border);
    background: var(--ds-surface-3);
    color: var(--ds-text-muted);
    font-size: var(--ds-text-xs);
    font-weight: 600;
    text-align: left;
    white-space: nowrap;
  }

  .ds-table td {
    padding: 12px 14px;
    border-bottom: 1px solid var(--ds-border);
    color: var(--ds-text-secondary);
    font-size: var(--ds-text-base);
    vertical-align: middle;
  }

  .ds-table tbody tr:last-child td { border-bottom: 0; }
  .ds-table tbody tr:hover td { background: rgba(255, 255, 255, 0.02); }

  /* ── Pagination (vendor/pagination views) ─────────────────────────── */
  .admin-pagination {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: var(--ds-space-3);
    font-family: var(--ds-font-sans);
  }

  .admin-pagination__summary { color: var(--ds-text-muted); font-size: var(--ds-text-sm); line-height: 1.5; }
  .admin-pagination__summary strong { color: var(--ds-text); font-weight: 600; }
  .admin-pagination__links { display: flex; align-items: center; flex-wrap: wrap; gap: 6px; }

  .admin-page-link {
    min-width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 8px;
    border: 1px solid var(--ds-border-strong);
    border-radius: var(--ds-radius-sm);
    background: var(--ds-surface-2);
    color: var(--ds-text-secondary);
    font-size: var(--ds-text-sm);
    font-weight: 500;
    line-height: 1;
    text-decoration: none;
    transition: background var(--ds-dur-1) ease, border-color var(--ds-dur-1) ease, color var(--ds-dur-1) ease;
  }

  a.admin-page-link:hover { background: var(--ds-surface-hover); color: var(--ds-text); }
  .admin-page-link.is-current { border-color: var(--ds-accent); background: var(--ds-accent); color: #ffffff; }
  .admin-page-link.is-disabled { opacity: 0.45; cursor: not-allowed; }

  @media (max-width: 640px) {
    .admin-pagination { align-items: flex-start; flex-direction: column; }
  }

  /* ── Empty state ──────────────────────────────────────────────────── */
  .ds-empty {
    padding: var(--ds-space-8) var(--ds-space-5);
    color: var(--ds-text-muted);
    font-size: var(--ds-text-base);
    line-height: var(--ds-leading-normal);
    text-align: center;
  }

  .ds-empty strong { display: block; margin-bottom: var(--ds-space-1); color: var(--ds-text); font-weight: 600; }

  /* ── Dialogs ──────────────────────────────────────────────────────── */
  .ds-modal-overlay {
    position: fixed;
    inset: 0;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--ds-space-4);
    background: var(--ds-overlay);
    overflow-y: auto;
  }

  .ds-modal {
    width: min(560px, 100%);
    max-height: calc(100vh - 32px);
    max-height: calc(100dvh - 32px);
    overflow-y: auto;
    background: var(--ds-surface);
    border: 1px solid var(--ds-border-strong);
    border-radius: var(--ds-radius-lg);
    box-shadow: var(--ds-shadow-lg);
  }

  /* ── Skeletons for data that arrives after the page ──────────────── */
  .ds-skeleton {
    display: block;
    border-radius: var(--ds-radius-xs);
    background: linear-gradient(90deg, var(--ds-surface-2) 25%, #22304a 37%, var(--ds-surface-2) 63%);
    background-size: 400% 100%;
    animation: ds-skeleton 1.4s ease infinite;
  }

  .ds-skeleton + .ds-skeleton { margin-top: var(--ds-space-2); }
  .ds-skeleton--text { height: 12px; }
  .ds-skeleton--title { height: 18px; width: 45%; }
  .ds-skeleton--block { height: 96px; }

  @keyframes ds-skeleton {
    from { background-position: 100% 50%; }
    to { background-position: 0 50%; }
  }

  /* Buttons that trigger work show it, and cannot be pressed twice. */
  [data-ds-loading="true"] {
    position: relative;
    pointer-events: none;
    color: transparent !important;
  }

  [data-ds-loading="true"] > * {
    visibility: hidden;
  }

  [data-ds-loading="true"]::after {
    content: "";
    position: absolute;
    inset: 50% auto auto 50%;
    width: 16px;
    height: 16px;
    margin: -8px 0 0 -8px;
    border: 2px solid currentColor;
    border-top-color: transparent;
    border-radius: 50%;
    opacity: 0.85;
    color: #ffffff;
    animation: ds-spin 0.6s linear infinite;
  }

  @keyframes ds-spin {
    to { transform: rotate(360deg); }
  }

  .ds-visually-hidden {
    position: absolute !important;
    width: 1px;
    height: 1px;
    margin: -1px;
    padding: 0;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
  }

  @media (max-width: 640px) {
    .ds-page-title { font-size: 1.25rem; }
    .ds-page-header { align-items: flex-start; margin-bottom: var(--ds-space-5); }
    .ds-page-actions { width: 100%; }
    .ds-card__head, .ds-card__body { padding-left: var(--ds-space-4); padding-right: var(--ds-space-4); }
  }

  /* Movement follows intent, and stops entirely when the viewer asks for that. */
  @media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
      animation-duration: 0.001ms !important;
      animation-iteration-count: 1 !important;
      transition-duration: 0.001ms !important;
      scroll-behavior: auto !important;
    }
  }
</style>

<script id="datasensei-design-system-script">
(() => {
  if (window.__dataSenseiDesignSystem) return;
  window.__dataSenseiDesignSystem = true;

  // Any form that leaves the page shows progress on the button that started it,
  // and cannot be submitted twice. Pages that manage their own button state opt
  // out with data-ds-no-loading.
  const markLoading = (button) => {
    if (!(button instanceof HTMLElement) || button.disabled || button.dataset.dsLoading === 'true') return;
    if (button.closest('[data-ds-no-loading]') || button.hasAttribute('data-ds-no-loading')) return;

    const width = button.getBoundingClientRect().width;
    if (width) button.style.minWidth = `${Math.round(width)}px`;
    button.dataset.dsLoading = 'true';
    button.setAttribute('aria-busy', 'true');
    // Let the page's own submit handlers run first; a cancelled submit is
    // released again by the pageshow handler below.
    window.setTimeout(() => {
      if (button.dataset.dsLoading === 'true' && button.tagName === 'BUTTON') button.disabled = true;
    }, 0);
  };

  const release = () => {
    document.querySelectorAll('[data-ds-loading="true"]').forEach((button) => {
      button.dataset.dsLoading = 'false';
      button.removeAttribute('aria-busy');
      button.style.minWidth = '';
      if (button.tagName === 'BUTTON') button.disabled = false;
    });
  };

  document.addEventListener('submit', (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || form.hasAttribute('data-ds-no-loading')) return;
    // A page that handles the submit itself (AJAX) keeps its own button state.
    if (event.defaultPrevented) return;
    if (form.getAttribute('target') === '_blank') return;

    const submitter = event.submitter
      || form.querySelector('button[type="submit"]:not([disabled])')
      || form.querySelector('button:not([type]):not([disabled])');
    markLoading(submitter);
  });

  // Back/forward navigation restores the page from cache: never leave a button spinning.
  window.addEventListener('pageshow', release);
  window.addEventListener('pagehide', release);

  // Wide server-rendered tables scroll inside their own box on small screens
  // instead of pushing the whole page sideways. Cells and data are untouched.
  const containTables = () => {
    document.querySelectorAll('table').forEach((table) => {
      if (table.closest('.CodeMirror, .cm-editor, [contenteditable="true"], .ds-table-scroll, [data-ds-no-table-scroll]')) return;
      const parent = table.parentElement;
      if (!parent) return;
      const overflowX = window.getComputedStyle(parent).overflowX;
      const firstRow = table.rows && table.rows[0];
      const columns = firstRow ? Array.from(firstRow.cells).reduce((sum, cell) => sum + (cell.colSpan || 1), 0) : 0;
      if (columns >= 4) table.classList.add('ds-table--wide');
      if (overflowX === 'auto' || overflowX === 'scroll') return;
      const wrapper = document.createElement('div');
      wrapper.className = 'ds-table-scroll';
      table.before(wrapper);
      wrapper.appendChild(table);
    });
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', containTables, { once: true });
  } else {
    containTables();
  }
})();
</script>
@endonce
