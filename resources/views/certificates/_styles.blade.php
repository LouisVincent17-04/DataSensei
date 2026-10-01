{{--
    Shared styles of the certificate pages (DataSensei Updates 13): the
    builder form, the preview frame and the plain status words. Colours come
    from the design system, so they follow dark and light mode; the
    certificate itself is always printed on white.
--}}
@once
<style id="ds-certificate-pages">
  .ct-form { display: grid; gap: 18px; }
  .ct-section { border: 1px solid var(--border); border-radius: var(--radius); background: var(--surface); }
  .ct-section > h2 { margin: 0; padding: 14px 18px; border-bottom: 1px solid var(--border); color: var(--text); font-size: .9375rem; font-weight: 600; }
  .ct-section-body { display: grid; gap: 14px; padding: 16px 18px 18px; }
  .ct-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px 18px; }
  @media (max-width: 800px) { .ct-grid { grid-template-columns: minmax(0, 1fr); } }
  .ct-field { display: grid; gap: 6px; min-width: 0; }
  .ct-field > label, .ct-field > .ct-label { color: var(--ds-text-secondary); font-size: .8125rem; font-weight: 500; }
  .ct-input, .ct-select, .ct-textarea { width: 100%; min-height: var(--ds-control-h, 38px); padding: 8px 12px; border: 1px solid var(--ds-input-border); border-radius: var(--radius-sm); background: var(--surface3); color: var(--text); font: 400 .875rem/1.45 var(--ds-font-sans); color-scheme: var(--ds-color-scheme, dark); }
  .ct-select { height: var(--ds-control-h, 38px); padding-top: 0; padding-bottom: 0; }
  .ct-textarea { min-height: 92px; resize: vertical; }
  .ct-input:focus, .ct-select:focus, .ct-textarea:focus { outline: none; border-color: var(--accent); box-shadow: var(--ds-focus-ring); }
  .ct-input[readonly] { background: var(--surface2); color: var(--muted); }
  .ct-input.is-invalid, .ct-select.is-invalid, .ct-textarea.is-invalid { border-color: var(--ds-danger); }
  .ct-error { margin: 0; color: var(--ds-danger-text); font-size: .8125rem; }
  .ct-help { margin: 0; color: var(--muted); font-size: .8125rem; line-height: 1.5; }
  .ct-placeholders { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 4px 18px; margin: 0; padding: 0; list-style: none; font-size: .8125rem; }
  @media (max-width: 800px) { .ct-placeholders { grid-template-columns: minmax(0, 1fr); } }
  .ct-placeholders li { display: flex; gap: 8px; align-items: baseline; color: var(--muted); }
  .ct-placeholders code { color: var(--text); font-family: var(--ds-font-mono); font-size: .75rem; }
  .ct-link-button { padding: 0; border: 0; background: none; color: var(--ds-accent-text); font: inherit; cursor: pointer; }
  .ct-link-button:hover { text-decoration: underline; }
  .ct-layouts { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 12px; }
  .ct-layout { display: grid; gap: 8px; padding: 10px; border: 1px solid var(--border); border-radius: var(--radius-sm); background: var(--surface2); cursor: pointer; }
  .ct-layout:has(input:checked) { border-color: var(--accent); box-shadow: 0 0 0 1px var(--accent); }
  .ct-layout input { margin: 0 6px 0 0; accent-color: var(--accent); }
  .ct-layout-name { color: var(--text); font-size: .875rem; font-weight: 600; }
  .ct-layout-desc { color: var(--muted); font-size: .75rem; line-height: 1.45; }
  .ct-thumb { border: 1px solid var(--border); background: #ffffff; line-height: 0; }
  .ct-thumb svg, .ct-frame svg { display: block; width: 100%; height: auto; }
  .ct-frame { border: 1px solid var(--ds-border-strong); background: #ffffff; line-height: 0; }
  .ct-actions { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
  .ct-actions form { display: inline; }
  .ct-btn { display: inline-flex; align-items: center; justify-content: center; min-height: var(--ds-control-h, 38px); padding: 0 14px; border: 1px solid var(--accent); border-radius: var(--radius-sm); background: var(--accent); color: #fff; font: 500 .875rem/1.2 var(--ds-font-sans); text-decoration: none; cursor: pointer; white-space: nowrap; }
  .ct-btn:hover { background: var(--accent-hover); border-color: var(--accent-hover); }
  .ct-btn-secondary { border-color: var(--ds-border-strong); background: var(--surface2); color: var(--text); }
  .ct-btn-secondary:hover { background: var(--ds-surface-hover); border-color: var(--ds-border-strong); }
  .ct-btn-danger { border-color: var(--ds-danger-border); background: transparent; color: var(--ds-danger-text); }
  .ct-btn-danger:hover { background: var(--ds-danger-soft); border-color: var(--ds-danger); }
  .ct-btn[disabled] { opacity: .5; cursor: not-allowed; }
  .ct-dl { display: grid; grid-template-columns: minmax(140px, 200px) minmax(0, 1fr); gap: 8px 16px; margin: 0; font-size: .875rem; }
  .ct-dl dt { color: var(--muted); }
  .ct-dl dd { margin: 0; color: var(--text); overflow-wrap: anywhere; }
  .ct-good { color: var(--ds-success-text); }
  .ct-warn { color: var(--ds-warning-text); }
  .ct-bad { color: var(--ds-danger-text); }
  .ct-muted { color: var(--muted); }
  .ct-table { width: 100%; border-collapse: collapse; font-size: .875rem; }
  .ct-table th { padding: 9px 14px; background: var(--surface3); color: var(--muted); font-size: .75rem; font-weight: 600; text-align: left; white-space: nowrap; }
  .ct-table td { padding: 10px 14px; border-top: 1px solid var(--border); color: var(--ds-text-secondary); vertical-align: top; }
  .ct-table td:first-child { color: var(--text); }
  .ct-table-wrap { overflow-x: auto; }
  .ct-empty { padding: 14px 18px; color: var(--muted); font-size: .875rem; }
  @media print {
    body * { visibility: hidden !important; }
    .ct-printable, .ct-printable * { visibility: visible !important; }
    .ct-printable { position: absolute; inset: 0; border: 0; }
  }
</style>
@endonce
