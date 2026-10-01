{{-- resources/views/student/modules/module_show.blade.php --}}

@include('student.shared.module_lesson_viewer', [
    'module' => $module,
    'contentSections' => $contentSections,
    'mcqQuestions' => $mcqQuestions,
    'relatedVersions' => $relatedVersions,
    'learningOutcomes' => $learningOutcomes ?? [],
    'viewerRole' => 'Student',
    'backRoute' => $backRoute ?? route('modules.index'),
    'versionRouteName' => 'student.modules.show',
    'versionRouteExtra' => ($versionClassId ?? null) ? ['class' => $versionClassId] : [],
    'sectionOnly' => $sectionOnly ?? false,
    'completion' => $completion ?? null,
])
