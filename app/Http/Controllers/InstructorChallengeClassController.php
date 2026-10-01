<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Services\ClassChallengePractice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Share a challenge with classes for practice (DataSensei Updates 9). Used
 * from the Challenge Builder (the instructor's own challenges) and the
 * Challenge Pool (platform challenges on the University Student level).
 * There is no due date, status or separate title: graded class work is an
 * Assessment.
 */
class InstructorChallengeClassController extends Controller
{
    public function update(Request $request, Challenge $challenge, ClassChallengePractice $practice): RedirectResponse
    {
        $instructorId = (int) Auth::id();
        abort_unless($practice->canShare($challenge, $instructorId), 404);

        $data = $request->validate([
            'class_ids' => ['nullable', 'array'],
            'class_ids.*' => ['integer'],
        ]);

        $changes = $practice->sync($challenge, $data['class_ids'] ?? [], $instructorId);

        $parts = [];
        if ($changes['added'] !== []) {
            $parts[] = 'Shared with '.implode(', ', $changes['added']).'.';
        }
        if ($changes['removed'] !== []) {
            $parts[] = 'No longer shared with '.implode(', ', $changes['removed']).'.';
        }

        $previous = strtok((string) url()->previous(), '#');

        return redirect()->to(($previous ?: route('instructor.challenge-builder.index')).'#classes')
            ->with('success', $parts === [] ? 'No change to the classes.' : implode(' ', $parts));
    }
}
