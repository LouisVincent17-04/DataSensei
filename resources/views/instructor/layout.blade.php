{{--
    Page frame for the instructor's Reports and Class Analytics (DataSensei
    Updates 8): the instructor sidebar, a plain page title and description,
    flash messages, then the page content. Colours, type and radius come from
    partials.design-system.

    Sections: title, page_title, page_subtitle, page_actions (optional),
    content. Stacks: head, scripts.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title') — DataSensei</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; }
    body { margin: 0; font-family: var(--ds-font-sans); background: var(--bg); color: var(--text); }
    .ip-layout { display: flex; min-height: 100vh; }
    .ip-main { flex: 1; min-width: 0; padding: 28px 32px 48px; background: var(--bg); }
    .ip-wrap { width: 100%; max-width: 1440px; margin: 0 auto; }
    .ip-top { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 24px; }
    .ip-top > div:first-child { min-width: 0; flex: 1 1 320px; }
    .ip-subtitle { margin: 4px 0 0; max-width: 80ch; color: var(--muted); font-size: .875rem; line-height: 1.55; }
    .ip-alert { margin-bottom: 16px; padding: 12px 16px; border: 1px solid var(--ds-success-border); border-radius: var(--radius-sm); background: var(--ds-success-soft); color: #d1fae5; font-size: .875rem; }
    .ip-alert.is-error { border-color: var(--ds-danger-border); background: var(--ds-danger-soft); color: #fee2e2; }
    @media (max-width: 900px) { .ip-main { padding: 24px 20px 40px; } }
    @media (max-width: 700px) { .ip-layout { display: block; } }
    @media (max-width: 640px) { .ip-main { padding: 20px 16px 32px; } }
  </style>
  @include('partials.page-head', ['pageTitle' => trim($__env->yieldContent('title')), 'pageDescription' => trim($__env->yieldContent('page_subtitle'))])
  @stack('head')
</head>
<body>
<div class="ip-layout">
  @include('partials.instructor-sidebar')
  <main class="ip-main">
    <div class="ip-wrap">
      <div class="ip-top">
        <div>
          <h1 class="ds-page-title">@yield('page_title')</h1>
          @hasSection('page_subtitle')<p class="ip-subtitle">@yield('page_subtitle')</p>@endif
        </div>
        @yield('page_actions')
      </div>

      @if(session('success'))<div class="ip-alert" role="status">{{ session('success') }}</div>@endif
      @if(session('error'))<div class="ip-alert is-error" role="alert">{{ session('error') }}</div>@endif

      @yield('content')
    </div>
  </main>
</div>
@stack('scripts')
</body>
</html>
