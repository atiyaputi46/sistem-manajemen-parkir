<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class UserController extends Controller
{
    /**
     * Display the Users management page.
     */
    public function index(): View
    {
        return view('users');
    }
}
