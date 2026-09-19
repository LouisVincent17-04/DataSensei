@extends('layouts.auth-reset', ['title' => 'Forgot Password', 'step' => 1])

@section('content')
  <h1 class="heading ds-page-title">Forgot your password?</h1>
  <p class="subheading">Enter the email address registered to your DataSensei account. For privacy, the response is the same whether or not an account exists.</p>

  @if ($errors->any() && ! $errors->has('rate_limit'))
    <div class="alert alert-danger">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('password.otp.send') }}" data-loading-form>
    @csrf
    <div class="field">
      <label class="field-label" for="reset-email">Registered email address</label>
      <input
        class="field-input"
        id="reset-email"
        type="email"
        name="email"
        value="{{ old('email') }}"
        placeholder="you@university.edu"
        autocomplete="email"
        required
        autofocus
      >
      @error('email')<div class="field-error">{{ $message }}</div>@enderror
    </div>

    <div class="actions">
      <button class="button" type="submit" data-loading-button data-loading-text="Sending code…">
        <span class="spinner" aria-hidden="true"></span>
        <span data-button-text>Send verification code</span>
      </button>
      <a class="button button-secondary" href="{{ route('login') }}">Back to sign in</a>
    </div>
  </form>
@endsection
