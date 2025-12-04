<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Section;
use App\Models\SectionItem;

class SectionItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get sections
        $aboutSection = Section::where('name', 'about-section')->first();
        $faqSection = Section::where('name', 'faq-section')->first();
        $portfolioSection = Section::where('name', 'portfolio-section')->first();

        if ($aboutSection) {
            // Progress items for About section
            $progressItems = [
                [
                    'section_id' => $aboutSection->id,
                    'title' => 'Landscaping ground',
                    'subtitle' => null,
                    'content' => null,
                    'icon' => null,
                    'metadata' => ['type' => 'progress', 'value' => 75],
                    'order' => 1,
                    'is_active' => true,
                    'locale' => 'en',
                ],
                [
                    'section_id' => $aboutSection->id,
                    'title' => 'Soil re-building',
                    'subtitle' => null,
                    'content' => null,
                    'icon' => null,
                    'metadata' => ['type' => 'progress', 'value' => 82],
                    'order' => 2,
                    'is_active' => true,
                    'locale' => 'en',
                ],
            ];

            foreach ($progressItems as $item) {
                SectionItem::create([
                    'section_id' => $item['section_id'],
                    'title' => $item['title'],
                    'subtitle' => $item['subtitle'],
                    'content' => $item['content'],
                    'icon' => $item['icon'],
                    'metadata' => $item['metadata'],
                    'order' => $item['order'],
                    'is_active' => $item['is_active'],
                    'locale' => $item['locale'],
                ]);
            }
        }

        if ($faqSection) {
            // FAQ items
            $faqItems = [
                [
                    'section_id' => $faqSection->id,
                    'title' => 'What does an inch of water mean?',
                    'subtitle' => null,
                    'content' => 'Gardens consist of a mix of natural and constructed elements, although even very natural gardens are always an inherently artificial creation. Natural elements present in a garden principally.',
                    'order' => 1,
                    'is_active' => true,
                    'locale' => 'en',
                ],
                [
                    'section_id' => $faqSection->id,
                    'title' => 'How does gardening make you feel?',
                    'subtitle' => null,
                    'content' => 'Gardens consist of a mix of natural and constructed elements, although even very natural gardens are always an inherently artificial creation. Natural elements present in a garden principally.',
                    'order' => 2,
                    'is_active' => true,
                    'locale' => 'en',
                ],
                [
                    'section_id' => $faqSection->id,
                    'title' => 'How often should I fertilize my plants?',
                    'subtitle' => null,
                    'content' => 'Gardens consist of a mix of natural and constructed elements, although even very natural gardens are always an inherently artificial creation. Natural elements present in a garden principally.',
                    'order' => 3,
                    'is_active' => true,
                    'locale' => 'en',
                ],
                [
                    'section_id' => $faqSection->id,
                    'title' => 'Do gardens help the environment?',
                    'subtitle' => null,
                    'content' => 'Gardens consist of a mix of natural and constructed elements, although even very natural gardens are always an inherently artificial creation. Natural elements present in a garden principally.',
                    'order' => 4,
                    'is_active' => true,
                    'locale' => 'en',
                ],
            ];

            foreach ($faqItems as $item) {
                SectionItem::create([
                    'section_id' => $item['section_id'],
                    'title' => $item['title'],
                    'subtitle' => $item['subtitle'],
                    'content' => $item['content'],
                    'order' => $item['order'],
                    'is_active' => $item['is_active'],
                    'locale' => $item['locale'],
                ]);
            }
        }

        if ($portfolioSection) {
            // Portfolio items
            $portfolioItems = [
                [
                    'section_id' => $portfolioSection->id,
                    'title' => 'Lawn and garden maintenance',
                    'subtitle' => 'Gardening',
                    'content' => null,
                    'image_path' => null,
                    'link' => '#',
                    'order' => 1,
                    'is_active' => true,
                    'locale' => 'en',
                ],
                [
                    'section_id' => $portfolioSection->id,
                    'title' => 'Tree-Trimming & Removal',
                    'subtitle' => 'Gardening',
                    'content' => null,
                    'image_path' => null,
                    'link' => '#',
                    'order' => 2,
                    'is_active' => true,
                    'locale' => 'en',
                ],
                [
                    'section_id' => $portfolioSection->id,
                    'title' => 'Junk Removal',
                    'subtitle' => 'Gardening',
                    'content' => null,
                    'image_path' => null,
                    'link' => '#',
                    'order' => 3,
                    'is_active' => true,
                    'locale' => 'en',
                ],
                [
                    'section_id' => $portfolioSection->id,
                    'title' => 'Watering plants',
                    'subtitle' => 'Gardening',
                    'content' => null,
                    'image_path' => null,
                    'link' => '#',
                    'order' => 4,
                    'is_active' => true,
                    'locale' => 'en',
                ],
                [
                    'section_id' => $portfolioSection->id,
                    'title' => 'Mowing the grass',
                    'subtitle' => 'Gardening',
                    'content' => null,
                    'image_path' => null,
                    'link' => '#',
                    'order' => 5,
                    'is_active' => true,
                    'locale' => 'en',
                ],
            ];

            foreach ($portfolioItems as $item) {
                SectionItem::create([
                    'section_id' => $item['section_id'],
                    'title' => $item['title'],
                    'subtitle' => $item['subtitle'],
                    'content' => $item['content'],
                    'image_path' => $item['image_path'],
                    'link' => $item['link'],
                    'order' => $item['order'],
                    'is_active' => $item['is_active'],
                    'locale' => $item['locale'],
                ]);
            }
        }
    }
}

