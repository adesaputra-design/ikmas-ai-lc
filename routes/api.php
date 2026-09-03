<?php

use App\Http\Controllers\Api\HermesController;
use Illuminate\Support\Facades\Route;

Route::prefix('hermes')->name('hermes.')->group(function () {
    Route::get('/status', [HermesController::class, 'status'])->name('status');
    Route::get('/latest', [HermesController::class, 'latest'])->name('latest');
    Route::get('/stats', [HermesController::class, 'stats'])->name('stats');
    Route::get('/members/pending', [HermesController::class, 'pendingMembers'])->name('members.pending');
    Route::get('/events/{slug}/preview', [HermesController::class, 'eventPreview'])->name('events.preview');
});
