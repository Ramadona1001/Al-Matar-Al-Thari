<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FooterMenuGroup;
use App\Models\Menu;

class FooterMenuGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Footer Menu Groups with their menu items
        $groups = [
            [
                'name' => [
                    'en' => 'Quick Links',
                    'ar' => 'روابط سريعة',
                ],
                'order' => 1,
                'is_active' => true,
                'menu_items' => [
                    [
                        'label' => [
                            'en' => 'Home',
                            'ar' => 'الرئيسية',
                        ],
                        'route' => 'public.home',
                        'order' => 1,
                    ],
                    [
                        'label' => [
                            'en' => 'About Us',
                            'ar' => 'من نحن',
                        ],
                        'route' => 'public.about',
                        'order' => 2,
                    ],
                    [
                        'label' => [
                            'en' => 'Services',
                            'ar' => 'الخدمات',
                        ],
                        'route' => 'public.services.index',
                        'order' => 3,
                    ],
                    [
                        'label' => [
                            'en' => 'How It Works',
                            'ar' => 'كيف يعمل',
                        ],
                        'route' => 'public.how',
                        'order' => 4,
                    ],
                ],
            ],
            [
                'name' => [
                    'en' => 'Company',
                    'ar' => 'الشركة',
                ],
                'order' => 2,
                'is_active' => true,
                'menu_items' => [
                    [
                        'label' => [
                            'en' => 'Blog',
                            'ar' => 'المدونة',
                        ],
                        'route' => 'public.blog.index',
                        'order' => 1,
                    ],
                    [
                        'label' => [
                            'en' => 'Partners',
                            'ar' => 'الشركاء',
                        ],
                        'route' => 'public.companies.index',
                        'order' => 2,
                    ],
                    [
                        'label' => [
                            'en' => 'Offers',
                            'ar' => 'العروض',
                        ],
                        'route' => 'public.offers.index',
                        'order' => 3,
                    ],
                    [
                        'label' => [
                            'en' => 'Contact',
                            'ar' => 'اتصل بنا',
                        ],
                        'route' => 'public.contact',
                        'order' => 4,
                    ],
                ],
            ],
            [
                'name' => [
                    'en' => 'Legal',
                    'ar' => 'قانوني',
                ],
                'order' => 3,
                'is_active' => true,
                'menu_items' => [
                    [
                        'label' => [
                            'en' => 'Terms & Conditions',
                            'ar' => 'الشروط والأحكام',
                        ],
                        'route' => 'public.terms',
                        'order' => 1,
                    ],
                    [
                        'label' => [
                            'en' => 'Privacy Policy',
                            'ar' => 'سياسة الخصوصية',
                        ],
                        'route' => 'public.privacy',
                        'order' => 2,
                    ],
                    [
                        'label' => [
                            'en' => 'FAQ',
                            'ar' => 'الأسئلة الشائعة',
                        ],
                        'route' => 'public.faq',
                        'order' => 3,
                    ],
                ],
            ],
        ];

        foreach ($groups as $groupData) {
            // Create Footer Menu Group
            $group = FooterMenuGroup::create([
                'order' => $groupData['order'],
                'is_active' => $groupData['is_active'],
            ]);

            // Set translations for the group
            foreach ($groupData['name'] as $locale => $name) {
                $group->translateOrNew($locale)->name = $name;
            }
            $group->save();

            // Create menu items for this group
            if (isset($groupData['menu_items'])) {
                foreach ($groupData['menu_items'] as $itemData) {
                    $menuItem = Menu::create([
                        'name' => 'footer',
                        'route' => $itemData['route'] ?? null,
                        'footer_menu_group_id' => $group->id,
                        'order' => $itemData['order'],
                        'is_active' => true,
                        'parent_id' => null,
                    ]);

                    // Set translations for the menu item
                    foreach ($itemData['label'] as $locale => $label) {
                        $menuItem->translateOrNew($locale)->label = $label;
                    }
                    $menuItem->save();
                }
            }
        }
    }
}
