<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class MemberController extends Controller
{
    /**
     * Display the Members management page.
     */
    public function index(): View
    {
        return view('members');
    }
}
