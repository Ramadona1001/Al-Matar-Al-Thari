<?php

namespace Tests\Feature\Customer;

use App\Models\LoyaltyPoint;
use App\Models\PointRedemption;
use App\Models\User;
use App\Services\PointsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PointRedemptionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'customer']);

        // seed default settings
        app(PointsService::class)->settings();
    }

    /** @test */
    public function customer_can_request_point_redemption(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        LoyaltyPoint::create([
            'user_id' => $customer->id,
            'points' => 200,
            'type' => 'earned',
        ]);

        $response = $this->actingAs($customer)->post(route('customer.loyalty.redeem'), [
            'points' => 100,
        ]);

        $response->assertRedirect(route('customer.loyalty.index'));

        $this->assertDatabaseHas('point_redemptions', [
            'user_id' => $customer->id,
            'points' => 100,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('loyalty_points', [
            'user_id' => $customer->id,
            'points' => 100,
            'type' => 'redeemed',
        ]);
    }
}
