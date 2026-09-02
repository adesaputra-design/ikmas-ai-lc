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
