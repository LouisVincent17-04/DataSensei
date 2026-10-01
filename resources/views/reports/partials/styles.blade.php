{{--
    Styles of the report pages and Class Analytics (DataSensei Updates 8):
    tabs, filters, summary figures, simple bars and tables. Colours, type and
    radius come from partials.design-system.
--}}
@once
<style>
  /* Reports (DataSensei Updates 8). Colours, type and radius come from the design system. */
  .rp { display: grid; gap: 20px; min-width: 0; }
  .rp-nav { display: flex; flex-wrap: wrap; gap: 4px; border-bottom: 1px solid var(--border); }
  .rp-nav-link { position: relative; padding: 10px 12px; color: var(--muted); font-size: .875rem; font-weight: 500; text-decoration: none; white-space: nowrap; }
  .rp-nav-link:hover { color: var(--text); }
  .rp-nav-link.is-current { color: var(--text); }
  .rp-nav-link.is-current::after { content: ''; position: absolute; left: 8px; right: 8px; bottom: -1px; height: 2px; border-radius: 2px; background: var(--accent); }
  .rp-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
  .rp-head-text { min-width: 0; flex: 1 1 360px; }
  .rp-title { margin: 0 0 4px; color: var(--text); font-size: 1.0625rem; font-weight: 600; line-height: 1.35; }
  .rp-description { margin: 0; max-width: 90ch; color: var(--muted); font-size: .875rem; line-height: 1.55; }
  .rp-exports { display: flex; gap: 8px; flex-wrap: wrap; }
  .rp-btn { display: inline-flex; align-items: center; justify-content: center; min-height: var(--ds-control-h, 38px); padding: 0 14px; border: 1px solid var(--accent); border-radius: var(--radius-sm); background: var(--accent); color: #fff; font: 500 .875rem/1.2 var(--ds-font-sans); text-decoration: none; white-space: nowrap; cursor: pointer; }
  .rp-btn:hover { background: var(--accent-hover); border-color: var(--accent-hover); }
  .rp-btn-secondary { border-color: var(--ds-border-strong); background: var(--surface2); color: var(--text); }
  .rp-btn-secondary:hover { background: var(--ds-surface-hover); border-color: var(--ds-border-strong); }
  .rp-filters { display: flex; align-items: flex-end; gap: 12px; flex-wrap: wrap; padding: 16px; border: 1px solid var(--border); border-radius: var(--radius); background: var(--surface); }
  .rp-field { display: grid; gap: 6px; flex: 0 1 180px; min-width: 150px; }
  .rp-field-wide { flex: 1 1 240px; }
  .rp-field label { color: var(--ds-text-secondary); font-size: .8125rem; font-weight: 500; }
  .rp-input { width: 100%; min-height: var(--ds-control-h, 38px); padding: 0 12px; border: 1px solid var(--ds-input-border); border-radius: var(--radius-sm); background: var(--surface3); color: var(--text); font: 400 .875rem/1.4 var(--ds-font-sans); color-scheme: var(--ds-color-scheme, dark); }
  .rp-input:focus { outline: none; border-color: var(--accent); box-shadow: var(--ds-focus-ring); }
  .rp-filter-actions { display: flex; gap: 8px; }
  .rp-error { margin: 0; color: var(--ds-danger-text); font-size: .875rem; }
  .rp-summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; }
  .rp-summary-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
  .rp-summary-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
  @media (max-width: 1000px) { :is(.rp-summary-3, .rp-summary-4) { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
  @media (max-width: 520px) { :is(.rp-summary-3, .rp-summary-4) { grid-template-columns: minmax(0, 1fr); } }
  .rp-tile { display: grid; gap: 4px; align-content: start; padding: 16px 18px; border: 1px solid var(--border); border-radius: var(--radius); background: var(--surface); }
  .rp-tile-label { color: var(--muted); font-size: .8125rem; font-weight: 500; }
  .rp-tile-value { color: var(--text); font-size: 1.5rem; font-weight: 700; line-height: 1.2; letter-spacing: -.02em; font-variant-numeric: tabular-nums; }
  .rp-tile-note { color: var(--dim); font-size: .75rem; line-height: 1.45; }
  .rp-panel { min-width: 0; border: 1px solid var(--border); border-radius: var(--radius); background: var(--surface); }
  .rp-panel-head { display: flex; align-items: baseline; justify-content: space-between; gap: 12px; padding: 14px 18px 0; }
  .rp-panel-title { margin: 0; padding: 14px 18px 0; color: var(--text); font-size: .9375rem; font-weight: 600; }
  .rp-panel-head .rp-panel-title { padding: 0; }
  .rp-count { color: var(--muted); font-size: .8125rem; white-space: nowrap; }
  .rp-note { margin: 6px 18px 0; color: var(--muted); font-size: .8125rem; line-height: 1.5; }
  .rp-table-wrap { margin-top: 12px; overflow-x: auto; border-top: 1px solid var(--border); }
  .rp-table { width: 100%; border-collapse: collapse; font-variant-numeric: tabular-nums; }
  .rp-table th { padding: 10px 14px; background: var(--surface3); color: var(--muted); font-size: .75rem; font-weight: 600; text-align: left; white-space: nowrap; }
  .rp-table td { padding: 11px 14px; border-top: 1px solid var(--border); color: var(--ds-text-secondary); font-size: .875rem; text-align: left; vertical-align: top; }
  .rp-table :is(th, td):first-child { padding-left: 18px; }
  .rp-table :is(th, td):last-child { padding-right: 18px; }
  .rp-table td:first-child { color: var(--text); font-weight: 500; }
  .rp-table tbody tr:hover td { background: rgba(255, 255, 255, .02); }
  .rp-empty { color: var(--muted) !important; font-weight: 400 !important; }
  .rp-link { color: var(--ds-accent-text); text-decoration: none; }
  .rp-link:hover { text-decoration: underline; }
  .rp-good { color: var(--ds-success-text) !important; }
  .rp-warn { color: var(--ds-warning-text) !important; }
  .rp-bad { color: var(--ds-danger-text) !important; }
  .rp-pages { padding: 12px 18px; border-top: 1px solid var(--border); }
  .rp-bars { display: grid; gap: 10px; padding: 14px 18px 18px; }
  .rp-bar { display: grid; grid-template-columns: minmax(120px, 220px) minmax(0, 1fr) minmax(90px, auto); align-items: center; gap: 12px; font-size: .875rem; }
  .rp-bar-label { color: var(--ds-text-secondary); }
  .rp-bar-track { height: 8px; border-radius: 4px; background: var(--surface3); overflow: hidden; }
  .rp-bar-fill { display: block; height: 100%; border-radius: 4px; background: var(--accent); }
  .rp-fill-good { background: var(--ds-success); }
  .rp-fill-warn { background: var(--ds-warning); }
  .rp-fill-bad { background: var(--ds-danger); }
  .rp-bar-text { color: var(--text); font-variant-numeric: tabular-nums; text-align: right; }
  /* Class Analytics */
  .rp-meta { margin: 0; color: var(--muted); font-size: .8125rem; line-height: 1.5; }
  .rp-back { color: var(--ds-accent-text); font-size: .875rem; text-decoration: none; }
  .rp-back:hover { text-decoration: underline; }
  .rp-attention { display: grid; gap: 0; margin: 12px 0 0; padding: 0; list-style: none; border-top: 1px solid var(--border); }
  .rp-attention li { display: grid; grid-template-columns: minmax(160px, 260px) minmax(0, 1fr); gap: 12px; padding: 11px 18px; border-bottom: 1px solid var(--border); font-size: .875rem; }
  .rp-attention li:last-child { border-bottom: 0; }
  .rp-attention span { color: var(--ds-warning-text); }
  .rp-rules { margin: 6px 18px 0; padding-left: 18px; color: var(--muted); font-size: .8125rem; line-height: 1.6; }
  .rp-panel-body { padding: 14px 18px 18px; color: var(--ds-text-secondary); font-size: .875rem; line-height: 1.55; }
  .rp-classes { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px; }
  .rp-class { display: grid; gap: 8px; padding: 16px 18px; border: 1px solid var(--border); border-radius: var(--radius); background: var(--surface); color: inherit; text-decoration: none; }
  .rp-class:hover { border-color: var(--ds-border-strong); background: var(--ds-surface-hover); }
  .rp-class strong { color: var(--text); font-size: .9375rem; }
  .rp-class dl { display: grid; grid-template-columns: 1fr auto; gap: 4px 12px; margin: 0; font-size: .8125rem; }
  .rp-class dt { color: var(--muted); }
  .rp-class dd { margin: 0; color: var(--text); font-variant-numeric: tabular-nums; text-align: right; }
  .rp-split { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; }
  @media (max-width: 1000px) { .rp-split { grid-template-columns: minmax(0, 1fr); } }
  @media (max-width: 640px) {
    .rp-attention li { grid-template-columns: minmax(0, 1fr); gap: 2px; }
    .rp-field, .rp-field-wide { flex: 1 1 100%; }
    .rp-filter-actions, .rp-exports { width: 100%; }
    .rp-filter-actions .rp-btn, .rp-exports .rp-btn { flex: 1 1 0; }
    .rp-bar { grid-template-columns: minmax(0, 1fr) auto; }
    .rp-bar-track { grid-column: 1 / -1; grid-row: 2; }
    .rp-nav { flex-wrap: nowrap; overflow-x: auto; scrollbar-width: thin; }
  }
</style>
@endonce
