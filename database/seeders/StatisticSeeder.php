<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Statistic;

class StatisticSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statistics = [
            [
                'label' => 'Total Customers',
                'value' => '3200',
                'suffix' => '+',
                'icon' => 'fas fa-users',
                'description' => 'Happy customers served',
                'order' => 1,
                'is_active' => true,
                'locale' => 'en',
            ],
            [
                'label' => 'Total Companies',
                'value' => '150',
                'suffix' => '+',
                'icon' => 'fas fa-building',
                'description' => 'Partner companies',
                'order' => 2,
                'is_active' => true,
                'locale' => 'en',
            ],
            [
                'label' => 'Completed Orders',
                'value' => '5000',
                'suffix' => '+',
                'icon' => 'fas fa-check-circle',
                'description' => 'Successful transactions',
                'order' => 3,
                'is_active' => true,
                'locale' => 'en',
            ],
            [
                'label' => 'Active Services',
                'value' => '25',
                'suffix' => '+',
                'icon' => 'fas fa-concierge-bell',
                'description' => 'Available services',
                'order' => 4,
                'is_active' => true,
                'locale' => 'en',
            ],
        ];

        foreach ($statistics as $statisticData) {
            Statistic::create($statisticData);
        }
    }
}

