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
      <div class="ds-notification-loading">Loading notifications…</div>
    </div>

    <a href="{{ route('student.notifications.index') }}" class="ds-notification-see-all">See all notifications</a>
  </section>
</div>

<style>
  .ds-notification-center{position:relative;flex:0 0 auto;font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif}
  .ds-notification-center--sidebar{position:fixed;top:14px;left:208px;z-index:5100}
  .ds-notification-trigger{position:relative;width:38px;height:38px;border:1px solid var(--border,#26364d);border-radius:9px;background:var(--surface2,#182438);color:var(--muted,#8ca0bb);display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:.16s ease;padding:0}
  .ds-notification-trigger:hover,.ds-notification-trigger[aria-expanded="true"]{color:var(--text,#f8fafc);border-color:var(--accent,#3b82f6);background:rgba(59,130,246,.12)}
  .ds-notification-center--topbar .ds-notification-trigger{width:30px;height:30px;border-radius:6px;background:transparent}
  .ds-notification-center--topbar .ds-notification-panel{top:50px;left:auto;right:12px;width:min(400px,calc(100vw - 24px))}
  .ds-notification-badge{position:absolute;top:-7px;right:-7px;min-width:19px;height:19px;padding:0 5px;border-radius:999px;background:#ef4444;color:#fff;border:2px solid var(--surface,#111c2d);font-size:10px;font-weight:800;line-height:15px;text-align:center}
  .ds-notification-panel{position:fixed;top:68px;left:242px;width:min(400px,calc(100vw - 270px));max-height:min(640px,calc(100vh - 84px));background:#111c2d;border:1px solid #273a55;border-radius:14px;box-shadow:0 24px 70px rgba(0,0,0,.48);z-index:5000;overflow:hidden;color:#f8fafc}
  .ds-notification-panel[hidden]{display:none}
  .ds-notification-panel-head{display:flex;justify-content:space-between;align-items:flex-start;gap:14px;padding:18px 18px 12px}
  .ds-notification-panel-head h2{font-size:1.25rem;line-height:1.2;margin:0;font-weight:750;letter-spacing:-.025em}
  .ds-notification-panel-head p{margin:4px 0 0;color:#8ca0bb;font-size:.76rem}
  .ds-notification-text-btn{border:0;background:transparent;color:#60a5fa;font:inherit;font-size:.76rem;font-weight:700;cursor:pointer;padding:4px 0;white-space:nowrap}
  .ds-notification-text-btn:hover{text-decoration:underline}
  .ds-notification-tabs{display:flex;gap:5px;padding:0 14px 12px;border-bottom:1px solid #22334b}
  .ds-notification-tabs button{border:0;background:transparent;color:#8ca0bb;padding:7px 13px;border-radius:999px;font:inherit;font-size:.78rem;font-weight:700;cursor:pointer}
  .ds-notification-tabs button.active{background:rgba(59,130,246,.16);color:#93c5fd}
  .ds-notification-list{overflow-y:auto;max-height:470px;min-height:120px}
  .ds-notification-item{display:grid;grid-template-columns:40px minmax(0,1fr) 8px;gap:11px;align-items:start;padding:13px 15px;text-decoration:none;color:inherit;border-bottom:1px solid rgba(39,58,85,.72);position:relative;transition:background .14s ease}
  .ds-notification-item:hover{background:#17243a}
  .ds-notification-item.unread{background:rgba(59,130,246,.075)}
  .ds-notification-item.unread:hover{background:rgba(59,130,246,.13)}
  .ds-notification-icon{width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:#1d2b42;border:1px solid #2d4262;color:#93c5fd;flex-shrink:0}
  .ds-notification-icon svg{width:18px;height:18px}
  .ds-notification-item-title{font-size:.85rem;font-weight:750;line-height:1.35;margin:0 0 3px;color:#f8fafc}
  .ds-notification-item-message{font-size:.78rem;line-height:1.45;color:#b0bfd2;margin:0;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
  .ds-notification-item-time{font-size:.7rem;color:#7186a3;margin-top:5px}
  .ds-notification-unread-dot{width:8px;height:8px;border-radius:50%;background:#3b82f6;margin-top:7px}
  .ds-notification-item:not(.unread) .ds-notification-unread-dot{visibility:hidden}
  .ds-notification-loading,.ds-notification-empty,.ds-notification-error{min-height:140px;display:flex;align-items:center;justify-content:center;padding:28px;color:#8ca0bb;text-align:center;font-size:.82rem;line-height:1.5}
  .ds-notification-see-all{display:flex;align-items:center;justify-content:center;min-height:46px;border-top:1px solid #273a55;color:#93c5fd;text-decoration:none;font-size:.8rem;font-weight:750;background:#101a2a}
  .ds-notification-see-all:hover{background:#17243a}
  @media(max-width:700px){
    .ds-notification-center--sidebar{left:auto;right:14px;top:14px}
    .ds-notification-panel{top:64px;left:12px;right:12px;width:auto;max-height:calc(100vh - 78px)}
    .ds-notification-center--topbar .ds-notification-panel{top:50px;left:12px;right:12px;width:auto}
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
    list.innerHTML = '<div class="ds-notification-loading">Loading notifications…</div>';
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
