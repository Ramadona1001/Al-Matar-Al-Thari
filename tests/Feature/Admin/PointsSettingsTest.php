<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\PointsSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PointsSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'super-admin']);
    }

    /** @test */
    public function super_admin_can_update_points_settings(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $response = $this->actingAs($admin)->put(route('admin.points.update'), [
            'earn_rate' => 8,
            'redeem_rate' => 0.2,
            'referral_bonus_points' => 100,
            'auto_approve_redemptions' => true,
        ]);

        $response->assertRedirect(route('admin.points.edit'));

        $this->assertDatabaseHas('points_settings', [
            'earn_rate' => 8,
            'redeem_rate' => 0.2,
            'referral_bonus_points' => 100,
            'auto_approve_redemptions' => true,
        ]);
    }
}
