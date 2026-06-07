{{-- resources/views/instructor/modules/module_show.blade.php --}}

@include('instructor.shared.module_netacad_viewer', [
    'module' => $module,
    'contentSections' => $contentSections,
    'mcqQuestions' => $mcqQuestions,
    'relatedVersions' => $relatedVersions,
    'viewerRole' => 'Instructor',
    'backRoute' => route('modules.module-library.index'),
    'versionRouteName' => 'modules.module-library.show',
])
