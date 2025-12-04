<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\PosDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PosDeviceController extends Controller
{
    /**
     * Display a listing of POS devices for the merchant's company
     */
    public function index()
    {
        $company = Auth::user()->company;
        
        if (!$company) {
            return redirect()->route('merchant.dashboard')
                ->with('error', 'You do not have a company assigned.');
        }

        $devices = PosDevice::where('company_id', $company->id)
            ->with(['branch'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('merchant.pos-devices.index', compact('devices'));
    }

    /**
     * Show the form for creating a new POS device
     */
    public function create()
    {
        $company = Auth::user()->company;
        
        if (!$company) {
            return redirect()->route('merchant.dashboard')
                ->with('error', 'You do not have a company assigned.');
        }

        $branches = $company->branches()->where('status', 'active')->get();

        return view('merchant.pos-devices.create', compact('branches'));
    }

    /**
     * Store a newly created POS device
     */
    public function store(Request $request)
    {
        $company = Auth::user()->company;
        
        if (!$company) {
            return redirect()->route('merchant.dashboard')
                ->with('error', 'You do not have a company assigned.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:pos_devices',
            'branch_id' => 'nullable|exists:branches,id',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Ensure branch belongs to company
        if ($request->branch_id) {
            $branch = $company->branches()->find($request->branch_id);
            if (!$branch) {
                return redirect()->back()
                    ->with('error', 'Selected branch does not belong to your company.')
                    ->withInput();
            }
        }

        $device = PosDevice::create([
            'device_id' => $this->generateDeviceId(),
            'company_id' => $company->id,
            'branch_id' => $request->branch_id,
            'name' => $request->name,
            'model' => $request->model,
            'serial_number' => $request->serial_number,
            'api_key' => $this->generateApiKey(),
            'status' => $request->status,
            'settings' => json_encode([
                'receipt_header' => $request->receipt_header ?? '',
                'receipt_footer' => $request->receipt_footer ?? '',
                'auto_print' => $request->auto_print ?? true,
                'sound_enabled' => $request->sound_enabled ?? true,
            ])
        ]);

        return redirect()->route('merchant.pos-devices.index')
            ->with('success', 'POS device created successfully.');
    }

    /**
     * Display the specified POS device
     */
    public function show(PosDevice $posDevice)
    {
        $company = Auth::user()->company;
        
        // Ensure device belongs to merchant's company
        if ($posDevice->company_id !== $company->id) {
            return redirect()->route('merchant.pos-devices.index')
                ->with('error', 'Unauthorized access to this device.');
        }

        $device = $posDevice->load(['company', 'branch', 'transactions' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }]);

        return view('merchant.pos-devices.show', compact('device'));
    }

    /**
     * Show the form for editing the specified POS device
     */
    public function edit(PosDevice $posDevice)
    {
        $company = Auth::user()->company;
        
        // Ensure device belongs to merchant's company
        if ($posDevice->company_id !== $company->id) {
            return redirect()->route('merchant.pos-devices.index')
                ->with('error', 'Unauthorized access to this device.');
        }

        $branches = $company->branches()->where('status', 'active')->get();
        $device = $posDevice;

        return view('merchant.pos-devices.edit', compact('device', 'branches'));
    }

    /**
     * Update the specified POS device
     */
    public function update(Request $request, PosDevice $posDevice)
    {
        $company = Auth::user()->company;
        
        // Ensure device belongs to merchant's company
        if ($posDevice->company_id !== $company->id) {
            return redirect()->route('merchant.pos-devices.index')
                ->with('error', 'Unauthorized access to this device.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:pos_devices,serial_number,' . $posDevice->id,
            'branch_id' => 'nullable|exists:branches,id',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Ensure branch belongs to company
        if ($request->branch_id) {
            $branch = $company->branches()->find($request->branch_id);
            if (!$branch) {
                return redirect()->back()
                    ->with('error', 'Selected branch does not belong to your company.')
                    ->withInput();
            }
        }

        $settings = json_decode($posDevice->settings, true) ?? [];
        $settings = array_merge($settings, [
            'receipt_header' => $request->receipt_header ?? '',
            'receipt_footer' => $request->receipt_footer ?? '',
            'auto_print' => $request->auto_print ?? true,
            'sound_enabled' => $request->sound_enabled ?? true,
        ]);

        $posDevice->update([
            'name' => $request->name,
            'model' => $request->model,
            'serial_number' => $request->serial_number,
            'branch_id' => $request->branch_id,
            'status' => $request->status,
            'settings' => json_encode($settings)
        ]);

        return redirect()->route('merchant.pos-devices.index')
            ->with('success', 'POS device updated successfully.');
    }

    /**
     * Regenerate API key for the device
     */
    public function regenerateApiKey(PosDevice $posDevice)
    {
        $company = Auth::user()->company;
        
        // Ensure device belongs to merchant's company
        if ($posDevice->company_id !== $company->id) {
            return redirect()->route('merchant.pos-devices.index')
                ->with('error', 'Unauthorized access to this device.');
        }

        $newApiKey = $this->generateApiKey();
        $posDevice->update(['api_key' => $newApiKey]);

        return redirect()->route('merchant.pos-devices.show', $posDevice)
            ->with('success', 'API key regenerated successfully.');
    }

    /**
     * Remove the specified POS device
     */
    public function destroy(PosDevice $posDevice)
    {
        $company = Auth::user()->company;
        
        // Ensure device belongs to merchant's company
        if ($posDevice->company_id !== $company->id) {
            return redirect()->route('merchant.pos-devices.index')
                ->with('error', 'Unauthorized access to this device.');
        }

        // Check if device has any transactions
        if ($posDevice->transactions()->count() > 0) {
            return redirect()->route('merchant.pos-devices.index')
                ->with('error', 'Cannot delete device with existing transactions.');
        }

        $posDevice->delete();

        return redirect()->route('merchant.pos-devices.index')
            ->with('success', 'POS device deleted successfully.');
    }

    /**
     * Generate unique device ID
     */
    private function generateDeviceId()
    {
        return 'POS-' . strtoupper(Str::random(8)) . '-' . date('YmdHis');
    }

    /**
     * Generate secure API key
     */
    private function generateApiKey()
    {
        return 'pos_' . bin2hex(random_bytes(32));
    }
}