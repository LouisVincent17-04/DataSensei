{{-- Shared styles for the instructor challenge builder and class challenge pages.
     Colours, type and radius come from partials.design-system; the controls mirror
     the admin content manager so the reused editor markup looks the same here. --}}
<style>
  :root{--accent3:var(--ds-success)}
  *{box-sizing:border-box;margin:0;padding:0}
  body{min-height:100vh;background:var(--bg);color:var(--text);font-family:var(--ds-font-sans)}
  a{color:inherit}
  .ds-shell{display:flex;min-height:100vh}
  .ds-main{flex:1;min-width:0;padding:28px 32px 48px}
  .wrap{max-width:1450px;margin:0 auto}

  /* page header */
  .top-row{display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px;margin-bottom:24px}
  .top-row > div:first-child{min-width:0;flex:1 1 320px}
  .page-subtitle{max-width:72ch;margin-top:4px;color:var(--muted);font-size:.875rem;line-height:1.55}
  .page-links{display:flex;flex-wrap:wrap;gap:8px 16px;margin-top:8px;font-size:.8125rem}
  .page-links a{color:var(--ds-accent-text,#93c5fd);text-decoration:none}
  .page-links a:hover{text-decoration:underline}

  /* buttons */
  .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:38px;padding:0 16px;
    border:1px solid var(--accent);border-radius:var(--radius-sm);background:var(--accent);color:#fff;
    font:500 .875rem/1.2 var(--ds-font-sans);text-decoration:none;white-space:nowrap;cursor:pointer;
    transition:background .12s ease,border-color .12s ease,color .12s ease}
  .btn:hover{border-color:var(--accent-hover);background:var(--accent-hover)}
  .btn.secondary{border-color:var(--ds-border-strong);background:var(--surface2);color:var(--text)}
  .btn.secondary:hover{background:var(--ds-surface-hover)}
  .btn.danger{border-color:var(--ds-danger-border);background:transparent;color:var(--ds-danger-text)}
  .btn.danger:hover{border-color:var(--ds-danger);background:var(--ds-danger-soft)}
  .btn.small{min-height:32px;padding:0 12px;font-size:.8125rem}
  .btn:disabled,.btn[aria-disabled="true"]{opacity:.55}
  .action-row{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
  .actions{display:flex;flex-wrap:wrap;gap:8px}

  /* messages */
  .notice{margin-bottom:16px;padding:12px 16px;border:1px solid var(--ds-success-border);border-radius:var(--radius-sm);
    background:var(--ds-success-soft);color:#d1fae5;font-size:.875rem;line-height:1.55}
  .notice.error{border-color:var(--ds-danger-border);background:var(--ds-danger-soft);color:#fee2e2}
  .notice.info{border-color:var(--ds-accent-border);background:var(--ds-accent-soft);color:#dbeafe}
  .notice ul{margin:8px 0 0 18px}

  /* panels */
  .panel,.card{min-width:0;margin-bottom:24px;overflow:hidden;display:flex;flex-direction:column;
    border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}
  .panel-head{padding:16px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;border-bottom:1px solid var(--border)}
  .panel-heading{min-width:0;flex:1 1 240px}
  .panel-title{color:var(--text);font-size:.9375rem;font-weight:600;line-height:1.35}
  .panel-subtitle{margin-top:2px;color:var(--muted);font-size:.8125rem;line-height:1.5}
  .panel-body{flex:1;padding:20px}

  /* forms */
  .form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
  .form-grid.three{grid-template-columns:repeat(3,minmax(0,1fr))}
  .field{min-width:0}
  .field label{display:block;margin-bottom:6px;color:var(--ds-text-secondary);font-size:.8125rem;font-weight:500;line-height:1.35}
  .input,.select,.textarea{width:100%;min-height:38px;padding:8px 12px;border:1px solid var(--ds-input-border);border-radius:var(--radius-sm);
    outline:0;background:var(--surface3);color:var(--text);font:400 .875rem/1.4 var(--ds-font-sans);
    transition:border-color .12s ease,box-shadow .12s ease}
  .textarea{min-height:96px;resize:vertical;line-height:1.55}
  .input::placeholder,.textarea::placeholder{color:var(--dim)}
  .input:focus,.select:focus,.textarea:focus{border-color:var(--accent);box-shadow:var(--ds-focus-ring)}
  .input[readonly],.textarea[readonly]{opacity:.7}
  .hint{display:block;margin-top:6px;color:var(--muted);font-size:.8125rem;line-height:1.5}
  .dim{color:var(--dim)}
  .muted{color:var(--muted)}

  /* tables */
  .card.table-wrap{padding:0;overflow-x:auto}
  .table{width:100%;min-width:720px;border-collapse:collapse}
  .table th{padding:10px 14px;border-bottom:1px solid var(--border);background:var(--surface3);color:var(--muted);
    font-size:.75rem;font-weight:600;text-align:left;white-space:nowrap}
  .table td{padding:12px 14px;border-bottom:1px solid var(--border);color:var(--ds-text-secondary);font-size:.875rem;line-height:1.45;vertical-align:middle}
  .table tbody tr:last-child td{border-bottom:0}
  .table tbody tr:hover td{background:rgba(255,255,255,.02)}
  .table td strong{color:var(--text);font-weight:600}
  .row-meta{display:block;margin-top:2px;color:var(--muted);font-size:.8125rem}
  .empty{padding:32px 20px;color:var(--muted);font-size:.875rem;line-height:1.5;text-align:center}
  .table tbody tr:hover td[colspan]{background:none}
  .pager{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;padding:14px 20px;border-top:1px solid var(--border);color:var(--muted);font-size:.8125rem}
  .pager nav{display:flex;gap:6px}

  @media(max-width:1000px){.form-grid.three{grid-template-columns:repeat(2,minmax(0,1fr))}}
  @media(max-width:900px){.ds-main{padding:24px 20px 40px}}
  @media(max-width:760px){
    .form-grid,.form-grid.three{grid-template-columns:minmax(0,1fr)}
    .form-grid > *{grid-column:auto !important}
  }
  @media(max-width:640px){
    .ds-main{padding:20px 16px 32px}
    .panel-body{padding:16px}
    .top-row{align-items:stretch;flex-direction:column}
    .top-row > div:first-child{flex:0 0 auto}
    .top-row .actions .btn{flex:1 1 auto}
  }
  @media(prefers-reduced-motion:reduce){.btn,.input,.select,.textarea{transition:none}}
</style>
