<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LearningMaterialController;
use App\Http\Controllers\Member\MemberDashboardController;
use App\Http\Controllers\PromptController;
use App\Http\Controllers\ShowcaseController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/materi', [LearningMaterialController::class, 'index'])->name('learning.index');
Route::get('/materi/{slug}', [LearningMaterialController::class, 'show'])->name('learning.show');

Route::get('/prompts', [PromptController::class, 'index'])->name('prompts.index');
Route::post('/prompts/{prompt}/copy', [PromptController::class, 'trackCopy'])->name('prompts.copy');

Route::get('/agenda', [EventController::class, 'index'])->name('events.index');
Route::get('/agenda/{slug}', [EventController::class, 'show'])->name('events.show');

Route::get('/showcase', [ShowcaseController::class, 'index'])->name('showcase.index');
Route::get('/showcase/{slug}', [ShowcaseController::class, 'show'])->name('showcase.show');

Route::get('/tentang', [\App\Http\Controllers\TentangController::class, 'index'])->name('tentang');


// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Member Routes (Authenticated)
Route::middleware('auth')->prefix('member')->name('member.')->group(function () {
    Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');
    Route::get('/showcase/create', [MemberDashboardController::class, 'createShowcase'])->name('showcase.create');
    Route::post('/showcase', [MemberDashboardController::class, 'storeShowcase'])->name('showcase.store');
    Route::post('/bookmarks/toggle', [MemberDashboardController::class, 'toggleBookmark'])->name('bookmarks.toggle');
});

// Admin Routes (Authenticated & Admin Role)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('materi', \App\Http\Controllers\Admin\AdminLearningMaterialController::class);
    Route::resource('prompts', \App\Http\Controllers\Admin\AdminPromptController::class);
    Route::resource('agenda', \App\Http\Controllers\Admin\AdminEventController::class);
    Route::get('agenda/{agenda}/broadcast-text', [\App\Http\Controllers\Admin\AdminEventController::class, 'getBroadcastText'])->name('agenda.broadcast');
    Route::get('/curation', [\App\Http\Controllers\Admin\AdminShowcaseCurationController::class, 'index'])->name('curation.index');
    Route::post('/curation/{showcase}/approve', [\App\Http\Controllers\Admin\AdminShowcaseCurationController::class, 'approve'])->name('curation.approve');
    Route::post('/curation/{showcase}/reject', [\App\Http\Controllers\Admin\AdminShowcaseCurationController::class, 'reject'])->name('curation.reject');
    Route::post('/curation/{showcase}/toggle-featured', [\App\Http\Controllers\Admin\AdminShowcaseCurationController::class, 'toggleFeatured'])->name('curation.toggle-featured');
    Route::delete('/curation/{showcase}', [\App\Http\Controllers\Admin\AdminShowcaseCurationController::class, 'destroy'])->name('curation.destroy');
    Route::get('/alumni', [\App\Http\Controllers\Admin\AdminAlumniController::class, 'index'])->name('alumni.index');
    Route::get('/alumni/export', [\App\Http\Controllers\Admin\AdminAlumniController::class, 'exportCsv'])->name('alumni.export');
    Route::get('/tentang', [\App\Http\Controllers\Admin\AdminTentangController::class, 'index'])->name('tentang.index');
    Route::post('/tentang', [\App\Http\Controllers\Admin\AdminTentangController::class, 'update'])->name('tentang.update');
});
