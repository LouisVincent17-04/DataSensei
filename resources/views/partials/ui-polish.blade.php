@once
<style id="datasensei-ui-polish">
  :root {
    --ds-focus: rgba(59, 130, 246, 0.45);
    --ds-card-shadow: 0 18px 50px rgba(0, 0, 0, 0.18);
    --ds-soft-shadow: 0 12px 32px rgba(0, 0, 0, 0.14);
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
      border-radius: 14px !important;
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
