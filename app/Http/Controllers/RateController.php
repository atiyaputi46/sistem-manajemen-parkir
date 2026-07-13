<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * Controller untuk mengelola halaman Konfigurasi Tarif (Payments & Rates).
 * Merender halaman Blade yang memuat komponen Livewire rate-management.
 */
class RateController extends Controller
{
    /**
     * Menampilkan halaman Manajemen Tarif dan Pembayaran.
     */
    public function index(): View
    {
        return view('rates');
    }
}
