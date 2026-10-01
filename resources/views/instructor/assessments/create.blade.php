<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Create Assessment — DataSensei</title>
  @include('instructor.assessments._styles')
  @include('partials.page-head', ['pageTitle' => 'Create Assessment', 'pageDescription' => 'Create an assessment, then add its questions.'])
</head>
<body>
<div class="layout">
  @include('partials.instructor-sidebar')
  <main class="main">
    <div class="wrap">
      <a class="crumb" href="{{ route('instructor.assessments.index') }}">&larr; Assessments</a>
      <div class="top">
        <div>
          <h1 class="title ds-page-title">Create assessment</h1>
          <p class="subtitle">
            @if($tos)
              Based on the Table of Specifications "{{ $tos->title }}": {{ $totalItems }} questions will be planned for you to write on the next page.
            @else
              Step 1 of 2. Fill in the details, then add the questions on the next page. It is saved as a draft; students see it only after you publish it.
            @endif
          </p>
        </div>
      </div>

      @if($errors->any())
        <div class="alert danger"><strong>Please check the form.</strong>
          <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
      @endif

      <form method="POST" action="{{ $tos ? route('instructor.assessments.store', $tos) : route('instructor.assessments.save') }}" class="section">
        @csrf
        <div class="section-head"><h2>Assessment details</h2></div>
        <div class="section-body">
          @include('instructor.assessments._settings_fields', ['assessment' => null])
          <div class="form-foot">
            <a class="btn" href="{{ $tos ? route('instructor.tos.show', $tos) : route('instructor.assessments.index') }}">Cancel</a>
            <button class="btn primary" type="submit">Save and add questions</button>
          </div>
        </div>
      </form>
    </div>
  </main>
</div>
</body>
</html>
