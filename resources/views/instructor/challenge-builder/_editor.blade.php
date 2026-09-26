{{-- Full page shared by create and edit: $type (mcq|coding), $challenge, $questions,
     $hasHistory, $pageTitle, $pageIntro, $formAction, $formMethod, $submitLabel. --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $pageTitle }} — DataSensei</title>
  @include('instructor.challenge-builder._styles')
  @include('partials.admin-inspired-page-style')
  @include('partials.page-head', ['pageTitle' => $pageTitle, 'pageDescription' => 'Build your own quiz and coding challenges and give them to your classes.'])
</head>
<body class="ds-admin-inspired">
  <div class="ds-shell">
    @include('partials.instructor-sidebar')
    <main class="ds-main">
      <div class="wrap">
        <div class="top-row">
          <div>
            <h1 class="page-title ds-page-title">{{ $pageTitle }}</h1>
            <p class="page-subtitle">{{ $pageIntro }}</p>
          </div>
          <a class="btn secondary" href="{{ route('instructor.challenge-builder.index') }}">Back to my challenges</a>
        </div>

        @if($errors->any())
          <div class="notice error" role="alert">
            <strong>Please fix the following:</strong>
            <ul>
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        @if($type === 'coding')
          @include('instructor.challenge-builder._coding_form', [
            'formAction' => $formAction,
            'formMethod' => $formMethod,
            'submitLabel' => $submitLabel,
            'cancelUrl' => route('instructor.challenge-builder.index'),
          ])
        @else
          @include('instructor.challenge-builder._mcq_form', [
            'formAction' => $formAction,
            'formMethod' => $formMethod,
            'submitLabel' => $submitLabel,
            'cancelUrl' => route('instructor.challenge-builder.index'),
          ])
        @endif
      </div>
    </main>
  </div>
  <script src="{{ asset('js/instructor-challenge-builder.js') }}"></script>
</body>
</html>
