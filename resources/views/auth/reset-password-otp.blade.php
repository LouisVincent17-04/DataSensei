@extends('layouts.auth-reset', ['title' => 'Create New Password', 'step' => 3])

@section('content')
  <h1 class="heading ds-page-title">Create a new password</h1>
  <p class="subheading">Your email has been verified. Choose a strong password that you do not use on another service.</p>

  @if ($errors->any() && ! $errors->has('rate_limit'))
    <div class="alert alert-danger">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('password.otp.reset') }}" data-loading-form>
    @csrf
    <div class="field">
      <label class="field-label" for="password">New password</label>
      <div class="field-wrap">
        <input class="field-input password-input" id="password" type="password" name="password" autocomplete="new-password" required autofocus>
        <button class="toggle" type="button" data-password-toggle="password" aria-label="Show password">Show</button>
      </div>
      @error('password')<div class="field-error">{{ $message }}</div>@enderror
    </div>

    <div class="field">
      <label class="field-label" for="password-confirmation">Confirm new password</label>
      <div class="field-wrap">
        <input class="field-input password-input" id="password-confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>
        <button class="toggle" type="button" data-password-toggle="password-confirmation" aria-label="Show password">Show</button>
      </div>
    </div>

    <div class="password-rules">
      <strong>Password requirements</strong>
      <p>At least {{ config('password_otp.password_min_length', 8) }} characters with uppercase and lowercase letters, a number, and a symbol.</p>
    </div>

    <button class="button" type="submit" data-loading-button data-loading-text="Resetting password…">
      <span class="spinner" aria-hidden="true"></span>
      <span data-button-text>Reset password</span>
    </button>
  </form>
@endsection
