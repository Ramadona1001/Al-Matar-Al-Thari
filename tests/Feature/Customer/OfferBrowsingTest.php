<?php

namespace Tests\Feature\Customer;

use App\Models\Company;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OfferBrowsingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'customer']);
        Role::create(['name' => 'merchant']);
    }

    /** @test */
    public function customer_can_view_offer_listing()
    {
        $user = User::factory()->create();
        $user->assignRole('customer');

        $merchant = User::factory()->create();
        $merchant->assignRole('merchant');

        $company = Company::create([
            'name' => 'Test Company',
            'email' => 'company@example.com',
            'user_id' => $merchant->id,
            'status' => 'approved',
        ]);

        Offer::create([
            'title' => ['en' => 'Test Offer'],
            'description' => ['en' => 'Great discount'],
            'type' => 'percentage',
            'discount_percentage' => 10,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
            'status' => 'active',
            'company_id' => $company->id,
        ]);

        $this->actingAs($user)
            ->get(route('customer.offers.index'))
            ->assertStatus(200)
            ->assertSee('Test Offer');
    }
}
