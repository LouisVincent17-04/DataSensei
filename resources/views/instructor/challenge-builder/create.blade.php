@include('instructor.challenge-builder._editor', [
  'type' => $type,
  'challenge' => $challenge,
  'questions' => $questions,
  'hasHistory' => false,
  'pageTitle' => $type === 'coding' ? 'New coding challenge' : 'New quiz challenge',
  'pageIntro' => $type === 'coding'
    ? 'Write one or more problems with test cases. Check your reference solution against the cases before saving; students are graded on exact output.'
    : 'Write the questions and answer choices. The preview on the right shows what students will see.',
  'formAction' => route('instructor.challenge-builder.store', ['type' => $type]),
  'formMethod' => 'POST',
  'submitLabel' => 'Save challenge',
])
