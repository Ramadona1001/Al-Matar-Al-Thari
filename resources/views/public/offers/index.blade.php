@extends('layouts.new-design')

@section('meta_title', __('Special Offers & Discounts'))
@section('meta_description', __('Discover amazing deals and exclusive offers from our partner merchants'))

@section('content')
    <x-page-title 
        :title="__('Special Offers & Discounts')" 
        :subtitle="__('Discover amazing deals and exclusive offers from our partner merchants')"
        :breadcrumbs="[
            ['label' => __('Home'), 'url' => route('public.home')],
            ['label' => __('Offers'), 'url' => '#']
        ]"
    />

    <!-- Offers Section -->
    <section class="section" style="padding: 80px 0; background: linear-gradient(135deg, color-mix(in srgb, var(--brand-primary) 5%, #ffffff), color-mix(in srgb, var(--gradient-end-color) 5%, #f8f9fa)); position: relative; overflow: hidden;">
        <div class="container" style="position: relative; z-index: 1;">
            @if(isset($categories) && $categories->count() > 0)
                <!-- Filter Buttons -->
                <div class="row mb-5">
                    <div class="col-12">
                        <div class="d-flex flex-wrap justify-content-center gap-3" role="group">
                            <button type="button" class="filter-btn active" data-filter="all" style="background: linear-gradient(135deg, var(--brand-primary), var(--gradient-end-color)); color: #ffffff; border: none; padding: 0.75rem 2rem; border-radius: 50px; font-weight: 600; font-size: 0.95rem; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                                {{ __('All Offers') }}
                            </button>
                            @foreach($categories as $category)
                                <button type="button" class="filter-btn" data-filter="{{ $category->slug ?? $category->id }}" style="background: #ffffff; color: var(--brand-primary); border: 2px solid var(--brand-primary); padding: 0.75rem 2rem; border-radius: 50px; font-weight: 600; font-size: 0.95rem; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                    {{ $category->localized_name ?? $category->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Offers Grid -->
            <div class="row g-4" id="offersGrid">
                @forelse($offers as $offer)
                    <div class="col-lg-4 col-md-6 col-12" data-category="{{ $offer->category_id ?? 'all' }}">
                        <div class="offer-card-modern" style="background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column; position: relative;">
                            @if($offer->image)
                                <div class="offer-image-wrapper" style="width: 100%; height: 220px; overflow: hidden; background: linear-gradient(135deg, var(--brand-primary) 0%, var(--gradient-end-color) 100%); position: relative;">
                                    <img src="{{ asset('storage/' . $offer->image) }}" 
                                         alt="{{ $offer->localized_title }}"
                                         style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                                    <div class="offer-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.3) 100%);"></div>
                                </div>
                            @else
                                <div class="offer-image-wrapper" style="width: 100%; height: 220px; background: linear-gradient(135deg, var(--brand-primary) 0%, var(--gradient-end-color) 100%); display: flex; align-items: center; justify-content: center; position: relative;">
                                    <i class="fas fa-tag" style="font-size: 4rem; color: rgba(255,255,255,0.3);"></i>
                                </div>
                            @endif
                            
                            {{-- Badge --}}
                            <div style="position: absolute; top: 15px; right: 15px; z-index: 2;">
                                @if($offer->discount_percentage)
                                    <span class="offer-badge-modern" style="background: linear-gradient(135deg, #ff6b6b, #ee5a6f); color: #ffffff; padding: 0.5rem 1rem; border-radius: 25px; font-weight: 700; font-size: 0.85rem; box-shadow: 0 4px 15px rgba(255,107,107,0.4);">
                                        {{ number_format($offer->discount_percentage, 0) }}% {{ __('OFF') }}
                                    </span>
                                @elseif($offer->discount_amount)
                                    <span class="offer-badge-modern" style="background: linear-gradient(135deg, var(--brand-primary), var(--gradient-end-color)); color: #ffffff; padding: 0.5rem 1rem; border-radius: 25px; font-weight: 700; font-size: 0.85rem; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                                        {{ __('Cashback') }}
                                    </span>
                                @else
                                    <span class="offer-badge-modern" style="background: linear-gradient(135deg, var(--brand-primary), var(--gradient-end-color)); color: #ffffff; padding: 0.5rem 1rem; border-radius: 25px; font-weight: 700; font-size: 0.85rem; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                                        {{ __('Special Offer') }}
                                    </span>
                                @endif
                            </div>
                            
                            <div class="offer-content" style="padding: 1.75rem; flex: 1; display: flex; flex-direction: column;">
                                <h5 style="font-size: 1.35rem; font-weight: 700; color: #333; margin-bottom: 0.75rem; line-height: 1.4; min-height: 3.5rem;">{{ $offer->localized_title }}</h5>
                                
                                @if($offer->localized_description)
                                    <p style="color: #666; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.25rem; flex: 1;">{{ Str::limit($offer->localized_description, 120) }}</p>
                                @endif
                                
                                <div style="padding-top: 1rem; border-top: 1px solid #f0f0f0; margin-top: auto;">
                                    @if($offer->company)
                                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                            <i class="fas fa-store" style="color: var(--brand-primary); font-size: 0.9rem;"></i>
                                            <span style="color: #666; font-size: 0.9rem; font-weight: 500;">{{ $offer->company->localized_name }}</span>
                                        </div>
                                    @endif
                                    
                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.25rem;">
                                        <i class="fas fa-calendar-alt" style="color: var(--brand-primary); font-size: 0.9rem;"></i>
                                        <span style="color: #666; font-size: 0.85rem;">{{ __('Valid until') }}: <strong>{{ $offer->end_date->format('M d, Y') }}</strong></span>
                                    </div>
                                    
                                    <a href="{{ route('public.offers.show', $offer->slug) }}" 
                                       class="btn-view-offer" 
                                       style="display: block; text-align: center; background: linear-gradient(135deg, var(--brand-primary), var(--gradient-end-color)); color: #ffffff; padding: 0.875rem 1.5rem; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 0.95rem; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                                        {{ __('View Offer') }} <i class="fas fa-arrow-left ms-2" style="transition: transform 0.3s ease;"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div style="background: #ffffff; padding: 4rem 2rem; border-radius: 20px; text-align: center; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);">
                            <i class="fas fa-tag" style="font-size: 4rem; color: var(--brand-primary); opacity: 0.3; margin-bottom: 1.5rem;"></i>
                            <h4 style="color: #333; font-weight: 600; margin-bottom: 0.5rem;">{{ __('No offers available') }}</h4>
                            <p style="color: #666; margin: 0;">{{ __('No offers available at the moment. Please check back later.') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if(method_exists($offers, 'links'))
                <div class="row mt-5">
                    <div class="col-12 d-flex justify-content-center">
                        <div style="background: #ffffff; padding: 1rem; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                            {{ $offers->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            const offerCards = document.querySelectorAll('[data-category]');
            
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');
                    
                    // Update active state
                    filterButtons.forEach(btn => {
                        if (filter === 'all' && btn === this) {
                            btn.classList.add('active');
                            btn.style.background = 'linear-gradient(135deg, var(--brand-primary), var(--gradient-end-color))';
                            btn.style.color = '#ffffff';
                            btn.style.border = 'none';
                        } else if (btn === this) {
                            btn.classList.add('active');
                            btn.style.background = 'linear-gradient(135deg, var(--brand-primary), var(--gradient-end-color))';
                            btn.style.color = '#ffffff';
                            btn.style.border = 'none';
                        } else {
                            btn.classList.remove('active');
                            btn.style.background = '#ffffff';
                            btn.style.color = 'var(--brand-primary)';
                            btn.style.border = '2px solid var(--brand-primary)';
                        }
                    });
                    
                    // Filter offers
                    offerCards.forEach(card => {
                        const category = card.getAttribute('data-category');
                        if (filter === 'all' || category == filter) {
                            card.style.display = 'block';
                            setTimeout(() => {
                                card.style.opacity = '1';
                                card.style.transform = 'scale(1)';
                            }, 10);
                        } else {
                            card.style.opacity = '0';
                            card.style.transform = 'scale(0.95)';
                            setTimeout(() => {
                                card.style.display = 'none';
                            }, 300);
                        }
                    });
                });
            });
        });
    </script>
@endsection

<style>
    .offer-card-modern {
        transition: all 0.3s ease;
    }
    
    .offer-card-modern:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15) !important;
    }
    
    .offer-card-modern:hover .offer-image-wrapper img {
        transform: scale(1.1);
    }
    
    .offer-card-modern:hover .btn-view-offer {
        box-shadow: 0 6px 20px rgba(0,0,0,0.2) !important;
        transform: translateY(-2px);
    }
    
    .offer-card-modern:hover .btn-view-offer .fa-arrow-left {
        transform: translateX(-5px);
    }
    
    .filter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15) !important;
    }
    
    .filter-btn.active {
        box-shadow: 0 6px 20px rgba(0,0,0,0.2) !important;
    }
    
    [data-category] {
        transition: opacity 0.3s ease, transform 0.3s ease;
    }
    
    /* Responsive adjustments */
    @media (max-width: 991.98px) {
        .section {
            padding: 60px 0 !important;
        }
        
        .offer-card-modern {
            margin-bottom: 1.5rem;
        }
    }
    
    @media (max-width: 767.98px) {
        .filter-btn {
            padding: 0.6rem 1.5rem !important;
            font-size: 0.85rem !important;
            margin-bottom: 0.5rem;
        }
        
        .offer-content {
            padding: 1.5rem !important;
        }
    }
</style>

