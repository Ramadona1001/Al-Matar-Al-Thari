<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = [
            [
                'title' => [
                    'en' => 'Transform Your Business with Innovation',
                    'ar' => 'حول عملك بالابتكار'
                ],
                'subtitle' => [
                    'en' => 'Welcome to Our Platform',
                    'ar' => 'مرحباً بكم في منصتنا'
                ],
                'description' => [
                    'en' => 'The end-to-end solution built to simplify complexity and accelerate transformation.',
                    'ar' => 'الحل الشامل المبني لتبسيط التعقيد وتسريع التحول.'
                ],
                'image_path' => null,
                'button_text' => [
                    'en' => 'Get Started',
                    'ar' => 'ابدأ الآن'
                ],
                'button_link' => '/register',
                'order' => 1,
                'is_active' => true,
                'locale' => 'en',
            ],
            [
                'title' => [
                    'en' => 'Discover Amazing Opportunities',
                    'ar' => 'اكتشف الفرص المذهلة'
                ],
                'subtitle' => [
                    'en' => 'Join Thousands of Happy Customers',
                    'ar' => 'انضم إلى آلاف العملاء السعداء'
                ],
                'description' => [
                    'en' => 'Experience the best services and offers from top companies in one place.',
                    'ar' => 'استمتع بأفضل الخدمات والعروض من الشركات الرائدة في مكان واحد.'
                ],
                'image_path' => null,
                'button_text' => [
                    'en' => 'Explore Now',
                    'ar' => 'استكشف الآن'
                ],
                'button_link' => '/services',
                'order' => 2,
                'is_active' => true,
                'locale' => 'en',
            ],
        ];

        foreach ($banners as $bannerData) {
            $banner = Banner::create([
                'title' => is_array($bannerData['title']) ? json_encode($bannerData['title']) : $bannerData['title'],
                'subtitle' => is_array($bannerData['subtitle']) ? json_encode($bannerData['subtitle']) : $bannerData['subtitle'],
                'description' => is_array($bannerData['description']) ? json_encode($bannerData['description']) : $bannerData['description'],
                'image_path' => $bannerData['image_path'],
                'button_text' => is_array($bannerData['button_text']) ? json_encode($bannerData['button_text']) : $bannerData['button_text'],
                'button_link' => $bannerData['button_link'],
                'order' => $bannerData['order'],
                'is_active' => $bannerData['is_active'],
                'locale' => $bannerData['locale'],
            ]);
        }
    }
}

