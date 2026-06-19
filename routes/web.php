<?php

use App\Http\Controllers\AllotmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EntryGateController;
use App\Http\Controllers\ExitGateController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Redirect root: login jika belum auth, pos/entry jika sudah
Route::get('/', fn () => auth()->check() ? redirect()->route('pos.entry') : redirect()->route('login'));

// Semua user yang sudah login (staff & admin)
Route::middleware(['auth'])->group(function () {
    Route::get('/pos/entry', [EntryGateController::class, 'index'])->name('pos.entry');
    Route::get('/pos/exit', [ExitGateController::class, 'index'])->name('pos.exit');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Hanya admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/allotment', [AllotmentController::class, 'index'])->name('allotment');
    Route::get('/members', [MemberController::class, 'index'])->name('members');
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get('/rates', [RateController::class, 'index'])->name('rates');
    Route::get('/report', [ReportController::class, 'index'])->name('report');
});

require __DIR__.'/auth.php';
