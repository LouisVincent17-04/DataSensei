<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ChallengesController;
use App\Http\Controllers\IdeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\CodingQuizController;
use App\Http\Controllers\SqlSandboxController;
use App\Http\Controllers\CodeReviewController;

// 1. Root Route
Route::get('/', function () {
    return redirect('/login');
});

// 2. Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

// 3. Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Student
    Route::get('/student/dashboard', [StudentController::class, 'dashboard'])->name('studentDashboard');
    Route::get('/student/modules', [ModuleController::class, 'showModules'])->name('showModules');

    // IDE
    Route::get('/ide', [IdeController::class, 'index'])->name('ide.index');
    Route::get('/ide/tree', [IdeController::class, 'tree'])->name('ide.tree');
    Route::post('/ide/nodes', [IdeController::class, 'storeNode'])->name('ide.nodes.store');
    Route::put('/ide/nodes/{node}', [IdeController::class, 'updateNode'])->name('ide.nodes.update');
    Route::patch('/ide/nodes/{node}/rename', [IdeController::class, 'renameNode'])->name('ide.nodes.rename');
    Route::patch('/ide/nodes/{node}/save', [IdeController::class, 'saveContent'])->name('ide.nodes.save');
    Route::patch('/ide/nodes/{node}/move', [IdeController::class, 'moveNode'])->name('ide.nodes.move');
    Route::delete('/ide/nodes/{node}', [IdeController::class, 'deleteNode'])->name('ide.nodes.delete');
    Route::post('/ide/nodes/{node}/run', [IdeController::class, 'runNode'])->name('ide.nodes.run');

    // ── MCQ Challenge System ──
    Route::get('/challenges', [ChallengesController::class, 'index'])->name('challenges');
    Route::post('/challenges/enroll', [ChallengesController::class, 'enrollOrganization'])->name('challenges.enroll');
    Route::get('/challenges/map/{slug}', [ChallengesController::class, 'map'])->name('challenges.map');
    Route::get('/challenges/{slug}/quiz/{challenge}', [ChallengesController::class, 'showQuiz'])->name('challenges.quiz');
    Route::post('/challenges/{slug}/quiz/{challenge}/submit', [ChallengesController::class, 'submitQuiz'])->name('challenges.quiz.submit');

    // ── Coding Challenge System ──
    Route::get('/challenges/coding', [ChallengesController::class, 'codingIndex'])->name('challenges.coding');
    Route::get('/challenges/coding/{slug}', [ChallengesController::class, 'codingMap'])->name('challenges.coding.map');
    Route::get('/challenges/coding/{slug}/challenge/{challenge}', [CodingQuizController::class, 'show'])->name('challenges.coding.quiz');
    Route::get('/challenges/coding/{slug}/challenge/{challenge}/ping/{question}', [CodingQuizController::class, 'ping'])->name('challenges.coding.ping');
    Route::post('/challenges/coding/{slug}/challenge/{challenge}/run/{question}', [CodingQuizController::class, 'run'])->name('challenges.coding.run');
    Route::post('/challenges/coding/{slug}/challenge/{challenge}/submit/{question}', [CodingQuizController::class, 'submit'])->name('challenges.coding.submit');
    Route::post('/challenges/coding/{slug}/challenge/{challenge}/start/{question}',
        [CodingQuizController::class, 'start'])
        ->name('challenges.coding.start');
    Route::post('/challenges/coding/{slug}/challenge/{challenge}/retake',
        [CodingQuizController::class, 'retake'])
        ->name('challenges.coding.retake');

    // Modules / Lessons
    Route::get('/module', [ModuleController::class, 'showModules'])->name('modules.index');
    Route::get('/module/{module}/lesson/{lesson?}', [LessonController::class, 'show'])->name('lesson.show');
    Route::post('/lesson/{lesson}/complete', [LessonController::class, 'complete'])->name('lesson.complete');

    // Profile
    Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
    Route::get('/change-password', [UserController::class, 'changePassword'])->name('change-password');

    // ── SQL Sandbox ──────────────────────────────────────────────────────────────
    Route::get('/sql-sandbox', [SqlSandboxController::class, 'index'])
        ->name('sql-sandbox.index');

    Route::post('/sql-sandbox/execute', [SqlSandboxController::class, 'execute'])
        ->name('sql-sandbox.execute');

    Route::get('/sql-sandbox/tables', [SqlSandboxController::class, 'tables'])
        ->name('sql-sandbox.tables');

    Route::delete('/sql-sandbox/tables/{table}', [SqlSandboxController::class, 'dropTable'])
        ->name('sql-sandbox.tables.drop');

    // ── AI Code / SQL Reviewer ───────────────────────────────────────────────────
    // Used by the ReviewBot chat widget in both the Python IDE and the SQL Sandbox.
    // Accepts POST: { code, language, question? }  →  { ok, message }
    Route::post('/api/code-review', [CodeReviewController::class, 'review'])
        ->name('api.code-review');
});