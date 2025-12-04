<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Master CMS Seeder
 * This seeder calls all CMS-related seeders in the correct order
 */
class CmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            // Seed sections first (needed by section items)
            SectionSeeder::class,
            // Seed section items (depends on sections)
            SectionItemSeeder::class,
            // Seed other independent entities
            BannerSeeder::class,
            MenuSeeder::class,
            ServiceSeeder::class,
            BlogSeeder::class,
            TestimonialSeeder::class,
            StatisticSeeder::class,
            HowItWorksStepSeeder::class,
            CompanyPartnerSeeder::class,
            PageSeeder::class,
        ]);
    }
}

