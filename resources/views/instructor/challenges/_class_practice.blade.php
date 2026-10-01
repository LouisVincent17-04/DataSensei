{{-- Which of the instructor's classes can practice this challenge (DataSensei
     Updates 9). Needs $challenge, $practiceClasses and $sharedClassIds. --}}
@once
<style>
  .cp-panel{margin:16px 0;padding:20px;border:1px solid var(--border);border-radius:var(--radius);background:var(--surface);scroll-margin-top:84px}
  .cp-title{margin:0;color:var(--text);font-size:1rem;font-weight:600;line-height:1.35}
  .cp-note{margin:4px 0 0;color:var(--muted);font-size:.875rem;line-height:1.55;max-width:80ch}
  .cp-list{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:8px 16px;margin:16px 0;padding:0;list-style:none}
  .cp-list label{display:flex;align-items:flex-start;gap:10px;color:var(--ds-text-secondary);font-size:.875rem;line-height:1.45;cursor:pointer}
  .cp-list input{margin-top:3px;accent-color:var(--accent)}
  .cp-list strong{color:var(--text);font-weight:500}
  .cp-actions{display:flex;align-items:center;gap:12px;flex-wrap:wrap}
  .cp-btn{display:inline-flex;align-items:center;min-height:36px;padding:0 16px;border:1px solid var(--accent);border-radius:var(--radius-sm);background:var(--accent);color:#fff;font:500 .875rem/1 var(--ds-font-sans);cursor:pointer}
  .cp-btn:hover{background:var(--accent-hover);border-color:var(--accent-hover)}
</style>
@endonce
<section class="cp-panel" id="classes" aria-labelledby="classes-title">
  <h2 class="cp-title" id="classes-title">Classes that can practice this</h2>
  <p class="cp-note">Students in the ticked classes see this challenge under "From your classes" on their challenge page and can take it for practice. Nothing is due and nothing is graded here; for graded class work with a due date, create an assessment.</p>

  @if($practiceClasses->isEmpty())
    <p class="cp-note">You have no active classes yet.</p>
  @else
    <form method="POST" action="{{ route('instructor.challenges.classes.update', $challenge) }}">
      @csrf
      @method('PUT')
      <ul class="cp-list">
        @foreach($practiceClasses as $class)
          <li>
            <label>
              <input type="checkbox" name="class_ids[]" value="{{ $class->id }}" @checked(in_array((int) $class->id, $sharedClassIds, true))>
              <span><strong>{{ $class->name }}</strong>@if($class->section)<br>{{ $class->section }}@endif</span>
            </label>
          </li>
        @endforeach
      </ul>
      <div class="cp-actions">
        <button class="cp-btn" type="submit">Save classes</button>
        @if(! $challenge->is_active)
          <span class="cp-note">This challenge is unavailable, so students will see it only after you make it available.</span>
        @endif
      </div>
    </form>
  @endif
</section>
