<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Notifications — DataSensei</title>
  @include('partials.brand-head')
  <style>
    :root{--bg:#0d1320;--surface:#111c2d;--surface2:#1a2638;--border:#243550;--text:#f8fafc;--muted:#8ca0bb;--dim:#657994;--accent:#3b82f6;--radius:10px;--radius-sm:7px}
    *{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--text);font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif}.layout{display:flex;min-height:100vh}.main{min-width:0;flex:1}.topbar{min-height:64px;padding:0 30px;display:flex;align-items:center;border-bottom:1px solid var(--border);background:rgba(17,28,45,.92)}.topbar h1{font-size:1rem;margin:0;font-weight:700}.content{max-width:980px;margin:0 auto;padding:32px 28px 70px}.page-head{display:flex;justify-content:space-between;align-items:flex-start;gap:20px;margin-bottom:20px}.page-head h2{font-size:1.375rem;line-height:1.25;margin:0;font-weight:700;letter-spacing:-.02em}.page-head p{margin:7px 0 0;color:var(--muted);font-size:.88rem}.head-actions{display:flex;gap:8px;flex-wrap:wrap}.btn{border:1px solid var(--border);background:var(--surface2);color:var(--text);border-radius:8px;padding:9px 13px;font:inherit;font-size:.78rem;font-weight:700;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;justify-content:center}.btn:hover{border-color:#3c5579}.btn.subtle{background:transparent;color:var(--muted)}.tabs{display:flex;gap:6px;margin-bottom:14px}.tab{padding:8px 14px;border-radius:999px;text-decoration:none;color:var(--muted);font-size:.8rem;font-weight:700}.tab.active{background:rgba(59,130,246,.15);color:#93c5fd}.panel{background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden}.item{display:grid;grid-template-columns:46px minmax(0,1fr) auto;gap:14px;padding:17px 18px;border-bottom:1px solid var(--border);color:inherit;text-decoration:none;position:relative}.item:last-child{border-bottom:0}.item:hover{background:#162238}.item.unread{background:rgba(59,130,246,.065)}.item.unread:before{content:"";position:absolute;left:0;top:0;bottom:0;width:3px;background:var(--accent)}.icon{width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:#1b2a41;border:1px solid #2e4566;color:#93c5fd}.icon svg{width:19px;height:19px}.item-title{font-size:.9rem;font-weight:750;margin:0 0 4px}.message{color:#b3c1d4;font-size:.82rem;line-height:1.55;margin:0}.meta{color:#7085a1;font-size:.72rem;margin-top:7px}.item-actions{display:flex;align-items:center;gap:6px}.item-actions form{margin:0}.icon-btn{width:34px;height:34px;border:1px solid transparent;border-radius:7px;background:transparent;color:var(--muted);display:flex;align-items:center;justify-content:center;cursor:pointer}.icon-btn:hover{background:var(--surface2);border-color:var(--border);color:var(--text)}.icon-btn.danger:hover{color:#fca5a5;border-color:rgba(239,68,68,.3);background:rgba(239,68,68,.08)}.empty{padding:70px 24px;text-align:center;color:var(--muted)}.empty strong{display:block;color:var(--text);font-size:.95rem;margin-bottom:7px}.pagination{padding:18px 0;display:flex;align-items:center;justify-content:center;gap:10px}.page-link{min-width:92px;text-align:center;border:1px solid var(--border);background:var(--surface);color:var(--text);border-radius:8px;padding:8px 12px;text-decoration:none;font-size:.78rem;font-weight:700}.page-link:hover{border-color:#3c5579}.page-link.disabled{opacity:.45;pointer-events:none}.page-status{color:var(--muted);font-size:.76rem}.flash{padding:11px 14px;border:1px solid rgba(16,185,129,.32);background:rgba(16,185,129,.1);color:#a7f3d0;border-radius:8px;margin-bottom:14px;font-size:.82rem}@media(max-width:700px){.content{padding:24px 16px}.page-head{display:block}.head-actions{margin-top:14px}.item{grid-template-columns:40px minmax(0,1fr);padding:15px}.icon{width:40px;height:40px}.item-actions{grid-column:2;justify-content:flex-start}.topbar{padding:0 18px}}
  </style>
</head>
<body>
<div class="layout">
  @include('partials.sidebar')
  <div class="main">
    <header class="topbar"><h1></h1></header>
    <main class="content">
      @if(session('success'))<div class="flash" role="status">{{ session('success') }}</div>@endif

      <div class="page-head">
        <div>
          <h2>Notifications</h2>
          <p>{{ $unreadCount ? $unreadCount . ' unread notification' . ($unreadCount === 1 ? '' : 's') : 'You are all caught up.' }}</p>
        </div>
        <div class="head-actions">
          @if($unreadCount > 0)
            <form method="POST" action="{{ route('student.notifications.read-all') }}">@csrf<button class="btn" type="submit">Mark all as read</button></form>
          @endif
          <form method="POST" action="{{ route('student.notifications.clear-read') }}">@csrf @method('DELETE')<button class="btn subtle" type="submit">Clear read</button></form>
        </div>
      </div>

      <nav class="tabs" aria-label="Notification filters">
        <a class="tab {{ $filter === 'all' ? 'active' : '' }}" href="{{ route('student.notifications.index') }}">All</a>
        <a class="tab {{ $filter === 'unread' ? 'active' : '' }}" href="{{ route('student.notifications.index', ['filter' => 'unread']) }}">Unread</a>
      </nav>

      <section class="panel">
        @forelse($notifications as $notification)
          @php
            $category = $notification->category;
            $iconPath = match($category) {
              'assignment' => '<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>',
              'assessment' => '<path d="M4 4h16v16H4z"/><path d="M8 9h8M8 13h8M8 17h5"/>',
              'achievement' => '<path d="M8 21h8M12 17v4"/><path d="M7 4h10v4a5 5 0 01-10 0V4z"/>',
              'challenge' => '<path d="M13 10V3L4 14h7v7l9-11h-7z"/>',
              'class' => '<path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>',
              'module' => '<path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/>',
              default => '<circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/>',
            };
          @endphp
          <article class="item {{ $notification->is_read ? '' : 'unread' }}">
            <a href="{{ route('student.notifications.open', $notification) }}" class="icon" aria-label="Open notification">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">{!! $iconPath !!}</svg>
            </a>
            <a href="{{ route('student.notifications.open', $notification) }}" style="color:inherit;text-decoration:none;min-width:0">
              <h3 class="item-title">{{ $notification->display_title }}</h3>
              <p class="message">{{ $notification->notification_text }}</p>
              <div class="meta">{{ $notification->created_at?->diffForHumans() }}</div>
            </a>
            <div class="item-actions">
              @if($notification->is_read)
                <form method="POST" action="{{ route('student.notifications.unread', $notification) }}">@csrf @method('PATCH')<button class="icon-btn" type="submit" title="Mark as unread" aria-label="Mark as unread"><svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 8l9 6 9-6"/><path d="M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></button></form>
              @else
                <form method="POST" action="{{ route('student.notifications.read', $notification) }}">@csrf<button class="icon-btn" type="submit" title="Mark as read" aria-label="Mark as read"><svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg></button></form>
              @endif
              <form method="POST" action="{{ route('student.notifications.destroy', $notification) }}">@csrf @method('DELETE')<button class="icon-btn danger" type="submit" title="Delete notification" aria-label="Delete notification"><svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v5M14 11v5"/></svg></button></form>
            </div>
          </article>
        @empty
          <div class="empty"><strong>No {{ $filter === 'unread' ? 'unread ' : '' }}notifications</strong>New class activity, coursework, results, and achievements will appear here.</div>
        @endforelse
      </section>

      @if($notifications->hasPages())
        <nav class="pagination" aria-label="Notification pages">
          @if($notifications->onFirstPage())
            <span class="page-link disabled">Previous</span>
          @else
            <a class="page-link" href="{{ $notifications->previousPageUrl() }}">Previous</a>
          @endif
          <span class="page-status">Page {{ $notifications->currentPage() }} of {{ $notifications->lastPage() }}</span>
          @if($notifications->hasMorePages())
            <a class="page-link" href="{{ $notifications->nextPageUrl() }}">Next</a>
          @else
            <span class="page-link disabled">Next</span>
          @endif
        </nav>
      @endif
    </main>
  </div>
</div>
</body>
</html>
