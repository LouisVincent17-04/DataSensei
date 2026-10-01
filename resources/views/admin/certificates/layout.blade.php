@extends('admin.layout')

@use('App\Support\Certificates\CertificateLayouts')

@section('title', CertificateLayouts::name($layout))
@section('page_title', CertificateLayouts::name($layout).' layout')
@section('page_subtitle', CertificateLayouts::LAYOUTS[$layout]['description'].' Shown with sample values and today\'s issuer settings.')

@push('head')
  @include('certificates._styles')
@endpush

@section('content')
  <div class="ct-form">
    <div class="ct-frame">{{ $svg }}</div>
    <div class="ct-actions">
      <a class="ct-btn ct-btn-secondary" href="{{ route('admin.certificates.index', ['tab' => 'layouts']) }}">Back to layouts</a>
      <span class="ct-help">This layout is {{ $enabled ? 'on' : 'off' }}.</span>
    </div>
  </div>
@endsection
