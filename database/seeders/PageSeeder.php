<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;
use Illuminate\Support\Str;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'about-us',
                'title' => [
                    'en' => 'About Us',
                    'ar' => 'من نحن'
                ],
                'excerpt' => [
                    'en' => 'Learn more about our company and mission.',
                    'ar' => 'تعرف على المزيد حول شركتنا ومهمتنا.'
                ],
                'content' => [
                    'en' => '<p>We are a leading platform built to simplify complexity and accelerate transformation. Our mission is to provide the best services and solutions to our customers.</p><p>With years of experience and a dedicated team, we strive to exceed expectations and deliver exceptional results.</p>',
                    'ar' => '<p>نحن منصة رائدة مبنية لتبسيط التعقيد وتسريع التحول. مهمتنا هي تقديم أفضل الخدمات والحلول لعملائنا.</p><p>مع سنوات من الخبرة وفريق مخصص، نسعى لتجاوز التوقعات وتقديم نتائج استثنائية.</p>'
                ],
                'meta_title' => [
                    'en' => 'About Us - Our Company',
                    'ar' => 'من نحن - شركتنا'
                ],
                'meta_description' => [
                    'en' => 'Learn more about our company, mission, and values.',
                    'ar' => 'تعرف على المزيد حول شركتنا ومهمتنا وقيمنا.'
                ],
                'menu_label' => [
                    'en' => 'About Us',
                    'ar' => 'من نحن'
                ],
                'is_published' => true,
                'show_in_menu' => true,
                'order' => 1,
                'locale' => 'en',
            ],
            [
                'slug' => 'terms-conditions',
                'title' => [
                    'en' => 'Terms & Conditions',
                    'ar' => 'الشروط والأحكام'
                ],
                'excerpt' => [
                    'en' => 'Read our terms and conditions.',
                    'ar' => 'اقرأ شروطنا وأحكامنا.'
                ],
                'content' => [
                    'en' => '<p>These terms and conditions govern your use of our website and services. By using our services, you agree to these terms.</p>',
                    'ar' => '<p>هذه الشروط والأحكام تحكم استخدامك لموقعنا وخدماتنا. باستخدام خدماتنا، فإنك توافق على هذه الشروط.</p>'
                ],
                'meta_title' => [
                    'en' => 'Terms & Conditions',
                    'ar' => 'الشروط والأحكام'
                ],
                'meta_description' => [
                    'en' => 'Read our terms and conditions for using our services.',
                    'ar' => 'اقرأ شروطنا وأحكامنا لاستخدام خدماتنا.'
                ],
                'menu_label' => [
                    'en' => 'Terms & Conditions',
                    'ar' => 'الشروط والأحكام'
                ],
                'is_published' => true,
                'show_in_menu' => true,
                'order' => 2,
                'locale' => 'en',
            ],
            [
                'slug' => 'privacy-policy',
                'title' => [
                    'en' => 'Privacy Policy',
                    'ar' => 'سياسة الخصوصية'
                ],
                'excerpt' => [
                    'en' => 'Our privacy policy explains how we collect and use your data.',
                    'ar' => 'سياسة الخصوصية الخاصة بنا تشرح كيف نجمع ونستخدم بياناتك.'
                ],
                'content' => [
                    'en' => '<p>We are committed to protecting your privacy. This policy explains how we collect, use, and safeguard your personal information.</p>',
                    'ar' => '<p>نحن ملتزمون بحماية خصوصيتك. توضح هذه السياسة كيف نجمع ونستخدم ونحمي معلوماتك الشخصية.</p>'
                ],
                'meta_title' => [
                    'en' => 'Privacy Policy',
                    'ar' => 'سياسة الخصوصية'
                ],
                'meta_description' => [
                    'en' => 'Read our privacy policy to understand how we handle your data.',
                    'ar' => 'اقرأ سياسة الخصوصية الخاصة بنا لفهم كيفية تعاملنا مع بياناتك.'
                ],
                'menu_label' => [
                    'en' => 'Privacy Policy',
                    'ar' => 'سياسة الخصوصية'
                ],
                'is_published' => true,
                'show_in_menu' => true,
                'order' => 3,
                'locale' => 'en',
            ],
        ];

        foreach ($pages as $pageData) {
            Page::create([
                'slug' => $pageData['slug'],
                'title' => $pageData['title'],
                'excerpt' => $pageData['excerpt'],
                'content' => $pageData['content'],
                'meta_title' => $pageData['meta_title'],
                'meta_description' => $pageData['meta_description'],
                'menu_label' => $pageData['menu_label'],
                'is_published' => $pageData['is_published'],
                'show_in_menu' => $pageData['show_in_menu'],
                'order' => $pageData['order'],
                'locale' => $pageData['locale'],
            ]);
        }
    }
}

