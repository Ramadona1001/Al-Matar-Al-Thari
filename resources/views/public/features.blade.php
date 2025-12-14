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
        <section class="section" style="padding: 80px 0; background: #f8f9fa;">
            <div class="container">
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8 text-center">
                        <h2 style="font-size: 2.5rem; font-weight: 700; color: #3D4F60; margin-bottom: 1rem;">{{ __('Admin Features') }}</h2>
                        <p style="font-size: 1.2rem; color: #6c757d;">{{ __('Powerful tools for managing your platform') }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="feature-card-modern" style="background: #ffffff; padding: 2.5rem; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column; position: relative; overflow: hidden;">
                            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #17A2B8 0%, #138496 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                                <i class="fas fa-chart-bar" style="font-size: 1.75rem; color: #ffffff;"></i>
                            </div>
                            <h5 style="font-size: 1.5rem; font-weight: 700; color: #3D4F60; margin-bottom: 1rem;">{{ __('Dashboard Analytics') }}</h5>
                            <p style="color: #6c757d; margin-bottom: 1.5rem; line-height: 1.6;">{{ __('Comprehensive analytics and reporting') }}</p>
                            <ul class="list-unstyled" style="margin: 0; flex: 1;">
                                <li style="color: #3D4F60; margin-bottom: 0.75rem; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745; margin-top: 0.25rem; flex-shrink: 0;"></i>
                                    <span>{{ __('Real-time statistics') }}</span>
                                </li>
                                <li style="color: #3D4F60; margin-bottom: 0.75rem; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745; margin-top: 0.25rem; flex-shrink: 0;"></i>
                                    <span>{{ __('User activity tracking') }}</span>
                                </li>
                                <li style="color: #3D4F60; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745; margin-top: 0.25rem; flex-shrink: 0;"></i>
                                    <span>{{ __('Revenue reports') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="feature-card-modern" style="background: #ffffff; padding: 2.5rem; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column; position: relative; overflow: hidden;">
                            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #3D4F60 0%, #2a3642 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                                <i class="fas fa-users-cog" style="font-size: 1.75rem; color: #ffffff;"></i>
                            </div>
                            <h5 style="font-size: 1.5rem; font-weight: 700; color: #3D4F60; margin-bottom: 1rem;">{{ __('User Management') }}</h5>
                            <p style="color: #6c757d; margin-bottom: 1.5rem; line-height: 1.6;">{{ __('Manage all users and permissions') }}</p>
                            <ul class="list-unstyled" style="margin: 0; flex: 1;">
                                <li style="color: #3D4F60; margin-bottom: 0.75rem; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745; margin-top: 0.25rem; flex-shrink: 0;"></i>
                                    <span>{{ __('User roles & permissions') }}</span>
                                </li>
                                <li style="color: #3D4F60; margin-bottom: 0.75rem; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745; margin-top: 0.25rem; flex-shrink: 0;"></i>
                                    <span>{{ __('Account management') }}</span>
                                </li>
                                <li style="color: #3D4F60; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745; margin-top: 0.25rem; flex-shrink: 0;"></i>
                                    <span>{{ __('Activity monitoring') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="feature-card-modern" style="background: #ffffff; padding: 2.5rem; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column; position: relative; overflow: hidden;">
                            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #28a745 0%, #218838 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                                <i class="fas fa-cog" style="font-size: 1.75rem; color: #ffffff;"></i>
                            </div>
                            <h5 style="font-size: 1.5rem; font-weight: 700; color: #3D4F60; margin-bottom: 1rem;">{{ __('System Configuration') }}</h5>
                            <p style="color: #6c757d; margin-bottom: 1.5rem; line-height: 1.6;">{{ __('Configure platform settings') }}</p>
                            <ul class="list-unstyled" style="margin: 0; flex: 1;">
                                <li style="color: #3D4F60; margin-bottom: 0.75rem; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745; margin-top: 0.25rem; flex-shrink: 0;"></i>
                                    <span>{{ __('System parameters') }}</span>
                                </li>
                                <li style="color: #3D4F60; margin-bottom: 0.75rem; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745; margin-top: 0.25rem; flex-shrink: 0;"></i>
                                    <span>{{ __('Integration settings') }}</span>
                                </li>
                                <li style="color: #3D4F60; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745; margin-top: 0.25rem; flex-shrink: 0;"></i>
                                    <span>{{ __('Security controls') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Merchant Features -->
        <section class="section" style="padding: 80px 0; background: #ffffff;">
            <div class="container">
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8 text-center">
                        <h2 style="font-size: 2.5rem; font-weight: 700; color: #3D4F60; margin-bottom: 1rem;">{{ __('Merchant Features') }}</h2>
                        <p style="font-size: 1.2rem; color: #6c757d;">{{ __('Tools to grow your business and engage customers') }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="feature-card-modern" style="background: #ffffff; padding: 2.5rem; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column; position: relative; overflow: hidden; border: 2px solid transparent;">
                            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #17A2B8 0%, #138496 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                                <i class="fas fa-tags" style="font-size: 1.75rem; color: #ffffff;"></i>
                            </div>
                            <h5 style="font-size: 1.5rem; font-weight: 700; color: #3D4F60; margin-bottom: 1rem;">{{ __('Offer Management') }}</h5>
                            <p style="color: #6c757d; margin-bottom: 1.5rem; line-height: 1.6;">{{ __('Create and manage discounts') }}</p>
                            <ul class="list-unstyled" style="margin: 0; flex: 1;">
                                <li style="color: #3D4F60; margin-bottom: 0.75rem; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745; margin-top: 0.25rem; flex-shrink: 0;"></i>
                                    <span>{{ __('Create unlimited offers') }}</span>
                                </li>
                                <li style="color: #3D4F60; margin-bottom: 0.75rem; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745; margin-top: 0.25rem; flex-shrink: 0;"></i>
                                    <span>{{ __('Set validity periods') }}</span>
                                </li>
                                <li style="color: #3D4F60; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745; margin-top: 0.25rem; flex-shrink: 0;"></i>
                                    <span>{{ __('Track offer performance') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="feature-card-modern" style="background: #ffffff; padding: 2.5rem; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column; position: relative; overflow: hidden; border: 2px solid transparent;">
                            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #3D4F60 0%, #2a3642 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                                <i class="fas fa-user-chart" style="font-size: 1.75rem; color: #ffffff;"></i>
                            </div>
                            <h5 style="font-size: 1.5rem; font-weight: 700; color: #3D4F60; margin-bottom: 1rem;">{{ __('Customer Insights') }}</h5>
                            <p style="color: #6c757d; margin-bottom: 1.5rem; line-height: 1.6;">{{ __('Track customer behavior') }}</p>
                            <ul class="list-unstyled" style="margin: 0; flex: 1;">
                                <li style="color: #3D4F60; margin-bottom: 0.75rem; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745; margin-top: 0.25rem; flex-shrink: 0;"></i>
                                    <span>{{ __('Customer analytics') }}</span>
                                </li>
                                <li style="color: #3D4F60; margin-bottom: 0.75rem; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745; margin-top: 0.25rem; flex-shrink: 0;"></i>
                                    <span>{{ __('Purchase patterns') }}</span>
                                </li>
                                <li style="color: #3D4F60; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745; margin-top: 0.25rem; flex-shrink: 0;"></i>
                                    <span>{{ __('Loyalty metrics') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="feature-card-modern" style="background: #ffffff; padding: 2.5rem; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column; position: relative; overflow: hidden; border: 2px solid transparent;">
                            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #28a745 0%, #218838 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                                <i class="fas fa-qrcode" style="font-size: 1.75rem; color: #ffffff;"></i>
                            </div>
                            <h5 style="font-size: 1.5rem; font-weight: 700; color: #3D4F60; margin-bottom: 1rem;">{{ __('QR Code Generator') }}</h5>
                            <p style="color: #6c757d; margin-bottom: 1.5rem; line-height: 1.6;">{{ __('Generate QR codes for your store') }}</p>
                            <ul class="list-unstyled" style="margin: 0; flex: 1;">
                                <li style="color: #3D4F60; margin-bottom: 0.75rem; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745; margin-top: 0.25rem; flex-shrink: 0;"></i>
                                    <span>{{ __('Custom QR codes') }}</span>
                                </li>
                                <li style="color: #3D4F60; margin-bottom: 0.75rem; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745; margin-top: 0.25rem; flex-shrink: 0;"></i>
                                    <span>{{ __('Print-ready formats') }}</span>
                                </li>
                                <li style="color: #3D4F60; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745; margin-top: 0.25rem; flex-shrink: 0;"></i>
                                    <span>{{ __('Digital display options') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Customer Features -->
        <section class="section" style="padding: 80px 0; background: #f8f9fa;">
            <div class="container">
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8 text-center">
                        <h2 style="font-size: 2.5rem; font-weight: 700; color: #3D4F60; margin-bottom: 1rem;">{{ __('Customer Features') }}</h2>
                        <p style="font-size: 1.2rem; color: #6c757d;">{{ __('Enjoy exclusive benefits and rewards') }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="feature-card-modern" style="background: #ffffff; padding: 2.5rem; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column; position: relative; overflow: hidden; border: 2px solid transparent;">
                            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #17A2B8 0%, #138496 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                                <i class="fas fa-coins" style="font-size: 1.75rem; color: #ffffff;"></i>
                            </div>
                            <h5 style="font-size: 1.5rem; font-weight: 700; color: #3D4F60; margin-bottom: 1rem;">{{ __('Loyalty Points') }}</h5>
                            <p style="color: #6c757d; margin-bottom: 1.5rem; line-height: 1.6;">{{ __('Earn and redeem points') }}</p>
                            <ul class="list-unstyled" style="margin: 0; flex: 1;">
                                <li style="color: #3D4F60; margin-bottom: 0.75rem; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745; margin-top: 0.25rem; flex-shrink: 0;"></i>
                                    <span>{{ __('Automatic point calculation') }}</span>
                                </li>
                                <li style="color: #3D4F60; margin-bottom: 0.75rem; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745; margin-top: 0.25rem; flex-shrink: 0;"></i>
                                    <span>{{ __('Point history tracking') }}</span>
                                </li>
                                <li style="color: #3D4F60; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745; margin-top: 0.25rem; flex-shrink: 0;"></i>
                                    <span>{{ __('Multiple redemption options') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="feature-card-modern" style="background: #ffffff; padding: 2.5rem; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column; position: relative; overflow: hidden; border: 2px solid transparent;">
                            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                                <i class="fas fa-star" style="font-size: 1.75rem; color: #ffffff;"></i>
                            </div>
                            <h5 style="font-size: 1.5rem; font-weight: 700; color: #3D4F60; margin-bottom: 1rem;">{{ __('Exclusive Offers') }}</h5>
                            <p style="color: #6c757d; margin-bottom: 1.5rem; line-height: 1.6;">{{ __('Access special discounts') }}</p>
                            <ul class="list-unstyled" style="margin: 0; flex: 1;">
                                <li style="color: #3D4F60; margin-bottom: 0.75rem; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745; margin-top: 0.25rem; flex-shrink: 0;"></i>
                                    <span>{{ __('Personalized offers') }}</span>
                                </li>
                                <li style="color: #3D4F60; margin-bottom: 0.75rem; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745; margin-top: 0.25rem; flex-shrink: 0;"></i>
                                    <span>{{ __('Early access to sales') }}</span>
                                </li>
                                <li style="color: #3D4F60; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745; margin-top: 0.25rem; flex-shrink: 0;"></i>
                                    <span>{{ __('Member-only deals') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="feature-card-modern" style="background: #ffffff; padding: 2.5rem; border-radius: 15px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column; position: relative; overflow: hidden; border: 2px solid transparent;">
                            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #28a745 0%, #218838 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                                <i class="fas fa-share-alt" style="font-size: 1.75rem; color: #ffffff;"></i>
                            </div>
                            <h5 style="font-size: 1.5rem; font-weight: 700; color: #3D4F60; margin-bottom: 1rem;">{{ __('Referral Program') }}</h5>
                            <p style="color: #6c757d; margin-bottom: 1.5rem; line-height: 1.6;">{{ __('Earn by referring friends') }}</p>
                            <ul class="list-unstyled" style="margin: 0; flex: 1;">
                                <li style="color: #3D4F60; margin-bottom: 0.75rem; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745; margin-top: 0.25rem; flex-shrink: 0;"></i>
                                    <span>{{ __('Share referral links') }}</span>
                                </li>
                                <li style="color: #3D4F60; margin-bottom: 0.75rem; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745; margin-top: 0.25rem; flex-shrink: 0;"></i>
                                    <span>{{ __('Track referrals') }}</span>
                                </li>
                                <li style="color: #3D4F60; display: flex; align-items: flex-start;">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745; margin-top: 0.25rem; flex-shrink: 0;"></i>
                                    <span>{{ __('Earn commissions') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection

<style>
.feature-card-modern:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
    border-color: rgba(61, 79, 96, 0.1) !important;
}

.feature-card-modern:hover > div[style*="gradient"] {
    transform: scale(1.1);
    transition: transform 0.3s ease;
}

@media (max-width: 768px) {
    .feature-card-modern {
        margin-bottom: 1.5rem;
    }
}
</style>
