<?php

namespace Tests\Feature\Customer;

use App\Models\Affiliate;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AffiliateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'customer']);
        Role::create(['name' => 'merchant']);
    }

    /** @test */
    public function customer_can_apply_for_affiliate_program(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $merchant = User::factory()->create();
        $merchant->assignRole('merchant');

        $company = Company::create([
            'name' => 'Affiliate Co',
            'email' => 'affiliate@example.com',
            'user_id' => $merchant->id,
            'status' => 'approved',
        ]);

        $response = $this->actingAs($customer)->post(route('customer.affiliate.store'), [
            'company_id' => $company->id,
        ]);

        $response->assertRedirect(route('customer.affiliate.index'));
        $this->assertDatabaseHas('affiliates', [
            'user_id' => $customer->id,
            'company_id' => $company->id,
            'status' => 'pending',
        ]);
    }
}
