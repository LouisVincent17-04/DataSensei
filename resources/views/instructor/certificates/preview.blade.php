@extends('instructor.layout')

@use('App\Models\CertificateDefinition')
@use('App\Support\Certificates\CertificateLayouts')
@use('App\Support\Glossary')

@section('title', 'Preview: '.$definition->name)
@section('page_title', $definition->name)
@section('page_subtitle', 'Preview with a sample learner, exactly as learners will receive it on screen and as a PDF.')

@section('content')
  @include('certificates._styles')

  @php
    $statusTone = ['active' => 'ct-good', 'inactive' => 'ct-muted', 'draft' => 'ct-warn'][$definition->status] ?? '';
  @endphp

  <div class="ct-form">
    <section class="ct-section">
      <h2>Configuration</h2>
      <div class="ct-section-body">
        <dl class="ct-dl">
          <dt>{!! Glossary::help('certificate_status') !!}</dt>
          <dd><span class="{{ $statusTone }}">{{ $definition->statusLabel() }}</span>@if($definition->status === CertificateDefinition::STATUS_ACTIVE && $definition->activated_at) <span class="ct-muted">since {{ $definition->activated_at->format('M j, Y g:i A') }}</span>@endif</dd>
          <dt>Class</dt><dd>{{ $definition->classRoom?->name }}{{ $definition->classRoom?->section ? ', '.$definition->classRoom->section : '' }}</dd>
          <dt>Module</dt><dd>{{ $moduleTitle }}</dd>
          <dt>{!! Glossary::help('certificate_requirement') !!}</dt><dd>{{ $rule }}</dd>
          <dt>{!! Glossary::help('certificate_layout') !!}</dt><dd>{{ CertificateLayouts::name($definition->layout_key) }}@unless($layoutEnabled) <span class="ct-warn">(turned off by an administrator)</span>@endunless</dd>
          <dt>Issued so far</dt><dd>{{ $issuedCount }}</dd>
        </dl>

        <div class="ct-actions">
          @if($definition->status !== CertificateDefinition::STATUS_ACTIVE)
            <form method="POST" action="{{ route('instructor.certificates.activate', $definition) }}">
              @csrf
              @method('PATCH')
              <button class="ct-btn" type="submit" @disabled($problem !== null)>Activate</button>
            </form>
          @else
            <form method="POST" action="{{ route('instructor.certificates.deactivate', $definition) }}" onsubmit="return confirm('Stop issuing this certificate? Certificates already issued stay valid.');">
              @csrf
              @method('PATCH')
              <button class="ct-btn ct-btn-secondary" type="submit">Make inactive</button>
            </form>
          @endif
          <a class="ct-btn ct-btn-secondary" href="{{ route('instructor.certificates.edit', $definition) }}">Edit</a>
          <a class="ct-btn ct-btn-secondary" href="{{ route('instructor.certificates.preview-pdf', $definition) }}" target="_blank" rel="noopener">Preview PDF</a>
          @if($definition->status !== CertificateDefinition::STATUS_ACTIVE && $issuedCount === 0)
            <form method="POST" action="{{ route('instructor.certificates.destroy', $definition) }}" onsubmit="return confirm('Delete this certificate configuration?');">
              @csrf
              @method('DELETE')
              <button class="ct-btn ct-btn-danger" type="submit">Delete</button>
            </form>
          @endif
          <a class="ct-btn ct-btn-secondary" href="{{ route('instructor.certificates.index') }}">All certificates</a>
        </div>
        @if($problem !== null && $definition->status !== CertificateDefinition::STATUS_ACTIVE)
          <p class="ct-help ct-warn" role="note">{{ $problem }}</p>
        @elseif($definition->status !== CertificateDefinition::STATUS_ACTIVE)
          <p class="ct-help">Activating issues the certificate to the learners of this class who already meet the requirement, and to every other learner as soon as they do.</p>
        @endif
      </div>
    </section>

    <section class="ct-section">
      <h2>Preview with a sample learner</h2>
      <div class="ct-section-body">
        <div class="ct-frame">{{ $svg }}</div>
      </div>
    </section>

    <section class="ct-section">
      <h2>Learners of this class</h2>
      @if($roster->isEmpty())
        <div class="ct-empty">No learners are enrolled in this class yet.</div>
      @else
        <div class="ct-table-wrap">
          <table class="ct-table">
            <thead><tr><th scope="col">Learner</th><th scope="col">Requirement</th><th scope="col">Certificate</th></tr></thead>
            <tbody>
              @foreach($roster as $row)
                <tr>
                  <td>{{ $row->name }}</td>
                  <td>@if($row->certificate)<span class="ct-good">Met</span>@elseif($row->met)<span class="ct-good">Met</span>@else<span class="ct-muted">Not yet</span>@endif</td>
                  <td>
                    @if($row->certificate)
                      {{ $row->certificate->certificate_number }}, {{ $row->certificate->issued_at?->format('M j, Y') }}@if($row->certificate->isRevoked()) <span class="ct-bad">(revoked)</span>@endif
                    @else
                      <span class="ct-muted">Not issued</span>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </section>
  </div>
@endsection
