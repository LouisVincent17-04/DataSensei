@if(!empty($exceptionalNotifications))
  @foreach($exceptionalNotifications as $notification)
    <div class="page-challenges-alert page-challenges-alert-success" style="margin-bottom:10px;">
      <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
      </svg>
      {{ $notification }}
    </div>
  @endforeach
@endif
