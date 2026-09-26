@php
  $initialBlocks = $blocks;
  if (is_string(old('blocks_json'))) {
      $decoded = json_decode(old('blocks_json'), true);
      if (is_array($decoded)) {
          $initialBlocks = array_values(array_filter($decoded, 'is_array'));
      }
  }
  $editorConfig = [
      'previewUrl' => route('admin.lessons.preview'),
      'uploadUrl' => route('admin.lessons.images.store'),
      'moduleId' => $module->id,
      'legacy' => (bool) $isLegacy,
  ];
@endphp

<form method="POST" action="{{ $formAction }}" id="lesson-editor-form" data-lesson-editor-form>
  @csrf
  @if($formMethod !== 'POST')
    @method($formMethod)
  @endif
  <input type="hidden" name="blocks_json" id="lesson-blocks-json" value="{{ old('blocks_json', '') }}">

  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">{{ $lesson->exists ? 'Lesson' : 'New lesson' }}</h2>
        <p class="panel-subtitle">The title appears in the module's lesson list and in the learning room outline.</p>
      </div>
      <div class="action-row">
        <button class="btn" type="submit">{{ $lesson->exists ? 'Save lesson' : 'Add lesson' }}</button>
        <a class="btn secondary" href="{{ route('admin.modules.lessons.index', $module) }}">Cancel</a>
      </div>
    </div>
    <div class="panel-body">
      <div class="field">
        <label for="lesson-title">Title</label>
        <input id="lesson-title" class="input" name="title" maxlength="189" value="{{ old('title', $lesson->title) }}" required>
      </div>
    </div>
  </section>

  <div class="lesson-editor" id="lesson-editor" data-lesson-editor>
    <section class="panel lesson-editor-pane">
      <div class="panel-head">
        <div class="panel-heading">
          <h2 class="panel-title">Blocks</h2>
          <p class="panel-subtitle">Add blocks in reading order. Code windows carry the same "Try in Compiler" button as existing lessons.</p>
        </div>
        <div class="action-row" data-add-block-row>
          <span class="lesson-editor-add-label">Add block</span>
          <button class="btn small secondary" type="button" data-add-block="code">Code</button>
          <button class="btn small secondary" type="button" data-add-block="text">Text</button>
          <button class="btn small secondary" type="button" data-add-block="table">Table</button>
          <button class="btn small secondary" type="button" data-add-block="image">Photo</button>
          <button class="btn small secondary" type="button" data-add-block="html">HTML</button>
        </div>
      </div>
      <div class="lesson-editor-blocks" data-block-list></div>
      <div class="lesson-editor-empty" data-block-empty hidden>No blocks yet. Use "Add block" above to start writing the lesson.</div>
    </section>

    <section class="panel lesson-editor-pane lesson-editor-preview">
      <div class="panel-head">
        <div class="panel-heading">
          <h2 class="panel-title">Live preview</h2>
          <p class="panel-subtitle">Rendered with the learning room's styles. It refreshes as you type.</p>
        </div>
        <span class="lesson-editor-status" data-preview-status aria-live="polite"></span>
      </div>
      <iframe class="lesson-editor-frame" title="Lesson preview" data-preview-frame sandbox="allow-same-origin allow-scripts"></iframe>
    </section>
  </div>

  <div class="action-row">
    <button class="btn" type="submit">{{ $lesson->exists ? 'Save lesson' : 'Add lesson' }}</button>
    <a class="btn secondary" href="{{ route('admin.modules.lessons.index', $module) }}">Cancel</a>
  </div>
</form>

<script type="application/json" id="lesson-editor-blocks">{!! json_encode($initialBlocks, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE) !!}</script>
<script type="application/json" id="lesson-editor-config">{!! json_encode($editorConfig, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
  .lesson-editor { display:grid; grid-template-columns:minmax(0,1fr) minmax(0,1fr); gap:24px; align-items:start; }
  .lesson-editor-pane { margin-bottom:24px; }
  .lesson-editor-preview { position:sticky; top:76px; }
  .lesson-editor-add-label { color:var(--muted); font-size:.8125rem; }
  .lesson-editor-blocks { display:grid; gap:12px; padding:16px; }
  .lesson-editor-blocks:empty { display:none; }
  .lesson-editor-empty { padding:32px 20px; color:var(--muted); font-size:.875rem; text-align:center; }
  .lesson-editor-empty[hidden] { display:none; }
  .lesson-editor-status { color:var(--muted); font-size:.75rem; }
  .lesson-editor-frame { display:block; width:100%; height:calc(100vh - 180px); min-height:480px; border:0; background:var(--bg); }

  .lesson-block { border:1px solid var(--border); border-radius:var(--radius); background:var(--surface3); }
  .lesson-block-head { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:10px 14px; border-bottom:1px solid var(--border); }
  .lesson-block-head strong { color:var(--text); font-size:.8125rem; font-weight:600; }
  .lesson-block-head .action-row { gap:4px; }
  .lesson-block-head .btn.small { min-height:28px; padding:0 8px; font-size:.75rem; }
  .lesson-block-body { display:grid; gap:12px; padding:14px; }
  .lesson-block-body .form-grid { gap:12px; }
  .lesson-block-body .textarea { min-height:80px; }
  .lesson-block-body .textarea.mono { font-family:var(--ds-font-mono); font-size:.8125rem; line-height:1.55; tab-size:4; white-space:pre; overflow-x:auto; min-height:140px; }
  .lesson-block-body .textarea.html { font-family:var(--ds-font-mono); font-size:.8125rem; line-height:1.55; min-height:260px; }
  .lesson-block-body .textarea.body { min-height:160px; }
  .lesson-block-check { display:flex; align-items:center; gap:8px; color:var(--ds-text-secondary); font-size:.8125rem; cursor:pointer; }
  .lesson-block-check input { accent-color:var(--accent); width:15px; height:15px; }
  .lesson-block-help { color:var(--muted); font-size:.75rem; line-height:1.5; }
  .lesson-block-help code { padding:.05em .3em; border:1px solid var(--border); border-radius:var(--radius-xs); background:var(--surface2); color:var(--text); font-family:var(--ds-font-mono); font-size:.9em; }
  .lesson-text-toolbar { display:flex; flex-wrap:wrap; gap:4px; }
  .lesson-text-toolbar .btn.small { min-height:28px; padding:0 10px; font-size:.75rem; }

  .lesson-table-grid { display:grid; gap:6px; overflow-x:auto; }
  .lesson-table-row { display:grid; grid-auto-flow:column; grid-auto-columns:minmax(120px,1fr); gap:6px; align-items:center; }
  .lesson-table-row .input { min-height:32px; padding:4px 8px; font-size:.8125rem; }
  .lesson-table-row .input.head { font-weight:600; }
  .lesson-table-row .btn.small { min-height:28px; padding:0 8px; font-size:.75rem; }
  .lesson-table-row .btn-cell { display:flex; justify-content:center; }
  .lesson-table-actions { display:flex; flex-wrap:wrap; gap:6px; }

  .lesson-image-preview { display:block; max-width:100%; height:auto; border:1px solid var(--border); border-radius:var(--radius-sm); background:var(--surface2); }
  .lesson-image-preview[hidden] { display:none; }
  .lesson-image-width { display:flex; align-items:center; gap:10px; }
  .lesson-image-width input[type="range"] { flex:1; accent-color:var(--accent); }
  .lesson-image-width output { min-width:44px; color:var(--text); font-size:.8125rem; font-variant-numeric:tabular-nums; }
  .lesson-image-file { color:var(--ds-text-secondary); font-size:.8125rem; }
  .lesson-image-file input[type="file"] { display:block; width:100%; margin-top:4px; color:var(--muted); font-size:.8125rem; }

  @media (max-width:1100px) {
    .lesson-editor { grid-template-columns:1fr; }
    .lesson-editor-preview { position:static; }
    .lesson-editor-frame { height:70vh; }
  }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/admin-lesson-editor.js') }}"></script>
@endpush
