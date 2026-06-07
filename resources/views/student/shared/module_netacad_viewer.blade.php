{{-- resources/views/student/shared/module_netacad_viewer.blade.php --}}
@php
    use Illuminate\Support\Facades\Route;

    $backRoute = $backRoute ?? url()->previous();
    $viewerRole = $viewerRole ?? 'Student';
    $versionRouteName = $versionRouteName ?? null;
    $isStudentView = strtolower($viewerRole) === 'student';
    $ideUrl = Route::has('ide.index') ? route('ide.index') : url('/ide');
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $module->title }} — {{ $module->version_name }}</title>

    <style>
        :root {
            --bg: #0d1320;
            --surface: #111c2d;
            --surface2: #1a2638;
            --surface3: #0f1928;
            --border: #1e2f47;
            --border-hover: #2c4168;
            --accent: #3b82f6;
            --accent2: #8b5cf6;
            --accent3: #10b981;
            --warn: #ef4444;
            --warn2: #f59e0b;
            --text: #fafafa;
            --muted: #8aa0bd;
            --dim: #4b607e;
            --radius: 16px;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(59, 130, 246, .12), transparent 32rem),
                radial-gradient(circle at top right, rgba(139, 92, 246, .10), transparent 30rem),
                var(--bg);
            color: var(--text);
        }

        a {
            color: inherit;
        }

        .lesson-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 330px minmax(0, 1fr);
        }

        .lesson-nav {
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            background: rgba(17, 28, 45, .92);
            border-right: 1px solid var(--border);
            backdrop-filter: blur(14px);
            padding: 22px;
        }

        .lesson-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            text-decoration: none;
            font-weight: 800;
            font-size: .84rem;
            margin-bottom: 18px;
        }

        .lesson-back:hover {
            color: var(--text);
        }

        .course-card,
        .side-card {
            border: 1px solid var(--border);
            border-radius: 20px;
            background: linear-gradient(180deg, rgba(255,255,255,.05), rgba(255,255,255,.025));
            box-shadow: 0 14px 40px rgba(0,0,0,.20);
            padding: 18px;
            margin-bottom: 15px;
        }

        .kicker {
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: .1em;
            font-size: .68rem;
            font-weight: 900;
            margin-bottom: 8px;
        }

        .course-card h1 {
            margin: 0;
            font-size: 1.26rem;
            line-height: 1.25;
            letter-spacing: -.03em;
        }

        .course-card p {
            margin: 10px 0 0;
            color: var(--muted);
            font-size: .86rem;
            line-height: 1.55;
        }

        .meta-list {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            margin-top: 14px;
        }

        .meta-list span {
            border: 1px solid rgba(59,130,246,.24);
            background: rgba(59,130,246,.10);
            color: #bfdbfe;
            border-radius: 999px;
            padding: 4px 8px;
            font-size: .68rem;
            font-weight: 900;
        }

        .side-card h3 {
            margin: 0 0 12px;
            color: var(--text);
            font-size: .78rem;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .version-list,
        .topic-list {
            display: grid;
            gap: 7px;
        }

        .version-list a,
        .topic-list a {
            color: var(--muted);
            text-decoration: none;
            border: 1px solid transparent;
            border-radius: 12px;
            padding: 10px;
            font-size: .82rem;
            font-weight: 800;
            line-height: 1.35;
        }

        .version-list a:hover,
        .topic-list a:hover,
        .version-list a.is-current {
            background: rgba(59,130,246,.10);
            border-color: rgba(59,130,246,.25);
            color: var(--text);
        }

        .version-list small {
            display: block;
            color: var(--dim);
            margin-top: 2px;
        }

        .topic-list a {
            display: grid;
            grid-template-columns: 34px 1fr;
            align-items: start;
        }

        .topic-list span {
            width: 25px;
            height: 25px;
            background: rgba(59,130,246,.14);
            color: #93c5fd;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .68rem;
            font-weight: 900;
        }

        .lesson-content {
            padding: 34px;
            max-width: 1160px;
            width: 100%;
        }

        .lesson-hero {
            border: 1px solid var(--border);
            background:
                linear-gradient(135deg, rgba(59,130,246,.18), rgba(139,92,246,.10)),
                var(--surface);
            border-radius: 28px;
            padding: 34px;
            margin-bottom: 22px;
            box-shadow: 0 18px 45px rgba(0,0,0,.22);
        }

        .lesson-hero h2 {
            margin: 0;
            font-size: 2.15rem;
            line-height: 1.1;
            letter-spacing: -.05em;
        }

        .lesson-hero p {
            max-width: 800px;
            color: var(--muted);
            line-height: 1.7;
            margin: 12px 0 0;
            font-size: 1rem;
        }

        .lesson-section {
            border: 1px solid var(--border);
            background: rgba(17, 28, 45, .92);
            border-radius: 26px;
            padding: 30px;
            margin-bottom: 22px;
            box-shadow: 0 14px 35px rgba(0,0,0,.20);
            scroll-margin-top: 28px;
        }

        .section-head {
            display: grid;
            grid-template-columns: 48px minmax(0, 1fr);
            gap: 16px;
            align-items: flex-start;
            margin-bottom: 18px;
        }

        .section-head > span {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            background: rgba(59,130,246,.14);
            color: #93c5fd;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            border: 1px solid rgba(59,130,246,.20);
        }

        .section-head h3 {
            margin: 0;
            font-size: 1.35rem;
            letter-spacing: -.025em;
        }

        .section-head p {
            color: var(--muted);
            margin: 5px 0 0;
            line-height: 1.5;
        }

        .lesson-body {
            color: #d5deea;
            line-height: 1.85;
            font-size: 1rem;
        }

        .code-card {
            margin-top: 20px;
            background: #08111f;
            border: 1px solid var(--border);
            border-radius: 18px;
            overflow: hidden;
        }

        .code-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 14px;
            border-bottom: 1px solid var(--border);
            background: rgba(255,255,255,.03);
        }

        .code-title {
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .08em;
            font-size: .72rem;
            font-weight: 900;
        }

        .try-btn {
            border: 1px solid rgba(16,185,129,.35);
            background: rgba(16,185,129,.12);
            color: #6ee7b7;
            border-radius: 10px;
            padding: 7px 10px;
            font-weight: 900;
            font-size: .76rem;
            cursor: pointer;
            text-decoration: none;
        }

        .try-btn:hover {
            background: rgba(16,185,129,.20);
        }

        .code-card pre {
            margin: 0;
            overflow-x: auto;
            padding: 20px;
        }

        .code-card code {
            color: #dbeafe;
            font-family: Consolas, Monaco, "JetBrains Mono", monospace;
            font-size: .92rem;
            line-height: 1.65;
        }

        .panel,
        .activity,
        .scenario,
        .explanation,
        .tip {
            margin-top: 18px;
            border-radius: 18px;
            padding: 16px 18px;
            line-height: 1.6;
        }

        .panel {
            background: rgba(255,255,255,.035);
            border: 1px solid var(--border);
            color: #dbeafe;
        }

        .panel-success {
            background: rgba(16,185,129,.10);
            border-color: rgba(16,185,129,.25);
            color: #a7f3d0;
        }

        .panel-warning {
            background: rgba(245,158,11,.10);
            border-color: rgba(245,158,11,.25);
            color: #fde68a;
        }

        .activity {
            background: rgba(59,130,246,.10);
            border: 1px solid rgba(59,130,246,.24);
            color: #bfdbfe;
        }

        .panel h4 {
            margin: 0 0 10px;
            font-size: .84rem;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: inherit;
        }

        .panel ul,
        .panel ol {
            margin: 0;
            padding-left: 21px;
        }

        .panel li {
            margin-bottom: 6px;
        }

        .quiz-list {
            display: grid;
            gap: 18px;
        }

        .question-card {
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 22px;
            background: rgba(255,255,255,.025);
        }

        .question-top {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .06em;
            font-size: .7rem;
            font-weight: 900;
            margin-bottom: 10px;
        }

        .question-card h4 {
            margin: 0;
            font-size: 1rem;
            line-height: 1.6;
            color: var(--text);
        }

        .scenario {
            background: rgba(255,255,255,.035);
            border: 1px solid var(--border);
            color: #d5deea;
        }

        .scenario p,
        .explanation p {
            margin: 6px 0 0;
        }

        .choices {
            display: grid;
            gap: 10px;
            margin-top: 16px;
        }

        .choice {
            display: grid;
            grid-template-columns: 34px 1fr;
            gap: 10px;
            align-items: start;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 12px;
            color: #d5deea;
            background: rgba(255,255,255,.02);
            cursor: pointer;
        }

        .choice:hover {
            border-color: var(--border-hover);
            background: rgba(255,255,255,.04);
        }

        .choice input {
            display: none;
        }

        .choice-letter {
            width: 26px;
            height: 26px;
            border-radius: 9px;
            background: rgba(59,130,246,.12);
            color: #93c5fd;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .74rem;
            font-weight: 900;
            border: 1px solid rgba(59,130,246,.18);
        }

        .choice p {
            margin: 0;
            line-height: 1.5;
        }

        .choice.is-correct {
            background: rgba(16,185,129,.10);
            border-color: rgba(16,185,129,.35);
            color: #a7f3d0;
            font-weight: 800;
        }

        .choice.is-wrong {
            background: rgba(239,68,68,.10);
            border-color: rgba(239,68,68,.35);
            color: #fecaca;
        }

        .check-btn {
            margin-top: 14px;
            border: 1px solid rgba(59,130,246,.35);
            background: rgba(59,130,246,.14);
            color: #bfdbfe;
            border-radius: 12px;
            padding: 9px 13px;
            font-weight: 900;
            cursor: pointer;
        }

        .check-btn:hover {
            background: rgba(59,130,246,.22);
        }

        .result-box {
            display: none;
            margin-top: 14px;
            border-radius: 16px;
            padding: 14px 16px;
            line-height: 1.6;
        }

        .result-box.correct {
            display: block;
            background: rgba(16,185,129,.10);
            border: 1px solid rgba(16,185,129,.35);
            color: #a7f3d0;
        }

        .result-box.wrong {
            display: block;
            background: rgba(245,158,11,.10);
            border: 1px solid rgba(245,158,11,.35);
            color: #fde68a;
        }

        .explanation {
            background: rgba(59,130,246,.10);
            border: 1px solid rgba(59,130,246,.24);
            color: #bfdbfe;
        }

        .tip {
            background: rgba(16,185,129,.10);
            border: 1px solid rgba(16,185,129,.24);
            color: #a7f3d0;
        }

        .muted-note {
            color: var(--muted);
            font-size: .86rem;
            line-height: 1.5;
        }

        @media (max-width: 1024px) {
            .lesson-shell {
                grid-template-columns: 1fr;
            }

            .lesson-nav {
                position: relative;
                height: auto;
            }

            .lesson-content {
                padding: 20px;
            }

            .lesson-hero h2 {
                font-size: 1.7rem;
            }
        }
    </style>
</head>
<body>
    <div class="lesson-shell">
        <aside class="lesson-nav">
            <a href="{{ $backRoute }}" class="lesson-back">← Back</a>

            <div class="course-card">
                <div class="kicker">DataSensei Module</div>
                <h1>{{ $module->title }}</h1>
                <p>{{ $module->description }}</p>

                <div class="meta-list">
                    <span>Module {{ $module->module_no }}</span>
                    <span>{{ $module->version_name }}</span>
                    <span>{{ $module->estimated_minutes }} min</span>
                    <span>{{ $viewerRole }}</span>
                </div>
            </div>

            @if (($relatedVersions ?? collect())->count() > 1)
                <div class="side-card">
                    <h3>Course Versions</h3>
                    <div class="version-list">
                        @foreach ($relatedVersions as $version)
                            <a
                                href="{{ $versionRouteName ? route($versionRouteName, $version) : '#' }}"
                                class="{{ $version->id === $module->id ? 'is-current' : '' }}"
                            >
                                <strong>{{ $version->version_name }}</strong>
                                <small>{{ $version->version_code }}</small>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="side-card">
                <h3>Module Contents</h3>
                <nav class="topic-list">
                    @foreach ($contentSections as $index => $section)
                        <a href="#section-{{ $index + 1 }}">
                            <span>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            {{ $section['heading'] ?? 'Untitled Section' }}
                        </a>
                    @endforeach

                    @if (count($mcqQuestions) > 0)
                        <a href="#knowledge-check">
                            <span>Q</span>
                            Knowledge Check
                        </a>
                    @endif
                </nav>
            </div>
        </aside>

        <main class="lesson-content">
            <section class="lesson-hero">
                <div class="kicker">Python Fundamentals · NetAcad-style Lesson</div>
                <h2>{{ $module->title }}</h2>
                <p>
                    Read the explanation, inspect the example, follow the walkthrough,
                    review common mistakes, and answer the knowledge check.
                </p>
            </section>

            @forelse ($contentSections as $index => $section)
                <section class="lesson-section" id="section-{{ $index + 1 }}">
                    <div class="section-head">
                        <span>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <div>
                            <h3>{{ $section['heading'] ?? 'Untitled Section' }}</h3>
                            @if (!empty($section['subheading']))
                                <p>{{ $section['subheading'] }}</p>
                            @endif
                        </div>
                    </div>

                    @if (!empty($section['body']))
                        <div class="lesson-body">
                            {!! nl2br(e($section['body'])) !!}
                        </div>
                    @endif

                    @if (!empty($section['code']))
                        <div class="code-card">
                            <div class="code-head">
                                <div class="code-title">Python Example</div>

                                @if ($isStudentView)
                                    <button
                                        type="button"
                                        class="try-btn"
                                        onclick='tryInCompiler(@json($section["code"]))'
                                    >
                                        Try in Compiler
                                    </button>
                                @endif
                            </div>
                            <pre><code>{{ $section['code'] }}</code></pre>
                        </div>
                    @endif

                    @if (!empty($section['walkthrough']) && is_array($section['walkthrough']))
                        <div class="panel">
                            <h4>Walkthrough</h4>
                            <ol>
                                @foreach ($section['walkthrough'] as $step)
                                    <li>{{ $step }}</li>
                                @endforeach
                            </ol>
                        </div>
                    @endif

                    @if (!empty($section['netacad_style_activity']))
                        <div class="activity">
                            <strong>Learning Activity:</strong>
                            <p>{{ $section['netacad_style_activity'] }}</p>
                        </div>
                    @endif

                    @if (!empty($section['common_mistakes']) && is_array($section['common_mistakes']))
                        <div class="panel panel-warning">
                            <h4>Common Mistakes</h4>
                            <ul>
                                @foreach ($section['common_mistakes'] as $mistake)
                                    <li>{{ $mistake }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (!empty($section['key_points']) && is_array($section['key_points']))
                        <div class="panel panel-success">
                            <h4>Key Points</h4>
                            <ul>
                                @foreach ($section['key_points'] as $point)
                                    <li>{{ $point }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </section>
            @empty
                <section class="lesson-section">
                    <h3>No content yet</h3>
                    <p class="muted-note">This module has no saved content sections.</p>
                </section>
            @endforelse

            @if (count($mcqQuestions) > 0)
                <section class="lesson-section" id="knowledge-check">
                    <div class="section-head">
                        <span>Q</span>
                        <div>
                            <h3>Knowledge Check</h3>
                            <p>
                                @if ($isStudentView)
                                    Select your answer first, then check it.
                                @else
                                    Instructor answer key and explanations.
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="quiz-list">
                        @foreach ($mcqQuestions as $index => $question)
                            @php
                                $choices = $question['choices'] ?? $question['options'] ?? [];
                                $answer = $question['answer'] ?? $question['correct_answer'] ?? null;
                                $answerKey = is_string($answer) ? $answer : '';
                            @endphp

                            <article class="question-card" data-question-card data-answer='@json($answerKey)'>
                                <div class="question-top">
                                    <span>Question {{ $index + 1 }}</span>
                                    @if (!empty($question['topic']))
                                        <small>{{ $question['topic'] }}</small>
                                    @endif
                                </div>

                                <h4>{{ $question['question'] ?? $question['q'] ?? 'Untitled question' }}</h4>

                                @if (!empty($question['scenario']))
                                    <div class="scenario">
                                        <strong>Scenario:</strong>
                                        <p>{{ $question['scenario'] }}</p>
                                    </div>
                                @endif

                                <div class="choices">
                                    @foreach ($choices as $choiceIndex => $choice)
                                        @php
                                            $choiceText = is_array($choice)
                                                ? ($choice['text'] ?? $choice['option_text'] ?? $choice[0] ?? '')
                                                : $choice;

                                            $isCorrect = false;

                                            if (is_array($choice)) {
                                                $isCorrect = (bool)($choice['is_correct'] ?? $choice[1] ?? false);
                                            } elseif ($answer !== null) {
                                                $isCorrect = $choiceText === $answer;
                                            }
                                        @endphp

                                        @if ($isStudentView)
                                            <label class="choice" data-choice data-value="{{ $choiceText }}">
                                                <input type="radio" name="q{{ $index }}" value="{{ $choiceText }}">
                                                <span class="choice-letter">{{ chr(65 + $choiceIndex) }}</span>
                                                <p>{{ $choiceText }}</p>
                                            </label>
                                        @else
                                            <div class="choice {{ $isCorrect ? 'is-correct' : '' }}">
                                                <span class="choice-letter">{{ chr(65 + $choiceIndex) }}</span>
                                                <p>{{ $choiceText }}</p>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                @if ($isStudentView)
                                    <button type="button" class="check-btn" onclick="checkModuleAnswer(this)">
                                        Check Answer
                                    </button>

                                    <div class="result-box" data-result-box></div>

                                    @if (!empty($question['explanation']))
                                        <div class="explanation" data-explanation style="display:none;">
                                            <strong>Explanation:</strong>
                                            <p>{{ $question['explanation'] }}</p>
                                        </div>
                                    @endif
                                @else
                                    @if (!empty($question['explanation']))
                                        <div class="explanation">
                                            <strong>Explanation:</strong>
                                            <p>{{ $question['explanation'] }}</p>
                                        </div>
                                    @endif

                                    @if (!empty($question['why_other_choices_are_wrong']) && is_array($question['why_other_choices_are_wrong']))
                                        <div class="panel panel-warning">
                                            <h4>Why the other choices are wrong</h4>
                                            <ul>
                                                @foreach ($question['why_other_choices_are_wrong'] as $wrongReason)
                                                    <li>{{ $wrongReason }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                @endif

                                @if (!empty($question['learning_tip']))
                                    <div class="tip">
                                        <strong>Learning Tip:</strong> {{ $question['learning_tip'] }}
                                    </div>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif
        </main>
    </div>

    <script>
        const ideUrl = @json($ideUrl);

        function tryInCompiler(code) {
            sessionStorage.setItem('datasensei_pending_code', code || '');
            sessionStorage.setItem('datasensei_return_url', window.location.href);
            window.location.href = ideUrl;
        }

        function checkModuleAnswer(button) {
            const card = button.closest('[data-question-card]');
            const selected = card.querySelector('input[type="radio"]:checked');
            const resultBox = card.querySelector('[data-result-box]');
            const explanation = card.querySelector('[data-explanation]');
            const answer = JSON.parse(card.dataset.answer || '""');

            card.querySelectorAll('[data-choice]').forEach(choice => {
                choice.classList.remove('is-correct', 'is-wrong');
            });

            if (!selected) {
                resultBox.className = 'result-box wrong';
                resultBox.textContent = 'Please select an answer first.';
                return;
            }

            const selectedChoice = selected.closest('[data-choice]');
            const isCorrect = selected.value === answer;

            if (isCorrect) {
                selectedChoice.classList.add('is-correct');
                resultBox.className = 'result-box correct';
                resultBox.textContent = 'Correct. Nice work!';
            } else {
                selectedChoice.classList.add('is-wrong');

                card.querySelectorAll('[data-choice]').forEach(choice => {
                    if (choice.dataset.value === answer) {
                        choice.classList.add('is-correct');
                    }
                });

                resultBox.className = 'result-box wrong';
                resultBox.textContent = 'Not quite. Review the highlighted correct answer and explanation.';
            }

            if (explanation) {
                explanation.style.display = 'block';
            }
        }
    </script>
</body>
</html>
