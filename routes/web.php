<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PaymentGatewayController;
use App\Http\Controllers\Admin\PaymentTransactionController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Merchant\StaffController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| All routes are localized using LaravelLocalization package.
|
*/

// Localized routes group using LaravelLocalization
Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function () {
    // Public website routes
    Route::get('/', [\App\Http\Controllers\PublicController::class, 'home'])->name('public.home');
    Route::get('/cards/{slug}', [\App\Http\Controllers\PublicController::class, 'card'])->name('public.cards.show');
    Route::get('/about', [\App\Http\Controllers\PublicController::class, 'about'])->name('public.about');
    Route::get('/how-it-works', [\App\Http\Controllers\PublicController::class, 'how'])->name('public.how');
    Route::get('/faq', [\App\Http\Controllers\PublicController::class, 'faq'])->name('public.faq');
    Route::get('/terms', [\App\Http\Controllers\PublicController::class, 'terms'])->name('public.terms');
    Route::get('/privacy', [\App\Http\Controllers\PublicController::class, 'privacy'])->name('public.privacy');
    Route::get('/contact', [\App\Http\Controllers\PublicController::class, 'contact'])->name('public.contact');
    Route::post('/contact', [\App\Http\Controllers\PublicController::class, 'submitContact'])->name('public.contact.submit');
    Route::post('/newsletter/subscribe', [\App\Http\Controllers\PublicController::class, 'subscribeNewsletter'])->name('public.newsletter.subscribe');
    
    // Blog routes
    Route::get('/blog', [\App\Http\Controllers\PublicController::class, 'blogIndex'])->name('public.blog.index');
    Route::get('/blog/{slug}', [\App\Http\Controllers\PublicController::class, 'blogShow'])->name('public.blog.show');
    Route::post('/blog/{slug}/comment', [\App\Http\Controllers\PublicController::class, 'submitComment'])->name('public.blog.comment');
    
    // Services routes
    Route::get('/services', [\App\Http\Controllers\PublicController::class, 'servicesIndex'])->name('public.services.index');
    Route::get('/services/{slug}', [\App\Http\Controllers\PublicController::class, 'servicesShow'])->name('public.services.show');
    
    // Offers routes
    Route::get('/offers', [\App\Http\Controllers\PublicController::class, 'offersIndex'])->name('public.offers.index');
    Route::get('/offers/{slug}', [\App\Http\Controllers\PublicController::class, 'offersShow'])->name('public.offers.show');
    
    // Companies routes
    Route::get('/companies', [\App\Http\Controllers\PublicController::class, 'companiesIndex'])->name('public.companies.index');
    Route::get('/companies/{company}', [\App\Http\Controllers\PublicController::class, 'companiesShow'])->name('public.companies.show');
    
    // Features route
    Route::get('/features', [\App\Http\Controllers\PublicController::class, 'features'])->name('public.features');
    
    // Dynamic pages
    Route::get('/page/{slug}', [\App\Http\Controllers\PublicController::class, 'page'])->name('public.page');

    // Public request link routes
    Route::get('/r/{uuid}', [\App\Http\Controllers\Customer\PointRequestController::class, 'showPublic'])->name('public.requests.show');
    Route::post('/r/{uuid}/send', [\App\Http\Controllers\Customer\PointRequestController::class, 'send'])->middleware('auth')->name('public.requests.send');

    // Super admin: Site management
    Route::prefix('admin')->middleware(['auth', 'role:super-admin|admin'])->name('admin.')->group(function () {
        // Site settings
        Route::get('/site/settings', [\App\Http\Controllers\Admin\SiteSettingsController::class, 'edit'])->name('site.settings.edit');
        Route::post('/site/settings', [\App\Http\Controllers\Admin\SiteSettingsController::class, 'update'])->name('site.settings.update');
        
        // Page Builder
        Route::get('/page-builder/{page}', [\App\Http\Controllers\Admin\PageBuilderController::class, 'show'])->name('page-builder.show');
        Route::post('/page-builder/{page}/save', [\App\Http\Controllers\Admin\PageBuilderController::class, 'saveLayout'])->name('page-builder.save');
        Route::get('/page-builder/{page}/sections', [\App\Http\Controllers\Admin\PageBuilderController::class, 'getSections'])->name('page-builder.sections');

        // Pages CRUD
        Route::get('/pages', [\App\Http\Controllers\Admin\PageController::class, 'index'])->name('pages.index');
        Route::get('/pages/create', [\App\Http\Controllers\Admin\PageController::class, 'create'])->name('pages.create');
        Route::post('/pages', [\App\Http\Controllers\Admin\PageController::class, 'store'])->name('pages.store');
        Route::get('/pages/{page}/edit', [\App\Http\Controllers\Admin\PageController::class, 'edit'])->name('pages.edit');
        Route::put('/pages/{page}', [\App\Http\Controllers\Admin\PageController::class, 'update'])->name('pages.update');
        Route::delete('/pages/{page}', [\App\Http\Controllers\Admin\PageController::class, 'destroy'])->name('pages.destroy');

        // CMS Management - Banners
        Route::get('/banners', [\App\Http\Controllers\Admin\BannerController::class, 'index'])->name('banners.index');
        Route::get('/banners/create', [\App\Http\Controllers\Admin\BannerController::class, 'create'])->name('banners.create');
        Route::post('/banners', [\App\Http\Controllers\Admin\BannerController::class, 'store'])->name('banners.store');
        Route::get('/banners/{banner}/edit', [\App\Http\Controllers\Admin\BannerController::class, 'edit'])->name('banners.edit');
        Route::put('/banners/{banner}', [\App\Http\Controllers\Admin\BannerController::class, 'update'])->name('banners.update');
        Route::delete('/banners/{banner}', [\App\Http\Controllers\Admin\BannerController::class, 'destroy'])->name('banners.destroy');

        // CMS Management - Sections
        Route::get('/sections', [\App\Http\Controllers\Admin\SectionController::class, 'index'])->name('sections.index');
        Route::get('/sections/create', [\App\Http\Controllers\Admin\SectionController::class, 'create'])->name('sections.create');
        Route::post('/sections', [\App\Http\Controllers\Admin\SectionController::class, 'store'])->name('sections.store');
        Route::get('/sections/{section}/edit', [\App\Http\Controllers\Admin\SectionController::class, 'edit'])->name('sections.edit');
        Route::put('/sections/{section}', [\App\Http\Controllers\Admin\SectionController::class, 'update'])->name('sections.update');
        Route::delete('/sections/{section}', [\App\Http\Controllers\Admin\SectionController::class, 'destroy'])->name('sections.destroy');

        // CMS Management - Section Items
        Route::get('/section-items', [\App\Http\Controllers\Admin\SectionItemController::class, 'index'])->name('section-items.index');
        Route::get('/section-items/create', [\App\Http\Controllers\Admin\SectionItemController::class, 'create'])->name('section-items.create');
        Route::post('/section-items', [\App\Http\Controllers\Admin\SectionItemController::class, 'store'])->name('section-items.store');
        Route::get('/section-items/{sectionItem}/edit', [\App\Http\Controllers\Admin\SectionItemController::class, 'edit'])->name('section-items.edit');
        Route::put('/section-items/{sectionItem}', [\App\Http\Controllers\Admin\SectionItemController::class, 'update'])->name('section-items.update');
        Route::delete('/section-items/{sectionItem}', [\App\Http\Controllers\Admin\SectionItemController::class, 'destroy'])->name('section-items.destroy');

        // CMS Management - Menus
        Route::get('/menus', [\App\Http\Controllers\Admin\MenuController::class, 'index'])->name('menus.index');
        Route::get('/menus/create', [\App\Http\Controllers\Admin\MenuController::class, 'create'])->name('menus.create');
        Route::post('/menus', [\App\Http\Controllers\Admin\MenuController::class, 'store'])->name('menus.store');
        Route::get('/menus/{menu}/edit', [\App\Http\Controllers\Admin\MenuController::class, 'edit'])->name('menus.edit');
        Route::put('/menus/{menu}', [\App\Http\Controllers\Admin\MenuController::class, 'update'])->name('menus.update');
        Route::delete('/menus/{menu}', [\App\Http\Controllers\Admin\MenuController::class, 'destroy'])->name('menus.destroy');

        // Footer Menu Groups Management
        Route::get('/footer-menu-groups', [\App\Http\Controllers\Admin\FooterMenuGroupController::class, 'index'])->name('footer-menu-groups.index');
        Route::get('/footer-menu-groups/create', [\App\Http\Controllers\Admin\FooterMenuGroupController::class, 'create'])->name('footer-menu-groups.create');
        Route::post('/footer-menu-groups', [\App\Http\Controllers\Admin\FooterMenuGroupController::class, 'store'])->name('footer-menu-groups.store');
        Route::get('/footer-menu-groups/{footerMenuGroup}/edit', [\App\Http\Controllers\Admin\FooterMenuGroupController::class, 'edit'])->name('footer-menu-groups.edit');
        Route::put('/footer-menu-groups/{footerMenuGroup}', [\App\Http\Controllers\Admin\FooterMenuGroupController::class, 'update'])->name('footer-menu-groups.update');
        Route::delete('/footer-menu-groups/{footerMenuGroup}', [\App\Http\Controllers\Admin\FooterMenuGroupController::class, 'destroy'])->name('footer-menu-groups.destroy');
        Route::get('/menus/create', [\App\Http\Controllers\Admin\MenuController::class, 'create'])->name('menus.create');
        Route::post('/menus', [\App\Http\Controllers\Admin\MenuController::class, 'store'])->name('menus.store');
        Route::get('/menus/{menu}/edit', [\App\Http\Controllers\Admin\MenuController::class, 'edit'])->name('menus.edit');
        Route::put('/menus/{menu}', [\App\Http\Controllers\Admin\MenuController::class, 'update'])->name('menus.update');
        Route::delete('/menus/{menu}', [\App\Http\Controllers\Admin\MenuController::class, 'destroy'])->name('menus.destroy');

        // CMS Management - Blogs
        Route::get('/blogs', [\App\Http\Controllers\Admin\BlogController::class, 'index'])->name('blogs.index');
        Route::get('/blogs/create', [\App\Http\Controllers\Admin\BlogController::class, 'create'])->name('blogs.create');
        Route::post('/blogs', [\App\Http\Controllers\Admin\BlogController::class, 'store'])->name('blogs.store');
        Route::get('/blogs/{blog}/edit', [\App\Http\Controllers\Admin\BlogController::class, 'edit'])->name('blogs.edit');
        Route::put('/blogs/{blog}', [\App\Http\Controllers\Admin\BlogController::class, 'update'])->name('blogs.update');
        Route::delete('/blogs/{blog}', [\App\Http\Controllers\Admin\BlogController::class, 'destroy'])->name('blogs.destroy');
        
        // Blog Comments Management
        Route::get('/blogs/{blog}/comments', [\App\Http\Controllers\Admin\BlogCommentController::class, 'index'])->name('blogs.comments.index');
        Route::post('/blog-comments/{comment}/approve', [\App\Http\Controllers\Admin\BlogCommentController::class, 'approve'])->name('blog-comments.approve');
        Route::post('/blog-comments/{comment}/reject', [\App\Http\Controllers\Admin\BlogCommentController::class, 'reject'])->name('blog-comments.reject');
        Route::delete('/blog-comments/{comment}', [\App\Http\Controllers\Admin\BlogCommentController::class, 'destroy'])->name('blog-comments.destroy');
        Route::post('/blogs/{blog}/comments/bulk-approve', [\App\Http\Controllers\Admin\BlogCommentController::class, 'bulkApprove'])->name('blogs.comments.bulk-approve');
        Route::post('/blogs/{blog}/comments/bulk-delete', [\App\Http\Controllers\Admin\BlogCommentController::class, 'bulkDelete'])->name('blogs.comments.bulk-delete');

        // CMS Management - Services
        Route::get('/services', [\App\Http\Controllers\Admin\ServiceController::class, 'index'])->name('services.index');
        Route::get('/services/create', [\App\Http\Controllers\Admin\ServiceController::class, 'create'])->name('services.create');
        Route::post('/services', [\App\Http\Controllers\Admin\ServiceController::class, 'store'])->name('services.store');
        Route::get('/services/{service}/edit', [\App\Http\Controllers\Admin\ServiceController::class, 'edit'])->name('services.edit');
        Route::put('/services/{service}', [\App\Http\Controllers\Admin\ServiceController::class, 'update'])->name('services.update');
        Route::delete('/services/{service}', [\App\Http\Controllers\Admin\ServiceController::class, 'destroy'])->name('services.destroy');

        // CMS Management - Testimonials
        Route::get('/testimonials', [\App\Http\Controllers\Admin\TestimonialController::class, 'index'])->name('testimonials.index');
        Route::get('/testimonials/create', [\App\Http\Controllers\Admin\TestimonialController::class, 'create'])->name('testimonials.create');
        Route::post('/testimonials', [\App\Http\Controllers\Admin\TestimonialController::class, 'store'])->name('testimonials.store');
        Route::get('/testimonials/{testimonial}/edit', [\App\Http\Controllers\Admin\TestimonialController::class, 'edit'])->name('testimonials.edit');
        Route::put('/testimonials/{testimonial}', [\App\Http\Controllers\Admin\TestimonialController::class, 'update'])->name('testimonials.update');
        Route::delete('/testimonials/{testimonial}', [\App\Http\Controllers\Admin\TestimonialController::class, 'destroy'])->name('testimonials.destroy');

        // CMS Management - Statistics
        Route::get('/statistics', [\App\Http\Controllers\Admin\StatisticController::class, 'index'])->name('statistics.index');
        Route::get('/statistics/create', [\App\Http\Controllers\Admin\StatisticController::class, 'create'])->name('statistics.create');
        Route::post('/statistics', [\App\Http\Controllers\Admin\StatisticController::class, 'store'])->name('statistics.store');
        Route::get('/statistics/{statistic}/edit', [\App\Http\Controllers\Admin\StatisticController::class, 'edit'])->name('statistics.edit');
        Route::put('/statistics/{statistic}', [\App\Http\Controllers\Admin\StatisticController::class, 'update'])->name('statistics.update');
        Route::delete('/statistics/{statistic}', [\App\Http\Controllers\Admin\StatisticController::class, 'destroy'])->name('statistics.destroy');

        // CMS Management - Section Settings
        Route::get('/section-settings', [\App\Http\Controllers\Admin\SectionSettingController::class, 'index'])->name('section-settings.index');
        Route::get('/section-settings/create', [\App\Http\Controllers\Admin\SectionSettingController::class, 'create'])->name('section-settings.create');
        Route::post('/section-settings', [\App\Http\Controllers\Admin\SectionSettingController::class, 'store'])->name('section-settings.store');
        Route::get('/section-settings/{sectionSetting}/edit', [\App\Http\Controllers\Admin\SectionSettingController::class, 'edit'])->name('section-settings.edit');
        Route::put('/section-settings/{sectionSetting}', [\App\Http\Controllers\Admin\SectionSettingController::class, 'update'])->name('section-settings.update');
        Route::delete('/section-settings/{sectionSetting}', [\App\Http\Controllers\Admin\SectionSettingController::class, 'destroy'])->name('section-settings.destroy');

        // CMS Management - FAQs
        Route::get('/faqs', [\App\Http\Controllers\Admin\FaqController::class, 'index'])->name('faqs.index');
        Route::get('/faqs/create', [\App\Http\Controllers\Admin\FaqController::class, 'create'])->name('faqs.create');
        Route::post('/faqs', [\App\Http\Controllers\Admin\FaqController::class, 'store'])->name('faqs.store');
        Route::get('/faqs/{faq}/edit', [\App\Http\Controllers\Admin\FaqController::class, 'edit'])->name('faqs.edit');
        Route::put('/faqs/{faq}', [\App\Http\Controllers\Admin\FaqController::class, 'update'])->name('faqs.update');
        Route::delete('/faqs/{faq}', [\App\Http\Controllers\Admin\FaqController::class, 'destroy'])->name('faqs.destroy');

        // CMS Management - How It Works Steps
        Route::get('/how-it-works-steps', [\App\Http\Controllers\Admin\HowItWorksStepController::class, 'index'])->name('how-it-works-steps.index');
        Route::get('/how-it-works-steps/create', [\App\Http\Controllers\Admin\HowItWorksStepController::class, 'create'])->name('how-it-works-steps.create');
        Route::post('/how-it-works-steps', [\App\Http\Controllers\Admin\HowItWorksStepController::class, 'store'])->name('how-it-works-steps.store');
        Route::get('/how-it-works-steps/{howItWorksStep}/edit', [\App\Http\Controllers\Admin\HowItWorksStepController::class, 'edit'])->name('how-it-works-steps.edit');
        Route::put('/how-it-works-steps/{howItWorksStep}', [\App\Http\Controllers\Admin\HowItWorksStepController::class, 'update'])->name('how-it-works-steps.update');
        Route::delete('/how-it-works-steps/{howItWorksStep}', [\App\Http\Controllers\Admin\HowItWorksStepController::class, 'destroy'])->name('how-it-works-steps.destroy');

        // CMS Management - Newsletter Subscribers
        Route::get('/newsletter-subscribers', [\App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'index'])->name('newsletter-subscribers.index');
        Route::get('/newsletter-subscribers/create', [\App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'create'])->name('newsletter-subscribers.create');
        Route::post('/newsletter-subscribers', [\App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'store'])->name('newsletter-subscribers.store');
        Route::get('/newsletter-subscribers/{newsletterSubscriber}', [\App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'show'])->name('newsletter-subscribers.show');
        Route::get('/newsletter-subscribers/{newsletterSubscriber}/edit', [\App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'edit'])->name('newsletter-subscribers.edit');
        Route::put('/newsletter-subscribers/{newsletterSubscriber}', [\App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'update'])->name('newsletter-subscribers.update');
        Route::delete('/newsletter-subscribers/{newsletterSubscriber}', [\App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'destroy'])->name('newsletter-subscribers.destroy');
        Route::post('/newsletter-subscribers/{newsletterSubscriber}/unsubscribe', [\App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'unsubscribe'])->name('newsletter-subscribers.unsubscribe');
        Route::post('/newsletter-subscribers/{newsletterSubscriber}/resubscribe', [\App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'resubscribe'])->name('newsletter-subscribers.resubscribe');
        Route::get('/newsletter-subscribers/export', [\App\Http\Controllers\Admin\NewsletterSubscriberController::class, 'export'])->name('newsletter-subscribers.export');

        // Contact messages management
        Route::get('/contact-messages', [\App\Http\Controllers\Admin\ContactMessageController::class, 'index'])->name('contact_messages.index');
        Route::get('/contact-messages/{contactMessage}', [\App\Http\Controllers\Admin\ContactMessageController::class, 'show'])->name('contact_messages.show');
        Route::delete('/contact-messages/{contactMessage}', [\App\Http\Controllers\Admin\ContactMessageController::class, 'destroy'])->name('contact_messages.destroy');
        Route::post('/contact-messages/{contactMessage}/mark-as-read', [\App\Http\Controllers\Admin\ContactMessageController::class, 'markAsRead'])->name('contact_messages.mark-as-read');
        Route::post('/contact-messages/{contactMessage}/mark-as-unread', [\App\Http\Controllers\Admin\ContactMessageController::class, 'markAsUnread'])->name('contact_messages.mark-as-unread');

        // Social Media Management
        Route::get('/social-media', [\App\Http\Controllers\Admin\SocialMediaController::class, 'index'])->name('social-media.index');
        Route::get('/social-media/create', [\App\Http\Controllers\Admin\SocialMediaController::class, 'create'])->name('social-media.create');
        Route::post('/social-media', [\App\Http\Controllers\Admin\SocialMediaController::class, 'store'])->name('social-media.store');
        Route::put('/social-media/update-all', [\App\Http\Controllers\Admin\SocialMediaController::class, 'updateAll'])->name('social-media.update-all');
        Route::put('/social-media/{platform}', [\App\Http\Controllers\Admin\SocialMediaController::class, 'update'])->name('social-media.update');
        Route::delete('/social-media/{platform}', [\App\Http\Controllers\Admin\SocialMediaController::class, 'destroy'])->name('social-media.destroy');
    });

    // Include dashboard routes
    require __DIR__ . '/dashboard.php';

    // Authenticated profile routes
    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Admin routes
    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
        // Payment Gateways
        Route::get('/payment-gateways', [PaymentGatewayController::class, 'index'])->name('payment-gateways.index');
        Route::get('/payment-gateways/create', [PaymentGatewayController::class, 'create'])->name('payment-gateways.create');
        Route::post('/payment-gateways', [PaymentGatewayController::class, 'store'])->name('payment-gateways.store');
        Route::get('/payment-gateways/{gateway}/edit', [PaymentGatewayController::class, 'edit'])->name('payment-gateways.edit');
        Route::put('/payment-gateways/{gateway}', [PaymentGatewayController::class, 'update'])->name('payment-gateways.update');
        Route::delete('/payment-gateways/{gateway}', [PaymentGatewayController::class, 'destroy'])->name('payment-gateways.destroy');
        Route::post('payment-gateways/{gateway}/toggle-status', [PaymentGatewayController::class, 'toggleStatus'])->name('payment-gateways.toggle');
        Route::post('payment-gateways/{gateway}/test-connection', [PaymentGatewayController::class, 'testConnection'])->name('payment-gateways.test');

        // Payment Transactions
        Route::get('/payment-transactions', [PaymentTransactionController::class, 'index'])->name('payment-transactions.index');
        Route::get('/payment-transactions/{transaction}', [PaymentTransactionController::class, 'show'])->name('payment-transactions.show');
        Route::post('payment-transactions/{transaction}/refund', [PaymentTransactionController::class, 'processRefund'])->name('payment-transactions.refund');
        Route::post('payment-transactions/affiliate-payout', [PaymentTransactionController::class, 'processAffiliatePayout'])->name('payment-transactions.affiliate-payout');
        Route::post('payment-transactions/subscription', [PaymentTransactionController::class, 'processSubscriptionPayment'])->name('payment-transactions.subscription');
        Route::get('payment-transactions/export', [PaymentTransactionController::class, 'export'])->name('payment-transactions.export');

        // Analytics Dashboard
        Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

        // Translations CRUD
        Route::get('translations', [\App\Http\Controllers\Admin\TranslationController::class, 'index'])->name('translations.index');
        Route::post('translations', [\App\Http\Controllers\Admin\TranslationController::class, 'store'])->name('translations.store');
        Route::post('translations/update', [\App\Http\Controllers\Admin\TranslationController::class, 'update'])->name('translations.update');
        Route::post('translations/delete', [\App\Http\Controllers\Admin\TranslationController::class, 'destroy'])->name('translations.destroy');

        // Networks CRUD
        Route::get('/networks', [\App\Http\Controllers\Admin\NetworkController::class, 'index'])->name('networks.index');
        Route::get('/networks/create', [\App\Http\Controllers\Admin\NetworkController::class, 'create'])->name('networks.create');
        Route::post('/networks', [\App\Http\Controllers\Admin\NetworkController::class, 'store'])->name('networks.store');
        Route::get('/networks/{network}/edit', [\App\Http\Controllers\Admin\NetworkController::class, 'edit'])->name('networks.edit');
        Route::put('/networks/{network}', [\App\Http\Controllers\Admin\NetworkController::class, 'update'])->name('networks.update');
        Route::delete('/networks/{network}', [\App\Http\Controllers\Admin\NetworkController::class, 'destroy'])->name('networks.destroy');
        // Assign managers to networks
        Route::get('networks/{network}/managers', [\App\Http\Controllers\Admin\NetworkController::class, 'managersEdit'])->name('networks.managers.edit');
        Route::put('networks/{network}/managers', [\App\Http\Controllers\Admin\NetworkController::class, 'managersUpdate'])->name('networks.managers.update');
    });

    // Merchant routes
    Route::middleware(['auth'])->prefix('merchant')->name('merchant.')->group(function () {
        // Staff Management
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
        Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
        Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
        Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit');
        Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
        Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');
        Route::get('/staff/{staff}', [StaffController::class, 'show'])->name('staff.show');

        // Loyalty Cards CRUD for partners
        Route::get('/loyalty-cards', [\App\Http\Controllers\Merchant\LoyaltyCardController::class, 'index'])->name('loyalty-cards.index');
        Route::get('/loyalty-cards/create', [\App\Http\Controllers\Merchant\LoyaltyCardController::class, 'create'])->name('loyalty-cards.create');
        Route::post('/loyalty-cards', [\App\Http\Controllers\Merchant\LoyaltyCardController::class, 'store'])->name('loyalty-cards.store');
        Route::get('/loyalty-cards/{loyaltyCard}', [\App\Http\Controllers\Merchant\LoyaltyCardController::class, 'show'])->name('loyalty-cards.show');
        Route::get('/loyalty-cards/{loyaltyCard}/edit', [\App\Http\Controllers\Merchant\LoyaltyCardController::class, 'edit'])->name('loyalty-cards.edit');
        Route::put('/loyalty-cards/{loyaltyCard}', [\App\Http\Controllers\Merchant\LoyaltyCardController::class, 'update'])->name('loyalty-cards.update');
        Route::delete('/loyalty-cards/{loyaltyCard}', [\App\Http\Controllers\Merchant\LoyaltyCardController::class, 'destroy'])->name('loyalty-cards.destroy');
    });

    // Partner routes (independent role)
    Route::middleware(['auth', 'role:partner'])->prefix('partner')->name('partner.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Merchant\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('loyalty-cards', \App\Http\Controllers\Merchant\LoyaltyCardController::class);
    });

    // Manager routes
    Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Manager\DashboardController::class, 'index'])->name('dashboard');
        Route::get('partners', [\App\Http\Controllers\Manager\PartnerController::class, 'index'])->name('partners.index');
        Route::get('partners/create', [\App\Http\Controllers\Manager\PartnerController::class, 'create'])->name('partners.create');
        Route::post('partners', [\App\Http\Controllers\Manager\PartnerController::class, 'store'])->name('partners.store');
    });

    // Auth routes under locale
    require __DIR__ . '/auth.php';
});

// Fallback route - LaravelLocalization handles locale redirects automatically
