<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Testimonial;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $testimonials = [
            [
                'name' => 'Humble D. Dow',
                'position' => 'Head of Idea',
                'company' => 'Creative Agency',
                'testimonial' => 'Started with a simple idea: Deliver quality, well-designed landscape for home, business and public spaces.',
                'rating' => 5,
                'is_featured' => true,
                'order' => 1,
                'is_active' => true,
                'locale' => 'en',
            ],
            [
                'name' => 'Rosalina D. William',
                'position' => 'Founder',
                'company' => 'Green Solutions',
                'testimonial' => 'One of the best gardening tips you\'ll ever get is to plan your new garden near a water source.',
                'rating' => 5,
                'is_featured' => true,
                'order' => 2,
                'is_active' => true,
                'locale' => 'en',
            ],
            [
                'name' => 'Miranda H. Halim',
                'position' => 'CEO of Halim Co.',
                'company' => 'Halim Co.',
                'testimonial' => 'When starting a garden, one of the top pieces of advice is to invest in soil that is nutrient rich & well drained.',
                'rating' => 5,
                'is_featured' => true,
                'order' => 3,
                'is_active' => true,
                'locale' => 'en',
            ],
            [
                'name' => 'John Smith',
                'position' => 'Homeowner',
                'company' => null,
                'testimonial' => 'Excellent service and beautiful results. My garden has never looked better!',
                'rating' => 5,
                'is_featured' => false,
                'order' => 4,
                'is_active' => true,
                'locale' => 'en',
            ],
        ];

        foreach ($testimonials as $testimonialData) {
            Testimonial::create($testimonialData);
        }
    }
}

