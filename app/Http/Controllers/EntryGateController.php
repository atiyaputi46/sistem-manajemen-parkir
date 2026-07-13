<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * Controller untuk mengelola Gerbang Masuk (Entry Gate).
 * Memuat halaman POS gerbang masuk untuk pencatatan kendaraan tiba.
 */
class EntryGateController extends Controller
{
    /**
     * Menampilkan halaman POS Gerbang Masuk.
     */
    public function index(): View
    {
        return view('pos.entry');
    }
}
