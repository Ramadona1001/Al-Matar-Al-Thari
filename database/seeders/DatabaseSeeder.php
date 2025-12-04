<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Disable foreign key checks to avoid constraint issues
        Schema::disableForeignKeyConstraints();

        $this->call([
            RolePermissionSeeder::class,
            UserSeeder::class,
            PointsSettingSeeder::class,
            // CMS Seeders
            BannerSeeder::class,
            SectionSeeder::class,
            SectionItemSeeder::class,
            MenuSeeder::class,
            ServiceSeeder::class,
            BlogSeeder::class,
            TestimonialSeeder::class,
            StatisticSeeder::class,
            HowItWorksStepSeeder::class,
            CompanyPartnerSeeder::class,
            PageSeeder::class,
            FooterMenuGroupSeeder::class,
            AboutSectionsSeeder::class,
        ]);

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();
    }
}
