{{--
    Basic Information of an Instructor Module Library version, shown in the
    first tab of the module editor (admin.partials.module-editor).
--}}
<section class="panel">
  <div class="panel-head">
    <div class="panel-heading">
      <h2 class="panel-title">Module Identity and Version</h2>
      <p class="panel-subtitle">Each row of the library is one version of a module. Codes must stay unique.@if($hasReferences ?? false) This version is assigned to a class, so its number and codes cannot change.@endif</p>
    </div>
  </div>
  <div class="panel-body">
    <div class="form-grid three">
      <div class="field">
        <label for="module-no">Module Number</label>
        <input id="module-no" class="input" type="number" name="module_no" min="1" value="{{ old('module_no', $module->module_no) }}" required>
      </div>
      {{-- The internal module and version codes are generated on the server (DataSensei Updates 9). --}}
      <div class="field">
        <label for="version-no">Version Number</label>
        <input id="version-no" class="input" type="number" name="version_no" min="1" value="{{ old('version_no', $module->version_no) }}" required>
      </div>
      <div class="field">
        <label for="version-name">Version Name</label>
        <input id="version-name" class="input" name="version_name" value="{{ old('version_name', $module->version_name) }}" placeholder="Python Fundamentals" required>
      </div>
    </div>
  </div>
</section>

<section class="panel">
  <div class="panel-head">
    <div class="panel-heading">
      <h2 class="panel-title">Display Information</h2>
      <p class="panel-subtitle">Shown to instructors in their module library and to students of the classes it is assigned to.</p>
    </div>
  </div>
  <div class="panel-body">
    <div class="form-grid three">
      <div class="field" style="grid-column:span 2">
        <label for="module-title">Title</label>
        <input id="module-title" class="input" name="title" maxlength="189" value="{{ old('title', $module->title) }}" required>
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
        <label for="year-level">Curriculum Year (optional)</label>
        <input id="year-level" class="input" name="year_level" maxlength="50" value="{{ old('year_level', $module->year_level) }}" placeholder="Year 1">
        <p class="field-help">For your reference only. Class modules are not grouped by year for students.</p>
      </div>
    </div>
    <div class="field" style="margin-top:16px">
      <label for="module-description">Description</label>
      <textarea id="module-description" class="textarea" name="description">{{ old('description', $module->description) }}</textarea>
    </div>
  </div>
</section>
