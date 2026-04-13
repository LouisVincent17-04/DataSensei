<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ChallengesController;
use App\Http\Controllers\IdeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LessonController;

// 1. Root Route
// If someone goes to your main URL, redirect them to the login page.
Route::get('/', function () {
    return redirect('/login');
});

// 2. Guest Routes (Only visible to logged-out users)
// The 'guest' middleware automatically uses RedirectIfAuthenticated if a logged-in user tries to visit here.
Route::middleware('guest')->group(function () {
    // Displays the login form
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    
    // Processes the login form
    Route::post('/login', [AuthController::class, 'login']);
    
    // Processes the registration form
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

// 3. Authenticated Routes (Only visible to logged-in users)
Route::middleware('auth')->group(function () {
    // Processes logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Student Dashboard
    Route::get('/student/dashboard', [StudentController::class, 'dashboard'])->name('studentDashboard');
    
    // Student Modules Page
    Route::get('/student/modules', [ModuleController::class, 'showModules'])->name('showModules');

    // Main IDE view
    Route::get('/ide', [IdeController::class, 'index'])->name('ide.index');

    // Challenges System
// Challenges System
    Route::get('/challenges', [ChallengesController::class, 'index'])->name('challenges');
    Route::get('/challenges/map/{slug}', [ChallengesController::class, 'map'])->name('challenges.map');
    
    // Add this new route for the invite code modal
    Route::post('/challenges/enroll', [ChallengesController::class, 'enrollOrganization'])->name('challenges.enroll');
    // The Learning Room (NetAcad interface)
    Route::get('/module', [ModuleController::class, 'showModules'])->name('module');
    Route::get('/module/{module}/lesson/{lesson?}', [LessonController::class, 'show'])->name('lesson.show');
    Route::post('/lesson/{lesson}/complete', [LessonController::class, 'complete'])->name('lesson.complete');

    // Profile Page
    Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
    Route::get('/change-password', [UserController::class, 'changePassword'])->name('change-password');
    
    // Tree (AJAX)
    Route::get('/ide/tree', [IdeController::class, 'tree'])->name('ide.tree');
 
    // Node CRUD
    Route::post('/ide/nodes',                [IdeController::class, 'storeNode'])->name('ide.nodes.store');
    Route::put('/ide/nodes/{node}',          [IdeController::class, 'updateNode'])->name('ide.nodes.update');
    Route::patch('/ide/nodes/{node}/rename', [IdeController::class, 'renameNode'])->name('ide.nodes.rename');
    Route::patch('/ide/nodes/{node}/save',   [IdeController::class, 'saveContent'])->name('ide.nodes.save');
    
    Route::patch('/ide/nodes/{node}/move',   [IdeController::class, 'moveNode'])->name('ide.nodes.move');
    
    Route::delete('/ide/nodes/{node}',       [IdeController::class, 'deleteNode'])->name('ide.nodes.delete');
 
    // Run
    Route::post('/ide/nodes/{node}/run',     [IdeController::class, 'runNode'])->name('ide.nodes.run');
});