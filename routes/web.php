<?php

use App\Http\Controllers\Admin\AdminAlumniController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminEventController;
use App\Http\Controllers\Admin\AdminLearningMaterialController;
use App\Http\Controllers\Admin\AdminLibraryChapterController;
use App\Http\Controllers\Admin\AdminLibraryController;
use App\Http\Controllers\Admin\AdminPromptController;
use App\Http\Controllers\Admin\AdminShowcaseCurationController;
use App\Http\Controllers\Admin\AdminSubscriberController;
use App\Http\Controllers\Admin\AdminTeamController;
use App\Http\Controllers\Admin\AdminTentangController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LearningMaterialController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\Member\MemberDashboardController;
use App\Http\Controllers\Member\MemberLibrarySubmissionController;
use App\Http\Controllers\PromptController;
use App\Http\Controllers\ShowcaseController;
use App\Http\Controllers\TentangController;
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

Route::get('/tentang', [TentangController::class, 'index'])->name('tentang');

// Modul Pustaka AI (Library)
Route::get('/library', [LibraryController::class, 'index'])->name('library.index');
Route::get('/library/{slug}', [LibraryController::class, 'show'])->name('library.show');

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
    Route::get('/library/create', [MemberLibrarySubmissionController::class, 'create'])->name('library.create');
    Route::post('/library', [MemberLibrarySubmissionController::class, 'store'])->name('library.store');
    Route::post('/bookmarks/toggle', [MemberDashboardController::class, 'toggleBookmark'])->name('bookmarks.toggle');
    Route::post('/password', [MemberDashboardController::class, 'updatePassword'])->name('password.update');
});

// Admin Routes (Authenticated & Admin or Staff Role)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Modul Materi
    Route::middleware('permission:materials')->group(function () {
        Route::resource('materi', AdminLearningMaterialController::class);
    });

    // Modul Prompts
    Route::middleware('permission:prompts')->group(function () {
        Route::resource('prompts', AdminPromptController::class);
    });

    // Modul Agenda
    Route::middleware('permission:events')->group(function () {
        Route::resource('agenda', AdminEventController::class);
        Route::get('agenda/{agenda}/broadcast-text', [AdminEventController::class, 'getBroadcastText'])->name('agenda.broadcast');
    });

    // Modul Kurasi Showcase
    Route::middleware('permission:curation')->group(function () {
        Route::get('/curation', [AdminShowcaseCurationController::class, 'index'])->name('curation.index');
        Route::post('/curation/{showcase}/approve', [AdminShowcaseCurationController::class, 'approve'])->name('curation.approve');
        Route::post('/curation/{showcase}/reject', [AdminShowcaseCurationController::class, 'reject'])->name('curation.reject');
        Route::post('/curation/{showcase}/toggle-featured', [AdminShowcaseCurationController::class, 'toggleFeatured'])->name('curation.toggle-featured');
        Route::delete('/curation/{showcase}', [AdminShowcaseCurationController::class, 'destroy'])->name('curation.destroy');
    });

    // Modul Alumni (Direktori)
    Route::middleware('permission:alumni')->group(function () {
        Route::get('/alumni', [AdminAlumniController::class, 'index'])->name('alumni.index');
        Route::get('/alumni/export', [AdminAlumniController::class, 'exportCsv'])->name('alumni.export');
        Route::post('/alumni/{user}/reset-password', [AdminAlumniController::class, 'resetPassword'])->name('alumni.reset-password');
    });

    // Modul Halaman Tentang
    Route::middleware('permission:pages')->group(function () {
        Route::get('/tentang', [AdminTentangController::class, 'index'])->name('tentang.index');
        Route::post('/tentang', [AdminTentangController::class, 'update'])->name('tentang.update');
    });

    // Modul Kelola Tim & Staf + Hapus Anggota (Eksklusif Admin Utama)
    Route::middleware('permission:manage_team')->group(function () {
        Route::get('/team', [AdminTeamController::class, 'index'])->name('team.index');
        Route::get('/team/{user}/role', fn () => redirect()->route('admin.team.index'));
        Route::post('/team/{user}/role', [AdminTeamController::class, 'updateRole'])->name('team.update-role');
        Route::delete('/team/{user}', [AdminTeamController::class, 'destroy'])->name('team.destroy');
        Route::post('/team/{id}/restore', [AdminTeamController::class, 'restore'])->name('team.restore');
        Route::post('/team/{user}/reset-password', [AdminTeamController::class, 'resetPassword'])->name('team.reset-password');

        Route::delete('/alumni/{user}', [AdminAlumniController::class, 'destroy'])->name('alumni.destroy');
        Route::post('/alumni/{id}/restore', [AdminAlumniController::class, 'restore'])->name('alumni.restore');
    });

    // Modul Subscriber
    Route::middleware('permission:subscribers')->group(function () {
        Route::get('/subscribers', [AdminSubscriberController::class, 'index'])->name('subscribers.index');
        Route::post('/subscribers/{user}/approve', [AdminSubscriberController::class, 'approve'])->name('subscribers.approve');
        Route::post('/subscribers/{user}/reject', [AdminSubscriberController::class, 'reject'])->name('subscribers.reject');
        Route::delete('/subscribers/{user}', [AdminSubscriberController::class, 'destroy'])->name('subscribers.destroy');
    });

    // Modul Pustaka AI & Kurasi Karya Ilmiah
    Route::middleware('permission:library')->group(function () {
        Route::resource('library', AdminLibraryController::class)->parameters(['library' => 'item']);
        Route::post('/library/{item}/approve', [AdminLibraryController::class, 'approve'])->name('library.approve');
        Route::post('/library/{item}/reject', [AdminLibraryController::class, 'reject'])->name('library.reject');
        Route::get('/library/{item}/chapters', [AdminLibraryChapterController::class, 'index'])->name('library.chapters.index');
        Route::post('/library/{item}/chapters', [AdminLibraryChapterController::class, 'store'])->name('library.chapters.store');
        Route::get('/library/{item}/preview', [AdminLibraryChapterController::class, 'preview'])->name('library.preview');
        Route::get('/library/{item}/preview/bab/{chapterSlug}', [AdminLibraryChapterController::class, 'previewChapter'])->name('library.preview.chapter');
    });
});
