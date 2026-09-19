@php($notificationCenterVariant = $variant ?? 'sidebar')
<div class="ds-notification-center ds-notification-center--{{ $notificationCenterVariant }}" id="ds-notification-center">
  <button
    type="button"
    class="ds-notification-trigger"
    id="ds-notification-trigger"
    aria-label="Open notifications"
    aria-haspopup="dialog"
    aria-expanded="false"
    title="Notifications"
  >
    <svg width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
      <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5"/>
      <path d="M9 17v1a3 3 0 006 0v-1"/>
    </svg>
    <span class="ds-notification-badge" id="ds-notification-badge" hidden>0</span>
  </button>

  <section class="ds-notification-panel" id="ds-notification-panel" role="dialog" aria-label="Notifications" hidden>
    <div class="ds-notification-panel-head">
      <div>
        <h2>Notifications</h2>
        <p id="ds-notification-summary">Your recent activity</p>
      </div>
      <button type="button" class="ds-notification-text-btn" id="ds-notification-read-all">Mark all as read</button>
    </div>

    <div class="ds-notification-tabs" role="tablist" aria-label="Notification filters">
      <button type="button" class="active" data-notification-filter="all" role="tab" aria-selected="true">All</button>
      <button type="button" data-notification-filter="unread" role="tab" aria-selected="false">Unread</button>
    </div>

    <div class="ds-notification-list" id="ds-notification-list" aria-live="polite">
      <div class="ds-notification-loading ds-visually-hidden">Loading notifications…</div>
      <div class="ds-notification-skeleton" aria-hidden="true"><span class="sk-avatar"></span><div><span class="sk-title"></span><span class="sk-line"></span></div></div>
      <div class="ds-notification-skeleton" aria-hidden="true"><span class="sk-avatar"></span><div><span class="sk-title"></span><span class="sk-line"></span></div></div>
      <div class="ds-notification-skeleton" aria-hidden="true"><span class="sk-avatar"></span><div><span class="sk-title"></span><span class="sk-line"></span></div></div>
    </div>

    <a href="{{ route('student.notifications.index') }}" class="ds-notification-see-all">See all notifications</a>
  </section>
</div>

<style>
  /* The bell sits in the sidebar header on desktop and in the mobile bar
     on small screens; the panel opens beside it. */
  .ds-notification-center{position:relative;flex:0 0 auto;font-family:var(--ds-font-sans,Inter,Arial,Helvetica,sans-serif)}
  .ds-notification-center--sidebar{position:fixed;top:20px;left:206px;z-index:1250}
  .ds-notification-trigger{position:relative;width:36px;height:36px;padding:0;display:inline-flex;align-items:center;justify-content:center;border:1px solid var(--ds-border-strong,#2c4168);border-radius:var(--ds-radius-sm,6px);background:var(--ds-surface-2,#1a2638);color:var(--ds-text-muted,#8aa0bd);cursor:pointer;transition:background .12s ease,color .12s ease,border-color .12s ease}
  .ds-notification-trigger svg{width:18px;height:18px}
  .ds-notification-trigger:hover,.ds-notification-trigger[aria-expanded="true"]{color:var(--ds-text,#f8fafc);border-color:var(--ds-accent-border,rgba(59,130,246,.4));background:var(--ds-accent-soft,rgba(59,130,246,.12))}
  .ds-notification-center--topbar .ds-notification-trigger{width:32px;height:32px;background:transparent}
  .ds-notification-center--topbar .ds-notification-panel{top:52px;left:auto;right:12px;width:min(400px,calc(100vw - 24px))}
  .ds-notification-badge{position:absolute;top:-6px;right:-6px;min-width:18px;height:18px;padding:0 4px;border:2px solid var(--ds-surface,#111c2d);border-radius:9px;background:var(--ds-danger,#ef4444);color:#fff;font-size:11px;font-weight:700;line-height:14px;text-align:center}
  .ds-notification-panel{position:fixed;top:68px;left:244px;z-index:1240;width:min(400px,calc(100vw - 268px));max-height:min(640px,calc(100vh - 84px));overflow:hidden;display:flex;flex-direction:column;background:var(--ds-surface,#111c2d);border:1px solid var(--ds-border-strong,#2c4168);border-radius:var(--ds-radius-lg,10px);box-shadow:var(--ds-shadow-lg);color:var(--ds-text,#f8fafc)}
  .ds-notification-panel[hidden]{display:none}
  .ds-notification-panel-head{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;padding:16px 16px 12px}
  .ds-notification-panel-head h2{margin:0;font-size:1rem;font-weight:600;line-height:1.3;letter-spacing:0}
  .ds-notification-panel-head p{margin:2px 0 0;color:var(--ds-text-muted,#8aa0bd);font-size:.75rem}
  .ds-notification-text-btn{padding:4px 0;border:0;background:transparent;color:var(--ds-accent-text,#93c5fd);font:inherit;font-size:.75rem;font-weight:500;white-space:nowrap;cursor:pointer}
  .ds-notification-text-btn:hover{text-decoration:underline}
  .ds-notification-tabs{display:flex;gap:16px;padding:0 16px;border-bottom:1px solid var(--ds-border,#1e2f47)}
  .ds-notification-tabs button{margin-bottom:-1px;padding:8px 0;border:0;border-bottom:2px solid transparent;border-radius:0;background:transparent;color:var(--ds-text-muted,#8aa0bd);font:inherit;font-size:.8125rem;font-weight:500;cursor:pointer}
  .ds-notification-tabs button:hover{color:var(--ds-text,#f8fafc)}
  .ds-notification-tabs button.active{border-bottom-color:var(--ds-accent,#3b82f6);color:var(--ds-text,#f8fafc)}
  .ds-notification-list{flex:1 1 auto;min-height:120px;max-height:470px;overflow-y:auto}
  .ds-notification-item{position:relative;display:grid;grid-template-columns:32px minmax(0,1fr) 8px;gap:12px;align-items:start;padding:12px 16px;border-bottom:1px solid var(--ds-border,#1e2f47);color:inherit;text-decoration:none;transition:background .12s ease}
  .ds-notification-item:hover{background:var(--ds-surface-2,#1a2638)}
  .ds-notification-item.unread{background:rgba(59,130,246,.06)}
  .ds-notification-item.unread:hover{background:rgba(59,130,246,.11)}
  .ds-notification-icon{width:32px;height:32px;flex-shrink:0;display:flex;align-items:center;justify-content:center;border:1px solid var(--ds-border-strong,#2c4168);border-radius:var(--ds-radius-sm,6px);background:var(--ds-surface-2,#1a2638);color:var(--ds-accent-text,#93c5fd)}
  .ds-notification-icon svg{width:16px;height:16px}
  .ds-notification-item-title{display:block;margin:0 0 2px;color:var(--ds-text,#f8fafc);font-size:.8125rem;font-weight:600;line-height:1.35}
  .ds-notification-item-message{margin:0;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;color:var(--ds-text-secondary,#c8d5e8);font-size:.8125rem;line-height:1.45}
  .ds-notification-item-time{display:block;margin-top:4px;color:var(--ds-text-dim,#68809f);font-size:.75rem}
  .ds-notification-unread-dot{width:8px;height:8px;margin-top:6px;border-radius:50%;background:var(--ds-accent,#3b82f6)}
  .ds-notification-item:not(.unread) .ds-notification-unread-dot{visibility:hidden}
  .ds-notification-loading,.ds-notification-empty,.ds-notification-error{min-height:140px;display:flex;align-items:center;justify-content:center;padding:28px;color:var(--ds-text-muted,#8aa0bd);font-size:.8125rem;line-height:1.5;text-align:center}
  /* Placeholder rows keep the panel at its real size while the feed loads. */
  .ds-notification-skeleton{display:grid;grid-template-columns:32px minmax(0,1fr);gap:12px;align-items:center;padding:12px 16px;border-bottom:1px solid var(--ds-border,#1e2f47)}
  .ds-notification-skeleton span{display:block;border-radius:var(--ds-radius-xs,4px);background:linear-gradient(90deg,rgba(148,163,184,.10) 25%,rgba(148,163,184,.20) 37%,rgba(148,163,184,.10) 63%);background-size:400% 100%;animation:ds-notification-shimmer 1.4s ease infinite}
  .ds-notification-skeleton .sk-avatar{width:32px;height:32px;border-radius:var(--ds-radius-sm,6px)}
  .ds-notification-skeleton .sk-title{width:62%;height:10px;margin-bottom:8px}
  .ds-notification-skeleton .sk-line{width:88%;height:8px}
  @keyframes ds-notification-shimmer{0%{background-position:100% 50%}100%{background-position:0 50%}}
  @media (prefers-reduced-motion: reduce){.ds-notification-skeleton span{animation:none}}
  .ds-notification-see-all{display:flex;align-items:center;justify-content:center;min-height:44px;border-top:1px solid var(--ds-border,#1e2f47);background:var(--ds-surface-3,#0f1928);color:var(--ds-accent-text,#93c5fd);font-size:.8125rem;font-weight:500;text-decoration:none}
  .ds-notification-see-all:hover{background:var(--ds-surface-2,#1a2638)}
  @media (max-width:900px){
    .ds-notification-center--sidebar{top:8px;left:auto;right:12px;z-index:910}
    .ds-notification-panel{top:60px;left:12px;right:12px;width:auto;max-height:calc(100dvh - 72px)}
    .ds-notification-center--topbar .ds-notification-panel{top:52px;left:12px;right:12px;width:auto}
    html.ds-nav-open .ds-notification-center--sidebar{visibility:hidden}
  }
</style>

<script>
(() => {
  const root = document.getElementById('ds-notification-center');
  if (!root || root.dataset.initialized === '1') return;
  root.dataset.initialized = '1';

  const trigger = document.getElementById('ds-notification-trigger');
  const panel = document.getElementById('ds-notification-panel');
  const badge = document.getElementById('ds-notification-badge');
  const list = document.getElementById('ds-notification-list');
  const summary = document.getElementById('ds-notification-summary');
  const readAll = document.getElementById('ds-notification-read-all');
  const tabs = Array.from(root.querySelectorAll('[data-notification-filter]'));
  const csrf = @json(csrf_token());
  let filter = 'all';
  let loading = false;

  const endpoints = {
    feed: @json(route('student.notifications.feed')),
    count: @json(route('student.notifications.count')),
    readAll: @json(route('student.notifications.read-all')),
    login: @json(route('login', ['expired' => 1])),
  };

  const redirectExpiredSession = (destination = null) => {
    if (window.DataSenseiSession?.redirectToLogin) {
      window.DataSenseiSession.redirectToLogin(true, destination);
      return;
    }

    window.__dataSenseiSessionEnding = true;
    try {
      window.dispatchEvent(new CustomEvent('datasensei:session-ending', {
        detail: {expired: true},
      }));
    } catch (_) {}
    window.location.replace(destination || endpoints.login);
  };

  const iconFor = (category) => {
    const paths = {
      assignment: '<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>',
      assessment: '<path d="M4 4h16v16H4z"/><path d="M8 9h8M8 13h8M8 17h5"/>',
      achievement: '<path d="M8 21h8M12 17v4"/><path d="M7 4h10v4a5 5 0 01-10 0V4z"/>',
      challenge: '<path d="M13 10V3L4 14h7v7l9-11h-7z"/>',
      class: '<path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>',
      module: '<path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/>',
      general: '<path d="M12 22a10 10 0 100-20 10 10 0 000 20z"/><path d="M12 8v4M12 16h.01"/>',
    };
    return `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">${paths[category] || paths.general}</svg>`;
  };

  const escapeHtml = (value) => String(value ?? '').replace(/[&<>'"]/g, char => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'
  }[char]));

  const setCount = (count) => {
    const value = Math.max(0, Number(count) || 0);
    badge.textContent = value > 99 ? '99+' : String(value);
    badge.hidden = value === 0;
    summary.textContent = value === 0 ? 'You are all caught up' : `${value} unread notification${value === 1 ? '' : 's'}`;
    document.querySelectorAll('[data-student-notification-count]').forEach(el => {
      el.textContent = value > 99 ? '99+' : String(value);
      el.hidden = value === 0;
    });
  };

  const render = (items) => {
    if (!Array.isArray(items) || items.length === 0) {
      list.innerHTML = `<div class="ds-notification-empty">${filter === 'unread' ? 'No unread notifications.' : 'No notifications yet.'}</div>`;
      return;
    }

    list.innerHTML = items.map(item => `
      <a class="ds-notification-item ${item.is_read ? '' : 'unread'}" href="${escapeHtml(item.open_url)}">
        <span class="ds-notification-icon">${iconFor(item.category)}</span>
        <span>
          <span class="ds-notification-item-title">${escapeHtml(item.title)}</span>
          <span class="ds-notification-item-message">${escapeHtml(item.message)}</span>
          <span class="ds-notification-item-time">${escapeHtml(item.time)}</span>
        </span>
        <span class="ds-notification-unread-dot" aria-label="Unread"></span>
      </a>
    `).join('');
  };

  const request = async (url, options = {}) => {
    const {background = false, ...fetchOptions} = options;
    const response = await fetch(url, {
      credentials: 'same-origin',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...(background ? {'X-DataSensei-Background': '1'} : {}),
        ...(fetchOptions.method && fetchOptions.method !== 'GET' ? {'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json'} : {}),
        ...(fetchOptions.headers || {}),
      },
      ...fetchOptions,
    });

    if (response.status === 401 || response.status === 419) {
      let loginUrl = endpoints.login;
      try {
        const payload = await response.json();
        if (payload?.login_url) loginUrl = payload.login_url;
      } catch (_) {}
      redirectExpiredSession(loginUrl);
      throw new Error('Session expired');
    }

    if (!response.ok) throw new Error('Request failed');
    return response.json();
  };

  const loadFeed = async () => {
    if (loading) return;
    loading = true;
    list.innerHTML = '<div class="ds-notification-loading ds-visually-hidden">Loading notifications…</div>' + '<div class="ds-notification-skeleton" aria-hidden="true"><span class="sk-avatar"></span><div><span class="sk-title"></span><span class="sk-line"></span></div></div>'.repeat(3);
    try {
      const data = await request(`${endpoints.feed}?filter=${encodeURIComponent(filter)}`);
      setCount(data.unread_count);
      render(data.notifications);
    } catch (error) {
      list.innerHTML = '<div class="ds-notification-error">Notifications could not be loaded. Refresh the page and try again.</div>';
    } finally {
      loading = false;
    }
  };

  const refreshCount = async () => {
    try {
      const data = await request(endpoints.count, {background: true});
      setCount(data.unread_count);
    } catch (error) {
      // Keep the last known count when the network is temporarily unavailable.
    }
  };

  const openPanel = () => {
    panel.hidden = false;
    trigger.setAttribute('aria-expanded', 'true');
    loadFeed();
  };

  const closePanel = () => {
    panel.hidden = true;
    trigger.setAttribute('aria-expanded', 'false');
  };

  trigger.addEventListener('click', (event) => {
    event.stopPropagation();
    panel.hidden ? openPanel() : closePanel();
  });

  tabs.forEach(tab => tab.addEventListener('click', () => {
    filter = tab.dataset.notificationFilter || 'all';
    tabs.forEach(candidate => {
      const active = candidate === tab;
      candidate.classList.toggle('active', active);
      candidate.setAttribute('aria-selected', active ? 'true' : 'false');
    });
    loadFeed();
  }));

  readAll.addEventListener('click', async () => {
    readAll.disabled = true;
    try {
      await request(endpoints.readAll, {method: 'POST', body: '{}'});
      setCount(0);
      await loadFeed();
    } catch (error) {
      // The feed remains unchanged when the request fails.
    } finally {
      readAll.disabled = false;
    }
  });

  document.addEventListener('click', (event) => {
    if (!root.contains(event.target)) closePanel();
  });
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closePanel();
  });
  document.addEventListener('visibilitychange', () => {
    if (!document.hidden) refreshCount();
  });

  refreshCount();
  window.setInterval(() => {
    if (!document.hidden) refreshCount();
  }, 60000);
})();
</script>
