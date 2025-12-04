<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanyPartner;

class CompanyPartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partners = [
            [
                'name' => 'Partner Company 1',
                'logo_path' => null,
                'website_url' => 'https://example.com',
                'order' => 1,
                'is_active' => true,
                'locale' => 'en',
            ],
            [
                'name' => 'Partner Company 2',
                'logo_path' => null,
                'website_url' => 'https://example.com',
                'order' => 2,
                'is_active' => true,
                'locale' => 'en',
            ],
            [
                'name' => 'Partner Company 3',
                'logo_path' => null,
                'website_url' => 'https://example.com',
                'order' => 3,
                'is_active' => true,
                'locale' => 'en',
            ],
            [
                'name' => 'Partner Company 4',
                'logo_path' => null,
                'website_url' => 'https://example.com',
                'order' => 4,
                'is_active' => true,
                'locale' => 'en',
            ],
            [
                'name' => 'Partner Company 5',
                'logo_path' => null,
                'website_url' => 'https://example.com',
                'order' => 5,
                'is_active' => true,
                'locale' => 'en',
            ],
        ];

        foreach ($partners as $partnerData) {
            CompanyPartner::create($partnerData);
        }
    }
}

