<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class AllotmentController extends Controller
{
    /**
     * Display the Allotment (slot map) page.
     */
    public function index(): View
    {
        return view('allotment');
    }
}
