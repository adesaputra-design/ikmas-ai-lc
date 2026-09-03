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
Route::redirect('/register', '/register/alumni', 301);

Route::get('/register/alumni', [AuthController::class, 'showAlumniRegisterForm'])->name('register.alumni');
Route::post('/register/alumni', [AuthController::class, 'registerAlumni'])->name('register.alumni.submit');

Route::get('/register/subscriber', [AuthController::class, 'showSubscriberRegisterForm'])->name('register.subscriber');
Route::post('/register/subscriber', [AuthController::class, 'registerSubscriber'])->name('register.subscriber.submit');
Route::get('/register/subscriber/pending', [AuthController::class, 'subscriberPending'])->name('register.subscriber.pending');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Member Routes (Authenticated)
Route::middleware('auth')->prefix('member')->name('member.')->group(function () {
    Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');
    Route::get('/showcase/create', [MemberDashboardController::class, 'createShowcase'])->name('showcase.create');
    Route::post('/showcase', [MemberDashboardController::class, 'storeShowcase'])->name('showcase.store');
    Route::post('/bookmarks/toggle', [MemberDashboardController::class, 'toggleBookmark'])->name('bookmarks.toggle');
    Route::post('/password', [MemberDashboardController::class, 'updatePassword'])->name('password.update');
});

// Admin Routes (Authenticated & Admin or Staff Role)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');

    // Modul Materi
    Route::middleware('permission:materials')->group(function () {
        Route::resource('materi', \App\Http\Controllers\Admin\AdminLearningMaterialController::class);
    });

    // Modul Prompts
    Route::middleware('permission:prompts')->group(function () {
        Route::resource('prompts', \App\Http\Controllers\Admin\AdminPromptController::class);
    });

    // Modul Agenda
    Route::middleware('permission:events')->group(function () {
        Route::resource('agenda', \App\Http\Controllers\Admin\AdminEventController::class);
        Route::get('agenda/{agenda}/broadcast-text', [\App\Http\Controllers\Admin\AdminEventController::class, 'getBroadcastText'])->name('agenda.broadcast');
    });

    // Modul Kurasi Showcase
    Route::middleware('permission:curation')->group(function () {
        Route::get('/curation', [\App\Http\Controllers\Admin\AdminShowcaseCurationController::class, 'index'])->name('curation.index');
        Route::post('/curation/{showcase}/approve', [\App\Http\Controllers\Admin\AdminShowcaseCurationController::class, 'approve'])->name('curation.approve');
        Route::post('/curation/{showcase}/reject', [\App\Http\Controllers\Admin\AdminShowcaseCurationController::class, 'reject'])->name('curation.reject');
        Route::post('/curation/{showcase}/toggle-featured', [\App\Http\Controllers\Admin\AdminShowcaseCurationController::class, 'toggleFeatured'])->name('curation.toggle-featured');
        Route::delete('/curation/{showcase}', [\App\Http\Controllers\Admin\AdminShowcaseCurationController::class, 'destroy'])->name('curation.destroy');
    });

    // Modul Alumni (Direktori)
    Route::middleware('permission:alumni')->group(function () {
        Route::get('/alumni', [\App\Http\Controllers\Admin\AdminAlumniController::class, 'index'])->name('alumni.index');
        Route::get('/alumni/export', [\App\Http\Controllers\Admin\AdminAlumniController::class, 'exportCsv'])->name('alumni.export');
        Route::post('/alumni/{user}/reset-password', [\App\Http\Controllers\Admin\AdminAlumniController::class, 'resetPassword'])->name('alumni.reset-password');
    });

    // Modul Halaman Tentang
    Route::middleware('permission:pages')->group(function () {
        Route::get('/tentang', [\App\Http\Controllers\Admin\AdminTentangController::class, 'index'])->name('tentang.index');
        Route::post('/tentang', [\App\Http\Controllers\Admin\AdminTentangController::class, 'update'])->name('tentang.update');
    });

    // Modul Kelola Tim & Staf + Hapus Anggota (Eksklusif Admin Utama)
    Route::middleware('permission:manage_team')->group(function () {
        Route::get('/team', [\App\Http\Controllers\Admin\AdminTeamController::class, 'index'])->name('team.index');
        Route::post('/team/{user}/role', [\App\Http\Controllers\Admin\AdminTeamController::class, 'updateRole'])->name('team.update-role');
        Route::delete('/team/{user}', [\App\Http\Controllers\Admin\AdminTeamController::class, 'destroy'])->name('team.destroy');
        Route::post('/team/{id}/restore', [\App\Http\Controllers\Admin\AdminTeamController::class, 'restore'])->name('team.restore');
        Route::post('/team/{user}/reset-password', [\App\Http\Controllers\Admin\AdminTeamController::class, 'resetPassword'])->name('team.reset-password');

        Route::delete('/alumni/{user}', [\App\Http\Controllers\Admin\AdminAlumniController::class, 'destroy'])->name('alumni.destroy');
        Route::post('/alumni/{id}/restore', [\App\Http\Controllers\Admin\AdminAlumniController::class, 'restore'])->name('alumni.restore');
    });

    // Modul Subscriber
    Route::middleware('permission:subscribers')->group(function () {
        Route::get('/subscribers', [\App\Http\Controllers\Admin\AdminSubscriberController::class, 'index'])->name('subscribers.index');
        Route::post('/subscribers/{user}/approve', [\App\Http\Controllers\Admin\AdminSubscriberController::class, 'approve'])->name('subscribers.approve');
        Route::post('/subscribers/{user}/reject', [\App\Http\Controllers\Admin\AdminSubscriberController::class, 'reject'])->name('subscribers.reject');
        Route::delete('/subscribers/{user}', [\App\Http\Controllers\Admin\AdminSubscriberController::class, 'destroy'])->name('subscribers.destroy');
    });
});

