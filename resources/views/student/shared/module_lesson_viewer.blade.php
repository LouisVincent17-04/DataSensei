{{-- resources/views/student/shared/module_lesson_viewer.blade.php --}}
@php
    use Illuminate\Support\Facades\Route;

    $backRoute = $backRoute ?? url()->previous();
    $viewerRole = $viewerRole ?? 'Student';
    $versionRouteName = $versionRouteName ?? null;
    $isStudentView = strtolower($viewerRole) === 'student';
    $ideUrl = Route::has('ide.index') ? route('ide.index') : url('/ide');
    $versionRouteExtra = $versionRouteExtra ?? [];

    // What You Will Learn (DataSensei Updates 5): the module's descriptive
    // learning outcomes. They replace the older "Intended Learning Outcomes"
    // (or "Learning Objectives") content section, so it is not shown twice.
    $learningOutcomes = $learningOutcomes ?? (is_array($module->learning_outcomes ?? null) ? $module->learning_outcomes : []);
    if (! empty($learningOutcomes)) {
        $contentSections = array_values(array_filter(
            (array) $contentSections,
            fn ($section) => ! preg_match('/^\s*(intended\s+)?learning\s+(outcomes?|objectives?)\s*$/i', (string) ($section['heading'] ?? ''))
        ));
    }
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $module->title }} — {{ $module->version_name }}</title>

    <style>
        /* Module lesson viewer. Colours, type and radius come from
           partials.design-system; the student and instructor viewers share
           this exact stylesheet. */
        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font-family: var(--ds-font-sans);
        }

        a {
            color: inherit;
        }

        .lesson-shell {
            min-height: 100vh;
            min-height: 100dvh;
            display: grid;
            grid-template-columns: 288px minmax(0, 1fr);
        }

        /* ── side panel: module summary, versions, contents ─────────── */
        .lesson-nav {
            position: sticky;
            top: var(--ds-sticky-top, 0px);
            height: calc(100vh - var(--ds-sticky-top, 0px));
            height: calc(100dvh - var(--ds-sticky-top, 0px));
            min-width: 0;
            overflow-y: auto;
            overscroll-behavior: contain;
            padding: 16px 20px 24px;
            background: var(--surface);
            border-right: 1px solid var(--border);
        }

        .lesson-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-height: 32px;
            margin: 0 0 4px -10px;
            padding: 0 10px;
            border-radius: var(--radius-sm);
            color: var(--muted);
            font-size: .875rem;
            font-weight: 500;
            text-decoration: none;
            transition: background-color .12s ease, color .12s ease;
        }

        .lesson-back:hover {
            background: var(--surface2);
            color: var(--text);
        }

        .course-card,
        .side-card {
            padding: 16px 0;
            border-top: 1px solid var(--border);
        }

        .course-card {
            padding-top: 8px;
            border-top: 0;
        }

        .course-title {
            margin: 0;
            color: var(--text);
            font-size: 1rem;
            font-weight: 600;
            line-height: 1.35;
        }

        .course-card p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: .8125rem;
            line-height: 1.5;
        }

        .meta-list {
            margin-top: 10px;
            color: var(--ds-text-secondary);
            font-size: .8125rem;
            line-height: 1.5;
            font-variant-numeric: tabular-nums;
            overflow-wrap: anywhere;
        }

        .meta-list span:not(:last-child)::after {
            content: ",";
        }

        .side-card h3 {
            margin: 0 0 8px;
            color: var(--muted);
            font-size: .8125rem;
            font-weight: 600;
            line-height: 1.4;
        }

        .version-list,
        .topic-list {
            display: grid;
            gap: 2px;
        }

        .version-list a,
        .topic-list a {
            display: block;
            padding: 7px 10px;
            border-radius: var(--radius-sm);
            color: var(--muted);
            font-size: .8125rem;
            font-weight: 500;
            line-height: 1.4;
            text-decoration: none;
            overflow-wrap: break-word;
            transition: background-color .12s ease, color .12s ease;
        }

        .version-list a:hover,
        .topic-list a:hover {
            background: var(--surface2);
            color: var(--text);
        }

        .version-list a.is-current {
            border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
            background: var(--surface2);
            color: var(--text);
            box-shadow: inset 3px 0 0 var(--accent);
        }

        .version-list strong {
            display: block;
            font-weight: 500;
        }

        .version-list small {
            display: block;
            margin-top: 2px;
            color: var(--dim);
            font-size: .75rem;
        }

        .topic-list a {
            display: grid;
            grid-template-columns: 24px minmax(0, 1fr);
            gap: 4px;
            align-items: start;
        }

        .topic-list span {
            color: var(--dim);
            font-size: .75rem;
            font-weight: 600;
            line-height: 1.52;
            font-variant-numeric: tabular-nums;
        }

        .topic-list a:hover span {
            color: var(--muted);
        }

        /* ── reading column ─────────────────────────────────────────── */
        .lesson-content {
            min-width: 0;
            padding: 28px 32px 48px;
        }

        .lesson-shell.is-section-only {
            display: block;
            min-height: 0;
        }

        .lesson-shell.is-section-only .lesson-content {
            padding: 16px;
        }

        .lesson-content > * {
            max-width: 840px;
        }

        .lesson-hero {
            margin-bottom: 24px;
        }

        .outcome-details > summary h3 {
            color: var(--text);
            font-size: 1.125rem;
            font-weight: 600;
            line-height: 1.35;
        }

        .outcome-details .ds-outcomes-intro {
            margin: 10px 0 0;
            color: var(--muted);
            font-size: .875rem;
            line-height: 1.5;
        }

        .outcome-section {
            padding-bottom: 20px;
        }

        .outcome-list {
            margin: 12px 0 0;
            padding-left: 20px;
            color: var(--ds-text-secondary);
            font-size: .9375rem;
            line-height: 1.6;
        }

        .outcome-list li + li {
            margin-top: 4px;
        }

        .lesson-html {
            white-space: normal;
        }

        .lesson-hero p {
            max-width: 72ch;
            margin: 4px 0 0;
            color: var(--muted);
            font-size: .875rem;
            line-height: 1.55;
        }

        .lesson-hero .lesson-meta {
            color: var(--ds-text-secondary);
            font-size: .8125rem;
            font-variant-numeric: tabular-nums;
        }

        /* Marking a class module complete (DataSensei Updates 8). */
        .module-completion { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
        .module-completion p { margin: 0; max-width: 60ch; color: var(--ds-text-secondary); font-size: .9375rem; line-height: 1.55; }
        .module-completion .is-done { color: var(--ds-success-text); }
        .module-complete-btn { display: inline-flex; align-items: center; justify-content: center; min-height: 38px; padding: 0 16px; border: 1px solid var(--accent); border-radius: var(--radius-sm); background: var(--accent); color: #fff; font: 500 .875rem/1.2 var(--ds-font-sans); cursor: pointer; }
        .module-complete-btn:hover { background: var(--accent-hover); border-color: var(--accent-hover); }

        .lesson-section {
            margin-bottom: 20px;
            padding: 20px 24px 24px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            scroll-margin-top: calc(var(--ds-sticky-top, 0px) + 16px);
        }

        .lesson-section > h3 {
            margin: 0;
            color: var(--text);
            font-size: 1rem;
            font-weight: 600;
        }

        .lesson-section > .muted-note {
            margin-top: 4px;
        }

        .section-head {
            display: grid;
            grid-template-columns: 28px minmax(0, 1fr);
            gap: 12px;
            align-items: start;
            margin-bottom: 16px;
        }

        .section-head > span {
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--ds-border-strong);
            border-radius: var(--radius-sm);
            background: var(--surface2);
            color: var(--muted);
            font-size: .75rem;
            font-weight: 600;
            font-variant-numeric: tabular-nums;
        }

        .section-head h3 {
            margin: 0;
            padding-top: 2px;
            color: var(--text);
            font-size: 1.125rem;
            font-weight: 600;
            line-height: 1.35;
        }

        .section-head p {
            max-width: 72ch;
            margin: 4px 0 0;
            color: var(--muted);
            font-size: .875rem;
            line-height: 1.5;
        }

        .lesson-body {
            max-width: 75ch;
            color: var(--ds-text-secondary);
            font-size: .9375rem;
            line-height: 1.65;
            overflow-wrap: break-word;
        }

        /* ── code example ───────────────────────────────────────────── */
        .code-card {
            margin-top: 20px;
            background: var(--surface3);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        .code-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px 12px;
            min-height: 44px;
            padding: 6px 12px 6px 16px;
            border-bottom: 1px solid var(--border);
        }

        .code-title {
            color: var(--muted);
            font-size: .8125rem;
            font-weight: 500;
        }

        .try-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 32px;
            padding: 0 12px;
            border: 1px solid var(--ds-success-border);
            border-radius: var(--radius-sm);
            background: transparent;
            color: var(--ds-success-text);
            font: 500 .8125rem/1.2 var(--ds-font-sans);
            white-space: nowrap;
            text-decoration: none;
            cursor: pointer;
            transition: background-color .12s ease;
        }

        .try-btn:hover {
            background: var(--ds-success-soft);
        }

        .code-card pre {
            margin: 0;
            padding: 16px;
            overflow-x: auto;
        }

        .code-card code {
            color: var(--ds-text-secondary);
            font-family: var(--ds-font-mono);
            font-size: .8125rem;
            line-height: 1.65;
        }

        /* ── callouts ───────────────────────────────────────────────── */
        .panel,
        .activity,
        .scenario,
        .explanation,
        .tip {
            margin-top: 16px;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: .875rem;
            line-height: 1.6;
            overflow-wrap: break-word;
        }

        .panel {
            background: var(--surface3);
            color: var(--ds-text-secondary);
        }

        .panel-success {
            background: var(--ds-success-soft);
            border-color: var(--ds-success-border);
            color: #d1fae5;
        }

        .panel-warning {
            background: var(--ds-warning-soft);
            border-color: var(--ds-warning-border);
            color: #fef3c7;
        }

        .activity {
            background: var(--ds-accent-soft);
            border-color: var(--ds-accent-border);
            color: #dbeafe;
        }

        .activity p {
            margin: 4px 0 0;
        }

        .panel h4 {
            margin: 0 0 6px;
            color: inherit;
            font-size: .875rem;
            font-weight: 600;
        }

        .panel ul,
        .panel ol {
            margin: 0;
            padding-left: 20px;
        }

        .panel li {
            margin-bottom: 6px;
        }

        .panel li:last-child {
            margin-bottom: 0;
        }

        /* ── knowledge check ────────────────────────────────────────── */
        .quiz-list {
            display: grid;
        }

        .question-card + .question-card {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }

        .question-top {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 4px 12px;
            margin-bottom: 6px;
            color: var(--muted);
            font-size: .8125rem;
            font-weight: 500;
        }

        .question-top small {
            font-size: .75rem;
        }

        .question-card h4 {
            max-width: 75ch;
            margin: 0;
            color: var(--text);
            font-size: .9375rem;
            font-weight: 600;
            line-height: 1.5;
        }

        .scenario {
            background: var(--surface3);
            color: var(--ds-text-secondary);
        }

        .scenario p,
        .explanation p {
            margin: 4px 0 0;
        }

        .choices {
            display: grid;
            gap: 8px;
            margin-top: 14px;
        }

        .choice {
            display: grid;
            grid-template-columns: 24px minmax(0, 1fr);
            gap: 10px;
            align-items: start;
            padding: 10px 12px;
            border: 1px solid var(--ds-input-border);
            border-radius: var(--radius-sm);
            background: var(--surface3);
            color: var(--ds-text-secondary);
            font-size: .875rem;
            transition: background-color .12s ease, border-color .12s ease;
        }

        label.choice {
            cursor: pointer;
        }

        label.choice:hover {
            border-color: var(--border-hover);
            background: var(--surface2);
        }

        label.choice {
            position: relative;
        }

        /* The radio stays in the page for keyboard use; the row shows its state. */
        .choice input {
            position: absolute;
            width: 1px;
            height: 1px;
            margin: 0;
            opacity: 0;
            pointer-events: none;
        }

        .choice:has(input:focus-visible) {
            box-shadow: var(--ds-focus-ring);
        }

        .choice:has(input:checked) {
            border-color: var(--ds-accent-border);
            background: var(--ds-accent-soft);
            color: var(--text);
        }

        .choice-letter {
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--ds-border-strong);
            border-radius: var(--radius-xs);
            background: var(--surface2);
            color: var(--muted);
            font-size: .75rem;
            font-weight: 600;
        }

        .choice:has(input:checked) .choice-letter {
            border-color: var(--accent);
            background: var(--accent);
            color: #fff;
        }

        .choice p {
            margin: 0;
            padding-top: 2px;
            line-height: 1.5;
            overflow-wrap: break-word;
        }

        .choices .choice.is-correct {
            border-color: var(--ds-success-border);
            background: var(--ds-success-soft);
            color: #d1fae5;
            font-weight: 500;
        }

        .choices .choice.is-correct .choice-letter {
            border-color: var(--ds-success);
            background: var(--ds-success);
            color: #fff;
        }

        .choices .choice.is-wrong {
            border-color: var(--ds-danger-border);
            background: var(--ds-danger-soft);
            color: #fee2e2;
        }

        .choices .choice.is-wrong .choice-letter {
            border-color: var(--ds-danger);
            background: var(--ds-danger);
            color: #fff;
        }

        .check-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 38px;
            margin-top: 14px;
            padding: 0 16px;
            border: 1px solid var(--ds-border-strong);
            border-radius: var(--radius-sm);
            background: var(--surface2);
            color: var(--text);
            font: 500 .875rem/1.2 var(--ds-font-sans);
            cursor: pointer;
            transition: background-color .12s ease;
        }

        .check-btn:hover {
            background: var(--ds-surface-hover);
        }

        .result-box {
            display: none;
            margin-top: 12px;
            padding: 12px 16px;
            border: 1px solid transparent;
            border-radius: var(--radius-sm);
            font-size: .875rem;
            line-height: 1.55;
        }

        .result-box.correct {
            display: block;
            background: var(--ds-success-soft);
            border-color: var(--ds-success-border);
            color: #d1fae5;
        }

        .result-box.wrong {
            display: block;
            background: var(--ds-warning-soft);
            border-color: var(--ds-warning-border);
            color: #fef3c7;
        }

        .explanation {
            background: var(--ds-accent-soft);
            border-color: var(--ds-accent-border);
            color: #dbeafe;
        }

        .tip {
            background: var(--ds-success-soft);
            border-color: var(--ds-success-border);
            color: #d1fae5;
        }

        .muted-note {
            margin: 0;
            color: var(--muted);
            font-size: .875rem;
            line-height: 1.5;
        }

        @media (max-width: 1100px) {
            .lesson-shell {
                grid-template-columns: 256px minmax(0, 1fr);
            }

            .lesson-content {
                padding: 24px 24px 48px;
            }
        }

        /* The side panel stacks above the lesson; its lists scroll inside
           a short box so the lesson itself starts close to the top. */
        @media (max-width: 900px) {
            .lesson-shell {
                grid-template-columns: minmax(0, 1fr);
            }

            .lesson-nav {
                position: static;
                height: auto;
                overflow: visible;
                padding: 12px 20px 4px;
                border-right: 0;
                border-bottom: 1px solid var(--border);
            }

            .version-list,
            .topic-list {
                max-height: 264px;
                overflow-y: auto;
                overscroll-behavior: contain;
            }

            .lesson-content {
                padding: 24px 20px 40px;
            }
        }

        /* Tablet: versions and contents sit side by side under the summary. */
        @media (min-width: 641px) and (max-width: 900px) {
            .lesson-nav {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                column-gap: 32px;
                align-items: start;
            }

            .lesson-nav > .lesson-back {
                justify-self: start;
            }

            .lesson-nav > .lesson-back,
            .lesson-nav > .course-card,
            .lesson-nav > .side-card:nth-child(3):last-child {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 640px) {
            .lesson-nav {
                padding: 8px 16px 0;
            }

            .lesson-content {
                padding: 20px 16px 32px;
            }

            .lesson-section {
                padding: 16px;
            }

            .section-head {
                gap: 10px;
                margin-bottom: 12px;
            }

            .code-card pre {
                padding: 12px 14px;
            }

            .panel,
            .activity,
            .scenario,
            .explanation,
            .tip {
                padding: 12px 14px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }
        }
    </style>
  @include('partials.ui-polish')
    @include('partials.page-head', ['pageDescription' => 'Read the lesson material for this DataSensei module.'])
</head>
<body>
    {{-- sectionOnly: the admin editor's "Preview section" shows one section alone. --}}
    <div class="lesson-shell {{ ($sectionOnly ?? false) ? 'is-section-only' : '' }}">
        @unless ($sectionOnly ?? false)
        <aside class="lesson-nav">
            <a href="{{ $backRoute }}" class="lesson-back">← Back</a>

            <div class="course-card">
                <h2 class="course-title">{{ $module->title }}</h2>
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
                                href="{{ $versionRouteName ? route($versionRouteName, array_merge(['module' => $version], $versionRouteExtra)) : '#' }}"
                                class="{{ $version->id === $module->id ? 'is-current' : '' }}"
                            >
                                <strong>{{ $version->version_name }}</strong>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="side-card">
                <h3>Module Contents</h3>
                <nav class="topic-list">
                    @if (! empty($learningOutcomes))
                        <a href="#what-you-will-learn">
                            <span>&#10003;</span>
                            What You Will Learn
                        </a>
                    @endif
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
        @endunless

        <main class="lesson-content">
            @unless ($sectionOnly ?? false)
            <section class="lesson-hero">
                <h1 class="ds-page-title">{{ $module->title }}</h1>
                <p>
                    Read the explanation, inspect the example, follow the walkthrough,
                    review common mistakes, and answer the knowledge check.
                </p>
            </section>
            @endunless

            @if (! empty($learningOutcomes))
                <section class="lesson-section outcome-section" id="what-you-will-learn">
                    {{-- Closed by default; learners open it when they want (Updates 6). --}}
                    @include('partials.learning-outcomes-toggle', [
                        'outcomes' => $learningOutcomes,
                        'label' => 'What You Will Learn',
                        'heading' => 'h3',
                        'intro' => 'By the end of this module you should be able to:',
                        'listClass' => 'outcome-list',
                        'class' => 'outcome-details',
                    ])
                </section>
            @endif

            @forelse ($contentSections as $index => $section)
                @php
                    // The section as content blocks (DataSensei Updates 6): the
                    // same organised view for every role, whichever way the
                    // section was written. A first subheading is its subtitle.
                    $sectionBlocks = app(\App\Services\ModuleBlockConverter::class)->fromLibrarySection(is_array($section) ? $section : []);
                    $sectionSubtitle = null;
                    if (($sectionBlocks[0]['type'] ?? null) === 'subheading') {
                        $sectionSubtitle = $sectionBlocks[0]['text'];
                        array_shift($sectionBlocks);
                    }
                @endphp
                <section class="lesson-section" id="section-{{ $index + 1 }}">
                    <div class="section-head">
                        <span>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <div>
                            <h3>{{ $section['heading'] ?? 'Untitled Section' }}</h3>
                            @if (! empty($sectionSubtitle))
                                <p>{{ $sectionSubtitle }}</p>
                            @endif
                        </div>
                    </div>

                    @include('partials.module-blocks', ['blocks' => $sectionBlocks, 'isStudentView' => $isStudentView])
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

            @if (! empty($completion) && ! ($sectionOnly ?? false))
                <section class="lesson-section module-completion" id="module-completion">
                    @if ($completion['completed_at'])
                        <p class="is-done">You marked this module as complete on {{ $completion['completed_at']->format('M d, Y') }}. Your instructor can see it in your class progress.</p>
                    @else
                        <p>When you have read every section and answered the knowledge check, mark the module as complete. Your instructor sees it in your class progress.</p>
                        <form method="POST" action="{{ $completion['url'] }}">
                            @csrf
                            <button type="submit" class="module-complete-btn">Mark module as complete</button>
                        </form>
                    @endif
                </section>
            @endif
        </main>
    </div>

    <script>
        // The outcome list starts closed; the contents link opens it.
        document.querySelectorAll('a[href="#what-you-will-learn"]').forEach(function (link) {
            link.addEventListener('click', function () {
                var details = document.querySelector('#what-you-will-learn details');
                if (details) details.open = true;
            });
        });
    </script>
    <script>
        const ideUrl = @json($ideUrl);

        const sqlSandboxUrl = @json(Route::has('sql-sandbox.index') ? route('sql-sandbox.index') : url('/sql-sandbox'));

        // The module's SQL examples query these practice tables. The SQL sandbox
        // starts empty, so the tables travel with the example.
        const SQL_EXAMPLE_TABLES = {
            student_scores: `DROP TABLE IF EXISTS student_scores;
CREATE TABLE student_scores (student_id INTEGER PRIMARY KEY, name TEXT, department TEXT, score REAL);
INSERT INTO student_scores (student_id, name, department, score) VALUES
  (1, 'Ana', 'Data Science', 92), (2, 'Ben', 'Data Science', 85), (3, 'Carla', 'Statistics', 78),
  (4, 'Dino', 'Statistics', 74), (5, 'Ella', 'Computer Science', 88), (6, 'Farid', 'Computer Science', 81);`,
            fact_sales: `DROP TABLE IF EXISTS fact_sales;
DROP TABLE IF EXISTS dim_date;
CREATE TABLE dim_date (date_key INTEGER PRIMARY KEY, month_number INTEGER, month_name TEXT);
CREATE TABLE fact_sales (sale_id INTEGER PRIMARY KEY, date_key INTEGER, sales_amount REAL);
INSERT INTO dim_date (date_key, month_number, month_name) VALUES
  (20260105, 1, 'January'), (20260118, 1, 'January'), (20260207, 2, 'February'), (20260312, 3, 'March');
INSERT INTO fact_sales (sale_id, date_key, sales_amount) VALUES
  (1, 20260105, 1200.50), (2, 20260118, 830.00), (3, 20260207, 1575.25), (4, 20260312, 990.75), (5, 20260312, 410.00);`,
        };

        function isSqlExample(code) {
            const firstStatement = String(code || '').replace(/^\s*(?:--[^\n]*\n\s*)*/, '');
            return /^(SELECT|WITH|INSERT|UPDATE|DELETE|CREATE|DROP|ALTER)\b/i.test(firstStatement);
        }

        function tryInCompiler(code) {
            sessionStorage.setItem('datasensei_return_url', window.location.href);

            // SQL examples used to be sent to the Python compiler, where every
            // one of them ended in "SyntaxError: invalid syntax".
            if (isSqlExample(code)) {
                const setup = Object.keys(SQL_EXAMPLE_TABLES)
                    .filter(table => new RegExp('\\b' + table + '\\b', 'i').test(code))
                    .map(table => '-- Practice data for this example\n' + SQL_EXAMPLE_TABLES[table])
                    .join('\n\n');
                sessionStorage.setItem('datasensei_pending_sql_code', (setup ? setup + '\n\n' : '') + code);
                window.location.href = sqlSandboxUrl;
                return;
            }

            sessionStorage.setItem('datasensei_pending_code', code || '');
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
