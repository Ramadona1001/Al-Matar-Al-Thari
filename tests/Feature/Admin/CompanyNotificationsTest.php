<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\User;
use App\Notifications\CompanyStatusChangedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CompanyNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'super-admin']);
        Role::create(['name' => 'merchant']);
    }

    /** @test */
    public function merchant_receives_notification_when_company_is_approved(): void
    {
        Notification::fake();

        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $merchant = User::factory()->create();
        $merchant->assignRole('merchant');

        $company = Company::create([
            'name' => 'Pending Co',
            'email' => 'pending@example.com',
            'user_id' => $merchant->id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.companies.approve', $company))
            ->assertRedirect(route('admin.companies.index'));

        Notification::assertSentTo(
            $merchant,
            CompanyStatusChangedNotification::class,
            function ($notification) use ($company) {
                return $notification->toArray($company->user)['status'] === 'approved';
            }
        );
    }

    /** @test */
    public function merchant_receives_notification_when_company_is_rejected(): void
    {
        Notification::fake();

        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $merchant = User::factory()->create();
        $merchant->assignRole('merchant');

        $company = Company::create([
            'name' => 'Pending Co',
            'email' => 'pending@example.com',
            'user_id' => $merchant->id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.companies.reject', $company), ['rejection_reason' => 'Incomplete docs'])
            ->assertRedirect(route('admin.companies.index'));

        Notification::assertSentTo(
            $merchant,
            CompanyStatusChangedNotification::class,
            function ($notification) use ($company) {
                return $notification->toArray($company->user)['status'] === 'rejected';
            }
        );
    }
}
