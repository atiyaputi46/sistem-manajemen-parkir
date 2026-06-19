<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class ReportController extends Controller
{
    /**
     * Display the Report page.
     */
    public function index(): View
    {
        return view('report');
    }
}
