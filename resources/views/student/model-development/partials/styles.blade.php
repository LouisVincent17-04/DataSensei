<style>
/*
 * Model Development (student pages and the instructor overview).
 * Colours, type, radius and fonts come from partials.design-system; the
 * --ml-* names are kept as aliases because page markup still references them.
 */
:root {
  --ml-bg: var(--ds-bg);
  --ml-card: var(--ds-surface);
  --ml-card2: var(--ds-surface-2);
  --ml-border: var(--ds-border);
  --ml-text: var(--ds-text);
  --ml-muted: var(--ds-text-muted);
  --ml-blue: var(--ds-accent);
  --ml-blue2: var(--ds-accent-strong);
  --ml-green: var(--ds-success);
  --ml-yellow: var(--ds-warning);
  --ml-red: var(--ds-danger);
  --ml-radius: var(--ds-radius-md);
}

*, *::before, *::after { box-sizing: border-box; }
body { margin: 0; background: var(--bg); color: var(--text); font: 400 .875rem/1.5 var(--ds-font-sans); }

/* ── Page frame ────────────────────────────────────────────────── */
.ml-layout { display: flex; min-height: 100vh; }
.ml-main { flex: 1 1 auto; min-width: 0; padding: 28px 32px 48px; }
.ml-main [hidden] { display: none !important; }

.ml-head { display: flex; align-items: flex-end; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px; }
.ml-head > :first-child { min-width: 0; flex: 1 1 320px; }
.ml-title { margin: 0; }
.ml-subtitle { margin: 4px 0 0; max-width: 72ch; color: var(--muted); font-size: .875rem; line-height: 1.55; }
.ml-subtitle-row { display: flex; align-items: center; flex-wrap: wrap; gap: 6px 12px; margin-top: 4px; }
.ml-subtitle-row .ml-subtitle { margin: 0; }
.ml-subtitle + .ml-subtitle-row { margin-top: 10px; }
.ml-muted { margin: 0; color: var(--muted); font-size: .875rem; line-height: 1.55; }
.ml-muted strong { color: var(--ds-text-secondary); }
.ml-help { margin-top: 4px; color: var(--muted); font-size: .75rem; line-height: 1.5; }
.ml-help strong { color: var(--ds-text-secondary); font-weight: 600; }
.ml-main code { padding: 1px 5px; border-radius: var(--radius-xs); background: var(--surface2); color: var(--ds-text-secondary); font-size: .8125rem; }

/* ── Buttons ───────────────────────────────────────────────────── */
.ml-actions { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; }
.ml-btn {
  display: inline-flex; align-items: center; justify-content: center; gap: 8px;
  min-height: 38px; padding: 8px 16px;
  border: 1px solid var(--accent); border-radius: var(--radius-sm);
  background: var(--accent); color: #fff;
  font: 500 .875rem/1.2 var(--ds-font-sans); text-align: center; text-decoration: none;
  cursor: pointer;
  transition: background-color .12s ease, border-color .12s ease, color .12s ease;
}
.ml-btn:hover { background: var(--accent-hover); border-color: var(--accent-hover); }
.ml-btn.secondary { background: var(--surface2); border-color: var(--ds-border-strong); color: var(--text); }
.ml-btn.secondary:hover { background: var(--ds-surface-hover); border-color: var(--ds-border-strong); }
.ml-btn.danger { background: transparent; border-color: var(--ds-danger-border); color: var(--ds-danger-text); }
.ml-btn.danger:hover { background: var(--ds-danger-soft); border-color: var(--ds-danger); }
.ml-btn.small { min-height: 32px; padding: 6px 12px; font-size: .8125rem; }
.ml-btn:disabled, .ml-btn[aria-disabled="true"] { opacity: .55; cursor: not-allowed; }

.ml-link-btn {
  min-height: 32px; padding: 0 10px; border: 0; border-radius: var(--radius-sm);
  background: none; color: var(--ds-accent-text); font: 500 .8125rem/1.2 var(--ds-font-sans); cursor: pointer;
  transition: background-color .12s ease, color .12s ease;
}
.ml-link-btn:hover { background: var(--surface2); color: var(--text); }

/* ── Cards, sections, grids ────────────────────────────────────── */
.ml-card { min-width: 0; padding: 20px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); }
/* A card inside a card reads as an inset group, not a second card. */
.ml-card .ml-card:not(.ml-algorithm) { padding: 16px; background: var(--surface3); border-radius: var(--radius-sm); }
.ml-section { margin: 24px 0; }
.ml-section-head { display: flex; align-items: flex-end; justify-content: space-between; flex-wrap: wrap; gap: 8px 16px; margin-bottom: 12px; }
.ml-section-head > :first-child { min-width: 0; flex: 1 1 auto; }
.ml-section-head > .ml-badge, .ml-section-head > .ml-btn, .ml-section-head > .ml-quality { flex-shrink: 0; }
.ml-card > .ml-section-head { align-items: flex-start; }
.ml-section-title { margin: 0 0 4px; color: var(--text); font-size: 1rem; font-weight: 600; line-height: 1.35; }
.ml-card .ml-section-title { font-size: .9375rem; }
.ml-section-head .ml-section-title:only-child { margin-bottom: 0; }

.ml-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
.ml-grid.two { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.ml-grid.four { grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
.ml-grid > * { min-width: 0; }

.ml-dataset-choice { display: flex; flex-direction: column; }
.ml-dataset-choice .ml-section-head { margin-bottom: 8px; }
.ml-dataset-choice .ml-section-title { margin: 0; }
.ml-dataset-choice .ml-actions { margin-top: auto; }
.ml-dataset-choice .ml-recommended-line { margin: 0 0 16px; color: var(--ds-text-secondary); font-size: .8125rem; line-height: 1.5; }

.ml-history-section { margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--border); }

/* Label and value pairs inside a card. */
.ml-meta { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; margin: 16px 0; }
.ml-meta > div { min-width: 0; padding: 10px 12px; background: var(--surface3); border: 1px solid var(--border); border-radius: var(--radius-sm); }
.ml-meta span { display: block; color: var(--muted); font-size: .75rem; font-weight: 500; line-height: 1.4; }
.ml-meta strong { display: block; margin-top: 2px; color: var(--text); font-size: .875rem; font-weight: 600; line-height: 1.4; overflow-wrap: anywhere; font-variant-numeric: tabular-nums; }

/* Summary figures. */
.ml-card.ml-stat { display: flex; flex-direction: column; gap: 4px; padding: 16px 18px; }
.ml-stat span { color: var(--muted); font-size: .8125rem; font-weight: 500; line-height: 1.4; }
.ml-stat strong { display: block; color: var(--text); font-size: 1.5rem; font-weight: 700; line-height: 1.2; letter-spacing: -.02em; font-variant-numeric: tabular-nums; overflow-wrap: anywhere; }
.ml-stat small { display: block; color: var(--muted); font-size: .75rem; line-height: 1.45; }
.ml-stat .ml-better { margin-top: 2px; font-size: .75rem; }
.ml-quality { color: var(--text); font-size: 1.5rem; font-weight: 700; line-height: 1.2; letter-spacing: -.02em; font-variant-numeric: tabular-nums; white-space: nowrap; }

.ml-list { margin: 8px 0 0; padding-left: 18px; color: var(--ds-text-secondary); font-size: .8125rem; line-height: 1.6; }
.ml-list li + li { margin-top: 2px; }

/* ── Badges and status ─────────────────────────────────────────── */
.ml-badge {
  display: inline-flex; align-items: center; gap: 6px; padding: 2px 8px;
  border: 1px solid var(--ds-border-strong); border-radius: var(--radius-xs);
  background: var(--surface2); color: var(--ds-text-secondary);
  font-size: .75rem; font-weight: 600; line-height: 1.4; white-space: nowrap;
}
.ml-badge.good { background: var(--ds-success-soft); border-color: var(--ds-success-border); color: var(--ds-success-text); }
.ml-badge.warn { background: var(--ds-warning-soft); border-color: var(--ds-warning-border); color: var(--ds-warning-text); }
.ml-badge.bad { background: var(--ds-danger-soft); border-color: var(--ds-danger-border); color: var(--ds-danger-text); }
.ml-badge.blue { background: var(--ds-accent-soft); border-color: var(--ds-accent-border); color: var(--ds-accent-text); }
.ml-badge-row { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }
.ml-badge-row:not(:has(.ml-badge:not([hidden]))) { display: none; }

.ml-status-dot { display: inline-block; width: 8px; height: 8px; margin-right: 8px; border-radius: 50%; background: var(--muted); vertical-align: 1px; }
.ml-badge .ml-status-dot { margin-right: 0; }
.ml-status-dot.completed, .ml-status-dot.ready { background: var(--ds-success); }
.ml-status-dot.running, .ml-status-dot.queued, .ml-status-dot.retrying { background: var(--ds-warning); }
.ml-status-dot.failed { background: var(--ds-danger); }

.ml-type-chip {
  display: inline-block; margin: 4px 4px 0 0; padding: 1px 6px;
  border: 1px solid var(--ds-border-strong); border-radius: var(--radius-xs);
  color: var(--muted); font-size: .75rem; font-weight: 500; line-height: 1.4;
}
.ml-type-chip.num { background: var(--ds-accent-soft); border-color: var(--ds-accent-border); color: var(--ds-accent-text); }
.ml-type-chip.cat { background: var(--surface2); color: var(--ds-text-secondary); }

/* ── Alerts and notes ──────────────────────────────────────────── */
.ml-alert {
  margin-bottom: 16px; padding: 12px 16px;
  border: 1px solid var(--ds-success-border); border-radius: var(--radius-sm);
  background: var(--ds-success-soft); color: #d1fae5; font-size: .875rem; line-height: 1.55;
}
.ml-alert.error { border-color: var(--ds-danger-border); background: var(--ds-danger-soft); color: #fee2e2; }
.ml-inline-note { margin: 12px 0; padding: 10px 12px; border: 1px solid var(--border); border-radius: var(--radius-sm); background: var(--surface3); color: var(--ds-text-secondary); font-size: .8125rem; line-height: 1.55; }
.ml-inline-note strong { color: var(--text); }

/* ── Tables ────────────────────────────────────────────────────── */
.ml-table-wrap { max-width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); }
.ml-table { width: 100%; border-collapse: collapse; font-variant-numeric: tabular-nums; }
.ml-table th, .ml-table td { padding: 12px 14px; text-align: left; border-bottom: 1px solid var(--border); white-space: nowrap; }
.ml-table th { padding: 10px 14px; background: var(--surface3); color: var(--muted); font-size: .75rem; font-weight: 600; }
.ml-table td { color: var(--ds-text-secondary); font-size: .875rem; vertical-align: middle; }
.ml-table td strong { color: var(--text); font-weight: 600; }
.ml-table td[colspan] { white-space: normal; }
.ml-table td.ml-muted { color: var(--muted); }
.ml-table tbody tr:hover td { background: rgba(255, 255, 255, .02); }
.ml-table tbody tr:last-child td { border-bottom: 0; }
.ml-table .ml-actions { flex-wrap: nowrap; }

.ml-cm-wrap { max-width: 100%; overflow-x: auto; }
.ml-cm { margin-top: 12px; border-collapse: separate; border-spacing: 4px; font-variant-numeric: tabular-nums; }
.ml-cm th { padding: 4px 8px; color: var(--muted); font-size: .75rem; font-weight: 600; text-align: center; white-space: nowrap; }
.ml-cm th.row { text-align: right; }
.ml-cm td { min-width: 52px; height: 40px; text-align: center; background: var(--surface3); border: 1px solid var(--border); border-radius: var(--radius-sm); color: var(--ds-text-secondary); font-size: .875rem; font-weight: 600; }
.ml-cm td.hit { background: var(--ds-success-soft); border-color: var(--ds-success-border); color: var(--ds-success-text); }
.ml-cm td.miss { background: var(--ds-danger-soft); border-color: var(--ds-danger-border); color: var(--ds-danger-text); }

.ml-chart { display: block; width: 100%; max-height: 520px; object-fit: contain; background: #fff; border: 1px solid var(--border); border-radius: var(--radius-sm); }
.ml-chart-caption { margin: 8px 0 0; color: var(--muted); font-size: .8125rem; line-height: 1.5; }

/* ── Forms ─────────────────────────────────────────────────────── */
.ml-field { margin-bottom: 16px; }
.ml-label { display: block; margin-bottom: 6px; color: var(--ds-text-secondary); font-size: .8125rem; font-weight: 500; line-height: 1.35; }
.ml-input, .ml-select, .ml-file {
  width: 100%; min-height: 38px; padding: 8px 12px;
  background: var(--surface3); border: 1px solid var(--ds-input-border); border-radius: var(--radius-sm);
  color: var(--text); font: 400 .875rem/1.4 var(--ds-font-sans);
  transition: border-color .12s ease, box-shadow .12s ease;
}
.ml-input::placeholder { color: var(--dim); }
.ml-input:focus, .ml-select:focus, .ml-file:focus { outline: none; border-color: var(--accent); box-shadow: var(--ds-focus-ring); }
.ml-file { padding: 6px 8px; }
.ml-file::file-selector-button {
  margin-right: 12px; min-height: 26px; padding: 0 10px;
  border: 1px solid var(--ds-border-strong); border-radius: var(--radius-xs);
  background: var(--surface2); color: var(--text); font: 500 .8125rem var(--ds-font-sans); cursor: pointer;
}
.ml-range { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 2px 8px; margin-top: 4px; color: var(--muted); font-size: .75rem; line-height: 1.45; }
.ml-range-warn { display: none; margin-top: 4px; color: var(--ds-warning-text); font-size: .75rem; line-height: 1.45; }
.ml-range-warn.visible { display: block; }

.ml-advanced { padding: 12px 16px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); }
.ml-card .ml-advanced { background: var(--surface3); border-radius: var(--radius-sm); }
.ml-advanced > summary { cursor: pointer; color: var(--text); font-size: .875rem; font-weight: 600; line-height: 1.5; }
.ml-advanced[open] > summary { margin-bottom: 8px; }

/* Selectable tiles: feature checkboxes, problem types and algorithms. */
.ml-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin-top: 16px; }
.ml-counter { margin-left: auto; color: var(--muted); font-size: .8125rem; line-height: 1.45; }
.ml-counter strong { color: var(--text); font-weight: 600; font-variant-numeric: tabular-nums; }

.ml-check-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 8px; }
.ml-check {
  display: flex; align-items: flex-start; gap: 10px; min-width: 0; padding: 10px 12px;
  background: var(--surface3); border: 1px solid var(--border); border-radius: var(--radius-sm);
  color: var(--ds-text-secondary); font-size: .8125rem; line-height: 1.45; cursor: pointer;
  transition: border-color .12s ease, background-color .12s ease;
}
.ml-check > input { flex: none; margin: 2px 0 0; }
.ml-check > span { min-width: 0; overflow-wrap: anywhere; }
.ml-check strong { color: var(--text); font-weight: 600; }
.ml-check:has(input:checked) { border-color: var(--ds-accent-border); background: var(--ds-accent-soft); }
.ml-check:has(input:disabled) { opacity: .55; cursor: not-allowed; }
.ml-check.is-target { background: transparent; }
.ml-check-reason { display: block; margin-top: 4px; color: var(--ds-danger-text); font-size: .75rem; }
.ml-check-reason:empty { display: none; }

.ml-card.ml-algorithm { display: block; padding: 16px; background: var(--surface3); border-radius: var(--radius-sm); cursor: pointer; transition: border-color .12s ease, background-color .12s ease; }
.ml-algorithm:hover { border-color: var(--border-hover); }
.ml-algorithm:has(input:checked) { border-color: var(--accent); background: var(--ds-accent-soft); }
.ml-choice-top { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
.ml-choice-top strong { min-width: 0; color: var(--text); font-size: .875rem; font-weight: 600; }
.ml-choice-top input { flex: none; margin: 0; }
.ml-algo-analogy { margin: 8px 0 0; color: var(--ds-text-secondary); font-size: .8125rem; line-height: 1.5; }
.ml-algo-more { margin-top: 10px; }
.ml-algo-more summary { cursor: pointer; color: var(--ds-accent-text); font-size: .8125rem; font-weight: 500; }
.ml-algo-more summary:hover { color: var(--text); }
.ml-mini-title { display: block; margin-top: 8px; color: var(--text); font-size: .75rem; font-weight: 600; }
.ml-mini-list { margin: 4px 0 0; padding-left: 16px; color: var(--muted); font-size: .75rem; line-height: 1.5; }

/* ── Step header and beginner guide ────────────────────────────── */
.ml-step-head { display: flex; align-items: flex-start; gap: 12px 14px; }
.ml-step-disc {
  flex: none; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;
  border: 1px solid var(--ds-accent-border); border-radius: var(--radius-sm);
  background: var(--ds-accent-soft); color: var(--ds-accent-text);
  font-size: .875rem; font-weight: 600; font-variant-numeric: tabular-nums;
}
.ml-step-head-copy { flex: 1 1 auto; min-width: 0; }
.ml-step-head-copy .ml-section-title { margin: 4px 0; font-size: 1.125rem; }
.ml-step-count { flex: none; padding-top: 6px; color: var(--muted); font-size: .8125rem; white-space: nowrap; font-variant-numeric: tabular-nums; }

.ml-guide { margin-top: 16px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); }
.ml-card .ml-guide, .ml-saved-milestone .ml-guide { background: var(--surface3); border-radius: var(--radius-sm); }
.ml-guide > summary {
  list-style: none; display: flex; align-items: center; gap: 12px; min-height: 44px; padding: 10px 16px;
  color: var(--text); font-size: .875rem; font-weight: 600; line-height: 1.4; cursor: pointer;
}
.ml-guide > summary::-webkit-details-marker { display: none; }
.ml-guide > summary::after { content: "Show"; flex: none; margin-left: auto; color: var(--ds-accent-text); font-size: .8125rem; font-weight: 500; }
.ml-guide > summary:hover::after { color: var(--text); }
.ml-guide[open] > summary { border-bottom: 1px solid var(--border); }
.ml-guide[open] > summary::after { content: "Hide"; }
.ml-guide-body { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px 24px; padding: 16px; }
.ml-guide-item { min-width: 0; }
.ml-guide-item h4 { margin: 0 0 4px; color: var(--text); font-size: .8125rem; font-weight: 600; }
.ml-guide-item p { margin: 0; color: var(--ds-text-secondary); font-size: .8125rem; line-height: 1.55; }
.ml-guide-item.wide { grid-column: 1 / -1; }
.ml-guide-item.tip h4 { color: var(--ds-success-text); }
.ml-guide-item.warn h4 { color: var(--ds-warning-text); }

.ml-phases { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 8px; margin-top: 16px; }
.ml-phase { min-width: 0; padding: 14px 16px; background: var(--surface3); border: 1px solid var(--border); border-radius: var(--radius-sm); }
.ml-phase h3 { display: flex; align-items: center; flex-wrap: wrap; gap: 4px 8px; margin: 0 0 6px; color: var(--text); font-size: .875rem; font-weight: 600; }
.ml-phase b { display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; border-radius: var(--radius-xs); background: var(--ds-accent-soft); color: var(--ds-accent-text); font-size: .75rem; font-weight: 600; }
.ml-phase small { margin-left: auto; color: var(--muted); font-size: .75rem; font-weight: 500; }
.ml-phase p { margin: 0; color: var(--muted); font-size: .8125rem; line-height: 1.5; }

.ml-starter { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 16px; align-items: center; margin-top: 16px; }
.ml-starter h3 { margin: 0 0 4px; color: var(--text); font-size: .9375rem; font-weight: 600; }
.ml-resume { border-color: var(--ds-warning-border); }

/* ── Roadmap (ten steps) ───────────────────────────────────────── */
.ml-roadmap-layout { display: grid; grid-template-columns: minmax(240px, 272px) minmax(0, 1fr); gap: 24px; align-items: start; }
.ml-roadmap-column, .ml-roadmap-content { min-width: 0; }
.ml-roadmap-panel { padding: 16px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); }
.ml-roadmap-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
.ml-roadmap-heading > div { min-width: 0; }
.ml-roadmap-heading .ml-roadmap-title { display: block; color: var(--text); font-size: .9375rem; font-weight: 600; }
.ml-roadmap-sub { display: block; margin-top: 2px; color: var(--muted); font-size: .75rem; }
.ml-roadmap-percent { color: var(--text); font-size: 1.125rem; font-weight: 700; line-height: 1.3; letter-spacing: -.02em; font-variant-numeric: tabular-nums; }
.ml-roadmap-progress { margin: 12px 0 10px; }
.ml-roadmap-position { margin: 0 0 12px; color: var(--ds-text-secondary); font-size: .8125rem; font-weight: 500; }

.ml-roadmap-list { list-style: none; margin: 0; padding: 0; }
.ml-roadmap-item { position: relative; padding-bottom: 4px; }
.ml-roadmap-item:last-child { padding-bottom: 0; }
.ml-roadmap-item::after { content: ""; position: absolute; left: 21px; top: 40px; bottom: -4px; width: 2px; background: var(--border); }
.ml-roadmap-item:last-child::after { display: none; }
.ml-roadmap-action {
  position: relative; z-index: 1; width: 100%; margin: 0; padding: 8px;
  display: grid; grid-template-columns: 28px minmax(0, 1fr); gap: 10px; align-items: start;
  border: 1px solid transparent; border-radius: var(--radius-sm); background: transparent;
  color: inherit; font: inherit; text-align: left; text-decoration: none;
  transition: background-color .12s ease, border-color .12s ease;
}
.ml-roadmap-item button.ml-roadmap-action { cursor: pointer; }
.ml-roadmap-item button.ml-roadmap-action:disabled { cursor: not-allowed; }
.ml-roadmap-action[aria-disabled="true"] { pointer-events: none; }
.ml-roadmap-item:is(.complete, .locked) :is(a, button).ml-roadmap-action:not([aria-disabled="true"]):not(:disabled):hover { background: var(--surface2); }
.ml-roadmap-step-number {
  width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;
  border: 1px solid var(--ds-border-strong); border-radius: var(--radius-sm); background: var(--surface3);
  color: var(--muted); font-size: .75rem; font-weight: 600; font-variant-numeric: tabular-nums;
}
.ml-roadmap-copy { min-width: 0; }
.ml-roadmap-copy strong { display: block; color: var(--text); font-size: .8125rem; font-weight: 600; line-height: 1.4; }
.ml-roadmap-copy small { display: block; margin-top: 2px; color: var(--muted); font-size: .75rem; line-height: 1.4; }
.ml-roadmap-state { display: block; margin-top: 4px; color: var(--muted); font-size: .75rem; font-weight: 500; line-height: 1.4; }

.ml-roadmap-item.current .ml-roadmap-action,
.ml-roadmap-item.current-complete .ml-roadmap-action { border-color: var(--ds-accent-border); background: var(--ds-accent-soft); }
.ml-roadmap-item.current .ml-roadmap-step-number { border-color: var(--accent); background: var(--accent); color: #fff; }
.ml-roadmap-item.current .ml-roadmap-state { color: var(--ds-accent-text); }
/* The label for the current step arrives in capitals from the server and the
   roadmap script; show it in sentence case like the other labels. */
.ml-roadmap-item.current .ml-roadmap-state { text-transform: lowercase; }
.ml-roadmap-item.current .ml-roadmap-state::first-letter { text-transform: capitalize; }
.ml-roadmap-item.complete .ml-roadmap-step-number,
.ml-roadmap-item.current-complete .ml-roadmap-step-number { border-color: var(--ds-success-border); background: var(--ds-success-soft); color: var(--ds-success-text); }
.ml-roadmap-item.complete .ml-roadmap-state,
.ml-roadmap-item.current-complete .ml-roadmap-state { color: var(--ds-success-text); }
.ml-roadmap-item.error .ml-roadmap-action { border-color: var(--ds-danger-border); background: var(--ds-danger-soft); }
.ml-roadmap-item.error .ml-roadmap-step-number { border-color: var(--ds-danger-border); background: var(--ds-danger-soft); color: var(--ds-danger-text); }
.ml-roadmap-item.error .ml-roadmap-state { color: var(--ds-danger-text); }
.ml-roadmap-item.locked .ml-roadmap-action { opacity: .6; }

/* ── Progress ──────────────────────────────────────────────────── */
.ml-progress { height: 6px; overflow: hidden; border-radius: 999px; background: var(--surface2); }
.ml-progress > div { height: 100%; border-radius: inherit; background: var(--accent); transition: width .4s ease; }

/* ── Wizard ────────────────────────────────────────────────────── */
.ml-workflow-step { display: none; }
.ml-workflow-step.active { display: block; }
.ml-step-error { display: none; margin-top: 16px; }
.ml-step-error.visible { display: block; }
.ml-step-navigation { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-top: 16px; }
.ml-step-navigation .ml-actions { margin: 0; }
.ml-step-navigation .ml-help { margin: 0; }

.ml-recommended-inline, .ml-recommended-model {
  padding: 14px 16px; border: 1px solid var(--ds-accent-border); border-radius: var(--radius-sm); background: var(--ds-accent-soft);
}
.ml-recommended-inline { margin: 8px 0; }
.ml-recommended-inline > strong { color: var(--text); font-size: .875rem; font-weight: 600; }
.ml-recommended-model { display: block; }
.ml-recommended-model.is-mismatch { display: none; }
.ml-recommended-model-head { display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 8px 16px; }
.ml-recommended-model-head > div { min-width: 0; flex: 1 1 240px; }
.ml-recommended-model h3 { margin: 0; color: var(--text); font-size: .9375rem; font-weight: 600; line-height: 1.4; }
.ml-recommended-model p { margin: 8px 0 12px; color: var(--ds-text-secondary); font-size: .8125rem; line-height: 1.6; }
.ml-recommended-model p strong { color: var(--text); }

.ml-insight {
  display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 12px 16px;
  margin-top: 16px; padding: 14px 16px; background: var(--surface3); border: 1px solid var(--border); border-radius: var(--radius-sm);
}
.ml-insight[hidden] { display: none; }
.ml-insight-cell { min-width: 0; }
.ml-insight-cell span { display: block; color: var(--muted); font-size: .75rem; font-weight: 500; }
.ml-insight-cell strong { display: block; margin-top: 2px; color: var(--text); font-size: .875rem; font-weight: 600; overflow-wrap: anywhere; }
.ml-insight-note { grid-column: 1 / -1; padding-top: 10px; border-top: 1px solid var(--border); color: var(--ds-text-secondary); font-size: .8125rem; line-height: 1.55; }
.ml-insight-note:empty { display: none; }
.ml-insight-note.warn { color: var(--ds-warning-text); }

.ml-split-visual { margin-top: 16px; }
.ml-split-bar { display: flex; height: 32px; overflow: hidden; border-radius: var(--radius-sm); background: var(--surface2); }
.ml-split-train { display: flex; align-items: center; padding: 0 12px; overflow: hidden; background: var(--ds-accent); color: #fff; font-size: .75rem; font-weight: 600; white-space: nowrap; transition: width .24s ease; }
.ml-split-test { flex: 1 1 auto; min-width: 0; display: flex; align-items: center; justify-content: flex-end; padding: 0 12px; overflow: hidden; background: var(--ds-success); color: var(--ds-bg); font-size: .75rem; font-weight: 600; white-space: nowrap; }
.ml-split-legend { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 4px 12px; margin-top: 8px; color: var(--muted); font-size: .8125rem; }
.ml-split-legend strong { color: var(--text); font-weight: 600; font-variant-numeric: tabular-nums; }

.ml-review-list { display: grid; gap: 8px; margin: 16px 0; }
.ml-review-item {
  display: grid; grid-template-columns: 22px minmax(0, 1fr) auto; gap: 12px; align-items: center; padding: 10px 12px;
  background: var(--surface3); border: 1px solid var(--border); border-radius: var(--radius-sm);
}
.ml-review-mark {
  width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center;
  border: 1px solid var(--ds-success-border); border-radius: var(--radius-xs); background: var(--ds-success-soft);
  color: var(--ds-success-text); font-size: .75rem; font-weight: 600;
}
.ml-review-item.bad { border-color: var(--ds-danger-border); }
.ml-review-item.bad .ml-review-mark { border-color: var(--ds-danger-border); background: var(--ds-danger-soft); color: var(--ds-danger-text); }
.ml-review-label { display: block; color: var(--muted); font-size: .75rem; font-weight: 500; }
.ml-review-value { display: block; margin-top: 2px; color: var(--text); font-size: .875rem; font-weight: 600; overflow-wrap: anywhere; }

.ml-selection-summary { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; margin: 16px 0; }
.ml-summary-row { min-width: 0; padding: 10px 12px; background: var(--surface3); border: 1px solid var(--border); border-radius: var(--radius-sm); }
.ml-summary-row > span { display: block; color: var(--muted); font-size: .75rem; font-weight: 500; }
.ml-summary-row strong { display: block; margin-top: 2px; color: var(--text); font-size: .875rem; font-weight: 600; overflow-wrap: anywhere; }
.ml-summary-row.full { grid-column: 1 / -1; }
.ml-summary-list { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 6px; }
.ml-summary-list span {
  display: inline-flex; align-items: center; max-width: 100%; padding: 2px 8px;
  border: 1px solid var(--ds-border-strong); border-radius: var(--radius-xs); background: var(--surface2);
  color: var(--ds-text-secondary); font-size: .75rem; font-weight: 500; line-height: 1.4; overflow-wrap: anywhere;
}
.ml-review-value .ml-summary-list { margin-top: 4px; }

.ml-training-ready {
  display: flex; align-items: flex-start; gap: 10px; margin: 16px 0; padding: 12px 16px;
  border: 1px solid var(--ds-success-border); border-radius: var(--radius-sm); background: var(--ds-success-soft); color: #d1fae5;
}
.ml-training-ready-icon { flex: none; color: var(--ds-success-text); font-weight: 600; line-height: 1.5; }
.ml-training-ready .ml-help { color: var(--ds-text-secondary); }

/* ── Training job ──────────────────────────────────────────────── */
.ml-training-stage-list { display: grid; gap: 8px; margin: 16px 0; }
.ml-training-stage {
  display: grid; grid-template-columns: 20px minmax(0, 1fr); gap: 10px; align-items: start; padding: 10px 12px;
  background: var(--surface3); border: 1px solid var(--border); border-radius: var(--radius-sm);
  color: var(--ds-text-secondary); font-size: .8125rem; font-weight: 500; line-height: 1.45;
}
.ml-training-stage-marker {
  width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center;
  border: 1px solid var(--ds-border-strong); border-radius: var(--radius-xs);
  color: var(--muted); font-size: .75rem; font-weight: 600; font-variant-numeric: tabular-nums;
}
.ml-stage-copy { display: block; margin-top: 2px; color: var(--muted); font-size: .75rem; font-weight: 400; }
.ml-training-stage.complete .ml-training-stage-marker { border-color: var(--ds-success-border); background: var(--ds-success-soft); color: var(--ds-success-text); }
.ml-training-stage.current { border-color: var(--ds-accent-border); background: var(--ds-accent-soft); color: var(--text); }
.ml-training-stage.current .ml-training-stage-marker { border-color: var(--accent); background: var(--accent); color: #fff; }
.ml-training-stage.current .ml-stage-copy { color: var(--ds-text-secondary); }
.ml-training-stage.error { border-color: var(--ds-danger-border); color: var(--ds-danger-text); }
.ml-training-stage.error .ml-training-stage-marker { border-color: var(--ds-danger-border); background: var(--ds-danger-soft); color: var(--ds-danger-text); }

.ml-wait-note { display: none; margin-top: 12px; }
.ml-wait-note.visible { display: block; }
.ml-alert.ml-wait-note:not(.error) { border-color: var(--ds-accent-border); background: var(--ds-accent-soft); color: #dbeafe; }
.ml-fix-list { margin: 8px 0 0; padding-left: 18px; font-size: .8125rem; line-height: 1.6; }

.ml-success-milestone { display: none; margin-top: 16px; padding: 16px 20px; border: 1px solid var(--ds-success-border); border-radius: var(--radius); background: var(--ds-success-soft); }
.ml-success-milestone.visible { display: block; }
.ml-success-title { margin: 0; color: var(--ds-success-text); font-size: 1rem; font-weight: 600; }
.ml-success-metrics { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8px; margin: 16px 0; }
.ml-success-metric { min-width: 0; padding: 10px 12px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-sm); }
.ml-success-metric span { display: block; color: var(--muted); font-size: .75rem; font-weight: 500; }
.ml-success-metric strong { display: block; margin-top: 2px; color: var(--text); font-size: 1rem; font-weight: 600; font-variant-numeric: tabular-nums; overflow-wrap: anywhere; }

/* ── Results (steps 8 to 10) ───────────────────────────────────── */
.ml-result-section { margin-bottom: 20px; }
.ml-result-section[hidden] { display: none !important; }

.ml-verdict {
  display: grid; grid-template-columns: auto minmax(0, 1fr); gap: 16px 24px; align-items: center; padding: 20px;
  background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius);
}
.ml-verdict-score { min-width: 112px; display: flex; flex-direction: column; gap: 2px; padding-right: 24px; border-right: 1px solid var(--border); }
.ml-verdict-score strong { color: var(--text); font-size: 1.75rem; font-weight: 700; line-height: 1.15; letter-spacing: -.02em; font-variant-numeric: tabular-nums; }
.ml-verdict-score span { color: var(--muted); font-size: .8125rem; font-weight: 500; }
.ml-verdict.strong .ml-verdict-score strong { color: var(--ds-success-text); }
.ml-verdict.fair .ml-verdict-score strong { color: var(--ds-warning-text); }
.ml-verdict.weak .ml-verdict-score strong { color: var(--ds-danger-text); }
.ml-verdict h3 { margin: 0 0 6px; color: var(--text); font-size: 1rem; font-weight: 600; }
.ml-verdict p { margin: 0; color: var(--ds-text-secondary); font-size: .875rem; line-height: 1.55; }
.ml-verdict ul { margin: 8px 0 0; padding-left: 18px; color: var(--ds-text-secondary); font-size: .8125rem; line-height: 1.6; }
.ml-verdict-foot { margin-top: 8px; color: var(--muted); font-size: .75rem; line-height: 1.5; }
.ml-better { display: block; color: var(--muted); font-size: .75rem; font-weight: 500; }

.ml-saved-milestone { padding: 20px; border: 1px solid var(--ds-success-border); border-radius: var(--radius); background: var(--ds-success-soft); }
.ml-saved-milestone .ml-muted { color: var(--ds-text-secondary); }
.ml-recap { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px 16px; margin: 16px 0 0; padding: 0; list-style: none; }
.ml-recap li { min-width: 0; color: var(--ds-text-secondary); font-size: .8125rem; line-height: 1.5; }
.ml-recap li::before { content: "✓"; margin-right: 8px; color: var(--ds-success-text); font-weight: 600; }

.ml-readiness { display: grid; gap: 8px; margin: 12px 0 0; padding: 0; list-style: none; }
.ml-readiness li { display: flex; align-items: flex-start; gap: 10px; color: var(--ds-text-secondary); font-size: .8125rem; line-height: 1.5; }
.ml-readiness li > span { min-width: 0; }
.ml-readiness i {
  flex: none; width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center;
  border: 1px solid var(--ds-border-strong); border-radius: var(--radius-xs);
  font-size: .75rem; font-style: normal; font-weight: 600; font-variant-numeric: tabular-nums;
}
.ml-readiness i.ok { border-color: var(--ds-success-border); background: var(--ds-success-soft); color: var(--ds-success-text); }
.ml-readiness i.warn { border-color: var(--ds-warning-border); background: var(--ds-warning-soft); color: var(--ds-warning-text); }

/* ── Responsive ────────────────────────────────────────────────── */
/* Beside the roadmap the content column is narrow: fewer, wider columns. */
@media (max-width: 1500px) {
  .ml-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .ml-roadmap-content .ml-section.ml-grid.two,
  .ml-roadmap-content .ml-history-section .ml-grid.two { grid-template-columns: minmax(0, 1fr); }
}
@media (max-width: 1280px) {
  .ml-grid.four, .ml-success-metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 1100px) {
  .ml-roadmap-layout { grid-template-columns: minmax(0, 1fr); gap: 20px; }
  .ml-roadmap-list { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); grid-template-rows: repeat(5, auto); grid-auto-flow: column; grid-auto-columns: minmax(0, 1fr); gap: 4px 12px; }
  .ml-roadmap-item, .ml-roadmap-item:last-child { padding: 0; }
  .ml-roadmap-item::after { display: none; }
  .ml-roadmap-action { height: 100%; }
  .ml-section.ml-grid.two { grid-template-columns: minmax(0, 1fr); }
}
@media (max-width: 900px) {
  .ml-main { padding: 24px 20px 40px; }
}
@media (max-width: 760px) {
  .ml-guide-body, .ml-recap { grid-template-columns: minmax(0, 1fr); }
  .ml-guide-item.wide { grid-column: auto; }
  .ml-starter { grid-template-columns: minmax(0, 1fr); }
  .ml-counter { width: 100%; margin-left: 0; }
}
@media (max-width: 640px) {
  .ml-main { padding: 20px 16px 32px; }
  .ml-head { flex-direction: column; align-items: stretch; margin-bottom: 20px; }
  .ml-head > :first-child { flex-basis: auto; }
  .ml-head > .ml-actions, .ml-head > form, .ml-head > .ml-btn { width: 100%; }
  .ml-head .ml-actions .ml-btn { flex: 1 1 auto; }
  .ml-card, .ml-roadmap-panel { padding: 16px; }
  .ml-grid, .ml-grid.two, .ml-selection-summary { grid-template-columns: minmax(0, 1fr); }
  .ml-step-count { display: none; }
  .ml-verdict { grid-template-columns: minmax(0, 1fr); padding: 16px; }
  .ml-verdict-score { padding: 0 0 12px; border-right: 0; border-bottom: 1px solid var(--border); }
  .ml-summary-row.full { grid-column: auto; }
  .ml-step-navigation { flex-direction: column; align-items: stretch; }
  .ml-step-navigation > .ml-btn, .ml-step-navigation .ml-actions, .ml-step-navigation .ml-actions .ml-btn { width: 100%; }
  .ml-starter .ml-btn { width: 100%; }
}
@media (max-width: 560px) {
  .ml-roadmap-list { grid-template-columns: minmax(0, 1fr); grid-template-rows: none; grid-auto-flow: row; }
  .ml-success-metrics { grid-template-columns: minmax(0, 1fr); }
}
@media (max-width: 420px) {
  .ml-grid.four { grid-template-columns: minmax(0, 1fr); }
}
@media (prefers-reduced-motion: reduce) {
  .ml-btn, .ml-link-btn, .ml-check, .ml-algorithm, .ml-roadmap-action, .ml-split-train, .ml-progress > div { transition: none; }
}

/* ══ Simplified four-step flow ═════════════════════════════════════ */

/* Step strip across the top of every page. */
.ml-steps { max-width: 1080px; margin: 0 0 20px; }
.ml-steps ol {
  display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 0; margin: 0; padding: 0; list-style: none;
  background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius);
  box-shadow: inset 0 1px 0 rgba(255,255,255,.035), var(--ds-shadow-md);
  overflow: hidden;
}
.ml-steps-item { min-width: 0; box-shadow: inset 1px 0 0 var(--border); }
.ml-steps-item:first-child { box-shadow: none; }
.ml-steps-link { display: flex; align-items: flex-start; gap: 12px; height: 100%; padding: 14px 16px; color: inherit; text-decoration: none; }
.ml-steps-link[href]:hover { background: var(--ds-surface-hover); }
.ml-steps-link:focus-visible { outline: none; box-shadow: inset var(--ds-focus-ring); }
.ml-steps-num { flex: none; padding-top: 1px; color: var(--ds-text-dim); font: 600 .8125rem/1.4 var(--ds-font-mono); }
.ml-steps-copy { min-width: 0; }
.ml-steps-copy strong { display: block; color: var(--muted); font-size: .875rem; font-weight: 600; line-height: 1.35; }
.ml-steps-copy small { display: block; margin-top: 2px; color: var(--ds-text-dim); font-size: .75rem; line-height: 1.4; }
.ml-steps-item.complete .ml-steps-num { color: var(--ds-success-text); }
.ml-steps-item.complete .ml-steps-copy strong { color: var(--ds-text-secondary); }
.ml-steps-item.complete .ml-steps-copy small { color: var(--ds-success-text); }
.ml-steps-item.current { background: var(--ds-accent-soft); box-shadow: inset 0 -2px 0 var(--accent), inset 1px 0 0 var(--border); }
.ml-steps-item.current .ml-steps-num { color: var(--ds-accent-text); }
.ml-steps-item.current .ml-steps-copy strong { color: var(--text); }
.ml-steps-item.current .ml-steps-copy small { color: var(--ds-accent-text); }
.ml-steps-item.error { box-shadow: inset 0 -2px 0 var(--ds-danger), inset 1px 0 0 var(--border); }
.ml-steps-item.error .ml-steps-num, .ml-steps-item.error .ml-steps-copy small { color: var(--ds-danger-text); }

/* The old two-column frame is now a single column. */
.ml-flow { display: block; min-width: 0; max-width: 1080px; }
.ml-flow > * + * { margin-top: 16px; }
.ml-flow .ml-guide-body { display: block; padding: 14px 16px; }
.ml-flow .ml-guide-body p { margin: 0; color: var(--ds-text-secondary); font-size: .8125rem; line-height: 1.6; }
.ml-flow .ml-guide-body p + p { margin-top: 8px; }
.ml-flow .ml-guide-body strong { color: var(--text); font-weight: 600; }

/* Cards with a little lift so the page reads as layered surfaces. */
.ml-raised { box-shadow: inset 0 1px 0 rgba(255,255,255,.04), var(--ds-shadow-md); }
.ml-plain-tag { color: var(--muted); font-size: .75rem; font-weight: 600; letter-spacing: .01em; }
.ml-plain-tag.good { color: var(--ds-success-text); }
.ml-plain-tag.warn { color: var(--ds-warning-text); }
.ml-plain-tag.bad { color: var(--ds-danger-text); }

/* Glossary words: dotted underline, definition on hover, focus or tap. */
.ml-term { position: relative; display: inline; }
.ml-term-word {
  display: inline; margin: 0; padding: 0; border: 0; background: none; color: inherit; font: inherit; text-align: inherit;
  text-decoration: underline dotted var(--ds-accent-text); text-decoration-thickness: 1.5px; text-underline-offset: 3px; cursor: help;
}
.ml-term-word:hover, .ml-term-word[aria-expanded="true"] { color: var(--ds-accent-text); }
.ml-term-word:focus-visible { outline: none; border-radius: 3px; box-shadow: var(--ds-focus-ring); }
.ml-term-tip {
  position: absolute; z-index: 60; left: 0; top: calc(100% + 8px); width: max-content; max-width: min(300px, 78vw);
  padding: 10px 12px; border: 1px solid var(--ds-border-strong); border-radius: var(--radius-sm);
  background: #0b1526; color: var(--ds-text-secondary); box-shadow: var(--ds-shadow-lg);
  font: 400 .8125rem/1.5 var(--ds-font-sans); letter-spacing: 0; text-align: left; text-transform: none; white-space: normal;
  display: none; /* a hidden box would still widen the page on a phone */
}
.ml-term .ml-term-tip b { display: block; margin: 0 0 2px; color: var(--text); font-size: .8125rem; font-weight: 600; line-height: 1.4; letter-spacing: 0; }
.ml-term.flip .ml-term-tip { left: auto; right: 0; }
.ml-term.above .ml-term-tip { top: auto; bottom: calc(100% + 8px); }
.ml-term:hover .ml-term-tip, .ml-term:focus-within .ml-term-tip, .ml-term.open .ml-term-tip { display: block; }

/* Dataset cards on the start page. */
.ml-data-card { display: flex; flex-direction: column; gap: 10px; }
.ml-data-card h3 { margin: 0; color: var(--text); font-size: 1rem; font-weight: 600; line-height: 1.35; }
.ml-data-card .ml-data-name { color: var(--muted); font-size: .8125rem; }
.ml-data-card .ml-data-facts { margin: 0; color: var(--ds-text-secondary); font-size: .8125rem; line-height: 1.55; }
.ml-data-card .ml-actions { margin-top: auto; padding-top: 6px; }
.ml-data-card:hover { border-color: var(--ds-border-strong); }

/* Set-up page. */
.ml-setup-block + .ml-setup-block { margin-top: 16px; }
.ml-setup-q { display: flex; align-items: baseline; gap: 10px; margin: 0 0 4px; color: var(--text); font-size: 1.0625rem; font-weight: 600; line-height: 1.35; }
.ml-setup-q .n { flex: none; color: var(--ds-accent-text); font: 600 .8125rem/1 var(--ds-font-mono); }
.ml-answer-note { margin-top: 12px; padding: 12px 14px; border-left: 3px solid var(--accent); border-radius: 0 var(--radius-sm) var(--radius-sm) 0; background: var(--surface3); color: var(--ds-text-secondary); font-size: .8125rem; line-height: 1.55; }
.ml-answer-note strong { color: var(--text); }
.ml-answer-note.warn { border-left-color: var(--ds-warning); }
.ml-method { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; margin-top: 12px; }
.ml-method-choice { position: relative; display: block; padding: 14px 16px 14px 44px; border: 1px solid var(--border); border-radius: var(--radius-sm); background: var(--surface3); cursor: pointer; transition: border-color .12s ease, background-color .12s ease; }
.ml-method-choice:hover { border-color: var(--ds-border-strong); }
.ml-method-choice input { position: absolute; left: 16px; top: 17px; width: 16px; height: 16px; margin: 0; accent-color: var(--accent); }
.ml-method-choice strong { display: block; color: var(--text); font-size: .875rem; font-weight: 600; }
.ml-method-choice span { display: block; margin-top: 2px; color: var(--muted); font-size: .8125rem; line-height: 1.5; }
.ml-method-choice:has(input:checked) { border-color: var(--ds-accent-border); background: var(--ds-accent-soft); box-shadow: inset 0 1px 0 rgba(255,255,255,.04); }
.ml-train-bar { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px 16px; }
.ml-train-bar .ml-btn { min-height: 44px; padding: 10px 22px; font-size: .9375rem; font-weight: 600; box-shadow: 0 1px 0 rgba(255,255,255,.18) inset, 0 6px 14px -6px rgba(37,99,235,.7); }
.ml-train-bar .ml-btn:active { transform: translateY(1px); box-shadow: 0 1px 0 rgba(255,255,255,.12) inset; }
.ml-col-reason { display: block; margin-top: 2px; color: var(--ds-warning-text); font-size: .75rem; line-height: 1.4; }
.ml-col-reason:empty { display: none; }

/* Results page. */
.ml-score { display: grid; grid-template-columns: auto minmax(0, 1fr); gap: 20px 28px; align-items: center; padding: 24px; border: 1px solid var(--border); border-radius: var(--radius); background: linear-gradient(180deg, var(--ds-surface-2), var(--surface)); box-shadow: inset 0 1px 0 rgba(255,255,255,.05), var(--ds-shadow-md); }
.ml-score-dial { position: relative; display: grid; place-items: center; width: 132px; height: 132px; border-radius: 50%; background: conic-gradient(var(--dial, var(--accent)) calc(var(--fill, 0) * 1%), var(--surface3) 0); box-shadow: 0 10px 22px -12px rgba(0,0,0,.8), inset 0 0 0 1px var(--border); }
.ml-score-dial::before { content: ""; position: absolute; inset: 12px; border-radius: 50%; background: var(--surface); box-shadow: inset 0 2px 6px rgba(0,0,0,.45); }
.ml-score-dial b { position: relative; color: var(--text); font-size: 1.875rem; font-weight: 700; letter-spacing: -.02em; line-height: 1; font-variant-numeric: tabular-nums; }
.ml-score-dial small { position: relative; display: block; margin-top: 4px; color: var(--muted); font-size: .6875rem; text-align: center; }
.ml-score-dial > span { position: relative; text-align: center; }
.ml-score.strong { --dial: var(--ds-success); }
.ml-score.fair { --dial: var(--ds-warning); }
.ml-score.weak { --dial: var(--ds-danger); }
.ml-score h2 { margin: 0 0 6px; color: var(--text); font-size: 1.375rem; font-weight: 700; letter-spacing: -.01em; line-height: 1.25; }
.ml-score p { margin: 0; color: var(--ds-text-secondary); font-size: .875rem; line-height: 1.6; }
.ml-score p + p { margin-top: 4px; }
.ml-score .ml-score-verdict { margin-top: 10px; font-weight: 600; }
.ml-score.strong .ml-score-verdict { color: var(--ds-success-text); }
.ml-score.fair .ml-score-verdict { color: var(--ds-warning-text); }
.ml-score.weak .ml-score-verdict { color: var(--ds-danger-text); }
.ml-mistakes { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; margin-top: 14px; }
.ml-mistake { padding: 14px 16px; border: 1px solid var(--border); border-radius: var(--radius-sm); background: var(--surface3); }
.ml-mistake b { display: block; color: var(--text); font-size: 1.5rem; font-weight: 700; line-height: 1.1; font-variant-numeric: tabular-nums; }
.ml-mistake > span { display: block; margin-top: 4px; color: var(--ds-text-secondary); font-size: .8125rem; line-height: 1.45; }
.ml-contest { margin: 12px 0 0; padding: 0; list-style: none; }
.ml-details-body > .ml-contest:first-child { margin-top: 0; }
.ml-contest li { display: grid; grid-template-columns: minmax(120px, 210px) minmax(0, 1fr) 64px; align-items: center; gap: 12px; padding: 7px 0; color: var(--ds-text-secondary); font-size: .8125rem; }
.ml-contest li + li { border-top: 1px solid var(--border); }
.ml-contest .bar { height: 8px; border-radius: 4px; background: var(--surface3); overflow: hidden; box-shadow: inset 0 1px 2px rgba(0,0,0,.4); }
.ml-contest .bar i { display: block; height: 100%; border-radius: 4px; background: var(--ds-border-strong); }
.ml-contest li.won { color: var(--text); font-weight: 600; }
.ml-contest li.won .bar i { background: var(--accent); }
.ml-contest .val { text-align: right; font-variant-numeric: tabular-nums; }
.ml-details { border: 1px solid var(--border); border-radius: var(--radius); background: var(--surface); }
.ml-details > summary { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 18px; color: var(--text); font-size: .9375rem; font-weight: 600; cursor: pointer; list-style: none; }
.ml-details > summary::-webkit-details-marker { display: none; }
.ml-details > summary::after { content: "Show"; color: var(--ds-accent-text); font-size: .8125rem; font-weight: 500; }
.ml-details[open] > summary::after { content: "Hide"; }
.ml-details[open] > summary { border-bottom: 1px solid var(--border); }
.ml-details > summary small { display: block; margin-top: 2px; color: var(--muted); font-size: .8125rem; font-weight: 400; }
.ml-details-body { padding: 18px; }
.ml-details-body > * + * { margin-top: 18px; }

/* Prediction page. */
.ml-answer { scroll-margin-top: 16px; padding: 22px 24px; border: 1px solid var(--ds-border-strong); border-radius: var(--radius); background: linear-gradient(180deg, var(--ds-surface-2), var(--surface)); box-shadow: inset 0 1px 0 rgba(255,255,255,.05), inset 4px 0 0 var(--tone, var(--accent)), var(--ds-shadow-lg); }
.ml-answer.alert { --tone: var(--ds-danger); }
.ml-answer.calm { --tone: var(--ds-success); }
.ml-answer-q { margin: 0; color: var(--muted); font-size: .875rem; }
.ml-answer h2 { margin: 4px 0 6px; color: var(--text); font-size: 1.75rem; font-weight: 700; letter-spacing: -.02em; line-height: 1.2; }
.ml-answer.alert h2 { color: var(--ds-danger-text); }
.ml-answer.calm h2 { color: var(--ds-success-text); }
.ml-answer p { margin: 0; color: var(--ds-text-secondary); font-size: .9375rem; line-height: 1.55; }
.ml-sure { margin-top: 16px; }
.ml-sure-head { display: flex; align-items: baseline; justify-content: space-between; gap: 12px; color: var(--ds-text-secondary); font-size: .8125rem; }
.ml-sure-head strong { color: var(--text); font-size: .9375rem; }
.ml-chances { margin: 10px 0 0; padding: 0; list-style: none; }
.ml-chances li { display: grid; grid-template-columns: minmax(110px, 220px) minmax(0, 1fr) 52px; align-items: center; gap: 12px; padding: 5px 0; color: var(--ds-text-secondary); font-size: .8125rem; }
.ml-chances .bar { height: 10px; border-radius: 5px; background: var(--surface3); overflow: hidden; box-shadow: inset 0 1px 2px rgba(0,0,0,.45); }
.ml-chances .bar i { display: block; height: 100%; border-radius: 5px; background: var(--ds-border-strong); transition: width .4s var(--ds-ease); }
.ml-chances li.chosen { color: var(--text); font-weight: 600; }
.ml-chances li.chosen .bar i { background: var(--tone, var(--accent)); }
.ml-chances .val { text-align: right; font-variant-numeric: tabular-nums; }
.ml-answer-caution { margin-top: 14px !important; padding-top: 12px; border-top: 1px solid var(--border); color: var(--muted) !important; font-size: .8125rem !important; }
.ml-examples { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; }
.ml-examples > span { color: var(--muted); font-size: .8125rem; }
.ml-example-note { margin-top: 10px; color: var(--ds-text-secondary); font-size: .8125rem; }
.ml-example-note strong { color: var(--text); }
.ml-field .ml-field-help { margin-top: 4px; color: var(--muted); font-size: .75rem; line-height: 1.45; }
.ml-field-range { display: flex; justify-content: space-between; gap: 8px; margin-top: 4px; color: var(--ds-text-dim); font-size: .75rem; font-variant-numeric: tabular-nums; }
.ml-field-name { color: var(--ds-text-dim); font: 400 .6875rem/1.4 var(--ds-font-mono); }

@media (max-width: 900px) {
  .ml-steps ol { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .ml-steps-item:nth-child(3) { box-shadow: inset 0 1px 0 var(--border); }
  .ml-steps-item:nth-child(4) { box-shadow: inset 1px 0 0 var(--border), inset 0 1px 0 var(--border); }
  .ml-steps-copy small { display: none; }
  .ml-method, .ml-mistakes { grid-template-columns: minmax(0, 1fr); }
  .ml-score { grid-template-columns: minmax(0, 1fr); justify-items: start; }
  .ml-contest li, .ml-chances li { grid-template-columns: minmax(0, 1fr) 56px; }
  .ml-contest .bar, .ml-chances .bar { grid-column: 1 / -1; order: 3; }
}
</style>

@once
<script id="datasensei-ml-term-script">
(() => {
    // Glossary words open on hover and keyboard focus through CSS alone. This adds
    // tap-to-toggle for touch screens, Escape to close, and keeps the box on screen.
    const closeAll = except => document.querySelectorAll('[data-ml-term].open').forEach(term => {
        if (term === except) return;
        term.classList.remove('open');
        term.querySelector('.ml-term-word')?.setAttribute('aria-expanded', 'false');
    });

    const place = term => {
        const tip = term.querySelector('.ml-term-tip');
        if (!tip) return;
        term.classList.remove('flip', 'above');
        const box = tip.getBoundingClientRect();
        if (box.right > window.innerWidth - 8) term.classList.add('flip');
        if (box.bottom > window.innerHeight - 8 && term.getBoundingClientRect().top > box.height + 16) term.classList.add('above');
    };

    document.addEventListener('click', event => {
        const word = event.target.closest('.ml-term-word');
        if (!word) { closeAll(null); return; }
        // A glossary word inside a <label> or <summary> must not also tick the box or toggle the section.
        event.preventDefault();
        event.stopPropagation();
        const term = word.closest('[data-ml-term]');
        const open = !term.classList.contains('open');
        closeAll(term);
        term.classList.toggle('open', open);
        word.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open) place(term);
    });
    document.addEventListener('mouseover', event => {
        const term = event.target.closest?.('[data-ml-term]');
        if (term) place(term);
    });
    document.addEventListener('focusin', event => {
        const term = event.target.closest?.('[data-ml-term]');
        if (term) place(term);
    });
    document.addEventListener('keydown', event => {
        if (event.key !== 'Escape') return;
        closeAll(null);
        if (document.activeElement?.classList.contains('ml-term-word')) document.activeElement.blur();
    });
})();
</script>
@endonce
