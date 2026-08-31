@switch($slug)
  @case('newbie')
    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22V12m0 0C12 7 7 5 3 6c0 4 2 8 9 6zm0 0c0-5 5-7 9-6-1 4-4 7-9 6z"/></svg>
    @break
  @case('university-student')
    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M5 12v5c4 3 10 3 14 0v-5"/></svg>
    @break
  @case('intermediate')
    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v7c0 2 3.6 3 8 3s8-1 8-3V5M4 12v7c0 2 3.6 3 8 3s8-1 8-3v-7"/></svg>
    @break
  @case('advanced')
    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="M8.6 13.5l6.8 4M15.4 6.5l-6.8 4"/></svg>
    @break
  @case('professional')
    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M3 12h18"/></svg>
    @break
  @default
    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7l8-4z"/></svg>
@endswitch
