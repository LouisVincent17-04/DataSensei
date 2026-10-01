{{-- One bank question in a list. Needs $item; optional $selectable (picker checkbox),
     $manageable (edit/archive actions), $showFit + $fitSlot (picker for a TOS
     assessment: the missing planned item this question would fill, or null). --}}
<div class="bank-row">
  <div class="bank-row-head">
    @if($selectable ?? false)
      <label class="bank-pick"><input type="checkbox" name="question_ids[]" value="{{ $item->id }}" form="bank-add-form"> Add</label>
    @endif
    <div>
      <p class="bank-q">{{ $item->question_text }}</p>
      <p class="bank-meta">
        {{ $item->typeLabel() }}, {{ $item->points }} {{ (int) $item->points === 1 ? 'point' : 'points' }}.
        @if($item->module_no)Module {{ $item->module_no }}.@endif
        @if($item->topic_title){{ $item->topic_title }}.@endif
        @if($item->difficulty_slug){{ \App\Models\QuestionBankItem::DIFFICULTIES[$item->difficulty_slug] ?? ucwords(str_replace('-', ' ', $item->difficulty_slug)) }}.@endif
        @if($item->bloom_level)Thinking level: {{ $item->bloom_level }}.@endif
        @if($item->ilo)Outcome: {{ \Illuminate\Support\Str::limit($item->ilo->title, 70) }}.@endif
        @if($item->isShared())<span class="shared">Shared pool</span>@endif
        @if($item->is_archived)<span class="archived">Archived</span>@endif
      </p>
      @if($showFit ?? false)
        <p class="bank-fit {{ $fitSlot ? 'fits' : 'no-fit' }}">
          @if($fitSlot)
            Fills planned item {{ $fitSlot->item_number }} ({{ $fitSlot->topic_title }}@if($fitSlot->bloom_level), {{ $fitSlot->bloom_level }}@endif).
          @else
            Does not match a missing planned item.
          @endif
        </p>
      @endif
      <details class="bank-preview">
        <summary>Preview with the answer</summary>
        <div class="bank-preview-body">
          @if($item->question_type === 'multiple_choice')
            <ul>
              @foreach($item->options as $option)
                <li @class(['correct' => $option->is_correct])>{{ chr(64 + $loop->iteration) }}. {{ $option->option_text }}@if($option->is_correct) — correct answer @endif</li>
              @endforeach
            </ul>
          @elseif($item->question_type === 'true_false')
            Correct answer: <span class="correct">{{ ucfirst((string) $item->correct_answer) }}</span>
          @elseif($item->question_type === 'essay')
            Graded by you.@if($item->rubric_text) Grading notes: {{ $item->rubric_text }}@endif
          @else
            Accepted: <span class="correct">{{ collect(preg_split('/\r\n|\r|\n/', (string) $item->correct_answer))->map(fn ($a) => trim($a))->filter(fn ($a) => $a !== '')->join(', ') ?: 'not set' }}</span>
          @endif
          @if($item->answer_explanation)
            <div style="margin-top:6px;color:var(--muted)">Explanation: {{ $item->answer_explanation }}</div>
          @endif
        </div>
      </details>
    </div>
    @if($manageable ?? false)
      <div class="bank-actions">
        @if($item->isEditableBy((int) auth()->id()))
          <a class="btn small" href="{{ route('instructor.question-bank.index', array_merge(request()->query(), ['edit' => $item->id])) }}#edit-question">Edit</a>
          <form method="POST" action="{{ route('instructor.question-bank.archive', $item) }}">
            @csrf
            @method('PATCH')
            <button class="btn small" type="submit">{{ $item->is_archived ? 'Restore' : 'Archive' }}</button>
          </form>
        @else
          <span class="bank-meta">Read-only</span>
        @endif
      </div>
    @endif
  </div>
</div>
