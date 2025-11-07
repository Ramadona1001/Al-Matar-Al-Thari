<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        $this->createSuperAdmin();

        // Create Admin Users
        $this->createAdminUsers();

        // Create Merchant Users
        $this->createMerchantUsers();

        // Create Customer Users
        $this->createCustomerUsers();

        // Create additional random users
        $this->createAdditionalUsers();
    }

    /**
     * Create Super Admin
     */
    private function createSuperAdmin(): void
    {
        $superAdmin = User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => '+1234567890',
            'date_of_birth' => '1990-01-01',
            'gender' => 'male',
            'locale' => 'en',
            'user_type' => 'admin',
            'is_active' => true,
            'remember_token' => \Illuminate\Support\Str::random(10),
        ]);

        $superAdmin->assignRole('super-admin');
    }

    /**
     * Create Admin Users
     */
    private function createAdminUsers(): void
    {
        $admin1 = User::create([
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => '+1234567891',
            'date_of_birth' => '1985-05-15',
            'gender' => 'male',
            'locale' => 'en',
            'user_type' => 'admin',
            'is_active' => true,
            'remember_token' => \Illuminate\Support\Str::random(10),
        ]);
        $admin1->assignRole('admin');

        $admin2 = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin2@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => '+1234567892',
            'date_of_birth' => '1988-08-20',
            'gender' => 'female',
            'locale' => 'ar',
            'user_type' => 'admin',
            'is_active' => true,
            'remember_token' => \Illuminate\Support\Str::random(10),
        ]);
        $admin2->assignRole('admin');
    }

    /**
     * Create Merchant Users
     */
    private function createMerchantUsers(): void
    {
        // Create merchant users first
        $merchant1 = User::create([
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'merchant@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => '+1234567893',
            'date_of_birth' => '1980-03-10',
            'gender' => 'male',
            'locale' => 'en',
            'user_type' => 'merchant',
            'is_active' => true,
            'remember_token' => \Illuminate\Support\Str::random(10),
        ]);
        $merchant1->assignRole('merchant');

        $merchant2 = User::create([
            'first_name' => 'Sarah',
            'last_name' => 'Johnson',
            'email' => 'merchant2@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => '+1234567894',
            'date_of_birth' => '1982-07-22',
            'gender' => 'female',
            'locale' => 'en',
            'user_type' => 'merchant',
            'is_active' => true,
            'remember_token' => \Illuminate\Support\Str::random(10),
        ]);
        $merchant2->assignRole('merchant');

        $merchant3 = User::create([
            'first_name' => 'Ahmed',
            'last_name' => 'Hassan',
            'email' => 'merchant3@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => '+1234567895',
            'date_of_birth' => '1978-11-15',
            'gender' => 'male',
            'locale' => 'ar',
            'user_type' => 'merchant',
            'is_active' => true,
            'remember_token' => \Illuminate\Support\Str::random(10),
        ]);
        $merchant3->assignRole('merchant');

        $merchant4 = User::create([
            'first_name' => 'Maria',
            'last_name' => 'Garcia',
            'email' => 'merchant4@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => '+1234567896',
            'date_of_birth' => '1985-02-28',
            'gender' => 'female',
            'locale' => 'en',
            'user_type' => 'merchant',
            'is_active' => true,
            'remember_token' => \Illuminate\Support\Str::random(10),
        ]);
        $merchant4->assignRole('merchant');

        // Create companies and link to merchants
        $company1 = Company::create([
            'name' => 'Tech Solutions Inc.',
            'email' => 'info@techsolutions.com',
            'phone' => '+1234567800',
            'address' => '123 Business Street, Tech City',
            'description' => 'Leading technology solutions provider',
            'website' => 'https://techsolutions.com',
            'status' => 'approved',
            'user_id' => $merchant1->id,
        ]);

        $company2 = Company::create([
            'name' => 'Fashion Boutique',
            'email' => 'contact@fashionboutique.com',
            'phone' => '+1234567801',
            'address' => '456 Fashion Avenue, Style City',
            'description' => 'Premium fashion and accessories',
            'website' => 'https://fashionboutique.com',
            'status' => 'approved',
            'user_id' => $merchant2->id,
        ]);

        $company3 = Company::create([
            'name' => 'Food & Beverage Co.',
            'email' => 'hello@foodbev.com',
            'phone' => '+1234567802',
            'address' => '789 Restaurant Road, Food City',
            'description' => 'Fine dining and catering services',
            'website' => 'https://foodbev.com',
            'status' => 'approved',
            'user_id' => $merchant3->id,
        ]);
    }

    /**
     * Create Customer Users
     */
    private function createCustomerUsers(): void
    {
        $customer1 = User::create([
            'first_name' => 'Alice',
            'last_name' => 'Brown',
            'email' => 'customer@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => '+1234567897',
            'date_of_birth' => '1990-04-12',
            'gender' => 'female',
            'locale' => 'en',
            'user_type' => 'customer',
            'is_active' => true,
            'remember_token' => \Illuminate\Support\Str::random(10),
        ]);
        $customer1->assignRole('customer');

        $customer2 = User::create([
            'first_name' => 'Bob',
            'last_name' => 'Wilson',
            'email' => 'customer2@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => '+1234567898',
            'date_of_birth' => '1988-09-25',
            'gender' => 'male',
            'locale' => 'en',
            'user_type' => 'customer',
            'is_active' => true,
            'remember_token' => \Illuminate\Support\Str::random(10),
        ]);
        $customer2->assignRole('customer');

        $customer3 = User::create([
            'first_name' => 'Fatima',
            'last_name' => 'Al-Rashid',
            'email' => 'customer3@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => '+1234567899',
            'date_of_birth' => '1992-12-08',
            'gender' => 'female',
            'locale' => 'ar',
            'user_type' => 'customer',
            'is_active' => true,
            'remember_token' => \Illuminate\Support\Str::random(10),
        ]);
        $customer3->assignRole('customer');

        $customer4 = User::create([
            'first_name' => 'David',
            'last_name' => 'Wilson',
            'email' => 'customer4@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => '+1234567800',
            'date_of_birth' => '1991-06-18',
            'gender' => 'male',
            'locale' => 'en',
            'user_type' => 'customer',
            'is_active' => true,
            'remember_token' => \Illuminate\Support\Str::random(10),
        ]);
        $customer4->assignRole('customer');

        $customer5 = User::create([
            'first_name' => 'Lisa',
            'last_name' => 'Anderson',
            'email' => 'customer5@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => '+1234567801',
            'date_of_birth' => '1993-10-30',
            'gender' => 'female',
            'locale' => 'en',
            'user_type' => 'customer',
            'is_active' => true,
            'remember_token' => \Illuminate\Support\Str::random(10),
        ]);
        $customer5->assignRole('customer');
    }

    /**
     * Create additional random users
     */
    private function createAdditionalUsers(): void
    {
        // Create 10 additional customers
        $customers = User::factory(10)->create();
        foreach ($customers as $customer) {
            $customer->assignRole('customer');
        }

        // Create 5 additional merchants
        $merchants = User::factory(5)->create();
        foreach ($merchants as $merchant) {
            $merchant->assignRole('merchant');
        }

        // Create 15 more random users with detailed attributes
        for ($i = 1; $i <= 15; $i++) {
            $user = User::create([
                'first_name' => fake()->firstName,
                'last_name' => fake()->lastName,
                'email' => fake()->unique()->safeEmail,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'phone' => fake()->phoneNumber,
                'date_of_birth' => fake()->date('Y-m-d', '2000-01-01'),
                'gender' => fake()->randomElement(['male', 'female']),
                'locale' => fake()->randomElement(['en', 'ar']),
                'user_type' => fake()->randomElement(['customer', 'merchant']),
                'is_active' => fake()->boolean(80),
                'remember_token' => \Illuminate\Support\Str::random(10),
            ]);

            // Randomly assign roles to additional users
            $roles = ['customer', 'merchant'];
            $randomRole = fake()->randomElement($roles);
            $user->assignRole($randomRole);
        }
    }
}