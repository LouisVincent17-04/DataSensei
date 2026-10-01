<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add from Question Bank — DataSensei</title>
  @include('instructor.assessments._styles')
  @include('instructor.question-bank._bank_styles')
  <style>
    .bank-add-bar{position:sticky;bottom:0;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px 12px;padding:12px 18px;border-top:1px solid var(--border);background:var(--surface)}
    .bank-add-bar .hint{margin:0}
  </style>
  @include('partials.page-head', ['pageTitle' => 'Add from Question Bank', 'pageDescription' => 'Copy reusable questions into this assessment.'])
</head>
<body>
<div class="layout">
  @include('partials.instructor-sidebar')
  <main class="main">
    <div class="wrap wide">
      <div class="top">
        <div>
          <h1 class="title ds-page-title">Add questions to “{{ $assessment->title }}”</h1>
          <p class="subtitle">
            Tick the bank questions to copy in. Each one is added as a snapshot,
            so later changes in the bank never affect this assessment.
          </p>
        </div>
        <div class="actions">
          <a class="btn" href="{{ route('instructor.question-bank.index') }}">Manage the bank</a>
          <a class="btn primary" href="{{ route('instructor.assessments.builder', $assessment) }}">Back to the builder</a>
        </div>
      </div>

      @if(session('success'))<div class="alert success" role="status">{{ session('success') }}</div>@endif
      @if($errors->any())<div class="alert danger" role="alert">{{ $errors->first() }}</div>@endif

      @if($usesTos)
        <div class="alert {{ $missingSlots->isEmpty() ? 'success' : '' }}" role="status">
          This assessment follows a {!! \App\Support\Glossary::help('table_of_specifications', 'Table of Specifications') !!} with {{ $slotCount }} planned items:
          {{ $slotCount - $missingSlots->count() }} filled, {{ $missingSlots->count() }} missing.
          @if($missingSlots->isEmpty())
            Every planned item is filled, so nothing more can be added here.
          @else
            A bank question can fill a missing item only when it is tagged with that item's thinking level (and, when it has a module, the same module).
            It keeps the item's planned points and topic. Questions that match nothing are not added.
          @endif
        </div>
        @if($missingSlots->isNotEmpty())
          <section class="section">
            <div class="section-head"><h2>Missing planned items</h2></div>
            <table class="bank-slots">
              <thead><tr><th scope="col">Item</th><th scope="col">Topic</th><th scope="col">Thinking level</th><th scope="col">Points</th><th scope="col"><span class="sr-only">Write it</span></th></tr></thead>
              <tbody>
                @foreach($missingSlots as $slot)
                  <tr>
                    <td>{{ $slot->item_number }}</td>
                    <td>{{ $slot->topic_title }}</td>
                    <td>{{ $slot->bloom_level ?: 'Any' }}</td>
                    <td>{{ $slot->points }}</td>
                    <td><a href="{{ route('instructor.assessments.builder', $assessment) }}#question-{{ $slot->id }}">Write it in the builder</a></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </section>
        @endif
      @endif

      <section class="section">
        <form class="bank-filter" method="GET" action="{{ route('instructor.assessments.bank', $assessment) }}">
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
          @if($usesTos && $missingSlots->isNotEmpty())
            <div class="field"><label for="f-fit">Show</label>
              <select class="select" id="f-fit" name="fit">
                <option value="fits">Questions that fit a missing item</option>
                <option value="all" @selected(! $fitFilter)>All questions</option>
              </select>
            </div>
          @endif
          <button class="btn" type="submit">Filter</button>
        </form>

        <form id="bank-add-form" method="POST" action="{{ route('instructor.assessments.bank.add', $assessment) }}">@csrf</form>

        @forelse($items as $item)
          @include('instructor.question-bank._item_row', $usesTos
              ? ['item' => $item, 'selectable' => true, 'showFit' => true, 'fitSlot' => $fits[$item->id] ?? null]
              : ['item' => $item, 'selectable' => true])
        @empty
          <p class="bank-empty">
            @if($fitFilter ?? false)
              No bank question fits a missing planned item yet. Tag a question with the item's thinking level in your <a href="{{ route('instructor.question-bank.index') }}">Question Bank</a>, show all questions, or write the item in the builder.
            @else
              No bank questions match these filters. <a href="{{ route('instructor.question-bank.index') }}">Create one in your Question Bank</a>, or write it directly in the builder.
            @endif
          </p>
        @endforelse

        <div class="bank-add-bar">
          <p class="hint">Ticked questions are copied in the order they appear.</p>
          <button class="btn primary" type="submit" form="bank-add-form">Add ticked questions</button>
        </div>
      </section>
      <div class="pagination">{{ $items->links() }}</div>
    </div>
  </main>
</div>
</body>
</html>
