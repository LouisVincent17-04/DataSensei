<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\CodingQuestion;
use App\Models\CodingQuestionAttempt;
use App\Models\CodingSubmission;
use App\Models\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;

class CodingQuizController extends Controller
{
    private bool $isWindows;

    public function __construct()
    {
        $this->isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE HELPER — always use this to compute elapsed/remaining.
    //
    // FIX: Carbon::diffInSeconds() is UNSIGNED (absolute). When APP_TIMEZONE
    // or the DB connection timezone differs from UTC, started_at can appear
    // to be in the future relative to now(), making diffInSeconds return e.g.
    // 28 800 with the wrong sign context — and then:
    //   remaining = 600 - (-28800) = 29 400 s ≈ 490 minutes   ← the bug
    //
    // Raw Unix timestamps are always UTC-based integers, so subtraction is
    // correctly signed and timezone-safe everywhere.
    // ─────────────────────────────────────────────────────────────────────────
    private function elapsedSeconds(CodingQuestionAttempt $attempt): int
    {
        return max(0, now()->timestamp - $attempt->started_at->timestamp);
    }

    private function remainingSeconds(CodingQuestionAttempt $attempt, CodingQuestion $question): int
    {
        return max(0, $question->time_limit_seconds - $this->elapsedSeconds($attempt));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SHOW QUIZ
    // GET /challenges/coding/{slug}/challenge/{challenge}
    // ─────────────────────────────────────────────────────────────────────────
    public function show(string $slug, Challenge|int $challenge)
    {
        if (is_int($challenge)) {
            $challenge = Challenge::findOrFail($challenge);
        }

        $challenge->load(['codingQuestions.visibleTestCases']);

        abort_if($challenge->codingQuestions->isEmpty(), 404, 'No coding questions found.');

        $userId = Auth::id();

        // Best prior submission per question.
        // keyBy() keeps the LAST item per key, so we order ascending by quality
        // (worst → best) so the best submission ends up as the survivor:
        //   1. tests_passed ASC  — fewer passing tests first
        //   2. passed status last — 'passed' rows overwrite 'failed'/'error' rows
        $priorSubmissions = CodingSubmission::where('user_id', $userId)
            ->whereIn('coding_question_id', $challenge->codingQuestions->pluck('id'))
            ->orderBy('tests_passed')
            ->orderByRaw("CASE WHEN status = 'passed' THEN 1 ELSE 0 END")
            ->get()
            ->keyBy('coding_question_id');

        // ── Build attempt data ────────────────────────────────────────────
        // Rule:
        //   • Already passed      → state='done',   no attempt needed, no timer
        //   • Existing DB attempt → state='active',  restore it (clock was already running)
        //   • First unsolved      → state='active',  create attempt NOW (stamp started_at)
        //   • Later unsolved      → state='locked',  clock starts lazily via start()
        $attempts            = [];
        $createdFirstAttempt = false;

        foreach ($challenge->codingQuestions as $question) {
            $alreadyPassed = isset($priorSubmissions[$question->id])
                && $priorSubmissions[$question->id]->status === 'passed';

            if ($alreadyPassed) {
                $attempts[$question->id] = [
                    'state'             => 'done',
                    'remaining_seconds' => $question->time_limit_seconds,
                    'expired'           => false,
                    'started_at'        => null,
                    'has_attempt'       => false,
                ];
                continue;
            }

            $existing = CodingQuestionAttempt::where('user_id', $userId)
                ->where('coding_question_id', $question->id)
                ->first();

            if ($existing) {
                // Clock already running — FIX: use timestamp arithmetic
                $remaining = $this->remainingSeconds($existing, $question);

                if ($remaining <= 0 && !$existing->expired) {
                    $existing->update(['expired' => true]);
                }

                $attempts[$question->id] = [
                    'state'             => 'active',
                    'remaining_seconds' => $remaining,
                    'expired'           => $remaining <= 0,
                    'started_at'        => $existing->started_at->toIso8601String(),
                    'has_attempt'       => true,
                ];

            } elseif (!$createdFirstAttempt) {
                // First unsolved question — stamp the clock now
                $attempt = CodingQuestionAttempt::create([
                    'user_id'            => $userId,
                    'coding_question_id' => $question->id,
                    'started_at'         => now(),
                    'expired'            => false,
                ]);

                $attempts[$question->id] = [
                    'state'             => 'active',
                    'remaining_seconds' => $question->time_limit_seconds, // full limit, just started
                    'expired'           => false,
                    'started_at'        => $attempt->started_at->toIso8601String(),
                    'has_attempt'       => true,
                ];

                $createdFirstAttempt = true;

            } else {
                // Not yet visited — show full limit in UI but DO NOT start the DB clock
                $attempts[$question->id] = [
                    'state'             => 'locked',
                    'remaining_seconds' => $question->time_limit_seconds,
                    'expired'           => false,
                    'started_at'        => null,
                    'has_attempt'       => false,
                ];
            }
        }

        // Compute the index of the first 'active' question for the blade's ACTIVE_IDX
        $activeIdx = null;
        foreach ($challenge->codingQuestions as $i => $question) {
            if (($attempts[$question->id]['state'] ?? 'locked') === 'active') {
                $activeIdx = $i;
                break;
            }
        }

        return view('student.coding-challenge-quiz', compact(
            'slug', 'challenge', 'priorSubmissions', 'attempts', 'activeIdx'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // START — lazy clock start when user first navigates to a question
    // POST /challenges/coding/{slug}/challenge/{challenge}/start/{question}
    // ─────────────────────────────────────────────────────────────────────────
    public function start(string $slug, Challenge $challenge, CodingQuestion $question)
    {
        $userId = Auth::id();

        // Already passed — no timer needed
        $alreadyPassed = CodingSubmission::where('user_id', $userId)
            ->where('coding_question_id', $question->id)
            ->where('status', 'passed')
            ->exists();

        if ($alreadyPassed) {
            return response()->json([
                'remaining_seconds' => $question->time_limit_seconds,
                'expired'           => false,
            ]);
        }

        // firstOrCreate is safe against race conditions / double-clicks
        $attempt = CodingQuestionAttempt::firstOrCreate(
            [
                'user_id'            => $userId,
                'coding_question_id' => $question->id,
            ],
            [
                'started_at' => now(),
                'expired'    => false,
            ]
        );

        // FIX: use timestamp arithmetic, never diffInSeconds
        $remaining = $this->remainingSeconds($attempt, $question);

        if ($remaining <= 0 && !$attempt->expired) {
            $attempt->update(['expired' => true]);
        }

        return response()->json([
            'remaining_seconds' => $remaining,
            'expired'           => $remaining <= 0,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PING — client calls this every 30 s to get server-authoritative remaining
    // GET /challenges/coding/{slug}/challenge/{challenge}/ping/{question}
    // ─────────────────────────────────────────────────────────────────────────
    public function ping(string $slug, Challenge $challenge, CodingQuestion $question)
    {
        $attempt = CodingQuestionAttempt::where('user_id', Auth::id())
            ->where('coding_question_id', $question->id)
            ->first();

        if (!$attempt) {
            return response()->json(['remaining_seconds' => 0, 'expired' => true]);
        }

        // FIX: use timestamp arithmetic, never diffInSeconds
        $remaining = $this->remainingSeconds($attempt, $question);

        if ($remaining <= 0 && !$attempt->expired) {
            $attempt->update(['expired' => true]);
        }

        return response()->json([
            'remaining_seconds' => $remaining,
            'expired'           => $remaining <= 0,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // RUN ONLY — free execution, no submission, no XP
    // POST /challenges/coding/{slug}/challenge/{challenge}/run/{question}
    // ─────────────────────────────────────────────────────────────────────────
    public function run(Request $request, string $slug, Challenge $challenge, CodingQuestion $question)
    {
        $request->validate([
            'code'  => 'required|string|max:20000',
            'input' => 'nullable|string|max:5000',
        ]);

        $result = $this->execute($request->input('code'), $request->input('input') ?? '');

        return response()->json([
            'output' => $result['stdout'],
            'stderr' => $result['stderr'],
            'image'  => $result['image'],
            'status' => $result['failed'] ? 'error' : 'ok',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SUBMIT — enforces server-side timer, runs test cases, awards XP
    // POST /challenges/coding/{slug}/challenge/{challenge}/submit/{question}
    // ─────────────────────────────────────────────────────────────────────────
    public function submit(Request $request, string $slug, Challenge $challenge, CodingQuestion $question)
    {
        $request->validate([
            'code' => 'required|string|max:20000',
        ]);

        $userId = Auth::id();

        // ── Anti-cheat: verify server-side timer ──────────────────────────
        $attempt = CodingQuestionAttempt::where('user_id', $userId)
            ->where('coding_question_id', $question->id)
            ->first();

        if (!$attempt) {
            return response()->json([
                'error'   => 'No attempt record found. Please reload the page.',
                'expired' => true,
            ], 403);
        }

        // FIX: use timestamp arithmetic, never diffInSeconds
        $elapsedSeconds = $this->elapsedSeconds($attempt);
        $timeTaken      = $elapsedSeconds;
        $timeExpired    = $elapsedSeconds >= $question->time_limit_seconds;

        if ($timeExpired && !$attempt->expired) {
            $attempt->update(['expired' => true]);
        }

        $alreadyPassed = CodingSubmission::where('user_id', $userId)
            ->where('coding_question_id', $question->id)
            ->where('status', 'passed')
            ->exists();

        if ($alreadyPassed) {
            return response()->json(['error' => 'Already solved.'], 422);
        }

        $question->load('testCases');

        if ($timeExpired) {
            CodingSubmission::create([
                'user_id'            => $userId,
                'coding_question_id' => $question->id,
                'code'               => $request->input('code'),
                'language'           => $question->language,
                'status'             => 'failed',
                'tests_passed'       => 0,
                'tests_total'        => $question->testCases->count(),
                'xp_earned'          => 0,
                'time_taken_seconds' => $timeTaken,
                'test_results'       => [],
                'error_message'      => 'Time limit exceeded.',
            ]);

            return response()->json([
                'status'             => 'expired',
                'tests_passed'       => 0,
                'tests_total'        => $question->testCases->count(),
                'xp_earned'          => 0,
                'results'            => [],
                'expired'            => true,
                'challenge_complete' => false,
            ]);
        }

        // ── Grade ─────────────────────────────────────────────────────────
        $results = $question->testCases->map(
            fn(TestCase $tc) => $this->runSingle($request->input('code'), $tc)
        )->toArray();

        $passed = collect($results)->where('passed', true)->count();
        $total  = $question->testCases->count();

        $status = match(true) {
            $passed === $total                                                => 'passed',
            collect($results)->contains('status', 'error') && $passed === 0  => 'error',
            default                                                           => 'failed',
        };

        $xp = 0;
        if ($passed > 0) {
            $rawXp  = (int) round($question->base_xp * ($passed / $total));
            $bonus  = ($passed === $total && $timeTaken < $question->time_limit_seconds * 0.5) ? 1.2 : 1.0;
            $xp     = (int) round($rawXp * $bonus);
        }

        $submission = CodingSubmission::create([
            'user_id'            => $userId,
            'coding_question_id' => $question->id,
            'code'               => $request->input('code'),
            'language'           => $question->language,
            'status'             => $status,
            'tests_passed'       => $passed,
            'tests_total'        => $total,
            'xp_earned'          => $xp,
            'time_taken_seconds' => $timeTaken,
            'test_results'       => $results,
            'error_message'      => collect($results)->firstWhere('status', 'error')['stderr'] ?? null,
        ]);

        $previousBest = CodingSubmission::where('user_id', $userId)
            ->where('coding_question_id', $question->id)
            ->where('id', '!=', $submission->id)
            ->max('xp_earned') ?? 0;

        if ($xp > $previousBest) {
            Auth::user()->increment('xp', $xp - $previousBest);
        }

        $questionIds = $challenge->codingQuestions()->pluck('id');

        $passedCount = CodingSubmission::where('user_id', $userId)
            ->whereIn('coding_question_id', $questionIds)
            ->where('status', 'passed')
            ->distinct('coding_question_id')
            ->count('coding_question_id');

        $challengeComplete = $passedCount >= $questionIds->count();

        return response()->json([
            'submission_id'      => $submission->id,
            'status'             => $status,
            'tests_passed'       => $passed,
            'tests_total'        => $total,
            'xp_earned'          => $xp,
            'results'            => $results,
            'expired'            => false,
            'remaining_seconds'  => max(0, $question->time_limit_seconds - $elapsedSeconds),
            'challenge_complete' => $challengeComplete,
            'redirect_url'       => $challengeComplete ? route('challenges.coding.map', $slug) : null,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Run code against one TestCase
    // ─────────────────────────────────────────────────────────────────────────
    private function runSingle(string $code, TestCase $tc): array
    {
        $base = [
            'test_case_id' => $tc->id,
            'input'        => $tc->input,
            'expected'     => $tc->expected_output,
            'actual'       => null,
            'passed'       => false,
            'status'       => 'error',
            'stderr'       => null,
            'is_hidden'    => $tc->is_hidden,
        ];

        if (config('app.env') === 'production') {
            return array_merge($base, ['stderr' => 'Sandboxed execution not configured.']);
        }

        $result   = $this->execute($code, $tc->input ?? '');
        $actual   = rtrim($result['stdout']);
        $expected = rtrim($tc->expected_output);
        $passed   = ($actual === $expected);

        return array_merge($base, [
            'actual' => (!$tc->is_hidden || $passed) ? $actual : null,
            'passed' => $passed,
            'status' => $result['failed'] ? 'error' : ($passed ? 'passed' : 'failed'),
            'stderr' => $result['stderr'] ?: null,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Execute Python — Matplotlib-safe, Windows-compatible
    // ─────────────────────────────────────────────────────────────────────────
    private function execute(string $code, ?string $stdin = ''): array
    {
        $stdin   = $stdin ?? '';
        $tmpDir  = sys_get_temp_dir();
        $pyFile  = tempnam($tmpDir, 'ds_') . '.py';
        $inFile  = tempnam($tmpDir, 'ds_in_');
        $imgFile = $tmpDir . DIRECTORY_SEPARATOR . 'ds_plot_' . uniqid() . '.png';

        $imgEscaped = addslashes($imgFile);
        $preamble   = <<<PREAMBLE
import os as _os, sys as _sys
_os.environ['MPLBACKEND'] = 'Agg'
try:
    import matplotlib as _mpl
    _mpl.use('Agg')
    import matplotlib.pyplot as _plt
    def _patched_show(*a, **kw):
        _plt.savefig(r'{$imgEscaped}', bbox_inches='tight', dpi=100)
        _plt.close('all')
    _plt.show = _patched_show
except ImportError:
    pass

PREAMBLE;

        file_put_contents($pyFile, $preamble . $code);
        file_put_contents($inFile, $stdin);

        $python = $this->isWindows ? 'python' : 'python3';

        try {
            if ($this->isWindows) {
                $pyEsc   = '"' . str_replace('/', '\\', $pyFile) . '"';
                $inEsc   = '"' . str_replace('/', '\\', $inFile) . '"';
                $cmd     = "cmd /c {$python} {$pyEsc} < {$inEsc} 2>&1";
                $lines   = [];
                $retCode = 0;
                exec($cmd, $lines, $retCode);
                $raw = implode("\n", $lines);

                $isError = $retCode !== 0
                    || str_contains($raw, 'Traceback (most recent call last)')
                    || preg_match('/\w+Error:/i', $raw);

                $stdout = $isError ? '' : $raw;
                $stderr = $isError ? $raw : null;
                $failed = (bool) $isError;
            } else {
                $proc   = Process::timeout(15)
                    ->input(file_get_contents($inFile))
                    ->run([$python, $pyFile]);
                $stdout = $proc->output();
                $stderr = $proc->errorOutput() ?: null;
                $failed = $proc->failed();
            }

            $image = null;
            if (file_exists($imgFile) && filesize($imgFile) > 0) {
                $image = 'data:image/png;base64,' . base64_encode(file_get_contents($imgFile));
            }

            return compact('stdout', 'stderr', 'image', 'failed');

        } catch (\Throwable $e) {
            return ['stdout' => '', 'stderr' => $e->getMessage(), 'image' => null, 'failed' => true];
        } finally {
            @unlink($pyFile);
            @unlink($inFile);
            @unlink($imgFile);
        }
    }
}