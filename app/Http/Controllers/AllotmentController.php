<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * Controller untuk mengelola halaman Alokasi Slot Parkir (Allotment Map).
 * Hanya memuat dan merender halaman Blade yang berisi komponen Livewire allotment map.
 */
class AllotmentController extends Controller
{
    /**
     * Menampilkan halaman peta visual alokasi slot parkir.
     */
    public function index(): View
    {
        return view('allotment');
    }
}
