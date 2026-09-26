<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminContentController;
use App\Http\Controllers\AdminAssessmentContentController;
use App\Http\Controllers\AdminMcqChallengeController;
use App\Http\Controllers\AdminCodingChallengeController;
use App\Http\Controllers\AdminChallengeMapController;
use App\Http\Controllers\AdminLessonController;
use App\Http\Controllers\AdminPublicModuleController;
use App\Http\Controllers\InstructorChallengeBuilderController;
use App\Http\Controllers\InstructorClassChallengeController;
use App\Http\Controllers\AdminModuleContentController;
use App\Http\Controllers\AdminGamificationController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdvancedTopicRecommendationController;
use App\Http\Controllers\AntiCheatEventController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChallengesController;
use App\Http\Controllers\ClassStudentController;
use App\Http\Controllers\CodeReviewController;
use App\Http\Controllers\CodingQuizController;
use App\Http\Controllers\IdeController;
use App\Http\Controllers\HybridMlApiController;
use App\Http\Controllers\InstructorMlDashboardController;
use App\Http\Controllers\InstitutionAdminController;
use App\Http\Controllers\InstitutionManagementController;
use App\Http\Controllers\InstructorAnalyticsController;
use App\Http\Controllers\InstructorAntiCheatController;
use App\Http\Controllers\InstructorAntiCheatEventController;
use App\Http\Controllers\InstructorApplicationController;
use App\Http\Controllers\InstructorAssignmentController;
use App\Http\Controllers\InstructorAssessmentController;
use App\Http\Controllers\InstructorAtRiskController;
use App\Http\Controllers\InstructorChallengePoolController;
use App\Http\Controllers\InstructorCompetencyController;
use App\Http\Controllers\InstructorClassController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\InstructorMasteryController;
use App\Http\Controllers\InstructorReportController;
use App\Http\Controllers\InstructorSubmissionController;
use App\Http\Controllers\InstructorTosController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ModuleLibraryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PasswordResetOtpController;
use App\Http\Controllers\SqlSandboxController;
use App\Http\Controllers\StudentAnalyticsController;
use App\Http\Controllers\StudentAssignmentController;
use App\Http\Controllers\StudentAssessmentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentCompetencyController;
use App\Http\Controllers\StudentDataToolkitController;
use App\Http\Controllers\StudentModelDevelopmentController;
use App\Http\Controllers\StudentGamificationController;
use App\Http\Controllers\StudentModuleLibraryController;
use App\Http\Controllers\SuperAdminAnalyticsController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'home'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:registration')
        ->name('register');

    Route::get('/forgot-password', [PasswordResetOtpController::class, 'showRequestForm'])
        ->name('password.request');
    Route::post('/forgot-password', [PasswordResetOtpController::class, 'sendOtp'])
        ->middleware('throttle:password-otp-request')
        ->name('password.otp.send');
    Route::get('/forgot-password/verify', [PasswordResetOtpController::class, 'showVerifyForm'])
        ->name('password.otp.verify.form');
    Route::post('/forgot-password/verify', [PasswordResetOtpController::class, 'verifyOtp'])
        ->middleware('throttle:password-otp-verify')
        ->name('password.otp.verify');
    Route::get('/reset-password', [PasswordResetOtpController::class, 'showResetForm'])
        ->name('password.reset.form');
    Route::post('/reset-password', [PasswordResetOtpController::class, 'resetPassword'])
        ->middleware('throttle:password-otp-reset')
        ->name('password.otp.reset');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('/profile/institution/apply', [ProfileController::class, 'applyAsInstructor'])->name('profile.institution.apply');
    Route::delete('/profile/delete', [ProfileController::class, 'deleteAccount'])->name('profile.delete');

    Route::get('/change-password', fn () => redirect()->route('profile', ['tab' => 'security']))->name('change-password');
    Route::post('/session/activity', fn () => response()->noContent())->name('session.activity');
});

Route::middleware(['auth', 'active', 'student'])->group(function () {
    Route::get('/student/dashboard', [StudentController::class, 'dashboard'])->name('studentDashboard');

    Route::prefix('student/notifications')->name('student.notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/feed', [NotificationController::class, 'feed'])->name('feed');
        Route::get('/count', [NotificationController::class, 'unreadCount'])->name('count');
        Route::get('/{notification}/open', [NotificationController::class, 'open'])->name('open');
        Route::patch('/{notification}/read', [NotificationController::class, 'markRead'])->name('read');
        Route::patch('/{notification}/unread', [NotificationController::class, 'markUnread'])->name('unread');
        Route::patch('/read-all', [NotificationController::class, 'markAllRead'])->name('read-all');
        Route::delete('/clear-read', [NotificationController::class, 'clearRead'])->name('clear-read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    Route::get('/student/modules', [StudentModuleLibraryController::class, 'index'])->name('student.modules.index');
    Route::get('/student/modules/{module}', [StudentModuleLibraryController::class, 'show'])->name('student.modules.show');

    Route::get('/student/analytics', [StudentAnalyticsController::class, 'index'])->name('student.analytics.index');
    Route::get('/student/competencies', [StudentCompetencyController::class, 'index'])->name('student.competencies.index');
    Route::get('/student/data-toolkit', [StudentDataToolkitController::class, 'index'])->name('student.data-toolkit.index');
    Route::post('/student/data-toolkit/upload', [StudentDataToolkitController::class, 'upload'])->middleware('throttle:ml-dataset-upload')->name('student.data-toolkit.upload');
    Route::get('/student/data-toolkit/{dataset}', [StudentDataToolkitController::class, 'show'])->name('student.data-toolkit.show');
    Route::get('/student/data-toolkit/{dataset}/rows', [StudentDataToolkitController::class, 'rows'])->name('student.data-toolkit.rows');
    Route::post('/student/data-toolkit/{dataset}/objective', [StudentDataToolkitController::class, 'objective'])->name('student.data-toolkit.objective');
    Route::post('/student/data-toolkit/{dataset}/clean', [StudentDataToolkitController::class, 'clean'])->name('student.data-toolkit.clean');
    Route::post('/student/data-toolkit/{dataset}/outliers', [StudentDataToolkitController::class, 'outliers'])->name('student.data-toolkit.outliers');
    Route::post('/student/data-toolkit/{dataset}/feature', [StudentDataToolkitController::class, 'feature'])->name('student.data-toolkit.feature');
    Route::post('/student/data-toolkit/{dataset}/analyze', [StudentDataToolkitController::class, 'analyze'])->name('student.data-toolkit.analyze');
    Route::get('/student/data-toolkit/{dataset}/report', [StudentDataToolkitController::class, 'report'])->name('student.data-toolkit.report');

    Route::prefix('student/model-development')->name('student.model-development.')->group(function () {
        Route::get('/', [StudentModelDevelopmentController::class, 'index'])->name('index');
        Route::post('/datasets/upload', [StudentModelDevelopmentController::class, 'uploadDataset'])->middleware('throttle:ml-dataset-upload')->name('datasets.upload');
        Route::get('/system-datasets/{dataset}', [StudentModelDevelopmentController::class, 'showSystemDataset'])->name('system-datasets.show');
        Route::get('/system-datasets/{dataset}/download', [StudentModelDevelopmentController::class, 'downloadSystemDataset'])->name('system-datasets.download');
        Route::get('/user-datasets/{dataset}', [StudentModelDevelopmentController::class, 'showUserDataset'])->name('user-datasets.show');
        Route::get('/user-datasets/{dataset}/download', [StudentModelDevelopmentController::class, 'downloadUserDataset'])->name('user-datasets.download');
        Route::delete('/user-datasets/{dataset}', [StudentModelDevelopmentController::class, 'destroyUserDataset'])->name('user-datasets.destroy');
        Route::get('/wizard/start', [StudentModelDevelopmentController::class, 'wizard'])->name('wizard');
        Route::post('/training', [StudentModelDevelopmentController::class, 'train'])->middleware('throttle:ml-training')->name('training.store');
        Route::get('/training/{trainingJob}', [StudentModelDevelopmentController::class, 'showTrainingJob'])->name('training.show');
        Route::get('/training/{trainingJob}/status', [StudentModelDevelopmentController::class, 'trainingStatus'])->name('training.status');
        Route::get('/models/{model}', [StudentModelDevelopmentController::class, 'showModel'])->name('models.show');
        Route::delete('/models/{model}', [StudentModelDevelopmentController::class, 'destroyModel'])->name('models.destroy');
        Route::post('/models/{model}/versions/{version}/activate', [StudentModelDevelopmentController::class, 'rollbackModel'])->name('models.versions.activate');
        Route::post('/models/{model}/predict', [StudentModelDevelopmentController::class, 'predict'])->middleware('throttle:ml-prediction')->name('models.predict');
        Route::get('/model-versions/{version}/visualizations/{chart}', [StudentModelDevelopmentController::class, 'visualization'])->name('visualizations.show');
        Route::get('/models/{model}/report', [StudentModelDevelopmentController::class, 'report'])->name('models.report');
    });

    Route::prefix('api/student/ml')->name('api.student.ml.')->group(function () {
        Route::get('/datasets', [HybridMlApiController::class, 'datasets'])->name('datasets.index');
        Route::post('/datasets/validate', [HybridMlApiController::class, 'validateDataset'])->middleware('throttle:ml-dataset-upload')->name('datasets.validate');
        Route::post('/datasets/upload', [HybridMlApiController::class, 'upload'])->middleware('throttle:ml-dataset-upload')->name('datasets.upload');
        Route::get('/datasets/{type}/{id}/quality', [HybridMlApiController::class, 'quality'])->name('datasets.quality');
        Route::post('/training-jobs', [HybridMlApiController::class, 'createTraining'])->middleware('throttle:ml-training')->name('training.store');
        Route::get('/training-jobs/{trainingJob}', [HybridMlApiController::class, 'training'])->name('training.show');
        Route::get('/models/{model}', [HybridMlApiController::class, 'model'])->name('models.show');
        Route::delete('/models/{model}', [HybridMlApiController::class, 'destroyModel'])->name('models.destroy');
        Route::post('/models/{model}/versions/{version}/activate', [HybridMlApiController::class, 'activateVersion'])->name('models.versions.activate');
        Route::post('/models/{model}/predictions', [HybridMlApiController::class, 'predict'])->middleware('throttle:ml-prediction')->name('models.predict');
        Route::get('/model-versions/{version}/visualizations/{chart}', [HybridMlApiController::class, 'visualization'])->name('visualizations.show');
    });
    Route::get('/student/achievements', [StudentGamificationController::class, 'achievements'])->name('student.achievements.index');
    Route::get('/student/leaderboard', [StudentGamificationController::class, 'leaderboard'])->name('student.leaderboard.index');
    Route::get('/student/advanced-topics', [AdvancedTopicRecommendationController::class, 'index'])->name('student.advanced-topics.index');



    Route::prefix('student/assessments')->name('student.assessments.')->group(function () {
        Route::get('/', [StudentAssessmentController::class, 'index'])->name('index');
        Route::get('/{assessment}', [StudentAssessmentController::class, 'show'])->name('show');
        Route::post('/{assessment}/start', [StudentAssessmentController::class, 'start'])->name('start');
        Route::get('/{assessment}/attempt/{submission}', [StudentAssessmentController::class, 'take'])->name('take');
        Route::post('/{assessment}/attempt/{submission}/autosave', [StudentAssessmentController::class, 'autosave'])
            ->middleware('throttle:120,1')
            ->name('autosave');
        Route::post('/{assessment}/attempt/{submission}/submit', [StudentAssessmentController::class, 'submit'])->name('submit');
        Route::get('/{assessment}/attempt/{submission}/result', [StudentAssessmentController::class, 'result'])->name('result');
    });

    Route::prefix('student/assignments')->name('student.assignments.')->group(function () {
        Route::get('/', [StudentAssignmentController::class, 'index'])->name('index');
        Route::get('/{assignment}', [StudentAssignmentController::class, 'show'])->name('show');
        Route::post('/{assignment}/start', [StudentAssignmentController::class, 'start'])->name('start');
        Route::get('/{assignment}/attempt/{submission}', [StudentAssignmentController::class, 'take'])->name('take');
        Route::post('/{assignment}/attempt/{submission}/autosave', [StudentAssignmentController::class, 'autosave'])
            ->middleware('throttle:120,1')
            ->name('autosave');
        Route::post('/{assignment}/attempt/{submission}/submit', [StudentAssignmentController::class, 'submit'])->name('submit');
        Route::get('/{assignment}/attempt/{submission}/result', [StudentAssignmentController::class, 'result'])->name('result');
    });

    Route::prefix('student/submissions')->name('student.submissions.')->group(function () {
        Route::get('/', [StudentAssignmentController::class, 'submissions'])->name('index');
        Route::get('/{submission}', [StudentAssignmentController::class, 'submissionResult'])->name('show');
    });

    Route::post('/anti-cheat/events', [AntiCheatEventController::class, 'store'])
        ->middleware('throttle:anti-cheat-event')
        ->name('anti-cheat.events.store');

    Route::get('/ide', [IdeController::class, 'index'])->name('ide.index');
    Route::post('/ide/workspace', [IdeController::class, 'initializeWorkspace'])->name('ide.workspace.initialize');
    Route::get('/ide/tree', [IdeController::class, 'tree'])->name('ide.tree');
    Route::post('/ide/nodes', [IdeController::class, 'storeNode'])->name('ide.nodes.store');
    Route::put('/ide/nodes/{node}', [IdeController::class, 'updateNode'])->name('ide.nodes.update');
    Route::patch('/ide/nodes/{node}/rename', [IdeController::class, 'renameNode'])->name('ide.nodes.rename');
    Route::patch('/ide/nodes/{node}/save', [IdeController::class, 'saveContent'])->name('ide.nodes.save');
    Route::patch('/ide/nodes/{node}/move', [IdeController::class, 'moveNode'])->name('ide.nodes.move');
    Route::delete('/ide/nodes/{node}', [IdeController::class, 'deleteNode'])->name('ide.nodes.delete');
    Route::post('/ide/nodes/{node}/run', [IdeController::class, 'runNode'])
        ->middleware('throttle:python-execution')
        ->name('ide.nodes.run');

    Route::get('/challenges', [ChallengesController::class, 'index'])->name('challenges');
    Route::get('/challenges/map/{slug}', [ChallengesController::class, 'map'])->name('challenges.map');
    Route::get('/challenges/{slug}/quiz/{challenge}', [ChallengesController::class, 'showQuiz'])->name('challenges.quiz');
    Route::get('/challenges/{slug}/quiz/{challenge}/result/{attempt}', [ChallengesController::class, 'showQuizResult'])->name('challenges.quiz.result');
    Route::post('/challenges/{slug}/quiz/{challenge}/autosave', [ChallengesController::class, 'autosaveQuiz'])->middleware('throttle:anti-cheat-event')->name('challenges.quiz.autosave');
    Route::post('/challenges/{slug}/quiz/{challenge}/heartbeat', [ChallengesController::class, 'heartbeatQuiz'])->middleware('throttle:anti-cheat-event')->name('challenges.quiz.heartbeat');
    Route::post('/challenges/{slug}/quiz/{challenge}/events', [ChallengesController::class, 'logQuizEvent'])->middleware('throttle:anti-cheat-event')->name('challenges.quiz.events');
    Route::post('/challenges/{slug}/quiz/{challenge}/submit', [ChallengesController::class, 'submitQuiz'])->name('challenges.quiz.submit');

    Route::get('/challenges/coding', [ChallengesController::class, 'codingIndex'])->name('challenges.coding');
    Route::get('/challenges/coding/{slug}', [ChallengesController::class, 'codingMap'])->name('challenges.coding.map');
    Route::get('/challenges/coding/{slug}/challenge/{challenge}', [CodingQuizController::class, 'show'])->name('challenges.coding.quiz');
    Route::get('/challenges/coding/{slug}/challenge/{challenge}/ping/{question}', [CodingQuizController::class, 'ping'])->name('challenges.coding.ping');
    Route::post('/challenges/coding/{slug}/challenge/{challenge}/run/{question}', [CodingQuizController::class, 'run'])
        ->middleware('throttle:python-execution')
        ->name('challenges.coding.run');
    Route::post('/challenges/coding/{slug}/challenge/{challenge}/submit/{question}', [CodingQuizController::class, 'submit'])
        ->middleware('throttle:python-execution')
        ->name('challenges.coding.submit');
    Route::post('/challenges/coding/{slug}/challenge/{challenge}/start/{question}', [CodingQuizController::class, 'start'])->name('challenges.coding.start');
    Route::post('/challenges/coding/{slug}/challenge/{challenge}/retake', [CodingQuizController::class, 'retake'])->name('challenges.coding.retake');

    Route::get('/module', [ModuleController::class, 'showModules'])->name('modules.index');
    Route::get('/module/{module}/lesson/{lesson?}', [LessonController::class, 'show'])->name('lesson.show');
    Route::post('/lesson/{lesson}/complete', [LessonController::class, 'complete'])->name('lesson.complete');

    Route::get('/sql-sandbox', [SqlSandboxController::class, 'index'])->name('sql-sandbox.index');
    Route::post('/sql-sandbox/execute', [SqlSandboxController::class, 'execute'])
        ->middleware('throttle:sql-execution')
        ->name('sql-sandbox.execute');
    Route::get('/sql-sandbox/tables', [SqlSandboxController::class, 'tables'])->name('sql-sandbox.tables');
    Route::delete('/sql-sandbox/tables/{table}', [SqlSandboxController::class, 'dropTable'])->name('sql-sandbox.tables.drop');

    Route::post('/api/code-review', [CodeReviewController::class, 'review'])
        ->middleware('throttle:code-review')
        ->name('api.code-review');
    Route::post('/api/code-review/warm', [CodeReviewController::class, 'warm'])
        ->middleware('throttle:code-review-status')
        ->name('api.code-review.warm');
    Route::get('/api/code-review/{review}/status', [CodeReviewController::class, 'status'])
        ->whereUuid('review')
        ->middleware('throttle:code-review-status')
        ->name('api.code-review.status');
});

Route::middleware(['auth', 'active', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/status', [AdminUserController::class, 'toggleStatus'])->name('users.status');

    Route::get('/content', [AdminContentController::class, 'index'])->name('content.index');
    Route::put('/content/categories/{category}', [AdminContentController::class, 'updateCategory'])
        ->name('content.categories.update');

    Route::prefix('module-library')->name('module-library.')->group(function () {
        Route::get('/', [AdminModuleContentController::class, 'index'])->name('index');
        Route::get('/create', [AdminModuleContentController::class, 'create'])->name('create');
        Route::post('/', [AdminModuleContentController::class, 'store'])->name('store');
        Route::get('/{module}', [AdminModuleContentController::class, 'show'])->name('show');
        Route::get('/{module}/edit', [AdminModuleContentController::class, 'edit'])->name('edit');
        Route::put('/{module}', [AdminModuleContentController::class, 'update'])->name('update');
        Route::post('/{module}/duplicate', [AdminModuleContentController::class, 'duplicate'])->name('duplicate');
        Route::patch('/{module}/status', [AdminModuleContentController::class, 'toggleStatus'])->name('status');
        Route::delete('/{module}', [AdminModuleContentController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('challenges')->name('challenges.')->group(function () {
        Route::get('/', [AdminMcqChallengeController::class, 'index'])->name('index');
        Route::get('/create', [AdminMcqChallengeController::class, 'create'])->name('create');
        Route::post('/', [AdminMcqChallengeController::class, 'store'])->name('store');
        Route::get('/{challenge}', [AdminMcqChallengeController::class, 'show'])->name('show');
        Route::get('/{challenge}/edit', [AdminMcqChallengeController::class, 'edit'])->name('edit');
        Route::put('/{challenge}', [AdminMcqChallengeController::class, 'update'])->name('update');
        Route::post('/{challenge}/duplicate', [AdminMcqChallengeController::class, 'duplicate'])->name('duplicate');
        Route::patch('/{challenge}/status', [AdminMcqChallengeController::class, 'toggleStatus'])->name('status');
        Route::delete('/{challenge}', [AdminMcqChallengeController::class, 'destroy'])->name('destroy');
    });

    // Public curriculum: the 24 modules at /module and their lessons.
    Route::prefix('modules')->name('modules.')->group(function () {
        Route::get('/', [AdminPublicModuleController::class, 'index'])->name('index');
        Route::get('/create', [AdminPublicModuleController::class, 'create'])->name('create');
        Route::post('/', [AdminPublicModuleController::class, 'store'])->name('store');
        Route::post('/reorder', [AdminPublicModuleController::class, 'reorder'])->name('reorder');
        Route::get('/{module}/edit', [AdminPublicModuleController::class, 'edit'])->name('edit');
        Route::put('/{module}', [AdminPublicModuleController::class, 'update'])->name('update');
        Route::delete('/{module}', [AdminPublicModuleController::class, 'destroy'])->name('destroy');

        // Module content: the lessons inside one module, with the block editor.
        Route::get('/{module}/lessons', [AdminLessonController::class, 'index'])->name('lessons.index');
        Route::get('/{module}/lessons/create', [AdminLessonController::class, 'create'])->name('lessons.create');
        Route::post('/{module}/lessons', [AdminLessonController::class, 'store'])->name('lessons.store');
        Route::post('/{module}/lessons/reorder', [AdminLessonController::class, 'reorder'])->name('lessons.reorder');
        Route::get('/{module}/lessons/{lesson}/edit', [AdminLessonController::class, 'edit'])->name('lessons.edit');
        Route::put('/{module}/lessons/{lesson}', [AdminLessonController::class, 'update'])->name('lessons.update');
        Route::delete('/{module}/lessons/{lesson}', [AdminLessonController::class, 'destroy'])->name('lessons.destroy');
    });

    // Live preview and image uploads for the block editor.
    Route::post('/lesson-preview', [AdminLessonController::class, 'preview'])->name('lessons.preview');
    Route::post('/lesson-images', [AdminLessonController::class, 'uploadImage'])->name('lessons.images.store');

    // The challenge maps: each level's title, description, order, and the
    // order and availability of the challenges on it.
    Route::prefix('challenge-maps')->name('challenge-maps.')->group(function () {
        Route::get('/', [AdminChallengeMapController::class, 'index'])->name('index');
        Route::put('/{category}', [AdminChallengeMapController::class, 'update'])->name('update');
        Route::post('/{category}/reorder', [AdminChallengeMapController::class, 'reorder'])->name('reorder');
    });

    Route::post('/challenge-question-images', [AdminMcqChallengeController::class, 'uploadImage'])->name('challenges.images.store');

    Route::prefix('coding-challenges')->name('coding-challenges.')->group(function () {
        Route::get('/', [AdminCodingChallengeController::class, 'index'])->name('index');
        Route::get('/create', [AdminCodingChallengeController::class, 'create'])->name('create');
        Route::post('/', [AdminCodingChallengeController::class, 'store'])->name('store');
        Route::post('/check-tests', [AdminCodingChallengeController::class, 'checkTests'])->name('check-tests');
        Route::get('/{challenge}', [AdminCodingChallengeController::class, 'show'])->name('show');
        Route::get('/{challenge}/edit', [AdminCodingChallengeController::class, 'edit'])->name('edit');
        Route::put('/{challenge}', [AdminCodingChallengeController::class, 'update'])->name('update');
        Route::patch('/{challenge}/status', [AdminCodingChallengeController::class, 'toggleStatus'])->name('status');
        Route::delete('/{challenge}', [AdminCodingChallengeController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('assessments')->name('assessments.')->group(function () {
        Route::get('/', [AdminAssessmentContentController::class, 'index'])->name('index');
        Route::get('/create', [AdminAssessmentContentController::class, 'create'])->name('create');
        Route::post('/', [AdminAssessmentContentController::class, 'store'])->name('store');
        Route::get('/{assessment}', [AdminAssessmentContentController::class, 'show'])->name('show');
        Route::get('/{assessment}/edit', [AdminAssessmentContentController::class, 'edit'])->name('edit');
        Route::put('/{assessment}', [AdminAssessmentContentController::class, 'update'])->name('update');
        Route::post('/{assessment}/duplicate', [AdminAssessmentContentController::class, 'duplicate'])->name('duplicate');
        Route::patch('/{assessment}/status', [AdminAssessmentContentController::class, 'toggleStatus'])->name('status');
        Route::delete('/{assessment}', [AdminAssessmentContentController::class, 'destroy'])->name('destroy');
    });

    Route::get('/gamification', [AdminGamificationController::class, 'index'])
        ->name('gamification.index');
    Route::put('/gamification/achievements/{achievement}', [AdminGamificationController::class, 'updateAchievement'])
        ->name('gamification.achievements.update');
    Route::put('/gamification/missions/{mission}', [AdminGamificationController::class, 'updateMission'])
        ->name('gamification.missions.update');

    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
});

Route::middleware(['auth', 'active', 'superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/analytics', [SuperAdminAnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/export/{section}', [SuperAdminAnalyticsController::class, 'export'])->name('analytics.export');

    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/status', [UserManagementController::class, 'toggleStatus'])->name('users.toggleStatus');
    Route::patch('/users/{user}/promote', [UserManagementController::class, 'promote'])->name('users.promote');
    Route::patch('/users/{user}/demote', [UserManagementController::class, 'demote'])->name('users.demote');
    Route::patch('/users/{user}/assign-inst-admin', [UserManagementController::class, 'assignInstitutionAdmin'])->name('users.assignInstitutionAdmin');

    Route::get('/institutions', [InstitutionManagementController::class, 'index'])->name('institutions.index');
    Route::post('/institutions', [InstitutionManagementController::class, 'store'])->name('institutions.store');
    Route::put('/institutions/{institution}', [InstitutionManagementController::class, 'update'])->name('institutions.update');
    Route::patch('/institutions/{institution}/status', [InstitutionManagementController::class, 'toggleStatus'])->name('institutions.toggleStatus');
    Route::delete('/institutions/{institution}', [InstitutionManagementController::class, 'destroy'])->name('institutions.destroy');
});

Route::middleware(['auth', 'active', 'institution.admin'])->prefix('institution_admin')->name('institution-admin.')->group(function () {
    Route::get('/dashboard', [InstitutionAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/applications', [InstructorApplicationController::class, 'index'])->name('applications.index');
    Route::patch('/applications/{application}/approve', [InstructorApplicationController::class, 'approve'])->name('applications.approve');
    Route::patch('/applications/{application}/reject', [InstructorApplicationController::class, 'reject'])->name('applications.reject');
});

Route::middleware(['auth', 'active', 'student'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/apply', [InstructorApplicationController::class, 'showApplyForm'])->name('apply');
    Route::post('/apply', [InstructorApplicationController::class, 'apply'])->name('apply.submit');
});

Route::middleware(['auth', 'active', 'instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/dashboard', [InstructorController::class, 'dashboard'])->name('dashboard');
    Route::get('/model-development', [InstructorMlDashboardController::class, 'index'])->name('model-development.index');

    Route::get('/analytics', [InstructorAnalyticsController::class, 'index'])->name('analytics.index');
    Route::post('/analytics/refresh', [InstructorAnalyticsController::class, 'refresh'])->name('analytics.refresh');
    Route::get('/mastery', [InstructorMasteryController::class, 'index'])->name('mastery.index');
    Route::get('/competencies', [InstructorCompetencyController::class, 'index'])->name('competencies.index');
    Route::post('/competencies/refresh', [InstructorCompetencyController::class, 'refresh'])->name('competencies.refresh');
    Route::get('/risk', [InstructorAtRiskController::class, 'index'])->name('risk.index');
    Route::post('/risk/refresh', [InstructorAtRiskController::class, 'refresh'])->name('risk.refresh');
    Route::get('/reports', [InstructorReportController::class, 'index'])->name('reports.index');
    Route::get('/submissions', [InstructorSubmissionController::class, 'index'])->name('submissions.index');
    Route::get('/challenges', [InstructorChallengePoolController::class, 'index'])->name('challenges.index');
    Route::get('/challenges/{challenge}', [InstructorChallengePoolController::class, 'show'])->name('challenges.show');
    // Instructors build their own MCQ and coding challenges (with test cases)
    // and give them, or the admin's, to a class.
    Route::prefix('challenge-builder')->name('challenge-builder.')->group(function () {
        Route::get('/', [InstructorChallengeBuilderController::class, 'index'])->name('index');
        Route::get('/create', [InstructorChallengeBuilderController::class, 'create'])->name('create');
        Route::post('/', [InstructorChallengeBuilderController::class, 'store'])->name('store');
        Route::post('/check-tests', [InstructorChallengeBuilderController::class, 'checkTests'])->name('check-tests');
        Route::post('/images', [InstructorChallengeBuilderController::class, 'uploadImage'])->name('images.store');
        Route::get('/{challenge}/edit', [InstructorChallengeBuilderController::class, 'edit'])->name('edit');
        Route::put('/{challenge}', [InstructorChallengeBuilderController::class, 'update'])->name('update');
        Route::delete('/{challenge}', [InstructorChallengeBuilderController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('class-challenges')->name('class-challenges.')->group(function () {
        Route::get('/', [InstructorClassChallengeController::class, 'index'])->name('index');
        Route::post('/', [InstructorClassChallengeController::class, 'store'])->name('store');
        Route::put('/{assignment}', [InstructorClassChallengeController::class, 'update'])->name('update');
        Route::delete('/{assignment}', [InstructorClassChallengeController::class, 'destroy'])->name('destroy');
    });
    Route::get('/anti-cheat/events', [InstructorAntiCheatEventController::class, 'index'])->name('anti-cheat.events');

    Route::get('/anti-cheat', [InstructorAntiCheatController::class, 'index'])->name('anti-cheat.index');
    Route::post('/anti-cheat', [InstructorAntiCheatController::class, 'store'])->name('anti-cheat.store');
    Route::put('/anti-cheat/{setting}', [InstructorAntiCheatController::class, 'update'])->name('anti-cheat.update');
    Route::delete('/anti-cheat/{setting}', [InstructorAntiCheatController::class, 'destroy'])->name('anti-cheat.destroy');

    Route::prefix('tos')->name('tos.')->group(function () {
        Route::get('/', [InstructorTosController::class, 'index'])->name('index');
        Route::get('/create', [InstructorTosController::class, 'create'])->name('create');
        Route::post('/', [InstructorTosController::class, 'store'])->name('store');
        Route::get('/{tos}', [InstructorTosController::class, 'show'])->name('show');
        Route::post('/{tos}/suggested-distribution', [InstructorTosController::class, 'applySuggestedDistribution'])->name('suggested-distribution');
        Route::patch('/{tos}/distribution', [InstructorTosController::class, 'updateDistribution'])->name('distribution.update');
        Route::get('/{tos}/review', [InstructorTosController::class, 'review'])->name('review');
        Route::delete('/{tos}', [InstructorTosController::class, 'destroy'])->name('destroy');
        Route::patch('/{tos}/rows/{row}', [InstructorTosController::class, 'updateRow'])->name('rows.update');
    });



    Route::prefix('assessments')->name('assessments.')->group(function () {
        Route::get('/', [InstructorAssessmentController::class, 'index'])->name('index');
        Route::get('/from-tos/{tos}/create', [InstructorAssessmentController::class, 'create'])->name('create');
        Route::post('/from-tos/{tos}', [InstructorAssessmentController::class, 'store'])->name('store');
        Route::get('/{assessment}/builder', [InstructorAssessmentController::class, 'builder'])->name('builder');
        Route::patch('/{assessment}/questions/quick-setup', [InstructorAssessmentController::class, 'applyQuickSetup'])->name('questions.quick-setup');
        Route::patch('/{assessment}/questions/{question}', [InstructorAssessmentController::class, 'updateQuestion'])->name('questions.update');
        Route::patch('/{assessment}/publish', [InstructorAssessmentController::class, 'publish'])->name('publish');
        Route::patch('/{assessment}/close', [InstructorAssessmentController::class, 'close'])->name('close');
        Route::get('/{assessment}/submissions', [InstructorAssessmentController::class, 'submissions'])->name('submissions');
        Route::get('/{assessment}/submissions/{submission}', [InstructorAssessmentController::class, 'showSubmission'])->name('submissions.show');
        Route::patch('/{assessment}/submissions/{submission}/grade', [InstructorAssessmentController::class, 'gradeSubmission'])->name('submissions.grade');
        Route::get('/{assessment}/analytics', [InstructorAssessmentController::class, 'analytics'])->name('analytics');
    });

    Route::prefix('assignments')->name('assignments.')->group(function () {
        Route::get('/', [InstructorAssignmentController::class, 'index'])->name('index');
        Route::get('/create', [InstructorAssignmentController::class, 'create'])->name('create');
        Route::post('/', [InstructorAssignmentController::class, 'store'])->name('store');
        Route::get('/{assignment}', [InstructorAssignmentController::class, 'show'])->name('show');
        Route::get('/{assignment}/edit', [InstructorAssignmentController::class, 'edit'])->name('edit');
        Route::put('/{assignment}', [InstructorAssignmentController::class, 'update'])->name('update');
        Route::delete('/{assignment}', [InstructorAssignmentController::class, 'destroy'])->name('destroy');
        Route::patch('/{assignment}/publish', [InstructorAssignmentController::class, 'publish'])->name('publish');
        Route::patch('/{assignment}/close', [InstructorAssignmentController::class, 'close'])->name('close');
        Route::patch('/{assignment}/archive', [InstructorAssignmentController::class, 'archive'])->name('archive');
        Route::patch('/{assignment}/submissions/{submission}/release', [InstructorAssignmentController::class, 'releaseHeldSubmission'])->name('submissions.release');
        Route::patch('/{assignment}/submissions/{submission}/keep-blocked', [InstructorAssignmentController::class, 'keepSubmissionBlocked'])->name('submissions.keep-blocked');
    });

    Route::prefix('classes')->name('classes.')->group(function () {
        Route::get('/', [InstructorClassController::class, 'index'])->name('index');
        Route::get('/create', [InstructorClassController::class, 'create'])->name('create');
        Route::post('/', [InstructorClassController::class, 'store'])->name('store');
        Route::get('/{class}', [InstructorClassController::class, 'show'])->name('show');
        Route::get('/{class}/edit', [InstructorClassController::class, 'edit'])->name('edit');
        Route::put('/{class}', [InstructorClassController::class, 'update'])->name('update');
        Route::delete('/{class}', [InstructorClassController::class, 'destroy'])->name('destroy');
        Route::patch('/{class}/archive', [InstructorClassController::class, 'archive'])->name('archive');
        Route::patch('/{class}/restore', [InstructorClassController::class, 'restore'])->name('restore');
        Route::patch('/{class}/regenerate-code', [InstructorClassController::class, 'regenerateCode'])->name('regenerate-code');

        Route::get('/{class}/students', [ClassStudentController::class, 'index'])->name('students');
        Route::delete('/{class}/students/{student}', [ClassStudentController::class, 'remove'])->name('students.remove');
        Route::delete('/{class}/students', [ClassStudentController::class, 'removeBulk'])->name('students.remove-bulk');
        Route::post('/{class}/students/add-by-email', [ClassStudentController::class, 'addByEmail'])->name('students.add-by-email');
    });
});

Route::middleware(['auth', 'active', 'instructor'])->prefix('modules')->name('modules.')->group(function () {
    Route::get('/module-library', [ModuleLibraryController::class, 'index'])->name('module-library.index');
    Route::post('/module-library/assign', [ModuleLibraryController::class, 'assign'])->name('module-library.assign');
    Route::get('/module-library/{module}', [ModuleLibraryController::class, 'show'])->name('module-library.show');
});
