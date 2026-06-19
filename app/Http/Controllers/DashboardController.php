<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Dashboard overview page.
     */
    public function index(): View
    {
        return view('dashboard');
    }
}
