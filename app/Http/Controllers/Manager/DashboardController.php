<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:manager']);
    }

    public function index()
    {
        return view('manager.dashboard');
    }
}

