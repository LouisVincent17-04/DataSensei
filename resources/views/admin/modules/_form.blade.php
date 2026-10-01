{{--
    Basic Information of a DataSensei Module, shown in the first tab of the
    module editor (admin.partials.module-editor).
--}}
<section class="panel">
  <div class="panel-head">
    <div class="panel-heading">
      <h2 class="panel-title">Module details</h2>
      <p class="panel-subtitle">What students see on the Modules page, where DataSensei Modules are grouped by year level and unlocked in order.</p>
    </div>
  </div>
  <div class="panel-body">
    <div class="form-grid three">
      <div class="field" style="grid-column:span 2">
        <label for="module-title">Title</label>
        @if($module->exists && $module->isCore())
          {{-- A Core Module keeps its title (DataSensei Updates 12); the server refuses any change. --}}
          <input id="module-title" class="input" name="title" maxlength="189" value="{{ $module->title }}" required readonly aria-readonly="true" aria-describedby="module-title-help">
          <div class="field-help" id="module-title-help">Core Module: the title and identity cannot be changed. Its content can still be edited.</div>
        @else
          <input id="module-title" class="input" name="title" maxlength="189" value="{{ old('title', $module->title) }}" required>
          @if($formMethod === 'POST')
            <div class="field-help">New modules are Custom modules.</div>
          @endif
        @endif
      </div>
      <div class="field">
        <label for="module-year">Year level</label>
        <select id="module-year" class="select" name="year_level" required>
          @foreach($yearLevels as $level)
            <option value="{{ $level }}" @selected(old('year_level', $module->year_level) === $level)>{{ $level }}</option>
          @endforeach
        </select>
      </div>
      <div class="field" style="grid-column:span 3">
        <label for="module-description">Description</label>
        <textarea id="module-description" class="textarea" name="description" maxlength="5000">{{ old('description', $module->description) }}</textarea>
      </div>
      <div class="field">
        <label for="module-xp">XP reward</label>
        <input id="module-xp" class="input" type="number" name="xp_reward" min="0" max="10000" value="{{ old('xp_reward', $module->xp_reward ?? 100) }}" required>
      </div>
      <div class="field">
        <label for="module-boss">Module type</label>
        <select id="module-boss" class="select" name="is_boss">
          <option value="0" @selected(!old('is_boss', $module->is_boss))>Standard module</option>
          <option value="1" @selected((bool) old('is_boss', $module->is_boss))>Boss module</option>
        </select>
      </div>
      <div class="field">
        <label for="module-coding">Coding exercises</label>
        <label class="check-field" for="module-coding">
          <input type="hidden" name="has_coding_exercises" value="0">
          <input id="module-coding" type="checkbox" name="has_coding_exercises" value="1" @checked((bool) old('has_coding_exercises', $module->has_coding_exercises))>
          <span>This module has coding exercises</span>
        </label>
        @if($formMethod === 'POST')
          <div class="field-help">When checked, a locked coding challenge is also created on every level alongside the MCQ challenge.</div>
        @else
          <div class="field-help">Changing this later does not create or remove challenges.</div>
        @endif
      </div>
    </div>
  </div>
</section>

@push('head')
<style>
  .field .check-field { display:flex; align-items:center; gap:10px; min-height:var(--ds-control-h); margin:0; padding:0 12px; border:1px solid var(--ds-input-border); border-radius:var(--radius-sm); background:var(--surface3); color:var(--text); font-size:.875rem; font-weight:400; cursor:pointer; }
  .check-field input { accent-color:var(--accent); width:16px; height:16px; }
  #module-title[readonly] { background:var(--surface2); color:var(--muted); cursor:not-allowed; }
</style>
@endpush
