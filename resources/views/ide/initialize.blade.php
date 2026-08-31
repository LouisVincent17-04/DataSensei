<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prepare Python IDE</title>
    @include('partials.brand-head')
    <style>
        :root { color-scheme: dark; }
        * { box-sizing: border-box; }
        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 24px;
            background: #0d1320;
            color: #f8fafc;
            font-family: Inter, system-ui, sans-serif;
        }
        .panel {
            width: min(440px, 100%);
            padding: 28px;
            border: 1px solid #263854;
            border-radius: 16px;
            background: #111c2d;
            text-align: center;
        }
        p { color: #91a4bf; line-height: 1.6; }
        button {
            border: 0;
            border-radius: 10px;
            padding: 11px 16px;
            background: #3b82f6;
            color: #fff;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <main class="panel">
        <h1>Preparing your Python IDE</h1>
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
