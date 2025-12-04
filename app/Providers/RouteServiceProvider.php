<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/en/customer/wallet';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        // Explicit route model bindings
        Route::bind('section', function ($value) {
            return \App\Models\Section::findOrFail($value);
        });
        
        Route::bind('banner', function ($value) {
            return \App\Models\Banner::findOrFail($value);
        });
        
        Route::bind('sectionItem', function ($value) {
            return \App\Models\SectionItem::findOrFail($value);
        });
        
        Route::bind('menu', function ($value) {
            return \App\Models\Menu::findOrFail($value);
        });
        
        Route::bind('blog', function ($value) {
            return \App\Models\Blog::findOrFail($value);
        });
        
        Route::bind('service', function ($value) {
            return \App\Models\Service::findOrFail($value);
        });
        
        Route::bind('testimonial', function ($value) {
            return \App\Models\Testimonial::findOrFail($value);
        });
        
        Route::bind('statistic', function ($value) {
            return \App\Models\Statistic::findOrFail($value);
        });
        
        Route::bind('gateway', function ($value) {
            return \App\Models\PaymentGateway::findOrFail($value);
        });
        
        Route::bind('transaction', function ($value) {
            return \App\Models\PaymentTransaction::findOrFail($value);
        });
        
        Route::bind('network', function ($value) {
            return \App\Models\Network::findOrFail($value);
        });
        
        Route::bind('staff', function ($value) {
            return \App\Models\User::findOrFail($value);
        });
        
        Route::bind('loyaltyCard', function ($value) {
            return \App\Models\LoyaltyCard::findOrFail($value);
        });
        
        Route::bind('company', function ($value) {
            return \App\Models\Company::findOrFail($value);
        });
        
        Route::bind('user', function ($value) {
            return \App\Models\User::findOrFail($value);
        });
        
        Route::bind('offer', function ($value) {
            return \App\Models\Offer::findOrFail($value);
        });
        
        Route::bind('coupon', function ($value) {
            return \App\Models\Coupon::findOrFail($value);
        });
        
        Route::bind('branch', function ($value) {
            return \App\Models\Branch::findOrFail($value);
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
