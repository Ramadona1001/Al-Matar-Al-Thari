<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\DigitalCard;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class FreezeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin|admin']);
    }

    /**
     * Freeze a user account.
     */
    public function freezeUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $user->update([
            'is_frozen' => true,
            'frozen_reason' => $validated['reason'],
            'frozen_by' => auth()->id(),
            'frozen_at' => now(),
        ]);

        AuditLogService::logAccountFrozen($user, $validated['reason'], auth()->user());

        return redirect()->back()
            ->with('success', __('User account frozen successfully.'));
    }

    /**
     * Unfreeze a user account.
     */
    public function unfreezeUser(User $user)
    {
        $user->update([
            'is_frozen' => false,
            'frozen_reason' => null,
            'frozen_by' => null,
            'frozen_at' => null,
        ]);

        AuditLogService::log(
            'account_unfrozen',
            $user,
            "User account unfrozen",
            ['is_frozen' => true],
            ['is_frozen' => false]
        );

        return redirect()->back()
            ->with('success', __('User account unfrozen successfully.'));
    }

    /**
     * Freeze a company account.
     */
    public function freezeCompany(Request $request, Company $company)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $company->update([
            'is_frozen' => true,
            'frozen_reason' => $validated['reason'],
            'frozen_by' => auth()->id(),
            'frozen_at' => now(),
        ]);

        AuditLogService::logAccountFrozen($company, $validated['reason'], auth()->user());

        return redirect()->back()
            ->with('success', __('Company account frozen successfully.'));
    }

    /**
     * Unfreeze a company account.
     */
    public function unfreezeCompany(Company $company)
    {
        $company->update([
            'is_frozen' => false,
            'frozen_reason' => null,
            'frozen_by' => null,
            'frozen_at' => null,
        ]);

        AuditLogService::log(
            'account_unfrozen',
            $company,
            "Company account unfrozen",
            ['is_frozen' => true],
            ['is_frozen' => false]
        );

        return redirect()->back()
            ->with('success', __('Company account unfrozen successfully.'));
    }

    /**
     * Freeze a digital card.
     */
    public function freezeCard(Request $request, DigitalCard $card)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $card->update([
            'is_frozen' => true,
            'frozen_reason' => $validated['reason'],
            'frozen_by' => auth()->id(),
            'frozen_at' => now(),
            'status' => 'blocked',
        ]);

        AuditLogService::logCardFrozen($card, $validated['reason'], auth()->user());

        return redirect()->back()
            ->with('success', __('Digital card frozen successfully.'));
    }

    /**
     * Unfreeze a digital card.
     */
    public function unfreezeCard(DigitalCard $card)
    {
        $card->update([
            'is_frozen' => false,
            'frozen_reason' => null,
            'frozen_by' => null,
            'frozen_at' => null,
            'status' => 'active',
        ]);

        AuditLogService::log(
            'card_unfrozen',
            $card,
            "Digital card unfrozen",
            ['is_frozen' => true, 'status' => 'blocked'],
            ['is_frozen' => false, 'status' => 'active']
        );

        return redirect()->back()
            ->with('success', __('Digital card unfrozen successfully.'));
    }
}
