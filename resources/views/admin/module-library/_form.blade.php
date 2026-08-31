<form method="POST" action="{{ $formAction }}">
  @csrf
  @if($formMethod !== 'POST')
    @method($formMethod)
  @endif

  @if($hasReferences ?? false)
    <div class="notice error">
      This version is already assigned to a class. Metadata may still be corrected, but changing the learning sections or review questions requires creating a new version.
    </div>
  @endif

  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Module Identity and Version</h2>
        <p class="panel-subtitle">Each row is one version of a curriculum module. Codes must remain unique.</p>
      </div>
    </div>
    <div class="panel-body">
      <div class="form-grid three">
        <div class="field">
          <label for="module-no">Module Number</label>
          <input id="module-no" class="input" type="number" name="module_no" min="1" value="{{ old('module_no', $module->module_no) }}" required>
        </div>
        <div class="field">
          <label for="module-code">Unique Module Code</label>
          <input id="module-code" class="input" name="module_code" value="{{ old('module_code', $module->module_code) }}" placeholder="PYTHON-BASIC-V1" required>
        </div>
        <div class="field">
          <label for="year-level">Year Level</label>
          <input id="year-level" class="input" name="year_level" value="{{ old('year_level', $module->year_level) }}" placeholder="Year 1" required>
        </div>
        <div class="field">
          <label for="version-no">Version Number</label>
          <input id="version-no" class="input" type="number" name="version_no" min="1" value="{{ old('version_no', $module->version_no) }}" required>
        </div>
        <div class="field">
          <label for="version-name">Version Name</label>
          <input id="version-name" class="input" name="version_name" value="{{ old('version_name', $module->version_name) }}" placeholder="Python Fundamentals" required>
        </div>
        <div class="field">
          <label for="version-code">Version Code</label>
          <input id="version-code" class="input" name="version_code" value="{{ old('version_code', $module->version_code) }}" placeholder="V1" required>
        </div>
      </div>
    </div>
  </section>

  <section class="panel" style="margin-top:24px">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Display Information</h2>
        <p class="panel-subtitle">This metadata is shown in the student and instructor module libraries.</p>
      </div>
    </div>
    <div class="panel-body">
      <div class="form-grid three">
        <div class="field" style="grid-column:span 2">
          <label for="module-title">Title</label>
          <input id="module-title" class="input" name="title" value="{{ old('title', $module->title) }}" required>
        </div>
        <div class="field">
          <label for="estimated-minutes">Estimated Minutes</label>
          <input id="estimated-minutes" class="input" type="number" name="estimated_minutes" min="1" value="{{ old('estimated_minutes', $module->estimated_minutes ?? 45) }}" required>
        </div>
        <div class="field">
          <label for="sort-order">Sort Order</label>
          <input id="sort-order" class="input" type="number" name="sort_order" min="0" value="{{ old('sort_order', $module->sort_order ?? 0) }}" required>
        </div>
        <div class="field">
          <label for="module-status">Publication Status</label>
          <select id="module-status" class="select" name="is_active">
            <option value="0" @selected(!old('is_active', $module->is_active))>Inactive / Draft</option>
            <option value="1" @selected((bool) old('is_active', $module->is_active))>Published</option>
          </select>
        </div>
      </div>
      <div class="field" style="margin-top:14px">
        <label for="module-description">Description</label>
        <textarea id="module-description" class="textarea" name="description">{{ old('description', $module->description) }}</textarea>
      </div>
    </div>
  </section>

  <section class="panel" style="margin-top:24px">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Learning Content Sections</h2>
        <p class="panel-subtitle">The seeded modules use flexible nested structures. Enter a valid JSON array to preserve every supported section field without data loss.</p>
      </div>
    </div>
    <div class="panel-body">
      <div class="field">
        <label for="content-sections-json">Content Sections JSON</label>
        <textarea id="content-sections-json" class="textarea code-editor" name="content_sections_json" spellcheck="false">{{ old('content_sections_json', $contentSectionsJson) }}</textarea>
        <p class="field-help">Common keys include heading, subheading, body, code, walkthrough, netacad_style_activity, common_mistakes, key_points, and check_your_understanding.</p>
      </div>
    </div>
  </section>

  <section class="panel" style="margin-top:24px">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Embedded Review Questions</h2>
        <p class="panel-subtitle">These are instructional review questions stored inside the module, not scored challenge questions.</p>
      </div>
    </div>
    <div class="panel-body">
      <div class="field">
        <label for="mcq-questions-json">Review Questions JSON</label>
        <textarea id="mcq-questions-json" class="textarea code-editor" name="mcq_questions_json" spellcheck="false">{{ old('mcq_questions_json', $mcqQuestionsJson) }}</textarea>
        <p class="field-help">Expected keys normally include question, choices, answer, and explanation.</p>
      </div>
    </div>
  </section>

  <div class="action-row" style="margin-top:24px">
    <button class="btn" type="submit">{{ $submitLabel }}</button>
    <a class="btn secondary" href="{{ $cancelUrl }}">Cancel</a>
  </div>
</form>

@push('head')
<style>
  .code-editor { min-height:360px; font-family:Consolas, Monaco, monospace; font-size:.78rem; line-height:1.55; tab-size:2; }
  .field-help { margin-top:7px; color:var(--dim); font-size:.72rem; line-height:1.5; }
</style>
@endpush
