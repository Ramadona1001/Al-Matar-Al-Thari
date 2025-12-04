<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $userType = $request->input('user_type', 'customer');
        
        // Base validation rules
        $rules = [
            'user_type' => ['required', 'in:customer,merchant'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['required', 'accepted'],
        ];

        // Customer validation rules
        if ($userType === 'customer') {
            $rules['name'] = ['required', 'string', 'max:255'];
            $rules['email'] = ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class];
            $rules['phone'] = ['nullable', 'string', 'max:20'];
        }

        // Merchant validation rules
        if ($userType === 'merchant') {
            $rules['company_name'] = ['required', 'string', 'max:255'];
            $rules['merchant_name'] = ['required', 'string', 'max:255'];
            $rules['merchant_email'] = ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class];
            $rules['merchant_phone'] = ['required', 'string', 'max:20'];
            $rules['tax_number'] = ['nullable', 'string', 'max:100'];
            $rules['commercial_register'] = ['nullable', 'string', 'max:100'];
            $rules['address'] = ['nullable', 'string', 'max:500'];
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            if ($userType === 'customer') {
                // Split name into first_name and last_name
                $names = explode(' ', $validated['name'], 2);
                $firstName = $names[0] ?? $validated['name'];
                $lastName = $names[1] ?? '';

                $user = User::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? null,
                    'password' => Hash::make($validated['password']),
                    'locale' => app()->getLocale(),
                    'user_type' => 'customer',
                ]);

                // Assign customer role
                $customerRole = Role::findByName('customer');
                if ($customerRole) {
                    $user->assignRole($customerRole);
                }
            } else {
                // Merchant registration
                // Split merchant name into first_name and last_name
                $names = explode(' ', $validated['merchant_name'], 2);
                $firstName = $names[0] ?? $validated['merchant_name'];
                $lastName = $names[1] ?? '';

                $user = User::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $validated['merchant_email'],
                    'phone' => $validated['merchant_phone'],
                    'password' => Hash::make($validated['password']),
                    'locale' => app()->getLocale(),
                    'user_type' => 'merchant',
                ]);

                // Assign merchant role
                $merchantRole = Role::findByName('merchant');
                if ($merchantRole) {
                    $user->assignRole($merchantRole);
                }

                // Create company for merchant
                $companyName = [
                    app()->getLocale() => $validated['company_name']
                ];
                
                Company::create([
                    'name' => $companyName,
                    'email' => $validated['merchant_email'],
                    'phone' => $validated['merchant_phone'],
                    'address' => $validated['address'] ?? null,
                    'tax_number' => $validated['tax_number'] ?? null,
                    'commercial_register' => $validated['commercial_register'] ?? null,
                    'user_id' => $user->id,
                    'status' => 'pending', // Requires admin approval
                ]);
            }

            event(new Registered($user));

            Auth::login($user);

            DB::commit();

            // Redirect to localized role-based dashboard
            $locale = session(config('localization.locale_session_key'))
                ?? $request->route('locale')
                ?? app()->getLocale()
                ?? config('localization.default_locale', 'en');

            // Show success message
            if ($userType === 'merchant') {
                return redirect()->route('dashboard', ['locale' => $locale])
                    ->with('success', __('Your account has been created successfully! Your company registration is pending admin approval.'));
            }

            return redirect()->route('dashboard', ['locale' => $locale])
                ->with('success', __('Registration successful! Welcome to our platform.'));
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->withErrors(['error' => __('Registration failed. Please try again.')]);
        }
    }
}
