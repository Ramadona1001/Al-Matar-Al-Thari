@extends('layouts.new-design')

@section('meta_title', __('Features'))
@section('meta_description', __('Discover all the powerful features our platform offers'))

@section('content')
    <x-page-title 
        :title="__('Features')" 
        :subtitle="__('Discover all the powerful features our platform offers')"
        :breadcrumbs="[
            ['label' => __('Home'), 'url' => route('public.home')],
            ['label' => __('Features'), 'url' => '#']
        ]"
    />
    
    @php
        $sections = class_exists(\App\Models\Section::class)
            ? \App\Models\Section::visible()
                ->forPage('features')
                ->with(['activeItems', 'translations'])
                ->ordered()
                ->get()
            : collect();
    @endphp
    
    @if($sections->count() > 0)
        @foreach($sections as $section)
            <x-section-renderer-new 
                :section="$section" 
                :banners="collect()" 
                :services="collect()" 
                :testimonials="collect()" 
                :statistics="collect()"
                :steps="collect()"
                :partners="collect()"
                :blogs="collect()"
            />
        @endforeach
    @else
        <!-- Admin Features -->
        <section class="section" style="padding: 80px 0; background: #ffffff;">
            <div class="container">
                <h2 class="text-center mb-5" style="font-size: 2.5rem; font-weight: 700; color: #3D4F60;">{{ __('Admin Features') }}</h2>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="system-card" style="background: #ffffff; padding: 2rem; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; height: 100%; text-align: center;">
                            <i class="fas fa-chart-bar" style="font-size: 3rem; color: #17A2B8; margin-bottom: 1rem;"></i>
                            <h5 style="font-size: 1.5rem; font-weight: 600; color: #3D4F60; margin-bottom: 1rem;">{{ __('Dashboard Analytics') }}</h5>
                            <p style="color: #6c757d; margin-bottom: 1rem;">{{ __('Comprehensive analytics and reporting') }}</p>
                            <ul class="list-unstyled text-start">
                                <li style="color: #6c757d; margin-bottom: 0.5rem;"><i class="fas fa-check text-success me-2"></i> {{ __('Real-time statistics') }}</li>
                                <li style="color: #6c757d; margin-bottom: 0.5rem;"><i class="fas fa-check text-success me-2"></i> {{ __('User activity tracking') }}</li>
                                <li style="color: #6c757d;"><i class="fas fa-check text-success me-2"></i> {{ __('Revenue reports') }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="system-card" style="background: #ffffff; padding: 2rem; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; height: 100%; text-align: center;">
                            <i class="fas fa-users-cog" style="font-size: 3rem; color: #3D4F60; margin-bottom: 1rem;"></i>
                            <h5 style="font-size: 1.5rem; font-weight: 600; color: #3D4F60; margin-bottom: 1rem;">{{ __('User Management') }}</h5>
                            <p style="color: #6c757d; margin-bottom: 1rem;">{{ __('Manage all users and permissions') }}</p>
                            <ul class="list-unstyled text-start">
                                <li style="color: #6c757d; margin-bottom: 0.5rem;"><i class="fas fa-check text-success me-2"></i> {{ __('User roles & permissions') }}</li>
                                <li style="color: #6c757d; margin-bottom: 0.5rem;"><i class="fas fa-check text-success me-2"></i> {{ __('Account management') }}</li>
                                <li style="color: #6c757d;"><i class="fas fa-check text-success me-2"></i> {{ __('Activity monitoring') }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="system-card" style="background: #ffffff; padding: 2rem; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; height: 100%; text-align: center;">
                            <i class="fas fa-cog" style="font-size: 3rem; color: #4BB543; margin-bottom: 1rem;"></i>
                            <h5 style="font-size: 1.5rem; font-weight: 600; color: #3D4F60; margin-bottom: 1rem;">{{ __('System Configuration') }}</h5>
                            <p style="color: #6c757d; margin-bottom: 1rem;">{{ __('Configure platform settings') }}</p>
                            <ul class="list-unstyled text-start">
                                <li style="color: #6c757d; margin-bottom: 0.5rem;"><i class="fas fa-check text-success me-2"></i> {{ __('System parameters') }}</li>
                                <li style="color: #6c757d; margin-bottom: 0.5rem;"><i class="fas fa-check text-success me-2"></i> {{ __('Integration settings') }}</li>
                                <li style="color: #6c757d;"><i class="fas fa-check text-success me-2"></i> {{ __('Security controls') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Merchant Features -->
        <section class="section" style="padding: 80px 0; background: #f8f9fa;">
            <div class="container">
                <h2 class="text-center mb-5" style="font-size: 2.5rem; font-weight: 700; color: #3D4F60;">{{ __('Merchant Features') }}</h2>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="system-card" style="background: #ffffff; padding: 2rem; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; height: 100%; text-align: center;">
                            <i class="fas fa-tags" style="font-size: 3rem; color: #17A2B8; margin-bottom: 1rem;"></i>
                            <h5 style="font-size: 1.5rem; font-weight: 600; color: #3D4F60; margin-bottom: 1rem;">{{ __('Offer Management') }}</h5>
                            <p style="color: #6c757d; margin-bottom: 1rem;">{{ __('Create and manage discounts') }}</p>
                            <ul class="list-unstyled text-start">
                                <li style="color: #6c757d; margin-bottom: 0.5rem;"><i class="fas fa-check text-success me-2"></i> {{ __('Create unlimited offers') }}</li>
                                <li style="color: #6c757d; margin-bottom: 0.5rem;"><i class="fas fa-check text-success me-2"></i> {{ __('Set validity periods') }}</li>
                                <li style="color: #6c757d;"><i class="fas fa-check text-success me-2"></i> {{ __('Track offer performance') }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="system-card" style="background: #ffffff; padding: 2rem; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; height: 100%; text-align: center;">
                            <i class="fas fa-user-chart" style="font-size: 3rem; color: #3D4F60; margin-bottom: 1rem;"></i>
                            <h5 style="font-size: 1.5rem; font-weight: 600; color: #3D4F60; margin-bottom: 1rem;">{{ __('Customer Insights') }}</h5>
                            <p style="color: #6c757d; margin-bottom: 1rem;">{{ __('Track customer behavior') }}</p>
                            <ul class="list-unstyled text-start">
                                <li style="color: #6c757d; margin-bottom: 0.5rem;"><i class="fas fa-check text-success me-2"></i> {{ __('Customer analytics') }}</li>
                                <li style="color: #6c757d; margin-bottom: 0.5rem;"><i class="fas fa-check text-success me-2"></i> {{ __('Purchase patterns') }}</li>
                                <li style="color: #6c757d;"><i class="fas fa-check text-success me-2"></i> {{ __('Loyalty metrics') }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="system-card" style="background: #ffffff; padding: 2rem; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; height: 100%; text-align: center;">
                            <i class="fas fa-qrcode" style="font-size: 3rem; color: #4BB543; margin-bottom: 1rem;"></i>
                            <h5 style="font-size: 1.5rem; font-weight: 600; color: #3D4F60; margin-bottom: 1rem;">{{ __('QR Code Generator') }}</h5>
                            <p style="color: #6c757d; margin-bottom: 1rem;">{{ __('Generate QR codes for your store') }}</p>
                            <ul class="list-unstyled text-start">
                                <li style="color: #6c757d; margin-bottom: 0.5rem;"><i class="fas fa-check text-success me-2"></i> {{ __('Custom QR codes') }}</li>
                                <li style="color: #6c757d; margin-bottom: 0.5rem;"><i class="fas fa-check text-success me-2"></i> {{ __('Print-ready formats') }}</li>
                                <li style="color: #6c757d;"><i class="fas fa-check text-success me-2"></i> {{ __('Digital display options') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Customer Features -->
        <section class="section" style="padding: 80px 0; background: #ffffff;">
            <div class="container">
                <h2 class="text-center mb-5" style="font-size: 2.5rem; font-weight: 700; color: #3D4F60;">{{ __('Customer Features') }}</h2>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="system-card" style="background: #ffffff; padding: 2rem; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; height: 100%; text-align: center;">
                            <i class="fas fa-coins" style="font-size: 3rem; color: #17A2B8; margin-bottom: 1rem;"></i>
                            <h5 style="font-size: 1.5rem; font-weight: 600; color: #3D4F60; margin-bottom: 1rem;">{{ __('Loyalty Points') }}</h5>
                            <p style="color: #6c757d; margin-bottom: 1rem;">{{ __('Earn and redeem points') }}</p>
                            <ul class="list-unstyled text-start">
                                <li style="color: #6c757d; margin-bottom: 0.5rem;"><i class="fas fa-check text-success me-2"></i> {{ __('Automatic point calculation') }}</li>
                                <li style="color: #6c757d; margin-bottom: 0.5rem;"><i class="fas fa-check text-success me-2"></i> {{ __('Point history tracking') }}</li>
                                <li style="color: #6c757d;"><i class="fas fa-check text-success me-2"></i> {{ __('Multiple redemption options') }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="system-card" style="background: #ffffff; padding: 2rem; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; height: 100%; text-align: center;">
                            <i class="fas fa-star" style="font-size: 3rem; color: #3D4F60; margin-bottom: 1rem;"></i>
                            <h5 style="font-size: 1.5rem; font-weight: 600; color: #3D4F60; margin-bottom: 1rem;">{{ __('Exclusive Offers') }}</h5>
                            <p style="color: #6c757d; margin-bottom: 1rem;">{{ __('Access special discounts') }}</p>
                            <ul class="list-unstyled text-start">
                                <li style="color: #6c757d; margin-bottom: 0.5rem;"><i class="fas fa-check text-success me-2"></i> {{ __('Personalized offers') }}</li>
                                <li style="color: #6c757d; margin-bottom: 0.5rem;"><i class="fas fa-check text-success me-2"></i> {{ __('Early access to sales') }}</li>
                                <li style="color: #6c757d;"><i class="fas fa-check text-success me-2"></i> {{ __('Member-only deals') }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="system-card" style="background: #ffffff; padding: 2rem; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; height: 100%; text-align: center;">
                            <i class="fas fa-share-alt" style="font-size: 3rem; color: #4BB543; margin-bottom: 1rem;"></i>
                            <h5 style="font-size: 1.5rem; font-weight: 600; color: #3D4F60; margin-bottom: 1rem;">{{ __('Referral Program') }}</h5>
                            <p style="color: #6c757d; margin-bottom: 1rem;">{{ __('Earn by referring friends') }}</p>
                            <ul class="list-unstyled text-start">
                                <li style="color: #6c757d; margin-bottom: 0.5rem;"><i class="fas fa-check text-success me-2"></i> {{ __('Share referral links') }}</li>
                                <li style="color: #6c757d; margin-bottom: 0.5rem;"><i class="fas fa-check text-success me-2"></i> {{ __('Track referrals') }}</li>
                                <li style="color: #6c757d;"><i class="fas fa-check text-success me-2"></i> {{ __('Earn commissions') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection

<style>
.system-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
}
</style>
