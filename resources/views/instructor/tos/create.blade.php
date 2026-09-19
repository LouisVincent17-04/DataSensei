<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create TOS — DataSensei</title>
@include('instructor.tos.partials.styles')
    @include('partials.page-head', ['pageTitle' => 'Create TOS', 'pageDescription' => 'Build a table of specifications for a balanced assessment.'])
</head>
<body>
<div class="layout">
  @include('partials.instructor-sidebar')
  <main class="main">
    <div class="wrap">
      <div class="top">
        <div>
          <h1 class="title ds-page-title">Create Table of Specifications</h1>
          <p class="subtitle">Start with only the information needed to build the assessment blueprint.</p>
        </div>
        <a class="btn secondary" href="{{ route('instructor.tos.index') }}">Back to TOS</a>
      </div>

      @include('instructor.tos.partials.wizard', ['currentStep' => 1])

      @if($errors->any())
        <div class="alert error">
          @foreach($errors->all() as $error){{ $error }}<br>@endforeach
        </div>
      @endif

      @php($usesCustomCoverage = old('module_no') === 'other')

      <form method="POST" action="{{ route('instructor.tos.store') }}" class="card">
        @csrf
        <div class="section-title">
          <div>
            <h2>Basic Information</h2>
            <div class="muted">No question writing yet. This step only defines what the assessment is for.</div>
          </div>
        </div>

        <div class="grid grid-2">
          <div class="field" style="grid-column:1/-1">
            <label for="title">Assessment Title</label>
            <input class="input" id="title" name="title" value="{{ old('title') }}" placeholder="e.g. Midterm Examination" required maxlength="191">
          </div>

          <div class="field">
            <label for="class_id">Subject / Course</label>
            <select class="select" id="class_id" name="class_id">
              <option value="">Template / no class yet</option>
              @foreach($classes as $class)
                <option value="{{ $class->id }}" @selected((string) old('class_id') === (string) $class->id)>
                  {{ $class->subject_code ? $class->subject_code.' — ' : '' }}{{ $class->name }}{{ $class->section ? ', '.$class->section : '' }}
                </option>
              @endforeach
            </select>
            <div class="muted small" style="margin-top:8px">You can create a reusable template without attaching it to a class.</div>
          </div>

          <div class="field">
            <label for="module_no">Module / Coverage</label>
            <select class="select" id="module_no" name="module_no" required>
              <option value="">Select module coverage</option>
              @foreach($modules as $module)
                <option value="{{ $module->order_index }}" @selected((string) old('module_no') === (string) $module->order_index)>
                  Module {{ $module->order_index }} — {{ $module->title }}
                </option>
              @endforeach
              <option value="other" @selected($usesCustomCoverage)>Others — Enter manually</option>
            </select>
            <div id="custom-coverage-field" style="margin-top:8px" @if(! $usesCustomCoverage) hidden @endif>
              <label for="custom_coverage">Other Module / Coverage</label>
              <input
                class="input"
                id="custom_coverage"
                name="custom_coverage"
                value="{{ old('custom_coverage') }}"
                placeholder="e.g. Data Ethics and Responsible AI"
                maxlength="191"
                @if($usesCustomCoverage) required @else disabled @endif>
              <div class="muted small" style="margin-top:8px">Enter the exact module or coverage for this assessment.</div>
            </div>
          </div>

          <div class="field">
            <label for="total_items">Total Number of Items</label>
            <input class="input" id="total_items" type="number" min="1" max="200" name="total_items" value="{{ old('total_items', 50) }}" required>
          </div>

          <div class="help">
            <strong>What DataSensei does next</strong>
            <p style="margin-bottom:0">For an existing module, it loads the competencies already defined for that module. For Others, it uses your custom coverage. It then creates a suggested Remember / Understand / Apply / Analyze distribution that you can change in Step 2.</p>
          </div>
        </div>

        <div class="footer-actions">
          <a class="btn secondary" href="{{ route('instructor.tos.index') }}">Cancel</a>
          <button class="btn" type="submit">Continue to Distribution →</button>
        </div>
      </form>
    </div>
  </main>
</div>
<script>
(() => {
  const coverageSelect = document.getElementById('module_no');
  const customField = document.getElementById('custom-coverage-field');
  const customInput = document.getElementById('custom_coverage');

  if (!coverageSelect || !customField || !customInput) return;

  const syncCustomCoverage = () => {
    const isCustom = coverageSelect.value === 'other';
    customField.hidden = !isCustom;
    customInput.disabled = !isCustom;
    customInput.required = isCustom;

    if (isCustom) customInput.focus();
  };

  coverageSelect.addEventListener('change', syncCustomCoverage);
  syncCustomCoverage();
})();
</script>
</body>
</html>
