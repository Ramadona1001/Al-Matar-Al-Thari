<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\Affiliate;
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
            $rules['agreement'] = ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240']; // 10MB max, PDF or Word documents
        }

        $validated = $request->validate($rules);

        
        DB::beginTransaction();
        try {
            if ($userType === 'customer') {
                // Split name into first_name and last_name
                $names = explode(' ', $validated['name'], 2);
                $firstName = $names[0] ?? $validated['name'];
                $lastName = $names[1] ?? '';
                

                // Handle referral code if present
                $referredByUserId = null;
                $referralCode = $request->input('ref') ?? $request->query('ref');
                
                if ($referralCode) {
                    // Find affiliate by referral code
                    $affiliate = Affiliate::where('referral_code', $referralCode)
                        ->where('status', 'active')
                        ->first();
                    
                    if ($affiliate && $affiliate->user_id) {
                        // Prevent self-referral
                        // We can't check email here since user doesn't exist yet, but we'll check in Job
                        $referredByUserId = $affiliate->user_id;
                    }
                }

                $user = User::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? null,
                    'password' => Hash::make($validated['password']),
                    'locale' => app()->getLocale(),
                    'user_type' => 'customer',
                    'referred_by_user_id' => $referredByUserId, // Lock referral attribution
                ]);

                // Create affiliate account automatically for customer
                if ($user->user_type === 'customer' && !$user->affiliate) {
                    $affiliateCode = Affiliate::generateUniqueReferralCode();
                    Affiliate::create([
                        'user_id' => $user->id,
                        'company_id' => null,
                        'offer_id' => null,
                        'referral_code' => $affiliateCode,
                        'referral_link' => config('app.url') . '/register?ref=' . $affiliateCode,
                        'commission_rate' => 0,
                        'commission_type' => 'percentage',
                        'status' => 'active',
                    ]);
                }

                // Assign customer role
                $customerRole = Role::findByName('customer');
                if ($customerRole) {
                    $user->assignRole($customerRole);
                }
                
                // Store referral code in session/cookie for later use (for transactions)
                if ($referralCode) {
                    session(['referral_code' => $referralCode]);
                    // Also store in cookie for persistence
                    cookie()->queue('referral_code', $referralCode, 30 * 24 * 60); // 30 days
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
                
                // Handle agreement file upload
                $agreementFilePath = null;
                if ($request->hasFile('agreement')) {
                    $agreementFile = $request->file('agreement');
                    $agreementFileName = time() . '_' . $user->id . '_agreement.' . $agreementFile->getClientOriginalExtension();
                    $agreementFilePath = $agreementFile->storeAs('companies/agreements', $agreementFileName, 'public');
                }
                
                Company::create([
                    'name' => $companyName,
                    'email' => $validated['merchant_email'],
                    'phone' => $validated['merchant_phone'],
                    'address' => $validated['address'] ?? null,
                    'tax_number' => $validated['tax_number'] ?? null,
                    'commercial_register' => $validated['commercial_register'] ?? null,
                    'agreement_file' => $agreementFilePath,
                    'user_id' => $user->id,
                    'status' => 'pending', // Requires admin approval
                ]);
            }
            

            event(new Registered($user));

            DB::commit();

            // Redirect to email verification page
            $locale = session(config('localization.locale_session_key'))
                ?? $request->route('locale')
                ?? app()->getLocale()
                ?? config('localization.default_locale', 'en');

            // Log the user in temporarily so they can verify their email
            Auth::login($user);

            return redirect()->route('verification.notice', ['locale' => $locale])
                ->with('status', __('Registration successful! Please verify your email address to continue.'));
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->withErrors(['error' => __('Registration failed. Please try again.')]);
        }
    }
}
