<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function companyId()
    {
        // Assuming authenticated user belongs to a company via relationship
        $user = Auth::user();
        return $user->company_id ?? null;
    }

    public function index()
    {
        $companyId = $this->companyId();
        $staff = Staff::where('company_id', $companyId)->orderBy('name')->paginate(15);
        return view('merchant.staff.index', compact('staff'));
    }

    public function create()
    {
        return view('merchant.staff.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'role' => 'required|string|max:50',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Staff::create([
            'company_id' => $this->companyId(),
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'status' => $request->status ?? true,
        ]);

        return redirect()->route('merchant.staff.index')->with('success', 'Staff member created successfully.');
    }

    public function edit(Staff $staff)
    {
        $this->authorizeStaff($staff);
        return view('merchant.staff.edit', compact('staff'));
    }

    public function update(Request $request, Staff $staff)
    {
        $this->authorizeStaff($staff);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'role' => 'required|string|max:50',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $staff->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'status' => $request->status ?? $staff->status,
        ]);

        return redirect()->route('merchant.staff.index')->with('success', 'Staff member updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        $this->authorizeStaff($staff);
        $staff->delete();
        return redirect()->route('merchant.staff.index')->with('success', 'Staff member deleted successfully.');
    }

    protected function authorizeStaff(Staff $staff)
    {
        abort_unless($staff->company_id === $this->companyId(), 403);
    }
}