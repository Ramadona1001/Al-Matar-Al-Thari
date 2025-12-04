<?php
// database/seeders/CmsFrontStaticSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Section;
use App\Models\SectionItem;
use App\Models\Service;
use App\Models\Statistic;
use App\Models\Testimonial;
use App\Models\Blog;
use App\Models\CompanyPartner;
use App\Models\Menu;
use App\Models\SiteSetting;
use App\Models\HowItWorksStep;

class CmsFrontStaticSeeder extends Seeder
{
    public function run(): void
    {
        $locales = config('localization.supported_locales', ['en']);
        $en = [
            'hero_title' => 'Transform Your Business with Innovation',
            'hero_subtitle' => 'Welcome',
            'hero_content' => 'The end-to-end solution built to simplify complexity and accelerate transformation.',
            'features_title' => 'Why choose us',
            'features_subtitle' => 'Features',
            'services_title' => 'Our Services',
            'services_subtitle' => 'What We Offer',
            'stats_title' => 'Our Achievements',
            'stats_subtitle' => 'Numbers that matter',
            'testimonials_title' => 'What clients say',
            'testimonials_subtitle' => 'Testimonials',
            'blogs_title' => 'Blog & Insights',
            'blogs_subtitle' => 'Latest News',
            'cta_title' => 'Ready to Get Started?',
            'cta_subtitle' => 'Join thousands of satisfied customers today',
            'newsletter_subtitle' => 'get weekly newsletter & offers',
            'newsletter_title' => 'classy offers too.',
        ];
        $ar = [
            'hero_title' => 'حوّل عملك بالابتكار',
            'hero_subtitle' => 'مرحبًا',
            'hero_content' => 'حل شامل لتبسيط التعقيد وتسريع التحول.',
            'features_title' => 'لماذا نحن',
            'features_subtitle' => 'المزايا',
            'services_title' => 'خدماتنا',
            'services_subtitle' => 'ما نقدمه',
            'stats_title' => 'إنجازاتنا',
            'stats_subtitle' => 'أرقام مهمة',
            'testimonials_title' => 'آراء العملاء',
            'testimonials_subtitle' => 'شهادات',
            'blogs_title' => 'المدونة والرؤى',
            'blogs_subtitle' => 'آخر الأخبار',
            'cta_title' => 'هل أنت جاهز للبدء؟',
            'cta_subtitle' => 'انضم إلى آلاف العملاء الراضين اليوم',
            'newsletter_subtitle' => 'احصل على النشرة الأسبوعية والعروض',
            'newsletter_title' => 'عروض أنيقة أيضًا.',
        ];

        foreach ($locales as $locale) {
            $t = $locale === 'ar' ? $ar : $en;

            $this->seedMenus($locale);
            $this->seedSiteSettings($locale, $t);

            $order = 1;

            $this->upsertSection('hero', 'home', $locale, [
                'title' => $t['hero_title'],
                'subtitle' => $t['hero_subtitle'],
                'content' => $t['hero_content'],
                'order' => $order++,
                'is_visible' => true,
                'data' => ['classes' => 'header1 hero-area'],
            ]);

            $features = $this->upsertSection('features', 'home', $locale, [
                'title' => $t['features_title'],
                'subtitle' => $t['features_subtitle'],
                'order' => $order++,
                'is_visible' => true,
                'data' => ['classes' => 'choose-area pt-120 pb-60'],
            ]);
            $this->seedFeaturesItems($features, $locale);

            $servicesSection = $this->upsertSection('services', 'home', $locale, [
                'title' => $t['services_title'],
                'subtitle' => $t['services_subtitle'],
                'order' => $order++,
                'is_visible' => true,
                'data' => ['classes' => 'services-area pt-200 pb-60'],
            ]);
            $this->seedServices($locale);
            $this->seedServicesItems($servicesSection, $locale);

            $this->upsertSection('statistics', 'home', $locale, [
                'title' => $t['stats_title'],
                'subtitle' => $t['stats_subtitle'],
                'order' => $order++,
                'is_visible' => true,
                'data' => ['classes' => 'counter-area pt-120 pb-60'],
            ]);
            $this->seedStatistics($locale);

            $this->upsertSection('how-it-works', 'home', $locale, [
                'title' => $locale === 'ar' ? 'كيف يعمل' : 'How It Works',
                'subtitle' => $locale === 'ar' ? 'خطوات بسيطة' : 'Simple Steps',
                'order' => $order++,
                'is_visible' => true,
                'data' => ['classes' => 'process-area pt-120 pb-60'],
            ]);
            $this->seedHowItWorks($locale);

            $this->upsertSection('testimonials', 'home', $locale, [
                'title' => $t['testimonials_title'],
                'subtitle' => $t['testimonials_subtitle'],
                'order' => $order++,
                'is_visible' => true,
                'data' => ['classes' => 'testimonial-area pt-120 pb-90'],
            ]);
            $this->seedTestimonials($locale);

            $this->upsertSection('blogs', 'home', $locale, [
                'title' => $t['blogs_title'],
                'subtitle' => $t['blogs_subtitle'],
                'order' => $order++,
                'is_visible' => true,
                'data' => ['classes' => 'blog-area pt-120 pb-75'],
            ]);
            $this->seedBlogs($locale);

            $this->upsertSection('partners', 'home', $locale, [
                'title' => $locale === 'ar' ? 'شركاؤنا' : 'Our Partners',
                'subtitle' => $locale === 'ar' ? 'موثوقون من الشركات الرائدة' : 'Trusted by Leading Companies',
                'order' => $order++,
                'is_visible' => true,
                'data' => ['classes' => 'clients-area pt-120 pb-90'],
            ]);
            $this->seedPartners($locale);

            $this->upsertSection('cta', 'home', $locale, [
                'title' => $t['cta_title'],
                'subtitle' => $t['cta_subtitle'],
                'content' => null,
                'order' => $order++,
                'is_visible' => true,
                'data' => ['classes' => 'cta-area pt-120 pb-90'],
            ]);
        }
    }

    private function seedMenus(string $locale): void
    {
        foreach ([[$locale === 'ar' ? 'الرئيسية' : 'Home', "/{$locale}/", 1],
                  [$locale === 'ar' ? 'من نحن' : 'About', "/{$locale}/about", 2],
                  [$locale === 'ar' ? 'الخدمات' : 'Services', "/{$locale}/how-it-works", 3],
                  [$locale === 'ar' ? 'المدونة' : 'Blog', "/{$locale}/blog", 4],
                  [$locale === 'ar' ? 'اتصل بنا' : 'Contact', "/{$locale}/contact", 5]] as [$label, $url, $order]) {
            Menu::firstOrCreate(['name' => 'header', 'label' => $label, 'locale' => $locale, 'url' => $url, 'parent_id' => null], ['order' => $order, 'is_active' => true]);
        }
        foreach ([[$locale === 'ar' ? 'الخصوصية' : 'Privacy', "/{$locale}/privacy", 1],
                  [$locale === 'ar' ? 'الشروط' : 'Terms', "/{$locale}/terms", 2],
                  [$locale === 'ar' ? 'الأسئلة الشائعة' : 'FAQ', "/{$locale}/faq", 3],
                  [$locale === 'ar' ? 'اتصل بنا' : 'Contact', "/{$locale}/contact", 4]] as [$label, $url, $order]) {
            Menu::firstOrCreate(['name' => 'footer', 'label' => $label, 'locale' => $locale, 'url' => $url, 'parent_id' => null], ['order' => $order, 'is_active' => true]);
        }
    }

    private function seedSiteSettings(string $locale, array $t): void
    {
        $site = SiteSetting::getSettings();
        $additional = $site->additional_settings ?? [];
        $additional['newsletter_subtitle'][$locale] = $t['newsletter_subtitle'];
        $additional['newsletter_title'][$locale] = $t['newsletter_title'];
        $additional['newsletter_action'] = $additional['newsletter_action'] ?? "/{$locale}/contact";
        
        // Fixed: Properly handle the JSON attribute modification
        $footerText = $site->footer_text ?? [];
        $footerText[$locale] = $locale === 'ar'
            ? 'نقدم حلولًا حديثة للبستنة وتصميم المناظر الطبيعية.'
            : 'We provide modern solutions for gardening and landscape design.';
        
        $site->footer_text = $footerText;
        $site->additional_settings = $additional;
        $site->save();
    }

    private function seedFeaturesItems(Section $section, string $locale): void
    {
        $items = [
            ['Quality & Reliability', 'جودة وموثوقية', 'fas fa-check', 'High quality materials and reliable service.', 'مواد عالية الجودة وخدمة موثوقة.'],
            ['Expert Team', 'فريق خبير', 'fas fa-users', 'Skilled professionals with years of experience.', 'محترفون مهرة بخبرة سنوات.'],
            ['Eco Friendly', 'صديق للبيئة', 'fas fa-leaf', 'Sustainable practices for a greener future.', 'ممارسات مستدامة لمستقبل أكثر خضرة.'],
            ['Fast Delivery', 'تسليم سريع', 'fas fa-shipping-fast', 'On-time delivery and efficient execution.', 'تسليم في الوقت المحدد وتنفيذ فعّال.'],
            ['Great Support', 'دعم رائع', 'fas fa-headset', 'Friendly support whenever you need.', 'دعم ودّي متى ما احتجت.'],
        ];
        $order = 1;
        foreach ($items as [$enTitle, $arTitle, $icon, $enContent, $arContent]) {
            SectionItem::firstOrCreate(
                ['section_id' => $section->id, 'title' => $locale === 'ar' ? $arTitle : $enTitle, 'locale' => $locale],
                ['subtitle' => null, 'content' => $locale === 'ar' ? $arContent : $enContent, 'icon' => $icon, 'order' => $order++, 'is_active' => true, 'metadata' => ['classes' => 'irc-item choose-item']]
            );
        }
    }

    private function seedServices(string $locale): void
    {
        $items = [
            ['planting plants', 'زراعة النباتات', 'fas fa-seedling', 'The laying out and care of a plot of ground devoted partially or wholly.', 'تجهيز ورعاية قطعة أرض مخصصة جزئيًا أو كليًا.'],
            ['garden designer', 'مصمم حدائق', 'fas fa-tree', 'Creative garden designs tailored to your space.', 'تصاميم حدائق إبداعية مخصصة لمساحتك.'],
            ['blossom garden', 'حديقة مزهرة', 'fas fa-spa', 'Seasonal flower arrangements and maintenance.', 'ترتيبات زهور موسمية وصيانة.'],
        ];
        $order = 1;
        foreach ($items as [$enTitle, $arTitle, $icon, $enDesc, $arDesc]) {
            Service::firstOrCreate(
                ['title' => $locale === 'ar' ? $arTitle : $enTitle, 'locale' => $locale],
                ['slug' => Str::slug($locale === 'ar' ? $arTitle : $enTitle) . '-' . $locale, 'short_description' => $locale === 'ar' ? $arDesc : $enDesc, 'description' => $locale === 'ar' ? $arDesc : $enDesc, 'icon' => $icon, 'order' => $order++, 'is_active' => true, 'is_featured' => true, 'meta_title' => $locale === 'ar' ? $arTitle : $enTitle, 'meta_description' => $locale === 'ar' ? $arDesc : $enDesc]
            );
        }
    }

    private function seedServicesItems(Section $section, string $locale): void
    {
        $order = 1;
        foreach (Service::active()->featured()->ordered()->get() as $svc) {
            SectionItem::firstOrCreate(
                ['section_id' => $section->id, 'title' => $svc->title, 'locale' => $locale],
                ['content' => $svc->short_description ?? $svc->description, 'icon' => $svc->icon, 'link' => '#', 'link_text' => $locale === 'ar' ? 'اقرأ المزيد' : 'read more', 'order' => $order++, 'is_active' => true, 'metadata' => ['classes' => 'single-service single-service-default mb-30']]
            );
        }
    }

    private function seedStatistics(string $locale): void
    {
        $stats = [
            ['projects completed', 'مشاريع مكتملة', 450, 'fas fa-briefcase'],
            ['clients satisfied', 'عملاء راضون', 800, 'fas fa-smile'],
            ['expert gardeners', 'خبراء بستنة', 35, 'fas fa-user-tie'],
            ['awards won', 'جوائز محققة', 20, 'fas fa-award'],
        ];
        $order = 1;
        foreach ($stats as [$enLabel, $arLabel, $value, $icon]) {
            Statistic::firstOrCreate(
                ['label' => $locale === 'ar' ? $arLabel : $enLabel, 'locale' => $locale],
                ['value' => $value, 'icon' => $icon, 'suffix' => '+', 'description' => null, 'order' => $order++, 'is_active' => true]
            );
        }
    }

    private function seedTestimonials(string $locale): void
    {
        $items = [
            ['humble d. dow', 'عُمبل د. دو', 'Plant scientist', 'عالم نبات', 5, '"Fantastic service and beautiful results!"', '"خدمة رائعة ونتائج جميلة!"'],
            ['rosalina d. william', 'روزالينا د. ويليام', 'Landscape architect', 'مهندس مناظر طبيعية', 5, '"Professional team and timely delivery."', '"فريق محترف وتسليم في الوقت."'],
        ];
        $order = 1;
        foreach ($items as [$enName, $arName, $enPos, $arPos, $rating, $enQuote, $arQuote]) {
            Testimonial::firstOrCreate(
                ['name' => $locale === 'ar' ? $arName : $enName, 'locale' => $locale],
                ['position' => $locale === 'ar' ? $arPos : $enPos, 'company' => null, 'avatar' => null, 'rating' => $rating, 'testimonial' => $locale === 'ar' ? $arQuote : $enQuote, 'is_featured' => true, 'order' => $order++, 'is_active' => true]
            );
        }
    }

    private function seedBlogs(string $locale): void
    {
        $posts = [
            ['gardening tips for spring', 'نصائح البستنة لفصل الربيع', 'Expert tips to prepare your garden for spring.', 'نصائح الخبراء لتحضير حديقتك للربيع.'],
            ['modern landscape ideas', 'أفكار حديثة لتنسيق الحدائق', 'Contemporary designs to elevate your outdoor space.', 'تصاميم عصرية لترقية مساحتك الخارجية.'],
            ['sustainable gardening', 'البستنة المستدامة', 'Eco-friendly practices for greener gardens.', 'ممارسات صديقة للبيئة لحدائق أكثر خضرة.'],
        ];
        $order = 1;
        foreach ($posts as [$enTitle, $arTitle, $enExcerpt, $arExcerpt]) {
            Blog::firstOrCreate(
                ['slug' => Str::slug($locale === 'ar' ? $arTitle : $enTitle) . '-' . $locale, 'locale' => $locale],
                ['title' => $locale === 'ar' ? $arTitle : $enTitle, 'excerpt' => $locale === 'ar' ? $arExcerpt : $enExcerpt, 'content' => $locale === 'ar' ? $arExcerpt : $enExcerpt, 'is_published' => true, 'is_featured' => true, 'published_at' => now()->subDays($order), 'author_name' => 'Admin', 'views' => 0]
            );
        }
    }

    private function seedPartners(string $locale): void
    {
        $names = ['Green Corp', 'Gardenia', 'Plantify', 'Leafy', 'EcoLand', 'BloomX'];
        $order = 1;
        foreach ($names as $name) {
            CompanyPartner::firstOrCreate(
                ['name' => $name, 'locale' => $locale],
                ['logo_path' => null, 'website_url' => null, 'order' => $order++, 'is_active' => true]
            );
        }
    }

    private function seedHowItWorks(string $locale): void
    {
        $steps = [
            ['Choose service', 'اختر الخدمة', 'fas fa-list', 1],
            ['Schedule visit', 'حدّد الموعد', 'fas fa-calendar-check', 2],
            ['Enjoy results', 'استمتع بالنتائج', 'fas fa-smile', 3],
        ];
        foreach ($steps as [$en, $ar, $icon, $num]) {
            HowItWorksStep::firstOrCreate(
                ['title' => $locale === 'ar' ? $ar : $en, 'locale' => $locale, 'step_number' => $num],
                ['description' => null, 'icon' => $icon, 'order' => $num, 'is_active' => true]
            );
        }
    }

    private function upsertSection(string $type, string $page, string $locale, array $attrs): Section
    {
        return Section::firstOrCreate(['type' => $type, 'page' => $page, 'locale' => $locale], $attrs);
    }
}