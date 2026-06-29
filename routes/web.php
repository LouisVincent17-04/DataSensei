<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminContentController;
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
use App\Http\Controllers\InstitutionAdminController;
use App\Http\Controllers\InstitutionManagementController;
use App\Http\Controllers\InstructorAnalyticsController;
use App\Http\Controllers\InstructorAntiCheatController;
use App\Http\Controllers\InstructorAntiCheatEventController;
use App\Http\Controllers\InstructorApplicationController;
use App\Http\Controllers\InstructorAssignmentController;
use App\Http\Controllers\InstructorAtRiskController;
use App\Http\Controllers\InstructorChallengePoolController;
use App\Http\Controllers\InstructorClassController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\InstructorMasteryController;
use App\Http\Controllers\InstructorReportController;
use App\Http\Controllers\InstructorSubmissionController;
use App\Http\Controllers\InstructorTosController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ModuleLibraryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PasswordResetOtpController;
use App\Http\Controllers\SqlSandboxController;
use App\Http\Controllers\StudentAnalyticsController;
use App\Http\Controllers\StudentAssignmentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentDataToolkitController;
use App\Http\Controllers\StudentGamificationController;
use App\Http\Controllers\StudentModuleLibraryController;
use App\Http\Controllers\SuperAdminAnalyticsController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register'])->name('register');

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

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('/profile/institution/apply', [ProfileController::class, 'applyAsInstructor'])->name('profile.institution.apply');
    Route::delete('/profile/delete', [ProfileController::class, 'deleteAccount'])->name('profile.delete');

    Route::get('/change-password', fn () => redirect()->route('profile', ['tab' => 'security']))->name('change-password');
});

Route::middleware(['auth', 'student'])->group(function () {
    Route::get('/student/dashboard', [StudentController::class, 'dashboard'])->name('studentDashboard');

    Route::get('/student/modules', [StudentModuleLibraryController::class, 'index'])->name('showModules');
    Route::get('/student/modules', [StudentModuleLibraryController::class, 'index'])->name('student.modules.index');
    Route::get('/student/modules/{module}', [StudentModuleLibraryController::class, 'show'])->name('student.modules.show');

    Route::get('/student/analytics', [StudentAnalyticsController::class, 'index'])->name('student.analytics.index');
    Route::get('/student/data-toolkit', [StudentDataToolkitController::class, 'index'])->name('student.data-toolkit.index');
    Route::get('/student/data-toolkit/{dataset}', [StudentDataToolkitController::class, 'show'])->name('student.data-toolkit.show');
    Route::post('/student/data-toolkit/{dataset}/analyze', [StudentDataToolkitController::class, 'analyze'])->name('student.data-toolkit.analyze');
    Route::get('/student/data-toolkit/{dataset}/report', [StudentDataToolkitController::class, 'report'])->name('student.data-toolkit.report');
    Route::get('/student/achievements', [StudentGamificationController::class, 'achievements'])->name('student.achievements.index');
    Route::get('/student/leaderboard', [StudentGamificationController::class, 'leaderboard'])->name('student.leaderboard.index');
    Route::get('/student/advanced-topics', [AdvancedTopicRecommendationController::class, 'index'])->name('student.advanced-topics.index');

    Route::prefix('student/assignments')->name('student.assignments.')->group(function () {
        Route::get('/', [StudentAssignmentController::class, 'index'])->name('index');
        Route::get('/{assignment}', [StudentAssignmentController::class, 'show'])->name('show');
        Route::post('/{assignment}/start', [StudentAssignmentController::class, 'start'])->name('start');
        Route::get('/{assignment}/attempt/{submission}', [StudentAssignmentController::class, 'take'])->name('take');
        Route::post('/{assignment}/attempt/{submission}/submit', [StudentAssignmentController::class, 'submit'])->name('submit');
        Route::get('/{assignment}/attempt/{submission}/result', [StudentAssignmentController::class, 'result'])->name('result');
    });

    Route::prefix('student/submissions')->name('student.submissions.')->group(function () {
        Route::get('/', [StudentAssignmentController::class, 'submissions'])->name('index');
        Route::get('/{submission}', [StudentAssignmentController::class, 'submissionResult'])->name('show');
    });

    Route::post('/anti-cheat/events', [AntiCheatEventController::class, 'store'])->name('anti-cheat.events.store');

    Route::get('/ide', [IdeController::class, 'index'])->name('ide.index');
    Route::get('/ide/tree', [IdeController::class, 'tree'])->name('ide.tree');
    Route::post('/ide/nodes', [IdeController::class, 'storeNode'])->name('ide.nodes.store');
    Route::put('/ide/nodes/{node}', [IdeController::class, 'updateNode'])->name('ide.nodes.update');
    Route::patch('/ide/nodes/{node}/rename', [IdeController::class, 'renameNode'])->name('ide.nodes.rename');
    Route::patch('/ide/nodes/{node}/save', [IdeController::class, 'saveContent'])->name('ide.nodes.save');
    Route::patch('/ide/nodes/{node}/move', [IdeController::class, 'moveNode'])->name('ide.nodes.move');
    Route::delete('/ide/nodes/{node}', [IdeController::class, 'deleteNode'])->name('ide.nodes.delete');
    Route::post('/ide/nodes/{node}/run', [IdeController::class, 'runNode'])->name('ide.nodes.run');

    Route::get('/challenges', [ChallengesController::class, 'index'])->name('challenges');
    Route::get('/challenges/map/{slug}', [ChallengesController::class, 'map'])->name('challenges.map');
    Route::get('/challenges/{slug}/quiz/{challenge}', [ChallengesController::class, 'showQuiz'])->name('challenges.quiz');
    Route::post('/challenges/{slug}/quiz/{challenge}/autosave', [ChallengesController::class, 'autosaveQuiz'])->name('challenges.quiz.autosave');
    Route::post('/challenges/{slug}/quiz/{challenge}/heartbeat', [ChallengesController::class, 'heartbeatQuiz'])->name('challenges.quiz.heartbeat');
    Route::post('/challenges/{slug}/quiz/{challenge}/events', [ChallengesController::class, 'logQuizEvent'])->name('challenges.quiz.events');
    Route::post('/challenges/{slug}/quiz/{challenge}/submit', [ChallengesController::class, 'submitQuiz'])->name('challenges.quiz.submit');

    Route::get('/challenges/coding', [ChallengesController::class, 'codingIndex'])->name('challenges.coding');
    Route::get('/challenges/coding/{slug}', [ChallengesController::class, 'codingMap'])->name('challenges.coding.map');
    Route::get('/challenges/coding/{slug}/challenge/{challenge}', [CodingQuizController::class, 'show'])->name('challenges.coding.quiz');
    Route::get('/challenges/coding/{slug}/challenge/{challenge}/ping/{question}', [CodingQuizController::class, 'ping'])->name('challenges.coding.ping');
    Route::post('/challenges/coding/{slug}/challenge/{challenge}/run/{question}', [CodingQuizController::class, 'run'])->name('challenges.coding.run');
    Route::post('/challenges/coding/{slug}/challenge/{challenge}/submit/{question}', [CodingQuizController::class, 'submit'])->name('challenges.coding.submit');
    Route::post('/challenges/coding/{slug}/challenge/{challenge}/start/{question}', [CodingQuizController::class, 'start'])->name('challenges.coding.start');
    Route::post('/challenges/coding/{slug}/challenge/{challenge}/retake', [CodingQuizController::class, 'retake'])->name('challenges.coding.retake');

    Route::get('/module', [ModuleController::class, 'showModules'])->name('modules.index');
    Route::get('/module/{module}/lesson/{lesson?}', [LessonController::class, 'show'])->name('lesson.show');
    Route::post('/lesson/{lesson}/complete', [LessonController::class, 'complete'])->name('lesson.complete');

    Route::get('/sql-sandbox', [SqlSandboxController::class, 'index'])->name('sql-sandbox.index');
    Route::post('/sql-sandbox/execute', [SqlSandboxController::class, 'execute'])->name('sql-sandbox.execute');
    Route::get('/sql-sandbox/tables', [SqlSandboxController::class, 'tables'])->name('sql-sandbox.tables');
    Route::delete('/sql-sandbox/tables/{table}', [SqlSandboxController::class, 'dropTable'])->name('sql-sandbox.tables.drop');

    Route::post('/api/code-review', [CodeReviewController::class, 'review'])->name('api.code-review');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/status', [AdminUserController::class, 'toggleStatus'])->name('users.status');

    // Module Library
    Route::get('/module-library', [AdminContentController::class, 'moduleLibrary'])
        ->name('module-library.index');

    Route::get('/content', [AdminContentController::class, 'index'])->name('content.index');
    Route::put('/content/modules/{module}', [AdminContentController::class, 'updateModule'])
        ->name('content.modules.update');
    Route::put('/content/categories/{category}', [AdminContentController::class, 'updateCategory'])
        ->name('content.categories.update');
    Route::put('/content/challenges/{challenge}', [AdminContentController::class, 'updateChallenge'])
        ->name('content.challenges.update');

    Route::get('/gamification', [AdminGamificationController::class, 'index'])
        ->name('gamification.index');
    Route::put('/gamification/achievements/{achievement}', [AdminGamificationController::class, 'updateAchievement'])
        ->name('gamification.achievements.update');
    Route::put('/gamification/missions/{mission}', [AdminGamificationController::class, 'updateMission'])
        ->name('gamification.missions.update');

    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
});

Route::middleware(['auth', 'superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
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

Route::middleware(['auth', 'institution.admin'])->prefix('institution_admin')->name('institution-admin.')->group(function () {
    Route::get('/dashboard', [InstitutionAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/applications', [InstructorApplicationController::class, 'index'])->name('applications.index');
    Route::patch('/applications/{application}/approve', [InstructorApplicationController::class, 'approve'])->name('applications.approve');
    Route::patch('/applications/{application}/reject', [InstructorApplicationController::class, 'reject'])->name('applications.reject');
});

Route::middleware('auth')->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/apply', [InstructorApplicationController::class, 'showApplyForm'])->name('apply');
    Route::post('/apply', [InstructorApplicationController::class, 'apply'])->name('apply.submit');
});

Route::middleware(['auth', 'instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/dashboard', [InstructorController::class, 'dashboard'])->name('dashboard');

    Route::get('/analytics', [InstructorAnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/mastery', [InstructorMasteryController::class, 'index'])->name('mastery.index');
    Route::get('/risk', [InstructorAtRiskController::class, 'index'])->name('risk.index');
    Route::get('/reports', [InstructorReportController::class, 'index'])->name('reports.index');
    Route::get('/submissions', [InstructorSubmissionController::class, 'index'])->name('submissions.index');
    Route::get('/challenges', [InstructorChallengePoolController::class, 'index'])->name('challenges.index');
    Route::get('/anti-cheat/events', [InstructorAntiCheatEventController::class, 'index'])->name('anti-cheat.events');

    Route::get('/anti-cheat', [InstructorAntiCheatController::class, 'index'])->name('anti-cheat.index');
    Route::post('/anti-cheat', [InstructorAntiCheatController::class, 'store'])->name('anti-cheat.store');
    Route::put('/anti-cheat/{setting}', [InstructorAntiCheatController::class, 'update'])->name('anti-cheat.update');
    Route::delete('/anti-cheat/{setting}', [InstructorAntiCheatController::class, 'destroy'])->name('anti-cheat.destroy');

    Route::prefix('tos')->name('tos.')->group(function () {
        Route::get('/', [InstructorTosController::class, 'index'])->name('index');
        Route::post('/', [InstructorTosController::class, 'store'])->name('store');
        Route::get('/{tos}', [InstructorTosController::class, 'show'])->name('show');
        Route::patch('/{tos}/rows/{row}', [InstructorTosController::class, 'updateRow'])->name('rows.update');
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
        Route::post('/{class}/students/{student}/approve', [ClassStudentController::class, 'approve'])->name('students.approve');
        Route::post('/{class}/students/approve-bulk', [ClassStudentController::class, 'approveBulk'])->name('students.approve-bulk');
        Route::delete('/{class}/students/{student}', [ClassStudentController::class, 'remove'])->name('students.remove');
        Route::delete('/{class}/students', [ClassStudentController::class, 'removeBulk'])->name('students.remove-bulk');
        Route::post('/{class}/students/add-by-email', [ClassStudentController::class, 'addByEmail'])->name('students.add-by-email');
    });
});

Route::middleware(['auth'])->prefix('modules')->name('modules.')->group(function () {
    Route::get('/module-library', [ModuleLibraryController::class, 'index'])->name('module-library.index');
    Route::post('/module-library/assign', [ModuleLibraryController::class, 'assign'])->name('module-library.assign');
    Route::get('/module-library/{module}', [ModuleLibraryController::class, 'show'])->name('module-library.show');
});
