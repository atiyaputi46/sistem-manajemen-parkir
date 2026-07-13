<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * Controller untuk mengelola halaman Manajemen Karyawan.
 * Merender halaman Blade yang memuat komponen Livewire user-management.
 */
class UserController extends Controller
{
    /**
     * Menampilkan halaman Manajemen Karyawan.
     */
    public function index(): View
    {
        return view('users');
    }
}
