<style>
/* Table of Specifications pages (index, create, show, review).
   Colours, type and radius come from partials.design-system. */
:root{--good:var(--ds-success);--warn:var(--ds-warning);--bad:var(--ds-danger)}
*{box-sizing:border-box}
body{margin:0;font-family:var(--ds-font-sans);background:var(--bg);color:var(--text)}
.layout{display:flex;min-height:100vh}
.main{flex:1;min-width:0;padding:28px 32px 48px;background:var(--bg)}
.wrap{max-width:1380px;margin:0 auto}

/* page header */
.top{display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px;margin-bottom:24px}
.top > div:first-child{min-width:0;flex:1 1 320px}
.subtitle{margin:4px 0 0;max-width:72ch;color:var(--muted);font-size:.875rem;line-height:1.55}

/* panels: header row, flush tables and a footer row */
.card{--pad:20px;margin-bottom:16px;padding:var(--pad);border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}
.card h2,.card h3{margin:0;color:var(--text);font-size:.9375rem;font-weight:600;line-height:1.35}
.card > .section-title,
.card > h2:first-child{margin:calc(-1 * var(--pad)) calc(-1 * var(--pad)) var(--pad);padding:14px var(--pad);border-bottom:1px solid var(--border)}
.card > .section-title + .table-wrap{margin-top:calc(-1 * var(--pad))}
.card > .table-wrap{width:auto;max-width:none;margin-left:calc(-1 * var(--pad));margin-right:calc(-1 * var(--pad))}
.card > .table-wrap:last-child{margin-bottom:calc(-1 * var(--pad))}
.card > .table-wrap + .pagination{margin:0 calc(-1 * var(--pad)) calc(-1 * var(--pad))}
.card > .footer-actions{margin:var(--pad) calc(-1 * var(--pad)) calc(-1 * var(--pad));padding:16px var(--pad);border-top:1px solid var(--border)}
/* legacy allocation card: the card itself scrolls; header and footer stay in view */
.card.table-wrap{padding:0;overflow-x:auto}
.card.table-wrap > .section-title{position:sticky;left:0;margin:0;padding:14px var(--pad)}
.card.table-wrap > .footer-actions{position:sticky;left:0;margin:0;padding:16px var(--pad);border-top:1px solid var(--border)}
.card.table-wrap > .table{min-width:900px}
.card.table-wrap > .table :is(th,td):first-child{min-width:240px;padding-left:var(--pad)}
.card.table-wrap > .table :is(th,td):last-child{padding-right:var(--pad)}
.card.table-wrap > .table td{vertical-align:top}
.card.table-wrap > .table .field label{font-size:.75rem}
.card > p{margin:0 0 16px;font-size:.875rem}
.card > p:last-child{margin-bottom:0}

.section-title{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px 16px}
.section-title > div:first-child{min-width:0;flex:1 1 280px}
.section-title .muted{margin-top:4px;font-size:.8125rem;line-height:1.5}
.section-title > .badge{flex-shrink:0}

.grid{display:grid;gap:16px}
.grid-2{grid-template-columns:repeat(2,minmax(0,1fr))}
.grid > .card{margin-bottom:0}
.wrap > .grid{margin-bottom:16px}

/* form fields */
.field label{display:block;margin-bottom:6px;color:var(--ds-text-secondary);font-size:.8125rem;font-weight:500;line-height:1.35}
.input,.select,.textarea{width:100%;min-height:38px;padding:8px 12px;border:1px solid var(--ds-input-border);border-radius:var(--radius-sm);
  background:var(--surface3);color:var(--text);font-family:var(--ds-font-sans);font-size:.875rem;line-height:1.4;
  transition:border-color .12s ease,box-shadow .12s ease}
.input::placeholder,.textarea::placeholder{color:var(--dim)}
.textarea{min-height:96px;resize:vertical;line-height:1.55}
.input:focus,.select:focus,.textarea:focus{outline:none;border-color:var(--accent);box-shadow:var(--ds-focus-ring)}
.input:disabled,.select:disabled,.textarea:disabled{opacity:.6}

/* buttons */
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:38px;padding:0 16px;
  border:1px solid var(--accent);border-radius:var(--radius-sm);background:var(--accent);color:#fff;
  font-family:var(--ds-font-sans);font-size:.875rem;font-weight:500;line-height:1.2;text-decoration:none;white-space:nowrap;cursor:pointer;
  transition:background .12s ease,border-color .12s ease,color .12s ease}
.btn:hover{background:var(--accent-hover);border-color:var(--accent-hover)}
.btn.secondary{background:var(--surface2);border-color:var(--ds-border-strong);color:var(--text)}
.btn.secondary:hover{background:var(--ds-surface-hover)}
.btn.warn{background:transparent;border-color:var(--ds-warning-border);color:var(--ds-warning-text)}
.btn.warn:hover{background:var(--ds-warning-soft)}
.btn.bad{background:transparent;border-color:var(--ds-danger-border);color:var(--ds-danger-text)}
.btn.bad:hover{background:var(--ds-danger-soft);border-color:var(--ds-danger)}
.btn.disabled{opacity:.5;cursor:not-allowed;pointer-events:none}
.actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center}

/* status labels */
.badge{display:inline-flex;align-items:center;padding:2px 8px;border:1px solid var(--ds-border-strong);border-radius:var(--radius-xs);
  background:var(--surface2);color:var(--ds-text-secondary);font-size:.75rem;font-weight:600;line-height:1.4;white-space:nowrap;font-variant-numeric:tabular-nums}
.badge.good{color:var(--ds-success-text);border-color:var(--ds-success-border);background:var(--ds-success-soft)}
.badge.warn{color:var(--ds-warning-text);border-color:var(--ds-warning-border);background:var(--ds-warning-soft)}
.badge.bad{color:var(--ds-danger-text);border-color:var(--ds-danger-border);background:var(--ds-danger-soft)}
.muted{color:var(--muted)}
.small{font-size:.8125rem}

.alert{margin-bottom:16px;padding:12px 16px;border:1px solid var(--ds-success-border);border-radius:var(--radius-sm);
  background:var(--ds-success-soft);color:#d1fae5;font-size:.875rem;line-height:1.55}
.alert.error{border-color:var(--ds-danger-border);background:var(--ds-danger-soft);color:#fee2e2}
.alert.warn{border-color:var(--ds-warning-border);background:var(--ds-warning-soft);color:#fef3c7}

/* cognitive-level figures (review) */
.metric{padding:14px 16px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--surface3)}
.metric strong{display:block;color:var(--text);font-size:.9375rem;font-weight:600;font-variant-numeric:tabular-nums}
.metric span{display:block;margin-top:4px;color:var(--muted);font-size:.8125rem;line-height:1.5}

/* tables */
.table-wrap{overflow-x:auto;container-type:inline-size}
.table{width:100%;border-collapse:collapse;font-variant-numeric:tabular-nums}
.table th,.table td{padding:12px 14px;border-bottom:1px solid var(--border);text-align:left;vertical-align:middle}
.table th{padding:10px 14px;background:var(--surface3);color:var(--muted);font-size:.75rem;font-weight:600;white-space:nowrap}
.table td{color:var(--ds-text-secondary);font-size:.875rem}
.table td strong{color:var(--text);font-weight:600}
.table tbody tr:last-child td{border-bottom:0}
.table tbody tr:hover td{background:rgba(255,255,255,.02)}
.table tfoot th{border-top:1px solid var(--border);border-bottom:0;color:var(--text);font-size:.8125rem}
.card > .table-wrap .table :is(th,td):first-child{padding-left:var(--pad)}
.card > .table-wrap .table :is(th,td):last-child{padding-right:var(--pad)}
.table .number-input{width:82px;text-align:center}
.table .actions{flex-wrap:nowrap}
.table .actions form{margin:0}
.table .actions .btn{min-height:32px;padding:0 12px;font-size:.8125rem}
/* An empty-table message stays in view while the header row scrolls. */
.table td[colspan] > div{position:sticky;left:0;max-width:calc(100cqw - 28px)}
.table td[colspan] > div p{margin:4px 0 16px;font-size:.875rem}
.weight-cell{min-width:90px}

.pagination nav{padding:12px var(--pad,20px);border-top:1px solid var(--border)}

/* inset notes */
.help{padding:12px 16px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--surface3);font-size:.875rem}
.help > strong{display:block;font-weight:600}
.help summary{color:var(--text);font-weight:600;cursor:pointer}
.help p{margin:6px 0 0;color:var(--muted);line-height:1.55}
.definition-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px 16px;margin-top:12px}
.definition strong{display:block;margin-bottom:2px;color:var(--text);font-size:.8125rem;font-weight:600}
.definition span{color:var(--muted);font-size:.8125rem;line-height:1.5}
.callout{padding:12px 16px;border:1px solid var(--ds-accent-border);border-radius:var(--radius-sm);background:var(--ds-accent-soft);
  color:#dbeafe;font-size:.875rem;line-height:1.55}

/* step indicator */
.wizard{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;margin-bottom:20px}
.wizard-step{display:flex;align-items:center;gap:10px;min-width:0;padding:10px 14px;border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}
.wizard-step > div:last-child{min-width:0}
.wizard-step .num{width:24px;height:24px;flex:0 0 24px;display:flex;align-items:center;justify-content:center;
  border:1px solid var(--ds-border-strong);border-radius:50%;background:var(--surface2);color:var(--muted);font-size:.75rem;font-weight:600}
.wizard-step strong{display:block;color:var(--ds-text-secondary);font-size:.875rem;font-weight:600;line-height:1.35}
.wizard-step span{display:block;margin-top:2px;color:var(--muted);font-size:.75rem}
.wizard-step.active{border-color:var(--ds-accent-border);background:var(--ds-accent-soft)}
.wizard-step.active strong{color:var(--text)}
.wizard-step.active .num{border-color:var(--accent);background:var(--accent);color:#fff}
.wizard-step.done .num{border-color:var(--ds-success-border);background:var(--ds-success-soft);color:var(--ds-success-text)}

/* live blueprint status */
.status-panel{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:14px 16px;
  border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}
.status-panel > div{min-width:0}
.status-panel strong{display:block;margin-bottom:2px;font-size:.9375rem;font-weight:600}
.status-panel .muted{font-size:.875rem;line-height:1.5}
.status-panel > .badge{flex-shrink:0}
.status-panel.good{border-color:var(--ds-success-border)}
.status-panel.warn{border-color:var(--ds-warning-border)}
.status-panel.bad{border-color:var(--ds-danger-border)}

/* review summary rows */
.review-row{display:flex;justify-content:space-between;align-items:center;gap:8px 20px;padding:12px 0;border-bottom:1px solid var(--border);font-size:.875rem}
.review-row:last-child{border-bottom:0;padding-bottom:0}
.card > h2:first-child + .review-row{padding-top:0}
.review-row > :first-child{flex-shrink:0;color:var(--muted)}
.review-row strong{min-width:0;color:var(--text);font-weight:600;text-align:right;overflow-wrap:anywhere}

.footer-actions{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-top:16px}

@media(max-width:1100px){.definition-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:1000px){.grid-2{grid-template-columns:minmax(0,1fr)}}
@media(max-width:900px){.main{padding:24px 20px 40px}}
@media(max-width:720px){
  .layout{display:block}
  .wizard{grid-template-columns:minmax(0,1fr)}
  .definition-grid{grid-template-columns:minmax(0,1fr)}
}
@media(max-width:640px){
  .main{padding:20px 16px 32px}
  .card{--pad:16px}
  .top > .btn,.top > .actions{width:100%}
  .top > .actions .btn{flex:1 1 auto}
  .status-panel{align-items:flex-start;flex-direction:column;gap:10px}
  .review-row{align-items:flex-start;flex-direction:column;gap:4px}
  .review-row strong{text-align:left}
  .footer-actions > .btn,.footer-actions > .actions,.footer-actions > .actions .btn{flex:1 1 auto}
  .footer-actions > span:empty{display:none}
}
</style>
