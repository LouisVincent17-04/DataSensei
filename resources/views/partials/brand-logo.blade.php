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
      color: var(--text, #fafafa);
      text-decoration: none;
      line-height: 1;
    }

    .ds-brand-logo__mark {
      width: 38px;
      height: 38px;
      flex: 0 0 auto;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 14px;
      overflow: hidden;
      background:
        radial-gradient(circle at 30% 18%, rgba(255,255,255,0.16), transparent 30%),
        linear-gradient(145deg, rgba(59,130,246,0.16), rgba(139,92,246,0.08));
      border: 1px solid rgba(127,147,176,0.22);
      box-shadow: 0 10px 28px rgba(0,0,0,0.22);
    }

    .ds-brand-logo__image {
      width: 100%;
      height: 100%;
      object-fit: contain;
      display: block;
      padding: 5px;
    }

    .ds-brand-logo__copy {
      min-width: 0;
      display: flex;
      flex-direction: column;
      gap: 4px;
    }

    .ds-brand-logo__name {
      display: block;
      color: var(--text, #fafafa);
      font-size: 1.08rem;
      font-weight: 850;
      letter-spacing: -0.035em;
      white-space: nowrap;
    }

    .ds-brand-logo__accent {
      color: var(--accent, #3b82f6);
    }

    .ds-brand-logo__subtext {
      display: block;
      color: var(--muted, #7f93b0);
      font-size: 0.72rem;
      font-weight: 650;
      letter-spacing: 0.01em;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 180px;
    }

    .ds-brand-logo--compact {
      gap: 7px;
    }

    .ds-brand-logo--compact .ds-brand-logo__mark {
      width: 26px;
      height: 26px;
      border-radius: 9px;
    }

    .ds-brand-logo--compact .ds-brand-logo__image {
      padding: 3px;
    }

    .ds-brand-logo--compact .ds-brand-logo__name {
      font-size: 0.9rem;
      font-weight: 800;
    }

    .ds-brand-logo--large {
      gap: 13px;
    }

    .ds-brand-logo--large .ds-brand-logo__mark {
      width: 52px;
      height: 52px;
      border-radius: 18px;
      border-color: rgba(59,130,246,0.26);
    }

    .ds-brand-logo--large .ds-brand-logo__image {
      padding: 6px;
    }

    .ds-brand-logo--large .ds-brand-logo__name {
      font-size: 1.35rem;
      font-weight: 900;
    }

    .ds-brand-logo--large .ds-brand-logo__subtext {
      font-size: 0.78rem;
      max-width: 240px;
    }

    .ds-brand-logo--icon-only .ds-brand-logo__mark {
      width: 34px;
      height: 34px;
      border-radius: 12px;
    }

    .ds-brand-logo--icon-only .ds-brand-logo__copy {
      display: none;
    }

    .ds-brand-logo--sidebar {
      width: 100%;
    }

    .ds-brand-logo--topbar .ds-brand-logo__mark {
      width: 24px;
      height: 24px;
      border-radius: 8px;
      box-shadow: none;
    }

    .ds-brand-logo--topbar .ds-brand-logo__image {
      padding: 3px;
    }

    .ds-brand-logo--topbar .ds-brand-logo__name {
      font-size: 0.9rem;
    }

    .ds-brand-logo--auth {
      align-items: center;
    }

    .ds-brand-logo--admin-card {
      margin-bottom: 18px;
    }
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
