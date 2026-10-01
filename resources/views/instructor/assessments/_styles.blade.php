{{-- Instructor assessment pages (DataSensei Updates 9): plain sections,
     standard form fields and tables. Colours and type come from
     partials.design-system. --}}
<style>
  *{box-sizing:border-box}
  body{margin:0;background:var(--bg);color:var(--text);font-family:var(--ds-font-sans)}
  .layout{display:flex;min-height:100vh}
  .main{flex:1;min-width:0;padding:28px 32px 48px}
  .wrap{max-width:1100px;margin:0 auto}
  .wrap.wide{max-width:1280px}

  .top{display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:12px 16px;margin-bottom:20px}
  .top > div:first-child{min-width:0;flex:1 1 320px}
  .subtitle{max-width:80ch;margin:4px 0 0;color:var(--muted);font-size:.875rem;line-height:1.55}
  .crumb{display:inline-block;margin-bottom:6px;color:var(--ds-accent-text);font-size:.8125rem;font-weight:500;text-decoration:none}
  .crumb:hover{text-decoration:underline}

  .btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;min-height:36px;padding:0 14px;border:1px solid var(--ds-border-strong);border-radius:var(--radius-sm);background:var(--surface2);color:var(--text);font:500 .875rem/1.2 var(--ds-font-sans);text-decoration:none;white-space:nowrap;cursor:pointer}
  .btn:hover{background:var(--ds-surface-hover)}
  .btn.primary{border-color:var(--accent);background:var(--accent);color:#fff}
  .btn.primary:hover{border-color:var(--accent-hover);background:var(--accent-hover)}
  .btn.danger{border-color:var(--ds-danger-border);background:transparent;color:var(--ds-danger-text)}
  .btn.danger:hover{background:var(--ds-danger-soft)}
  .btn.small{min-height:30px;padding:0 10px;font-size:.8125rem}
  .btn[disabled]{opacity:.55;cursor:not-allowed}
  .actions{display:flex;flex-wrap:wrap;gap:8px;align-items:center}
  .actions form{margin:0}

  .alert{margin-bottom:16px;padding:12px 16px;border:1px solid var(--ds-accent-border);border-radius:var(--radius-sm);background:var(--ds-accent-soft);color:var(--ds-accent-ink, #dbeafe);font-size:.875rem;line-height:1.5}
  .alert.success{border-color:var(--ds-success-border);background:var(--ds-success-soft);color:var(--ds-success-ink, #d1fae5)}
  .alert.danger{border-color:var(--ds-danger-border);background:var(--ds-danger-soft);color:var(--ds-danger-ink, #fee2e2)}
  .alert ul{margin:6px 0 0;padding-left:18px}

  .section{margin-bottom:16px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--surface);scroll-margin-top:80px}
  .section-head{display:flex;justify-content:space-between;align-items:baseline;flex-wrap:wrap;gap:8px 12px;padding:14px 18px;border-bottom:1px solid var(--border)}
  .section-head h2{margin:0;font-size:1rem;font-weight:600;line-height:1.35}
  .section-head p{margin:0;color:var(--muted);font-size:.8125rem;line-height:1.5}
  .section-body{padding:16px 18px}

  .form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px 16px}
  .form-grid.three{grid-template-columns:repeat(3,minmax(0,1fr))}
  .span-all{grid-column:1/-1}
  .field label,.field .label{display:block;margin-bottom:6px;color:var(--ds-text-secondary);font-size:.8125rem;font-weight:500}
  .field .hint{display:block;margin-top:5px;color:var(--muted);font-size:.8125rem;line-height:1.45}
  .field .error{display:block;margin-top:5px;color:var(--ds-danger-text);font-size:.8125rem}
  .input,.select,.textarea{width:100%;min-height:38px;padding:8px 12px;border:1px solid var(--ds-input-border);border-radius:var(--radius-sm);background:var(--surface3);color:var(--text);font:400 .875rem/1.45 var(--ds-font-sans);color-scheme:var(--ds-color-scheme,dark)}
  .textarea{min-height:90px;resize:vertical}
  .input:focus,.select:focus,.textarea:focus{outline:none;border-color:var(--accent);box-shadow:var(--ds-focus-ring)}
  .form-foot{display:flex;justify-content:flex-end;flex-wrap:wrap;gap:8px;margin-top:16px}

  .facts{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px 20px;margin:0}
  .facts dt{color:var(--muted);font-size:.8125rem;font-weight:500}
  .facts dd{margin:2px 0 0;color:var(--text);font-size:.9375rem;font-weight:500;overflow-wrap:anywhere}

  .muted{color:var(--muted)}
  .state-good{color:var(--ds-success-text)}
  .state-warn{color:var(--ds-warning-text)}
  .state-bad{color:var(--ds-danger-text)}

  .table{width:100%;border-collapse:collapse}
  .table th{padding:9px 14px;background:var(--surface3);color:var(--muted);font-size:.75rem;font-weight:600;text-align:left;white-space:nowrap}
  .table td{padding:11px 14px;border-top:1px solid var(--border);color:var(--ds-text-secondary);font-size:.875rem;vertical-align:top}
  .table td strong{color:var(--text);font-weight:600}
  .table .sub{display:block;margin-top:2px;color:var(--muted);font-size:.8125rem}

  @media(max-width:900px){.main{padding:24px 20px 40px}.facts{grid-template-columns:repeat(2,minmax(0,1fr))}.form-grid.three{grid-template-columns:repeat(2,minmax(0,1fr))}}
  @media(max-width:640px){
    .main{padding:20px 16px 32px}
    .form-grid,.form-grid.three{grid-template-columns:minmax(0,1fr)}
    .section-body{padding:14px}
    .section-head{padding:12px 14px}
    .top{flex-direction:column;align-items:stretch}
    /* In a column the 320px basis would become a 320px-tall empty block. */
    .top > div:first-child{flex:0 0 auto}
    .facts{grid-template-columns:minmax(0,1fr)}
  }
</style>
