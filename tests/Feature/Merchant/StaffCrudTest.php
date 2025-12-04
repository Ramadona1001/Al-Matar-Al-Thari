<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class StaffCrudTest extends TestCase
{
    use RefreshDatabase;
    public function test_merchant_can_create_staff()
    {
        $user = User::create([
            'name' => 'Merchant User',
            'first_name' => 'Merchant',
            'last_name' => 'User',
            'email' => 'merchant@example.com',
            'password' => Hash::make('password'),
        ]);

        $company = Company::create([
            'name' => 'Test Co',
            'email' => 'company@example.com',
            'status' => 'approved',
            'user_id' => $user->id,
        ]);

        $user->company_id = $company->id;
        $user->save();

        $this->actingAs($user);

        $response = $this->post(route('merchant.staff.store'), [
            'name' => 'Staff Member',
            'email' => 'staff@example.com',
            'phone' => '1234567890',
            'role' => 'staff',
        ]);

        $response->assertRedirect(route('merchant.staff.index'));
    }
}