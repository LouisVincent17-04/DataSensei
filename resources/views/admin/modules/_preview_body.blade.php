{{--
    The body of a DataSensei module preview (DataSensei Updates 5): every
    section rendered as the learning room shows it, in order, with the
    module's learning outcomes first and its review questions last. Shown
    inside admin.modules.lessons._preview_frame.
--}}
<h2>{{ $title }}</h2>
@if(trim($description) !== '')
  <p>{{ $description }}</p>
@endif

@if(! empty($outcomes))
  <div class="lesson-callout" style="margin:0 0 24px;padding:14px 18px;border:1px solid var(--border);border-radius:12px;background:var(--surface2);">
    <h4 style="margin:0 0 8px;font-size:0.95rem;">What you will learn</h4>
    <ul style="margin:0;padding-left:20px;line-height:1.6;">
      @foreach($outcomes as $outcome)
        <li>{{ $outcome }}</li>
      @endforeach
    </ul>
  </div>
@else
  <p style="color:var(--muted);">No learning outcomes yet. A module needs at least one before it can be published.</p>
@endif

@forelse($sections as $index => $section)
  <hr style="margin:32px 0 20px;border:0;border-top:1px solid var(--border);">
  <p style="margin:0 0 12px;color:var(--muted);font-size:.8125rem;">Section {{ $index + 1 }} of {{ count($sections) }}: {{ $section['title'] !== '' ? $section['title'] : 'Untitled section' }}</p>
  @if(trim($section['html']) === '')
    <p style="color:var(--muted);">This section has no content yet.</p>
  @else
    {!! $section['html'] !!}
  @endif
@empty
  <hr style="margin:32px 0 20px;border:0;border-top:1px solid var(--border);">
  <p style="color:var(--muted);">No sections yet. Add one under Learning Content.</p>
@endforelse

@if(! empty($questions))
  <hr style="margin:32px 0 20px;border:0;border-top:1px solid var(--border);">
  <h2>Review questions</h2>
  <p>Students answer these after the last section, for practice only. The correct answer is marked here; students see it after they check their answer.</p>
  @foreach($questions as $qIndex => $question)
    @php
      $choices = array_values((array) ($question['choices'] ?? []));
      $answer = (string) ($question['answer'] ?? '');
    @endphp
    <h3>Question {{ $qIndex + 1 }}@if(! empty($question['topic'])), {{ $question['topic'] }}@endif</h3>
    <p><strong>{{ $question['question'] ?? '' }}</strong></p>
    @if(! empty($question['scenario']))
      <p>{{ $question['scenario'] }}</p>
    @endif
    <ol type="A">
      @foreach($choices as $choice)
        <li>{{ $choice }}@if((string) $choice === $answer) <strong>(correct answer)</strong>@endif</li>
      @endforeach
    </ol>
    @if(! empty($question['explanation']))
      <p><strong>Explanation:</strong> {{ $question['explanation'] }}</p>
    @endif
    @if(! empty($question['learning_tip']))
      <p><strong>Learning tip:</strong> {{ $question['learning_tip'] }}</p>
    @endif
  @endforeach
@endif
