@extends('admin.layout')

@use('App\Support\Certificates\CertificateLayouts')
@use('App\Support\Glossary')

@section('title', 'Certificates')
@section('page_title', 'Certificates')
@section('page_subtitle', 'Issued certificates, the three core certificates, the issuer and signatory they show, and the five predefined layouts. Changes apply to certificates issued afterwards; an issued certificate is never changed, only revoked or reissued with a reason.')

@push('head')
  @include('certificates._styles')
  <style>
    .ac-nav { display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 16px; border-bottom: 1px solid var(--border); }
    .ac-nav a { position: relative; padding: 10px 12px; color: var(--muted); font-size: .875rem; font-weight: 500; text-decoration: none; }
    .ac-nav a.is-current { color: var(--text); }
    .ac-nav a.is-current::after { content: ''; position: absolute; left: 8px; right: 8px; bottom: -1px; height: 2px; background: var(--accent); }
    .ac-filters { display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-end; }
    .ac-filters .ct-field { flex: 1 1 200px; }
    .ac-check { display: flex; gap: 8px; align-items: flex-start; color: var(--text); font-size: .875rem; }
    .ac-check input { margin-top: 3px; accent-color: var(--accent); }
    .ac-logo { margin-bottom: 6px; }
    .ac-logo img { display: block; width: 56px; height: 56px; object-fit: contain; background: #fff; border: 1px solid var(--border); border-radius: 4px; }
    .ac-file { height: auto; padding-top: 6px; padding-bottom: 6px; }
    .ac-layouts { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 14px; }
    .ac-layout { display: grid; gap: 10px; padding: 12px; border: 1px solid var(--border); border-radius: var(--radius-sm); background: var(--surface2); }
    .ac-layout h3 { margin: 0; color: var(--text); font-size: .9375rem; }
  </style>
@endpush

@section('content')
  <nav class="ac-nav" aria-label="Certificates">
    @foreach($tabs as $key => $label)
      <a href="{{ route('admin.certificates.index', ['tab' => $key]) }}" class="{{ $tab === $key ? 'is-current' : '' }}" @if($tab === $key) aria-current="page" @endif>{{ $label }}</a>
    @endforeach
  </nav>

  @if($tab === 'issued')
    <section class="ct-section">
      <h2>Issued certificates</h2>
      <div class="ct-section-body">
        <form method="GET" action="{{ route('admin.certificates.index') }}" class="ac-filters">
          <input type="hidden" name="tab" value="issued">
          <div class="ct-field">
            <label for="ac-q">{!! Glossary::help('certificate_id') !!} or learner</label>
            <input id="ac-q" class="ct-input" type="search" name="q" value="{{ $filters['q'] }}" maxlength="100" placeholder="DS-... or a name">
          </div>
          <div class="ct-field">
            <label for="ac-cert">Certificate</label>
            <select id="ac-cert" class="ct-select" name="certificate">
              <option value="0">All certificates</option>
              @foreach($definitionChoices as $choice)
                <option value="{{ $choice->id }}" @selected($filters['certificate'] === (int) $choice->id)>{{ $choice->name }}{{ $choice->is_system ? ' (core)' : '' }}</option>
              @endforeach
            </select>
          </div>
          <div class="ct-field">
            <label for="ac-status">Status</label>
            <select id="ac-status" class="ct-select" name="status">
              <option value="">Active and revoked</option>
              <option value="active" @selected($filters['status'] === 'active')>Active</option>
              <option value="revoked" @selected($filters['status'] === 'revoked')>Revoked</option>
            </select>
          </div>
          <div class="ct-actions"><button class="ct-btn" type="submit">Filter</button></div>
        </form>
      </div>
      @if($issued->isEmpty())
        <div class="ct-empty">No certificate matches these filters.</div>
      @else
        <div class="ct-table-wrap">
          <table class="ct-table">
            <thead><tr><th scope="col">Certificate ID</th><th scope="col">Certificate</th><th scope="col">Learner</th><th scope="col">Issued</th><th scope="col">Status</th><th scope="col"></th></tr></thead>
            <tbody>
              @foreach($issued as $certificate)
                <tr>
                  <td><code>{{ $certificate->certificate_number }}</code></td>
                  <td>{{ $certificate->snapshotData()['name'] ?? $certificate->definition?->name }}</td>
                  <td>{{ $certificate->user?->name ?? 'Deleted account' }}</td>
                  <td>{{ $certificate->issued_at?->format('M j, Y') }}</td>
                  <td>@if($certificate->isRevoked())<span class="ct-bad">Revoked</span>@else<span class="ct-good">Active</span>@endif</td>
                  <td><a class="ct-btn ct-btn-secondary" href="{{ route('admin.certificates.show', $certificate) }}">Open</a></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="ct-section-body">{{ $issued->links() }}</div>
      @endif
    </section>
  @elseif($tab === 'core')
    @foreach($core as $row)
      @php $definition = $row['definition']; @endphp
      <section class="ct-section" style="margin-bottom:16px">
        <h2>{{ $definition->name }}</h2>
        <div class="ct-section-body">
          <dl class="ct-dl">
            <dt>{!! Glossary::help('certificate_status') !!}</dt><dd><span class="{{ $definition->is_active ? 'ct-good' : 'ct-muted' }}">{{ $definition->statusLabel() }}</span></dd>
            <dt>{!! Glossary::help('certificate_requirement', 'Requirement') !!}</dt><dd>{{ $row['rule'] }}</dd>
            <dt>Requirement version</dt><dd>Version {{ $row['set']?->version ?? 0 }}, {{ count($row['items']) }} items @if($row['versions']->count() > 1)<span class="ct-muted">(earlier versions: {{ $row['versions']->skip(1)->map(fn ($v) => 'v'.$v->version.' with '.$v->item_count.' items')->implode(', ') }})</span>@endif</dd>
            <dt>Active copies issued</dt><dd>{{ $row['issued'] }}</dd>
          </dl>
          <details>
            <summary class="ct-link-button">Show the {{ count($row['items']) }} requirements</summary>
            <ol class="ct-help" style="margin:8px 0 0;padding-left:20px">
              @foreach($row['items'] as $item)
                <li>{{ $item['title'] }}@if(! empty($item['content_code'])) <span class="ct-muted">({{ $item['content_code'] }})</span>@elseif($definition->certificate_key !== 'core_24_modules') <span class="ct-warn">(no challenge found on this install)</span>@endif</li>
              @endforeach
            </ol>
          </details>
          <div class="ct-actions">
            <form method="POST" action="{{ route('admin.certificates.definitions.status', $definition) }}">
              @csrf
              @method('PATCH')
              <button class="ct-btn {{ $definition->is_active ? 'ct-btn-secondary' : '' }}" type="submit">{{ $definition->is_active ? 'Make inactive' : 'Activate' }}</button>
            </form>
          </div>
        </div>
      </section>
    @endforeach
    <p class="ct-help">The requirements come from the Core Module identities, never from titles. The challenge level they use is set under Issuer and signatory. When they change after a certificate was issued, a new requirement version is written; issued certificates keep the version they were issued under.</p>
  @elseif($tab === 'settings')
    <form method="POST" action="{{ route('admin.certificates.settings') }}" class="ct-form" enctype="multipart/form-data" novalidate>
      @csrf
      @method('PUT')
      <section class="ct-section">
        <h2>Issuer and signatory of the core certificates</h2>
        <div class="ct-section-body">
          <div class="ct-grid">
            <div class="ct-field">
              <label for="ac-issuer">Issuer name</label>
              <input id="ac-issuer" class="ct-input{{ $errors->has('issuer_name') ? ' is-invalid' : '' }}" name="issuer_name" maxlength="120" value="{{ old('issuer_name', $settings['issuer_name']) }}" required>
              @error('issuer_name')<p class="ct-error">{{ $message }}</p>@enderror
              <p class="ct-help">Also used on class certificates of instructors who belong to no institution.</p>
            </div>
            <div class="ct-field">
              <label for="ac-line">Issuer line</label>
              <input id="ac-line" class="ct-input{{ $errors->has('issuer_line') ? ' is-invalid' : '' }}" name="issuer_line" maxlength="160" value="{{ old('issuer_line', $settings['issuer_line']) }}">
              @error('issuer_line')<p class="ct-error">{{ $message }}</p>@enderror
              <p class="ct-help">A short line under the issuer, for example Data Science Learning Platform.</p>
            </div>
            <div class="ct-field">
              <label for="ac-signatory">Signatory name</label>
              <input id="ac-signatory" class="ct-input{{ $errors->has('signatory_name') ? ' is-invalid' : '' }}" name="signatory_name" maxlength="120" value="{{ old('signatory_name', $settings['signatory_name']) }}" required>
              @error('signatory_name')<p class="ct-error">{{ $message }}</p>@enderror
            </div>
            <div class="ct-field">
              <label for="ac-signatory-title">Signatory title</label>
              <input id="ac-signatory-title" class="ct-input{{ $errors->has('signatory_title') ? ' is-invalid' : '' }}" name="signatory_title" maxlength="120" value="{{ old('signatory_title', $settings['signatory_title']) }}" required>
              @error('signatory_title')<p class="ct-error">{{ $message }}</p>@enderror
            </div>
            <div class="ct-field">
              <label for="ac-logo">Issuer logo <span class="ct-muted">(optional)</span></label>
              @if($settings['issuer_logo'] !== '')
                <div class="ac-logo"><img src="data:image/jpeg;base64,{{ $settings['issuer_logo'] }}" alt="Current issuer logo" width="56" height="56"></div>
              @endif
              <input id="ac-logo" class="ct-input ac-file{{ $errors->has('issuer_logo') ? ' is-invalid' : '' }}" type="file" name="issuer_logo" accept="image/jpeg,image/png,image/gif,image/webp">
              @error('issuer_logo')<p class="ct-error">{{ $message }}</p>@enderror
              @if($settings['issuer_logo'] !== '')
                <label class="ac-check"><input type="checkbox" name="remove_logo" value="1"> <span>Remove the logo</span></label>
              @endif
              <p class="ct-help">JPG, PNG, GIF or WebP, up to 2 MB. The Institutional layout shows it beside the issuer name, or the issuer's initials when there is none. Class certificates of instructors who belong to an institution use that institution's logo.</p>
            </div>
            <div class="ct-field">
              <label for="ac-layout">Layout of the core certificates</label>
              <select id="ac-layout" class="ct-select{{ $errors->has('system_layout') ? ' is-invalid' : '' }}" name="system_layout">
                @foreach($layouts as $key => $enabled)
                  @if($enabled)
                    <option value="{{ $key }}" @selected(old('system_layout', $settings['system_layout']) === $key)>{{ CertificateLayouts::name($key) }}</option>
                  @endif
                @endforeach
              </select>
              @error('system_layout')<p class="ct-error">{{ $message }}</p>@enderror
            </div>
          </div>
        </div>
      </section>

      <section class="ct-section">
        <h2>Official requirement set of the core challenge certificates</h2>
        <div class="ct-section-body">
          <div class="ct-grid">
            <div class="ct-field">
              <label for="ac-level">Challenge level</label>
              <select id="ac-level" class="ct-select{{ $errors->has('core_challenge_level') ? ' is-invalid' : '' }}" name="core_challenge_level">
                @foreach($levels as $level)
                  <option value="{{ $level->slug }}" @selected(old('core_challenge_level', $settings['core_challenge_level']) === $level->slug)>{{ $level->name }}</option>
                @endforeach
              </select>
              @error('core_challenge_level')<p class="ct-error">{{ $message }}</p>@enderror
              <p class="ct-help">Core 24 Challenge Completion needs this level's MCQ challenge of each Core Module (agreed: Newbie).</p>
            </div>
            <div class="ct-field">
              <span class="ct-label">Core Coding Challenge Completion</span>
              <label class="ac-check"><input type="hidden" name="core_coding_all_levels" value="0"><input type="checkbox" name="core_coding_all_levels" value="1" @checked(old('core_coding_all_levels', $settings['core_coding_all_levels']) === '1')> <span>Require the coding challenges of every level, not only the level above</span></label>
              <p class="ct-help">Either way, only built-in coding challenges of Core Modules count; Core Modules without one are skipped, and custom-module or instructor challenges never count.</p>
            </div>
          </div>
        </div>
      </section>

      <div class="ct-actions"><button class="ct-btn" type="submit">Save settings</button></div>
    </form>
  @else
    <div class="ac-layouts">
      @foreach($layouts as $key => $enabled)
        <section class="ac-layout">
          <h3>{{ CertificateLayouts::name($key) }}</h3>
          <p class="ct-help">{{ CertificateLayouts::LAYOUTS[$key]['description'] }}</p>
          <p class="ct-help">Status: <span class="{{ $enabled ? 'ct-good' : 'ct-muted' }}">{{ $enabled ? 'On' : 'Off' }}</span>@if($settings['system_layout'] === $key) <span class="ct-muted">(used by the core certificates)</span>@endif</p>
          <div class="ct-actions">
            <a class="ct-btn ct-btn-secondary" href="{{ route('admin.certificates.layouts.preview', $key) }}">Preview</a>
            <form method="POST" action="{{ route('admin.certificates.layouts.status', $key) }}">
              @csrf
              @method('PATCH')
              <button class="ct-btn {{ $enabled ? 'ct-btn-secondary' : '' }}" type="submit">{{ $enabled ? 'Turn off' : 'Turn on' }}</button>
            </form>
          </div>
        </section>
      @endforeach
    </div>
    <p class="ct-help" style="margin-top:12px">Turning a layout off stops instructors from choosing it for new or edited certificates. Certificates already issued keep their layout.</p>
  @endif
@endsection
