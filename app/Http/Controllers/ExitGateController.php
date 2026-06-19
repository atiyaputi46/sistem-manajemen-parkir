<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class ExitGateController extends Controller
{
    /**
     * Display the Exit Gate POS page.
     */
    public function index(): View
    {
        return view('pos.exit');
    }
}
