<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Notifications — DataSensei</title>
<style>
    /* Student notifications. Colours, type and radius come from partials.design-system. */
    *{box-sizing:border-box}
    body{margin:0;background:var(--bg);color:var(--text);font-family:var(--ds-font-sans)}
    .layout{display:flex;min-height:100vh}
    .main{min-width:0;flex:1}
    .content{max-width:980px;margin:0 auto;padding:28px 32px 48px}

    /* page header */
    .page-head{display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px;margin-bottom:20px}
    .page-head > div:first-child{min-width:0;flex:1 1 280px}
    .page-head p{margin:4px 0 0;max-width:72ch;color:var(--muted);font-size:.875rem;line-height:1.55}
    .head-actions{display:flex;gap:8px;flex-wrap:wrap}
    .head-actions form{margin:0}

    .btn{min-height:32px;display:inline-flex;align-items:center;justify-content:center;padding:0 12px;
      border:1px solid var(--ds-border-strong);border-radius:var(--radius-sm);background:var(--surface2);color:var(--text);
      font-family:var(--ds-font-sans);font-size:.8125rem;font-weight:500;line-height:1.2;white-space:nowrap;cursor:pointer;text-decoration:none;
      transition:background .12s ease,border-color .12s ease,color .12s ease}
    .btn:hover{background:var(--ds-surface-hover)}
    .btn.subtle{background:transparent;border-color:transparent;color:var(--muted)}
    .btn.subtle:hover{background:var(--surface2);color:var(--text)}

    /* filter tabs: underline style */
    .tabs{display:flex;gap:20px;margin-bottom:16px;border-bottom:1px solid var(--border)}
    .tab{margin-bottom:-1px;padding:8px 2px 10px;border-bottom:2px solid transparent;color:var(--muted);
      font-size:.875rem;font-weight:500;text-decoration:none;transition:color .12s ease,border-color .12s ease}
    .tab:hover{color:var(--text)}
    .tab.active{border-bottom-color:var(--accent);color:var(--text)}

    /* notification list */
    .panel{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden}
    .item{position:relative;display:grid;grid-template-columns:36px minmax(0,1fr) auto;gap:14px;align-items:start;
      padding:14px 16px 14px 20px;border-bottom:1px solid var(--border);color:inherit;text-decoration:none;transition:background .12s ease}
    .item:last-child{border-bottom:0}
    .item:hover{background:var(--surface2)}
    .item.unread{background:var(--ds-accent-soft)}
    .item.unread:before{content:"";position:absolute;left:0;top:0;bottom:0;width:3px;background:var(--accent)}
    .item > .icon{width:36px;height:36px;display:flex;align-items:center;justify-content:center;
      border:1px solid var(--ds-border-strong);border-radius:var(--radius-sm);background:var(--surface2);color:var(--ds-accent-text)}
    .item > .icon svg{width:18px;height:18px}
    .item-title{margin:0 0 2px;font-size:.875rem;font-weight:600;line-height:1.4;overflow-wrap:anywhere}
    .item:not(.unread) .item-title{color:var(--ds-text-secondary)}
    .message{margin:0;color:var(--ds-text-secondary);font-size:.8125rem;line-height:1.55;overflow-wrap:anywhere}
    .meta{margin-top:6px;color:var(--muted);font-size:.75rem}
    .item-actions{display:flex;align-items:center;gap:4px}
    .item-actions form{margin:0}
    .icon-btn{width:32px;height:32px;display:flex;align-items:center;justify-content:center;padding:0;
      border:1px solid transparent;border-radius:var(--radius-sm);background:transparent;color:var(--muted);cursor:pointer;
      transition:background .12s ease,border-color .12s ease,color .12s ease}
    .icon-btn:hover{background:var(--surface2);border-color:var(--ds-border-strong);color:var(--text)}
    .icon-btn.danger:hover{background:var(--ds-danger-soft);border-color:var(--ds-danger-border);color:var(--ds-danger-text)}

    .empty{padding:32px 20px;text-align:center;color:var(--muted);font-size:.875rem;line-height:1.55}
    .empty strong{display:block;margin-bottom:4px;color:var(--text);font-size:.9375rem;font-weight:600}

    .pagination{display:flex;align-items:center;justify-content:center;flex-wrap:wrap;gap:8px;padding:16px 0}
    .page-link{min-width:88px;min-height:32px;display:inline-flex;align-items:center;justify-content:center;padding:0 12px;
      border:1px solid var(--ds-border-strong);border-radius:var(--radius-sm);background:var(--surface2);color:var(--text);
      font-size:.8125rem;font-weight:500;text-decoration:none}
    .page-link:hover{background:var(--ds-surface-hover)}
    .page-link.disabled{opacity:.45;pointer-events:none}
    .page-status{color:var(--muted);font-size:.8125rem;font-variant-numeric:tabular-nums}

    .flash{margin-bottom:16px;padding:12px 16px;border:1px solid var(--ds-success-border);border-radius:var(--radius-sm);
      background:var(--ds-success-soft);color:#d1fae5;font-size:.875rem}

    @media(max-width:900px){.content{padding:24px 20px 40px}}
    @media(max-width:640px){
      .content{padding:20px 16px 32px}
      .page-head{align-items:flex-start;flex-direction:column}
      .page-head > div:first-child{flex:0 0 auto;width:100%}
      .head-actions{width:100%}
      .item{grid-template-columns:32px minmax(0,1fr);gap:12px;padding:14px 16px}
      .item > .icon{width:32px;height:32px}
      .item > .icon svg{width:16px;height:16px}
      .item-actions{grid-column:2;justify-content:flex-start;margin-left:-6px}
    }
    @media(prefers-reduced-motion:reduce){.btn,.tab,.item,.icon-btn{transition:none}}
  </style>
    @include('partials.page-head', ['pageTitle' => 'Notifications', 'pageDescription' => 'Class announcements, feedback, and reminders in one place.'])
</head>
<body>
<div class="layout">
  @include('partials.sidebar')
  <div class="main">
    <main class="content">
      @if(session('success'))<div class="flash" role="status">{{ session('success') }}</div>@endif

      <div class="page-head">
        <div>
          <h1 class="ds-page-title">Notifications</h1>
          <p>{{ $unreadCount ? $unreadCount . ' unread notification' . ($unreadCount === 1 ? '' : 's') : 'You are all caught up.' }}</p>
        </div>
        <div class="head-actions">
          @if($unreadCount > 0)
            <form method="POST" action="{{ route('student.notifications.read-all') }}">@csrf @method('PATCH')<button class="btn" type="submit">Mark all as read</button></form>
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
                <form method="POST" action="{{ route('student.notifications.read', $notification) }}">@csrf @method('PATCH')<button class="icon-btn" type="submit" title="Mark as read" aria-label="Mark as read"><svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg></button></form>
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
