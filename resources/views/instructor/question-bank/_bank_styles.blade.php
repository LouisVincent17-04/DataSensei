{{-- Styles and behaviour shared by the Question Bank page and the builder's
     bank picker (DataSensei Updates 11). Loads after assessments._styles. --}}
<style>
  .q-form{padding:14px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--surface2)}
  .q-choices{display:grid;gap:8px;margin:0 0 8px;padding:0;list-style:none}
  .q-choice{display:grid;grid-template-columns:auto minmax(0,1fr);gap:8px;align-items:center}
  .q-correct{display:inline-flex;align-items:center;gap:6px;cursor:pointer}
  .q-letter{display:inline-block;width:16px;color:var(--muted);font-weight:600}
  .q-inline{display:inline-flex;align-items:center;gap:6px;margin-right:16px;color:var(--ds-text-secondary);font-size:.875rem;cursor:pointer}
  .q-block[hidden]{display:none}
  .bank-filter{display:flex;align-items:flex-end;gap:10px;flex-wrap:wrap;padding:12px 18px;border-bottom:1px solid var(--border)}
  .bank-filter .field{min-width:150px;flex:0 1 auto}
  .bank-row{padding:14px 18px;border-bottom:1px solid var(--border)}
  .bank-row:last-child{border-bottom:0}
  .bank-row-head{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap}
  .bank-row-head > div{min-width:0;flex:1 1 320px}
  .bank-q{margin:0;color:var(--text);font-size:.9375rem;font-weight:600;line-height:1.5;overflow-wrap:anywhere}
  .bank-meta{margin:4px 0 0;color:var(--muted);font-size:.8125rem;line-height:1.5}
  .bank-meta .shared{color:var(--ds-accent-text);font-weight:600}
  .bank-meta .archived{color:var(--ds-warning-text);font-weight:600}
  .bank-preview{margin-top:8px}
  .bank-preview > summary{cursor:pointer;color:var(--ds-accent-text);font-size:.8125rem;list-style:none}
  .bank-preview > summary::-webkit-details-marker{display:none}
  .bank-preview > summary::before{content:"▸ "}
  .bank-preview[open] > summary::before{content:"▾ "}
  .bank-preview-body{margin-top:8px;padding:12px 14px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--surface3);font-size:.875rem;line-height:1.6}
  .bank-preview-body ul{margin:6px 0 0;padding-left:20px}
  .bank-preview-body .correct{color:var(--ds-success-text);font-weight:600}
  .bank-pick{display:flex;align-items:center;gap:8px;flex-shrink:0}
  .bank-actions{display:flex;gap:8px;flex-wrap:wrap;flex-shrink:0}
  .bank-empty{padding:24px 18px;color:var(--muted);font-size:.875rem}
  .bank-fit{margin:4px 0 0;font-size:.8125rem;line-height:1.5}
  .bank-fit.fits{color:var(--ds-success-text)}
  .bank-fit.no-fit{color:var(--muted)}
  .bank-slots{width:100%;border-collapse:collapse;font-size:.875rem}
  .bank-slots th,.bank-slots td{padding:8px 18px;border-bottom:1px solid var(--border);text-align:left;vertical-align:top}
  .bank-slots th{color:var(--muted);font-weight:600;font-size:.8125rem}
  .bank-slots tr:last-child td{border-bottom:0}
  .sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
  @media (max-width:640px){.bank-slots th,.bank-slots td{padding:8px 10px}}
</style>

@once
<script id="datasensei-bank-form-script">
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-bank-form]').forEach((form) => {
    const select = form.querySelector('[data-type-select]');
    const blocks = form.querySelectorAll('.q-block');
    const showType = () => {
      const type = select ? select.value : '';
      blocks.forEach((block) => {
        const on = (block.dataset.for || '').split(' ').includes(type);
        block.hidden = !on;
        // Hidden fields are not sent, so the answer kinds never overwrite each other.
        block.querySelectorAll('input, textarea, select, button').forEach((el) => { el.disabled = !on; });
      });
    };
    if (select) select.addEventListener('change', showType);
    showType();
  });
});
</script>
@endonce
