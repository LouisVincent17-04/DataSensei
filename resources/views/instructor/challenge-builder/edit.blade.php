@include('instructor.challenge-builder._editor', [
  'type' => $type,
  'challenge' => $challenge,
  'questions' => $questions,
  'hasHistory' => $hasHistory,
  'pageTitle' => 'Edit ' . ($type === 'coding' ? 'coding challenge' : 'quiz challenge'),
  'pageIntro' => 'Changes apply to every class this challenge is given to. Once students have worked on it, the graded content is frozen.',
  'formAction' => route('instructor.challenge-builder.update', $challenge),
  'formMethod' => 'PUT',
  'submitLabel' => 'Save changes',
])
