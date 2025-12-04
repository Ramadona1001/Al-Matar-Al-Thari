<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Blog;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blogs = [
            [
                'title' => 'Most gardens consist of a mix of natural',
                'excerpt' => 'Gardens consist of a mix of natural and constructed elements, although even very natural gardens are always an inherently artificial creation.',
                'content' => '<p>Most gardens consist of a mix of natural and constructed elements, although even very natural gardens are always an inherently artificial creation. Natural elements present in a garden principally.</p><p>Gardens may exhibit structural enhancements, sometimes called follies, including water features such as fountains, ponds (with or without fish), waterfalls or creeks, dry creek beds, statuary, arbors, trellises and more.</p>',
                'author_name' => 'Domson',
                'published_at' => Carbon::now()->subDays(5),
                'is_published' => true,
                'is_featured' => true,
                'locale' => 'en',
                'tags' => ['gardening', 'nature', 'design'],
                'categories' => ['Tips', 'Design'],
            ],
            [
                'title' => 'Place your garden in a part of your yard',
                'excerpt' => 'When starting a garden, one of the top pieces of advice is to invest in soil that is nutrient rich & well drained.',
                'content' => '<p>Place your garden in a part of your yard where you\'ll see it regularly (out of sight, out of mind definitely applies to gardening). That way you\'ll be much more likely to spend time in it.</p><p>When starting a garden, one of the top pieces of advice is to invest in soil that is nutrient rich & well drained.</p>',
                'author_name' => 'Jeson',
                'published_at' => Carbon::now()->subDays(7),
                'is_published' => true,
                'is_featured' => false,
                'locale' => 'en',
                'tags' => ['garden', 'tips', 'beginner'],
                'categories' => ['Tips'],
            ],
            [
                'title' => 'You\'ll see it regularly of sight, out of mind',
                'excerpt' => 'One of the best gardening tips you\'ll ever get is to plan your new garden near a water source.',
                'content' => '<p>You\'ll see it regularly of sight, out of mind definitely applies to gardening. That way you\'ll be much more likely to spend time in it.</p><p>One of the best gardening tips you\'ll ever get is to plan your new garden near a water source.</p>',
                'author_name' => 'Peter',
                'published_at' => Carbon::now()->subDays(10),
                'is_published' => true,
                'is_featured' => false,
                'locale' => 'en',
                'tags' => ['planning', 'water', 'garden'],
                'categories' => ['Planning'],
            ],
        ];

        foreach ($blogs as $blogData) {
            Blog::create([
                'slug' => Str::slug($blogData['title']),
                'title' => $blogData['title'],
                'excerpt' => $blogData['excerpt'],
                'content' => $blogData['content'],
                'author_name' => $blogData['author_name'],
                'published_at' => $blogData['published_at'],
                'is_published' => $blogData['is_published'],
                'is_featured' => $blogData['is_featured'],
                'locale' => $blogData['locale'],
                'tags' => $blogData['tags'],
                'categories' => $blogData['categories'],
            ]);
        }
    }
}

