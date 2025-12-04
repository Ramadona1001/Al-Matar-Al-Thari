<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HowItWorksStep;

class HowItWorksStepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $steps = [
            [
                'title' => 'Register',
                'description' => 'Create an account to access personalized offers.',
                'icon' => 'fas fa-user-plus',
                'step_number' => 1,
                'order' => 1,
                'is_active' => true,
                'locale' => 'en',
            ],
            [
                'title' => 'Browse',
                'description' => 'Explore featured companies and deals tailored to you.',
                'icon' => 'fas fa-search',
                'step_number' => 2,
                'order' => 2,
                'is_active' => true,
                'locale' => 'en',
            ],
            [
                'title' => 'Engage',
                'description' => 'Contact companies or redeem offers securely within the platform.',
                'icon' => 'fas fa-check',
                'step_number' => 3,
                'order' => 3,
                'is_active' => true,
                'locale' => 'en',
            ],
            [
                'title' => 'Enjoy',
                'description' => 'Enjoy great offers and rewards from top companies.',
                'icon' => 'fas fa-heart',
                'step_number' => 4,
                'order' => 4,
                'is_active' => true,
                'locale' => 'en',
            ],
        ];

        foreach ($steps as $stepData) {
            HowItWorksStep::create($stepData);
        }
    }
}

