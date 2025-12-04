<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            // Main Menu Items
            [
                'name' => 'header',
                'label' => 'Home',
                'route' => 'public.home',
                'order' => 1,
                'is_active' => true,
                'locale' => 'en',
            ],
            [
                'name' => 'header',
                'label' => 'About',
                'route' => 'public.about',
                'order' => 2,
                'is_active' => true,
                'locale' => 'en',
            ],
            [
                'name' => 'header',
                'label' => 'Services',
                'route' => 'public.services.index',
                'order' => 3,
                'is_active' => true,
                'locale' => 'en',
            ],
            [
                'name' => 'header',
                'label' => 'How It Works',
                'route' => 'public.how',
                'order' => 4,
                'is_active' => true,
                'locale' => 'en',
            ],
            [
                'name' => 'header',
                'label' => 'Blog',
                'route' => 'public.blog.index',
                'order' => 5,
                'is_active' => true,
                'locale' => 'en',
            ],
            [
                'name' => 'header',
                'label' => 'Contact',
                'route' => 'public.contact',
                'order' => 6,
                'is_active' => true,
                'locale' => 'en',
            ],
            // Footer Menu Items
            [
                'name' => 'footer-menu',
                'label' => 'About Us',
                'route' => 'public.about',
                'order' => 1,
                'is_active' => true,
                'locale' => 'en',
            ],
            [
                'name' => 'footer-menu',
                'label' => 'Services',
                'route' => 'public.services.index',
                'order' => 2,
                'is_active' => true,
                'locale' => 'en',
            ],
            [
                'name' => 'footer-menu',
                'label' => 'Blog',
                'route' => 'public.blog.index',
                'order' => 3,
                'is_active' => true,
                'locale' => 'en',
            ],
            [
                'name' => 'footer-menu',
                'label' => 'Contact',
                'route' => 'public.contact',
                'order' => 4,
                'is_active' => true,
                'locale' => 'en',
            ],
            [
                'name' => 'footer-menu',
                'label' => 'Terms & Conditions',
                'route' => 'public.terms',
                'order' => 5,
                'is_active' => true,
                'locale' => 'en',
            ],
            [
                'name' => 'footer-menu',
                'label' => 'Privacy Policy',
                'route' => 'public.privacy',
                'order' => 6,
                'is_active' => true,
                'locale' => 'en',
            ],
        ];

        foreach ($menus as $menuData) {
            Menu::create($menuData);
        }
    }
}

