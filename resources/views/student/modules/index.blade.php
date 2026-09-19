{{-- resources/views/student/modules/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Modules — DataSensei</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
        /* Module library for students. Colours, type and radius come from
           partials.design-system. */
        :root {
            --accent3: var(--ds-success);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font-family: var(--ds-font-sans);
        }

        .student-mod-page {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 28px 32px 48px;
        }

        .student-mod-header {
            margin-bottom: 24px;
        }

        .student-mod-header p {
            max-width: 72ch;
            margin: 4px 0 0;
            color: var(--muted);
            font-size: .875rem;
            line-height: 1.5;
        }

        .student-mod-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            align-items: start;
        }

        .student-mod-card {
            min-width: 0;
            padding: 20px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
        }

        .student-mod-number {
            margin-bottom: 4px;
            color: var(--muted);
            font-size: .8125rem;
            font-weight: 500;
            font-variant-numeric: tabular-nums;
        }

        .student-mod-card h2 {
            margin: 0;
            color: var(--text);
            font-size: 1rem;
            font-weight: 600;
            line-height: 1.35;
            overflow-wrap: break-word;
        }

        .student-mod-card p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: .875rem;
            line-height: 1.55;
            overflow-wrap: break-word;
        }

        /* Versions: one row per version, inside a flat inset. */
        .student-mod-versions {
            display: grid;
            margin-top: 16px;
            overflow: hidden;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--surface3);
        }

        .student-mod-versions a {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 12px;
            min-height: 40px;
            padding: 10px 12px;
            color: var(--ds-accent-text);
            font-size: .875rem;
            font-weight: 500;
            line-height: 1.4;
            text-decoration: none;
            transition: background-color .12s ease, color .12s ease;
        }

        .student-mod-versions a + a {
            border-top: 1px solid var(--border);
        }

        .student-mod-versions a:hover {
            background: var(--surface2);
            color: var(--text);
        }

        .student-mod-version-name {
            min-width: 0;
            overflow-wrap: anywhere;
        }

        .student-mod-version-time {
            flex-shrink: 0;
            color: var(--muted);
            font-size: .8125rem;
            font-weight: 400;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        .student-mod-empty {
            grid-column: 1 / -1;
            padding: 32px 20px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            color: var(--muted);
            font-size: .875rem;
            line-height: 1.5;
            text-align: center;
        }

        @media (max-width: 1100px) {
            .student-mod-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 900px) {
            .student-mod-page {
                padding: 24px 20px 40px;
            }
        }

        @media (max-width: 640px) {
            .student-mod-page {
                padding: 20px 16px 32px;
            }

            .student-mod-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .student-mod-card {
                padding: 16px;
            }
        }
    </style>
  @include('partials.ui-polish')
    @include('partials.page-head', ['pageTitle' => 'Student Modules', 'pageDescription' => 'Work through DataSensei lessons and modules at your own pace.'])
</head>
<body>
    <main class="student-mod-page">
        <div class="student-mod-header">
            <h1 class="ds-page-title">Learning Modules</h1>
            <p>
                @if($isClassScoped)
                    Open a module assigned through one of your active classes.
                @else
                    Open any active module in the independent-learning library.
                @endif
            </p>
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
                                <span class="student-mod-version-name">{{ $version->version_name }}</span>
                                <span class="student-mod-version-time">{{ $version->estimated_minutes }} min</span>
                            </a>
                        @endforeach
                    </div>
                </article>
            @empty
                <div class="student-mod-empty">
                    {{ $isClassScoped
                        ? 'No module versions have been assigned to your active classes yet.'
                        : 'No modules are available yet.' }}
                </div>
            @endforelse
        </div>
    </main>
</body>
</html>
