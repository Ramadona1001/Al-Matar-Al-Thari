<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Offer;
use App\Models\LoyaltyCard;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        // Load CMS content
        $banners = class_exists(\App\Models\Banner::class)
            ? \App\Models\Banner::active()
                ->ordered()
                ->get()
            : collect();
        
        $sections = class_exists(\App\Models\Section::class)
            ? \App\Models\Section::visible()
                ->forPage('home')
                ->with(['activeItems'])
                ->ordered()
                ->get()
            : collect();
        
        // Load additional CMS data
        $services = class_exists(\App\Models\Service::class)
            ? \App\Models\Service::active()
                ->featured()
                ->ordered()
                ->limit(6)
                ->get()
            : collect();
        
        $testimonials = class_exists(\App\Models\Testimonial::class)
            ? \App\Models\Testimonial::active()
                ->ordered()
                ->limit(6)
                ->get()
            : collect();
        
        $statistics = class_exists(\App\Models\Statistic::class)
            ? \App\Models\Statistic::active()
                ->ordered()
                ->limit(4)
                ->get()
            : collect();
        
        // How It Works Steps
        $steps = class_exists(\App\Models\HowItWorksStep::class)
            ? \App\Models\HowItWorksStep::active()
                ->ordered()
                ->get()
            : collect();
        
        // Load section settings for homepage sections
        $sectionSettings = [];
        if (class_exists(\App\Models\SectionSetting::class)) {
            $sectionKeys = ['banner_section', 'services_section', 'testimonials_section', 'statistics_section', 'how_it_works_section', 'blogs_section', 'newsletter_section'];
            foreach ($sectionKeys as $key) {
                $setting = \App\Models\SectionSetting::getByKey($key);
                if ($setting) {
                    $sectionSettings[$key] = $setting;
                }
            }
        }
        
        // Company Partners
        $partners = class_exists(\App\Models\CompanyPartner::class)
            ? \App\Models\CompanyPartner::active()
                ->ordered()
                ->get()
            : collect();
        
        // Latest Blogs/News
        $blogs = class_exists(\App\Models\Blog::class)
            ? \App\Models\Blog::where('is_published', true)
                ->latest('published_at')
                ->limit(6)
                ->get()
            : collect();
        
        // Load FAQs for homepage if needed
        $faqs = class_exists(\App\Models\Faq::class)
            ? \App\Models\Faq::active()
                ->ordered()
                ->limit(6)
                ->get()
            : collect();
        
        // Featured Offers for front-end
        $offers = class_exists(Offer::class)
            ? Offer::active()
                ->featured()
                ->with(['company', 'category'])
                ->latest()
                ->limit(9)
                ->get()
            : collect();
        
        // Featured Companies for front-end
        $companies = class_exists(Company::class)
            ? Company::query()
                ->where('status', 'approved')
                ->latest()
                ->limit(6)
                ->get()
            : collect();
        
        // Legacy content (for backward compatibility)
        $featuredCompanies = $companies;
        $featuredOffers = $offers;
        $homepageCards = class_exists(\App\Models\LoyaltyCard::class) && class_exists(Company::class)
            ? \App\Models\LoyaltyCard::query()
                ->where('status', 'published')
                ->where('visible_on_homepage', true)
                ->whereHas('company', function ($q) {
                    $q->where('status', 'approved')->where('can_display_cards_on_homepage', true);
                })
                ->with('company')
                ->latest()
                ->limit(8)
                ->get()
            : collect();
        
        return view('public.home', compact(
            'banners', 
            'sections', 
            'services',
            'testimonials',
            'statistics',
            'steps',
            'partners',
            'blogs',
            'offers',
            'companies',
            'featuredCompanies', 
            'featuredOffers', 
            'homepageCards',
            'sectionSettings',
            'faqs'
        ));
    }

    /**
     * Show a public loyalty card details page.
     */
    public function card(string $slug)
    {
        $card = LoyaltyCard::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->whereHas('company', function ($q) {
                $q->where('status', 'approved')->where('can_display_cards_on_homepage', true);
            })
            ->with('company')
            ->firstOrFail();

        // Increment view analytics
        $card->increment('views_count');

        return view('public.cards.show', compact('card'));
    }

    public function about()
    {
        return $this->renderPage('about', 'public.about', __('About Us'));
    }

    public function how()
    {
        // Load How It Works Steps
        $steps = class_exists(\App\Models\HowItWorksStep::class)
            ? \App\Models\HowItWorksStep::active()
                ->ordered()
                ->get()
            : collect();
        
        // Load sections from CMS
        $sections = class_exists(\App\Models\Section::class)
            ? \App\Models\Section::visible()
                ->where(function($q) {
                    $q->where('page', 'how-it-works')
                      ->orWhere('type', 'how-it-works');
                })
                ->with(['activeItems', 'translations'])
                ->ordered()
                ->get()
            : collect();
        
        return view('public.how', compact('steps', 'sections'));
    }

    public function faq()
    {
        // Load FAQs from database
        $faqs = class_exists(\App\Models\Faq::class)
            ? \App\Models\Faq::active()
                ->ordered()
                ->get()
            : collect();
        
        // Load FAQ section settings
        $faqSectionSetting = class_exists(\App\Models\SectionSetting::class)
            ? \App\Models\SectionSetting::getByKey('faq_section')
            : null;
        
        // Load sections from CMS
        $sections = class_exists(\App\Models\Section::class)
            ? \App\Models\Section::visible()
                ->forPage('faq')
                ->with(['activeItems', 'translations'])
                ->ordered()
                ->get()
            : collect();
        
        return view('public.faq', compact('faqs', 'faqSectionSetting', 'sections'));
    }

    public function terms()
    {
        return $this->renderPage('terms', 'public.terms', __('Terms & Conditions'));
    }

    public function privacy()
    {
        return $this->renderPage('privacy', 'public.privacy', __('Privacy Policy'));
    }

    public function blogIndex()
    {
        $blogs = class_exists(\App\Models\Blog::class)
            ? \App\Models\Blog::where('is_published', true)
                ->latest('published_at')
                ->paginate(9)
            : collect();
        
        // Get recent posts for sidebar
        $recentPosts = class_exists(\App\Models\Blog::class)
            ? \App\Models\Blog::where('is_published', true)
                ->latest('published_at')
                ->limit(4)
                ->get()
            : collect();
        
        // Get all categories from blogs
        $allCategories = collect();
        if (class_exists(\App\Models\Blog::class)) {
            $blogsForCategories = \App\Models\Blog::where('is_published', true)->get();
            foreach ($blogsForCategories as $blog) {
                if ($blog->categories && is_array($blog->categories)) {
                    foreach ($blog->categories as $category) {
                        if (!empty($category)) {
                            $allCategories->push($category);
                        }
                    }
                }
            }
            $allCategories = $allCategories->unique()->values();
        }
        
        // Get all tags from blogs
        $allTags = collect();
        if (class_exists(\App\Models\Blog::class)) {
            $blogsForTags = \App\Models\Blog::where('is_published', true)->get();
            foreach ($blogsForTags as $blog) {
                if ($blog->tags && is_array($blog->tags)) {
                    foreach ($blog->tags as $tag) {
                        if (!empty($tag)) {
                            $allTags->push($tag);
                        }
                    }
                }
            }
            $allTags = $allTags->unique()->values();
        }
        
        return view('public.blog.index', compact('blogs', 'recentPosts', 'allCategories', 'allTags'));
    }

    public function blogShow(string $slug)
    {
        $blog = class_exists(\App\Models\Blog::class)
            ? \App\Models\Blog::where('slug', $slug)
                ->where('is_published', true)
                ->firstOrFail()
            : null;
        $recent = class_exists(\App\Models\Blog::class)
            ? \App\Models\Blog::where('is_published', true)
                ->latest('published_at')
                ->where('slug', '!=', $slug)
                ->limit(5)
                ->get()
            : collect();
        
        // Get approved comments for this blog
        $comments = class_exists(\App\Models\BlogComment::class) && $blog
            ? $blog->approvedComments()->with('approvedReplies')->get()
            : collect();
        
        // Get all categories and tags for sidebar
        $allCategories = collect();
        $allTags = collect();
        if (class_exists(\App\Models\Blog::class)) {
            $blogsForData = \App\Models\Blog::where('is_published', true)->get();
            foreach ($blogsForData as $b) {
                if ($b->categories && is_array($b->categories)) {
                    foreach ($b->categories as $category) {
                        if (!empty($category)) {
                            $allCategories->push($category);
                        }
                    }
                }
                if ($b->tags && is_array($b->tags)) {
                    foreach ($b->tags as $tag) {
                        if (!empty($tag)) {
                            $allTags->push($tag);
                        }
                    }
                }
            }
            $allCategories = $allCategories->unique()->values();
            $allTags = $allTags->unique()->values();
        }
        
        return view('public.blog.show', compact('blog', 'recent', 'comments', 'allCategories', 'allTags'));
    }
    
    public function submitComment(Request $request, string $slug)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:255',
            'comment' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:blog_comments,id',
        ]);

        $blog = \App\Models\Blog::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $validated['blog_id'] = $blog->id;
        $validated['is_approved'] = false; // Comments need approval

        \App\Models\BlogComment::create($validated);

        return redirect()->route('public.blog.show', $slug)
            ->with('success', __('Your comment has been submitted and is pending approval.'));
    }

    public function servicesIndex()
    {
        $services = class_exists(\App\Models\Service::class)
            ? \App\Models\Service::active()
                ->ordered()
                ->paginate(12)
            : collect();
        return view('public.services.index', compact('services'));
    }

    public function servicesShow(string $slug)
    {
        $service = class_exists(\App\Models\Service::class)
            ? \App\Models\Service::where('slug', $slug)
                ->where('is_active', true)
                ->firstOrFail()
            : null;
        $related = class_exists(\App\Models\Service::class)
            ? \App\Models\Service::active()
                ->where('slug', '!=', $slug)
                ->ordered()
                ->limit(6)
                ->get()
            : collect();
        return view('public.services.show', compact('service', 'related'));
    }

    public function offersIndex()
    {
        $offers = class_exists(Offer::class)
            ? Offer::active()
                ->with(['company', 'category'])
                ->latest()
                ->paginate(12)
            : collect();
        $categories = class_exists(\App\Models\Category::class)
            ? \App\Models\Category::active()->get()
            : collect();
        return view('public.offers.index', compact('offers', 'categories'));
    }

    public function offersShow(string $slug)
    {
        $offer = class_exists(Offer::class)
            ? Offer::where('slug', $slug)
                ->active()
                ->with(['company', 'category', 'branch'])
                ->firstOrFail()
            : null;
        $related = class_exists(Offer::class)
            ? Offer::active()
                ->where('slug', '!=', $slug)
                ->where('category_id', $offer->category_id ?? null)
                ->latest()
                ->limit(6)
                ->get()
            : collect();
        return view('public.offers.show', compact('offer', 'related'));
    }

    public function companiesIndex()
    {
        $companies = class_exists(Company::class)
            ? Company::where('status', 'approved')
                ->withCount(['offers', 'products'])
                ->latest()
                ->paginate(12)
            : collect();
        return view('public.companies.index', compact('companies'));
    }

    public function companiesShow(Company $company)
    {
        if ($company->status !== 'approved') {
            abort(404);
        }
        $company->load([
            'branches', 
            'offers' => function($q) {
                $q->active()->latest()->limit(6);
            },
            'products' => function($q) {
                $q->where('status', 'active')->latest()->limit(12);
            }
        ]);
        return view('public.companies.show', compact('company'));
    }

    public function features()
    {
        return $this->renderPage('features', 'public.features', __('Features'));
    }

    public function contact()
    {
        // Load contact section from CMS if exists
        $section = class_exists(\App\Models\Section::class)
            ? \App\Models\Section::visible()
                ->forPage('contact')
                ->where('type', 'contact')
                ->first()
            : null;
        
        // Load contact section settings
        $contactSectionSetting = class_exists(\App\Models\SectionSetting::class)
            ? \App\Models\SectionSetting::getByKey('contact_section')
            : null;
        
        // Load site settings for contact info
        $siteSettings = class_exists(\App\Models\SiteSetting::class)
            ? \App\Models\SiteSetting::first()
            : null;
        
        return view('public.contact', compact('section', 'contactSectionSetting', 'siteSettings'));
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:2000',
        ]);

        ContactMessage::create($validated);

        return redirect()->route('public.contact')
            ->with('success', __('Thanks! We will reply soon.'));
    }
    
    public function subscribeNewsletter(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:newsletter_subscribers,email',
            'name' => 'nullable|string|max:255',
        ]);

        if (class_exists(\App\Models\NewsletterSubscriber::class)) {
            \App\Models\NewsletterSubscriber::create([
                'email' => $validated['email'],
                'name' => $validated['name'] ?? null,
                'source' => 'website',
                'subscribed_at' => now(),
                'is_active' => true,
            ]);
        }

        return redirect()->back()
            ->with('success', __('Thank you for subscribing to our newsletter!'));
    }

    private function renderPage(string $slug, string $fallbackView, string $fallbackTitle)
    {
        if (class_exists(\App\Models\Page::class)) {
            $page = \App\Models\Page::where('slug', $slug)
                ->where('is_published', true)
                ->first();
            if ($page) {
                return view('public.page', compact('page'));
            }
        }
        // Fallback to dynamic sections by page slug
        $sections = class_exists(\App\Models\Section::class)
            ? \App\Models\Section::visible()
                ->forPage($slug)
                ->with(['activeItems'])
                ->ordered()
                ->get()
            : collect();
        if ($sections->count()) {
            return view('public.generic', ['sections' => $sections, 'title' => $fallbackTitle]);
        }
        // Fallback to static view if no page configured (now using new design)
        return view($fallbackView)->with('title', $fallbackTitle);
    }
    
    public function page(string $slug)
    {
        if (class_exists(\App\Models\Page::class)) {
            $page = \App\Models\Page::where('slug', $slug)
                ->where('is_published', true)
                ->first();
            if ($page) {
                return view('public.page', compact('page'));
            }
        }
        abort(404);
    }
}
