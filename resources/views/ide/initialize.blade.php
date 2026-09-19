<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prepare Python IDE — DataSensei</title>
<style>
        /* Colours, type and radius come from partials.design-system. */
        * { box-sizing: border-box; }
        body {
            min-height: 100vh;
            min-height: 100dvh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 24px 16px;
            background: var(--bg);
            color: var(--text);
            font-family: var(--ds-font-sans);
        }
        .panel {
            width: min(440px, 100%);
            padding: 24px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: var(--surface);
        }
        .panel p { margin: 8px 0 20px; color: var(--muted); font-size: .875rem; line-height: 1.55; }
        .panel button {
            min-height: 38px;
            padding: 0 16px;
            border: 1px solid var(--accent);
            border-radius: var(--radius-sm);
            background: var(--accent);
            color: #fff;
            font: 500 .875rem/1.2 var(--ds-font-sans);
            cursor: pointer;
            transition: background .12s ease, border-color .12s ease;
        }
        .panel button:hover { background: var(--accent-hover); border-color: var(--accent-hover); }
    </style>
    @include('partials.page-head', ['pageTitle' => 'Prepare Python IDE', 'pageDescription' => 'Write, run, and get feedback on Python inside the DataSensei workspace.'])
</head>
<body>
    <main class="panel">
        <h1 class="ds-page-title">Preparing your Python IDE</h1>
        <p>Your private workspace needs to be initialized once before the editor opens.</p>
        <form id="workspace-initializer" method="POST" action="{{ route('ide.workspace.initialize') }}">
            @csrf
            <button type="submit">Open Python IDE</button>
        </form>
    </main>
    <script>
        document.getElementById('workspace-initializer').requestSubmit();
    </script>
</body>
</html>
