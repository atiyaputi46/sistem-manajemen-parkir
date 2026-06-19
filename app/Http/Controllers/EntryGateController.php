<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class EntryGateController extends Controller
{
    /**
     * Display the Entry Gate POS page.
     */
    public function index(): View
    {
        return view('pos.entry');
    }
}
