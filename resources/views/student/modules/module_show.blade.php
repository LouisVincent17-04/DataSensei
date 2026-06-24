{{-- resources/views/student/modules/module_show.blade.php --}}

@include('student.shared.module_lesson_viewer', [
    'module' => $module,
    'contentSections' => $contentSections,
    'mcqQuestions' => $mcqQuestions,
    'relatedVersions' => $relatedVersions,
    'viewerRole' => 'Student',
    'backRoute' => route('student.modules.index'),
    'versionRouteName' => 'student.modules.show',
])
