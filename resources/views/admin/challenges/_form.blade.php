<div class="mcq-editor-layout">
<form method="POST" action="{{ $formAction }}" data-mcq-form data-image-upload-url="{{ route('admin.challenges.images.store') }}">
  @csrf
  @if($formMethod !== 'POST')
    @method($formMethod)
  @endif

  @if($hasHistory ?? false)
    <div class="notice error">
      This challenge already has attempt history. Metadata may be corrected, but changing questions or answer choices requires duplicating it as a new version.
    </div>
  @endif

  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Challenge Identity and Version</h2>
        <p class="panel-subtitle">The content code groups versions of the same challenge. Coding challenge records are not available in this workspace.</p>
      </div>
    </div>
    <div class="panel-body">
      <div class="form-grid three">
        <div class="field">
          <label for="challenge-category">Difficulty Category</label>
          <select id="challenge-category" class="select" name="challenge_category_id" required>
            <option value="">Select category</option>
            @foreach($categories as $category)
              <option value="{{ $category->id }}" @selected((int) old('challenge_category_id', $challenge->challenge_category_id) === (int) $category->id)>{{ $category->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="field">
          <label for="content-code">Content Code</label>
          <input id="content-code" class="input" name="content_code" value="{{ old('content_code', $challenge->content_code) }}" placeholder="Leave blank to generate one" maxlength="64">
        </div>
        <div class="field">
          <label for="version-no">Version Number</label>
          <input id="version-no" class="input" type="number" name="version_no" min="1" value="{{ old('version_no', $challenge->version_no) }}" required>
        </div>
        <div class="field">
          <label for="version-name">Version Name</label>
          <input id="version-name" class="input" name="version_name" value="{{ old('version_name', $challenge->version_name) }}" required>
        </div>
        <div class="field">
          <label for="version-code">Version Code</label>
          <input id="version-code" class="input" name="version_code" value="{{ old('version_code', $challenge->version_code) }}" placeholder="V1" required>
        </div>
        <div class="field">
          <label for="challenge-status">Publication Status</label>
          <select id="challenge-status" class="select" name="is_active">
            <option value="0" @selected(!old('is_active', $challenge->is_active))>Inactive / Draft</option>
            <option value="1" @selected((bool) old('is_active', $challenge->is_active))>Published</option>
          </select>
        </div>
      </div>
    </div>
  </section>

  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Challenge Settings</h2>
        <p class="panel-subtitle">Configure the title, timer, XP, and map order used by the existing challenge workflow.</p>
      </div>
    </div>
    <div class="panel-body">
      <div class="form-grid three">
        <div class="field" style="grid-column:span 2">
          <label for="challenge-title">Title</label>
          <input id="challenge-title" class="input" name="title" value="{{ old('title', $challenge->title) }}" required>
        </div>
        <div class="field">
          <label for="time-limit">Time Limit in Seconds</label>
          <input id="time-limit" class="input" type="number" name="time_limit_seconds" min="60" max="21600" value="{{ old('time_limit_seconds', $challenge->time_limit_seconds ?? 600) }}" required>
        </div>
        <div class="field">
          <label for="base-xp">Base XP</label>
          <input id="base-xp" class="input" type="number" name="base_xp" min="0" value="{{ old('base_xp', $challenge->base_xp ?? 100) }}" required>
        </div>
        <div class="field">
          <label for="order-index">Map Order</label>
          <input id="order-index" class="input" type="number" name="order_index" min="0" value="{{ old('order_index', $challenge->order_index ?? 0) }}" required>
        </div>
      </div>
      <div class="field" style="margin-top:16px">
        <label for="challenge-description">Description</label>
        <textarea id="challenge-description" class="textarea" name="description">{{ old('description', $challenge->description) }}</textarea>
      </div>
    </div>
  </section>

  <section class="panel" data-question-editor>
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">MCQ Questions</h2>
        <p class="panel-subtitle">Each question requires at least two choices and exactly one correct answer.</p>
      </div>
      <button class="btn small" type="button" data-add-question="mcq">Add Question</button>
    </div>

    <div class="question-list" data-question-list>
      @foreach($questions as $questionIndex => $question)
        <article class="question-item">
          <div class="question-head">
            <strong>Question <span data-question-number>{{ $loop->iteration }}</span></strong>
            <div class="action-row">
              <button class="btn small secondary" type="button" data-move-up>Move Up</button>
              <button class="btn small secondary" type="button" data-move-down>Move Down</button>
              <button class="btn small danger" type="button" data-remove-question>Remove</button>
            </div>
          </div>
          <div class="field">
            <label>Question Text</label>
            <textarea class="textarea" data-field="question_text" name="questions[{{ $questionIndex }}][question_text]" required>{{ $question['question_text'] ?? '' }}</textarea>
          </div>
          <div class="field question-image" data-question-image>
            <label>Picture (optional)</label>
            <input type="hidden" data-field="image_path" name="questions[{{ $questionIndex }}][image_path]" value="{{ $question['image_path'] ?? '' }}">
            <div class="question-image-row">
              <img class="question-image-thumb" data-image-thumb src="{{ $question['image_path'] ?? '' }}" alt="" @if(empty($question['image_path'])) hidden @endif>
              <div class="question-image-controls">
                <input class="input" type="file" accept="image/png,image/jpeg,image/gif,image/webp" data-image-file>
                <div class="action-row">
                  <button class="btn small danger" type="button" data-remove-image @if(empty($question['image_path'])) hidden @endif>Remove picture</button>
                  <span class="dim" data-image-status></span>
                </div>
              </div>
            </div>
          </div>
          <div class="option-list" data-option-list>
            @foreach(($question['options'] ?? []) as $optionIndex => $option)
              <div class="option-item">
                <label class="correct-choice" title="Mark as correct">
                  <input type="radio" data-field="correct_option" name="questions[{{ $questionIndex }}][correct_option]" value="{{ $optionIndex }}" @checked((int) ($question['correct_option'] ?? 0) === (int) $optionIndex)>
                  <span data-option-number>{{ $loop->iteration }}</span>
                </label>
                <input class="input" data-field="option_text" name="questions[{{ $questionIndex }}][options][{{ $optionIndex }}][option_text]" value="{{ $option['option_text'] ?? '' }}" placeholder="Answer choice" required>
                <button class="btn small secondary" type="button" data-move-up aria-label="Move option up">↑</button>
                <button class="btn small secondary" type="button" data-move-down aria-label="Move option down">↓</button>
                <button class="btn small danger" type="button" data-remove-option>Remove</button>
              </div>
            @endforeach
          </div>
          <button class="btn small secondary" type="button" data-add-option>Add Choice</button>
        </article>
      @endforeach
    </div>

    <template data-question-template>
      <article class="question-item">
        <div class="question-head">
          <strong>Question <span data-question-number></span></strong>
          <div class="action-row">
            <button class="btn small secondary" type="button" data-move-up>Move Up</button>
            <button class="btn small secondary" type="button" data-move-down>Move Down</button>
            <button class="btn small danger" type="button" data-remove-question>Remove</button>
          </div>
        </div>
        <div class="field"><label>Question Text</label><textarea class="textarea" data-field="question_text" required></textarea></div>
        <div class="field question-image" data-question-image>
          <label>Picture (optional)</label>
          <input type="hidden" data-field="image_path" value="">
          <div class="question-image-row">
            <img class="question-image-thumb" data-image-thumb src="" alt="" hidden>
            <div class="question-image-controls">
              <input class="input" type="file" accept="image/png,image/jpeg,image/gif,image/webp" data-image-file>
              <div class="action-row">
                <button class="btn small danger" type="button" data-remove-image hidden>Remove picture</button>
                <span class="dim" data-image-status></span>
              </div>
            </div>
          </div>
        </div>
        <div class="option-list" data-option-list>
          @for($i = 0; $i < 4; $i++)
            <div class="option-item">
              <label class="correct-choice"><input type="radio" data-field="correct_option" @checked($i === 0)><span data-option-number>{{ $i + 1 }}</span></label>
              <input class="input" data-field="option_text" placeholder="Answer choice" required>
              <button class="btn small secondary" type="button" data-move-up>↑</button>
              <button class="btn small secondary" type="button" data-move-down>↓</button>
              <button class="btn small danger" type="button" data-remove-option>Remove</button>
            </div>
          @endfor
        </div>
        <button class="btn small secondary" type="button" data-add-option>Add Choice</button>
      </article>
    </template>

    <template data-option-template>
      <div class="option-item">
        <label class="correct-choice"><input type="radio" data-field="correct_option"><span data-option-number></span></label>
        <input class="input" data-field="option_text" placeholder="Answer choice" required>
        <button class="btn small secondary" type="button" data-move-up>↑</button>
        <button class="btn small secondary" type="button" data-move-down>↓</button>
        <button class="btn small danger" type="button" data-remove-option>Remove</button>
      </div>
    </template>
  </section>

  <div class="action-row">
    <button class="btn" type="submit">{{ $submitLabel }}</button>
    <a class="btn secondary" href="{{ $cancelUrl }}">Cancel</a>
  </div>
</form>

@include('admin.challenges._preview')
</div>

@push('head')
<style>
  .question-list { display:grid; gap:16px; padding:16px; }
  .question-item { padding:16px; border:1px solid var(--border); border-radius:var(--radius); background:var(--surface3); }
  .question-head { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:16px; }
  .question-head strong { color:var(--text); }
  .option-list { display:grid; gap:8px; margin:16px 0; }
  .option-item { display:grid; grid-template-columns:auto minmax(180px,1fr) auto auto auto; align-items:center; gap:8px; }
  .correct-choice { width:34px; height:34px; display:flex; align-items:center; justify-content:center; gap:4px; border:1px solid var(--border); border-radius:var(--radius-sm); color:var(--muted); cursor:pointer; }
  .correct-choice input { accent-color:var(--accent3); }
  .question-image { margin-top:16px; }
  .question-image-row { display:flex; gap:12px; align-items:flex-start; }
  .question-image-thumb { width:120px; max-height:120px; object-fit:contain; flex:0 0 auto; border:1px solid var(--border); border-radius:var(--radius-sm); background:var(--surface2); }
  .question-image-controls { flex:1; min-width:0; display:grid; gap:8px; }
  .question-image-controls .action-row { align-items:center; }

  .mcq-editor-layout { display:grid; grid-template-columns:minmax(0,1fr) minmax(320px,420px); gap:24px; align-items:start; }
  .mcq-editor-layout > form { min-width:0; }
  .mcq-preview { position:sticky; top:16px; }
  .mcq-preview .panel-body { max-height:calc(100vh - 140px); overflow:auto; }
  .mcq-preview-title { margin:0; color:var(--text); font-size:1.05rem; font-weight:600; line-height:1.35; overflow-wrap:anywhere; }
  .mcq-preview-desc { margin:8px 0 0; color:var(--muted); font-size:.875rem; line-height:1.55; white-space:pre-wrap; overflow-wrap:anywhere; }
  .mcq-preview-meta { margin:10px 0 0; color:var(--muted); font-size:.8125rem; }
  .mcq-preview-empty { color:var(--muted); font-size:.875rem; }
  .mcq-preview-question { margin-top:16px; padding:14px; border:1px solid var(--border); border-radius:var(--radius); background:var(--surface3); }
  .mcq-preview-q-head { display:flex; gap:10px; align-items:flex-start; }
  .mcq-preview-q-number { width:26px; height:26px; flex:0 0 26px; display:flex; align-items:center; justify-content:center; border:1px solid var(--border); border-radius:var(--radius-sm); background:var(--surface2); color:var(--muted); font-size:.75rem; font-weight:600; }
  .mcq-preview-q-text { min-width:0; padding-top:3px; color:var(--text); font-size:.9375rem; font-weight:600; line-height:1.5; white-space:pre-wrap; overflow-wrap:anywhere; }
  .mcq-preview-q-image { display:block; max-width:100%; height:auto; max-height:260px; margin:12px 0 0; border:1px solid var(--border); border-radius:var(--radius-sm); background:var(--surface2); }
  .mcq-preview-options { margin:12px 0 0; padding:0; list-style:none; display:grid; gap:6px; }
  .mcq-preview-option { display:flex; gap:10px; align-items:center; padding:8px 12px; border:1px solid var(--border); border-radius:var(--radius-sm); background:var(--surface2); color:var(--text); font-size:.875rem; line-height:1.45; overflow-wrap:anywhere; }
  .mcq-preview-option-radio { width:14px; height:14px; flex:0 0 14px; border:1px solid var(--muted); border-radius:50%; }
  .mcq-preview-option.is-correct { border-color:var(--accent3); }
  .mcq-preview-option.is-correct .mcq-preview-option-radio { border-color:var(--accent3); background:var(--accent3); }
  .mcq-preview-option-note { margin-left:auto; color:#a7f3d0; font-size:.75rem; font-weight:600; white-space:nowrap; }
  @media (max-width:700px) { .option-item { grid-template-columns:auto 1fr; } .option-item .btn { grid-column:auto; } .question-head { align-items:flex-start; flex-direction:column; } .question-image-row { flex-direction:column; } }
  @media (max-width:1100px) { .mcq-editor-layout { grid-template-columns:minmax(0,1fr); } .mcq-preview { position:static; } .mcq-preview .panel-body { max-height:none; } }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/admin-content-manager.js') }}"></script>
<script src="{{ asset('js/admin-mcq-preview.js') }}"></script>
@endpush
