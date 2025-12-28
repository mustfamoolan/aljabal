@extends('layouts.representative', ['title' => $product->name])

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">{{ $product->name }}</h4>
                    <a href="{{ route('representative.orders.create') }}" class="btn btn-secondary">
                        <iconify-icon icon="solar:arrow-left-bold-duotone"></iconify-icon>
                        العودة للمنتجات
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Product Images -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                @if($product->images->isNotEmpty())
                    <div id="productCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($product->images as $index => $image)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" 
                                         alt="{{ $product->name }}" 
                                         class="d-block w-100 rounded" 
                                         style="max-height: 500px; object-fit: contain;">
                                </div>
                            @endforeach
                        </div>
                        @if($product->images->count() > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        @endif
                    </div>
                    @if($product->images->count() > 1)
                        <div class="d-flex gap-2 mt-3 overflow-auto">
                            @foreach($product->images as $index => $image)
                                <img src="{{ asset('storage/' . $image->image_path) }}" 
                                     alt="Thumbnail {{ $index + 1 }}"
                                     class="img-thumbnail cursor-pointer" 
                                     style="width: 80px; height: 80px; object-fit: cover;"
                                     onclick="document.querySelector('#productCarousel').carousel({{ $index }})">
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 400px;">
                        <iconify-icon icon="solar:box-bold-duotone" class="fs-64 text-muted"></iconify-icon>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Product Details -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    @if($product->product_type->value === 'original')
                        <span class="badge bg-success">أصلي</span>
                    @else
                        <span class="badge bg-info">عادي</span>
                    @endif
                </div>

                <h2 class="mb-3">{{ $product->name }}</h2>

                @if($product->sku)
                    <p class="text-muted mb-2"><strong>SKU:</strong> {{ $product->sku }}</p>
                @endif

                <div class="mb-3">
                    <h4 class="text-primary mb-0">
                        سعر الجملة: {{ format_currency($product->wholesale_price ?? 0) }}
                    </h4>
                    @if($product->retail_price)
                        <p class="text-muted mb-0">سعر التجزئة: {{ format_currency($product->retail_price) }}</p>
                    @endif
                </div>

                <div class="mb-3">
                    <p class="mb-1"><strong>الكمية المتاحة:</strong> {{ $product->quantity }}</p>
                    @if($product->min_quantity)
                        <p class="text-muted mb-0 small">الحد الأدنى: {{ $product->min_quantity }}</p>
                    @endif
                </div>

                <!-- Categories and Tags -->
                <div class="mb-3">
                    @if($product->category)
                        <a href="{{ route('representative.orders.create') }}?category_id={{ $product->category->id }}" 
                           class="badge bg-info-subtle text-info me-2 text-decoration-none clickable-badge">
                            <iconify-icon icon="solar:folder-bold-duotone"></iconify-icon>
                            {{ $product->category->name }}
                        </a>
                    @endif
                    @if($product->subcategory)
                        <a href="{{ route('representative.orders.create') }}?subcategory_id={{ $product->subcategory->id }}" 
                           class="badge bg-secondary-subtle text-secondary me-2 text-decoration-none clickable-badge">
                            <iconify-icon icon="solar:folder-with-files-bold-duotone"></iconify-icon>
                            {{ $product->subcategory->name }}
                        </a>
                    @endif
                    @if($product->tags->isNotEmpty())
                        @foreach($product->tags as $tag)
                            <a href="{{ route('representative.orders.create') }}?tags[]={{ $tag->id }}" 
                               class="badge bg-primary-subtle text-primary me-1 text-decoration-none clickable-badge">
                                <iconify-icon icon="solar:tag-bold-duotone"></iconify-icon>
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    @endif
                </div>

                <!-- Product Info -->
                <div class="row mb-3">
                    @if($product->author)
                        <div class="col-md-6 mb-2">
                            <strong>المؤلف:</strong> 
                            <a href="{{ route('representative.orders.create') }}?author={{ urlencode($product->author) }}" 
                               class="text-primary text-decoration-none clickable-link">
                                <iconify-icon icon="solar:user-bold-duotone"></iconify-icon>
                                {{ $product->author }}
                            </a>
                        </div>
                    @endif
                    @if($product->publisher)
                        <div class="col-md-6 mb-2">
                            <strong>دار النشر:</strong> 
                            <a href="{{ route('representative.orders.create') }}?publisher={{ urlencode($product->publisher) }}" 
                               class="text-primary text-decoration-none clickable-link">
                                <iconify-icon icon="solar:book-bold-duotone"></iconify-icon>
                                {{ $product->publisher }}
                            </a>
                        </div>
                    @endif
                    @if($product->supplier)
                        <div class="col-md-6 mb-2">
                            <strong>المورد:</strong> {{ $product->supplier->name }}
                        </div>
                    @endif
                    @if($product->page_count)
                        <div class="col-md-6 mb-2">
                            <strong>عدد الصفحات:</strong> {{ $product->page_count }}
                        </div>
                    @endif
                    @if($product->size)
                        <div class="col-md-6 mb-2">
                            <strong>الحجم:</strong> {{ $product->size }}
                        </div>
                    @endif
                    @if($product->weight_value)
                        <div class="col-md-6 mb-2">
                            <strong>الوزن:</strong> {{ $product->weight_value }} 
                            @if($product->weight_unit)
                                {{ $product->weight_unit->label() }}
                            @endif
                        </div>
                    @endif
                </div>

                @if($product->short_description)
                    <div class="mb-3">
                        <h5>الوصف القصير</h5>
                        <p class="text-muted">{{ $product->short_description }}</p>
                    </div>
                @endif

                @if($product->long_description)
                    <div class="mb-3">
                        <h5>الوصف التفصيلي</h5>
                        <p class="text-muted">{{ $product->long_description }}</p>
                    </div>
                @endif

                <!-- Add to Cart -->
                <div class="card bg-light">
                    <div class="card-body">
                        <h5 class="mb-3">إضافة للسلة</h5>
                        <div class="mb-3">
                            <label class="form-label">الكمية</label>
                            <input type="number" 
                                   id="product-quantity" 
                                   class="form-control" 
                                   min="1" 
                                   max="{{ $product->quantity }}" 
                                   value="1">
                        </div>
                        <button type="button" 
                                class="btn btn-primary w-100 add-to-cart-btn"
                                data-product-id="{{ $product->id }}"
                                data-product-name="{{ $product->name }}"
                                data-wholesale-price="{{ $product->wholesale_price ?? 0 }}">
                            <iconify-icon icon="solar:cart-plus-bold-duotone"></iconify-icon>
                            إضافة للسلة
                        </button>
                        <p class="text-muted small mt-2 mb-0">
                            ملاحظة: سيتم إدخال سعر البيع للزبون في صفحة السلة
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recommendations -->
@if($recommendations->isNotEmpty())
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">منتجات مقترحة</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($recommendations as $recommended)
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card h-100 product-card">
                                @if($recommended->images->isNotEmpty())
                                    <a href="{{ route('representative.products.show', $recommended) }}">
                                        <img src="{{ asset('storage/' . $recommended->images->first()->image_path) }}" 
                                             alt="{{ $recommended->name }}" 
                                             class="card-img-top" 
                                             style="height: 200px; object-fit: cover;">
                                    </a>
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <iconify-icon icon="solar:box-bold-duotone" class="fs-48 text-muted"></iconify-icon>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <a href="{{ route('representative.products.show', $recommended) }}" class="text-dark">
                                            {{ $recommended->name }}
                                        </a>
                                    </h6>
                                    @if($recommended->sku)
                                        <p class="text-muted mb-2 small">SKU: {{ $recommended->sku }}</p>
                                    @endif
                                    <div class="mb-2">
                                        <span class="badge bg-info-subtle text-info">
                                            سعر الجملة: {{ format_currency($recommended->wholesale_price ?? 0) }}
                                        </span>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted">الكمية: {{ $recommended->quantity }}</small>
                                    </div>
                                    @if(!empty($recommended->recommendation_matches))
                                        <div class="mb-2">
                                            <small class="text-success">
                                                مشابه في: 
                                                @foreach($recommended->recommendation_matches as $match)
                                                    @if($match === 'tags') تاغات
                                                    @elseif($match === 'publisher') الناشر
                                                    @elseif($match === 'author') الكاتب
                                                    @elseif($match === 'category') القسم
                                                    @elseif($match === 'subcategory') القسم الفرعي
                                                    @endif
                                                    @if(!$loop->last), @endif
                                                @endforeach
                                            </small>
                                        </div>
                                    @endif
                                    <a href="{{ route('representative.products.show', $recommended) }}" class="btn btn-sm btn-primary w-100">
                                        عرض التفاصيل
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('script')
@vite(['resources/js/components/order-cart.js'])
<style>
.clickable-badge {
    cursor: pointer;
    transition: all 0.2s;
}
.clickable-badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
.clickable-link {
    cursor: pointer;
    transition: all 0.2s;
}
.clickable-link:hover {
    text-decoration: underline !important;
    font-weight: 600;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addToCartBtn = document.querySelector('.add-to-cart-btn');
    const quantityInput = document.getElementById('product-quantity');

    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            const productId = parseInt(this.dataset.productId);
            const productName = this.dataset.productName;
            const wholesalePrice = parseFloat(this.dataset.wholesalePrice) || 0;
            const quantity = parseInt(quantityInput.value) || 1;

            if (quantity <= 0 || quantity > {{ $product->quantity }}) {
                alert('الكمية غير صحيحة');
                return;
            }

            // Get cart from localStorage
            const CART_KEY = 'order_cart';
            const cart = JSON.parse(localStorage.getItem(CART_KEY) || '[]');

            // Check if product already exists
            const existingIndex = cart.findIndex(item => item.product_id === productId);

            if (existingIndex >= 0) {
                // Update quantity
                cart[existingIndex].quantity += quantity;
            } else {
                // Add new item (without customer_price - will be added in cart page)
                cart.push({
                    product_id: productId,
                    product_name: productName,
                    wholesale_price: wholesalePrice,
                    customer_price: 0, // Will be set in cart page
                    quantity: quantity,
                    profit_per_item: 0,
                    subtotal: 0,
                    profit_subtotal: 0
                });
            }

            // Save cart
            localStorage.setItem(CART_KEY, JSON.stringify(cart));

            // Update cart count
            const badge = document.getElementById('cart-count');
            if (badge) {
                const count = cart.reduce((sum, item) => sum + item.quantity, 0);
                badge.textContent = count;
                badge.style.display = count > 0 ? 'block' : 'none';
            }

            // Show success message
            const toast = document.createElement('div');
            toast.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <strong>تم!</strong> تم إضافة المنتج إلى السلة.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        });
    }
});
</script>
@endsection

