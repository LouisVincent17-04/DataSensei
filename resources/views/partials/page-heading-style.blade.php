@once
{{-- Every page title in the product reads the same, whichever layout renders it. --}}
<style id="datasensei-uniform-page-heading">
  .ds-page-title {
    font-family: 'Inter', Arial, Helvetica, sans-serif !important;
    font-size: 1.375rem !important;
    font-weight: 700 !important;
    line-height: 1.3 !important;
    letter-spacing: -0.015em !important;
    text-transform: none !important;
    color: var(--ds-text, #f8fafc);
  }

  .ds-page-title span,
  .ds-page-title b {
    color: inherit;
    font-weight: inherit;
  }

  @media (max-width: 640px) {
    .ds-page-title { font-size: 1.25rem !important; }
  }
</style>
@endonce
