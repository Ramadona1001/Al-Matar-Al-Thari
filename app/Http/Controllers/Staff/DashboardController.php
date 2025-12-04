<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:staff']);
    }

    public function index(Request $request)
    {
        $staff = Staff::query()->where('user_id', $request->user()->id)->firstOrFail();
        abort_unless($staff->is_verified, 403);

        return view('staff.dashboard', compact('staff'));
    }
}

