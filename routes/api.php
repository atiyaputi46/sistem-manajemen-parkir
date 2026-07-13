<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Route;

// Rute grup API publik dengan perlindungan rate limiter maksimal 60 request per menit
Route::middleware('throttle:60,1')->group(function (): void {
    // Mendapatkan kapasitas slot kosong real-time
    Route::get('/available-slots', [ApiController::class, 'availableSlots']);

    // Mendapatkan daftar tarif aktif terbaru
    Route::get('/rates', [ApiController::class, 'rates']);

    // Mengirim form pendaftaran online member baru
    Route::post('/members', [ApiController::class, 'registerMember']);
});
