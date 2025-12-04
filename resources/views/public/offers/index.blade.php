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
    <section class="section">
        <div class="container">
            @if(isset($categories) && $categories->count() > 0)
                <!-- Filter Buttons -->
                <div class="row mb-4">
                    <div class="col-12 text-center">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary active" data-filter="all">{{ __('All Offers') }}</button>
                            @foreach($categories as $category)
                                <button type="button" class="btn btn-outline-primary" data-filter="{{ $category->slug ?? $category->id }}">
                                    {{ $category->localized_name ?? $category->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Offers Grid -->
            <div class="row" id="offersGrid">
                @forelse($offers as $offer)
                    <div class="col-md-4 mb-4" data-category="{{ $offer->category_id ?? 'all' }}">
                        <div class="offer-card">
                            @if($offer->image)
                                <div class="offer-image">
                                    <img src="{{ asset('storage/' . $offer->image) }}" alt="{{ $offer->localized_title }}">
                                </div>
                            @else
                                <div class="offer-image">
                                    <i class="fas fa-tag"></i>
                                </div>
                            @endif
                            <div class="offer-info">
                                @if($offer->discount_percentage)
                                    <span class="offer-badge">{{ number_format($offer->discount_percentage, 0) }}% {{ __('OFF') }}</span>
                                @elseif($offer->discount_amount)
                                    <span class="offer-badge">{{ __('Cashback') }}</span>
                                @else
                                    <span class="offer-badge">{{ __('Special Offer') }}</span>
                                @endif
                                <h5>{{ $offer->localized_title }}</h5>
                                <p>{{ Str::limit($offer->localized_description, 100) }}</p>
                                @if($offer->company)
                                    <p class="text-muted small"><i class="fas fa-store me-1"></i> {{ $offer->company->localized_name }}</p>
                                @endif
                                <p class="text-muted small"><i class="fas fa-calendar me-1"></i> {{ __('Valid until') }}: {{ $offer->end_date->format('M d, Y') }}</p>
                                <a href="{{ route('public.offers.show', $offer->slug) }}" class="btn btn-sm btn-primary-custom mt-2">{{ __('View Offer') }}</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <p>{{ __('No offers available at the moment.') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if(method_exists($offers, 'links'))
                <div class="row mt-4">
                    <div class="col-12">
                        {{ $offers->links() }}
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('[data-filter]').on('click', function() {
                const filter = $(this).data('filter');
                $('[data-filter]').removeClass('active');
                $(this).addClass('active');

                if (filter === 'all') {
                    $('[data-category]').fadeIn();
                } else {
                    $('[data-category]').hide();
                    $('[data-category="' + filter + '"]').fadeIn();
                }
            });
        });
    </script>
@endsection

