<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->group(function (): void {
    Route::get('/available-slots', [ApiController::class, 'availableSlots']);
    Route::get('/rates', [ApiController::class, 'rates']);
    Route::post('/members', [ApiController::class, 'registerMember']);
});
