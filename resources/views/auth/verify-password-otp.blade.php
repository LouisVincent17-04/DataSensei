@extends('layouts.auth-reset', ['title' => 'Verify Code', 'step' => 2])

@section('content')
  <h1 class="heading">Verify your email</h1>
  <p class="subheading">Enter the six-digit code sent to <strong>{{ $maskedEmail }}</strong>. The code expires after {{ config('password_otp.expires_minutes', 5) }} minutes and can be used only once.</p>

  @if ($errors->has('otp'))
    <div class="alert alert-danger">{{ $errors->first('otp') }}</div>
  @endif

  <form method="POST" action="{{ route('password.otp.verify') }}" data-loading-form>
    @csrf
    <input type="hidden" name="email" value="{{ $email }}">

    <div class="field">
      <label class="field-label" for="otp">Verification code</label>
      <input
        class="field-input otp-input"
        id="otp"
        type="text"
        name="otp"
        value=""
        inputmode="numeric"
        pattern="[0-9]*"
        maxlength="{{ config('password_otp.length', 6) }}"
        autocomplete="one-time-code"
        placeholder="000000"
        required
        autofocus
      >
    </div>

    <button class="button" type="submit" data-loading-button data-loading-text="Verifying…">
      <span class="spinner" aria-hidden="true"></span>
      <span data-button-text>Verify code</span>
    </button>
  </form>

  <div class="resend-row">
    <span id="resend-message">Didn’t receive the code?</span>
    <form method="POST" action="{{ route('password.otp.send') }}" data-loading-form>
      @csrf
      <input type="hidden" name="email" value="{{ $email }}">
      <button
        class="resend-button"
        id="resend-button"
        type="submit"
        data-loading-button
        data-loading-text="Sending…"
        data-cooldown="{{ $cooldownRemaining }}"
        {{ $cooldownRemaining > 0 ? 'disabled' : '' }}
      ><span data-button-text>Resend code</span></button>
    </form>
  </div>

  <div class="link-row"><a class="link" href="{{ route('password.request') }}">Use a different email</a></div>
@endsection

@push('scripts')
<script>
  (() => {
    const button = document.getElementById('resend-button');
    const message = document.getElementById('resend-message');
    if (!button || !message) return;

    let remaining = Number(button.dataset.cooldown || 0);
    const render = () => {
      if (remaining <= 0) {
        button.disabled = false;
        button.querySelector('[data-button-text]').textContent = 'Resend code';
        message.textContent = 'Didn’t receive the code?';
        return;
      }
      button.disabled = true;
      button.querySelector('[data-button-text]').textContent = `Resend in ${remaining}s`;
      message.textContent = 'A new code can be requested shortly.';
      remaining -= 1;
      window.setTimeout(render, 1000);
    };
    render();
  })();
</script>
@endpush
