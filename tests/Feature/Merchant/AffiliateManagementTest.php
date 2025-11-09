<?php

namespace Tests\Feature\Merchant;

use App\Models\Affiliate;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AffiliateManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'merchant']);
        Role::create(['name' => 'customer']);
    }

    /** @test */
    public function merchant_can_update_affiliate_status(): void
    {
        $merchant = User::factory()->create();
        $merchant->assignRole('merchant');

        $company = Company::create([
            'name' => 'Merchant Co',
            'email' => 'merchant@example.com',
            'user_id' => $merchant->id,
            'status' => 'approved',
        ]);

        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $affiliate = Affiliate::create([
            'user_id' => $customer->id,
            'company_id' => $company->id,
            'referral_code' => Affiliate::generateUniqueReferralCode(),
            'referral_link' => 'http://example.com',
            'commission_rate' => 5,
            'commission_type' => 'percentage',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($merchant)->patch(route('merchant.affiliates.update-status', $affiliate), [
            'status' => 'approved',
        ]);

        $response->assertRedirect(route('merchant.affiliates.index'));
        $this->assertDatabaseHas('affiliates', [
            'id' => $affiliate->id,
            'status' => 'approved',
        ]);
    }
}
