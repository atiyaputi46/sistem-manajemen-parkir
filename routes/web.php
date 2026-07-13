<?php

use App\Http\Controllers\AllotmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EntryGateController;
use App\Http\Controllers\ExitGateController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Rute Akar (Root Route): Pengalihan ke login jika belum auth, ke POS gerbang masuk jika sudah login
Route::get('/', fn () => auth()->check() ? redirect()->route('pos.entry') : redirect()->route('login'));

// Rute Grup Internal: Hanya dapat diakses oleh pengguna terautentikasi (role staff & admin)
Route::middleware(['auth'])->group(function () {
    // POS Gerbang Masuk
    Route::get('/pos/entry', [EntryGateController::class, 'index'])->name('pos.entry');

    // POS Gerbang Keluar
    Route::get('/pos/exit', [ExitGateController::class, 'index'])->name('pos.exit');

    // Pengelolaan profil akun pengguna yang sedang login
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rute Grup Admin: Hanya dapat diakses oleh pengguna dengan role 'admin'
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Dashboard ringkasan analitik
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Peta visual alokasi slot parkir (Allotment Map)
    Route::get('/allotment', [AllotmentController::class, 'index'])->name('allotment');

    // Manajemen pendaftaran member langganan
    Route::get('/members', [MemberController::class, 'index'])->name('members');

    // Manajemen akun karyawan (tambah/edit/hapus staff)
    Route::get('/users', [UserController::class, 'index'])->name('users');

    // Konfigurasi tarif aktif parkir
    Route::get('/rates', [RateController::class, 'index'])->name('rates');

    // Halaman laporan transaksi masuk-keluar
    Route::get('/report', [ReportController::class, 'index'])->name('report');

    // Ekspor laporan transaksi ke format Excel & PDF
    Route::get('/report/export/excel', [ReportExportController::class, 'exportExcel'])->name('report.export.excel');
    Route::get('/report/export/pdf', [ReportExportController::class, 'exportPdf'])->name('report.export.pdf');
});

// Memuat berkas rute autentikasi bawaan (Fortify/Breeze)
require __DIR__.'/auth.php';
