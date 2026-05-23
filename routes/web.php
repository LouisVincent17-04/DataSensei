<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ModuleLibraryController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ChallengesController;
use App\Http\Controllers\IdeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\CodingQuizController;
use App\Http\Controllers\SqlSandboxController;
use App\Http\Controllers\CodeReviewController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\InstitutionManagementController;
use App\Http\Controllers\InstitutionAdminController;
use App\Http\Controllers\InstructorApplicationController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\InstructorClassController;
use App\Http\Controllers\ClassStudentController;
use App\Http\Controllers\ProfileController;

// ── 1. Root Route ─────────────────────────────────────────────────────────────
Route::get('/', function () {
    return redirect('/login');
});

// ── 2. Guest Routes ───────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

// ── 3. Authenticated Routes ───────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ── Student ───────────────────────────────────────────────────────────────
    Route::get('/student/dashboard', [StudentController::class, 'dashboard'])->name('studentDashboard');
    Route::get('/student/modules',   [ModuleController::class, 'showModules'])->name('showModules');

    // ── IDE ───────────────────────────────────────────────────────────────────
    Route::get('/ide',                         [IdeController::class, 'index'])->name('ide.index');
    Route::get('/ide/tree',                    [IdeController::class, 'tree'])->name('ide.tree');
    Route::post('/ide/nodes',                  [IdeController::class, 'storeNode'])->name('ide.nodes.store');
    Route::put('/ide/nodes/{node}',            [IdeController::class, 'updateNode'])->name('ide.nodes.update');
    Route::patch('/ide/nodes/{node}/rename',   [IdeController::class, 'renameNode'])->name('ide.nodes.rename');
    Route::patch('/ide/nodes/{node}/save',     [IdeController::class, 'saveContent'])->name('ide.nodes.save');
    Route::patch('/ide/nodes/{node}/move',     [IdeController::class, 'moveNode'])->name('ide.nodes.move');
    Route::delete('/ide/nodes/{node}',         [IdeController::class, 'deleteNode'])->name('ide.nodes.delete');
    Route::post('/ide/nodes/{node}/run',       [IdeController::class, 'runNode'])->name('ide.nodes.run');

    // ── MCQ Challenge System ──────────────────────────────────────────────────
    Route::get('/challenges',                                           [ChallengesController::class, 'index'])->name('challenges');
    Route::post('/challenges/enroll',                                   [ChallengesController::class, 'enrollOrganization'])->name('challenges.enroll');
    Route::get('/challenges/map/{slug}',                                [ChallengesController::class, 'map'])->name('challenges.map');
    Route::get('/challenges/{slug}/quiz/{challenge}',                   [ChallengesController::class, 'showQuiz'])->name('challenges.quiz');
    Route::post('/challenges/{slug}/quiz/{challenge}/submit',           [ChallengesController::class, 'submitQuiz'])->name('challenges.quiz.submit');

    // ── Coding Challenge System ───────────────────────────────────────────────
    Route::get('/challenges/coding',                                                          [ChallengesController::class, 'codingIndex'])->name('challenges.coding');
    Route::get('/challenges/coding/{slug}',                                                   [ChallengesController::class, 'codingMap'])->name('challenges.coding.map');
    Route::get('/challenges/coding/{slug}/challenge/{challenge}',                             [CodingQuizController::class, 'show'])->name('challenges.coding.quiz');
    Route::get('/challenges/coding/{slug}/challenge/{challenge}/ping/{question}',             [CodingQuizController::class, 'ping'])->name('challenges.coding.ping');
    Route::post('/challenges/coding/{slug}/challenge/{challenge}/run/{question}',             [CodingQuizController::class, 'run'])->name('challenges.coding.run');
    Route::post('/challenges/coding/{slug}/challenge/{challenge}/submit/{question}',          [CodingQuizController::class, 'submit'])->name('challenges.coding.submit');
    Route::post('/challenges/coding/{slug}/challenge/{challenge}/start/{question}',           [CodingQuizController::class, 'start'])->name('challenges.coding.start');
    Route::post('/challenges/coding/{slug}/challenge/{challenge}/retake',                     [CodingQuizController::class, 'retake'])->name('challenges.coding.retake');

    // ── Modules / Lessons ─────────────────────────────────────────────────────
    Route::get('/module', [ModuleController::class, 'showModules'])->name('modules.index');
    Route::get('/module/{module}/lesson/{lesson?}', [LessonController::class, 'show'])->name('lesson.show');
    Route::post('/lesson/{lesson}/complete', [LessonController::class, 'complete'])->name('lesson.complete');

    // ── Profile ───────────────────────────────────────────────────────────────
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('/profile/institution/apply', [ProfileController::class, 'applyAsInstructor'])->name('profile.institution.apply');
    Route::delete('/profile/delete', [ProfileController::class, 'deleteAccount'])->name('profile.delete');

    Route::get('/change-password', function () {
        return redirect()->route('profile', ['tab' => 'security']);
    })->name('change-password');

    // ── SQL Sandbox ───────────────────────────────────────────────────────────
    Route::get('/sql-sandbox',                    [SqlSandboxController::class, 'index'])->name('sql-sandbox.index');
    Route::post('/sql-sandbox/execute',           [SqlSandboxController::class, 'execute'])->name('sql-sandbox.execute');
    Route::get('/sql-sandbox/tables',             [SqlSandboxController::class, 'tables'])->name('sql-sandbox.tables');
    Route::delete('/sql-sandbox/tables/{table}',  [SqlSandboxController::class, 'dropTable'])->name('sql-sandbox.tables.drop');

    // ── AI Code / SQL Reviewer ────────────────────────────────────────────────
    Route::post('/api/code-review', [CodeReviewController::class, 'review'])->name('api.code-review');
});

// ── 4. Super Admin Routes ─────────────────────────────────────────────────────
Route::middleware(['auth', 'superadmin'])
    ->name('superadmin.')
    ->group(function () {

        Route::get('/superadmin/dashboard', [SuperAdminController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/superadmin/users', [UserManagementController::class, 'index'])
            ->name('users.index');
        Route::post('/superadmin/users', [UserManagementController::class, 'store'])
            ->name('users.store');
        Route::put('/superadmin/users/{user}', [UserManagementController::class, 'update'])
            ->name('users.update');
        Route::patch('/superadmin/users/{user}/status', [UserManagementController::class, 'toggleStatus'])
            ->name('users.toggleStatus');
        Route::patch('/superadmin/users/{user}/promote', [UserManagementController::class, 'promote'])
            ->name('users.promote');
        Route::patch('/superadmin/users/{user}/demote', [UserManagementController::class, 'demote'])
            ->name('users.demote');
        Route::patch('/superadmin/users/{user}/assign-inst-admin', [UserManagementController::class, 'assignInstitutionAdmin'])
            ->name('users.assignInstitutionAdmin');

        Route::get('/superadmin/institutions', [InstitutionManagementController::class, 'index'])
            ->name('institutions.index');
        Route::post('/superadmin/institutions', [InstitutionManagementController::class, 'store'])
            ->name('institutions.store');
        Route::put('/superadmin/institutions/{institution}', [InstitutionManagementController::class, 'update'])
            ->name('institutions.update');
        Route::patch('/superadmin/institutions/{institution}/status', [InstitutionManagementController::class, 'toggleStatus'])
            ->name('institutions.toggleStatus');
        Route::delete('/superadmin/institutions/{institution}', [InstitutionManagementController::class, 'destroy'])
            ->name('institutions.destroy');
    });

// ── 5. Institution Admin Routes ───────────────────────────────────────────────
Route::middleware(['auth', 'institution.admin'])
    ->prefix('institution_admin')
    ->name('institution-admin.')
    ->group(function () {

        Route::get('/dashboard', [InstitutionAdminController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/applications', [InstructorApplicationController::class, 'index'])
            ->name('applications.index');
        Route::patch('/applications/{application}/approve', [InstructorApplicationController::class, 'approve'])
            ->name('applications.approve');
        Route::patch('/applications/{application}/reject', [InstructorApplicationController::class, 'reject'])
            ->name('applications.reject');
    });

// ── 6. Instructor Routes ──────────────────────────────────────────────────────
// FIX: merged the two separate instructor groups into one to avoid duplication.
Route::middleware('auth')
    ->prefix('instructor')
    ->name('instructor.')
    ->group(function () {

        // Application & dashboard
        Route::get('/apply',  [InstructorApplicationController::class, 'showApplyForm'])->name('apply');
        Route::post('/apply', [InstructorApplicationController::class, 'apply'])->name('apply.submit');
        Route::get('/dashboard', [InstructorController::class, 'dashboard'])->name('dashboard');

        // Class management
        Route::prefix('classes')->name('classes.')->group(function () {
            Route::get('/',        [InstructorClassController::class, 'index'])->name('index');
            Route::get('/create',  [InstructorClassController::class, 'create'])->name('create');
            Route::post('/',       [InstructorClassController::class, 'store'])->name('store');
            Route::get('/{class}', [InstructorClassController::class, 'show'])->name('show');
            Route::get('/{class}/edit',  [InstructorClassController::class, 'edit'])->name('edit');
            Route::put('/{class}',       [InstructorClassController::class, 'update'])->name('update');
            Route::delete('/{class}',    [InstructorClassController::class, 'destroy'])->name('destroy');

            Route::patch('/{class}/archive',         [InstructorClassController::class, 'archive'])->name('archive');
            Route::patch('/{class}/restore',         [InstructorClassController::class, 'restore'])->name('restore');
            Route::patch('/{class}/regenerate-code', [InstructorClassController::class, 'regenerateCode'])->name('regenerate-code');

            // ── Student roster ────────────────────────────────────────────────
            Route::get   ('/{class}/students',                    [ClassStudentController::class, 'index'])       ->name('students');
            Route::post  ('/{class}/students/{student}/approve',  [ClassStudentController::class, 'approve'])     ->name('students.approve');
            Route::post  ('/{class}/students/approve-bulk',       [ClassStudentController::class, 'approveBulk']) ->name('students.approve-bulk');
            Route::delete('/{class}/students/{student}',          [ClassStudentController::class, 'remove'])      ->name('students.remove');
            Route::delete('/{class}/students',                    [ClassStudentController::class, 'removeBulk'])  ->name('students.remove-bulk');
        });
    });

Route::middleware(['auth'])
    ->prefix('modules')
    ->name('modules.')
    ->group(function () {
        Route::get('/module-library', [ModuleLibraryController::class, 'index'])
            ->name('module-library.index');

        Route::post('/module-library/assign', [ModuleLibraryController::class, 'assign'])
            ->name('module-library.assign');
    });