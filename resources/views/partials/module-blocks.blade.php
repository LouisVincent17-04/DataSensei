{{--
    One section's content blocks in the module viewers (DataSensei Updates 6).

    Used by the student and instructor module viewers and by the admin
    preview of library modules, so every role sees the same organised lesson:
    text, lists, notes, examples, code, walkthroughs, activities, key points.
    Never raw HTML or JSON. Blocks come from ModuleBlockConverter, so a section
    saved before the module builder existed shows the same way.

    Parameters:
      blocks         list of blocks (required)
      isStudentView  show "Try in Compiler" / "Try in SQL Sandbox" buttons
--}}
@php
  /** @var \App\Services\LessonBlockRenderer $blockRenderer */
  $blockRenderer = app(\App\Services\LessonBlockRenderer::class);
  $blockItems = fn ($value) => array_values(array_filter(array_map(fn ($item) => is_scalar($item) ? trim((string) $item) : '', (array) $value), fn ($item) => $item !== ''));
@endphp
@once
<style>
  .block-heading { margin: 24px 0 8px; color: var(--text); font-size: 1.0625rem; font-weight: 600; line-height: 1.4; }
  .block-subheading { margin: 20px 0 6px; color: var(--text); font-size: .9375rem; font-weight: 600; line-height: 1.4; }
  .block-heading:first-child, .block-subheading:first-child { margin-top: 0; }
  .lesson-body p { margin: 0 0 12px; }
  .lesson-body p:last-child { margin-bottom: 0; }
  .lesson-body + .lesson-body { margin-top: 12px; }
  .lesson-body code { padding: .1em .35em; border: 1px solid var(--border); border-radius: var(--radius-xs); background: var(--surface2); color: var(--text); font-family: var(--ds-font-mono); font-size: .875em; }
  .block-list { max-width: 75ch; margin: 12px 0 0; padding-left: 22px; color: var(--ds-text-secondary); font-size: .9375rem; line-height: 1.65; }
  .block-list li + li { margin-top: 4px; }
  .code-output { padding: 12px 16px 14px; border-top: 1px solid var(--border); }
  .code-output span { display: block; margin-bottom: 6px; color: var(--dim); font-size: .75rem; font-weight: 600; }
  .code-output pre { margin: 0; padding: 0 !important; color: var(--ds-text-secondary); font: .8125rem/1.6 var(--ds-font-mono); white-space: pre-wrap; }
  .block-explanation { margin-top: 12px; }
  .block-figure { margin: 20px 0 0; }
  .block-figure img { display: block; max-width: 100%; height: auto; border: 1px solid var(--border); border-radius: var(--radius-sm); }
  .block-figure figcaption { margin-top: 6px; color: var(--muted); font-size: .8125rem; }
  .block-table { margin-top: 20px; overflow-x: auto; border: 1px solid var(--border); border-radius: var(--radius); }
  .block-table table { width: 100%; border-collapse: collapse; font-size: .875rem; }
  .block-table caption { padding: 10px 14px; color: var(--muted); font-size: .8125rem; text-align: left; caption-side: top; }
  .block-table th, .block-table td { padding: 8px 12px; border-top: 1px solid var(--border); color: var(--ds-text-secondary); text-align: left; vertical-align: top; }
  .block-table th { color: var(--text); font-weight: 600; background: var(--surface3); }
  .block-table-note { padding: 10px 14px; border-top: 1px solid var(--border); color: var(--muted); font-size: .8125rem; white-space: pre-wrap; }
  .panel-note { background: var(--ds-accent-soft); border-color: var(--ds-accent-border); color: var(--ds-accent-ink, #dbeafe); }
  .panel p { margin: 0 0 6px; }
  .panel p:last-child { margin-bottom: 0; }
  .block-original { margin-top: 16px; }
</style>
@endonce
@foreach($blocks as $block)
  @php $type = (string) ($block['type'] ?? ''); @endphp
  @switch($type)
    @case('heading')
      @if(trim((string) ($block['text'] ?? '')) !== '')
        <h4 class="block-heading">{!! $blockRenderer->inline((string) $block['text']) !!}</h4>
      @endif
      @break
    @case('subheading')
      @if(trim((string) ($block['text'] ?? '')) !== '')
        <h5 class="block-subheading">{!! $blockRenderer->inline((string) $block['text']) !!}</h5>
      @endif
      @break
    @case('paragraph')
      @if(trim((string) ($block['text'] ?? '')) !== '')
        <div class="lesson-body">{!! $blockRenderer->paragraphs((string) $block['text']) !!}</div>
      @endif
      @break
    @case('bulleted_list')
    @case('numbered_list')
      @php $listItems = $blockItems($block['items'] ?? []); $listTag = $type === 'numbered_list' ? 'ol' : 'ul'; @endphp
      @if($listItems !== [])
        <{{ $listTag }} class="block-list">
          @foreach($listItems as $item)
            <li>{!! $blockRenderer->inline($item) !!}</li>
          @endforeach
        </{{ $listTag }}>
      @endif
      @break
    @case('note')
    @case('example')
    @case('activity')
      @php
        $calloutText = trim((string) ($block['text'] ?? ''));
        $calloutTitle = trim((string) ($block['title'] ?? '')) ?: ['note' => 'Important Note', 'example' => 'Example', 'activity' => 'Practice Activity'][$type];
        // Seeded activities start with their own name ("Outcome Mapping
        // Activity: ..."); that name becomes the title instead of repeating.
        if ($type === 'activity' && preg_match('/^\s*([A-Z][A-Za-z -]{2,48}Activity)\s*:\s*(.+)$/s', $calloutText, $activityMatch)) {
            [$calloutTitle, $calloutText] = [$activityMatch[1], trim($activityMatch[2])];
        }
      @endphp
      @if($calloutText !== '')
        @if($type === 'activity')
          <div class="activity">
            <strong>{{ $calloutTitle }}:</strong>
            <p>{!! nl2br($blockRenderer->inline($calloutText)) !!}</p>
          </div>
        @else
          <div class="panel {{ $type === 'note' ? 'panel-note' : '' }}">
            <h4>{{ $calloutTitle }}</h4>
            {!! $blockRenderer->paragraphs($calloutText) !!}
          </div>
        @endif
      @endif
      @break
    @case('walkthrough')
    @case('mistakes')
    @case('key_points')
    @case('check')
      @php
        $calloutItems = $blockItems($block['items'] ?? []);
        [$calloutTitle, $calloutClass] = [
            'walkthrough' => ['Walkthrough', ''],
            'mistakes' => ['Common Mistakes', 'panel-warning'],
            'key_points' => ['Key Points', 'panel-success'],
            'check' => ['Check Your Understanding', ''],
        ][$type];
      @endphp
      @if($calloutItems !== [])
        <div class="panel {{ $calloutClass }}">
          <h4>{{ $calloutTitle }}</h4>
          <{{ $type === 'walkthrough' ? 'ol' : 'ul' }}>
            @foreach($calloutItems as $item)
              <li>{!! $blockRenderer->inline($item) !!}</li>
            @endforeach
          </{{ $type === 'walkthrough' ? 'ol' : 'ul' }}>
        </div>
      @endif
      @break
    @case('python_code')
    @case('sql_code')
    @case('code_snippet')
    @case('code')
      @php
        $code = (string) ($block['code'] ?? '');
        $language = match ($type) { 'sql_code' => 'sql', 'code_snippet' => 'text', 'python_code' => 'python', default => (string) ($block['language'] ?? 'python') };
        $codeTitle = trim((string) ($block['title'] ?? $block['label'] ?? '')) ?: ['sql' => 'SQL Example', 'text' => 'Code Example'][$language] ?? 'Python Example';
        $codeOutput = rtrim((string) ($type === 'sql_code' ? ($block['result'] ?? '') : ($block['output'] ?? '')));
        $codeExplanation = trim((string) ($block['explanation'] ?? ''));
      @endphp
      @if(trim($code) !== '')
        <div class="code-card">
          <div class="code-head">
            <div class="code-title">{{ $codeTitle }}</div>
            @if(($isStudentView ?? false) && $language !== 'text')
              <button type="button" class="try-btn" onclick='tryInCompiler(@json($code))'>{{ $language === 'sql' ? 'Try in SQL Sandbox' : 'Try in Compiler' }}</button>
            @endif
          </div>
          <pre><code>{!! $blockRenderer->highlight($code, $language) !!}</code></pre>
          @if($codeOutput !== '')
            <div class="code-output">
              <span>{{ $type === 'sql_code' ? 'Expected Result' : 'Output' }}</span>
              <pre>{{ $codeOutput }}</pre>
            </div>
          @endif
        </div>
      @elseif($codeOutput !== '')
        <div class="panel"><h4>{{ $type === 'sql_code' ? 'Expected Result' : 'Output' }}</h4><p style="white-space:pre-wrap">{{ $codeOutput }}</p></div>
      @endif
      @if($codeExplanation !== '')
        <div class="lesson-body block-explanation">{!! $blockRenderer->paragraphs($codeExplanation) !!}</div>
      @endif
      @break
    @case('image')
      @php $imageSrc = $blockRenderer->imageSource((string) ($block['src'] ?? '')); @endphp
      @if($imageSrc !== '')
        <figure class="block-figure">
          <img src="{{ $imageSrc }}" alt="{{ $block['alt'] ?? '' }}" style="width:{{ max(25, min(100, (int) ($block['width'] ?? 100))) }}%">
          @if(trim((string) ($block['caption'] ?? '')) !== '')
            <figcaption>{{ $block['caption'] }}</figcaption>
          @endif
        </figure>
      @endif
      @break
    @case('table')
      @php
        $tableColumns = array_values((array) ($block['columns'] ?? []));
        $tableRows = array_values((array) ($block['rows'] ?? []));
      @endphp
      @if($tableColumns !== [] || $tableRows !== [])
        <div class="block-table">
          <table>
            @if(trim((string) ($block['label'] ?? '')) !== '')
              <caption>{{ $block['label'] }}</caption>
            @endif
            @if($tableColumns !== [])
              <thead><tr>@foreach($tableColumns as $column)<th>{!! $blockRenderer->inline((string) $column) !!}</th>@endforeach</tr></thead>
            @endif
            <tbody>
              @foreach($tableRows as $row)
                <tr>@foreach(array_values((array) $row) as $cell)<td>{!! $blockRenderer->inline((string) $cell) !!}</td>@endforeach</tr>
              @endforeach
            </tbody>
          </table>
          @if(trim((string) ($block['note'] ?? '')) !== '')
            <div class="block-table-note">{{ $block['note'] }}</div>
          @endif
        </div>
      @endif
      @break
    @case('quiz')
    @case('preserved')
    @case('html')
    @case('text')
      {{-- Interactive quizzes and original formatting keep their own markup. --}}
      <div class="lesson-body block-original">{!! $blockRenderer->renderBlock($block) !!}</div>
      @break
  @endswitch
@endforeach
