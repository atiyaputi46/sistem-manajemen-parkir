<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * Controller untuk mengelola halaman Laporan.
 * Merender halaman Blade yang memuat komponen Livewire report-page.
 */
class ReportController extends Controller
{
    /**
     * Menampilkan halaman Laporan Transaksi.
     */
    public function index(): View
    {
        return view('report');
    }
}
