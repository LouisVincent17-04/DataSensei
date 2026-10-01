@extends('admin.layout')

@use('App\Support\Glossary')

@section('title', 'Certificate '.$certificate->certificate_number)
@section('page_title', 'Certificate '.$certificate->certificate_number)
@section('page_subtitle', 'The certificate exactly as it was issued. It can be revoked or reissued with a reason; both are kept with the certificate and in the audit log.')

@push('head')
  @include('certificates._styles')
@endpush

@section('content')
  <div class="ct-form">
    <section class="ct-section">
      <h2>{{ $data['title'] }}</h2>
      <div class="ct-section-body">
        <dl class="ct-dl">
          <dt>Status</dt>
          <dd>@if($certificate->isRevoked())<span class="ct-bad">Revoked</span> {{ $certificate->revoked_at?->format('M j, Y g:i A') }}{{ $revokedBy ? ' by '.$revokedBy : '' }}@else<span class="ct-good">Active</span>@endif</dd>
          @if($certificate->isRevoked() && $certificate->revoke_reason)
            <dt>Reason</dt><dd>{{ $certificate->revoke_reason }}</dd>
          @endif
          <dt>Learner</dt><dd>{{ $certificate->user?->name ?? 'Deleted account' }}{{ $certificate->user?->email ? ' ('.$certificate->user->email.')' : '' }}</dd>
          <dt>Course or milestone</dt><dd>{{ $data['module'] }}</dd>
          @if($data['class_name'])<dt>Class</dt><dd>{{ $data['class_name'] }}</dd>@endif
          <dt>Issued</dt><dd>{{ $certificate->issued_at?->format('M j, Y g:i A') }}, {{ $data['issued_by'] ? 'by '.$data['issued_by'] : 'automatically after the requirements were met' }}</dd>
          <dt>Requirement version</dt><dd>{{ $certificate->requirement_version }}</dd>
          @if($replaces)<dt>Replaces</dt><dd><a class="ct-link-button" href="{{ route('admin.certificates.show', $replaces) }}">{{ $replaces->certificate_number }}</a></dd>@endif
          @if($replacedBy)<dt>Reissued as</dt><dd><a class="ct-link-button" href="{{ route('admin.certificates.show', $replacedBy) }}">{{ $replacedBy->certificate_number }}</a></dd>@endif
          <dt>{!! Glossary::help('certificate_verification') !!}</dt><dd><a class="ct-link-button" href="{{ $data['verify_url'] }}" target="_blank" rel="noopener">{{ $data['verify_url'] }}</a></dd>
        </dl>
      </div>
    </section>

    <div class="ct-frame">{{ $svg }}</div>

    @if($replacedBy === null)
      <section class="ct-section">
        <h2>{!! Glossary::help('certificate_revocation', 'Revoke or reissue') !!}</h2>
        <div class="ct-section-body">
          <div class="ct-grid">
            @unless($certificate->isRevoked())
              <form method="POST" action="{{ route('admin.certificates.revoke', $certificate) }}" class="ct-field" onsubmit="return confirm('Revoke this certificate? It stays on record as revoked.');">
                @csrf
                @method('PATCH')
                <label for="ac-revoke">Reason for revoking</label>
                <textarea id="ac-revoke" class="ct-textarea{{ $errors->has('reason') ? ' is-invalid' : '' }}" name="reason" maxlength="400" required>{{ old('reason') }}</textarea>
                @error('reason')<p class="ct-error">{{ $message }}</p>@enderror
                <div class="ct-actions"><button class="ct-btn ct-btn-danger" type="submit">Revoke</button></div>
              </form>
            @endunless
            <form method="POST" action="{{ route('admin.certificates.reissue', $certificate) }}" class="ct-field" onsubmit="return confirm('Reissue this certificate with a new certificate ID?');">
              @csrf
              <label for="ac-reissue">Reason for reissuing</label>
              <textarea id="ac-reissue" class="ct-textarea" name="reason" maxlength="400" required></textarea>
              <p class="ct-help">The current copy is revoked and a new copy with a new certificate ID is issued, with the learner's current name.</p>
              <div class="ct-actions"><button class="ct-btn ct-btn-secondary" type="submit">Reissue</button></div>
            </form>
          </div>
        </div>
      </section>
    @endif

    <div class="ct-actions"><a class="ct-btn ct-btn-secondary" href="{{ route('admin.certificates.index') }}">All issued certificates</a></div>
  </div>
@endsection
