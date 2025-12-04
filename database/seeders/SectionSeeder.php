<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Section;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            [
                'name' => 'hero-section',
                'type' => 'hero',
                'title' => [
                    'en' => 'Transform Your Business with Innovation',
                    'ar' => 'حول عملك بالابتكار'
                ],
                'subtitle' => [
                    'en' => 'Welcome',
                    'ar' => 'مرحباً'
                ],
                'content' => [
                    'en' => 'The end-to-end solution built to simplify complexity and accelerate transformation.',
                    'ar' => 'الحل الشامل المبني لتبسيط التعقيد وتسريع التحول.'
                ],
                'page' => 'home',
                'order' => 1,
                'is_visible' => true,
                'locale' => 'en',
            ],
            [
                'name' => 'about-section',
                'type' => 'about',
                'title' => [
                    'en' => 'Gardeny offers a full-service',
                    'ar' => 'جاردني تقدم خدمة كاملة'
                ],
                'subtitle' => [
                    'en' => 'Since from 2000',
                    'ar' => 'منذ عام 2000'
                ],
                'content' => [
                    'en' => 'Most gardens consist of a mix of natural and constructed elements, although even very natural gardens are always an inherently artificial creation.',
                    'ar' => 'معظم الحدائق تتكون من مزيج من العناصر الطبيعية والمنشأة، على الرغم من أن الحدائق الطبيعية جداً هي دائماً خلق اصطناعي بطبيعته.'
                ],
                'page' => 'home',
                'order' => 2,
                'is_visible' => true,
                'locale' => 'en',
            ],
            [
                'name' => 'services-section',
                'type' => 'services',
                'title' => [
                    'en' => 'What we provide',
                    'ar' => 'ما نقدمه'
                ],
                'subtitle' => [
                    'en' => 'Services',
                    'ar' => 'الخدمات'
                ],
                'action_text' => [
                    'en' => 'Provides Hassle-Free Backyard Transformations with Artistic Solutions.',
                    'ar' => 'يوفر تحولات الفناء الخلفي الخالية من المتاعب مع الحلول الفنية.'
                ],
                'action_link' => '/contact',
                'page' => 'home',
                'order' => 3,
                'is_visible' => true,
                'locale' => 'en',
            ],
            [
                'name' => 'how-it-works-section',
                'type' => 'how-it-works',
                'title' => [
                    'en' => 'How It Works',
                    'ar' => 'كيف يعمل'
                ],
                'subtitle' => [
                    'en' => 'Simple Steps',
                    'ar' => 'خطوات بسيطة'
                ],
                'page' => 'home',
                'order' => 4,
                'is_visible' => true,
                'locale' => 'en',
            ],
            [
                'name' => 'statistics-section',
                'type' => 'statistics',
                'title' => [
                    'en' => 'Our Impact',
                    'ar' => 'تأثيرنا'
                ],
                'subtitle' => [
                    'en' => 'By The Numbers',
                    'ar' => 'بالأرقام'
                ],
                'page' => 'home',
                'order' => 5,
                'is_visible' => true,
                'locale' => 'en',
            ],
            [
                'name' => 'testimonials-section',
                'type' => 'testimonials',
                'title' => [
                    'en' => 'People say about us',
                    'ar' => 'ماذا يقول الناس عنا'
                ],
                'subtitle' => [
                    'en' => 'Testimonials',
                    'ar' => 'الشهادات'
                ],
                'page' => 'home',
                'order' => 6,
                'is_visible' => true,
                'locale' => 'en',
            ],
            [
                'name' => 'portfolio-section',
                'type' => 'portfolio',
                'title' => [
                    'en' => 'Watch our gallery',
                    'ar' => 'شاهد معرضنا'
                ],
                'subtitle' => [
                    'en' => 'Showcase',
                    'ar' => 'عرض'
                ],
                'page' => 'home',
                'order' => 7,
                'is_visible' => true,
                'locale' => 'en',
            ],
            [
                'name' => 'pricing-cta-section',
                'type' => 'pricing-cta',
                'title' => [
                    'en' => 'Estimate price',
                    'ar' => 'تقدير السعر'
                ],
                'subtitle' => [
                    'en' => 'Call to action',
                    'ar' => 'دعوة للعمل'
                ],
                'page' => 'home',
                'order' => 8,
                'is_visible' => true,
                'locale' => 'en',
            ],
            [
                'name' => 'faq-section',
                'type' => 'faq',
                'title' => [
                    'en' => 'Some questions',
                    'ar' => 'بعض الأسئلة'
                ],
                'subtitle' => [
                    'en' => 'FAQ',
                    'ar' => 'الأسئلة الشائعة'
                ],
                'page' => 'home',
                'order' => 9,
                'is_visible' => true,
                'locale' => 'en',
            ],
            [
                'name' => 'newsletter-section',
                'type' => 'newsletter',
                'title' => [
                    'en' => 'Classy offers too.',
                    'ar' => 'عروض أنيقة أيضاً.'
                ],
                'subtitle' => [
                    'en' => 'Get weekly newsletter & offers',
                    'ar' => 'احصل على النشرة الإخبارية والعروض الأسبوعية'
                ],
                'page' => 'home',
                'order' => 10,
                'is_visible' => true,
                'locale' => 'en',
            ],
        ];

        foreach ($sections as $sectionData) {
            // Prepare data JSON for additional fields
            $data = [];
            if (isset($sectionData['action_text'])) {
                $data['action_text'] = is_array($sectionData['action_text']) ? $sectionData['action_text'] : $sectionData['action_text'];
            }
            if (isset($sectionData['action_link'])) {
                $data['action_link'] = $sectionData['action_link'];
            }
            
            Section::create([
                'name' => $sectionData['name'],
                'type' => $sectionData['type'],
                'title' => is_array($sectionData['title']) ? json_encode($sectionData['title']) : $sectionData['title'],
                'subtitle' => isset($sectionData['subtitle']) && is_array($sectionData['subtitle']) ? json_encode($sectionData['subtitle']) : ($sectionData['subtitle'] ?? null),
                'content' => isset($sectionData['content']) && is_array($sectionData['content']) ? json_encode($sectionData['content']) : ($sectionData['content'] ?? null),
                'data' => !empty($data) ? $data : null,
                'page' => $sectionData['page'],
                'order' => $sectionData['order'],
                'is_visible' => $sectionData['is_visible'],
                'locale' => $sectionData['locale'],
            ]);
        }
    }
}

