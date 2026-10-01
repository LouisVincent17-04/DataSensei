@once
{{--
    Shared "?" tooltips (DataSensei Updates 10).

    App\Support\Glossary::help('term') prints a word with a small "?" button
    beside it. The definition opens on hover, on keyboard focus, and on tap,
    closes with Escape, and stays inside the viewport. Include this partial
    once on any page that prints a glossary term.
--}}
<style id="datasensei-glossary-tips">
  .ds-term { position: relative; display: inline; }
  .ds-term-help {
    display: inline-grid !important; place-items: center; width: 15px !important; height: 15px !important; margin: 0 0 0 5px !important; padding: 0 !important;
    min-height: 0 !important; min-width: 0 !important;
    vertical-align: 1px;
    border: 1px solid var(--ds-border-strong, #2c4168); border-radius: 50%;
    background: var(--ds-surface-2, #1a2638); color: var(--ds-text-muted, #8aa0bd);
    font: 600 0.625rem/1 var(--ds-font-sans, Arial, sans-serif); cursor: help;
  }
  .ds-term-help:hover, .ds-term-help[aria-expanded="true"] { border-color: var(--ds-accent, #3b82f6); color: var(--ds-accent-text, #93c5fd); }
  .ds-term-help:focus-visible { outline: none; box-shadow: var(--ds-focus-ring, 0 0 0 3px rgba(59, 130, 246, 0.35)); }
  .ds-term-tip {
    position: absolute !important; z-index: 90; left: 0; top: calc(100% + 8px); width: max-content; max-width: min(320px, 78vw);
    margin: 0 !important; padding: 10px 12px; border: 1px solid var(--ds-border-strong, #2c4168); border-radius: var(--ds-radius-sm, 6px);
    background: var(--ds-tip-bg, #0b1526) !important; color: var(--ds-text-secondary, #c8d5e8) !important;
    box-shadow: var(--ds-shadow-lg, 0 8px 24px rgba(0, 0, 0, 0.5));
    font: 400 0.8125rem/1.5 var(--ds-font-sans, Arial, sans-serif) !important; letter-spacing: 0;
    text-align: left; text-transform: none; white-space: normal;
    /* Hidden, not just moved: a hidden box must not widen the page on a
       phone. !important on both states, because these tips render inside
       190 existing pages whose own CSS (".metric span" and the like) must
       never restyle them. */
    display: none !important;
  }
  .ds-term-tip b { display: block; margin: 0 0 2px; color: var(--ds-text, #f8fafc); font-size: 0.8125rem; font-weight: 600; line-height: 1.4; }
  .ds-term-tip .ds-tip-formula { display: block; margin-top: 6px; color: var(--ds-accent-text, #93c5fd); font-family: var(--ds-font-mono, monospace); font-size: 0.8125rem; }
  .ds-term-tip .ds-tip-purpose { display: block; margin-top: 4px; }
  .ds-term.flip .ds-term-tip { left: auto; right: 0; }
  .ds-term.above .ds-term-tip { top: auto; bottom: calc(100% + 8px); }
  .ds-term:hover .ds-term-tip, .ds-term:focus-within .ds-term-tip, .ds-term.open .ds-term-tip { display: block !important; }
  th .ds-term-help { vertical-align: 0; }
</style>

<script id="datasensei-glossary-tips-script">
(() => {
  // Hover and keyboard focus open the tooltip through CSS alone. This adds
  // tap-to-toggle for touch screens, Escape to close, and keeps the box on
  // screen near the page edges.
  const closeAll = except => document.querySelectorAll('[data-ds-term].open').forEach(term => {
    if (term === except) return;
    term.classList.remove('open');
    term.querySelector('.ds-term-help')?.setAttribute('aria-expanded', 'false');
  });

  const place = term => {
    const tip = term.querySelector('.ds-term-tip');
    if (!tip) return;
    term.classList.remove('flip', 'above');
    const box = tip.getBoundingClientRect();
    if (box.right > window.innerWidth - 8) term.classList.add('flip');
    if (box.bottom > window.innerHeight - 8 && term.getBoundingClientRect().top > box.height + 16) term.classList.add('above');
  };

  document.addEventListener('click', event => {
    const help = event.target.closest('.ds-term-help');
    if (!help) {
      closeAll(null);
      // A tapped "?" keeps focus, and focus alone keeps the box visible.
      if (document.activeElement?.classList.contains('ds-term-help')) document.activeElement.blur();
      return;
    }
    // A "?" inside a <label> or <summary> must not also tick the box or toggle the section.
    event.preventDefault();
    event.stopPropagation();
    const term = help.closest('[data-ds-term]');
    const open = !term.classList.contains('open');
    closeAll(term);
    term.classList.toggle('open', open);
    help.setAttribute('aria-expanded', open ? 'true' : 'false');
    if (open) place(term);
  });
  document.addEventListener('mouseover', event => {
    const term = event.target.closest?.('[data-ds-term]');
    if (term) place(term);
  });
  document.addEventListener('focusin', event => {
    const term = event.target.closest?.('[data-ds-term]');
    if (term) place(term);
  });
  document.addEventListener('keydown', event => {
    if (event.key !== 'Escape') return;
    closeAll(null);
    if (document.activeElement?.classList.contains('ds-term-help')) document.activeElement.blur();
  });
})();
</script>
@endonce
