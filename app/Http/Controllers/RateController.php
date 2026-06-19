<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class RateController extends Controller
{
    /**
     * Display the Payments & Rates management page.
     */
    public function index(): View
    {
        return view('rates');
    }
}
