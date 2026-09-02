<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LearningMaterialController;
use App\Http\Controllers\PromptController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/materi', [LearningMaterialController::class, 'index'])->name('learning.index');
Route::get('/materi/{slug}', [LearningMaterialController::class, 'show'])->name('learning.show');

Route::get('/prompts', [PromptController::class, 'index'])->name('prompts.index');
Route::post('/prompts/{prompt}/copy', [PromptController::class, 'trackCopy'])->name('prompts.copy');

Route::get('/agenda', [EventController::class, 'index'])->name('events.index');
Route::get('/agenda/{slug}', [EventController::class, 'show'])->name('events.show');
