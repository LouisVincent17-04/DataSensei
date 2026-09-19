@include('partials.ui-polish')
@php
  $brandVariant = $variant ?? 'default';
  $brandSize = $size ?? 'normal';
  $brandText = $text ?? 'DataSensei';
  $brandSubtext = $subtext ?? null;
  $brandHref = $href ?? null;
  $brandShowText = $showText ?? true;
  $brandClass = $class ?? '';
  $brandAlt = $alt ?? 'DataSensei Logo';
  $brandLabel = trim($brandText . ($brandSubtext ? ' - ' . $brandSubtext : ''));
  $brandLogoSvg = asset('assets/images/logo.svg');
  $brandLogoPng = asset('assets/images/logo.png');
@endphp

@once
  <style>
    .ds-brand-logo {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      min-width: 0;
      color: var(--ds-text, #f8fafc);
      text-decoration: none;
      line-height: 1;
    }

    .ds-brand-logo__mark {
      width: 36px;
      height: 36px;
      flex: 0 0 auto;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      border: 1px solid var(--ds-border-strong, #2c4168);
      border-radius: var(--ds-radius-md, 8px);
      background: var(--ds-surface-2, #1a2638);
    }

    .ds-brand-logo__image {
      width: 100%;
      height: 100%;
      display: block;
      padding: 4px;
      object-fit: contain;
    }

    .ds-brand-logo__copy {
      min-width: 0;
      display: flex;
      flex-direction: column;
      gap: 3px;
    }

    .ds-brand-logo__name {
      display: block;
      color: var(--ds-text, #f8fafc);
      font-family: var(--ds-font-sans, Inter, Arial, Helvetica, sans-serif);
      font-size: 1.0625rem;
      font-weight: 700;
      letter-spacing: -0.02em;
      white-space: nowrap;
    }

    .ds-brand-logo__accent {
      color: var(--ds-accent, #3b82f6);
    }

    .ds-brand-logo__subtext {
      display: block;
      max-width: 180px;
      color: var(--ds-text-muted, #8aa0bd);
      font-size: 0.75rem;
      font-weight: 500;
      line-height: 1.3;
      overflow-wrap: break-word;
    }

    .ds-brand-logo--compact { gap: 8px; }

    .ds-brand-logo--compact .ds-brand-logo__mark {
      width: 28px;
      height: 28px;
      border-radius: var(--ds-radius-sm, 6px);
    }

    .ds-brand-logo--compact .ds-brand-logo__image { padding: 3px; }

    .ds-brand-logo--compact .ds-brand-logo__name {
      font-size: 0.9375rem;
    }

    .ds-brand-logo--large { gap: 12px; }

    .ds-brand-logo--large .ds-brand-logo__mark {
      width: 44px;
      height: 44px;
      border-radius: var(--ds-radius-lg, 10px);
    }

    .ds-brand-logo--large .ds-brand-logo__image { padding: 6px; }

    .ds-brand-logo--large .ds-brand-logo__name {
      font-size: 1.25rem;
    }

    .ds-brand-logo--large .ds-brand-logo__subtext {
      max-width: 240px;
      font-size: 0.8125rem;
    }

    .ds-brand-logo--icon-only .ds-brand-logo__mark {
      width: 32px;
      height: 32px;
      border-radius: var(--ds-radius-sm, 6px);
    }

    .ds-brand-logo--icon-only .ds-brand-logo__copy { display: none; }

    .ds-brand-logo--sidebar { width: 100%; }

    .ds-brand-logo--topbar .ds-brand-logo__mark {
      width: 26px;
      height: 26px;
      border-radius: var(--ds-radius-sm, 6px);
    }

    .ds-brand-logo--topbar .ds-brand-logo__image { padding: 3px; }

    .ds-brand-logo--topbar .ds-brand-logo__name { font-size: 0.9375rem; }

    .ds-brand-logo--auth { align-items: center; }

    .ds-brand-logo--admin-card { margin-bottom: 16px; }
  </style>
@endonce

@if($brandHref)
  <a href="{{ $brandHref }}" class="ds-brand-logo ds-brand-logo--{{ $brandVariant }} ds-brand-logo--{{ $brandSize }} {{ $brandClass }}" aria-label="{{ $brandLabel }}">
@else
  <div class="ds-brand-logo ds-brand-logo--{{ $brandVariant }} ds-brand-logo--{{ $brandSize }} {{ $brandClass }}" aria-label="{{ $brandLabel }}" role="img">
@endif
    <span class="ds-brand-logo__mark" aria-hidden="true">
      <img
        class="ds-brand-logo__image"
        src="{{ $brandLogoSvg }}"
        alt="{{ $brandAlt }}"
        onerror="this.onerror=null;this.src='{{ $brandLogoPng }}';"
      >
    </span>

    @if($brandShowText)
      <span class="ds-brand-logo__copy">
        <span class="ds-brand-logo__name">Data<span class="ds-brand-logo__accent">Sensei</span></span>
        @if($brandSubtext)
          <span class="ds-brand-logo__subtext">{{ $brandSubtext }}</span>
        @endif
      </span>
    @endif
@if($brandHref)
  </a>
@else
  </div>
@endif
