<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * Controller untuk mengelola halaman Dashboard Utama Admin.
 * Merender halaman Blade dashboard yang memuat komponen Livewire admin-dashboard.
 */
class DashboardController extends Controller
{
    /**
     * Menampilkan halaman ikhtisar analitik / dashboard admin.
     */
    public function index(): View
    {
        return view('dashboard');
    }
}
