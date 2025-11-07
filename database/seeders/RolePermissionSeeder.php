<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $this->createPermissions();

        // Create roles and assign permissions
        $this->createRoles();
    }

    /**
     * Create all permissions
     */
    private function createPermissions(): void
    {
        // User Management Permissions
        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'delete users']);
        Permission::create(['name' => 'manage users']);

        // Company Management Permissions
        Permission::create(['name' => 'view companies']);
        Permission::create(['name' => 'create companies']);
        Permission::create(['name' => 'edit companies']);
        Permission::create(['name' => 'delete companies']);
        Permission::create(['name' => 'manage companies']);

        // Branch Management Permissions
        Permission::create(['name' => 'view branches']);
        Permission::create(['name' => 'create branches']);
        Permission::create(['name' => 'edit branches']);
        Permission::create(['name' => 'delete branches']);
        Permission::create(['name' => 'manage branches']);

        // Category Management Permissions
        Permission::create(['name' => 'view categories']);
        Permission::create(['name' => 'create categories']);
        Permission::create(['name' => 'edit categories']);
        Permission::create(['name' => 'delete categories']);
        Permission::create(['name' => 'manage categories']);

        // Offer Management Permissions
        Permission::create(['name' => 'view offers']);
        Permission::create(['name' => 'create offers']);
        Permission::create(['name' => 'edit offers']);
        Permission::create(['name' => 'delete offers']);
        Permission::create(['name' => 'manage offers']);

        // Coupon Management Permissions
        Permission::create(['name' => 'view coupons']);
        Permission::create(['name' => 'create coupons']);
        Permission::create(['name' => 'edit coupons']);
        Permission::create(['name' => 'delete coupons']);
        Permission::create(['name' => 'manage coupons']);

        // Digital Card Management Permissions
        Permission::create(['name' => 'view digital cards']);
        Permission::create(['name' => 'create digital cards']);
        Permission::create(['name' => 'edit digital cards']);
        Permission::create(['name' => 'delete digital cards']);
        Permission::create(['name' => 'manage digital cards']);

        // Loyalty Points Permissions
        Permission::create(['name' => 'view loyalty points']);
        Permission::create(['name' => 'create loyalty points']);
        Permission::create(['name' => 'edit loyalty points']);
        Permission::create(['name' => 'delete loyalty points']);
        Permission::create(['name' => 'manage loyalty points']);

        // Affiliate Marketing Permissions
        Permission::create(['name' => 'view affiliates']);
        Permission::create(['name' => 'create affiliates']);
        Permission::create(['name' => 'edit affiliates']);
        Permission::create(['name' => 'delete affiliates']);
        Permission::create(['name' => 'manage affiliates']);

        // Transaction Permissions
        Permission::create(['name' => 'view transactions']);
        Permission::create(['name' => 'create transactions']);
        Permission::create(['name' => 'edit transactions']);
        Permission::create(['name' => 'delete transactions']);
        Permission::create(['name' => 'manage transactions']);

        // Notification Permissions
        Permission::create(['name' => 'view notifications']);
        Permission::create(['name' => 'create notifications']);
        Permission::create(['name' => 'edit notifications']);
        Permission::create(['name' => 'delete notifications']);
        Permission::create(['name' => 'manage notifications']);

        // Report Permissions
        Permission::create(['name' => 'view reports']);
        Permission::create(['name' => 'create reports']);
        Permission::create(['name' => 'manage reports']);

        // System Permissions
        Permission::create(['name' => 'access admin panel']);
        Permission::create(['name' => 'access merchant panel']);
        Permission::create(['name' => 'access customer portal']);
        Permission::create(['name' => 'manage settings']);
        Permission::create(['name' => 'view dashboard']);
    }

    /**
     * Create roles and assign permissions
     */
    private function createRoles(): void
    {
        // Super Admin Role
        $superAdmin = Role::create(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin Role
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo([
            'view users', 'create users', 'edit users', 'manage users',
            'view companies', 'create companies', 'edit companies', 'manage companies',
            'view branches', 'create branches', 'edit branches', 'manage branches',
            'view categories', 'create categories', 'edit categories', 'manage categories',
            'view offers', 'create offers', 'edit offers', 'manage offers',
            'view coupons', 'create coupons', 'edit coupons', 'manage coupons',
            'view digital cards', 'create digital cards', 'edit digital cards', 'manage digital cards',
            'view loyalty points', 'create loyalty points', 'edit loyalty points', 'manage loyalty points',
            'view affiliates', 'create affiliates', 'edit affiliates', 'manage affiliates',
            'view transactions', 'create transactions', 'edit transactions', 'manage transactions',
            'view notifications', 'create notifications', 'edit notifications', 'manage notifications',
            'view reports', 'create reports', 'manage reports',
            'access admin panel', 'view dashboard', 'manage settings'
        ]);

        // Merchant Role
        $merchant = Role::create(['name' => 'merchant']);
        $merchant->givePermissionTo([
            'view companies', 'edit companies',
            'view branches', 'create branches', 'edit branches', 'manage branches',
            'view categories', 'view offers', 'create offers', 'edit offers', 'manage offers',
            'view coupons', 'create coupons', 'edit coupons', 'manage coupons',
            'view digital cards', 'create digital cards', 'edit digital cards', 'manage digital cards',
            'view loyalty points', 'create loyalty points', 'edit loyalty points', 'manage loyalty points',
            'view affiliates', 'view transactions', 'view notifications',
            'view reports', 'access merchant panel', 'view dashboard'
        ]);

        // Customer Role
        $customer = Role::create(['name' => 'customer']);
        $customer->givePermissionTo([
            'view offers', 'view coupons', 'view digital cards',
            'view loyalty points', 'view transactions', 'view notifications',
            'access customer portal', 'view dashboard'
        ]);
    }
}