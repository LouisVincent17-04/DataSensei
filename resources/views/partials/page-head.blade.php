@php
    /**
     * Shared document head: fonts, favicon, description, and social preview.
     *
     * Usage: @include('partials.page-head', [
     *     'pageTitle' => 'Model Development',
     *     'pageDescription' => 'What this page is for, in one sentence.',
     * ])
     */
    $dsPageTitle = trim((string) ($pageTitle ?? ''));
    $dsPageDescription = trim((string) ($pageDescription ?? ''));
    if ($dsPageDescription === '') {
        $dsPageDescription = 'DataSensei is a data science learning platform where students practise Python, SQL, and machine learning with guided feedback.';
    }
    $dsSocialTitle = $dsPageTitle === '' ? 'DataSensei' : $dsPageTitle.' — DataSensei';
@endphp
<meta name="description" content="{{ $dsPageDescription }}">
<meta name="application-name" content="DataSensei">
<meta property="og:type" content="website">
<meta property="og:site_name" content="DataSensei">
<meta property="og:title" content="{{ $dsSocialTitle }}">
<meta property="og:description" content="{{ $dsPageDescription }}">
<meta property="og:image" content="{{ asset('assets/images/logo.png') }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="{{ $dsSocialTitle }}">
<meta name="twitter:description" content="{{ $dsPageDescription }}">
<meta name="twitter:image" content="{{ asset('assets/images/logo.png') }}">
@include('partials.brand-head')
@include('partials.design-system')
@include('partials.page-heading-style')
@unless (request()->routeIs('login'))
{{-- The sign-in page keeps what was typed, in this tab only, so it can retry
     once after a session-expiry bounce. Any other page loading means that is
     over, so it is dropped here rather than left until the tab closes. --}}
<script>try { sessionStorage.removeItem('datasensei.signin.retry'); } catch (e) {}</script>
@endunless
