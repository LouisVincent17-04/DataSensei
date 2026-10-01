<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Question Bank — DataSensei</title>
  @include('instructor.assessments._styles')
  @include('instructor.question-bank._bank_styles')
  @include('partials.page-head', ['pageTitle' => 'Question Bank', 'pageDescription' => 'Reusable questions for your assessments.'])
</head>
<body>
<div class="layout">
  @include('partials.instructor-sidebar')
  <main class="main">
    <div class="wrap wide">
      <div class="top">
        <div>
          <h1 class="title ds-page-title">Question Bank</h1>
          <p class="subtitle">
            Reusable questions for your assessments: {{ number_format($ownCount) }} of yours and {{ number_format($sharedCount) }} in the shared pool.
            Adding one to an assessment copies it, so editing or archiving it here never changes an assessment that already uses it.
          </p>
        </div>
        <div class="actions">
          <a class="btn" href="{{ route('instructor.assessments.index') }}">Assessments</a>
          <a class="btn primary" href="#edit-question">Add a question</a>
        </div>
      </div>

      @if(session('success'))<div class="alert success" role="status">{{ session('success') }}</div>@endif
      @if($errors->any())<div class="alert danger" role="alert">{{ $errors->first() }}</div>@endif

      <section class="section" id="edit-question">
        <div class="section-head">
          <h2>{{ $editing ? 'Edit question' : 'Add a question' }}</h2>
          @if($editing)<a class="btn small" href="{{ route('instructor.question-bank.index') }}">Cancel editing</a>@endif
        </div>
        <div class="section-body">
          @include('instructor.question-bank._item_form', [
              'item' => $editing,
              'types' => $types,
              'ilos' => $ilos,
              'action' => $editing ? route('instructor.question-bank.update', $editing) : route('instructor.question-bank.store'),
              'method' => $editing ? 'PATCH' : 'POST',
              'submitLabel' => $editing ? 'Save changes' : 'Add to bank',
          ])
        </div>
      </section>

      <section class="section">
        <form class="bank-filter" method="GET" action="{{ route('instructor.question-bank.index') }}">
          <div class="field"><label for="f-search">Search</label><input class="input" id="f-search" name="search" value="{{ request('search') }}" placeholder="Question or topic"></div>
          <div class="field"><label for="f-scope">Whose</label>
            <select class="select" id="f-scope" name="scope">
              <option value="">Mine and shared</option>
              <option value="mine" @selected(request('scope') === 'mine')>Mine only</option>
              <option value="shared" @selected(request('scope') === 'shared')>Shared pool only</option>
            </select>
          </div>
          <div class="field"><label for="f-module">Module</label><input class="input" id="f-module" type="number" min="1" name="module_no" value="{{ request('module_no') }}"></div>
          <div class="field"><label for="f-type">Type</label>
            <select class="select" id="f-type" name="question_type">
              <option value="">All</option>
              @foreach($types as $value => $label)<option value="{{ $value }}" @selected(request('question_type') === $value)>{{ $label }}</option>@endforeach
            </select>
          </div>
          <div class="field"><label for="f-difficulty">Difficulty</label>
            <select class="select" id="f-difficulty" name="difficulty">
              <option value="">All</option>
              @foreach($difficulties as $slug => $label)<option value="{{ $slug }}" @selected(request('difficulty') === $slug)>{{ $label }}</option>@endforeach
            </select>
          </div>
          <div class="field"><label for="f-level">Thinking level</label>
            <select class="select" id="f-level" name="bloom_level">
              <option value="">All</option>
              @foreach($thinkingLevels as $level)<option value="{{ $level }}" @selected(request('bloom_level') === $level)>{{ $level }}</option>@endforeach
            </select>
          </div>
          <div class="field"><label for="f-ilo">Learning outcome</label>
            <select class="select" id="f-ilo" name="ilo_id">
              <option value="">All</option>
              @foreach($ilos as $ilo)<option value="{{ $ilo->id }}" @selected(request('ilo_id') == $ilo->id)>Module {{ $ilo->module_no }}: {{ \Illuminate\Support\Str::limit($ilo->title, 45) }}</option>@endforeach
            </select>
          </div>
          <label class="q-inline" style="margin:0 0 9px"><input type="checkbox" name="archived" value="1" @checked(request()->boolean('archived'))> Archived only</label>
          <button class="btn" type="submit">Filter</button>
        </form>

        @forelse($items as $item)
          @include('instructor.question-bank._item_row', ['item' => $item, 'manageable' => true])
        @empty
          <p class="bank-empty">No questions match. Add your first question above, or clear the filters.</p>
        @endforelse
        <div class="pagination" style="padding:12px 18px">{{ $items->links() }}</div>
      </section>
    </div>
  </main>
</div>
</body>
</html>
