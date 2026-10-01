{{-- The full content of an assignment for the instructor (DataSensei Updates 9):
     every question with its points, all answer choices with the correct one
     marked, the accepted answers of fill-in-the-blank questions and the
     explanation. Needs $item (AssignmentLibraryItem with questions.options and
     questions.blankAnswers loaded). --}}
@once
<style>
  .ac-list{margin:0;padding:0;list-style:none}
  .ac-q{padding:16px 0;border-top:1px solid var(--border)}
  .ac-q:first-child{border-top:0;padding-top:4px}
  .ac-q-head{display:flex;justify-content:space-between;gap:12px;color:var(--muted);font-size:.8125rem;font-weight:500}
  .ac-q-text{margin:6px 0 10px;color:var(--text);font-size:.9375rem;font-weight:600;line-height:1.55;white-space:pre-wrap;overflow-wrap:anywhere}
  .ac-choices{display:grid;gap:6px;margin:0;padding:0;list-style:none}
  .ac-choice{display:flex;gap:10px;align-items:flex-start;padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);color:var(--ds-text-secondary);font-size:.875rem;line-height:1.5}
  .ac-choice b{flex:0 0 18px;color:var(--muted);font-weight:600}
  .ac-choice span{min-width:0;flex:1 1 auto;overflow-wrap:anywhere}
  .ac-choice.is-correct{border-color:var(--ds-success-border);color:var(--text)}
  .ac-choice em{flex-shrink:0;color:var(--ds-success-text);font-size:.8125rem;font-style:normal;font-weight:600}
  .ac-answer{margin:0;color:var(--ds-text-secondary);font-size:.875rem;line-height:1.55}
  .ac-answer strong{color:var(--ds-success-text);font-weight:600}
  .ac-expl{margin:10px 0 0;color:var(--muted);font-size:.8125rem;line-height:1.55}
  .ac-expl strong{color:var(--ds-text-secondary);font-weight:600}
  .ac-empty{padding:16px 0;color:var(--muted);font-size:.875rem}
</style>
@endonce
<ol class="ac-list">
  @forelse($item->questions as $question)
    <li class="ac-q">
      <div class="ac-q-head">
        <span>Question {{ $loop->iteration }}, {{ $question->question_type === 'mcq' ? 'Multiple choice' : 'Fill in the blank' }}</span>
        <span>{{ $question->points }} {{ (int) $question->points === 1 ? 'point' : 'points' }}</span>
      </div>
      <p class="ac-q-text">{{ $question->question_text }}</p>

      @if($question->question_type === 'mcq')
        @php $literal = \App\Support\ChoiceText::anySignificant($question->options); @endphp
        <ol class="ac-choices">
          @foreach($question->options as $option)
            <li class="ac-choice {{ $option->is_correct ? 'is-correct' : '' }}">
              <b>{{ chr(64 + $loop->iteration) }}.</b>
              <span>{{ \App\Support\ChoiceText::html($option->option_text, $literal) }}</span>
              @if($option->is_correct)<em>Correct answer</em>@endif
            </li>
          @endforeach
        </ol>
      @else
        @php $accepted = $question->blankAnswers->pluck('answer_text')->filter(fn ($a) => trim((string) $a) !== '')->values(); @endphp
        <p class="ac-answer">
          Expected answer:
          @if($accepted->isEmpty())
            <span>none set</span>
          @else
            <strong>{{ $accepted->first() }}</strong>
            @if($accepted->count() > 1)
              <br>Also accepted: {{ $accepted->slice(1)->join(', ') }}
            @endif
          @endif
        </p>
      @endif

      @if(filled($question->explanation))
        <p class="ac-expl"><strong>Explanation:</strong> {{ $question->explanation }}</p>
      @endif
    </li>
  @empty
    <li class="ac-empty">This assignment has no questions yet.</li>
  @endforelse
</ol>
