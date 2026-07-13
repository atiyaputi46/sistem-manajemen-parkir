<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * Controller untuk mengelola halaman Manajemen Member.
 * Merender halaman Blade yang memuat komponen Livewire member-management.
 */
class MemberController extends Controller
{
    /**
     * Menampilkan halaman Manajemen Member.
     */
    public function index(): View
    {
        return view('members');
    }
}
