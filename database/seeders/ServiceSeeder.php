<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'title' => 'Garden landscaping',
                'short_description' => 'Professional garden design and landscaping services.',
                'description' => 'The laying out and care of a plot of ground devoted partially or wholly to the growing of plants such as flowers, herbs, or vegetables.',
                'icon' => 'flaticon-gardening',
                'order' => 1,
                'is_active' => true,
                'is_featured' => true,
                'locale' => 'en',
            ],
            [
                'title' => 'Soil making & carbon',
                'short_description' => 'Soil preparation and carbon enrichment services.',
                'description' => 'Professional soil preparation, enrichment, and carbon management to ensure optimal plant growth and health.',
                'icon' => 'flaticon-soil',
                'order' => 2,
                'is_active' => true,
                'is_featured' => true,
                'locale' => 'en',
            ],
            [
                'title' => 'Planting plants',
                'short_description' => 'Expert plant selection and installation services.',
                'description' => 'We help you choose the right plants for your garden and provide professional planting services to ensure their success.',
                'icon' => 'flaticon-plant',
                'order' => 3,
                'is_active' => true,
                'is_featured' => false,
                'locale' => 'en',
            ],
            [
                'title' => 'Tree trimming',
                'short_description' => 'Professional tree maintenance and trimming services.',
                'description' => 'Keep your trees healthy and beautiful with our expert trimming and maintenance services.',
                'icon' => 'flaticon-tree',
                'order' => 4,
                'is_active' => true,
                'is_featured' => false,
                'locale' => 'en',
            ],
        ];

        foreach ($services as $serviceData) {
            Service::create([
                'slug' => Str::slug($serviceData['title']),
                'title' => $serviceData['title'],
                'short_description' => $serviceData['short_description'],
                'description' => $serviceData['description'],
                'icon' => $serviceData['icon'],
                'order' => $serviceData['order'],
                'is_active' => $serviceData['is_active'],
                'is_featured' => $serviceData['is_featured'],
                'locale' => $serviceData['locale'],
            ]);
        }
    }
}

