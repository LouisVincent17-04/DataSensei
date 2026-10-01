<style>
  /* Student assessments: My Classes and one class's assessments
     (DataSensei Updates 9). Colours, type and radius come from
     partials.design-system. Plain text statuses, no badges. */
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  html, body { min-height: 100%; font-family: var(--ds-font-sans); background: var(--bg); color: var(--text); }
  a { color: inherit; text-decoration: none; }

  .ds-shell { display: flex; min-height: 100vh; }
  .ds-main { flex: 1; min-width: 0; padding: 28px 32px 48px; }
  .wrap { max-width: 1200px; margin: 0 auto; }

  .mobile-header { display: none; align-items: center; gap: 10px; height: 52px; padding: 0 12px; padding-right: max(64px, env(safe-area-inset-right)); background: var(--surface); border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 900; }
  .hamburger { flex: 0 0 38px; width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; background: var(--surface2); border: 1px solid var(--ds-border-strong); border-radius: var(--radius-sm); color: var(--text); cursor: pointer; }
  .mobile-title { min-width: 0; flex: 1 1 auto; overflow: hidden; font-size: .9375rem; font-weight: 600; text-overflow: ellipsis; white-space: nowrap; }
  .sidebar-overlay { display: none; position: fixed; inset: 0; background: var(--ds-overlay); z-index: 950; }
  .sidebar-overlay.open { display: block; }

  .top-row { display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 16px; margin-bottom: 20px; }
  .top-row > div:first-child { min-width: 0; flex: 1 1 320px; }
  .page-subtitle { color: var(--muted); max-width: 72ch; margin-top: 4px; font-size: .875rem; line-height: 1.55; }
  .crumb { display: inline-block; margin-bottom: 8px; color: var(--ds-accent-text); font-size: .8125rem; font-weight: 500; }
  .crumb:hover { text-decoration: underline; }

  .card { border: 1px solid var(--border); background: var(--surface); border-radius: var(--radius-sm); overflow: hidden; margin-bottom: 16px; }
  .card-head { display: flex; align-items: baseline; justify-content: space-between; gap: 12px; padding: 14px 16px; border-bottom: 1px solid var(--border); }
  .card-head h2 { color: var(--text); font-size: .9375rem; font-weight: 600; line-height: 1.35; }
  .card-head span { color: var(--muted); font-size: .8125rem; }
  .card-empty { padding: 14px 16px; color: var(--muted); font-size: .875rem; }

  .btn { min-height: 34px; border-radius: var(--radius-sm); border: 1px solid var(--ds-border-strong); background: var(--surface2); color: var(--text); padding: 0 14px; font: 500 .875rem/1.2 var(--ds-font-sans); cursor: pointer; display: inline-flex; align-items: center; justify-content: center; white-space: nowrap; }
  .btn:hover { background: var(--ds-surface-hover); }
  .btn.primary { background: var(--accent); border-color: var(--accent); color: #fff; }
  .btn.primary:hover { background: var(--accent-hover); border-color: var(--accent-hover); }

  .table { width: 100%; border-collapse: collapse; }
  .table th, .table td { text-align: left; border-bottom: 1px solid var(--border); vertical-align: middle; }
  .table th { padding: 9px 16px; font-size: .75rem; font-weight: 600; color: var(--muted); background: var(--surface3); white-space: nowrap; }
  .table td { padding: 11px 16px; font-size: .875rem; color: var(--ds-text-secondary); }
  .table td strong { color: var(--text); font-weight: 600; overflow-wrap: anywhere; }
  .table td .sub { display: block; margin-top: 2px; font-size: .8125rem; color: var(--muted); }
  .table tbody tr:last-child td { border-bottom: none; }
  .table td.action { text-align: right; white-space: nowrap; }
  .table a.row-link:hover strong { color: var(--ds-accent-text); text-decoration: underline; }

  .state-good { color: var(--ds-success-text); }
  .state-warn { color: var(--ds-warning-text); }
  .state-bad { color: var(--ds-danger-text); }

  .summary-line { margin: 0 0 16px; color: var(--ds-text-secondary); font-size: .875rem; }
  .alert { border-radius: var(--radius-sm); padding: 12px 16px; font-size: .875rem; margin-bottom: 16px; border: 1px solid var(--ds-accent-border); background: var(--ds-accent-soft); color: var(--ds-accent-ink, #dbeafe); }
  .alert.success { background: var(--ds-success-soft); border-color: var(--ds-success-border); color: var(--ds-success-ink, #d1fae5); }
  .alert.danger { background: var(--ds-danger-soft); border-color: var(--ds-danger-border); color: var(--ds-danger-ink, #fee2e2); }

  @media (max-width: 900px) {
    .mobile-header { display: flex; }
    .ds-main { padding: 24px 20px 40px; }
  }
  @media (min-width: 901px) { .sidebar-overlay.open { display: none; } }
  @media (max-width: 720px) {
    .table thead { display: none; }
    .table, .table tbody { display: block; width: 100%; }
    .table tr { display: grid; grid-template-columns: minmax(0, 1fr); row-gap: 4px; padding: 12px 16px; border-bottom: 1px solid var(--border); }
    .table tr:last-child { border-bottom: none; }
    .table td { padding: 0; border: none; }
    .table td[data-label]:not(:first-child)::before { content: attr(data-label) ": "; color: var(--muted); }
    .table td.action { text-align: left; margin-top: 6px; }
    .table td.action .btn { width: 100%; }
  }
  @media (max-width: 640px) { .ds-main { padding: 20px 16px 32px; } }
</style>
