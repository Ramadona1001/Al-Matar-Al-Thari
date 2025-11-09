<?php

namespace Tests\Feature\Merchant;

use App\Models\Company;
use App\Models\Offer;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\NewOfferNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OfferNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'merchant']);
        Role::create(['name' => 'customer']);
    }

    /** @test */
    public function customers_with_past_transactions_receive_new_offer_notification(): void
    {
        Notification::fake();

        $merchant = User::factory()->create();
        $merchant->assignRole('merchant');

        $company = Company::create([
            'name' => 'Great Deals',
            'email' => 'company@example.com',
            'user_id' => $merchant->id,
            'status' => 'approved',
        ]);

        $customer = User::factory()->create();
        $customer->assignRole('customer');

        Transaction::create([
            'transaction_id' => 'TXN-12345',
            'amount' => 100,
            'discount_amount' => 0,
            'final_amount' => 100,
            'loyalty_points_earned' => 0,
            'loyalty_points_used' => 0,
            'status' => 'completed',
            'payment_method' => 'cash',
            'user_id' => $customer->id,
            'company_id' => $company->id,
        ]);

        $payload = [
            'title_en' => 'Mega Sale',
            'description_en' => 'Up to 30% off',
            'type' => 'percentage',
            'discount_percentage' => 30,
            'start_date' => now()->addHour()->format('Y-m-d'),
            'end_date' => now()->addDays(7)->format('Y-m-d'),
            'status' => 'active',
        ];

        $this->actingAs($merchant)
            ->post(route('merchant.offers.store'), $payload)
            ->assertRedirect(route('merchant.offers.index'));

        Notification::assertSentTo(
            $customer,
            NewOfferNotification::class
        );
    }
}
