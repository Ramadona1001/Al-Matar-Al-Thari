<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin|admin']);
    }

    public function index(Request $request)
    {
        $query = Blog::with('author');

        if ($request->has('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('translations', function($tq) use ($search) {
                $tq->where('title', 'like', "%{$search}%")
                   ->orWhere('excerpt', 'like', "%{$search}%")
                   ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $blogs = $query->latest()->get();
        $locales = config('localization.supported_locales', ['en', 'ar']);

        return view('admin.cms.blogs.index', compact('blogs', 'locales'));
    }

    public function create()
    {
        $authors = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['super-admin', 'admin']);
        })->get();
        $locales = config('localization.supported_locales', ['en', 'ar']);
        return view('admin.cms.blogs.create', compact('authors', 'locales'));
    }

    public function store(Request $request)
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        
        $validated = $request->validate([
            'slug' => 'nullable|string|max:255|unique:blogs,slug',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'author_name' => 'nullable|string|max:255',
            'author_id' => 'nullable|exists:users,id',
            'published_at' => 'nullable|date',
            'is_published' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'categories' => 'nullable|array',
        ]);

        foreach ($locales as $locale) {
            $request->validate([
                "title.{$locale}" => 'required|string|max:255',
                "excerpt.{$locale}" => 'nullable|string|max:500',
                "content.{$locale}" => 'required|string',
                "meta_title.{$locale}" => 'nullable|string|max:255',
                "meta_description.{$locale}" => 'nullable|string|max:500',
                "meta_keywords.{$locale}" => 'nullable|string|max:255',
            ]);
        }

        // Generate slug from title if not provided
        if (empty($validated['slug'])) {
            $title = $request->input('title.en') ?? $request->input('title.ar') ?? '';
            $validated['slug'] = Str::slug($title);
        }

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('blogs', 'public');
        }

        if ($request->hasFile('og_image')) {
            $validated['og_image'] = $request->file('og_image')->store('blogs', 'public');
        }

        $validated['is_published'] = $request->has('is_published');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['author_id'] = $validated['author_id'] ?? auth()->id();

        $blog = Blog::create($validated);

        foreach ($locales as $locale) {
            $translation = $blog->translateOrNew($locale);
            $translation->title = $request->input("title.{$locale}");
            $translation->excerpt = $request->input("excerpt.{$locale}");
            $translation->content = $request->input("content.{$locale}");
            $translation->meta_title = $request->input("meta_title.{$locale}");
            $translation->meta_description = $request->input("meta_description.{$locale}");
            $translation->meta_keywords = $request->input("meta_keywords.{$locale}");
            $translation->save();
        }
        
        // Refresh the blog to ensure translations are loaded
        $blog->refresh();

        return redirect()->route('admin.blogs.index')
            ->with('success', __('Blog post created successfully.'));
    }

    public function edit(Blog $blog)
    {
        $authors = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['super-admin', 'admin']);
        })->get();
        $locales = config('localization.supported_locales', ['en', 'ar']);
        return view('admin.cms.blogs.edit', compact('blog', 'authors', 'locales'));
    }

    public function update(Request $request, Blog $blog)
    {
        $locales = config('localization.supported_locales', ['en', 'ar']);
        
        $validated = $request->validate([
            'slug' => 'nullable|string|max:255|unique:blogs,slug,' . $blog->id,
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'author_name' => 'nullable|string|max:255',
            'author_id' => 'nullable|exists:users,id',
            'published_at' => 'nullable|date',
            'is_published' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'categories' => 'nullable|array',
        ]);

        foreach ($locales as $locale) {
            $request->validate([
                "title.{$locale}" => 'required|string|max:255',
                "excerpt.{$locale}" => 'nullable|string|max:500',
                "content.{$locale}" => 'required|string',
                "meta_title.{$locale}" => 'nullable|string|max:255',
                "meta_description.{$locale}" => 'nullable|string|max:500',
                "meta_keywords.{$locale}" => 'nullable|string|max:255',
            ]);
        }

        if ($request->hasFile('featured_image')) {
            if ($blog->featured_image) {
                Storage::disk('public')->delete($blog->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')->store('blogs', 'public');
        }

        if ($request->hasFile('og_image')) {
            if ($blog->og_image) {
                Storage::disk('public')->delete($blog->og_image);
            }
            $validated['og_image'] = $request->file('og_image')->store('blogs', 'public');
        }

        $validated['is_published'] = $request->has('is_published');
        $validated['is_featured'] = $request->has('is_featured');

        $blog->update($validated);

        foreach ($locales as $locale) {
            $translation = $blog->translateOrNew($locale);
            $translation->title = $request->input("title.{$locale}");
            $translation->excerpt = $request->input("excerpt.{$locale}");
            $translation->content = $request->input("content.{$locale}");
            $translation->meta_title = $request->input("meta_title.{$locale}");
            $translation->meta_description = $request->input("meta_description.{$locale}");
            $translation->meta_keywords = $request->input("meta_keywords.{$locale}");
            $translation->save();
        }
        
        // Refresh the blog to ensure translations are loaded
        $blog->refresh();

        return redirect()->route('admin.blogs.index')
            ->with('success', __('Blog post updated successfully.'));
    }

    public function destroy(Blog $blog)
    {
        if ($blog->featured_image) {
            Storage::disk('public')->delete($blog->featured_image);
        }
        if ($blog->og_image) {
            Storage::disk('public')->delete($blog->og_image);
        }

        $blog->delete();

        return redirect()->route('admin.blogs.index')
            ->with('success', __('Blog post deleted successfully.'));
    }
}
