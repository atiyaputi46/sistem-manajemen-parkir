<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * Controller untuk mengelola Gerbang Keluar (Exit Gate).
 * Memuat halaman POS gerbang keluar untuk kalkulasi biaya dan pembayaran parkir.
 */
class ExitGateController extends Controller
{
    /**
     * Menampilkan halaman POS Gerbang Keluar.
     */
    public function index(): View
    {
        return view('pos.exit');
    }
}
