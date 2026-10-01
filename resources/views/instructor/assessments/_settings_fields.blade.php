{{-- Assessment settings fields, used by the create page and the builder.
     Needs $classes; optional $assessment (existing) and $tos. --}}
@php
  $current = $assessment ?? null;
  $dt = fn ($value) => $value ? $value->format('Y-m-d\TH:i') : '';
@endphp
<div class="form-grid">
  <div class="field span-all">
    <label for="title">Title</label>
    <input class="input" id="title" name="title" maxlength="191" value="{{ old('title', $current?->title ?? (isset($tos) && $tos ? $tos->title.' Assessment' : '')) }}" placeholder="Example: Quiz 1, Descriptive Statistics" required>
    @error('title')<span class="error">{{ $message }}</span>@enderror
  </div>
  <div class="field">
    <label for="class_id">Class</label>
    <select class="select" id="class_id" name="class_id" required>
      <option value="">Choose a class</option>
      @foreach($classes as $class)
        <option value="{{ $class->id }}" @selected((string) old('class_id', $current?->class_id ?? ($tos->class_id ?? '')) === (string) $class->id)>{{ $class->name }}{{ $class->section ? ', '.$class->section : '' }}{{ $class->is_archived ? ' (archived)' : '' }}</option>
      @endforeach
    </select>
    @error('class_id')<span class="error">{{ $message }}</span>@enderror
  </div>
  <div class="field">
    <label for="max_attempts">Attempts allowed</label>
    <input class="input" id="max_attempts" type="number" min="1" max="10" name="max_attempts" value="{{ old('max_attempts', $current?->max_attempts ?? 1) }}" required>
    @error('max_attempts')<span class="error">{{ $message }}</span>@enderror
  </div>
  <div class="field">
    <label for="time_limit_minutes">Duration in minutes</label>
    <input class="input" id="time_limit_minutes" type="number" min="1" max="1440" name="time_limit_minutes" value="{{ old('time_limit_minutes', $current ? $current->time_limit_minutes : 60) }}" placeholder="No time limit">
    <span class="hint">Leave empty for no time limit.</span>
    @error('time_limit_minutes')<span class="error">{{ $message }}</span>@enderror
  </div>
  <div class="field">
    <label for="available_at">Available from</label>
    <input class="input" id="available_at" type="datetime-local" name="available_at" value="{{ old('available_at', $dt($current?->available_at)) }}">
    <span class="hint">Leave empty to open it as soon as it is published.</span>
    @error('available_at')<span class="error">{{ $message }}</span>@enderror
  </div>
  <div class="field">
    <label for="due_at">Due date</label>
    <input class="input" id="due_at" type="datetime-local" name="due_at" value="{{ old('due_at', $dt($current?->due_at)) }}">
    <span class="hint">Optional.</span>
    @error('due_at')<span class="error">{{ $message }}</span>@enderror
  </div>
  <div class="field span-all">
    <label for="instructions">Instructions for students</label>
    <textarea class="textarea" id="instructions" name="instructions" maxlength="10000" placeholder="Optional. For example: Answer every question. Calculators are allowed.">{{ old('instructions', $current?->instructions) }}</textarea>
    @error('instructions')<span class="error">{{ $message }}</span>@enderror
  </div>
</div>
