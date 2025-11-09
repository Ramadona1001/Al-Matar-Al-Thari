<?php

namespace Tests\Feature\Admin;

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

        Role::create(['name' => 'super-admin']);
        Role::create(['name' => 'merchant']);
        Role::create(['name' => 'customer']);
    }

    /** @test */
    public function admin_can_change_affiliate_status(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $merchant = User::factory()->create();
        $merchant->assignRole('merchant');

        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $company = Company::create([
            'name' => 'Company',
            'email' => 'company@example.com',
            'user_id' => $merchant->id,
            'status' => 'approved',
        ]);

        $affiliate = Affiliate::create([
            'user_id' => $customer->id,
            'company_id' => $company->id,
            'referral_code' => Affiliate::generateUniqueReferralCode(),
            'referral_link' => 'http://example.com',
            'commission_rate' => 5,
            'commission_type' => 'percentage',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.affiliates.update-status', $affiliate), [
            'status' => 'approved',
        ]);

        $response->assertRedirect(route('admin.affiliates.index'));
        $this->assertDatabaseHas('affiliates', [
            'id' => $affiliate->id,
            'status' => 'approved',
        ]);
    }
}
