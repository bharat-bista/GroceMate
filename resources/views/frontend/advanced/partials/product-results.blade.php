<div id="product-listing" class="gm-products-grid">
    @forelse($ecommerceProducts as $ecommerceProduct)
        @php
            $product = $ecommerceProduct->product;
            $discountPercent = (float) ($ecommerceProduct->discount_percent ?? 0);
            $hasOldPrice = !is_null($ecommerceProduct->previous_price) && (float) $ecommerceProduct->previous_price > 0;
            $currentPrice = (float) ($ecommerceProduct->display_price ?: $ecommerceProduct->mrp);
        @endphp

        @if($product)
            <div class="gm-product-card gm-ecom-card">
                <span class="gm-product-badge">
                    {{ $discountPercent > 0 ? rtrim(rtrim(number_format($discountPercent, 2, '.', ''), '0'), '.') . '% OFF' : 'FEATURED' }}
                </span>
                <span class="gm-cart-icon-badge"
                      data-product-id="{{ $ecommerceProduct->id }}"
                      data-product-name="{{ $product->name }}"
                      data-product-price="{{ $currentPrice }}"
                      data-product-image="{{ $ecommerceProduct->thumbnail ? asset('storage/' . $ecommerceProduct->thumbnail) : asset('assets/img/product/product1.jpg') }}"
                      title="Add to cart">
                    <i class="fas fa-shopping-cart"></i>
                </span>
                <a href="{{ route('description', $ecommerceProduct->id) }}" class="gm-product-card-link">
                    <div class="gm-product-img-wrap">
                        @if($ecommerceProduct->thumbnail)
                            <img src="{{ asset('storage/' . $ecommerceProduct->thumbnail) }}" alt="{{ $product->name }}" loading="lazy" decoding="async">
                        @else
                            <img src="{{ asset('assets/img/product/product1.jpg') }}" alt="{{ $product->name }}" loading="lazy" decoding="async">
                        @endif
                    </div>
                    <div class="gm-product-info">
                        <h3 class="gm-product-name">{{ $product->name }}</h3>
                        <div class="gm-product-price">
                            <span class="gm-price-new">Rs.{{ number_format($currentPrice, 2) }}</span>
                        </div>
                        <div class="gm-ecom-discount">
                            @if($hasOldPrice)
                                <span class="gm-price-old">Rs.{{ number_format((float) $ecommerceProduct->previous_price, 2) }}</span>
                            @else
                                <span class="gm-price-old">Rs.{{ number_format((float) $ecommerceProduct->mrp, 2) }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
        @endif
    @empty
        <div class="gm-product-card gm-ecom-card" style="grid-column: 1 / -1; text-align: center; padding: 30px;">
            <p style="margin: 0; color: var(--gm-gray);">No products found for the selected filters.</p>
        </div>
    @endforelse
</div>

@if($ecommerceProducts->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $ecommerceProducts->links() }}
    </div>
@endif
