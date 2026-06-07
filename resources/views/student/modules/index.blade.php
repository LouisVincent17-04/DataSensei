{{-- resources/views/student/modules/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Modules</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        :root {
            --bg: #0d1320;
            --surface: #111c2d;
            --surface2: #1a2638;
            --border: #1e2f47;
            --accent: #3b82f6;
            --accent3: #10b981;
            --text: #fafafa;
            --muted: #8aa0bd;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(59, 130, 246, .12), transparent 32rem),
                var(--bg);
            color: var(--text);
        }

        .student-mod-page {
            padding: 32px;
            max-width: 1280px;
            margin: 0 auto;
        }

        .student-mod-kicker {
            color: var(--accent);
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            font-weight: 900;
            margin-bottom: 6px;
        }

        .student-mod-header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 900;
            letter-spacing: -.03em;
        }

        .student-mod-header p {
            color: var(--muted);
            margin-top: 8px;
        }

        .student-mod-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 16px;
            margin-top: 24px;
        }

        .student-mod-card {
            background: rgba(17,28,45,.92);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 14px 35px rgba(0,0,0,.20);
        }

        .student-mod-number {
            color: var(--muted);
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            font-weight: 900;
            margin-bottom: 8px;
        }

        .student-mod-card h2 {
            margin: 0;
            font-size: 1.12rem;
            font-weight: 900;
        }

        .student-mod-card p {
            color: var(--muted);
            line-height: 1.5;
            font-size: .9rem;
        }

        .student-mod-versions {
            display: grid;
            gap: 8px;
            margin-top: 14px;
        }

        .student-mod-versions a {
            text-decoration: none;
            color: #bfdbfe;
            background: rgba(59,130,246,.12);
            border: 1px solid rgba(59,130,246,.25);
            border-radius: 12px;
            padding: 10px 12px;
            font-weight: 800;
            font-size: .86rem;
        }

        .student-mod-versions a:hover {
            background: rgba(59,130,246,.20);
        }

        .student-mod-empty {
            background: rgba(17,28,45,.92);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 24px;
            color: var(--muted);
            font-weight: 700;
        }
    </style>
</head>
<body>
    <main class="student-mod-page">
        <div class="student-mod-header">
            <div class="student-mod-kicker">Student Learning</div>
            <h1>Learning Modules</h1>
            <p>Open a module to study the same NetAcad-style content structure.</p>
        </div>

        <div class="student-mod-grid">
            @forelse ($modules as $moduleNo => $versions)
                @php $first = $versions->first(); @endphp

                <article class="student-mod-card">
                    <div class="student-mod-number">Module {{ $moduleNo }}</div>
                    <h2>{{ $first->title }}</h2>
                    <p>{{ $first->description }}</p>

                    <div class="student-mod-versions">
                        @foreach ($versions as $version)
                            <a href="{{ route('student.modules.show', $version) }}">
                                {{ $version->version_name }} · {{ $version->estimated_minutes }} min
                            </a>
                        @endforeach
                    </div>
                </article>
            @empty
                <div class="student-mod-empty">No modules available yet.</div>
            @endforelse
        </div>
    </main>
</body>
</html>
